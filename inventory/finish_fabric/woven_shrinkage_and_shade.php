<?
/*-------------------------------------------- Comments -----------------------
Version (MySql)          :  V2
Version (Oracle)         :  V2
Converted by             :  Md. Saidul Islam Reza
Converted Date           :  14-07-2021
Purpose			         : 	This page Will Create Shrinkage and Shade Entry.
Functionality	         :
JS Functions	         :
Created by		         :	
Creation date 	         : 	
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         :  This version  is oracle Compatible
-------------------------------------------------------------------------------*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

echo load_html_head_contents("Shrinkage and Shade Entry","../../", 1, 1, $unicode,1,'');
?>


<script>
	var permission = '<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	
	function fnc_shrinkage_shade_entry(operation){
		
		if (form_validation('cbo_company_id*txt_job_no*txt_consigment','Company Name*Job No*Consigment')==false)
		{
			release_freezing();
			return;
		}
		else{
			var statusShrinkageFoundRecv=$("#statusShrinkageFoundRecv").val();
			if(statusShrinkageFoundRecv==1)
			{
				alert("Update Restricted. Found This Shrinkage in Receive");
				return;
			}

			var dataArr=Array();	var txtActualWgtValidation=0;
			var totalRows=$('#mst_dtls_part tr').length;
			for(var i=0;i<totalRows;i++){
				var dtlsData='dtlsID_'+i+'*cclNo_'+i+'*intellocutRollNo_'+i+'*lengthYDS_'+i+'*width_'+i+'*shade_'+i+'*beforeWashLengthCM_'+i+'*beforeWashWidthCM_'+i+'*afterWashLengthCM_'+i+'*afterWashWidthCM_'+i+'*beforeWashGSM_'+i+'*afterWashGSM_'+i+'*barcodeNo_'+i+'*grnDtlsId_'+i+'*bookingId_'+i+'*bookingNo_'+i+'*batchId_'+i;
				dataArr.push(dtlsData); 
				var txtActualWgt=$("#afterWashGSM_"+i).val();
				if((txtActualWgt=="" || txtActualWgt==0))
				{
					txtActualWgtValidation +=1;
				}
				
			}
			var dtlsData=dataArr.join('*');

			if(txtActualWgtValidation >0)
			{
				alert("Please give After wash GSM/actual weight");
				return;
			}
			var data="action=save_update_delete&total_rows="+totalRows+"&operation="+operation+get_submitted_data_string('txt_sys_no*txt_mst_id*cbo_company_id*cbo_location_id*txt_grn_no*txt_job_no*cbo_buyer_id*cbo_brand_id*cbo_season_name*cbo_season_year*txt_consigment*txt_remarks*'+dtlsData,"../");

			http.open("POST","requires/woven_shrinkage_and_shade_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_shrinkage_shade_entry_response;
			
			
		}

	}
 	
	function fnc_shrinkage_shade_entry_response(){
		if(http.readyState == 4) 
		{	  	
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
			
			if(reponse[0]==0 || reponse[0]==1)
			{
				
				//$("#txt_mst_id").val(reponse[1]);
				//$("#txt_sys_no").val(reponse[2]);
				//$("#pattern_summery").attr("disabled",false)
				set_button_status(1, permission, 'fnc_shrinkage_shade_entry',1);
			
				var totalLenth=$('#mst_dtls_part tr').length;
				for(var i=1; i<totalLenth;i++){
					deleteBreakDownTr(1);
				}
 				get_php_form_data(reponse[1], "populate_sys_data", "requires/woven_shrinkage_and_shade_controller");			
			}
			else if(reponse[0]==10)
			{
			
			}

			release_freezing();
			return;
		}
	
	}
	function fn_open_sys_popup(){
		
		if (form_validation('cbo_company_id','Company Name')==false)
		{
			release_freezing();
			return;
		}
		
		var cbo_company_id=$("#cbo_company_id").val();
		var page_link = 'requires/woven_shrinkage_and_shade_controller.php?cbo_company_id='+cbo_company_id+'&action=sys_data_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Sys Dtls', 'width=1150px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose = function ()
		{
			
			//$("#pattern_summery").attr("disabled",false)
			var totalLenth=$('#mst_dtls_part tr').length;
			for(var i=1; i<totalLenth;i++){
				deleteBreakDownTr(1);
			}

			var theform=this.contentDoc.forms[0];
			var dataString=this.contentDoc.getElementById("selected_data").value;
			var dataString=dataString.split("_");
			//var job_id=this.contentDoc.getElementById("selected_data").value;
			var shrinkage_id=dataString[0];
			var statusShrinkageFoundRecv=dataString[1];
			$("#statusShrinkageFoundRecv").val(statusShrinkageFoundRecv);
			//get_php_form_data(shrinkage_id, "populate_job_data", "requires/woven_shrinkage_and_shade_controller");
			get_php_form_data(shrinkage_id, "populate_sys_data", "requires/woven_shrinkage_and_shade_controller");
			set_button_status(1, permission, 'fnc_shrinkage_shade_entry',1);
			$('#cbo_location_id').attr("disabled",true);
			$('#cbo_buyer_id').attr("disabled",true);
			$('#cbo_season_name').attr("disabled",true);
			$('#cbo_season_year').attr("disabled",true);
			$('#cbo_brand_id').attr("disabled",true);
		}
		
		//alert(page_link);return;
		
		
	}
	
	function fn_open_job_popup(){
		
		if (form_validation('cbo_company_id','Company Name')==false)
		{
			release_freezing();
			return;
		}
		
		var cbo_company_id=$("#cbo_company_id").val();
		var barcodeNoExisting=$("#barcodeNo_0").val();
		var page_link = 'requires/woven_shrinkage_and_shade_controller.php?cbo_company_id='+cbo_company_id+'&barcodeNoExisting='+barcodeNoExisting+'&action=job_data_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Pattern Dtls', 'width=850px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose = function ()
		{
			var theform=this.contentDoc.forms[0];
			var job_id=this.contentDoc.getElementById("selected_data").value;
			get_php_form_data(job_id, "populate_job_data", "requires/woven_shrinkage_and_shade_controller");
			var grnNO=$("#txt_grn_no").val();
			get_php_form_data(grnNO, "populate_dtls_data", "requires/woven_shrinkage_and_shade_controller");
			var job_idData=job_id.split("_");
			$('#cbo_location_id').val(job_idData[3]);
			$('#cbo_location_id').attr("disabled",true);
			$('#cbo_buyer_id').attr("disabled",true);
			$('#cbo_season_name').attr("disabled",true);
			$('#cbo_season_year').attr("disabled",true);
			$('#cbo_brand_id').attr("disabled",true);
		}
		
		//alert(page_link);return;
		
		
	}
	
	/*function fn_open_pattern_summary_popup(){
		var pattern_data_str=$("#txt_pattern_data_str").val();
		var page_link = 'requires/woven_shrinkage_and_shade_controller.php?pattern_data='+pattern_data_str+'&action=pattern_data_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Pattern Dtls', 'width=320px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose = function ()
		{
			var theform=this.contentDoc.forms[0];
			var pattern_data=this.contentDoc.getElementById("txt_selected_data").value;
			$("#txt_pattern_data_str").val(pattern_data);
		}
		
	}*/

	/*function openmypage_mrr()
    {
        if (form_validation('cbo_company_id','Company')==false )
        {
            return;
        }
        var cbo_company_id = $("#cbo_company_id").val();
        var page_link='requires/woven_shrinkage_and_shade_controller.php?action=mrr_popup_search&cbo_company_id='+cbo_company_id;
        var title='MRR Information Form';
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=400px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
          
        }
    }*/

	function fnc_print()
	{
		if($('#txt_mst_id').val()=="")
		{
			alert("Please Save First.");
			return;
		}
		print_report( $('#cbo_company_id').val()+'**'+$('#txt_mst_id').val(), "print_report", "requires/woven_shrinkage_and_shade_controller" );
		return;
	}

	function addBreakDownTr(i) 
	{
		var i=$('#mst_dtls_part tr').length-1;
		i++;
		$("#mst_dtls_part tr:last").clone().find("input,select").each(function() {
		 // $("#mst_dtls_part tr:eq("+ii+")").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  //'name': function(_, name) {  var name=name.split("_"); return name[0] +"_"+ i },
			  'value': function(_, value) { return value }              
			});  
		  }).end().appendTo("#mst_dtls_part");
		$('#increase_'+i).removeAttr("onClick").attr("onClick","addBreakDownTr("+i+");");
		$('#decrease_'+i).removeAttr("onClick").attr("onClick","deleteBreakDownTr("+i+")");
		$('#dtlsID_'+i).val("");
	}

	function deleteBreakDownTr(rowNo) 
	{
		if(rowNo!=0)
		{
			$("#mst_dtls_part tr:eq("+rowNo+")").remove();
			
			var numRow=$('#mst_dtls_part tr').length;
			for(i = 0;i <= numRow;i++){
				$("#mst_dtls_part tr:eq("+i+")").find("input,select").each(function() {
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  //'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
					  'value': function(_, value) { return value }              
					}); 
					
				$('#increase_'+i).removeAttr("onClick").attr("onClick","addBreakDownTr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","deleteBreakDownTr("+i+")");
				})

			}
		}		
	}

	

	
