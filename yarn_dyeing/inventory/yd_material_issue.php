<?

/*--- ----------------------------------------- Comments
Purpose         :   show the content of yarn dyeing material issue 
Functionality   :   
JS Functions    :
Created by      :   Samiur
Creation date   :   10-02-2020
Updated by      :   Sapayth
Update date     :
Oracle Convert  :       
Convert date    :   
QC Performed BY :       
QC Date         :   
Comments        :
*/

session_start(); 
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Dyeing Material Issue", "../../", 1,1, $unicode,1,'');

?>

<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php"; 
    var permission='<? echo $permission; ?>';

    function ResetForm()
    {
        reset_form('yarnissue_1','issue_list_view','','cbouom_1,1', "$('#details_tbl tbody tr:not(:first)').remove(); disable_enable_fields('cbo_company_name*cbo_within_group*cbo_party_name',0)")
    }
    function openmypage_issue_id()
    { 
        var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
        var page_link='requires/yd_material_issue_controller.php?action=issue_popup&data='+data;
        var title="Issue ID";
        
        emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];//("search_subcontract_frm"); //Access the form inside the modal window
            //var theemail=this.contentDoc.getElementById("selected_job");
            var theemail=this.contentDoc.getElementById("selected_job").value;
            /*alert (theemail); */
            var splt_val=theemail.split("_");
            if (splt_val[0]!="")
            {
                //freeze_window(5);
                reset_form('','','txt_issue_no*cbo_company_name*cbo_location_name*cbo_party_name*txt_issue_challan*txt_issue_date*cbo_within_group*txt_job_no*update_id','','');
				
                get_php_form_data( splt_val[0], "load_php_data_to_form", "requires/yd_material_issue_controller" );
                var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1, 'load_php_dtls_form_aftersave', '', 'requires/yd_material_issue_controller');
                if(list_view_orders!='')
                {
                    $("#rec_issue_table tr").remove();
                    $("#rec_issue_table").append(list_view_orders);
                }
                fnc_total_calculate();
                var within_group=document.getElementById('cbo_within_group').value*1;
                // fnc_load_party(within_group);
                set_button_status(1, permission, 'fnc_material_issue', 1);
                //release_freezing();
            }
        }
    }

    function job_search_popup()
    {
        if ( form_validation('cbo_company_name*cbo_within_group','Company Name*Within Group')==false )
        {
            return;
        }
        else
        {
            var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
            var page_link='requires/yd_material_issue_controller.php?action=job_popup&data='+data;
            var title='Order Popup';
            
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../')
            emailwindow.onclose=function()
            {
                //freeze_window(5);
                var theform=this.contentDoc.forms[0];
                var theemail=this.contentDoc.getElementById("selected_order").value;
                $("#txt_job_no").val( theemail );

                get_php_form_data( theemail, "load_php_mst_data_to_form", "requires/yd_material_issue_controller" );
                
                var list_view_orders = return_global_ajax_value( theemail, 'load_php_dtls_form', '', 'requires/yd_material_issue_controller');
                //alert(list_view_orders);
                if(list_view_orders!='')
                {
                    $("#rec_issue_table tr").remove();
                    $("#rec_issue_table").append(list_view_orders);
                }
                fnc_total_calculate();
                fnc_balance_qty_calculate();
                $('#cbo_company_name').attr('disabled','disabled');
                $('#cbo_party_name').attr('disabled','disabled');
                $('#cbo_within_group').attr('disabled','disabled');
                /*release_freezing();*/
            }
        }
    }

    function fnc_material_issue( operation )
    {
        if ( form_validation('cbo_company_name*cbo_party_name*txt_issue_date*txt_job_no', 'Company Name*Party*Issue Date*Job No')==false )
        {
           
           return;
        }
        else
        {
            var total_row=$('#details_tbl tbody tr').length;

            /*var cbo_within_group = document.getElementById('cbo_within_group').value;
            if (cbo_within_group == 1)
            {
                for (var i=1; i<=total_row; i++)
                {
                    if (form_validation('txtbuyerPo_'+i+'*txtstyleRef_'+i,'Buyer PO*Style Ref.')==false)
                    {
                        return;
                    }
                }
            }*/
            
            var data_str="";



            var data_str=get_submitted_data_string('txt_issue_no*cbo_company_name*cbo_location_name*cbo_party_name*txt_issue_challan*txt_receive_quantity*txt_issue_date*cbo_within_group*txt_job_no*update_id*hdn_booking_type_id*hdn_booking_without_order',"../../");
            //alert(data_str);
            var k=0;
            /*alert(total_row);*/
            for (var i=1; i<=total_row; i++)
            {
                var qty=$('#txtissuequantity_'+i).val();
                //if(qty*1>0)
                //{
                    k++;
                    data_str+="&txtstyle_" + k + "='" + $('#txtstyle_'+i).val()+"'"+"&txtsalesorderno_" + k + "='" + $('#txtsalesorderno_'+i).val()+"'&hdnOrderId_" + k + "=" + $('#hdnOrderId_'+i).val()+""+"&txtlot_" + k + "='" + $('#txtlot_'+i).val()+"'"+"&cbouom_" + k + "='" + $('#cbouom_'+i).val()+"'"+"&txtcount_" + k + "='" + $('#txtcount_'+i).val()+"'"+"&txtyarntype_" + k + "='" + $('#txtyarntype_'+i).val()+"'"+"&txtyarncomposition_" + k + "='" + $('#txtyarncomposition_'+i).val()+"'"+"&txtitemcolor_" + k + "='" + $('#txtitemcolor_'+i).val()+"'"+"&txtnoofbag_" + k + "='" + $('#txtnoofbag_'+i).val()+"'"+"&txtconeperbag_" + k + "='" + $('#txtconeperbag_'+i).val()+"'"+"&txtnoofcone_" + k + "='" + $('#txtnoofcone_'+i).val()+"'"+"&txtavgwtpercone_" + k + "='" + $('#txtavgwtpercone_'+i).val()+"'"+"&txtissuequantity_" + k + "='" + $('#txtissuequantity_'+i).val()+"'"+"&ordernoid_" + k + "='" + $('#ordernoid_'+i).val()+"'"+"&txtbuyerPoId_" + k + "='" + $('#txtbuyerPoId_'+i).val()+"'"+"&colorid_" + k + "='" + $('#colorid_'+i).val()+"'"+"&updatedtlsid_" + k + "='" + $('#updatedtlsid_'+i).val()+"'"+"&hidsalesorderid_" + k + "='" + $('#hidsalesorderid_'+i).val()+"'"+"&hidproductid_" + k + "='" + $('#hidproductid_'+i).val()+"'";
                //}
            }

            var data="action=save_update_delete&operation="+operation+'&total_row='+k+data_str;//+'&zero_val='+zero_val
            /*alert (data);return;*/
            freeze_window(operation);
            http.open("POST","requires/yd_material_issue_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_material_issue_response;
        }
    }
    
    function fnc_material_issue_response()
    {
        if(http.readyState == 4) 
        {
            //alert(http.responseText);
            var response=trim(http.responseText).split('**');
            //if (response[0].length>3) reponse[0]=10;  
            
            show_msg(response[0]);
            //$('#cbo_uom').val(12);

            if(response[0]==0 || response[0]==1)
            {
                
               /*alert(response);return;*/
                
                document.getElementById('txt_issue_no').value= response[1];
                document.getElementById('update_id').value = response[2];
                 set_button_status(1, permission, 'fnc_material_issue',1);
                 var list_view_orders = return_global_ajax_value( response[2]+'**'+response[3]+'**'+2+'**'+response[1], 'load_php_dtls_form_aftersave', '', 'requires/yd_material_issue_controller');
                if(list_view_orders!='')
                {
                    $("#rec_issue_table tr").remove();
                    $("#rec_issue_table").append(list_view_orders);
                }
                $('#txt_job_no').attr('disabled','true');
                fnc_total_calculate();
                 //release_freezing();
            }
            if(response[0]==2)
            {
                reset_fnc();
            }
            release_freezing();
        }
    }
    
    function reset_fnc()
    {
        location.reload(); 
    }
    
    function check_iss_qty_ability(value,i)
    {
        var placeholder_value = $("#txtissuequantity_"+i).attr('placeholder')*1;
        var pre_iss_qty = $("#txtissuequantity_"+i).attr('pre_issue_qty')*1;
        var rec_qty = $("#txtissuequantity_"+i).attr('rec_qty')*1;
        //alert(placeholder_value);
		//alert(pre_iss_qty);
		//alert(rec_qty);
        if(((value*1)+pre_iss_qty)>rec_qty)
        {
            alert("Issue qty Excceded by Receive qty");
			$("#txtissuequantity_"+i).val('');
            /*var confirm_value=confirm("Issue qty Excceded by Order qty .Press cancel to proceed otherwise press ok. ");
            if(confirm_value!=0)
            {
                $("#txtissuequantity_"+i).val('');
            } */          
            return;
        }
    }
    
    function location_select()
    {
        if($('#cbo_location_name option').length==2)
        {
            if($('#cbo_location_name option:first').val()==0)
            {
                $('#cbo_location_name').val($('#cbo_location_name option:last').val());
                //eval($('#cbo_location_name').attr('onchange')); 
            }
        }
        else if($('#cbo_location_name option').length==1)
        {
            $('#cbo_location_name').val($('#cbo_location_name option:last').val());
            //eval($('#cbo_location_name').attr('onchange'));
        }   
    }
    
	function fnc_yd_material_issue_print(operation){

        if ( form_validation('cbo_company_name*update_id','Company Name*Update Id')==false )
        {
            return;
        }
        if(operation==1){
            var report_title=$( "div.form_caption" ).html();
            print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_within_group').val(), "yd_material_issue_print", "requires/yd_material_issue_controller") 
            //return;
            show_msg("3");
        }
    }

    function fnc_total_calculate()
    {
        var rowCount = $('#rec_issue_table tr').length;
        var ddd={ dec_type:1, comma:0, currency:0}
        math_operation( "txtTotissueqty", "txtissuequantity_", "+", rowCount,ddd);
    }

    function fnc_balance_qty_calculate() {
        var currentIssueQty = document.getElementById('txtTotissueqty').value;
        console.log(currentIssueQty);
    }
   
</script>

</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <fieldset style="width:900px;">
    <legend style="text-align: left;">Yarn Dyeing Material Issue</legend>
        <form name="yarnissue_1" id="yarnissue_1" autocomplete="off">  
            <table  width="900" cellspacing="2" cellpadding="0"  border="0">
                <tr>
                    <td></td>
                    <td></td>
                    <td  width="130" height="" align="right">Issue ID</td>
                    <td  width="170">
                        <input class="text_boxes"  type="text" name="txt_issue_no" id="txt_issue_no" onDblClick="openmypage_issue_id('xx','Yarn Dyeing Material Issue')"  placeholder="Double Click" style="width:160px;" readonly/> <input type="hidden" name="update_id" id="update_id">
                        <input type="hidden" name="hdn_booking_type_id" id="hdn_booking_type_id">
                        <input type="hidden" name="hdn_booking_without_order" id="hdn_booking_without_order">

                    </td>
                   
                </tr>

                <tr>
                    <td  width="130" align="right" class="must_entry_caption">Company Name </td>
                    <td width="172"> 
                        <? echo create_drop_down( "cbo_company_name", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yd_material_issue_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/yd_material_issue_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
                    </td>
                    <td width="130" align="right">Location Name</td>
                    <td id="location_td">
                         <? echo create_drop_down( "cbo_location_name", 172, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                    </td>
                    
                    <td align="right" class="must_entry_caption">Within Group</td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 172, $yes_no,"", 1, "-- Select --", 0, "load_drop_down( 'requires/yd_material_issue_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?>
                    </td>
                    
                </tr>
                <tr>
                    <td width="130" class="must_entry_caption" align="right">Party</td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, ""); ?></td>

                     <td  width="130" class="must_entry_caption" align="right">Issue Challan</td>
                    <td  width="160">
                        <input class="text_boxes" placeholder="Write" type="text" name="txt_issue_challan" id="txt_issue_challan" style="width:160px;" />  
                    </td>
                    
                    <td  width="130" class="must_entry_caption" align="right">Issue Date</td>
                    <td>
                        <input type="text" name="txt_issue_date" id="txt_issue_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:160px" />             
                    </td>
                    
                </tr>
                <tr>

                    <td width="130" align="right" class="must_entry_caption">Job No</td>
                    <td width="160">
                       <input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="job_search_popup();" placeholder="Double Click" style="width:160px;" readonly/>             
                    </td>

                     <td  width="130"  align="right">Receive Qty</td>
                    <td  width="160">
                        <input class="text_boxes_numeric"  type="text" name="txt_receive_quantity" id="txt_receive_quantity" style="width:160px;" />  
                    </td>

                    <td  width="130"  align="right">Balance Qty</td>
                    <td  width="160">
                        <input class="text_boxes_numeric"  type="text" name="txt_balance_quantity" id="txt_balance_quantity" style="width:160px;" />  
                    </td>
                    
                    
                    
                </tr>
                
            </table>         
            <legend>Material Details Entry</legend>
            <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" width="700" id="details_tbl" rules="all">
                <thead class="form_table_header">
                    <tr align="center" >
                        <th width="80">Style</th>
                        <th width="120">Job No/Sales order no</th>
                        <th width="40">Lot</th>
                        <th width="50">Count</th>
                        <th width="50">Yarn Type</th>
                        <th width="60">Yarn Composition</th>
                        <th width="50">Item Color</th>
                        <th width="40">No of Bag</th>
                        <th width="40">Cone Per Bag</th>
                        <th width="50">No of Cone</th>
                        <th width="70">AVG. Wt. Per Cone</th>
                        <th width="50">Uom</th>
                        <th width="70" class="must_entry_caption">Issue Qty</th>
                    </tr>
                </thead> 
                <tbody id="rec_issue_table">
                <tr>
                	<td>
                		<input type="hidden" name="ordernoid_1" id="ordernoid_1">
                        <input type="hidden" name="jobno_1" id="jobno_1">
                        <input type="hidden" name="colorid_1" id="colorid_1">
                        
                        <input type="hidden" name="txtbuyerPoId_1" id="txtbuyerPoId_1">
                        <input type="hidden" name="updatedtlsid_1" id="updatedtlsid_1">
                        <input type="hidden" name="hidsalesorderid_1" id="hidsalesorderid_1">
                        <input type="hidden" name="hidproductid_1" id="hidproductid_1">

                        
                      <input name="txtstyle_1" id="txtstyle_1" class="text_boxes" type="text"  style="width:80px" value=""readonly/>  
                    </td>

                     
                    <td>
                         <input type="text" id="txtsalesorderno_1" name="txtsalesorderno_1" class="text_boxes" style="width:120px" readonly>
                    </td>
                    <td>
                         <input type="text" id="txtlot_1" name="txtlot_1" class="text_boxes" style="width:40px" readonly>
                    </td>

                    <td>
                         <input type="text" id="txtcount_1" name="txtcount_1" class="text_boxes" style="width:50px" readonly>
                    </td>
                    <td>
                         <input type="text" id="txtyarntype_1" name="txtyarntype_1" class="text_boxes" style="width:50px" readonly>
                    </td>
                    <td>
                        <input name="txtyarncomposition_1" id="txtyarncomposition_1" class="text_boxes" type="text"  style="width:60px" value="" readonly/>
                    </td>
                   <td>
                        <input name="txtitemcolor_1" id="txtitemcolor_1" class="text_boxes" type="text"  style="width:50px" value="" readonly/>
                    </td>
                    <td>
                        <input name="txtnoofbag_1" id="txtnoofbag_1" class="text_boxes_numeric" type="text"  style="width:40px" readonly/>
                    </td>
                     <td>
                        <input name="txtconeperbag_1" id="txtconeperbag_1" class="text_boxes_numeric" type="text"  style="width:40px" readonly/>
                    </td>
                     <td>
                        <input name="txtnoofcone_1" id="txtnoofcone_1" class="text_boxes_numeric" type="text"  style="width:50px" readonly/>
                    </td>
                    <td>
                        <input name="txtavgwtpercone_1" id="txtavgwtpercone_1" class="text_boxes_numeric" type="text"  style="width:70px" readonly/>
                    </td>
                    <td><? echo create_drop_down( "cbouom_1",50, $unit_of_measurement,"", 1, "-Select-",1,"", 1,"" );?></td>
                    <td>
                        <input name="txtissuequantity_1" id="txt_issue_quantity" class="text_boxes_numeric" type="text"  style="width:70px" />
                    </td>
                    
                    
                  
                </tr>
              </tbody>
              <tfoot>
                    <tr class="tbl_bottom" name="tr_btm" id="tr_btm">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>Total:</td>
                        <td><input name="txtTotissueqty" id="txtTotissueqty" class="text_boxes_numeric" type="text" readonly style="width:70px" /></td>
                        
                    </tr>
                </tfoot>  
             </table> 
             <table width="900" cellspacing="2" cellpadding="0" border="0">
                 <tr>
                      
                      
                 </tr>
                 <tr>
                    <td align="center" colspan="10" valign="middle" class="button_container">
                        <? echo load_submit_buttons($permission, "fnc_material_issue", 0,0,"ResetForm()",1); ?>
                        
                    </td>
                 </tr>    
                 <tr>
                    <td  align="center" colspan="10">
                      <input id="Print" class="formbutton" type="button" style="width:80px" onClick="fnc_yd_material_issue_print(1)" name="print" value="Print">
                    </td>
                 </tr>          
          </table>
      <div id="issue_list_view"></div>
    </form>
    </fieldset>

</div>


   
    

</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>