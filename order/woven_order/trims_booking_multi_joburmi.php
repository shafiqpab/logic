<?
/*-------------------------------------------- Comments ----------------------------------------
Version (MySql)          :
Version (Oracle)         :
Converted by             :  Monzu
Purpose			         :  This form will create Trims Booking
Functionality	         :
JS Functions	         :
Created by		         :  Aziz
Creation date 	         :  28-07-2016
Requirment Client        :  Urmi
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         :
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$user_id=$_SESSION['logic_erp']['user_id'];
echo load_html_head_contents("Woven Trims Booking", "../../", 1, 1,$unicode,'','');


?>
<script>
	var mandatory_field ='';
	var mandatory_message='';
	var mandatory_field=new Array();
	var mandatory_message=new Array();

	<?
	if(count($_SESSION['logic_erp']['mandatory_field'][87])>0)
	{
	echo "var mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][87]) . "';\n";
	echo "var mandatory_message = '". implode('*',$_SESSION['logic_erp']['mandatory_message'][87]) . "';\n";

	}
	
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][87] ); 
	echo "var field_level_data= ". $data_arr . ";\n";
?>

	function call_print_button_for_mail(mail_address,mail_body,type){
		var response=return_global_ajax_value(document.getElementById('cbo_company_name').value, 'send_mail_report_setting_first_select', '', 'requires/trims_booking_multi_job_controllerurmi');
		var report_id=response.split(",");
		var mail_address= mail_address+'___'+mail_body;
		
		if(report_id[0]==67) generate_trim_report('show_trim_booking_report2',1,'1___'+mail_address);
		else if(report_id[0]==183) generate_trim_report('show_trim_booking_report3',1,'1___'+mail_address);
		else if(report_id[0]==227)  generate_trim_report('show_trim_booking_report8',1,'1___'+mail_address);
		else if(report_id[0]==209) generate_trim_report('show_trim_booking_report4',1,'1___'+mail_address);
		else if(report_id[0]==235) generate_trim_report('show_trim_booking_report5',1,'1___'+mail_address);
        else if(report_id[0]==176) generate_trim_report('show_trim_booking_report6',1,'1___'+mail_address);
		else if(report_id[0]==746) generate_trim_report('show_trim_booking_report7',1,'1___'+mail_address);
		else if(report_id[0]==177) generate_trim_report('show_trim_booking_report9',1,'1___'+mail_address);
		else if(report_id[0]==241) generate_trim_report('show_trim_booking_report11',1,'1___'+mail_address);
		else if(report_id[0]==274) generate_trim_report('show_trim_booking_report10',1,'1___'+mail_address);
        else if(report_id[0]==269) generate_trim_report('show_trim_booking_report12',1,'1___'+mail_address);
		else if(report_id[0]==28)  generate_trim_report('show_trim_booking_report13',1,'1___'+mail_address);
		else if(report_id[0]==280) generate_trim_report('show_trim_booking_report14',1,'1___'+mail_address);
		else if(report_id[0]==304) generate_trim_report('show_trim_booking_report15',1,'1___'+mail_address);
		else if(report_id[0]==14)  generate_trim_report('show_trim_booking_report16',0,'1___'+mail_address);
		else if(report_id[0]==719) generate_trim_report('show_trim_booking_report17',1,'1___'+mail_address);
		else if(report_id[0]==339) generate_trim_report('show_trim_booking_report18',1,'1___'+mail_address);
		else if(report_id[0]==433) generate_trim_report('show_trim_booking_report19',1,'1___'+mail_address);
		else if(report_id[0]==768) generate_fabric_excel_report('show_trim_booking_report20',1,'1___'+mail_address);
		else if(report_id[0]==404) generate_trim_report('show_trim_booking_report21',1,'1___'+mail_address);
		else if(report_id[0]==419) generate_trim_report('show_trim_booking_report22',1,'1___'+mail_address);
		else if(report_id[0]==774) generate_trim_report('show_trim_booking_report_wg',1,'1___'+mail_address);
		else if(report_id[0]==786) generate_trim_report('show_trim_booking_report25',1,'1___'+mail_address);
		else if(report_id[0]==502) generate_trim_report('show_trim_booking_report26',1,'1___'+mail_address);
		else if(report_id[0]==845) generate_trim_report('show_trim_booking_report_AAL',1,'1___'+mail_address);
		else if(report_id[0]==437)  generate_trim_report('show_trim_booking_report27',1,'1___'+mail_address);
		else if(report_id[0]==875)  generate_trim_report('show_trim_booking_report_excel28',1,'1___'+mail_address);

	}
	

    function openmypage_file_info()
	{
		var company_id=document.getElementById('cbo_company_name').value;
		var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	 
		// alert(company_id);
		//page_link='requires/file_wise_export_status_controller.php?action=file_popup&company_id='+company_id+'&buyer_id='+buyer_id+'&lien_bank='+lien_bank+'&cbo_year='+cbo_year;
		page_link='requires/trims_booking_multi_job_controllerurmi.php?action=file_popup&company_id='+company_id+'&cbo_buyer_name='+cbo_buyer_name;
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Select File", 'width=600px,height=390px,center=1,resize=0,scrolling=0','../')

			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var data=this.contentDoc.getElementById("hide_file_no").value;//alert(item_description_all);
				// alert(data);
				document.getElementById('txt_internal_file_no').value=data;
				/*document.getElementById('is_lc_sc').value=data[1];
				document.getElementById('lc_sc_id').value=data[2];
				document.getElementById('lc_sc_no').value=data[3];
				document.getElementById('lc_sc_file_year').value=data[4];*/
			}
		}
	}

	function fnc_file_upload(i)
	{
			var update_id = $("#txtbookingid_"+i).val();		 
		   file_uploader ( '../../', update_id,'', 'knit_trims_booking_multi_job_v2_dtls', 0,1);
	}
