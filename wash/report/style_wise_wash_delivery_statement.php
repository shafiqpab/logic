<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Order Wise Received & Delevery Statement Report.
Functionality	:	
JS Functions	:
Created by		:	Aziz
Creation date 	: 	15-034-2021
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
echo load_html_head_contents("Style Wise Wash Delevery Statement", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = 
	{
		col_29: "none",
		col_operation: {
		id: ["gt_Yrecev_qty_id","gt_Ydel_qty_id","gt_toreceev_qty_id","gt_ToDeli_qty_id","gt_MorDel_qty_id","gt_access_qty_id"],
		col: [9,10,11,12,14,15],
		operation: ["sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	var tableFilterss = 
	{
		col_29: "none",
		col_operation: {
		id: ["gt_Yrecev_qty_id1","gt_Ydel_qty_id1","gt_toreceev_qty_id1","gt_ToDeli_qty_id1","gt_MorDel_qty_id1","gt_baln_qty_id1"],
		col: [9,10,11,12,13,14],
		operation: ["sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	var tableFilterss1 = 
	{
		col_29: "none",
		col_operation: {
		id: ["gt_Yrecev_qty_id11","gt_Ydel_qty_id11","gt_toreceev_qty_id11","gt_ToDeli_qty_id11","gt_MorDel_qty_id11","gt_baln_qty_id11"],
		col: [9,10,11,12,13,14],
		operation: ["sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	var tableFilterss2 = 
	{
		col_29: "none",
		col_operation: {
		id: ["gt_Yrecev_qty_id12","gt_Ydel_qty_id12","gt_toreceev_qty_id12","gt_ToDeli_qty_id12","gt_MorDel_qty_id12","gt_baln_qty_id12"],
		col: [9,10,11,12,13,14],
		operation: ["sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	} 
	
	function fn_report_generated(operation)
	{
		
		var txt_job=$("#txt_job").val();
		var cbo_shipping=$("#cbo_shipping").val();
		var cbo_party_name=$("#cbo_party_name").val();
		var cbo_within_group = $("#cbo_within_group").val();

		if(cbo_within_group==0){
			if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
			{
				return;
			}
		}else{
			if(txt_job=="" && cbo_shipping==0 && cbo_party_name==0)
			{
				if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
				{
					return;
				}
			}
		}
		
		// if(txt_job=="" && cbo_shipping==0 && cbo_party_name==0)
		// {
		// 	if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
		// 	{
		// 		return;
		// 	}
		// }
		
		if (form_validation('cbo_company_id','Comapny Name')==false)
		{
			return;
		}
		else	
		{
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_party_name*txt_date_from*txt_date_to*cbo_shipping*cbo_within_group*txt_job*txt_job_id',"../../")+'&report_title='+report_title+'&report_type='+operation;
			freeze_window(operation);
			//freeze_window(3);
			http.open("POST","requires/style_wise_wash_delivery_statement_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
			
		}
	}
	
	function fn_report_generated_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("table_body",-1,tableFilters);
			//setFilterGrid("table_body1",-1,tableFilterss);
			//setFilterGrid("table_body11",-1,tableFilterss1);
			//setFilterGrid("table_body12",-1,tableFilterss2);
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		//$('#table_body tbody').find('tr:first').hide();
		
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		//$('#table_body tbody').find('tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="none";
		
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
	function search_by(val)
	{
		$('#txt_search_string').val('');
		if(val==1 || val==0) $('#search_by_td').html('Style');
		else if(val==2) $('#search_by_td').html('Order No');
	}
		
		
	function fnc_load_party(type,within_group)
	{
		
		// alert(type);
		if ( form_validation('cbo_company_id','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		
		if(within_group==0)
		{
			$('#cbo_party_name').attr('disabled',true);
			$('#txt_job').attr('disabled',true);
			$('#cbo_party_name').val(0);
			$('#txt_job').val('');
			$('#txt_job_id').val('');
			$('#txt_job_sl').val('');
		}
		else
		{
			$('#cbo_party_name').attr('disabled',false);
			$('#txt_job').attr('disabled',false);
			
		}
		//$('#txtOrderDeliveryDate_1').val($('#txt_delivery_date').val());
		var company = $('#cbo_company_id').val();
		var party_name = $('#cbo_party_name').val();
		if(within_group==1 && type==1)
		{
			load_drop_down( 'requires/style_wise_wash_delivery_statement_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/style_wise_wash_delivery_statement_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
		}
	}
	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_party_name").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		var cbo_within_group = $("#cbo_within_group").val();
		var txt_job_id = $("#txt_job_id").val();
		var txt_job = $("#txt_job").val();
		var txt_job_sl = $("#txt_job_sl").val();
		//txt_job_id txt_job_sl
		var page_link='requires/style_wise_wash_delivery_statement_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&cbo_within_group='+cbo_within_group+'&txt_job_id='+txt_job_id+'&txt_job='+txt_job+'&txt_job_sl='+txt_job_sl;
		 
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var job_sl=this.contentDoc.getElementById("hide_job_sl").value;
			//$('#txt_job').val(job_no);
			//$('#txt_job_id').val(job_id);
			//var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			//var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			//var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_job").val(job_no);
			$("#txt_job_id").val(job_id); 
			$("#txt_job_sl").val(job_sl);	 
		}
	}
	function report_po_popup(cbo_company_name,style_ref,party_id,action,type)
    {
     
	    var cbo_within_group = $("#cbo_within_group").val();
	//	var buyer_id = $("#cbo_buyer_name").val();
	//alert(master_style_ref);
	if(type==1)
	{
		 var popup_width='650px';
	}
	 
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_wash_delivery_statement_controller.php?cbo_company_name='+cbo_company_name+'&style_ref='+style_ref+'&type='+type+'&party_id='+party_id+'&cbo_within_group='+cbo_within_group+'&action='+action, 'Remark Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
    }
	
</script>
</head>
<body onLoad="set_hotkey();">
<form id="washProductionReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <h3 style="width:900px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:900px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>                    
                    <th width="135" class="must_entry_caption">Company</th>
                    <th width="110" class="must_entry_caption">Within Group</th>
                    <th width="125">Customer</th>
                    <th width="100" id="">Wash Job No</th>
                    <th width="100" id="">Delivery Status</th>
                    <th width="120">Delivery Date</th>
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
						echo create_drop_down( "cbo_within_group", 110, $yes_no,"", 1, "-- Select  --", 1, "fnc_load_party(1,this.value); " ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>
                        
                    <td align="center" >
                        <input type="text" name="txt_job" id="txt_job" class="text_boxes" style="width:100px" placeholder="Wr./Br." onDblClick="openmypage_job();" />
                         <input type="hidden" name="txt_job_id" id="txt_job_id" class="text_boxes" style="width:50px" />
                         <input type="hidden" name="txt_job_sl" id="txt_job_sl" class="text_boxes" style="width:50px" />
                    </td>
                    <td>
						<?
                           // $search_by_arr=array(1=>"Style",2=>"Order No");
                            echo create_drop_down( "cbo_shipping",100, $delivery_status,"",1, "--All--",0,'',"0","2,3" );
                        ?>
                    </td>
                    
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:45px" placeholder="Enter Date" > &nbsp;
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:45px" placeholder="Enter Date" >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" />&nbsp;
                            <input type="button" id="show_button" class="formbutton" style="width:50px" value="Show2" onClick="fn_report_generated(2)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                        
                    </tr>
                </tbody>
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
