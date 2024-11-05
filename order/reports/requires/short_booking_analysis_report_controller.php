<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

if($action=="load_drop_down_buyer")
{
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_id", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id    and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
		exit();
	}
}

if ($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
	?>	
    <script>
	function js_set_value( job_id )
	{
		//alert(po_id)
		document.getElementById('txt_job_id').value=job_id;
		parent.emailwindow.hide();
	}
		  
	</script>
     <input type="hidden" id="txt_job_id" />
 <?
	if ($data[0]==0) $company_id=""; else $company_id=" and a.company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and a.buyer_name=$data[1]";
	//if ($data[2]==0) $year_id=""; else $year_id=" and buyer_name=$data[2]";
	if($db_type==0)
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$data[2]).""; else $year_cond="";	
	}
	else
	{
		if(str_replace("'","",$data[2])!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$data[2]).""; else $year_cond="";
	}
	
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$teamMemberArr = return_library_array("select id,team_leader_name from lib_marketing_team ","id","team_leader_name");
	 $order_type=str_replace("'","",$data[3]);
	if($order_type==1)
	{
		 $sql= "select a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 $company_id $buyer_id $year_cond group by a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader order by a.job_no ";
	}
	else
	{
		 $sql= "select a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader from wo_po_details_master a, wo_po_break_down  b where  b.job_no_mst=a.job_no and a.status_active=1 and a.is_deleted=0 $company_id $buyer_id $year_cond group by a.id, a.job_no,a.job_no_prefix_num, a.style_ref_no, a.product_dept, a.dealing_marchant, a.team_leader order by a.job_no";
	}
	//echo $sql;die;
	
	$arr=array(2=>$product_dept,3=>$marchentrArr,4=>$teamMemberArr);
	echo  create_list_view("list_view", "Job No,Style Ref.,Prod. Dept.,Marchant,Team Name", "100,110,110,150,150","680","360",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "0,0,product_dept,dealing_marchant,team_leader", $arr , "job_no_prefix_num,style_ref_no,product_dept,dealing_marchant,team_leader", "",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	exit();
}

if ($action=="po_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
	
?>	
    <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	
	function check_all_data()
	{
		var row_num=$('#list_view tr').length-1;
		for(var i=1;  i<=row_num;  i++)
		{
			$("#tr_"+i).click();
		}
	}
		
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#txt_po_id').val( id );
		$('#txt_po_val').val( ddd );
	} 
		  
	</script>
     <input type="hidden" id="txt_po_id" />
     <input type="hidden" id="txt_po_val" />
     <?
	// echo $data[0];
	 if ($data[0]==0) $company_name=""; else $company_name=" and a.company_name='$data[0]'";
	 if ($data[1]==0) $buyer_name=""; else $buyer_name=" and a.buyer_name='$data[1]'";
	 if ($data[2]=="") $job_num=""; else $job_num=" and a.job_no_prefix_num='$data[2]'";
	if($db_type==0)
	{
		if(str_replace("'","",$data[3])!=0) $year_cond=" and year(a.insert_date)=".str_replace("'","",$data[3]).""; else $year_cond="";	
	}
	else
	{
		if(str_replace("'","",$data[3])!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$data[3]).""; else $year_cond="";
	}
	
	$order_type=str_replace("'","",$data[4]);
	if($order_type==1)
	{
		$sql= "select b.id, b.po_number, b.job_no_mst, b.pub_shipment_date,c.gmts_item_id from  wo_po_details_mas_set_details c,wo_po_details_master a 
		LEFT JOIN wo_po_break_down b ON a.job_no = b.job_no_mst 
		AND b.is_deleted =0 AND b.status_active =1
		where  a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 $job_num $company_name $buyer_name $year_cond group by b.id, b.po_number, b.job_no_mst, b.pub_shipment_date,c.gmts_item_id order by po_number";
	}
	else
	{
		$sql= "select b.id, b.po_number, b.job_no_mst, b.pub_shipment_date,c.gmts_item_id from wo_po_details_master a, wo_po_break_down  b, wo_po_details_mas_set_details c where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 $job_num $company_name $buyer_name $year_cond group by b.id, b.po_number, b.job_no_mst, b.pub_shipment_date,c.gmts_item_id order by po_number";
	}
	//echo  $sql;die;
	$arr=array(3=>$garments_item);
	echo  create_list_view("list_view", "PO No.,Job No.,Pub Shipment Date,Item Name", "100,100,80,150","450","360",0, $sql , "js_set_value", "id,po_number", "", 1, "0,0,0,gmts_item_id", $arr , "po_number,job_no_mst,pub_shipment_date,gmts_item_id", "",'setFilterGrid("list_view",-1);','0,0,3,0','',1) ;
	exit();	 
}

