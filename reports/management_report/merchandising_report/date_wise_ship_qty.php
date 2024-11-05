<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Date Wise Ship Quantity Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	08-06-2014
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
echo load_html_head_contents("Cost Breakdown Report","../../../", 1, 1, $unicode,1,1);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated(type)
	{
		
		var cbo_report_type = $("#cbo_report_type").val();
		var cbo_shipment_status = $("#cbo_shipment_status").val();
		if(form_validation('cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to','From Year*From Month*To Year*To Month')==false)
		{
			return;
		}
		else
		{	
			if(cbo_report_type==1 && type==1)
			{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to*cbo_order_status*txt_season*cbo_product_category*cbo_date_category*cbo_report_type*cbo_shipment_status*cbo_dealing_merchant',"../../../");
			}
			else if(cbo_report_type==1 && type==3)
			{
			var data="action=report_generate3"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to*cbo_order_status*txt_season*cbo_product_category*cbo_date_category*cbo_report_type*cbo_shipment_status*cbo_dealing_merchant',"../../../");
			}
			else if(cbo_report_type==2 && type==1)
			{
				var data="action=report_generate_order"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to*cbo_order_status*txt_season*cbo_product_category*cbo_date_category*cbo_report_type*cbo_shipment_status*cbo_dealing_merchant',"../../../");
				//alert(data);
			}
			else if(type==2)
			{
				if(cbo_report_type!=2){
					alert("Please select report type order wise details for this button");
					release_freezing();
					return;
				}
				else{
					var data="action=report_generate_order2"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to*cbo_order_status*txt_season*cbo_product_category*cbo_date_category*cbo_report_type*cbo_shipment_status*cbo_dealing_merchant',"../../../");
				}
				
				//alert(data);
			}
			else if(cbo_report_type==3 && type==1)
			{
				var data="action=report_generate_order_summary"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to*cbo_order_status*txt_season*cbo_product_category*cbo_date_category*cbo_report_type*cbo_shipment_status*cbo_dealing_merchant',"../../../");
				//alert(data);
			}
			else if(cbo_report_type==4 && type==1)
			{
				var data="action=report_generate_com_buyer_ord_allocation"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to*cbo_order_status*txt_season*cbo_product_category*cbo_date_category*cbo_report_type*cbo_shipment_status*cbo_dealing_merchant',"../../../");
				//alert(data);
			}
			
			freeze_window(3);
			http.open("POST","requires/date_wise_ship_qty_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			// $('#report_container2').html("");
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:100px"/>&nbsp;&nbsp;&nbsp;<input type="button" onclick="print_report()" value="Print" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}
	
	
	function fn_report_generated_all()
	{
		
		var cbo_report_type = $("#cbo_report_type").val();
		var cbo_shipment_status = $("#cbo_shipment_status").val();
		if(form_validation('cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to','From Year*From Month*To Year*To Month')==false)
		{
			return;
		}
		else
		{
			var all_date=2;	
			if(cbo_report_type==1)
			{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to*cbo_order_status*txt_season*cbo_product_category*cbo_date_category*cbo_report_type*cbo_shipment_status*cbo_dealing_merchant',"../../../")+"&all_date="+all_date;
			}
			/*else 
			{
				alert("This Button only for Buyer Wise Summery Report Type");
			}*/
			else if(cbo_report_type==2)
			{
				var data="action=report_generate_order"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to*cbo_order_status*txt_season*cbo_product_category*cbo_date_category*cbo_report_type*cbo_shipment_status*cbo_dealing_merchant',"../../../")+"&all_date="+all_date;
				//alert(data);
			}
			else if(cbo_report_type==3)
			{
				var data="action=report_generate_order_summary"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to*cbo_order_status*txt_season*cbo_product_category*cbo_date_category*cbo_report_type*cbo_shipment_status*cbo_dealing_merchant',"../../../")+"&all_date="+all_date;
				//alert(data);
			}
			else if(cbo_report_type==4)
			{
				var data="action=report_generate_com_buyer_ord_allocation"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to*cbo_order_status*txt_season*cbo_product_category*cbo_date_category*cbo_report_type*cbo_shipment_status*cbo_dealing_merchant',"../../../")+"&all_date="+all_date;
				//alert(data);
			}
			
			freeze_window(3);
			http.open("POST","requires/date_wise_ship_qty_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_all_reponse;
		}
	}
		
	function fn_report_generated_all_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			$('#report_container2').html("");
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:100px"/>&nbsp;&nbsp;&nbsp;<input type="button" onclick="print_report()" value="Print" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}
	
	
	function new_window()
	{
		
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><style>.verticalText2{writing-mode: tb-rl; filter: flipv fliph; -webkit-transform: rotate(270deg);-moz-transform: rotate(270deg);-o-transform: rotate(270deg);-ms-transform: rotate(270deg);transform: rotate(270deg);width: 1em;line-height: 1em;};@media print {thead {display: table-header-group;}}</style></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
	
	}


	function print_report()
	{
		$('#form_body').hide();
		$('#report_container').hide();
		window.print(document.getElementById('report_container').innerHTML);
		$('#form_body').show();
		$('#report_container').show();
	}
	
	function fn_report_generated_order()
	{
		if(form_validation('cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to','From Year*From Month*To Year*To Month')==false)
		{
			return;
		}
		else
		{	
			var data="action=report_generate_order"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year_from*cbo_month_from*cbo_year_to*cbo_month_to*cbo_order_status*txt_season*cbo_product_category*cbo_date_category*cbo_report_type*cbo_shipment_status',"../../../");
			freeze_window(3);
			http.open("POST","requires/date_wise_ship_qty_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_order_reponse;
		}
	}
		
	function fn_report_generated_order_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			$('#report_container2').html("");
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:100px"/>&nbsp;&nbsp;&nbsp;<input type="button" onclick="print_report()" value="Print" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}	
	
	function generate_worder_report(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,type,i,booking_type)
	{
		//var report_title='Budget Wise Fabric Booking';
		var data="action="+type+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+"Budget Wise Fabric Booking"+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		'&path=../../../';
		//alert(data);return;
			//var data="action="+show_fabric_booking_report_gr+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no',"../../")+'&report_title='+$report_title+'&show_yarn_rate='+show_yarn_rate+'&path=../../';
			
			//$report_title=$( "div.form_caption" ).html();
			
			//var data="action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id*txt_job_no*i',"../../")+'&path=../../';
			
			//freeze_window(5);
			//http.open("POST","requires/fabric_booking_controller.php",true);
			
						
					
		/*if(type==1)	
		{			
			http.open("POST","../order/woven_order/requires/short_fabric_booking_controller.php",true);
		}
		else if(action=='show_fabric_booking_report_gr')
		{
			http.open("POST","../order/woven_order/requires/fabric_booking_controller.php",true);
		}
		else
		{
			http.open("POST","../order/woven_order/requires/sample_booking_controller.php",true);
		}*/
		
		if(booking_type=='p'){
			http.open("POST","../../../order/woven_order/requires/partial_fabric_booking_controller.php",true);
		}
		else{
			http.open("POST","../../../order/woven_order/requires/fabric_booking_controller.php",true);
		}
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
		   }
			
		}
	}
	
	function openmypage_season()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyerID = $("#cbo_buyer_name").val();
		
		var page_link='requires/date_wise_ship_qty_controller.php?action=search_popup&companyID='+companyID+'&buyerID='+buyerID;
		var title='Season Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=350px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hide_season_name=this.contentDoc.getElementById("hide_season").value;
			var hide_season_id=this.contentDoc.getElementById("hide_season_id").value;
	
			$('#txt_season').val(hide_season_id);
			$('#txt_season_name').val(hide_season_name);
		}
	}

	function openmypage_smv(job_no,order_uom,po_id)
	{
		var page_link='requires/date_wise_ship_qty_controller.php?action=smv_popup&job_no='+job_no+'&order_uom='+order_uom+'&po_id='+po_id;
		var title='SMV Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=470px,height=350px,center=1,resize=1,scrolling=0','../../');
	}
	function embellish_type_popup(company_id,job_no)
	{
		var company_id = company_id;
		var job_no = job_no;

		var page_link='requires/date_wise_ship_qty_controller.php?action=embellish_type_popup&company_id='+company_id+'&job_no='+job_no;

		var title='Embellish Type Popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=450px,center=1,resize=1,scrolling=0','../../');

	}

	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
			{
				if(report_id[k]==222)
				{
					$("#show_1").show();	 
				}
				if(report_id[k]==259)
				{
					$("#show_2").show();	 
				}	
				if(report_id[k]==715)
				{
					$("#all_report_id").show();	 
				}	
				if(report_id[k]==124)
				{
					$("#order_id").show();	 
				}	
				if(report_id[k]==310)
				{
					$("#catagory_id").show();	
				}	
			}
	}
