<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create General Item Ledger
				
Functionality	:	
JS Functions	:
Created by		:	Md Jakir Hosen
Creation date 	: 	22-08-2022
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
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id,item_cate_id FROM user_passwd where id=$user_id");
$store_location_id = $userCredential[0][csf('store_location_id')];
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("General Item Issue Report","../../../", 1, 1, $unicode,1,1);

?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters = 
	{
		col_operation: {
		id: ["prod_total","value_prod_bal_qty"],
		col: [16,18],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"]
		}
	}

	function generate_report(operation)
	{
		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
		{
			return;
		} 
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_department = $("#cbo_department").val();
		var cbo_section = $("#cbo_section").val();
		var cbo_floor = $("#cbo_floor").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();	
		var report_title=$( "div.form_caption" ).html();
        var cbo_item_category = $("#cbo_item_category").val();
		var item_group_id = $("#txt_item_group_id").val();
		var item_account_id = $("#txt_item_account_id").val();
		
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_item_category="+cbo_item_category+"&cbo_department="+cbo_department+"&cbo_section="+cbo_section+"&item_account_id="+item_account_id+"&item_group_id="+item_group_id+"&from_date="+from_date+"&to_date="+to_date+"&report_title="+report_title+"&cbo_floor="+cbo_floor;
		var data="action=generate_report"+dataString;
		freeze_window(operation);
		http.open("POST","requires/general_item_issue_report_controller.php",true);
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
            setFilterGrid("table_body_id",-1,tableFilters);
            show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		 
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
            $("#table_body_id tr:first").hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close(); 
			document.getElementById('scroll_body').style.overflow="auto"; 
			document.getElementById('scroll_body').style.maxHeight="250px";
            $("#table_body_id tr:first").show();

    }

	function openmypage_item_group()
	{
		
		if( form_validation('cbo_company_name*cbo_item_category','Company Name*Item Category')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var cbo_item_category = $("#cbo_item_category").val();
		var page_link='requires/general_item_issue_report_controller.php?action=item_group_such_popup&company='+company+'&cbo_item_category_id='+cbo_item_category;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var item_group_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var item_group_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			//alert(style_no);
			$("#txt_item_group").val(item_group_des);
			$("#txt_item_group_id").val(item_group_id); 
		}
	}

    function openmypage_item_account()
	{		
		if( form_validation('cbo_company_name*cbo_item_category*txt_item_group','Company Name*Item Category*Item Group')==false )
		{
			return;
		}
		
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category').value+"_"+document.getElementById('txt_item_group_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/general_item_issue_report_controller.php?action=item_account_popup&data='+data,'Item Account Popup', 'width=800px,height=480px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("item_account_id");
			var theemailv=this.contentDoc.getElementById("item_account_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_item_account_id").value=response[0];
			    document.getElementById("txt_item_acc").value=theemailv.value;
				//reset_form();
				release_freezing();
			}
		}
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission);  ?>   		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" > 
    <h3 style="width:1100px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel" style="width:100%;" align="center">
        <fieldset style="width:1100px;">
			<table class="rpt_table" width="1100" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th width="130" class="must_entry_caption">Company</th>
                        <th width="120">Department</th>
                        <th width="110" >Section</th>
                        <th width="110">Floor</th>
                        <th width="135">Categories</th>
                        <th width="100">Item Group</th>
                        <th width="100">Item Name</th>
                        <th width="170" class="must_entry_caption">Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>
                </thead>
                <tr class="general" align="center">
                    <td>
                        <?
                        echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/general_item_issue_report_controller', this.value, 'load_drop_down_department', 'department_td' );load_drop_down( 'requires/general_item_issue_report_controller', this.value , 'load_drop_down_floor', 'floor_td' );" );
                        ?>
                    </td>
                    <td align="center" id="department_td">
                        <?
                        echo create_drop_down( "cbo_department", 120, $blank_array,"", 1, "-- Select --", "", "" );
                        ?>
                    </td>
                    <td align="center" id="section_td">
                        <?
                        echo create_drop_down( "cbo_section", 110, $blank_array,"", 1, "-- Select --", "", "" );
                        ?>
                    </td>
                    <td align="center" id="floor_td">
                        <?
                        echo create_drop_down( "cbo_floor", 110, $blank_array,"", 1, "-- Select --", "", "" );
                        ?>
                    </td>
                    <td align="center">
                        <?
                        echo create_drop_down( "cbo_item_category", 135, $general_item_category,"", 0, "-- Select Item --", $selected, "",0,"" );
                        ?>
                    </td>
                    <td>
                        <input style="width:90px;" name="txt_item_group" id="txt_item_group" class="text_boxes" onDblClick="openmypage_item_group()" placeholder="Browse" readonly />
                        <input type="hidden" name="txt_item_group_id" id="txt_item_group_id" style="width:90px;"/>  
                    </td>
                    <td>
                        <input style="width:90px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
                        <input type="hidden" name="txt_item_account_id" id="txt_item_account_id" style="width:90px;"/>
                    </td>
                    <td align="center">
                         <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:65px"/>
                         To
                         <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:65px"/>
                    </td>
                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7" align="center"><? echo load_month_buttons(1);  ?></td>
                </tr>
            </table> 
        </fieldset> 
    </div>
    
        <!-- Result Contain Start-->
        
        	<div id="report_container" align="center" style="margin: 6px;"></div>
            <div id="report_container2"></div> 
        
        <!-- Result Contain END-->
    
    
    </form>    
</div>    
</body>
<script>
    set_multiselect('cbo_item_category','0','0','0','0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
