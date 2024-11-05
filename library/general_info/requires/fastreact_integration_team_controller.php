<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
echo "0**";

$fr_product_type=return_library_array("select id,item_name from lib_garment_item where status_active=1 and is_deleted=0 order by item_name","id","item_name");
//$production_process=array(1=>"Cutting",2=>"Knitting",3=>"Dyeing",4=>"Finishing",5=>"Sewing",6=>"Fabric Printing",7=>"Washing",8=>"Gmts Printing",9=>"Embroidery",10=>"Iron",11=>"Gmts Finishing");

$production_process_freact=array(1=>"Yarn",2=>"Knitting",3=>"AOP",4=>"Cutting",5=>"Printing Send",6=>"Printing Received",7=>"Embroidery Send",8=>"Sweing In",9=>"Sewing Out",10=>"Wash Send",11=>"Iron Output",12=>"Gmts Finishing",13=>"Ex-Factory",14=>"Dyeing",17=>"Embroidery Received",18=>"Wash Received");


$gmt_prod_id_map[1]=10; //cut
//$gmt_prod_id_map[2]=6;
//$gmt_prod_id_map[3]=5;
$gmt_prod_id_map[4]=60; //Sewing Input
$gmt_prod_id_map[5]=70; //Sewing Output
$gmt_prod_id_map[8]=100; //packing & Finishing
//$gmt_prod_id_map[8]=12;
$gmt_prod_id_map[9]=20;  //Print Send
$gmt_prod_id_map[10]=30; //Print Rec
$gmt_prod_id_map[11]=40; //Emb Send
$gmt_prod_id_map[12]=80; //Wash Send
$gmt_prod_id_map[17]=50; //Emb Rec
$gmt_prod_id_map[18]=90; //Wash Rec
$gmt_prod_id_map[13]=110; //Ship

