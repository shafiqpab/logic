<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$color_arr=return_library_array("select id, color_name from lib_color",'id','color_name');



if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 
if ($action=="job_no_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
    <input type="hidden" id="selected_id" name="selected_id" /> 
	<script>
	
		function js_set_value(id)
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="540" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>               	 
                    <th width="140">Party Name</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_by_td">Wash Job No</th>
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                        <td id="buyer_td"><input type="hidden" id="selected_job"><? $data=$company_id; ?>
                         <? 
							 if($cbo_within_group==1)
								{
									echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_buyer_name, "");
								}
								else
								{
									echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=".$company_id." $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $cbo_buyer_name, "" );
								}	
											 
						 ?></td>
                    <td>
						<?
                            $search_by_arr=array(1=>"Wash Job No",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $company_id ?>+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+<? echo $cbo_within_group;?>, 'create_job_search_list_view', 'search_div', 'monthly_party_wise_delivery_statement_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                    </tr>
                </tbody>
            </table>  
             <table width="840" cellspacing="0" cellpadding="0" border="1" rules="all"  align="center">
            		 <tr>
                        <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
             </table>  
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	disconnect($con);
    exit();
}
if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	//echo "<pre>";
	//print_r($data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[2]);
	$search_str=trim(str_replace("'","",$data[3]));
	$within_group =$data[4];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";  
		}
	

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_po_arr=array();
	if($within_group==1)
	{
		$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}
	
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(c.color_id)";
		$buyer_po_id_str="group_concat(b.buyer_po_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
		$buyer_po_id_str="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}
	 $sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref, $color_id_str as color_id, $buyer_po_id_str as buyer_po_id
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	 where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $company $party_id_cond $withinGroup $search_com_cond   $withinGroup and b.id=c.mst_id  
	 group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref
	 order by a.id DESC";
	 //echo $sql;
	 $data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="785" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="130">Buyer Po</th>
            <th width="130">Buyer Style</th>
            <th width="100">Ord Receive Date</th>
            <th>Delivery Date</th>
        </thead>
        </table>
        <div style="width:785px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="765" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				$buyer_po=""; $buyer_style="";
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('subcon_job')]; ?>')" style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
                    <td width="130" style="word-break:break-all"><? if ($within_group==1)echo $buyer_po; else echo $row[csf('buyer_po_no')];//echo $buyer_po; ?></td>
                    <td width="130" style="word-break:break-all"><? if ($within_group==1)echo $buyer_style; echo $row[csf('buyer_style_ref')];//echo $buyer_style; ?></td>
                    <td width="100" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
    </div>
	<?    
	exit();
}

