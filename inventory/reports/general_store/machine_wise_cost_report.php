<?
/*-------------------------------------------- Comments
Purpose			: 	This form will Machine Wise Cost Report
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	02-09-2016
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Machine Wise Cost Report","../../../", 1, 1, $unicode,'',''); 
//var_dump($item_category);
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function generate_report(operation)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_location_id*cbo_store_name*cbo_machine_category*cbo_floor_id*cbo_machine_name*txt_date_from*txt_date_to',"../../../")+"&report_title="+report_title;
		//alert (data);return;
		freeze_window(operation);
		http.open("POST","requires/machine_wise_cost_report_controller.php",true);
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
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="230px";
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
        <form name="closingstock_1" id="closingstock_1" autocomplete="off" > 
         <h3 style="width:950px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1080px" >      
            <fieldset>  
                <table class="rpt_table" width="1030" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="130" class="must_entry_caption">Company</th>
                        <th width="130">Location</th>
                        <th width="130">Store Name</th>
                        <th width="130">MC Category</th>
                        <th width="130">Flore</th>
                        <th width="130">MC No</th>
                        <th width="170">Issue Date</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('closingstock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td align="center">
                                <? 
                                    echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/machine_wise_cost_report_controller', this.value , 'load_drop_down_location', 'location_td' );load_drop_down('requires/machine_wise_cost_report_controller',this.value, 'load_drop_down_store', 'store_td' );" );//load_drop_down( 'requires/machine_wise_cost_report_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );
                                ?>                            
                            </td>
                            <td align="center" id="location_td">
                            <?
                            	echo create_drop_down( "cbo_location_id", 130, $blank_array,"", 01, "Select Location", 0, "", 0,"" );
                            ?>
                            </td>
                            <td id="store_td"><?
                        	    echo create_drop_down( "cbo_store_name", 130, $blank_array,"", 1, "-- All Store --", $selected, "",0,"" );
                                ?>
                            </td>
                            <td align="center">
                            <? 
							echo create_drop_down( "cbo_machine_category", 130, $machine_category,"", 1, "-- Select --", "", "load_drop_down( 'requires/machine_wise_cost_report_controller', document.getElementById('cbo_company_name').value+'_'+this.value+'_'+document.getElementById('cbo_floor_id').value, 'load_drop_machine', 'machine_td' );load_drop_down( 'requires/machine_wise_cost_report_controller',document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );" );
							?>
                            </td>
                            <td id="floor_td">
							<? echo create_drop_down( "cbo_floor_id", 130, $blank_array,"", 1, "-- Select Floor --", 0, "",0 ); ?>
                            </td>
                            <td id="machine_td">
								<? echo create_drop_down( "cbo_machine_name", 130, $blank_array,"", 1, "-- Select Machine --", 0, "",0 ); ?>
                            </td>
                            <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px;"/>	
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px;"/>         
                            </td>
                            <td align="center">
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8" align="center"><? echo load_month_buttons(1);  ?></td>
                        </tr>
                    </tfoot>
                </table> 
            </fieldset> 
            </div>
                <div id="report_container" align="center"></div>
                <div id="report_container2"></div> 
        </form>    
    </div>
</body> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
