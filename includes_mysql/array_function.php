<?php
//******************************************************************************************** please DO NOT CHANGE BELOW ARRAYS

$blank_array=array();    // for blank Drop Down or empty drop down
$booking_type = array(1 => 'FB', 2 => 'TB', 3 => 'Service Booking', 4 => 'Sample Booking', 5 => 'Trim Sample', 6 => 'Embellishment Booking', 7 => 'Dia Wise Fabric Booking');
$mod_permission_type=array(0=>"Selective Permission",1=>"Full Permission",2=>"No Permission");
$form_permission_type=array(1=>"Permitted",2=>"Not Permitted");
$row_status=array(1=>"Active",2=>"InActive",3=>"Cancelled");
$knitting_program_status=array(1=>"Waiting",2=>"Running",3=>"Stop",4=>"Closed");
//$knitting_program_status=array(1=>"Running",2=>"Waiting",3=>"Stop");
$project_type_arr=array(1=>"Knit", 2=>"Woven", 3=>"Trims", 4=>"Spinning", 5=>"AOP", 6=>"Sweater", 7=>"Wash", 8=>"Printing", 9=>"Embroidery");
$year_closing_ref_arr=array(1=>"Store Wise", 2=>"Floor Wise", 3=>"Room Wise", 4=>"Rack Wise", 5=>"Shelf Wise", 6=>"Bin/Box Wise", 7=>"Order Wise", 8=>"Sales Order Wise");
$attach_detach_array=array(1=>"Attach",0=>"Detach");
$planning_status=array(1=>"Pending",2=>"Planning Done",3=>"Requisition Done",4=>"Demand Done");
$yes_no=array(1=>"Yes",2=>"No"); //2= Deleted,3= Locked
$is_approved=array(1=>"Yes",2=>"No",3=>"Partial Approved"); //New array as per Monzu vai
$delivery_status = array(1 => "Full Pending", 2 => "Partial Deliverd", 3 => "Full Deliverd");

$fabric_finishing_previous_process = array(1 => "After Brush", 2 => "After Peach", 3 => "Chemical Finish", 4 => "After AOP", 5 => "Before peach", 6 => "Before Brush", 7 => "Before AOP", 8 => "Dry");
$responsibility_dept_arr = array(1 =>"Knitting",2 => "Dyeing",3 =>"Finishing", 4 =>"Others");

$approval_type_arr=array(0=>"Un-Approved",1=>"Approved");
$approval_necessity_array = array(1 => "Price Quotation", 2 => "Component Wise Pre-Costing - Fabric", 3 => "Component Wise Pre-Costing - Trims", 4 => "Component Wise Pre-Costing - Embellishment", 5 => "Fabric Booking", 6 => "Short Fabric Booking", 7 => "Sample Fabric Booking - With Order", 8 => 'Sample Fabric Booking - Without Order', 9 => 'Trims Booking', 10 => 'Short Trims Booking', 11 => 'Sample Trims Booking - With Order', 12 => 'Sample Trims Booking - Without Order', 13 => 'Purchase Requisition', 14 => 'Yarn Purchase Requisition', 15 => 'Yarn Purchase Order', 16 => 'Stationery Purchase Order', 17 => 'Fabric Sales Order', 18 => 'Pro Forma Invoice', 19 => 'Yarn Delivery Challan', 20 => 'Dyeing Batch', 21 => 'Dyes and Chemical Purchase Order', 22 => 'Other General Item Purchase Order', 23 => 'Store Issue Requisition', 24 => 'Yarn Dyeing Work Order', 25 => 'Pre-Costing', 26 => 'Sample Requisition', 27 => 'Service Booking For Knitting', 28 => 'Quick Costing', 29 => 'Commercial Office Note', 30 => 'Sample Requisition Acknowledgement', 31 => 'Work Study', 32 => 'TNA Approval', 33 => 'Lab Test Approval',35 => 'Operation Bulletin',36 => 'Sourcing Post Cost',37=>"Pre-Costing Woven",38=>'Gate Pass Activation Approval',39=>"Import Document Acceptance Approval",40=>"Sample Or Additional Yarn WO",41=>"General Transfer Requisition");
asort($approval_necessity_array);

$months = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
$months_short = array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
$year = array(2010=>'2010',2011=>'2011',2012=>'2012',2013=>'2013',2014=>'2014',2015=>'2015',2016=>'2016',2017=>'2017',2018=>'2018',2019=>'2019',2020=>'2020',2021=>'2021',2022=>'2022',2023=>'2023',2024=>'2024');

$dec_place = array(1=>'2',2=>'2',3=>'8',4=>'2',5=>'4',6=>'0',7=>'2');
//1=qnty(kg),2=qnty(yds),3=Rate,4=Amount(local Currency/Taka), 5=Foreign Currency, 6=Gmts qnty, 7=%(percentage)
$dec_place_other_item = array( 1 => "1", 2 => "2", 3 => "3", 4 => "4", 5 => "5", 6 => "6", 7 => "7", 8 => "8");

$all_cal_day=array(1=>"Saturday",2=>"Sunday",3=>"Monday",4=>"Tuesday",5=>"Wednesday",6=>"Thursday",7=>"Friday");

$integrated_project_list=array(1=>"Platform",2=>"HRM System",3=>"Acounts");
$string_search_type=array(1=>"Exact",2=>"Starts with",3=>"Ends with",4=>"Contents");
$calculation_basis=array(1=>"Order Qty",2=>"Plan Cut Qty");
$dyeing_re_process=array(1=>"Topping",2=>"Adding",3=>"Stripping");
$pi_status=array(2=>"All",1=>"Approved",0=>"Un Approved");
$project_list=array(1=>"Accounts",2=>"HRM System",3=>"Trims ERP",4=>"Buying House ERP");
// Plannig Board Parameter
$smv_basis=array(1=>"Non-Calculative",2=>"Calculative");
$line_shape_arr=array(1=>"Straight Line Single",2=>"Straight Line Double",3=>"U-Shape");
$difficulty_arr=array(1=>'Easy',2=>'Semi-critical',3=>'Critical',4=>'Others');
//******************************************************************************************** please DO NOT CHANGE UPPER ARRAYS
$commercial_invoice_format=array(1=>"Invoice F-1",2=>"Invoice F-2",3=>"Invoice F-3",4=>"Invoice F-4",5=>"Invoice F-5",6=>"Invoice F-6",7=>"Invoice F-7",8=>"Invoice F-8",9=>"Invoice F-9",10=>"Invoice F-10",11=>"Invoice F-11",12=>"Invoice F-12",13=>"Invoice F-13",14=>"Invoice F-14",15=>"Invoice F-15",16=>"Invoice F-16",17=>"Invoice F-17",18=>"Invoice F-18",19=>"Invoice F-19",20=>"Invoice F-20");//As per Monzu

// common for All Module //
//$currency=array(1=>"Taka",2=>"USD",3=>"EURO",4=>"CHF",5=>"SGD");
$currency=array(1=>"BDT",2=>"USD",3=>"EURO",4=>"CHF",5=>"SGD",6=>"Pound",7=>"YEN");
// Library Module //
$user_type=array(1=>"General User",2=>"Admin User",3=>"Demo User");
$mail_user_type=array('1'=>'Management','2'=>'Marketing','3'=>'General');

$get_upto=array(1=>"Greater Than",2=>"Less Than",3=>"Greater/Equal",4=>"Less/Equal",5=>"Equal");
$knit_defect_array=array(1=>"Hole",5=>"Loop",10=>"Press Off",15=>"Lycra Out",20=>"Lycra Drop",21=>"Lycra Out/Drop",25=>"Dust",30=>"Oil Spot",35=>"Fly Conta",40=>"Slub",45=>"Patta",50=>"Needle Break",55=>"Sinker Mark",60=>"Wheel Free",65=>"Count Mix",70=>"Yarn Contra",75=>"NEPS",80=>"Black Spot",85=>"Oil/Ink Mark",90=>"Set up",95=>"Pin Hole",100=>"Slub Hole",105=>"Needle Mark",110=>"Miss Yarn",115=>"Color Contra [Yarn]",120=>"Color/dye spot",125=>"friction mark",130=>"Pin out",135=>"softener spot",140=>"Dirty Spot",145=>"Rust Stain",150=>"Stop mark",155=>"Compacting Broken",160=>"Insect Spot",165=>"Grease spot",166=>"Knot",167=>"Tara",168 =>"Contamination",169 =>"Thick and Thin");
$sew_fin_alter_defect_type_sweater_arr = array(1 => "Shoulder Joint Wavy",2 => "Broken Stich At Seam",3 => "Button Joint Up Down",4 => "Cuff Joint Up Down",5 => "Finishing Yarn Visible", 6 => "H/Tack Loose At Armhole", 7 => "H/Tack Loose At Neck", 8 => "Hole At Armhole", 9 => "Improper Bartak", 10 => "Improper H/Stich At Neck", 11 => "Improper Pulling At Neck",12 => "Improper Pulling At Placket",13 => "Improper Trim",14 => "Inside V Stich Poor", 15 => "Up Down",16 => "Neck Shape Poor", 17 => "Needle Drop",18 => "Overlock Wavy",
19 => "Placket Edge Stich Poor", 20 => "Placket Shading", 21 => "Pulled Yarn",22 => "Shoulder Stich Broken", 23 => "Shoulder Tack Missing", 24 => "Side Seam Joint Allowance", 
25 => "Uncut Thread",26 => "V Joint Poor",27 => "Visible Knot",28 => "Visible Stitch End",29 => "Wrong Mending"); 
$sew_fin_reject_type_arr = array(1=>"Fabric", 2=>"Sewing", 3=>"Measurement", 4=>"Spot", 5=>"Shade", 6=>"Hole", 7=>"Cutting", 8=>"Wash", 9=>"Print", 10=>"Twisting", 11=>"Conta",12 => "Color Spot",13 => "Crease Mark",14 => "Dirty Spot",15 => "Distinguish",16 => "Dusted",17 => "Dyeline",18 => "Emb Rejection ",19 => "Embroidery",20 => "Fabric (Z) Hole",21 => "HTS Problem Cutting",22 => "HTS Problem Finishing",23 => "Iron Spot",24 => "Knot",25 => "M/C Knife  Cut",26 => "Measurement (+-)",27 => "Needle Cut",28 => "Oil Spot",29 => "Part Mistake",30 => "Part Shade",31 => "Patta",32 => "Pleat",33 => "Print Reject",34 => "Runing Shade",35 => "Scissor Cut",36 => "Slub",37 => "Softner Mark",38 => "Tag Gun Rej",39 => "Twist",40 => "Uneven Dyeing",41 => "Yarn Missing",42 => "Yarn Contamination",100=>"Others");

$knit_defect_short_array=array(1=>"H",5=>"L",10=>"POF",15=>"LYO",20=>"LYD",21=>"LO",25=>"DU",30=>"OS",35=>"FC",40=>"SL",45=>"PA",50=>"NB",55=>"SM",60=>"WF",65=>"CM",70=>"YCO",75=>"NEPS",80=>"BS",85=>"OIM",90=>"SU",95=>"PH",100=>"SH",105=>"NM",110=>"MY",115=>"YC",120=>"DS",125=>"FR",130=>"PO",135=>"SP",140=>"D",145=>"SR",150=>"STM",155=>"CB",160=>"IS",165=>"GS");
$knit_defect_inchi_array=array(1=>'Defect=<3" : 1',2=>'Defect=<6" but >3" : 2',3=>'Defect=<9" but >6" : 3',4=>'Defect>9" : 4',5=>'Hole<1" : 2',6=>'Hole>1" : 4');
$foc_claim_arr=array(1=>"FOC",2=>"Claim");

$sample_type=array(2=>"PP",3=>"FIT",4=>"Size Set",5=>"Others",6=>"Development",7=>"Production",8=>"Tag",9=>"Photo",10=>"Packing",11=>"Final",12=>"Proto",13=>"Counter", 14 => "SMS",15 => "Line Review", 16 => "GCR", 17 => "Wash", 18 => "AD",19=>"GPT");
$sample_sent_to_list = array(1=>'Buyer/BH',2=>'MKT',3=>'Textile Lab',4=>'3rd Party',5=>'Dyeing',6=>'Garments GPQ',7=>'Garments QA',8=>'Other Unit',9=>'Meeting Sample',10=>'Store',11=>'Sample Gift');
$design_source_arr=array(1=>"Buyer",2=>"F. Studio",3=>"DPD");
$trims_production_module = array(1 =>"Production Update Areas", 2 =>"Last Process production Qty Control",3=>"Last Process Trims.Del.Qty Control",4=>"Delivery Qty. Auto Fill Up");
$printing_production_module = array(1 =>"Printing Recipe Entry",2 =>"Printing Bill  Quantity Control ",3 =>"Printing Delivery Entry Control", 4=>"Embroidery Delivery Entry Control",5=>"Printing Barcode Maintain",6=>"Embroidery Bill Quantity Control");
$trims_production_update_areas = array(1 => "Item Level", 2 => "PO Level", 3 => "Color and Size Level");
$trim_type=array(1=>"Sewing",2=>"Packing/Finishing",3 => "Heat Transparent Print");
$production_module=array(1=>"Production Update Areas",2=>"Sewing Piece Rate WQ Limit",3=>"Fabric in Roll Level",4=>"Fabric in Machine Level",13=>"Batch Maintained",15=>"Auto Fabric Store Update",23=>"Production Resource Allocation",24=>"Auto Batch No Creation",25=>"SMV Source For Efficiency",26=>"Sewing Production Start",27=>"Barcode Generation",28=>"Production Update for Reject Qty",29=>"Cutting Piece Rate WQ Limit",30=>"Piece Rate Safety %",31=>"Booking Approval needed for Knitting Plan",32=>"Cut Panel delv. Basis",33=>"Last Process Prod. Qty Control",34=>"Process Costing Maintain",35=>"Fabric Production Control with program/booking",36=>"Grey Fabric Grouping",37=>"Bundle No Creation",38=>"Cut and Lay Roll Wise Batch no",39=>"RMG No Creation",40=>"Service Rate Source",41=>"Working Company Mandatory",42=>"Qty Source for Poly Entry",43=>"Qty Source for Packing And Finishing",44=>"Fabric Source for Batch",45=>"Finish Fabric Grouping",46=>"Process Costing Rate Basis for Knitting",47=>"Auto Production quantity update by QC",48 => "Mandatory QC For Delivery",49 => "Roll Weight Control", 50 => "Last Process Prod. Qty Control Sweater", 51=>"Fabric Production Over Control", 52=>"Textile business concept",53=>"Delivery Qty Source (Sample)",54=>"Stock Qty Check",55 =>"Finish Fabric Production Validation With",56 =>"Textile Store Selection" ,57 =>"Dyeing Charge Source For Finish Fabric Rate" ,58 =>"Projected PO By Garments Production",59 =>"Recipe Maintain Level",60 =>"Actual Production Resource Entry Style Ref.",61 =>"Booking Qnty of FSO",62 =>"Hide QC Result from Knitting Production",63 =>"Dyeing Production Control Based on Chemical Issue" ,64 =>"Service Booking Mandatory For Outbound Subcontact Knitting",65=>"Linking Input Production Source[Bundle]",66=>"Textile Sales Maintain",67=>"Tube/Ref. No Setting");
asort($production_module);
$sales_order_type_arr = array(1=>"Fully Sales", 2=>"Service");
$dyeing_charge_source_arr = array(1=>"From Library(Without Materials)",2=>"From Budget(With Materials, Dyes and Chemical Cost)"); 
$textile_business_concept = array(1 => "Composite", 2 => "Textile", 3 => "Both");
$knitting_system_arr = array(1=>'Single System',2=>'Double System');
$fit_list_arr = array(1=>"Slim FIT",2=>"Regular FIT",3=>"Standard FIT",4=>"Plus FIT",5=>"Over Size FIT",6=>"Loose FIT",7=>"Relaxed FIT",8=>"Junior FIT",9=>"American FIT",10=>"Boxy FIT");

$finish_qc_defect_array=array(1=>"Hole", 5=>"Color/Dye Spot", 10=>"Insect Spot", 15=>"Yellow Spot", 20=>"Poly Conta", 25=>"Dust", 30=>"Oil Spot", 35=>"Fly Conta", 40=>"Slub", 45=>"Patta/Barrie Mark", 50=>"Cut/Joint", 55=>"Sinker Mark", 60=>"Print Mis", 65=>"Yarn Conta", 70=>"Slub Hole", 75=>"Softener Spot",     95=>"Dirty Stain", 100=>"NEPS", 105=>"Needle Drop", 110=>"Chem: Stain", 115=>"Cotton seeds", 120=>"Loop hole", 125=>"Dead Cotton", 130=>"Thick & Thin", 135=>"Rust Spot", 140=>"Needle Broken Mark", 145=>"Dirty Spot", 150=>"Side To Center Shade", 155=>"Bowing", 160=>"Uneven", 165=>"Yellow Writing", 170=>"Fabric Missing", 175=>"Dia Mark", 180=>"Miss Print", 185=>"Hairy", 190=>"G.S.M Hole", 195=>"Compacting Mark", 200=>"Rib Body Shade", 205=>"Running Shade", 210=>"Plastic Conta", 215=>"Crease mark", 220=>"Patches", 225=>"M/c Stoppage", 230=>"Needle Line", 235=>"Crample mark", 240=>"White Specks", 245=>"Mellange Effect", 250=>"Line Mark", 255=>"Loop Out", 260=>"Needle Broken" ,261=>"Loop",262=>"Oil Spot/Line",263=>"Lycra Out/Drop",264=>"Miss Yarn",265=>"Color Contra [Yarn]",266=>"Friction Mark",267=>"Pin Out",268=>"Rust Stain",269=>"Stop Mark",270=>"Compacting Broken",271=>"Grease Spot",272=>"Cut Hole",273=>"Snagging/Pull Out" ,274=>"Press Off",275=>"Wheel Free",276=>"Count Mix",277=>"Black Spot",278=>"Set Up",279=>"Pin Hole" ); //AS Per Rehan
$aop_orde_type = array(1 => "Flat", 2 => "Rotary", 3 => "Flat and Rotary");  //AS Per Mahbub
$aop_work_order_type = array(1 => "Main", 2 => "Sample", 3 => "Subcontract"); //AS Per Mahbub

$subcon_variable = array(1 => "Dyeing & Finishing Bill Qty", 2 => "Knitting Fabric From Yarn Count Det.", 3 => "Bill Rate", 4 => "SubCon Batch Fabric Source", 5 => "Fabric in Roll Level", 6 => "Barcode Generation", 7 => "In-House Knit Bill From", 8 => "In-House Finishing Bill From", 9 => "Knitting In-House", 10 => "Knitting Out-Bound", 11 => "Dyeing & Finishing In-House", 12=> "Dyeing & Finishing Out-Bound", 13=> "AOP Master Batch", 14=> "Mandatory For AOP QC Entry", 15=> "In-bound Sub-Contract Program", 16=> "Validation about Yarn Issue and Knitting Production", 17=>"Color Mixing In-bound Sub-Contract Program",18=>"Subcon Mandatory Field");

$trims_sub_section = array(1 => "Crochet", 2 => "Jacquard", 3 => "Covering Rubber", 4 => "Printed label", 5 => "Label Screen Print", 6 => "PP", 7 => "PE", 8 => "HDPE", 9 => "PVC", 10 => "BOPP", 11 => "ZIP Lock", 12 => "Tag Pin", 13 => "Lock Pin", 14 => "Polyester", 15 => "Nylon", 16 => "LDPE", 17 => "LLDPE", 18 => "Woven Label", 19 => "ST 2000 mtr", 20 => "ST 4000 mtr",21=>"ST 3000 mtr.",22=>"Draw Cord",23=>"Normal Elastic",24=>"Blister"); //AS per Nazim

$yarn_color_arr=array(1=>'GREY',2=>'GREY WHITE',3=>'MELANGE');
$yarn_fibre_type_arr=array(1=>'SINGLE',2=>'BLENDED',3=>'MIXED');
$yarn_fibre_arr=array(1=>'COTTON',2=>'POLYESTER',3=>'LUREX',4=>'CVC (CHIEF VALUE COTTON)');
$count_system_arr=array(1=>'NE',2=>'D',3=>'NM');
$number_of_filament_arr=array(1=>'Mono',2=>'24 F');
$yarn_finish_arr=array(1=>'SIRO',2=>'INJECT',3=>'SLUB',4=>'NIM');
$yarn_spinning_system_arr=array(1=>'RING SPUN',2=>'ROTOR',3=>'DTY');


$dyeing_finishing_bill=array(1=>"On Grey Qty",2=>"On Delivery Qty");
$production_update_areas=array(1=>"Gross Quantity Level",2=>"Color Level",3=>"Color & Size Level");
$smv_adjustment_head=array(1=>"Extra Hour",2=>"Lunch Out",3=>"Sick Out",4=>"Leave Out",5=>"Late In",6=>"General Out",7=>"NPT");
$wash_operation_arr = array(1 => "1st Wash", 2 => "Final Wash", 3 => "1st Dyeing", 4 => "2nd Dyeing");
$wash_sub_operation_arr = array(1 => "Towel Wash", 2 => "Acid Wash", 3 => "Tie Dye", 4 => "Cool Dye" , 5 => "Deep Dye" );
//$wash_gmts_type_array = array(1 => "Denim Gmts", 2 => "Twill Gmts", 3 => "Dyeing Gmts");
$wash_gmts_type_array = array(1 => "Denim Garments", 2 => "Twill Garments", 3 => "Dyeing Garments", 4 => "Woven Garments", 5 => "Knit Garments", 6 => "Non Denims", 7 => "Sweater Garments",);

$fabric_weight_type = array(1=>'Ounce',2=>'GSM');
$main_fabric_co_arr=array(1=>"Local",2=>"Import");

$item_category = return_library_array("select category_id,short_name from lib_item_category_list where status_active=1 and is_deleted=0 order by short_name", "category_id", "short_name");


$item_category_type_arr=array(1 => "Yarn", 2 => "Finish Fabric", 3 => "Woven Finish Fabric", 4 => "Accessories", 5 => "Dyes Chemical and Auxilary Chemical", 8 => "General item", 12 => "Services - Fabric", 13 => "Grey Fabric", 14 => "Woven Grey Fabric", 101 => "Raw Material");
$rack_shelf_upto_arr=array(1 => "Store", 2 => "Floor", 3 => "Room", 4 => "Rack", 5 => "Shelf", 6 => "Bin/Box");

$maping_export_import_category=array(3 => "100", 10 => "2", 11 => "3", 20 => "71", 23 => "74", 36 => "104", 37 => "103", 35 => "102", 40 => "1", 45 => "4");
$maping_import_export_category=array(100 => "3", 2 => "10", 3 => "11", 71 => "20", 74 => "23", 104 => "36", 103 => "37", 102 => "35", 1 => "40", 4 => "45");

$report_signeture_list = array(0 => "-- Select Report --", 1 => "Fabric Booking", 2 => "Trims Booking", 3 => "PI Wise Yarn Receive", 4 => "Short Fabric Booking", 5 => "Sample Fabric Booking -With order", 6 => "Sample Fabric Booking -Without order", 7 => "Yarn Receive Return", 8 => "Dyes And Chemical Receive", 9 => "Dyes And Chemical Issue", 10 => "Dye/Chem Receive Return", 11 => "General Item Receive", 12 => "General Item Issue", 13 => "General Item Receive Return", 14 => "General Item Issue Return", 15 => "Dyes And Chemical Issue Requisition", 16 => "Knit Grey Fabric Receive", 17 => "Knit Grey Fabric Issue", 18 => "Grey Fabric Transfer Entry", 19 => "Grey Fabric Order To Order Transfer Entry", 20 => "Woven Finish Fabric Receive", 21 => "Knit Finish Fabric Issue", 22 => "Woven Finish Fabric Issue", 23 => "Finish Fabric Transfer Entry", 24 => "Finish Fabric Order To Order Transfer Entry", 25 => "Purchase Requisition", 26 => "Embellishment Issue Entry", 27 => "Embellishment Receive Entry", 28 => "Sewing Input", 29 => "Sewing Output", 30 => "Iron entry", 31 => "Packing And Finishing", 32 => "Ex-Factory", 33 => "Gate In Entry", 34 => "Gate Out Entry", 35 => "Trims Receive Entry", 36 => "Trims Issue", 37 => "Yarn Issue Return", 38 => "Yarn Transfer Entry", 39 => "Yarn Order To Order Transfer Entry", 40 => "Daily Yarn Demand", 41 => "Knitting Plan Report", 42 => "Yarn Work Order", 43 => "Yarn Dyeing Work Order", 44 => "Knitting Delivery Challan", 45 => "SubCon Fabric Finishing Entry", 46 => "SubCon Delivery Challan", 47 => "SubCon Knitting Bill Issue", 48 => "SubCon Dyeing And Finishing Bill Issue", 49 => "Yarn Issue", 50 => "SubCon Cutting Bill Issue", 51 => "SubCon Material Return Challan", 52 => "Batch Creation", 53 => "Fabric Service Booking", 54 => "Cutting Delivary To Input", 55 => "Stationary Work Order", 56 => "SubCon Batch Creation", 57 => "Embellishment Work Order", 58 => "Cut and Lay Entry", 59 => "Dyes Chemical Work Order", 60 => "Spare Parts Work Order", 61 => "Subcon Material Issue", 62 => "Recipe Entry", 63 => "Garments Delivery", 64 => "SubCon Knitting Delivery Challan", 65 => "Yarn Receive", 66 => "Knit Finish Fabric Receive By Textile", 67 => "Finish Fabric Production Entry", 68 => "Finish Fabric Delivery to Store", 69 => "Quotation Evaluation", 70 => "Grey Fabric Delivery to store roll wise", 71 => "Grey Fabric Receive Roll By Batch", 72 => "Grey Roll Issue to Sub Contact ", 73 => "AOP Roll Receive", 74 => "Finish Fabric Roll Receive By Cutting", 77 => "Sample Ex-factory", 78 => "Scrap Out Challan", 79 => "Service Booking For AOP", 80 => "Lab Test Work Order", 81 => "Service Booking For Knitting", 82 => "Service Booking For Dyeing", 83 => "Knit Finish Fabric Receive Return", 84 => "Piece Rate Work Order", 85 => "Knit Grey Fabric Receive Return", 86 => "Sample Development", 87 => "Knit Grey Fabric Issue Return", 88 => "Finish Fabric Issue Return", 89 => "Dye/Chem Issue Return", 90 => "Trims Issue Return", 91 => "Inspection", 92 => "Service Booking For AOP Without Order", 93 => "Fabric Requisition For Batch", 94 => "Roll Wise Grey Fabric Transfer Entry", 95 => "TNA Progress Report", 96 => "Order Wise Sewing Bill Wages Statement", 97 => "BTB Liability Coverage Report", 98 => "Trims Receive Return Entry", 99 => "Fab Service Receive", 100 => "Yarn Requisition Entry", 101 => "GSD Entry", 102 => "Yarn Purchase Requisition", 103 => "Poly Entry", 104 => "Roll Receive by Finish Process", 105 => "Trims Transfer", 106 => "Grey Fabric Bar-code Striker Export Report", 107 => "Finish Fabric Roll Delivery To Store", 108 => "Grey Fabric Delivery to Store", 109 => "Pre-Costing", 110 => "Operation Bulletin", 111 => "Roll wise Grey Sales Order To Sales Order Transfer", 112 => "Finish Roll Issue Return", 113 => "Yarn Dyeing Work Order Sales", 114 => "Gate Pass Entry", 115 => "Sample Trims Booking Without Order", 116 => "Finishing Input", 117 => "Cutting QC", 118 => "Cutting Entry", 119 => "Knitting Card", 120 => "Ready To Sewing Entry", 121 => "Partial Fabric Booking", 122 => "Yarn Service Work Order", 123 => "Cutting Delivery To Input Challan", 124 => "Grey Fabric Roll Issue", 125 => "Roll Wise Grey Fabric Delivery To Store", 126 => "Quotation Inquery", 127 => "Sample Delivery", 128 => "Finish Fabric Requisition for Cutting", 129 => "Woven Finish Fabric Issue Return", 130 => "Woven Finish Fabric Receive Return", 131 => "Fab Service Receive Return", 132 => "Multiple Job Wise Trims Booking V2", 133 => "Multiple Job Wise Emblishment Work Order", 134 => "Sample Requisition With Booking", 135 => "Sample Requisition Fabric Booking -With order", 136 => "Buyer Order Wise Prod Spent Min Produce Min With CM Report", 137 => "Order Reconciliation report", 138 => "Embl. Recipe Entry", 139 => "Embl.Dyes And Chemical Issue Requisition", 140 => "Embellishment Production", 141 => "Buyer Wise Total Export Value and CM", 142 => "LC Wise Knit Finish Fabric Receive Report", 143 => "Item Issue requisition", 144 => "Post Cost Analysis Report V2", 145 => "Fabric Issue to Finish Process", 146 => "Sample Requisition", 147 => "Pro Forma Invoice V2", 148 => "Finish Fabric Multi Issue challan", 149 => "Knit Finish Fabric Receive By Garments", 150 => "Multi Job Wise Service Booking Knitting", 151 => "Yarn Dyeing Work Order Without Order", 152 => "Other Purchase order", 153 => "General Item Transfer", 154 => "Embellishment Delivery", 155 => "Embellishment Bill Issue",156 => "Raw Material Receive", 157 => "Raw Material Issue",158 =>"Packing and Finishing Bill Issue",159 =>"Pre-Costing V2 [Acc. Dtls. V2]",160 =>"Trims Job Card Preparation",161 =>"Multiple Job Wise Short Trims Booking V2",162 =>"AOP Batch Creation",163 =>"Order Sheet Report",164 =>"AOP Recipe Entry",165 =>"Topping Adding Stripping Recipe Entry",166 =>"Grey Fabric Service Work Order",167 =>"Trims Bill Issue",168 =>"AOP Dyes Chemical Issue",169 =>"AOP Dyes And Chemical Issue Requisition",170 =>"Embroidery Bill Issue",171 =>"Printing Bill Issue",172 =>"Wash Dyes Chemical Issue",173 =>"AOP Delivery Entry",174 =>"Trims Delivery Entry",175 =>"Knitting Bill Entry",176 =>"Fabric Sales Order Entry",177 =>"Commercial Office Note",178 =>"Left Over Garments Issue",179 =>"Left Over Garments Receive",180 =>"Service Booking For Kniting and Dyeing [Without Order]",181 =>"Wash Delivery",182=>"Sewing Bill Issue",183=>"Bundle Issue To Linking",184=>"Embroidery Material Receive",185=>"Embroidery Material Issue",186=>"Embroidery Material Receive Return",187=>"Embroidery Material Issue Return",188=>"Bundle Issue To First Inspection",189=>"Bundle Receive In First Inspection",190=>"AOP Bill Issue",191=>"Request For Lab Dip",192=>"Bundle Issue To Wash",193=>"Bundle Receive in Wash",194=>"Recipe For Sweater",195=>"Color Ingredients",196=>"Dyes And Chemical Issue Requisition [Sweater]",197=>"Ex-Factory Against Garments Bill Entry",198=>"Knitting W/O Bill",199>"Dyeing W/O Bill",200 => "Sub-Con Order Entry",201 => "Import Document Acceptance",202 =>"Grey Fabric Transfer Entry V2",203=>"Knitting Plan Report[Sample Without Order]",204=>"Scrap Material Issue",205=>"Finish Fabric Delivery To Garments",206=>"Bill Processing Entry", 207 => "Sample Req. With Booking[Woven]", 208 => "Planing Info Entry", 209 => "Yarn Store Requisition Entry",210=>"Sub Con Work Order Entry",211=>"Finish Trims Receive Entry",212=>"Trims Order Receive",213=>"Knitting Plan Report[Sales]",214=>"Price Quotation",215=>"Wash Received and Delivery Statement",216=>"Wash Material Receive Return",217=>"Wash Material Issue Return",218=>"Pre-Costing V2 Woven",219=>"Sourcing Pre-Costing V2 Woven",220=>"Knitting Bill Issue",221=>"Woven Cut and Lay Entry Ratio Wise",222=>"Sample Embellishment Issue",223=>"Fabric Issue For AOP",224=>"Fabric Receive For AOP",225=>"Scrap Material Receive",226=>"Bundle Wise Sewing Input",227=>"Cutting Wages Bill",228=>"Sewing Wages Bill",229=>" Ironing Wages  Bill",230=>"Knitting Production",231=>"AOP Material Issue",232=>"Wash Material Issue",233=>"Unwashed Body Issue Requisition",234=>"AOP Material Issue Requisition",235=>"Demand For Accessories",236=>"Sewing Bill Entry",237=>"Pre-Costing V3",238=>"LC Wise Trims Receive",239=>"Raw Material Issue Requisition",240=>"Sourcing Post Cost",241=>"Finish Fabric Bill Entry For Roll",242=>"Bundle Issued to Embroidery",243=>"Bundle Issued to Print",244=>"Cutting QC V2",245=>"Cut and Lay Ratio Wise 3",246=>"Knit Finish Fabric Roll Receive By Textile",247=>"Stock Yarn Allocation",248=>"Garments Service Work Order",249=>"Knitting Work Order",250=>"Woven Finishing Entry",251=>"Woven Wash Issue",252=>"Woven Wash Receive",253 =>"General Transfer Requisition",254=>"Printing Delivery Entry [Bundle]",255=>"Additional Raw Material Issue requisition", 256=>"Hang Tag Entry",257=>"Export Pro Forma Invoice",258=>"Printing Dyes And Chemical Issue Requisition");
asort($report_signeture_list);


