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
echo load_html_head_contents("Woven Trims Booking", "../../", 1, 1,$unicode,1,'');

?>
<script>
	<?
	
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][273] ); 
	echo "var field_level_data= ". $data_arr . ";\n";
    echo "var mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][273]) . "';\n";
    echo "var field_message = '". implode('*',$_SESSION['logic_erp']['field_message'][273]) . "';\n";
?>

    $( document ).ready(function() {
        <?
            if(!empty($_SESSION['logic_erp']['mandatory_field'][273][1]))
            {
                echo "document.getElementById('responsible_person_td').style.color = 'blue';\n";
            }
            if(!empty($_SESSION['logic_erp']['mandatory_field'][273][2]))
            {
                echo "document.getElementById('reason_td').style.color = 'blue';\n";
            }
            if(!empty($_SESSION['logic_erp']['mandatory_field'][273][3]))
            {
                echo "document.getElementById('responsible_dept_td').style.color = 'blue';\n";
            }
           
        ?>
    });
</script>
<?
//----------------------------------------------------------------------------------------------------------------------------------
$date                  = date('d-m-Y');
$level_arr             = array(1=>"PO Level",2=>"Job Level");
$buyer_cond            = set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond          = set_user_lavel_filtering(' and comp.id','company_id');
$cbo_booking_month     = create_drop_down( "cbo_booking_month", 90, $months,"", 1, "-- Select --", "", "",0 );
$cbo_booking_year      = create_drop_down( "cbo_booking_year", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );
$cbo_company_name      = create_drop_down( "cbo_company_name", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name",1, "-- Select Company --", $selected, "get_php_form_data( this.value, 'populate_variable_setting_data', 'requires/short_trims_booking_multi_job_controllerurmi' );","","" );
$cbo_buyer_name        = create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --", $selected, "","" );
$cbo_supplier_name     = create_drop_down( "cbo_supplier_name", 172, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=4 and   a.status_active =1 and a.is_deleted=0 order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/short_trims_booking_multi_job_controllerurmi');",0 );
$cbo_currency          = create_drop_down( "cbo_currency", 172, $currency,"", 1, "-- Select --", 2, "",0 );
$cbo_pay_mode          = create_drop_down( "cbo_pay_mode", 172, $pay_mode,"", 1, "-- Select Pay Mode --", "", "load_drop_down( 'requires/short_trims_booking_multi_job_controllerurmi', this.value, 'load_drop_down_supplier', 'supplier_td' )","" );
$cbo_source            = create_drop_down( "cbo_source", 172, $source,"", 1, "-- Select Source --", "", "","" );
$cbo_level             = create_drop_down( "cbo_level", 172, $level_arr,"", 0, "", 2, "","","" );
$cbo_ready_to_approved = create_drop_down( "cbo_ready_to_approved", 172, $yes_no,"", 1, "-- Select--", 2, "","","" );
$endis                 = "disable_enable_fields( 'cbo_currency*cbo_company_name*cbo_supplier_name*cbo_level*cbo_buyer_name', 0 )";
$buttons               = load_submit_buttons( $permission, "fnc_trims_booking", 0,0 ,"reset_form('trimsbooking_1','booking_list_view*booking_list_view_list*app_sms2*pdf_file_name','id_approved_id*txt_select_item','txt_booking_date,".$date."*cbo_ready_to_approved,2',$endis,'cbo_currency*cbo_booking_year*cbo_booking_month*copy_val*cbo_pay_mode*cbo_source*cbo_supplier_name*txt_attention*txt_delivery_date*cbo_level*cbo_company_name*cbo_buyer_name')",1);
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
                    <td align=""></td>
                    <td align="" valign="top">
                    </td>
                    <td  width="130" height="" align="right" class="must_entry_caption">Booking No </td>
                    <td  width="170" >
                    <input class="text_boxes" type="text" style="width:160px" onDblClick="openmypage_booking('requires/short_trims_booking_multi_job_controllerurmi.php?action=trims_booking_popup','Trims Booking Search')"  placeholder="Double Click for Booking" name="txt_booking_no" id="txt_booking_no" readonly/>
                    </td>
                    <td align="">
                    <input type="hidden" id="id_approved_id">
                    <input type="hidden" id="exeed_budge_qty">
                    <input type="hidden" id="exeed_budge_amount">
                    <input type="hidden" id="amount_exceed_level">
                    <input type="hidden" id="report_ids" />
                    <input type="hidden" id="cbo_currency_job"  />
                    <input type="hidden" id="booking_mst_id"  />
                    </td>
                    <td align=""></td>
                </tr>
                <tr>
                    <td align="right" class="must_entry_caption">Shipment Month</td>
                    <td> <? echo $cbo_booking_month.$cbo_booking_year;	?> </td>
                    <td  align="right" class="must_entry_caption">Company Name</td>
                    <td><? echo $cbo_company_name;?></td>
                    <td align="right" class="must_entry_caption">Buyer Name</td>
                    <td id="buyer_td">
                    <? echo $cbo_buyer_name; ?>
                    </td>
                </tr>
                <tr>

                    <td  width="130" align="right" class="must_entry_caption">Booking Date</td>
                    <td width="170">
                    <input class="datepicker" type="text" style="width:160px" name="txt_booking_date" id="txt_booking_date" value="<? echo $date ?>" disabled />
                    </td>
                    <td  width="130" align="right" class="must_entry_caption">Delivery Date</td>
                    <td width="170">
                    <input class="datepicker" type="text" style="width:160px" name="txt_delivery_date" id="txt_delivery_date"/>
                    </td>

                     <td class="must_entry_caption" align="right">Pay Mode</td>
                    <td> <? echo $cbo_pay_mode; ?></td>
                </tr>
                <tr>
                    <td align="right">Currency</td>
                    <td><? echo $cbo_currency; ?></td>
                   <td  align="right" class="must_entry_caption">Supplier Name</td>
                    <td id="supplier_td"><? echo $cbo_supplier_name;?></td>
                    <td align="right" width="130"  class="must_entry_caption"> Source </td>
                    <td width="170" ><? echo $cbo_source; ?> </td>
                </tr>
                <tr>
                    <td align="right">Attention</td>
                    <td align="left" height="10">
                    <input class="text_boxes" type="text" style="width:160px;"  name="txt_attention" id="txt_attention"/>
                    </td>
                    <td align="right">Ready To Approved</td>
                    <td align="left" height="10"><? echo $cbo_ready_to_approved; ?></td>
                    <td align="right">Level</td>
                    <td align="left" height="10"><? echo $cbo_level; ?></td>
                </tr>
                <tr>
                    <td align="right" id="responsible_dept_td">Responsible Dept.</td>
                    <td><?=create_drop_down( "cbo_responsible_dept", 172,"select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by  department_name", "id,department_name", 0, "", '', ''); ?></td>
                    <td align="right" id="responsible_person_td">Responsible person</td>
                    <td><input name="cbo_responsible_person" id="cbo_responsible_person" class="text_boxes" type="text" value=""  style="width:160px "/></td>
                    <td align="right" id="reason_td">Reason</td>
                    <td width="170"><input name="txt_reason" id="txt_reason" class="text_boxes" type="text" value="" style="width:160px" /></td>
                </tr>
                <tr>
                    <td align="right">Remarks</td>
                    <td align="left" height="10" colspan="3">
                    <input class="text_boxes" type="text" style="width:97%;"  name="txt_remarks" id="txt_remarks"/>
                    </td>
                    <td align="right"></td>
                    <td align="center" height="10">
                    <!-- <input type="button" id="set_button" class="image_uploader" style="width:160px;" value="Terms & Condition/Notes" onClick="open_terms_condition_popup('requires/short_trims_booking_multi_job_controllerurmi.php?action=terms_condition_popup','Terms and Condition')" /> -->

                    <?
                        //include("../../terms_condition/terms_condition.php");
                        //terms_condition(87,'txt_booking_no','../../');
                    ?>
                    <?
                        include("../../terms_condition/terms_condition.php");
                        terms_condition(87,'txt_booking_no','../../','');
                    ?>

                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="6" valign="top" id="app_sms2" style="font-size:18px; color:#F00">

                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="6" valign="middle" class="button_container">
                    <? echo $buttons ; ?>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan="6" height="10">
                    <input type="button" value="Print Booking" onClick="generate_trim_report('show_trim_booking_report2',1)"  style="width:100px;display:none" name="print_booking1" id="print_booking1" class="formbutton" />
                    <input type="button" value="Print Booking 2" onClick="generate_trim_report('show_trim_booking_report3',1)"  style="width:100px;display:none" name="print_booking2" id="print_booking2" class="formbutton" />
                    Copy:<input type="checkbox" id="copy_val"  name="copy_val" checked/>
                    <input class="text_boxes" type="hidden" style="width:160px"  readonly  name="txt_tot_req_amount" id="txt_tot_req_amount"/>
                    <input class="text_boxes" type="hidden" style="width:160px"  readonly  name="txt_tot_cu_amount" id="txt_tot_cu_amount"/>
                    <div id="pdf_file_name"></div>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <fieldset style="width:950px;">
            <legend title="V3">Trims Booking Item Form &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            Select Item: <input class="text_boxes" type="text" style="width:160px" onDblClick="fnc_process_data()" readonly placeholder="Double Click" name="txt_select_item" id="txt_select_item"/>
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
		var cbo_currency_job=document.getElementById('cbo_currency_job').value;
		var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
		var cbo_level=document.getElementById('cbo_level').value;
	    var page_link='requires/short_trims_booking_multi_job_controllerurmi.php?action=fnc_process_data';
		var title='Trim Booking Search';
		page_link=page_link+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&company_id='+cbo_company_name+'&garments_nature='+garments_nature+'&cbo_currency='+cbo_currency+'&cbo_currency_job='+cbo_currency_job+'&cbo_supplier_name='+cbo_supplier_name+'&cbo_level='+cbo_level+'&cbo_buyer_name='+cbo_buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1340px,height=450px,center=1,resize=1,scrolling=0','../')
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
				fnc_generate_booking(theemail.value,theemail3.value,theemail4.value,cbo_company_name)
			}
		}
	}
}

