<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action']; 
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$body_part_arr=return_library_array( "select id,body_part_full_name from lib_body_part", "id", "body_part_full_name"  );
$buyer_list=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$floor_arr=return_library_array( "select id,floor_name from  lib_prod_floor",'id','floor_name');
$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
$process_format=array(0=>"ALL", 32 => 'Heat Setting', 47 => "Singeing", 30 => 'Slitting/Squeezing', 31 => 'Drying', 48 => "Stentering", 33 => 'Compacting', 34 => 'Special Finish',68 => 'Brush', 67 => 'Peach');
                               
if($db_type==0) $group_concat="group_concat(c.po_number)"; 
else if($db_type==2) $group_concat="listagg(c.po_number,',' ) within group (order by c.po_number) AS po_number";


//--------------------------------------------------------------------------------------------------------------------
 
//popup for booking number
if($action=="bookingnumbershow")
{
    echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>
    <script>
      var company_id='<? echo $company_name;?>';
      function js_set_value(id)
      { 
        document.getElementById('selected_id').value=id;
        parent.emailwindow.hide();
      }
    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
        <fieldset style="width:900px;">
            <table width="896" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <th>Company</th>
                    <th>Buyer</th>
                    <th>Year</th>
                    <th>Within Group</th>
                    <th>FSO No</th>
                    <th>Booking No</th>
                    <th>Style Ref.</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                    <tr>
                        <td> 
                            <?
                            echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down('requires/fabric_finishing_report_urmi_controller', this.value, 'load_drop_down_buyer_fso', 'buyer_td_fso' );" );
                            ?>

                        </td>
                        <td id="buyer_td_fso">
                            <?
                            echo create_drop_down( "cbo_buyer_name", 110, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by short_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
                            ?>
                        </td>
                        <td>
                            <?
                            echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                            ?>
                        </td>
                        <td>
                            <?
                            echo create_drop_down( "cbo_within_group", 65, $yes_no,"", 1,"-- All --", "", "",0,"" );
                            ?>
                        </td>
                        <td align="center"> 
                            <input type="text" style="width:130px" class="text_boxes" name="txt_fso_no" id="txt_fso_no" />
                        </td>

                        <td align="center"> 
                            <input type="text" style="width:130px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
                        </td> 
                        <td align="center">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_style_no" id="txt_style_no" />
                        </td>                 
                        <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('txt_fso_no').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_style_no').value+'**'+document.getElementById('cbo_within_group').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('cbo_buyer_name').value, 'bookingnumbershow_search_list_view', 'search_div', 'fabric_finishing_report_urmi_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
            <div style="margin-top:15px" id="search_div"></div>
    </form>


    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script type="text/javascript">
        $("#cbo_company_name").val(company_id);
    </script>
    </html>
    <?
    exit(); 
}


if($action=="bookingnumbershow_search_list_view")
{
    //echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    list($company_name,$txt_fso_no,$txt_booking_no,$txt_style_no,$cbo_within_group,$cbo_year,$cbo_buyer_name)=explode('**',$data);

    if($txt_fso_no)    $search_con=" and a.job_no_prefix_num =$txt_fso_no"; 
    if($txt_booking_no)       $search_con .=" and a.sales_booking_no like('%$txt_booking_no%')";
    if($txt_style_no)       $search_con .=" and a.style_ref_no like('%$txt_style_no%')";
    if($cbo_within_group)       $search_con .=" and a.within_group =$cbo_within_group";
    if($cbo_buyer_name)       $search_con .=" and a.buyer_id=$cbo_buyer_name";
    if($cbo_year)       $search_con .=" and to_char(a.insert_date,'YYYY')= $cbo_year";

    
    ?>
    <input type="hidden" id="selected_id" name="selected_id" /> 
    <? 
   
     $sql="SELECT a.id, a.job_no_prefix_num,a.sales_booking_no,a.style_ref_no,a.within_group,(case when a.within_group=1 then b.buyer_id else  a.buyer_id end) as buyer_id from FABRIC_SALES_ORDER_MST a left join wo_booking_mst b on a.sales_booking_no=b.booking_no  where a.company_id=$company_name and a.is_deleted = 0 $search_con group by a.id, a.job_no_prefix_num,a.sales_booking_no,a.style_ref_no,a.within_group,(case when a.within_group=1 then b.buyer_id else  a.buyer_id end)"; 
    $arr=array(3=>$yes_no,4=>$buyer_arr);
    echo  create_list_view("list_view", "Fso no,Booking no,Style,Within Group,Buyer", "100,100,100,100,170","620","290",0, $sql, "js_set_value", "job_no_prefix_num,job_no_prefix_num", "", 1, "0,0,0,within_group,buyer_id", $arr , "job_no_prefix_num,sales_booking_no,style_ref_no,within_group,buyer_id", "",'','0') ;
    exit();
}//bookingnumbershow;


if($action=="batchnumbershow")
{
	echo load_html_head_contents("Batch Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	  function js_set_value(id)
	  { 
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
	  }
	</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:750px;">
            <table width="746" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Batch No</th>
                    <th>Batch Date Range</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                            <input type="text" style="width:150px" class="text_boxes" name="txt_batch_no" id="txt_batch_no" />
                        </td>                 
                        <td align="center">	
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px" placeholder="From Date"/>
                                     &nbsp;To&nbsp;
                                     <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px" placeholder="To Date"/>
                        </td>     
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_name; ?>'+'**'+document.getElementById('txt_batch_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'batchnumbershow_search_list_view', 'search_div', 'fabric_finishing_report_urmi_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
                    <tr>
                    <tr>
                        <td headers="5"></td>
                    </tr>
                    <td colspan="8">
                        <? echo load_month_buttons(1); ?>
                    </td>
                </tr>
            	</tbody>
           	</table>
		</fieldset>
            <div style="margin-top:15px" id="search_div"></div>
	</form>


    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}


if($action=="batchnumbershow_search_list_view")
{
	//echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    list($company_name,$txt_batch_no,$txt_date_from,$txt_date_to)=explode('**',$data);
    $date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
    $date_to=change_date_format($txt_date_to,'yyyy-mm-dd');           
         
    if($db_type==2)
    {
        $txt_date_from=change_date_format($txt_date_from,'','',1);
        $txt_date_to=change_date_format($txt_date_to,'','',1);
    }
	
	if($txt_batch_no!=''){
		$search_con=" and batch_no like('%$txt_batch_no')";	
	}
   
	if($txt_date_from!='' && $txt_date_to!='')
    {
		 $search_con .=" and batch_date between '$txt_date_from' and '$txt_date_to'";	
	}

	
    ?>
    <input type="hidden" id="selected_id" name="selected_id" /> 
    <? if($db_type==0) $field_grpby=" GROUP BY batch_no"; 
    else if($db_type==2) $field_grpby="GROUP BY batch_no,extention_no,id,batch_no,batch_for,booking_no,color_id,batch_weight";
    $sql="SELECT id,batch_no,extention_no,batch_for,booking_no,color_id,batch_weight from pro_batch_create_mst where company_id=$company_name and is_deleted = 0 $search_con $field_grpby ";	
    $arr=array(2=>$color_library,4=>$batch_for);
    echo  create_list_view("list_view", "Batch no,Ext No,Color,Booking no, Batch for,Batch weight ", "100,50,100,100,100,170","620","290",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,0,color_id,0,batch_for,0", $arr , "batch_no,extention_no,color_id,booking_no,batch_for,batch_weight", "",'','0') ;
    exit();
}//batchnumbershow;


 if ($action=="load_drop_down_location")
{
    echo create_drop_down( "cbo_location_id", 110, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in($data)    order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/fabric_finishing_report_urmi_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
    exit();      
}
if ($action=="load_drop_down_floor")
{
    $ex_data = explode("_", $data);
    echo create_drop_down( "cbo_floor_id", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process in(3,4) and company_id in($ex_data[0]) and location_id in($ex_data[1]) order by floor_name","id,floor_name",1, "-- Select Floor --", $selected, "load_drop_down( 'requires/fabric_finishing_report_urmi_controller',this.value, 'load_drop_down_machine', 'machine_td' );",0 );            
    exit();  
}

if ($action=="load_drop_down_machine")
{
    
    
    echo create_drop_down( "cbo_machine_id", 110, "SELECT id,machine_no || '-' || brand as machine_name from lib_machine_name where   floor_id=$data and status_active=1 and is_deleted=0 and is_locked=0 ","id,machine_name", 1, "-- Select Machine --", $selected, "",0 );            
    exit();  
}

if($action=="load_drop_down_buyer")
{ 
	
    echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	exit();
}
if($action=="load_drop_down_buyer_fso")
{ 
    
    echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
    exit();
}


 

 
if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
  //  $all_condition="";	 

    $cbo_type = str_replace("'","",$cbo_type);
    $cbo_company_name = str_replace("'","",$cbo_company_name);
    $cbo_location_id = str_replace("'","",$cbo_location_id);
    $cbo_floor_id = str_replace("'","",$cbo_floor_id);
    $cbo_machine_id = str_replace("'","",$cbo_machine_id);
    $cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
    $cbo_year = str_replace("'","",$cbo_year);
    $fso_no = str_replace("'","",$booking_number);
    $cbo_source = str_replace("'","",$cbo_source); 
    $cbo_gate_upto = str_replace("'","",$cbo_gate_upto);  
    $txt_days = str_replace("'","",$txt_days); 
    $txt_date_from = str_replace("'","",$txt_date_from); 
    $txt_date_to = str_replace("'","",$txt_date_to); 
    $batch_number = str_replace("'","",$batch_number); 
    $batch_number_show = str_replace("'","",$batch_number_show); 
    $booking_number_show = str_replace("'","",$booking_number_show); 
	$all_condition2="";
    if($cbo_type)
    {
        if($cbo_type==67 || $cbo_type==68  || $cbo_type==35   || $cbo_type==194 ) $all_condition2.=" and d.process_id like '%$cbo_type%'";
        else   $all_condition2.=" and d.entry_form=$cbo_type";
    }
    if($cbo_company_name>0){$all_condition2.=" and d.service_company in($cbo_company_name)";}
    if($cbo_floor_id>0) {$all_condition2.=" and d.floor_id in($cbo_floor_id)";}
    if($cbo_machine_id>0) {$all_condition2.=" and d.machine_id in($cbo_machine_id)";}
	//echo $all_condition2.'ddd';die;
    if($cbo_buyer_name) $all_condition2.=" and a.party_id in($cbo_buyer_name)";
    //if($cbo_year) $all_condition.=" and to_char(a.insert_date,'YYYY') =$cbo_year";
	
	 if($cbo_year>0) $all_condition2.=" and to_char(c.insert_date,'YYYY') =$cbo_year";
   // if($fso_no)$all_condition.=" and a.job_no_prefix_num=$fso_no";
    if($cbo_source>0) {$all_condition2.=" and d.service_source=$cbo_source";}
    if($batch_number!="") {$all_condition2.=" and c.id in($batch_number)";}
    if($batch_number_show!="") { $all_condition2.=" and c.batch_no in('$batch_number_show')";}
    if($booking_number_show!="") $all_condition2.=" and a.job_no_prefix_num='$booking_number_show'";
 
    if($txt_date_from && $txt_date_to)
    {
        if($db_type==0)
        {
            $date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
            $date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
            $all_condition.="and  d.process_end_date BETWEEN '$date_from' AND '$date_to'";
		   $all_condition2.="and  d.process_end_date BETWEEN '$date_from' AND '$date_to'";
            
        }
        if($db_type==2)
        {
            $date_from=change_date_format($txt_date_from,'','',1);
            $date_to=change_date_format($txt_date_to,'','',1);
         //   $all_condition.=" and  d.process_end_date BETWEEN '$date_from' AND '$date_to'";
			$all_condition2.=" and  d.process_end_date BETWEEN '$date_from' AND '$date_to'";
             
        }
    }  
	
    if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name"; 
    else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
    $machine_arr=return_library_array( "select id,$field_concat from  lib_machine_name",'id','machine_name'); 
	/*	 $sql_batch="SELECT f.id as batch_dtls_id,f.prod_id,d.batch_ext_no as process_batch_ex,d.entry_form, c.id as batch_id,f.body_part_id,f.item_description as fabric_desc,d.batch_no,d.batch_ext_no,f.batch_qnty as batch_qty FROM pro_batch_create_mst c ,pro_batch_create_dtls f,fabric_sales_order_mst a,pro_fab_subprocess d Where   c.id=f.mst_id and a.id=c.sales_order_id and c.id=d.batch_id and a.SALES_BOOKING_NO=c.booking_no  and a.status_active=1 and c.status_active=1 and d.status_active=1 and d.entry_form in(32,47,30,31,48,33,34) $all_condition order by c.id asc "; 
	 foreach(sql_select($sql_batch) as $row)
     { 
	 	 if($dup_chk_entry_arr[$row[csf("entry_form")]][$row[csf("batch_dtls_id")]]=="")
        {
            $batch_qty=$row[csf("batch_qty")];
            $dup_chk_entry_arr[$row[csf("entry_form")]][$row[csf("batch_dtls_id")]]=420;
        }
        else $batch_qty=0;
		$batch_qty_arr[$row[csf("batch_id")]][$row[csf("body_part_id")]][$row[csf("prod_id")]][$row[csf("entry_form")]]+=$batch_qty;
	 }*/
	 
	 
  
	 
	 $main_query_sub="SELECT a.party_id as buyer_name,a.subcon_job,b.cust_style_ref as style_ref_no, c.entry_form as bat_entry_form,d.batch_ext_no as process_batch_ex,f.id as batch_dtls_id, e.roll_id, d.previous_process, d.process_id,d.re_stenter_no AS re_process1,
     e.id as dtls_id,d.entry_form, c.id as batch_id,c.extention_no as batch_ext_no, f.prod_id,f.body_part_id,f.item_description as fabric_desc,e.gsm as gsm_weight,e.dia_width ,e.width_dia_type,c.color_id,c.color_range_id,b.order_no,d.batch_no,d.batch_ext_no as batch_ext, d.ltb_btb_id,d.water_flow_meter,d.production_date as process_end_date,d.temparature,d.stretch,d.over_feed,d.feed_in,d.insert_date,d.pinning,d.speed_min,d.end_hours,d.end_minutes,d.machine_id,d.floor_id,d.load_unload_id,d.result,d.multi_batch_load_id,d.shift_name,d.process_end_date as production_date,d.remarks, d.chemical_name,d.length_shrinkage,d.width_shrinkage,d.spirality,d.process_start_date,d.start_minutes,d.start_hours,d.service_source,d.service_company,d.received_chalan,d.issue_chalan,d.issue_challan_mst_id,d.fabric_type,d.re_stenter_no,d.booking_no,d.hour_load_meter,d.hour_unload_meter,d.system_no,d.recv_number_prefix,d.recv_number_prefix_num,d.recv_number,d.receive_date  ,f.batch_qnty as batch_qty, e.production_qty,e.roll_no, e.no_of_roll FROM subcon_ord_mst a, subcon_ord_dtls b,pro_batch_create_mst c ,pro_fab_subprocess d ,pro_fab_subprocess_dtls e,pro_batch_create_dtls f Where  b.job_no_mst=a.subcon_job and b.id=f.po_id and c.id=d.batch_id and d.id=e.mst_id and  c.id=f.mst_id  and e.prod_id=f.prod_id  and c.status_active=1 and d.status_active=1 and e.status_active=1 and a.status_active=1 and b.status_active=1 and d.entry_form in(32,47,30,31,48,33,34)  and c.entry_form=36  $all_condition2 order by c.id,d.insert_date asc "; //order by c.id,d.insert_date asc
	 //in(32,47,30,31,48,33,34)
  
	 $sub_main_array=array(); $sub_chk_batch_qty=array();$sub_prod_qty_arr=array();
    $sub_all_booking=array(); 
    $sub_all_batch=array(); 
    foreach(sql_select($main_query_sub) as $row)
    { 
        $production_qty=0;
        $brush_qty=0;
        $peach_qty=0;
        $chemical_qty=0;
        $aop_qty=0;
        $ext_production_qty=0;
        $re_work_production_qty=0;
      
		//echo $row[csf("batch_id")].'CCXX';
        $all_batch[$row[csf("batch_id")]]=$row[csf("batch_id")];
        if($row[csf("entry_form")]==34)
        {
            $row[csf("entry_form")]=$row[csf("process_id")];
        }

        $head_arr[$row[csf("entry_form")]]=$row[csf("entry_form")];
        if($dup_chk_arr[$row[csf("entry_form")]][$row[csf("batch_dtls_id")]][$row[csf("roll_id")]]=="")
        {
            $batch_qty=$row[csf("batch_qty")];
            $dup_chk_arr[$row[csf("entry_form")]][$row[csf("batch_dtls_id")]][$row[csf("roll_id")]]=420;
        }
        else $batch_qty=0;

         if($dup_chk_arr2[$row[csf("entry_form")]][$row[csf("batch_id")]] =="")
        {
             
            $dup_chk_arr2[$row[csf("entry_form")]][$row[csf("batch_id")]] =420;
            $summary_arr[$row[csf("entry_form")]]["no_of_batch"] +=1;
        }
		//party_id as buyer_name,a.subcon_job,b.cust_style_ref

        
        //if($re_stenter_no==0 || $re_stenter_no=="")$production_qty=$row[csf("production_qty")];
      
        //if($ext_no)$production_qty_ex=$row[csf("production_qty")];
        if($fab_dlts_id_chk[$row[csf("batch_id")]][$row[csf("dtls_id")]]=="")
        {
            if($row[csf("previous_process")]==1)$brush_qty=$row[csf("production_qty")];
            else if($row[csf("previous_process")]==2)$peach_qty=$row[csf("production_qty")];
            else if($row[csf("previous_process")]==3)$chemical_qty=$row[csf("production_qty")];
            else if($row[csf("previous_process")]==4)$aop_qty=$row[csf("production_qty")];
            else
            {

                if($row[csf("process_batch_ex")]*1>0)
                 $re_work_production_qty=$row[csf("production_qty")];
                else $production_qty=$row[csf("production_qty")];
				//echo $row[csf("entry_form")].'='.$production_qty;
            }
         //   if($row[csf("batch_ext_no")])$ext_production_qty=$row[csf("production_qty")];
			$summary_arr[$row[csf("entry_form")]]["no_of_roll"]+=$row[csf("no_of_roll")];
			 if($row[csf("re_stenter_no")]>0) $re_work_production_qty=$row[csf("production_qty")];
			if($row[csf("batch_ext_no")])
			{
			   $re_work_production_qty=0;
			   $ext_production_qty=$row[csf("production_qty")];
			}
			if($row[csf("previous_process")]==1)
			{
			   $re_work_production_qty=0;
			    $ext_production_qty=0;
			}
			if($row[csf("previous_process")]==2)
			{
			   $re_work_production_qty=0;
			     $ext_production_qty=0;
			}

			if($row[csf("previous_process")]==3)
			{
			   $re_work_production_qty=0;
			     $ext_production_qty=0;
			}
			if($row[csf("previous_process")]==4)
			{
			   $re_work_production_qty=0;
			    $ext_production_qty=0;
			} 
            $fab_dlts_id_chk[$row[csf("batch_id")]][$row[csf("dtls_id")]]=420;
         // $summary_arr[$row[csf("entry_form")]]["no_of_roll"]+=1;//no_of_roll
			

        }
        
       
        $summary_arr[$row[csf("entry_form")]]["batch_qty"]+=$batch_qty;
        $summary_arr[$row[csf("entry_form")]]["production_qty"]+=$production_qty;
        $summary_arr[$row[csf("entry_form")]]["ext_production_qty"]+=$ext_production_qty;
        $summary_arr[$row[csf("entry_form")]]["re_work_production_qty"]+=$re_work_production_qty;
        $summary_arr[$row[csf("entry_form")]]["brush_qty"]+=$brush_qty;
        $summary_arr[$row[csf("entry_form")]]["peach_qty"]+=$peach_qty;
        $summary_arr[$row[csf("entry_form")]]["chemical_qty"]+=$chemical_qty;
        $summary_arr[$row[csf("entry_form")]]["aop_qty"]+=$aop_qty;
        


        $dtls_arr_qnty[$row[csf("machine_id")]][$row[csf("shift_name")]][$row[csf("entry_form")]]+=$production_qty;
        $dtls_arr_head[$row[csf("machine_id")]][$row[csf("shift_name")]]=$row[csf("shift_name")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["remarks"]=$row[csf("remarks")];   $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["fabric_desc"]=$row[csf("fabric_desc")];
        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["po_job_no"]=$row[csf("subcon_job")];
		$sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["cust_style_ref"]=$row[csf("cust_style_ref")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["re_stenter_no"]=$row[csf("re_stenter_no")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["previous_process"]=$row[csf("previous_process")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["process_id"]=$row[csf("process_id")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["re_process1"]=$row[csf("re_process1")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["re_process2"]=$row[csf("re_process2")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["booking_type"]=$row[csf("booking_type")];


        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["width_shrinkage"]=$row[csf("width_shrinkage")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["chemical_name"]=$row[csf("chemical_name")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["length_shrinkage"]=$row[csf("length_shrinkage")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["stretch"]=$row[csf("stretch")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["over_feed"]=$row[csf("over_feed")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["feed_in"]=$row[csf("feed_in")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["pinning"]=$row[csf("pinning")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["speed_min"]=$row[csf("speed_min")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["no_of_roll"]=$row[csf("no_of_roll")];
        



        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["process_end_date"]=$row[csf("process_end_date")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["process_end_time"]=$row[csf("end_hours")].':'.$row[csf("end_minutes")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["process_start_date"]=$row[csf("process_start_date")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["process_start_time"]=$row[csf("start_hours")].':'.$row[csf("start_minutes")];


        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["batch_no"]=$row[csf("batch_no")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["temparature"]=$row[csf("temparature")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["sales_booking_no"]=$row[csf("order_no")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["style_ref_no"]=$row[csf("style_ref_no")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["season"]=$row[csf("season")];

      //  $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["within_group"]=$row[csf("within_group")];
//echo $row[csf("buyer_name")].'D';
        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["buyer_id"]=$row[csf("buyer_name")]; 
       // $main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("determination_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["po_job_no"]=$row[csf("po_job_no")];
        $comp_arr=explode(",",$row[csf("fabric_desc")]);
        if($sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["fabric_desc"]=="")
        {
            $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["fabric_type"].=$comp_arr[0];
            $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["fabric_desc"].=$comp_arr[1];
            $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["body_part_id"].=$body_part_arr[$row[csf("body_part_id")]];
            $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["gsm_weight"].=$row[csf("gsm_weight")];

            $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["width_dia_type"].=$fabric_typee[$row[csf("width_dia_type")]];

            $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["dia"].=$row[csf("dia_width")];


        }
        else
        {
            $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["fabric_type"].=','.$comp_arr[0];
            $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["fabric_desc"].=','.$comp_arr[1];

            $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["body_part_id"].=','.$body_part_arr[$row[csf("body_part_id")]];
            $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["gsm_weight"].=','.$row[csf("gsm_weight")];

            $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["width_dia_type"].=','.$fabric_typee[$row[csf("width_dia_type")]];

            $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["dia"].=','.$row[csf("dia_width")];
        }
        
        

        
        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["floor_id"]=$row[csf("floor_id")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["buyer_id"]=$row[csf("buyer_name")];

        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["shift_name"]=$row[csf("shift_name")];
        $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["color_range_id"]=$row[csf("color_range_id")];
        if(! $qnty_chk_arr[$row[csf("dtls_id")]])
        {   
            // $main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("determination_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["batch_qty"]+=$row[csf("batch_qty")];
		//echo $row[csf("production_qty")].'='.$row[csf("entry_form")].'<br>';
            $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["production_qty"]+=$row[csf("production_qty")];
			  $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["no_of_roll"]+=$row[csf("no_of_roll")];
			// $prod_qty_arr[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("determination_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["production_qty"]+=$row[csf("production_qty")];
            $qnty_chk_arr[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
        }
        else if(! $qnty_chk_arr_ex_prod[$row[csf("batch_id")]][$row[csf("dtls_id")]]  &&  $row[csf("batch_ext_no")])
        {
           //$main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("job_no")]][$row[csf("style_ref_no")]][$row[csf("body_part_id")]][$row[csf("determination_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["batch_qty_ex"]+=$row[csf("batch_qty")];
            //echo "string".$row[csf("batch_ext_no")].' '.$row[csf("batch_no")]."<br>";
            $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["production_qty_ex"]+=$row[csf("production_qty")];
            $qnty_chk_arr_ex_prod[$row[csf("batch_id")]][$row[csf("dtls_id")]]=$row[csf("dtls_id")]; 
        }

       // if($roll_count[$row[csf("entry_form")]][$row[csf("production_date")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("fabric_desc")]][$row[csf("re_stenter_no")]][$row[csf("roll_no")]][$row[csf("dtls_id")]]=="")
	    if($fab_dlts_id_chk2[$row[csf("batch_id")]][$row[csf("dtls_id")]]=="")
        {
            //echo $row[csf("batch_no")];
            $roll_count2[$row[csf("entry_form")]][change_date_format($row[csf("production_date")])][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("fabric_desc")]]+=$row[csf("no_of_roll")];
			 $fab_dlts_id_chk2[$row[csf("batch_id")]][$row[csf("dtls_id")]]=420;
            //$roll_count[$row[csf("entry_form")]][$row[csf("production_date")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("fabric_desc")]][$row[csf("re_stenter_no")]][$row[csf("roll_no")]][$row[csf("dtls_id")]]=420;
        }
		if($dup_chk_entry_arr2[$row[csf("entry_form")]][$row[csf("batch_dtls_id")]]=="")
        {
            $batch_qty=$row[csf("batch_qty")];
            $dup_chk_entry_arr2[$row[csf("entry_form")]][$row[csf("batch_dtls_id")]]=420;
        }
        else $batch_qty=0;
	   // $batch_qty=$row[csf("batch_qty")];
		//echo  $batch_qty.'<br>';
            $sub_main_array[$row[csf("entry_form")]][$row[csf("machine_id")]][$row[csf("subcon_job")]][$row[csf("prod_id")]][$row[csf("color_id")]][$row[csf("batch_id")]][$row[csf("batch_ext_no")]][$row[csf("re_stenter_no")]][$row[csf("service_company")]][$row[csf("service_source")]][$row[csf("production_date")]]["batch_qty"]+=$batch_qty;

    } //SubCon End
	
	$sub_prod_rowspan_arr=array();//sub_main_array
	 foreach($sub_main_array as $entry_form_id=>$machine_data)
	 {
		foreach($machine_data as $machine_id=>$job_data)
		{
			foreach($job_data as $job_no=>$body_data)
			{
					$sub_prod_row_span=0;
					foreach($body_data as $prod_id=>$color_data)
					{  
							foreach($color_data as $color_id=>$batch_data)
							{
								foreach($batch_data as $batch_id=>$batch_ext_no_data)
								{
									foreach($batch_ext_no_data as $ext_no=>$re_stenter_no_data )
									{
										foreach($re_stenter_no_data as $re_stenter_no=>$service_company_data)
										{
											foreach($service_company_data as $service_company=>$service_source_data)
											{
												foreach($service_source_data as $service_source=>$production_date_data)
												{
													foreach($production_date_data as $production_date=>$row)
													{
														if($entry_form_id==30)
														{
														$prod_qty_arr[$entry_form_id][$machine_id][$job_no][$prod_id][$color_id][$batch_id][$ext_no][$re_stenter_no][$service_company][$service_source]["production_qty"]+=$row["production_qty"];
														$prod_qty_arr[$entry_form_id][$machine_id][$job_no][$prod_id][$color_id][$batch_id][$ext_no][$re_stenter_no][$service_company][$service_source]["no_of_roll"]+=$row["no_of_roll"];
														}
														else
														{
														$prod_qty_arr[$entry_form_id][$machine_id][$job_no][$prod_id][$color_id][$batch_id][$ext_no][$re_stenter_no][$service_company][$service_source][$production_date]["production_qty"]+=$row["production_qty"];
														$prod_qty_arr[$entry_form_id][$machine_id][$job_no][$prod_id][$color_id][$batch_id][$ext_no][$re_stenter_no][$service_company][$service_source][$production_date]["no_of_roll"]+=$row["no_of_roll"];
														
														}
														$sub_prod_row_span++;
													}
												$sub_prod_rowspan_arr[$entry_form_id][$machine_id][$job_no][$color_id][$batch_id][$ext_no][$service_company][$service_source]=$sub_prod_row_span;
									}
								}
							}
						}
					}
				  }
				}
			}
		}
	 }
	//print_r($prod_qty_arr);
	
    $all_booking_nos="'".implode("','",$all_booking)."'";
	$all_booking_nos_non="'".implode("','",$all_booking_non)."'";
    $all_batch_id="'".implode("','",$all_batch)."'";

   $process_seq_sql="SELECT    batch_id,  process_id , machine_id  FROM pro_fab_subprocess Where  status_active=1 and  batch_id in($all_batch_id)   order by  id asc ";
    foreach(sql_select($process_seq_sql) as $val )
    {
        if($process_seq_arr[$val[csf("batch_id")]][$val[csf("machine_id")]])
        $process_seq_arr[$val[csf("batch_id")]][$val[csf("machine_id")]].=','.$val[csf("process_id")];
        else 
             $process_seq_arr[$val[csf("batch_id")]][$val[csf("machine_id")]].=$val[csf("process_id")];
    }
    //echo "<pre>"; print_r($process_seq_arr);die;
     $dyeing_sql="SELECT batch_id, batch_no, batch_ext_no,process_end_date,end_minutes,end_hours from pro_fab_subprocess where batch_id in($all_batch_id) and  status_active=1 and   entry_form in(38) and load_unload_id = 2 " ;
    foreach(sql_select($dyeing_sql) as $row )
    {
        $dyeing_data[$row[csf("batch_id")]][$row[csf("batch_ext_no")]]["date"]=$row[csf("process_end_date")];
        $dyeing_data[$row[csf("batch_id")]][$row[csf("batch_ext_no")]]["time"]=$row[csf("end_hours")].":".$row[csf("end_minutes")];
    }
	 $booking_data_arr=array();
	$booking_data_arr=array();
$non_booking_sql="SELECT buyer_id,booking_no,booking_type from wo_non_ord_samp_booking_mst where booking_no in($all_booking_nos_non) and  status_active=1 and   booking_type = 4 " ;
    foreach(sql_select($non_booking_sql) as $row )
    {
		if($row[csf("booking_type")]==4)
        {
             $booking_data_arr[$row[csf("booking_no")]]["type"]="Sample Without Order";
			  $booking_data_arr[$row[csf("booking_no")]]["buyer_id"]=$row[csf("buyer_id")];
        }
    }
	// 
   
    $booking_data="SELECT a.buyer_id, a.booking_no ,a.short_booking_type ,a.booking_type,a.is_short,b.division_id from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1 and a.booking_no in($all_booking_nos) group by  a.buyer_id, a.booking_no ,a.short_booking_type ,a.booking_type,a.is_short,b.division_id ";
    foreach(sql_select($booking_data) as $v)
    {
        if($booking_data_arr[$v[csf("booking_no")]]["division"])
            $booking_data_arr[$v[csf("booking_no")]]["division"].=','.$short_division_array[$v[csf("division_id")]];
        else $booking_data_arr[$v[csf("booking_no")]]["division"].=$short_division_array[$v[csf("division_id")]];

        $booking_data_arr[$v[csf("booking_no")]]["short_booking_type"] =$short_booking_type[$v[csf("short_booking_type")]];
        if($v[csf("booking_type")]==1 && $v[csf("is_short")]==1)
        {

             $booking_data_arr[$v[csf("booking_no")]]["type"]="Short";
        }

        else if($v[csf("booking_type")]==1 && $v[csf("is_short")]==2)
        {
            
             $booking_data_arr[$v[csf("booking_no")]]["type"]="Main";
        }
       else if($v[csf("booking_type")]==2)
        {
            
             $booking_data_arr[$v[csf("booking_no")]]["type"]="Trims";
        }

         else if($v[csf("booking_type")]==3)
        {
            
             $booking_data_arr[$v[csf("booking_no")]]["type"]="Service";
        }

         else if($v[csf("booking_type")]==4)
        {
            
             $booking_data_arr[$v[csf("booking_no")]]["type"]="Sample";
        }

         else if($v[csf("booking_type")]==5)
        {
            
             $booking_data_arr[$v[csf("booking_no")]]["type"]="Trims Sample";
        }

         else if($v[csf("booking_type")]==6)
        {
            
             $booking_data_arr[$v[csf("booking_no")]]["type"]="Embellishment sample";
        }
          else if($v[csf("booking_type")]==7)
        {
            
             $booking_data_arr[$v[csf("booking_no")]]["type"]="Dia";
        }
         $booking_data_arr[$v[csf("booking_no")]]["buyer_id"]=$v[csf("buyer_id")];

    }
    
    //echo $all_booking_nos;die;
   // echo "<pre>";print_r($booking_data_arr);die;
   // echo $main_query;
    ob_start();
    if($operation==0)
    {


     ?>
  
          <div style="width:4320px;">
          <style type="text/css">
              .alignment_css
              {
                word-break: break-all;
                word-wrap: break-word;
              }
          </style>
            <fieldset style="width:3920px;">
                 
                <table class="rpt_table" width="3920" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <caption><b> Fabric Finishing Report SubCon Urmi<br>
               <? echo  change_date_format($txt_date_from).' To '. change_date_format($txt_date_to); ?>
               </b>
                </caption>
                    <thead>
                        <tr>
                            <th width="80" class="alignment_css">SL</th> 
                            <th width="80" class="alignment_css">Source</th> 
                            <th width="80" class="alignment_css">Working <br>Company</th> 
                            <th width="80" class="alignment_css">Floor</th> 
                            <th width="80" class="alignment_css">M/C No</th> 
                            <th width="80" class="alignment_css">Shift</th> 
                            <th width="80" class="alignment_css">Production <br> Date</th> 
                            <th width="80" class="alignment_css">Buyer</th> 
                            <th width="80" class="alignment_css">Style Ref.</th> 
                            <th width="80" class="alignment_css">Job</th> 
                            <th width="80" class="alignment_css">Order No</th> 
                            <th width="80" class="alignment_css">Batch No</th> 
                            <th width="80" class="alignment_css">Extn. No</th> 
                            <th width="80" class="alignment_css">Fabric Type</th> 
                            <th width="80" class="alignment_css">Fabric <br>Composition</th> 
                            <th width="80" class="alignment_css">GSM</th> 
                            <th width="80" class="alignment_css">DIA</th> 
                            <th width="80" class="alignment_css">Dia/Width<br> Type</th> 
                            <th width="80" class="alignment_css">Color Name</th> 
                            <th width="80" class="alignment_css">Color Range</th> 
                            <th width="80" class="alignment_css">Batch Qty.</th> 
                            <th width="80" class="alignment_css">Prod. Qty.</th> 
                            <th width="80" class="alignment_css">Ext Prod.<br> qty.</th> 
                            <th width="80" class="alignment_css">Re-Work <br>Prod.  Qty.</th> 
                            <th width="80" class="alignment_css">After Brush</th> 
                            <th width="80" class="alignment_css">After Peach</th> 
                            <th width="80" class="alignment_css">Chemical <br>Finish</th> 
                            <th width="80" class="alignment_css">After AOP</th> 
                            <th width="80" class="alignment_css">Reprocess No</th> 
                            <th width="80" class="alignment_css">No of Roll</th> 
                            <th width="80" class="alignment_css">Fin. Start<br> Date</th> 
                            <th width="80" class="alignment_css">Fin. Start <br>Time</th> 
                            <th width="80" class="alignment_css">Fin. End<br> Date</th> 
                            <th width="80" class="alignment_css">Fin. End<br> Time</th> 
                            <th width="80" class="alignment_css">Time Used</th> 
                            <th width="80" class="alignment_css">Dyeing  <br>Unload Date</th> 
                            <th width="80" class="alignment_css">Dyeing  <br>Unload Time</th> 
                            <th width="80" class="alignment_css">Execution<br> Days</th> 
                            <th width="80" class="alignment_css">Execution<br> Time</th> 
                            <th width="80" class="alignment_css">Remarks</th> 
                            <th width="80" class="alignment_css">Chemical <br>Name</th> 
                            <th width="80" class="alignment_css">Temperature</th> 
                            <th width="80" class="alignment_css">Stretch</th> 
                            <th width="80" class="alignment_css">Over Feed</th> 
                            <th width="80" class="alignment_css">Feed-In</th> 
                            <th width="80" class="alignment_css">Pinning</th> 
                            <th width="80" class="alignment_css">Speed<br>(M/Min)</th> 
                            <th width="80" class="alignment_css">Length<br>Shrinkage%</th> 
                            <th width="80" class="alignment_css">Width<br>Shrinkage%</th> 
                             
                        </tr>
                    </thead>
                </table>
                <div style=" max-height:380px; width:3940px; overflow-y:scroll;" id="scroll_body">
                    <table class="rpt_table" id="table_body" width="3920" cellpadding="0" cellspacing="0" border="1" rules="all" align="">
                     <tbody>
                    <?
					//print_r($prod_qty_arr);
					  $ii=0;
                     $ii=1;   

					 foreach($sub_main_array as $entry_form_id=>$machine_data)
                     {
                        $entry_form_batch_qty=0;
                        $entry_form_production_qty=0;
                        $entry_form_re_work_production_qty=0;
                        $entry_form_batch_qty_ex=0;
                        $entry_form_production_qty_ex=0;
                        $entry_form_brush_batch_qty=0;
                        $entry_form_peach_production_qty=0;
                        $entry_form_chemical_batch_qty_ex=0;
                        $entry_form_aop_production_qty_ex=0;
						//echo $entry_form.'D';

                        ?>
                        <tr>
                            <th colspan="49" align="left">
                                <strong>
                                
								
								<?
								if($ii==1) echo 'In-Bound Subcontract<br>';else echo "";
                                   if($entry_form_id==35 || $entry_form_id==67 || $entry_form_id==68   || $entry_form_id==194)
                                   {
                                        echo $conversion_cost_head_array[$entry_form_id];
                                   } 

                                   else 
                                   {
                                       echo $process_format[$entry_form_id];
                                   }
                                   ?>

                               </strong>
                                    </th>
                        </tr>

                        <?
						$kk=1;
                        foreach($machine_data as $machine_id=>$job_data)
                        { 
						
                            $machine_batch_qty=0;
                            $machine_production_qty=0;
                            $machine_batch_qty_ex=0;
                            $machine_production_qty_ex=0;
                            $machine_re_work_production_qty=0;

                            $machine_brush_batch_qty=0;
                            $machine_peach_production_qty=0;
                            $machine_chemical_batch_qty_ex=0;
                            $machine_aop_production_qty_ex=0;

                                
								   foreach($job_data as $job_no=>$body_data)
                                   { 
								      $p=1; 
								    foreach($body_data as $prod_id=>$color_data)
                                    {  
                                       
                                            foreach($color_data as $color_id=>$batch_data)
                                            {
                                                foreach($batch_data as $batch_id=>$batch_ext_no_data)
                                                {
                                                    foreach($batch_ext_no_data as $ext_no=>$re_stenter_no_data )
                                                    {
                                                        foreach($re_stenter_no_data as $re_stenter_no=>$service_company_data)
                                                        {
                                                           
															foreach($service_company_data as $service_company=>$service_source_data)
                                                            {
                                                                
															    foreach($service_source_data as $service_source=>$production_date_data)
                                                                {
                                                                   // echo $machine_id.'b,'; 
																	foreach($production_date_data as $production_date=>$row)
                                                                    {
                                                                      
																	  // echo $body_part_id."<br>";
                                                                       					//echo $prod_qty.'d';
                                                                        $batch_qty=0;
                                                                        $production_qty=0;
                                                                        $production_qty_ex=0;
                                                                        $re_work_production_qty=0;
                                                                        $brush_qty=0;
                                                                        $peach_qty=0;
                                                                        $chemical_qty=0;
                                                                        $aop_qty=0; $prod_qty=0;

                                                                        $batch_qty=$row["batch_qty"];
                                                                        if($re_stenter_no==0 || $re_stenter_no=="")
                                                                        {
                                                                            //if($p==1)
																			 //{
																				 if($entry_form_id==30)
																				 {
																		    $prod_qty=$prod_qty_arr[$entry_form_id][$machine_id][$job_no][$prod_id][$color_id][$batch_id][$ext_no][$re_stenter_no][$service_company][$service_source]["production_qty"];
																			   $prod_no_of_roll=$prod_qty_arr[$entry_form_id][$machine_id][$prod_id][$job_no][$color_id][$batch_id][$ext_no][$re_stenter_no][$service_company][$service_source]["no_of_roll"];
																				 }
																				 else
																				 {
																				$prod_qty=$prod_qty_arr[$entry_form_id][$machine_id][$job_no][$prod_id][$color_id][$batch_id][$ext_no][$re_stenter_no][$service_company][$service_source][$production_date]["production_qty"];
																				$prod_no_of_roll=$prod_qty_arr[$entry_form_id][$machine_id][$job_no][$prod_id][$color_id][$batch_id][$ext_no][$re_stenter_no][$service_company][$service_source][$production_date]["no_of_roll"];
																				 }
																			// }
																			$production_qty=$prod_qty;//$row["production_qty"];

                                                                        }
																		//echo $re_stenter_no.'='.$production_qty.'<br>';
                                                                        if($row["re_stenter_no"]>0)
                                                                        {
                                                                           $production_qty=0;
                                                                           $re_work_production_qty=$row["production_qty"];                                                                  

                                                                        }
                                                                        if($ext_no)
                                                                        {

                                                                           $production_qty_ex=$row["production_qty"]; 
                                                                           $production_qty=0;
                                                                           $re_work_production_qty=0;
                                                                        }
                                                                        if($row["previous_process"]==1)
                                                                        {
                                                                           $brush_qty=$row["production_qty"];
                                                                           $production_qty=0;
                                                                           $re_work_production_qty=0;
                                                                           $production_qty_ex=0;
                                                                        }
                                                                        if($row["previous_process"]==2)
                                                                        {
                                                                           $peach_qty=$row["production_qty"];
                                                                           $production_qty=0;
                                                                           $re_work_production_qty=0;
                                                                           $production_qty_ex=0;
                                                                           $brush_qty=0;
                                                                        }

                                                                        if($row["previous_process"]==3)
                                                                        {
                                                                           $chemical_qty=$row["production_qty"];
                                                                           $production_qty=0;
                                                                           $re_work_production_qty=0;
                                                                           $production_qty_ex=0;
                                                                           $brush_qty=0;
                                                                           $peach_qty=0;
                                                                        }
                                                                        if($row["previous_process"]==4)
                                                                        {
                                                                           $aop_qty=$row["production_qty"];
                                                                           $production_qty=0;
                                                                           $re_work_production_qty=0;
                                                                           $production_qty_ex=0;
                                                                           $brush_qty=0;
                                                                           $peach_qty=0;
                                                                           $chemical_qty=0;
                                                                        } 

                                                                        $machine_batch_qty+= $batch_qty;
                                                                        $machine_production_qty+=$production_qty;
                                                                        $machine_re_work_production_qty+=$re_work_production_qty;
                                                                        $machine_batch_qty_ex+=$production_qty_ex;
                                                                        $machine_production_qty_ex+=$production_qty_ex;

                                                                        $machine_brush_batch_qty+= $brush_qty;
                                                                        $machine_peach_production_qty+=$peach_qty;
                                                                        $machine_chemical_batch_qty_ex+=$chemical_qty;
                                                                        $machine_aop_production_qty_ex+=$aop_qty;


                                                                        $entry_form_batch_qty+= $batch_qty;
                                                                        $entry_form_production_qty+=$production_qty;
                                                                        $entry_form_re_work_production_qty+=$re_work_production_qty;
                                                                        $entry_form_batch_qty_ex+=$production_qty_ex;
                                                                        $entry_form_production_qty_ex+=$production_qty_ex;

                                                                        $entry_form_brush_batch_qty+= $brush_qty;
                                                                        $entry_form_peach_production_qty+=$peach_qty;
                                                                        $entry_form_chemical_batch_qty_ex+=$chemical_qty;
                                                                        $entry_form_aop_production_qty_ex+=$aop_qty;

                                                                     $body_prod_rowspan=$sub_prod_rowspan_arr[$entry_form_id][$machine_id][$job_no][$color_id][$batch_id][$ext_no][$service_company][$service_source];
																	 //echo  $body_prod_rowspan.'B,<br>';
																	    if($batch_qty ||$brush_qty || $peach_qty || $chemical_qty || $aop_qty || $production_qty ||  $batch_qty_ex ||  $production_qty_ex || $re_work_production_qty || 1==1  )
                                                                        {
                                                                            if ($kk%2==0)  
                                                                                $bgcolor="#E9F3FF";
                                                                            else
                                                                                $bgcolor="#FFFFFF";                                                             
																			//echo $entry_form_id.'m';
                                                                            ?>

                                                                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trsub_<? echo $kk.$entry_form_id; ?>','<? echo $bgcolor;?>')" id="trsub_<? echo $kk.$entry_form_id; ?>">
                                                                             <td width="80" class="alignment_css"><? echo $kk++ ;?></td>
                                                                             <td width="80" class="alignment_css"><? echo $knitting_source[$service_source] ;?> </td>
                                                                             <td width="80" class="alignment_css"><? echo $company_library[$service_company];?> </td>
                                                                             <td width="80" class="alignment_css"><? echo $floor_arr[$row["floor_id"]];?></td>
                                                                             <td width="80" class="alignment_css"><? echo $machine_arr[$machine_id];?></td>
                                                                             <td width="80" class="alignment_css"><? echo $shift_name[$row["shift_name"]];?></td>
                                                                             <td width="80" class="alignment_css"><? echo change_date_format($production_date);?> </td>
                                                                             <td width="80" class="alignment_css"><? echo $buyer_arr[$row["buyer_id"]];?></td>
                                                                             <td width="80" class="alignment_css"><? echo $row["style_ref_no"];?></td>
                                                                             <td width="80" class="alignment_css"><? echo $job_no; //else echo $job_no;?></td>
                                                                           
                                                                             <td width="80" title="SubCon PO No" class="alignment_css"><? echo $row["sales_booking_no"];?></td>
                                                                           
                                                                             <td width="80" class="alignment_css"><? echo $row["batch_no"];?></td>
                                                                             <td width="80" class="alignment_css"><? echo $ext_no;?></td>
                                                                            
                                                                             <td width="80" class="alignment_css"><?
                                                                                 $com_data=array_unique( explode(",",$row["fabric_type"])) ;
                                                                                 $com_data_desc=array_unique( explode(",",$row["fabric_desc"])) ;
                                                                                 echo implode(",",$com_data);?> </td>
                                                                                 <td width="80" class="alignment_css"><? echo implode(",",$com_data_desc);?> </td>
                                                                                 <td width="80" class="alignment_css"><? echo implode(",",array_unique( explode(",", $row["gsm_weight"])));   ?> </td>
                                                                                 <td width="80" class="alignment_css"><? echo implode(",",array_unique( explode(",", $row["dia"])));  ;?> </td>
                                                                                 <td width="80" class="alignment_css"><? echo implode(",",array_unique( explode(",", $row["width_dia_type"])));  ;?> </td>
                                                                                  
                                                                                 <td width="80" class="alignment_css"><? echo $color_library[$color_id];?> </td>
                                                                                 <td width="80" class="alignment_css"><? echo $color_range[$row["color_range_id"]];?> </td>
                                                                                 <td width="80" class="alignment_css"><? echo number_format($batch_qty,2);?></td>
                                                                                 <?
																				 //if($entry_form_id!=32)
																				// {
                                                                               //  if($p==1)
																				// {
																				 ?>
                                                                                 <td width="80" class="alignment_css" rowspan="<? //echo  $body_prod_rowspan;?>"><? echo number_format($production_qty,2);?></td>
                                                                                 <?
																				  //}
																				 //}
																				 //else
																				 //{
																				
																				 ?>
                                                                                <!-- <td width="80" class="alignment_css"><?echo number_format($production_qty,2);?></td>-->
                                                                                 <?
																				//}
																				  
                                                                                 ?>
                                                                                 <td width="80" class="alignment_css"><? echo number_format($production_qty_ex,2);?> </td>
                                                                                 <td width="80" class="alignment_css"><? echo number_format($re_work_production_qty,2);?> </td>
                                                                                 <td width="80" class="alignment_css"><? echo number_format($brush_qty,2); ?></td>
                                                                                 <td width="80" class="alignment_css"><? echo number_format($peach_qty,2) ?></td>
                                                                                 <td width="80" class="alignment_css"><? echo number_format( $chemical_qty,2); ?></td>
                                                                                 <td width="80" class="alignment_css"><? echo number_format($aop_qty,2); ?></td>
                                                                                 <td width="80" class="alignment_css"><?  echo $row["re_process1"];    ?></td>
                                                                                 <td width="80" class="alignment_css" title="No of Roll=<? echo $prod_no_of_roll;?>"><? echo $roll_count2[$entry_form_id][change_date_format($production_date)][$batch_id][$ext_no][$row["re_stenter_no"]][$body_part_id];?> </td>
                                                                                 <td width="80" class="alignment_css"><? echo change_date_format($row["process_start_date"]);?> </td>
                                                                                 <td width="80" class="alignment_css"><? echo $row["process_start_time"];?> </td>
                                                                                 <td width="80" class="alignment_css"><? echo change_date_format($row["process_end_date"]);?> </td>
                                                                                 <td width="80" class="alignment_css"><? echo $row["process_end_time"];?> </td>
                                                                                 <td width="80" class="alignment_css"> 
                                                                                     <? 
                                                                                     $dyeing_date=$dyeing_data[$batch_id][$ext_no]["date"];
                                                                                     $dyeing_time=$dyeing_data[$batch_id][$ext_no]["time"];
                                                                                     $end_time=$batch[csf('end_hours')].':'.$batch[csf('end_minutes')];
                                                                                     $start_time=$batch[csf('start_hours')].':'.$batch[csf('start_minutes')];

                                                                                     $new_date_time_start=($row["process_start_date"].' '.$row["process_start_time"].':'.'00');
                                                                                     $new_date_time_end=($row["process_end_date"].' '.$row["process_end_time"].':'.'00');
                                                                                    $new_date_time_end2=$dyeing_date.' '.$dyeing_time.':'.'00';

                                                                                     $total_time=datediff(n,$new_date_time_end ,$new_date_time_start);
                                                                                     $total_time_ex=datediff(n,$new_date_time_end2 ,$new_date_time_start);
                                                                                    echo  abs ( floor(abs($total_time/60)))." H :".abs($total_time%60)." M ";                                                                                      
                                                                                    $exe_days=   round($total_time/60/24);
                                                                                    
                                                                                    ?></td>
                                                                                    <td width="80" class="alignment_css"><? echo change_date_format($dyeing_date)  ;?></td>
                                                                                    <td width="80" class="alignment_css"><? echo $dyeing_time ;?></td>
                                                                                    
                                                                                    <td width="80" class="alignment_css"><? echo  abs($exe_days);?></td>
                                                                                    <td width="80" title="Unload date time and Prod Date Time" class="alignment_css"><?  if($dyeing_date!="") echo  abs ( floor( abs($total_time_ex/60)) )." H :".abs($total_time_ex%60)." M ";else echo " ";  ?></td>
                                                                                    <td width="80" class="alignment_css"><? echo $row["remarks"];?></td>
                                                                                    <td width="80" class="alignment_css"><? echo $row["chemical_name"];?></td>
                                                                                    <td width="80" class="alignment_css"><? echo $row["temparature"];?></td>
                                                                                    <td width="80" class="alignment_css"><? echo $row["stretch"];?> </td>
                                                                                    <td width="80" class="alignment_css"><? echo $row["over_feed"];?> </td>
                                                                                    <td width="80" class="alignment_css"><? echo $row["feed_in"];?> </td>
                                                                                    <td width="80" class="alignment_css"><? echo $row["pinning"];?></td>
                                                                                    <td width="80" class="alignment_css"><? echo $row["speed_min"];?></td>
                                                                                    <td width="80" class="alignment_css"><? echo $row["length_shrinkage"];?>%</td>
                                                                                    <td width="80" class="alignment_css"><? echo $row["width_shrinkage"];?>% </td>

                                                                                </tr>
                                                                                <?
																				$p++;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
												  }
                                           }
                                    }
                                }
                                ?>
                                    <tr style="background-color:whitesmoke; font-size:12px; cursor:pointer;" onClick="change_color('tr2_<? echo $ii; ?>','<? echo $bgcolor; ?>')" id="tr2_<? echo $ii; ?>"  >
                                     <td width="1600" colspan="20" class="alignment_css" align="right"><strong><? echo  $machine_arr[$machine_id];?> Total=</strong></td>
                                      
                                     <td width="80" class="alignment_css"><? echo number_format($machine_batch_qty);?></td>
                                     <td width="80" class="alignment_css"><? echo number_format($machine_production_qty);?></td>
                                     <td width="80" class="alignment_css"><? echo number_format($machine_batch_qty_ex);?> </td>
                                     <td width="80" class="alignment_css"><? echo number_format($machine_re_work_production_qty);?> </td>
                                      <td width="80" class="alignment_css"><? echo number_format($machine_brush_batch_qty);?></td>
                                     <td width="80" class="alignment_css"><? echo number_format($machine_peach_production_qty);?></td>
                                     <td width="80" class="alignment_css"><? echo number_format($machine_chemical_batch_qty_ex);?> </td>
                                     <td width="80" class="alignment_css"><? echo number_format($machine_aop_production_qty_ex);?> </td>
                                      
                                     <td colspan="21" width="1600" class="alignment_css"> </td>

                                 </tr>

                                <?
                            }

                             ?>
                                    <tr style="background-color:whitesmoke;font-size:12px; cursor:pointer; " onClick="change_color('tr3_<? echo $ii; ?>','<? echo $bgcolor; ?>')" id="tr3_<? echo $ii; ?>"  >
                                     <td width="1600" colspan="20" class="alignment_css" align="right"><strong><? echo  $process_format[$entry_form_id];?> Total=</strong></td>
                                      
                                     <td width="80" class="alignment_css"><? echo number_format($entry_form_batch_qty);?></td>
                                     <td width="80" class="alignment_css"><? echo number_format($entry_form_production_qty);?></td>
                                     <td width="80" class="alignment_css"><? echo number_format($entry_form_batch_qty_ex);?> </td>
                                     <td width="80" class="alignment_css"><? echo number_format($entry_form_re_work_production_qty);?> </td>

                                     <td width="80" class="alignment_css"><? echo number_format($entry_form_brush_batch_qty);?></td>
                                     <td width="80" class="alignment_css"><? echo number_format($entry_form_peach_production_qty);?></td>
                                     <td width="80" class="alignment_css"><? echo number_format($entry_form_chemical_batch_qty_ex);?> </td>
                                     <td width="80" class="alignment_css"><? echo number_format($entry_form_aop_production_qty_ex);?> </td>
                                      
                                     <td colspan="21" width="1600" class="alignment_css"> </td>
                                      

                                 </tr>
                                 <?
                        }
                    
                    ?>
                        


                     </tbody>
                    </table>
                </div>
        </fieldset>
        </div>



        <? 
    }
    else if($operation==1)
    {
        $dtls_width=200+(count($head_arr)*100);
        $dtls_width2=220+(count($head_arr)*100);


        ?>
  
        <div style="width:1220px;">
        <br> <br>
            <style type="text/css">
                .alignment_css
                {
                    word-break: break-all;
                    word-wrap: break-word;
                }
            </style>
            <fieldset style="width:1220px;">


                <table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                 <caption><b> Fabric Finishing Report SubCon Urmi<br>
               <? echo  change_date_format($txt_date_from).' To '. change_date_format($txt_date_to); ?>
               </b>
                </caption>
                    <thead>
                        <tr>
                            <th width="50" class="alignment_css">SL No</th> 
                            <th width="150" class="alignment_css">Process Name</th> 
                            <th width="100" class="alignment_css">Batch Qty.</th> 
                            <th width="100" class="alignment_css">Prod. Qty.</th> 
                            <th width="100" class="alignment_css">Ext Production <br>Qty.</th> 
                            <th width="100" class="alignment_css">Re-Work <br>Production Qty.</th> 
                            <th width="100" class="alignment_css">After Brush</th> 
                            <th width="100" class="alignment_css">After Peach</th> 
                            <th width="100" class="alignment_css">After AOP</th> 
                            <th width="100" class="alignment_css">Chemical Finish</th> 
                            <th width="100" class="alignment_css">No of Batch</th> 
                            <th width="100" class="alignment_css">No of Roll</th> 


                        </tr>
                    </thead>
                </table>
                <div style=" max-height:380px; width:1220px; overflow-y:scroll;" id="scroll_body">
                    <table class="rpt_table" id="table_body" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all" align="">
                        <tbody>
                            <?
                            $ii=1;  
                            $gr_batch_qty=0; 
                            $gr_ex_batch_qty=0; 
                            $gr_prod_qty=0;
                            $gr_rew_prod_qty=0;
                            $gr_brush=0;
                            $gr_peach=0;
                            $gr_aop=0;
                            $gr_chemical=0;

                            foreach($head_arr as $entry_form_id=>$row)
                            {


                                ?>
                                <tr>
                                    <td width="50" align="center"><? echo $ii++;?></td>
                                    <td width="150" align="center">
                                        <strong><?
                                            if($entry_form_id==35 || $entry_form_id==67 || $entry_form_id==68   || $entry_form_id==194)
                                            {
                                                echo $conversion_cost_head_array[$entry_form_id];
                                            } 

                                            else 
                                            {
                                                echo $process_format[$entry_form_id];
                                            }
                                            ?>

                                        </strong>
                                    </td>
                                      

                                    <td width="100" align="center"><? echo $batch_qty=number_format($summary_arr[$entry_form_id]["batch_qty"],2);?></td>
                                    <td width="100" align="center"><? echo $production_qty=number_format($summary_arr[$entry_form_id]["production_qty"],2);?></td>
                                    <td width="100" align="center"><? echo $ext_production_qty=number_format($summary_arr[$entry_form_id]["ext_production_qty"],2);?></td>
                                    <td width="100" align="center"><? echo $re_work_production_qty=number_format($summary_arr[$entry_form_id]["re_work_production_qty"],2);?></td>
                                    <td width="100" align="center"><? echo $brush_qty=number_format($summary_arr[$entry_form_id]["brush_qty"],2);?></td>
                                    <td width="100" align="center"><? echo $peach_qty=number_format($summary_arr[$entry_form_id]["peach_qty"],2);?></td>
                                    <td width="100" align="center"><? echo $aop_qty=number_format($summary_arr[$entry_form_id]["aop_qty"],2);?></td>
                                    <td width="100" align="center"><? echo $chemical_qty=number_format($summary_arr[$entry_form_id]["chemical_qty"],2);?></td>
                                    <td width="100" align="center"><? echo $summary_arr[$entry_form_id]["no_of_batch"] ;?></td>
                                    <td width="100" align="center"><? echo $summary_arr[$entry_form_id]["no_of_roll"] ;?></td>
                                     
                                </tr>

                                <?
                                $gr_batch_qty+=str_replace(",","",$batch_qty); 
                                $gr_ex_batch_qty+=str_replace(",","",$ext_production_qty); 
                                $gr_prod_qty+=str_replace(",","",$production_qty);
                                $gr_rew_prod_qty+=str_replace(",","",$re_work_production_qty);
                                $gr_brush+=str_replace(",","",$brush_qty);
                                $gr_peach+=str_replace(",","",$peach_qty);
                                $gr_aop+=str_replace(",","",$aop_qty);
                                $gr_chemical+=str_replace(",","",$chemical_qty);
                            }

                            ?>

                            <tr>
                                    <td width="50" align="center"> </td>
                                    <td width="150" align="right"><strong>All M/C Total=</strong></td>
                                      

                                    <td width="100" align="center"> <? echo number_format($gr_batch_qty,2);?></td>
                                    <td width="100" align="center"> <? echo number_format($gr_prod_qty,2);?></td>
                                    <td width="100" align="center"> <? echo number_format($gr_ex_batch_qty,2);?></td>
                                    <td width="100" align="center"> <? echo number_format($gr_rew_prod_qty,2);?></td>
                                    <td width="100" align="center"> <? echo number_format($gr_brush,2);?></td>
                                    <td width="100" align="center"> <? echo number_format($gr_peach,2);?></td>
                                    <td width="100" align="center"> <? echo number_format($gr_aop,2);?></td>
                                    <td width="100" align="center"> <? echo number_format($gr_chemical,2);?></td>
                                    <td width="100" align="center">&nbsp; </td>
                                    <td width="100" align="center">&nbsp; </td>
                                     
                            </tr>





                        </tbody>
                    </table>
                </div>
            </fieldset>


             <br> <br>
            <fieldset style=" float: left; width:<? echo $dtls_width2;?>px;">


                <table class="rpt_table" width="<? echo $dtls_width ;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th width="150" class="alignment_css">M/C</th> 
                            <th width="50" class="alignment_css">Shift</th> 
                            <?
                                foreach($head_arr as $entry_form_id=>$vv )

                                {
                                    if($entry_form_id==35 || $entry_form_id==67 || $entry_form_id==68   || $entry_form_id==194)
                                   {
                                       
                                        echo "<th width='100' class='alignment_css'>$conversion_cost_head_array[$entry_form_id]</th> ";
                                   } 

                                   else 
                                   {
                                    echo "<th width='100' class='alignment_css'>$process_format[$entry_form_id]</th> ";

                                       
                                   }
                                }
                            ?>
                            


                        </tr>
                    </thead>
                </table>
                <div style=" max-height:500px; width:<? echo $dtls_width2;?>px; overflow-y:scroll;" id="scroll_body">
                    <table class="rpt_table" id="table_body" width="<? echo $dtls_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                        <tbody>
                            <?
                                

                            foreach($dtls_arr_head as $machine_id=>$shift_data)
                            {
                                foreach($shift_data as $shift_id=>$row)
                                {



                                    ?>
                                    <tr>
                                        <td width="150" align="center"><? echo $machine_arr[$machine_id];?></td>
                                        <td width="50" align="center"><? echo $shift_name[$shift_id];?></td> 
                                        <?
                                        foreach($head_arr as $entry_form_id=>$vv )

                                        {
                                            $vals=$dtls_arr_qnty[$machine_id][$shift_id][$entry_form_id];
                                            ?>
                                            <td width="100" align="center"><? echo   number_format($vals,2);?></td> 

                                            <?
                                            $process_total_arr[$entry_form_id]+=$vals;
                                        }
                                        ?>


                                    </tr>

                                    <?
                                }
                            }

                            ?>

                            <tr>
                                        <td width="150" align="center"> </td>
                                        <td width="50" align="right"><strong>Total=</strong></td> 
                                        <?
                                        foreach($head_arr as $entry_form_id=>$vv )

                                        {
                                            ?>
                                            <td width="100" align="center"><? echo  number_format($process_total_arr[$entry_form_id],2);?></td> 

                                            <?
                                           
                                        }
                                        ?>


                            </tr>



                        </tbody>
                    </table>
                </div>
            </fieldset>



        </div>



        <? 
    }

	
	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
//---------end------------//
	$filename=time().".xls";
	$create_new_doc = fopen($filename, 'w');
	$fdata=ob_get_contents();
	fwrite($create_new_doc,$fdata);
	ob_end_clean();
	echo "$fdata****$filename****$operation";
exit();

    }
?>