<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knitting WIP Report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Shafiqul Islam shafiq
Creation date 	: 	07-05-2019
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
echo load_html_head_contents("Party Wise Yarn Reconciliation","../../", 1, 1, $unicode,1,0); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{			
		if( form_validation('cbo_company_name*txt_rate*txt_date_from*txt_date_to','Company Name*Rate*From Date*To Date')==false)
		{
			return;
		}
		else
		{		
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_lc_location_name*cbo_source*cbo_party_name*cbo_wc_location_name*txt_rate*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title+'&type='+type;
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/knitting_wip_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = generate_report_reponse;  
		}
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");
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
		//$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
	}	

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../",$permission); ?><br />    		 
    <form name="PartyWiseYarnReconciliation_1" id="PartyWiseYarnReconciliation_1" autocomplete="off" > 
        <div style="width:100%;" align="center">
            <fieldset style="width:1090px;">
            <legend>Search Panel</legend> 
                <table class="rpt_table" width="1090" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="140" class="must_entry_caption">LC Company</th>                                
                            <th width="130">Location</th>
                            <th width="100">Source</th>
                            <th width="100">Working Company</th>
                            <th width="140">Location</th>
                            <th width="80" class="must_entry_caption">Rate</th>
                            <th class="must_entry_caption">Date Range</th>
                            <th width="100"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('PartyWiseYarnReconciliation_1', 'report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/knitting_wip_report_controller',this.value, 'load_drop_down_location', 'cbo_lc_location_td' );" );
                            ?>                            
                        </td>
                        <td id="cbo_lc_location_td">
                            <? 
                               echo create_drop_down( "cbo_lc_location_name", 140, "$blank_array","", 1, "-- Select Location --", $selected, "" );
                            ?>                            
                        </td>
                        <td>
							<?
                                // echo create_drop_down("cbo_knitting_source",130,$knitting_source,"", 1, "-- Select Source --", 0,"kniting_company_val();",0,'1,3');
                                echo create_drop_down("cbo_source",130,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'requires/knitting_wip_report_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_party','party_td');",0,'1,3');
                            ?>
                        </td> 
                        <td id="party_td">
							<?
                                echo create_drop_down("cbo_party_name",130,$blank_array,"", 1, "-- Select Source --", 0,"",0,'');
                            ?>
                        </td>                            
                        <td id="wc_location">
                            <?
                            echo create_drop_down("cbo_wc_location_name", 130, $blank_array, "", 1, "-- Select Location --", $selected, "");
                            ?>
                        </td>        
                       
                         <td>
                            <input type="text" id="txt_rate" name="txt_rate" class="text_boxes" style="width:100px" placeholder="Enter Rate" />
                        </td>
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("01-m-Y"); ?>" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y"); ?>" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                        <td>
                        	<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" />
                        </td>
                    </tr>
                    <tr class="general">
                        <td colspan="9" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
            </fieldset>  
            
            <div id="report_container" align="center" style="padding: 10px;"></div>
        	<div id="report_container2"></div>
        </div>
    </form>    
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