$report_template_list=array(1=>'Template 1',2=>'Template 2',3=>'Template 3',4=>'Template 4',5=>'Template 5');
//,100=>"Yarn Purchase Order",101=>"Dyes And Chemical Purchase Order",102=>"Stationary Purchase Order",103=>"Others Purchase Order" // //Dublicate //Remove as per Jahid
$entry_form = array(1 => "Yarn Receive", 2 => "Knitting Production", 3 => "Yarn Issue", 4 => "Chemical Receive", 5 => "Chemical Issue", 6 => "Dye Production Update", 7 => "Finish Fabric Production Entry", 8 => 'Yarn Receive Return', 9 => 'Yarn Issue Return', 10 => 'Yarn Transfer Entry', 11 => 'Yarn Order To Order Transfer Entry', 12 => 'Grey Fabric Transfer Entry', 13 => 'Grey Fabric Order To Order Transfer Entry', 14 => 'Finish Fabric Transfer Entry', 15 => 'Finish Fabric Order To Order Transfer Entry', 16 => 'Knit Grey Fabric Issue', 17 => 'Woven Finish Fabric Receive', 18 => 'Knit Finish Fabric Issue', 19 => 'Woven Finish Fabric Issue', 20 => 'General Item Receive', 21 => 'General Item Issue', 22 => 'Knit Grey Fabric Receive', 23 => 'Woven Grey Fabric Receive', 24 => 'Trims Receive', 25 => 'Trims Issue', 26 => 'General Item Receive Return', 27 => 'General Item Issue Return', 28 => 'Dye/Chem Receive Return', 29 => 'Dye/Chem Issue Return', 30 => 'Slitting/Squeezing', 31 => 'Drying', 32 => 'Heat Setting', 33 => 'Compacting', 34 => 'Special Finish', 35 => 'Dyeing Production', 36 => 'SubCon Batch Creation', 37 => "Finish Fabric Receive Entry", 38 => "SubCon Dyeing Production", 39 => "Doc. Submission to Buyer", 40 => "Doc. Submission to Bank", 41 => "Yarn Dying With Order", 42 => "Yarn Dying Without Order", 43 => "Main Trims Booking", 44 => "Main Trims Booking V2", 45 => "Knit Grey Fabric Receive Return", 46 => "Knit Finish Fabric Receive Return", 47 => "Singeing", 48 => "Stentering", 49 => "Trims Receive Return", 50 => "Woven Grey Fabric Receive Return", 51 => "Knit Grey Fabric Issue Return", 52 => "Knit Finish Fabric Issue Return", 53 => "Grey Fabric Delivery to store", 54 => "Finish Fabric Delivery to store", 55 => "Chemical Transfer Entry", 56 => "Grey Fabric Delivery to store roll wise", 57 => "General Item Transfer", 58 => "Knit Grey Fabric Receive Roll", 59 => "Recipe Entry", 60 => "Dyeing Re Process", 61 => "Grey Fabric Issue Roll Wise", 62 => "Grey Fabric Receive Roll By Batch", 63 => "Grey Roll Issue to Sub Contact ", 64 => "Batch Creation For Roll", 65 => "AOP Roll Receive", 66 => "Finish Fabric Production and QC By Roll", 67 => "Finish Fabric Roll Delevery To Store", 68 => "Finish Fabric Roll Receive By Store", 69 => "Purchase Requisition", 70 => "Yarn Purchase Requisition", 71 => "Finish Fabric Roll Issue", 72 => "Finish Fabric Roll Receive By Cutting", 73 => "Trims Issue Return", 74 => "Batch Creation for Gmts Wash", 75 => "Roll Splitting", 76 => "cut and lay entry", 77 => "cut and lay entry roll wise", 78 => "Trims Order To Order Transfer Entry", 79 => "Lab Test Work Order", 80 => "Grey Fabric Order To Sample Transfer Entry", 81 => "Grey Fabric Sample To Order Transfer Entry", 82 => "Roll Wise Grey Fabric Transfer Entry", 83 => "Roll wise Grey Fabric Order To Order Transfer Entry", 84 => "Roll wise Grey Fabric Issue Return", 85 => "Garments Ex-Factory Return", 86 => "Main Fabric Booking", 87 => "Multiple Job Wise Trims Booking V2", 88 => "Short Fabric Booking", 89 => "Sample Fabric Booking -With order", 90 => "Sample Fabric Booking -Without order", 91 => "Fabric Issue to Fin. Process", 92 => "Fabric Service Receive", 93 => "Cut and Lay Entry Ratio Wise", 94 => "Yarn Service Work Order", 95 => "Sewing Input", 96 => "Bundle Wise Sewing Input", 97 => "Cut and Lay Entry Ratio Wise RMG No", 98 => "Knitting Production", 99 => "Cut and Lay Entry Ratio Wise Urmi", 104 => "Pro Forma Invoice", 105 => "BTB/Margin LC", 106 => "Export LC Entry", 107 => "Sales Contract Entry", 108 => "Partial Fabric Booking", 109 => "Fabric Sales Order Entry", 110 => "Roll wise Grey Fabric Order To Sample Transfer Entry", 111 => "Pre-Costing", 112 => "Trims Transfer", 113 => "Grey Roll Splitting Before Issue", 114 => "Yarn Dying Without Order 2", 115 => "Roll Receive by Finish Process", 116 => "Sample Development", 117 => "Sample Requisition", 118 => "Main Fabric Booking Urmi", 119 => "Dia Wise Fabric Booking", 120 => "Yarn Requisition Entry For Sales", 121 => "Cutting Entry", 122 => "Order Update Entry", 123 => "Fabric Requisition For Batch 2", 124 => "Material/Goods Parking", 125 => "Yarn Dying Work Order Without Lot", 126 => "Finish Roll Issue Return", 127 => "Sample Requisition Cutting", 128 => "Sample Embellishment Entry", 129 => "Cotton Receive", 130 => "Sample Requisition Sewing Output", 131 => "Sample Wash Or Dyeing", 132 => "Sample Delivery Entry", 133 => "Roll wise Grey Sales Order To Sales Order Transfer", 134 => "Roll wise Finish Fabric Order To Order Transfer Entry", 135 => "Yarn Dyeing Work Order Sales", 136 => "Trims Batch Creation", 137 => "Sample Approval-Before Order Place", 138 => "Cut and Lay Entry Plies", 139 => "Sample Requisition Fabric Booking-With order", 140 => "Sample Requisition Fabric Booking-Without order", 141 => "Finish Roll Splitting Before Issue", 142 => "Sample Trims Booking With Order", 143 => "Sample Trims Booking Without Order", 144 => "Yarn Purchase Order", 145 => "Dyes And Chemical Purchase Order", 146 => "Stationary Purchase Order", 147 => "Others Purchase Order", 148 => "Sewing Operation", 149 => "Operation Bulletin", 150 => "SubCon Batch For Gmts Wash/Dyeing/Printing", 151 => "Recipe Entry For Gmts Wash/Dyeing/Printing", 152 => "Export Pro Forma Invoice", 153 => "Cotton Issue to Production", 154 => "Item Issue Requisition", 155 => "Cotton Issue Requisition", 156 => "Dyes And Chemical Issue Requisition", 157 => "SubCon Dyes And Chemical Issue Requisition", 158 => "Pre-Costing-V2", 159 => "SubCon Knitting Production", 160 => "Production All Pages(Cutting,Sewing,Emb)", 161 => "Embellishment Work Order V2", 162 => "Service Booking For AOP V2", 163 => "Order Entry", 164 => "Poly Entry", 165 => "PI Yarn", 166 => "PI Fabrics", 167 => "PI Accessories", 168 => "PI Service Fabric", 169 => "PI Yarn Dyeing", 170 => "PI Embellishment", 171 => "PI Lab Test", 172 => "PI General", 173 => "Yarn Production", 174 => "SubCon PI", 175 => "All Purchase Order Page", 176 => "Fabric Service Booking", 177 => "Service Booking For AOP Without Order", 178 => "Short Trims Booking [Multiple Order]", 179 => "Lab Test WO - Without Order", 180 => "Roll Wise Grey Fabric Sample To Sample Transfer Entry", 181 => "Cotton QC Entry", 182 => "Service Booking For Knitting", 183 => "Roll Wise Grey Fabric Sample To Order Transfer Entry", 184 => "Yarn Count Determination", 185 => "Cotton Receive Openning", 186 => "Knitting Bill Issue", 187 => "Inspection Expenses", 188 => "Ind Sales Confirmation", 189 => "Ind Sales Contract", 190 => "Ind Pi Request", 191 => "Indenting Pi", 192 => "Ind Lc Entry", 193 => "Ind Lc Amendment", 194 => "Service Booking For Kniting and Dyeing [Without Order]", 195 => "Woven Finish Fabric Roll Issue", 196 => "Woven Finish Roll Issue Return", 197 => "PI Garments Service", 198 => "Garments Delivery Entry", 199 => "Print Booking", 200 => "Print Booking Urmi", 201 => "Multi Job Wise Print Booking", 202 => "Woven Finish Fabric Receive Return", 203 => "Sample Requisition With Booking", 204 => "Printing Order Entry", 205 => "Embellishment Material Receive", 206 => "Fab Service Receive Return", 207 => "Embellishment Material Issue", 208 => "Trims Delivery", 209 => "Woven Finish Fabric Issue Return", 210 => "Waste Cotton Receive", 211 => "Waste Cotton Delivery Order", 212 => "Waste Cotton Delivery/issue", 213 => "Waste Cotton Bill", 214 => "Roll wise Finish Fabric sample To sample Transfer Entry", 215 => "Country and Order Wise trim Booking V3", 216 => "Roll wise Finish Fabric Order To Sample Transfer Entry", 217 => "Embellishment Batch Creation", 218 => "Roll Wise Grey Fabric Receive Return", 219 => "Roll wise Finish Fabric Sample To Order Transfer Entry", 220 => "Embl. Recipe Entry", 221 => "Embl.Dyes And Chemical Issue Requisition", 222 => "Embellishment Production", 223 => "Embellishment QC Entry", 224 => "Finish Fabric Delivery To Garments", 225 => "Knit Finish Fabric Receive By Garments", 226 => "Cotton Item Transfer", 227 => "PI Dyes Chemical", 228 => "Multi Job Wise Service Booking Knitting", 229 => "Multi Job Wise Service Booking Dyeing", 230 => "Finish Fabric FSO to FSO Transfer",231 => "Finish Fabric Multi Issue Challan", 232 => "Service Booking For Dyeing", 233 => "Knit Finish Fabric Issue Return", 234 => "Yarn Purchase Order[Sweater]", 235 => "Ring Machine Wise Production Entry", 236 => "Autocone Machine Wise Production Entry", 237 => "Packing Production Entry", 238 => "Sub-Con Order Entry", 239 => "Synthetic Fiver Receive", 240 => "Synthetic Fiver QC and Stock Recognising", 241 => "Synthetic Fiver Issue to Production", 242 => "Synthetic Fiver Receive Return", 243 => "Synthetic Fiver Issue Return", 244 => "Synthetic Fiver Item Transfer", 245 => "Time And Weight Record", 246 => "Synthetic Fiver Issue Requisition", 247 => "Finish Fabric Transfer Acknowledgement", 248 => "Sweater Yarn Receive", 249 => "Sweater Yarn Style To Style Transfer", 250 => "Embellishment Dyes Chemical Issue", 251 => "Gate Pass Entry", 252 => "Multiple Job Wise Trims Booking V2 for Sweater", 253 => "Yarn Lot Ratio Entry", 254 => "Embellishment Delivery", 255 => "Trims Order Receive", 256 => "Yarn Dyeing Bill Entry", 257 => "Job Card Preparation", 258 => "Woven Finish Fabric Transfer Entry", 259 => "Machine Wash Requisition", 260 => "Country and Order Wise Trims Booking V2", 261 => "Multiple Job Wise Trims Booking", 262 => "Multiple Job Wise Short Trims Booking V2", 263 => "Raw Material Receive", 264 => "Raw Material Receive Return", 265 => "Raw Material Issue", 266 => "Raw Material Issue Return", 267 => "Finish Fabric QC Result", 268 => "Woven Finish Fabric Transfer Acknowledgement", 269 => "Trims Production Entry", 270 => "Export Invoice", 271 => "Woven Partial fabric Booking", 272 => "Woven Multiple Job Wise Trims Booking", 273 => "Woven Multi Job Wise Short Trims Booking", 274 => "Woven Lab Test Work Order", 275 => "Woven Short fabric Booking",276=>"Trims Bill Issue",277=>"Sweater Yarn Issue",278=>"Aop Order Entry",279=>"AOP Material Receive",280=>"AOP Material Issue",281=>"AOP Batch Creation",282=>"Planning Info Entry For Sales Order",283=>"Grey qc for android",284=>"Sample Yarn Purchase Order[Sweater]",285=>"AOP Recipe Entry",286=>"Fabric Sales Order Entry Inter Company",287=>"Knit Finish Fabric Textile Receive Return",288=>"SubCon Material Receive",289=>"Woven Cut and Lay Entry Ratio Wise",290=>"AOP Dyes And Chemical Issue Requisition",291=>"AOP Production",292=>"Subcon Fabric Finishing Entry",293=>"Subcon Printing Production",294=>"AOP QC Entry",295=>"Wash Order Entry",296=>"Wash Material Receive",297=>"Wash Material Issue",298=>"Wash Dyes Chemical Issue",299=>"Wash Dyes And Chemical Issue Requisition",300=>"Wash Recipe Entry",301=>"Wash Production",302=>"Wash QC Entry",303=>"Wash Delivery Entry",304=>"Wash Bill Issue",306=>"Finish Fabric Transfer Entry With Sample",307=>"AOP Delivery Entry",308=>"AOP Dyes Chemical Issue",309=>"Grey Fabric Service Work Order",310=>"De Oiling",311=>"Embroidery Order Entry",312=>"Embroidery Material Receive",313=>"Embroidery Material Issue",314=>"Price Quotation",315=>"Embroidery Production",316=>"Batch Creation For Gmts. Wash",317=>"Knit Finish Fabric Roll Receive By Textile",318=>"Finish Fabric Roll Delivery To Garments",319=>"Bundle Issue To Linking",320=>"Bundle Receive In Linking",321=>"Bundle Wise Linking Input",322=>"Bundle Wise Linking Output",323=>"Dry Slitting",324=>"Embroidery QC Entry",325=>"Embroidery Delivery",326=>"Embel. Issue [Sweater]",327=>"Cotton Purchase Order",328=>"Knit Finish Fabric Roll Receive Return of Textile", 329=>"Finish Fabric Roll Issue Return of Textile",330=>"Embel. Receive [Sweater]",331=>"Iron Entry [Sweater]",332=>"Embroidery Bill Issue",333=>"Poly Entry [Sweater]",334=>"Item Account Creation for Trims",335=>"Sweater Yearn Service Work Order",336=>"Dyeing And Finishing Bill Issue",337=>"Sample Sewing Input",338=>"Sample Embellishment Issue",339=>"Roll Wise Grey Fabric Requisition For Transfer",340=>"Yarn Service Work Order Without Lot",341=>"Sweater Sample Requisition",342=>"Dry Production",343=>"SubCon Material Issue",344=>"SubCon Material Return",345=>"Sweater Sample Acknowledge",346=>"Bundle Wise Linking Operation Track",347=>"Bundle Wise Cutting Delivery To Input Challan", 348 => "Bundle Wise Sewing Line Input", 349 => "Bundle Wise Sewing Line Output", 350 => "Trims Receive Entry Multi Ref.",351 => "Order Entry By Matrix Woven",352 => "Roll wise Grey Sales Order To Sales Order Requisition For Transfer",353 => "Grey Fabric Requisition For Transfer Entry",354 => "Bundle Wise Sewing Input From Text File",355 => "Bundle Wise Sewing Output From Text File",356 => "Synthetic Fibre Purchase Order",357 => "Trims issue requisition[Packing/Finishing]",358 => "AOP Bill Issue",359=>"Grey Fabric Transfer Acknowledgement Entry",360=>"Wash Delivery Return",361 => "Service Booking For AOP",362 => "Weight Wise Grey Sales Order To Sales Order Transfer",363 =>"Gate In Entry",364 =>"Finish Fabric Transfer Acknowledgement For Sample",365=>"Order Entry By Matrix",366=>"Embroidery Material Receive",367=>"Embroidery Material Issue",368=>"Embroidery Material Receive Return",369=>"Embroidery Material Issue Return",370=>"Knitting Closing Sweater",371=>"Grey FSO To FSO Transfer Acknowledgement",372=>'Wash Material Receive Return',373=>'Wash Dyes and Chemical Issue Return',374=>'YD Order Entry', 375 => "Bundle Issue To First Inspection", 376 => "Bundle Receive From First Inspection",377 => "Trims issue requisition[Sewing]",378 => "Carding Machine Wise Production Entry",379 => "Drawing Frame (Finisher) Production Entry",380 => "Simplex Machine Wise Production Entry", 381 => "Sweater Yarn Receive Return", 382 => "Sweater Yarn Issue Return",383=>"Bundle Issue To Wash",384=>"Bundle Receive in Wash",385=>"Yarn Requisition Entry For Sample Without Order",386=>"Trims Transfer Acknowledgement",387=>"Yarn Dyeing Material Receive",388=>"Yarn Dyeing Material Issue",389=>"Batch Creation For Sweater",390=>"Recipe For Sweater",391=>"Dyes And Chemical Issue Requisition [Sweater]",392=>"Dyes And Chemical Issue [Sweater]",393=>"Wet Production [Sweater]",394=>"Ex-Factory Against Garments Bill Entry",395=>"Printing Bill Issue",396 =>"Sample Delivery To MKT",397 =>"Soft Conning Production Entry",398 =>"Yarn Dyeing Batch Creation",399 =>"Emblishment work order without order",400 =>"Soft Coning Production Delivery Entry",401 =>"Order Entry Sweater",402 => "Embellishment Work Order",403=> "Multiple Job Wise Embellishment Work Order",404=> "Sample AOP With Order new entry form",405=> "Pro Forma Invoice V2",406=> "Yarn bag sticker",407=> "PI Dyes Chemical",408=> "Batch Creation",409=> "Batch Creation Without Roll",410=> "Sample Recieve Entry",411=> "Strip Measurement for Sales Order",412=> "Knitting Work Order",413=> "SubCon Knitting Work Order",414=>"Dyeing Production For Y/D",415=>"Garments Issued to Wash",416=>"Garments Receive From Wash",417=>"Hydro Extractor",418=>"Dyeing Work Order",419=>"Yarn Transfer Acknowledgement",420=>"Item Account Creation",421=>"Knitting W/O Bill",422=>"Dyeing W/O Bill",423=>'Re-Winding',424=>'Wash/AOP Wash',425=>'Pre-Costing V2-Woven',426=>'Fabric Determination',427=>'Raw Material Issue Requisition',428=>'Planning Info Entry For Sample Without Order',429=>'Planning Info Entry',430=>'Quick Costing Woven',431=>'Piece Rate Work Order Urmi',432=>'Grey Fabric Sample To Sample Transfer Entry',433=>'Buyer Inquiry Knit',434=>'Buyer Inquiry Woven',435=>'Yarn Dyeing Recipe Entry',436=>'Wash Material Issue Return',437=>'Dyes And Chemical Issue Requisition For Y/D',438=>'Knitting Bill Entry for Gross',439=>"Woven Sample Requisition Fabric Booking -Without order",440=>"Woven Sample Requisition Fabric Booking -With order",441=>'YD Dryer',442=>'YD Re-Winding',443=>"YD Inspection",444=>'Quick Costing V2',445=>'Dyeing and Finishing Recipe',446=>"Packing And Finishing Bill Entry",447=>"YD Packing And Delivery",448=>"Yarn Service Bill Entry", 449 => "Sample Req. With Booking[Woven]", 450 => "Sub Con Work Order Entry",451=>"Finish Trims Receive Entry",452=>"Woven Finishing Entry",453=>"AOP Finishing Entry",454=>"Country Entry",455=>"Production Plan",456=>"Production Capacity Calculation",457=>"Buyer Inquiry Sweater",458=>"Short Quotation V2",459=>"Sweater Sample Requisition V2",460=>"Bundle Wise Sewing Output",461=>"Yarn Count Determination[Sweater]",462=>"Fabric Issue For AOP",463=>"Garments Finishing Delivery Entry",464=>"Dyes Chemical Transfer Acknowledgement",465=>"Printing Material Receive",466=>"Finish Trims Purchase Requisition",467=>"Fabric Receive Form AOP",468=>"Recipe Entry For Finishing",469=>"Sourcing Post Cost Sheet",470=>"Buyer Inspection",471=>"Short Quotation V3",472=>"Fabric Sales Order Entry V2",473=>"Unwashed Body Issue Requisition",474=>"AOP Material Issue Requisition",475=>"Yarn Allocation[Sales]",476=>"Waste Cotton Transfer Entry",477=>"General Item Transfer Acknowledgement",478=>"Program Wise Priority",479=>"Demand For Accessories",480=>"Stripe Measurement - Sales Order",481=>"Comparative Statement General",482=>"Comparative Statement Accessories",483=>"General Service Bill Entry",484=>"General Service Work Order",485=>"Finished Goods Order To Order Transfer",486=>"Finish Fabric Bill Entry For Roll",487=>"Raw Material Transfer Entry",488=>"Raw Material Transfer Acknowledgement",489=>"Waste Cotton Sales Return",490=>"Cut and Lay Entry Ratio Wise 3",491=>"Outside Knitting Bill Entry",492=>"Woven Multiple Job Wise Trims Booking V2",493=>"Order Entry By Matrix v2",494 =>"General Transfer Requisition",495=>"Printing Material Issue [Bundle]",496=>"Bill Processing Entry",497=>"Printing Production [Bundle]",498=>"Printing QC Entry [Bundle]",499=>"Printing Delivery Entry [Bundle]",500=>"Sewing Output",501=>"Additional Raw Material Issue requisition",502=>"Finish Garments Receive Entry", 503=>"Finish Garments Issue Entry", 504=>"Finish Garments Issue Return",505=>"Roll Wise Finish Fabric Transfer Entry",506=>"Roll Wise Finish Fabric Requisition For Transfer",507=>"Woven Finish Fabric Requisition for Cutting",508=>"Knit Finish Fabric Requisition for Cutting",509=>"Cut and Lay Entry Ratio Wise 4",510 => "Order Entry By Matrix Sweater",511 => "Short Quotation [Sweater]",512 => "Comparative Statements Fabries",513 => "Sales Target Entry For Fabric",514=>"Raw Cotton Delivery Order",515=>"Raw Cotton Bill Issue");
//, 231 => "Pro Forma Invoice V2",305=>"Printing Delivery"  

//Not Used
$entry_form_for_roll=array(1=>"grey_productin_entry", 2=>"batch_creation", 3=>"Dye Production Update",4=>"finish_fabric_receive",5=>"Woven Finish Fabric Receive",16=>"Knit Grey Fabric Issue",18=>'Knit Finish Fabric Issue',19=>'Woven Finish Fabric Issue',22=>'Knit Grey Fabric Receive',23=>'Woven Grey Fabric Receive',45=>"Knit Grey Fabric Receive Return");


$form_list_for_mail=array(1 => "Daily Order Entry", 2 => "Yesterday Total Activities", 3 => "TNA Task Mail", 4 => "Order Position By Team", 5 => "Booking Revised", 6 => "Missing PO List in TNA Process", 7 => "Order Revised", 8 => "Cancelled Order", 9 => "Subcontract Dyeing", 10 => "Returnable Pending", 11 => "Precost approval auto mail", 12 => "Below 5% Profitability Order", 13 => "Less EPM than CPM", 14 => "Total Production Activities", 15 => "Price Quotation Approval Status", 16 => "Grey Fabric Receive", 17 => "Finish Fabric Receive", 18 => "Daily Producion Auto Mail", 19 => "Bill of Entry overdue list", 20 => 'Yarn issue pending from allocation.', 21 => 'Bill of lading delay (Commercial)', 22 => 'Monthly capacity vs booked auto mail',23=>"Fabric Booking Revised",24=>"Price Quotation Mail Notification",
26=>"Sweater Export LC/Sales Contract Report",27=>"Sweater Shipment Pending Report",28=>"Pending pi for approval auto mail",29=>"Sweater Sample Delivery Pending",30=>"Sample Finish Fabric Pending Auto Mail",31=>"Machine Summary Below 80 % production",32=>"Sweater Garments Pre Costing BOM Auto Mail",33=>"Unit Wise Garments Production",34=>"Daily Order Update Auto Mail",35=>"Batch Wise Process Loss",36=>"LC/SC Notification",37=>"BTB Margin LC Notification",38=>"Order List Without Yarn Booking",39=>"Purchase Requisition Approval Notification",40=>"Buyer Inquiry Woven",41=>"Sample Requisition Wilth booking",42=>"Sample Requisition Acknowledge",43=>"Sample Requisition Unacknowledge",44=>"Sample Requisition refuse cause",45=>"Export CI Statement Auto Mail",56=>"Re-Order Label Item Report",57=>"Daily Dyeing Prod Analysis Auto Mail",58=>"Consumption Entry [CAD] For LA Costing",59 => "Monthly Producion Auto Mail",60 => "Buyer Inspection_ Mail Notification",61 => "Daily ERP Production Auto Mail",62 => "Order List Without Fabric Booking",63 =>"Pro Forma Invoice V2",64 => "No Fabric Booking",65 => "Fabric Booking Approval",66 => "Yarn Work Order Approval",67 => "Dyes N Chemical WO Approval",68 => "Woven Partial Fabric Booking",69=>"Monthly Capacity Vs Booked As TNA Date Wise",70=>"Date Wise Item Receive and Issue Report",71=>"Bank Liability Position As Of Today",72=>"BTB/Margin LC Amendment",73=>"Date Wise Item Receive and Issue Report [Woven Fabrics]",74=>"Daily Ex-factory Schedule Auto Mail",78=>"Pre-Costing V2-Woven");

$entry_form_for_approval=array(1=>"Purchase Requisition Approval", 2=>"Yarn WO Approval",3=>"Dyes/Chemical WO Approval",4=>"Spare parts WO Approval",5=>"Stationary WO Approval",6=>"Pro-forma Invoice WO Approval",7=>"Fabric Booking Approval",8=>"Trims Booking Approval",9=>"Sample Booking (Without Order) Approval",10=>"Price Quatation Approval",12=>"Short Fabric Booking Approval",13=>"Sample Fabric Booking-With Order",14=>"Yarn Delivery Approval",15=>"Pre-Costing",16=>"Dyeing Batch Approval",17=>"Other Purchase WO Approval",19=>"Gate Pass Activation Approval",20=>"yarn requisition approval",21=>"PI approval",22=>"All approval",23=>"GSD Entry Approval",24=>"Fabric Sales Order Approval",25=>"Sample Requisition Approval",26=>"Item Issue Requisiton",27=>"PI approval new",28=>"Service Booking AOP Approval",29=>" Service Booking For Knitting",30=>"Yarn Dyeing Work Order ",31=>"Sample Requisition with Booking",32=>"Embellishment Work Order Approval",33=>"Yarn Dyeing without Work Order", 34 => "Price Quotation V3", 35 => "Yarn Delivery Acknowledgement",36=> "Quick Costing Approval",37=> "Transfer Requisition Approval",38=> "Import Document Acceptance Approval",39=> "Commercial Office Note Approval",40=> "Transfer Requisition Approval for Sales Order",41=> "TNA Approval", 42 => 'Lab Test Approval',43 => 'Yarn WO Approval Sweater',44 => 'Topping Adding Stripping Recipe Entry',45 => 'Quick Costing Approval [WVN]',46 =>'Pre-Costing Approval [WVN]',47 =>'Sourcing Post Cost Approval',48 =>'Fabric Sales Order Approval V2',49 =>'CS Approval [General]',50 =>'CS Approval [Accessories]',51 =>"Trims order rcv Approval",52 =>"General Item Transfer Requisition Approval",53 =>"Sample Or Additional Yarn WO Approval",54 =>"Sample Requisition Acknowledge",55 =>"Sample Trims Booking Without Order",56 =>"Item Issue Requisition Approval V2",57 =>"CS Approval [Fabrics]");//,11=>"Yarn Delivery Approval" 18=>"Yarn Purchase Requisition Approval", Not used 

