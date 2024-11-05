<?
/************************************************************************
|	Purpose			:	This Controller is for Field Level Access
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Nuruzzaman 
|	Creation date 	:	26.08.2015
|	Updated by 		:   Md. Didarul Alam		
|	Update date		:   14.08.2016,21.08.2016 
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
*************************************************************************/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
include('../../includes/field_list_array.php');
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
extract($_REQUEST);

$level_arr = array(1=>"PO Level",2=>"Job Level");
/*
|--------------------------------------------------------------------------
| for load_drop_down_item
|--------------------------------------------------------------------------
|
*/
if($action == "load_drop_down_item")
{ 
	// echo $data;die;
	$field_arr = get_fieldlevel_arr($data);
	// print_r($field_arr);die;
	echo create_drop_down("cboFieldId_1", 200, $field_arr, "", 1, "----Select----", 0, "set_hide_data(this.value+'**'+1);", "", "", "", "", "", "", "", "cbo_field_id");
	exit();
} 

/*
|--------------------------------------------------------------------------
| for set_field_name
|--------------------------------------------------------------------------
|
*/
if($action == "set_field_name")
{
	$data_ref = explode("**",$data);
   // echo $data_ref[0]."____".$data_ref[1]. "____".$data_ref[2];die;
	$field_val = $fieldlevel_arr[$data_ref[0]][$data_ref[1]];
 
	/*
	|--------------------------------------------------------------------------
	| for yarn issue page
	|--------------------------------------------------------------------------
	|
	*/

	if($data_ref[0]==1)
	{
		if($data_ref[1]==6)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $currency,"",1,"----Select----",0,"","","","","","","","","" );
		}
		if($data_ref[1]==7)
		{
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
		}	
		
	}
	else if($data_ref[0]==316)
	{
		if($data_ref[1]==1)
		{
			$receive_basis=array(1=>"1st Wash",2=>"Final Wash",3=>"1st Dyeing",4=>"2nd Dyeing");
			echo create_drop_down("setDefaultVal_".$data_ref[2],152,$receive_basis,"",0,"","","");
		}
	}
	else if($data_ref[0]==2)
	{
		// for sales order field
		//echo $data_ref[1].'';
		
		if($data_ref[1]==1)
		{
			$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan",4=>"Sales Order");
			echo create_drop_down("setDefaultVal_".$data_ref[2],152,$receive_basis,"",0,"","","");

			// echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$yes_no,'',1,'-Select-',1,"",0,'');
		}
		else if($data_ref[1]==2)
		{
			
			// echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$yes_no,'',1,'-Select-',1,0,0);
			// echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
		}
	}
	else if($data_ref[0]==3)
	{
		// for sales order field
		//echo $data_ref[1].'';
		if($data_ref[1]==1)
		{

			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$yes_no,'',1,'-Select-',1,"",0,'');
		}
		else if($data_ref[1]==2)
		{
			//echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$yes_no,'',1,'-Select-',1,0,0);
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
		}
		else if($data_ref[1]==4)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $issue_basis,"",1,"----Select----",0,"","","","","","","","","" );
		}
	}
	else if($data_ref[0]==4)
	{
		if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $receive_basis_arr,"",1,"----Select----",0,"","","","","","","","","" );
		}
	}
	else if($data_ref[0]==7)
	{
		if($data_ref[1]==1)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $batch_status_array,"",1,"----Select----",0,"","","","","","","","","" );
		}
	}
	else if($data_ref[0]==16)
	{
		if($data_ref[1]==2)
		{
			$grey_issue_basis=array(1=>"Booking",2=>"Independent",3=>"Knitting Plan");
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $grey_issue_basis,"",1,"----Select----",3,"","","","","","","","","" );
		}
		else
		{
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
		}
	}
	else if($data_ref[0]==17)
	{
		if($data_ref[1]==1){
			$distribiution_method=array(1=>"Proportionately",2=>"Manually");
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $distribiution_method,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==2){
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $receive_basis_arr,"",1,"----Select----",0,"","","1,2,4,6","","","","","","" );
		}
		else if($data_ref[1]==3){
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
		}
	}
	else if($data_ref[0]==19) // Woven Finish Fabric issue // mahbub
	{
		if($data_ref[1]==1){
			$distribiution_method=array(1=>"Proportionately",2=>"Manually");
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $distribiution_method,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==2)
		{
			$woven_issue_basis = array(1=>'Batch Basis',2=>'Requisition Basis');
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $woven_issue_basis,"",1,"----Select----",0,"","","","","","","","","" );
		}
	}
	else if($data_ref[0]==41) // Yarn Dyeing Work Order // Zayed
	{
		if($data_ref[1]==1){
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		}
	}
	else if($data_ref[0]==146) // Stationary Purchase Order
	{
		if($data_ref[1]==1){
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $wo_basis,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"",1,"----Select----",0,"","","","","","","","","" );
		}
	}
	else if($data_ref[0]==18)
	{
		$is_sales_order_arr = array(1=>"Yes", 2 =>"No");
		if($data_ref[1]==1){
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $is_sales_order_arr,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else{
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
		}
	}
	else if($data_ref[0]==20)
	{
		if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $receive_basis_arr,"",1,"----Select----",0,"","","","","","","","","" );
		}
		
	}
	else if($data_ref[0]==21)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==2)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $knitting_source,"",1,"----Select----",0,"","","","","","","","","" );	
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==24)
	{
		//for Received Date field
		if($data_ref[1]==1)
		{
			echo '<input type="text" name="setDefaultVal_'.trim($data_ref[2]).'" id="setDefaultVal_'.trim($data_ref[2]).'" class="text_boxes" style="width:140px;" />';
		}
		if($data_ref[1]==2) //for Challan Date field
		{

			echo '<input type="text" name="setDefaultVal_'.trim($data_ref[2]).'" id="setDefaultVal_'.trim($data_ref[2]).'" class="text_boxes" style="width:140px;" />';
		}
		else if($data_ref[1]==3)
		{
			$payment_yes_no=array(0=>"Yes",1=>"No"); 
			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$payment_yes_no,'',1,'-Select-',1,0,0);
		}
	}
	else if($data_ref[0]==43)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"",1,"----Select----",0,"","","","","","","","","" );	
	}
	else if($data_ref[0]==54)
	{
		$is_sales_arr = array(1=>"Yes", 0 =>"No");
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $is_sales_arr,"",1,"----Select----",0,"","","","","","","","","" );	
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==61)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $knitting_source,"",1,"----Select----",0,"","","","","","","","","" );	
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==62)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $acceptance_time,"",1,"----Select----",0,"","","","","","","","","" );	
		/*else if($data_ref[1]==2)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $fabric_source,"",1,"----Select----",0,"","","","","","","","","" );	*/
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==68)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",0,"----Select----",0,"","","","","","","","","" );	
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==70)
	{
		if($data_ref[1]==1){
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $issue_basis,"",1,"----Select----",0,"","","","","","","","","" );	
		}
		else{
			echo '<input type="text" name="setDefaultVal_'.$data_ref[2].'" id="setDefaultVal_'.$data_ref[2].'" class="text_boxes" style="width:140px;" />';
		}
	}else if($data_ref[0]==87) //====knit============Multiple Job Wise Trims Booking V2==============
	{
		if($data_ref[1]==1)
		{
			$level_arr             = array(1=>"PO Level",2=>"Job Level");
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $level_arr,"",1,"----Select----",0,"","","","","","","","","" );
		}
		
	}
	else if($data_ref[0]==88)
	{
		if($data_ref[1]==10){
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"",1,"----Select----",0,"","","","","","","","","" );	
		}
		else if($data_ref[1]==31){
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $short_booking_type,"",1,"----Select----",0,"","","","","","","","","" );
		}
		//else if($data_ref[1]==11)
			//echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		else{
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
		}
	}
	else if($data_ref[0]==89)
	{
		if($data_ref[1]==10)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"",1,"----Select----",0,"","","","","","","","","" );	
		else
			echo '<input type="text" name="setDefaultVal_'.$data_ref[2].'" id="setDefaultVal_'.$data_ref[2].'" class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==16)
	{
		$grey_issue_basis=array(1=>"Booking",2=>"Independent",3=>"Knitting Plan");
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $grey_issue_basis,"",1,"----Select----",0,"","","","","","","","","" );	
		// else
		// 	echo '<input type="text" name="setDefaultVal_'.$data_ref[2].'" id="setDefaultVal_'.$data_ref[2].'" class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==90)
	{
		if($data_ref[1]==8)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"",1,"----Select----",0,"","","","","","","","","" );	
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==94)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==2)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		else if($data_ref[1]==3)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );	
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	/*
	|--------------------------------------------------------------------------
	| for knitting production page
	|--------------------------------------------------------------------------
	|
	*/
	else if($data_ref[0]==98)
	{
		//for sales order field
		$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan",4=>"Sales Order");
		if($data_ref[1]==42)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no, '', 0, '', 1, '', 0, '');
		}
		else if($data_ref[1]==3){
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $receive_basis,"",1,"----Select----",0,"","","","","","","","","" );	
		}
		else if($data_ref[1]==4){

		}
		else
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no, '', 0, '', 1, 0, 0);
		}
	}
	else if($data_ref[0]==108)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $unit_of_measurement,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==2)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $fabric_source,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==3)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==4)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $item_category,"",1,"----Select----",0,"","","2,3","","","","","","" );
		else if($data_ref[1]==6)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $source,"",1,"----Select----",0,"","","","","","","","","" );
		//else if($data_ref[1]==7)
			//echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==147)
	{
		if($data_ref[1]==1){
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"",1,"----Select----",0,"","","","","","","","","" );
		}
         
		else if($data_ref[1]==3){
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $nature_mode,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==4){
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $profit_center_mode,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else{
			echo '<input type="text" name="vvvsetDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
		}
	}

	else if($data_ref[0]==271)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $unit_of_measurement,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==2)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $fabric_source,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==3)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==4)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $item_category,"",1,"----Select----",0,"","","2,3","","","","","","" );
		else if($data_ref[1]==6)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $source,"",1,"----Select----",0,"","","","","","","","","" );
		else if($data_ref[1]==7)
		{
			$level_arr             = array(1=>"PO Level",2=>"Job Level");
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $level_arr,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==109)
	{
		if($data_ref[1]==1)
		{ 
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150,  "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", 'id,company_name',1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==3)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $currency,"",1,"----Select----",0,"","","","","","","","","" );
		}	
		else if($data_ref[1]==4)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $shipment_mode,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==5)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $sales_order_type_arr,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==6)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, "select id,location_name from lib_location where company_id='$data_ref[3]' and status_active =1 and is_deleted=0 order by location_name","id,location_name",1,"----Select----",0,"","","","","","","","","" );
		}
	}
	else if($data_ref[0]==162 ) {
        if ($data_ref[1] == 2){
            echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"", 1, "-- Select Pay Mode --", "", "", "" );
        }
		else if($data_ref[1]==3)
		echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
	}
	else if($data_ref[0]==163 )
	{
		if($data_ref[1]==1)
			echo '<input type="text" name="setDefaultVal_'.$data_ref[2].'" id="setDefaultVal_'.$data_ref[2].'" class="text_boxes" style="width:140px;" />';
		else if($data_ref[1]==2)
			echo '<input type="text" name="setDefaultVal_'.$data_ref[2].'" id="setDefaultVal_'.$data_ref[2].'" class="text_boxes" style="width:140px;" />';
		else if($data_ref[1]==3)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $design_source_arr,"",1,"----Select----",0,"","","","","","","","","" );
		else if($data_ref[1]==4)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $quality_label,"",1,"----Select----",0,"","","","","","","","","" );
		else if($data_ref[1]==5)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $product_category,"",1,"----Select----",0,"","","","","","","","","" );
		else if($data_ref[1]==6)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1,"----Select----",0,"","","","","","","","","" );
		else if($data_ref[1]==7)
			echo '<input type="text" name="setDefaultVal_'.$data_ref[2].'" id="setDefaultVal_'.$data_ref[2].'" class="text_boxes" style="width:140px;" />';
		else if($data_ref[1]==8)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $packing,"",1,"----Select----",0,"","","","","","","","","" );
		else if($data_ref[1]==9)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $packing,"",1,"----Select----",0,"","","","","","","","","" );
		else
			echo '<input type="text" name="setDefaultVal_'.$data_ref[2].'" id="setDefaultVal_'.$data_ref[2].'" class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==104)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $acceptance_time,"",1,"----Select----",0,"","","","","","","","","" );	
		/*else if($data_ref[1]==2)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $fabric_source,"",1,"----Select----",0,"","","","","","","","","" );	*/
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==106)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $export_item_category,"",1,"----Select----",0,"","","","","","","","","" );
		else if($data_ref[1]==2)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==3)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $export_lc_type,"",1,"----Select----",0,"","","","","","","","","" );
		else if($data_ref[1]==4)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $shipment_mode,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==5)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $contract_source,"",1,"----Select----",0,"","","","","","","","","" );
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==107)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $export_item_category,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==2)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $convertible_to_lc,"",1,"----Select----",0,"","","","","","","","","" );
		else if($data_ref[1]==3)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );	
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==161)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $calculation_basis,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==2)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==184)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $item_category,"",1,"----Select----",0,"","","2,3","","","","","","" );	
		/*else if($data_ref[1]==2)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $fabric_source,"",1,"----Select----",0,"","","","","","","","","" );	*/
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==118)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==3)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $item_category,"",1,"----Select----",0,"","","2,3","","","","","","" );
			else if($data_ref[1]==4)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $unit_of_measurement,"",1,"----Select----",0,"","","1,12,23,27","","","","","","" );
			else if($data_ref[1]==5)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $fabric_source,"",1,"----Select----",0,"","","","","","","","","" );
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==201)
	{
		if($data_ref[1]==1)
		{

			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"",1,"----Select----",0,"","","","","","","","","" );	
		}
		else if($data_ref[1]==2)
		{
			
            $level_arr=array(1=>"PO Level",2=>"Job Level");
                      
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $level_arr,"",1,"----Select----",0,"","","","","","","","","" );	
		}
		/*else if($data_ref[1]==2)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );*/	
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==158 || $data_ref[0]==520)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $unit_of_measurement,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==2)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $item_category,"",1,"----Select----",0,"","","2,3","","","","","","" );
		else if($data_ref[1]==4)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $costing_per,"",1,"----Select----",0,"","","","","","","","","" );
		else if($data_ref[1]==6)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $fabric_source,"",1,"----Select----",0,"","","","","","","","","" );
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==5)//Dyes and Chemical Issue shariar 12006
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $general_issue_purpose,"",1,"----Select----",0,"","","1,2,3,5,8,11,15,18,37,38,43,44,49,53,54,56,57,60,61,63,64,65,66,69,74,79,84,87,88,89,90,91,92,96,97,98","","","","","","" );	
		else if($data_ref[1]==2)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $receive_basis_arr,"",1,"----Select----",0,"","","4,5,7","","","","","","" );
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==404) // Sample AOP With Order // Zayed
	{
		if($data_ref[1]==1){
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		}
	}
	else if($data_ref[0]==125) //Yarn Dyeing Work Order Without Lot// Zayed
	{
		if($data_ref[1]==1){
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		}
	}
	else if($data_ref[0]==425 || $data_ref[0]==521)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $unit_of_measurement,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==2)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $item_category,"",1,"----Select----",0,"","","2,3","","","","","","" );
		else if($data_ref[1]==4)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $costing_per,"",1,"----Select----",0,"","","","","","","","","" );
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==469)
	{
		if($data_ref[1]==1)
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==314)
	{
		if($data_ref[1]==3)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $costing_per,"",1,"----Select----",0,"","","","","","","","","" );		
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==251)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		else if($data_ref[1]==3)	
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		else if($data_ref[1]==6)	
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		else if($data_ref[1]==7)	
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $get_pass_basis,"",1,"----Select----",0,"","","","","","","","","" );
		else if($data_ref[1]==8)
			echo '<input type="text" name="setDefaultVal_'.$data_ref[2].'" id="setDefaultVal_'.$data_ref[2].'" class="text_boxes" style="width:140px;" />';
			else if($data_ref[1]==9)
			echo '<input type="text" name="setDefaultVal_'.$data_ref[2].'" id="setDefaultVal_'.$data_ref[2].'" class="text_boxes" style="width:140px;" />';
			else if($data_ref[1]==10)
			echo '<input type="text" name="setDefaultVal_'.$data_ref[2].'" id="setDefaultVal_'.$data_ref[2].'" class="text_boxes" style="width:140px;" />';
		else 
		echo '<input type="text" name="setDefaultVal_'.$data_ref[2].'" id="setDefaultVal_'.$data_ref[2].'" class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==485)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );	
	}
	else if($data_ref[0]==270)
	{   
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $shipment_mode,"",1,"----Select----",0,"","","","","","","","","" );	
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==282)
	{
		if($data_ref[1]==1 || $data_ref[1]==2 || $data_ref[1]==3 || $data_ref[1]==4 || $data_ref[1]==5 || $data_ref[1]==6 || $data_ref[1]==7 || $data_ref[1]==8 || $data_ref[1]==9 || $data_ref[1]==10 || $data_ref[1]==11 || $data_ref[1]==12 || $data_ref[1]==13 || $data_ref[1]==14 || $data_ref[1]==15 || $data_ref[1]==16 || $data_ref[1]==17 || $data_ref[1]==18 || $data_ref[1]==19 || $data_ref[1]==20 || $data_ref[1]==21 || $data_ref[1]==22  || $data_ref[1]==23 || $data_ref[1]==24)
		    echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
		else
		    echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
	}
	else if($data_ref[0]==255)
	{
		if($data_ref[1]==1)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $currency,"",1,"----Select----",0,"","","","","","","","","" );
		}
	}
	else if($data_ref[0]==405)
	{
		if($data_ref[1]==1)
		    echo '<input type="text" name="setDefaultVal_"'.$data_ref[1].' id="setDefaultVal_"'.$data_ref[1].' class="text_boxes" style="width:140px;" />';
		elseif($data_ref[1]==2)
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
		elseif($data_ref[1]==3)
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[3].' id="setDefaultVal_"'.$data_ref[3].' class="text_boxes" style="width:140px;" />';
		elseif($data_ref[1]==4)
		    echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $source,"",1,"----Select----",0,"","","","","","","","","" );
		elseif($data_ref[1]==5)
		    echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pi_basis,"",1,"----Select----",0,"","","","","","","","","" );
		elseif($data_ref[1]==6)
		    echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $acceptance_time,"",1,"----Select----",0,"","","","","","","","","" );
	}
	else if($data_ref[0]==255 || $data_ref[0]==208 || $data_ref[0]==257 || $data_ref[0]==269)
	{
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );	
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==351)
	{
		if($data_ref[1]==1)
		{

			echo '<input type="text" name="setDefaultVal_'.trim($data_ref[2]).'" id="setDefaultVal_'.trim($data_ref[2]).'" class="text_boxes" style="width:140px;" />';
		}
		else if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150,$breakdown_type,"",1,"----Select----",0,"","","","","","4","","","" );
		}
		else if($data_ref[1]==3)
		{
			echo '<input type="text" name="setDefaultVal_'.trim($data_ref[2]).'" id="setDefaultVal_'.trim($data_ref[2]).'" class="text_boxes" style="width:140px;" />';
		}
		else if($data_ref[1]==4)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150,"select id,team_leader_name from lib_marketing_team where project_type=2 and status_active =1 and is_deleted=0  group by id,team_leader_name order by team_leader_name","id,team_leader_name",1,"----Select----",0,"","","","","","4","","","" );
		}
		else if($data_ref[1]==5)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150,$packing,"",1,"----Select----",0,"","","","","","4","","","" );
		}
		else if($data_ref[1]==6)
		{
			//echo create_drop_down( "setDefaultVal_".$data_ref[2], 150,$packing,"",1,"----Select----",0,"","","","","","4","","","" );
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data_ref[3]' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id,buyer_name", 1, "----Select----", $selected, "" );
		}
		else if($data_ref[1]==7)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150,$blank_array, "", 1, "----Select----", "" );
		}
		else if($data_ref[1]==8)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150,$packing,"",1,"----Select----",0,"","","","","","4","","","" );
		}
		else if($data_ref[1]==9)
		{
			echo '<input type="text" name="setDefaultVal_'.trim($data_ref[2]).'" id="setDefaultVal_'.trim($data_ref[2]).'" class="text_boxes" style="width:140px;" />';
		}
		else if($data_ref[1]==10)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, "select id, season_name from lib_buyer_season where  status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "----Select----", $selected, "" );
		}
	}
	else if($data_ref[0]==365)
	{
		if($data_ref[1]==1)
		{

			echo '<input type="text" name="setDefaultVal_'.trim($data_ref[2]).'" id="setDefaultVal_'.trim($data_ref[2]).'" class="text_boxes" style="width:140px;" />';
		}
		else if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150,$product_category,"",1,"----Select----",0,"","","","","","4","","","" );
		}
		else if($data_ref[1]==3)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150,"select id,team_leader_name from lib_marketing_team where project_type=1 and status_active =1 and is_deleted=0  group by id,team_leader_name order by team_leader_name","id,team_leader_name",1,"----Select----",0,"","","","","","4","","","" );
		}
		else if($data_ref[1]==4)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150,"select id, company_name from lib_company  where status_active =1 and is_deleted=0 order by company_name","id,company_name",1,"----Select----",0,"","","","","","","","","" );
		}
	}
	else if($data_ref[0]==493)
	{
		if($data_ref[1]==1)
		{

			echo '<input type="text" name="setDefaultVal_'.trim($data_ref[2]).'" id="setDefaultVal_'.trim($data_ref[2]).'" class="text_boxes" style="width:140px;" />';
		}
		else if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150,$product_category,"",1,"----Select----",0,"","","","","","4","","","" );
		}
		else if($data_ref[1]==3)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150,$unit_of_measurement,"",1,"----Select----",0,"","","1,57,58","","","4","","","" );
		}
		else if($data_ref[1]==4)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150,$breakdown_type,"",1,"----Select----",0,"","","","","","","","","" );
		}
	}
	else if($data_ref[0]==120)
	{
		if($data_ref[1]==1)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		}
	}
	else if($data_ref[0]==472)
	{
		if($data_ref[1]==1)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150,  "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0  order by comp.company_name", 'id,company_name',1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==3)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $currency,"",1,"----Select----",0,"","","","","","","","","" );
		}	
		else if($data_ref[1]==4)
		{
			//echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $shipment_mode,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==5)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $sales_order_type_arr,"",1,"----Select----",0,"","","","","","","","","" );
		}
		
	}
	else if($data_ref[0]==478)
	{
		if($data_ref[1]==1)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		}
		
	}
	else if($data_ref[0]==480)
	{
		if($data_ref[1]==1)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		}
		
	}
	else if($data_ref[0]==135)
	{
		if($data_ref[1]==1)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==3)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		}
		
	}
	else if($data_ref[0]==350)
	{
		if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $receive_basis_arr,"",1,"----Select----",0,"","","","","","","","","" );
		}
		
	}
	// else if($data_ref[0]==505)
	// {
	// 	if($data_ref[1]==1)
	// 	{
	// 		echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $item_transfer_criteria,"",1,"----Select----",0,"","","1,2,4","","","","","","" );
	// 	}
		
	// }
	else if($data_ref[0]==276)
	{
		if($data_ref[1]==1)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $currency,"",1,"----Select----",0,"","","","","","","","","" );
		}	
		
	}
	else if($data_ref[0]==25)
	{
		$trims_issue_basis=array(1=>"With Order",2=>"Without Order",3=>"Requisition");

		 if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $trims_issue_basis,"",1,"----Select----",0,"","","","","","","","","" );
		}	
		
	}
	else if($data_ref[0]==273)
	{
		if($data_ref[1]==1)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $source,"",1,"----Select----",0,"","","","","","","","","" );
		}	
		
	}
	else if($data_ref[0]==492)
	{
		if($data_ref[1]==1)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $source,"",1,"----Select----",0,"","","","","","","","","" );
		}else if($data_ref[1]==3)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $level_arr,"",1,"----Select----",0,"","","","","","","","","" );
		}	
		
	}
	else if($data_ref[0]==69)
	{
		
		if($data_ref[1]==2)	
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"",1,"----Select----",0,"","","","","","","","","" );
		else
			echo '<input type="text" name="setDefaultVal_'.$data_ref[2].'" id="setDefaultVal_'.$data_ref[2].'" class="text_boxes" style="width:140px;" />';	
		
	}
	else if($data_ref[0]==450)
	{
		
		if($data_ref[1]==1)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $currency,"",1,"----Select----",0,"","","","","","","","","" );
		}	
		
	}
	else if($data_ref[0]==105)
	{
		if($data_ref[1]==1)
		{

			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$pay_term,'',1,'-Select-',0,"",0,'1,2,3,4');
		}
		else if($data_ref[1]==2)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$shipment_mode,'',1,'-Select-',1,0,0);
		}
		else if($data_ref[1]==3)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$incoterm,'',1,'-Select-',1,0,0);
		}
		else if($data_ref[1]==4)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,"SELECT id,country_name from lib_country order by country_name",'id,country_name','',1,'-Select-',1,0,0);
		}
	}
	else if($data_ref[0]==152)
	{
		if($data_ref[1]==1)
		{

			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$yes_no,'',1,'-Select-',1,"",0,'');
		}
		else
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$yes_no,'',1,'-Select-',1,0,0);
		}
	}
	else if($data_ref[0]==363)
	{

		if($data_ref[1]==4)	
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		else
			echo '<input type="text" name="setDefaultVal_'.$data_ref[2].'" id="setDefaultVal_'.$data_ref[2].'" class="text_boxes" style="width:140px;" />';
	}		
	else if($data_ref[0]==496)
	{
		if($data_ref[1]==1)	
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );
		else
			echo '<input type="text" name="setDefaultVal_'.$data_ref[2].'" id="setDefaultVal_'.$data_ref[2].'" class="text_boxes" style="width:140px;" />';

	}
	else if($data_ref[0]==231)
	{
		if($data_ref[1]==1)
		{

			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$yes_no,'',1,'-Select-',1,"",0,'');
		}
		else
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$yes_no,'',1,'-Select-',1,0,0);
		}

	}
	else if($data_ref[0]==204)
	{
		if($data_ref[1]==1)
		{

			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$yes_no,'',1,'-Select-',1,"",0,'');
		}
		else
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $currency,"",1,"----Select----",0,"","","","","","","","","" );
		}

	}
	else if($data_ref[0]==205)
	{
		if($data_ref[1]==3)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$yes_no,'',1,'-Select-',1,"",0,'');
		}
	}
	else if($data_ref[0]==295)
	{
		if($data_ref[1]==1)
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$yes_no,'',1,'-Select-',1,"",0,'');
		}
	}
	else if($data_ref[0]==403)
	{
		if($data_ref[1]==1) echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $pay_mode,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==2)
		{
			
            $level_arr=array(1=>"PO Level",2=>"Job Level");
                      
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $level_arr,"",1,"----Select----",0,"","","","","","","","","" );	
		}
		else if($data_ref[1]==3)
		{                    
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $yes_no,"",1,"----Select----",0,"","","","","","","","","" );	
		}
	}
	else if($data_ref[0]==631)
	{
		if($data_ref[1]==1) echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $receive_basis_arr,"",1,"----Select----",0,"","","1,2","","","","","","" );	
	}
	else if($data_ref[0]==556)
	{
		if($data_ref[1]==1)
		{

			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$yes_no,'',1,'-Select-',0,"",0,'');
		}
	}
	else if($data_ref[0]==634)
	{
		echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $qccosting_per,"",1,"----Select----",0,"","","","","","","","","" );
	}
	/*
	|--------------------------------------------------------------------------
	| for Raw Material Issue Requisition
	|--------------------------------------------------------------------------
	|
	*/
	else if($data_ref[0]==427)
	{
		if($data_ref[1]==1) echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $trims_section,"",1,"----Select----",0,"","","","","","","","","" );
	}
	else if($data_ref[0]==159)
	{
		$productionBasisArr = array(1=>'Sub-Contract Order', 2=>'Sub-Contract Plan');
		if($data_ref[1]==1) echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $productionBasisArr,"",1,"----Select----",0,"","","","","","","","","" );
	}

	else if($data_ref[0]==511)
	{
		if($data_ref[1]==1) echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $qccosting_per,"",1,"----Select----",0,"","","","","","","","","" );
	}

	else if($data_ref[0]==202)
	{
		if($data_ref[1]==1){
		
		}
	}
	else if($data_ref[0]==209)
	{
		if($data_ref[1]==1){
			
		}
	}
	else if($data_ref[0]==258)
	{
		if($data_ref[1]==1){
			
		}
	}
	else if($data_ref[0]==507)
	{
		if($data_ref[1]==1){
			
		}
	}
	else if($data_ref[0]==741)
	{
		if($data_ref[1]==1){
			
		}
	}


	// 121____1____1
	else if($data_ref[0]==121)
	{
		$ProducedBy=array(1=>"Salary Based Worker",2=>"Piece Rate Worker");
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $ProducedBy,"",1,"----Select----",0,"","","","","","","","","" );	
		else if($data_ref[1]==2)
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
		else if($data_ref[1]==4)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $costing_per,"",1,"----Select----",0,"","","","","","","","","" );
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}
	else if($data_ref[0]==418)//Dyeing Work Order
	{
		$wo_basis_arr=array(1=>"Challan no",2=>"FSO Wise");
		if($data_ref[1]==1)
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $wo_basis_arr,"",1,"----Select----",0,"","","","","","","","","" );	
		else
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
	}

	else if($data_ref[0]==296 || $data_ref[0]==297 || $data_ref[0]==303)
	{
		if($data_ref[1]==1)
		{

			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$yes_no,'',1,'-Select-',1,"",0,'');
		}
		else
		{
			echo create_drop_down( "setDefaultVal_".$data_ref[2],150,$yes_no,'',1,'-Select-',1,0,0);
		}
	}
	else if($data_ref[0]==735) // Reference Closing
	{
		//echo 'Reference Closing';die;

		$reference_type = [2=>'Knitting Production'];
		if($data_ref[1]==1){
			echo create_drop_down( "setDefaultVal_".$data_ref[2], 150, $reference_type,"",1,"----Select----",0,"","","","","","","","","" );
		}
		else if($data_ref[1]==2)
		{
			echo '<input type="text" name="setDefaultVal_"'.$data_ref[2].' id="setDefaultVal_"'.$data_ref[2].' class="text_boxes" style="width:140px;" />';
		}
	}
	else
	{
		//echo $data_ref[0]."____".$data_ref[1]. "____".$data_ref[2];die;
		echo '<input type="text" name="setDefaultVal_'.trim($data_ref[2]).'" id="setDefaultVal_'.trim($data_ref[2]).'" class="text_boxes" style="width:140px;" />';
	}
	//echo $data_ref[0]; die;
	?>	
	<input type="hidden" name="txtFieldName[]"  id="txtFieldName_<? echo $data_ref[2];?>" value="<? echo $field_val; ?>" class="text_boxes" style="width:100px;" />
	<input type="hidden" name="hiddenPaymode" id="hiddenPaymode" />
	<?
	exit();
}

