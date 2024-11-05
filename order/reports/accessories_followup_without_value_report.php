<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Accessories Followup Report[Without Value].
Functionality	:	
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor 
Creation date 	: 	20-05-2022
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
echo load_html_head_contents("Accessories Followup Report[Without Value]", "../../", 1, 1,$unicode,'1','');
?>	

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	
	function fn_report_generated(action)
	{
		
		if(action=='item_report_generate')
		{
			if(form_validation('cbo_item_group','Item Group')==false)
			{
				$("#cbo_item_group").focus();
				return;
			}
		}
		if(form_validation('cbo_company_name','Company Name')==false)//*txt_date_from*txt_date_to*From Date*To Date
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action="+action+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_item_group*cbo_date_type*txt_date_from*txt_date_to*cbo_year*txt_job_no*txt_style_ref*txt_order_no*cbo_search_by*cbo_year_selection*txt_internal_ref*txt_file_no*txt_order_no_id*txt_style_id*cbo_season_id*cbo_team_name*cbo_team_member*cbo_shipping_status',"../../")+"&report_title="+report_title;
			//alert(data);
			freeze_window(3);
			http.open("POST","requires/accessories_followup_without_value_report_controller.php",true);
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
			var tot_rows=reponse[2];
			var search_by=document.getElementById('cbo_search_by').value;
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+tot_rows+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			if (reponse[3]==2)
			{
				if(tot_rows*1>1){
					if(search_by==1){
					
						 var tableFilters = {
							col_operation: {
							   id: ["value_pre_costing","value_wo_qty","value_wo_balance","value_in_amount","value_rec_qty","value_issue_amount","value_leftover_amount"],
							   col: [20,24,25,30,31,33,35],
							   operation: ["sum","sum","sum","sum","sum","sum","sum"],
							   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
							}	
						 }
						 //alert(tableFilters);
						
					}
					if(search_by==2){
						 var tableFilters = {
							col_operation: {
							   id: ["value_pre_costing","value_wo_qty","value_wo_balance","value_in_amount","value_rec_qty","value_issue_amount","value_leftover_amount"],
							   col: [19,23,24,29,30,32,34],
							   operation: ["sum","sum","sum","sum","sum","sum","sum"],
							   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
							}	
						 }
					}
					setFilterGrid("table_body",-1,tableFilters);
				}
			}
			//setFilterGrid("table_body",-1,tableFilters);
			//setFilterGrid("table_body_style",-1);
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

	function new_window(html_filter_print)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		//if(html_filter_print*1>1) $("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		
		if(html_filter_print*1>1) $("#table_body tr:first").show();
	}	
	
	function print_report_button_setting________________off(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==50)
			{
				$("#report_btn_1").show();	 
			}
			if(report_id[k]==51)
			{
				$("#report_btn_2").show();	 
			}
			if(report_id[k]==52)
			{
				$("#report_btn_3").show();	 
			}
			if(report_id[k]==63)
			{
				$("#report_btn_4").show();	 
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
			+"&rate_amt="+rate_amt
			+"&zero_value="+zero_val
			+"&txt_job_no='"+job_no
			+"'&cbo_company_name="+company
			+"&cbo_buyer_name="+buyer_name
			+"&txt_style_ref='"+style_ref_no
			+"'&txt_costing_date='"+costing_date
			+"'&txt_po_breack_down_id="+po_id
			+"&cbo_costing_per="+costing_per
		;

		http.open("POST","../woven_order/requires/pre_cost_entry_controller_v2.php",true);
	
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
	
	function openmypage(po_id,item_name,job_no,book_num,trim_dtla_id,action)
	{ //alert(book_num);
		var cbo_company_name=$("#cbo_company_name").val();
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_without_value_report_controller.php?po_id='+po_id+'&item_name='+item_name+'&job_no='+job_no+'&book_num='+book_num+'&trim_dtla_id='+trim_dtla_id+'&action='+action+'&cbo_company_name='+cbo_company_name, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	function openmypage_inhouse(po_id,item_name,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_without_value_report_controller.php?po_id='+po_id+'&item_name='+item_name+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	
	function openmypage_issue(po_id,item_name,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_without_value_report_controller.php?po_id='+po_id+'&item_name='+item_name+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	
	function order_qty_popup(company,job_no,po_id,buyer,from_date,to_date,action)
	{
		//alert(action);
		var popup_width='800px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_without_value_report_controller.php?company='+company+'&job_no='+job_no+'&po_id='+po_id+'&buyer='+buyer+'&from_date='+from_date+'&to_date='+to_date+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	
	function order_req_qty_popup(company,job_no,po_id,buyer,rate,item_group,boook_no,description,country_id,trim_dtla_id,start_date,end_date,action)
	{
		//alert(country_id);
		var popup_width='800px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_without_value_report_controller.php?company='+company+'&job_no='+job_no+'&po_id='+po_id+'&buyer='+buyer+'&rate='+rate+'&item_group='+item_group+'&boook_no='+boook_no+'&description='+description+'&country_id_string='+country_id+'&trim_dtla_id='+trim_dtla_id+'&start_date='+start_date+'&end_date='+end_date+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../');
	}
	
	function search_populate(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Shipment Date";
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="Precost Date";
		}
	}
	
	
	function print_report_button_setting(report_ids) 
    {
     
      // alert(report_ids);
	    $('#show_button').hide();
        $('#show_button1').hide();
		$('#show_button2').hide();
		$('#show_item').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==108){$('#show_button').show();}
            else if(items==257){$('#show_button1').show();}
			else if(items==444){$('#show_button2').show();}
		  	else if(items==243){$('#show_item').show();}			 
            });
    }
	




