<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

/*$fr_product_type=array(
1=>"LS TEE",
2=>"SS TEE",
3=>"POLO",
4=>"POLO",
5=>"TANK TOP",
6=>"LS TEE",
7=>"Hoodies",
8=>"Y-NECK TEE",
9=>"TANK TOP",
10=>"LS TEE",
11=>"High Neck/Turtle Neck",
12=>"Scarf",
14=>"Jacket",
15=>"Jacket",
16=>"Night Wear",
17=>"DRESS",
18=>"DRESS",
19=>"DRESS",
20=>"LONG PANT",
21=>"Short Pant",
22=>"LONG PANT",
23=>"LONG PANT",
24=>"Romper Short Sleeve",
25=>"Romper Long Sleeve",
26=>"Romper Sleeveless",
27=>"Romper",
28=>"Legging",
29=>"SHORT PANT",
30=>"Long Skirt",
31=>"Jump Suit",
32=>"Cap",
33=>"Tanktop Pyjama",
34=>"Jump Suit",
35=>"LONG PANT",
36=>"Bag",
37=>"Bra",
38=>"Panty",
39=>"Hoody T-Shirt",
40=>"TANK TOP",
41=>"TANK TOP",
42=>"SHORT PANT",
43=>"SHORT PANT",
44=>"SHORT PANT",
45=>"SHORT PANT",
46=>"Panty",
47=>"Panty",
48=>"Panty",
49=>"Panty",
50=>"Panty",
51=>"Bikini",
52=>"Lingerie",
53=>"JACKET",
54=>"Panty",
60=>"Tube",
61=>"Tube",
62=>"Tube",
63=>"Tube",
64=>"Tube",
65=>"Tube",
66=>"Tube",
67=>"LONG PANT",
68=>"Sports Ware",
69=>"TANK TOP",
70=>"LONG PANT",
71=>"LONG PANT",
72=>"Bolero",
73=>"TANK TOP",
74=>"DRESS"
);*/

$fr_product_type=array(
1=>"LS TEE",
2=>"SS TEE",
3=>"POLO",
4=>"POLO",
5=>"TANK TOP",
6=>"SS TEE",
7=>"Hoody T-Shirt",
8=>"Y-NECK TEE",
9=>"VEST",
10=>"SS TEE",
11=>"LS TEE",
12=>"Scarf",
14=>"JACKET",
15=>"JACKET",
16=>"DRESS",
17=>"FROCK",
18=>"DRESS",
19=>"DRESS",
20=>"LONG PANT",
21=>"SHORT PANT",
22=>"LONG PANT",
23=>"LONG PANT",
24=>"JUMP SUIT",
25=>"JUMP SUIT",
26=>"VEST",
27=>"JUMP SUIT",
28=>"LEGGING",
29=>"LEGGING",
30=>"Long Skirt",
31=>"Jump Suit",
32=>"Cap",
33=>"Top Btm Set",
34=>"Top Btm Set",
35=>"LONG PANT",
36=>"Bag",
37=>"Bag",
38=>"PANTY",
39=>"JACKET",
40=>"VEST",
41=>"TANK TOP",
42=>"SHORT PANT",
43=>"SHORT PANT",
44=>"SHORT PANT",
45=>"SHORT PANT",
46=>"PANTY",
47=>"SHORT PANT",
48=>"SHORT PANT",
49=>"SHORT PANT",
50=>"SHORT PANT",
51=>"PANTY",
52=>"PANTY",
53=>"PANTY",
54=>"PANTY",
60=>"Socks",
61=>"Socks",
62=>"Socks",
63=>"Socks",
64=>"Socks",
65=>"Socks",
66=>"Socks",
67=>"LONG PANT",
68=>"Sports Ware",
69=>"SS TEE",
70=>"LONG PANT",
71=>"SHORT PANT",
72=>"Bra",
73=>"SS TEE",
74=>"DRESS"
);

$group_unit_array=array(
10 => '',
    9 => 1,
    1 => 1,
    2 => 1,
    3 => 1,
    4 => 2,
    5 => 2,
    6 => 2,
    7 => 2,
    8 => '',
    18 => '',
    12 => '',
    11 => '',
    13 =>'',
    14 => '',
    15 =>'',
    16 => ''
);

