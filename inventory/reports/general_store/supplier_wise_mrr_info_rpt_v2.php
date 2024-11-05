<?
/*-------------------------------------------- Comments
Purpose			: 	This report will decscribe supplier wise mrr information

Functionality	:
JS Functions	:
Created by		:	Md Jakir Hosen
Creation date 	: 	17-04-2022
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

$user_id = $_SESSION['logic_erp']['user_id'];

$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Supplier Wise MRR Info Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


	function generate_report()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_supplier_name = $("#cbo_supplier_name").val();
        var cbo_item_category = $("#cbo_item_category").val();
        var cbo_item_group = $("#txt_item_group_id").val();
        var cbo_item_description = $("#txt_item_description_id").val();
        var txt_mrr_number = $("#txt_mrr_number").val();
        var txt_wo_number = $("#txt_wo_number").val();
        var txt_pi_number = $("#txt_pi_number").val();
		var txt_rcv_challan_number = $("#txt_rcv_challan_number").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
        var cbo_year = $('#cbo_year_selection').val();
		var report_title= "Supplier Wise Item Receive Status";

        if( cbo_supplier_name==0 && cbo_item_category=="")
		{
            $("#cbo_supplier_name").focus();
			return;
		}
		
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_supplier_name="+cbo_supplier_name+"&cbo_item_category="+cbo_item_category+"&txt_mrr_number="+txt_mrr_number+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_year="+cbo_year+"&report_title="+report_title+"&cbo_item_group="+cbo_item_group+"&cbo_item_description="+cbo_item_description+"&txt_wo_number="+txt_wo_number+"&txt_pi_number="+txt_pi_number+"&txt_rcv_challan_number="+txt_rcv_challan_number;
		var data="action=generate_report"+dataString;

		freeze_window(operation);
		http.open("POST","requires/supplier_wise_mrr_info_rpt_v2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			setFilterGrid('mrr_details_tbl_body',-1);
			release_freezing();
		}
	}

	function new_window()
	{
			$(".flt").hide();
			// document.getElementById('scroll_body').style.overflow="auto";
			// document.getElementById('scroll_body').style.maxHeight="none";
			document.getElementById('democlass').removeAttribute("style");
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close();
			$(".flt").show();
			release_freezing();	
			// document.getElementById('scroll_body').style.overflow="auto";
			// document.getElementById('scroll_body').style.maxHeight="250px";
			document.getElementById('democlass').removeAttribute("style");
	}

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}

function openmypage_item()
{
    if( form_validation('cbo_company_name','Company Name')==false )
    {
        return;
    }
    var company = $("#cbo_company_name").val();
    var cbo_item_category = $("#cbo_item_category").val();
    var page_link='requires/supplier_wise_mrr_info_rpt_v2_controller.php?action=group_popup&company='+company+'&cbo_item_category='+cbo_item_category;
    var title="Search Group Popup";
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=365px,height=370px,center=1,resize=0,scrolling=0','../')
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var item_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
        var item_name=this.contentDoc.getElementById("txt_selected").value; // product Description
        //var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
        //alert(style_des_no);
        $("#txt_item_group").val(item_name);
        $("#txt_item_group_id").val(item_id);
        //$("#txt_order_id_no").val(style_des_no);
    }
}

function openmypage_description()
{
    if( form_validation('cbo_company_name','Company Name')==false )
    {
        return;
    }
    var company = $("#cbo_company_name").val();
    var cbo_item_category = $("#cbo_item_category").val();
    var cbo_item_group = $("#txt_item_group_id").val();
    var page_link='requires/supplier_wise_mrr_info_rpt_v2_controller.php?action=item_description_popup&company='+company+'&cbo_item_group='+cbo_item_group+'&cbo_item_category='+cbo_item_category;
    var title="Search Item Description Popup";
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=465px,height=370px,center=1,resize=0,scrolling=0','../')
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var item_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
        var item_name=this.contentDoc.getElementById("txt_selected").value; // product Description
        //var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
        //alert(style_des_no);
        $("#txt_item_description").val(item_name);
        $("#txt_item_description_id").val(item_id);
        //$("#txt_order_id_no").val(style_des_no);
    }
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission);  ?>
    <form name="item_inquiry_report" id="item_inquiry_report" autocomplete="off" >
    <h3 style="width:1340px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel" style="width:100%;" align="center">
        <fieldset style="width:950px;">
			<table class="rpt_table" width="1340" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th rowspan="2" width="130" class="must_entry_caption">Company</th>
                        <th rowspan="2" width="130" class="must_entry_caption">Supplier</th>
                        <th rowspan="2" width="110" class="must_entry_caption">Item Category</th>
                        <th rowspan="2" width="110">Item Group</th>
                        <th rowspan="2" width="110">Item Description</th>
                        <th rowspan="2" width="90">Receive No</th>
                        <th rowspan="2" width="90">Wo No</th>
                        <th rowspan="2" width="90">PI No</th>
                        <th rowspan="2" width="100">Receive Challan No</th>
                        <th colspan="2">Receive Date</th>
                        <th rowspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('item_inquiry_report','report_container*report_container2','','','')" /></th>
                    </tr>
                    <tr>
                        <th width="80" class="must_entry_caption">From Date</th>
                        <th width="80" class="must_entry_caption">To Date</th>
                    </tr>
                </thead>
                <tr class="general" align="center">
                    <td>
                            <?
                               echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/supplier_wise_mrr_info_rpt_v2_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );" );
                            ?>
                    </td>
                    <td align="center" id="supplier_td">
                        <?
                            echo create_drop_down( "cbo_supplier_name", 130, $blank_array,"", 1, "--Select Supplier--", "", "" );
                        ?>
                    </td>
                    <td align="center">
                        <?php
                        echo create_drop_down( "cbo_item_category", 110, $item_category,"", 0, "-- Select Item --", $selected, "",0, "" );
                        ?>
<!--                        <input style="width:110px;"  name="txt_item_category" id="txt_item_category" class="text_boxes" placeholder="Write Item Category"/>-->
                    </td>
                    <td align="center">
                        <input style="width:110px;"  name="txt_item_group" id="txt_item_group" ondblclick="openmypage_item()" class="text_boxes" placeholder="Browse"/>
                        <input type="hidden" name="txt_item_group_id" id="txt_item_group_id" class="text_boxes" />
                    </td>
                    <td align="center">
                        <input style="width:110px;"  name="txt_item_description" ondblclick="openmypage_description()" id="txt_item_description" class="text_boxes" placeholder="Browse"/>
                        <input type="hidden" name="txt_item_description_id" id="txt_item_description_id" class="text_boxes" />
                    </td>

                    <td align="center">
                        <input style="width:90px;"  name="txt_mrr_number" id="txt_mrr_number" class="text_boxes" placeholder="Write Receive No"/>
                    </td>
                    <td align="center">
                        <input style="width:90px;"  name="txt_wo_number" id="txt_wo_number" class="text_boxes" placeholder="Write Wo No"/>
                    </td>
                    <td align="center">
                        <input style="width:90px;"  name="txt_pi_number" id="txt_pi_number" class="text_boxes" placeholder="Write PI No"/>
                    </td>
                    <td align="center">
                        <input style="width:100px;"  name="txt_rcv_challan_number" id="txt_rcv_challan_number" class="text_boxes" placeholder="Write Receive Challan No"/>
                    </td>
                     <td align="center">
                        <input  style="width:80px;" type="text" name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From Date"  readonly />

                    </td>
                    <td align="center">
                        <input style="width:80px;" type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" readonly />

                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:80px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td colspan="12" align="center"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </table>
        </fieldset>
    </div>
    <br />

        <!-- Result Contain Start-------------------------------------------------------------------->

        	<div id="report_container" align="center" style="width: 1340px;"></div>
            <div id="report_container2"></div>

        <!-- Result Contain END-------------------------------------------------------------------->


    </form>
</div>
</body>
<script>set_multiselect('cbo_item_category','0','0','','0');</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