</script>
<?
//----------------------------------------------------------------------------------------------------------------------------------
$date                  = date('d-m-Y');
$level_arr             = array(1=>"PO Level",2=>"Job Level");
$buyer_cond            = set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond          = set_user_lavel_filtering(' and comp.id','company_id');
$cbo_booking_month     = create_drop_down( "cbo_booking_month", 80, $months,"", 1, "-- Select --", "", "",0 );
$cbo_booking_year      = create_drop_down( "cbo_booking_year", 60, $year,"", 1, "-- Select --", date('Y'), "",0 );
$cbo_company_name      = create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "get_php_form_data( this.value, 'populate_variable_setting_data', 'requires/trims_booking_multi_job_controllerurmi' );","","" );
$cbo_buyer_name        = create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "check_paymode(this.value);","" );
$cbo_supplier_name     = create_drop_down( "cbo_supplier_name", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in (4,5) and a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_multi_job_controllerurmi');",0 );
$cbo_currency          = create_drop_down( "cbo_currency", 150, $currency,"", 1, "-- Select --", 2, "",0 );
$cbo_pay_mode          = create_drop_down( "cbo_pay_mode", 150, $pay_mode,"", 1, "-- Select Pay Mode --", "", "load_drop_down( 'requires/trims_booking_multi_job_controllerurmi', this.value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_supplier', 'supplier_td' )","" );
$cbo_source            = create_drop_down( "cbo_source", 150, $source,"", 1, "-- Select Source --", "", "","" );
$cbo_pay_term          = create_drop_down( "cbo_pay_term", 150, $pay_term,"", 1, "-- Select --", "", "","" );

$cbo_material_source   = create_drop_down( "cbo_material_source", 150, $fabric_source,"", 1, "-- Select Source --", "2", "","","","","","1" );
$cbo_level             = create_drop_down( "cbo_level", 150, $level_arr,"", 0, "", 2, "","","" );
$cbo_nominated_id   = create_drop_down( "cbo_nominated_id", 150, $yes_no,"", 1, "-- Select--", "", "","","" );
$endis                 = "disable_enable_fields( 'cbo_currency*cbo_company_name*cbo_supplier_name*cbo_level*cbo_buyer_name', 0 )";
$buttons               = load_submit_buttons( $permission, "fnc_trims_booking", 0,0 ,"reset_form('trimsbooking_1','booking_list_view*booking_list_view_list*app_sms2*pdf_file_name','id_approved_id*txt_select_item','txt_booking_date,".$date."*cbo_ready_to_approved,2',$endis,'cbo_currency*cbo_booking_year*cbo_booking_month*copy_val*cbo_pay_mode*cbo_source*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_level*cbo_company_name*cbo_buyer_name*cbo_material_source*cbo_nominated_id')",1);
?>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="trimsbooking_1"  autocomplete="off" id="trimsbooking_1">
        <fieldset style="width:950px;">
            <legend title="V3">Trims Booking &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;   <font id="app_sms" style="color:#F00"></font></legend>
            <table  width="900" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td align="right" class="must_entry_caption" colspan="4"><b>Booking No</b></td>
                    <td colspan="4">
                        <input class="text_boxes" type="text" style="width:140px" onDblClick="openmypage_booking('requires/trims_booking_multi_job_controllerurmi.php?action=trims_booking_popup','Trims Booking Search');" placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no" readonly/>
                        <input type="hidden" id="id_approved_id">
                        <input type="hidden" id="exeed_budge_qty">
                        <input type="hidden" id="exeed_budge_amount">
                        <input type="hidden" id="amount_exceed_level">
                        <input type="hidden" id="report_ids" />
                        <input type="hidden" id="cbo_currency_job"/>
                        <input type="hidden" id="lib_tna_intregrate"/>
                        <input type="hidden" id="update_id"/>
                    </td>
                </tr>
                <tr>
                    <td width="110" align="right">Shipment Month</td>
                    <td width="150"><?=$cbo_booking_month.$cbo_booking_year;	?> </td>
                    <td width="110" align="right" class="must_entry_caption">Company Name</td>
					<td width="150"><?=$cbo_company_name; ?></td>
                    <td width="110" align="right" class="must_entry_caption">Buyer Name</td>
                    <td width="150" id="buyer_td"><?=$cbo_buyer_name; ?></td>
                    <td width="110" align="right" class="must_entry_caption">Booking Date</td>
                    <td> <input class="datepicker" type="text" style="width:140px" name="txt_booking_date" id="txt_booking_date" value="<?=$date ?>" disabled /></td>
                </tr>
                <tr>
                    <td align="right" class="must_entry_caption">Delivery Date</td>
                    <td><input class="datepicker" type="text" style="width:140px" name="txt_delivery_date" id="txt_delivery_date"/></td>
                    <td class="must_entry_caption" align="right">Pay Mode</td>
                    <td><?=$cbo_pay_mode; ?></td>
                    <td align="right" class="must_entry_caption">Supplier Name</td>
                    <td id="supplier_td"><?=$cbo_supplier_name;?></td>
                    <td align="right" class="must_entry_caption">Material Source</td>
                    <td><?=$cbo_material_source?></td>
                </tr>
                <tr>
					<td align="right"> Pay Term </td>
                    <td><?=$cbo_pay_term ; ?></td>
					<td align="right">Tenor</td>
                    <td><input style="width:140px;" type="text" class="text_boxes_numeric" name="txt_tenor" id="txt_tenor" /></td>
                    <td align="right">Currency</td>
                    <td><?=$cbo_currency; ?></td>
                 	<td align="right" class="">Trims Type</td>
                    <td><?=create_drop_down( "cbo_trim_type", 150, $trim_type,"", "1", "---- Select ----", 0, "" ); ?></td>
                </tr>
                <tr>
					<td align="right">Attention</td>
                    <td height="10"><textarea class="text_area" type="text" style="width:140px; height:40px;" name="txt_attention" id="txt_attention" placeholder="Attention"></textarea></td>
                    <td align="right">Delivery To</td>
                    <td height="10"><textarea id="delivery_address" name="delivery_address" class="text_area" type="text" style="width:140px; height:40px;" placeholder="Delivery Address" ></textarea></td>
                    <td align="right">Remarks</td>
                    <td height="10"><textarea id="txt_remarks" name="txt_remarks" class="text_area" type="text" style="width:140px; height:40px;" placeholder="Remarks" ></textarea></td>
                     <td align="right">Internal File No</td>
                    <td><input type="text" name="txt_internal_file_no" id="txt_internal_file_no"  style="width:140px"  class="text_boxes" readonly maxlength="50" onClick="openmypage_file_info()" placeholder="Browse"  /></td>
                </tr>
                 <tr>
					<td align="right">Ready To App.</td>
                    <td align="left"><?=create_drop_down( "cbo_ready_to_approved", 150, $yes_no,"", 1, "-- Select--", 2, "fncChangeButton(this.value);","","" ); ?></td>
                    <td>&nbsp;</td>
                    <td><input type="button" name="btn_appSubmission_withoutanyChange" id="btn_appSubmission_withoutanyChange" class="formbuttonplasminus" style="width:130px;" onClick="fnc_appSubmission_withoutanyChange();" value="Submit For Approval"></td> 
                    <td align="right">Un-App. Req</td>
                    <td align="left"><Input name="txt_un_appv_request" class="text_boxes" readonly placeholder="Double Click for Brows" ID="txt_un_appv_request" style="width:140px"  onClick="openmypage_unapprove_request();"></td>
                    
					<td align="right" class="must_entry_caption">Level</td>
                    <td align="left"><?=$cbo_level; ?></td>
                </tr>
                <tr>
                	<td align="right" class="must_entry_caption"> Source </td>
                    <td><?=$cbo_source; ?> </td>
                	<td align="right">Nominated</td>
                    <td align="left"><?=$cbo_nominated_id; ?></td>
                    <td>&nbsp;</td>
                    <td><input type="button" class="image_uploader" style="width:110px" value="ADD FILE" onClick="file_uploader ( '../../', document.getElementById('txt_booking_no').value,'', 'multiple_job_wise_trims_booking_v2', 2 ,1)"> </td>
					
					<td>&nbsp;</td>
                    <td>
                      <?
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(87,'txt_booking_no','../../','');
                      ?>
                    </td>
                    
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="top" id="app_sms2" style="font-size:18px; color:#F00"></td>
                </tr>
                <tr>
                    <td align="center" colspan="8" valign="middle" class="button_container">
                    <?=$buttons ; ?>
					<input type="button" value="Send" onClick="fnSendMail('../../','update_id',1,0,0,0,0)"  style="width:80px;" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="8" height="10">
                    <input type="button" value="Print Booking" onClick="generate_trim_report('show_trim_booking_report2',1)"  style="width:100px;display:none" name="print_booking1" id="print_booking1" class="formbutton" />
					<input type="button" value="Print Booking1" onClick="generate_trim_report('show_trim_booking_report16')"  style="width:100px;display:none" name="print_booking16" id="print_booking16" class="formbutton" />
                    <input type="button" value="Print Booking2" onClick="generate_trim_report('show_trim_booking_report3',1)"  style="width:100px;display:none" name="print_booking2" id="print_booking2" class="formbutton" />
					<input type="button" value="Print Booking3" onClick="generate_trim_report('show_trim_booking_report4',1)"  style="width:100px;display:none" name="print_booking3" id="print_booking3" class="formbutton" />
					<input type="button" value="Print Booking5" onClick="generate_trim_report('show_trim_booking_report5',1)"  style="width:100px;display:none" name="print_booking5" id="print_booking5" class="formbutton" />
                    <input type="button" value="Print Booking6" onClick="generate_trim_report('show_trim_booking_report6',1)"  style="width:100px;display:none" name="print_booking6" id="print_booking6" class="formbutton" /> &nbsp;<a id="print_report6" href="" style="text-decoration:none" download hidden>BB</a>
                     <input type="button" value="Print Booking7" onClick="generate_trim_report('show_trim_booking_report7',1)"  style="width:100px;display:none" name="print_booking7" id="print_booking7" class="formbutton" />
                      <input type="button" value="Print Booking8" onClick="generate_trim_report('show_trim_booking_report8',1)"  style="width:100px;display:none" name="print_booking8" id="print_booking8" class="formbutton" />
                       <input type="button" value="Print Booking4" onClick="generate_trim_report('show_trim_booking_report9',1)"  style="width:100px;display:none" name="print_booking9" id="print_booking9" class="formbutton" />
                        <input type="button" value="Print Booking10" onClick="generate_trim_report('show_trim_booking_report10',1)"  style="width:100px;display:none" name="print_booking10" id="print_booking10" class="formbutton" />                     
						<input type="button" value="Print Booking 11" onClick="generate_trim_report('show_trim_booking_report11',1)"  style="width:100px;display:none" name="print_booking11" id="print_booking11" class="formbutton" />
						<input type="button" value="Print Booking 12" onClick="generate_trim_report('show_trim_booking_report12',1)"  style="width:100px;display:none" name="print_booking12" id="print_booking12" class="formbutton" />
                        <input type="button" value="Print Booking 13" onClick="generate_trim_report('show_trim_booking_report13',1)"  style="width:100px;display:none" name="print_booking13" id="print_booking13" class="formbutton" />
						<input type="button" value="Print Booking 14" onClick="generate_trim_report('show_trim_booking_report14',1)"  style="width:100px;display:none" name="print_booking2" id="print_booking14" class="formbutton" />
						<input type="button" value="Print Booking15" onClick="generate_trim_report('show_trim_booking_report15',1)"  style="width:100px;display:none" name="print_booking15" id="print_booking15" class="formbutton" />
						<input type="button" value="Print Booking 16" onClick="generate_trim_report('show_trim_booking_report17',1)"  style="width:100px;display:none" name="print_booking17" id="print_booking17" class="formbutton" />
                        <input type="button" value="Print Booking18" onClick="generate_trim_report('show_trim_booking_report18',1)"  style="width:100px;display:none" name="print_booking18" id="print_booking18" class="formbutton" />
                        <input type="button" value="Print Booking19" onClick="generate_trim_report('show_trim_booking_report19',1)"  style="width:100px;display:none" name="print_booking19" id="print_booking19" class="formbutton" />&nbsp;<a id="print_excel19" href="" style="text-decoration:none" download hidden>PP</a>
						<input type="button" value="Print Booking20" onClick="generate_fabric_excel_report('show_trim_booking_report20',1)"   style="width:100px;display:none"  name="print_booking20" id="print_booking20" class="formbutton" />&nbsp;<a id="print_excel20" href="" style="text-decoration:none" download hidden>PP</a>						
						<input type="button" value="Print Booking 21" onClick="generate_trim_report('show_trim_booking_report21',1)"  style="width:100px;display:none" name="print_booking21" id="print_booking21" class="formbutton" />
						<input type="button" value="Print Booking 22" onClick="generate_trim_report('show_trim_booking_report22',1)"  style="width:100px;display:none" name="print_booking22" id="print_booking22" class="formbutton" />
						<input type="button" value="Print Booking 23"onClick="generate_trim_report('show_trim_booking_report23',1)"style="width:100px;display:none" name="print_booking23" id="print_booking23" class="formbutton" />
						<input type="button" value="Print Booking 23/1"onClick="generate_trim_report('show_trim_booking_report23_1',1)"style="width:115px;display:none" name="print_booking23_1" id="print_booking23_1" class="formbutton" />
						<input type="button" value="Print Booking 24" onClick="generate_trim_report('show_trim_booking_report24',1)"  style="width:100px;display:none" name="print_booking24" id="print_booking24" class="formbutton" />
						<input type="button" value="Print Booking 25" onClick="generate_trim_report('show_trim_booking_report25',1)"  style="width:100px;display:none" name="print_booking25" id="print_booking25" class="formbutton" />
						<input type="button" value="Print Booking 26" onClick="generate_trim_report('show_trim_booking_report26',1)"  style="width:100px;display:none" name="print_booking26" id="print_booking26" class="formbutton" />
						<input type="button" value="Print Booking AAL" onClick="generate_trim_report('show_trim_booking_report_AAL',1)"  style="width:100px;display:none" name="print_booking_aal" id="print_booking_aal" class="formbutton" />
						<input type="button" value="Print Booking 27" onClick="generate_trim_report('show_trim_booking_report27',1)"  style="width:100px;display:none" name="print_booking27" id="print_booking27" class="formbutton" />
						<input type="button" value="Excel Print 18" onClick="generate_trim_report('show_trim_booking_report_excel28',1)"  style="width:100px;display:none" name="exel_print_booking18" id="exel_print_booking18" class="formbutton" />&nbsp;   
                            <? echo create_drop_down( "cbo_template_id", 90, $report_template_list,'', 0, '', 0, ""); ?>
                            &nbsp;<a id="exel_print18" href="" style="text-decoration:none" download hidden>PP</a>
                    Copy:<input type="checkbox" id="copy_val"  name="copy_val" checked/>
                    <input class="text_boxes" type="hidden" style="width:160px"  readonly  name="txt_tot_req_amount" id="txt_tot_req_amount"/> 
                    <input class="text_boxes" type="hidden" style="width:160px"  readonly  name="txt_tot_cu_amount" id="txt_tot_cu_amount"/>
                    <div style="width:950px;word-break:break-all" id="pdf_file_name"></div>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <fieldset style="width:950px;">
            <legend title="V3">Trims Booking Item Form &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            Select Item: <input class="text_boxes" type="text" style="width:160px" onDblClick="fnc_process_data();" readonly placeholder="Double Click" name="txt_select_item" id="txt_select_item"/>
            <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="txt_selected_po" id="txt_selected_po"/>
            <input class="text_boxes" type="hidden" style="width:160px"  readonly placeholder="Double Click" name="txt_selected_trim_id" id="txt_selected_trim_id"/></legend>
    <div id="booking_list_view"><font id="save_sms" style="color:#F00">Select new Item</font></div>
    </fieldset>
    <div id="booking_list_view_list"></div>
</div>
<div style="display:none" id="data_panel"></div>
</body>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';
	
	function fncChangeButton(value)
	{
		if(value==1) $("#btn_appSubmission_withoutanyChange").val("Submit For Approval");
		else $("#btn_appSubmission_withoutanyChange").val("UN-Submit For Editing");
	}
	
	function fnc_appSubmission_withoutanyChange()
	{
		freeze_window(1);
		var cbo_ready_to_approved=$("#cbo_ready_to_approved").val();
		
		var booking_no=$("#txt_booking_no").val();
				
		var submission_withoutanyChange=return_global_ajax_value(booking_no+'**'+$("#update_id").val()+'**'+cbo_ready_to_approved+'**'+$("#cbo_company_name").val(), 'appSubmission_withoutanyChange', '', 'requires/trims_booking_multi_job_controllerurmi');
		var response=submission_withoutanyChange.split('**');
		
		if(trim(response[0])=='approved'){
			alert("This Booking is Approved.");
			release_freezing();
			return;
		}
		
		if(trim(response[0])==1)
		{
			if(cbo_ready_to_approved==1)
			{
				alert("Ready To Approved Yes is Updated Successfully.");
			}
			else
			{
				alert("Ready To Approved No is Updated Successfully.");
			}
			release_freezing();
			return;
		}
		else
		{
			alert("Ready To Approved Yes is not Updated Successfully.");
			release_freezing();
			return;
		}
	}
	
	function fnc_process_data()
	{
		if (form_validation('cbo_company_name*txt_booking_no','Company*Booking No')==false){
			return;
		}
		else{

			//alert(str_data);
			//cbo_trim_type
			var txt_booking_no=document.getElementById('txt_booking_no').value;
			var txt_booking_date=document.getElementById('txt_booking_date').value;
			var garments_nature=document.getElementById('garments_nature').value;
			var cbo_booking_month=document.getElementById('cbo_booking_month').value;
			var cbo_booking_year=document.getElementById('cbo_booking_year').value;
			var cbo_company_name=document.getElementById('cbo_company_name').value;
			var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
			var cbo_currency=document.getElementById('cbo_currency').value;
			var cbo_currency_job=document.getElementById('cbo_currency_job').value;
			var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
			var cbo_level=document.getElementById('cbo_level').value;
			var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;	
			var cbo_trim_type=document.getElementById('cbo_trim_type').value;
            var cbo_source=document.getElementById('cbo_source').value;
			
			var cbo_material_source=document.getElementById('cbo_material_source').value;
			var page_link='requires/trims_booking_multi_job_controllerurmi.php?action=fnc_process_data';
			var title='PO Search For Trim Booking';
			page_link=page_link+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&company_id='+cbo_company_name+'&garments_nature='+garments_nature+'&cbo_currency='+cbo_currency+'&cbo_currency_job='+cbo_currency_job+'&cbo_supplier_name='+cbo_supplier_name+'&cbo_buyer_name='+cbo_buyer_name+'&txt_booking_no='+txt_booking_no+'&cbo_level='+cbo_level+'&txt_booking_date='+txt_booking_date+'&cbo_material_source='+cbo_material_source+'&cbo_trim_type='+cbo_trim_type+'&cbo_source='+cbo_source+'&cbo_pay_mode='+cbo_pay_mode;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1240px,height=450px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function(){
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("txt_selected_id");
				var theemail2=this.contentDoc.getElementById("txt_job_id");
				var theemail3=this.contentDoc.getElementById("txt_selected_po");
				var theemail4=this.contentDoc.getElementById("itemGroup");
				if (theemail.value!=""){
					document.getElementById('txt_select_item').value=theemail.value;
					document.getElementById('txt_selected_po').value=theemail3.value;
					document.getElementById('txt_selected_trim_id').value=theemail4.value;
					//get_php_form_data(theemail3.value+"_save_"+cbo_company_name+"_"+cbo_pay_mode+"_"+cbo_level, "set_delivery_date_from_tna", "requires/trims_booking_multi_job_controllerurmi" );
			//var tna_date=$('#txt_tna_date').val();
			 		
					fnc_generate_booking(theemail.value,theemail3.value,theemail4.value,cbo_company_name)
					
				}
			}
		}
	}
