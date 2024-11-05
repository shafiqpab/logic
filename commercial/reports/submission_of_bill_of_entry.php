<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Submission of Bill of Entry Report
				
Functionality	:	
JS Functions	:
Created by		:	Mohammad Shafiqur Rahman
Creation date 	: 	05-08-2019
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
echo load_html_head_contents("Submission of Bill of Entry","../../", 1, 1, $unicode,1,1); 
?>	
    <script>
        var permission='<? echo $permission; ?>';
        if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

        var tableFilters = 
        {
            col_60: "none",
            col_operation: {
            id: ["value_tot_lc_value","value_tot_bill_value","value_gt_total_paid","value_total_out_standing","total_qnty","value_total_receive","value_balance_value"],
            col: [13,18,19,20,35,39,40],
            operation: ["sum","sum","sum","sum","sum","sum","sum"],
            write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
            }
        } 
        
            
        function generate_report(operation)
        {
            if(form_validation('cbo_company_id*cbo_issue_banking','Company Name*Bank Name')==false)
            {
                return;
            }
            else
            {	
            
                var report_title=$( "div.form_caption" ).html();
                var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_issue_banking*txt_date_from*txt_date_to*txt_lc_sc*txt_lc_sc_id*txt_lc_sc_no*is_lc_or_sc',"../../")+'&report_title='+report_title;
                freeze_window(3);
                http.open("POST","requires/submission_of_bill_of_entry_controller.php",true);
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
                var tot_rows=reponse[2];
                $('#report_container2').html(reponse[0]);
                document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
                //append_report_checkbox('table_header_1',1);
                //append_row_wise_chekbox('tbl_body');
                setFilterGrid("tbl_body",-1,tableFilters);//,tableFilters
                show_msg('3');
                release_freezing();
            }
        }
        
        // function append_row_wise_chekbox(table_id){
        //     var i=0;
        //     $("#"+table_id+" tr").each(function(){
        //         $(this).addClass("res");
        //         $(this).prepend('<td width="30"><input type="checkbox" name="row_check[]" id="row_check_'+i+'" /></td>');
        //         i++;
        //     });
        // }

        function get_invoice_ids(table_id,rpt_type)
		{

            var total_row = $("#"+table_id+" tr").length;
            var invoice_ids=''; var i=1; var invoice_ids_uncheck='';
            //alert(total_row);
            for ( i; i < total_row; i++) {
                if($("#row_check_"+i).is(':checked'))
                {
                    invoice_ids += $("#row_check_"+i).data("invoice_id")+",";
                    
                }else{
                    invoice_ids_uncheck += $("#row_check_"+i).data("invoice_id")+",";
                }
                
            }
            //alert(invoice_ids);
            print_report( $('#cbo_company_id').val()+'*'+$('#cbo_issue_banking').val()+'*'+invoice_ids+'*'+invoice_ids_uncheck+'*'+rpt_type, "print_submission_bill_entry", "requires/submission_of_bill_of_entry_controller");
            
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

        function openmypage_lc_sc()
        {
            if( form_validation('cbo_company_id*cbo_issue_banking','Company Name*Bank Name')==false )
            {
                return;
            }
            var company = $("#cbo_company_id").val();	
            var bank_name = $("#cbo_issue_banking").val();	
            var page_link='requires/submission_of_bill_of_entry_controller.php?action=lc_sc_search&company='+company+'&bank_name='+bank_name; 
            var title="Search BTB LC NO Popup";
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=0,scrolling=0','../')
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0]; 
                var lc_sc_id=this.contentDoc.getElementById("txt_selected_id").value; // lc sc ID
                var lc_sc_no=this.contentDoc.getElementById("txt_selected").value; // lc sc no
                var serial_no=this.contentDoc.getElementById("txt_selected_no").value; // Serial No
                var is_lc_sc=this.contentDoc.getElementById("is_lc_or_sc").value;// is lc sc
                $("#txt_lc_sc").val(lc_sc_no);
                $("#txt_lc_sc_id").val(lc_sc_id);
                $("#txt_lc_sc_no").val(serial_no); 
                $("#is_lc_or_sc").val(is_lc_sc); 
            }
        }


    </script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../","");  ?><br />    		 
        <form name="submission_of_bill_of_entry_form" id="submission_of_bill_of_entry_form" autocomplete="off" > 
            <h3 style="width:760px; margin-top:10px; border-radius:5px!important; padding: 5px 0 0 4px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
            <div id="content_search_panel" style="width:770px" >      
                <fieldset>  
                    <table class="rpt_table" width="730" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <thead>
                            <th width="150" class="must_entry_caption">Company</th>
                            <th width="150"  class="must_entry_caption">Bank Name</th>
                            <th width="100">BTB No</th>
                            <th width="210" colspan="2">BTB Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:120px" class="formbutton" onClick="reset_form('submission_of_bill_of_entry_form','report_container*report_container2','','','')" /></th>
                        </thead>
                        <tbody>
                            <tr>
                                <td  align="center">
                                    <? 
                                        echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and comp.core_business in(1,3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "" );
                                    ?>                            
                                </td>
                                <td  align="center">
                                    <? 
                                        echo create_drop_down( "cbo_issue_banking", 150, "select id,(bank_name || ' (' || branch_name || ')' ) as bank_name from  lib_bank where issusing_bank=1 and status_active=1 and is_deleted=0  order by bank_name","id,bank_name", 1, "--Select Bank--", $selected, "" );
                                    ?>                            
                                </td>
                                <td align="center">
                                    <input  type="text" style="width:100px;"  name="txt_lc_sc" id="txt_lc_sc"  ondblclick="openmypage_lc_sc()"  class="text_boxes" placeholder="Dubble Click For Item"  readonly />   
                                    <input type="hidden" name="txt_lc_sc_id" id="txt_lc_sc_id"/>  
                                    <input type="hidden" name="txt_lc_sc_no" id="txt_lc_sc_no"/> 
                                    <input type="hidden" name="is_lc_or_sc" id="is_lc_or_sc"/>           
                                </td> 
                                <td  align="center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px;" placeholder="From date"/>  
                                                            
                                </td>
                                <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px;"placeholder="To date"/></td>
                                <td><input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:120px" class="formbutton" /></td>
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
            <div style="margin-top:10px" id="report_container"></div>
            <div id="report_container2"></div>
        </form> 
    </div>
</body>
<script>
	//set_multiselect('cbo_source_id*cbo_item_category_id','0*0','0*0','','0*0');
  //setTimeout[($("#cat_td a").attr("onclick","disappear_list(cbo_item_category_id,'0');getStoreId();") ,3000)];
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
