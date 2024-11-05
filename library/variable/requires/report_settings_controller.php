
<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../../includes/common.php');

$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$format_name = $_REQUEST['format_name'];
$txt_report_button_name = $_REQUEST['txt_report_button_name'];
$report_button_wise_user_id = $_REQUEST['txt_report_button_wise_user_id'];

$action = $_REQUEST['action'];

function getBtnMaping($index)
{
	// echo 'getBtnMaping';die;
	$report_format_id_arr[1] = "1,2,3,4,5,6,7,13,28,39,45,53,73,93,78,84,85,129,193,269,280,304,719,723,339,370,383,404,419,426,432,452,786,502,437,833,865,38"; // Menu ID---- Main Fabric Booking
	$report_format_id_arr[2] = "8,9,10,45,46,53,136,244,72,124,191,220"; //Short Fabric Booking
	$report_format_id_arr[3] = "38,39,16,64,177"; //Sample Fabric Booking With Order Entry
	$report_format_id_arr[4] = "34,35,36,37,64,72,174,220"; //Sample Fabric Booking Without Order Entry
	$report_format_id_arr[5] = "13,14,15,16"; //Multiple Order Wise Trims Booking
	$report_format_id_arr[6] = "17,18,19,175,176,177,16,174,774"; //Country and Order Wise Trims Booking
	$report_format_id_arr[7] = "13,15,74,75,76,77"; //Yarn Dyeing Work Order
	$report_format_id_arr[8] = "79,80,81,82,13"; //Yarn Dyeing Work Order without Order
	$report_format_id_arr[10] = "60,61"; //Service Booking for AOP
	$report_format_id_arr[11] = "11,12,59,116,136,177"; //Fabric Service Booking
	$report_format_id_arr[12] = "13,12,15,16,175,176,746"; //Service Booking for Kniting
	$report_format_id_arr[15] = "20"; //Short Trims Booking
	$report_format_id_arr[16] = "21"; //Sample Trims Booking With Order
	$report_format_id_arr[17] = "22"; //Sample Trims Booking Without Order
	$report_format_id_arr[18] = "23,24,25,26,27,29,160,182,285,309,580,72,191"; //Order Wise Budget Report
	$report_format_id_arr[19] = "30,31,32,33,49"; //Export To Excel Report
	$report_format_id_arr[20] = "23,40,41,42,43,44"; //Party Wise Grey Fabric Reconciliation
	$report_format_id_arr[21] = "47,48,66"; //Embellishment Issue
	$report_format_id_arr[22] = "50,51,52,63,120,142,156,157,158,159,170,171,173,192,197,211,221,229,235,238,268,215,270,581,730,759,351,381,403,405,765,769,445,460,24,129,25,494,498,800,427,341,342,486,881,882,509";
	$report_format_id_arr[43] = "50,51,52,63,120,142,156,157,158,159,170,171,173,192,197,211,221,229,235,238,268,215,270,581,730,759,351,381,403,405,765,769,445,460,24,129,25,494,498,800,427,341,342,486,881,882,509";
	$report_format_id_arr[161] = "50,51,52,63,120,142,156,157,158,159,170,171,173,192,197,211,221,229,235,238,268,215,270,581,730,759,351,381,403,405,765,769,445,460,24,129,25,494,498,800,427,341,342,486,881,882,509"; //Pre-Costing; Pre-Costing V2; Pre-Costing V3
	$report_format_id_arr[23] = "54,55,56,57,58,198,801,802,803,804,805,506"; //Tna Knit 486
	$report_format_id_arr[24] = "54,55,56,57,58"; //Pre-Costing
	$report_format_id_arr[25] = "60,61,62"; //Multiple Job Wise Trims Booking
	$report_format_id_arr[26] = "14,67,227,183,209,177,235,176,174,274,746,241,269,28,280,304,719,339,433,768,404,419,426,774,452,786,502,809,845,437,875"; //Multiple Job Wise Trims Booking V2 //67,
	$report_format_id_arr[27] = "68,69,70,71,136,181,236,325,326,327,78,365,388,84,415,451,137,839,860,866,848,883,885"; //Grey Fabric Roll Issue
	$report_format_id_arr[28] = "74,75,76,77,78,117"; //Yarn Dyeing Work Order Without Lot;
	$report_format_id_arr[29] = "79,80,81,82,83"; //Yarn Dyeing Work Order Without Order2
	$report_format_id_arr[30] = "84,85,134,732,137,129,191,227,235,354,274,430,241,427,72,28"; //Others Purchase Order
	$report_format_id_arr[31] = "86,87,88,89"; //Embellishment Work Order V2
	$report_format_id_arr[32] = "90,91,92,137,194,213,217,219,239,275,308,336,406,414,191,220"; //Price Quotation
	$report_format_id_arr[33] = "94,95,35,36,37,64"; //Price Quotation
	$report_format_id_arr[34] = "96,97,98,99,100,101,150"; //Party Wise Yarn Reconciliation
	$report_format_id_arr[35] = "143,84,85,151,160,175,218,220,155,235,274,191,241,269,28,280,304,339,370,719,723,768,425"; //Partial Fabric Booking;
	$report_format_id_arr[36] = "23,102,103,104,105,106,107,108,152,338,195,778,811,812,813,814,815,816,906"; //Daily Yarn stock;
	$report_format_id_arr[37] = "109,110,111,112,113,114,89,129,161,172,184,227,230,235,274,241,419,425,764,427,28,280,304,719,768,846"; //Yarn Issue;
	$report_format_id_arr[38] = "115,116,136,137,196,199,206,207,208,212,129,161,191,271,42,362,227,235,274,707,738,747,241,427,28,437,280,304,719,836,723,865"; //Gate Pass entry;
	$report_format_id_arr[39] = "118,119,120,121,122,123,129,169,165,227,241,580,28,280,243,688,310,304,370,719,723,339,382,235,768,425,419,426,274"; //Purchase Requisition;
	$report_format_id_arr[40] = "124,125,126,127,128,292,293"; //Gate Pass entry;
	$report_format_id_arr[41] = "130,131,132,133,231,232,287,89,580,581,572,345,356,762,767,424,227,241,503,807"; //Knitting Plan Report;//================================================================================
	$report_format_id_arr[42] = "134,135,136,137,138,139,161,162,191,227,235,274,241,427"; //Roll Wise Grey Fabric Delivery to Store;
	$report_format_id_arr[44] = "108,96,140,141,492,195"; //Monthly Buyer Wise Order Summary
	$report_format_id_arr[45] = "72,78,84,85,193,129,191,227,235"; //Yarn purchage order ;
	$report_format_id_arr[46] = "147,148,149,150,276,277,689,690,691,340,305,242"; //Capacity and Order Booking Status;
	$report_format_id_arr[47] = "153,154"; //Fabric Receive Status Report;
	$report_format_id_arr[114] = "23,124,223,724"; //Fabric Receive Status Report2;
	$report_format_id_arr[48] = "17,155"; //Sample Requisition Fabric Booking -With order ;
	$report_format_id_arr[49] = "163,164,16,177,288,176,746"; //Service Booking For AOP V2 ;
	$report_format_id_arr[50] = "84,85,86,89,129,161,191,220,235"; //Bundle Issued to Print;
	$report_format_id_arr[51] = "86,165,166,129"; //Bundle Receive From Print;
	$report_format_id_arr[52] = "84,85,86,89,129,161, 191"; //Bundle Issued to Embroidery ;
	$report_format_id_arr[53] = "86,165,166,129"; //Bundle Receive From Embroidery;
	$report_format_id_arr[54] = "178,179,180,23,825"; //Accessories Followup Report V2
	$report_format_id_arr[55] = "86,84,88,839,129"; //Fabric Requisition For Batch 2
	$report_format_id_arr[56] = "86,185,186,187,224,225,226,274,269,241,220,235,324,280,304,719,723,339,370,768,404,419,3"; //Batch Creation
	$report_format_id_arr[57] = "67,19,16,177"; //Multiple Job Wise Short Trims Booking V2
	$report_format_id_arr[58] = "78,188,80,189,190,210,130,121,85,132,440,441,137,807,72,191"; //Dyes And Chemical Issue Requisition
	$report_format_id_arr[59] = "108,195"; //Daily Production Progress Report
	$report_format_id_arr[60] = "201,202,203,204,205"; //Factory monthly production report
	$report_format_id_arr[61] = "66,134,732,85,137,129,430,72"; //Stationary Purchase Order
	$report_format_id_arr[62] = "24,25,214,215,216,217,268,53"; //Cost Break Up Report V2
	$report_format_id_arr[63] = "108,195,262,263,264,501"; //Style Wise Production Report
	$report_format_id_arr[64] = "222,223"; //Order Wise Budget Sweater Report
	$report_format_id_arr[65] = "13,15,16,177,175,176"; //Multi Job Wise Service booking for Knitting
	$report_format_id_arr[67] = "115,116,136,137,129,110,72,191,235"; //fabric sales order entry
	$report_format_id_arr[68] = "233,234,237,240,137,78,737,129,884"; // Doc. Submission to Bank
	$report_format_id_arr[69] = "134,135,136,137,64,72,191,227,777,799,764,235"; //yarn Purchase Req.
	$report_format_id_arr[70] = "108,195,242,243,54,90"; //Monthly Capacity Vs Buyer Wise Booked
	$report_format_id_arr[71] = "247,246,245,138,286,290,335"; //Daily Knitting Production Report
	$report_format_id_arr[72] = "108,248"; //Work progress report
	$report_format_id_arr[73] = "108,249"; //Order Follow-up Report
	$report_format_id_arr[74] = "42,126,250,251,252,253,254,291,284,282,283,312,53,72"; //Daily Ex-Factory Report
	$report_format_id_arr[75] = "108,257,243,444"; //Accessories Followup Report [Budget-2]
	$report_format_id_arr[76] = "108,254,255"; //Weekly Capacity and Booking Status
	$report_format_id_arr[77] = "108,256,258"; //Fabric Production Status Report
	$report_format_id_arr[78] = "108,242,259"; //Sewing Plan Vs Production
	$report_format_id_arr[79] = "108,260,265"; //Cutting Status Report
	$report_format_id_arr[80] = "108,195,242,359,712,149"; //Daily RMG Production status Report V2
	$report_format_id_arr[81] = "108,259,261,242,359"; //Date Wise Production Report
	$report_format_id_arr[82] = "108,149,150"; //Factory Monthly Production Report for Urmi
	$report_format_id_arr[83] = "84,86"; //Factory Monthly Production Report for Urmi
	$report_format_id_arr[84] = "149,150,222,256,266,267,277,783,689"; //Closing Stock Report for General
	$report_format_id_arr[85] = "266,256,267,264"; //Buyer Inquiry Status Report
	$report_format_id_arr[86] = "78,121,122,123,127,169,235,580,758,227,274,241"; //Garments Delivery Entry
	$report_format_id_arr[87] = "147"; //Order Wise Production Report 147,195,107
	$report_format_id_arr[88] = "272,273";
	$report_format_id_arr[89] = "13,15,16,177,175,746,220,235"; //Multiple Job Wise Embellishment Work Order
	$report_format_id_arr[90] = "10,17,61";
	//For Woven
	$report_format_id_arr[92] = "155,749";
	$report_format_id_arr[93] = "178,278,279";
	$report_format_id_arr[94] = "107,178,195,242";
	$report_format_id_arr[95] = "108,23,138,290"; //Dyeing Production Report-V3
	$report_format_id_arr[96] = "261,281,282,283,284,305,160"; //Export CI Statement
	$report_format_id_arr[97] = "23,24,25,26,27,29,182,285"; //Woven Order Wise Budget Report
	$report_format_id_arr[98] = "35,36,78,137"; //Daily Yarn Demand Entry
	$report_format_id_arr[99] = "78"; //Wash Dyes Chemical Issue
	$report_format_id_arr[100] = "78"; //Batch Creation For Gmts. Wash
	$report_format_id_arr[101] = "2,78"; //Wash Recipe Entry
	$report_format_id_arr[102] = "78"; //Wash Dyes And Chemical Issue Requisition
	$report_format_id_arr[103] = "78"; //Wet Production
	$report_format_id_arr[104] = "78"; //Dry Production
	$report_format_id_arr[105] = "78,66"; //Wash Delivery
	$report_format_id_arr[106] = "78"; //Wash Bill Issue
	$report_format_id_arr[107] = "78"; //Wash Delivery Return
	$report_format_id_arr[108] = "108,195,23,150"; //Statement of Total Export Value and CM
	$report_format_id_arr[109] = "107,108,195,263"; //Fabric Production Status Report - Sales Order
	$report_format_id_arr[122] = "51,158,159,170,171,192,197,307,311,313,260,381,761,403,770,473,852,862,873,63"; //For Pre-Costing V2 woven
	$report_format_id_arr[110] = "149,222,256,266,267"; //Closing Stock Report for Embroidery
	// else if($data==112]="108,289,472";//Bank Liability Position As Of Today
	$report_format_id_arr[113] = "579,578,577,124,108,363,255"; //Order Wise Grey Fabrics Stock Report
	$report_format_id_arr[115] = "233,234,240,344,678,679,680,681,682,683,684,685,686,687,692,693,694,695,697,698,699,700,701,702,703,704,705,713,314,718,720,721,731,760,357,358,361,368,369,372,374,375,376,378,391,392,410,411,412,413,418,435,436,457,458,459,461,462,463,464,465,467,468,469,470,471,241,474,493,496,497,499,500,799,822,823,824,836,404,419,426,582,903,905"; // BTB/Margin LC
	$report_format_id_arr[116] = "256,263,264,420,40"; //Daily Yarn Issue Report
	$report_format_id_arr[117] = "294,295,296,297,298,299,300,301,302,303,714,722,366,367,371,431,434,439,442,827,828"; // Woven Cut and Lay Entry Ratio Wise
	//else if($data==118]="294,295,296,297,298,299,300,301,302,303"; //Cut and Lay Entry Ratio Wise 4
	$report_format_id_arr[118] = "297,298,333,332,331,300,328,330,329,379,380,714,453,787,806,808,857,858"; //Cut and Lay Entry Ratio Wise 4
	$report_format_id_arr[119] = "115,116"; //Scrap Material Issue
	$report_format_id_arr[120] = "195,222"; //Weekly Capacity and Order Booking Status V2
	$report_format_id_arr[121] = "250,282,281,165,305,306,307,283,364,407,284,406,712"; //Shipment Shedule Report
	$report_format_id_arr[123] = "86,66,111,129"; //Embellishment Issue
	$report_format_id_arr[124] = "222,259,242,359"; //Daily Gate In And Out Report
	$report_format_id_arr[125] = "78,66,85,89,129"; //Woven Finish Fabric Receive
	$report_format_id_arr[126] = "78"; //Woven Finish Fabric Issue
	$report_format_id_arr[127] = "78"; //Woven Finish Fabric Receive Return
	$report_format_id_arr[128] = "78"; //Woven Finish Fabric Roll Issue
	$report_format_id_arr[129] = "78"; //Woven Finish Fabric issue return
	$report_format_id_arr[130] = "78"; //Woven Finish Fabric Transfer Entry
	$report_format_id_arr[131] = "78,696"; //Woven Finish Roll Issue Return
	$report_format_id_arr[132] = "78,84,732,85,430,137"; //Dyes And Chemical Purchase Order
	$report_format_id_arr[133] = "66,85,706,129,161"; //Knitting Bill Issue
	$report_format_id_arr[134] = "260,243,708,709"; //Daily Cutting And Input Inhand Report
	$report_format_id_arr[135] = "108,242,259,359"; //Order Wise Finish Fabric Stock
	$report_format_id_arr[136] = "108,267,710,711,712,750,161,758,122"; //Dyeing Report
	$report_format_id_arr[137] = "108,195,242"; //Sample Followup Report- Sweater
	$report_format_id_arr[138] = "143,84,85,151,160,175,155,274,72,191,428,241"; //Woven Partial Fabric Booking
	$report_format_id_arr[139] = "256,267,715,716,717,438"; //Closing Stock Dyes and Chemical
	$report_format_id_arr[140] = "72,315,316,317,318,319,320,321,322,337,748,355,331,390,66,136,137,129,810,72,870,872"; //Knitting Production
	$report_format_id_arr[141] = "313,323"; //Sourcing Post Cost Sheet
	$report_format_id_arr[142] = "45,109,110,111,129,161,746,220,241,427,28"; //Sample Requisition With Booking
	$report_format_id_arr[143] = "725,726,727,728,729,733,740,741,742"; //Date Wise Dyes Chemical Receive Issue
	$report_format_id_arr[144] = "222,107,734,735,736,149"; //Closing Stock Report for Trims
	$report_format_id_arr[145] = "298,299,328,329,330,331,332,333,343,373,393,409,429,443,454,784,785,834,838,842,380,843,844,856,337"; //Cut and Lay Entry Ratio Wise 3
	$report_format_id_arr[146] = "317,322,334,320,331,880"; //Roll Splitting Before Issue
	$report_format_id_arr[147] = "739"; //Comparative Statement
	$report_format_id_arr[148] = "195"; //Job Wise Cost Analysis Report-Woven
	$report_format_id_arr[149] = "108,743,744,745,752"; //Date Wise Production Report [CM]
	$report_format_id_arr[150] = "72,78,85,116,137,230,227"; //Fabric Sales Order Entry v2
	$report_format_id_arr[151] = "477,478,479,480,481,482,483,484,485,486,487,488,489,490,491,835,159"; //Export Pro Forma Invoice
	$report_format_id_arr[152] = "78"; //Demand For Accessories
	$report_format_id_arr[153] = "108,725"; //Wash Received and Delivery Statement
	$report_format_id_arr[154] = "222,259,242"; //Unit Wise Production 2
	$report_format_id_arr[155] = "753,754,755,756,757,78,466,476,829,427,426"; //Sales Contract Entry
	$report_format_id_arr[156] = "78,66,85,137,129"; //Item Issue Requisition
	$report_format_id_arr[157] = "23,124,223"; //Yarn Purchase Requisition Follow Up Report
	$report_format_id_arr[158] = "108,259,242,359,712"; //Production Summary [Fabric And Garments]
	$report_format_id_arr[159] = "341,342"; //Hourly Production Monitoring Reports
	$report_format_id_arr[160] = "108,259"; //Sample Production Report
	$report_format_id_arr[162] = "107,108,195,710,750"; //Batch wise Dyeing and Finishing Cost
	$report_format_id_arr[163] = "346,347,348,349,350"; //	SubCon Dye And Finishing Delivery
	$report_format_id_arr[164] = "86,66,85,89,68,69,129,72"; // Knit Finish Fabric Roll Issue
	$report_format_id_arr[165] = "178,195,256,263,264,352,734"; // Finish Fabric Closing Stock
	$report_format_id_arr[166] = "178,195,242,359,352,712,389,758,220"; // Style Wise Finish Fabric Status
	$report_format_id_arr[167] = "86,68,69,84,764"; // Finish Fabric Roll Delivery To Store
	$report_format_id_arr[168] = "130,131,133,353,572,356,132,424,503,807"; // Knitting Plan Report[Sales]
	$report_format_id_arr[169] = "86,84"; // Finish Fabric Roll Delivery To Store
	$report_format_id_arr[170] = "86,84,85,346"; // Sample Delivery Entry
	$report_format_id_arr[171] = "86,84,85,68,69,89,129"; // Knit Grey Fabric Roll Receive
	$report_format_id_arr[172] = "86,69"; // Knit Finish Fabric Roll Receive
	$report_format_id_arr[173] = "86,84,377"; // Trims Order Receive
	$report_format_id_arr[174] = "86,84,85,360,129,137,161,230,220,235"; // Trims Delivery Entry
	$report_format_id_arr[175] = "86,84,85,160"; // Trims Bill Entry
	$report_format_id_arr[176] = "108,195"; //Fabric Booking Approval New
	$report_format_id_arr[177] = "108,195"; //Short Fabric Booking Approval New
	$report_format_id_arr[178] = "108,195"; //pre-costing Approval
	$report_format_id_arr[179] = "84,85"; //Dyes And Chemical Issue
	$report_format_id_arr[263] = "78,84"; //Dyes And Chemical Receive
	$report_format_id_arr[180] = "56,127,41"; //Textile Tna
	$report_format_id_arr[181] = "86,116,136"; //Trims Issue
	$report_format_id_arr[182] = "66,85,143,160,129"; //General Item Issue
	$report_format_id_arr[183] = "86,116,85,751,479,137"; //Pro Forma Invoice V2
	$report_format_id_arr[184] = "147,195,107"; //Pro Forma Invoice Approval Status Report
	$report_format_id_arr[185] = "80,108,149"; //Order wise Production and Delivery Report
	$report_format_id_arr[186] = "726,727,149"; //Date Wise Finish Fabric Receive Issue
	$report_format_id_arr[187] = "726,727,725,733,384,385,386,387,206"; //Date Wise Item Receive and Issue
	$report_format_id_arr[188] = "147,259,763,242"; //MIS Report
	$report_format_id_arr[189] = "678,395,396,397,398,399,400,401,402"; //Import Document Acceptance
	$report_format_id_arr[190] = "108,259,125"; //Style Wise Production Summary
	$report_format_id_arr[191] = "86,116,136,137"; //Trims Receive Entry
	$report_format_id_arr[192] = "135,136,137,72,129,191,220,235,274,241,427,28,280,304"; //Bundle Wise Sewing Input
	$report_format_id_arr[193] = "107,178,195,242,359"; //Work Order [Booking] Report
	$report_format_id_arr[194] = "78,66,85,137,129,72,191,220,235"; //General Item Receive
	$report_format_id_arr[195] = "8,163,164,209,177,129,220,274"; //Multi Job Wise Service Booking Dyeing
	$report_format_id_arr[196] = "108,195"; //Store Item List
	$report_format_id_arr[197] = "54,108,256,267"; //Order Wise Grey Fabrics Stock Report V2
	$report_format_id_arr[198] = "766,108,195,242,359,712,289,23,408,446,389,191"; //Color and Size Breakdown Report
	$report_format_id_arr[199] = "78,84,85,416,417"; //Roll Wise Grey Fabric Requisition For Transfer
	$report_format_id_arr[200] = "261,23,150,421,422,423,282"; //Style and Store Wise Grey Fabric Stock Report
	$report_format_id_arr[201] = "108,447,448,449,450"; //Style wise CM Report
	$report_format_id_arr[202] = "109,110,111"; //Style wise CM Report
	$report_format_id_arr[203] = "282,283"; //Shipment Schedule Details
	$report_format_id_arr[204] = "108,195,242"; //File Wise Yarn Receive and Issue Report
	$report_format_id_arr[205] = "108,195,455,456,242,359"; //PI Statement Report
	$report_format_id_arr[206] = "732,86,84,85"; //Service Work Order
	$report_format_id_arr[207] = "8,12,16,177,175,176,172,508"; //Service Booking for Dyeing
	$report_format_id_arr[208] = "86,96"; //File Wise Export Status
	$report_format_id_arr[209] = "86,116"; //Sweater Sample Requisition
	$report_format_id_arr[210] = "66"; //Raw Material Issue Requisition
	$report_format_id_arr[211] = "66,149,223,771,772,773"; //Date and Style wise Inspection Report
	$report_format_id_arr[212] = "108,195,242"; //Master Style Follow Up Report
	$report_format_id_arr[213] = "115,66,111,137"; //Finish Fabric Delivery To Garments
	$report_format_id_arr[214] = "178,255"; //Sales Forecast Vs Booked
	$report_format_id_arr[215] = "250,282"; //Daily Ex-Factory Report Order/Style wise
	$report_format_id_arr[216] = "134,135,136"; //Finish Fabric Roll Delivery To Garments
	$report_format_id_arr[217] = "300,315,316,317,320,334,136,502,137,810"; //Subcon Knitting Production
	$report_format_id_arr[218] = "108,195,242"; //PI Approval New
	$report_format_id_arr[219] = "67,14,183,85,177,175,746,774,235,72"; //Multiple Job Wise Trims Booking V2 -woven,
	$report_format_id_arr[220] = "108,259,242,359,712,389,887"; //Style Closing Report
	$report_format_id_arr[221] = "150,777,778,149,421"; //Rack Wise Grey Fabrics Stock Report Sales
	$report_format_id_arr[222] = "247,246,245,138,286,290,335"; // Style Owner Wise Daily Knitting Production Report.
	$report_format_id_arr[223] = "779,780,781,782"; // Gate In and Out Report

	$report_format_id_arr[225] = "108,195"; // Purchase Requisition Approval Status Report
	$report_format_id_arr[228] = "2,3,6"; // Yarn Dyeing Work Order Sales
	$report_format_id_arr[229] = "107,169,256,267,264,580,758"; // Style wise Cost Comparison
	$report_format_id_arr[230] = "78,84,85"; // Trims Receive Entry Multi Ref V3
	$report_format_id_arr[232] = "108,195,149,125,777,242"; //Order Monitoring Report
	$report_format_id_arr[233] = "178,152,475,826"; // Raw Material Stock Report
	$report_format_id_arr[234] = "109,116,281,85,305"; // Grey Fabric Delivery to Store
	$report_format_id_arr[235] = "109,110,111,160"; // Finish Fabric Delivery to Store
	$report_format_id_arr[236] = "108,149"; // Order Booking Status Report 3
	$report_format_id_arr[237] = "78"; // SubCon Material Receive
	$report_format_id_arr[238] = "109,110,732"; // Service Requisition
	$report_format_id_arr[239] = "147,259,242"; // Hourly Production Monitoring Report 2nd
	$report_format_id_arr[240] = "147,195,495,242"; // Date Wise Delivery Report
	$report_format_id_arr[241] = "108,195,242,359"; // Order Forecasting Report
	$report_format_id_arr[242] = "108,195,242,359,306"; // Style Wise materials Follow up Report
	$report_format_id_arr[243] = "115,116,136,137,129,161,220,274,241"; // Recipe Entry
	$report_format_id_arr[244] = "13,14"; //Multiple Job Wise Embellishment Work Order[WVN]
	$report_format_id_arr[245] = "150,84,85,137,129,788,789,790,791,792,793,794,795,796,797,798,853,72,584,777,904,907"; //Export Invoice
	$report_format_id_arr[246] = "297,298,333,332,331,300,328,330,329,379,380,714,453,784,787,806"; //Contrast Cutting Entry
	$report_format_id_arr[247] = "108,195"; //Cost Breakdown Analysis Report [Budget]
	$report_format_id_arr[248] = "108,195"; //Post Costing Report V4
	$report_format_id_arr[249] = "115,116"; //Export Proceeds Realization
	$report_format_id_arr[250] = "54,55,56,57,58,198,223,504,505,506,507"; //Woven TNA Progress Report
	$report_format_id_arr[251] = "108,195,242,359,712"; //Work Order Details Report
	$report_format_id_arr[253] = "108,115,116,195,242,136,859"; //Fabric Issue to Fin. Process
	$report_format_id_arr[254] = "108,422,195"; //abric Issue to Fin. Process
	$report_format_id_arr[255] = "115,116,137,129,72,816,817"; //Fabric Sales Order Entry [ Yarn Part ]
	$report_format_id_arr[256] = "107,121,123,127,264"; //Style Wise Trims Received Issue And Stock
	$report_format_id_arr[257] = "78,818"; //Knit Grey Fabric Receive
	$report_format_id_arr[258] = "66,78,85,129,137,161,819"; //Knit Finish Fabric Receive By Garments
	$report_format_id_arr[259] = "78,819"; //Knit Finish Fabric Receive By Garments
	$report_format_id_arr[260] = "108,242,259,359,712,389"; //Job/Order Wise Cutting Lay and Production Report
	$report_format_id_arr[261] = "78,84,85"; //Yarn Store Requisition Entry
	$report_format_id_arr[262] = "178,195,242,352,710"; // Style Wise Finish Fabric Status 2
	$report_format_id_arr[264] = "753,754,755,829,757,830"; // Export LC Entry
	$report_format_id_arr[265] = "108,249,223,831"; // Order Follow-up Report Woven
	$report_format_id_arr[266] = "143,832,66,85,160,129"; // Dyeing And Finishing Bill Issue
	$report_format_id_arr[267] = "143,66"; // Dyeing And Finishing Bill Entry
	$report_format_id_arr[268] = "143"; // Knitting Bill Entry
	$report_format_id_arr[269] = "78,84,85"; //Yarn Requisition Entry For Sales
	$report_format_id_arr[270] = "108,195"; //Order Allocation Details V2
	$report_format_id_arr[271] = "78,84,85,129,160"; //Cutting QC V2
	$report_format_id_arr[272] = "108,256"; //Roll Position Tracking Report
	$report_format_id_arr[273] = "108,195,242,4,5,840,841,359,712,389"; //Daily Cutting And Input Inhand Report 2
	$report_format_id_arr[274] = "108,195,242,243"; //Dyes Chemical Loan Ledger
	$report_format_id_arr[275] = "222,259"; //Style wise Cost Comparison Woven
	$report_format_id_arr[276] = "86,110,85"; //Printing Delivery Entry [Bundle]
	$report_format_id_arr[278] = "222,259"; //Hourly Production Monitoring Report Chaity
	$report_format_id_arr[279] = " 222,259,715,124,310"; //Date Wise Shipment Status
	$report_format_id_arr[280] = "84,86"; //Handloom/Strikeoff/Labdip Requisition
	$report_format_id_arr[281] = "78,84,85,129,160"; //Yarn Purchase Order [Sweater]
	$report_format_id_arr[283] = "66,85,137"; //Roll wise Grey Sales Order To Sales Order Transfer
	$report_format_id_arr[284] = "66,72,85,86,129,137,191"; //Bundle Wise Cutting Delivery To Input Challan
	$report_format_id_arr[285] = "86,165,167,168"; //Bundle Issued to Special Work
	$report_format_id_arr[287] = "86,110"; //Trims Receive Entry Multi Ref.
	$report_format_id_arr[288] = "108,256"; //Buyer and Style Wise Trims Stock.
	$report_format_id_arr[290] = "8,14"; //Service Booking for Dyeing v2
	$report_format_id_arr[292] = "108,745"; //Date Wise Production Report [CM] 2
	$report_format_id_arr[295] = "108,242"; // Sample Progress Report
	$report_format_id_arr[296] = "108,195"; // Cross LC Report
	$report_format_id_arr[297] = "108,195,242,261,877,878,879"; // BTB or Margin LC Report
	$report_format_id_arr[298] = "108,195,242,359"; // Monthly Export Status summary

	$report_format_id_arr[304] = "116,85,89"; // Dyes And Chemical Issue V2
	$report_format_id_arr[305] = "66,129,137,235,274"; // Topping Adding Stripping Recipe Entry
	$report_format_id_arr[308] = "108,893,894,895,896,897,898,899,900,901";


	return ($report_format_id_arr[$index]) ? $report_format_id_arr[$index] : $report_format_id_arr[$index] * 1;
}