$sustainability_standard=array(1=>"GOTS",2=>"OCS",3=>"BCI",4=>"GRS",5=>"C2C",6=>"SUPIMA",7=>"Others",8=>"Conventional",9=>"IC2",10=>"CMIA",11=>"ORGANIC");

$wages_rate_var_for=array(1=>"Garments Cutting",2=>"Garments Finishing");
$bulletin_type_arr = array(1 => "RnD", 2 => "Marketing", 3 => "Budget", 4 => "Production");
$certification_arr=array(1=>'Organic/GRS',2=>'Organic',3=>'BCI');


$production_resource=array(1=>"SNL Auto",2=>"2T FL Auto",3=>"3T OL Manual",4=>"BH Auto",5=>"BA Auto",6=>"Snap BA",7=>"EH",8=>"SNDL Kansai",9=>"FEED of the ARM",10=>"Rib Scissoring",11=>"Ngai Sing-76",12=>"Ngai Sing-82",13=>"Ngai Sing-84",14=>"Ngai Sing-85",15=>"2NDL",16=>"Crease M/C",17=>"Fusing M/C",18=>"BTK",19=>"SNDL Edg Cutter",20=>"Flat Bed",21=>"SMOKE",22=>"LZ",23=>"J Stitch",40=>"Assistant Operator",41=>"Sewing QI",42=>"Eyelet",43=>"Table",44=>"Exam Table",45=>"DNL Lock Switch Auto" ,46=>"2NDL FL",47=>"4 OT OL",48=>"Supervisor",49=>"SNDL Z/Z",50=>"DNL Chain Stitch", 51=>"PICODING", 52=>"Pattern Sewer",53=>"Finishing Iron", 54=>"Finishing QI", 55=>"Poly Helper",56=>"Packing",57=>"Auto",58=>"3T OL Auto",59=>"4T OL Auto",60=>"5T OL",61=>"2T FL Manual",62=>"3T FL Auto",63=>"4T FL Auto",64=>"5T FL Auto",65=>"FLAT SEAM",66=>"Vertical Cutter",67=>"BT",68=>"Folding",69=>"Sewing Iron",70=>"Finishing Helper",71=>"2NDL Kansai",72=>"3NDL Kansai",73=>"PIPING Cutter",74=>"SNL Manual",75=>"DNL Lock Stitch Manual",76=>"3T FL Manual",77=>"4T FL Manual",78=>"5T FL Manual",79=>"4T OL Manual",80=>"OL Cutter",81=>"VT Auto",82=>"VT Manual",83=>"BA Manual",84=>"BH Manual",85=>"Snap",86=>"WRAPING",87=>"LATUS",88=>"HEAT SEAL",89=>"ZIGZAG",90=>"Hand Tag",91=>"1NDL FL",92=>"OL no thread",93=>"SNL[Chain STS]",94=>"KNS[SMOKE]",95=>"KNS",96=>"Velcro",97=>"Elastic Diviser",98=>"SNL-UBT",99=>"SNL-VT",100=>"2TOL",101=>"3ZZ",102=>"FL(FB)",103=>"FL(CB)",104=>"Ladder Stitch",105=>"SNDL Lock Stitch",106=>"OL Serging",107=>"APW",108=>"Pressing",109=>"Btn/Stc",110=>"C.C",111=>" MNDL- Kansai",112=>"F.Fitter",113=>"OL- Back latch",114=>"4TOL- Auto elastic cutter",115=>"FL- Right side cutter",116=>"FL- Left side cutter",117=>"FL- Left side cutter- Nero",118=>"FL- Belt join",119=>"SNL- cutter",120=>"Button hole",121=>"Button attach",122=>"Dise",123=>"Blind Stitch",124=>"Forming",125=>"Spot Tuck",126 => "1NDL PM",127 => "2NDL PM",128 => "Saddle stitch",129 => "Man",130 => "Machine",131=>"DNLS (an)",132=>"kansai (AGM)",133=>"H (LS)", 134=>"H (CS)",135=>"L/SETTER",136=>"FOA",137=>"FOA(SP)",138=>"T/COVER",139=>"5T OL Auto",140=>"Chain Stitch",141=>"Boxer", 142=>"Cuff Making", 143=>"Hanger Loop", 144=>"Template", 145=>"CM", 146=>"6TFL", 147=>"Sewing Helper",148=>"FLMC", 149=>"FLR", 150=>"FLRC", 151=>"FUA", 152=>"OL", 153=>"PC", 154=>"PMD", 155=>"PT", 156=>"SB", 157=>"SNCS", 158=>"SNLS", 159=>"DNLS", 160=>"DNCS", 161=>"DNLS [SB]", 162=>"COVERING STITCH", 163=>"KANSAI", 164=>"VERTICAL M/C", 165=>"FLAT LOCK", 166=>"SNAP BTN", 167=>"EYELET HOLE", 168=>"EYELET HOLE", 169=>"ZIG ZAG STITCH", 170=>"BOTTOM HEMMING", 171=>"PKT DECORATION", 172=>"PKT SETTER", 173=>"LOOP SETTER", 174=>"PKT RULLING");

$machine_category=array(1=>"Knitting",2=>"Dyeing",3=>"Printing",4=>"Finishing",5=>"Embroidery",6=>"Washing",7=>"Cutting",8=>"Sewing",9=>"CAD Machine",10=>"Vehicles",11=>"Others",12=>"ETP",13=>"Seamless",14=>"Maintenance",15=>"Ring Machine",16=>" Auto Cone Machine",17=>" Uniflex",18=>" Carding",19=>" Breaker Draw Frame",20=>" Lap Former",21=>" Comber",22=>" Finisher Draw Frame",23=>" Simplex",24=>" Spinning",25 =>"Trims/Accessories",26 =>"Insp",27 =>"Link",28 =>"Attachment",29 =>"Hole Button",30 =>"Iron",31 =>"Final",32 =>"Packing",33 =>"AOP",34 =>"WTP",35 =>"Lab",36 =>"Yarn Dyeing");
asort($machine_category);
$machine_type= array(1 => "Direct Production", 2 => "Auxiliary Production");
$depreciation_method=array(1=>"Straight-line",2=>"Reducing Balance");

$item_transfer_criteria=array(1=>"Company To Company",2=>"Store To Store",3=>"Style To Style",4=>"Order To Order",5=>"Item To Item",6=>"Order To Sample",7=>"Sample To Order",8=>"Sample To Sample");

$party_type=array(1=>"Buyer", 2=>"Subcontract", 3=>"Buyer/Subcontract", 4=>"Notifying Party", 5=>"Consignee", 6=>"Notifying/Consignee", 7=>"Client", 20=>"Buying Agent", 21=>"Buyer/Buying Agent", 22=>"Export LC Applicant", 23=>"LC Applicant/Buying Agent", 30=>"Developing Buyer", 80=>"Other Buyer", 90=>"Buyer/Supplier", 100=>"Also Notify Party");

$party_type_supplier = array(1 => "Supplier", 2 => "Yarn Supplier", 3 => "Dyes & Chemical Supplier", 4 => "Trims Supplier", 5 => "Accessories Supplier", 6 => "Machineries Supplier", 7 => "General Item", 8 => "Stationery Supplier", 9 => "Fabric Supplier", 20 => "Knit Subcontract", 21 => "Dyeing/Finishing Subcontract", 22 => "Garments Subcontract", 23 => "Embellishment Subcontract", 24 => "Fabric Washing Subcontract", 25 => "AOP Subcontract", 26 => "Lab Test Company", 30 => "C & F Agent", 31 => "Clearing Agent", 32 => "Forwarding Agent", 35 => "Transport Supplier", 36 => "Labor Contractor", 37 => "Civil Contractor", 38 => "Interior", 39 => "Other Contractor", 40 => "Indentor", 41 => "Inspection", 90 => "Buyer/Supplier", 91 => "Loan Party", 92 => "Vehicle Components", 93 => "Twisting", 94 => "Re-Waxing", 95 => "Grey Fabric Service Subcontract",96 => "Trims Sub-Contract");
asort($party_type_supplier);

$tna_task_catagory = array(1 => "General", 5 => "Sample Approval", 6 => "Lab Dip Approval", 7 => "Trims Approval", 8 => "Embellishment Approval", 9 => "Test Approval", 15 => "Purchase", 20 => "Material Receive", 25 => "Fabric Production", 26 => "Garments Production", 30 => "Inspection", 35 => "Export");
asort($tna_task_catagory);

$recipe_for=array(1=>"Sample",2=>"Bulk",3=>"Compound Color");
$supplier_nature=array(
1=>" Goods",
2 =>"Service",
3 =>"Both"
);
$fabric_typee=array(1=>"Open Width",2=>"Tubular",3=>"Needle Open");
$process_type=array(1=>"Main Process",2=>"Additional Process");
$account_type=array(1=>"CD A/C", 2=>"STD A/C", 3=>"OD A/C", 4=>"CC A/C", 5=>"BTB Margin A/C", 6=>"ERQ A/C", 7=>"Imp. LC Margin A/C", 8=>"BG Margin A/C", 9=>"ECC A/C", 10=>"PC A/C", 11=>"Advance A/C");
$core_business=array(1=>"Manufacturing",2=>"Trading",3=>"Service",4=>"Educational",5=>"Social Welfare");
$company_nature=array(1=>"Private Ltd",2=>"Public Ltd",3=>"Sole Tradership",4=>"Partnership");
$loan_type=array(0=>"Percent",1=>"Fixed");
$commercial_module = array(5 => "Garments Export Capacity", 6 => "Max BTB Limit", 7 => "Max PC Limit", 17 => "Possible Heads For BTB", 18 => "Export Invoice Rate", 19 => "Doc Monitoring Standard", 20 => "Internal File Source", 21 => "Attach Approved PI", 22 => "Export Invoice Qty Source", 23 => "After Goods Receive Data Source", 24 => "Yarn Purchase Order Controll", 25 => "PI Source BTB LC", 26 =>"Commission source at Export Invoice", 27 => "Export PI No From System", 28 => "Yarn Purchase Order Rate Control With Budget", 29=>"Commercial Office Note Signature Source",30=>"Control PI Sent for Approval Without SC/LC", 31=>"Control PI Entry After Last Ship Date", 32 =>"Sales Contract Entry Contract Number", 33 => "Export LC Rate");
$commission_source_at_export_invoice = array(1 => "Manual(Existing)", 2 =>"Pre-Cost");
$cost_heads=array(0=>"--Select--","Knitting Charge"=>"Knitting Charge","Fabric Dyeing Charge"=>"Fabric Dyeing Charge","Yarn Dyeing Charge"=>"Yarn Dyeing Charge","All Over Print Charge"=>"All Over Print Charge","Dyed Yarn Knit Charge"=>"Dyed Yarn Knit Charge","Stantering Charge"=>"Stantering Charge","Brush Peach Charge"=>"Brush Peach Charge","Washing Charge"=>"Washing Charge","Printing"=>"Printing","Embroidery"=>"Embroidery","Washing"=>"Washing");

$rate_for=array(1=>"Knitting",2=>"Warping",3=>"Sizing",4=>"Knotting/ Drawing",5=>"Weaving",10=>"Dying",20=>"Cutting",30=>"Sewing",35=>"Ironing",40=>"Finishing");


$cal_parameter=array(1=>"Sewing Thread",2=>"Carton",3=>"Carton Sticker",4=>"Blister Poly ",5=>"Elastic",6=>"Gum Tap",7=>"Tag Pin",8=>"Sequines", 9 => "Eyelet",10=>"Button GG",11=>"Button Gross",12=>"Embroidery Thread", 13 => "Carton Board");
$cm_cost_predefined_method=array(1=>"=((SMV*CPM)*Costing per + (SMV*CPM*Costing per)* Efficiency Wastage%)/Exchange Rate",2=>"(((SMV*CPM)*Costing per / Efficiency %)+((SMV*CPM)*Costing per / Efficiency %))/Exchange Rate",3=>"{(MCE/WD)/NFM)*MPL)}/[{(PHL)*WH}]*Costing Per/Exchange Rate",4=>"[((CPM/Efficiency%)*SMV*Costing Per)/Exchange Rate]");
$commercial_cost_predefined_method=array(1=>"Yarn+Trims+Fabric Purchase",2=>"On Selling Price",3=>"On Net Selling Price",4=>"Yarn+Trims+Fabric Purchase+Embellishment Cost",5=>"Fabric Purchase + Trims Cost + Embellishment Cost + Garments Wash + Lab Test + Inspection + CM Cost + Freight + Courier Cost + Certificate Cost + Design Cost + Studio Cost + Operating Expenses", 6 => "Fabric Purchase + Trims Cost + Embellishment Cost + Garments Wash + Lab Test + Inspection +  Freight + Courier Cost + Certificate Cost + Design Cost + Studio Cost + Operating Expenses");

$test_for = array(1 => "Garments", 2 => "Fabrics", 3 => "Trims",4=>"Yarn",5=>"Chemical");


$testing_category=array(1=>"Color Fastness",
                     2=>"Dimension & Appearance ",
                     3=>"Strength ",
                     4=>"Fabric Construction ",
                     5=>"Composition ",
                     6=>"Flammability ",
                     8=>"Fabric Performance ",
                     10=>"Garments Accessories ",
                     11=>"Stability/Appearance ",
                     12=>"Chemical Analysis",
                     13=>"Physicals",
                     14=>"Safety",
					15 => "Seam Slippag",
					16=>"Washing Performance"
                     );
// Library Module ends //



// Merchandising
$approval_status=array( 1=>"Submitted",2=>"Rejected",3=>"Approved",4=>"Cancelled",5=>"Re-Submitted");
$order_status=array(1=>"Confirmed",2=>"Projected");
$region=array(1=>"Asia",2=>"Africa",3=>"Australia",4=>"Antarctica",5=>"Europe",6=>"North America ",7=>"South America");
$packing=array(1=>"Solid Color Solid Size",2=>"Assort Color Solid Size",3=>"Solid Color Assort Size",4=>"Assort Color Assort Size");
$inquery_status_arr = array(1=>'Approved',2=>'Reject',3=>'Confirmed',4=>'Sample not Suitable',5=>'Price not workable',6=>'Waiting for comments',7=>'Submitted',8=>'Unsubmitted');

//$ship_mode=array(1=>"Air",2=>"Sea");
$cut_up_array=array(1=>"1st Cut-Off",2=>"2nd Cut-Off",3=>"3rd Cut-Off");
$product_dept = array(1 => "Mens", 2 => "Ladies", 3 => "Teenage-Girls", 4 => "Teenage-Boys", 5 => "Kids-Boys", 6 => "Infant", 7 => "Unisex", 8 => "Kids-Girls", 9 => "Baby", 10 => "Kids", 11 => "Women",12=>"Infant Boy",13=>"Infant Girls",14=>"Toddler Boys",15=>"Toddler Girls",16=>"New Born",17=>"Pet",18=>"CHILDREN",19=>"ACTIVE",20=>"ABM",21=>"NIGHTWEAR",22=>"Older girls",23=>"Girls",24=>"Older Boys",25=>"Boys",26=>"Mini Boyes",27=>"Mini Girls");
//$pord_dept=array(1=>"Menz",2=>"Ladies",3=>"Teen Age-Girls",4=>"Teen Age-Boys",5=>"Kids",6=>"Infant",7=>"Intimates");
$inquery_price_arr = array(1=>'Submit Price',2=>'Buyer Target Price');
$inquery_stage_arr = array(1=>'1st Quoted', 2=>'2nd Quoted', 3=>'3rd Quoted', 4=>'4th Quoted', 5=>'5th Quoted', 6=>'Limit Price', 7=>'Bottom Price', 8=>'Last Call', 9=>'Confirmed', 10=>'Best', 11=>'1st Target', 12=>'2nd Target', 13=>'3rd Target');
$shift_name=array(1=>"A",2=>"B",3=>"C");
$fabric_shade=array(1=>"A",2=>"B",3=>"C",4=>"D");
$country_type=array(1=>"General",2=>"Special");
$product_types=array(1=>"Brief",2=>"Bra",3=>"Top",4=>"Bottom",5=>"Boxer");
$feeder = array(1 => "Full Feeder", 2 => "Half Feeder");
$batch_type_arr=array(1=>"Bluk Batch",2=>"Trail Batch");
$wo_type_arr = array(1 => "Sample", 2 => "Bulk");
$product_category=array(1=>"Outwears",2=>"Lingerie",3=>"Sweater",4=>"Socks",5=>"Fabric",6=>"Top",7=>"Bottom");
$aop_qc_reject_type = array(1 =>"Design  Mistake",2 =>"Design Setting Out",3 => "Color Bleed", 4 => "Color Flashing", 5 =>"Print Color Shade", 6 =>'Print Overlapping',7 => 'Color Spot',8 =>'Sheading',9 =>'Fabric Ground Shade Deviation', 10 =>'Repeat Measurement',11 => 'Fabric Shrinkage', 12 =>'Fabric Side Curling',13 =>'Gsm Low',14 =>'Gsm High', 15 => 'Pin Hole', 16 =>'Excess GSM Hole & Shade Hole',17 => 'Repeat Mark',18 => 'Dia Variations', 19 => 'Bowing', 20 =>'Print Color Missing',21 =>'Print in Wrong side',22 =>'Print in Wrong Way', 23 =>'Pentration Variations', 24 => 'Others');

$garments_item=return_library_array("select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name","id","item_name");

$cm_cost_particular_arr = array(1=>'Knitting',2=>'Winding',3=>'Linking',4=>'All Attachment',5=>'Zipper Attachment',6=>'Epulate Attachment',7=>'Button Attachment',8=>'Mending',9=>'Trimming',10=>'Wash',11=>'Finishing',12=>'Fixed Cost');
$quality_label = array(1 => "Platinum", 2 => "Gold", 3 => "Silver",4=>"AQL 1.5",5=>"AQL 4",6=>"AQL 2.5",7=>"Platform");
$fbooking_order_nature = array(1 => "QEI", 2 => "QR", 3 => "ADVERT + QEI", 4 => "ADVERT + QR", 5 => "QEI + QR", 6 => "SPEED", 7 => "NOS", 8 => "ADVERT",9=>"QEI + QR + ADVERT",10=>"BLUE VIP",11=>"Block");
$composition=return_library_array("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name","id","composition_name");

$unit_of_measurement = array(1 => "Pcs", 2 => "Dzn", 3 => "Grs", 4 => "GG", 10 => "Mg", 11 => "Gm", 12 => "Kg", 13 => "Quintal", 14 => "Ton", 15 => "Lbs", 20 => "Km", 21 => "Hm", 22 => "Dm", 23 => "Mtr", 24 => "Dcm", 25 => "CM", 26 => "MM", 27 => "Yds", 28 => "Feet", 29 => "Inch", 30 => "CFT", 31 => "SFT", 40 => "Ltr", 41 => "ML", 50 => "Roll", 51 => "Coil", 52 => "Cone", 53 => "Bag", 54 => "Box", 55 => "Drum", 56 => "Bottle", 57 => "Pack", 58 => "Set", 59 => "Can", 60 => "Each", 61 => "Gallon", 62 => "Lachi", 63 => "Pair", 64 => "Lot", 65 => "Packet", 66 => "Pot", 67 => "Book", 68 => "Culind", 69 => "Rim", 70 => "Cft", 71 => "Syp", 72 => "K.V", 73 => "CU-M3", 74 => "Bundle", 75 => "Strip", 76 => "SQM", 77 => "Ounce", 78 => "Cylinder", 79 => "Course", 80 => "Sheet", 81 => "RFT", 82 => "Square Inch", 83 => "Carton", 84 => "Thane",85 => "Gross Yds", 86 => "Jar", 87 => "Reel");
$pord_dept=array(1=>"Mens",2=>"Ladies",3=>"Teen Age-Girls",4=>"Teen Age-Boys",5=>"Kids",6=>"Infant",7=>"Intimates");

//merchandise variable settings
$order_tracking_module = array(12 => "Sales Year started", 14 => "TNA Integrated", 15 => "Pre Costing : Profit Calculative", 18 => "Process Loss Method", 19 => "Consumtion Basis", 20 => "Copy Quotation", 21 => "Conversion Charge From Chart", 22 => "CM Cost Predefined Method (Pre-cost)", 23 => "Color From Library", 24 => "Yarn Dyeing Charge (In WO) from Chart", 25 => "Publish Shipment Date", 26 => "Material Control", 27 => "Commercial Cost Predefined Method-Pre-Costing", 28 => "Gmt Number repeat style", 29 => "Duplicate Ship Date", 30 => "Image Mandatory", 31 => "TNA Process type", 32 => "Po Update Period", 33 => "Po Receive Date", 34 => "Inquery ID Mandatory", 35 => "BOM Rate Source", 36 => "CM Cost Predefined Method (Price Quotation)", 37 => "Budget Validation", 38 => "S.F. Booking Before M.F. 100%", 39 => "Lab Test Rate Update", 40 => "Collar Cuff Percent", 41 => "Pre-cost Approval", 42 => "Report Date Catagory", 43 => "TNA Process Start Date", 44 => "Season Mandatory", 45 => "Excess Cut Source in Order Entry", 46 => "Allow Ship Date on Off Day", 47 => "Style & SMV Source/Combinations", 48 => "Default Fabric Nature", 49 => "Default Fabric Source", 50 => "BOM Page Setting", 51 => "Min Lead Time Control", 52 => "PO Entry Limit On Capacity", 53 => "Cost Control Source", 54 => "Efficiency Source For Pre-Cost", 55 => "Work Study Mapping", 56 => "Embellishment Budget On", 57 => "Currier Cost Predefined Method", 58 => "Commercial Cost Predefined Method-Price Quotation", 59 => "Fabric Source For AOP", 60 => "Yarn Issue Validation Based on Service Approval", 61 => "Price Quotation Approval", 62 => "Textile TNA Baseed On", 63 => "Sequence validation with Booking", 64 => "Sew Comp. and location mandatory in order entry", 65 => "Excess Cut % Level in Order Entry",66=>"Fabric Req. Qty. Source",67=>"Location Wise Financial Parameter" ,68=>"QC Cons. From",69=> "Yarn Dyeing Work Order Used",70=> "Knitting Charge Source",71=> "Fabric Ref Automation",72=>"Booking Rate and Supplier From",73=>"Fabric Booking Control With SC/LC",74=>"Sample delivery date calculation",75=>"Fabric Budget On",76=>"Budget Un-Approved Validation",77=>"Sample Style source",78=>"PO Entry Control With Pre-Costing Approval",79=>"Is use Sourcing Post Cost Sheet",80=>"Budget(V2) mandatory",81=>"Lab Test Budget Validation");


$pre_cost_approval=array(1=>"Electronic Approval",2=>"Manual Approval");
$capacity_exceed_level=array(1=>"Confirmed Order Qty-LC",2=>"Confirmed Order Value-LC",3=>"Confirmed Order Mint-LC",4=>"Proj & Conf. Order Qty-LC",5=>"Proj & Conf. Order Value-LC",6=>"Proj & Conf. Order Mint-LC",7=>"Confirmed Order Qty-Working",8=>"Confirmed Order Value-Working",9=>"Confirmed Order Mint-Working",10=>"Proj & Conf. Order Qty-Working",11=>"Proj & Conf. Order Value-Working",12=>"Proj & Conf. Order Mint-Working");
$process_loss_method=array(1=>"Markup Method",2=>"Margin method");
$embellishment_budget_on = array(1 => "Order Qty.", 2 => "Plan Cut Qty.");
$fabric_budget_on = array(1 => "Order Qty.", 2 => "Plan Cut Qty.");
$consumtion_basis=array(1=>"Cad Basis",2=>"Measurement Basis",3=>"Marker Basis");
//$wo_category = array(2=>"Knit Fabrics",3=>"Woven Fabrics",4=>"Accessories",13=>'Grey Fabric(Knitt)',14=>'Grey Fabric(woven)',12=>"Services");
$gmts_nature= array(1=>"Knit Garments",2=>"Woven Garments",3=>"Sweater");
$incoterm=array(1=>"FOB",2=>"CFR",3=>"CIF",4=>"FCA",5=>"CPT",6=>"EXW",7=>"FAS",8=>"CIP",9=>"DAF",10=>"DES",11=>"DEQ",12=>"DDU",13=>"DDP",14=>"DAP");
$fabric_source=array(1=>"Production",2=>"Purchase",3=>"Buyer Supplied",4=>"Stock");
$color_range = array(1 => "Dark Color", 2 => "Light Color", 3 => "Black Color", 4 => "White Color", 5 => "Average Color", 6 => "Melange", 7 => "Wash", 8 => "Scouring", 9 => "Extra Dark", 10 => "Medium Color", 11 => "Super Dark", 12 => "Royal color",13 => "Average-Double Dyeing",14 => "Dark - Double Dyeing",15 => "Black-Double Dyeing",16 => "Light-Double Dyeing",17 => "Medium-Double Dyeing",18 => "Extra Dark-Double Dyeing",19 => "Peroxide Wash",20 => "White / G.Mell / Scouring / H2o2",21 => "Green / Turquoise Color",22 => "Reactive Black",23 => "Light Royal",24 => "Dark Royal",25 => "Green / Turquoise Color- Double Dyeing",26 => "Reactive Black- Double Dyeing",27 => "Light Royal - Double Dyeing",28 => "Dark Royal - Double Dyeing",29 => "Light Melange",30 => "Dark Melange",31 => "Navy",32 => "Light Lemon");

$costing_per=array(1=>"For 1 Dzn",2=>"For 1 Pcs",3=>"For 2 Dzn",4=>"For 3 Dzn",5=>"For 4 Dzn");
$qccosting_per = array(1 => "Dzn", 2 => "Pcs");
$delay_for=array(1=>"Sample Approval Delay",2=>"Lab Dip Approval Delay",3=>"Trims Approval Delay",4=>"Yarn In-House Delay",5=>"Knitting Delay",6=>"Dyeing Delay",7=>"Fabric In-House Delay",8=>"Trims In-House Delay",9=>"Print/Emb Delay",10=>"Line Insufficient",11=>"Worker Insufficient",12=>"Bulk Prod. Approval Delay",13=>"Traget Falilure",14=>"Inspection Fail",15=>"Production Problem",16=>"Quality Problem");
//$body_part=array(1=>"Main Fabric",2=>"Collar",3=>"Culf",4=>"Rib",5=>"Hood",6=>"Pocketing",7=>"Bottom Rib",8=>"Sleeve",9=>"Back Part",10=>"Front Part");


$body_part=return_library_array("select id,body_part_full_name from  lib_body_part where status_active=1 and is_deleted=0 order by body_part_full_name","id","body_part_full_name");

asort($body_part);

$color_type=array(1=>"Solid",2=>"Stripe (Y/D)",3=>"Cross Over (Y/D)",4=>"Check (Y/D)",5=>"AOP",6=>"Solid (Y/D)",7=>"AOP Stripe",20=>"Florecent",25=>"Reactive",26=>"Melange",27=>"Marl",28=>"Burn Out",29=>"Gmts Dyeing",30=>"Cross Dyeing",31=>"Over Dyed",32=>"Space Y/D",33=>"Faulty Y/D",34=>"Solid Stripe",35=>"One Part Dye",36=>"Space Dyeing",37=>"Dope Dye", 38 => "INDIGO",39 => "Neon",40=>"RND Shade",41=>"Tie Dyed",42=>"RFD",43=>"Inject",44=>"Stripe [Y/D Melange]",45=>"AOP [Melange]",46=>"RFD Shade",47=>"Stripe [Y/D AOP]",48=>"Stripe [Y/D Burn-Out AOP]",49=>"AOP on RFD",50=>"Dip Dye",51=>"Solid[Discharge Able Dyeing]",52=>"Discharge Dyeing",53=>"Acid Wash",54=>"AOP [Pigment]",55=>"AOP [Reactive]",56=>"AOP [Discharge]",57=>"AOP [Disperse]",58=>"AOP [Acid Print]",59=>"AOP [Burn Out]",60=>"AOP [Digital Print]",61=>"Siro",62=>"Normal Wash",63=>"Solid [Y/D AOP]",64=>"Double Dyeing",65=>"Vertical Stripe",66=>"Solid[Dry hand feel]",67=>"AOP + Over Dyed");

$dyeing_sub_process = array(1=>"Demineralisation",10=>"Pretreatment-1",20=>"Neutralisation-1",21=>"Neutralisation-2",22=>"Neutralisation-3",23=>"Neutralisation-4",30=>"Biopolish",40=>"Dyestuff",50=>"Dyeing Bath",60=>"After Treatment 1",70=>"Color Remove",90=>"Other",91=>"Levelling 1",92=>"Finishing Process",93=>"Wash 1",94=>"Wash 2",95=>"Wash 3",96=>"Wash 4",97=>"Wash 5",98=>"Wash 6",99=>"After Treatment 2",100=>"After Treatment 3",101=>"After Treatment 4",102=>"Desizing",103=>"Enzyme",104=>"PP Bleach",105=>"Bleach",106=>"PP Bleach Neutral",107=>"Bleach Neutral",108=>"Cleaning",109=>"PP Neutral",110=>"Tint",111=>"Fixing",112=>"Softener",113=>"Acid Wash",114=>"Towel Bleach",115=>"Scouring",116=>"Resign Spray",117=>"Dyeing",118=>"Soaping 1",119=>"Silicon",120=>"Independent Process",121=>"Levelling 2",122=>"Levelling 3",123=>"Levelling 4",124=>"Pretreatment-2",125=>"PreTreatment-3", 126 => "PreTreatment-4",127=>"Soaping 2",128=>"Reduction Clear 1",129=>"Reduction Clear 2",126 => "Heat Setting", 127 => "Dye stuff 1", 128 => "Dye stuff 2", 129 => "Dyeing bath-1", 130 => "Dyeing bath-2", 131=> "Dyeing bath-3", 132=> "Stentering", 133=> "Squeezing", 134=> "Sliting", 135=> "AOP Wash",136=> "Compacting", 137 => "Singeing",138 => "Drying", 139 => "Special Finish");

$dose_base = array(1=>"GPLL",2=>"% on BW");

