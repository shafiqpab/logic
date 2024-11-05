<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Cutting and Input Inhand Report.
Functionality	:	
JS Functions	:
Created by		:	Ashraful Islam
Creation date 	: 	26-04-2014
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
echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,'','');

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
 function open_style_ref()
	 {
		 if( form_validation('cbo_company_name','Company Name')==false )
				{
					return;
				}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var page_link='requires/style_wise_production_report_controller.php?action=style_wise_search&company='+company+'&buyer='+buyer+'&job_no='+job_no+'&job_id='+job_id; 
		var title="Search Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var styleID=this.contentDoc.getElementById("txt_selected_id").value;
				var styleDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
				$("#txt_style_no").val(styleDescription);
				$("#hidden_style_id").val(styleID); 
			}
	 }	
 function open_order_no()
	 {
		 if( form_validation('cbo_company_name','Company Name')==false)
				{
					return;
				}
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var style_no=$('#txt_style_no').val();
		var style_id=$('#hidden_style_id').val();
	    var page_link='requires/style_wise_production_report_controller.php?action=order_wise_search&company='+company+'&buyer='+buyer+'&style_id='+style_id+'&job_id='+job_id+'&job_no='+job_no; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=560px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				//alert(prodID); // product ID
				var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
				$("#txt_order_no").val(prodDescription);
				$("#hidden_order_id").val(prodID); 
			}
	 }
	 
	 
	 
	 function open_job_no()
	   {
		 if( form_validation('cbo_company_name','Company Name')==false)
				{
					return;
				}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
	    var page_link='requires/style_wise_production_report_controller.php?action=job_wise_search&company='+company+'&buyer='+buyer; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=510px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				//alert(prodID); // product ID
				var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
				$("#txt_job_no").val(prodDescription);
				$("#hidden_job_id").val(prodID); 
				//alert($("#hidden_job_id").val())
			}
	 }
	 
	 

function generate_report()
	{
		if( form_validation('cbo_company_name*txt_date_from','Company Name*Date')==false )
			{
				return;
			}

		var report_title=$( "div.form_caption" ).html();
		
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_style_no*txt_order_no*hidden_order_id*hidden_job_id*hidden_style_id*txt_date_from*cbo_search_by',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/style_wise_production_report_controller.php",true);
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
			var tot_rows=reponse[2];
			var search_by=reponse[3];
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			if(tot_rows*1>1)
			{
			if(search_by==1)
			    {
			
				 var tableFilters = 
				 {
					  /*col_5: "none",
					  col_12: "none",
					  col_20: "none",
					  display_all_text: " ---Show All---",*/
					   col_operation: {
					   id: ["value_job_total","value_fabric_issue","value_plan_cut","value_cut_today","value_cut_total","value_cut_bal","value_embl_iss","value_embl_iss_total","value_embl_iss_bal","value_embl_rec","value_embl_rec_total","value_embl_rec_bal","value_sew_in","value_sew_in_to","value_sew_in_bal","value_sew_out","value_sew_out_total","value_sew_out_bal","value_iron","value_iron_to","value_iron_bal","value_reject","value_reject_to","value_finish","value_finish_to","value_finish_bal","value_exfactory","value_exfactory_to","value_exfac_bal","value_finish","value_finish_to"],
					   col: [5,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37],
					   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}	
				 }
				}
				if( search_by==3)
			    {
			
				 var tableFilters = 
				 {
					  /*col_5: "none",
					  col_12: "none",
					  col_20: "none",
					  display_all_text: " ---Show All---",*/
					   col_operation: {
					   id: ["value_job_total","value_fabric_issue","value_plan_cut","value_cut_today","value_cut_total","value_cut_bal","value_embl_iss","value_embl_iss_total","value_embl_iss_bal","value_embl_rec","value_embl_rec_total","value_embl_rec_bal","value_sew_in","value_sew_in_to","value_sew_in_bal","value_sew_out","value_sew_out_total","value_sew_out_bal","value_iron","value_iron_to","value_iron_bal","value_re_iron","value_re_iron_to","value_reject","value_reject_to","value_finish","value_finish_to","value_finish_bal","value_exfactory","value_exfactory_to","value_exfac_bal"],
					   col: [5,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37],
					   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}	
				 }
				}

			if(search_by==2 || search_by==4)
			    {
			
				 var tableFilters = 
				 {
					  /*col_5: "none",
					  col_12: "none",
					  col_20: "none",
					  display_all_text: " ---Show All---",*/
					   col_operation: {
					   id: ["value_job_total","value_fabric_issue","value_plan_cut","value_cut_today","value_cut_total","value_cut_bal","value_embl_iss","value_embl_iss_total","value_embl_iss_bal","value_embl_rec","value_embl_rec_total","value_embl_rec_bal","value_sew_in","value_sew_in_to","value_sew_in_bal","value_sew_out","value_sew_out_total","value_sew_out_bal","value_iron","value_iron_to","value_iron_bal","value_reject","value_reject_to","value_finish","value_finish_to","value_finish_bal","value_exfactory","value_exfactory_to","value_exfac_bal"],
					   col: [6,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36],
					   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}	
				 }
				}
		
			setFilterGrid("table_body",-1,tableFilters);
		  }
			show_msg('3');
			release_freezing();
	  } 
	}
	function new_window(html_filter_print)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		if(html_filter_print*1>1) $("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
		if(html_filter_print*1>1) $("#table_body tr:first").show();
	}
	