</script>
 
 
</head>
<!-- <body onLoad="set_hotkey();"> -->
<body onLoad="set_hotkey();">
    <div style="width:100%;">
   	<? echo load_freeze_divs ("../../",$permission);  ?><br />   
    <fieldset style="width:980px;">
        <legend>Shrinkage and Shade Page</legend>
        <form name="shrinkShade_1" id="shrinkShade_1" autocomplete="off">

            <table  width="100%" cellspacing="2" cellpadding=""  border="0">
            	<tr>
                    <td colspan="8" align="center">
                    	System ID : 
                        <input  style="width:150px;" type="text" onDblClick="fn_open_sys_popup()" class="text_boxes"  name="txt_sys_no" id="txt_sys_no" readonly placeholder="Browse" />
                    	<input type="hidden" name="txt_mst_id" id="txt_mst_id" />
                    </td>
            	</tr>
                <tr>
                    <td width="80" class="must_entry_caption" align="right">Company Name</td>
                    <td width="130">
                    	<? echo create_drop_down("cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/woven_shrinkage_and_shade_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/woven_shrinkage_and_shade_controller',this.value, 'load_drop_down_location', 'location_td' );"); ?>
                    </td>
                    <td width="80" align="right" class="must_entry_caption">GRN No</td>
                    <td width="130"><input type="text" id="txt_grn_no" name="txt_grn_no" class="text_boxes" style="width:140px;" onDblClick="fn_open_job_popup()" readonly placeholder="Browse" />
                    	<input type="hidden" id="txt_job_no" name="txt_job_no"/>
                    </td>
                    <td width="80" align="right" class="must_entry_caption">Location</td>
                    <td width="130" id="location_td"><? echo create_drop_down("cbo_location_id", 150, "","", 1, "-- Select Location --", $selected, ""); ?></td>
                    <!-- <td width="80" align="right" class="must_entry_caption">Job No</td> -->
                    
                    <td align="right">Buyer</td>
                    <td  id="buyer_td"><? echo create_drop_down("cbo_buyer_id", 150, "","", 1, "-- All --", $selected, ""); ?></td>
                </tr>
                <tr>
                    <td align="right">Brand</td>
                    <td id="brand_td">
                    	<? echo create_drop_down("cbo_brand_id", 150, "select id,brand_name from LIB_BUYER_BRAND where is_deleted = 0 AND status_active = 1 ORDER BY brand_name ASC","id,brand_name", 1, "-- All --", $selected, ""); ?>
                    </td>
                    <td align="right">Season & Year</td>
                    <td id="td_season_season_year">
						<? echo create_drop_down("cbo_season_name", 87, "","", 1, "-- All --", $selected, ""); ?>
						<? echo create_drop_down("cbo_season_year", 60, $year,"", 1, "-- All --", $selected, ""); ?>
                    </td>
                    <td align="right" >Remarks</td>
                    <td colspan="3" ><input type="text" id="txt_remarks" name="txt_remarks" class="text_boxes" style="width:340px;" /></td>
                    <!-- <td align="right">Merch Style Ref.</td>
                    <td><input type="text" id="txt_style_ref" name="txt_style_ref" class="text_boxes" style="width:140px;" /></td> -->
                   <!--  <td align="right" class="must_entry_caption">Gmts Color</td>
                    <td id="td_gmtd_color"><? //echo create_drop_down("cbo_gmts_color_id", 150, "","", 1, "-- All --", $selected, ""); ?></td> -->
                </tr>
                <tr>
                    <td align="right" class="must_entry_caption">Consigment</td>
                    <td>
						<input type="text" id="txt_consigment" name="txt_consigment" class="text_boxes" style="width:140px;" />
						<!-- &nbsp;MRR&nbsp;<input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:40px" placeholder="Browse" onDblClick="openmypage_mrr()" readonly/> -->
					</td>
                   <!--  <td align="right" class="must_entry_caption">Tolarence%</td>
                    <td><input type="text" id="txt_tolarence_per" name="txt_tolarence_per" class="text_boxes_numeric" style="width:140px;" /></td> -->
                    <!-- <td align="right" >File</td>
                    <td align="center" >
                    	<input type="file" id="dtls_data_from_excel" style="width:150px;" name="dtls_data_from_excel" onChange="file_upload();">
                    </td> -->
                   <!--  <td align="center" colspan="2">
						<input type="button" id="image_button" class="image_uploader" style="width:120px;" value="CLICK TO ADD FILE" onClick="file_uploader( '../../', document.getElementById('txt_mst_id').value,'', 'shrinkage_and_shade_entry',2,1);" />
                    </td> -->
                </tr>
                <tr>
					
                   <!--  <td align="center" colspan="2" >
						<input type="button" disabled id="pattern_summery" value="Pattern Summery" onClick="fn_open_pattern_summary_popup();" style="width:150px" class="formbutton">
						<input type="hidden" id="txt_pattern_data_str" value="">
                    </td> -->
                </tr>
                <tr>
                    <td align="center" valign="middle" class="button_container" colspan="8">
                    	<input type="hidden" name="statusShrinkageFoundRecv[]" id="statusShrinkageFoundRecv">
                        <? echo load_submit_buttons( $permission, "fnc_shrinkage_shade_entry", 0,0 ,"ResetForm()",1,0) ; ?>
                    </td>
                </tr>
                <tr>
                    <td align="center"  colspan="8">
					<input type="button" name="printBtn" id="printBtn" value="Print" onClick="fnc_print()" style="width:100px" class="formbutton" />
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
     
    <fieldset style="width:1100px;">
    	<form name="shrinkShade_2" id="shrinkShade_2" autocomplete="off">
	        <table cellpadding="0" cellspacing="0" border="1" rules="all" width="" class="rpt_table">
	            <thead>
					<tr>
						<th width="30" rowspan="2">SL No</th>
						<th width="100" rowspan="2">Manual Roll No</th>
						<th width="100" rowspan="2">Barcode</th>
						<!-- <th width="60" rowspan="2">Roll No</th> -->
						<th width="60" rowspan="2">Length (YDS)</th>
						<th width="60" rowspan="2">Width</th>
						<th width="60" rowspan="2">Shade</th>
						<th colspan="2">Before Wash</th>
						<th colspan="2">After Wash</th>
						<th colspan="2">Shrinkage %</th>
						<th rowspan="2" width="60">Before wash GSM</th>
						<th rowspan="2" width="60">After wash GSM</th>
						<th rowspan="2" width="60"> GSM Variance %</th>
						<!-- <th rowspan="2" width="100">Action</th> -->
					</tr>
					<tr>
						<th width="60">Length [CM]</th>
						<th width="60">Width [CM]</th>
						<th width="60">Length [CM]</th>
						<th width="60">Width [CM]</th>
						<th width="60">Length</th>
						<th width="60">Width</th>
					</tr>
	            </thead>
	            <tbody id="mst_dtls_part">
	                <tr>
	                    <td>
	                        <input type="hidden" id="dtlsID_0" value="">
	                        <input type="hidden" id="grnDtlsId_0" value="">
	                        <input type="hidden" id="bookingId_0" value="">
	                        <input type="hidden" id="bookingNo_0" value="">
	                        <input type="hidden" id="batchId_0" value="">
	                        <input type="text" id="cclNo_0" value="" class="text_boxes" style="width:30px;" disabled readonly>
	                    </td>
	                    <td><input type="text" id="intellocutRollNo_0" value="" class="text_boxes" disabled readonly style="width:80px;"></td>
	                    <td><input type="text" id="barcodeNo_0" value="" disabled readonly class="text_boxes" style="width:100px;"></td>
	                   <!--  <td><input type="text" id="rollNo_0" value="" class="text_boxes" style="width:60px;"></td> -->
	                    <td><input type="text" id="lengthYDS_0" value="" disabled readonly class="text_boxes" style="width:60px;"></td>
	                    <td><input type="text" id="width_0" value="" class="text_boxes" disabled readonly style="width:60px;"></td>
	                    
	                     <td id="shadeTd_1" width="140" >
							<?
	                        	echo create_drop_down( "shade_0", 60, $fabric_shade,"", 1, "-- Select Shade --", "", "",0,0,"","","","","","" );	
	                        ?>
	                    </td>
	                    
	                   

	                    <td><input type="text" id="beforeWashLengthCM_0" value="" class="text_boxes_numeric" style="width:60px;"></td>
	                    <td><input type="text" id="beforeWashWidthCM_0" value="" class="text_boxes_numeric" style="width:60px;"></td>
	                    <td><input type="text" id="afterWashLengthCM_0" value="" class="text_boxes_numeric" style="width:60px;"></td>
	                    <td><input type="text" id="afterWashWidthCM_0" value="" class="text_boxes_numeric" style="width:60px;"></td>
	                    <td><input type="text" id="shrinkageWashLengthCM_0" value="" class="text_boxes_numeric" style="width:60px;"></td>
	                    <td><input type="text" id="shrinkagerWashWidthCM_0" value="" class="text_boxes_numeric" style="width:60px;"></td>
	                    <td><input type="text" id="beforeWashGSM_0" value="" class="text_boxes_numeric" style="width:60px;" disabled readonly></td>
	                    <td><input type="text" id="afterWashGSM_0" name="afterWashGSM[]" value="" class="text_boxes_numeric" style="width:60px;"></td>
	                    <td><input type="text" id="GSMVariance_0" value="" class="text_boxes_numeric"></td>
	                   <!--  <td align="center">
							<input type="button" id="increase_0" style="width:30px" class="formbutton" value="+" onClick="javascript:addBreakDownTr(0);" />
							<input type="button" id="decrease_0" style="width:30px" class="formbutton" value="-" onClick="javascript:deleteBreakDownTr(0);"  />
	                    </td> -->
	                </tr>
	            </tbody>
	        </table>
    	</form>
    </fieldset>
	</div>     
     
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>











