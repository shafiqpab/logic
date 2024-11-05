<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$buyer_list=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
//--------------------------------------------------------------------------------------------------------------------

if($action=="load_drop_down_location")
{ 
    echo create_drop_down( "cbo_buyer_id", 140, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and   b.tag_company='$data' and a.status_active=1 and a.is_deleted=0 order by a.short_name","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
	exit();
}

if($action=="batch_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script type="text/javascript">
        function js_set_value(id)
        { 
            document.getElementById('selected_id').value=id;
            parent.emailwindow.hide();
        }
    </script>
    <input type="hidden" id="selected_id" name="selected_id" /> 
<?
	if($db_type==0) $group_by_cond="GROUP BY a.batch_no, a.extention_no"; 
	else if($db_type==2) $group_by_cond=" GROUP BY a.id, a.batch_no, a.extention_no, a.batch_no, a.booking_no, a.color_id, a.batch_weight order by a.batch_no, a.extention_no desc";

	$sql="select a.id, a.batch_no, a.extention_no, a.booking_no, a.color_id, a.batch_weight from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 $group_by_cond ";	

	$arr=array(2=>$color_library);
	
	echo  create_list_view("list_view", "Batch no,Ext,Color,Booking no,Batch weight ", "100,70,100,100,100","520","350",0, $sql, "js_set_value", "id,batch_no,extention_no", "", 1, "0,0,color_id,0,0", $arr , "batch_no,extention_no,color_id,booking_no,batch_weight", "subcon_batch_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,2') ;
	exit();
}


if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
    </script>
    <input type="hidden" id="selected_id" name="selected_id" /> 
	<?
    if($db_type==0) $year_field="year(a.insert_date) as year"; 
    else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
    else $year_field="";
    
    $year_job = str_replace("'","",$year);
    if(trim($year)!=0) $year_cond=" $year_field_by=$year_job"; else $year_cond="";
    if($cbo_buyer_name==0) $buyer_cond=""; else $buyer_cond=" and a.party_id='$cbo_buyer_name'";
    
    $buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	
    $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.order_no, b.cust_style_ref, b.main_process_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' $buyer_cond order by a.id desc";	
	
    ?>
    <table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="80">Job no</th>
            <th width="70">Year</th>
            <th width="130">Buyer</th>
            <th width="110">Style</th>
            <th width="120">Po number</th>
       </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
    <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
     <? $data_array=sql_select($sql);
        $i=1;
		 foreach($data_array as $row)
		 {
			 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 ?>
			<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value('<? echo $row[csf('job_no_prefix_num')]; ?>')" style="cursor:pointer;">
				<td width="30"><? echo $i; ?></td>
				<td align="center"  width="80"><? echo $row[csf('job_no_prefix_num')]; ?></td>
				<td align="center" width="70"><? echo $row[csf('year')]; ?></td>
				<td width="130"><? echo $buyer[$row[csf('party_id')]]; ?></td>
				<td width="110"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
				<td width="120"><p><? echo $row[csf('order_no')]; ?></p></td>
			</tr>
			<? $i++; 
			} 
		?>
    </table>
    </div>
	<script> setFilterGrid("table_body2",-1); </script>
    <?
	disconnect($con);
	exit();
}


if($action=="order_number_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
        function js_set_value(id)
        { 
            document.getElementById('selected_id').value=id;
            parent.emailwindow.hide();
        }
    </script>
    <input type="hidden" id="selected_id" name="selected_id" /> 
<?
	$buyer = str_replace("'","",$buyer_name);
	$year = str_replace("'","",$year);
	$job_no = str_replace("'","",$job_number);
	if($db_type==0) $year_field="year(b.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY') as year";
	else $year_field="";
	
	if ($buyer==0) $buyername_cond=""; else $buyername_cond=" and a.party_id=$buyer";
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num=$job_no";
	
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

    $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.id, b.order_no, b.cust_style_ref, b.main_process_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_name' $buyername_cond $job_no_cond order by a.id desc";	

?>
    <table width="370" border="1" rules="all" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="80">Job no</th>
            <th width="70">Year</th>
            <th width="130">Buyer</th>
            <th width="110">Style</th>
            <th width="120">Po number</th>
       </thead>
    </table>
    <div style="max-height:380px; overflow:auto;">
    <table id="table_body2" width="370" border="1" rules="all" class="rpt_table">
     <? $data_array=sql_select($sql);
            $i=1;
             foreach($data_array as $row)
             {
                 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             ?>
                <tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value('<? echo $row[csf('id')].'_'.$row[csf('order_no')]; ?>')" style="cursor:pointer;">
                    <td width="30"><? echo $i; ?></td>
                    <td align="center"  width="80"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td align="center" width="70"><? echo $row[csf('year')]; ?></td>
                    <td width="130"><? echo $buyer[$row[csf('party_id')]]; ?></td>
                    <td width="110"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
                    <td width="120"><p><? echo $row[csf('order_no')]; ?></p></td>
                </tr>
                <? $i++; 
                } 
                ?>
        </table>
    </div>
    <script> setFilterGrid("table_body2",-1); </script>
    <?
	exit();
}

if($action=="batchextensionpopup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

?>
	<script type="text/javascript">
        function js_set_value(id)
        { 
            document.getElementById('selected_id').value=id;
            parent.emailwindow.hide();
        }
    </script>
    <input type="hidden" id="selected_id" name="selected_id" /> 
<?
$buyer = str_replace("'","",$buyer_name);
$year = str_replace("'","",$year);
$buyer = str_replace("'","",$buyer_name);
$batch_number= str_replace("'","",$batch_number_show);
if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
else $year_field_by="";

if ($company_name==0) $company=""; else $company=" and a.company_id=$company_name";
if ($batch_number==0) $batch_no=""; else $batch_no=" and a.batch_no=$batch_number";

if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";

//echo $buyer;die;
if ($buyer==0) $buyername=""; else $buyername=" and b.buyer_name=$buyer";

 $sql="select a.id,a.batch_no,a.extention_no,a.batch_for,a.booking_no,a.color_id,a.batch_weight from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.is_deleted=0 $company $batch_no ";	

$arr=array(2=>$color_library);
	
	echo  create_list_view("list_view", "Batch no,Extention No,Color,Booking no, Batch for,Batch weight ", "100,70,100,100,100,170","620","350",0, $sql, "js_set_value", "extention_no,extention_no", "", 1, "0,0,color_id,0,0,0", $arr , "batch_no,extention_no,color_id,booking_no,batch_for,batch_weight", "employee_info_controller",'setFilterGrid("list_view",-1);','0') ;
	exit();
}//batchnumbershow;

if($action=="batch_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company = str_replace("'","",$cbo_company_id);
	$buyer = str_replace("'","",$cbo_buyer_id);
	$job_number = str_replace("'","",$hid_job_id);
	$job_number_id = str_replace("'","",$txt_job_no);
	
	$batch_no = str_replace("'","",$txt_batch_no);
	//echo $batch_no;die;
	$batch_number_hidden = str_replace("'","",$hid_batch_id);
	$ext_num = str_replace("'","",$txt_ext_no);
	$hidden_ext = str_replace("'","",$hidden_ext_no);
	$txt_order = str_replace("'","",$txt_order_no);
	$hidden_order = str_replace("'","",$hid_order_id);
	$cbo_type = str_replace("'","",$cbo_type);
	$year = str_replace("'","",$cbo_year);
	//echo $order_no;die;
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$process=33;
	$jobdata=($job_number_id )? " and d.job_no_prefix_num='".$job_number_id ."'" : '';
	$buyerdata=($buyer)?' and d.party_id='.$buyer : '';
	$batch_num=($batch_no)?" and a.batch_no='".$batch_no."'" : '';
	if(trim($ext_no)!="") $ext_no_search="%".trim($ext_no)."%"; else $ext_no_search="%%";
	if ($txt_order=="") $order_no=""; else $order_no="  and c.order_no='$txt_order'";
	//echo $order_no;die;
	if($db_type==0) $year_field_by="and YEAR(a.insert_date)"; 
	else if($db_type==2) $year_field_by=" and to_char(a.insert_date,'YYYY')";
	else $year_field_by="";
	
	if($db_type==0) $find_inset="and  FIND_IN_SET(33,a.process_id)"; 
	else if($db_type==2) $find_inset="and   ',' || a.process_id || ',' LIKE '%33%'";
	

	if ($ext_num=="") $ext_no=""; else $ext_no="  and a.extention_no=$ext_num ";
	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	
	if($txt_date_from && $txt_date_to)
	{
		if($db_type==0)
		{
		$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
		$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
		if($db_type==2)
		{
		$date_from=change_date_format($txt_date_from,'','',1);
		$date_to=change_date_format($txt_date_to,'','',1);
		$dates_com="and a.batch_date BETWEEN '$date_from' AND '$date_to'";
		}
	}
	
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
			
	
$yarn_lot_data=sql_select("select b.order_id, b.cons_comp_id as prod_id,b.gsm,b.yarn_lot as yarn_lot from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.company_id=$company and a.entry_form in(159) and a.status_active=1
 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.yarn_lot!='0' group by b.cons_comp_id, b.gsm,b.order_id,b.yarn_lot");
	foreach($yarn_lot_data as $row)
	{
		$yarn_lot=explode(",",$row[csf('yarn_lot')]);
		$yarn_lot_arr[$row[csf('gsm')]][$row[csf('order_id')]]=implode(",",array_unique($yarn_lot));
	}
		//var_dump($yarn_lot);
			
	$sql_batch_dyeing=sql_select("select batch_id from  pro_fab_subprocess where entry_form=35 and status_active=1 and is_deleted=0");
	$i=1;
	foreach($sql_batch_dyeing as $row_dyeing)
	{
		if($i!==1) $row_d.=",";
		$row_d.=$row_dyeing[csf('batch_id')];
		$i++;
	}
	$sql_batch_h=sql_select("select batch_id from  pro_fab_subprocess where entry_form=32 and status_active=1 and is_deleted=0");
	$i=1;
	foreach($sql_batch_h as $row_h)
	{
		if($i!==1) $row_heat.=",";
		$row_heat.=$row_h[csf('batch_id')];
		$i++;
	}

//###################  Making array chunk for query ######################################

	if($db_type==2 && explode(",", $row_d)>400)
    {
        $row_d_cond=" and (";
        $datasArr=array_chunk(explode(",",$row_d),399);
        foreach($datasArr as $data_ids)
        {
            $data_ids=implode(",",$data_ids);
            $row_d_cond.=" a.id not in($data_ids) or ";
        }
        $row_d_cond=chop($row_d_cond,'or ');
        $row_d_cond.=")";
    }
    else
    {
        $row_d_cond=" and a.id not in ($row_heat)";
    }

	
	if($db_type==0)
	{
		$po_cond="group_concat( distinct c.order_no ) AS po_number";
	}
	else
	{
		$po_cond="listagg(c.order_no,',' ) within group (order by c.order_no) AS po_number";
	}

	if($cbo_type==1)//Date Wise Report
	{
		$sql="SELECT a.id,a.batch_no, a.batch_date,a.batch_weight,a.color_id,a.booking_no_id,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.gsm,b.grey_dia,b.item_description,b.po_id,b.prod_id, b.width_dia_type, $po_cond,c.job_no_mst,d.job_no_prefix_num,d.party_id as buyer_name from pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_dtls c, subcon_ord_mst d where a.company_id=$company $dates_com $jobdata $batch_num $buyerdata $order_no $ext_no $year_cond and a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY a.id,a.batch_no, b.item_description,b.prod_id,a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no,b.po_id, b.prod_id,b.gsm,b.grey_dia, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.party_id order by a.batch_no";
	}
	else if($cbo_type==2) //wait for Heat Setting
	{
		if($row_heat!=0)
		{
		$sql="SELECT a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no_id,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.gsm,b.grey_dia,b.width_dia_type, $po_cond,c.job_no_mst,d.job_no_prefix_num,d.party_id as buyer_name from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_ord_dtls c,subcon_ord_mst d where a.company_id=$company $dates_com $jobdata $batch_num $buyerdata $order_no $ext_no $year_cond and a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2 $find_inset and a.id not in($row_heat) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY a.id,a.batch_no, b.item_description,a.batch_date,b.gsm,b.grey_dia, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.party_id order by a.batch_no ";
		}
	}
	else if($cbo_type==3)// Wait For Dyeing
	{
		if($row_d!=0)
		{
		$sql="SELECT a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no_id,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.gsm,b.grey_dia,b.item_description,b.po_id,b.gsm,b.grey_dia,b.prod_id,b.width_dia_type, $po_cond,c.job_no_mst,d.job_no_prefix_num,d.party_id as buyer_name from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_ord_dtls c, subcon_ord_mst d where a.company_id=$company $dates_com $jobdata $batch_num $buyerdata $order_no $ext_no $year_cond and a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst $row_d_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY a.id,a.batch_no,b.gsm,b.grey_dia, b.item_description,a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no,b.po_id, b.prod_id, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.party_id order by a.batch_no ";
		}
	}
	else if($cbo_type==4) //Wait For Re-Dyeing
	{
		if($row_d!=0)
		{
		$sql="SELECT a.id,a.batch_no,a.batch_date,a.batch_weight,a.color_id,a.booking_no_id,a.extention_no,SUM(b.batch_qnty) AS batch_qnty,b.item_description,b.po_id,b.prod_id,b.gsm,b.grey_dia,b.width_dia_type, $po_cond, c.job_no_mst,d.job_no_prefix_num,d.party_id as buyer_name from pro_batch_create_mst a, pro_batch_create_dtls b,subcon_ord_dtls c,subcon_ord_mst d where a.company_id=$company $dates_com $jobdata $batch_num $buyerdata $order_no $ext_no $year_cond and a.entry_form=36 and a.id=b.mst_id and b.po_id=c.id and d.subcon_job=c.job_no_mst and a.batch_against=2 $row_d_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 GROUP BY a.id,a.batch_no, b.item_description,a.batch_date, a.batch_weight, a.color_id, a.booking_no_id, a.extention_no,b.po_id, b.prod_id,b.gsm,b.grey_dia, b.width_dia_type,c.job_no_mst, d.job_no_prefix_num, d.party_id order by a.batch_no";
		}
	}		

	//echo $sql;
	$batchdata=sql_select($sql);
?>
<div>
<fieldset style="width:1160px;">
<div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> Batch Report </strong> </div>
 <table class="rpt_table" width="1160" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="75">Batch Date</th>
                <th width="60">Batch No</th>
                <th width="40">Ext. No</th>
                <th width="80">Batch Color</th>
                <th width="50">Buyer</th>
                <th width="120">PO No</th>
                <th width="70">Job</th>
                <th width="100">Construction</th>
                <th width="150">Composition</th>
                <th width="50">Dia/ Width</th>
                <th width="50">GSM</th>
                <th width="60">Lot No</th>
                <th width="70">Batch Qty.</th>
                <th width="50">Batch Weight</th>
                <th>Grey Issued</th>
            </tr>
        </thead>
</table>
<div style=" max-height:350px; width:1173px; overflow-y:scroll;" id="scroll_body">
<table class="rpt_table" id="table_body" width="1160" cellpadding="0" cellspacing="0" border="1" rules="all">
<tbody>
<? 
		/*$booking_qnty_arr=array();
		$query=sql_select("select b.po_break_down_id, b.fabric_color_id, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.booking_type=1 and a.is_deleted=0 and a.status_active=1 and b.booking_type=1 and b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id, b.fabric_color_id");
		foreach($query as $row)
		{
			$booking_qnty_arr[$row[csf('po_break_down_id')]][$row[csf('fabric_color_id')]]=$row[csf('grey_fab_qnty')];
		}*/
		
		$storbatch=0;
		$sl=1;
		$i=1;
		$f=0;
		$btq=0;
		$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$batch_chk_arr=array();
		foreach($batchdata as $batch)
		{ 
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$order_id=$batch[csf('po_id')];
		$color_id=$batch[csf('color_id')];
		$booking_qty=$booking_qnty_arr[$order_id][$color_id];
		$desc=explode(",",$batch[csf('item_description')]); 
		$po_number=implode(",",array_unique(explode(",",$batch[csf('po_number')]))); 
?>
            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)" style="cursor:pointer;">
            <? if (!in_array($batch[csf('id')],$batch_chk_arr) )
				{ $f++;
							?>
                <td width="30"><? echo $f; ?></td>
                <td align="center" width="75" title="<? echo change_date_format($batch[csf('batch_date')]); ?>"><p><? echo change_date_format($batch[csf('batch_date')]); ?></p></td>
                <td align="center" width="60" title="<? echo $batch[csf('batch_no')]; ?>"><p><? echo $batch[csf('batch_no')]; ?></p></td>
                <td align="center" width="40" title="<? echo $batch[csf('extention_no')]; ?>"><p><? echo $batch[csf('extention_no')]; ?></p></td>
                <td width="80" title="<? echo $color_library[$batch[csf('color_id')]]; ?>"><p><? echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td width="50" title="<? echo $buyer_arr[$batch[csf('buyer_name')]]; ?>"><p><? echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
				<?	
                $batch_chk_arr[]=$batch[csf('id')];
                $book_qty+=$booking_qty;
                  } 
				else
				  { ?>
                <td width="30"><? //echo $sl; ?></td>
                <td  align="center" width="75"><p><? //echo $batch[csf('batch_date')]; ?></p></td>
                <td  align="center" width="60"><p><? //echo $booking_qty; ?></p></td>
                <td  align="center" width="40"><p><? //echo $batch[csf('extention_no')]; ?></p></td>
                <td  width="80"><p><? //echo $color_library[$batch[csf('color_id')]]; ?></p></td>
                <td  width="50"><p><? //echo $buyer_arr[$batch[csf('buyer_name')]]; ?></p></td>
				<? }
				?>
                <td width="120" title="<? echo $po_number; ?>"><p><? echo $po_number; ?></p></td>
                <td width="70" title="<? echo $batch[csf('job_no_prefix_num')]; ?>"><p><? echo $batch[csf('job_no_prefix_num')]; ?></p></td>
              
                <td width="100" title="<? echo $desc[0]; ?>"><p><? echo $desc[0]; ?></p></td>
                <td width="150" title="<? echo $desc[1]; ?>"><p><? echo $desc[1]; ?></p></td>
                <td align="center" width="50" title="<? echo $desc[3];  ?>"><p><? echo $batch[csf('grey_dia')]; ?></p></td>
                <td align="center" width="50" title="<? echo $desc[2]; ?>"><p><? echo $batch[csf('gsm')]; ?></p></td>
                 <td align="left" width="60" title="<? echo $yarn_lot_arr[$batch[csf('prod_id')]]; ?>"><p><? echo $yarn_lot_arr[$row[csf('gsm')]][$order_id];//$yarn_lot_arr[$batch[csf('prod_id')]][$order_id]; ?></p></td>
                <td align="right" width="70" title="<? echo $batch[csf('batch_qnty')];  ?>"><? echo $batch[csf('batch_qnty')];  ?></td>
                <td  align="right" width="50" title="<? echo $batch[csf('batch_weight')]; ?>"><? echo $batch[csf('batch_weight')]; ?></td>
                <td>&nbsp;</td>
            </tr>
<? 

		$i++;
		$btq+=$batch[csf('batch_qnty')];
		
		$balance=$btq-$book_qty;
		$bal_qty=$balance;
		
		if($bal_qty>0)
		{
		$color="";	
		$txt="Over Batch Qty";
		}
		else if($bal_qty<0)
		{
		$color="red";
		$txt="Below Batch Qty";
		}
		
		} 

 ?>
 
       </tbody>
</table>
<table class="rpt_table" width="1160" cellpadding="0" cellspacing="0" border="1" rules="all">
        <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="75">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="40">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="120">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="150">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="50">&nbsp;</th>
                <th width="60">&nbsp;</th>
                <th width="70" id="batch_td_qty"><? echo $btq; ?></th>
                <th width="50">&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            <tr>
                <td colspan="10" align="right" style="border:none;"><b>Grey Req. Qty </b></td>
                <td colspan="11" align="left">&nbsp; 
                 <? echo number_format($book_qty,2); ?>
                </td>
            </tr>
             <tr>
                <td colspan="10" align="right" style="border:none;"> <b>Batch Qty.</b></td>
                <td colspan="11" align="left" id="batch_td_qty">&nbsp; <? echo number_format($btq,2); ?> </td>
            </tr>
             <tr>
                <td colspan="10" align="right" style="border:none;"><b>Balance Qty: &nbsp;&nbsp;</b> </td>
                <td colspan="11" align="left">&nbsp;<b><font color="<? echo $color;?>"> <?  echo number_format($bal_qty,2); ?> &nbsp;&nbsp;</font></b> </td>
            </tr>
        </tfoot>
</table>
</div>
</fieldset>
</div>
<?
	exit();
}//BatchReport
?>