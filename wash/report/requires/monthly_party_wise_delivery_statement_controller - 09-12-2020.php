<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');



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
		if( $from_date==0 && $to_date==0 ) $subcon_date=""; else $subcon_date= " and a.delivery_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
			if( $from_date==0 && $to_date==0 ) $delivery_date=""; else $delivery_date= " and a.delivery_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";	
 	}
	if($db_type==2)
	{
		if( $from_date==0 && $to_date==0 ) $subcon_date=""; else $subcon_date= " and a.delivery_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";	
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

	 $do_sql="select a.id,a.delivery_no, a.delivery_date, a.job_no, b.id as details_id ,b.delivery_qty, b.order_id,b.remarks,c.rate,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,d.party_id,d.order_no,c.buyer_buyer,c.party_buyer_name,e.embellishment_type,e.process,c.id as job_dtls_id,c.order_uom
from subcon_delivery_mst a, subcon_delivery_dtls b,subcon_ord_dtls c,subcon_ord_mst d,subcon_ord_breakdown e 
where a.entry_form = 303  and a.id=b.mst_id and  b.order_id=c.id and c.mst_id=d.id  and c.id=e.mst_id   $company_name  $party_con $within_group   $delivery_date $search_com_cond"; 


		$do_result = sql_select($do_sql);
        $do_all_data=array();
        foreach($do_result as $row)
        {
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['id']=$row[csf('id')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['party_buyer_name']=$row[csf('party_buyer_name')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['order_no']=$row[csf('order_no')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['job_no']=$row[csf('job_no')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['gmts_item_id']=$row[csf('gmts_item_id')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['delivery_qty']=$row[csf('delivery_qty')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['rate']=$row[csf('rate')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['buyer_style_ref']=$row[csf('buyer_style_ref')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['chalan_no']=$row[csf('chalan_no')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['remarks']=$row[csf('remarks')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['party_id']=$row[csf('party_id')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['order_no']=$row[csf('order_no')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['details_id']=$row[csf('details_id')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['job_dtls_id']=$row[csf('job_dtls_id')];
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['embellishment_type'].=$row[csf('embellishment_type')].',';
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['process'].=$row[csf('process')].',';
			$do_all_data[$row[csf('party_id')]][$row[csf('buyer_style_ref')]][$row[csf('job_no')]]['order_uom']=$row[csf('order_uom')];
        }
		
		//echo "<pre>";
		//print_r($do_all_data);
		
		$receeive_qty_array=array();
		 $sql_receeive="Select b.job_dtls_id,a.subcon_date,b.quantity as receive_qnty from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.entry_form=296 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$sql_receeive_result=sql_select($sql_receeive); $receeive_date_array=array(); 
		foreach ($sql_receeive_result as $row)
		{
			$receeive_qty_array[$row[csf('job_dtls_id')]]['receive_qnty']+=$row[csf('receive_qnty')];
		}
			
		
        foreach($do_all_data as $party_id=>$party_data) //$pay_term
        {
			 $k=1;
			 $sl=1;
        ?>
        <table style="width:1200px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
        <tr>
       	 <td colspan="12" style="font-size:24px"><b>Party Name: <? echo $party_arr[$party_id];?></b></td>
        </tr>
        </table>               	
        <table style="width:1200px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="scroll_body" >
        <thead>
            <th width="30" >SL</th>
            <th width="120" >Party Buyer</th>
            <th width="130" >Order No</th>
             <th width="130" >Style No</th>
            <th width="130" >Job No</th>
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
        $total_balance_quantity=0; $total_delivery_qty_dzn=0; $total_delivery_amount_usd=0;$total_receeive_qty=0;
        foreach($party_data as $buyer_style_ref_id=>$buyer_style_ref_data)
        {
			foreach($buyer_style_ref_data as $job_no_id=>$row_data)
			{	
				
				
										
					if ($k%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					
					
					if($row_data['order_uom']==1)
					{
						$rate=$row_data['rate']*12;
						$delivery_qty_dzn=$row_data['delivery_qty']/12;
						$delivery_amount_usd=($row_data['delivery_qty']/12)*$rate;
						$delivery_qty=$row_data['delivery_qty'];
					}
					if($row_data['order_uom']==2)
					{
						$rate=$row_data['rate'];
						$delivery_qty_dzn=$row_data['delivery_qty']/12;
						$delivery_amount_usd=($row_data['delivery_qty']/12)*$rate;
						$delivery_qty=$row_data['delivery_qty'];
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
					$receeive_qty=$receeive_qty_array[$row_data['job_dtls_id']]['receive_qnty'];
				?>
				
				<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
					<td width="30" ><?php echo $sl; ?></td>
					<td width="120" align="center" title="<? echo $row_data['party_buyer_name'] ?>"><? echo $row_data['party_buyer_name']; ?></td>
					<td width="130" align="center"><? echo $row_data['order_no'];   ?></td>
					<td width="130"  align="center"><? echo $row_data['buyer_style_ref'];  ?></td>
                    <td width="130"  align="center"><? echo $row_data['job_no'];  ?></td>
                    <td width="130" align="center"><? echo $garments_item[$row_data['gmts_item_id']];   ?></td>
                    <td width="130" align="center"><? echo chop($embellishment_type,','); ?></td>
                    <td width="110"  align="right"><? echo number_format($receeive_qty,2);  ?></td>
					<td width="110"  align="right"><? echo number_format($delivery_qty,2);  ?></td>
					<td width="110"  align="right"><?  echo number_format($delivery_qty_dzn,2);   ?></td>
                    <td width="110"  align="right"><?  echo number_format($rate,4);   ?></td>
                    <td width="110"  align="right"><?  echo number_format($delivery_amount_usd,2);   ?></td>
					<td width="200"  align="center"></td>
				</tr>
				<?
        		$k++;
				$sl++;
			
        	}
				
			$total_balance_quantity+=$delivery_qty;
			$total_receeive_qty+=$receeive_qty;
			$total_delivery_qty_dzn+=$delivery_qty_dzn;		
			$total_delivery_amount_usd+=$delivery_amount_usd;					
        
       	 }
		 
		 ?>
        <tr>
            <td colspan="7" align="right"><b>Total: </b></td>
            <td align="right"><b><? echo number_format($total_receeive_qty,2); ?></b></td>
            <td align="right"><b><? echo number_format($total_balance_quantity,2); ?></b></td>
            <td align="right"><b><? echo number_format($total_delivery_qty_dzn,2); ?></b></td>
            <td align="right">&nbsp;</td>
            <td align="right"><b><? echo number_format($total_delivery_amount_usd,2); ?></b></td>
            <td align="right">&nbsp;</td>
        </tr>
        <?
		}
        ?>
        </tbody>			               
        </table>
        </div> 
   
   	
    <?
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

?>