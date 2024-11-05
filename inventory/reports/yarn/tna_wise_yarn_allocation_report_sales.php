<?
/*-------------------------------------------- Comments
Purpose			: 	TNA wise yarn allocation details
				
Functionality	:	
JS Functions	:
Created by		:	Md. Nuruzzaman
Creation date 	: 	15.12.2021
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
echo load_html_head_contents("TNA Wise Yarn Allocation Report [Sales]","../../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report(btn)
{
	if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
	{
		return;
	}
	
	var cbo_company_name = $("#cbo_company_name").val();
	var cbo_dyed_type = $("#cbo_dyed_type").val();
	var cbo_yarn_type = $("#cbo_yarn_type").val();
	var txt_count 	= $("#cbo_yarn_count").val();
	var txt_lot_no 	= $("#txt_lot_no").val();
	var from_date 	= $("#txt_date_from").val();
	var to_date 	= $("#txt_date_to").val();
	var value_with 	= $("#cbo_value_with").val();
	var cbo_supplier = $("#cbo_supplier").val();	
	var txt_composition = $("#txt_composition").val();
	var txt_composition_id = $("#txt_composition_id").val();
	
	var txt_job_no = $("#txt_job_no").val();	
	var txt_booking_no = $("#txt_booking_no").val();
	var cbo_date_type = $("#cbo_date_type").val();
	
	var lot_search_type = 0
	if ($('#lot_search_type').is(":checked"))
	{
	   lot_search_type = 1;
	}

	var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_dyed_type="+cbo_dyed_type+"&cbo_yarn_type="+cbo_yarn_type+"&txt_count="+txt_count+"&txt_lot_no="+txt_lot_no+"&from_date="+from_date+"&to_date="+to_date+"&value_with="+value_with+"&cbo_supplier="+cbo_supplier+"&txt_composition="+txt_composition+"&txt_composition_id="+txt_composition_id+"&txt_job_no="+txt_job_no+"&txt_booking_no="+txt_booking_no+"&cbo_date_type="+cbo_date_type+"&lot_search_type="+lot_search_type;
	
	if(btn == 5)
	{
 		var data="action=generate_report"+dataString;
	}
	else
	{
 		var data="action=generate_summary_report"+dataString;
	}

	freeze_window(3);
	http.open("POST","requires/tna_wise_yarn_allocation_report_sales_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;  
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{	 
 		var reponse=trim(http.responseText).split("**");
		$("#report_container2").html(reponse[0]);  
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		
		/*var tableFilters = { 
			col_0: "none", 
			col_operation: {
				id: ["value_total_allocation_qty","value_total_issue_qty","value_total_issue_return_qty","value_total_balance"],
				col: [17,18,19,20],
				operation: ["sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		}
		setFilterGrid("table_body",-1,tableFilters);*/

		setFilterGrid("table_body",-1);
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
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><style></style></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close(); 
	document.getElementById('scroll_body').style.overflow="scroll"; 
	document.getElementById('scroll_body').style.maxHeight="350px";
	$("#table_body tr:first").show();
}

function validate(e)
{
	var key;
	var keychar;
	if (window.event)
		key = window.event.keyCode;
	else if (e)
		key = e.which;
	else
		return true;
	keychar = String.fromCharCode(key);
	// control keys
	if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
	return true;
	// numbers
	else if ((("%").indexOf(keychar) > -1))
		return false;
	else
		return true;
}

$(document).ready(function() 
{
	$('#txt_composition').bind('copy paste cut',function(e) {
		e.preventDefault(); //disable cut,copy,paste
	});
});

function openmypage_composition()
{
	var pre_composition_id = $("#txt_composition_id").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_wise_yarn_allocation_report_sales_controller.php?action=composition_popup&pre_composition_id='+pre_composition_id, 'Composition Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var composition_des=this.contentDoc.getElementById("hidden_composition").value; //Access form field with id="emailfield"
		var composition_id=this.contentDoc.getElementById("hidden_composition_id").value;
		$("#txt_composition").val(composition_des);
		$("#txt_composition_id").val(composition_id);
	}
}

function func_qty_popup(id,action)
{
	//var companyID = $("#cbo_company_name").val();
	var popup_width='610px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_wise_yarn_allocation_report_sales_controller.php?id='+id+'&action='+action, 'Required Qty. Popup', 'width='+popup_width+', height=350px,center=1,resize=1,scrolling=0','../../');
}

