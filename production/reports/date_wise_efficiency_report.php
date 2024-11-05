<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Production Incentive Payment Report
				
Functionality	:	(Copy from daily efficiency report )
JS Functions	:
Created by		:	Helal Uddin
Creation date 	: 	30-11-2020
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

echo load_html_head_contents("Batch Creation Info", "../../", 1, 1,'','','');
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function generate_report(type)
	{
		//console.log('start');
		if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Production Date*Production Date')==false )
		{
			return;
		}
		//console.log('valid');
		var report_title=$( "div.form_caption" ).html();
		var txt_date_from=$("#txt_date_from").val();
		var txt_date_to=$("#txt_date_to").val();

		//console.log('date');
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*hidden_line_id',"../../")+'&txt_date_from='+txt_date_from+'&txt_date_to='+txt_date_to+'&report_title='+report_title;
		
	
		freeze_window(3);
		http.open("POST","requires/date_wise_efficiency_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	function generate_report2(type)
	{
		//console.log('start');
		if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Production Date*Production Date')==false )
		{
			return;
		}
		//console.log('valid');
		var report_title=$( "div.form_caption" ).html();
		var txt_date_from=$("#txt_date_from").val();
		var txt_date_to=$("#txt_date_to").val();

		//console.log('date');
		
		var data="action=report_generate2"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*hidden_line_id',"../../")+'&txt_date_from='+txt_date_from+'&txt_date_to='+txt_date_to+'&report_title='+report_title;
		
	
		freeze_window(3);
		http.open("POST","requires/date_wise_efficiency_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);  
			//alert(reponse[1]);
			document.getElementById('report_container').innerHTML='<a href="##" onclick="fnExportToExcel()" target=_blank; style="text-decoration:none" id="dlink"><input type="button" class="formbutton" value="Export to Excel" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			show_msg('3');
			release_freezing();
			
		}
	} 
function fnExportToExcel()
{
    // $(".fltrow").hide();
    let tableData = document.getElementById("report_container2").innerHTML;
    // alert(tableData);
    let data_type = 'data:application/vnd.ms-excel;base64,',
    template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
    base64 = function (s) {
        return window.btoa(unescape(encodeURIComponent(s)))
    },
    format = function (s, c) {
        return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
    }

    let ctx = {
        worksheet: 'Worksheet',
        table: tableData
    }

    let dt = new Date();
    document.getElementById("dlink").href = data_type + base64(format(template, ctx));
    document.getElementById("dlink").traget = "_blank";
    document.getElementById("dlink").download = dt.getTime()+'_display_board.xls';
    document.getElementById("dlink").click();
    // $(".fltrow").show();
    // alert('ok');
}


	function openmypage(po_information,available_minit,id,line_name)
	{
		popup_width='1050px'; 
		var pre_po_available_min=$("#poWiseAvailableMinutes"+id).val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_efficiency_report_controller.php?po_iinformation='+po_information+'&available_minit='+available_minit+'&pre_po_available_min='+pre_po_available_min+'&action=distribute_available_minit', 'Distribute Used Minutes of line '+line_name, 'width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var available_minute_info=this.contentDoc.getElementById("po_available_minutes").value;
			$("#poWiseAvailableMinutes"+id).val(available_minute_info);
			$("#tr_"+id).css('background-color','#FFD6C1')
			//alert(available_minute_info)FFD6C1
		}
	}
	
	function openmypage_line()
	{
		if( form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var location=$("#cbo_location_id").val();
		var floor_id=$("#cbo_floor_id").val();
		var txt_date=$("#txt_date").val();
		var page_link='requires/date_wise_efficiency_report_controller.php?action=line_search_popup&company='+company+'&location='+location+'&floor_id='+floor_id+'&txt_date='+txt_date; 
		
		var title="Search line Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#cbo_line").val(prodDescription);
			$("#hidden_line_id").val(prodID); 
		}
	}
	
	function check_date_format(current_date,selected_date)
	{
		var fdate=current_date.split('-');
		var new_date_from=fdate[2]+'-'+fdate[1]+'-'+fdate[0];
		
		var tdate=selected_date.split('-');
		var new_date_to=tdate[2]+'-'+tdate[1]+'-'+tdate[0];
		
		var fromDate=new Date(new_date_from);
		
		var toDate=new Date(new_date_to);
		if(fromDate.getTime() <= toDate.getTime())
		{
			$("#txt_date").val("");
			alert("Production Date Must Be Less Than Current Date");
		} 
	}
	
	function new_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none"; 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="auto"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
	 
</script>
<script src="../../Chart.js-master/amcharts/amcharts.js"></script>
<script src="../../Chart.js-master/amcharts/serial.js"></script>
<script src="../../Chart.js-master/amcharts/light.js"></script>
</head>
<body onLoad="set_hotkey();">

<form id="OrderWiseAvailableMinutes_1">
    <div style="width:100%;" align="center">    
    
       <? echo load_freeze_divs ("../../",$permission);  ?>
         
         <div id="content_search_panel" >      
         <fieldset style="width:1100px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="200" class="must_entry_caption">Company</th>
                   
                    <th width="160">Location</th>
                    <th width="140">Floor</th>
                    <th width="140">Line No</th>
            		<th class="must_entry_caption" width="200" colspan="2">Production Date</th>
                    <th width="200"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('OrderWiseAvailableMinutes_1','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general"  bgcolor="">
                       <td>
							<? 
                                echo create_drop_down( "cbo_company_id", 200, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/date_wise_efficiency_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>                            
                        </td>
                        
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 1, "-- Select Location --", "", "" );
                            ?>                            
                        </td>
                        <td id="floor_td">
                            <? 
                                echo create_drop_down( "cbo_floor_id", 130, $blank_array,"", 1, "-- Select Floor --", "", "" );
                            ?>                            
                        </td>
                         <td id="line_td">
                               <input type="text" id="cbo_line"  name="cbo_line"  style="width:140px" class="text_boxes" onDblClick="openmypage_line()" placeholder="Browse"  readonly/>
                               <input type="hidden" id="hidden_line_id" name="hidden_line_id" />
                        </td>
                        <td>
                        	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" >
                        </td>
                         <td >
                
                    		<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date"  >
                    	</td>
                        <td>
                            <!--Not Use --hidden button> --> 
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
							<input type="button" name="search" id="search" value="Show2" onClick="generate_report2(2)" style="width:80px" class="formbutton" />
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
    	</div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
    <br/> <br/>
    
 </form>   
</body>


<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
