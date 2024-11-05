<?php
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
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_location")
{
	 echo create_drop_down( "cbo_location_id", 160, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );		 
}

if ($action=="load_drop_down_machine_gauge")
{
    //select min(id) as ID,BRAND,GAUGE from LIB_MACHINE_NAME where COMPANY_ID=3 and CATEGORY_ID=1 group by BRAND,GAUGE
	 echo create_drop_down( "cbo_machine_gauge", 160, "select min(id) as ID,BRAND,GAUGE, CONCAT(CONCAT(GAUGE,' '), BRAND) as brand_gauge from LIB_MACHINE_NAME where company_id='$data' and CATEGORY_ID=1 and status_active =1 and is_deleted=0 group by BRAND,GAUGE","id,brand_gauge", 1, "--Select Gauge--", $selected, "","","","","","",3 );		 
}

if ($action=="load_php_dtls_form")
{
	$data           = explode('_',$data);
	$company_id     = $data[0];
	$location_id    = $data[1];
	$year           = $data[2];
	$basic_smv      = $data[3];
	$efficiency_per = $data[4];
	$smoothing_per  = $data[5];
	$machine_gauge  = $data[6];
    
    //Check for if data already saved for a company in a specific year.
    $capacitySQL = "SELECT A.ID, A.COMPANY_ID, A.LOCATION_ID, A.BASIC_SMV, A.EFFI_PERCENT, A.SMOOTHING_PERCENT, A.MACHINE_GAUGE, A.YEAR,
                    B.MACHINE_ID, B.PARTICULAR_ID, B.MONTH_ID, B.PARTICULAR_VALUE, B.PARTICULAR_TOTAL_VALUE
                    FROM LIB_KNITTING_CAPACITY_SWEATER_MST A 
                    INNER JOIN LIB_KNITTING_CAPACITY_SWEATER_DTLS B ON A.ID=B.MST_ID
                    WHERE A.COMPANY_ID=$company_id AND A.ENTRY_FORM=736 AND A.YEAR=$year AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0";
    //echo $capacitySQL;
    $capacityResult = sql_select($capacitySQL);
    $capacityResultCount = count($capacityResult);
    $dataArray = array();
    $existingMachineGaugeArray = array();
    if($capacityResultCount > 0){
        foreach($capacityResult as $row){
            $dataArray[$row[csf("MACHINE_ID")]][$row[csf("PARTICULAR_ID")]][$row[csf("MONTH_ID")]] = $row[csf("PARTICULAR_VALUE")];
            $dataArray['selector_right_total'][$row[csf("MACHINE_ID")]][$row[csf("PARTICULAR_ID")]][$row[csf("MONTH_ID")]] = $row[csf("PARTICULAR_TOTAL_VALUE")];
           
            $PARTICULAR_ID = 0;
            if($row[csf("PARTICULAR_ID")] == 10){$PARTICULAR_ID = 4;} 
            else if($row[csf("PARTICULAR_ID")] == 6){$PARTICULAR_ID = 2;} 
            else if($row[csf("PARTICULAR_ID")] == 8){$PARTICULAR_ID = 3;} 
            else if($row[csf("PARTICULAR_ID")] == 1){$PARTICULAR_ID = 1;} 

            $dataArray['total_selector'][$PARTICULAR_ID][$row[csf("MONTH_ID")]] += $row[csf("PARTICULAR_VALUE")];
            $dataArray['right_bottom_total'][$PARTICULAR_ID] += $row[csf("PARTICULAR_VALUE")];
            $existingMachineGaugeArray[$row['MACHINE_ID']] = $row['MACHINE_ID'];
        }
    }

    $machine_gauge = trim($machine_gauge.",".implode(',', $existingMachineGaugeArray), ',');
    //echo $machine_gauge; exit();

    
    ?>
        <table cellpadding="0" border="1" cellspacing="0" width="1500" class="rpt_table" rules="all" id="myTable">
            <thead>
                <th width="100">GG</th>
                <th width="100">Particular</th>
                <th width="100">January</th>
                <th width="100">February</th>
                <th width="100">March</th>
                <th width="100">Appril</th>
                <th width="100">May</th>
                <th width="100">Jun</th>
                <th width="100">July</th>
                <th width="100">August</th>
                <th width="100">September</th>
                <th width="100">October</th>
                <th width="100">November</th>
                <th width="100">December</th>
                <th width="100">Total</th>
            </thead>
            <tbody>
                <?php 
                    $machineGaugeSql = "SELECT min(id) as ID,BRAND,GAUGE from LIB_MACHINE_NAME where company_id='$company_id' and CATEGORY_ID=1 and status_active =1 and is_deleted=0 and id in ($machine_gauge) group by BRAND,GAUGE";
                  // echo $machineGaugeSql; exit();
                    $machinesArray = array();
                    foreach(sql_select($machineGaugeSql) as $gauge){
                        $machinesArray[$gauge['ID']] = $gauge['BRAND'].','.$gauge['GAUGE'];
                    }
                    
                    $attribute_arr = array(1=>"Machine QTY", 2=>"Working Day", 3=>"Working Hours", 4=>"Working Min", 5=>"Smoothing (%)", 6=>"Available Min", 7=>"Efficency (%)", 8=>"Capacity Min", 9=>"Avg SMV", 10=>"Capacity in Pcs");
                    $totalAttributes = array(1=>"Machine QTY",2=>"Available Min",3=>"Capacity Min",4=>"Capacity in Pcs");
                    $totalGrouping = array(1=>"Total");
                    //$totalRow = (count($attributes) * count($machinesArray)) + (count($totalAttributes) * count($totalGrouping));

                    $machineIds 			= implode(',',array_keys($machinesArray));
                    $particularIds 			= implode(',',array_keys($attribute_arr));
                    $totalParticularIds 	= implode(',',array_keys($totalAttributes));
                    $i=1;
                    foreach($machinesArray as $machineId => $group)
                    {
                        
                        foreach($attribute_arr as $particularId => $attribute)
                        {
                            
                            if($particularId == 5){
                                $value = $smoothing_per;
                            }else if($particularId == 7){
                                $value = $efficiency_per;
                            }else if($particularId == 9){
                                $value = $basic_smv;
                            }else{
                                $value = "";
                            }

                            //Title field
                            if($particularId == 4){
                                $title = $attribute_arr[3]." * 60";
                            }else if($particularId == 6){
                                $title = $attribute_arr[1]." * ".$attribute_arr[2]." * ".$attribute_arr[4]." * ".$attribute_arr[5];
                            }else if($particularId == 8){
                                $title = $attribute_arr[6]." * ".$attribute_arr[7];
                            }else if($particularId == 10){
                                $title = $attribute_arr[8]." / ".$attribute_arr[9];
                            }else{
                                $title = "";
                            }

                            
                            ?>
                            <tr style="<? echo $i==10 || $i%10==0 ? 'border-bottom: 3px solid green' : '';?>">
                                <? if($particularId == 1) { ?>
                                <td width="100" rowspan="<?= count($attribute_arr);?>" style="vertical-align:middle; text-align: center;"><?=$group?></td>
                                <? } ?>
                                <td width="100" ><?=$attribute?></td>
									<? foreach($months as $month_key => $month) {
										if($particularId == 1 || $particularId == 2 || $particularId == 3 || $particularId == 5 || $particularId == 7 || $particularId == 9){
											$readonly = "";
											$onkeyup = "calculateData('"."selector_".$machineId."_".$particularId."_".$month_key."', '".$machineIds."');";
										}else{
											$readonly = "readonly";
											$onkeyup = "";
										}
                                        $dataArrayVal = $dataArray[$machineId][$particularId][$month_key];
									?>
                                <td class="dataTd" title="<?=$title;?>"><input onkeyup="<?=$onkeyup?>" style="width: 80px;" id="selector_<?=$machineId?>_<?=$particularId?>_<?=$month_key?>" value="<?= $capacityResultCount > 0 && $dataArrayVal ? $dataArrayVal :$value; ?>" class="text_boxes_numeric" type="text" <?=$readonly?> /></td>
								<? } ?>
                                <td class="dataTd" ><input style="width: 80px;" id="selector_right_total_<?=$machineId?>_<?=$particularId?>" value="<?= $capacityResultCount > 0 ? $dataArray['selector_right_total'][$machineId][$particularId][$month_key] :$value; ?>" class="text_boxes_numeric" type="text" readonly /></td>
                            </tr>
                            <? 
                            $i++;
                        }
                       
                    }
                        ?>
						<input id="toUpdateId" value="<?=$capacityResult[0]["ID"]?>" type="hidden" />
						<input id="machineIds" value="<?=$machineIds?>" type="hidden" />
						<input id="particularIds" value="<?=$particularIds?>" type="hidden" />
						<input id="totalParticularIds" value="<?=$totalParticularIds?>" type="hidden" />
            </tbody>
            <tfoot>
                <?php 
                    
                        foreach($totalAttributes as $particularId => $attribute)
                        {
                            
                            ?>
							<tr>
                           <? if($particularId == 1) {?>
						   
						   		<td width="100" rowspan="<?= count($totalAttributes);?>" style="vertical-align:middle; text-align: center;">Total</td>
						   <? }
								
						    ?>
                         
                                <td width="100" ><?=$attribute?></td>

                                <? foreach($months as $month_key => $month) { ?>
                                	<td class="dataTd" ><input onkeyup="<?=$onkeyup?>" style="width: 80px;" id="total_selector_<?=$particularId?>_<?=$month_key?>" value="<?= $dataArray['total_selector'][$particularId][$month_key]; ?>" class="text_boxes_numeric" type="text" <?=$readonly?> /></td>
								<? } ?>

                                <td class="dataTd" ><input onkeyup="<?=$onkeyup?>" style="width: 80px;" id="right_bottom_total_<?=$particularId?>" value="<?=$dataArray['right_bottom_total'][$particularId]?>" class="text_boxes_numeric" type="text" <?=$readonly?> /></td>
                            </tr>
                            <? 
                             
                        }
                        ?>
            </tfoot>
        </table>

       
    <?
	
}



