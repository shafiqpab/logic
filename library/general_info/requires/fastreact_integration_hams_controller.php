<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
//include('fr_extra_arr_hams.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
//echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
echo "0**";

//$fr_product_type=return_library_array("select id,item_name from lib_garment_item where status_active=1 and is_deleted=0 order by item_name","id","item_name");
//$production_process=array(1=>"Cutting",2=>"Knitting",3=>"Dyeing",4=>"Finishing",5=>"Sewing",6=>"Fabric Printing",7=>"Washing",8=>"Gmts Printing",9=>"Embroidery",10=>"Iron",11=>"Gmts Finishing");

$companyUnitsql=sql_select("select id, company_name, company_short_name from lib_company where status_active =1 and is_deleted=0");
$companyUnitid="";
foreach($companyUnitsql as $row)
{
	$companyArr[$row[csf("id")]]=$row[csf("company_name")];
	$comShortName=$row[csf("company_short_name")];
	if($companyUnitid=="") $companyUnitid=$row[csf("id")]; else $companyUnitid.=','.$row[csf("id")];
}
unset($companyArr);

$production_process_freact=array(20=>"Cut",30=>"Print Send",40=>"Print Receive",50=>"Emb Send",60=>"Emb Receive",70=>"Sewing Input",80=>"Sewing Output",90=>"Wash Send",100=>"Wash Receive",110=>"Poly",120=>"Ship");


$gmt_prod_id_map[1]=20; //cut
//$gmt_prod_id_map[2]=6;
//$gmt_prod_id_map[3]=5;
$gmt_prod_id_map[4]=70; //Sewing Input
$gmt_prod_id_map[5]=80; //Sewing Output
$gmt_prod_id_map[7]=110; //Poly
//$gmt_prod_id_map[8]=12;
$gmt_prod_id_map[9]=30;  //Print Send
$gmt_prod_id_map[10]=40; //Print Rec
$gmt_prod_id_map[11]=50; //Emb Send
$gmt_prod_id_map[12]=90; //Wash Send
$gmt_prod_id_map[17]=60; //Emb Rec
$gmt_prod_id_map[18]=100; //Wash Rec
$gmt_prod_id_map[13]=120; //Ship


	$line_namecomp1=array( 
	1 => 110, //1
	2 => 120, //2
	3 => 130, //3
	4 => 140, //4
	5 => 150, //5
	6 => 160, //6
	7 => 170, //7
	8 => 180, //8
	9 => 190, //9
	10 => 200, //10
	11 => 250, //11
	12 => 260, //12
	13 => 270, //13
	14 => 280, //14
	15 => 290, //15
	16 => 300, //16
	17 => 310, //17
	18 => 350, //18
	19 => 360, //19
	20 => 370, //20
	21 => 380, //21
	22 => 390, //22
	23 => 400, //23
	24 => 410, //24
	120 => 420, //25
	121 => 430, //26
	122 => 440, //27
	28 => 500, //28
	29 => 510, //29
	27 => 520, //30
	26 => 530, //31
	25 => 540, //32
	123 => 550, //33
	35 => 560, //34
	46 => 860, //UG-1
	47 => 870, //UG-2
	48 => 880, //UG-3
	49 => 890, //UG-4
	50 => 900, //UG-5
	51 => 910, //UG-6
	52 => 920, //UG-7
	53 => 930, //UG-8
	54 => 940, //UG-9
	55 => 950, //UG-10
	56 => 960, //UG-11
	57 => 970, //UG-12
	58 => 980, //UG-13
	59 => 990, //UG-14
	60 => 1000, //UG-15
	61 => 1010, //UG-16
	110 => 1020, //UG-17
	111 => 1030, //UG-18
	
	62 => 1050, //UG-19
	63 => 1060, //UG-20
	64 => 1070, //UG-21
	65 => 1080, //UG-22
	66 => 1090, //UG-23
	67 => 1100, //UG-24
	68 => 1110, //UG-25
	69 => 1120, //UG-26
	70 => 1130, //UG-27
	71 => 1140, //UG-28
	72 => 1150, //UG-29
	73 => 1160, //UG-30
	94 => 1170, //UG-31
	95 => 1180, //UG-32
	112 => 1200, //UG-33
	113 => 1210, //UG-34
	114 => 1220, //UG-35
	115 => 1230, //UG-36
	116 => 1240, //UG-37
	117 => 1250, //UG-38
	118 => 1260, //UG-39
	119 => 1270 //UG-40
	);//HAMS Garments Ltd.
	//ERP LIB LINE ID=>FR LINE //ERP LINE NAME
	$line_namecomp2=array( 
	74 => 1360, //301
	75 => 1370, //302
	76 => 1380, //303
	77 => 1390, //304
	78 => 1400, //305
	79 => 1410, //306
	80 => 1420, //307
	81 => 1430, //308
	82 => 1440, //309
	83 => 1450, //310
	84 => 1460, //311
	85 => 1470, //312
	112 => 1500, //401
	113 => 1510, //402
	114 => 1520, //403
	115 => 1530, //404
	116 => 1540, //405
	117 => 1550, //406
	118 => 1560, //407
	119 => 1570, //408
	120 => 1580, //409
	121 => 1590, //410
	122 => 1600, //411
	123 => 1610, //412
	128 => 1650, //501
	129 => 1660, //502
	130 => 1670, //503
	131 => 1680, //504
	132 => 1690, //505
	133 => 1700, //506
	134 => 1710, //507
	135 => 1720 //508
	);//Dhaka Garments & Washing Ltd.
	//ERP LIB LINE ID=>FR LINE //ERP LINE NAME
	$line_namecomp3=array( 
	103 => 660, //1
	104 => 670, //2
	105 => 680, //3
	106 => 690, //4
	107 => 700, //5
	98 => 710, //6
	99 => 720, //7
	100 => 730, //8
	101 => 740, //9
	102 => 750 //10
	);//HAMS Fashion Ltd.
	//ERP LIB LINE ID=>FR LINE //ERP LINE NAME
	$line_namecomp4=array( 
	112 => 1810, //1
	113 => 1820, //2
	114 => 1830, //3
	115 => 1840, //4
	116 => 1850, //5
	117 => 1860, //6
	118 => 1870, //7
	119 => 1880, //8
	120 => 1890, //9
	121 => 1900, //10
	122 => 1910, //11
	123 => 1920, //12
	124 => 1930, //13
	125 => 1940, //14
	126 => 2000, //15
	127 => 2010, //16
	128 => 2020, //17
	129 => 2030, //18
	130 => 2040, //19
	131 => 2050, //20
	132 => 2060, //21
	133 => 2070, //22
	134 => 2080, //23
	135 => 2090, //24
	136 => 2100, //25
	137 => 2110, //26
	138 => 2120, //27
	139 => 2130, //28
	140 => 2140, //29
	141 => 2200, //30
	142 => 2210, //31
	143 => 2220, //32
	144 => 2230, //33
	145 => 2240, //34
	146 => 2250, //35
	147 => 2260, //36
	148 => 2270, //37
	149 => 2280, //38
	150 => 2290, //39
	151 => 2300 //40
	);//Victoria Intimates Ltd.
	//ERP LIB LINE ID=>FR LINE //ERP LINE NAME

if($companyUnitid==1) $line_name=$line_namecomp1;//HAMS Garments Ltd.
else if($companyUnitid==2) $line_name=$line_namecomp2;//Dhaka Garments & Washing Ltd.
else if($companyUnitid==3) $line_name=$line_namecomp3;//HAMS Fashion Ltd.
else if($companyUnitid==4) $line_name=$line_namecomp4;//Victoria Intimates Ltd.
else $line_name=array();

$gItemNameArr1=array( 
	1 => "T-Shirt-Long Sleeve",
	2 => "T-Shirt-Short Sleeve",
	3 => "Polo Shirt-Long Sleeve",
	4 => "Polo Shirt-Short Sleeve",
	5 => "Tank Top",
	6 => "T-Shirt 3/4 Sleeve",
	7 => "Hoodie",
	8 => "Henley",
	9 => "T-Shirt-Sleeveless",
	10 => "T-Shirt-Raglan Sleeve",
	11 => "T-Shirt Hi-Neck",
	12 => "Scarf",
	13 => "",
	14 => "Blazer",
	15 => "Jacket",
	16 => "Nightware-Top",
	17 => "Dress",
	18 => "Ladies Long Dress",
	19 => "Girls Dress",
	20 => "Full Pant",
	21 => "Short Pant",
	22 => "Trouser",
	23 => "Payjama",
	24 => "Romper",
	25 => "Romper",
	26 => "Romper",
	27 => "Romper",
	28 => "Legging",
	29 => "Short Pant",
	30 => "Skirts",
	31 => "Jump Suit",
	32 => "Cap",
	33 => "Jump Suit",
	34 => "Jump Suit",
	35 => "Jogger",
	36 => "Bag",
	37 => "Bra",
	38 => "Underwear",
	39 => "Sweat Shirt",
	40 => "Singlet",
	41 => "Singlet",
	42 => "Boxer",
	43 => "Stripe Boxer",
	44 => "Teens Boxer",
	45 => "Jersy Boxer",
	46 => "Panty",
	47 => "Slip Brief",
	48 => "Classic Brief",
	49 => "Short Brief",
	50 => "Mini Brief",
	51 => "Girls Brief",
	52 => "Girls Brief",
	53 => "Bikers",
	54 => "Underwear",
	55 => "",
	56 => "",
	57 => "",
	58 => "",
	59 => "",
	60 => "Socks",
	61 => "Socks",
	62 => "Socks",
	63 => "Socks",
	64 => "Socks",
	65 => "Socks",
	66 => "Socks",
	67 => "Sweat Pant",
	68 => "Sports Ware",
	69 => "Jogging Top",
	70 => "Long Pant",
	71 => "Long Pant",
	72 => "Bolero",
	73 => "Tank Top",
	74 => "Dress",
	75 => "T-Shirt-Long Sleeve",
	76 => "Dress",
	77 => "Underwear",
	78 => "Dress",
	79 => "Bag",
	80 => "Romper",
	81 => "Romper",
	82 => "Romper",
	83 => "Dress",
	84 => "Dress",
	85 => "Dress",
	86 => "Dress",
	87 => "Dress",
	88 => "Dress",
	89 => "Dress",
	90 => "Dress",
	91 => "Jogger",
	92 => "Jogger",
	93 => "Jogger",
	94 => "Jogger",
	95 => "Jegging",
	96 => "Nightware-Top",
	97 => " Shirt",
	98 => "Romper",
	99 => "Inner Top",
	100 => "Outer Top",
	101 => "T-Shirt-Long Sleeve",
	102 => "T-Shirt-Long Sleeve",
	103 => "T-Shirt V Neck",
	104 => "BLANKET",
	105 => "Playsuit",
	106 => "Sweater",
	107 => "Jumper",
	108 => "Cardigan",
	109 => "Nightware-Top",
	110 => "Nightware-Bottom",
	111 => "T-Shirt V Neck",
	112 => "T-Shirt-Short Sleeve",
	113 => "Mens Trunk",
	114 => "Mens Brief",
	115 => "Mens Boxer",
	116 => "Boys Boxer",
	117 => "Boys Brief",
	118 => "Girls Brief",
	119 => "Underwear",
	120 => "Ladies Thong",
	121 => "Mens Trunk",
	122 => "Mens Pattern Brief",
	123 => "Mens Pattern Boxer",
	124 => "T-Shirt-Long Sleeve",
	125 => "T-Shirt-Long Sleeve",
	126 => "T-Shirt-Long Sleeve",
	127 => "Swimwear",
	128 => "Twill Long pant",
	129 => "Twill Short pant",
	130 => "Twill Dress",
	131 => "Twill Shirt",
	132 => "Twill Jacket",
	133 => "Twill Skirt",
	134 => "Twill Boys pant",
	135 => "Twill Over All",
	136 => "Twill Short All",
	137 => "T-Shirt-Short Sleeve",
	138 => "Polo Shirt-Short Sleeve",
	139 => "Denim Long pant",
	140 => "Denim Short pant",
	141 => "Denim Dress",
	142 => "Denim Shirt",
	143 => "Denim Jacket",
	144 => "Denim Skirt",
	145 => "Denim Boys pant",
	146 => "Denim Over All",
	147 => "Denim Short All",
	148 => "Apron",
	149 => "Apron",
	150 => "Quilt Cover",
	151 => "Pillow Cover",
	152 => "Shirt",
	153 => "Apron",
	154 => "Short Pant",
	155 => "Pillow Cover",
	156 => "Over All",
	157 => "Tank Top",
	158 => "Dress",
	159 => "Sleeev Join",
	160 => "T-Shirt-Short Sleeve",
	161 => "T-Shirt-Short Sleeve",
	162 => "T-Shirt-Short Sleeve",
	163 => "Tank Top",
	164 => "Tank Top",
	165 => "Tank Top",
	166 => "Short Pant",
	167 => "Short Pant",
	168 => "Short Pant",
	169 => "Short Pant",
	170 => "Short Pant",
	171 => "Others",
	172 => "",
	173 => "Muffler",
	174 => "Muffler",
	175 => "Rubber + Pigment",
	176 => "Pigment + Puff"
	);//HAMS Garments Ltd.
	
