<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Daily Cutting and Input Inhand Report.
Functionality	:	
JS Functions	:
Created by		:	Kamrul
Creation date 	: 	03-09-2023
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
echo load_html_head_contents("Production Status Report Sweater", "../../../", 1, 1,$unicode,1,1);
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
		var working_company = $("#cbo_working_company").val();	
		var year=$("#cbo_year_selection").val();
		// alert(year);
		
		var page_link='requires/production_status_report_sweater_controller.php?company='+company+'&working_company='+working_company+'&year='+year+'&action=style_no_search_popup';  
		var title="Search Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=820px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var poID=this.contentDoc.getElementById("hide_style_id").value;
				var styleDescription=this.contentDoc.getElementById("hide_style_no").value; // product Description
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
		var working_company = $("#cbo_working_company").val();	
		var year=$("#cbo_year_selection").val();
		// alert(year);
		
		var page_link='requires/production_status_report_sweater_controller.php?company='+company+'&working_company='+working_company+'&year='+year+'&action=job_no_search_popup';  
		var title="Search Job Popup";
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

	 function open_po_no()
	 {
		 if( form_validation('cbo_company_name','Company Name')==false)
				{
					return;
				}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
	    var year=$("#cbo_year_selection").val();
		var working_company = $("#cbo_working_company").val();	
		// alert(year);
		
		var page_link='requires/production_status_report_sweater_controller.php?company='+company+'&working_company='+working_company+'&year='+year+'&action=po_no_search_popup';  
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var prodID=this.contentDoc.getElementById("hide_po_id").value;
				//alert(prodID); // product ID
				var prodDescription=this.contentDoc.getElementById("hide_po_no").value; // product Description
				$("#txt_po_no").val(prodDescription);
				$("#hidden_po_id").val(prodID); 
				//alert($("#hidden_job_id").val())
			}
	 }
	 
	 

function generate_report(type)
	{
		
		let job_no= $("#txt_job_no").val();
		let style_ref= $("#txt_style_no").val();
		let po_no= $("#txt_po_no").val(); 

		if(job_no=="" && po_no=="" &&  style_ref=="" )
        {        
        	
			if ($('#cbo_company_name').val() || $('#cbo_working_company').val()) 
			{
				if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
				{
					return;
				}

			}
			else
			{
				if (form_validation('cbo_working_company*txt_date_from*txt_date_to','Working Company*From Date*To Date')==false)
				{
					return;
				}
			}
        }
		else
		{
			
			if (!$('#cbo_company_name').val()) 
			{
				if (form_validation('cbo_working_company','Working Company')==false)
				{
					return;
				}
			}
		}	
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_working_company*cbo_buyer_name*txt_job_no*cbo_year*txt_style_no*hidden_job_id*txt_po_no*hidden_po_id*txt_date_from*cbo_ship_status*txt_date_to',"../../../")+'&report_title='+report_title+'&type='+type;
		freeze_window(3);
		http.open("POST","requires/production_status_report_sweater_controller.php",true);
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
		const el = document.querySelector('#scroll_body');
		  if (el) {
		    document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none"; 

		}
		
		$(".flt").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		 if (el) {
		    document.getElementById('scroll_body').style.overflowY="auto"; 
			document.getElementById('scroll_body').style.maxHeight="400px";

		}
		
		$(".flt").show();
	}
	


function reset_form()
{
	$("#hidden_style_id").val("");
	$("#hidden_order_id").val("");
	$("#hidden_job_id").val("");
	
}
function getCompanyId() 
{
    var company_id = document.getElementById('cbo_company_name').value;
    //var search_type = document.getElementById('cbo_search_by').value;
    if(company_id !='') 
    {
      	var data="action=load_drop_down_buyer&choosenCompany="+company_id;
      	http.open("POST","requires/production_status_report_sweater_controller.php",true);
      	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
      	http.send(data); 
      	http.onreadystatechange = function()
      	{
          	if(http.readyState == 4) 
          	{
              	var response = trim(http.responseText);
              	$('#buyer_td').html(response);  
    			
          	}			 
      	};
    }    
}


function openmypage_embl(company_id,order_id,order_number,insert_date,type,action,width,height,embl_type,color_id)
	{
	var popup_width=width;
	var popup_height=height;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/production_status_report_sweater_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&order_id='+order_id+'&order_number='+order_number+'&type='+type+'&embl_type='+embl_type+'&color_id='+color_id, 'Detail Veiw', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../../../');
		
	} 	


	function openmypage_buyer_ins(style,po_id,action)
	{
		var data=style+'_'+po_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/production_status_report_sweater_controller.php?data='+data+'&action='+action, 'Inspection View', 'width=550px,height=300px,center=1,resize=0,scrolling=0','../../../');
	}


	 
	 
	function print_report_button_setting(company_id)
	{

		var report_ids=return_global_ajax_value(company_id, 'print_report_button_setting', '', 'requires/production_status_report_sweater_controller');

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
    <h3 style="width:1410px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:1410px;">
            <table class="rpt_table" width="1400px" cellpadding="0" cellspacing="0" align="center">
               <thead>                    
                       <tr>
                        <th class="must_entry_caption" width="150">Company Name</th>
                        <th width="150">Working Company </th>
                        <th width="150" >Buyer Name</th>
						<th>Year</th>
                        <th width="110">Job No</th>
						<th width="110">Style Ref.</th>
						<th width="110">Po No</th>
                        <th width="110">Shipment Status </th>      
                        <th class="must_entry_caption" width="150"> Production Date </th>
                      
                        <th width="180"><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td width="140" id="company_td"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/production_status_report_sweater_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );print_report_button_setting(this.value)" );
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
					<td width="60">
                    	<? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", 0, "",0,"" ); ?>
                    </td>
                    <td width="">
                       <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:120px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse/Write" />
                       <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                    </td>
                    <td width="" id="location_td">
                     <input type="text" id="txt_style_no"  name="txt_style_no"  style="width:120px" class="text_boxes" onDblClick="open_style_ref()" placeholder="Browse/Write" />
                       <input type="hidden" id="hidden_style_id"  name="hidden_style_id" />
                        
                    </td>
					<td width="">
                       <input type="text" id="txt_po_no"  name="txt_po_no"  style="width:120px" class="text_boxes" onDblClick="open_po_no()" placeholder="Browse/Write" />
                       <input type="hidden" id="hidden_po_id"  name="hidden_po_id" />
                    </td>
                  
                     <td>
                     <?
					     // $shipment_status=array(1=>"All",3=>"Full Shipment",2=>"Pending"); // change 14-10-2018
					   
						 $shipStatus=array(1 => "Full Shipped",2 => "Partial or Pending");
						echo create_drop_down( "cbo_ship_status", 100, $shipStatus,"", 1, "--Select--", 0, "",0,"" ); 
                     ?>
                     </td>
                   
                    
                     <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >&nbsp; To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  ></td>
                   
                  
                    <td width="180">
                        <input type="button" id="show_button" class="formbutton" style="width:80px;" value="Show" onClick="generate_report(1)" />

						<input type="button" id="show_button2" class="formbutton" style="width:80px;" value="Show2" onClick="generate_report(2)" />
                        
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
<script>    	
    	set_multiselect('cbo_company_name','0','0','','0');
    	setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_name,'0');getCompanyId();") ,3000)];
    	
    	set_multiselect('cbo_working_company','0','0','','0'); 
    	 
    	$('#cbo_buyer_name').val(0);
    </script>
 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
