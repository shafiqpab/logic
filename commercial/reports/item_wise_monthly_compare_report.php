<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Closing Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	Tipu
Creation date 	: 	06-06-2018
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: New Report
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Closing Stock Report","../../", 1, 1, $unicode,1,''); 
//var_dump($item_category);
$user_id=$_SESSION['logic_erp']['user_id'];
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	var tableFilters = 
	{
		col_40: "none",
		col_operation: {
		id: ["value_prev_tot_amount","value_cu_tot_amount","value_variance_amount"],
		col: [8,11,12],
		operation: ["sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML"]
		}
	}

	function disappear_list(fld,close_fnc)
	{
		if(!close_fnc) var close_fnc=0;
		 if(close_fnc!=0) 
		 {
			 close_fnc=close_fnc.split('__');
		  if (close_fnc[0]=="") close_fnc[0]=fld.value;
		 	get_php_form_data( close_fnc[0], close_fnc[1], close_fnc[2] );
		 }
	 	document.getElementById('txt_item_group').value = ''; 
		$("#multi_select_"+fld.id).hide("slow");
	}
	
	function generate_report(operation)
	{
		if( form_validation('cbo_company_name*cbo_month*cbo_month_end','Company Name*Start Month*End Month')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var item_group_id = $("#txt_item_group_id").val();
		var item_account_id = $("#txt_item_acc").val();
		var cbo_year_name = $("#cbo_year_name").val();
		var cbo_month = $("#cbo_month").val();
		var cbo_month_end = $("#cbo_month_end").val();
		var cbo_end_year_name = $("#cbo_end_year_name").val();

		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_item_category_id="+cbo_item_category_id+"&cbo_year_name="+cbo_year_name+"&cbo_month="+cbo_month+"&cbo_month_end="+cbo_month_end+"&cbo_end_year_name="+cbo_end_year_name+"&item_account_id="+item_account_id+"&item_group_id="+item_group_id+"&report_title="+report_title+"&report_type="+operation;
		var data="action=generate_report"+dataString;
		//alert (data);
		freeze_window(operation);
		http.open("POST","requires/item_wise_monthly_compare_report_controller.php",true);
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
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
				setFilterGrid("table_body_id",-1,tableFilters);
			
			show_msg('3');
			release_freezing();
		}
	}	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
	
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
        $('#scroll_body tr:first').show();
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
	
	function openmypage_itemgroup()
	{
		if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var txt_item_group = $("#txt_item_group").val();
		var txt_item_group_id = $("#txt_item_group_id").val();
		var txt_item_group_no = $("#txt_item_group_no").val();
		var page_link='requires/item_wise_monthly_compare_report_controller.php?action=item_group_such_popup&company='+company+'&cbo_item_category_id='+cbo_item_category_id+'&txt_item_group='+txt_item_group+'&txt_item_group_id='+txt_item_group_id+'&txt_item_group_no='+txt_item_group_no;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var item_group_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var item_group_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var item_group_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_item_group").val(item_group_des);
			$("#txt_item_group_id").val(item_group_id); 
			$("#txt_item_group_no").val(item_group_no);
			$("#txt_item_acc").val('');
		}
	}
	
	function openmypage_itemaccount()
	{
		if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var txt_item_group_id = $("#txt_item_group_id").val();
		var txt_item_acc = $("#txt_item_acc").val();
		var txt_item_account_id = $("#txt_item_account_id").val();
		var txt_item_acc_no = $("#txt_item_acc_no").val();
		var page_link='requires/item_wise_monthly_compare_report_controller.php?action=item_account_such_popup&company='+company+'&cbo_item_category_id='+cbo_item_category_id+'&txt_item_group_id='+txt_item_group_id+'&txt_item_acc='+txt_item_acc+'&txt_item_account_id='+txt_item_account_id+'&txt_item_acc_no='+txt_item_acc_no;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var item_acc_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var item_acc_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var item_acc_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_item_acc").val(item_acc_des);
			$("#txt_item_account_id").val(item_acc_id); 
			$("#txt_item_acc_no").val(item_acc_no);
		}
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
        <form name="closingstock_1" id="closingstock_1" autocomplete="off" > 
         <h3 style="width:980px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:980px" >      
            <fieldset>  
                <table class="rpt_table" width="980" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="130" class="must_entry_caption">Company</th>
                        <th width="180">Item Category</th>
                        <th width="90">Item Group</th>
                        <th width="90">Item Desc</th>
                        <th width="120">Start Year</th>
                        <th width="120">Start Month</th>
                        <th width="120">End Year</th>
                        <th width="120">End Month</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:50px" class="formbutton" onClick="reset_form('closingstock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center">
                                <? 
                                    echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and comp.core_business in(1,3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/item_wise_monthly_compare_report_controller', this.value, 'load_drop_down__', 'store_td' );" );
                                ?>                            
                            </td>
                            <td align="center">
                            <?
                            $userCredential = sql_select("SELECT store_location_id, item_cate_id FROM user_passwd where id=$user_id");
                            $item_cat_cond = ($userCredential[0][csf("item_cate_id")]) ? $userCredential[0][csf("item_cate_id")] : "" ;
                            echo create_drop_down( "cbo_item_category_id", 180, $item_category,"", 0, "", 0, "", 0,"$item_cat_cond", "", "", "1,2,3,4,12,13,14,24,25,3143,71,72,73,74,75,76,77,78,79");
                            ?>                            
                            </td>

                            <td align="center">
                            <input style="width:90px;"  name="txt_item_group" id="txt_item_group" onDblClick="openmypage_itemgroup()" class="text_boxes" placeholder="Browse"/>   
                            <input type="hidden" name="txt_item_group_id" id="txt_item_group_id"/>    <input type="hidden" name="txt_item_group_no" id="txt_item_group_no"/>
                            </td>
                            <td align="center">
                            <input style="width:90px;"  name="txt_item_acc" id="txt_item_acc" onDblClick="openmypage_itemaccount()" class="text_boxes" placeholder="Browse"/>   
                            <input type="hidden" name="txt_item_account_id" id="txt_item_account_id"/>    <input type="hidden" name="txt_item_acc_no" id="txt_item_acc_no"/>
                            </td>
                            <td align="center">
                                <? 
                                echo create_drop_down( "cbo_year_name", 100,$year,"id,year", 1, "-- Select Year --", date('Y'),"" );
                                ?>
                            </td>
                            <td align="center">
                                <?
                                echo create_drop_down( "cbo_month", 100,$months,"", 1, "-- Select --", "","" );
                                ?>
                            </td>
                            <td align="center">
                                <? 
                                echo create_drop_down( "cbo_end_year_name", 100,$year,"id,year", 1, "-- Select Year --", date('Y'),"" );
                                ?>
                            </td>
                            <td align="center">
                                <?
                                echo create_drop_down( "cbo_month_end", 100,$months,"", 1, "-- Select --", "","" );
                                ?>
                            </td>
                            <td align="center">
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:50px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                </table> 
            </fieldset> 
            </div>
            <br /> 
                <div id="report_container" align="center"></div>
                <div id="report_container2"></div> 
        </form>    
    </div>
</body> 
<script>
	set_multiselect('cbo_item_category_id','0','0','0','0');
	//function set_multiselect( fld_id, max_selection, is_update, update_values, on_close_fnc_param )
</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
