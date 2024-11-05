<?
/*--- ----------------------------------------- Comments
Purpose			: 						
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	23-01-2019
Updated by 		: 		
Update date		:
Oracle Convert 	:		
Convert date	: 	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start(); 
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Trims Job Card Preparation", "../../", 1,1, $unicode,1,'');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	
    }
</script>
</head>
<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
   	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="capacitycalculation_1" id="capacitycalculation_1" method="" autocomplete="off">
    <fieldset style="width:900px ">
    <legend>Capacity Calculationy</legend>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td class="must_entry_caption">Company </td>
                <td align="left">
                    <input type="hidden" id="update_id" name="update_id" />
                    <input type="hidden" id="line_id" name="line_id" />
                    <?
                        echo create_drop_down( "cbo_company_id",160,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/capacity_calculation_controller', this.value, 'load_drop_down_location', 'location_td');","","","","","",2); //location_select();
                    ?>
                </td>
                <td class="must_entry_caption">Capacity Source </td>
                <td align="left" id="">
                    <?
                        echo create_drop_down( "cbo_capacity_source",160,$knitting_source,"", 1, "--Select Location--", $selected, "","","1,3","","","","");
                    ?>
                </td>
                <td >Location </td>
                <td align="left" id="location_td">
                    <?
                        echo create_drop_down( "cbo_location_id",160,$blank_array,"", 1, "--Select Location--", $selected, "","","","","","",2);
                    ?>
                </td>
                
            </tr>
            <tr>
            <td width="59" class="must_entry_caption">Year</td>
                <td width="155">
                    <?
                        $cyear=date("Y",time());
                        $pyear=$cyear-5;
                        for ($i=0; $i<5; $i++)
                        {
                        $year[$pyear+$i]=$pyear+$i;
                        }
                        echo create_drop_down( "cbo_year", 160,$year,"", 1, "-- Select --", $selected,"update_year(this.value);" );
                    ?>                              
                </td>
                <td width="59" class="must_entry_caption">Month</td>
                <td width="155">
                    <?
                        $cmonth=date("M",time());
                        echo create_drop_down( "cbo_month", 160,$months,"", 1, "-- Select --", $cmonth,"daysInMonth(); update_pr(this.value);" );//last_fild_value(document.getElementById('txt_no_of_line_1').value);
                    ?>
                </td>
                <td width="130" class="must_entry_caption">Man / Machine Per Line</td>
                <td width="100">
                    <input type="text" name="txt_avg_mch_line" id="txt_avg_mch_line" class="text_boxes_numeric" style="width:150px"/>
                </td>
                
            </tr>
            <tr>
                <td width="120" class="must_entry_caption">Basic SAM</td>
                <td width="122">
                    <input type="text" name="txt_basic_smv" id="txt_basic_smv" class="text_boxes_numeric" style="width:150px" />
                </td>
                <td width="130" class="must_entry_caption">Efficiency %</td>
                <td width="100">
                    <input type="text" name="txt_efficiency_per" id="txt_efficiency_per" class="text_boxes_numeric" style="width:150px"/>
                </td>
                
            </tr>
        </table>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td align="center" colspan="9" valign="middle" class="button_container">
                    <?
                        echo load_submit_buttons( $permission, "fnc_capacity_calculation", 0,0 ,"reset_form('capacitycalculation_1','','','','disable_enable_fields(\'cbo_company_id*cbo_location_id*cbo_year*txt_avg_mch_line*txt_basic_smv\'); $(\'#date_tbl tr:not(:first)\').remove(); ')",1);
                    ?>
                </td>
            </tr>
        </table>
        </fieldset>
        <br>
        <table cellpadding="0" cellspacing="0" width="100%" >
            <tr>
                <td align="center" valign="top" width="420">
                <fieldset style="width:440px ">
                    <table cellpadding="0" border="1" cellspacing="0" width="100%" class="rpt_table" rules="all" >
                        <thead>
                            <th width="50">SL</th>
                            <th width="70">Month</th>
                            <th width="80">Working Day</th>
                            <th width="100">Capacity (Mnt.)</th>
                            <th width="100">Capacity (Pcs)</th>
                        </thead>
                    </table>
                     <table cellpadding="0" border="1" cellspacing="0" width="100%" id="year_tbl" class="rpt_table" rules="all">
                        <tbody>
							<? $kk=1; for( $i = 1; $i <= 12; $i++ ) { ?>
                            <tr>
                                <td>
                                    <input type="hidden" id="update_id_year_dtls_<?php echo $kk; ?>" name="update_id_year_dtls_<?php echo $kk; ?>" />
                                    <input type="text" name="txt_sl_no_<?php echo $kk; ?>" id="txt_sl_no_<?php echo $kk; ?>" class="text_boxes_numeric" value="<?php echo $kk; ?>" style="width:50px" readonly />
                                </td>
                                <td>
                                    <input type="text" name="txt_month_<?php echo $kk; ?>" id="txt_month_<?php echo $kk; ?>" class="text_boxes" style="width:70px" value="<? echo $months[$i]; ?>" readonly />
                                    <input type="hidden" id="txt_month_id_<?php echo $kk; ?>" name="txt_month_id_<?php echo $kk; ?>" value="<?php echo $kk; ?>" />
                                </td>
                                <td>
                                    <input type="text" name="txt_working_day_<?php echo $kk; ?>" id="txt_working_day_<?php echo $kk; ?>" class="text_boxes_numeric" style="width:80px" readonly />
                                </td>
                                <td>
                                    <input type="text" name="txt_year_capacity_min_<?php echo $kk; ?>" id="txt_year_capacity_min_<?php echo $kk; ?>" class="text_boxes_numeric" style="width:100px" readonly />
                                </td>
                                <td>
                                    <input type="text" name="txt_year_capacity_pcs_<?php echo $kk; ?>" id="txt_year_capacity_pcs_<?php echo $kk; ?>" class="text_boxes_numeric" style="width:100px" readonly />
                                </td>
                            </tr>
                            <? $kk++; } ?>
                        </tbody>
                    </table>
                    <table cellpadding="0" border="1" cellspacing="0" width="100%" class="rpt_table" rules="all">
                        <tfoot>
                            <tr>
                                <td colspan="2" width="120" align="right"><strong>Total : </strong></td>
                                <td width="80" align="right">
                                    <input type="text" name="txt_working_day_total" id="txt_working_day_total" class="text_boxes_numeric" style="width:80px;" readonly />
                                </td>
                                <td width="100">
                                    <input type="text" name="txt_capacity_min_total" id="txt_capacity_min_total" class="text_boxes_numeric" style="width:100px;" readonly />
                                </td>
                                <td width="100">
                                    <input type="text" name="txt_capacity_pcs_total" id="txt_capacity_pcs_total" class="text_boxes_numeric" style="width:100px;" readonly />
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    </fieldset>
                </td>
            </tr>
        </table>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>