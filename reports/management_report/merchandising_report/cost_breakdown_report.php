<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Cost Breakdown Report.
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	10-02-2013
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
		col_33: "none",
		col_operation: {
		id: ["total_order_qnty","total_order_qnty_in_pcs","value_tot_cm_cost","value_tot_cost","value_order","value_margin","value_tot_trims_cost","value_tot_embell_cost"],
	    col: [11,13,27,29,31,32,33,34],
	    operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
	    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }
	
	function fn_report_generated()
	{
		if(form_validation('cbo_company_name','Company Name')==false)//*txt_date_from*txt_date_to*From Date*To Date
		{
			return;
		}
		else
		{	
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*txt_date_from*txt_date_to*cbo_team_name*cbo_team_member*txt_ref_no*txt_file_no',"../../../");
			freeze_window(3);
			http.open("POST","requires/cost_breakdown_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			/*var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); */

			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			append_report_checkbox('table_header_1',1);
			
			setFilterGrid("table_body",-1,tableFilters);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',400,700 );
	 		show_msg('3');
			release_freezing();
		}
	}



	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$('#scroll_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	
	function openmypage(po_id,po_qnty,po_no,job_no,type,tittle)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/cost_breakdown_report_controller.php?po_id='+po_id+'&po_qnty='+po_qnty+'&po_no='+po_no+'&job_no='+job_no+'&action='+type, tittle, 'width=650px, height=350px, center=1, resize=0, scrolling=0', '../../');
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
		http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
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
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/cost_breakdown_report_controller.php?data='+data+'&action=job_no_popup', 'Job No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theemailjob=this.contentDoc.getElementById("txt_job_no").value;
				//var response=theemailid.value.split('_');
				if ( theemailjob!="" )
				{
					freeze_window(5);
					$("#txt_job_no").val(theemailjob);
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
         <h3 align="left" id="accordion_h1" style="width:930px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:930px;">
                <table class="rpt_table" width="930" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th width="140" class="must_entry_caption">Company Name</th>
                            <th width="120">Buyer Name</th>
                            <th width="60">Job Year</th>
                            <th width="80">Job No.</th>
                            <th width="60">File No</th>
                            <th width="60">Ref. No</th>
                            <th width="100">Team</th>
                        	<th width="110">Team Member</th>
                            <th width="110" colspan="2">Shipment Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td><? echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cost_breakdown_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                            <td id="buyer_td"> <? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                            <td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", $selected, "",0,"" ); ?></td>
                            <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write/Browse" onDblClick="openmypage_job();" /></td>
                            <td><input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:50px"  placeholder="Write File"  /></td>
                            <td><input type="text" id="txt_ref_no" name="txt_ref_no" class="text_boxes" style="width:50px"  placeholder="Write Ref"  /></td>
                            <td><? echo create_drop_down( "cbo_team_name", 100, "select id,team_name from lib_marketing_team where status_active=1 and is_deleted=0 order by team_name","id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( 'requires/cost_breakdown_report_controller', this.value, 'load_drop_down_team_member', 'team_td' )" ); ?></td>
                            <td id="team_td"><? echo create_drop_down( "cbo_team_member", 110, $blank_array,"", 1, "- Team Member- ", $selected, "" ); ?></td>
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" ></td>
                            <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" ></td>
                            <td><input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated()" /></td>
                        </tr>
                    </tbody>
                    <tfoot>
                    	<tr>
                            <td colspan="11" align="center">
                                <? echo load_month_buttons(1); ?>
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
