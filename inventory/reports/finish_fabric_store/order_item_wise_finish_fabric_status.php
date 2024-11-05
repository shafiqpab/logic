<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Item Order Wise Finish Fabric Status Report
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	08-12-2018
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
echo load_html_head_contents("Item Order Wise Finish Fabric Status Report","../../../", 1, 1, $unicode,'',''); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	var tableFilters = 
	{
		//col_15: "none",
		col_operation: {
		id: ["value_total_rcv_qnty","value_total_rcv_rtn_quantity","value_total_issue_qnty","value_total_issue_rtn_quantity"],
		col: [15,16,17,18],
		operation: ["sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function generate_report()
	{
		if( form_validation('cbo_company_id*cbo_store_id','Company Name*Store')==false )
		{
			return;
		}

		if($("#txt_search_comm").val().trim() == "" && $("#txt_search_booking").val().trim() == "")
		{
			if( form_validation('txt_date_from*txt_date_to','Date Form*Date To')==false )
			{
				return;
			}
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_report_type*cbo_year*cbo_search_by*txt_search_comm*txt_search_booking*cbo_store_id*cbo_shipment_status*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		//alert (data);return;
		freeze_window(3);
		http.open("POST","requires/order_item_wise_finish_fabric_status_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
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
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/order_item_wise_finish_fabric_status_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			
			$('#txt_job_no').val(job_no);
			$('#hide_job_id').val(job_id);	 
		}
	}
	
	function openmypage_order()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/order_item_wise_finish_fabric_status_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=700px,height=420px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("order_no_id");
			var theemailv=this.contentDoc.getElementById("order_no_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_order_id").value=theemail.value;
			    document.getElementById("txt_order_no").value=theemailv.value;
				release_freezing();
			}
		}
	}
	
	function openmypage(prod_id,po_id,batch_id,store_id,date_form,date_to,item_category_id,action)
	{ 
	
		//alert(type);
		var companyID = $("#cbo_company_id").val();
		var popup_width='620px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_item_wise_finish_fabric_status_controller.php?companyID='+companyID+'&prod_id='+prod_id+'&po_id='+po_id+'&batch_id='+batch_id+'&store_id='+store_id+'&date_form='+date_form+'&date_to='+date_to+'&item_category_id='+item_category_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	
	function openmypage_ex_factory(po_id,rpt_type)
	{ 
		var companyID = $("#cbo_company_id").val();
		var action="";
		if(rpt_type==1)
		{
			action="open_exfactory";
			popup_width='350px';
		}
		else
		{
			action="open_order_exfactory";
			popup_width='500px';
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_item_wise_finish_fabric_status_controller.php?companyID='+companyID+'&po_id='+po_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=400px,center=1,resize=0,scrolling=0','../../');
	}
	
	function change_caption(type)
	{
		if(type==1)
		{
			$('#td_search').html('Enter Job');
		}
		else if(type==2)
		{
			$('#td_search').html('Enter Style');
		}
		else if(type==3)
		{
			$('#td_search').html('Enter Order');
		}
		else if(type==4)
		{
			$('#td_search').html('Enter File');
		}
		else if(type==5)
		{
			$('#td_search').html('Enter Ref.');
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>   		 
    <form name="ordewisefinishfabricstock_1" id="ordewisefinishfabricstock_1" autocomplete="off" > 
    <h3 style="width:1330px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1330px;">
                <table class="rpt_table" width="1330" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="150" class="must_entry_caption">Company</th>
                            <th width="150">Buyer</th> 
                            <th width="120">Category By</th>
                            <th width="90">Year</th>
                            <th width="100">Search By</th> 
                            <th width="115" id="td_search">Enter Job</th>
                            <th width="115">Enter Booking</th>
                            <th width="110" class="must_entry_caption">Store</th>
                            <th width="110">Shipment Status</th>
                            <th width="170" class="must_entry_caption">Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center" class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_item_wise_finish_fabric_status_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/order_item_wise_finish_fabric_status_controller',this.value, 'load_drop_down_store', 'store_td' );" );
                            ?>                            
                        </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
                                $report_arr=array(1=>'Knit Finish',2=>'Woven Finish');
                                echo create_drop_down( "cbo_report_type", 115, $report_arr, "", 0, "--  --", 0, "", "", "");
                            ?>
                        </td>
                        <td> 
                            <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 80, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                            ?>
                        </td>
                         <td> 
                            <?
								$search_by=array(1=>'Job',2=>'Style',3=>'Order',4=>'File',5=>'Ref.');
                                echo create_drop_down( "cbo_search_by", 90, $search_by,"", 0, "--Select--", 1, "change_caption(this.value);",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_search_comm" name="txt_search_comm" class="text_boxes" style="width:100px"  placeholder="Write" />
                        </td>
                        <td>
                            <input type="text" id="txt_search_booking" name="txt_search_booking" class="text_boxes" style="width:100px"  placeholder="Write" />
                        </td>
                        <td id="store_td">
                            <?
                            	echo create_drop_down( "cbo_store_id", 100, $blank_array,"", 1, "--Select Store--", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_shipment_status", 100, $shipment_status,"", 0, "", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:55px;" />&nbsp; To &nbsp;
                            <input type="text" name="txt_date_to" id="txt_date_to" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:55px;" />				
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:60px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="12" align="center"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                </table> 
            </fieldset> 
        </div>
             
    </form>    
</div>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>      
</body>  
<script>
	//set_multiselect('cbo_yarn_type*cbo_yarn_count','0*0','0*0','','0*0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
