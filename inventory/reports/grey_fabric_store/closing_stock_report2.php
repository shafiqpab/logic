<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Closing Stock Report

Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	24-11-2013
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
echo load_html_head_contents("Closing Stock Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters =
	{
		col_17: "none",
		col_operation: {
		id: ["value_tot_opening","value_tot_receive","value_tot_issue_return","value_tot_trans_in","value_total_receive","value_tot_issue","value_tot_rec_return","value_tot_transfer_out","value_total_issue","value_totalStock"],
		col: [6,7,8,9,10,11,12,13,14,15],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
        var tableFilters2 =
	{
		col_operation: {
		id: ["value_tot_opening","value_tot_opening_amount","value_tot_receive","value_tot_issue_return","value_tot_trans_in","value_total_receive","value_total_rcv_amount","value_tot_issue","value_tot_rec_return","value_tot_transfer_out","value_total_issue","value_tot_issue_amount","value_totalStock","value_totalStock_amount"],
                col: [7,8,9,10,11,12,13,14,15,16,17,18,20,21],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function openmypage_item_account()
	{
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/closing_stock_report2_controller.php?action=item_account_popup&data='+data,'Item Account Popup', 'width=500px,height=370px,center=1,resize=0','../../')

		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("item_account_id");
			var theemailv=this.contentDoc.getElementById("item_account_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_item_account_id").value=response[0];
			    document.getElementById("txt_item_acc").value=theemailv.value;
				//reset_form();
				//get_php_form_data( response[0], "item_account_dtls_popup", "requires/closing_stock_report2_controller" );
				release_freezing();
			}
		}
	}

	function generate_report(operation)
	{
		if( form_validation('cbo_company_name*cbo_item_category_id*txt_date_from*txt_date_to','Company Name*Fabric Nature*From Date*To Date')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var item_account_id = $("#txt_item_account_id").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var cbo_value_with = $("#cbo_value_with").val();
		var get_upto_qnty = $("#cbo_get_upto_qnty").val();
		var txt_qnty = $("#txt_qnty").val();

		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_store_name="+cbo_store_name+"&cbo_item_category_id="+cbo_item_category_id+"&from_date="+from_date+"&to_date="+to_date+"&item_account_id="+item_account_id+"&cbo_value_with="+cbo_value_with+"&report_title="+report_title+"&get_upto_qnty="+get_upto_qnty+"&txt_qnty="+txt_qnty+"&report_type="+operation;
		var data="action=generate_report"+dataString;
		//alert (data);
		freeze_window(operation);
		http.open("POST","requires/closing_stock_report2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("**");
            // alert(reponse[1])
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			/*if(reponse[2] == 3){
				setFilterGrid("table_body",-1,tableFilters);
				setFilterGrid("table_body_non_ord",-1);
			}else{
				setFilterGrid("table_body",-1,tableFilters2);
				setFilterGrid("table_body_non_ord",-1);
			}*/
			setFilterGrid("table_body",-1,tableFilters);
			//setFilterGrid("table_body_non_ord",-1);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		$("#table_body tr:first").hide();
		$("#table_body_non_ord tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflow="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";

		$("#table_body tr:first").show();
		$("#table_body_non_ord tr:first").show();
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>
        <form name="closingstock_1" id="closingstock_1" autocomplete="off" >
         <h3 style="width:1250px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <div id="content_search_panel" style="width:1250px" >
            <fieldset>
                <table class="rpt_table" width="1230" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th class="must_entry_caption">Company</th>
                        <th class="must_entry_caption">Fabric Nature</th>
                        <th>Item Description</th>
                        <th>Store</th>
                        <th>Value</th>
                        <th>Get Upto</th>
                        <th>Qty.</th>
                        <th class="must_entry_caption">Date Range</th>
                        <th colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('closingstock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <?
                                    echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "" );
                                ?>
                            </td>
                           <td>
								<?
									echo create_drop_down( "cbo_item_category_id", 150,$item_category,"", 1, "-- Select Category--", $selected, "load_drop_down( 'requires/closing_stock_report2_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_store', 'store_td' );","","13,14","","","");
                                ?>
                          </td>
                            <td>
                            	<input style="width:140px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_item_account_id" id="txt_item_account_id" style="width:90px;"/>
                            </td>
                           <td id="store_td">
                                <?
                                    echo create_drop_down( "cbo_store_name", 120, $blank_array,"", 1, "--Select Store--", "", "" );
                                ?>
                           </td>
                            <td>
								<?
									$valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
									echo create_drop_down( "cbo_value_with", 115, $valueWithArr, "", 1, "", 0, "", "", "");
                                ?>
                            </td>
                            <td> 
                            <?
                            	$get_upto=array(1=>"Greater Than",2=>"Less Than",3=>"Greater/Equal",4=>"Less/Equal",5=>"Equal");
                                echo create_drop_down( "cbo_get_upto_qnty", 70, $get_upto,"", 1, "- All -", 0, "",0 );
                            ?>
                        	</td>
                        	<td>
                            <input type="text" id="txt_qnty" name="txt_qnty" class="text_boxes_numeric" style="width:30px" value="" />
                        	</td>
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:55px;"/>
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:55px;"/>
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:80px" class="formbutton" />
                            </td>
                            <td  align="center">
                            	<input type="button" name="search" id="search" value="Closing Report" onClick="generate_report(4)" style="width:100px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8" align="center"><? echo load_month_buttons(1);  ?></td>

                            <td colspan="2" align="right"><input type="button" name="search" id="search" value="Buyer Wise Summary" onClick="generate_report(5)" style="width:130px; display:none;" class="formbutton" /></td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
            </div>
            <br />
                <div id="report_container" align="center"></div>
                <div id="report_container2"></div>
        </form>
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
