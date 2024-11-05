<?php
//******************************************************************************************** please DO NOT CHANGE BELOW ARRAYS
$blank_array = array(); // for blank Drop Down or empty drop down
$booking_type = array(1 => 'FB', 2 => 'TB', 3 => 'ServiceB', 4 => 'Sample B', 5 => 'Trim Sample', 6 => 'embellishment Booking', 7 => 'Dia Wise Fabric Booking');
$mod_permission_type = array(0 => "Selective Permission", 1 => "Full Permission", 2 => "No Permission");
$form_permission_type = array(1 => "Permitted", 2 => "Not Permitted");
$row_status = array(1 => "Active", 2 => "InActive", 3 => "Cancelled");
$knitting_program_status = array(1 => "Waiting", 2 => "Running", 3 => "Stop", 4 => "Closed");
//$knitting_program_status=array(1=>"Running",2=>"Waiting",3=>"Stop");
$project_type_arr=array(1=>"Knit", 2=>"Woven", 3=>"Trims", 4=>"Spinning", 5=>"AOP", 6=>"Sweater", 7=>"Wash", 8=>"Printing", 9=>"Embroidery");
$year_closing_ref_arr=array(1=>"Store Wise", 2=>"Floor Wise", 3=>"Room Wise", 4=>"Rack Wise", 5=>"Shelf Wise", 6=>"Bin/Box Wise", 7=>"Order Wise", 8=>"Sales Order Wise");


$attach_detach_array = array(1 => "Attach", 0 => "Detach");
$planning_status = array(1 => "Pending", 2 => "Planning Done", 3 => "Requisition Done", 4 => "Demand Done");
$yes_no = array(1 => "Yes", 2 => "No"); //2= Deleted,3= Locked
$is_approved = array(1 => "Yes", 2 => "No", 3 => "Partial Approved"); //New array as per Monzu vai

$approval_type_arr = array(0 => "Un-Approved", 1 => "Approved");
$approval_necessity_array = array(1 => "Price Quotation", 2 => "Component Wise Pre-Costing - Fabric", 3 => "Component Wise Pre-Costing - Trims", 4 => "Component Wise Pre-Costing - Embellishment", 5 => "Fabric Booking", 6 => "Short Fabric Booking", 7 => "Sample Fabric Booking - With Order", 8 => 'Sample Fabric Booking - Without Order', 9 => 'Trims Booking', 10 => 'Short Trims Booking', 11 => 'Sample Trims Booking - With Order', 12 => 'Sample Trims Booking - Without Order', 13 => 'Purchase Requisition', 14 => 'Yarn Purchase Requisition', 15 => 'Yarn Purchase Order', 16 => 'Stationery Purchase Order', 17 => 'Fabric Sales Order', 18 => 'Pro Forma Invoice', 19 => 'Yarn Delivery Challan', 20 => 'Dyeing Batch', 21 => 'Dyes and Chemical Purchase Order', 22 => 'Other General Item Purchase Order', 23 => 'Store Issue Requisition', 24 => 'Yarn Dyeing Work Order', 25 => 'Pre-Costing', 26 => 'Sample Requisition', 27 => 'Service Booking For Knitting', 28 => 'Quick Costing', 29 => 'Commercial Office Note');
asort($approval_necessity_array);
$delivery_status = array(1 => "Full Pending", 2 => "Partial Deliverd", 3 => "Full Deliverd");// as per Nazim
$fabric_finishing_previous_process = array(1 => "After Brush", 2 => "After Peach", 3 => "Chemical Finish", 4 => "After AOP"); // as per Rehan

$months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
$months_short = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');

$year = array(2010 => '2010', 2011 => '2011', 2012 => '2012', 2013 => '2013', 2014 => '2014', 2015 => '2015', 2016 => '2016', 2017 => '2017', 2018 => '2018', 2019 => '2019', 2020 => '2020', 2021 => '2021', 2022 => '2022', 2023 => '2023', 2024 => '2024');

$dec_place = array(1 => '2', 2 => '2', 3 => '8', 4 => '2', 5 => '4', 6 => '0', 7 => '2');
//1=qnty(kg),2=qnty(yds),3=Rate,4=Amount(local Currency/Taka), 5=Foreign Currency, 6=Gmts qnty, 7=%(percentage)

$all_cal_day = array(1 => "Saturday", 2 => "Sunday", 3 => "Monday", 4 => "Tuesday", 5 => "Wednesday", 6 => "Thursday", 7 => "Friday");

$integrated_project_list = array(1 => "Platform", 2 => "HRM System", 3 => "Acounts");
$string_search_type = array(1 => "Exact", 2 => "Starts with", 3 => "Ends with", 4 => "Contents");
$calculation_basis = array(1 => "Order Qty", 2 => "Plan Cut Qty");
$dyeing_re_process = array(1 => "Topping", 2 => "Adding", 3 => "Stripping");
$pi_status = array(2 => "All", 1 => "Approved", 0 => "Un Approved");
$project_list = array(1 => "Accounts", 2 => "HRM System", 3 => "Trims ERP", 4 => "Buying House ERP");
// Plannig Board Parameter
$smv_basis = array(1 => "Non-Calculative", 2 => "Calculative");
$line_shape_arr = array(1 => "Straight Line Single", 2 => "Straight Line Double", 3 => "U-Shape");
//******************************************************************************************** please DO NOT CHANGE UPPER ARRAYS
$commercial_invoice_format = array(1 => "Invoice F-1", 2 => "Invoice F-2", 3 => "Invoice F-3", 4 => "Invoice F-4", 5 => "Invoice F-5", 6 => "Invoice F-6", 7 => "Invoice F-7", 8 => "Invoice F-8", 9 => "Invoice F-9", 10 => "Invoice F-10", 11 => "Invoice F-11", 12 => "Invoice F-12", 13 => "Invoice F-13", 14 => "Invoice F-14", 15 => "Invoice F-15", 16 => "Invoice F-16", 17 => "Invoice F-17", 18 => "Invoice F-18", 19 => "Invoice F-19", 20 => "Invoice F-20"); //As per Monzu

// common for All Module //
$currency = array(1 => "Taka", 2 => "USD", 3 => "EURO", 4 => "CHF", 5 => "SGD", 6 => "Pound", 7 => "YEN");


// Library Module //
$user_type = array(1 => "General User", 2 => "Admin User", 3 => "Demo User");
$mail_user_type = array('1' => 'Management', '2' => 'Marketing', '3' => 'General');

$get_upto = array(1 => "Greater Than", 2 => "Less Than", 3 => "Greater/Equal", 4 => "Less/Equal", 5 => "Equal");

$knit_defect_array = array(1 => "Hole", 5 => "Loop", 10 => "Press Off", 15 => "Lycra Out", 20 => "Lycra Drop", 21 => "Lycra Out/Drop", 25 => "Dust", 30 => "Oil Spot", 35 => "Fly Conta", 40 => "Slub", 45 => "Patta", 50 => "Needle Break", 55 => "Sinker Mark", 60 => "Wheel Free", 65 => "Count Mix", 70 => "Yarn Contra", 75 => "NEPS", 80 => "Black Spot", 85 => "Oil/Ink Mark", 90 => "Set up", 95 => "Pin Hole", 100 => "Slub Hole", 105 => "Needle Mark", 110 => "Miss Yarn", 115 => "Color Contra [Yarn]", 120 => "Color/dye spot", 125 => "friction mark", 130 => "Pin out", 135 => "softener spot", 140 => "Dirty Spot", 145 => "Rust Stain", 150 => "Stop mark", 155 => "Compacting Broken", 160 => "Insect Spot", 165 => "Grease spot", 166 => "Knot", 167 => "Tara");

$knit_defect_short_array = array(1 => "H", 5 => "L", 10 => "POF", 15 => "LYO", 20 => "LYD", 21 => "LO", 25 => "DU", 30 => "OS", 35 => "FC", 40 => "SL", 45 => "PA", 50 => "NB", 55 => "SM", 60 => "WF", 65 => "CM", 70 => "YCO", 75 => "NEPS", 80 => "BS", 85 => "OIM", 90 => "SU", 95 => "PH", 100 => "SH", 105 => "NM", 110 => "MY", 115 => "YC", 120 => "DS", 125 => "FR", 130 => "PO", 135 => "SP", 140 => "D", 145 => "SR", 150 => "STM", 155 => "CB", 160 => "IS", 165 => "GS");

$knit_defect_inchi_array = array(1 => 'Defect=<3" : 1', 2 => 'Defect=<6" but >3" : 2', 3 => 'Defect=<9" but >6" : 3', 4 => 'Defect>9" : 4', 5 => 'Hole<1" : 2', 6 => 'Hole>1" : 4');
$foc_claim_arr=array(1=>"FOC",2=>"Claim");

$sample_type = array(2 => "PP", 3 => "FIT", 4 => "Size Set", 5 => "Others", 6 => "Development", 7 => "Production", 8 => "Tag", 9 => "Photo", 10 => "Packing", 11 => "Final", 12 => "Proto", 13 => "Counter", 14 => "SMS");
asort($sample_type);
$trims_production_module = array(1 =>"Production Update Areas", 2 =>"Last Process production Qty Control");
$trims_production_update_areas = array(1 => "Item Level", 2 => "PO Level", 3 => "Color and Size Level");

$trim_type = array(1 => "Sewing", 2 => "Packing/Finishing");
$production_module = array(1 => "Production Update Areas", 2 => "Sewing Piece Rate WQ Limit", 3 => "Fabric in Roll Level", 4 => "Fabric in Machine Level", 13 => "Batch Maintained", 15 => "Auto Fabric Store Update", 23 => "Production Resource Allocation", 24 => "Auto Batch No Creation", 25 => "SMV Source For Efficiency", 26 => "Sewing Production Start", 27 => "Barcode Generation", 28 => "Production Update for Reject Qty", 29 => "Cutting Piece Rate WQ Limit", 30 => "Piece Rate Safety %", 31 => "Booking Approval needed for Knitting Plan", 32 => "Cut Panel delv. Basis", 33 => "Last Process Prod. Qty Control", 34 => "Process Costing Maintain", 35 => "Fabric Production Control", 36 => "Grey Fabric Grouping", 37 => "Bundle No Creation", 38 => "Cut and Lay Roll Wise Batch no", 39 => "RMG No Creation", 40 => "Service Rate Source", 41 => "Working Company Mandatory", 42 => "Qty Source for Poly Entry", 43 => "Qty Source for Packing And Finishing", 44 => "Fabric Source for Batch", 45 => "Finish Fabric Grouping", 46 => "Process Costing Rate Basis for Knitting", 47 => "Auto Production quantity update by QC",48 => "Mandatory QC For Delivery",49 => "Roll Weight Control",50 => "Last Process Prod. Qty Control Sweater", 51=>"Fabric Production Over Control", 52=>"Textile business concept" );
asort($production_module);
$textile_business_concept = array(1 => "Composite", 2 => "Textile", 3 => "Both");
$finish_qc_defect_array=array(1=>"Hole", 5=>"Color/Dye Spot", 10=>"Insect Spot", 15=>"Yellow Spot", 20=>"Poly Conta", 25=>"Dust", 30=>"Oil Spot", 35=>"Fly Conta", 40=>"Slub", 45=>"Patta/Barrie Mark", 50=>"Cut/Joint", 55=>"Sinker Mark", 60=>"Print Mis", 65=>"Yarn Conta", 70=>"Slub Hole", 75=>"Softener Spot",     95=>"Dirty Stain", 100=>"NEPS", 105=>"Needle Drop", 110=>"Chem: Stain", 115=>"Cotton seeds", 120=>"Loop hole", 125=>"Dead Cotton", 130=>"Thick & Thin", 135=>"Rust Spot", 140=>"Needle Broken Mark", 145=>"Dirty Spot", 150=>"Side To Center Shade", 155=>"Bowing", 160=>"Uneven", 165=>"Yellow Writing", 170=>"Fabric Missing", 175=>"Dia Mark", 180=>"Miss Print", 185=>"Hairy", 190=>"G.S.M Hole", 195=>"Compacting Mark", 200=>"Rib Body Shade", 205=>"Running Shade", 210=>"Plastic Conta", 215=>"Crease mark", 220=>"Patches", 225=>"M/c Stoppage", 230=>"Needle Line", 235=>"Crample mark", 240=>"White Specks", 245=>"Mellange Effect", 250=>"Line Mark", 255=>"Loop Out", 260=>"Needle Broken",261=>"Loop",262=>"Oil Spot/Line",263=>"Lycra Out/Drop",264=>"Miss Yarn",265=>"Color Contra [Yarn]",266=>"Friction Mark",267=>"Pin Out",268=>"Rust Stain",269=>"Stop Mark",270=>"Compacting Broken",271=>"Grease Spot",272=>"Cut Hole",273=>"Snagging/Pull Out"  ); //AS Per Rehan


$aop_orde_type = array(1 => "Flat", 2 => "Rotary", 3 => "Flat and Rotary");  //AS Per Mahbub
$aop_work_order_type = array(1 => "Main", 2 => "Sample", 3 => "Subcontract"); //AS Per Mahbub


$subcon_variable = array(1 => "Dyeing & Finishing Bill Qty", 2 => "Knitting Fabric From Yarn Count Det.", 3 => "Bill Rate", 4 => "SubCon Batch Fabric Source", 5 => "Fabric in Roll Level", 6 => "Barcode Generation", 7 => "In-House Knit Bill From", 8 => "In-House Finishing Bill From", 9 => "Knitting In-House", 10 => "Knitting Out-Bound", 11 => "Dyeing & Finishing In-House", 12=> "Dyeing & Finishing Out-Bound", 13=> "AOP Master Batch", 14=> "Mandatory For AOP QC Entry");

$trims_sub_section = array(1 => "Crochet", 2 => "Jacquard", 3 => "Covering Rubber", 4 => "Printed label", 5 => "Label Screen Print", 6 => "PP", 7 => "PE", 8 => "HDPE", 9 => "PVC", 10 => "BOPP", 11 => "ZIP Lock", 12 => "Tag Pin", 13 => "Lock Pin", 14 => "Polyester", 15 => "Nylon", 16 => "LDPE", 17 => "LLDPE", 18 => "Woven Label"); //AS per Nazim

$dyeing_finishing_bill = array(1 => "On Grey Qty", 2 => "On Delivery Qty");
$production_update_areas = array(1 => "Gross Quantity Level", 2 => "Color Level", 3 => "Color & Size Level");
$smv_adjustment_head = array(1 => "Extra Hour", 2 => "Lunch Out", 3 => "Sick Out", 4 => "Leave Out", 5 => "Late In");
$wash_operation_arr = array(1 => "1st Wash", 2 => "Final Wash", 3 => "1st Dyeing", 4 => "2nd Dyeing");
$wash_sub_operation_arr = array(1 => "Towel Wash", 2 => "Acid Wash", 3 => "Tie Dye", 4 => "Cool Dye" , 5 => "Deep Dye" );
$wash_gmts_type_array = array(1 => "Denim Garments", 2 => "Twill Garments", 3 => "Dyeing Garments", 4 => "Woven Garments", 5 => "Knit Garments");

$nature_of_buyer_claim=array(1=>"Fabric Quality",2=>"Workmanship",3=>"Measurements",4=>"Colour Shading",5=>"Lab Results Non Conformed",6=>"Labelling Non Conformed",7=>"Packing Non Conformed",8=>"Late Shipment",9=>"Part/Short/Over Shipment",10=>"Chemical Substances",11=>"Quality of Supplies and Accessories",12=>"Embellishment/Print Quality",99=>"Others");
$buyer_claim_inspected_by=array(1=>"Local Office",2=>"Buying House",3=>"3rd Party",4=>"Warehouse");//As Per Kausar



$export_item_category = array(1 => "Knit Garments", 2 => "Woven Garments", 3 => "Sweater Garments", 4 => "Leather Garments", 10 => "Knit Fabric ", 11 => "Woven Fabric", 20 => "Knitting", 21 => "Weaving", 22 => "Dyeing & Finishing", 23 => "All Over Printing", 24 => "Fabric Washing", 30 => "Cutting", 31 => "Sewing", 35 => "Gmts Printing", 36 => "Gmts Embroidery", 37 => "Gmts Washing", 40 => "Yarn", 45 => "Accessories", 50 => "Chemical", 51 => "Dyes", 55 => "Food Item", 60 => "Medicine", 65 => "Transportation", 66 => "C & F");

/*$item_category=array(1=>"Yarn",2=>"Knit Finish Fabrics",3=>"Woven Fabrics",4=>"Accessories",5=>"Chemicals",6=>"Dyes",7=>"Auxilary Chemicals",8=>"Spare Parts",9=>"Spare Parts & Machinaries",10=>"Other Capital Items",11=>"Stationaries",12=>"Services - Fabric",13=>'Grey Fabric(Knit)',14=>'Grey Fabric(woven)',15=>'Electrical',16=>'Maintenance',17=>'Medical',18=>'ICT',19=>'Print & Publication',20=>'Utilities & Lubricants',21=>'Construction Materials',22=>'Printing Chemicals & Dyes',23=>'Dyes Chemicals & Auxilary Chemicals',24=>'Services - Yarn Dyeing ',25=>'Services - Embellishment',28=>'Cut Panel',30=>'Garments',31=>'Services Lab Test',32=>'Vehicle Components',33=>'Others',34=>'Painting Goods',35=>'Sanitary Goods',36=>'Safety and Security',37=>'Food and Grocery',38=>'Needles',39=>'ETP');*/

/*$item_category = array(1 => "Yarn",
	2 => "Knit Finish Fabrics",
	3 => "Woven Fabrics",
	4 => "Accessories",
	5 => "Chemicals",
	6 => "Dyes",
	7 => "Auxilary Chemicals",
	8 => "Spare Parts",
	9 => "Machinaries",
	10 => "Other Capital Items",
	11 => "Stationeries",
	12 => "Services - Fabric",
	13 => 'Grey Fabric(Knit)',
	14 => 'Grey Fabric(woven)',
	15 => 'Electrical',
	16 => 'Maintenance',
	17 => 'Medical',
	18 => 'ICT Equipment', // previous in ICT
	19 => 'Print & Publication',
	20 => 'Utilities & Lubricants',
	21 => 'Construction Materials',
	22 => 'Printing Chemicals & Dyes', //Chemicals & Dyes in spinning
	23 => 'Dyes Chemicals & Auxilary Chemicals', // previous 22 in spinning
	24 => 'Services - Yarn Dyeing ',
	25 => 'Services - Embellishment',
	28 => 'Cut Panel',
	30 => 'Garments',
	31 => 'Services Lab Test',
	32 => 'Vehicle Components',
	33 => 'Others',
	34 => 'Painting Goods',
	35 => 'Plumbing and Sanitary Goods',
	36 => 'Safety and Security',
	37 => 'Food and Grocery',
	38 => 'Needles',
	39 => 'WTP and ETP Machinery', //previous ETP in 3rdversion3.1
	40 => 'Spare Parts - Mechanical', // previous 9  in spinning
	41 => 'Spare Parts - Electrical', // previous 15 in spinning
	42 => 'Cotton', // previous 34 in spinning
	43 => 'Synthetic Fibre', // previous 35  in spinning
	44 => 'Packing Materials', // previous 36  in spinning
	45 => 'Factory Machinery', // new add
	46 => 'Iron Dril Machinery Machinery', // new add
	47 => 'Felt Machinery', // new add
	48 => 'Dosing Motor Pump', // new add
	49 => 'Centrifugal Water Pump', // new add
	50 => 'Flack Machinery', // new add
	51 => 'Back Sewing Machine', // new add
	52 => 'Batter Cabinet', // new add
	53 => 'TV', // new add
	54 => 'Finishing Machinery', // new add
	55 => 'Compresser Machinery', // new add
	56 => 'Sewing Machinery', // new add
	57 => 'Embroidery Machinery', // new add
	58 => 'Washing Machinery', // new add
	59 => 'Cutting Machinery', // new add
	60 => 'Knitting Machinery', // new add
	61 => 'Printing Machinery', // new add
	62 => 'Laboratory Machinery', // new add
	63 => 'PMD Machinery', // new add
	64 => 'Dyeing Machinery', // new add
	65 => 'Oil and Gas Generator', // new add
	66 => 'Fabric Spreader Machinery', // new add
	67 => 'Consumable', // new add
	68 => 'ICT Consumable',
	69 => 'Furniture', // new add
	70 => 'Fixture', // new add
	71 => 'Service Knitting', // new add
	72 => 'Service Dyeing', // new add
	73 => 'Service Heat Setting', // new add
	74 => 'Service All Over Print', // new add
	75 => 'Service Squeezing', // new add
	76 => 'Service Stentering', // new add
	77 => 'Service Open Compacting', // new add
	78 => 'Service Singeing', // new add
	79 => 'Service Fabric Finishing', // new add
	80 => 'Blow Room',
	81 => 'Carding',
	82 => 'Draw Frame',
	83 => 'Lap Former',
	84 => 'Comber',
	85 => 'Simplex',
	86 => 'Ring',
	87 => 'Autocone',
	88 => 'Conditioning',
	89 => 'AC Plant',
	90 => 'Chiller',
	91 => 'Substation',
	92 => 'Pump',
	93 => 'Cooling Tower',
	94 => 'Vehicle', // new add
	95 => 'Fabric Sales Order', // new add
	96 => 'Wastage', // new add
	97 => 'Repairing', // new add
	98 => 'Waste Cotton', // new add
	99 => 'Cleaning Goods',
	100 => 'Sweater',
	101 => 'Raw Material',
	102 => 'Services - Printing',
	103 => 'Services - Wash',
	104 => 'Services - Embroidery',
	105 => 'Wash Delivery'
);
asort($item_category);*/

$item_category = return_library_array("select CATEGORY_ID,SHORT_NAME from  lib_item_category_list where status_active=1 and is_deleted=0 order by SHORT_NAME", "CATEGORY_ID", "SHORT_NAME");


$item_category_type_arr=array(1 => "Yarn", 2 => "Finish Fabric", 3 => "Woven Finish Fabric", 4 => "Accessories", 5 => "Dyes Chemical and Auxilary Chemical", 8 => "General item", 13 => "Grey Fabric", 14 => "Woven Grey Fabric");
$rack_shelf_upto_arr=array(1 => "Store", 2 => "Floor", 3 => "Room", 4 => "Rack", 5 => "Shelf", 6 => "Bin/Box");

$maping_export_import_category=array(3 => "100", 10 => "2", 11 => "3", 20 => "71", 23 => "74", 36 => "104", 37 => "103", 35 => "102", 40 => "1", 45 => "4");
$maping_import_export_category=array(100 => "3", 2 => "10", 3 => "11", 71 => "20", 74 => "23", 104 => "36", 103 => "37", 102 => "35", 1 => "40", 4 => "45");

$report_signeture_list = array(0 => "-- Select Report --", 1 => "Fabric Booking", 2 => "Trims Booking", 3 => "PI Wise Yarn Receive", 4 => "Short Fabric Booking", 5 => "Sample Fabric Booking -With order", 6 => "Sample Fabric Booking -Without order", 7 => "Yarn Receive Return", 8 => "Dyes And Chemical Receive", 9 => "Dyes And Chemical Issue", 10 => "Dye/Chem Receive Return", 11 => "General Item Receive", 12 => "General Item Issue", 13 => "General Item Receive Return", 14 => "General Item Issue Return", 15 => "Dyes And Chemical Issue Requisition", 16 => "Knit Grey Fabric Receive", 17 => "Knit Grey Fabric Issue", 18 => "Grey Fabric Transfer Entry", 19 => "Grey Fabric Order To Order Transfer Entry", 20 => "Woven Finish Fabric Receive", 21 => "Knit Finish Fabric Issue", 22 => "Woven Finish Fabric Issue", 23 => "Finish Fabric Transfer Entry", 24 => "Finish Fabric Order To Order Transfer Entry", 25 => "Purchase Requisition", 26 => "Embellishment Issue Entry", 27 => "Embellishment Receive Entry", 28 => "Sewing Input", 29 => "Sewing Output", 30 => "Iron entry", 31 => "Packing And Finishing", 32 => "Ex-Factory", 33 => "Gate In Entry", 34 => "Gate Out Entry", 35 => "Trims Receive Entry", 36 => "Trims Issue", 37 => "Yarn Issue Return", 38 => "Yarn Transfer Entry", 39 => "Yarn Order To Order Transfer Entry", 40 => "Daily Yarn Demand", 41 => "Knitting Plan Report", 42 => "Yarn Work Order", 43 => "Yarn Dyeing Work Order", 44 => "Knitting Delivery Challan", 45 => "SubCon Fabric Finishing Entry", 46 => "SubCon Delivery Challan", 47 => "SubCon Knitting Bill Issue", 48 => "SubCon Dyeing And Finishing Bill Issue", 49 => "Yarn Issue", 50 => "SubCon Cutting Bill Issue", 51 => "SubCon Material Return Challan", 52 => "Batch Creation", 53 => "Fabric Service Booking", 54 => "Cutting Delivary To Input", 55 => "Stationary Work Order", 56 => "SubCon Batch Creation", 57 => "Embellishment Work Order", 58 => "Cut and Lay Entry", 59 => "Dyes Chemical Work Order", 60 => "Spare Parts Work Order", 61 => "Subcon Material Issue", 62 => "Recipe Entry", 63 => "Garments Delivery", 64 => "SubCon Knitting Delivery Challan", 65 => "Yarn Receive", 66 => "Knit Finish Fabric Receive By Textile", 67 => "Finish Fabric Production Entry", 68 => "Finish Fabric Delivery to Store", 69 => "Quotation Evaluation", 70 => "Grey Fabric Delivery to store roll wise", 71 => "Grey Fabric Receive Roll By Batch", 72 => "Grey Roll Issue to Sub Contact ", 73 => "AOP Roll Receive", 74 => "Finish Fabric Roll Receive By Cutting", 77 => "Sample Ex-factory", 78 => "Scrap Out Challan", 79 => "Service Booking For AOP", 80 => "Lab Test Work Order", 81 => "Service Booking For Knitting", 82 => "Service Booking For Dyeing", 83 => "Knit Finish Fabric Receive Return", 84 => "Piece Rate Work Order", 85 => "Knit Grey Fabric Receive Return", 86 => "Sample Development", 87 => "Knit Grey Fabric Issue Return", 88 => "Finish Fabric Issue Return", 89 => "Dye/Chem Issue Return", 90 => "Trims Issue Return", 91 => "Inspection", 92 => "Service Booking For AOP Without Order", 93 => "Fabric Requisition For Batch", 94 => "Roll Wise Grey Fabric Transfer Entry", 95 => "TNA Progress Report", 96 => "Order Wise Sewing Bill Wages Statement", 97 => "BTB Liability Coverage Report", 98 => "Trims Receive Return Entry", 99 => "Fab Service Receive", 100 => "Yarn Requisition Entry", 101 => "GSD Entry", 102 => "Yarn Purchase Requisition", 103 => "Poly Entry", 104 => "Roll Receive by Finish Process", 105 => "Trims Transfer", 106 => "Grey Fabric Bar-code Striker Export Report", 107 => "Finish Fabric Roll Delivery To Store", 108 => "Grey Fabric Delivery to Store", 109 => "Pre-Costing", 110 => "Operation Bulletin", 111 => "Roll wise Grey Sales Order To Sales Order Transfer", 112 => "Finish Roll Issue Return", 113 => "Yarn Dyeing Work Order Sales", 114 => "Gate Pass Entry", 115 => "Sample Trims Booking Without Order", 116 => "Finishing Input", 117 => "Cutting QC", 118 => "Cutting Entry", 119 => "Knitting Card", 120 => "Ready To Sewing Entry", 121 => "Partial Fabric Booking", 122 => "Yarn Service Work Order", 123 => "Cutting Delivery To Input Challan", 124 => "Grey Fabric Roll Issue", 125 => "Roll Wise Grey Fabric Delivery To Store", 126 => "Quotation Inquery", 127 => "Sample Delivery", 128 => "Finish Fabric Requisition for Cutting", 129 => "Woven Finish Fabric Issue Return", 130 => "Woven Finish Fabric Receive Return", 131 => "Fab Service Receive Return", 132 => "Multiple Job Wise Trims Booking V2", 133 => "Multiple Job Wise Emblishment Work Order", 134 => "Sample Requisition With Booking", 135 => "Sample Requisition Fabric Booking -With order", 136 => "Buyer Order Wise Prod Spent Min Produce Min With CM Report", 137 => "Order Reconciliation report", 138 => "Embl. Recipe Entry", 139 => "Embl.Dyes And Chemical Issue Requisition", 140 => "Embellishment Production", 141 => "Buyer Wise Total Export Value and CM", 142 => "LC Wise Knit Finish Fabric Receive Report", 143 => "Item Issue requisition", 144 => "Post Cost Analysis Report V2", 145 => "Fabric Issue to Finish Process", 146 => "Sample Requisition", 147 => "Pro Forma Invoice V2", 148 => "Finish Fabric Multi Issue challan", 149 => "Knit Finish Fabric Receive By Garments", 150 => "Multi Job Wise Service Booking Knitting", 151 => "Yarn Dyeing Work Order Without Order", 152 => "Other Purchase order", 153 => "General Item Transfer", 154 => "Embellishment Delivery", 155 => "Embellishment Bill Issue",156 => "Raw Material Receive", 157 => "Raw Material Issue",158 =>"Packing and Finishing Bill Issue",159 =>"Pre-Costing V2 [Acc. Dtls. V2]",160 =>"Trims Job Card Preparation",161 =>"Multiple Job Wise Short Trims Booking V2",162 =>"AOP Batch Creation",163 =>"Order Sheet Report",164 =>"AOP Recipe Entry",165 =>"Topping Adding Stripping Recipe Entry",166 =>"Grey Fabric Service Work Order",167 =>"Trims Bill Issue",168 =>"AOP Dyes Chemical Issue",169 =>"AOP Dyes And Chemical Issue Requisition",170 =>"Embroidery Bill Issue",171 =>"Printing Bill Issue",172 =>"Wash Dyes Chemical Issue",173 =>"AOP Delivery Entry",174 =>"Trims Delivery Entry",175 =>"Knitting Bill Entry",176 =>"Fabric Sales Order Entry",177 =>"Commercial Office Note",178 =>"Left Over Garments Issue",179 =>"Left Over Garments Receive",180 =>"Service Booking For Kniting and Dyeing [Without Order]",181 =>"Wash Delivery",182=>"Sewing Bill Issue",183=>"Bundle Issue To Linking",184=>"Embroidery Material Receive",185=>"Embroidery Material Issue",186=>"Embroidery Material Receive Return",187=>"Embroidery Material Issue Return");
asort($report_signeture_list);

