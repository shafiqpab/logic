<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create File Wise Grey Fabrics Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	Tipu
Creation date 	: 	18-10-2020
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
echo load_html_head_contents("File Wise Grey Fabrics Stock Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(rpt_type)
	{
		//alert(operation);
		if( form_validation('cbo_company_id*cbo_report_type*txt_date_from*txt_date_to','Company Name*Report Type*Date Form*Date To')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html(); 
		var cbo_company_id = $("#cbo_company_id").val();
		var cbo_report_type = $("#cbo_report_type").val();
		var cbo_buyer_id = $("#cbo_buyer_id").val();
		var txt_sales_order_no = $("#txt_sales_order_no").val();
		var txt_file_no = $("#txt_file_no").val();
		var txt_ref_no = $("#txt_ref_no").val();
		var txt_order_no = $("#txt_order_no").val();
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_batch_no = $("#txt_batch_no").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
	    var dataString = "&cbo_company_id="+cbo_company_id+"&cbo_report_type="+cbo_report_type+"&cbo_buyer_id="+cbo_buyer_id+"&txt_sales_order_no="+txt_sales_order_no+"&txt_file_no="+txt_file_no+"&txt_ref_no="+txt_ref_no+"&txt_order_no="+txt_order_no+"&txt_style_ref_no="+txt_style_ref_no+"&txt_batch_no="+txt_batch_no+"&from_date="+from_date+"&to_date="+to_date+"&report_title="+report_title+'&rpt_type='+rpt_type;

	    //alert(dataString);
		var data="action=generate_report"+dataString;
		//alert (data);
		freeze_window(1);
		http.open("POST","requires/ref_to_ref_transfer_report_sales_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			var tot_rows=reponse[2];
			//alert (reponse[2]);return;
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			//setFilterGrid("tbl_issue_status",-1,tableFilters);
	 		show_msg('3');
			release_freezing();
		}
	}
	
	

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>  		 
    <form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" > 
    <h3 style="width:1250px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1250px;">
                <table class="rpt_table" width="1250" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="120" class="must_entry_caption">Company</th>
                            <th class="must_entry_caption">Report Type</th>
                            <th>Buyer</th>
                          	<th>Sales Order NO</th>
                            <th>File No.</th>
                            <th>Ref. No.</th>
                            <th>Order No.</th>
                            <th>Style Ref:</th>
                            <th>Batch No</th>
                            <th class="must_entry_caption">Transaction Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr class="general">
                    	<td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 120, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/ref_to_ref_transfer_report_sales_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>
                        <td>
	                    	<?
							$report_type=array(1=>"Knit Grey Fabric",2=>"Knit Finish Fabric Textile",3=>"Knit Finish Fabric Garments");
	                        echo create_drop_down( "cbo_report_type", 120, $report_type,"", 1, "-- Select Type --", 0, "",0 );
	                        ?>
	                    </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td> 
                            <input type="text" id="txt_sales_order_no" name="txt_sales_order_no" class="text_boxes" style="width:80px" placeholder="Write"/>
                        </td>
						<td>
                            <input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:80px" placeholder="Write"/>
                        </td>
                        <td> 
                            <input type="text" id="txt_ref_no" name="txt_ref_no" class="text_boxes" style="width:80px" placeholder="Write"/>
                        </td>
                        <td> 
                            <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:80px" placeholder="Write"/>
                        </td><td> 
                            <input type="text" id="txt_style_ref_no" name="txt_style_ref_no" class="text_boxes" style="width:80px" placeholder="Write"/>
                        </td>
                        <td> 
                            <input type="text" id="txt_batch_no" name="txt_batch_no" class="text_boxes" style="width:80px" placeholder="Write"/>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:70px;" />
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;"/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    	<tr>
                            <td colspan="11" align="center"><? echo load_month_buttons(1);  ?></td>
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
