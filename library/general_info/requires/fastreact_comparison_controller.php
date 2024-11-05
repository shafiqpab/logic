<?php
header('Content-type:text/html; charset=utf-8');
//header('Content-Type: text/csv; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ( $action=="save_update_delete" )
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($shift_date!='' &&  $received_date !='')
	{ 
        $color_lib=return_library_array("select id,color_name from lib_color","id","color_name");
		$buyer_name=return_library_array("select id,short_name from lib_buyer","id","short_name");
        
        $shift_date = change_date_format($shift_date, "yyyy-mm-dd", "-",1);
        $received_date = change_date_format($received_date, "yyyy-mm-dd", "-",1);
 		
        $myfile  = "../../../file_upload/fr_csv_999999999.csv";
        
        $file = fopen($myfile,"r") or die("Unable to open file!");
       
        while(! feof($file))
        {
            $orderFileData[] = fgetcsv($file);       
        }
        
        fclose($file); 
        
        $fr_data = array();
        foreach($orderFileData as $key=>$value){
            if($key!=0)
            {
				
				
				$tmp=explode("/",$value[7]);
				$tmp1=explode("/",$value[5]);
				
				if(strlen($tmp[2])==2) $tmp[2]="20".$tmp[2];
				if(strlen($tmp1[2])==2) $tmp1[2]="20".$tmp1[2];
				/*
				if($value[3]=="FFL-16-00270")
				{
					echo "".$tmp1[2]."-".$tmp1[1]."-".$tmp1[0];
					echo date("Y-m-d",strtotime($value[5]))."==";
					echo $value[5]."=".$value[7]; die;
				}
				*/
				
				$sdt=date("Y-m-d",strtotime($tmp[2]."-".$tmp[1]."-".$tmp[0]));
				$opd=date("Y-m-d",strtotime($tmp1[2]."-".$tmp1[1]."-".$tmp1[0]));
				//$opd=date("Y-m-d",strtotime($value[5]));
				$po=trim($value[2]);//."".str_replace("FFL","",trim($value[3]));
           // $fr_data[$value[2]][$value[3]][$value[8]] = $value[7];  
		   		$valt=explode("::",$value[3]);
				$value[3]=$valt[0];
			   $fr_data[($value[1])][trim($value[3])][trim($po)][$sdt]['qnty'] = trim($value[9]); 
			   $fr_data[($value[1])][trim($value[3])][trim($po)][$sdt]['opd'] = trim($opd);
			   $fr_data[($value[1])][trim($value[3])][trim($po)][$sdt]['sts'] = trim($value[0]);  
			     
            }
			
        }
		//echo "<pre>";
        //  print_r( $fr_data); die;  
          
        $sql = sql_select("
        SELECT b.id as id, b.item_number_id,a.job_no_mst,a.po_number,b.country_ship_date,a.po_received_date,sum(b.plan_cut_qnty) as plan_cut_new, sum(order_quantity) as order_quantity, a.is_confirmed, min(b.shiping_status) as shiping_status,b.country_id,b.po_break_down_id,b.color_number_id,a.is_deleted,b.cutup,c.buyer_name,a.shipment_date FROM wo_po_break_down a,wo_po_color_size_breakdown b,  wo_po_details_master c WHERE a.id=b.po_break_down_id and b.is_deleted=0 and b.status_active=1  and b.country_ship_date between '$shift_date' and '$received_date'and b.plan_cut_qnty!=0 and a.job_no_mst=c.job_no and b.job_no_mst=c.job_no
        group by a.job_no_mst,a.po_number, b.country_ship_date, a.is_confirmed,b.color_number_id
        order by c.id"); //b.country_ship_date,b.po_break_down_id,b.cutup
        //print_r($sql);die; 
		//group by b.id,b.country_id,b.item_number_id,a.job_no_mst,a.po_number, b.country_ship_date, a.is_confirmed,b.po_break_down_id, b.color_number_id,a.is_deleted,b.cutup
		//echo "SELECT b.id as id, b.item_number_id,a.job_no_mst,a.po_number,b.country_ship_date,a.po_received_date,sum(b.plan_cut_qnty) as plan_cut_new, sum(order_quantity) as order_quantity, a.is_confirmed, min(b.shiping_status) as shiping_status,b.country_id,b.po_break_down_id,b.color_number_id,a.is_deleted,b.cutup,c.buyer_name FROM wo_po_break_down a,wo_po_color_size_breakdown b,  wo_po_details_master c WHERE a.id=b.po_break_down_id and b.is_deleted=0 and b.status_active=1  and b.country_ship_date between '$shift_date' and '$received_date'and b.plan_cut_qnty!=0 and a.job_no_mst=c.job_no and b.job_no_mst=c.job_no group by  id,b.country_id,b.item_number_id,a.job_no_mst,a.po_number, b.country_ship_date, a.is_confirmed,b.po_break_down_id, b.color_number_id,a.is_deleted,b.cutup
       // order by b.country_ship_date,b.po_break_down_id,b.cutup";
		
        $missinng_list=array();
        foreach($sql as $name)
        {	
		
			//$fr_data[$value[1]][$value[3]][$value[2]][$value[7]][$value[8]]['sts']
			/// echo $name[csf('job_no_mst')]."=".$name[csf('country_ship_date')];
		 // print_r( $fr_data[$buyer_name[$name[csf('buyer_name')]]][$name[csf('job_no_mst')]][$name[csf('po_number')]."".str_replace("FFL","",trim($name[csf('job_no_mst')]))]); die;
			 
			if($fr_data[$buyer_name[$name[csf('buyer_name')]]][$name[csf('job_no_mst')]][$name[csf('po_number')]."".str_replace("FFL","",trim($name[csf('job_no_mst')]))][$name[csf('country_ship_date')]]['opd'] == '') { 
				if($color_lib[$name[csf('color_number_id')]]!="TBA")
				{
					$missinng_list[$i]['buyer']=$buyer_name[$name[csf('buyer_name')]];
					$missinng_list[$i]['job']=$name[csf('job_no_mst')];
					$missinng_list[$i]['po']=$name[csf('po_number')];
					$missinng_list[$i]['color']=$color_lib[$name[csf('color_number_id')]];
					$missinng_list[$i]['opd']=$name[csf('po_received_date')];
					$missinng_list[$i]['ship']=$name[csf('country_ship_date')];
					$missinng_list[$i]['sts']=$order_status[$name[csf('is_confirmed')]];
					$i++;
					$count[$name[csf('job_no_mst')]]+=1;
				}
				
            //if($fr_data[$name[csf('po_number')]][$name[csf('job_no_mst')]][$color_lib[$name[csf('color_number_id')]]] == '') { 
               // $missing_po[$buyer_name[$name[csf('buyer_name')]]][$name[csf('job_no_mst')]][$name[csf('po_number')]][$color_lib[$name[csf('color_number_id')]]] = $name[csf('country_ship_date')].'_'.$color_lib[$name[csf('color_number_id')]].'_'.$name[csf('po_number')];		               
            }
        } 
        
    }

}
?>

