<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Buyer Claims Entry
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	27-06-2019
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
echo load_html_head_contents("Buyer Claims Entry", "../../", 1, 1,$unicode,'','');

?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	
	var permission='<? echo $permission; ?>';

	function openmypage_po(page_link,title)
	{
		var garments_nature=document.getElementById('garments_nature').value;
	    page_link=page_link+'&garments_nature='+garments_nature;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("po_id") //Access form field with id="emailfield"
			if (theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/buyer_claims_entry_controller" );
				release_freezing();
			} 
		}
	}
	
	function fnc_buyer_claim_entry( operation )
	{
		if(operation==2)
		{
			alert("Delete Restricted.")
			return;
		}
		if ( form_validation('txt_order_no*txt_claimentry_date*cbo_inspected_by*txt_inspected_comp*txt_responsible_dept*txt_claim_validated','Order No*Claim Entry Date*Inspected By*Base on Ex-Fac. Val.*Responsible Dept* Claim validated by')==false )
		{
			return;
		}
		else
		{
			var dataString 	='';
			var tot_row=$('#tbl_claim tbody tr').length;
			//alert(tot_row);
			var k=0;
			for (var i=1; i<=tot_row; i++)
			{
				if(i==13) i=99;
				var is_check=$('#chkRemark_'+i).val()*1;
				if(is_check==1)
				{
					k++;
					dataString+="&chkRemark_" + k + "='" + $('#chkRemark_'+i).val()+"'"+"&txtremarks_" + k + "='" + $('#txtremarks_'+i).val()+"'"+"&hiddnclaimid_" + k + "='" + $('#hiddnclaimid_'+i).val()+"'"+"&txtDtlsUpId_" + k + "='" + $('#txtDtlsUpId_'+i).val()+"'";
				}
				if(i==99) i=13;
			}
			if(k==0)
			{
				alert("All Nature OF Claims is Blank!!!!");
				return;
			}
			
			var data="action=save_update_delete&operation="+operation+'&tot_row='+k+get_submitted_data_string('order_id*txt_update_id*txt_claimentry_date*txt_claimentry_per*txt_base_exfactory_val*txt_air_freight*txt_sea_freight*txt_discount*cbo_inspected_by*txt_inspected_comp*txt_comments*txt_responsible_dept*txt_claim_validated',"../../")+dataString;
			
			//alert(data); release_freezing(); return;
			
			freeze_window(operation);
			http.open("POST","requires/buyer_claims_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_on_submit_reponse;
		}
	}
	
	function fnc_on_submit_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				show_msg(trim(reponse[0]));
				
				document.getElementById('txt_update_id').value = reponse[1];
				get_php_form_data( reponse[2], "populate_data_from_search_popup", "requires/buyer_claims_entry_controller" );
				set_button_status(1, permission, 'fnc_buyer_claim_entry',1,0);	
				release_freezing();
			}
			release_freezing();
		}
	}
	
	function fnc_checkbox(inc)
	{
		if(document.getElementById('chkRemark_'+inc).checked==true)
		{
			document.getElementById('chkRemark_'+inc).value=1;
			$('#txtremarks_'+inc).removeAttr('disabled','disabled');
			$('#txtremarks_'+inc).val('');
		}
		else if(document.getElementById('chkRemark_'+inc).checked==false)
		{
			document.getElementById('chkRemark_'+inc).value=2;
			$('#txtremarks_'+inc).attr('disabled','disabled');
			$('#txtremarks_'+inc).val('');
		}
	}
	
	function fnc_camount_calculation()
	{
		var exfacvalue=$('#txt_exfactory_val').val()*1;
		var calim_per=$('#txt_claimentry_per').val()*1;
		
		var calim_amount=(exfacvalue*(calim_per/100));
		
		if(calim_amount=='') calim_amount=0;
		$('#txt_base_exfactory_val').val( number_format(calim_amount,2,'.','' ));
	}
</script>
</head>