$report_template_list = array(1 => 'Template 1', 2 => 'Template 2', 3 => 'Template 3', 4 => 'Template 4', 5 => 'Template 5');
//,100=>"Yarn Purchase Order",101=>"Dyes And Chemical Purchase Order",102=>"Stationary Purchase Order",103=>"Others Purchase Order" //Dublicate //Remove as per Jahid


$entry_form = array(1 => "Yarn Receive", 2 => "Grey Receive", 3 => "Yarn Issue", 4 => "Chemical Receive", 5 => "Chemical Issue", 6 => "Dye Production Update", 7 => "Finish Fabric Production Entry", 8 => 'Yarn Receive Return', 9 => 'Yarn Issue Return', 10 => 'Yarn Transfer Entry', 11 => 'Yarn Order To Order Transfer Entry', 12 => 'Grey Fabric Transfer Entry', 13 => 'Grey Fabric Order To Order Transfer Entry', 14 => 'Finish Fabric Transfer Entry', 15 => 'Finish Fabric Order To Order Transfer Entry', 16 => 'Knit Grey Fabric Issue', 17 => 'Woven Finish Fabric Receive', 18 => 'Knit Finish Fabric Issue', 19 => 'Woven Finish Fabric Issue', 20 => 'General Item Receive', 21 => 'General Item Issue', 22 => 'Knit Grey Fabric Receive', 23 => 'Woven Grey Fabric Receive', 24 => 'Trims Receive', 25 => 'Trims Issue', 26 => 'General Item Receive Return', 27 => 'General Item Issue Return', 28 => 'Dye/Chem Receive Return', 29 => 'Dye/Chem Issue Return', 30 => 'Slitting/Squeezing', 31 => 'Drying', 32 => 'Heat Setting', 33 => 'Compacting', 34 => 'Special Finish', 35 => 'Dyeing Production', 36 => 'SubCon Batch Creation', 37 => "Finish Fabric Receive Entry", 38 => "SubCon Dyeing Production", 39 => "Doc. Submission to Buyer", 40 => "Doc. Submission to Bank", 41 => "Yarn Dying With Order", 42 => "Yarn Dying Without Order", 43 => "Main Trims Booking", 44 => "Main Trims Booking V2", 45 => "Knit Grey Fabric Receive Return", 46 => "Knit Finish Fabric Receive Return", 47 => "Singeing", 48 => "Stentering", 49 => "Trims Receive Return", 50 => "Woven Grey Fabric Receive Return", 51 => "Knit Grey Fabric Issue Return", 52 => "Knit Finish Fabric Issue Return", 53 => "Grey Fabric Delivery to store", 54 => "Finish Fabric Delivery to store", 55 => "Chemical Transfer Entry", 56 => "Grey Fabric Delivery to store roll wise", 57 => "General Item Transfer", 58 => "Knit Grey Fabric Receive Roll", 59 => "Recipe Entry", 60 => "Dyeing Re Process", 61 => "Grey Fabric Issue Roll Wise", 62 => "Grey Fabric Receive Roll By Batch", 63 => "Grey Roll Issue to Sub Contact ", 64 => "Batch Creation For Roll", 65 => "AOP Roll Receive", 66 => "Finish Fabric Production and QC By Roll", 67 => "Finish Fabric Roll Delevery To Store", 68 => "Finish Fabric Roll Receive By Store", 69 => "Purchase Requisition", 70 => "Yarn Purchase Requisition", 71 => "Finish Fabric Roll Issue", 72 => "Finish Fabric Roll Receive By Cutting", 73 => "Trims Issue Return", 74 => "Batch Creation for Gmts Wash", 75 => "Roll Splitting", 76 => "cut and lay entry", 77 => "cut and lay entry roll wise", 78 => "Trims Order To Order Transfer Entry", 79 => "Lab Test Work Order", 80 => "Grey Fabric Order To Sample Transfer Entry", 81 => "Grey Fabric Sample To Order Transfer Entry", 82 => "Roll Wise Grey Fabric Transfer Entry", 83 => "Roll wise Grey Fabric Order To Order Transfer Entry", 84 => "Roll wise Grey Fabric Issue Return", 85 => "Garments Ex-Factory Return", 86 => "Main Fabric Booking", 87 => "Multiple Job Wise Trims Booking V2", 88 => "Short Fabric Booking", 89 => "Sample Fabric Booking -With order", 90 => "Sample Fabric Booking -Without order", 91 => "Fabric Issue to Fin. Process", 92 => "Fabric Service Receive", 93 => "Cut and Lay Entry Ratio Wise", 94 => "Yarn Service Work Order", 95 => "Sewing Input", 96 => "Bundle Wise Sewing Input", 97 => "Cut and Lay Entry Ratio Wise RMG No", 98 => "Knitting Production", 99 => "Cut and Lay Entry Ratio Wise Urmi", 104 => "Pro Forma Invoice", 105 => "BTB/Margin LC", 106 => "Export LC Entry", 107 => "Sales Contract Entry", 108 => "Partial Fabric Booking", 109 => "Fabric Sales Order Entry", 110 => "Roll wise Grey Fabric Order To Sample Transfer Entry", 111 => "Pre-Costing", 112 => "Trims Transfer", 113 => "Grey Roll Splitting Before Issue", 114 => "Yarn Dying Without Order 2", 115 => "Roll Receive by Finish Process", 116 => "Sample Development", 117 => "Sample Requisition", 118 => "Main Fabric Booking Urmi", 119 => "Dia Wise Fabric Booking", 120 => "Yarn Requisition Entry For Sales", 121 => "Cutting Entry", 122 => "Order Update Entry", 123 => "Fabric Requisition For Batch 2", 124 => "Material/Goods Parking", 125 => "Yarn Dying Work Order Without Lot", 126 => "Finish Roll Issue Return", 127 => "Sample Requisition Cutting", 128 => "Sample Embellishment Entry", 129 => "Cotton Receive", 130 => "Sample Requisition Sewing Output", 131 => "Sample Wash Or Dyeing", 132 => "Sample Delivery", 133 => "Roll wise Grey Sales Order To Sales Order Transfer", 134 => "Roll wise Finish Fabric Order To Order Transfer Entry", 135 => "Yarn Dyeing Work Order Sales", 136 => "Trims Batch Creation", 137 => "Sample Approval-Before Order Place", 138 => "Cut and Lay Entry Plies", 139 => "Sample Requisition Fabric Booking-With order", 140 => "Sample Requisition Fabric Booking-Without order", 141 => "Finish Roll Splitting Before Issue", 142 => "Sample Trims Booking With Order", 143 => "Sample Trims Booking Without Order", 144 => "Yarn Purchase Order", 145 => "Dyes And Chemical Purchase Order", 146 => "Stationary Purchase Order", 147 => "Others Purchase Order", 148 => "Sewing Operation", 149 => "Operation Bulletin", 150 => "SubCon Batch For Gmts Wash/Dyeing/Printing", 151 => "Recipe Entry For Gmts Wash/Dyeing/Printing", 152 => "Export Pro Forma Invoice", 153 => "Cotton Issue to Production", 154 => "Item Issue Requisition", 155 => "Cotton Issue Requisition", 156 => "Dyes And Chemical Issue Requisition", 157 => "SubCon Dyes And Chemical Issue Requisition", 158 => "Pre-Costing-V2", 159 => "SubCon Knitting Production", 160 => "Production All Pages(Cutting,Sewing,Emb)", 161 => "Embellishment Work Order V2", 162 => "Service Booking For AOP V2", 163 => "Order Entry", 164 => "Poly Entry", 165 => "PI Yarn", 166 => "PI Fabrics", 167 => "PI Accessories", 168 => "PI Service Fabric", 169 => "PI Yarn Dyeing", 170 => "PI Embellishment", 171 => "PI Lab Test", 172 => "PI General", 173 => "Yarn Production", 174 => "SubCon PI", 175 => "All Purchase Order Page", 176 => "Fabric Service Booking", 177 => "Service Booking For AOP Without Order", 178 => "Short Trims Booking [Multiple Order]", 179 => "Lab Test WO - Without Order", 180 => "Roll Wise Grey Fabric Sample To Sample Transfer Entry", 181 => "Cotton QC Entry", 182 => "Service Booking For Knitting", 183 => "Roll Wise Grey Fabric Sample To Order Transfer Entry", 184 => "Yarn Count Determination", 185 => "Cotton Receive Openning", 186 => "Knitting Bill Issue", 187 => "Inspection Expenses", 188 => "Ind Sales Confirmation", 189 => "Ind Sales Contract", 190 => "Ind Pi Request", 191 => "Indenting Pi", 192 => "Ind Lc Entry", 193 => "Ind Lc Amendment", 194 => "Service Booking For Kniting and Dyeing [Without Order]", 195 => "Woven Finish Fabric Roll Issue", 196 => "Woven Finish Roll Issue Return", 197 => "PI Garments Service", 198 => "Garments Delivery Entry", 199 => "Print Booking", 200 => "Print Booking Urmi", 201 => "Multi Job Wise Print Booking", 202 => "Woven Finish Fabric Receive Return", 203 => "Sample Requisition With Booking", 204 => "Embellishment Order Entry", 205 => "Embellishment Material Receive", 206 => "Fab Service Receive Return", 207 => "Embellishment Material Issue", 208 => "Trims Delivery", 209 => "Woven Finish Fabric Issue Return", 210 => "Waste Cotton Receive", 211 => "Waste Cotton Delivery Order", 212 => "Waste Cotton Delivery/issue", 213 => "Waste Cotton Bill", 214 => "Roll wise Finish Fabric sample To sample Transfer Entry", 215 => "Country and Order Wise trim Booking V3", 216 => "Roll wise Finish Fabric Order To Sample Transfer Entry", 217 => "Embellishment Batch Creation", 218 => "Roll Wise Grey Fabric Receive Return", 219 => "Roll wise Finish Fabric Sample To Order Transfer Entry", 220 => "Embl. Recipe Entry", 221 => "Embl.Dyes And Chemical Issue Requisition", 222 => "Embellishment Production", 223 => "Embellishment QC Entry", 224 => "Finish Fabric Delivery To Garments", 225 => "Knit Finish Fabric Receive By Garments", 226 => "Cotton Item Transfer", 227 => "PI Dyes Chemical", 228 => "Multi Job Wise Service Booking Knitting", 229 => "Multi Job Wise Service Booking Dyeing", 230 => "Finish Fabric FSO to FSO Transfer", 231 => "Pro Forma Invoice V2", 232 => "Service Booking For Dyeing", 233 => "Knit Finish Fabric Issue Return", 234 => "Yarn Purchase Order[Sweater]", 235 => "Ring Machine Wise Production Entry", 236 => "Autocone Machine Wise Production Entry", 237 => "Packing Production Entry", 238 => "Sub-Con Order Entry", 239 => "Synthetic Fiver Receive", 240 => "Synthetic Fiver QC and Stock Recognising", 241 => "Synthetic Fiver Issue to Production", 242 => "Synthetic Fiver Receive Return", 243 => "Synthetic Fiver Issue Return", 244 => "Synthetic Fiver Item Transfer", 245 => "Time And Weight Record", 246 => "Synthetic Fiver Issue Requisition", 247 => "Finish Fabric Transfer Acknowledgement", 248 => "Sweater Yarn Receive", 249 => "Sweater Yarn Style To Style Transfer", 250 => "Embellishment Dyes Chemical Issue", 251 => "Gate Pass Entry", 252 => "Multiple Job Wise Trims Booking V2 for Sweater", 253 => "Yarn Lot Ratio Entry", 254 => "Embellishment Delivery", 255 => "Trims Order Receive", 256 => "Yarn Dyeing Bill Entry", 257 => "Job Card Preparation", 258 => "Woven Finish Fabric Transfer Entry", 259 => "Machine Wash Requisition", 260 => "Country and Order Wise Trims Booking V2", 261 => "Multiple Job Wise Trims Booking", 262 => "Multiple Job Wise Short Trims Booking V2", 263 => "Raw Material Receive", 264 => "Raw Material Receive Return", 265 => "Raw Material Issue", 266 => "Raw Material Issue Return", 267 => "Finish Fabric QC Result", 268 => "Woven Finish Fabric Transfer Acknowledgement", 269 => "Trims Production Entry", 270 => "Export Invoice", 271 => "Woven Partial fabric Booking", 272 => "Woven Multiple Job Wise Trims Booking", 273 => "Woven Multi Job Wise Short Trims Booking", 274 => "Woven Lab Test Work Order", 275 => "Woven Short fabric Booking",276=>"Trims Bill Issue",277=>"Sweater Yarn Issue",278=>"Aop Order Entry",279=>"AOP Material Receive",280=>"AOP Material Issue",281=>"AOP Batch Creation",282=>"Planning Info Entry For Sales Order",283=>"Grey qc for android",284=>"Sample Yarn Purchase Order[Sweater]",285=>"AOP Recipe Entry",286=>"Fabric Sales Order Entry Inter Company",287=>"Knit Finish Fabric Textile Receive Return",288=>"SubCon Material Receive",289=>"Woven Cut and Lay Entry Ratio Wise",290=>"AOP Dyes And Chemical Issue Requisition",291=>"AOP Production",292=>"Subcon Fabric Finishing Entry",293=>"Subcon Printing Production",294=>"AOP QC Entry",295=>"Wash Order Entry",296=>"Wash Material Receive",297=>"Wash Material Issue",298=>"Wash Dyes Chemical Issue",299=>"Wash Dyes And Chemical Issue Requisition",300=>"Wash Recipe Entry",301=>"Wash Production",302=>"Wash QC Entry",303=>"Wash Delivery Entry",304=>"Wash Bill Issue",306=>"Finish Fabric Transfer Entry With Sample",307=>"AOP Delivery Entry",308=>"AOP Dyes Chemical Issue",309=>"Grey Fabric Service Work Order",310=>"De Oiling",311=>"Embroidery Order Entry",312=>"Embroidery Material Receive",313=>"Embroidery Material Issue",314=>"Price Quotation",315=>"Embroidery Production",316=>"Batch Creation For Gmts. Wash",317=>"Knit Finish Fabric Roll Receive By Textile",318=>"Finish Fabric Roll Delivery To Garments",319=>"Bundle Issue To Linking",320=>"Bundle Receive In Linking",321=>"Bundle Wise Linking Input",322=>"Bundle Wise Linking Output",323=>"Dry Slitting",324=>"Embroidery QC Entry",325=>"Embroidery Delivery",326=>"Embel. Issue [Sweater]",327=>"Cotton Purchase Order",328=>"Knit Finish Fabric Roll Receive Return of Textile", 329=>"Finish Fabric Roll Issue Return of Textile",330=>"Embel. Receive [Sweater]",331=>"Iron Entry [Sweater]",332=>"Embroidery Bill Issue",333=>"Poly Entry [Sweater]",334=>"Item Account Creation for Trims",335=>"Sweater Yearn Service Work Order",336=>"Dyeing And Finishing Bill Issue",337=>"Sample Sewing Input",338=>"Sample Embellishment Issue",339=>"Roll Wise Grey Fabric Requisition For Transfer",340=>"Yarn Service Work Order Without Lot",341=>"Sweater Sample Requisition",342=>"Dry Production",343=>"SubCon Material Issue",344=>"SubCon Material Return",345=>"Sweater Sample Acknowledge",346=>"Bundle Wise Linking Operation Track",347=>"Bundle Wise Cutting Delivery To Input Challan", 348 => "Bundle Wise Sewing Line Input", 349 => "Bundle Wise Sewing Line Output", 350 => "Trims Receive Entry Multi Ref.",351 => "Order Entry By Matrix Woven",352 => "Roll wise Grey Sales Order To Sales Order Requisition For Transfer",353 => "Grey Fabric Requisition For Transfer Entry",354 => "Bundle Wise Sewing Input From Text File",355 => "Bundle Wise Sewing Output From Text File",356 => "Synthetic Fibre Purchase Order",357 => "Trims issue requisition",358 => "AOP Bill Issue",359=>"Grey Fabric Transfer Acknowledgement Entry",360=>"Wash Delivery Return",361 => "Service Booking For AOP",362 => "Weight Wise Grey Sales Order To Sales Order Transfer",363 =>"Gate In Entry",364 =>"Finish Fabric Transfer Acknowledgement For Sample",365=>"Order Entry By Matrix",366=>"Embroidery Material Receive",367=>"Embroidery Material Issue",368=>"Embroidery Material Receive Return",369=>"Embroidery Material Issue Return");

//,305=>"Printing Delivery" 
asort($entry_form);

//Not Used
$entry_form_for_roll = array(1 => "grey_productin_entry", 2 => "batch_creation", 3 => "Dye Production Update", 4 => "finish_fabric_receive", 5 => "Woven Finish Fabric Receive", 16 => "Knit Grey Fabric Issue", 18 => 'Knit Finish Fabric Issue', 19 => 'Woven Finish Fabric Issue', 22 => 'Knit Grey Fabric Receive', 23 => 'Woven Grey Fabric Receive', 45 => "Knit Grey Fabric Receive Return");
asort($entry_form_for_roll);

$form_list_for_mail = array(1 => "Daily Order Entry", 2 => "Yesterday Total Activities", 3 => "TNA Task Mail", 4 => "Order Position By Team", 5 => "Booking Revised", 6 => "Missing PO List in TNA Process", 7 => "Order Revised", 8 => "Cancelled Order", 9 => "Subcontract Dyeing", 10 => "Returnable Pending", 11 => "Precost approval auto mail", 12 => "Below 5% Profitability Order", 13 => "Less EPM than CPM", 14 => "Total Production Activities", 15 => "Price Quotation Approval Status", 16 => "Grey Fabric Receive", 17 => "Finish Fabric Receive", 18 => "Daily Producion Auto Mail", 19 => "Bill of Entry overdue list", 20 => 'Yarn issue pending from allocation.', 21 => 'Bill of lading delay (Commercial)', 22 => 'Monthly capacity vs booked auto mail',23=>"Fabric Booking Revised",24=>"Price Quotation Mail Notification",
/*25=>"Sweater TNA Task Mail",*/
26=>"Sweater Export LC/Sales Contract Report",27=>"Sweater Shipment Pending Report",28=>"Pending pi for approval auto mail",29=>"Sweater Sample Delivery Pending",30=>"Sample Finish Fabric Pending Auto Mail",31=>"Machine Summary Below 80 % production");
asort($form_list_for_mail);

$entry_form_for_approval = array(1 => "Purchase Requisition Approval", 2 => "Yarn WO Approval", 3 => "Dyes/Chemical WO Approval", 4 => "Spare parts WO Approval", 5 => "Stationary WO Approval", 6 => "Pro-forma Invoice WO Approval", 7 => "Fabric Booking Approval", 8 => "Trims Booking Approval", 9 => "Sample Booking (Without Order) Approval", 10 => "Price Quatation Approval", 12 => "Short Fabric Booking Approval", 13 => "Sample Fabric Booking-With Order", 14 => "Yarn Delivery Approval", 15 => "Pre-Costing", 16 => "Dyeing Batch Approval", 17 => "Other Purchase WO Approval", 18 => "Yarn Purchase Requisition Approval", 19 => "Gate Pass Activation Approval", 20 => "yarn requisition approval", 21 => "PI approval", 22 => "All approval", 23 => "GSD Entry Approval", 24 => "Fabric Sales Order Approval", 25 => "Sample Requisition Approval", 26 => "Item Issue Requisiton", 27 => "PI approval new", 28 => "Service Booking AOP Approval", 29 => " Service Booking For Knitting", 30 => "Yarn Dyeing Work Order ", 31 => "Sample Requisition with Booking", 32 => "Embellishment Work Order Approval", 33 => "Yarn Dyeing without Work Order", 34 => "Price Quotation V3", 35 => "Yarn Delivery Acknowledgement",36=> "Quick Costing Approval",37=> "Transfer Requisition Approval",38=> "Import Document Acceptance Approval",39=> "Commercial Office Note Approval",40=> "Transfer Requisition Approval for Sales Order");
asort($entry_form_for_approval); //11=>"Yarn Delivery Approval",

$wages_rate_var_for = array(1 => "Garments Cutting", 2 => "Garments Finishing");
$bulletin_type_arr = array(1 => "RnD", 2 => "Marketing", 3 => "Budget", 4 => "Production");

/*$production_resource=array(1=>"Single Needle",2=>"Flat Lock",3=>"Over Lock",4=>"Button Hole",5=>"Button Stich",6=>"Snap Button",7=>"Eyelet Hole",8=>"Kansai",9=>"Feed Of the Arm",10=>"Rib Scissoring",11=>"Ngai Sing-76",12=>"Ngai Sing-82",13=>"Ngai Sing-84",14=>"Ngai Sing-85",15=>"Double Needle",16=>"Crease Machine",17=>"Fusing Machine",18=>"Bartack",19=>"S/N Lock-Edge Cutter",20=>"Flat Bed",40=>"Sewing Helper",41=>"Sewing QuaLity Inspector",42=>"Eyelet",43=>"Table",44=>"Exam Table",45=>"Double Needle Lock" ,46=>"Double Needle Flat Lock",47=>"4 OT Over Lock",48=>"Supervisor",49=>"Single Needle Z/Z", 50=>"Double Needle Chain Stitch", 51=>"Picoding", 52=>"Patter Sewer", 53=>"Finishing I/M", 54=>"Finishing QI", 55=>"Poly Helper",56=>"Packing");

$production_resource=array(1=>"SNDL",2=>"FL",3=>"OL",4=>"BH",5=>"BA",6=>"Snap BA",7=>"EH",8=>"SNDL Kansai",9=>"F.O. Arm",10=>"Rib Scissoring",11=>"Ngai Sing-76",12=>"Ngai Sing-82",13=>"Ngai Sing-84",14=>"Ngai Sing-85",15=>"2NDL",16=>"Crease M/C",17=>"Fusing M/C",18=>"BTK",19=>"SNDL Edg Cutter",20=>"Flat Bed",21=>"Smoking",22=>"LZ",40=>"Sew Helper",41=>"Sewing QI",42=>"Eyelet",43=>"Table",44=>"Exam Table",45=>"2NDL Lock" ,46=>"2NDL FL",47=>"4 OT OL",48=>"Supervisor",49=>"SNDL Z/Z", 50=>"2NDL Ch. Stitch", 51=>"Picoting", 52=>"Patter Sewer", 53=>"Fin I/M", 54=>"Fin QI", 55=>"Poly Helper",56=>"Packing",57=>"Auto",58=>"3T OL",59=>"4T OL",60=>"5T OL",61=>"2T FL",62=>"3T FL",63=>"4T FL",64=>"5T FL",65=>"Flat Seam",66=>"Vertical Cutter",67=>"BackTap",68=>"FLD Man",69=>"L I/M",70=>"Fin Asst",71=>"2NDL Kansai",72=>"3NDL Kansai",73=>"Piping Cutter");*/

$production_resource = array(1 => "SNL Auto", 2 => "2T FL Auto", 3 => "3T OL Manual", 4 => "BH Auto", 5 => "BA Auto", 6 => "Snap BA", 7 => "EH", 8 => "SNDL Kansai", 9 => "FEED of the ARM", 10 => "Rib Scissoring", 11 => "Ngai Sing-76", 12 => "Ngai Sing-82", 13 => "Ngai Sing-84", 14 => "Ngai Sing-85", 15 => "2NDL", 16 => "Crease M/C", 17 => "Fusing M/C", 18 => "BTK", 19 => "SNDL Edg Cutter", 20 => "Flat Bed", 21 => "SMOKE", 22 => "LZ", 23 => "J Stitch", 40 => "Assistant Operator", 41 => "Sewing QI", 42 => "Eyelet", 43 => "Table", 44 => "Exam Table", 45 => "DNL Lock Switch Auto", 46 => "2NDL FL", 47 => "4 OT OL", 48 => "Supervisor", 49 => "SNDL Z/Z", 50 => "DNL Chain Stitch", 51 => "PICODING", 52 => "Patter Sewer", 53 => "Finishing Iron", 54 => "Finishing QI", 55 => "Poly Helper", 56 => "Packing", 57 => "Auto", 58 => "3T OL Auto", 59 => "4T OL Auto", 60 => "5T OL", 61 => "2T FL Manual", 62 => "3T FL Auto", 63 => "4T FL Auto", 64 => "5T FL Auto", 65 => "FLAT SEAM", 66 => "Vertical Cutter", 67 => "BT", 68 => "Folding", 69 => "Sewing Iron", 70 => "Finishing Helper", 71 => "2NDL Kansai", 72 => "3NDL Kansai", 73 => "PIPING Cutter", 74 => "SNL Manual", 75 => "DNL Lock Stitch Manual", 76 => "3T FL Manual", 77 => "4T FL Manual", 78 => "5T FL Manual", 79 => "4T OL Manual", 80 => "OL Cutter", 81 => "VT Auto", 82 => "VT Manual", 83 => "BA Manual", 84 => "BH Manual", 85 => "Snap", 86 => "WRAPING", 87 => "LATUS", 88 => "HEAT SEAL", 89 => "ZIGZAG", 90 => "Hand Tag", 91 => "1NDL FL", 92 => "OL no thread", 93 => "SNL[Chain STS]", 94 => "KNS[SMOKE]", 95 => "KNS", 96 => "Velcro", 97 => "Elastic Diviser", 98 => "SNL-UBT", 99 => "SNL-VT", 100 => "2TOL", 101 => "3ZZ", 102 => "FL(FB)", 103 => "FL(CB)", 104 => "Ladder Stitch", 105 => "SNDL Lock Stitch", 106 => "OL Serging", 107 => "APW", 108 => "Pressing", 109 => "Btn/Stc", 110 => "C.C", 111 => " MNDL- Kansai", 112 => "F.Fitter", 113 => "OL- Back latch", 114 => "4TOL- Auto elastic cutter", 115 => "FL- Right side cutter", 116 => "FL- Left side cutter", 117 => "FL- Left side cutter- Nero", 118 => "FL- Belt join", 119 => "SNL- cutter", 120 => "Button hole", 121 => "Button attach", 122 => "Dise", 123 => "Blind Stitch", 124 => "Forming", 125 => "Spot Tuck",126 => "1NDL PM",127 => "2NDL PM",128 => "Saddle stitch",129 => "Man",130 => "Machine");
asort($production_resource);

$machine_category = array(1 => "Knitting", 2 => "Dyeing", 3 => "Printing", 4 => "Finishing", 5 => "Embroidery", 6 => "Washing", 7 => "Cutting", 8 => "Sewing", 9 => "CAD Machine", 10 => "Vehicles", 11 => "Others", 12 => "ETP", 13 => "Seamless", 14 => "Maintenance", 15 => "Ring Machine", 16 => " Auto Cone Machine", 17 => " Uniflex", 18 => " Carding", 19 => " Breaker Draw Frame", 20 => " Lap Former", 21 => " Comber", 22 => " Finisher Draw Frame", 23 => " Simplex", 24 => " Spinning", 25 =>"Trims/Accessories",26 =>"Insp",27 =>"Link",28 =>"Attachment",29 =>"Hole Button",30 =>"Iron",31 =>"Final",32 =>"Packing");
asort($machine_category);

$depreciation_method = array(1 => "Straight-line", 2 => "Reducing Balance");

$item_transfer_criteria = array(1 => "Company To Company", 2 => "Store To Store", 3 => "Style To Style", 4 => "Order To Order", 5 => "Item To Item", 6 => "Order To Sample", 7 => "Sample To Order", 8 => "Sample To Sample");

$party_type = array(
	1 => "Buyer",
	2 => "Subcontract",
	3 => "Buyer/Subcontract",
	4 => "Notifying Party",
	5 => "Consignee",
	6 => "Notifying/Consignee",
	7 => "Client",
	20 => "Buying Agent",
	21 => "Buyer/Buying Agent",
	22 => "Export LC Applicant",
	23 => "LC Applicant/Buying Agent",
	30 => "Developing Buyer",
	80 => "Other Buyer",
	90 => "Buyer/Supplier",
	100 => "Also Notify Party",
);
asort($party_type);

$party_type_supplier = array(
	1 => "Supplier",
	2 => "Yarn Supplier",
	3 => "Dyes & Chemical Supplier",
	4 => "Trims Supplier",
	5 => "Accessories Supplier",
	6 => "Machineries Supplier",
	7 => "General Item",
	8 => "Stationery Supplier",
	9 => "Fabric Supplier",
	20 => "Knit Subcontract",
	21 => "Dyeing/Finishing Subcontract",
	22 => "Garments Subcontract",
	23 => "Embellishment Subcontract",
	24 => "Fabric Washing Subcontract",
	25 => "AOP Subcontract",
	26 => "Lab Test Company",
	30 => "C & F Agent",
	31 => "Clearing Agent",
	32 => "Forwarding Agent",
	35 => "Transport Supplier",
	36 => "Labor Contractor",
	37 => "Civil Contractor",
	38 => "Interior",
	39 => "Other Contractor",
	40 => "Indentor",
	41 => "Inspection",
	90 => "Buyer/Supplier",
	91 => "Loan Party",
	92 => "Vehicle Components",
	93 => "Twisting",
	94 => "Re-Waxing",
	95 => "Grey Fabric Service Subcontract"
);
asort($party_type_supplier);

$tna_task_catagory = array(
	1 => "General",
	5 => "Sample Approval",
	6 => "Lab Dip Approval",
	7 => "Trims Approval",
	8 => "Embellishment Approval",
	9 => "Test Approval",
	15 => "Purchase",
	20 => "Material Receive",
	25 => "Fabric Production",
	26 => "Garments Production",
	30 => "Inspection",
	35 => "Export");
asort($tna_task_catagory);

$recipe_for = array(1 => "Sample", 2 => "Bulk", 3 => "Compound Color");
$supplier_nature = array(
	1 => " Goods",
	2 => "Service",
	3 => "Both",
);
$fabric_typee = array(1 => "Open Width", 2 => "Tubular", 3 => "Needle Open");
$process_type = array(1 => "Main Process", 2 => "Additional Process");
$account_type = array(1 => "CD A/C", 2 => "STD A/C", 3 => "OD A/C", 4 => "CC A/C", 5 => "BTB Margin A/C", 6 => "ERQ A/C", 7 => "Imp. LC Margin A/C", 8 => "BG Margin A/C", 9 => "ECC A/C", 10 => "PC A/C", 11 => "Advance A/C");
$core_business = array(1 => "Manufacturing", 2 => "Trading", 3 => "Service", 4 => "Educational", 5 => "Social Welfare");
$company_nature = array(1 => "Private Ltd", 2 => "Public Ltd", 3 => "Sole Tradership", 4 => "Partnership");
$loan_type = array(0 => "Percent", 1 => "Fixed");