$gItemNameArr2=array(
	1=>"T-Shirt-Long Sleeve",
	2=>"T-Shirt-Short Sleeve",
	3=>"Polo Shirt-Long Sleeve",
	4=>"Polo Shirt-Short Sleeve",
	5=>"Tank Top",
	6=>"T-Shirt 3/4 Sleeve",
	7=>"Hoodie",
	8=>"",
	9=>"T-Shirt-Sleeveless",
	10=>"T-Shirt-Raglan Sleeve",
	11=>"T-Shirt Hi-Neck",
	12=>"Scarf",
	13=>"",
	14=>"Blazer",
	15=>"Jacket",
	16=>"Nightware-Top",
	17=>"Dress",
	18=>"Ladies Long Dress",
	19=>"Girls Dress",
	20=>"Full Pant",
	21=>"Short Pant",
	22=>"Trouser",
	23=>"Payjama",
	24=>"Romper",
	25=>"Romper",
	26=>"Romper",
	27=>"Romper",
	28=>"Legging",
	29=>"Short Pant",
	30=>"Skirts",
	31=>"Jump Suit",
	32=>"Cap",
	33=>"Jump Suit",
	34=>"Jump Suit",
	35=>"Jogger",
	36=>"Bag",
	37=>"Bra",
	38=>"Underwear",
	39=>"Sweat Shirt",
	40=>"Singlet",
	41=>"Singlet",
	42=>"Boxer",
	43=>"Stripe Boxer",
	44=>"Teens Boxer",
	45=>"Jersy Boxer",
	46=>"Panty",
	47=>"Slip Brief",
	48=>"Classic Brief",
	49=>"Short Brief",
	50=>"Mini Brief",
	51=>"Girls Brief",
	52=>"Girls Brief",
	53=>"Bikers",
	54=>"Underwear",
	55=>"",
	56=>"",
	57=>"",
	58=>"",
	59=>"",
	60=>"Socks",
	61=>"Socks",
	62=>"Socks",
	63=>"Socks",
	64=>"Socks",
	65=>"Socks",
	66=>"Socks",
	67=>"Sweat Pant",
	68=>"",
	69=>"",
	70=>"Long Pant",
	71=>"Long Pant",
	72=>"",
	73=>"Tank Top",
	74=>"Dress",
	75=>"T-Shirt-Long Sleeve",
	76=>"Dress",
	77=>"Underwear",
	78=>"Dress",
	79=>"Bag",
	80=>"Romper",
	81=>"Romper",
	82=>"Romper",
	83=>"Dress",
	84=>"Dress",
	85=>"Dress",
	86=>"Dress",
	87=>"Dress",
	88=>"Dress",
	89=>"Dress",
	90=>"Dress",
	91=>"Jogger",
	92=>"Jogger",
	93=>"Jogger",
	94=>"Jogger",
	95=>"Jegging",
	96=>"Nightware-Top",
	97=>" Shirt",
	98=>"Romper",
	99=>"Inner Top",
	100=>"Outer Top",
	101=>"T-Shirt-Long Sleeve",
	102=>"T-Shirt-Long Sleeve",
	103=>"T-Shirt V Neck",
	104=>"BLANKET",
	105=>"Playsuit",
	106=>"Sweater",
	107=>"Jumper",
	108=>"Cardigan",
	109=>"Nightware-Top",
	110=>"Nightware-Bottom",
	111=>"T-Shirt V Neck",
	112=>"T-Shirt-Short Sleeve",
	113=>"Mens Trunk",
	114=>"Mens Brief",
	115=>"Mens Boxer",
	116=>"Boys Boxer",
	117=>"Boys Brief",
	118=>"Girls Brief",
	119=>"Underwear",
	120=>"Ladies Thong",
	121=>"Mens Trunk",
	122=>"Mens Pattern Brief",
	123=>"Mens Pattern Boxer",
	124=>"T-Shirt-Long Sleeve",
	125=>"T-Shirt-Long Sleeve",
	126=>"T-Shirt-Short Sleeve",
	127=>"Swimwear",
	128=>"Twill Long pant",
	129=>"Twill Short pant",
	130=>"Twill Dress",
	131=>"Twill Shirt",
	132=>"Twill Jacket",
	133=>"Twill Skirt",
	134=>"Twill Boys pant",
	135=>"Twill Over All",
	136=>"Twill Short All",
	137=>"T-Shirt-Short Sleeve",
	138=>"Polo Shirt-Short Sleeve",
	139=>"Denim Long pant",
	140=>"Denim Short pant",
	141=>"Denim Dress",
	142=>"Denim Shirt",
	143=>"Denim Jacket",
	144=>"Denim Skirt",
	145=>"Denim Boys pant",
	146=>"Denim Over All",
	147=>"Denim Short All",
	148=>"Apron",
	149=>"Apron",
	150=>"Quilt Cover",
	151=>"Pillow Cover",
	152=>" Shirt",
	153=>"Apron",
	154=>"Short Pant",
	155=>"Pillow Cover",
	156=>"Over All",
	157=>"Tank Top",
	158=>"Dress",
	159=>"",
	160=>"T-Shirt-Short Sleeve",
	161=>"T-Shirt-Short Sleeve",
	162=>"T-Shirt-Short Sleeve",
	163=>"Tank Top",
	164=>"Tank Top",
	165=>"Tank Top",
	166=>"Short Pant",
	167=>"Short Pant",
	168=>"Short Pant",
	169=>"Short Pant",
	170=>"Short Pant",
	171=>"",
	172=>"",
	173=>"Muffler",
	174=>"Muffler");//Dhaka Garments & Washing Ltd.
	
$gItemNameArr3=array( 
	1=>"T-Shirt-Long Sleeve",
	2=>"T-Shirt-Short Sleeve",
	3=>"Polo Shirt-Long Sleeve",
	4=>"Polo Shirt-Short Sleeve",
	5=>"Tank Top",
	6=>"T-Shirt 3/4 Sleeve",
	7=>"Hoodie",
	8=>"",
	9=>"T-Shirt-Sleeveless",
	10=>"T-Shirt-Raglan Sleeve",
	11=>"T-Shirt Hi-Neck",
	12=>"Scarf",
	13=>"",
	14=>"Blazer",
	15=>"Jacket",
	16=>"Nightware-Top",
	17=>"Dress",
	18=>"Ladies Long Dress",
	19=>"Girls Dress",
	20=>"Full Pant",
	21=>"Short Pant",
	22=>"Trouser",
	23=>"Payjama",
	24=>"Romper",
	25=>"Romper",
	26=>"Romper",
	27=>"Romper",
	28=>"Legging",
	29=>"Short Pant",
	30=>"Skirts",
	31=>"Jump Suit",
	32=>"Cap",
	33=>"Jump Suit",
	34=>"Jump Suit",
	35=>"Jogger",
	36=>"Bag",
	37=>"Bra",
	38=>"Underwear",
	39=>"Sweat Shirt",
	40=>"Singlet",
	41=>"Singlet",
	42=>"Boxer",
	43=>"Stripe Boxer",
	44=>"Teens Boxer",
	45=>"Jersy Boxer",
	46=>"Panty",
	47=>"Slip Brief",
	48=>"Classic Brief",
	49=>"Short Brief",
	50=>"Mini Brief",
	51=>"Girls Brief",
	52=>"Girls Brief",
	53=>"Bikers",
	54=>"Underwear",
	55=>"",
	56=>"",
	57=>"",
	58=>"",
	59=>"",
	60=>"Socks",
	61=>"Socks",
	62=>"Socks",
	63=>"Socks",
	64=>"Socks",
	65=>"Socks",
	66=>"Socks",
	67=>"Sweat Pant",
	68=>"",
	69=>"",
	70=>"Long Pant",
	71=>"Long Pant",
	72=>"",
	73=>"Tank Top",
	74=>"Dress",
	75=>"T-Shirt-Long Sleeve",
	76=>"Dress",
	77=>"Underwear",
	78=>"Dress",
	79=>"Bag",
	80=>"Romper",
	81=>"Romper",
	82=>"Romper",
	83=>"Dress",
	84=>"Dress",
	85=>"Dress",
	86=>"Dress",
	87=>"Dress",
	88=>"Dress",
	89=>"Dress",
	90=>"Dress",
	91=>"Jogger",
	92=>"Jogger",
	93=>"Jogger",
	94=>"Jogger",
	95=>"Jegging",
	96=>"Nightware-Top",
	97=>" Shirt",
	98=>"Romper",
	99=>"Inner Top",
	100=>"Outer Top",
	101=>"T-Shirt-Long Sleeve",
	102=>"T-Shirt-Long Sleeve",
	103=>"T-Shirt V Neck",
	104=>"BLANKET",
	105=>"Playsuit",
	106=>"Sweater",
	107=>"Jumper",
	108=>"Cardigan",
	109=>"Nightware-Top",
	110=>"Nightware-Bottom",
	111=>"T-Shirt V Neck",
	112=>"T-Shirt-Short Sleeve",
	113=>"Mens Trunk",
	114=>"Mens Brief",
	115=>"Mens Boxer",
	116=>"Boys Boxer",
	117=>"Boys Brief",
	118=>"Girls Brief",
	119=>"Underwear",
	120=>"Ladies Thong",
	121=>"Mens Trunk",
	122=>"Mens Pattern Brief",
	123=>"Mens Pattern Boxer",
	124=>"T-Shirt-Long Sleeve",
	125=>"T-Shirt-Long Sleeve",
	126=>"T-Shirt-Short Sleeve",
	127=>"Swimwear",
	128=>"Twill Long pant",
	129=>"Twill Short pant",
	130=>"Twill Dress",
	131=>"Twill Shirt",
	132=>"Twill Jacket",
	133=>"Twill Skirt",
	134=>"Twill Boys pant",
	135=>"Twill Over All",
	136=>"Twill Short All",
	137=>"T-Shirt-Short Sleeve",
	138=>"Polo Shirt-Short Sleeve",
	139=>"Denim Long pant",
	140=>"Denim Short pant",
	141=>"Denim Dress",
	142=>"Denim Shirt",
	143=>"Denim Jacket",
	144=>"Denim Skirt",
	145=>"Denim Boys pant",
	146=>"Denim Over All",
	147=>"Denim Short All",
	148=>"Apron",
	149=>"Apron",
	150=>"Quilt Cover",
	151=>"Pillow Cover",
	152=>" Shirt",
	153=>"Apron",
	154=>"Short Pant",
	155=>"Pillow Cover",
	156=>"Over All",
	157=>"Tank Top",
	158=>"Dress",
	159=>"",
	160=>"T-Shirt-Short Sleeve",
	161=>"T-Shirt-Short Sleeve",
	162=>"T-Shirt-Short Sleeve",
	163=>"Tank Top",
	164=>"Tank Top",
	165=>"Tank Top",
	166=>"Short Pant",
	167=>"Short Pant",
	168=>"Short Pant",
	169=>"Short Pant",
	170=>"Short Pant",
	171=>"",
	172=>"",
	173=>"Muffler",
	174=>"Muffler");//HAMS Fashion Ltd.
	
