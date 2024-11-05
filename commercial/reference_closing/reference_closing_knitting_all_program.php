
<?
/****************************************************************
|	Purpose			:	This form is Reference Closing
|	Functionality	:
|	JS Functions	:
|	Created by		:	Md.Didarul Alam
|	Creation date 	:	01/09/2021
|	Updated by 		:	
|	Update date		:   
|	QC Performed BY	:
|	QC Date			:
|	Comments		:
******************************************************************/

	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Reference Closing", "../../", 1, 1,'','','');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
		var permission='<? echo $permission; ?>';

	function fnc_ref_closing(operation)
	{
		var only_full=$('#with_full_shipment').is(':checked');
		var ref_type=$('#cbo_ref_type').val();
		
		var unclose_id=$('#unclose_id').val()*1;

		if (form_validation('cbo_company_name*txt_ref_cls_date*cbo_ref_type','Company*Closing Date*Reference Type')== false)
		{
			return;
		}

		var total_id=$('#total_id').val();



		if(total_id=="")
		{
			alert("Please Select Reference");return;
		}

		if(ref_type==2)
		{
			/*if(only_full==false || only_full==true)
			{
				var data="action=save_update_delete&operation="+operation+"&only_full="+only_full+get_submitted_data_string('cbo_company_name*txt_ref_cls_date*cbo_ref_type*total_id*update_id',"../../")+'&unclose_id='+unclose_id;
				
				freeze_window(operation);
				http.open("POST","requires/reference_closing_knitting_all_program_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_reference_response;
			}*/

			var cbo_company_name = $("#cbo_company_name").val();
			var txt_ref_cls_date = $("#txt_ref_cls_date").val();
			var cbo_ref_type = $("#cbo_ref_type").val();
			var total_id = $("#total_id").val();
			var update_id = $("#update_id").val();
			var unclose_id = $("#unclose_id").val();			

			var data = {
				'action': 'save_update_delete',
				'operation':operation,
				'only_full':only_full,
				'cbo_company_name':cbo_company_name,
				'txt_ref_cls_date':txt_ref_cls_date,
				'cbo_ref_type':cbo_ref_type,
				'total_id':total_id,
				'update_id':update_id,
				'unclose_id':unclose_id
			};

			if(only_full==false || only_full==true)
			{
			
				$.post("requires/reference_closing_knitting_all_program_controller.php", data, function(reponse)
				{
					var reponse=reponse.split('**');
					show_msg(trim(reponse[0]));
					
					resultofdetails(reponse[1]);
					
					release_freezing();
				});

			}
		}
	}

	/*function fnc_reference_response()
	{
		if(http.readyState == 4)
		{
			//release_freezing();return;
			//alert (http.responseText); return ;
			var reponse=http.responseText.split('**');
			show_msg(trim(reponse[0]));
			//reset_form('refclosingform_1','','');
			resultofdetails(reponse[1]);
			//set_button_status(0, permission, 'fnc_ref_closing',1);
			release_freezing();
		}
	}*/

	function resultofdetails(type)
	{
		var type=$("#cbo_ref_type").val();
		var only_full=$('#with_full_shipment').is(':checked');
		var check_only_full=$('#with_full_shipment').is(':checked');
		//alert(only_full);

		if(form_validation('cbo_company_name*cbo_ref_type','Company Name*Item Category')==false)
		{
			return;
		}
		else
		{
			if(type==2)
			{
				var data="action=show_details_knit_closing&type="+type+"&only_full="+Number(only_full)+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to',"../../")+'&check_only_full='+check_only_full;
			}
			// alert(data);return
			freeze_window(3);
			http.open("POST","requires/reference_closing_knitting_all_program_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_result_response;
		}
	}

	function fnc_result_response()
	{
		if(http.readyState == 4)
		{
			//alert (http.responseText); return ;
			//var reponse=http.responseText.split('**');
			//show_msg(trim(reponse[0]));
			$("#responsecontainer").html(http.responseText);
			release_freezing();

			$('#exl_rpt_link').attr('href',document.getElementById('txt_excl_link').value);
		}
	}
	function disabled_fn()
	{
		
		$("#with_full_shipment").attr("disabled",true);
	}

	function fnc_type(type)
	{
		//alert(type);
		if(type==105)
		{
		document.getElementById('th_dynamic').innerHTML='L/C Date';
		}
		else if(type==4)
		{
		document.getElementById('th_dynamic').innerHTML='Receive Date';
		}
		else if(type==106)
		{
		document.getElementById('th_dynamic').innerHTML='LC Date';
		}
		else if(type==2)
		{
		document.getElementById('th_dynamic').innerHTML='Production Date';
		}
		else if(type==370)
		{
		document.getElementById('th_dynamic').innerHTML='Pub ShipDate';
		}
		else if(type==104)
		{
		document.getElementById('th_dynamic').innerHTML='PI Date';
		}
		else if(type==163)
		{
		document.getElementById('th_dynamic').innerHTML='Orgi. Shipdate Date';
		}
		else if(type==107)
		{
		document.getElementById('th_dynamic').innerHTML='Contract Date Date';
		}
		else if(type==69)
		{
		document.getElementById('th_dynamic').innerHTML='Requisition Date';
		}
		else if(type==117)
		{
		document.getElementById('th_dynamic').innerHTML='Requisition Date';
		}
		else if(type==70)
		{
		document.getElementById('th_dynamic').innerHTML='Requisition Date';
		}
		else if(type==108)
		{
		document.getElementById('th_dynamic').innerHTML='Booking Date';
		}
		else if(type==94)
		{
		document.getElementById('th_dynamic').innerHTML='WO Date';
		}
		else {
			document.getElementById('th_dynamic').innerHTML='Date Range';
		}
	}

</script>
</head>
<body onLoad="set_hotkey()">
<div align="center">
<? echo load_freeze_divs ("../../",$permission);  ?>
<form  name="refclosingform_1" id="refclosingform_1" autocomplete="off">
<fieldset style="width:740px;">
<legend>Reference Closing</legend>
        <table class="rpt_table" cellspacing="0" cellpadding="0" width="740" border="1" rules="all">

   <!-- <table width="100%" align="center">-->
	     <thead>
         	 <th class="must_entry_caption">Company </th>
             <th class="must_entry_caption">Closing Date </th>
             <th class="must_entry_caption">Reference Type </th>
             <th id="th_dynamic">Date Range </th>
             <!-- value="1" onClick="disabled_fn();"-->
             <th><input type="checkbox" name="with_full_shipment" id="with_full_shipment" >&nbsp; Reference Closed<tbody>
        <tr>
            <td align="center" width="100">
              	<?
            	echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "" );
				?>
            </td>
             <td align="center" width="80">
            <input type="text" name="txt_ref_cls_date" id="txt_ref_cls_date" class="datepicker"  maxlength="50" title="Maximum 50 Character" style="width:50px;" value="<? echo date("d-m-Y"); ?>"/>
            </td>
            <td align="center" width="100">
            <?
            echo create_drop_down( "cbo_ref_type", 160, $entry_form,"", 1, "-- Select Ref.Type --", $selected,"fnc_type(this.value);","","2" );
            ?>
            </td>
            <td align="center" width="180">
             <input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:60px;" placeholder="From Date" readonly /> To
             <input type="text" name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:60px;" placeholder="To Date" readonly />
            </td>
            <td align="center">
            	 <input type="button" name="search" id="search" value="Show" onClick="resultofdetails();" style="width:70px" class="formbutton" />
            </td>
        </tr>
		 <tr>
	        <td colspan="12" align="center">
				<? echo load_month_buttons(1); ?>
	        </td>
	    </tr>
        <tr>
            <input type="hidden" id="update_id" name="update_id"/>
        </tr>
        <tr>
            <td colspan="7" align="center" height="30" valign="bottom">
               <div id="report_container" align="center" style="margin-top:10px; margin-bottom:10px">
                    <input style="display:none;" type="button" id="reprt_html" onClick="view_html_report_lp()" class="formbutton" value="HTML Preview">&nbsp;&nbsp;
                        <a id="exl_rpt_link"><input type="button" id="reprt_excl" class="formbutton" value="Download Excel"></a>
                    </a>
                </div>   
            </td>
        </tr>
        <tr>
            <td  align="center" colspan="9"  class="button_container">
            <input id="save1" class="formbutton" type="button" style="width:80px" onClick="fnc_ref_closing(0)" name="save" value="Close">
            <?
            //echo load_submit_buttons( $permission, "fnc_ref_closing", 0,0 ,"reset_form('refclosingform_1','','')",1);
            ?>
            </td>
        </tr>
      </tbody>

    </table>
     <div id="report_container2"> </div>
</fieldset>
<fieldset style="width:90%;" id="responsecontainer"></fieldset>
</form>
</div>
</body>
<script>
function view_html_report_lp()
{
    $('#table_body tbody tr:first').hide();
    //return;
    var response = document.getElementById('report_container2').innerHTML;
    var w = window.open("Surprise", "#");
    var d = w.document.open();
    $('#table_body tbody tr:first').show();
    d.write(response);
    d.close();
}
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
