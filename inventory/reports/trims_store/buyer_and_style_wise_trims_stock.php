<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Buyer and Style Wise Trims Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	Tipu
Creation date 	: 	16-04-2020
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
echo load_html_head_contents(" Style Wise Trims Received Issue and Stock Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	var tableFilters = 
	{
		col_30: "none",
		col_operation: {
		id: ["total_opening_qnty","total_opening_amunt","grand_total_recv_qty","total_item_transfer_receive_qty","total_issue_return_qty","total_recv_qty","total_item_transfer_issue","total_issue_qty","total_receive_return_qty","grand_total_issue_qty","total_stock_qnty","total_stock_amount"],
		col: [12,13,14,15,16,17,18,19,20,21,22,24],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	var tableFilters2 = 
	{
		col_30: "none",
		col_operation: {
		id: ["value_total_opening_qnty","value_total_opening_amunt","value_grand_total_recv_qty","value_total_item_transfer_receive_qty","value_total_issue_return_qty","value_total_recv_qty","value_total_item_transfer_issue","value_total_issue_qty","value_total_receive_return_qty","value_grand_total_issue_qty","value_total_stock_qnty","value_total_stock_amount"],
		col: [10,11,12,13,14,15,16,17,18,19,20,22],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	function openmypage_style()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/buyer_and_style_wise_trims_stock_controller.php?data='+data+'&action=style_popup', 'style Search', 'width=480px,height=420px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				if (theemailid.value!="" || theemailval.value!="")
				{
					//alert (theemailid.value);
					freeze_window(5);
					$("#txt_style").val(theemailid.value);
					$("#txt_style_id").val(theemailval.value);
					release_freezing();
				}
			}
	}
	
	function fn_report_generated(operation)
	{
		var style=document.getElementById('txt_style').value;
		var txt_ref_no=document.getElementById('txt_ref_no').value;
		var cbo_item_group=document.getElementById('cbo_item_group').value;
		var txt_item_description_id=document.getElementById('txt_item_description_id').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
	
		if(cbo_item_group!="" || txt_item_description_id!="" || txt_job_no!="" || style!="" || txt_ref_no!="")
		{
			if(form_validation('cbo_company_id','Company')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_id*txt_date_from*txt_date_to','Company*Location*From date Fill*To date Fill')==false)
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();
		if(operation==5) var action="report_generate"; else var action="report_generate_kal";
		var data="action="+action+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_product_department*cbo_team_leader*txt_style*txt_style_id*txt_date_from*txt_date_to*txt_ref_no*cbo_store_name*txt_job_no*cbo_item_group*txt_item_description*txt_item_description_id*cbo_value_with*cbo_get_upto*txt_days*cbo_get_upto_qnty*txt_qnty*shipping_status',"../../../")+'&report_title='+report_title;
		// alert(data);return;

		freeze_window(3);
		http.open("POST","requires/buyer_and_style_wise_trims_stock_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;  
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("tbl_issue_status",-1);
	 		show_msg('3');
			if(response[2]==5) setFilterGrid("tbl_issue_status",-1,tableFilters);
			else setFilterGrid("tbl_issue_status",-1,tableFilters2);
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#tbl_issue_status tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="450px";
		
		$("#tbl_issue_status tr:first").show();
	}
	
	function openmypage_des(po_id,item_group,des_prod,action)
	{ //alert(des_prod)
		var companyID = $("#cbo_company_id").val();
		var popup_width='500px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/buyer_and_style_wise_trims_stock_controller.php?companyID='+companyID+'&po_id='+po_id+'&item_group='+item_group+'&action='+action+'&des_prod='+des_prod, 'Details Veiw', 'width='+popup_width+', height=250px,center=1,resize=0,scrolling=0','../../');
	}

	function openmypage_item_description()
	{
		if(form_validation('cbo_company_id','Company')==false){ return; }

		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_item_group').value;

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/buyer_and_style_wise_trims_stock_controller.php?action=item_description_popup&data='+data,'Item Description Popup', 'width=620px,height=400px,center=1,resize=0','../../')

		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("item_desc_id");
			var theemailv=this.contentDoc.getElementById("item_desc_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_item_description_id").value=response[0];
				document.getElementById("txt_item_description").value=theemailv.value;

				release_freezing();
			}
		}
	}

	function print_report_button_setting(report_ids)
	{
	    $('#search').hide();
	    $('#search2').hide();
	    var report_id=report_ids.split(",");
	    report_id.forEach(function(items)
	    {
	        if(items==108){$('#search').show();}
	        else if(items==256){$('#search2').show();}
        });
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>    		 
        <form name="greyissuestatus_1" id="greyissuestatus_1" autocomplete="off" > 
         <h3 style="width:1735px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <div id="content_search_panel" style="width:1735px" >
            <fieldset>  
                <table class="rpt_table" width="1735" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th class="must_entry_caption">Company</th>
                        <th>Store Name</th>
                        <th>Buyer</th>
                        <th>Product Dept.</th>
                        <th>Team Leader</th>
                        <th>IR</th>
                        <th>Job</th>
                        <th>Style</th>
                        <th>Item Group</th>
                        <th>Item Description</th>
                        <th>Value</th>
                        <th>Get Upto</th>
                        <th>Days</th>
                        <th>Get Upto</th>
                        <th>Qty.</th>
						<th>Shipment Status</th>
                        <th align="center" class="must_entry_caption">Date</th>
                       	<th><input type="reset" name="res" id="res" value="Reset" style="width:60px" onClick="$('#txt_style').val('');" class="formbutton" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/buyer_and_style_wise_trims_stock_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/buyer_and_style_wise_trims_stock_controller', this.value, 'load_drop_down_store', 'store_td' ); get_php_form_data(this.value,'print_button_variable_setting','requires/buyer_and_style_wise_trims_stock_controller' );");
									
                                ?>                            
                            </td>
							<td id="store_td">
							<?
								echo create_drop_down( "cbo_store_name", 110,$blank_array,"", 1, "-- Select Store --", $selected, "","","","","","");
							?> 
							</td>
                            <td id="buyer_td">
							<?
                                echo create_drop_down( "cbo_buyer_id", 130,$blank_array,"", 1, "-- Select Buyer --", $selected, "","","","","","");
                            ?> 
                          	</td>
                            <td>
                                <?
                                echo create_drop_down( "cbo_product_department", 100, $product_dept, "", 1, "-- Select --", $selected, "", "", "" );;
                                ?>
                            </td>
                            <td>
                                <?
                                echo create_drop_down( "cbo_team_leader", 120, "select id,team_leader_name from lib_marketing_team where project_type=1 and team_type in (0,1,2) and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team Leader --", $selected, "" );
                                ?>
                            </td>
                            <td >
                            	<input style="width:40px;" name="txt_ref_no" id="txt_ref_no" class="text_boxes" placeholder="Write"  />
                            </td>
                            <td >
                            	<input style="width:60px;" name="txt_job_no" id="txt_job_no" class="text_boxes" placeholder="Write Job"  />
                            </td>
                            <td>
                            	<input style="width:90px;" name="txt_style_id" id="txt_style_id" class="text_boxes" onDblClick="openmypage_style()" placeholder="Browse Style" readonly />
                                <input type="hidden" name="txt_style" id="txt_style" style="width:90px;"/>
                            </td>
                            <td>
								<?
                                	echo create_drop_down( "cbo_item_group", 140, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name","id,item_name", 0, "", $selected, "" );
                                ?>
                            </td>
                            <td align="center">
                                <input style="width:100px;" name="txt_item_description" id="txt_item_description" class="text_boxes" onDblClick="openmypage_item_description()"  placeholder="Browse Description"  />
                                <input type="hidden" name="txt_item_description_id" id="txt_item_description_id"/>
                            </td>
                            <td>
								<?
									$valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
									echo create_drop_down( "cbo_value_with", 105, $valueWithArr, "", 0, "--  --", 0, "", "", "");
                                ?>
                            </td>
                            <td>
								<?
									$get_upto=array(1=>"Greater Than",2=>"Less Than",3=>"Greater/Equal",4=>"Less/Equal",5=>"Equal");
									echo create_drop_down( "cbo_get_upto", 70, $get_upto,"", 1, "-- All --", 0, "",0 );
                                ?>
                            </td>
                            <td align="center">
                            	<input type="text" id="txt_days" name="txt_days" class="text_boxes_numeric" style="width:30px" value="" />
                            </td>
                            <td>
								<?
                                	echo create_drop_down( "cbo_get_upto_qnty", 70, $get_upto,"", 1, "-- All --", 0, "",0 );
                                ?>
                            </td>
                            <td>
                            	<input type="text" id="txt_qnty" name="txt_qnty" class="text_boxes_numeric" style="width:30px" value="" />
                            </td>
							<td align="center">	
	                    	<?				
								echo create_drop_down( "shipping_status", 100, $shipment_status,"", 1, "-- Select --", $selected, "",0,'2,3','','','','' );
							?>
		                    </td>
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:50px;" readonly/>
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y", time() - 86400);?>" class="datepicker" style="width:50px;" readonly/>
                            </td>
                            <td>                                
                                <input type="button" name="search" id="search" value="Show" onClick="fn_report_generated(5)" style="width:60px; display: none;" class="formbutton" />
                    </tbody>
                    <tfoot>                    
                        <tr>
                            <td colspan="16" align="center"><? echo load_month_buttons(1);  ?>
                            </td>
							<td></td>
                            <td><input type="button" align="right" name="search2" id="search2" value="Report 2" onClick="fn_report_generated(6)" style="width:60px;display: none;" class="formbutton" /></td>
                            
                        </tr>
                    </tfoot>
                </table> 
            </fieldset> 
            </div>
                <div id="report_container" align="center"></div>
                <div id="report_container2"></div> 
              
        </form>    


    </div>
</body>  
<script> set_multiselect('cbo_item_group','0','0','','0');</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
