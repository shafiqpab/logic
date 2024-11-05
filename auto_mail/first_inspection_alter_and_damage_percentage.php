<?
require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
echo load_html_head_contents("Graph", "../", "", $popup, $unicode, $multi_select, 1);


$sweater_reject_type=array( 1=>"Needle Drop", 2=>"Double Line", 3=>"Puckering Yarn", 4=>"Side Needle Drop", 5=>"Color Shading", 6=>"Wrong Measurement", 7=>"Defective Needle", 8=>"Tention Tight Loose", 9=>"Starting c/s", 10=>"Wrong Needle", 11=>"Dirty Fashion", 12=>"Yarn Thin & Thick", 13=>"Nylon Visible");
//--------------------------------------------------------------------------------------------------------------------
$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),0))),'','',1);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 
 
	

$company_library =return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	
foreach($company_library as $company=>$company_name){
    $sql_cond=" and a.company_id='$company' ";
    $sql_cond.=" and a.cutting_qc_date between '$prev_date' and '$prev_date'";
    
    
    $data_sql="SELECT a.id, a.job_no as JOB_NO, b.id as DTLS_ID, b.mst_id as MST_ID, b.production_qnty as PRODUCTION_QNTY, b.reject_qty as REJECT_QTY, b.barcode_no as BARCODE_NO, b.defect_qty as DEFECT_QTY from pro_gmts_cutting_qc_mst a, pro_garments_production_dtls b where a.id=b.delivery_mst_id and b.production_type=52 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond group by a.id, a.job_no, b.id, b.mst_id, b.production_qnty, b.reject_qty, b.barcode_no, b.defect_qty order by b.id asc";
      //echo $data_sql;
    $data_result=sql_select($data_sql);
    $gmt_mst_arr=array();$job_arr=array();$chk_arr=array();$chk_arr1=array();
    $total_bundle_qty=$total_production_qnty=$total_defect_qty=$total_reject_qty=$total_replace_qty=0;
    foreach($data_result as $row )
    {
        $gmt_mst_arr[$row['MST_ID']]=$row['MST_ID'];
        $job_arr[$row['JOB_NO']]="'".$row['JOB_NO']."'";

       
        if(!in_array($row['BARCODE_NO'],$chk_arr))
        {
          $chk_arr[]=$row['BARCODE_NO'];
          $total_production_qnty+=$row['PRODUCTION_QNTY'];
          $total_defect_qty+=$row['DEFECT_QTY'];
          $total_reject_qty+=$row['REJECT_QTY'];
          $total_replace_qty+=$row['REPLACE_QTY'];
          $total_bundle_qty+=$row['PRODUCTION_QNTY']+$row['DEFECT_QTY']+$row['REJECT_QTY'];
        }
    }

    $style_count=sql_select("SELECT count(style_ref_no) as STYLE_NO from wo_po_details_master where job_no in (".implode(',', $job_arr).")");

    $sql_defect="SELECT defect_point_id, sum(defect_qty) as DEFECT_QTY from pro_gmts_prod_dft where status_active=1 and is_deleted=0 and mst_id in (".implode(',', $gmt_mst_arr).") and production_type=52 and defect_type_id in(3,4) and status_active=1 and is_deleted=0 group by defect_point_id";
    $sql_defect_result=sql_select($sql_defect);
    $defect_data=array();
    foreach ($sql_defect_result as  $value) 
    {
        $defect_data[$value[csf('defect_point_id')]]+=$value['DEFECT_QTY'];  
    }
	ob_start();
    ?>	

    <div style="margin:10px 0 0 10px; width:99%; text-align:center">
    	<h2><? echo $company_library[$company];?></h2>
    	<h3>Dashboard (Alter%  &  Damage%)</h3>
    	<h3>Section: 1st Inspection</h3>
    	<h3>For The Date Of <? echo change_date_format($prev_date);?></h3>
    </div>
	
     <table width="400" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <tr>
                <th width="30">Sl</th>
                <th width="180">Perameters</th>
                <th width="220" colspan='2'>Particulars</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Total Style No.</td>
                <td colspan='2' style='text-align:center'><? echo $style_count[0]['STYLE_NO'];?></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Total QC Qty [Pcs]</td>
                <td colspan='2' style='text-align:center'><? echo $total_bundle_qty;?></td>
            </tr>
            <tr style="background-color:#8DAFDA;">
                <td style='text-align:center'><strong>Sl</strong></td>
                <td style='text-align:center'><strong>Particulars</strong></td>
                <td style='text-align:center'><strong>Qty [Pcs]</strong></td>
                <td style='text-align:center'><strong>Perc. %</strong></td>
            </tr>
            <tr>
                <td>3</td>
                <td>Total QC Pass Qty</td>
                <td style='text-align:right;'><? echo $total_production_qnty;?></td>
                <td style='text-align:right;'><?
                    echo number_format(($total_production_qnty/$total_bundle_qty)*100,2);
                    $total_production_qnty_prcnt=number_format(($total_production_qnty/$total_bundle_qty)*100,2);
                ?></td>
            </tr>
            <tr>
                <td>4</td>
                <td>Total Alter Qty</td>
                <td style='text-align:right;'><? echo $total_defect_qty;?></td>
                <td style='text-align:right;'><?
                    echo number_format(($total_defect_qty/$total_bundle_qty)*100,2);
                    $total_defect_qty_prcnt=number_format(($total_defect_qty/$total_bundle_qty)*100,2);
                ?></td>
            </tr>
            <tr>
                <td>5</td>
                <td>Total Damage Qty</td>
                <td style='text-align:right;'><? echo $total_reject_qty;?></td>
                <td style='text-align:right;'><?
                    echo number_format(($total_reject_qty/$total_bundle_qty)*100,2);
                    $total_reject_qty_prcnt=number_format(($total_reject_qty/$total_bundle_qty)*100,2);
                ?></td>
            </tr>
        </tbody>
    </table>
     <table width="1300" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <tr><th colspan='<? echo count($sweater_reject_type)+1;?>'>Type Of Defects</th></tr>
            <tr>
                <th>Total No. of Defect</th>
                <?
                foreach($sweater_reject_type as $val)
                {
                  ?>
                      <th><? echo $val;?></th>
                  <?
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style='text-align:center'><?
                    $total_defect_qty_result=$total_defect_qty+$total_reject_qty;
                    echo $total_defect_qty_result;
                    ?></td>
                <?
                foreach($sweater_reject_type as $key=>$val)
                {
                  ?>
                      <td style='text-align:center'><? echo $defect_data[$key];?></td>
                  <?
                }
                ?>
            </tr>
            <tr>
                <td></td>
                <?
                foreach($sweater_reject_type as $key=>$val)
                {
                  $defect_val[$key]=fn_number_format($defect_data[$key]/$total_defect_qty_result,2);
                  ?>
                      <td style='text-align:center'><strong><? echo fn_number_format($defect_data[$key]/$total_defect_qty_result,2)." %";?></strong></td>
                  <?
                }
                ?>
            </tr>
        </tbody>
    </table>
	<?
	
	
    $emailBody=ob_get_contents();
    ob_clean();
    $to='';
    $sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=79 and b.mail_user_setup_id=c.id and a.company_id=$company AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
    $mail_sql=sql_select($sql);
    $mailArr=array();
    foreach($mail_sql as $row)
    {
        $mailArr[$row[csf('email_address')]]=$row[csf('email_address')]; 
    }
    $to=implode(',',$mailArr);
    $subject="Order List Without Fabric Booking";

    if($_REQUEST['isview']==1){
        echo $emailBody;
    }
    else{
        if($to!="")echo sendMailMailer( $to, $subject, $emailBody );
    }
	
	
	
}

?>
<!-- sendMailMailer( $to, $subject, $emailBody ); -->


