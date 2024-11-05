<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Purchase Recap Report.
Functionality	:
JS Functions	:
Created by		:	Fuad
Creation date 	: 	18-06-2013
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
echo load_html_head_contents("Purchase Recap Report", "../../", 1, 1,'',1,1);
//echo load_html_head_contents("Import CI Statement Report","../../", 1, 1, $unicode,1,1); 


$container_status = array(1=>"FCL", 2=>"LCL");
$container_size = array(1=>"20 ft GP", 2=>"20 ft HQ", 3=>"40 ft GP", 4=>"40 ft HQ");
?>

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	var permission = '<? echo $permission; ?>';

	function generate_report(type)
	{
		
			if(type==3)
			{
				if(form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false)
				{
					return;
				}
				var item_cat=$('#cbo_item_category_id').val();
				
				if(item_cat*1!=4)
				{
					alert("This Button Only For Item Category Accessories ");
					return;
				}
				
				
				
				
			}
			else
			{
				if(form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false)
				{
					return;
				}
			}
		
		

		var report_title=$( "div.form_caption" ).html();

		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_issuing_bank*cbo_item_category_id*cbo_lc_type_id*txt_date_from*txt_date_to*cbo_date_type*cbo_source_id*cbo_receive_status*pi_no*pi_no_id',"../../")+'&report_title='+report_title+'&type='+type;

		freeze_window(3);
		http.open("POST","requires/purchase_recap_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			var item_cat=$('#cbo_item_category_id').val();
			if(item_cat==1)
			{
				document.getElementById('report_container').innerHTML+='&nbsp;&nbsp;<a href="requires/'+response[2]+'" style="text-decoration:none"><input type="button" value="Excel Short Preview" name="excel" id="excel" class="formbutton" style="width:130px"/></a>';
			}
			document.getElementById('report_container').innerHTML+='&nbsp;&nbsp;<a href="requires/'+response[3]+'" style="text-decoration:none"><input type="button" value="Excel Small" name="excel" id="excel" class="formbutton" style="width:130px"/></a>';
			if(response[4]==2)
			{
				var tableFilters = {
					//col_0: "none",
					col_operation: {
						id: ["value_td_pi_qty","value_td_pi_val","value_td_mrr_qnty","value_td_mrr_val","value_td_balance_qnty","value_td_balance_val"], 
						col: [13,15,21,22,23,24], 
						operation: ["sum","sum","sum","sum","sum","sum"], 
						write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"] 
					}
				}
			}
			else if(response[4]==3)
			{
				
				
				var tableFilters = {
					//col_0: "none",
					col_operation: {
						id: ["td_pi_qty","td_pi_val","td_wo_amount","td_mrr_qnty","td_mrr_val","td_short_val"],
						col: [9,11,15,16,17,18],
						operation: ["sum","sum","sum","sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
			}
			else
			{
				
				var tableFilters = {
					//col_0: "none", ,"value_tot_btb_amt"
					col_operation: {
						id: ["value_tot_pi_qnty","value_tot_pi_amt","value_tot_paid_amt","value_tot_mrr_qnty","value_tot_mrr_value","value_tot_short_val"],
						col: [9,11,54,56,57,58],
						operation: ["sum","sum","sum","sum","sum","sum"],
						write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}
				}
			}
			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		//$("#table_body tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');// media="print"
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
		$("#table_body tr:first").show();

	}

	function show_qty_details(pi_id,category_id,goods_rcv_status, description)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/purchase_recap_report_controller.php?pi_id='+pi_id+'&category_id='+category_id+'&goods_rcv_status='+goods_rcv_status+'&description='+description+'&action=receive_qnty', "Receive Wise Quantity", 'width=550px,height=330px,center=1,resize=0,scrolling=0','../');
	}
	
	function show_receive_qty_details(pi_id,category_id,goods_rcv_status,goods_description,goods_uom,item_group,item_description,rate)
	{
		
		//alert(goods_description);
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/purchase_recap_report_controller.php?pi_id='+pi_id+'&category_id='+category_id+'&goods_rcv_status='+goods_rcv_status+'&goods_description='+goods_description+'&goods_uom='+goods_uom+'&item_group='+item_group+'&item_description='+item_description+'&rate='+rate+'&action=receive_qnty_details', "Receive Wise Quantity", 'width=550px,height=330px,center=1,resize=0,scrolling=0','../');
	}
	
	
	function search_populate(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="PI Date";
			//$('#search_by_th_up').css('color','blue');
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="PI Insert Date";
			//$('#search_by_th_up').css('color','blue');
		}
		else if(str==3)
		{
			document.getElementById('search_by_th_up').innerHTML="BTB Date";
			//$('#search_by_th_up').css('color','blue');
		}
		else if(str==4)
		{
			document.getElementById('search_by_th_up').innerHTML="BTB Insert Date";
			//$('#search_by_th_up').css('color','blue');
		}
		else if(str==5)
		{
			document.getElementById('search_by_th_up').innerHTML="Maturity Date";
			//$('#search_by_th_up').css('color','blue');
		}
		else if(str==6)
		{
			document.getElementById('search_by_th_up').innerHTML="Maturity Insert Date";
			//$('#search_by_th_up').css('color','blue');
		}
	}
	/*function active_inactive(company) 
	{
		 $("#search2").removeClass( "formbutton_disabled"); //To make disable print to button
         $("#search2").addClass( "formbutton"); //To make enable print to button
	}*/
	
	function openmypage_pi()
	{
		if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false ){
			return;
		}
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_supplier = $("#cbo_supplier").val();
		var item_category_id = $("#cbo_item_category_id").val();
		if(item_category_id!=4){
			alert("Only for Accessories Category");
			return;
		}

		var page_link='requires/purchase_recap_report_controller.php?action=pi_no_popup&cbo_company_name='+cbo_company_name+'&cbo_supplier='+cbo_supplier+'&item_category_id='+item_category_id;
		var title='PI Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=460px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var pi_no=this.contentDoc.getElementById("hide_pi_no").value;
			var pi_id=this.contentDoc.getElementById("hide_pi_id").value;
			$('#txt_pi').val(pi_no);
			$('#pi_no').val(pi_no);
			$('#pi_no_id').val(pi_id);
			if(pi_id!='')
			{
				$('#txt_date_from').val("").attr("disabled",true);
				$('#txt_date_to').val("").attr("disabled",true);
			}
		}
	}

    function generate_trim_report(action,txt_booking_no,cbo_company_name,id_approved_id,cbo_level,comp_imgShowRef)
	{		
		var show_comment='';
		var r=confirm("Press  \"Cancel\"  to hide  comments\nPress  \"OK\"  to Show comments");
		if (r==true)
		{
			show_comment="1";
		}
		else
		{
			show_comment="0";
		}		
		
		var requestFile='';var title='';var path='';
		
		requestFile="../../order/woven_gmts/requires/trims_booking_multi_job_controllerurmi.php";
		title="Multiple Job Wise Trims Booking V2";
		path='../../';
	
		//var show_comment='1';
		var data="action="+action+
					'&txt_booking_no='+"'"+txt_booking_no+"'"+
					'&cbo_company_name='+"'"+cbo_company_name+"'"+
					'&report_title='+title+
					'&show_comment='+"'"+show_comment+"'"+
					'&id_approved_id='+"'"+id_approved_id+"'"+
					'&cbo_level='+"'"+cbo_level+"'"+
					'&path='+path;
		
		http.open("POST",requestFile,true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{			
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
				d.close();
		   }			
		}
	}	