if($action=="color_popup")
{
	echo load_html_head_contents("Color Info", "../../", 1, 1,'','','');
	extract($_REQUEST);	
	?> 
	<script>
	
	$(document).ready(function(e) {
        setFilterGrid('tbl_list_search',-1);
		set_all();
    });
	
	 var selected_id = new Array, selected_name = new Array();
		
		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) 
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
        // Keep old selected user id until click on refresh button
		function set_all()
		{
			var old = document.getElementById( 'txt_user_row_id' ).value;          
			if(old !="")
			{   
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{  
					js_set_value( old[i] ) 
				}
			}
		}

		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			 
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id =''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hidden_user_id').val( id );
			$('#hidden_user_name').val( name );
		}
		
    </script>
    <input type="hidden" name="user_id" id="hidden_user_id" value="" />
    <input type="hidden" name="user_name" id="hidden_user_name" value="" />
    <div>	
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="340" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th>User Name</th>
            </thead>
		</table>
		<div style="width:340px; max-height:280px; overflow-y:scroll">
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="320" class="rpt_table" id="tbl_list_search">
            <?php 
				$i=1; $user_row_id=""; $user_id=explode(",",$user_id);
                $nameArray = sql_select( "select id,user_name from user_passwd where valid=1" );
				$i=0;
                foreach ($nameArray as $selectResult)
				{
					$i++;    
                    if ($i%2==0) { 
						$bgcolor="#E9F3FF";
                    } else {
						$bgcolor="#FFFFFF";	
                    } 
                    if(in_array($selectResult[csf('id')],$user_id)) 
					{
						if($user_row_id=="") $user_row_id=$i; else $user_row_id.=",".$i;
					}
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
                        <td width="50" align="center"><?php echo $i; ?>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $selectResult[csf('id')]; ?>"/>	
                            <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i; ?>" value="<?php echo $selectResult[csf('user_name')]; ?>"/>
                        </td>	
                        <td><p><?php echo $selectResult[csf('user_name')];?></p></td>
                    </tr>
                    <?                   
                }
                ?>
               	<input type="hidden" name="txt_user_row_id" id="txt_user_row_id" value="<?php echo $user_row_id;?>"/>	
            </table>
        </div>
        <table width="340" cellspacing="0" cellpadding="0" border="1" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%"> 
                        <div style="width:45%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data(this.checked)" /> Check / Uncheck All
                        </div>
                        <div style="width:55%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>
    <?
	exit();
}

