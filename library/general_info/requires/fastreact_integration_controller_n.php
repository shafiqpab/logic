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
	
	if ($picupFile == true && $shift_date!='' &&  $received_date !='')
	{
        $shift_date = change_date_format($shift_date, "yyyy-mm-dd", "-",1);
 
        $myfile  = "frfiles/$picupFile";
        
        $file = fopen($myfile,"r") or die("Unable to open file!");
       
        while(! feof($file))
        {
            $orderFileData[] = fgetcsv($file);       
        }
        
        fclose($file); 

		$i=1;
		foreach($orderFileData as $val){			
			foreach($orderFileData[$i] as $key=>$value){                
				//if($value){$fileData[$orderFileData[0][$key]]=$value;}
                $fileData[$orderFileData[0][$key]][] = $value;    
			}
            $i++;
		}	
       
        /* echo $sql = "SELECT b.id as id, b.item_number_id,a.job_no_mst,a.po_number, b.country_ship_date,sum(b.plan_cut_qnty) as plan_cut_new, sum(order_quantity) as order_quantity, a.is_confirmed, min(b.shiping_status) as shiping_status,b.country_id,b.po_break_down_id, b.color_number_id,a.is_deleted,b.cutup FROM wo_po_break_down a, wo_po_color_size_breakdown b WHERE a.id=b.po_break_down_id and b.is_deleted=0 and b.status_active=1  and b.country_ship_date>'$shift_date' 
		and b.plan_cut_qnty!=0
		group by b.id,b.country_id,b.item_number_id,a.job_no_mst,a.po_number, b.country_ship_date, a.is_confirmed,b.po_break_down_id, b.color_number_id,a.is_deleted,b.cutup
 order by b.country_ship_date,b.po_break_down_id,b.cutup";  die; */

       /*  $sql=sql_select ( "SELECT group_concat(b.id) as id, b.item_number_id,a.job_no_mst,a.po_number, b.country_ship_date,sum(b.plan_cut_qnty) as plan_cut_new, sum(order_quantity) as order_quantity, a.is_confirmed, min(b.shiping_status) as shiping_status,b.country_id,b.po_break_down_id, b.color_number_id,a.is_deleted,b.cutup FROM wo_po_break_down a, wo_po_color_size_breakdown b WHERE $job_cond_in_mst  and  a.id=b.po_break_down_id and b.is_deleted=0 and b.status_active=1  and b.country_ship_date>$received_date 
		and b.plan_cut_qnty!=0
		group by b.item_number_id,a.job_no_mst,a.po_number, b.country_ship_date, a.is_confirmed,b.po_break_down_id, b.color_number_id,a.is_deleted,b.cutup
 order by b.country_ship_date,b.po_break_down_id,b.cutup" );   //and is_confirmed=1 */
        
        $sql = sql_select("SELECT b.id as id, b.item_number_id,a.job_no_mst,a.po_number, b.country_ship_date,sum(b.plan_cut_qnty) as plan_cut_new, sum(order_quantity) as order_quantity, a.is_confirmed, min(b.shiping_status) as shiping_status,b.country_id,b.po_break_down_id, b.color_number_id,a.is_deleted,b.cutup FROM wo_po_break_down a, wo_po_color_size_breakdown b WHERE a.id=b.po_break_down_id and b.is_deleted=0 and b.status_active=1  and b.country_ship_date>'$shift_date' 
		and b.plan_cut_qnty!=0
		group by b.id,b.country_id,b.item_number_id,a.job_no_mst,a.po_number, b.country_ship_date, a.is_confirmed,b.po_break_down_id, b.color_number_id,a.is_deleted,b.cutup
 order by b.country_ship_date,b.po_break_down_id,b.cutup");

		$txt .=str_pad("O.CODE",0," ")."\t".str_pad("O.PROD",0," ")."\t".str_pad("O.CUST",0," ")."\t".str_pad("O^DD:1",0," ")."\t".str_pad("O^DQ:1",0," ")."\t".str_pad("O.DESCRIP",0," ")."\t".str_pad("O.STATUS",0," ")."\t".str_pad("O.COMPLETE",0," ")."\t".str_pad("O.CONTRACT_QTY",0," ")."\t".str_pad("O.EVBASE",0," ")."\t".str_pad("O.UDJob No",0," ")."\t".str_pad("O.UDOPD",0," ")."\t".str_pad("O.UDOrder Code",0," ")."\t".str_pad("O.UDColour",0," ")."\t".str_pad("O.UDOrder",0," ")."\r\n";
		
		foreach($sql as $name)
		{	
            if( $name[csf('is_confirmed')]==1 ) $str="F"; else $str="P";

			if( $name[csf('shiping_status')]==3 ) $ssts="1";

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
			
			if( $name[csf('is_deleted')]==1 ) { $str="X";  } //$ssts=0;
			
			$str_po_list[$name[csf('po_break_down_id')]][$str_po]=$str_po;	
			
			$txt .=str_pad($str_po,0," ")."\t".str_pad($name[csf('job_no_mst')]."".$str_item,0," ")."\t".str_pad($buyer_name_array[$ft_data_arr[$name[csf('job_no_mst')]][buyer_name]],0," ")."\t".str_pad(date("d/m/Y",strtotime($name[csf('country_ship_date')])),0," ")."\t".str_pad($name[csf('plan_cut_new')],0," ")."\t".str_pad($style_ref_arr[$name[csf('job_no_mst')]],0," ")."\t".str_pad($str,0," ")."\t".str_pad($ssts,0," ")."\t".str_pad($name[csf('order_quantity')],0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($dtd,0," ")."\t".str_pad($name[csf('po_number')],0," ")."\t".str_pad($color_lib[$name[csf('color_number_id')]],0," ")."\t".str_pad($name[csf('po_number')]."::".$color_lib[$name[csf('color_number_id')]]."::". $str_po_cut .$fr_product_type[$name[csf('item_number_id')]],0," ")."\r\n";
       
            // $id_arr[] = $id;
        }
        
        /* 
        $fr_comparison_field_array = '';
        $fr_comparison_data_array  = '';
        
        $feedback = execute_query(bulk_update_sql_statement("lib_fr_comparison","id", $fr_comparison_field_array,$fr_comparison_data_array,$id_arr),1);
        */ 

        // ======= checking style one ========
        //print_r($fileData);die;
        /* if ($fileData['O.CODE']) {
        
            //echo 'have';
        
        } else {
            echo "Not have";
        } */
        // ======= checking style one ========
        
        
        // ======= checking style two ========
        foreach ($fileData as $filarr) {
            if (in_array($str_po,$filarr)) {
                echo "Data match";
                exit();
            } else {
                echo "Data Not match";
            }
        }        
        // ======= checking style two ========
        
        
       // print_r($fileData);
       
    }

}
?>