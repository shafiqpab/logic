<?
/*--- ----------------------------------------- Comments
Purpose         :   This form will create Efficiency Percentage Slab Entry              
Functionality   :   
JS Functions    :
Created by      :   Mirza Tahmid Tajik
Creation date   :   27-05-2017
Updated by      :   Shafiq, REZA  
Update date     :   27-06-2019,04-10-2022      
QC Performed BY :       
QC Date         :
Comments        :
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Efficiency Percentage Slab", "../../", 1,1, $unicode,1,'');
?>

<script type="text/javascript">
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

    function fnc_LoadCompanyData(data)
    {  
        // freeze_window(1);
        var company_id=$('#cbo_company_name').val();
        var garmentItem=$('#garmentItem').val();
        var buyer_id=$('#buyer_id').val();

        $("#list_view_container_div").show('fast');

        show_list_view(company_id+'**'+garmentItem+'**'+buyer_id,'show_dtls_listview','list_view_container','requires/efficiency_percentage_slab_controller','setFilterGrid(\'tbl_list_search\',-1)');
        // release_freezing();
    }

    function show_operation(type)
    {
        if(type==2)
        {
            var company_id=$('#cbo_company_name').val();
            var location_id=$('#cbo_location_name').val();
            var gmts_item_id=$('#cbo_gmts_item').val();
            var buyer_id=$('#cbo_buyer_name').val();
            var garmentItem=$('#garmentItem').val();
            var buyerId=$('#buyer_id').val();

            show_list_view(company_id,'show_dtls_listview','list_view_container','requires/efficiency_percentage_slab_controller','setFilterGrid(\'tbl_list_search\',-1)');
        }
        else
        {
           var company_id=$('#cbo_company_name').val();
            show_list_view(company_id,'show_dtls_listview','list_view_container','requires/efficiency_percentage_slab_controller','setFilterGrid(\'tbl_list_search\',-1)');
        }
    }

    function fnc_efficiency_percentage( operation )
    {   
        if (form_validation('cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_gmts_item','Company Name*Location Name*Buyer Name*Item Name')==false)
        {
          return;
        }
        var company_name = $("#cbo_company_name").val();
        var location_name = $("#cbo_location_name").val();
        var item_name = $("#cbo_gmts_item").val();
        var buyer_name = $("#cbo_buyer_name").val();
        var updateMstId = $("#updateMstId").val();
        var row_num=$('#tbl_efficiency_percentage tbody tr').length;

        var fieldArr=Array();
        for (var i=1; i<=row_num; i++) 
        {
            /*if (form_validation('txtSmvLowerLimit_'+i+'*txtSmvUpperLimit_'+i+'*txtOrderQtyLowerLimit_'+i+'*txtOrderQtyUpperLimit_'+i,'SMV Lower Limit*SMV Upper Limit*Order Quantity Lower Limit*Order Quantity Upper Limit')==false)
                {
                return; 
                }*/

                var fieldStr = 'slabNo_'+i+'*txtSmvLowerLimit_'+i+'*txtSmvUpperLimit_'+i+'*txtLearningCubPercentage_'+i+'*updateDtls_'+i+'*cboComplexityLevel_'+i;
                fieldArr.push(fieldStr);

           // data_all=data_all+get_submitted_data_string('slabNo_'+i+'*txtSmvLowerLimit_'+i+'*txtSmvUpperLimit_'+i+'*txtLearningCubPercentage_'+i+'*updateDtls_'+i+'*cboComplexityLevel_'+i,"../../");
        }
        var data_all_str=fieldArr.join('*');
        var data_all=get_submitted_data_string(data_all_str,"../../");

        
        var data="action=save_update_delete_efficiency_percentage&operation="+operation+'&total_row='+row_num+data_all+'&company_name='+company_name+'&location_name='+location_name+'&item_name='+item_name+'&buyer_name='+buyer_name+'&updateMstId='+updateMstId;
       // alert(data);return;
        freeze_window(operation);
        http.open("POST","requires/efficiency_percentage_slab_controller.php", true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_efficiency_percentage_reponse;
    }
    
    function fnc_efficiency_percentage_reponse()
    {
        if(http.readyState == 4) 
        {
            var reponse=trim(http.responseText).split('**');
            
			if(reponse[0]==0)
			{
				show_msg(reponse[0]);
				fnc_LoadCompanyData(reponse[1]);
			}
			else if(reponse[0]==1 )
			{
				show_msg(reponse[0]);
                fnc_LoadCompanyData(reponse[1]);
                set_button_status(0, permission, 'fnc_efficiency_percentage',2,0);                
                reset_form('efficiencyDtls_1','','','');
			}
			else if(reponse[0]==10 )
			{
				show_msg(reponse[0]);
			}
            else if(reponse[0]==11 )
			{
				show_msg(reponse[0]);
			}
            release_freezing();
        }
    }

    function add_rf_tr(i)
    { 
        var row_num=$('#tbl_efficiency_percentage tbody tr').length;
        if (row_num!=i)
        {
            return false;
        }

        else
        { 
            i++;
            var k=i-1;
            $("#tbl_efficiency_percentage tbody tr:last").clone().find("input,select").each(function(){
            $(this).attr({ 
              'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
              'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
              'value': function(_, value) { return value }              
            });
            }).end().appendTo("#tbl_efficiency_percentage");
           
            $('#slabNo_'+i).val(i); 
            $('#txtSmvLowerLimit_'+i).val('');
            $('#txtSmvUpperLimit_'+i).val('');
            $('#txtOrderQtyLowerLimit_'+i).val('');
            $('#txtOrderQtyUpperLimit_'+i).val('');
            $('#txtNewOrder_'+i).val('');
            $('#txtRepeatOrder_'+i).val('');
            $('#txtLearningCubPercentage_'+i).val('');
            $('#updateDtls_'+i).val('');
            

            $('#increaserf_'+i).removeAttr("value").attr("value","+");
            $('#decreaserf_'+i).removeAttr("value").attr("value","-");
            $('#increaserf_'+i).removeAttr("onclick").attr("onclick","add_rf_tr("+i+");");
            $('#decreaserf_'+i).removeAttr("onclick").attr("onclick","fn_rf_deleteRow("+i+");");
            
            
        }
    }
       
    function fn_rf_deleteRow(rowNo) 
    { 
            var k=rowNo-1;
            if(rowNo!=1)
            {
              var numRow = $('#tbl_efficiency_percentage tbody tr').length; 
              $("table#tbl_efficiency_percentage tbody tr:eq("+k+")").remove();
                
				for(i = rowNo;i <= numRow;i++)
                {
					$("#tbl_efficiency_percentage tr:eq("+i+")").find("input").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+(i-1)},
							'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ (i-1) },
							'value': function(_, value) { return value }             
						}); 
						
						$('#slabNo_'+(i-1)).val(i-1); 
						$('#increaserf_'+i).removeAttr("value").attr("value","+");
						$('#decreaserf_'+i).removeAttr("value").attr("value","-");
						$('#increaserf_'+i).removeAttr("onclick").attr("onclick","add_rf_tr("+(i-1)+");");
						$('#decreaserf_'+i).removeAttr("onclick").attr("onclick","fn_rf_deleteRow("+(i-1)+");");
					
					})
					
					
						
						
						/*$('#slabNo_'+s).val(i); 
						$('#txtSmvLowerLimit_'+i).val();
						$('#txtSmvUpperLimit_'+i).val();
						$('#txtOrderQtyLowerLimit_'+i).val();
						$('#txtOrderQtyUpperLimit_'+i).val();
						$('#txtNewOrder_'+i).val();
						$('#txtRepeatOrder_'+i).val();
						*/
						
					
					
					
                }
            }
            else
            {
                return false;
            }
    }

    function fnc_load_from_data(id)
    {
        get_php_form_data(id,'populate_input_form_data','requires/efficiency_percentage_slab_controller');
        
    }

    function fnc_load_from_data_dtls(id)
    {
        //alert(id); return;        
        show_list_view(id,'show_dtls_listview_data','slabDtls','requires/efficiency_percentage_slab_controller','');
    }