$line_array=array( 67 => 1,
 7 => 10,
    15 => 11,
    16 => 12,
    59 => 13,
    18 => 14,
    19 => 15,
    20 => 16,
    21 => 17,
    22 => 18,
    23 => 19,
    61 => 2,
    68 => 20,
    8 => 21,
    9 => 22,
    17 => 23,
    11 => 24,
    10 => 25,
    12 => 26,
    13 => 27,
    14 => 28,
    45 => 29,
    62 => 3,
    46 => 30,
    47 => 31,
    48 => 32,
    49 => 33,
    50 => 34,
    51 => 35,
    63 => 36,
    37 => 37,
    36 => 38,
    34 => 39,
    1 => 4,
    35 => 40, 
    33 => 41,
    57 => 42,
    32 => 43,
    31 => 44,
    56 => 45,
    52 => 46,
    54 => 47,
    55 => 48,
    70 => 49,
    2 => 5,
    24 => 50,
    25 => 51,
    60 => 52,
    26 => 53,
    27 => 54,
    28 => 55,
    29 => 56,
    30 => 57,
    38 => 58,
    58 =>0,
    39 => 59,
    3 => 6,
    40 => 60
);
	 
 
$mapped_tna_task = array(	
1	=> "5",			
2	=> "",				
3	=> "",				
4	=> "",				
5	=> "",			
7	=> "25",		
8	=> "40",
9	=> "15", 		
10	=> "20",			
11	=> "",				
12	=> "45",			
13	=> "30",			
14	=> "75",		
15	=> "80",		
16	=> "85",		
17	=> "90",		
19	=> "55",		
20	=> "60",		
21	=> "",			
22	=> "50",		
23	=> "35",		
24	=> "",			
25	=> "",			
26	=> "",			
27	=> "",			
28	=> "",			
29	=> "",			
30	=> "",				
31	=> "10",		
32	=> "95",			
33	=> "",		
34	=> "",			
40	=> "",				
41	=> "",			
50	=> "",				
51	=> "120",			
52	=> "130",		
60	=> "135",			
61	=> "",			
62	=> "170",			
63	=> "180",		
64	=> "145",			
70	=> "100",			
71	=> "105",		
72	=> "145",	
73	=> "160",		
80	=> "65",			
81	=> "",			
82	=> "",			
83	=> "",				
84	=> "190",			
85	=> "200",			
86	=> "210",			
87	=> "",			
88	=> "",				
89	=> "220",		
90	=> "230",		
100	=> "",			
101	=> "",			
110	=> "",				
120	=> "",				
121	=> "");	

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	foreach (glob("frfiles/"."*.*") as $filename)
	{			
		@unlink($filename);
	}
    
	 
	header('Content-Type: text/csv; charset=utf-8');
	if ( $cbo_fr_integrtion==0 )
	{
		$color_lib=return_library_array("select id,color_name from lib_color","id","color_name");
		$floor_name=return_library_array("select id,floor_name from  lib_prod_floor","id","floor_name");
		$line_name=return_library_array("select id,line_name from lib_sewing_line","id","line_name");
		$line_name_res=return_library_array("select id,line_number from prod_resource_mst","id","line_number");
		$floor_name_res=return_library_array("select id,floor_id from prod_resource_mst","id","floor_id");
		 
		// Customer File
		$sql=sql_select ( "select id,short_name from lib_buyer" );
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
			
		// Products file Data
		//echo $received_date; die;
		 	if( $db_type==0 )
			{
				//$shipment_date="and c.country_ship_date >= '2014-11-01'";
				$shipment_date="and b.po_received_date >= '$received_date'";
				if(trim( $received_date)=="") $received_date="2015-08-01"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
			}
			if($db_type==2)
			{
				$shipment_date="and b.po_received_date >= '01-Oct-2015'";
				 if(trim( $received_date)=="") $received_date="01-Oct-2015"; else $received_date=date("d-M-Y",strtotime($received_date));
			}
			$shiping_status="and c.shiping_status !=3";
			
			//letsgrowourfood.com
			
			$po_arr=array();
			$ft_data_arr=array();
			 
			$sql_po="select a.id,a.job_no,a.style_ref_no,a.gmts_item_id,a.buyer_name,b.po_quantity from wo_po_details_master a, wo_po_break_down b  where a.job_no=b.job_no_mst  and b.shipment_date >= '$received_date' and b.is_confirmed=2   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  order by a.id,b.id";
			$sql_po_data=sql_select($sql_po);
			foreach($sql_po_data as $sql_po_row)
			{
				$ft_data_arr[$sql_po_row[csf('job_no')]][job_no]=$sql_po_row[csf('job_no')];//P.CODE
				$ft_data_arr[$sql_po_row[csf('job_no')]][gmts_item_id]=$sql_po_row[csf('gmts_item_id')];//P.TYPE
				$ft_data_arr[$sql_po_row[csf('job_no')]][style_ref_no]=$sql_po_row[csf('style_ref_no')];//P.DESCRIP
				$po_arr[job_no][$sql_po_row[csf('job_no')]]="'".$sql_po_row[csf('job_no')]."'";
				$ft_data_arr[$sql_po_row[csf('job_no')]][buyer_name]=$sql_po_row[csf('buyer_name')];
				$ft_data_arr[$sql_po_row[csf('job_no')]][po_quantity]=$sql_po_row[csf('po_quantity')];
				//$po_arr[po_id][0]=0;
			}
			
			unset($sql_po_data);
			//echo "0**d";
			//print_r($ft_data_arr);
			//die;
			$sql_po="select a.id,b.id as bid,a.job_no,a.style_ref_no,a.gmts_item_id,d.costing_per,d.sew_smv,e.fab_knit_req_kg,a.buyer_name,b.po_quantity   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_mst d, wo_pre_cost_sum_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no  and b.id=c.po_break_down_id  $shipment_date   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by a.id,b.id";
			$sql_po_data=sql_select($sql_po);
			foreach($sql_po_data as $sql_po_row)
			{
			$po_arr[po_id][$sql_po_row[csf('id')]]=$sql_po_row[csf('id')];
			$po_arr[job_no][$sql_po_row[csf('job_no')]]="'".$sql_po_row[csf('job_no')]."'";
			$po_arr[costing_per][$sql_po_row[csf('job_no')]]=$sql_po_row[csf('costing_per')];
			
			$ft_data_arr[$sql_po_row[csf('job_no')]][job_no]=$sql_po_row[csf('job_no')];//P.CODE
			$ft_data_arr[$sql_po_row[csf('job_no')]][gmts_item_id]=$sql_po_row[csf('gmts_item_id')];//P.TYPE
			$ft_data_arr[$sql_po_row[csf('job_no')]][style_ref_no]=$sql_po_row[csf('style_ref_no')];//P.DESCRIP
			$fab_knit_req_kg=0;
			 
			$ft_data_arr[$sql_po_row[csf('job_no')]][buyer_name]=$sql_po_row[csf('buyer_name')];
			$ft_data_arr[$sql_po_row[csf('job_no')]][po_quantity]=$sql_po_row[csf('po_quantity')];
			$po_break[$sql_po_row[csf('bid')]]=$sql_po_row[csf('bid')];
			
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
			
		   $job=array_chunk($po_arr[job_no],999, true);
		   $job_cond_in="";
		   $ji=0;
		   foreach($job as $key=> $value)
		   {
			   if($ji==0)
			   {
				$job_cond_in="job_no in(".implode(",",$value).")"; 
				$job_cond_in_mst.=" a.job_no_mst in(".implode(",",$value).")"; 
			   }
			   else
			   {
					$job_cond_in.=" or job_no in(".implode(",",$value).")"; 
					$job_cond_in_mst.=" or a.job_no_mst in(".implode(",",$value).")"; 
			   }
			   $ji++;
		   }
		 
		   
		   $sql_fabric_prod=sql_select("select min(id) as id,job_no  from wo_pre_cost_fabric_cost_dtls where $job_cond_in and fabric_source=1 and avg_cons>0  and status_active=1 and is_deleted=0  group by job_no");
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
		 
		
		  //=================================Item wise Array Srart=====================================
		   $arr_itemsmv=array();
		   $sql_itemsmv=sql_select("select job_no,gmts_item_id,set_item_ratio,smv_pcs_precost,smv_set_precost,smv_pcs,embelishment  from wo_po_details_mas_set_details where $job_cond_in");
		   foreach($sql_itemsmv as $row_itemsmv)
		   {
				$arr_itemsmv[$row_itemsmv[csf('job_no')]][$row_itemsmv[csf('gmts_item_id')]]['smv']=$row_itemsmv[csf('smv_pcs')]; 
				$arr_itemsmv[$row_itemsmv[csf('job_no')]][$row_itemsmv[csf('gmts_item_id')]]['emb']=$row_itemsmv[csf('embelishment')];  
		   }
		   $array_fabric_cons_item=array();
		   $sql_fabric_cons_item=sql_select("select job_no,item_number_id,sum(avg_cons) as  avg_cons	  from wo_pre_cost_fabric_cost_dtls where $job_cond_in  and status_active=1 and is_deleted=0  group by job_no,item_number_id");
		   foreach($sql_fabric_cons_item as $row_fabric_cons_item)
		   {
			   $costingper=$po_arr[costing_per][$row_fabric_cons_item[csf('job_no')]];
			   $fab_knit_req_kg=0;
			   if($costingper==1)
				{
					$fab_knit_req_kg=$row_fabric_cons_item[csf('avg_cons')]/12;
				}
				if($costingper==2)
				{
					$fab_knit_req_kg=$row_fabric_cons_item[csf('avg_cons')];
				}
				if($costingper==3)
				{
					$fab_knit_req_kg=$row_fabric_cons_item[csf('avg_cons')]/24;
				}
				if($costingper==4)
				{
					$fab_knit_req_kg=$row_fabric_cons_item[csf('avg_cons')]/36;
				}
				if($costingper==5)
				{
					$fab_knit_req_kg=$row_fabric_cons_item[csf('avg_cons')]/48;
				}
			    $array_fabric_cons_item[$row_fabric_cons_item[csf('job_no')]][$row_fabric_cons_item[csf('item_number_id')]]=number_format($fab_knit_req_kg,3);   
		   }
		   
		   $array_fabric_prod_item=array();
		   $sql_fabric_prod_item=sql_select("select min(id) as id,job_no,item_number_id  from wo_pre_cost_fabric_cost_dtls where $job_cond_in and fabric_source=1 and avg_cons>0  and status_active=1 and is_deleted=0  group by job_no,item_number_id");
		   foreach($sql_fabric_prod_item as $row_fabric_prod_item)
		   {
			$array_fabric_prod_item[$row_fabric_prod_item[csf('job_no')]][$row_fabric_prod_item[csf('item_number_id')]]=1;   
		   }
		   //=================================Item wise Array End=====================================
		
		// Products file
		$txt="";
		$file_name="frfiles/PRODUCTS.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
	 	
		$txt .=str_pad("P.CODE",0," ")."\t".str_pad("P.TYPE",0," ")."\t".str_pad("P.DESCRIP",0," ")."\t".str_pad("P^CF:5",0," ")."\t".str_pad("P^WC:5",0," ")."\t".str_pad("P^WC:10",0," ")."\t".str_pad("P^WC:15",0," ")."\t".str_pad("P^WC:20",0," ")."\t".str_pad("P^WC:25",0," ")."\t".str_pad("P^WC:30",0," ")."\t".str_pad("P^WC:35",0," ")."\t".str_pad("P^WC:140",0," ")."\t".str_pad("P^WC:145",0," ")."\t".str_pad("P^WC:150",0," ")."\t".str_pad("P^WC:160",0," ")."\t".str_pad("P^WC:165",0," ")."\t".str_pad("P.UDFabrication",0," ")."\t".str_pad("P.UDGSM",0," ")."\t".str_pad("P.UDYarn Count",0," ")."\t".str_pad("P.UDStyle",0," ")."\r\n";
		
		foreach($ft_data_arr as $rows)
		{
			$gitem=explode(",",$rows[gmts_item_id]);
			
			$dt="";
			$style_ref_arr[$rows[job_no]]=$rows[style_ref_no];
			$job_ref_arr[$rows[job_no]]=$rows[job_no];
			//$job_ref_arr[$rows[job_no]]=$rows[job_no];
			
			if(count($gitem)>1)
			{
				$item_array[$rows[job_no]]=1;
				
				foreach($gitem as $id)
				{
					//$new_job_no[$rows[job_no]]=$rows[job_no]
					//if($dt=="") $dt=$fr_product_type[$id]; else $dt .=",".$fr_product_type[$id];
					//$array_fabric_cons_item[$row_fabric_cons_item[csf('job_no')]][$row_fabric_cons_item[csf('item_number_id')]]
					$img_prod[$rows[job_no]][]=$rows[job_no]."::".$fr_product_type[$id];
					
					if(trim($rows[job_no])!="") $txt .=str_pad($rows[job_no]."::".$fr_product_type[$id],0," ")."\t".str_pad($fr_product_type[$id],0," ")."\t".str_pad($rows[style_ref_no],0," ")."\t".str_pad($array_fabric_cons_item[$rows[job_no]][$id],0," ")."\t".str_pad($array_fabric_prod_item[$rows[job_no]][$id] == '' ? 0 :$array_fabric_prod_item[$rows[job_no]][$id],0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],0," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],0," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],0," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($arr_itemsmv[$rows[job_no]][$id]['smv'] == '' ? 0 :$arr_itemsmv[$rows[job_no]][$id]['smv'],0," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],0," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],0," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],0," ")."\t".str_pad("1",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\r\n";
				}
			}
			else
			{
				$img_prod[$rows[job_no]][]=$rows[job_no];
				if(trim($rows[job_no])!="") $txt .=str_pad($rows[job_no],0," ")."\t".str_pad($fr_product_type[$rows[gmts_item_id]],0," ")."\t".str_pad($rows[style_ref_no],0," ")."\t".str_pad($array_fabric_cons_item[$rows[job_no]][$rows[gmts_item_id]],0," ")."\t".str_pad($array_fabric_prod_item[$rows[job_no]][$rows[gmts_item_id]] == '' ? 0 :$array_fabric_prod_item[$rows[job_no]][$rows[gmts_item_id]],0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],0," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],0," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],0," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'] == '' ? 0 :$arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'],0," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],0," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],0," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],0," ")."\t".str_pad("1",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\r\n";
			}
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		
		// Orders file
		$job_no_list_arr=array_chunk(array_unique(explode(",",$po_break)),999);
		
		if($db_type==0)
		{
			$sewing_qnty=sql_select("SELECT po_break_down_id,country_id,sum(production_quantity) AS production_quantity,item_number_id 	  from pro_garments_production_mst where production_type ='5' and is_deleted=0 and status_active=1 and po_break_down_id in (".implode(",",$po_break).") group by po_break_down_id,item_number_id,country_id ");
		}
		else
		{
			
	
			$sql = "SELECT po_break_down_id,country_id,sum(production_quantity) AS production_quantity,item_number_id 	  from pro_garments_production_mst where production_type ='5' and is_deleted=0 and status_active=1 and  ";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .="  ( po_break_down_id in(".implode(',',$job_no_process).")"; else  $sql .=" or po_break_down_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=") group by po_break_down_id,item_number_id,country_id";
			$sewing_qnty=sql_select($sql);
		}
		 foreach($sewing_qnty as $row_sew)
		{
			$sew_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('country_id')]][sewing_qnty]=$row_sew[csf('production_quantity')];
		}
	 	
		$tna_template=array();
		
		
		if($db_type==0)
		{
			$sql=sql_select ( "select a.template_id,a.po_number_id,a.job_no, a.actual_start_date, a.actual_finish_date, task_number from tna_process_mst a where po_number_id in (".implode(",",$po_break).")" ); 
		}
		else
		{
			
	
			$sql = "select a.template_id,a.po_number_id,a.job_no, a.actual_start_date, a.actual_finish_date, task_number from tna_process_mst a where ";
			$p=1;
			foreach($job_no_list_arr as $job_no_process)
			{
				if($p==1) $sql .="  ( po_number_id in(".implode(',',$job_no_process).")"; else  $sql .=" or po_number_id in(".implode(',',$job_no_process).")";
				
				$p++;
			}
			$sql .=")";
			$sql=sql_select($sql);
		}
		
		 foreach($sql as $name)
		 {
			 $tna_template[$name[csf('po_number_id')]]=$name[csf('template_id')];
		 }
		 $template_list=return_library_array("select task_template_id,lead_time from  tna_task_template_details group by task_template_id","task_template_id","lead_time");
		
		echo "SELECT group_concat(b.id) as id, b.item_number_id,a.job_no_mst,a.po_number, b.country_ship_date,sum(b.plan_cut_qnty) as plan_cut_qnty,sum(order_quantity) as order_quantity, a.is_confirmed, min(b.shiping_status) as shiping_status,country_id,b.po_break_down_id, b.color_number_id,b.is_deleted,b.cutup FROM wo_po_break_down a, wo_po_color_size_breakdown b WHERE $job_cond_in_mst  and  a.id=b.po_break_down_id and b.is_deleted=0 and b.status_active=1 
