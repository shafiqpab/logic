<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	Shipment track report
Functionality	:	
JS Functions	:
Created by		:	Md. Sakibul Islam 
Creation date 	: 	05-09-2023
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
$user_level=$_SESSION['logic_erp']["user_level"];

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Shipment Track Report","../../", 1, 1, $unicode,1,1);
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	
	// var tableFilters = 
	//  {
	// 	col_33: "none",
	// 	col_operation: {
	// 	id: ["total_order_qnty","total_order_qnty_in_pcs","value_tot_cm_cost","value_tot_cost","value_order","value_margin","value_tot_trims_cost","value_tot_embell_cost"],
	//     col: [11,13,27,29,31,32,33,34],
	//     operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
	//     write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
	// 	}
	//  }
	
	function fn_report_generated()
	{
		var job_id=$('#txt_job_id').val();
		var job_no=$('#txt_job_no').val();
		var date_from=$('#txt_date_from').val();
		var date_to=$('#txt_date_to').val();
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else if(job_id=='' && job_no=='' && (date_from=='' || date_to=='')){
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
			{
				return;
			}
		}		
		else
		{	
			//var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*txt_job_id*txt_style_no*cbo_product_department*txt_date_from*txt_date_to',"../../../");
            var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location_id*cbo_work_company_name*cbo_work_location_id*cbo_buyer_name*txt_job_no*txt_style_no*txt_job_id*txt_int_ref*txt_date_from*txt_date_to*cbo_year_selection',"../../../");
            //alert(data);
			freeze_window(3);
			http.open("POST","requires/shipment_track_report_controller.php",true);
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
			/*var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); */

			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			append_report_checkbox('table_header_1',1);
			
			setFilterGrid("table_body",-1);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',400,700 );
	 		show_msg('3');
			release_freezing();
		}
	}



	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+ '<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$('#scroll_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}
	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{

		}
	}
	
	// function openmypage(po_id,po_qnty,po_no,job_no,type,tittle)
	// {
	// 	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_position_report_controller.php?po_id='+po_id+'&po_qnty='+po_qnty+'&po_no='+po_no+'&job_no='+job_no+'&action='+type, tittle, 'width=650px, height=350px, center=1, resize=0, scrolling=0', '../../');
	// }
		
	// function generate_pre_cost_report(po_id,job_no,company_id,buyer_id,style_ref,costing_date)
	// {
	// 	var zero_val='';
	// 	var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
	// 	if (r==true) zero_val="1"; else zero_val="0";
		
	// 	var data="&action=preCostRpt"+
	// 	'&txt_po_breack_down_id='+"'"+po_id+"'"+
	// 	'&txt_job_no='+"'"+job_no+"'"+
	// 	'&cbo_company_name='+"'"+company_id+"'"+
	// 	'&txt_style_ref='+"'"+style_ref+"'"+
	// 	'&txt_costing_date='+"'"+costing_date+"'"+
	// 	'&zero_value='+zero_val+
	// 	'&cbo_buyer_name='+"'"+buyer_id+"'";
	// 	http.open("POST","../../../order/woven_order/requires/pre_cost_entry_controller_v2.php",true);
	// 	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	// 	http.send(data);
	// 	http.onreadystatechange = fnc_generate_report_reponse;
	// }
	
	// function fnc_generate_report_reponse()
	// {
	// 	if(http.readyState == 4) 
	// 	{
	// 		$('#data_panel').html( http.responseText );
			
	// 		var w = window.open("Surprise", "#");
	// 		var d = w.document.open();
	// 		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
	// 		d.close();
	// 	}
	// }		
	function openmypage(type)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/shipment_track_report_controller.php?action=job_no_popup&type='+type+'&data='+data,'Job No Popup', 'width=650px,height=420px,center=1,resize=0','../../')
		
		emailwindow.onclose=function()
		{
			//alert(type);
			var theemail=this.contentDoc.getElementById("job_no_id");
			var theemailv=this.contentDoc.getElementById("job_no_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				if(type==1)
				{
					document.getElementById("txt_job_id").value=theemail.value;
			   		document.getElementById("txt_job_no").value=theemailv.value;
				}
				else if(type==2)
				{
					document.getElementById("txt_job_id").value=theemail.value;
			   		document.getElementById("txt_style_no").value=theemailv.value;
				}
				else if(type==3)
				{
					document.getElementById("txt_int_ref").value=theemailv.value;
				}
				release_freezing();
			}
		}
	}
	