if ($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$job_no=str_replace("'","",$txt_job_no);
	$style_ref=str_replace("'","",$txt_styleref);
	$main_booking=str_replace("'","",$txt_main_booking);
	$short_booking=str_replace("'","",$txt_short_booking);
	$cause_type=str_replace("'","",$cbo_cause_type);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	//if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_name=$cbo_company";
	
	if($cbo_buyer==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer";
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num=$job_no";
	if ($style_ref=="") $styleRefCond=""; else $styleRefCond=" and a.style_ref_no='$style_ref'";
	if ($main_booking=="") $main_booking_cond=""; else $main_booking_cond=" and b.booking_no_prefix_num='$main_booking'";
	if ($short_booking=="") $short_booking_cond=""; else $short_booking_cond=" and b.booking_no_prefix_num='$short_booking'";
	if ($cause_type==0) $cause_type_cond=""; else $cause_type_cond=" and d.cause_id='$cause_type'";
	
	if($db_type==0)
	{
		if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond= " and b.booking_date between '".$date_from."' and '".$date_to."'";
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and year(b.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	else if($db_type==2)
	{
		if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond= " and b.booking_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and to_char(b.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$locationArr = return_library_array("select id,location_name from lib_location ","id","location_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier ","id","supplier_name");
	$causesArr = return_library_array("select id,cause from booking_cause ","id","cause");
	
	$cause_group_arr=array(1=>"Merketing",2=>"Sample",3=>"Textile",4=>"Textile",5=>"Textile",6=>"Textile",7=>"Textile",8=>"Textile",9=>"Textile",10=>"Textile",11=>"Textile",12=>"Textile",13=>"Screen Print",14=>"Embroidery",15=>"Garments Wash",16=>"Garments Unit");
	ob_start();
	?>
    <div>
    <table width="1150px" cellspacing="0">
        <tr class="form_caption" style="border:none;">
            <td colspan="12" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
        </tr>
        <tr style="border:none;">
            <td colspan="12" align="center" style="border:none; font-size:16px; font-weight:bold"><? echo $companyArr[$cbo_company]; ?></td>
        </tr>
        <tr style="border:none;">
            <td colspan="12" align="center" style="border:none;font-size:12px; font-weight:bold">
                <? echo "From ".change_date_format($date_from)." To ".change_date_format($date_to); ?>
            </td>
        </tr>
    </table>
    <?
		$mbooking_arr=array();
        $sql_main="select b.booking_no_prefix_num, c.booking_no, c.job_no, c.fin_fab_qnty
            from wo_po_details_master a, wo_booking_mst b, wo_booking_dtls c
            where a.job_no=c.job_no and b.booking_no=c.booking_no and b.booking_type=1 and b.is_short=2 and a.company_name='$cbo_company' 
            and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $job_no_cond $main_booking_cond $styleRefCond";//$year_cond 
        $sql_main_res=sql_select($sql_main); $jobstr=""; $tot_rows=0;
        foreach($sql_main_res as $mrow)
        {
            $mbooking_arr[$mrow[csf('job_no')]][2]['main'][$mrow[csf('booking_no')]]=$mrow[csf('booking_no')];
            $mbooking_arr[$mrow[csf('job_no')]][2]['qty'][$mrow[csf('booking_no')]]+=$mrow[csf('fin_fab_qnty')];
            $tot_rows++;
            $jobstr.="'".$mrow[csf("job_no")]."',";
        }
        //print_r($mbooking_arr['OG-19-00064']);
        unset($sql_main_res);
        $jobstr=chop($jobstr,','); $jobstr_cond="";
        if ($main_booking!="")
        {
            if($db_type==2 && $tot_rows>1000)
            {
                $jobstr_cond=" and (";
                
                $jobstrArr=array_chunk(explode(",",$jobstr),999);
                foreach($jobstrArr as $ids)
                {
                    $ids=implode(",",$ids);
                    $jobstr_cond.=" a.job_no in ($ids) or ";
                }
                
                $jobstr_cond=chop($jobstr_cond,'or ');
                $jobstr_cond.=")";
            }
            else $jobstr_cond=" and a.job_no in ($jobstr)";
        }
        
        $sql_short="select a.job_no, a.buyer_name, b.booking_no_prefix_num, b.booking_no, b.is_short, b.booking_date, b.pay_mode, b.supplier_id, b.supplier_location_id, c.fin_fab_qnty, d.gray_qty as grey_fab_qnty , d.res_company, d.res_location, d.cause_id, d.cause, d.qty, d.percent
            from wo_po_details_master a, wo_booking_mst b, wo_booking_dtls c, wo_booking_short_cause d 
            where a.job_no=c.job_no and b.booking_no=c.booking_no and b.booking_no=d.booking_no and c.id=d.dtls_id and b.booking_type=1 and b.is_short=1 and a.company_name='$cbo_company' and d.entry_form=88
            and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $buyer_id_cond $jobstr_cond $job_no_cond $booking_date_cond $short_booking_cond $cause_type_cond $styleRefCond order by b.booking_no, d.cause_id, d.cause ASC";// $year_cond $main_booking_cond 
           //echo $sql_short;
        $sql_short_res=sql_select($sql_short); $booking_arr=array(); $cause_type_arr=array(); $summary_arr=array();
        foreach($sql_short_res as $srow)
        {
			$res_unit='';
			$res_unit=$srow[csf('res_company')].'**'.$srow[csf('res_location')];
            $booking_arr[$srow[csf('job_no')]][1]['short'][$srow[csf('booking_no')]]=$srow[csf('booking_no')];
            $booking_arr[$srow[csf('job_no')]][1]['date'][$srow[csf('booking_no')]]=$srow[csf('booking_date')];
            $booking_arr[$srow[csf('job_no')]][1]['buyer_id'][$srow[csf('booking_no')]]=$srow[csf('buyer_name')];
            $booking_arr[$srow[csf('job_no')]][1]['res_company'][$srow[csf('booking_no')]]=$srow[csf('pay_mode')].'_'.$srow[csf('supplier_id')].'_'.$res_unit;
            
            $cause_type_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('cause_id')]][$srow[csf('cause')]]['qty']+=$srow[csf('qty')];
            $cause_type_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('cause_id')]][$srow[csf('cause')]]['gray_qty']+=$srow[csf('grey_fab_qnty')];
			$cause_type_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('cause_id')]][$srow[csf('cause')]]['percent']+=$srow[csf('percent')];
			$cause_type_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('cause_id')]][$srow[csf('cause')]]['unit']=$res_unit;
			
			$summary_arr[$cause_group_arr[$srow[csf('cause_id')]]][$res_unit][$srow[csf('cause_id')]][$srow[csf('cause')]]['qty']+=$srow[csf('qty')];
			$summary_arr[$cause_group_arr[$srow[csf('cause_id')]]][$res_unit][$srow[csf('cause_id')]][$srow[csf('cause')]]['gray_qty']+=$srow[csf('grey_fab_qnty')];
			$summary_arr[$cause_group_arr[$srow[csf('cause_id')]]][$res_unit][$srow[csf('cause_id')]][$srow[csf('cause')]]['percent']+=$srow[csf('percent')];
			$summary_arr[$cause_group_arr[$srow[csf('cause_id')]]][$res_unit][$srow[csf('cause_id')]][$srow[csf('cause')]]['booking'].=$srow[csf('booking_no')].',';
            $tot_rows++;
            $jobstr.="'".$srow[csf("job_no")]."',";
        }
		//print_r($summary_arr).'kausar';
        unset($sql_short_res); $main_booking_qty_arr=array();
        $job_count_arr=array(); $sub_tot_count_arr=array(); $short_tot_count_arr=array();
        foreach($cause_type_arr as $jobno=>$job_data)
        {
            foreach($job_data as $sbooking_no=>$sbooking_data)
            {
                $short_tot_count_arr[$jobno]+=1;
                foreach($sbooking_data as $cause_id=>$cause_data)
                {
                    $sub_tot_count_arr[$jobno]+=1;
                    foreach($cause_data as $cause=>$extdata)
                    {
						$qty=0;
                        $job_count_arr[$jobno]+=1;
						$qty=$extdata['qty'];
						$main_booking_qty_arr[$jobno][$sbooking_no]+=$qty;
                    }
                }
            }
        }
		
		$summary_count_arr=array(); $summary_cgroup=array(); $summary_resunit=array(); $summary_qty_arr=array();
		foreach($summary_arr as $cause_group=>$cause_group_data)
		{
			$groupspan=0;
			foreach($cause_group_data as $res_id=>$res_data)
			{
				$unitspan=0;
				foreach($res_data as $cause_id=>$cause_id_data)
				{
					$causespan=0;
					foreach($cause_id_data as $cause=>$cause_data)
					{
						$groupspan++;
						$unitspan++;
						$causespan++;
						$summary_qty_arr[$cause_group][$res_id][$cause_id]+=$cause_data['qty'];
						$summary_short_tot+=$cause_data['qty'];
						$summary_resunit[$cause_group][$res_id]['qty']+=$cause_data['qty'];	
						$summary_cgroup[$cause_group]['qty']+=$cause_data['qty'];
					}
					$groupspan++;
					$summary_count_arr[$cause_group][$res_id][$cause_id]=$causespan;
					//$summary_cgroup[$cause_group]=$groupspan;
					$unitspan++;
				}
				$groupspan++;
				$summary_resunit[$cause_group][$res_id]['span']=$unitspan;	
			}
			$summary_cgroup[$cause_group]['span']=$groupspan;
		}
		//var_dump($summary_cgroup);
	?>
    <table width="1130px" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
        	<tr>
            	<th colspan="12">Cause Type Wise Summary</th>
            </tr>
            <tr style="font-size:13px">
                <th width="30" rowspan="2">SL.</th>
                <th width="90" rowspan="2">Cause Group</th>
                <th width="130" rowspan="2">Responsible Unit</th>     
                <th width="110" rowspan="2">Cause Type</th>
                <th width="250" rowspan="2">Causes</th>
                <th width="80" rowspan="2">Short Gray Qty</th>
                <th width="80" rowspan="2">Short Finish Qty [KG]</th>
                <th colspan="4">Weight %</th>
                <th rowspan="2">Total Short WO</th>
             </tr>
             <tr style="font-size:13px">
             	<th width="70">Cause</th>
                <th width="70">Cause Type</th>
                <th width="70">Unit</th>
                <th width="70">Cause Group</th>
             </tr>
        </thead>
    </table>
    <div style="width:1150px; max-height:200px; overflow-y:scroll" id="scroll_body1"> 
    <table width="1130px" border="1" cellspacing="0" class="rpt_table" rules="all">
    	<? $a=1;
		foreach($summary_arr as $cause_group=>$cause_group_data)
		{
			$j=1;
			$group_span=$summary_cgroup[$cause_group]['span'];
			foreach($cause_group_data as $res_id=>$res_data)
			{
				$k=1;
				$unit_span=$summary_resunit[$cause_group][$res_id]['span'];
				foreach($res_data as $cause_id=>$cause_id_data)
				{
					$q=1;
					$srow_span=$summary_count_arr[$cause_group][$res_id][$cause_id];
					foreach($cause_id_data as $cause=>$cause_data)
					{
						if($a%2==0)$bgcolors="#E9F3FF";  else $bgcolors="#FFFFFF";
						$count_booking=0; $group_tot=0; $unit_tot=0; $unit_per=0; $devision_per=0; $exunit='';
						
						$unit_tot=$summary_resunit[$cause_group][$res_id]['qty']; 
						$group_tot=$summary_cgroup[$cause_group]['qty'];
						
						$unit_per=($unit_tot/$group_tot)*100;
						$devision_per=($group_tot/$summary_short_tot)*100;
						//echo $res_id;
						$exunit=array_filter(explode("**",trim($res_id)));
						//print_r($exunit); die;
						$res_unit=""; $resLocation='';
						$res_company=$companyArr[$exunit[0]];
						$resLocation=$locationArr[$exunit[1]];
						
						$res_unit=$res_company.','.$resLocation;
						
						$ex_booking=array_filter(array_unique(explode(",",$cause_data['booking'])));
						$count_booking=count($ex_booking); $summary_per=0; $cause_type_per=0;
						$summary_per=($cause_data['qty']/$summary_qty_arr[$cause_group][$res_id][$cause_id])*100;
						
						$cause_type_per=($summary_qty_arr[$cause_group][$res_id][$cause_id]/$summary_resunit[$cause_group][$res_id]['qty'])*100;//$summary_short_tot
						?>
                        <tr bgcolor="<? echo $bgcolors; ?>" onClick="change_color('trsu<? echo $a; ?>','<? echo $bgcolors; ?>')" id="trsu<? echo $a; ?>" style="font-size:13px">
                        <?
						if($j==1)
						{
							?>
								<td width="30" rowspan="<? echo $group_span+1; ?>"><? echo $a; $a++; ?></td> 
                                <td width="90" rowspan="<? echo $group_span; ?>" style="word-break:break-all"><? echo $cause_group; ?></td> 
                             <?
						}
						if($k==1)
						{
							?>
                                <td width="130" rowspan="<? echo $unit_span; ?>" style="word-break:break-all"><? echo $res_unit; ?></td> 
							<?
						}
						
						if($q==1)
						{
							?>
                                <td width="110" rowspan="<? echo $srow_span; ?>" style="word-break:break-all"><? echo $short_booking_cause_arr[$cause_id]; ?></td>
                             <?
						}
						?>
                        <td width="250" style="word-break:break-all"><? echo $causesArr[$cause]; ?></td>
                        <td width="80" align="right"><? echo number_format($cause_data['gray_qty'],2); ?></td>
                        <td width="80" align="right"><? echo number_format($cause_data['qty'],2); ?></td>
                        <td width="70" align="right"><? echo number_format($summary_per,4); ?></td>
                        
                        <?
                        if($q==1)
						{
							?>      
								<td width="70" align="right" rowspan="<? echo $srow_span; ?>"><? echo number_format($cause_type_per,4); ?></td>
                            <?
							$sum_cause_tot_per+=$cause_type_per;
							$sum_unit_tot_per+=$cause_type_per;
							$sum_group_tot_per+=$cause_type_per;
							$sum_grand_tot_per+=$cause_type_per;
						}
						if($k==1)
						{
							?>
                                <td width="70" rowspan="<? echo $unit_span+1; ?>" valign="bottom" style="word-break:break-all" align="right"><? echo number_format($unit_per,2); ?></td> 
							<?
							$group_per_tot+=$unit_per;
						}
						if($j==1)
						{
							?>
                                <td width="70" rowspan="<? echo $group_span+1; ?>" valign="bottom" style="word-break:break-all" align="right"><? echo number_format($devision_per,2); ?></td> 
                             <?
							 $devision_tot_per+=$devision_per;
						}
							?>
							<td align="right"><? echo $count_booking; ?></td>
						</tr>
						<?
						$j++;
						$k++;
						$q++;
						
						$sum_cause_tot_qty+=$cause_data['qty'];
						$sum_unit_tot_qty+=$cause_data['qty'];
						$sum_group_tot_qty+=$cause_data['qty'];
						$sum_grand_tot_qty+=$cause_data['qty'];

						$sum_cause_tot_gray_qty+=$cause_data['gray_qty'];
						$sum_unit_tot_gray_qty+=$cause_data['gray_qty'];
						$sum_group_tot_gray_qty+=$cause_data['gray_qty'];
						$sum_grand_tot_gray_qty+=$cause_data['gray_qty'];
					}
					?>
                    <tr bgcolor="#D4DF55" style="font-weight:bold; font-size:13px">
                        <td align="right">&nbsp;</td>
                        <td align="right">Cause Type Total : </td>
						<td align="right"><? echo number_format($sum_cause_tot_gray_qty, 2); ?></td>
                        <td align="right"><? echo number_format($sum_cause_tot_qty, 2); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? echo number_format($sum_cause_tot_per, 2); ?></td>
                        <td align="right">&nbsp;</td>
                    </tr>
                    <?
                    $sum_cause_tot_qty = 0;
                    $sum_cause_tot_gray_qty = 0;
                    $sum_cause_tot_per = 0;
				}
				?>
                <tr bgcolor="#FFFFAA" style="font-weight:bold; font-size:13px">
                	<td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td align="right">Responsible Unit Total : </td>
					<td align="right"><? echo number_format($sum_unit_tot_gray_qty, 2); ?></td>
                    <td align="right"><? echo number_format($sum_unit_tot_qty, 2); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="right"><? echo number_format($sum_unit_tot_per, 2); ?></td>
                    <td align="right">&nbsp;</td>
                </tr>
            	<?
				$sum_unit_tot_qty=0;
				$sum_unit_tot_gray_qty=0;
				$sum_unit_tot_per=0;
			}
			?>
            <tr bgcolor="#D4FF55" style="font-weight:bold; font-size:13px">
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right">&nbsp;</td>
                <td align="right">Cause Group Total : </td>
				<td align="right"><? echo number_format($sum_group_tot_gray_qty, 2); ?></td>
                <td align="right"><? echo number_format($sum_group_tot_qty, 2); ?></td>
                <td align="right">&nbsp;</td>
                <td align="right"><? echo number_format($sum_group_tot_per, 2); ?></td>
                <td align="right"><? echo number_format($group_per_tot,2); ?></td>
            </tr>
            <?
            $sum_group_tot_qty=0;
            $sum_group_tot_gray_qty=0;
			$sum_group_tot_per=0;
			$group_per_tot=0;
		}
		?>
    </table>
    </div>
    <table width="1130px" cellspacing="0" border="1" class="tbl_bottom" rules="all">
        <tr style="font-size:13px">
            <td width="30">&nbsp;</td> 
            <td width="90">&nbsp;</td>
            <td width="130">&nbsp;</td>
            <td width="110">&nbsp;</td>
            <td width="250" align="right">Summary Total:</td>
			<td width="80" align="right"><? echo number_format($sum_grand_tot_gray_qty,2); ?></td>
            <td width="80" align="right"><? echo number_format($sum_grand_tot_qty,2); ?></td>
            <td width="70">&nbsp;</td>
            <td width="70" align="right"><? //echo number_format($gnd_cause_per,2); ?></td>
            <td>&nbsp;</td>
            <td width="70" align="right"><? echo number_format($devision_tot_per,2); ?></td>
            <td>&nbsp;</td>
         </tr>
    </table>
    <br />
    <table width="1420px" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <tr style="font-size:13px">
                <th width="30">SL.</th>
                <th width="80" style="word-break:break-all">Job No</th>    
                <th width="110" style="word-break:break-all">Main Booking</th>
                <th width="80" style="word-break:break-all">Main Booking Finish Qty (Kg)</th>
                <th width="110">Short Booking No</th>
                
                <th width="70" style="word-break:break-all">Booking Date</th>
                <th width="100">Buyer</th>
                <th width="100" style="word-break:break-all">Cause Group</th>
                <th width="90" style="word-break:break-all">Responsible Unit</th>
                <th width="100">Cause Type</th>
                
                <th width="300">Causes</th>
                <th width="70">Short Gray Qty</th>
                <th width="70">Short Fin Qty</th>
                <th>Cause Weight %</th>
             </tr>
        </thead>
    </table>
    <div style="width:1440px; max-height:350px; overflow-y:scroll" id="scroll_body" > 
    <table width="1420px" border="1" cellspacing="0" class="rpt_table" rules="all" id="">
		<?
        //print_r($job_count_arr);
        $i=1;
        foreach($cause_type_arr as $jobno=>$job_data)
        {
            $k=1;
            foreach($job_data as $sbooking_no=>$sbooking_data)
            {
                foreach($sbooking_data as $cause_id=>$cause_data)
                {
                    foreach($cause_data as $cause=>$extdata)
                    {
                        if($j%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                        $sbooking_date=''; $sbooking_buyer=0; $res_company=''; $res_location=''; $ex_compnay=""; $qty=0; $percent=0; $exdata="";
                        $sbooking_date=$booking_arr[$jobno][1]['date'][$sbooking_no];
                        $sbooking_buyer=$booking_arr[$jobno][1]['buyer_id'][$sbooking_no];
                        $ex_compnay=explode("_",$booking_arr[$jobno][1]['res_company'][$sbooking_no]);						
						
						$ex_unit=array_filter(explode("**",$extdata['unit']));
						$res_unit="";
						$res_company=$companyArr[$ex_unit[0]];
						$res_location=$locationArr[$ex_unit[1]];
						
						$res_unit=$res_company.','.$res_location;
                        
                        $qty=$extdata['qty'];
                        $gray_qty=$extdata['gray_qty'];
                        $percent=$extdata['percent'];
						
						$cal_per=($qty*100)/$main_booking_qty_arr[$jobno][$sbooking_no];
                        
                        if($k==1) {
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $j; ?>" style="font-size:13px">
                            <? 
                            $main_booking_no=""; $main_booking_fin_qty=0; $row_span=0;
                            $main_booking_no=implode(",",array_unique($mbooking_arr[$jobno][2]['main']));
                            //print_r($main_booking_no);
                            $main_booking_fin_qty=array_sum($mbooking_arr[$jobno][2]['qty']);
                            $row_span=$job_count_arr[$jobno]+$sub_tot_count_arr[$jobno]+$short_tot_count_arr[$jobno];
							$main_booking_qty_tot+=$main_booking_fin_qty;
                            ?>
                            <td width="30" rowspan="<? echo $row_span+1; ?>" valign="middle"><? echo $i; $i++; ?></td>
                            <td width="80" rowspan="<? echo $row_span; ?>" valign="middle" style="word-break:break-all"><? echo $jobno; ?></td>
                            <td width="110" rowspan="<? echo $row_span; ?>" valign="middle" style="word-break:break-all"><? echo $main_booking_no; ?></td>
                            <td width="80" rowspan="<? echo $row_span; ?>" valign="middle" style="word-break:break-all;" align="right"><? echo number_format($main_booking_fin_qty,2); ?></td>
                            <td width="110" style="word-break:break-all"><? echo $sbooking_no; ?></td>
                            <td width="70" style="word-break:break-all"><? echo change_date_format($sbooking_date); ?></td>
                            <td width="100" style="word-break:break-all"><? echo $buyerArr[$sbooking_buyer]; ?></td>
                            <td width="100" style="word-break:break-all"><? echo $cause_group_arr[$cause_id]; ?></td>
                            <td width="90" style="word-break:break-all"><? echo $res_unit; ?>&nbsp;</td>
                            <td width="100" style="word-break:break-all"><? echo $short_booking_cause_arr[$cause_id]; ?></td>
                            <td width="300" style="word-break:break-all"><? echo $causesArr[$cause]; ?></td>
                            <td width="70" align="right"><? echo number_format($gray_qty,2); ?></td>
                            <td width="70" align="right"><? echo number_format($qty,2); ?></td>
                            <td align="right"><? echo number_format($cal_per,4); ?></td>
                        </tr>
                        <?
                        } 
                        else
                        {	
                            ?>
                             <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $j; ?>" style="font-size:13px">
                                <td width="110" style="word-break:break-all"><? echo $sbooking_no; ?></td>
                                <td width="70" style="word-break:break-all"><? echo change_date_format($sbooking_date); ?></td>
                                <td width="100" style="word-break:break-all"><? echo $buyerArr[$sbooking_buyer]; ?></td>
                                <td width="100" style="word-break:break-all"><? echo $cause_group_arr[$cause_id]; ?></td>
                                <td width="90" style="word-break:break-all"><? echo $res_unit; ?>&nbsp;</td>
                                <td width="100" style="word-break:break-all"><? echo $short_booking_cause_arr[$cause_id]; ?></td>
                                <td width="300" style="word-break:break-all"><? echo $causesArr[$cause]; ?></td>
                                <td width="70" align="right"><? echo number_format($gray_qty,2); ?></td>
                                <td width="70" align="right"><? echo number_format($qty,2); ?></td>
                                <td align="right"><? echo number_format($cal_per,4); ?></td>
                            </tr>
                            <?
                        }
                        $cause_total_qty+=$qty;
                        $cause_total_per+=$cal_per;
                        $sbooking_total_qty+=$qty;
                        $sbooking_total_per+=$cal_per;
                        $mbooking_total_qty+=$qty;
                        $mbooking_total_per+=$cal_per;
                        $gbooking_total_qty+=$qty;
                        $gbooking_total_per+=$cal_per;

						$cause_total_gray_qty+=$gray_qty;
						$sbooking_total_gray_qty+=$gray_qty;
						$mbooking_total_gray_qty+=$gray_qty;
						$gbooking_total_gray_qty+=$gray_qty;

                        $k++;
                    }
                    ?>
                        <tr bgcolor="#CCCCCC" style="font-weight:bold; font-size:13px;">
                            <td colspan="7" align="right">Cause Type Total : </td>
							<td align="right"><? echo number_format($cause_total_gray_qty, 2); ?></td>
                            <td align="right"><? echo number_format($cause_total_qty, 2); ?></td>
                            <td align="right"><? echo number_format($cause_total_per, 4); ?></td>
                        </tr>
                    <?
                    $cause_total_gray_qty = 0;
                    $cause_total_qty = 0;
                    $cause_total_per = 0;
                }
                ?>
                    <tr bgcolor="#CCCCFF" style="font-weight:bold; font-size:13px">
                        <td colspan="7" align="right">Short Booking Total : </td>
                        <td align="right"><? echo number_format($sbooking_total_gray_qty, 2); ?></td>
                        <td align="right"><? echo number_format($sbooking_total_qty, 2); ?></td>
                        <td align="right"><? echo number_format($sbooking_total_per, 4); ?></td>
                    </tr>
                <?
                $sbooking_total_gray_qty = 0;
                $sbooking_total_qty = 0;
                $sbooking_total_per = 0;
            }
            ?>
                <tr bgcolor="#FFFFCC" style="font-weight:bold; font-size:13px;">
                    <td colspan="10" align="right">Main Booking Total : </td>
                    <td align="right"><? echo number_format($mbooking_total_gray_qty, 2); ?></td>
                    <td align="right"><? echo number_format($mbooking_total_qty, 2); ?></td>
                    <td align="right"><? //echo number_format($mbooking_total_per, 4); ?>&nbsp;</td>
                </tr>
            <?
            $mbooking_total_gray_qty = 0;
            $mbooking_total_qty = 0;
            $mbooking_total_per = 0;
        }
        ?>
    </table>
    </div>
    <table width="1420px" border="1" cellspacing="0" class="tbl_bottom" rules="all" >
       <tr>
            <td width="30">&nbsp;</td>
            <td width="80">&nbsp;</td>
            <td width="110">Grand Total :</td>
            <td width="80" align="right"><? echo number_format($main_booking_qty_tot, 2); ?></td>
            <td width="110">&nbsp;</td>
            <td width="70">&nbsp;</td>
            <td width="100">&nbsp;</td>
            <td width="100">&nbsp;</td>
            <td width="90">&nbsp;</td>
            <td width="100">&nbsp;</td>
            <td width="300">&nbsp;</td>
            <td width="70" align="right"><? echo number_format($gbooking_total_gray_qty, 2); ?></td>
            <td width="70" align="right"><? echo number_format($gbooking_total_qty, 2); ?></td>
            <td align="right">&nbsp;</td>
        </tr>
    </table>
    </div>
    <?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if ($action=="report_generate2")
{
	extract($_REQUEST);
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$job_no=str_replace("'","",$txt_job_no);
	$style_ref=str_replace("'","",$txt_styleref);
	$main_booking=str_replace("'","",$txt_main_booking);
	$short_booking=str_replace("'","",$txt_short_booking);
	$cause_type=str_replace("'","",$cbo_cause_type);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
    $internal_ref=str_replace("'","",$txt_internalref);
	
	//if ($cbo_company==0) $company_id=""; else $company_id=" and a.company_name=$cbo_company";
	
	if($cbo_buyer==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer";
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num=$job_no";
	if ($style_ref=="") $styleRefCond=""; else $styleRefCond=" and a.style_ref_no='$style_ref'";
	if ($main_booking=="") $main_booking_cond=""; else $main_booking_cond=" and b.booking_no_prefix_num='$main_booking'";
    if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and f.grouping='$internal_ref'";
	if ($short_booking=="") $short_booking_cond=""; else $short_booking_cond=" and b.booking_no_prefix_num='$short_booking'";
	if ($cause_type==0) $cause_type_cond=""; else $cause_type_cond=" and d.cause_id='$cause_type'";
	
	if($db_type==0)
	{
		if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond= " and b.booking_date between '".$date_from."' and '".$date_to."'";
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and year(b.insert_date)=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
	else if($db_type==2)
	{
		if( $date_from=="" && $date_to=="" ) $booking_date_cond=""; else $booking_date_cond= " and b.booking_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		
		if(str_replace("'","",$cbo_year)!=0) $year_cond=" and to_char(b.insert_date,'YYYY')=".str_replace("'","",$cbo_year).""; else $year_cond="";
	}
    $lip_yarn_count=return_library_array( "select id,fabric_composition_id from lib_yarn_count_determina_mst where  status_active=1", "id", "fabric_composition_id");
    $fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where  status_active=1", "id", "fabric_composition_name");
    $department_name_library=return_library_array( "select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by  department_name", "id", "department_name"  );
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$locationArr = return_library_array("select id,location_name from lib_location ","id","location_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier ","id","supplier_name");
	$causesArr = return_library_array("select id,cause from booking_cause ","id","cause");
    $user_name_arr=return_library_array( "select id, user_name from user_passwd ",'id','user_name');
	$color_library=return_library_array( "SELECT id,color_name from lib_color ", "id", "color_name");
	$cause_group_arr=array(1=>"Merketing",2=>"Sample",3=>"Textile",4=>"Textile",5=>"Textile",6=>"Textile",7=>"Textile",8=>"Textile",9=>"Textile",10=>"Textile",11=>"Textile",12=>"Textile",13=>"Screen Print",14=>"Embroidery",15=>"Garments Wash",16=>"Garments Unit");
	ob_start();
	?>
    <div>
    <table width="5620px" cellspacing="0">
        <tr class="form_caption" style="border:none;">
            <td colspan="56" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
        </tr>
        <tr style="border:none;">
            <td colspan="56" align="center" style="border:none; font-size:16px; font-weight:bold"><? echo $companyArr[$cbo_company]; ?></td>
        </tr>
        <tr style="border:none;">
            <td colspan="56" align="center" style="border:none;font-size:12px; font-weight:bold">
                <? echo "From ".change_date_format($date_from)." To ".change_date_format($date_to); ?>
            </td>
        </tr>
    </table>
    <?
        $jobstr=chop($jobstr,','); $jobstr_cond="";
        if ($main_booking!="")
        {
            if($db_type==2 && $tot_rows>1000)
            {
                $jobstr_cond=" and (";
                
                $jobstrArr=array_chunk(explode(",",$jobstr),999);
                foreach($jobstrArr as $ids)
                {
                    $ids=implode(",",$ids);
                    $jobstr_cond.=" a.job_no in ($ids) or ";
                }
                
                $jobstr_cond=chop($jobstr_cond,'or ');
                $jobstr_cond.=")";
            }
            else $jobstr_cond=" and a.job_no in ($jobstr)";
        }
        
        $sql_main="select a.job_no,c.fabric_color_id,SUM(c.fin_fab_qnty) as fab_qnty,e.body_part_id from wo_po_details_master a, wo_booking_mst b, wo_booking_dtls c,wo_pre_cost_fabric_cost_dtls e where a.job_no=c.job_no and b.booking_no=c.booking_no  and c.pre_cost_fabric_cost_dtls_id=e.id and b.booking_type=1 and b.is_short=2 and a.company_name='$cbo_company' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $jobstr_cond $job_no_cond $styleRefCond group by a.job_no,c.fabric_color_id,e.body_part_id";
        $sql_main_res=sql_select($sql_main);
        foreach($sql_main_res as $mrow)
        {
			$another_arr[$mrow[csf('job_no')]][$mrow[csf('fabric_color_id')]][$mrow[csf('body_part_id')]]['main_qnty']+=$mrow[csf('fab_qnty')];
        }
        /*  echo '<pre>';
        print_r($another_arr);die;  */
        unset($sql_main_res);
        
        $sql_short="select MAX(h.approved_date) as last_approve_date,a.buyer_name,b.provider_id,b.remarks,c.fabric_color_id,c.dia_width,a.job_no, b.booking_no_prefix_num, b.booking_no, b.is_short, b.booking_date, b.pay_mode, b.supplier_location_id, c.fin_fab_qnty,c.gsm_weight, d.gray_qty as grey_fab_qnty , d.res_company, d.res_location, d.cause_id, d.cause, d.qty, d.percent,d.previous_qty,d.cause_description,c.process_loss_percent,b.inserted_by,e.body_part_id, e.color_type_id, e.construction, e.composition, e.gsm_weight,f.grouping as internal_ref_no,e.width_dia_type,e.avg_finish_cons,e.lib_yarn_count_deter_id AS determin_id,b.department_name
            from wo_po_details_master a, wo_booking_mst b, wo_booking_dtls c, wo_booking_short_cause d,wo_pre_cost_fabric_cost_dtls e,wo_po_break_down f,approval_history h
            where a.job_no=c.job_no and b.booking_no=c.booking_no and b.booking_no=d.booking_no and c.id=d.dtls_id and c.pre_cost_fabric_cost_dtls_id=e.id and a.id=f.job_id and b.booking_type=1 and b.is_short=1 and a.company_name='$cbo_company' and d.entry_form=88 and b.id=h.mst_id and h.entry_form in(12,13,7)
            and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $buyer_id_cond $jobstr_cond $job_no_cond $booking_date_cond $short_booking_cond $cause_type_cond $styleRefCond $internal_ref_cond group by a.buyer_name,b.provider_id,b.remarks,c.fabric_color_id,c.dia_width,a.job_no, b.booking_no_prefix_num, b.booking_no, b.is_short, b.booking_date, b.pay_mode,  b.supplier_location_id, c.fin_fab_qnty, d.gray_qty , d.res_company, d.res_location, d.cause_id, d.cause, d.qty, d.percent,d.previous_qty,d.cause_description,c.process_loss_percent,b.inserted_by,c.gsm_weight,e.body_part_id, e.color_type_id, e.construction, e.composition, e.gsm_weight,f.grouping,e.width_dia_type,e.avg_finish_cons,e.lib_yarn_count_deter_id,b.department_name order by b.booking_no, d.cause_id, d.cause ASC";// $year_cond $main_booking_cond 
           //echo $sql_short;die;
        $sql_short_res=sql_select($sql_short); $booking_arr=array(); $cause_type_arr=array(); $main_arr=array();
        foreach($sql_short_res as $srow)
        {
            $main_qty=$another_arr[$srow[csf('job_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['main_qnty'];
            $des=$fabric_composition[$lip_yarn_count[$srow[csf('determin_id')]]];
			$main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['last_approve_date']=$srow[csf('last_approve_date')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['buyer_name']=$srow[csf('buyer_name')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['supplier_id']=$srow[csf('provider_id')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['remarks']=$srow[csf('remarks')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['main_fab_qnty']=$main_qty;
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['fabric_color_id']=$srow[csf('fabric_color_id')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['color_type']=$srow[csf('color_type_id')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['body_part']=$body_part[$srow[csf('body_part_id')]];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['fabrication']=$srow[csf('construction')].','.$des;
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['gsm_weight']=$srow[csf('gsm_weight')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['department_name']=$srow[csf('department_name')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['internal_ref_no']=$srow[csf('internal_ref_no')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['pay_mode']=$srow[csf('pay_mode')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['avg_cons']=$srow[csf('avg_finish_cons')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['dia_type']=$srow[csf('width_dia_type')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['dia_width']=$srow[csf('dia_width')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['process_loss_percent']=$srow[csf('process_loss_percent')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['previous_qty']+=$srow[csf('previous_qty')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['qty']+=$srow[csf('qty')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['cause_qty'][$srow[csf('cause_id')]][$srow[csf('cause')]]+=$srow[csf('qty')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['short_cause_qty'][$srow[csf('cause_id')]]+=$srow[csf('qty')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['cause_description'].=$srow[csf('cause_description')].',';
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['inserted_by']=$srow[csf('inserted_by')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['cause_id']=$srow[csf('cause_id')];
            $main_arr[$srow[csf('job_no')]][$srow[csf('booking_no')]][$srow[csf('fabric_color_id')]][$srow[csf('body_part_id')]]['cause']=$srow[csf('cause')];
        }
            /* echo "<pre>";
		print_r($main_arr);die;   */
        unset($sql_short_res);
        foreach($main_arr as $job_id=>$job_data)
		{
            foreach($job_data as $booking_id=>$color_data)
            {
                foreach($color_data as $color_id=>$body_data)
                {
                    foreach($body_data as $body_id=>$booking_data)
                    {
                        $total_main_qnty+=$booking_data['main_fab_qnty'];
                        $total_prev_qnty+=$booking_data['previous_qty'];
                        $total_allo_qnty+=$booking_data['qty'];
                        $total_diff_perc=fn_number_format($total_allo_qnty/$total_main_qnty,2);
                        $total_knit_qnty+=$booking_data['short_cause_qty'][4];
                        $total_dying_qnty+=$booking_data['short_cause_qty'][6];
                        $total_yarn_qnty+=$booking_data['short_cause_qty'][3];
                        $total_aop_qnty+=$booking_data['short_cause_qty'][11];
                        $total_printing_qnty+=$booking_data['short_cause_qty'][23];
                        $total_cutting_qnty+=$booking_data['short_cause_qty'][24];
                        $total_sewing_qnty+=$booking_data['short_cause_qty'][26];
                        $total_gmtfin_qnty+=$booking_data['short_cause_qty'][27];
                        $total_merc_qnty+=$booking_data['short_cause_qty'][1];
                        $total_wash_qnty+=$booking_data['short_cause_qty'][29];
                        $total_store_qnty+=$booking_data['short_cause_qty'][28];
                        $varifiantion_qty=$booking_data['previous_qty']-$booking_data['qty']; 
                        $varifiantion_amnt=$varifiantion_qty*500;
                        $total_varifiantion_qty+=$varifiantion_qty;
                        $total_varifiantion_amnt+=$varifiantion_amnt;
                    }
                }
            }
        }        
       
	?>
    <table width="5900px" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
        	<tr>
            	<th colspan="15"></th>
                <th colspan="3" align="center">Total</th>
                <th rowspan="3" width="100"><? echo $total_diff_perc;?></th>
                <th rowspan="4" width="100">Cause Description</th>
                <th colspan="35" align="center">Responsible Department</th>
                <th colspan="4"></th>
            </tr>
            <tr>
            	<th colspan="15"></th>
                <th rowspan="2"><? echo  $total_main_qnty;?></th>
                <th rowspan="2"><? echo  $total_prev_qnty;?></th>
                <th rowspan="2"><? echo  $total_allo_qnty;?></th>
                <th colspan="5"><? echo  $total_knit_qnty;?></th>
                <th colspan="7"><? echo $total_dying_qnty;?></th>
                <th colspan="3"><? echo  $total_yarn_qnty;?></th>
                <th colspan="2"><? echo  $total_aop_qnty;?></th>
                <th colspan="2"><? echo  $total_printing_qnty;?></th>
                <th colspan="3"><? echo  $total_cutting_qnty;?></th>
                <th colspan="3"><? echo  $total_sewing_qnty;?></th>
                <th colspan="2"><? echo  $total_gmtfin_qnty;?></th>
                <th colspan="4"><? echo  $total_merc_qnty;?></th>
                <th colspan="2"><? echo  $total_wash_qnty;?></th>
                <th colspan="2"><? echo  $total_store_qnty;?></th>
                <th rowspan="2"><? echo  $total_varifiantion_qty;?></th>
                <th rowspan="2"><? echo  $total_varifiantion_amnt;?></th>
                <th rowspan="3" width="100">Remarks</th>
                <th rowspan="3" width="100">Verified By</th>
            </tr>
            <tr>
            	<th colspan="15"></th>
                <th colspan="5">Knitting</th>
                <th colspan="7">Dyeing & Finishing</th>
                <th colspan="3">Yarn Quality</th>
                <th colspan="2">Aop</th>
                <th colspan="2">Printing</th>
                <th colspan="3">Cutting</th>
                <th colspan="3">Sewing</th>
                <th colspan="2">Garments Finishing</th>
                <th colspan="4">Merchandising</th>
                <th colspan="2">Washing</th>
                <th colspan="2">STORE</th>
            </tr>
            <tr style="font-size:13px">
                <th width="100">Last App. Date</th>
                <th width="100">App. Month</th>
                <th width="100">Buyer</th>
                <th width="100">Short provide Factory</th>
                <th width="100">Issued by</th>
                <th width="100">IR/IB No</th>
                <th width="100">Colour</th>
                <th width="100">Color Type</th>
                <th width="100">Body Part</th>
                <th width="100">Fabric Description</th>
                <th width="100">GSM</th>
                <th width="100">Dia Type</th>
                <th width="100">Dia (F>C)</th>
                <th width="100">Finished Cons.</th>
                <th width="100">Booking PL%</th>
                <th width="100">Finish Fabric Booking Qty</th>
                <th width="100">Submitted Short qty (kg)</th>
                <th width="100">Allowed Short qty (kg)</th>
                <th width="100">Short %</th>

                <th width="100" >GSM Problem</th>
                <th width="100" >Process Loss</th>
                <th width="100" >Dia Problem</th>
                <th width="100" >Knitting Quality Problem</th>
                <th width="100" >Cut Panel Rejection</th>

                <th width="100">Process loss</th>
                <th width="100">Cut Panel Rejection</th>
                <th width="100">Dia Variation</th>
                <th width="100">GSM High</th>
                <th width="100">Dyeing &  Finishing Quality Problem</th>
                <th width="100">Shade Not Ok</th>
                <th width="100">Group Marker</th>

                <th width="100">Cut Panel Rejection</th>
                <th width="100">Yarn Quality Problem</th>
                <th width="100">Yarn Weight Loss</th>

                <th width="100">Process loss</th>
                <th width="100">Cut Panel Rejection</th>

                <th width="100">Cut Panel Rejection</th>
                <th width="100">Print Missing</th>

                <th width="100">Cut Panel Missing</th>
                <th width="100">Wrong Cutting</th>
                <th width="100">Input Mistake</th>

                <th width="100">Garments Rejection</th>
                <th width="100">Mersurement Problem</th>
                <th width="100">Garments Missing</th>

                <th width="100">Garments Rejection</th>
                <th width="100">Garments Missing</th>

                <th width="100">Booking Mistake</th>
                <th width="100">Wrong Consumption</th>
                <th width="100">Buyer Issue</th>
                <th width="100">Purchase Fabric Reprocess </th>

                <th width="100">Wash Rejection</th>
                <th width="100">Garments Missing</th>

                <th width="100">Fabric Missing</th>
                <th width="100">Short Receive </th>

                <th width="100">Saved by verification (KG)</th>
                <th width="100">Saved Amount (BDT)</th>
             </tr>
        </thead>
    </table>
    <div style="width:5920px; max-height:200px; overflow-y:scroll" id="scroll_body1"> 
    <table width="5900" border="1" cellspacing="0" class="rpt_table" rules="all">
    	<? $a=1;
        /* foreach($main_arr as $job_id=>$job_data)
		{
            foreach($job_data as $booking_id=>$color_data)
            {
                foreach($color_data as $color_id=>$body_data)
                {
                    $color_rowspan=0;
                    foreach($body_data as $body_id=>$booking_data)
                    {
                        $color_rowspan++;
                    }
                    $color_rowspan_arr[$job_id][$booking_id][$color_id]=$color_rowspan;
                }
            }
        }   */      
        foreach($main_arr as $job_id=>$job_data)
		{
            foreach($job_data as $booking_id=>$color_data)
            {
                foreach($color_data as $color_id=>$body_data)
                {   $x=1;
                    foreach($body_data as $body_id=>$booking_data)
                    {
                        if($j%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";?>
                        <tr bgcolor="#D4DF55" style="font-weight:bold; font-size:13px"> 
                       <!--  <?
                        //if($x==1)rowspan="<? echo $color_rowspan_arr[$job_id][$booking_id][$color_id];"
                        {
                        ?> -->
                        <td  style="word-break:break-all" align="center" width="100"><? echo date('m-d-Y',strtotime($booking_data['last_approve_date'])); ?><?// echo $booking_data['last_approve_date'];?></td>
                        <td  style="word-break:break-all" align="center" width="100"><? echo date('M,Y',strtotime($booking_data['last_approve_date'])); ?></td>
                        <td  style="word-break:break-all" align="center" width="100"><? echo $buyerArr[$booking_data['buyer_name']]; ?></td>
                        <td  style="word-break:break-all" align="center" width="100"><? 
                        if($booking_data['pay_mode']!=5){
                            echo $supplierArr[$booking_data['supplier_id']];
                        }else{
                            echo $companyArr[$booking_data['supplier_id']];
                        }
                        ?></td>
                        <td  style="word-break:break-all" align="center" width="100"> <? echo $booking_data['department_name']//echo $department_name_library[$booking_data['responsible_dept']]; ?> </td>
                        <td  style="word-break:break-all" align="center" width="100"><? echo $booking_data['internal_ref_no']; ?></td>
                        <td  style="word-break:break-all" align="center" width="100"><? echo $color_library[$booking_data['fabric_color_id']]; ?></td>
                       <!--  <?
                        }
                        ?> -->
                        <td style="word-break:break-all" align="center" width="100"><? echo $color_type[$booking_data['color_type']]; ?></td>
                        <td style="word-break:break-all" align="center" width="100"><? echo $booking_data['body_part']; ?></td>
                        <td style="word-break:break-all" align="center" width="100"><? echo $booking_data['fabrication']; ?></td>
                        <td style="word-break:break-all" align="center" width="100"><? echo $booking_data['gsm_weight']; ?></td>
                        <td style="word-break:break-all" align="center" width="100"><? echo $fabric_typee[$booking_data['dia_type']]; ?></td>
                        <td style="word-break:break-all" align="center" width="100"><? echo $booking_data['dia_width']; ?></td>
                        <td style="word-break:break-all" align="center" width="100"><? echo $booking_data['avg_cons']; ?></td>
                        <td style="word-break:break-all" align="center" width="100"><? echo $booking_data['process_loss_percent']; ?></td>
                        <td style="word-break:break-all" align="center" width="100"><? echo $booking_data['main_fab_qnty']; ?></td>
                        <td style="word-break:break-all" align="center" width="100"><? echo $booking_data['previous_qty']; ?></td>
                        <td style="word-break:break-all" align="center" width="100"><? echo $booking_data['qty']; ?></td>
                        <td style="word-break:break-all" align="center" width="100"><? echo fn_number_format($booking_data['qty']/$booking_data['main_fab_qnty'],0); ?></td>
                        <td style="word-break:break-all" align="center" width="100"><? 
                        $causeDescription=implode(",",array_unique(explode(",",chop($booking_data['cause_description'],","))));
                        echo $causeDescription;
                        ?></td>

                        <td align="right" width="100"><? echo $booking_data['cause_qty'][4][109]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][4][110]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][4][111]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][4][112]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][4][113]; ?></td>

                        <td align="right" width="100"><? echo $booking_data['cause_qty'][6][87]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][6][89]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][6][91]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][6][92]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][6][114]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][6][115]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][6][116]; ?></td>

                        <td align="right" width="100"><? echo $booking_data['cause_qty'][3][93]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][3][117]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][3][118]; ?></td>

                        <td align="right" width="100"><? echo $booking_data['cause_qty'][11][119]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][11][120]; ?></td>

                        <td align="right" width="100"><? echo $booking_data['cause_qty'][23][121]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][23][122]; ?></td>

                        <td align="right" width="100"><? echo $booking_data['cause_qty'][24][102]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][24][124]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][24][125]; ?></td>

                        <td align="right" width="100"><? echo $booking_data['cause_qty'][26][126]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][26][127]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][26][128]; ?></td>

                        <td align="right" width="100"><? echo $booking_data['cause_qty'][27][129]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][27][130]; ?></td>

                        <td align="right" width="100"><? echo $booking_data['cause_qty'][1][95]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][1][96]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][1][107]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][1][108]; ?></td>

                        <td align="right" width="100"><? echo $booking_data['cause_qty'][29][156]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][29][157]; ?></td>

                        <td align="right" width="100"><? echo $booking_data['cause_qty'][28][131]; ?></td>
                        <td align="right" width="100"><? echo $booking_data['cause_qty'][28][132]; ?></td>

                        <td align="right" width="100"><? 
                        $varifiantion_qnty=$booking_data['previous_qty']-$booking_data['qty']; 
                        $varifiantion_amt=$varifiantion_qnty*500;
                        echo $varifiantion_qnty; ?></td>
                        <td align="right" width="100"><? echo  $varifiantion_amt; ?></td>
                        <td align="center" width="100"><? echo $booking_data['remarks']; ?></td>
                        <td align="center" width="100"><? echo $user_name_arr[$booking_data['inserted_by']]; ?></td>
                        </tr>
                        <? $x++;
                    }
                }
            }

        }
		?>
    </table>
    </div>
    <br />
    </div>
    <?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}
?>
