<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");  
include('../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();	 
}


if ($action=="load_drop_down_party_name")
{
	
	$data=explode('_',$data);
	
	if($data[1]==1)
	{	
		$party_arr=return_library_array("select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id","company_name");
		//$value = 1;
		$value =0;
		if(count($party_arr)==1)
		{
			$value =0;
		}
		echo create_drop_down( "cbo_party_name", 150, $party_arr,"",1, "-- Select Party --", $value, "","","","","","",5 ); 
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "",0,"","","","",5);
	}
	exit();
}

if ($action == "check_conversion_rate") {
	$data = explode("**", $data);
	if ($db_type == 0) {
		$conversion_date = change_date_format($data[1], "Y-m-d", "-", 1);
	} else {
		$conversion_date = change_date_format($data[1], "d-M-y", "-", 1);
	}
	$exchange_rate = set_conversion_rate($data[0], $conversion_date, $data[2]);
	echo $exchange_rate;
	exit();
}
  
if($action=="garments_bill_list_view")
{
		echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
		//echo $data;
		$data=explode('***',$data);
		//echo "<pre>";
		//print_r($data);
		$company_name=str_replace("'","",$data[0]);
		$location_name=str_replace("'","",$data[1]);
		$cbo_party_source=str_replace("'","",$data[2]);
		$party_name=str_replace("'","",$data[3]);
		$txt_exchange_rate=str_replace("'","",$data[4]);
		$bill_date=str_replace("'","",$data[5]);
		$date_from=str_replace("'","",$data[6]);
		$date_to=str_replace("'","",$data[7]);
		$cbo_currency=str_replace("'","",$data[8]);
		$update_id=str_replace("'","",$data[9]);
		//$update_id=str_replace("'","",$data[10]);
		$delv_id="'".implode("','",explode('!!!!',$data[11]))."'";
		$delv_details_id="'".implode("','",explode('!!!!',$data[12]))."'";
	//echo $delv_id."dfdf".$delv_details_id; die;
	//-------------------------------------------------------------------------------------------------------------------------------------------------
		if($company_name!=0) $company=" and b.delivery_company_id='$company_name'"; else { echo "Please Select Company First."; die; }
		
		if($db_type==0)
		{ 
		if ($date_from!="" &&  $date_to!="") $date_cond= "and m.ex_factory_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
		$year_cond= "year(b.insert_date)as year";
		}
		else if ($db_type==2)
		{
		if ($date_from!="" &&  $date_to!="") $date_cond= "and m.ex_factory_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
		$year_cond= "TO_CHAR(b.insert_date,'YYYY') as year";
		}
		if($party_name!=0) $party_id=" and b.company_id='$party_name'"; 
		
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$party_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
		?>
		</head>
		<body>
			<div style="width:100%;">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1117px" class="rpt_table">
              		  <thead>
                 		<th width="40">&nbsp;</th>
						<th width="30">SL</th>
						<th width="90">Challan No</th>
						<th width="70">Challan Date</th>
						<th width="80">PO No.</th>
                        <th width="80">Internal Ref</th>
                        <th width="70">Job No</th>
                        <th width="90">Style Ref.</th>                    
						<th width="70">Buyer</th>
                        <th width="80">Gmts. Item</th>
						<th width="80">Color</th>
                        <th width="60">Country</th>
						<th width="60">Delivery Qty</th>
                        <th width="90">Delivery Company</th>
                        <th>LC Company</th>
					</thead>
			 </table>
        </div>
        <div style="width:1120px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100px" class="rpt_table" id="tbl_list_search">
            <?
			$i=1;
			if(!$update_id)
			{
				$sql="select  b.sys_number,b.id as delev_mst_id, m.ex_factory_date, m.po_break_down_id,d.po_number,a.job_no_mst,c.style_ref_no,b.buyer_id,a.item_number_id,a.color_number_id,a.country_id,sum(ex.production_qnty) as plan_cut_qnty,b.company_id,b.delivery_company_id,0 as type,a.color_mst_id,a.job_id,d.grouping
			from wo_po_color_size_breakdown a,
				 pro_ex_factory_mst m,
				 pro_ex_factory_dtls ex,
				 pro_ex_factory_delivery_mst b,
				 wo_po_details_master c,
				 wo_po_break_down d
				 where  
				 ex.color_size_break_down_id = a.id
				 and m.id = ex.mst_id
				 and m.delivery_mst_id=b.id
				 and c.job_no=d.job_no_mst 
				 and a.job_no_mst=c.job_no
				 and a.po_break_down_id=d.id 
				 and b.entry_form!=85  
				 and b.source=1 
				 and m.status_active = 1
				 and m.is_deleted = 0
				 and ex.status_active = 1
				 and ex.is_deleted = 0
				 and a.is_deleted = 0
				 and a.status_active in (1, 2, 3) 
				 $company $party_id $date_cond
			group by b.sys_number,b.id, m.ex_factory_date, m.po_break_down_id,d.po_number,a.job_no_mst,c.style_ref_no,b.buyer_id,a.item_number_id,a.color_number_id,a.country_id,b.company_id,b.delivery_company_id,a.color_mst_id,a.job_id,d.grouping";
			
			$sql_test ="select e.sys_number,b.delivery_mst_id as delev_mst_id,b.delivery_date as ex_factory_date,b.po_break_down_id,d.po_number,d.job_no_mst, c.style_ref_no,c.buyer_name as buyer_id,b.gmts_item_id as item_number_id,b.color_id as color_number_id,b.country_id as country_id, 
   e.company_id,e.delivery_company_id,b.color_mst_id,d.job_id,e.bill_status,d.grouping
  from subcon_inbound_bill_dtls b, wo_po_details_master c,wo_po_break_down d ,pro_ex_factory_delivery_mst e
			 where 
			 b.po_break_down_id=d.id
			 and c.job_no=d.job_no_mst 
			 and b.delivery_mst_id=e.id
			 and b.process_id=394 
			 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 
			 group by e.sys_number,b.delivery_mst_id,b.delivery_date,b.po_break_down_id,d.po_number,d.job_no_mst, c.style_ref_no,c.buyer_name,b.gmts_item_id,
 b.color_id,b.country_id,e.company_id,e.delivery_company_id,b.color_mst_id,d.job_id,e.bill_status,d.grouping";
			
			
				$sql_tst_result =sql_select($sql_test); 
				$testArr = array();
				foreach($sql_tst_result as $row)
				{
					$testArr[$row[csf('sys_number')]][$row[csf('ex_factory_date')]][$row[csf('po_number')]][$row[csf('job_no_mst')]][$row[csf('style_ref_no')]][$row[csf('buyer_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('grouping')]] = $row[csf('country_id')];
				}
			//echo $sql_test;
			
				$sql_tst_result =sql_select($sql_test); 
				$testArr = array();
				foreach($sql_tst_result as $row)
				{
					$testArr[$row[csf('sys_number')]][$row[csf('ex_factory_date')]][$row[csf('po_number')]][$row[csf('job_no_mst')]][$row[csf('style_ref_no')]][$row[csf('buyer_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('grouping')]] = $row[csf('country_id')];
				}
			
			}
			else
			{
					$sql="(select  b.sys_number,b.id as delev_mst_id, m.ex_factory_date, m.po_break_down_id,d.po_number,a.job_no_mst,c.style_ref_no,b.buyer_id,a.item_number_id,a.color_number_id,a.country_id,sum(ex.production_qnty) as plan_cut_qnty,b.company_id,b.delivery_company_id,a.color_mst_id,a.job_id,b.bill_status,d.grouping
				from wo_po_color_size_breakdown a,
					 pro_ex_factory_mst m,
					 pro_ex_factory_dtls ex,
					 pro_ex_factory_delivery_mst b,
					 wo_po_details_master c,
					 wo_po_break_down d
				 	 where  
					 ex.color_size_break_down_id = a.id
					 and m.id = ex.mst_id
					 and m.delivery_mst_id=b.id
					 and c.job_no=d.job_no_mst 
					 and a.job_no_mst=c.job_no
					 and a.po_break_down_id=d.id 
					 and b.entry_form!=85  
					 and b.source=1 
					 and m.status_active = 1
					 and m.is_deleted = 0
					 and ex.status_active = 1
					 and ex.is_deleted = 0
					 and a.is_deleted = 0
					 and a.status_active in (1, 2, 3) 
					 $company $party_id $date_cond
			group by b.sys_number,b.id, m.ex_factory_date, m.po_break_down_id,d.po_number,a.job_no_mst,c.style_ref_no,b.buyer_id,a.item_number_id,a.color_number_id,a.country_id,b.company_id,b.delivery_company_id,a.color_mst_id,a.job_id,b.bill_status,d.grouping)
					 union
					 	(select e.sys_number,b.delivery_mst_id as delev_mst_id,b.delivery_date as ex_factory_date,b.po_break_down_id,d.po_number,d.job_no_mst, c.style_ref_no,c.buyer_name as buyer_id,b.gmts_item_id as item_number_id,b.color_id as color_number_id,b.country_id as country_id,b.delivery_qty as plan_cut_qnty, 
   e.company_id,e.delivery_company_id,b.color_mst_id,d.job_id,e.bill_status,d.grouping
  from subcon_inbound_bill_dtls b, wo_po_details_master c,wo_po_break_down d ,pro_ex_factory_delivery_mst e
					 where 
					 b.po_break_down_id=d.id
					 and c.job_no=d.job_no_mst 
					 and b.delivery_mst_id=e.id
					 and b.id in ($delv_details_id) and b.process_id=394 
					 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 ) order by sys_number DESC";
					 
					 
			$sql_test ="select e.sys_number,b.delivery_mst_id as delev_mst_id,b.delivery_date as ex_factory_date,b.po_break_down_id,d.po_number,d.job_no_mst, c.style_ref_no,c.buyer_name as buyer_id,b.gmts_item_id as item_number_id,b.color_id as color_number_id,b.country_id as country_id,b.delivery_qty as plan_cut_qnty, 
   e.company_id,e.delivery_company_id,1 as type,b.color_mst_id,d.job_id,e.bill_status,d.grouping
  from subcon_inbound_bill_dtls b, wo_po_details_master c,wo_po_break_down d ,pro_ex_factory_delivery_mst e
			 where 
			 b.po_break_down_id=d.id
			 and c.job_no=d.job_no_mst 
			 and b.delivery_mst_id=e.id
			 and b.id in ($delv_details_id) and b.process_id=394 
			 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0";
			 
			 
			 $sql_test2 ="select e.sys_number,b.delivery_mst_id as delev_mst_id,b.delivery_date as ex_factory_date,b.po_break_down_id,d.po_number,d.job_no_mst, c.style_ref_no,c.buyer_name as buyer_id,b.gmts_item_id as item_number_id,b.color_id as color_number_id,b.country_id as country_id,b.delivery_qty as plan_cut_qnty, 
   e.company_id,e.delivery_company_id,1 as type,b.color_mst_id,d.job_id,e.bill_status,d.grouping
  from subcon_inbound_bill_dtls b, wo_po_details_master c,wo_po_break_down d ,pro_ex_factory_delivery_mst e
			 where 
			 b.po_break_down_id=d.id
			 and c.job_no=d.job_no_mst 
			 and b.delivery_mst_id=e.id
			 and b.process_id=394 
			 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0";

			 
			 
				$sql_tst_result2 =sql_select($sql_test2); 
				$testArr2 = array();
				foreach($sql_tst_result2 as $row)
				{
					$testArr2[$row[csf('sys_number')]][$row[csf('ex_factory_date')]][$row[csf('po_number')]][$row[csf('job_no_mst')]][$row[csf('style_ref_no')]][$row[csf('buyer_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('grouping')]] = $row[csf('country_id')];
				}
				
				
				$sql_tst_result =sql_select($sql_test); 
				$testArr = array();
				foreach($sql_tst_result as $row)
				{
					$testArr[$row[csf('sys_number')]][$row[csf('ex_factory_date')]][$row[csf('po_number')]][$row[csf('job_no_mst')]][$row[csf('style_ref_no')]][$row[csf('buyer_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('grouping')]] = $row[csf('country_id')];
				}
				
				
				
					
			}
				//echo $sql; die;
				//echo "<pre>";
				//print_r($testArr);
			//echo $sql_test;
			
			
					/*$sql_tst_result =sql_select($sql_test); 
					$testArr = array();
					foreach($sql_tst_result as $row)
					{
						$testArr[$row[csf('sys_number')]][$row[csf('ex_factory_date')]][$row[csf('po_number')]][$row[csf('job_no_mst')]][$row[csf('style_ref_no')]][$row[csf('buyer_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('grouping')]] = $row[csf('country_id')];
					}
			
					$sql_tst_result2 =sql_select($sql_test2);
					 
					$testArr2 = array();
					foreach($sql_tst_result2 as $row)
					{
						$testArr2[$row[csf('sys_number')]][$row[csf('ex_factory_date')]][$row[csf('po_number')]][$row[csf('job_no_mst')]][$row[csf('style_ref_no')]][$row[csf('buyer_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('grouping')]] = $row[csf('country_id')];
					}*/
					
				//}
			

			 $sql_result =sql_select($sql);
			

			if(!$update_id)
			{
				foreach($sql_result as $row)
				{
					
					$all_value=$row[csf('delev_mst_id')].'_'.change_date_format($row[csf('ex_factory_date')]).'_'.$row[csf('po_break_down_id')].'_'.$row[csf('item_number_id')].'_'.$row[csf('color_number_id')].'_'.$row[csf('country_id')].'_'.$row[csf('job_id')];
					
					$str_val=$i.'_'.$row[csf('delev_mst_id')].'_'.$row[csf('sys_number')].'_'.change_date_format($row[csf('ex_factory_date')]).'_'.$row[csf('po_break_down_id')].'_'.$row[csf('po_number')].'_'.$row[csf('job_no_mst')].'_'.$row[csf('style_ref_no')].'_'.$row[csf('buyer_id')].'_'.$row[csf('item_number_id')].'_'.$row[csf('color_number_id')].'_'.$row[csf('country_id')].'_'.$row[csf('company_id')].'_'.$row[csf('delivery_company_id')].'_'.$party_arr[$row[csf('buyer_id')]].'_'.$garments_item[$row[csf('item_number_id')]].'_'.$color_library[$row[csf('color_number_id')]].'_'.$country_library[$row[csf('country_id')]].'_'.$company_id[$row[csf('delivery_company_id')]].'_'.$company_id[$row[csf('company_id')]].'_'.$row[csf('plan_cut_qnty')].'_'.$row[csf('color_mst_id')].'_'.$row[csf('job_id')].'_'.$row[csf('grouping')]; 

					if(empty($testArr[$row[csf('sys_number')]][$row[csf('ex_factory_date')]][$row[csf('po_number')]][$row[csf('job_no_mst')]][$row[csf('style_ref_no')]][$row[csf('buyer_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('grouping')]]))
					{

					?>
						<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value;?>');" >
							<td width="40" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="<? echo $checked_val; ?>" <? echo $ischeck; ?> ></td>
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="90" style="word-break:break-all"><? echo $row[csf('sys_number')]; ?></td>
							<td width="70"><? echo change_date_format($row[csf('ex_factory_date')]); ?></td>
							 <td width="80" style="word-break:break-all"><? echo $row[csf('po_number')]; ?></td>
                             <td width="80" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
							 <td width="70" style="word-break:break-all"><? echo $row[csf('job_no_mst')]; ?></td>
							<td width="90" style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></td>
							<td width="70" style="word-break:break-all"><? echo $party_arr[$row[csf('buyer_id')]]; ?>&nbsp;</td>
							<td width="80" style="word-break:break-all"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
							<td width="80" style="word-break:break-all"><? echo $color_library[$row[csf('color_number_id')]]; ?></td>
							<td width="60" style="word-break:break-all"><? echo $country_library[$row[csf('country_id')]]; ?></td>
							<td width="60" style="word-break:break-all" align="center"><? echo $row[csf('plan_cut_qnty')]; ?></td>
							<td width="90" style="word-break:break-all"><? echo $company_id[$row[csf('delivery_company_id')]]; ?></td> 
							<td style="word-break:break-all"><? echo $company_id[$row[csf('company_id')]]; ?>
							<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
							</td>
						</tr>
						<?php 
						$i++;
					}
				}
			}
			else
			{
				foreach($sql_result as $row)// update row 
				{
					$all_value=$row[csf('delev_mst_id')].'_'.change_date_format($row[csf('ex_factory_date')]).'_'.$row[csf('po_break_down_id')].'_'.$row[csf('item_number_id')].'_'.$row[csf('color_number_id')].'_'.$row[csf('country_id')].'_'.$row[csf('job_id')];
					$str_val=$i.'_'.$row[csf('delev_mst_id')].'_'.$row[csf('sys_number')].'_'.change_date_format($row[csf('ex_factory_date')]).'_'.$row[csf('po_break_down_id')].'_'.$row[csf('po_number')].'_'.$row[csf('job_no_mst')].'_'.$row[csf('style_ref_no')].'_'.$row[csf('buyer_id')].'_'.$row[csf('item_number_id')].'_'.$row[csf('color_number_id')].'_'.$row[csf('country_id')].'_'.$row[csf('company_id')].'_'.$row[csf('delivery_company_id')].'_'.$party_arr[$row[csf('buyer_id')]].'_'.$garments_item[$row[csf('item_number_id')]].'_'.$color_library[$row[csf('color_number_id')]].'_'.$country_library[$row[csf('country_id')]].'_'.$company_id[$row[csf('delivery_company_id')]].'_'.$company_id[$row[csf('company_id')]].'_'.$row[csf('plan_cut_qnty')].'_'.$row[csf('color_mst_id')].'_'.$row[csf('job_id')].'_'.$row[csf('grouping')]; 
										
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$checked_val=2; $ischeck="";
						if(!empty($testArr[$row[csf('sys_number')]][$row[csf('ex_factory_date')]][$row[csf('po_number')]][$row[csf('job_no_mst')]][$row[csf('style_ref_no')]][$row[csf('buyer_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('grouping')]]))
						{
							$bgcolor="yellow";
							$checked_val=1;
							$ischeck="checked";
						}
						else
						{
							$row_color=$bgcolor;
							$checked_val=2;
							$ischeck="";
						}
					
					if(!empty($testArr[$row[csf('sys_number')]][$row[csf('ex_factory_date')]][$row[csf('po_number')]][$row[csf('job_no_mst')]][$row[csf('style_ref_no')]][$row[csf('buyer_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('grouping')]]))
					{
						
						?>
						<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value;?>');" >
							<td width="40" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="<? echo $checked_val; ?>" <? echo $ischeck; ?> ></td>
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="90" style="word-break:break-all"><? echo $row[csf('sys_number')]; ?></td>
							<td width="70"><? echo change_date_format($row[csf('ex_factory_date')]); ?></td>
							 <td width="80" style="word-break:break-all"><? echo $row[csf('po_number')]; ?></td>
                             <td width="80" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
							 <td width="70" style="word-break:break-all"><? echo $row[csf('job_no_mst')]; ?></td>
							<td width="90" style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></td>
							<td width="70" style="word-break:break-all"><? echo $party_arr[$row[csf('buyer_id')]]; ?>&nbsp;</td>
							<td width="80" style="word-break:break-all"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
							<td width="80" style="word-break:break-all"><? echo $color_library[$row[csf('color_number_id')]]; ?></td>
							<td width="60" style="word-break:break-all"><? echo $country_library[$row[csf('country_id')]]; ?></td>
							<td width="60" style="word-break:break-all" align="center"><? echo $row[csf('plan_cut_qnty')]; ?></td>
							<td width="90" style="word-break:break-all"><? echo $company_id[$row[csf('delivery_company_id')]]; ?></td> 
							<td style="word-break:break-all"><? echo $company_id[$row[csf('company_id')]]; ?>
							<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
							</td>
						</tr>
						<?php 
						$i++;
					}
				}
				foreach($sql_result as $row) // new row  
				{
					$all_value=$row[csf('delev_mst_id')].'_'.change_date_format($row[csf('ex_factory_date')]).'_'.$row[csf('po_break_down_id')].'_'.$row[csf('item_number_id')].'_'.$row[csf('color_number_id')].'_'.$row[csf('country_id')].'_'.$row[csf('job_id')];
					$str_val=$i.'_'.$row[csf('delev_mst_id')].'_'.$row[csf('sys_number')].'_'.change_date_format($row[csf('ex_factory_date')]).'_'.$row[csf('po_break_down_id')].'_'.$row[csf('po_number')].'_'.$row[csf('job_no_mst')].'_'.$row[csf('style_ref_no')].'_'.$row[csf('buyer_id')].'_'.$row[csf('item_number_id')].'_'.$row[csf('color_number_id')].'_'.$row[csf('country_id')].'_'.$row[csf('company_id')].'_'.$row[csf('delivery_company_id')].'_'.$party_arr[$row[csf('buyer_id')]].'_'.$garments_item[$row[csf('item_number_id')]].'_'.$color_library[$row[csf('color_number_id')]].'_'.$country_library[$row[csf('country_id')]].'_'.$company_id[$row[csf('delivery_company_id')]].'_'.$company_id[$row[csf('company_id')]].'_'.$row[csf('plan_cut_qnty')].'_'.$row[csf('color_mst_id')].'_'.$row[csf('job_id')].'_'.$row[csf('grouping')]; 
										
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$checked_val=2; $ischeck="";
					if(!empty($testArr2[$row[csf('sys_number')]][$row[csf('ex_factory_date')]][$row[csf('po_number')]][$row[csf('job_no_mst')]][$row[csf('style_ref_no')]][$row[csf('buyer_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('grouping')]]))
					{
						$bgcolor="yellow";
						$checked_val=1;
						$ischeck="checked";
					}
					else
					{
						$row_color=$bgcolor;
						$checked_val=2;
						$ischeck="";
					}
					if(empty($testArr2[$row[csf('sys_number')]][$row[csf('ex_factory_date')]][$row[csf('po_number')]][$row[csf('job_no_mst')]][$row[csf('style_ref_no')]][$row[csf('buyer_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('grouping')]]))
					{
						?>
						<tr id="tr_<? echo $all_value; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value;?>');" >
							<td width="40" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="<? echo $checked_val; ?>" <? echo $ischeck; ?> ></td>
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="90" style="word-break:break-all"><? echo $row[csf('sys_number')]; ?></td>
							<td width="70"><? echo change_date_format($row[csf('ex_factory_date')]); ?></td>
							<td width="80" style="word-break:break-all"><? echo $row[csf('po_number')]; ?></td>
                            <td width="80" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
							<td width="70" style="word-break:break-all"><? echo $row[csf('job_no_mst')]; ?></td>
							<td width="90" style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></td>
							<td width="70" style="word-break:break-all"><? echo $party_arr[$row[csf('buyer_id')]]; ?>&nbsp;</td>
							<td width="80" style="word-break:break-all"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
							<td width="80" style="word-break:break-all"><? echo $color_library[$row[csf('color_number_id')]]; ?></td>
							<td width="60" style="word-break:break-all"><? echo $country_library[$row[csf('country_id')]]; ?></td>
							<td width="60" style="word-break:break-all" align="center"><? echo $row[csf('plan_cut_qnty')]; ?></td>
							<td width="90" style="word-break:break-all"><? echo $company_id[$row[csf('delivery_company_id')]]; ?></td> 
							<td style="word-break:break-all"><? echo $company_id[$row[csf('company_id')]]; ?>
							<input type="hidden" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
							</td>

						</tr>
						<?php 
						$i++;
					}
				}
			}
			?>
         </table>
         </div>
        <div>
            <table width="920">
                <tr style="border:none">
                    <td bgcolor="#7FDF00" align="center"><input type="checkbox" name="checkall" id="checkall" class="formbutton" value="2" onClick="check_all_data();"/><b>Check all</b></td>
                    <td bgcolor="#FF80FF" align="center"><input type="button" id="show_button" align="middle" class="formbutton" style="width:100px" value="Close" onClick="window_close(0);" /></td>
                </tr>
           </table>
      	</div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="bill_no_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	$ex_data=explode('_',$data);
	
	//print_r($ex_data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('issue_id').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_popup(type,party_source)
		{
			var company = $('#cbo_company_id').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			load_drop_down( 'ex_factory_against_garments_bill_entry_controller', company+'_'+party_source, 'load_drop_down_party_name', 'party_td' );
			load_drop_down( 'ex_factory_against_garments_bill_entry_controller', company, 'load_drop_down_location', 'location_td');
		}
	</script>
	</head>
	<body onLoad="fnc_load_party_popup(<? echo "1";?>,<? echo $ex_data[3];?>)">
        <div align="center" style="width:100%;" >
            <form name="knittingbill_1"  id="knittingbill_1" autocomplete="off">
                <table width="780" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                     <tr>
                         <th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",'1' ); ?></th>
                    </tr>
                     <tr>
                        <th width="130">Company Name</th>
                        <th width="130">Party Name</th>
                        <th width="120">Location</th>
                        <th width="70">Bill ID</th>
                        <th width="170" colspan="2">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>           
                     </tr>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="issue_id">  
								<?   
									
									echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"fnc_load_party_popup(1,".$ex_data[3].");",0 );
									
                                ?>
                            </td>
                            <td width="130" id="party_td">
								<?
									echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", 0, "","","","","","",5 );
                                ?> 
                            </td>
                            <td id="location_td">
								<?
									echo create_drop_down( "cbo_location_name", 150, $blank_loc,"", 1, "--Select Location--", $selected,"","","","","","",3);
								?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:65px" placeholder="Write" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<? echo $data; ?>', 'bill_list_view', 'search_div', 'ex_factory_against_garments_bill_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <div id="search_div" style="margin-top:10px;"></div>   
            </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="bill_list_view")
{
	$data=explode('_',$data);
	//print_r($data);
	if ($data[0]!=0) $company_name=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $party_name_cond=" and a.party_id='$data[1]'"; else $party_name_cond="";
	$date_from=str_replace("'","",$data[2]);
	$date_to=str_replace("'","",$data[3]);
	$bill_no_prefx=str_replace("'","",$data[4]);
	$location_name=str_replace("'","",$data[5]);
	if($location_name!=0) $location_id=" and a.location_id='$location_name'";
	if($bill_no_prefx!='')
	{
		if ($bill_no_prefx!='') $search_bill_cond=" and a.prefix_no_num='$bill_no_prefx'"; else $search_bill_cond="";
	}

	//-------------------------------------------------------------------------------------------------------------------------------------------------
	if($db_type==0)
	{ 
	if ($date_from!="" &&  $date_to!="") $date_cond= "and a.bill_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
	}
	else if ($db_type==2)
	{
	if ($date_from!="" &&  $date_to!="") $date_cond= "and a.bill_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
	}
	$sql= "select a.id,a.bill_no,a.prefix_no_num,a.location_id,a.bill_date,a.party_id,a.bill_for from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.process_id=394  $party_name_cond $company_name $location_id $date_cond $search_bill_cond
	group by a.id,a.bill_no,a.prefix_no_num,a.location_id,a.bill_date,a.party_id,a.bill_for order by a.id DESC";
	
	//echo $sql; die;
	$location=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		
	
	?> 
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="70">Bill No</th>
                <th width="120">Location</th>
                <th width="80">Bill Date</th>
                <th width="120">Party</th>
            </thead>
     	</table>
     </div>
     <div style="width:750px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_po_list">
			<?
			$i=1; $result = sql_select($sql);
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")]; ?>);" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row[csf("prefix_no_num")]; ?></td>
						<td width="120"><? echo $location[$row[csf("location_id")]];  ?></td>	
						<td width="80"><? echo change_date_format($row[csf("bill_date")]); ?></td>
						<td width="120"><? echo $party_arr[$row[csf("party_id")]];?> </td>	
					</tr>
				<? 
				$i++;
            }
			?>
			</table>
		</div> 
	<?	
	exit();
}

if ($action=="load_php_data_to_form_issue")
{
	$ex_data=explode('_',$data);
	//print_r($ex_data);
	
	 $sql="SELECT min(delivery_date) as min_date, max(delivery_date) as max_date FROM subcon_inbound_bill_dtls WHERE mst_id='$ex_data[0]' and status_active=1 and is_deleted=0 group by mst_id";
	$sql_result_arr =sql_select($sql); 
	$mindate='';  $maxdate='';
	$mindate=$sql_result_arr[0][csf('min_date')];
	$maxdate=$sql_result_arr[0][csf('max_date')];
	unset($sql_result_arr);
	//id, prefix_no, prefix_no_num, bill_no, company_id,party_source, location_id,bill_date, party_id, process_id,currency,exchange_rate,remarks
	$nameArray= sql_select("select id, bill_no, company_id, location_id,party_location_id, bill_date, party_id, party_source, attention, bill_for, is_posted_account,post_integration_unlock,currency,exchange_rate,remarks from subcon_inbound_bill_mst where id=$ex_data[0]");
	foreach ($nameArray as $row)
	{
			
		echo "document.getElementById('txt_bill_no').value 					= '".$row[csf("bill_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_party_name').value				= '".$row[csf("party_id")]."';\n"; 
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value			= '".$row[csf("exchange_rate")]."';\n"; 
		echo "document.getElementById('txt_bill_date').value 				= '".change_date_format($row[csf("bill_date")])."';\n";   
		echo "document.getElementById('txt_del_date_from').value 			= '".change_date_format($mindate)."';\n";  
		echo "document.getElementById('txt_del_date_to').value 			    = '".change_date_format($maxdate)."';\n";  
	    echo "document.getElementById('cbo_currency').value            		= '".$row[csf("currency")]."';\n"; 
		echo "document.getElementById('txt_remarks').value            		= '".$row[csf("remarks")]."';\n"; 
		echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('is_posted_account').value            = '".$row[csf("is_posted_account")]."';\n";
		
		
		//echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_party_source*cbo_party_name*cbo_bill_for',1);\n";
		//echo "fnc_disable_mst_field(document.getElementById('cbo_party_name').value);\n";
	}	
	exit();
}
if ($action=="load_dtls_data") 
{
	$ex_data=explode("!^!",$data);
	$upid=$ex_data[0];
	//var_dump($order_array);
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$party_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	
	 $sql="select id as upd_id,mst_id,delivery_mst_id,delivery_date,delivery_qty,rate,amount,remarks,process_id,color_id,country_id,color_mst_id,po_break_down_id,gmts_item_id,job_id from subcon_inbound_bill_dtls where mst_id=$upid and process_id=394 and status_active=1 and is_deleted=0 and process_id=394 order by challan_no ASC";
	$sql_result_arr =sql_select($sql);
	
	$delivery_id_arr=array();
	foreach ($sql_result_arr as $row)
	{
		$delivery_id_arr[$row[csf('delivery_mst_id')]]=$row[csf('delivery_mst_id')];
		
	}
	$delever_order_arr=array();
		
	 $sql_delever="select  b.sys_number,b.id as delev_mst_id, m.ex_factory_date, m.po_break_down_id,d.po_number,a.job_no_mst,c.style_ref_no,b.buyer_id,a.item_number_id,a.color_number_id,a.country_id,sum(ex.production_qnty) as plan_cut_qnty,b.company_id,b.delivery_company_id,0 as type,a.color_mst_id,a.job_id,d.grouping
				from wo_po_color_size_breakdown a,
					 pro_ex_factory_mst m,
					 pro_ex_factory_dtls ex,
					 pro_ex_factory_delivery_mst b,
					 wo_po_details_master c,
					 wo_po_break_down d
				 	 where  
					 ex.color_size_break_down_id = a.id
					 and m.id = ex.mst_id
					 and m.delivery_mst_id=b.id
					 and c.job_no=d.job_no_mst 
					 and a.job_no_mst=c.job_no
					 and a.po_break_down_id=d.id 
					 and b.entry_form!=85  
					 and b.source=1 
					 and m.status_active = 1
					 and m.is_deleted = 0
					 and ex.status_active = 1
					 and ex.is_deleted = 0
					 and a.is_deleted = 0
					 and a.status_active in (1, 2, 3) and b.id in(".implode(',', $delivery_id_arr).")  
			group by b.sys_number,b.id, m.ex_factory_date, m.po_break_down_id,d.po_number,a.job_no_mst,c.style_ref_no,b.buyer_id,a.item_number_id,a.color_number_id,a.country_id,b.company_id,b.delivery_company_id,a.color_mst_id,a.job_id,d.grouping"; 
			
			
		$sql_delever_result =sql_select($sql_delever);
		foreach($sql_delever_result as $dele_row)
		{
			
			$all_dele_value=$dele_row[csf('delev_mst_id')].'_'.change_date_format($dele_row[csf('ex_factory_date')]).'_'.$dele_row[csf('po_break_down_id')].'_'.$dele_row[csf('item_number_id')].'_'.$dele_row[csf('color_number_id')].'_'.$dele_row[csf('country_id')].'_'.$dele_row[csf('job_id')];
			
			/*$delever_order_arr[$dele_row[csf('delev_mst_id')]][$dele_row[csf('po_break_down_id')]]['sys_number']=$dele_row[csf('sys_number')];
			$delever_order_arr[$dele_row[csf('delev_mst_id')]][$dele_row[csf('po_break_down_id')]]['po_number']=$dele_row[csf('po_number')];
			$delever_order_arr[$dele_row[csf('delev_mst_id')]][$dele_row[csf('po_break_down_id')]]['job_no_mst']=$dele_row[csf('job_no_mst')];
			$delever_order_arr[$dele_row[csf('delev_mst_id')]][$dele_row[csf('po_break_down_id')]]['style_ref_no']=$dele_row[csf('style_ref_no')];
			$delever_order_arr[$dele_row[csf('delev_mst_id')]][$dele_row[csf('po_break_down_id')]]['buyer_id']=$dele_row[csf('buyer_id')];
			$delever_order_arr[$dele_row[csf('delev_mst_id')]][$dele_row[csf('po_break_down_id')]]['company_id']=$dele_row[csf('company_id')];
			$delever_order_arr[$dele_row[csf('delev_mst_id')]][$dele_row[csf('po_break_down_id')]]['delivery_company_id']=$dele_row[csf('delivery_company_id')];
			$delever_order_arr[$dele_row[csf('delev_mst_id')]][$dele_row[csf('po_break_down_id')]]['job_id']=$dele_row[csf('job_id')];
			$delever_order_arr[$dele_row[csf('delev_mst_id')]][$dele_row[csf('po_break_down_id')]]['grouping']=$dele_row[csf('grouping')];*/
			
			$delever_order_arr[$all_dele_value]['sys_number']=$dele_row[csf('sys_number')];
			$delever_order_arr[$all_dele_value]['po_number']=$dele_row[csf('po_number')];
			$delever_order_arr[$all_dele_value]['job_no_mst']=$dele_row[csf('job_no_mst')];
			$delever_order_arr[$all_dele_value]['style_ref_no']=$dele_row[csf('style_ref_no')];
			$delever_order_arr[$all_dele_value]['buyer_id']=$dele_row[csf('buyer_id')];
			$delever_order_arr[$all_dele_value]['company_id']=$dele_row[csf('company_id')];
			$delever_order_arr[$all_dele_value]['delivery_company_id']=$dele_row[csf('delivery_company_id')];
			$delever_order_arr[$all_dele_value]['job_id']=$dele_row[csf('job_id')];
			$delever_order_arr[$all_dele_value]['grouping']=$dele_row[csf('grouping')];
			
			
		}
		unset($sql_delever_result);
	
	 $str_val="";
	$i=1;
	foreach ($sql_result_arr as $row) // delivery_id $job_order_arr[$row[csf('delivery_id')]]['work_order_id']
	{
		
		$all_value=$row[csf('delivery_mst_id')].'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('po_break_down_id')].'_'.$row[csf('gmts_item_id')].'_'.$row[csf('color_id')].'_'.$row[csf('country_id')].'_'.$row[csf('job_id')];
		
		/*	$sys_number=$delever_order_arr[$row[csf('delivery_mst_id')]][$row[csf('po_break_down_id')]]['sys_number']; 
			$po_number=$delever_order_arr[$row[csf('delivery_mst_id')]][$row[csf('po_break_down_id')]]['po_number'];
			$job_no_mst=$delever_order_arr[$row[csf('delivery_mst_id')]][$row[csf('po_break_down_id')]]['job_no_mst'];
			$style_ref_no=$delever_order_arr[$row[csf('delivery_mst_id')]][$row[csf('po_break_down_id')]]['style_ref_no'];
			$buyer_id=$delever_order_arr[$row[csf('delivery_mst_id')]][$row[csf('po_break_down_id')]]['buyer_id'];
			$companyid=$delever_order_arr[$row[csf('delivery_mst_id')]][$row[csf('po_break_down_id')]]['company_id'];
			$delivery_company_id=$delever_order_arr[$row[csf('delivery_mst_id')]][$row[csf('po_break_down_id')]]['delivery_company_id'];
			$job_id=$delever_order_arr[$row[csf('delivery_mst_id')]][$row[csf('po_break_down_id')]]['job_id'];
			$grouping=$delever_order_arr[$row[csf('delivery_mst_id')]][$row[csf('po_break_down_id')]]['grouping'];*/
			
			
			$sys_number=$delever_order_arr[$all_value]['sys_number']; 
			$po_number=$delever_order_arr[$all_value]['po_number'];
			$job_no_mst=$delever_order_arr[$all_value]['job_no_mst'];
			$style_ref_no=$delever_order_arr[$all_value]['style_ref_no'];
			$buyer_id=$delever_order_arr[$all_value]['buyer_id'];
			$companyid=$delever_order_arr[$all_value]['company_id'];
			$delivery_company_id=$delever_order_arr[$all_value]['delivery_company_id'];
			$job_id=$delever_order_arr[$all_value]['job_id'];
			$grouping=$delever_order_arr[$all_value]['grouping'];
			
			//echo "<pre>";
			/*print_r($row); 
			echo $row[csf('delivery_mst_id')];*/  //$i
			//die;
			
			
			//,1228,,19-04-2020,38627,,,,,151,89,5,,,,Polo Shirt,GREEN,Albania,,,39,326622,,8.0000,312,ui,1608
	
		if($str_val=="") $str_val=$i.'_'.$row[csf('delivery_mst_id')].'_'.$sys_number.'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('po_break_down_id')].'_'.$po_number.'_'.$job_no_mst.'_'.$style_ref_no.'_'.$buyer_id.'_'.$row[csf('gmts_item_id')].'_'.$row[csf('color_id')].'_'.$row[csf('country_id')].'_'.$companyid.'_'.$delivery_company_id.'_'.$party_arr[$buyer_id].'_'.$garments_item[$row[csf('gmts_item_id')]].'_'.$color_library[$row[csf('color_id')]].'_'.$country_library[$row[csf('country_id')]].'_'.$company_id[$delivery_company_id].'_'.$company_id[$companyid].'_'.$row[csf('delivery_qty')].'_'.$row[csf('color_mst_id')].'_'.$job_id.'_'.number_format($row[csf('rate')],4,'.','').'_'.$row[csf('amount')].'_'.$row[csf('remarks')].'_'.$row[csf('upd_id')].'_'.$grouping;
			else $str_val.="###".$i.'_'.$row[csf('delivery_mst_id')].'_'.$sys_number.'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('po_break_down_id')].'_'.$po_number.'_'.$job_no_mst.'_'.$style_ref_no.'_'.$buyer_id.'_'.$row[csf('gmts_item_id')].'_'.$row[csf('color_id')].'_'.$row[csf('country_id')].'_'.$companyid.'_'.$delivery_company_id.'_'.$party_arr[$buyer_id].'_'.$garments_item[$row[csf('gmts_item_id')]].'_'.$color_library[$row[csf('color_id')]].'_'.$country_library[$row[csf('country_id')]].'_'.$company_id[$delivery_company_id].'_'.$company_id[$companyid].'_'.$row[csf('delivery_qty')].'_'.$row[csf('color_mst_id')].'_'.$job_id.'_'.number_format($row[csf('rate')],4,'.','').'_'.$row[csf('amount')].'_'.$row[csf('remarks')].'_'.$row[csf('upd_id')].'_'.$grouping;
	
	$i++;
					
	}
	echo $str_val; 
	exit();
}
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$bill_process_id="394";//entry form 
	
	
	
	if ($operation==0)   // Insert Here========================================================================================delivery_id
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		/*if (is_duplicate_field( "delivery_id", "subcon_inbound_bill_dtls", "mst_id=$update_id" )==1)
		{
			echo "11**0"; 
			die;			
		}*/
		if($db_type==0)$year_cond=" and YEAR(insert_date)";	
		else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		
		$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'GBI', date("Y",time()), 5, "select prefix_no, prefix_no_num from  subcon_inbound_bill_mst where company_id=$cbo_company_name and process_id=$bill_process_id $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		if(str_replace("'",'',$update_id)=="")
		{
			$id=return_next_id( "id", "subcon_inbound_bill_mst", 1 ) ; 	
			$field_array="id, prefix_no, prefix_no_num, bill_no, company_id,party_source, location_id,bill_date, party_id, process_id,currency,exchange_rate,remarks, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_name.",".$cbo_party_source.",".$cbo_location_name.",".$txt_bill_date.",".$cbo_party_name.",".$bill_process_id.",".$cbo_currency.",".$txt_exchange_rate.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			
			$return_no=$new_bill_no[0]; 
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="company_id*party_source*location_id*bill_date*party_id*process_id*currency*exchange_rate*remarks*updated_by*update_date";
			$data_array="".$cbo_company_name."*".$cbo_party_source."*".$cbo_location_name."*".$txt_bill_date."*".$cbo_party_name."*".$bill_process_id."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$return_no=str_replace("'",'',$txt_bill_no);
		}
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		//----------------------------------------------------------------------
		$field_array1 ="id, mst_id,delivery_mst_id,delivery_date,delivery_qty,rate,amount,remarks,process_id,color_id,country_id,color_mst_id,po_break_down_id,gmts_item_id,domestic_amount,job_id,inserted_by,insert_date";
		$field_array_up ="delivery_mst_id*delivery_date*delivery_qty*rate*amount*remarks*process_id*color_id*country_id*color_mst_id*po_break_down_id*gmts_item_id*domestic_amount*job_id*updated_by*update_date";
		
		
		$field_array_delivery="bill_status";
		$process_id=394;
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			
			$delivery_id="deliveryid_".$i;
			$delevery_date="deleverydate_".$i;
			$quantity="deliveryqnty_".$i;
			$rate="txtrate_".$i;
			$amount="amount_".$i;
			$remarksname="remarks_".$i;
			$txtColorId="txtColorId_".$i;
			$jobid="jobid_".$i;
			$cbocountry="cbocountry_".$i;
			$colormstid="colormstid_".$i;
			$orderid="ordernoid_".$i;
			$cboitemname="cboitemname_".$i;
			$updateid_dtls="updateiddtls_".$i;
			
			$txtdomisticamount=str_replace("'",'',$txt_exchange_rate)*str_replace("'",'',$$amount);
			
			//echo "10**".$txtdomisticamount; die;
			
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{
				if($$amount!="")
				{
					if ($add_comma!=0) $data_array1 .=",";
					$data_array1 .="(".$id1.",".$id.",".$$delivery_id.",".$$delevery_date.",".$$quantity.",".$$rate.",".$$amount.",".$$remarksname.",".$process_id.",".$$txtColorId.",".$$cbocountry.",".$$colormstid.",".$$orderid.",".$$cboitemname.",".$txtdomisticamount.",".$$jobid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id1=$id1+1;
					$add_comma++;
				}
				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$delivery_id."*".$$delevery_date."*".$$quantity."*".$$rate."*".$$amount."*".$$remarksname."*".$process_id."*".$$txtColorId."*".$$cbocountry."*".$$colormstid."*".$$orderid."*".$$cboitemname."*".$txtdomisticamount."*".$$jobid."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				$data_array_delivery[str_replace("'",'',$$delivery_id)] =explode("*",("1"));
			}
			
		}
		$flag=1;
		if(str_replace("'",'',$update_id)=="")
		{
			//echo "INSERT INTO subcon_inbound_bill_mst (".$field_array.") VALUES ".$data_array; die; 
			$rID=sql_insert("subcon_inbound_bill_mst",$field_array,$data_array,1);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($data_array1!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1;die;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,1);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		$rID2=execute_query(bulk_update_sql_statement( "pro_ex_factory_delivery_mst", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
		
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		//$rID2=execute_query(bulk_update_sql_statement( "subcon_delivery_dtls", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
		//if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$rID.'='.$rID1.'='.$rID2.'='.$flag; die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
			}
		}	
		
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here=============================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=str_replace("'",'',$update_id);
		
		$nameArray= sql_select("select is_posted_account,post_integration_unlock from subcon_inbound_bill_mst where id='$id'");
		$posted_account=$nameArray[0][csf('is_posted_account')];
		$post_integration_unlock=$nameArray[0][csf('post_integration_unlock')];
		if($posted_account==1 && $post_integration_unlock==0)
		{
			echo "14**All Ready Posted in Accounting.";
			disconnect($con);
			exit();
		}
		
		
		
		$field_array="company_id*party_source*location_id*bill_date*party_id*process_id*currency*exchange_rate*remarks*updated_by*update_date";
		$data_array="".$cbo_company_name."*".$cbo_party_source."*".$cbo_location_name."*".$txt_bill_date."*".$cbo_party_name."*".$bill_process_id."*".$cbo_currency."*".$txt_exchange_rate."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		 
		$sql_dtls="Select id,delivery_mst_id from subcon_inbound_bill_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$nameArray=sql_select( $sql_dtls );
		foreach($nameArray as $row)
		{
			$dtls_update_id_array[]=$row[csf('id')];
			$dtls_update_delivery_id_array[$row[csf('id')]]=$row[csf('delivery_mst_id')];
		}
		 
		$return_no=str_replace("'",'',$txt_bill_no);
		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		
		
			$field_array1 ="id, mst_id,delivery_mst_id,delivery_date,delivery_qty,rate,amount,remarks,process_id,color_id,country_id,color_mst_id,po_break_down_id,gmts_item_id,domestic_amount,job_id,inserted_by,insert_date";
		$field_array_up ="delivery_mst_id*delivery_date*delivery_qty*rate*amount*remarks*process_id*color_id*country_id*color_mst_id*po_break_down_id*gmts_item_id*domestic_amount*job_id*updated_by*update_date";
		$field_array_delivery="bill_status";
		$process_id=394;
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$delivery_id="deliveryid_".$i;
			$delevery_date="deleverydate_".$i;
			$quantity="deliveryqnty_".$i;
			$rate="txtrate_".$i;
			$amount="amount_".$i;
			$jobid="jobid_".$i;
			$remarksname="remarks_".$i;
			$txtColorId="txtColorId_".$i;
			$cbocountry="cbocountry_".$i;
			$colormstid="colormstid_".$i;
			$orderid="ordernoid_".$i;
			$cboitemname="cboitemname_".$i;
			$updateid_dtls="updateiddtls_".$i;
			
			$txtdomisticamount=str_replace("'",'',$txt_exchange_rate)*str_replace("'",'',$$amount);
			//echo "10**".$txtdomisticamount; die;
			//echo $up_id=str_replace("'",'',$$updateid_dtls);
			
			
			if(str_replace("'",'',$$updateid_dtls)=="")  
			{ 
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$delivery_id.",".$$delevery_date.",".$$quantity.",".$$rate.",".$$amount.",".$$remarksname.",".$process_id.",".$$txtColorId.",".$$cbocountry.",".$$colormstid.",".$$orderid.",".$$cboitemname.",".$txtdomisticamount.",".$$jobid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$delivery_id."*".$$delevery_date."*".$$quantity."*".$$rate."*".$$amount."*".$$remarksname."*".$process_id."*".$$txtColorId."*".$$cbocountry."*".$$colormstid."*".$$orderid."*".$$cboitemname."*".$txtdomisticamount."*".$$jobid."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$id_arr_delivery[]=str_replace("'",'',$$delivery_id);
				$id_arr_deli=implode(',',explode('_',str_replace("'",'',$$delivery_id)));
				$delv_id=explode(',',$id_arr_deli);
				foreach($delv_id as $val)
				{
					$id_arr_delivery[]=$val;
					$data_array_delivery[$val] =explode("*",("1"));
				}
				
			}
			
		}
		
		
		//echo $distance_delete_id; die;
		//echo bulk_update_sql_statement( "subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ); die;
		
		$flag=1;
		if($update_id!="")
		{
				$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
				if($rID==1 && $flag==1) $flag=1; else $flag=0;
				$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
				if($rID1==1 && $flag==1) $flag=1; else $flag=0;
			
		}
		
		if($data_array1!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1;
			$rID7=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,0);
			if($rID7==1 && $flag==1) $flag=1; else $flag=0;
		}
	
			if(implode(',',$id_arr)!="")
			{
				$distance_delete_id=implode(',',array_diff($dtls_update_id_array,$id_arr));
			}
			else
			{
				$distance_delete_id=implode(',',$dtls_update_id_array);
			}
			$new_delete_id=implode(",",array_diff($dtls_update_id_array,$id_arr));
			$all_delt_id=explode(",",$new_delete_id);
			//echo "10**"."mahbub<pre>";
			//print_r($all_delt_id);
			$data_array_status_up=array();
			//$id_delete_arr = array();
			foreach($all_delt_id as $val)
			{
				if($val != '')
				{
					$id_delete_arr[]=$val;
					$data_array_status_up[$val] =explode("*",("0*1"));
					$all_delver_id[]=$dtls_update_delivery_id_array[$val];
				}
			}
			
			if(!empty($id_delete_arr))
			{
				$field_array_status_up="status_active*is_deleted";
				$rID3=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_status_up,$data_array_status_up,$id_delete_arr ));
				if($rID3==1) $flag=1; else $flag=0;
			}
			if(!empty($id_arr_delivery))
			{
				$rID2=execute_query(bulk_update_sql_statement( "pro_ex_factory_delivery_mst", "id",$field_array_delivery,$data_array_delivery,$id_arr_delivery ));
				if($rID2==1 && $flag==1) $flag=1; else $flag=0;
			}
		
			$data_array_bill_status_up=array();
			foreach($all_delver_id as $val)
			{
				if($val != '')
				{
					$id_delver_bill_arr[]=$val;
					$data_array_bill_status_up[$val] =explode("*",("0"));
				}
			}
			if(!empty($id_delver_bill_arr))
			{
				//echo "10**".bulk_update_sql_statement("subcon_production_dtls", "id",$field_array_delivery,$data_array_bill_status_up,$id_delver_bill_arr ); die;
				$rID6=execute_query(bulk_update_sql_statement("pro_ex_factory_delivery_mst", "id",$field_array_delivery,$data_array_bill_status_up,$id_delver_bill_arr ));
				if($rID6==1 && $flag==1) $flag=1; else $flag=0;
			}
			//echo "10**".$rID3.'-'.$rID2.'-'.$rID6; die;
			
			//	10**1-1----0
			
			//10**1-1-1----1
		//echo "10**".$rID6; die;
		
		//1-0-1---1-0
	//echo "10**".$rID.'-'.$rID1.'-'.$rID2.'-'.$rID3.'-'.$rID6.'-'.$rID7.'-'.$flag; die;
				
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$posted_account);
			}
			else
			{
				mysql_query("ROLLBACK"); 

				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$posted_account);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}	
		disconnect($con);
		die;
	}
	
}
if($action=="garments_bill_issue_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$delv_id="'".implode("','",explode('!!!!',$data[3]))."'";
	
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	
	  $sql_mst="select id, prefix_no_num, bill_no, location_id, bill_date, party_id, party_source, process_id, party_location_id, remarks,currency from subcon_inbound_bill_mst where process_id=394 and status_active=1 and is_deleted=0 and id='$data[1]'";
	$dataArray = sql_select($sql_mst);
	?>
    <div style="width:1030px;">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="100" align="right"> 
                    <img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
                </td>
                <td>
                    <table width="1100" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong><? echo $data[2]; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
         <br>
        <table width="100%" cellpadding="0" cellspacing="0" >  
       	 <tr>
            	<td width="130"><strong>Bill No:</strong></td>
                <td width="175"><? echo $dataArray[0][csf('bill_no')]; ?></td>
            	<td width="130"><strong>Bill Date:</strong></td>
                <td width="175"><? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="130"><strong>Source:</strong></td>
                <td><? echo $knitting_source[$dataArray[0][csf('party_source')]]; ?></td>
                <td width="130"><strong>Currency:</strong></td>
                <td><? echo $currency[$dataArray[0][csf('currency')]]; ?></td>
            </tr>
            
            <tr>
            	<td><strong>Party Name:</strong></td>
                <td><? echo $company_library[$dataArray[0][csf('party_id')]]; ?></td>
            	<td><strong>Address:</strong></td>
                <td colspan="5"><? echo $location_arr[$dataArray[0][csf('location_id')]];//party_address; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="1030" border="1" rules="all" class="rpt_table" style="font-size:12px">
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="100">Challan No</th>
                    <th width="70">Challan Date</th>
                    <th width="80">Order</th>
                    <th width="80">Internal Ref</th>
                    <th width="80">Buyer</th>
                    <th width="90">Style</th>
                    <th width="100">Garments Item</th>
                    <th width="70">Deli. Qty</th>
                    <th width="60">UOM</th>
                    <th width="60">Rate</th>
                    <th width="70">Amount</th>
                    <th>Remarks</th>
                </thead>
				<?
				
		$sys_number = return_library_array("select id, sys_number from pro_ex_factory_delivery_mst", 'id', 'sys_number');
		
		
		if($db_type==0) $id_cond="group_concat(b.remarks)";
		else if($db_type==2) $id_cond="listagg(b.remarks,',') within group (order by b.remarks)";

		$sql="select b.delivery_mst_id,b.delivery_date,b.po_break_down_id,b.gmts_item_id,c.buyer_name,d.po_number,d.grouping, 
   c.style_ref_no,c.order_uom, sum(b.delivery_qty) as delivery_qty,sum(b.amount) as amount,$id_cond as remarks from subcon_inbound_bill_dtls b, wo_po_details_master c,wo_po_break_down d 
					 where 
					 b.po_break_down_id=d.id
					 and c.job_no=d.job_no_mst 
					 and b.mst_id=$data[1] and b.process_id=394
					 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  group by b.delivery_mst_id,b.delivery_date,b.po_break_down_id,b.gmts_item_id,c.buyer_name, 
   c.style_ref_no,d.po_number,c.order_uom ,d.grouping order by delivery_mst_id ASC";
				$sql_res=sql_select($sql);
				
 				$i=1; $grand_tot_qty=0; $k=1;

				foreach ($sql_res as $row) 
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$rate=($row[csf('amount')]/$row[csf('delivery_qty')]);
					
					
				$remarksArr='';
				$remarksd=array_unique(explode(",",$row[csf("remarks")]));
				foreach($remarksd as $val)
				{
					if($remarksd=="") $remarksArr=$val; else $remarksArr.=$val.",";
				}
				$remarks=implode(",",array_unique(explode(",",$remarksArr)));
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    
                   
                        <td><? echo $i; ?></td>
                        <td style="word-break:break-all"><? echo $sys_number[$row[csf("delivery_mst_id")]]; ?></td>
                        <td style="word-break:break-all"><? echo change_date_format($row[csf("delivery_date")]); //?></td>
                        <td style="word-break:break-all"><? echo $row[csf('po_number')]; ?></td>
                         <td style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
                        <td style="word-break:break-all"><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                        <td style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?>&nbsp;</td>
                        <td style="word-break:break-all"><? echo $garments_item[$row[csf('gmts_item_id')]];?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('delivery_qty')], 2, '.', ''); ?>&nbsp;</td>
                         <td align="right"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>&nbsp;</td>
                        <td align="right"><? echo  number_format($rate,4,'.',''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($row[csf('amount')], 2, '.', ''); ?>&nbsp;</td>
                        <td style="word-break:break-all"><? echo chop($remarks,",");?>&nbsp;</td>
                    </tr>
					<?
					$i++;
					$sub_total_qty+=$row[csf('delivery_qty')];
					$sub_total_amt+=$row[csf('amount')];
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right"><b>Total:</b></td>
                    <td align="right"><b><? echo number_format($sub_total_qty,2); ?></b></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><b><? echo number_format($sub_total_amt,2); ?></b></td>
                </tr>
                <tr>
            	<td colspan="160" align="left"><b>In Word: <? echo number_to_words($sub_total_qty,$uom_unit,$uom_gm); ?></b></td>
        		</tr>
            </table>
            
            <br>
            
			<? echo signature_table(197, $data[0], "1030px"); ?>
        </div>
    </div>
	<?
	exit();
}
?>