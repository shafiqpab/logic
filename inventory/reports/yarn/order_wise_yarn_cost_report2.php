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
echo load_html_head_contents("Order Wise Yarn Stcok Report", "../../../", 1, 1,'',1,1);

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
var permission = '<? echo $permission; ?>';
 
function fn_report_generated(type)
{
	
	var cbo_date_typeId=$("#cbo_date_type").val();

	if(type==3)
	{
		var action="report_generate_2";
	}
	else
	{
		var action="report_generate";
	}
	
	if(cbo_date_typeId==2)
	{
		if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
		{
			return;
		}
	}

	if (form_validation('cbo_company_name','Comapny Name')==false)
	{
		return;
	}
	else
	{
		if(type==1)
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_search_string*txt_date_from*txt_date_to*cbo_year*txt_job_no*cbo_date_type',"../../../")+ "&type=" + type;
		}
		else if(type==3)
		{
			var data="action=report_generate_2"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_search_string*txt_date_from*txt_date_to*cbo_year*txt_job_no*cbo_date_type',"../../../")+ "&type=" + type;
		}
		else
		{			
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_search_string*txt_date_from*txt_date_to*cbo_year*txt_job_no*cbo_shipping_status*cbo_date_type',"../../../")+ "&type=" + type;
		}
		
		freeze_window(3);
		http.open("POST","requires/order_wise_yarn_cost_report_controller2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
}
	

function fn_report_generated_reponse()
{
 	if(http.readyState == 4) 
	{
  		var response=trim(http.responseText).split("****");
  		var rpt_type = response[2];
		$('#report_container2').html(response[0]);
		document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:155px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>'; 
		
		if(rpt_type==3)
		{
			var coldata = [12,17,18,19,20,21,22,23];
		}
		else
		{
			var coldata = [11,18,19,20,21,22,23,24];
		}
		
		var tableFilters = 
		{
			col_operation: 
			{
				id: ["total_order_qnty","value_tot_mkt_required","value_tot_required_cost","value_tot_booking_qty","value_yarn_iss_qty","value_yarn_iss_cost","value_req_bal_qty","value_cost_bal_cost"],
				col: coldata,
				operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}
	 	setFilterGrid("table_body",-1,tableFilters);
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
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	$("#table_body tr:first").show();
	
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="400px";
}


function generate_worder_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved,action,entryForm)
{
	var data="action="+action+
				'&txt_booking_no='+"'"+booking_no+"'"+
				'&cbo_company_name='+"'"+company_id+"'"+
				'&txt_order_no_id='+"'"+order_id+"'"+
				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
				'&cbo_fabric_source='+"'"+fabric_source+"'"+
				'&id_approved_id='+"'"+approved+"'"+
				'&txt_job_no='+"'"+job_no+"'";
				//alert(action)
	if(type==1)	
	{			
		http.open("POST","../../../order/woven_order/requires/short_fabric_booking_controller.php",true);
	}
	else if(type==2)
	{
		if(entryForm==118)
		{
			http.open("POST","../../../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
		}
		else
		{
			http.open("POST","../../../order/woven_order/requires/fabric_booking_controller.php",true);
		}
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

function openmypage(po_id,type,tittle,company_id)
{
	var popup_width='';
	if(type=="yarn_issue_cost")
	{
		popup_width='990px';
	}
	else
	{
		popup_width='880px';
	}

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_yarn_cost_report_controller2.php?po_id='+po_id+'&action='+type+'&company_id='+company_id, tittle, 'width='+popup_width+', height=420px, center=1, resize=0, scrolling=0', '../../');
}

function date_fill_change(str)
	{
		if (str==1)
		{
			document.getElementById('search_date_td').innerHTML='Ship Date';
		}
		else if(str==2)
		{			
			document.getElementById('search_date_td').innerHTML='Ref. Close Date';
		}
		else
		{
			document.getElementById('search_date_td').innerHTML='Ship Date';
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
         
         <h3 style="width:850px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:840px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th>Date Category</th>
                    <th colspan="2" id="search_date_td">Ship Date</th>
                    <th>Job Year</th>
                    <th>Job No</th>
                    <th>Order No</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('fabricReceiveStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:80px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/order_wise_yarn_cost_report_controller2',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                           
						 
						    ?>
                            
                        </td>
                         	<td>
							<? 
							   $date_type_arr=array(1=>'Ship Date',2=>'Ref. Close Date');
							echo create_drop_down( "cbo_date_type", 100, $date_type_arr, "", 0, "----Select----",1, "date_fill_change(this.value);",0,"" ); 
							?>
                        </td>
                        
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
                        </td>
                        <td>
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date" readonly>
                        </td>
                        <td>
                        	<?
								echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                        </td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write" /></td>
                        <td id="search_by_td"><input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:70px" placeholder="Write" /></td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <table>
            	<tr>
                	<td>
 						<? echo load_month_buttons(1); ?>
                   	</td>
					<td>
						<input type="button" id="show_button" class="formbutton" style="width:80px; margin-left: 59px;" value="Show2" onClick="fn_report_generated(3)" />
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