$gItemNameArr4=array(
	1=>"T-Shirt-Long Sleeve",
	2=>"T-Shirt-Short Sleeve",
	3=>"Polo Shirt-Long Sleeve",
	4=>"Polo Shirt-Short Sleeve",
	5=>"Tank Top",
	6=>"T-Shirt 3/4 Sleeve",
	7=>"Hoodie",
	8=>"",
	9=>"T-Shirt-Sleeveless",
	10=>"T-Shirt-Raglan Sleeve",
	11=>"T-Shirt Hi-Neck",
	12=>"Scarf",
	13=>"",
	14=>"Blazer",
	15=>"Jacket",
	16=>"Nightware-Top",
	17=>"Dress",
	18=>"Ladies Long Dress",
	19=>"Girls Dress",
	20=>"Full Pant",
	21=>"Short Pant",
	22=>"Trouser",
	23=>"Payjama",
	24=>"Romper",
	25=>"Romper",
	26=>"Romper",
	27=>"Romper",
	28=>"Legging",
	29=>"Short Pant",
	30=>"Skirts",
	31=>"Jump Suit",
	32=>"Cap",
	33=>"Jump Suit",
	34=>"Jump Suit",
	35=>"Jogger",
	36=>"Bag",
	37=>"Bra",
	38=>"Underwear",
	39=>"Sweat Shirt",
	40=>"Singlet",
	41=>"Singlet",
	42=>"Boxer",
	43=>"Stripe Boxer",
	44=>"Teens Boxer",
	45=>"Jersy Boxer",
	46=>"Panty",
	47=>"Slip Brief",
	48=>"Classic Brief",
	49=>"Short Brief",
	50=>"Mini Brief",
	51=>"Girls Brief",
	52=>"Girls Brief",
	53=>"Bikers",
	54=>"Underwear",
	55=>"",
	56=>"",
	57=>"",
	58=>"",
	59=>"",
	60=>"Socks",
	61=>"Socks",
	62=>"Socks",
	63=>"Socks",
	64=>"Socks",
	65=>"Socks",
	66=>"Socks",
	67=>"Sweat Pant",
	68=>"",
	69=>"",
	70=>"Long Pant",
	71=>"Long Pant",
	72=>"",
	73=>"Tank Top",
	74=>"Dress",
	75=>"T-Shirt-Long Sleeve",
	76=>"",
	77=>"Underwear",
	78=>"Dress",
	79=>"Bag",
	80=>"Romper",
	81=>"Romper",
	82=>"Romper",
	83=>"Dress",
	84=>"Dress",
	85=>"Dress",
	86=>"Dress",
	87=>"Dress",
	88=>"Dress",
	89=>"Dress",
	90=>"Dress",
	91=>"Jogger",
	92=>"Jogger",
	93=>"Jogger",
	94=>"Jogger",
	95=>"Jegging",
	96=>"Nightware-Top",
	97=>" Shirt",
	98=>"Romper",
	99=>"Inner Top",
	100=>"Outer Top",
	101=>"T-Shirt-Long Sleeve",
	102=>"T-Shirt-Long Sleeve",
	103=>"T-Shirt V Neck",
	104=>"BLANKET",
	105=>"Playsuit",
	106=>"Sweater",
	107=>"Jumper",
	108=>"Cardigan",
	109=>"Nightware-Top",
	110=>"Nightware-Bottom",
	111=>"T-Shirt V Neck",
	112=>"T-Shirt-Short Sleeve",
	113=>"Mens Trunk",
	114=>"Mens Brief",
	115=>"Mens Boxer",
	116=>"Boys Boxer",
	117=>"Boys Brief",
	118=>"Girls Brief",
	119=>"Underwear",
	120=>"Ladies Thong",
	121=>"Mens Trunk",
	122=>"Mens Pattern Brief",
	123=>"Mens Pattern Boxer",
	124=>"",
	125=>"",
	126=>"",
	127=>"Swimwear",
	128=>"Twill Long pant",
	129=>"Twill Short pant",
	130=>"Twill Dress",
	131=>"Twill Shirt",
	132=>"Twill Jacket",
	133=>"Twill Skirt",
	134=>"Twill Boys pant",
	135=>"Twill Over All",
	136=>"Twill Short All",
	137=>"T-Shirt-Short Sleeve",
	138=>"",
	139=>"Denim Long pant",
	140=>"Denim Short pant",
	141=>"Denim Dress",
	142=>"Denim Shirt",
	143=>"Denim Jacket",
	144=>"Denim Skirt",
	145=>"Denim Boys pant",
	146=>"Denim Over All",
	147=>"Denim Short All",
	148=>"Apron",
	149=>"Apron",
	150=>"Quilt Cover",
	151=>"Pillow Cover",
	152=>" Shirt",
	153=>"Apron",
	154=>"Short Pant",
	155=>"Pillow Cover",
	156=>"Over All",
	157=>"Tank Top",
	158=>"Dress",
	159=>"",
	160=>"",
	161=>"",
	162=>"",
	163=>"",
	164=>"",
	165=>"",
	166=>"Short Pant",
	167=>"Short Pant",
	168=>"Short Pant",
	169=>"Short Pant",
	170=>"Short Pant",
	171=>"",
	172=>"",
	173=>"Muffler",
	174=>"Muffler");//Victoria Intimates Ltd.
	
if($companyUnitid==1) $fr_product_type=$gItemNameArr1;//HAMS Garments Ltd.
else if($companyUnitid==2) $fr_product_type=$gItemNameArr2;//Dhaka Garments & Washing Ltd.
else if($companyUnitid==3) $fr_product_type=$gItemNameArr3;//HAMS Fashion Ltd.
else if($companyUnitid==4) $fr_product_type=$gItemNameArr4;//Victoria Intimates Ltd.
else $fr_product_type=array();

$locationArr=array(1=>"HGL_T",4=>"HGL_UG",2=>"DGWL",3=>"HGL_T",5=>"VIL");

