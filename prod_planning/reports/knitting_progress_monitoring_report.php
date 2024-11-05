<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Knitting Status Report.
Functionality	:	
JS Functions	:
Created by		:	Tajik 
Creation date 	: 	18-01-2018
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
echo load_html_head_contents("Knitting Progress Monitoring Report", "../../", 1, 1,'',1,1);
?>	

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
 
function fn_report_generated(type)
{
	if (form_validation('cbo_company_name','Comapny Name')==false)
	{
		return;	
	}

	freeze_window(3);

    if(type==2){var actions="report_generate2";}
    else{var actions="report_generate";}
	
	var data="action="+actions+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*txt_order_no*hide_order_id*txt_booking_no*txt_booking_id*cbo_knitting_status*cbo_shipment_status*txt_date*cbo_based_on*txt_date_from*txt_date_to',"../../")+'&type='+type;

	//alert(data);
	http.open("POST","requires/knitting_progress_monitoring_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText);
		$('#report_container2').html(response);
		document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
		append_report_checkbox('table_header_1',1);
		setFilterGrid('tbl_list_search',-1);
		show_msg('3');
		release_freezing();
 	}	
}

function openmypage_order()
{
	if(form_validation('cbo_company_name','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_name").val();
	var page_link='requires/knitting_progress_monitoring_report_controller.php?action=order_no_search_popup&companyID='+companyID;
	var title='Order No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var order_no=this.contentDoc.getElementById("hide_order_no").value;
		var order_id=this.contentDoc.getElementById("hide_order_id").value;
		
		$('#txt_order_no').val(order_no);
		$('#hide_order_id').val(order_id);	 
	}
}

function openmypage_booking()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value;
	//alert (data);
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/knitting_progress_monitoring_report_controller.php?action=booking_no_search_popup&data='+data,'Booking No Popup', 'width=1050px,height=420px,center=1,resize=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var booking_id=this.contentDoc.getElementById("selected_booking").value;
		var booking_no=this.contentDoc.getElementById("selected_booking_no").value;
		
		$('#txt_booking_id').val(booking_id);
		$('#txt_booking_no').val(booking_no);
	}
}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="knittingProgressMonitoringReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1250px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1250px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <!-- <th>Knitting Source</th> -->
                    <th>Buyer Name</th>
                    <!-- <th>Party Name</th> -->
                    <th>Job Year</th>
                    <th>Job No</th>
                    <th>Order No</th>
                    <th>Booking No</th>
                    <!-- <th>Program No</th> -->
                    <th>Program Status</th>
                    <th>Production Date</th>
                    <th>Shipment Status</th>
                    <th>Based On</th>
                    <th>Date Range</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingProgressMonitoringReport_1','report_container*report_container2','','','')" class="formbutton" style="width:60px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "- Select Company -", $selected, "load_drop_down( 'requires/knitting_progress_monitoring_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <!-- <td>
                        	<?
                        		//echo create_drop_down( "cbo_knitting_source", 97, $knitting_source,"",1, "- All -", "","load_drop_down( 'requires/knitting_progress_monitoring_report_controller',this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_party_type', 'party_type_td' );",0,'','','','2');
                        	?>
                        </td> -->
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );
                            ?>
                        </td> 
                        <!-- <td id="party_type_td">
                        	<?
								//echo create_drop_down( "cbo_party_type", 110, $blank_array,"",1, "--Select--", "",'',1 );
							?>
                        </td>  -->
                        <td>
                        	<?
								echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                        </td>
                        <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:60px" placeholder=" Write" autocomplete="off">
                        </td>
                        <td>
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:90px" placeholder="Browse" onDblClick="openmypage_order();" onChange="$('#hide_order_id').val('');" autocomplete="off" readonly>
                            <input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
                        </td>
                         <td>
                             <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes_numeric" style="width:90px" onDblClick="openmypage_booking();" placeholder="Browse Booking" readonly />
                        	 <input type="hidden" id="txt_booking_id" name="txt_booking_id"/>
                        </td>
                        <!-- <td>
                            <input name="txt_program_no" id="txt_program_no" class="text_boxes_numeric" style="width:60px">
                        </td> -->
                        <td align="center">
                            <? 
                                echo create_drop_down( "cbo_knitting_status", 110, $knitting_program_status,"", 0, "- Select -", $selected, "",0,"" );
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date" id="txt_date" value="<? echo date("d-m-Y"); ?>" class="datepicker" style="width:60px"/>
                        </td>
                        <td>
                        	<?
                            $ship_status_arr = array(1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment/Closed"); 
                            echo create_drop_down( "cbo_shipment_status", 97, $ship_status_arr,"", 1,"-All-","", "",0,"" );
								//echo create_drop_down( "cbo_shipment_status", 97, $shipment_status,"",0, "", 1,'',0,'','','','');
							?>
                        </td>
                        <td>
                            <?
                                $based_on_arr=array(1=>"Plan Date",2=>"Program Date");
                                echo create_drop_down( "cbo_based_on", 97, $based_on_arr,"",0, "", "",'',0 );
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                            &nbsp;
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                        </td>
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:60px;" value="Show" onClick="fn_report_generated(1)" />                        	
                        </td>   
                    </tr>
                    <tr>
                        <td colspan="12" align="center">
						<? echo load_month_buttons(1); ?>
                        <input type="button" id="show_button" class="formbutton" style="width:60px;" value="Show 2" onClick="fn_report_generated(2)" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>  
</body>
<script>
	set_multiselect('cbo_knitting_status','0','0','','');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