if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}

if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
	if ($_SESSION['logic_erp']["buyer_id"] != 0) $buyer_cond = " and id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
	else $buyer_cond = "";
	if ($_SESSION['logic_erp']["company_id"] != 0) $company_cond = " and id in (" . $_SESSION['logic_erp']["company_id"] . ")";
	else $company_cond = "";
} else {
	$buyer_cond = "";
	$company_cond = "";
}

if ($action == "load_drop_down_report_module") {

	if ($data == 1) //Module ID---Lib
	{
		$report_format_name = "196"; //Menu ID //echo $report_format_name;
	} else if ($data == 2) //Module ID---Merchandising
	{
		$report_format_name = "1,2,3,4,5,6,7,8,9,10,11,12,14,15,16,17,22,25,26,28,29,31,32,35,43,48,49,54,57,65,75,83,89,90,92,122,137,138,141,142,161,193,195,198,207,209,219,228,241,242,244,251,277,280,286,291,314"; //Menu ID //echo $report_format_name;
	} else if ($data == 3) //Module ID---TNA
	{
		$report_format_name = "23,24,180,250"; //Menu ID
	} else if ($data == 4) //Module ID---Planning
	{
		$report_format_name = "41,78,79,88,98,117,118,145,168,246,260,261,269,270,294"; //Menu ID
	} else if ($data == 5) //Module ID---Commercial
	{
		$report_format_name = "30,45,61,68,69,93,96,115,112,132,151,155,183,189,205,208,245,249,264,281,296,297,298"; //Menu ID
	} else if ($data == 6) //Module ID---Inventory
	{
		$report_format_name = "20,27,33,34,36,37,38,39,84,94,113,116,119,124,125,126,127,128,129,130,131,135,139,143,144,146,156,162,164,165,166,171,172,179,181,182,186,187,191,194,764,197,199,200,204,213,216,221,223,224,230,254,256,257,258,262,263,274,283,863,864,287,288,282,299,302,304,313,311"; //Menu ID
	} else if ($data == 7) { //Module ID---Production
		//$report_format_name = "20,27,33,34,36,37,38,39,51,53,63,80,81,86,95,71,21,40,42,60,50,56,59,82,272,123,272,154,149,134,140,136,52,234,188,239,222,305,58,301,192,211,87,292,273,100,289,284,271,308,243,159,55,47,253,255,67,77,84,94,109,113,114,116,119,124,125,126,127,128,129,130,131,135,139,143,144,146,156,162,164,165,166,167,171,172,179,181,182,186,187,191,194,764,197,199,200,204,213,216,221,223,224,230,235,254,256,257,258,262,263,274,283,863,864,287,288,282,299,302,304,309,311,312,310"; //Menu ID

		$report_format_name="21,40,42,47,50,51,52,53,55,56,58,59,60,63,67,71,72,73,74,75,76,77,80,81,82,86,87,95,109,114,123,134,136,140,149,150,154,159,167,169,188,192,211,222,234,235,239,243,253,255,271,272,273,278,284,285,289,292,301,305,847,583,308,309,310,311,312"; //Menu ID ,83,165
		
	}
	else if($data==8) //Module ID---S. Con 109
	{
		$report_format_name = "133,163,217,237,266,267,268,300"; //Menu ID
	} else if ($data == 11) //Module ID---management Report
	{
		$report_format_name = "18,19,44,46,62,64,70,72,73,74,75,76,85,97,108,29,120,121,148,158,190,201,203,212,215,220,229,232,236,247,248,265,279,275,306"; //Menu ID
	} else if ($data == 19) //Module ID---S. Chain
	{
		$report_format_name = "132,147,152,157,206,238,307,30"; //Menu ID
	} else if ($data == 20 || $data == 24) //Module ID---Wash //24==Radinace Wash=24
	{
		$report_format_name = "99,100,101,102,103,104,105,106,107,153"; //Menu ID
	} else if ($data == 22) //Module ID---EMB
	{
		$report_format_name = "110,111"; //Menu ID
	} else if ($data == 14) //Module ID---sample
	{
		$report_format_name = "160,170,295"; //Menu ID
	} else if ($data == 17) //Module ID---Trims
	{
		$report_format_name = "173,174,175,185,210,214,233,240,259,293"; //Menu ID
	} else if ($data == 12) //Module ID---approval
	{
		$report_format_name = "176,177,178,184,218,225,303"; //Menu ID
	} else if ($data == 16) //Module ID---approval
	{
		$report_format_name = "202"; //Menu ID
	} else if ($data == 18) //Module ID---lab
	{
		$report_format_name = "282"; //Menu ID
	} else if ($data == 15) //Module ID---Printing
	{
		$report_format_name = "276"; //Menu ID
	} else {
		$report_format_name = "0";
	}

	echo create_drop_down("cbo_report_name", 182, $report_name, "", 1, "--- Select Report ---", $selected, "load_drop_down( 'requires/report_settings_controller', this.value, 'load_drop_down_report_name', 'report_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/report_settings_controller' );", "", $report_format_name);
	exit();
}


