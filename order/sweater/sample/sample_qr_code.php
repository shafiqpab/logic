<?
/*-------------------------------------------- Comments
Purpose			: PCS wise sticker Generation
Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	15-02-2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/ 
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("PCS wise sticker Generation","../../../", 1, 1, $unicode,'','');
?>
<script>
	var permission='<? echo $permission; ?>';
			
	function openmypage_req_no(id)
	{
		var cbo_company_id = $('#cbo_company_name').val();
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var title = 'Search Lot Ratio No';	
		var page_link = 'requires/sample_qr_code_controller.php?company_id='+cbo_company_id+'&action=requisition_id_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1300px,height=400px,center=1,resize=0,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var req_no=(this.contentDoc.getElementById("selected_job").value).split('_');
			$('#hidden_req_id').val(req_no[0]);
			document.getElementById('txt_req_no').value=req_no[1];
			release_freezing();
		}
	}
	
	function fnc_cut_lay_info( operation )
	{		
		if(form_validation('cbo_company_name*txt_req_no','Company Name*Lot Ratio No')==false)
	  	{
			return;
		}
		
        var data="action=generate_bundle"+get_submitted_data_string('cbo_company_name*cbo_location_name*txt_req_no*hidden_req_id*txt_date_from*txt_date_to',"../../../");    
	    
		freeze_window(operation);
		http.open("POST","requires/sample_qr_code_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_cut_lay_info_reponse;
	}


	function fnc_cut_lay_info_reponse()
	{
		if(http.readyState == 4) 
		{
			// alert(http.responseText);
			var reponse=trim(http.responseText).split('####');
			$('#report_container2').html(reponse[0]);
			release_freezing();
		}
	} 

	function check_all_report()
	{
		$("input[name=chk_bundle]").each(function(index, element) { 
				
			if( $('#check_all').prop('checked')==true) 
			{
				$(this).attr('checked','true');
			}
			else
			{
				$(this).removeAttr('checked');
			}
		});
	}		

	function fnc_bundle_report_qr_code(type)
	{
		var data="";
		var error=1;
		var bundle_id = new Array();
		var barcode = new Array();
		$("input[name=chk_bundle]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{					
				error=0;
				var idd=$(this).attr('id').split("_");
				// if(data=="") data=$('#hiddenid_'+idd[2] ).val(); else data=data+","+$('#hiddenid_'+idd[2] ).val();
				var barcode_sl = $('#hiddenid_'+idd[2] ).data('sl');
				bundle_id.push($('#hiddenid_'+idd[2] ).val()+'_'+barcode_sl);
				// barcode.push($('#hiddenid_'+idd[2] ).data('bcode'));
			}
		});
		var unique_ids = [...new Set(bundle_id)];
		// var barcode_ids = [...new Set(barcode)];
		// alert(barcode_ids);
	
		if( error==1 )
		{
			alert('No data selected');
			return;
		}
		// alert(unique_ids);release_freezing();return;
		freeze_window(1);
		if(type==1)
		{
			var	action ="print_qrcode_operation";
		}
		else
		{
			var	action ="print_qrcode_sticker";
		}
		http.open( 'POST', 'requires/sample_qr_code_controller.php?action='+action+'&data='+ unique_ids );

		http.onreadystatechange = response_pdf_data;
		http.send(null);		
	}

	function response_pdf_data() 
	{
		if(http.readyState == 4) 
		{
			release_freezing();
			var response = http.responseText.split('###');
			// alert(response[0]);
			window.open('requires/'+response[1], '', '');
		}
	}

	function select_chield()
	{
		// alert('ok');
		$('.parent').change(function() 
		{
		    $(this).nextUntil('.parent').prop('checked', $(this).prop('checked')); 
		});
	}

	$(document).ready(function() {
		
	  $(".chield_bndl").on("click",function() {
	  	alert('ok');
	      $parent = $(this).prevAll(".parent_bndl");
	      if ($(this).is(":checked")) $parent.prop("checked",true);
	      else {
	         var len = $(this).parent().find(".chield_bndl:checked").length;
	         $parent.prop("checked",len>0);
	      }    
	  });
	  $(".parent_bndl").on("click",function() {
	  	alert('ok');
	      $(this).parent().find(".chield_bndl").prop("checked",this.checked);
	      console.log('checked');
	  });
	});
	

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>
    <form name="cutandlayentry_1" id="cutandlayentry_1">
    
         <h3 style="width:750px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel">      
         <fieldset style="width:750px;margin: 0 auto;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                   	<th class="must_entry_caption">Company</th>
                   	<th>Location</th>
                    <th class="must_entry_caption">Sample Req. No</th>
                    <th colspan="2" id="cap_cut_date">Req. Date</th>
                    <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('cuttingLayProductionReport_1','','','','')" class="formbutton" style="width:50px" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                      
                       
                        <td>
							<? echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sample_qr_code_controller',this.value, 'load_drop_down_location', 'lc_location_td' );" ); 
							
							?> 
                        </td>
                        <td id="lc_location_td">
                        <? 
	                       echo create_drop_down( "cbo_location_name", 130, "select id,location_name from lib_location comp where status_active=1 and is_deleted=0  order by location_name","id,location_name", 1, "-- All  --", $selected, "" );
	                     ?>
                        </td>
                        <td> 
                            <input type="text" id="txt_req_no" name="txt_req_no" class="text_boxes" style="width:100px" onDblClick="openmypage_req_no();" placeholder="Browse" readonly="true" />
                            <input type="hidden" name="hidden_req_id"  id="hidden_req_id"  />
                        </td>
                        
                        <td>
                        	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px"  placeholder="From Date" readonly>
                        </td>
                        <td>
                        	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" readonly>
                        </td>
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:70px;" value="Show" onClick="fnc_cut_lay_info(1)" />
                        </td>
                    </tr>
                    <tr>
                	<td colspan="6"><? echo load_month_buttons(1); ?></td>
               		</tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
         <div style="display:none" id="data_panel"></div>   
    	<div id="report_container" align="center"></div>
    	<div id="report_container2" align="left"></div>
   
    </form>
	</div>
</body>
           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>