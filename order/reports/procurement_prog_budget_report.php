<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Style Wise Budget Cost Report.
Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	01-11-2017
Updated by 		:	Mohammad Shafiqur Rahman
Update date		:	09/10/2018 
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
echo load_html_head_contents("Style Wise Budget Cost Report", "../../", 1, 1,$unicode,1,1);
?>
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission = '<? echo $permission; ?>';

	var tableFilters =
	{
		col_40: "none",
		col_operation: {
		id: ["value_tot_wo_qnty","value_tot_wo_value","value_tot_ontime_receive_qnty","value_tot_receive_qnty","value_tot_rcv_balance"],
		col: [16,18,21,23,24],
		operation: ["sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function openmypage_wo()
	{
		if(form_validation('cbo_company_id*cbo_category_id','Company Name*Item Category')==false)
		{
			return;
		}
		else
		{
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_year_id").val()+"_"+$("#cbo_category_id").val()+"_"+$("#cbo_wo_type").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/procurement_prog_budget_report_controller.php?data='+data+'&action=wo_no_popup', 'Wo No Search', 'width=660px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_wo_id");
				var theemailval=this.contentDoc.getElementById("txt_wo_no");
				if ( theemailval.value!="" )
				{
					freeze_window(5);
					$("#hidd_wo_id").val(theemailid.value);
					$("#txt_wo_no").val(theemailval.value);
					release_freezing();
				}
			}
		}
	}

	function fn_report_generated(operation)
	{
		if(form_validation('cbo_company_id','Company Name')==false){ return; }

		var job=$("#txt_job_no").val();
		var style=$("#txt_style_ref").val();
		var search_id=$("#hidd_search_id").val();
		var cbo_search_type=$("#cbo_search_type").val();

		if(job=="" && style=="" && cbo_search_type==0)
		{
			if(form_validation('txt_date_from*txt_date_to','From Date*To Date')==false){  return; }
		}

		var report_title=$( "div.form_caption" ).html();
		if(operation==0)
		{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_job_year_id*txt_job_no*hidd_job_id*txt_style_ref*cbo_search_type*txt_search_no*hidd_search_id*txt_date_from*txt_date_to*hidd_job_no',"../../")+'&report_title='+report_title;	
		}
		if(operation==1)
		{
			var data="action=report_generate1"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_job_year_id*txt_job_no*hidd_job_id*txt_style_ref*cbo_search_type*txt_search_no*hidd_search_id*txt_date_from*txt_date_to*hidd_job_no',"../../")+'&report_title='+report_title;
		}		
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/procurement_prog_budget_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;

	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			//var tot_rows=reponse[2];
			//$('#report_container2').html(reponse[0]);
			//$('#report_container2').html();
			//alert("response_received " + reponse[2]);
			document.getElementById('report_container2').innerHTML=reponse[0];
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
			//var cat_id=$("#cbo_category_id").val();
			setFilterGrid("table_body",-1);
			//,tableFilters
			/*if(cat_id==2)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			else if(cat_id==4)
			{
				setFilterGrid("table_body",-1,tableFilters1);
			}*/
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
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$("#table_body tr:first").show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
	}

	function openmypage_inhouse(book_id,po_id,item_name,item_description,mst_id,popup_type,action)
	{
		var popup_width='670px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/procurement_prog_budget_report_controller.php?book_id='+book_id+'&po_id='+po_id+'&item_name='+item_name+'&item_description='+item_description+'&mst_id='+mst_id+'&popup_type='+popup_type+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}

	function search_by(val,type)
	{
		if(type==1)
		{
			$('#txt_search_no').removeAttr('disabled','disabled');
			$('#txt_search_no').val("");
			$('#hidd_search_id').val("");
			$('#hidd_job_no').val("");
			if(val==4)
			{
				$('#th_type').html('Accessories');
				$('#txt_search_no').attr('onDblClick','openmypage_search_no()');
				$('#txt_search_no').attr('placeholder','Browse');
				$('#txt_search_no').attr('readonly',true);
			}
			else if(val==2 || val==3)
			{
				$('#th_type').html('Fabric Booking [Purchase]');
				$('#txt_search_no').attr('onDblClick','openmypage_search_no()');
				$('#txt_search_no').attr('placeholder','Browse');
				$('#txt_search_no').attr('readonly',true);
			}
			else if(val==12 || val==24 || val==25)
			{
				$('#th_type').html('Service Work order');
				$('#txt_search_no').attr('onDblClick','openmypage_search_no()');
				$('#txt_search_no').attr('placeholder','Browse');
				$('#txt_search_no').attr('readonly',true);
			}
			else if(val==1)
			{
				$('#th_type').html('PI No');
				$('#txt_search_no').removeAttr('onDblClick');
				$('#txt_search_no').attr('placeholder','Write');
				$('#txt_search_no').attr('readonly',false);
			}
			else if(val==8)
			{
				$('#th_type').html('PI System ID');
				$('#txt_search_no').removeAttr('onDblClick');
				$('#txt_search_no').attr('placeholder','Write');
				$('#txt_search_no').attr('readonly',false);
			}
			else
			{
				$('#th_type').html('WO No.');
				$('#txt_search_no').attr('disabled','disabled');
				$('#txt_search_no').attr('onDblClick','openmypage_search_no()');
				$('#txt_search_no').attr('placeholder','Browse');
				$('#txt_search_no').attr('readonly',true);
			}
		}
	}

	function show_popup_report_details(action,datas,width)
	{
		 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/procurement_prog_budget_report_controller.php?action='+action+'&datas='+datas, 'Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../');

	}

	function openmypage_search_no()
	{
		if(form_validation('cbo_company_id*cbo_search_type','Company Name*Search Type')==false)
		{
			return;
		}
		else
		{
			var data = $("#cbo_company_id").val()+"_"+$("#cbo_buyer_id").val()+"_"+$("#cbo_job_year_id").val()+"_"+$("#cbo_search_type").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/procurement_prog_budget_report_controller.php?data='+data+'&action=wo_job_no_popup', 'Wo No Search', 'width=660px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_wo_id");
				var theemailval=this.contentDoc.getElementById("txt_wo_no");
				var theemailjob=this.contentDoc.getElementById("txt_job_no");
				//var response=theemailid.value.split('_');
				if ( theemailval.value!="" )
				{
					//alert (response[0]);
					freeze_window(5);
					$("#hidd_search_id").val(theemailid.value);
					$("#txt_search_no").val(theemailval.value);
					$("#hidd_job_no").val(theemailjob.value);
					release_freezing();
				}
			}
		}
	}

	function generate_report(company,job_no,buyer_name,style_ref_no,costing_date,po_id,costing_per,type)
	{
		
		var zero_val='';
		var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
		if (r==true)
		{
			zero_val="1";
		}
		else
		{
			zero_val="0";
		} 
		var rate_amt=2;
		var data="action="+type
			+"&rate_amt=2"+
			+"&zero_value="+zero_val
			+"&txt_job_no='"+job_no
			+"'&cbo_company_name="+company
			+"&cbo_buyer_name="+buyer_name
			+"&txt_style_ref='"+style_ref_no
			+"'&txt_costing_date='"+costing_date
			+"'&txt_po_breack_down_id="+po_id
			+"&cbo_costing_per="+costing_per
		;
		http.open("POST","../sweater/requires/pre_cost_entry_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_generate_report_reponse;
	}
	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4) 
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><body>'+http.responseText+'</body</html>');
			d.close();
		}
	}

