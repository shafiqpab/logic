<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Cost Color Size Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	13-06-2021
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
echo load_html_head_contents("Cost Breakdown Report","../../../", 1, 1, $unicode,1,1);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	var tableFilters = 
	 {
		col_0: "none",
		col_operation: {
		id: ["total_order_qnty_in_pcs","total_order_qnty_needtocut"],
	    col: [22,23],
	    operation: ["sum","sum"],
	    write_method: ["innerHTML","innerHTML"]
		}
	 }
	
	function fn_report_generated()
	{
		
	 
		var date_from = $("#txt_date_from").val();
		var date_to = $("#txt_date_to").val();
		var job_no = $("#txt_job_no").val();
	
		 if(job_no!="")
		 {
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		 }
		else
		{
			 
			if(form_validation('txt_date_from*txt_date_to','From date Fill*To date Fill')==false)
			{
				return;
			}
		}
				
			var report_title=$( "div.form_caption" ).html();	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*hidden_job_id*txt_date_to*txt_date_from',"../../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/color_size_details_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		 
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			append_report_checkbox('table_header_1',1);
			
			setFilterGrid("table_body",-1,tableFilters);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',400,700 );
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function openmypage(po_id,po_qnty,po_no,job_no,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/color_size_details_report_controller.php?po_id='+po_id+'&po_qnty='+po_qnty+'&po_no='+po_no+'&job_no='+job_no+'&action='+type, tittle, 'width=650px, height=350px, center=1, resize=0, scrolling=0', '../../');
	}
		
	function generate_pre_cost_report(po_id,job_no,company_id,buyer_id,style_ref,costing_date)
	{
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true) zero_val="1"; else zero_val="0";
		
		var data="&action=preCostRpt"+
		'&txt_po_breack_down_id='+"'"+po_id+"'"+
		'&txt_job_no='+"'"+job_no+"'"+
		'&cbo_company_name='+"'"+company_id+"'"+
		'&txt_style_ref='+"'"+style_ref+"'"+
		'&txt_costing_date='+"'"+costing_date+"'"+
		'&zero_value='+zero_val+
		'&cbo_buyer_name='+"'"+buyer_id+"'";
		http.open("POST","../../../order/woven_order/requires/color_size_details_report_controller.php",true);
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

	function openmypage_job()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else 
		{	
			var data = $("#cbo_company_name").val()+"_"+$("#cbo_buyer_name").val()+"_"+$("#cbo_year").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/color_size_details_report_controller.php?data='+data+'&action=job_no_popup', 'Job No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theemailjob=this.contentDoc.getElementById("txt_job_no").value;
				var hidden_job_id=this.contentDoc.getElementById("hidden_job_id").value;
				//var response=theemailid.value.split('_');
				if ( theemailjob!="" )
				{
					freeze_window(5);
					$("#txt_job_no").val(theemailjob);
					$("#hidden_job_id").val(hidden_job_id);
					 
					release_freezing();
				}
			}
		}
	}
</script>

</head>
<body onLoad="set_hotkey();">
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:700px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:700px;">
                <table class="rpt_table" width="700" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th width="150" class="must_entry_caption">LC Company</th>
                            <th width="120">Buyer Name</th>
                            <th width="60">Year</th>
                            <th width="100" class="must_entry_caption">Job No.</th>
                             <th colspan="2" id="search_by_th_up" class="must_entry_caption">PO receive date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/color_size_details_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                            <td id="buyer_td"> <? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                            <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", $selected, "",0,"" ); ?></td>

                            <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" placeholder="Write/Browse" onDblClick="openmypage_job();" /><input type="hidden" name="hidden_job_id" id="hidden_job_id" class="text_boxes" style="width:100px"  />
                            </td>
                         <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:45px" placeholder="From Date" >
                        </td>
                        <td>
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:45px"  placeholder="To Date" >
                        </td>
                        
                             
                            <td><input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated()" /></td>
                        </tr>
                    </tbody>
                    <tfoot>
                    	<tr>
                            <td colspan="11" align="center">
                                <? //echo load_month_buttons(1); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div style="display:none" id="data_panel"></div>  
 </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
