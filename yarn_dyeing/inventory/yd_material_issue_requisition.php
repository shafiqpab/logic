<?
/*--- ----------------------------------------- Comments
Purpose         :   show the content of yarn dyeing material issue requisition
Functionality   :   
JS Functions    :
Created by      :   Sakib Ahamed
Creation date   :   15-11-2023
Updated by      :   
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
echo load_html_head_contents("Yarn Dyeing Material Issue Requisition", "../../", 1,1, $unicode,1,'','','');

?>

<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php"; 
    var permission='<? echo $permission; ?>';

    function ResetForm()
    {
        reset_form('yarnissue_1','issue_list_view','','cbouom_1,1', "$('#details_tbl tbody tr:not(:first)').remove(); disable_enable_fields('cbo_company_name*cbo_within_group*cbo_party_name',0)")
    }
    function openmypage_issue_req_id()
    { 
        if ( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_pro_type').value+"_"+document.getElementById('cbo_order_type').value+"_"+document.getElementById('cbo_yd_type').value;
        var page_link='requires/yd_material_issue_requisition_controller.php?action=issue_req_popup&data='+data;
        var title="Issue Req ID";
        
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
                reset_form('','','txt_req_no*cbo_company_name*cbo_location_name*txt_req_date*cbo_pro_type*cbo_order_type*cbo_yd_type*cbo_yd_process*txt_job_no*update_id','','');
				
                get_php_form_data( splt_val[0], "load_php_data_to_form", "requires/yd_material_issue_requisition_controller" );
                // alert(splt_val[0]+"******"+splt_val[1]);
                var list_view_orders = return_global_ajax_value( splt_val[0], 'load_php_dtls_form_aftersave', '', 'requires/yd_material_issue_requisition_controller');
                if(list_view_orders!='')
                {
                    $("#rec_issue_table tr").remove();
                    $("#rec_issue_table").append(list_view_orders);
                }
                fnc_total_calculate();
                set_all_onclick();
                
                // fnc_load_party(within_group);
                set_button_status(1, permission, 'fnc_material_issue_req', 1);
                //release_freezing();
            }
        }
    }

    function job_search_popup()
    {
        if ( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        else
        {
            var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_pro_type').value+"_"+document.getElementById('cbo_order_type').value+"_"+document.getElementById('cbo_yd_type').value+"_"+document.getElementById('cbo_yd_process').value;
            var page_link='requires/yd_material_issue_requisition_controller.php?action=job_popup&data='+data;
            var title='Order Popup';
            
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=400px,center=1,resize=0,scrolling=0','../')
            
            emailwindow.onclose=function()
            {
                //freeze_window(5);
                var theform=this.contentDoc.forms[0];
                var ids=this.contentDoc.getElementById("hidden_details_id").value;
                var job_no=this.contentDoc.getElementById("hidden_job_no").value;

                // var receive_ids=this.contentDoc.getElementById("txt_individual_id").value;
                
                $("#txt_job_no").val( job_no );

                get_php_form_data( job_no, "load_php_mst_data_to_form", "requires/yd_material_issue_requisition_controller" );
                
                var list_view_orders = return_global_ajax_value( ids+'**'+job_no, 'load_php_dtls_form', '', 'requires/yd_material_issue_requisition_controller');
                //alert(list_view_orders);
                if(list_view_orders!='')
                {
                    $("#rec_issue_table tr").remove();
                    $("#rec_issue_table").append(list_view_orders);
                }
                fnc_total_calculate();
                fnc_balance_qty_calculate();
                set_all_onclick();
                $('#cbo_company_name').attr('disabled','disabled');
                $('#cbo_pro_type').attr('disabled','disabled');
                $('#cbo_order_type').attr('disabled','disabled');
                $('#cbo_yd_type').attr('disabled','disabled');
                /*release_freezing();*/
            }
        }
    }
    
    function reset_fnc()
    {
        location.reload(); 
    }
    
    function check_iss_qty_ability(value,i)
    {
        
        var stock=(document.getElementById('txtStock_'+i).value)*1;
        var cumuQty=(document.getElementById('txtCumuReqQty_'+i).value)*1;
        var newReqQty=(value*1);
        // alert(newReqQty);
        var available= stock-newReqQty;

        if((newReqQty*1)>stock)
        {
            alert("Requisition qty Exceeded by Stock qty");
            document.getElementById('availableQty_'+i).value=0;         
            document.getElementById('txtreqquantity_'+i).value=stock;         
            return;
        }
        document.getElementById('availableQty_'+i).value=available.toFixed(2);
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
    
	function fnc_yd_material_issue_print(report_no){

        if ( form_validation('cbo_company_name*update_id','Company Name*Update Id')==false )
        {
            return;
        }
        if(report_no==1){
            var report_title=$( "div.form_caption" ).html();
            print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "yd_material_issue_print", "requires/yd_material_issue_requisition_controller") 
            //return;
            show_msg("3");
        }
    }

    function fnc_total_calculate()
    {
        var rowCount = $('#rec_issue_table tr').length;
        var ddd={ dec_type:2, comma:0, currency:''}
         math_operation( "txtTotIssueReqqty", "txtreqquantity_", "+", rowCount, ddd );
    }

    function fnc_balance_qty_calculate() {
        var currentIssueQty = document.getElementById('txtTotIssueReqqty').value;
        console.log(currentIssueQty);
    }

    function fnc_material_issue_req( operation )
    {
        if ( form_validation('cbo_company_name*cbo_location_name*txt_req_date*txt_job_no', 'Company Name*Location*Requisition Date*Job No')==false )
        { 
           return;
        }
        else
        {
            var total_row=$('#details_tbl tbody tr').length;   
            var data_str="";
            var data_str=get_submitted_data_string('cbo_company_name*cbo_location_name*txt_req_date*cbo_pro_type*cbo_order_type*cbo_yd_type*cbo_yd_process*txt_job_no*update_id*txt_req_no*txt_remarks',"../../");
            // alert(data_str);
            var k=0;
            // alert(total_row);
            for (var i=1; i<=total_row; i++)
            {
                var qty=($('#txtreqquantity_'+i).val())*1;
                
                if(qty>0)
                {
                    k++;
                    data_str+="&cboWithinGroup_" + k + "='" + $('#hdnWithinGroup_'+i).val()+"'"+"&cboPartyName_" + k + "='" + $('#hdnPartyName_'+i).val()+"'&txtJobNo_" + k + "=" + $('#txtJobNo_'+i).val()+""+"&txtOrderNo_" + k + "='" + $('#txtOrderNo_'+i).val()+"'"+"&txtJobDescription_" + k + "='" + $('#txtJobDescription_'+i).val()+"'"+"&txtLotNo_" + k + "='" + $('#txtLotNo_'+i).val()+"'"+"&txtRvcdQty_" + k + "='" + $('#txtRvcdQty_'+i).val()+"'"+"&txtStock_" + k + "='" + $('#txtStock_'+i).val()+"'"+"&txtCumuReqQty_" + k + "='" + $('#txtCumuReqQty_'+i).val()+"'"+"&txtreqquantity_" + k + "='" + $('#txtreqquantity_'+i).val()+"'"+"&availableQty_" + k + "='" + $('#availableQty_'+i).val()+"'"+"&orderno_" + k + "='" + $('#orderno_'+i).val()+"'"+"&orderDtlsid_" + k + "='" + $('#orderDtlsid_'+i).val()+"'"+"&metarialRcvid_" + k + "='" + $('#metarialRcvid_'+i).val()+"'"+"&metarialRcvDtlsid_" + k + "='" + $('#metarialRcvDtlsid_'+i).val()+"'"+"&updatedtlsid_" + k + "='" + $('#updatedtlsid_'+i).val()+"'";
                }
            }

            var data="action=save_update_delete&operation="+operation+'&total_row='+k+data_str;//+'&zero_val='+zero_val
            /*alert (data);return;*/
            freeze_window(operation);
            http.open("POST","requires/yd_material_issue_requisition_controller.php",true);
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
            if(response[0]=="11")
			{
				alert (response[1]);
				release_freezing();
				return;
			}
            show_msg(response[0]);
            //$('#cbo_uom').val(12);

            if(response[0]==0 || response[0]==1)
            {
                
               /*alert(response);return;*/
                
                document.getElementById('txt_req_no').value= response[1];
                document.getElementById('update_id').value = response[2];
                 set_button_status(1, permission, 'fnc_material_issue_req',1);
                 
                 var list_view_orders = return_global_ajax_value(response[2], 'load_php_dtls_form_aftersave', '', 'requires/yd_material_issue_requisition_controller');
                
                if(list_view_orders!='')
                {
                    $("#rec_issue_table tr").remove();
                    $("#rec_issue_table").append(list_view_orders);
                }
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
   
</script>

</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?>
        <fieldset style="width:1200px;">
        <legend style="text-align: left;">Yarn Dyeing Material Issue Requisition</legend>
            <form name="yarnIssueReq_1" id="yarnIssueReq_1" autocomplete="off">  
                <table  width="1200" cellspacing="5" cellpadding="0"  border="0">
                    <tr>
                        
                        <td height="" align="right" colspan="5">Req No.</td>
                        <td  width="170" colspan="5">
                            <input class="text_boxes"  type="text" name="txt_req_no" id="txt_req_no" onDblClick="openmypage_issue_req_id()"  placeholder="Double Click" style="width:160px;" readonly/> <input type="hidden" name="update_id" id="update_id">
                            

                        </td>
                    
                    </tr>

                    <tr>
                        <td align="right" class="must_entry_caption">YD Company </td>
                        <td width="150"> 
                            <? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/yd_material_issue_requisition_controller', this.value, 'load_drop_down_location', 'location_td' );"); ?>
                        </td>
                        <td align="right" class="must_entry_caption" >Location</td>
                        <td id="location_td">
                            <? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                        </td>
                        <td class="must_entry_caption" align="right">Requisition Date</td>
                        <td width="160">
                            <input type="text" name="txt_req_date" id="txt_req_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:160px" />             
                        </td>
                        <td align="right">Prod. Type</td>                                              
                        <td id="order_type_td" width="160">
                            <?
                            echo create_drop_down("cbo_pro_type", 150, $w_pro_type_arr,"", 1, "-- Select Type --",$selected,"", "","","","","",7 ); 
                            ?>
                        </td>
                        <td align="right">Order Type</td>                                              
                        <td id="order_type_td">
                            <?
                            echo create_drop_down("cbo_order_type", 150, $w_order_type_arr,"", 1, "-- Select Type --",$selected,"fnc_load_order_type(this.value);", "","","","","",7 ); 
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">Y/D Type</td>                                              
                        <td id="yd_type_td">
                            <?
                            echo create_drop_down("cbo_yd_type", 150, $yd_type_arr,"", 1, "-- Select Y/D Type --",$selected,"", "","","","","",7 ); 
                            ?>
                        </td>
                        <td align="right">Y/D Process</td>                                              
                        <td id="yd_process_td">
                            <?
                           
                            echo create_drop_down("cbo_yd_process", 150, $yd_process_arr,"", 1, "-- Select Y/D Process --",$selected,"change_sub_process(this.value);",1,"","","","",7 ); 
                            ?>
                        </td>

                        <td align="right" class="must_entry_caption" align="right">Job No</td>
                        <td width="380" colspan="3">
                        <input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="job_search_popup();" placeholder="Double Click" style="width:375px;" readonly/>             
                        </td>
                        <td align="right" class="" align="right">Remarks</td>
                        <td>
                            <input class="text_boxes" type="text" name="txt_remarks" id="txt_remarks"  style="width:160px" />   
                        </td>
                    </tr>
                    
                </table>         


                <legend>Yd Material Issue Requisition Details Entry</legend>
                <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" width="1200" id="details_tbl" rules="all">
                    <thead class="form_table_header">
                        <tr align="center" >
                            <th width="30">SL</th>
                            <th width="80">Within Group</th>
                            <th width="120">Party</th>
                            <th width="120">Job No</th>
                            <th width="120">Wo No</th>
                            <th width="230">Job Description</th>
                            <th width="100">Lot No</th>
                            <th width="80">Rcvd Qty.</th>
                            <th width="80">Stock Qty.</th>
                            <th width="80">Cumu. Req. Qty.</th>
                            <th width="80">Requisition Qty</th>
                            <th width="80">Available Req. Qty</th>
                        </tr>
                    </thead> 
                    <tbody id="rec_issue_table">
                        <tr>
                            <td>
                                <input type="hidden" name="ordernoid_1" id="ordernoid_1">
                                <input type="hidden" name="jobno_1" id="jobno_1">
                                <input type="hidden" name="rcvid_1" id="colorid_1">
                              

                                
                            <input name="Serialid_1" id="" class="Serialid_1" type="text"  style="width:30px" value=""readonly/>  
                            </td>

                            
                            <td>
                                <input type="text" id="cboWithinGroup_1" name="cboWithinGroup_1" class="text_boxes" style="width:80px" readonly>
                            </td>
                            <td>
                                <input type="text" id="cboPartyName_1" name="cboPartyName_1" class="text_boxes" style="width:120px" readonly>
                            </td>

                            <td>
                                <input type="text" id="txtJobNo_1" name="txtJobNo_1" class="text_boxes" style="width:120px" readonly>
                            </td>
                            <td>
                                <input type="text" id="txtOrderNo_1" name="txtOrderNo_1" class="text_boxes" style="width:120px" readonly>
                            </td>
                            <td>
                                <input name="txtJobDescription_1" id="txtJobDescription_1" class="text_boxes" type="text"  style="width:230px" value="" readonly/>
                            </td>
                            <td>
                                <input name="txtLotNo_1" id="txtLotNo_1" class="text_boxes" type="text"  style="width:100px" value="" readonly/>
                            </td>
                            <td>
                                <input name="txtRvcdQty_1" id="txtRvcdQty_1" class="text_boxes_numeric" type="text"  style="width:80px" readonly/>
                            </td>
                            <td>
                                <input name="txtStock_1" id="txtStock_1" class="text_boxes_numeric" type="text"  style="width:80px" readonly/>
                            </td>
                            <td>
                                <input name="txtCumuReqQty_1" id="txtCumuReqQty_1" class="text_boxes_numeric" type="text"  style="width:80px" readonly/>
                            </td>
                            <td>
                                <input name="txtreqquantity_1" id="txtreqquantity_1" class="text_boxes_numeric" type="text"  style="width:80px" readonly/>
                            </td>
                            
                            <td>
                                <input name="availableQty_1" id="availableQty_1" class="text_boxes_numeric" type="text"  style="width:80px" readonly/>
                            </td>
                            <td></td>
                            
                            
                        
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
                            <td colspan="2">Requisition Total:</td>
                            <td><input name="txtTotIssueReqqty" id="txtTotIssueReqqty" class="text_boxes_numeric" type="text" readonly style="width:80px" /></td>
                            <td>&nbsp;</td>
                            
                        </tr>
                    </tfoot>  
                </table> 
                <table width="900" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" colspan="10" valign="middle" class="button_container">
                            <? echo load_submit_buttons($permission, "fnc_material_issue_req", 0,0,"ResetForm()",1); ?>
                            
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