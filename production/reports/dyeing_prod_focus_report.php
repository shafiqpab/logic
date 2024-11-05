 <?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Knitting Program Wise Grey Fab Report.
Functionality	:	
JS Functions	:
Created by		:	Tipu
Creation date 	: 	02-02-2019
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
echo load_html_head_contents("Batch Progress Report", "../../", 1, 1,'',1,1);
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
var tableFilters = 
{
	col_0: "none",
	col_operation: {
		id: ["total_batch_qty","total_finish_qty","total_delivery_qty","total_balance_qty"],
   //col: [14,26,27],
   col: [17,28,31,32],
   operation: ["sum","sum","sum","sum"],
   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
	}	
}

function fn_report_generated(type)
{
	

	var batch_no=document.getElementById('txt_batch_no').value;
	var working_company_id=document.getElementById('cbo_working_company').value;
	var txt_date_from=document.getElementById('txt_date_from').value;
	var txt_date_to=document.getElementById('txt_date_to').value;
	
	if(batch_no!="")
	{
		if(working_company_id ==0) 
		{			
			alert("Please Select working company");
			return;			
		}
	}
	else
	{
		if(working_company_id ==0) 
		{			
			alert("Please Select working company");
			return;			
		}
		else if (txt_date_from=='') 
		{
			if( form_validation('txt_date_from*txt_date_to','Form Date*To Date')==false )
			{
				return;
			}
		}
	}
		
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_working_company*cbo_floor_id*txt_machine_id*txt_batch_no*txt_date_from*txt_date_to*exchange_rate*cbo_year_selection',"../../")+'&report_title='+report_title;
	freeze_window(3);
	http.open("POST","requires/dyeing_prod_focus_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_report_generated_reponse;
}
function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		/*var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		var path = '../../';
		document.getElementById('report_container').innerHTML=report_convert_button(path); 

		//setFilterGrid("table_body",-1,tableFilters);

		append_report_checkbox('table_header_1',1);*/

		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
		//setFilterGrid("table_body",-1,tableFilters);
		//append_report_checkbox('table_header_1',1);

		show_msg('3');
		release_freezing();
 	}
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	
	$("#table_body tr:first").hide();
	
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="450px";
	
	$("#table_body tr:first").show();
}

function openmypage_batch()
{
	if(form_validation('cbo_working_company','Company Name')==false)
	{
		return;
	}
	var companyID = $("#cbo_working_company").val();
	//var buyer_name = $("#cbo_buyer_name").val();
	var page_link='requires/dyeing_prod_focus_report_controller.php?action=batch_no_search_popup&companyID='+companyID;
	var title='Batch Number Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var order_no=this.contentDoc.getElementById("hide_order_no").value;
		var order_id=this.contentDoc.getElementById("hide_order_id").value;
		$('#txt_batch_no').val(order_no);
		$('#hide_batch_id').val(order_id);	 
	}
}
function openmypage_job()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var companyID = $("#cbo_company_name").val();
	var buyer_name = $("#cbo_buyer_name").val();
	var cbo_year_id = $("#cbo_year").val();
	//var cbo_month_id = $("#cbo_month").val();
	//alert(cbo_year_id);
	var page_link='requires/dyeing_prod_focus_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
	var title='Job No Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var job_no=this.contentDoc.getElementById("hide_job_no").value;
		var job_id=this.contentDoc.getElementById("hide_job_id").value;
		$('#txt_job_no').val(job_no);
		$('#txt_job_id').val(job_id);	 
	}
}
function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="380px";
}

function fnc_dyeing_popup(date_key,batch_id,company_id,action,type,re_dying_batch)
{ //alert(des_prod)
	var companyID = $("#cbo_working_company").val();
	var exchange_rate = $("#exchange_rate").val();
	var popup_width='1180px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/dyeing_prod_focus_report_controller.php?companyID='+companyID+'&date_key='+date_key+'&batch_id='+batch_id+'&type='+type+'&exchange_rate='+exchange_rate+'&re_dying_batch='+re_dying_batch+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
}
function openmypage_issue(po_id,prog_no,booking_no,action)
{ //alert(des_prod)
	var companyID = $("#cbo_company_name").val();
	var popup_width='580px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/dyeing_prod_focus_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&prog_no='+prog_no+'&booking_no='+booking_no+'&prog_no='+prog_no+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../');
}
function js_set_value( str ) {
	toggle( document.getElementById( 'tr_' + str), '#FFF' );
}