if ($action=="openpopup_report_formate")
{
    // print_r($data);exit;
	//echo $txt_report_button_wise_user_id;die;
	if($data==1) $report_format_id="1,2,3,4,5,6,7,28,39,45,53,73,93,78,84,85,129,193,269,280,304,719,723,339,370,383,404,419,426,432,452,786,502,437,833,849,865,892,38";// Menu ID---- Main Fabric Booking
	else if($data==2) $report_format_id="8,9,10,45,46,53,136,244,72,124,191,220";//Short Fabric Booking
	else if($data==3) $report_format_id="38,39,16,64,177";//Sample Fabric Booking With Order Entry
	else if($data==4) $report_format_id="34,35,36,37,64,72,174,220";//Sample Fabric Booking Without Order Entry
	else if($data==5) $report_format_id="13,14,15,16";//Multiple Order Wise Trims Booking
	else if($data==6) $report_format_id="17,18,19,175,176,177,16,174,774";//Country and Order Wise Trims Booking
	else if($data==7) $report_format_id="13,15,74,75,76,77";//Yarn Dyeing Work Order
	else if($data==8) $report_format_id="79,80,81,82,13";//Yarn Dyeing Work Order without Order
	else if($data==10) $report_format_id="60,61";//Service Booking for AOP
	else if($data==11) $report_format_id="11,12,59,116,136,177";//Fabric Service Booking
	else if($data==12) $report_format_id="13,12,15,16,175,176,746";//Service Booking for Kniting
	else if($data==14) $report_format_id="79,80,867,868";//Yarn Service Work Order
	else if($data==15) $report_format_id="20";//Short Trims Booking
	else if($data==16) $report_format_id="21";//Sample Trims Booking With Order
	else if($data==17) $report_format_id="22";//Sample Trims Booking Without Order
	else if($data==18) $report_format_id="23,24,25,26,27,29,160,182,285,309,580,72,191";//Order Wise Budget Report
	else if($data==19) $report_format_id="30,31,32,33,49";//Export To Excel Report
	else if($data==20) $report_format_id="23,40,41,42,43,44";//Party Wise Grey Fabric Reconciliation
	else if($data==21) $report_format_id="47,48,66";//Embellishment Issue
	else if($data==22 || $data==43 || $data==161) $report_format_id="50,51,52,63,120,142,156,157,158,159,170,171,173,192,197,211,221,229,235,238,268,215,270,581,730,759,351,381,403,405,765,769,445,460,24,129,25,494,498,800,427,341,342,486,874,881,882,509";//Pre-Costing; Pre-Costing V2; Pre-Costing V3
	else if($data==23) $report_format_id="54,55,56,57,58,198,801,802,803,804,805,506";//Tna Knit
	else if($data==24) $report_format_id="54,55,56,57,58";//Pre-Costing
	else if($data==25) $report_format_id="60,61,62";//Multiple Job Wise Trims Booking
	else if($data==26) $report_format_id="14,67,227,183,209,177,235,176,174,274,746,241,269,28,280,304,719,339,433,768,404,419,426,774,452,786,502,809,845,437,875";//Multiple Job Wise Trims Booking V2 //67,
	else if($data==27) $report_format_id="68,69,70,71,136,181,236,325,326,327,78,365,388,84,415,451,137,839,860,866,848,883,885";//Grey Fabric Roll Issue
	else if($data==28) $report_format_id="74,75,76,77,78,117";//Yarn Dyeing Work Order Without Lot;
	else if($data==29) $report_format_id="79,80,81,82,83";//Yarn Dyeing Work Order Without Order2
	else if($data==30) $report_format_id="84,85,134,732,137,129,191,227,235,354,274,430,241,427,72,28";//Others Purchase Order
	else if($data==31) $report_format_id="86,87,88,89";//Embellishment Work Order V2

	else if($data==32) $report_format_id="90,91,92,137,194,213,217,219,239,275,308,336,406,414,191,220";//Price Quotation

	else if($data==33) $report_format_id="109,66, 85,137,95,909,863";    //94,95,35,36,37,64";//Knit Grey Fabric Issue

	else if($data==34) $report_format_id="96,97,98,99,100,101,150";//Party Wise Yarn Reconciliation
	else if($data==35) $report_format_id="143,84,85,151,160,175,218,220,155,235,274,191,241,269,28,280,304,339,370,719,723,768,425";//Partial Fabric Booking;
	else if($data==36) $report_format_id="23,102,103,104,105,106,107,108,152,338,195,778,811,812,813,814,815";//Daily Yarn stock;
	else if($data==37) $report_format_id="109,110,111,112,113,114,89,129,161,172,184,227,230,235,274,241,419,425,764,427,28,280,304,846,723,768,863,864,885,886";//Yarn Issue; 230
	else if($data==38) $report_format_id="115,116,136,137,196,199,206,207,208,212,129,161,191,271,42,362,227,235,274,707,738,747,241,427,28,437,280,304,719,865";//Gate Pass entry;
	else if($data==39) $report_format_id="118,119,120,121,122,123,129,169,165,227,241,580,28,280,243,688,310,304,370,719,723,339,382,235,768,425,419,426,274,908";//Purchase Requisition;
	else if($data==40) $report_format_id="124,125,126,127,128,292,293";//Gate Pass entry;
	else if($data==41) $report_format_id="130,131,132,133,231,232,287,89,580,581,572,345,356,762,767,424,227,241,503,807,98,108,259,242,149,281,119,76,889,890,891,353";//Knitting Plan Report;//================================================================================
	else if($data==42) $report_format_id="134,135,136,137,138,139,161,162,191,227,235,274,241,427,848,28,768,902";//Roll Wise Grey Fabric Delivery to Store;
	else if($data==44) $report_format_id="108,96,140,141,492,195";//Monthly Buyer Wise Order Summary
	else if($data==45) $report_format_id="72,78,84,85,193,129,191,227,235";//Yarn purchage order ;
	else if($data==46) $report_format_id="147,148,149,150,276,277,689,690,691,340,305,242";//Capacity and Order Booking Status;
	else if($data==47) $report_format_id="153,154";//Fabric Receive Status Report;

	else if($data==48) $report_format_id="17,155";//Sample Requisition Fabric Booking -With order ;
	else if($data==49) $report_format_id="163,164,16,177,288,176,746";//Service Booking For AOP V2 ;
	else if($data==50) $report_format_id="84,85,86,89,129,161,191,220,235";//Bundle Issued to Print;
	else if($data==51) $report_format_id="86,165,166,129";//Bundle Receive From Print;
	else if($data==52) $report_format_id="84,85,86,89,129,161, 191";//Bundle Issued to Embroidery ;
	else if($data==53) $report_format_id="86,165,166,129";//Bundle Receive From Embroidery;
	else if($data==54) $report_format_id="178,179,180,23,825";//Accessories Followup Report V2
	else if($data==55) $report_format_id="86,84,88,839,129";//Fabric Requisition For Batch 2
	else if($data==56) $report_format_id="86,185,186,187,224,225,226,274,269,241,220,235,324,280,304,719,723,339,370,768,404,419,3";//Batch Creation
	else if($data==57) $report_format_id="67,19,16,177";//Multiple Job Wise Short Trims Booking V2
	else if($data==58) $report_format_id="78,188,80,189,190,210,130,121,85,132,440,441,137,807,72,191";//Dyes And Chemical Issue Requisition
	else if($data==59) $report_format_id="108,195,242,359,712";//Daily Production Progress Report

	else if($data==60) $report_format_id="201,202,203,204,205";//Factory monthly production report
	else if($data==61) $report_format_id="66,134,732,85,137,129,430,72";//Stationary Purchase Order
	else if($data==62) $report_format_id="24,25,214,215,216,217,268,53";//Cost Break Up Report V2
	else if($data==63) $report_format_id="108,195,262,263,264,501";//Style Wise Production Report
	else if($data==64) $report_format_id="222,223";//Order Wise Budget Sweater Report
	else if($data==65) $report_format_id="13,15,16,177,175,176";//Multi Job Wise Service booking for Knitting
	else if($data==67) $report_format_id="115,116,136,137,129,110,72,191,220,235";//fabric sales order entry
	else if($data==68) $report_format_id="233,234,237,240,137,78,737,129,884";// Doc. Submission to Bank
	else if($data==69) $report_format_id="134,135,136,137,64,72,191,227,777,799,764,235";//yarn Purchase Req.
	else if($data==70) $report_format_id="108,195,242,243,54,90";//Monthly Capacity Vs Buyer Wise Booked
	else if($data==71) $report_format_id="247,246,245,138,286,290,335";//Daily Knitting Production Report
	else if($data==72) $report_format_id="108,248";//Work progress report
	else if($data==73) $report_format_id="108,249";//Order Follow-up Report
	else if($data==74) $report_format_id="42,126,250,251,252,253,254,291,284,282,283,312,53,72";//Daily Ex-Factory Report
	else if($data==75) $report_format_id="108,257,243,444";//Accessories Followup Report [Budget-2]
	else if($data==76) $report_format_id="108,254,255";//Weekly Capacity and Booking Status
	else if($data==77) $report_format_id="108,256,258";//Fabric Production Status Report
	else if($data==78) $report_format_id="108,242,259";//Sewing Plan Vs Production
	else if($data==79) $report_format_id="108,260,265";//Cutting Status Report
	else if($data==80) $report_format_id="108,195,242,359,712,149";//Daily RMG Production status Report V2
	else if($data==81) $report_format_id="108,259,261,242,359";//Date Wise Production Report
	else if($data==82) $report_format_id="108,149,150";//Factory Monthly Production Report for Urmi
	else if($data==83) $report_format_id="84,86";//Factory Monthly Production Report for Urmi
	else if($data==84) $report_format_id="149,150,222,256,266,267,277,783,689";//Closing Stock Report for General
	else if($data==85) $report_format_id="266,256,267,264";//Buyer Inquiry Status Report
	else if($data==86) $report_format_id="78,121,122,123,127,169,235,580,758,227,274,241";//Garments Delivery Entry
	else if($data==87) $report_format_id="147,195,242";//Order Wise Production Report
	else if($data==88) $report_format_id="272,273";
	else if($data==89) $report_format_id="13,15,16,177,175,746,220,235"; //Multiple Job Wise Embellishment Work Order
	else if($data==90) $report_format_id="10,17,61";
	//For Woven
	else if($data==92) $report_format_id="155,749";
	else if($data==93) $report_format_id="178,278,279";
	else if($data==94) $report_format_id="107,178,195,242";
	else if($data==95) $report_format_id="108,23,138,290";//Dyeing Production Report-V3
	else if($data==96) $report_format_id="261,281,282,283,284,305,160";//Export CI Statement
	else if($data==97) $report_format_id="23,24,25,26,27,29,182,285";//Woven Order Wise Budget Report
	else if($data==98) $report_format_id="35,36,78,137";//Daily Yarn Demand Entry
	else if($data==99) $report_format_id="78"; //Wash Dyes Chemical Issue
	else if($data==100) $report_format_id="78"; //Batch Creation For Gmts. Wash
	else if($data==101) $report_format_id="2,78"; //Wash Recipe Entry
	else if($data==102) $report_format_id="78"; //Wash Dyes And Chemical Issue Requisition
	else if($data==103) $report_format_id="78"; //Wet Production
	else if($data==104) $report_format_id="78"; //Dry Production
	else if($data==105) $report_format_id="78,66"; //Wash Delivery
	else if($data==106) $report_format_id="78"; //Wash Bill Issue
	else if($data==107) $report_format_id="78"; //Wash Delivery Return
	else if($data==108) $report_format_id="108,195,23,150"; //Statement of Total Export Value and CM
	else if($data==109) $report_format_id="107,108,195,263"; //Fabric Production Status Report - Sales Order
	else if($data==110) $report_format_id="149,222,256,266,267";//Closing Stock Report for Embroidery
	// else if($data==112) $report_format_id="108,289,472";//Bank Liability Position As Of Today
	else if($data==113) $report_format_id="579,578,577,124,108,363,255";//Order Wise Grey Fabrics Stock Report
	else if($data==114) $report_format_id="23,124,223,724";//Fabric Receive Status Report2;
	else if($data==115) $report_format_id="233,234,240,344,678,679,680,681,682,683,684,685,686,687,692,693,694,695,697,698,699,700,701,702,703,704,705,713,314,718,720,721,731,760,357,358,361,368,369,372,374,375,376,378,391,392,410,411,412,413,418,435,436,457,458,459,461,462,463,464,465,467,468,469,470,471,241,474,493,496,497,499,500,799,822,823,824,836,851,404,419,426,582,903,905"; // BTB/Margin LC
	else if($data==116) $report_format_id="256,263,264,420,40"; //Daily Yarn Issue Report
	else if($data==117) $report_format_id="294,295,296,297,298,299,300,301,302,303,714,722,366,367,371,431,434,439,442,827,828,838"; // Woven Cut and Lay Entry Ratio Wise
	//else if($data==118) $report_format_id="294,295,296,297,298,299,300,301,302,303"; //Cut and Lay Entry Ratio Wise 4
	else if($data==118) $report_format_id="297,298,333,332,331,300,328,330,329,379,380,714,453,787,806,808,857,858"; //Cut and Lay Entry Ratio Wise 4
	else if($data==119) $report_format_id="115,116"; //Scrap Material Issue
	else if($data==120) $report_format_id="195,222"; //Weekly Capacity and Order Booking Status V2

	else if($data==121) $report_format_id="250,282,281,165,305,306,283,364,407,284"; //Shipment Shedule Report 307,406,712
	
	else if($data==122) $report_format_id="51,158,159,170,171,192,197,307,311,313,260,381,761,403,770,473,852,862,873,63";//For Pre-Costing V2 woven
	else if($data==123) $report_format_id="86,66,111,129";//Embellishment Issue
	else if($data==124) $report_format_id="222,259,242,359";//Daily Gate In And Out Report
	else if($data==125) $report_format_id="78,66,85,89,129";//Woven Finish Fabric Receive
	else if($data==126) $report_format_id="78,85";//Woven Finish Fabric Issue
	else if($data==127) $report_format_id="78";//Woven Finish Fabric Receive Return
	else if($data==128) $report_format_id="78";//Woven Finish Fabric Roll Issue
	else if($data==129) $report_format_id="78";//Woven Finish Fabric issue return
	else if($data==130) $report_format_id="78";//Woven Finish Fabric Transfer Entry
	else if($data==131) $report_format_id="78,696";//Woven Finish Roll Issue Return
	else if($data==132) $report_format_id="78,84,732,85,430,137";//Dyes And Chemical Purchase Order
	else if($data==133) $report_format_id="66,85,706,129,161";//Knitting Bill Issue
	else if($data==134) $report_format_id="260,243,708,709";//Daily Cutting And Input Inhand Report
	else if($data==135) $report_format_id="108,242,259,359";//Order Wise Finish Fabric Stock
	else if($data==136) $report_format_id="108,267,710,711,712,750,161,758,23,389,122";//Dyeing Report
	else if($data==137) $report_format_id="108,195,242";//Sample Followup Report- Sweater
	else if($data==138) $report_format_id="143,84,85,151,160,175,155,274,72,191,428,241,10";//Woven Partial Fabric Booking
	else if($data==139) $report_format_id="256,267,715,716,717,438";//Closing Stock Dyes and Chemical

	else if($data==140) $report_format_id="315,316,317,318,319,320,321,322,337,748,355,390,810,880,872,847,583";//Knitting Production 66,136,137,129,72 331

	else if($data==141) $report_format_id="313,323"; //Sourcing Post Cost Sheet
	else if($data==142) $report_format_id="45,109,110,111,129,161,746,220,235,274,241,427,28"; //Sample Requisition With Booking
	else if($data==143) $report_format_id="725,726,727,728,729,733,740,741,742"; //Date Wise Dyes Chemical Receive Issue
	else if($data==144) $report_format_id="222,107,734,735,736,149";//Closing Stock Report for Trims
	else if($data==145) $report_format_id="298,299,328,329,330,331,332,333,343,373,393,409,429,443,454,784,785,834,838,842,380,843,844,856,869,337"; //Cut and Lay Entry Ratio Wise 3
	else if($data==146) $report_format_id="317,322,334,320,331,72,810,880"; //Roll Splitting Before Issue 337
	else if($data==147) $report_format_id="739"; //Comparative Statement
	else if($data==148) $report_format_id="195"; //Job Wise Cost Analysis Report-Woven
	else if($data==149) $report_format_id="108,743,744,745,752"; //Date Wise Production Report [CM]
	else if($data==150) $report_format_id="72,78,85,116,137,230,227"; //Fabric Sales Order Entry v2
	else if($data==151) $report_format_id="477,478,479,480,481,482,483,484,485,486,487,488,489,490,491,835,159"; //Export Pro Forma Invoice
	else if($data==152) $report_format_id="78"; //Demand For Accessories
	else if($data==153) $report_format_id="108,725"; //Wash Received and Delivery Statement
	else if($data==154) $report_format_id="222,259,242"; //Unit Wise Production 2
	else if($data==155) $report_format_id="753,754,755,756,757,78,466,476,829,427,426"; //Sales Contract Entry
	else if($data==156) $report_format_id="78,66,85,137,129"; //Item Issue Requisition
	else if($data==157) $report_format_id="23,124,223"; //Yarn Purchase Requisition Follow Up Report
	else if($data==158) $report_format_id="108,259,242,359,712,389,191"; //Production Summary [Fabric And Garments]
	else if($data==159) $report_format_id="341,342"; //Hourly Production Monitoring Reports
	else if($data==160) $report_format_id="108,259"; //Sample Production Report
	else if($data==162) $report_format_id="107,108,195,710,750,854,855,293"; //Batch wise Dyeing and Finishing Cost
	else if($data==163) $report_format_id="346,347,348,349,350"; //	SubCon Dye And Finishing Delivery
	else if($data==164) $report_format_id="86,66,85,89,68,69,129,72";// Knit Finish Fabric Roll Issue
	else if($data==165) $report_format_id="178,195,242,256,263,264,352,734,422";// Finish Fabric Closing Stock
	else if($data==166) $report_format_id="178,195,242,359,352,712,389,758,220,235,274";// Style Wise Finish Fabric Status
	else if($data==167) $report_format_id="86,68,69,84,764";// Finish Fabric Roll Delivery To Store
	else if($data==168) $report_format_id="130,131,133,353,572,356,132,424,503,807";// Knitting Plan Report[Sales]
	else if($data==169) $report_format_id="86,84";// Finish Fabric Roll Delivery To Store
	else if($data==170) $report_format_id="86,84,85,346";// Sample Delivery Entry
	else if($data==171) $report_format_id="86,84,85,68,69,89,129,848";// Knit Grey Fabric Roll Receive
	else if($data==172) $report_format_id="86,69";// Knit Finish Fabric Roll Receive
	else if($data==173) $report_format_id="86,84,377,160";// Trims Order Receive
	else if($data==174) $report_format_id="86,84,85,360,129,137,161,230,220,235";// Trims Delivery Entry
	else if($data==175) $report_format_id="86,84,85,160";// Trims Bill Entry
	else if($data==176) $report_format_id="108,195";//Fabric Booking Approval New
	else if($data==177) $report_format_id="108,195";//Short Fabric Booking Approval New
	else if($data==178) $report_format_id="108,195";//pre-costing Approval
	else if($data==179) $report_format_id="84,85";//Dyes And Chemical Issue
	else if($data==263) $report_format_id="78,84";//Dyes And Chemical Receive
	else if($data==180) $report_format_id="56,127,41";//Textile Tna
	else if($data==181) $report_format_id="86,116,136";//Trims Issue
	else if($data==182) $report_format_id="66,85,143,160,129";//General Item Issue
	else if($data==183) $report_format_id="86,116,85,751,479,137";//Pro Forma Invoice V2
	else if($data==184) $report_format_id="147,195,107";//Pro Forma Invoice Approval Status Report
	else if($data==185) $report_format_id="80,108,149";//Order wise Production and Delivery Report
	else if($data==186) $report_format_id="726,727,149";//Date Wise Finish Fabric Receive Issue
	else if($data==187) $report_format_id="726,727,725,733,384,385,386,387,206";//Date Wise Item Receive and Issue
	else if($data==188) $report_format_id="147,259,763,242";//MIS Report
	else if($data==189) $report_format_id="678,395,396,397,398,399,400,401,402";//Import Document Acceptance
	else if($data==190) $report_format_id="108,259,125";//Style Wise Production Summary
	else if($data==191) $report_format_id="86,116,136,137";//Trims Receive Entry
	else if($data==192) $report_format_id="135,136,137,72,129,191,220,235,274,241,427,28,280,304";//Bundle Wise Sewing Input
	else if($data==193) $report_format_id="107,178,195,242,359";//Work Order [Booking] Report
	else if($data==194) $report_format_id="78,66,85,137,129,72,191,220,235";//General Item Receive
	else if($data==195) $report_format_id="8,93,163,164,209,177,129,161,191,220,274";//Multi Job Wise Service Booking Dyeing
	else if($data==196) $report_format_id="108,195";//Store Item List
	else if($data==197) $report_format_id="54,108,256,267";//Order Wise Grey Fabrics Stock Report V2
	else if($data==198) $report_format_id="766,108,195,242,359,712,289,23,408,446,389,191";//Color and Size Breakdown Report
	else if($data==199) $report_format_id="78,84,85,416,417";//Roll Wise Grey Fabric Requisition For Transfer
	else if($data==200) $report_format_id="261,23,150,421,422,423,282";//Style and Store Wise Grey Fabric Stock Report
    else if($data==201) $report_format_id="108,447,448,449,450,195";//Style wise CM Report
	else if($data==202) $report_format_id="109,110,111";//Style wise CM Report
	else if($data==203) $report_format_id="282,283";//Shipment Schedule Details
	else if($data==204) $report_format_id="108,195,242";//File Wise Yarn Receive and Issue Report
	else if($data==205) $report_format_id="108,195,455,456,242,359";//PI Statement Report
	else if($data==206) $report_format_id="732,86,84,85";//Service Work Order
	else if($data==207) $report_format_id="8,12,16,177,175,176,172,508";//Service Booking for Dyeing
	else if($data==208) $report_format_id="86,96,195";//File Wise Export Status
	else if($data==209) $report_format_id="86,116";//Sweater Sample Requisition
	else if($data==210) $report_format_id="66";//Raw Material Issue Requisition
	else if($data==211) $report_format_id="66,149,223,771,772,773";//Date and Style wise Inspection Report
	else if($data==212) $report_format_id="108,195,242,359";//Master Style Follow Up Report
	else if($data==213) $report_format_id="115,66,111,137";//Finish Fabric Delivery To Garments
	else if($data==214) $report_format_id="178,255";//Sales Forecast Vs Booked
	else if($data==215) $report_format_id="250,282";//Daily Ex-Factory Report Order/Style wise
	else if($data==216) $report_format_id="134,135,136";//Finish Fabric Roll Delivery To Garments
	else if($data==217) $report_format_id="300,315,316,317,320,334,136,502,137,810";//Subcon Knitting Production
	else if($data==218) $report_format_id="108,195,242";//PI Approval New
	else if($data==219) $report_format_id="67,14,183,85,177,175,746,774,235,72";//Multiple Job Wise Trims Booking V2 -woven,
	else if($data==220) $report_format_id="108,259,242,359,712,389,887";//Style Closing Report
	else if($data==221) $report_format_id="150,777,778,149,850,421";//Rack Wise Grey Fabrics Stock Report Sales
	else if($data==222) $report_format_id="247,246,245,138,286,290,335";// Style Owner Wise Daily Knitting Production Report.
	else if($data==223) $report_format_id="779,780,781,782";// Gate In and Out Report

	else if($data==225) $report_format_id="108,195";// Purchase Requisition Approval Status Report
	else if($data==228) $report_format_id="2,3,6";// Yarn Dyeing Work Order Sales
	else if($data==229) $report_format_id="107,169,256,267,264,580,758";// Style wise Cost Comparison
	else if($data==230) $report_format_id="78,84,85";// Trims Receive Entry Multi Ref V3
	else if($data==232) $report_format_id="108,195,149,125,777,242";//Order Monitoring Report
	else if($data==233) $report_format_id="178,152,475,826";// Raw Material Stock Report
	else if($data==234) $report_format_id="109,116,281,85,305";// Grey Fabric Delivery to Store
	else if($data==235) $report_format_id="109,110,111,160";// Finish Fabric Delivery to Store
	else if($data==236) $report_format_id="108,149";// Order Booking Status Report 3
	else if($data==237) $report_format_id="78";// SubCon Material Receive
	else if($data==238) $report_format_id="109,110,732";// Service Requisition
	else if($data==239) $report_format_id="147,259,242";// Hourly Production Monitoring Report 2nd
	else if($data==240) $report_format_id="147,195,495,242";// Date Wise Delivery Report
	else if($data==241) $report_format_id="108,195,242,359";// Order Forecasting Report
	else if($data==242) $report_format_id="108,195,242,359,306";// Style Wise materials Follow up Report
	else if($data==243) $report_format_id="115,116,136,137,129,161,191,220,235,274,241";// Recipe Entry
	else if($data==244) $report_format_id="13,14"; //Multiple Job Wise Embellishment Work Order[WVN]
	else if($data==245) $report_format_id="150,84,85,137,129,788,789,790,791,792,793,794,795,796,797,798,853,72,584,777,904,907"; //Export Invoice
	else if($data==246) $report_format_id="296,297,298,333,332,331,300,328,330,329,379,380,429,714,453,784,787,806"; //Contrast Cutting Entry
	else if($data==247) $report_format_id="108,195"; //Cost Breakdown Analysis Report [Budget]
	else if($data==248) $report_format_id="108,195,242"; //Post Costing Report V4
	else if($data==249) $report_format_id="115,116"; //Export Proceeds Realization
	else if($data==250) $report_format_id="54,55,56,57,58,198,223,504,505,506,507"; //Woven TNA Progress Report
	else if($data==251) $report_format_id="108,195,242,359,712"; //Work Order Details Report
	else if($data==253) $report_format_id="108,115,116,195,242,136,859"; //Fabric Issue to Fin. Process
	else if($data==254) $report_format_id="108,422,195"; //abric Issue to Fin. Process
	else if($data==255) $report_format_id="115,116,137,129,72,816,817"; //Fabric Sales Order Entry [ Yarn Part ]
	else if($data==256) $report_format_id="107,121,123,127,264"; //Style Wise Trims Received Issue And Stock
	else if($data==257) $report_format_id="78,818"; //Knit Grey Fabric Receive
	else if($data==258) $report_format_id="66,78,85,129,137,161,819"; //Knit Finish Fabric Receive By Garments
	else if($data==259) $report_format_id="78,819"; //Knit Finish Fabric Receive By Garments
	else if($data==260) $report_format_id="108,242,259,359,712,389"; //Job/Order Wise Cutting Lay and Production Report
	else if($data==261) $report_format_id="78,84,85"; //Yarn Store Requisition Entry
	else if($data==262) $report_format_id="178,195,242,352,710";// Style Wise Finish Fabric Status 2
	else if($data==264) $report_format_id="753,754,755,829,757,830";// Export LC Entry
	else if($data==265) $report_format_id="108,249,223,831";// Order Follow-up Report Woven
	else if($data==266) $report_format_id="143,832,66,85,160,129";// Dyeing And Finishing Bill Issue
	else if($data==267) $report_format_id="143,66";// Dyeing And Finishing Bill Entry
	else if($data==268) $report_format_id="143,777";// Knitting Bill Entry
	else if($data==269) $report_format_id="78,84,85"; //Yarn Requisition Entry For Sales
	else if($data==270) $report_format_id="108,195"; //Order Allocation Details V2
	else if($data==271) $report_format_id="78,84,85,129,160"; //Cutting QC V2
	else if($data==272) $report_format_id="108,256"; //Roll Position Tracking Report
	else if($data==273) $report_format_id="108,195,242,4,5,840,841,359,712,389";//Daily Cutting And Input Inhand Report 2
	else if($data==274) $report_format_id="108,195,242,243";//Dyes Chemical Loan Ledger
	else if($data==275) $report_format_id="222,259";//Style wise Cost Comparison Woven
	else if($data==276) $report_format_id="86,110,85";//Printing Delivery Entry [Bundle]
	else if($data==277) $report_format_id="115,116";//Buyer Inquiry Woven Textile
	else if($data==278) $report_format_id="222,259";//Hourly Production Monitoring Report Chaity
	else if($data==279) $report_format_id="222,259,715,124,310";//Date Wise Shipment Status
	else if($data==280) $report_format_id="84,86";//Handloom/Strikeoff/Labdip Requisition
	else if($data==281) $report_format_id="78,84,85,129,160";//Yarn Purchase Order [Sweater]
	else if($data==282) $report_format_id="134,135";//Yarn Purchase Order [Sweater]
	else if($data==283) $report_format_id="66,85,137";//Roll wise Grey Sales Order To Sales Order Transfer
	else if($data==284) $report_format_id="66,72,85,86,129,137,191";//Bundle Wise Cutting Delivery To Input Challan
	else if($data==285) $report_format_id="86,165,167,168";//Bundle Issued to Special Work
	else if($data==286) $report_format_id="147,195";//Pre Costing/Budget List
	else if($data==287) $report_format_id="86,110";//Trims Receive Entry Multi Ref.
	else if($data==288) $report_format_id="108,256";//Buyer and Style Wise Trims Stock.
	else if($data==289) $report_format_id="210,134";//Machine Wash Requisition.
	else if($data==290) $report_format_id="8,14";//Service Booking for Dyeing v2

	else if($data==291) $report_format_id="2,6,45,53";//Quick Costing Woven

	else if($data==292) $report_format_id="108,745";//Date Wise Production Report [CM] 2
	else if($data==293) $report_format_id="147,195,242";//Date Wise Production Report [CM] 2
	else if($data==294) $report_format_id="108,195,242";//Line Wise Planning Report V2
	// report id name update
	else if($data==295) $report_format_id="108,242"; // Sample Progress Report
	else if($data==296) $report_format_id="108,195"; // Cross LC Report
	else if($data==297) $report_format_id="108,195,242,261,877,878,879"; // BTB or Margin LC Report
	else if($data==298) $report_format_id="108,195,242,359"; // Monthly Export Status summary
	else if($data==299) $report_format_id="178,195,242,359";//Style Wise Grey Fabric Stock Report-Sales
	else if($data==301) $report_format_id="108,195,242";//Style and Line Wise Production Report
	else if($data==302) $report_format_id="85,110,134";//Yarn receive
	else if($data==303) $report_format_id="178,195";//BOM Confirmation Before Approval
	else if($data==304) $report_format_id="116,85,89";//Dyes And Chemical Issue V2
	else if($data==311) $report_format_id="108,195";//Item Wise Purchase
	else if($data==305) $report_format_id="66,129,137,235,274";//Topping Adding Stripping Recipe Entry
	else if($data==306) $report_format_id="108,195,242,359,712,389,887";//Topping Adding Stripping Recipe Entry
	else if($data==307) $report_format_id="45";//Sample Or Additional Yarn WO
	else if($data==308) $report_format_id="108,893,894,895,896,897,898,899,900,901";//Sample Or Additional Yarn WO
	else if($data==309) $report_format_id="298,299";//Finish Fabric Production and QC By Roll
	else if($data==310) $report_format_id="66,85";
	else if($data==311) $report_format_id="66,85";
	else if($data==312) $report_format_id="108,195,242";
	else if($data==313) $report_format_id="78,66";
	else if($data==314) $report_format_id="3,6";//Order Entry by Matrix V2
	else $report_format_id="";

	echo load_html_head_contents("Report Button","../../../", 1, 1, $unicode,'','');

	$selected_btn_name_arr = explode(',', $txt_report_button_name);
	$txt_report_button_wise_user_id_arr = explode('*', $report_button_wise_user_id);
	// print_r($txt_report_button_wise_user_id_arr);die;

	$custom_data_arr = array();
	$btn_data_arr = array();
	foreach ($txt_report_button_wise_user_id_arr as $key => $btn_user_str) {
		if ($btn_user_str != null) {
			list($btn_id, $user_id) = explode('_', $btn_user_str);
			$custom_data_arr[$btn_id] = $btn_user_str;
			$btn_data_arr[$key] = $btn_id;
		}
	}
?>
	<script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_report_wise_user = new Array();

		function fnc_add_users(button_id, btn_name) {
			var page_link = 'report_settings_controller.php?action=openpopup_user_formate&data=' + $('#selected_button_user_id_' + button_id).val() + '&button_id=' + button_id;
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, 'User List', 'width=250px,height=300px,center=1,resize=1,scrolling=0', '../../');
			emailwindow.onclose = function() {
				var btn_name = btn_name;
				var txt_selected_user_id = this.contentDoc.getElementById("txt_selected_user_id").value;
				var txt_selected_button_id = this.contentDoc.getElementById("txt_selected_button_id").value;
				$('#selected_button_user_id_' + txt_selected_button_id).val('');
				if (txt_selected_user_id) {
					$("#color_selected_" + button_id).css("background-color", "yellow");
					$('#selected_button_user_id_' + txt_selected_button_id).val(txt_selected_button_id + '_' + txt_selected_user_id);
					// js_get_value(button_id, btn_name);
				} else {
					$("#color_selected_" + button_id).css("background-color", "");
				}
				$('#selected_button_id_' + txt_selected_button_id).val(txt_selected_button_id);
			}
		}

		function js_set_value(btn_id, btn_name) {
			//alert("data.");
			if ($("#selected_button_user_id_" + btn_id).val()) {
				$("#selected_button_user_id_" + btn_id).val('');
				document.getElementById('color_selected_' + btn_id).style.backgroundColor = 'white';
			} else {
				$("#selected_button_user_id_" + btn_id).val(btn_id + '_0');
				document.getElementById('color_selected_' + btn_id).style.backgroundColor = 'yellow';
			}
			js_get_value(btn_id, btn_name);
		}

		function js_get_value(btn_id, btn_name) {
			btn_id = btn_id * 1;
			if (jQuery.inArray(btn_id, selected_id) == -1) {
				selected_id.push(btn_id);
				selected_name.push(btn_name);
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == btn_id) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';

			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			$('#txt_report_format_id').val(id);
			$('#txt_report_format_name').val(name);
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form action="">
				<table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
					<thead>
						<th width="25">SL</th>
						<th width="50">Report ID</th>
						<th width="240">Report Button</th>
						<th>User</th>
					</thead>
				</table>
				<div style="width:420px; overflow-y:scroll; max-height:340px;">
					<table width="400" cellspacing="0" class="rpt_table" border="0" rules="all" align="left" id="item_table">
						<tbody>
							<?
							// print_r($btn_data_arr);
							$i = 1;
							$btn_name_arr = array();
							foreach (explode(",", $report_format_id) as $report_format_id) {
								if ($i % 2 == 0) $bgcolor = "#E9F3FF";
								else $bgcolor = "#FFFFFF";
								$btn_name_arr[$report_format_id] = $report_format[$report_format_id];

								if ($btn_data_arr[0] == $report_format_id) {
									$bgcolor = "green";
								} elseif (in_array($report_format_id, $btn_data_arr)) {
									$bgcolor = "yellow";
								}
							?>
								<tr bgcolor="<?= $bgcolor; ?>" id="color_selected_<?= trim($report_format_id); ?>">
									<td onClick="js_set_value(<?= $report_format_id; ?>,'<?= $report_format[$report_format_id] ?>')" width="25"><?= $i; ?></td>
									<td onClick="js_set_value(<?= $report_format_id; ?>,'<?= $report_format[$report_format_id] ?>')" width="50"><?= $report_format_id; ?></td>
									<td onClick="js_set_value(<?= trim($report_format_id); ?>,'<?= $report_format[trim($report_format_id)] ?>')" width="240"><?= $report_format[trim($report_format_id)]; ?></td>
									<td align="center">
										<input type="button" value="Add User" onClick="fnc_add_users(<?= trim($report_format_id); ?>,'<?= $report_format[trim($report_format_id)] ?>');" style="width:70px" class="formbutton">
										<input type="hidden" id="selected_button_user_id_<?= trim($report_format_id); ?>" value="<?= $custom_data_arr[trim($report_format_id)]; ?>" />
									</td>
								</tr>
							<?
								$i++;
							}
							?>
						</tbody>
					</table>
				</div>
				<table width="420" cellspacing="0" cellpadding="0" style="border:none" align="center">
					<tr>
						<td align="center" height="30" valign="bottom">
							<div style="width:100%">
								<div style="width:50%;" align="center">
									<input type="hidden" id="txt_report_format_id" value="">
									<input type="hidden" id="txt_report_format_name" value="<?= implode(',', $txt_report_button_name_arr); ?>">
									<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
								</div>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>
	<script>
		setFilterGrid('item_table', -1);

		var btn_name_arr = '<?= implode(',', $selected_btn_name_arr); ?>';
		var btn_data_arr = '<?= implode(',', $btn_data_arr); ?>';
		btn_name_arr = btn_name_arr.split(',');
		btn_data_arr = btn_data_arr.split(',');
		var i = 0;
		btn_data_arr.forEach((btn_id) => {

			btn_id = btn_id * 1;
			var btn_name = btn_name_arr[i];
			if (btn_id) {
				js_get_value(btn_id, btn_name);
			}
			i++;
		});
	</script>

	</html>
<?php
}


