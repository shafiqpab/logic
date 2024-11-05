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

	function generate_report(rpt_type)
	{
            if(rpt_type != 2){
		if( form_validation('cbo_company_id*txt_date_from','Company Name*Date')==false )
		{
			return;
		}
            }
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_job_id*cbo_search_by*txt_search_comm*cbo_presentation*txt_date_from*cbo_sock_for*cbo_value_with*cbo_store_id',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/job_wise_grey_fabric_stock_consolidated_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			//var tableFilters='';
			if(reponse[2]==3){setFilterGrid("table_body",-1);}
			
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
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
		var page_link='requires/job_wise_grey_fabric_stock_consolidated_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
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
		 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/job_wise_grey_fabric_stock_consolidated_controller.php?action='+action+'&po_id='+po_id, 'Booking Details Info', 'width=900px,height=370px,center=1,resize=0,scrolling=0','../../');
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

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>  		 
    <form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" > 
    <h3 style="width:1230px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1230px;">
                <table class="rpt_table" width="1230" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th>Company</th>                                
                            <th>Buyer</th>
                            <th>Year</th>
                            <th>Job</th>
                            <th>Search By</th>
                            <th id="td_search">Enter Order</th>
                            <th>Store</th>
                            <th style="display: none">Presentation</th>
                            <th>Value</th>
                            <th class="must_entry_caption">Date</th>
                            <th>Stock For</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_wise_grey_fabric_stock_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/job_wise_grey_fabric_stock_consolidated_controller',this.value, 'load_drop_down_store', 'store_td' );" );
                            ?>                            
                        </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
                                echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                            ?>
                        </td>
						<td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:70px" onDblClick="openmypage_job();" placeholder="Browse/Write" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
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
                        <td id="store_td">
                            <?
                            	echo create_drop_down( "cbo_store_id", 110, $blank_array,"", 1, "--Select Store--", 0, "",0 );
                            ?>
                        </td>
                        <td style="display: none;">
                            <?
								$presentation=array(1=>"Order Wise",2=>"Order/Rack & Shelf Wise",3=>"Style Wise",4=>"Buyer Wise");
                                echo create_drop_down( "cbo_presentation", 120, $presentation,"", 0, "", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <?   
                                $valueWithArr=array(1=>'Value With 0',2=>'Value Without 0');
                                echo create_drop_down( "cbo_value_with", 102, $valueWithArr,"",0,"",2,"","","");
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:70px;" readonly/>				
                        </td>
                        <td>
                            <?
								$stock_for_arr=array(1=>"Running Order",2=>"Cancelled Order",3=>"Left Over");
                                echo create_drop_down( "cbo_sock_for", 120, $stock_for_arr,"", 1, "Select", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:45px" class="formbutton" />
                        </td>
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
</html>
