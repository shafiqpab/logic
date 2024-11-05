<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Date Wise Finish Fabric Rcv Issue Report
				
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	12-08-2021
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
echo load_html_head_contents("Shade and Shrinkage Report","../../../", 1, 1, $unicode,1,''); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	var tableFilters = 
	{
		//col_15: "none",
		col_operation: {
		id: ["value_total_rcv_qnty_kg","value_total_issue_rtn_kg","value_total_transfe_in_kg","value_total_rcv_qnty_yds","value_total_issue_rtn_yds","value_total_transfe_in_yds","value_total_rcv_qnty_mtr","value_total_issue_rtn_mtr","value_total_transfe_in_mtr","value_total_cons_amount","value_total_order_amount"],
		col: [28,29,30,31,32,33,34,35,36,38,40],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	var tableFilters2 = 
	{
		//col_15: "none",
		col_operation: {
		id: ["value_issue_qnty_kg","value_rcv_rtn_kg","value_transfe_out_kg","value_issue_qnty_yds","value_rcv_rtn_yds","value_transfe_out_yds","value_issue_qnty_mtr","value_rcv_rtn_mtr","value_transfe_out_mtr","value_total_cons_amount","value_total_order_amount"],
		col: [29,30,31,32,33,34,35,36,37,39,41],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	var tableFilters3 = 
	{
		//col_15: "none",
		col_operation: {
		id: ["value_rcv_qnty_kg","value_issue_qnty_kg","value_rcv_qnty_yds","value_issue_qnty_yds","value_rcv_qnty_mtr","value_issue_qnty_mtr"],
		col: [12,13,14,15,16,17],
		operation: ["sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	

	function generate_report(rpt_type)
	{
		var job_no=$('#txt_job_no').val();
		var order_no=$('#txt_order_no').val();
		var booking_no=$('#txt_booking_no').val();
		var pi_no=$('#txt_pi_no').val();
		if(job_no == "" && order_no == "" && booking_no == "" && pi_no == "")
		{
			if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Date Form*Date To')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_id','Company Name')==false )
			{
				return;
			}
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_brand_id*cbo_year*txt_job_no*txt_job_id*txt_style_ref_no*txt_consigment_no',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		//alert (data);return;
		freeze_window(3);
		http.open("POST","requires/shade_and_shrinkage_report_controller.php",true);
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
			//alert(reponse[2]);
			if(reponse[2]==1)
			{
				// setFilterGrid("table_body",-1,tableFilters);
			}
			else if(reponse[2]==2)
			{
				// setFilterGrid("table_body",-1,tableFilters2);
			}
			else
			{
				// setFilterGrid("table_body",-1,tableFilters3);
			}
			
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_print.css" type="text/css"  /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
	}

	function openmypage_job(search_type)
	{
		if( form_validation('cbo_company_id*cbo_year','Company Name*Year')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var page_link='requires/shade_and_shrinkage_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&search_type='+search_type;
		
		if(search_type==1)
			var title='Job No Search';
		else if(search_type==2)
			var title='Order No Search';
		else if(search_type==3)
			var title='Booking No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			if(search_type==1)
			{
				$('#txt_job_no').val(job_no);
				$('#txt_job_id').val(job_id);	
			}
			else if(search_type==2)
			{
				$('#txt_style_ref_no').val(job_no);
				$('#txt_job_no').val(job_id);
			}
		
		}
	}
	
	function openmypage_pi()
	{
		if( form_validation('cbo_company_id*cbo_year','Company Name*Year')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var page_link='requires/shade_and_shrinkage_report_controller.php?action=pi_no_popup&companyID='+companyID+'&cbo_year_id='+cbo_year_id;
		var title='PI No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var pi_no=this.contentDoc.getElementById("hide_pi_no").value;
			var pi_id=this.contentDoc.getElementById("hide_pi_id").value;
			$('#txt_pi_no').val(pi_no);
			$('#txt_pi_id').val(pi_id);
		}
	}
	function openmypage_po(po_id,action)
	{ 
		var popup_width='320px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/shade_and_shrinkage_report_controller.php?po_id='+po_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	function openmypage(prod_id,po_id,batch_id,store_id,date_form,date_to,item_category_id,action)
	{ 
		//alert(type);
		var companyID = $("#cbo_company_id").val();
		var popup_width='620px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/shade_and_shrinkage_report_controller.php?companyID='+companyID+'&prod_id='+prod_id+'&po_id='+po_id+'&batch_id='+batch_id+'&store_id='+store_id+'&date_form='+date_form+'&date_to='+date_to+'&item_category_id='+item_category_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	
	function getStoreId() 
	{	 
		var company_id = document.getElementById('cbo_company_id').value;
		load_drop_down( 'requires/shade_and_shrinkage_report_controller',company_id+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
		load_drop_down( 'requires/shade_and_shrinkage_report_controller',company_id, 'load_drop_down_stores', 'store_td' );
		set_multiselect('cbo_store_id','0','0','','0');      
	}
	
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>   		 
    <form name="ordewisefinishfabricstock_1" id="ordewisefinishfabricstock_1" autocomplete="off" > 
    <h3 style="width:850px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:850px;">
                <table class="rpt_table" width="830" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="140" class="must_entry_caption">Company</th>
                            <th width="140">Buyer</th> 
							<th width="120">Brand</th>                            
                            <th width="60">Year</th>
                            <th width="100" id="td_search">Job No</th>
                            <th width="100">Merch Style Ref.</th>
							<th width="100">Consigment</th>
                          
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center" class="general">
                        <td id="td_company">
                            <? 
                               echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/shade_and_shrinkage_report_controller',this.value+'_'+1, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
						<td id="brand_td"> 
                            <?
                                echo create_drop_down( "cbo_brand_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>                       
                        <td> 
                            <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 60, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:90px" onDblClick="openmypage_job(1)"   placeholder="Browse/Write" />
                            <input type="hidden" name="txt_job_id" id="txt_job_id"/>
                        </td>
                        <td>
                            <input type="text" id="txt_style_ref_no" name="txt_style_ref_no" class="text_boxes" style="width:90px" onDblClick="openmypage_job(2)"  placeholder="Browse/Write" />
                         
                        </td>       
						<td>
                            <input type="text" id="txt_consigment_no" name="txt_consigment_no" class="text_boxes" style="width:90px" />
                         
                        </td>                 
                     
                                         
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:55px" class="formbutton" />
                           
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="8" align="center"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                </table> 
            </fieldset> 
        </div>
             
    </form>    
</div>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>      
</body>  
<script>
	// set_multiselect('cbo_company_id*cbo_store_id','0*0','0*0','','0*0');
	//
	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_id,'0');getStoreId();") ,3000)];	
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
