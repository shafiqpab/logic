<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Multi Challan Wise Bundle Issue to Embellishment

Functionality	:
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	27-02-2022
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Multi Challan Wise Bundle Issue to Embellishment","../", 1, 1, $unicode,1);
?>
<script>
var permission='<? echo $permission; ?>';
var tableFilters = {}
var tableFilters2 = {}
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(type)
	{
		var job_no = $("#txt_job_no").val();
		var style_ref_no = $("#txt_style_ref_no").val();
		var order_no = $("#txt_order_no").val();

		if(job_no !="" || style_ref_no !="" || order_no !="")
		{
			if( form_validation('cbo_company_id','Company')==false )
			{
				return;
			}
		}
		else
		{
			if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company*Date from*Date To')==false )
			{
				return;
			}

		}


		var report_title=$( "div.form_caption" ).html();

		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_party_id*cbo_emb_type*txt_order_no*txt_date_from*txt_date_to*hiden_order_id',"../")+'&report_title='+report_title+'&type='+type;

		// alert(data); return;

		freeze_window(3);
		http.open("POST","requires/multi_challan_wise_bundle_issue_to_embellishment_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4)
			{
				//alert (http.responseText);
				var reponse=trim(http.responseText).split("####");
				$("#report_container2").html(reponse[0]);

				release_freezing();
				//document.getElementById('factory_efficiency').innerHTML=document.getElementById('total_factory_effi').innerHTML;
				//document.getElementById('factory_parfomance').innerHTML=document.getElementById('total_factory_per').innerHTML;
				//alert(reponse[1]);
				document.getElementById('report_container').innerHTML='<input type="button" onclick="fnc_prinr_report()" value="Print" name="Print" class="formbutton" style="width:100px"/> <input type="button" onclick="fnc_print_report()" value="Print 2" name="Print 2" class="formbutton" style="width:100px"/>';
				
				if(type==1)
				{
					setFilterGrid("html_search",-1,tableFilters);
				}
				else
				{
					//  setFilterGrid("table_body",-1,tableFilters2);
				}
				setFilterGrid("table_body_id",-1);
				show_msg('3');
				release_freezing();
			}
		}
	}

	function openmypage_order_no()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var lc_company = $("#cbo_company_id").val();
		var buyer = $("#cbo_buyer_id").val();
		var page_link='requires/multi_challan_wise_bundle_issue_to_embellishment_controller.php?action=order_no_popup&lc_company='+lc_company+'&buyer='+buyer;
		var title="Order No Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("txt_selected_po").value; // product ID
			var style_no=this.contentDoc.getElementById("txt_selected_style").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_des_no);
			// $("#txt_order").val(style_des);
			var order_id_arr = po_id.split(',');
			var unique_ord_id_arr = Array.from(new Set(order_id_arr));
			var orderIds = unique_ord_id_arr.join(',');

			var style_no_arr = style_no.split(',');
			var unique_style_arr = Array.from(new Set(style_no_arr));
			var styleNo = unique_style_arr.join(',');

			$("#hiden_order_id").val(orderIds);
			$("#txt_order_no").val(styleNo);
		}
	}

	function fnc_checkbox_check_party(rowNo)
	{
		var isChecked=$('#tbl_'+rowNo).is(":checked");
		var party=$('#party_'+rowNo).val();
		// alert(party);
		if(isChecked==true)
		{
			var tot_row=$('#tbl_search tr').length;
			for(var i=1; i<=tot_row; i++)
			{
				if(i!=rowNo)
				{
					try
					{
						if ($('#tbl_'+i).is(":checked"))
						{
							var partyCurrent=$('#party_'+i).val();

							if(party!=partyCurrent)
							{
								alert("Party mix not allow! Please select same party.");
								$('#tbl_'+rowNo).attr('checked',false);
								return;
							}
						}
					}
					catch(e)
					{
						//got error no operation
					}
				}
			}
		}
	}

	function fnc_prinr_report()
	{
		var data="";
		var error=1;
		var challan_id_arr = new Array();
		var party_id_arr = new Array();
		$("input[name=checkbox_chk]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				error=0;
				var idd=$(this).attr('id').split("_");
				challan_id_arr.push($('#mstidall_'+idd[1] ).val());
				party_id_arr.push($('#party_'+idd[1] ).val());
			}
		});
		var challan_ids = [...new Set(challan_id_arr)];
		var party_ids = [...new Set(party_id_arr)];
		// alert(barcode_ids);

		if( error==1 )
		{
			alert('No data selected');
			return;
		}
		// alert(unique_ids);
		var company_id = $("#cbo_company_id").val();
		window.open('requires/multi_challan_wise_bundle_issue_to_embellishment_controller.php?action=print_report&challan_ids='+challan_ids+'&party_ids='+party_ids+'&company_id='+company_id, '', '');
	}

	// For Print Two Button
	function fnc_print_report()
	{
		var data="";
		var error=1;
		var challan_id_arr = new Array();
		var party_id_arr = new Array();
		$("input[name=checkbox_chk]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{
				error=0;
				var idd=$(this).attr('id').split("_");
				challan_id_arr.push($('#mstidall_'+idd[1] ).val());
				party_id_arr.push($('#party_'+idd[1] ).val());
			}
		});
		var challan_ids = [...new Set(challan_id_arr)];
		var party_ids = [...new Set(party_id_arr)];
		// alert(barcode_ids);

		if( error==1 )
		{
			alert('No data selected');
			return;
		}
		// alert(unique_ids);
		var company_id = $("#cbo_company_id").val();
		window.open('requires/multi_challan_wise_bundle_issue_to_embellishment_controller.php?action=print_report_one&challan_ids='+challan_ids+'&party_ids='+party_ids+'&company_id='+company_id, '', '');
	}

