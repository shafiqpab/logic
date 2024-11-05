<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Emb. Issue Callan Report.
Functionality	:	
JS Functions	:
Created by		:	Tajik 
Creation date 	: 	04-11-2017
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
echo load_html_head_contents("Embellishment Issue Challan Report", "../../", 1, 1,$unicode,'','');

?>	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
			
	function fn_report_generated()
	{
		if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false)
		{
			return;
		}	
			
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_order_no*txt_file_no*txt_internal_ref*txt_date_from*txt_date_to*cbo_source*cbo_supplier',"../../");
		freeze_window(3);
		http.open("POST","requires/embellishment_issue_challan_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;	
	}
	
	function fn_report_generated_reponse()
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

	function fn_with_source_report(operation) 
	{
	 	if(operation==4) // Print
		{
				var master_ids = ""; var total_tr=$('#tbl_list_search tr').length;
				for(i=1; i<total_tr; i++)
				{
					try 
					{
						if ($('#tbl_'+i).is(":checked"))
						{
							master_id = $('#mstidall_'+i).val();
							if(master_ids=="") master_ids= master_id; else master_ids +='_'+master_id;
						}
					}
					catch(e){}
				}
				if(master_ids=="")
				{
					alert("Please Select At Least One Item");
					return;
				}
			 freeze_window(3);
			 var report_title="Embellishment Delivery Challan";
			 print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title+'*'+$('#txt_delivery_date').val(), "delivery_challan_print", "requires/embellishment_issue_challan_controller" );
			 release_freezing(); 
			 return;
		}
	}
</script>
</head>
 
<body onLoad="set_hotkey();">
<form id="">
    <div style="width:100%;" align="center"> 

        <? echo load_freeze_divs ("../../",'');  ?>  

         <fieldset style="width:1000px;">
        	<legend>Search Panel</legend>
        	<div align="left">
        		<b>Delivery Date : </b> <input type="text"  name="txt_delivery_date" id="txt_delivery_date" class="datepicker" style="width:70px;" value="<? echo date("d-m-Y"); ?>" readonly>
        	</div>
            <table class="rpt_table" width="1300px" cellpadding="0" cellspacing="0" align="center">
               <thead>                    
                    <tr>
                        <th class="must_entry_caption">Company Name</th>
                        <th>Buyer Name</th>
						<th>Source</th>
						<th>Supplier</th>
                        <th>Job No</th>
                        <th>Order No</th>
                        <th>File No</th>
                        <th>Ref. No</th>
                        <th id="search_text_td" class="must_entry_caption">Embellishment Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr class="general">
                    <td width="150"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/embellishment_issue_challan_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>
                    </td>
					
                    <td width="110" id="buyer_td">
                        <? 
                            echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
					<td width="110"> 
                        <?
						$knitting_source_2 = array(1=>"In-house", 3=>"Out-BoundSubcontract");
                            echo create_drop_down( "cbo_source", 150,$knitting_source_2 ,"", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/embellishment_issue_challan_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );" );
                           
                        ?>
                    </td>
					<td width="110" id="supplier_td">
                        <? 
                            echo create_drop_down( "cbo_supplier", 110, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                        ?>
                    </td>
                    <td width="110">
                    	<input type="text"  name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:90px;"   placeholder="Write">
                    </td>
                    <td width="110">
                    	<input type="text"  name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:90px;"   placeholder="Write">
                    </td>
                    <td width="100">
                  	 <input type="text"  name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:90px;"   placeholder="Write">
                    </td>
                    <td width="100">
                  	 <input type="text"  name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:90px;"   placeholder="Write">
                    </td>
                    <td width="">
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly >&nbsp; To
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" readonly >
                    </td>
                    <td width="110">
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" />
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
                		<input id="print" class="formbutton" style="width:90px;" value="Print" name="print" onclick="fn_with_source_report(4)" type="button">
                	</td>
                </tr>
            </table> 
            <br />
        </fieldset>
    </div>
    <br>
    <div id="report_container" align="center" style="width:1190px; margin: 0 auto;"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