$excess_cut_per_level = array(1=>"Color Size Level",2=>"PO Level");
$fab_req_qty_source = array(1 => "Budget", 2 => "Fabric Booking");
//$conversion_cost_head_array=array(1=>"Knitting",2=>"Weaving",30=>"Yarn Dyeing",31=>"Fabric Dyeing",32=>"Tube Opening",33=>"Heat Setting",34=>"Stiching Back To Tube",35=>"All Over Printing",36=>"Stripe Printing",37=>"Cross Over Printing",60=>"Scouring",61=>"Color Dosing",62=>"Neutralization",63=>"Squeezing",64=>"Washing",65=>"Stentering",66=>"Compacting",67=>"Peach Finish",68=>"Brush",69=>"Peach+Brush",70=>"Heat+Peach",71=>"Peach+Brush+Heat",72=>"UV Prot",73=>"Odour Finish",74=>"Teflon Coating",75=>"Cool Touch",76=>"MM",77=>"Easy Care Finish",78=>"Water Repellent",79=>"Flame Resistant",80=>"Hydrophilics",81=>"Antistatic",82=>"Enzyme",83=>"Silicon", 84=>"Softener", 85=>"Brightener",86=>"Fixing/Binding Agent",87=>"Leveling Agent",101=>"Dyes & Chemical Cost");
$conversion_cost_head_array=array(
1=>"Knitting",
2=>"Weaving",
3=>"Collar and Cuff Knitting",
4=>"Feeder Stripe Knitting",
25=>"Dyeing+Enzyme+Silicon",
26=>"Dyeing+No Enzyme+Silicon",
30=>"Yarn Dyeing",
31=>"Fabric Dyeing",
32=>"Tube Opening/Sliting",
33=>"Heat Setting",
34=>"Stiching Back To Tube",
35=>"All Over Printing",
36=>"Stripe Printing",
37=>"Cross Over Printing",
38=>"Reversing",
39=>"Discharge Dyeing",
40=>"Discharge Print",
60=>"Scouring",
61=>"Color Dosing",
62=>"Neutralization",
63=>"Slitting/Squeezing",
64=>"Normal Wash",
65=>"Stentering",
66=>"Open Compacting",
67=>"Peach Finish",
68=>"Brush",
69=>"Peach+Brush",
70=>"Heat+Peach",
71=>"Peach+Brush+Heat",
72=>"UV Prot",
73=>"Odour Finish",
74=>"Teflon Coating",
75=>"Cool Touch",
76=>"MM",
77=>"Easy Care Finish",
78=>"Water Repellent",
79=>"Flame Resistant",
80=>"Hydrophilics",
81=>"Antistatic",
82=>"Enzyme",
83=>"Silicon Finish",
84=>"Softener",
85=>"Brightener",
86=>"Fixing/Binding Agent",
87=>"Leveling Agent",
88=>"Sueding",
89=>"Double Enzyme",
90=>"Tube Dryer",
91=>"Tube Compacting",
92=>"Carbon",
93=>"Trumble Dryer",
94=>"Singeing",
100=>"Back Sewing",
101=>"Dyes & Chemical Cost",
120=>"Cutting",
121=>"Gmts. Printing",
122=>"Gmt. Embroidery",
123=>"Gmts. Washing",
124=>"Sewing",
125=>"Calendering",
127=>"Shearing",
128=>"Combing",
129=>"Burn Out",
130=>"Iron",
131=>"Gmts Finishing",
132=>"Peroxide Wash",
133=>"Curing",
134=>"Twisting",
135=>"Scouring + Enzyme + Silicon",
136=>"Double Stentering",
137=>"Double Dyeing",
138=>"Dyeing Enzyme Silicon Boi-Wash",
139=>"Direct Dyeing + Enzyme + Silicon",
140=>"WASH ENZYME SILICON",
141=>"Steam Tumble Dry",
142=>"Wash Hydro Tumble Dry",
143=>"Steam Tumble + Open Compacting",
144=>"No Softener",
145=>"Fabric Turning",
146=>" Dyeing And Enzyme",
147=>"Re Dyeing",
148=>"Re Wash",
149=>"Re Match",
150=>"Re Compacting",
151=>"Re Stanter",
152=>"Poly",
153=>"Re Conning",
154=>"Fabric Embroidery",
155=>"Standard",
156=>"Compacting",
157=>"Standard and Compacting",
158=>"DYEING AND FINISHING",
159=>"Brush + Stenter + Compacting",
160=>"Brush + Softener + Stenter + Compacting",
161=>"Peach + Brush + Stenter + Compacting",
162=>"Dyeing + Enzyme + Finishing",
163=>"Dyeing + Finishing + Brush + Peach",
164=>"Stitching",
165=>"Air Turning",
166=>"Sliting",
167=>"Brush One Side",
168=>"Brush Both Side",
169=>"Anti Piling",
170=>"Special Finish",
171=>"Drying",
172=>"Tube Finish",
173=>"Squeezer",
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
202=> "Open Compacting + Print",
203=> "Others",
204=> "Hydro Mc",
205=> "Brush + Shearing",
206=> "Rotation",
207=> "Steaming",
208=> "Mercerization",
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
228=> "Auto Sewing",
229=> "Stenter + Tumble",
230=> "Half Dry",
231=> "Cool Wash",
232=> "Re Softener",
233=> "Hot Wash",
234=> "Tumble Dry",
235=> "Foil Print",
236=> "Puff Print",
237=> "Radium Print",
238=> "CPB Dyeing",
239=> "Double Silicon Wash",
240=> "Sanforizing Finish",
241=> "Edge Cutting",
242=> "Open Dryer",
243=>"Brush[With Finish]",
244=>"Sueding[With Finish]",
245=>"Peach[With Finish]",
246=>"Carbon[With Finish]",
247=>"Peach Brush[With Finish]",
248=>"Hydro Extractor Tumble",
249=>"Hydro Extractor",
250=>"Lasting Colors",
251=>"Wicking Test",
252=>"Hardener",
253=>"All Tests",
254=>"Edge Cutting + Compacting",
255=>"Edge Cutting+Heat Setting",
256=>"Damping",
257=>"Enzyme+Silicon+Dyeing+Finishing",
258=>"Enzyme+No Silicon+Hydrophilic softener use+Dyeing+No Compacting",
259=>"Silicon+Softener Wash+Finishing",
260=>"Heatset+Enzyme+Silicon+Dyeing+Finishing",
261=>"Heatset+Silicon+ Softener Wash+Finishing",
262=>"Singeing + Slitting",
263=>"Mercerizing",
264=>"Quilting",
265=>"Fabric Finishing",
266=>"Dyeing and Finishing [Single Part]",
267=>"Dyeing and Finishing [Double Part]",
268=>"Slitting + Singeing",
269=>"Pigment White Paste",
270=>"Pigment Neon Print",
271=>"Discharge Neon Print",
272=>"Afson Print",
273=>"Glitter+Neon+White+Pigment Print",
274=>"Jell Print",
275=>"Glow in the Dark Print",
276=>"Print Fabrics Wash",
277=>"Sueding + Stenter",
278=>"Tumble Dryer + Compacting",
279=>"Stenter Brush",
280=>"Re-Process",
281=>"Continuous Tumble",
282=>"Two Time Shearing",
283=>"Round Tumble",
284=>"Shearing-Three Time",
285=>"Stenter-Two Time",
286=>"Stenter-Three Time",
287=>"Dyeing and Finishing",
288=>"Brush + Stenter",
289=>"All Over Embroidery",
290=>"Egalizer",
291=>"Chain Dryer",
292=>"Grey Brush",
293=>"Steam Setting",
294=>"Stripping",
295=>"OBA",
296=>"Bleach",
297=>"Wicking Finish",
298=>"Dyeing+Dryer",
299=>"N/Wash+Finish+Heat Set",
300=>"D&F+Silicon+Peach+Stenter",

303=>"D&F+Enz",
304=>"D&F+Enz.+Silicon",
305=>"D&F+Disechargeable",
306=>"D&F+Enz+Bursh+Stenter",
307=>"N/Wash+Finish+Heat",
308=>"Two Part D&F+Enz+Silic",
309=>"D&F+peach+H set",
310=>"N/Wash+Finish+Silic+Brush",
311=>"Stipp+D&F+Enz.+Silicon",
312=>"D&F+Disechargeable+Enz.",
313=>"D&F+Enz.+Brush",
314=>"N/Wash+Finish+Enz",
315=>"N/Wash+Finish",
316=>"N/Wash+Dryer",
317=>"D&F+Enz+Peach+Stenter",
318=>"Slitting+Heat setting",
319=>"Slitting+Stenter+Tumble",
320=>"N/Wash+Finish+Silicon",
321=>"N/Wash+Finish+Silicon+Heat Set",
322=>"Slitting+Stenter+Compacting",
323=>"D&F+Enz.+Silicon+Peach+Stenter",
324=>"Stenter+Silicon Softner",
325=>"N/Wash+Finish+Enz.",
326=>"Squizer+Dyer",
327=>"D&F+D/Enzyme",
328=>"N/Wash+Finish+Enz.+Silic.",
329=>"D&F+Enz+Silicon+Brush+Stenter",
330=>"N/Wash+Finish+Brush+Stenter",
331=>"D&F+Enz+Silicon+Heatset",
332=>"D&F+Deschargable+Enz+Peach+Stanter",
333=>"D&F+Enz.+H.Set ",
334=>"Rematching",
335=>"Two Part D&F+Enz+Silic+Brush+Stenter",
336=>"Dyeing & Finish+Silicon",
337=>"Peach Stenter",
338=>"Brush+Compacting",
339=>"Re Stander+Compacting",
340=>"Two Part D&F+Silicon",
341=>"Dyer",
342=>"Two Part D&F+Enzyme+Silicon",
343=>"Re Tumble",
344=>"Two Part D&F+Enz+Silic+Peach+Brush+Stenter",
345=>"Two Part D&F+Enz+Peach+Brush+Stenter",
346=>"Two Part D&F+Enz+Silic+Peach+Stenter",
347=>"D&F+Enz.+H.Set",
348=>"Two Part D&F+Enz+Brush+Heatset",
349=>"Two Part D&F+Enz+Heatset",
350=>"Dyeing & Finish Heatset",
351=>"N/Wash+Finish+Silicon",
352=>"Dyeing & Finish+Brush+Stenter",
353=>"D&F+Enz+Brush+Stenter",
354=>"D&F+Dischargeable",
355=>"D&F+D/Enzyme+Silicon",
356=>"D&F+Double+En",
357=>"D&F+Double+Enz+Silicon",
358=>"D&F+Enz+Peach+Stenter +H.Set",
359=>"D&F+Enz+Peach+Stenter +H.Set+Siliting",
360=>"Two Part D&F+Enz+Heatset",
361=>"Two Part+ D&F+Double+Enz+Silicon",
362=>"D&F+Double+Enz+Silicon+H.Set",
363=>"Dyeing & Finish+Silicon +H.Set",
364=>"D&F+Enz+Brush+Stenter",
365=>"Squaring",
366=>"D&F+Enz+Silicon+Peach+H.Set",
367=>"Disecharge Print",
368=>"Rubber Print+Brush+Stenter",
369=>"D&F+Enz+Peach+Stenter+H.Set+Brush",
370=>"D&F+Disechargeable+Enz.",
371=>"D&F+Enz+Silicon+Brush+H Set",
372=>"N/Wash+Finish+Enz.+Brush",
373=>"D&F+Enz+Silicon+Heatset+Siliting",
374=>"Two Part Dyeing Dryer",
375=>"N/Wash+Finish+H.Set+Siliting",
376=>"D&F+Enz.+Brush+H Set",
377=>"Dyeing & Finish+Brush+peach+stenter",
378=>"Screen charge",
379=>"Stenter+Compacting+Softner",
380=>"Stenter+Brush+Stenter",
381=>"D&F+Disechargeable+Enz.+H Set",
382=>"N/Wash+Finish+Enz.+H set",
383=>"Stenter+Softner",
384=>"Y/D PK",
385=>"Two Part+D&F+Double+Enz",
386=>"D&F+Enz.+Taflon wash",
387=>"Dyeing&Finish+Peach",
388=>"N/Wash+Finish+Heat+Peach",
389=>"Grey Fabric Return",
390=>"Two Part D&F+Enzy+Brush+Stenter",
391=>"Two Part D&F+Enzyme",
392=>"C Print",
393=>"Full covarege Print",
394=>"CBR",
395=>"CBR RFD",
396=>"CWR",
397=>"Tumble Wash",
398=>"Single Part Dyeing [Reactive Dyeing]",
399=>"Single Part Dyeing [Direct Dyeing]",
400=>"Single Part Dyeing [Disperse Dyeing]",
401=>"Double Part Dyeing [Reactive + Disperse]",
402=>"Anti-Bacterial Finish",
403=>"Flame Retardant Finish",
404=>"Wicking/Moisture Finish",
405=>"Peach/Suding/Carbon With Finish",
406=>"RFD + Cool Wash",
407=>"Quick Dry",
408=>"LAMINATION (OUTER INNER)",
409=>"SOLID (BODY BINDING)",
410=>"LAMINATION"




 ////If need to add any process here,plz contact with Aziz

);
asort($conversion_cost_head_array);

$qc_template_item_arr=array(1=>"BTS",2=>"BTM");// Added Ref.By Kausar(Quick Costing)
$qc_template_wovenItem_arr = array(1 => "TOP", 2 => "BTM");
$priority_arr = array(1=>'High',2=>'Medium',3=>'Low');
$mandatory_subprocess=array(33=>"Heat Setting",34=>"Stiching Back To Tube",94=>"Singeing",60=>"Scouring",63=>"Slitting/Squeezing",65=>"Stentering",66=>"Open Compacting",78=>"Water Repellent",82=>"Enzyme",90=>"Tube Dryer",91=>"Tube Compacting");//need to consult with sir

$conversion_cost_type=array(1=>"Knitting",10=>"Yarn Dyeing",11=>"Dyeing",12=>"AOP",13=>"Wash",20=>"Finishing",21=>"Chemical Finish",22=>"Special Finishing",40=>"Dyes & Chemical ");
$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Gmts Dyeing",6=>"UV Print",99=>"Others");
$cost_heads_for_btb=array(1=>"Knitting",30=>"Yarn Dyeing",31=>"Fabric Dyeing",35=>"All Over Printing",75=>"Knit Fabric Purchase",78=>"Woven Fabric Purchase",101=>"Printing",102=>"Embroidery",103=>"Wash");//101 means 1, 102 means 2, 103 means 3,4=>"Feeder Stripe Knitting",64=>"Washing Charge",65=>"Stentering",68=>"Brush"
$sew_fin_woven_defect_array = array(1=>"BROKEN STC", 2=>"SKIP STC", 3=>"OPEN STC", 4=>"UN-EVEN STC", 5=>"POOR JOINT STC", 6=>"HI-LOW", 7=>"TENSION LOOSE /TIGHT", 8=>"PUCKERING / PLEAT", 9=>"DOWN STC", 10=>"SLANTED", 11=>"MISSING", 12=>"POOR SHAPE", 13=>"NEEDLE CUT", 14=>"SIZE MISTAKE", 15=>"REJECT", 16=>"FABRIC FAULT", 17=>"RUN OFF STITCH", 18=>"UNCUT  THREADS", 19=>"INCOMPLETE STC", 20=>"OVER STITCH", 21=>"OIL / DIRTY SPOT", 22=>"SHADING", 23=>"MISTAKE", 24=>"RAWEDGE", 25=>"RAWEDGE OUT", 26=>"VISIBLE", 27=>"WRONG THREAD",28=>"REVERSED");
$sew_fin_wash_check_array = array(1=>"OFF SHADE / SHADING", 2=>"DAMAGE", 3=>"POOR DRY PROCESS");
$sew_fin_measurment_check_array = array(1=>"WAIST", 2=>"SEAT / HIP", 3=>"THIGH", 4=>"INSEAM");
$npt_category = array(1 => "Cutting", 2 => "Merchandising", 3 => "Maintenance", 4 => "Production Floor", 5 => "Quality", 6 => "Store", 7 => "CAD", 8 => "Commercial", 9 => "HR And Admin", 10 => "IE And Techincal",11=>"Electrical",12=>"Embroidery & Template",13=>"Down",14=>"Production & Technical");
$npt_cause = array(1 => 'Others', 2 => 'Waiting for input', 3 => 'Wrong cut panel supply', 4 => 'Fusing delay', 5 => 'Cutting section capacity', 6 => 'Fabric delay', 7 => 'Accessories not in-house', 8 => 'Accessories delay', 9 => 'Print/Emb.', 10 => 'Size set delay', 11 => 'Order not ready', 12 => 'PP activity delay', 13 => 'M/C breakdown', 14 => 'M/C adjustment', 15 => 'M/C arrangement lacking', 16 => 'Wrong attachment', 17 => 'Bulk size set delay', 18 => 'Technical problem', 19 => 'Incapable manpower', 20 => 'Alteration issue', 21 => 'Quality not achieve', 22 => 'Accessories supply delay', 23 => 'Delay fabric supply to cutting', 24 => 'Wrong marker supply', 25 => 'Marker supply delay', 26 => 'BTB / TT delay', 27 => 'Late forwarder selection', 28 => 'Late raw material clearance', 29 => 'Theft of goods partils / full', 30 => 'Power failure', 31 => 'HR meeting (WPC) & others', 32 => 'Worker transport delay', 33 => 'Migration', 34 => 'Natural call to evacuate floor', 35 => 'Worker unrest', 36 => 'Operator absentisiom', 37 => 'Wrong method', 38 => 'Style change over', 39 => 'Wrong SMV calculation at pre-cost', 40 => 'Line balance', 41 => 'Quality problem', 42 => 'Power failure', 43 => 'Air pressure Problem', 44 => 'Others', 45 => 'Waiting for Input', 46 => 'Embroidery & Template  Capacity', 47 => 'Others', 48 => 'Waiting for Input', 49 => 'Down Capacity', 50 => 'Others', 51 => 'Needle break', 52 => 'TEMPLATE Others, CWS, EMB & Down M/C Problem', 53 => 'Needle break', 54 => 'Style Change over', 55 => 'Wrong method',56=>'Quality Prob');//


$emblishment_print_type_arr=array(1=>"Rubber",2=>"Glitter",3=>"Flock",4=>"Puff",5=>"High Density",6=>"Foil",7=>"Rubber+Foil",8=>"Rubber+Silver",9=>"Pigment",10=>"Rubber+Pearl",11=>"Rubber+Sugar",12=>"Transfer / Sel",13=>"Crack",14=>"Photo",15=>"Foil+Photo",16=>"Pigment+Stud",17=>"Rubber+Stud",18=>"Rubber+Glitter",19=>"Photo+Silicon",20=>"Rubber+Silicon",21=>"Rubber+Stud/Stone",22=>"Photo+Stud/Stone",23=>"Rubber+Flock",24=>"Photo+Flock",25=>"Discharge",26=>"Discharge+ Flock",	27=>"Discharge + Pigment",28=>"Pigment + Glitter",29=>"Pigment + Foil",30=>"Pigment+ Plastisol",31=>"Plastisol",32=>"Flou color",33=>"Fluo +Pigment",34=>"Photo + Pigment",35=>"Reverse",36=>"Reverse + Pigment",37=>"Aop",38=>"Burnout",39=>"Sublimation",40=>"Heat Press",41=>"Pigment + Rubber",42=>"Emboss",43=>"Leaser Print",44=>"Glow In Dark",45=>"Metallic",46=>"Pad Printing",47=>"Pigment/Rubber",48=>"Regular+Puff+Silver Foil",49=>"Foil + Glitter",50=>"Gel + Pigment + Flock",51=>"Applique",52=>"Rubber+High Density",53=>"Placement",54=>"High D + Foil",55=>"Silicon",56=>"Screen Print",57=>"Rubber-Label Print",58=>"Pigment-Label Print",59=>"Foil+Puff",60=>"Digital Printing",61=>"Flock+Pigment",62=>"HD Gel",63=>"Pigment Stone",64=>"Rubber Flock",65=>"Pigment + Crack print",66=>"Pigment + High-density Print",67=>"Crack + High-density Print",68=>"Pigment + Glitter + Foil",69=>"High Density + Gradient",70=>"Pigment High Raised Rubber",71=>"Eco Discharge + Gel",72=>"Cmyk Photo",73=>"Cmyk Discharge",74=>"Cmyk Pigment",75=>"Cmyk Rubber",76=>"Cmyk Foil",77=>"Rubber + Gel", 78 => "Reflective Print", 79 => "Gel",80=>"Stud",81=>"Semi Rubber",82=>"Afsan",83=>"Sticker print",84=>"Label Print",85=>"Holographic",86=>"3D Silicon and Discharge",87=>"Discharge + Rubber + Crack",88=>"Discharge HD Print",89=>"Glitter + Puff");

$emblishment_embroy_type=array(1=>"Applique",2=>"Plain",3=>"Sequence",4=>"Patch Label",5=>"Snail",6=>"3D",7=>"Back Pkt EMB",8=>"Cord",9=>"Boring",10=>"Terry Embroidery",11=>"Felt",12=>"Plain+Sequence");
$emblishment_wash_type=array(1=>"Normal",2=>"Pigment",3=>"Acid",4=>"PP Spray/Dz",5=>"Enzyme",6=>"Enzyme+Silicon",7=>"Grinding",8=>"Cold Dye",9=>"Tie Dye",10=>"Batik Dye",11=>"Deep Dye",12=>"P.P Spray + Bleach Wash",13=>"Enzyme + Bleach wash",14=>"Burnout Wash",15=>"Crinkle Wash",16=>"Direct dyeing Acid Wash",17=>"Spray Wash",18=>"Antique",19=>"Pigment Garments Dye",20=>"Sand Wash",21=>"Vintage Wash",22=>"Grinding + Garments Wash",23=>"Pigment Dye + Heavy Enzyme Wash",24=>"Double Enzyme",25=>"Peroxide Wash",26=>"Dyeing Enzyme Silicon Boi-Wash",27=>"Wash Hydro Tumble Dry",28=>"Dyeing And Enzyme",29=>"Re Wash",30=>"Stone Enzyme Wash",31=>"Caustic Wash",32=>"Bleach Wash",33=>"Tint Wash",34=>"Towel Bleach Wash",35=>"Cool Pigment Dye",36=>"Thermo-Chromatic Dye",37=>"Pigment Garments Dye+Acid Wash",38=>"Whisker",39=>"Hand Scraping",40=>"Tagging",41=>"Destroy",42=>"Enzyme wash",43=>"Garments wash",44=>"Rinse wash",45=>"Silicon wash",46=>"PP Sprey",47=>"3D",48=>"Tie Mark",49=>"Stone",50=>"White Pest",51=>"Neon Dye",52=>"Cool Dye",53=>"Reactive Dye",54=>"Discharable Dye",55=>"Pigment Dye",56=>"Knee Cut",57=>"Laser whisker",58=>"Laser Destroy",59=>"Potash",60=>"Cleaning",61=>"Desize",62=>"PP Rubbing",63=>"Garments Dye",64=>"Over Dye",65=>"Tumble Dry",66=>"Garments wash + Softener",67=>"Enz Stone Bleach Wash",68=>"Whiskers Chevron",69=>"Handsand",70=>"PP",71=>"Softener",72=>"Springsteen",73=>"Juice Wrld wash" ,74=>"Vert wash",75=>"Ranburne",76=>"Kennedy wash",77=>"Abanda wash/como destruction",78=>"Akon wash /Dean Destruction",79=>"Joy division Wash / Ozzy Destruction",80=>"Van Halen Destruction",81=>"Mid-wash with full body laser",82=>"Rip Wash",83=>"As Per Standard",84=>"Dark Wash",85=>"MID Wash",86=>"Light Wash");
$emblishment_spwork_type=array(1=>"Stone",2=>"Bow",3=>"Ribbon",4=>"Beeds",5=>"H/Press",6=>"Smocking",7=>"Scalloped Cutting",8=>"Quilting",9=>"Pintuck",10=>"Burn Out");
$emblishment_gmts_type=array(1=>"Tie Dyeing",2=>"Dip Dyeing",3=>"Spray Dyeing",4=>"Over dyeing",5=>"Cold dyeing",6=>"High white dyeing",7=>"Washable dyeing",8=>"Reverse dyeing",9=>"Top dyeing",10=>"Direct Dye and Acid Wash",11=>"Double Dyeing",12=>"Pigment Dyeing",13=>"Reactive Dyeing",14=>"Gel Dyeing", 15 => "Fluorescent Pigment Dyeing", 16 => "Direct Dye");
$emblishment_other_type_arr = array(1 => "Others");

$commission_particulars=array(1=>"Foreign",2=>"Local");
$commission_base_array=array(1=>" in Percentage",2=>"Per Pcs",3=>"Per Dzn");
$camarcial_items=array(1=>"LC Cost ",2=>"Port & Clearing",3=>"Transportation",4=>"All Together");
$size_color_sensitive=array(1=>"As per Gmts. Color",2=>"Size Sensitive",3=>"Contrast Color",4=>"Color & Size Sensitive");
$shipment_status = array(1 => "Full Pending", 2 => "Partial Delivery", 3 => "Full Delivery/Closed");

$pay_mode=array(1=>"Credit",2=>"Import",3=>"In House",4=>"Cash",5=>"Within Group");
$actual_cost_heads=array(1=>"Testing Cost",2=>"Freight Cost",3=>"Inspection Cost",4=>"Courier Cost",5=>"CM",6=>"Commercial");
$aop_nonor_fabric_source=array(1=>"Sample Booking", 2=>"Transfer From Order");
$breakdown_type=array(1=>"Matrix With Full Qty.",2=>"Matrix With Packing Ratio + Ctn Qty",3=>"Matrix With Packing Ratio + Gmts Qty",4=>"Matrix With Packing Ratio + Pack Qty");
$exCut_source=array(1=>"Manual",2=>"Slab",3=>"No-Need");
$qc_consumption_basis=array(1=>"Cad/Manual-CM",2=>"Measurement-CM",3=>"Cad/Manual-Inch",4=>"Measurement-Inch");
$short_booking_type=array(1=>"Additional ",2=>"Compensative",3=>"Compensative -Dia Change", 4 => "Compensative -On Return");
$short_division_array=array(1=>"Textile ",2=>"Garments");
//---------------------------------------------------------------------Start production Module Array------------------------------------------------------//
$cause_type=array(1=>'Disorder',2=>'Routine Maintenance',3=>'Job Not Available',4=>'Job Not Assigned',5=>'Operator Not Available',6=>'Worker Unrest',7=>'Off-Day',8=>'Material Not Available',9=>'M/C WASH',10=>'Batch Preparation Late',11=>'Batch Not Available',12=>'Heat Set',13=>'Sewing',14=>'Breakdown Maintenance',15=>'Trolley',16=>'Electrical Problem',17=>'Program Change',18=>'Lycra Change',19=>'Problematic Yarn',20=>'Sample',21=>'Mechanical Work',22=>'Machine Disabled',23=>'Design Change',24=>'Design Program',25=>'Machine Servicing',26=>'Yarn Store delivery problem',27=>'Yarn Nil',28=>'Loose Yarn use', 29 => 'Mechanical problem', 30 => 'Helper short', 31 => 'Meeting', 32 => 'Namaj', 33 => 'M/C cleaning', 34 => 'Dust cleaning',50=>'Others', 51 => 'No Program', 52 => 'Mix Lot', 53 => 'Re-coning Yarn', 54 => 'Slub Yarn', 55 => 'Maintenance Problem', 56 => 'Operator Absent', 57=> 'Operator in Leave');
$production_type=array(1=>"Cutting",2=>"Printing",3=>"Print Received",4=>"Sweing In",5=>"Sewing Out",6=>"Finish Input",7=>"Iron Output",8=>"Gmts Finish",9=>"Cutting Delivery",10=>"Finish Garments Order to Order transfer",11=>"Poly Entry",
12=>"Sewing Line input",13=>"Sewing Line Output",14=>"Garments Finishing Delivery Entry",40 => "Plan Cut",50 => "Bundle Issue to Knitting Floor", 51 => "Bundle Receive from Knitting Floor", 52 => "Knitting QC", 53 => "Bundle issue to Linking ", 54 => "Bundle receive in Linking", 55 => "Bundle Wise Linking Input", 56 => "Bundle Wise Linking Output", 57 => "Delivery to Wash", 58 => "Receive in Wash", 59 => "Batch Creation for Wash", 60 => "Recipe for Wash", 61 => "Wash Chemical Issue Requisition", 62 => "Wash Production Entry (QC Passed)", 63 => "Embellishment Issue", 64 => "Embellishment Receive", 65=> "Re-linking", 66 => "Special Operation", 67 => "Iron entry", 68 => "Poly entry", 69 => "Packing and Finishing", 70 => "Final Inspection", 71 => "Ex-factory", 72 => "Operation wise entry", 73 => "Linking QC", 74 => "Lot Ratio", 75 => "Linking Operation Track", 76 => "Bundle Issue To First Inspection", 77 => "Bundle Receive From First Inspection",78=>"Bundle Issue To Wash",79=>"Bundle Receive in Wash",80=>"Woven Finishing Entry", 81=>"Finish Garments Receive Entry", 82=>"Finish Garments Issue Entry", 83=>"Finish Garments Issue Return");

$production_type_sweater=array(1=>"Kniting Complete",3=>"Wash Complete",4=>"Linking Complete",5=>"Sewing Complete",11=>"Attachment Complete",100=>"First Inspection",111=>"Trimming Complete",112=>"Mending Complete",113=>"Get Up Complete",114=>"PQC Complete");

$ltb_btb = array(1 => 'BTB', 2 => 'LTB');
$batch_for=array(1=>"Fabric Dyeing",2=>"Yarn Dyeing",3=>"Trims Dyeing");
$batch_against = array(1 => "Buyer Order", 2 => "Re-Dyeing", 3 => "Sample", 4 => "External", 5 => "Without Booking", 6 => "Gmts Wash", 7 => "Gmts Dyeing", 8 => "Fabric Wash", 9 => "Without Job", 10 => "Gmts Printing", 11 => "Re-Wash", 12 => "Return-Re-Wash");
$inspection_status=array(1=>"Passed",2=>"Re- Check",3=>"Failed", 4 => "2nd Re_check", 5 => "3rd Re_check");
$inspection_cause=array(1=>"Major",2=>"Minor",3=>"Acceptable");
$loading_unloading=array(1=>'Loading',2=>'Un-loading');
$dyeing_result = array(1 => 'Shade Matched', 2 => 'Re-Dyeing Needed', 3 => 'Fabric Damaged', 4 => 'Incomplete/Running', 5 => 'Under Trial', 6 => 'Re-Wash Needed',11 => 'Complete',12 => 'Next process Stentering',13 => 'Next process Dryer',14 => 'Next process Compacting',15 => 'Next process Brush',16 => 'Next process Peach',17 => 'Waiting for Fastness',18 => 'Waiting for Shrinkage',19 => 'Waiting for Decision',
100 => 'Others');
$next_process_arr=array(1=>"Next Process Stentering",2=>"Next Process Drying",3=>"Next Process Compacting",4=>"Next Process Brush",5=>"Next Process Peach");
$dyeing_type_arr = array(1 => 'Exhaust Dyeing', 2 => 'CPB Dyeing');

