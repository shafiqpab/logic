<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 
/*$fr_product_type=array(1=>"LS TEE",2=>"SS TEE",3=>"POLO",4=>"POLO",5=>"TANK TOP",6=>"SS TEE",7=>"Hoody T-Shirt",8=>"Y-NECK TEE",9=>"VEST",10=>"SS TEE",11=>"LS TEE",12=>"Scarf",14=>"JACKET",15=>"JACKET",16=>"DRESS",17=>"FROCK",18=>"DRESS",19=>"DRESS",20=>"LONG PANT",21=>"SHORT PANT",22=>"LONG PANT",23=>"LONG PANT",24=>"JUMP SUIT",25=>"JUMP SUIT",26=>"VEST",27=>"JUMP SUIT",28=>"LEGGING",29=>"LEGGING",30=>"Long Skirt",31=>"Jump Suit",32=>"Cap",33=>"Top Btm Set",34=>"Top Btm Set",35=>"LONG PANT",36=>"Bag",37=>"Bag",38=>"PANTY",39=>"JACKET",40=>"VEST",41=>"TANK TOP",42=>"SHORT PANT",43=>"SHORT PANT",44=>"SHORT PANT",45=>"SHORT PANT",46=>"PANTY",47=>"SHORT PANT",48=>"SHORT PANT",49=>"SHORT PANT",50=>"SHORT PANT",51=>"PANTY",52=>"PANTY",53=>"PANTY",54=>"PANTY",60=>"Socks",61=>"Socks",62=>"Socks",63=>"Socks",64=>"Socks",65=>"Socks",66=>"Socks",67=>"LONG PANT",68=>"Sports Ware",69=>"SS TEE",70=>"LONG PANT",71=>"SHORT PANT",72=>"Bra",73=>"SS TEE",74=>"DRESS");*/
$fr_product_type=$garments_item;
$group_unit_array=array( 10 => '',9 => 1,1 => 1,2 => 1,3 => 1,4 => 2,5 => 2,6 => 2,7 => 2,8 => '',18 => '',12 => '',11 => '',13 =>'',14 => '',15 =>'',16 => '');

/*$line_array=array( 67 => 1,7 => 10,15 => 11,16 => 12,59 => 13,18 => 14,19 => 15,20 => 16,21 => 17,22 => 18,23 => 19,61 => 2,68 => 20,8 => 21,9 => 22,17 => 23,11 => 24,10 => 25,12 => 26,13 => 27,14 => 28,45 => 29,62 => 3,46 => 30,47 => 31,48 => 32,49 => 33,50 => 34,51 => 35,63 => 36,37 => 37,36 => 38,34 => 39,1 => 4,35 => 40, 33 => 41,57 => 42,32 => 43,31 => 44,56 => 45,52 => 46,54 => 47,55 => 48,70 => 49,2 => 5,24 => 50,25 => 51,60 => 52,26 => 53,27 => 54,28 => 55,29 => 56,30 => 57,38 => 58,58 =>0,39 => 59,3 => 6,40 => 60);*/

$line_array=array(1=>"1",2=>"2",3=>"3",4=>"4",5=>"5",6=>"6",7=>"7",8=>"8",9=>"9",10=>"10",11=>"11",12=>"12",13=>"13",14=>"14",15=>"15",16=>"16",17=>"17",18=>"18",19=>"19",20=>"20",21=>"21",22=>"22",23=>"23",24=>"24",25=>"25",26=>"26",27=>"27",28=>"28",29=>"29",30=>"30",31=>"31",32=>"32",33=>"33",34=>"34",35=>"35",36=>"36",37=>"37",39=>"39",40=>"40",41=>"41",42=>"42",43=>"43",45=>"45",46=>"46",47=>"47",48=>"48",49=>"49",50=>"50",51=>"51",52=>"52",54=>"54",55=>"55",56=>"56",57=>"57",58=>"58",59=>"59",60=>"60",61=>"61",62=>"62",63=>"63",64=>"64",65=>"65",66=>"66",67=>"67",68=>"68",70=>"70",71=>"71",72=>"72",73=>"73",74=>"74",75=>"75",77=>"77",78=>"78",79=>"79",80=>"80",81=>"81",82=>"82",83=>"83",84=>"84",85=>"85",86=>"86",87=>"87",88=>"88",89=>"89",90=>"90",91=>"91",92=>"92",93=>"93",94=>"94",95=>"95",96=>"96",97=>"97",98=>"98",103=>"103",104=>"104",105=>"105",106=>"106",107=>"107",108=>"108",109=>"109",110=>"110",111=>"111",112=>"112",113=>"113",114=>"114",115=>"115",116=>"116",117=>"117",118=>"118",119=>"119",120=>"120",121=>"121",122=>"122",123=>"123",124=>"124",125=>"125",126=>"126",127=>"127",128=>"128",129=>"129",130=>"130",131=>"131",132=>"132",133=>"133",134=>"134",135=>"135",136=>"136",137=>"137",138=>"138",139=>"139",140=>"140",141=>"141",142=>"142",143=>"143",144=>"144",145=>"145",146=>"146",147=>"147"); 
 
 
$mapped_tna_task = array(1=> "5",2=> "",3=> "",4=> "",5=> "",7=> "25",8=> "40",9=> "15", 10=> "20",11=> "",12=> "45",13=> "30",14=> "75",15=> "80",16=> "85",17=> "90",19=> "55",20=> "60",21=> "",22=> "50",23=> "35",24=> "",25=> "",26=> "",27=> "",28=> "",29=> "",30=> "",31=> "10",32=> "95",33=> "",34=> "",40=> "",41=> "",45=> "",46=> "",47=> "",48=> "",50=> "",51=> "120",52=> "125",60=> "",61=> "",62=> "165",63=> "175",64=> "",70=> "100",71=> "105",72=> "",73=> "",74=> "185",80=> "65",81=> "",82=> "75",83=> "80",84=> "",85=> "",86=> "",87=> "",88=> "",89=> "215",90=> "225",100=> "",101=> "",110=> "",120=> "",121=> "",122=> '');