$commercial_module = array(5 => "Garments Export Capacity", 6 => "Max BTB Limit", 7 => "Max PC Limit", 17 => "Possible Heads For BTB", 18 => "Export Invoice Rate", 19 => "Doc Monitoring Standard", 20 => "Internal File Source", 21 => "Attach Approved PI", 22 => "Export Invoice Qty Source", 23 => "After Goods Receive Data Source", 24 => "Yarn Purchase Order Controll", 25 => "PI Source BTB LC", 26 => "Commission source at Export Invoice");
$commission_source_at_export_invoice = array(1 =>"Manual(Existing)", 2 =>"Pre-Cost");

$export_invoice_qty_source = array(1 => "Manual (Existing)", 2 => "Gate Out ID", 3 => "Garment Delivery ID");

$cost_heads = array(0 => "--Select--", "Knitting Charge" => "Knitting Charge", "Fabric Dyeing Charge" => "Fabric Dyeing Charge", "Yarn Dyeing Charge" => "Yarn Dyeing Charge", "All Over Print Charge" => "All Over Print Charge", "Dyed Yarn Knit Charge" => "Dyed Yarn Knit Charge", "Stantering Charge" => "Stantering Charge", "Brush Peach Charge" => "Brush Peach Charge", "Washing Charge" => "Washing Charge", "Printing" => "Printing", "Embroidery" => "Embroidery", "Washing" => "Washing");

$rate_for = array(1 => "Knitting", 2 => "Warping", 3 => "Sizing", 4 => "Knotting/ Drawing", 5 => "Weaving", 10 => "Dying", 20 => "Cutting", 30 => "Sewing", 35 => "Ironing", 40 => "Finishing");

$cal_parameter = array(1 => "Sewing Thread", 2 => "Carton", 3 => "Carton Sticker", 4 => "Blister Poly ", 5 => "Elastic", 6 => "Gum Tap", 7 => "Tag Pin", 8 => "Sequines", 9 => "Eyelet");
$cm_cost_predefined_method = array(1 => "=((SMV*CPM)*Costing per + (SMV*CPM*Costing per)* Efficiency Wastage%)/Exchange Rate", 2 => "(((SMV*CPM)*Costing per / Efficiency %)+((SMV*CPM)*Costing per / Efficiency %))/Exchange Rate", 3 => "{(MCE/WD)/NFM)*MPL)}/[{(PHL)*WH}]*Costing Per/Exchange Rate", 4 => "[((CPM/Efficiency%)*SMV*Costing Per)/Exchange Rate]");
$commercial_cost_predefined_method = array(1 => "Yarn+Trims+Fabric Purchase", 2 => "On Selling Price", 3 => "On Net Selling Price", 4 => "Yarn+Trims+Fabric Purchase+Embellishment Cost", 5 => "Fabric Purchase + Trims Cost + Embellishment Cost + Garments Wash + Lab Test + Inspection + CM Cost + Freight + Courier Cost + Certificate Cost + Design Cost + Studio Cost + Operating Expenses", 6 => "Fabric Purchase + Trims Cost + Embellishment Cost + Garments Wash + Lab Test + Inspection +  Freight + Courier Cost + Certificate Cost + Design Cost + Studio Cost + Operating Expenses");

$test_for = array(1 => "Garments", 2 => "Fabrics", 3 => "Trims");

$testing_category = array(1 => "Color Fastness",
	2 => "Dimension & Appearance ",
	3 => "Strength ",
	4 => "Fabric Construction ",
	5 => "Composition ",
	6 => "Flammability ",
	8 => "Fabric Performance ",
	10 => "Garments Accessories ",
	11 => "Stability/Appearance ",
	12 => "Chemical Analysis",
	13 => "Physicals",
	14 => "Safety ",
	15 => "Seam Slippag"
);
// Library Module ends //

// Merchandising
$approval_status = array(1 => "Submitted", 2 => "Rejected", 3 => "Approved", 4 => "Cancelled", 5 => "Re-Submitted");
$order_status = array(1 => "Confirmed", 2 => "Projected");
$region = array(1 => "Asia", 2 => "Africa", 3 => "Australia", 4 => "Antarctica", 5 => "Europe", 6 => "North America ", 7 => "South America");
$packing = array(1 => "Solid Color Solid Size", 2 => "Assort Color Solid Size", 3 => "Solid Color Assort Size", 4 => "Assort Color Assort Size");
//$ship_mode=array(1=>"Air",2=>"Sea");
$cut_up_array = array(1 => "1st Cut-Off", 2 => "2nd Cut-Off", 3 => "3rd Cut-Off");
$product_dept = array(1 => "Mens", 2 => "Ladies", 3 => "Teenage-Girls", 4 => "Teenage-Boys", 5 => "Kids-Boys", 6 => "Infant", 7 => "Unisex", 8 => "Kids-Girls", 9 => "Baby", 10 => "Kids", 11 => "Women");
//$pord_dept=array(1=>"Menz",2=>"Ladies",3=>"Teen Age-Girls",4=>"Teen Age-Boys",5=>"Kids",6=>"Infant",7=>"Intimates");
$shift_name = array(1 => "A", 2 => "B", 3 => "C");
$fabric_shade = array(1 => "A", 2 => "B", 3 => "C", 4 => "D");
$country_type = array(1 => "General", 2 => "Special");
$product_types = array(1 => "Brief", 2 => "Bra", 3 => "Top", 4 => "Bottom", 5 => "Boxer");
$feeder = array(1 => "Full Feeder", 2 => "Half Feeder");

$product_category = array(1 => "Outwears", 2 => "Lingerie", 3 => "Sweater", 4 => "Socks", 5 => "Fabric", 6 => "Top", 7 => "Bottom");
$aop_qc_reject_type = array(1 =>"Design  Mistake",2 =>"Design Setting Out",3 => "Color Bleed", 4 => "Color Flashing", 5 =>"Print Color Shade", 6 =>'Print Overlapping',7 => 'Color Spot',8 =>'Sheading',9 =>'Fabric Ground Shade Deviation', 10 =>'Repeat Measurement',11 => 'Fabric Shrinkage', 12 =>'Fabric Side Curling',13 =>'Gsm Low',14 =>'Gsm High', 15 => 'Pin Hole', 16 =>'Excess GSM Hole & Shade Hole',17 => 'Repeat Mark',18 => 'Dia Variations', 19 => 'Bowing', 20 =>'Print Color Missing',21 =>'Print in Wrong side',22 =>'Print in Wrong Way', 23 =>'Pentration Variations', 24 => 'Others');

//$product_category=array(1=>"Garments",2=>"Intimates",3=>"Sweater",4=>"Socks",5=>"Fabric");
/*$garments_item=array(
1=>"T-Shirt-Long Sleeve",
2=>"T-Shirt-Short Sleeve",
3=>"Polo Shirt-Long Sleeve",
4=>"Polo Shirt-Short Sleeve",
5=>"Tank Top",
6=>"T-Shirt 3/4 ARM",
7=>"Hoodies",
8=>"Henley",
9=>"T-Shirt-Sleeveless",
10=>"Raglans",
11=>"High Neck/Turtle Neck",
12=>"Scarf",
14=>"Blazer",
15=>"Jacket",
16=>"Night Wear",
17=>"Marry Dress",
18=>"Ladies Long Dress",
19=>"Girls Dress",
20=>"Full Pant",
21=>"Short Pant",
22=>"Trouser",
23=>"Payjama",
24=>"Romper Short Sleeve",
25=>"Romper Long Sleeve",
26=>"Romper Sleeveless",
27=>"Romper",
28=>"Legging",
29=>"Three Quater",
30=>"Skirts",
31=>"Jump Suit",
32=>"Cap",
33=>"Tanktop Pyjama",
34=>"Short Sleeve Pyjama",
35=>"Jogging Pant",
36=>"Bag",
37=>"Bra",
38=>"Underwear Bottom",
39=>"Sweat Shirt",
40=>"Singlet",
41=>"Teens Singlet",
42=>"Boxer",
43=>"Stripe Boxer",
44=>"Teens Boxer",
45=>"Jersy Boxer",
46=>"Panty",
47=>"Slip Brief",
48=>"Classic Brief",
49=>"Short Brief",
50=>"Mini Brief",
51=>"Bikini",
52=>"Lingerie",
53=>"Bikers",
54=>"Underwear",
60=>"Plain Socks",
61=>"Rib Socks",
62=>"Jacuard/Patern Socks",
63=>"Heavy Gauge Socks",
64=>"Sports Socks",
65=>"Terry Socks",
66=>"Tight Socks",
67=>"Sweat Pant",
68=>"Sports Ware",
69=>"Jogging Top",
70=>"Long Pant",
71=>"Pirates Pant",
72=>"Bolero",
73=>"Strap Top",
74=>"Ladies Gypsy",
75=>"Long Sleeve Body",
76=>"Tank Top Body",
77=>"Underwear Top",
78=>"Whiper",
79=>"Sleeping Bag",
80=>"Romper Long Sleeve Boys",
81=>"Romper Long Sleeve Girls",
82=>"Romper Long Sleeve Unisex",
83=>"Baby Bodies",
84=>"TQ Pintuck Tee",
85=>"LS Pintuck Tee",
86=>"Twist Neck Pintuck",
87=>"Maxi dress",
88=>"Lace Gupsy",
89=>"Gypsy Tie Neck Tee",
90=>"V-neck Tunic",
91=>"Roll Top Jogger",
92=>"Soft Touch Jogger",
93=>"Plaited Jogger",
94=>"Loopback Jogger",
95=>"Jegging",
96=>"Mens Night Wear",
97=>"Spots Shirt",
98=>"Romper B",
99=>"Inner Top",
100=>"Outer Top",
101=>"Mock Long Sleeve T- Shirt",
102=>"Round Neck Long Sleeve T- Shirt",
103=>"V Neck Long Sleeve T-Shirt",
104=>"BLANKET",
105=>"Playsuit",
106=>"Sweater",
107=>"Jumper",
108=>"Cardigan",
109=>"Nightware-Top",
110=>"Nightware-Bottom",
111=>"V Neck Short Sleeve T-Shirt ",
112=>"R Neck Short Sleeve T-Shirt",
113=>"Mens Trunk",
114=>"Mens Brief",
115=>"Mens Boxer",
116=>"Boys Boxer",
117=>"Boys Brief",
118=>"Girls Brief",
119=>"Ladies Hipster",
120=>"Ladies Thong",
121=>"Mens Pattern Trunk",
122=>"Mens Pattern Brief",
123=>"Mens Pattern Boxer",
124=>"T-Shirt-Long Sleeve-1",
125=>"T-Shirt-Long Sleeve-2"
);*/


$garments_item = return_library_array("select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name", "id", "item_name");


$quality_label = array(1 => "Platinum", 2 => "Gold", 3 => "Silver");
$fbooking_order_nature = array(1 => "QEI", 2 => "QR", 3 => "ADVERT + QEI", 4 => "ADVERT + QR", 5 => "QEI + QR", 6 => "SPEED", 7 => "NOS", 8 => "ADVERT",9=>"QEI + QR + ADVERT");
$composition = return_library_array("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name", "id", "composition_name");



/*$composition=array(
1=>"Cotton",
2=>"Spandex",
3=>"Viscose",
4=>"Polyester",
5=>"Organic",
6=>"BCI Cotton",
7=>"Modal",
8=>"Conventional",
9=>"ECRU Melange",
10=>"Elastane",
11=>"Carded Flo Fair",
12=>"Linen",
13=>"Slub",
14=>"Coolmax",
15=>"Creap Viscos",
16=>"Filament Viscose",
17=>"Merc Viscose",
18=>"Melange",
19=>"Ecru.Melange [Ctn 99%-Vis 1%]",
20=>"G.Melange [Ctn 95%-Vis 5%]",
21=>"G.Melange [Ctn 90%-Vis 10%]",
22=>"G.Melange [Ctn 85%-Vis 15%]",
23=>"G.Melange [Ctn 80%-Vis 20%]",
24=>"PC [Polyester 65%-Ctn 35%]",
25=>"Ecru.Melange[Ctn98%-Vis2%]",
26=>"PC [Polyester 90% - Ctn 10%]",
27=>"PC [Polyester 85% - Ctn 15%]",
28=>"PC [Polyester 80% - Ctn 20%]",
29=>"PC [Polyester 75% - Ctn 25%]",
30=>"PC [Polyester 70% - Ctn 30%]",
31=>"PC [Polyester 65% - Ctn 35%]",
32=>"PC [Polyester 60% - Ctn 40%]",
33=>"PC [Polyester 50% - Ctn 50%]",
34=>"CVC [Ctn 90% - Polyester 10%]",
35=>"CVC [Ctn 85% - Polyester 15%]",
36=>"CVC [Ctn 80% - Polyester 20%]",
37=>"CVC [Ctn 75% - Polyester 25%]",
38=>"CVC [Ctn 70% - Polyester 30%]",
39=>"CVC [Ctn 65% - Polyester 35%]",
40=>"CVC [Ctn 60% - Polyester 40%]",
41=>"Cotton-Modal [Ctn 50% - Modal 50%]",
42=>"Cotton-Viscose [Ctn 50% - Viscose 50%]",
43=>"Wool",
44=>"Silk",
45=>"CM [Ctn 62% - Modal 38%]",
46=>"Lyocell",
47=>"G.Melange [Ctn 93% - Viscose 7%]",
48=>"G.Melange [Ctn 60% - Viscose 40%]",
49=>"Tencel",
50=>"CVC [Ctn 2.80% - Polyester 20%]",
51=>"Inject",
52=>"Cotton-Lurex [Ctn 95% - Lurex 5%]",
53=>"M.Lurex [Ctn 90% - Vis 5% - Lurex 5%]",
54=>"Viscose-Polyester [Vis 50% - Polyester 50%]",
55=>"G.Melange [Ctn 92% - Viscose 8%]",
56=>"G.Melange [Ctn 70% - Viscose 30%]",
57=>"CD Melange",
58=>"CB Melange",
59=>"G.Melange [Ctn 94% - Viscose 6%]",
60=>"G.Melange [Viscose 60% - Ctn 40%]",
61=>"CVC [Ctn 58% - Polyester 38%]",
62=>"Cotton-Modal [Ctn 48% - Modal 52%]",
63=>"Polyester-Linen [Polyester 85% - Linen 15%]",
64=>"Polyester-Linen [Polyester 65% - Linen 35%]",
65=>"Rayon",
66=>"Polyester-Rayon [Polyester 94% - Rayon 6%]",
67=>"Polyester-Rayon [Polyester 2% - Rayon 98%]",
68=>"PC [Polyester 2% - Ctn 98%]",
69=>"CVC [Ctn 60% - Polyester 37%- Polyester Neps 3%]",
70=>"PVC [Poly-50%, Ctn-25%, Vis-25%]",
71=>"CVC [Org-50%, Poly-38%, Vis-12%]",
72=>"Acrylic",
73=>"Organic Cotton",
74=>"G.Melange [Organic 95% - Vis 5%]",
75=>"Organic Real Melange",
76=>"Organic Cotton 50% - Cotton 50%]",
77=>"CVC [60% Org Ctn-40% Poly slub]",
78=>"CVC [55% Ctn-34% Poly-11% Viscos]",
79=>"Cotton 70% - Linen 30%",
80=>"Core Spun",
81=>"Polyamaid",
82=>"Nylon",
83=>"P/C/Cationic [Polyester - 55% Ctn - 25% Cationic - 20%]",
84=>"P/C/Cationic [Polyester - 65% Ctn - 25% Cationic - 10%]",
85=>"PCR [Polyester - 50% Ctn - 38% Rayon - 12%]",
86=>"Benetton",
87=>"Glitter",
88=>"Vortex [Ctn - 27% Poly - 65% Viscose - 8%]",
89=>"PVC [Poly - 65% Ctn - 27% Viscose - 8%]",
90=>"PV [Poly 70% - Viscose 30%]",
91=>"Linen Lyocell [Linen 50% - Lyocell 50%]",
92=>"PVC [Poly 50% - Vis 25% - Ctn 25%]",
93=>"[45 % Ctn + 55% Silk]",
94=>"[92 % Modal + 8% Cashmere]",
95=>"Cupro",
96=>"[94 % Viscose-65 Woll]",
97=>"Winter linen",
98=>"Modal 50% - Viscose 30% - Poly 20%",
99=>"Modal 50% - Ctn 30% - Poly 20%",
100=>"Ctn 50% - Poly 50%",
101=>"PCV [50% Poly - 25% Ctn - 25% Vis]",
102=>"Excel",
103=>"Bross Melange",
104=>"100% Spun Polyester",
105=>"100% Filament Polyester",
106=>"Loop 100% Cotton",
107=>"Loop 60% Cotton 40% Polyester",
108=>"Loop 100% Polyester",
109=>"PCV [Poly 50% - Ctn 38% - Vis 12%]",
110=>"CVC [Ctn 97% - Poly 3%]",
111=>"PV [Poly 70% - White Viscose 30%]",
114=>"CM[Ctn 60% - Modal 40%]",
115=>"PV[Poly 50% - Vis 50%]",
116=>"Slub[BCI Ctn 60% - Poly 40%]",
117=>"60% BCI Ctn - 40% Poly Melange",
118=>"Poly 90% - Linen 10%",
119=>"VP[Vis 90% - Poly 10%]",
120=>"TL[Tencel 50% - Linen 50%]",
121=>"PV[Poly 65% - Viscose 35%]",
122=>"CVP[CTN 58%-Viscose 35%-Poly7%]",
123=>"CP[CTN 95%-Poly 5%]",
124=>"VP[Vis 70%-Poly 30%]",
125=>"PV[Poly 75%-Vis 25%]",
126=>"CVC[Ctn 97%-Poly Neps 3%]",
127=>"PCV[Poly 38%-Ctn 50%-Vis 12%]",
128=>"LPWC [Lenzing vis 70%-Poly 20%-Wool 5%-Cashmere 5%]",
129=>"CPV [50%Ctn,38% Poly,12%Vis]",
130=>" CPV [Cotton 80% Ploy 17% Viscose 3%]",
131=>" Micro Modal Unifeel",
132=>" RP [ Rayon 90%- Poly 10%]",
133=>" TP [ Tencel 62% - Poly 38%]",
134=>" Inject [98%ctn 2% polyester]",
135=>"[cotton 95% - Viscose 5%]",
136=>"Neps yarn [cotton 98% - Poly 2%]",
137=>"[Acrylic 62% - Poly 38%]",
138=>"[Acrylic 77% - Rayon 22% - Nylon 1%]",
139=>"[Viscose 83% - Ctn17%]",
140=>"Cotton Melange",
141=>"[ Polyester 50% Linen 50%]",
142=>"[ Viscose 70% Wool 30%]",
143=>"PVC[Polyester - 50% Cotton - 35% - Viscose - 15% ]",
144=>"PCR [Polyester - 52% Cotton - 35% Rayon - 13% ]",
145=>"[Cotton 97% Viscose 3%]",
146=>"Ecru Melange[BCI Ctn 99% Vis 1%]",
147=>"Poly-Modal [Polyester 65%  Modal 35%]",
148=>"CVP [Cotton 47%  Viscose 47%  polyester 6%]",
149=>"VP [Viscose 95% Poly 5% ]",
150=>"G.Melange[85%Organic cotton-15%Viscose]",
151=>"VP [65% Viscose 35% Poly]",
152=>"Poly-Linen [99% poly 1% linen]",
153=>"PV [Poly 68% Vis 32%]",
154=>"Bamboo",
155=>"PVC[Poly 52% Cotton 33% Viscose 15%]",
156=>"PV[Poly 97% -Viscose 3%]",
157=>"Spun Polyester",
158=>"100% Cotton",
159=>"VL[90 % Viscose - 10 % Linen]",
160=>"TS[90 % Tencel - 10 % Silk]",
161=>"VT[70 % Viscose - 30 % Tencel]",
162=>"PL[90 % Polyestere - 10 % Linen]",
163=>"ML[65 % Modal - 35 % Linen]",
164=>"PLV[Polyester 82%-Linen 13%-Viscose 5%]",
165=>"PV[Poly 3%- Viscose 97%]",
166=>"PVC [Poly 65%- Viscose 25%- Ctn 10%]",
167=>"[Cotton 85%- Linen 15%]",
168=>"CVC[Ctn 57% - Poly 43%]",
169=>"[Polyester 76%- Lyocell 24%]",
170=>"CPV [Ctn 67%-Polyester 30%-Viscose 3%]",
171=>"VP[Viscose 80%-Polyester 20%]",
172=>"VP[Viscose 60%-Poly 40%]",
173=>"CW [ Ctn 70%-Wool 30%]",
174=>"CPV [Ctn 50%-Poly 40%-Vis 10%]",
175=>"PCV[Poly 38%-Ctn 32%-Vis 30%]",
176=>"CV[Ctn 95%-Vis 5% ]",
177=>"Twisted",
178=>"Modal Polyester [Modal 75%-Poly 25%]",
179=>"PV[Poly 60%-Viscose 40%]",
180=>"Cotton 92.5%-Viscose 7.5%",
181=>"Pro-Viscose [ Lenzing Viscose 70%- Tencel LF 30%]",
182=>"GM Slub [ Ctn 90%- Vis10%]",
183=>"G.Melange [Ctn 98%- Vis 2%]",
184=>"VM [White Vis 95% - Black Vis 05%]",
185=>"CL[ Ctn 80% - Linen 20% ]",
186=>"VL [ Viscose 80%- Linen 20% ]",
187=>"VP[viscose 97%-Poly 3%]",
188=>"PC [ Polyster 57% - Cotton 43% ]",
189=>"G. Melange [Ctn 65% - Vis 35%] ",
190=>"CA [ Cotton 50% - Acrylic 50% ]",
191=>"PC [Recycled poly 65% Cotton 35% ]",
192=>"PCR [Polyester-50% Cotton- 35% Rayon-15%]",
193=>"Lurex",
194=>"Cotton Lurex",
195=>"Melange Lurex",
196=>"Pima Modal [Pima Cotton 60%- Modal 40%]",
197=>"Cotton-Melange[60% cotton 40% B.Cotton]",
198=>"Cotton-Melange[75% cotton 25% B.Cotton]",
199=>"Rayon Lurex [Rayon 75% - Lurex 25% ]",
200=>"Pes Rayon [ Pes 77% - Rayon 23% ]",
201=>"Linen Rayon Neps [ Linen 55% - Rayon Neps 45% ] ",
202=>"Rayon Poly [ Rayon 50 % - Poly 50% ]",
203=>"VCPL [Vis 40% - Ctn 35% - Poly 20% - Golden Lurex 5% ]",
204=>"Linen Viscose [ Linen 75% - Vis 25% ]",
205=>"[poly 81% - rayon 17 % - polyurethane 2%]",
206=>"Organic FT Cotton",
207=>"Lenzing Viscose",
208=>"Cotton Slub",
209=>"CVC [Loop Cotton 65% Polyester 35%]",
210=>"Modal-Cotton[ Modal 70%-Cotton 30%]",
211=>"CL [Cotton 85 %-Linen 15%]",
212=>"CVB[Ctn42%- Vis33%- Black Ctn25%]",
213=>"Organic Cotton 50% - Lenzing Modal 50%",
214=>"G.Melange[Organic Cotton 50% - Lenzing Modal 50%]",
215=>"Blue Melange[Organic Cotton 50% - Lenzing Modal 50%]",
216=>"[50% Organic CTN -30 CTN-20% Polyester]",
217=>"CVP [Cotton 50% - Viscose 25% - Polyester 25% ]",
218=>"Rayon Poly [Rayon 85% - Polyester 15%]",
219=>"[Polyester 70% - Rayon 25% - Elastane 5%]",
220=>"Ecru.Melange [Ctn 97%- Vis 3%]",
221=>"CV [ Ctn 80% - Vis 20% ]",
222=>"PV [ Poly 83 % - Vis 17 % ]",
223=>"CPL [ Ctn 50% - Poly 40% - Linen 10%]",
224=>"TC [Tencel 70% Cotton 30%]",
225=>"PCV [ Poly 30% - Ctn 53.2% - Vis 16.8]",
226=>"CV [ Ctn 60% - Vis 40% ]",
227=>"Organic Cotton 50%-Tencel 50%",
228=>"CV[Cotton65%- Viscose35%]",
229=>"[50%Org.CTN-40% POL-10% CTN]",
230=>"Pima Cotton",
231=>"VC [ Viscose 83% - Cotton 17 % ]",
232=>"CV[Cotton 55%-Viscose 45%]",
233=>"[Viscose 60% - Modal Melange 40%]",
234=>"PVL [Polyester 55%- Viscose 25%- Linen 20%]",
235=>"CVC [50% Org.Ctn-30% Conv.Ctn-20% Polyester]",
236=>"CVC [60% Ctn-40% Poly Slub]",
237=>"PL [Polyester 80% - Linen 20%]",
238=>"Polyester Cationic",
239=>"Melange [viscose 60%- Cotton 40%]",
240=>"CVC [Organic Cotton 80% Polyester 20%]",
241=>"MC[Lenzing Modal 50% Organic Cotton 50%]",
242=>"Slub [Rayon 60% - Cotton 40%]",
243=>"[Organic Cotton 50% - Poly 40% - Cotton 10%]",
244=>"Filament",
245=>"CVC[Cotton 60%-Viscose 40%]",
246=>"[Ctn 70%-Poly 25%-Vis 5%] ",
247=>"CVC [Ctn 87%-Poly 13%] ]",
248=>"[Ctn 82%-Poly 14%-Vis 4%]",
249=>"CVCK[96%-4%] NY",
250=>"CVCK[97%-3%] NAPY",
251=>"PCVK[2%-88-10%] NAPY",
252=>"PC[Polyester 96%-Cotton 4%]",
253=>"[ 50% Viscose, 48% Polyester, 2% Elastane]",
254=>"PVC[Polyester-50% Viscose-12% Snow-38%]",
255=>"PVC[Polyester-20% Viscose-65% Cotton-15%]",
256=>"Conventional Cotton",
257=>"[Ctn 83%-Poly 13%-Vis 4%]",
258=>"[Ctn 82%-Poly 13%-Vis 5%]",
259=>"40%linen -60% Lyocell",
260=>"90% Viscose 10% glitter",
261=>"PVC [Poly 50%-Cot 25%- Vis 25%]",
262=>"BC[ Bamboo 60%- Cotton 40%]",
263=>"EC [Excel 70%- Cotton 30%] ",
264=>"[Ctn 93% - Visc 4% - Lurex 3%]",
265=>"[Ctn 97% - Lurex 3%]",
266=>"[Organic 50% - Conventional Ctn 49% - Visc 1%]",
267=>"TC [ Cotton 60%- T 40%]",
268=>"PC [Poly 58%- Ctn42%]",
269=>"BCV [BCI Ctn 95% - Visc 5%]",
270=>"Cotton-Silk [Cotton 85%- Silk 15%]",
271=>"Cotton-Silk[Ctn 70% - Silk 30%]",
272=>"[Cotton95% - Injected Viscose5%]",
273=>"[Rayon 55%-Polyester 34%-Angelina 1%]",
274=>"RPS[Rayon 66% - Poly 27% - Spandex 7%]",
275=>"VPS[Viscose 66% - Poly 27% - Spandex 7%]",
276=>"RL [Rayon 85% - Linen 15%]",
277=>"CPV [Ctn80%-Poly10% -Vis10%]",
278=>"Ecro Melange[Org.Ctn99% - Visc 1%]",
279=>"[Cotton 92.5% - Viscose 7.5%]",
280=>"VC [Lenzing Viscose 50% - Organic Cotton 50%]",
281=>"Neps Yarn",
282=>"CVC [Ctn 98% - Poly 2%]",
283=>"VL [Viscose 70% - Linen30%]",
284=>"CVC [Ctn 91% - Polyester 9% ]",
285=>"[Cotton 96% - Viscose 4%]",
286=>"PV[Polyester 95% - Viscose 5%]",
287=>"VL [ Viscose 50% - linen 50% ]",
288=>"FT Cotton",
289=>"CVC [60% FT Ctn-40% Poly]",
290=>"CVC [80% FT Ctn-20% Poly]",
291=>"Poly",
292=>"Org Cotn 50%-Modal 50%",
293=>"[Cotton 75% - Viscose 25%]",
294=>"[Viscose 65%-Linen 30%-poly 5%]",
295=>"G.Melange [Ctn 92.5%-Vis 7.5%]",
296=>"[Cotton 50%- Modal 45% -Melange 5%]",
297=>"[Org ctn 50% - BCI ctn 48% - polyester 2%]",
298=>"G.Melange[Org Cotn 95% -Vis 5%]",
299=>"PC[Poly 52%- Cotn 48%]",
300=>"PV[Polyester 80% - Viscose 20%]",
301=>"[Viscose 94% -acrylic 5%- Poly 1%]",
302=>"[Poly 84% - Viscose 16% ]",
303=>"RP [Rayon 55%- Poly 45%]",
304=>"CVC[cotton 55%- Poly 45%]",
305=>"Inject[Poly 90.3% - Ctn 9.7%]",
306=>"CVC [Ctn 52% - Poly 48%]",
307=>"[Poly 47% - Visc 51% - Elast 2%]",
308=>"BCI[Ctn-50% Modal-50%]",
309=>"CL[Cotton60%- Lurex40%]",
310=>"CVC[ BCI Ctn80% -Poly20% ]",
311=>"G.melange[ BCI Ctn 85%- Visc15%]",
312=>"Neppy[Ctn90% - Poly10%]",
313=>"[Poly94%-Elastan6%]",
314=>"Pina",
315=>"Banana",
316=>"PC[Ctn 88% - Nylon 12% ]",
317=>"LC [Lyocell 70%- Cotton 30%]",
318=>"CT[Ctn 89% - Teton 11%]",
319=>"PR[Poly 75% - Rayon 25%]",
320=>"LC[Linen 55% - Ctn 45%]",
321=>"CMN[Ctn 94% - Metallic 3% - Nylon 3%]",
322=>"CT[Ctn 85% - Teton 15%]",
323=>"[Lyocell 60% - Linen 40%]",
324=>"[Polyester 60% - Polyamide 40%]",
325=>"[Polymide 60%- Polyester 40%]",
326=>"G.Melange [Ctn 73%-Polyester 22%-Vis 5%]",
327=>"PVC[Poly - 50% Vis - 32% CTN - 7%]",
328=>"CVC [Ctn 68% - Polyester 32%]",
329=>" PC [Polyester 95% - Ctn 5%]",
330=>"[Viscose 71% - Poly 24% - Spandex 5%]",
331=>"PV[Polyester62%-Viscose38%]",
332=>"[Org Ctn 50%- BCI Ctn50%]",
333=>"Metalisde 52% - Polyamide 48%",
334=>"CVC[Ctn 96% - Poly 4%]",
335=>"[Poly 90% - Inj Cotn 10%]",
336=>"[Cotn 95%- Inj poly 5%]",
337=>"100% Inj Anthra Mel Yarn",
338=>"[Cotn 94%-Vis 4%-Lurex 2%]",
339=>"[Pes 47% - Vis 22% - Cotn 18% - modal 13%]",
340=>"CVL [Cotn 88% - Vis 10% - Lurex 2% ]",
341=>"[Poly-35% vis-35% Cationic-30%]",
342=>"VC[Vis 52%-Ctn 48%]",
343=>"[Org.Ctn 50%- BCL Ctn 50%]",
344=>"PVC[Poly 43%-Vis 11%-Cotton 46%]",
345=>"PC[Poly 8%-Ctn 92%]",
346=>"CL[Cotton 50% - Linen 50%]",
347=>"VC[Vis 65%-Ctn 35%]",
348=>"CMV[Ctn 50%- Modal 45%- Vis 5%]",
349=>"Flake[Poly 73%- Ctn21%- Vis 6%]",
350=>"G.Melange[Ctn 50% - Modal 43% - Vis 7%]",
351=>"CV[Cotton42%-Viscoes58%]",
352=>"CMIA [Ctn 50% - Lenzing Modal 50%]",
353=>"CVC [BCI Ctn 60% - Poly 40%]",
354=>"VA[Viscose 75% - Acrylic 25%]",
355=>"[Viscose96% - Spandex 4%]",
356=>"Organic Ctn 80% - Polyester 20%",
357=>"G.Melange [Ctn 88% - Vis 12%]",
358=>"[Org. Ctn 97%- Poly 3%]",
359=>"CVC[ BCI Cotton 97% - Poly 3%]",
360=>"[Ctn 90% - Vis 6% - Poly 4%]"
);
 */
