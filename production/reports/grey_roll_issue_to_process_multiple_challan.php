<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Emb. Issue Callan Report.
Functionality	:	
JS Functions	:
Created by		:	Md. Nuruzzaman
Creation date 	: 	04.11.2020
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
echo load_html_head_contents("Grey Roll Issue To Process Multiple Challan", "../../", 1, 1,$unicode,'','');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1)
		window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';
			
	function func_show()
	{
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false)
		{
			return;
		}

		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_service_source*cbo_service_company*txt_challan_no*txt_date_from*txt_date_to',"../../");
		freeze_window(3);
		http.open("POST","requires/grey_roll_issue_to_process_multiple_challan_controller.php",true);
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

	function fnc_checkbox_check(rowNo)
	{
		var isChecked=$('#tbl_'+rowNo).is(":checked");

		var issue_to=$('#issue_to_'+rowNo).val();
		var emb_source=$('#emb_source_'+rowNo).val();

		if(isChecked==true)
		{
			var tot_row=$('#tbl_list_search tr').length-1;
			for(var i=1; i<=tot_row; i++)
			{ 
				if(i!=rowNo)
				{
					try 
					{
						if ($('#tbl_'+i).is(":checked"))
						{
							var issue_toCurrent=$('#issue_to_'+i).val();
							var emb_sourceCurrent=$('#emb_source_'+i).val();
							if( (issue_to!=issue_toCurrent) || (emb_source!=emb_sourceCurrent) )					
							{
								alert("Please Select Same  Source and  Serving Company");
								$('#tbl_'+rowNo).attr('checked',false);
								return;
							}
						}
					}
					catch(e){}
				}
			}
		}
	}
	
	function func_check_all()
	{
		var isChecked=$('#check_all').is(":checked");
		var tot_row=$('#tbl_list_search tr').length-1;
		if(isChecked==true)
		{
			for(var i=1; i<=tot_row; i++)
			{
				$('#tbl_'+i).attr('checked',true);
			}
		}
		else
		{
			for(var i=1; i<=tot_row; i++)
			{
				$('#tbl_'+i).attr('checked',false);
			}
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
            <fieldset style="width:820px;">
                <legend>Search Panel</legend>
                <table class="rpt_table" width="820px" cellpadding="0" cellspacing="0" align="center">
                   <thead>                    
                        <tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Service Source</th>
                            <th>Service Company</th>
                            <th>Challan No</th>
                            <th id="search_text_td" class="must_entry_caption">Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </tr>    
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td width="150"> 
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
                        <td width="110">
							<?
                                echo create_drop_down( "cbo_service_source", 110, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/grey_roll_issue_to_process_multiple_challan_controller',this.value+'**'+$('#cbo_company_name').val(),'load_drop_down_knitting_com','dyeing_company_td' );","","1,3" );
                            ?>
                        </td>
                        <td width="150" id="dyeing_company_td">
                            <?
                                echo create_drop_down( "cbo_service_company", 150, $blank_array,"", 1, "-- Select --", $selected, "","","" );
                            ?>
                        </td>
                        <td width="100">
                            <input type="text"  name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:90px;"   placeholder="Write">
                        </td>
                        <td width="">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly >
                        </td>
                        <td width="100">
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
                    <tr align="center">
                        <td>
                            <input id="print_1" class="formbutton" style="width:90px;" value="Print-1" name="print_1" onClick="func_print(1)" type="button">
                            <input id="print_2" class="formbutton" style="width:90px;" value="Print-2" name="print_2" onClick="func_print(2)" type="button">
                        </td>
                    </tr>
                </table> 
                <br />
            </fieldset>
        </div>
        <br>
        <div id="report_container" align="center" style="width:860px; margin: 0 auto;"></div>
     </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>