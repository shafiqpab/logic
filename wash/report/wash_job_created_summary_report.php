<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Wash Production Report.
Functionality	:	
JS Functions	:
Created by		:	Md Mahbubur Rahnan
Creation date 	: 	04-11-2019
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
echo load_html_head_contents("Wash Production Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	 /*var tableFilters = 
	 {
		col_operation: {
		   id: ["gt_receeive_qty_id","gt_whisker_qty_id","gt_hand_sand_qty_id","gt_tagging_qty_id","gt_tieing_qty_id","gt_grinding_qty_id","gt_first_wash_qty_id","gt_destroy_qty_id","gt_ythreed_qty_id","gt_final_wash_qty_id","gt_ythreed_qty_id","gt_wrinkle_qty_id","gt_laser_whisker_qty_id","gt_laser_brush_qty_id","gt_laser_destroy_qty_id","gt_laser_chemo_print_qty_id","gt_first_dyeing_qty_id","gt_laser_others_qty_id","gt_delevery_qty_id","gt_blance_qty_id"],
		   col: [8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27],
		   operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}	

	 }*/
	
	function fn_report_generated(operation)
	{
		   var job_no = $('#txt_job_no').val();
 			if(job_no!="")
			{
				if (form_validation('cbo_company_id','Comapny Name')==false)//*txt_date_from*txt_date_to----*From Date*To Date
				{
				return;
				}	
			}
			else
			{
				if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Comapny Name*From Date*To Date')==false)//*txt_date_from*txt_date_to----*From Date*To Date
				{
				return;
				}
			} 
		 
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_party_name*txt_job_no*cbo_gmts_type*txt_order_no*cbo_date_category*txt_date_from*txt_date_to*cbo_within_group*cbo_year_selection',"../../")+'&report_title='+report_title;
			freeze_window(operation);
			//freeze_window(3);
			http.open("POST","requires/wash_job_created_summary_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
	   
	}

	
	function fn_report_generated_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tbody').find('tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#table_body tbody').find('tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="none";
	}


	function show_progress_report_details(action,order_id,color_id,mst_id,dtls_id,width)
	{ 
		 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/wash_job_created_summary_report_controller.php?action='+action+'&order_id='+order_id+'&color_id='+color_id+'&mst_id='+mst_id+'&dtls_id='+dtls_id, 'Wash Job Rate Report Details.', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../');
		
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

	function openmypage_job()
	{ 
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company_name=document.getElementById('cbo_company_id').value;
		var cbo_buyer_name=document.getElementById('cbo_party_name').value;
		var cbo_within_group=document.getElementById('cbo_within_group').value;
		var page_link="requires/wash_job_created_summary_report_controller.php?action=job_no_popup&company_id="+company_name+"&cbo_buyer_name="+cbo_buyer_name+"&cbo_within_group="+cbo_within_group;
		var title="Job Number";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=530px,height=420px,center=1,resize=0,scrolling=0','../')
	
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("selected_id").value;
			//var job=theemail.split("_");
			document.getElementById('txt_job_no').value=theemail;
			release_freezing();
		}
	}
	
	function fnc_load_party(type,within_group)
	{
		
		//alert(type);
		if ( form_validation('cbo_company_id','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		
		if(within_group==0)
		{
			$('#cbo_party_name').attr('disabled',true);
		}
		else
		{
			$('#cbo_party_name').attr('disabled',false);
			
		}
		//$('#txtOrderDeliveryDate_1').val($('#txt_delivery_date').val());
		var company = $('#cbo_company_id').val();
		var party_name = $('#cbo_party_name').val();
		if(within_group==1 && type==1) 
		{
			load_drop_down( 'requires/wash_job_created_summary_report_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/wash_job_created_summary_report_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
		}
	}

	function image_popup(ids)
	{
		//alert(ids);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/wash_job_created_summary_report_controller.php?data='+ids+'&action=view_image_dtls', 'Image Details','width=500px,height=300px,resize=0,scrolling=0','../')
	}




		
</script>
</head>
<body onLoad="set_hotkey();">
<form id="washProductionReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:1150px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1150px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="135" class="must_entry_caption">Company</th>
                    <th width="110" class="must_entry_caption">Within Group</th>
                    <th width="125">Party </th>
                    <th width="130">Job No</th>
                    <th width="100">Gmts Type</th>
                    <th width="100">Party WO</th>
                    <th width="100">Date Category</th>
                    <th width="180" class="must_entry_caption">Date Range</th>
                    
                    <th width=""><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr >
                        <td  align="center"> 
                            <?
								echo create_drop_down( "cbo_company_id", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(1,document.getElementById('cbo_within_group').value);");
                            ?>
                        </td>
                        <td><?php 
                        $witnin_group = array(0=>'All',1=>'Yes',2=>'No');
                        echo create_drop_down( "cbo_within_group", 110, $witnin_group,"", 0, "--  --",1, "fnc_load_party(1,this.value); " ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>
                        
                        <td>
                            <input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:125px" placeholder="Wr/Br Job" onDblClick="openmypage_job();" >
                        </td>
                        <td><? 
							echo create_drop_down( "cbo_gmts_type", 100, $wash_gmts_type_array,"", 1, "-- Select Type --", $selected, "" ); ?>
								
						</td>
                        <td >
                            <input type="text" name="txt_order_no" id="txt_order_no" value="" class="text_boxes" style="width:100px;" placeholder="Write"/>                    							
                        </td>
                        <td> 
						   <?   
                            $date_cat=array(1=>"Order Receive Date",2=>"Target Delv Date");
							echo create_drop_down( "cbo_date_category", 100, $date_cat, "", 0, "--All--","", " ", "", ""); //$('#th_date_caption').html($('#cbo_date_category option:selected').text());
                            ?>
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px" placeholder="From Date" >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px"  placeholder="To Date"  >
                        </td>
                        
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10" align="center">
							<? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tfoot>
           </table> 
           <br />
        </fieldset>
    </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
