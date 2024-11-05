<? 
header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------


/*
|------------------------------------------------------------------------
| for report_generate
|------------------------------------------------------------------------
*/
if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

    $company            = str_replace("'","",$cbo_company_id);
    $cbo_within_group   = str_replace("'","",$cbo_within_group);
    $cbo_inbound_subcon = str_replace("'","",$cbo_inbound_subcon);
    $year               = str_replace("'","",$cbo_year);
	$month_to           = str_replace("'","",$cbo_to_month);

    $num_days = cal_days_in_month(CAL_GREGORIAN, $month_to, $year);
	$end_date=$year."-".$month_to."-$num_days";

    if ($month_to>9) 
    {
        //echo $month_to.'=string';die;
        $end_date2=$year."-".$month_to."-01";
    }
    else {
        //echo $month_to.'=string2';die;
        $end_date2=$year."-0".$month_to."-01";
    }

    // $end_date2=$year."-0".$month_to."-01";
    $start_date = date('Y-m-d', strtotime($end_date2.'-1 month'));
    //echo $start_date = date('Y-m-d', strtotime($end_date2.'-1 month')).'='.$end_date;

    $end_date22=explode('-',$end_date2);
    $start_date11=explode('-',$start_date);
    

    if ($company==0 || $company=="") $companyCond=""; else $companyCond="  and a.company_id in($company)";
    if ($cbo_within_group==0 || $cbo_within_group=="") $withinGroupCond=""; else $withinGroupCond="  and d.within_group in($cbo_within_group)";
    if ($cbo_within_group==0 || $cbo_within_group=="") $knitting_withinGroupCond=""; else $knitting_withinGroupCond="  and e.within_group in($cbo_within_group)";
    if ($cbo_within_group==0 || $cbo_within_group=="") $fdg_withinGroupCond=""; else $fdg_withinGroupCond="  and c.within_group in($cbo_within_group)";

    // if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	if($db_type==0) 
	{
		$date_cond=" and f.process_end_date between '$start_date' and '$end_date'";
		$knitting_date_cond=" and a.receive_date between '$start_date' and '$end_date'";
		$knitting_sub_date_cond=" and a.product_date between '$start_date' and '$end_date'";
		$fdg_date_cond=" and a.issue_date between '$start_date' and '$end_date'";
		$inbskd_date_cond=" and a.delivery_date between '$start_date' and '$end_date'";
		$aop_date_cond=" and a.product_date between '$start_date' and '$end_date'";
	}
	if($db_type==2) 
	{
		
		$date_cond=" and f.process_end_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		$knitting_date_cond=" and a.receive_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		$knitting_sub_date_cond=" and a.product_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		$fdg_date_cond=" and a.issue_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		$inbskd_date_cond=" and a.delivery_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		$aop_date_cond=" and a.product_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
       
	}
	// echo $date_cond;die;


    $companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");

    // ==================== Dyeing Production CB Report Start ===================== 
   
    $sql = "SELECT a.id as batch_id, a.total_trims_weight, a.batch_weight, f.process_end_date as production_date,d.within_group,f.floor_id
    from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a 
    where f.batch_id=a.id and a.id=b.mst_id and f.batch_id=b.mst_id and b.po_id=d.id 
    $companyCond $date_cond $withinGroupCond
    and a.entry_form=0   and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1 order by f.process_end_date ";
   
    
    if($cbo_inbound_subcon==1)
    {
        $sql_subcon="SELECT a.id as batch_id, a.total_trims_weight, a.batch_weight, b.batch_qnty AS sub_batch_qnty, f.process_end_date as production_date, null as within_group, f.floor_id
        from pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d, pro_batch_create_mst a, pro_fab_subprocess f
        where f.batch_id=a.id and f.batch_id=b.mst_id $companyCond $date_cond and a.entry_form=36 and a.id=b.mst_id and f.entry_form=38 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 ";
       
    }

    //echo $sql_subcon;
   
    $batchdata=sql_select($sql);
    $subcondata=sql_select($sql_subcon);
    
    $batchIdsChk = array();
    $batchIdsArr = array();
    foreach($batchdata as $row)
    {
        if($batchIdsChk[$row[csf("batch_id")]]=='')
        {
            $batchIdsChk[$row[csf("batch_id")]]=$row[csf("batch_id")];
            array_push($batchIdsArr,$row[csf("batch_id")]);
        }
    }

   
    $sql_prod_ref= sql_select("select a.id,a.batch_id, b.prod_id, b.const_composition, (b.batch_qty) as batch_qty, (b.production_qty) as production_qty
    from  pro_fab_subprocess a, pro_fab_subprocess_dtls b
    where a.id = b.mst_id and a.load_unload_id = 2 and b.load_unload_id=2 and b.entry_page=35 and a.status_active = 1 and b.status_active=1 ".where_con_using_array($batchIdsArr,0,'a.batch_id')." ");
    $batch_product_arr = array();
    foreach ($sql_prod_ref as $val) 
    {
        $batch_product_arr[$val[csf("batch_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
    }

    // echo "<pre>";
    // print_r($batch_product_arr);

    $prodCapacityFromArr = array();
    $prodCapacityToArr   = array();
    $batchIdChk          = array();
    $dyeing_to_qty       = 0;
    $dyeing_frm_qty      = 0;
    foreach($batchdata as $row)
    {
        if($batchIdChk[$row[csf("batch_id")]]=='')
        {
            $batchIdChk[$row[csf("batch_id")]]=$row[csf("batch_id")];

            if(strtotime($end_date2) <= strtotime($row[csf("production_date")]))
            {
              
                $prod_data = explode('-',$row[csf("production_date")]);
                $prodCapacityToArr[$prod_data[0]]["production"] +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
                //$prodCapacityToArr[$row[csf("production_date")]]["production"] +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
                $dyeing_to_qty +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
                
            }
            else
            {
                $prod_data = explode('-',$row[csf("production_date")]);
                $prodCapacityFromArr[$prod_data[0]]["production"] +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
                // $prodCapacityFromArr[$row[csf("production_date")]]["production"] +=$batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];
                $dyeing_frm_qty += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"]+$row[csf("total_trims_weight")];

            } 
        }
       
    }
    unset($batchdata);
   
    //  echo "<pre>";print_r($prodCapacityToArr);

    if($subcondata > 0 )
    {
        $batchIdsubconChk = array();
        $floorIdsubconChk = array();
        foreach($subcondata as $row)
        {
            if($batchIdsubconChk[$row[csf("batch_id")]]=='')
            {
                $batchIdsubconChk[$row[csf("batch_id")]]=$row[csf("batch_id")];
    
                if(strtotime($end_date2) <= strtotime($row[csf("production_date")]))
                {
                    $prod_data = explode('-',$row[csf("production_date")]);
                    $prodCapacityToArr[$prod_data[0]]["production"] += $row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
                    $dyeing_to_qty += $row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
                }
                else 
                {
                    $prod_data = explode('-',$row[csf("production_date")]);
                    $prodCapacityFromArr[$prod_data[0]]["production"] +=$row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
                    $dyeing_frm_qty +=$row[csf("sub_batch_qnty")]+$row[csf("total_trims_weight")];
                  
                } 
            }
           
        }
        unset($subcondata);
    }


    // ==================== Knitting Production CB Report Start ===================== 
	

    $sql_knitting_result="SELECT * from ( (SELECT a.receive_date, b.grey_receive_qnty 
    from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e,wo_booking_mst f 
    where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=f.booking_no and a.entry_form=2 
    and a.item_category=13 and c.entry_form=2 and c.trans_type=1 $companyCond and a.status_active=1 
    and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and 
    f.is_deleted=0 $knitting_date_cond $knitting_withinGroupCond
    group by a.receive_date,b.grey_receive_qnty) 
    union all 
    (SELECT a.receive_date, b.grey_receive_qnty 
    from inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e ,wo_non_ord_samp_booking_mst g where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.sales_booking_no=g.booking_no and g.status_active=1 and g.is_deleted=0 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 $companyCond and a.status_active=1  and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $knitting_date_cond $knitting_withinGroupCond group by a.receive_date,b.grey_receive_qnty ) 
    union all 
    (SELECT a.receive_date, b.grey_receive_qnty 
    from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,fabric_sales_order_mst e 
    where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.within_group=2 and a.entry_form=2 and a.item_category=13 and c.entry_form=2 and c.trans_type=1 $companyCond and a.status_active=1 and a.is_deleted=0 and 
    b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $knitting_date_cond $knitting_withinGroupCond group by a.receive_date,b.grey_receive_qnty 
    ) ) 
    order by receive_date ";

    if($cbo_inbound_subcon==1)
    {
        $sql_knit_inhouse_sub = "SELECT a.product_date as receive_date, b.product_qnty 
        from subcon_production_mst a, subcon_production_dtls b, lib_machine_name c, subcon_ord_dtls d 
        where a.id=b.mst_id and b.machine_id=c.id and b.job_no=d.job_no_mst and d.id=b.order_id and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $companyCond $knitting_sub_date_cond
        group by a.product_date,b.product_qnty 
        order by a.product_date";
    }

    //echo $sql_knitting_result;
    //echo $sql_knit_inhouse_sub;//die;

    $sql_knitting_result_arr=sql_select($sql_knitting_result);
    $sql_knitting_sub_result_arr=sql_select($sql_knit_inhouse_sub);
  

    $knit_prodCapacityToArr = array();
    $knit_prodCapacityFromArr = array();
    $knitting_to_qty       = 0;
    $knitting_frm_qty      = 0;
    foreach($sql_knitting_result_arr as $row)
    {
        if(strtotime($end_date2) <= strtotime($row[csf("receive_date")]))
        {
            
            $prod_data = explode('-',$row[csf("receive_date")]);
            $knit_prodCapacityToArr[$prod_data[0]]["production"] +=$row[csf("grey_receive_qnty")];
            $knitting_to_qty +=$row[csf("grey_receive_qnty")];
            
        }
        else
        {
            $prod_data = explode('-',$row[csf("receive_date")]);
            $knit_prodCapacityFromArr[$prod_data[0]]["production"] +=$row[csf("grey_receive_qnty")];
            $knitting_frm_qty +=$row[csf("grey_receive_qnty")]; 
        } 
       
    }
    unset($sql_knitting_result_arr);
    //echo "<pre>";print_r($knit_prodCapacityFromArr);

    foreach($sql_knitting_sub_result_arr as $row)
    {
        if(strtotime($end_date2) <= strtotime($row[csf("receive_date")]))
        {
            
            $prod_data = explode('-',$row[csf("receive_date")]);
            $knit_prodCapacityToArr[$prod_data[0]]["production"] +=$row[csf("product_qnty")];
            $knitting_to_qty +=$row[csf("product_qnty")];
            
        }
        else
        {
            $prod_data = explode('-',$row[csf("receive_date")]);
            $knit_prodCapacityFromArr[$prod_data[0]]["production"] +=$row[csf("product_qnty")];
            $knitting_frm_qty +=$row[csf("product_qnty")];
        } 
       
    }
    unset($sql_knitting_sub_result_arr);
    //echo "<pre>";print_r($knit_prodCapacityToArr);


    // ==================== Fabric Delivery to Garments CB Report Start  ===================== 

        $fdg_sql = "SELECT a.issue_date,b.issue_qnty 
        from inv_issue_master a, inv_finish_fabric_issue_dtls b, fabric_sales_order_mst c, product_details_master d, 
        pro_batch_create_mst e 
        where a.id=b.mst_id and a.entry_form=224 and a.fso_id=c.id and b.prod_id=d.id and b.batch_id=e.id and 
        a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and 
        d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $companyCond $fdg_date_cond $fdg_withinGroupCond 
        order by a.issue_date ";

        //echo  $fdg_sql;
        $fdg_sql_result_arr=sql_select($fdg_sql);

        $fdg_prodCapacityToArr = array();
        $fdg_prodCapacityFromArr = array();
        $fdgToQty = 0;
        $fdgFrmQty = 0;
        foreach($fdg_sql_result_arr as $row)
        {
            if(strtotime($end_date2) <= strtotime($row[csf("issue_date")]))
            {
                
                $prod_data = explode('-',$row[csf("issue_date")]);
                $fdg_prodCapacityToArr[$prod_data[0]]["production"] +=$row[csf("issue_qnty")];
                $fdgToQty +=$row[csf("issue_qnty")];
                
            }
            else
            {
                $prod_data = explode('-',$row[csf("issue_date")]);
                $fdg_prodCapacityFromArr[$prod_data[0]]["production"] +=$row[csf("issue_qnty")];
                $fdgFrmQty +=$row[csf("issue_qnty")];
            } 
        
        }
        unset($fdg_sql_result_arr);
        //echo "<pre>";print_r($fdg_prodCapacityToArr);

        // ====================  Subcontract  Knitting Delivery CB Report Start  ===================== 

        $inbskd_sql = "SELECT a.delivery_date, b.delivery_qty from subcon_delivery_mst a, subcon_delivery_dtls b where a.process_id=2 and 
        a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $companyCond $inbskd_date_cond order by a.delivery_date"; 
        //echo $inbskd_sql;

        $inbskd_sql_result_arr=sql_select($inbskd_sql);

        $inbskd_prodCapacityToArr = array();
        $inbskd_prodCapacityFromArr = array();
        $inbskdToQty = 0;
        $inbskdFrmQty = 0;
        foreach($inbskd_sql_result_arr as $row)
        {
            if(strtotime($end_date2) <= strtotime($row[csf("delivery_date")]))
            {
                
                $prod_data = explode('-',$row[csf("delivery_date")]);
                $inbskd_prodCapacityToArr[$prod_data[0]]["production"] +=$row[csf("delivery_qty")];
                $inbskdToQty +=$row[csf("delivery_qty")];
                
            }
            else
            {
                $prod_data = explode('-',$row[csf("delivery_date")]);
                $inbskd_prodCapacityFromArr[$prod_data[0]]["production"] +=$row[csf("delivery_qty")];
                $inbskdFrmQty +=$row[csf("delivery_qty")];
            } 
        
        }
        unset($fdg_sql_result_arr);
        //echo "<pre>";print_r($inbskd_prodCapacityToArr);

        // ====================  SubCon Dye And Finishing Delivery CB Report Start  ===================== 

          $inbsfd_sql = "SELECT a.delivery_date, b.delivery_qty from subcon_delivery_mst a, subcon_delivery_dtls b where a.process_id in(3,4) and b.process_id in(3,4) and 
          a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $companyCond $inbskd_date_cond order by a.delivery_date"; 
          //echo $inbsfd_sql;
  
          $inbsfd_sql_result_arr=sql_select($inbsfd_sql);
  
          $inbsfd_prodCapacityToArr = array();
          $inbsfd_prodCapacityFromArr = array();
          $inbsfdToQty = 0;
          $inbsfdFrmQty = 0;
          foreach($inbsfd_sql_result_arr as $row)
          {
              if(strtotime($end_date2) <= strtotime($row[csf("delivery_date")]))
              {
                $prod_data = explode('-',$row[csf("delivery_date")]);
                $inbsfd_prodCapacityToArr[$prod_data[0]]["production"] +=$row[csf("delivery_qty")];
                $inbsfdToQty +=$row[csf("delivery_qty")];
              }
              else
              {
                $prod_data = explode('-',$row[csf("delivery_date")]);
                $inbsfd_prodCapacityFromArr[$prod_data[0]]["production"] +=$row[csf("delivery_qty")];
                $inbsfdFrmQty +=$row[csf("delivery_qty")];
              } 
          
          }
          unset($inbsfd_sql_result_arr);
          //echo "<pre>";print_r($inbsfd_prodCapacityToArr);

        // ====================  Flatbed AOP Production CB Report Start  ===================== 

        $aop_sql = "SELECT a.product_date, b.product_qnty
        from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=453 and a.status_active=1 and a.is_deleted=0 
        and b.status_active=1 and b.is_deleted=0 $companyCond $aop_date_cond
        order by a.product_date"; 
        //echo $aop_sql;

        $aop_sql_result_arr=sql_select($aop_sql);

        $aop_prodCapacityToArr = array();
        $aop_prodCapacityFromArr = array();
        $aopToQty = 0;
        $aopFrmQty = 0;
        foreach($aop_sql_result_arr as $row)
        {
            if(strtotime($end_date2) <= strtotime($row[csf("product_date")]))
            {
              $prod_data = explode('-',$row[csf("product_date")]);
              $aop_prodCapacityToArr[$prod_data[0]]["production"] +=$row[csf("product_qnty")];
              $aopToQty +=$row[csf("product_qnty")];
            }
            else
            {
              $prod_data = explode('-',$row[csf("product_date")]);
              $aop_prodCapacityFromArr[$prod_data[0]]["production"] +=$row[csf("product_qnty")];
              $aopFrmQty +=$row[csf("product_qnty")];
            } 
        
        }
        unset($aop_sql_result_arr);
        //echo "<pre>";print_r($aop_prodCapacityFromArr);  





    //------------------------------------------------------------------------------------------------

    $dayArr = array(1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06',7=>'07',8=>'08',9=>'09',10=>'10',11=>'11',12=>'12',13=>'13',14=>'14',15=>'15',16=>'16',17=>'17',18=>'18',19=>'19',20=>'20',21=>'21',22=>'22',23=>'23',24=>'24',25=>'25',26=>'26',27=>'27',28=>'28',29=>'29',30=>'30',31=>'31');
    
    ob_start();
    ?>
    <style>
        .wrd_brk{word-break: break-all;word-wrap: break-word;}          
    </style>

    <fieldset style="width:730px;" >
  
        <table width="700" cellpadding="0" cellspacing="0"  rules="all" class="rpt_table" style="border:none;">
            <tr class="form_caption" style="border:none;">
                <td colspan="9" align="center" style="border:none;font-size:18px; font-weight:bold" >Textile CB Report For Different Process</td>
            </tr>
            <tr style="border:none;">
                <td colspan="9" align="center" style="border:none; font-size:16px;">
                    Company Name : <? echo $companyArr[str_replace("'", "", $company)]; ?>
                </td>
            </tr>
        </table>

        <div style="width:100%; " align="center">
          
          <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <thead>
                    <tr> <th colspan="7">CTL Production CB Summary:	</th></tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="350">Department</th>
                        <th width="100"> 
                            <?  echo $months[ltrim($end_date22[1],0)].'-'.$end_date22[0];?>
                        </th>
                        <th width="100">
                            <?   echo $months[ltrim($start_date11[1],0)].'-'.$start_date11[0]; ?>
                        </th>
                        <th width="120">Difference</th>
                    </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="30" align="center" bgcolor='#E9F3FF'>1</td>
                    <td width="350" align="center" bgcolor='#E9F3FF'>Dyeing</td>
                    <td width="100" align="right" bgcolor='#E9F3FF'><? echo number_format($dyeing_to_qty,2); ?></td>
                    <td width="100" align="right" bgcolor='#E9F3FF'><? echo number_format($dyeing_frm_qty,2);?></td>
                    <td width="120" align="right" bgcolor='#E9F3FF'><? echo number_format($dyeing_to_qty-$dyeing_frm_qty,2);?></td>
                </tr>
                <tr>
                    <td width="30" align="center" bgcolor='#FFFFFF'>2</td>
                    <td width="350" align="center" bgcolor='#FFFFFF'>Knitting</td>
                    <td width="100" align="right" bgcolor='#FFFFFF'><? echo number_format($knitting_to_qty,2);?></td>
                    <td width="100" align="right" bgcolor='#FFFFFF'><? echo number_format($knitting_frm_qty,2);?></td>
                    <td width="120" align="right" bgcolor='#FFFFFF'><? echo number_format($knitting_to_qty-$knitting_frm_qty,2);?></td>
                </tr>
                <tr> 
                    <td width="30" align="center" bgcolor='#E9F3FF'>3</td>
                    <td width="350" align="center" bgcolor='#E9F3FF'>AOP</td>
                    <td width="100" align="right" bgcolor='#E9F3FF'><? echo number_format($aopToQty,2);?></td>
                    <td width="100" align="right" bgcolor='#E9F3FF'><? echo number_format($aopFrmQty,2);?></td>
                    <td width="120" align="right" bgcolor='#E9F3FF'><? echo number_format($aopToQty-$aopFrmQty,2);?></td>
                </tr>
                <tr>
                    <td width="30" align="center" bgcolor='#FFFFFF'>4</td>
                    <td width="350" align="center" bgcolor='#FFFFFF'>Fabric Delivery to Garments</td>
                    <td width="100" align="right" bgcolor='#FFFFFF'><? echo number_format($fdgToQty,2);?></td>
                    <td width="100" align="right" bgcolor='#FFFFFF'><? echo number_format($fdgFrmQty,2);?></td>
                    <td width="120" align="right" bgcolor='#FFFFFF'><? echo number_format($fdgToQty-$fdgFrmQty,2);?></td>
                </tr>
                <tr>
                    <td width="30" align="center" bgcolor='#E9F3FF'>5</td>
                    <td width="350" align="center" bgcolor='#E9F3FF'> In bound Subcontract  Knitting Delivery</td>
                    <td width="100" align="right" bgcolor='#E9F3FF'><? echo number_format($inbskdToQty,2);?></td>
                    <td width="100" align="right" bgcolor='#E9F3FF'><? echo number_format($inbskdFrmQty,2);?></td>
                    <td width="120" align="right" bgcolor='#E9F3FF'><? echo number_format($inbskdToQty-$inbskdFrmQty,2);?></td>
                </tr>
                <tr>
                    <td width="30" align="center" bgcolor='#FFFFFF'>6</td>
                    <td width="350" align="center" bgcolor='#FFFFFF'>In bound SubCon Dye And Finishing Delivery</td>
                    <td width="100" align="right" bgcolor='#FFFFFF'><? echo number_format($inbsfdToQty,2);?></td>
                    <td width="100" align="right" bgcolor='#FFFFFF'><? echo number_format($inbsfdFrmQty,2);?></td>
                    <td width="120" align="right" bgcolor='#FFFFFF'><? echo number_format($inbsfdToQty-$inbsfdFrmQty,2);?></td>
                </tr>
            </tbody>
          </table>
        </div>

        <br>

        <!-- ==================== Dyeing Production Start =====================  -->
  
        <div style="width:100%; " align="center">
          
            <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
        
                <thead>
                   <tr> <th colspan="7">Dyeing Production CB Report</th></tr>
                    <tr>
                        <th width="100" rowspan="3">Day</th>
                        <th width="200" colspan="2">
                            <? echo $months[ltrim($end_date22[1],0)].'-'.$end_date22[0];?>
                        </th>
                        <th width="200" colspan="2">
                            <? echo $months[ltrim($start_date11[1],0)].'-'.$start_date11[0];?>
                        </th>
                        <th width="200" colspan="2">Difference from Last Month</th>
                    </tr>
                    <tr>
                        <th width="100">Production</th>
                        <th width="100">CB</th>
                        <th width="100">Production</th>
                        <th width="100">CB</th>
                        <th width="100">Production</th>
                        <th width="100">CB</th>
                    </tr>
                    <tr>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                    </tr>
                </thead>
            </table>
            <!-- style="max-height:350px; overflow-y:auto; width:1667px;" -->
            <div>
            <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
          
            <? 	$i=1;
                $prev_to_qty =0;
                $prev_frm_qty =0;
                $frm_total_qty =0;
                $prod_frm_total_qty =0;
                $prod_to_total_qty =0;
                $cbo_frm_total_qty =0;
                $cbo_to_total_qty =0;
                $total_diff_prod_qty =0;
                $total_diff_cb_qty =0;
                foreach ($dayArr as $key => $val) 
                {
                    if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>

                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    
                    <td width="100" class="wrd_brk" align="center"><? echo $val;?>&nbsp;</td>
                    <td width="100" class="wrd_brk" align="right">
                    <? echo number_format($prodCapacityToArr[$val]["production"],2);$prev_to_qty +=$prodCapacityToArr[$val]["production"];?> &nbsp;
                    </td>
                    <td width="100" class="wrd_brk" align="right">
                    <? 
                    if($prodCapacityToArr[$val]["production"] >0 )
                    {
                        $cb_to_qty = $prev_to_qty;
                    }
                    else
                    {
                        $cb_to_qty = 0;
                    }
                    
                    echo number_format($cb_to_qty,2);
                    ?>&nbsp;
                    </td>
                    <td width="100" class="wrd_brk" align="right" >
                    <? echo number_format($prodCapacityFromArr[$val]["production"],2); $prev_from_qty +=$prodCapacityFromArr[$val]["production"]; ?>&nbsp;
                    </td>
                    <td width="100" class="wrd_brk" align="right">
                    <? 
                    if($prodCapacityFromArr[$val]["production"] >0)
                    {
                        $cb_from_qty = $prev_from_qty; 
                      
                    }
                    else
                    {
                        $cb_from_qty = 0;
                    }
                    echo number_format($cb_from_qty,2);
                    ?>&nbsp;
                   
                    </td>
                    <td width="100" class="wrd_brk" align="right">
                    <? 
                    $diff_prod = $prodCapacityToArr[$val]["production"]-$prodCapacityFromArr[$val]["production"];
                    echo number_format($diff_prod,2);?>&nbsp;
                    </td>
                    <td width="100" class="wrd_brk" align="right" >
                    <? 
                    $diff_cb =$cb_to_qty-$cb_from_qty;
                    echo number_format($diff_cb,2);?>&nbsp;
                    </td>
                    
                </tr>
                <?
                $i++;
                $prod_frm_total_qty +=$prodCapacityFromArr[$val]["production"];
                $prod_to_total_qty +=$prodCapacityToArr[$val]["production"];
                $cbo_frm_total_qty +=$cb_from_qty;
                $cbo_to_total_qty +=$cb_to_qty;
                $total_diff_prod_qty +=$diff_prod;
                $total_diff_cb_qty +=$diff_cb;
                }
                ?>
                <tfoot>
                    <tr bgcolor="#a6acaf">
                        <th width="100" >Total : </th>
                        <th width="100" align="right"><? echo number_format($prod_to_total_qty,2);?>&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($cbo_to_total_qty,2);?>&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($prod_frm_total_qty,2);?>&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($cbo_frm_total_qty,2);?>&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($total_diff_prod_qty,2);?>&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($total_diff_cb_qty,2);?>&nbsp;</th>
                    </tr>
                    
                </tfoot>
            </table>
            </div>
        </div>

        <br>

        <!-- ==================== Knitting Production Start =====================  -->

        <div style="width:100%; " align="center" >
          
            <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
        
                <thead>
                   <tr> <th colspan="7">Knitting Production CB Report</th></tr>
                    <tr>
                        <th width="100" rowspan="3">Day</th>
                        <th width="200" colspan="2">
                            <?  echo $months[ltrim($end_date22[1],0)].'-'.$end_date22[0];?>
                        </th>
                        <th width="200" colspan="2">
                            <? echo $months[ltrim($start_date11[1],0)].'-'.$start_date11[0];
                            ?>
                        </th>
                        <th width="200" colspan="2">Difference from Last Month</th>
                    </tr>
                    <tr>
                        <th width="100">Production</th>
                        <th width="100">CB</th>
                        <th width="100">Production</th>
                        <th width="100">CB</th>
                        <th width="100">Production</th>
                        <th width="100">CB</th>
                    </tr>
                    <tr>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                    </tr>
                </thead>
            </table>
            <!-- style="max-height:350px; overflow-y:auto; width:1667px;" -->
            <div>
            <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
          
            <? 	$i=1;
                $knit_prev_to_qty =0;
                $knit_prev_frm_qty =0;
                $knit_prod_frm_total_qty =0;
                $knit_prod_to_total_qty =0;
                $knit_cb_frm_total_qty =0;
                $knit_cb_to_total_qty =0;
                $knit_total_diff_prod_qty =0;
                $knit_total_diff_cb_qty =0;
                foreach ($dayArr as $key => $val) 
                {
                    if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>

                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('ktr<? echo $i;?>','<? echo $bgcolor;?>')" id="ktr<? echo $i;?>">
                    
                    <td width="100" class="wrd_brk" align="center"><? echo $val;?>&nbsp;</td>
                    <td width="100" class="wrd_brk" align="right">
                    <? echo number_format($knit_prodCapacityToArr[$val]["production"],2);$knit_prev_to_qty +=$knit_prodCapacityToArr[$val]["production"];?> &nbsp;
                    </td>
                    <td width="100" class="wrd_brk" align="right">
                    <? 
                    if($knit_prodCapacityToArr[$val]["production"] >0 )
                    {
                        $knit_cb_to_qty = $knit_prev_to_qty;
                    }
                    else
                    {
                        $knit_cb_to_qty = 0;
                    }
                    
                    echo number_format($knit_cb_to_qty,2);
                    ?>&nbsp;
                    </td>
                    <td width="100" class="wrd_brk" align="right" >
                    <? echo number_format($knit_prodCapacityFromArr[$val]["production"],2); $knit_prev_frm_qty +=$knit_prodCapacityFromArr[$val]["production"]; ?>&nbsp;
                    </td>
                    <td width="100" class="wrd_brk" align="right">
                    <? 
                    if($knit_prodCapacityFromArr[$val]["production"] >0)
                    {
                        $knit_cb_from_qty = $knit_prev_frm_qty; 
                      
                    }
                    else
                    {
                        $knit_cb_from_qty = 0;
                    }
                    echo number_format($knit_cb_from_qty,2);
                    ?>&nbsp;
                   
                    </td>
                    <td width="100" class="wrd_brk" align="right">
                    <? 
                    $knit_diff_prod = $knit_prodCapacityToArr[$val]["production"]-$knit_prodCapacityFromArr[$val]["production"];
                    echo number_format($knit_diff_prod,2);?>&nbsp;
                    </td>
                    <td width="100" class="wrd_brk" align="right" >
                    <? 
                    $knit_diff_cb =$knit_cb_to_qty-$knit_cb_from_qty;
                    echo number_format($knit_diff_cb,2);?>&nbsp;
                    </td>
                    
                </tr>
                <?
                $i++;
                
                $knit_prod_to_total_qty +=$knit_prodCapacityToArr[$val]["production"];
                $knit_prod_frm_total_qty +=$knit_prodCapacityFromArr[$val]["production"];
                $knit_cb_frm_total_qty +=$knit_cb_from_qty;
                $knit_cb_to_total_qty +=$knit_cb_to_qty;
                $knit_total_diff_prod_qty +=$knit_diff_prod;
                $knit_total_diff_cb_qty +=$knit_diff_cb;
                }
                ?>
                <tfoot>
                    <tr bgcolor="#a6acaf">
                        <th width="100" >Total : </th>
                        <th width="100" align="right"><? echo number_format($knit_prod_to_total_qty,2);?>&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($knit_cb_to_total_qty,2);?>&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($knit_prod_frm_total_qty,2);?>&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($knit_cb_frm_total_qty,2);?>&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($knit_total_diff_prod_qty,2);?>&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($knit_total_diff_cb_qty,2);?>&nbsp;</th>
                    </tr>
                    
                </tfoot>
            </table>
            </div>
        </div>

        <br>

        <!-- ==================== Flatbed AOP Production CB Report =====================  -->

        <div style="width:100%; " align="center" >
          
            <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
        
                <thead>
                   <tr> <th colspan="7">Flatbed AOP Production CB Report</th></tr>
                    <tr>
                        <th width="100" rowspan="3">Day</th>
                        <th width="200" colspan="2">
                            <?  echo $months[ltrim($end_date22[1],0)].'-'.$end_date22[0];
                            ?>
                        </th>
                        <th width="200" colspan="2">
                            <?  echo $months[ltrim($start_date11[1],0)].'-'.$start_date11[0]; ?>
                        </th>
                        <th width="200" colspan="2">Difference from Last Month</th>
                    </tr>
                    <tr>
                        <th width="100">Production</th>
                        <th width="100">CB</th>
                        <th width="100">Production</th>
                        <th width="100">CB</th>
                        <th width="100">Production</th>
                        <th width="100">CB</th>
                    </tr>
                    <tr>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                        <th width="100">Kg</th>
                    </tr>
                </thead>
            </table>
            <!-- style="max-height:350px; overflow-y:auto; width:1667px;" -->
            <div>
            <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
          
            <? 	$i=1;
                $aop_prev_to_qty =0;
                $aop_prev_frm_qty =0;
                $aop_prod_frm_total_qty =0;
                $aop_prod_to_total_qty =0;
                $aop_cb_frm_total_qty =0;
                $aop_cb_to_total_qty =0;
                $aop_total_diff_prod_qty =0;
                $aop_total_diff_cb_qty =0;
                foreach ($dayArr as $key => $val) 
                {
                    if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>

                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('ktr<? echo $i;?>','<? echo $bgcolor;?>')" id="ktr<? echo $i;?>">
                    
                    <td width="100" class="wrd_brk" align="center"><? echo $val;?>&nbsp;</td>
                    <td width="100" class="wrd_brk" align="right">
                    <? echo number_format($aop_prodCapacityToArr[$val]["production"],2);$aop_prev_to_qty +=$aop_prodCapacityToArr[$val]["production"];?> &nbsp;
                    </td>
                    <td width="100" class="wrd_brk" align="right">
                    <? 
                    if($aop_prodCapacityToArr[$val]["production"] >0 )
                    {
                        $aop_cb_to_qty = $aop_prev_to_qty;
                    }
                    else
                    {
                        $aop_cb_to_qty = 0;
                    }
                    
                    echo number_format($aop_cb_to_qty,2);
                    ?>&nbsp;
                    </td>
                    <td width="100" class="wrd_brk" align="right" >
                    <? echo number_format($aop_prodCapacityFromArr[$val]["production"],2); $aop_prev_frm_qty +=$aop_prodCapacityFromArr[$val]["production"]; ?>&nbsp;
                    </td>
                    <td width="100" class="wrd_brk" align="right">
                    <? 
                    if($aop_prodCapacityFromArr[$val]["production"] >0)
                    {
                        $aop_cb_from_qty = $aop_prev_frm_qty; 
                      
                    }
                    else
                    {
                        $aop_cb_from_qty = 0;
                    }
                    echo number_format($aop_cb_from_qty,2);
                    ?>&nbsp;
                   
                    </td>
                    <td width="100" class="wrd_brk" align="right">
                    <? 
                    $aop_diff_prod = $aop_prodCapacityToArr[$val]["production"]-$aop_prodCapacityFromArr[$val]["production"];
                    echo number_format($aop_diff_prod,2);?>&nbsp;
                    </td>
                    <td width="100" class="wrd_brk" align="right" >
                    <? 
                    $aop_diff_cb =$aop_cb_to_qty-$aop_cb_from_qty;
                    echo number_format($aop_diff_cb,2);?>&nbsp;
                    </td>
                    
                </tr>
                <?
                $i++;
                
                $aop_prod_to_total_qty +=$aop_prodCapacityToArr[$val]["production"];
                $aop_prod_frm_total_qty +=$aop_prodCapacityFromArr[$val]["production"];
                $aop_cb_frm_total_qty +=$aop_cb_from_qty;
                $aop_cb_to_total_qty +=$aop_cb_to_qty;
                $aop_total_diff_prod_qty +=$aop_diff_prod;
                $aop_total_diff_cb_qty +=$aop_diff_cb;
                }
                ?>
                <tfoot>
                    <tr bgcolor="#a6acaf">
                        <th width="100" >Total : </th>
                        <th width="100" align="right"><? echo number_format($aop_prod_to_total_qty,2);?>&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($aop_cb_to_total_qty,2);?>&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($aop_prod_frm_total_qty,2);?>&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($aop_cb_frm_total_qty,2);?>&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($aop_total_diff_prod_qty,2);?>&nbsp;</th>
                        <th width="100" align="right"><? echo number_format($aop_total_diff_cb_qty,2);?>&nbsp;</th>
                    </tr>
                    
                </tfoot>
            </table>
            </div>
        </div>

        <br>
        <!-- ==================== Fabric Delivery to Garments CB Report =====================  -->

        <div style="width:100%; " align="center" >
          
          <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
      
              <thead>
                 <tr> <th colspan="7">Fabric Delivery to Garments CB Report	</th></tr>
                  <tr>
                      <th width="100" rowspan="3">Day</th>
                      <th width="200" colspan="2">
                          <?  echo $months[ltrim($end_date22[1],0)].'-'.$end_date22[0]; ?>
                      </th>
                      <th width="200" colspan="2">
                          <?  echo $months[ltrim($start_date11[1],0)].'-'.$start_date11[0];?>
                      </th>
                      <th width="200" colspan="2">Difference from Last Month</th>
                  </tr>
                  <tr>
                      <th width="100">Production</th>
                      <th width="100">CB</th>
                      <th width="100">Production</th>
                      <th width="100">CB</th>
                      <th width="100">Production</th>
                      <th width="100">CB</th>
                  </tr>
                  <tr>
                      <th width="100">Kg</th>
                      <th width="100">Kg</th>
                      <th width="100">Kg</th>
                      <th width="100">Kg</th>
                      <th width="100">Kg</th>
                      <th width="100">Kg</th>
                  </tr>
              </thead>
          </table>
          <!-- style="max-height:350px; overflow-y:auto; width:1667px;" -->
          <div>
          <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
        
          <? 	$i=1;
              $fdg_prev_to_qty =0;
              $fdg_prev_frm_qty =0;
              $fdg_prod_frm_total_qty =0;
              $fdg_prod_to_total_qty =0;
              $fdg_cb_frm_total_qty =0;
              $fdg_cb_to_total_qty =0;
              $fdg_total_diff_prod_qty =0;
              $fdg_total_diff_cb_qty =0;
              foreach ($dayArr as $key => $val) 
              {
                  if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
              ?>

              <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('fdgtr<? echo $i;?>','<? echo $bgcolor;?>')" id="fdgtr<? echo $i;?>">
                  
                  <td width="100" class="wrd_brk" align="center"><? echo $val;?>&nbsp;</td>
                  <td width="100" class="wrd_brk" align="right">
                  <? echo number_format($fdg_prodCapacityToArr[$val]["production"],2);$fdg_prev_to_qty +=$fdg_prodCapacityToArr[$val]["production"];?> &nbsp;
                  </td>
                  <td width="100" class="wrd_brk" align="right">
                  <? 
                  if($fdg_prodCapacityToArr[$val]["production"] >0 )
                  {
                      $fdg_cb_to_qty = $fdg_prev_to_qty;
                  }
                  else
                  {
                      $fdg_cb_to_qty = 0;
                  }
                  
                  echo number_format($fdg_cb_to_qty,2);
                  ?>&nbsp;
                  </td>
                  <td width="100" class="wrd_brk" align="right" >
                  <? echo number_format($fdg_prodCapacityFromArr[$val]["production"],2); $fdg_prev_frm_qty +=$fdg_prodCapacityFromArr[$val]["production"]; ?>&nbsp;
                  </td>
                  <td width="100" class="wrd_brk" align="right">
                  <? 
                  if($fdg_prodCapacityFromArr[$val]["production"] >0)
                  {
                      $fdg_cb_from_qty = $fdg_prev_frm_qty; 
                    
                  }
                  else
                  {
                      $fdg_cb_from_qty = 0;
                  }
                  echo number_format($fdg_cb_from_qty,2);
                  ?>&nbsp;
                 
                  </td>
                  <td width="100" class="wrd_brk" align="right">
                  <? 
                  $fdg_diff_prod = $fdg_prodCapacityToArr[$val]["production"]-$fdg_prodCapacityFromArr[$val]["production"];
                  echo number_format($fdg_diff_prod,2);?>&nbsp;
                  </td>
                  <td width="100" class="wrd_brk" align="right" >
                  <? 
                  $fdg_diff_cb =$fdg_cb_to_qty-$fdg_cb_from_qty;
                  echo number_format($fdg_diff_cb,2);?>&nbsp;
                  </td>
                  
              </tr>
              <?
              $i++;
              
              $fdg_prod_to_total_qty +=$fdg_prodCapacityToArr[$val]["production"];
              $fdg_prod_frm_total_qty +=$fdg_prodCapacityFromArr[$val]["production"];
              $fdg_cb_frm_total_qty +=$fdg_cb_from_qty;
              $fdg_cb_to_total_qty +=$fdg_cb_to_qty;
              $fdg_total_diff_prod_qty +=$fdg_diff_prod;
              $fdg_total_diff_cb_qty +=$fdg_diff_cb;
              }
              ?>
              <tfoot>
                  <tr bgcolor="#a6acaf">
                      <th width="100" >Total : </th>
                      <th width="100" align="right"><? echo number_format($fdg_prod_to_total_qty,2);?>&nbsp;</th>
                      <th width="100" align="right"><? echo number_format($fdg_cb_to_total_qty,2);?>&nbsp;</th>
                      <th width="100" align="right"><? echo number_format($fdg_prod_frm_total_qty,2);?>&nbsp;</th>
                      <th width="100" align="right"><? echo number_format($fdg_cb_frm_total_qty,2);?>&nbsp;</th>
                      <th width="100" align="right"><? echo number_format($fdg_total_diff_prod_qty,2);?>&nbsp;</th>
                      <th width="100" align="right"><? echo number_format($fdg_total_diff_cb_qty,2);?>&nbsp;</th>
                  </tr>
                  
              </tfoot>
          </table>
          </div>
        </div>
        <br>
         <!-- ====================  Subcontract  Knitting Delivery CB Report =====================  -->

         <div style="width:100%; " align="center" >
          
          <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
      
              <thead>
                 <tr> <th colspan="7"> Subcontract  Knitting Delivery CB Report</th></tr>
                  <tr>
                      <th width="100" rowspan="3">Day</th>
                      <th width="200" colspan="2">
                          <?  echo $months[ltrim($end_date22[1],0)].'-'.$end_date22[0];?>
                      </th>
                      <th width="200" colspan="2">
                          <? echo $months[ltrim($start_date11[1],0)].'-'.$start_date11[0]; ?>
                      </th>
                      <th width="200" colspan="2">Difference from Last Month</th>
                  </tr>
                  <tr>
                      <th width="100">Production</th>
                      <th width="100">CB</th>
                      <th width="100">Production</th>
                      <th width="100">CB</th>
                      <th width="100">Production</th>
                      <th width="100">CB</th>
                  </tr>
                  <tr>
                      <th width="100">Kg</th>
                      <th width="100">Kg</th>
                      <th width="100">Kg</th>
                      <th width="100">Kg</th>
                      <th width="100">Kg</th>
                      <th width="100">Kg</th>
                  </tr>
              </thead>
          </table>
          <!-- style="max-height:350px; overflow-y:auto; width:1667px;" -->
          <div>
          <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
        
          <?  $i=1;
              $inbskd_prev_to_qty =0;
              $inbskd_prev_frm_qty =0;
              $inbskd_prod_frm_total_qty =0;
              $inbskd_prod_to_total_qty =0;
              $inbskd_cb_frm_total_qty =0;
              $inbskd_cb_to_total_qty =0;
              $inbskd_total_diff_prod_qty =0;
              $inbskd_total_diff_cb_qty =0;
              foreach ($dayArr as $key => $val) 
              {
                  if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
              ?>

              <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('inbskdtr<? echo $i;?>','<? echo $bgcolor;?>')" id="inbskdtr<? echo $i;?>">
                  
                  <td width="100" class="wrd_brk" align="center"><? echo $val;?>&nbsp;</td>
                  <td width="100" class="wrd_brk" align="right">
                  <? echo number_format($inbskd_prodCapacityToArr[$val]["production"],2);$inbskd_prev_to_qty +=$inbskd_prodCapacityToArr[$val]["production"];?> &nbsp;
                  </td>
                  <td width="100" class="wrd_brk" align="right">
                  <? 
                  if($inbskd_prodCapacityToArr[$val]["production"] >0 )
                  {
                      $inbskd_cb_to_qty = $inbskd_prev_to_qty;
                  }
                  else
                  {
                      $inbskd_cb_to_qty = 0;
                  }
                  
                  echo number_format($inbskd_cb_to_qty,2);
                  ?>&nbsp;
                  </td>
                  <td width="100" class="wrd_brk" align="right" >
                  <? echo number_format($inbskd_prodCapacityFromArr[$val]["production"],2); $inbskd_prev_frm_qty +=$inbskd_prodCapacityFromArr[$val]["production"]; ?>&nbsp;
                  </td>
                  <td width="100" class="wrd_brk" align="right">
                  <? 
                  if($inbskd_prodCapacityFromArr[$val]["production"] >0)
                  {
                      $inbskd_cb_from_qty = $inbskd_prev_frm_qty; 
                    
                  }
                  else
                  {
                      $inbskd_cb_from_qty = 0;
                  }
                  echo number_format($inbskd_cb_from_qty,2);
                  ?>&nbsp;
                 
                  </td>
                  <td width="100" class="wrd_brk" align="right">
                  <? 
                  $inbskd_diff_prod = $inbskd_prodCapacityToArr[$val]["production"]-$inbskd_prodCapacityFromArr[$val]["production"];
                  echo number_format($inbskd_diff_prod,2);?>&nbsp;
                  </td>
                  <td width="100" class="wrd_brk" align="right" >
                  <? 
                  $inbskd_diff_cb =$inbskd_cb_to_qty-$inbskd_cb_from_qty;
                  echo number_format($inbskd_diff_cb,2);?>&nbsp;
                  </td>
                  
              </tr>
              <?
              $i++;
              
              $inbskd_prod_to_total_qty +=$inbskd_prodCapacityToArr[$val]["production"];
              $inbskd_prod_frm_total_qty +=$inbskd_prodCapacityFromArr[$val]["production"];
              $inbskd_cb_frm_total_qty +=$inbskd_cb_from_qty;
              $inbskd_cb_to_total_qty +=$inbskd_cb_to_qty;
              $inbskd_total_diff_prod_qty +=$inbskd_diff_prod;
              $inbskd_total_diff_cb_qty +=$inbskd_diff_cb;
              }
              ?>
              <tfoot>
                  <tr bgcolor="#a6acaf">
                      <th width="100" >Total : </th>
                      <th width="100" align="right"><? echo number_format($inbskd_prod_to_total_qty,2);?>&nbsp;</th>
                      <th width="100" align="right"><? echo number_format($inbskd_cb_to_total_qty,2);?>&nbsp;</th>
                      <th width="100" align="right"><? echo number_format($inbskd_prod_frm_total_qty,2);?>&nbsp;</th>
                      <th width="100" align="right"><? echo number_format($inbskd_cb_frm_total_qty,2);?>&nbsp;</th>
                      <th width="100" align="right"><? echo number_format($inbskd_total_diff_prod_qty,2);?>&nbsp;</th>
                      <th width="100" align="right"><? echo number_format($inbskd_total_diff_cb_qty,2);?>&nbsp;</th>
                  </tr>
                  
              </tfoot>
          </table>
          </div>
        </div>

        <br>
         <!-- ====================  SubCon Dye And Finishing Delivery =====================  -->

         <div style="width:100%; " align="center" >
          
          <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
      
              <thead>
                 <tr> <th colspan="7"> SubCon Dye And Finishing Delivery CB Report </th></tr>
                  <tr>
                      <th width="100" rowspan="3">Day</th>
                      <th width="200" colspan="2">
                          <? echo $months[ltrim($end_date22[1],0)].'-'.$end_date22[0];?>
                      </th>
                      <th width="200" colspan="2">
                          <? echo $months[ltrim($start_date11[1],0)].'-'.$start_date11[0];?>
                      </th>
                      <th width="200" colspan="2">Difference from Last Month</th>
                  </tr>
                  <tr>
                      <th width="100">Production</th>
                      <th width="100">CB</th>
                      <th width="100">Production</th>
                      <th width="100">CB</th>
                      <th width="100">Production</th>
                      <th width="100">CB</th>
                  </tr>
                  <tr>
                      <th width="100">Kg</th>
                      <th width="100">Kg</th>
                      <th width="100">Kg</th>
                      <th width="100">Kg</th>
                      <th width="100">Kg</th>
                      <th width="100">Kg</th>
                  </tr>
              </thead>
          </table>
          <!-- style="max-height:350px; overflow-y:auto; width:1667px;" -->
          <div>
          <table width="700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
        
          <?  $i=1;
              $inbsfd_prev_to_qty =0;
              $inbsfd_prev_frm_qty =0;
              $inbsfd_prod_frm_total_qty =0;
              $inbsfd_prod_to_total_qty =0;
              $inbsfd_cb_frm_total_qty =0;
              $inbsfd_cb_to_total_qty =0;
              $inbsfd_total_diff_prod_qty =0;
              $inbsfd_total_diff_cb_qty =0;
              foreach ($dayArr as $key => $val) 
              {
                  if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
              ?>

              <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('inbskdtr<? echo $i;?>','<? echo $bgcolor;?>')" id="inbskdtr<? echo $i;?>">
                  
                  <td width="100" class="wrd_brk" align="center"><? echo $val;?>&nbsp;</td>
                  <td width="100" class="wrd_brk" align="right">
                  <? echo number_format($inbsfd_prodCapacityToArr[$val]["production"],2);$inbsfd_prev_to_qty +=$inbsfd_prodCapacityToArr[$val]["production"];?> &nbsp;
                  </td>
                  <td width="100" class="wrd_brk" align="right">
                  <? 
                  if($inbsfd_prodCapacityToArr[$val]["production"] >0 )
                  {
                      $inbsfd_cb_to_qty = $inbsfd_prev_to_qty;
                  }
                  else
                  {
                      $inbsfd_cb_to_qty = 0;
                  }
                  
                  echo number_format($inbsfd_cb_to_qty,2);
                  ?>&nbsp;
                  </td>
                  <td width="100" class="wrd_brk" align="right" >
                  <? echo number_format($inbsfd_prodCapacityFromArr[$val]["production"],2); $inbsfd_prev_frm_qty +=$inbsfd_prodCapacityFromArr[$val]["production"]; ?>&nbsp;
                  </td>
                  <td width="100" class="wrd_brk" align="right">
                  <? 
                  if($inbsfd_prodCapacityFromArr[$val]["production"] >0)
                  {
                      $inbsfd_cb_from_qty = $inbsfd_prev_frm_qty; 
                    
                  }
                  else
                  {
                      $inbsfd_cb_from_qty = 0;
                  }
                  echo number_format($inbsfd_cb_from_qty,2);
                  ?>&nbsp;
                 
                  </td>
                  <td width="100" class="wrd_brk" align="right">
                  <? 
                  $inbsfd_diff_prod = $inbsfd_prodCapacityToArr[$val]["production"]-$inbsfd_prodCapacityFromArr[$val]["production"];
                  echo number_format($inbsfd_diff_prod,2);?>&nbsp;
                  </td>
                  <td width="100" class="wrd_brk" align="right" >
                  <? 
                  $inbsfd_diff_cb =$inbsfd_cb_to_qty-$inbsfd_cb_from_qty;
                  echo number_format($inbsfd_diff_cb,2);?>&nbsp;
                  </td>
                  
              </tr>
              <?
              $i++;
              
              $inbsfd_prod_to_total_qty +=$inbsfd_prodCapacityToArr[$val]["production"];
              $inbsfd_prod_frm_total_qty +=$inbsfd_prodCapacityFromArr[$val]["production"];
              $inbsfd_cb_frm_total_qty +=$inbsfd_cb_from_qty;
              $inbsfd_cb_to_total_qty +=$inbsfd_cb_to_qty;
              $inbsfd_total_diff_prod_qty +=$inbsfd_diff_prod;
              $inbsfd_total_diff_cb_qty +=$inbsfd_diff_cb;
              }
              ?>
              <tfoot>
                  <tr bgcolor="#a6acaf">
                      <th width="100" >Total : </th>
                      <th width="100" align="right"><? echo number_format($inbsfd_prod_to_total_qty,2);?>&nbsp;</th>
                      <th width="100" align="right"><? echo number_format($inbsfd_cb_to_total_qty,2);?>&nbsp;</th>
                      <th width="100" align="right"><? echo number_format($inbsfd_prod_frm_total_qty,2);?>&nbsp;</th>
                      <th width="100" align="right"><? echo number_format($inbsfd_cb_frm_total_qty,2);?>&nbsp;</th>
                      <th width="100" align="right"><? echo number_format($inbsfd_total_diff_prod_qty,2);?>&nbsp;</th>
                      <th width="100" align="right"><? echo number_format($inbsfd_total_diff_cb_qty,2);?>&nbsp;</th>
                  </tr>
                  
              </tfoot>
          </table>
          </div>
        </div>

    </fieldset>

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