</script>


<style>
	.verticalText2 
	{
	 writing-mode: tb-rl;
     filter: flipv fliph;
    -webkit-transform: rotate(270deg);
    -moz-transform: rotate(270deg);
    -o-transform: rotate(270deg);
    -ms-transform: rotate(270deg);
    transform: rotate(270deg);
    width: 1em;
    line-height: 1em;
	
	}
</style>

</head>
 
<body onLoad="set_hotkey();">
		 
<form id="ship_status_rpt" action="" autocomplete="off" method="post">
    <div style="width:100%;" align="center" id="form_body">
        <? echo load_freeze_divs ("../../../",""); ?>
         <h3 align="left" id="accordion_h1" style="width:1480px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1480px;">
                <table class="rpt_table" width="1480" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                    	<tr>                   
                            <th width="130">Company Name</th>
                            <th width="120">Buyer Name</th>
                            <th width="100">Dealing Marchant</th>
                            <th width="160">Start Range</th>
                            <th width="160">End Range</th>
                            <th width="80">Season</th>
							<th width="80">Product Category</th>
                            <th width="80">Order Status</th>
                            <th width="80">Shipment Status</th>
                            <th width="80">Date Category</th>
                            <th width="80">Report Type</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('ship_status_rpt','report_container*report_container2','','','');" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general">
                        <td> 
                            <?
                                echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/date_wise_ship_qty_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value, 'set_print_button', 'requires/date_wise_ship_qty_controller');" );
                            ?>
                        </td>
                        <td id="buyer_td"> 
                            <? echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0  $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0,"" ); ?>
                        </td>
                         <td id="td_marchant"> 
                            <? echo create_drop_down( "cbo_dealing_merchant", 100, "select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- All --", $selected, "",0,"" ); ?>
                        </td>
                        <td colspan="2">
							<?
								$year_current=date("Y");
								$month_current=date("m");
                            	echo create_drop_down( "cbo_year_from", 70, $year,"", 1, "-Select-",$year_current);
								echo " ";
								echo create_drop_down( "cbo_month_from", 70, $months,"", 1, "-Select-",$month_current);
								echo " ";
								echo '&nbsp;<span style="font-size:16px; font-weight:bold">To:</span>';
								echo " ";
                        		echo create_drop_down( "cbo_year_to", 70, $year,"", 1, "-Select-",$year_current);
								echo " ";
								echo create_drop_down( "cbo_month_to", 70, $months,"", 1, "-Select-",$month_current);
                        
                        	?>
                        </td>
                         <td align="center">
                        	<input type="text" name="txt_season" id="txt_season_name" class="text_boxes" style="width:80px" placeholder="Browse" onDblClick="openmypage_season();" readonly/>

                        	<input type="hidden" name="txt_season" id="txt_season" class="text_boxes" style="width:80px" readonly/>
                        </td>
						<td> 
                            <? 
								echo create_drop_down( "cbo_product_category", 80, $product_category,"", 1, "-- Select --",  0, "",0,'','','','','' );
                            ?>	
                        </td>
                        <td>
							<? 
								$order_status=array(0=>"All",1=>"Confirmed",2=>"Projected"); 
								echo create_drop_down( "cbo_order_status", 80, $order_status,"", 0, "", 0, "" ); 
                            ?>
                        </td>
                        <td>
							<? 
								$shipment_status_arr=array(1=>"Running Full Order Qty",2=>"Running Order Balance Qty",3=>"Fully Shipped",4=>"Cancelled Order"); 
								echo create_drop_down( "cbo_shipment_status", 80, $shipment_status_arr,"", 0, "", 0, "" ); 
                            ?>
                        </td>
                        
                        <td> 
                            <? 
							$date_category=array(1=>"Country Ship Date",2=>"Cut-off Date");
							echo create_drop_down( "cbo_date_category", 100, $date_category,"", 0, "-- All Date --", $selected, "",0,"" ); ?>
                        </td>
                        <td>
							<? 
								//$report_type_arr=array(1=>"Buyer Wise Summary",2=>"Order Wise Detail",3=>"Order Wise Summary",4=>"Company and Buyer Wise Allocation"); 
								$report_type_arr=array(1=>"Buyer Wise Summary",2=>"Order Wise Detail",3=>"Order Wise Summary"); 
								echo create_drop_down( "cbo_report_type", 80, $report_type_arr,"", 0, "", 0, "" ); 
                            ?>
                        </td>
                        <td>
                            <input type="button" id="show_1" class="formbutton" style="width:65px;display:none;" value="Show" onClick="fn_report_generated(1)" />
                            <input type="button" id="show_2" class="formbutton" style="width:65px;display:none;" value="Show 2" onClick="fn_report_generated(2)" />
                              <input type="button" id="all_report_id" class="formbutton" style="width:65px;display:none;" value="All Date" onClick="fn_report_generated_all()" />
                            <input type="hidden" id="order_id" class="formbutton" style="width:65px;display:none;" value="Order Wise" onClick="fn_report_generated_order()" />
							<input type="button" id="catagory_id" class="formbutton" style="width:85px;display:none;" value="Category Wise" onClick="fn_report_generated(3)" />
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
