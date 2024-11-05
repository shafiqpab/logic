<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Import CI Statement Inter Company Report
				
Functionality	:	
JS Functions	:
Created by		:	Tipu
Creation date : 01-04-2019
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
echo load_html_head_contents("Import CI Statement Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters = 
	{
		col_60: "none",
		col_operation: {
		id: ["value_tot_lc_value","value_tot_bill_value","value_gt_total_paid","value_total_out_standing","value_total_receive"],
		col: [3,20,21,22,32],
		operation: ["sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 

	function openmypage_pi_date(import_invoice_id,suppl_id,item_id,pi_id,curr_id,action,title)
	{
		var popup_width="";
		if(action=="pi_details") popup_width="900px"; else popup_width="850px";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/import_ci_statement_inter_company_controller.php?import_invoice_id='+import_invoice_id+'&suppl_id='+suppl_id+'&item_id='+item_id+'&pi_id='+pi_id+'&curr_id='+curr_id+'&action='+action, title, 'width='+popup_width+',height=390px,center=1,resize=0,scrolling=0','../');
	}
	
  function openmypage_inHouse_date(pi_id,receive_value,receive_qnty,item_category,action,title)
  {
    var popup_width="";
    if(action=="pi_rec_details") popup_width="620px"; else popup_width="620px";
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/import_ci_statement_inter_company_controller.php?pi_id='+pi_id+'&receive_value='+receive_value+'&receive_qnty='+receive_qnty+'&item_category='+item_category+'&action='+action, title, 'width='+popup_width+',height=390px,center=1,resize=0,scrolling=0','../');
  }
		
	
		
	function generate_report(operation)
	{
    var cbo_issue_banking=$('#cbo_issue_banking').val();
    var cbo_supplier_id=$('#cbo_supplier_id').val();
    var pending_type=$('#pending_type').val();
    var txt_pending_date=$('#txt_pending_date').val();

    if(cbo_issue_banking>0 || cbo_supplier_id>0 || pending_type>0 || txt_pending_date!="")
    {
      if(form_validation('cbo_company_id','Company Name')==false)
      {
        return;
      }
    }
    else
    {
      if(form_validation('cbo_company_id*search_by_id*txt_date_from*txt_date_to','Company Name*Search By*Form Date*To Date')==false)
      {
        return;
      }
    }
		
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_issue_banking*cbo_supplier_id*search_by_id*txt_date_from*txt_date_to*pending_type*txt_pending_date',"../../")+'&report_title='+report_title;
      //alert(data);return;
			freeze_window(3);
			http.open("POST","requires/import_ci_statement_inter_company_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		
	}
	
  function print_preview_button(url)
  {
    return '<input type="button" onclick="print_priview_html( \'report_container2\', \'scroll_body\',\'table_header_1\',\'report_table_footer\', 3, \'0\',\''+url+'\' )" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
  }
  function excel_preview_button(url)
  {
    return '<a href="requires/'+url+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/>';
  }

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=print_preview_button('../../');
      document.getElementById('report_container3').innerHTML=excel_preview_button(reponse[1]);
			append_report_checkbox('table_header_1',1);
			setFilterGrid("tbl_body",-1,tableFilters);
	 		show_msg('3');
			release_freezing();
		}
	}

	function new_window()
  {
    document.getElementById('scroll_body').style.overflow="auto";
    document.getElementById('scroll_body').style.maxHeight="none"; 
    $("#tbl_body tr:first").hide();
    var w = window.open("Surprise", "#");
    var d = w.document.open();
    d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
    d.close();         
    document.getElementById('scroll_body').style.overflowY="auto"; 
    document.getElementById('scroll_body').style.maxHeight="400px";
    $("#tbl_body tr:first").show();
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

  function openmypage_supplier()
  {
    var company = $("#cbo_company_id").val();

    if(form_validation('cbo_company_id','Company Name')==false)
    {
      return;
    }

    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/import_ci_statement_inter_company_controller.php?action=supplier_list_popup'+'&company='+company, "Supplier List", 'width=420px,height=320px,center=1,resize=0,scrolling=0','../');
    emailwindow.onclose=function()
      {
        var theform=this.contentDoc.forms[0];
        var txt_selected_id=this.contentDoc.getElementById("hidden_supplier_id").value;  
        var txt_selected=this.contentDoc.getElementById("hidden_supplier_name").value; 
        $("#cbo_supplier_id").val(txt_selected_id);
        $("#cbo_supplier_name").val(txt_selected);

      }
  }

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../","");  ?><br />    		 
        <form name="importcistatement_1" id="importcistatement_1" autocomplete="off" > 
         <h3 style="width:900px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:910px" >      
            <fieldset>  
                <table class="rpt_table" width="900" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="150" class="must_entry_caption">Company</th>
                        <th width="150">Issuing Bank</th>
                        <th width="100">Supplier</th>
                        <th width="100">Search By</th>
                        <th width="150" class="must_entry_caption">Date Range</th>
                        <th width="100">Pending Type</th>
                       	<th width="50">Pending As On</th>
                        <th width="70"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('importcistatement_1','report_container*report_container2*report_container3','','','')" /></th>
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
                                    echo create_drop_down( "cbo_issue_banking", 150, "select id,(bank_name||' ('||branch_name||')') as bank_name from  lib_bank where issusing_bank=1 and status_active=1 and is_deleted=0  order by bank_name","id,bank_name", 1, "--Select Bank--", $selected, "" );
                                ?>                            
                           </td>
                          <td id="supplier_td"  align="center">
                            <input type="text" name="cbo_supplier_name" id="cbo_supplier_name" class="text_boxes" style="width:100px;" placeholder="Browse" ondblclick="openmypage_supplier()" readonly/>  
                            <input type="hidden" name="cbo_supplier_id" id="cbo_supplier_id" style="width:100px;" />
                          </td>      

                          <td  align="center">
                              <?
                                $search_by_arr=array(1=>"B2B Date",2=>"Maturity Date",3=>"Company Accpt. Date",4=>"Bank Accpt. Date",5=>"Paid Date");
                                echo create_drop_down( "search_by_id",100,$search_by_arr,'',1,'Select',0,"",0); 
                              ?>  
                         </td>                    
                          <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:48px;"/>
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:48px;"/>                        
                          </td>
                          <td  align="center">
                            	<?
								                $ourstanding_array=array(1=>"All",2=>"Immaturity",3=>"Maturity");
                            		echo create_drop_down( "pending_type",100,$ourstanding_array,'',1,'Select',0,"",0); 
                            	?>  
	                       </td>
                          <td align="center">
                                <input type="text" name="txt_pending_date" id="txt_pending_date" class="datepicker" style="width:48px;"/>
                          </td>
                          <td align="center">
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:60px" class="formbutton" />                            
                          </td>
                      </tr>
                  </tbody>
                  <tfoot>
                      <tr>
                        <td colspan="13"><? echo load_month_buttons(1);  ?>&nbsp; &nbsp;&nbsp;
                        </td>
                      </tr>
                    </tfoot>
                </table> 
            </fieldset>
        </div>
    <div style="margin-top:10px" id=""><span id="report_container"></span><span id="report_container3"></span></div>
    <div id="report_container2"></div>
 </form> 
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
