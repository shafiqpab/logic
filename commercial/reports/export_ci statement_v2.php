<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Export CI Statement V2.
Functionality	:
JS Functions	:
Created by		:	Wayasel Ahmmed
Creation date 	: 	15-10-2023
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
echo load_html_head_contents("Export CI Statement V2", "../../", 1, 1,'','','');
?>

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';


	function generate_report(RptType)
	{
		var cbo_based_on=$('#cbo_based_on').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var cbo_lien_bank=$('#cbo_lien_bank').val();
		var cbo_location=$('#cbo_location').val();
		var txt_invoice_no=$('#txt_invoice_no').val();
		var txt_lc_sc_no=$('#txt_lc_sc_no').val();
		var cbo_ascending_by=$('#cbo_ascending_by').val();
		var txt_int_ref_no=$('#txt_int_ref_no').val();
        if(form_validation('cbo_company_name','Company Name')==false)
        {
            return;
        }
        
        if(txt_int_ref_no=="" && txt_lc_sc_no=="" && txt_invoice_no==""){
            // alert(txt_invoice_no);return;
            if(form_validation('txt_date_from*txt_date_to','Form Date*To Date')==false)
            {
                return;
            }
        }

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_buyer_name*cbo_lien_bank*cbo_location*shipping_mode*cbo_based_on*txt_date_from*txt_date_to*txt_invoice_no*txt_lc_sc_no*cbo_ascending_by*txt_int_ref_no*cbo_search_by","../../")+'&report_title='+report_title+'&RptType='+RptType;
		// alert(data);return;
		freeze_window(3);
		http.open("POST","requires/export_ci_statement_v2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("****");
			//alert(http.responseText);//return;
			$('#report_container2').html(response[0]);
			release_freezing();
			//alert(response[1]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[2]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			release_freezing();
				// return;			
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$(".flt").css("display","none");

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="400px";
        $(".flt").css("display","block");
	}
	
	function openmypage_invoce_no(){
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var cbo_company_name=$('#cbo_company_name').val();
		page_link='requires/export_ci_statement_v2_controller.php?action=invoice_popup_search'+'&cbo_company_name='+cbo_company_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Invoice No PopUp', 'width=900px,height=350px,center=1,resize=0,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var txt_invoice_no=this.contentDoc.getElementById("txt_invoice_no").value;
			var cbo_buyer_name=this.contentDoc.getElementById("cbo_buyer_name").value;

			document.getElementById('txt_invoice_no').value = txt_invoice_no;
			document.getElementById('cbo_buyer_name').value = cbo_buyer_name;

			$('#cbo_company_name').attr('disabled','disabled');
			$('#cbo_buyer_name').attr('disabled','disabled');

		}
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <form id="frm_lc_salse_contact" name="frm_lc_salse_contact">
    <div style="width:1400px;">
    <h3 align="left" id="accordion_h1" style="width:1400px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel">
    <fieldset style="width:1400px;">
        <table class="rpt_table" cellspacing="0" cellpadding="0" width="1400" border="1" rules="all">
            <thead>
                <th width="130" class="must_entry_caption">Company</th>
                <th width="130">Buyer</th>
                <th width="130">Lien Bank</th>
                <th width="130">Location</th>
                <th width="100"> Ship Mode </th>
                <th width="70">Invoice No</th>
                <th width="70">Int. Ref. No</th>
                <th width="80">LC/SC No</th>
                <th width="120">Based On</th>
                <th width="140" class="must_entry_caption">Date Range</th>
                <th width="80">Ascending By</th>                            
                <th width="80">Search By</th>                            
                <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('frm_lc_salse_contact','report_container*report_container2','','','')" /></th>
            </thead>
            <tbody>
                <tr> 
                    <td>
                    <?                 
                        echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/export_ci_statement_v2_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                    ?>
                    </td>
                    <td id="buyer_td"><?
                        echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                    ?></td>
                    <td>
                    <?
                        echo create_drop_down( "cbo_lien_bank", 130, "select (bank_name||' ('||branch_name||')') as bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- All Lien Bank --", 0, "" );
                    ?>
                    </td>
                    <td>
                    <?
                        echo create_drop_down( "cbo_location", 130, "select id,location_name from lib_location","id,location_name", 1, "-- Select Location --", $selected,"",0,"" );
                    ?>
                    </td>         
                    <td>
						<?
                            echo create_drop_down( "shipping_mode", 100, $shipment_mode,"", 1, "-- Select --", 0, "" );
                        ?>
                    </td>
                    <td>
                    	<input ondblclick="openmypage_invoce_no();" type="text" id="txt_invoice_no" name="txt_invoice_no" class="text_boxes" style="width:70px;" placeholder="Browse/Write" >
                    </td>
                    <td>
                    	<input type="text" id="txt_int_ref_no" name="txt_int_ref_no" class="text_boxes" style="width:70px;" placeholder="Write" >
                    </td>
                    <td>                   
                      <input type="text" id="txt_lc_sc_no" name="txt_lc_sc_no" class="text_boxes" style="width:70px;" onFocus="lc_sc_no_auto_com()" placeholder="Write" >
                    </td>
                    <td>
                    <?
                    $based_on_arr=array(1=>"Invoice Insert Date",2=>"Invoice C. Date");
                        echo create_drop_down( "cbo_based_on", 120, $based_on_arr,"", 1, "------ Select ------", 1,"","","","",1);
                    ?>
                    </td>
                    <td>
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:55px" placeholder="From Date" readonly>
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"placeholder="To Date" readonly>
                    </td>
					<td><? 
						$ascending_by_array = array(0=> "--Select--", 1=>"Invoice No", 2=>"Invoice Date");
						echo create_drop_down("cbo_ascending_by", 80, $ascending_by_array, "", 0, "----- Select -----",0); ?>
                    </td>
                    <td><?  
						$search_by_array = array(0=> "--Select--", 1=>"Used Invoice No", 2=>"Unused Invoice No");
						echo create_drop_down("cbo_search_by", 80, $search_by_array, "", 0, "----- Select -----",0); ?>
                    </td>

                    <td align="center">
                      <input type="button" name="search_1" id="search_1" value="Details" onClick="generate_report(1)" style="width:60px" class="formbutton" />                                   
                    </td>
                </tr>
                <tr>
					<td></td>
                    <td colspan="11" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </tbody>
        </table>
    </fieldset>
    </div>
    </div>
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2"></div>
    </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
