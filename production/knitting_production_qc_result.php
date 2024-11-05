<?
/*-------------------------------------------- Comments
Purpose			: 	This form created qc barcode  Report
				
Functionality	:	
JS Functions	:
Created by		:	Md Didarul Alam 
Creation date 	: 	19/03/2018
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Knitting Production QC Result","../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


function grey_receive_popup()
{
	var cbo_company_id = $('#cbo_company_id').val();
	
	if (form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	else
	{ 	
		var page_link='requires/knitting_production_qc_result_controller.php?cbo_company_id='+cbo_company_id+'&action=grey_receive_popup_search';
		var title='Grey Production Form';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var grey_recv_id=this.contentDoc.getElementById("hidden_recv_id").value;
			if(trim(grey_recv_id)!="")
			{
				freeze_window(5);
				get_php_form_data(grey_recv_id, "populate_data_from_grey_recv", "requires/knitting_production_qc_result_controller" );
				var txt_service_booking = $('#txt_service_booking').val();

				release_freezing();
			}
		}
	}
}
	
function reset_field()
{
	reset_form('item_receive_issue_1','report_container2','','','','');
}


function  generate_report(rptType)
{
	var cbo_search_category = $('#cbo_string_search_type').val();
	var cbo_company_id = $("#cbo_company_id").val();
	var txt_booking_no = $("#txt_booking_no").val();
	var cbo_year = $('#cbo_year_selection').val();
	var txt_date_from = $('#txt_date_from').val();
	var txt_date_to = $('#txt_date_to').val();
	var txt_barcode = $("#txt_barcode").val();
	var txt_recieved_number = $("#txt_recieved_id").val();
	
    if( form_validation('cbo_company_id','Company')==false )
    {
        return;
    }

	var dataString = "&cbo_company_id="+cbo_company_id+"&cbo_search_category="+cbo_search_category+"&txt_booking_no="+txt_booking_no+"&cbo_year="+cbo_year+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&txt_barcode="+txt_barcode+"&txt_recieved_number="+txt_recieved_number;


	var data="action=generate_report"+dataString;
	freeze_window(5);
	http.open("POST","requires/knitting_production_qc_result_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse; 
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{
		//alert(http.responseText);	 
		var reponse=trim(http.responseText).split("**");
		//alert(reponse[2]);
		$("#report_container2").html(reponse[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		release_freezing();
		show_msg('3');
	}
} 


function fn_knit_defect(prod_dtls_id,barcode_no)
{
	var roll_maintained=$('#roll_maintained').val();
	//alert(roll_maintained);

	var company_id=$('#cbo_company_id').val();
	if(prod_dtls_id=="")
	{
		alert("Sorry !!.");return;
	}
	else
	{
		var title = 'Knitting Defect Info';	

		var page_link='requires/knitting_production_qc_result_controller.php?update_dtls_id='+prod_dtls_id+'&roll_maintained='+roll_maintained+'&company_id='+company_id+'&barcode_no='+barcode_no+'&action=knit_defect_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=500px,center=1,resize=1,scrolling=0','');

		emailwindow.onclose=function()
		{
			
		}
	}
}


function new_window()
{
	$('#scroll_body tr:first').hide();
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	
	d.close(); 
	$('#scroll_body tr:first').show();
}


</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
   <? echo load_freeze_divs ("../",$permission); ?>
   <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
   <h3 align="left" id="accordion_h1" style="width:900px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    
    <div style="width:900px;" align="center" id="content_search_panel">
        <fieldset style="width:100%;">
                <table class="rpt_table" cellpadding="0" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="must_entry_caption">Company</th>
                        <th>Barcode</th>  
                        <th>Searching Pattern</th> 
                        <th>Booking No</th>
                        <th>Production ID</th>
                        <th colspan="2" width="200">Production Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_field()" /></th>
                    </tr>

                </thead>
                <tr class="general">
                    <td>
                        <?
                        	echo create_drop_down( "cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "get_php_form_data( this.value,'roll_maintained' ,'requires/knitting_production_qc_result_controller');" );
                        ?>  
                        <input type="hidden" name="roll_maintained" id="roll_maintained" value="">                        
                    </td>

                    <td>
						<input type="text" name="txt_barcode" id="txt_barcode" class="text_boxes" style="width:100px" onDblClick="openmypage_barcode()" placeholder="Write/Scan/Browse">
					</td>

                    <td>
                      <?
                       echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" );
                      ?>
                    </td>

                    <td>
						<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px">
					</td>

                    <td>
						<input type="hidden" name="update_id" id="update_id" />
						<input type="text" name="txt_recieved_id" id="txt_recieved_id" class="text_boxes" style="width:100px" placeholder="Double Click" onDblClick="grey_receive_popup();" >
					</td>

					<td colspan="2">
	                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
	                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                    </td> 

                    <td>
                        <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                    </td>

                </tr>

                <tr>
                    <td  align="center" height="40" valign="middle" colspan="8">
                    <? 
                    echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                    echo load_month_buttons();  
                    ?>
                    </td>
                </tr>

            </table> 
        </fieldset> 
           
    </div>
        <!-- Result Contain Start-->
        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div> 
        <!-- Result Contain END-->
    </form>    
</div>    
</body>
<script>
function openmypage_barcode()
	{ 
		var company_id=$('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/knitting_production_qc_result_controller.php?company_id='+company_id+'&action=barcode_popup','Barcode Popup', 'width=1240px,height=380px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos
			
			if(barcode_nos!="")
			{
				$("#txt_barcode").val(barcode_nos);
				generate_report(1);
			}
		}
	}
	
$('#txt_barcode').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			 var txt_barcode= $("#txt_barcode").val();
			 if(txt_barcode){
			generate_report(1);
			 }
		}
	});
	
/*$("#txt_barcode").keyup(function(e){
  if (e.which==13) { // 13 is the code for return
  }
  else {
	 var txt_barcode= $("#txt_barcode").val();
	 if(txt_barcode){
     generate_report(1);
	 }
  }
  //e.preventDefault();
});*/
</script>
<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
