<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finishing production Report
				
Functionality	:	
JS Functions	:
Created by		:	Rehan Uddin
Creation date 	: 	22-07-2019
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
echo load_html_head_contents("Finishing Production Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	
	var tableFilters = 
	{
		col_operation: {
		id: ["gr_rec_qty","gr_issue_rtn","gr_tr_in","gr_ttl_recv","gr_issue_qty","gr_rec_rtn","gr_tr_out","gr_ttl_issue","gr_stock"],
		col: [25,26,27,28,29,30,31,32,33 ],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	function date_mixed_chk(type)
	{
		var txt_date_from= $("#txt_date_from").val().trim();
		var txt_date_to= $("#txt_date_to").val().trim();
		if(txt_date_from && txt_date_to)
		{
			txt_date_from_arr=txt_date_from.split("-");
			txt_date_to_arr=txt_date_to.split("-");
			if(txt_date_from_arr[1].trim()!=txt_date_to_arr[1].trim())
			{
				if(type==1)$("#txt_date_from").val('');
				else  $("#txt_date_to").val('');
			}

		}
	}

	function generate_report(rpt_type)
	{
		var txt_date_from= $("#txt_date_from").val().trim();
		var txt_date_to= $("#txt_date_to").val().trim();

		if( form_validation('cbo_company_id*cbo_item_cat','Company Name*Item Category')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*hide_dynamic_id*txt_dynamic_search*cbo_within_group*cbo_item_cat*cbo_year*cbo_po_company_id*cbo_buyer_id*cbo_search_by*cbo_year_selection*cbo_batch_status*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/batch_wise_fabric_process_loss_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			setFilterGrid("table_body",-1,tableFilters );
			release_freezing();
		}
	}

	function openmypage_booking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		
		var page_link='requires/batch_wise_fabric_process_loss_report_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Booking No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=430px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
			var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
			$('#txt_dynamic_search').val(booking_no);
			$('#hide_dynamic_id').val(booking_id);
		}
	}

	function openmypage_transfer_in_out(data,type)
	{
		 
		var page_link='requires/batch_wise_fabric_process_loss_report_controller.php?action=transfer_in_popup&data='+data+"&type="+type;
		var title='Transfer In Popup';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=380px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			 
		}
	}

	function openmypage_finish_qty(data )
	{
		 
		var page_link='requires/batch_wise_fabric_process_loss_report_controller.php?action=finish_qtypopup&data='+data ;
		var title='Transfer In Popup';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=380px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			 
		}
	}
	function openmypage_receive_qty(data )
	{
		 
		var page_link='requires/batch_wise_fabric_process_loss_report_controller.php?action=receive_qtypopup&data='+data ;
		var title='Receive Popup';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1190px,height=380px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			 
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	
	function change_search_by(type)
	{
		 
		$('#txt_dynamic_search').val('');
		$('#hide_dynamic_id').val('');
		
		if(type==1)
		{
			$('#th_search').html('Batch No');
			$('#txt_dynamic_search').attr('placeholder','Browse/Write');
			$('#txt_dynamic_search').attr('readonly',false);
			$('#txt_dynamic_search').attr("onDblClick","batchnumber();");
		}
		else if(type==2)
		{
			$('#th_search').html('FSO No');
			$('#txt_dynamic_search').attr('placeholder','Browse/Write');
			$('#txt_dynamic_search').attr('readonly',false);
			$('#txt_dynamic_search').attr("onDblClick","openmypage_sales_order();");
		}
		else if(type==3)
		{
			$('#th_search').html('Booking No');
			$('#txt_dynamic_search').attr('placeholder','Browse');
			$('#txt_dynamic_search').attr('readonly',true);
			$('#txt_dynamic_search').attr("onDblClick","openmypage_booking();");
		}
		
	}
	
	function openmypage_sales_order()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year = $("#cbo_year").val();

		var page_link='requires/batch_wise_fabric_process_loss_report_controller.php?action=sales_order_no_search_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year='+cbo_year;

		var title='Sales Order No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=420px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_data=this.contentDoc.getElementById("hidden_booking_data").value;
			if (order_data!="")
			{
				var exdata=order_data.split("**");
				$('#txt_dynamic_search').val(exdata[1]);
				$('#hide_dynamic_id').val(exdata[0]);	 
			}
		}
	}
	
	//<!--BatchNumber -->
	function batchnumber()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var page_link="requires/batch_wise_fabric_process_loss_report_controller.php?action=batchnumbershow&company_name="+company_name; 
		var title="Batch Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var batch=theemail.split("_");
			document.getElementById('hide_dynamic_id').value=batch[0];
			document.getElementById('txt_dynamic_search').value=batch[1];
			release_freezing();
		}
	}	
	function disable_date_range(data)
	{
		 
		if(data==1)
		{
			$(".date_range_cls").css("display","none");
		}
		else
		{
			$(".date_range_cls").css("display","block");
		}
	}

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission); ?>  		 
    <form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" > 
    <h3 style="width:1220px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1220px;">
                <table class="rpt_table" width="1210" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="160" class="must_entry_caption">Company Name</th> 
                            <th width="100" class="must_entry_caption">Item Category</th> 
                            <th width="100">Within Group</th>
                            <th width="160">PO Company</th>                                                    
                            <th width="110">Po Buyer</th>
                            <th width="65">Year</th> 
                            <th width="100">Batch Status</th> 
                            <th width="80">Search by</th>
                            <th width="70" id="th_search">Sales Order No</th>
                            <th  width="160"  class="date_range_cls">Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>                            
                        </td>
                        
                        <td> 
                            <?
                            $item_cat_arr=array(1=>"Grey Fabric",2=>"Knit Finish Fabric");
                                echo create_drop_down( "cbo_item_cat", 100, $item_cat_arr,"", 0, "--Select Item--", 2, "disable_date_range(this.value)",0 );
                            ?>
                        </td>
                        <td align="center">
							<? 
                           
                            echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 1, "--Select Group--", 0, "" );
                            ?>
                   	 	</td>
                   	 	  <td>
                            <? 
                               echo create_drop_down( "cbo_po_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/batch_wise_fabric_process_loss_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>



                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 110, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td id="extention_td">
                        	<?
                        	echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y"), "",0,"" );
                        	?>
                        </td>
						<td align="center">
							<? 
							$cbo_batch_status = array("1"=>"Complete","2"=>"Incomplete");
                           
                            echo create_drop_down( "cbo_batch_status", 100, $cbo_batch_status,"", 1, "-- All --", 1, "" );
                            ?>
                   	 	</td>
                        
                        <td> 
                            <?
								//$search_by_arr=array(2=>'FSO No',1=>'Batch No','3'=>'Booking No');
								$search_by_arr=array(2=>'FSO No');
                                echo create_drop_down( "cbo_search_by", 80, $search_by_arr,"", 0, "--Select--", 0, "change_search_by(this.value);",0 );
                            ?>
                        </td>
                        
                        <td>
                            <input type="text" id="txt_dynamic_search" name="txt_dynamic_search" class="text_boxes" style="width:70px;"  placeholder="Write/Brows"  onDblClick="openmypage_sales_order()"/>
                            <input type="hidden" name="hide_dynamic_id" id="hide_dynamic_id" readonly>
                        </td>

                        <td width="160" class="date_range_cls">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px; float:left" placeholder="From Date" onchange="date_mixed_chk(1);" /> To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" onchange="date_mixed_chk(2);" class="datepicker" style="width:55px;" placeholder="To Date" />				
                        </td>

                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:79px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                		<td colspan="13" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                	</tr>
                </table> 
            </fieldset> 
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>   
    </form>    
</div>    
<div style="display:none" id="data_panel"></div>
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
