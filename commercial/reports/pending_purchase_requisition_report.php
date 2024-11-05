<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Pending Purchase Requisition Report.
Functionality	:
JS Functions	:
Created by		:	Nayem
Creation date 	: 	01-11-2021
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
echo load_html_head_contents("Pending Purchase Requisition Report", "../../", 1, 1,'',1,1);
?>

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	function openmypage_group()
	{
		var category=document.getElementById('cbo_category_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/pending_purchase_requisition_report_controller.php?category='+category+'&action=item_group_popup','Search Item Group', 'width=950px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var data=this.contentDoc.getElementById("item_id").value.split('_');
			$("#txt_item_group_id").val(data[0]);
			$("#txt_item_group").val(data[1]);
		}
	}

	function generate_report(type)
	{
		var report_title=$("div.form_caption" ).html();

		if(form_validation("txt_date_from*txt_date_to","Date From*Date To")==false){
			return;
		}
	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_category_id*txt_item_group*txt_item_group_id*txt_item_code*txt_description*txt_requisition_no*cbo_req_status*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title+'&type='+type;

		freeze_window(3);
		http.open("POST","requires/pending_purchase_requisition_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
		$("#table_body tr:first").show();

	}	

</script>

</head>

<body onLoad="set_hotkey();">
<form id="pendingPurchaseStatusReport">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1220px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:1220px;">
                <table class="rpt_table" width="1200" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>
                            <th >Company</th>
                            <th>Category</th>
                            <th>Item Group</th>
                            <th>Item Code</th>
                            <th>Item Des.</th>
                            <th>Req. No</th>
                            <th>Req. Status</th>
                            <th class="must_entry_caption">Req. Date Range</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" onClick="reset_form('pendingPurchaseStatusReport','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <?
                                    echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                ?>
                            </td>
                            <td align="center">
								<?
									echo create_drop_down( "cbo_category_id", 130, $item_category,"", 1, "-- Select Category --", $selected, "",0,"","","","1,2,3,12,13,14,24,25,35" );
								?>  
                        	</td>
							<td align="center">
								<input style="width:110px;" name="txt_item_group" id="txt_item_group" class="text_boxes" ondblclick="openmypage_group()" placeholder="Browse"/>   
								<input type="hidden" name="txt_item_group_id" id="txt_item_group_id"/> 
							</td>
							<td align="center">
								<input style="width:110px;" name="txt_item_code" id="txt_item_code" class="text_boxes" placeholder="Write" />
							</td>
							<td align="center">
								<input style="width:130px;" name="txt_description" id="txt_description"  class="text_boxes" placeholder="Write" />   
								<!-- <input type="hidden" name="txt_product_id" id="txt_product_id"/>  -->
							</td>
							<td align="center">
							    <input style="width:110px;" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" placeholder="Write"/>
                        	</td>  
							<td align="center">
								<?
									$req_status=array(1=>"WO Full Pending",2=>"WO Partial Pending",3=>"WO Full & Partial Pending",4=>"WO Done",5=>"All Req");
									echo create_drop_down( "cbo_req_status", 130, $req_status,"", 1, "-- Select --", $selected, "",0,"" );
								?>  
                        	</td>                      	
                            <td align="center" width="180">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:65px" placeholder="From Date"/>&nbsp;To&nbsp;<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:65px" placeholder="To Date"/>
                        	</td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:90px;" class="formbutton" />                             
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="9" align="center"><? echo load_month_buttons(1); ?></td>                        	
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div style="margin-top:10px" id="report_container" align="center"></div>
    <div id="report_container2"></div>
</form>
</body>
<script>set_multiselect('cbo_company_id','0','0','','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