if ($action == "openpopup_user_formate") {
	extract($_REQUEST);
	list($buttonId, $user_ids) = explode('_', $data);
	echo load_html_head_contents("User Select", "../../../", 1, 1, $unicode, '', '');
?>
	<script>
		var userId = '<?= $user_ids; ?>';

		function user_check_all_data(allUserStr) {
			allUserArr = allUserStr.split(',');
			allUserArr.forEach((user_id) => {
				user_js_set_value(user_id);
			});
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		var selected_id = new Array();

		function user_js_set_value(user_id) {
			user_id = user_id * 1;
			toggle(document.getElementById('tr_' + user_id), '#E9F3FF');

			if (jQuery.inArray(user_id, selected_id) == -1) {
				selected_id.push(user_id);
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == user_id) break;
				}
				selected_id.splice(i, 1);
			}

			var id = ''
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			$('#txt_selected_user_id').val(id);
		}
	</script>
	</head>

	<body>
		<?
		$sql_users = sql_select("select USER_NAME,ID from user_passwd where valid=1 order by user_name ASC");
		?>
		<div align="center" style="width:100%;">
			<input type="hidden" id="txt_selected_user_id" name="txt_selected_user_id" value="" />
			<input type="hidden" id="txt_selected_button_id" name="txt_selected_button_id" value="<?= $button_id; ?>" />
			<table width="220" cellspacing="0" class="rpt_table" border="0" rules="all">
				<thead>
					<th width="40">User ID</th>
					<th>User</th>
				</thead>
			</table>
			<div style="width:220px; max-height:220px; overflow-y:scroll;">
				<table cellspacing="0" width="200" class="rpt_table" border="0" rules="all" id="item_table2" align="left">
					<tbody>
						<?
						$i = 1;
						$user_id_arr = array();
						foreach ($sql_users as $key => $user) {
							$bgcolor = ($i % 2 == 0) ? "#E9F3FF" : "#FFFFFF";
							$user_id_arr[$user['ID']] = $user['ID'];
						?>
							<tr bgcolor="<?= $bgcolor; ?>" onClick="user_js_set_value(<?= $user['ID']; ?>)" id="tr_<?= $user['ID']; ?>">
								<td width="40"><?= $user['ID']; ?></td>
								<td><?= $user['USER_NAME'] ?></td>
							</tr>
						<?
							$i++;
						}
						?>
					</tbody>
				</table>
			</div><br>
			<table cellspacing="0" cellpadding="0" style="border:none" align="center">
				<tr>
					<td valign="bottom">
						<div style="width:100%">
							<div style="width:55%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all" onClick="user_check_all_data('<?= implode(',', $user_id_arr); ?>')" /> Check / Uncheck All
							</div>
							<div style="width:17%; float:left" align="left">
								<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</body>
	<script>
		setFilterGrid('item_table2', -1);
		if (userId != '') {
			user_check_all_data(userId);
		}
	</script>

	</html>
