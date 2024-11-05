<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Woven Garments Price Quotation Entry.
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	05-02-2013
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($db_type==0)
{
	$group_concat="group_concat";
	$defalt_date_format="0000-00-00";
}
else
{
	$group_concat="wm_concat";
	$defalt_date_format="";
}

//--------------------------------------------------------------------------------------------------------------------
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name"  ); 
$team_name_library=return_library_array( "select id, team_name from lib_marketing_team where status_active=1 and is_deleted=0", "id", "team_name"  );
$team_leader_library=return_library_array( "select id, team_leader_name from lib_marketing_team where status_active=1 and is_deleted=0", "id", "team_leader_name"  );


$company_library=return_library_array( "select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"  );
$color_library = return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 order by id", "id", "color_name"  );
$sample_library = return_library_array( "select id,sample_name from lib_sample order by id", "id", "sample_name"  ); 
$team_member_library=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info where status_active=1 and is_deleted=0", "id", "team_member_name"  );
connect();


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "get_buyer_config(this.value);" );     	 
	exit();
}

if ($action=="load_drop_down_team_member")
{
if($data!=0)
	{
        echo create_drop_down( "cbo_team_member", 120, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" ); 
	}
 else
   {
		 echo create_drop_down( "cbo_team_member", 120, $blank_array,"", 1, "-Select Team Member- ", $selected, "" );
   }
}

if ($action=="load_drop_down_brand")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=100; else $width=100;
	echo create_drop_down( "cbo_brand_id", $width, "SELECT id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $brand_cond group by id, brand_name order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if ($action=="load_drop_down_season_buyer")
{
	echo create_drop_down( "cbo_season_name", 100, "SELECT id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 group by id, season_name order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if(str_replace("'","",$cbo_company_name)==0)
	{ 
		$company_name="";$company_name_cond="";
	}
	else
	{
	  $company_name=" and b.company_name=$cbo_company_name"; $company_name_cond=" and company_id=$cbo_company_name";
	}
	//if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_id=$cbo_company_name";
	if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name=""; else $buyer_name=" and b.buyer_name=$cbo_buyer_name";
		
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))==""){
	$txt_date="";
	}
	else{
		$txt_date=" and a.pub_shipment_date between $txt_date_from and $txt_date_to";
	}
	
	if(str_replace("'","",$txt_order_no)!="") $order_cond=" and a.po_number like '%".str_replace("'","",$txt_order_no)."%'  ";
	if(str_replace("'","",$txt_style)!="") $style_cond=" and b.style_ref_no like '%".str_replace("'","",$txt_style)."%'";
	if(str_replace("'","",$cbo_team_name)!=0) $team_leder_cond=" and b.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and b.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	$cbo_brand_id=str_replace("'","",$cbo_brand_id);
	$cbo_season_name=str_replace("'","",$cbo_season_name);
	$cbo_season_year=str_replace("'","",$cbo_season_year);
	$order_status_id=str_replace("'","",$cbo_order_status);
	$brand_seasion_seasion_year_cond='';
	if(!empty($cbo_brand_id))
	{
		$brand_seasion_seasion_year_cond=" and b.brand_id=$cbo_brand_id";
	}
	if(!empty($cbo_season_name))
	{
		$brand_seasion_seasion_year_cond.=" and b.season_buyer_wise=$cbo_season_name";
	}
	if(!empty($cbo_season_year))
	{
		$brand_seasion_seasion_year_cond.=" and b.season_year=$cbo_season_year";
	}
	$order_status_cond='';
	if($order_status_id!=0)
	{
		//$order_status_cond="";// and a.is_confirmed in(1,2)
		$order_status_cond=" and a.is_confirmed=$order_status_id";	
	}
	else
	{
		$order_status_cond=" and a.is_confirmed in(1,2)";	
	}
	
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	if($txt_job_no!="") 
		{  
			$job_cond=" and a.job_no_mst like '%$txt_job_no'";
		    if($db_type==2) $job_cond.=" and extract(year from a.insert_date)=".str_replace("'","",$cbo_year)."";
	        if($db_type==0) $job_cond.=" and year(a.insert_date)=".str_replace("'","",$cbo_year)."";
		
		}
		  //  if($db_type==2){$fill_con=" LISTAGG(CAST(b.gmts_item_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as gmts_item_id";}
	      //  if($db_type==0){$fill_con=" group_concat(b.gmts_item_id ) as gmts_item_id";}
	
	  $sql="SELECT 
		 a.id,
		 a.po_number,
		 a.unit_price,
		 a.po_quantity,
		 a.pub_shipment_date,
		 a.job_no_mst,
		 a.is_confirmed,
		 b.buyer_name,
		 b.style_ref_no,
		 b.company_name,
		 b.gmts_item_id,
		 	 
		 b.team_leader,
		 b.dealing_marchant,
		 b.factory_marchant,
		 SUM(c.order_total) as amount
	 from
		 wo_po_break_down a,
		 wo_po_details_master b,
		 wo_po_color_size_breakdown c
	 where
		 a.job_no_mst=b.job_no and
		 a.id not in (select wo_po_break_down_id from com_export_lc_order_info where status_active=1 and is_deleted=0) and
		 a.id not in (select wo_po_break_down_id from com_sales_contract_order_info where status_active=1 and is_deleted=0) and
		 a.id=c.po_break_down_id and
		 a.job_no_mst=c.job_no_mst
		 
		 
		 $company_name
		 $buyer_name
		 $txt_date
		 $order_cond
		 $team_leder_cond
		 $team_cond
		 $job_cond
		 $style_cond 
		 $brand_seasion_seasion_year_cond
		 $order_status_cond and
		 
		 a.status_active=1 and
		 a.is_deleted=0 and
		 b.status_active=1 and
		 b.is_deleted=0 and
		 c.status_active=1 and
		 c.is_deleted=0
	group by
		 a.id,
		 a.po_number,
		 a.unit_price,
		 a.po_quantity,
		 a.pub_shipment_date,
		 a.job_no_mst,
		 a.is_confirmed,
		 b.buyer_name,
		 b.style_ref_no,
		 b.company_name,
		 b.team_leader,
		 b.dealing_marchant,
		 b.factory_marchant,
		 b.gmts_item_id
	order by a.id DESC
		 ";
		 
		
	//echo $sql;die;
		
		$master_sql=sql_select($sql);
	 	foreach($master_sql as $rows)
		{
			$data_arr[]=array(
				'id'=>$rows[csf('id')],
				'po_number'=>$rows[csf('po_number')],
				'po_quantity'=>$rows[csf('po_quantity')],
				'pub_shipment_date'=>$rows[csf('pub_shipment_date')],
				'job_no_mst'=>$rows[csf('job_no_mst')],
				'buyer_name'=>$rows[csf('buyer_name')],
				'style_ref_no'=>$rows[csf('style_ref_no')],
				'gmts_item_id'=>$rows[csf('gmts_item_id')],
				'team_leader'=>$rows[csf('team_leader')],
				'dealing_marchant'=>$rows[csf('dealing_marchant')],
				'factory_marchant'=>$rows[csf('factory_marchant')],
				'amount'=>$rows[csf('amount')],
				'company_name'=>$rows[csf('company_name')],
				'is_confirmed'=>$rows[csf('is_confirmed')],
				'gmts_item_id'=>$rows[csf('gmts_item_id')]
			);
			$order_arr[]=$rows[csf('id')];	
		}
$order_string=implode(',',$order_arr);	

//var_dump($data_arr); die;
$sewing_output_qty_library=return_library_array( "select po_break_down_id,sum(production_quantity) as production_quantity from  pro_garments_production_mst where  po_break_down_id in($order_string) and production_type=5 and status_active=1 and is_deleted=0 $company_name_cond group by po_break_down_id", "po_break_down_id", "production_quantity");

	
//echo "select po_break_down_id,production_quantity from  pro_garments_production_mst where company_id=$cbo_company_name and po_break_down_id in($order_string) and status_active=1 and is_deleted=0";	
	
	
	ob_start();
		?>
    <fieldset style="width:1605px;">
    <legend>Yet to Receive Export LC/SC against order</legend>				
		<div style="width:1520px;" align="left">
            <table width="1570" border="1" rules="all"  class="rpt_table">
            
                <thead>
                    <th width="35">SL</th>
                    <th width="120">Company</th>
                    <th width="80">Job No</th>
                    <th width="100">Po Number</th>
					<th width="100">Order Status</th>
                    <th width="100">Buyer Name</th>
                    <th width="120">Style Name</th>
                    <th width="120">Item Name</th>
                    <th width="80">Po Qnty.</th>
                    <th width="50">Unit Price</th>
                    <th width="120">Po Value</th>
                    <th width="80">Ship Date</th>
                    <th width="80">Remaining Days to Ship</th>
                    <th width="70">Sewing Finish</th>
                    <th width="100">Team Leader</th>
                    <th width="120">Dealing Merchant</th>
                    <th>Factory Merchant</th>
                </thead>
            </table>
            <div style="width:1590px; max-height:300px; overflow-y:scroll">	
            <table width="1570" border="1" class="rpt_table" rules="all" id="tbl_details" >
            <?	
				
				$i=1;
                foreach($data_arr as $row) 
                {
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";										 
                
					$item_id_arr=array_unique(explode(',',$row['gmts_item_id']));
					$items='';
					foreach($item_id_arr as $val)
					{
					$items.=$garments_item[$val].', ';	
					}
					$items=rtrim($items,', ');
				
					$currTime=date("Y-m-d h:i:s A");
					$remaining_day=datediff("d",$currTime,$row['pub_shipment_date']);				
					if($remaining_day<0)$tdcolor="#D00"; else $tdcolor='';
					
					$total_qty+=$row['po_quantity'];
					$total_value+=$row['amount'];
					$total_sewing_finish+=$sewing_output_qty_library[$row['id']];
				?>
                <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" >
                    <td width="35" align="center"> <? echo $i;?> </td>
                    <td width="120" align="center"><p><? echo $company_library[$row['company_name']]; ?></p></td>
                    <td width="80" align="center"><p><? echo $row['job_no_mst']; ?></p></td>
                    <td width="100" align="center"><p><? echo $row['po_number']; ?></p></td>	
					<td width="100" align="center"><p><? echo $order_status[$row['is_confirmed']];//$order_status[]; ?></p></td>	
                    <td width="100" align="center"><p><? echo $buyer_library[$row['buyer_name']]; ?></p></td>	
                    <td width="120" align="center"><p><? echo $row['style_ref_no']; ?></p></td>	
                    <td width="120" align="center"><p><? echo $items; ?></p></td>	
                    <td width="80" align="right"><? echo $row['po_quantity']; ?></td>	
                    <td width="50" align="right"><? echo number_format($row['amount']/$row['po_quantity'],2); ?></td>	
                    <td width="120" align="right"><? echo number_format($row['amount'],2); ?></td>	
                    <td width="80" align="center"><? echo change_date_format($row['pub_shipment_date']); ?></td>	
                    <td width="80" align="center" bgcolor="<? echo $tdcolor;?>"><? echo $remaining_day; ?></td>	
                    <td width="70" align="right"><? echo $sewing_output_qty_library[$row['id']]; ?></td>	
                    <td width="100" align="center"><p><? echo $team_leader_library[$row['team_leader']]; ?></p></td>	
                    <td width="120" align="center"><p><? echo $team_member_library[$row['dealing_marchant']]; ?></p></td>	
                    <td align="center"><p><? echo $team_member_library[$row['factory_marchant']];?></p></td>	
                </tr>

                  <? 		
                 $i++;
				 }// Master Job  table queery ends here
                ?>
               </table>
          </div>
            <table width="1570" border="1" class="rpt_table" rules="all" >
                <tfoot>
                    <tr>
                        <th colspan="8" align="left"><b>Total</b></th>
                        <th width="80" align="right"><b id="tot_qty"><? echo $total_qty;?></b></th>
                        <th width="50" align="right"><b id=""><? //echo $total_qty;?></b></th>
                        <th width="120" align="right"><b id="tot_val"><? echo number_format($total_value,2);?></b></th>
                        <th width="80" align="left"></th>
                        <th width="80" align="left"></th>
                        <th width="70" align="right"><b id="tot_sf"><? echo $total_sewing_finish;?></b></th>
                        <th width="100" align="right"></th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                    </tr>
                </tfoot>    
          </table>
        </div>
      </fieldset>						
                               				
		
        
 		<?		
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$data=ob_get_contents();
	ob_get_clean();
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$data);
	echo $data.'****'.$filename.'****'.$i;

exit();	

}

 
disconnect($con);
?>