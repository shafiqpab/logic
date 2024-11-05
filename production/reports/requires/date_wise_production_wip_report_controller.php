<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_id", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );          
    exit();
}



if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location_id", 140, "SELECT id,location_name FROM lib_location WHERE status_active=1 AND is_deleted=0 AND company_id='$data' 
    ORDER BY location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/date_wise_production_wip_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/date_wise_production_wip_report_controller',document.getElementById('cbo_floor_id').value+'_'+this.value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_from').value, 'load_drop_down_line', 'line_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/date_wise_production_wip_report_controller' );",0 ); 
    exit();      
}




if ($action=="load_drop_down_floor")
{
    echo create_drop_down( "cbo_floor_id", 150, "SELECT id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/date_wise_production_wip_report_controller',this.value+'_'+document.getElementById('cbo_location_id').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_from').value, 'load_drop_down_line', 'line_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/date_wise_production_wip_report_controller' );",0 );             
    exit();   


}
 
if ($action == "eval_multi_select") 
{
    echo "set_multiselect('cbo_line_id','0','0','','0');\n";
    exit();
}

if ($action=="load_drop_down_line")
{
    $explode_data = explode("_",$data);
    $prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
    $txt_date = $explode_data[3];
    
    $cond="";
    if($prod_reso_allo==1)
    {
        $line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
        $line_array=array();
        
        if($txt_date=="")
        {
            if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
            if( $explode_data[0]!=0 ) $cond = " and floor_id= $explode_data[0]";
            $line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
        }
        else
        {
            if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
            if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";
         if($db_type==0)    $data_format="and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."'";
         if($db_type==2)    $data_format="and b.pr_date='".change_date_format($txt_date,'','',1)."'";

             $line_data=sql_select( "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id $data_format and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
        }
        
        foreach($line_data as $row)
        {
            $line='';
            $line_number=explode(",",$row[csf('line_number')]);
            foreach($line_number as $val)
            {
                if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
            }
            $line_array[$row[csf('id')]]=$line;
        }

        echo create_drop_down( "cbo_line_id", 150,$line_array,"", 1, "-- Select --", $selected, "",0,0 );
    }
    else
    {
        if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
        if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

        echo create_drop_down( "cbo_line_id", 150, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select --", $selected, "",0,0 );
    }
    exit();
}



if($action=="report_generate") 
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    $prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$cbo_company_id and variable_list=23 and is_deleted=0 and status_active=1");

    $companyArr = return_library_array("SELECT id,company_name FROM lib_company WHERE status_active=1 and is_deleted=0 and id=$cbo_company_id ","id","company_name"); 

    
    $buyer_lib = return_library_array("SELECT id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name"); 
    $floor_lib = return_library_array("SELECT id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id=$cbo_company_id","id","floor_name"); 

    $line_lib = return_library_array("SELECT id,line_name from lib_sewing_line","id","line_name");
    if($prod_reso_allo==1)
    {  
        
        $line_libr ="SELECT id,line_number from prod_resource_mst where company_id=$cbo_company_id and is_deleted=0 ";
        foreach(sql_select($line_libr) as $row)
        {             
            $line='';
            $line_number=explode(",",$row[csf('line_number')]);
            foreach($line_number as $val)
            {
                if($line=='') $line=$line_lib[$val]; else $line.=",".$line_lib[$val];
            }
            $line_lib_resource[$row[csf('id')]]=$line; 
        }       
         
    } 
    

    $locationArr = return_library_array("SELECT id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id=$cbo_company_id","id","location_name"); 
    $color_lib = return_library_array("SELECT id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name"); 
    $size_lib = return_library_array("SELECT id,size_name FROM lib_size WHERE status_active=1 AND is_deleted=0","id","size_name"); 

    $today_date=date("Y-m-d");
    $date_from=str_replace("'","",$txt_date_from);
    $company_cond=(str_replace("'","",$cbo_company_id)==0)?"":" and a.company_id= $cbo_company_id";
    $working_company_cond=(str_replace("'","",$cbo_working_company_id)==0)?"":" and a.serving_company= $cbo_working_company_id";
    $buyer_cond=(str_replace("'","",$cbo_buyer_id)==0)?"":" and b.buyer_name= $cbo_buyer_id";
    $location_cond=(str_replace("'","",$cbo_location_id)==0)?"":" and a.location= $cbo_location_id";
    $floor_cond=(str_replace("'","",$cbo_floor_id)==0)?"":" and a.floor_id= $cbo_floor_id";
    $cbo_line_id=str_replace("'","",$cbo_line_id);
    $line_cond=($cbo_line_id)?  " and a.sewing_line in($cbo_line_id)" : " "; 


    if (str_replace("'", "", $txt_date_from) != "") 
    {
        if ($db_type == 0) 
        {
            $start_date = change_date_format(str_replace("'", "", $txt_date_from), "yyyy-mm-dd", "");
        } 
        else if ($db_type == 2) 
        {
            $start_date = change_date_format(str_replace("'", "", $txt_date_from), "", "", 1);
        }
        $date_cond = " and a.production_date='$start_date'";
    } 

        $sql_today = "SELECT a.po_break_down_id  FROM  pro_garments_production_mst a WHERE a.production_type in(4,5) and  a.status_active = 1 and a.is_deleted = 0  $company_cond $location_cond $floor_cond
        $line_cond  $date_cond "; 
        $production_data_today = sql_select($sql_today);
        foreach($production_data_today as $k=>$vals)
        {
            $today_all_po_arr[$vals[csf("po_break_down_id")]]=$vals[csf("po_break_down_id")];
        }
        $today_all_po_ids=implode(",", $today_all_po_arr); 


   
    
    
        $sql = "SELECT b.buyer_name,b.style_ref_no,b.job_no_prefix_num ,a.po_break_down_id,c.po_number,c.grouping,c.file_no,a.item_number_id,e.color_number_id,a.floor_id,a.sewing_line ,
        sum( case when a.production_type=4 and d.production_type=4 $date_cond then d.production_qnty else 0 end ) as today_sew_input,
        sum( case when a.production_type=5 and d.production_type=5 $date_cond then d.production_qnty else 0 end ) as today_sew_output,
        sum( case when a.production_type=4 and d.production_type=4 and a.production_date <= '$start_date' then d.production_qnty else 0 end ) as total_sew_input,
        sum( case when a.production_type=5 and d.production_type=5 and a.production_date <= '$start_date' then d.production_qnty else 0 end ) as total_sew_output
        FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a, pro_garments_production_dtls d, wo_po_color_size_breakdown e  WHERE b.job_no = c.job_no_mst  and c.id = a.po_break_down_id and a.id = d.mst_id and a.production_type in(4,5) and d.production_type in(4,5)  and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  and c.status_active = 1 and c.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 
        and a.location is not null and a.location <> 0 and d.color_size_break_down_id is not null and d.color_size_break_down_id <> 0 $company_cond $location_cond $floor_cond
        $line_cond   $buyer_cond $working_company_cond and a.po_break_down_id in ($today_all_po_ids) group by b.buyer_name,b.style_ref_no,b.job_no_prefix_num ,a.po_break_down_id,c.po_number,c.grouping,c.file_no,a.item_number_id,e.color_number_id,a.floor_id,a.sewing_line  order by  a.floor_id,a.sewing_line"; 

       // echo $sql;die();
        $production_data = sql_select($sql);
        $po_ids="";
        $color_ids="";
        $item_ids="";
        foreach($production_data as $vals)
        {

            if($vals[csf("today_sew_input")] || $vals[csf("today_sew_output")] )
            {
               // if( ($vals[csf("total_sew_input")] - $vals[csf("total_sew_output")])*1!=0 )
               // {
                    $data_array[$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]] ["today_sew_input"]+=$vals[csf("today_sew_input")];
                    $data_array[$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]] ["today_sew_output"]+=$vals[csf("today_sew_output")];
                    $data_array[$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]] ["int_ref"]=$vals[csf("grouping")];
                    $data_array[$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]] ["file_no"]=$vals[csf("file_no")];

                    $all_po_arr[$vals[csf("po_break_down_id")]]=$vals[csf("po_break_down_id")];            
                    $all_color_arr[$vals[csf("color_number_id")]]=$vals[csf("color_number_id")];            
                    $all_item_arr[$vals[csf("item_number_id")]]=$vals[csf("item_number_id")];
                    $floor_line_total[$vals[csf("floor_id")]][$vals[csf("sewing_line")]]["today_sew_input"]+=$vals[csf("today_sew_input")];            
                    $floor_line_total[$vals[csf("floor_id")]][$vals[csf("sewing_line")]]["today_sew_output"]+=$vals[csf("today_sew_output")];            
                    $floor_total[$vals[csf("floor_id")]]["today_sew_input"]+=$vals[csf("today_sew_input")];            
                    $floor_total[$vals[csf("floor_id")]]["today_sew_output"]+=$vals[csf("today_sew_output")]; 

                   // floor wise details part array

                    $data_array_floor[$vals[csf("floor_id")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]] ["today_sew_input"]+=$vals[csf("today_sew_input")]; 
                    $data_array_floor[$vals[csf("floor_id")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]] ["today_sew_output"]+=$vals[csf("today_sew_output")]; 
                    $data_array_floor[$vals[csf("floor_id")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]] ["int_ref"]=$vals[csf("grouping")]; 
                    $data_array_floor[$vals[csf("floor_id")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]] ["file_no"]=$vals[csf("file_no")]; 


                   // $data_array[$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]] ["total_sew_input"]+=$vals[csf("total_sew_input")];
                   // $data_array[$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]] ["total_sew_output"] +=$vals[csf("total_sew_output")];

                    $floor_line_total[$vals[csf("floor_id")]][$vals[csf("sewing_line")]]["total_sew_input"]+=$vals[csf("total_sew_input")];            
                    $floor_line_total[$vals[csf("floor_id")]][$vals[csf("sewing_line")]]["total_sew_output"]+=$vals[csf("total_sew_output")];    
                    $floor_total[$vals[csf("floor_id")]]["total_sew_input"]+=$vals[csf("total_sew_input")];
                    $floor_total[$vals[csf("floor_id")]]["total_sew_output"]+=$vals[csf("total_sew_output")];
                    // floor wise details part array

                     
                      $all_po_arr2[$vals[csf("po_break_down_id")]]=$vals[csf("po_break_down_id")]; 

               // }
                
                $data_array_total[$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]] ["total_sew_input"]+=$vals[csf("total_sew_input")];
                $data_array_total[$vals[csf("floor_id")]][$vals[csf("sewing_line")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]] ["total_sew_output"] +=$vals[csf("total_sew_output")];

                $data_array_floor_total[$vals[csf("floor_id")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]] ["total_sew_input"]+=$vals[csf("total_sew_input")]; 
                $data_array_floor_total[$vals[csf("floor_id")]][$vals[csf("buyer_name")]][$vals[csf("job_no_prefix_num")]][$vals[csf("style_ref_no")]][$vals[csf("po_break_down_id")]][$vals[csf("item_number_id")]][$vals[csf("color_number_id")]] ["total_sew_output"]+=$vals[csf("total_sew_output")]; 
                    

            }            
           
        }
        $all_po="'".implode("','", $all_po_arr)."'"; 
        $color_ids="'".implode("','", $all_color_arr)."'"; 
        $item_ids="'".implode("','", $all_item_arr)."'"; 
        $min_input_date_sql="SELECT po_break_down_id ,min(production_date) as production_date from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=4 and po_break_down_id in ($today_all_po_ids) group by po_break_down_id";
        foreach(sql_select($min_input_date_sql) as $k=>$vals)
        {
            $min_input_date_arr[$vals[csf("po_break_down_id")]]=$vals[csf("production_date")];
        }

        $all_po2=implode(",", $all_po_arr2); 
        $min_input_date_sql="SELECT po_break_down_id ,min(production_date) as production_date from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=4 and po_break_down_id in ($all_po2) group by po_break_down_id";
        foreach(sql_select($min_input_date_sql) as $k=>$vals)
        {
            $min_input_date_arr[$vals[csf("po_break_down_id")]]=$vals[csf("production_date")];
        }

    
      $plan_cut_sql="SELECT po_break_down_id,item_number_id,color_number_id, SUM(plan_cut_qnty) as plan_cut ,sum(order_quantity) as order_quantity FROM wo_po_color_size_breakdown   WHERE status_active=1 AND is_deleted=0  AND po_break_down_id in($all_po) AND color_number_id in($color_ids) AND item_number_id in($item_ids) GROUP BY po_break_down_id,item_number_id,color_number_id ";
    $plan_cut_result=sql_select($plan_cut_sql);
    foreach($plan_cut_result as $val_plan)
    {
        $plan_cut_arr[$val_plan[csf("po_break_down_id")]][$val_plan[csf("item_number_id")]][$val_plan[csf("color_number_id")]]["plan_cut"]+=$val_plan[csf("plan_cut")];
        $plan_cut_arr[$val_plan[csf("po_break_down_id")]][$val_plan[csf("item_number_id")]][$val_plan[csf("color_number_id")]]["order_quantity"]+=$val_plan[csf("order_quantity")];

    }

     $po_lib_arr = return_library_array("SELECT id,po_number FROM wo_po_break_down WHERE status_active=1 AND is_deleted=0 and id in($all_po) ","id","po_number"); 

                    foreach($data_array as $f_id=>$f_data)
                    {       

                        foreach($f_data as $l_id=>$l_data) 
                        {
                            $color_span=0;
                            foreach($l_data as $b_id =>$b_data)
                            {
                                foreach($b_data as $job_id=>$job_data)
                                {
                                    foreach($job_data as $style_id=>$style_data)
                                    {
                                        foreach($style_data as $po_id =>$po_data)
                                        {
                                            
                                            foreach($po_data as $item_id =>$item_data)
                                            {
                                                
                                                foreach($item_data as $color_id =>$color_data)
                                                {
                                                    $color_span++;

                                                }
                                                
                                            }
                                            $floor_line_wise_span_array[$f_id][$l_id]=$color_span;
                                        }
                                    }
                                }
                            }
                        }
                    }


                    foreach($data_array_floor as $f_id=>$f_data)
                    {      

                        
                            foreach($f_data as $b_id =>$b_data)
                            {   $color_span=0;
                                foreach($b_data as $job_id=>$job_data)
                                {
                                    foreach($job_data as $style_id=>$style_data)
                                    {
                                        foreach($style_data as $po_id =>$po_data)
                                        {
                                            
                                            foreach($po_data as $item_id =>$item_data)
                                            {
                                                
                                                foreach($item_data as $color_id =>$color_data)
                                                {
                                                    $color_span++;

                                                }
                                                
                                            }
                                            $floor_buyer_wise_span_array[$f_id][$b_id]=$color_span;
                                        }
                                    }
                                }
                            }
                        
                    }


                    /* echo "<pre>";
                   print_r($floor_buyer_wise_span_array);die;*/
   
       ob_start(); 
    ?>
    
         <script type="text/javascript">
             setFilterGrid('table_body',-1);
         </script>
         <br>
         <br>
         
    <div>
                     <tr class="form_caption">
		            	<td colspan="<? echo $colspan;?>" align="center"><strong><? echo "Date:".change_date_format( str_replace("'","",( $today_date) )); ?></strong></td> 
		            </tr>

                    <br>

                    
       
        <table class="rpt_table" width="2100" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" >
            <thead>
                <tr>
                    <th width="40"  style='word-break:break-all' ><p>SL</p></th>
                    <th width="110" style='word-break:break-all' ><p>Line No</p></th>
                    <th width="120" style='word-break:break-all' ><p>Floor No</p></th>
                    <th width="120" style='word-break:break-all' ><p>Buyer Name</p></th>
                    <th width="80" style='word-break:break-all' ><p>Job No</p></th>
                    <th width="100" style='word-break:break-all' ><p>Style No</p></th>
                    <th width="110" style='word-break:break-all' ><p>PO NO</p></th>
                    <th width="100" style='word-break:break-all' ><p>Ref NO</p></th>
                    <th width="100" style='word-break:break-all' ><p>File NO</p></th>
                    <th width="110" style='word-break:break-all' ><p>Gmts Item</p></th>
                    <th width="100" style='word-break:break-all' ><p>Color Name</p></th>
                    <th width="80" style='word-break:break-all' ><p>Color Qty</p></th>
                    <th width="100" style='word-break:break-all' ><p>Excess Cut %</p></th>
                    <th width="100" style='word-break:break-all' ><p>Plan Qty</p></th>
                    <th width="100" style='word-break:break-all' ><p>First Input Date</p></th>
                    <th width="100"  style='word-break:break-all' ><p>Today Input</p></th>
                    <th width="100"  style='word-break:break-all' ><p>Total Input</p></th>
                     <th width="100" style='word-break:break-all'  ><p>Today Output</p></th>
                    <th width="100"  style='word-break:break-all' ><p>Total Output</p></th>
                    <th width="100" style='word-break:break-all'  ><p>WIP</p></th>
                    <th width="100" style='word-break:break-all' ><p>Sew. Balance (Plan)</p></th>
                </tr>
            </thead>
        </table>
        <div style="width:2140px;max-height:425px;overflow-y:scroll; " id="scroll_body" > 
            <table class="rpt_table" width="2100" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                 
                    <?   
                    $m=1; 
                    $gr_floor_wise_today_sew_input=0;
                    $gr_floor_wise_today_sew_output=0;
                    $gr_floor_wise_total_sew_input=0;
                    $gr_floor_wise_total_sew_output=0;
                    $gr_floor_wise_wip=0;
                    $gr_floor_wise_today_sew_bal=0;                 
                    foreach($data_array as $f_id=>$f_data)
                    { 
                        ksort($f_data);
                         $floor_sew_bal=0;

                        foreach($f_data as $l_id=>$l_data) 
                        {   ksort($l_data);
                            $l=0;
                            $floor_line_sew_bal=0;
                            foreach($l_data as $b_id =>$b_data)
                            {
                                foreach($b_data as $job_id=>$job_data)
                                {
                                    foreach($job_data as $style_id=>$style_data)
                                    {
                                        foreach($style_data as $po_id =>$po_data)
                                        {
                                            foreach($po_data as $item_id =>$item_data)
                                            {
                                                foreach($item_data as $color_id =>$color_data)
                                                {
                                                    


                    
                                                    if ($m % 2 == 0)
                                                    $bgcolor = "#E9F3FF";
                                                    else
                                                    $bgcolor = "#FFFFFF"; 
                                                    ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
                                   <td style='word-break:break-all'  width="40" align="center"><p><?  echo $m;?></p></td>
                                   <?
                                   if($l==0)
                                   {
                                    $row_sp=$floor_line_wise_span_array[$f_id][$l_id];
                                    ?>
                                     <td style='word-break:break-all'  width="110" align="center" rowspan="<? echo $row_sp; ?>" valign="middle"><p><?  if($prod_reso_allo==1){ echo $line_lib_resource[$l_id];} else{ echo  $line_lib[$l_id];}?></p></td>
                                   <td style='word-break:break-all'  width="120" align="center" rowspan="<? echo $row_sp; ?>" valign="middle"><p><?  echo $floor_lib[$f_id];?></p></td>

                                    <?
                                    
                                   }
                                   $excess_cut_perc=($plan_cut_arr[$po_id][$item_id][$color_id]["plan_cut"]-$plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"])*100/$plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"];
                                   ?>
                                  
                                   <td style='word-break:break-all'  width="120"><p><?  echo $buyer_lib[$b_id];?></p></td>
                                   <td style='word-break:break-all'  width="80"><p><?  echo $job_id;?></p></td>
                                   <td style='word-break:break-all'  width="100"><p><?  echo $style_id;?></p></td>
                                   <td style='word-break:break-all'  width="110"><p><?  echo $po_lib_arr[$po_id];?></p></td>
                                   <td style='word-break:break-all'  width="100"><p><?  echo $color_data['int_ref'];?></p></td>
                                   <td style='word-break:break-all'  width="100"><p><?  echo $color_data['file_no'];?></p></td>
                                   <td style='word-break:break-all'  width="110"><p><? echo $garments_item[$item_id]; ?></p></td>
                                   <td style='word-break:break-all'  width="100"><p><?  echo  $color_lib[$color_id];?></p> </td>
                                   <td style='word-break:break-all'  width="80" align="right"> <p><?  echo   $plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"];?></p></td>
                                   <td style='word-break:break-all'  width="100" align="right"><p><? if($excess_cut_perc>0) { echo  number_format($excess_cut_perc,0);}?></p></td>
                                  <td style='word-break:break-all'  width="100" align="right"><p><?  echo   $plan_cut_arr[$po_id][$item_id][$color_id]["plan_cut"];?></p></td>
                                   <td style='word-break:break-all'  width="100"> <p><? echo  change_date_format($min_input_date_arr[$po_id]);?> </p></td>
                                   <td style='word-break:break-all'  width="100" align="right"> <p> <? echo $color_data["today_sew_input"]; ?></p></td>
                                   <td style='word-break:break-all'  width="100" align="right"> <p> <? echo $tot_swin= $data_array_total[$f_id][$l_id][$b_id][$job_id][$style_id][$po_id][$item_id][$color_id]["total_sew_input"]; ?> </p></td>
                                   <td style='word-break:break-all'  width="100" align="right"> <p> <? echo $color_data["today_sew_output"]; ?></p></td>  
                                   <td style='word-break:break-all'  width="100" align="right"> <p> <? echo $tot_swout= $data_array_total[$f_id][$l_id][$b_id][$job_id][$style_id][$po_id][$item_id][$color_id]["total_sew_output"]; ?></p></td>
                                   <td style='word-break:break-all'  width="100" align="right"><p><?  echo  $tot_swin-$tot_swout;?></p> </td>
                                   <td style='word-break:break-all'  width="100" align="right"><p><?  echo  $plan_cut_arr[$po_id][$item_id][$color_id]["plan_cut"]-$tot_swout;?></p> </td>
                                    
                            </tr>

                            <?  $floor_line_sew_bal+=$plan_cut_arr[$po_id][$item_id][$color_id]["plan_cut"]-$tot_swout;
                            
                            $l++;
                            $m++;

                                                }
                                            }
                                        }
                                    }
                                }
                            }
                             ?>
                 <tr bgcolor="#EAEAEA"  onClick="change_color('tr_<? echo $m+1000; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m+1000; ?>">
                    <th  style='word-break:break-all' colspan="15" align="right"><p>Line Total:</p></th>                    
                    <th style='word-break:break-all'   align="right"><p><? echo $floor_line_total[$f_id][$l_id]["today_sew_input"]; ?></p></th>
                    <th  style='word-break:break-all'  align="right"><p><? echo $floor_line_total[$f_id][$l_id]["total_sew_input"]; ?></p></th>
                      <th style='word-break:break-all'   align="right"><p><? echo $floor_line_total[$f_id][$l_id]["today_sew_output"]; ?></p></th>
                    <th  style='word-break:break-all'  align="right"><p><? echo $floor_line_total[$f_id][$l_id]["total_sew_output"]; ?></p></th>
                      
                    <th style='word-break:break-all'  width="100" align="right"><p><? echo $floor_line_total[$f_id][$l_id]["total_sew_input"]-$floor_line_total[$f_id][$l_id]["total_sew_output"]; ?></p></th>
                    <th style='word-break:break-all'  align="right"><p><? echo $floor_line_sew_bal;$floor_sew_bal+=$floor_line_sew_bal;?></p></th>
                </tr>

                        <?   

                        }
                       
                                
                   
                    ?>
                <tr bgcolor="#EAEAEA"  onClick="change_color('tr_<? echo $m+2000; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m+2000; ?>">
                    <th style='word-break:break-all'  colspan="15" align="right"><p>Floor Total:</p></th>                    
                    <th style='word-break:break-all'   align="right"><p><? echo $floor_total[$f_id]["today_sew_input"]; ?></p></th>
                    <th style='word-break:break-all'   align="right"><p><? echo $floor_total[$f_id]["total_sew_input"]; ?></p></th>
                      <th style='word-break:break-all'   align="right"><p><? echo $floor_total[$f_id]["today_sew_output"]; ?></p></th>
                    <th  style='word-break:break-all'  align="right"><p><? echo $floor_total[$f_id]["total_sew_output"]; ?></p></th>
                      
                    <th  style='word-break:break-all'   align="right"><p><? echo $floor_total[$f_id]["total_sew_input"]-$floor_total[$f_id]["total_sew_output"]; ?></p></th>
                    <th style='word-break:break-all'  align="right"><p><? echo $floor_sew_bal;?></p></th>
                </tr>

                        <?   
                                $gr_floor_wise_today_sew_input +=$floor_total[$f_id]["today_sew_input"];
                                $gr_floor_wise_today_sew_output +=$floor_total[$f_id]["today_sew_output"];
                                $gr_floor_wise_total_sew_input +=$floor_total[$f_id]["total_sew_input"];
                                $gr_floor_wise_total_sew_output +=$floor_total[$f_id]["total_sew_output"];
                                $gr_floor_wise_wip +=$floor_total[$f_id]["total_sew_input"]-$floor_total[$f_id]["total_sew_output"];
                                $gr_floor_wise_today_sew_bal+=$floor_sew_bal;
                                //$gr_floor_wise_today_sew_bal +=$floor_sew_bal;
                    }
                    ?>  

                    <tr bgcolor="#A4C2EA"  onClick="change_color('tr_<? echo $m+3000; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m+3000; ?>">
                                <th style='word-break:break-all'  colspan="15" align="right"><p>Grand Total:</p></th>                    
                                <th style='word-break:break-all'   align="right"><p><? echo $gr_floor_wise_today_sew_input; ?></p></th>
                                <th style='word-break:break-all'   align="right"><p><? echo $gr_floor_wise_total_sew_input; ?></p></th>
                                  <th style='word-break:break-all'   align="right"><p><? echo $gr_floor_wise_today_sew_output; ?></p></th>
                                <th style='word-break:break-all'   align="right"><p><? echo $gr_floor_wise_total_sew_output; ?></p></th>
                                  
                                <th style='word-break:break-all'   align="right"><p><? echo $gr_floor_wise_wip; ?></p></th>
                                <th style='word-break:break-all'  align="right"><p><? echo $gr_floor_wise_today_sew_bal;?></p></th>
                            </tr>
                                     
                                            
                       
               
            </table>
        </div>
    </div>

    <div>
    <br> <br> <br>
    <legend style="width:1870px;">FLOOR WISE SUMMARY</legend>
    <? $k=1;
    $gr_floor_wise_today_sew_input=0;
    $gr_floor_wise_today_sew_output=0;
    $gr_floor_wise_total_sew_input=0;
    $gr_floor_wise_total_sew_output=0;
    $gr_floor_wise_wip=0;
    $gr_floor_wise_today_sew_bal=0;
     foreach($data_array_floor as $f_id=>$f_data)
                    {   
                     ksort($f_data);
                         ?>
                        
                        <table class="rpt_table" width="1870" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" >
                            <thead>
                            <tr>
                                <td colspan="19"><b><? echo $floor_lib[$f_id]; ?></b></td>
                            </tr>
                                <tr>
                                    <th style='word-break:break-all'  width="40"><p>SL</p></th>                                    
                                    <th style='word-break:break-all'  width="120"><p>Buyer Name</p></th>
                                    <th style='word-break:break-all'  width="80"><p>Job No</p></th>
                                    <th style='word-break:break-all'  width="100"><p>Style No</p></th>
                                    <th style='word-break:break-all'  width="110"><p>PO NO</p></th>
                                    <th style='word-break:break-all'  width="100"><p>Ref NO</p></th>
                                    <th style='word-break:break-all'  width="100"><p>File NO</p></th>
                                    <th style='word-break:break-all'  width="110"><p>Gmts Item</p></th>
                                    <th style='word-break:break-all'  width="100"><p>Color Name</p></th>
                                    <th style='word-break:break-all'  width="80"><p>Color Qty</p></th>
                                    <th style='word-break:break-all'  width="100"><p>Excess Cut %</p></th>
                                    <th style='word-break:break-all'  width="100"><p>Plan Qty</p></th>
                                    <th style='word-break:break-all'  width="100"><p>First Input Date</p></th>
                                    <th style='word-break:break-all'  width="100" ><p>Today Input</p></th>
                                    <th style='word-break:break-all'  width="100" ><p>Total Input</p></th>
                                     <th style='word-break:break-all'  width="100" ><p>Today Output</p></th>
                                    <th style='word-break:break-all'  width="100" ><p>Total Output</p></th>
                                    <th style='word-break:break-all'  width="100" ><p>WIP</p></th>
                                    <th style='word-break:break-all'  width="100"><p>Sew. Balance (Plan)</p></th>
                                </tr>
                            </thead>
                        </table>

                        <div style="width:1910px;max-height:425px;overflow-y:scroll; " id="scroll_body" > 
            <table class="rpt_table" width="1870" cellpadding="0" cellspacing="0" border="1" rules="all" id="">


                         <?

                            $total_floor_sew_bal=0;
                            foreach($f_data as $b_id =>$b_data)
                            {   $p=0;
                                
                                foreach($b_data as $job_id=>$job_data)
                                { 
                                    foreach($job_data as $style_id=>$style_data)
                                    {
                                        foreach($style_data as $po_id =>$po_data)
                                        {
                                             
                                            foreach($po_data as $item_id =>$item_data)
                                            {
                                                
                                                foreach($item_data as $color_id =>$color_data)
                                                {
                                                    if ($k % 2 == 0)
                                                    {
                                                        $bgcolor = "#E9F3FF";
                                                    }
                                                    else
                                                    {

                                                        $bgcolor = "#FFFFFF"; 
                                                    }

                                                     
                                                    
                                                        ?>
                                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tri_<? echo $k; ?>', '<? echo $bgcolor; ?>')" id="tri_<? echo $k; ?>">
                                                        <td style='word-break:break-all'  width="40" align="center"><p><?  echo $k;?></p></td>
                                                       <?
                                                       if($p==0)
                                                       {
                                                        $row_sp=$floor_buyer_wise_span_array[$f_id][$b_id];
                                                        ?>
                                                    
                                                        <td style='word-break:break-all'  width="120" align="center" rowspan="<? echo $row_sp; ?>" valign="middle"><p><?  echo $buyer_lib[$b_id];?></p></td>

                                                        <?

                                                       }
                                                       $excess_cut_perc=($plan_cut_arr[$po_id][$item_id][$color_id]["plan_cut"]-$plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"])*100/$plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"];
                                                       ?>
                                  
                                                   
                                                        <td style='word-break:break-all'  width="80"><p><?  echo $job_id;?></p></td>
                                                        <td style='word-break:break-all'  width="100"><p><?  echo $style_id;?></p></td>
                                                        <td style='word-break:break-all'  width="110"><p><?  echo $po_lib_arr[$po_id];?></p></td>
                                                        <td style='word-break:break-all'  width="100"><p><?  echo $color_data['int_ref'];?></p></td>
                                                        <td style='word-break:break-all'  width="100"><p><?  echo $color_data['file_no'];?></p></td>
                                                        <td style='word-break:break-all'  width="110"><p><? echo $garments_item[$item_id]; ?></p></td>
                                                        <td style='word-break:break-all'  width="100"><p><?  echo  $color_lib[$color_id];?></p> </td>
                                                        <td style='word-break:break-all'  width="80" align="right"> <p><?  echo   $plan_cut_arr[$po_id][$item_id][$color_id]["order_quantity"];?></p></td>
                                                        <td style='word-break:break-all'  width="100" align="right"><p><?  echo  number_format($excess_cut_perc,0);?></p></td>
                                                        <td style='word-break:break-all'  width="100" align="right"><p><?  echo   $plan_cut_arr[$po_id][$item_id][$color_id]["plan_cut"];?></p></td>
                                                        <td style='word-break:break-all'  width="100"> <p><? echo  change_date_format($min_input_date_arr[$po_id]);?> </p></td>
                                                        <td style='word-break:break-all'  width="100" align="right"> <p> <? echo $color_data["today_sew_input"]; ?></p></td>
                                                        <td style='word-break:break-all'  width="100" align="right"> <p> <? echo $tot_swin_fl= $data_array_floor_total[$f_id][$b_id][$job_id][$style_id][$po_id][$item_id][$color_id]["total_sew_input"] ; ?></p></td>                  

                                                        <td style='word-break:break-all'  width="100" align="right"> <p> <? echo $color_data["today_sew_output"]; ?></p></td>
                                                        <td style='word-break:break-all'  width="100" align="right"> <p> <? echo $tot_swout_fl= $data_array_floor_total[$f_id][$b_id][$job_id][$style_id][$po_id][$item_id][$color_id]["total_sew_output"] ; ?> </p></td>
                                                        <td style='word-break:break-all'  width="100" align="right"><p><?  echo  $tot_swin_fl-$tot_swout_fl;?></p> </td>
                                                        <td style='word-break:break-all'  width="100" align="right"><p><?  echo $sew_bal= $plan_cut_arr[$po_id][$item_id][$color_id]["plan_cut"]-$tot_swout_fl;?></p> </td>
                                    
                                                    </tr>



                                                     <?
                                                     $total_floor_sew_bal +=$sew_bal;
                                                      $k++;
                                                      $p++;
                                                     

                                                }
                                                
                                            }
                                            
                                        }
                                    }
                                }
                            }
                                $gr_floor_wise_today_sew_input +=$floor_total[$f_id]["today_sew_input"];
                                $gr_floor_wise_today_sew_output +=$floor_total[$f_id]["today_sew_output"];
                                $gr_floor_wise_total_sew_input +=$floor_total[$f_id]["total_sew_input"];
                                $gr_floor_wise_total_sew_output +=$floor_total[$f_id]["total_sew_output"];
                                $gr_floor_wise_wip +=$floor_total[$f_id]["total_sew_input"]-$floor_total[$f_id]["total_sew_output"];
                                $gr_floor_wise_today_sew_bal+=$total_floor_sew_bal;
                            ?>
                            <tr bgcolor="#EAEAEA"  onClick="change_color('tri_<? echo $k+6000; ?>', '<? echo $bgcolor; ?>')" id="tri_<? echo $k+6000; ?>">
                                <th style='word-break:break-all'  colspan="13" align="right"><p>Floor Total:</p></th>                    
                                <th style='word-break:break-all'   align="right"><p><? echo $floor_total[$f_id]["today_sew_input"]; ?></p></th>
                                <th  style='word-break:break-all'  align="right"><p><? echo $floor_total[$f_id]["total_sew_input"]; ?></p></th>
                                  <th style='word-break:break-all'   align="right"><p><? echo $floor_total[$f_id]["today_sew_output"]; ?></p></th>
                                <th style='word-break:break-all'   align="right"><p><? echo $floor_total[$f_id]["total_sew_output"]; ?></p></th>
                                  
                                <th style='word-break:break-all'   align="right"><p><? echo $floor_total[$f_id]["total_sew_input"]-$floor_total[$f_id]["total_sew_output"]; ?></p></th>
                                <th style='word-break:break-all'  align="right"><p><? echo $total_floor_sew_bal;?></p></th>
                            </tr>

                            <?
                        
                    }



    ?>
                           <tr bgcolor="#A4C2EA"  onClick="change_color('tri_<? echo $k+5000; ?>', '<? echo $bgcolor; ?>')" id="tri_<? echo $k+5000; ?>">
                                <th style='word-break:break-all'  colspan="13" align="right"><p>Grand Total:</p></th>                    
                                <th style='word-break:break-all'   align="right"><p><? echo $gr_floor_wise_today_sew_input; ?></p></th>
                                <th  style='word-break:break-all'  align="right"><p><? echo $gr_floor_wise_total_sew_input; ?></p></th>
                                  <th style='word-break:break-all'   align="right"><p><? echo $gr_floor_wise_today_sew_output; ?></p></th>
                                <th style='word-break:break-all'   align="right"><p><? echo $gr_floor_wise_total_sew_output; ?></p></th>
                                  
                                <th style='word-break:break-all'   align="right"><p><? echo $gr_floor_wise_wip; ?></p></th>
                                <th style='word-break:break-all'  align="right"><p><? echo $gr_floor_wise_today_sew_bal;?></p></th>
                            </tr>

    </table>
    </div>
       
    </div>
     
     
    
   
    <?    
    foreach (glob("$user_id*.xls") as $filename) 
    {
        if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename,'w');
    $is_created = fwrite($create_new_doc,ob_get_contents());
    echo "$total_data####$filename";
    exit();      
    
} 

