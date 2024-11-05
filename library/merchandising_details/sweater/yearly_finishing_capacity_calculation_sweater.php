<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Capacity Calculation for sweater.
Functionality	:	
JS Functions	:
Created by		:	Kamrul Hasan
Creation date 	: 	04.02.2024
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
echo load_html_head_contents("Capacity Calculationy", "../../../", 1, 1,$unicode,1,'');
?>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Include TableExport library -->
<script src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
<!-- Include Blob.js and FileSaver.js for compatibility with some browsers -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Blob.js/1.1.1/blob.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
    var permission='<? echo $permission; ?>';

	
	function update_year(val)
	{
        $("#cbo_company_id").attr("disabled", true);
        $("#cbo_location_id").attr("disabled", true);
        //$("#basic_smv").attr("readonly", "readonly");
        //$("#efficiency_per").attr("readonly", "readonly");
        //$("#smoothing_per").attr("readonly", "readonly");
        $("#show_textcbo_machine_gauge").prop("disabled", true);
        $("#cbo_year").attr("readonly", "readonly");
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location_id').value+"_"+val+"_"+document.getElementById('basic_smv').value+"_"+document.getElementById('efficiency_per').value+"_"+document.getElementById('smoothing_per').value+"_"+document.getElementById('cbo_machine_gauge').value;
		//alert(data);
		var list_view_capacity = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/yearly_finishing_capacity_calculation_sweater_controller');
			
 		if(list_view_capacity!='')
		{
			$("#php_dtls_form_load").html('');
			$("#php_dtls_form_load").html(list_view_capacity);
		}
        setTimeout(function(){
            var mast_id = $("#toUpdateId").val();
            if(mast_id){
                $("#update_id").val(mast_id);
                set_button_status(1, permission, 'fnc_capacity_calculation',1);
            }
        }
        , 1000);
		
	}

    function calculateData(idStr, machinesIdStr){
        var [selectTxt,machinId,particularId,monthId] = idStr.split('_');
        var machinesArray = machinesIdStr.split(',');
        var selectVal = 0;
        var multipliedVal = 0;
        var minute = 60;
        if(particularId == 3){
            machinesArray.map((mid)=>{
                var particulaTotal_3 = 0; var particulaTotal_4 = 0;
                for(m=1;m<=12;m++){
                    selectVal = $('#'+selectTxt+'_'+mid+'_3_'+m).val()*1;
                    multipliedVal = selectVal*minute;
                    $('#'+selectTxt+'_'+mid+'_4_'+m).val(multipliedVal.toFixed(2));
                    particulaTotal_3 += selectVal;
                    particulaTotal_4 += multipliedVal;
                }
                $('#selector_right_total'+'_'+mid+'_3').val(particulaTotal_3.toFixed(2));
                $('#selector_right_total'+'_'+mid+'_4').val(particulaTotal_4.toFixed(2));
            });

        }

        multiplyData(idStr, machinesIdStr);

    }//function end;


    function multiplyData(idStr, machinesIdStr){
        var [selectTxt,machinId,particularId,monthId] = idStr.split('_');
        var machinesArray = machinesIdStr.split(',')
       
       
        if(particularId == 1 || particularId == 2 || particularId == 3 || particularId == 5 || particularId == 7 || particularId == 9){
            machinesArray.map((mid)=>{
                var particulaTotal_1 = 0; var particulaTotal_2 = 0; var particulaTotal_6 = 0; 
                var particulaTotal_8 = 0; var particulaTotal_10 = 0;var particulaTotal_11 = 0;
                var particulaTotal_12 = 0;
                var selectValTotal_5 = 0; var selectValTotal_7 = 0; var selectValTotal_9 = 0;
                var selectValAvg_5 = 0; var selectValAvg_7 = 0; var selectValAvg_9 = 0;

                for(m=1;m<=12;m++){
                    var selectVal_1 = $('#'+selectTxt+'_'+mid+'_1_'+m).val()*1;
                    var selectVal_2 = $('#'+selectTxt+'_'+mid+'_2_'+m).val()*1;
                    var selectVal_4 = $('#'+selectTxt+'_'+mid+'_4_'+m).val()*1;
                    var selectVal_5 = $('#'+selectTxt+'_'+mid+'_5_'+m).val()*1;
                    var selectVal_7 = $('#'+selectTxt+'_'+mid+'_7_'+m).val()*1;
                    var selectVal_9 = $('#'+selectTxt+'_'+mid+'_9_'+m).val()*1;

                    var multiplyQty_6  = selectVal_1 * selectVal_2 * selectVal_4 * selectVal_5;
                    var multiplyQty_8  = multiplyQty_6 * selectVal_7;
                    var multiplyQty_10 = multiplyQty_8 / selectVal_9;
                    var multiplyQty_11 = multiplyQty_10 / (selectVal_2 ? selectVal_2 : 1);
                    var multiplyQty_12 = multiplyQty_11 / (selectVal_1 ? selectVal_1 : 1 ) / 10;

                    selectValTotal_5 += selectVal_5;
                    selectValTotal_7 += selectVal_7;
                    selectValTotal_9 += selectVal_9;

                    $('#'+selectTxt+'_'+mid+'_6_'+m).val(multiplyQty_6.toFixed(2));
                    $('#'+selectTxt+'_'+mid+'_8_'+m).val(multiplyQty_8.toFixed(2));
                    $('#'+selectTxt+'_'+mid+'_10_'+m).val(multiplyQty_10.toFixed(2));
                    $('#'+selectTxt+'_'+mid+'_11_'+m).val(multiplyQty_11.toFixed(2));
                    $('#'+selectTxt+'_'+mid+'_12_'+m).val(multiplyQty_12.toFixed(2));

                    particulaTotal_1  += selectVal_1;
                    particulaTotal_2  += selectVal_2;
                    particulaTotal_6  += multiplyQty_6;
                    particulaTotal_8  += multiplyQty_8;
                    particulaTotal_10 += multiplyQty_10;
                    //particulaTotal_11 += multiplyQty_11;
                    particulaTotal_12 += multiplyQty_12;
                }

                selectValAvg_5 = selectValTotal_5/12;
                selectValAvg_7 = selectValTotal_7/12;
                selectValAvg_9 = selectValTotal_9/12;
                particulaTotal_11 = particulaTotal_10/12;
                $('#selector_right_total'+'_'+mid+'_1').val(particulaTotal_1.toFixed(2));
                $('#selector_right_total'+'_'+mid+'_2').val(particulaTotal_2.toFixed(2));
                $('#selector_right_total'+'_'+mid+'_5').val(selectValAvg_5.toFixed(2));
                $('#selector_right_total'+'_'+mid+'_6').val(particulaTotal_6.toFixed(2));
                $('#selector_right_total'+'_'+mid+'_7').val(selectValAvg_7.toFixed(2));
                $('#selector_right_total'+'_'+mid+'_8').val(particulaTotal_8.toFixed(2));
                $('#selector_right_total'+'_'+mid+'_9').val(selectValAvg_9.toFixed(2));
                $('#selector_right_total'+'_'+mid+'_10').val(particulaTotal_10.toFixed(2));
                $('#selector_right_total'+'_'+mid+'_11').val(particulaTotal_11.toFixed(2));
                $('#selector_right_total'+'_'+mid+'_12').val(''); //particulaTotal_12.toFixed(2)

            });

            getBottomTotal(idStr, machinesIdStr);

        }

    }


    function getBottomTotal(idStr, machinesIdStr){
        var [selectTxt,machinId,particularId,monthId] = idStr.split('_');
        var machinesArray = machinesIdStr.split(',');
        
            var btmRightTotal_1 = 0; var btmRightTotal_2 = 0; var btmRightTotal_3 = 0; var btmRightTotal_4 = 0;
            for(m=1;m<=12;m++){
                var btmTotal_1 = 0; var btmTotal_2 = 0; var btmTotal_3 = 0; var btmTotal_4 = 0;
                machinesArray.map((mid)=>{
                    var selectVal_1 = $('#'+selectTxt+'_'+mid+'_1_'+m).val()*1;
                    var selectVal_6 = $('#'+selectTxt+'_'+mid+'_6_'+m).val()*1;
                    var selectVal_8 = $('#'+selectTxt+'_'+mid+'_8_'+m).val()*1;
                    var selectVal_10 = $('#'+selectTxt+'_'+mid+'_10_'+m).val()*1;
                    btmTotal_1 += selectVal_1;
                    btmTotal_2 += selectVal_6;
                    btmTotal_3 += selectVal_8;
                    btmTotal_4 += selectVal_10;

                    btmRightTotal_1 += selectVal_1;
                    btmRightTotal_2 += selectVal_6;
                    btmRightTotal_3 += selectVal_8;
                    btmRightTotal_4 += selectVal_10;
                });

             $('#total_selector_1_'+m).val(btmTotal_1.toFixed(2));
             $('#total_selector_2_'+m).val(btmTotal_2.toFixed(2));
             $('#total_selector_3_'+m).val(btmTotal_3.toFixed(2));
             $('#total_selector_4_'+m).val(btmTotal_4.toFixed(2));
            }

             $('#right_bottom_total_1').val(btmRightTotal_1.toFixed(2));
             $('#right_bottom_total_2').val(btmRightTotal_2.toFixed(2));
             $('#right_bottom_total_3').val(btmRightTotal_3.toFixed(2));
             $('#right_bottom_total_4').val(btmRightTotal_4.toFixed(2));
       
    }

    function fnc_capacity_calculation( operation )
	{
        var fieldId = '';
        var machineIdsArray      = $("#machineIds").val().split(',');
        var particularIdsArray   = $("#particularIds").val().split(',');
        var totalParticularIds   = $("#totalParticularIds").val().split(',');
		if ( form_validation('cbo_company_id*basic_smv*efficiency_per*smoothing_per*cbo_machine_gauge*cbo_year','Company Name*Basic SMV*Efficiency*Smoothing*machine gauge*Year')==false )
		{
			return;
		}	
        fieldId += "cbo_company_id*cbo_location_id*basic_smv*efficiency_per*smoothing_per*cbo_machine_gauge*cbo_year*";
        machineIdsArray.map((machineId) => {
            particularIdsArray.map((particularId) => {
                for(month=1; month<=12; month++){
                    fieldId += "selector_"+machineId+"_"+particularId+"_"+month+"*";

                }
                fieldId += "selector_right_total_"+machineId+"_"+particularId+"*";
            });
        });

        totalParticularIds.map((particularId) => {
            for(month=1; month<=12; month++){
                fieldId += "total_selector_"+particularId+"_"+month+"*";

            }
            fieldId += "right_bottom_total_"+particularId+"*";
        });
        fieldId = rtrim(fieldId, '*');
        data = get_submitted_data_string(fieldId,"../../../");
        data = "action=save_update_delete&operation="+operation+"&update_id="+$("#update_id").val()+data+"&machine_ids="+$("#machineIds").val()+"&particular_ids="+$("#particularIds").val()+"&total_particular_ids="+$("#totalParticularIds").val();
        //console.log(data); return;
		freeze_window(operation);
		http.open("POST","requires/yearly_finishing_capacity_calculation_sweater_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_capacity_calculation_reponse;
	}


	function fnc_capacity_calculation_reponse()
	{
		if(http.readyState == 4) 
		{
            var reponse=trim(http.responseText).split('**');
			if(reponse[0]==0){
                document.getElementById("update_id").value=reponse[1];
                set_button_status(1, permission, 'fnc_capacity_calculation',1);
            }
			release_freezing();
           
		}
	}

    function rtrim(str, charToRemove) {
        const escapedChar = charToRemove.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(escapedChar + '+$');
        return str.replace(regex, '');
    }

    function make_multi_select(type)
	{
        
		set_multiselect('cbo_machine_gauge','0','0','','0');
	}

    

	
		
</script>
</head>
<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
   	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="capacitycalculation_1" id="capacitycalculation_1" method="" autocomplete="off">
    <fieldset style="width:900px ">
    <legend>Capacity Calculation</legend>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td class="must_entry_caption">Company </td>
                <td align="left">
                    <input type="hidden" id="update_id" name="update_id" />
                    <?
                        echo create_drop_down( "cbo_company_id",160,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/yearly_finishing_capacity_calculation_sweater_controller', this.value, 'load_drop_down_location', 'location_td');make_multi_select();","","","","","",2); //load_drop_down( 'requires/yearly_finishing_capacity_calculation_sweater_controller', this.value, 'load_drop_down_machine_gauge', 'machine_gauge_td');
                    ?>
                </td>
                <td >Location </td>
                <td align="left" id="location_td">
                    <?
                        echo create_drop_down( "cbo_location_id",160,$blank_array,"", 1, "--Select Location--", $selected, "","","","","","",2);
                    ?>
                </td>

                <td class="must_entry_caption">Basic SMV</td>
                <td align="left" id="">
                    <input type="text" name="basic_smv" id="basic_smv" class="text_boxes_numeric" style="width:150px"  />
                </td>
                
                
            </tr>
            <tr>
                <td width="59" class="must_entry_caption">Efficiency(%)</td>
                <td width="155">
                    <input type="text" name="efficiency_per" id="efficiency_per" class="text_boxes_numeric" style="width:150px" />                            
                </td>
                <td width="59" class="must_entry_caption">Smoothing(%)</td>
                <td width="155">
                    <input type="text" name="smoothing_per" id="smoothing_per" class="text_boxes_numeric" style="width:150px" />
                </td>
                <td width="130" class="must_entry_caption">Section</td>
                <td width="100" id="machine_gauge_td">
                    <?php 
                        $machinesArray = array(1=> "Button", 2=>"Technical Attachment", 3=> "Labeling", 4=> "Iron", 5=>"Packing");
                        echo create_drop_down( "cbo_machine_gauge", 160, $machinesArray,"", 0, "-- Select gauge--", $selected, "","","" );
                    ?>
                </td>
                
            </tr>
            <tr>
                <td class="must_entry_caption">Year</td>
                <td>
                    <?php echo create_drop_down( "cbo_year", 160,$year,"", 1, "-- Select --", $selected,"update_year(this.value);" ); ?>
                </td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td align="center" colspan="9" valign="middle" class="button_container">
                    <?
                        echo load_submit_buttons( $permission, "fnc_capacity_calculation", 0,0 ,"reset_form('capacitycalculation_1','','','','disable_enable_fields(\'cbo_company_id*cbo_location_id*cbo_year*txt_avg_mch_line*txt_basic_smv\'); $(\'#date_tbl tr:not(:first)\').remove(); ')",1);
                    ?>
                    <input type="button" style="width:80px;" class="formbutton" id="exportBtn" value="Excell"/>
                </td>
            </tr>
        </table>
        </fieldset>
        <br>
        <fieldset style="width:95%" id="php_dtls_form_load">
            
        </fieldset>
    </form>
</div>
</body>
<script>
    set_multiselect('cbo_machine_gauge','0','0','','0');
</script>
<script>
    
    $(document).ready(function() {
        // Trigger the export when the button is clicked
        $("#exportBtn").click(function() {
            // Replace input values in the corresponding <td> elements
            $('.dataTd').each(function(i,v){
                var inputVal = $('.dataTd input').val();
                $(v).text(inputVal);
            });
            // Initialize TableExport with the table ID
            $("#myTable").tableExport({
                formats: ["xlsx"], // You can add other formats if needed
                fileName: "table_export",
                bootstrap: true, // Use Bootstrap styles
            });
            /* $('.dataTd').each(function(i,v){
                $('.dataTd').html('<input type="text" value="' + $(".dataTd input").text() + '">');
            }); */
            $("#cbo_year").trigger("change");
        });
    });
        
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>