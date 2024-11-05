<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Grey fabric / finish fabric process Reprot
Functionality	:	
JS Functions	:
Created by		:	Kaiyum
Creation date 	: 	12-06-2019
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
echo load_html_head_contents("Daily Batch Creation Report", "../../", 1, 1,'','','');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

/*var tableFilters = 
{
	col_30: "none",
	col_operation: {
	id: ["td_fab_woqnty_id","td_fab_issue_id","td_fab_recv_id","td_fab_processloss_id","td_fab_balance_id"],
	col: [7,8,9,10,11],
	operation: ["sum","sum","sum","sum","sum"],
	write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
	}
} */
	 	
function fn_report_generated(operation)
{
	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbo_process=document.getElementById('cbo_process').value;
	var cbo_supplier_name=document.getElementById('cbo_supplier_name').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	var txt_date_from=document.getElementById('txt_date_from').value;
	var txt_date_to=document.getElementById('txt_date_to').value;
	var booking_number=document.getElementById('txt_hide_booking_id').value;
	var booking_no=document.getElementById('txt_booking_no').value;
	var order_no=document.getElementById('order_no').value;	
	var hidden_order=document.getElementById('hidden_order_id').value;	
	var cbo_year_selection=document.getElementById('cbo_year_selection').value;	
	
	if (booking_no=="" && order_no=="") 
	{			
		if( form_validation('cbo_company_name*cbo_process*cbo_service_source*txt_date_from*txt_date_to','Company Name*Process*Service Source*Form Date*To Date')==false )
		{
			return;
		}
	}
	else{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
	}
	freeze_window(5);
    var data="action=report_generated&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_supplier_name*cbo_service_source*cbo_process*txt_hide_booking_id*txt_booking_no*order_no*hidden_order_id*cbo_year_selection*txt_date_from*txt_date_to',"../../");
	//alert(data);
		http.open("POST","requires/service_booking_wise_finish_fabric_record_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_show_report_generated;
}
function fnc_show_report_generated()
{
	/*if(http.readyState == 4) 
	{
		// alert(http.responseText);
		document.getElementById('report_container2').innerHTML=http.responseText;
		document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
		setFilterGrid("table_body",-1,tableFilters);
		setFilterGrid("table_body2",-1,tableFilters2);
		setFilterGrid("table_body3",-1,tableFilters3);
		show_msg('3');
		release_freezing();
	}*/
	if(http.readyState == 4) 
	{
		// alert(http.responseText);
		var response=trim(http.responseText).split("####");
		$("#report_container2").html(response[0]);  
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		release_freezing();
		//setFilterGrid("table_body",-1,tableFilters);
 		show_msg('3');
	}
}
function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	$('#table_body tr:first').hide();
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	$('#table_body tr:first').show();
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="330px";
}


function openmypage_booking()
{
    if( form_validation('cbo_company_name','Company Name')==false )
    {
        return;
    }
    var companyID = $("#cbo_company_name").val();
    var buyer_name = $("#cbo_buyer_name").val();
    var cbo_year_selection_id = $("#cbo_year_selection").val();
    //var cbo_month_id = $("#cbo_month").val();
    var page_link='requires/service_booking_wise_finish_fabric_record_report_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_selection_id='+cbo_year_selection_id;
    var title='Booking No Search';
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1010px,height=370px,center=1,resize=1,scrolling=0','../');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
        var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
        $('#txt_booking_no').val(booking_no);
        $('#txt_hide_booking_id').val(booking_id);   
    }
}

function openmypage_order(id)
{ 
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	var company_name = $("#cbo_company_name").val();
    var buyer_name = $("#cbo_buyer_name").val();
    var year = $("#cbo_year_selection").val();


	var page_link="requires/service_booking_wise_finish_fabric_record_report_controller.php?action=order_number_popup&company_name="+company_name+"&buyer_name="+buyer_name+"&year="+year;
	var title="Order Number";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=420px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theemail=this.contentDoc.getElementById("selected_id").value;
		var theemail=theemail.split("__");
		document.getElementById('hidden_order_id').value=theemail[0];
		document.getElementById('order_no').value=theemail[1];
		release_freezing();
	}
}



