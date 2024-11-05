<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Purchase Status Report.
Functionality	:
JS Functions	:
Created by		:	Rakib
Creation date 	: 	09-01-2021
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
echo load_html_head_contents("Purchase Status Report", "../../", 1, 1,'',1,1);
?>

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	var permission = '<? echo $permission; ?>';

	function generate_report(type)
	{
		var report_title=$("div.form_caption" ).html();
		var requisition_no = $("#requisition_no").val();
		if (requisition_no != '')
		{
			if(form_validation("cbo_company_id","Company Name")==false){
	            return;
	        }
		}
		else
		{
			if(form_validation("cbo_company_id*txt_date_from*txt_date_to","Company Name*Date From*Date To")==false){
	            return;
	        }
		}	
			
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_department_id*item_category_id*requisition_no*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title+'&type='+type;

		freeze_window(3);
		http.open("POST","requires/purchase_status_report_controller.php",true);
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
			
			setFilterGrid("table_body",-1);
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
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
		$("#table_body tr:first").show();

	}	
	
	function openmypage_itemCategory()
    {
        var cbo_company = $("#cbo_company_id").val();

        if(form_validation("cbo_company_id","Company Name")==false){
            return;
        }

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/purchase_status_report_controller.php?action=itemCategory_popup&company='+cbo_company, "Item Category", 'width=400px,height=380px,center=1,resize=0,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var item_category_id=this.contentDoc.getElementById("hidden_item_category_id").value;
            var item_category_name=this.contentDoc.getElementById("hidden_item_category").value;
            $("#item_category_id").val(item_category_id);
            $("#item_category").val(item_category_name);
        }
    }

	function general_purchase_req_report(print_btn,cbo_company_name,update_id,is_approved,location_id,txt_remark,type){
		var report_title=$( "div.form_caption" ).html();
		var template_id=1;

		if(print_btn==118){

			 print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+is_approved+'*'+''+'*'+template_id+'*'+location_id, "purchase_requisition_print", "../../inventory/requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(print_btn==120){
			var show_item="";
            
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id, "purchase_requisition_print_2", "../../inventory/requires/purchase_requisition_controller" ) ;
			show_msg("3");
		}
		else if(print_btn==122)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_3", "../../inventory/requires/purchase_requisition_controller" ) ;
			show_msg("3");
			
			// print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val()+'*'+$('#is_approved').val(), "purchase_requisition_print_3", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(print_btn==169)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_8", "../../inventory/requires/purchase_requisition_controller" ) ;

			//  print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_8", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(print_btn==425)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_26", "../../inventory/requires/purchase_requisition_controller" ) ;

			//  print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_26", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(print_btn==123)
		{

			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_4", "../../inventory/requires/purchase_requisition_controller" ) ;
		
			//  print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_4", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(print_btn==165)
		{

			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_9", "../../inventory/requires/purchase_requisition_controller" ) ;
			
			//  print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_9", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(print_btn==129)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_5", "../../inventory/requires/purchase_requisition_controller" ) ;
			
			//  print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_5", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(print_btn==227)
		{

			
			var show_item="";
			//var r=confirm("Press  \"Cancel\"  to hide  Last Rate & Req Value \nPress  \"OK\"  to Show Last Rate & Req Value");
			var r=confirm("Press  \"Cancel\"  to hide Last Rec. Date & Last Rec. Qty & Last Rate & Req. Value \nPress  \"OK\"  to Show Last Rec. Date & Last Rec. Qty & Last Rate & Req. Value");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_10", "../../inventory/requires/purchase_requisition_controller" ) ;
			
			//  print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_10", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(print_btn==241)
		{
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+''+'*'+template_id+'*'+location_id, "purchase_requisition_print_11", "../../inventory/requires/purchase_requisition_controller" ) ;
			
			 //print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+''+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_11", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(print_btn==580)
		{

			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_4_akh", "../../inventory/requires/purchase_requisition_controller" ) ;
		
			//  print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_4_akh", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(print_btn==28)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_13", "../../inventory/requires/purchase_requisition_controller" ) ;
			
			// print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_13", "requires/purchase_requisition_controller" ) ;
			show_msg("3");
		}
		else if(print_btn==280)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_14", "../../inventory/requires/purchase_requisition_controller" ) ;
			
			// print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_14", "requires/purchase_requisition_controller" ) ;
			show_msg("3");
		}
		else if(print_btn==688)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_15", "../../inventory/requires/purchase_requisition_controller" ) ;
			
			//print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_15", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(print_btn==243)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_16", "../../inventory/requires/purchase_requisition_controller" ) ;
			
			// print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_16", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(print_btn==310)
		{
			var show_item="";
			var r=confirm("Press \"OK\" Show With Model / Article, Size/MSR, Brand \nPress \"Cancel\" Show Without Model / Article, Size/MSR, Brand");
			if(r==true){ show_item=1; }else{ show_item=0; }
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_category_wise_print", "../../inventory/requires/purchase_requisition_controller" ) ;

			// print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_category_wise_print", "requires/purchase_requisition_controller" ) ;
			show_msg("3");
		}
		else if(print_btn==304)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_18", "../../inventory/requires/purchase_requisition_controller" ) ;
			
			// print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_18", "requires/purchase_requisition_controller" ) ;
			show_msg("3");
		}
		else if(print_btn==719)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_19", "../../inventory/requires/purchase_requisition_controller" ) ;
			
			// print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_19", "requires/purchase_requisition_controller" ) ;
			show_msg("3");
		}
		else if(print_btn==723)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_20", "../../inventory/requires/purchase_requisition_controller" ) ;

			// print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_20", "requires/purchase_requisition_controller" ) ;
			show_msg("3");
		}
		else if(print_btn==339)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_21", "../../inventory/requires/purchase_requisition_controller" ) ;
			
			// print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_21", "requires/purchase_requisition_controller" ) ;
			show_msg("3");
		}
		else if(print_btn==370)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_22", "../../inventory/requires/purchase_requisition_controller" ) ;
		
			//  print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_22", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(print_btn==382)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_23", "../../inventory/requires/purchase_requisition_controller" ) ;
			
			//  print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_23", "requires/purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
		else if(print_btn==235)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_24", "../../inventory/requires/purchase_requisition_controller" ) ;
			
			// print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_24", "requires/purchase_requisition_controller" ) ;
			show_msg("3");
		}
		else if(print_btn==768)
		{
			var show_item="";
			print_report( cbo_company_name+'*'+update_id+'*'+report_title+'*'+txt_remark+'*'+type+'*'+show_item+'*'+template_id+'*'+location_id, "purchase_requisition_print_25", "../../inventory/requires/purchase_requisition_controller" ) ;
			
			//print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#txt_remarks').val()+'*'+type+'*'+show_item+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "purchase_requisition_print_25", "requires/purchase_requisition_controller" );
			show_msg("3");
		}
		
	}

	function general_item_rec_report(print_btn,company,hidden_mrr_id,basis,varibale_str){
         
		var report_title=$( "div.form_caption" ).html();
       if(print_btn==66){
		var data= company+'__'+hidden_mrr_id+'__'+report_title+'__'+basis+'__'+varibale_str;
		window.open("../../inventory/general_store/requires/general_item_receive_controller.php?data=" + data+'&action='+'general_item_receive_print_new', true );

		return;
	  }
	  else if(print_btn==85){
		var data= company+'__'+hidden_mrr_id+'__'+report_title+'__'+basis+'__'+varibale_str;
		window.open("../../inventory/general_store/requires/general_item_receive_controller.php?data=" + data+'&action='+'general_item_receive_print_3', true );

		return;
	  }
	  else if(print_btn==137){
		var data= company+'__'+hidden_mrr_id+'__'+report_title+'__'+basis+'__'+varibale_str;
		window.open("../../inventory/general_store/requires/general_item_receive_controller.php?data=" + data+'&action='+'general_item_receive_print_4', true );

		return;
	  }
	  else if(print_btn==129){
		var data= company+'__'+hidden_mrr_id+'__'+report_title+'__'+basis+'__'+varibale_str;
		window.open("../../inventory/general_store/requires/general_item_receive_controller.php?data=" + data+'&action='+'general_item_receive_print_5', true );

		return;
	  }
	  else if(print_btn==72){
		var data= company+'__'+hidden_mrr_id+'__'+report_title+'__'+basis+'__'+varibale_str;
		window.open("../../inventory/general_store/requires/general_item_receive_controller.php?data=" + data+'&action='+'general_item_receive_print_6', true );

		return;
	  }
	  else if(print_btn==78){
		var data= company+'__'+hidden_mrr_id+'__'+report_title+'__'+basis+'__'+varibale_str;
		window.open("../../inventory/general_store/requires/general_item_receive_controller.php?data=" + data+'&action='+'general_item_receive_print', true );

		return;
	  }


	}