function fnc_generate_booking(param,po_id,pre_cost_id,cbo_company_name){
	freeze_window(operation);
	var garments_nature=document.getElementById('garments_nature').value;
	var cbo_booking_month=document.getElementById('cbo_booking_month').value;
	var cbo_booking_year=document.getElementById('cbo_booking_year').value;
	var txt_delivery_date= document.getElementById('txt_delivery_date').value
	var cbo_currency=document.getElementById('cbo_currency').value;
	var cbo_currency_job=document.getElementById('cbo_currency_job').value;
	var cbo_level=document.getElementById('cbo_level').value;
	var param="'"+param+"'"
	var data="'"+po_id+"'"
	var precost_id="'"+pre_cost_id+"'"
	var data="action=generate_fabric_booking&data="+data+'&cbo_company_name='+cbo_company_name+'&txt_delivery_date='+txt_delivery_date+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&garments_nature='+garments_nature+'&cbo_currency='+cbo_currency+'&cbo_currency_job='+cbo_currency_job+'&cbo_level='+cbo_level+'&pre_cost_id='+precost_id+'&param='+param;
	http.open("POST","requires/short_trims_booking_multi_job_controllerurmi.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_generate_booking_reponse;
}

function fnc_generate_booking_reponse(){
	if(http.readyState == 4){
		document.getElementById('booking_list_view').innerHTML=http.responseText;
	    $("#cbo_currency").attr("disabled",true);
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

function open_consumption_popup(page_link,title,po_id,i){
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

	if(po_id==0 ){
		alert("Select Po Id")
	}

	else{
		var page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&txt_job_no='+txt_job_no+'&txt_po_id='+txt_po_id+'&cbo_trim_precost_id='+cbo_trim_precost_id+'&txt_trim_group_id='+txt_trim_group_id+'&txt_update_dtls_id='+txt_update_dtls_id+'&cbo_colorsizesensitive='+cbo_colorsizesensitive+'&txt_req_quantity='+txt_req_quantity+'&txt_avg_price='+txt_avg_price+'&txt_country='+txt_country+'&txt_pre_des='+txt_pre_des+'&txt_pre_brand_sup='+txt_pre_brand_sup+"&cbo_level="+cbo_level+"&txtwoq="+txtwoq+"&txt_booking_no="+txt_booking_no;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1060px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function(){
			var cons_breck_down=this.contentDoc.getElementById("cons_breck_down");
			var woq=this.contentDoc.getElementById("woqty_sum");
			var rate=this.contentDoc.getElementById("rate_sum");
			var amount=this.contentDoc.getElementById("amount_sum");
			var json_data=this.contentDoc.getElementById("json_data");
			document.getElementById('consbreckdown_'+i).value=cons_breck_down.value;
			document.getElementById('txtwoq_'+i).value=woq.value;
			document.getElementById('txtrate_'+i).value=rate.value;
			document.getElementById('txtamount_'+i).value=amount.value;
			document.getElementById('jsondata_'+i).value=json_data.value;
			calculate_amount(i)
		}
	}
}

function set_cons_break_down(i){
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
	var cbo_level=document.getElementById('cbo_level').value*1;
	var cons_breack_down=return_global_ajax_value(garments_nature+"_"+cbo_company_name+"_"+txt_job_no+"_"+txt_po_id+"_"+cbo_trim_precost_id+"_"+txt_trim_group_id+"_"+txt_update_dtls_id+"_"+cbo_colorsizesensitive+"_"+txt_req_quantity+"_"+txt_avg_price+"_"+txt_country+"_"+txt_pre_des+"_"+txt_pre_brand_sup+"_"+cbo_level, 'set_cons_break_down', '', 'requires/short_trims_booking_multi_job_controllerurmi');
	cons_breack_down=cons_breack_down.split("**");
    document.getElementById('consbreckdown_'+i).value=cons_breack_down[0];
    document.getElementById('jsondata_'+i).value=cons_breack_down[1];
}

function openmypage_booking(page_link,title){
	var cbo_booking_month=document.getElementById('cbo_booking_month').value;
	var cbo_booking_year=document.getElementById('cbo_booking_year').value;
    var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	var page_link=page_link+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&company_id='+cbo_company_name+'&buyer_id='+cbo_buyer_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=970px,height=455px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function(){
		var theform=this.contentDoc.forms[0];
		var theemail=this.contentDoc.getElementById("txt_booking");
		if (theemail.value!=""){
			reset_form('trimsbooking_1','booking_list_view','id_approved_id','txt_booking_date,<? echo date("d-m-Y"); ?>','','cbo_currency*cbo_booking_year*cbo_booking_month*copy_val');
			document.getElementById('copy_val').checked=true;
			document.getElementById('booking_list_view').innerHTML='<font id="save_sms" style="color:#F00">Select new Item</font>';
			$("#cbo_currency").attr("disabled",true);
			get_php_form_data( theemail.value, "populate_data_from_search_popup_booking", "requires/short_trims_booking_multi_job_controllerurmi" );
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

/*function open_terms_condition_popup(page_link,title){
	var txt_booking_no=document.getElementById('txt_booking_no').value;
	if (txt_booking_no==""){
		alert("Save The Booking First")
		return;
	}
	else{
	    page_link=page_link+get_submitted_data_string('txt_booking_no','../../');
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=470px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
}*/

function fnc_trims_booking( operation ){
	freeze_window(operation);
	var data_all="";
	if (form_validation('cbo_company_name*cbo_buyer_name*cbo_supplier_name*txt_booking_date*cbo_source*txt_delivery_date*cbo_pay_mode','Company Name*Buyer Name*Supplier Name*Booking Date*Source*Delivery Date*Pay Mode')==false){
		release_freezing();
		return;
	}
    if(mandatory_field){
        if (form_validation(mandatory_field,field_message)==false)
        {
            release_freezing();
            return;
        }
    }

    if(document.getElementById('id_approved_id').value==1 || document.getElementById('id_approved_id').value==3)
    {
        alert("This booking is approved")
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
	data_all=data_all+get_submitted_data_string('txt_booking_no*cbo_booking_month*cbo_booking_year*cbo_company_name*cbo_buyer_name*cbo_supplier_name*cbo_currency*txt_booking_date*txt_delivery_date*cbo_pay_mode*cbo_source*txt_attention*txt_remarks*cbo_level*cbo_ready_to_approved*booking_mst_id*cbo_responsible_dept*cbo_responsible_person*txt_reason',"../../");

	var data="action=save_update_delete&operation="+operation+data_all+'&delete_cause='+delete_cause;
	http.open("POST","requires/short_trims_booking_multi_job_controllerurmi.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_trims_booking_reponse;
}

function fnc_trims_booking_reponse(){
	if(http.readyState == 4){
		var reponse=trim(http.responseText).split('**');
		if(trim(reponse[0])=='app1'){
			alert("This booking is approved")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='recv1'){
			alert("Receive Number Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='pi1'){
			alert("PI Number Found :"+number_format(trim(reponse[2]),2,'.','' )+"\n So Update/Delete Not Possible")
		    release_freezing();
		    return;
		}
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(trim(reponse[0]));
		if(trim(reponse[0])==0 || trim(reponse[0])==1){
            document.getElementById('txt_booking_no').value=reponse[1];
			document.getElementById('booking_mst_id').value=reponse[2];
			$("#cbo_company_name").attr("disabled",true);
			$("#cbo_supplier_name").attr("disabled",true);
			$("#cbo_buyer_name").attr("disabled",true);
			$("#cbo_level").attr("disabled",true);
			set_button_status(1, permission, 'fnc_trims_booking',1);
		}
		else if(trim(reponse[0])==2){
			location.reload();
		}
		release_freezing();
	}
}

function fnc_trims_booking_dtls( operation ){
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
	data_all=data_all+get_submitted_data_string('txt_booking_no*strdata*cbo_pay_mode*booking_mst_id',"../../");

	var row_num=$('#tbl_list_search tr').length;
	if(row_num <1){
		alert("Select Item");
		release_freezing();
		return;
	}
	for (var i=1; i<=row_num; i++){
		var txtrate=document.getElementById('txtrate_'+i).value
		if(txtrate=="" || txtrate==0){
			alert("Insert Rate")
			release_freezing();
			return;
		}
		var consbreckdown=document.getElementById('consbreckdown_'+i).value
		var txtbookingid=document.getElementById('txtbookingid_'+i).value
		if (consbreckdown=="" && txtbookingid==""){
			set_cons_break_down(i)
		}
		var consbreckdown=document.getElementById('consbreckdown_'+i).value
		if (trim(consbreckdown)=="" && operation ==0){
			alert("Unable to create Cons break down for minimum work order Qty, Data  not saved");
			release_freezing();
			$('#search'+i).css('background-color', 'red');
			return;
		}
		data_all=data_all+get_submitted_data_string('txtbookingid_'+i+'*txtpoid_'+i+'*txtcountry_'+i+'*txttrimcostid_'+i+'*txttrimgroup_'+i+'*txtuom_'+i+'*cbocolorsizesensitive_'+i+'*txtwoq_'+i+'*txtrate_'+i+'*txtamount_'+i+'*txtddate_'+i+'*consbreckdown_'+i+'*txtexchrate_'+i+'*txtjob_'+i+'*txtreqqnty_'+i+'*jsondata_'+i,"../../",i);
	}
	var cbo_level=document.getElementById('cbo_level').value;
	if(cbo_level==1){
		var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+row_num+data_all+'&delete_cause='+delete_cause;
	}
	if(cbo_level==2){
		var data="action=save_update_delete_dtls_job_level&operation="+operation+'&total_row='+row_num+data_all+'&delete_cause='+delete_cause;
	}
	http.open("POST","requires/short_trims_booking_multi_job_controllerurmi.php",true);
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
		if(trim(reponse[0])=='recv1'){
			alert("Receive Qty  :"+trim(reponse[3])+" Found in Receive Number "+ trim(reponse[2])+" \n So WOQ Less Then Receive Qty/Delete Not Possible")
		    release_freezing();
		    return;
		}
		if(trim(reponse[0])=='pi1'){
			alert("PI Qty  :"+number_format(trim(reponse[3]),2,'.','' )+"  Found in PI Number "+ trim(reponse[2])+" \n So WOQ Less Then PI Qty/Delete Not Possible")
		    release_freezing();
		    return;
		}
		if (reponse[0].length>2) reponse[0]=10;
		show_msg(trim(reponse[0]));
		if(trim(reponse[0])==0 || trim(reponse[0])==1 || trim(reponse[0])==2){
			var str="";
			if(trim(reponse[0])==0){
				str='Saved';
				$("#cbo_supplier_name").attr("disabled",true);
			}
			if(trim(reponse[0])==1){
				str='Updated';
				$("#cbo_supplier_name").attr("disabled",true);

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
	http.open("POST","requires/short_trims_booking_multi_job_controllerurmi.php",true);
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

function fnc_show_booking(wo_pre_cost_trim_id,po_id,booking_id){
	freeze_window(operation);
	var garments_nature=document.getElementById('garments_nature').value;
	var cbo_booking_month=document.getElementById('cbo_booking_month').value;
	var cbo_booking_year=document.getElementById('cbo_booking_year').value;
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbo_currency=document.getElementById('cbo_currency').value;
	var data=document.getElementById('txt_selected_po').value;
	var precost_id=document.getElementById('txt_selected_trim_id').value;
	var param=document.getElementById('txt_select_item').value;

	var cbo_level=document.getElementById('cbo_level').value;

	var data="action=show_trim_booking"+get_submitted_data_string('txt_booking_no',"../../")+'&cbo_company_name='+cbo_company_name+'&cbo_booking_month='+cbo_booking_month+'&cbo_booking_year='+cbo_booking_year+'&garments_nature='+garments_nature+'&data='+po_id+'&booking_id='+booking_id+'&pre_cost_id='+wo_pre_cost_trim_id+'&cbo_level='+cbo_level+'&cbo_currency='+cbo_currency;
	http.open("POST","requires/short_trims_booking_multi_job_controllerurmi.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_show_booking_reponse;
}

function fnc_show_booking_reponse(){
	if(http.readyState == 4){
        $("#cbo_currency").attr("disabled",true);
		document.getElementById('booking_list_view').innerHTML=http.responseText;
		set_all_onclick();
		release_freezing();
	}
}

function generate_trim_report(action,report_type){
	if (form_validation('txt_booking_no','Booking No')==false){
		return;
	}
	else{
		var show_comment='';
		var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
		if (r==true){
			show_comment="1";
		}
		else{
			show_comment="0";
		}
		$report_title=$( "div.form_caption" ).html();
        freeze_window();
		var data="action="+action+get_submitted_data_string('txt_booking_no*cbo_company_name*id_approved_id*cbo_level',"../../")+'&report_title='+$report_title+'&show_comment='+show_comment+'&report_type='+report_type;
		http.open("POST","requires/short_trims_booking_multi_job_controllerurmi.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;
	}
}

function generate_trim_report_reponse(){
	if(http.readyState == 4){
        release_freezing();
		var file_data=http.responseText.split("****");
		//$('#pdf_file_name').html(file_data[1]);
        var report_title=$( "div.form_caption" ).html();
		$('#data_panel').html(file_data[0]);
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/prt.css" type="text/css" /><title>'+report_title+'</title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}
//for print button

function print_report_button_setting(report_ids){
	var report_id=report_ids.split(",");
    $("#print_booking1").hide();
    $("#print_booking2").hide();
	for (var k=0; k<report_id.length; k++){
		if(report_id[k]==67){
			$("#print_booking1").show();
		}
        if(report_id[k]==19){
            $("#print_booking2").show();
        }
	}
}
set_multiselect('cbo_responsible_dept','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>