function compare_date(str)
{
	var row_num=$('#tbl_list_search tr').length;
	//alert(str);
	for (var i=1; i<=row_num; i++){
		//var txt_delevary_date_data=document.getElementById('txtddate_'+i).value;
		//var txt_delevary_date_data=document.getElementById('txttnadate_'+i).value;
		
		var txt_delevary_date_data=document.getElementById('txtddate_'+i).value;
		var txt_tna_date_data=document.getElementById('txttnadate_'+i).value;
		var booking_date=document.getElementById('txt_booking_date').value;
	if(txt_delevary_date_data=='')
	{
		txt_delevary_date_data=document.getElementById('txttnadate_'+i).value;
	}
	txt_delevary_date_data= txt_delevary_date_data.split('-');
	var txt_delevary_date_inv=txt_delevary_date_data[2]+"-"+txt_delevary_date_data[1]+"-"+txt_delevary_date_data[0];
	txt_tna_date_data = txt_tna_date_data.split('-');
	var txt_tna_date_inv=txt_tna_date_data[2]+"-"+txt_tna_date_data[1]+"-"+txt_tna_date_data[0];
	booking_date = booking_date.split('-');
	var booking_date_inv=booking_date[2]+"-"+booking_date[1]+"-"+booking_date[0];
	
	var txt_delevary_date = new Date(txt_delevary_date_inv);
    var txt_tna_date = new Date(txt_tna_date_inv);
	var txt_booking_date = new Date(booking_date_inv);
	var lib_tna_intregrate=$('#lib_tna_intregrate').val();
	//alert(lib_tna_intregrate);
	var cbo_isshort=2;
	if(cbo_isshort==1)
	{
		if(txt_delevary_date < txt_booking_date)
		{
			//salert('Delivery Date Not Allowed Less than Booking Date');
			//document.getElementById('txt_delevary_date').value='';
			txt_delevary_date_data=document.getElementById('txttnadate_'+i).value;
		}
	}
	else
	{
		if(str==1)
		{
			if(txt_tna_date_data !='')
			{
				if( lib_tna_intregrate==1)
				{
					if(txt_delevary_date > txt_tna_date)
					{
						alert('Delivery Date Not Allowed Greater Than TNA Date');
						if(txt_tna_date>txt_booking_date)
						{
							//document.getElementById('txt_delevary_date').value=document.getElementById('txt_tna_date').value;
							document.getElementById('txtddate_'+i).value=document.getElementById('txttnadate_'+i).value;
						}
						else
						{
							document.getElementById('txtddate_'+i).value='';
						}
						
						//return;
					}
					else if((txt_delevary_date < txt_booking_date) ||  (txt_booking_date > txt_tna_date))
					{
						alert('Delivery Date Not Allowed Less than Booking Date');
						//document.getElementById('txt_delevary_date').value=document.getElementById('txt_booking_date').value;
						document.getElementById('txtddate_'+i).value='';
					}
				}
				else
				{
					if((txt_delevary_date < txt_booking_date))
					{
						//alert('Delivery Date Not Allowed Less than Booking Date');
						//document.getElementById('txt_delevary_date').value=document.getElementById('txt_booking_date').value;
						//document.getElementById('txtddate_'+i).value='';
					}
				}
			}
			else
			{
				if(txt_delevary_date < txt_booking_date )
				{
					//alert('Delivery Date Not Allowed Less than Booking Date');
					
					//document.getElementById('txt_delevary_date').value=document.getElementById('txt_booking_date').value;
					document.getElementById('txtddate_'+i).value=document.getElementById('txt_booking_date').value;
				}
			}
		}
		if(str==2)
		{
			if(lib_tna_intregrate==1)
			{
				//alert(txt_tna_date);
				if(txt_tna_date !='')
				{
					if(txt_tna_date < txt_booking_date)
					{
						alert('TNA Date is Less than Booking Date');
						//document.getElementById('txt_delevary_date').value='';
						document.getElementById('txtddate_'+i).value='';
						//document.getElementById('txt_tna_date').value='';
						return;
					}
					else
					{
						//document.getElementById('txt_delevary_date').value=document.getElementById('txt_tna_date').value;
						document.getElementById('txtddate_'+i).value=document.getElementById('txttnadate_'+i).value;
						return;
					}
				}
			}
		}
	  }
	} //Loop End
}