</script>
</head>
<body onLoad="set_hotkey();">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <br>
    <center>
        <div style="width:450px;">
            <form name="efficiencyDtls_1" id="efficiencyDtls_1" class="fetch_results" style="margin-bottom:10px; float:left">
               <fieldset style="width:250px;" id="mst_part">
                    <table width="450" cellspacing="2" cellpadding="0" align="center">
                        <tr>
                            <td width="90" class="must_entry_caption"> <strong>Company Name</strong> </td>
                            <td>
                                <?
                                echo create_drop_down( "cbo_company_name", 135, "select comp.id,comp.company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_LoadCompanyData(this.value);load_drop_down( 'requires/efficiency_percentage_slab_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/efficiency_percentage_slab_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );// fnc_LoadCompanyData(this.value);
                                ?>
                                 <input type="hidden" id="updateMstId" name="updateMstId" class="abc">
                            </td>
                            <td width="90" class="must_entry_caption"> <strong>Location</strong> </td>
                            <td id="location_td">
                                <?
                                echo create_drop_down( "cbo_location_name", 135, $blank_array,"", 1, "-- Select Location --", $selected, "" );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="90" class="must_entry_caption"> <strong>Garments Item</strong> </td>
                            <td>
                                <?
                                echo create_drop_down( "cbo_gmts_item", 135, $garments_item,"", 1, "-- Select Item --", $selected, "" );
                                ?>
                            </td>
                            <td width="90" class="must_entry_caption"> <strong>Buyer Name</strong> </td>
                            <td id="buyer_td">
                                <?
                                echo create_drop_down( "cbo_buyer_name", 135, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                                ?>
                            </td>
                        </tr>   
                    </table>
               </fieldset>
            <br>
               <fieldset style="width:550px;" id="dtls_part">
                    <table id="tbl_efficiency_percentage" class="rpt_table" rules="all" width="100%" cellspacing="0" cellpadding="0" border="1">
                        <thead>
                            <tr>
                                <th rowspan="2" width="40">Slab No</th> 
                                <th colspan="2" width="200">SMV Range</th>
                                <th rowspan="2" width="100">Learning Curve %</th>
                                <th rowspan="2" width="100">Complexity Level</th>
                                <th rowspan="2" width="100"></th>
                            </tr>
                            <tr>
                                <th>Lower Limit</th>
                                <th>Upper Limit</th>
                            </tr>
                        </thead>
                        <tbody id="slabDtls">
                            <tr id="tr_1" class="general">
                                <td align="center">
                                    <input type="button" id="slabNo_1" name="slabNo_1" value="1" style="background-color:#B0B0B0" disabled>
                                </td>
                                <td align="center">
                                    <input type="text" name="txtSmvLowerLimit_1" id="txtSmvLowerLimit_1" class="text_boxes_numeric" style="width:100px">
                                </td>
                                <td align="center">
                                    <input type="text" name="txtSmvUpperLimit_1" id="txtSmvUpperLimit_1" class="text_boxes_numeric" style="width:100px">
                                </td>                    
                                <td align="center">
                                    <input type="text" name="txtLearningCubPercentage_1" id="txtLearningCubPercentage_1" class="text_boxes" style="width:100px" placeholder="0,0,0">
                                    <input type="hidden" id="updateDtls_1" name="updateDtls_1" class="abc">
                                </td> 
                                <td>
                                <?
                                   // $level_type_arr=return_library_array( "select ID,LEVEL_TYPE from LIB_COMPLEXITY_LEVEL where is_deleted=0 and status_active=1 order by LEVEL_TYPE",'ID','LEVEL_TYPE');
                                    echo create_drop_down( "cboComplexityLevel_1", 100, $complexity_level,"", 1, "-- Select --", $selected, "" );
                                ?>
                                </td>                       
                                <td>
                                    <input type="button" id="increaserf_1" name="increaserf_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_rf_tr(1)" />
                                    <input type="button" id="decreaserf_1" name="decreaserf_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_rf_deleteRow(1);" />
                                </td>
                            </tr>              
                        </tbody>    
                    </table>
                <center>
                    <table>
                        <tr>
                            <td colspan="6" height="40" valign="bottom" align="center" class="">
                				<? 
                                echo load_submit_buttons( $permission, "fnc_efficiency_percentage", 0,0 ,"reset_form('efficiencyDtls_1');",2);
                                ?>
                             </td>  
                         </tr>
                    </table>
                </center>

            </fieldset>
            </form>
        </div>
        <!-- ===================================== LIST VIEW PART START ================================ -->
        <!-- <div style="width:890px; margin-top:5px;" id="list_view_container"></div> -->
        <div style="width:660px; margin: 0 auto;display: none;" id="list_view_container_div">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="640" class="rpt_table" align="left">
                <thead>
                    <tr>
                        <th rowspan="2" width="40">SL</th>
                        <th rowspan="2" width="130">Company</th>
                        <th rowspan="2" width="130">Location</th>
                        <th width="130">Garments Item</th>
                        <th width="130">Buyer</th>
                    </tr>
                    <tr>
                        <th><? asort($garments_item); echo create_drop_down( "garmentItem", 130, $garments_item,'', 1,"-- Select Item --",$data[1],'fnc_LoadCompanyData(this.value);' ); ?></th>
                        <th>
                            <?                            
                             echo create_drop_down( "buyer_id", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name  order by buyer_name","id,buyer_name", 1, "-- Select Buyer--",'','fnc_LoadCompanyData(this.value);','','' ); 
                             ?>
                             
                        </th>
                    </tr>

                </thead>
            </table>
            <div style="width: 660px; overflow-y: scroll; max-height: 350px;">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="640" class="rpt_table" id="tbl_list_search">
                    <tbody id="list_view_container">
                        
                    </tbody>
                </table>
            </div>
        </div>
    </center>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html> 