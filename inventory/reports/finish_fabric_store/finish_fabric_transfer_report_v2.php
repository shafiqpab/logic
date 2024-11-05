<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Fabric Transfer Report
				
Functionality	:	
JS Functions	:
Created by		:	Abdul Barik Tipu
Creation date 	: 	09-04-2023
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
echo load_html_head_contents("Finish Fabric Transfer Report","../../../", 1, 1, $unicode,1,1); 

?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";

	function generate_report(type)
	{
		var cbo_from_job 	=  trim($("#cbo_from_job").val());
		var cbo_to_job 		=  trim($("#cbo_to_job").val());
		var validate_id 	= "";
		var validate_msg 	= "";
		if(cbo_from_job =="" && cbo_to_job =="")
		{
			validate_id += "*txt_date_from*txt_date_to";
			validate_msg += "*Date From*Date To";
		}
		if (form_validation('cbo_company_id'+validate_id,'Comapny Name'+validate_msg)==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_buyer_id*cbo_from_store*cbo_to_store*txt_date_from*txt_date_to*cbo_transfer_criteria*cbo_year*cbo_from_job*cbo_to_job',"../../../")+'&report_title='+report_title+'&type='+type;
		
		freeze_window(3);
		http.open("POST","requires/finish_fabric_transfer_report_v2_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("####");

			
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			var tableFilters = {
				col_operation: {
				id: ["value_trans_in_qty","value_trans_out_qty"],
				col:  [21,22],
				operation: ["sum","sum"],
				write_method: ["innerHTML","innerHTML"]
				}
			}
			setFilterGrid("scanning_table",-1,tableFilters);
			
			show_msg('3');
			release_freezing();
		}
	} 
	
	function new_window()
	{
		if(document.getElementById('scanning_table'))
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#scroll_body tr:first').hide();
		}

		var w = window.open("Surprise", "#");

		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		if(document.getElementById('scanning_table'))
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="397px";
	        $('#scroll_body tr:first').show();
	    }
	}
	
</script>
</head>

<body >
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>   		 
    <form name="knitting_bill_report_1" id="knitting_bill_report_1" autocomplete="off" > 
    <h3 style="width:1270px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1270px;">
                <table class="rpt_table" width="1250" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th class="must_entry_caption">Company</th>                                
                            <th>Location</th>
                            <th>Buyer</th>
                            <th>Transfer Criteria</th>
                            <th>Year</th>
                            <th>From Job</th>
                            <th>To Job</th>
                            <th>From Store</th>
                            <th>To Store</th>
                            <th class="must_entry_caption" colspan="2">Transfer Date</th>
                            <th width="150">
                            	<input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('knitting_bill_report_1','report_container*report_container2','','','','');" />
                            </th>
                        </tr>
                    </thead>
                    <tbody>

	                    <tr class="general">
	                        <td>
	                            <? 
	                               echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/finish_fabric_transfer_report_v2_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/finish_fabric_transfer_report_v2_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/finish_fabric_transfer_report_v2_controller', this.value, 'load_drop_to_store', 'to_store_td' );load_drop_down( 'requires/finish_fabric_transfer_report_v2_controller', this.value, 'load_drop_from_store', 'from_store_td' );" );
	                            ?>                            
	                        </td>
	                        <td id="location_td">
	                        	<?
									echo create_drop_down( "cbo_location_id", 130,$blank_array,"", 1, "-- Select Location--", $selected, "","","","","","");
	                            ?>
	                   		 </td>
	                        <td id="buyer_td">
								<?
									echo create_drop_down( "cbo_buyer_id", 130,$blank_array,"", 1, "-- Select Buyer--", $selected, "","","","","","");
                                ?> 
	                        </td>
	                        <td align="center" >
								<?
	                                echo create_drop_down("cbo_transfer_criteria", 130,$item_transfer_criteria,"", 1,"-- Select --",'0',"",'','1,2,4');
	                            ?>
							</td>
							<td align="center" >
								<? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?>
							</td>
							<td> 
	                            <input type="text" name="cbo_from_job" id="cbo_from_job" class="text_boxes" style="width:80px">
	                        </td>
	                        <td> 
	                            <input type="text" name="cbo_to_job" id="cbo_to_job" class="text_boxes" style="width:80px">
	                        </td>	                        	                        
	                        <td id="from_store_td">
	                            <?
	                                echo create_drop_down( "cbo_from_store", 130, $blank_array,"", 1, "--Select store--", 0, "" );
	                            ?>	
	                        </td>
	                       
	                        <td id="to_store_td">
	                            <?
	                                echo create_drop_down( "cbo_to_store", 130, $blank_array,"", 1, "--Select store--", 0, "" );
	                            ?>	
	                        </td>
	                        <td>
	                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker"  placeholder="From Date" style="width:50px"/>
	                        </td>
	                        <td>
	                            
	                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" placeholder="To Date" style="width:50px"/>
	                        </td>
	                        <td>
	                        	<table>
	                        		<tr>
	                        			<td><input type="button" name="search" id="search" value="Show" onClick="generate_report(1);" style="width:70px;" class="formbutton" /></td>
	                        		</tr>
	                        	</table>
	                        </td>
	                    </tr>
	                    <tr>
							<td colspan="11" align="center" width="100%"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
                </table> 
            </fieldset> 
        </div>
            <div id="report_container" align="center"></div>
            <div id="report_container2"></div>   
    </form>    
</div>    
</body>  

<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