<?

}



if ($action == "load_drop_down_report_name") {
	$report_format_id = getBtnMaping($data);
	echo create_drop_down("cbo_format_name", 182, $report_format, "", 0, "--- Select Report ---", $selected, "", "", $report_format_id);
	exit();
}

if ($action == "eval_multi_select") {
	echo "set_multiselect('cbo_format_name','0','0','','0');\n";
	exit();
}

if ($action == "get_page_url") {
	echo return_field_value("f_location", "main_menu", "m_menu_id='$data'");
	//echo "sds";
}

if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	if ($report_button_wise_user_id != '0') {
		$report_button_wise_user_id = preg_replace("/'/", '', $report_button_wise_user_id);

		$report_button_wise_user_ids = explode("*", $report_button_wise_user_id);

		$convert_to_array = [];
		foreach ($report_button_wise_user_ids as $button_wise_user_id) {
			if ($button_wise_user_id != NULL) {
				array_push($convert_to_array, $button_wise_user_id);
			}
		}
		$button_wise_user_ids = array();
		for ($i = 0; $i < count($convert_to_array); $i++) {
			$key_value = explode('_', $convert_to_array[$i]);
			$button_wise_user_ids[$key_value[0]] = $key_value[1];
		}
	}


	if ($operation == 0)  // Insert Here
	{
		// echo "insert heare";exit;
		$con = connect();

		if (is_duplicate_field("template_name", "lib_report_template", "  template_name=$cbo_company_id  and module_id=$cbo_module_name and report_id=$cbo_report_name  and is_deleted=0") == 1) {
			echo "11**Duplicate Not Allow Please Update.";
			disconnect($con);
			die;
		}

		$cbo_format_name = str_replace("'", "", $cbo_format_name);
		$id = return_next_id("id", "lib_report_template", 1);
		$data_array = "(" . $id . "," . $cbo_company_id . "," . $cbo_module_name . "," . $cbo_report_name . ",'" . $cbo_format_name . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_status . ",0)";

		$dtls_id = return_next_id("id", "lib_report_template_details", 1);
		if ($report_button_wise_user_id != '0') {
			foreach ($button_wise_user_ids as $key => $button_wise_user_id) {
				foreach (explode(",", $button_wise_user_id) as $data) {
					if ($data_array_dtls) {
						$add_comma = ", ";
					}
					$data_array_dtls .= "$add_comma (" . $dtls_id . ", " . $id . ", " . $key . ", " . $data . ", " . $_SESSION['logic_erp']['user_id'] . ", '" . $pc_date_time . "', " . $cbo_status . ", 0)";
					$dtls_id++;
				}
			}
		}


		$field_array = "id, template_name, module_id, report_id, format_id, inserted_by, insert_date, status_active, is_deleted";
		$rID = sql_insert("lib_report_template", $field_array, $data_array, 1);

		$field_array_dtls = "id, mst_id, button_id, user_id, inserted_by, insert_date, status_active, is_deleted";
		$rID2 = sql_insert("lib_report_template_details", $field_array_dtls, $data_array_dtls, 1);

		// echo "10**$rID**$rID2";oci_rollback($con);disconnect($con);die;

		if ($rID && $rID2) {
			oci_commit($con);
			echo "0**" . $id . "**" . str_replace("'", "", $cbo_company_id) . "**" . str_replace("'", "", $cbo_module_name) . "";
		} else {
			oci_rollback($con);
			echo "005**" . $id . "**" . str_replace("'", "", $cbo_company_id) . "**" . str_replace("'", "", $cbo_module_name) . "";
		}

		disconnect($con);
		die;
	} else if ($operation == 1)   // Update Here
	{
		//print_r($update_id);exit;
		$con = connect();
		$field_array = "template_name*module_id*report_id*format_id*updated_by*update_date*status_active*is_deleted";
		if ($update_id != "") {
			if (is_duplicate_field("template_name", "lib_report_template", " id!=$update_id and template_name=$cbo_company_id  and module_id=$cbo_module_name and report_id=$cbo_report_name  and is_deleted=0") == 1) {
				//echo "11**0"; disconnect($con); die;
			}
			$data_array = "" . $cbo_company_id . "*" . $cbo_module_name . "*" . $cbo_report_name . "*" . $cbo_format_name . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $cbo_status . "*0";
		}
		if ($update_id == "") {
			if (is_duplicate_field("template_name", "lib_report_template", "  template_name=$cbo_company_id  and module_id=$cbo_module_name and report_id=$cbo_report_name  and is_deleted=0") == 1) {
				//echo "11**0"; disconnect($con); die;
			}
			$data_array = "" . $cbo_company_id . "*" . $cbo_module_name . "*" . $cbo_report_name . "*" . $cbo_format_name . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $cbo_status . "*0";
		}
		$data_array_dtls = '';
		$dtls_id = return_next_id("id", "lib_report_template_details", 1);
		if ($report_button_wise_user_id != '0') {
			foreach ($button_wise_user_ids as $key => $button_wise_user_id) {
				foreach (explode(",", $button_wise_user_id) as $data) {
					if ($data_array_dtls) {
						$add_comma = ", ";
					}
					$data_array_dtls .= "$add_comma (" . $dtls_id . ", " . $update_id . ", " . $key . ", " . $data . ", " . $_SESSION['logic_erp']['user_id'] . ", '" . $pc_date_time . "', " . $cbo_status . ", 0)";
					$dtls_id++;
				}
			}
		}

		//echo "insert into lib_report_template $field_array values $data_array";die;



		$rID = sql_update("lib_report_template", $field_array, $data_array, "id", $update_id, 1);

		$rID_delete = sql_select("delete from lib_report_template_details where mst_id=$update_id");

		$field_array_dtls = "id, mst_id, button_id, user_id, inserted_by, insert_date, status_active, is_deleted";
		$rID2 = sql_insert("lib_report_template_details", $field_array_dtls, $data_array_dtls, 1);


		if ($rID) {
			oci_commit($con);
			echo "1**" . str_replace("'", "", $update_id) . "**" . str_replace("'", "", $cbo_company_id) . "**" . str_replace("'", "", $cbo_module_name) . "**" . str_replace("'", "", $cbo_format_name) . "";
		} else {
			oci_rollback($con);
			echo "6**" . str_replace("'", "", $update_id) . "**" . str_replace("'", "", $cbo_company_id) . "**" . str_replace("'", "", $cbo_module_name) . "**" . str_replace("'", "", $cbo_format_name) . "";
		}

		disconnect($con);
		die;
	} else if ($operation == 2) //Delete here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$field_array = "updated_by*update_date*status_active*is_deleted";
		$data_array = "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
		$rID = sql_delete("lib_report_template", $field_array, $data_array, "id", "" . $update_id . "", 1);
		if ($db_type == 0) {
			if ($rID) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $update_id) . "**" . str_replace("'", "", $cbo_company_id) . "";
			} else {
				mysql_query("ROLLBACK");
				echo "7**" . str_replace("'", "", $update_id) . "**" . str_replace("'", "", $cbo_company_id) . "";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $update_id) . "**0";
			} else {
				oci_rollback($con);
				echo "7**" . str_replace("'", "", $update_id) . "**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "load_drop_down_report_list_view") {
	$sql = "select id, template_name, module_id, report_id, format_id, status_active from lib_report_template where template_name='$data' and is_deleted=0 and status_active in(1,2) order by id";
	$module_lib = return_library_array("select m_mod_id, main_module from main_module", 'm_mod_id', 'main_module');
	//$menu_lib=return_library_array( "select m_menu_id, menu_name from main_menu",'m_menu_id','menu_name');
	$company_id = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$arr = array(0 => $company_id, 1 => $module_lib, 2 => $report_name, 3 => $format_name_arr, 4 => $row_status);

	echo create_list_view("list_view", "Company Name,Module Name,Report Name,Report Format,Status", "100,100,100,100,60", "550", "240", 0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "template_name,module_id,report_id,format_id,status_active", $arr, "template_name,module_id,report_id,format_id,status_active", "requires/report_settings_controller", 'setFilterGrid("list_view",-1);', '0,0,0,0,0');
	exit();
}