$erplinenamearr=return_library_array("select id,line_name from lib_sewing_line","id","line_name");
$line_name=array( 
	1 => 405, //GT L01
 	2 => 410, //GT L02
    3 => 415, //GT L03
    4 => 420, //GT L04
    5 => 425, //GT L05
    6 => 430, //GT L06
    7 => 435, //GT L07
    8 => 440, //GT L08
    9 => 445, //GT L09
    10 => 450, //GT L10
	11 => 455, //GT L11
 	12 => 460, //GT L12
    13 => 465, //GT L13
    14 => 470, //GT L14
    15 => 475, //GT L15
    16 => 480, //GT L16
    17 => 485, //GT L17
    18 => 490, //GT L18
    19 => 495, //GT L19
    20 => 500, //GT L20
	21 => 505, //GT L21
 	22 => 510, //GT L22
   	23 => 515, //GT L23
    24 => 520, //GT L24
    25 => 525, //GT L25
    26 => 530, //GT L26
    27 => 535, //GT L27
    28 => 540, //GT L28
    29 => 545, //GT L29
    30 => 550, //GT L30
	31 => 555, //GT L31
 	32 => 560, //GT L32
    33 => 565, //GT L33
    34 => 570, //GT L34
    35 => 575, //GT L35
    36 => 580, //GT L36
    37 => 0, //1
    38 => 900, //MS L01
    39 => 905, //MS L02
    40 => 910, //MS L03
	41 => 915, //MS L04
 	42 => 920, //MS L05
    43 => 925, //MS L06
    44 => 930, //MS L07
    45 => 935, //MS L08
    46 => 940, //MS L09
    47 => 945, //MS L10
    48 => 950, //MS L11
    49 => 955, //MS L12
    50 => 900, //MS L01
	51 => 905, //MS L02
 	52 => 910, //MS L03
    53 => 915, //MS L04
    54 => 920, //MS L05
    55 => 925, //MS L06
    56 => 930, //MS L07
    57 => 935, //MS L08
    58 => 940, //MS L09
    59 => 945, //MS L10
    60 => 950, //MS L11
	61 => 955, //MS L12
 	62 => 705, //BR L01
    63 => 710, //BR L02
    64 => 715, //BR L03
    65 => 720, //BR L04
    66 => 725, //BR L05
    67 => 730, //BR L06
    68 => 735, //BR L07
    69 => 740, //BR L08
    70 => 745, //BR L09
	71 => 750, //BR L10
 	72 => 755, //BR L11
    73 => 760, //BR L12
    74 => 765, //BR L13
    75 => 770, //BR L14
    76 => 775, //BR L15
	
    77 => 1005, //Line-A
    78 => 1010, //Line-B
    79 => 1015, //Line-C
    80 => 1020, //Line-D
	81 => 1025, //Line-E
 	82 => 1030, //Line-F
    83 => 1035, //Line-G
    84 => 1040, //Line-H
	221=>1045,//CB L-I
	
    85 => 0, //4A1 GF A
    86 => 0, //4A1 GF B
    87 => 0, //4A1 GF C
    88 => 0, //4A1 GF D
	
    89 => 30, //4A-1-01
    90 => 32, //4A-1-02
	91 => 34, //4A-1-03
 	92 => 36, //4A-1-04
    93 => 0, //4A1 1STF E
    94 => 0, //4A1 1STF F
    95 => 0, //4A1 1STF G
    96 => 0, //4A1 1STF H
    97 => 12, //4A-1-05
    98 => 16, //4A-1-06
    99 => 38, //4A-1-07
    100 => 40, //4A-1-08
	101 => 0, //4A1 3RDF A
 	102 => 0, //4A1 3RDF B
    103 => 0, //4A1 3RDF C
    104 => 0, //4A1 3RDF D
    105 => 0, //4A1 3RDF E
    106 => 0, //4A1 3RDF F
    107 => 0, //4A1 3RDF G
    108 => 0, //4A1 3RDF H
    109 => 0, //4A2 3RDF A
    110 => 0, //4A2 3RDF B
	111 => 0, //4A2 3RDF C
 	112 => 0, //4A2 3RDF D
    113 => 0, //4A2 3RDF E
    114 => 0, //4A2 3RDF F
    115 => 0, //4A2 3RDF G
    116 => 0, //4A2 3RDF H
    117 => 0, //4A2 3RDF I
    118 => 0, //4A2 3RDF J
    119 => 0, //4A2 3RDF K
    120 => 0, //4A2 3RDF L
	121 => 0, //4A2 3RDF M
 	122 => 0, //4A2 3RDF N
   	123 => 0, //4A2 3RDF O
    124 => 0, //4A2 3RDF P
    125 => 0, //4A2 3RDF Q
    126 => 0, //4A2 3RDF R
    127 => 14, //4A-1-17
    128 => 20, //4A-1-18
    129 => 100, //4A-1-19--
    130 => 105, //4A-1-20
	131 => 110, //4A-1-21
 	132 => 115, //4A-1-22
    133 => 120, //4A-1-23
    134 => 125, //4A-1-24
    135 => 0, //Sew 1
    136 => 0, //1
    137 => 0, //1
    138 => 155, //4A-2-15
    139 => 0, //1
    140 => 48, //4A-1-09
	141 => 50, //4A-1-10
 	142 => 10, //4A-1-11
    143 => 18, //4A-1-12
    144 => 58, //4A-1-13
    145 => 56, //4A-1-14
    146 => 54, //4A-1-15
    147 => 52, //4A-1-16
    148 => 130, //4A-2-01
    149 => 132, //4A-2-02
    150 => 134, //4A-2-03
	151 => 136, //4A-2-04
 	152 => 138, //4A-2-05
    153 => 140, //4A-2-06
    154 => 142, //4A-2-07
    155 => 144, //4A-2-08
    156 => 146, //4A-2-09
    157 => 148, //4A-2-10
    158 => 150, //4A-2-11
    159 => 152, //4A-2-12
    160 => 155, //4A-2-13
	161 => 158, //4A-2-14
 	162 => 160, //4A-2-15
    163 => 161, //4A-2-16
    164 => 170, //4A-2-17
    165 => 176, //4A-2-18
    166 => 178, //4A-2-19
    167 => 180, //4A-2-20
    168 => 185, //4A-2-21
    169 => 194, //4A-2-22
    170 => 196, //4A-2-23
	171 => 198, //4A-2-24
 	172 => 200, //4A-2-25
    173 => 206, //4A-2-26
    174 => 208, //4A-2-27
    175 => 210, //4A-2-28
    176 => 212, //4A-2-29
    177 => 216, //4A-2-30
    178 => 218, //4A-2-31
    179 => 220, //4A-2-32
    180 => 260, //4A-2-33
	181 => 266, //4A-2-34
 	182 => 268, //4A-2-35
    183 => 280, //4A-2-36
    184 => 282, //4A-2-37
    185 => 284, //4A-2-38
    186 => 286, //4A-2-39
    187 => 288, //4A-2-40
    188 => 290, //4A-2-41
    189 => 292, //4A-2-42
    190 => 295, //4A-2-43
	191 => 300, //4A-2-44
 	192 => 305, //4A-2-45
    193 => 310, //4A-2-46
    194 => 315, //4A-2-47
    195 => 320, //4A-2-48
    196 => 360, //4A-2-49
    197 => 362, //4A-2-50
    198 => 363, //4A-2-51
    199 => 365, //4A-2-52
    200 => 367, //4A-2-53
	201 => 370, //4A-2-54
 	202 => 372, //4A-2-55
    203 => 274, //4A-2-56
    204 => 0, //SE
    205 => 0, //SE
	206 => 376, //4A-2-57
	207 => 377, //4A-2-58
	208 => 378, //4A-2-59
	209 => 380, //4A-2-60
	210 => 381, //4A-2-61
	211 => 382, //4A-2-62
	212 => 383, //4A-2-63
	213 => 385, //4A-2-64
	214 => 386, //4A-2-65
	215 => 387, //4A-2-66
	216 => 585, //GT Pilot
	217 => 0, //Sample 1+2+3
	218 => 0, //Sample 4+5+6
	219 => 0, //Sample 7+8+9
	220 => 0, //1
	222 => 780 //BR L16
	//ERP LIB LINE=>FR LINE /ERP LINE NAME
);
$companyArr=return_library_array("select id,company_name from lib_company","id","company_name");

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
	if ( $cbo_fr_integrtion==0 )
	{
		$color_lib=return_library_array("select id,color_name from lib_color","id","color_name");
		$size_lib=return_library_array("select id,size_name from lib_size","id","size_name");
		$floor_name=return_library_array("select id,floor_name from  lib_prod_floor","id","floor_name");
		$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
		$line_name_res=return_library_array("select id,line_number from prod_resource_mst","id","line_number");
		$floor_name_res=return_library_array("select id,floor_id from prod_resource_mst","id","floor_id");
		$team_leader_arr=return_library_array("select id,team_leader_name from lib_marketing_team","id","team_leader_name");
		$factMarchent_arr=return_library_array("select id, user_tag_id from lib_mkt_team_member_info","id","user_tag_id");
		$user_arr=return_library_array("select id,user_name from user_passwd","id","user_name");

//        $company_wise_tna_task = [1 => [64,310,301], 2 => [306,277,310,301], 3 => [306,277,310,301], 4=> [306,277,310,301], 5 => [306,277,310,301], 6=>[46,47,70,71]];
        $company_wise_tna_task_type = [1 => 1, 2 => 6, 3 => 6, 4=> 6, 5 => 6, 6=>3];
		// Customer File
		$sql=sql_select ( "select id, buyer_name, short_name from lib_buyer" );
		$file_name="frfiles/CUSTOMER.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .=str_pad("C.CODE",0," ")."\tEND\r\n";
		foreach($sql as $name)
		{
			//if(strlen($name)>6) $txt .=$name."\tEND\r\n"; else 
			$buyer_name_array[$name[csf("id")]]=$name[csf("short_name")];
			$txt .=str_pad($name[csf("short_name")],0," ")."\tEND\r\n";
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		
		$txt="";
		if( $db_type==0 )
		{
			if(trim( $received_date)=="") $received_date="2021-07-01"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
		}
		else if($db_type==2)
		{
			if(trim( $received_date)=="") $received_date=date('d-M-Y', strtotime("01-07-2021")); else $received_date=date("d-M-Y",strtotime($received_date));
		}
		// Products file Data
		//echo $received_date; die;
		
		
		if( $db_type==0) $shipment_date1=" and b.po_received_date >= '$received_date' "; else if($db_type==2) $shipment_date=" and b.po_received_date >= '$received_date' ";
//		$shiping_status=" and b.shiping_status !=3";

		 $shipment_date=" and b.pub_shipment_date >= '01-Jan-2023' ";
		//	echo $shipment_date; die;
		$po_arr=array(); $ft_data_arr=array(); $temp_order_status_arr = []; $temp_id_wise_status = [];
		 
		$sql_po='select a.id as "id", a.company_name as "company_name", a.job_no as "job_no", a.style_ref_no as "style_ref_no", a.style_ref_no_prev as "style_ref_no_prev", a.style_description as "style_description", a.gmts_item_id as "gmts_item_id", a.gmts_item_id_prev as "gmts_item_id_prev", a.buyer_name as "buyer_name", a.order_uom as "order_uom", a.team_leader as "team_leader", a.bh_merchant as "bh_merchant", a.agent_name as "agent_name", a.season_buyer_wise as "season", a.total_set_qnty as "total_set_qnty", a.factory_marchant as "factory_marchant", a.dealing_marchant as "dealing_marchant", a.garments_nature as "garments_nature", a.ship_mode as "ship_mode", b.id as "bid", b.po_number as "po_number", b.projected_po_id as "projected_po_id", b.is_confirmed as "is_confirmed", b.po_quantity as "po_quantity", b.pub_shipment_date as "pub_shipment_date", b.shipment_date as "shipment_date", b.po_received_date as "po_received_date", b.factory_received_date as "factory_received_date", b.shiping_status as "shiping_status", (b.pub_shipment_date - b.po_received_date) as "leadtime", b.status_active as "status_active", c.status_active as "color_status_active", b.po_number_prev as "po_number_prev", b.pub_shipment_date_prev as "pub_shipment_date_prev", c.id as "color_size_break_id", c.item_number_id as "item_number_id", c.country_ship_date as "country_ship_date", c.country_id as "country_id", c.cutup as "cutup", c.color_mst_id as "color_mst_id", c.item_mst_id as "item_mst_id", c.color_number_id as "color_number_id", c.size_number_id as "size_number_id", c.plan_cut_qnty as "plan_cut_qnty", c.order_quantity as "order_quantity", c.order_total as "order_total", c.country_ship_date_prev as "country_ship_date_prev", c.color_number_id_prev as "color_number_id_prev", c.cutup_date as "cutup_date"
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.company_name <> 6 '.$shipment_date.$shipment_date1.' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 order by a.id';
		//echo $sql_po; die; //.$shiping_status
		$sql_po_data=sql_select($sql_po); $product_size_arr=array(); $po_qty_arr = []; $temp_check_arr = []; $po_arr_comlete = array(); $orders_arr=array(); $orders_data_arr=array(); $ft_data_arr1 = array(); $order_size_arr=array(); $prod_up_arr=array();
		foreach($sql_po_data as $porow)
		{
//			$porow['pub_shipment_date']=$porow['shipment_date'];
            if($porow['color_status_active'] == 0 || $porow['color_status_active'] == 3 || $porow['color_status_active'] == 2){
                $porow['plan_cut_qnty'] = 0;
                $porow['order_quantity'] = 0;
                $porow['order_total'] = 0;
            }
            if($porow['order_quantity'] == 0){
                $porow['color_status_active'] = 0;
            }

			$porow['po_number']=trim($porow['po_number']);
			$porow['po_number_prev']=trim($porow['po_number_prev']);
			
			$job_no=""; $po_number=""; $pub_ship_date=""; $item_no_id=''; $color_id=''; $size_id=''; $color_size_break_id="";
			$job_no=$porow['job_no'];
			$po_number=$porow['po_number'];
			$pub_ship_date=$porow['pub_shipment_date'];
			$item_no_id=$porow['item_number_id'];
			$color_id=$porow['color_number_id']; 
			$size_id=$porow['size_number_id'];
			$color_size_break_id=$porow['color_size_break_id'];
			$po_arr[col_po_id][$color_size_break_id]=$color_size_break_id;
			$po_arr[po_id][$porow['bid']]=$porow['bid'];
            $po_qty_arr[$porow['bid']]=$porow['po_quantity'];
			$po_arr[job_id][$porow['id']]=$porow['id'];
			$po_arr[job_no][$job_no]="'".$job_no."'";

            $temp_id_wise_status[$color_size_break_id] = $porow['color_status_active'];


            if($porow['po_quantity'] == 0){
                $porow['status_active'] = 0;
            }

            $order_stattus = $porow['status_active'];

            if($order_stattus == 2)
                $order_stattus = 3;

            if($order_stattus == 0)
                $order_stattus = 3;

            $temp_order_status_arr[$job_no][$order_stattus] = $order_stattus;

			if($porow['shiping_status']!=3)
			{
				$po_break[$porow['bid']]=$porow['bid'];
			}
			$ft_data_arr[$job_no][agent_name]=$buyer_name_array[$porow['agent_name']];//P.CODE
			$ft_data_arr[$job_no][job_no]=$job_no;//P.CODE
			$ft_data_arr[$job_no][company]=$companyArr[$porow['company_name']];//P.CODE
            $ft_data_arr[$job_no][company_id]=$porow['company_name'];//P.CODE
			$ft_data_arr[$job_no][gmts_item_id]=$porow['gmts_item_id'];//P.TYPE
			$ft_data_arr[$job_no][style_ref_no]=$porow['style_ref_no'];//P.DESCRIP
			$ft_data_arr[$job_no][style_description]=$porow['style_description'];//P.DESCRIP
			$ft_data_arr[$job_no][buyer_name]=$porow['buyer_name'];
			$ft_data_arr[$job_no][team_leader]=$porow['team_leader'];
			$ft_data_arr[$job_no][bh_merchant]=$porow['bh_merchant'];
			$ft_data_arr[$job_no][po_quantity]=$porow['po_quantity'];
			$ft_data_arr[$job_no][order_uom]=$porow['order_uom'];
			$ft_data_arr[$job_no][season]=$season_arr[$porow['season']];
			$ft_data_arr[$job_no][ship_mode]=$shipment_mode[$porow['ship_mode']];


			$garmentsNature='';
			if($porow['garments_nature']==2) $garmentsNature='Knit'; else if($porow['garments_nature']==3) $garmentsNature='Woven'; else if($porow['garments_nature']==100) $garmentsNature='Sweater';
			$ft_data_arr[$job_no][garments_nature]=$garmentsNature;
			$ft_data_arr[$job_no][user]=$user_arr[$factMarchent_arr[$porow['factory_marchant']]];
			$ft_data_arr[$job_no][team]=$team_leader_arr[$porow['team_leader']];
			$ft_data_arr[$job_no][deal_team]=$user_arr[$factMarchent_arr[$porow['dealing_marchant']]];
			$ft_data_arr[$job_no][leadtime]=$porow['leadtime'];
			
			$ft_data_arr[$job_no][old_gmts_item_id]=$porow['gmts_item_id_prev'];//P.TYPE
			$ft_data_arr[$job_no][old_style_ref_no]=$porow['style_ref_no_prev'];//P.DESCRIP
			
			$ft_data_arr[$job_no][cutting]=1;//P^WC:10
			$ft_data_arr[$job_no][sew_input]=1;//P^WC:35
			$ft_data_arr[$job_no][poly]=1;//P^WC:160

			
			$product_size_arr[$job_no][$size_lib[$size_id]]=$size_lib[$size_id];//P.CODE and size for product size file
			
			$po_str=$porow['bid'].'_'.$pub_ship_date.'_'.$porow['is_confirmed'].'_'.$porow['status_active'].'_'.$porow['projected_po_id'].'_'.$porow['factory_received_date'].'_'.$porow['color_status_active'];

			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['plan_cut']+=$porow['plan_cut_qnty'];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['company'] = $porow['company_name'];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['order_qty']+=$porow['order_quantity'];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['order_value']+=$porow['order_total'];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['color_size_break_id'].=trim($color_size_break_id).'**'.$porow['shiping_status'].',';
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['bid'].=$porow['bid'].",";
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['order_uom']=$porow['order_uom'];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['porec_date']=$porow['po_received_date'];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['po_str']=$po_str;

            $temp_check_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id][$porow['color_status_active']]= $color_id;

			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['icmstid']=$porow['item_mst_id'].'_'.$porow['color_mst_id'];
			
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['old_po_number']=$porow['po_number_prev'];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['old_color_number']=$porow['color_number_id_prev'];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['old_pub_shipdate']=$porow['pub_shipment_date_prev'];
			$orders_arr[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id]['old_country_shipdate']=$porow['country_ship_date_prev'];
			$order_size_array[$job_no][$po_number][$item_no_id][$color_id][$pub_ship_date][$size_id]+=$porow['order_quantity'];
			
			$new_color_size[$job_no][$po_number][$pub_ship_date][$item_no_id][$color_id][$color_size_break_id]=$color_size_break_id;
			
			$orders_data_arr[$porow['bid']]['po_no']=$po_number;
			$orders_data_arr[$porow['bid']]['season']=$porow['season'];
			
			$order_size_arr[$po_number][$job_no][$item_no_id][$color_id][$size_id]+=$porow['order_quantity'];
			$prod_up_arr[$color_size_break_id]=$size_id;
			$prod_up_arr_powise[$color_size_break_id]=$porow['bid'];
		}
		unset($sql_po_data);
		//print_r($orders_arr); die;
		
		$po_string= implode(",",$po_arr[po_id]);
		$job_string= implode(",",$po_arr[job_no]);
		
		$jobNoImgCond=where_con_using_array($po_arr[job_no],0,"master_tble_id");
		
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=1");
		oci_commit($con);
		//echo "11";
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1, 1, $po_arr[po_id]);//PO ID
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1, 2, $po_arr[job_id]);//Job ID table name, User, entry form, id type, data array,
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1, 3, $po_arr[col_po_id]);//Color Size Break Down ID table name, User, entry form, id type, data array,

		$cmArr=return_library_array("select a.job_no, a.cm_cost from wo_pre_cost_dtls a, gbl_temp_engine b where a.job_id=b.ref_val and b.user_id = ".$user_id." and b.entry_form=1 and b.ref_from=2 and a.is_deleted=0 and a.status_active=1","job_no","cm_cost");
		//echo $user_id; die;
		$sqlBomDtls='select d.job_no as "job_no", d.sew_effi_percent as "sew_effi_percent", d.costing_per as "costing_per", d.sew_smv as "sew_smv", e.fab_knit_req_kg as "fab_knit_req_kg" from wo_pre_cost_mst d, wo_pre_cost_sum_dtls e, gbl_temp_engine f where d.job_no=e.job_no and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and d.job_id=f.ref_val and f.user_id = '.$user_id.' and f.entry_form=1 and f.ref_from=2';
		 
		// $product_size_arr=array(); $orders_arr=array(); $orders_data_arr=array(); $order_size_arr=array(); $prod_up_arr=array();
		$sqlBomDtlsData=sql_select($sqlBomDtls);
		//print_r($sqlBomDtlsData);
		foreach($sqlBomDtlsData as $bomrow)
		{
			$fab_knit_req_kg=0;
			if($bomrow['costing_per']==1) $fab_knit_req_kg=12;
			else if($bomrow['costing_per']==2) $fab_knit_req_kg=1;
			else if($bomrow['costing_per']==3) $fab_knit_req_kg=24;
			else if($bomrow['costing_per']==4) $fab_knit_req_kg=36;
			else if($bomrow['costing_per']==5) $fab_knit_req_kg=48;
			
			$ft_data_arr[$bomrow['job_no']][fab_knit_req_kg]=number_format($bomrow['fab_knit_req_kg']/$fab_knit_req_kg,3);//P^CF:5
			$ft_data_arr[$bomrow['job_no']][shiped]=$bomrow['sew_smv'];//P^WC:165
			$ft_data_arr[$bomrow['job_no']][sew_output]=$bomrow['sew_smv'];//P^WC:140
			$ft_data_arr[$bomrow['job_no']][sew_eff]=$bomrow['sew_effi_percent'];
			
			$ft_data_arr[$bomrow['job_no']][cmpcs]=$cmArr[$bomrow['job_no']]/$fab_knit_req_kg;
		}
		unset($sqlBomDtlsData);
		//echo $ft_data_arr['GKD-20-00630'][cmpcs]; die;
		   
		$sql_fabric_prod=sql_select('select a.job_no as "job_no", a.color_type_id as "color_type_id", a.gsm_weight as "gsm_weight" from wo_pre_cost_fabric_cost_dtls a, gbl_temp_engine b where a.fabric_source=1 and a.avg_cons>0 and a.status_active=1 and a.is_deleted=0 and a.job_id=b.ref_val and b.user_id = '.$user_id.' and b.entry_form=1 and b.ref_from=2');
		
		foreach($sql_fabric_prod as $row_fabric_prod)
		{
			$ft_data_arr[fab_delivery][$row_fabric_prod['job_no']]=1;//P^WC:5 
			if($row_fabric_prod['color_type_id']==5) $ft_data_arr[aop][$row_fabric_prod['job_no']]=1;
			else $ft_data_arr[aop][$row_fabric_prod['job_no']]=0;
			
			$ft_data_arr[$row_fabric_prod['job_no']][gsm]=$row_fabric_prod['gsm_weight'].',';
		}
		unset($sql_fabric_prod);
		
		$sql_print_embroid=sql_select('select min(a.id) as "id", a.job_no as "job_no", a.emb_name as "emb_name", avg(a.cons_dzn_gmts) as "cons_dzn_gmts" from wo_pre_cost_embe_cost_dtls a, gbl_temp_engine b where a.emb_name in(1,2,3) and a.cons_dzn_gmts>0 and a.status_active=1 and a.is_deleted=0 and a.job_id=b.ref_val and b.user_id = '.$user_id.' and b.entry_form=1 and b.ref_from=2 group by a.job_no, a.emb_name');
		foreach($sql_print_embroid as $row_print_embroid)
		{
			if($row_print_embroid['emb_name']==1)
			{
				$ft_data_arr[$row_print_embroid['job_no']][dv_printing]=1; //P^WC:20
				$ft_data_arr[$row_print_embroid['job_no']][rv_printing]=1; //P^WC:30
			}
			else
			{ 
				$ft_data_arr[$row_print_embroid['job_no']][dv_printing]=0; //P^WC:20
				$ft_data_arr[$row_print_embroid['job_no']][rv_printing]=0; //P^WC:30
			}
			if($row_print_embroid['emb_name']==2)
			{
				$ft_data_arr[$row_print_embroid['job_no']][dv_embrodi]=1; //P^WC:40
				$ft_data_arr[$row_print_embroid['job_no']][rv_embrodi]=1; //P^WC:50
			}
			else 
			{
				$ft_data_arr[$row_print_embroid['job_no']][dv_embrodi]=0; //P^WC:40
				$ft_data_arr[$row_print_embroid['job_no']][rv_embrodi]=0; //P^WC:50
			}
			if($row_print_embroid['emb_name']==3)
			{
				$ft_data_arr[$row_print_embroid['job_no']][dv_wash]=1; //P^WC:80
				$ft_data_arr[$row_print_embroid['job_no']][rv_wash]=1; //P^WC:90
			}
			else
			{
				$ft_data_arr[$row_print_embroid['job_no']][dv_wash]=0; //P^WC:80
				$ft_data_arr[$row_print_embroid['job_no']][rv_wash]=0; //P^WC:90
			}
		}
		
		unset($sql_print_embroid);
		//=================================Item wise Array Srart=====================================
		$arr_itemsmv=array();
		$sql_itemsmv=sql_select('select a.job_no as "job_no", a.gmts_item_id as "gmts_item_id", a.set_item_ratio as "set_item_ratio", a.smv_pcs_precost as "smv_pcs_precost", a.smv_set_precost as "smv_set_precost", a.smv_pcs as "smv_pcs", a.embelishment as "embelishment" from wo_po_details_mas_set_details a, gbl_temp_engine b where a.job_id=b.ref_val and b.user_id = '.$user_id.' and b.entry_form=1 and b.ref_from=2');
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
		$file_name="frfiles/PRODUCTS.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
	 	
		//$txt .=str_pad("P.CODE",0," ")."\t".str_pad("P.DESCRIP",0," ")."\t".str_pad("P.TYPE",0," ")."\t".str_pad("P.CUST",0," ")."\t".str_pad("P^WC:20",0," ")."\t".str_pad("P^WC:30",0," ")."\t".str_pad("P^WC:40",0," ")."\t".str_pad("P^WC:50",0," ")."\t".str_pad("P^WC:70",0," ")."\t".str_pad("P^WC:80",0," ")."\t".str_pad("P^WC:90",0," ")."\r\n";
		
		$txt .=str_pad("P.CODE",0," ")."\t".str_pad("P.DESCRIP",0," ")."\t".str_pad("P.TYPE",0," ")."\t".str_pad("P.CUST",0," ")."\t".str_pad("P^WC:10",0," ")."\t".str_pad("P^WC:20",0," ")."\t".str_pad("P^WC:30",0," ")."\t".str_pad("P^WC:40",0," ")."\t".str_pad("P^WC:50",0," ")."\t".str_pad("P^WC:60",0," ")."\t".str_pad("P^WC:70",0," ")."\t".str_pad("P^WC:80",0," ")."\t".str_pad("P^WC:90",0," ")."\t".str_pad("P^WC:100",0," ")."\t".str_pad("P^WC:110",0," ")."\r\n";
		
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

            $order_status = array_values($temp_order_status_arr[$rows[job_no]]);

            $status_product = 1;
            if(count($order_status) == 1 && $order_status[0] == 3)
                $status_product = 0;

            if($status_product == 1) {
                if (count($gitem) > 1) {
                    $item_array[$rows[job_no]] = 1;
                    foreach ($gitem as $id) {
                        $emb_req = '';
                        $smv = '';
                        $smv = $arr_itemsmv[$rows[job_no]][$id]['smv'];
                        $emb_req = $arr_itemsmv[$rows[job_no]][$id]['emb'];
                        $img_prod[$rows[job_no]][] = $rows[style_ref_no] . "::" . $rows[season] . "::" . $rows[job_no] . "::" . $fr_product_type[$id];

                        //$ft_data_arr[$porow[csf('job_no')]][old_gmts_item_id]=$porow[csf('gmts_item_id_prev')];//P.TYPE
                        //$ft_data_arr[$porow[csf('job_no')]][old_style_ref_no]
                        $style_changed = 0;
                        $old_code = '';
                        if (trim($rows[old_style_ref_no]) != '') {
                            if (trim($rows[old_style_ref_no]) != trim($rows[style_ref_no])) {
                                $old_code = $rows[old_style_ref_no] . "::" . $rows[season] . "::" . $rows[job_no] . "::" . $fr_product_type[$id];
                                $style_changed = 1;
                            }
                        }
                        if ($item_chaned == 1) {
                            if ($style_changed == 1)
                                $old_code = $rows[old_style_ref_no] . "::" . $rows[season] . "::" . $rows[job_no] . "::" . $fr_product_type[$old_gitem[$inc]];
                            else
                                $old_code = $rows[style_ref_no] . "::" . $rows[season] . "::" . $rows[job_no] . "::" . $fr_product_type[$old_gitem[$inc]];

                            $new_item_index[$rows[job_no]][$id] = $old_gitem[$inc];
                            $ft_data_arr[$rows[job_no]][old_gmts_item_id_chaged] = 1;
                        }

                        $inc++;

                        if (trim($rows[job_no]) != "" && ($smv * 1) > 0) $txt .= str_pad($rows[style_ref_no] . "::" . $rows[season] . "::" . $fr_product_type[$id], 0, " ") . "\t" . str_pad($rows[style_description], 0, " ") . "\t" . str_pad($fr_product_type[$id], 0, " ") . "\t" . str_pad($buyer_name_array[$rows[buyer_name]], 0, " ") . "\t" . str_pad("1", 0, " ") . "\t" . str_pad($ft_data_arr[$rows[job_no]][dv_printing] == '' ? 0 : $ft_data_arr[$rows[job_no]][dv_printing], 0, " ") . "\t" . str_pad($ft_data_arr[$rows[job_no]][rv_printing] == '' ? 0 : $ft_data_arr[$rows[job_no]][rv_printing], 0, " ") . "\t" . str_pad($ft_data_arr[$rows[job_no]][dv_embrodi] == '' ? 0 : $ft_data_arr[$rows[job_no]][dv_embrodi], 0, " ") . "\t" . str_pad($ft_data_arr[$rows[job_no]][rv_embrodi] == '' ? 0 : $ft_data_arr[$rows[job_no]][rv_embrodi], 0, " ") . "\t" . str_pad("1", 0, " ") . "\t" . str_pad($smv, 0, " ") . "\t" . str_pad($ft_data_arr[$rows[job_no]][dv_wash] == '' ? 0 : $ft_data_arr[$rows[job_no]][dv_wash], 0, " ") . "\t" . str_pad($ft_data_arr[$rows[job_no]][rv_wash] == '' ? 0 : $ft_data_arr[$rows[job_no]][rv_wash], 0, " ") . "\t" . str_pad("1", 0, " ") . "\t" . str_pad("1", 0, " ") . "\r\n";
                    }
                }
                else {
                    $old_code = '';
                    if (trim($rows[old_style_ref_no]) != '') {
                        if (trim($rows[old_style_ref_no]) != trim($rows[style_ref_no])) {
                            $old_code = $rows[old_style_ref_no] . "::" . $rows[season] . "::" . $rows[job_no];
                        }
                    }
                    $order_uom = $ft_data_arr[$rows[job_no]][order_uom];
                    $add_item = "";
                    if ($order_uom == 58) $add_item = "::" . $fr_product_type[$rows[gmts_item_id]];
                    $smv = $arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'];
                    $img_prod[$rows[job_no]][] = $rows[style_ref_no] . "::" . $rows[season];

                    if (trim($rows[job_no]) != "" && ($smv * 1) > 0) $txt .= str_pad($rows[style_ref_no] . "::" . $rows[season] . $add_item, 0, " ") . "\t" . str_pad($rows[style_description], 0, " ") . "\t" . str_pad($fr_product_type[$rows[gmts_item_id]], 0, " ") . "\t" . str_pad($buyer_name_array[$rows[buyer_name]], 0, " ") . "\t" . str_pad("1", 0, " ") . "\t" . str_pad($ft_data_arr[$rows[job_no]][dv_printing] == '' ? 0 : $ft_data_arr[$rows[job_no]][dv_printing], 0, " ") . "\t" . str_pad($ft_data_arr[$rows[job_no]][rv_printing] == '' ? 0 : $ft_data_arr[$rows[job_no]][rv_printing], 0, " ") . "\t" . str_pad($ft_data_arr[$rows[job_no]][dv_embrodi] == '' ? 0 : $ft_data_arr[$rows[job_no]][dv_embrodi], 0, " ") . "\t" . str_pad($ft_data_arr[$rows[job_no]][rv_embrodi] == '' ? 0 : $ft_data_arr[$rows[job_no]][rv_embrodi], 0, " ") . "\t" . str_pad("1", 0, " ") . "\t" . str_pad($smv, 0, " ") . "\t" . str_pad($ft_data_arr[$rows[job_no]][dv_wash] == '' ? 0 : $ft_data_arr[$rows[job_no]][dv_wash], 0, " ") . "\t" . str_pad($ft_data_arr[$rows[job_no]][rv_wash] == '' ? 0 : $ft_data_arr[$rows[job_no]][rv_wash], 0, " ") . "\t" . str_pad("1", 0, " ") . "\t" . str_pad("1", 0, " ") . "\r\n";
                }
            }
		}

		fwrite($myfile, $txt);
		fclose($myfile);
		$txt="";
	  	//die;

        $sql_tna = sql_select("select a.task_finish_date, a.actual_finish_date, a.po_number_id, a.task_type, a.task_number from tna_process_mst a, gbl_temp_engine c where a.task_number in (64,277,306,310,301,12) and a.po_number_id = c.ref_val and c.user_id = $user_id and c.entry_form=1 and c.ref_from=1 and a.status_active = 1
            and a.is_deleted = 0 and a.task_finish_date is not null order by a.task_finish_date");

        $tna_task_arr = []; $tna_task_acc_in_arr = []; $tna_task_fab_in_arr = []; $tna_task_pp_approval_arr = [];
        foreach ($sql_tna as $data) {
            if($data[csf('task_number')] == 64) {
                $tna_task_arr[$data[csf('po_number_id')]][$data[csf('task_type')]] = $data[csf('task_finish_date')];
                $tna_task_fab_in_arr[$data[csf('po_number_id')]][$data[csf('task_type')]][64] = strtotime($data[csf('actual_finish_date')]);
            }
            if($data[csf('task_number')] == 277 || $data[csf('task_number')] == 306) {
                $tna_task_arr[$data[csf('po_number_id')]][$data[csf('task_type')]] = $data[csf('task_finish_date')];
                $tna_task_fab_in_arr[$data[csf('po_number_id')]][$data[csf('task_type')]][$data[csf('task_number')]] = strtotime($data[csf('actual_finish_date')]);
            }
            if($data[csf('task_number')] == 301 || $data[csf('task_number')] == 310) {
                $tna_task_acc_in_arr[$data[csf('po_number_id')]][$data[csf('task_type')]][$data[csf('task_number')]] = strtotime($data[csf('actual_finish_date')]);
            }
            if($data[csf('task_number')] == 12) {
                $tna_task_pp_approval_arr[$data[csf('po_number_id')]][$data[csf('task_type')]][12] = strtotime($data[csf('actual_finish_date')]);
            }
        }
		
		$file_name="frfiles/ORDERS.TXT";
		$myfile = fopen( $file_name, "w" ) or die("Unable to open file!");

        $get_pre_cost_emb_wash = sql_select("select a.job_no, nvl(a.embel_cost, 0) as embel_cost, nvl(a.wash_cost, 0) as wash_cost from WO_PRE_COST_DTLS a, gbl_temp_engine b where a.job_id=b.ref_val and b.user_id = $user_id and b.entry_form=1 and b.ref_from=2
                                 and a.status_active = 1 and a.is_deleted = 0");

        $pre_cost_emb_wash_arr = [];

        foreach ($get_pre_cost_emb_wash as $val) {
            $pre_cost_emb_wash_arr[$val['JOB_NO']]['emb'] = $val['EMBEL_COST'];
            $pre_cost_emb_wash_arr[$val['JOB_NO']]['wash'] = $val['WASH_COST'];
        }

		
		$sqlOldcode="select job_no, po_id, color_mst_id, item_mst_id, shipdate, new_code from fr_temp_code where user_id=".$_SESSION['logic_erp']['user_id']."";
		$nameArray=sql_select($sqlOldcode); $oldCodeArr=array();
		foreach($nameArray as $orow)
		{
			$oldCodeArr[$orow[csf("job_no")]][$orow[csf("po_id")]][$orow[csf("color_mst_id")]][$orow[csf("item_mst_id")]]=$orow[csf("new_code")];
		}
		unset($nameArray);
		
		$currTime=strtotime(date("d-M-Y", time()));
		$backfiftinDays=strtotime(date("d-M-Y", time() - 1296000));

        $plan_cut_extender_slab = [1=>['a' => 6, 'b'=>5.5, 'c'=>5, 'd'=>4.5, 'e'=>4, 'f'=>3.5, 'g'=>3, 'h'=>2.5, 'i'=>2.5],
            2=>['a' => 5, 'b'=>5, 'c'=>4, 'd'=>3.5, 'e'=>3, 'f'=>2.5, 'g'=>2.5, 'h'=>2, 'i'=>2],
            3=>['a' => 5, 'b'=>5, 'c'=>4, 'd'=>3.5, 'e'=>3, 'f'=>2.5, 'g'=>2.5, 'h'=>2, 'i'=>2],
            4=>['a' => 3, 'b'=>2.5, 'c'=>2, 'd'=>1.5, 'e'=>1, 'f'=>1, 'g'=>1, 'h'=>1, 'i'=>1],
            5=>['a' => 5, 'b'=>5, 'c'=>4, 'd'=>3.5, 'e'=>3, 'f'=>2.5, 'g'=>2.5, 'h'=>2, 'i'=>2],
            6=>['a' => 5, 'b'=>3, 'c'=>2, 'd'=>2, 'e'=>1.5, 'f'=>1.5, 'g'=>1.5, 'h'=>1.5, 'i'=>1.5]
        ];

        $plan_cut_extender_slab_solid = [1=>['a' => 5, 'b'=>4.5, 'c'=>4, 'd'=>3.5, 'e'=>3, 'f'=>2.5, 'g'=>2, 'h'=>1.5, 'i'=>1.5],
            2=>['a' => 4, 'b'=>4, 'c'=>3, 'd'=>2.5, 'e'=>2, 'f'=>1.5, 'g'=>1.5, 'h'=>1, 'i'=>1],
            3=>['a' => 4, 'b'=>4, 'c'=>3, 'd'=>2.5, 'e'=>2, 'f'=>1.5, 'g'=>1.5, 'h'=>1, 'i'=>1],
            4=>['a' => 3, 'b'=>2.5, 'c'=>2, 'd'=>1.5, 'e'=>1, 'f'=>1, 'g'=>1, 'h'=>1, 'i'=>1],
            5=>['a' => 4, 'b'=>4, 'c'=>3, 'd'=>2.5, 'e'=>2, 'f'=>1.5, 'g'=>1.5, 'h'=>1, 'i'=>1],
            6=>['a' => 3, 'b'=>2.5, 'c'=>2, 'd'=>1.5, 'e'=>1, 'f'=>1, 'g'=>1, 'h'=>1, 'i'=>1]
        ];

		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.OLDCODE",0," ")."\t".str_pad("O.PROD",0," ")."\t".str_pad("O.CUST",0," ")."\t".str_pad("O^DD:1",0," ")."\t".str_pad("O^DR:1",0," ")."\t".str_pad("O^DQ:1",0," ")."\t".str_pad("O.CONTRACT_QTY",0," ")."\t".str_pad("O.STATUS",0," ")."\t".str_pad("O.TIME",0," ")."\t".str_pad("O.SPRICE",0," ")."\t".str_pad("O.SCOST",0," ")."\t".str_pad("O.COMPLETE",0," ")."\t".str_pad("O.MCOST",0," ")."\t".str_pad("O.UDBUYER_STYLE_QTY",0," ")."\t".str_pad("O.UDFACTORY",0," ")."\t".str_pad("O.UDGSM",0," ")."\t".str_pad("O.UDMERCHANDISER",0," ")."\t".str_pad("O.UDPRE_EFFICIENCY",0," ")."\t".str_pad("O.UDLEAD TIME",0," ")."\t".str_pad("O.UDSEASON",0," ")."\t".str_pad("O.UDSHIP MODE",0," ")."\t".str_pad("O.UDMATERIAL ETA",0," ")."\t".str_pad("O.EVBASE",0," ") . "\t" . str_pad("O.UDACT ACC IN-HOUS", 0, " ") . "\t" . str_pad("O.UDACT FAB IN-HOUS", 0, " ") . "\t" . str_pad("O.UDPR. LEAD TIME", 0, " ") . "\t" . str_pad("O.UDPP APPROVAL", 0, " ")."\r\n";
		
		$order_size=array(); $tmpNewCodeArr=array(); $projectedCodeArr=array(); $confirmCodeArr=array(); $cancelDeleteRowArr=array(); $deleted_colors = [];
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
							if($other_val['order_qty']>0)
							{
								$ex_po=explode("_",$other_val['po_str']);
								$po_id=implode(",",array_filter(array_unique(explode(",",$other_val['bid'])))); 
								$shipdate=date("d/m/Y",strtotime($ship_date));
								$is_confirm=$ex_po[2];
								$is_deleted=$ex_po[3];
								$color_is_deleted=$ex_po[6];
								$projected_po_id=$ex_po[4];

                                $facRecDate = "";
                                $pr_lead_time = 0;
                                if ($tna_task_arr[$ex_po[0]][$company_wise_tna_task_type[$ft_data_arr[$jobno][company_id]]] != "") {
                                    $facRecDate = date("d/m/Y", strtotime($tna_task_arr[$ex_po[0]][$company_wise_tna_task_type[$ft_data_arr[$jobno][company_id]]]));
                                    $difference2 = strtotime($ship_date) - strtotime($tna_task_arr[$ex_po[0]][$company_wise_tna_task_type[$ft_data_arr[$jobno][company_id]]]);
                                    $total_days2 = $difference2 / (60 * 60) / 24;
                                    if ($total_days2 > 0)
                                        $pr_lead_time = $total_days2 + 1; //add one day
                                }

                                $acc_in_house_date = "";
                                if(count($tna_task_acc_in_arr[$ex_po[0]][$company_wise_tna_task_type[$ft_data_arr[$jobno][company_id]]]) > 1){
                                    if($tna_task_acc_in_arr[$ex_po[0]][$company_wise_tna_task_type[$ft_data_arr[$jobno][company_id]]][301] > 0 && $tna_task_acc_in_arr[$ex_po[0]][$company_wise_tna_task_type[$ft_data_arr[$jobno][company_id]]][310] > 0){
                                        $max_acc_in_date = max($tna_task_acc_in_arr[$ex_po[0]][$company_wise_tna_task_type[$ft_data_arr[$jobno][company_id]]]);
                                        $acc_in_house_date = date("d/m/Y", $max_acc_in_date);
                                    }
                                }else {
                                    $max_acc_in_date = max($tna_task_acc_in_arr[$ex_po[0]][$company_wise_tna_task_type[$ft_data_arr[$jobno][company_id]]]);
                                    if ($max_acc_in_date > 0) {
                                        $acc_in_house_date = date("d/m/Y", $max_acc_in_date);
                                    }
                                }

                                $fab_in_house_date = "";
                                if(count($tna_task_fab_in_arr[$ex_po[0]][$company_wise_tna_task_type[$ft_data_arr[$jobno][company_id]]]) > 1) {
                                    if($tna_task_fab_in_arr[$ex_po[0]][$company_wise_tna_task_type[$ft_data_arr[$jobno][company_id]]][277] > 0 && $tna_task_acc_in_arr[$ex_po[0]][$company_wise_tna_task_type[$ft_data_arr[$jobno][company_id]]][306] > 0) {
                                        $max_fab_in_date = max($tna_task_fab_in_arr[$ex_po[0]][$company_wise_tna_task_type[$ft_data_arr[$jobno][company_id]]]);
                                        $fab_in_house_date = date("d/m/Y", $max_fab_in_date);
                                    }
                                }else {
                                    $max_fab_in_date = max($tna_task_fab_in_arr[$ex_po[0]][$company_wise_tna_task_type[$ft_data_arr[$jobno][company_id]]]);
                                    if ($max_fab_in_date > 0) {
                                        $fab_in_house_date = date("d/m/Y", $max_fab_in_date);
                                    }
                                }

                                $pp_aproval_date = "";
                                $max_pp_date = $tna_task_pp_approval_arr[$ex_po[0]][$company_wise_tna_task_type[$ft_data_arr[$jobno][company_id]]][12];
                                if ($max_pp_date > 0) {
                                    $pp_aproval_date = date("d/m/Y", $max_pp_date);
                                }

                                $difference1 = strtotime($ship_date) - strtotime($other_val['porec_date']);
                                $total_days1 = $difference1 / (60 * 60) / 24;
                                $lead_time = 0;
                                if($total_days1 > 0)
                                    $lead_time = $total_days1+1; //add one day
//								if($ex_po[5] != "") $facRecDate=date("d/m/Y",strtotime($ex_po[5]));
//								else $facRecDate="";
								
								$col_sizebreak_id_str=""; $shiping_status_str="";
								$ex_other_data=explode(",",$other_val['color_size_break_id']);
								 
								if($is_confirm==1) $str="F"; else $str="P";
								$po_no=$po_st;
								$old_str_po=''; $str_po=''; $changed=0; $ssts="0"; $old_item_code=''; $buyer_style=''; 
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
									$buyer_style=$ft_data_arr[$jobno][style_ref_no]."::".$ft_data_arr[$jobno][season]."::".$fr_product_type[$item_id];//."::".$ft_data_arr[$jobno][season]."::".$jobno."::".$fr_product_type[$item_id];
								}
								else
								{
									$str_item=""; 
									$buyer_style=$ft_data_arr[$jobno][style_ref_no]."::".$ft_data_arr[$jobno][season];//."::".$ft_data_arr[$jobno][season]."::".$jobno;
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
								//$str_po=$jobno."::".$po_no."::".$color_lib[$color_id]."::".date("ymd",strtotime($shipdate))."".$str_item; ."::".$jobno."::".$shipdate."".$str_item."".$str_job
								if($ft_data_arr[$jobno][order_uom] == 58){
                                    $str_po=$ft_data_arr[$jobno][style_ref_no]."::".$po_no."-".$garments_item[$item_id]."::".$color_lib[$color_id]."::".$jobno;
                                }else{
                                    $str_po=$ft_data_arr[$jobno][style_ref_no]."::".$po_no."::".$color_lib[$color_id]."::".$jobno;
                                }


								$tmpNewCodeArr[$str_po]=$jobno."__".$po_id."__".$itemMstId."__".$colorMstId."__".$shipdate."__".$oldCode; 

                                foreach($ex_other_data as $val_color_size_id)
                                {
                                    $ex_color_size_break_id_val=explode("**",$val_color_size_id);
                                    $tmp_col_size[$ex_color_size_break_id_val[0]]=$ex_color_size_break_id_val[1];
                                    if( $ex_color_size_break_id_val[1]==3) $ssts="1";
                                }

                                if($is_confirm==2)
                                {
                                    $projectedCodeArr[$po_id]=$str_po;
                                }
								
								if($is_confirm==1 && $ssts != "1")
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
								//color_size_break_id

                                foreach($new_color_size[$jobno][$po_st][$ship_date][$item_id][$color_id] as $cids)
                                {
                                    $newid_ar[trim($cids)]=$str_po;
                                }

								$tmppoo=implode("",array_unique(explode(",",$po_id)));
                                $count_temp = count($temp_check_arr[$jobno][$po_st][$ship_date][$item_id][$color_id]);
								if( $is_deleted==2 ||  $is_deleted==3 || $is_deleted==0)
								{ 
									$str="T";
									$deleted_po_list[$tmppoo]=$tmppoo;

								}else {
                                    if (($color_is_deleted == 0 && $count_temp == 1) || ($color_is_deleted == 3 && $count_temp == 1) || ($color_is_deleted == 2 && $count_temp == 1)) {
                                        $str = "T";
                                        foreach ($new_color_size[$jobno][$po_st][$ship_date][$item_id][$color_id] as $cids) {
                                            if ($temp_id_wise_status[trim($cids)] == 0 || $temp_id_wise_status[trim($cids)] == 3)
                                                $deleted_colors[trim($cids)] = $str_po;
                                        }
                                    }
                                }
								
								$fob=0; $fob=$other_val['order_value']/$other_val['order_qty'];
								$udbuyer_style_qty=''; $udbuyer_style_qty=$buyer_name_array[$ft_data_arr[$jobno][buyer_name]].'_'.$ft_data_arr[$jobno][style_ref_no].'_'.$other_val['order_qty'];
								$fabgsm=implode(",",array_filter(array_unique(explode(",",$ft_data_arr[$jobno][gsm]))));
								
								$cmpcs=0;
								$cmpcs=$ft_data_arr[$jobno][cmpcs];
								$noldCode=""; $str_po01 = $str_po;
								if($oldCode!=$str_po){
                                    $noldCode = $oldCode;
                                    $str_po01 = $oldCode;
                                }
								if($str=="T")//Delete and Cancel New File
								{
									$cancelDeleteRowArr[$str_po01]="T";
								}
								else
								{
                                    $order_plan_cut_team = $other_val['plan_cut'];
                                    $order_qty = $other_val['order_qty'];
                                    if($order_plan_cut_team == $order_qty){
                                        $slab = "";

                                        if($order_plan_cut_team <= 1000)
                                            $slab = 'a';
                                        elseif($order_plan_cut_team > 1000 && $order_plan_cut_team <= 3000)
                                            $slab = 'b';
                                        elseif($order_plan_cut_team > 3000 && $order_plan_cut_team <= 5000)
                                            $slab = 'c';
                                        elseif($order_plan_cut_team > 5000 && $order_plan_cut_team <= 8000)
                                            $slab = 'd';
                                        elseif($order_plan_cut_team > 8000 && $order_plan_cut_team <= 10000)
                                            $slab = 'e';
                                        elseif($order_plan_cut_team > 10000 && $order_plan_cut_team <= 15000)
                                            $slab = 'f';
                                        elseif($order_plan_cut_team > 15000 && $order_plan_cut_team <= 20000)
                                            $slab = 'g';
                                        elseif($order_plan_cut_team > 20000 && $order_plan_cut_team <= 30000)
                                            $slab = 'h';
                                        elseif($order_plan_cut_team > 30000)
                                            $slab = 'i';

                                        if($pre_cost_emb_wash_arr[$jobno]['emb'] > 0 || $pre_cost_emb_wash_arr[$jobno]['wash'] > 0){
                                            if($plan_cut_extender_slab[$other_val['company']][$slab] > 0){
                                                $get_additional = ($order_plan_cut_team/100)*$plan_cut_extender_slab[$other_val['company']][$slab];
                                                $order_plan_cut_team = $order_plan_cut_team+round($get_additional);
                                            }
                                        }else{
                                            if($plan_cut_extender_slab_solid[$other_val['company']][$slab] > 0){
                                                $get_additional = ($order_plan_cut_team/100)*$plan_cut_extender_slab_solid[$other_val['company']][$slab];
                                                $order_plan_cut_team = $order_plan_cut_team+round($get_additional);
                                            }
                                        }
                                    }
//                                    echo $order_plan_cut_team."---".$order_qty."<br>";
									$txt .=str_pad($str_po,0," ")."\t".str_pad($noldCode,0," ")."\t".str_pad($buyer_style,0," ")."\t".str_pad($buyer_name_array[$ft_data_arr[$jobno][buyer_name]],0," ")."\t".str_pad(' '.$shipdate,0," ")."\t".str_pad(' '.$facRecDate,0," ")."\t".str_pad($order_plan_cut_team,0," ")."\t".str_pad($other_val['order_qty'],0," ")."\t".str_pad($str,0," ")."\t".str_pad($ft_data_arr[$jobno][team],0," ")."\t".str_pad(number_format($fob, 4, '.', ''),0," ")."\t".str_pad($cmpcs,0," ")."\t".str_pad($ssts,0," ")."\t".str_pad($cmpcs,0," ")."\t".str_pad($udbuyer_style_qty,0," ")."\t".str_pad($ft_data_arr[$jobno][company],0," ")."\t".str_pad($fabgsm,0," ")."\t".str_pad($ft_data_arr[$jobno][team],0," ")."\t".str_pad($ft_data_arr[$jobno][sew_eff],0," ")."\t".str_pad($lead_time,0," ")."\t".str_pad($ft_data_arr[$jobno][season],0," ")."\t".str_pad($ft_data_arr[$jobno][ship_mode],0," ")."\t".str_pad(' '.$facRecDate,0," ")."\t".str_pad(' '.date("d/m/Y",strtotime($other_val['porec_date'])),0," ") . "\t" . str_pad(' '.$acc_in_house_date, 0, " ") . "\t" . str_pad(' '.$fab_in_house_date, 0, " ") . "\t" . str_pad($pr_lead_time, 0, " ") . "\t" . str_pad(' '.$pp_aproval_date, 0, " ")."\r\n";
								}
							}
                            else{
                                $ex_po=explode("_",$other_val['po_str']);
                                $po_id=implode(",",array_filter(array_unique(explode(",",$other_val['bid']))));
                                $is_confirm=$ex_po[2];
                                $is_deleted=$ex_po[3];
                                $color_is_deleted=$ex_po[6];
                                $projected_po_id=$ex_po[4];

                                $col_sizebreak_id_str=""; $shiping_status_str="";
                                $ex_other_data=explode(",",$other_val['color_size_break_id']);
                                $po_no=$po_st;
                                $old_str_po=''; $str_po=''; $changed=0; $ssts="0"; $old_item_code=''; $buyer_style='';

                                $icmstid=""; $oldCode="";
                                $icmstid=$other_val['icmstid'];
                                $exicid=explode("_",$icmstid);

                                $itemMstId=$exicid[0];
                                $colorMstId=$exicid[1];

                                $oldCode=$oldCodeArr[$jobno][$po_id][$colorMstId][$itemMstId];
                                if($ft_data_arr[$jobno][order_uom] == 58){
                                    $str_po = $ft_data_arr[$jobno][style_ref_no] . "::" . $po_no . "-" . $garments_item[$item_id] . "::" . $color_lib[$color_id] . "::" . $jobno;
                                }else{
                                    $str_po = $ft_data_arr[$jobno][style_ref_no] . "::" . $po_no . "::" . $color_lib[$color_id] . "::" . $jobno;
                                }

                                foreach($ex_other_data as $val_color_size_id)
                                {
                                    $ex_color_size_break_id_val=explode("**",$val_color_size_id);
                                    $tmp_col_size[$ex_color_size_break_id_val[0]]=$ex_color_size_break_id_val[1];
                                    if( $ex_color_size_break_id_val[1]==3) $ssts="1";
                                }

                                foreach($new_color_size[$jobno][$po_st][$ship_date][$item_id][$color_id] as $cids)
                                {
                                    $newid_ar[trim($cids)]=$str_po;
                                }

                                $tmppoo=implode("",array_unique(explode(",",$po_id)));
                                $count_temp = count($temp_check_arr[$jobno][$po_st][$ship_date][$item_id][$color_id]);
                                if( $is_deleted==2 ||  $is_deleted==3 || $is_deleted==0)
                                {
                                    $str="T";
                                    $deleted_po_list[$tmppoo]=$tmppoo;

                                }else {
                                    if (($color_is_deleted == 0 && $count_temp == 1) || ($color_is_deleted == 3 && $count_temp == 1) || ($color_is_deleted == 2 && $count_temp == 1)) {
                                        $str = "T";
                                        foreach ($new_color_size[$jobno][$po_st][$ship_date][$item_id][$color_id] as $cids) {
                                            if ($temp_id_wise_status[trim($cids)] == 0 || $temp_id_wise_status[trim($cids)] == 3)
                                                $deleted_colors[trim($cids)] = $str_po;
                                        }
                                    }
                                }

                                if($str=="T")//Delete and Cancel New File
                                {
                                    if($oldCode!=$str_po){
                                        $cancelDeleteRowArr[$oldCode]="T";
                                    }else{
                                        $cancelDeleteRowArr[$str_po]="T";
                                    }
                                }
                            }
						}
					}
				}
			}
		}