if ( $action=="save_update_delete" )
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	foreach (glob("frfiles/"."*.*") as $filename)
	{			
		@unlink($filename);
	}
	//echo $received_date;
	header('Content-Type: text/csv; charset=utf-8');
	
	//print_r($line_name); die;
	if ( $cbo_fr_integrtion==0 )
	{
		$color_lib=return_library_array("select id,color_name from lib_color","id","color_name");
		$size_lib=return_library_array("select id,size_name from lib_size","id","size_name");
		$floor_name=return_library_array("select id,floor_name from  lib_prod_floor","id","floor_name");
		$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
		$line_name_res=return_library_array("select id,line_number from prod_resource_mst","id","line_number");
		$floor_name_res=return_library_array("select id,floor_id from prod_resource_mst","id","floor_id");
		$team_leader_arr=return_library_array("select id,team_leader_name from lib_marketing_team","id","team_leader_name");
		$factMarchent_arr=return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
		$user_arr=return_library_array("select id,user_name from user_passwd","id","user_name");
		 
		// Customer File
		//$sql=sql_select ( "select id, buyer_name, short_name from lib_buyer" );
		$sql=sql_select ( "select a.id, a.buyer_name, a.short_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company in ($companyUnitid) and a.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by a.id, a.buyer_name, a.short_name order by buyer_name" );
		$buyer_name_array=array(); $buyerArr=array();
		
		foreach($sql as $name)
		{
			//if(strlen($name)>6) $txt .=$name."\tEND\r\n"; else 
			$buyer_name_array[$name[csf("id")]]=$name[csf("short_name")];
			$buyerArr[$name[csf("id")]]=$name[csf("buyer_name")];
		}
		$txt="";
		if( $db_type==0 )
		{
			if(trim( $received_date)=="") $received_date="2022-01-01"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
		}
		else if($db_type==2)
		{
			if(trim( $received_date)=="") $received_date=date('d-M-Y', strtotime("01-01-2022")); else $received_date=date("d-M-Y",strtotime($received_date));
		}
		// Products file Data
		//echo $received_date; die;
		
		
		if( $db_type==0) $shipment_date=" and c.country_ship_date >= '$received_date'"; else if($db_type==2) $shipment_date=" and c.country_ship_date >= '$received_date'";
		$shiping_status=" and c.shiping_status !=3";
		
		// $shipment_date=" and c.cutup_date >= '".date('d-M-Y', strtotime("01-10-2017"))."'";
		//	echo $shipment_date; die;
		$po_arr=array(); $ft_data_arr=array();
		 
		$sql_po='select a.id as "id", a.company_name as "company_name", a.location_name as "location_name", a.job_no as "job_no", a.style_ref_no as "style_ref_no", a.style_ref_no_prev as "style_ref_no_prev", a.style_description as "style_description", a.gmts_item_id as "gmts_item_id", a.gmts_item_id_prev as "gmts_item_id_prev", a.buyer_name as "buyer_name", a.order_uom as "order_uom", a.team_leader as "team_leader", a.bh_merchant as "bh_merchant", a.product_dept as "product_dept", a.season_buyer_wise as "season", a.total_set_qnty as "total_set_qnty", a.factory_marchant as "factory_marchant", a.dealing_marchant as "dealing_marchant", a.garments_nature as "garments_nature", a.ship_mode as "ship_mode", b.id as "bid", b.po_number as "po_number", b.projected_po_id as "projected_po_id", b.is_confirmed as "is_confirmed", b.po_quantity as "po_quantity", b.original_po_qty as "original_po_qty", b.pub_shipment_date as "pub_shipment_date", b.shipment_date as "shipment_date", b.po_received_date as "po_received_date", b.factory_received_date as "factory_received_date", b.shiping_status as "shiping_status", (b.pub_shipment_date - b.po_received_date) as "leadtime", b.status_active as "status_active", b.po_number_prev as "po_number_prev", b.pub_shipment_date_prev as "pub_shipment_date_prev", c.id as "color_size_break_id", c.item_number_id as "item_number_id", c.country_ship_date as "country_ship_date", c.country_id as "country_id", c.cutup as "cutup", c.color_mst_id as "color_mst_id", c.item_mst_id as "item_mst_id", c.color_number_id as "color_number_id", c.size_number_id as "size_number_id", c.plan_cut_qnty as "plan_cut_qnty", c.order_quantity as "order_quantity", c.order_total as "order_total", c.country_ship_date_prev as "country_ship_date_prev", c.color_number_id_prev as "color_number_id_prev", c.cutup_date as "cutup_date"
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.company_name in ('.$companyUnitid.') and a.id=b.job_id and a.id=c.job_id and b.id=c.po_break_down_id'. $shipment_date.' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and c.is_deleted=0 order by a.id';
		//echo $sql_po; die;
		
		$sql_po_data=sql_select($sql_po); $product_size_arr=array(); $orders_arr=array(); $orders_data_arr=array(); $order_size_arr=array(); $prod_up_arr=array(); $po_arr=array(); $custFileArr=array();
		foreach($sql_po_data as $porow)
		{
			$porow['pub_shipment_date']=$porow['shipment_date'];
			$porow['po_number']=trim($porow['po_number']);
			$porow['po_number_prev']=trim($porow['po_number_prev']);
			
			$custFileArr[$buyer_name_array[$porow['buyer_name']]]=$buyerArr[$porow['buyer_name']];
			
			$job_no=""; $po_number=""; $country_ship_date=""; $item_no_id=''; $color_id=''; $size_id=''; $color_size_break_id="";
			$job_no=$porow['job_no'];
			$po_number=$porow['po_number'];
			$country_ship_date=$porow['country_ship_date'];
			$item_no_id=$porow['item_number_id'];
			$color_id=$porow['color_number_id']; 
			$size_id=$porow['size_number_id'];
			$color_size_break_id=$porow['color_size_break_id'];
			$po_arr[col_po_id][$color_size_break_id]=$color_size_break_id;
			
			if($porow['shiping_status']!=3)
			{
				$po_break[$porow['bid']]=$porow['bid'];
			}
			
			$po_arr[po_id][$porow['bid']]=$porow['bid'];
			$po_arr[job_no][$job_no]="'".$job_no."'";
			$po_arr[job_id][$porow['id']]=$porow['id'];
			
			$ft_data_arr[$job_no][product_dept]=$product_dept[$porow['product_dept']];//P.UDProduct Dept.
			$ft_data_arr[$job_no][job_no]=$job_no;//P.CODE
			$ft_data_arr[$job_no][location]=$locationArr[$porow['location_name']];//P.CODE
			$ft_data_arr[$job_no][gmts_item_id]=$porow['gmts_item_id'];//P.TYPE
			$ft_data_arr[$job_no][style_ref_no]=$porow['style_ref_no'];//P.DESCRIP
			$ft_data_arr[$job_no][style_description]=$porow['style_description'];//P.DESCRIP
			$ft_data_arr[$job_no][buyer_name]=$porow['buyer_name'];
			$ft_data_arr[$job_no][team_leader]=$porow['team_leader'];
			//$ft_data_arr[$job_no][bh_merchant]=$porow['bh_merchant'];
			$ft_data_arr[$job_no][po_quantity]=$porow['po_quantity'];
			$ft_data_arr[$job_no][order_uom]=$porow['order_uom'];
			$ft_data_arr[$job_no][season]=$season_arr[$porow['season']];
			//$ft_data_arr[$job_no][ship_mode]=$shipment_mode[$porow['ship_mode']];
			//$garmentsNature='';
			//if($porow['garments_nature']==2) $garmentsNature='Knit'; else if($porow['garments_nature']==3) $garmentsNature='Woven'; else if($porow['garments_nature']==100) $garmentsNature='Sweater';
			//$ft_data_arr[$job_no][garments_nature]=$garmentsNature;
			//$ft_data_arr[$job_no][user]=$user_arr[$factMarchent_arr[$porow['factory_marchant']]];
			$ft_data_arr[$job_no][team]=$factMarchent_arr[$porow['dealing_marchant']];
			//$ft_data_arr[$job_no][deal_team]=$user_arr[$factMarchent_arr[$porow['dealing_marchant']]];
			$ft_data_arr[$job_no][leadtime]=$porow['leadtime'];
			
			$ft_data_arr[$job_no][old_gmts_item_id]=$porow['gmts_item_id_prev'];//P.TYPE
			$ft_data_arr[$job_no][old_style_ref_no]=$porow['style_ref_no_prev'];//P.DESCRIP
			
			$product_size_arr[$job_no][$size_lib[$size_id]]=$size_lib[$size_id];//P.CODE and size for product size file
			
			$po_str=$porow['bid'].'_'.$porow['pub_shipment_date'].'_'.$porow['is_confirmed'].'_'.$porow['status_active'].'_'.$porow['projected_po_id'].'_'.$porow['factory_received_date'].'_'.$porow['original_po_qty'];
			
			$orders_arr[$job_no][$po_number][$country_ship_date][$item_no_id][$color_id]['plan_cut']+=$porow['plan_cut_qnty'];
			$orders_arr[$job_no][$po_number][$country_ship_date][$item_no_id][$color_id]['order_qty']+=$porow['order_quantity'];
			$orders_arr[$job_no][$po_number][$country_ship_date][$item_no_id][$color_id]['order_value']+=$porow['order_total'];
			$orders_arr[$job_no][$po_number][$country_ship_date][$item_no_id][$color_id]['color_size_break_id'].=trim($color_size_break_id).'**'.$porow['shiping_status'].',';
			$orders_arr[$job_no][$po_number][$country_ship_date][$item_no_id][$color_id]['bid'].=$porow['bid'].",";
			$orders_arr[$job_no][$po_number][$country_ship_date][$item_no_id][$color_id]['order_uom']=$porow['order_uom'];
			$orders_arr[$job_no][$po_number][$country_ship_date][$item_no_id][$color_id]['porec_date']=$porow['po_received_date'];
			$orders_arr[$job_no][$po_number][$country_ship_date][$item_no_id][$color_id]['po_str']=$po_str;
			$orders_arr[$job_no][$po_number][$country_ship_date][$item_no_id][$color_id]['icmstid']=$porow['item_mst_id'].'_'.$porow['color_mst_id'];
			
			$orders_arr[$job_no][$po_number][$country_ship_date][$item_no_id][$color_id]['old_po_number']=$porow['po_number_prev'];
			$orders_arr[$job_no][$po_number][$country_ship_date][$item_no_id][$color_id]['old_color_number']=$porow['color_number_id_prev'];
			$orders_arr[$job_no][$po_number][$country_ship_date][$item_no_id][$color_id]['old_pub_shipdate']=$porow['pub_shipment_date_prev'];
			$orders_arr[$job_no][$po_number][$country_ship_date][$item_no_id][$color_id]['old_country_shipdate']=$porow['country_ship_date_prev'];
			$order_size_array[$job_no][$po_number][$item_no_id][$color_id][$country_ship_date][$size_id]+=$porow['order_quantity'];
			
			$new_color_size[$job_no][$po_number][$country_ship_date][$item_no_id][$color_id][$color_size_break_id]=$color_size_break_id;
			
			$orders_data_arr[$porow['bid']]['po_no']=$po_number;
			$orders_data_arr[$porow['bid']]['season']=$porow['season'];
			
			$order_size_arr[$po_number][$job_no][$item_no_id][$color_id][$size_id]+=$porow['order_quantity'];
			$prod_up_arr[$color_size_break_id]=$size_id;
			$prod_up_arr_powise[$color_size_break_id]=$porow['bid'];
		}
		unset($sql_po_data);
		
		$file_name="frfiles/CUSTOMER_".$comShortName.".TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .=str_pad("C.CODE",0," ")."\t".str_pad("C.DESCRIP",0," ")."\tEND\r\n";
		foreach($custFileArr as $sname=>$fname)
		{
			$txt .=str_pad($sname,0," ")."\t".str_pad($fname,0," ")."\tEND\r\n";
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		
		$txt="";
		
		
		$job_string= implode(",",$po_arr[job_no]);
		
		$jobNoImgCond=where_con_using_array($po_arr[job_no],0,"master_tble_id");
		
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=1");
		oci_commit($con);
		//echo "11";
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1, 1, $po_arr[po_id], $empty_arr);//PO ID
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1, 2, $po_arr[job_id], $empty_arr);//Job ID table name, entry form, id type, data array,
		//fnc_tempengine($table_name, $user_id, $entry_form, $ref_from, $ref_id_arr,  $ref_str_arr)
		
		//echo $user_id; die;
		
		$cmArr=return_library_array("select a.job_no, a.cm_cost from wo_pre_cost_dtls a, gbl_temp_engine b where a.job_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=1 and b.ref_from=2 and a.is_deleted=0 and a.status_active=1","job_no","cm_cost");
		
		$sqlBomDtls='select d.job_no as "job_no", d.sew_effi_percent as "sew_effi_percent", d.costing_per as "costing_per", d.sew_smv as "sew_smv", e.fab_knit_req_kg as "fab_knit_req_kg" from wo_pre_cost_mst d, wo_pre_cost_sum_dtls e, gbl_temp_engine f where d.job_id=e.job_id and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and d.job_id=f.ref_val and e.job_id=f.ref_val and f.user_id = '.$user_id.' and f.entry_form=1 and f.ref_from=2';
		 
		$sqlBomDtlsData=sql_select($sqlBomDtls);
		//print_r($cmArr);
		foreach($sqlBomDtlsData as $bomrow)
		{
			$fab_knit_req_kg=0;
			if($bomrow['costing_per']==1) $fab_knit_req_kg=12;
			else if($bomrow['costing_per']==2) $fab_knit_req_kg=1;
			else if($bomrow['costing_per']==3) $fab_knit_req_kg=24;
			else if($bomrow['costing_per']==4) $fab_knit_req_kg=36;
			else if($bomrow['costing_per']==5) $fab_knit_req_kg=48;
			
			$ft_data_arr[$bomrow['job_no']][cmpcs]=number_format($cmArr[$bomrow['job_no']]/$fab_knit_req_kg, 4, '.', '');
			//echo $cmArr[$bomrow['job_no']].'-'.$fab_knit_req_kg.'<br>';
		}
		//die;
		unset($sqlBomDtlsData);
		unset($cmArr);
		//echo $ft_data_arr['GKD-20-00630'][cmpcs]; die;
		   
		$sql_fabric_prod=sql_select('select a.job_no as "job_no", a.color_type_id as "color_type_id", a.construction as "construction", a.composition as "composition", a.gsm_weight as "gsm_weight" from wo_pre_cost_fabric_cost_dtls a, gbl_temp_engine b where a.job_id=b.ref_val and b.user_id = '.$user_id.' and b.entry_form=1 and b.ref_from=2 and a.avg_cons>0 and a.status_active=1 and a.is_deleted=0 order by a.id asc');
		foreach($sql_fabric_prod as $row_fabric_prod)
		{
			$ft_data_arr[$row_fabric_prod['job_no']][fab_delivery]=1;//P^WC:5 
			
			if($row_fabric_prod['color_type_id']==5) $ft_data_arr[$row_fabric_prod['job_no']][aop]=1;
			else $ft_data_arr[$row_fabric_prod['job_no']][aop]=0;
			
			if($ft_data_arr[$row_fabric_prod['job_no']][fab]=="")
				$ft_data_arr[$row_fabric_prod['job_no']][fab]=$row_fabric_prod['construction'].','.$row_fabric_prod['composition'];
			//else
				//$ft_data_arr[$row_fabric_prod['job_no']][fab].=" | ".$row_fabric_prod['construction'].','.$row_fabric_prod['composition'];
		}
		unset($sql_fabric_prod);
		//print_r($ft_data_arr); die;
		$sql_print_embroid=sql_select('select a.id as "id", a.job_no as "job_no", a.emb_name as "emb_name", a.emb_type as "emb_type" from wo_pre_cost_embe_cost_dtls a, gbl_temp_engine b where a.job_id=b.ref_val and b.user_id = '.$user_id.' and b.entry_form=1 and b.ref_from=2 and a.emb_name in(1,2,3) and a.cons_dzn_gmts>0  and a.status_active=1 and a.is_deleted=0');
		foreach($sql_print_embroid as $row_print_embroid)
		{
			if($row_print_embroid['emb_name']==1)
			{
				$ft_data_arr[$row_print_embroid['job_no']][dv_printing]=1; //P^WC:30
				$ft_data_arr[$row_print_embroid['job_no']][rv_printing]=1; //P^WC:40
				if($ft_data_arr[$row_print_embroid['job_no']][printtype]=="")
					$ft_data_arr[$row_print_embroid['job_no']][printtype]=$emblishment_print_type[$row_print_embroid['emb_type']];
				else
					$ft_data_arr[$row_print_embroid['job_no']][printtype].=" | ".$emblishment_print_type[$row_print_embroid['emb_type']];
			}
			else
			{ 
				$ft_data_arr[$row_print_embroid['job_no']][dv_printing]=0; //P^WC:30
				$ft_data_arr[$row_print_embroid['job_no']][rv_printing]=0; //P^WC:40
			}
			
			if($row_print_embroid['emb_name']==2)
			{
				$ft_data_arr[$row_print_embroid['job_no']][dv_embrodi]=1; //P^WC:50
				$ft_data_arr[$row_print_embroid['job_no']][rv_embrodi]=1; //P^WC:60
				if($ft_data_arr[$row_print_embroid['job_no']][embtype]=="")
					$ft_data_arr[$row_print_embroid['job_no']][embtype]=$emblishment_embroy_type[$row_print_embroid['emb_type']];
				else
					$ft_data_arr[$row_print_embroid['job_no']][embtype].=" | ".$emblishment_embroy_type[$row_print_embroid['emb_type']];
			}
			else 
			{
				$ft_data_arr[$row_print_embroid['job_no']][dv_embrodi]=0; //P^WC:50
				$ft_data_arr[$row_print_embroid['job_no']][rv_embrodi]=0; //P^WC:60
			}
			if($row_print_embroid['emb_name']==3)
			{
				$ft_data_arr[$row_print_embroid['job_no']][dv_wash]=1; //P^WC:90
				$ft_data_arr[$row_print_embroid['job_no']][rv_wash]=1; //P^WC:100
				
				if($ft_data_arr[$row_print_embroid['job_no']][washtype]=="")
					$ft_data_arr[$row_print_embroid['job_no']][washtype]=$emblishment_wash_type[$row_print_embroid['emb_type']];
				else
					$ft_data_arr[$row_print_embroid['job_no']][washtype].=" | ".$emblishment_wash_type[$row_print_embroid['emb_type']];
			}
			else
			{
				$ft_data_arr[$row_print_embroid['job_no']][dv_wash]=0; //P^WC:90
				$ft_data_arr[$row_print_embroid['job_no']][rv_wash]=0; //P^WC:100
			}
		}
		unset($sql_print_embroid);
		//=================================Item wise Array Srart=====================================
		$arr_itemsmv=array();
		$sql_itemsmv=sql_select('select a.job_no as "job_no", a.gmts_item_id as "gmts_item_id", a.set_item_ratio as "set_item_ratio", a.smv_pcs_precost as "smv_pcs_precost", a.smv_set_precost as "smv_set_precost", a.smv_pcs as "smv_pcs", a.embelishment as "embelishment" from wo_po_details_mas_set_details a, gbl_temp_engine b where a.job_id=b.ref_val and b.user_id = '.$user_id.' and b.entry_form=1 and b.ref_from=2 and 1=1 ');
		foreach($sql_itemsmv as $row_itemsmv)
		{
			$arr_itemsmv[$row_itemsmv['job_no']][$row_itemsmv['gmts_item_id']]['smv']=$row_itemsmv['smv_pcs']; 
			$arr_itemsmv[$row_itemsmv['job_no']][$row_itemsmv['gmts_item_id']]['emb']=$row_itemsmv['embelishment'];  
		}
		unset($sql_itemsmv);
		 
		// =================================Item wise Array End=====================================
		//print_r($array_fabric_prod_item); die;
		// Products file
		$txt=""; $myfile =''; $file_name='';
		$file_name="frfiles/PRODUCTS_".$comShortName.".TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
	 	
		//$txt .=str_pad("P.CODE",0," ")."\t".str_pad("P.DESCRIP",0," ")."\t".str_pad("P.TYPE",0," ")."\t".str_pad("P.CUST",0," ")."\t".str_pad("P^WC:20",0," ")."\t".str_pad("P^WC:30",0," ")."\t".str_pad("P^WC:40",0," ")."\t".str_pad("P^WC:50",0," ")."\t".str_pad("P^WC:70",0," ")."\t".str_pad("P^WC:80",0," ")."\t".str_pad("P^WC:90",0," ")."\r\n";
		$txt .=str_pad("P.CODE",0," ")."\t".str_pad("P.OLDCODE",0," ")."\t".str_pad("P.DESCRIP",0," ")."\t".str_pad("P.TYPE",0," ")."\t".str_pad("P.CUST",0," ")."\t".str_pad("P^WC:20",0," ")."\t".str_pad("P^WC:30",0," ")."\t".str_pad("P^WC:40",0," ")."\t".str_pad("P^WC:50",0," ")."\t".str_pad("P^WC:60",0," ")."\t".str_pad("P^WC:70",0," ")."\t".str_pad("P^WC:80",0," ")."\t".str_pad("P^WC:90",0," ")."\t".str_pad("P^WC:100",0," ")."\t".str_pad("P^WC:110",0," ")."\t".str_pad("P^WC:120",0," ")."\t".str_pad("P.UDProduct Dept.",0," ")."\t".str_pad("P.UDSeason",0," ")."\r\n";
		//echo $txt; die;
		
		foreach($ft_data_arr as $rows)
		{
			$item_chaned=0;
			$gitem=explode(",",$rows[gmts_item_id]);
			$old_gitem=explode(",", trim($rows[old_gmts_item_id]));
			$changgid=array_diff($gitem,$old_gitem);
			if(count($changgid)>0) $item_chaned=1;
			if(trim($rows[old_gmts_item_id])=='')$item_chaned=0;
			
			$dt=""; $aop=0; $inc=0;
			$style_ref_arr[$rows[job_no]]=$rows[style_ref_no];
			$job_ref_arr[$rows[job_no]]=$rows[job_no];
			//$job_ref_arr[$rows[job_no]]=$rows[job_no];
			$aop=$ft_data_arr[aop][$rows[job_no]];
			if(count($gitem)>1)
			{
				$item_array[$rows[job_no]]=1;
				foreach( $gitem as $id )
				{
					$emb_req=''; $smv='';
					$smv=$arr_itemsmv[$rows[job_no]][$id]['smv'];
					$emb_req=$arr_itemsmv[$rows[job_no]][$id]['emb'];
					$img_prod[$rows[job_no]][]=$rows[style_ref_no]."::".$fr_product_type[$id]."::".$rows[job_no];
					
					//$ft_data_arr[$porow[csf('job_no')]][old_gmts_item_id]=$porow[csf('gmts_item_id_prev')];//P.TYPE
					//$ft_data_arr[$porow[csf('job_no')]][old_style_ref_no]
					$style_changed=0;
					$old_code='';
					if(trim($rows[old_style_ref_no])!='')
					{
						if(trim($rows[old_style_ref_no])!=trim($rows[style_ref_no]))
						{
							$old_code=$rows[old_style_ref_no]."::".$fr_product_type[$id]."::".$rows[job_no];
							$style_changed=1;
						}
					}
					if($item_chaned==1)
					{
						if($style_changed==1) 
							$old_code=$rows[old_style_ref_no]."::".$fr_product_type[$old_gitem[$inc]]."::".$rows[job_no];
						else
							$old_code=$rows[style_ref_no]."::".$fr_product_type[$old_gitem[$inc]]."::".$rows[job_no];
							
						$new_item_index[$rows[job_no]][$id]=$old_gitem[$inc];
						$ft_data_arr[$rows[job_no]][old_gmts_item_id_chaged]=1;
					}
				 
					$inc++; 
					
					if(trim($rows[job_no])!="" && ($smv*1)>0) $txt .=str_pad($rows[style_ref_no]."::".$fr_product_type[$id]."::".$rows[job_no],0," ")."\t".str_pad($old_code,0," ")."\t".str_pad($rows[style_description],0," ")."\t".str_pad($fr_product_type[$id],0," ")."\t".str_pad($buyer_name_array[$rows[buyer_name]],0," ")."\t".str_pad("1",0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_printing],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][rv_printing],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_embrodi],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($smv,0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_wash],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][rv_wash],0," ")."\t".str_pad("1",0," ")."\t".str_pad("1",0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][product_dept],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][season],0," ")."\r\n";
				}
			}
			else
			{
				$old_code='';
				if(trim($rows[old_style_ref_no])!='')
				{
					if(trim($rows[old_style_ref_no])!=trim($rows[style_ref_no]))
					{
						$old_code=$rows[old_style_ref_no]."::".$fr_product_type[$rows[gmts_item_id]]."::".$rows[job_no];
					}
				}
				$order_uom=$ft_data_arr[$rows[job_no]][order_uom]; $add_item="";
				if($order_uom==58) $add_item="::".$fr_product_type[$rows[gmts_item_id]];
				$smv=$arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'];
				
				$img_prod[$rows[job_no]][]=$rows[style_ref_no].$add_item."::".$rows[job_no];
				
				if(trim($rows[job_no])!="" && ($smv*1)>0) $txt .=str_pad($rows[style_ref_no].$add_item."::".$rows[job_no],0," ")."\t".str_pad($old_code,0," ")."\t".str_pad($rows[style_description],0," ")."\t".str_pad($fr_product_type[$rows[gmts_item_id]],0," ")."\t".str_pad($buyer_name_array[$rows[buyer_name]],0," ")."\t".str_pad("1",0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_printing],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][rv_printing],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_embrodi],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($smv,0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][dv_wash],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][rv_wash],0," ")."\t".str_pad("1",0," ")."\t".str_pad("1",0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][product_dept],0," ")."\t".str_pad($ft_data_arr[$rows[job_no]][season],0," ")."\r\n";
			}
		}
		unset($arr_itemsmv);
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	  	//die;
		
		$file_name="frfiles/ORDERS_".$comShortName.".TXT";
		$myfile = fopen( $file_name, "w" ) or die("Unable to open file!");
		
		$sqlOldcode='select ajob_no as "job_no", po_id as "po_id", color_mst_id as "color_mst_id", item_mst_id as "item_mst_id", shipdate as "shipdate", new_code as "new_code" from fr_temp_code where user_id='.$_SESSION['logic_erp']['user_id'].'';
		$nameArray=sql_select($sqlOldcode); $oldCodeArr=array();
		foreach($nameArray as $orow)
		{
			$oldCodeArr[$orow["job_no"]][$orow["po_id"]][$orow["color_mst_id"]][$orow["item_mst_id"]]=$orow["new_code"];
		}
		unset($nameArray);
		
		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.OLDCODE",0," ")."\t".str_pad("O.PROD",0," ")."\t".str_pad("O.CUST",0," ")."\t".str_pad("O^DD:1",0," ")."\t".str_pad("O^DQ:1",0," ")."\t".str_pad("O.CONTRACT_QTY",0," ")."\t".str_pad("O.STATUS",0," ")."\t".str_pad("O.TIME",0," ")."\t".str_pad("O.SET",0," ")."\t".str_pad("O.SPRICE",0," ")."\t".str_pad("O.MCOST",0," ")."\t".str_pad("O.UDFabric Info.",0," ")."\t".str_pad("O.UDPrint Type",0," ")."\t".str_pad("O.UDEmb Type",0," ")."\t".str_pad("O.UDWash Type",0," ")."\t".str_pad("O.UDMerchandiser",0," ")."\t".str_pad("O.UDPROD. FACILITY",0," ")."\t".str_pad("O.EVBASE",0," ")."\r\n";
		$order_size=array(); $tmpNewCodeArr=array(); $projectedCodeArr=array(); $confirmCodeArr=array(); $cancelDeleteRowArr=array();
		foreach($orders_arr as $jobno=>$po_data)
		{
			foreach($po_data as $po_st=>$ship_item_data)
			{
				foreach($ship_item_data as $ship_date=>$item_data)
				 {
					foreach($item_data as $item_id=>$color_data)
					{
						foreach($color_data as $color_id=>$other_val)
						{
							if($other_val['order_qty']>1)
							{
								$ex_po=explode("_",$other_val['po_str']);
								$po_id=implode(",",array_filter(array_unique(explode(",",$other_val['bid'])))); 
								//echo str_replace("-","",date("Y-m-d",strtotime($ship_date))).'<br>';
								$shipdate=date("d/m/Y",strtotime($ship_date));
								$is_confirm=$ex_po[2];
								$is_deleted=$ex_po[3];
								$projected_po_id=$ex_po[4];
								$projected_po_qty=$ex_po[6];
								if($ex_po[5]!="") $facRecDate=date("d/m/Y",strtotime($ex_po[5]));
								else $facRecDate="";
								
								$col_sizebreak_id_str=""; $shiping_status_str="";
								$ex_other_data=explode(",",$other_val['color_size_break_id']);
								 
								if($is_confirm==1) $str="F"; else $str="P";
								$po_no=$po_st;
								$old_str_po=''; $str_po=''; $changed=0; $ssts="0"; $old_item_code=''; $buyer_style=''; $setIdentifier="";
								if( $other_val['order_uom']!=1 ) 
								{
									if( $ft_data_arr[$jobno][old_gmts_item_id_chaged]==1)
									{
										$changed=1;
										$old_item_code="::".$fr_product_type[$new_item_index[$jobno][$item_id]];
									}
									else
										$old_item_code="::".$fr_product_type[$item_id];
										
									if($fr_product_type[$item_id]!="") $str_item="::".$fr_product_type[$item_id]; else $str_item="";
									$buyer_style=$ft_data_arr[$jobno][style_ref_no];//."::".$ft_data_arr[$jobno][season]."::".$jobno."::".$fr_product_type[$item_id];
									$setIdentifier=$jobno."::".$shipdate;
								}
								else
								{
									$str_item=""; 
									$buyer_style=$ft_data_arr[$jobno][style_ref_no];//."::".$ft_data_arr[$jobno][season]."::".$jobno;
									$setIdentifier="";
								}
								
								if($is_confirm==2) $str_job="::".$jobno; else $str_job="";
								
								$icmstid=""; $oldCode="";
								$job_wise_uom[$jobno]=$other_val['order_uom'];
								$icmstid=$other_val['icmstid'];
								$exicid=explode("_",$icmstid);
								
								$itemMstId=$exicid[0];
								$colorMstId=$exicid[1];
								
								$oldCode=$oldCodeArr[$jobno][$po_id][$colorMstId][$itemMstId];
								
								//$str_po=$po_no."::".$jobno."::".$color_lib[$color_id]."".$str_item."::".date("d-m-Y",strtotime($shipdate));  
								//$str_po=$jobno."::".$po_no."::".$color_lib[$color_id]."::".date("ymd",strtotime($shipdate))."".$str_item; 
								if($is_confirm==1)
								{
									$str_po=$ft_data_arr[$jobno][style_ref_no]."::".$po_no."::".$color_lib[$color_id]."".$str_item."::".str_replace("-","",date("Y-m-d",strtotime($ship_date)))."::".$jobno;
								}
								else
								{
									$str_po=$buyer_name_array[$ft_data_arr[$jobno][buyer_name]]."::".$ft_data_arr[$jobno][style_ref_no]."".$str_item."::".$jobno;
								}
								$pcode=$ft_data_arr[$jobno][style_ref_no]."".$str_item."::".$jobno;
								
								$tmpNewCodeArr[$str_po]=$jobno."__".$po_id."__".$itemMstId."__".$colorMstId."__".$shipdate."__".$oldCode; 
								
								if($is_confirm==2 || $projected_po_qty>0)
								{
									$projectCode=$buyer_name_array[$ft_data_arr[$jobno][buyer_name]]."::".$ft_data_arr[$jobno][style_ref_no]."".$str_item."::".$jobno;
									$projectedCodeArr[$po_id]=$projectCode;
								}
								
								if($is_confirm==1)
								{
									$confirmCodeArr[$str_po]=$po_id."__".$projected_po_id."__".$itemMstId."__".$colorMstId;
								}
								
								/*if( $ft_data_arr[$jobno][old_style_ref_no]!='' && $ft_data_arr[$jobno][old_style_ref_no]!=$ft_data_arr[$jobno][style_ref_no])
								{
									$old_style=$ft_data_arr[$jobno][old_style_ref_no];
									$changed=1;
								}
								else $old_style=$ft_data_arr[$jobno][style_ref_no];
								
								if( $other_val['old_po_number']!='' && $other_val['old_po_number']!=$po_no)
								{
									$old_po= $other_val['old_po_number'];
									$changed=1;
								}
								else $old_po= $po_no;
								
								if( $other_val['old_color_number']!='' && $other_val['old_color_number']!=$color_id)
								{
									$old_color= $other_val['old_color_number'];
									$changed=1;
								}
								else $old_color= $color_id;
								
								if( $other_val['old_pub_shipdate']!='' && date("d/m/Y",strtotime($other_val['old_pub_shipdate']))!=$shipdate)
								{
									$old_pub_ship= date("d/m/Y",strtotime($other_val['old_pub_shipdate']));
									$changed=1;
								}
								else $old_pub_ship=$shipdate;
								
								if($changed==1)
									$old_str_po=$old_style."::".$old_po."::".$color_lib[$old_color]."::".$jobno."::".$old_pub_ship."".$old_item_code; */
									//$old_str_po=$jobno."::".$old_po."::".$color_lib[$old_color]."::".$old_pub_ship."".$old_item_code; 
								
								foreach($new_color_size[$jobno][$po_st][$ship_date][$item_id][$color_id] as $cids)
								{
									$newid_ar[trim($cids)]=$str_po;
								}
								//color_size_break_id
								foreach($ex_other_data as $val_color_size_id)
								{
									$ex_color_size_break_id_val=explode("**",$val_color_size_id);
									$tmp_col_size[$ex_color_size_break_id_val[0]]=$ex_color_size_break_id_val[1];
									if( $ex_color_size_break_id_val[1]==3) $ssts="1"; 
								}
								$tmppoo=implode("",array_unique(explode(",",$po_id)));
								/*if( $is_deleted==2 ||  $is_deleted==3) 
								{ 
									$str="T"; 
									$deleted_po_list[$tmppoo]=$tmppoo; 
								} */ //$ssts=0; 
								
								
								$fob=0; $fob=$other_val['order_value']/$other_val['order_qty'];
								$udbuyer_style_qty=''; $udbuyer_style_qty=$buyer_name_array[$ft_data_arr[$jobno][buyer_name]].'_'.$ft_data_arr[$jobno][style_ref_no].'_'.$other_val['order_qty'];
								$fabricConstCompo=implode(" | ",array_filter(array_unique(explode(" | ",$ft_data_arr[$jobno][fab]))));//implode(",",array_filter(array_unique(explode(",",$ft_data_arr[$jobno][gsm]))));
								
								$cmpcs=0;
								$cmpcs=$ft_data_arr[$jobno][cmpcs];
								$noldCode="";
								if($oldCode!=$str_po) $noldCode=$oldCode;
								if($str=="T")//Delete and Cancel New File
								{
									$cancelDeleteRowArr[$str_po]="X";
								}
								else
								{
									$txt .=str_pad($str_po,0," ")."\t".str_pad($noldCode,0," ")."\t".str_pad($pcode,0," ")."\t".str_pad($buyer_name_array[$ft_data_arr[$jobno][buyer_name]],0," ")."\t".str_pad(' '.$shipdate,0," ")."\t".str_pad($other_val['plan_cut'],0," ")."\t".str_pad($other_val['order_qty'],0," ")."\t".str_pad($str,0," ")."\t".str_pad('',0," ")."\t".str_pad($setIdentifier,0," ")."\t".str_pad(number_format($fob, 4, '.', ''),0," ")."\t".str_pad($cmpcs,0," ")."\t".str_pad($fabricConstCompo,0," ")."\t".str_pad($ft_data_arr[$jobno][printtype],0," ")."\t".str_pad($ft_data_arr[$jobno][embtype],0," ")."\t".str_pad($ft_data_arr[$jobno][washtype],0," ")."\t".str_pad($ft_data_arr[$jobno][team],0," ")."\t".str_pad($ft_data_arr[$jobno][location],0," ")."\t".str_pad(' '.date("d/m/Y",strtotime($other_val['porec_date'])),0," ")."\r\n";
								}
							}
						}
					}
				}
			}
		}
		unset($oldCodeArr);
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		//die;
		
		$con=connect();
		execute_query("delete from fr_temp_code where user_id=".$_SESSION['logic_erp']['user_id']."",0);
		oci_commit($con);
		foreach($tmpNewCodeArr as $ncode=>$str_val)
		{
			$exstrVal=explode("__",$str_val);
			$jobno=$po_id=$itemMstId=$colorMstId=$shipdate=$oldCode="";
			//echo $str_val.'<br>';
			$jobno=$exstrVal[0];
			$po_id=$exstrVal[1];
			$itemMstId=$exstrVal[2];
			$colorMstId=$exstrVal[3];
			$shipdate=date("d-M-Y",strtotime($exstrVal[4]));
			$oldCode=$exstrVal[5];
			//echo "insert into fr_temp_code (job_no, po_id, color_mst_id, item_mst_id, shipdate, new_code, old_code, user_id) values ('".$jobno."','".$po_id."','".$colorMstId."','".$itemMstId."','".$shipdate."','".$ncode."','".$oldCode."',".$_SESSION['logic_erp']['user_id'].")<br>";
			execute_query("insert into fr_temp_code (job_no, po_id, color_mst_id, item_mst_id, shipdate, new_code, old_code, user_id) values ('".$jobno."','".$po_id."','".$colorMstId."','".$itemMstId."','".$shipdate."','".$ncode."','".$oldCode."',".$_SESSION['logic_erp']['user_id'].")");
		}
		oci_commit($con);
		disconnect($con);
		
		$file_name="frfiles/RELATE_".$comShortName.".TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");

		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.HOST_ORDER",0," ")."\t".str_pad("END",0," ")."\r\n";
		foreach($confirmCodeArr as $cstr=>$condate)
		{
			$excdata=explode("__",$condate);
			$po_id=$proj_po_id=$itemMstId=$colorMstId="";
			$po_id=$excdata[0];
			$proj_po_id=$excdata[1];
			$itemMstId=$excdata[2];
			$colorMstId=$excdata[3];
			
			$projCode="";
			
			$projCode=$projectedCodeArr[$po_id];
			
			if($cstr!="" && $projCode!="")
				$txt .=str_pad($cstr,0," ")."\t".str_pad($projCode,0," ")."\t END\r\n";
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		
		
		/*$file_name="frfiles/ORDUPDAT_Cancel.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");

		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.STATUS",0," ")."\r\n";
		foreach($cancelDeleteRowArr as $cdcode=>$val)
		{
			if($cdcode!="")
				$txt .=str_pad($cdcode,0," ")."\t".str_pad($val,0," ")."\r\n";
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";*/
		
		//die;
		
		// Production file
		$prod_sql='SELECT a.po_break_down_id as "po_break_down_id", a.country_id as "country_id", a.item_number_id as "item_number_id", a.floor_id as "floor_id", a.sewing_line as "sewing_line", a.production_type as "production_type", a.production_source as "production_source", a.production_date as "production_date", b.color_size_break_down_id as "color_size_break_down_id", b.production_qnty AS "production_quantity", a.embel_name as "embel_name", a.status_active as "status_active", a.is_deleted as "is_deleted" from pro_garments_production_mst a, pro_garments_production_dtls b, gbl_temp_engine c where a.production_type in (1,2,3,4,5,11) and a.id=b.mst_id and a.po_break_down_id=c.ref_val and c.user_id = '.$user_id.' and c.entry_form=1 and c.ref_from=1 order by production_date, b.color_size_break_down_id asc'; //$poIds_cond_prod tmp_poid (userid, poid)  and a.embel_name in (1,2,3,4,5)  and a.is_deleted=0 and a.status_active=1  and c.userid='$user_id'
		//echo $prod_sql; die;
	//	print_r($deleted_po_list);
		$prod_sql_res=sql_select($prod_sql);
		foreach($prod_sql_res as $row_sew)
		{
			if($deleted_po_list[$row_sew["po_break_down_id"]]!='')
				$deleted_colors[$row_sew["color_size_break_down_id"]]=$row_sew["color_size_break_down_id"];
			
			if($newid_ar[$row_sew["color_size_break_down_id"]]!='' && $prod_up_arr_powise[$row_sew['color_size_break_down_id']]==$row_sew["po_break_down_id"] && $deleted_po_list[$row_sew["po_break_down_id"]]=='')
			{
				if($row_sew["production_type"]==3)//issue
				{
					if($row_sew["embel_name"]==1) $row_sew["production_type"]=9;
					else if($row_sew["embel_name"]==2) $row_sew["production_type"]=11;
					else if( $row_sew["embel_name"]==3 ) $row_sew["production_type"]=12;
				}
				else if($row_sew["production_type"]==2) // send emb
				{
					if($row_sew["embel_name"]==1) $row_sew["production_type"]=10;
					else if($row_sew["embel_name"]==2) $row_sew["production_type"]=17;
					else if( $row_sew["embel_name"]==3 ) $row_sew["production_type"]=18;
				}
				if($row_sew[csf("production_type")]==11) $row_sew["production_type"]=7;
				
				if( $row_sew["status_active"]==0 && $row_sew["is_deleted"]==1 ) $row_sew["production_quantity"]=0; 
				
				$row_sew["sewing_line"]=(int)$line_name_res[$row_sew["sewing_line"]]; 
				
				
				$prod_typ=$row_sew["production_type"];
				if( $prod_typ!=5 ) $row_sew["sewing_line"]=0;
				//if( $prod_typ==8 ||$prod_typ==1 ||$prod_typ==9 ||$prod_typ==10 ||$prod_typ==4 ) $row_sew["sewing_line"]=0;
				$exfdate=$row_sew["production_date"];
				/*$lineName="";
				if($row_sew["sewing_line"]!="") 
				{
					$exLine=explode(",",$row_sew["sewing_line"]);
					foreach($exLine as $lid)
					{
						if($lineName=="") $lineName=$line_name[$lid]; else $lineName.=','.$line_name[$lid];
					}
				}*/
				if($row_sew["production_type"]==4)
				{
					if($row_sew["production_source"]==1)
					{
						$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['qnty']+=$row_sew["production_quantity"];
						$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['pdate']=$exfdate; 
						$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['col_size_id']=$row_sew["color_size_break_down_id"]; 
					}
				}
				else if($row_sew["production_type"]==5)
				{
					if($row_sew["production_source"]==1)
					{
						$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['qnty']+=$row_sew["production_quantity"];
						$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['pdate']=$exfdate; 
						$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['col_size_id']=$row_sew["color_size_break_down_id"]; 
					}
				}
				else 
				{
					$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['qnty']+=$row_sew["production_quantity"];
					$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['pdate']=$exfdate; 
					$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['col_size_id']=$row_sew["color_size_break_down_id"];
				}
			}
		}
		unset($prod_sql_res);
		/*echo"<pre>";
		print_r($production_qty);*/ //die;
	
		$prod_sql='SELECT a.mst_id as "mst_id", a.color_size_break_down_id as "color_size_break_down_id", a.production_qnty as "production_qnty", b.ex_factory_date as "ex_factory_date", b.shiping_status as "shiping_status" from pro_ex_factory_dtls a, pro_ex_factory_mst b, gbl_temp_engine c where b.id=a.mst_id and a.is_deleted=0 and a.status_active=1 and a.mst_id=b.id and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id=c.ref_val and c.user_id = '.$user_id.' and c.entry_form=1 and c.ref_from=1';// and c.userid='$user_id'
		//echo $prod_sql; die;
		$prod_sql_res=sql_select($prod_sql); $exFactoryArr=array();
		foreach($prod_sql_res as $row_sew)
		{
			if($newid_ar[$row_sew["color_size_break_down_id"]]!='' && $deleted_colors[$row_sew["color_size_break_down_id"]]=='') 
			{
				$row_sew["production_type"]=13;
				$row_sew["sewing_line"]=0;
				
				if($row_sew["shiping_status"]==3)
				{
					$exFactoryArr[$newid_ar[$row_sew["color_size_break_down_id"]]]['exdate']=$row_sew["ex_factory_date"];
					$exFactoryArr[$newid_ar[$row_sew["color_size_break_down_id"]]]['ostatus']=$row_sew["ex_factory_date"];
				}
				
				$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['qnty']+=$row_sew["production_qnty"];
				$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['pdate']=$row_sew["ex_factory_date"]; 
				$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['col_size_id']=$row_sew["color_size_break_down_id"]; 
			}
		}
		unset($prod_sql_res);
		$file_name="frfiles/UPDNORM_".$comShortName.".TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");

		$txt .=str_pad("U.ORDER",0," ")."\t".str_pad("U.DATE",0," ")."\t".str_pad("U.OPERATION",0," ")."\t".str_pad("U.LINE_EXTERNAL_ID",0," ")."\t".str_pad("U.QTY",0," ")."\t".str_pad("U.OPN_COMPLETE",0," ")."\r\n";
		foreach($production_qty as $oid=>$prodctn)
		{
			foreach($prodctn as $prod_typ=>$prodflr)
			{
				foreach($prodflr as $line=>$sdata)
				{
					//if($prod_typ==5) { print_r($sdata); echo $line."="; }
					$prod_type_n=$gmt_prod_id_map[$prod_typ]*1;
					
					$sdate=date("d/m/Y",strtotime($sdata['pdate']));
					if($line==0) $line=""; else $line=$line_name[$line];//(int)$line_name_res[$line]; 
					$flor=$floor_name_res[$line];
					if($flor==0) $flor=""; 
					if($line==0) $line="";
					//$size_data=$size_lib[$prod_up_arr[$sdata[col_size_id]]];
					if( $prod_type_n==12 || $prod_type_n==4 || $prod_type_n==5 || $prod_type_n==6 || $prod_type_n==8 ) $line="";
					if( $prod_type_n==6 ||$prod_type_n==5 ) $size_data="";
					$usect='';
					if($prod_type_n==9) $usect="SEW";
					
					if(  $prod_type_n>0)
						$txt .=str_pad($oid,0," ")."\t ".str_pad($sdate,0," ")."\t".str_pad($prod_type_n,0," ")."\t".str_pad($line,0," ")."\t".str_pad($sdata['qnty'],0," ")."\t".str_pad('',0," ")."\r\n";
				}
			}	
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		
		$file_name="frfiles/ORDUPDAT_".$comShortName.".TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");

		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.STATUS",0," ")."\t".str_pad("O.COMPLETE",0," ")."\t".str_pad("O.UDACTUAL SHIPMENT",0," ")."\r\n";
		foreach($exFactoryArr as $ocode=>$exdate)
		{
			if($ocode!="")
				$txt .=str_pad($ocode,0," ")."\t".str_pad($cancelDeleteRowArr[$ocode],0," ")."\t 1 \t ".str_pad(date("d/m/Y",strtotime($exdate['exdate'])),0," ")."\r\n";
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=1");
		oci_commit($con);
		disconnect($con);
		// print_r($production_qty); die;
		
		$sql=sql_select("select id as ID, master_tble_id as MASTER_TBLE_ID, image_location as IMAGE_LOCATION from common_photo_library where is_deleted=0 and file_type=1 and form_name='knit_order_entry' $jobNoImgCond ");
		//echo "select id as ID, master_tble_id as MASTER_TBLE_ID, image_location as IMAGE_LOCATION from common_photo_library where is_deleted=0 and file_type=1 and form_name='knit_order_entry' $jobNoImgCond";
		
		$file_name="frfiles/IMGATTACH_".$comShortName.".TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .="IMG.CODE\tIMG.FILENAME\tIMG.NAME\tIMG.FILEPATH\tIMG.DEFAULT\r\n";
		
		$zipimg = new ZipArchive();			// Load zip library	
		$filenames = str_replace(".sql",".zip",'frfiles/ImgFolders.sql');			// Zip name
		if($zipimg->open($filenames, ZIPARCHIVE::CREATE)!==TRUE)
		{		// Opening zip file to load files
			$error .=  "* Sorry ZIP creation failed at this time<br/>"; echo $error;
		}
		
		if($companyUnitid==1) $imlocc="Z:\\";//HAMS Garments Ltd.
		else if($companyUnitid==2) $imlocc="Y:\dgwlimage";//Dhaka Garments & Washing Ltd.
		else if($companyUnitid==3) $imlocc="Y:\hflimage";//HAMS Fashion Ltd.
		else if($companyUnitid==4) $imlocc="Y:\vilimage";//Victoria Intimates Ltd.
		else $imlocc="";
		
		foreach($sql as $rows)
		{
			$name=explode("/",$rows[csf("IMAGE_LOCATION")]);
			foreach( $img_prod[$rows[csf("MASTER_TBLE_ID")]] as $job  )
			{
				
				$txt .=$job."\t".$name[1]."\t".str_replace(".jpg","",$name[1])."\t".$imlocc."\t1\r\n";
			}
			 //echo "0**../../../".$rows[csf("image_location")]; 
			$zipimg->addFile("../../../".$rows[csf("IMAGE_LOCATION")]);
			
			/*$name=explode("/",$rows["IMAGE_LOCATION"]);
			foreach( $img_prod[$rows["MASTER_TBLE_ID"]] as $job  )
			{
				$exfile=explode(".",$name[1]);
				if($exfile[1]=='jpg' || $exfile[1]=='JPG' || $exfile[1]=='png' || $exfile[1]=='PNG' || $exfile[1]=='BMP' || $exfile[1]=='bmp')
				{
					$txt .=$job."\t".$name[1]."\t".str_replace(".jpg","",$name[1])."\t".$imlocc."\t1\r\n";
				}
			}
			$extfile=explode(".",$rows["IMAGE_LOCATION"]);
			if($extfile[1]=='jpg' || $extfile[1]=='JPG' || $extfile[1]=='png' || $extfile[1]=='PNG' || $extfile[1]=='BMP' || $extfile[1]=='bmp')
			{
				//echo "0**../../../".$rows["IMAGE_LOCATION"]; 
				$zipimg->addFile("../../../".$rows["IMAGE_LOCATION"]);
			}*/
		}
		$zipimg->close();
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	}
	else if ( $cbo_fr_integrtion==1 )
	{
		$sql=sql_select ( "select id,short_name from lib_buyer" );
		$file_name="frfiles/CUSTOMER.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .=str_pad("C.CODE",15," ")."\tEND\r\n";
		foreach($sql as $name)
		{
			//if(strlen($name)>6) $txt .=$name."\tEND\r\n"; else 
			$txt .=str_pad($name[csf("short_name")],15," ")."\tEND\r\n";
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	}
	else if ( $cbo_fr_integrtion==2 )
	{
		if($db_type==0) $shipment_date="and c.country_ship_date >= '2019-07-01'"; else if($db_type==2) $shipment_date="and c.country_ship_date >= '01-Jul-2019'";
		
		$shiping_status="and c.shiping_status !=3";
		
		$po_arr=array();
		$ft_data_arr=array();
		
		$sql_po="select a.id,a.job_no,a.style_ref_no,a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and b.shipment_date >= '2014-11-01' and b.is_confirmed=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.id,b.id";
		$sql_po_data=sql_select($sql_po);
		foreach($sql_po_data as $sql_po_row)
		{
			$ft_data_arr[$sql_po_row[csf('job_no')]][job_no]=$sql_po_row[csf('job_no')];//P.CODE
			$ft_data_arr[$sql_po_row[csf('job_no')]][gmts_item_id]=$sql_po_row[csf('gmts_item_id')];//P.TYPE
			$ft_data_arr[$sql_po_row[csf('job_no')]][style_ref_no]=$sql_po_row[csf('style_ref_no')];//P.DESCRIP
		}
		
		unset($sql_po_data);
		
		$sql_po="select a.id,a.job_no,a.style_ref_no,a.gmts_item_id,d.costing_per,d.sew_smv,e.fab_knit_req_kg   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_mst d, wo_pre_cost_sum_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no  and b.id=c.po_break_down_id $shipment_date   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by a.id,b.id"; //and b.is_confirmed=1  
		$sql_po_data=sql_select($sql_po);
		foreach($sql_po_data as $sql_po_row)
		{
		$po_arr[po_id][$sql_po_row[csf('id')]]=$sql_po_row[csf('id')];
		$po_arr[job_no][$sql_po_row[csf('job_no')]]="'".$sql_po_row[csf('job_no')]."'";
		
		$ft_data_arr[$sql_po_row[csf('job_no')]][job_no]=$sql_po_row[csf('job_no')];//P.CODE
		$ft_data_arr[$sql_po_row[csf('job_no')]][gmts_item_id]=$sql_po_row[csf('gmts_item_id')];//P.TYPE
		$ft_data_arr[$sql_po_row[csf('job_no')]][style_ref_no]=$sql_po_row[csf('style_ref_no')];//P.DESCRIP
		$fab_knit_req_kg=0;
		if($sql_po_row[csf('costing_per')]==1)
		{
			$fab_knit_req_kg=$sql_po_row[csf('fab_knit_req_kg')]/12;
		}
		if($sql_po_row[csf('costing_per')]==2)
		{
			$fab_knit_req_kg=$sql_po_row[csf('fab_knit_req_kg')];
		}
		if($sql_po_row[csf('costing_per')]==3)
		{
			$fab_knit_req_kg=$sql_po_row[csf('fab_knit_req_kg')]/24;
		}
		if($sql_po_row[csf('costing_per')]==4)
		{
			$fab_knit_req_kg=$sql_po_row[csf('fab_knit_req_kg')]/36;
		}
		if($sql_po_row[csf('costing_per')]==5)
		{
			$fab_knit_req_kg=$sql_po_row[csf('fab_knit_req_kg')]/48;
		}
		$ft_data_arr[$sql_po_row[csf('job_no')]][fab_knit_req_kg]=number_format($fab_knit_req_kg,3);//P^CF:5
		$ft_data_arr[$sql_po_row[csf('job_no')]][cutting]=1;//P^WC:10
		
		
		
		$ft_data_arr[$sql_po_row[csf('job_no')]][sew_input]=1;//P^WC:35
		$ft_data_arr[$sql_po_row[csf('job_no')]][sew_output]=$sql_po_row[csf('sew_smv')];//P^WC:140
		
		$ft_data_arr[$sql_po_row[csf('job_no')]][poly]=1;//P^WC:160
		$ft_data_arr[$sql_po_row[csf('job_no')]][shiped]=$sql_po_row[csf('sew_smv')];//P^WC:165
	
		}
		
		
		$po_string= implode(",",$po_arr[po_id]);
		$job_string= implode(",",$po_arr[job_no]);
		
	   $job=array_chunk($po_arr[job_no],1000, true);
	   $job_cond_in="";
	   $ji=0;
	   foreach($job as $key=> $value)
	   {
		   if($ji==0)
		   {
			$job_cond_in="job_no in(".implode(",",$value).")"; 
		   }
		   else
		   {
				$job_cond_in.=" or job_no in(".implode(",",$value).")"; 
		   }
		   $ji++;
	   }
	   
	   
	   $sql_fabric_prod=sql_select("select min(id) as id,job_no  from wo_pre_cost_fabric_cost_dtls where $job_cond_in and fabric_source=1 and avg_cons>0  and status_active=1 and is_deleted=0 group by job_no");
	   foreach($sql_fabric_prod as $row_fabric_prod)
	   {
		$ft_data_arr[fab_delivery][$row_fabric_prod[csf('job_no')]]=1;//P^WC:5   
	   }
	  $sql_print_embroid=sql_select("select min(id) as id,job_no,emb_name,avg(cons_dzn_gmts) as cons_dzn_gmts  from wo_pre_cost_embe_cost_dtls where $job_cond_in and emb_name in(1,2,3) and cons_dzn_gmts>0  and status_active=1 and is_deleted=0 group by job_no,emb_name");
	  foreach($sql_print_embroid as $row_print_embroid)
	  {
		  if($row_print_embroid[csf('emb_name')]==1)
		  {
			$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_printing]=1; //P^WC:15
			$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_printing]=1; //P^WC:20
		  }
		  if($row_print_embroid[csf('emb_name')]==2)
		  {
			$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_embrodi]=1; //P^WC:25
			$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_embrodi]=1; //P^WC:30
		  }
		  if($row_print_embroid[csf('emb_name')]==3)
		  {
			$ft_data_arr[$row_print_embroid[csf('job_no')]][dv_wash]=1; //P^WC:145
			$ft_data_arr[$row_print_embroid[csf('job_no')]][rv_wash]=1; //P^WC:150
		  }
	  }
		//fputcsv($output, array( $rows[job_no],$rows[gmts_item_id],$rows[style_ref_no],$rows[fab_knit_req_kg],$rows[cutting] == '' ? 0 : $rows[cutting],$rows[dv_printing] == '' ? 0 : $rows[dv_printing],$rows[rv_printing] == '' ? 0 : $rows[rv_printing],$rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],$rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],$rows[sew_input] == '' ? 0 :$rows[sew_input],$rows[sew_output] == '' ? 0 :$rows[sew_output],$rows[dv_wash] == '' ? 0 :$rows[dv_wash],$rows[rv_wash] == '' ? 0 :$rows[rv_wash],$rows[poly] == '' ? 0 :$rows[poly],$rows[shiped] == '' ? 0 :$rows[shiped],'','','',''));
		 
		$txt="";
		$file_name="frfiles/PRODUCTS.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .=str_pad("P.CODE",15," ")."\t".str_pad("P.TYPE",15," ")."\t".str_pad("P.DESCRIP",25," ")."\t".str_pad("P^CF:5",12," ")."\t".str_pad("P^WC:5",12," ")."\t".str_pad("P^WC:10",12," ")."\t".str_pad("P^WC:15",12," ")."\t".str_pad("P^WC:20",12," ")."\t".str_pad("P^WC:25",12," ")."\t".str_pad("P^WC:30",12," ")."\t".str_pad("P^WC:35",12," ")."\t".str_pad("P^WC:140",12," ")."\t".str_pad("P^WC:145",12," ")."\t".str_pad("P^WC:150",12," ")."\t".str_pad("P^WC:160",12," ")."\t".str_pad("P^WC:165",12," ")."\t".str_pad("P.UDFabrication",12," ")."\t".str_pad("P.UDGSM",12," ")."\t".str_pad("P.UDYarn Count",12," ")."\t".str_pad("P.UDStyle",12," ")."\r\n";

		foreach($ft_data_arr as $rows)
		{
			$txt .=str_pad($rows[job_no],15," ")."\t".str_pad($rows[gmts_item_id],15," ")."\t".str_pad($rows[style_ref_no],25," ")."\t".str_pad($rows[fab_knit_req_kg],12," ")."\t".str_pad($rows[cutting] == '' ? 0 : $rows[cutting],12," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],12," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],12," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],12," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],12," ")."\t".str_pad($rows[sew_input] == '' ? 0 :$rows[sew_input],12," ")."\t".str_pad($rows[sew_output] == '' ? 0 :$rows[sew_output],12," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],12," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],12," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],12," ")."\t".str_pad($rows[shiped] == '' ? 0 :$rows[shiped],12," ")."\t\t\t\t\t\r\n";
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	}
	else if ( $cbo_fr_integrtion==4 )
	{
		
	}
	else if( $cbo_fr_integrtion==7 )
	{
		$sql=sql_select ( "select id,master_tble_id,image_location from common_photo_library where is_deleted=0 and file_type=1 and form_name='knit_order_entry' and ((image_location like '%.jpg') or (image_location like '%.JPG') or (image_location like '%.PNG') or (image_location like '%.png') or (image_location like '%.BMP') or (image_location like '%.bmp'))" ); // and master_tble_id in ('".implode("','",$job_ref_arr)."')
		$file_name="frfiles/IMGATTACH.txt";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .="IMG.CODE\tIMG.FILENAME\tIMG.NAME\tIMG.FILEPATH\tIMG.DEFAULT\r\n";
		
		$zipimg = new ZipArchive();			// Load zip library	
		$filenamess = str_replace(".sql",".zip",'frfiles/ImgFolders.sql');			// Zip name
		if($zipimg->open($filenamess, ZIPARCHIVE::CREATE)!==TRUE)
		{		// Opening zip file to load files
			$error .=  "* Sorry ZIP creation failed at this time<br/>"; echo $error; 
		}
		 
		foreach($sql as $rows)
		{
			/*$exfile=explode(".",$rows[csf("image_location")]);
			if($exfile[1]=='.jpg' || $exfile[1]=='.JPG' || $exfile[1]=='.png' || $exfile[1]=='.PNG' || $exfile[1]=='.BMP' || $exfile[1]=='.bmp')
			{*/
				$name=explode("/",$rows[csf("image_location")]);
				foreach( $img_prod[$rows[csf("master_tble_id")]] as $job  )
				{
					$txt .=$job."\t".$name[1]."\t".str_replace(".jpg","",$name[1])."\t".$name[1]."\t1\r\n";
				}
				$zipimg->addFile("../../../".$rows[csf("image_location")]);
			//}
		}
		$zipimg->close();
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
 
	}
	
	$zip = new ZipArchive();			// Load zip library	
	$filename = str_replace(".sql",".zip",'frfiles/fr_files.sql');			// Zip name
	if($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE)
	{		// Opening zip file to load files
		$error .=  "* Sorry ZIP creation failed at this time<br/>"; echo $error;
	} 
	
	foreach (glob("frfiles/"."*.*") as $filenames)
	{			
	   $zip->addFile($filenames);		
	}
	//	$zip->addFile("frfiles/ImgFolders.zip");
	$zip->close();
	echo "0**d";
	disconnect($con);
	exit();
}


