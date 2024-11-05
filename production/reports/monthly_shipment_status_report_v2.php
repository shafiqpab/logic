<?

/* -------------------------------------------- Comments -----------------------
  Purpose			: 	This Form Will Create Monthly Shipment Status Report V2.
  Functionality	:
  JS Functions	:
  Created by		:	Md Mamun Ahmed Sagor
  Creation date 	: 	28-12-2022
  Updated by 		:
  Update date		:
  QC Performed BY	:
  QC Date			:
  Comments		    :
  CRM               :29740
 */

session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Monthly Shipment Status Report V2", "../../", 1, 1, $unicode, 1, '');
?>	

<script>

    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";
    var permission = '<? echo $permission; ?>';

	var tableFilters4 = 
	{
		col_operation:{
			id:["totDtlsPoQty","totDtlsExQty","value_totDtlsExVal","totDtlsEarly","totDtlsOntime","totDtlsDelay","totDtlsShipQty"],
			col:[8,11,12,13,14,15,16],
			operation:["sum","sum","sum","sum","sum","sum","sum"],
			write_method:["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 


    function fn_report_generated(operation)
    {
        if (form_validation('txt_date_from*txt_date_to', 'Start Date*End Date') == false)
        {
            return;
        } 
        else
        {
            if(operation==5 || operation==6)
            {
                var cbo_type=document.getElementById('cbo_type').value;
                if (cbo_type.length==0 || cbo_type!=5)
                {
                    alert('Select Actual Po Shipment date');
                    return;
                } 
            }
            var report_title=$( "div.form_caption" ).html();
            var from_date = $('#txt_date_from').val();
            var to_date = $('#txt_date_to').val();
            var datediff = date_diff('d', from_date, to_date) + 1;
            var report_title = $("div.form_caption").html();
            var data = "action=report_generate" + get_submitted_data_string('cbo_company_id*cbo_location*cbo_buyer_name*txt_date_from*txt_date_to*cbo_type*cbo_dealing_merchant*cbo_prod_category', "../../") + '&report_title=' + report_title + '&datediff=' + datediff + '&report_type=' + operation;
            freeze_window(3);
            http.open("POST", "requires/monthly_shipment_status_report_controller_v2.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fn_report_generated_reponse;
        }
    }

    function fn_report_generated_reponse()
    {
        if (http.readyState == 4)
        {
            //alert (http.responseText);
            var reponse = trim(http.responseText).split("####");
            $("#report_container2").html(reponse[0]);
            document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			if(reponse[2]==4){
				setFilterGrid("table_body4",-1,tableFilters4);
			}

            show_msg('3');
            release_freezing();
        }
    }

    function new_window(str)
    {
        if(str==4){$("#table_body4 tr:first").hide();}
        if(str!=5 && str !=6 )
        {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";
        }
        document.getElementById('scroll_body2').style.overflow = "auto";
        document.getElementById('scroll_body2').style.maxHeight = "none";
        
        
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
        d.close();
        
        if(str!=5 && str !=6 )
        {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "350px";
        }
        document.getElementById('scroll_body2').style.overflow = "auto";
        document.getElementById('scroll_body2').style.maxHeight = "350px";
		if(str==4){$("#table_body4 tr:first").show();}
    }

    function change_color(v_id, e_color)
    {
        if (document.getElementById(v_id).bgColor == "#33CC00")
        {
            document.getElementById(v_id).bgColor = e_color;
        } else
        {
            document.getElementById(v_id).bgColor = "#33CC00";
        }
    }
    function openmypage_ex_popup(company,location,buyer,color_size,date_from,date_to,return_qnty)
    {
	   var cbo_type = $('#cbo_type').val();
		
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_shipment_status_report_controller_v2.php?company='+company+'&action=ex_qnty_popup&location='+location+'&buyer='+buyer+'&color_size='+color_size+'&date_from='+date_from+'&date_to='+date_to+'&return_qnty='+return_qnty+'&cbo_type='+cbo_type, 'Detail Veiw', 'width=700px, height=450px,center=1,resize=1,scrolling=1','../');
    }
    function openmypage_actual_popup(company,client,buyer,type,dtls_id,action = '')
    {
        if(action == '') action = 'actual_po_poup';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_shipment_status_report_controller_v2.php?company='+company+'&action='+action+'&client='+client+'&buyer='+buyer+'&dtls_id='+dtls_id+'&types='+type, (type.replace("_"," ")).toUpperCase(), 'width=770px, height=450px,center=1,resize=1,scrolling=1','../');
    }
	
    function myPopup(po_id,date,type,action)
    {
		
		var page_link='requires/monthly_shipment_status_report_controller_v2.php?po_id='+po_id+'&date='+date+'&cbo_type='+type+'&action='+action;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Early Qty Dtls', 'width=600px,height=400px,center=1,resize=1,scrolling=1','../');
		emailwindow.onclose=function()
		{
			//var theform=this.contentDoc.forms[0];
			//var hdn_head_id=this.contentDoc.getElementById("hdn_head_id").value;
			//var hdn_head_val=this.contentDoc.getElementById("hdn_head_val").value;
		}
		
		
		
	}
	
	
	
	

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs("../../", $permission); ?><br />    		 
        <form name="unitwiseproduction_1" id="unitwiseproduction_1" autocomplete="off" > 
            <h3 style="width:1110px; margin-top:5px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id, 'content_search_panel', '')"> -Search Panel</h3> 
            <div id="content_search_panel" style="width:1110px" align="center" >      
                <fieldset>  
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
                        <thead>                    
                        <th width="150" class="">Delivery Company</th>
                        <th width="150" class="">Location</th>
                        <th width="120" class="">Buyer</th>
                        <th width="180" class="">Dealing Merchant</th>
                        <th width="120">Type</th>
                        <th width="120">Product Category</th>
                        <th width="130" colspan="2" class="must_entry_caption">Ex-Factory Date Range</th>
                        <th>
                            <input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('unitwiseproduction_1', 'report_container*report_container2', '', '', '')" />
                        </th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><? echo create_drop_down("cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/monthly_shipment_status_report_controller_v2', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/monthly_shipment_status_report_controller_v2', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?> </td>
                                <td id="location_td"><? echo create_drop_down("cbo_location", 150, $blank_array, "", 1, "-- Select --", $selected, "", 1, ""); ?></td>
                                <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-Select Buyer-", $selected, "",1,"" ); ?></td>
                                
                                <td> 
                            	<? 
							  		echo create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );

								?>	
                           		</td>
                                
                                <td id="type_td"><? echo create_drop_down( "cbo_type", 120, array(1=>'Shipment Date wise',2=>'Country Ship Date wise',3=>'Originial Shipment Date',4=>'Publish Shipment Date',5=>'Actual Po Shipment Date'),"", "", "", 2, "",0,"" ); //?></td>
                                <td ><?=create_drop_down( "cbo_prod_category", 120, $product_category,"", 1, "Select Category", "", "","","" ); ?>
                            
                            </td>
                                <td><input name="txt_date_from" value="<?=date("d-m-Y", strtotime(date('Y-m-1')))?>" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" readonly></td>
                                <td><input name="txt_date_to" value="<?=date( 't-m-Y' );?>" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  readonly></td>
                                <td align="center">
                                    <input type="button" id="show_button" class="formbutton" style="width:85px" value="Category Wise" onClick="fn_report_generated(1)" />
                                </td>
                            </tr>
                        </tbody>
                        <tr>
                            <td colspan="8" align="center"><?=load_month_buttons(1); ?></td>
                        </tr>
                    </table> 
                </fieldset>
            </div>
            <div id="report_container" align="center"></div>
            <div id="report_container2" align="left"></div>
        </form> 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
