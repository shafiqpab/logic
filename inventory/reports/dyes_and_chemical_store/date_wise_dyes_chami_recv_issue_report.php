<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Date Wise Dyes Chamical Receive Issue Report

Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	10/02/2015
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
echo load_html_head_contents("Dyes Chamical Receive Issue","../../../", 1, 1, $unicode,1,1);
?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";



function  generate_report(rptType)
{
	var cbo_item_cat = $("#cbo_item_cat").val();
	var cbo_company_name = $("#cbo_company_name").val();
	var txt_date_from = $("#txt_date_from").val();
	var txt_date_to = $("#txt_date_to").val();
	var cbo_based_on = $("#cbo_based_on").val();
	var cbo_purpose = $("#cbo_purpose").val();
	var cbo_supplier_name = $("#cbo_supplier_name").val();
	var cbo_store_name = $("#cbo_store_name").val();
	var txt_item_des = $("#txt_item_des").val();
	var cbo_uom = $("#cbo_uom").val();
	
	//alert(cbo_purpose);
	if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Form Date*To Date')==false )
	{
		return;
	}

	var dataString = "&cbo_item_cat="+cbo_item_cat+"&cbo_company_name="+cbo_company_name+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_based_on="+cbo_based_on+"&cbo_purpose="+cbo_purpose+"&cbo_supplier_name="+cbo_supplier_name+"&cbo_store_name="+cbo_store_name+"&txt_item_des="+txt_item_des+"&cbo_uom="+cbo_uom+"&rptType="+rptType;
	var data="action=generate_report"+dataString;
	freeze_window(5);
	http.open("POST","requires/date_wise_dyes_chami_recv_issue_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;
}

function generate_report_reponse()
{
	if(http.readyState == 4)
	{
		//alert(http.responseText);
		var reponse=trim(http.responseText).split("**");
		//alert(reponse[0]);
		if(reponse[3]==4 || reponse[3]==7 || reponse[3]==8 || reponse[3]==9)
		{
			if(reponse[0]!='')
			{
				//alert("reponse");
				$('#aa1').removeAttr('href').attr('href','requires/'+reponse[1]);
				 document.getElementById('aa1').click();
			}
			show_msg('3');
			release_freezing();
		}
		else
		{
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			append_report_checkbox('table_header_1',1);
			if(reponse[3]==1)
			{
				var tableFilters =
				{
					col_30: "none",
					col_operation: {
					id: ["value_total_receive","value_total_order_amt","value_total_issue","value_total_amount"],
					col: [23,24,25,27],
					operation: ["sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
						}
				}
				setFilterGrid("table_body",-1,tableFilters);
			}
			else if(reponse[3]==2)
			{
				var tableFilters =
				{
					col_30: "none",
					col_operation: {
					id: ["value_total_receive","value_total_order_amt","value_total_amount"],
					col: [23,24,26],
					operation: ["sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML"]
						}
				}
				setFilterGrid("table_body",-1,tableFilters);
			}
			else if(reponse[3]==3)
			{
				var tableFilters =
				{
					col_30: "none",
					col_operation: {
					id: ["value_total_issue","value_total_amount"],
					col: [24,26],
					operation: ["sum","sum"],
					write_method: ["innerHTML","innerHTML"]
						}
				}
				setFilterGrid("table_body",-1,tableFilters);
			}
            else if(reponse[3]==6)
            {
                var tableFilters =
                    {
                        col_30: "none",
                        col_operation: {
                            id: ["value_total_issue","value_total_amount"],
                            col: [25,27],
                            operation: ["sum","sum"],
                            write_method: ["innerHTML","innerHTML"]
                        }
                    }
                setFilterGrid("table_body",-1,tableFilters);
            }
            else if(reponse[3]==5)
            {
                var tableFilters =
                    {
                        col_30: "none",
                        col_operation: {
                            id: ["value_total_receive","value_total_order_amt","value_total_issue","value_total_amount"],
                            col: [23,24,25,27],
                            operation: ["sum","sum","sum","sum"],
                            write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
                        }
                    }
                setFilterGrid("table_body",-1,tableFilters);
            }
			else
			{
				var tableFilters =
				{
					col_30: "none",
					col_operation: {
					id: ["value_total_issue","value_total_amount"],
				col: [18,20],
				operation: ["sum","sum"],
				write_method: ["innerHTML","innerHTML"]
					}
				}
				setFilterGrid("table_body",-1,tableFilters);
			}
			release_freezing();
			show_msg('3');
		}
		//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
	}
}


function  generate_report_excel(rptType)
{
	var cbo_item_cat = $("#cbo_item_cat").val();
	var cbo_company_name = $("#cbo_company_name").val();
	var txt_date_from = $("#txt_date_from").val();
	var txt_date_to = $("#txt_date_to").val();
	var cbo_based_on = $("#cbo_based_on").val();
	var cbo_purpose = $("#cbo_purpose").val();
	var cbo_supplier_name = $("#cbo_supplier_name").val();
	var cbo_store_name = $("#cbo_store_name").val();
	var txt_item_des = $("#txt_item_des").val();
	var cbo_uom = $("#cbo_uom").val();
	
	//alert(cbo_purpose);
	if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Form Date*To Date')==false )
	{
		return;
	}

	var dataString = "&cbo_item_cat="+cbo_item_cat+"&cbo_company_name="+cbo_company_name+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_based_on="+cbo_based_on+"&cbo_purpose="+cbo_purpose+"&cbo_supplier_name="+cbo_supplier_name+"&cbo_store_name="+cbo_store_name+"&txt_item_des="+txt_item_des+"&cbo_uom="+cbo_uom+"&rptType="+rptType;
	var data="action=generate_report_excel"+dataString;
	freeze_window(5);
	http.open("POST","requires/date_wise_dyes_chami_recv_issue_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_excel_reponse;
}

function generate_report_excel_reponse()
{
	if(http.readyState == 4)
	{
		//alert(http.responseText);
		var reponse=trim(http.responseText).split("####");
		show_msg('3');
		if(reponse[0]!='')
		{
			//alert("reponse");
			$('#aa1').removeAttr('href').attr('href','requires/'+reponse[1]);
			document.getElementById('aa1').click();
		}
		show_msg('3');
		release_freezing();return;
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
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	d.close();
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="250px";
	$('#table_body tr:first').show();
}


function fn_change_base(str)
{
	if(str==1)
	{
		$("#up_tr_date").html("");
		$("#up_tr_date").html("Transaction Date Range").attr('style','color:blue');
	}
	else
	{
		$("#up_tr_date").html("");
		$("#up_tr_date").html("Insert Date Range").attr('style','color:blue');
	}
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission);  ?><br />
    <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
    <h3 align="left" id="accordion_h1" style="width:1300px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div style="width:1300px;" align="center" id="content_search_panel">
        <fieldset style="width:1300px;">
                <table class="rpt_table" width="1300" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                    	<th width="120" >Item Category</th>
                        <th width="120" class="must_entry_caption">Company</th>
                        <th width="100" >Store</th>
                        <th width="100" >Item Description</th>
                        <th width="100" >Supplier</th>
                        <th width="100">Based On</th>
                        <th width="110">Issue Purpose</th>
                        <th width="100">UOM</th>
                        <th width="160" id="up_tr_date" class="must_entry_caption">Transaction Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:90px" class="formbutton" onClick="reset_form('item_receive_issue_1','report_container2','','','','');" /></th>
                    </tr>
                </thead>
                <tr class="general">
                	<td>
						<?
                        	echo create_drop_down( "cbo_item_cat", 110, $item_category,"", 1, "-- ALL --", $selected, "",0,"5,6,7,23" );
                        ?>
                    </td>
                    <td>
						<?
                        	echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/date_wise_dyes_chami_recv_issue_report_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );load_drop_down( 'requires/date_wise_dyes_chami_recv_issue_report_controller', this.value, 'load_drop_down_store', 'store_td' ); get_php_form_data( this.value, 'company_wise_report_button_setting','requires/date_wise_dyes_chami_recv_issue_report_controller');" );
                        ?>
                    </td>
                    <td id="store_td"> 
                            <?
                                echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "-- Select Store --", 0, "" );
                            ?>
                        </td>
					<td >
						<input type="text" name="txt_item_des" id="txt_item_des" class="text_boxes" style="width:90px;" />
                    </td>
                    <td id="supplier_td">
						<?
                        	echo create_drop_down( "cbo_supplier_name", 100, $blank_array,"", 1, "-- All Supplier --", 0, "" );
                        ?>
                    </td>

                    <td >
                    	<?
						$base_on_arr=array(1=>"Transaction Date",2=>"Insert Date");
                        echo create_drop_down( "cbo_based_on", 100, $base_on_arr,"", 0, "", 1, "fn_change_base(this.value);",0 );
                        ?>
                    </td>
					<td>
                    	<?
						//$purpose_sql="";
                        echo create_drop_down( "cbo_purpose", 110, $general_issue_purpose,"", 1, "--Select Purpose--", 0, "",0 );
                        ?>
                    </td>
					<td>
                    	<?
						//$purpose_sql="";
                        echo create_drop_down( "cbo_uom", 100, $unit_of_measurement,"", 1, "--Select UOM--", 0, "",0 );
                        ?>
                    </td>
                    <td>
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px;" placeholder="From Date" readonly/> TO
                    	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:55px;" readonly/>
                    </td>

                    <td style="display:none;">
                    	<?
						$order_type=array(1=>"With Order",2=>"Without Order");
                        echo create_drop_down( "cbo_order_type", 80, $order_type,"", 1, "ALL", 0, "",0 );
                        ?>
                    </td>

                    <td>
                        <input type="button" name="search1" id="search1" value="All" onClick="generate_report(1)" style="width:45px" class="formbutton" />
                        <input type="button" name="search2" id="search2" value="Receive" onClick="generate_report(2)" style="width:50px" class="formbutton" />
                        <input type="button" name="search3" id="search3" value="Issue" onClick="generate_report(3)" style="width:45px" class="formbutton" />
                        <input type="button" name="search6" id="search6" value="Issue 2" onClick="generate_report(6)" style="width:45px" class="formbutton" />
                        <input type="button" name="search4" id="search4" value="Recv-Issue" onClick="generate_report(5)" style="width:70px" class="formbutton" />
                        <a id="aa1" style="text-decoration:none;"></a>
                    </td>
                </tr>
                <tr>
                	<td colspan="9" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    <td align="center">
                        <input type="button" name="search5" id="search5" value="All Excel" onClick="generate_report_excel(4)" style="width:55px" class="formbutton" />
                        <input type="button" name="search7" id="search7" value="Rcv. Excel" onClick="generate_report_excel(7)" style="width:65px" class="formbutton" />
                        <input type="button" name="search8" id="search8" value="Iss. Excel" onClick="generate_report_excel(8)" style="width:65px" class="formbutton" />
                        <input type="button" name="search9" id="search9" value="Iss2. Excel" onClick="generate_report_excel(9)" style="width:65px" class="formbutton" />
                    </td>
                </tr>

            </table>
        </fieldset>

    </div>
        <!-- Result Contain Start-------------------------------------------------------------------->
        	<div id="report_container" align="center"></div>
            <div id="report_container2"></div>
        <!-- Result Contain END-------------------------------------------------------------------->


    </form>
</div>
</body>
<!--<script>
	set_multiselect('cbo_source','0','0','','0');
</script>
-->
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