if($action=="report_generate")
{ 
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$job_no=str_replace("'","",$txt_job_no);
	$cbo_buyer_id=str_replace("'","",$cbo_party_name);
	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	if($db_type==0) $select_from_date=change_date_format($from_date,'yyyy-mm-dd');
	if($db_type==2) $select_from_date=change_date_format($from_date,'','',1);
	if($db_type==0) $select_to_date=change_date_format($to_date,'yyyy-mm-dd');
	if($db_type==2) $select_to_date=change_date_format($to_date,'','',1);
	
	if($cbo_within_group==1)
	{
		$party_arr=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	}
	
	$search_str=trim(str_replace("'","",$txt_search_string));
	$search_type =str_replace("'","",$cbo_type);
	
		if($search_str!="")
		{
			if($search_type==1) $search_com_cond=" and c.buyer_po_no like '%$search_str'";
			else if($search_type==2) $search_com_cond=" and c.buyer_style_ref like '%$search_str'"; 
			else if($search_type==3) $search_com_cond=" and a.job_no like '%$search_str'";  
		}
		
		//echo $search_com_cond; die;
	
	if($cbo_buyer_id==0) $party_con=""; else $party_con=" and a.party_id='$cbo_buyer_id'";
	if($cbo_within_group==0) $within_group=""; else $within_group=" and d.within_group='$cbo_within_group'";
	
	// return_library_array satart 
	$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	if($cbo_within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	// return_library_array end 
	
	if(str_replace("'","",$company_id)==0)$company_name=""; else $company_name=" and a.company_id=$company_id";
	if($db_type==0)
	{
		if( $from_date==0 && $to_date==0 ) $subcon_date=""; else $subcon_date= " and a.subcon_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
			if( $from_date==0 && $to_date==0 ) $delivery_date=""; else $delivery_date= " and a.delivery_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";	
 	}
	if($db_type==2)
	{
		if( $from_date==0 && $to_date==0 ) $subcon_date=""; else $subcon_date= " and a.subcon_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
		if( $from_date==0 && $to_date==0 ) $delivery_date=""; else $delivery_date= " and a.delivery_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
 	
	}
			
	ob_start();
	?>
    
    <div id="mstDiv" align="center">
        <table style="width:1200px"> 
            <?
            $company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$company_id."");
            foreach( $company_library as $row)
            {
                $company=$row[csf('company_name')];
            ?>
            <tr>
            <td colspan="12" align="center" style="font-size:22px"><? echo $row[csf('company_name')];?></td>
            </tr>
            <?
            }
            ?>
            <tr>
            <td colspan="12" align="center" style="font-size:20px"><? echo $report_title; ?></td>
            </tr>
            <tr style="border:none;">
                <td colspan="12" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if($from_date!="" || $to_date!="")echo "Date Range : ".change_date_format($from_date)."  to  ".change_date_format($to_date)."" ;?>
                </td>
            </tr>
        </table>
        <?

	/*$delivery_sql = "select a.id, a.delivery_no, a.delivery_date, a.job_no, b.id as details_id, b.delivery_qty, b.order_id, b.remarks
  		from subcon_delivery_mst a, subcon_delivery_dtls b
 		where a.entry_form = 303 and a.id = b.mst_id $company_name $party_con $delivery_date $search_com_cond and a.status_active=1 and b.status_active=1";*/

 	$delivery_sql = "select a.id, a.job_no, sum(b.delivery_qty) as delivery_qty, b.order_id, c.job_no_mst, c.gmts_color_id, c.gmts_item_id, b.id as dtls_id, a.delivery_no, a.delivery_date, b.remarks
    			from subcon_delivery_mst a, subcon_delivery_dtls b, subcon_ord_dtls c
   				where a.id = b.mst_id and a.entry_form = '303' and b.order_id = c.id $company_name $party_con $delivery_date $search_com_cond and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
				group by a.id, c.job_no_mst, c.gmts_color_id, c.gmts_item_id, a.delivery_no, a.delivery_date, a.job_no, b.id, b.delivery_qty, b.order_id,
       				b.remarks
				order by a.id desc";

	// echo $delivery_sql;

 	$delivery_result = sql_select($delivery_sql);

	// $do_result = sql_select($do_sql);
    $do_all_data=array();
    $delivery_data=array();
    $delivery_arr=array();
    $order_id_arr=array();

    foreach ($delivery_result as $row) {
    	if( $delivery_data[$row[csf('order_id')]['job_no']] == $row[csf('job_no_mst')] ) {
    		$delivery_data[$row[csf('order_id')]]['delivery_qty']+=$row[csf('delivery_qty')];
    	} else {
    		$delivery_data[$row[csf('order_id')]]['delivery_qty']=$row[csf('delivery_qty')];
    	}
    	$delivery_data[$row[csf('order_id')]]['id']=$row[csf('id')];
    	$delivery_data[$row[csf('order_id')]]['delivery_no']=$row[csf('delivery_no')];
    	$delivery_data[$row[csf('order_id')]]['delivery_date']=$row[csf('delivery_date')];
    	$delivery_data[$row[csf('order_id')]]['job_no']=$row[csf('job_no_mst')];
    	$delivery_data[$row[csf('order_id')]]['details_id']=$row[csf('details_id')];
    	$delivery_data[$row[csf('order_id')]]['order_id']=$row[csf('order_id')];
    	$delivery_data[$row[csf('order_id')]]['remarks']=$row[csf('remarks')];
    	$delivery_data[$row[csf('order_id')]]['gmts_color_id']=$row[csf('gmts_color_id')];
    	$delivery_data[$row[csf('order_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];

    	if( isset($delivery_arr[$row[csf('job_no_mst')]]) )
	   {
    		$delivery_arr[$row[csf('job_no_mst')]][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['delivery_qty']+=$row[csf('delivery_qty')];
    	} else {
    		$delivery_arr[$row[csf('job_no_mst')]][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['delivery_qty']=$row[csf('delivery_qty')];
    	}

    	$order_id_arr[] = $row[csf('order_id')];
    }
 	unset($delivery_result);

    $order_id_arr = array_unique($order_id_arr);

    $order_ids = implode(',', $order_id_arr);

    /*echo '<pre>';
    print_r($order_ids);
    echo '</pre>';
    die;*/

    if($order_ids == '') {
    	echo '<h2>No Delivery Data Found!!!</h2>';
    	ob_end_flush();
    	die;
    }
    /*if($db_type==0)	{ mysql_query("BEGIN");} else {$con = connect();}
    
    $user_id=$_SESSION['logic_erp']['user_id'];
    foreach ($order_id_arr as $order_id) {
		$rID=execute_query("insert into tmp_poid(userid, poid, type) values($user_id,$order_id,259)");
	}

	if($db_type==0) {
		if($rID) {
			mysql_query("COMMIT");
		}
	} else {
		if($rID) {
			oci_commit($con);
		}
	}*/

	// echo $user_id;die;

 	/*$order_sql = "select c.rate, c.gmts_item_id, c.gmts_color_id, c.order_no, c.buyer_po_no, c.buyer_style_ref, d.party_id, c.buyer_buyer, c.party_buyer_name, e.embellishment_type, e.process, c.id as ord_dtls_id, c.order_uom, c.order_quantity
  		from subcon_ord_dtls c, subcon_ord_mst d, subcon_ord_breakdown e, tmp_poid f
 		where f.type=959 and f.userid=$user_id and c.id = f.poid and d.subcon_job=e.job_no_mst and e.mst_id = f.poid and c.mst_id = d.id and c.id = e.mst_id $within_group and c.status_active=1 and d.status_active=1 and e.status_active=1";*/

 	// echo $order_sql;die;



	$order_con=where_con_using_array($order_id_arr,0,"c.id");




 	$order_sql = "select c.rate, c.gmts_item_id, c.gmts_color_id, c.order_no, c.buyer_po_no, c.buyer_style_ref, d.party_id, c.buyer_buyer, c.party_buyer_name, e.embellishment_type, e.process, c.id as ord_dtls_id, c.order_uom, c.order_quantity
  		from subcon_ord_dtls c, subcon_ord_mst d, subcon_ord_breakdown e
 		where c.mst_id = d.id and d.subcon_job=e.job_no_mst and c.id = e.mst_id $within_group $order_con and c.status_active=1 and d.status_active=1 and e.status_active=1"; //c.id in($order_id) and

 	 //echo $order_sql;//die;

 	$order_result = sql_select($order_sql);

 	// echo $order_sql;

    foreach($order_result as $row)
	 {
    	$job_no = $delivery_data[$row[csf('ord_dtls_id')]]['job_no'];
    	// echo "$job_no <br>";
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['id']=$delivery_data[$row[csf('ord_dtls_id')]]['id'];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['party_buyer_name']=$row[csf('party_buyer_name')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['order_no']=$row[csf('order_no')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['job_no']=$delivery_data[$row[csf('ord_dtls_id')]]['job_no'];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];    	
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['rate']=$row[csf('rate')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['buyer_style_ref']=$row[csf('buyer_style_ref')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['gmts_color_id']=$row[csf('gmts_color_id')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['remarks']=$delivery_data[$row[csf('ord_dtls_id')]]['remarks'];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['party_id']=$row[csf('party_id')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['details_id']=$delivery_data[$row[csf('ord_dtls_id')]]['details_id'];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['job_dtls_id']=$row[csf('ord_dtls_id')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['embellishment_type'].=$row[csf('embellishment_type')].',';
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['process']=$row[csf('process')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['order_uom']=$row[csf('order_uom')];

    	if( isset($do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]) ) {
    		$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['order_quantity']+=$row[csf('order_quantity')];
    	} else {
    		$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['order_quantity']=$row[csf('order_quantity')];
    	}

    	/*if( isset($do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]]) ) {
    		echo $job_no.'<br>';
    		$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]]['delivery_qty']+=$delivery_data[$row[csf('ord_dtls_id')]]['delivery_qty'];
    	} else {
    		$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]]['delivery_qty']=$delivery_data[$row[csf('ord_dtls_id')]]['delivery_qty'];
    	}*/
    }

    unset($order_result);
		
		$receeive_qty_array=array();
		$sql_receeive="Select b.job_dtls_id,c.job_no_mst,a.subcon_date,b.quantity as receive_qnty, c.gmts_color_id,c.gmts_item_id from subcon_ord_dtls c, sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.entry_form=296 and b.job_dtls_id=c.id $subcon_date and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

		//echo $sql_receeive;
		$sql_receeive_result=sql_select($sql_receeive); $receeive_date_array=array(); 
		foreach ($sql_receeive_result as $row)
		{
			//$receeive_qty_array[$row[csf('job_dtls_id')]][$row[csf('gmts_color_id')]]['receive_qnty']+=$row[csf('receive_qnty')];
			$receeive_qty_array[$row[csf('job_no_mst')]][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['receive_qnty']+=$row[csf('receive_qnty')];
		}
			

		 $grand_total_balance_quantity=0;
	     $grand_total_receeive_qty=0;
	     $grand_total_delivery_qty_dzn=0;		
	     $grand_total_delivery_amount_usd=0;

        foreach($do_all_data as $party_id=>$party_data) //$pay_term
        {
        	$total_balance_quantity=0; $total_delivery_qty_dzn=0; $total_delivery_amount_usd=0;$total_receeive_qty=0;
			 $k=1;
			 $sl=1;
        ?>
        <table style="width:1300px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
        <tr>
       	 <td colspan="12" style="font-size:24px"><b>Party Name: <? echo $party_arr[$party_id];?></b></td>
        </tr>
        </table>               	
        <table style="width:1300px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="scroll_body" >
        <thead>
            <th width="30" >SL</th>
            <th width="120" >Party Buyer</th>
            <th width="130" >Order No</th>
             <th width="130" >Style No</th>
            <th width="130" >Job No</th>
            <th width="100" >Color</th>
            <th width="130" >Gmts. Item</th>
            <th width="130" >Type of Wash</th>
             <th width="110" >Receive Qty (pcs)</th>
            <th width="110" >Delevery Qty (pcs)</th>
            <th width="110" >Qty (DZN)</th>
            <th width="110" >Rate (DZN) USD</th>
             <th width="110" >Amount USD</th>
            <th width="200" >Remarks</th>
        </thead>
        <tbody>
        <?
        
        foreach($party_data as $buyer_style_ref_id=>$buyer_style_ref_data)
        {
			foreach($buyer_style_ref_data as $job_no_id=>$job_data)
			{	
				foreach($job_data as $gmts_color_id=>$gmts_color_data)
				{	
					foreach($gmts_color_data as $gmts_item_id=>$row_data)
					{								
						if ($k%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						
						$delivery_qty=$delivery_arr[$row_data['job_no']][$row_data['gmts_color_id']][$row_data['gmts_item_id']]['delivery_qty'];
						
						if($row_data['order_uom']==1)
						{
							$rate=$row_data['rate']*12;
							$delivery_qty_dzn=$delivery_qty/12;
							$delivery_amount_usd=($delivery_qty/12)*$rate;
						}
						if($row_data['order_uom']==2)
						{
							$rate=$row_data['rate'];
							$delivery_qty_dzn=$delivery_qty/12;
							$delivery_amount_usd=($delivery_qty/12)*$rate;
						}
						
					
						$processId=""; 
						$processname=explode(",",$row_data['process']);
						foreach($processname as $process_data)
						{
							if($processId=="") $processId=$process_data; else $processId.=','.$process_data;
						}
						$processvalue=implode(",",array_unique(explode(",",$processId)));
						if($processvalue==1) $process_type=$wash_wet_process;
						else if($processvalue==2) $process_type=$wash_dry_process;
						else if($processvalue==3) $process_type=$wash_laser_desing;
						else $process_type=$blank_array;
					
						$embellishment_type=""; 
						$embellishmenttypeid=explode(",",$row_data['embellishment_type']);
						foreach($embellishmenttypeid as $embellishmentType)
						{
							if($embellishment_type=="") $embellishment_type=$process_type[$embellishmentType]; else $embellishment_type.=','.$process_type[$embellishmentType];
						}
						$embellishment_type=implode(",",array_unique(explode(",",$embellishment_type)));
						//$receeive_qty=$receeive_qty_array[$row_data['job_dtls_id']][$row_data['gmts_color_id']]['receive_qnty'];
						$receeive_qty=$receeive_qty_array[$row_data['job_no']][$row_data['gmts_color_id']][$row_data['gmts_item_id']]['receive_qnty'];
						//$receeive_qty=$row_data['order_quantity'];
					?>
					
					<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
						<td width="30" title="<? echo $row_data['job_no']."==".$row_data['gmts_color_id'];?>"><?php echo $sl; ?></td>
						<td width="120" align="center" title="<? echo $row_data['party_buyer_name'] ?>"><? echo $row_data['party_buyer_name']; ?></td>
						<td width="130" align="center"><? echo $row_data['order_no'];   ?></td>
						<td width="130"  align="center"><? echo $row_data['buyer_style_ref'];  ?></td>
	                    <td width="130"  align="center"><? echo $row_data['job_no'];  ?></td>
	                    <td width="100"  align="center"><? echo $color_arr[$row_data['gmts_color_id']];  ?></td>
	                    <td width="130" align="center"><? echo $garments_item[$row_data['gmts_item_id']];   ?></td>
	                    <td width="130" align="center"><? echo chop($embellishment_type,','); ?></td>
	                    <td width="110"  align="right"><p><a href='#report_details' onClick="openmypage_receive('<? echo $row_data['job_no']; ?>','<? echo $row_data['gmts_color_id']; ?>','<? echo $row_data['gmts_item_id'];?>','<? echo $row_data['order_no']; ?>','<? echo $party_id;?>','<? echo $row_data['buyer_style_ref'];?>','receive_popup');"><? echo number_format($receeive_qty,2);  ?> </a></p></td>
						<td width="110"  align="right"><p><a href='#report_details' onClick="openmypage_delivery('<? echo $row_data['job_no']; ?>','<? echo $row_data['gmts_color_id']; ?>','<? echo $row_data['gmts_item_id'];?>','<? echo $row_data['order_no']; ?>','<? echo $party_id;?>','<? echo $row_data['buyer_style_ref']; ?>','delivery_popup');"><? echo number_format($delivery_qty,2);  ?> </a></p></td>
						<td width="110"  align="right"><?  echo number_format($delivery_qty_dzn,2);   ?></td>
	                    <td width="110"  align="right"><?  echo number_format($rate,4);   ?></td>
	                    <td width="110"  align="right"><?  echo number_format($delivery_amount_usd,2);   ?></td>
						<td width="200"  align="center"></td>
					</tr>
					<?
	        		$k++;
					$sl++;
					 $total_balance_quantity+=$delivery_qty;
				     $total_receeive_qty+=$receeive_qty;
				     $total_delivery_qty_dzn+=$delivery_qty_dzn;		
				     $total_delivery_amount_usd+=$delivery_amount_usd;


				     

				 	}

				}

        	}
				
								
        
       	 }
		 
		 ?>
        <tr>
            <td colspan="8" align="right"><b>Total: </b></td>
            <td align="right"><b><? echo number_format($total_receeive_qty,2); ?></b></td>
            <td align="right"><b><? echo number_format($total_balance_quantity,2); ?></b></td>
            <td align="right"><b><? echo number_format($total_delivery_qty_dzn,2); ?></b></td>
            <td align="right">&nbsp;</td>
            <td align="right"><b><? echo number_format($total_delivery_amount_usd,2); ?></b></td>
            <td align="right">&nbsp;</td>
        </tr>
        
        <?
         $grand_total_balance_quantity+=$total_balance_quantity;
	     $grand_total_receeive_qty+=$total_receeive_qty;
	     $grand_total_delivery_qty_dzn+=$total_delivery_qty_dzn;		
	     $grand_total_delivery_amount_usd+=$total_delivery_amount_usd;
		}
        ?>
        <tr>
            <td colspan="8" align="right"><b>Grand Total: </b></td>
            <td align="right"><b><? echo number_format($grand_total_receeive_qty,2); ?></b></td>
            <td align="right"><b><? echo number_format($grand_total_balance_quantity,2); ?></b></td>
            <td align="right"><b><? echo number_format($grand_total_delivery_qty_dzn,2); ?></b></td>
            <td align="right">&nbsp;</td>
            <td align="right"><b><? echo number_format($grand_total_delivery_amount_usd,2); ?></b></td>
            <td align="right">&nbsp;</td>
        </tr>
        </tbody>			               
        </table>
        </div>
    <?
	

    /*$rID2=execute_query("delete from tmp_poid where userid=$user_id and type=259");

	if($db_type==0) {
		if($rID2) {
			mysql_query("COMMIT");
		}
	} else {
		if($rID2) {
			oci_commit($con);
		}
	}
	disconnect($con);*/

    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
    exit();
}

if($action=="report_generate_receive_date")
{ 
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$job_no=str_replace("'","",$txt_job_no);
	$cbo_buyer_id=str_replace("'","",$cbo_party_name);
	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	if($db_type==0) $select_from_date=change_date_format($from_date,'yyyy-mm-dd');
	if($db_type==2) $select_from_date=change_date_format($from_date,'','',1);
	if($db_type==0) $select_to_date=change_date_format($to_date,'yyyy-mm-dd');
	if($db_type==2) $select_to_date=change_date_format($to_date,'','',1);
	
	if($cbo_within_group==1)
	{
		$party_arr=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	}
	
	$search_str=trim(str_replace("'","",$txt_search_string));
	$search_type =str_replace("'","",$cbo_type);
	
		if($search_str!="")
		{
			if($search_type==1) $search_com_cond=" and c.buyer_po_no like '%$search_str'";
			else if($search_type==2) $search_com_cond=" and c.buyer_style_ref like '%$search_str'"; 
			else if($search_type==3) $search_com_cond=" and c.job_no_mst like '%$search_str'";  
		}
		
		//echo $search_com_cond; die;
	
	//if($cbo_buyer_id==0) $party_con=""; else $party_con=" and a.party_id='$cbo_buyer_id'";
	if($cbo_buyer_id==0) $party_con=""; else $party_con=" and d.party_id='$cbo_buyer_id'";
	
	
	if($cbo_within_group==0) $within_group=""; else $within_group=" and d.within_group='$cbo_within_group'";
	
	// return_library_array satart 
	$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	if($cbo_within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	// return_library_array end 
	
	if(str_replace("'","",$company_id)==0)$company_name=""; else $company_name=" and a.company_id=$company_id";
	if(str_replace("'","",$company_id)==0)$receive_company_name=""; else $receive_company_name=" and f.company_id=$company_id";
	if($db_type==0)
	{
		if( $from_date==0 && $to_date==0 ) $order_receive_date=""; else $order_receive_date= " and d.receive_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		if( $from_date==0 && $to_date==0 ) $subcon_date=""; else $subcon_date= " and a.subcon_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		if( $from_date==0 && $to_date==0 ) $receive_date=""; else $receive_date= " and f.subcon_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		
		if( $from_date==0 && $to_date==0 ) $delivery_date=""; else $delivery_date= " and a.delivery_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";	
 	}
	if($db_type==2)
	{
		if( $from_date==0 && $to_date==0 ) $order_receive_date=""; else $order_receive_date= " and d.receive_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
		if( $from_date==0 && $to_date==0 ) $subcon_date=""; else $subcon_date= " and a.subcon_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
		if( $from_date==0 && $to_date==0 ) $receive_date=""; else $receive_date= " and f.subcon_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
		if( $from_date==0 && $to_date==0 ) $delivery_date=""; else $delivery_date= " and a.delivery_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
 	
	}
			
	ob_start();
	?>
    
    <div id="mstDiv" align="center">
        <table style="width:1200px"> 
            <?
            $company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$company_id."");
            foreach( $company_library as $row)
            {
                $company=$row[csf('company_name')];
            ?>
            <tr>
            <td colspan="12" align="center" style="font-size:22px"><? echo $row[csf('company_name')];?></td>
            </tr>
            <?
            }
            ?>
            <tr>
            <td colspan="12" align="center" style="font-size:20px"><? echo $report_title; ?></td>
            </tr>
            <tr style="border:none;">
                <td colspan="12" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if($from_date!="" || $to_date!="")echo "Date Range : ".change_date_format($from_date)."  to  ".change_date_format($to_date)."" ;?>
                </td>
            </tr>
        </table>
        <?

	/*$delivery_sql = "select a.id, a.delivery_no, a.delivery_date, a.job_no, b.id as details_id, b.delivery_qty, b.order_id, b.remarks
  		from subcon_delivery_mst a, subcon_delivery_dtls b
 		where a.entry_form = 303 and a.id = b.mst_id $company_name $party_con $delivery_date $search_com_cond and a.status_active=1 and b.status_active=1";*/

   $delivery_sql = "select a.id, a.job_no, sum(b.delivery_qty) as delivery_qty, b.order_id, c.job_no_mst, c.gmts_color_id, c.gmts_item_id, b.id as dtls_id, a.delivery_no, a.delivery_date, b.remarks
    			from subcon_delivery_mst a, subcon_delivery_dtls b, subcon_ord_dtls c
   				where a.id = b.mst_id and a.entry_form = '303' and b.order_id = c.id $company_name  $search_com_cond and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  group by a.id, c.job_no_mst, c.gmts_color_id, c.gmts_item_id, a.delivery_no, a.delivery_date, a.job_no, b.id, b.delivery_qty, b.order_id,
       				b.remarks
				order by a.id desc";  //$delivery_date

	// echo $delivery_sql;

 	$delivery_result = sql_select($delivery_sql);

	// $do_result = sql_select($do_sql);
    $do_all_data=array();
    $delivery_data=array();
    $delivery_arr=array();
    $order_id_arr=array();

    foreach ($delivery_result as $row)
	 {
    	if( $delivery_data[$row[csf('order_id')]['job_no']] == $row[csf('job_no_mst')] )
		{
    		$delivery_data[$row[csf('order_id')]]['delivery_qty']+=$row[csf('delivery_qty')];
    	} 
		else 
		{
    		$delivery_data[$row[csf('order_id')]]['delivery_qty']=$row[csf('delivery_qty')];
    	}
    	$delivery_data[$row[csf('order_id')]]['id']=$row[csf('id')];
    	$delivery_data[$row[csf('order_id')]]['delivery_no']=$row[csf('delivery_no')];
    	$delivery_data[$row[csf('order_id')]]['delivery_date']=$row[csf('delivery_date')];
    	$delivery_data[$row[csf('order_id')]]['job_no']=$row[csf('job_no_mst')];
    	$delivery_data[$row[csf('order_id')]]['details_id']=$row[csf('dtls_id')];
    	$delivery_data[$row[csf('order_id')]]['order_id']=$row[csf('order_id')];
    	$delivery_data[$row[csf('order_id')]]['remarks']=$row[csf('remarks')];
    	$delivery_data[$row[csf('order_id')]]['gmts_color_id']=$row[csf('gmts_color_id')];
    	$delivery_data[$row[csf('order_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];

    	if( isset($delivery_arr[$row[csf('job_no_mst')]]) )
	   {
    		$delivery_arr[$row[csf('job_no_mst')]][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['delivery_qty']+=$row[csf('delivery_qty')];
    	} else {
    		$delivery_arr[$row[csf('job_no_mst')]][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['delivery_qty']=$row[csf('delivery_qty')];
    	}

    	$order_id_arr[] = $row[csf('order_id')];
    }
 	unset($delivery_result);
     $order_id_arr = array_unique($order_id_arr);
     $order_ids = implode(',', $order_id_arr);

	$order_con=where_con_using_array($order_id_arr,0,"c.id");
	
	
	 /*  $job_dtls_id_arr=array();
	   $receeive_qty_array=array();
		$sql_receeive="Select b.job_dtls_id,c.job_no_mst,a.subcon_date,b.quantity as receive_qnty, c.gmts_color_id,c.gmts_item_id from subcon_ord_dtls c, sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.entry_form=296 and b.job_dtls_id=c.id $subcon_date and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

		//echo $sql_receeive;
		$sql_receeive_result=sql_select($sql_receeive); $receeive_date_array=array(); 
		foreach ($sql_receeive_result as $row)
		{
			//$receeive_qty_array[$row[csf('job_dtls_id')]][$row[csf('gmts_color_id')]]['receive_qnty']+=$row[csf('receive_qnty')];
			$receeive_qty_array[$row[csf('job_no_mst')]][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['receive_qnty']+=$row[csf('receive_qnty')];
			$job_dtls_id_arr[] = $row[csf('job_dtls_id')];
		}
	
	 unset($sql_receeive_result);
     $job_dtls_id_arr = array_unique($job_dtls_id_arr);
     $job_dtls_ids = implode(',', $job_dtls_id_arr);

	$job_dtls_id_con=where_con_using_array($job_dtls_id_arr,0,"c.id");*/
	
	
	
	/*$order_sql = "select c.rate,c.job_no_mst, c.gmts_item_id, c.gmts_color_id, c.order_no, c.buyer_po_no, c.buyer_style_ref, d.party_id, c.buyer_buyer, c.party_buyer_name, e.embellishment_type, e.process, c.id as ord_dtls_id, c.order_uom, c.order_quantity,g.quantity as receive_qnty,c.remarks
  		from subcon_ord_dtls c, subcon_ord_mst d, subcon_ord_breakdown e,sub_material_mst f, sub_material_dtls g
 		where c.mst_id = d.id and d.subcon_job=e.job_no_mst and f.id=g.mst_id and g.job_dtls_id=c.id and c.id = e.mst_id and f.entry_form=296 $receive_company_name $search_com_cond $within_group $receive_date $party_con and c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0"; */
	
	if($db_type==0) $process_type_cond="group_concat(e.process,'*',e.embellishment_type)";
	else if ($db_type==2) $process_type_cond="listagg(e.process||'*'||e.embellishment_type,',') within group (order by e.process||'*'||e.embellishment_type)";
	
	

  $order_sql = "select c.rate,c.job_no_mst, c.gmts_item_id, c.gmts_color_id, c.order_no, c.buyer_po_no, c.buyer_style_ref, d.party_id, c.buyer_buyer, c.party_buyer_name,c.id as ord_dtls_id, c.order_uom, c.order_quantity as order_quantity,g.quantity as receive_qnty,c.remarks,$process_type_cond as process
  		from subcon_ord_dtls c, subcon_ord_mst d,subcon_ord_breakdown e,sub_material_mst f, sub_material_dtls g
 		where c.mst_id = d.id   and d.subcon_job=e.job_no_mst  and c.id = e.mst_id  and f.id=g.mst_id and g.job_dtls_id=c.id and f.entry_form=296 $receive_company_name $search_com_cond $within_group $receive_date $party_con and c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 group by c.rate,c.job_no_mst, c.gmts_item_id, c.gmts_color_id, c.order_no, c.buyer_po_no, c.buyer_style_ref, d.party_id, c.buyer_buyer, c.party_buyer_name, c.id , c.order_uom, c.order_quantity,g.quantity,c.remarks"; 


 	/*$order_sql = "select c.rate, c.gmts_item_id, c.gmts_color_id, c.order_no, c.buyer_po_no, c.buyer_style_ref, d.party_id, c.buyer_buyer, c.party_buyer_name, e.embellishment_type, e.process, c.id as ord_dtls_id, c.order_uom, c.order_quantity from subcon_ord_dtls c, subcon_ord_mst d, subcon_ord_breakdown e,sub_material_mst f, sub_material_dtls g
 		where c.mst_id = d.id and d.subcon_job=e.job_no_mst and f.id=g.mst_id and g.job_dtls_id=c.id and c.id = e.mst_id $within_group $job_dtls_id_con and c.status_active=1 and d.status_active=1 and e.status_active=1";*/ //c.id in($order_id) and

 	//echo $order_sql;die;

 	$order_result = sql_select($order_sql);

 	// echo $order_sql;

    foreach($order_result as $row)
	 {
    	$job_no = $delivery_data[$row[csf('ord_dtls_id')]]['job_no'];
    	// echo "$job_no <br>";
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['id']=$delivery_data[$row[csf('ord_dtls_id')]]['id'];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['party_buyer_name']=$row[csf('party_buyer_name')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['order_no']=$row[csf('order_no')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['job_no']=$row[csf('job_no_mst')];//$delivery_data[$row[csf('ord_dtls_id')]]['job_no'];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['gmts_item_id']=$row[csf('gmts_item_id')];    	
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['rate']=$row[csf('rate')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['buyer_style_ref']=$row[csf('buyer_style_ref')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['gmts_color_id']=$row[csf('gmts_color_id')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['remarks']=$row[csf('remarks')];//$delivery_data[$row[csf('ord_dtls_id')]]['remarks'];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['party_id']=$row[csf('party_id')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['details_id']=$delivery_data[$row[csf('ord_dtls_id')]]['details_id'];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['job_dtls_id']=$row[csf('ord_dtls_id')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['embellishment_type'].=$row[csf('embellishment_type')].',';
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['process']=$row[csf('process')];
    	$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['order_uom']=$row[csf('order_uom')];
		

    	if( isset($do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]) )
		{
    		$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['order_quantity']+=$row[csf('order_quantity')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['receive_qnty']+=$row[csf('receive_qnty')];
    	} 
		else 
		{
    		$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['order_quantity']=$row[csf('order_quantity')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]][$row[csf('gmts_item_id')]]['receive_qnty']=$row[csf('receive_qnty')];
    	}

    	/*if( isset($do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]]) ) {
    		echo $job_no.'<br>';
    		$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]]['delivery_qty']+=$delivery_data[$row[csf('ord_dtls_id')]]['delivery_qty'];
    	} else {
    		$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$job_no][$row[csf('gmts_color_id')]]['delivery_qty']=$delivery_data[$row[csf('ord_dtls_id')]]['delivery_qty'];
    	}*/
    }

    unset($order_result);
		
		
			

		 $grand_total_balance_quantity=0;
	     $grand_total_receeive_qty=0;
	     $grand_total_delivery_qty_dzn=0;		
	     $grand_total_delivery_amount_usd=0;

        foreach($do_all_data as $party_id=>$party_data) //$pay_term
        {
        	$total_balance_quantity=0; $total_delivery_qty_dzn=0; $total_delivery_amount_usd=0;$total_receeive_qty=0;
			 $k=1;
			 $sl=1;
        ?>
        <table style="width:1300px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
        <tr>
       	 <td colspan="12" style="font-size:24px"><b>Party Name: <? echo $party_arr[$party_id];?></b></td>
        </tr>
        </table>               	
        <table style="width:1300px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="scroll_body" >
        <thead>
            <th width="30" >SL</th>
            <th width="120" >Party Buyer</th>
            <th width="130" >Order No</th>
             <th width="130" >Style No</th>
            <th width="130" >Job No</th>
            <th width="100" >Color</th>
            <th width="130" >Gmts. Item</th>
            <th width="130" >Type of Wash</th>
             <th width="110" >Receive Qty (pcs)</th>
            <th width="110" >Delevery Qty (pcs)</th>
            <th width="110" >Qty (DZN)</th>
            <th width="110" >Rate (DZN) USD</th>
             <th width="110" >Amount USD</th>
            <th width="200" >Remarks</th>
        </thead>
        <tbody>
        <?
        
        foreach($party_data as $buyer_style_ref_id=>$buyer_style_ref_data)
        {
			foreach($buyer_style_ref_data as $job_no_id=>$job_data)
			{	
				foreach($job_data as $gmts_color_id=>$gmts_color_data)
				{	
					foreach($gmts_color_data as $gmts_item_id=>$row_data)
					{								
						if ($k%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						
						
				   $ex_process=array_unique(explode(",",$row_data['process']));  
					$process_name=""; $sub_process_name="";
					foreach($ex_process as $process_data)
					{
						$ex_process_type=explode("*",$process_data);
						$process_id=$ex_process_type[0];
						$type_id=$ex_process_type[1];
						if($process_id==1) $process_type_arr=$wash_wet_process;
						else if($process_id==2) $process_type_arr=$wash_dry_process;
						else if($process_id==3) $process_type_arr=$wash_laser_desing;
						else $process_type_arr=$blank_array;
						
						if($process_name=="") $process_name=$wash_type[$process_id]; else $process_name.=','.$wash_type[$process_id];
						
						if($sub_process_name=="") $sub_process_name=$process_type_arr[$type_id]; else $sub_process_name.=','.$process_type_arr[$type_id];
					}
					$process_name=implode(",",array_unique(explode(",",$process_name)));
					$sub_process_name=implode(",",array_unique(explode(",",$sub_process_name)));
						
						///////////////////
						
						$delivery_qty=$delivery_arr[$row_data['job_no']][$row_data['gmts_color_id']][$row_data['gmts_item_id']]['delivery_qty'];
						
						if($row_data['order_uom']==1)
						{
							$rate=$row_data['rate']*12;
							$delivery_qty_dzn=$delivery_qty/12;
							$delivery_amount_usd=($delivery_qty/12)*$rate;
						}
						if($row_data['order_uom']==2)
						{
							$rate=$row_data['rate'];
							$delivery_qty_dzn=$delivery_qty/12;
							$delivery_amount_usd=($delivery_qty/12)*$rate;
						}
						
					
						/*$processId=""; 
						$processname=explode(",",$row_data['process']);
						foreach($processname as $process_data)
						{
							if($processId=="") $processId=$process_data; else $processId.=','.$process_data;
						}
						$processvalue=implode(",",array_unique(explode(",",$processId)));
						if($processvalue==1) $process_type=$wash_wet_process;
						else if($processvalue==2) $process_type=$wash_dry_process;
						else if($processvalue==3) $process_type=$wash_laser_desing;
						else $process_type=$blank_array;
					
						$embellishment_type=""; 
						$embellishmenttypeid=explode(",",$row_data['embellishment_type']);
						foreach($embellishmenttypeid as $embellishmentType)
						{
							if($embellishment_type=="") $embellishment_type=$process_type[$embellishmentType]; else $embellishment_type.=','.$process_type[$embellishmentType];
						}
						$embellishment_type=implode(",",array_unique(explode(",",$embellishment_type)));*/
						//$receeive_qty=$receeive_qty_array[$row_data['job_dtls_id']][$row_data['gmts_color_id']]['receive_qnty'];
						//$receeive_qty=$receeive_qty_array[$row_data['job_no']][$row_data['gmts_color_id']][$row_data['gmts_item_id']]['receive_qnty'];
						$receeive_qty=$row_data['receive_qnty'];
					?>
					
					<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
						<td width="30" title="<? echo $row_data['job_no']."==".$row_data['gmts_color_id'];?>"><?php echo $sl; ?></td>
						<td width="120" align="center" title="<? echo $row_data['party_buyer_name'] ?>"><? echo $row_data['party_buyer_name']; ?></td>
						<td width="130" align="center"><? echo $row_data['order_no'];   ?></td>
						<td width="130"  align="center"><? echo $row_data['buyer_style_ref'];  ?></td>
	                    <td width="130"  align="center"><? echo $row_data['job_no'];  ?></td>
	                    <td width="100"  align="center"><? echo $color_arr[$row_data['gmts_color_id']];  ?></td>
	                    <td width="130" align="center"><? echo $garments_item[$row_data['gmts_item_id']];   ?></td>
	                    <td width="130" align="center"><? echo $sub_process_name;//chop($embellishment_type,','); ?></td>
	                    <td width="110"  align="right"><p><a href='#report_details' onClick="openmypage_receive('<? echo $row_data['job_no']; ?>','<? echo $row_data['gmts_color_id']; ?>','<? echo $row_data['gmts_item_id'];?>','<? echo $row_data['order_no']; ?>','<? echo $party_id;?>','<? echo $row_data['buyer_style_ref'];?>','receive_popup');"><? echo number_format($receeive_qty,2);  ?> </a></p></td>
						<td width="110"  align="right"><p><a href='#report_details' onClick="openmypage_delivery('<? echo $row_data['job_no']; ?>','<? echo $row_data['gmts_color_id']; ?>','<? echo $row_data['gmts_item_id'];?>','<? echo $row_data['order_no']; ?>','<? echo $party_id;?>','<? echo $row_data['buyer_style_ref']; ?>','delivery_popup');"><? echo number_format($delivery_qty,2);  ?> </a></p></td>
						<td width="110"  align="right"><?  echo number_format($delivery_qty_dzn,2);   ?></td>
	                    <td width="110"  align="right"><?  echo number_format($rate,4);   ?></td>
	                    <td width="110"  align="right"><?  echo number_format($delivery_amount_usd,2);   ?></td>
						<td width="200"  align="center"></td>
					</tr>
					<?
	        		$k++;
					$sl++;
					 $total_balance_quantity+=$delivery_qty;
				     $total_receeive_qty+=$receeive_qty;
				     $total_delivery_qty_dzn+=$delivery_qty_dzn;		
				     $total_delivery_amount_usd+=$delivery_amount_usd;


				     

				 	}

				}

        	}
				
								
        
       	 }
		 
		 ?>
        <tr>
            <td colspan="8" align="right"><b>Total: </b></td>
            <td align="right"><b><? echo number_format($total_receeive_qty,2); ?></b></td>
            <td align="right"><b><? echo number_format($total_balance_quantity,2); ?></b></td>
            <td align="right"><b><? echo number_format($total_delivery_qty_dzn,2); ?></b></td>
            <td align="right">&nbsp;</td>
            <td align="right"><b><? echo number_format($total_delivery_amount_usd,2); ?></b></td>
            <td align="right">&nbsp;</td>
        </tr>
        
        <?
         $grand_total_balance_quantity+=$total_balance_quantity;
	     $grand_total_receeive_qty+=$total_receeive_qty;
	     $grand_total_delivery_qty_dzn+=$total_delivery_qty_dzn;		
	     $grand_total_delivery_amount_usd+=$total_delivery_amount_usd;
		}
        ?>
        <tr>
            <td colspan="8" align="right"><b>Grand Total: </b></td>
            <td align="right"><b><? echo number_format($grand_total_receeive_qty,2); ?></b></td>
            <td align="right"><b><? echo number_format($grand_total_balance_quantity,2); ?></b></td>
            <td align="right"><b><? echo number_format($grand_total_delivery_qty_dzn,2); ?></b></td>
            <td align="right">&nbsp;</td>
            <td align="right"><b><? echo number_format($grand_total_delivery_amount_usd,2); ?></b></td>
            <td align="right">&nbsp;</td>
        </tr>
        </tbody>			               
        </table>
        </div>
    <?
	

    /*$rID2=execute_query("delete from tmp_poid where userid=$user_id and type=259");

	if($db_type==0) {
		if($rID2) {
			mysql_query("COMMIT");
		}
	} else {
		if($rID2) {
			oci_commit($con);
		}
	}
	disconnect($con);*/

    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
    exit();
}

if($action=="receive_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id;
	$buyer_library=return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(9,20) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id","supplier_name");
	?>
	<script>
		function print_window()
		{
			$(".flt").css("display","none");
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			$(".flt").css("display","block");
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
		}
		var tableFilters =
		{
			col_14: "none",
			col_operation:
			 {
				id: ["value_total_balance"],
				col: [4],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
	</script>
	<fieldset style="width:500px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div id="report_container" style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="480" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Wash Received Info</b>
				</caption>
				<thead>
					<th width="30">Sl</th>
					<th width="100">Receive No</th>
					<th width="100">Year</th>
					<th width="100">Party</th>
					<th>Receive Quantity</th>
				</thead>
			</table>
			<div style="width:500px; overflow-y:scroll; max-height:250px;font-size:12px;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="480" cellpadding="0" cellspacing="0" id="table_body">
 
		<?
		
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		 
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
 	}
	
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	
	
	
	
	
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	if($db_type==0)
	{
		if( $from_date==0 && $to_date==0 ) $subcon_date=""; else $subcon_date= " and a.subcon_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
			if( $from_date==0 && $to_date==0 ) $delivery_date=""; else $delivery_date= " and a.delivery_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";	
 	}
	if($db_type==2)
	{
		if( $from_date==0 && $to_date==0 ) $subcon_date=""; else $subcon_date= " and a.subcon_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
		if( $from_date==0 && $to_date==0 ) $delivery_date=""; else $delivery_date= " and a.delivery_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
 	
	}
	
	
		if($buyer_style_ref!=""){ $buyerstyle_ref=" and c.buyer_style_ref='$buyer_style_ref'";}
	
	
		
			//job_no,gmts_color_id,gmts_item_id,order_no,party_id buyer_style_ref		 
        	  $sql="select a.id,a.sys_no, a.subcon_date, a.chalan_no, a.embl_job_no, b.id as details_id ,b.quantity, b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name,b.remarks, $ins_year_cond as year
		from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c,subcon_ord_mst d
		where a. entry_form = 296 and a.trans_type=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0   and a.id=b.mst_id and  b.job_dtls_id=c.id  and c.mst_id=d.id and a.embl_job_no='$job_no' and  a.company_id=$companyID  and c.gmts_item_id='$gmts_item_id' and  c.gmts_color_id='$gmts_color_id' and c.order_no ='$order_no'  $buyerstyle_ref  and a.party_id='$party_id'  $subcon_date  order by a.subcon_date desc";
		
		/*$sql="select a.id,a.sys_no, a.subcon_date, a.chalan_no, a.embl_job_no, b.id as details_id ,b.quantity, b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name,b.remarks, $ins_year_cond as year
		from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c,subcon_ord_mst d
		where a. entry_form = 296 and a.trans_type=1 and a.is_deleted = 0 and a.id=b.mst_id and  b.job_dtls_id=c.id  and c.mst_id=d.id and a.embl_job_no='$job_no' and  a.company_id=$companyID  and c.gmts_item_id='$gmts_item_id' and  c.gmts_color_id='$gmts_color_id' and c.order_no ='$order_no'  and c.buyer_style_ref='$buyer_style_ref' and a.party_id='$party_id'  $subcon_date  order by a.subcon_date desc";*/
        
					//echo $sql; die;
					$result = sql_select($sql);
         
					?>
					<tbody>
						<?
						$i = 1;
						foreach($result as $row)
						{
							?>
							<tr>
								<td width="30"><? echo $i;?></td>
 								<td width="100" title="<? echo $row[csf("sys_no")];?>"><? echo $row[csf("sys_no")];?></td>
								<td width="100"><? echo $row[csf("year")];?></td>
								<td width="100"><? echo $party_arr[$row[csf("party_id")]]; ?></td>
								<td  align="right"><? echo number_format($row[csf("quantity")],2);?></td>
							</tr>
							<?
							$i++;
							$total_balance += $row[csf("quantity")];
						}
						?>
					</tbody>
					<tfoot>
						<th colspan="4">Total:</th>
						<th align="right" id="total_balance"><? echo number_format($total_balance,2); ?></th>
					</tfoot>

				</table>
			</div>

		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1,tableFilters);</script>
	<?
	exit();
}

 if($action=="delivery_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id;
	$buyer_library=return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(9,20) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id","supplier_name");
	?>
	<script>
		function print_window()
		{
			$(".flt").css("display","none");
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			$(".flt").css("display","block");
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
		}
		var tableFilters =
		{
			col_14: "none",
			col_operation:
			 {
				id: ["value_total_balance"],
				col: [4],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
	</script>
	<fieldset style="width:500px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div id="report_container" style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="480" cellpadding="0" cellspacing="0" id="table_header">
				<caption>
					<b>Wash Delivery Info</b>
				</caption>
				<thead>
					<th width="30">Sl</th>
					<th width="100">Delivery No</th>
					<th width="100">Year</th>
					<th width="100">Party</th>
					<th>Delivery Quantity</th>
				</thead>
			</table>
			<div style="width:500px; overflow-y:scroll; max-height:250px;font-size:12px;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="480" cellpadding="0" cellspacing="0" id="table_body">
 
		<?
		
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		 
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
 	}
	
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	
		$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	if($db_type==0)
	{
		if( $from_date==0 && $to_date==0 ) $subcon_date=""; else $subcon_date= " and a.subcon_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
			if( $from_date==0 && $to_date==0 ) $delivery_date=""; else $delivery_date= " and a.delivery_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";	
 	}
	if($db_type==2)
	{
		if( $from_date==0 && $to_date==0 ) $subcon_date=""; else $subcon_date= " and a.subcon_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
		if( $from_date==0 && $to_date==0 ) $delivery_date=""; else $delivery_date= " and a.delivery_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
 	
	}
			//job_no,gmts_color_id,gmts_item_id,order_no,party_id buyer_style_ref		 
        	/*$sql="select a.id,a.sys_no, a.subcon_date, a.chalan_no, a.embl_job_no, b.id as details_id ,b.quantity, b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name,b.remarks, $ins_year_cond as year
		from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c,subcon_ord_mst d
		where a. entry_form = 296 and a.trans_type=1 and a.is_deleted = 0 and a.id=b.mst_id and  b.job_dtls_id=c.id  and c.mst_id=d.id and a.embl_job_no='$job_no' and  a.company_id=$companyID  and c.gmts_item_id='$gmts_item_id' and  c.gmts_color_id='$gmts_color_id' and c.order_no ='$order_no'  and c.buyer_style_ref='$buyer_style_ref' and a.party_id='$party_id'  order by a.subcon_date desc";*/
		
			if($buyer_style_ref!=""){ $buyerstyle_ref=" and c.buyer_style_ref='$buyer_style_ref'";}
        
		
		 $do_sql="select a.id,a.delivery_no, a.delivery_date,a.party_id, a.job_no,$ins_year_cond as year, b.id as details_id ,b.delivery_qty, b.order_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,b.remarks
			from subcon_delivery_mst a, subcon_delivery_dtls b,subcon_ord_dtls c,subcon_ord_mst d
			where a.entry_form = 303 and a.is_deleted = 0  and a.id=b.mst_id and  b.order_id=c.id and c.mst_id=d.id and a.job_no='$job_no' and  a.company_id=$companyID  and c.gmts_item_id='$gmts_item_id' and  c.gmts_color_id='$gmts_color_id' and c.order_no ='$order_no'  $buyerstyle_ref and a.party_id='$party_id'   order by a.delivery_date desc"; 
		
					//echo $do_sql; die;
					$result = sql_select($do_sql);
         
					?>
					<tbody>
						<?
						$i = 1;
						foreach($result as $row)
						{
							?>
							<tr>
								<td width="30"><? echo $i;?></td>
 								<td width="100" title="<? echo $row[csf("delivery_no")];?>"><? echo $row[csf("delivery_no")];?></td>
								<td width="100"><? echo $row[csf("year")];?></td>
								<td width="100"><? echo $party_arr[$row[csf("party_id")]]; ?></td>
								<td  align="right"><? echo number_format($row[csf("delivery_qty")],2);?></td>
							</tr>
							<?
							$i++;
							$total_balance += $row[csf("delivery_qty")];
						}
						?>
					</tbody>
					<tfoot>
						<th colspan="4">Total:</th>
						<th align="right" id="total_balance"><? echo number_format($total_balance,2); ?></th>
					</tfoot>

				</table>
			</div>

		</div>
	</fieldset>
	<script>setFilterGrid('table_body',-1,tableFilters);</script>
	<?
	exit();
}

?>