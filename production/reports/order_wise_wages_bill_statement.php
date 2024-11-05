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
echo load_html_head_contents("Order Wise Wages Bill Statement", "../../", 1, 1,$unicode,'','');
?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
 
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
	    var page_link='requires/order_wise_wages_bill_statement_controller.php?action=order_wise_search&company='+company+'&buyer='+buyer+'&style_id='+style_id+'&job_id='+job_id+'&job_no='+job_no; 
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
	 
	 
	 
function generate_report(str)
	{
		
		if( form_validation('cbo_company_name*cbo_buyer_name','Company Name*Buyer Name')==false ){return;}
		
		var report_title=$( "div.form_caption" ).html();
		if(str==1){
			var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_order_no*hidden_order_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		}
		else if(str==2){
			var data="action=sewing_short_report"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_order_no*hidden_order_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		}
		freeze_window(3);
		http.open("POST","requires/order_wise_wages_bill_statement_controller.php",true);
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
			if(reponse[2]==1){
				var tableFilters = 
				{
					col_operation: {
						id: ["total_po_qnty","total_cut_appv_qty","total_cut_bill_qty","total_remain_qty_cut","total_cut_bill_amnt","total_cut_qnty","total_excess_cut_qty","total_sew_appv_qty","total_sew_bill_qty","total_remain_qty_sew","total_sew_bill_amnt","total_sew_qnty","total_excess_sewingout_qty","total_iron_appv_qty","total_finish_bill_qty","total_remain_qty_iron","total_finish_bill_amnt","total_iron_input_qnty","total_excess_iron_input_qty","total_grand_total"],
						col: [5,6,8,9,10,11,12,13,15,16,17,18,19,20,22,23,24,25,26,27],
						operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				} 
			}
			else if(reponse[2]==2){
				var tableFilters = 
				{
					col_operation: {
						id: ["total_po_qnty","total_sew_appv_qty","total_sew_bill_qty","total_remain_qty_sew","total_sew_qnty","total_excess_sewingout_qty"],
						col: [5,6,7,8,9,10],
						operation: ["sum","sum","sum","sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				} 
			}
			setFilterGrid("table_body",-1,tableFilters);
			
		}
	} 
	 
	
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$("#table_body tr:first").show();
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="300px";
	}
	
	function openmypage_bill_info(company,po_id,item_id,type)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_wages_bill_statement_controller.php?company='+company+'&po_id='+po_id+'&item_id='+item_id+'&action='+type, 'Bill Qnty Info', 'width=700px,height=350px,center=1,resize=0,scrolling=0','../');
	
	}
	 
	 

</script>

</head>
 
<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center"> 
   <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:850px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">    
      <fieldset style="width:850px;">
            <table class="rpt_table" width="850px" cellpadding="0" cellspacing="0" align="center">
               <thead>                    
                       <tr>
                        <th class="must_entry_caption" width="150">Company Name</th>
                        <th class="must_entry_caption">Buyer Name</th>
                        <th>Order No </th>
                        <th> Country Shipment Date </th>
                      
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" onClick="reset_form()"/></th>
                    </tr>   
              </thead>
                <tbody>
                <tr class="general">
                    <td> 
                        <?
                            echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/daily_production_progress_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' )" );
                        ?>
                    </td>
                    <td id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td id="floor_td">
                     <input type="text" id="txt_order_no"  name="txt_order_no"  style="width:120px" class="text_boxes" onDblClick="open_order_no()" placeholder="Browse/Write" />
                     <input type="hidden" id="hidden_order_id"  name="hidden_order_id" />
                    </td>
                    
                     <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" >&nbsp; To
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date"  ></td>
                   
                  
                    <td>
                        <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="generate_report(1)" />
                        <input type="button" id="show_button" class="formbutton" style="width:80px" value="Sewing Short" onClick="generate_report(2)" />
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
     </form> 
      </div>
      
      <div id="report_container" align="center"></div>
      <div id="report_container2"></div>  
 </div>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