/*$unit_of_measurement=array(
"PIECES"=>array(
01=>"Pcs",
02=>"Dzn",
03=>"Grs",
04=>"GG"
),
"WEIGHT"=>array(
10=>"Mg",
11=>"Gm",
12=>"Kg",
13=>"Quintal",
14=>"Ton"
),
"LENGTH"=>array(
20=>"Km",
21=>"Hm",
22=>"Dm",
23=>"Mtr",
24=>"Dcm",
25=>"CM",
26=>"MM",
27=>"Yds",
28=>"Feet",
29=>"Inch"
),
"LIQUID"=>array(
40=>"Ltr",
41=>"Ml"
),
"OTHERS"=>array(
50=>"Roll",
51=>"Coil",
52=>"Cone",
53=>"Bag",
54=>"Box",
55=>"Drum",
56=>"Bottle",
57=>"Pkt",
58=>"Set"
)
);*/
$unit_of_measurement = array(
	1 => "Pcs",
	2 => "Dzn",
	3 => "Grs",
	4 => "GG",
	10 => "Mg",
	11 => "Gm",
	12 => "Kg",
	13 => "Quintal",
	14 => "Ton",
	15 => "Lbs",
	20 => "Km",
	21 => "Hm",
	22 => "Dm",
	23 => "Mtr",
	24 => "Dcm",
	25 => "CM",
	26 => "MM",
	27 => "Yds",
	28 => "Feet",
	29 => "Inch",
	30 => "CFT",
	31 => "SFT",
	40 => "Ltr",
	41 => "Ml",
	50 => "Roll",
	51 => "Coil",
	52 => "Cone",
	53 => "Bag",
	54 => "Box",
	55 => "Drum",
	56 => "Bottle",
	57 => "Pack",
	58 => "Set",
	59 => "Can",
	60 => "Each",
	61 => "Gallon",
	62 => "Lachi",
	63 => "Pair",
	64 => "Lot",
	65 => "Packet",
	66 => "Pot",
	67 => "Book",
	68 => "Culind",
	69 => "Rim",
	70 => "Cft",
	71 => "Syp",
	72 => "K.V",
	73 => "CU-M3",
	74 => "Bundle",
	75 => "Strip",
	76 => "SQM",
	77 => "Ounce",
	78 => "Cylinder",
	79 => "Course",
	80 => "Sheet",
	81 => "RFT",
	82 => "Square Inch",
	83 => "Carton"
);
$pord_dept = array(1 => "Mens", 2 => "Ladies", 3 => "Teen Age-Girls", 4 => "Teen Age-Boys", 5 => "Kids", 6 => "Infant", 7 => "Intimates");

//merchandise variable settings Sheet
$order_tracking_module = array(12 => "Sales Year started", 14 => "TNA Integrated", 15 => "Pre Costing : Profit Calculative", 18 => "Process Loss Method", 19 => "Consumtion Basis", 20 => "Copy Quotation", 21 => "Conversion Charge From Chart", 22 => "CM Cost Predefined Method (Pre-cost)", 23 => "Color From Library", 24 => "Yarn Dyeing Charge (In WO) from Chart", 25 => "Publish Shipment Date", 26 => "Material Control", 27 => "Commercial Cost Predefined Method-Pre-Costing", 28 => "Gmt Number repeat style", 29 => "Duplicate Ship Date", 30 => "Image Mandatory", 31 => "TNA Process type", 32 => "Po Update Period", 33 => "Po Receive Date", 34 => "Inquery ID Mandatory", 35 => "Trim Rate", 36 => "CM Cost Predefined Method (Price Quotation)", 37 => "Budget Validation", 38 => "S.F. Booking Before M.F. 100%", 39 => "Lab Test Rate Update", 40 => "Colar Culff Percent", 41 => "Pre-cost Approval", 42 => "Report Date Catagory", 43 => "TNA Process Start Date", 44 => "Season Mandatory", 45 => "Excess Cut Source in Order Entry", 46 => "Allow Ship Date on Off Day", 47 => "Style & SMV Source/Combinations", 48 => "Default Fabric Nature", 49 => "Default Fabric Source", 50 => "BOM Page Setting", 51 => "Min Lead Time Control", 52 => "PO Entry Limit On Capacity", 53 => "Cost Control Source", 54 => "Efficiency Source For Pre-Cost", 55 => "Work Study Mapping", 56 => "Embellishment Budget On", 57 => "Currier Cost Predefined Method", 58 => "Commercial Cost Predefined Method-Price Quotation", 59 => "Fabric Source For AOP", 60 => "Yarn Issue Validation Based on Service Approval", 61 => "Price Quotation Approval", 62 => "Textile TNA Baseed On", 63 => "Sequence validation with Booking", 64 => "Sew Comp. and location mandatory in order entry", 65 => "Excess Cut % Level in Order Entry",66=>"Fabric Req. Qty. Source",67=>"Location Wise Financial Parameter" ,68=>"QC Cons. From",69=> "Yarn Dyeing Work Order Used");


$pre_cost_approval = array(1 => "Electronic Approval", 2 => "Manual Approval");
$capacity_exceed_level = array(1 => "Confirmed Order Qty-LC", 2 => "Confirmed Order Value-LC", 3 => "Confirmed Order Mint-LC", 4 => "Proj & Conf. Order Qty-LC", 5 => "Proj & Conf. Order Value-LC", 6 => "Proj & Conf. Order Mint-LC", 7 => "Confirmed Order Qty-Working", 8 => "Confirmed Order Value-Working", 9 => "Confirmed Order Mint-Working", 10 => "Proj & Conf. Order Qty-Working", 11 => "Proj & Conf. Order Value-Working", 12 => "Proj & Conf. Order Mint-Working");
$process_loss_method = array(1 => "Markup Method", 2 => "Margin method");
$embellishment_budget_on = array(1 => "Order Qnty", 2 => "Plan Cut Qnty");
$consumtion_basis = array(1 => "Cad Basis", 2 => "Measurement Basis", 3 => "Marker Basis");
//$wo_category = array(2=>"Knit Fabrics",3=>"Woven Fabrics",4=>"Accessories",13=>'Grey Fabric(Knitt)',14=>'Grey Fabric(woven)',12=>"Services");
$gmts_nature = array(1 => "Knit Garments", 2 => "Woven Garments", 3 => "Sweater");
$incoterm = array(1 => "FOB", 2 => "CFR", 3 => "CIF", 4 => "FCA", 5 => "CPT", 6 => "EXW", 7 => "FAS", 8 => "CIP", 9 => "DAF", 10 => "DES", 11 => "DEQ", 12 => "DDU", 13 => "DDP", 14 => "DAP");
$fabric_source = array(1 => "Production", 2 => "Purchase", 3 => "Buyer Supplied", 4 => "Stock");
$color_range = array(1 => "Dark Color", 2 => "Light Color", 3 => "Black Color", 4 => "White Color", 5 => "Average Color", 6 => "Melange", 7 => "Wash", 8 => "Scouring", 9 => "Extra Dark", 10 => "Medium Color", 11 => "Super Dark", 12 => "Royal color");
$costing_per = array(1 => "For 1 Dzn", 2 => "For 1 Pcs", 3 => "For 2 Dzn", 4 => "For 3 Dzn", 5 => "For 4 Dzn");
$delay_for = array(1 => "Sample Approval Delay", 2 => "Lab Dip Approval Delay", 3 => "Trims Approval Delay", 4 => "Yarn In-House Delay", 5 => "Knitting Delay", 6 => "Dyeing Delay", 7 => "Fabric In-House Delay", 8 => "Trims In-House Delay", 9 => "Print/Emb Delay", 10 => "Line Insufficient", 11 => "Worker Insufficient", 12 => "Bulk Prod. Approval Delay", 13 => "Traget Falilure", 14 => "Inspection Fail", 15 => "Production Problem", 16 => "Quality Problem");
//$body_part=array(1=>"Main Fabric",2=>"Collar",3=>"Culf",4=>"Rib",5=>"Hood",6=>"Pocketing",7=>"Bottom Rib",8=>"Sleeve",9=>"Back Part",10=>"Front Part");

/*$body_part=array(1=>"Main Fabric Top",2=>"Collar",3=>"Cuff",4=>"Rib",5=>"Flap",6=>"Hood",7=>"Pocketing",8=>"Bottom Rib",9=>"Sleeve",10=>"Back Part",11=>"Front Part",12=>"Facing Fabric",13=>"Binding",14=>"Body part-1",15=>"Body part-2",16=>"Body part-3",17=>"Body part-4",18=>"Shoulder",19=>"Hood lining",20=>"Main Fabric Bottom",21=>"Pocketing Bottom",22=>"Bottom Rib Bottom",23=>"Waist belt insert",24=>"Waist belt",25=>"Back Loop",26=>"Yoke-Back",27=>"Epaulate/Tab",28=>"Assembly",29=>"Fly",30=>"Back Pocket FCG",31=>"Back Flap",32=>"Loop",33=>"Side Flap",34=>"Side Pocket FCG",35=>"Side Pocket",36=>"Front Top",37=>"Front/Back Waist Band",38=>"Tape",39=>"Back Pocket",40=>"Placket",41=>"Neck Tape",42=>"Piping",43=>"Pocket binding",44=>"Neck band",45=>"Neck insert",46=>"Drawstring",47=>"Contrast insert",48=>"Mesh insert",49=>"Tricot mesh",50=>"Woven insert",51=>"Double layer",52=>"Side slit",53=>"Yoke-Front",54=>"Applique fabrics",55=>"Neck Rib",56=>"Inner Neck",57=>"Sleeve Layer",58=>"Half Moon",59=>"Neck",60=>"Moon Back",61=>"Back Top",62=>"Carpenter Pocket",63=>"Waist Belt",64=>"Front Pocket",65=>"Coin Pocket",66=>"Welt Pocket",67=>"Front Welt Pkt",68=>"Collar Tai",69=>"Inner Front Body",70=>"Facg Tape",71=>"Sleeve Tape",72=>"Thigh Flap",73=>"Thigh Pocket",74=>"Waist Rib",75=>"Neck Binding",76=>"Bow",77=>"Half Moon+Under layer",78=>"Neck tape loops",79=>"Zipper Ups Binding",80=>"Bottom cuff and pocket binding",81=>"Back Tape",82=>"Fake Sleeve",83=>"Chest Piping",84=>"Waist Belt + Hem",85=>"Neck + Bottom + Cuff Rib",86=>"Drawstring + Pocket Binding",87=>"Neck + Bottom + Collar Rib",88=>"Neck + Cuff",89=>"Bottom + Cuff Rib",90=>"Tipping at Rib",91=>"Neck + Armhole Binding",92=>"Patch-Bottom",93=>"Left Sleeve",94=>"Right Sleeve",95=>"Back Part + Front Part",96=>"Ruffles",97=>"Collar Stand",98=>"Back Hem",99=>"Back Facing",100=>"Back Part-2",101=>"Band",102=>"Bottom Hem",103=>"Box",104=>"Box Plaket",105=>"Coin Welt  Pkt",106=>"Facing",107=>"Front Flap",108=>"Front Hem",109=>"Front Part-2",110=>"Gambel",111=>"Harmoniam",112=>"Leg Hem",113=>"Lining Loop",114=>"Panel",115=>"Pkt Piping",116=>"Rib Pkt",117=>"Sam",118=>"Sleeve Hem",119=>"Welt Pkt-2",120=>"Inner Bra",121=>"Sample Fabric",122=>"Frill",123=>"Neck and armhole strap",124=>"Printed Label",125=>"Main Fabric+Placket",126=>"Back Neck Patch",127=>"Embroidery Inside",128=>"Main Fabric Bottom Waist Belt",129=>"Insert Back Body",130=>"Bottom Insert",131=>"Main Fabric Top + Sleeve",132=>"Main Fabric Top + Neck",133=>"Collar Inside",134=>"Sleeve Panel",135=>"Back + Slv + N.Tape",136=>"Back Moon Placket",137=>"Key Hole Binding",138=>"Neck raw edge",139=>"Sleeve raw edge",140=>"Three Quarter Sleeve Tee",141=>"Shoulder tape",142=>"Gusset",143=>"Back Part + Slv",144=>"Herringbone tape",145=>"Inner Placket",146=>"Upper Placket",147=>"Raglan slv + Hood",148=>"Hood + slv",149=>"Main fabric + Placket + Foot",150=>"Front Lower part",151=>"Front Lower part + pocket",152=>"Back part + Pocket",153=>"Raglan Slv",154=>"Cuff Bind",155=>"Insert",156=>"Boon",157=>"Insert Bind",158=>"Crochet Lace",159=>"Hood Lining + Zipper Binding + Back Tape",160=>"Side piping",161=>"Neck Tape + Piping",162=>"Youk Shoulder",163=>"Placket + Necktape",164=>"Main Fabric Bag",165=>"Handel",166=>"Shoulder Placket",167=>"Main fabric Top Placket Shoulder",170=>"Hood + Cuff",171=>"Body + Binding + Nack Tape",172=>"Collar Tipping",173=>"Self outer collar band",174=>"Self outer placket",175=>"Inner collar band and placket",176=>"Inner pocket",177=>"High Neck",178=>"Inner Placket + Button Placket + Inner Collar",179=>"Piping Inside Neck",180=>"Neck tape + Pocket" ,181=>"Neck + Upper placket",182=>"Elbow/Patch",183=>"Neck Binding + Pocket",184=>"Polo Placket",185=>"Neck Binding Placket",186=>"Under Layer + Neck Tape",187=>"Twill Tape",188=>"Front+Slv panel",189=>"Btm Back+Middle Slv.",190=>"Back Upper",191=>"Body+Sleeve+Binding",192=>"Label Make",193=>"Finishing",194=>"Neck Rib Sleeve Hem Hem",195=>"Collar Outside",196=>"Ply",197=>"Bone",198=>"Main Body-Sleeve-Hood",199=>"Inner Tunnel Part",200=>"Zipper Garage",201=>"Main Body-Ruffles",202=>"Neck Tape + Moon",203=>"Collar + Cuff Triping",204=>"Back Neck",205=>"Front Neck",206=>"BK NECK + SLV TAPE",207=>"Sleeve + Sleeve Tab",208=>"Body + Sleeve + Placket + Pocket",209=>"Mock Vest",210=>"Bow Tie + Pocket Bag",211=>"Fancy Tape",212=>"Collar + Placket",213=>"Tail",214=>"Cuff Tipping",215=>"Hood + Slv + Btm Patch + Pocket",216=>"NECK + DRAWCORD + TUNNEL",217=>"Body + Pocket + Neck Tape",218=>"Sleeve + Neck Binding",219=>"Body + Placket + sleeve",220=>"Neck Binding + Neck Piping",221=>"Under Layer",222=>"Neck+Hem+Cuff",223=>"Neck+Placket+Armhole",224=>"Armhole",225=>"Body + Armhole",226=>"[Collar stand + Back Collar + Inside Placket]",227=>"[Body + Sleeve Placket + Collar ]",228=>"Neck+Neck tape+ Placket",229=>"Placket+Pocket+Elbow",230=>"Sleeve+shoulder",231=>"Neck Binding+Neck Tape",232=>"Body+Hood+Pocket",233=>"BODY+NECK+SHOULDER",234=>"BODY+NECK TAPE+FRONT",235=>"BODY+NECK+ARMHOLE",236=>"Body+Sleeve+Bowtie",237=>"Patch",238=>"BODY+NECK TAPE",239=>"NECK+SHOULDER",240=>"Bottom Skirt",241=>"NECK + FRONT CROSS",242=>"NECK + ARMHOLE + WAIST",243=>"Inner Sleeve Hem",244=>"Upper Neck",245=>"Straps + Bow",246=>"Side Body + Front + Back",247=>"Center + Front + Back",248=>"Slv + Btm Hem Layer + Neck Piping",249=>"Neck + Placket",250=>"Inner Hood + Slv + Btm Hem Layer",251=>"Half Moon + Slv + Btm Hem Layer + Back Tape",252=>"Body + Sleeve + Pocket",253=>"Body + Pocket + Sleeve + Neck Binding",254=>"Cross Lyling",255=>"Neck + Bottom layer",256=>"Knit Tape",257=>"Moon + Yoke",258=>"Body+Neck Binding+Placket+ Pocket",259=>"Zipper Piping",260=>"Neck Piping",261=>"Placket+Pocket Piping",262=>"Sleeve + Placket + Pocketing",263=>"Placket + Neck Tape Side",264=>"Slit Cuff + Neck + Forward Pocketing",265=>"3D Applique",266=>"Collar + Sleeve Hem + Pocket",267=>"Ear",268=>"Sleeve + Inner Placket",269=>"Neck Chambray",270=>"Neck Binding + Sleeve Hem",271=>"Inner Neck + Inner Cuff Binding",272=>"Neck collar",273=>"Sleeve Cuff",274=>"Body + Placket + Neck  Binding",275=>"Sleeve + Placket",276=>"Main Body + Bow",277=>"Front Lower Part + Back Part",278=>"Front Upper Part + Sleeve",279=>"Back Tape + Chest Piping",280=>"Moon + Back Tape",281=>"Front Upper Part + Sleeve + Back Part",282=>"Chest mini Piping",283=>"Body Lower Front + Front slv + Top Placket + Neck Tape",284=>"Body Back Part + Front Yoke + Back slv + Under Placket",285=>"Collar + Collar Binding + Under Upper Placket + Lower Placket",286=>"Half Moon + Collar Binding",287=>"Lower Placket",288=>"Inner Collar stand + Half Moon + Upper Placket",289=>"Mini Piping",290=>"Waist Band + Drawstring",291=>"Sleeve Cuff Rib + Bottom Cuff Rib",292=>"Bottom + Pocket + Waist ",293=>"Front Pocket Facing",294=>"Sleeve + Back Neck Tape",295=>"Cuff Rib",296=>"Top Hem",297=>"Side Panel + Placket",298=>"Chest Panel + Back Tape",299=>"Raw Edge",300=>"Inner Collar Stand + Half Moon + Under Upper Placket",301=>"Body + Neck Binding",302=>"Hairband",303=>"Cap",304=>"Scarf",305=>"Front and Back Body Lower Part + Slv + Neck Tape",306=>"Front and back body upper part + slv + pocket.",307=>"Neck tape + side slit + piping.",308=>"Front and back panel + neck panel piping.",309=>"Front + back panel with slv + placket + neck tape.",310=>"Yoke Front + Yoke Back",311=>"Leg Binding",312=>"Drawstring + Mini Piping",313=>"Placket Binding",314=>"Label",315=>"Top Cuff + Top Hem",316=>"Bottom Cuff + Bottom Hem",317=>"Band + Placket",318=>"Crossover Y/D + AOP",319=>"Bottom Cuff + Waist",320=>"NkTape +Shoulder Tape",321=>"NK+Slv+Btm Layer",322=>"Body + Hood",323=>"Bottom Sleeve",324=>"Hood Lining + Nk Tape",325=>"Body+Placket+Pocket+Sleeve+Collar Stand+Collar",326=>"Binding+Pocket",327=>"Waist+Cuff+Rib",328=>"Neck Binding+Bottom Hem + Sleeve",329=>"Neck Tape + Side Slits + Placket",330=>"Top + Bottom",331=>"Half moon + Sleev and bottom hem layer +Inner chest",332=>"Placket drawstring + Back Tape",333=>"Knee Patch",334=>"Sleeve+Hem",335=>"Neck Piping + Pocket",336=>"Placket + Neck piping",337=>"Neck + Bottom",338=>"Neck Tipping",339=>"Moon+Pocket+Tape+Placket",340=>"Moon+Placket",341=>"Inner chest +Moon+Bottom Layer+Cuff layer",342=>"Inner hood",343=>"Hood cord",344=>"Front facing",345=>"Front Facing + Moon",346=>"Sleeve and Bottom Layer",347=>"Placket+Collar Stand",348=>"Placket + Outside of Collarstand + Inside of Collar",349=>"Sleeve+Shoulder+Placket",350=>"Body+Sleeve",351=>"Hood+Pocket",352=>"Bottom Layer",353=>"Neck Rib + Neck Tape + Placket + Drawstring",354=>"Cuff+Hem",355=>"Fur",356=>"Pocket Bone + Placket",357=>"Neck Tape + Pocket + Placket",358=>"Front Panel+Moon",359=>"Chest Mini Piping + Back Tape",360=>"Front Lower Part+Chest Piping+Back Neck Tape",361=>"Neck + Cuff Rib",362=>"Moon + Chest Piping",363=>"Front Lower Part+Back Neck Tape",364=>"Main Fabric Top+Neck+Neck Tape",365=>"Cut and Sew",366=>"Hood Lining+Zipper Binding",367=>"Back Neck+Zipper Binding",368=>"Rumper",369=>"Hood Piping",370=>"Mini Patch",371=>"Main Fabric Top+Neck Tape",372=>"Front Lower Part + Moon",373=>"Body+Slv+Hood+Pocket",374=>"Inner Yoke + Inner Sleeve Cuff",375=>"Cord",376=>"Front Fake Neck + Back Fake Neck",377=>"Sleeve loop + Shoulder inset",378=>"Half Moon + Back Label",379=>"Inset Neck Layer",380=>"Pocket Bone",381=>"Neck binding + Fake neck + Back neck",382=>"Sleeve + Front Yoke",383=>"Pocket + Zipper + Facing",384=>"Padding",385=>"Quilting",386=>"Hood Linning+Back Neck+Side Seam Tape",387=>"Front Part+Hood",388=>"Armhole Piping",389=>"Sleeve Triangle",390=>"Body",391=>"Hood Lining + Half Moon",392=>"Body upper part + Hood + Upper sleeve",393=>"Body middle part",394=>"Body lower part + lower sleeve",395=>"Neck String",396=>"Collar+Sleeve Hem+Bottom Hem Piping",397=>"Pocket Sacks+ Neck Tape+Zipper Piping",398=>"Hood Lining + Pocketing",399=>"Inner Looper",400=>"Side Sleeve Panel Placket",401=>"Side Sleeve Panel Piping",402=>"Back Neck Tape + Side Slit",403=>"Front Part+Hood+Pocket",404=>"Bottom +Cuff Layer",405=>"Cuff + Placket",406=>"Bottom + Collar",407=>"Bottom + Placket",408=>"Hood + embroidery",409=>"Hood opening + back neck piping",410=>"Neck Tape + Hood Piping",411=>"Side panel",412=>"Side Tipping ",413=>"Bottom + Cuff + Neck Rib",414=>"Neck Tape + Piping + Moon + Placket + Bon");*/
$body_part = return_library_array("select id,body_part_full_name from  lib_body_part where status_active=1 and is_deleted=0 order by body_part_full_name", "id", "body_part_full_name");

asort($body_part);

$color_type = array(1 => "Solid", 2 => "Stripe [Y/D]", 3 => "Cross Over [Y/D]", 4 => "Check [Y/D]", 5 => "AOP", 6 => "Solid [Y/D]", 7 => "AOP Stripe", 20 => "Florecent", 25 => "Reactive", 26 => "Melange", 27 => "Marl", 28 => "Burn Out", 29 => "Gmts Dyeing", 30 => "Cross Dyeing", 31 => "Over Dyed", 32 => "Space Y/D", 33 => "Faulty Y/D", 34 => "Solid Stripe", 35 => "One Part Dye", 36 => "Space Dyeing", 37 => "Dope Dye", 38 => "INDIGO", 39 => "Neon");

$dyeing_sub_process = array(1 => "Demineralisation", 2 => "Demineralization -1", 3 => "Demineralization -2", 4 => "Bleaching -1", 5 => "Bleaching – 2", 6 => "Bleaching – 3", 7 => "Bleaching – 4", 8 => "Bleaching – 5", 9 => "Soaping – 1", 10 => "Pretreatment-1", 11 => "Soaping – 2", 12 => " Soaping – 3", 13 => "Enzyme – 1", 14 => "Enzyme – 2", 15 => "Enzyme - 3", 20 => "Neutralisation-1", 21 => "Neutralisation-2", 22 => "Neutralisation-3", 23 => "Neutralisation-4", 30 => "Biopolish", 40 => "Dyestuff", 50 => "Dyeing Bath", 60 => "After Treatment 1", 70 => "Color Remove", 90 => "Other", 91 => "Levelling 1", 92 => "Finishing Process", 93 => "Wash 1", 94 => "Wash 2", 95 => "Wash 3", 96 => "Wash 4", 97 => "Wash 5", 98 => "Wash 6", 99 => "After Treatment 2", 100 => "After Treatment 3", 101 => "After Treatment 4", 102 => "Desizing", 103 => "Enzyme", 104 => "PP Bleach", 105 => "Bleach", 106 => "PP Bleach Neutral", 107 => "Bleach Neutral", 108 => "Cleaning", 109 => "PP Neutral", 110 => "Tint", 111 => "Fixing", 112 => "Softener", 113 => "Acid Wash", 114 => "Towel Bleach", 115 => "Scouring", 116 => "Resign Spray", 117 => "Dyeing", 118 => "Soaping", 119 => "Silicon", 120 => "Independent Process", 121 => "Levelling 2", 122 => "Levelling 3", 123 => "Levelling 4", 124 => "Pretreatment-2", 125 => "PreTreatment-3",126 => "PreTreatment-4",127=>"Soaping 2",128=>"Reduction Clear 1",129=>"Reduction Clear 2", 126 => "Heat Setting", 127 => "Dye stuff 1", 128 => "Dye stuff 2", 129 => "Dyeing bath-1", 130 => "Dyeing bath-2", 131=> "Dyeing bath-3");

$dose_base = array(1 => "GPLL", 2 => "% on BW");