/*$dyeing_method=array(1=>"Black-B Process",10=>"Vacilis Process",20=>"Turquise (80-95-80)<1",30=>"Turquise (80-95-80)>1",40=>"All 60&#8451 Normal",50=>"All 60&#8451 Critical",60=>"40-60 Process",70=>"Polyestar Process",80=>"White Process",90=>"Viscose Process",100=>"CVC/PC Double Part (Vacilis Process)",110=>"CVC/PC Double Part (Separate Reduction Process)", 120 => "APC Process");*/
$dyeing_method = array(1 => "Black-B Process", 2 => "100 % Cotton S/J Fabric Black Color Dyeing Process", 3 => "100 % Cotton Terry Fabric Black Color Dyeing Process", 4 => "100 % Cotton Fleece Fabric Black Color Dyeing Process", 5 => "100 % Cotton PK/Rib Fabric Black Color Dyeing Process", 6 => "100 % Cotton S/J Fabric Light Color Dyeing Process", 7 => "100 % Cotton Terry Fabric Light Color Dyeing Process", 8 => "100 % Cotton Fleece Fabric Light Color Dyeing Process", 9 => "100 % Cotton PK/Rib Fabric Light Color Dyeing Process", 10 => "Vacilis Process", 11 => "100 % Cotton S/J Fabric Dark Color Dyeing Process", 12 => "100 % Cotton Terry Fabric Dark Color Dyeing Process", 13 => "100 % Cotton Fleece Fabric Dark Color Dyeing Process", 14 => "100 % Cotton PK/Rib Fabric Dark Color Dyeing Process", 15 => "100 % Cotton S/J Fabric Critical Color Migration Dyeing Process", 16 => "100 % Cotton Terry Fabric Critical Color Migration Dyeing Process", 17 => "100 % Cotton Fleece Fabric Critical Color Migration Dyeing Process", 18 => "100 % Cotton PK/Rib Fabric Critical Color Migration Dyeing Process", 19 => "100 % Cotton S/J Fabric Turquoise Color Dyeing Process", 20 => "Turquise (80-95-80)<1", 21 => "100 % Cotton Terry Fabric Turquoise Color Dyeing Process", 22 => "100 % Cotton Fleece Fabric Turquoise Color Dyeing Process", 23 => " 100 % Cotton PK/Rib Fabric Turquoise Color Dyeing Process", 24 => "Ly S/J Fabric Dark Color Dyeing Process", 25 => "Ly S/J Fabric Light & Critical Color Dyeing Process", 26 => "Viscose Fabric Dyeing Process", 27 => "CVC (Double Part) Light Color Dyeing Process", 28 => "CVC (Double Part) Dark Color Dyeing Process", 29 => "100 % Cotton S/J Fabric White Color Dyeing Process", 30 => "Turquise (80-95-80)>1", 31 => "100 % Cotton Terry Fabric White Color Dyeing Process", 32 => "100 % Cotton Fleece Fabric White Color Dyeing Process", 33 => "100 % Cotton PK/Rib Fabric White Color Dyeing Process", 34 => "100 % Cotton S/J Fabric Short Dyeing Process", 35 => "100 % Cotton Terry Fabric Short Dyeing Process", 36 => "100 % Cotton Fleece Fabric Short Dyeing Process", 37 => "100 % Cotton PK/Rib Fabric Short Dyeing Process", 38 => "100 % Cotton S/J Fabric Normal Wash Process", 39 => "100 % Cotton Terry Normal Wash Process", 40 => "All 60&#8451 Normal", 41 => "100 % Cotton Fleece Fabric Normal Wash Process", 42 => "100 % Cotton PK/Rib Fabric Normal Wash Process", 43 => "100 % Cotton S/J Fabric Enzyme Wash Process.", 44 => "100 % Cotton Terry Enzyme Wash Process.", 45 => "100 % Cotton Fleece Fabric Enzyme Wash Process.", 46 => "100 % Cotton PK/Rib Fabric Enzyme Wash Process.", 50 => "All 60&#8451 Critical", 60 => "40-60 Process", 70 => "Polyestar Process", 80 => "White Process", 90 => "Viscose Process", 100 => "CVC/PC Double Part (Vacilis Process)", 110 => "CVC/PC Double Part (Separate Reduction Process)", 120 => "APC Process",130 => "ETP and WTP", 140 => "Bleach and Continuous Wash", 150 => "Gas Boiler", 160 => "Lab", 170 => "Before Brush", 171 => "After Brush", 172 => "Before Peach", 173 => "After Peach", 174 => "Peach AOP", 175 => "Re-Process", 176 => "Softner", 177 => "Machine Wash", 178 => "Garments Wash", 179 => "Dyeing Finishing");
$batch_status_array=array(0=>"Incomplete", 1=>"Complete");

$worker_type=array(1=>"Salary Based Worker",2=>"Piece Rate Worker");
$piece_rate_wq_limit_arr=array(1=>"Up to Order Qty",2=>"Up to Plan Cut Qty");
$fabric_type_for_dyeing=array(1=>'Cotton',2=>'Polyster',3=>'Lycra',4=>'Both Part',5=>'White',6=>'Wash',7=>'Melange',8=>'Viscose',9=>'CVC 1 Part',10=>'Scouring',11=>'AOP Wash',12=>'Y/D Wash');
$inspected_by_arr=array(1=>'Buyer',2=>'3rd Party',3=>'Self');
$validation_type=array(1=>"Order Wise",2=>"Country Wise");
$defect_type=array(1=>"Alter",2=>"Spot",3=>"Reject");
$sew_fin_alter_defect_type = array(1 => "Fab Fault/ Colour Variation", 2 => "Run of seam", 3 => "Open Seam", 4 => "Skip Stitch", 5 => "Uneven Top Stitch", 6 => "Broken Stitch", 7 => "Loose Stitch", 8 => "Irregular Stitch", 9 => "Puckering", 10 => "Label Wrong/Mistake", 11 => "Slanted At Label", 12 => "Rawadge", 13 => "Tack Missing", 14 => "Tack Position Wrong", 15 => "Up Down", 16 => "Label Missing", 17 => "Shading", 18 => "Pleat", 19 => "Twisting", 20 => "Irregular Gathering", 21 => "Uncut Thread", 22 => "Button Missing", 23 => "Button Position Wrong", 24 => "Print Defect", 25 => "Poor Shape", 26 => "Yarn Contamination", 27 => "Slub", 28 => "Others", 29 => "Seam Reversed", 30 => "Needle Mark", 31 => "Bad Ten shire", 32 => "Over Stitch", 33 => "Incorrect SPI", 34 => "Uneven Seam Allowance", 35 => "Crocked Label", 36 => "Joint/Gathering Stitch", 37 => "Uneven Shape", 38 => "Cut Stitch", 39 => "Thread Missing", 40 => "Hole", 41 => "OPEN STC", 42 => "Hem Hi-Low", 43 => "TENSION LOOSE/TIGHT", 44 => "Down Stitch", 45 => "Needle Cut", 46 => "SIZE MISTAKE", 47=> "REJECT", 48 => "RUN OFF STC", 49 => "Incomplete Stitch", 50 => "WAIST / CHEST", 51 => "HIP / SWEEP", 52 => "THIGH / F/BK LENGTH", 53 => "INSEAM / SLV LENGTH", 54 => "Point Up-Down", 55 => "Side Slit up-Down", 56 => "Slanted at Placket", 57 => "Tack Revers", 58 => "Hole Damage", 59 => "Measurement Problem", 60 => "Eyelet Problem", 61 => "Zipper Problem", 62 => "Poor Iron", 63 => "Scissor Cut/Cut Damage",64 => "Slanted at Loop",65 => "Slanted",66 => "MISSING",67=>"OIL /DIRTY SPOT",68=>"MISTAKE",69=>"UN-EVEN STC",70=>"Uneven Hem",71=>"Twisting",72=>"Emb Placement Wrong",73=>"Glue Mark",74=>"Process Missing",75=>"Both Sleeve Up-Down",76=>"Pull Yarn",77=>"Drawstring Up-Down",78=>"Cracking",79=>"Waviness",80=>"Both Shoulder Up-Down",81=>"Label Position Wrong",82=>"Single Stitch",83=>"Print/HTL Placement Wrong",84=>"Loop Missing",85=>"Belt Missing",86=>"Crease Mark",87=>"Tal Part",88=>"Patta",89=>"Looseness",90=>"Missing Yarn",91=>"Knot",92=>"Roping",93=>"Bartack Placement Wrong",94=>"Drawstring Cap Open",95=>"Pocket Uneven",96=>"V-Tack Missing",97=>"V-Tack Slanted",98=>"Embroidary Broken",99=>"Shiny Mark");
$sew_fin_spot_defect_type=array(1=>"Dirty Stain",2=>"Oil Stain");

$cutting_qc_reject_type=array(1=>"Crease Mark",2=>"Dirty Spot",3=>"Hole",4=>"Knitting Defect",5=>"Dyeing Spot",6=>'Others', 7=> 'Stick Mark',8=>'Miss yarn',9=>'Contamination',10=>'Slub',11=>'Oil Spot',12=>'Needle Line',13=>'Needle Hole',14=>'Lycra Out',15=>'Dia Mark',16=>'Knot',17=>'Miss Cut',18=>'Over Compusser',19=>'Un-Even Dyenig',20=>'Patta',21=>'Loop Out', 22 => 'Grease Spot', 23 => 'Thick and Thin', 24 => 'Gsm Cut', 25 => 'Fabric Join', 26 => 'Reject For Dia Short', 27 => 'Crimple Mark', 28 => 'Broken', 29 => 'Marker Line', 30 => 'Shape Uneven', 31 => 'Numb Spot', 32 => 'Miss Print', 33=> 'Color Yarn', 34=> 'Yellow Mark', 35=> 'Shade Bar', 36=> 'Loose Warp', 37=> 'Thick Yarn', 38=> 'Stop Mark', 39=> 'End Out', 40=> 'Running Shade', 41=> 'Self Edge', 42=> 'Niddle Mark', 43=> 'Spot');

asort($cutting_qc_reject_type);

$upto_receive_batch=array(1=>'Heat setting',2=>'Dyeing',3=>'Slitting/Squeezing',4=>'Stentering',5=>'Drying',6=>'Special Finish',7=>'Compacting',8=>'Singeing',9=>'De Oiling',10=>'Dry Slitting');
$trims_section = array(1 => "Elastic", 2 => "Gum Tape", 3 => "Label", 4 => "Offset Print", 5 => "Poly", 6 => "Screen Print", 7 => "Sewing Thread", 8 => "Twill Tape", 9 => "Drawstring", 10 => "Yarn Dyeing", 11=> "All Over Print", 12 => "Embroidery", 13 => "Hanger", 14 => "Carton", 15 => "Twisting", 16 => "Doubling", 17 => "Price Ticket", 18 => "Paper", 19 => "Tipping", 20 => "Dye Cut", 21 => "Button", 22 => "Fabric",23 => "Tape", 24 => "Others",25=>"HTP");
//---------------------------------------------------------------------Start production Module Array END --------------------------------------------------//



//---------------------------------------------------------------------Start Commercial Module Array------------------------------------------------------//
$source=array(1=>"Abroad",2=>"EPZ",3=>"Non-EPZ");
$pi_basis=array(1=>"Work Order Based",2=>"Independent",3=>"Sales Order",4=>"Purchase Contract");
$wo_basis=array(1=>"Requisition Based",2=>"Independent",3=>"Buyer PO");
$lc_basis=array(1=>"PI Basis",2=>"Independent");
$convertible_to_lc=array(1=>"LC/SC",2=>"No",3=>"Finance");
$pay_term=array(1=>"At Sight",2=>"Usance",3=>"Cash In Advance",4=>"Open Account", 5 => "Block Order", 6=>"A/C Payee Chaque", 7=>"Cash on Delivery");
$shipment_mode=array(1=>"Sea",2=>"Air",3=>"Road",4=>"Train",5=>"Sea/Air",6=>"Road/Air",7=>"Courier",8 =>"Sea/Air/Road/Courier");
$extend_shipment_mode=array(1=>"Sea",2=>"Sea with discount",3=>"Air",4=>"Air with discount",5=>"Sea & Air");
$contract_source=array(1=>"Foreign",2=>"Inland");

$dyedType=array(1=>'Dyed Yarn',2=>'Non Dyed Yarn');


$yarn_type_for_entry=array(1=>"Carded",2=>"Combed",3=>"Compact",4=>"Polyester",5=>"CVC",6=>"PC",7=>"Melange",8=>"Micro Poly",9=>"Rottor",10=>"Slub",11=>"Spandex",12=>"Viscose",13=>"Modal Cotton",14=>"BCI",15=>"Modal",16=>"Semi Combed",17=>"Special",18=>"Cotton Linen",19=>"Pima",20=>"Su-Pima",21=>"Lurex",22=>"PV",23=>"Tencel",24=>"Excel/Linen",25=>"CV",26=>"CVC Slub",27=>"Pmax",28=>"Mercerize",29=>"Organic",30=>"Twist",31=>"Melange Slub",32=>"Melange Neps",33=>"Neps",34=>"Ctn Melange",35=>"Inject",36=>"Cotton Lurex",37=>"Melange Lurex",38=>"Viscos/Linen",39=>"Vortex",40=>"Polyester/Linen",41=>"CB Slub",42=>"PC Slub",43=>"Carded Slub",44=>"Org-Melange",45=>"PVC",46=>"Acrylic",47=>"Spun",48=>"Viscose-Wool",49=>"Linen-Tencel",50=>"Viscose Melange",51=>"Poly Filament",52=>"Spun Poly",53=>"Ring Spun",54=>"Poly Coolmax", 55=>"Poly HScool", 56=>"Poly Thermolit", 57=>"Poly Trevira", 58=>"Poly CD Yarn", 59=>"Cambric Viscose", 60=>"Ring Card", 61=>"CVC Melange", 62=>"PC Melange", 63=>"Modal Linen", 64=>"Siro", 65=>"Viscose Slub", 66=>"CPV ", 67=>"VC", 68=>"Cotton-Tencil", 69=>"Cotton-Rayon", 70=>"Siro Slub", 71=>"Inject Slub Melange",72=>"Pima Melange",73=>"Triblend",74=>"Space Slub",75=>"Carded Ring Spun",76=>"Combed Slub",77=>"Recycle",78=>"Pina ",79=>"Banana",80=>"Eco Fresh",81=>"VP",82=>"Lenzing",83=>"Combed Compact",84=>"COMBED- CONTRA FREE",85=>"COMFORJET",86=>"Carded Contra Free",87=>"Carded Contra Control",88=>"Inject Slub",89=>"CB Compact Contra Free",90=>"MVS",91=>"Cupro",92=>"CREPE",93=>"NYLON",94=>"Charcoal Mel",95=>"VPC",96=>"Combed Vortex",97=>"Carded-S Twist",98=>"Rubber Thread",99=>"CPL",100=>"PVM",101=>"Organic Carded",102=>"Organic Combed",103=>"Carded BCI",104=>"Combed BCI",105=>"Carded Slub BCI",106=>"Combed Slub BCI",107=>"Carded Organic BCI",108=>"Combed Organic BCI",109=>"Carded Slub Organic BCI",110=>"Combed Slub Organic BCI",111=>"Grey Melange",112=>"Grey Melange Slub",113=>"Organic Melange",114=>"Carded Grey Melange",115=>"Carded Grey Melange Slub",116=>"Carded Grey Melange Organic",117=>"Combed Grey Melange",118=>"Combed Grey Melange Slub",119=>"Combed Grey Melange Organic",120=>"100% Viscose",121=>"Viscose-Acrylic",122=>"Organic Carded Slub",123=>"Combed Organic Slub",124=>"Full Dull",125=>"Semi Dull",126=>"Autocoro",127=>"Covered",128=>"Carded Open End",129=>"Marvel melange",130=>"OE CVC",131=>"OE",132=>"EM",133=>"PCV",134=>"DM",135=>"CVC Dope Dyed",136=>"Polyester Black",137=>"Suprima",138=>"Compact Carded",139=>"Grey Melange BCI",140=>"Snow",141=>"Marble Heather",142=>"Space Dyed",143=>"Neon Space",144=>"Creek Heather",145=>"Organic Slub",146=>"Organic Melange Slub",147=>"Long Slub",148=>"Combed Gassed Mecerised",149=>"Organic Combed Slub",150=>"Organic Carded Melange",151=>"Organic Combed Melange",152=>"Organic Compact",153=>"Organic Vortex",154=>"Organic Compact Vortex",155=>"Organic Combed Vortex",156=>"Organic Carded Vortex",157=>"Organic CVC",158=>"Organic CVC Slub",159=>"Organic PC",160=>"Organic PC Slub",161=>"Organic CV",162=>"Organic CV Slub",163=>"Compact Combed",164=>"Combed Slub Organic",165=>"Combed Weaving",166=>"Carded Weaving",167=>"Grey Melange Slub BCI",168=>"Contra Free",169=>"Carded Ring Spun",170=>"Mohair Melange",171=>"Linen",172=>"VP Slub",173=>"Melange Combed",174=>"Gassed Mercerized Combed",175=>"Rayon",176=>"BCI Slub",177=>"BCI Inject",178=>"Coolmax",179=>"Black Polyester",180=>"Dope Dyed Polyester",181=>"Green Polyester",182=>"Super Combed",183=>"Super Carded",184=>"Recycle CVC",185=>"Glitter",186=>"Viscose-Glitter",187=>"Combed Compact Contra Free",188=>"Black Spandex",189=>"Compact Slub",190=>"BCI-CVC",191=>"BCI-PC",192=>"Siro Compact",193=>"Carded Weaving Compact",194=>"Combed Weaving Compact",195=>"Core Spandex",196=>"Ring Combed",197=>"S-Twist",198=>"Filament",199=>"Combed CVC",200=>"Twist Slub",201=>"Dope Dyed Yellow",202=>"Neppy",203=>"PC Siro",204=>"CVC Black",205=>"Monofilament",206=>"OE Contra Free",207=>"Long Staple Cotton",208=>"Blended Yarn",209=>"CVC GRAPE WINE",210=>"CVC Navy",211=>"Carded Contra Free Slub", 212 =>"", 213 => "X-Static", 214 => "White Polyester", 215 => "Cotton-Wool", 216 => "Air Covered", 217 => "Invista", 218 => "Combed Compact Slub", 219 => "Semi Dull[NIM]", 220 => "Semi Dull [SIM]",221 =>"Semi Dull [LIM]", 222 =>"Polyester Navy", 223 =>"Chennil", 224 =>"PC (Moroccan Blue)", 225 =>"PC (Mid Grey M�lange)", 226 =>"PC (Cameo Pink)", 227 =>"PC (Violet Ice)", 228 =>"PC (Navy Blue)", 229 =>"Soft Vortex", 230 =>"Ecru Melange Slub",231 =>"Melange Vortex",232 =>"Fully Drawn yarn",233 =>"Draw Texturing Yarn",234 =>"RING POLYESTER",235 =>"Vortex Contra Free",236 =>"CMIA",237 =>"Anthra Mel",238 =>"Anthra Mel Slub",239 =>"Ring Slub",240=>"Core Cotton",241=>"Modal Cotton Viscose",242=>"Bamboo Cotton",243=>"Combed PC",244=>"Vortex BCI",245=>"Heat Tech",246=>"Cima",247=>"GOTS",248=>"Ecru Melange",249=>"Anthra Melange",250=>"Contra Control",251=>"Suprima Combed",252=>"Combed Contra Free Slub",253=>"Bright",254=>"Inject White",255=>"Inject Black",256=>"Melange[OE]",257=>"Fome",258=>"Z-Twist",259=>"DSN",260=>"Carded CVC",261=>"Cotton Modal",262=>"MID MARLE",263=>"Cotton Modal Viscose Melange",264=>"CH",265=>"Dope Dyed Green",266=>"Inject Reverse Flack",267=>"Organic Conta Free",268=>"Thermolite PC",269=>"Lyocell",270=>"Recycle-Vortex",271=>"Elastomeric",272=>"Siro Snow",273=>"Project COMBED",274=>"CPM",275=>"TP",276=>"Cotton Slub",277=>"PC Vortex",278=>"Ecru Melange Neps",279=>"OE PC",280=>"Modal Poly",281=>"Snow Marl",282=>"CVC Snow Marl",283=>"CVC Blue Snow Marl",284=>"COMBED SIRO",285=>"Combed Contamination Control",286=>"Combed Compact Contamination Control",287=>"Siro Melange",288=>"Carded Ring",289=>"Roving Grindle Melange",290=>"CB",291=>"Cotton Melange",292=>"Cotton Melange Slub",293=>"Neppy Slub",294=>"Ecovero",295=>"Silk Neps",296=>"Fair Trade",297=>"Open End CVC",298=>"CVP",299=>"Cotton Modal Slub",300=>"Organic Gots",301=>"Organic OCS",302=>"OCS",303=>"Siro Spun",304=>'AH',305=>"Rottor Contra Free",306=>"CVC Vortex.",307=>"MULTI COLOR NEPS",308=>"Polyester Linen",309=>"RFD Shade",310=>"PC Neps",311=>"Modal-Viscose",312=>"Supima Twisted",313=>"Supima Combed",314=>"Supima Combed Compact",315=>"PC Ring",316=>"CVC Ring",317=>"Cool Plus",318=>"CVC Recycle",319=>"Conventional",320=>"Dyed Yarn",321=>"BCI-Melange",322=>"Eco Coolmax",323=>"Semi Dull [HIM]",324=>"Cotton Recycled",325=>"Recycle Grey Melange",326=>"Polyamide",367=>"Lurex Silver",368=>"Lurex Golden",369=>"Lurex Coper",370=>"OXFORD",371=>"Open End",372=>"Recycle Ecru Melange",373=>"Recycle Anthra Melange",374=>"Airy Toyobo", 375=>"30% H.N.N",376=>"Metallie",377=>"Carded Recycle CVC",378=>"Carded PC",379=>"Carded Vortex",380=>"Carded CVC Siro",381=>"Carded MC",382=>"Combed MC",383=>"Combed Recycle",384=>"Combed Recycle CVC",385=>"Combed Vortex",386=>"Combed CVC Siro",387=>"Inject Melange",388=>"IC2",389=>"CVC White",390=>"Combed Cmia.",391=>"Compact BCI",392=>"Black Neppy",393=>"White Neppy",394=>"Carded Essential cotton",395=>"Shankar-6",396=>"CVC Contra Free",397=>"Melange CMIA",398=>"TC",399=>"Color Cotton",400=>"OE Bleached",401=>"Carded Hosiery",402=>"Combed Hosiery",403=>"Super",404=>"Hosiery",405=>"Combed Compact Hosiery",406=>"Carded CMIA",407=>"Carded OCS GRS",408=>"TCD Dancing Yarn",409=>"Snow White",410=>"Snow Black",411=>"Carded Polyester Slub",412=>"ORG Ecru Melange",413=>"BCI Ecru Melange",414=>"Dyeable",415=>"BCI Carded Cotton Modal",416=>"Siro Black Carded",417=>"CVC Slub Carded",418=>"BCI Recycled Cotton",419=>"Liva Eco",420=>"Neppy Black",421=>"BCI CVC Siro",422=>"Ring Yarn",423=>"Semi-Dull [H. Temp.]",424=>"Siro Black",425=>"Cima OCS",426=>"FSC",427=>"Carded Essential Contra Free Cotton",428=>"Carded Recycle",429=>"Recycle PC",430=>"Carded Compact",431=>"Carded PSCP",432=>"Carded Slub CMIA",433=>"Combed PSCP",434=>"Fibre Dye",435=>"Grey Melange",436=>"Lycra",437=>"Slub Compact",438=>"Viloft", 439=>"BCI Rottor Carded Contra Free");
asort($yarn_type_for_entry);

$yarn_type = return_library_array("select YARN_TYPE_ID,YARN_TYPE_SHORT_NAME from lib_yarn_type where is_deleted=0 and status_active=1 order by YARN_TYPE_SHORT_NAME", "YARN_TYPE_ID", "YARN_TYPE_SHORT_NAME");
$yarn_type=(count($yarn_type))?$yarn_type:$yarn_type_for_entry;
asort($yarn_type);


$service_type= array(1=>"Knitting",2=>"Collar and Cuff Knitting",3=>"Feeder Stripe Knitting",10=>"Yarn Dyeing",11=>"Fabric Dyeing",12=>"All Over Printing",20=>"Scouring",21=>"Brushing",22=>"Sueding",23=>"Washing",24=>"Stentering",25=>"Compacting",40=>"Cutting",41=>"Gmts. Printing",42=>"Gmt. Embroidery",43=>"Gmts. Washing",44=>"Sewing");
//$service_type= array(1=>"AOP",2=>"Yarn Dyeing",3=>"Gmt. Print",4=>"Gmt. Embroidery",5=>"Gmt. Wash",6=>"Scouring",7=>"Brushing",8=>"Sueding",9=>"Knitting",10=>"Dyeing",11=>"Collar and Cuff Knitting",12=>"Feeder Stripe Knitting",13=>"Stripe Print Charge",20=>"Others");
//$export_finance_loan_type=array(1=>"Packing Credit",2=>"Export Cash Credit");
$yarn_dyeing_process = array(1 => "Soft Conning", 2 => "Yarn Dyeing", 3 => "Hydro Extractor", 4 => "Dryer");

$lc_type = array( 1 => "BTB LC", 2 => "Margin LC", 3 => "Fund Building", 4 => "TT", 5 => "FTT", 6 => "FDD");
$export_lc_type = array( 1 =>"Foreign", 2 =>"Local");

$source_pay_term = array(
	1	=> "01 Import LC - At sight",
	2	=> "02 Import LC - Usance",
	3	=> "03 BTB Inland - At sight",
	4	=> "04 BTB Inland - Usance",
	5	=> "05 BTB Foreign LC - At sight",
	6	=> "06 BTB Foreign LC - Usance",
	7	=> "10 Import LC EPZ - Usance",
	8	=> "11 BTB LC EPZ - At sight",
	9	=> "12 BTB LC EPZ - Usance",
	10	=> "99 Import From Inland to EPZ"
);

$supply_source = array(1 => "CASH LC AT SIGHT (01)", 2 => "CASH LC USANCE (02)", 3 => "IN LAND BTB LC AT SIGHT (03)", 4 => "IN LAND BTB LC USANCE (04)", 5 => "FOREIGN BTB LC AT SIGHT (05)", 6 => "FOREIGN BTB LC USANCE(06)", 11 => "EPZ BTB LC  AT SIGHT (11)", 12 => "EPZ BTB LC USANCE (12)", 23 => "CASH LC AT SIGHT (23)", 99 => "99 Others");

$maturity_from = array(
	1	=> "Acceptance Date",
	2	=> "Shipment Date",
	3	=> "Negotiation Date",
	4	=> "B/L Date" ,
	5	=> "Delivery Challan Date"
);
 $credit_to_be_advised=array(1=>"Teletransmission",2=>"Airmail",3=>"Courier",4=>"Airmail/Courier",5=>"Telex",6=>"SWIFT");
 $increase_decrease=array(1=>"Increase",2=>"Decrease");
$nature_of_buyer_claim=array(1=>"Fabric Quality",2=>"Workmanship",3=>"Measurements",4=>"Colour Shading",5=>"Lab Results Non Conformed",6=>"Labelling Non Conformed",7=>"Packing Non Conformed",8=>"Late Shipment",9=>"Part/Short/Over Shipment",10=>"Chemical Substances",11=>"Quality of Supplies and Accessories",12=>"Embellishment/Print Quality",99=>"Others");
$buyer_claim_inspected_by=array(1=>"Local Office",2=>"Buying House",3=>"3rd Party",4=>"Warehouse");


$export_item_category = array(1 => "Knit Garments", 2 => "Woven Garments", 3 => "Sweater Garments", 4 => "Leather Garments", 10 => "Knit Fabric ", 11 => "Woven Fabric", 20 => "Knitting", 21 => "Weaving", 22 => "Dyeing & Finishing", 23 => "All Over Printing", 24 => "Fabric Washing", 30 => "Cutting", 31 => "Sewing", 35 => "Gmts Printing", 36 => "Gmts Embroidery", 37 => "Gmts Washing", 40 => "Yarn", 45 => "Accessories", 50 => "Chemical", 51 => "Dyes", 55 => "Food Item", 60 => "Medicine", 65 => "Transportation", 66 => "C & F", 67 => "Inbound Sub Con");

/* previous array change 7/1/2017
$commercial_head = array(1=>"Negotiation Loan/Liability", 5=>"BTB Margin/DFC/BLO A/C", 6=>"ERQ A/C", 10=>"CD Account", 11=>"STD A/C", 15=>"CC Account", 16=>"OD A/C20", 20=>"Packing Credit", 21=>"Bi-Salam/PC", 22=>"Export Cash Credit", 30=>"EDF A/C", 31=>"PAD", 32=>"LTR", 33=>"FTT/TR", 34=>"LIM", 35=>"Term Loan", 36=>"Force Loan", 40=>"IFDBC Liability", 45=>"Bank Charge", 46=>"SWIFT Charge", 47=>"Postage Charge", 48=>"Handling Charge", 49=>"Source Tax", 50=>"Excise Duty", 51=>"Foreign Collection Charge", 60=>"Other Charge", 61=>"Foreign Commision", 62=>"Local  Commision", 63=>"Penalty on Doc Descrepency", 64=>"Penalty on Goods Descrepency", 65=>"FDBC Commision", 70=>"Interest", 71=>"Import Margin A/C", 75=>"Discount A/C", 76=>"Advance A/C", 80=>"HPSM", 81=>"Sundry A/C", 82=>"MDA Special", 83=>"MDA UR", 84=>"Vat", 85=>"FDR Build up", 86=>"Miscellaneous Charge", 87=>"others Fund(sinking)", 88=>"Bank Commission", 89=>"Vat On Bank Commission", 90=>"Insurance Coverage", 91=>"Add Confirmation Change", 92=>"MDA Normal", 93=>"Settlement A/C", 94=>"Cash Security A/C", 95=>"Loan A/C", 96=>"Courier Charge", 97=>"Telex Charge", 98=>"Application Form Fee");*/

/*$commercial_head = array(1=>"Negotiation Loan/Liability", 5=>"BTB Margin/DFC/BLO A/C", 6=>"ERQ A/C", 10=>"CD Account", 11=>"STD A/C", 15=>"CC Account", 16=>"OD A/C20", 20=>"Packing Credit", 21=>"Bi-Salam/PC", 22=>"Export Cash Credit", 30=>"EDF A/C", 31=>"PAD", 32=>"LTR", 33=>"FTT/TR", 34=>"LIM", 35=>"Term Loan", 36=>"Force Loan", 40=>"IFDBC Liability", 45=>"Bank Charge", 46=>"SWIFT Charge", 47=>"Postage Charge", 48=>"Handling Charge", 49=>"Source Tax", 50=>"Excise Duty", 51=>"Foreign Collection Charge", 60=>"Other Charge", 61=>"Foreign Commision", 62=>"Local  Commision", 63=>"Penalty on Doc Descrepency", 64=>"Penalty on Goods Descrepency", 65=>"FDBC Commision", 70=>"Interest", 71=>"Import Margin A/C", 75=>"Discount A/C", 76=>"Advance A/C", 80=>"HPSM", 81=>"Sundry A/C", 82=>"MDA Special", 83=>"MDA UR", 84=>"Vat On Bank Commission", 85=>"FDR Build up", 86=>"Miscellaneous Charge", 87=>"others Fund(sinking)", 88=>"Bank Commission", 89=>"Vat", 90=>"Insurance Coverage", 91=>"Add Confirmation Charge", 92=>"MDA Normal", 93=>"Settlement A/C", 94=>"Cash Security A/C", 95=>"Loan A/C", 96=>"Courier Charge", 97=>"Telex Charge", 98=>"Application Form Fee",99=>"UPAS",100=>"Offshore",101=>"Stationary",102=>"Stamp Charge",103=>"Amendment Charge",104=>"Long Term Loan-Secured",105=>"Long Term Loan-UnSecured", 106=>"Demand Loan",107=>"SOD", 108=>"Pre-Shipment Finance",109=>"Post-Shipment Finance",110=>"Pre-Import Finance");*/