if ($action == "report_settings") {
	$sqls = sql_select("select id, format_id from lib_report_template where is_deleted=0 and template_name='$data'");
	//$sqls=sql_select("select id, mst_id, button_id, user_id from lib_report_template_details where is_deleted=0 and mst_id='$data'");

	foreach ($sqls as $val) {
		if ($val[csf("format_id")] != "" || $val[csf("format_id")] != 0) {
			if (strpos($val[csf("format_id")], ",") == false) {
				$format_name_arr[$val[csf("id")]] = $report_format[$val[csf('format_id')]];
			} else {
				$format_name = "";
				$vals = explode(",", $val[csf("format_id")]);
				foreach ($vals as $row_id) {
					if ($format_name == "") {
						$format_name .= $report_format[$row_id];
					} else {
						$format_name .= ',' . $report_format[$row_id];
					}
				}
				$format_name_arr[$val[csf("id")]] = $format_name;
			}
		}
	}

	// echo "<pre>";
	// print_r($format_name_arr);
	// echo "</pre>";
	// exit;

	$sql = "select id, template_id, template_name, module_id, report_id, format_id, status_active from lib_report_template where template_name='$data' and is_deleted=0";

	$module_lib = return_library_array("select m_mod_id, main_module from main_module", 'm_mod_id', 'main_module');
	$company_id = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$arr = array(0 => $company_id, 1 => $module_lib, 2 => $report_name, 3 => $format_name_arr, 4 => $row_status);

	echo create_list_view("list_view", "Company Name, Module Name, Report Name, Report Format, Status", "100,100,100,100,60", "550", "240", 0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "template_name,module_id,report_id,id,status_active", $arr, "template_name,module_id,report_id,id,status_active", "requires/report_settings_controller", '', '0,0,0,0,0');
	exit();
}

