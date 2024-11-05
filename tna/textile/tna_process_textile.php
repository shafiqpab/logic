<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("TNA Process","../../", 1, 1, $unicode,'','');
?>

<script>
if($('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';	
function fnc_tna_process( operation )
{
	
	if ( form_validation('cbo_company','Company Name')==false )
	{
		return;
	}
	else
	{
		var data="action=tna_process&cbo_company="+$('#cbo_company').val()+"&cbo_buyer="+$('#cbo_buyer').val()+"&txt_booking_no="+$('#txt_booking_no').val()+"&txt_booking_no_id="+$('#txt_booking_no_id').val()+"&is_delete="+$("#cbx_delete_process").val()+"&is_manual_process=1";
		freeze_window(operation);
		// alert(data)
		http.open("POST","requires/tna_process_textile_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_tna_process_reponse; 
	}
}

function fnc_tna_process_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		//if (reponse[0].length>2) reponse[0]=10;
		release_freezing();
		//if( trim( reponse[2])!="" && trim( reponse[2])!=undefined )
		if( trim( reponse[0])==10)
		{
			$('#missing_po').html("Process Failed for following PO Number-"+reponse[2]);
		}
		else
		{
			$('#missing_po').html("Process is completed successfully.");
		}
	}
}

function open_popup()
{
	if( form_validation('cbo_company','Company Name')==false )
	{
		return;
	}
	
	var company= $("#cbo_company").val();	
	var buyer= $("#cbo_buyer").val();
	var page_link='requires/tna_process_textile_controller.php?action=search_po_number&company='+company+'&buyer='+buyer+"&is_manual_process=1"; 
	var title="Search PO Number/Style Reff No";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=955px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var selected_id=this.contentDoc.getElementById("txt_selected_id").value;
		var selected_name=this.contentDoc.getElementById("txt_selected_name").value;
 		if (selected_id!="")
		{
			freeze_window(5);
			$('#txt_booking_no').val(selected_name);
			$('#txt_booking_no_id').val(selected_id);
			release_freezing();
		}
   	}
}
$(window).unload(function() {
    alert('confirm close');
})


</script>
</head>
<body onLoad="set_hotkey()">
<div align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
	<fieldset style="width:600px;">
		<legend>Textile TNA Process</legend>
		<form name="tnaprocess_1" id="tnaprocess_1">	
			<table cellpadding="0"  cellspacing="2" width="100%">
			 	<tr>
					<td width="120" align="right" class="must_entry_caption"><strong>Company Name</strong></td>
					<td colspan="1">
					   <?
                           echo create_drop_down( "cbo_company", 170,"select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select company --", 0, "load_drop_down( 'requires/tna_process_textile_controller', this.value, 'load_drop_down_buyer', 'buyer_td')" );
                       ?>                     
					</td>
                    <td width="80" align="right"><strong>Buyer Name</strong></td>
					<td colspan="1" id="buyer_td">
						 <?
                            echo create_drop_down( "cbo_buyer", 170,"select a.id,a.buyer_name from  lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and a.status_active=1 and a.is_deleted=0 group by a.id,a.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
                        ?> 					
					</td>              		
               </tr>
			   <tr>
                <tr>
					<td width="140" align="right"><strong>Booking/Sales Order</strong></td>
					<td colspan="3">
				       <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" placeholder="Browse" style="width:428px" onDblClick="open_popup()"/> 
                       <input type="hidden" name="txt_booking_no_id" id="txt_booking_no_id" />                           
					</td>                                		
                </tr>
                     <td width="40" align="right">
                     	<script>
							function setv()
							{
								if($('#cbx_delete_process').val()==0)
									$('#cbx_delete_process').val(1);
								else
									$('#cbx_delete_process').val(0);
							}
						</script>
                     	<input type="checkbox" id="cbx_delete_process" value="0" onChange="setv()">
                    </td>
					<td colspan="3">Delete Old Process Data [<span class="must_entry_caption"> Note: Re-process for Actual date if delete old data.</span>]</td>                                		
                </tr>
				<tr>
				  <td colspan="4" align="center" class="button_container">
						<input type="button" name="process" class="formbutton" style="width:150px;" onClick="fnc_tna_process(7)" value="Start Process">
			     </td>				
				</tr>
				<tr>
			  		<td colspan="4" id="missing_po"></td>
			  	</tr>
			</table>
		</form>	
	</fieldset>
		
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
