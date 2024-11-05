<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Fabric Stock Summary Report
				
Functionality	:	
JS Functions	:
Created by		:	Kaiyum
Creation date 	: 	01-03-2020
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
echo load_html_head_contents("Finish Fabric Stock Summary Report","../../../", 1, 1, $unicode,1,''); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	/*var tableFilters = 
	{
		//col_15: "none",
		col_operation: {
		id: ["value_sub_total_booking_quantity","value_sub_total_rcv","value_sub_total_rcv_balance","value_sub_total_issue","value_sub_total_stock_qnty","value_total_transfe_in_yds"],
		col: [10,11,12,13,14,15],
		operation: ["sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}*/
	
	function generate_report(rpt_type)
	{
		var job_no=$('#txt_job_no').val();
		if(job_no == "" )
		{
			if( form_validation('cbo_company_id*txt_date_to','Company Name*Date Form*Date To')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_id','Company Name')==false )
			{
				return;
			}
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_buyer_id*cbo_search_by*cbo_store_id*txt_job_no*txt_job_id*txt_date_to',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		//alert (data);return;
		freeze_window(3);
		http.open("POST","requires/finish_fabric_stock_summary_controller.php",true);
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
			//alert(reponse[2]);
			if(reponse[2]==1)
			{
				//setFilterGrid("table_body",-1,tableFilters);
			}
			else
			{
				//setFilterGrid("table_body",-1,tableFilters3);
			}
			
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').show();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#scroll_body tr:first').show();
	}
	function clearDate()
	{
		$('#txt_date_to').val('');	
	}
	function dateValidate()
	{
		$('#txt_job_no').val('');	
		$('#txt_job_id').val('');	
	}
	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var search_type = $("#cbo_search_by").val();
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var page_link='requires/finish_fabric_stock_summary_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&search_type='+search_type;
		
		if(search_type==1)
			var title='Job No Search';
		else if(search_type==2)
			var title='Order No Search';
		else if(search_type==3)
			var title='Booking No Search';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			/*if(search_type==1)
			{*/
				$('#txt_job_no').val(job_no);
				$('#txt_job_id').val(job_id);	
				$('#txt_date_to').val('');	
			//}
			/*else if(search_type==2)
			{
				$('#txt_order_no').val(job_no);
				$('#txt_order_id').val(job_id);
			}
			else if(search_type==3)
			{
				$('#txt_booking_no').val(job_no);
				$('#txt_booking_id').val(job_id);
			}*/
		}
	}
	
	function getStoreId() 
	{	 
		var company_id = document.getElementById('cbo_company_id').value;
		load_drop_down( 'requires/finish_fabric_stock_summary_controller',company_id, 'load_drop_down_location', 'location_td' );
		load_drop_down( 'requires/finish_fabric_stock_summary_controller',company_id+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
		load_drop_down( 'requires/finish_fabric_stock_summary_controller',company_id, 'load_drop_down_stores', 'store_td' );
		set_multiselect('cbo_store_id','0','0','','0');      
	}
	function fncMultiStore()
	{
		set_multiselect('cbo_store_id','0','0','','0');    
	}
	function change_caption(type)
	{
		if(type==1)
		{
			$('#td_search').html('Job No');
		}
		else if(type==2)
		{
			$('#td_search').html('Style');
		}
		else if(type==3)
		{
			$('#td_search').html('Booking No');
		}
		$('#txt_job_no').val('');
		$('#txt_job_id').val('');
	}
	function openmypage_qnty(booking_no,prod_ref,action,from_date,to_date,issue_rtn_id,transfer_in_ids,recv_rtn_id,transfer_out_ids,store_ids)
	{
		var companyID = $("#cbo_company_id").val();
		var popup_width='600px';
		var buyerId=$("#cbo_buyer_id").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/finish_fabric_stock_summary_controller.php?companyID='+companyID+'&booking_no='+booking_no+'&prod_ref='+prod_ref+'&action='+action+'&from_date='+from_date+'&to_date='+to_date+'&issue_rtn_id='+issue_rtn_id+'&recv_rtn_id='+recv_rtn_id+'&transfer_in_ids='+transfer_in_ids+'&transfer_out_ids='+transfer_out_ids+'&buyerId='+buyerId +'&store_ids='+store_ids, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}	
	function reset_multiselect()
	{
		$('#table_bodycbo_company_id tbody :checkbox').removeAttr( "checked" );
		$('#table_bodycbo_store_id tbody :checkbox').removeAttr( "checked" );
		
	}
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>   		 
    <form name="ordewisefinishfabricstock_1" id="ordewisefinishfabricstock_1" autocomplete="off" > 
    <h3 style="width:930px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:930px;">
                <table class="rpt_table" width="930" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="140" class="must_entry_caption">Company</th>
                            <th width="120">Location</th>
                            <th width="120">Store</th>
                            <th width="140">Buyer</th> 
                            <th width="100">Search By</th>
                            <th width="100" id="td_search">Job No</th>
                            <th width="70" class="must_entry_caption">Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','',reset_multiselect(),'');" /></th>
                        </tr>
                    </thead>
                    <tr align="center" class="general">
                        <td id="td_company">
                            <? 
								//load_drop_down( 'requires/finish_fabric_stock_summary_controller',this.value, 'load_drop_down_store', 'store_td' );
                               echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "", $selected, "" );
                            ?>                            
                        </td>
                        <td id="location_td">
							<?
								echo create_drop_down("cbo_location_id", 120, $blank_arra, "",1, "-Select Location-", "", "");
							?>
                        </td>
                        <td id="store_td">
                            <?
                            	echo create_drop_down( "cbo_store_id", 120, $blank_array,"", 0, "", 0, "",0 );
                            ?>
                        </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td> 
                            <?
								$search_by=array(1=>'Job',2=>'Style',3=>'Booking');
                                echo create_drop_down( "cbo_search_by", 90, $search_by,"", 0, "--Select--", 1, "change_caption(this.value);",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:90px" onDblClick="openmypage_job()" onchange="clearDate();"  placeholder="Browse/Write" />
                            <input type="hidden" name="txt_job_id" id="txt_job_id"/>
                        </td>
                        <td>
                            <!-- <input type="text" name="txt_date_from" id="txt_date_from" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;" readonly/>To -->
                            <input type="text" name="txt_date_to" id="txt_date_to" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;" onclick="dateValidate();" readonly/>				
                        </td>
                    
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:55px" class="formbutton" />
                        </td>
                    </tr>
                    <!-- <tr>
                    	<td colspan="13" align="center"><? //echo load_month_buttons(1);  ?></td>
                    </tr> -->
                </table> 
            </fieldset> 
        </div>
             
    </form>    
</div>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>      
</body>  
<script>
	set_multiselect('cbo_company_id*cbo_store_id','0*0','0*0','','0*0');
	//
	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_id,'0');getStoreId();") ,3000)];	
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