function toggle( x, origColor ) 
{
	var newColor = 'green';
	if ( x.style ) {
		x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
	}
}
		
function js_set_value( str ) {
	toggle( document.getElementById( 'tr_' + str), '#FFF' );
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
			document.getElementById('supplier_head').innerHTML='Party';
			//$('#supplier_head').css('color','blue');
		}
}
function openmypage_qnty(booking_no,poID,action,fabricDescId,bodyPartId,diaWidth,gmts_color,dtlsId,from_date,to_date,processId)
{
	var companyID = $("#cbo_company_name").val();
	var popup_width='490px';
	var buyerId=$("#cbo_buyer_id").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/service_booking_wise_finish_fabric_record_report_controller.php?companyID='+companyID+'&booking_no='+booking_no+'&poID='+poID+'&action='+action+'&fabricDescId='+fabricDescId+'&bodyPartId='+bodyPartId+'&diaWidth='+diaWidth+'&gmts_color='+gmts_color+'&dtlsId='+dtlsId+'&buyerId='+buyerId+'&from_date='+from_date+'&to_date='+to_date+'&processId='+processId, 'Details Veiw', 'width='+popup_width+', height=250px,center=1,resize=0,scrolling=0','../../');
}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",''); ?>
		 <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1"> 
         <h3 style="width:1140px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
             <fieldset style="width:1140px;">
                <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <th class="must_entry_caption">Company Name</th>
                            <th class="must_entry_caption">Process</th>
                            <th class="must_entry_caption">Service Source</th>
                            <th id="supplier_head">Party Wise</th>
                            <th>Buyer</th>
                            <th>Booking No</th>
                            <th>Order No</th>
                            <th class="must_entry_caption">WO Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
                        </thead>
                        <tbody>
                            <tr>
                                <td> 
                                    <?
                                    echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/service_booking_wise_finish_fabric_record_report_controller',this.value, 'load_drop_down_buyer','cbo_buyer_name_td' );" );
                                    ?>
                                </td>
                                <td> 
                                    <?
                                        echo create_drop_down( "cbo_process", 150, $conversion_cost_head_array,'', 1, '--Select Process--', 0,"",'','1,35,31','','','',3);
                                    ?>
                                </td>
                               	<td>
									<?
                                	echo create_drop_down( "cbo_service_source", 120, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/service_booking_wise_finish_fabric_record_report_controller',this.value+'**'+$('#cbo_company_name').val(),'load_drop_down_knitting_com','supplier_td' );check_supplier_td(this.value);","","1,3" );
                            	?>
                      			</td>
						
						 		<td id="supplier_td">
                                <? 
                                    echo create_drop_down( "cbo_supplier_name", 120, $blank_array,"", 1, "--Select Supplier--", "", "" );
                                ?>
                           		</td>
                                <td id="cbo_buyer_name_td">
                                	<?
										 echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
										  // echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
									?>
                                </td>
                                <td>
                                     <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px" placeholder="Browse Or Write" onDblClick="openmypage_booking();" >
                                <input type="hidden" name="txt_hide_booking_id" id="txt_hide_booking_id" readonly>
                                </td>
                                <td>
                                     <input type="text"  name="order_no" id="order_no" class="text_boxes" style="width:100px;" tabindex="1" placeholder="Write/Browse" onDblClick="openmypage_order()">
                                     <input type="hidden" name="hidden_order_id" id="hidden_order_id">
                                </td>
                                <td align="center">
                                     <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:50px" placeholder="From Date"/>
                                     &nbsp;To&nbsp;
                                     <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:50px" placeholder="To Date"/>
                                </td>
                                <td><input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated()" /></td>
                            </tr>
                        </tbody>
                    </table>
                    <table>
            	<tr>
                	<td colspan="9">
 						<? echo load_month_buttons(1); ?>
                   	</td>
                </tr>
            </table> 
            <br />
                </fieldset>
            </div>
            <div id="report_container"></div>
    		<div id="report_container2"></div>
		</form>
	</div>
    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>