if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));  

    
		
    $location_id=str_replace("'","",$cbo_location_id);
    $company_id=str_replace("'","",$cbo_company_id);
    $basic_smv=str_replace("'","",$basic_smv);
    $effi_percent=str_replace("'","",$efficiency_per);
    $smoothing_percent=str_replace("'","",$smoothing_per);
    $machine_gauge=str_replace("'","",$cbo_machine_gauge);
    $cbo_year=str_replace("'","",$cbo_year);

    $machineIdArray     = explode(',', $machine_ids);
    $particularIdArray  = explode(',', $particular_ids);

    $con = connect();
	if ($operation==0)  // Insert Here==================================================
	{
		

        $mst_id = return_next_id( "id", "LIB_KNITTING_CAPACITY_SWEATER_MST", 1 ) ; 
        $field_array_mst="id,company_id,location_id,basic_smv,effi_percent,smoothing_percent,machine_gauge,year,entry_form";
		$data_array_mst="(".$mst_id.",".$company_id.",".$location_id.",".$basic_smv.",".$effi_percent.",".$smoothing_percent.",'".$machine_gauge."',".$cbo_year.", 736)";
		
		$dtls_id=return_next_id( "id", "LIB_KNITTING_CAPACITY_SWEATER_DTLS", 1 ); 
		$field_array_dtls="id,mst_id,machine_id,particular_id,year,month_id,particular_value,particular_total_value,company_id";
 
        foreach($machineIdArray as $machine_id)
		{
            foreach($particularIdArray as $particular_id)
            {
                foreach($months as $month_id => $month)
                {
                    $particular_value            =   "selector_".$machine_id."_".$particular_id."_".$month_id;
                    $particular_value            =   str_replace("'","",$$particular_value);
                    $particular_total_value      =   "selector_right_total_".$machine_id."_".$particular_id;
                    $particular_total_value      =   str_replace("'","",$$particular_total_value);
                    //echo $particular_total_value."<br>";
                    if ($data_array_dtls != "") $data_array_dtls .=",";
                    $data_array_dtls.="(".$dtls_id.",".$mst_id.",".$machine_id.",".$particular_id.",".$cbo_year.",".$month_id.",'".$particular_value."','".$particular_total_value."',".$company_id.")";
                    $dtls_id=$dtls_id+1;
                    
                }
            }
        
		}
       // exit();
      // echo "INSERT INTO LIB_KNITTING_CAPACITY_SWEATER_DTLS ($field_array_dtls) VALUES $data_array_dtls"; exit();
        $rID=sql_insert("LIB_KNITTING_CAPACITY_SWEATER_MST",$field_array_mst,$data_array_mst,0);
        $rID1=sql_insert("LIB_KNITTING_CAPACITY_SWEATER_DTLS",$field_array_dtls,$data_array_dtls,0);	
        //echo "0**".$rID." ".$rID1; exit();
		if( $rID && $rID1 ){
            oci_commit($con); 
            echo "0**".str_replace("'",'',$mst_id);
        }else{
            oci_rollback($con); 
            echo "10**".str_replace("'",'',$mst_id);
            }
        disconnect($con);
        die;
	}
	else if ($operation==1)   // Update Here=============================================================================
	{
		//$mst_id = return_next_id( "id", "LIB_KNITTING_CAPACITY_SWEATER_MST", 1 ) ; 
        $field_array_mst="company_id*location_id*basic_smv*effi_percent*smoothing_percent*machine_gauge*year";
		$data_array_mst="".$company_id."*".$location_id."*".$basic_smv."*".$effi_percent."*".$smoothing_percent."*'".$machine_gauge."'*".$cbo_year."";

        $dtls_sql = "SELECT B.ID FROM LIB_KNITTING_CAPACITY_SWEATER_DTLS B WHERE b.mst_id=$update_id and b.status_active=1 and b.is_deleted=0";
        $dtlsIdArray = array();
        $dtlsResult = sql_select($dtls_sql);
        foreach($dtlsResult as $row){
            $dtlsIdArray[$row[csf('id')]] = $row[csf('id')];
        }
        $mstIdsComma = implode(',', $dtlsIdArray);
        $field_array_dtls_update="status_active*is_deleted";
        $data_array_dtls_update="0*1";
       
		$dtls_id=return_next_id( "id", "LIB_KNITTING_CAPACITY_SWEATER_DTLS", 1 ); 
		$field_array_dtls="id,mst_id,machine_id,particular_id,year,month_id,particular_value,particular_total_value,company_id";
     
        foreach($machineIdArray as $machine_id)
		{
            foreach($particularIdArray as $particular_id)
            {
                foreach($months as $month_id => $month)
                {
                    $particular_value            =   "selector_".$machine_id."_".$particular_id."_".$month_id;
                    $particular_value            =   str_replace("'","",$$particular_value);
                    $particular_total_value      =   "selector_right_total_".$machine_id."_".$particular_id;
                    $particular_total_value      =   str_replace("'","",$$particular_total_value);
                    //echo $particular_total_value."<br>";
                    if ($data_array_dtls != "") $data_array_dtls .=",";
                    $data_array_dtls.="(".$dtls_id.",".$update_id.",".$machine_id.",".$particular_id.",".$cbo_year.",".$month_id.",'".$particular_value."','".$particular_total_value."',".$company_id.")";
                    $dtls_id=$dtls_id+1;
                    
                }
            }
          
		}
        //echo $field_array_mst."##".$data_array_mst; exit();
        //echo "INSERT INTO LIB_KNITTING_CAPACITY_SWEATER_MST ($field_array_mst) VALUES $data_array_mst"; exit();
        $ridUp = sql_multirow_update("LIB_KNITTING_CAPACITY_SWEATER_DTLS",$field_array_dtls_update,$data_array_dtls_update,'id',$mstIdsComma, 0);
        $rID=sql_update("LIB_KNITTING_CAPACITY_SWEATER_MST",$field_array_mst,$data_array_mst,"id",$update_id,0);
        $rID1=sql_insert("LIB_KNITTING_CAPACITY_SWEATER_DTLS",$field_array_dtls,$data_array_dtls,0);	
        //echo "1**".$rID ."&&". $rID1 ."&&". $ridUp."&&".$update_id; exit();
		if($rID && $rID1 && $ridUp ){
            oci_commit($con); 
            echo "1**".str_replace("'",'',$update_id);
        }else{
            oci_rollback($con); 
            echo "10**".str_replace("'",'',$update_id);
            }
        disconnect($con);
        die;
	}
}


?>