//        echo "<br>Orders<br>";
//        echo $txt;
		fwrite($myfile, $txt);
		fclose($myfile);
		$txt="";
		
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
//			$shipdate=change_date_format($exstrVal[4], '', '', 1);
			$oldCode=$exstrVal[5];
//			echo "insert into fr_temp_code (job_no, po_id, color_mst_id, item_mst_id, shipdate, new_code, old_code, user_id) values ('".$jobno."','".$po_id."','".$colorMstId."','".$itemMstId."','".$shipdate."','".$ncode."','".$oldCode."',".$_SESSION['logic_erp']['user_id'].");<br>";
			execute_query("insert into fr_temp_code (job_no, po_id, color_mst_id, item_mst_id, shipdate, new_code, old_code, user_id) values ('".$jobno."','".$po_id."','".$colorMstId."','".$itemMstId."','".$shipdate."','".$ncode."','".$oldCode."',".$_SESSION['logic_erp']['user_id'].")");
		}

		oci_commit($con);
		disconnect($con);
		
		$file_name="frfiles/RELATE.TXT";
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
			
			$projCode=$projectedCodeArr[$proj_po_id];
			
			if($cstr!="")
				$txt .=str_pad($cstr,0," ")."\t".str_pad($projCode,0," ")."\t END\r\n";
		}
