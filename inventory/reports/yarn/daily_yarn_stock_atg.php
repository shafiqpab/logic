<?
/*-------------------------------------------- Comments
Purpose			: 	This file will create Daily Yarn Stock Report -AGT
Functionality	:
JS Functions	:
Created by		:	Kamrul Hasan
Creation date 	: 	21-06-2023
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
echo load_html_head_contents("Daily Yarn Stock","../../../", 1, 1, $unicode,1,1);

?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report(type)
{
    if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
	{
		return;
	}
    var txt_date_from 	= $("#txt_date_from").val();
	var txt_date_to 	= $("#txt_date_to").val();
	var company 	    = $("#cbo_company_name").val();

	//var txt_supplier 	    = $("#txt_supplier").val();
	var txt_supplier 	    = $("#txt_supplier_id").val();
    var cbo_dyed_type 	    = $("#cbo_dyed_type").val();
	var txt_yarn_type 	    = $("#txt_yarn_type_id").val();
	var txt_yarn_count 	    = $("#txt_yarn_count_id").val();
	var txt_lot_no 	    = $("#txt_lot_no").val();
	var cbo_value_with 	    = $("#cbo_value_with").val();
	var txt_excange_rate 	    = $("#txt_excange_rate").val();
    var data_string = "&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_company_name="+company+"&txt_supplier="+txt_supplier+"&cbo_dyed_type="+cbo_dyed_type+"&txt_yarn_type="+txt_yarn_type+"&txt_yarn_count="+txt_yarn_count+"&txt_lot_no="+txt_lot_no+"&cbo_value_with="+cbo_value_with+"&txt_excange_rate="+txt_excange_rate;
 	var data="action=generate_report"+data_string;
	freeze_window(3);
	http.open("POST","requires/daily_yarn_stock_atg_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;
}


function generate_report_reponse()
{
	if(http.readyState == 4)
	{
		$("#report_container").html(http.responseText);

		show_msg('3');
		release_freezing();
	}
}

function openmypage_supplier()
{
	/* if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	} */
	var companyID = $("#cbo_company_name").val();

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_stock_atg_controller.php?action=supplier_popup&companyID='+companyID, 'Supplier Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var supplier_des=this.contentDoc.getElementById("hidden_supplier").value;
		var supplier_id=this.contentDoc.getElementById("hidden_supplier_id").value;
		$("#txt_supplier").val(supplier_des);
		$("#txt_supplier_id").val(supplier_id);

	}
}
function openmypage_yarn_type()
{
	var companyID = $("#cbo_company_name").val();

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_stock_atg_controller.php?action=yarn_type_popup&companyID='+companyID, 'Yarn Type Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var yarn_type_des=this.contentDoc.getElementById("hidden_yarn_type").value; //Access form field with id="emailfield"
		var yarn_type_id=this.contentDoc.getElementById("hidden_yarn_type_id").value;
		$("#txt_yarn_type").val(yarn_type_des);
		$("#txt_yarn_type_id").val(yarn_type_id);

	}
}

function openmypage_yarn_count()
{
	var companyID = $("#cbo_company_name").val();

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/daily_yarn_stock_atg_controller.php?action=yarn_count_popup&companyID='+companyID, 'Yarn Count Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var yarn_count_des=this.contentDoc.getElementById("hidden_yarn_count").value; //Access form field with id="emailfield"
		var yarn_count_id=this.contentDoc.getElementById("hidden_yarn_count_id").value;
		$("#txt_yarn_count").val(yarn_count_des);
		$("#txt_yarn_count_id").val(yarn_count_id);

	}
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission);  ?>
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
    <div style="width:100%;" align="center">
        <h3 style="width:1200px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div style="width:100%;" id="content_search_panel">
            <fieldset style="width:1200px;">
                <table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Supplier</th>
                            <th>Dyed Type</th>
                            <th>Yarn Type</th>
                            <th>Count</th>
                            <th>Lot</th>
                            <th>Value Type</th>
                            <th class="must_entry_caption" colspan="2">Date</th>
                            <th>$ Conv. Rate</th>
                            <th ><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr>
                        <td align="center"> 
							<?
                               echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- All Company --", $selected, "load_drop_down( 'requires/daily_yarn_stock_report_controller', this.value+'**'+document.getElementById('cbo_store_wise').value, 'load_drop_down_store', 'store_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/daily_yarn_stock_report_controller' );get_php_form_data( this.value, 'company_wise_report_button_setting','requires/daily_yarn_stock_report_controller' );" );
                            ?>
                        </td>
                        <td align="center">

							<input type="text" id="txt_supplier" name="txt_supplier" class="text_boxes" style="width:150px" value="" onDblClick="openmypage_supplier();" placeholder="Browse" readonly />

							<input type="hidden" id="txt_supplier_id" name="txt_supplier_id" class="text_boxes" style="width:70px" value=""  />
                           </td>
                        <td align="center">
                            <?
                                $dyedType=array(0=>'All',1=>'Dyed Yarn',2=>'Non Dyed Yarn');
                                echo create_drop_down( "cbo_dyed_type", 80, $dyedType,"", 0, "--Select--", $selected, "", "","");
                            ?>
                        </td>
                        <td align="center">
							<input type="text" id="txt_yarn_type" name="txt_yarn_type" class="text_boxes" style="width:80px" value="" onDblClick="openmypage_yarn_type();" placeholder="Browse" readonly />

							<input type="hidden" id="txt_yarn_type_id" name="txt_yarn_type_id" class="text_boxes" style="width:70px" value=""  />
                            
                        </td>
                        <td align="center">
							<input type="text" id="txt_yarn_count" name="txt_yarn_count" class="text_boxes" style="width:120px" value="" onDblClick="openmypage_yarn_count();" placeholder="Browse" readonly />

							<input type="hidden" id="txt_yarn_count_id" name="txt_yarn_count_id" class="text_boxes" style="width:70px" value=""  />
                            <?
                            ?>
                        </td>
                        <td align="center">
                            <input type="text" id="txt_lot_no" name="txt_lot_no" class="text_boxes" style="width:45px" value="" />
                        </td>
                        <td align="center">
                            <?
                                $valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
                                echo create_drop_down( "cbo_value_with", 110, $valueWithArr,"",0,"",1,"","","");
                            ?>
                        </td>
                        <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:55px" readonly/>
                          </td>
                        <td align="center">

                             <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:55px" readonly/>
                        </td>
                        
						<td align="center">
                            <input type="text" id="txt_excange_rate" name="txt_excange_rate" class="text_boxes_numeric" style="width:30px" value="" />
                        </td>
                        <td align="center">
                            <input type="button" name="search" id="search1" value="Show" onClick="generate_report(1)" style="width:60px;display:display;" class="formbutton" />
                        </td>
                    </tr>
                </table>
            </fieldset>
		</div>
    </div>
    <br />
        <!-- Result Contain Start-->

        	<div id="report_container" align="center"></div>

        <!-- Result Contain END-->


    </form>
</div>
</body>
<script>
	//set_multiselect('cbo_yarn_count*cbo_supplier*cbo_yarn_type','0*0*0','0*0*0','','0*0*0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$("#cbo_value_with").val(1);
</script>
</html>