if($action=='save_update_delete')
{
	$process=array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//insert
	if($operation==0)
	{
		$con = connect();
		$id=return_next_id( "id", "field_level_access", 1 ) ; 
		$mst_id=$id;
				
        $user_id = str_replace("'","",$text_user_id);// '132,133,134' 
        $duplicate_sql = "select id from field_level_access where company_id=$cbo_company_name and user_id in($user_id) and page_id=$cbo_page_id and status_active=1"; 
                
        $duplicate_result = sql_select($duplicate_sql); 
                
        if(count($duplicate_result)>0)
        {			
            $rID = execute_query("delete from field_level_access where company_id=$cbo_company_name and user_id in ($user_id) and page_id=$cbo_page_id",1);	
        }
        
        for($i=1;$i<=$total_row;$i++)
		{
			$cboFieldId="cboFieldId_".$i;
			
			if($i!=1)
				$duplicate_sql.="  or  field_id= ".$$cboFieldId."";
			else
				$duplicate_sql.=" and ( field_id=".$$cboFieldId ."";
		}
		$duplicate_sql.=" )";
		$duplicate_result=sql_select($duplicate_sql);
		foreach($duplicate_result as $row){
			$key=$row[csf('user_id')].$row[csf('page_id')].$row[csf('field_id')];
			$duplicateFillArr[$key]=$row[csf('field_id')];	
		}
		
		if(str_replace("'","",$update_id)=="")
		{
			$field_array="id,mst_id,company_id,user_id,page_id,field_id,field_name,is_disable,defalt_value,inserted_by,insert_date";
			
            $user_ids = explode(',',$user_id);
            $data_array="";$add_comm=0;
            foreach ($user_ids as $userId) {                           
                for($i=1;$i<=$total_row;$i++)
                {             
                    $cboFieldId="cboFieldId_".$i;
                    $txtFieldName="txtFieldName_".$i;
                    $cboIsDisable="cboIsDisable_".$i;
                    $setDefaultVal="setDefaultVal_".$i;
                    
					$key = str_replace("'",'',$userId).str_replace("'",'',$cbo_page_id).str_replace("cboFieldId_","",$cboFieldId); 
					if($duplicateFillArr[$key]!=str_replace("cboFieldId_","",$cboFieldId) && $$cboFieldId !=0){							
						//if ($i!=1) $data_array .=",";
						if ($add_comm!=0) $data_array .=",";
						$data_array	.="(".$id.",".$mst_id.",".$cbo_company_name.",".$userId.",".$cbo_page_id.",'".$$cboFieldId."','".$$txtFieldName."','".$$cboIsDisable."','".$$setDefaultVal."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";						
                        $id++;$add_comm++;
                    }                  
                }                            
            }          
		}
       	
		if ($data_array	!='') {
            $rID = sql_insert("field_level_access",$field_array,$data_array,1);
        } 
		else {
             echo "20**Duplicate Field Name Not Allow in The Same Page.";
             die;
		} 
		//echo $rID; die;
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".$mst_id."**".str_replace("'","",$cbo_company_name)."**".str_replace("'","",$text_user_id)."**".str_replace("'","",$cbo_page_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "0**".$mst_id."**".str_replace("'","",$cbo_company_name)."**".str_replace("'","",$text_user_id)."**".str_replace("'","",$cbo_page_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$text_user_id;
			}
		}
		disconnect($con);
		die;		
	}
	// update
	if($operation==1)
	{
		$con = connect();
		$id=return_next_id( "id", "field_level_access", 1 ) ; 
		$mst_id=$id;

		$all_user_id= '';
		for($i=1;$i<=$total_row;$i++)
        {
        	$cboUserId="cboUserId_".$i;

        	if($$cboUserId!=0)
        	{
        		if($all_user_id!='')
	        	{
	        		$all_user_id .=",";
	        	}
	        	$all_user_id .= $$cboUserId;

        	}
        }

        $all_user_id = explode(",",$all_user_id);

        $user_id_arr = array_unique($all_user_id);

        $userId = $_SESSION['logic_erp']['user_id'];
		$con = connect();

		//fnc_tempengine("GBL_TEMP_ENGINE", $userId, 2000, 100,$user_id_arr, $empty_arr);
	
        $user_id = str_replace("'", "", $text_user_id); // '132,133,134' 
        $duplicate_sql = "select a.id from field_level_access a where a.company_id=$cbo_company_name  and a.USER_ID=$user_id and a.page_id=$cbo_page_id and a.status_active=1"; 
		// echo $duplicate_sql;die;
  
        $duplicate_result = sql_select($duplicate_sql); 
          
        if(count($duplicate_result)>0)
        {			
            $rID = execute_query("delete  from field_level_access a where a.company_id=$cbo_company_name  and a.USER_ID=$user_id and a.page_id=$cbo_page_id and a.status_active=1",1);	
        }

        for($i=1;$i<=$total_row;$i++)
		{
			$cboFieldId="cboFieldId_".$i;
			
			if($i!=1)
				$duplicate_sql.="  or  field_id= ".$$cboFieldId."";
			else
				$duplicate_sql.=" and ( field_id=".$$cboFieldId ."";
		}
		$duplicate_sql.=" )";
		$duplicate_result=sql_select($duplicate_sql);
		foreach($duplicate_result as $row){
			$key=$row[csf('user_id')].$row[csf('page_id')].$row[csf('field_id')];
			$duplicateFillArr[$key]=$row[csf('field_id')];	
		}

		//$ird=execute_query("delete from GBL_TEMP_ENGINE where user_id=$userId and entry_form =2000 and ref_from=100");
		
		if($ird)
		{
			oci_commit($con);
		}
		
		//if(str_replace("'","",$update_id)!="")
		//{
		$field_array="id,mst_id,company_id,user_id,page_id,field_id,field_name,is_disable,defalt_value,updated_by,update_date";
		
		$user_ids = explode(',',$user_id);
		$data_array="";$add_comm=0;
		//foreach ($user_ids as $userId) {                           
		for($i=1;$i<=$total_row;$i++)
		{
			$cboFieldId="cboFieldId_".$i;
			$txtFieldName="txtFieldName_".$i;
			$cboIsDisable="cboIsDisable_".$i;
			$setDefaultVal="setDefaultVal_".$i;
			$cboUserId="cboUserId_".$i;

			$key = str_replace("'",'',$$cboUserId).str_replace("'",'',$cbo_page_id).str_replace("cboFieldId_","",$cboFieldId); 
			if($duplicateFillArr[$key]!=str_replace("cboFieldId_","",$cboFieldId) && $$cboFieldId !=0 && $$cboUserId !=0){							
				//if ($i!=1) $data_array .=",";
				if ($add_comm!=0) $data_array .=",";
				$data_array	.="(".$id.",".$mst_id.",".$cbo_company_name.",".$$cboUserId.",".$cbo_page_id.",'".$$cboFieldId."','".$$txtFieldName."','".$$cboIsDisable."','".$$setDefaultVal."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";						
				$id++;$add_comm++;
			}
		}
            //}  
		    // print_r($data_array);die;
            // echo "insert into field_level_access $field_array values $data_array";die;
		$rID = sql_insert("field_level_access", $field_array, $data_array,1);
			// mysql_query("COMMIT");
			// echo $rID;  die;
		//}
		

		//print_r($data_array);die;
		//echo $update_id;die;
       	
		// if ($data_array	!='') {
        //     $rID = sql_insert("field_level_access",$field_array,$data_array,1);
        //     //echo "10**INSERT INTO field_level_access (".$field_array.") VALUES ".$data_array.""; die;
        // } 
		// else {
        //      echo "20**Duplicate Field Name Not Allow in The Same Page.";
        //      die;
		// } 
		
		if($rID)
		{
			oci_commit($con);   
			echo "1**".$mst_id."**".str_replace("'","",$cbo_company_name)."**".str_replace("'","",$text_user_id)."**".str_replace("'","",$cbo_page_id);
		}
		else
		{
			oci_rollback($con);
			echo "10**".$text_user_id;
		}
		disconnect($con);
		die;		
		
	}
	
	//delete
	if($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$user_id = str_replace("'","",$text_user_id);// '132,133,134' 
		$rID = execute_query("delete from field_level_access where company_id=$cbo_company_name and USER_ID=$user_id and page_id=$cbo_page_id", 1);
		//$rID = execute_query("delete from field_level_access where mst_id = $update_id",1);		
		if($rID)
		{
			oci_commit($con);   
			echo "2**".$cbo_user_id;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$cbo_user_id;
		}
		disconnect($con);
		die;	
	}
}