group by b.po_break_down_id,b.item_number_id,b.color_number_id,b.country_ship_date order by b.country_ship_date,b.po_break_down_id,b.cutup" ; 
		if($db_type==0)
		{
			$sql=sql_select ( "SELECT group_concat(b.id) as id, b.item_number_id,a.job_no_mst,a.po_number, b.country_ship_date,sum(b.plan_cut_qnty) as plan_cut_qnty,sum(order_quantity) as order_quantity, a.is_confirmed, min(b.shiping_status) as shiping_status,country_id,b.po_break_down_id, b.color_number_id,b.is_deleted,b.cutup FROM wo_po_break_down a, wo_po_color_size_breakdown b WHERE $job_cond_in_mst  and  a.id=b.po_break_down_id and b.is_deleted=0 and b.status_active=1 
group by b.po_break_down_id,b.item_number_id,b.color_number_id,b.country_ship_date order by b.country_ship_date,b.po_break_down_id,b.cutup" ); 
		}
		else
		{
			$sql=sql_select ( "SELECT LISTAGG(b.id, ', ') WITHIN GROUP (ORDER BY b.id)  id, b.item_number_id,a.job_no_mst,a.po_number, b.country_ship_date,sum(b.plan_cut_qnty) as plan_cut_qnty,sum(order_quantity) as order_quantity, a.is_confirmed, min(b.shiping_status) as shiping_status,country_id,b.po_break_down_id, b.color_number_id,b.is_deleted,b.cutup FROM wo_po_break_down a, wo_po_color_size_breakdown b WHERE $job_cond_in_mst  and  a.id=b.po_break_down_id and b.is_deleted=0 and b.status_active=1 
group by b.po_break_down_id,b.item_number_id,b.color_number_id,b.country_ship_date order by b.country_ship_date,b.po_break_down_id,b.cutup" ); 
		}
		
		$file_name="frfiles/ORDERS.TXT";
		$myfile = fopen( $file_name, "w" ) or die("Unable to open file!");
	  	
		//HnM 28
		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.PROD",0," ")."\t".str_pad("O.CUST",0," ")."\t".str_pad("O^DD:1",0," ")."\t".str_pad("O^DQ:1",0," ")."\t".str_pad("O.DESCRIP",0," ")."\t".str_pad("O.STATUS",0," ")."\t".str_pad("O.COMPLETE",0," ")."\t".str_pad("O.CONTRACT_QTY",0," ")."\t".str_pad("O.EVBASE",0," ")."\t".str_pad("O.UDJob No",0," ")."\t".str_pad("O.UDOPD",0," ")."\t".str_pad("O.UDOrder Code",0," ")."\t".str_pad("O.UDColour",0," ")."\t".str_pad("O.UDOrder",0," ")."\t".str_pad("O.TIME",0," ")."\r\n";
		foreach($sql as $name)
		{
			if($name[csf('is_confirmed')]==1) $str="F"; else $str="P";
			//echo $name[csf('po_break_down_id')]."==";
			if($name[csf('shiping_status')]==3) $ssts="1";
			//else if($name[csf('shiping_status')]==3) $ssts="1";
			else $ssts="0"; //Sewing Out
			
			if( $item_array[$name[csf("job_no_mst")]]==1) $str_item="::".$fr_product_type[$name[csf('item_number_id')]]; else $str_item=""; 
			
			if($name[csf('cutup')]==0)
			{
				 $str_po=$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item;
				 $str_po_cut="";
			}
			else 
			{
				$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]+=1;
				
				$str_po=$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."::".$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]."".$str_item;  
				$str_po_cut=$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]."::"; 
			}
			$tna_po_id[ $name[csf('po_break_down_id')] ]=$str_po;
			$nid=explode(",",$name[csf('id')]);
			foreach($nid as $vid)
			{
				$newid_ar[$vid]=$str_po;
			}
			
			if($dtls_id=="") $dtls_id=$name[csf('id')]; else $dtls_id .=",".$name[csf('id')];
			
			$sew_qty_arr[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."::".$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]."::".$str][col_size_id]=$name[csf('id')];
			$sew_qty_arr[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."::".$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]."::".$str][order_qty]=$name[csf('order_quantity')];
			
			//if( $sew_qty_arr[$name[csf('po_break_down_id')]][$name[csf('item_number_id')]][$name[csf('country_id')]][sewing_qnty]>=$name[csf('order_quantity')]) $ssts=1;
			
			
			if( $name[csf('is_deleted')]==1 ) { $str="X";  } //$ssts=0;
			
			$str_po_list[$name[csf('po_break_down_id')]][$str_po]=$str_po;	
						
			$txt .=str_pad($str_po,0," ")."\t".str_pad($name[csf('job_no_mst')]."".$str_item,0," ")."\t".str_pad($buyer_name_array[$ft_data_arr[$name[csf('job_no_mst')]][buyer_name]],0," ")."\t".str_pad(date("d/m/Y",strtotime($name[csf('country_ship_date')])),0," ")."\t".str_pad($name[csf('plan_cut_qnty')],0," ")."\t".str_pad($style_ref_arr[$name[csf('job_no_mst')]],0," ")."\t".str_pad($str,0," ")."\t".str_pad($ssts,0," ")."\t".str_pad($name[csf('order_quantity')],0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($name[csf('po_number')],0," ")."\t".str_pad($color_lib[$name[csf('color_number_id')]],0," ")."\t".str_pad($name[csf('po_number')]."::".$color_lib[$name[csf('color_number_id')]]."::". $str_po_cut .$fr_product_type[$name[csf('item_number_id')]],0," ")."\t".$buyer_name_array[$ft_data_arr[$name[csf('job_no_mst')]][buyer_name]]." ".$template_list[$tna_template[$name[csf('po_break_down_id')]]]."\r\n"; 
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		//echo "SUUUU";
		//print_r($tna_po_id);
		// Production file
		$file_name="frfiles/UPDNORM.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
	  	
		$operation_arra=array(1=>"10",2=>"15",3=>"20",4=>"35",5=>"140",6=>"finish_input",7=>"iron_output",8=>"160",9=>"cutting delivery");
	  	//U.DATE	U.GROUP_EXTERNAL_ID	U.LINE_EXTERNAL_ID	U.QTY	U.OPN_COMPLETE
		/*005 Fab Delivery	5
140 Sewing Output	140
010 Cutting	10
025 Deliver to Emb.	25
030 Receive from Emb.	30
015 Deliver to Print	15
020 Receive from Print	20
145 Deliver to Wash/Emb	145
150 Receive from Wash/Emb.	150
160 Poly	160
165 Final Inspection	165
035 Sewing Input	35
*/
		$txt .=str_pad("U.ORDER",0," ")."\t".str_pad("U.DATE",0," ")."\t".str_pad("U.OPERATION",0," ")."\t".str_pad("U.GROUP_EXTERNAL_ID",0," ")."\t".str_pad("U.LINE_EXTERNAL_ID",0," ")."\t".str_pad("U.QTY",0," ")."\t".str_pad("U.OPN_COMPLETE",0," ")."\r\n";
		
		$sewing_qnty=sql_select( "SELECT po_break_down_id,country_id,sum(production_qnty) AS production_quantity,item_number_id,floor_id,sewing_line,color_size_break_down_id,a.production_type,max(a.production_date) as production_date	  from pro_garments_production_mst a, pro_garments_production_dtls b  where a.production_type in (1,2,3,4,5,8) and a.id=b.mst_id  and a.is_deleted=0 and a.status_active=1 and po_break_down_id in (".implode(",",$po_break).") group by color_size_break_down_id,a.production_type,floor_id,sewing_line order by  color_size_break_down_id,production_date asc "); 
		
		
		//production data,
		foreach($sewing_qnty as $row_sew)
		{
			//$ssts=1;
			//if($sew_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('country_id')]][order_qty]>$row_sew[csf('production_quantity')]) $ssts=0;
			
			if($operation_arra[$row_sew[csf("production_type")]]==35 )
			{
				$row_sew[csf("floor_id")]=0;
				$row_sew[csf("sewing_line")]=0;
			}
			$production_qty[$newid_ar[$row_sew[csf("color_size_break_down_id")]]][$row_sew[csf("production_type")]][$row_sew[csf("floor_id")]][$row_sew[csf("sewing_line")]][qnty]+=$row_sew[csf("production_quantity")];
			
			$production_qty[$newid_ar[$row_sew[csf("color_size_break_down_id")]]][$row_sew[csf("production_type")]][$row_sew[csf("floor_id")]][$row_sew[csf("sewing_line")]][pdate]=$row_sew[csf("production_date")]; 
		}
		// echo "0**";
		// print_r($production_qty); die;
		foreach($production_qty as $oid=>$prodctn)
		{
			foreach($prodctn as $prod_typ=>$prodflr)
			{
				foreach($prodflr as $flor=>$lndata)
				{
					foreach($lndata as $line=>$sdata)
					{
						//foreach($sdata as $sdate=>$data)
						//{
							$sdate=date("d/m/Y",strtotime($sdata[pdate]));
							if($line==0) $line=""; else $line=(int)$line_name_res[$line]; 
							$flor=$floor_name_res[$line];
							if($flor==0) $flor="";
							
							if(trim($oid)!='')
								$txt .=str_pad($oid,0," ")."\t".str_pad($sdate,0," ")."\t".str_pad($operation_arra[$prod_typ],0," ")."\t".str_pad($group_unit_array[$flor],0," ")."\t".str_pad($line_array[$line],0," ")."\t".str_pad($sdata[qnty],0," ")."\t".str_pad($ssts,0," ")."\r\n";
							 
						//}
					}
				}
			}	
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		
		//Attendance SMV
		
		 $tpd_data_arr=sql_select( "select company_id,location_id,floor_id,line_marge,line_number,b.id, a.pr_date, sum(a.target_per_hour*a.working_hour) tpd, sum(a.man_power*a.working_hour) tsmv from prod_resource_dtls a, prod_resource_mst b where b.id=a.mst_id   and a.is_deleted=0 and b.is_deleted=0 group by floor_id,line_number,a.pr_date");
		$file_name="frfiles/ATTMINS.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .="A.DATE\tA.GROUP\tA.ROW\tA.MINUTES\r\n";
		
        foreach($tpd_data_arr as $row)
        {
			//$production_date=date("Y-m-d", strtotime($row[csf('pr_date')])); 
           // $tpdArr[$production_date]+=$row[csf('tpd')];
			//$tsmvArr[$row[csf('floor_id')]][$row[csf('line_number')]][$production_date]+=$row[csf('tsmv')];
			$txt .=date("d/m/Y",strtotime($row[csf('pr_date')]))."\t".$row[csf('floor_id')]."\t".$row[csf('line_number')]."\t".($row[csf('tsmv')]*60)."\r\n";
        }
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt=""; 
		
		//Image Uploads
		$sql=sql_select ( "select id,master_tble_id,image_location from common_photo_library where is_deleted=0 and file_type=1 and form_name='knit_order_entry' and master_tble_id in ('".implode("','",$job_ref_arr)."')" );
		$file_name="frfiles/IMGATTACH.txt";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .="IMG.CODE\tIMG.FILENAME\tIMG.NAME\tIMG.FILEPATH\tIMG.DEFAULT\r\n";
		
		$zipimg = new ZipArchive();			// Load zip library	
		$filenames = str_replace(".sql",".zip",'frfiles/ImgFolders.sql');			// Zip name
		if($zipimg->open($filenames, ZIPARCHIVE::CREATE)!==TRUE)
		{		// Opening zip file to load files
			$error .=  "* Sorry ZIP creation failed at this time<br/>"; echo $error;
		}
		
		foreach($sql as $rows)
		{
			$name=explode("/",$rows[csf("image_location")]);
			foreach( $img_prod[$rows[csf("master_tble_id")]] as $job  )
			{
				$txt .=$job."\t".$name[1]."\t".str_replace(".jpg","",$name[1])."\t".$name[1]."\t1\r\n";
			}
			 //echo "0**../../../".$rows[csf("image_location")]; 
			$zipimg->addFile("../../../".$rows[csf("image_location")]);
		}
		$zipimg->close();
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		//Image Uploads
		
		// TNA file
		$sql=sql_select ( "select a.po_number_id,a.job_no, a.actual_start_date, a.actual_finish_date, task_number from tna_process_mst a where po_number_id in (".implode(",",$po_break).") order by po_number_id,task_number" ); 
		
		$file_name="frfiles/EVENTS.TXT";
		$myfile = fopen( $file_name, "w" ) or die("Unable to open file!");
	 	
		$txt .=str_pad("E.CODE",0," ")."\t".str_pad("E.TYPE",0," ")."\t".str_pad("E.EVENT",0," ")."\t".str_pad("E.AD",0," ")."\t".str_pad("E.DONE",0," ")."\t".str_pad("E.SKIP",0," ")."\t".str_pad("E.NOTE",0," ")."\r\n";
		foreach($sql as $name)
		{
			if( $name[csf('actual_finish_date')]=="0000-00-00")  $name[csf('actual_finish_date')]=""; else   $name[csf('actual_finish_date')]=date("d/m/Y",strtotime($name[csf('actual_finish_date')]));
			if( $name[csf('actual_start_date')]=="0000-00-00")  $name[csf('actual_start_date')]=""; else   $name[csf('actual_start_date')]=date("d/m/Y",strtotime($name[csf('actual_start_date')]));
			
			$tna_po_name[$name[csf('po_number_id')]]=$name[csf('po_number_id')];
			$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['actual_finish_date']=$name[csf('actual_finish_date')];
			$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['job_no_mst']=$name[csf('job_no')];
			//$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['actual_finish_date']=$name[csf('actual_finish_date')];
			//$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['actual_finish_date']=$name[csf('actual_finish_date')];
		}
		unset( $sql );
		
		foreach($tna_po_name as $poid )
		{
			
			foreach( $str_po_list[$poid] as $order=>$no ) //$str_po_list[$name[csf('po_break_down_id')]][$str_po]=$str_po;	
			{
				
				foreach($tna_po_task[$poid] as $task=>$names)
				{
					// print_r($names['job_no_mst']);
					if( $mapped_tna_task[$task]=='' ) $mapped_tna_task[$task]=0;
					if( $mapped_tna_task[$task]!=0 )
						$txt .=str_pad($order,0," ")."\tO\t".str_pad($mapped_tna_task[$task],0," ")."\t\t".str_pad($names['actual_finish_date'],0," ")."\t0\t".str_pad($style_ref_arr[$names['job_no_mst']],0," ")."\r\n";
				}
			} //$tna_po_id[$name[csf('po_number_id')]]
		}
		fwrite( $myfile, $txt );
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
		
		if($db_type==0)
		{
			$shipment_date="and c.country_ship_date >= '2014-11-01'";
		}
		if($db_type==2)
		{
			$shipment_date="and c.country_ship_date >= '01-Oct-2014'";
		}
		$shiping_status="and c.shiping_status !=3";
		
		$po_arr=array();
		$ft_data_arr=array();
		
		$sql_po="select a.id,a.job_no,a.style_ref_no,a.gmts_item_id from wo_po_details_master a, wo_po_break_down b  where a.job_no=b.job_no_mst  and b.shipment_date >= '2014-11-01' and b.is_confirmed=2   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  order by a.id,b.id";
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
		$sql=sql_select ( "select id,master_tble_id,image_location from common_photo_library where is_deleted=0 and file_type=1 and form_name='knit_order_entry' " ); // and master_tble_id in ('".implode("','",$job_ref_arr)."')
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
			$name=explode("/",$rows[csf("image_location")]);
			foreach( $img_prod[$rows[csf("master_tble_id")]] as $job  )
			{
				$txt .=$job."\t".$name[1]."\t".str_replace(".jpg","",$name[1])."\t".$name[1]."\t1\r\n";
		 		
			}

			$zipimg->addFile("../../../".$rows[csf("image_location")]);
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