function fnc_generate_booking(param,po_id,pre_cost_id,cbo_company_name){
	freeze_window(operation);
	var garments_nature=document.getElementById('garments_nature').value;
	var cbo_booking_month=document.getElementById('cbo_booking_month').value;
	var cbo_booking_year=document.getElementById('cbo_booking_year').value;
	var txt_delivery_date= document.getElementById('txt_delivery_date').value
	var cbo_currency=document.getElementById('cbo_currency').value;
	var cbo_currency_job=document.getElementById('cbo_currency_job').value;
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var txt_booking_date=document.getElementById('txt_booking_date').value;
	var cbo_level=document.getElementById('cbo_level').value;
	var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
	var permission='<? echo $permission; ?>';
	var param="'"+param+"'"
	var data="'"+po_id+"'"
	var precost_id="'"+pre_cost_id+"'"
	var data="action=generate_fabric_booking&data="+data+'&cbo_company_name='+cbo_company_name+'&txt_delivery_date='+txt_delivery_date+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&garments_nature='+garments_nature+'&cbo_currency='+cbo_currency+'&cbo_currency_job='+cbo_currency_job+'&cbo_level='+cbo_level+'&pre_cost_id='+precost_id+'&param='+param+'&txt_booking_no='+txt_booking_no+'&txt_booking_date='+txt_booking_date+'&cbo_pay_mode='+cbo_pay_mode+'&permission='+permission;
	http.open("POST","requires/trims_booking_multi_job_controllerurmi.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_generate_booking_reponse;
}

function fnc_generate_booking_reponse(){
	if(http.readyState == 4){
		document.getElementById('booking_list_view').innerHTML=http.responseText;
	    $("#cbo_currency").attr("disabled",true);
		compare_date(1);
		set_all_onclick();
		release_freezing();
	}
}

function copy_value(value,field_id,i){
	var copy_val=document.getElementById('copy_val').checked;
	var txttrimgroup=document.getElementById('txttrimgroup_'+i).value;
	var rowCount = $('#tbl_list_search tr').length;

	if(copy_val==true){
		freeze_window(operation);
		for(var j=i; j<=rowCount; j++){
			if(field_id=='txtdescription_'){
				if( txttrimgroup==document.getElementById('txttrimgroup_'+j).value){
					document.getElementById(field_id+j).value=value;
				}
			}
			if(field_id=='txtbrandsupref_'){
				if( txttrimgroup==document.getElementById('txttrimgroup_'+j).value){
					document.getElementById(field_id+j).value=value;
				}
			}
			if(field_id=='cbocolorsizesensitive_'){

				if( txttrimgroup==document.getElementById('txttrimgroup_'+j).value){
					document.getElementById(field_id+j).value=value;
				}
			}
		}
		release_freezing();
	}
}

function open_consumption_popup(page_link,title,po_id,i)
{
	var garments_nature=document.getElementById('garments_nature').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var txt_job_no=document.getElementById('txtjob_'+i).value;
	var txt_po_id =document.getElementById(po_id).value;
	var cbo_trim_precost_id=document.getElementById('txttrimcostid_'+i).value;
	var txt_trim_group_id=document.getElementById('txttrimgroup_'+i).value;
	var txt_update_dtls_id=document.getElementById('txtbookingid_'+i).value;
	var cbo_colorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
	var txt_req_quantity=document.getElementById('txtreqqnty_'+i).value;
	var txtwoq=document.getElementById('txtwoq_'+i).value;
	var txtbalwoq=document.getElementById('txtbalwoq_'+i).value;
	var txt_req_amount=document.getElementById('txtreqamount_'+i).value;
	var txt_avg_price=document.getElementById('txtrate_'+i).value;
	var txt_country=document.getElementById('txtcountry_'+i).value;
	var txt_pre_des=document.getElementById('txtdesc_'+i).value;
	var txt_pre_brand_sup=document.getElementById('txtbrandsup_'+i).value;
	var txtcuwoq=document.getElementById('txtcuwoq_'+i).value;
	var txtcuamount=document.getElementById('txtcuamount_'+i).value*1;
	var cbo_level=document.getElementById('cbo_level').value*1;
	var cons_breck_downn=document.getElementById('consbreckdown_'+i).value;
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
	var txtuom=document.getElementById('txtuom_'+i).value;
	var txtexchrate=document.getElementById('txtexchrate_'+i).value;
	var calculatorstring=document.getElementById('calculatorstring_'+i).value;
	1
	if(po_id==0 ){
		alert("Select Po Id")
	}
	else{
		var page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&txt_job_no='+txt_job_no+'&txt_po_id='+txt_po_id+'&cbo_trim_precost_id='+cbo_trim_precost_id+'&txt_trim_group_id='+txt_trim_group_id+'&txt_update_dtls_id='+txt_update_dtls_id+'&cbo_colorsizesensitive='+cbo_colorsizesensitive+'&txt_req_quantity='+txt_req_quantity+'&txt_avg_price='+txt_avg_price+'&txt_country='+txt_country+'&txt_pre_des='+txt_pre_des+'&txt_pre_brand_sup='+txt_pre_brand_sup+"&cbo_level="+cbo_level+"&txtwoq="+txtwoq+"&txt_booking_no="+txt_booking_no+"&cbo_supplier_name="+cbo_supplier_name+"&txtuom="+txtuom+"&txtexchrate="+txtexchrate+"&calculatorstring="+calculatorstring+"&txtbalwoq="+txtbalwoq+"&txtcuwoq="+txtcuwoq;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1280px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			var calculator_string=this.contentDoc.getElementById("calculator_string");
			var woq=this.contentDoc.getElementById("woqty_sum");
			var rate=this.contentDoc.getElementById("rate_sum");
			var amount=this.contentDoc.getElementById("amount_sum");
			var json_data=this.contentDoc.getElementById("json_data");
			document.getElementById('consbreckdown_'+i).value=cons_breck_down.value;
			document.getElementById('calculatorstring_'+i).value=calculator_string.value;
			document.getElementById('txtwoq_'+i).value=woq.value;
			document.getElementById('txtrate_'+i).value=rate.value;
			document.getElementById('txtamount_'+i).value=amount.value;
			document.getElementById('jsondata_'+i).value=json_data.value;
			calculate_amount(i);
		}
	}
}

