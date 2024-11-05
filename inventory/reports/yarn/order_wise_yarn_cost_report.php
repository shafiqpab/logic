<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Receive Status Report 2.
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	15-01-2015
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
echo load_html_head_contents("Fabric Receive Status Report 2", "../../../", 1, 1,'',1,1);

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
var permission = '<? echo $permission; ?>';
 
function fn_report_generated(type)
{
	
	if (form_validation('cbo_company_name','Comapny Name')==false)
	{
		return;
	}
	else
	{
		if(type==1)
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_type*cbo_company_name*cbo_buyer_name*txt_search_string*txt_date_from*txt_date_to*txt_date_from_po*txt_date_to_po*txt_date_from_rec*txt_date_to_rec*txt_job_no*cbo_year',"../../../");
		}
		
		
		freeze_window(3);
		http.open("POST","requires/order_wise_yarn_cost_report_controller.php",true);
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
		//alert(response[3]);
		$('#report_container2').html(response[0]);
		//document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>'; 
		//$('#report_container').append('&nbsp;&nbsp;&nbsp;<a href="'+response[2]+'" style="text-decoration:none"><input type="button" value="Convert To Excel Short" name="excel" id="excel" class="formbutton" style="width:155px"/></a>');

			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window2()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		var tot_rows=$('#table_body tr').length;
		if(tot_rows>1)
		{
			var type=$('#cbo_type').val();
			if(type==1)
			{
			 	var tableFilters = {
			  //col_10:'none',
			 // display_all_text: " ---Show All---",
						col_operation: {
						id: ["value_tot_order_qnty","value_tot_mkt_required","value_tot_mkt_cost","value_tot_fabric_req","value_tot_yarn_issue","value_tot_yarn_balance"],
						
					   col: [8,16,17,18,19,20],
					   operation: ["sum","sum","sum","sum","sum","sum"],
					   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
			}
			
			setFilterGrid("table_body",-1,tableFilters);
		 }
		//append_report_checkbox('table_header_1',1);
		// $("input:checkbox").hide();
		show_msg('3');
		release_freezing();
 	}
	
}
function new_window2()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
	}
function new_window()
{
	document.getElementById('company_id_td').style.visibility='visible';
	document.getElementById('date_td').style.visibility='visible';
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write(document.getElementById('buyer_summary').innerHTML);
	document.getElementById('company_id_td').style.visibility='hidden';
	document.getElementById('date_td').style.visibility='hidden';
	d.close();
}

function show_inner_filter(e)
{
	if (e!=13) {var unicode=e.keyCode? e.keyCode : e.charCode } else {unicode=13;}
	if (unicode==13 )
	{
		fn_report_generated(2);
	}
}
 	
function search_by(val)
{
	$('#txt_search_string').val('');
	if(val==1)
	{
		$('#search_by_td_up').html('Order No');
	}
	else
	{
		$('#search_by_td_up').html('Style Ref.');
	}
}

function open_febric_receive_status_order_wise_popup(order_id,type,color)
{
	var popup_width='';
	if(type=="fabric_receive" || type=="fabric_purchase" || type=="grey_issue" || type=="dye_qnty") 
	{
		popup_width='900px';
	}
	else if(type=="grey_receive" || type=="grey_purchase")
	{
		popup_width='1050px';	
	}
	else popup_width='760px';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_yarn_cost_report_controller.php?order_id='+order_id+'&action='+type+'&color='+color, 'Detail Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
}

