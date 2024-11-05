<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Fabric Followup Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	18-06-2020
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
echo load_html_head_contents("Fabric Followup Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';
	

	
	function fn_report_generated(type)
	{
		//alert(type);return;
		var txt_style_ref = $("#txt_style_ref").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_job = $("#txt_job").val();
	
		
		if(txt_style_ref!="" || txt_job!="")
		{
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}
		else
		{
				
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Shipment Form Date*Shipment To Date')==false)
				{
					return;
				}
		}
		var report_title=$("div.form_caption" ).html();	
		
		var data="action=fabric_report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_style_ref*txt_style_ref_id*txt_date_from*txt_date_to*txt_job*cbo_season_id',"../../../")+'&report_title='+report_title;
		
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/fabric_followup_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var reponse=trim(http.responseText).split("**");
			//alert(reponse[3]);
			//console.log(reponse);
			$('#report_container2').html(reponse[0]);
			//var tot_rows=$('#table_body tr').length;
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[3]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>'; 
			/*document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';*/
			/*if(tot_rows>1)
			{*/
			//alert(reponse[2]+'='+reponse[3]);
			
				if(reponse[2]==1)
				{
					var tableFilters = {
						/*col_operation: {
						id: ["td_order_qty","td_yarn_req_qty","td_yarn_issIn_qty","td_yarn_issOut_qty","td_yarn_trnsInq_qty","td_yarn_trnsOut_qty","td_yarn_issue_qty","td_yarn_undOvr_qty","td_grey_req_qty","td_grey_in_qty","td_grey_out_qty","td_grey_trnsIn_qty","td_grey_transOut_qty","td_grey_qty","td_grey_rec_qty","td_grey_prLoss_qty","td_grey_undOver_qty","td_grey_issDye_qty","td_grey_lftOver_qty","td_fin_req_qty","td_fin_in_qty","td_fin_out_qty","td_fin_transIn_qty","td_fin_transOut_qty","td_fin_qty","td_fin_prLoss_qty","td_fin_undOver_qty","td_fin_issCut_qty","td_fin_lftOver_qty","td_wovenReqQty","td_wovenRecQty","td_wovenRecBalQty","td_wovenIssueQty","td_wovenIssueBalQty","td_gmt_qty","td_cutting_qty","td_printIssIn_qty","td_printIssOut_qty","td_printIssue_qty","td_printRcvIn_qty","td_printRcvOut_qty","td_printRcv_qty","td_printRjt_qty","td_sewInInput_qty","td_sewInOutput_qty","td_sewIn_qty","td_sewInBal_qty",	"td_sewRcvIn_qty","td_sewRcvOut_qty","td_sewRcv_qty","td_sewRcvBal_qty","td_sewRcvRjt_qty","td_washRcvIn_qty","td_washRcvOut_qty","td_washRcv_qty","td_washRcvBal_qty","td_gmtFinIn_qty","td_gmtFinOut_qty","td_gmtFin_qty","td_gmtFinBal_qty","td_gmtFinRjt_qty","td_gmtrej_qty","td_gmtEx_qty","td_gmtFinLeftOver_qty","td_shortExcess_exFactory_qty","td_prLoss_qty","td_prLossDye_qty","td_prLossCut_qty"],
						col: [6,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,  56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76],
						operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]*/
						}
					}
					setFilterGrid("table_body",-1);
				}
				
			
			
	 		show_msg('3');
		}
	
	
	function new_window(type)
	{
		
		//alert(type);
		
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(type!=3)
		{
			$('#table_body tr:first').hide();
			$('#table_body2 tr:first').hide();
		}
		else
		{
			$('#table_body3 tr:first').hide();
		}
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		if(type!=3)
		{
			$('#table_body tr:first').show();
			$('#table_body2 tr:first').show();
		}
		else
		{
			$('#table_body3 tr:first').hide();
		}
		
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	function new_window2()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		$('#table_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	
	function openmypage_style()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var page_link='requires/fabric_followup_report_controller.php?action=style_refarence_surch&companyID='+company+'&buyer_name='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year_id='+cbo_year;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_style_ref").val(style_des);
			$("#txt_style_ref_id").val(style_id); 
			$("#txt_style_ref_no").val(style_no); 
		}
	}
	
	function openmypage_order(type_id)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer = $("#cbo_buyer_name").val();	
		var txt_style_ref = $("#txt_style_ref").val();
		var cbo_year = $("#cbo_year").val();
		var txt_order_id_no = $("#txt_order_id_no").val();
		var txt_order_id = $("#txt_order_id").val();
		var txt_order = $("#txt_order").val();
		var page_link='requires/fabric_followup_report_controller.php?action=order_surch&company='+company+'&buyer_name='+buyer+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year+'&type_id='+type_id; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			//alert(type_id);
			if(type_id==1)
			{
			$("#txt_order").val(style_des);
			$("#txt_order_id").val(style_id); 
			$("#txt_order_id_no").val(style_des_no);
			}
			else
			{
				$("#txt_ref_no").val(style_des); 
			}
		}
	}

	function fn_order_disable(type_id)
	{
		if(type_id==2)
		{
			$('#txt_order').attr("disabled",true);
		}
		else
		{
			$('#txt_order').attr("disabled",false);
		}
	}
	function fn_date_chack(str)
	{
		if(str==1)
		{
			var ship_date=$('#txt_date_from').val();
			if(ship_date!="")
			{
				$('#txt_ex_date_form').val("");
				$('#txt_ex_date_to').val("");
			}
		}
		else
		{
			var ex_fact_date=$('#txt_ex_date_form').val();
			if(ex_fact_date!="")
			{
				$('#txt_date_from').val("");
				$('#txt_date_to').val("");
			}
		}
	}
	
	function open_trims_dtls(po_break_down_id,tot_po_qnty,ratio,page_title,action)
	{
		//alert(po_break_down_id);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_followup_report_controller.php?po_break_down_id='+po_break_down_id+'&tot_po_qnty='+tot_po_qnty+'&ratio='+ratio+'&action='+action, page_title, 'width=670px,height=400px,center=1,resize=0,scrolling=0','../../');
	}
	function generate_ex_factory_popup(action,job,id,width)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_followup_report_controller.php?action='+action+'&job='+job+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
	function open_wo_popup(po_id,body_id,deterId,color_id,jobNo,title,action)
	{
		//alert(po_id);
		if(action=='fin_recv_popup')
		{
			var width=1180+'px';
		}
		else if(action=='fin_issue_popup')
		{
			var width=1180+'px';
		}
		else
		{
			var width=880+'px';
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_followup_report_controller.php?action='+action+'&po_id='+po_id+'&body_id='+body_id+'&deterId='+deterId+'&color_id='+color_id+'&jobNo='+jobNo, title, 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
	
	function openmypage_rej(po_id,company,action,reportType)
	{
		//alert(country_id);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_followup_report_controller.php?po_id='+po_id+'&company='+company+'&action='+action+'&reportType='+reportType, 'Reject Quantity', 'width=600px,height=350px,center=1,resize=0,scrolling=0','../../');
	}	
	function fn_order_date(type)
	{
		if(type==1)
		{
			var head_title="Actual.Ship Date";
		} 
		else  var head_title="Publish.Ship Date";
		document.getElementById('td_head').innerHTML=head_title;
	}
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <form id="orderStatusReport" name="orderStatusReport">
			<? echo load_freeze_divs ("../../../"); ?>
            <h3 align="left" id="accordion_h1" style="width:820px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:820px;">
                <table class="rpt_table" width="820" cellpadding="0" cellspacing="0" align="center" rules="all">
                    <thead>
                        <th class="must_entry_caption" width="120">Company</th>
                        <th width="110">Buyer</th>
                        <th width="50">Job Year</th>
                        <th  width="100">Job No</th>
                         <th  width="100">Style Ref.</th>
                         <th  width="100">Season</th>
                       
                        <th width="200" class="must_entry_caption">Pub. Ship Date</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:55px" onClick="reset_form('orderStatusReport','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tr class="general">                   
                        <td> 
							<?
                            echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/fabric_followup_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                        
                        <td><? echo create_drop_down( "cbo_year", 50, create_year_array(),"", 1,"-- All --",date("Y",time()) , "",0,"" );//date("Y",time()) ?>	</td>
                        <td align="center">
                            <input style="width:100px;" name="txt_job" id="txt_job"  class="text_boxes" placeholder="Write"/>
                                    
                        </td>
                         <td align="center">
                            <input style="width:100px;" name="txt_style_ref" id="txt_style_ref" onDblClick="openmypage_style()" class="text_boxes" placeholder="Browse/Write"/>
                            <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/> 
                            <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>            
                        </td>
                         <td id="season_td">
                  
                            <? //echo create_drop_down( "txt_season", 150, $blank_array,'', 1, "-- Select Season--",$selected, "" ); 
							echo create_drop_down( "cbo_season_id", 80, "select id, season_name from lib_buyer_season where  status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );?>
                        
                        </td>
                        
                        <td>
                            <input type="text" id="txt_date_from" name="txt_date_from" class="datepicker" style="width:50px" onChange="fn_date_chack(1)" readonly>To
                            <input type="text" id="txt_date_to" name="txt_date_to" class="datepicker" style="width:50px" readonly>
                        </td>
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:55px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="9" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                </table>
            </fieldset>
            </div>
        </form>
    </div> 
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>  
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
