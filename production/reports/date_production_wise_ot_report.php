<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Date Wise Production OT Report.
Functionality	:	
JS Functions	:
Created by		:	Mostafizur Rahman
Creation date 	: 	18/09/2023
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
	

	var page_link='requires/date_production_wise_ot_report_controller.php?action=style_wise_search&company='+company+'&buyer='+buyer; 
	var title="Search Style Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var poID=this.contentDoc.getElementById("txt_selected_id").value;
		var styleDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
		$("#txt_style_no").val(styleDescription);
		$("#hidden_order_id").val(poID); 
	}
}

function generate_report(type)
	{
       
			if( form_validation('cbo_company_name*ot_avg_rate*txt_date_from','Company Name*OT Average Rate *Production Date')==false )
			{
				return;
			}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style_no*txt_date_from*ot_avg_rate',"../../")+'&report_title='+report_title+'&type='+type;
		freeze_window(3);
		http.open("POST","requires/date_production_wise_ot_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			show_msg('3');
			release_freezing();
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		    var tableFilters = 
		    {
			   col_operation:
			   {
			   id: ["grand_total_order","grand_total_plan","grand_total_fabric_qty","grand_fabric_total","grand_fabric_bal","grand_today_cut","grand_total_cut","grand_cutting_balance","grand_today_print_iss","grand_total_print_iss","grand_today_print_rec","grand_total_print_rec","grand_print_bal","grand_today_embl_iss","grand_total_embl_iss","grand_today_embl_rec","grand_total_embl_rec","grand_total_embl_bal","grand_today_wash_iss","grand_total_wash_iss","grand_today_wash_rec","grand_total_wash_rec","grand_total_wash_bal","grand_today_sp_iss","grand_total_sp_iss","grand_today_sp_rec","grand_total_sp_rec","grand_total_sp_bal","grand_today_sew","grand_total_sew","grand_total_stock","grand_today_out","grand_total_out","grand_total_sew_bal","grand_today_finish","grand_total_finish","grand_total_finish_bal"],
			   col: [5,6,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,37,38,39,40,41,42,43,44,45],
			   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
				}	
			}
			//setFilterGrid("table_body",-1,tableFilters);
			
		} 

	} 
	

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow='auto';
        document.getElementById('scroll_body').style.maxHeight='none'; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
        d.close();
        document.getElementById('scroll_body').style.overflowY='scroll';
        document.getElementById('scroll_body').style.maxHeight='600px';
	}
	


function reset_form()
{
	$("#hidden_style_id").val("");
	$("#hidden_order_id").val("");
	$("#hidden_job_id").val("");
	
}


function openmypage_embl(company_id,order_id,order_number,insert_date,type,action,width,height,embl_type,color_id)
	{
	var popup_width=width;
	var popup_height=height;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_production_progress_report_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&order_id='+order_id+'&order_number='+order_number+'&type='+type+'&embl_type='+embl_type+'&color_id='+color_id, 'Detail Veiw', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
		
	} 	


	function openmypage_buyer_ins(style,po_id,action)
	{
		var data=style+'_'+po_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_production_progress_report_controller.php?data='+data+'&action='+action, 'Inspection View', 'width=550px,height=300px,center=1,resize=0,scrolling=0','../');
	}


	 
	 
	
	  
	function openmypage_file(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=300px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{

		}
	}
</script>

</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:820px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:820px;">
            <table class="rpt_table" width="800" cellpadding="0" cellspacing="0" align="center">
               <thead>                    
                       <tr>
                        <th class="must_entry_caption" width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        
                        <th width="110">Style Reference</th>
                        <th width="90" class="must_entry_caption">OT Avg Rate</th>
                        <th width="90" class="must_entry_caption">Production Date</th>
                       
                        <th width="120"><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td width="140"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/date_production_wise_ot_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>
                    </td>
                    <td width="120" id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
					
                    <td width="" id="location_td">
                     <input type="text" id="txt_style_no"  name="txt_style_no"  style="width:120px" class="text_boxes" onDblClick="open_style_ref()" placeholder="Browse/Write" />
                       <input type="hidden" id="hidden_style_id"  name="hidden_style_id" />
                    </td>
                    <td><input name="ot_avg_rate" id="ot_avg_rate" class="text_boxes" style="width:70px" placeholder="write ot_avg_rate"   ></td>
                     <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >
                    </td>
                    <td width="120">
                        <input type="button" id="show_button" class="formbutton" style="width:80px; 
                        " value="Show" onClick="generate_report(1)" />
                       
                    </td>
                </tr>
                </tbody>
            </table>
           
      </fieldset>
      <div   id="report_container" align="center"></div>
      <div id="report_container2"></div>  
 </form> 
 </div>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
