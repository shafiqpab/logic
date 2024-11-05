<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Linking Operation Track Report.
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	27-09-2021
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
echo load_html_head_contents("Linking Operation Track Report", "../../../", 1, 1,$unicode,1,1);
?>	

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';
	function open_operator_popup()
	{
	 	if( form_validation('cbo_company_name','Company Name')==false )
	 	{
			return;
	 	}
		var company = $("#cbo_company_name").val();	
		var wo_company = $("#cbo_location_id").val();	
			
		var page_link='requires/linking_operation_track_report_controller.php?action=operator_search_popup&company='+company+'&wo_company='+wo_company; 
		var title="Search Operator Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			/*var theform=this.contentDoc.forms[0]; 
			var opID=this.contentDoc.getElementById("hidden_operator_id").value;
			var opIDCard=this.contentDoc.getElementById("hidden_operator_idcard").value;
			var opName=this.contentDoc.getElementById("hidden_operator_name").value; // product Description
			console.log(opID);
			console.log(opName);*/
			var employee_data=(this.contentDoc.getElementById("hidden_emp_number").value).split("_");
			// $("#txt_operator_name").val(employee_data[2]);
			$("#txt_operator_name").val(employee_data[1]);
			$("#hidden_operator_id").val(employee_data[0]); 
			$("#hidden_operator_id_card").val(employee_data[1]); 
		}
	}	 
	 
	function open_style_ref()
	{
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
	    var page_link='requires/linking_operation_track_report_controller.php?action=job_no_search_popup&company='+company+'&style=0'; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("hide_job_id").value;
			//alert(prodID); // product ID
			var prodDescription=this.contentDoc.getElementById("hide_job_no").value; // product Description
			var styleDescription=this.contentDoc.getElementById("hide_style").value; // product Description
			$("#txt_job_no").val(prodDescription);
			$("#txt_style_no").val(styleDescription);
			$("#hidden_job_id").val(prodID); 
		}
	}	 

	function generate_report(type)
	{
		
		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();

		

		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_location_id*txt_operator_name*hidden_operator_id*hidden_operator_id_card*cbo_buyer_name*txt_style_no*hidden_job_id*txt_date_from*txt_date_to*txt_job_no',"../../../")+'&report_title='+report_title+'&type='+type;
		
		freeze_window(3);
		http.open("POST","requires/linking_operation_track_report_controller.php",true);
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
				id: ["knitting_issue","knitting_receive","knitting_receive_weight","balance"],
				col:  [15,16,17,18],
				operation: ["sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
				}
			}
			setFilterGrid("scroll_body",-1,tableFilters);			
		} 
	} 
	

	function new_window()
	{
		const el = document.querySelector('#scroll_body');
		  if (el) {
		    document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none"; 
			$("#scroll_body tr:first").hide();

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

		}
		
		//$(".flt").show();
	}
	


function reset_form()
{
	$("#hidden_style_id").val("");
	$("#hidden_order_id").val("");
	$("#hidden_job_id").val("");
	
}




	 		 	 
</script>

</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../../",'');  ?>
    <h3 style="width:970px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      	<fieldset style="width:970px;">
            <table class="rpt_table" width="970px" cellpadding="0" cellspacing="0" align="center">
               <thead>                    
                   <tr>
                    	<th class="must_entry_caption" width="120">Company Name</th>
                    	<th width="100">Localtion</th>
                    	<th width="100">Operator ID</th>
                    	<th width="120" >Buyer Name</th>
                    	<th width="120" >Style Ref.</th>                   
                    	<th class="must_entry_caption" width="160"> Production Date </th>                  
                    	<th width="140" colspan="2"><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" onClick="reset_form()"/></th>
                	</tr>   
            	</thead>
            	<tbody>
	                <tr class="general">
	                    <td> 
	                        <?
	                            echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/linking_operation_track_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/date_and_style_wise_inspection_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
	                        ?>
	                    </td>	                    
						<td id="location_td">
                        	<? echo create_drop_down( "cbo_location_id", 100, $blank_array,"",1, "-- Select Location --", 0, "" ); ?>
                        </td>
	                    <td>
	                       	<input type="text" id="txt_operator_name"  name="txt_operator_name"  style="width:80px" class="text_boxes" onDblClick="open_operator_popup()" placeholder="Browse/Write" />
							   
	                       	<input type="hidden" id="hidden_operator_id"  name="hidden_operator_id" readonly/>
	                       	<input type="hidden" id="hidden_operator_id_card"  name="hidden_operator_id_card" />
	                    </td>
	                    <td id="buyer_td">
	                        <? 
	                            echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
	                        ?>
	                    </td>
	                    <td>
	                     	<input type="text" id="txt_style_no"  name="txt_style_no"  style="width:120px" class="text_boxes" onDblClick="open_style_ref()" placeholder="Browse/Write" />
	                       	<input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
							   <input type="hidden" id="txt_job_no"  name="txt_job_no" />
	                    </td>
	                  
	                     <td>
	                     	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" >&nbsp; To
	                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  >
	                    </td>
	                    <td colspan="2">
	                        <input type="button" id="show_button" class="formbutton" style="width:80px;" value="Show" onClick="generate_report(1)" />  
							<input type="button" id="show_button" class="formbutton" style="width:80px;" value="Summary" onClick="generate_report(2)" />     
							                      
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
      <div   id="report_container" align="center" style="padding: 5px 0;"></div>
      <div id="report_container2"></div>  
 </form> 
 </div>   
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
