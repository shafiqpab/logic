<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Emb. Issue Callan Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Nuruzzaman
Creation date 	: 	24.11.2020
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
echo load_html_head_contents("Sample Fabric Production Status", "../../", 1, 1,$unicode,'','');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1)
		window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';
			
	//for func_onDblClick_requisition
	function func_onDblClick_requisition()
	{
        if (form_validation('cbo_company_name', 'Company Name') == false)
		{
            return;
        }

        var cbo_company_name = $("#cbo_company_name").val();
        var cbo_year = $("#cbo_year").val();
        var cbo_buyer_name = $("#cbo_buyer_name").val();
        var page_link = 'requires/sample_fabric_production_status_controller.php?action=popup_onDblClick_requisition&cbo_company_name=' + cbo_company_name + '&cbo_year=' + cbo_year + '&cbo_buyer_name=' + cbo_buyer_name;
        var title = 'Requisition Search';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=390px,center=1,resize=1,scrolling=0', '../');
        emailwindow.onclose = function ()
		{
            var theform = this.contentDoc.forms[0];
            var req_str = this.contentDoc.getElementById("selected_requisition_no").value;
			var req = req_str.split('_');
			$('#hdn_requisition_id').val(req[0]);
            $('#txt_requisition_no').val(req[1]);
			get_php_form_data( req[2], "load_dealing_marchant", "requires/sample_fabric_production_status_controller" );
        }
	}
	
	//func_onKeyDown
	function func_onKeyDown()
	{
		alert('su..re');
		$('#hdn_requisition_id').val('');
		$('#txt_requisition_no').val('');
	}
	
	//for func_onDblClick_booking
	function func_onDblClick_booking(page_link,title)
	{
        var cbo_company_name = $("#cbo_company_name").val();
        var cbo_year = $("#cbo_year").val();
        var cbo_buyer_name = $("#cbo_buyer_name").val();
        var cbo_booking_type = $("#cbo_booking_type").val();
		var page_link = 'requires/sample_fabric_production_status_controller.php?action=popup_onDblClick_booking&cbo_company_name=' + cbo_company_name + '&cbo_year=' + cbo_year + '&cbo_buyer_name=' + cbo_buyer_name + '&cbo_booking_type=' + cbo_booking_type;
        var title = 'Booking Search';
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=390px,center=1,resize=1,scrolling=0', '../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_str=this.contentDoc.getElementById("selected_booking").value;
			var booking = booking_str.split('_');
			$("#txt_booking_no").val(booking[0]);
			$('#hdn_requisition_id').val(booking[1]);
			$('#txt_style').val(booking[2]);
			/*
			if (theemail.value!="")
			{
				freeze_window(5);
				reset_form('fabricbooking_1','','booking_list_view','cbo_pay_mode,3*cbo_currency,2*cbo_ready_to_approved,2*txt_booking_date,<? echo date("d-m-Y"); ?>');
	
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/sample_booking_non_order_controller" );
				get_php_form_data( this.value+'_'+document.getElementById('txt_booking_no').value, 'check_dtls_part', 'requires/sample_booking_non_order_controller');
				print_button_setting();
				check_kniting_charge();
				reset_form('orderdetailsentry_2','booking_list_view','','','');
				show_list_view(theemail.value,'show_fabric_booking','booking_list_view','requires/sample_booking_non_order_controller','setFilterGrid(\'list_view\',-1)');
				set_button_status(1, permission, 'fnc_fabric_booking',1);
				release_freezing();
			}
			*/
		}
	}
	
	//for func_show
	function func_show()
	{
		var booking_no = $('#txt_booking_no').val();
		

		if( booking_no=="" )
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*From Date*To Date')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_name','Company')==false )
			{
				return;
			}
		}

		//alert('su..re');
		// if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false)
		// {
		// 	return;
		// }

		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_year*cbo_buyer_name*cbo_season_name*txt_requisition_no*hdn_requisition_id*cbo_booking_type*txt_booking_no*txt_date_from*txt_date_to',"../../");
		freeze_window(3);
		http.open("POST","requires/sample_fabric_production_status_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = func_show_reponse;	
	}
	
	function func_show_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText); 
			$('#report_container').html(reponse);		
			show_msg('3');
			release_freezing();
		}
	}	 


	function func_print(button_no) 
	{
		var master_ids = "";
		var total_tr=$('#tbl_list_search tr').length-1;
		for(i=1; i<=total_tr; i++)
		{
			try 
			{
				if ($('#tbl_'+i).is(":checked"))
				{
					master_id = $('#mstidall_'+i).val();
					if(master_ids=="")
						master_ids= master_id;
					else
						master_ids +='_'+master_id;
				}
			}
			catch(e){}
		}
		
		if(master_ids == '')
		{
			alert("Please Select At Least One Item");
			return;
		}
		
		freeze_window(3);
		print_report( $('#cbo_company_name').val()+'*'+master_ids, 'action_print_'+button_no, "requires/grey_roll_issue_to_process_multiple_challan_controller" );
		release_freezing(); 
		return;
	}
