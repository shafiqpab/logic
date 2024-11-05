<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Receive Status  Without Order Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	01-07-2014
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
echo load_html_head_contents("Fabric Receive Status Without Order Report", "../../", 1, 1,'',1,1);

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
	else
	{
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_sample_type*cbo_wo_year*txt_wo_no*txt_date_from*txt_date_to',"../../");
		freeze_window(3);
		http.open("POST","requires/febric_receive_pord_without_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
}
	

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText).split("####");
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		setFilterGrid("table_body",-1);
		//append_report_checkbox('table_header_1',1);
		// $("input:checkbox").hide();
		show_msg('3');
		release_freezing();
 	}
	
}

function new_window()
{
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	$('#table_body tbody').find('tr:first').hide();
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	$('#table_body tbody').find('tr:first').show();
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="300px";
	
}

function generate_order_report3(booking_no,company_id,is_approved) //Not Use
{
	//alert(print_format);
	/* print_format.split(',');
	//alert(print_format[0]);
	for (var k=0; k<print_format.length; k++)
		{
			if(print_format[k]==34)
			{
			var action_type="show_fabric_booking_report";	
			}
		}*/
				var data="action=show_fabric_booking_report"+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&id_approved_id='+"'"+is_approved+"'";
				
				
				/*var data="action=show_fabric_booking_report"+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&id_approved_id='+"'"+is_approved+"'";*/
	
		
		http.open("POST","../../order/woven_order/requires/sample_booking_non_order_controller.php",true);
	
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_fabric_report_reponse3;
}
	
	
function generate_fabric_report_reponse3()
{
	if(http.readyState == 4) 
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
		d.close();
	}
}


function generate_order_report(booking_no,company_id,is_approved,fabric_nature,type,i)
	{
		//var report_title='Budget Wise Fabric Booking';
		var data="action="+type+
		'&txt_booking_no='+"'"+booking_no+"'"+
		'&cbo_company_name='+"'"+company_id+"'"+
		'&id_approved_id='+"'"+is_approved+"'"+
		'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
		'&path=../../';
		//alert(data);return;
			//var data="action="+show_fabric_booking_report_gr+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../")+'&report_title='+$report_title+'&show_yarn_rate='+show_yarn_rate+'&path=../../';
			
			//$report_title=$( "div.form_caption" ).html();
			
			//var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no*i',"../../")+'&path=../../';
			
			//freeze_window(5);
			//http.open("POST","requires/fabric_booking_controller.php",true);
			http.open("POST","../../order/woven_order/requires/sample_booking_non_order_controller.php",true);
						
					
		/*if(type==1)	
		{			
			http.open("POST","../order/woven_order/requires/short_fabric_booking_controller.php",true);
		}
		else if(action=='show_fabric_booking_report_gr')
		{
			http.open("POST","../order/woven_order/requires/fabric_booking_controller.php",true);
		}
		else
		{
			http.open("POST","../order/woven_order/requires/sample_booking_controller.php",true);
		}*/
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
		   }
			
		}
	}


function openmypage(boking_id,type)
{
	page_link='requires/febric_receive_pord_without_order_controller.php?boking_id='+boking_id+'&action='+type;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail Veiw', 'width=1100px, height=450px, center=1, resize=0, scrolling=0','../');
}


function open_febric_receive_status_color_wise_popup(boking_id,type,color_id)
{
	var widths="";
	if(type=='issue_to_cut')
	{
		widths='800px';
	}
	else
	{
		widths='900px';
	}
	page_link='requires/febric_receive_pord_without_order_controller.php?boking_id='+boking_id+'&action='+type+'&color_id='+color_id;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Detail Veiw', 'width='+widths+', height=450px, center=1, resize=0, scrolling=0','../');
}
</script>

<style>
	hr
	{
		color: #676767;
		background-color: #676767;
		height: 1px;
	}
</style> 
</head>
 
<body onLoad="set_hotkey();">

<form id="fabricReceiveStatusReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
         
         <h3 style="width:900px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:900px;">
             <table class="rpt_table" width="880" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption" width="140">Company Name</th>
                    <th width="140">Buyer Name</th>
                    <th width="100">Sample Type</th>
                    <th width="90">WO Year</th>
                    <th width="100">WO Number</th>
                    <th colspan="2" width="200">WO Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('fabricReceiveStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:90px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/febric_receive_pord_without_order_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td> 
                            <?
                                echo create_drop_down( "cbo_sample_type", 100, "select id,sample_name from lib_sample order by sample_name","id,sample_name", 1, "-- Select --", $selected, "" );
                            ?>
                        </td>
                        <td >
                            <? 
								echo create_drop_down( "cbo_wo_year", 80, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                            ?>
                        </td>
                         <td>
                            <input name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:100px">
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
                        </td>
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date" readonly>
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated()" />
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
            <br />
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