$excess_cut_per_level = array(1 => "Color Size Level", 2 => "PO Level");
$fab_req_qty_source = array(1 => "Budget", 2 => "Fabric Booking");
//$conversion_cost_head_array=array(1=>"Knitting",2=>"Weaving",30=>"Yarn Dyeing",31=>"Fabric Dyeing",32=>"Tube Opening",33=>"Heat Setting",34=>"Stiching Back To Tube",35=>"All Over Printing",36=>"Stripe Printing",37=>"Cross Over Printing",60=>"Scouring",61=>"Color Dosing",62=>"Neutralization",63=>"Squeezing",64=>"Washing",65=>"Stentering",66=>"Compacting",67=>"Peach Finish",68=>"Brush",69=>"Peach+Brush",70=>"Heat+Peach",71=>"Peach+Brush+Heat",72=>"UV Prot",73=>"Odour Finish",74=>"Teflon Coating",75=>"Cool Touch",76=>"MM",77=>"Easy Care Finish",78=>"Water Repellent",79=>"Flame Resistant",80=>"Hydrophilics",81=>"Antistatic",82=>"Enzyme",83=>"Silicon", 84=>"Softener", 85=>"Brightener",86=>"Fixing/Binding Agent",87=>"Leveling Agent",101=>"Dyes & Chemical Cost");
$conversion_cost_head_array = array(
	1 => "Knitting",
	2 => "Weaving",
	3 => "Collar and Cuff Knitting",
	4 => "Feeder Stripe Knitting",
	25 => "Dyeing+Enzyme+Silicon",
	26 => "Dyeing+No Enzyme+Silicon",
	30 => "Yarn Dyeing",
	31 => "Fabric Dyeing",
	32 => "Tube Opening/Sliting",
	33 => "Heat Setting",
	34 => "Stiching Back To Tube",
	35 => "All Over Printing",
	36 => "Stripe Printing",
	37 => "Cross Over Printing",
	38 => "Reversing",
	39 => "Discharge Dyeing",
	40 => "Discharge Print",
	60 => "Scouring",
	61 => "Color Dosing",
	62 => "Neutralization",
	63 => "Slitting/Squeezing",
	64 => "Normal Wash",
	65 => "Stentering",
	66 => "Open Compacting",
	67 => "Peach Finish",
	68 => "Brush",
	69 => "Peach+Brush",
	70 => "Heat+Peach",
	71 => "Peach+Brush+Heat",
	72 => "UV Prot",
	73 => "Odour Finish",
	74 => "Teflon Coating",
	75 => "Cool Touch",
	76 => "MM",
	77 => "Easy Care Finish",
	78 => "Water Repellent",
	79 => "Flame Resistant",
	80 => "Hydrophilics",
	81 => "Antistatic",
	82 => "Enzyme",
	83 => "Silicon Finish",
	84 => "Softener",
	85 => "Brightener",
	86 => "Fixing/Binding Agent",
	87 => "Leveling Agent",
	88 => "Sueding",
	89 => "Double Enzyme",
	90 => "Tube Dryer",
	91 => "Tube Compacting",
	92 => "Carbon",
	93 => "Trumble Dryer",
	94 => "Singeing",
	100 => "Back Sewing",
	101 => "Dyes & Chemical Cost",
	120 => "Cutting",
	121 => "Gmts. Printing",
	122 => "Gmt. Embroidery",
	123 => "Gmts. Washing",
	124 => "Sewing",
	125 => "Calendering",
	127 => "Shearing",
	128 => "Combing",
	129 => "Burn Out",
	130 => "Iron",
	131 => "Gmts Finishing",
	132 => "Peroxide Wash",
	133 => "Curing",
	134 => "Twisting",
	135 => "Scouring + Enzyme + Silicon",
	136 => "Double Stentering",
	137 => "Double Dyeing",
	138 => "Dyeing Enzyme Silicon Boi-Wash",
	139 => "Direct Dyeing + Enzyme + Silicon",
	140 => "WASH ENZYME SILICON",
	141 => "Steam Tumble Dry",
	142 => "Wash Hydro Tumble Dry",
	143 => "Steam Tumble + Open Compacting",
	144 => "No Softener",
	145 => "Fabric Turning",
	146 => "Dyeing And Enzyme",
	147 => "Re Dyeing",
	148 => "Re Wash",
	149 => "Re Match",
	150 => "Re Compacting",
	151 => "Re Stanter",
	152 => "Poly",
	153 => "Re Conning",
	154 => "Fabric Embroidery",
	155 => "Stenter",
	156 => "Compacting",
	157 => "Standard and Compacting",
	158 => "DYEING AND FINISHING",
	159 => "Brush + Stenter + Compacting",
	160 => "Brush + Softener + Stenter + Compacting",
	161 => "Peach + Brush + Stenter + Compacting",
	162 => "Dyeing + Enzyme + Finishing",
	163 => "Dyeing + Finishing + Brush + Peach",
	164 => "Stitching",
	165 => "Air Turning",
	166 => "Sliting",
	167 => "Brush One Side",
	168 => "Brush Both Side",
	169 => "Anti Piling",
	170 => "Special Finish",
	171 => "Drying",
	172	=> "Tube Finish",//Fabric Dyeing+Slitting/Squeezing+Stenter
	173	=> "Squeezer",
	174	=> "Fabric Dyeing+Slitting/Squeezing+Stenter",
	175	=> "SILICON WASH",
	176	=> "Turning",
	177	=> "Grey Return",
	178	=> "Back stain",
	179	=> "De-oiling",
	180	=> "Double Open Compacting",
	181	=> "Double Tube Compacting",
	182	=> "Both Side Singeing",
	183	=> "Top Side Singeing",
	184	=> "Inside Brush",
	185	=> "Outside Brush",
	186	=> "Contrast",
	187	=> "Dumping",
	188	=> "Gmts Wash",
	189	=> "No Enzyme",
	190=> "Resin Finish",
	191=> "Stenter(For Rubbing)",
	192=> "UV Finish",
	193=> "Washing",
	194=> "Chemical Finish",
	195=> "Brush + Shearing + Stenter",
	196=> "Stenter + Compacting",
	197=> "Peach Finish + Compacting",
	198=> "Brush + Shearing + Peach finish + Stenter",
	199=> "Shearing + Stenter",
	200	=> "Dry Slitting",
	201=> "Brush Wash",
	202=> "Back Sewing",
	203=> "Others",
	204=> "Hydro Mc",
	205=> "Brush + Shearing",
	206=> "Rotation",
	207=> "Steaming",
	208=> "Moisture",
	209=> "AOP Wash", 
	210=> "Steaming + Wash + Softener + Compacting",
	211=> "Curing + Wash + Softener + Compacting",
	212=> "Curing + Wash + Softener + Sunforizing",
	213=> "Pigment Print",
	214=> "Reactive Print",
	215=> "Burnout Print",
	216=> "Florescent Print",
	217=> "Rubber Print",
	218=> "Teflon Finish",
	219=> "Brush + Compacting + Shearing",
	220=> "Sunforizing",
	221=> "Dyeing wash",
	222=> "Neon Print",
	223=> "Glitter Print",
	224=> "Brush + Continuous Tumble + Shearing + Stenter ",
	225=> "Brush + Shearing + Stenter + Compacting",
	226=> "Stenter + Print",
	227=> "Both Side Peach",
	228=> "Brush Wash"
	
	
	
);
asort($conversion_cost_head_array);//

$qc_template_item_arr = array(1 => "BTS", 2 => "BTM"); // Added Ref.By Kausar(Quick Costing)

$mandatory_subprocess = array(33 => "Heat Setting", 34 => "Stiching Back To Tube", 94 => "Singeing", 60 => "Scouring", 63 => "Slitting/Squeezing", 65 => "Stentering", 66 => "Open Compacting", 78 => "Water Repellent", 82 => "Enzyme", 90 => "Tube Dryer", 91 => "Tube Compacting"); //need to consult with sir

$conversion_cost_type = array(1 => "Knitting", 10 => "Yarn Dyeing", 11 => "Dyeing", 12 => "AOP", 13 => "Wash", 20 => "Finishing", 21 => "Chemical Finish", 22 => "Special Finishing", 40 => "Dyes & Chemical ");
$emblishment_name_array = array(1 => "Printing", 2 => "Embroidery", 3 => "Wash", 4 => "Special Works", 5 => "Gmts Dyeing", 99 => "Others");

$cost_heads_for_btb = array(1 => "Knitting", 30 => "Yarn Dyeing", 31 => "Fabric Dyeing", 35 => "All Over Printing", 75 => "Knit Fabric Purchase", 78 => "Woven Fabric Purchase", 101 => "Printing", 102 => "Embroidery", 103 => "Wash"); //101 means 1, 102 means 2, 103 means 3,4=>"Feeder Stripe Knitting",64=>"Washing Charge",65=>"Stentering",68=>"Brush"

$emblishment_print_type = array(1 => "Rubber", 2 => "Glitter", 3 => "Flock", 4 => "Puff", 5 => "High Density", 6 => "Foil", 7 => "Rubber+Foil", 8 => "Rubber+Silver", 9 => "Pigment", 10 => "Rubber+Pearl", 11 => "Rubber+Sugar", 12 => "Transfer / Sel", 13 => "Crack", 14 => "Photo", 15 => "Foil+Photo", 16 => "Pigment+Stud", 17 => "Rubber+Stud", 18 => "Rubber+Glitter", 19 => "Photo+Silicon", 20 => "Rubber+Silicon", 21 => "Rubber+Stud/Stone", 22 => "Photo+Stud/Stone", 23 => "Rubber+Flock", 24 => "Photo+Flock", 25 => "Discharge", 26 => "Discharge+ Flock", 27 => "Discharge + Pigment", 28 => "Pigment + Glitter", 29 => "Pigment + Foil", 30 => "Pigment+ Plastisol", 31 => "Plastisol", 32 => "Flou color", 33 => "Fluo +Pigment", 34 => "Photo + Pigment", 35 => "Reverse", 36 => "Reverse + Pigment", 37 => "Aop", 38 => "Burnout", 39 => "Sublimation", 40 => "Heat Press", 41 => "Pigment + Rubber", 42 => "Emboss", 43 => "Leaser Print", 44 => "Glow In Dark", 45 => "Metallic", 46 => "Pad Printing", 47 => "Pigment/Rubber", 48 => "Regular+Puff+Silver Foil", 49 => "Foil + Glitter", 50 => "Gel + Pigment + Flock", 51 => "Applique", 52 => "Rubber+High Density", 53 => "Placement", 54 => "High D + Foil", 55 => "Silicon", 56 => "Screen Print", 57 => "Rubber-Label Print", 58 => "Pigment-Label Print", 59 => "Foil+Puff", 60 => "Digital Printing", 61 => "Flock+Pigment", 62 => "HD Gel", 63 => "Pigment Stone", 64 => "Rubber Flock", 65 => "Pigment + Crack print", 66 => "Pigment + High-density Print", 67 => "Crack + High-density Print", 68 => "Pigment + Glitter + Foil", 69 => "High Density + Gradient", 70 => "Pigment High Raised Rubber", 71 => "Eco Discharge + Gel", 72 => "Cmyk Photo", 73 => "Cmyk Discharge", 74 => "Cmyk Pigment", 75 => "Cmyk Rubber", 76 => "Cmyk Foil", 77 => "Rubber + Gel", 78 => "Reflective Print",79 => "Gel",80=>"Stud",81=>"Semi Rubber");
$emblishment_embroy_type = array(1 => "Applique", 2 => "Plain", 3 => "Sequence", 4 => "Patch Label", 5 => "Snail",6=>"3D",7=>"Back Pkt EMB",8=>"Cord",9=>"Boring");
$emblishment_wash_type = array(1 => "Normal", 2 => "Pigment", 3 => "Acid", 4 => "PP Spray/Dz", 5 => "Enzyme", 6 => "Enzyme+Silicon", 7 => "Grinding", 8 => "Cold Dye", 9 => "Tie Dye", 10 => "Batik Dye", 11 => "Deep Dye", 12 => "P.P Spray + Bleach Wash", 13 => "Enzyme + Bleach wash", 14 => "Burnout Wash", 15 => "Crinkle Wash", 16 => "Direct dyeing Acid Wash", 17 => "Spray Wash", 18 => "Antique", 19 => "Pigment Garments Dye", 20 => "Sand Wash", 21 => "Vintage Wash", 22 => "Grinding + Garments Wash", 23 => "Pigment Dye + Heavy Enzyme Wash", 24 => "Double Enzyme", 25 => "Peroxide Wash", 26 => "Dyeing Enzyme Silicon Boi-Wash", 27 => "Wash Hydro Tumble Dry", 28 => "Dyeing And Enzyme", 29 => "Re Wash", 30 => "Stone Enzyme Wash", 31 => "Caustic Wash", 32 => "Bleach Wash", 33 => "Tint Wash", 34 => "Towel Bleach Wash", 35 => "Cool Pigment Dye", 36 => "Thermo-Chromatic Dye", 37 => "Pigment Garments Dye+Acid Wash", 38 => "Whisker", 39 => "Hand Scraping", 40 => "Tagging", 41 => "Destroy", 42 => "Enzyme wash", 43 => "Garments wash", 44 => "Rinse wash", 45 => "Silicon wash", 46 => "PP Sprey", 47 => "3D", 48 => "Tie Mark", 49 => "Stone", 50 => "White Pest", 51 => "Neon Dye", 52 => "Cool Dye", 53 => "Reactive Dye", 54 => "Discharable Dye", 55 => "Pigment Dye", 56 => "Knee Cut", 57 => "Laser whisker", 58 => "Laser Destroy", 59 => "Potash", 60 => "Cleaning", 61 => "Desize", 62 => "PP Rubbing", 63 => "Garments Dye", 64 => "Over Dye", 65 => "Tumble Dry",66=>"Garments wash + Softener",67=>"Enz Stone Bleach Wash",68=>"Whiskers Chevron",69=>"Handsand",70=>"PP",71=>"Softener");

$emblishment_spwork_type = array(1 => "Stone", 2 => "Bow", 3 => "Ribbon", 4 => "Beeds", 5 => "H/Press", 6 => "Smocking",7=>"Scalloped Cutting");
$emblishment_gmts_type = array(1 => "Tie Dyeing", 2 => "Dip Dyeing", 3 => "Spray Dyeing", 4 => "Over dyeing", 5 => "Cold dyeing", 6 => "High white dyeing", 7 => "Washable dyeing", 8 => "Reverse dyeing", 9 => "Top dyeing", 10 => "Direct Dye and Acid Wash", 11 => "Double Dyeing", 12 => "Pigment Dyeing", 13 => "Reactive Dyeing", 14 => "Gel Dyeing", 15 => "Fluorescent Pigment Dyeing");
$commission_particulars = array(1 => "Foreign", 2 => "Local");
$commission_base_array = array(1 => " in Percentage", 2 => "Per Pcs", 3 => "Per Dzn");
$camarcial_items = array(1 => "LC Cost ", 2 => "Port & Clearing", 3 => "Transportation", 4 => "All Together");
$size_color_sensitive = array(1 => "As per Gmts. Color", 2 => "Size Sensitive", 3 => "Contrast Color", 4 => "Color & Size Sensitive");

$shipment_status = array(0 => "All", 1 => "Full Pending", 2 => "Partial Delivery", 3 => "Full Delivery/Closed");


$pay_mode = array(1 => "Credit", 2 => "Import", 3 => "In House", 4 => "Cash", 5 => "Within Group");
$actual_cost_heads = array(1 => "Testing Cost", 2 => "Freight Cost", 3 => "Inspection Cost", 4 => "Courier Cost", 5 => "CM", 6 => "Commercial");
$aop_nonor_fabric_source = array(1 => "Sample Booking", 2 => "Transfer From Order");
//$breakdown_type=array(1=>"Matrix With Full Qty.",2=>"Matrix With Packing Ratio + Ctn Qty",3=>"Matrix With Packing Ratio + Gmts Qty",4=>"Matrix With Packing Ratio + Pack Qty");
$breakdown_type = array(1 => "Matrix With Full Qty.", 2 => "Matrix With Packing Ratio + Ctn Qty", 3 => "Matrix With Packing Ratio + Gmts Qty");
$exCut_source = array(1 => "Manual", 2 => "Slab", 3 => "No-Need");
$qc_consumption_basis = array(1 => "Cad/Manual-CM", 2 => "Measurement-CM", 3 => "Cad/Manual-Inch", 4 => "Measurement-Inch");
$short_booking_type = array(1 => "Additional ", 2 => "Compensative", 3 => "Compensative -Dia Change", 4 => "Compensative -On Return");
$short_division_array = array(1 => "Textile ", 2 => "Garments");
//---------------------------------------------------------------------Start production Module Array------------------------------------------------------//
$cause_type = array(1 => 'Disorder', 2 => 'Routine Maintenance', 3 => 'Job Not Available', 4 => 'Job Not Assigned', 5 => 'Operator Not Available', 6 => 'Worker Unrest', 7 => 'Off-Day', 8 => 'Material Not Available', 9 => 'M/C WASH', 10 => 'Batch Preparation Late', 11 => 'Batch Not Available', 12 => 'Heat Set', 13 => 'Sewing', 14 => 'Breakdown Maintenance', 15 => 'Trolley', 16 => 'Electrical Problem', 17 => 'Program Change', 18 => 'Lycra Change', 19 => 'Problematic Yarn', 20 => 'Sample', 21 => 'Mechanical Work', 22 => 'Machine Disabled', 23 => 'Design Change', 24 => 'Design Program', 25 => 'Machine Servicing', 26 => 'Yarn Store delivery problem', 27 => 'Yarn Nil', 28 => 'Loose Yarn use', 29 => 'Mechanical problem', 30 => 'Helper short', 31 => 'Meeting', 32 => 'Namaj', 33 => 'M/C cleaning', 34 => 'Dust cleaning', 50 => 'Others', 51 => 'No Program', 52 => 'Mix Lot');

$production_type=array(1=>"Cutting",2=>"Printing",3=>"Print Received",4=>"Sweing In",5=>"Sewing Out",6=>"Finish Input",7=>"Iron Output",8=>"Gmts Finish",9=>"Cutting Delivery",10=>"Finish Garments Order to Order transfer",11=>"Poly Entry",
12=>"Sewing Line input",13=>"Sewing Line Output",40 => "Plan Cut",50 => "Bundle Issue to Knitting Floor", 51 => "Bundle Receive from Knitting Floor", 52 => "Knitting QC", 53 => "Bundle issue to Linking ", 54 => "Bundle receive in Linking", 55 => "Bundle Wise Linking Input", 56 => "Bundle Wise Linking Output", 57 => "Delivery to Wash", 58 => "Receive in Wash", 59 => "Batch Creation for Wash", 60 => "Recipe for Wash", 61 => "Wash Chemical Issue Requisition", 62 => "Wash Production Entry (QC Passed)", 63 => "Embellishment Issue", 64 => "Embellishment Receive", 65=> "Re-linking", 66 => "Special Operation", 67 => "Iron entry", 68 => "Poly entry", 69 => "Packing and Finishing", 70 => "Final Inspection", 71 => "Ex-factory", 72 => "Operation wise entry", 73 => "Linking QC", 74 => "Lot Ratio", 75 => "Linking Operation Track");

$batch_for = array(1 => "Fabric Dyeing", 2 => "Yarn Dyeing", 3 => "Trims Dyeing");
$batch_against = array(1 => "Buyer Order", 2 => "Re-Dyeing", 3 => "Sample", 4 => "External", 5 => "Without Booking", 6 => "Gmts Wash", 7 => "Gmts Dyeing", 8 => "Fabric Wash", 9 => "Without Job", 10 => "Gmts Printing", 11 => "Re-Wash");
$inspection_status = array(1 => "Passed", 2 => "Re- Check", 3 => "Failed", 4 => "2nd Re_check", 5 => "3rd Re_check");
$inspection_cause = array(1 => "Major", 2 => "Minor",3=>"Acceptable");
$loading_unloading = array(1 => 'Loading', 2 => 'Un-loading');
$dyeing_result = array(1 => 'Shade Matched', 2 => 'Re-Dyeing Needed', 3 => 'Fabric Damaged', 4 => 'Incomplete/Running', 5 => 'Under Trial', 6 => 'Re-Wash Needed',11 => 'Complete',12 => 'Next process Stentering',13 => 'Next process Dryer',14 => 'Next process Compacting',15 => 'Next process Brush',16 => 'Next process Peach');


$dyeing_method = array(1 => "Black-B Process", 2 => "100 % Cotton S/J Fabric Black Color Dyeing Process", 3 => "100 % Cotton Terry Fabric Black Color Dyeing Process", 4 => "100 % Cotton Fleece Fabric Black Color Dyeing Process", 5 => "100 % Cotton PK/Rib Fabric Black Color Dyeing Process", 6 => "100 % Cotton S/J Fabric Light Color Dyeing Process", 7 => "100 % Cotton Terry Fabric Light Color Dyeing Process", 8 => "100 % Cotton Fleece Fabric Light Color Dyeing Process", 9 => "100 % Cotton PK/Rib Fabric Light Color Dyeing Process", 10 => "Vacilis Process", 11 => "100 % Cotton S/J Fabric Dark Color Dyeing Process", 12 => "100 % Cotton Terry Fabric Dark Color Dyeing Process", 13 => "100 % Cotton Fleece Fabric Dark Color Dyeing Process", 14 => "100 % Cotton PK/Rib Fabric Dark Color Dyeing Process", 15 => "100 % Cotton S/J Fabric Critical Color Migration Dyeing Process", 16 => "100 % Cotton Terry Fabric Critical Color Migration Dyeing Process", 17 => "100 % Cotton Fleece Fabric Critical Color Migration Dyeing Process", 18 => "100 % Cotton PK/Rib Fabric Critical Color Migration Dyeing Process", 19 => "100 % Cotton S/J Fabric Turquoise Color Dyeing Process", 20 => "Turquise (80-95-80)<1", 21 => "100 % Cotton Terry Fabric Turquoise Color Dyeing Process", 22 => "100 % Cotton Fleece Fabric Turquoise Color Dyeing Process", 23 => " 100 % Cotton PK/Rib Fabric Turquoise Color Dyeing Process", 24 => "Ly S/J Fabric Dark Color Dyeing Process", 25 => "Ly S/J Fabric Light & Critical Color Dyeing Process", 26 => "Viscose Fabric Dyeing Process", 27 => "CVC (Double Part) Light Color Dyeing Process", 28 => "CVC (Double Part) Dark Color Dyeing Process", 29 => "100 % Cotton S/J Fabric White Color Dyeing Process", 30 => "Turquise (80-95-80)>1", 31 => "100 % Cotton Terry Fabric White Color Dyeing Process", 32 => "100 % Cotton Fleece Fabric White Color Dyeing Process", 33 => "100 % Cotton PK/Rib Fabric White Color Dyeing Process", 34 => "100 % Cotton S/J Fabric Short Dyeing Process", 35 => "100 % Cotton Terry Fabric Short Dyeing Process", 36 => "100 % Cotton Fleece Fabric Short Dyeing Process", 37 => "100 % Cotton PK/Rib Fabric Short Dyeing Process", 38 => "100 % Cotton S/J Fabric Normal Wash Process", 39 => "100 % Cotton Terry Normal Wash Process", 40 => "All 60&#8451 Normal", 41 => "100 % Cotton Fleece Fabric Normal Wash Process", 42 => "100 % Cotton PK/Rib Fabric Normal Wash Process", 43 => "100 % Cotton S/J Fabric Enzyme Wash Process.", 44 => "100 % Cotton Terry Enzyme Wash Process.", 45 => "100 % Cotton Fleece Fabric Enzyme Wash Process.", 46 => "100 % Cotton PK/Rib Fabric Enzyme Wash Process.", 50 => "All 60&#8451 Critical", 60 => "40-60 Process", 70 => "Polyestar Process", 80 => "White Process", 90 => "Viscose Process", 100 => "CVC/PC Double Part (Vacilis Process)", 110 => "CVC/PC Double Part (Separate Reduction Process)");


$batch_status_array = array(0 => "Incomplete", 1 => "Complete");

$worker_type = array(1 => "Salary Based Worker", 2 => "Piece Rate Worker");
$piece_rate_wq_limit_arr = array(1 => "Up to Order Qty", 2 => "Up to Plan Cut Qty");
$fabric_type_for_dyeing = array(1 => 'Cotton', 2 => 'Polyster', 3 => 'Lycra', 4 => 'Both Part', 5 => 'White', 6 => 'Wash', 7 => 'Melange', 8 => 'Viscose', 9 => 'CVC 1 Part', 10 => 'Scouring', 11 => 'AOP Wash', 12 => 'Y/D Wash');
$inspected_by_arr = array(1 => 'Buyer', 2 => '3rd Party', 3 => 'Self');
$validation_type = array(1 => "Order Wise", 2 => "Country Wise");
$defect_type = array(1 => "Alter", 2 => "Spot", 3 => "Reject");

$sew_fin_alter_defect_type = array(1 => "Fab Fault/ Colour Variation", 2 => "Run of seam", 3 => "Open Seam", 4 => "Skip Stitch", 5 => "Uneven Top Stitch", 6 => "Broken Stitch", 7 => "Loose Stitch", 8 => "Irregular Stitch", 9 => "Puckering", 10 => "Label Wrong Mistake", 11 => "Slanted At Label", 12 => "Rawadge", 13 => "Missing Tuck", 14 => "Wrong Tacking", 15 => "Up Down", 16 => "Missing Lbl /Bartack", 17 => "Shading", 18 => "Pleat", 19 => "Twisting", 20 => "Gathring Wrong", 21 => "Uncut Thread", 22 => "Button Missing", 23 => "Button Position Wrong", 24 => "Print Defect", 25 => "Poor Shape", 26 => "Contamination", 27 => "Slub", 28 => "Others", 29 => "Seam Reversed", 30 => "Needle Mark", 31 => "Bad Ten shire", 32 => "Over STC", 33 => "Incorrect SPI", 34 => "Uneven Seam Allowance", 35 => "Crooked Label", 36 => "Joint/Gathering STC", 37 => "Uneven Shape", 38 => "Cut Stitch", 39 => "Thread Missing", 40 => "Hole");

$sew_fin_spot_defect_type = array(1 => "Dirty Stain", 2 => "Oil Stain");
$cutting_qc_reject_type = array(1 => "Crease Mark", 2 => "Dirty Spot", 3 => "Hole", 4 => "Knitting Defect", 5 => "Dyeing Spot", 6 => 'Others', 8 => 'Miss yarn', 9 => 'Contamination', 10 => 'Slub', 11 => 'Oil Spot', 12 => 'Needle Line', 13 => 'Needle Hole', 14 => 'Lycra Out', 15 => 'Dia Mark', 16 => 'Knot', 17 => 'Miss Cut', 18 => 'Over Compusser', 19 => 'Un-Even Dyenig', 20 => 'Patta', 21 => 'Loop Out', 22 => 'Grease Spot', 23 => 'Thick and Thin', 24 => 'Gsm Cut', 25 => 'Fabric Join', 26 => 'Reject For Dia Short', 27 => 'Crimple Mark', 28 => 'Broken', 29 => 'Marker Line', 30 => 'Shape Uneven', 31 => 'Numb Spot', 32 => 'Miss Print');
$upto_receive_batch = array(1 => 'Heat setting', 2 => 'Dyeing', 3 => 'Slitting  Squeezing', 4 => 'Stentering', 5 => 'Drying', 6 => 'Special Finish', 7 => 'Compacting', 8 => 'Singeing');
$trims_section = array(1 => "Elastic", 2 => "Gum Tape", 3 => "Label", 4 => "Offset Print", 5 => "Poly", 6 => "Screen Print", 7 => "Sewing Thread", 8 => "Twill Tape", 9 => "Drawstring", 10 => "Yarn Dyeing", 11=> "All Over Print", 12 => "Embroidery", 13 => "Hanger", 14 => "Carton", 15 => "Twisting", 16 => "Doubling", 17 => "Price Ticket", 18 => "Paper", 19 => "Tipping", 20 => "Dye Cut");

//---------------------------------------------------------------------Start production Module Array END --------------------------------------------------//

//---------------------------------------------------------------------Start Commercial Module Array------------------------------------------------------//
$source = array(1 => "Abroad", 2 => "EPZ", 3 => "Non-EPZ");
$pi_basis = array(1 => "Work Order Based", 2 => "Independent", 3 => "Sales Order", 4 => "Purchase Contract");
$wo_basis = array(1 => "Requisition Based", 2 => "Independent", 3 => "Buyer PO");
$sample_wo_basis = array(1 => "Requisition Based", 2 => "Job Based", 3 => "Sample Based");
$lc_basis = array(1 => "PI Basis", 2 => "Independent");
$convertible_to_lc = array(1 => "LC/SC", 2 => "No", 3 => "Finance");
$pay_term = array(1 => "At Sight", 2 => "Usance", 3 => "Cash In Advance", 4 => "Open Account", 5 => "Block Order");
$shipment_mode = array(1 => "Sea", 2 => "Air", 3 => "Road", 4 => "Train", 5 => "Sea/Air", 6 => "Road/Air", 7 => "Courier");
$extend_shipment_mode = array(1 => "Sea", 2 => "Sea with discount", 3 => "Air", 4 => "Air with discount", 5 => "Sea & Air");
$contract_source = array(1 => "Foreign", 2 => "Inland");

$dyedType = array(0 => 'All', 1 => 'Dyed Yarn', 2 => 'Non Dyed Yarn');