function openmypage_style(type_id)
	{		
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
			var data = $("#cbo_company_name").val()+"_"+$("#cbo_buyer_name").val()+"_"+type_id;
			//$("#cbo_company_name").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_without_value_report_controller.php?data='+data+'&action=style_popup', 'style Search', 'width=480px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				if (theemailid.value!="" || theemailval.value!="")
				{
					//alert (theemailid.value);
					freeze_window(5);
					if(type_id==1)
					{
					$("#txt_style_id").val(theemailid.value);
					$("#txt_job_no").val(theemailval.value);
					}
					else
					{
						$("#txt_style_id").val(theemailid.value);
						$("#txt_style_ref").val(theemailval.value);
					}
					release_freezing();
				}
			}
	}	

	function openmypage_order()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
	//alert(1);
		
		var data = $("#cbo_company_name").val()+"_"+$("#cbo_buyer_name").val()+"_"+$("#txt_style_ref").val()+"_"+$("#cbo_year_selection").val();
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/accessories_followup_without_value_report_controller.php?data='+data+'&action=order_no_popup', 'Order No Search', 'width=450px,height=420px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theemailid=this.contentDoc.getElementById("txt_po_id");
				var theemailval=this.contentDoc.getElementById("txt_po_val");
				if (theemailid.value!="" || theemailval.value!="")
				{
					//alert (theemailid.value);
					freeze_window(5);
					$("#txt_order_no_id").val(theemailid.value);
					$("#txt_order_no").val(theemailval.value);
					release_freezing();
				}
			}
	}	
	

</script>

</head>

<body onLoad="set_hotkey();">
    <form id="accessoriesFollowup_report">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <h3 align="left" id="accordion_h1" style="width:1650px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1650px;">
                <table class="rpt_table" width="1650" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                    <thead>
                        <tr>                    
                            <th width="130" class="must_entry_caption">Company Name</th>
                            <th width="130">Buyer Name</th>
							<th>Team</th>
                  			<th>Team Member</th>
                            <th width="130">Season</th>
                            <th width="100">Type</th>
                            <th width="50">Job Year</th>
                            <th width="70">Job No</th>
                            <th width="80">Style Ref.</th>
                            <th width="80">Internal Ref.</th>
                            <th width="80">File No</th>
                            <th width="80">Order No</th>
                            <th width="130">Item Group</th>
							<th width="100">Shipment Status</th>
                            <th width="80">Date Type</th>
                            <th width="130" colspan="2" id="date_td">Country Shipment Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:80px" value="Reset" /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td><? echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/accessories_followup_without_value_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                            <td id="buyer_td"> <? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
							<td><?
							
							echo 	create_drop_down( "cbo_team_name", 120, "select id,team_leader_name from lib_marketing_team where   status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/accessories_followup_without_value_report_controller', this.value, 'load_drop_down_team_member', 'team_td' );" );
							
							?>
                         </td>
                         <td id="team_td">
							 <? 
                                echo create_drop_down( "cbo_team_member", 150, $blank_array,"", 1, "-Select Team Member- ", $selected, "" );
                             ?>	
                         </td>
							<td id="season_td"><? echo create_drop_down( "cbo_season_id", 130, $blank_array,'', 1, "-Select Season-",$selected, "",1,"" ); ?></td>
                            <td><? $search_by_arr1 = array(1=>"Order Wise",2=>"Style Wise");
                            	echo create_drop_down( "cbo_search_by", 100, $search_by_arr1,"",0, "", 2,'',0 );//search_by(this.value)?></td>
                            <td><? echo create_drop_down( "cbo_year", 50, create_year_array(),"", 1,"-All-", "", "",0,"" ); ?></td>
                            <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:60px" placeholder="Wr./Browse"  onDblClick="openmypage_style(1)" ></td>
                            <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:70px" placeholder="Wr./Browse " onDblClick="openmypage_style(2)" >
                            <input type="hidden" name="txt_style_id" id="txt_style_id" style="width:90px;"/>
                            </td>
                            <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                            <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                            <td><input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:70px" placeholder="Wr./Browse" onDblClick="openmypage_order()" onChange="$('#txt_order_no_id').val('');" >
                             <input type="hidden" name="txt_order_no_id" id="txt_order_no_id" style="width:90px;"/>
                            </td>
                            <td><? echo create_drop_down( "cbo_item_group", 130, "select item_name,id from lib_item_group where is_deleted=0 and status_active=1 order by item_name","id,item_name", 0, "", $selected, "" ); ?></td>
							<td><? $search_by_ship_status= array(2=>"Partial Delivery",3=>"Full Delivery/closed");
							echo create_drop_down( "cbo_shipping_status", 110, $shipment_status,"", 1, "-- Select --", "", "",0,'2,3','','','','' );?></td>
                            <td><? $search_by_date = array(1=>"Pub. Ship Date",2=>"Country Shipment Date");
                            	echo create_drop_down( "cbo_date_type", 100, $search_by_date,"",0, "", 2,'',0 );//search_by(this.value)?></td>
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                            <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                            <td>
                                <input type="button" id="show_button" class="formbutton" style="width:80px;" value="Show" onClick="fn_report_generated('report_generate')" />
                                
                            </td>
                        </tr>
                        <tr>
                            <td colspan="15" align="center"><? echo load_month_buttons(1); ?></td>                            
							
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" ></div>
    </form>    

</body>
<script>
	set_multiselect('cbo_item_group','0','0','0','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