</script>

</head>

<body onLoad="set_hotkey();">
<form id="purchase_status_report">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:920px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:920px;">
                <table class="rpt_table" width="900" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th>Department</th>
                            <th>Item Category</th>
                            <th>Requisition No</th>
                            <th class="must_entry_caption">Requisition Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" onClick="reset_form('purchase_status_report','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <?
                                    echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                ?>
                            </td>
                            <td>
                                <?
                                    echo create_drop_down( "cbo_department_id", 150, "select id,department_name from lib_department where is_deleted=0 and status_active=1","id,department_name", 1, "-- Select Department --", $selected, "","","","","","");
                                ?>                               
                           	</td>
                            <td align="center">
                            	<!-- <input style="width:100px;"  name="item_category" id="item_category"  ondblclick="openmypage_itemCategory()"  class="text_boxes" placeholder="Browse"  readonly /> -->
                                <!-- <input type="hidden" name="item_category_id" id="item_category_id"/> -->
								<?
                                    echo create_drop_down( "item_category_id", 150, "select category_id, short_name from lib_item_category_list where status_active=1 and is_deleted=0 order by short_name","category_id,short_name", 1, "-- Select Department --", $selected, "","","","","","");
                                ?>  
                        	</td>
                        	<td align="center">
							    <input style="width:110px;" name="requisition_no" id="requisition_no" class="text_boxes" placeholder="Write"/>
                        	</td>                        	
                            <td align="center" width="180">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:65px" placeholder="From Date"/>&nbsp;To&nbsp;<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:65px" placeholder="To Date"/>
                        	</td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:90px;" class="formbutton" />                             
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="5" align="center"><? echo load_month_buttons(1); ?></td>                        	
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
	set_multiselect('cbo_department_id','0','0','','0');
	set_multiselect('item_category_id','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
