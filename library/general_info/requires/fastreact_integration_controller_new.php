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
	if($cbo_fr_integrtion==0)
	{
		$color_lib=return_library_array("select id,color_name from lib_color","id","color_name");
		$floor_name=return_library_array("select id,floor_name from  lib_prod_floor","id","floor_name");
		$line_name=return_library_array("select id,line_name from lib_sewing_line","id","line_name");
		$line_name_res=return_library_array("select id,line_number from prod_resource_mst","id","line_number");
		$floor_name_res=return_library_array("select id,floor_id from prod_resource_mst","id","floor_id");
		 
		// Customer File
		$sql=sql_select ( "select ID,SHORT_NAME from lib_buyer" );
		$file_name="frfiles/CUSTOMER.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .=str_pad("C.CODE",0," ")."\tEND\r\n";
		foreach($sql as $name)
		{
			//if(strlen($name)>6) $txt .=$name."\tEND\r\n"; else 
			$buyer_name_array[$name["ID"]]=$name["SHORT_NAME"];
			$txt .=str_pad($name["SHORT_NAME"],0," ")."\tEND\r\n";
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
			
		$po_arr=array(); $ft_data_arr=array(); $jobCostingper=array();
		// echo $received_date; die;
		$sql_po="select A.ID, A.JOB_NO, A.STYLE_REF_NO, A.GMTS_ITEM_ID, A.BUYER_NAME, B.PO_QUANTITY from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.po_received_date >= '$received_date' and a.is_deleted=0 and a.status_active=1 order by a.id,b.id"; //and b.is_deleted=0 and b.status_active=1 
		$sql_po_data=sql_select($sql_po);  //and b.is_confirmed=2 
		foreach($sql_po_data as $sql_po_row)
		{
			$ft_data_arr[$sql_po_row['JOB_NO']][job_no]=$sql_po_row['JOB_NO'];//P.CODE
			$ft_data_arr[$sql_po_row['JOB_NO']][gmts_item_id]=$sql_po_row['GMTS_ITEM_ID'];//P.TYPE
			$ft_data_arr[$sql_po_row['JOB_NO']][style_ref_no]=$sql_po_row['STYLE_REF_NO'];//P.DESCRIP
			$po_arr[job_no][$sql_po_row['ID']]=$sql_po_row['ID'];
			$ft_data_arr[$sql_po_row['JOB_NO']][buyer_name]=$sql_po_row['BUYER_NAME'];
			$ft_data_arr[$sql_po_row['JOB_NO']][po_quantity]=$sql_po_row['PO_QUANTITY'];
			//$po_arr[po_id][0]=0;
		}
		unset($sql_po_data);
			
			//print_r($ft_data_arr);
			//die;
			$sql_po="select A.ID, A.JOB_NO, A.STYLE_REF_NO, A.BUYER_NAME, A.GMTS_ITEM_ID, B.ID AS BID, B.PO_QUANTITY, B.UNIT_PRICE, D.COSTING_PER, D.SEW_SMV from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_mst d where a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and b.id=c.po_break_down_id and b.job_id=d.job_id and c.job_id=d.job_id $shipment_date and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by a.id,b.id";
			$sql_po_data=sql_select($sql_po);
			foreach($sql_po_data as $sql_po_row)
			{
				//$po_arr[po_id][$sql_po_row['ID']]=$sql_po_row['ID'];
				$po_arr[job_no][$sql_po_row['ID']]=$sql_po_row['ID'];
				$jobCostingper[$sql_po_row['JOB_NO']]=$sql_po_row['COSTING_PER'];
				
				$ft_data_arr[$sql_po_row['JOB_NO']][job_no]=$sql_po_row['JOB_NO'];//P.CODE
				$ft_data_arr[$sql_po_row['JOB_NO']][gmts_item_id]=$sql_po_row['GMTS_ITEM_ID'];//P.TYPE
				$ft_data_arr[$sql_po_row['JOB_NO']][style_ref_no]=$sql_po_row['STYLE_REF_NO'];//P.DESCRIP
				$fab_knit_req_kg=0;
				 
				$ft_data_arr[$sql_po_row['JOB_NO']][buyer_name]=$sql_po_row['BUYER_NAME'];
				$ft_data_arr[$sql_po_row['JOB_NO']][po_quantity]=$sql_po_row['PO_QUANTITY'];
				$ft_data_arr[$sql_po_row['JOB_NO']][fob]=$sql_po_row['UNIT_PRICE'];
				$po_break[$sql_po_row['BID']]=$sql_po_row['BID'];
				
				$ft_data_arr[$sql_po_row['JOB_NO']][cutting]=1;//P^WC:10
				$ft_data_arr[$sql_po_row['JOB_NO']][sew_input]=1;//P^WC:35
				$ft_data_arr[$sql_po_row['JOB_NO']][sew_output]=$sql_po_row['SEW_SMV'];//P^WC:140
				$ft_data_arr[$sql_po_row['JOB_NO']][poly]=1;//P^WC:160
				$ft_data_arr[$sql_po_row['JOB_NO']][shiped]=$sql_po_row['SEW_SMV'];//P^WC:165
			}
			
			//$po_string= implode(",",$po_arr[po_id]);
			$job_string= implode(",",$po_arr[job_no]);
			
		   $job=array_chunk($po_arr[job_no],1000, true);
		   $job_cond_in=""; $job_condtrim_in="";
		   $ji=0;
		   foreach($job as $key=> $value)
		   {
			   if($ji==0)
			   {
				$job_cond_in="job_id in(".implode(",",$value).")"; 
				$job_cond_in_mst.=" a.job_id in(".implode(",",$value).")";
				$job_condtrim_in="a.job_id in(".implode(",",$value).")";  
			   }
			   else
			   {
					$job_cond_in.=" or job_id in(".implode(",",$value).")"; 
					$job_cond_in_mst.=" or a.job_id in(".implode(",",$value).")"; 
					$job_condtrim_in.=" or a.job_id in(".implode(",",$value).")";
			   }
			   $ji++;
		   }
		   //print_r($job_cond_in); die;
		  
		  /* $sql_fabric_prod=sql_select("select min(id) as id,job_no from wo_pre_cost_fabric_cost_dtls where $job_cond_in and fabric_source=1 and avg_cons>0  and status_active=1 and is_deleted=0  group by job_no");
		   foreach($sql_fabric_prod as $row_fabric_prod)
		   {
			$ft_data_arr[fab_delivery][$row_fabric_prod['job_no']]=1;//P^WC:5   
		   }*/
		  
		  $sql_print_embroid=sql_select("select MIN(ID) AS ID,JOB_NO,EMB_NAME, AVG(CONS_DZN_GMTS) AS CONS_DZN_GMTS  from wo_pre_cost_embe_cost_dtls where $job_cond_in and emb_name in(1,2,3) and cons_dzn_gmts>0  and status_active=1 and is_deleted=0 group by job_no,emb_name");
		  foreach($sql_print_embroid as $row_print_embroid)
		  {
			  if($row_print_embroid['EMB_NAME']==1)
			  {
				$ft_data_arr[$row_print_embroid['JOB_NO']][dv_printing]=1; //P^WC:15
				$ft_data_arr[$row_print_embroid['JOB_NO']][rv_printing]=1; //P^WC:20
			  }
			  if($row_print_embroid['EMB_NAME']==2)
			  {
				$ft_data_arr[$row_print_embroid['JOB_NO']][dv_embrodi]=1; //P^WC:25
				$ft_data_arr[$row_print_embroid['JOB_NO']][rv_embrodi]=1; //P^WC:30
			  }
			  if($row_print_embroid['EMB_NAME']==3)
			  {
				$ft_data_arr[$row_print_embroid['JOB_NO']][dv_wash]=1; //P^WC:145
				$ft_data_arr[$row_print_embroid['JOB_NO']][rv_wash]=1; //P^WC:150
			  }
		  }
		  
		  //=================================Item wise Array Srart=====================================
		   $arr_itemsmv=array();
		   $sql_itemsmv=sql_select("select JOB_NO,GMTS_ITEM_ID,SET_ITEM_RATIO,SMV_PCS_PRECOST,SMV_SET_PRECOST,SMV_PCS,EMBELISHMENT  from wo_po_details_mas_set_details where $job_cond_in");
		   foreach($sql_itemsmv as $row_itemsmv)
		   {
				$arr_itemsmv[$row_itemsmv['JOB_NO']][$row_itemsmv['GMTS_ITEM_ID']]['smv']=$row_itemsmv['SMV_PCS']; 
				$arr_itemsmv[$row_itemsmv['JOB_NO']][$row_itemsmv['GMTS_ITEM_ID']]['emb']=$row_itemsmv['EMBELISHMENT'];  
		   }
		   $cmArr=return_library_array("select job_no, cm_cost from wo_pre_cost_dtls where $job_cond_in and is_deleted=0 and status_active=1","job_no","cm_cost");
		$array_fabric_cons_item=array(); $array_fabric_prod_item=array(); $cmCostArr=array();
		$sql_fabric_cons_item=sql_select("select JOB_NO, ITEM_NUMBER_ID, FABRIC_SOURCE, AVG_FINISH_CONS, AVG_CONS from wo_pre_cost_fabric_cost_dtls where $job_cond_in  and status_active=1 and is_deleted=0");
		// echo "select job_no,item_number_id, sum(avg_finish_cons) as avg_cons, sum(avg_cons) as avg_grey_cons from wo_pre_cost_fabric_cost_dtls where $job_cond_in  and status_active=1 and is_deleted=0 group by job_no, item_number_id"; die;
		foreach($sql_fabric_cons_item as $row_fabric_cons_item)
		{
			if($row_fabric_cons_item['FABRIC_SOURCE']==1)
			{
				$array_fabric_prod_item[$row_fabric_cons_item['JOB_NO']][$row_fabric_cons_item['ITEM_NUMBER_ID']]=1;    
			}
			$costingper=$jobCostingper[$row_fabric['JOB_NO']];
			
			if($costingper==1) $costingQty=12;
			else if($costingper==2) $costingQty=1;
			else if($costingper==3) $costingQty=24;
			else if($costingper==4) $costingQty=36;
			else if($costingper==5) $costingQty=48;
			else $costingQty=0;
			
			$fab_knit_req_kg=$fab_knitgrey_req_kg=$cmCost=0;
			
			$fab_knit_req_kg=$row_fabric_cons_item['AVG_FINISH_CONS']/$costingQty;
			$fab_knitgrey_req_kg=$row_fabric_cons_item['AVG_CONS']/$costingQty;
			
			$array_fabric_cons_item[$row_fabric_cons_item['JOB_NO']][$row_fabric_cons_item['ITEM_NUMBER_ID']]['fin']+=number_format($fab_knit_req_kg,4);
			$array_fabric_cons_item[$row_fabric_cons_item['JOB_NO']][$row_fabric_cons_item['ITEM_NUMBER_ID']]['grey']+=number_format($fab_knitgrey_req_kg,4); 
			
			$cmCost=$cmArr[$row_fabric_cons_item['JOB_NO']]/$costingQty;
			
			$cmCostArr[$row_fabric_cons_item['JOB_NO']]=$cmCost;
		}
		   
		   $ishtparr=array();
			$sqltrim_bom=sql_select("select a.JOB_NO from wo_pre_cost_trim_cost_dtls a, lib_item_group b where $job_condtrim_in and a.trim_group=b.id and a.status_active=1 and a.is_deleted=0 and b.trim_type=3 group by a.job_no");
			
			foreach($sqltrim_bom as $rowtrim)
			{
				$ishtparr[$rowtrim['JOB_NO']]=1;   
			}
			unset($sqltrim_bom);
		   //=================================Item wise Array End=====================================
		
		// Products file
		$txt="";
		$file_name="frfiles/PRODUCTS.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
	 	
		$txt .=str_pad("P.CODE",0," ")."\t".str_pad("P.TYPE",0," ")."\t".str_pad("P.DESCRIP",0," ")."\t".str_pad("P^CF:1",0," ")."\t".str_pad("P^WC:1",0," ")."\t".str_pad("P^CF:2",0," ")."\t".str_pad("P^WC:2",0," ")."\t".str_pad("P^CF:5",0," ")."\t".str_pad("P^WC:5",0," ")."\t".str_pad("P^WC:10",0," ")."\t".str_pad("P^WC:12",0," ")."\t".str_pad("P^WC:15",0," ")."\t".str_pad("P^WC:20",0," ")."\t".str_pad("P^WC:25",0," ")."\t".str_pad("P^WC:30",0," ")."\t".str_pad("P^WC:35",0," ")."\t".str_pad("P^WC:140",0," ")."\t".str_pad("P^WC:145",0," ")."\t".str_pad("P^WC:150",0," ")."\t".str_pad("P^WC:160",0," ")."\t".str_pad("P^WC:165",0," ")."\t".str_pad("P.SPRICE",0," ")."\t".str_pad("P.OTHCOST",0," ")."\t".str_pad("P.UDFabrication",0," ")."\t".str_pad("P.UDGSM",0," ")."\t".str_pad("P.UDYarn Count",0," ")."\t".str_pad("P.UDStyle",0," ")."\r\n";
		
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
					//$array_fabric_cons_item[$row_fabric_cons_item['job_no']][$row_fabric_cons_item['item_number_id']]
					$img_prod[$rows[job_no]][]=$rows[job_no]."::".$fr_product_type[$id];
					
					if(trim($rows[job_no])!="") $txt .=str_pad($rows[job_no]."::".$fr_product_type[$id],0," ")."\t".str_pad($fr_product_type[$id],0," ")."\t".str_pad($rows[style_ref_no],0," ")."\t".str_pad(number_format($array_fabric_cons_item[$rows[job_no]][$id]['grey'], 4, '.', ''),0," ")."\t".str_pad("1",0," ")."\t".str_pad(number_format($array_fabric_cons_item[$rows[job_no]][$id]['grey'], 4, '.', ''),0," ")."\t".str_pad("1",0," ")."\t".str_pad(number_format($array_fabric_cons_item[$rows[job_no]][$id]['fin'], 4, '.', ''),0," ")."\t".str_pad($array_fabric_prod_item[$rows[job_no]][$id] == '' ? 0 :$array_fabric_prod_item[$rows[job_no]][$id],0," ")."\t".str_pad("1",0," ")."\t".str_pad($ishtparr[$rows[job_no]] == '' ? 0 : $ishtparr[$rows[job_no]],0," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],0," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],0," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],0," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($arr_itemsmv[$rows[job_no]][$id]['smv'] == '' ? 0 :$arr_itemsmv[$rows[job_no]][$id]['smv'],0," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],0," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],0," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[fob],0," ")."\t".str_pad($cmCostArr[$rows[job_no]],0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\r\n";
				}
			}
			else
			{
				$img_prod[$rows[job_no]][]=$rows[job_no];
				if(trim($rows[job_no])!="") $txt .=str_pad($rows[job_no],0," ")."\t".str_pad($fr_product_type[$rows[gmts_item_id]],0," ")."\t".str_pad($rows[style_ref_no],0," ")."\t".str_pad( number_format( $array_fabric_cons_item[$rows[job_no]][$rows[gmts_item_id]]['grey'], 4, '.', '' ),0," ")."\t".str_pad("1",0," ")."\t".str_pad( number_format( $array_fabric_cons_item[$rows[job_no]][$rows[gmts_item_id]]['grey'], 4, '.', '' ),0," ")."\t".str_pad("1",0," ")."\t".str_pad( number_format( $array_fabric_cons_item[$rows[job_no]][$rows[gmts_item_id]]['fin'], 4, '.', '' ),0," ")."\t".str_pad($array_fabric_prod_item[$rows[job_no]][$rows[gmts_item_id]] == '' ? 0 :$array_fabric_prod_item[$rows[job_no]][$rows[gmts_item_id]],0," ")."\t".str_pad("1",0," ")."\t".str_pad($ishtparr[$rows[job_no]] == '' ? 0 : $ishtparr[$rows[job_no]],0," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],0," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],0," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],0," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'] == '' ? 0 :$arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'],0," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],0," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],0," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[fob],0," ")."\t".str_pad($cmCostArr[$rows[job_no]],0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\r\n";
			}
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		
		// Orders file
	
 		$sql="SELECT rtrim(xmlagg(xmlelement(e,c.id,',').extract('//text()') order by c.id).GetClobVal(),',') as ID, A.BUYER_NAME, A.STYLE_REF_NO, A.ORDER_UOM, A.GMTS_ITEM_ID, A.GMTS_ITEM_ID_PREV, B.JOB_NO_MST, B.PO_NUMBER, B.PUB_SHIPMENT_DATE, B.PO_RECEIVED_DATE, B.PO_NUMBER_PREV, B.IS_CONFIRMED, B.IS_DELETED, C.PO_BREAK_DOWN_ID, C.ITEM_NUMBER_ID, C.COUNTRY_SHIP_DATE, C.COLOR_NUMBER_ID, C.CUTUP, C.COLOR_NUMBER_ID_PREV, MIN(C.SHIPING_STATUS) AS SHIPING_STATUS, SUM(C.PLAN_CUT_QNTY) AS PLAN_CUT_NEW, SUM(C.ORDER_QUANTITY) AS ORDER_QUANTITY
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
			
			$job=$name['JOB_NO_MST'];
			$cdate=$name['COUNTRY_SHIP_DATE'];
			$poId=$name['PO_BREAK_DOWN_ID']; 
			$item_id=$name['ITEM_NUMBER_ID']; 
			$color_id=$name['COLOR_NUMBER_ID']; 
			$cut_up=$name['CUTUP'];
			
			$name['ID']= $name['ID']->load();
			$buyer_name=$buyer_name_array[$name['BUYER_NAME']];
			
			if( $name['SHIPING_STATUS']==3) $ssts="1"; else $ssts="0"; //Sewing Out
			
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['cid']=$name['ID'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['buyer']=$buyer_name;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['style_ref']=$name['STYLE_REF_NO'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['uom']=$name['ORDER_UOM'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['gmts_item_id']=$name['GMTS_ITEM_ID'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_number']=$name['PO_NUMBER'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_received_date']=$name['PO_RECEIVED_DATE'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_confirmed']=$name['IS_CONFIRMED'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_deleted']=$name['IS_DELETED'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['shiping_status']=$ssts;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['plan_cut_new']+=$name['PLAN_CUT_NEW'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['order_quantity']+=$name['ORDER_QUANTITY'];
			
			if( trim($name['PO_NUMBER_PREV'])!='' && trim($name['PO_NUMBER_PREV'])!=$name['PO_NUMBER'])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_number_prev']=$name['PO_NUMBER_PREV'];
			}
			if( trim($name['GMTS_ITEM_ID_PREV'])!='' && trim($name['GMTS_ITEM_ID_PREV'])!=$name['GMTS_ITEM_ID'])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['gmts_item_id_prev']=$name['GMTS_ITEM_ID_PREV'];
			}
			
			if( trim($name['COLOR_NUMBER_ID_PREV'])!='' && trim($name['COLOR_NUMBER_ID_PREV'])!=$name['COLOR_NUMBER_ID'])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['color_number_id_prev']=$name['COLOR_NUMBER_ID_PREV'];
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
										//$name["job_no_mst"]."::".$old_po."::".$color_lib[$old_color]."::".$old_pub_ship."".$old_item_code; 
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
		
        $fr_comparison_field_array = ''; $fr_comparison_data_array  = '';
         
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
		
		if(trim($received_date)=="") $received_date="01-Aug-2022"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
		
		$bookingcreatedate="and to_char(a.INSERT_DATE,'dd-mm-YYYY') >= '$received_date'";
		
		
		$sql=sql_select("select a.ID, a.BOOKING_NO, a.BUYER_ID, a.BOOKING_DATE, b.GMTS_ITEM_ID, c.QRR_DATE, c.STYLE_REF_NO from WO_NON_ORD_SAMP_BOOKING_MST a, WO_NON_ORD_SAMP_BOOKING_DTLS b, SAMPLE_DEVELOPMENT_MST c where a.BOOKING_NO=b.BOOKING_NO and b.STYLE_ID=c.ID and a.ENTRY_FORM_ID=140 and c.ENTRY_FORM_ID=203 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by a.ID, b.ID, c.ID"); 
		
		$sampleDataArr=array();
		foreach($sql as $row)
		{
			$sampleDataArr[$row['BOOKING_NO']]['buyer']=$row['BUYER_ID'];
			$sampleDataArr[$row['BOOKING_NO']]['product']=$row['GMTS_ITEM_ID'];
			$sampleDataArr[$row['BOOKING_NO']]['qrr_date']=$row['QRR_DATE'];
			$sampleDataArr[$row['BOOKING_NO']]['style']=$row['STYLE_REF_NO'];
		}
		unset($sql);
		$txt="";
		$file_name="frfiles/PRODUCR.S.txt";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .="P.CODE\tP.TYPE\tP.DESCRIP\tP^WC:140\tP.CUST\tP.LAUNCHDATE\r\n";
		
		foreach($sampleDataArr as $smn_booking=>$booking_data)
		{
			$txt .=str_pad($smn_booking,0," ")."\t".str_pad($garments_item[$booking_data['product']],0," ")."\t".str_pad($buyerArr[$booking_data['style']],0," ")."\t".str_pad("0",0," ")."\t".str_pad($buyer_name_array[$booking_data['buyer']],0," ")."\t".str_pad($booking_data['qrr_date'] == '' ? '' :date("d/m/Y",strtotime($booking_data['qrr_date'])),0," ")."\r\n";
		}
		
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	}
	else if($cbo_fr_integrtion==1)
	{
		$sql=sql_select ( "select ID, SHORT_NAME from lib_buyer" );
		$file_name="frfiles/CUSTOMER.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .=str_pad("C.CODE",15," ")."\tEND\r\n";
		foreach($sql as $name)
		{
			//if(strlen($name)>6) $txt .=$name."\tEND\r\n"; else 
			$txt .=str_pad($name["SHORT_NAME"],15," ")."\tEND\r\n";
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	}
	else if($cbo_fr_integrtion==2)
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
		
		$po_arr=array(); $ft_data_arr=array(); $jobCostingper=array();
		 
		$sql_po="select A.ID, A.JOB_NO, A.STYLE_REF_NO, A.GMTS_ITEM_ID, A.BUYER_NAME, B.PO_QUANTITY from wo_po_details_master a, wo_po_break_down b  where a.job_no=b.job_no_mst  and b.shipment_date >= '$received_date'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.job_no='FFL-20-02215' order by a.id,b.id";
		$sql_po_data=sql_select($sql_po);// and b.is_confirmed=2 
		foreach($sql_po_data as $sql_po_row)
		{
			$ft_data_arr[$sql_po_row['JOB_NO']][job_no]=$sql_po_row['JOB_NO'];//P.CODE
			$ft_data_arr[$sql_po_row['JOB_NO']][gmts_item_id]=$sql_po_row['GMTS_ITEM_ID'];//P.TYPE
			$ft_data_arr[$sql_po_row['JOB_NO']][style_ref_no]=$sql_po_row['STYLE_REF_NO'];//P.DESCRIP
			$po_arr[job_no][$sql_po_row['ID']]=$sql_po_row['ID'];
			$ft_data_arr[$sql_po_row['JOB_NO']][buyer_name]=$sql_po_row['BUYER_NAME'];
			$ft_data_arr[$sql_po_row['JOB_NO']][po_quantity]=$sql_po_row['PO_QUANTITY'];
			//$po_arr[po_id][0]=0;
		}
		unset($sql_po_data);
		//echo "0**d";
		//print_r($ft_data_arr);
		//die;
		$sql_po="select A.ID, A.JOB_NO, A.BUYER_NAME, A.STYLE_REF_NO, B.ID AS BID, B.PO_QUANTITY, B.UNIT_PRICE, A.GMTS_ITEM_ID, D.COSTING_PER, D.SEW_SMV from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_mst d where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and b.id=c.po_break_down_id and b.job_id=d.job_id and c.job_id=d.job_id $shipment_date and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by a.id, b.id";
		//echo $sql_po; die;
		$sql_po_data=sql_select($sql_po);
		foreach($sql_po_data as $sql_po_row)
		{
			//$po_arr[po_id][$sql_po_row['ID']]=$sql_po_row['ID];
			$po_arr[job_no][$sql_po_row['ID']]=$sql_po_row['ID'];
			$jobCostingper[$sql_po_row['JOB_NO']]=$sql_po_row['COSTING_PER'];
			
			$ft_data_arr[$sql_po_row['JOB_NO']][job_no]=$sql_po_row['JOB_NO'];//P.CODE
			$ft_data_arr[$sql_po_row['JOB_NO']][gmts_item_id]=$sql_po_row['GMTS_ITEM_ID'];//P.TYPE
			$ft_data_arr[$sql_po_row['JOB_NO']][style_ref_no]=$sql_po_row['STYLE_REF_NO'];//P.DESCRIP
			$fab_knit_req_kg=0;
			 
			$ft_data_arr[$sql_po_row['JOB_NO']][buyer_name]=$sql_po_row['BUYER_NAME'];
			$ft_data_arr[$sql_po_row['JOB_NO']][po_quantity]=$sql_po_row['PO_QUANTITY'];
			$ft_data_arr[$sql_po_row['JOB_NO']][ufob]=$sql_po_row['UNIT_PRICE'];
			$po_break[$sql_po_row['BID']]=$sql_po_row['BID'];
			$ft_data_arr[$sql_po_row['JOB_NO']][cutting]=1;//P^WC:10
			$ft_data_arr[$sql_po_row['JOB_NO']][sew_input]=1;//P^WC:35
			$ft_data_arr[$sql_po_row['JOB_NO']][sew_output]=$sql_po_row['SEW_SMV'];//P^WC:140
			
			$ft_data_arr[$sql_po_row['JOB_NO']][poly]=1;//P^WC:160
			$ft_data_arr[$sql_po_row['JOB_NO']][shiped]=$sql_po_row['SEW_SMV'];//P^WC:165
		}
		
		//print_r($jobCostingper); die;
		//$po_string= implode(",",$po_arr[po_id]);
		$job_string= implode(",",$po_arr[job_no]);
		
	   $job=array_chunk($po_arr[job_no],1000, true);
	   $job_cond_in=""; $job_condtrim_in="";
	   $ji=0;
	   foreach($job as $key=> $value)
	   {
		   if($ji==0)
		   {
				$job_cond_in="job_id in(".implode(",",$value).")"; 
				$job_cond_in_mst.=" a.job_id in(".implode(",",$value).")"; 
				$job_condtrim_in=" a.job_id in(".implode(",",$value).")"; 
		   }
		   else
		   {
				$job_cond_in.=" or job_id in(".implode(",",$value).")"; 
				$job_cond_in_mst.=" or a.job_id in(".implode(",",$value).")"; 
				$job_condtrim_in.=" or a.job_id in(".implode(",",$value).")";
		   }
		   $ji++;
	   }
	   //print_r($job_cond_in); die;
	   
	   /*$sql_fabric_prod=sql_select("select min(id) as id,JOB_NO  from wo_pre_cost_fabric_cost_dtls where $job_cond_in and fabric_source=1 and avg_cons>0  and status_active=1 and is_deleted=0  group by job_no");
	   foreach($sql_fabric_prod as $row_fabric_prod)
	   {
		$ft_data_arr[fab_delivery][$row_fabric_prod['JOB_NO']]=1;//P^WC:5   
	   }*/
	   //print_r($ft_data_arr); die;
	  $sql_print_embroid=sql_select("select min(id) as ID, JOB_NO, EMB_NAME, avg(cons_dzn_gmts) as CONS_DZN_GMTS from wo_pre_cost_embe_cost_dtls where $job_cond_in and emb_name in(1,2,3) and cons_dzn_gmts>0  and status_active=1 and is_deleted=0 group by job_no,emb_name");
	  foreach($sql_print_embroid as $row_print_embroid)
	  {
		  if($row_print_embroid['EMB_NAME']==1)
		  {
			$ft_data_arr[$row_print_embroid['JOB_NO']][dv_printing]=1; //P^WC:15
			$ft_data_arr[$row_print_embroid['JOB_NO']][rv_printing]=1; //P^WC:20
		  }
		  if($row_print_embroid['EMB_NAME']==2)
		  {
			$ft_data_arr[$row_print_embroid['JOB_NO']][dv_embrodi]=1; //P^WC:25
			$ft_data_arr[$row_print_embroid['JOB_NO']][rv_embrodi]=1; //P^WC:30
		  }
		  if($row_print_embroid['EMB_NAME']==3)
		  {
			$ft_data_arr[$row_print_embroid['JOB_NO']][dv_wash]=1; //P^WC:145
			$ft_data_arr[$row_print_embroid['JOB_NO']][rv_wash]=1; //P^WC:150
		  }
	  }
	  
	  //=================================Item wise Array Srart=====================================
	   $arr_itemsmv=array();
	   $sql_itemsmv=sql_select("select JOB_NO, GMTS_ITEM_ID, SET_ITEM_RATIO, SMV_PCS_PRECOST, SMV_SET_PRECOST, SMV_PCS, EMBELISHMENT from wo_po_details_mas_set_details where $job_cond_in");
	   foreach($sql_itemsmv as $row_itemsmv)
	   {
			$arr_itemsmv[$row_itemsmv['JOB_NO']][$row_itemsmv['GMTS_ITEM_ID']]['smv']=$row_itemsmv['SMV_PCS']; 
			$arr_itemsmv[$row_itemsmv['JOB_NO']][$row_itemsmv['GMTS_ITEM_ID']]['emb']=$row_itemsmv['EMBELISHMENT'];  
	   }
		$cmArr=return_library_array("select job_no, cm_cost from wo_pre_cost_dtls where $job_cond_in and is_deleted=0 and status_active=1","job_no","cm_cost");
		//print_r($cmArr);
		$array_fabric_cons_item=array(); $array_fabric_prod_item=array(); $cmCostArr=array();
		$sql_fabric_cons_item=sql_select("select JOB_NO, ITEM_NUMBER_ID, FABRIC_SOURCE, AVG_FINISH_CONS, AVG_CONS from wo_pre_cost_fabric_cost_dtls where $job_cond_in  and status_active=1 and is_deleted=0");
		// echo "select job_no, item_number_id, fabric_source, avg_finish_cons, avg_cons from wo_pre_cost_fabric_cost_dtls where $job_cond_in  and status_active=1 and is_deleted=0"; die;
		foreach($sql_fabric_cons_item as $row_fabric)
		{
			if($row_fabric['FABRIC_SOURCE']==1)
			{
				$array_fabric_prod_item[$row_fabric['JOB_NO']][$row_fabric['ITEM_NUMBER_ID']]=1;    
			}
			$costingper=$jobCostingper[$row_fabric['JOB_NO']];
			
			if($costingper==1) $costingQty=12;
			else if($costingper==2) $costingQty=1;
			else if($costingper==3) $costingQty=24;
			else if($costingper==4) $costingQty=36;
			else if($costingper==5) $costingQty=48;
			else $costingQty=0;
			
			$fab_knit_req_kg=$fab_knitgrey_req_kg=$cmCost=0;
			
			$fab_knit_req_kg=$row_fabric['AVG_FINISH_CONS']/$costingQty;
			$fab_knitgrey_req_kg=$row_fabric['AVG_CONS']/$costingQty;
			
			$array_fabric_cons_item[$row_fabric['JOB_NO']][$row_fabric['ITEM_NUMBER_ID']]['fin']+=number_format($fab_knit_req_kg,4);
			$array_fabric_cons_item[$row_fabric['JOB_NO']][$row_fabric['ITEM_NUMBER_ID']]['grey']+=number_format($fab_knitgrey_req_kg,4); 
			
			$cmCost=$cmArr[$row_fabric['JOB_NO']]/$costingQty;
			//echo $cmArr[$row_fabric['job_no']].'/'.$costingper.'<br>';
			$cmCostArr[$row_fabric['JOB_NO']]=$cmCost;
		}
		//print_r($cmCostArr);
		//echo "10**select job_no,item_number_id, sum(avg_finish_cons) as avg_cons, sum(avg_cons) as avg_grey_cons from wo_pre_cost_fabric_cost_dtls where $job_cond_in  and status_active=1 and is_deleted=0 group by job_no, item_number_id"; die;
		
		/*$sql_fabric_prod_item=sql_select("select min(id) as id,JOB_NO,ITEM_NUMBER_ID  from wo_pre_cost_fabric_cost_dtls where $job_cond_in and fabric_source=1 and avg_cons>0  and status_active=1 and is_deleted=0  group by job_no,item_number_id");
		foreach($sql_fabric_prod_item as $row_fabric_prod_item)
		{
			$array_fabric_prod_item[$row_fabric_prod_item['JOB_NO']][$row_fabric_prod_item['ITEM_NUMBER_ID']]=1;   
		}*/
		//echo "10**select a.job_no, 1 as type from wo_pre_cost_trim_cost_dtls a, lib_item_group b where $job_condtrim_in and a.trim_group=b.id and a.status_active=1 and a.is_deleted=0 and b.trim_type=3"; die;
		$ishtparr=array();
		$sqltrim_bom=sql_select("select a.JOB_NO from wo_pre_cost_trim_cost_dtls a, lib_item_group b where $job_condtrim_in and a.trim_group=b.id and a.status_active=1 and a.is_deleted=0 and b.trim_type=3 group by a.job_no");
		
		foreach($sqltrim_bom as $rowtrim)
		{
			$ishtparr[$rowtrim['JOB_NO']]=1;   
		}
		unset($sqltrim_bom);
		/*echo count($ishtparr);
		print_r($ishtparr);die;*/
		
		//echo "10**select job_no, cm_cost from wo_pre_cost_dtls where $job_cond_in and is_deleted=0 and status_active=1"; die;
	   //=================================Item wise Array End=====================================
		
		// Products file
		$txt="";
		$file_name="frfiles/PRODUCTS.TXT";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
	 	
		$txt .=str_pad("P.CODE",0," ")."\t".str_pad("P.TYPE",0," ")."\t".str_pad("P.DESCRIP",0," ")."\t".str_pad("P^CF:1",0," ")."\t".str_pad("P^WC:1",0," ")."\t".str_pad("P^CF:2",0," ")."\t".str_pad("P^WC:2",0," ")."\t".str_pad("P^CF:5",0," ")."\t".str_pad("P^WC:5",0," ")."\t".str_pad("P^WC:10",0," ")."\t".str_pad("P^WC:12",0," ")."\t".str_pad("P^WC:15",0," ")."\t".str_pad("P^WC:20",0," ")."\t".str_pad("P^WC:25",0," ")."\t".str_pad("P^WC:30",0," ")."\t".str_pad("P^WC:35",0," ")."\t".str_pad("P^WC:140",0," ")."\t".str_pad("P^WC:145",0," ")."\t".str_pad("P^WC:150",0," ")."\t".str_pad("P^WC:160",0," ")."\t".str_pad("P^WC:165",0," ")."\t".str_pad("P.SPRICE",0," ")."\t".str_pad("P.OTHCOST",0," ")."\t".str_pad("P.UDFabrication",0," ")."\t".str_pad("P.UDGSM",0," ")."\t".str_pad("P.UDYarn Count",0," ")."\t".str_pad("P.UDStyle",0," ")."\r\n";
		
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
					//$array_fabric_cons_item[$row_fabric_cons_item['job_no']][$row_fabric_cons_item['item_number_id']]['fin']
					$img_prod[$rows[job_no]][]=$rows[job_no]."::".$fr_product_type[$id];
					
					if(trim($rows[job_no])!="") $txt .=str_pad($rows[job_no]."::".$fr_product_type[$id],0," ")."\t".str_pad($fr_product_type[$id],0," ")."\t".str_pad($rows[style_ref_no],0," ")."\t".str_pad(number_format($array_fabric_cons_item[$rows[job_no]][$id]['grey'], 4, '.', ''),0," ")."\t".str_pad("1",0," ")."\t".str_pad(number_format($array_fabric_cons_item[$rows[job_no]][$id]['grey'], 4, '.', ''),0," ")."\t".str_pad("1",0," ")."\t".str_pad(number_format($array_fabric_cons_item[$rows[job_no]][$id]['fin'], 4, '.', ''),0," ")."\t".str_pad($array_fabric_prod_item[$rows[job_no]][$id] == '' ? 0 :$array_fabric_prod_item[$rows[job_no]][$id],0," ")."\t".str_pad("1",0," ")."\t".str_pad($ishtparr[$rows[job_no]] == '' ? 0 : $ishtparr[$rows[job_no]],0," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],0," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],0," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],0," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($arr_itemsmv[$rows[job_no]][$id]['smv'] == '' ? 0 :$arr_itemsmv[$rows[job_no]][$id]['smv'],0," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],0," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],0," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[ufob],0," ")."\t".str_pad($cmCostArr[$rows[job_no]],0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\r\n";
				}
			}
			else
			{
				$img_prod[$rows[job_no]][]=$rows[job_no];
				if(trim($rows[job_no])!="") $txt .=str_pad($rows[job_no],0," ")."\t".str_pad($fr_product_type[$rows[gmts_item_id]],0," ")."\t".str_pad($rows[style_ref_no],0," ")."\t".str_pad( number_format( $array_fabric_cons_item[$rows[job_no]][$rows[gmts_item_id]]['grey'], 4, '.', '' ),0," ")."\t".str_pad("1",0," ")."\t".str_pad( number_format( $array_fabric_cons_item[$rows[job_no]][$rows[gmts_item_id]]['grey'], 4, '.', '' ),0," ")."\t".str_pad("1",0," ")."\t".str_pad( number_format( $array_fabric_cons_item[$rows[job_no]][$rows[gmts_item_id]]['fin'], 4, '.', '' ),0," ")."\t".str_pad($array_fabric_prod_item[$rows[job_no]][$rows[gmts_item_id]] == '' ? 0 :$array_fabric_prod_item[$rows[job_no]][$rows[gmts_item_id]],0," ")."\t".str_pad("1",0," ")."\t".str_pad($ishtparr[$rows[job_no]] == '' ? 0 : $ishtparr[$rows[job_no]],0," ")."\t".str_pad($rows[dv_printing] == '' ? 0 : $rows[dv_printing],0," ")."\t".str_pad($rows[rv_printing] == '' ? 0 : $rows[rv_printing],0," ")."\t".str_pad($rows[dv_embrodi] == '' ? 0 :$rows[dv_embrodi],0," ")."\t".str_pad($rows[rv_embrodi] == '' ? 0 :$rows[rv_embrodi],0," ")."\t".str_pad("1",0," ")."\t".str_pad($arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'] == '' ? 0 :$arr_itemsmv[$rows[job_no]][$rows[gmts_item_id]]['smv'],0," ")."\t".str_pad($rows[dv_wash] == '' ? 0 :$rows[dv_wash],0," ")."\t".str_pad($rows[rv_wash] == '' ? 0 :$rows[rv_wash],0," ")."\t".str_pad($rows[poly] == '' ? 0 :$rows[poly],0," ")."\t".str_pad("1",0," ")."\t".str_pad($rows[ufob],0," ")."\t".str_pad($cmCostArr[$rows[job_no]],0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\t".str_pad("",0," ")."\r\n";
			}
		}
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";

		
	}
	else if($cbo_fr_integrtion==3)
	{
		$color_lib=return_library_array("select id,color_name from lib_color","id","color_name");
		$floor_name=return_library_array("select id,floor_name from  lib_prod_floor","id","floor_name");
		$line_name=return_library_array("select id,line_name from lib_sewing_line","id","line_name");
		$line_name_res=return_library_array("select id,line_number from prod_resource_mst","id","line_number");
		$floor_name_res=return_library_array("select id,floor_id from prod_resource_mst","id","floor_id");
		
		$sql=sql_select ( "select ID, SHORT_NAME from lib_buyer" );
 
		foreach($sql as $name)
		{
			//if(strlen($name)>6) $txt .=$name."\tEND\r\n"; else 
			$buyer_name_array[$name["ID"]]=$name["SHORT_NAME"];
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
 
 		$sql=sql_select ( "SELECT rtrim(xmlagg(xmlelement(e,c.id,',').extract('//text()') order by c.id).GetClobVal(),',') as ID, A.BUYER_NAME, A.STYLE_REF_NO, A.ORDER_UOM, A.GMTS_ITEM_ID, A.GMTS_ITEM_ID_PREV, B.JOB_NO_MST, B.PO_NUMBER, B.PUB_SHIPMENT_DATE, B.PO_RECEIVED_DATE, B.PO_NUMBER_PREV, B.IS_CONFIRMED, B.IS_DELETED, C.PO_BREAK_DOWN_ID, C.ITEM_NUMBER_ID, C.COUNTRY_SHIP_DATE, C.COLOR_NUMBER_ID, C.CUTUP, C.COLOR_NUMBER_ID_PREV, MIN(C.SHIPING_STATUS) AS SHIPING_STATUS, SUM(C.PLAN_CUT_QNTY) AS PLAN_CUT_NEW, SUM(C.ORDER_QUANTITY) AS ORDER_QUANTITY
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c WHERE 1=1 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.plan_cut_qnty!=0 $shipment_date
		group by a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.gmts_item_id_prev, b.job_no_mst, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_number_prev, b.is_confirmed, b.is_deleted, c.po_break_down_id, c.item_number_id, c.country_ship_date, c.color_number_id, c.cutup, c.color_number_id_prev order by c.country_ship_date, c.po_break_down_id, c.cutup"); //b.PUB_SHIPMENT_DATE_PREV, C.COUNTRY_SHIP_DATE_PREV,
		
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
			
			$job=$name['JOB_NO_MST'];
			$cdate=$name['COUNTRY_SHIP_DATE'];
			$poId=$name['PO_BREAK_DOWN_ID']; 
			$item_id=$name['ITEM_NUMBER_ID']; 
			$color_id=$name['COLOR_NUMBER_ID']; 
			$cut_up=$name['CUTUP'];
			
			$name['ID']= $name['ID']->load();
			$buyer_name=$buyer_name_array[$name['BUYER_NAME']];
			
			if( $name['SHIPING_STATUS']==3) $ssts="1";
			else $ssts="0"; //Sewing Out
			/*$gitem=explode(",",$name['GMTS_ITEM_ID']);
			if( count($gitem)>1) $str_item="::".$fr_product_type[$name['ITEM_NUMBER_ID']]; else $str_item=""; */
			
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['cid']=$name['ID'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['buyer']=$buyer_name;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['style_ref']=$name['STYLE_REF_NO'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['uom']=$name['ORDER_UOM'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['gmts_item_id']=$name['GMTS_ITEM_ID'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_number']=$name['PO_NUMBER'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_received_date']=$name['PO_RECEIVED_DATE'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_confirmed']=$name['IS_CONFIRMED'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_deleted']=$name['IS_DELETED'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['shiping_status']=$ssts;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['plan_cut_new']+=$name['PLAN_CUT_NEW'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['order_quantity']+=$name['ORDER_QUANTITY'];
			
			if( trim($name['PO_NUMBER_PREV'])!='' && trim($name['PO_NUMBER_PREV'])!=$name['PO_NUMBER'])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_number_prev']=$name['PO_NUMBER_PREV'];
			}
			if( trim($name['GMTS_ITEM_ID_PREV'])!='' && trim($name['GMTS_ITEM_ID_PREV'])!=$name['GMTS_ITEM_ID'])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['gmts_item_id_prev']=$name['GMTS_ITEM_ID_PREV'];
			}
			
			if( trim($name['COLOR_NUMBER_ID_PREV'])!='' && trim($name['COLOR_NUMBER_ID_PREV'])!=$name['COLOR_NUMBER_ID'])
			{
				$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['color_number_id_prev']=$name['COLOR_NUMBER_ID_PREV'];
			}
			
			
			/*if( $name['CUTUP']==0 )
			{
				 $str_po=$name['PO_NUMBER']."-".str_replace("FFL-","",$name['JOB_NO_MST'])."::".$color_lib[$name['COLOR_NUMBER_ID']]."".$str_item;
				 $str_po_cut="";
			}
			else 
			{
				$cutoff[$name['PO_BREAK_DOWN_ID']."::".$color_lib[$name['COLOR_NUMBER_ID']]."".$str_item]+=1;
				
				$str_po=$name['PO_NUMBER']."-".str_replace("FFL-","",$name['JOB_NO_MST'])."::".$color_lib[$name['COLOR_NUMBER_ID']]."::".$cutoff[$name['PO_BREAK_DOWN_ID']."::".$color_lib[$name['COLOR_NUMBER_ID']]."".$str_item]."".$str_item;  
				$str_po_cut=$cutoff[$name['PO_BREAK_DOWN_ID']."::".$color_lib[$name['COLOR_NUMBER_ID']]."".$str_item]."::"; 
			}
			
			$pgitem=explode(",",trim($name['GMTS_ITEM_ID_PREV']));
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
				$old_item_code="::".$fr_product_type[$name['ITEM_NUMBER_ID']];
			
			if( $name['PO_NUMBER_PREV']!='' && trim($name['PO_NUMBER_PREV'])!=$name['PO_NUMBER'])
			{
				$old_po=trim($name['PO_NUMBER_PREV']);
				$changed=1;
			}
			else
				$old_po= $name['PO_NUMBER'];
				
			if( $name['COLOR_NUMBER_ID_PREV']!='' && $name['COLOR_NUMBER_ID_PREV']!=$name['COLOR_NUMBER_ID'])
			{
				$old_color= trim($name['COLOR_NUMBER_ID_PREV']);
				$changed=1;
			}
			else
				$old_color= $name['COLOR_NUMBER_ID'];
			
			if( $name['PUB_SHIPMENT_DATE_PREV']!='' && date("d-m-Y",strtotime($name['PUB_SHIPMENT_DATE_PREV']))!=date("d-m-Y",strtotime($name['PUB_SHIPMENT_DATE'])))
			{
				$old_pub_ship= date("ymd",strtotime($name['PUB_SHIPMENT_DATE_PREV']));
				$changed=1;
			}
			else
				$old_pub_ship= date("ymd",strtotime($name['PUB_SHIPMENT_DATE']));
			
			if($changed==1)
			{
				if( $name['cutup']==0 )
				{
					$old_str_po=$old_po."-".str_replace("FFL-","",$name['JOB_NO_MST'])."::".$color_lib[$old_color]."".$old_item_code;
					//$name["JOB_NO_MST"]."::".$old_po."::".$color_lib[$old_color]."::".$old_pub_ship."".$old_item_code; 
				}
				else
				{
					$old_str_po=$old_po."-".str_replace("FFL-","",$name['JOB_NO_MST'])."::".$color_lib[$old_color]."::".$cutoff[$name['PO_BREAK_DOWN_ID']."::".$color_lib[$old_color]."".$str_item]."".$old_item_code;  
				}
			}
			
			if($dtls_id=="") $dtls_id=$name['ID']; else $dtls_id .=",".$name['ID'];
			
			//$ssts=0;
			
			$txt .=str_pad($str_po,0," ")."\t".str_pad($old_str_po,0," ")."\t".str_pad($name['JOB_NO_MST']."".$str_item,0," ")."\t".str_pad( $buyer_naem,0," ")."\t".str_pad(date("d/m/Y",strtotime($name['COUNTRY_SHIP_DATE'])),0," ")."\t".str_pad($name['PLAN_CUT_NEW'],0," ")."\t".str_pad($name['STYLE_REF_NO'],0," ")."\t".str_pad($str,0," ")."\t".str_pad($ssts,0," ")."\t".str_pad($name['ORDER_QUANTITY'],0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($name['PO_NUMBER'],0," ")."\t".str_pad($color_lib[$name['COLOR_NUMBER_ID']],0," ")."\t".str_pad($name['PO_NUMBER']."::".$color_lib[$name['COLOR_NUMBER_ID']]."::". $str_po_cut .$fr_product_type[$name['ITEM_NUMBER_ID']],0," ")."\r\n";
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
										//$name["job_no_mst"]."::".$old_po."::".$color_lib[$old_color]."::".$old_pub_ship."".$old_item_code; 
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
	else if($cbo_fr_integrtion==4)
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
		
		$sql=sql_select ( "select A.PO_NUMBER_ID, A.JOB_NO, A.ACTUAL_START_DATE, A.ACTUAL_FINISH_DATE, A.TASK_NUMBER, B.PO_NUMBER, C.STYLE_REF_NO, C.GMTS_ITEM_ID, D.ITEM_NUMBER_ID, D.CUTUP, D.COLOR_NUMBER_ID from tna_process_mst a, wo_po_break_down b, wo_po_details_master c, wo_po_color_size_breakdown d where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and a.po_number_id=b.id and b.id=d.po_break_down_id $shipment_date order by a.po_number_id, a.task_number" ); 
		
		
		$file_name="frfiles/EVENTS.TXT";
		$myfile = fopen( $file_name, "w" ) or die("Unable to open file!");
	 	
		$txt .=str_pad("E.CODE",0," ")."\t".str_pad("E.TYPE",0," ")."\t".str_pad("E.EVENT",0," ")."\t".str_pad("E.AD",0," ")."\t".str_pad("E.DONE",0," ")."\t".str_pad("E.SKIP",0," ")."\t".str_pad("E.NOTE",0," ")."\r\n";
		foreach($sql as $name)
		{
			$gitem=explode(",",$name['GMTS_ITEM_ID']);
			
			if( count($gitem)>1) $str_item="::".$fr_product_type[$name['ITEM_NUMBER_ID']]; else $str_item=""; 
			
			if( $name['CUTUP']==0 )
			{
				 $str_po=$name['PO_NUMBER']."-".str_replace("FFL-","",$name['JOB_NO'])."::".$color_lib[$name['COLOR_NUMBER_ID']]."".$str_item;
				 $str_po_cut="";
			}
			else 
			{
				$cutoff[$name['PO_NUMBER_ID']."::".$color_lib[$name['COLOR_NUMBER_ID']]."".$str_item]+=1;
				
				$str_po=$name['PO_NUMBER']."-".str_replace("FFL-","",$name['JOB_NO'])."::".$color_lib[$name['COLOR_NUMBER_ID']]."::".$cutoff[$name['PO_NUMBER_ID']."::".$color_lib[$name['COLOR_NUMBER_ID']]."".$str_item]."".$str_item;  
				$str_po_cut=$cutoff[$name['PO_NUMBER_ID']."::".$color_lib[$name['COLOR_NUMBER_ID']]."".$str_item]."::"; 
			}
			
			$str_po_list[$name['PO_NUMBER_ID']][$str_po]=$str_po;	
			
			if( $name['ACTUAL_FINISH_DATE']=="0000-00-00") $name['ACTUAL_FINISH_DATE']=""; else $name['ACTUAL_FINISH_DATE']=date("d/m/Y",strtotime($name['ACTUAL_FINISH_DATE']));
			if( $name['ACTUAL_START_DATE']=="0000-00-00") $name['ACTUAL_START_DATE']=""; else $name['ACTUAL_START_DATE']=date("d/m/Y",strtotime($name['ACTUAL_START_DATE']));
			
			$tna_po_name[$name['PO_NUMBER_ID']]=$name['PO_NUMBER_ID'];
			$tna_po_task[$name['PO_NUMBER_ID']][$name['TASK_NUMBER']]['actual_finish_date']=$name['ACTUAL_FINISH_DATE'];
			$tna_po_task[$name['PO_NUMBER_ID']][$name['TASK_NUMBER']]['job_no_mst']=$name['JOB_NO'];
			$tna_po_task[$name['PO_NUMBER_ID']][$name['TASK_NUMBER']]['style_ref_no']=$name['STYLE_REF_NO'];
			//$tna_po_task[$name['PO_NUMBER_ID']][$name['TASK_NUMBER']]['actual_finish_date']=$name['ACTUAL_FINISH_DATE'];
			//$tna_po_task[$name['PO_NUMBER_ID']][$name['TASK_NUMBER']]['actual_finish_date']=$name['ACTUAL_FINISH_DATE'];
		}
		unset( $sql );
		
		foreach($tna_po_name as $poid )
		{
			foreach( $str_po_list[$poid] as $order=>$no ) //$str_po_list[$name['po_break_down_id']][$str_po]=$str_po;	
			{
				foreach($tna_po_task[$poid] as $task=>$names)
				{
					// print_r($names['job_no_mst']);
					if( $mapped_tna_task[$task]=='' ) $mapped_tna_task[$task]=0;
					if( $mapped_tna_task[$task]!=0 )
						$txt .=str_pad($order,0," ")."\tO\t".str_pad($mapped_tna_task[$task],0," ")."\t\t".str_pad($names['actual_finish_date'],0," ")."\t0\t".str_pad($names['style_ref_no'],0," ")."\r\n";
				}
			} //$tna_po_id[$name['po_number_id']]
		}
		fwrite( $myfile, $txt );
		fclose($myfile); 
		$txt="";
	}
	else if($cbo_fr_integrtion==5)
	{
		if(trim( $received_date)=="") $received_date="01-Aug-2018"; else $received_date=change_date_format($received_date, "yyyy-mm-dd", "-",1);
		$shipment_date="and b.po_received_date >= '$received_date'";
		
		$color_lib=return_library_array("select id,color_name from lib_color","id","color_name");
		$floor_name=return_library_array("select id,floor_name from  lib_prod_floor","id","floor_name");
		$line_name=return_library_array("select id,line_name from lib_sewing_line","id","line_name");
		$line_name_res=return_library_array("select id,line_number from prod_resource_mst","id","line_number");
		$floor_name_res=return_library_array("select id,floor_id from prod_resource_mst","id","floor_id");
		//echo '0**';
		$sql_po="SELECT rtrim(xmlagg(xmlelement(e,c.id,',').extract('//text()') order by c.id).GetClobVal(),',') as ID, A.BUYER_NAME, A.STYLE_REF_NO, A.ORDER_UOM, A.GMTS_ITEM_ID, A.GMTS_ITEM_ID_PREV, B.JOB_NO_MST, B.PO_NUMBER, B.PUB_SHIPMENT_DATE, B.PO_RECEIVED_DATE, B.PO_NUMBER_PREV, B.IS_CONFIRMED, B.IS_DELETED, C.PO_BREAK_DOWN_ID, C.ITEM_NUMBER_ID, C.COUNTRY_SHIP_DATE, C.COLOR_NUMBER_ID, C.CUTUP, C.COLOR_NUMBER_ID_PREV, MIN(C.SHIPING_STATUS) AS SHIPING_STATUS, SUM(C.PLAN_CUT_QNTY) AS PLAN_CUT_NEW, SUM(C.ORDER_QUANTITY) AS ORDER_QUANTITY
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c WHERE 1=1 and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.plan_cut_qnty!=0 $shipment_date
		group by a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.gmts_item_id_prev, b.job_no_mst, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_number_prev, b.is_confirmed, b.is_deleted, c.po_break_down_id, c.item_number_id, c.country_ship_date, c.color_number_id, c.cutup, c.color_number_id_prev order by c.country_ship_date, c.po_break_down_id, c.cutup";
		
		//echo "10**".$sql_po;
		$new_indx_powise=array();
		$sql_po_data=sql_select($sql_po); $tot_rows=0; $poIds=''; $i=0; $k=1; $po_req_qty_array=array(); $req_qty_array=array();
		foreach($sql_po_data as $sql_po_row)
		{
			$tot_rows++;
			$poIds.=$sql_po_row["PO_BREAK_DOWN_ID"].",";
			$po_break[$sql_po_row['PO_BREAK_DOWN_ID']]=$sql_po_row['PO_BREAK_DOWN_ID'];
			
			$old_str_po=''; $old_item_code=''; $changed=0; $old_po="";
			$job=$cdate="";
			$poId=0; $item_id=0; $color_id=0; $cut_up=0;
			
			$job=$sql_po_row['JOB_NO_MST'];
			$cdate=$sql_po_row['COUNTRY_SHIP_DATE'];
			$poId=$sql_po_row['PO_BREAK_DOWN_ID']; 
			$item_id=$sql_po_row['ITEM_NUMBER_ID']; 
			$color_id=$sql_po_row['COLOR_NUMBER_ID']; 
			$cut_up=$sql_po_row['CUTUP'];
			
			$sql_po_row['ID']= $sql_po_row['ID']->load();
			$buyer_name=$buyer_name_array[$sql_po_row['BUYER_NAME']];
			//echo $sql_po_row['id'].'=';
			
			if( $sql_po_row['SHIPING_STATUS']==3) $ssts="1";
			else $ssts="0"; //Sewing Out
			/*$gitem=explode(",",$name['GMTS_ITEM_ID']);
			if( count($gitem)>1) $str_item="::".$fr_product_type[$name['ITEM_NUMBER_ID']]; else $str_item=""; */
			
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['cid'].=$sql_po_row['ID'].',';
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['buyer']=$buyer_name;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['style_ref']=$sql_po_row['STYLE_REF_NO'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['uom']=$sql_po_row['ORDER_UOM'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['gmts_item_id']=$sql_po_row['GMTS_ITEM_ID'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_number']=$sql_po_row['PO_NUMBER'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['po_received_date']=$sql_po_row['PO_RECEIVED_DATE'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_confirmed']=$sql_po_row['IS_CONFIRMED'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['is_deleted']=$sql_po_row['IS_DELETED'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['shiping_status']=$ssts;
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['plan_cut_new']+=$sql_po_row['PLAN_CUT_NEW'];
			$orders_arr[$job][$poId][$item_id][$cdate][$color_id][$cut_up]['order_quantity']+=$sql_po_row['ORDER_QUANTITY'];
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
	  	
		$operation_arra=array(1=>"10",2=>"15",3=>"20",4=>"35",5=>"140",6=>"Finish Input",7=>"Iron Output",8=>"160",9=>"Cutting Delivery",25=>"25",30=>"30",145=>"145",150=>"150",165=>"165",170=>"170",180=>"180"); //145=issue to wash,150=receive from wash,165=Shipped,170= grey production,180=finish production
	  	
		$sql_booking="select B.PO_BREAK_DOWN_ID, SUM(B.FIN_FAB_QNTY) AS REQ_QNTY, SUM(B.GREY_FAB_QNTY) AS GREY_REQ_QNTY from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $bookingpoIds_cond group by b.po_break_down_id";
		$booking_req_arr=array();
		$sql_booking_res=sql_select($sql_booking);
		foreach($sql_booking_res as $row_req)
		{
			$booking_req_arr[$row_req["PO_BREAK_DOWN_ID"]]['grey']=$row_req["GREY_REQ_QNTY"];
			$booking_req_arr[$row_req["PO_BREAK_DOWN_ID"]]['fin']=$row_req["REQ_QNTY"];
		}
		unset($sql_booking_res);
		
		$fab_sql="select C.PO_BREAKDOWN_ID, C.ENTRY_FORM, SUM(C.QUANTITY) AS QUANTITY, MAX(A.RECEIVE_DATE) AS RECEIVE_DATE from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in (2,7) and c.entry_form in (2,7) $prodpoIds_cond group by c.po_breakdown_id, c.entry_form";
		
		$fabric_production_arr=array();
		$fab_sql_res=sql_select($fab_sql);
		foreach($fab_sql_res as $row_p)
		{
			if($row_p["ENTRY_FORM"]==2)
			{
				$fabric_production_arr[$row_p["PO_BREAKDOWN_ID"]]['grey']+=$row_p["QUANTITY"];
				$fabric_production_arr[$row_p["PO_BREAKDOWN_ID"]]['greyDate']=$row_p["RECEIVE_DATE"];
			}
			else if($row_p["ENTRY_FORM"]==7)
			{
				$fabric_production_arr[$row_p["PO_BREAKDOWN_ID"]]['fin']+=$row_p["QUANTITY"];
				$fabric_production_arr[$row_p["PO_BREAKDOWN_ID"]]['finDate']=$row_p["RECEIVE_DATE"];
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
		
		$exfac_sql=sql_select( "SELECT A.PO_BREAK_DOWN_ID, SUM(B.PRODUCTION_QNTY) AS PRODUCTION_QUANTITY, B.COLOR_SIZE_BREAK_DOWN_ID, 165 AS PRODUCTION_TYPE, MAX(A.EX_FACTORY_DATE) AS PRODUCTION_DATE from pro_ex_factory_mst a, pro_ex_factory_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 $poIds_cond group by a.po_break_down_id,  b.color_size_break_down_id order by a.po_break_down_id, production_date asc "); 
		foreach($exfac_sql as $row_ex)
		{
			$production_qty[$newid_ar[$row_ex["COLOR_SIZE_BREAK_DOWN_ID"]]][$row_ex["PRODUCTION_TYPE"]][0][0][qnty]+=$row_ex["PRODUCTION_QUANTITY"];
			$production_qty[$newid_ar[$row_ex["COLOR_SIZE_BREAK_DOWN_ID"]]][$row_ex["PRODUCTION_TYPE"]][0][0][pdate]=$row_ex["PRODUCTION_DATE"]; 
		}
		unset($exfac_sql);
		//print_r($new_indx_powise_qnty); die;
		
		//$txt .=str_pad("U.ORDER",0," ")."\t".str_pad("U.DATE",0," ")."\t".str_pad("U.OPERATION",0," ")."\t".str_pad("U.GROUP_EXTERNAL_ID",0," ")."\t".str_pad("U.LINE_EXTERNAL_ID",0," ")."\t".str_pad("U.QTY",0," ")."\t".str_pad("U.OPN_COMPLETE",0," ")."\r\n";
		$txt .=str_pad("U.ORDER",0," ")."\t".str_pad("U.DATE",0," ")."\t".str_pad("U.OPERATION",0," ")."\t".str_pad("U.LINE_EXTERNAL_ID",0," ")."\t".str_pad("U.QTY",0," ")."\r\n";
		$sewing_qnty=sql_select( "SELECT A.PO_BREAK_DOWN_ID, A.EMBEL_NAME, SUM(B.PRODUCTION_QNTY) AS PRODUCTION_QUANTITY, A.FLOOR_ID, A.SEWING_LINE, B.COLOR_SIZE_BREAK_DOWN_ID, A.PRODUCTION_TYPE, MAX(A.PRODUCTION_DATE) AS PRODUCTION_DATE from pro_garments_production_mst a, pro_garments_production_dtls b where a.production_type in (1,2,3,4,5,8) and a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.production_qnty is not null $poIds_cond group by a.po_break_down_id, a.embel_name, a.floor_id, a.sewing_line, b.color_size_break_down_id, a.production_type order by a.po_break_down_id, production_date asc "); // and a.po_break_down_id=22333
		
		//production data,
		
		foreach($sewing_qnty as $row_sew)
		{
			$ssts=1;
			
			if($operation_arra[$row_sew["PRODUCTION_TYPE"]]==35 )
			{
				$row_sew["FLOOR_ID"]=0;
				$row_sew["SEWING_LINE"]=0;
			}
			if($row_sew["PRODUCTION_TYPE"]==1 || $row_sew["PRODUCTION_TYPE"]==2 || $row_sew["PRODUCTION_TYPE"]==3 || $row_sew["PRODUCTION_TYPE"]==4) $row_sew["FLOOR_ID"]=0;//$row_sew["PRODUCTION_TYPE"]==1 || 
			
			if($row_sew["PRODUCTION_TYPE"]==2 && $row_sew["EMBEL_NAME"]==3)
			{
				$row_sew["PRODUCTION_TYPE"]=145;
				$row_sew["FLOOR_ID"]=0;
				$row_sew["SEWING_LINE"]=0;
			}
			if($row_sew["PRODUCTION_TYPE"]==2 && $row_sew["EMBEL_NAME"]==2)//Embro
			{
				$row_sew["PRODUCTION_TYPE"]=25;
				$row_sew["FLOOR_ID"]=0;
				$row_sew["SEWING_LINE"]=0;
			}
			if($row_sew["PRODUCTION_TYPE"]==3 && $row_sew["EMBEL_NAME"]==3)
			{
				$row_sew["PRODUCTION_TYPE"]=150;
				$row_sew["FLOOR_ID"]=0;
				$row_sew["SEWING_LINE"]=0;
			}
			if($row_sew["PRODUCTION_TYPE"]==3 && $row_sew["EMBEL_NAME"]==2)//Embro
			{
				$row_sew["PRODUCTION_TYPE"]=30;
				$row_sew["FLOOR_ID"]=0;
				$row_sew["SEWING_LINE"]=0;
			}
			//if($newid_ar[$row_sew["COLOR_SIZE_BREAK_DOWN_ID"]]=="") echo $row_sew["COLOR_SIZE_BREAK_DOWN_ID"].'<br>';
			$production_qty[$newid_ar[$row_sew["COLOR_SIZE_BREAK_DOWN_ID"]]][$row_sew["PRODUCTION_TYPE"]][$row_sew["FLOOR_ID"]][$row_sew["SEWING_LINE"]][qnty]+=$row_sew["PRODUCTION_QUANTITY"];
			
			$production_qty[$newid_ar[$row_sew["COLOR_SIZE_BREAK_DOWN_ID"]]][$row_sew["PRODUCTION_TYPE"]][$row_sew["FLOOR_ID"]][$row_sew["SEWING_LINE"]][pdate]=$row_sew["PRODUCTION_DATE"]; 
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
	else if($cbo_fr_integrtion==7)
	{
		$sql=sql_select ( "select ID, MASTER_TBLE_ID, IMAGE_LOCATION from common_photo_library where is_deleted=0 and file_type=1 and form_name='knit_order_entry' " ); // and master_tble_id in ('".implode("','",$job_ref_arr)."')
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
			$name=explode("/",$rows["IMAGE_LOCATION"]);
			foreach( $img_prod[$rows["MASTER_TBLE_ID"]] as $job  )
			{
				$txt .=$job."\t".$name[1]."\t".str_replace(".jpg","",$name[1])."\t".$name[1]."\t1\r\n";
		 		
			}

			$zipimg->addFile("../../../".$rows["IMAGE_LOCATION"]);
		}
		$zipimg->close();
		fwrite($myfile, $txt);
		fclose($myfile); 
		$txt="";
	}
	else if($cbo_fr_integrtion==8)
	{
		$buyerArr=return_library_array("select id, short_name from lib_buyer","id","short_name");
		
		$sql=sql_select("select a.ID, a.BOOKING_NO, a.BUYER_ID, a.BOOKING_DATE, b.GMTS_ITEM_ID, c.QRR_DATE, c.STYLE_REF_NO from WO_NON_ORD_SAMP_BOOKING_MST a, WO_NON_ORD_SAMP_BOOKING_DTLS b, SAMPLE_DEVELOPMENT_MST c where a.BOOKING_NO=b.BOOKING_NO and b.STYLE_ID=c.ID and a.ENTRY_FORM_ID=140 and c.ENTRY_FORM_ID=203 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by a.ID, b.ID, c.ID"); 
		
		$sampleDataArr=array();
		foreach($sql as $row)
		{
			$sampleDataArr[$row['BOOKING_NO']]['buyer']=$row['BUYER_ID'];
			$sampleDataArr[$row['BOOKING_NO']]['product']=$row['GMTS_ITEM_ID'];
			$sampleDataArr[$row['BOOKING_NO']]['qrr_date']=$row['QRR_DATE'];
			$sampleDataArr[$row['BOOKING_NO']]['style']=$row['STYLE_REF_NO'];
		}
		unset($sql);
		$txt="";
		$file_name="frfiles/PRODUCR.S.txt";
		$myfile = fopen($file_name, "w") or die("Unable to open file!");
		$txt .="P.CODE\tP.TYPE\tP.DESCRIP\tP^WC:140\tP.CUST\tP.LAUNCHDATE\r\n";
		
		foreach($sampleDataArr as $smn_booking=>$booking_data)
		{
			$txt .=str_pad($smn_booking,0," ")."\t".str_pad($garments_item[$booking_data['product']],0," ")."\t".str_pad($buyerArr[$booking_data['style']],0," ")."\t".str_pad("0",0," ")."\t".str_pad($buyerArr[$booking_data['buyer']],0," ")."\t".str_pad($booking_data['qrr_date'] == '' ? '' :date("d/m/Y",strtotime($booking_data['qrr_date'])),0," ")."\r\n";
		}
		
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
?>