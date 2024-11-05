<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Color And Size Breakdown Report.
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	29-12-2013
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
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Color And Size Breakdown Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';	

	function openmypage_job()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		/*		else 
			{	
		*/			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_year").val()+"_"+$("#txt_order_type").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/size_and_color_break_report_controller.php?data='+data+'&action=job_no_popup', 'Job No Search', 'width=700px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_job_id");
				var response=theemailid.value.split('_');
				if ( theemailid.value!="" )
				{
					//alert (response[0]);
					freeze_window(5);
					$("#hidd_job_id").val(response[0]);
					$("#txt_job_no").val(response[1]);
					release_freezing();
				}
			}
		//}
	}
	
	function openmypage_po()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
/*		else
		{	
*/			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#txt_job_no").val()+"_"+$("#cbo_year").val()+"_"+$("#txt_order_type").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/size_and_color_break_report_controller.php?data='+data+'&action=po_no_popup', 'PO No Search', 'width=500px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				if (theemailid.value!="" || theemailval.value!="")
				{
					//alert (theemailid.value);
					freeze_window(5);
					$("#hidd_po_id").val(theemailid.value);
					$("#txt_po_no").val(theemailval.value);
					release_freezing();
				}
			}
		//}
	}
	
	function fn_report_generated(type)
	{
		freeze_window(3);
		var cbo_company=document.getElementById('cbo_company_id').value;
		var cbo_buyer_id=document.getElementById('cbo_buyer_id').value;
		var txt_job_no=document.getElementById('txt_job_no').value;
		var txt_po_no=document.getElementById('txt_po_no').value;
		var txt_date_from=document.getElementById('txt_date_from').value;
		var txt_date_to=document.getElementById('txt_date_to').value;
		
		/*if(cbo_buyer_id==0 && txt_job_no=='' && txt_po_no=='' && (txt_date_from=='' || txt_date_to=='')){
			var divData="cbo_company_id*cbo_buyer_id";	
			var msgData="Company Name*Buyer";	
		}
		else{
			var divData="cbo_company_id";	
			var msgData="Company Name";	
		}*/
		if(cbo_buyer_id==0 && cbo_company==0){
			var divData="cbo_company_id*cbo_buyer_id";	
			var msgData="Company Name*Buyer";
		}
		else{
			var divData="cbo_company_id";	
			var msgData="Company Name";
		}
		if(type==1)
		{
			if(form_validation("txt_file_no","File No")==false)
			{
				release_freezing();
				return;
			}
		}
		if(type==2)
		{
			if(form_validation("txt_po_no","PO No")==false)
			{
				release_freezing();
				return;
			}
		}
		
		if(cbo_buyer_id==0 && cbo_company==0)
		{
			if(form_validation(divData,msgData)==false){
				release_freezing();
				return;
			}
		}		
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			if(type==1)
			{
				var data="action=report_generate_show2"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*hidd_job_id*hidd_po_id*txt_po_no*txt_date_from*txt_date_to*cbo_year*txt_file_no*txt_ref_no*txt_order_type*cbo_presantation_type*txt_style_ref*txt_style_description*cbo_brand_id*cbo_season_id*cbo_season_year*cbo_order_status',"../../")+'&report_title='+report_title+'&type='+type;
			}
			else if(type==2)
			{
				var data="action=report_generate_summary"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*hidd_job_id*hidd_po_id*txt_po_no*txt_date_from*txt_date_to*cbo_year*txt_file_no*txt_ref_no*txt_order_type*cbo_presantation_type*txt_style_ref*txt_style_description*cbo_brand_id*cbo_season_id*cbo_season_year*cbo_order_status',"../../")+'&report_title='+report_title+'&type='+type;
			}
			else if(type==3)
			{
				var data="action=report_generate_show3"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*hidd_job_id*hidd_po_id*txt_po_no*txt_date_from*txt_date_to*cbo_year*txt_file_no*txt_ref_no*txt_order_type*cbo_presantation_type*txt_style_ref*txt_style_description*cbo_brand_id*cbo_season_id*cbo_season_year*cbo_order_status',"../../")+'&report_title='+report_title+'&type='+type;
			}
			else if(type==5)
			{
				var data="action=report_generate_show5"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*hidd_job_id*hidd_po_id*txt_po_no*txt_date_from*txt_date_to*cbo_year*txt_file_no*txt_ref_no*txt_order_type*cbo_presantation_type*txt_style_ref*txt_style_description*cbo_brand_id*cbo_season_id*cbo_season_year*cbo_order_status',"../../")+'&report_title='+report_title+'&type='+type;
			}
			else if(type==6)
			{
				var data="action=report_generate6"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*hidd_job_id*hidd_po_id*txt_po_no*txt_date_from*txt_date_to*cbo_year*txt_file_no*txt_ref_no*txt_order_type*cbo_presantation_type*txt_style_ref*txt_style_description*cbo_brand_id*cbo_season_id*cbo_season_year*cbo_order_status',"../../")+'&report_title='+report_title+'&type='+type;
			}else if(type==8)
			{
				var data="action=report_generate8"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*hidd_job_id*hidd_po_id*txt_po_no*txt_date_from*txt_date_to*cbo_year*txt_file_no*txt_ref_no*txt_order_type*cbo_presantation_type*txt_style_ref*txt_style_description*cbo_brand_id*cbo_season_id*cbo_season_year*cbo_order_status',"../../")+'&report_title='+report_title+'&type='+type;
			}
			else if(type==10)
			{
				var data="action=report_generate10"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*hidd_job_id*hidd_po_id*txt_po_no*txt_date_from*txt_date_to*cbo_year*txt_file_no*txt_ref_no*txt_order_type*cbo_presantation_type*txt_style_ref*txt_style_description*cbo_brand_id*cbo_season_id*cbo_season_year*cbo_order_status',"../../")+'&report_title='+report_title+'&type='+type;
			}
			else if(type==11)
			{
				var data="action=report_generate11"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*hidd_job_id*hidd_po_id*txt_po_no*txt_date_from*txt_date_to*cbo_year*txt_file_no*txt_ref_no*txt_order_type*cbo_presantation_type*txt_style_ref*txt_style_description*cbo_brand_id*cbo_season_id*cbo_season_year*cbo_order_status',"../../")+'&report_title='+report_title+'&type='+type;
			}
			else
			{ 
				var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*hidd_job_id*hidd_po_id*txt_po_no*txt_date_from*txt_date_to*cbo_year*txt_file_no*txt_ref_no*txt_order_type*cbo_presantation_type*txt_style_ref*txt_style_description*cbo_brand_id*cbo_season_id*cbo_season_year*cbo_order_status',"../../")+'&report_title='+report_title+'&type='+type;
			}
			//alert(operation);
			http.open("POST","requires/size_and_color_break_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setc()
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function fn_report_generated_cutoff(operation)
	{
		var cbo_company=document.getElementById('cbo_company_id').value;
		var cbo_buyer_id=document.getElementById('cbo_buyer_id').value;

		if(cbo_buyer_id==0 && cbo_company==0){
			var divData="cbo_company_id*cbo_buyer_id";	
			var msgData="Company Name*Buyer";	
		}
		else{
			var divData="cbo_company_id";	
			var msgData="Company Name";	
		}
		
		if(cbo_buyer_id==0 && cbo_company==0)
		{
			if(form_validation(divData,msgData)==false){
			return;
			}
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate_cutoff"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_job_no*hidd_job_id*hidd_po_id*txt_po_no*txt_date_from*txt_date_to*cbo_year*txt_ref_no*txt_file_no*txt_order_type*cbo_presantation_type*txt_style_ref*txt_style_description*cbo_brand_id*cbo_season_id*cbo_season_year*cbo_order_status',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/size_and_color_break_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_cutoff_reponse;
		}
	}
	
	function fn_report_generated_cutoff_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
			//$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
			release_freezing();
		}
	}
		
	
	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}
	
	function openmypage_job_color_size(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
		}
	}
	function openmypage_fabric_dtls(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=250px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("txt_selected_id");
			var theemail_job=this.contentDoc.getElementById("txt_job_no");
			if (theemail.value!=""){
				//document.getElementById('txt_select_item').value=theemail.value;
				var company=document.getElementById('cbo_company_id').value;
				var data="action=packing_list_for_cutting&fabric_id="+theemail.value+'&company_id='+company+'&job_no='+theemail_job.value+
				'&path=../../../';
				console.log(data);
				http.open("POST","requires/size_and_color_break_report_controller.php",true);

				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = function()
				{
					var file_data=http.responseText.split("****");
					if(http.readyState == 4)
					{
						var w = window.open("Surprise", "_blank");
						var d = w.document.open();
						d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
						d.close();
						release_freezing();
				   	}
				}
			}
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="330px";
	}
	
	function fnRemoveHidden(str){
		document.getElementById(str).value='';
	}
	
	function fnc_file_ref_dis_en(val)
	{
		if(val==1)
		{
			$('#txt_file_no').removeAttr('disabled','disabled');
			$('#txt_ref_no').removeAttr('disabled','disabled');
		}
		else if(val==2)
		{
			$('#txt_file_no').attr('disabled','disabled');
			$('#txt_ref_no').attr('disabled','disabled');
		}
	}
	
	function set_week_date()
	{
		var weekFrom=document.getElementById('cboFromWeek').value*1;
		var weekTo=document.getElementById('cboToWeek').value*1;
		var year=document.getElementById('cbo_year_selection').value;
		if(weekFrom && weekTo)
		{
			$('.month_button').attr('disabled','true');
			$('.month_button_selected').attr('disabled','true');
			$('#txt_date_from').attr('disabled','true');
			$('#txt_date_to').attr('disabled','true');
			var week_date=return_global_ajax_value(weekFrom+"_"+weekTo+"_"+year, 'week_date', '', 'requires/size_and_color_break_report_controller');
			var week_date_arr=week_date.split('_');
			document.getElementById('txt_date_from').value=week_date_arr[0];
			document.getElementById('txt_date_to').value=week_date_arr[1];
		}else{
			$('.month_button').removeAttr('disabled');
			$('.month_button_selected').removeAttr('disabled');
			$('#txt_date_from').removeAttr('disabled');
			$('#txt_date_to').removeAttr('disabled');
			$('#txt_date_from').val('');
			$('#txt_date_to').val('');
		}
	}
	
	function fnc_brandload()
	{
		var buyer=$('#cbo_buyer_id').val();
		if(buyer!=0)
		{
			load_drop_down( 'requires/size_and_color_break_report_controller', buyer, 'load_drop_down_brand', 'brand_td');
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey(); fnc_brandload();">
<form id="colorsizebreakdown_report">
<div style="width:100%; margin:1px auto;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <h3 style="width:1460px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" style="width:1460px" > 		 
            <fieldset style="width:1460px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="130" class="must_entry_caption">Company Name</th>
                    <th width="120">Buyer</th>
                    <th width="70">Brand</th>
                    <th width="70">Season</th>
                    <th width="50">Season Year</th>
                    <th width="70">Search Type</th>
                    <th width="50">Job Year</th>
                    <th width="60">Job No.</th>
                    <th width="80">Style Ref.</th>
					<th width="80">Style Description</th>
                    <th width="80">PO No.</th>
                    <th width="80">Presentation</th>
                    <th width="70">File No.</th>
                    <th width="70">IR/IB</th>
                    <th width="60">From Week</th>
                    <th width="60">To Week</th>
                    <th width="120" colspan="2">Ship Date</th>
                    <th width="60">Ship Status</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('colorsizebreak_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/size_and_color_break_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); get_php_form_data( this.value, 'company_wise_report_button_setting','requires/size_and_color_break_report_controller');" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, ""); ?></td>
                        <td id="brand_td"><?=create_drop_down( "cbo_brand_id", 70, $blank_array,"", 1, "-- All Brand --", $selected, "",0,"" ); ?></td>
                        <td id="season_td"><?=create_drop_down( "cbo_season_id", 70, $blank_array,"", 1, "-- All Brand --", $selected, "",0,"" ); ?></td>
                        <td><?=create_drop_down("cbo_season_year",50,create_year_array(),"",1,"-Year-", $selected, "" ); ?></td>
                        <td><? 
                            $order_type_arr=array(1=>'All',2=>'With Order');
                            echo create_drop_down( "txt_order_type", 70, $order_type_arr,"", 0, "", 1, "",0,"","" );
                            ?></td>
                        <td><? echo create_drop_down( "cbo_year", 50, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                        <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:48px" placeholder="Write/Browse" onChange="fnRemoveHidden('hidd_job_id');" onDblClick="openmypage_job();"  />
                            <input type="hidden" id="hidd_job_id" name="hidd_job_id" style="width:40px" />
                        </td>
                        <td><input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:68px" placeholder="Write" /></td>
						<td><input type="text" name="txt_style_description" id="txt_style_description" class="text_boxes" style="width:68px" placeholder="Write" /></td>
                        <td>
                            <input type="text" name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:68px" placeholder="Write/Browse" onChange="fnRemoveHidden('hidd_po_id');" onDblClick="openmypage_po();"  />
                            <input type="hidden" id="hidd_po_id" name="hidd_po_id" style="width:50px" />
                        </td>
                        <td><? 
                            $presantation_type_arr=array(1=>'With File & Ref.',2=>'Without File & Ref.');
                            echo create_drop_down( "cbo_presantation_type", 80, $presantation_type_arr,"", 0, "", 1, "fnc_file_ref_dis_en(this.value);",0,"","" );
                            ?></td>
                        <td><input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:58px" placeholder="Write"/></td>
                        <td><input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:58px" placeholder="Write"/></td>
                        <td> 
							<?
                            $weekArr=array();
                            $sql=sql_select("select id, week from week_of_year where year=".date("Y"));
                            foreach($sql as $row){
                            	$weekArr[$row[csf('week')]]="W-".$row[csf('week')];
                            }
                            echo create_drop_down( "cboFromWeek", 60, $weekArr,"", 1, "-Select-", $selected, "set_week_date();");
                            ?>
                        </td>
                        <td><?=create_drop_down( "cboToWeek", 60, $weekArr,"", 1, "-Select-", $selected, "set_week_date();"); ?></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"/></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" /></td>
                        <td><?=create_drop_down( "cbo_order_status", 60, $order_status,"", 1, "-All-", $selected,"", "" ); ?></td>
                        <td><input type="button" id="show_button1" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0);" /></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="11"><?=load_month_buttons(1); ?></td>
                        <td align="center"><input type="button" id="show_button2" class="formbutton" style="width:60px" value="By Cut-Off" onClick="fn_report_generated_cutoff(0);" /></td>
                        <td align="center"><input type="button" id="show_button3" class="formbutton" style="width:60px" value="Show 2" onClick="fn_report_generated(1);" /></td>
						<td align="center"><input type="button" id="show_button4" class="formbutton" style="width:60px" value="Show 3" onClick="fn_report_generated(3);" /></td>
                        <td align="center"><input type="button" id="show_button5" class="formbutton" style="width:60px" value="Show 4" onClick="fn_report_generated(4);" /></td>
                        <td align="center"><input type="button" id="show_button6" class="formbutton" style="width:60px" value="Show 5" onClick="fn_report_generated(5);" /></td>
						<td align="center"><input type="button" id="show_button7" class="formbutton" style="width:60px" value="Ratio wise" onClick="fn_report_generated(6);" /></td>
						<td align="center"><input type="button" id="show_button10" class="formbutton" style="width:60px" value="Show 6" onClick="fn_report_generated(10);" /></td>
						<td align="center"><input type="button" id="show_button9" class="formbutton" style="width:70px" value="Inseam Wise" onClick="fn_report_generated(8);" /></td>
						<td align="center"><input type="button" id="show_button11" class="formbutton" style="width:60px" value="Show 7" onClick="fn_report_generated(11);" /></td>
                        <td align="center"><input type="button" id="show_button8" class="formbutton" style="width:60px" value="Summary" onClick="fn_report_generated(2);" /></td>
						<a id="slab_report" href="" style="text-decoration:none" download="" hidden="">Slab</a>
                    </tr>
                </tbody>
            </table> 
        	</fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </div>
</form> 
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
