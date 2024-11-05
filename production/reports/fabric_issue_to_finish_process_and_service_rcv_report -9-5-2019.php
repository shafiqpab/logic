<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Issue to Finish Process and Fab Service Receive Report.
Functionality	:	
JS Functions	:
Created by		:	Tofazzal
Creation date 	: 	19-12-2017
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
echo load_html_head_contents("Fabric Issue to Finish Process and Fab Service Receive Report", "../../", 1, 1,'',1,1);
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';

function fn_report_generated()
{
	if (form_validation('cbo_company_name','Comapny Name')==false)
	{
		return;
	}
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_order_type*cbo_presentation*txt_date_from*txt_date_to*cbo_service_source*cbo_supplier_name*txt_ref_no*txt_file_no*txt_wo_no',"../../")+'&report_title='+report_title;
	freeze_window(3);
	http.open("POST","requires/fabric_issue_to_finish_process_and_service_rcv_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML=report_convert_button('../../'); 

       setFilterGrid("tbl_list_search",-1);
		show_msg('3');
		release_freezing();
 	}
}
function check_supplier_td(type_id)
{
		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
		return;
		}
		if(type_id==1)
		{
			document.getElementById('supplier_head').innerHTML='Company';
			//$('#supplier_head').css('color','blue');
		}
		else
		{
			document.getElementById('supplier_head').innerHTML='Supplier';
			//$('#supplier_head').css('color','blue');
		}
}
function openmypage_wo_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var cbo_buyer_name = $("#cbo_buyer_name").val();

		var page_link='requires/fabric_issue_to_finish_process_and_service_rcv_report_controller.php?action=booking_no_popup&companyID='+companyID+ '&cbo_buyer_name='+cbo_buyer_name;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=710px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_job_no").value;
			var booking_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_wo_no').val(booking_no);
			$('#txt_booking_no').val(booking_no);
		}
	}
</script>
</head>
 
<body onLoad="set_hotkey();">

<form id="fabricReceiveStatusReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:950px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:950px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                	<th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
					<th>Service Source</th>
					
					<th id="supplier_head">Supplier</th>
                    <th>Job No</th>
					 <th>Int. Ref. No</th>
					 <th>File No</th>
					 <th>Wo No</th>
                    <th>Order Type</th>
                    <th>Presentation</th>
                    <th colspan="2" id="date_td">Transaction Date Range</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('fabricReceiveStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/fabric_issue_to_finish_process_and_service_rcv_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/fabric_issue_to_finish_process_and_service_rcv_report_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );;" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
						 
                        <td>
							<?
                                echo create_drop_down( "cbo_service_source", 120, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/fabric_issue_to_finish_process_and_service_rcv_report_controller',this.value+'**'+$('#cbo_company_name').val(),'load_drop_down_knitting_com','supplier_td' );check_supplier_td(this.value);","","1,3" );
                            ?>
                        </td>
						
						 <td width="120" id="supplier_td">
                                <? 
                                    echo create_drop_down( "cbo_supplier_name", 120, $blank_array,"", 1, "--Select Supplier--", "", "" );
                                ?>
                           </td>
						   
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" /></td>
						<td><input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:70px" /></td>
						<td><input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px" /></td>
						<td><input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" onDblClick="openmypage_wo_no();" placeholder="Wr./Br. Wo No" style="width:70px" />
						 <input type="hidden" name="txt_booking_no" id="txt_booking_no">
						</td>
                        <td width="100">
                         	<? 
								$order_type_arr=array(1=>"-- All --",2=>"With Order",3=>"Without Order");
                                echo create_drop_down( "cbo_order_type", 120, $order_type_arr,"", 0, "", 1, "",0,"" );
                            ?>
                        </td>
                        <td width="100">
                         	<? 
								$presentation_arr=array(1=>"-- All --",2=>"Issue to Process",3=>"Receive from Process");
                                echo create_drop_down( "cbo_presentation", 120, $presentation_arr,"", 0, "", 1, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly>
                        </td>
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" readonly>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                    <tr class="general">
                    	<td colspan="13" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div style="display:none" id="data_panel"></div>   
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_discrepancy').val(0);
</script>
</html>
