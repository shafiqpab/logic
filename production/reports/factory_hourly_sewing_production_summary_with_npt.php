<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Factory Hourly Sewing Production Summary With NPT Report
				
Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	03-03-2021
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
echo load_html_head_contents("Factory Hourly Sewing Production Summary With NPT","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
		{
			return;
		}
		freeze_window(3);
		var report_title=$( "div.form_caption" ).html();
		var action = (type==1) ? "report_generate" : "report_generate2";
		var data="action="+action+get_submitted_data_string('cbo_company_id*cbo_location_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		
		http.open("POST","requires/factory_hourly_sewing_production_summary_with_npt_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			//alert(reponse[1]);
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
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}

	function openmypage_line()
	{
		if( form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var location=$("#cbo_location_id").val();
		var floor_id=$("#cbo_floor_id").val();
		var txt_date=$("#txt_date").val();
		var page_link='requires/factory_hourly_sewing_production_summary_with_npt_controller.php?action=line_search_popup&company='+company+'&location='+location+'&floor_id='+floor_id+'&txt_date='+txt_date; 
		
		var title="Search line Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=280px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#cbo_line").val(prodDescription);
			$("#hidden_line_id").val(prodID); 
		}
	}
	 
	function openmypage(company_id,order_id,floor_id,line_no,action,item_smv,prod_date)
	{
		popup_width='550px'; 

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/factory_hourly_sewing_production_summary_with_npt_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&item_smv='+item_smv, 'Detail Veiw', 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
	}
	function openmypage(company_id,order_id,subcon_order,floor_id,line_no,action,item_smv,actual_time,line_date,prod_date)
		{
			 popup_width='550px'; 

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/factory_hourly_sewing_production_summary_with_npt_controller.php?po_id='+order_id+'&company_id='+company_id+'&action='+action+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&item_smv='+item_smv+'&subcon_order='+subcon_order+'&line_date='+line_date+'&actual_time='+actual_time, 'Detail Veiw', 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
		}
	 
</script>

</head>
<body onLoad="set_hotkey();">

<form id="LineWiseProductivityAnalysis_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:600px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:600px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="160" class="must_entry_caption">Company</th>
                    <th width="160">Location</th>
                    <th class="must_entry_caption">Production Date</th>
                    <th width="120"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('LineWiseProductivityAnalysis_1','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td>
							<? 
                                echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/factory_hourly_sewing_production_summary_with_npt_controller',this.value, 'load_drop_down_buyer', 'buyer_td_id' );load_drop_down( 'requires/factory_hourly_sewing_production_summary_with_npt_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>                            
                        </td>
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 1, "-- Select Location --", "", "" );
                            ?>                            
                        </td>                        
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" value="<? echo date('d-m-Y', strtotime('first day of last month'));?>">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" value="<? echo date('d-m-Y', strtotime('last day of last month'));?>">
                        </td>  
                        <td>      
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(2)" style="width:40px" class="formbutton" />
                            <input type="button" name="search" id="search" value="Actual Smv" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                        </td>
                    </tr>
                    <!-- <tr>
                        <td colspan="4" align="center" width="100%"><? echo load_month_buttons(1); ?></td>
                    </tr> -->
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center" style="padding:5px 0"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>