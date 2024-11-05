<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Lc Opening Payment
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	13-03-2014
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

function open_lc_popup()
{
	
	if (form_validation('cbo_company_id','Company Name')==false)
	{
		return;
	}	
	else
	{
	
		var company = $("#cbo_company_id").val();
		var bank = $("#cbo_issue_banking").val();
		var supplier = $("#txt_supplier").val();
		var date_from = $("#txt_date_from").val();
		var date_to = $("#txt_date_to").val();
		var cbo_year_selection = $("#cbo_year_selection").val();
		page_link='requires/lc_opening_payment_entry_controller.php?action=lc_no_popup&company='+company+'&bank='+bank+'&supplier='+supplier+'&date_from='+date_from+'&date_to='+date_to+'&cbo_year_selection='+cbo_year_selection;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Lc Number Search", 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var data=this.contentDoc.getElementById("hidden_lc").value.split("_");
			document.getElementById('hide_lc_id').value=data[0];
			document.getElementById('txt_lc_no').value=data[1];
		}
	}


}

function open_supplier_popup()
{
	if (form_validation('cbo_company_id','Company Name')==false)
	{
		return;
	}
	else
	{
		var company = $("#cbo_company_id").val();
		page_link='requires/lc_opening_payment_entry_controller.php?action=supplier_popup&company='+company;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Lc Number Search", 'width=415px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var data=this.contentDoc.getElementById("hidden_suplier_id").value.split("_");
			document.getElementById('txt_supplier').value=data[0];
			document.getElementById('txt_supplier_name').value=data[1];
	
		}
	}
	
}


function generate_list()
{
	if(form_validation('cbo_company_id','Company Name')==false)
	{
	return;
	}
	
	var data="action=list_generate"+get_submitted_data_string("cbo_company_id*cbo_issue_banking*txt_supplier_name*txt_supplier*txt_date_from*txt_date_to*txt_lc_no*hide_lc_id","../../");
	freeze_window(4);
	http.open("POST","requires/lc_opening_payment_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fn_generate_list_response;
}
	
function fn_generate_list_response()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split("####");
		//alert(http.responseText);return;
		$('#list_view').html(response[0]);
		setFilterGrid('tbl_lc_list',-1);
		show_msg('4');
		release_freezing();
	}
}

function open_payment(id)
{
	if (form_validation('cbo_company_id','Company Name')==false)
	{
		return;
	}
	else
	{
		var company_id=lc_num=supplier=issue_bank=item_cat=lc_val=btb_lc_id=parmit="";
		company_id=$('#cbo_company_id').val();		
		lc_num=$('#lc_num_'+id).text();
		supplier=$('#supplier_id_'+id).text();
		issue_bank=$('#issue_bank_'+id).text();
		item_cat=$('#item_cat_'+id).text();
		lc_val=$('#lc_val_'+id).text()
		btb_lc_id=$('#lc_id_'+id).val();
		hidden_entry_id=$('#hidden_entry_id_'+id).val();
		//alert(btb_lc_id);
		/*
		var total_row=$('#tbl_lc_list tbody tr').length-1;
		for(var k=1; k<=total_row;k++)
		{
			 lc_num+=$('#lc_num_'+k).text()+'_';
			 supplier+=$('#supplier_id_'+k).text()+'_';
			 issue_bank+=$('#issue_bank_'+k).text()+'_';
			 item_cat+=$('#item_cat_'+k).text()+'_';
			 lc_val+=$('#lc_val_'+k).text()+'_';
		}
		alert(lc_val);
		*/
		page_link='requires/lc_opening_payment_entry_controller.php?action=charge_popup&lc_num='+lc_num+'&supplier='+supplier+'&issue_bank='+issue_bank+'&item_cat='+item_cat+'&lc_val='+lc_val+'&btb_lc_id='+btb_lc_id+'&hidden_entry_id='+hidden_entry_id+'&company_id='+company_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Lc Number Search", 'width=710px,height=480px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];;
			var data=this.contentDoc.getElementById("hedden_value").value.split("_");
			$('#txt_charge_'+id).val(number_format(data[0],2));
			$('#hidden_entry_id_'+id).val(data[1]);
		}
	}
}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
        <form name="lc_open_payment" id="lc_open_payment" autocomplete="off" > 
         <div id="content_search_panel" style="width:900px" align="left">      
            <fieldset>  
                <table class="rpt_table" width="900" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="130" class="must_entry_caption">Company</th>
                        <th width="130">Issue Bank</th>
                        <th width="200">Supplier</th>
                        <th width="100">LC Date From</th>
                        <th width="100">LC Date To</th>
                        <th width="120">LC No.</th>
                        <th ><input type="reset" name="res" id="res" value="Reset" style="width:90px" class="formbutton" onClick="reset_form('content_search_panel','list_view','','','')" /></th>
                    </thead>
                    <tbody>
                    
                        <tr>
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business in(1,3) $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected /*, "load_drop_down( 'requires/lc_opening_payment_entry_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );"*/ );
                                ?>                            
                           </td>
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_issue_banking", 130, "select id,(bank_name || ' (' || branch_name || ')' ) as bank_name from  lib_bank where issusing_bank=1 and status_active=1 and is_deleted=0  order by bank_name","id,bank_name", 1, "--Select Bank--", $selected, "" );
                                ?>                            
                           </td>
                           <td >
								 <input type="hidden" name="txt_supplier" id="txt_supplier"  />
                                 <input type="text" name="txt_supplier_name" id="txt_supplier_name" class="text_boxes" style="width:190px;" onDblClick="open_supplier_popup()" placeholder="Browse" readonly/>
                          </td>
                          <td>
                            	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:95px;"/> 
	                       </td>
                           <td>
								 <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:95px;"/>
                           </td>
                           <td>
								 <input type="text" name="txt_lc_no" id="txt_lc_no" class="text_boxes" style="width:100px;" onDblClick="open_lc_popup()" placeholder="Browse or Write"/>
                                 <input type="hidden" name="hide_lc_id" id="hide_lc_id" />
                           </td>
                         
                          <td>
                                <input type="button" name="search" id="search" value="Show" style="width:90px" class="formbutton" onClick="generate_list()" />
                          </td>
                      </tr>
                      <tr>
                        <td colspan="7" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                      </tr>
                  </tbody>
                </table> 
            </fieldset>
        </div>
 </form>
    <div style="margin-top:50px;" id="list_view"></div>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
