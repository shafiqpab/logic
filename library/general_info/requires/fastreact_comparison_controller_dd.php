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
        
        $shift_date = change_date_format($shift_date, "yyyy-mm-dd", "-",1);
        $received_date = change_date_format($received_date, "yyyy-mm-dd", "-",1);
 		
        $myfile  = "../../../file_upload/fr_csv_999999999.csv";
        
        $file = fopen($myfile,"r") or die("Unable to open file!");
       
        while(! feof($file))
        {
            $orderFileData[] = fgetcsv($file);       
        }
        
        fclose($file); 
        //echo "<pre>";print_r($orderFileData); die;	
        $fr_data = array();
        foreach($orderFileData as $key=>$value){
            if($key!=0)
            {
               $fr_data[$value[2]][$value[3]][$value[8]] = $value[7];             
            }
        }
        
        // echo "<pre>";
        // print_r($fr_data); 
     
        $sql = sql_select("
        SELECT b.id as id, b.item_number_id,a.job_no_mst,a.po_number,b.country_ship_date,sum(b.plan_cut_qnty) as plan_cut_new, sum(order_quantity) as order_quantity, a.is_confirmed, min(b.shiping_status) as shiping_status,b.country_id,b.po_break_down_id,b.color_number_id,a.is_deleted,b.cutup FROM wo_po_break_down a,wo_po_color_size_breakdown b WHERE a.id=b.po_break_down_id and b.is_deleted=0 and b.status_active=1  and b.country_ship_date between '$shift_date' and '$received_date'and b.plan_cut_qnty!=0
        group by b.id,b.country_id,b.item_number_id,a.job_no_mst,a.po_number, b.country_ship_date, a.is_confirmed,b.po_break_down_id, b.color_number_id,a.is_deleted,b.cutup
        order by b.country_ship_date,b.po_break_down_id,b.cutup");
        //print_r($sql);die;
            
        foreach($sql as $name)
        {	
            if($fr_data[$name[csf('po_number')]][$name[csf('job_no_mst')]][$color_lib[$name[csf('color_number_id')]]] == '') { 
                $missing_po[$name[csf('po_number')]][$name[csf('job_no_mst')]][$color_lib[$name[csf('color_number_id')]]] = $name[csf('country_ship_date')].'_'.$color_lib[$name[csf('color_number_id')]].'_'.$name[csf('po_number')];		               
            }
        } 
        
    }

}
?>

<?php if($mising_po='generate_mising_po_list') { ?>
    <div id="scroll_body" align="center" style="height:auto; width:550px; margin:0 auto; padding:0;">
        
        <table width="550px" >        
            <tr>
                <td colspan="6" align="center" style="font-size:15px"><center><strong><u><? echo $report_title; ?> Report</u></strong></center></td>                
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:15px"> <?php echo $shift_date." To ".$received_date?> </td>
            </tr>
        </table>
       
        <div style="width:550px; height:auto">
            <table align="right" cellspacing="0" width="550px"  border="1" rules="all" class="rpt_table" id="tbl_suppler_list" >   
                <thead bgcolor="#dddddd" align="center">
                    <tr>
                        <th width="50">SL</th>
                        <th width="80" align="center">Date</th>
                        <th width="100" align="center">Po Number</th>
                        <th width="75" align="center">Color</th> 
                    </tr>         
                </thead>
                <tbody>
                    <?php 
                    $sl = 0;
                    foreach ($missing_po as $missingPoArr) {                      
                        foreach ($missingPoArr as $jobNoMstArr) { 
                            foreach ($jobNoMstArr as $poKey=>$poValue) {
                                $sl++;
                                list($dat,$colorNumberId,$poNumber) = explode('_',$poValue);
                                ?>
                                    <tr bgcolor="">
                                        <td align="center"><? echo $sl; ?></td>
                                        <td align="center"><? echo $dat; ?></td>
                                        <td><? echo $poNumber; ?></td>
                                        <td align="center"><? echo $colorNumberId; ?></td>                                                                                      
                                    </tr> 
                                <?php 
                            }
                        } 
                    }
                    ?>    
                </tbody> 
            </table>
        </div>
    </div>
<?php  } ?>