function func_allocation_qty_popup(job_no, prod_id, action)
{
	var popup_width='990px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/tna_wise_yarn_allocation_report_sales_controller.php?prod_id='+prod_id+'&job_no='+job_no+'&action='+action, 'Required Qty. Popup', 'width='+popup_width+', height=350px,center=1,resize=1,scrolling=0','../../');
}

function fabric_sales_order_print6(company_id, booking_no, job_no)
{
	var data = company_id + '**' + booking_no + '*' + job_no + '*Fabric Sales Order Entry v2';
	var within_group=2
	if (within_group == 2)
	{
		window.open("../../../production/requires/fabric_sales_order_entry_v2_controller.php?data=" + data + '&action=fabric_sales_order_print6', true);
	}
	else
	{
		alert("This report available for Within Group No");
	}
	return;
}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission);  ?>    		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" > 
    <div style="width:100%;" align="center">
        <h3 style="width:1200px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div style="width:100%;" id="content_search_panel">
            <fieldset style="width:1030px;">
                <table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th>Company</th> 
                            <th>Supplier</th>                               
                            <th>Dyed Type</th>
                            <th>Yarn Type</th>
                            <th>Count</th>
                            <th>Composition</th>
                            <th>Lot<br><input type="checkbox" name="lot_search_type" id="lot_search_type" title="Lot Search start with"></th>
                            <th>FSO No</th>
                            <th>Sales/Booking No</th>
                            <th>Date Type</th>
                            <th class="must_entry_caption" colspan="2">Date</th>
                            <th colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr>
                        <td>
							<? 
                               echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- All Company --", $selected, "load_drop_down( 'requires/tna_wise_yarn_allocation_report_sales_controller', this.value, 'load_drop_down_supplier', 'supplier' );get_php_form_data( this.value, 'eval_multi_select', 'requires/tna_wise_yarn_allocation_report_sales_controller' );" );
                            ?>                            
                        </td>
                        <td id="supplier"> 
							<?
                            	
							echo create_drop_down("cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier c where c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 0, "-- Select --", 0, "", 0);
                            ?>
                        </td>
                        
                        <td align="center">
                            <?   
                                $dyedType=array(0=>'All',1=>'Dyed Yarn',2=>'Non Dyed Yarn');
                                echo create_drop_down( "cbo_dyed_type", 80, $dyedType,"", 0, "--Select--", $selected, "", "","");
                            ?>              
                        </td>
                        <td> 
                            <?
                                echo create_drop_down("cbo_yarn_type",100,$yarn_type,"",0, "-- Select --", $selected, "");
							?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down("cbo_yarn_count",80,"select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count","id,yarn_count",0, "-- Select --", $selected, "");
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_composition" name="txt_composition" class="text_boxes" style="width:100px" value="" onDblClick="openmypage_composition();" placeholder="Browse" readonly />
                            <input type="hidden" id="txt_composition_id" name="txt_composition_id" class="text_boxes" style="width:70px" value=""  />
                        </td>
                        <td>
                            <input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:60px" value="" />
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" placeholder="Write" />
                            <!--<input type="hidden" id="txt_job_id" name="txt_job_id"/>-->
                        </td>
                        <td>
                            <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:100px" placeholder="Write" />
                            <!--<input type="hidden" id="txt_booking_id" name="txt_booking_id"/>-->
                        </td>
                        <td align="center">
                            <?   
							$dateType=array(1=>'Start Date',2=>'Finish Date');
							echo create_drop_down( "cbo_date_type", 80, $dateType, "", 0, "--Select--", 1, "", "","");
                            ?>              
                        </td>
                        <td align="center"> 
                             <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:55px" readonly/>
                          </td>
                        <td align="center">
							<input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:55px" readonly/>
                        </td>
                        <td align="center">
                            <input type="button" name="search" id="search1" value="Show" onClick="generate_report(5)" style="width:60px;display:display;" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11">&nbsp;&nbsp;&nbsp;&nbsp;<? echo load_month_buttons(1); ?></td>
                        <td colspan="2" align="right">
                            <input type="button" name="search2" id="search2" value="Summary" onClick="generate_report(6)" style="width:70px;display:display;" class="formbutton" />
                        </td>
                    </tr>
                </table> 
            </fieldset> 
		</div>
    </div>
    <br /> 
        <!-- Result Contain Start-->       
        	<div id="report_container" align="center"></div>
            <div id="report_container2" align="center" style="margin-left:5px"></div> 
        <!-- Result Contain END-->   
    </form>    
</div>    
</body> 

<script>
	set_multiselect('cbo_yarn_count*cbo_supplier*cbo_yarn_type','0*0*0','0*0*0','','0*0*0');
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$("#cbo_value_with").val(1);
</script> 
</html>