//,113=>"VAT on Bank Commission",//Dublicate Found
$commercial_head = array(1 => "Negotiation Loan/Liability", 5 => "BTB Margin/DFC/BLO/DAD/RAD A/C", 6 => " ERQ/FCAD A/C", 10 => "CD Account", 11 => "STD A/C", 15 => "CC Account", 16 => "OD A/C", 20 => "Packing Credit", 21 => "Bi-Salam/PC", 22 => "Export Cash Credit", 30 => "EDF A/C", 31 => "PAD", 32 => " LTR/MPI", 33 => "FTT/FDD/TR", 34 => "LIM", 35 => "Term Loan", 36 => "Force Loan", 40 => "IFDBC Liability", 45 => "Bank Charge", 46 => "SWIFT Charge", 47 => "Postage Charge", 48 => "Handling Charge", 49 => "Source Tax", 50 => "Excise Duty", 51 => "Foreign Collection Charge", 60 => "Other Charge", 61 => "Foreign Commission", 62 => "Local  Commission", 63 => "Penalty on Doc Discrepancy", 64 => "Penalty on Goods Discrepancy", 65 => "FDBC Commission", 70 => "Interest", 71 => "Import Margin A/C", 75 => "Discount A/C", 76 => "Advance A/C", 80 => "HPSM", 81 => "Sundry A/C", 82 => "MDA Special", 83 => "MDA UR", 84 => "Vat On Bank Commission", 85 => "FDR Build up", 86 => "Miscellaneous Charge", 87 => "others Fund(sinking)", 88 => "Bank Commission", 89 => "VAT", 90 => "Insurance Coverage", 91 => "Add Confirmation Change", 92 => "MDA Normal", 93 => "Settlement A/C", 94 => "Cash Security A/C", 95 => "Loan A/C", 96 => "Courier Charge", 97 => "Telex Charge", 98 => "Application Form Fee", 99 => "UPAS", 100 => "Offshore", 101 => "Stationary", 102 => "Stamp Charge", 103 => "Amendment Charge", 104 => "Long Term Loan-Secured", 105 => "Long Term Loan-Unsecured", 106 => "Demand Loan", 107 => "SOD", 108 => "Pre-Shipment Finance", 109 => "Post-Shipment Finance", 110 => "Pre-Import Finance", 111 => "Bank Guarantee Charge", 112 => "VAT on SWIFT Charge", 114 => "VAT on Add Confirmation Charge", 115 => "VAT on LC Application Form Fee", 116 => "VAT on Stamp Charge", 117 => "VAT on Bank Guarantee Charge", 118 => "VAT on Miscellaneous Charge", 119 => "Post-Import Finance", 120 => "Cash Incentive loan", 121 => "Additional Tax", 122 => "Exp Charge", 123 => "Special Notice Deposit [SND]", 124 => "Local Collection Charge", 125 => "Central Fund", 126 => "Re-Imbursement Payment", 127 => "Retirement", 128 => "Overdue interest", 129 => "RMG", 130 => "Export Reserve Margin", 131 => "BTB Margin(Foreign)", 132 => "BTB Margin(Local)", 133 => "BTB Margin (BUP)", 134 => "Advance Income Tex (AIT)", 135 => "Interest For Factoringg", 136 => "Late shipment penalty", 137 => "Late presentation charges", 138 => "Security For factoring", 139 => "LC Goods Releasing NOC Charge",140 => "TT/DD Charge",141 => "Accept Comm. Charge",142 => "UPASS / MIX UPASS",143 => "Outstanding Claim",144 => "Discounted to Buyer",145 => "CBM Discrepency",146 => "Late Inspection penalty",147 => "Short Realize/Shipment",148 => "Air Release Charges for Document delay",149 => "Buyer Discripency Fee",150 => "Negotiation Charge",151 => "Trade Sourcing Fee (TSF)",152 => "Product Liability Insurance (PLI)",153 => "Trade Commission for Service (TCS)",154 => "Shipment Endorsement fee/FCR Endorsement Fee",155 => "Online Transfer Charge",156 => "Commission In Lieu of Exchange (CILE)",157 => "Usance Commission",158 => "LC Transferring Charge",159 => "Document Examination Fee",160 => "Azo free cert/Te-Test report",161 => "Document Tracer charge",162 => "IBB A/C",163 => "MTR A/C",164 => "CC HYPO A/C",165 => "General A/C",166 => "Inspection Charge",167 => "Portal Charge",168 => "Libor Interest",169 => "LC Expire Charge",170 => "SFC A/C",171 => "SFC Special A/C",172 => "Vat on courier",173 => "VAT on Commission",174 => "VAT on Postage Charge",175 =>"MDA Normal",176 => "Special Security Fund [SSF]",177 => "Cash In Advance",178 => "Document Processing Fees",179 => "Vat on Document Processing Fees",180 => "Collection Charge on LDBC",181 => "Vat on Collection Charge");

$acceptance_time=array(1=>"After Goods Receive",2=>"Before Goods Receive");
$document_status=array(1=>"Original",2=>"Copy");
$submited_to=array(1=>"Lien Bank",2=>"Buyer");
$submission_type=array(1=>"Collection",2=>"Negotiation");

$document_set=array(1=>"Bill of Exchange",2=>"Delivery Challan",3=>"Commercial Invoice",4=>"Packing List",5=>"Certificate of Origin",6=>"Beneficiary Certificate",7=>"Mushak -11",8=>"Truck Receipt",9=>"BTMA",10=>"Letter of Credit with Proforma Invoice",11=>"Pre-Shipment Inspection Certificate",12=>"B/L No",13=>"GSP FORM A",14=>"EXP NO.",15=>"Inspection Certificate",16=>"Air Way Bill",17=>"Courier Receipt No.",18=>"Master Bill of Landing",19 =>"Production Certificate",20=>" LDC Statement",21=>"Annual Packing Declaration",22=>"Certificate Of Origin of YARN",23=>"Certificate of Origin of Cotton",24=>"Summary Sheet of all Bale Number", 25 => "Logality", 26 => "E-Mail Massage(Proof of Documents Sub)", 27 => "Short Shipment Certificate");
//--------------------------------------------------------------------End Commercial Module Array--------------------------------------//

//--------------------------------------------------------------- Start Accounts Module Array ------------------------------------------------//

$accounts_main_group=array(1=>"OWNERS EQUITY",
						   2=>"NON-CURRENT LIABILITIES",
						   3=>"CURRENT LIABILITIES",
						   4=>"NON-CURRENT ASSETS",
						   5=>"CURRENT ASSETS",
						   6=>"REVENUE",
						   7=>"COST OF GOOD SOLD",
						   8=>"OPERATING EXPENSES",
						   9=>"FINANCIAL EXPENSES",
						   10=>"NON-OPERATING INCOME & EXPENSE",
						   11=>"EXTRA ORDINARY ITEMS",
						   12=>"TAX EXPENSE");

$accounts_statement_type=array(1=>"Balance Sheet",
							   2=>"Income Statement");//Profit & Loss

$accounts_account_type=array( 1=>"Credit",
							  2=>"Debit");

$accounts_cash_flow_group=array(1=>"Operating Activities",
								2=>"Investing Activities",
								3=>"Financing Activities",
								4=>"Cash & Cash Equivalents");

								/* OLD
								1=>"Financing Activities",
									2=>"Operating Activities",
									3=>"Investing Activities",
									4=>"Cash & Cash Equivalents",
									5=>"Operating Activities");
									*/

$accounts_journal_type=array(
						   1=>"Opening/Closing Journal",
						   2=>"Credit Purchase Journal",
						   3=>"Credit Sales Journal",
						   4=>"Cash withdrawn Journal",
						   5=>"Cash Deposit Journal",
						   6=>"Cash Receive Journal",
						   7=>"Cheque Deposit Journal",
						   8=>"Cash Payment Journal",
						   9=>"Export Realization Journal",
						   10=>"Bank Payment Journal",
						   11=>"Adjustment Journal",
						   12=>"Provisional Journal",
						   13=>"Reverse Journal",
						   14=>"Rectifying Journal",
						   15=>"General Journal");

$control_accounts = array(1=>"AP",
						  2=>"AR",
						  3=>"Import Payable",
						  4=>"Export Receivable",
						  5=>"Advance Paid",
						  6=>"Advance Received",
						  7=>"Export Negotiation Liability",
						  8=>"Other Trade Finance",
						  9=>"Tax at source from Suppliers' Bill",
						  10=>"Tax at source from Sales Bill",
						  11=>"VAT at source from Suppliers' Bill",
						  12=>"VAT at source from Sales Bill",
						  13=>"Security at source from Suppliers' Bill",
						  14=>"Security at source from Sales Bill",
						  15=>"Tax at source from Employees' Salary",
						  16=>"Discount Allowed",
						  17=>"Discount Received",
						  18=>"Write-off Assets",
						  19=>"Write-off Liability",
						  20=>"Other Subsidiary",
						  21=>"Advance Paid-Employee",
						  22=>"Advance Paid-Others",
						  23=>"Advance Received-Employee",
						  24=>"Advance Received-Others",
						  25=>"Goods in Transit",
						  26=>"ILE Control Account",
						  27=>"Yarn Dyeing Charge");

  $account_nature = array(1=>"Cash",
						  2=>"Bank",
						  3=>"OD/CC",
						  4=>"Foreign Sales",
						  5=>"Local Sales",
						  6=>"Project Sales",
						  7=>"Purchase",
						  8=>"Project Cost",
						  9=>"Interest",
						  10=>"Bank Charges",
						  11=>"Currency Exchange Gain/Loss - Export",
						  12=>"Currency Exchange Gain/Loss - Import",
						  13=>"Project Common Cost",
						  14=>"Depreciation, Amortization & Depletion");

	$instrument_type = array(1=>"Bearer Cheque",
						  2=>"Crossed Cheque",
						  3=>"Pay Order",
						  4=>"TT",
						  5=>"DD",
						  6=>"Special Crossed Cheque",
						  7=>"Deposit Slip");

 	$ac_loan_type = array(
						1=>"PAD",
						2=>"LTR",
						3=>"LIM",
						10=>"Packing Credit",
						11=>"ECC",
						20=>"Term Loan",
						50=>"Project Loan");

  $ratio_category  = array(
						1=>"Liquidity",
						2=>"Activity",
						3=>"Leverage",
						4=>"Profitability",
						5=>"Market");

//------------------------------------------------------ End Accounts Module Array -------------------------------------------------------------//

//---------------all day---------------------------------------sohel -------------------------------------------------------------//


//--------------------------TNA_task----------------------------------------

 $general_task=array(1=>"Order Placement Date",2=>"Order Evaluation",3=>"Acceptance to be given",4=>"Internal communication to be done");
 $test_approval_task=array(1=>"Fabric test to be done",2=>"Garments test to be done");
 $purchase_task=array(1=>"Fabric booking to be issued",2=>"Trims booking to be issued",3=>"Fabric service work order to be issued",4=>"Sample Fabric booking to be issued");
 $material_receive_task=array(1=>"Grey fabric to be in-house",2=>"Finished fabric to be in-house",3=>"Sewing trims to be in-house",4=>"Finishing trims to be in-house");

 $fabric_production_task=array(1=>"Grey fabric production to be done",2=>"Dyeing production to be done",3=>"Finish fabric production to be done",4=>"Yarn Send for Dyeing",5=>"Dyed Yarn Receive",6=>"Fabric Send for AOP",7=>"AOP Receive");

 $garments_production_task=array(1=>"PP meeting to be conducted",2=>"Trail cut to be done",3=>"Trail production to be submitted",4=>"Trail production approval to be received",5=>"PCD to be end",6=>"Print/Emb TOD  to be end",7=>"Sewing  to be end",8=>"Garments finishing to be done");

 $inspection_task=array(1=>"Inspection schedule to be offered",2=>"Inspection to be done");
 $export_task=array(1=>"Ex-Factory to be done",2=>"Document to be submited",3=>"Proceeds to be realized");
 $lapdip_task_name=array(1=>"Submission",2=>"Target Approval");
 $embelishment_approval_task=$emblishment_name_array;//array(1=>"Local",2=>"Imported");

$category_wise_task_array=array(1=>"General Task",5=>"Sample Approval Task",6=>"Lapdip Task Name",7=>"Trims Approval Task",8=>"Embelishment Approval Task",9=>"Test Approval Task",15=>"Purchase Task",20=>"Material Receive Task",25=>"Fabric Production Task",26=>"Garments Production Task",30=>"Inspection Task",35=>"Export Task");

$material_source=array(1=>"Local",2=>"Imported");
$test_approval_task=array(1=>"Fabric Approval",2=>"Garments Approval");
$inspection_task=array(1=>"Inspection Offered",2=>"Inspection Completed");
$knit_fabric_production_task=array(1=>"Knitting",2=>"Dyeing & Finishing");
$woven_fabric_production_task=array(1=>"Weaving",2=>"Dyeing",3=>"Finishing");
$material_purchase_task=array(1=>"Fabric Booking",2=>"Trims Booking",3=>"Embellishment Booking");

$knitting_source =array(1=>"In-house",2=>"In-bound Subcontract",3=>"Out-bound Subcontract");

$time_source =array(1=>"AM",2=>"PM");
$order_source=array(1=>"Self Order",2=>"Subcontract Order",3=>"Reprocess Batch",4=>"Trims Batch",5=> "Sample Batch");

$tna_task_name = array(
						1	=> "Order Placement Date",
						2	=> "Order Evaluation",
						3	=> "Acceptance To Be Given",
						4	=> "Internal Communication To Be Done",
						5	=> "SC/LC Received",
						7	=> "Fit Sample Submit",
						8	=> "PP Sample Submit",
						9	=> "Labdip Submit",
						10	=> "Labdip Approval",
						11	=> "Trims Approval",
						12	=> "PP Sample Approval",
						13	=> "Fit Sample Approval",
						14	=> "Size Set Submission",
						15	=> "Size Set Approval",
						16	=> "Production Sample Submission",
						17	=> "Production Sample Approval",
						18	=> "Labdip Requisition",
						19	=> "Embellishment Submission",
						20	=> "Embellishment Approval",
						21	=> "Tag Sample Submission",
						22	=> "Tag Sample Approval",
						23	=> "Photo Sample Submission",
						24	=> "Photo Sample Approval",
						25	=> "Trims Submission",
						26	=> "Packing  Sample Submission",
						27	=> "Packing  Sample Approval",
						28	=> "Final  Sample Submission",
						29	=> "Final  Sample Approval",
						30	=> "Sample Fabric Booking To Be Issued",
						31	=> "Fabric Booking To Be Issued",
						32	=> "Trims Booking To Be Issued",
						33	=> "Fabric Service Work Order To Be Issued",
						34	=> "Woven Fabric Work Order To Be Issued",
						35  => "Labdip Receive From Factory",
						36  => "PPS Booking Date",
						37  => "PPS Making",
						40	=> "Fabric Test To Be Done",
						41	=> "Garments Test To Be Done",
						45	=> "Yarn purchase requisition",
						46	=> "Yarn purchase order",
						47	=> "Yarn Receive",
						48	=> "Yarn Allocating",
						50	=> "Yarn Issue To Be Done",
						51	=> "Yarn Send for Dyeing",
						52	=> "Dyed Yarn Receive",

						60	=> "Grey Fabric Production To Be Done",
						61	=> "Dyeing Production To Be Done",
						62	=> "Fabric Send for AOP",
						63	=> "AOP Receive",
						64	=> "Finish Fabric Production To Be Done",
						70	=> "Sewing Trims To Be In-house",
						71	=> "Finishing Trims To Be In-house",
						72	=> "Grey fabric to be in-house",
						73	=> "Finished fabric to be in-house",
						74	=> "Finish Fabric Issue to Cut",
						80	=> "PP Meeting To Be Conducted",
						81	=> "Trial cut to be done",
						82	=> "Trial production to be submitted",
						83	=> "Trial production approval to be received",
						84	=> "Cutting To Be Done",
						85	=> "Print/Emb To Be Done",
						86	=> "Sewing To Be Done",
						87	=> "Iron To Be Done",
						88	=> "Garments Finishing To Be Done",
						89	=> "Garments sent for Wash",
						90	=> "Garments Receive from Wash",
						91	=> "Poly Entry done",
						100	=> "Inspection Schedule To Be Offered",
						101	=> "Inspection To Be Done",
						110	=> "Ex-Factory To Be Done",
						120	=> "Document to be submited",
						121	=> "Proceeds to be realized",
						122	=> "Sewing Input To Be Done",
						123	=> "Test Sample Approval",
						124	=> "Photo In Lay/Litho Link",
						125 => "CAD - Marker",
						126 => "PSO Submit",
						127 => "PSO Approval",
						128 => "ESO Submit",
						129 => "ESO Approval",
						130 => "Trim Card Handover",
						131 => "Production File Handover",
						132 => "Pre Final",
						133	=>'Tech File Receive Date',
						134	=>'Packing Accessories Booking ',
						135	=>'Fit Sample Fabric Booking',
						136	=>'Size Set Sample Fabric Booking',
						137	=>'Production Sample Fabric Booking',
						138	=>'Tag Sample Fabric Booking',
						139	=>'Photo Sample Fabric Booking',
						140	=>'Packing Sample Fabric Booking',
						141	=>'Final Sample Fabric Booking',
						142	=>'PPS Fabric Requisition',
						143	=>'Fit Sample Fabric Requisition',
						144	=>'Fit Sample Fabric Requisition',
						145	=>'Production Sample Fabric Requisition',
						146	=>'Tag Sample Fabric Requisition',
						147	=>'Photo Sample Fabric Requisition',
						148	=>'Packing Sample Fabric Requisition',
						149	=>'Final Sample Fabric Requisition',
						150	=>'PPS Fabric Issue',
						151	=>'Fit Sample Fabric Issue',
						152	=>'Size Set Sample Fabric Issue',
						153	=>'Production Sample Fabric Issue',
						154	=>'Tag Sample Fabric Issue',
						155	=>'Photo Sample Fabric Issue',
						156	=>'Packing Sample Fabric Issue',
						157	=>'Final Sample Fabric Issue',
						158	=>'Fit Sample Making',
						159	=>'Size Set Sample Making',
						160	=>'Production Sample Making',
						161	=>'Tag Sample Making',
						162	=>'Photo Sample Making',
						163	=>'Packing Sample Making',
						164	=>'Final Sample Making',
						165	=>'Yarndip Requisition',
						166	=>'Yarndip Submit To Buyer',
						167	=>'Yarndip Approval',

						168	=> "PPS Fabrics Issue (AOP)",
						169	=> "PPS Fabrics Issue (YD)",
						170	=> "PPS Making (AOP)",
						171	=> "PPS Making (YD)",
						172	=> "PPS Submit (AOP)",
						173	=> "PPS Approval (AOP)",
						174	=> "PPS Submit (YD)",
						175	=> "PPS Approval (YD)",
						176	=> "Trim Card Handover (AOP)",
						177	=> "Trim Card Handover (YD)",
						178	=> "Knitting production (YD)",
						179	=> "Finish Fabrics Inhouse (AOP)",
						180	=> "Finish Fabrics Inhouse (YD)",
						181	=> "Production File Handover(YD)",
						182	=> "Size Set Making (AOP)",
						183	=> "Size Set Making (YD)",
						184	=> "PP Meeting (AOP)",
						185	=> "PP Meeting (YD)",
						186	=> "Cutting Production (AOP)",
						187	=> "Cutting Production (YD)",
						188	=> "Embellishment (AOP)",
						189	=> "Embellishment (YD)",
						190	=> "Sewing Production(AOP)",
						191	=> "Sewing Production(YD)",
						192=>"Proto Sample Requisition",

						193=>"Proto Sample Submission",
						194=>"Proto Sample Approval",
						195=>"Counter Sample Fabric Booking",
						196=>"Counter Sample Requisition",
						197=>"Counter Sample Submit",
						198=>"Counter Sample Approval",

						199=>"Fabric Sales Order",
						200=>"Knitting Plan Solid",
						201=>"Grey Fabric Delivery To Store",
						202=>"Grey Fabric Requisition for batch",
						203=>"Grey Fabric Issue",
						204=>"Grey Receive By Batch",
						205=>"Batch Creation",
						206=>"Fabric Service Receive",
						207=>"Finish fabric Delivery to Store",
						208=>"Fabric Test AOP",
						209=>"Fabric Test YD",
						210=>"Knitting Plan AOP",
						211=>"Knitting Plan YD",
						212=>"Knitting Production Solid",
						213=>"Labdip Approval AOP",
						214=>"Labdip Approval YD",
						215=>"Dyeing Production AOP",
						216=>"Dyeing Production YD",
						217=>"Fabric Service Send AOP",
						218=>"Fabric Service Send YD",


						219=>"In-Line Inspection",
						220=>"Mid-Line Inspection",
						221=>"Documents Mailing",
						222=>"Forwarder Booking",
						223=>"Final Inspection Booking",

						224=>"Export PI Issue",
						225=>"LC Rcv at Bank",
						226=>"B2B LC Arrange",
						227=>"MO Issue Date",
						228=>"Bulk Swatch Ready Date",
						229=>"Pilot Run Review",
						230=>"Packing Method Approval Date",
						231=>"Packing RM Rcv Date",
						232=>"Packing List Rcv Date",
						233=>"Pack Finish Date",
						234=>"Vessel Booking",
						235=>"Garments Handover Date",
						236=>"Document Dispatched Date"	,
						237=>"Yarn PI Rcv Date",
						238=>"Grey PI Rcv Date",
						239=>"Finish Fabric Delivery To Garments",
						240=>"Yarn ETD",
						241=>"Yarn ETA",
						242=>"Trims ETD",
						243=>"Trims ETA",
						244=>"Trims Inhouse",
						245=>"Bulk Yarn App",
						246=>"In Line",
						247=>"BL Date",
						248=>"Size Set Sample Requisition",
						249=>"First Batch Production",
						250=>"Trial Production run",
						251=>"Fabric quality sample collection",
						252=>"Yarn Store Requisition",
						253=>"Embellishment Solid",
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
						266=>"SMS Sample Approval",
						267=> "Print To Be Done",
						268=> "Emb To Be Done",
						);asort($tna_task_name);

$tna_common_task_name_to_process = array(
						1	=> "Order Placement Date",
						2	=> "Order Evaluation",
						3	=> "Acceptance To Be Given",
						4	=> "Internal Communication To Be Done",
						5	=> "SC/LC Received",
						28	=> "Final Sample Submissinon",
						29	=> "Final Sample Approval",
						30	=> "Sample Fabric Booking To Be Issued",
						//31	=> "Fabric Booking To Be Issued",
						32	=> "Trims Booking To Be Issued",
						33	=> "Fabric Service Work Order To Be Issued",
						//34	=> "Woven Fabric Work Order To Be Issued",
						40	=> "Fabric Test To Be Done",
						41	=> "Garments Test To Be Done",
						//45	=> "Yarn purchase requisition",
						//46	=> "Yarn purchase order",
						//47	=> "Yarn Receive",
						//48	=> "Yarn Allocating",
						//50	=> "Yarn Issue To Be Done",
						//60	=> "Grey Fabric Production To Be Done",
						//61	=> "Dyeing Production To Be Done",
						//64	=> "Finish Fabric Production To Be Done",
						70	=> "Sewing Trims To Be In-house",
						71	=> "Finishing Trims To Be In-house",
						//72	=> "Grey fabric to be in-house",
						//73	=> "Finished fabric to be in-house",
						74	=> "Finish Fabric Issue to Cut",
						80	=> "PP Meeting To Be Conducted",
						81	=> "Trail cut to be done",
						82	=> "Trail production to be submitted",
						83	=> "Trail production approval to be received",
						84	=> "Cutting To Be Done",
						86	=> "Sewing To Be Done",
						87	=> "Iron To Be Done",
						88	=> "Garments Finishing To Be Done",
						100	=> "Inspection Schedule To Be Offered",
						101	=> "Inspection To Be Done",
						110	=> "Ex-Factory To Be Done",
						120	=> "Document to be submited",
						121	=> "Proceeds to be realized",
						122	=> "Sewing Input To Be Done",
						192=>"Proto Sample Requisition",

						193=>"Proto Sample Submission",
						194=>"Proto Sample Approval",
						195=>"Counter Sample Fabric Booking",
						196=>"Counter Sample Requisition",
						197=>"Counter Sample Submit",
						198=>"Counter Sample Approval"
						);

//--------------------------- Start Inventory ------------- 04_03_2013  --------------------
// Bill processing page
$bill_disupcharge=array(1=>"Discount",2=>"Upcharge");
//Yarn Receive Basis
$receive_basis_arr=array(1=>"PI Based",2=>"WO/Booking Based",3=>"In-Bound Subcontract",4=>"Independent",5=>"Batch Based",6=>"Opening Balance",7=>"Requisition",8=>"Recipe Based",9=>"Production",10=>"Delivery",11=>"Service Booking Based",12=>"Delivery Challan(Int.)",13=>"Delivery Challan(Ext.)",14=>"Sales Order Based",15=>"Job Card",16=>"Delivery From Store",17=>"Issue ID",18=>"Fabric Booking");
//Yarn Issue Entry
$yarn_issue_purpose=array(1=>"Knitting",2=>"Yarn Dyeing",3=>"Sales",4=>"Sample With Order",5=>"Loan",6=>"Sample-material", 7=>"Yarn Test", 8=>"Sample Without Order", 9=>"Sewing Production", 10=>"Fabric Test",11=>"Fabric Dyeing",12=>"Reconning",13=>"Machine Wash",14=>"Topping",15=>"Twisting",16=>"Grey Yarn",26=>"Damage",27=>"Pilferage",28=>"Expired",29=>"Stolen",30=>"Adjustment",31=>"Scrap Store",32=>"ETP",33=>"WTP",34=>"Wash",35=>"Re Wash",36=>"Sewing",37=>"Dyeing",38=>"Re-Waxing",39=>"Moisturizing",40=>"Lab Test",41=>"Cutting",42=>"Finishing",43=>"Dyed Yarn Purchase",44=>"Re Process",45=>"Used Cone Sale",46=>"Dryer",47=>"Linking",48=>"Boiler",49=>"Generator",50=>"Doubling",51=>"Punda", 52 => "AOP", 53 => "Production", 54 => "Narrow Fabric", 56 => "General Use", 58 => "RND", 59 => "Sample", 60 => "Expose",61 => "Gmts Wash", 62 => "Continuous Machine",63=>"Waxing",64=>"Extra Purpose") ;// 57 => "Dye Finishing"
$wash_issue_purpose_arr= array(1 => "Bulk", 2 => "Pilot", 3 => "Shade Band", 4 => "Size Set", 5 => "1st Bulk");
//$using_item_arr=array(1=>'Drawstring',2=>'Twill Tape',3=>'Collar',4=>'Cuff',5=>'Rubber Thread');
$using_item_arr=array(1=>'Drawstring',2=>'Twill Tape',3=>'Collar',4=>'Cuff',5=>'Rubber Thread',6=>'Elastic',7=>'Development',8=>"Oeko-Tex");

//Inventory Variable List. Created by sohel // As per Siddik and CTO -Remove it= 22=>"Independent Receive/Issue Basis"
$inventory_module = array(8 => "ILE/Landed Cost Standard", 9 => "Hide Opening Stock Flag", 10 => "Item Rate Manage in MRR", 11 => "Item QC", 16 => "User given item code", 17 => "Book Keeping Method", 18 => "Allocated Quantity", 19 => "Receive Control On Gate Entry", 20 => "Independent basis controll and rate manage", 21 => "Rack Wise Balance Show", 23 => "Material Over Receive Control", 24 => "Issue Requisition Mandatory", 25 => "Yarn item and rate matching with budget", 26 => "Woven Finish Fabric Desc Change", 27 => "Ack. Required For Item Transfer", 28 =>"Yarn Issue Basis", 29 =>"Dyes Chemical Lot Maintain", 30 =>"Requisition Basis Transfer", 31 =>"WO PI Receive Level", 32 =>"Printing Chemicals & Dyes Lot Maintain", 33 =>"Auto Batch No Woven Finish Fabric",34 =>"Woven Finish Fabric Style Wise", 35 => "Item Issue Req. Stock Validation", 36 => "Yarn Test Mandatory For Allocation");
//Transaction Type
$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return",5=>"Item Transfer Receive",6=>"Item Transfer Issue");
$issue_basis=array(1=>"Booking", 2=>"Independent", 3=>"Requisition", 4=>"Sales Order", 5=>"Job", 6=>"Lot Ratio", 7=>"Sample", 8=>"Demand", 9=>"Service Booking", 10=>"Sample Booking");
$store_method=array(1=>"FIFO",2=>"LIFO");
$general_issue_purpose=array(1=>"Damage",2=>"Pilferage",3=>"Stolen",4=>"Unknown",5=>"Loan",6=>"Sewing",7=>"Cutting",8=>"Finishing",9=>"Building Development",10=>"Land Development",11=>"Generator",12=>"Machinery",13=>"Air Cooling System",14=>"Furniture & Fixtures",15=>"Sales",16=>"Capital Expenditure",17=>"Dyeing",18=>"AOP",19=>"Screen Print",20=>"Yarn Production",21=>"Sample With Order",22=>"Sample Without Order",23=>"Deffered Expenses",24=>"Trims Production",25=>"Thread Dyeing",26 => "Knitting", 27 => "Linking", 28 => "Winding", 29 => "Packing", 30 => "Final Inspection", 31 => "HR and Admin", 32 => "ICT",33=>"Print",34=>"Embroidery",35=>"Heat Transparent Print [HTP]",36=>"Loan Return");

