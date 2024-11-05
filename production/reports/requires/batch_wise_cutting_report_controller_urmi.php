<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
    exit();	 
}

if ($action=="generate_report") 
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
    // ==============Searching Data Rcv=============================
    $report_type        =   str_replace("'","",$type);
    $txt_cut_no         =   str_replace("'","",$txt_cut_no);
    $report_title       =   str_replace("'","",$report_title);
    $txt_date_to        =   str_replace("'","",$txt_date_to);
    $txt_date_from      =   str_replace("'","",$txt_date_from);
    $cbo_buyer_name     =   str_replace("'","",$cbo_buyer_name);
    $company            =   str_replace("'","",$cbo_wo_company_name);
    $cbo_cutting_year   =   str_replace("'","",$cbo_cutting_year);
    $batch_no           =   str_replace("'","",$txt_batch_no);

    // ==================Common Array Create===========================
    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
    $buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

    //  **************************************************************
    $sql_cond="";
    if(str_replace("'","",$company)=="") $sql_cond=""; else $sql_cond=" AND A.COMPANY_ID=".str_replace("'","",$company)."";
    if(str_replace("'","",$txt_cut_no)=="") $sql_cond.=""; else $sql_cond.=" AND A.CUTTING_NO like '%".str_replace("'","",$txt_cut_no)."'";
    if(str_replace("'","",$cbo_buyer_name)==0) $sql_cond.=""; else $sql_cond.=" AND D.BUYER_NAME='".str_replace("'","",$cbo_buyer_name)."'";
    if( $txt_date_from!="" && $txt_date_to!="" )
	{
		if($db_type==0)
		{
			$sql_date= " AND A.ENTRY_DATE  BETWEEN'".change_date_format($txt_date_from,'yyyy-mm-dd')."' AND '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_date= " AND A.ENTRY_DATE  BETWEEN '".change_date_format($txt_date_from,'yyyy-mm-dd','-',1)."' AND '".change_date_format($txt_date_to,'yyyy-mm-dd','-',1)."'";
		}
	}
    if($cbo_cutting_year>0)
	{
		if($db_type==0)
		{
			if($txt_job_no !=""){
                $sql_cond.=" and year(a.insert_date)='$cbo_cutting_year'";
			}
		}
		else
		{
			if($txt_job_no !=""){
                $sql_cond.=" and to_char(a.insert_date,'YYYY')='$cbo_cutting_year'";
			}
		}	
	}

    if($batch_no!="")
    {
        $roll_id_arr=return_library_array( "SELECT b.id,b.id as roll_id from PRO_BATCH_CREATE_MST a,PRO_ROLL_DETAILS b where a.id=b.is_extra_roll and a.batch_no='$batch_no' and b.entry_form=99 and b.status_active=1", "id", "roll_id");
        if(count($roll_id_arr)>0)
        {
            $roll_id_cond = where_con_using_array($roll_id_arr,0,"b.roll_id");
        }
    }
    // ======================Search Data and mst Query ==========================
    $sql_cut="SELECT A.ID,A.CUTTING_NO, A.JOB_NO, C.BATCH_ID, A.ENTRY_DATE,B.BUNDLE_NO,C.MARKER_QTY, C.ORDER_CUT_NO,C.ROLL_DATA,D.BUYER_NAME,D.JOB_NO,MIN(E.PUB_SHIPMENT_DATE) AS FIRST_SHIP_DATE, MAX(E.PUB_SHIPMENT_DATE) AS LAST_SHIP_DATE
    FROM PPL_CUT_LAY_MST A,PPL_CUT_LAY_BUNDLE B,PPL_CUT_LAY_DTLS C ,WO_PO_DETAILS_MASTER D,WO_PO_BREAK_DOWN E
    WHERE C.MST_ID=A.ID AND B.MST_ID=A.ID AND A.JOB_NO=D.JOB_NO AND D.ID=E.JOB_ID AND A.ENTRY_FORM=99 
    AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 
    AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 
    AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0
    AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0
    AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0
    $sql_cond $sql_date $roll_id_cond GROUP BY A.ID,A.CUTTING_NO, A.JOB_NO, C.BATCH_ID, A.ENTRY_DATE,B.BUNDLE_NO,C.MARKER_QTY, C.ORDER_CUT_NO,C.ROLL_DATA,D.BUYER_NAME,D.JOB_NO ORDER BY A.ID DESC";
    // echo $sql_cut."<br>";die; 

    $result=sql_select($sql_cut);

    if(count($result)==0)
    {
        ?>
        <div style="text-align: center;color:red;font-size:18px;">Data Not Fount.</div>
        <?
        die;
    }
    $cutting_data_arr=array();
    $batch_id_arr = array();
    foreach ($result as $val) 
    {
        $rolls = explode("**", $val["ROLL_DATA"]); //Roll Data 6 index Booking NUmber
        $batch = "";
        foreach ($rolls as $vals) 
        {
            $roll_data = explode("=", $vals);
            $batch = $roll_data[6];
            if($batch!="")
            {
                $cutting_data_arr[$val["CUTTING_NO"]][$batch]["bundle"]=$val["BUNDLE_NO"];
                $cutting_data_arr[$val["CUTTING_NO"]][$batch]["job"]=$val["JOB_NO"];
                $cutting_data_arr[$val["CUTTING_NO"]][$batch]["buyer"]=$val["BUYER_NAME"];
                $cutting_data_arr[$val["CUTTING_NO"]][$batch]["cut_qty"]=$val["MARKER_QTY"];
                $cutting_data_arr[$val["CUTTING_NO"]][$batch]["cut_date"]=$val["ENTRY_DATE"];
                $cutting_data_arr[$val["CUTTING_NO"]][$batch]["f_ship_date"]=$val["FIRST_SHIP_DATE"];
                $cutting_data_arr[$val["CUTTING_NO"]][$batch]["l_ship_date"]=$val["LAST_SHIP_DATE"];
                $batch_id_arr[$batch] = $batch;
                $cut_no_arr[$val["CUTTING_NO"]] = $val["CUTTING_NO"];
                $job_no_arr[$val["JOB_NO"]] = $val["JOB_NO"];
            }            
        } 
    }
    // ***************************************************************
    // ====================For Booking Number=======================
    $batch_id_cond = where_con_using_array($batch_id_arr,1,"A.ID");
    $batch_id_cond2 = where_con_using_array($batch_id_arr,1,"A.BATCH_ID");
    $cut_no_cond = where_con_using_array($cut_no_arr,1,"B.CUT_NO");
    $finish_batch_id_cond = where_con_using_array($batch_id_arr,1,"B.BATCH_ID");

    //$booking_no_arr = return_library_array("SELECT ID,BOOKING_NO FROM PRO_BATCH_CREATE_MST WHERE STATUS_ACTIVE=1 AND IS_DELETED=0 $batch_id_cond","ID","BOOKING_NO");
    $sql_booking="SELECT A.ID,a.batch_no,A.BATCH_DATE,A.BOOKING_NO,B.BATCH_QNTY FROM PRO_BATCH_CREATE_MST A, PRO_BATCH_CREATE_DTLS B WHERE B.MST_ID=A.ID AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 
    AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  $batch_id_cond";
    //echo $sql_booking;
    $result_booking=sql_select($sql_booking);
    $booking_no_arr=array();
    $batch_no_arr=array();
    foreach ($result_booking as $val) 
    {
        $batch_no_arr[$val["ID"]]=$val["BATCH_NO"];
        $booking_no_arr[$val["ID"]]["booking"]=$val["BOOKING_NO"];
        $booking_no_arr[$val["ID"]]["batch_date"]=$val["BATCH_DATE"];
        $booking_no_arr[$val["ID"]]["batch_qnty"]+=$val["BATCH_QNTY"];
    }
    /* ============================================================================================/
    /                                       Grey Production                                        /
    /============================================================================================ */    
    $batchIdCond = where_con_using_array($batch_id_arr,1,"c.mst_id");
    $sql = "SELECT c.mst_id as batch_id,d.extention_no, min(receive_date) as first_rcv_date, max(receive_date) as last_rcv_date from INV_RECEIVE_MASTER a, PRO_ROLL_DETAILS b, PRO_BATCH_CREATE_DTLS c, PRO_BATCH_CREATE_MST d where a.id=b.mst_id and b.id=c.roll_id and d.id=c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 $batchIdCond group by c.mst_id,d.extention_no";
    // echo $sql;die;
    $res = sql_select($sql);
    $grey_prod_data_arr = array();
    foreach ($res as $val) 
    {
        $grey_prod_data_arr[$val['BATCH_ID']]['first_date'] = $val['FIRST_RCV_DATE'];
        $grey_prod_data_arr[$val['BATCH_ID']]['last_date'] = $val['LAST_RCV_DATE'];
        $grey_prod_data_arr[$val['BATCH_ID']]['extention_no'] = $val['EXTENTION_NO'];
    }
    // print_r($grey_prod_data_arr);



    // **************************************************************
    //======================Dyeing==================================
    $sql_dyeing="SELECT A.BATCH_NO,A.BATCH_ID,A.PROCESS_END_DATE,B.PRODUCTION_QTY FROM PRO_FAB_SUBPROCESS A,PRO_FAB_SUBPROCESS_DTLS B  WHERE  A.ID=B.MST_ID $batch_id_cond2  AND A.LOAD_UNLOAD_ID=2 AND A.SHADE_MATCHED=1 AND A.ENTRY_FORM=35 AND A.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.STATUS_ACTIVE=1 ";
    //echo $sql_dyeing;
    $result_dyeing=sql_select($sql_dyeing);
    $dyeing_data_arr=array();
    foreach ($result_dyeing as $val) 
    {
        $dyeing_data_arr[$val["BATCH_ID"]]["dy_date"]=$val["PROCESS_END_DATE"];
        $dyeing_data_arr[$val["BATCH_ID"]]["dy_qty"]+=$val["PRODUCTION_QTY"];
        // $batch_no_arr[$val["BATCH_NO"]] = $val["BATCH_NO"];
    }
        
    //**************************************************************
    // ======================Delivery=================================
    $sql_delivery="SELECT MIN(A.DELEVERY_DATE) AS F_DELIVERY_DATE, MAX(A.DELEVERY_DATE) AS L_DELIVERY_DATE, B.GREY_USED_QNTY,B.BATCH_ID from PRO_GREY_PROD_DELIVERY_MST A,PRO_GREY_PROD_DELIVERY_DTLS B
    WHERE A.ID=B.MST_ID $finish_batch_id_cond AND B.ENTRY_FORM=54 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 GROUP BY B.GREY_USED_QNTY,B.BATCH_ID";
    //echo "Finish Fabric<br>".$sql_delivery;
    $result_finish=sql_select($sql_delivery);
    $delivery_data_arr=array();
    foreach ($result_finish as $val) 
    {
        $delivery_data_arr[$val["BATCH_ID"]]["F_DELIVERY_DATE"]=$val["F_DELIVERY_DATE"];
        $delivery_data_arr[$val["BATCH_ID"]]["L_DELIVERY_DATE"]=$val["L_DELIVERY_DATE"];
        $delivery_data_arr[$val["BATCH_ID"]]["DELIVERY_QTY"]+=$val["GREY_USED_QNTY"];
    }
    // echo "<pre>";
    // print_r($delivery_data_arr);
    // echo "</pre>";
    // *************************************************************

    // =========================
    $sql_finish="SELECT MIN(A.TRANSACTION_DATE) AS F_FINISH_DATE, MAX(A.TRANSACTION_DATE) AS L_FINISH_DATE, B.RECEIVE_QNTY,B.BATCH_ID 
    FROM INV_TRANSACTION A, PRO_FINISH_FABRIC_RCV_DTLS B
    WHERE A.ID=B.MST_ID $finish_batch_id_cond
    AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
    GROUP BY  B.RECEIVE_QNTY,B.BATCH_ID";
    //echo $sql_finish;
    $result_finish=sql_select($sql_finish);
    $finish_data_arr=array();
    foreach ($result_finish as $val) 
    {
        $finish_data_arr[$val["BATCH_ID"]]["F_FINISH_DATE"]=$val["F_FINISH_DATE"];
        $finish_data_arr[$val["BATCH_ID"]]["L_FINISH_DATE"]=$val["L_FINISH_DATE"];
        $finish_data_arr[$val["BATCH_ID"]]["FINISH_QTY"]+=$val["RECEIVE_QNTY"];
    }
    // echo "<pre>";
    // print_r($finish_data_arr);
    // echo "</pre>";

    // *************************

    //=======================Bundle Wise Sewing Input===============
    $sql_swing="SELECT A.CUT_NO,sum(B.PRODUCTION_QNTY) as PRODUCTION_QNTY,max(a.production_date) as last_input_date,min(a.production_date) as first_input_date,B.ENTRY_FORM 
    FROM PRO_GARMENTS_PRODUCTION_MST A,PRO_GARMENTS_PRODUCTION_DTLS B 
    WHERE A.ID=B.MST_ID $cut_no_cond AND B.PRODUCTION_TYPE in (4,5) AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 
    GROUP By A.CUT_NO,B.ENTRY_FORM ";
    //echo $sql_swing;
    $result_swing=sql_select($sql_swing);
    $swing_data_arr=array();
    $input_data_ar=array();
    foreach ($result_swing as $val) 
    {
        $swing_data_arr[$val["CUT_NO"]]["qty"]+=$val["PRODUCTION_QNTY"];
        $swing_data_arr[$val["CUT_NO"]]["first_input_date"]=$val["FIRST_INPUT_DATE"];
        $swing_data_arr[$val["CUT_NO"]]["last_input_date"]=$val["LAST_INPUT_DATE"];

        $input_data_ar[$val["CUT_NO"]][$val["ENTRY_FORM"]]["qty"]=$val["PRODUCTION_QNTY"];
        $input_data_ar[$val["CUT_NO"]][$val["ENTRY_FORM"]]["first_input_date"]=$val["FIRST_INPUT_DATE"];
        $input_data_ar[$val["CUT_NO"]][$val["ENTRY_FORM"]]["last_input_date"]=$val["LAST_INPUT_DATE"];
    }
    // echo "<pre>";
    // print_r($swing_data_arr);
    // echo "</pre>";
    ob_start();
    if($report_type==1) //Show Button 
    {
        //    echo $company."**".$cbo_buyer_name."**".$txt_cut_no."**".$txt_date_from."**".$txt_date_to;
        ?>
        <fieldset style="width: 2920px;margin: 0 auto;">
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 10px;">
				<h2><?=$report_title;?></h2>
				<h2>Company : <?=$company_library[$company]; ?></h2>
				<h2>Date : <?=change_date_format($txt_date_from); ?>To<?=change_date_format($txt_date_to); ?> </h2>
			</div>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="2900"  align="center">
	             	<thead>
	             		<tr>
	             			<th width="100">Booking No. </th>
                            <th width="100">Job No.</th>
                            <th width="100">Buyer</th>
                            <th width="120">1st Grey Production</th>
                            <th width="150">Last Gray Production</th>
                            <th width="100">Batch</th>
                            <th width="100">Ext. No.</th>
                            <th width="100">Batch Date</th>
                            <th width="100">Batch Qty.</th>
                            <th width="100">Dyeing Date</th>
                            <th width="100">Dyeing Qty.</th>
                            <th width="100">1st Finishing Date</th>
                            <th width="100">Last Finishing Date</th>
                            <th width="100">Finishing Qty.</th>
                            <th width="100">1st Delivery Date</th>
                            <th width="100">Last Delivery Date</th>
                            <th width="100">Delivery Qty.</th>
                            <th width="100">Cutting No.</th>
                            <th width="100">Cutting Date</th>
                            <th width="100">Cutting Qty.</th>
                            <th width="100">1st Input Date</th>
                            <th width="100">Last Input Date</th>
                            <th width="100">Input Qty</th>
                            <th width="100">1st Sewing Output Date</th>
                            <th width="100">Last Sewing Output Date </th>
                            <th width="100">Sewing Qty</th>
                            <th width="100">1st Ship Date</th>
                            <th width="100">Last Ship Date</th>
	             		</tr>
	             	</thead>
	            </table>
	            <div style=" width:2920px;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="2900"  align="center" id="table_body_1">
		             	<tbody>
                            <?
                                $i=1;
                                $total_cut_qc=0;
                                $total_booking_qty=0; 
                                $total_dyeing_qty=0; 
                                $total_swing_qty=0; 
                                $total_delivery_qty=0;
                                $total_finish_qty=0;
                                $total_input_qty=0;
                                foreach ($cutting_data_arr as $cut_key => $batch_data) 
                                {
                                    foreach ($batch_data as $batch_key => $row) 
                                    {
                                        if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                                        ?>
                                            <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">

                                                <td width="100" style="text-align:left;"><?=$booking_no_arr[$batch_key]["booking"];?></td>
                                                <td width="100" style="text-align:left;" ><?=$row["job"];?></td>
                                                <td width="100" style="text-align:left;"><?=$buyer_arr[$row["buyer"]];?></td>
                                                <td width="120" style="text-align:center;"><?=change_date_format($grey_prod_data_arr[$batch_key]['first_date']);?></td>
                                                <td width="150" style="text-align:center;"><?=change_date_format($grey_prod_data_arr[$batch_key]['last_date']);?></td>
                                                <td width="100" style="text-align:left;"><?=$batch_no_arr[$batch_key];?></td>
                                                <td width="100" style="text-align:center;"><?=$grey_prod_data_arr[$batch_key]['extention_no'];?></td>
                                                <td width="100" style="text-align:center;"><?=$booking_no_arr[$batch_key]["batch_date"];?></td>
                                                <td width="100" style="text-align:right;"><?=number_format($booking_no_arr[$batch_key]["batch_qnty"],2);?></td>
                                                <td width="100" style="text-align:center;"><?=$dyeing_data_arr[$batch_key]["dy_date"];?></td>
                                                <td width="100" style="text-align:right;"><?=number_format($dyeing_data_arr[$batch_key]["dy_qty"],2);?></td>
                                                <td width="100" style="text-align:center;"><?=$finish_data_arr[$batch_key]["F_FINISH_DATE"];?></td>
                                                <td width="100" style="text-align:center;"><?=$finish_data_arr[$batch_key]["L_FINISH_DATE"];?></td>
                                                <td width="100" style="text-align:right;"><?=number_format($finish_qty=$finish_data_arr[$batch_key]["FINISH_QTY"],2);?></td>
                                                <td width="100" style="text-align:center;"><?=$delivery_data_arr[$batch_key]["F_DELIVERY_DATE"]?></td>
                                                <td width="100" style="text-align:center;"><?=$delivery_data_arr[$batch_key]["L_DELIVERY_DATE"]?></td>
                                                <td width="100" style="text-align:right;"><?=number_format($delivery_qty=$delivery_data_arr[$batch_key]["DELIVERY_QTY"],2);?></td>
                                                <td width="100" style="text-align:center;"><?=$cut_key;?></td>
                                                <td width="100" style="text-align:center;"><?=$row["cut_date"];?></td>
                                                <td width="100" style="text-align:right;"><?=$row["cut_qty"];?></td>
                                                <td width="100" style="text-align:center;"><?=$input_data_ar[$cut_key][96]["first_input_date"]?></td>
                                                <td width="100" style="text-align:center;"><?=$input_data_ar[$cut_key][96]["last_input_date"];?></td>
                                                <td width="100" style="text-align:right;">
                                                    <?=$input_qty=$input_data_ar[$cut_key][96]["qty"];?>
                                                </td>
                                                <td width="100" style="text-align:center;"><?=$swing_data_arr[$cut_key]["first_input_date"];?></td>
                                                <td width="100" style="text-align:center;"><?=$swing_data_arr[$cut_key]["last_input_date"];?></td>
                                                <td width="100" style="text-align:right;"><?=$swing_data_arr[$cut_key]["qty"];?></td>
                                                <td width="100" style="text-align:center;"><?=$row["f_ship_date"];?></td>
                                                <td width="100" style="text-align:center;"><?=$row["l_ship_date"];?></td>
                                            </tr>
                                        <?
                                        $i++;
                                        $total_cut_qc+=$row["cut_qty"];
                                        $total_delivery_qty+=$delivery_qty;
                                        $total_finish_qty+=$finish_qty;
                                        $total_booking_qty+=$booking_no_arr[$batch_key]["batch_qnty"];
                                        $total_dyeing_qty+=$dyeing_data_arr[$batch_key]["dy_qty"];
                                        $total_input_qty+=$input_qty;
                                        $total_swing_qty+=$swing_data_arr[$cut_key]["qty"];
                                    }
                                }
                            ?>	
                            </tbody>
                            <tfoot>
                            <tr>
                                <th width="870" colspan="8" ><strong>Total</strong></th>
                                <!-- Batch Qty. -->
                                <th width="100" ><strong><?=number_format($total_booking_qty,2);?></strong></th>

                                <!-- Dyeing Date || Dyeing Qty. -->
                                <th width="100">&nbsp;</th>
                                <th width="100" ><strong><?=number_format($total_dyeing_qty,2);?></td>

                                <!-- 1st Finishing Date || Last Finishing Date || Finishing Qty. -->
                                <th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="100" ><strong><?=number_format($total_finish_qty,2);?></strong></th>

                                <!-- 1st Delivery Date || Last Delivery Date || Delivery Qty. -->
                                <th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="100" ><strong><?=number_format($total_delivery_qty,2);?></strong></th>

                                <!-- Cutting No. || Cutting Date || Cutting Qty. -->
                                <th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="100" ><strong><?=number_format($total_cut_qc,0);?></strong></th>

                                <!-- 1st Input Date || Last Input Date || Input Qty -->
                                <th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="100" ><strong><?=number_format($total_input_qty,0);?></strong></th>

                                <!-- 1st Sewing Date || Last Sewing Date || Sweing Qty -->
                                <th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="100" ><strong><?=number_format($total_swing_qty,0);?></strong></th>

                                <!-- 1st Ship Date || Last Ship Date -->
                                <th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                            </tr>
						</tbody>
					</table>
				</div>
			</div>
			
		</fieldset>
        <?
    }
    $html = ob_get_contents();
    ob_clean();
    foreach (glob($user_name."_*.xls") as $filename) {
        @unlink($filename);
    }
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename####$report_type";
    exit();
}