/*
select sum(b.document_currency) as tot_document_currency 
from com_export_proceed_realization a, 2815
com_export_proceed_rlzn_dtls b, 
com_export_doc_submission_invo c 
where a.id=b.mst_id and a.invoice_bill_id=c.doc_submission_mst_id -- and a.benificiary_id like '1' 
--and a.is_invoice_bill=1 --and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
and b.insert_date between '02-Mar-2015' and '03-Mar-2015' 
--Group By b.document_currency
Order by 1

select a.buyer_id,c.is_lc,c.lc_sc_id,b.type,sum(b.document_currency) as tot_document_currency 
from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c 
where a.id=b.mst_id and a.invoice_bill_id=c.doc_submission_mst_id and a.benificiary_id like '1' and a.is_invoice_bill=1 
and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
and b.insert_date between '02-Mar-2015' and '03-Mar-2015' group by a.buyer_id,c.is_lc,c.lc_sc_id,b.type


select sum(nvl(b.document_currency,0)) as tot_document_currency 
from com_export_proceed_rlzn_dtls b
where b.insert_date between '02-Mar-2015' and '03-Mar-2015' 
--group by a.buyer_id --,c.is_lc,c.lc_sc_id,b.type,a.insert_date,a.update_date,b.insert_date,b.update_date
Order by 1
*/
?>