//        echo "<br>Relate<br>";
//        echo $txt;
		fwrite($myfile, $txt);
		fclose($myfile);
		$txt="";
		
		
		$file_name="frfiles/ORDUPDAT_Cancel.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");

		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.STATUS",0," ")."\r\n";
		foreach($cancelDeleteRowArr as $cdcode=>$val)
		{
			if($cdcode!="")
				$txt .=str_pad($cdcode,0," ")."\t".str_pad($val,0," ")."\r\n";
		}

		fwrite($myfile, $txt);
		fclose($myfile);
		$txt="";
		
		//die;
		
		// Production file
		//$prod_sql="SELECT a.po_break_down_id, a.country_id, a.item_number_id, a.floor_id, a.sewing_line, a.production_type, a.production_date as production_date, b.color_size_break_down_id, b.production_qnty AS production_quantity, a.embel_name, a.status_active, a.is_deleted from pro_garments_production_mst a, pro_garments_production_dtls b, tmp_col_po_id c where a.production_type in (1,2,3,4,5,11) and a.id=b.mst_id and b.color_size_break_down_id=c.col_po_id and c.userid='$user_id' order by production_date ASC,b.color_size_break_down_id asc"; 
		
		$backOneDaysGmtsProd=date('d-M-Y', strtotime(' -1 day'));
		$backOneDaysGmtsProdCond="";
		//if($backOneDaysGmtsProd!="") $backOneDaysGmtsProdCond=" and a.production_date>='$backOneDaysGmtsProd'";
		
		$prod_sql="SELECT a.po_break_down_id as PO_BREAK_DOWN_ID, a.company_id as COMPANY_ID, a.production_source as PRODUCTION_SOURCE, a.sewing_line as SEWING_LINE, a.production_type as PRODUCTION_TYPE, max(a.production_date) as PRODUCTIONDATE, b.color_size_break_down_id as COLOR_SIZE_BREAK_DOWN_ID, sum(b.production_qnty) AS PRODUCTION_QUANTITY, sum(b.reject_qty) as REJECT_QTY, a.embel_name as EMBEL_NAME, a.status_active as STATUS_ACTIVE, a.is_deleted as IS_DELETED from pro_garments_production_mst a, pro_garments_production_dtls b, gbl_temp_engine c where a.id=b.mst_id and b.color_size_break_down_id=c.ref_val and c.user_id = ".$user_id." and c.entry_form=1 and c.ref_from=3 and a.production_type in (1,2,3,4,5,8) 
		group by a.po_break_down_id,a.company_id, a.sewing_line, a.production_source, a.production_type, b.color_size_break_down_id, a.embel_name, a.status_active, a.is_deleted order by a.po_break_down_id, PRODUCTIONDATE asc"; //'. $backOneDaysGmtsProdCond .'
		//echo $prod_sql; die;
	//	print_r($deleted_po_list);
		$prod_sql_res=sql_select($prod_sql);
        $company_wise_sub_con_line_arr = [1=>'655', 2=>'995', 3=>'845', 4=>'390', 5=>'1100'];
