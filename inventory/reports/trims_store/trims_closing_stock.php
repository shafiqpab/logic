<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Stock
				
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	21-10-2014
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
echo load_html_head_contents("Trims Closing  Stock","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters = 
	{
		col_40: "none",
		col_operation: {
		id: ["value_total_opening_td","value_total_receive_td","value_total_issue_return_td","value_total_transfer_in","value_total_receive_balance_td","value_total_issue_td","value_total_receive_return_td","value_total_transfer_out","value_total_issue_balance_td","value_total_closing_stock_td","value_total_closing_amnt"],
        //col: [7,8,9,10,11,12,13,14,15,16,18],
		col: [8,9,10,11,12,13,14,15,16,17,19],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
    var tableFilters2 = 
	{
		//col_16: "none",
		col_operation: {
		id: ["value_total_opening_td","value_total_opening_value_td","value_total_receive_td","value_total_receive_transfer","value_total_issue_return_td","value_total_receive_balance_td","value_total_receive_value_td","value_total_issue_td","value_total_issue_transfer","value_total_receive_return_td","value_total_issue_balance_td","value_total_issue_value_td","value_total_closing_stock_td","value_total_closing_amnt"],
		col: [6,7,8,9,10,11,12,13,14,15,16,17,18,20],
              //col: [4,5,6,7,8,9,10,11,13],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	var tableFilters6 = 
	{
		// col_40: "none",
		col_operation: {
		id: ["value_total_opening_td","value_total_receive_td","value_total_issue_return_td","value_total_transfer_in","value_total_receive_balance_td","value_total_issue_td","value_total_receive_return_td","value_total_transfer_out","value_total_issue_balance_td","value_total_closing_stock_td","value_total_closing_amnt"],
		//col: [5,6,7,8,9,10,11,12,14],
        col: [3,4,5,6,7,8,9,10,11,12,14],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function fnc_generate_report(operation)
	{
        if(operation != 3){
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
			{
				return;
			}
        }
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_group = $("#cbo_item_group").val();
		var item_description_id = $("#txt_item_description_id").val();
	
		var from_date 	= $("#txt_date_from").val();
		var to_date 	= $("#txt_date_to").val();
		var value_with 	= $("#cbo_value_with").val();
		var cbo_get_upto = $("#cbo_get_upto").val();
		var txt_days = $("#txt_days").val();
		var cbo_get_upto_qnty = $("#cbo_get_upto_qnty").val();
		var txt_qnty = $("#txt_qnty").val();
		var cbo_store_name = $("#cbo_store_name").val();
		
	
		if(cbo_get_upto!=0 && txt_days*1<=0)
		{
			alert("Please Insert Days.");	
			$("#txt_days").focus();
			return;
		}
		if(cbo_get_upto_qnty!=0 && txt_qnty*1<=0)
		{
			alert("Please Insert Qty.");	
			$("#txt_qnty").focus();
			return;
		}
	
		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_item_group="+cbo_item_group+"&item_description_id="+item_description_id+"&cbo_store_name="+cbo_store_name+"&from_date="+from_date+"&to_date="+to_date+"&get_upto="+cbo_get_upto+"&txt_days="+txt_days+"&get_upto_qnty="+cbo_get_upto_qnty+"&txt_qnty="+txt_qnty+"&value_with="+value_with+"&report_type="+operation;
		var data="action=generate_report"+dataString;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/trims_closing_stock_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse; 
	}
	
	function fnc_generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			//alert(reponse[0]);
			if(reponse[2]==7 || reponse[2]==8)
			{
				// $("#report_container2").html(reponse[0]);  
			}
			else
			{
				$("#report_container2").html(reponse[0]);  
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				
			}
 			
            if(reponse[2]==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			else if(reponse[2]==2)
			{
				setFilterGrid("table_body",-1,tableFilters2);
			}
			else if(reponse[2]==6)
			{
				setFilterGrid("table_body",-1,tableFilters6);
			}
 			else if(reponse[2]==7 || reponse[2]==8)
			{
				if(reponse[0]!='')
				{
					$('#aa1').removeAttr('href').attr('href','requires/'+reponse[1]);
					document.getElementById('aa1').click();
				}
			}
 			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(str)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(str==1 || str==2 || str==6)
		{
			$("#table_body tr:first").hide();
		}
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="350px";
		if(str==1 || str==2 || str==6)
		{
			$("#table_body tr:first").show();
		}
	}

	function openmypage_item_description()
	{
		if(form_validation('cbo_company_name','Company')==false){ return; }
		
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_group').value;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/trims_closing_stock_controller.php?action=item_description_popup&data='+data,'Item Description Popup', 'width=720px,height=400px,center=1,resize=0','../../')
		
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
        //alert(report_ids);
        $('#search').hide();
        $('#search1').hide();
        $('#search2').hide();
        $('#search3').hide();
        $('#search4').hide();
        $('#search5').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==222){$('#search').show();}
            else if(items==107){$('#search1').show();}
            else if(items==734){$('#search2').show();}
            else if(items==735){$('#search3').show();}
            else if(items==736){$('#search4').show();}
			else if(items==149){$('#search5').show();}
            });
    }



</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission);  ?>    		 
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" > 
        <div style="width:100%;" align="center">
            <h3 style="width:1110px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
            <div style="width:100%;" id="content_search_panel">
                <fieldset style="width:1110px;">
                    <table class="rpt_table" width="1110" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <thead>
                            <tr> 	 	
                                <th class="must_entry_caption">Company</th> 
                                <th>Item Group</th>                               
                                <th>Item Description</th>
                                <th>Store</th>
                                <th>Value</th>
                                <th class="must_entry_caption" colspan="2">Date</th>
                                <th>Get Upto</th>
                                <th>Days</th>
                                <th>Get Upto</th>
                                <th>Qty.</th>
                                <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" /></th>
                            </tr>
                        </thead>
                        <tr class="general">
                            <td>
								<? 
                                	echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/trims_closing_stock_controller',this.value, 'load_drop_down_store', 'store_td' ); get_php_form_data(this.value,'print_button_variable_setting','requires/trims_closing_stock_controller' );" );
                                ?>                            
                            </td>
                            <td> 
								<?
                                	echo create_drop_down( "cbo_item_group", 140, "select item_name,id from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name","id,item_name", 0, "", $selected, "" );
                                ?>
                            </td>
                            <td align="center">
                                <input style="width:110px;" name="txt_item_description" id="txt_item_description" class="text_boxes" onDblClick="openmypage_item_description()"  placeholder="Browse Description"  />
                                <input type="hidden" name="txt_item_description_id" id="txt_item_description_id" style="width:90px;"/>             
                            </td>
                             <td id="store_td">
								<?
                                    echo create_drop_down( "cbo_store_name", 122, $blank_array,"",1, "--Select store--", 1, "" );
                                ?>
                            </td>
                            <td> 
								<?   
									$valueWithArr=array(0=>'Value With 0',1=>'Value Without 0');
									echo create_drop_down( "cbo_value_with", 115, $valueWithArr, "", 0, "--  --", 0, "", "", "");
                                ?>
                            </td>
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time()-86400);?>" class="datepicker" style="width:65px" readonly/>
                            </td>
                            <td>
                                <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y",time()-86400);?>" class="datepicker" style="width:65px" readonly/>
                            </td>
                            <td> 
								<?   
									$get_upto=array(1=>"Greater Than",2=>"Less Than",3=>"Greater/Equal",4=>"Less/Equal",5=>"Equal");
									echo create_drop_down( "cbo_get_upto", 70, $get_upto,"", 1, "- All -", 0, "",0 );
                                ?>
                            </td>
                            <td align="center">
                            	<input type="text" id="txt_days" name="txt_days" class="text_boxes_numeric" style="width:30px" value="" />
                            </td>
                            <td> 
								<?
                                	echo create_drop_down( "cbo_get_upto_qnty", 70, $get_upto,"", 1, "- All -", 0, "",0 );
                                ?>
                            </td>
                            <td>
                            	<input type="text" id="txt_qnty" name="txt_qnty" class="text_boxes_numeric" style="width:30px" value="" />
                            </td>
                            <td>
                            	<input type="button" name="search" id="search" value="Show" onClick="fnc_generate_report(1)" style="width:70px" class="formbutton" />
                            </td>
                         </tr>
                        <tr>
                            <td colspan="12" align="center">
                                <? echo load_month_buttons(1); ?>
                                <input type="button" name="search1" id="search1" value="Report" onClick="fnc_generate_report(2)" style="width:70px" class="formbutton" />
                                <input type="button" name="search2" id="search2" value="Total Value" onClick="fnc_generate_report(3)" style="width:70px" class="formbutton" />
                                <input type="button" name="search3" id="search3" value="Total Value G.A" onClick="fnc_generate_report(4)" style="width:82px" class="formbutton" />
                                <input type="button" name="search4" id="search4" value="Value B.W" onClick="fnc_generate_report(5)" style="width:70px" class="formbutton" />
                                <input type="button" name="search5" id="search5" value="Summery" onClick="fnc_generate_report(6)" style="width:70px" class="formbutton" />
                                <input type="button" name="search" id="search" value="Export to Excel" onClick="fnc_generate_report(7)" style="width:100px" class="formbutton" />
                                <input type="button" name="search" id="search" value="Show Excel" onClick="fnc_generate_report(8)" style="width:100px" class="formbutton" />
                                 <a id="aa1" href="" style="text-decoration:none" download hidden>BB</a>
                            </td>
                        </tr>
                    </table> 
                </fieldset> 
            </div>
        </div>
        <br /> 
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div> 
    </form>    
    </div>    
</body> 
	<script> set_multiselect('cbo_item_group','0','0','','0');</script> 
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script> $("#cbo_value_with").val(0);</script> 
</html>