</script>

</head>
<body onLoad="set_hotkey();">
<!-- id= cost_breakdown_rpt -->
<form id="shipment_track_report">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../"); ?>
         <h3 align="left" id="accordion_h1" style="width:930px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:930px;">
                <table class="rpt_table" width="920" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                   
                            <th width="140" class="must_entry_caption">LC Company</th>
                            <th width="140">LC Location</th>
                            <th width="140">Working Company</th>
                            <th width="140">WC Location</th>
                            <th width="120">Buyer</th>
                            <th width="60">Job No.</th>
                            <th width="80">Style</th>
                            <th width="80">Int.Ref</th>
                            <th width="120" colspan="2" class="must_entry_caption">Public Ship Date</th>
                            <th><input type="reset" id="reset" class="formbutton" style="width:60px" value="Reset" onclick="reset_form('','report_container*report_container2','txt_job_id','','disable_enable_fields(\'txt_job_no\',0)')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td id="company_td"><? echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/shipment_track_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' ); load_drop_down('requires/shipment_track_report_controller', this.value, 'load_drop_down_location', 'location_td' );" ); ?></td>
                            <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location_id", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "","" );
                            ?>
                            </td>
                            <td>
                                <?
                                    echo create_drop_down( "cbo_work_company_name", 140, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "Select Company", $selected, "load_drop_down('requires/shipment_track_report_controller', this.value, 'load_drop_down_work_location', 'work_location_td' );" ); 
                                 ?>
                            </td>
                            <td id="work_location_td">
                            <? 
                                echo create_drop_down( "cbo_work_location_id", 140, $blank_array,"", 1, "-- Select --", $selected, "",1,"" );
                            ?>
                            </td>
                            <td id="buyer_td"> <? echo create_drop_down( "cbo_buyer_name", 120,"select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "","" ); ?></td>
                            
                            <td><input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:60px"  placeholder="Write/Browse" onDblClick="openmypage(1);"  /></td> 
                            <td><input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:80px" placeholder="Write/Browse" onDblClick="openmypage(2);" />
                            <input type="hidden" id="txt_job_id" value="" /></td>
                            <td><input type="text" name="txt_int_ref" id="txt_int_ref" class="text_boxes" style="width:80px" placeholder="Write/Browse" onDblClick="openmypage(3);" />
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                            <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                            <td><input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated()" /></td>
                        </tr>
                    </tbody>
                    <tfoot>
                    	<tr>
                            <td colspan="11" align="center">
                                <? echo load_month_buttons(1); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div style="display:none" id="data_panel"></div>  
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	set_multiselect('cbo_company_name','0','0','','0','fn_on_change()');
	set_multiselect('cbo_buyer_name','0','0','','0');
    set_multiselect('cbo_location_id','0','0','','0');
    set_multiselect('cbo_work_company_name','0','0','','0','load_work_location()');
    set_multiselect('cbo_work_location_id','0','0','','0');

    function fn_on_change()
	{
		var cbo_company_name=$("#cbo_company_name").val();
		load_drop_down( 'requires/shipment_track_report_controller', cbo_company_name, 'load_drop_down_buyer', 'buyer_td' );
        load_drop_down( 'requires/shipment_track_report_controller', cbo_company_name, 'load_drop_down_location', 'location_td' );
        set_multiselect('cbo_buyer_name','0','0','','0');
        set_multiselect('cbo_location_id','0','0','','0');
        //print_button_setting();
		
	
	}

    function load_work_location()
	{
		var company=$("#cbo_work_company_name").val();
		load_drop_down( 'requires/shipment_track_report_controller', company, 'load_drop_down_work_location', 'work_location_td' );
        set_multiselect('cbo_work_location_id','0','0','','0');
        //print_button_setting(); 
		
	
	}
</script>

</html>