function reset_form()
{
	$("#hidden_style_id").val("");
	$("#hidden_order_id").val("");
	$("#hidden_job_id").val("");
	
}
	
function openmypage(company_id,jobnumber_prefix,insert_date,action,width)
	{
		var popup_width=width;
		

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_production_report_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&jobnumber_prefix='+jobnumber_prefix, 'Detail Veiw', 'width='+popup_width+', height=400px,center=1,resize=0,scrolling=0','../');
	}
function openmypage_order(company_id,order_id,order_number,insert_date,type,action,width,height)
	{
		
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_production_report_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&order_id='+order_id+'&order_number='+order_number+'&type='+type, 'Detail Veiw', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
      
		
	}	
	 	
	 	 
</script>

</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:1050px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:1050px;">
            <table class="rpt_table" width="1070px" cellpadding="0" cellspacing="0" align="center">
               <thead>                    
                       <tr>
                        <th class="must_entry_caption" width="150">Company Name</th>
                        <th width="120">Buyer Name</th>
                        <th width="120">Type</th>
                        <th width="120">job No</th>
                        <th width="130">Style Reference</th>
                        <th width="130">Order No </th>
                        
                        <th id="search_text_td" class="must_entry_caption" width="100"> Date</th>
                        <th width="100"><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td width="150"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/style_wise_production_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' )" );
                        ?>
                    </td>
                    <td width="150" id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                     <td align="center" width="120">
                    	<? 
                            $search_by_arr = array(1=>"Style Wise(All)",2=>"Order Wise(All)",3=>"Style Wise",4=>"Order Wise");
							echo create_drop_down( "cbo_search_by", 120, $search_by_arr,"",0, "", 3,'',0 );//search_by(this.value)
                         ?>
                          </td>
                    <td width="120">
                       <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:120px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse/Write" />
                    </td>
                    <td width="130" id="location_td">
                     <input type="text" id="txt_style_no"  name="txt_style_no"  style="width:130px" class="text_boxes" onDblClick="open_style_ref()" placeholder="Browse/Write" />
                       <input type="hidden" id="hidden_style_id"  name="hidden_style_id" />
                       <input type="hidden" id="hidden_order_id"  name="hidden_order_id" />
                        <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                    </td>
                    <td width="130" id="floor_td">
                     <input type="text" id="txt_order_no"  name="txt_order_no"  style="width:130px" class="text_boxes" onDblClick="open_order_no()" placeholder="Browse/Write" />
                    </td>
                    <td width="100">
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" >
                    </td>
                    <td width="100">
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="generate_report()" />
                    </td>
                </tr>
                </tbody>
            </table>
      </fieldset>
       
 </form> 
 </div> 
  <div id="report_container" align="center"></div>
      <div id="report_container2"></div>  
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
