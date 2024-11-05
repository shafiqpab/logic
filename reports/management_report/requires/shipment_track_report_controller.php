<?php
//--------------------------------------------------------------------------------------------------------------------
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header('location:login.php');
include('../../../includes/common.php'); 
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_level=$_SESSION['logic_erp']["user_level"];
$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
$item_arr=return_library_array( "select id,item_name from lib_garment_item", "id", "item_name"  );
$user_arr=return_library_array( "select id,user_name from user_passwd", "id", "user_name"  ); 
$unit_lib=$unit_of_measurement; 
// pre($unit_lib); die;
if($action=="load_drop_down_buyer")
{
	if($data!=0)
	{
		echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 0, "-- Select Buyer --", $selected, "" );
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 140, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 0, "-- Select Buyer --", $selected, "" );
	}
	exit();
}
if ($action == "load_drop_down_location") {
	echo create_drop_down("cbo_location_id", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in ($data) order by location_name", "id,location_name", 1, "--Select Location--", $selected, "", 0);

}
if ($action == "load_drop_down_work_location") {
	echo create_drop_down("cbo_work_location_id", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in ($data) order by location_name", "id,location_name", 1, "--Select Location--", $selected, "", 0);

}
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	list($company_name,$buyer_name)=explode('_',$data);

	?>
    	<script>
		var type = <?php echo $type; ?>;

		function js_set_value(values) {			
			var jobStr=values.split("_");
			document.getElementById('job_no_id').value = jobStr[0];
			document.getElementById('job_no_val').value = jobStr[1];
           parent.emailwindow.hide();
		}

		/**
		 * change search by title after the popup is loaded
		 */
		window.addEventListener('load', function() {
		    if(type == 1) {
				document.getElementById('search_by_td_up').innerHTML = 'Please Enter Job No';
			}
            if(type == 2) {
				document.getElementById('search_by_td_up').innerHTML = 'Please Enter Style';
			}
            if(type == 3) {
				document.getElementById('search_by_td_up').innerHTML = 'Please Enter Internal Ref';
			}
		})
	
    </script>
        <input type="hidden" id="job_no_id" />
        <input type="hidden" id="job_no_val" />
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:650px;">
            <table width="630px" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="150">Please Enter here</th>
                    <th width="110" colspan="2" class="must_entry_caption"> Date</th>
                    <th>
                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                    </th>
                    <input type="hidden" name="job_no_id" id="job_no_id" value=""/>
                    <input type="hidden" name="job_no_val" id="job_no_val" value=""/>
                    <input type="hidden" name="style_no_val" id="style_no_val" value=""/>
                    <input type="hidden" name="int_ref_val" id="int_ref_val" value=""/>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_name $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Int ref");
                            if($type == 1){
                                $selected_index = 1;
                            }
                            if($type == 2){
                                $selected_index = 2;
                            }
                            else  $selected_index = 3;
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 120, $search_by_arr,"",0, "--Select--", $selected_index,$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:120px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                             <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" ></td>
                            <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date" ></td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year_selection').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'job_no_popup_list', 'search_div', 'shipment_track_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:90px;" />
                    	</td>
                    </tr>
                    <tfoot>
                    	<tr>
                            <td colspan="11" align="center">
                                <? echo load_month_buttons(1); ?>
                            </td>
                        </tr>
                    </tfoot>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}
if ($action=="job_no_popup_list")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	list($companyID,$buyer_name,$search_by,$search_common,$cbo_year_selection,$txt_date_from,$txt_date_to)=explode('**',$data);

    if(trim($txt_date_to)!="") $txt_date_from=$txt_date_from;
	if(trim($txt_date_to)!="") $txt_date_to=$txt_date_to;
   
	$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd','-',1);
	$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd','-',1);
    if ($txt_date_from!="" && $txt_date_to!="") $date_cond="and b.insert_date between '$txt_date_from' and '$txt_date_to'"; else $date_cond="";
    $date=date('d-m-Y');
    $year_field_con=" and to_char(b.insert_date,'YYYY')";
	if(trim($cbo_year_selection)!=0) $year_cond_2=" $year_field_con=$cbo_year_selection"; else $year_cond="";
 
 	if ($buyer_name!=0) $where_con.=" and b.buyer_name=$buyer_name";
	
	if($search_by==1 && $search_common!=''){
		$where_con.=" and b.job_no like('%".$search_common."%')";
	}
	else if($search_by==2 && $search_common!='')
	{
		$where_con.=" and a.style_ref_no like('%".$search_common."%')";
	}
	else if($search_by==3 && $search_common!='')
	{
		$where_con.=" and a.grouping like('%".$search_common."%')";
	}
	if($search_by==1 || $search_by==2)
    {
         $str_data_cond="id,job_no_prefix_num";
         $sql="select b.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no,min(a.grouping) as  grouping,to_char(b.insert_date,'YYYY') as year from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$companyID and b.is_deleted=0 $where_con $year_cond_2 $date_cond group by  b.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no,b.insert_date ORDER BY b.job_no";
    }
    else{
        $str_data_cond="id,grouping";
        $sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no,a.grouping,to_char(b.insert_date,'YYYY') as year from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$companyID and b.is_deleted=0 $where_con $year_cond_2 $date_cond ORDER BY b.job_no";
    }

	
	  //echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$arr=array(1=>$buyer);

	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Int Ref No", "110,110,150,180","610","350",0, $sql, "js_set_value", $str_data_cond, "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,grouping", "budget_breakdown_report_controller",'','0,0,0,0,0','') ;
	disconnect($con);
	exit();
}
if($action=="show_image")
{
    
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");

	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        {
			?>
			<td align="centre"><img src='../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
			<?
        }
        ?>
        </tr>
    </table>
    <?
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST ); 

	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_name);
	$location_id=str_replace("'","",$cbo_location_id);
	$work_company_name=str_replace("'","",$cbo_work_company_name);
	$work_location_id=str_replace("'","",$cbo_work_location_id);
    $buyer_name=str_replace("'","",$cbo_buyer_name);
	$job_no=str_replace("'","",$txt_job_no);
	$style_no=str_replace("'","",$txt_style_no);
    $int_ref=str_replace("'","",$txt_int_ref);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
    $cbo_year=str_replace("'","",$cbo_year_selection);

    $year_field_con=" and to_char(a.insert_date,'YYYY')";
	if(trim($cbo_year)!=0) $year_cond_2=" $year_field_con=$cbo_year"; else $year_cond="";

    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $company_name_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
    $company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
    $imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');
    $color_library = return_library_array( "select id,color_name from lib_color order by id", "id", "color_name"  );
    	//====================================== getting image ==================================
	$image_library=return_library_array( "SELECT MASTER_TBLE_ID, IMAGE_LOCATION FROM COMMON_PHOTO_LIBRARY WHERE FORM_NAME='sample_booking_non' AND ID IN($job_no)", "MASTER_TBLE_ID", "IMAGE_LOCATION"  );
    $approval_status = array(1 => "Submitted", 2 => "Rejected", 3 => "Approved", 4 => "Cancelled", 5 => "Re-Submitted");
    

	if($company_name==0 && $buyer_name==0)
    {
        echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select company or buyer first.";
        die;
    }
	
	if($company_name!=0) $company_name_cond="and a.company_name in($company_name) "; else $company_name_cond="";
    if($location_id!=0) $location_id_cond="and a.working_company_id in($work_location_id) "; else $work_location_id_cond="";
    if($work_company_name!=0) $work_company_name_cond="and a.working_company_id in($work_company_name) "; else $work_company_name_cond="";
    if($work_location_id!=0) $work_location_id_cond="and a.working_company_id in($work_location_id) "; else $work_location_id_cond="";
    if($style_no!=0) $style_no_cond="and a.style_ref_no in($style_no) "; else $style_no_cond="";
    if($job_no!=0) $job_no_cond="and a.job_no_prefix_num =$job_no "; else $job_no_cond="";
  
	if($buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_name"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_name"].")";
			}
			else { $buyer_id_cond=""; }
		}
		else { $buyer_id_cond=""; }
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name in($buyer_name)";
	}
	if(trim($date_from)!="") $start_date=$date_from;
	if(trim($date_to)!="") $end_date=$date_to;
	//echo $shipment_status_cond;die;

	if(trim($int_ref)!="") $int_ref_cond="and b.grouping='$int_ref'";else $int_ref_cond="";
	$start_date=change_date_format($date_from,'yyyy-mm-dd','-',1);
	$end_date=change_date_format($date_to,'yyyy-mm-dd','-',1);
    if ($start_date!="" && $end_date!="") $date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'"; else $date_cond="";
    $date=date('d-m-Y');

		ob_start();
		?>
        <div style="width:1920px">
            <table width="1500px" cellpadding="0" cellspacing="0" id="caption"  align="left">
            </table>
            <br />
            <table width="1920 px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header"  align="left">
                <thead>
                    <tr>
                        <th width="40">SL</th>
                        <th width="100">Style</th>
                        <th width="120">Job No</th>
                        <th width="150">Picture</th>
						<th width="180">Yarn Quality</th>
                        <th width="120">Prod. Factory</th>
                        <th width="100">Order Qnty</th>
                        <th width="110">Color Name</th>
                        <th width="70">GG</th>
                        <th width="120">Production Approval Status</th>
                        <th width="120">Bulk Yarns Inhouse Status</th>
                        <th width="120">Production Size Set Approval Status</th>
                        <th width="110">Production Status</th>
                        <th width="110">Ship Sample Days Remains Before ETD</th>
                        <th width="100">Shipment Sample Status</th>
                        <th width="100">ETD</th>
                        <th width="70">FTY ETD</th>
                    </tr>
                </thead>
            </table>
            <div style="width:1940px; overflow-y:scroll; max-height:380px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
            <table width="1920px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                <tbody>
                <?

                $main_sql="SELECT a.style_ref_no, a.id as job_id, a.job_no_prefix_num, a.job_no,to_char(a.insert_date,'YYYY') as year,a.yarn_quality,a.style_owner,a.job_quantity,a.gauge,a.company_name, a.buyer_name,a.location_name,a.working_company_id,a.working_company_id, a.gmts_item_id, a.remarks,b.id as po_id, b.po_number, b.grouping, b.inserted_by, b.po_received_date, b.pub_shipment_date, (a.total_set_qnty*b.po_quantity) as po_quantity_pcs,(b.unit_price/a.total_set_qnty) as unit_price_pcs,c.color_number_id, d.approval_status from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_po_sample_approval_info d where b.id=c.po_break_down_id and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  AND c.status_active = 1  AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND d.po_break_down_id = c.po_break_down_id and a.company_name in ($company_name) $work_company_name_cond $work_location_id_cond $style_no_cond $buyer_id_cond  $date_cond $year_cond_2 $int_ref_cond $job_no_cond  order by a.job_no DESC";

                $main_result=sql_select($main_sql);
				$po_id_arr=array();
				foreach($main_result as $row)
                {
                    $job_num_full_text=$row[csf("job_no")];
				    $po_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
                    $job_color_arr[$row[csf("job_no")]]['job_no']=$row[csf("job_no")];
                    $job_color_arr[$row[csf("job_no")]]['style_ref_no']=$row[csf("style_ref_no")];
                    $job_color_arr[$row[csf("job_no")]]['color_number_id'].=$row[csf("color_number_id")].',';
                    $job_color_arr[$row[csf("job_no")]]['yarn_quality']=$row[csf("yarn_quality")];
                    $job_color_arr[$row[csf("job_no")]]['yarn_quality']=$row[csf("yarn_quality")];
                    $job_color_arr[$row[csf("job_no")]]['style_owner']=$row[csf("style_owner")];
                    $job_color_arr[$row[csf("job_no")]]['job_quantity']=$row[csf("job_quantity")];
                    $job_color_arr[$row[csf("job_no")]]['gauge']=$row[csf("gauge")];
                    $job_color_arr[$row[csf("job_no")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
                    $job_color_arr[$row[csf("job_no")]]['remarks']=$row[csf("remarks")];
                    $job_color_arr[$row[csf("job_no")]]['approval_status']=$row[csf("approval_status")];

				}
            //    $production_approval_statusArr=return_library_array( "select id,approval_status from wo_po_sample_approval_info",'id','approval_status');
             //   $production_approval_status= sql_select("SELECT a.job_no, TO_CHAR (a.insert_date, 'YYYY')     AS year, c.color_number_id, d.approval_status from  wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_po_sample_approval_info d where b.id=c.po_break_down_id and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  AND c.status_active = 1  AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND d.po_break_down_id = c.po_break_down_id and a.company_name in ($company_name) $work_company_name_cond $work_location_id_cond $style_no_cond $buyer_id_cond  $date_cond $year_cond_2 $int_ref_cond $job_no_cond group by a.job_no, a.insert_date, c.color_number_id,  d.approval_status ");

                  //  foreach ($production_approval_status as $row)
                   // {
                       // $production_approvalArr[$row[csf("job_no")]][$row[csf("color")]]['approval_status']=$row[csf("approval_status")];
                       // $production_approvalArr[$row[csf("job_id")]]['approval_status']=$row[csf("approval_status")];
                 //   }
                /*    echo "<pre>";
                    print_r($production_approvalArr);*/

                if($job_no!=0) $job_no_cond2="and d.job_no_prefix_num =$job_no "; else $job_no_cond2="";
                $year_yarn_field_con=" and to_char(d.insert_date,'YYYY')";
                if(trim($cbo_year)!=0) $year_cond_yarn=" $year_yarn_field_con=$cbo_year"; else $year_cond_yarn="";

                $bulk_yarn_status_inhouse_sql= sql_select("SELECT b.job_no, b.order_qnty,c.color from inv_receive_master a,inv_transaction b,  product_details_master c, wo_po_details_master d where a.id=b.mst_id and b.prod_id=c.id and b.job_no=d.job_no $job_no_cond2 $year_cond_yarn and b.company_id in ($company_name) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0");

                    foreach ($bulk_yarn_status_inhouse_sql as $val)
                    {
                        $yarn_recvArr[$row[csf("job_no")]][$row[csf("color")]]+=$row[csf("order_qnty")];
                    }
                $size_set_field_con=" and to_char(a.insert_date,'YYYY')";
                if(trim($cbo_year)!=0) $year_cond_size_set=" $size_set_field_con=$cbo_year"; else $year_cond_size_set="";

                $size_set_approval_sql= sql_select("SELECT b.id, b.sizeset_no, b.job_no,c.color_id,c.total_weight FROM wo_po_details_master a, ppl_size_set_mst    b RIGHT JOIN ppl_size_set_dtls c ON b.id = c.mst_id where b.job_no=a.job_no $job_no_cond $year_cond_size_set and  b.status_active=1 and b.is_deleted=0 group by b.id, b.sizeset_no, b.job_no,c.color_id,c.total_weight");

                foreach ($size_set_approval_sql as $size_set_row)
                {
                    $size_set_approvalArr[$size_set_row[csf("job_no")]][$size_set_row[csf("color_id")]]=$size_set_row[csf("total_weight")];
                 }
                $date=date('d-m-Y');
                $year_select="to_char(a.insert_date,'YYYY') as year";
                $days_on=" (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1";

                 $ex_factor_sql=sql_select("SELECT  MAX (c.ex_factory_date) AS ex_factory_date,$year_select,$days_on,a.job_no FROM wo_po_details_master a,wo_po_break_down b  LEFT JOIN pro_ex_factory_mst c ON b.id = c.po_break_down_id AND c.status_active = 1 AND c.is_deleted = 0 WHERE a.job_no = b.job_no_mst $date_cond $year_cond_2 $job_no_cond AND a.status_active = 1 AND b.status_active = 1 group by c.ex_factory_date,a.insert_date,b.pub_shipment_date,a.job_no");

                $ex_factory_date_arr=array();
                 foreach ($ex_factor_sql as $row)
                 { 
                     $ex_factory_date_arr[$row[csf('job_no')]]['ex_factory_date']=$row[csf('ex_factory_date')];
                     $ex_factory_date_arr[$row[csf('job_no')]]['date_diff_1']=$row[csf('date_diff_1')];
                 }


                $year_field_con=" and to_char(a.insert_date,'YYYY')";
                if(trim($cbo_year)!=0) $year_cond_3=" $year_field_con=$cbo_year"; else $year_cond3="";
                if($job_no!=0) $job_no_cond2="and d.job_no_prefix_num =$job_no "; else $job_no_cond2="";
                if($job_no!=0) $job_no_cond2="and a.job_no_prefix_num =$job_no "; else $job_no_cond2="";

                 $shipment_sample_status= sql_select("SELECT a.id, a.job_no_prefix_num, d.company_id, a.job_no, TO_CHAR (b.pub_shipment_date, 'DD-MM-YYYY') AS pub_shipment_date, d.production_quantity, b.id AS po_id, b.po_number,d.item_number_id,d.sewing_line FROM wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst d WHERE a.id = b.job_id AND d.PO_BREAK_DOWN_ID = b.id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND a.company_name IN ($company_name)  $year_cond_3 $job_no_cond2 group by a.id, a.job_no_prefix_num, d.company_id, a.job_no, b.pub_shipment_date,  d.production_quantity,  b.id,b.po_number,d.item_number_id,d.sewing_line ORDER BY pub_shipment_date ASC ");

                 foreach ($shipment_sample_status as $sample_status_row)
                 {
                     $shipment_sample_statusArr[$sample_status_row[csf("job_no")]][$sample_status_row[csf("po_number")]]["item_number_id"]+=$sample_status_row[csf("item_number_id")];
                     $shipment_sample_statusArr[$sample_status_row[csf("job_no")]][$sample_status_row[csf("po_number")]]["production_quantity"]+=$sample_status_row[csf("production_quantity")];
                     $shipment_sample_statusArr[$sample_status_row[csf("job_no")]][$sample_status_row[csf("po_number")]]["sewing_line"]+=$sample_status_row[csf("sewing_line")];
                 }

                $k=1;$m=1;
                $temp_arr_buyer=array();
                foreach($job_color_arr as $job_no=>$row)
                { 
                    if($m%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $colorId=trim($row["color_number_id"],',');
                    $colorIdArr=explode(",",$colorId);
                    $yarn_recv=0;
                    $shipment_sample_status=0;
                    $size_set_approval=0;
                    foreach($colorIdArr as $cid)
                    {
                        $shipment_sample_status+=$shipment_sample_statusArr[$job_no][$cid];
                        $yarn_recv+= $yarn_recvArr[$job_no][$cid];
                        $size_set_approval= $size_set_approvalArr[$job_no][$cid];
                        $color_arr[$cid]=$color_library[$cid];
                    }
                    //echo count($size_set_approval);
                    if(count($yarn_recv)>0)
                    {
                        $bulk_yarn_status="Inhouse";
                    }
                    else $bulk_yarn_status="";
                    
                    if(count($size_set_approval)>0){
                        $Production_Size_Set_Approval_Status="Approved";
                    }
                    else $Production_Size_Set_Approval_Status="";

                    
                    
                    if(count($shipment_sample_statusArr)>0){
                        $shipment_sample_status_result="Goods Ready";
                    }
                    else $shipment_sample_status_result="";



                    $ex_factory_date=$ex_factory_date_arr[$job_no]['ex_factory_date'];
                    $date_diff_1=$ex_factory_date_arr[$job_no]['date_diff_1'];
                    $job_no=str_replace("'","",$txt_job_no);


                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
                        <td width="40" align="center"><p><? echo $m;?>&nbsp;</p></td>
                        <td width="100"><p><? echo $row["style_ref_no"]; ?>&nbsp;</p></td>
                        <td width="120"><p><? echo $job_no; ?>&nbsp;</p></td>

                        <td width="150" onclick="openmypage_image('requires/shipment_track_report_controller.php?action=show_image&job_no=<?=$job_num_full_text ?>','Image View')"><img  src='<? echo  base_url($imge_arr[$job_num_full_text]); ?>' height='25' width='30' /></td>
						
                        <td width="180"><p><? echo $row["yarn_quality"]; ?>&nbsp;</p></td>
                        <td width="120"><p><? echo $company_name_arr[$row["style_owner"]]; ?>&nbsp;</p></td>
                        <td width="100"><p>
                            <? 
                                $total_job_quantity +=$row[('job_quantity')];
                                echo $row["job_quantity"];
                            ?>&nbsp;</p></td>
                        
                        <td width="110"><p><? echo implode(",",$color_arr); ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $gauge_arr[$row["gauge"]];?>&nbsp;</p></td>
                        <td width="120"><p><? if($row["approval_status"]!=0)echo "Approved"; else echo "";?>&nbsp;</p></td>
                        <td width="120"><p><?=$bulk_yarn_status; ?>&nbsp;</p></td>
                        <td width="120"><p><?=$Production_Size_Set_Approval_Status; ?>&nbsp;</p></td>
                        <td width="110" align="left"><p><?=$shipment_sample_status_result  ?>&nbsp;</p></td>
                        <td width="110" align="left"><? $total_date_diff +=$date_diff_1; echo $date_diff_1; ?></td>
                        <td width="100" align="center"><p><? echo $row["pub_shipment_date"]; ?> &nbsp;</p></td>
                        <td width="100" align="center"><?=$ex_factory_date.$ex_factory_date_arr[$row[csf('job_no')]]['ex_factory_date']; ?></td>
                        <td width="70" align="center"><?=$row["pub_shipment_date"]; ?></td>
                    </tr>
                    <?
                    $m++;
                } 
                ?>
                    <tr bgcolor="#DDDDDD">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right">Total:&nbsp;</td>
						<td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($total_job_quantity,2); ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($total_date_diff,2); ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr  bgcolor="#CCCCCC">
                        <td width="40">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="120">&nbsp;</td>
						<td width="150" align="right">Grand Total:&nbsp;</td>
                        <td width="180">&nbsp;</td>
                        <td width="120">&nbsp;</td>
                        <td width="100" align="right"><? $grand_total+=$total_job_quantity; echo number_format($grand_total,2); ?></td>
                        <td width="110">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="120">&nbsp;</td>
                        <td width="120">&nbsp;</td>
                        <td width="120">&nbsp;</td>
                        <td width="110">&nbsp;</td>
                        <td width="110" align="right"><? $grand_date_diff_total+=$total_date_diff; echo number_format($grand_date_diff_total,2); ?></td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
        <?
}



?>  