<?
/* -------------------------------------------- Comments -----------------------
  Purpose			: 	This Form Will Create Order Allocation Details v2 Report.
  Functionality	    :
  JS Functions	    :
  Created by		:	Aziz
  Creation date 	: 	18-3-2023
  Updated by 		:
  Update date		:
  QC Performed BY	:
  QC Date			:
  omments		    :
 */

session_start();


if ($_SESSION['logic_erp']['user_id'] == "")
    header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Allocation Details", "../../", 1, 1, $unicode, 1, 1);
?>	
<script>
    if ($('#index_page', window.parent.document).val() != 1)
        window.location.href = "../../logout.php";

    var tableFilters =
            {
                col_33: "none",
                col_operation: {
                    id: ["total_order_qnty", "total_order_qnty_in_pcs", "value_tot_cm_cost", "value_tot_cost", "value_order", "value_margin", "value_tot_trims_cost", "value_tot_embell_cost"],
                    col: [9, 11, 25, 26, 29, 30, 31, 32],
                    operation: ["sum", "sum", "sum", "sum", "sum", "sum", "sum", "sum"],
                    write_method: ["innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML", "innerHTML"]
                }
            }
    function fn_report_generated(type)
    {
        
		/*if(type==1)
		{
			var sew_date_from=$('#txt_sew_date_from').val();
			var sew_date_to=$('#txt_sew_date_to').val();
			if(sew_date_from!="" && sew_date_to!="")
			{
				alert('Only for show 2 button');
				$('#txt_sew_date_from').val('');
				$('#txt_sew_date_to').val('');
				return;
			}
		}*/
		
		
		if ($('#cbo_company_id').val() == 0)
        {
            if ($('#cbo_allocation_company_id').val() == 0)
            {
                alert('Please select Owner or Allocated company');
                return;
            }
        }
		 /*if ($('#cbo_allocation_company_id').val() == 0)
		{
			alert('Please select   Allocated company');
			return;
		}*/
			
		if(type==2 || type==1)
		{
			var sew_date_from=$('#txt_sew_date_from').val();
			var sew_date_to=$('#txt_sew_date_to').val();
			var date_from=$('#txt_date_from').val();
			var date_to=$('#txt_date_to').val();
			
			if(sew_date_from=="" && sew_date_to=="")
			{
				if(date_from=="" && date_to=="")
				{
					if (form_validation('txt_date_from*txt_date_to', 'From Date*To Date') == false)//*txt_date_from*txt_date_to*From Date*To Date
					{
						return;
					}
				}
			}
			
			/*if (form_validation('txt_date_from*txt_date_to', 'From Date*To Date') == false)//*txt_date_from*txt_date_to*From Date*To Date
			{
				return;
			}*/
		}
			if(type==1)
			{
				var data = "action=report_generate" + get_submitted_data_string('cbo_company_id*cbo_allocation_company_id*cbo_buyer_name*txt_date_from*txt_date_to*txt_sew_date_from*txt_sew_date_to', "../../")+'&type='+type;
			}
			else if(type==2)
			{
				var data = "action=report_generate2" + get_submitted_data_string('cbo_company_id*cbo_allocation_company_id*cbo_buyer_name*txt_date_from*txt_date_to*txt_sew_date_from*txt_sew_date_to', "../../")+'&type='+type;
			}
            freeze_window(3);
            http.open("POST", "requires/order_allocation_details_v2_controller.php", true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fn_report_generated_reponse;
         
    }

    function fn_report_generated_reponse()
    {
        if (http.readyState == 4)
        {
            //var reponse=trim(http.responseText).split("****");
            var reponse = trim(http.responseText).split("####");
            $("#report_container2").html(reponse[0]);
			typeId=reponse[2];
            //alert(reponse[0]);  
            document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(typeId)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            //append_report_checkbox('table_header_1',1);
			if(typeId==1)
			{
             setFilterGrid("table_body", -1, tableFilters);
             setFilterGrid("tbl_header", -1);
			}

            show_msg('3');
            release_freezing();
        }
    }

    function new_window(type)
    {
      // alert(type);
	   if(type==1)
	   {
	    document.getElementById('scroll_body').style.overflow = "auto";
        document.getElementById('scroll_body').style.maxHeight = "none";
	   }
        //$('#scroll_body tr:first').hide();
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>' + document.getElementById('report_container2').innerHTML + '</body</html>');
        d.close();
		if(type==1)
		{
        document.getElementById('scroll_body').style.overflowY = "scroll";
        document.getElementById('scroll_body').style.maxHeight = "380px";
		}
        //$('#scroll_body tr:first').show();
        //document.getElementById('scroll_body').style.maxWidth="120px";
    }
	
	function fn_on_change()
	{
		var cbo_company_id = $("#cbo_company_id").val();
		load_drop_down( 'requires/order_allocation_details_v2_controller', cbo_company_id, 'load_drop_down_buyer', 'buyer_td' );
	}
	function print_button_setting()
    {
        $('#button_data_panel').html('');
        get_php_form_data($('#cbo_company_id').val(),'print_button_variable_setting','requires/order_allocation_details_v2_controller' );
    }


    function print_report_button_setting(report_ids)
    {
        var report_id=report_ids.split(",");
        for (var k=0; k<report_id.length; k++)
        {
            if(report_id[k]==108)
            {

                $('#button_data_panel').append( '<input type="button"  id="show_button" class="formbutton" style="width:70px;" value="Show"  name="show_button"  onClick="fn_report_generated(1)" />&nbsp;&nbsp;' );
            }
            if(report_id[k]==195)
            {
                $('#button_data_panel').append( '<input type="button"  id="show_button2" class="formbutton" style="width:70px;" value="Show 2"  name="show_button2"  onClick="fn_report_generated(2)" />&nbsp;&nbsp;' );
            }
			 
        }
    }
</script>
</head>

<body onLoad="set_hotkey();">

    <form id="cost_breakdown_rpt">
        <div style="width:100%;" align="center">
            <? echo load_freeze_divs("../../"); ?>
            <h3 align="left" id="accordion_h1" style="width:1060px" class="accordion_h" onClick="accordion_menu(this.id, 'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
                <fieldset style="width:1060px;">
                    <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr>                   
                                <th class="must_entry_caption">Company Name</th>
                                <th class="must_entry_caption" >Allocation Company</th>
                                <th>Buyer Name</th>

                                 <th class="must_entry_caption" id="td_date_caption">Pub Ship Date</th>
                                <th style="display:none" id="td_sew_date_caption">Sewing Date</th>
                                <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                                 
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td> 
                                    <?
                                    echo create_drop_down("cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 0, "-- Select Company --", $selected, " load_drop_down( 'requires/order_allocation_details_controller_v2', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                                    ?>
                                </td>

                                <td>
                                    <?
                                    echo create_drop_down("cbo_allocation_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 0, "-- Select Company --", $selected, "");
                                    ?>

                                </td>
                                <td id="buyer_td">
                                    <?
                                    echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "-- All Buyer --", $selected, "", 0, "");
                                    ?>
                                </td>

                                <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"  placeholder="From Date" >&nbsp; To&nbsp;
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"   placeholder="To Date" >
                                 </td>
                                 <td style="display:none"><input type="text" name="txt_sew_date_from" id="txt_sew_date_from" class="datepicker" style="width:70px"  placeholder="From Date" >&nbsp; To&nbsp;
                                    <input type="text" name="txt_sew_date_to" id="txt_sew_date_to" class="datepicker" style="width:70px"   placeholder="To Date" >
                                 </td>
                                <td>
                                    <!-- <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(1)" />
                                    <input type="button" id="show_button2" class="formbutton" style="width:80px" value="Show2" onClick="fn_report_generated(2)" />-->
                                     <span id="button_data_panel"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table>
                        <tr>
                            <td><? echo load_month_buttons(1);?></td>
                        </tr>
                    </table> 
                </fieldset>
            </div>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>

    </form>    
</body>
    <script>
    set_multiselect('cbo_company_id','0','0','','0','print_button_setting();fn_on_change();');
    set_multiselect('cbo_allocation_company_id','0','0','','0');
    </script>
        <?php
		$sql=sql_select("select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name");
		$company_id='';
		$is_single_select=0;
		if(count($sql)==1){
			$company_id=$sql[0][csf('id')];
			$is_single_select=1;
        ?>
        <script>
        set_multiselect('cbo_company_id','0','<?php echo $is_single_select; ?>','<?php echo $company_id; ?>','0','print_button_setting();fn_on_change();');
        // set_multiselect('cbo_allocation_company_id','0','< ?php echo $is_single_select; ?>','< ?php echo $company_id; ?>','0');
        print_button_setting();fn_on_change();
        </script> 
        <?php	}   ?>
 
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>