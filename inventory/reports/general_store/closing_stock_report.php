<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Closing Stock Report

Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	20-11-2013
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
echo load_html_head_contents("Closing Stock Report","../../../", 1, 1, $unicode,1,'');
//var_dump($item_category);
$user_id=$_SESSION['logic_erp']['user_id'];
?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(operation)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var txt_product_id = $("#txt_product_id").val();
		var item_group_id = $("#txt_item_group_id").val();
		var item_sub_group_id = $("#txt_item_sub_group_id").val();
		var item_account_id = $("#txt_item_account_id").val();
		var txt_item_code = $("#txt_item_code").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var cbo_yes_no = $("#cbo_yes_no").val();
		var cbo_value_with = $("#cbo_value_with").val();
		var variable_string_inventory = $("#variable_string_inventory").val();


		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_store_name="+cbo_store_name+"&cbo_item_category_id="+cbo_item_category_id+"&txt_item_code="+txt_item_code+"&txt_product_id="+txt_product_id+"&from_date="+from_date+"&to_date="+to_date+"&item_account_id="+item_account_id+"&item_group_id="+item_group_id+"&cbo_yes_no="+cbo_yes_no+"&cbo_value_with="+cbo_value_with+"&report_title="+report_title+"&report_type="+operation+"&item_sub_group_id="+item_sub_group_id+"&variable_string_inventory="+variable_string_inventory;
		var data="action=generate_report"+dataString;
		//alert (data);
		freeze_window(operation);
		http.open("POST","requires/closing_stock_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("**");
			if(reponse[2] == 11)
			{
				if(reponse[0]!='')
				{
					$('#aa1').removeAttr('href').attr('href','requires/'+reponse[1]);
					document.getElementById('aa1').click();
				}
			}
			else
			{
				$("#report_container2").html(reponse[0]);
				document.getElementById('report_container').innerHTML='<a href="##" onclick="fnExportToExcel()" target=_blank; style="text-decoration:none" id="dlink"><input type="button" class="formbutton" value="Excel Preview " style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			}
			//alert();
			if(reponse[2]!=8) setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	}
function fnExportToExcel()
{
    // $(".fltrow").hide();
    let tableData = document.getElementById("report_container2").innerHTML;
    // alert(tableData);
    let data_type = 'data:application/vnd.ms-excel;base64,',
    template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
    base64 = function (s) {
        return window.btoa(unescape(encodeURIComponent(s)))
    },
    format = function (s, c) {
        return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
    }

    let ctx = {
        worksheet: 'Worksheet',
        table: tableData
    }

    let dt = new Date();
    document.getElementById("dlink").href = data_type + base64(format(template, ctx));
    document.getElementById("dlink").traget = "_blank";
    document.getElementById("dlink").download = dt.getTime()+'_display_board.xls';
    document.getElementById("dlink").click();
    // $(".fltrow").show();
    // alert('ok');
}
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");

		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="350px";
        $('#scroll_body tr:first').show();
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

	function openmypage_itemgroup()
	{
		if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var txt_item_group = $("#txt_item_group").val();
		var txt_item_group_id = $("#txt_item_group_id").val();
		var txt_item_group_no = $("#txt_item_group_no").val();
		var page_link='requires/closing_stock_report_controller.php?action=item_group_such_popup&company='+company+'&cbo_item_category_id='+cbo_item_category_id+'&txt_item_group='+txt_item_group+'&txt_item_group_id='+txt_item_group_id+'&txt_item_group_no='+txt_item_group_no;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var item_group_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var item_group_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var item_group_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_item_group").val(item_group_des);
			$("#txt_item_group_id").val(item_group_id);
			$("#txt_item_group_no").val(item_group_no);
		}
	}

	function openmypage_itemSubgroup()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
        if( form_validation('cbo_item_category_id','Item Category')==false )
        {
            alert('Item Category Required!');
            return;
        }
		var company = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var txt_item_group = $("#txt_item_group").val();
		var txt_item_group_id = $("#txt_item_group_id").val();
		var txt_item_group_no = $("#txt_item_group_no").val();
		var txt_item_sub_group = $("#txt_item_sub_group").val();
		var txt_item_sub_group_id = $("#txt_item_sub_group_id").val();
		var txt_item_sub_group_no = $("#txt_item_sub_group_no").val();
		var page_link='requires/closing_stock_report_controller.php?action=item_sub_group_such_popup&company='+company+'&cbo_item_category_id='+cbo_item_category_id+'&txt_item_sub_group='+txt_item_sub_group+'&txt_item_sub_group_id='+txt_item_sub_group_id+'&txt_item_sub_group_no='+txt_item_sub_group_no+'&txt_item_group='+txt_item_group+'&txt_item_group_id='+txt_item_group_id+'&txt_item_group_no='+txt_item_group_no;
		var title="Search Item Sub Group Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var item_sub_group_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var item_sub_group_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var item_sub_group_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_item_sub_group").val(item_sub_group_des);
			$("#txt_item_sub_group_id").val(item_sub_group_id);
			$("#txt_item_sub_group_no").val(item_sub_group_no);
		}
	}

	function openmypage_itemaccount()
	{
		if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var txt_item_group_id = $("#txt_item_group_id").val();
		var txt_item_acc = $("#txt_item_acc").val();
		var txt_item_account_id = $("#txt_item_account_id").val();
		var txt_item_acc_no = $("#txt_item_acc_no").val();
		var txt_item_sub_group_id = $("#txt_item_sub_group_id").val();
		var page_link='requires/closing_stock_report_controller.php?action=item_account_such_popup&company='+company+'&cbo_item_category_id='+cbo_item_category_id+'&txt_item_group_id='+txt_item_group_id+'&txt_item_acc='+txt_item_acc+'&txt_item_account_id='+txt_item_account_id+'&txt_item_acc_no='+txt_item_acc_no+'&txt_item_sub_group_id='+txt_item_sub_group_id;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var item_acc_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var item_acc_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var item_acc_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_item_acc").val(item_acc_des);
			$("#txt_item_account_id").val(item_acc_id);
			$("#txt_item_acc_no").val(item_acc_no);
		}
	}

	function fn_store_visibility(yes_no_id)
	{
		$('#cbo_store_name').val(0);
		if(yes_no_id==2) {
			$('#cbo_store_name').attr('disabled',true);
		} else {
			$('#cbo_store_name').attr('disabled',false);
		}
	}

	function print_report_button_setting(report_ids)
    {
        //alert(report_ids);
        $('#show').hide();
        $('#report1').hide();
        $('#report2').hide();
        $('#report3').hide();
        $('#summary').hide();
		$('#show_excel').hide();
		$('#Summary_4').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==222){$('#show').show();}
            else if(items==266){$('#report1').show();}
            else if(items==256){$('#report2').show();}
            else if(items==267){$('#report3').show();}
            else if(items==149){$('#summary').show();}
			else if(items==150){$('#summary2').show();}
			else if(items==277){$('#summary3').show();}
			else if(items==689){$('#Summary_4').show();}
			else if(items==783){$('#show_excel').show();}
            });
    }

	function rate_controll_function(data)
	{
		var varible_string=return_global_ajax_value( data, 'varible_inventory', '', 'requires/closing_stock_report_controller');
		var varible_string_ref=varible_string.split("**");
		//alert(varible_string_ref[0]);
		if(varible_string_ref[0]) {
			$('#variable_string_inventory').val(varible_string_ref[1]);
		} else {
			$('#variable_string_inventory').val("");
		}
	}