$quot_evaluation_factor=array(1=>"Quoted Item",2=>"Specification",3=>"Performance",4=>"Brand",5=>"Country of Origin",6=>"Delivery Days",7=>"Pay Term",8=>"Warranty",9=>"Service Agreement",10=>"online Support",11=>"Local Support Center",12=>"Price");
/*$get_pass_basis=array(1=>"Independent",2=>"Challan(Yarn)",3=>"Challan(Grey Fabric)",4=>"Challan(Finish Fabric)",5=>"Challan(General Item)",6=>"Challan(Trims)",6=>"Challan(Dyes & Chemical)",7=>"Challan(Trims)",8=>"Challan Subcon(grey fabric)",9=>"Challan Subcon (finish fabric)",10=>"Grey Fabric Delivery to Store",11=>"Finish Fabric Delivery to Store",12=>"Challan(Garments Delivery)",13=>"Challan(Embellishment Issue)",14=>"Challan[Cutting Delivery]",15=>"Challan[Finish Fab Rec Return]"
,16=>"Challan[Yarn-Transfer]",17=>"Challan[Grey Fabric-Transfer]",18=>"Challan[General Item-Transfer]",19=>"Challan[Trims-Transfer]",20=>"Challan[Dyes and Chemical-Transfer]",21=>"Challan(Yarn Recv Return)",22=>"Fabric Issue to Fin. Process",23=>"Challan[General Item Receive Return]",24=>"Challan[Trims Receive Return]",25=>"Challan[Dyes And Chemical Receive Return]",26=>"Challan[SubCon Material Return]",27=>"Challan Subcon[Garment Delivery]",28=>"Sample Delivery Challan",29=>"Challan[Woven Finish Fab Rec Return]",30=>"Challan[Fabric Service Receive Return]",31=>"Challan[Woven Finish Fabric Issue Return]",32=>"Challan[Woven Finish Fabric Issue]",33=>"Challan[SubCon Embellishment Delivery]");*/
$get_pass_basis=array(1=>"Independent",2=>"Yarn Issue",3=>"Knit Grey Fabric Issue/Return",4=>"Knit Finish Fabric Issue",5=>"General Item Issue",6=>"Dyes And Chemical Issue",7=>"Trims Issue",8=>"SubCon Knitting Delivery",9=>"SubCon Dyeing And Finishing Delivery",10=>"Grey Fabric Delivery to Store",11=>"Finish Fabric Delivery to Store",12=>"Garments Delivery Entry/Return",13=>"Embellishment Issue Entry",14=>"Cutting Delivery To Input Challan",15=>"Knit Finish Fabric Receive Return",16=>"Yarn Transfer",17=>"Grey Fabric Transfer",18=>"General Item Transfer",19=>"Trims Transfer",20=>"Dyes and Chemical Transfer",21=>"Yarn Recv Return",22=>"Fabric Issue to Finish Process",23=>"General Item Receive Return",24=>"Trims Receive Return",25=>"Dyes And Chemical Receive Return",26=>"SubCon Material Return",27=>"SubCon Garment Delivery",28=>"Sample Delivery Challan",29=>"Woven Finish Fabric Receive Return",30=>"Fabric Service Receive Return",31=>"Woven Finish Fabric Issue Return",32=>"Woven Finish Fabric Issue",33=>"SubCon Embellishment Delivery",34=>"Finish Fabric Transfer Entry",35=>"Scrap Out Entry",36=>"Raw Material Receive",37=>"Raw Material Receive Return",38=>"Raw Material Issue",39=>"Raw Material Issue Return",40=>"Cotton Issue", 41=>"Cotton Receive Return", 42=>"Cotton Item Transfer", 43=>"Synthetic Fibre Issue", 44=>"Synthetic Fibre Receive Return", 45=>"Synthetic Fibre Transfer", 46=>"Waste Cotton Issue", 47=>"Waste Cotton Receive Return", 48=>"Waste Cotton Transfer",49=>"Printing Delivery",50=>"Trims Delivery Challan",51=>"Wash Delivery",52=>"AOP Delivery Challan",53=>"Bundle Wise Cutting Delivery To Input Challan",54=>"Finish Fabric Delivery To Germents",55=>"Embellishment Issue for Bundle - Printing", 56=>"Wash Material Receive Return", 57=>"Sample Delivery To MKT",58=>"Roll Issue to Finish Process",59=>"Embroidery Delivery Entry",60=>"Sample Embellishment Issue",61=>"Wash Dyes Chemical Issue",62=>"Embellishment Issue for Bundle - Embroidery",63=>"Finish Fabric Roll Delivery To Garments",64=>"SubCon Material Receive Return");
//-------------------------- End Inventory -------------------------------------------------

//------------------------- Start Sub. Bill ------------------ 09_03_2013 ------------------
$rate_type=array(1=>"External",2=>"Internal");
$is_deleted=array(0=>"No",1=>"Yes");
$production_process=array(1=>"Cutting",2=>"Knitting",3=>"Dyeing",4=>"Finishing",5=>"Sewing",6=>"Fabric Printing",7=>"Washing",8=>"Gmts Printing",9=>"Embroidery",10=>"Iron",11=>"Gmts Finishing",12=>"Gmts Dyeing",13=>"Poly",14=>"Re Conning",15=>"Common", 16=> "Knit Finish Fabric",17=> "Dyeing process",18=> "Trims",19=> "Yarn Dyeing",20=> "Hang Tag Entry",21=>"Washing");

$bill_for=array(1=>"Order",2=>"Sample with order",3=>"Sample without order");
$bill_section=array(1=>"Knitting",2=>"Drawstring",3=>"Collar and Cuff",4=>"Twill Tape");
$service_for_arr=array(1=>"Repair", 2=>"Maintenance", 3=>"Installation");

$instrument_payment=array(1=>"Cash",2=>"Cheque",3=>"Pay Order",4=>"LC",5=>"Non-Cash");

$adjustment_type=array(1=>"Discount",2=>"Bad Debts",3=>"Write Off",5=>"Others",6=>"Advance Adjustment");
$payment_type=array(1=>"Due & Advance Rec.",2=>"Advance",3=>"Due Adjustment");
$bill_rate=array(1=>"Rate Manually",2=>"Rate from Order",3=>"Rate from Library",4=>"Rate from Mkt Order Entry");

//------------------------- End Sub. Bill --------------------------------------------------
$buyer_quotation_status=array(1=>"Submitted",2=>"Confirm",3=>"Cancle",4=>"Inactive");
$clearance_method=array(1=>"First Come First Adjust",2=>"Manual Adjustment");

//=================Planning

//$complexity_level=array(1=>"Basic",2=>"Simply Complex", 3=>"Highly Complex");
$complexity_level=array(0=>"",1=>"Basic",2=>"Fancy", 3=>"Critical", 4=>"Average");

$complexity_level_data[0]['fdout']=0;
$complexity_level_data[0]['increment']=0;
$complexity_level_data[0]['target']=0; ///complexity_levels
$complexity_level_data[1]['fdout']=1000;
$complexity_level_data[1]['increment']=100;
$complexity_level_data[1]['target']=1200;
$complexity_level_data[2]['fdout']=800;
$complexity_level_data[2]['increment']=100;
$complexity_level_data[2]['target']=1200;
$complexity_level_data[3]['fdout']=600;
$complexity_level_data[3]['increment']=100;
$complexity_level_data[3]['target']=1200; ///complexity_levels
$complexity_level_data[4]['fdout']=880;
$complexity_level_data[4]['increment']=100;
$complexity_level_data[4]['target']=1100; ///complexity_levels

$complexity_type_tmp=array( 1=>"Regular complexity",2=>"Percentage Based");

//report

$report_format = array( 1 => "Print GP", 2 => "Print B1", 3 => "Print B2", 4 => "Print Cut1", 5 => "Print Cut2", 6 => "Print B3", 7 => "Print B3", 8 => "Print Booking", 9 => "Print Booking 2", 10 => "Fabric Booking", 11 => "Fabric Booking", 12 => "Print Booking 1", 13 => "Print Booking", 14 => "Print Booking 1", 15 => "Print Booking 2", 16 => "Print Booking 3", 17 => "Print Booking", 18 => "Print Booking 1", 19 => "Print Booking 2", 20 => "Print Booking", 21 => "Print Booking", 22 => "Print Booking", 23 => "Summary", 24 => "Budget", 25 => "Budget Report2", 26 => "Quote Vs Budget", 27 => "Budget On Shipout", 28 => "Print B13", 29 => "C.Date Budget On Shipout", 30 => "Projection Template MM", 31 => "Plan Vs Ex-F", 32 => "Ex-F Vs Plan", 33 => "Both Plan and Ex-Factory", 34 => "Print Booking", 35 => "Print Booking 2", 36 => "Print Amana", 37 => "Print AKH", 38 => "Print Booking1", 39 => "Print Booking2", 40 => "Party Wise", 41 => "Job Wise", 42 => "Challan Wise", 43 => "Returnable", 44 => "Reset", 45 => "Print B4", 46 => "Short Fabric Booking Urmi", 47 => "With Source", 48 => "Without Source", 49 => "Fabrics", 50 => "Pre Cost Rpt", 51 => "Pre Cost Rpt2", 52 => "BOM Rpt", 53 => "Print B5", 54 => "Buyer Wise", 55 => "Generate Task Wise", 56 => "Generate Report", 57 => "Overdue Task", 58 => "Penalty", 59 => "Print Booking BPKW", 60 => "Print Booking", 61 => "Print Booking1", 62 => "Print Booking2", 63 => "BOM Rpt2", 64 => "Metro", 65 => "Metro", 66 => "Print 2", 67 => "Print Booking", 68 => "Print Barcode", 69 => "Fabric Details", 70 => "GIN3-MC", 71 => "GIN4",72 => "Print 6",  73 => "Print B6", 74 => "Print Order With Rate", 75 => "Print Order Without Rate", 76 => "Print With Multiple Job", 77 => "Multiple Job Without Rate",78 => "Print",79 => "Print With Rate", 80 => "Print Without Rate", 81 => "Multiple Sample With Rate", 82 => "Multiple Sample Without Rate", 83 => "Print Work Order Report",84 => "Print 2", 85 => "Print 3",86 => "Print", 87 => "Print Actual", 88 => "Print3", 89 => "Print4", 90 => "Quot. Rpt", 91 => "Quot. Rpt2", 92 => "Quot. Rpt3", 93 => "Print B9", 94 => "Print", 95 => "Print With VAT", 96 => "Summary", 97 => "Party Wise", 98 => "Job Wise", 99 => "Challan Wise", 100 => "Returnable", 101 => "Returnable Without Challan", 102 => "Count Wise Summ", 103 => "Type Wise Summ", 104 => "Composition Wise Summ", 105 => "Stock Only", 106 => "Count & Type Wise - 2", 107 => "Report", 108 => "Show", 109 => "Print", 110 => "Print2", 111 => "Print3", 112 => "Print With VAT", 113 => "Requisition Details", 114 => "Without Program", 115 => "Print", 116 => "Print 2", 117 => "Print 2 With Rate", 118 => "With Group", 119 => "Without Group", 120 => "Print Report", 121 => "Print Report 2", 122 => "Print Report 3", 123 => "Print Report 4", 124 => "Order Wise", 125 => "Color Wise", 126 => "Country Wise", 127 => "Color and Size", 128 => "Order and Size",129 => "Print 5",130 => "Requisition Print", 131 => "Requisition Print2", 132 => "Requisition Print3", 133 => "Knitting Card", 134 => "Print", 135 => "Print 2", 136 => "Print 3", 137 => "Print 4", 138 => "Machine Wise", 139 => "Fabric Label", 140 => "Country Ship", 141 => "Summary Country Ship", 142 => "Pre Cost Rpt Bpkw", 143 => "Print 1", 144 => "Accessories Followup Report", 145 => "Accessories Followup Report V2", 146 => "Accessories Followup Report [Budget-2]", 147 => "Show", 148 => "Location Wise", 149 => "Summary", 150 => "Summary 2", 151 => "AAL Print", 152 => "MRR Wise Stock", 153 => "Budget Wise Fabric Booking", 154 => "Main Fabric Booking V2", 155 => "Fabric Booking", 156 => "Acce. Details", 157 => "Acce. Details 2", 158 => "Pre Cost Woven", 159 => "Bom Woven", 160 => "Print 4", 161 => "Print 6", 162 => "Booking Wise", 163 => "Print Booking 1", 164 => "Print Booking 2", 165 => "Size Wise Print", 166 => "Size Wise Print2", 167 => "Color & Size Wise Print", 168 => "Color & Size Wise Print2", 169 => "Print Report 6", 170 => "Cost Rpt3", 171 => "Cost Rpt4", 172 => "Print Outbound", 173 => "Cost Rpt5", 174 => "Print For UG", 175 => "Print Booking 5", 176 => "Print Booking 6", 177 => "Print Booking 4", 178 => "Show", 179 => "Show With Html ", 180 => "Group by Style", 181 => "GIN5", 182 => "Budget Report 3", 183 => "Print Booking 2", 184 => "Composition wise lot", 185 => "Batch Card 2", 186 => "Batch Card 3", 187 => "Batch Card 4", 188 => "Adding/Topping", 189 => "Without Rate 2", 190 => "Adding/Topping Without Rate", 191 => "Print 7", 192 => "BOM Dtls",193 => "Print 4", 194 => "Quot. Woven", 195 => "Show 2", 196 => "Cutting Delivery", 197 => "BOM 3", 198 => "TNA With Commitment", 199 => "Fabric Delivery", 200 => "Finish Fabric", 201 => "Gmt Prod Sew", 202 => "Gmt Prod Fin", 203 => "Fabric Prod", 204 => "Short Form", 205 => "Gmt Prod Sew2", 206 => "General", 207 => "Gmts Delivery2", 208 => "Sample Delivery", 209 => "Print booking 3", 210 => "Without Rate Urmi", 211 => "MO Sheet", 212 => "Yarn Delivery", 213 => "Quot. Woven2", 214 => "Budget3 Summary", 215 => "Budget3 Details", 216 => "Quot. Summary", 217 => "LC Cost Details", 218 => "Northan", 219 => "Quot. Summary", 220 => "Print 8(NFL)", 221 => "Fabric Pre-Cost", 222 => "Show", 223 => "Style Wise", 224 => "Batch Card 5", 225 => "Batch Card 6", 226 => "Batch Card 7", 227 => "Print 8", 228 => "Without Rate 3", 229 => "Weight Sheet", 230 => "Print 7", 231 => "Knitting Card 2", 232 => "Knitting Card 3", 233 => "Print letter", 234 => "Print letter 2", 235 => "Print 9", 236 => "Print With Collar Cuff", 237 => "Bill Of Exchange",238 =>"BoM Summary",239 =>"Quot. Summary2", 240 => "Print letter 3", 241 => "Print 11", 242 => "Show3" , 243 => "Item Wise", 244 => "Fabric For NTG", 245 => "Prod. Wise", 246 => "Today Prod", 247 => "Machine Wise 2", 248 => "Garments", 249 => "Show With TNA", 250 => "Detail", 251 => "Monthly", 252 => "Country Wise 2", 253 => "Daily", 254 => "Show-Country", 255 => "Show-2", 256 => "Report2", 257 => "With Html", 258 => "FB issue Days", 259 => "Show2", 260 => "PO Wise", 261 => "Details", 262 => "WIP", 263 => "Report 3", 264 => "Report 4", 265 => "Country",266 => "Report1", 267 => "Report3", 268 => "Budget4", 269 => "Print B12", 270 => "Cost Rpt6", 271 => "Finish Fabric Delivery",272=>"Program info format1",273=>"Program info format2",274=>"Print 10",275=>"Quot. Rpt5",276=>"Location Wise Summary",277=>"Summary3[Mkt]",278=>"Recap1",279=>"Trims recap",280=>"Print B14",281=>"Short",282=>"Details-2",283=>"Details-3",284=>"Details-4",285=>"Spot Cost Vs Budget", 286 => "Prod. Wise2", 287 => "Knitting Card 5", 288 => "Print Booking 5",289 => "Retention Statement",290 => "Construction Wise", 291 => "Monthly2", 292 => "Job And Color Wise", 293 => "Excel Convert",294 => "Bundle",295 => "List Shade",296 => "Sticker 6/Page",297 => "Sticker 8/Page",298 => "Sticker 1/Page",299 => "Sticker 1/Page V2",300 => "Send To Printer",301 => "Sticker 128",302 => "Qr Code",303 => "Sticker 1/Page V3",304 => "Print B15",305 => "Short2",306 => "PCD",307=>"Basic Cost",308=>"Quo. Wov. EPM",309=>"PQ Vs Budget Wvn",310=>"Category Wise",311=>"BOM EPM",312=>"Monthly 3",313=>"MKT Vs Source",314=>"TT of Brack",
315 =>"Send To Ex. DB",316 =>"Barcode 128",317 =>"Barcode 128 v2",318 =>"Barcode 88X50",319 =>"Barcode M",320 =>"Direct Print",321=>"Send To Database",322=>"Barcode N",323=>"Final App",324=>"Program Wise",325=>"GIN2",326=>"Sales Wise Issue",327=>"GIN1",328=>"Bundle Sticker 128",329=>"Bundle Sticker 128 V2",330=>"Bundle Sticker QRCode",331=>"Sticker 10/Page 2",332=>"Bundle List",333=>"Sticker 10/Page",334=>"Barcode Generation",335=>"Prod. Wise3",336=>"Qou. Wov. Rpt 3",337=>"Barcode A",338=>"Yarn Test",339 =>"Print B18",
340=>"Show Weekly",341=>"V2",342=>"V3",343=>"Bundle QR Sticker 24/Page",344=>"Print letter 6",345=>"Requisition Print5",346=>"With Gate Pass",347=>"With Gate Pass2",348=>"WithOut G.Pass",349=>"WithOut G.Pass2",350=>"With Gate Pass JK",351=>"BOM Rpt4",352=>"UOM wise",353=>"Knitting Card 7",354=>"Print Wo",355=>"Printout",356 => "Knitting Card 9",357 =>"LC Forwarding",  358 =>"Pubali LCA",359 =>"Show 4",360 =>"Print Multi Delivery Challan Number", 361 =>"SCB LCA", 362 =>"Challan Wise 2",363 =>"Order Wise 4",364 =>"PHD",365 =>"Print Multiple Challan",366 =>"Sticker 1/Page V4",367 =>"Sticker 1/Page V5",368=>"Print letter 7",369=>"Print letter 8",370=>"Print B19",371=>"Lay Bundle",372 => "Print letter 9",
373 => "Bundle Sticker",374=>"Jamuna-LCA", 375=>"NCC-CF7" , 376=>"NCC-LCA",377=>"In-House",378=>"UCB-CF7",379=>"Sticker 10/Page 3", 380=>"128 V3", 381=>"MO Sheet2",382=>"Print Out5",383=>"Print B20",384=>"Receive 2",385=>" Receive Issue Summary",386=>"Issue 3",387=>"Issue Return",388=>"GIN6",389=>"Show 6",390=>"Barcode K",391 => "TT/FDD Letter 2", 392 => "LC Forwarding 2", 393 => "Bundle Sticker2",394 => "Forwarding letter", 395 => "Forwarding letter 2", 396 => "Forwarding letter 3", 397 => "Advance Payment letter", 398 => "Advance Acceptance", 399 => "Regular Acceptance letter", 400 => "BTB Acceptance Letter", 401 => "UNDERTAKING(IFIC)", 402 => "Payment letter", 403 => "MO Sheet 3",404 =>"Print B21",405 =>"Materials Dtls 2",406=>"=>Buyer Sub. Summary",407 =>"Order Book'",408 =>"Ratio Wise",409=>"QRCode 75x50",572 => "Knitting Card 8",
 577=>"Order Wise 2",578=>"Order Wise 3",579=>"Order Wise 2 Excel",580=>"Print Report 5", 581 => "Cost sheet",678=>"Letter Print",679=>"Print letter4",680=>"EXIM",681=>"BRAC",682=>"IFIC",683=>"ONE",684=>"DBBL",685=>"Undertaking Letter",686=>"LCA form For Brack Bank",687=>"LCA form For IFIC Bank",688=>"Re-Order Level",689=>"Summary4[Mkt]",690=>"Summary5[Mkt]",691=>"Fabric Requirement",692=>"Eastern Bank",693=>"Jamuna Bank",694=>"LC Opening Letter 2",695=>"UCB Bank",696=>"Print Grouping",697=>"Print letter5",698=>"Prime Bank",699=>"City Bank",700=>"Bank Asia",701=>"Shahjalal Islami Bank",702=>"Mutual Trust Bank",703=>"Dhaka Bank",704=>"Pubali Bank",705=>"HSBC" ,706=>"Without Collar Cuff", 707=>"With Challan" ,708=>"Item Wise2", 709=>"Item Wise3", 710 =>"Batch Wise", 711 =>"Machine Wise W/C Report", 712 =>"Show5", 713 =>"IBBL", 714 =>"QC Bundle",715 => "All Data",716 => "Stock Value",717=> "Lot Wise",718 =>"TT/FDD Letter",719 =>"Print B16",720=>"LC Opening Letter",721=>"FTT Letter",722 => "Sticker 128 v2",723 =>"Print B17",724 =>"Fabric Wise", 725 =>"All" ,726 =>"Receive",727 =>"Issue" ,728 =>"Recv-Issue",729 =>"All Excel",730=>"Budget Sheet",731=>"BTB REQ",732=>"PO Print",733=>"Issue 2",734=>"Total Value",735=>"Total Value G.A",736=>"Value B.W",737=>"Bank Forwarding and Bill of Exchange", 738 =>"Print AMT", 739 =>"Generate CS", 740 =>"Rcv. Excel", 741 =>"Iss. Excel", 742 =>"Iss2. Excel", 743 =>"CM Value", 744 =>"CM Value 2", 745 =>"Convert to Excel", 746 =>"Print Booking 7", 747 =>"Print Libas", 748 =>"Barcode 88X50 Y",749=>"Fabric Booking 2",750=>"Batch Wise2",751=>"PI Print",752 =>"CM Value 3", 753 =>"Lien Letter", 754 =>"Lien Letter2", 755 =>"Lien Export Lc App", 756 =>"Lien Lc App2", 757 =>"Check List", 758 =>"Print Report 7", 759 =>"Materials Dtls", 760=>"BTB CHEM", 761=>"BOM PCS", 762=>"Requisition Print6", 763=>"Graph",764=>"FSO(V2)", 765=>"BOM Rpt5", 766=>"By Cut-Off", 766=>"By Cut-Off", 767=>"Requisition Print7",768=>"Print 20");

asort($report_format);


//Button ID/Name

$report_name=array(1=>"Main Fabric Booking",2=>"Short Fabric Booking",3=>"Sample Fabric Booking -With order",4=>"Sample Fabric Booking -Without order",5=>"Multiple Order Wise Trims Booking",6=>"Country and Order Wise Trims Booking",7=>"Yarn Dyeing Work Order",8=>"Yarn Dyeing Work Order Without Order",9=>"Embellishment Work Order",10=>"Service Booking For AOP",11=>"Fabric Service Booking",12=>"Service Booking For Knitting",13=>"Yarn Dyeing Work Order",14=>"Yarn Service Work Order",15=>"Short Trims Booking",16=>"Sample Trims Booking With Order",17=>"Sample Trims Booking Without Order",18=>"Order Wise Budget Report",19=>"Export To Excel Report",20=>"Party Wise Grey Fabric Reconciliation",21=>"Embellishment Issue Entry",22=>"Pre-Costing",23=>"TNA Progress Report",24=>"Fabric TNA Progress Report",25=>"Multiple Job Wise Trims Booking",26=>"Multiple Job Wise Trims Booking V2",27=>"Grey Fabric Roll Issue",28=>"Yarn Dyeing Work Order With Out Lot",29=>"Yarn Dyeing Work Order With Out Order 2",30=>"Others Purchase Order",31=>"Embellishment Work Order V2",32=>"Price Quotation",33=>"Knit Grey Fabric Issue",34=>"Party Wise Yarn Reconciliation",35=>"Partial Fabric Booking",36=>"Daily Yarn Stock",37=>"Yarn Issue",38=>"Gate Pass Entry",39=>"Purchase Requisition",40=>"Order Wise RMG Production Status",41=>"Knitting Plan Report",42=>"Roll Wise Grey Fabric Delivery to Store",43=>"Pre-Costing V2",44=>"Monthly Buyer Wise Order Summary",45=>"Yarn Purchase Order",46=>"Capacity and Order Booking Status",47=>"Fabric Receive Status Report",48=>"Sample Requisition Fabric Booking -With order",49=>"Service Booking For AOP V2",50=>"Bundle Issued to Print",51=>"Bundle Receive From Print",52=>"Bundle Issued to Embroidery",53=>"Bundle Receive From Embroidery",54=>"Accessories Followup Report V2",55=>"Fabric Requisition For Batch 2",56=>"Batch Creation",57=>"Multiple Job Wise Short Trims Booking V2",58=>"Dyes And Chemical Issue Requisition",59=>"Daily Production Progress Report",60=>"Factory Monthly Production Report",61=>"Stationary Purchase Order",62=>"Cost Break Up Report V2",63=>"Style Wise Production Report",64=>"Order Wise Budget Sweater Report",65=>"Multi Job Wise Service Booking Knitting",66=>"Fabric Issue to Finish Process",67=>"Fabric Sales Order Entry",68=>"Doc. Submission to Bank",69 =>"Yarn Purchase Requisition", 70 => "Monthly Capacity Vs Buyer Wise Booked",71=>"Daily Knitting Production Report",72 => "Work progress report",73 => "Order Follow-up Report",74 => "Daily Ex-Factory Report",75 => "Accessories Followup Report [Budget-2]",76 => "Weekly Capacity and Booking Status",77 => "Fabric Production Status Report",78 => "Sewing Plan Vs Production",79 => "Cutting Status Report",80 => "Daily RMG Production status Report V2",81 => "Date Wise Production Report",82 => "Factory Monthly Production Report for Urmi",83 =>"Quick Costing",84 => "Closing Stock Report General",85 => "Buyer Inquiry Status Report",86 => "Garments Delivery Entry",87=>"Order Wise Production Report",88 =>"Planning Info Entry For Sales Order", 89 =>"Multiple Job Wise Embellishment Work Order",90=>"Sample Requisition Fabric Booking -Without order",91=>"Wash Dyes Chemical Issue",92=>"Woven Short fabric Booking",93=>" Purchase Recap",94=>"Order and Color Wise Finish Fabric Stock Report",95=>"Dyeing Production Report-V3",96=>"Export CI Statement",97=>"Woven Order Wise Budget",98=>"Daily Yarn Demand Entry",99=>"Wash Dyes Chemical Issue",100=>"Batch Creation For Gmts. Wash",101=>"Wash Recipe Entry",102=>"Wash Dyes And Chemical Issue Requisition",103=>"Wet Production",104=>"Dry Production",105=>"Wash Delivery",106=>"Wash Bill Issue",107=>"Wash Delivery Return",108=>"Statement of Total Export Value and CM",109=>"Fabric Production Status Report - Sales Order",110=>"Closing Stock Report Embroidery",111=>"Embroidery Item Ledger",112=>"Bank Liability Position As Of Today",113=>"Order Wise Grey Fabrics Stock Report",114=>"Fabric Receive Status Report 2",115=>"BTB/Margin LC",116=>"Daily Yarn Issue Report",117=>"Woven Cut and Lay Entry Ratio Wise",118=>"Cut and Lay Entry Ratio Wise 4",119=>"Scrap Material Issue",120=>"Weekly Capacity and Order Booking Status V2",121=>"Shipment Schedule",122=>"Pre-Costing woven",123=>"Embellishment Receive Entry",124=>"Daily Gate In And Out Report",125=>"Woven Finish Fabric Receive",126=>"Woven Finish Fabric Issue",127=>"Woven Finish Fabric Receive Return",128=>"Woven Finish Fabric Roll Issue",129=>"Woven Finish Fabric issue return",130=>"Woven Finish Fabric Transfer Entry",131=>"Woven Finish Roll Issue Return",132=>"Dyes And Chemical Purchase Order",133=>"Knitting Bill Issue",134=>"Daily Cutting And Input Inhand Report",135=>"Order Wise Finish Fabric Stock" ,136=>"Dyeing Report",137=>"Sample Followup Report- Sweater",138=>"Woven Partial Fabric Booking",139 =>"Closing Stock Dyes and Chemical",140 =>"Knitting Production",141=>"Sourcing Post Cost Sheet",142=>"Sample Requisition With Booking",143=>"Date Wise Dyes Chemical Receive Issue",144 => "Closing Stock Of Trims",145 => "Cut and Lay Entry Ratio Wise 3",146=>"Roll Splitting Before Issue",147=>"Comparative Statement",148 => "Job Wise Cost Analysis Report-Woven",149 => "Date Wise Production Report [CM]",150=>"Fabric Sales Order Entry v2",151=>"Export Pro Forma Invoice",152=>"Demand For Accessories",153=>"Wash Received and Delivery Statement",154=>"Unit Wise Production 2", 155=>"Sales Contract Entry", 156=>"Item Issue Requisition", 157=>"Yarn Purchase Requisition Follow Up Report", 158=>"Production Summary [Fabric And Garments]",159=>"Hourly Production Monitoring Reports",160=>"Sample Production Report",161=>"Pre-Costing V3",162=>"Batch wise Dyeing and Finishing Cost",163=>"SubCon Dye And Finishing Delivery",164=>"Knit Finish Fabric Roll Issue",165 =>"Finish Fabric Closing Stock", 166=>"Style Wise Finish Fabric Status",167=>"Finish Fabric Roll Delivery To Store",168=>"Knitting Plan Report[Sales]",169=>"Grey Roll Issue to Process",170=>"Sample Delivery Entry", 171=>"Knit Grey Fabric Roll Receive",172=>"Knit Finish Fabric Roll Receive",173=>"Trims Order Receive",174=>"Trims Delivery Entry", 175=>"Trims Bill Entry", 176=>"Short Fabric Booking Approval New",177=>"Short Fabric Booking Approval New",178=>"Pre-Costing Approval",179=>"Dyes And Chemical Issue",180=>"Textile TNA Progress Report",181=>"Trims Issue",182=>"General Item Issue",183 =>"Pro Forma Invoice V2",184 =>"Pro Forma Invoice Approval Status Report",185 =>"Order wise Production and Delivery Report",186 =>"Date Wise Finish Fabric Receive Issue",187 =>"Date Wise Item Receive and Issue",188 =>"MIS Report", 189 => "Import Document Acceptance", 190 => "Style Wise Production Summary", 191 => "Trims Receive Entry",192 =>"Bundle Wise Sewing Input",193 => "Work Order [Booking] Report",194 => "General Item Receive" ,195=>"Multi Job Wise Service Booking Dyeing",196=>"Store item List",197=>"Order Wise Grey Fabrics Stock Report V2",198=>"Color and Size Breakdown Report" ); 
asort($report_name);

//22.09.2015
$info_type=array(1=>"Recv Date Wise",2=>"Order Wise",3=>"Transaction Date Wise");
$report_date_catagory=array(1=>"Country Ship Date Wise",2=>"Pub Ship Date Wise",3=>"Org. Ship Date Wise",4=>"Po Insert Date Wise", 5=> "Extended Ship Date");
//14.1.18
$report_date_consolidated=array(1=>"Country Ship Date",2=>"Public Ship Date");
//20.11.17
$fabric_issue_basis=array(1=>"Batch Wise",2=>"WO Wise");
//06.10.2015
$letter_type_arr = array(1 => "Shipping Guarantee", 2 => "Delivery of Consignment", 3 => "Sales Contact Lien", 4 => "Export LC Lien", 5 => "Export LC Amendment", 6 => "Export LC Replace", 7 => "Forwording Letter", 8 => "Acceptance Letter", 9 => "BTB LC Open");

