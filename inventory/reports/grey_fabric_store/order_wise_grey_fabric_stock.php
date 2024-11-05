<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Order Wise Grey Fabrics Stock Report
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	03-04-2014
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
echo load_html_head_contents("Order Wise Grey Fabrics Stock Report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


	var tableFilters_2 = 
	{
		//col_30: "none",
		col_operation: {
		id: ["value_tot_booking_qty","value_tot_recv_qty","value_tot_iss_ret_qty","value_tot_trans_in_qty","value_grand_tot_recv_qty","value_tot_iss_qty","value_tot_rec_ret_qty","value_tot_trans_out_qty","value_grand_tot_iss_qty","value_grand_stock_qty"],
		//col: [10,16,17,18,19,20,21,22,23,24],
		// col: [10,17,18,19,20,21,22,23,24,25],
		col: [10,19,20,21,22,23,24,25,26,27],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 



	function generate_report(rpt_type)
	{
		if( form_validation('cbo_company_id*txt_date_from_trans','Company Name*Date')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		// if(rpt_type==6){

		// }
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_job_id*cbo_search_by*txt_booking_no*txt_search_comm*cbo_presentation*txt_date_from_trans*cbo_sock_for*cbo_value_with*cbo_date_cat*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/order_wise_grey_fabric_stock_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+')" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			//var tableFilters='';
			//alert(reponse[2]);
			if(reponse[2]==2)
			{
				setFilterGrid("table_body",-1,tableFilters_2);
			}
			else if(reponse[2]==3)
			{
				setFilterGrid("table_body",-1);
			}
			
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window(type)
	{
		if(type==2||type==3){
			$(".flt").css("display","none");
		}
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		if(type==2||type==3){
			$(".flt").css("display","block");
		}
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="280px";
	}
	function generate_report_exel_only(rpt_type)
	{
		if( form_validation('cbo_company_id*txt_date_from_trans','Company Name*Date')==false )
		{
			return;
		}
	  	var report_title=$( "div.form_caption" ).html();

		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_job_id*cbo_search_by*txt_booking_no*txt_search_comm*cbo_presentation*txt_date_from_trans*cbo_sock_for*cbo_value_with',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;

		  http.open("POST","requires/order_wise_grey_fabric_stock_controller.php",true);
		  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		  http.send(data);
		  http.onreadystatechange = generate_report_reponse_exel_only;
		  freeze_window(2);
	}
	function generate_report_reponse_exel_only()
	{
	  if(http.readyState == 4) 
	  {  
	    var reponse=trim(http.responseText).split("####");

	    if(reponse!='')
	    {
	      $('#aa1').removeAttr('href').attr('href','requires/'+reponse[1]);
	      document.getElementById('aa1').click();
	    }
	    show_msg('3');
	    release_freezing();
	  }

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
		var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/order_wise_grey_fabric_stock_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);	 
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
		var cbo_month_id = $("#cbo_month").val();
		var txt_job_no = $("#txt_job_no").val();
		var page_link='requires/order_wise_grey_fabric_stock_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&txt_job_no='+txt_job_no;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=430px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			//alert(booking_no);
			$('#txt_booking_no').val(booking_no);
			//$('#txt_job_id').val(job_id);	 
		}
	}
	
	/*function openmypage_order()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/order_wise_grey_fabric_stock_controller.php?action=order_no_popup&data='+data,'Order No Popup', 'width=630px,height=420px,center=1,resize=0','../../')
		
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
	}*/
	
	function openpage_fabric_booking(action,po_id)
	{ 
		 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_grey_fabric_stock_controller.php?action='+action+'&po_id='+po_id, 'Booking Details Info', 'width=900px,height=370px,center=1,resize=0,scrolling=0','../../');
	}
	
	function change_caption(type)
	{
		$('#txt_search_comm').val('');
		if(type==1)
		{
			$('#td_search').html('Enter Style');
		}
		else if(type==2)
		{
			$('#td_search').html('Enter Order');
		}
		else if(type==3)
		{
			$('#td_search').html('Enter File');
		}
		else if(type==4)
		{
			$('#td_search').html('Enter Ref.');
		}
	}
	function openmypage_delivery(orderID,programNo,prodID,from_date,to_date,popup_width,action,type,entry_form)
	{ //alert(orderID);
		var companyID = $("#cbo_company_id").val();
		var popup_width=popup_width;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_grey_fabric_stock_controller.php?companyID='+companyID+'&orderID='+orderID+'&programNo='+programNo+'&prodID='+prodID+'&from_date='+from_date+'&to_date='+to_date+'&action='+action+'&popup_width='+popup_width+'&type='+type+'&entry_form='+entry_form, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	function openmypage_delivery_all(orderID,programNo,prodID,entryForm,from_date,to_date,popup_width,action,type)
	{ //alert(entryForm);
		var companyID = $("#cbo_company_id").val();
		var to_date = $("#txt_date_from_trans").val();
		var year_id = $("#cbo_year").val();
		var popup_width=popup_width;
		if(type==1)
		{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_grey_fabric_stock_controller.php?companyID='+companyID+'&orderID='+orderID+'&programNo='+programNo+'&prodID='+prodID+'&entryForm='+entryForm+'&from_date='+from_date+'&to_date='+to_date+'&year_id='+year_id+'&action='+action+'&popup_width='+popup_width+'&type='+type, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');

		}
		else if (type==2) 
		{
			var issue_return_data=from_date;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_grey_fabric_stock_controller.php?companyID='+companyID+'&orderID='+orderID+'&programNo='+programNo+'&prodID='+prodID+'&entryForm='+entryForm+'&from_date='+from_date+'&to_date='+to_date+'&year_id='+year_id+'&action='+action+'&popup_width='+popup_width+'&type='+type+'&issue_return_data='+issue_return_data, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
		}
		else if (type==3) 
		{
			var transIn_data=from_date;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_grey_fabric_stock_controller.php?companyID='+companyID+'&orderID='+orderID+'&programNo='+programNo+'&prodID='+prodID+'&entryForm='+entryForm+'&from_date='+from_date+'&to_date='+to_date+'&year_id='+year_id+'&action='+action+'&popup_width='+popup_width+'&type='+type+'&transIn_data='+transIn_data, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
		}
		else if(type==4)
		{
			var other_data=from_date;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_grey_fabric_stock_controller.php?companyID='+companyID+'&orderID='+orderID+'&programNo='+programNo+'&prodID='+prodID+'&entryForm='+entryForm+'&from_date='+from_date+'&to_date='+to_date+'&year_id='+year_id+'&action='+action+'&popup_width='+popup_width+'&type='+type+'&other_data='+other_data, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');

		}
		else if (type==5) 
		{
			var transOut_data=from_date;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_grey_fabric_stock_controller.php?companyID='+companyID+'&orderID='+orderID+'&programNo='+programNo+'&prodID='+prodID+'&entryForm='+entryForm+'&from_date='+from_date+'&to_date='+to_date+'&year_id='+year_id+'&action='+action+'&popup_width='+popup_width+'&type='+type+'&transOut_data='+transOut_data, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
		}
		else if(type==6)
		{
			var other_data=from_date;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_grey_fabric_stock_controller.php?companyID='+companyID+'&orderID='+orderID+'&programNo='+programNo+'&prodID='+prodID+'&entryForm='+entryForm+'&from_date='+from_date+'&to_date='+to_date+'&year_id='+year_id+'&action='+action+'&popup_width='+popup_width+'&type='+type+'&other_data='+other_data, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');

		}
		
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>  		 
    <form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" > 
    <h3 style="width:1880px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1880px;">
                <table class="rpt_table" width="1880" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th class="must_entry_caption">Company</th>                                
                            <th>Buyer</th>
                            <th>Year</th>
                          	<th>Job</th>
                            <th>Booking No</th>
                            <th>Search By</th>
                            <th id="td_search">Enter Order</th>
                            <th>Presentation</th>
                            <th>Value</th>
                            <th class="must_entry_caption">Date</th>
                            <th>Stock For</th>
                            <th>Date Category</th>
                            <th>Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_wise_grey_fabric_stock_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value, 'company_wise_report_button_setting','requires/order_wise_grey_fabric_stock_controller');" );
                            ?>                            
                        </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 130, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
								echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                            ?>
                        </td>
						<td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:70px" onDblClick="openmypage_job();" placeholder="Browse/Write" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                        </td>
                        <td>
                            <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:80px" onDblClick="openmypage_booking();" placeholder="Browse/Write" />
                            <input type="hidden" id="txt_booking_id" name="txt_booking_id"/>
                        </td>
                        <td> 
                            <?
								$search_by_arr=array(1=>"Style",2=>"Order",3=>"File",4=>"Ref.");
								echo create_drop_down( "cbo_search_by", 70, $search_by_arr,"", 0,"-Select-", 2, "change_caption(this.value);","","" );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_search_comm" name="txt_search_comm" class="text_boxes" style="width:80px" placeholder="Write"/>
                        </td>
                        <td>
                            <?
								$presentation=array(1=>"Order Wise",2=>"Order/Rack & Shelf Wise",3=>"Style Wise",4=>"Buyer Wise",5=>"Order/Floor, Room, Rack & Shelf Wise");
                                echo create_drop_down( "cbo_presentation", 100, $presentation,"", 0, "", 0, "",0 );
                            ?>
                        </td>
                         <td>
                            <?   
                                $valueWithArr=array(1=>'Value With 0',2=>'Value Without 0');
                                echo create_drop_down( "cbo_value_with", 100, $valueWithArr,"",0,"",1,"","","");
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from_trans" id="txt_date_from_trans" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:60px;" readonly/>				
                        </td>
                        <td>
                            <?
								$stock_for_arr=array(1=>"Running Order",2=>"Cancelled Order",3=>"Left Over");
                                echo create_drop_down( "cbo_sock_for", 100, $stock_for_arr,"", 1, "Select", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <?
								$date_cate_arr=array(1=>"Ship Date",2=>"Cancel Date");
                                echo create_drop_down( "cbo_date_cat", 100, $date_cate_arr,"", 1, "Select", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px;" readonly/>&nbsp;To&nbsp;
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px;" readonly/>				
                        </td>
                        <td>
                            <input type="button" name="show" id="show" value="Show" onClick="generate_report(1)" style="width:45px" class="formbutton" />
                            <input type="button" name="show2" id="show2" value="Show 2" onClick="generate_report(6)" style="width:45px" class="formbutton" />
                            <input type="button" name="order_wise" id="order_wise" value="Order Wise" onClick="generate_report(2)" style="width:70px" class="formbutton" />
                            <input type="button" name="order_wise_2" id="order_wise_2" value="Order Wise 2" onClick="generate_report(3)" style="width:79px" class="formbutton" />
                            <input type="button" name="order_wise_3" id="order_wise_3" value="Order Wise 3" onClick="generate_report(4)" style="width:79px" class="formbutton" />
                            <input type="button" name="order_wise_4" id="order_wise_4" value="Order Wise 4" onClick="generate_report(5)" style="width:79px" class="formbutton" />
                            <input type="button" name="order_wise_2_excel" id="order_wise_2_excel" value="Order Wise 2 Excel" onClick="generate_report_exel_only(3)" style="width:105px" class="formbutton" />
							<input type="button" name="order_wise_4" id="order_wise_4" value="Show 7" onClick="generate_report(7)" style="width:79px" class="formbutton" />
            				<!--<a href="" id="aa1"></a>-->
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="13" align="center"><? echo load_month_buttons(1);  ?></td>
                        <td></td>
                    </tr>
                </table> 
            </fieldset> 
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>   
    </form>    
</div>    
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_year','0','0','0');
	set_multiselect('cbo_year','0','1','<?= date("Y",time());?>','0');
</script>
</html>
