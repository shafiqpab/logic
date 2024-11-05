<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Import CI Statement Report
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	22-12-2013
Updated by 		: 		
Update date		: 	Jahid	   
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
		id: ["value_tot_lc_value","value_tot_bill_value","value_gt_total_paid","value_total_out_standing","value_atsite_il","value_atsite_bc","value_atsite_int","total_qnty","value_total_receive","value_balance_value"],
		// col: [14,21,22,23,24,25,26,41,46,47],
		//col: [16,23,24,25,26,27,28,43,48,49],
		col: [17,24,25,26,27,28,29,44,49,50],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	var tableFilters2 = 
	{
		col_60: "none",
		col_operation: {
		id: ["value_tot_lc_value","value_tot_bill_value","value_gt_total_margin","value_gt_total_erq","value_gt_total_std","value_gt_total_paid"],
		col: [9,13,14,15,16,17],
		operation: ["sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	

	function openmypage_pi_date(import_invoice_id,suppl_id,item_id,pi_id,curr_id,action,title)
	{
		var popup_width="";
		if(action=="pi_details") popup_width="900px"; else popup_width="850px";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/import_ci_statement_report_controller.php?import_invoice_id='+import_invoice_id+'&suppl_id='+suppl_id+'&item_id='+item_id+'&pi_id='+pi_id+'&curr_id='+curr_id+'&action='+action, title, 'width='+popup_width+',height=390px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_inHouse_date(pi_id,rcv_basis,receive_value,receive_qnty,category_id,action,title,invoice_id)
	{
		var popup_width="";
		if(action=="pi_rec_details") popup_width="700px"; else popup_width="700px";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/import_ci_statement_report_controller.php?pi_id='+pi_id+'&rcv_basis='+rcv_basis+'&receive_value='+receive_value+'&receive_qnty='+receive_qnty+'&category_id='+category_id+'&invoice_id='+invoice_id+'&action='+action, title, 'width='+popup_width+',height=390px,center=1,resize=0,scrolling=0','../');
	}
		
	
		
	function generate_report(operation)
	{
		if(operation==3 || operation==5)
		{
			if(form_validation('cbo_company_id','Company Name')==false)
			{
				return;
			}
		}else if(operation==6){
      
      if(form_validation('cbo_company_id*txt_date_from_p*txt_date_to_p','Company Name*Paid From Date*Paid to Date')==false)
			{
				return;
			}
    }
		else
		{
			if(form_validation('cbo_company_id*txt_date_from_p*txt_date_to_p','Company Name*From Paid Date*To Paid Date')==false)
			{
				return;
			}	
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_issue_banking*cbo_supplier_id*cbo_lc_type_id*cbo_currency_id*cbo_item_category_id*txt_date_from_c*txt_date_to_c*txt_date_from_b*txt_date_to_b*txt_date_from*txt_date_to*txt_date_from_p*txt_date_to_p*pending_type*txt_pending_date*txt_date_from_btb*txt_date_to_btb*cbo_source_id*txt_lc_no*txt_lc_id*txt_lc_sc',"../../")+'&report_title='+report_title+'&report_type='+operation;
		freeze_window(3);
		http.open("POST","requires/import_ci_statement_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	// function fn_report_generated_reponse()
	// {
	// 	if(http.readyState == 4) 
	// 	{
	// 		var reponse=trim(http.responseText).split("****");
	// 		var tot_rows=reponse[2];
	// 		$('#report_container2').html(reponse[0]);
	// 		document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
	// 		append_report_checkbox('table_header_1',1);
	// 		setFilterGrid("tbl_body",-1,tableFilters);//,tableFilters
	//  		show_msg('3');
	// 		release_freezing();
	// 	}
	// }
  function fn_report_generated_reponse()
  {
      if(http.readyState == 4) 
      {
          var reponse=trim(http.responseText).split("****");
          var tot_rows=reponse[2];
          $('#report_container2').html(reponse[0]);
          document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>'; 
          
          if(reponse[2]==3)
          {
            append_report_checkbox('table_header_1',1);
            setFilterGrid("tbl_body",-1,tableFilters);
          }
          else if(reponse[2]==4)
          {
            setFilterGrid("tbl_body",-1,tableFilters2);
          }
          // else if(reponse[2]==6)
          // {
          //   append_report_checkbox('table_header_12',1);
          //   setFilterGrid("tbl_body_ss",-1,tableFiltersss);
          // }
          else
          {
            setFilterGrid("tbl_body",-1);
          }
          show_msg('3');
          release_freezing();
      }
  }

    function new_window()
    {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none"; 
         document.getElementById('tbl_body').style.marginLeft="-18px"; 
        document.getElementById('tbl_body').style.paddingLeft="0px"; 
        document.getElementById('report_table_footer').style.marginLeft="-18px"; 
        document.getElementById('report_table_footer').style.paddingLeft="0px"; 
        $(".flt").hide();
        
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close(); 
        
        document.getElementById('scroll_body').style.overflowY="scroll"; 
        document.getElementById('scroll_body').style.maxHeight="400px";
        $(".flt").show();
        document.getElementById('tbl_body').style.marginLeft="0px"; 
        document.getElementById('tbl_body').style.paddingLeft="0px";
        document.getElementById('report_table_footer').style.marginLeft="0px"; 
        document.getElementById('report_table_footer').style.paddingLeft="0px";
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
	
	function paid_amount_dtls(invoice_id,action,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/import_ci_statement_report_controller.php?invoice_id='+invoice_id+'&action='+action, title, 'width=620px,height=390px,center=1,resize=0,scrolling=0','../');
	}

  function openmypage_category()
  {
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/import_ci_statement_report_controller.php?action=item_category_list_popup', "Item Category List", 'width=420px,height=320px,center=1,resize=0,scrolling=0','../');
    emailwindow.onclose=function()
      {
        var theform=this.contentDoc.forms[0]//("search_order_frm"); 
        var txt_selected_id=this.contentDoc.getElementById("hidden_item_category_id").value;  
        var txt_selected=this.contentDoc.getElementById("hidden_item_category_name").value; 
        $("#cbo_item_category_id").val(txt_selected_id);
        $("#cbo_item_category_name").val(txt_selected);
      }
  }

  function openmypage_supplier()
  {
    var company = $("#cbo_company_id").val();
    var category = $("#cbo_item_category_id").val();

    if(form_validation('cbo_company_id*cbo_item_category_id','Company Name*Item Category')==false)
    {
      return;
    }

    var title = 'Supplier List';   
    var page_link = 'requires/import_ci_statement_report_controller.php?action=supplier_list_popup'+'&company='+company+'&category='+category;
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=320px,center=1,resize=1,scrolling=0','');

    emailwindow.onclose=function()
      {
        var theform=this.contentDoc.forms[0];
        var txt_selected_id=this.contentDoc.getElementById("hidden_supplier_id").value;  
        var txt_selected=this.contentDoc.getElementById("hidden_supplier_name").value; 
        $("#cbo_supplier_id").val(txt_selected_id);
        $("#cbo_supplier_name").val(txt_selected);

      }
  }


	function openmypage_lc()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var page_link='requires/import_ci_statement_report_controller.php?action=lc_search&company='+company; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var lc_id=this.contentDoc.getElementById("txt_selected_id").value; // lc sc ID
			var lc_no=this.contentDoc.getElementById("txt_selected").value; // lc sc no
			$("#txt_lc_no").val(lc_no);
			$("#txt_lc_id").val(lc_id);
		}
	}


</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../","");  ?><br />    		 
        <form name="importcistatement_1" id="importcistatement_1" autocomplete="off" > 
         <h3 style="width:1900px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:1760px" >      
            <fieldset>  
                <table class="rpt_table" width="1900" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="90" class="must_entry_caption">Company</th>
                        <th width="90">Issuing Bank</th>
                        <th width="90">Item Category</th>
                        <th width="150">LC/SC No</th>
                        <th width="65">LC Type</th>
                        <th width="150">BTB LC No</th>
                        <th width="65">Currency</th>
                        <th width="90">Supplier</th>
                        <th width="90">Import Source</th>
                        <th width="150" >Company Accep. Date</th>
                        <th width="150" >Bank Accep. Date</th>
                        <th width="150" >Maturity Date</th>
                        <th width="150" >Paid Date</th>
                        <th width="150" >BTB Date</th>
                        <th width="60">Pending Type</th>
                       	<th >Pending As On</th>
                    </thead>
                    <tbody>
                      <tr>
                        <td  align="center">
                            <? 
                                echo create_drop_down( "cbo_company_id", 90, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business in(1,3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "" );
                            ?>                            
                        </td>
                        <td  align="center">
                            <? 
                              if ($db_type==0)
                              {
                                echo create_drop_down( "cbo_issue_banking", 90, "select id,concat(a.bank_name,' (', a.branch_name,')') as bank_name from  lib_bank where issusing_bank=1 and status_active=1 and is_deleted=0  order by bank_name","id,bank_name", 1, "--Select Bank--", $selected, "" );
                              }
                              else
                              {
                                echo create_drop_down( "cbo_issue_banking", 90, "select id,(bank_name || ' (' || branch_name || ')' ) as bank_name from  lib_bank where issusing_bank=1 and status_active=1 and is_deleted=0  order by bank_name","id,bank_name", 1, "--Select Bank--", $selected, "" );
                              }
                            ?>                            
                        </td>
                        <td id="cat_td">
                          <?php 
                            echo create_drop_down( "cbo_item_category_id", 170,$item_category,"", 0, "", $selected, "","","","","","");
                          ?> 
                        </td>
                        <td>
                          <input type="text" style="width:140px;" name="txt_lc_sc" id="txt_lc_sc" class="text_boxes" placeholder="Write" /> 
                        </td>
                        <td  align="center">
                            <? echo create_drop_down( "cbo_lc_type_id",65,$lc_type,'',1,'--Select LC Type--',0,"",0); ?>  
                        </td>
                        <td align="center">
                            <input  type="text" style="width:130px;"  name="txt_lc_no" id="txt_lc_no"  ondblclick="openmypage_lc()"  class="text_boxes" placeholder="Dubble Click For Item"  readonly />   
                            <input type="hidden" name="txt_lc_id" id="txt_lc_id"/>  
                            <!-- <input type="hidden" name="txt_lc_sc_no" id="txt_lc_sc_no"/> 
                            <input type="hidden" name="is_lc_or_sc" id="is_lc_or_sc"/>            -->
                        </td>
                        <td align="center">
                            <? echo create_drop_down( "cbo_currency_id", 65, $currency,"", 1, "--Select--", 0, "",0 ); ?>
                        </td>                          
                        <td id="supplier_td"  align="center">
                            <input type="text" name="cbo_supplier_name" id="cbo_supplier_name" class="text_boxes" style="width:80px;" placeholder="Browse" onDblClick="openmypage_supplier()"/>  
                            <input type="hidden" name="cbo_supplier_id" id="cbo_supplier_id" style="width:50px;" /> 
                        </td>
                        <td align="center">
                            <?
                              echo create_drop_down( "cbo_source_id", 170,$supply_source,"", 1, "-- Select Source--", $selected, "","","","","","");
                            ?> 
                        </td>
                        <td  align="center">
                          <input type="text" name="txt_date_from_c" id="txt_date_from_c" class="datepicker" style="width:48px;"/>                    							
                          To
                          <input type="text" name="txt_date_to_c" id="txt_date_to_c" class="datepicker" style="width:48px;"/>                        
                        </td  align="center">
                        <td  align="center">
                          <input type="text" name="txt_date_from_b" id="txt_date_from_b" class="datepicker" style="width:48px;"/>                    							
                          To
                          <input type="text" name="txt_date_to_b" id="txt_date_to_b" class="datepicker" style="width:48px;"/>                        
                        </td>
                        <td  align="center">
                          <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:48px;"/>                    							
                          To
                          <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:48px;"/>                        
                        </td>
                        <td  align="center">
                          <input type="text" name="txt_date_from_p" id="txt_date_from_p" class="datepicker" style="width:48px;"/>                    							
                          To
                          <input type="text" name="txt_date_to_p" id="txt_date_to_p" class="datepicker" style="width:48px;"/>                        
                        </td>
                        <td  align="center">
                          <input type="text" name="txt_date_from_btb" id="txt_date_from_btb" class="datepicker" style="width:48px;"/>                    							
                          To
                          <input type="text" name="txt_date_to_btb" id="txt_date_to_btb" class="datepicker" style="width:48px;"/>                        
                        </td>
                        <td  align="center">
                            <?
                              $ourstanding_array=array(1=>"All",2=>"Immaturity",3=>"Maturity");
                              echo create_drop_down( "pending_type",60,$ourstanding_array,'',1,'Select',0,"",0); 
                            ?>  
                        </td>
                        <td align="center">
                          <input type="text" name="txt_pending_date" id="txt_pending_date" class="datepicker" style="width:48px;"/>                    							
                        </td>
                      </tr>
                  </tbody>
                  <tfoot>
                      <tr>
                           <td colspan="13"><? echo load_month_buttons(1);  ?>
                           &nbsp; &nbsp;&nbsp;<input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:120px" class="formbutton" />
                           &nbsp; &nbsp;&nbsp;<input type="button" name="search" id="search" value="Paid" onClick="generate_report(6)" style="width:120px" class="formbutton" />
                           &nbsp; &nbsp;&nbsp;<input type="button" name="search" id="search" value="Payment Details" onClick="generate_report(4)" style="width:120px" class="formbutton" />
                           &nbsp; &nbsp;&nbsp;<input type="button" name="search" id="search" value="Show 2" onClick="generate_report(5)" style="width:120px" class="formbutton" />
                           &nbsp;&nbsp; <input type="reset" name="res" id="res" value="Reset" style="width:120px" class="formbutton" onClick="reset_form('importcistatement_1','report_container*report_container2','','','')" />
</td>
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
	set_multiselect('cbo_company_id*cbo_source_id*cbo_item_category_id','0*0*0','0*0*0','','0*0*0');
  //setTimeout[($("#cat_td a").attr("onclick","disappear_list(cbo_item_category_id,'0');getStoreId();") ,3000)];
</script>
<?
		$sql=sql_select("select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name");
		$company_id='';
		$is_single_select=0;
		if(count($sql)==1){
			$company_id=$sql[0][csf('id')];
			$is_single_select=1;
			?>
			<script>
			console.log('shariar');
			set_multiselect('cbo_company_id','0','<? echo $is_single_select?>','<? echo $company_id?>','0');
			
			</script>	
			<?
		}	
	?>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