$yarn_type = array(1 => "Carded", 2 => "Combed", 3 => "Compact", 4 => "Polyester", 5 => "CVC", 6 => "PC", 7 => "Melange", 8 => "Micro Poly", 9 => "Rottor", 10 => "Slub", 11 => "Spandex", 12 => "Viscose", 13 => "Modal Cotton", 14 => "BCI", 15 => "Modal", 16 => "Semi Combed", 17 => "Special", 18 => "Cotton Linen", 19 => "Pima", 20 => "Su-Pima", 21 => "Lurex", 22 => "PV", 23 => "Tencel", 24 => "Excel/Linen", 25 => "CV", 26 => "CVC Slub", 27 => "Pmax", 28 => "Mercerize", 29 => "Organic", 30 => "Twist", 31 => "Melange Slub", 32 => "Melange Neps", 33 => "Neps", 34 => "Ctn Melange", 35 => "Inject", 36 => "Cotton Lurex", 37 => "Melange Lurex", 38 => "Viscos/Linen", 39 => "Vortex", 40 => "Polyester/Linen", 41 => "CB Slub", 42 => "PC Slub", 43 => "Carded Slub", 44 => "Org-Melange", 45 => "PVC", 46 => "Acrylic", 47 => "Spun", 48 => "Viscose-Wool", 49 => "Linen-Tencel", 50 => "Viscose Melange", 51 => "Poly Filament", 52 => "Spun Poly", 53 => "Ring Spun", 54 => "Poly Coolmax", 55 => "Poly HScool", 56 => "Poly Thermolit", 57 => "Poly Trevira", 58 => "Poly CD Yarn", 59 => "Cambric Viscose", 60 => "Ring Card", 61 => "CVC Melange", 62 => "PC Melange", 63 => "Modal Linen", 64 => "Siro", 65 => "Viscose Slub", 66 => "CPV ", 67 => "VC", 68 => "Cotton-Tencil", 69 => "Cotton-Rayon", 70 => "Siro Slub", 71 => "Inject Slub Melange", 72 => "Pima Melange", 73 => "Triblend", 74 => "Space Slub", 75 => "Carded Ring Spun", 76 => "Combed Slub", 77 => "Recycle", 78 => "Pina ", 79 => "Banana", 80 => "Eco Fresh", 81 => "VP", 82 => "Lenzing", 83 => "Combed Compact", 84 => "COMBED- CONTRA FREE", 85 => "COMFORJET", 86 => "Carded Contra Free", 87 => "Carded Contra Control", 88 => "Inject Slub", 89 => "CB Compact Contra Free", 90 => "MVS", 91 => "Cupro", 92 => "CREPE", 93 => "NYLON", 94 => "Charcoal Mel", 95 => "VPC", 96 => "Combed Vortex", 97 => "Carded-S Twist", 98 => "Rubber Thread", 99 => "CPL", 100 => "PVM", 101 => "Organic Carded", 102 => "Organic Combed", 103 => "Carded BCI", 104 => "Combed BCI", 105 => "Carded Slub BCI", 106 => "Combed Slub BCI", 107 => "Carded Organic BCI", 108 => "Combed Organic BCI", 109 => "Carded Slub Organic BCI", 110 => "Combed Slub Organic BCI", 111 => "Gray Melange", 112 => "Gray Melange Slub", 113 => "Organic Melange", 114 => "Carded Gray Melange", 115 => "Carded Gray Melange Slub", 116 => "Carded Gray Melange Organic", 117 => "Combed Gray Melange", 118 => "Combed Gray Melange Slub", 119 => "Combed Gray Melange Organic", 120 => "100% Viscose", 121 => "Viscose-Acrylic", 122 => "Organic Carded Slub", 123 => "Combed Organic Slub", 124 => "Full Dull", 125 => "Semi Dull", 126 => "Autocoro", 127 => "Covered", 128 => "Carded Open End", 129 => "Marvel melange", 130 => "OE CVC", 131 => "OE", 132 => "EM", 133 => "PCV", 134 => "DM", 135 => "CVC Dope Dyed", 136 => "Polyester Black", 137 => "Suprima", 138 => "Compact Carded", 139 => "Gray Melange BCI", 140 => "Snow", 141 => "Marble Heather", 142 => "Space Dyed", 143 => "Neon Space", 144 => "Creek Heather", 145 => "Organic Slub", 146 => "Organic Melange Slub", 147 => "Long Slub", 148 => "Combed Gassed Mecerised", 149 => "Organic Combed Slub", 150 => "Organic Carded Melange", 151 => "Organic Combed Melange", 152 => "Organic Compact", 153 => "Organic Vortex", 154 => "Organic Compact Vortex", 155 => "Organic Combed Vortex", 156 => "Organic Carded Vortex", 157 => "Organic CVC", 158 => "Organic CVC Slub", 159 => "Organic PC", 160 => "Organic PC Slub", 161 => "Organic CV", 162 => "Organic CV Slub", 163 => "Compact Combed", 164 => "Combed Slub Organic", 165 => "Combed Weaving", 166 => "Carded Weaving", 167 => "Gray Melange Slub BCI", 168 => "Contra Free", 169 => "Carded Ring Spun", 170 => "Mohair Melange", 171 => "Linen", 172 => "VP Slub", 173 => "Melange Combed", 174 => "Gassed Mercerized Combed", 175 => "Rayon", 176 => "BCI Slub", 177 => "BCI Inject", 178 => "Coolmax", 179 => "Black Polyester", 180 => "Dope Dyed Polyester", 181 => "Green Polyester", 182 => "Super Combed", 183 => "Super Carded", 184 => "Recycle CVC", 185 => "Glitter", 186 => "Viscose-Glitter", 187 => "Combed Compact Contra Free", 188 => "Black Spandex", 189 => "Compact Slub", 190 => "BCI-CVC", 191 => "BCI-PC", 192 => "Siro Compact", 193 => "Carded Weaving Compact", 194 => "Combed Weaving Compact", 195 => "Core Spandex", 196 => "Ring Combed", 197 => "S-Twist", 198 => "Filament", 199 => "Combed CVC", 200 => "Twist Slub", 201 => "Dope Dyed Yellow", 202 => "Neppy", 203 => "PC Siro", 204 => "CVC Black", 205 => "Monofilament", 206 => "OE Contra Free", 207 => "Long Staple Cotton", 208 => "Blended Yarn", 209 => "CVC GRAPE WINE", 210 => "CVC Navy", 211 => "Carded Contra Free Slub", 212 => "", 213 => "X-Static", 214 => "White Polyester", 215 => "Cotton-Wool", 216 => "Air Covered", 217 => "Invista", 218 => "Combed Compact Slub", 219 => "Semi Dull[NIM]", 220 => "Semi Dull [SIM]", 221 =>"Semi Dull [LIM]", 222 =>"Polyester Navy", 223 =>"Chennil", 224 =>"PC (Moroccan Blue)", 225 =>"PC (Mid Grey Mélange)", 226 =>"PC (Cameo Pink)", 227 =>"PC (Violet Ice)", 228 =>"PC (Navy Blue)", 229 =>"Soft Vortex", 230 =>"Ecru Melange Slub",231 =>"Melange Vortex",232 =>"Fully Drawn yarn",233 =>"Draw Texturing Yarn",234 =>"RING POLYESTER",235 =>"Vortex Contra Free",236 =>"CMIA",237 =>"Anthra Mel",238 =>"Anthra Mel Slub",239 =>"Ring Slub",240=>"Core Cotton",241=>"Modal Cotton Viscose",242=>"Bamboo Cotton",243=>"Combed PC",244=>"Vortex BCI",245=>"Heat Tech",246=>"Cima",247=>"GOTS",248=>"Ecru Melange",249=>"Anthra Melange",250=>"Contra Control",251=>"Suprima Combed",252=>"Combed Contra Free Slub",253=>"Bright",254=>"Inject White",255=>"Inject Black",256=>"Melange[OE]",257=>"Fome",258=>"Z-Twist",259=>"DSN",260=>"Carded CVC",261=>"Cotton Modal",262=>"MID MARLE",263=>"Cotton Modal Viscose Melange",264=>"CH",265=>"Dope Dyed Green",266=>"Inject Reverse Flack",267=>"Organic Conta Free",268=>"Thermolite PC",269=>"Lyocell",270=>"Recycle-Vortex",271=>"Elastomeric",272=>"Siro Snow",273=>"Project COMBED",274=>"CPM",275=>"TP",276=>"Cotton Slub",277=>"PC Vortex"); 
asort($yarn_type);//     

$service_type = array(1 => "Knitting", 2 => "Collar and Cuff Knitting", 3 => "Feeder Stripe Knitting", 10 => "Yarn Dyeing", 11 => "Fabric Dyeing", 12 => "All Over Printing", 20 => "Scouring", 21 => "Brushing", 22 => "Sueding", 23 => "Washing", 24 => "Stentering", 25 => "Compacting", 40 => "Cutting", 41 => "Gmts. Printing", 42 => "Gmt. Embroidery", 43 => "Gmts. Washing", 44 => "Sewing");
//$service_type= array(1=>"AOP",2=>"Yarn Dyeing",3=>"Gmt. Print",4=>"Gmt. Embroidery",5=>"Gmt. Wash",6=>"Scouring",7=>"Brushing",8=>"Sueding",9=>"Knitting",10=>"Dyeing",11=>"Collar and Cuff Knitting",12=>"Feeder Stripe Knitting",13=>"Stripe Print Charge",20=>"Others");
//$export_finance_loan_type=array(1=>"Packing Credit",2=>"Export Cash Credit");

$lc_type = array(
	1 => "BTB LC",
	2 => "Margin LC",
	3 => "Fund Building",
);

$source_pay_term = array(
	1 => "01 Import LC - At sight",
	2 => "02 Import LC - Usance",
	3 => "03 BTB Inland - At sight",
	4 => "04 BTB Inland - Usance",
	5 => "05 BTB Foreign LC - At sight",
	6 => "06 BTB Foreign LC - Usance",
	7 => "10 Import LC EPZ - Usance",
	8 => "11 BTB LC EPZ - At sight",
	9 => "12 BTB LC EPZ - Usance",
	10 => "99 Import From Inland to EPZ",
);

$supply_source = array(1 => "CASH LC AT SIGHT (01)", 2 => "CASH LC USANCE (02)", 3 => "IN LAND BTB LC AT SIGHT (03)", 4 => "IN LAND BTB LC USANCE (04)", 5 => "FOREIGN BTB LC AT SIGHT (05)", 6 => "FOREIGN BTB LC USANCE(06)", 11 => "EPZ BTB LC  AT SIGHT (11)", 12 => "EPZ BTB LC USANCE (12)", 23 => "CASH LC AT SIGHT (23)", 99 => "99 Others");

$maturity_from = array(
	1 => "Acceptance Date",
	2 => "Shipment Date",
	3 => "Negotiation Date",
	4 => "B/L Date",
	5 => "Delivery Challan Date",
);
$credit_to_be_advised = array(1 => "Teletransmission", 2 => "Airmail", 3 => "Courier", 4 => "Airmail/Courier", 5 => "Telex", 6 => "SWIFT");
$increase_decrease = array(1 => "Increase", 2 => "Decrease");



//$commercial_head = array(1=>"Negotiation Loan/Liability", 5=>"BTB Margin/DFC/BLO A/C", 6=>"ERQ A/C", 10=>"CD Account", 11=>"STD A/C", 15=>"CC Account", 16=>"OD A/C20", 20=>"Packing Credit", 21=>"Bi-Salam/PC", 22=>"Export Cash Credit", 30=>"EDF A/C", 31=>"PAD", 32=>"LTR", 33=>"FTT/TR", 34=>"LIM", 35=>"Term Loan", 36=>"Force Loan", 40=>"IFDBC Liability", 45=>"Bank Charge", 46=>"SWIFT Charge", 47=>"Postage Charge", 48=>"Handling Charge", 49=>"Source Tax", 50=>"Excise Duty", 51=>"Foreign Collection Charge", 60=>"Other Charge", 61=>"Foreign Commision", 62=>"Local  Commision", 63=>"Penalty on Doc Descrepency", 64=>"Penalty on Goods Descrepency", 65=>"FDBC Commision", 70=>"Interest", 71=>"Import Margin A/C", 75=>"Discount A/C", 76=>"Advance A/C", 80=>"HPSM", 81=>"Sundry A/C", 82=>"MDA Special", 83=>"MDA UR", 84=>"Vat", 85=>"FDR Build up", 86=>"Miscellaneous Charge", 87=>"others Fund(sinking)", 88=>"Bank Commission", 89=>"Vat On Bank Commission", 90=>"Insurance Coverage", 91=>"Add Confirmation Change", 92=>"MDA Normal", 93=>"Settlement A/C", 94=>"Cash Security A/C", 95=>"Loan A/C", 96=>"Courier Charge", 97=>"Telex Charge", 98=>"Application Form Fee",99=>"UPAS",100=>"Offshore",101=>"Stationary",102=>"Stamp Charge",103=>"Amendment Charge",104=>"Long Term Loan-Secured",105=>"Long Term Loan-UnSecured", 106=>"Demand Loan",107=>"SOD", 108=>"Pre-Shipment Finance",109=>"Post-Shipment Finance",110=>"Pre-Import Finance"); //,111=>"MPI"

//113=>"VAT on Bank Commission",//Dublicate Found
$commercial_head = array(1 => "Negotiation Loan/Liability", 5 => "BTB Margin/DFC/BLO/DAD A/C", 6 => " ERQ/FCAD A/C", 10 => "CD Account", 11 => "STD A/C", 15 => "CC Account", 16 => "OD A/C", 20 => "Packing Credit", 21 => "Bi-Salam/PC", 22 => "Export Cash Credit", 30 => "EDF A/C", 31 => "PAD", 32 => " LTR/MPI", 33 => "FTT/TR", 34 => "LIM", 35 => "Term Loan", 36 => "Force Loan", 40 => "IFDBC Liability", 45 => "Bank Charge", 46 => "SWIFT Charge", 47 => "Postage Charge", 48 => "Handling Charge", 49 => "Source Tax", 50 => "Excise Duty", 51 => "Foreign Collection Charge", 60 => "Other Charge", 61 => "Foreign Commission", 62 => "Local  Commission", 63 => "Penalty on Doc Discrepancy", 64 => "Penalty on Goods Discrepancy", 65 => "FDBC Commission", 70 => "Interest", 71 => "Import Margin A/C", 75 => "Discount A/C", 76 => "Advance A/C", 80 => "HPSM", 81 => "Sundry A/C", 82 => "MDA Special", 83 => "MDA UR", 84 => "Vat On Bank Commission", 85 => "FDR Build up", 86 => "Miscellaneous Charge", 87 => "others Fund(sinking)", 88 => "Bank Commission", 89 => "VAT", 90 => "Insurance Coverage", 91 => "Add Confirmation Change", 92 => "MDA Normal", 93 => "Settlement A/C", 94 => "Cash Security A/C", 95 => "Loan A/C", 96 => "Courier Charge", 97 => "Telex Charge", 98 => "Application Form Fee", 99 => "UPAS", 100 => "Offshore", 101 => "Stationary", 102 => "Stamp Charge", 103 => "Amendment Charge", 104 => "Long Term Loan-Secured", 105 => "Long Term Loan-Unsecured", 106 => "Demand Loan", 107 => "SOD", 108 => "Pre-Shipment Finance", 109 => "Post-Shipment Finance", 110 => "Pre-Import Finance", 111 => "Bank Guarantee Charge", 112 => "VAT on SWIFT Charge", 114 => "VAT on Add Confirmation Charge", 115 => "VAT on LC Application Form Fee", 116 => "VAT on Stamp Charge", 117 => "VAT on Bank Guarantee Charge", 118 => "VAT on Miscellaneous Charge", 119 => "Post-Import Finance", 120 => "Cash Incentive loan", 121 => "Additional Tax", 122 => "Exp Charge", 123 => "Special Notice Deposit [SND]", 124 => "Local Collection Charge", 125 => "Central Fund", 126 => "Re-Imbursement Payment", 127 => "Retirement", 128 => "Overdue interest", 129 => "RMG", 130 => "Export Reserve Margin", 131 => "BTB Margin(Foreign)", 132 => "BTB Margin(Local)", 133 => "BTB Margin (BUP)", 134 => "Advance Income Tex (AIT)", 135 => "Interest For Factoringg", 136 => "Late shipment penalty", 137 => "Late presentation charges", 138 => "Security For factoring", 139 => "LC Goods Releasing NOC Charge",140 => "TT/DD Charge",141 => "Accept Comm. Charge",142 => "UPASS / MISS UPASS",143 => "Outstanding Claim",144 => "Discounted to Buyer",145 => "CBM Discrepency",146 => "Late Inspection penalty",147 => "Short Shipment",148 => "Air Release Charges for Document delay",149 => "Buyer Discripency Fee",150 => "Negotiation Charge",151 => "Trade Sourcing Fee (TSF)",152 => "Product Liability Insurance (PLI)",153 => "Trade Commission for Service (TCS)",154 => "Shipment Endorsement fee/FCR Endorsement Fee",155 => "Online Transfer Charge",156 => "Commission In Lieu of Exchange (CILE)",157 => "Usance Commission",158 => "LC Transferring Charge",159 => "Document Examination Fee",160 => "Azo free cert/Te-Test report",161 => "Document Tracer charge",162 => "IBB A/C",163 => "MTR A/C",164 => "CC HYPO A/C",165 => "General A/C");


$acceptance_time = array(1 => "After Goods Receive", 2 => "Before Goods Receive");
$document_status = array(1 => "Original", 2 => "Copy");
$submited_to = array(1 => "Lien Bank", 2 => "Buyer");
$submission_type = array(1 => "Collection", 2 => "Negotiation");
//$supply_source=array(01=>"01 Foreign Cash Sight",02=>"02 Foreign Deferred Cash",03=>"03 EDF Local",04=>"04 Local",05=>"05 EDF Foreign",06=>"06 Foreign",11=>"11 EPZ EDF",12=>"12 EPZ BTB",99=>"99 Others");

$document_set = array(1 => "Bill of Exchange", 2 => "Delivery Challan", 3 => "Commercial Invoice", 4 => "Packing List", 5 => "Certificate of Origin", 6 => "Beneficiary Certificate", 7 => "Mushak -11", 8 => "Truck Receipt", 9 => "BTMA", 10 => "Letter of Credit with Proforma Invoice", 11 => "Pre-Shipment Inspection Certificate", 12 => "B/L No", 13 => "GSP FORM A", 14 => "EXP NO.", 15 => "Inspection Certificate", 16 => "Air Way Bill", 17 => "Courier Receipt No.", 18 => "Master Bill of Landing", 19 => "Production Certificate", 20 => " LDC Statement", 21 => "Annual Packing Declaration", 22 => "Certificate Of Origin of YARN", 23 => "Certificate of Origin of Cotton", 24 => "Summary Sheet of all Bale Number");

//--------------------------------------------------------------------End Commercial Module Array--------------------------------------//

//--------------------------------------------------------------- Start Accounts Module Array ------------------------------------------------//

$accounts_main_group = array(1 => "OWNERS EQUITY",
	2 => "NON-CURRENT LIABILITIES",
	3 => "CURRENT LIABILITIES",
	4 => "NON-CURRENT ASSETS",
	5 => "CURRENT ASSETS",
	6 => "REVENUE",
	7 => "COST OF GOOD SOLD",
	8 => "OPERATING EXPENSES",
	9 => "FINANCIAL EXPENSES",
	10 => "NON-OPERATING INCOME & EXPENSE",
	11 => "EXTRA ORDINARY ITEMS",
	12 => "TAX EXPENSE");

$accounts_statement_type = array(1 => "Balance Sheet",
	2 => "Income Statement"); //Profit & Loss

$accounts_account_type = array(1 => "Credit",
	2 => "Debit");

$accounts_cash_flow_group = array(1 => "Operating Activities",
	2 => "Investing Activities",
	3 => "Financing Activities",
	4 => "Cash & Cash Equivalents");

/* OLD
								1=>"Financing Activities",
									2=>"Operating Activities",
									3=>"Investing Activities",
									4=>"Cash & Cash Equivalents",
									5=>"Operating Activities");
									*/

$accounts_journal_type = array(
	1 => "Opening/Closing Journal",
	2 => "Credit Purchase Journal",
	3 => "Credit Sales Journal",
	4 => "Cash withdrawn Journal",
	5 => "Cash Deposit Journal",
	6 => "Cash Receive Journal",
	7 => "Cheque Deposit Journal",
	8 => "Cash Payment Journal",
	9 => "Export Realization Journal",
	10 => "Bank Payment Journal",
	11 => "Adjustment Journal",
	12 => "Provisional Journal",
	13 => "Reverse Journal",
	14 => "Rectifying Journal",
	15 => "General Journal");

$control_accounts = array(1 => "AP",
	2 => "AR",
	3 => "Import Payable",
	4 => "Export Receivable",
	5 => "Advance Paid",
	6 => "Advance Received",
	7 => "Export Negotiation Liability",
	8 => "Other Trade Finance",
	9 => "Tax at source from Suppliers' Bill",
	10 => "Tax at source from Sales Bill",
	11 => "VAT at source from Suppliers' Bill",
	12 => "VAT at source from Sales Bill",
	13 => "Security at source from Suppliers' Bill",
	14 => "Security at source from Sales Bill",
	15 => "Tax at source from Employees' Salary",
	16 => "Discount Allowed",
	17 => "Discount Received",
	18 => "Write-off Assets",
	19 => "Write-off Liability",
	20 => "Other Subsidiary",
	21 => "Advance Paid-Employee",
	22 => "Advance Paid-Others",
	23 => "Advance Received-Employee",
	24 => "Advance Received-Others",
	25 => "Goods in Transit",
	26 => "ILE Control Account",
	27 => "Yarn Dyeing Charge");

$account_nature = array(1 => "Cash",
	2 => "Bank",
	3 => "OD/CC",
	4 => "Foreign Sales",
	5 => "Local Sales",
	6 => "Project Sales",
	7 => "Purchase",
	8 => "Project Cost",
	9 => "Interest",
	10 => "Bank Charges",
	11 => "Currency Exchange Gain/Loss - Export",
	12 => "Currency Exchange Gain/Loss - Import",
	13 => "Project Common Cost",
	14 => "Depreciation, Amortization & Depletion");

$instrument_type = array(1 => "Bearer Cheque",
	2 => "Crossed Cheque",
	3 => "Pay Order",
	4 => "TT",
	5 => "DD",
	6 => "Special Crossed Cheque",
	7 => "Deposit Slip");

$ac_loan_type = array(
	1 => "PAD",
	2 => "LTR",
	3 => "LIM",
	10 => "Packing Credit",
	11 => "ECC",
	20 => "Term Loan",
	50 => "Project Loan");

$ratio_category = array(
	1 => "Liquidity",
	2 => "Activity",
	3 => "Leverage",
	4 => "Profitability",
	5 => "Market");

//------------------------------------------------------ End Accounts Module Array -------------------------------------------------------------//

//---------------all day---------------------------------------sohel -------------------------------------------------------------//

//--------------------------TNA_task----------------------------------------

$general_task = array(1 => "Order Placement Date", 2 => "Order Evaluation", 3 => "Acceptance to be given", 4 => "Internal communication to be done");
$test_approval_task = array(1 => "Fabric test to be done", 2 => "Garments test to be done");
$purchase_task = array(1 => "Fabric booking to be issued", 2 => "Trims booking to be issued", 3 => "Fabric service work order to be issued", 4 => "Sample Fabric booking to be issued");
$material_receive_task = array(1 => "Gray fabric to be in-house", 2 => "Finished fabric to be in-house", 3 => "Sewing trims to be in-house", 4 => "Finishing trims to be in-house");

$fabric_production_task = array(1 => "Gray fabric production to be done", 2 => "Dyeing production to be done", 3 => "Finish fabric production to be done", 4 => "Yarn Send for Dyeing", 5 => "Dyed Yarn Receive", 6 => "Fabric Send for AOP", 7 => "AOP Receive");

$garments_production_task = array(1 => "PP meeting to be conducted", 2 => "Trail cut to be done", 3 => "Trail production to be submitted", 4 => "Trail production approval to be received", 5 => "PCD to be end", 6 => "Print/Emb TOD  to be end", 7 => "Sewing  to be end", 8 => "Garments finishing to be done");

$inspection_task = array(1 => "Inspection schedule to be offered", 2 => "Inspection to be done");
$export_task = array(1 => "Ex-Factory to be done", 2 => "Document to be submited", 3 => "Proceeds to be realized");
$lapdip_task_name = array(1 => "Submission", 2 => "Target Approval");
$embelishment_approval_task = $emblishment_name_array; //array(1=>"Local",2=>"Imported");

$category_wise_task_array = array(1 => "General Task", 5 => "Sample Approval Task", 6 => "Lapdip Task Name", 7 => "Trims Approval Task", 8 => "Embelishment Approval Task", 9 => "Test Approval Task", 15 => "Purchase Task", 20 => "Material Receive Task", 25 => "Fabric Production Task", 26 => "Garments Production Task", 30 => "Inspection Task", 35 => "Export Task");

$material_source = array(1 => "Local", 2 => "Imported");
$test_approval_task = array(1 => "Fabric Approval", 2 => "Garments Approval");
$inspection_task = array(1 => "Inspection Offered", 2 => "Inspection Completed");
$knit_fabric_production_task = array(1 => "Knitting", 2 => "Dyeing & Finishing");
$woven_fabric_production_task = array(1 => "Weaving", 2 => "Dyeing", 3 => "Finishing");
$material_purchase_task = array(1 => "Fabric Booking", 2 => "Trims Booking", 3 => "Embellishment Booking");

$knitting_source = array(1 => "In-house", 2 => "In-bound Subcontract", 3 => "Out-bound Subcontract");

$time_source = array(1 => "AM", 2 => "PM");
$order_source = array(1 => "Self Order", 2 => "Subcontract Order", 3 => "Reprocess Batch", 4 => "Trims Batch");

$tna_task_name = array(
	1 => "Order Placement Date",
	2 => "Order Evaluation",
	3 => "Acceptance To Be Given",
	4 => "Internal Communication To Be Done",
	5 => "SC/LC Received",
	7 => "Fit Sample Submit",
	8 => "PP Sample Submit",
	9 => "Labdip Submit",
	10 => "Labdip Approval",
	11 => "Trims Approval",
	12 => "PP Sample Approval",
	13 => "Fit Sample Approval",
	14 => "Size Set Submission",
	15 => "Size Set Approval",
	16 => "Production Sample Submission",
	17 => "Production Sample Approval",
	18 => "Labdip Requisition",
	19 => "Embellishment Submission",
	20 => "Embellishment Approval",
	21 => "Tag Sample Submission",
	22 => "Tag Sample Approval",
	23 => "Photo Sample Submission",
	24 => "Photo Sample Approval",
	25 => "Trims Submission",
	26 => "Packing  Sample Submission",
	27 => "Packing  Sample Approval",
	28 => "Final  Sample Submission",
	29 => "Final  Sample Approval",
	30 => "Sample Fabric Booking To Be Issued",
	31 => "Fabric Booking To Be Issued",
	32 => "Trims Booking To Be Issued",
	33 => "Fabric Service Work Order To Be Issued",
	34 => "Woven Fabric Work Order To Be Issued",
	35 => "Labdip Receive From Factory",
	36 => "PP Sample Requisition",
	37 => "PPS Making",
	40 => "Fabric Test To Be Done",
	41 => "Garments Test To Be Done",
	45 => "Yarn purchase requisition",
	46 => "Yarn purchase order",
	47 => "Yarn Receive",
	48 => "Yarn Allocating",
	50 => "Yarn Issue To Be Done",
	51 => "Yarn Send for Dyeing",
	52 => "Dyed Yarn Receive",

	60 => "Gray Fabric Production To Be Done",
	61 => "Dyeing Production To Be Done",
	62 => "Fabric Send for AOP",
	63 => "AOP Receive",
	64 => "Finish Fabric Production To Be Done",
	70 => "Sewing Trims To Be In-house",
	71 => "Finishing Trims To Be In-house",
	72 => "Gray fabric to be in-house",
	73 => "Finished fabric to be in-house",
	74 => "Finish Fabric Issue to Cut",
	80 => "PP Meeting To Be Conducted",
	81 => "Trial cut to be done",
	82 => "Trial production to be submitted",
	83 => "Trial production approval to be received",
	84 => "Cutting To Be Done",
	85 => "Print/Emb To Be Done",
	86 => "Sewing To Be Done",
	87 => "Iron To Be Done",
	88 => "Garments Finishing To Be Done",
	89 => "Garments sent for Wash",
	90 => "Garments Receive from Wash",
	91 => "Poly Entry done",
	100 => "Inspection Schedule To Be Offered",
	101 => "Inspection To Be Done",
	110 => "Ex-Factory To Be Done",
	120 => "Document to be submited",
	121 => "Proceeds to be realized",
	122 => "Sewing Input To Be Done",
	123 => "Test Sample Approval",
	124 => "Photo In Lay/Litho Link",
	125 => "CAD - Marker",
	126 => "PSO Submit",
	127 => "PSO Approval",
	128 => "ESO Submit",
	129 => "ESO Approval",
	130 => "Trim Card Handover",
	131 => "Production File Handover",
	132 => "Pre Final",
	133 => "Tech File Receive Date",
	134 => "Packing Accessories Booking ",
	135 => "Fit Sample Fabric Booking",
	136 => "Size Set Sample Fabric Booking",
	137 => "Production Sample Fabric Booking",
	138 => "Tag Sample Fabric Booking",
	139 => "Photo Sample Fabric Booking",
	140 => "Packing Sample Fabric Booking",
	141 => "Final Sample Fabric Booking",
	142 => "PPS Fabric Booking",
	143 => "Fit  Sample Requisition",
	144 => "Fit Sample Fabric Requisition",
	145 => "Production Sample Requisition",
	146 => "Tag Sample Requisition",
	147 => "Photo Sample Requisition",
	148 => "Packing Sample Requisition",
	149 => "Final Sample Fabric Requisition",
	150 => "PPS Fabric Issue",
	151 => "Fit Sample Fabric Issue",
	152 => "Size Set Sample Fabric Issue",
	153 => "Production Sample Fabric Issue",
	154 => "Tag Sample Fabric Issue",
	155 => "Photo Sample Fabric Issue",
	156 => "Packing Sample Fabric Issue",
	157 => "Final Sample Fabric Issue",
	158 => "Fit Sample Making",
	159 => "Size Set Sample Making",
	160 => "Production Sample Making",
	161 => "Tag Sample Making",
	162 => "Photo Sample Making",
	163 => "Packing Sample Making",
	164 => "Final Sample Making",
	165 => "Yarndip Requisition",
	166 => "Yarndip Submit To Buyer",
	167 => "Yarndip Approval",

	168 => "PPS Fabrics Issue (AOP)",
	169 => "PPS Fabrics Issue (YD)",
	170 => "PPS Making (AOP)",
	171 => "PPS Making (YD)",
	172 => "PPS Submit (AOP)",
	173 => "PPS Approval (AOP)",
	174 => "PPS Submit (YD)",
	175 => "PPS Approval (YD)",
	176 => "Trim Card Handover (AOP)",
	177 => "Trim Card Handover (YD)",
	178 => "Knitting production (YD)",
	179 => "Finish Fabrics Inhouse (AOP)",
	180 => "Finish Fabrics Inhouse (YD)",
	181 => "Production File Handover(YD)",
	182 => "Size Set Making (AOP)",
	183 => "Size Set Making (YD)",
	184 => "PP Meeting (AOP)",
	185 => "PP Meeting (YD)",
	186 => "Cutting Production (AOP)",
	187 => "Cutting Production (YD)",
	188 => "Embellishment (AOP)",
	189 => "Embellishment (YD)",
	190 => "Sewing Production(AOP)",
	191 => "Sewing Production(YD)",
	192 => "Proto Sample Requisition",

	193 => "Proto Sample Submission",
	194 => "Proto Sample Approval",
	195 => "Counter Sample Fabric Booking",
	196 => "Counter Sample Requisition",
	197 => "Counter Sample Submit",
	198 => "Counter Sample Approval",

	199 => "Fabric Sales Order",
	200 => "Knitting Plan Solid",
	201 => "Grey Fabric Delivery To Store",
	202 => "Grey Fabric Requisition for batch",
	203 => "Grey Fabric Issue",
	204 => "Grey Receive By Batch",
	205 => "Batch Creation",
	206 => "Fabric Service Receive",
	207 => "Finish fabric Delivery to Store",
	208 => "Fabric Test AOP",
	209 => "Fabric Test YD",
	210 => "Knitting Plan AOP",
	211 => "Knitting Plan YD",
	212 => "Knitting Production Solid",
	213 => "Labdip Approval AOP",
	214 => "Labdip Approval YD",
	215 => "Dyeing Production AOP",
	216 => "Dyeing Production YD",
	217 => "Fabric Service Send AOP",
	218 => "Fabric Service Send YD",
	219 => "In-Line Inspection",
	220 => "Mid-Line Inspection",
	221 => "Documents Mailing",
	222 => "Forwarder Booking",
	223 => "Final Inspection Booking",
	224 => "Export PI Issue",
	225 => "LC Rcv at Bank",
	226 => "B2B LC Arrange",
	227 => "MO Issue Date",
	228 => "Bulk Swatch Ready Date",
	229 => "Pilot Run Review",
	230 => "Packing Method Approval Date",
	231 => "Packing RM Rcv Date",
	232 => "Packing List Rcv Date",
	233 => "Pack Finish Date",
	234 => "Vessel Booking",
	235 => "Garments Handover Date",
	236 => "Document Dispatched Date",
	237 => "Yarn PI Rcv Date",
	238 => "Grey PI Rcv Date",
	239 => "Finish Fabric Delivery To Garments",
	240 => "Yarn ETD",
	241 => "Yarn ETA",
	242 => "Trims ETD",
	243 => "Trims ETA",
	244 => "Trims Inhouse",
	245 => "Bulk Yarn App",
	246 => "In Line",
	247 => "BL Date",
	248 => "Size Set Sample Requisition",
	249 => "First Batch Production",
	250 => "Trial Production run",
	251 => "Fabric quality sample collection",
	252 => "Yarn Store Requisition",
	253 => "Embellishment Solid",
	254=> "AOP Strike Off Submission",
	255=> "AOP Strike Off Approval",
	256=> "YD Knit down Submission",
	257=> "YD Knit down Approval",
	258=> "Bulk Hanger submission",
	259=> "Bulk Hanger Approval",
	260=> "Buying Sample Submission",
	261=> "Buying Sample Approval",
	262=> "Gold Seal Sample Submission",
	263=> "Gold Seal Sample Approval",
	264=>"Test Sample Submission",
	265=>"SMS Sample Submission",
	266=>"SMS Sample Approval"
);
asort($tna_task_name);