</script>
<script src="../ext_resource/hschart/hschart.js"></script>
</head>
<body onLoad="set_hotkey();">

<form id="StyleandLineWiseProductionReport_1">
    <div style="width:100%;" align="center">

        <? echo load_freeze_divs ("../",'');  ?>

         <h3 style="width:900px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel" >
         <fieldset style="width:900px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="160" class="must_entry_caption">Company Name</th>
                    <th width="120" class="">Buyer</th>
                    <th width="100">Order No.</th>
                    <th width="120" class="">Party Name</th>
                    <th width="120" class="">Embellishment Type</th>
                    <th width=""  id="search_by_th_up" class="must_entry_caption">Date Range</th>
                    <th width="70">
                    	<input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('StyleandLineWiseProductionReport_1','report_container','','','')" />
                    </th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td id="cbo_lc_company_td">
							<?
                                echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/multi_challan_wise_bundle_issue_to_embellishment_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/multi_challan_wise_bundle_issue_to_embellishment_controller', this.value, 'load_drop_down_party', 'party_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
							<?
                                echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-- All --", 0, "" );
                            ?>
                        </td>
                        <td>
                         <input type="text" id="txt_order_no"  name="txt_order_no"  style="width:100px" class="text_boxes"  placeholder="Write or Browse" onDblClick="openmypage_order_no()"/>
                         <input type="hidden" name="hiden_order_id" id="hiden_order_id">
                        </td>
                        <td id="party_td">
							<?
                                echo create_drop_down( "cbo_party_id", 120, $blank_array,"", 1, "-- All --", 0, "" );
                            ?>
                        </td>
                        <td>
							<?
								$emb_type_arr = array(1=>'Print',2=>'Embroidery');
                                echo create_drop_down( "cbo_emb_type", 120, $emb_type_arr,"", 1, "-- All --", 0, "" );
                            ?>
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" >
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"  placeholder="To Date"  >
                        </td>
                        <td>
                            <input type="button" name="search1" id="search1" value="Show" onClick="generate_report(1)" style="width:60px" class="formbutton" />
                        </td>

                    </tr>
                    <tr>
                        <td colspan="7" align="center" width="100%"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>

    <div id="report_container" align="center" style="padding: 10px 0;"></div>

    <div id="report_container2" align="left">
    	<div style="float:left; " id="report_container3"></div>
    </div>
 </form>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