if($action=='action_user_data')
{
	// echo "su..re";die;
	$data_ref = explode("**",$data);
	$com_id = $data_ref[0];
	$user_id = $data_ref[1];
	$page_id = $data_ref[2];

	$user_id_arr = explode(",",$user_id);
	$user_id_arr = array_unique($user_id_arr);

	$userId = $_SESSION['logic_erp']['user_id'];
	
	$con = connect();
	execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$userId and entry_form =2000 and ref_from=100");
	oci_commit($con);

	fnc_tempengine("GBL_TEMP_ENGINE", $userId, 2000, 100, $user_id_arr, $empty_arr);

	$sql = "SELECT a.id, a.mst_id, a.field_id, a.field_name, a.is_disable, a.defalt_value, a.user_id from field_level_access a, GBL_TEMP_ENGINE b where a.company_id=$com_id and a.user_id=b.ref_val and b.entry_form=2000 and b.ref_from=100 and b.user_id=$userId and a.page_id=$page_id and a.status_active=1 and a.is_deleted=0";
	// echo $sql;die;
	$array = sql_select($sql);

	// print_r($array);die;

	execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$userId and entry_form =2000 and ref_from=100");
	oci_commit($con);
	disconnect($con);

	$field_arr=get_fieldlevel_arr($page_id);
	$str='0';
	$i=0;
	if(count($array)>0)
	{
		foreach($array as $row)
		{
			if($i==0)
				$str=$row[csf("id")]."*".$row[csf("mst_id")]."*".$row[csf("field_id")]."*".$row[csf("field_name")]."*".$row[csf("is_disable")]."*".$row[csf("defalt_value")]."*".$row[csf("user_id")];
			else
				$str .="@@".$row[csf("id")]."*".$row[csf("mst_id")]."*".$row[csf("field_id")]."*".$row[csf("field_name")]."*".$row[csf("is_disable")]."*".$row[csf("defalt_value")]."*".$row[csf("user_id")];
			$i++;
		}
	}
	echo "$('#txt_update_data_dtls').val('".$str."');\n";
	exit();
	
/*    $i=1;
	//$field_arr=get_fieldlevel_arr($page_id);
	if(count($array)>0)
	{
		foreach($array as $row)
		{
			if(count($array)==$i) $disable_anable=""; else $disable_anable=" display: none";
			?>
			<tr>
				<td align="center" id="fieldtd">
				  <? echo create_drop_down("cboFieldId_".$i,200,$field_arr,"",1,"----Select----",$row[csf("field_id")],"set_hide_data(this.value+'**'+". $i . ");","","","","","","",""); ?>
				</td>
				<td align="center">
				 <? echo create_drop_down("cboIsDisable_".$i,150,$yes_no,"",1,"-- Select --",$row[csf("is_disable")],"","","","","","","",""); ?> 
				 <input type="hidden" id="txtFieldName_<? echo $i;?>" name="txtFieldName[]" style="width:100px;" value="<? echo $row[csf("field_name")]; ?>" />  
				</td>
				<td align="center" id="tdId_<? echo $i; ?>">
                <?
				if($page_id==108)
				{
					if($row[csf("field_id")]==1)
					{
						echo create_drop_down("setDefaultVal_".$i,150,$unit_of_measurement,"",1,"-UOM-", $row[csf("defalt_value")],"","","1,12,23,27","","","","","");
						}
						else
						{
							echo create_drop_down("setDefaultVal_".$i,150,$fabric_source,"",1,"-Fabric Source-", $row[csf("defalt_value")],"","","","","","","","");

						}
					//echo create_drop_down("setDefaultVal_".$i,150,$row[csf("defalt_value")],"",1,"----Select----", "","","","","","","","","");
				}
				else
				{
				?>
	                  <input type="text" id="setDefaultVal_<? echo $i;?>" name="" style="width:100px" class="text_boxes" value="<? echo $row[csf("defalt_value")]; ?>" />
				 <?
				}
	            ?>
                	<input type="hidden" id="hideDtlsId_<? echo $i;?>" name="hideDtlsId[]" style="width:100px;" value="<? echo $row[csf("id")]; ?>" /> 
                </td>
				<td align="center" id="increment_<? echo $i;?>">
				<input style="width:30px; <? echo $disable_anable; ?>" type="button" id="incrementfactor_<? echo $i;?>" name="incrementfactor_<? echo $i;?>"  class="formbutton" value="+" onClick="add_factor_row(<? echo $i;?>)"/>
				<input style="width:30px; <? echo $disable_anable; ?>" type="button" id="decrementfactor_<? echo $i;?>" name="decrementfactor_<? echo $i;?>"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i;?>)"/>&nbsp;
				</td>
			</tr>
			<?
			$i++;
		}
		?>
        <input type="hidden" id="button_status_check" value="1" />
        <input type="hidden" id="update_id" name="update_id" class="text_boxes" value="<? echo $array[0][csf("mst_id")];?>" readonly />
        <?
	}
	else
	{
		?>

        <tr>
            <td align="center" id="fieldtd">
              <? echo create_drop_down("cboFieldId_".$i,200,$field_arr,"",1,"----Select----","","set_hide_data(this.value+'**'+1)","","","","","","",""); ?>
            </td>
            <td align="center">
             <? echo create_drop_down("cboIsDisable_".$i,150,$yes_no,"",1,"-- Select --",0,"","","","","","","",""); ?> 
             <input type="hidden" id="txtFieldName_<? echo $i;?>" name="txtFieldName[]" style="width:100px;" />  
            </td>
            <td align="center" id="tdId_<? echo $i; ?>">
           
              <input type="text" id="setDefaultVal_<? echo $i;?>" name="" style="width:100px" class="text_boxes" />
           
              <input type="hidden" id="hideDtlsId_<? echo $i;?>" name="hideDtlsId[]" style="width:100px;" value="" /> 
            </td>
            <td align="center" id="increment_<? echo $i;?>">
            <input style="width:30px;" type="button" id="incrementfactor_<? echo $i;?>" name="incrementfactor_<? echo $i;?>"  class="formbutton" value="+" onClick="add_factor_row(<? echo $i;?>)"/>
            <input style="width:30px;" type="button" id="decrementfactor_<? echo $i;?>" name="decrementfactor_<? echo $i;?>"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i;?>)"/>&nbsp;
            </td>
        </tr>
        <input type="hidden" id="button_status_check" value="0" />
        <input type="hidden" id="update_id" name="update_id" class="text_boxes" readonly />
        <?
			//echo create_drop_down("setDefaultVal_".$i,150,$unit_of_measurement,"",1,"-UOM-", "","","","1,12,23,27","","","","","");
			//echo create_drop_down("setDefaultVal_".$i,150,$fabric_source,"",1,"-Fabric Source-", "","","","","","","","",""); 


	}
	exit();*/
	
	 
 }
?>