//        $company_wise_sub_con_line_arr = [1=>[655 => 'GT SUBCON 1'], 2=>[995 => 'Mars Stitch SUBCON'], 3=>[845 => 'Brothers SUBCON'], 4=>[390=>'4A Jacket SUBCON'], 5=>[1100 => 'CBM SUBCON']];
		foreach($prod_sql_res as $row_sew)
		{
			//echo $row_sew["PRODUCTION_TYPE"].'<br>';
			if($deleted_po_list[$row_sew["PO_BREAK_DOWN_ID"]]!='')
				$deleted_colors[$row_sew["COLOR_SIZE_BREAK_DOWN_ID"]]=$row_sew["COLOR_SIZE_BREAK_DOWN_ID"];
			
//			if($newid_ar[$row_sew["COLOR_SIZE_BREAK_DOWN_ID"]]!='' && $prod_up_arr_powise[$row_sew['COLOR_SIZE_BREAK_DOWN_ID']]==$row_sew["PO_BREAK_DOWN_ID"] && $deleted_po_list[$row_sew["PO_BREAK_DOWN_ID"]]=='')
//			{
            if($deleted_colors[$row_sew["COLOR_SIZE_BREAK_DOWN_ID"]] == '' ) {
                if ($row_sew["PRODUCTION_TYPE"] == 3)//issue
                {
                    if ($row_sew["EMBEL_NAME"] == 1) $row_sew["PRODUCTION_TYPE"] = 10;
                    else if ($row_sew["EMBEL_NAME"] == 2) $row_sew["PRODUCTION_TYPE"] = 17;
                    else if ($row_sew["EMBEL_NAME"] == 3) $row_sew["PRODUCTION_TYPE"] = 18;
                } else if ($row_sew["PRODUCTION_TYPE"] == 2) // send emb
                {
                    if ($row_sew["EMBEL_NAME"] == 1) $row_sew["PRODUCTION_TYPE"] = 9;
                    else if ($row_sew["EMBEL_NAME"] == 2) $row_sew["PRODUCTION_TYPE"] = 11;
                    else if ($row_sew["EMBEL_NAME"] == 3) $row_sew["PRODUCTION_TYPE"] = 12;
                }
//				if($row_sew["PRODUCTION_TYPE"]==11) $row_sew["PRODUCTION_TYPE"]=7;

                if ($row_sew["STATUS_ACTIVE"] == 0 && $row_sew["IS_DELETED"] == 1) $row_sew["PRODUCTION_QUANTITY"] = 0;

                $row_sew["SEWING_LINE"] = (int)$line_name_res[$row_sew["SEWING_LINE"]];

                if ($row_sew["SEWING_LINE"] == 129) {
                    $row_sew["SEWING_LINE"] = 166;
                } elseif ($row_sew["SEWING_LINE"] == 130) {
                    $row_sew["SEWING_LINE"] = 167;
                } elseif ($row_sew["SEWING_LINE"] == 131) {
                    $row_sew["SEWING_LINE"] = 168;
                } elseif ($row_sew["SEWING_LINE"] == 132) {
                    $row_sew["SEWING_LINE"] = 169;
                } elseif ($row_sew["SEWING_LINE"] == 133) {
                    $row_sew["SEWING_LINE"] = 170;
                } elseif ($row_sew["SEWING_LINE"] == 134) {
                    $row_sew["SEWING_LINE"] = 171;
                }

                $prod_typ = $row_sew["PRODUCTION_TYPE"];
                if ($prod_typ != 5) {
                    $row_sew["SEWING_LINE"] = 0;
                } else {
                    if ($row_sew['PRODUCTION_SOURCE'] == 3) {
                        $row_sew["SEWING_LINE"] = $company_wise_sub_con_line_arr[$row_sew["COMPANY_ID"]];
                    }
                }
                //if( $prod_typ==8 ||$prod_typ==1 ||$prod_typ==9 ||$prod_typ==10 ||$prod_typ==4 ) $row_sew["SEWING_LINE"]=0;
                $exfdate = $row_sew["PRODUCTIONDATE"];


                $production_qty[$newid_ar[$row_sew["COLOR_SIZE_BREAK_DOWN_ID"]]][$row_sew["PRODUCTION_TYPE"]][$row_sew["SEWING_LINE"]]['qnty'] += $row_sew["PRODUCTION_QUANTITY"];
                $production_qty[$newid_ar[$row_sew["COLOR_SIZE_BREAK_DOWN_ID"]]][$row_sew["PRODUCTION_TYPE"]][$row_sew["SEWING_LINE"]]['rejqnty'] += $row_sew["REJECT_QTY"];
                $production_qty[$newid_ar[$row_sew["COLOR_SIZE_BREAK_DOWN_ID"]]][$row_sew["PRODUCTION_TYPE"]][$row_sew["SEWING_LINE"]]['proddate'] = $row_sew["PRODUCTIONDATE"];
                //$production_qty[$newid_ar[$row_sew["COLOR_SIZE_BREAK_DOWN_ID"]]][$row_sew["PRODUCTION_TYPE"]][$row_sew["PRODUCTION_DATE"]][$row_sew["SEWING_LINE"]]['col_size_id']=$row_sew["COLOR_SIZE_BREAK_DOWN_ID"];
//			}
            }
		}
		unset($prod_sql_res);
		/*echo"<pre>";
		print_r($production_qty);*/ //die;
	
		//$prod_sql="SELECT a.mst_id, a.color_size_break_down_id, a.production_qnty, b.ex_factory_date, b.shiping_status from pro_ex_factory_dtls a, pro_ex_factory_mst b, tmp_col_po_id c where a.color_size_break_down_id=c.col_po_id and b.id=a.mst_id and a.is_deleted=0 and a.status_active=1 and a.mst_id=b.id and b.is_deleted=0 and b.status_active=1 and c.userid='$user_id'";// and c.userid='$user_id'
		
		$backOneDaysGmtsExfacCond="";
		//if($backOneDaysGmtsProd!="") $backOneDaysGmtsExfacCond=" and b.ex_factory_date>='$backOneDaysGmtsProd'";
		
		$prod_sql='SELECT a.mst_id as "mst_id", a.color_size_break_down_id as "color_size_break_down_id", ( case when b.entry_form <> 85 then a.production_qnty else 0 end) as "production_qnty", (case when b.entry_form = 85 then a.production_qnty else 0 end) as "production_qnty_rtn", b.ex_factory_date as "ex_factory_date", b.shiping_status as "shiping_status", b.po_break_down_id as "po_break_down_id" from pro_ex_factory_dtls a, pro_ex_factory_mst b, gbl_temp_engine c where a.mst_id=b.id and b.po_break_down_id=c.ref_val and c.user_id = '.$user_id.' and c.entry_form=1 and c.ref_from=1 '.$backOneDaysGmtsExfacCond.' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by b.ex_factory_date asc';// and c.userid='$user_id'