</script>
</head>
<body onLoad="set_hotkey();">
    <form id="">
        <div style="width:100%;" align="center"> 
			<? echo load_freeze_divs ("../../",'');  ?>  
            <fieldset style="width:1360px;">
                <legend>Search Panel</legend>
                <table class="rpt_table" width="1480px" cellpadding="0" cellspacing="0" align="center">
                   <thead>                    
                        <tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Year</th>
                            <th>Buyer Name</th>
                            <th>Season</th>
                            <th>Requisition No</th>
                            <th>Dealing Merchant</th>
                            <th>Booking Type</th>
                            <th>Booking No</th>
                            <th>Style</th>
                            <th id="search_text_td" class="must_entry_caption">Booking Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </tr>    
                    </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?php
                                echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_fabric_production_status_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );");
                            ?>
                        </td>
                        <td>
                            <?php
                            $selected_year = date('Y');
                            echo create_drop_down( "cbo_year", 90, $year,"", 1, "-- Select --", $selected_year, "" );
                            ?>
                        </td>                       
                        <td id="buyer_td">
                            <?php
                            echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "-- All Buyer --", $selected, "", 0, "");
                            ?>
                        </td>
                        <td id="season_td">
							<? echo create_drop_down( "cbo_season_name", 150, $blank_array,"", 1, "-- Select Season --", $selected, "" ); ?>
                        </td>
                        <td>
                            <input type="text"  name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:90px;" placeholder="Browse" onDblClick="func_onDblClick_requisition()" readonly />
                            <input type="hidden"  name="hdn_requisition_id" id="hdn_requisition_id" />
                        </td>
                        <td>
                            <input type="text"  name="txt_dealing_Merchant" id="txt_dealing_Merchant" class="text_boxes" style="width:90px;" placeholder="display" readonly />
                        </td>
                        <td>
                            <?php
                            echo create_drop_down( "cbo_booking_type", 100, $booking_type=array(1=>'With Order', 2=>'Without Order'),"", 1, "-- Select --", $selected_year, "" );
                            ?>
                        </td>                       
                        <td id="search_by_td">
                            <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:110px" placeholder="Browse" onDblClick="func_onDblClick_booking()" readonly />
                            <!--<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />-->
                        </td>
                        <td>
                            <input type="text"  name="txt_style" id="txt_style" class="text_boxes" style="width:90px;" placeholder="display" readonly />
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly >
                        </td>
                        <td>
                            <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="func_show()" />
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <? echo load_month_buttons(1); ?> 
                        </td>
                    </tr>
                </table> 
                <br />
            </fieldset>
        </div>
        <br>
        <div id="report_container" align="center" style="width:100%; margin: 0 auto;"></div>
     </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>