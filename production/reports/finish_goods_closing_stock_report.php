<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Finish goods Closing Stock Report
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	9-12-2021
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
echo load_html_head_contents("Finish goods Closing Stock Report", "../../", 1, 1,$unicode,1,'');

?>

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	 
	function open_job_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company_name=$("#cbo_company_name").val();
		var page_link='requires/finish_goods_closing_stock_report_controller.php?action=job_popup&company_name='+company_name;
		var title="Search Job/Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var job_no=this.contentDoc.getElementById("hide_job_no").value;

			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_id); 
		}
	}

	function fn_generate_report(type)
	{
		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date Range*Date Range')==false )
        {
            return;
        }

  		var cbo_company_name = $("#cbo_company_name").val();
  		var cbo_store_name = $("#cbo_store_name").val();
  		var cbo_year = $("#cbo_year").val();
  		var cbo_buyer_id = $("#cbo_buyer_id").val();
  		var hidden_job_id = $("#hidden_job_id").val();
  		var from_date = $("#txt_date_from").val();
  		var to_date = $("#txt_date_to").val();
  		var value_with = $("#cbo_value_with").val();
  		var cbo_get_upto = $("#cbo_get_upto").val();
		var txt_days = $("#txt_days").val();
		var cbo_get_upto_qnty = $("#cbo_get_upto_qnty").val();
		var txt_qnty = $("#txt_qnty").val();
		
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

		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+'&cbo_company_name='+cbo_company_name+'&cbo_store_name='+cbo_store_name+'&cbo_year='+cbo_year+'&cbo_buyer_id='+cbo_buyer_id+'&hidden_job_id='+hidden_job_id+'&from_date='+from_date+'&to_date='+to_date+'&value_with='+value_with+'&get_upto='+cbo_get_upto+'&txt_days='+txt_days+'&get_upto_qnty='+cbo_get_upto_qnty+'&txt_qnty='+txt_qnty+'&rpt_type='+type+'&report_title='+report_title;
		// console.log(data)
		freeze_window(3);
		http.open("POST","requires/finish_goods_closing_stock_report_controller.php",true);
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
			// setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$(".flt").css("display","none");
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
		$(".flt").css("display","block");
	}

    function openmypage_stock(job_id,store_id,item_id,color_id,to_date) 
	{
		page_link='requires/finish_goods_closing_stock_report_controller.php?action=stock_popup&job_id='+job_id+'&store_id='+store_id+'&item_id='+item_id+'&color_id='+color_id+'&to_date='+to_date;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,'Closing Stock Pop Up', 'width=600px, height=350px, center=1, resize=0, scrolling=0','');
		emailwindow.onclose=function(){}
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center"> 
		<? echo load_freeze_divs ("../../",'');  ?>
		<h3 style="width:1200px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
		<div style="width:100%;" align="center" id="content_search_panel">
		<form id="dateWiseProductionReport_1">    
			<fieldset style="width:1200px;">
				<table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
					<thead>                    
						<tr>
							<th class="must_entry_caption" width="150" >Company</th>
							<th width="100">Store</th>
							<th width="80">Job Year</th>
							<th width="100">Buyer</th>
							<th width="100">Job No</th>
							<th width="80">Value</th>
							<th class="must_entry_caption" width="150">Date Range</th>
                            <th width="80">Get Upto</th>
                            <th width="50">Days</th>
                            <th width="80">Get Upto</th>
                            <th width="50">Qty.</th>
							<th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form()"/></th>
						</tr>   
					</thead>
					<tbody>
						<tr class="general">
							<td align="center" > 
								<?
									echo create_drop_down( "cbo_company_name", 150, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/finish_goods_closing_stock_report_controller', company, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
							</td>
							<td align="center" id="store_td"> 
								<? 
									echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "--Select Store--", 0, "" );
								?>
							</td>
							<td align="center"> 
								<? 
									echo create_drop_down( "cbo_year", 80, $year,"", 1, "-- Year --", 0, "" );
								?>
							</td>
							<td align="center" id="buyer_td"> 
								<? 
									echo create_drop_down( "cbo_buyer_id", 100, $blank_array,"", 1, "--Select Buyer--", 0, "" );
								?>
							</td>
							<td>
								<input type="text" id="txt_job_no" name="txt_job_no"  style="width:100px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
								<input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
							</td>
							<td align="center" > 
								<? 
									$valueWithArr=array(1=>'Value With 0',2=>'Value Without 0');
									echo create_drop_down( "cbo_value_with", 100, $valueWithArr,"", 0, "-- All --", 0, "" );
								?>
							</td>
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px" value="<? echo date("d-m-Y", time()-86400);?>"  placeholder="From Date" readonly/>&nbsp;To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" value="<? echo date("d-m-Y", time()-86400);?>" placeholder="To Date" readonly/>
							</td>
                            <td> 
								<?   
									$get_upto=array(1=>"Greater Than",2=>"Less Than",3=>"Greater/Equal",4=>"Less/Equal",5=>"Equal");
									echo create_drop_down( "cbo_get_upto", 80, $get_upto,"", 1, "- All -", 0, "",0 );
                                ?>
                            </td>
                            <td align="center">
                            	<input type="text" id="txt_days" name="txt_days" class="text_boxes_numeric" style="width:50px" value="" />
                            </td>
                            <td> 
								<?
                                	echo create_drop_down( "cbo_get_upto_qnty", 80, $get_upto,"", 1, "- All -", 0, "",0 );
                                ?>
                            </td>
                            <td>
                            	<input type="text" id="txt_qnty" name="txt_qnty" class="text_boxes_numeric" style="width:50px" value="" />
                            </td>
							<td>
								<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_generate_report(1)" /> 
							</td>
						</tr>
						<tr>
							<td colspan="13" align="center">
								<? echo load_month_buttons(1); ?> 
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</form> 
	</div>
	<div id="report_container" style="margin:10px 0;"></div>
	<div id="report_container2"></div>  
</body>
<script type="text/javascript">
	set_multiselect('cbo_company_name','0','0','0','0');
	set_multiselect('cbo_store_name','0','0','0','0');
	$("#multi_select_cbo_company_name a").click(function(){
		load_store_buyer();
 	});
	function load_store_buyer()
	{
		var company=$("#cbo_company_name").val();
		load_drop_down( 'requires/finish_goods_closing_stock_report_controller', company, 'load_drop_down_store', 'store_td' );
		load_drop_down( 'requires/finish_goods_closing_stock_report_controller', company, 'load_drop_down_buyer', 'buyer_td' );
		set_multiselect('cbo_store_name','0','0','0','0');
	}
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