function openmypage(order_id,type)
{
	var popup_width='';
	if(type=="yarn_issue_not") 
	{
		popup_width='1000px';
	}
	else
	{
		popup_width='1290px';
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_yarn_cost_report_controller.php?order_id='+order_id+'&action='+type, 'Detail Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
}

function generate_worder_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved)
{
	var data="action=show_fabric_booking_report"+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&txt_order_no_id='+"'"+order_id+"'"+
				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
				'&cbo_fabric_source='+"'"+fabric_source+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&txt_job_no='+"'"+job_no+"'";
	if(type==1)	
	{			
		http.open("POST","../../../order/woven_order/requires/short_fabric_booking_controller.php",true);
	}
	else if(type==2)
	{
		http.open("POST","../../../order/woven_order/requires/fabric_booking_controller.php",true);
	}
	else
	{
		http.open("POST","../../../order/woven_order/requires/sample_booking_controller.php",true);
	}
	
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_fabric_report_reponse;
}

function generate_fabric_report_reponse()
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

function generate_pre_cost_report(type,job_no,company_id,buyer_id,style_ref,costing_date)
{
	var data="action="+type+
				'&txt_job_no='+"'"+job_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&cbo_buyer_name='+"'"+buyer_id+"'"+
				'&txt_style_ref='+"'"+style_ref+"'"+
				'&txt_costing_date='+"'"+costing_date+"'"+
				"&zero_value=1"+
				'&path=../../../';
				
	http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_generate_report_reponse;
}

function fnc_generate_report_reponse()
{
	if(http.readyState == 4) 
	{
		$('#data_panel').html( http.responseText );
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}

/*function progress_comment_popup(job_no,po_id,template_id,tna_process_type)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', '../../reports/management_report/merchandising_report/requires/shipment_date_wise_wp_report_controller.php?job_no='+job_no+'&po_id='+po_id+'&template_id='+template_id+'&tna_process_type='+tna_process_type+'&action=update_tna_progress_comment'+'&permission='+permission, "TNA Progress Comment", 'width=1030px,height=390px,center=1,resize=1,scrolling=0','../');
}*/


function progress_comment_popup(job_no,po_id,template_id,tna_process_type)
{
	var data="action=update_tna_progress_comment"+
							'&job_no='+"'"+job_no+"'"+
							'&po_id='+"'"+po_id+"'"+
							'&template_id='+"'"+template_id+"'"+
							'&tna_process_type='+"'"+tna_process_type+"'"+
							'&permission='+"'"+permission+"'";	
							
	http.open("POST","../../../reports/management_report/merchandising_report/requires/shipment_date_wise_wp_report_controller.php",true);
	
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_progress_comment_reponse;	
}

function generate_progress_comment_reponse()
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
    
        <? echo load_freeze_divs ("../../../",'');  ?>
         
         <h3 style="width:1150px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1140px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th colspan="2">Shipment Date</th>
                    <th colspan="2">PO Insert Date</th>
                    <th colspan="2">PO Receive Date</th>
                    <th>Job Year</th>
                    <th>Job No</th>
                    <th>Type</th>
                    <th id="search_by_td_up">Order No</th>
                   
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('fabricReceiveStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:70px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/order_wise_yarn_cost_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly>
                        </td>
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" readonly>
                        </td>
                        <td>
                            <input name="txt_date_from_po" id="txt_date_from_po" class="datepicker" style="width:60px" placeholder="From Date" readonly>
                        </td> 
                        <td>
                            <input name="txt_date_to_po" id="txt_date_to_po" class="datepicker" style="width:60px"  placeholder="To Date" readonly>
                        </td>
                        <td>
                            <input name="txt_date_from_po" id="txt_date_from_rec" class="datepicker" style="width:60px" placeholder="From Date" readonly>
                        </td> 
                        <td>
                            <input name="txt_date_to_po" id="txt_date_to_rec" class="datepicker" style="width:60px"  placeholder="To Date" readonly>
                        </td>
                        <td>
                        	<?
								echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                        </td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" /></td>
                        <td>
                        	<?
								$search_by_arr=array(1=>"Order Wise");
								echo create_drop_down( "cbo_type",80, $search_by_arr,"",0, "", "",'search_by(this.value)',0 );
							?>
                        </td> 
                        <td id="search_by_td"><input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:70px" /></td>
                       
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1)" />
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_discrepancy').val(0);
</script>
</html>