<?php if($mising_po='generate_mising_po_list') { ?>
    <div id="scroll_body" align="center" style="height:auto; width:950px; margin:0 auto; padding:0;">
        
        <table width="950" >        
            <tr>
                <td colspan="6" align="center" style="font-size:15px"><center><strong><u><? echo $report_title; ?> Report</u></strong></center></td>                
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:15px"> <?php echo $shift_date." To ".$received_date?> </td>
            </tr>
        </table>
       
        <div style="width:950px; height:auto">
            <table align="right" cellspacing="0" width="950"  border="1" rules="all" class="rpt_table" id="tbl_suppler_list" >   
                <thead bgcolor="#dddddd" align="center">
                    <tr>
                        <th width="50">SL</th>
                        <th width="120" align="center">Buyer Name</th>
                        <th width="80" align="center">Job No</th>
                        <th align="center">PO Number</th>
                        <th width="80" align="center">OPD</th>
                        <th width="80" align="center">Country Shipment date</th>
                       	<th align="center" width="200">Color</th> 
                        <th align="center" width="80">Status</th> 
                    </tr>         
                </thead>
                <tbody>
                    <?php 
                    $sl = 0;
					foreach($missinng_list as $id=>$vals)
					{
						$sl++;
						?>
						<tr bgcolor="">
							<td align="center"><? echo $sl; ?></td>
                            <?
							if( $jnew[$vals['job']]=='')
							{
								$jnew[$vals['job']]=$vals['job'];
							?>
							<td  valign="middle"  rowspan="<? echo $count[$vals['job']]; ?>"><? echo $vals['buyer']; ?></td>
							<td valign="middle" rowspan="<? echo $count[$vals['job']]; ?>"><? echo $vals['job']; ?></td>
                            <? } ?>
							<td><? echo $vals['po']; ?></td>  
                            <td><? echo change_date_format($vals['opd']);; ?></td>
                            <td><? echo change_date_format($vals['ship']); ; ?></td>
                            <td><? echo $vals['color']; ?></td>
                            <td><? echo $vals['sts']; ?></td>                                                                                    
						</tr>
					<?
					}
                     
                    ?>    
                </tbody> 
            </table>
        </div>
    </div>
<?php  } ?>