<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
        <fieldset style="width:970px;">
            <legend>Buyer Claims Entry</legend>
            <form name="claimsentry_1" id="claimsentry_1" autocomplete="off">
                <table width="970" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td colspan="4" class="must_entry_caption" align="right">Order No</td>
                        <td colspan="4">
                            <input style="width:120px;" type="text" title="Double Click to Search" onDblClick="openmypage_po('requires/buyer_claims_entry_controller.php?action=order_popup','Job/Order Selection Form')" class="text_boxes" placeholder="Browse" name="txt_order_no" id="txt_order_no" readonly />
                            <input type="hidden" id="order_id" name="order_id" readonly />
                            <input type="hidden" id="txt_update_id" name="txt_update_id" />
                        </td>
                    </tr>
                    <tr>
                        <td width="100">Company Name</td>
                        <td width="140"><? echo create_drop_down( "cbo_company_name", 130, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "",1); ?></td>
                        <td width="100">Location Name</td>
                        <td width="140" id="location"><? echo create_drop_down( "cbo_location_name", 130, "select id, location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select--", $selected, "",1 );	?></td>
                        <td width="100">Job No</td>              
                        <td width="140">
                            <input style="width:120px;" type="text" class="text_boxes" name="txt_job_no" id="txt_job_no" disabled />
                            <input type="hidden" name="hidd_job_id" id="hidd_job_id" style="width:30px;" class="text_boxes" />
                        </td>
                        <td width="100">Buyer Name</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ,1); ?>	  
                        </td>
                    </tr>
                    <tr>
                        <td>Style Ref.</td>
                        <td><input class="text_boxes" type="text" style="width:120px" disabled name="txt_style_ref" id="txt_style_ref"/></td>
                        <td>Style Description</td>
                        <td><input class="text_boxes" type="text" style="width:120px;" disabled name="txt_style_description" id="txt_style_description"/></td>
                        <td>Shipment Date</td>
                        <td><input class="datepicker" type="text" style="width:120px;" name="txt_ship_date" id="txt_ship_date" disabled/></td>
                        <td>Ex-Factory Date</td>
                        <td><input class="datepicker" type="text" style="width:120px;" name="txt_exfactory_date" id="txt_exfactory_date" disabled/></td>
                    </tr>
                    <tr>
                        <td>Po Qty</td>
                        <td><input class="text_boxes_numeric" type="text" style="width:68px;" name="txt_po_qnty" id="txt_po_qnty" disabled/>
                            <? echo create_drop_down( "cbo_order_uom",50, $unit_of_measurement, "",0, "", 1, "","1","1,58" ); ?>
                        </td>
                        <td>Po Value</td>
                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_po_value" id="txt_po_value" disabled/></td>
                        <td>Plan Cut Qty</td>
                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_plan_cut_qnty" id="txt_plan_cut_qnty" disabled/></td>
                        <td>Ex-Factory Qty</td>
                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_exfactory_qty" id="txt_exfactory_qty" disabled/></td>
                    </tr>
                    <tr>
                    	<td>Ex-Factory Value</td>
                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_exfactory_val" id="txt_exfactory_val" disabled/></td>
                        <td>Team Leader</td>   
                        <td><? echo create_drop_down( "cbo_team_leader", 130, "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "--Select Team--", $selected, "",1 ); ?></td>
                        <td>Dealing Merchant</td>   
                        <td><? echo create_drop_down( "cbo_dealing_merchant", 130, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "",1 ); ?></td>
                        <td class="must_entry_caption">Claim Entry Date</td>
                        <td><input class="datepicker" type="text" style="width:120px;" name="txt_claimentry_date" id="txt_claimentry_date" /></td>
                     </tr> 
                     <tr>
                    	<td class="must_entry_caption">Claim %</td>
                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_claimentry_per" id="txt_claimentry_per" onBlur="fnc_camount_calculation();" /></td>
                        <td class="must_entry_caption" title="Base on Ex-Fac.">Claim Amount</td>   
                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_base_exfactory_val" id="txt_base_exfactory_val" readonly /></td>
                        <td>Air Freight</td>   
                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_air_freight" id="txt_air_freight"  /></td>
                        <td>Sea Freight</td>   
                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_sea_freight" id="txt_sea_freight"  /></td>
                     </tr>
                     <tr>
                     	<td>Discount</td>   
                        <td><input class="text_boxes_numeric" type="text" style="width:120px;" name="txt_discount" id="txt_discount" /></td>
                        <td class="must_entry_caption">Inspected By</td>   
                        <td><? echo create_drop_down( "cbo_inspected_by", 130, $buyer_claim_inspected_by,"", 1, "-Inspected By-", $selected, "","" ); ?></td>
                        <td class="must_entry_caption">Inspected Company</td>
                        <td><input class="text_boxes" type="text" style="width:120px;" name="txt_inspected_comp" id="txt_inspected_comp" /></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                     </tr> 
                     <tr>
                     	<td>Comments</td>
                        <td colspan="7"><input class="text_boxes" type="text" style="width:820px;" name="txt_comments" id="txt_comments" /></td>
                     </tr> 
                </table>
                <br>
                <table width="800" cellspacing="2" cellpadding="0" class="rpt_table" border="1" rules="all" id="tbl_claim">
                	<thead>
                    	<th width="30">SL</th>
                        <th width="250">NATURE OF CLAIMS</th>
                        <th width="60">Check</th>
                        <th>Remarks</th>
                    </thead>
                    <tbody>
					<?
                        $k=0;
                        foreach($nature_of_buyer_claim as $claimid=>$claimname)
                        {
                            $k++;
                            if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr id="tr_<? echo $k; ?>" bgcolor="<? echo $bgcolor; ?>">
                                <td width="30" align="center"><? echo $k; ?></td>
                                <td width="250"><? echo $claimname; ?></td>
                                <td width="60" align="center"><input type="checkbox" name="chkRemark[]<? echo $claimid; ?>" id="chkRemark_<? echo $claimid; ?>" onClick="fnc_checkbox(<? echo $claimid; ?>);" value="2" ></td>
                                <td>
                                	<input type="text" name="txtremarks[]<? echo $claimid; ?>" id="txtremarks_<? echo $claimid; ?>" class="text_boxes" style="width:450px;" value="" placeholder="Write" disabled />
                                    <input type="hidden" name="hiddnclaimid[]<? echo $claimid; ?>" id="hiddnclaimid_<? echo $claimid; ?>" style="width:30px" class="text_boxes" value="<? echo $claimid; ?>" />
                                    <input type="hidden" name="txtDtlsUpId[]<? echo $claimid; ?>" id="txtDtlsUpId_<? echo $claimid; ?>" style="width:30px" class="text_boxes" value="" />
                                </td>
                            </tr>
                            <?
                        }
                    ?>
                    </tbody>
                 </table>
                 <br>
                 <table width="800" cellspacing="2" cellpadding="0" class="rpt_table" border="1" rules="all">
                	<thead>
                    	<th colspan="4">ROOTS OF CLAIMS</th>
                    </thead>
                    <tbody>
                    	<tr>
                        	<td width="200" class="must_entry_caption" align="right">Responsible Dept :</td>
                            <td width="200"><input type="text" name="txt_responsible_dept" id="txt_responsible_dept" class="text_boxes" style="width:180px;" value="" placeholder="Write" /></td>
                            <td width="200" class="must_entry_caption" align="right">Claim validated by:</td>
                            <td><input type="text" name="txt_claim_validated" id="txt_claim_validated" class="text_boxes" style="width:180px;" value="" placeholder="Write" /></td>
                        </tr>
                    </tbody>
                     <tr>
                        <td align="center" colspan="4" valign="middle" class="button_container">
                        	<? echo load_submit_buttons( $permission, "fnc_buyer_claim_entry", 0,1,"",1); ?>
                        </td>
                    </tr> 
				</table>
            </form>
        </fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>