function search_populate(str)
{
	if(str==1)
	{
		document.getElementById('search_by_th_up').innerHTML="Batch Date";
		$('#search_by_th_up').css('color','blue');
	}
	else if(str==2)
	{
		document.getElementById('search_by_th_up').innerHTML="Dyeing Date";
		$('#search_by_th_up').css('color','blue');
	}	
}
 function openmypage_machine()
    {
        if (form_validation('cbo_working_company', 'Company Name') == false)
        {
            return;
        }
        var data = document.getElementById('cbo_working_company').value+'_'+ document.getElementById('cbo_floor_id').value;//+"_"+document.getElementById('cbo_location_id').value
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/dyeing_prod_focus_report_controller.php?action=machine_no_popup&data=' + data, 'Machine Name Popup', 'width=470px,height=420px,center=1,resize=0', '../')

        emailwindow.onclose = function ()
        {
            var theemail = this.contentDoc.getElementById("hid_machine_id");
            var theemailv = this.contentDoc.getElementById("hid_machine_name");
            var response = theemail.value.split('_');
            if (theemail.value != "")
            {
                freeze_window(5);
                document.getElementById("txt_machine_id").value = theemail.value;
                document.getElementById("txt_machine_name").value = theemailv.value;
                release_freezing();
            }
        }
    }
    function check_exchange_rate()
	{
		var cbo_currercy=2;
		var costing_date = $('#hidd_costing_date').val();
		var cbo_company_name = $('#cbo_working_company').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+costing_date+"**"+cbo_company_name, 'check_conversion_rate', '', 'requires/dyeing_prod_focus_report_controller');
		var response=response.split("_");
		$('#exchange_rate').val(response[1]);
	}
    </script>
</head>
<body onLoad="set_hotkey();">
<form id="knittingStatusReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:750px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:750px;">
             <table class="rpt_table" width="750px" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
					<th class="must_entry_caption" width="120">W. Company</th>
                    <th width="80">Batch No</th>
                    <th width="100">Floor </th>
                     <th width="80" >Machine</th>
                     <th width="80">Exchange Rate</th>
                    <th id="search_by_th_up" class="must_entry_caption">Dyeing Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('knittingStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
                </thead>
                <tbody>
                    <tr align="center">
                        
						<td>
                             <?
                                echo create_drop_down("cbo_working_company", 100, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name", "id,company_name", 1, "-- Select Working Company --", 0, "load_drop_down( 'requires/dyeing_prod_focus_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );check_exchange_rate();");
                              ?>
                        </td>
                        <td>
                            <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:70px" placeholder="Browse Or Write" onDblClick="openmypage_batch();" onChange="$('#hide_batch_id').val('');" autocomplete="off">
                            <input type="hidden" name="hide_batch_id" id="hide_batch_id" readonly>
                        </td>	
                        
                         <td id="floor_td">
                                    <? echo create_drop_down("cbo_floor_id", 100, $blank_array, "", 1, "-Select Floor-", 0, "", 1); ?>
                                </td>
                           <td align="center">
                                    <input type="text" name="txt_machine_name" id="txt_machine_name" class="text_boxes" style="width:60px" placeholder="Browse" onDblClick="openmypage_machine()" readonly />
                                    <input type="hidden" name="txt_machine_id" id="txt_machine_id" class="text_boxes" style="width:50px" />
									<? $curret_date=date('d-m-Y');?>
									<input type="hidden" name="hidd_costing_date" id="hidd_costing_date" class="text_boxes" value="<?= $curret_date?>"style="width:50px" />
                          </td>
                          <td>
                            <input type="text"  name="exchange_rate" id="exchange_rate" class="text_boxes_numeric" style="width:60px;" value="" />
                          </td>
                        
                        						
                                            
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px" placeholder="From Date"/>
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px" placeholder="To Date"/>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
