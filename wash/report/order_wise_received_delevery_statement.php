<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Order Wise Received & Delevery Statement Report.
Functionality	:	
JS Functions	:
Created by		:	Md Mahbubur Rahnan
Creation date 	: 	16-03-2020
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
echo load_html_head_contents("Order Wise Received And Delevery Statement", "../../", 1, 1,$unicode,1,1);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	var tableFilters = 
	{
		col_29: "none",
		col_operation: {
		id: ["gt_Yrecev_qty_id","gt_Ydel_qty_id","gt_toreceev_qty_id","gt_ToDeli_qty_id","gt_MorDel_qty_id","gt_baln_qty_id"],
		col: [9,10,11,12,13,14],
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
		if (form_validation('cbo_company_id','Comapny Name')==false)
		{
			return;
		}
		else	
		{
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_party_name*txt_date_from*cbo_within_group*cbo_type*txt_search_string',"../../")+'&report_title='+report_title;
			freeze_window(operation);
			//freeze_window(3);
			http.open("POST","requires/order_wise_received_delevery_statement_controller.php",true);
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
			setFilterGrid("table_body",-1,tableFilters);
			setFilterGrid("table_body1",-1,tableFilterss);
			setFilterGrid("table_body11",-1,tableFilterss1);
			setFilterGrid("table_body12",-1,tableFilterss2);
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tbody').find('tr:first').hide();
		
		document.getElementById('scroll_body1').style.overflow="auto";
		document.getElementById('scroll_body1').style.maxHeight="none";
		$('#table_body1 tbody').find('tr:first').hide();
		
		document.getElementById('scroll_body11').style.overflow="auto";
		document.getElementById('scroll_body11').style.maxHeight="none";
		$('#table_body11 tbody').find('tr:first').hide();
		
		document.getElementById('scroll_body12').style.overflow="auto";
		document.getElementById('scroll_body12').style.maxHeight="none";
		$('#table_body12 tbody').find('tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#table_body tbody').find('tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$('#table_body1 tbody').find('tr:first').show();
		document.getElementById('scroll_body1').style.overflowY="scroll";
		document.getElementById('scroll_body1').style.maxHeight="none";
		
		$('#table_body11 tbody').find('tr:first').show();
		document.getElementById('scroll_body11').style.overflowY="scroll";
		document.getElementById('scroll_body11').style.maxHeight="none";
		
		$('#table_body12 tbody').find('tr:first').show();
		document.getElementById('scroll_body12').style.overflowY="scroll";
		document.getElementById('scroll_body12').style.maxHeight="none";
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
			load_drop_down( 'requires/order_wise_received_delevery_statement_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/order_wise_received_delevery_statement_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
		}
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
                    <th width="125">Party Name</th>
                    <th width="100" >Search By</th>
                    <th width="100" id="search_by_td">Style</th>
                    <th width="120">Date</th>
                    <th width=""><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                </thead>
                <tbody>
                    <tr >
                        <td  align="center"> 
                            <?
								echo create_drop_down( "cbo_company_id", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(1,document.getElementById('cbo_within_group').value);");
                            ?>
                        </td>
                        <td><?php echo create_drop_down( "cbo_within_group", 110, $yes_no,"", 1, "-- Select  --", 0, "fnc_load_party(1,this.value); " ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>
                        <td>
						<?
                            $search_by_arr=array(1=>"Style",2=>"Order No");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center" >
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:120px" placeholder="Enter Date" >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
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