</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />
        <form name="closingstock_1" id="closingstock_1" autocomplete="off" >
         <h3 style="width:1160px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <div id="content_search_panel" style="width:1160px" >
            <fieldset>
                <table class="rpt_table" width="1160" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="130" class="must_entry_caption">Company</th>
                        <th width="180">Item Category</th>
                        <th width="90">Item Group</th>
                        <th width="90">Sub Group Name</th>
                        <th width="90">Item Account</th>
                        <th width="100">Store Wise</th>
                        <th width="120">Store</th>
                        <th width="100">Value</th>
                        <th >Date</th>
                        <th width="70"><input type="reset" name="res" id="res" value="Reset" style="width:50px" class="formbutton" onClick="reset_form('closingstock_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td align="center">
                                <?
									//and core_business not in(3) 
                                    echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/closing_stock_report_controller', this.value+'**'+$('#cbo_yes_no').val() , 'load_drop_down_store', 'store_td' );get_php_form_data(this.value,'print_button_variable_setting','requires/closing_stock_report_controller' );rate_controll_function(this.value);" );//load_drop_down( 'requires/closing_stock_report_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );

                                ?>
                                <input type="hidden" name="variable_string_inventory" id="variable_string_inventory" />
                            </td>
                            <td align="center">
                            <?
                            $userCredential = sql_select("SELECT store_location_id, item_cate_id FROM user_passwd where id=$user_id");
                           	$item_cat_cond = ($userCredential[0][csf("item_cate_id")]) ? $userCredential[0][csf("item_cate_id")] : "" ;
							$item_cat_cond = implode(",",array_diff(explode(",",$item_cat_cond), array("4")));
							//create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name ) 4
                            echo create_drop_down( "cbo_item_category_id", 180, $general_item_category,"", 0, "", 0, "", 0,"$item_cat_cond", "", "", "");
                            ?>

                            </td>
                            <td align="center">
                            <input style="width:90px;"  name="txt_item_group" id="txt_item_group" onDblClick="openmypage_itemgroup()" class="text_boxes" placeholder="Browse"/>
                            <input type="hidden" name="txt_item_group_id" id="txt_item_group_id"/>
                              <input type="hidden" name="txt_item_group_no" id="txt_item_group_no"/>
                            </td>

                             <td align="center">
                            <input style="width:90px;"  name="txt_item_sub_group" id="txt_item_sub_group" onDblClick="openmypage_itemSubgroup()" class="text_boxes" placeholder="Browse"/>
                            <input type="hidden" name="txt_item_sub_group_id" id="txt_item_sub_group_id"/>
                            <input type="hidden" name="txt_item_sub_group_no" id="txt_item_sub_group_no"/>
                            </td>
                            <td align="center">
                            <input style="width:90px;"  name="txt_item_acc" id="txt_item_acc" onDblClick="openmypage_itemaccount()" class="text_boxes" placeholder="Browse"/>
                            <input type="hidden" name="txt_item_account_id" id="txt_item_account_id"/>    <input type="hidden" name="txt_item_acc_no" id="txt_item_acc_no"/>
                            </td>
                           <td align="center">
							<?
                                echo create_drop_down( "cbo_yes_no", 80, $yes_no,"", 0, "", 2, "fn_store_visibility(this.value)" );
                            ?>
                           </td>
                           <td id="store_td" align="center">
							<?
                                echo create_drop_down( "cbo_store_name", 120, $blank_array,"", 1, "--Select Store--", "", "",1 );
                            ?>
                           </td>
                            <td>
                            <?
                                $valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
                                echo create_drop_down( "cbo_value_with", 100, $valueWithArr,"",0,"",1,"","","");
                            ?>
                        </td>
                            <td align="center">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y");//date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:50px;" readonly />
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y");//date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:50px;" readonly />
                            </td>
                            <td align="center">
                                <input type="button" name="search" id="show" value="Show" onClick="generate_report(3)" style="width:50px; display: none;" class="formbutton"/>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" align="center"><? echo load_month_buttons(1);  ?>
							</td>
                            <td colspan="3" align="center">
                            	<input type="button" name="report1" id="report1" value="Report1" onClick="generate_report(5)" style="width:70px; display: none;" class="formbutton" />
                                <input type="button" name="report2" id="report2" value="Report2" onClick="generate_report(4)" style="width:70px; display: none;" class="formbutton" />
                                <input type="button" name="report3" id="report3" value="Report3" onClick="generate_report(6)" style="width:70px; display: none;" class="formbutton" />
                                <input type="button" name="summary" id="summary" value="Summary" onClick="generate_report(7)" style="width:70px; display: none;" class="formbutton" />
                                <input type="button" name="summary2" id="summary2" value="summary 2" onClick="generate_report(8)" style="width:70px; display: none;" class="formbutton" />
                                <input type="button" name="summary3" id="summary3" value="summary 3" onClick="generate_report(9)" style="width:70px; display: none;" class="formbutton" />
                                <input type="button" name="lot_wise" id="lot_wise" value="Lot Wise" onClick="generate_report(10)" style="width:70px;" class="formbutton" />
								<input type="button" name="show_excel" id="show_excel" value="Show Excel" onClick="generate_report(11)" style="width:70px;  display: none;" class="formbutton" />
								<a id="aa1" href="" style="text-decoration:none" download hidden>BB</a>
								<input type="button" name="Summary_4" id="Summary_4" value="Summary 4" onClick="generate_report(12)" style="width:70px;  display: none;" class="formbutton" />
                            </td>
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
<script>
	set_multiselect('cbo_item_category_id','0','0','0','0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