if ($action == "load_php_data_to_form") {

	//echo "select id, template_id, template_name, module_id, report_id, format_id, status_active from lib_report_template where id='$data' and status_active in(1,2) ";die;

	$nameArray = sql_select("select id, template_id, template_name, module_id, report_id, format_id, status_active from lib_report_template where id='$data' and status_active in(1,2,3)");

	foreach ($nameArray as $inf) {
		$templateDetailsArr = sql_select("select ID, MST_ID, BUTTON_ID, USER_ID,
		STATUS_ACTIVE, IS_DELETED from lib_report_template_details where MST_ID='$data' and STATUS_ACTIVE=1 and IS_DELETED=0");

		foreach ($templateDetailsArr as $templateDetails) {
			$templateDetail[$templateDetails['BUTTON_ID']][$templateDetails['USER_ID']] = $templateDetails['USER_ID'];
		}

		$tmpDataArr = array();
		foreach ($templateDetail as $btn => $urserArr) {
			$tmpDataArr[] = $btn . '_' . implode(',', $urserArr);
		}


		$tmpButtonArr = array();
		foreach (explode(',', $inf[csf("format_id")]) as $pbtnid) {
			$tmpButtonArr[$pbtnid] = $report_format[$pbtnid];
		}

		$report_button_wise_user_id = implode('*', $tmpDataArr);

		$button_name = implode(',', $tmpButtonArr);


		echo "document.getElementById('cbo_company_id').value = '" . ($inf[csf("template_name")]) . "';\n";
		echo "document.getElementById('cbo_module_name').value = '" . ($inf[csf("module_id")]) . "';\n";

		echo "load_drop_down( 'requires/report_settings_controller', " . $inf[csf("module_id")] . ", 'load_drop_down_report_module', 'report_name_td');";

		echo "document.getElementById('cbo_report_name').value  = '" . ($inf[csf("report_id")]) . "';\n";

		echo "document.getElementById('cbo_format_name').value  = '" . ($inf[csf("format_id")]) . "';\n";
		echo "document.getElementById('cbo_format_name_view').value = '" . ($button_name) . "';\n";
		echo "document.getElementById('txt_report_button_wise_user_id').value = '" . ($report_button_wise_user_id) . "';\n";


		echo "document.getElementById('cbo_status').value  = '" . ($inf[csf("status_active")]) . "';\n";
		echo "document.getElementById('update_id').value  = '" . ($inf[csf("id")]) . "';\n";


		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_report_settings',1);\n";
	}
}

if ($action == "report_settings_popup") {
	echo load_html_head_contents("Report Settings Info", "../../../", 1, 1, $unicode);
?>

	<script>
		function js_set_value(val) {
			var val = val.split("_");
			$('#id_field').val(val[0]);
			$('#name_field').val(val[1]);
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:530px;">
			<form name="searchscfrm" id="searchscfrm">
				<fieldset style="width:100%;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="450" class="rpt_table">
						<thead>
							<th>Template Name</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="id_field" id="id_field" value="" />
								<input type="hidden" name="name_field" id="name_field" value="" />
							</th>
						</thead>
						<tr class="general">
							<td>
								<input type="text" name="txt_template_name" id="txt_template_name" class="text_boxes" style="width:150px">
							</td>
							<td>
								<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_template_name').value, 'create_popup_list_view', 'search_div', 'report_settings_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
							</td>
						</tr>
					</table>
					<table width="450" style="margin-top:5px" align="center">
						<tr>
							<td colspan="2" id="search_div" align="center"></td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}

if ($action == "create_popup_list_view") {
	if ($data == "0") $data = "%%";
	else $data = "%" . trim($data) . "%";

	$sql = "select template_id, template_name from lib_report_template where template_name like '$data' and is_deleted=0 ";
	//echo $sql;
	echo create_list_view("list_view", "Template Id,Template Name", "130,250", "450", "240", 0, $sql, "js_set_value", "template_id,template_name", "", 1, "0,0", $arr, "template_id,template_name", "", '', '0,0');
	exit();
}
?>