if ( $action=="save_update_delete" )
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	foreach (glob("frfiles/"."*.*") as $filename)
	{			
		@unlink($filename);
	}
	//echo $cbo_fr_integrtion=str_replace("'",$cbo_fr_integrtion); //die;
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
			if(trim( $received_date)=="") $received_date="01-Aug-2018"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
		// Products file Data
		//echo $received_date; die;
		 	if( $db_type==0 )
			{
				//$shipment_date="and c.country_ship_date >= '2014-11-01'";
				$shipment_date="and b.po_received_date >= '$received_date'";
			}
			if($db_type==2)
			{
				$shipment_date="and b.po_received_date >= '$received_date'";
			}
			$shiping_status="and c.shiping_status !=3";
			
			//letsgrowourfood.com
			
			$po_arr=array();
			$ft_data_arr=array();
			// echo $received_date; die;
			$sql_po="select a.id,a.job_no,a.style_ref_no,a.gmts_item_id,a.buyer_name,b.po_quantity from wo_po_details_master a, wo_po_break_down b  where a.job_no=b.job_no_mst  and b.po_received_date >= '$received_date'   and a.is_deleted=0 and a.status_active=1  order by a.id,b.id"; //and b.is_deleted=0 and b.status_active=1 
			$sql_po_data=sql_select($sql_po);  //and b.is_confirmed=2 
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
			
		   $job=array_chunk($po_arr[job_no],1000, true);
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
		   //print_r($job_cond_in); die;
		  
		   $sql_fabric_prod=sql_select("select min(id) as id,job_no from wo_pre_cost_fabric_cost_dtls where $job_cond_in and fabric_source=1 and avg_cons>0  and status_active=1 and is_deleted=0  group by job_no");
		   foreach($sql_fabric_prod as $row_fabric_prod)
		   {
			$ft_data_arr[fab_delivery][$row_fabric_prod[csf('job_no')]]=1;//P^WC:5   
		   }
		  
		  $sql_print_embroid=sql_select("select min(id) as id,job_no,emb_name, avg(cons_dzn_gmts) as cons_dzn_gmts  from wo_pre_cost_embe_cost_dtls where $job_cond_in and emb_name in(1,2,3) and cons_dzn_gmts>0  and status_active=1 and is_deleted=0 group by job_no,emb_name");
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
		   $sql_fabric_cons_item=sql_select("select job_no,item_number_id, sum(avg_finish_cons) as avg_cons from wo_pre_cost_fabric_cost_dtls where $job_cond_in  and status_active=1 and is_deleted=0 group by job_no,item_number_id");
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
			    $array_fabric_cons_item[$row_fabric_cons_item[csf('job_no')]][$row_fabric_cons_item[csf('item_number_id')]]=number_format($fab_knit_req_kg,4);   
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
					
					if(trim($rows[job_no])!="") $txt .=str_pad($rows[job_no]."::".$fr_product_type[$id],0," ")."\t".str_pad($fr_product_type[$id],0," ")."\t".str_pad($rows[style_ref_no],0," ")."\t".str_pad(number_format($array_fabric_cons_item[$rows[job_no]][$id], 4, '.', ''),0," ")."\t".str_pad($array_fabric_prod_item[$rows[job_no]][$id] == '' ? 0 :$array_fabric_prod_item[$rows[job_no]][$id],0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],0," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],0," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],0," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($arr_itemsmv[$rows[job_no]][$id]['smv'] == '' ? 0 :$arr_itemsmv[$rows[job_no]][$id]['smv'],0," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],0," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],0," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],0," ")."\t".str_pad("1",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\r\n";
				}
			}
			else
			{
				$img_prod[$rows[job_no]][]=$rows[job_no];
				if(trim($rows[job_no])!="") $txt .=str_pad($rows[job_no],0," ")."\t".str_pad($fr_product_type[$rows[gmts_item_id]],0," ")."\t".str_pad($rows[style_ref_no],0," ")."\t".str_pad( number_format( $array_fabric_cons_item[$rows[job_no]][$rows[gmts_item_id]], 4, '.', '' ),0," ")."\t".str_pad($array_fabric_prod_item[$rows[job_no]][$rows[gmts_item_id]] == '' ? 0 :$array_fabric_prod_item[$rows[job_no]][$rows[gmts_item_id]],0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],0," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],0," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],0," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'] == '' ? 0 :$arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'],0," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],0," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],0," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],0," ")."\t".str_pad("1",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\r\n";
			}
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		
		// Orders file
	
 		$sql="SELECT rtrim(xmlagg(xmlelement(e,c.id,',').extract('//text()') order by c.id).GetClobVal(),',') as id, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.gmts_item_id_prev, b.job_no_mst, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_number_prev, b.is_confirmed, b.is_deleted, c.po_break_down_id, c.item_number_id, c.country_ship_date, c.color_number_id, c.cutup, c.color_number_id_prev, min(c.shiping_status) as shiping_status, sum(c.plan_cut_qnty) as plan_cut_new, sum(c.order_quantity) as order_quantity
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c WHERE 1=1 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.plan_cut_qnty!=0 $shipment_date
		group by a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.gmts_item_id_prev, b.job_no_mst, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_number_prev, b.is_confirmed, b.is_deleted, c.po_break_down_id, c.item_number_id, c.country_ship_date, c.color_number_id, c.cutup, c.color_number_id_prev order by c.country_ship_date, c.po_break_down_id, c.cutup";
		//echo "0**".$sql;
		$sql_res=sql_select($sql);
		//print_r($sql_res);
		$orders_arr=array();
		foreach($sql_res as $name)
		{
			$old_str_po=''; $old_item_code=''; $changed=0; $old_po="";
			$job=$cdate="";
			$poId=0; $item_id=0; $color_id=0; $cut_up=0;
			
			$job=$name[csf('job_no_mst')];
			$cdate=$name[csf('country_ship_date')];
			$poId=$name[csf('po_break_down_id')]; 
			$item_id=$name[csf('item_number_id')]; 
			$color_id=$name[csf('color_number_id')]; 
			$cut_up=$name[csf('cutup')];
			
			$name[csf('id')]= $name[csf('id')]->load();
			$buyer_name=$buyer_name_array[$name[csf('buyer_name')]];
			
			if( $name[csf('shiping_status')]==3) $ssts="1"; else $ssts="0"; //Sewing Out
			
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['cid']=$name[csf('id')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['buyer']=$buyer_name;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['style_ref']=$name[csf('style_ref_no')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['uom']=$name[csf('order_uom')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['gmts_item_id']=$name[csf('gmts_item_id')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_number']=$name[csf('po_number')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_received_date']=$name[csf('po_received_date')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_confirmed']=$name[csf('is_confirmed')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_deleted']=$name[csf('is_deleted')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['shiping_status']=$ssts;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['plan_cut_new']+=$name[csf('plan_cut_new')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['order_quantity']+=$name[csf('order_quantity')];
			
			if( trim($name[csf('po_number_prev')])!='' && trim($name[csf('po_number_prev')])!=$name[csf('po_number')])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_number_prev']=$name[csf('po_number_prev')];
			}
			if( trim($name[csf('gmts_item_id_prev')])!='' && trim($name[csf('gmts_item_id_prev')])!=$name[csf('gmts_item_id')])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['gmts_item_id_prev']=$name[csf('gmts_item_id_prev')];
			}
			
			if( trim($name[csf('color_number_id_prev')])!='' && trim($name[csf('color_number_id_prev')])!=$name[csf('color_number_id')])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['color_number_id_prev']=$name[csf('color_number_id_prev')];
			}
        } 
		 
		$file_name="frfiles/ORDERS.TXT";
		$myfile = fopen( $file_name, "w" ) or die("Unable to open file!");
	  	
		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.OLDCODE",0," ")."\t".str_pad("O.PROD",0," ")."\t".str_pad("O.CUST",0," ")."\t".str_pad("O^DD:1",0," ")."\t".str_pad("O^DQ:1",0," ")."\t".str_pad("O.DESCRIP",0," ")."\t".str_pad("O.STATUS",0," ")."\t".str_pad("O.COMPLETE",0," ")."\t".str_pad("O.CONTRACT_QTY",0," ")."\t".str_pad("O.EVBASE",0," ")."\t".str_pad("O.UDJob No",0," ")."\t".str_pad("O.UDOPD",0," ")."\t".str_pad("O.UDOrder Code",0," ")."\t".str_pad("O.UDColour",0," ")."\t".str_pad("O.UDOrder",0," ")."\r\n";
		foreach($orders_arr as $jobno=>$job_data)
		{
			foreach($job_data as $poid=>$po_data)
			{
				foreach($po_data as $item_id=>$item_data)
				{
					foreach($item_data as $cdate=>$cdate_data)
					{
						foreach($cdate_data as $colorid=>$colorid_data)
						{
							foreach($colorid_data as $cut_up=>$exdata)
							{
								$old_str_po=''; $old_item_code=''; $changed=0; $old_po="";
								$gitem=explode(",",$exdata['gmts_item_id']);
								if( count($gitem)>1) $str_item="::".$fr_product_type[$item_id]; else $str_item=""; 
								
								if( $cut_up==0 && count($item_data)==1)
								{
									 $str_po=$exdata['po_number']."-".str_replace("FFL-","",$jobno)."::".$color_lib[$colorid]."".$str_item;
									 $str_po_cut="";
								}
								else 
								{
									$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]+=1;
									$str_po=$exdata['po_number']."-".str_replace("FFL-","",$jobno)."::".$color_lib[$colorid]."::".$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]."".$str_item;  
									$str_po_cut=$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]."::"; 
								}
								
								$pgitem=explode(",",trim($exdata['gmts_item_id_prev']));
								$inc=0;
								if(count($pgitem)>1)
								{
									foreach( $pgitem as $iid )
									{
										if( $iid!="")
										{
											$changed=1;
											$old_item_code="::".$fr_product_type[$iid];
										}
										$inc++;
									}
								}
								else
									$old_item_code=$str_item;//$old_item_code="::".$fr_product_type[$item_id];
								
								if( trim($exdata['po_number_prev'])!='' && trim($exdata['po_number_prev'])!=$exdata['po_number'])
								{
									$old_po=trim($exdata['po_number_prev']);
									$changed=1;
								}
								else
									$old_po= $exdata['po_number'];
									
								if( $exdata['color_number_id_prev']!='' && $exdata['color_number_id_prev']!=$colorid)
								{
									if( $exdata['color_number_id_prev']!=0)
									{
										$old_color= trim($exdata['color_number_id_prev']);
										$changed=1;
									}
								}
								else
									$old_color= $colorid;
									
								$old_str_po="";	
								if($changed==1)
								{
									if($cut_up==0 && count($item_data)==1)
									{
										$old_str_po=$old_po."-".str_replace("FFL-","",$jobno)."::".$color_lib[$old_color]."".$old_item_code;
										//$name[csf("job_no_mst")]."::".$old_po."::".$color_lib[$old_color]."::".$old_pub_ship."".$old_item_code; 
									}
									else
									{
										$old_str_po=$old_po."-".str_replace("FFL-","",$jobno)."::".$color_lib[$old_color]."::".$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]."".$old_item_code;  
									}
								}
								
								if($dtls_id=="") $dtls_id=$exdata['id']; else $dtls_id .=",".$exdata['id'];
								
								if( $exdata['is_confirmed']==1) $str="F"; else $str="P";
								if( $exdata['is_deleted']==1 ) { $str="X";  } 
								
								
								$txt .=str_pad($str_po,0," ")."\t".str_pad($old_str_po,0," ")."\t".str_pad($jobno."".$str_item,0," ")."\t".str_pad( $exdata['buyer'],0," ")."\t".str_pad(date("d/m/Y",strtotime($cdate)),0," ")."\t".str_pad($exdata['plan_cut_new'],0," ")."\t".str_pad($exdata['style_ref'],0," ")."\t".str_pad($str,0," ")."\t".str_pad($exdata['shiping_status'],0," ")."\t".str_pad($exdata['order_quantity'],0," ")."\t".str_pad( date("d/m/Y",strtotime($exdata['po_received_date'])),0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($exdata['po_number'],0," ")."\t".str_pad($color_lib[$colorid],0," ")."\t".str_pad($exdata['po_number']."::".$color_lib[$colorid]."::". $str_po_cut .$fr_product_type[$item_id],0," ")."\r\n";
								//$buyer_naem='';
								//$id_arr[] = $id;
							}
						}
					}
				}
			}
		}
		
		/*foreach($sql as $name)
		{
			//$data_array[$name[csf('job_no_mst')]][$name[csf('po_break_down_id')]][$name[csf('item_number_id')]][$name[csf('color_number_id')]]+=[$name[csf('plan_cut_new')]];
			
		//	[$name[csf('job_no_mst')]]
			
			// by b.job_no_mst,b.po_break_down_id,b.item_number_id,b.color_number_id,b.country_ship_date
			$name[csf('id')]= $name[csf('id')]->load();
			if( $name[csf('is_confirmed')]==1 ) $str="F"; else $str="P";
			//echo $name[csf('po_break_down_id')]."==";
			if( $name[csf('shiping_status')]==3 ) $ssts="1";
			//else if($name[csf('shiping_status')]==3) $ssts="1";
			else $ssts="0"; //Sewing Out
			
			if( $item_array[$name[csf("job_no_mst")]]==1) $str_item="::".$fr_product_type[$name[csf('item_number_id')]]; else $str_item=""; 
			
			if( $name[csf('cutup')]==0 )
			{
				 $str_po=$name[csf('po_number')]."-".str_replace("FFL-","",$name[csf('job_no_mst')])."::".$color_lib[$name[csf('color_number_id')]]."".$str_item;
				 $str_po_cut="";
			}
			else 
			{
				$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]+=1;
				
				$str_po=$name[csf('po_number')]."-".str_replace("FFL-","",$name[csf('job_no_mst')])."::".$color_lib[$name[csf('color_number_id')]]."::".$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]."".$str_item;  
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
			 
			$txt .=str_pad($str_po,0," ")."\t".str_pad($name[csf('job_no_mst')]."".$str_item,0," ")."\t".str_pad($buyer_name_array[$ft_data_arr[$name[csf('job_no_mst')]][buyer_name]],0," ")."\t".str_pad(date("d/m/Y",strtotime($name[csf('country_ship_date')])),0," ")."\t".str_pad($name[csf('plan_cut_new')],0," ")."\t".str_pad($style_ref_arr[$name[csf('job_no_mst')]],0," ")."\t".str_pad($str,0," ")."\t".str_pad($ssts,0," ")."\t".str_pad($name[csf('order_quantity')],0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($name[csf('po_number')],0," ")."\t".str_pad($color_lib[$name[csf('color_number_id')]],0," ")."\t".str_pad($name[csf('po_number')]."::".$color_lib[$name[csf('color_number_id')]]."::". $str_po_cut .$fr_product_type[$name[csf('item_number_id')]],0," ")."\r\n";
       
            $id_arr[] = $id;
        }*/
		
        $fr_comparison_field_array = '';
        $fr_comparison_data_array  = '';
         
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
		$txt="";
			if(trim( $received_date)=="") $received_date="01-Aug-2018"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
		// Products file Data
		//echo $received_date; die;
		 	if( $db_type==0 )
			{
				//$shipment_date="and c.country_ship_date >= '2014-11-01'";
				$shipment_date="and b.po_received_date >= '$received_date'";
			}
			if($db_type==2)
			{
				$shipment_date="and b.po_received_date >= '$received_date'";
			}
			$shiping_status="and c.shiping_status !=3";
			
			//letsgrowourfood.com
			
			$po_arr=array();
			$ft_data_arr=array();
			 
			$sql_po="select a.id,a.job_no,a.style_ref_no,a.gmts_item_id,a.buyer_name,b.po_quantity from wo_po_details_master a, wo_po_break_down b  where a.job_no=b.job_no_mst  and b.shipment_date >= '$received_date'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  order by a.id,b.id";
			$sql_po_data=sql_select($sql_po);// and b.is_confirmed=2 
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
			$sql_po="select a.id,b.id as bid,a.job_no,a.style_ref_no,a.gmts_item_id,d.costing_per,d.sew_smv,e.fab_knit_req_kg,a.buyer_name,b.po_quantity   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_mst d, wo_pre_cost_sum_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no  and b.id=c.po_break_down_id $shipment_date and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by a.id,b.id";
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
			
		   $job=array_chunk($po_arr[job_no],1000, true);
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
		   //print_r($job_cond_in); die;
		   
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
		   $sql_fabric_cons_item=sql_select("select job_no,item_number_id,sum(avg_finish_cons) as avg_cons from wo_pre_cost_fabric_cost_dtls where $job_cond_in  and status_active=1 and is_deleted=0 group by job_no,item_number_id");
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
			    $array_fabric_cons_item[$row_fabric_cons_item[csf('job_no')]][$row_fabric_cons_item[csf('item_number_id')]]=number_format($fab_knit_req_kg,4);   
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
					
					if(trim($rows[job_no])!="") $txt .=str_pad($rows[job_no]."::".$fr_product_type[$id],0," ")."\t".str_pad($fr_product_type[$id],0," ")."\t".str_pad($rows[style_ref_no],0," ")."\t".str_pad(number_format($array_fabric_cons_item[$rows[job_no]][$id], 4, '.', ''),0," ")."\t".str_pad($array_fabric_prod_item[$rows[job_no]][$id] == '' ? 0 :$array_fabric_prod_item[$rows[job_no]][$id],0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],0," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],0," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],0," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($arr_itemsmv[$rows[job_no]][$id]['smv'] == '' ? 0 :$arr_itemsmv[$rows[job_no]][$id]['smv'],0," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],0," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],0," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],0," ")."\t".str_pad("1",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\r\n";
				}
			}
			else
			{
				$img_prod[$rows[job_no]][]=$rows[job_no];
				if(trim($rows[job_no])!="") $txt .=str_pad($rows[job_no],0," ")."\t".str_pad($fr_product_type[$rows[gmts_item_id]],0," ")."\t".str_pad($rows[style_ref_no],0," ")."\t".str_pad( number_format( $array_fabric_cons_item[$rows[job_no]][$rows[gmts_item_id]], 4, '.', '' ),0," ")."\t".str_pad($array_fabric_prod_item[$rows[job_no]][$rows[gmts_item_id]] == '' ? 0 :$array_fabric_prod_item[$rows[job_no]][$rows[gmts_item_id]],0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],0," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],0," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],0," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'] == '' ? 0 :$arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'],0," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],0," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],0," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],0," ")."\t".str_pad("1",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\r\n";
			}
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";

		/*foreach($ft_data_arr as $rows)
		{
			$txt .=str_pad($rows[job_no],15," ")."\t".str_pad($rows[gmts_item_id],15," ")."\t".str_pad($rows[style_ref_no],25," ")."\t".str_pad($rows[fab_knit_req_kg],12," ")."\t".str_pad($rows[cutting] == '' ? 0 : $rows[cutting],12," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],12," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],12," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],12," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],12," ")."\t".str_pad($rows[sew_input] == '' ? 0 :$rows[sew_input],12," ")."\t".str_pad($rows[sew_output] == '' ? 0 :$rows[sew_output],12," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],12," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],12," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],12," ")."\t".str_pad($rows[shiped] == '' ? 0 :$rows[shiped],12," ")."\t\t\t\t\t\r\n";
		}*/
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	}
	else if ( $cbo_fr_integrtion==3)
	{
		$color_lib=return_library_array("select id,color_name from lib_color","id","color_name");
		$floor_name=return_library_array("select id,floor_name from  lib_prod_floor","id","floor_name");
		$line_name=return_library_array("select id,line_name from lib_sewing_line","id","line_name");
		$line_name_res=return_library_array("select id,line_number from prod_resource_mst","id","line_number");
		$floor_name_res=return_library_array("select id,floor_id from prod_resource_mst","id","floor_id");
		
		$sql=sql_select ( "select id,short_name from lib_buyer" );
 
		foreach($sql as $name)
		{
			//if(strlen($name)>6) $txt .=$name."\tEND\r\n"; else 
			$buyer_name_array[$name[csf("id")]]=$name[csf("short_name")];
			 
		}
		
		if(trim( $received_date)=="") $received_date="01-Aug-2018"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
			 
		if( $db_type==0 )
		{
			//$shipment_date="and c.country_ship_date >= '2014-11-01'";
			$shipment_date="and b.po_received_date >= '$received_date'";
		}
		if($db_type==2)
		{
			$shipment_date="and b.po_received_date >= '$received_date'";
		}
 
 		$sql=sql_select ( "SELECT rtrim(xmlagg(xmlelement(e,c.id,',').extract('//text()') order by c.id).GetClobVal(),',') as id, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.gmts_item_id_prev, b.job_no_mst, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_number_prev, b.is_confirmed, b.is_deleted, c.po_break_down_id, c.item_number_id, c.country_ship_date, c.color_number_id, c.cutup, c.color_number_id_prev, min(c.shiping_status) as shiping_status, sum(c.plan_cut_qnty) as plan_cut_new, sum(c.order_quantity) as order_quantity
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c WHERE 1=1 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.plan_cut_qnty!=0 $shipment_date
		group by a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.gmts_item_id_prev, b.job_no_mst, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_number_prev, b.is_confirmed, b.is_deleted, c.po_break_down_id, c.item_number_id, c.country_ship_date, c.color_number_id, c.cutup, c.color_number_id_prev order by c.country_ship_date, c.po_break_down_id, c.cutup"); //b.pub_shipment_date_prev, c.country_ship_date_prev,
		
		$file_name="frfiles/ORDERS.TXT";
		$myfile = fopen( $file_name, "w" ) or die("Unable to open file!");
	  	$txt="";
		
		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.OLDCODE",0," ")."\t".str_pad("O.PROD",0," ")."\t".str_pad("O.CUST",0," ")."\t".str_pad("O^DD:1",0," ")."\t".str_pad("O^DQ:1",0," ")."\t".str_pad("O.DESCRIP",0," ")."\t".str_pad("O.STATUS",0," ")."\t".str_pad("O.COMPLETE",0," ")."\t".str_pad("O.CONTRACT_QTY",0," ")."\t".str_pad("O.EVBASE",0," ")."\t".str_pad("O.UDJob No",0," ")."\t".str_pad("O.UDOPD",0," ")."\t".str_pad("O.UDOrder Code",0," ")."\t".str_pad("O.UDColour",0," ")."\t".str_pad("O.UDOrder",0," ")."\r\n";
		$orders_arr=array();
		foreach($sql as $name)
		{
			$old_str_po=''; $old_item_code=''; $changed=0; $old_po="";
			$job=$cdate="";
			$poId=0; $item_id=0; $color_id=0; $cut_up=0;
			
			$job=$name[csf('job_no_mst')];
			$cdate=$name[csf('country_ship_date')];
			$poId=$name[csf('po_break_down_id')]; 
			$item_id=$name[csf('item_number_id')]; 
			$color_id=$name[csf('color_number_id')]; 
			$cut_up=$name[csf('cutup')];
			
			$name[csf('id')]= $name[csf('id')]->load();
			$buyer_name=$buyer_name_array[$name[csf('buyer_name')]];
			
			if( $name[csf('shiping_status')]==3) $ssts="1";
			else $ssts="0"; //Sewing Out
			/*$gitem=explode(",",$name[csf('gmts_item_id')]);
			if( count($gitem)>1) $str_item="::".$fr_product_type[$name[csf('item_number_id')]]; else $str_item=""; */
			
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['cid']=$name[csf('id')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['buyer']=$buyer_name;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['style_ref']=$name[csf('style_ref_no')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['uom']=$name[csf('order_uom')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['gmts_item_id']=$name[csf('gmts_item_id')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_number']=$name[csf('po_number')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_received_date']=$name[csf('po_received_date')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_confirmed']=$name[csf('is_confirmed')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_deleted']=$name[csf('is_deleted')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['shiping_status']=$ssts;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['plan_cut_new']+=$name[csf('plan_cut_new')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['order_quantity']+=$name[csf('order_quantity')];
			
			if( trim($name[csf('po_number_prev')])!='' && trim($name[csf('po_number_prev')])!=$name[csf('po_number')])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_number_prev']=$name[csf('po_number_prev')];
			}
			if( trim($name[csf('gmts_item_id_prev')])!='' && trim($name[csf('gmts_item_id_prev')])!=$name[csf('gmts_item_id')])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['gmts_item_id_prev']=$name[csf('gmts_item_id_prev')];
			}
			
			if( trim($name[csf('color_number_id_prev')])!='' && trim($name[csf('color_number_id_prev')])!=$name[csf('color_number_id')])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['color_number_id_prev']=$name[csf('color_number_id_prev')];
			}
			
			
			/*if( $name[csf('cutup')]==0 )
			{
				 $str_po=$name[csf('po_number')]."-".str_replace("FFL-","",$name[csf('job_no_mst')])."::".$color_lib[$name[csf('color_number_id')]]."".$str_item;
				 $str_po_cut="";
			}
			else 
			{
				$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]+=1;
				
				$str_po=$name[csf('po_number')]."-".str_replace("FFL-","",$name[csf('job_no_mst')])."::".$color_lib[$name[csf('color_number_id')]]."::".$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]."".$str_item;  
				$str_po_cut=$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]."::"; 
			}
			
			$pgitem=explode(",",trim($name[csf('gmts_item_id_prev')]));
			$inc=0;
			if(count($pgitem)>1)
			{
				foreach( $pgitem as $iid )
				{
					if( $iid!="")
					{
						$changed=1;
						$old_item_code="::".$fr_product_type[$iid];
					}
					$inc++;
				}
			}
			else
				$old_item_code="::".$fr_product_type[$name[csf('item_number_id')]];
			
			if( $name[csf('po_number_prev')]!='' && trim($name[csf('po_number_prev')])!=$name[csf('po_number')])
			{
				$old_po=trim($name[csf('po_number_prev')]);
				$changed=1;
			}
			else
				$old_po= $name[csf('po_number')];
				
			if( $name[csf('color_number_id_prev')]!='' && $name[csf('color_number_id_prev')]!=$name[csf('color_number_id')])
			{
				$old_color= trim($name[csf('color_number_id_prev')]);
				$changed=1;
			}
			else
				$old_color= $name[csf('color_number_id')];
			
			if( $name[csf('pub_shipment_date_prev')]!='' && date("d-m-Y",strtotime($name[csf('pub_shipment_date_prev')]))!=date("d-m-Y",strtotime($name[csf('pub_shipment_date')])))
			{
				$old_pub_ship= date("ymd",strtotime($name[csf('pub_shipment_date_prev')]));
				$changed=1;
			}
			else
				$old_pub_ship= date("ymd",strtotime($name[csf('pub_shipment_date')]));
			
			if($changed==1)
			{
				if( $name[csf('cutup')]==0 )
				{
					$old_str_po=$old_po."-".str_replace("FFL-","",$name[csf('job_no_mst')])."::".$color_lib[$old_color]."".$old_item_code;
					//$name[csf("job_no_mst")]."::".$old_po."::".$color_lib[$old_color]."::".$old_pub_ship."".$old_item_code; 
				}
				else
				{
					$old_str_po=$old_po."-".str_replace("FFL-","",$name[csf('job_no_mst')])."::".$color_lib[$old_color]."::".$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$old_color]."".$str_item]."".$old_item_code;  
				}
			}
			
			if($dtls_id=="") $dtls_id=$name[csf('id')]; else $dtls_id .=",".$name[csf('id')];
			
			//$ssts=0;
			
			
			
			
			$txt .=str_pad($str_po,0," ")."\t".str_pad($old_str_po,0," ")."\t".str_pad($name[csf('job_no_mst')]."".$str_item,0," ")."\t".str_pad( $buyer_naem,0," ")."\t".str_pad(date("d/m/Y",strtotime($name[csf('country_ship_date')])),0," ")."\t".str_pad($name[csf('plan_cut_new')],0," ")."\t".str_pad($name[csf('style_ref_no')],0," ")."\t".str_pad($str,0," ")."\t".str_pad($ssts,0," ")."\t".str_pad($name[csf('order_quantity')],0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($name[csf('po_number')],0," ")."\t".str_pad($color_lib[$name[csf('color_number_id')]],0," ")."\t".str_pad($name[csf('po_number')]."::".$color_lib[$name[csf('color_number_id')]]."::". $str_po_cut .$fr_product_type[$name[csf('item_number_id')]],0," ")."\r\n";
       		$buyer_naem='';
            $id_arr[] = $id;*/
        }
		
		foreach($orders_arr as $jobno=>$job_data)
		{
			foreach($job_data as $poid=>$po_data)
			{
				foreach($po_data as $item_id=>$item_data)
				{
					foreach($item_data as $cdate=>$cdate_data)
					{
						foreach($cdate_data as $colorid=>$colorid_data)
						{
							foreach($colorid_data as $cut_up=>$exdata)
							{
								$old_str_po=''; $old_item_code=''; $changed=0; $old_po="";
								$gitem=explode(",",$exdata['gmts_item_id']);
								if( count($gitem)>1) $str_item="::".$fr_product_type[$item_id]; else $str_item=""; 
								
								if( $cut_up==0 && count($item_data)==1)
								{
									 $str_po=$exdata['po_number']."-".str_replace("FFL-","",$jobno)."::".$color_lib[$colorid]."".$str_item;
									 $str_po_cut="";
								}
								else 
								{
									$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]+=1;
									$str_po=$exdata['po_number']."-".str_replace("FFL-","",$jobno)."::".$color_lib[$colorid]."::".$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]."".$str_item;  
									$str_po_cut=$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]."::"; 
								}
								
								$pgitem=explode(",",trim($exdata['gmts_item_id_prev']));
								$inc=0;
								if(count($pgitem)>1)
								{
									foreach( $pgitem as $iid )
									{
										if( $iid!="")
										{
											$changed=1;
											$old_item_code="::".$fr_product_type[$iid];
										}
										$inc++;
									}
								}
								else
									$old_item_code=$str_item;//$old_item_code="::".$fr_product_type[$item_id];
								
								if( trim($exdata['po_number_prev'])!='' && trim($exdata['po_number_prev'])!=$exdata['po_number'])
								{
									$old_po=trim($exdata['po_number_prev']);
									$changed=1;
								}
								else
									$old_po= $exdata['po_number'];
									
								if( $exdata['color_number_id_prev']!='' && $exdata['color_number_id_prev']!=$colorid)
								{
									if($exdata['color_number_id_prev']!=0)
									{
										$old_color= trim($exdata['color_number_id_prev']);
										$changed=1;
									}
								}
								else
									$old_color= $colorid;
									
									
								if($changed==1)
								{
									if( $cut_up==0 && count($item_data)==1)
									{
										$old_str_po=$old_po."-".str_replace("FFL-","",$jobno)."::".$color_lib[$old_color]."".$old_item_code;
										//$name[csf("job_no_mst")]."::".$old_po."::".$color_lib[$old_color]."::".$old_pub_ship."".$old_item_code; 
									}
									else
									{
										$old_str_po=$old_po."-".str_replace("FFL-","",$jobno)."::".$color_lib[$old_color]."::".$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]."".$old_item_code;  
									}
								}
								
								if($dtls_id=="") $dtls_id=$exdata['id']; else $dtls_id .=",".$exdata['id'];
								
								if( $exdata['is_confirmed']==1) $str="F"; else $str="P";
								if( $exdata['is_deleted']==1 ) { $str="X";  } 
								
								
								$txt .=str_pad($str_po,0," ")."\t".str_pad($old_str_po,0," ")."\t".str_pad($jobno."".$str_item,0," ")."\t".str_pad( $exdata['buyer'],0," ")."\t".str_pad(date("d/m/Y",strtotime($cdate)),0," ")."\t".str_pad($exdata['plan_cut_new'],0," ")."\t".str_pad($exdata['style_ref'],0," ")."\t".str_pad($str,0," ")."\t".str_pad($exdata['shiping_status'],0," ")."\t".str_pad($exdata['order_quantity'],0," ")."\t".str_pad(date("d/m/Y",strtotime($exdata['po_received_date'])),0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($exdata['po_number'],0," ")."\t".str_pad($color_lib[$colorid],0," ")."\t".str_pad($exdata['po_number']."::".$color_lib[$colorid]."::". $str_po_cut .$fr_product_type[$item_id],0," ")."\r\n";
								//$buyer_naem='';
								//$id_arr[] = $id;
							}
						}
					}
				}
			}
		}
		//echo $txt;
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	}
	else if ( $cbo_fr_integrtion==4 )
	{
		if(trim( $received_date)=="") $received_date="01-Aug-2018"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
		$color_lib=return_library_array("select id, color_name from lib_color","id","color_name");
		if( $db_type==0 )
		{
			//$shipment_date="and c.country_ship_date >= '2014-11-01'";
			$shipment_date="and b.po_received_date >= '$received_date'";
		}
		if($db_type==2)
		{
			$shipment_date="and b.po_received_date >= '$received_date'";
		}
		//echo "select a.po_number_id, a.job_no, a.actual_start_date, a.actual_finish_date, a.task_number, b.po_number, c.style_ref_no, c.gmts_item_id, d.item_number_id, d.cutup, d.color_number_id from tna_process_mst a, wo_po_break_down b, wo_po_details_master c, wo_po_color_size_breakdown d where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and a.po_number_id=b.id and b.id=d.po_break_down_id $shipment_date order by a.po_number_id, a.task_number"; die;
		
		$sql=sql_select ( "select a.po_number_id, a.job_no, a.actual_start_date, a.actual_finish_date, a.task_number, b.po_number, c.style_ref_no, c.gmts_item_id, d.item_number_id, d.cutup, d.color_number_id from tna_process_mst a, wo_po_break_down b, wo_po_details_master c, wo_po_color_size_breakdown d where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and a.po_number_id=b.id and b.id=d.po_break_down_id $shipment_date order by a.po_number_id, a.task_number" ); 
		
		
		$file_name="frfiles/EVENTS.TXT";
		$myfile = fopen( $file_name, "w" ) or die("Unable to open file!");
	 	
		$txt .=str_pad("E.CODE",0," ")."\t".str_pad("E.TYPE",0," ")."\t".str_pad("E.EVENT",0," ")."\t".str_pad("E.AD",0," ")."\t".str_pad("E.DONE",0," ")."\t".str_pad("E.SKIP",0," ")."\t".str_pad("E.NOTE",0," ")."\r\n";
		foreach($sql as $name)
		{
			$gitem=explode(",",$name[csf('gmts_item_id')]);
			
			if( count($gitem)>1) $str_item="::".$fr_product_type[$name[csf('item_number_id')]]; else $str_item=""; 
			
			if( $name[csf('cutup')]==0 )
			{
				 $str_po=$name[csf('po_number')]."-".str_replace("FFL-","",$name[csf('job_no')])."::".$color_lib[$name[csf('color_number_id')]]."".$str_item;
				 $str_po_cut="";
			}
			else 
			{
				$cutoff[$name[csf('po_number_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]+=1;
				
				$str_po=$name[csf('po_number')]."-".str_replace("FFL-","",$name[csf('job_no')])."::".$color_lib[$name[csf('color_number_id')]]."::".$cutoff[$name[csf('po_break_down_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]."".$str_item;  
				$str_po_cut=$cutoff[$name[csf('po_number_id')]."::".$color_lib[$name[csf('color_number_id')]]."".$str_item]."::"; 
			}
			
			$str_po_list[$name[csf('po_number_id')]][$str_po]=$str_po;	
			
			if( $name[csf('actual_finish_date')]=="0000-00-00")  $name[csf('actual_finish_date')]=""; else   $name[csf('actual_finish_date')]=date("d/m/Y",strtotime($name[csf('actual_finish_date')]));
			if( $name[csf('actual_start_date')]=="0000-00-00")  $name[csf('actual_start_date')]=""; else   $name[csf('actual_start_date')]=date("d/m/Y",strtotime($name[csf('actual_start_date')]));
			
			$tna_po_name[$name[csf('po_number_id')]]=$name[csf('po_number_id')];
			$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['actual_finish_date']=$name[csf('actual_finish_date')];
			$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['job_no_mst']=$name[csf('job_no')];
			$tna_po_task[$name[csf('po_number_id')]][$name[csf('task_number')]]['style_ref_no']=$name[csf('style_ref_no')];
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
						$txt .=str_pad($order,0," ")."\tO\t".str_pad($mapped_tna_task[$task],0," ")."\t\t".str_pad($names['actual_finish_date'],0," ")."\t0\t".str_pad($names['style_ref_no'],0," ")."\r\n";
				}
			} //$tna_po_id[$name[csf('po_number_id')]]
		}
		fwrite( $myfile, $txt );
		fclose($myfile); 
		$txt="";
	}
	else if ( $cbo_fr_integrtion==5 )
	{
		if(trim( $received_date)=="") $received_date="01-Aug-2018"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
		$shipment_date="and b.po_received_date >= '$received_date'";
		
		$color_lib=return_library_array("select id,color_name from lib_color","id","color_name");
		$floor_name=return_library_array("select id,floor_name from  lib_prod_floor","id","floor_name");
		$line_name=return_library_array("select id,line_name from lib_sewing_line","id","line_name");
		$line_name_res=return_library_array("select id,line_number from prod_resource_mst","id","line_number");
		$floor_name_res=return_library_array("select id,floor_id from prod_resource_mst","id","floor_id");
		//echo '0**';
		$sql_po="SELECT rtrim(xmlagg(xmlelement(e,c.id,',').extract('//text()') order by c.id).GetClobVal(),',') as id, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.gmts_item_id_prev, b.job_no_mst, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_number_prev, b.is_confirmed, b.is_deleted, c.po_break_down_id, c.item_number_id, c.country_ship_date, c.color_number_id, c.cutup, c.color_number_id_prev, min(c.shiping_status) as shiping_status, sum(c.plan_cut_qnty) as plan_cut_new, sum(c.order_quantity) as order_quantity
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c WHERE 1=1 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.plan_cut_qnty!=0 $shipment_date
		group by a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.gmts_item_id_prev, b.job_no_mst, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_number_prev, b.is_confirmed, b.is_deleted, c.po_break_down_id, c.item_number_id, c.country_ship_date, c.color_number_id, c.cutup, c.color_number_id_prev order by c.country_ship_date, c.po_break_down_id, c.cutup";
		
		/*$sql_po="select a.id, a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, a.gmts_item_id, b.id as bid, b.po_number, b.po_quantity, b.po_received_date, d.costing_per, d.sew_smv, e.fab_knit_req_kg, 
		rtrim(xmlagg(xmlelement(e,c.id,',').extract('//text()') order by c.id).GetClobVal(),',') as cid, c.item_number_id, min(c.shiping_status) as shiping_status, c.country_ship_date, c.color_number_id, c.cutup, sum(c.order_quantity) as cqty from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_mst d, wo_pre_cost_sum_dtls e where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and b.shiping_status in (1,2) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $shipment_date and a.job_no='FFL-19-02133'
		group by  a.id, b.id, a.job_no, a.style_ref_no, a.order_uom, a.gmts_item_id, b.po_number, d.costing_per, d.sew_smv, e.fab_knit_req_kg, a.buyer_name, b.po_quantity, c.item_number_id, c.country_ship_date, c.color_number_id, c.cutup order by a.id, b.id, c.country_ship_date";*/
		//echo "10**".$sql_po;
		$new_indx_powise=array();
		$sql_po_data=sql_select($sql_po); $tot_rows=0; $poIds=''; $i=0; $k=1; $po_req_qty_array=array(); $req_qty_array=array();
		foreach($sql_po_data as $sql_po_row)
		{
			$tot_rows++;
			$poIds.=$sql_po_row[csf("po_break_down_id")].",";
			$po_break[$sql_po_row[csf('po_break_down_id')]]=$sql_po_row[csf('po_break_down_id')];
			
			$old_str_po=''; $old_item_code=''; $changed=0; $old_po="";
			$job=$cdate="";
			$poId=0; $item_id=0; $color_id=0; $cut_up=0;
			
			$job=$sql_po_row[csf('job_no_mst')];
			$cdate=$sql_po_row[csf('country_ship_date')];
			$poId=$sql_po_row[csf('po_break_down_id')]; 
			$item_id=$sql_po_row[csf('item_number_id')]; 
			$color_id=$sql_po_row[csf('color_number_id')]; 
			$cut_up=$sql_po_row[csf('cutup')];
			
			$sql_po_row[csf('id')]= $sql_po_row[csf('id')]->load();
			$buyer_name=$buyer_name_array[$sql_po_row[csf('buyer_name')]];
			//echo $sql_po_row[csf('id')].'=';
			
			if( $sql_po_row[csf('shiping_status')]==3) $ssts="1";
			else $ssts="0"; //Sewing Out
			/*$gitem=explode(",",$name[csf('gmts_item_id')]);
			if( count($gitem)>1) $str_item="::".$fr_product_type[$name[csf('item_number_id')]]; else $str_item=""; */
			
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['cid'].=$sql_po_row[csf('id')].',';
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['buyer']=$buyer_name;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['style_ref']=$sql_po_row[csf('style_ref_no')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['uom']=$sql_po_row[csf('order_uom')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['gmts_item_id']=$sql_po_row[csf('gmts_item_id')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_number']=$sql_po_row[csf('po_number')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_received_date']=$sql_po_row[csf('po_received_date')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_confirmed']=$sql_po_row[csf('is_confirmed')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_deleted']=$sql_po_row[csf('is_deleted')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['shiping_status']=$ssts;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['plan_cut_new']+=$sql_po_row[csf('plan_cut_new')];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['order_quantity']+=$sql_po_row[csf('order_quantity')];
		}
		//print_r($orders_arr); die;
		
		$newid_ar=array();
		foreach($orders_arr as $jobno=>$job_data)
		{
			foreach($job_data as $poid=>$po_data)
			{
				foreach($po_data as $item_id=>$item_data)
				{
					foreach($item_data as $cdate=>$cdate_data)
					{
						foreach($cdate_data as $colorid=>$colorid_data)
						{
							foreach($colorid_data as $cut_up=>$exdata)
							{
								$old_str_po=''; $old_item_code=''; $changed=0; $old_po="";
								$gitem=explode(",",$exdata['gmts_item_id']);
								if( count($gitem)>1) $str_item="::".$fr_product_type[$item_id]; else $str_item=""; 
								
								if( $cut_up==0 && count($item_data)==1)
								{
									 $str_po=$exdata['po_number']."-".str_replace("FFL-","",$jobno)."::".$color_lib[$colorid]."".$str_item;
									 $str_po_cut="";
								}
								else 
								{
									$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]+=1;
									$str_po=$exdata['po_number']."-".str_replace("FFL-","",$jobno)."::".$color_lib[$colorid]."::".$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]."".$str_item;  
									$str_po_cut=$cutoff[$poid."::".$color_lib[$colorid]."".$str_item]."::"; 
								}
								$nid="";
								$nid=array_filter(array_unique(explode(",",$exdata['cid'])));
								foreach($nid as $vid)
								{
									$newid_ar[$vid]=$str_po;
								}
							}
						}
					}
				}
			}
		}
		//print_r($newid_ar); die;
		
		$poIds=chop($poIds,','); $poIds_cond=""; $bookingpoIds_cond=""; $prodpoIds_cond="";
		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_cond=" and (";
			$bookingpoIds_cond=" and (";
			$prodpoIds_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" po_break_down_id in($ids) or ";
				$bookingpoIds_cond.=" b.po_break_down_id in($ids) or ";
				$prodpoIds_cond.=" c.po_breakdown_id in($ids) or ";
			}
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
			
			$bookingpoIds_cond=chop($bookingpoIds_cond,'or ');
			$bookingpoIds_cond.=")";
			
			$prodpoIds_cond=chop($prodpoIds_cond,'or ');
			$prodpoIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and po_break_down_id in ($poIds)";
			$bookingpoIds_cond=" and b.po_break_down_id in ($poIds)";
			$prodpoIds_cond=" and c.po_breakdown_id in ($poIds)";
		}
		$txt="";
		
		// Production file
		$file_name="frfiles/UPDNORM.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
	  	
		$operation_arra=array(1=>"10",2=>"15",3=>"20",4=>"35",5=>"140",6=>"Finish Input",7=>"Iron Output",8=>"160",9=>"Cutting Delivery",145=>"145",150=>"150",165=>"165",170=>"170",180=>"180"); //145=issue to wash,150=receive from wash,165=Shipped,170= grey production,180=finish production
	  	
		$sql_booking="select b.po_break_down_id, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $bookingpoIds_cond group by b.po_break_down_id";
		$booking_req_arr=array();
		$sql_booking_res=sql_select($sql_booking);
		foreach($sql_booking_res as $row_req)
		{
			$booking_req_arr[$row_req[csf("po_break_down_id")]]['grey']=$row_req[csf("grey_req_qnty")];
			$booking_req_arr[$row_req[csf("po_break_down_id")]]['fin']=$row_req[csf("req_qnty")];
		}
		unset($sql_booking_res);
		
		$fab_sql="select c.po_breakdown_id, c.entry_form, sum(c.quantity) as quantity, max(a.receive_date) as receive_date from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (2,7) and c.entry_form in (2,7) $prodpoIds_cond group by c.po_breakdown_id, c.entry_form";
		
		$fabric_production_arr=array();
		$fab_sql_res=sql_select($fab_sql);
		foreach($fab_sql_res as $row_p)
		{
			if($row_p[csf("entry_form")]==2)
			{
				$fabric_production_arr[$row_p[csf("po_breakdown_id")]]['grey']+=$row_p[csf("quantity")];
				$fabric_production_arr[$row_p[csf("po_breakdown_id")]]['greyDate']=$row_p[csf("receive_date")];
			}
			else if($row_p[csf("entry_form")]==7)
			{
				$fabric_production_arr[$row_p[csf("po_breakdown_id")]]['fin']+=$row_p[csf("quantity")];
				$fabric_production_arr[$row_p[csf("po_breakdown_id")]]['finDate']=$row_p[csf("receive_date")];
			}
		}
		unset($fab_sql_res);
		//print_r( $fabric_production_arr); 
		//die;
		foreach( $fabric_production_arr as $po=>$datas)
		{
			$reqfin_qty=$booking_req_arr[$po]['fin'];
			$grey_qty=$datas['grey'];
			$greyDate=$datas['greyDate'];
			$fin_qty=$datas['fin'];
			$finDate=$datas['finDate'];
			
			$poQty=array_sum($new_indx_powise_qnty[$po]);
			$j=1;
			foreach( $new_indx_powise_qnty[$po] as $inc=>$cQty)
			{
				$colSize_qty=0;
				$colSize_qty=$cQty/$poQty;
				
				//$greyQty=$grey_qty*$colSize_qty;
				$finQtyReq=$reqfin_qty*$colSize_qty;
				$finprodQty=$fin_qty*$colSize_qty;
				if ( $finQtyReq >= $finprodQty ) 
					$finQty=$finprodQty; 
				else
				{
					$finQty=$finprodQty; 
					$fin_qty =$fin_qty -$finprodQty;
				}
				
				$postr=$new_indx_powise_id[$po][$inc];
				
				/*if($greyQty!=0)
				{
					$production_qty[$postr]['170'][0][0][qnty]+=$greyQty;
					$production_qty[$postr]['170'][0][0][pdate]+=$greyDate;
				}*/
				if($finQty!=0)
				{
					$production_qty[$postr]['180'][0][0][qnty]+=$finQty;
					$production_qty[$postr]['180'][0][0][pdate]=$finDate;
				}
			}
		}
		
		$exfac_sql=sql_select( "SELECT a.po_break_down_id, sum(b.production_qnty) AS production_quantity, b.color_size_break_down_id, 165 as production_type, max(a.ex_factory_date) as production_date from pro_ex_factory_mst a, pro_ex_factory_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 $poIds_cond group by a.po_break_down_id,  b.color_size_break_down_id order by a.po_break_down_id, production_date asc "); 
		foreach($exfac_sql as $row_ex)
		{
			$production_qty[$newid_ar[$row_ex[csf("color_size_break_down_id")]]][$row_ex[csf("production_type")]][0][0][qnty]+=$row_ex[csf("production_quantity")];
			$production_qty[$newid_ar[$row_ex[csf("color_size_break_down_id")]]][$row_ex[csf("production_type")]][0][0][pdate]=$row_ex[csf("production_date")]; 
		}
		unset($exfac_sql);
		//print_r($new_indx_powise_qnty); die;
		
		//$txt .=str_pad("U.ORDER",0," ")."\t".str_pad("U.DATE",0," ")."\t".str_pad("U.OPERATION",0," ")."\t".str_pad("U.GROUP_EXTERNAL_ID",0," ")."\t".str_pad("U.LINE_EXTERNAL_ID",0," ")."\t".str_pad("U.QTY",0," ")."\t".str_pad("U.OPN_COMPLETE",0," ")."\r\n";
		$txt .=str_pad("U.ORDER",0," ")."\t".str_pad("U.DATE",0," ")."\t".str_pad("U.OPERATION",0," ")."\t".str_pad("U.LINE_EXTERNAL_ID",0," ")."\t".str_pad("U.QTY",0," ")."\r\n";
		$sewing_qnty=sql_select( "SELECT a.po_break_down_id, a.embel_name, sum(b.production_qnty) AS production_quantity, a.floor_id, a.sewing_line, b.color_size_break_down_id, a.production_type, max(a.production_date) as production_date from pro_garments_production_mst a, pro_garments_production_dtls b where a.production_type in (1,2,3,4,5,8) and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.production_qnty is not null $poIds_cond group by a.po_break_down_id, a.embel_name, a.floor_id, a.sewing_line, b.color_size_break_down_id, a.production_type order by a.po_break_down_id, production_date asc "); // and a.po_break_down_id=22333
		
		//production data,
		
		foreach($sewing_qnty as $row_sew)
		{
			$ssts=1;
			
			if($operation_arra[$row_sew[csf("production_type")]]==35 )
			{
				$row_sew[csf("floor_id")]=0;
				$row_sew[csf("sewing_line")]=0;
			}
			if($row_sew[csf("production_type")]==1 || $row_sew[csf("production_type")]==2 || $row_sew[csf("production_type")]==3 || $row_sew[csf("production_type")]==4) $row_sew[csf("floor_id")]=0;//$row_sew[csf("production_type")]==1 || 
			
			if($row_sew[csf("production_type")]==2 && $row_sew[csf("embel_name")]==3)
			{
				$row_sew[csf("production_type")]=145;
				$row_sew[csf("floor_id")]=0;
				$row_sew[csf("sewing_line")]=0;
			}
			if($row_sew[csf("production_type")]==3 && $row_sew[csf("embel_name")]==3)
			{
				$row_sew[csf("production_type")]=150;
				$row_sew[csf("floor_id")]=0;
				$row_sew[csf("sewing_line")]=0;
			}
			//if($newid_ar[$row_sew[csf("color_size_break_down_id")]]=="") echo $row_sew[csf("color_size_break_down_id")].'<br>';
			$production_qty[$newid_ar[$row_sew[csf("color_size_break_down_id")]]][$row_sew[csf("production_type")]][$row_sew[csf("floor_id")]][$row_sew[csf("sewing_line")]][qnty]+=$row_sew[csf("production_quantity")];
			
			$production_qty[$newid_ar[$row_sew[csf("color_size_break_down_id")]]][$row_sew[csf("production_type")]][$row_sew[csf("floor_id")]][$row_sew[csf("sewing_line")]][pdate]=$row_sew[csf("production_date")]; 
		}
		
		//print_r($production_qty); die;
		
		foreach($production_qty as $oid=>$prodctn)
		{
			ksort($prodctn);
			foreach($prodctn as $prod_typ=>$prodflr)
			{
				ksort($prodflr);
				foreach($prodflr as $flor=>$lndata)
				{
					ksort($lndata);
					foreach($lndata as $line=>$sdata)
					{
						//ksort($sdata);
						//foreach($sdata as $sdate=>$data)
						//{
							$sdate=date("d/m/Y",strtotime($sdata[pdate]));
							if($line==0) $line=""; else $line=(int)$line_name_res[$line]; 
							$flor=$floor_name_res[$line];
							if($flor==0) $flor="";
							$ssts=0;
							if(trim($oid)!='')
								$txt .=str_pad($oid,0," ")."\t".str_pad($sdate,0," ")."\t".str_pad($operation_arra[$prod_typ],0," ")."\t".str_pad($line_array[$line],0," ")."\t".str_pad($sdata[qnty],0," ")."\r\n";
						//}
					}
				}
			}	
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
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
	
	//$zip->addFile("frfiles/ImgFolders.zip");
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