function set_cons_break_down(i){

	document.getElementById('consbreckdown_'+i).value="";
	document.getElementById('jsondata_'+i).value="";

	var garments_nature=document.getElementById('garments_nature').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var txt_job_no=document.getElementById('txtjob_'+i).value;
	var txt_po_id =document.getElementById('txtpoid_'+i).value;
	var cbo_trim_precost_id=document.getElementById('txttrimcostid_'+i).value;
	var txt_trim_group_id=document.getElementById('txttrimgroup_'+i).value;
	var txt_update_dtls_id=document.getElementById('txtbookingid_'+i).value;
	var cbo_colorsizesensitive=document.getElementById('cbocolorsizesensitive_'+i).value;
	var txt_req_quantity=document.getElementById('txtreqqnty_'+i).value;
	var txt_avg_price=document.getElementById('txtrate_'+i).value;
	var txt_country=document.getElementById('txtcountry_'+i).value;
	var txt_pre_des=document.getElementById('txtdesc_'+i).value;
	var txt_pre_brand_sup=document.getElementById('txtbrandsup_'+i).value;
	var txtwoq=document.getElementById('txtbalwoq_'+i).value;
	var txtcurwoq=document.getElementById('txtwoq_'+i).value*1;
	var cbo_level=document.getElementById('cbo_level').value*1;
	var cons_breack_down=trim(return_global_ajax_value(garments_nature+"_"+cbo_company_name+"_"+txt_job_no+"_"+txt_po_id+"_"+cbo_trim_precost_id+"_"+txt_trim_group_id+"_"+txt_update_dtls_id+"_"+cbo_colorsizesensitive+"_"+txt_req_quantity+"_"+txt_avg_price+"_"+txt_country+"_"+txt_pre_des+"_"+txt_pre_brand_sup+"_"+cbo_level+"_"+txtcurwoq, 'set_cons_break_down', '', 'requires/trims_booking_multi_job_controllerurmi'));
	//alert(cons_breack_down);
	cons_breack_down=cons_breack_down.split("**");
    document.getElementById('consbreckdown_'+i).value=trim(cons_breack_down[0]);
    document.getElementById('jsondata_'+i).value=cons_breack_down[1];
}

function openmypage_booking(page_link,title){
	var cbo_booking_month=document.getElementById('cbo_booking_month').value;
	var cbo_booking_year=document.getElementById('cbo_booking_year').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var page_link=page_link+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&company_id='+cbo_company_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1130px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function(){
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("txt_booking");
		if (theemail.value!=""){
			reset_form('trimsbooking_1','booking_list_view','id_approved_id','txt_booking_date,<? echo date("d-m-Y"); ?>','','cbo_currency*cbo_booking_year*cbo_booking_month*copy_val');
			document.getElementById('copy_val').checked=true;
			document.getElementById('booking_list_view').innerHTML='<font id="save_sms" style="color:#F00">Select new Item</font>';
			$("#cbo_currency").attr("disabled",true);
			get_php_form_data( theemail.value, "populate_data_from_search_popup_booking", "requires/trims_booking_multi_job_controllerurmi" );
			
			fncChangeButton($("#cbo_ready_to_approved").val());
			set_button_status(1, permission, 'fnc_trims_booking',1);
		}
	}
}

function calculate_amount(i){
	var txtrate_precost=(document.getElementById('txtrate_precost_'+i).value)*1
	var txtrate=(document.getElementById('txtrate_'+i).value)*1
	var txtexchrate=(document.getElementById('txtexchrate_'+i).value)*1
	var txtwoq=(document.getElementById('txtwoq_'+i).value)*1
	document.getElementById('txtamount_'+i).value=number_format_common((txtrate*txtwoq),5,0);
	var tot_amount=0
	var row_num=$('#tbl_list_search tr').length;
	for (var j=1; j<=row_num; j++){
		var amount=document.getElementById('txtamount_'+j).value*1
		tot_amount+=amount;
	}
	document.getElementById('tot_amount').value=number_format_common(tot_amount,5,0);
}

function fnc_date_copy(i)
{
	var is_datecopy=document.getElementById('checkdate').value;
	var date=(document.getElementById('txtddate_'+i).value);
	if(is_datecopy==1)
	{		
		var row_nums=$('#tbl_list_search tr').length;
		for(var j=i; j<=row_nums; j++)
		{
			document.getElementById('txtddate_'+j).value=date;
		}
	}
}
function fnc_check(inc_id)
	{
		//alert(inc_id)
		if(inc_id=="date")
		{
			if(document.getElementById('checkdate').checked==true)
			{
				document.getElementById('checkdate').value=1;
			}
			else if(document.getElementById('checkdate').checked==false)
			{
				document.getElementById('checkdate').value=2;
			}
		}
		else
		{
			if(document.getElementById('checkid'+inc_id).checked==true)
			{
				document.getElementById('checkid'+inc_id).value=1;
			}
			else if(document.getElementById('checkid'+inc_id).checked==false)
			{
				document.getElementById('checkid'+inc_id).value=2;
			}
		}
	}
