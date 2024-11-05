<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Tims accessories report
				
Functionality	:	
JS Functions	:
Created by		:	WAYASEL AHMMED
Creation date 	: 	11-12-2023
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
//------------------------------------------------------------
echo load_html_head_contents("Tims accessories report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';

	function fn_report_generated(report_type)
	{
		var txt_job_no=document.getElementById('txt_job_no').value;	

		if(txt_job_no!="")
		{
			if(form_validation('cbo_company_id','Company')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company*From date*To date')==false)
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report&report_type="+report_type+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_working_location*cbo_floor_name*cbo_sewing_line*txt_job_no*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/date_wise_requisition_and_style_wise_requisition_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;  

	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(type)
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	}
	
    function BuyerWithLocation_load(){
        var company_id=$("#cbo_company_id").val();
        load_drop_down( 'requires/date_wise_requisition_and_style_wise_requisition_report_controller',company_id, 'load_drop_down_working_location', 'working_location_td')
         load_drop_down( 'requires/date_wise_requisition_and_style_wise_requisition_report_controller', company_id, 'load_drop_down_buyer', 'buyer_td' );
    }
    function Floor_load(location){
        var company_id=$("#cbo_company_id").val();
        load_drop_down( 'requires/date_wise_requisition_and_style_wise_requisition_report_controller', location+'*'+company_id, 'load_drop_down_floor', 'floor_td' );
    }
    function Swine_load(floor_id){
        var company_id=$("#cbo_company_id").val();
        var location_id=$("#cbo_working_location").val();
        load_drop_down( 'requires/date_wise_requisition_and_style_wise_requisition_report_controller', floor_id+'_'+company_id+'_'+location_id, 'load_drop_down_sewing_line', 'sewing_td' )
    }

    function fn_order()
	{
		var cbo_company_id = $("#cbo_company_id").val();
		var floor_name = $("#cbo_floor_name").val();
		var sewing_line = $("#cbo_sewing_line").val();
	
		var title = 'Job Info';	
		var page_link = 'requires/date_wise_requisition_and_style_wise_requisition_report_controller.php?cbo_company_id='+cbo_company_id+'&floor_name='+floor_name+'&sewing_line='+sewing_line+'&action=po_search_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1195px,height=420px,center=1,resize=1,scrolling=0','../../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_job_no=this.contentDoc.getElementById("hidden_job_no").value; 
            $("#txt_job_no").val(hidden_job_no);
		}   
	}
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>    		 
        <form name="greyissuestatus_1" id="greyissuestatus_1" autocomplete="off" > 
         <h3 style="width:1300px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1300px" >      
            <fieldset>  
                <table class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th class="must_entry_caption">Company</th>
                        <th>Location</th>                      
                        <th>Garments Floor</th>                      
                        <th>Sewing Line</th>
                        <th>Job No.</th>
                        <th>Buyer</th>
                        <th align="center" class="must_entry_caption">Requisition Date</th>
                       <th><input type="reset" name="res" id="res" value="Reset" style="width:120px" onClick="$('#txt_style').val('');$('#txt_order_no_id').val('');" class="formbutton" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <? 
								echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "" );
                                ?>                            
                            </td>
                            <td width="160" id="working_location_td">
                                <?
                                echo create_drop_down( "cbo_working_location", 152, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                                ?>
                            </td>                              
                            <td width="160" id="floor_td">
                                <? 
								echo create_drop_down( "cbo_floor_name", 152, $blank_array,"", 1, "-- Select Floor --", $selected, "" );?>
                            </td>
                           
                            <td id="sewing_td">
                                <?
                                 echo create_drop_down( "cbo_sewing_line", 152, $blank_array,"", 1, "--- Select ---", $selected, "",1 );
                                ?>
                            </td>                          
                            <td>
                                <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px;" onDblClick="fn_order()"  placeholder="Browse" />
                                <input type="hidden" id="hdn_order_id" name="hdn_order_id" />
                            </td>
                            <td id="buyer_td">
								<?
								echo create_drop_down( "cbo_buyer_id", 150,$blank_array,"", 1, "-- Select Buyer--", $selected, "","","","","","");
                                ?> 
                          	</td>                          
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:50px;" />
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:50px;"/>
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Style Wish" onClick="fn_report_generated(1)" style="width:60px" class="formbutton" />
                                <input type="button" name="search" id="search" value="Date Wish" onClick="fn_report_generated(2)" style="width:60px" class="formbutton" />
                            </td>                       
                        </tr>                      
                    </tbody>
                    <tfoot>                   
                        <tr>
                            <td colspan="9" align="center"><? echo load_month_buttons(1);  ?>
                            </td>                        
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
<script>
    set_multiselect('cbo_company_id','0','0','','0',"BuyerWithLocation_load()");
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
