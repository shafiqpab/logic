<? 
$MinOrgShipmentDate=date("Y-m-d",strtotime("2016-02-01"));
$dd=change_date_format(trim($txt_country_ship_date,"'"),"yyyy-mm-dd","-");
$currentOrgShipmentDate2=date("Y-m-d",strtotime($dd));
$cbo_company_name_z=return_field_value("company_name", "wo_po_details_master", "job_no='$txt_job_no'");
$cbo_buyer_name_z=return_field_value("buyer_name", "wo_po_details_master", "job_no='$txt_job_no'");
//echo "select ";
if ($operation==0 && $cbo_company_name_z==3 && $cbo_buyer_name_z!=3){
	if( $MinOrgShipmentDate > $currentOrgShipmentDate2){
		echo "12**0"; 
		die;
	}
}
else if($operation==1 && $cbo_company_name_z==3 && $cbo_buyer_name_z!=3){
		$oldOrgShipmentDate=return_field_value("country_ship_date", "wo_po_color_size_breakdown", "po_break_down_id=$order_id and country_id=$hid_old_country");
		$oldOrgShipmentDate=date("Y-m-d",strtotime($oldOrgShipmentDate));
		
		/*if($MinOrgShipmentDate > $oldOrgShipmentDate && $MinOrgShipmentDate > $currentOrgShipmentDate2){
		}*/
		
		if($MinOrgShipmentDate < $oldOrgShipmentDate && $MinOrgShipmentDate > $currentOrgShipmentDate2){
			echo "12**0"; 
			die;
		}
}
?>