//Add 31.10.2015 by sohel for HOME_PAGE_PRIVILEDGE
$home_page_module=array(1=>'Graph',2=>'Pending',3=>'Inventory',4=>'Merchant',5=>'Merchants');
//18.11.2015 For sample Booking None order
$body_type_arr=array(1=>'Plain Collar',2=>'Bit Collar',3=>'Kushikata',4=>'Plain Cuff',5=>'Bit Cuff');

$use_for=array(1=>'Development',2=>'Repair & Maintenance',3=>'Operation');

$garments_item_prefix=array(
1=>"TSLS",
2=>"TSSS",
3=>"PSLS",
4=>"PSSS",
5=>"TT",
6=>"TSA",
7=>"HOD",
8=>"HNL",
9=>"TSSL",
10=>"RAG",
11=>"TSHN",
12=>"SCRF",
14=>"BLZ",
15=>"JKT",
16=>"NW",
17=>"MD",
18=>"LLD",
19=>"GD",
20=>"PNT",
21=>"SP",
22=>"TRSR",
23=>"PJM",
24=>"RMPSS",
25=>"RMPLS",
26=>"RMPSL",
27=>"RMP",
28=>"LGG",
29=>"3QTR",
30=>"SKRT",
31=>"JMPS",
32=>"CAP",
33=>"TTPJM",
34=>"TSPJM",
35=>"JP",
36=>"BAG",
37=>"BRA",
38=>"UWB",
39=>"SS",
40=>"SNGT",
41=>"TSNGT",
42=>"BXR",
43=>"STBXR",
44=>"TBXR",
45=>"JBXR",
46=>"PTY",
47=>"SB",
48=>"CB",
49=>"SRTB",
50=>"MNB",
51=>"BKN",
52=>"LNGR",
53=>"BKR",
54=>"UW",
60=>"PSKS",
61=>"RSKS",
62=>"JSKS",
63=>"HGSKS",
64=>"SSKS",
65=>"TSKS",
66=>"TGTSKS",
67=>"SWTP",
68=>"SW",
69=>"JT",
70=>"LP",
71=>"PP",
72=>"BLR",
73=>"ST",
74=>"LG",
75=>"LSB",
76=>"TTB",
77=>"UWT",
78=>"WPR",
79=>"SBG",
80=>"RMPLS",
81=>"RMPLS",
82=>"RMPLS",
83=>"BABY",
84=>"TQPT",
85=>"LSPT",
86=>"TNP",
87=>"MXD",
88=>"LG",
89=>"GTNT",
90=>"VNT",
91=>"RTJ",
92=>"STJ",
93=>"PJ",
94=>"LBJ",
95=>"JGNG",
96=>"MNW",
97=>"SPS",
98=>"RMPR"
);

$body_part_prefix=array
(1=>"MFT",
2=>"CLR",
3=>"CF",
4=>"RIB",
5=>"FLP",
6=>"HD",
7=>"PKT",
8=>"RIB",
9=>"SLV",
10=>"BP",
11=>"FP",
12=>"FF",
13=>"BND",
4=>"MFT",
15=>"MFT",
16=>"MFT",
17=>"MFT",
18=>"SLDR",
19=>"HDL",
20=>"MFB",
21=>"PKT",
22=>"RIB",
23=>"WB",
24=>"WB",
25=>"BL",
26=>"BY",
27=>"TAB",
28=>"ASBL",
29=>"FLY",
30=>"PKT",
31=>"BF",
32=>"LOOP",
33=>"SF",
34=>"PKT",
35=>"PKT",
36=>"FT",
37=>"WB",
38=>"TAPE",
39=>"PKT",
40=>"PLKT",
41=>"NT",
42=>"PPG",
43=>"PB",
44=>"NB",
45=>"NI",
46=>"DST",
47=>"CI",
48=>"MI",
49=>"TM",
50=>"WI",
51=>"DL",
52=>"SS",
53=>"FY",
54=>"",
55=>"NR",
56=>"IN",
57=>"SL",
58=>"HM",
59=>"NK",
60=>"BM",
61=>"BT",
62=>"PKT",
63=>"",
64=>"PKT",
65=>"PKT",
66=>"PKT",
67=>"PKT",
68=>"CT",
69=>"IFB",
70=>"FTP",
71=>"ST",
72=>"TF",
73=>"PKT",
74=>"WR",
75=>"NBD",
76=>"BOW",
77=>"",
78=>"NTL",
79=>"ZUB",
80=>"",
81=>"BTP",
82=>"FS",
83=>"CP",
84=>"",
85=>"",
86=>"",
87=>"",
88=>"",
89=>"",
90=>"",
91=>"",
92=>"",
93=>"SLV",
94=>"SLV",
95=>"",
96=>"",
97=>"CS",
98=>"HEM",
99=>"BF",
100=>"BP",
101=>"",
102=>"HEM",
103=>"",
104=>"PLKT",
105=>"PKT",
106=>"",
107=>"FLAP",
108=>"HEM",
109=>"FP",
110=>"",
111=>"",
112=>"HEM",
113=>"LOOP",
114=>"",
115=>"PPG",
116=>"RIB",
117=>"",
118=>"HEM",
119=>"PKT",
120=>"BRA",
121=>"",
122=>"",
123=>"",
124=>"",
125=>"",
126=>"BNP",
127=>"",
128=>"WB",
129=>"",
130=>"",
131=>"",
132=>"",
133=>"",
134=>"",
135=>"",
136=>"PLKT",
137=>"KHB",
138=>"",
139=>"",
140=>"SLV",
141=>"STP",
142=>"",
143=>"",
144=>"HTP",
145=>"PKT",
146=>"PKT",
147=>"",
148=>"",
149=>"",
150=>"",
151=>"",
152=>"",
153=>"SLV",
154=>"",
155=>"",
156=>"",
157=>"",
158=>"",
159=>"",
160=>"PPG",
161=>"",
162=>"YS",
163=>"",
164=>"MFG",
165=>"");

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

$home_page_array[1][1]['name']="Order In Hand Qnty";
$home_page_array[1][1]['lnk']="order_in_hand_qnty";
$home_page_array[1][2]['name']="Order In Hand Value";
$home_page_array[1][2]['lnk']="order_in_hand_val";
$home_page_array[1][3]['name']="Company Key Performance";
$home_page_array[1][3]['lnk']="company_kpi";
$home_page_array[1][4]['name']="Today Hourly Production";
$home_page_array[1][4]['lnk']="Today_Hourly_Production";
$home_page_array[1][5]['name']="Stack Bar Qty";
$home_page_array[1][5]['lnk']="stack_qnty";
$home_page_array[1][6]['name']="Stack Bar Value";
$home_page_array[1][6]['lnk']="stack_value";
$home_page_array[1][7]['name']="Sales Forecast Qnty";
$home_page_array[1][7]['lnk']="sales_forecast_qnty";
$home_page_array[1][8]['name']="Sales Forecast Value";
$home_page_array[1][8]['lnk']="sales_forecast_value";
$home_page_array[1][9]['name']="Last 30 days Knit Prod Trend";
$home_page_array[1][9]['lnk']="30_days_knit_eff_trend";
$home_page_array[1][10]['name']="Last 30 Days Dyeing Prod Trend";
$home_page_array[1][10]['lnk']="30_days_dyen_eff_trend";
$home_page_array[1][11]['name']="Last 30 days Sew Effi Trend";
$home_page_array[1][11]['lnk']="30_days_sewn_eff_trend";
$home_page_array[1][12]['name']="30 Days Idle Lines";
$home_page_array[1][12]['lnk']="30_days_idle_lines";
$home_page_array[1][13]['name']="30 Days Idle RMG Worker";
$home_page_array[1][13]['lnk']="30_days_idle_rmg_worker";
$home_page_array[1][14]['name']="30 days Idle Knit Machines";
$home_page_array[1][14]['lnk']="30_days_idle_knit_mchn";
$home_page_array[1][15]['name']="30 days Idle Dyeing Machines";
$home_page_array[1][15]['lnk']="30_days_idle_dyen_mchn";
$home_page_array[1][16]['name']="Capacity Status In Minute";
$home_page_array[1][16]['lnk']="capacity_status_smv";
$home_page_array[1][17]['name']="Capacity and Order Booked";
$home_page_array[1][17]['lnk']="capacity_booked_qty";
$home_page_array[1][18]['name']="Gmts Reject Alter Percentage";
$home_page_array[1][18]['lnk']="gmts_reject_alter_per";
$home_page_array[1][19]['name']="Fabric & Order Analysis";
$home_page_array[1][19]['lnk']="fabric_order_analysis";
$home_page_array[1][20]['name']="Finishing Capacity & Achievment(Iron)";
$home_page_array[1][20]['lnk']="finishing_capacity_achievment_iron";

$home_page_array[1][21]['name']="Capacity Comparison In Hour";
$home_page_array[1][21]['lnk']="capacity_comparison_in_hour";

$home_page_array[1][22]['name']="Daily Finishing Capacity & Achievment (Iron)";
$home_page_array[1][22]['lnk']="daily_finishing_capacity_achievment_iron";

$home_page_array[1][23]['name']="Dyeing Capacity VS Load";
$home_page_array[1][23]['lnk']="dyeing_capacity_vs_load";

$home_page_array[1][24]['name']="Knitting Capacity VS Load";
$home_page_array[1][24]['lnk']="knitting_capacity_vs_load";

$home_page_array[1][25]['name']="Count Wise Yarn Stock";
$home_page_array[1][25]['lnk']="yarn_stock_graph";
$home_page_array[1][26]['name']="Yarn Consumption";
$home_page_array[1][26]['lnk']="yarn_consumption_grap";
$home_page_array[1][27]['name']="Today Hourly Production 2";
$home_page_array[1][27]['lnk']="today_production_graph_working_company";
$home_page_array[1][28]['name']="Total Activities";
$home_page_array[1][28]['lnk']="total_activities_auto_mail";
$home_page_array[1][29]['name']="Order In Hand Qnty PSD";
$home_page_array[1][29]['lnk']="order_in_hand_qnty_PSD";

$home_page_array[1][30]['name']="Textile Graph";
$home_page_array[1][30]['lnk']="textile_graph";

$home_page_array[1][31]['name']="Garments Graph";
$home_page_array[1][31]['lnk']="garments_graph";
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
$home_page_array[1][41]['name'] = "Monthly Ex-factory Status";
$home_page_array[1][41]['lnk'] = "monthly_ex_factory_status";
$home_page_array[1][42]['name'] = "Monthly First Inspection Alter And Damage Percentage";
$home_page_array[1][42]['lnk'] = "monthly_first_inspection_alter_and_damage_percentage";



$home_page_array[2][1]['name']="Shipment Pending Report";
$home_page_array[2][1]['lnk']="shipment_pending_report";
$home_page_array[2][2]['name']="Country Wise Shipment Pending Report";
$home_page_array[2][2]['lnk']="country_wise_shipment_pending_report";
$home_page_array[2][3]['name']="Quotation Submission Pending";
$home_page_array[2][3]['lnk']="quotation_submission_pending";
$home_page_array[2][4]['name']="Quotation Finalization Pending";
$home_page_array[2][4]['lnk']="buyer_inquiry_status_report";
$home_page_array[2][5]['name']="Yarn Issue Pending";
$home_page_array[2][5]['lnk']="yarn_issue_pending";
$home_page_array[2][6]['name']="PP Sample Approval Pending";
$home_page_array[2][6]['lnk']="PP_sample_approval_pending";
$home_page_array[2][7]['name']="Ex-Factory vs Commercial Activities";
$home_page_array[2][7]['lnk']="Ex_Factory_vs_commercial_activities";
$home_page_array[2][8]['name']="Data";
$home_page_array[2][8]['lnk']="data";
$home_page_array[2][9]['name']="Data";
$home_page_array[2][9]['lnk']="data";
$home_page_array[2][10]['name']="Data";
$home_page_array[2][10]['lnk']="data";

$home_page_array[3][1]['name']="Data";
$home_page_array[3][1]['lnk']="Data";
$home_page_array[3][2]['name']="Data";
$home_page_array[3][2]['lnk']="Data";
$home_page_array[3][3]['name']="Data";
$home_page_array[3][3]['lnk']="Data";
$home_page_array[3][4]['name']="Data";
$home_page_array[3][4]['lnk']="Data";
$home_page_array[4][1]['name']="Data";
$home_page_array[4][1]['lnk']="Data";
$home_page_array[4][2]['name']="Data";
$home_page_array[4][2]['lnk']="Data";
$home_page_array[4][3]['name']="Data";
$home_page_array[4][3]['lnk']="Data";
$home_page_array[4][4]['name']="Data";
$home_page_array[4][4]['lnk']="Data";
$home_page_array[4][1]['name']="Data";
$home_page_array[4][1]['lnk']="Data";
$home_page_array[5][2]['name']="Data";
$home_page_array[5][2]['lnk']="Data";
$home_page_array[5][3]['name']="Data";
$home_page_array[5][3]['lnk']="Data";
$home_page_array[5][4]['name']="Data";
$home_page_array[5][4]['lnk']="Data";


$home_graph_arr___ = array(
	1 => 'dash_board.php',
	2 => 'today_production_graph.php',
	3 => 'trend_monthly_graph.php',
	4 => 'trend_daily_graph.php',
	5 => 'graph_grp.php',
	6 => 'dash_board_2.php',
	7 => 'graph_public_ship_date.php',
	8 => 'graph_lc_style_company.php',
	9 => 'graph_index.php'
);


$home_graph_arr=array(
	1 => 'monthly_order_export.php',
	2 => 'graph_lc_style_company.php',
	3 => 'graph_public_ship_date.php',
	4 => 'graph_grp.php',
	5 => 'monthly_order_value.php',
	6 => 'monthly_order_value_minute.php',
	7=>'monthly_order_export_with_value.php'
);

$cost_components=array(1=>'Fabric Cost',2=>'Trims Cost',3=>'Embell.Cost',4=>'Gmts.Wash',5=>'Commission',6=>'Commercial Cost',7=>'Lab Test',8=>'Inspection Cost',9=>'Gmts Freight Cost',10=>'Currier Cost',11=>'Certificate Cost',12=>'Depreciation & Amortization',13=>'Interest',14=>'Income Tax',15=>'Others Cost');
$sample_stage=array(1=>"After Order Place",2=>"Before Order Place",3=>"R&D");

 /*$general_item_category =array(4=>"Accessories",8=>"Spare Parts",9=>"Machinaries",10=>"Other Capital Items",11=>"Stationaries",15=>'Electrical',16=>'Maintenance',17=>'Medical',18=>'ICT Equipment',19=>'Print & Publication',20=>'Utilities & Lubricants',21=>'Construction Materials',22=>'Printing Chemicals & Dyes',32=>'Vehicle Components',34=>'Painting Goods',35=>'Sanitary Goods',36=>'Safety and Security',37=>'Food and Grocery',38=>'Needles',39=>'WTP and ETP Machinery',40=>'Spare Parts - Mechanical',41=>'Spare Parts - Electrical',68=>'ICT Consumable',23=>'Dyes Chemicals & Auxilary Chemicals',
	33=>'Others',
	44=>'Packing Materials',
	45=>'Factory Machinery',
	46=>'Iron Dril Machinery Machinery',
	47=>'Felt Machinery',
	48=>'Dosing Motor Pump',
	49=>'Centrifugal Water Pump',
	50=>'Flack Machinery',
	51=>'Bag Sewing Machine',
	52=>'Batter Cabinet',
	53=>'TV',
	54=>'Finishing Machinery',
	55=>'Compresser Machinery',
	56=>'Sewing Machinery',
	57=>'Embroidery Machinery',
	58=>'Washing Machinery',
	59=>'Cutting Machinery',
	60=>'Knitting Machinery',
	61=>'Printing Machinery',
	62=>'Laboratory Machinery',
	63=>'PMD Machinery',
	64=>'Dyeing Machinery',
	65=>'Oil and Gas Generator',
	66=>'Fabric Spreader Machinery',
	67=>'Consumable',
	69=>'Furniture',
	70=>'Fixture',
	89=>'AC Plant',
	90=>'Chiller',
	91=>'Substation',
	92=>'Pump',
	93=>'Cooling Tower',
	94=>'Vehicle',
	99=>'Cleaning Goods');*/
	$general_item_category = return_library_array("select category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 and category_type=1 order by short_name", "category_id", "short_name");


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

	$body_part_type=array(
	1 =>"Top",
	20 =>"Bottom",
	30 =>"Others",
	40 =>"Flat Knit",
	50 =>"Cuff"
	);

$sample_checklist_set=array(1=>"Design Sketch",20=>"Basic Construction with design sketch",40=>"Physical Garment",60=>"BOM Details",80=>"Trim card with Trims",100=>"PU Cup Available",120=>"Plastichat",140=>"Wire Available",160=>"Lace Image",180=>"Lace Swatch",200=>"Lace width",220=>"Size Ratio",240=>"Size Range",260=>"Size Split",280=>"Fabric Specification",300=>"Shrinkage",320=>"Fabric Stretch",340=>"Fabric Lay way",360=>"Moldability",380=>"Skewing",400=>"Bowing",420=>"Special Decorative Trims",440=>"Pattern");
$feeding_arr=array(1=>'Knit',2=>'Track',3=>'Loop'); //As Per Reza

//$lab_test_agent=array(1=>"Sgs",2=>"Attex",3=>"Bureau Veritas",4=>"ITS");
$lab_test_agent = array(1 => "SGS", 2 => "AITEX", 3 => "Bureau Veritas", 4 => "ITS", 5 => "UL VS Bangladesh Ltd", 6 => "Consumer Testing Laboratories", 7 => "Intertek",8 => "TÜV SÜD Bangladesh",9 => "Modern Testing Services",10 => "TUV RHEINLAND BANGLADESH",11 => "KOTITI Bangladesh",12 => "QTEC",13=>"SATRA",14=>"Geo Chem",15=>"HOHENSTEIN");
asort($lab_test_agent);
$sample_delivery_basis=array(1=>"Requisition",2=>"Sample for order",3=>"Sample booking without order");
$template_type_arr=array(1=>'Garments',2=>'Textile');
$quotation_status=array(1=>"Available",2=>"Confirmed",3=>"Closed");

$time_weight_panel=array(1=>"1st Front",2=>"2nd Front",3=>"Back",4=>"Right Sleeve",5=>"Left Sleeve",6=>"Collar / Neck",7=>"Neck Band",8=>"Cardigan Band",9=>"Strap / Zip Facing",10=>"Hood",11=>"Pocket",12=>"Pocket Rib",13=>"Pocket Bag",14=>"Linking Yarn",15=>"Others",16=>"Button Placket",17=>"Hood Rib",18=>"Pocket Flap",19=>"Neck Tape",20=>"Zipper Piping",21=>"Accessories(Teeth)",22=>"Accessories (Ear)", 23 => "Moon", 24 => "Loop", 25 => "Belt", 26 => "Embroidery Applique",27 => "Armhole Piping", 28 => "Body Placket", 29 => "Pocket Placket", 30 => "Bottom Piping",31 => "Shoulder Potty", 32 => "Label Carrier", 33 => "Badge Carrier", 33 => "Badge Carrier", 34 => "Cut Out Rib", 35 => "Cap [Body Part]", 36 => "Scarf [Body Part]",37 => "Ruffle",38 => "Cuff Rib",39 => "Body Piping",40 => "Pompom",41 => "Pant", 42 => "Pant Neck");

$development_no=array(1=>"1st Development",2=>"2nd Development",3=>"3rd Development",4=>"4th Development",5=>"5th Development",6=>"6th Development",7=>"7th Development",8=>"8th Development",9=>"9th Development",10=>"10th Development");

$short_booking_cause_arr = array(1=>"Merchandising",2=>"Technical",3=>"Yarn",4=>"Knitting",5=>"Dyeing",6=>"Dyeing Finishing",7=>"Textile Quality",8=>"Color Lab",9=>"Sample And RND Textile",10=>"Finish Fabric Store",11=>"AOP",12=>"Dyed Yarn",13=>"Placement Print",14=>"Embroidery",15=>"Garments Wash",16=>"Garments Unit",17=>"Buyer/Buying House",18=>"Commercial",19=>"Supplier",20=>"Others",21=>"Miscellaneous",22=>"CPB Dyeing");


$wash_type=array(1 => "Wet Process", 2 => "Dry Process", 3 => "Laser Design");
asort($wash_type);
$wash_wet_process=array(1=>"Garments Wash",2=>"Enzyme Wash",3=>"Enzyme stone Wash",4=>"Bleach Wash",5=>"Acid Wash",6=>"Random Wash",7=>"Towel Bleach",8=>"Reactive Dyeing",9=>"Pigment Dyeing",10=>"Pluorescent Dyeing",11=>"Cool Dyeing/ Mould",12=>"Tie Dye[Signle Colour] ",13=>"Dis Chargeable Dyeing/ Fashion Dyeing",40=>"Other",41=>"Desizing",42=>"Neutral",43=>"Cleaning",44=>"Tint",45=>"Softener",46=>"Catanizer",47=>"Dyeing",48=>"Soaping",49=>"Fixing",50=>"Binder",51=>"Deep Dye[Double Part]",52=>"Normal Wash",53=>"Heavy Wash",54=>"Vintage Wash",55=>"Bobble Wash [With Stone]",56=>"Aggressive Vintage Wash",57=>"Burn Out",58=>"Snow Wash",59=>"Sand Wash",60=>"Optic Wash",61=>"Galaxy Wash",62=>"Cool Pigment Dyeing",63=>"Oil Wash",64=>"Cool Dyeing Fluorescent",65=>"Aqua Wash",66=>"Resyspec Eco Marble",67=>"Rubber Ball Wash",68=>"Deep Dye [Single Part]",69=>"Tie Dye [Multi Colour]",70=>"Sausage Dyeing",71=>"Fluorescent Pigment Dyeing",72=>"Stone Dye [Multi Colour]",73=>"Stone Dye [Single Colour]",74=>"Antarctic Wash",75=>"Garments Dyeing (Direct)",76=>"Garments Dyeing (Reactive)",77=>"Nebu Dyeing",78=>"Potash",79=>"Scouring",80=>"Acetic Acid Wash" ,81=>"Cetric Acid Wash",82=>"PP Bleach Wash",83=>"PP  Neutral",84=>"Bleach Neutral",85=>"PP Bleach Neutral");
asort($wash_wet_process);
$wash_dry_process=array(1=>"Whisker",2=>"Hand Sand",3=>"PP Spray",4=>"Pigment Spray",5=>"Tagging",6=>"Destroy",7=>"3D",8=>"Tieing",9=>"Grinding",10=>"Resing Depping Spray",11=>"Wrinkle",30=>"Others",31=>"Air Blow Out",32=>"PP Rubbing", 33=>"All over PP Rubbing",34=>"All over PP Spray",35=>"Dryer",36=>"Final Dryer",37=>"Hydro",38=>"Oven Curing",39=>"Final hydro",40=>"Resin Spray",41=>"Resin Dip");
asort($wash_dry_process);
$wash_laser_desing=array(1=>"Laser Whisker",2=>"Laser Brush",3=>"Laser Destroy",4=>"Laser Chemo Print",5=>"Laser Marking",6=>"All Over Laser Print",20=>"Others");

//Wash Module ====================
$print_type = array(1 => "Pigment",2 => "Discharge",3 => "Glitter", 4 => "Burnout", 5 => "Reactive",6=>"Disperse",7=>"Acid Print",8=>"Digital Print",9=>"Glue in the dark",10=>"Metallic");
asort($wash_laser_desing);

//Dyeing Lab
$lab_section=array(1 => "FD", 2 => "YD");
asort($lab_section);

$dyeinglab_dyetype_arr = array(1 => "Acid", 2 => "Basic", 3 => "Discharge", 4 => "Disperse", 5 => "Direct", 6 => "Highfast/Discharge", 7 => "Disperse with OBA", 8 => 'Hsublimn Disperse', 9 => 'SHFast Disperse', 10 => 'Reactive/SH Fast', 11 => 'Basic with OBA', 12 => 'Low Temp Disperse', 13 => 'Basic/Discharge', 14 => 'Basic/Hfast Disperse', 15 => 'Reactive with OBA', 16 => 'Basic /Disperse', 17 => 'Basic/Acid', 18 => 'Reactive', 19 => 'Disperse/Reactive', 20 => 'Acid/Reactive', 21 => 'Basic/Reactive', 22 => 'Disperse/Discharge', 23 => 'Washing', 24 => 'Reactive/High Fast', 25 => 'Acid with OBA', 26 => 'Reactive CPB');
asort($dyeinglab_dyetype_arr);

$dyeinglab_dyecode_arr = array(1 => "A", 2 => "B", 3 => "C", 4 => "D", 5 => "E", 6 => "F", 7 => "G", 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T', 21 => 'U', 22 => 'V', 23 => 'W', 24 => 'X', 25 => 'Y', 26 => 'Z');
asort($dyeinglab_dyecode_arr);


$gauge_arr=array(1=>"1.5 GG",2=>"3 GG",3=>"5 GG",4=>"7 GG",5=>"10 GG",6=>"12 GG",7=>"14 GG",8=>"9 GG",9=>"2 GG",10=>"3.5 GG",11=>"16 GG");

//,12=>"3.5 GG",13=>"5 GG",14=>"7 GG",15=>"9 GG",16=>"10 GG",17=>"12 GG",17=>"14 GG",17=>"16 GG"
$stripe_type_arr = array(1=>"Feeder Stripe",2=>"Engineering Stripe");

$dyeinglab_shadeBrightness_arr = array(1 => "Light", 2 => "Medium", 3 => "Dark", 4 => "Extra Dark");
asort($dyeinglab_shadeBrightness_arr);
$sample_delivery_source = array(1=>"Production",2=>"Sample Delivery");

$cnf_import_bill_head_arr = array(1 => "Vat Payment", 2 => "Document Processing Fee", 3=>"Scanning Fee", 4=>"Laber Charge", 5=>"Landing Charge", 6=>"TR Challan", 7=>"Amendment / Short Shipment", 8=>"Association Fees", 9=>"Night Custom Miscellineous", 10=>"Goods Unloading Extra Labour", 11=>"Receiver and Table Persons", 12=>"Pass Book Entry", 13=>"Miscellineous Expenses", 14=>"Transport Charge", 15=>"HAWB Bill Charge", 16=>"Biman Loader Charge", 17=>"Welfare Fund DT", 18=>"Special Permission for Late Stuffing Charge", 19=>"Agency Commission Invoice Value Maximum", 20=>"Agency Commission Invoice Value Minimum", 21=>"Custom Tex/DF/Vat", 22=>"Out Pass", 23=>"100% Examination", 24=>"Contaner Charge  Mlo", 25=>"D/C Mark Customs", 26=>"D/O.P/O & B/L Verify", 27=>"Extra Misc Expenses.", 28=>"Forklift/Highster/Crane", 29=>"Free of Cost", 30=>"Igm Office Add", 31=>"Next Per Truck", 32=>"Nill wrong Illegible Marks Expanses", 33=>"Noc Charge Freight Forwarding.", 34=>"Port Bill", 35=>"Programmer", 36=>"Special Delivery", 37=>"Statement Parpas");

$cnf_export_bill_head_arr = array(1 => "Income Tax", 2 => "Scanning fee", 3=>"Document Processing fee", 4=>"Vat(1+2+3)", 5=>"Laber Charge", 6=>"Landing Charge",7=>"TR Challan", 8=>"Amendment / Short Shipment", 9=>"Association Fees", 10=>"Night Custom Miscellineous", 11=>"Goods Unloading Extra Labour", 12=>"Miscellineous Expenses", 13=>"Agency Commission Invoice Value", 14=>"Custom Tex/DF/Vat", 15=>"Shorting Charge", 16=>"Special Permission");

$priority_array=array(1=>"Normal",2=>"Urgent",3=>"Critical");
$requisition_array=array(1=>"Standard Procurement",2=>"Cash Procurement",3=>"Emergency Procurement",4=>"Direct Procurement");

$wo_type_array=array(1=>"Local",2=>"Import");

$sew_fin_reject_defect_type = array(1 =>"Color Spot", 2 =>"Crease Mark", 3 => "Dirty Spot", 4 => "Distinguish", 5 => "Dusted", 6 => "Dyeline", 7 => "Emb Rejection 
", 8 => "Embroidery" , 9 =>"Fabric (Z) Hole", 10 =>"HTS Problem Cutting", 11 => "HTS Problem Finishing", 12 => "Iron Spot", 13 => "Knot", 14 => "M/C Knife  Cut", 15 => "Measurement (+-)", 16 => "Needle Cut", 17 =>"Oil Spot", 18 => "Part Mistake", 19 => "Part Shade", 20 => "Patta", 21 => "Pleat", 22 => "Print Reject
", 23 => "Runing Shade", 24 => "Scissor Cut", 25 =>"Slub", 26 => "Softner Mark", 27 => "Tag Gun Rej", 28 => "Twist", 29 => "Uneven Dyeing", 30 => "Yarn 
Missing", 31 => "Yarn Contamination");

$lc_charge_arr=array(1=>"Lc Opening",2=>"Lc Amendments",3=>"Lc Acceptance");
$sms_item_array=array(1=>"Daily ERP Activities",2=>"Export Information",3=>"Daily Ex-factory Schedule");
asort($sms_item_array);


$cutting_qc_reject_type = array(1 => "Crease Mark", 2 => "Dirty Spot", 3 => "Hole", 4 => "Knitting Defect", 5 => "Dyeing Spot", 6 => 'Others', 7=> 'Stick Mark', 8 => 'Miss yarn', 9 => 'Contamination', 10 => 'Slub', 11 => 'Oil Spot', 12 => 'Needle Line', 13 => 'Needle Hole', 14 => 'Lycra Out', 15 => 'Dia Mark', 16 => 'Knot', 17 => 'Miss Cut', 18 => 'Over Compusser', 19 => 'Un-Even Dyenig', 20 => 'Patta', 21 => 'Loop Out', 22 => 'Grease Spot', 23 => 'Thick and Thin', 24 => 'Gsm Cut', 25 => 'Fabric Join', 26 => 'Reject For Dia Short', 27 => 'Crimple Mark', 28 => 'Broken', 29 => 'Marker Line', 30 => 'Shape Uneven', 31 => 'Numb Spot', 32 => 'Miss Print', 33=> 'Color Yarn', 34=> 'Yellow Mark', 35=> 'Shade Bar', 36=> 'Loose Warp', 37=> 'Thick Yarn', 38=> 'Stop Mark', 39=> 'End Out', 40=> 'Running Shade', 41=> 'Self Edge', 42=> 'Niddle Mark', 43=> 'Spot', 44=> 'Marker Pen Mark', 45=> 'AOP Problem', 46=> 'Cutting Problem', 47=> 'Rub Mark', 48=> 'Stander Hole', 49=> 'Color Spot', 50=> 'Yarn Messing', 51=> 'Foreign Yarn');
asort($cutting_qc_reject_type);


?>