function fnc_trims_booking( operation ){
	freeze_window(operation);
	var data_all="";
	if (form_validation('cbo_company_name*cbo_buyer_name*cbo_supplier_name*txt_booking_date*cbo_source*txt_delivery_date*cbo_pay_mode*cbo_material_source','Company Name*Buyer Name*Supplier Name*Booking Date*Source*Delivery Date*Pay Mode*Material Source')==false){
		release_freezing();
		return;
	}
	var delete_cause=''; var delete_type=0;
	if(operation==2){
		var al_magg="Press OK to delete master and details part.\n Press CANCEL to delete only details part.";
		var r=confirm(al_magg);

		if(r==true)
		{
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
			delete_type=1;
		}
		else
		{
			delete_type=0;
		}
		var q=confirm("Press OK to Delete Or Press Cancel");
		if(q==false){
			release_freezing();
			return;
		}
	}

	if(mandatory_field !='')
		{
			if (form_validation(mandatory_field,mandatory_message)==false)
			{
				release_freezing();
				return;
			}
		}

	data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_booking_month*cbo_booking_year*cbo_company_name*cbo_buyer_name*cbo_supplier_name*cbo_currency*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_source*txt_attention*txt_remarks*cbo_level*cbo_ready_to_approved*cbo_nominated_id*cbo_material_source*delivery_address*cbo_trim_type*txt_tenor*cbo_pay_term*update_id*txt_internal_file_no',"../../")+"&delete_type="+delete_type;

	var data="action=save_update_delete&operation="+operation+data_all+'&delete_cause='+delete_cause;
	http.open("POST","requires/trims_booking_multi_job_controllerurmi.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_trims_booking_reponse;
}

function fnc_trims_booking_reponse(){
	if(http.readyState == 4){
		var reponse=trim(http.responseText).split('**');
		//alert(reponse)
        if(trim(reponse[0])=='10'){
            release_freezing();
            return;
        }
		if(trim(reponse[0])=='app1'){
			alert("This booking is approved")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='lockAnotherProcess'){
			alert("This booking is Attached In Trims Order Receive (Trims ERP). Ref :"+trim(reponse[1])+" \n So Update/Delete Not Allowed.")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='recv1'){
			alert("Receive Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
		    release_freezing();
		    return;
		}
		
		if(trim(reponse[0])=='pi1'){
			alert("PI Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
		    release_freezing();
		    return;
		}
		
        if(trim(reponse[0])=="delQtyExeed")
        {
            release_freezing();
			alert("Quantity Exeed Delivery Quantity. Delivery ID:"+trim(reponse[1])+"\n So Update/Delete Not Possible")
            return;
        }
        if(trim(reponse[0])=='orderFound')
        {
            alert("This booking is Attached In Trims Order Receive (Trims ERP).Receive:"+trim(reponse[2])+". Delete Not Allowed");
            release_freezing();
            return;
        }
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(trim(reponse[0]));
		if(trim(reponse[0])==0 || trim(reponse[0])==1){
            document.getElementById('txt_booking_no').value=reponse[1];
			document.getElementById('update_id').value=reponse[2];
			$("#cbo_company_name").attr("disabled",true);
		
			$("#cbo_buyer_name").attr("disabled",true);
            $("#cbo_level").attr("disabled",true);
            $("#cbo_booking_month").attr("disabled",true);
			$("#cbo_booking_year").attr("disabled",true);
			//$("#cbo_supplier_name").attr("disabled",true);
			//$("#cbo_pay_mode").attr("disabled",true);
			$("#cbo_material_source").attr("disabled",true);
			$("#cbo_source").attr("disabled",true);
			set_button_status(1, permission, 'fnc_trims_booking',1);
		}
		else if(trim(reponse[0])==2){
			location.reload();
		}
		release_freezing();
	}
}

function fnc_trims_booking_dtls( operation ){
    console.log(document.getElementById('cbo_level').value);
    //return;
	freeze_window(operation);
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
	if (form_validation('txt_booking_no','Booking No')==false){
		release_freezing();
		return;
	}
	data_all=data_all+get_submitted_data_string('txt_booking_no*strdata*cbo_pay_mode*update_id',"../../");

	var row_num=$('#tbl_list_search tr').length;
	if(row_num <1){
		alert("Select Item");
		release_freezing();
		return;
	}
	 //var reg=/[^a-zA-Z0-9!@#$%^,;|\[\]\- ]/g;
	  var reg=/[^a-zA-Z0-9!@#$%^,;.:<>{}?\+|\[\]\- \/]/g;
	
	

	for (var i=1; i<=row_num; i++){
		var txtrate=document.getElementById('txtrate_'+i).value
		if($("#cbo_material_source").val() != 3) {
			if(txtrate=="" || txtrate==0){
					alert("Insert Rate")
					release_freezing();
					return;
			}
		}
		var txt_booking_date=$('#txt_booking_date').val();
		var cbo_pay_mode=$("#cbo_pay_mode").val();
		var delivery_date=$('#txtddate_'+i).val();
		var lib_tna_intregrate=$('#lib_tna_intregrate').val();
		//alert(lib_tna_intregrate);
		if(lib_tna_intregrate==1  && delivery_date=="")
		{
			alert('Delivery date is empty');
			//$('#txtddate_'+i).focus();
			release_freezing();
			return;
		}
		if(lib_tna_intregrate==2  && delivery_date=="")  //28170 for Islam Group
		{
				alert('Delivery date is empty');
				release_freezing();
					return;
		}
		//alert(lib_tna_intregrate+'='+delivery_date);
		if((lib_tna_intregrate==2 || lib_tna_intregrate==0 ) && delivery_date!="") //28170 for Islam Group
		{
			
			if(date_compare(txt_booking_date, delivery_date)==false)
			{
				alert('Delivery Date Not Allowed Less than Booking Date');
				document.getElementById('txtddate_'+i).value='';
				release_freezing();
					return;
			}
		}

		var consbreckdown=document.getElementById('consbreckdown_'+i).value
		var txtbookingid=document.getElementById('txtbookingid_'+i).value
		if (consbreckdown=="" ){//&& txtbookingid==""
			set_cons_break_down(i)
		}
		var consbreckdown=document.getElementById('consbreckdown_'+i).value
		if(operation!=2) //Delete not allowed for this, only for Save and Update
		{
			if (trim(consbreckdown)=="" ){//&& operation ==0
				alert("Unable to create Cons break down for minimum work order Qty, Data  not saved");
				release_freezing();
				$('#search'+i).css('background-color', 'red');
				return;
			}
		}
		data_all=data_all+get_submitted_data_string('txtbookingid_'+i+'*txtpoid_'+i+'*txtcountry_'+i+'*txttrimcostid_'+i+'*txtReqAmt_'+i+'*txttrimgroup_'+i+'*txtuom_'+i+'*cbocolorsizesensitive_'+i+'*txtwoq_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtddate_'+i+'*consbreckdown_'+i+'*txtexchrate_'+i+'*txtjob_'+i+'*txtreqqnty_'+i+'*jsondata_'+i+'*txtdesc_'+i+'*txtbrandsup_'+i+'*txtreqamount_'+i+'*txtreqamountjoblevelconsuom_'+i+'*txtreqamountitemlevelconsuom_'+i+'*txthscode_'+i+'*txtremark_'+i+'*hiddlabeldtlsdata_'+i+'*calculatorstring_'+i,"../../",i);
	}
	var cbo_level=document.getElementById('cbo_level').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	
	if(cbo_level==1){
		var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+data_all+'&delete_cause='+delete_cause+'&cbo_company_name='+cbo_company_name;
	}

	if(cbo_level==2){
		var data="action=save_update_delete_dtls_job_level&operation="+operation+'&total_row='+row_num+data_all+'&delete_cause='+delete_cause+'&cbo_company_name='+cbo_company_name;
	}
	// alert(data);
	http.open("POST","requires/trims_booking_multi_job_controllerurmi.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_trims_booking_dtls_reponse;
}

function fnc_trims_booking_dtls_reponse(){
	if(http.readyState == 4){
		var reponse=trim(http.responseText).split('**');
		if(trim(reponse[0])=='app1'){
			alert("This booking is approved")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='lockAnotherProcess'){
			alert("This booking is Attached In Trims Order Receive (Trims ERP). Ref :"+trim(reponse[1])+" \n So Update/Delete Not Allowed.")
		    release_freezing();
		    return;
		}
		/*if(trim(reponse[0])=='recv1'){
			alert("Receive Qty  :"+trim(reponse[3])+" Found in Receive Number "+ trim(reponse[2])+" \n So WOQ Less Then Receive Qty/Delete Not Possible")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='pi1'){
			alert("PI Qty  :"+number_format(trim(reponse[3]),2,'.','' )+" Found in PI Number "+ trim(reponse[2])+" \n So WOQ Less Then PI Qty/Delete Not Possible")
		    release_freezing();
		    return;
		}*/
		if(trim(reponse[0])=='recvRate1'){
			alert("Receive Rate Change Found, Receive No :"+trim(reponse[2])+"\n So Update/Delete Not Possible");
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='piapp'){
			alert("PI Approved Update or Delete Not Possible")
			release_freezing();
			return;
		}
		if(reponse[3]>0)
		{
			if(trim(reponse[0])=='recv1'){
				alert("Receive Qty  :"+trim(reponse[3])+" Found in Receive Number "+ trim(reponse[2])+" \n So WOQ Less Then Receive Qty/Update or Delete Not Possible")
				release_freezing();
				return;
			}			
			if(trim(reponse[0])=='pi1'){
				alert("PI Qty  :"+number_format(trim(reponse[3]),2,'.','' )+" Found in PI Number "+ trim(reponse[2])+" \n So WOQ Less Then PI Qty/Update or Delete Not Possible")
				release_freezing();
				return;
			}
			if(trim(reponse[0])=='pi1amt'){
				alert("PI Amount  :"+number_format(trim(reponse[3]),2,'.','' )+" Found in PI Number "+ trim(reponse[2])+" \n So WOAmount Less Then PI Amount/Update or Delete Not Possible")
				release_freezing();
				return;
			}
		}
		else
		{
			if(trim(reponse[0])=='recv1'){
    			alert("Receive Number Found "+ trim(reponse[2])+" \n So Update or Delete Not Possible")
    		    release_freezing();
    		    return;
    		}
    		if(trim(reponse[0])=='pi1'){
    			alert(" PI Number Found"+ trim(reponse[2])+" \n So Update or Delete Not Possible")
    		    release_freezing();
    		    return;
    		}
		}
        if(trim(reponse[0])=='orderFound')
        {
          //  alert("This booking is Attached In Trims Order Receive (Trims ERP).Receive:"+trim(reponse[1])+". Delete Not Allowed");
           // release_freezing();
           // return;
        }
		
		if(trim(reponse[0])=='orderFound')
        {
            alert("WO Qty is not less than Trims Order Receive (Trims ERP)\n Receive No:"+trim(reponse[2])+". \n Delete Not Allowed");
            release_freezing();
            return;
        }
		

		if(trim(reponse[0])=='vad1'){
			alert("Budget amount Exceed")
			$('#search'+reponse[2]).css('background-color', 'red');
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='vad2'){
			alert("Budget Qty Exceed")
			$('#search'+reponse[2]).css('background-color', 'red');
		    release_freezing();
		    return;
		}
        if(trim(reponse[0])=='﻿﻿delQtyExeed'){
            alert("Quantity Exeed Delivery Quantity. Delivery ID:"+trim(reponse[1])+"\n So Update/Delete Not Possible")
            release_freezing();
            return;
        }

		if (reponse[0].length>2) reponse[0]=10;
		show_msg(trim(reponse[0]));
		if(trim(reponse[0])==0 || trim(reponse[0])==1 || trim(reponse[0])==2){
			var str="";
			if(trim(reponse[0])==0){
				str='Saved';
			//	$("#cbo_supplier_name").attr("disabled",true);
				//$("#cbo_pay_mode").attr("disabled",true);
			}
			if(trim(reponse[0])==1){
				str='Updated';
			//	$("#cbo_supplier_name").attr("disabled",true);
			//	$("#cbo_pay_mode").attr("disabled",true);

			}
			if(trim(reponse[0])==2){
				str='Deleted';
			}
			document.getElementById('txt_select_item').value='';
			document.getElementById('booking_list_view').innerHTML=''
			document.getElementById('booking_list_view').innerHTML='<font id="save_sms" style="color:#F00">Data '+str+', Select new Item</font>';
			fnc_show_booking_list()
		}
		release_freezing();
	}
}

function fnc_show_booking_list(){
	var garments_nature=document.getElementById('garments_nature').value;
	var cbo_booking_month=document.getElementById('cbo_booking_month').value;
	var cbo_booking_year=document.getElementById('cbo_booking_year').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbo_currency=document.getElementById('cbo_currency').value;
	var data=document.getElementById('txt_selected_po').value;
	var precost_id=document.getElementById('txt_selected_trim_id').value;
	var param=document.getElementById('txt_select_item').value;

	var param="'"+param+"'"
	var data="'"+data+"'"
	var precost_id="'"+precost_id+"'"

	var cbo_level=document.getElementById('cbo_level').value;

	var data="action=show_trim_booking_list"+get_submitted_data_string('txt_booking_no',"../../")+'&cbo_company_name='+cbo_company_name+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&garments_nature='+garments_nature+'&data='+data+'&param='+param+'&pre_cost_id='+precost_id+'&cbo_level='+cbo_level+'&cbo_currency='+cbo_currency;
	http.open("POST","requires/trims_booking_multi_job_controllerurmi.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_show_booking_list_reponse;
}

function fnc_show_booking_list_reponse(){
	if(http.readyState == 4){
        $("#cbo_currency").attr("disabled",true);
		document.getElementById('booking_list_view_list').innerHTML=http.responseText;
		set_button_status(1, permission, 'fnc_trims_booking',2);
		set_all_onclick();
	}
}

function fnc_show_booking(wo_pre_cost_trim_id,po_id,booking_id,job_no){
	freeze_window(operation);
	var garments_nature=document.getElementById('garments_nature').value;
	var cbo_booking_month=document.getElementById('cbo_booking_month').value;
	var cbo_booking_year=document.getElementById('cbo_booking_year').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbo_currency=document.getElementById('cbo_currency').value;
	var data=document.getElementById('txt_selected_po').value;
	var precost_id=document.getElementById('txt_selected_trim_id').value;
	var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
	var param=document.getElementById('txt_select_item').value;
	var permission='<? echo $permission; ?>';

	var cbo_level=document.getElementById('cbo_level').value;

	var data="action=show_trim_booking"+get_submitted_data_string('txt_booking_no',"../../")+'&cbo_company_name='+cbo_company_name+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&garments_nature='+garments_nature+'&data='+po_id+'&booking_id='+booking_id+'&pre_cost_id='+wo_pre_cost_trim_id+'&cbo_level='+cbo_level+'&cbo_currency='+cbo_currency+'&job_no='+job_no+'&cbo_pay_mode='+cbo_pay_mode+'&permission='+permission;
	http.open("POST","requires/trims_booking_multi_job_controllerurmi.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_show_booking_reponse;
}

function fnc_show_booking_reponse(){
	if(http.readyState == 4){
        $("#cbo_currency").attr("disabled",true);
		document.getElementById('booking_list_view').innerHTML=http.responseText;
			//compare_date(2);
		set_all_onclick();
		release_freezing();
	}
}

function generate_trim_report(action,report_type,mail_send_data){
	if (form_validation('txt_booking_no','Booking No')==false){
		return;
	}
	else
	{
		if(action=='show_trim_booking_report3')
		{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r==true) show_comment="1"; else show_comment="0";
		}
        else if(action=='show_trim_booking_report12') //show_trim_booking_report_wg
		{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide Rate,Amount,Remarks and Comments\nPress  \"OK\"  to Show Rate,Amount,Remarks and Comments");
			if (r==true) show_comment="1"; else show_comment="0";
		}
		else if(action=='show_trim_booking_report_wg')
		{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r==true) show_comment="1"; else show_comment="0";
		}
		else if(action=='show_trim_booking_report5')
		{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
			if (r==true) show_comment="1"; else show_comment="0";
		}
        else if(action=='show_trim_booking_report13')
        {
            var show_comment='';
            var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
            if (r==true) show_comment="1"; else show_comment="0";
        }
		else if(action=='show_trim_booking_report27')
        {
            var show_comment='';
            var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
            if (r==true) show_comment="1"; else show_comment="0";
        }
		else if(action=='show_trim_booking_report26')
        {
            var show_comment='';
            var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
            if (r==true) show_comment="1"; else show_comment="0";
        }
		else if(action=='show_trim_booking_report_AAL')
        {
            var show_comment='';
            var r=confirm("Press  \"Cancel\"  to hide Rate,Amount and Remarks\nPress  \"OK\"  to Show Rate,Amount and Remarks");
            if (r==true) show_comment="1"; else show_comment="0";
        }
		else if(action=='show_trim_booking_report9')
        {
            var show_comment='';
            var r=confirm("Press  \"Cancel\"  to hide Rate\nPress  \"OK\"  to Show Rate");
            if (r==true) show_comment="1"; else show_comment="0";
        }
		else if(action=='show_trim_booking_report16')
        {
            var show_comment='';
            var r=confirm("Press  \"Cancel\"  to hide Rate & Amount\nPress  \"OK\"  to Show Rate & Amount");
            if (r==true) show_comment="1"; else show_comment="0";
        }
		else if(action=='show_trim_booking_report17')
        {
            var show_comment='';
            var r=confirm("Press  \"Cancel\"  to hide Rate, Amount & Remarks\nPress  \"OK\"  to Show Rate, Amount & Remarks");
            if (r==true) show_comment="1"; else show_comment="0";
        }
        else if(action=='show_trim_booking_report6')
        {
            var show_comment=1;
        }
		else if(action=='show_trim_booking_report18')
        {
            var show_comment=1;
        }

		else if(action=='show_trim_booking_report7')
        {
            var show_comment='';
            var r=confirm("Press  \"Cancel\"  to hide Remarks and  Total Amount \nPress  \"OK\"  to Show  Remarks  and Total Amount");
            if (r==true) show_comment="1"; else show_comment="0";
        }
		else
		{
			var show_comment='';
			var r=confirm("Press  \"Cancel\"  to hide  Remarks\nPress  \"OK\"  to Show Remarks");
			if (r==true) show_comment="1"; else show_comment="0";
		}
		var report_title=$( "div.form_caption" ).html();
        // freeze_window();
		var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_template_id',"../../")+'&report_title='+report_title+'&show_comment='+show_comment+'&report_type='+report_type+'&mail_send_data='+mail_send_data;
		http.open("POST","requires/trims_booking_multi_job_controllerurmi.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;
	
	}
}
function generate_trim_report_reponse(){
	if(http.readyState == 4){
        release_freezing();
		var file_data=http.responseText.split("####");
         //alert(file_data[2]);
		if(file_data[2]==100)
        {
        $('#data_panel').html(file_data[0]);
        $('#print_report6').removeAttr('href').attr('href','requires/'+trim(file_data[1]));
            //$('#print_report4')[0].click();
        document.getElementById('print_report6').click();
        }
		//alert(file_data[2]);
		 if(file_data[2]==101)
        {
			// alert(file_data[1]);
        $('#data_panel').html(file_data[0]);
        $('#print_excel19').removeAttr('href').attr('href','requires/'+trim(file_data[1]));
            //$('#print_excel19')[0].click();
        document.getElementById('print_excel19').click();
        }
        else{
          $('#pdf_file_name').html(file_data[1]);
          $('#data_panel').html(file_data[0]);
        }
		if(file_data[2]==103)
        {
			$('#data_panel').html(file_data[0]);
			$('#exel_print18').removeAttr('href').attr('href','requires/'+trim(file_data[1]));
			document.getElementById('exel_print18').click();
        }else{
          $('#pdf_file_name').html(file_data[1]);
          $('#data_panel').html(file_data[0]);
        }
        
        var report_title=$( "div.form_caption" ).html();
        var w = window.open("Surprise", "_blank");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title>'+report_title+'</title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
        d.close();
	}
}



function generate_fabric_excel_report(action,report_type)
{
	if (form_validation('txt_booking_no','Booking No')==false){
			return;
	}
	else
	{
		var show_comment='';
		var r=confirm("Press  \"Cancel\"  to hide  Remarks\nPress  \"OK\"  to Show Remarks");
		if (r==true) show_comment="1"; else show_comment="0";
	
		var report_title=$( "div.form_caption" ).html();
		  // freeze_window();
	
	
		var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id',"../../")+'&report_title='+report_title+'&show_comment='+show_comment+'&report_type='+report_type;
		var excel_check=0;

		if(action=='show_trim_booking_report20')
		{
			freeze_window(5);
			var user_id = "<? echo $user_id; ?>";
			$.ajax({
				url: 'requires/trims_booking_multi_job_controllerurmi.php',
				type: 'POST',
				data: data,
				success: function(data){
					window.open('../../auto_mail/tmp/multiple_trims_booking_v2_'+user_id+'.pdf');
					release_freezing();
				}
			});
			var excel_check=1;
		}
		if (excel_check==1){

			freeze_window(5);
			http.open("POST","requires/trims_booking_multi_job_controllerurmi.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_fabric_report_reponse2;
		}
	}
}

function generate_fabric_report_reponse2(){
		if(http.readyState == 4){
		release_freezing();
		var file_data=http.responseText.split("####");
		if(file_data[2]==101)
		  {
			 $('#data_panel').html(file_data[0]);
			 $('#print_excel20').removeAttr('href').attr('href','requires/'+trim(file_data[1]));
			 //$('#print_excel20')[0].click();
			 document.getElementById('print_excel20').click();
		  }
		else{
			$('#data_panel').html(file_data[0]);
		}
	}
}

//for print button
function print_report_button_setting(report_ids)
{
	$("#print_booking1").hide();
	$("#print_booking2").hide();
	$("#print_booking4").hide();
	$("#print_booking5").hide();
	$("#print_booking6").hide();
	$("#print_booking7").hide();
	$("#print_booking8").hide();
	$("#print_booking9").hide();
	$("#print_booking10").hide();
	$("#print_booking11").hide();
    $("#print_booking12").hide();
	$("#print_booking13").hide();
	$("#print_booking14").hide();
	$("#print_booking15").hide();
	$("#print_booking16").hide();
	$("#print_booking17").hide();
	$("#print_booking18").hide();
	$("#print_booking19").hide();
	$("#print_booking20").hide();
	$("#print_booking21").hide();
	$("#print_booking22").hide();
	$("#print_booking23").hide();
	$("#print_booking24").hide();
	$("#print_booking_wg").hide();
	$("#print_booking25").hide();
	$("#print_booking26").hide();
	$("#print_booking_aal").hide();
	$("#print_booking23_1").hide();
	$("#print_booking27").hide();
	$("#exel_print_booking18").hide();
	
	var report_id=report_ids.split(",");
	for (var k=0; k<report_id.length; k++)
	{
		//177
		//alert(report_id[k])
		if(report_id[k]==67) $("#print_booking1").show();
		else if(report_id[k]==183) $("#print_booking2").show();		
		else if(report_id[k]==209) $("#print_booking3").show();
		else if(report_id[k]==235) $("#print_booking5").show();
        else if(report_id[k]==176) $("#print_booking6").show();
		else if(report_id[k]==746) $("#print_booking7").show();
		else if(report_id[k]==227) $("#print_booking8").show();
		else if(report_id[k]==177) $("#print_booking9").show();
		else if(report_id[k]==241) $("#print_booking11").show();
		else if(report_id[k]==274) $("#print_booking10").show();
        else if(report_id[k]==269) $("#print_booking12").show();
		else if(report_id[k]==28) $("#print_booking13").show();
		else if(report_id[k]==280) $("#print_booking14").show();
		else if(report_id[k]==304) $("#print_booking15").show();
		else if(report_id[k]==14) $("#print_booking16").show();
		else if(report_id[k]==719) $("#print_booking17").show();
		else if(report_id[k]==339) $("#print_booking18").show();
		else if(report_id[k]==433) $("#print_booking19").show();
		else if(report_id[k]==768) $("#print_booking20").show();
		else if(report_id[k]==404) $("#print_booking21").show();
		else if(report_id[k]==419) $("#print_booking22").show();
		else if(report_id[k]==426) $("#print_booking23").show();
		else if(report_id[k]==809) $("#print_booking23_1").show();
		else if(report_id[k]==774) $("#print_booking_wg").show();
		else if(report_id[k]==452) $("#print_booking24").show();
		else if(report_id[k]==786) $("#print_booking25").show();
		else if(report_id[k]==502) $("#print_booking26").show();
		else if(report_id[k]==845) $("#print_booking_aal").show();
		else if(report_id[k]==437) $("#print_booking27").show();
		else if(report_id[k]==875) $("#exel_print_booking18").show();
	}
}

function check_paymode()
{
	$('#cbo_pay_mode').val('');
}

function openmypage_unapprove_request()
{
	if (form_validation('txt_booking_no','Booking Number')==false)
	{
		return;
	}

	var txt_booking_no=document.getElementById('txt_booking_no').value;
	var txt_un_appv_request=document.getElementById('txt_un_appv_request').value;
	var data=txt_booking_no+"_"+txt_un_appv_request;
	var title = 'Un Approval Request';
	var page_link = 'requires/trims_booking_multi_job_controllerurmi.php?data='+data+'&action=unapp_request_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
		$('#txt_un_appv_request').val(unappv_request.value);
	}
}

function openlabeldtls_popup(trimitem,i)
{
	var title = 'Label Details';
	
	var page_link = 'requires/trims_booking_multi_job_controllerurmi.php?data='+trimitem+'&action=labeldtls_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var labeldtlsdata=this.contentDoc.getElementById("hidd_dtlsdata").value;
		
		$('#hiddlabeldtlsdata_'+i).val(labeldtlsdata);
		
	}
}

function deletedata(){
	
		var operation=2;
		freeze_window(operation);
		
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

		var txt_booking_no=document.getElementById('txt_booking_no').value;
		var check_is_booking_used_id=return_global_ajax_value(txt_booking_no, 'check_is_booking_used', '', 'requires/trims_booking_multi_job_controllerurmi');
		var reponse=trim(check_is_booking_used_id).split('**');
		if(trim(reponse[0])!="")
		{
			if(trim(reponse[0])=='approved'){
				alert("This booking is approved");
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
			//alert("This booking used in PI Table. So Adding or removing order is not allowed")
			return;
		}

		var row_num=1;
		if (form_validation('txt_booking_no','Booking No')==false){
			release_freezing()
			return;
		}
		
        var i=1; var dltsid=""; var z=1;
		var data_all=get_submitted_data_string('txt_booking_no',"../../",i);
		var listrows =$('#list_view tbody tr').length; 
		
		if(document.getElementById('chkdeleteall').checked==true)
		{
			for (var i = 1; i <= listrows; i++)
			{
				document.getElementById('chkdelete_'+i).checked=true;
				dltsid+="&txtbookingid_"+z+"='" + $('#txtdelete'+i).val()+"'";
				z++;
			}
		}
		else
		{
			for (var i = 1; i <= listrows; i++)
			{
				if(document.getElementById('chkdelete_'+i).checked==true)
				{
					dltsid+="&txtbookingid_"+z+"='" + $('#txtdelete'+i).val()+"'";
					z++;
				}
			}
		}
		if(z==1 && dltsid=="")
		{
			alert("Please Select minimum 1 row.");
			release_freezing()
			return;
		}
		var data="action=delete_dtls_data&operation="+operation+'&total_row='+z+dltsid+data_all+"&delete_cause="+delete_cause;
		
		/*alert(data);release_freezing()
			return;*/
		http.open("POST","requires/trims_booking_multi_job_controllerurmi.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_trims_booking_dtls_reponse;
	}

</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>