</script>

</head>

<body onLoad="set_hotkey();">
<form id="PurchaseRecap_report">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1250px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:1250px;">
                <table class="rpt_table" width="1250" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Issuing Bank</th>
                            <th>Data Category</th>
                            <th id="search_by_th_up">PI Date</th>
                           <!-- <th>BTB Date</th>-->
                            <th class="must_entry_caption">Item Category</th>
                            <th>LC Type</th> 
                            <th>Import Source</th>
                            <th>PI No</th>
                            <th>Receiving status</th>
                            <!-- <th>Acceptance Status</th> -->

                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" onClick="reset_form('PurchaseRecap_report','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <?
                                    echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "get_php_form_data( this.value, 'company_wise_report_button_setting','requires/purchase_recap_report_controller' );" );
                                ?>
                            </td>
                            <td>
                                <?
                                    echo create_drop_down( "cbo_issuing_bank", 120, "select id,bank_name from lib_bank where issusing_bank=1 and status_active=1 and is_deleted=0  order by bank_name","id,bank_name", 1, "--Select Bank--", $selected, "" );
                                ?>
                           	</td>
                            <td align="center">
                             <? 
							 //txt_date_from /txt_date_to/txt_maturity_date_from,txt_maturity_date_to/txt_date_from_btb,txt_date_to_btb
							 $dd="search_populate(this.value)";
							//echo create_drop_down( "cbo_search_date", 100, $search_by,"",0, "--Select--", $selected,$dd,0 );

							$date_type_arr=array(1=>"PI Date",2=>"PI Insert Date",3=>"BTB Date",4=>"BTB Insert Date",5=>"Maturity Date",6=>"Maturity Insert Date");
							echo create_drop_down( "cbo_date_type", 100, $date_type_arr,"", 0, "--Select Date--", $selected,$dd );
							?>

                        	</td>
                            <td align="center" >
                             <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                        	</td>
                            <!--<td align="center">
                             <input type="text" name="txt_date_from_btb" id="txt_date_from_btb" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to_btb" id="txt_date_to_btb" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                        	</td>-->
                            <td align="center">
                                <? echo create_drop_down( "cbo_item_category_id", 100, $item_category,'', 1, '-- Select --',0,"",0); ?>
                            </td>
                            <td>
                                <? echo create_drop_down( "cbo_lc_type_id",90,$lc_type,'',1,'-- All Type --',0,"",0); ?>
                            </td>
                            <td  align="center">
				                <?
					               echo create_drop_down( "cbo_source_id", 150,$supply_source,"", 1, "-- Select Source--", $selected, "","","","","","");
                				?> 
                          </td>
                
						  <td align="center">
                            	<input type="text" name="pi_no" id="pi_no" value="" class="text_boxes" style="width:100px" placeholder="Browse/Write" onDblClick="openmypage_pi();"  />
                            	<input type="hidden" name="pi_no_id" id="pi_no_id">
                            </td>
							<?
							$receive_status=array(1=>"Full Pending",2=>"Partial Received",3=>"Fully Received",4=>"Full Pending And Partial Received",5=>"All");
                            echo create_drop_down( "cbo_receive_status", 100, $receive_status,"", 0, "", 5, "" );
                            ?>
                          </td>
                           <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:60px; display:none" class="formbutton" />
                                <input type="button" name="search2" id="search2" value="Recap 1" onClick="generate_report(2)" style="width:60px; display:none" class="formbutton" />                                
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="9" align="center"><? echo load_month_buttons(1); ?></td>
                        	<td><input type="button" name="search3" id="search3" value="Trims recap" onClick="generate_report(3)" style="width:70px; display:none" class="formbutton" /></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>

    <div style="margin-top:10px" id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>
</body>
<script>
	set_multiselect('cbo_source_id','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
