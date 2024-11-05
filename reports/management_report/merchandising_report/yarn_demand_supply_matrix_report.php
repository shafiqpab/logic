<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Yarn Requirement Report.
Functionality	:	
JS Functions	:
Created by		:	Shafiq 
Creation date 	: 	4-01-2020
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
echo load_html_head_contents("Yarn Requirement Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  

 	var tableFilters =
	{		
		col_operation: 
		{
			id: ["gr_stock","gr_free_stock","gr_returnable_qty","gr_backlog","gr_today_plan","gr_today_allocation","gr_demand","gr_purc_bklog","gr_today_rcvable","gr_today_rcv","gr_pipe_line","gr_closing_backlog"],
		   	col: [3,4,5,6,7,8,9,10,11,12,13,14],
		   	operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		   	write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function fn_report_generated()
	{
		if(form_validation('cbo_company_name*txt_date','Company Name*Future Alloc Date')==false)
		{
			return;
		}
		else
		{	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_date*cbo_without_zero',"../../../");
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/yarn_demand_supply_matrix_report_controller.php",true);
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
			$('#report_container2').html(response[0]);
			//alert(response[1]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>';
			setFilterGrid("table_body",-1,tableFilters); 
			// document.getElementById('content_search_panel').style='display:none';
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function open_popup(action,title,company_id,comp_id,count_id,future_date,height,width)
	{
		var page_link='requires/yarn_demand_supply_matrix_report_controller.php?action='+action+'&company_id='+company_id+'&comp_id='+comp_id+'&count_id='+count_id+'&future_date='+future_date;		
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+width+',height='+height+',center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		{
		 
		}
	}


	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		//$('#scroll_body tr:last').hide();
		$(".flt").css("display","none");
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
		//$('#scroll_body tr:last').show();
		$(".flt").css("display","block");
		
	}
	
	function new_window2(comp_div, container_div)
	{
		document.getElementById(comp_div).style.visibility="visible";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById(container_div).innerHTML+'</body</html>');
		document.getElementById(comp_div).style.visibility="hidden";
		d.close();
	}
	
</script>
<style type="text/css">
	.datepicker_ {
	    height: 18px;
	    font-size: 11px;
	    line-height: 16px;
	    padding: 0 5px;
	    text-align: left;
	    border: 1px solid #676767;
	    border-radius: 3px;
	    border-radius: .5em;
	}
</style>
</head>
<body onLoad="set_hotkey();">	 
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:350px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:350px;">
                <table class="rpt_table" width="350" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th class="must_entry_caption">Company Name</th>
                            <th class="must_entry_caption">Future Alloc Date </th>
                            <th >Show without Zero</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yarn_demand_supply_matrix_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td><input type="text" name="txt_date" id="txt_date" class="datepicker_" style="width:70px" placeholder="Date" ></td>
                        <td>
							<?
								$without_zero=array(1=>"Opening Backlog [Allocation]",2=>"Yarn Demand",3=>"Receivable",4=>"Today Allocation Plan",5=>"Today Allocation",6=>"Purchase Backlog",7=>"Today Purchase Plan",8=>"Today Yarn Rcvd",9=>"Returnable For SMN");
                                echo create_drop_down( "cbo_without_zero", 150, $without_zero,"", 1, "-- Select --", $selected, "" );
                            ?>
						</td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div style="padding:10px 0;" id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		$('#txt_date').datepicker({
			dateFormat: 'dd-mm-yy',
		    minDate: 1
		});
	});
</script>
</html>