if($action=='all_prod_qty_popup')   
{   
    extract($_REQUEST); 
    echo load_html_head_contents("Production Info", "../../", 1, 1,$unicode,'','');
    list($po_id,$color,$item,$line,$po_number)=explode("**",$datas);
    $color_lib = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name"); 
    $size_lib = return_library_array("SELECT id,size_name FROM lib_size WHERE status_active=1 AND is_deleted=0","id","size_name"); 
  
 $productin_sql="SELECT m.production_date,m.challan_no,SUM(n.production_qnty) AS qnty FROM pro_garments_production_dtls n ,pro_garments_production_mst  m,wo_po_color_size_breakdown o WHERE n.status_active=1 AND n.is_deleted=0 AND m.status_active=1 AND m.is_deleted=0 AND o.status_active=1 AND o.is_deleted=0  AND n.production_type=4 AND m.id=n.mst_id  AND o.id= n.color_size_break_down_id AND o.color_number_id='$color'
 AND m.production_type=4 AND m.po_break_down_id='$po_id' AND m.item_number_id='$item' AND m.sewing_line='$line' GROUP BY m.production_date,m.challan_no order by m.production_date";
 

    $productin_sql_size="SELECT m.production_date,m.challan_no,SUM(n.production_qnty) AS qnty,o.size_number_id FROM pro_garments_production_dtls n ,pro_garments_production_mst  m,wo_po_color_size_breakdown o WHERE n.status_active=1 AND n.is_deleted=0 AND m.status_active=1 AND m.is_deleted=0 AND o.status_active=1 AND o.is_deleted=0  AND n.production_type=4 AND m.id=n.mst_id  AND o.id= n.color_size_break_down_id AND o.color_number_id='$color'
 AND m.production_type=4 AND m.po_break_down_id='$po_id' AND m.item_number_id='$item' AND m.sewing_line='$line' GROUP BY m.production_date,m.challan_no,o.size_number_id";

    $size_sql="SELECT id,po_break_down_id,item_number_id,size_number_id,color_number_id FROM wo_po_color_size_breakdown WHERE status_active=1 AND is_deleted=0 AND po_break_down_id='$po_id' AND item_number_id='$item' AND color_number_id='$color'";
    $size_result=sql_select($size_sql);
    foreach($size_result as $size_val)
    {
        $size_arr[$size_val[csf("size_number_id")]]=$size_val[csf("size_number_id")];
    }
    foreach(sql_select( $productin_sql_size) as $key=>$rows)
    {
        $prod_arr_size[$rows[csf("production_date")]][$rows[csf("challan_no")]][$rows[csf("size_number_id")]] +=$rows[csf("qnty")];
         $size_wise_total_qnty_array[$rows[csf("size_number_id")]]+=$rows[csf("qnty")];
    }

     
       
     
 ?>
    <fieldset>
    <legend><b> Production Info</b></legend>
     <div style="width:470px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
             <tr>
                 <th colspan="<? echo 3+count($size_arr);  ?>">Total Input Qty POP-UP:</th>
             </tr>
              <tr>
                 <th >PO: <? echo $po_number;?></th>
                 <th colspan="<? echo 2+count($size_arr);  ?>">Color: <? echo $color_lib[$color]; ?></th>
              </tr>

             <tr>
                 <th>Input Date</th>
                 <th>Challan</th>
                 <th>Challan Qty</th>
                 <?
                 foreach($size_arr as $key=>$vals_size)
                 {
                    ?>
                     <th><? echo $size_lib[$vals_size];?></th>

                    <?

                 }
                 ?>
             </tr>
             <?
             $prod_qty_challan=0;
             foreach(sql_select($productin_sql) as $prod_val)
             {
                ?>
              <tr>
                <td align="center"><? echo change_date_format($prod_val[csf("production_date")],"d/m/Y");?></td>
                <td align="center"><? echo $prod_val[csf("challan_no")];?></td>
                <td align="center"><? echo $prod_qtys=$prod_val[csf("qnty")];?></td>
                <?
                $prod_qty_challan+=$prod_qtys;
                foreach($size_arr as $key=>$vals_size)
                 {
                    ?>
                     <td align="center"><? echo  $prod_arr_size[$prod_val[csf("production_date")]][$prod_val[csf("challan_no")]][$vals_size];?></td>

                    <?

                 }
                 ?>
              </tr>
              <?
             }

             ?>
             <tr>
                 <th colspan="2" align="right">Total</th>
                 <th><? echo $prod_qty_challan;?></th>
                 <?
                 foreach($size_arr as $key=>$vals_size)
                 {
                    ?>
                     <th align="center"><? echo  $size_wise_total_qnty_array[$vals_size];?></th>

                    <?

                 }
                 ?>
             </tr>
        </table>
     </div>
    </fieldset>      
        

     
 <?
 exit();
}
 
?>