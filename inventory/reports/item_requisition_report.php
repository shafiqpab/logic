<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Item Requisition Report
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	30-09-2021
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
echo load_html_head_contents("Item Requisition Report","../../", 1, 1, $unicode,0,''); 

?>	
<script>
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function generate_report(report_type){
		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Requisition Date')==false )
		{
			return;
		}

        var report_title=$( "div.form_caption" ).html();	
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_item_category_id*cbo_store_name*txt_requisition_no*txt_date_from*txt_date_to',"../../")+'&report_type='+report_type+'&report_title='+report_title;
		//alert (data);
		freeze_window(report_type);
		http.open("POST","requires/item_requisition_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse(){	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//alert();
			setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(){
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
	
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
        $('#scroll_body tr:first').show();
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
        <form name="itemRequisition_1" id="itemRequisition_1" autocomplete="off" > 
            <h3 style="width:850px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
            <div id="content_search_panel" style="width:850px" >      
                <fieldset>  
                    <table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <thead>
                            <th width="130" class="must_entry_caption">Company Name</th>
                            <th width="120" >Location</th>
                            <th width="120">Item Category</th>
                            <th width="120">Store</th> 
                            <th width="100">Requisition No</th>
                            <th class="must_entry_caption">Requisition Date</th>
                            <th width="70"><input type="reset" name="res" id="res" value="Reset" style="width:50px" class="formbutton" onClick="reset_form('itemRequisition_1','report_container*report_container2','','','')" /></th>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td align="center">
                                    <? 
                                        echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/item_requisition_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                                    ?>
                                </td>
                                <td id="location_td" align="center">
                                    <? 
                                        echo create_drop_down( "cbo_location_name", 120, $blank_array,"", 1, "--Select Location--", 0, "" );
                                    ?>
                                </td>
                                <td align="center">
                                    <?
                                        echo create_drop_down( "cbo_item_category_id", 120, $general_item_category,"", 1, "--Select Category--", 0, "");
                                    ?>
                                </td>
                                <td id="store_td" align="center">
                                    <? 
                                        echo create_drop_down( "cbo_store_name", 120, $blank_array,"", 1, "--Select Store--", 0, "");
                                    ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:80px"/>
                                </td>
                                <td align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px;" readonly />                    							
                                    To
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px;" readonly />                        
                                </td>
                                <td align="center">
                                <input type="button" name="search" id="show" value="Show" onClick="generate_report(1)" style="width:50px;" class="formbutton"/>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7" align="center"><? echo load_month_buttons(1);  ?>
                                </td>
                                <td align="center">
                                    
                                </td>
                            </tr>
                        </tfoot>
                    </table> 
                </fieldset> 
            </div>
            <br /> 
                <div id="report_container" align="center"></div>
                <div id="report_container2"></div> 
        </form>    
    </div>
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