</script>
<style>
	.rotate {
	  /* FF3.5+ */
	  -moz-transform: rotate(-90.0deg);
	  /*transform: rotate(-90.0deg);*/
	  text-align: center;
	  white-space: nowrap;
	  vertical-align: middle;
	  width: 20px;
	  margin-top:12px;
	}
</style>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>
    <form name="wofbreport_1" id="wofbreport_1" autocomplete="off" >
    <h3 style="width:1000px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:1000px;">
            <table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="130">Buyer</th>
                    <th width="60">Job Year</th>
                    <th width="80">Job No</th>
                    <th width="80">Style Ref.</th>
                    <th width="110">Search Type</th>
                    <th width="100" id="th_type">WO No.</th>
                    <th width="130" class="must_entry_caption" colspan="2">Ship Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('wofbreport_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down('requires/procurement_prog_budget_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                        <td id="buyer_td">
							<? echo create_drop_down( "cbo_buyer_id", 130, $blank_array,"", 1, "--Select Buyer--", $selected, "",1,"" ); ?>
                        </td>
                        <td><? $selected_year=date("Y");
                        	echo create_drop_down( "cbo_job_year_id", 60, $year,"", 1, "--ALL--", $selected_year, "",0,"","" );
                        ?></td>
                        <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px" placeholder="Write" />
                            <input type="hidden" id="hidd_job_id" name="hidd_job_id" style="width:50px" />
                        </td>
                        <td><input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" placeholder="Write" /></td>
                        <td><?
							//$search_type=array(1=>"Accessories",2=>"Fabric Booking [Purchase]",3=>"Service Work order",4=>"PI No",5=>"PI System ID");

                        	$search_type=array(1=>"PI No",2=>"Knit Finish Fabrics",3=>"Woven Fabrics",4=>"Accessories",12=> "Services - Fabric",24=>"Services - Yarn Dyeing ",25=> "Services - Embellishment",8=>"PI System ID");

							echo create_drop_down( "cbo_search_type", 110, $search_type,"", 1, "--All--", $selected, "search_by(this.value,1)",0,"","" );
                        ?></td>
                        <td>
                            <input type="text" name="txt_search_no" id="txt_search_no" class="text_boxes" style="width:70px" placeholder="Browse" onDblClick="openmypage_search_no();" disabled />
                            <input type="hidden" id="hidd_search_id" name="hidd_search_id" style="width:50px" />
                            <input type="hidden" id="hidd_job_no" name="hidd_job_no" style="width:50px" />
                        </td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" ></td>
                        <td align="left">
                        	<input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(0)" />
                        	<input type="button" id="show_button" class="formbutton" style="width:60px" value="Sweater" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="10" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
        	</fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </form>
</div>
   <div style="display:none" id="data_panel"></div>
</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
