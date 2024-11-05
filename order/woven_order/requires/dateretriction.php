<? 
if ($operation==0 && trim($cbo_company_name,"'")==3 && trim($cbo_buyer_name,"'")!=3){
	$MinOrgShipmentDate=date("Y-m-d",strtotime("2016-02-01"));
	$dd=change_date_format(trim($txt_org_shipment_date,"'"),"yyyy-mm-dd","-");
	$currentOrgShipmentDate2=date("Y-m-d",strtotime($dd));
	if( $MinOrgShipmentDate > $currentOrgShipmentDate2){
		echo "12**0"; 
		die;
	}
}
else if($operation==1 && trim($cbo_company_name,"'")==3 && trim($cbo_buyer_name,"'")!=3){
	$MinOrgShipmentDate=date("Y-m-d",strtotime("2016-02-01"));
	$dd=change_date_format(trim($txt_org_shipment_date,"'"),"yyyy-mm-dd","-");
	$currentOrgShipmentDate2=date("Y-m-d",strtotime($dd));
	$oldOrgShipmentDate=return_field_value("shipment_date", "wo_po_break_down", "id=$update_id_details");
	$oldOrgShipmentDate=date("Y-m-d",strtotime($oldOrgShipmentDate));
	/*if($MinOrgShipmentDate > $oldOrgShipmentDate && $MinOrgShipmentDate > $currentOrgShipmentDate2){
	}*/
	if($MinOrgShipmentDate < $oldOrgShipmentDate && $MinOrgShipmentDate > $currentOrgShipmentDate2){
		echo "12**0"; 
		die;
	}
}
?>