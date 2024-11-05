<?
/*-------------------------------------------- Comments----------------
Purpose			: 	This form will create Order Booking Status Report
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	28-07-2019
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: 
-----------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------
echo load_html_head_contents("Order Booking Status Report","../../../", 1, 1, $unicode,1,"");
?>	
<script>
	var permission='<? echo $permission; ?>';
	//[14,17,18,20,21,22,23,24,25,26,27,28]
	var tableFilters = 
	{
		col_operation: 
		{
			id: ["td_tsmv","td_pQtyPcs","td_poValue","td_commission","td_netPoValue","td_exQtyPcs","td_exValue","td_exBalPcs","td_exBalValue"],
			//col: [18,21,23,24,25,26,27,28,29],
			col: [19,22,24,25,26,27,28,29,30],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		},
		col_1: "select",
		col_2: "select",
		col_7: "select",
		col_8: "select",
		col_12: "select",
		col_20: "select",
		col_31: "select",
		col_32: "select",
		col_33: "select",
		col_34: "select",
		display_all_text:'Show All'
	}
	
	function fnc_generate_report(type)
	{
		var txt_style_ref = $("#txt_style_ref").val();
		var cbo_style_owner = $("#cbo_style_owner").val();
		// if(form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
		// {
		// 	return;
		// }
		if(txt_style_ref!="")
		{
			if(form_validation('cbo_style_owner','Style Owner')==false)
			{
				return;
			}
		}
		else
		{
				
			if(form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
				{
					return;
				}
		}
		var report_title=$( "div.form_caption" ).html();

		var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_style_owner*cbo_buyer_name*cbo_agent*txt_style_ref*cbo_year*cbo_team_name*cbo_team_member*cbo_factory_merchant*cbo_product_category*cbo_category_by*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
		
		freeze_window(3);
		http.open("POST","requires/order_booking_status_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=(http.responseText).split('****');	
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			if(response[2]==1) setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		$("#table_body tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflow="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";

		$("#table_body tr:first").show();
	}	
	
	function print_report_part_by_part(id,button_id)
	{
		$(button_id).removeAttr("onClick").attr("onClick","javascript:window.print()");
		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title></head><body>'+document.getElementById(id).innerHTML+'</body</html>');
		d.close();
		$(button_id).removeAttr("onClick").attr("onClick","print_report_part_by_part('"+id+"','"+button_id+"')");
	}
	
	function set_defult_date(companyId){
		var defult_date=return_global_ajax_value(companyId, 'get_defult_date', '', 'requires/order_booking_status_controller');
		document.getElementById('cbo_category_by').value=trim(defult_date);
		//alert(defult_date);
		
	}
	function generate_ex_factory_popup(action,job_no,id,width)
	{
		//alert(job_no); 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_booking_status_controller.php?action='+action+'&job_no='+job_no+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
	

	function fnc_load_report_format()
    {
        var data=$('#cbo_style_owner').val();
        var report_ids = return_global_ajax_value( data, 'load_report_format', '', 'requires/order_booking_status_controller');
        print_report_button_setting(report_ids);
    }

    function print_report_button_setting(report_ids)
    {
        if(trim(report_ids)=="")
        {
            $("#search1").show();
            $("#summary1").show();
         }
        else
        {
            var report_id=report_ids.split(",");
            $("#search1").hide();
            $("#summary1").hide();
             for (var k=0; k<report_id.length; k++)
            {
                if(report_id[k]==108)
                {
                    $("#search1").show();
                }
                else if(report_id[k]==149)
                {
                    $("#summary1").show();
                }
            }
        }
    }
</script>
</head>
<body onLoad="set_defult_date(document.getElementById('cbo_style_owner').value);fnc_load_report_format()">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <form name="orderBooking_1" id="orderBooking_1" autocomplete="off" > 
        <h3 style="width:1200px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
        <div id="content_search_panel" style="width:1200px" >      
			<fieldset>
                <table align="center" class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="140">Style Owner</th>
                            <th width="130">Buyer</th>
                            <th width="130">Agent</th>
                            <th width="80">Style</th>
                            <th width="50">Job Year</th>
                            <th width="80">Team</th>
                            <th width="80">Dealing Merchant</th>
                            <th width="80">Factory Merchant</th>
                            <th width="80">Product Category</th>
                            <th width="90">Date Category</th>
                            <th width="120" class="must_entry_caption" colspan="2">Date</th>
                            <th><input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                         <td><? echo create_drop_down( "cbo_style_owner", 140, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "set_defult_date(this.value);" ); ?></td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" ); ?></td>
                        <td id="agent_td"><? echo create_drop_down( "cbo_agent", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by  buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Agent--", $selected, "" ); ?></td>
                        <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" /></td>
                        <td><? echo create_drop_down( "cbo_year", 50, create_year_array(),"", 1,"-All-",0 , "",0,"" ); ?></td>
                        <td><? echo create_drop_down( "cbo_team_name", 80, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( 'requires/order_booking_status_controller', this.value, 'load_drop_down_team_member', 'team_td' );load_drop_down( 'requires/order_booking_status_controller', this.value, 'cbo_factory_merchant', 'div_marchant_factory' ) " ); ?></td>
                        
                        <td id="team_td"><? echo create_drop_down( "cbo_team_member", 80, $blank_array,"", 1, "-Select Dealing Merchant- ", $selected, "" ); ?></td>
                        <td id="div_marchant_factory" ><?  echo create_drop_down( "cbo_factory_merchant", 80, $blank_array,"", 1, "-Select-", $selected, "" ); ?></td>
                        <td><? echo create_drop_down( "cbo_product_category", 80, $product_category,"", 1, "-- Select --", $selected, ""  ); ?></td>
                        <td><? echo create_drop_down( "cbo_category_by", 90, $report_date_catagory,"", 0, "", $selected, "",'',"1,2,3,4" ); ?></td>
                        <td><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:50px" placeholder="From Date"></td>
                        <td><input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:50px" placeholder="To Date"></td>
                        <td><input type="button" name="search" id="search1" value="Show" onClick="fnc_generate_report(1)" style="width:70px" class="formbutton" /></td>
                    </tr>
                    <tr>
                        <td colspan="12" align="center"><? echo load_month_buttons(1); ?></td>
                        <td align="center"><input type="button" name="summary" id="summary1" value="Summary" onClick="fnc_generate_report(2)" style="width:70px;" class="formbutton" /></td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2"></div>
        </form>
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	set_multiselect('cbo_style_owner','0','0','','0',"fnc_load_report_format();");
</script>
<?
		$sql=sql_select("select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name");
		$company_id='';
		$is_single_select=0;
		if(count($sql)==1){
			$company_id=$sql[0][csf('id')];
			$is_single_select=1;
			?>
			<script>
			console.log('shariar');
			set_multiselect('cbo_style_owner','0','<? echo $is_single_select?>','<? echo $company_id?>','0');
			
			</script>
			
			<?
		}
		
		?>
</html>