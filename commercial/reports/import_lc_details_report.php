<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Import LC Details Report
				
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	30-09-2018
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
echo load_html_head_contents("Import LC Details Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters = 
	{
		col_35: "none",
		col_operation: {
		id: ["value_tot_lc_value"],
		col: [10],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	} 

	function openmypage(company_name,lc_number,ship_date,supplier_id,lc_date,exp_date,payterm,pi_id,action,title)
	{
		var popup_width="";
		if(action=="pi_details") popup_width="900px"; else popup_width="850px";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/import_lc_details_report_controller.php?company_name='+company_name+'&lc_number='+lc_number+'&ship_date='+ship_date+'&supplier_id='+supplier_id+'&lc_date='+lc_date+'&exp_date='+exp_date+'&payterm='+payterm+'&pi_id='+pi_id+'&action='+action, title, 'width='+popup_width+',height=390px,center=1,resize=0,scrolling=0','../');
	}	
	
	function generate_report(operation)
	{
        if ($("#cbo_company_id").val()==0  && $("#cbo_issue_banking").val()==0)
        {
            if(form_validation('txt_date_from*txt_date_to','LC Date Range*LC Date Range')==false)
            {
                return;
            }
        }
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_issue_banking*txt_lc_sc_id*txt_lc_sc*txt_ex_rate*txt_date_from*txt_date_to*cbo_lc_type_id',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/import_lc_details_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
		
	}
	
    function generate_report_summary(operation)
	{
        // if ($("#cbo_company_id").val()==0  && $("#cbo_issue_banking").val()==0)
        // {
            if(form_validation('txt_date_from*txt_date_to','LC Date Range*LC Date Range')==false)
            {
                return;
            }
        // }
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate_summary"+get_submitted_data_string('cbo_company_id*cbo_issue_banking*txt_lc_sc_id*txt_lc_sc*txt_ex_rate*txt_date_from*txt_date_to*cbo_lc_type_id',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/import_lc_details_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	/*function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			setFilterGrid("tbl_marginlc_list",-1,tableFilters);
	 		show_msg('3');
			release_freezing();
		}
	}*/
    function fn_report_generated_reponse()
    {
        if(http.readyState == 4) 
        {
            var response=trim(http.responseText).split("####");
            //alert(http.responseText);return;
            $('#report_container2').html(response[0]);
            //document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
            document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
            release_freezing();
        }
    }

    function new_window()
    {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none";
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body></html>');
        d.close(); 
        document.getElementById('scroll_body').style.overflowY="scroll";
        document.getElementById('scroll_body').style.maxHeight="400px";
    }
	
	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}
	
	function lc_details_popup(lc_id,action,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/import_lc_details_report_controller.php?lc_id='+lc_id+'&action='+action, title, 'width=520px,height=390px,center=1,resize=0,scrolling=0','../');
	}

    function openmypage_lc_sc()
    {
        if( form_validation('cbo_company_id','Company Name')==false )
        {
            return;
        }
        var company = $("#cbo_company_id").val(); 
        var cbo_issue_banking = $("#cbo_issue_banking").val();
        var page_link='requires/import_lc_details_report_controller.php?action=lc_sc_search&company='+company+'&cbo_issue_banking='+cbo_issue_banking; 
        var title="Search Item Popup";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=370px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]; 
            var lc_sc_id=this.contentDoc.getElementById("txt_selected_id").value; // lc sc ID
            var lc_sc_no=this.contentDoc.getElementById("txt_selected").value; // lc sc no
            var serial_no=this.contentDoc.getElementById("txt_selected_no").value; // Serial No
            var is_lc_sc=this.contentDoc.getElementById("is_lc_or_sc").value;// is lc sc
            //alert(lc_sc_id);
            //$("#txt_lc_sc").val(lc_sc_no);
            $("#txt_lc_sc_id").val(lc_sc_id);
            $("#txt_lc_sc_no").val(serial_no); 
            //$("#is_lc_or_sc").val(is_lc_sc); 
        }
    }
	
	function openmypage_file(i,pi_id)
	{
		var page_link='requires/import_lc_details_report_controller.php?action=show_file&pi_id='+pi_id; 
		var title="Image View";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=250px,center=1,resize=0,scrolling=0','../')
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>   		 
        <form name="marginlcregister_1" id="marginlcregister_1" autocomplete="off" > 
         <h3 style="width:890px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:890px" >      
            <fieldset>  
                <table class="rpt_table" width="880" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="200" >Company</th>
                        <th width="100">Ex. Rate</th>
                        <th width="180">Bank</th>
                        <th class="must_entry_caption" width="180">LC Date Range</th>
                        <th width="65">LC Type</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('marginlcregister_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody class="general">
                        <tr>
                            <td align="center">
                                <? 
                                    echo create_drop_down( "cbo_company_id", 180, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and comp.core_business in(1,3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "" );
                                ?>                            
                            </td>
                            <td>
                                <input type="text"  name="txt_ex_rate"  id="txt_ex_rate" class="text_boxes_numeric" placeholder="Write" style="width:80px;">
                            </td>
                            <td align="center">
                                <? 
                                    echo create_drop_down( "cbo_issue_banking", 170, "select id,(bank_name || ' (' || branch_name || ')' ) as bank_name from  lib_bank where issusing_bank=1 and status_active=1 and is_deleted=0  order by bank_name","id,bank_name", 1, "--Select Bank--", $selected, "" );
                                ?>                            
                           </td>
                           <td align="center" style="display: none;">
                                <input  type="text" style="width:130px;"  name="txt_lc_sc" id="txt_lc_sc" onDblClick="openmypage_lc_sc()"  class="text_boxes" placeholder="Dubble Click For Item"  />   
                                <input type="hidden" name="txt_lc_sc_id" id="txt_lc_sc_id"/>   
                                <input type="hidden" name="txt_lc_sc_no" id="txt_lc_sc_no"/> <input type="hidden" name="is_lc_or_sc" id="is_lc_or_sc"/>           
                            </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;"/>                    							
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px;"/>                        
                            </td>
                             <td  align="center">
                            	<?
                            		echo create_drop_down( "cbo_lc_type_id",65,$lc_type,'',1,'--Select LC Type--',0,"",0,"1,2,4,5,6"); 
                            	?>  
	                       </td>
                           <td>
                                <span style="float:left">
                                    <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:70px" class="formbutton" />
                                </span>
                                <span style="float:right">
                                    <input type="button" name="search" id="search" value="Summary" onClick="generate_report_summary(3)" style="width:75px" class="formbutton" />
                                </span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" align="center"><? echo load_month_buttons(1);  ?></td>
                        </tr>
                    </tfoot>
                </table> 
            </fieldset>
        </div>
    <div style="margin-top:10px" id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>  
 </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