$tna_common_task_name_to_process = array(
	1 => "Order Placement Date",
	2 => "Order Evaluation",
	3 => "Acceptance To Be Given",
	4 => "Internal Communication To Be Done",
	5 => "SC/LC Received",
	7 => "Fit Sample Submit",
	8 => "PP Sample Submit",
	9 => "Labdip Submit",
	10 => "Labdip Approval",
	12 => "PP Sample Approval",
	13 => "Fit Sample Approval",
	14 => "Size Set Submission",
	15 => "Size Set Approval",
	17 => "Production Sample Approval",
	21 => "Tag Sample Submission",
	22 => "Tag Sample Approval",
	25 => "Trims Submission",
	28 => "Final Sample Submissinon",
	29 => "Final Sample Approval",
	30 => "Sample Fabric Booking To Be Issued",
	//31	=> "Fabric Booking To Be Issued",
	32 => "Trims Booking To Be Issued",
	33 => "Fabric Service Work Order To Be Issued",
	//34	=> "Woven Fabric Work Order To Be Issued",

	36 => "PP Sample Requisition",

	40 => "Fabric Test To Be Done",
	41 => "Garments Test To Be Done",
	//45	=> "Yarn purchase requisition",
	//46	=> "Yarn purchase order",
	//47	=> "Yarn Receive",
	//48	=> "Yarn Allocating",
	50	=> "Yarn Issue To Be Done",

	//60	=> "Gray Fabric Production To Be Done",
	61	=> "Dyeing Production To Be Done",
	64	=> "Finish Fabric Production To Be Done",
	70 => "Sewing Trims To Be In-house",
	71 => "Finishing Trims To Be In-house",
	//72	=> "Gray fabric to be in-house",
	//73	=> "Finished fabric to be in-house",
	74 => "Finish Fabric Issue to Cut",
	//80	=> "PP Meeting To Be Conducted",
	81 => "Trail cut to be done",
	82 => "Trail production to be submitted",
	83 => "Trail production approval to be received",
	//85 => "Print/Emb To Be Done",
	//84	=> "Cutting To Be Done",
	//86	=> "Sewing To Be Done",
	87 => "Iron To Be Done",
	91 => "Poly Entry done",
	88 => "Garments Finishing To Be Done",
	100 => "Inspection Schedule To Be Offered",
	101 => "Inspection To Be Done",
	110 => "Ex-Factory To Be Done",
	120 => "Document to be submited",
	121 => "Proceeds to be realized",
	122 => "Sewing Input To Be Done", //NEW BELOW


	126 => "PSO Submit",
	127 => "PSO Approval",
	128 => "ESO Submit",
	129 => "ESO Approval",



	141 => 'Final Sample Fabric Booking',
	149 => 'Final Sample Fabric Requisition',
	157 => 'Final Sample Fabric Issue',
	164 => 'Final Sample Making',
	135 => 'Fit Sample Fabric Booking',
	158 => 'Fit Sample Making',
	143 => 'Fit  Sample Requisition',
	151 => 'Fit Sample Fabric Issue',
	18 => "Labdip Requisition",
	35 => "Labdip Receive From Factory",
	//150	=>'PPS Fabric Issue',
	142 => 'PPS Fabric Booking',
	36 => "PP Sample Requisition",
	//37  => "PPS Making",
	163 => 'Packing Sample Making',
	140 => 'Packing Sample Fabric Booking',
	148 => 'Packing Sample Requisition',
	156 => 'Packing Sample Fabric Issue',
	134 => 'Packing Accessories Booking',
	162 => 'Photo Sample Making',
	139 => 'Photo Sample Fabric Booking',
	147 => 'Photo Sample Requisition',
	155 => 'Photo Sample Fabric Issue',
	132 => "Pre Final",
	//131 => "Production File Handover",
	160 => 'Production Sample Making',
	137 => 'Production Sample Fabric Booking',
	145 => 'Production Sample Requisition',
	153 => 'Production Sample Fabric Issue',
	//159	=>'Size Set Sample Making',
	136 => 'Size Set Sample Fabric Booking',
	152 => 'Size Set Sample Fabric Issue',
	161 => 'Tag Sample Making',
	138 => 'Tag Sample Fabric Booking',
	146 => 'Tag Sample Requisition',
	154 => 'Tag Sample Fabric Issue',
	133 => 'Tech File Receive Date',
	//130 => "Trim Card Handover",
	//167	=>'Yarndip Approval',
	//165	=>'Yarndip Requisition',
	//166	=>'Yarndip Submit To Buyer',
	125 => "CAD - Marker",
	192 => "Proto Sample Requisition",

	193 => "Proto Sample Submission",
	194 => "Proto Sample Approval",
	195 => "Counter Sample Fabric Booking",
	196 => "Counter Sample Requisition",
	197 => "Counter Sample Submit",
	198 => "Counter Sample Approval",
	199 => "Fabric Sales Order",
	200 => "Knitting Plan Solid",
	210 => "Knitting Plan AOP",
	211 => "Knitting Plan YD",
	219 => "In-Line Inspection",
	221 => "Documents Mailing",
	223 => "Final Inspection Booking",
	224 => "Export PI Issue",
	225 => "LC Rcv at Bank",
	226 => "B2B LC Arrange",
	227 => "MO Issue Date",
	228 => "Bulk Swatch Ready Date",
	229 => "Pilot Run Review",
	230 => "Packing Method Approval Date",
	231 => "Packing RM Rcv Date",
	232 => "Packing List Rcv Date",
	233 => "Pack Finish Date",
	234 => "Vessel Booking",
	235 => "Garments Handover Date",
	236 => "Document Dispatched Date",
	237 => "Yarn PI Rcv Date",
	238 => "Grey PI Rcv Date",
	242 => "Trims ETD",
	250 => "Trial Production run",
	251 => "Fabric quality sample collection",
	252 => "Yarn Store Requisition",
	260=> "Buying Sample Submission",
	261=> "Buying Sample Approval",
	262=> "Gold Seal Sample Submission",
	263=> "Gold Seal Sample Approval",
	264=>"Test Sample Submission",
	265=>"SMS Sample Submission",
	266=>"SMS Sample Approval"

);

//--------------------------- Start Inventory ------------- 04_03_2013  --------------------
// Bill processing page
$bill_disupcharge = array(1 => "Discount", 2 => "Upcharge");
//Yarn Receive Basis
$receive_basis_arr = array(1 => "PI Based", 2 => "WO/Booking Based", 3 => "In-Bound Subcontract", 4 => "Independent", 5 => "Batch Based", 6 => "Opening Balance", 7 => "Requisition", 8 => "Recipe Based", 9 => "Production", 10 => "Delivery", 11 => "Service Booking Based", 12 => "Delivery Challan(Int.)", 13 => "Delivery Challan(Ext.)", 14 => "Sales Order Based", 15 => "Job Card",16=>"Delivery From Store");
//Yarn Issue Entry
$yarn_issue_purpose = array(1 => "Knitting", 2 => "Yarn Dyeing", 3 => "Sales", 4 => "Sample With Order", 5 => "Loan", 6 => "Sample-material", 7 => "Yarn Test", 8 => "Sample Without Order", 9 => "Sewing Production", 10 => "Fabric Test", 11 => "Fabric Dyeing", 12 => "Reconning", 13 => "Machine Wash", 14 => "Topping", 15 => "Twisting", 16 => "Grey Yarn", 26 => "Damage", 27 => "Pilferage", 28 => "Expired", 29 => "Stolen", 30 => "Adjustment", 31 => "Scrap Store", 32 => "ETP", 33 => "WTP", 34 => "Wash", 35 => "Re Wash", 36 => "Sewing", 37 => "Dyeing", 38 => "Re-Waxing", 39 => "Moisturizing", 40 => "Lab Test", 41 => "Cutting", 42 => "Finishing", 43 => "Dyed Yarn Purchase", 44 => "Re Process", 45 => "Used Cone Sale", 46 => "Dryer", 47 => "Linking", 48 => "Boiler", 49 => "Generator", 50 => "Doubling", 51 => "Punda", 52 => "AOP", 53 => "Production", 54 => "Narrow Fabric", 55 => "Scrap Store"); 
$using_item_arr = array(1 => 'Drawstring', 2 => 'Twill Tape', 3 => 'Collar', 4 => 'Cuff', 5 => 'Rubber Thread', 6 => 'Elastic', 7 => 'Development', 8 => "Oeko-Tex", 9 => "Lab Test Sample", 10 => "Yarn Test Sample");//Scrap Store
//Inventory Variable List. Created by sohel // As per Siddik and CTO -Remove it= 22=>"Independent Receive/Issue Basis"
$inventory_module = array(8 => "ILE/Landed Cost Standard", 9 => "Hide Opening Stock Flag", 10 => "Item Rate Manage in MRR", 11 => "Item QC", 16 => "User given item code", 17 => "Book Keeping Method", 18 => "Allocated Quantity", 19 => "Receive Control On Gate Entry", 20 => "Independent basis controll", 21 => "Rack Wise Balance Show", 23 => "Material Over Receive Control", 24 => "Issue Requisition Mandatory", 25 => "Yarn item and rate matching with budget", 26 => "Woven Finish Fabric Desc Change", 27 => "Auto Transfer Receive", 28 =>"Yarn Issue Basis", 29 =>"Dyes Chemical Lot Maintain", 30 =>"Requisition Basis Transfer", 31 =>"WO PI Receive Level");
//Transaction Type
$transaction_type = array(1 => "Receive", 2 => "Issue", 3 => "Receive Return", 4 => "Issue Return", 5 => "Item Transfer Receive", 6 => "Item Transfer Issue");
$issue_basis = array(1 => "Booking", 2 => "Independent", 3 => "Requisition", 4 => "Sales Order", 5 => "Job", 6 => "Lot Ratio", 7 => "Sample", 8=>"Demand", 9=>"Service Booking", 10=>"Sample Booking");
$store_method = array(1 => "FIFO", 2 => "LIFO");

$general_issue_purpose = array(1 => "Damage", 2 => "Pilferage", 3 => "Stolen", 4 => "Unknown", 5 => "Loan", 6 => "Sewing", 7 => "Cutting", 8 => "Finishing", 9 => "Building Development", 10 => "Land Development", 11 => "Generator", 12 => "Machinery", 13 => "Air Cooling System", 14 => "Furniture & Fixtures", 15 => "Sales", 16 => "Capital Expenditure", 17 => "Dyeing", 18 => "AOP", 19 => "Screen Print", 20 => "Yarn Production", 21 => "Sample With Order", 22 => "Sample Without Order", 23 => "Deffered Expenses", 24 => "Trims Production", 25 => "Thread Dyeing");

$quot_evaluation_factor = array(1 => "Quoted Item", 2 => "Specification", 3 => "Performance", 4 => "Brand", 5 => "Country of Origin", 6 => "Delivery Days", 7 => "Pay Term", 8 => "Warranty", 9 => "Service Agreement", 10 => "online Support", 11 => "Local Support Center", 12 => "Price");
 $fabric_service_type=array(1=>"Heat Setting",2=>"Singeing");

/*$get_pass_basis=array(1=>"Independent",2=>"Challan(Yarn)",3=>"Challan(Gray Fabric)",4=>"Challan(Finish Fabric)",5=>"Challan(General Item)",6=>"Challan(Trims)",6=>"Challan(Dyes & Chemical)",7=>"Challan(Trims)",8=>"Challan Subcon(grey fabric)",9=>"Challan Subcon (finish fabric)",10=>"Grey Fabric Delivery to Store",11=>"Finish Fabric Delivery to Store",12=>"Challan(Garments Delivery)",13=>"Challan(Embellishment Issue)",14=>"Challan[Cutting Delivery]",15=>"Challan[Finish Fab Rec Return]"
,16=>"Challan[Yarn-Transfer]",17=>"Challan[Grey Fabric-Transfer]",18=>"Challan[General Item-Transfer]",19=>"Challan[Trims-Transfer]",20=>"Challan[Dyes and Chemical-Transfer]",21=>"Challan(Yarn Recv Return)",22=>"Fabric Issue to Fin. Process",23=>"Challan[General Item Receive Return]",24=>"Challan[Trims Receive Return]",25=>"Challan[Dyes And Chemical Receive Return]",26=>"Challan[SubCon Material Return]",27=>"Challan Subcon[Garment Delivery]",28=>"Sample Delivery Challan",29=>"Challan[Woven Finish Fab Rec Return]",30=>"Challan[Fabric Service Receive Return]",31=>"Challan[Woven Finish Fabric Issue Return]",32=>"Challan[Woven Finish Fabric Issue]",33=>"Challan[SubCon Embellishment Delivery]");*/

$get_pass_basis = array(1 => "Independent", 2 => "Yarn Issue", 3 => "Knit Grey Fabric Issue/Return", 4 => "Knit Finish Fabric Issue", 5 => "General Item Issue", 6 => "Dyes And Chemical Issue", 7 => "Trims Issue", 8 => "SubCon Knitting Delivery", 9 => "SubCon Dyeing And Finishing Delivery", 10 => "Grey Fabric Delivery to Store", 11 => "Finish Fabric Delivery to Store", 12 => "Garments Delivery Entry/Return", 13 => "Embellishment Issue Entry", 14 => "Cutting Delivery To Input Challan", 15 => "Knit Finish Fabric Receive Return"
	, 16 => "Yarn Transfer", 17 => "Grey Fabric Transfer", 18 => "General Item Transfer", 19 => "Trims Transfer", 20 => "Dyes and Chemical Transfer", 21 => "Yarn Recv Return", 22 => "Fabric Issue to Finish Process", 23 => "General Item Receive Return", 24 => "Trims Receive Return", 25 => "Dyes And Chemical Receive Return", 26 => "SubCon Material Return", 27 => "SubCon Garment Delivery", 28 => "Sample Delivery Challan", 29 => "Woven Finish Fabric Receive Return", 30 => "Fabric Service Receive Return", 31 => "Woven Finish Fabric Issue Return", 32 => "Woven Finish Fabric Issue", 33 => "SubCon Embellishment Delivery", 34 => "Finish Fabric Transfer Entry", 35 => "Scrap Out Entry", 36 => "Raw Material Receive", 37 => "Raw Material Receive Return", 38 => "Raw Material Issue", 39 => "Raw Material Issue Return", 40 => "Cotton Issue", 41 => "Cotton Receive Return", 42 => "Cotton Item Transfer", 43 => "Synthetic Fibre Issue", 44 => "Synthetic Fibre Receive Return", 45 => "Synthetic Fibre Transfer", 46 => "Waste Cotton Issue", 47 => "Waste Cotton Receive Return", 48 => "Waste Cotton Transfer",49=>"Printing Delivery",50=>"Trims Delivery Challan",51=>"Wash Delivery",52=>"AOP Delivery Challan");
asort($get_pass_basis);

//-------------------------- End Inventory -------------------------------------------------

//------------------------- Start Sub. Bill ------------------ 09_03_2013 ------------------
$rate_type = array(1 => "External", 2 => "Internal");
$is_deleted = array(0 => "No", 1 => "Yes");
$production_process = array(1 => "Cutting", 2 => "Knitting", 3 => "Dyeing", 4 => "Finishing", 5 => "Sewing", 6 => "Fabric Printing", 7 => "Washing", 8 => "Gmts Printing", 9 => "Embroidery", 10 => "Iron", 11 => "Gmts Finishing", 12 => "Gmts Dyeing", 13 => "Poly", 14 => "Re Conning", 15 => "Common", 16=> "Knit Finish Fabric",17=> "Dyeing process",18=> "Trims");

$bill_for = array(1 => "Order", 2 => "Sample with order", 3 => "Sample without order");

$instrument_payment = array(1 => "Cash", 2 => "Cheque", 3 => "Pay Order", 4 => "LC", 5 => "Non-Cash");

$adjustment_type = array(1 => "Discount", 2 => "Bad Debts", 3 => "Write Off", 5 => "Others", 6 => "Advance Adjustment");
$payment_type = array(1 => "Due & Advance Rec.", 2 => "Advance", 3 => "Due Adjustment");
$bill_rate = array(1 => "Rate Manually", 2 => "Rate from Order", 3 => "Rate from Library");
//------------------------- End Sub. Bill --------------------------------------------------
$buyer_quotation_status = array(1 => "Submitted", 2 => "Confirm", 3 => "Cancle", 4 => "Inactive");
$clearance_method = array(1 => "First Come First Adjust", 2 => "Manual Adjustment");

//=================Planning

//$complexity_level=array(1=>"Basic",2=>"Simply Complex", 3=>"Highly Complex");
$complexity_level = array(0 => "", 1 => "Basic", 2 => "Fancy", 3 => "Critical", 4 => "Average");

$complexity_level_data[0]['fdout'] = 0;
$complexity_level_data[0]['increment'] = 0;
$complexity_level_data[0]['target'] = 0; ///complexity_levels
$complexity_level_data[1]['fdout'] = 1000;
$complexity_level_data[1]['increment'] = 100;
$complexity_level_data[1]['target'] = 1200;
$complexity_level_data[2]['fdout'] = 800;
$complexity_level_data[2]['increment'] = 100;
$complexity_level_data[2]['target'] = 1200;
$complexity_level_data[3]['fdout'] = 600;
$complexity_level_data[3]['increment'] = 100;
$complexity_level_data[3]['target'] = 1200; ///complexity_levels
$complexity_level_data[4]['fdout'] = 880;
$complexity_level_data[4]['increment'] = 100;
$complexity_level_data[4]['target'] = 1100; ///complexity_levels

$complexity_type_tmp = array(1 => "Learning effect by fixed Quantity", 2 => "Learning effect by Efficiency Percentage");
asort($complexity_type_tmp);
//report Country Summary

$report_format = array(1 => "Print GP", 2 => "Print B1", 3 => "Print B2", 4 => "Print Cut1", 5 => "Print Cut2", 6 => "Print B3", 7 => "Print B3", 8 => "Print Booking", 9 => "Print Booking 2", 10 => "Fabric Booking", 11 => "Fabric Booking", 12 => "Print Booking 1", 13 => "Print Booking", 14 => "Print Booking 1", 15 => "Print Booking 2", 16 => "Print Booking 3", 17 => "Print Booking", 18 => "Print Booking 1", 19 => "Print Booking 2", 20 => "Print Booking", 21 => "Print Booking", 22 => "Print Booking", 23 => "Summary", 24 => "Budget", 25 => "Budget Report2", 26 => "Quote Vs Budget", 27 => "Budget On Shipout", 28 => "Print B13", 29 => "C.Date Budget On Shipout", 30 => "Projection Template MM", 31 => "Plan Vs Ex-F", 32 => "Ex-F Vs Plan", 33 => "Both Plan and Ex-Factory", 34 => "Print 1", 35 => "Print 2", 36 => "Print 3", 37 => "Print 4", 38 => "Print Booking1", 39 => "Print Booking2", 40 => "Party Wise", 41 => "Job Wise", 42 => "Challan Wise", 43 => "Returnable", 44 => "Reset", 45 => "Print B4", 46 => "Short Fabric Booking Urmi", 47 => "With Source", 48 => "Without Source", 49 => "Fabrics", 50 => "Pre Cost Rpt", 51 => "Pre Cost Rpt2", 52 => "BOM Rpt", 53 => "Print B5", 54 => "Generate Buyer Wise", 55 => "Generate Task Wise", 56 => "Generate Report", 57 => "Overdue Task", 58 => "Penalty", 59 => "Print Booking BPKW", 60 => "Print Booking", 61 => "Print Booking1", 62 => "Print Booking2", 63 => "BOM Rpt2", 64 => "Print 5", 65 => "Metro", 66 => "Print 2", 67 => "Print Booking", 68 => "Print Barcode", 69 => "Fabric Details", 70 => "GIN3-MC", 71 => "GIN4", 72 => "Print 6", 73 => "Print B6", 74 => "Print Order With Rate", 75 => "Print Order Without Rate", 76 => "Print With Multiple Job", 77 => "Multiple Job Without Rate", 78 => "Print", 79 => "Print With Rate", 80 => "Print Without Rate", 81 => "Multiple Sample With Rate", 82 => "Multiple Sample Without Rate", 83 => "Print Work Order Report", 84 => "Print B7", 85 => "Print B8", 86 => "Print", 87 => "Print Actual", 88 => "Print3", 89 => "Print4", 90 => "Quot. Rpt", 91 => "Quot. Rpt2", 92 => "Quot. Rpt3", 93 => "Print B9", 94 => "Print", 95 => "Print With VAT", 96 => "Summary", 97 => "Party Wise", 98 => "Job Wise", 99 => "Challan Wise", 100 => "Returnable", 101 => "Returnable Without Challan", 102 => "Count Wise Summ", 103 => "Type Wise Summ", 104 => "Composition Wise Summ", 105 => "Stock Only", 106 => "Count & Type Wise - 2", 107 => "Report", 108 => "Show", 109 => "Print", 110 => "Print2", 111 => "Print3", 112 => "Print With VAT", 113 => "Requisition Details", 114 => "Without Program", 115 => "Print", 116 => "Print 2", 117 => "Print 2 With Rate", 118 => "With Group", 119 => "Without Group", 120 => "Print Report", 121 => "Print Report 2", 122 => "Print Report 3", 123 => "Print Report 4", 124 => "Order Wise", 125 => "Color Wise", 126 => "Country Wise", 127 => "Color and Size", 128 => "Order and Size", 129 => "Print B10", 130 => "Requisition Print", 131 => "Requisition Print2", 132 => "Requisition Print3", 133 => "Knitting Card", 134 => "Print", 135 => "Print 2", 136 => "Print 3", 137 => "Print 4", 138 => "Machine Wise", 139 => "Fabric Label", 140 => "Country Ship", 141 => "Summary Country Ship", 142 => "Pre Cost Rpt Bpkw", 143 => "Print 1", 144 => "Accessories Followup Report", 145 => "Accessories Followup Report V2", 146 => "Accessories Followup Report [Budget-2]", 147 => "Show", 148 => "Location Wise", 149 => "Summary", 150 => "Summary 2", 151 => "AAL Print", 152 => "MRR Wise Stock", 153 => "Budget Wise Fabric Booking", 154 => "Main Fabric Booking V2", 155 => "Fabric Booking", 156 => "Acce. Details", 157 => "Acce. Details 2", 158 => "Pre Cost Woven", 159 => "Bom Woven", 160 => "Print 4", 161 => "Print 6", 162 => "Booking Wise", 163 => "Print Booking 1", 164 => "Print Booking 2", 165 => "Size Wise Print", 166 => "Size Wise Print2", 167 => "Color & Size Wise Print", 168 => "Color & Size Wise Print2", 169 => "Print Report 6", 170 => "Cost Rpt3", 171 => "Cost Rpt4", 172 => "Print Outbound", 173 => "Cost Rpt5", 174 => "Print 7", 175 => "Print Booking 5", 176 => "Print Booking 6", 177 => "Print Booking 4", 178 => "Show", 179 => "Show With Html ", 180 => "Group by Style", 181 => "GIN5", 182 => "Budget Report 3", 183 => "Print Booking 2", 184 => "Composition wise lot", 185 => "Batch Card 2", 186 => "Batch Card 3", 187 => "Batch Card 4", 188 => "Adding/Topping", 189 => "Without Rate 2", 190 => "Adding/Topping Without Rate", 191 => "Print 7", 192 => "BOM Dtls", 193 => "Print B11", 194 => "Quot. Woven", 195 => "Show 2", 196 => "Cutting Delivery", 197 => "BOM 3", 198 => "TNA With Commitment", 199 => "Fabric Delivery", 200 => "Finish Fabric", 201 => "Gmt Prod Sew", 202 => "Gmt Prod Fin", 203 => "Fabric Prod", 204 => "Short Form", 205 => "Gmt Prod Sew2", 206 => "General", 207 => "Gmts Delivery2", 208 => "Sample Delivery", 209 => "Print booking 3", 210 => "Without Rate Urmi", 211 => "MO Sheet", 212 => "Yarn Delivery", 213 => "Quot. Woven2", 214 => "Budget3 Summary", 215 => "Budget3 Details", 216 => "Quot. Summary", 217 => "LC Cost Details", 218 => "Northan", 219 => "Quot. Summary", 220 => "Print 8(NFL)", 221 => "Fabric Pre-Cost", 222 => "Show", 223 => "Style Wise", 224 => "Batch Card 5", 225 => "Batch Card 6", 226 => "Batch Card 7", 227 => "Print 8", 228 => "Without Rate 3", 229 => "Weight Sheet", 230 => "Print 7", 231 => "Knitting Card 2", 232 => "Knitting Card 3", 233 => "Print letter", 234 => "Print letter 2", 235 => "Print 9", 235 => "Print Booking 5", 236 => "Print With Collar Cuff", 237 => "Bill Of Exchange",238 =>"BoM Summary",239 =>"Quot. Summary2", 240 => "Print letter 3", 241 => "Print 11", 242 => "Show3" , 243 => "Item Wise", 244 => "Fabric For NTG", 245 => "Prod. Wise", 246 => "Today Prod", 247 => "Machine Wise 2", 248 => "Garments", 249 => "Show With TNA", 250 => "Detail", 251 => "Monthly", 252 => "Country Wise 2", 253 => "Daily", 254 => "Show-Country", 255 => "Show-2", 256 => "Report2", 257 => "With Html", 258 => "FB issue Days", 259 => "Show2", 260 => "PO Wise", 261 => "Details", 262 => "WIP", 263 => "Report 3", 264 => "Report 4", 265 => "Country",266 => "Report1", 267 => "Report3", 268 => "Budget4", 269 => "Print B12", 270 => "Cost Rpt6", 271 => "Finish Fabric Delivery",272=>"Program info format1",273=>"Program info format2",274=>"Print 10",275=>"Quot. Rpt5",276=>"Location Wise Summary",277=>"Summary3[Mkt]",278=>"Recap1",279=>"Trims Recap",280=>"Print B14",281=>"Short",282=>"Details-2",283=>"Details-3",284=>"Details-4",285=>"Spot Cost Vs Budget", 286 => "Prod. Wise2", 287 => "Knitting Card 5");
asort($report_format);

$report_name = array(1 => "Main Fabric Booking", 2 => "Short Fabric Booking", 3 => "Sample Fabric Booking -With order", 4 => "Sample Fabric Booking -Without order", 5 => "Multiple Order Wise Trims Booking", 6 => "Country and Order Wise Trims Booking", 7 => "Yarn Dyeing Work Order", 8 => "Yarn Dyeing Work Order Without Order", 9 => "Embellishment Work Order", 10 => "Service Booking For AOP", 11 => "Fabric Service Booking", 12 => "Service Booking For Knitting", 13 => "Yarn Dyeing Work Order", 14 => "Yarn Service Work Order", 15 => "Short Trims Booking", 16 => "Sample Trims Booking With Order", 17 => "Sample Trims Booking Without Order", 18 => "Order Wise Budget Report", 19 => "Export To Excel Report", 20 => "Party Wise Grey Fabric Reconciliation", 21 => "Embellishment Issue Entry", 22 => "Pre-Costing", 23 => "TNA Progress Report", 24 => "Fabric TNA Progress Report", 25 => "Multiple Job Wise Trims Booking", 26 => "Multiple Job Wise Trims Booking V2", 27 => "Grey Fabric Roll Issue", 28 => "Yarn Dyeing Work Order With Out Lot", 29 => "Yarn Dyeing Work Order With Out Order 2", 30 => "Others Purchase Order", 31 => "Embellishment Work Order V2", 32 => "Price Quotation", 33 => "Knit Grey Fabric Issue", 34 => "Party Wise Yarn Reconciliation", 35 => "Partial Fabric Booking", 36 => "Daily Yarn Stock", 37 => "Yarn Issue", 38 => "Gate Pass Entry", 39 => "Purchase Requisition", 40 => "Order Wise RMG Production Status", 41 => "Knitting Plan Report", 42 => "Roll Wise Grey Fabric Delivery to Store", 43 => "Pre-Costing V2", 44 => "Monthly Buyer Wise Order Summary", 45 => "Yarn Purchase Order", 46 => "Capacity and Order Booking Status", 47 => "Fabric Receive Status Report", 48 => "Sample Requisition Fabric Booking -With order", 49 => "Service Booking For AOP V2", 50 => "Bundle Issued to Print", 51 => "Bundle Receive From Print", 52 => "Bundle Issued to Embroidery", 53 => "Bundle Receive From Embroidery", 54 => "Accessories Followup Report V2", 55 => "Fabric Requisition For Batch 2", 56 => "Batch Creation", 57 => "Multiple Job Wise Short Trims Booking V2", 58 => "Dyes And Chemical Issue Requisition", 59 => "Daily Production Progress Report", 60 => "Factory Monthly Production Report", 61 => "Stationary Purchase Order", 62 => "Cost Break Up Report V2", 63 => "Style Wise Production Report", 64 => "Order Wise Budget Sweater Report", 65 => "Multi Job Wise Service Booking Knitting", 66 => "Fabric Issue to Finish Process", 67 => "Fabric Sales Order Entry", 68 => "Doc. Submission to Bank", 69 => "Yarn Purchase Requisition", 70 => "Monthly Capacity Vs Buyer Wise Booked",71=>"Daily Knitting Production Report",72 => "Work progress report",73 => "Order Follow-up Report",74 => "Daily Ex-Factory Report",75 => "Accessories Followup Report [Budget-2]",76 => "Weekly Capacity and Booking Status",77 => "Fabric Production Status Report",78 => "Sewing Plan Vs Production",79 => "Cutting Status Report",80 => "Daily RMG Production status Report V2",81 => "Date Wise Production Report",82 => "Factory Monthly Production Report for Urmi",83 =>"Quick Costing",84 => "Closing Stock Report General",85 => "Buyer Inquiry Status Report",86 => "Garments Delivery Entry",87=>"Order Wise Production Report", 88 =>"Planning Info Entry For Sales Order", 89 =>"Multiple Job Wise Embellishment Work Order",90=>"Sample Requisition Fabric Booking -Without order",91=>"Wash Dyes Chemical Issue",92=>"Woven Short fabric Booking",93=>" Purchase Recap",94=>"Order and Color Wise Finish Fabric Stock Report",95=>"Dyeing Production Report-V3",96=>"Export CI Statement",97=>"Woven Order Wise Budget",98=>"Daily Yarn Demand Entry",99=>"Wash Dyes Chemical Issue",100=>"Batch Creation For Gmts. Wash",101=>"Wash Recipe Entry",102=>"Wash Dyes And Chemical Issue Requisition",103=>"Wet Production",104=>"Dry Production",105=>"Wash Delivery",106=>"Wash Bill Issue",107=>"Wash Delivery Return",108=>"Statement of Total Export Value and CM");
asort($report_name);

