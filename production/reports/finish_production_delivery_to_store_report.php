<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finishing production Report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Didarul Alam
Creation date 	: 	14-08-2018
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
echo load_html_head_contents("Finish product Delivery Report","../../", 1, 1, $unicode,1,1); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	
	var tableFilters = 
	{
		col_operation: {
		id: ["value_total_qc_qty"],
		col: [27],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	} 

	function generate_report(rpt_type)
	{
		var txt_date_from= $("#txt_date_from").val().trim();
		var txt_date_to= $("#txt_date_to").val().trim();

		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var action = "";
		if(rpt_type==1)
		{
			action="report_generate";
		}
		else if(rpt_type==2)
		{
			action="report_generate2";
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action="+action+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*txt_mc_no*cbo_source*cbo_buyer_name*cbo_search_by*cbo_booking_type*txt_dynamic_search*hide_dynamic_id*cbo_year_selection*txt_date_from*txt_date_to*cbo_value_with',"../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/finish_production_delivery_to_store_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 			
			document.getElementById('report_container2').innerHTML=http.responseText;
			document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//setFilterGrid("tbl_dyeing",-1,tableFilters);
			show_msg('3');
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
		var cbo_booking_type = $("#cbo_booking_type").val();
		
		var page_link='requires/finish_production_delivery_to_store_report_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&cbo_booking_type='+cbo_booking_type;
		var title='Booking No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=430px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_booking_no").value;
			var booking_id=this.contentDoc.getElementById("hide_booking_id").value;
			$('#txt_dynamic_search').val(booking_no);
			$('#hide_dynamic_id').val(booking_id);
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

		var page_link='requires/finish_production_delivery_to_store_report_controller.php?action=sales_order_no_search_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year='+cbo_year;

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
	
	<!--BatchNumber -->
	function batchnumber()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var page_link="requires/finish_production_delivery_to_store_report_controller.php?action=batchnumbershow&company_name="+company_name; 
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
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission); ?>  		 
    <form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" > 
    <h3 style="width:1380px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1370px;">
                <table class="rpt_table" width="1370" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="160" class="must_entry_caption">Working Company</th> 
                            <th width="100">Location</th> 
                            <th width="100">Floor</th>
                            <th width="80">M/C Name</th>
                            <th width="80">Source</th>
                            <th width="110">Buyer</th>
                            <th width="80">Search by</th>
                            <th width="120">Booking type</th>
                            <th width="120">Value</th>
                            <th width="70" id="th_search">Batch No</th>
                            <th  width="160" class="">Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/finish_production_delivery_to_store_report_controller',this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/finish_production_delivery_to_store_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>                            
                        </td>
                        
                        <td id="location_td"> 
                            <?
                                echo create_drop_down( "cbo_location_id", 100, $blank_array,"", 1, "--Select Location--", 0, "",0 );
                            ?>
                        </td>
                        
                        <td id="floor_td"> 
                            <?
                                echo create_drop_down( "cbo_floor_id", 100, $blank_array,"", 1, "--Select Floor--", 0, "",0 );
                            ?>
                        </td>
                        
                        <td>
                            <input type="text" id="txt_mc_no" name="txt_mc_no" class="text_boxes" style="width:60px"  placeholder="Write" />
                        </td>

                        <td align="center">
							<? 
                            $source_arr=array(1=>'Inhouse',3=>'Outbound');
                            echo create_drop_down( "cbo_source", 70, $source_arr,"", 1, "--Select Source--", 0, "" );
                            ?>
                   	 	</td>

                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 110, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        
                        <td> 
                            <?
								$search_by_arr=array(1=>'Batch No',2=>'FSO No','3'=>'Booking No');
                                echo create_drop_down( "cbo_search_by", 80, $search_by_arr,"", 0, "--Select--", 1, "change_search_by(this.value);",0 );
                            ?>
                        </td>

                        <td> 
                            <?
								$search_by_arr=array(1=>'Main Fabric Booking',2=>'Partial Fabric Booking',3=>'Short Fabric Booking',4=>'Sample fabric booking with order',5=>'Sample fabric booking without order',);
                                echo create_drop_down( "cbo_booking_type", 120, $search_by_arr,"", 0, "--Select--", 1, "",0 );
                            ?>
                        </td>
                        <td> 
                           <? $valueWithArr=array(0=>'Value With 0',1=>'Value Without 0',2=>'Value Only 0');
                            echo create_drop_down( "cbo_value_with", 115, $valueWithArr, "", 0, "--  --", 0, "", "", ""); ?>
                        </td>
                        
                        <td>
                            <input type="text" id="txt_dynamic_search" name="txt_dynamic_search" class="text_boxes" style="width:70px;"  placeholder="Write/Brows"  onDblClick="batchnumber()"/>
                            <input type="hidden" name="hide_dynamic_id" id="hide_dynamic_id" readonly>
                        </td>

                        <td width="160">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px; float:left" placeholder="From Date" /> To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px;" placeholder="To Date" />				
                        </td>

                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:79px" class="formbutton" />
                            <input type="button" name="search" id="search" value="FSO" onClick="generate_report(2)" style="width:79px" class="formbutton" />
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