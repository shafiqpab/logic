<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Cutting and Input Inhand Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Helal Uddin
Creation date 	: 	1-11-2020
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
echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,1,1);
?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
var permission = '<? echo $permission; ?>';
 function open_style_ref()
	 {
		 if( form_validation('cbo_company_name','Company Name')==false )
		 {
			return;
		 }
		var company = $("#cbo_company_name").val();	
		
		var page_link='requires/date_wise_knitting_production_report_controller.php?action=job_no_search_popup&company='+company+'&style=1'; 
		var title="Search Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var poID=this.contentDoc.getElementById("hide_job_id").value;
				var styleDescription=this.contentDoc.getElementById("hide_job_no").value; // product Description
				console.log(poID);
				console.log(styleDescription);
				$("#txt_style_no").val(styleDescription);
				$("#hidden_style_id").val(poID); 
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
	    var page_link='requires/date_wise_knitting_production_report_controller.php?action=job_no_search_popup&company='+company+'&style=0'; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var prodID=this.contentDoc.getElementById("hide_job_id").value;
				//alert(prodID); // product ID
				var prodDescription=this.contentDoc.getElementById("hide_job_no").value; // product Description
				$("#txt_job_no").val(prodDescription);
				$("#hidden_job_id").val(prodID); 
				//alert($("#hidden_job_id").val())
			}
	 }
	 
	 

function generate_report(type)
	{
		
		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date*Date')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_working_company*cbo_buyer_name*txt_job_no*cbo_year_selection*txt_style_no*hidden_job_id*hidden_style_id*txt_date_from*cbo_status*txt_date_to',"../../../")+'&report_title='+report_title+'&type='+type;
		freeze_window(3);
		http.open("POST","requires/date_wise_knitting_production_report_controller.php",true);
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

			var tableFilters = {
				col_operation: {
				id: ["knitting_issue","knitting_receive","balance","knitting_issue_weight","knitting_receive_weight"],
				col:  [14,15,16,17,18],
				operation: ["sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
				}
			}
			var tableFilters1 = {
				col_operation: {
				id: ["knitting_issue_acc","knitting_receive_acc","balance_acc","knitting_issue_weight_acc","knitting_receive_weight_acc"],
				col:  [14,15,16,17,18],
				operation: ["sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
				}
			}
			setFilterGrid("scroll_body",-1,tableFilters);
			setFilterGrid("scroll_body1",-1,tableFilters1);
			
		} 

	} 
	

	function new_window()
	{
		const el = document.querySelector('#scroll_body');
		  if (el) {
		    document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none"; 
			$("#scroll_body tr:first").hide();
			document.getElementById('scroll_body1').style.overflow="auto";
			document.getElementById('scroll_body1').style.maxHeight="none"; 
			$("#scroll_body1 tr:first").hide();

		}
		
		//$(".flt").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		 if (el) {
		    document.getElementById('scroll_body').style.overflowY="auto"; 
			document.getElementById('scroll_body').style.maxHeight="400px";
			$("#scroll_body tr:first").show();
			document.getElementById('scroll_body1').style.overflowY="auto"; 
			document.getElementById('scroll_body1').style.maxHeight="400px";
			$("#scroll_body1 tr:first").show();

		}
		
		//$(".flt").show();
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
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_knitting_production_report_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&order_id='+order_id+'&order_number='+order_number+'&type='+type+'&embl_type='+embl_type+'&color_id='+color_id, 'Detail Veiw', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../../../');
		
	} 	


	function openmypage_buyer_ins(style,po_id,action)
	{
		var data=style+'_'+po_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_knitting_production_report_controller.php?data='+data+'&action='+action, 'Inspection View', 'width=550px,height=300px,center=1,resize=0,scrolling=0','../../../');
	}


	 
	 
	function print_report_button_setting(company_id)
	{

		var report_ids=return_global_ajax_value(company_id, 'print_report_button_setting', '', 'requires/date_wise_knitting_production_report_controller');

		$("#show_button").hide();	 
		$("#show_button2").hide();	 

		var report_id=report_ids.split(",");
		if(trim(report_ids))
		{


			for (var k=0; k<report_id.length; k++)
			{
				if(report_id[k]==108)
				{
					$("#show_button").show();	 
				}
				if(report_id[k]==195)
				{
					$("#show_button2").show();	 
				}
			}
		}
		else
		{
			$("#show_button").show();	 
			$("#show_button2").show();	
		}
	}
	 
	$(function(){
		$("#cbo_status").val(1);
	}) ;
	 		 	 
</script>

</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../../",'');  ?>
    <h3 style="width:1310px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:1310px;">
            <table class="rpt_table" width="1300px" cellpadding="0" cellspacing="0" align="center">
               <thead>                    
                       <tr>
                        <th class="must_entry_caption" width="150">Company Name</th>
                        <th width="150">Working Company </th>
                        <th width="150" >Buyer Name</th>
                        <th width="110">Job No</th>
                        <th width="110">Style Reference</th>
                      
                        <th width="110">Shipment Status </th>
                      
                        
                        <th class="must_entry_caption" width="150"> Production Date </th>
                      
                        <th width="180"><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td width="140"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/date_wise_knitting_production_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );print_report_button_setting(this.value)" );
                        ?>
                    </td>
                     <td width="140"> 
                        <?
                            echo create_drop_down( "cbo_working_company", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                        ?>
                    </td>
                    <td width="120" id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td width="">
                       <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:120px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse/Write" />
                       <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                    </td>
                    <td width="" id="location_td">
                     <input type="text" id="txt_style_no"  name="txt_style_no"  style="width:120px" class="text_boxes" onDblClick="open_style_ref()" placeholder="Browse/Write" />
                       <input type="hidden" id="hidden_style_id"  name="hidden_style_id" />
                        
                    </td>
                  
                     <td>
                     <?
					     // $shipment_status=array(1=>"All",3=>"Full Shipment",2=>"Pending"); // change 14-10-2018
					     $shipment_status=array(2=>"Full Pending and Partial Shipment",3=>"Full Shipment/Closed");
                         echo create_drop_down( "cbo_status", 110, $shipment_status,"", 1, "--Select--", 1, "",0,"" );
                     ?>
                     </td>
                   
                    
                     <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >&nbsp; To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  ></td>
                   
                  
                    <td width="180">
                        <input type="button" id="show_button" class="formbutton" style="width:80px;" value="Show" onClick="generate_report(1)" />
                        <input type="button" id="show_button2" class="formbutton" style="width:80px;" value="Show 2" onClick="generate_report(2)" />
                        
                    </td>
                </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td>
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
            </table> 
      </fieldset>
      <div   id="report_container" align="center"></div>
      <div id="report_container2"></div>  
 </form> 
 </div>   
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>