$home_page_module = array(1 => 'Graph', 2 => 'Pending', 3 => 'Inventory', 4 => 'Merchant', 5 => 'Merchants');
asort($home_page_module);
$use_for = array(1 => 'Development', 2 => 'Repair & Maintenance', 3 => 'Operation');

/*$home_page_array[1][1]['name']="Value Wise";
$home_page_array[1][1]['lnk']="value_chart";
$home_page_array[1][2]['name']="Quantity Wise";
$home_page_array[1][2]['lnk']="qnty_chart";
$home_page_array[1][3]['name']="Stack Value Chart";
$home_page_array[1][3]['lnk']="stack_value";
$home_page_array[1][4]['name']="Stack Quantity Chart";
$home_page_array[1][4]['lnk']="stack_qnty";
$home_page_array[1][5]['name']="Today Hourly Prod.";
$home_page_array[1][5]['lnk']="hrly_trend";
$home_page_array[1][6]['name']="Trend Monthly";
$home_page_array[1][6]['lnk']="monthly_trend";
$home_page_array[1][7]['name']="Trend Daily";
$home_page_array[1][7]['lnk']="daily_trend";
$home_page_array[1][8]['name']="Order Summary (Qty)";
$home_page_array[1][8]['lnk']="order_summ_qnty";
$home_page_array[1][9]['name']="Order Summary (Value)";
$home_page_array[1][9]['lnk']="order_summ_val";
$home_page_array[1][10]['name']="Dash Board";
$home_page_array[1][10]['lnk']="dash_board";*/

$home_page_array[1][1]['name'] = "Order In Hand Qnty";
$home_page_array[1][1]['lnk'] = "order_in_hand_qnty";
$home_page_array[1][2]['name'] = "Order In Hand Value";
$home_page_array[1][2]['lnk'] = "order_in_hand_val";
$home_page_array[1][3]['name'] = "Company Key Performance";
$home_page_array[1][3]['lnk'] = "company_kpi";
$home_page_array[1][4]['name'] = "Today Hourly Production";
$home_page_array[1][4]['lnk'] = "Today_Hourly_Production";
$home_page_array[1][5]['name'] = "Stack Bar Qty";
$home_page_array[1][5]['lnk'] = "stack_qnty";
$home_page_array[1][6]['name'] = "Stack Bar Value";
$home_page_array[1][6]['lnk'] = "stack_value";
$home_page_array[1][7]['name'] = "Sales Forecast Qnty";
$home_page_array[1][7]['lnk'] = "sales_forecast_qnty";
$home_page_array[1][8]['name'] = "Sales Forecast Value";
$home_page_array[1][8]['lnk'] = "sales_forecast_value";
$home_page_array[1][9]['name'] = "Last 30 days Knit Prod Trend";
$home_page_array[1][9]['lnk'] = "30_days_knit_eff_trend";
$home_page_array[1][10]['name'] = "Last 30 Days Dyeing Prod Trend";
$home_page_array[1][10]['lnk'] = "30_days_dyen_eff_trend";
$home_page_array[1][11]['name'] = "Last 30 days Sew Effi Trend";
$home_page_array[1][11]['lnk'] = "30_days_sewn_eff_trend";
$home_page_array[1][12]['name'] = "30 Days Idle Lines";
$home_page_array[1][12]['lnk'] = "30_days_idle_lines";
$home_page_array[1][13]['name'] = "30 Days Idle RMG Worker";
$home_page_array[1][13]['lnk'] = "30_days_idle_rmg_worker";
$home_page_array[1][14]['name'] = "30 days Idle Knit Machines";
$home_page_array[1][14]['lnk'] = "30_days_idle_knit_mchn";
$home_page_array[1][15]['name'] = "30 days Idle Dyeing Machines";
$home_page_array[1][15]['lnk'] = "30_days_idle_dyen_mchn";
$home_page_array[1][16]['name'] = "Capacity Status In Minute";
$home_page_array[1][16]['lnk'] = "capacity_status_smv";
$home_page_array[1][17]['name'] = "Capacity and Order Booked";
$home_page_array[1][17]['lnk'] = "capacity_booked_qty";
$home_page_array[1][18]['name'] = "Gmts Reject Alter Percentage";
$home_page_array[1][18]['lnk'] = "gmts_reject_alter_per";
$home_page_array[1][19]['name'] = "Fabric & Order Analysis";
$home_page_array[1][19]['lnk'] = "fabric_order_analysis";
$home_page_array[1][20]['name'] = "Finishing Capacity & Achievment(Iron)";
$home_page_array[1][20]['lnk'] = "finishing_capacity_achievment_iron";
$home_page_array[1][21]['name'] = "Capacity Comparison In Hour";
$home_page_array[1][21]['lnk'] = "capacity_comparison_in_hour";
$home_page_array[1][22]['name'] = "Daily Finishing Capacity & Achievment (Iron)";
$home_page_array[1][22]['lnk'] = "daily_finishing_capacity_achievment_iron";
$home_page_array[1][23]['name'] = "Dyeing Capacity VS Load";
$home_page_array[1][23]['lnk'] = "dyeing_capacity_vs_load";
$home_page_array[1][24]['name'] = "Knitting Capacity VS Load";
$home_page_array[1][24]['lnk'] = "knitting_capacity_vs_load";
$home_page_array[1][25]['name'] = "Count Wise Yarn Stock";
$home_page_array[1][25]['lnk'] = "yarn_stock_graph";
$home_page_array[1][26]['name'] = "Yarn Consump- tion";
$home_page_array[1][26]['lnk'] = "yarn_consumption_grap";
$home_page_array[1][27]['name'] = "Today Hourly Production 2";
$home_page_array[1][27]['lnk'] = "today_production_graph_working_company";
$home_page_array[1][28]['name'] = "Total Activities";
$home_page_array[1][28]['lnk'] = "total_activities_auto_mail";
$home_page_array[1][29]['name'] = "Order In Hand Qnty PSD";
$home_page_array[1][29]['lnk'] = "order_in_hand_qnty_PSD";
$home_page_array[1][30]['name'] = "Textile Graph";
$home_page_array[1][30]['lnk'] = "textile_graph";
$home_page_array[1][31]['name'] = "Garments Graph";
$home_page_array[1][31]['lnk'] = "garments_graph";
$home_page_array[1][32]['name'] = "KPI Woven";
$home_page_array[1][32]['lnk'] = "company_kpi_woven";
$home_page_array[1][33]['name'] = "TNA Plan Vs Actual Finish";
$home_page_array[1][33]['lnk'] = "tna_report_controller";
$home_page_array[1][34]['name'] = "Hourly Production Monitoring Reports";
$home_page_array[1][34]['lnk'] = "hourly_production_monitoring_reports";
$home_page_array[1][35]['name'] = "Order in Hand Qty Team Leader Wise";
$home_page_array[1][35]['lnk'] = "order_in_hand_qty_team_leader_wise";
$home_page_array[1][36]['name'] = "Order in Hand Val Team Leader Wise";
$home_page_array[1][36]['lnk'] = "order_in_hand_val_team_leader_wise";
$home_page_array[1][37]['name'] = "Order in Hand Qty Week Wise";
$home_page_array[1][37]['lnk'] = "order_in_hand_qty_week_wise";
$home_page_array[1][38]['name'] = "Order in Hand Val Week Wise";
$home_page_array[1][38]['lnk'] = "order_in_hand_val_week_wise";
$home_page_array[1][39]['name'] = "Monthly Order Qty Vs Sewing Balance Qty";
$home_page_array[1][39]['lnk'] = "monthly_order_qty_vs_sewing_balance_qty";
$home_page_array[1][40]['name'] = "Trims Order Receive & Sales Value($)";
$home_page_array[1][40]['lnk'] = "trims_order_receive_sales_value";

$home_page_array[2][1]['name'] = "Shipment Pending Report";
$home_page_array[2][1]['lnk'] = "shipment_pending_report";
$home_page_array[2][2]['name'] = "Country Wise Shipment Pending Report";
$home_page_array[2][2]['lnk'] = "country_wise_shipment_pending_report";
$home_page_array[2][3]['name'] = "Quotation Submission Pending";
$home_page_array[2][3]['lnk'] = "quotation_submission_pending";
$home_page_array[2][4]['name'] = "Quotation Finalization Pending";
$home_page_array[2][4]['lnk'] = "buyer_inquiry_status_report";
$home_page_array[2][5]['name'] = "Yarn Issue Pending";
$home_page_array[2][5]['lnk'] = "yarn_issue_pending";
$home_page_array[2][6]['name'] = "PP Sample Approval Pending";
$home_page_array[2][6]['lnk'] = "PP_sample_approval_pending";
$home_page_array[2][7]['name'] = "Ex-Factory vs Commercial Activities";
$home_page_array[2][7]['lnk'] = "Ex_Factory_vs_commercial_activities";

$home_page_array[2][8]['name'] = "TNA Plan Vs Actual Finish";
$home_page_array[2][8]['lnk'] = "tna_report_controller";

$home_page_array[2][9]['name'] = "Data";
$home_page_array[2][9]['lnk'] = "data";
$home_page_array[2][10]['name'] = "Data";
$home_page_array[2][10]['lnk'] = "data";

$home_page_array[3][1]['name'] = "Data";
$home_page_array[3][1]['lnk'] = "Data";
$home_page_array[3][2]['name'] = "Data";
$home_page_array[3][2]['lnk'] = "Data";
$home_page_array[3][3]['name'] = "Data";
$home_page_array[3][3]['lnk'] = "Data";
$home_page_array[3][4]['name'] = "Data";
$home_page_array[3][4]['lnk'] = "Data";
$home_page_array[4][1]['name'] = "Data";
$home_page_array[4][1]['lnk'] = "Data";
$home_page_array[4][2]['name'] = "Data";
$home_page_array[4][2]['lnk'] = "Data";
$home_page_array[4][3]['name'] = "Data";
$home_page_array[4][3]['lnk'] = "Data";
$home_page_array[4][4]['name'] = "Data";
$home_page_array[4][4]['lnk'] = "Data";
$home_page_array[4][1]['name'] = "Data";
$home_page_array[4][1]['lnk'] = "Data";
$home_page_array[5][2]['name'] = "Data";
$home_page_array[5][2]['lnk'] = "Data";
$home_page_array[5][3]['name'] = "Data";
$home_page_array[5][3]['lnk'] = "Data";
$home_page_array[5][4]['name'] = "Data";
$home_page_array[5][4]['lnk'] = "Data";

//22.09.2015
$info_type = array(1 => "Recv Date Wise", 2 => "Order Wise", 3 => "Transaction Date Wise");
$report_date_catagory = array(1 => "Country Ship Date Wise", 2 => "Ship Date Wise", 3 => "Org. Ship Date Wise", 4 => "PO Insert Date Wise", 5=> "Extended Ship Date");
//14.1.18
$report_date_consolidated = array(1 => "Country Ship Date", 2 => "Public Ship Date");
//20.11.17
$fabric_issue_basis = array(1 => "Batch Wise", 2 => "WO Wise");
//06.10.2015
$letter_type_arr = array(1 => "Shipping Guarantee", 2 => "Delivery of Consignment", 3 => "Sales Contact Lien", 4 => "Export LC Lien", 5 => "Export LC Amendment", 6 => "Export LC Replace", 7 => "Forwording Letter", 8 => "Acceptance Letter", 9 => "BTB LC Open");
asort($letter_type_arr);
//18.11.2015 For sample Booking None order
$body_type_arr = array(1 => 'Plain Collar', 2 => 'Bit Collar', 3 => 'Kushikata', 4 => 'Plain Cuff', 5 => 'Bit Cuff');

$garments_item_prefix = array(
	1 => "TSLS",
	2 => "TSSS",
	3 => "PSLS",
	4 => "PSSS",
	5 => "TT",
	6 => "TSA",
	7 => "HOD",
	8 => "HNL",
	9 => "TSSL",
	10 => "RAG",
	11 => "TSHN",
	12 => "SCRF",
	14 => "BLZ",
	15 => "JKT",
	16 => "NW",
	17 => "MD",
	18 => "LLD",
	19 => "GD",
	20 => "PNT",
	21 => "SP",
	22 => "TRSR",
	23 => "PJM",
	24 => "RMPSS",
	25 => "RMPLS",
	26 => "RMPSL",
	27 => "RMP",
	28 => "LGG",
	29 => "3QTR",
	30 => "SKRT",
	31 => "JMPS",
	32 => "CAP",
	33 => "TTPJM",
	34 => "TSPJM",
	35 => "JP",
	36 => "BAG",
	37 => "BRA",
	38 => "UWB",
	39 => "SS",
	40 => "SNGT",
	41 => "TSNGT",
	42 => "BXR",
	43 => "STBXR",
	44 => "TBXR",
	45 => "JBXR",
	46 => "PTY",
	47 => "SB",
	48 => "CB",
	49 => "SRTB",
	50 => "MNB",
	51 => "BKN",
	52 => "LNGR",
	53 => "BKR",
	54 => "UW",
	60 => "PSKS",
	61 => "RSKS",
	62 => "JSKS",
	63 => "HGSKS",
	64 => "SSKS",
	65 => "TSKS",
	66 => "TGTSKS",
	67 => "SWTP",
	68 => "SW",
	69 => "JT",
	70 => "LP",
	71 => "PP",
	72 => "BLR",
	73 => "ST",
	74 => "LG",
	75 => "LSB",
	76 => "TTB",
	77 => "UWT",
	78 => "WPR",
	79 => "SBG",
	80 => "RMPLS",
	81 => "RMPLS",
	82 => "RMPLS",
	83 => "BABY",
	84 => "TQPT",
	85 => "LSPT",
	86 => "TNP",
	87 => "MXD",
	88 => "LG",
	89 => "GTNT",
	90 => "VNT",
	91 => "RTJ",
	92 => "STJ",
	93 => "PJ",
	94 => "LBJ",
	95 => "JGNG",
	96 => "MNW",
	97 => "SPS",
	98 => "RMPR",
);
asort($garments_item_prefix);
$body_part_prefix = array
	(1 => "MFT",
	2 => "CLR",
	3 => "CF",
	4 => "RIB",
	5 => "FLP",
	6 => "HD",
	7 => "PKT",
	8 => "RIB",
	9 => "SLV",
	10 => "BP",
	11 => "FP",
	12 => "FF",
	13 => "BND",
	4 => "MFT",
	15 => "MFT",
	16 => "MFT",
	17 => "MFT",
	18 => "SLDR",
	19 => "HDL",
	20 => "MFB",
	21 => "PKT",
	22 => "RIB",
	23 => "WB",
	24 => "WB",
	25 => "BL",
	26 => "BY",
	27 => "TAB",
	28 => "ASBL",
	29 => "FLY",
	30 => "PKT",
	31 => "BF",
	32 => "LOOP",
	33 => "SF",
	34 => "PKT",
	35 => "PKT",
	36 => "FT",
	37 => "WB",
	38 => "TAPE",
	39 => "PKT",
	40 => "PLKT",
	41 => "NT",
	42 => "PPG",
	43 => "PB",
	44 => "NB",
	45 => "NI",
	46 => "DST",
	47 => "CI",
	48 => "MI",
	49 => "TM",
	50 => "WI",
	51 => "DL",
	52 => "SS",
	53 => "FY",
	54 => "",
	55 => "NR",
	56 => "IN",
	57 => "SL",
	58 => "HM",
	59 => "NK",
	60 => "BM",
	61 => "BT",
	62 => "PKT",
	63 => "",
	64 => "PKT",
	65 => "PKT",
	66 => "PKT",
	67 => "PKT",
	68 => "CT",
	69 => "IFB",
	70 => "FTP",
	71 => "ST",
	72 => "TF",
	73 => "PKT",
	74 => "WR",
	75 => "NBD",
	76 => "BOW",
	77 => "",
	78 => "NTL",
	79 => "ZUB",
	80 => "",
	81 => "BTP",
	82 => "FS",
	83 => "CP",
	84 => "",
	85 => "",
	86 => "",
	87 => "",
	88 => "",
	89 => "",
	90 => "",
	91 => "",
	92 => "",
	93 => "SLV",
	94 => "SLV",
	95 => "",
	96 => "",
	97 => "CS",
	98 => "HEM",
	99 => "BF",
	100 => "BP",
	101 => "",
	102 => "HEM",
	103 => "",
	104 => "PLKT",
	105 => "PKT",
	106 => "",
	107 => "FLAP",
	108 => "HEM",
	109 => "FP",
	110 => "",
	111 => "",
	112 => "HEM",
	113 => "LOOP",
	114 => "",
	115 => "PPG",
	116 => "RIB",
	117 => "",
	118 => "HEM",
	119 => "PKT",
	120 => "BRA",
	121 => "",
	122 => "",
	123 => "",
	124 => "",
	125 => "",
	126 => "BNP",
	127 => "",
	128 => "WB",
	129 => "",
	130 => "",
	131 => "",
	132 => "",
	133 => "",
	134 => "",
	135 => "",
	136 => "PLKT",
	137 => "KHB",
	138 => "",
	139 => "",
	140 => "SLV",
	141 => "STP",
	142 => "",
	143 => "",
	144 => "HTP",
	145 => "PKT",
	146 => "PKT",
	147 => "",
	148 => "",
	149 => "",
	150 => "",
	151 => "",
	152 => "",
	153 => "SLV",
	154 => "",
	155 => "",
	156 => "",
	157 => "",
	158 => "",
	159 => "",
	160 => "PPG",
	161 => "",
	162 => "YS",
	163 => "",
	164 => "MFG",
	165 => "");
asort($body_part_prefix);

$home_graph_arr___ = array(
	1 => 'dash_board.php',
	2 => 'today_production_graph.php',
	3 => 'trend_monthly_graph.php',
	4 => 'trend_daily_graph.php',
	5 => 'graph_grp.php',
	6 => 'dash_board_2.php',
	7 => 'graph_public_ship_date.php',
	8 => 'graph_lc_style_company.php',
	9 => 'graph_index.php',
);


$home_graph_arr = array(
	1 => 'monthly_order_export.php',
	2 => 'graph_lc_style_company.php',
	3 => 'graph_public_ship_date.php',
	4 => 'graph_grp.php',
	5 => 'monthly_order_value.php',
);
asort($home_graph_arr);

$cost_components = array(1 => 'Fabric Cost', 2 => 'Trims Cost', 3 => 'Embell.Cost', 4 => 'Gmts.Wash', 5 => 'Commission', 6 => 'Commercial Cost', 7 => 'Lab Test', 8 => 'Inspection Cost', 9 => 'Gmts Freight Cost', 10 => 'Currier Cost', 11 => 'Certificate Cost', 12 => 'Others Cost');

$sample_stage = array(1 => "After Order Place", 2 => "Before Order Place", 3 => "R&D");
//23=>'Dyes Chemicals & Auxilary Chemicals',
/*$general_item_category = array(
	4 => "Accessories",
	8 => "Spare Parts",
	9 => "Machinaries",
	10 => "Other Capital Items",
	11 => "Stationaries",
	15 => 'Electrical',
	16 => 'Maintenance',
	17 => 'Medical',
	18 => 'ICT Equipment',
	19 => 'Print & Publication',
	20 => 'Utilities & Lubricants',
	21 => 'Construction Materials',
	22 => 'Printing Chemicals & Dyes',
	32 => 'Vehicle Components',
	33 => 'Others',
	34 => 'Painting Goods',
	35 => 'Plumbing and Sanitary Goods',
	36 => 'Safety and Security',
	37 => 'Food and Grocery',
	38 => 'Needles',
	39 => 'WTP and ETP Machinery',
	40 => 'Spare Parts - Mechanical',
	41 => 'Spare Parts - Electrical',
	44 => 'Packing Materials',
	45 => 'Factory Machinery',
	46 => 'Iron Dril Machinery Machinery',
	47 => 'Felt Machinery',
	48 => 'Dosing Motor Pump',
	49 => 'Centrifugal Water Pump',
	50 => 'Flack Machinery',
	51 => 'Bag Sewing Machine',
	52 => 'Batter Cabinet',
	53 => 'TV',
	54 => 'Finishing Machinery',
	55 => 'Compresser Machinery',
	56 => 'Sewing Machinery',
	57 => 'Embroidery Machinery',
	58 => 'Washing Machinery',
	59 => 'Cutting Machinery',
	60 => 'Knitting Machinery',
	61 => 'Printing Machinery',
	62 => 'Laboratory Machinery',
	63 => 'PMD Machinery',
	64 => 'Dyeing Machinery',
	65 => 'Oil and Gas Generator',
	66 => 'Fabric Spreader Machinery',
	67 => 'Consumable',
	68 => 'ICT Consumable',
	69 => 'Furniture',
	70 => 'Fixture',
	89 => 'AC Plant',
	90 => 'Chiller',
	91 => 'Substation',
	92 => 'Pump',
	93 => 'Cooling Tower',
	94 => 'Vehicle',
	99 => 'Cleaning Goods');
asort($general_item_category);*/

$general_item_category = return_library_array("select CATEGORY_ID,SHORT_NAME from  lib_item_category_list where status_active=1 and is_deleted=0 and CATEGORY_TYPE=1 order by SHORT_NAME", "CATEGORY_ID", "SHORT_NAME");
//4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94
$category_wise_entry_form = array(
	1 => "165",
	2 => "166",
	3 => "166",
	13 => "166",
	14 => "166",
	4 => "167",
	12 => "168",
	24 => "169",
	25 => "170",
	74 => "170",
	102 => "170",
	103 => "170",
	104 => "170",
	30 => "197",
	31 => "171",
	5 => "227",
	6 => "227",
	7 => "227",
	23 => "227",
	8 => "172",
	9 => "172",
	10 => "172",
	11 => "172",
	15 => '172',
	16 => '172',
	17 => '172',
	18 => '172',
	19 => '172',
	20 => '172',
	21 => '172',
	22 => '172',
	32 => '172',
	33 => '172',
	34 => '172',
	35 => '172',
	36 => '172',
	37 => '172',
	38 => '172',
	39 => '172',
	40 => '172',
	41 => '172',
	44 => '172',
	45 => '172',
	46 => '172',
	47 => '172',
	48 => '172',
	49 => '172',
	50 => '172',
	51 => '172',
	52 => '172',
	53 => '172',
	54 => '172',
	55 => '172',
	56 => '172',
	57 => '172',
	58 => '172',
	59 => '172',
	60 => '172',
	61 => '172',
	62 => '172',
	63 => '172',
	64 => '172',
	65 => '172',
	66 => '172',
	67 => '172',
	68 => '172',
	69 => '172',
	70 => '172',
	89 => '172',
	90 => '172',
	91 => '172',
	92 => '172',
	93 => '172',
	94 => '172',
	101 => '172',
	106 => '172'
);

$body_part_type = array(
	1 => "Top",
	20 => "Bottom",
	30 => "Others",
	40 => "Flat Knit",
	50 => "Cuff",
);

$sample_checklist_set = array(1 => "Design Sketch", 20 => "Basic Construction with design sketch", 40 => "Physical Garment", 60 => "BOM Details", 80 => "Trim card with Trims", 100 => "PU Cup Available", 120 => "Plastichat", 140 => "Wire Available", 160 => "Lace Image", 180 => "Lace Swatch", 200 => "Lace width", 220 => "Size Ratio", 240 => "Size Range", 260 => "Size Split", 280 => "Fabric Specification", 300 => "Shrinkage", 320 => "Fabric Stretch", 340 => "Fabric Lay way", 360 => "Moldability", 380 => "Skewing", 400 => "Bowing", 420 => "Special Decorative Trims", 440 => "Pattern");
$feeding_arr = array(1 => 'Knit', 2 => 'Track', 3 => 'Loop'); //As Per Reza

//$lab_test_agent=array(1=>"Sgs",2=>"Attex",3=>"Bureau Veritas",4=>"ITS");
$lab_test_agent = array(1 => "SGS", 2 => "AITEX", 3 => "Bureau Veritas", 4 => "ITS", 5 => "UL VS Bangladesh Ltd");
$sample_delivery_basis = array(1 => "Requisition", 2 => "Sample for order", 3 => "Sample booking without order");

$template_type_arr = array(1 => 'Garments', 2 => 'Textile', 3 => 'Sweater');
$quotation_status = array(1 => "Available", 2 => "Confirmed", 3 => "Closed");

$time_weight_panel = array(1 => "1st Front", 2 => "2nd Front", 3 => "Back", 4 => "Right Sleeve", 5 => "Left Sleeve", 6 => "Collar / Neck", 7 => "Neck Band", 8 => "Cardigan Band", 9 => "Strap / Zip Facing", 10 => "Hood", 11 => "Pocket", 12 => "Pocket Rib", 13 => "Pocket Bag", 14 => "Linking Yarn", 15 => "Others", 16 => "Button Placket", 17 => "Hood Rib", 18 => "Pocket Flap", 19 => "Neck Tape", 20 => "Zipper Piping", 21 => "Accessories(Teeth)", 22 => "Accessories (Ear)", 23 => "Moon", 24 => "Loop", 25 => "Belt", 26 => "Embroidery Applique",27 => "Armhole Piping", 28 => "Body Placket", 29 => "Pocket Placket", 30 => "Bottom Piping",31 => "Shoulder Potty", 32 => "Label Carrier", 33 => "Badge Carrier", 34 => "Cut Out Rib", 35 => "Cap [Body Part]", 36 => "Scarf [Body Part]");

$development_no = array(1 => "1st Development", 2 => "2nd Development", 3 => "3rd Development", 4 => "4th Development", 5 => "5th Development", 6 => "6th Development", 7 => "7th Development", 8 => "8th Development", 9 => "9th Development", 10 => "10th Development");

$short_booking_cause_arr = array(1=>"Merchandising",2=>"Technical",3=>"Yarn",4=>"Knitting",5=>"Dyeing",6=>"Dyeing Finishing",7=>"Textile Quality",8=>"Color Lab",9=>"Sample And RND Textile",10=>"Finish Fabric Store",11=>"AOP",12=>"Dyed Yarn",13=>"Placement Print",14=>"Embroidery",15=>"Garments Wash",16=>"Garments Unit");



 //Wash Module ====================

$wash_type=array(1 => "Wet Process", 2 => "Dry Process", 3 => "Laser Design");
asort($wash_type);
$wash_wet_process=array(1=>"Garments Wash",2=>"Enzyme Wash",3=>"Enzyme stone Wash",4=>"Bleach Wash",5=>"Acid Wash",6=>"Random Wash",7=>"Towel Bleach",8=>"Reactive Dyeing",9=>"Pigment Dyeing",10=>"Pluorescent Dyeing",11=>"Cool Dyeing/ Mould",12=>"Tie Dyeing",13=>"Dis Chargeable Dyeing/ Fashion Dyeing",40=>"Other",41=>"Desizing",42=>"Neutral",43=>"Cleaning",44=>"Tint",45=>"Softener",46=>"Catanizer",47=>"Dyeing",48=>"Soaping",49=>"Fixing",50=>"Binder",51=>"Deep Dye");
asort($wash_wet_process);
$wash_dry_process=array(1=>"Whisker",2=>"Hand Sand",3=>"PP Spray",4=>"Pigment Spray",5=>"Tagging",6=>"Destroy",7=>"3D",8=>"Tieing",9=>"Grinding",10=>"Resing Depping Spray",11=>"Wrinkle",30=>"Others",31=>"Air Blow Out",32=>"PP Rubbing");
asort($wash_dry_process);
$wash_laser_desing=array(1=>"Laser Whisker",2=>"Laser Brush",3=>"Laser Destroy",4=>"Laser Chemo Print",20=>"Others");

//Wash Module ====================
$print_type = array(1 => "Pigment",2 => "Discharge",3 => "Glitter", 4 => "Burnout", 5 => "Reactive");
asort($wash_laser_desing);


//Dyeing Lab
$lab_section=array(1 => "FD", 2 => "YD");
asort($lab_section);

$dyeinglab_dyetype_arr = array(1 => "Acid", 2 => "Basic", 3 => "Discharge", 4 => "Disperse", 5 => "Direct", 6 => "Highfast/Discharge", 7 => "Disperse with OBA", 8 => 'Hsublimn Disperse', 9 => 'SHFast Disperse', 10 => 'Reactive/SH Fast', 11 => 'Basic with OBA', 12 => 'Low Temp Disperse', 13 => 'Basic/Discharge', 14 => 'Basic/Hfast Disperse', 15 => 'Reactive with OBA', 16 => 'Basic /Disperse', 17 => 'Basic/Acid', 18 => 'Reactive', 19 => 'Disperse/Reactive', 20 => 'Acid/Reactive', 21 => 'Basic/Reactive', 22 => 'Disperse/Discharge', 23 => 'Washing', 24 => 'Reactive/High Fast', 25 => 'Acid with OBA', 26 => 'Reactive CPB');
asort($dyeinglab_dyetype_arr);

$dyeinglab_dyecode_arr = array(1 => "A", 2 => "B", 3 => "C", 4 => "D", 5 => "E", 6 => "F", 7 => "G", 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T', 21 => 'U', 22 => 'V', 23 => 'W', 24 => 'X', 25 => 'Y', 26 => 'Z');
asort($dyeinglab_dyecode_arr);

$dyeinglab_shadeBrightness_arr = array(1 => "Light", 2 => "Medium", 3 => "Dark", 4 => "Extra Dark");
asort($dyeinglab_shadeBrightness_arr);
?>
