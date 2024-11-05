<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Date Wise Finish Fabric Rcv Issue V2 Report
				
Functionality	:	
JS Functions	:
Created by		:	Abu Sayed
Creation date 	: 	18-12-2021
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
echo load_html_head_contents("Date Wise Finish Fabric Rcv Issue V2 Report","../../../", 1, 1, $unicode,1,''); 
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	var tableFilters = 
	{
		//col_15: "none",
		col_operation: {
		id: ["value_total_rcv_qnty_kg"],
		col: [14],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	}
	
	var tableFilters2 = 
	{
		//col_15: "none",
		col_operation: {
		id: ["value_issue_qnty_kg"],
		col: [14],
		operation: ["sum"],
		write_method: ["innerHTML"]
		}
	}

	function generate_report(rpt_type)
	{
		if($('#cbo_cutting_floor').val() >0 && rpt_type==1)
		{
			alert("Selected Cutting Floor Applicable Only For Issue Button...");
			return;
		}
		var job_no=$('#txt_job_no').val();
		var order_no=$('#txt_order_no').val();
		var booking_no=$('#txt_booking_no').val();
		var style_no=$('#txt_style_no').val();
		var cutting_floor=$('#cbo_cutting_floor').val();
		if(job_no == "" && order_no == "" && booking_no == "" && style_no == ""  && cutting_floor == 0 )
		{
			if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*Date Form*Date To')==false )
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
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_store_id*cbo_year*txt_job_no*txt_job_id*txt_order_no*txt_order_id*txt_booking_no*txt_booking_id*txt_date_from*txt_date_to*txt_batch_no*cbo_location_id*cbo_floor_id*txt_style_no*cbo_cutting_floor*cbo_based_on',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		//alert (data);return;
		freeze_window(3);
		http.open("POST","requires/date_wise_finish_feb_recv_issue_v2_controller.php",true);
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
				setFilterGrid("table_body",-1,tableFilters);
			}
			else if(reponse[2]==2)
			{
				setFilterGrid("table_body",-1,tableFilters2);
			}
			
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

	function openmypage_job(search_type)
	{
		if( form_validation('cbo_company_id*cbo_year','Company Name*Year')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var page_link='requires/date_wise_finish_feb_recv_issue_v2_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&search_type='+search_type;
		
		if(search_type==1)
			var title='Job No Search';
		else if(search_type==2)
			var title='Order No Search';
		else if(search_type==3)
			var title='Booking No Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			if(search_type==1)
			{
				$('#txt_job_no').val(job_no);
				$('#txt_job_id').val(job_id);	
			}
			else if(search_type==2)
			{
				$('#txt_order_no').val(job_no);
				$('#txt_order_id').val(job_id);
			}
			else if(search_type==3)
			{
				$('#txt_booking_no').val(job_no);
				$('#txt_booking_id').val(job_id);
			}
		}
	}
	
	function openmypage(prod_id,po_id,batch_id,store_id,date_form,date_to,item_category_id,action)
	{ 
		//alert(type);
		var companyID = $("#cbo_company_id").val();
		var popup_width='620px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/date_wise_finish_feb_recv_issue_v2_controller.php?companyID='+companyID+'&prod_id='+prod_id+'&po_id='+po_id+'&batch_id='+batch_id+'&store_id='+store_id+'&date_form='+date_form+'&date_to='+date_to+'&item_category_id='+item_category_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
	
	function getStoreId() 
	{	
       
		var company_id = document.getElementById('cbo_company_id').value;
		var location_id = document.getElementById('cbo_location_id').value;
		var store_id = document.getElementById('cbo_store_id').value;
		load_drop_down( 'requires/date_wise_finish_feb_recv_issue_v2_controller',company_id+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
		     
        load_drop_down( 'requires/date_wise_finish_feb_recv_issue_v2_controller',company_id, 'load_drop_down_location', 'location_td'); 
        set_multiselect('cbo_location_id','0','0','','0'); 
        setTimeout[($("#location_td a").attr("onclick", "disappear_list(cbo_location_id,'0');loadStore();loadCuttingFloor();"), 3000)];

        load_drop_down( 'requires/date_wise_finish_feb_recv_issue_v2_controller',company_id+'_'+location_id, 'load_drop_down_stores', 'store_td' );
		set_multiselect('cbo_store_id','0','0','','0'); 
        loadFloor();
	}

    function loadStore() 
    {
        var company_id = document.getElementById('cbo_company_id').value;
		var location_id = document.getElementById('cbo_location_id').value;
        var store_id = document.getElementById('cbo_store_id').value;
        //alert(location_id);
        load_drop_down('requires/date_wise_finish_feb_recv_issue_v2_controller', company_id+'_'+location_id, 'load_drop_down_stores', 'store_td');
        set_multiselect('cbo_store_id', '0', '0', '', '0');
        setTimeout[($("#store_td a").attr("onclick", "disappear_list(cbo_store_id,'0');loadFloor();"), 3000)];
        loadFloor();

    }

    function loadFloor() 
    {
        var company_id = document.getElementById('cbo_company_id').value;
		var location_id = document.getElementById('cbo_location_id').value;
        var store_id = document.getElementById('cbo_store_id').value;
        //alert(location_id);
        load_drop_down( 'requires/date_wise_finish_feb_recv_issue_v2_controller',company_id+'_'+location_id+'_'+store_id, 'load_drop_down_floors', 'floor_td'); 
        set_multiselect('cbo_floor_id','0','0','','0'); 
    }

	function loadCuttingFloor() 
    {
        var company_id = document.getElementById('cbo_company_id').value;
		var location_id = document.getElementById('cbo_location_id').value;
        //alert(location_id);
        load_drop_down( 'requires/date_wise_finish_feb_recv_issue_v2_controller',company_id+'_'+location_id, 'load_drop_down_cuttingfloor', 'cutting_floor'); 
    }

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>   		 
    <form name="ordewisefinishfabricstock_1" id="ordewisefinishfabricstock_1" autocomplete="off" > 
    <h3 style="width:1755px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1755px;">
                <table class="rpt_table" width="1755" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="140" class="must_entry_caption">Company</th>
                            <th width="140" >Location</th>
                            <th width="120">Store Name</th>
                            <th width="120">Store Floor</th>
                            <th width="140">Buyer</th> 
                            <th width="60">Year</th>
                            <th width="100">Style No</th>
                            <th width="100" id="td_search">Job No</th>
                            <th width="100">Order No</th>
                            <th width="100">Booking No</th>
                            <th width="100">Batch No</th>
                            <th width="120">Cutting Floor</th>
							<th width="70">Based on</th>
                            <th width="140" class="must_entry_caption">Transaction Date Range</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center" class="general">
                        <td id="td_company">
                            <? 
                               echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "", $selected, "" );
							   //get_php_form_data( this.value, 'company_wise_report_button_setting','requires/date_wise_finish_feb_recv_issue_v2_controller');
                            ?>                            
                        </td>
                        <td id="location_td">
							<?
								echo create_drop_down("cbo_location_id", 140, $blank_arra, "",1, "-Select Location-", "", "");
							?>
                        </td>
                        <td id="store_td">
                            <?
                            	echo create_drop_down( "cbo_store_id", 120, $blank_array,"", 1, "-Select Store-", 0, "",0 );
                            ?>
                        </td>
                        <td id="floor_td">
                            <?
                            	echo create_drop_down( "cbo_floor_id", 120, $blank_array,"", 1, "-Select Floor-", 0, "",0 );
                            ?>
                        </td>
                        <td id="buyer_td"> 
                            <?
                                echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                       
                        <td> 
                            <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 60, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_style_no" name="txt_style_no" class="text_boxes" style="width:90px"    placeholder="Write" />
                         
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:90px" onDblClick="openmypage_job(1)"   placeholder="Browse/Write" />
                            <input type="hidden" name="txt_job_id" id="txt_job_id"/>
                        </td>
                        <td>
                            <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:90px" onDblClick="openmypage_job(2)"  placeholder="Browse/Write" />
                            <input type="hidden" name="txt_order_id" id="txt_order_id"/>
                        </td>
                        <td>
                            <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:90px" onDblClick="openmypage_job(3)"  placeholder="Browse/Write" />
                            <input type="hidden" name="txt_booking_id" id="txt_booking_id"/>
                        </td>
                        <td>
                            <input type="text" id="txt_batch_no" name="txt_batch_no" class="text_boxes" style="width:90px"  placeholder="Write" />
                        </td>
                        <td id="cutting_floor">
                            <?
								
                        		echo create_drop_down( "cbo_cutting_floor", 120, $blank_array,"", 1, "--Select Cutting Floor--", 0, "",0 );
                            ?>
                        </td>
						<td>
                            <?
								$base_on_arr=array(1=>"Transaction Date",2=>"Insert Date");
                        		echo create_drop_down( "cbo_based_on", 70, $base_on_arr,"", 0, "", 1, "fn_change_base(this.value);",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;" />To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="<? //echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;" />				
                        </td>
                     
                        <td>
                            <input type="button" name="search1" id="search1" value="Receive" onClick="generate_report(1)" style="width:65px" class="formbutton" />
                            <input type="button" name="search2" id="search2" value="Issue" onClick="generate_report(2)" style="width:65px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="15" align="center"><? echo load_month_buttons(1);  ?></td>
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
	set_multiselect('cbo_company_id*cbo_store_id*cbo_location_id*cbo_floor_id','0*0*0*0','0*0*0*0','','0*0*0*0');
	//
	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_id,'0');getStoreId();loadCuttingFloor();") ,3000)];		
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