//		echo $prod_sql; die;
		$prod_sql_res=sql_select($prod_sql); $exFactoryArr=array();
		foreach($prod_sql_res as $row_sew)
		{
//			if($newid_ar[$row_sew["color_size_break_down_id"]]!='' && $deleted_colors[$row_sew["color_size_break_down_id"]]=='')
//			{
            if($deleted_po_list[$row_sew["po_break_down_id"]]!='')
                $deleted_colors[$row_sew["color_size_break_down_id"]]=$row_sew["color_size_break_down_id"];

            if($deleted_colors[$row_sew["color_size_break_down_id"]] == '' ) {
                $row_sew["production_type"] = 13;
                $row_sew["sewing_line"] = 0;

                if ($row_sew["shiping_status"] == 3) {
                    $exFactoryArr[$newid_ar[$row_sew["color_size_break_down_id"]]] = $row_sew["ex_factory_date"];
                }

                $production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['qnty'] += ($row_sew["production_qnty"] - $row_sew["production_qnty_rtn"]);
                if ($row_sew["ex_factory_date"] != "")
                    $production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["sewing_line"]]['proddate'] = $row_sew["ex_factory_date"];
                //$production_qty[$newid_ar[$row_sew["color_size_break_down_id"]]][$row_sew["production_type"]][$row_sew["ex_factory_date"]][$row_sew["sewing_line"]]['col_size_id']=$row_sew["color_size_break_down_id"];
//			}
            }
		}

		unset($prod_sql_res);
		
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2,3) and ENTRY_FORM=1");
		oci_commit($con);
		disconnect($con);
		
		$file_name="frfiles/UPDNORM.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");

		//$txt .=str_pad("U.ORDER",0," ")."\t".str_pad("U.DATE",0," ")."\t".str_pad("U.OPERATION",0," ")."\t".str_pad("U.LINE_EXTERNAL_ID",0," ")."\t".str_pad("U.QTY",0," ")."\t".str_pad("U.OPN_COMPLETE",0," ")."\r\n";
		
		$txt .=str_pad("U.ORDER",0," ")."\t".str_pad("U.DATE",0," ")."\t".str_pad("U.OPERATION",0," ")."\t".str_pad("U.LINE",0," ")."\t".str_pad("U.LINE_EXTERNAL_ID",0," ")."\t".str_pad("U.QTY",0," ")."\t".str_pad("U.REJECTQTY",0," ")."\t".str_pad("U.OPN_COMPLETE",0," ")."\r\n";
		
		foreach($production_qty as $oid=>$prodctn)
		{
			ksort($prodctn);
            foreach ($prodctn as $prod_typ => $prodtypedata) {
                ksort($prodtypedata);
                foreach ($prodtypedata as $line => $othersdata) {
                    $maxproddate = "";
                    //print_r($sdata);
                    $maxproddate = date("d/m/Y", strtotime($othersdata['proddate']));
                    //if($prod_typ==5) { print_r($sdata); echo $line."="; }
                    $prod_type_n = $gmt_prod_id_map[$prod_typ] * 1;

                    //$maxproddate=date("d/m/Y",strtotime($sdate['pdate']));
                    if ($line == 0) {
                        $linestr = "";
                        $line = "";
                    } else {
                        if ($line == 1100) {
                            $linestr = "CBM SUBCON";
                        } elseif ($line == 995) {
                            $linestr = "Mars Stitch SUBCON";
                        } elseif ($line == 845) {
                            $linestr = "Brothers SUBCON";
                        } elseif ($line == 655) {
                            $linestr = "GT SUBCON 1";
                        } elseif ($line == 390) {
                            $linestr = "4A Jacket SUBCON";
                        } else {
                            $linestr = $erplinenamearr[$line];
                            $line = $line_name[$line];
                        }
                    }//(int)$line_name_res[$line];

                    $flor = $floor_name_res[$line];
                    if ($flor == 0) $flor = "";
                    if ($line == 0) $line = "";

                    if ($prod_type_n > 0)
                        $txt .= str_pad($oid, 0, " ") . "\t " . str_pad(date("d/m/Y", strtotime($othersdata['proddate'])), 0, " ") . "\t" . str_pad($prod_type_n, 0, " ") . "\t" . str_pad($linestr, 0, " ") . "\t" . str_pad($line, 0, " ") . "\t" . str_pad($othersdata['qnty'], 0, " ") . "\t" . str_pad($othersdata['rejqnty'], 0, " ") . "\t" . str_pad('', 0, " ") . "\r\n";
                }
            }
		}

		fwrite($myfile, $txt);
		fclose($myfile);
		$txt="";
		
		/*$file_name="frfiles/ORDUPDAT.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");

		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.COMPLETE",0," ")."\t".str_pad("O.UDACTUAL SHIPMENT",0," ")."\r\n";
		foreach($exFactoryArr as $ocode=>$exdate)
		{
			if($ocode!="")
				$txt .=str_pad($ocode,0," ")."\t 1 \t ".str_pad(date("d/m/Y",strtotime($exdate)),0," ")."\r\n";
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";*/
		//	print_r($production_qty); die;
		// print_r($production_qty); die;
		//$sql=sql_select("select id, master_tble_id, image_location from common_photo_library where is_deleted=0 and file_type=1 and form_name='knit_order_entry'  $job_cond_img");
		$sql=sql_select("select id as ID, master_tble_id as MASTER_TBLE_ID, image_location as IMAGE_LOCATION from common_photo_library where is_deleted=0 and file_type=1 and form_name='knit_order_entry' ".$jobNoImgCond." ");
		//echo 'select id as "ID", master_tble_id as "MASTER_TBLE_ID", image_location as "IMAGE_LOCATION" from common_photo_library where is_deleted=0 and file_type=1 and form_name="knit_order_entry" '.$jobNoImgCond.' '; die;
		
		$file_name="frfiles/IMGATTACH.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .="IMG.CODE\tIMG.FILENAME\tIMG.NAME\tIMG.FILEPATH\tIMG.DEFAULT\r\n";
		
		$zipimg = new ZipArchive();			// Load zip library	
		$filenames = str_replace(".sql",".zip",'frfiles/ImgFolders.sql');			// Zip name
		if($zipimg->open($filenames, ZIPARCHIVE::CREATE)!==TRUE)
		{		// Opening zip file to load files
			$error .=  "* Sorry ZIP creation failed at this time<br/>"; echo $error;
		}
		$imlocc="D:\FR_IMAGE" ;
		foreach($sql as $rows)
		{
			$name=explode("/",$rows["IMAGE_LOCATION"]);
			foreach( $img_prod[$rows["MASTER_TBLE_ID"]] as $job  )
			{
				$exfile=explode(".",$name[1]);
				if(end($exfile) =='jpg' || end($exfile) =='JPG' || end($exfile) =='png' || end($exfile) =='PNG' || end($exfile) =='BMP' || end($exfile) =='bmp')
				{
                    $image_name = str_replace('knit_order_entry_', "", $name[1]);
					$txt .=$job."\t".$name[1]."\t".str_replace(array(".jpg", ".JPG", ".png", ".PNG", ".bmp", ".BMP"),array("", "", "", "", "", ""), $image_name)."\t".$imlocc."\t1\r\n";
				}
			}
			$extfile=explode(".",$rows["IMAGE_LOCATION"]);
			if(end($extfile) =='jpg' || end($extfile) =='JPG' || end($extfile) =='png' || end($extfile) =='PNG' || end($extfile) =='BMP' || end($extfile) =='bmp')
			{
				//echo "0**../../../".$rows[csf("image_location")]; 
				$zipimg->addFile("../../../".$rows["IMAGE_LOCATION"]);
			}
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
		$file_name="frfiles/IMGATTACH.TXT";
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