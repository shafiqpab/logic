<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');


if($action=="print_button_variable_setting")
{
	 
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in($data) and module_id=20 and report_id=153 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit(); 
}


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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $company_id ?>+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+<? echo $cbo_within_group;?>, 'create_job_search_list_view', 'search_div', 'wash_received_and_delivery_statement_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num = '$search_str'";  
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";  
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
	 where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $company $party_id_cond $withinGroup $search_com_cond and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  
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
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('job_no_prefix_num')]; ?>')" style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
                    <td width="130" style="word-break:break-all"><? if ($within_group==1)echo $buyer_po; else echo $row[csf('buyer_po_no')];//echo $buyer_po; ?></td>
                    <td width="130" style="word-break:break-all"><? if ($within_group==1)echo $buyer_style; else echo $row[csf('buyer_style_ref')];//echo $buyer_style; ?></td>
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
	$cbo_job_year = str_replace("'", '', $cbo_job_year);

	/*if ($from_date == '' && $to_date == '') {
		$from_date = "01-Jan-$cbo_job_year";
		$to_date = "31-Dec-$cbo_job_year";
	}*/
	//echo $from_date."_".$to_date; die;
	$year_con='';
	if($db_type==0)
	{

		$year_con="and YEAR(d.insert_date)='".$cbo_job_year."'";
		$year_con1="and YEAR(a.insert_date)='".$cbo_job_year."'";
	}
	else if($db_type==2 || $db_type==0)
	{

		$year_con="and to_char(d.insert_date,'YYYY')='".$cbo_job_year."'";
		$year_con1="and to_char(a.insert_date,'YYYY')='".$cbo_job_year."'";
	}


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

			if($search_type==1) $search_com_cond1=" and b.buyer_po_no like '%$search_str'";
			else if($search_type==2) $search_com_cond1=" and b.buyer_style_ref like '%$search_str'";  
		}
		
		//echo $search_com_cond; die;
	
	if($cbo_buyer_id==0) $party_con=""; else $party_con=" and a.party_id='$cbo_buyer_id'";
	if($cbo_within_group==0) $within_group=""; else $within_group=" and d.within_group='$cbo_within_group'";
	if($cbo_within_group==0) $within_group1=""; else $within_group1=" and a.within_group='$cbo_within_group'";
	
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
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and d.job_no_prefix_num in ('$job_no') ";
	if ($job_no=="") $job_no_cond1=""; else $job_no_cond1=" and a.job_no_prefix_num in ('$job_no')";
	if ($job_no=="") $del_job_no_cond=""; else $del_job_no_cond=" and a.job_no in ('$job_no') ";
	

	if ($from_date == '' && $to_date == '') {
    $sql_job="select a.id,a.company_id, a.subcon_job from subcon_ord_mst a, subcon_ord_dtls b
	where a.id=b.mst_id  and a.status_active =1 and a.is_deleted=0  and b.status_active =1 and b.is_deleted=0   $company_name $search_com_cond1  $within_group1  $party_con $job_no_cond1 $year_con1 order by a.id desc";
	//echo $sql_job; die;
		$job_result = sql_select($sql_job);
	    $job_data=array();
	    foreach($job_result as $row)
	    {
	    	$job_data[$row[csf('id')]]=$row[csf('id')];
	    }

	    $job_ids = implode(",", $job_data);
	    if ($job_ids!='') {
	    	$job_ids=$job_ids;
	    }else{
	    	echo "Job No Not Found"; die;
	    }
	    
    }
  	
	if($db_type==0)
	{
		if( $from_date=='' && $to_date=='' ) $subcon_date=""; else $subcon_date= " and a.subcon_date  between '".change_date_format(str_replace("'", '', $from_date),'yyyy-mm-dd')."' and '".change_date_format(str_replace("'", '', $to_date),'yyyy-mm-dd')."'";
			if( $from_date=='' && $to_date=='' ) $delivery_date=""; else $delivery_date= " and a.delivery_date  between '".change_date_format(str_replace("'", '', $from_date),'yyyy-mm-dd')."' and '".change_date_format(str_replace("'", '', $to_date),'yyyy-mm-dd')."'";	
 	}
	if($db_type==2)
	{
		if( $from_date=='' && $to_date=='' ) $subcon_date=""; else $subcon_date= " and a.subcon_date  between '".change_date_format(str_replace("'", '', $from_date),'','',1)."' and '".change_date_format(str_replace("'", '', $to_date),'','',1)."'";	
		if( $from_date=='' && $to_date=='' ) $delivery_date=""; else $delivery_date= " and a.delivery_date  between '".change_date_format(str_replace("'", '', $from_date),'','',1)."' and '".change_date_format(str_replace("'", '', $to_date),'','',1)."'";	
 	
	}



	if ($from_date == '' && $to_date == '') 
	{
        $buyer_sql="select a.id,a.sys_no, a.subcon_date, a.chalan_no, a.embl_job_no, b.id as details_id, b.quantity, b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name, c.id as sub_ord_dtls_id
	from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c,subcon_ord_mst d
	where a.entry_form = 296 and a.trans_type=1 and a.id=b.mst_id and b.job_dtls_id=c.id and c.mst_id=d.id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0 and  d.id in($job_ids) $company_name $search_com_cond $within_group $party_con $job_no_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
        }
        else 
        {
		  $buyer_sql="select a.id,a.sys_no, a.subcon_date, a.chalan_no, a.embl_job_no, b.id as details_id, b.quantity, b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name, c.id as sub_ord_dtls_id
	from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c,subcon_ord_mst d
	where a.entry_form = 296 and a.trans_type=1 and a.id=b.mst_id and b.job_dtls_id=c.id and c.mst_id=d.id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0 $company_name $search_com_cond $within_group $party_con $job_no_cond $subcon_date and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0"; 
		}


	 
	//$year_con
	
		// echo $buyer_sql;
		$buyer_result = sql_select($buyer_sql);
        $buyer_data=array();
        // $ordNoArr = array();
        foreach($buyer_result as $row)
        {
        	$job_no = $row[csf('embl_job_no')];
			
			$buyer_data[$row[csf('embl_job_no')]]['gmts_item_id'].=$garments_item[$row[csf('gmts_item_id')]].",";
			$buyer_data[$row[csf('embl_job_no')]]['gmts_color_id']=$row[csf('gmts_color_id')];
			$buyer_data[$row[csf('embl_job_no')]]['buyer_po_no'].=$row[csf('buyer_po_no')].",";;
			$buyer_data[$row[csf('embl_job_no')]]['buyer_style_ref'].=$row[csf('buyer_style_ref')].",";;
			$buyer_data[$row[csf('embl_job_no')]]['party_id'].=$party_arr[$row[csf('party_id')]].",";		        
			$buyer_data[$row[csf('embl_job_no')]]['buyer_buyer'].=$row[csf('party_buyer_name')].",";
			// $buyer_data[$row[csf('embl_job_no')]]['order_quantity'] += $row[csf('order_quantity')];
			$ordNo = $row[csf('order_no')];
        }

    $ordDtlsIds = implode(',', array_unique($ordNoArr));
    $orderQtySql = sql_select("select sum(order_quantity) as order_qty from subcon_ord_dtls where order_no='$ordNo' and  status_active =1 and  is_deleted=0");
    // echo "select sum(order_quantity) as order_qty from subcon_ord_dtls where id in($ordDtlsIds)";
		$buyer_po=$buyer_data[$job_no]['buyer_po_no'];
		$buyer_po_no=rtrim(implode(",",array_unique(explode(",",$buyer_po))));
		$buyer_style=$buyer_data[$job_no]['buyer_style_ref'];
		$buyer_style_ref=rtrim(implode(",",array_unique(explode(",",$buyer_style))));
		$party_id_arr=$buyer_data[$job_no]['party_id'];
		$party_id=implode(",",array_unique(explode(",",$party_id_arr)));
		$gmts_item_id_arr=$buyer_data[$job_no]['gmts_item_id'];
		$gmts_item_id=rtrim(implode(",",array_unique(explode(",",$gmts_item_id_arr))));
		$buyer_buyer_arr=$buyer_data[$job_no]['buyer_buyer'];
		$buyer_buyer_id=rtrim(implode(",",array_unique(explode(",",$buyer_buyer_arr))));
		$orderQty = $orderQtySql[0][csf('order_qty')];
			
	ob_start();
	?>
    
    <div id="mstDiv" align="center">
        <table style="width:800px"> 
            <?
            $company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$company_id."");
            foreach( $company_library as $row)
            {
                $company=$row[csf('company_name')];
            ?>
            <tr>
            <td colspan="7" align="center" style="font-size:22px"><? echo $row[csf('company_name')];?></td>
            </tr>
            <?
            }
            ?>
            <tr>
            <td colspan="7" align="center" style="font-size:20px"><? echo $report_title; ?></td>
            </tr>
            <tr style="border:none;">
                <td colspan="7" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if($from_date!="" || $to_date!="")echo "Date Range : ".change_date_format($from_date)."  to  ".change_date_format($to_date)."" ;?>
                </td>
            </tr>
           
        </table>
        <?
        if ($from_date == '' && $to_date == '') 
		{
        	$sql="select a.id,a.sys_no, a.subcon_date, a.chalan_no, a.embl_job_no, b.id as details_id ,b.quantity, b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name,b.remarks
		from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c,subcon_ord_mst d
		where a. entry_form = 296 and a.trans_type=1 and a.is_deleted = 0 and a.id=b.mst_id and  b.job_dtls_id=c.id  and c.mst_id=d.id and d.id in($job_ids) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0 $company_name $search_com_cond  $within_group  $party_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  order by a.subcon_date desc";
        }
        else 
        {
		  $sql="select a.id,a.sys_no, a.subcon_date, a.chalan_no, a.embl_job_no, b.id as details_id ,b.quantity, b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name,b.remarks
		from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c,subcon_ord_mst d
		where a. entry_form = 296 and a.trans_type=1 and a.is_deleted = 0 and a.id=b.mst_id and  b.job_dtls_id=c.id  and c.mst_id=d.id  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0 $company_name $search_com_cond  $within_group  $party_con $job_no_cond $subcon_date and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by a.subcon_date desc"; 
		}
		//echo $sql; die;
		$result = sql_select($sql);
        $all_data=array();
        foreach($result as $row)
        {
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['id']=$row[csf('id')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['details_id']=$row[csf('details_id')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['job_dtls_id']=$row[csf('job_dtls_id')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['sys_no']=$row[csf('sys_no')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['subcon_date']=$row[csf('subcon_date')]; 
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['chalan_no']=$row[csf('chalan_no')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['embl_job_no']=$row[csf('embl_job_no')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['quantity']+=$row[csf('quantity')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['remarks']=$row[csf('remarks')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['gmts_item_id'].=$garments_item[$row[csf('gmts_item_id')]].",";;
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['gmts_color_id']=$row[csf('gmts_color_id')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['order_no']=$row[csf('order_no')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['remarks']=$row[csf('remarks')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['buyer_po_no'].=$row[csf('buyer_po_no')].",";;
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['buyer_style_ref'].=$row[csf('buyer_style_ref')].",";;
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['party_id'].=$party_arr[$row[csf('party_id')]].",";		   
			$all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['buyer_buyer'].=$row[csf('party_buyer_name')].",";
		 
			$job_dtls_id_arr[$row[csf('job_dtls_id')]]=$row[csf('job_dtls_id')];
			$order_no_arr[$row[csf('order_no')]]=$row[csf('order_no')];
			$material_dtls_arr[$row[csf('details_id')]]=$row[csf('details_id')];
			$material_mst_arr[$row[csf('id')]]=$row[csf('id')];
        }
		
		?>
        <table style="width:800px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
        
            <tr>
                 <td width="80"><b>Job No:</b></td>
                 <td align="left"><? echo $job_no;?></td>
            </tr>
            <tr>
                 <td width="80"><b>Party:</b></td>
                 <td align="left"><? echo chop($party_id,',');?></td>
            </tr>
            <tr>
                 <td width="80"><b>Party Buyer:</b> </td>
                 <td align="left"><? echo chop($buyer_buyer_id,',');?></td>
            </tr>
            <tr>
                 <td width="80"><b>Buyer style:</b></td>
                 <td align="left"><? echo chop($buyer_style_ref,',');?></td>
            </tr>
            <tr>
                <td width="80"><b>Gmts. Item:</b></td>
                <td align="left"><? echo chop($gmts_item_id,',');?></td>
            </tr>
            <tr>
                <td width="80"><b>Order Qty:</b></td>
                <td align="left"><?php echo number_format($orderQty); ?></td>
            </tr>
        </table> 
        
        <table>
            <tr>
                 <td colspan="7" style="font-size:22px" align="center"></td>
            </tr>
        </table> 
        <table style="width:800px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <tr>
                 <td colspan="7" style="font-size:22px" align="center"><b>Received</b></td>
            </tr>
        </table> 
        <?
        foreach($all_data as $gmts_color_id=>$gmts_color_data) //$pay_term
        {
			foreach($gmts_color_data as $byer_po_id=>$byer_po_data) //$pay_term
			{
				 $k=1;
				 $sl=1;
			?>
			<table style="width:800px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<tr>
			 <td colspan="3" style="font-size:14px"><b>Color: <? echo $color_library_arr[$gmts_color_id];?></b></td>
             <td colspan="4" style="font-size:14px"><b>Buyer PO No: <? echo $byer_po_id;?></b></td>
			</tr>
			</table>               	
			<table style="width:800px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="scroll_body" >
			<thead>
				<th width="30">SL</th>
				<th width="120">Date</th>
				<th width="130">Challan No</th>
				<th width="130">System Challan No</th> 
				<th width="110">Qty</th>
				<th width="110">Total Qty</th>
				<th width="200">Remarks</th>
			</thead>
			<tbody>
			<?
			$total_balance_value=0; $total_balance_quantity=0;
			foreach($byer_po_data as $subcon_date_id=>$subcon_date_data)
			{
				foreach($subcon_date_data as $chalan_no_id=>$row_data)
				{	
											
					if ($k%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					
					if($sl==1)
					{
						$total_balance_value=$row_data['quantity'];
					}
					else
					{
						$total_balance_value+=$row_data['quantity'];
					}
					?>
					
					<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
						<td width="30" ><?php echo $sl; ?></td>
						<td width="120" align="center" title="<? echo $row_data['embl_job_no'] ?>"><? echo change_date_format($row_data['subcon_date']); ?></td>
						<td width="130" align="center"><? echo $row_data['chalan_no'];   ?></td>
						<td width="130"  align="center"><? echo $row_data['sys_no'];  ?></td>
						<td width="110"  align="right"><? echo number_format($row_data['quantity'],2);  ?></td>
						<td width="110"  align="right"><?  echo number_format($total_balance_value,2);   ?></td>
						<td width="200"  align="center"><? echo $row_data['remarks'];  ?></td>
					</tr>
					<?
					$k++;
					$sl++;
				
				}
					
				$total_balance_quantity+=$row_data['quantity'];
				$total_balance_value=$total_balance_value;;							
			
			 }
			 
			 ?>
			<tr>
				<td colspan="5" align="right"><b>Buyer PO Total: </b></td>
				<td align="right"><b><? echo number_format($total_balance_value,2); ?></b></td>
				<td align="right">&nbsp;</td>
			</tr>
			<?
			$total_diff_quantity+=$total_balance_quantity;
			$total_value+=$total_balance_value;
			} 
		}
        ?>
        <tr>
        <td colspan="5" align="right"><b>G.Total: </b></td>
        <td align="right"><b><? echo number_format($total_value,2); ?></b></td>
        <td align="right">&nbsp;</td>
        </tr>
        </tbody>			               
        </table>
        </div> 
   
   	<table style="width:800px">
            <tr>
                 <td colspan="7" style="font-size:22px" align="center">&nbsp;</td>
            </tr>
        </table> 


        <!-- Received Return -->

        <?

        if ($from_date == '' && $to_date == '') {
		  $sql_return="select a.id,a.sys_no, a.subcon_date, a.chalan_no, a.embl_job_no, b.id as details_id ,b.quantity, b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name,b.remarks
		from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c,subcon_ord_mst d
		where a. entry_form = 372 and a.trans_type=3 and a.is_deleted = 0 and a.id=b.mst_id and  b.job_dtls_id=c.id  and c.mst_id=d.id and d.id in($job_ids) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0 $company_name $search_com_cond  $within_group $party_con  order by a.subcon_date desc"; 

		}
		else
		{
			$sql_return="select a.id,a.sys_no, a.subcon_date, a.chalan_no, a.embl_job_no, b.id as details_id ,b.quantity, b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name,b.remarks
		from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c,subcon_ord_mst d
		where a. entry_form = 372 and a.trans_type=3 and a.is_deleted = 0 and a.id=b.mst_id and  b.job_dtls_id=c.id  and c.mst_id=d.id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0 $company_name $search_com_cond  $within_group  $party_con $job_no_cond $subcon_date order by a.subcon_date desc";
		}
	//echo $sql_return; die;

		$result_return = sql_select($sql_return);
        $all_return_data=array();
        foreach($result_return as $row)
        {
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['id']=$row[csf('id')];
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['details_id']=$row[csf('details_id')];
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['job_dtls_id']=$row[csf('job_dtls_id')];
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['sys_no']=$row[csf('sys_no')];
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['subcon_date']=$row[csf('subcon_date')]; 
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['chalan_no']=$row[csf('chalan_no')];
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['embl_job_no']=$row[csf('embl_job_no')];
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['quantity']+=$row[csf('quantity')];
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['remarks']=$row[csf('remarks')];
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['gmts_item_id'].=$garments_item[$row[csf('gmts_item_id')]].",";;
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['gmts_color_id']=$row[csf('gmts_color_id')];
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['order_no']=$row[csf('order_no')];
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['remarks']=$row[csf('remarks')];
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['buyer_po_no'].=$row[csf('buyer_po_no')].",";;
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['buyer_style_ref'].=$row[csf('buyer_style_ref')].",";;
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['party_id'].=$party_arr[$row[csf('party_id')]].",";		   
			$all_return_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('subcon_date')]][$row[csf('sys_no')]]['buyer_buyer'].=$row[csf('party_buyer_name')].",";
		 
			$job_dtls_id_arr[$row[csf('job_dtls_id')]]=$row[csf('job_dtls_id')];
			$order_no_arr[$row[csf('order_no')]]=$row[csf('order_no')];
			$material_dtls_arr[$row[csf('details_id')]]=$row[csf('details_id')];
			$material_mst_arr[$row[csf('id')]]=$row[csf('id')];
        }
		
		?>
        
        <div id="mstDiv" align="center">
        <table>
            <tr>
                 <td colspan="7" style="font-size:22px" align="center"></td>
            </tr>
        </table> 
        <table style="width:800px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <tr>
                 <td colspan="7" style="font-size:22px" align="center"><b>Received Return</b></td>
            </tr>
        </table> 
        <?
       
        foreach($all_return_data as $gmts_color_id=>$gmts_color_data) //$pay_term
        {
			foreach($gmts_color_data as $byer_po_id=>$byer_po_data) //$pay_term
			{
			 $k=1;
			 $sl=1;
        ?>
        <table style="width:800px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
        <tr>
       	 <td colspan="3" style="font-size:14px"><b>Color: <? echo $color_library_arr[$gmts_color_id];?></b></td>
         <td colspan="4" style="font-size:14px"><b>Buyer PO No: <? echo $byer_po_id;?></b></td>
        </tr>
        </table>               	
        <table style="width:800px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="scroll_body" >
        <thead>
            <th width="30">SL</th>
            <th width="120">Date</th>
            <th width="130">Challan No</th>
            <th width="130">System Challan No</th> 
            <th width="110">Qty</th>
            <th width="110">Total Qty</th>
            <th width="200">Remarks</th>
        </thead>
        <tbody>
        <?
        $total_balance_value=0; $total_balance_quantity=0;
        foreach($byer_po_data as $subcon_date_id=>$subcon_date_data)
        {
			foreach($subcon_date_data as $chalan_no_id=>$row_data)
			{	
										
				if ($k%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
				
				if($sl==1)
				{
					$total_balance_value=$row_data['quantity'];
				}
				else
				{
					$total_balance_value+=$row_data['quantity'];
				}
				?>
				
				<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
					<td width="30" ><?php echo $sl; ?></td>
					<td width="120" align="center" title="<? echo $row_data['embl_job_no'] ?>"><? echo change_date_format($row_data['subcon_date']); ?></td>
					<td width="130" align="center"><? echo $row_data['chalan_no'];   ?></td>
					<td width="130"  align="center"><? echo $row_data['sys_no'];  ?></td>
					<td width="110"  align="right"><? echo number_format($row_data['quantity'],2);  ?></td>
					<td width="110"  align="right"><?  echo number_format($total_balance_value,2);   ?></td>
					<td width="200"  align="center"><? echo $row_data['remarks'];  ?></td>
				</tr>
				<?
        		$k++;
				$sl++;
			
        	}
				
			$total_balance_quantity+=$row_data['quantity'];
			$total_balance_value=$total_balance_value;;							
        
       	 }
		 
		 ?>
        <tr>
            <td colspan="5" align="right"><b>Buyer Total: </b></td>
            <td align="right"><b><? echo number_format($total_balance_value,2); ?></b></td>
            <td align="right">&nbsp;</td>
        </tr>
        <?
        $total_diff_quantity+=$total_balance_quantity;
        $total_value_return+=$total_balance_value;
		} }
        ?>
        <tr>
        <td colspan="5" align="right"><b>G.Total: </b></td>
        <td align="right"><b><? echo number_format($total_value_return,2); ?></b></td>
        <td align="right">&nbsp;</td>
        </tr>
        </tbody>			               
        </table>
        </div> 
   
   	<table style="width:800px">
            <tr>
                 <td colspan="7" style="font-size:22px" align="center">&nbsp;</td>
            </tr>
        </table> 

<!-- 	Delivery -->

   		     
    <div id="mstDiv" align="center">
    
		<table style="width:800px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <tr>
                 <td colspan="7" style="font-size:22px" align="center" ><b>Delivery</b></td>
            </tr>
        </table> 
        <?

        if ($from_date == '' && $to_date == '') 
		{
		  $do_sql="select a.id,a.delivery_no, a.delivery_date, a.job_no, b.id as details_id ,b.delivery_qty, b.order_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,b.remarks,c.buyer_po_no
			from subcon_delivery_mst a, subcon_delivery_dtls b,subcon_ord_dtls c,subcon_ord_mst d
			where a.entry_form = 303 and a.is_deleted = 0  and a.id=b.mst_id and  b.order_id=c.id and c.mst_id=d.id and d.id in($job_ids) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0  $company_name  $party_con $within_group  order by a.delivery_date desc"; //$del_job_no_cond

		}
		else
		{
			$do_sql="select a.id,a.delivery_no, a.delivery_date, a.job_no, b.id as details_id ,b.delivery_qty, b.order_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,b.remarks,c.buyer_po_no
			from subcon_delivery_mst a, subcon_delivery_dtls b,subcon_ord_dtls c,subcon_ord_mst d
			where a.entry_form = 303 and a.is_deleted = 0  and a.id=b.mst_id and  b.order_id=c.id and c.mst_id=d.id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0  $company_name  $party_con $within_group  $job_no_cond $delivery_date order by a.delivery_date desc"; //$del_job_no_cond
		}


		$do_result = sql_select($do_sql);
        $do_all_data=array();
        foreach($do_result as $row)
        {
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['id']=$row[csf('id')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['details_id']=$row[csf('details_id')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['job_dtls_id']=$row[csf('order_id')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['sys_no']=$row[csf('delivery_no')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['delivery_date']=$row[csf('delivery_date')]; 
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['chalan_no']=$row[csf('chalan_no')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['job_no']=$row[csf('job_no')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['delivery_qty']+=$row[csf('delivery_qty')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['remarks']=$row[csf('remarks')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['gmts_item_id']=$row[csf('gmts_item_id')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['gmts_color_id']=$row[csf('gmts_color_id')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['order_no']=$row[csf('order_no')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('buyer_po_no')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['remarks']=$row[csf('remarks')];
		
			$job_dtls_id_arr[$row[csf('job_dtls_id')]]=$row[csf('order_id')];
			$order_no_arr[$row[csf('order_no')]]=$row[csf('order_no')];
			$do_dtls_arr[$row[csf('details_id')]]=$row[csf('details_id')];
			$do_mst_arr[$row[csf('id')]]=$row[csf('id')];
        }
		
        foreach($do_all_data as $gmts_color_id=>$gmts_color_data) //$pay_term
        {
			
			foreach($gmts_color_data as $byer_po_id3=>$byer_po_data) //$pay_term
			{
			 $k=1;
			 $sl=1;
        ?>
        <table style="width:800px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
        <tr>
       	<td colspan="3" style="font-size:14px"><b>Color: <? echo $color_library_arr[$gmts_color_id];?></b></td>
         <td colspan="4" style="font-size:14px"><b>Buyer PO No: <? echo $byer_po_id3;?></b></td>
        </tr>
        </table>               	
        <table style="width:800px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="scroll_body" >
        <thead>
        <th width="30" >SL</th>
        <th width="120" >Date</th>
        <th width="130" >System Challan No</th> 
        <th width="110" >Qty</th>
        <th width="110" >Total Qty</th>
        <th width="200" >Remarks</th>
        </thead>
        <tbody>
        <?
       
		 $do_total_balance_delivery_qty=0; $do_total_balance_value=0;
        foreach($byer_po_data as $subcon_date_id=>$subcon_date_data)
        {
			
			foreach($subcon_date_data as $chalan_no_id=>$row_data)
			{	
										
				if ($k%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
				
				if($sl==1)
				{
					$do_total_balance_value=$row_data['delivery_qty'];
				}
				else
				{
					$do_total_balance_value+=$row_data['delivery_qty'];
				}
				?>
				
				<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
					<td width="30" ><?php echo $sl; ?></td>
					<td width="120" align="center" title="<? echo $row_data['job_no'] ?>"><? echo change_date_format($row_data['delivery_date']); ?></td>
					<td width="130"  align="center"><? echo $row_data['sys_no'];  ?></td>
					<td width="110"  align="right"><? echo number_format($row_data['delivery_qty'],2);  ?></td>
					<td width="110"  align="right"><? echo  number_format($do_total_balance_value,2);    ?></td>
					<td width="200"  align="center"><? echo $row_data['remarks'];  ?></td>
				</tr>
				<?
        		 $k++;
			 	$sl++;
        	}
			$do_total_balance_delivery_qty+=$row_data['delivery_qty'];
			$do_total_balance_value=$do_total_balance_value;							
        
       	 }
		 
		 ?>
        <tr>
            <td colspan="4" align="right"><b>Buyer Total: </b></td>
            <td align="right"><b><? echo number_format($do_total_balance_value,2); ?></b></td>
            <td align="right">&nbsp;</td>
        </tr>
        <?
        $do_total_delivery_qty+=$total_balance_delivery_qty;
        $do_total_value+=$do_total_balance_value;
        //$balance = ($total_value - $do_total_value);
        //$balance = ($total_value - $total_value_return);
		} 
	 }
        ?>
        <tr>
        <td colspan="4" align="right"><b>G.Total: </b></td>
        <td align="right"><b><? echo number_format($do_total_value,2); ?></b></td>
        <td align="right">&nbsp;</td>
        </tr>
        <tr>
        <td colspan="4" align="right"><b>Balance </b></td>
        <td align="right"><b><?php
		  $receive_balance = ($total_value - $total_value_return);
		  $balance = ($receive_balance-$do_total_value);
		  echo number_format($balance,2); ?></b></td>
        <td align="right">&nbsp;</td>
        </tr>
        </tbody>			               
        </table>
        <?php
        	echo signature_table(215, $company_id, '900px');
        ?>
        </div>

    <?php
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













if($action=="report_generate_all")
{ 
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$job_no=str_replace("'","",$txt_job_no);
	$cbo_buyer_id=str_replace("'","",$cbo_party_name);
	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_within_group=str_replace("'","",$cbo_within_group);	
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	$cbo_job_year = str_replace("'", '', $cbo_job_year);

	if ($from_date == '' && $to_date == '') {
		$from_date = "01-Jan-$cbo_job_year";
		$to_date = "31-Dec-$cbo_job_year";
	}

	/*if($db_type==0) $select_from_date=change_date_format(str_replace("'", '', $from_date),'yyyy-mm-dd');
	if($db_type==2) $select_from_date=change_date_format(str_replace("'", '', $from_date),'','',1);
	if($db_type==0) $select_to_date=change_date_format(str_replace("'", '', $to_date),'yyyy-mm-dd');
	if($db_type==2) $select_to_date=change_date_format(str_replace("'", '', $to_date),'','',1);*/

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
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and d.subcon_job like '%$job_no' ";
	if ($job_no=="") $del_job_no_cond=""; else $del_job_no_cond=" and a.job_no in ('$job_no') ";
	
	
	
	if($db_type==0)
	{
		if( $from_date=='' && $to_date=='' ) $subcon_date=""; else $subcon_date= " and a.subcon_date  between '".change_date_format(str_replace("'", '', $from_date),'yyyy-mm-dd')."' and '".change_date_format(str_replace("'", '', $to_date),'yyyy-mm-dd')."'";
			if( $from_date=='' && $to_date=='' ) $delivery_date=""; else $delivery_date= " and a.delivery_date  between '".change_date_format(str_replace("'", '', $from_date),'yyyy-mm-dd')."' and '".change_date_format(str_replace("'", '', $to_date),'yyyy-mm-dd')."'";	
 	}
	if($db_type==2)
	{
		if( $from_date=='' && $to_date=='' ) $subcon_date=""; else $subcon_date= " and a.subcon_date  between '".change_date_format(str_replace("'", '', $from_date),'','',1)."' and '".change_date_format(str_replace("'", '', $to_date),'','',1)."'";	
		if( $from_date=='' && $to_date=='' ) $delivery_date=""; else $delivery_date= " and a.delivery_date  between '".change_date_format(str_replace("'", '', $from_date),'','',1)."' and '".change_date_format(str_replace("'", '', $to_date),'','',1)."'";	
 	
	}
	
	$buyer_sql="select a.id,a.sys_no, a.subcon_date, a.chalan_no, a.embl_job_no, b.id as details_id, b.quantity, b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name, c.id as sub_ord_dtls_id
	from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c,subcon_ord_mst d
	where a.entry_form = 296 and a.trans_type=1 and a.id=b.mst_id and b.job_dtls_id=c.id and c.mst_id=d.id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0 $company_name $search_com_cond $within_group $party_con $job_no_cond $subcon_date"; 
	
		// echo $buyer_sql;
		$buyer_result = sql_select($buyer_sql);
        $buyer_data=array();
        // $ordNoArr = array();
        foreach($buyer_result as $row)
        {
        	$job_no = $row[csf('embl_job_no')];
			
			$buyer_data[$row[csf('embl_job_no')]]['gmts_item_id'].=$garments_item[$row[csf('gmts_item_id')]].",";
			$buyer_data[$row[csf('embl_job_no')]]['gmts_color_id']=$row[csf('gmts_color_id')];
			$buyer_data[$row[csf('embl_job_no')]]['buyer_po_no'].=$row[csf('buyer_po_no')].",";;
			$buyer_data[$row[csf('embl_job_no')]]['buyer_style_ref'].=$row[csf('buyer_style_ref')].",";;
			$buyer_data[$row[csf('embl_job_no')]]['party_id'].=$party_arr[$row[csf('party_id')]].",";		        
			$buyer_data[$row[csf('embl_job_no')]]['buyer_buyer'].=$row[csf('party_buyer_name')].",";
			// $buyer_data[$row[csf('embl_job_no')]]['order_quantity'] += $row[csf('order_quantity')];
			$ordNo = $row[csf('order_no')];
        }

    $ordDtlsIds = implode(',', array_unique($ordNoArr));
    $orderQtySql = sql_select("select sum(order_quantity) as order_qty from subcon_ord_dtls where order_no='$ordNo' and  status_active =1 and  is_deleted=0");
    // echo "select sum(order_quantity) as order_qty from subcon_ord_dtls where id in($ordDtlsIds)";
		$buyer_po=$buyer_data[$job_no]['buyer_po_no'];
		$buyer_po_no=rtrim(implode(",",array_unique(explode(",",$buyer_po))));
		$buyer_style=$buyer_data[$job_no]['buyer_style_ref'];
		$buyer_style_ref=rtrim(implode(",",array_unique(explode(",",$buyer_style))));
		$party_id_arr=$buyer_data[$job_no]['party_id'];
		$party_id=implode(",",array_unique(explode(",",$party_id_arr)));
		$gmts_item_id_arr=$buyer_data[$job_no]['gmts_item_id'];
		$gmts_item_id=rtrim(implode(",",array_unique(explode(",",$gmts_item_id_arr))));
		$buyer_buyer_arr=$buyer_data[$job_no]['buyer_buyer'];
		$buyer_buyer_id=rtrim(implode(",",array_unique(explode(",",$buyer_buyer_arr))));
		$orderQty = $orderQtySql[0][csf('order_qty')];
			
	ob_start();
	?>
    
    <div id="mstDiv" align="center">
        <table style="width:1000px"> 
            <?
            $company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$company_id."");
            foreach( $company_library as $row)
            {
                $company=$row[csf('company_name')];
            ?>
            <tr>
            <td colspan="7" align="center" style="font-size:22px"><? echo $row[csf('company_name')];?></td>
            </tr>
            <?
            }
            ?>
            <tr>
            <td colspan="7" align="center" style="font-size:20px"><? echo $report_title; ?></td>
            </tr>
            <tr style="border:none;">
                <td colspan="7" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? if($from_date!="" || $to_date!="")echo "Date Range : ".change_date_format($from_date)."  to  ".change_date_format($to_date)."" ;?>
                </td>
            </tr>
           
        </table>
        <?
		  $sql="select a.id,a.sys_no, a.subcon_date, a.entry_form, a.trans_type, a.chalan_no, a.embl_job_no, b.id as details_id,
		  sum(CASE WHEN a.entry_form =296 and a.trans_type=1  THEN b.quantity ELSE 0 END) AS receive_qty,
		  sum(CASE WHEN a.entry_form =372 and a.trans_type=3  THEN b.quantity ELSE 0 END) AS receive_return_qty,
		   b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name,b.remarks
		from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c,subcon_ord_mst d
		where a. entry_form in(296,372) and a.trans_type in(1,3) and a.id=b.mst_id and  b.job_dtls_id=c.id  and c.mst_id=d.id $company_name $search_com_cond  $within_group  $party_con $job_no_cond $subcon_date and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.id,a.sys_no, a.subcon_date, a.entry_form, a.trans_type, a.chalan_no, a.embl_job_no, b.id,
		   b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name,b.remarks order by a.subcon_date"; 



	//$sql_return="select a.id,a.sys_no, a.subcon_date, a.chalan_no, a.embl_job_no, b.id as details_id ,b.quantity, b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name,b.remarks
		//from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c,subcon_ord_mst d
		//where a. entry_form = 372 and a.trans_type=3 and a.id=b.mst_id and  b.job_dtls_id=c.id  and c.mst_id=d.id $company_name $search_com_cond  $within_group  $party_con $job_no_cond $subcon_date order by a.subcon_date desc"; 


		$result = sql_select($sql);
        $all_data=array();
        foreach($result as $row)
        {
			
			$all_data[$row[csf('gmts_color_id')]][$row[csf('subcon_date')]][$row[csf('chalan_no')]]['sys_no']=$row[csf('sys_no')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('subcon_date')]][$row[csf('chalan_no')]]['subcon_date']=$row[csf('subcon_date')]; 
			$all_data[$row[csf('gmts_color_id')]][$row[csf('subcon_date')]][$row[csf('chalan_no')]]['chalan_no']=$row[csf('chalan_no')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('subcon_date')]][$row[csf('chalan_no')]]['embl_job_no']=$row[csf('embl_job_no')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('subcon_date')]][$row[csf('chalan_no')]]['receive_qty']+=$row[csf('receive_qty')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('subcon_date')]][$row[csf('chalan_no')]]['remarks']=$row[csf('remarks')];
			$all_data[$row[csf('gmts_color_id')]][$row[csf('subcon_date')]][$row[csf('chalan_no')]]['receive_return_qty']+=$row[csf('receive_return_qty')];
			
			
        }
		
		?>
        <table style="width:1000px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
        
            <tr>
                 <td width="80"><b>Job No:</b></td>
                 <td align="left"><? echo $job_no;?></td>
            </tr>
            <tr>
                 <td width="80"><b>Party:</b></td>
                 <td align="left"><? echo chop($party_id,',');?></td>
            </tr>
            <tr>
                 <td width="80"><b>Party Buyer:</b> </td>
                 <td align="left"><? echo chop($buyer_buyer_id,',');?></td>
            </tr>
            <tr>
                 <td width="80"><b>Buyer style:</b></td>
                 <td align="left"><? echo chop($buyer_style_ref,',');?></td>
            </tr>
            <tr>
                <td width="80"><b>Gmts. Item:</b></td>
                <td align="left"><? echo chop($gmts_item_id,',');?></td>
            </tr>
            <tr>
                <td width="80"><b>Order Qty:</b></td>
                <td align="left"><?php echo number_format($orderQty); ?></td>
            </tr>
        </table> 
        
        <table>
            <tr>
                 <td colspan="10" style="font-size:22px" align="center"></td>
            </tr>
        </table> 
        <table style="width:1000px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <tr>
                 <td colspan="10" style="font-size:22px" align="center"><b>Received</b></td>
            </tr>
        </table> 
        <?
       
        foreach($all_data as $gmts_color_id=>$gmts_color_data) //$pay_term
        {
			 $k=1;
			 $sl=1;
        ?>
        <table style="width:1000px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
        <tr>
       	 <td colspan="10" style="font-size:14px"><b>Color: <? echo $color_library_arr[$gmts_color_id];?></b></td>
        </tr>
        </table>               	
        <table style="width:1000px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="scroll_body" >
        <thead>
            <th width="30">SL</th>
            <th width="100">Date</th>
            <th width="130">Challan No</th>
            <th width="130">System Challan No</th> 
            <th width="100">Qty</th>
            <th width="100">Total Qty</th>
            <th width="100">Return Qty</th>
            <th width="100">Return Total Qty</th>
            <th width="100">Balance</th>
            <th width="100">Remarks</th>
        </thead>
        <tbody>
        <?
        $total_balance_value=0; $total_balance_quantity=0; $total_return_balance_value=0; $total_return_balance_quantity=0; $total_balance=0;
        foreach($gmts_color_data as $subcon_date_id=>$subcon_date_data)
        {
			foreach($subcon_date_data as $chalan_no_id=>$row_data)
			{	
										
				if ($k%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
				
				if($sl==1)
				{
					$total_balance_value=$row_data['receive_qty'];
				}
				else
				{
					$total_balance_value+=$row_data['receive_qty'];
				}

				if($sl==1)
				{
					$total_return_balance_value=$row_data['receive_return_qty'];
				}
				else
				{
					$total_return_balance_value+=$row_data['receive_return_qty'];
				}
				?>
				
				<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
					<td width="30" ><?php echo $sl; ?></td>
					<td width="100" align="center" title="<? echo $row_data['embl_job_no'] ?>"><? echo change_date_format($row_data['subcon_date']); ?></td>
					<td width="130" align="center"><? echo $row_data['chalan_no'];   ?></td>
					<td width="130"  align="center"><? echo $row_data['sys_no'];  ?></td>
					<td width="100"  align="right"><? echo number_format($row_data['receive_qty'],2);  ?></td>
					<td width="100"  align="right"><?  echo number_format($total_balance_value,2);   ?></td>

					<td width="100"  align="right"><? echo number_format($row_data['receive_return_qty'],2);  ?></td>
					<td width="100"  align="right"><?  echo number_format($total_return_balance_value,2);   ?></td>
					<td width="100"  align="right"><?  echo number_format($total_balance_value-$total_return_balance_value,2);   ?></td>
					<td width="100"  align="center"><? echo $row_data['remarks'];  ?></td>
				</tr>
				<?
        		$k++;
				$sl++;
			
        	}

        	$total_balance =($total_balance_value-$total_return_balance_value);
        	$total_return_balance_quantity+=$row_data['receive_return_qty'];
        	$total_return_balance_value=$total_return_balance_value; 
				
			$total_balance_quantity+=$row_data['receive_qty'];
			$total_balance_value=$total_balance_value;						
        
       	 }
		 
		 ?>
        <tr>
            <td colspan="5" align="right"><b>Color Total: </b></td>
            <td align="right"><b><? echo number_format($total_balance_value,2); ?></b></td>
            <td align="right">&nbsp;</td>
            <td align="right"><b><? echo number_format($total_return_balance_value,2); ?></b></td>
            <td align="right"><b><? echo number_format($total_balance,2); ?></b></td>
            <td align="right">&nbsp;</td>
        </tr>
        <?
        $total_diff_quantity+=$total_balance_quantity;
        $total_value+=$total_balance_value;
		}
        ?>
        
        </tbody>			               
        </table>
        </div> 
   
   	<table style="width:1000px">
            <tr>
                 <td colspan="9" style="font-size:22px" align="center">&nbsp;</td>
            </tr>
        </table> 








   		     
    <div id="mstDiv" align="center">
    
		<table style="width:1000px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
            <tr>
                 <td colspan="9" style="font-size:22px" align="center" ><b>Delivery</b></td>
            </tr>
        </table> 
        <?
		$do_sql="select a.id,a.delivery_no, a.delivery_date, a.entry_form, a.job_no, b.id as details_id,
		sum(CASE WHEN a.entry_form =303 THEN b.delivery_qty ELSE 0 END) AS delivery_qty,
		sum(CASE WHEN a.entry_form =360 THEN b.delivery_qty ELSE 0 END) AS delivery_return_qty,
		 b.order_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,b.remarks
			from subcon_delivery_mst a, subcon_delivery_dtls b,subcon_ord_dtls c,subcon_ord_mst d
			where a.entry_form in(303,360) and a.id=b.mst_id and  b.order_id=c.id and c.mst_id=d.id and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  and c.status_active =1 and c.is_deleted=0 and d.status_active =1 and d.is_deleted=0  $company_name  $party_con $within_group  $job_no_cond $delivery_date group by a.id,a.delivery_no, a.delivery_date, a.entry_form, a.job_no, b.id, b.delivery_qty, b.order_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,b.remarks order by a.delivery_date"; //$del_job_no_cond


			//$sql="select a.id,a.sys_no, a.subcon_date, a.entry_form, a.trans_type, a.chalan_no, a.embl_job_no, b.id as details_id,
		  //sum(CASE WHEN a.entry_form =296 and a.trans_type=1  THEN b.quantity ELSE 0 END) AS receive_qty,
		 // sum(CASE WHEN a.entry_form =372 and a.trans_type=3  THEN b.quantity ELSE 0 END) AS receive_return_qty,
		  // b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name,b.remarks
//from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c,subcon_ord_mst d
//where a. entry_form in(296,372) and a.trans_type in(1,3) and a.id=b.mst_id and  b.job_dtls_id=c.id  and c.mst_id=d.id $company_name $search_com_cond  $within_group  $party_con $job_no_cond $subcon_date group by a.id,a.sys_no, a.subcon_date, a.entry_form, a.trans_type, a.chalan_no, a.embl_job_no, b.id,
		  // b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name,b.remarks order by a.subcon_date desc"; 



		$do_result = sql_select($do_sql);
        $do_all_data=array();
        foreach($do_result as $row)
        {
			//$do_all_data[$row[csf('gmts_color_id')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['id']=$row[csf('id')];
			//$do_all_data[$row[csf('gmts_color_id')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['details_id']=$row[csf('details_id')];
			//$do_all_data[$row[csf('gmts_color_id')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['job_dtls_id']=$row[csf('order_id')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['sys_no']=$row[csf('delivery_no')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['delivery_date']=$row[csf('delivery_date')]; 
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['chalan_no']=$row[csf('chalan_no')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['job_no']=$row[csf('job_no')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['delivery_qty']+=$row[csf('delivery_qty')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['delivery_return_qty']+=$row[csf('delivery_return_qty')];
			$do_all_data[$row[csf('gmts_color_id')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['remarks']=$row[csf('remarks')];
			//$do_all_data[$row[csf('gmts_color_id')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['gmts_item_id']=$row[csf('gmts_item_id')];
			//$do_all_data[$row[csf('gmts_color_id')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['gmts_color_id']=$row[csf('gmts_color_id')];
			//$do_all_data[$row[csf('gmts_color_id')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['order_no']=$row[csf('order_no')];
			//$do_all_data[$row[csf('gmts_color_id')]][$row[csf('delivery_date')]][$row[csf('delivery_no')]]['remarks']=$row[csf('remarks')];
		
			//$job_dtls_id_arr[$row[csf('job_dtls_id')]]=$row[csf('order_id')];
			//$order_no_arr[$row[csf('order_no')]]=$row[csf('order_no')];
			//$do_dtls_arr[$row[csf('details_id')]]=$row[csf('details_id')];
			//$do_mst_arr[$row[csf('id')]]=$row[csf('id')];
        }
		
        foreach($do_all_data as $gmts_color_id=>$gmts_color_data) //$pay_term
        {
			 $k=1;
			 $sl=1;
        ?>
        <table style="width:1000px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
        <tr>
       	 <td colspan="9" style="font-size:14px"><b>Color: <? echo $color_library_arr[$gmts_color_id];?></b></td>
        </tr>
        </table>               	
        <table style="width:1000px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="scroll_body" >
        <thead>
        <th width="30" >SL</th>
        <th width="120" >Date</th>
        <th width="130" >System Challan No</th> 
        <th width="100" >Qty</th>
        <th width="100" >Total Qty</th>
        <th width="100" >Return Qty</th>
        <th width="100" >Return Total Qty</th>
        <th width="100" >Balance</th>
        <th width="200" >Remarks</th>
        </thead>
        <tbody>
        <?
       
		 $do_total_balance_delivery_qty=0; $do_total_balance_value=0; $do_return_total_balance_delivery_qty=0;
			$do_return_total_balance_value=0; $delivery_balance=0;
        foreach($gmts_color_data as $subcon_date_id=>$subcon_date_data)
        {
			
			foreach($subcon_date_data as $chalan_no_id=>$row_data)
			{	
										
				if ($k%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
				
				if($sl==1)
				{
					$do_total_balance_value=$row_data['delivery_qty'];
				}
				else
				{
					$do_total_balance_value+=$row_data['delivery_qty'];
				}

				if($sl==1)
				{
					$do_return_total_balance_value=$row_data['delivery_return_qty'];
				}
				else
				{
					$do_return_total_balance_value+=$row_data['delivery_return_qty'];
				}
				?>
				
				<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
					<td width="30" ><?php echo $sl; ?></td>
					<td width="120" align="center" title="<? echo $row_data['job_no'] ?>"><? echo change_date_format($row_data['delivery_date']); ?></td>
					<td width="130"  align="center"><? echo $row_data['sys_no'];  ?></td>
					<td width="100"  align="right"><? echo number_format($row_data['delivery_qty'],2);  ?></td>
					<td width="100"  align="right"><? echo  number_format($do_total_balance_value,2);    ?></td>

					<td width="100"  align="right"><? echo number_format($row_data['delivery_return_qty'],2);  ?></td>
					<td width="100"  align="right"><? echo  number_format($do_return_total_balance_value,2);    ?></td>
					<td width="100"  align="right"><? echo number_format($do_total_balance_value-$do_return_total_balance_value,2);  ?></td>
					<td width="200"  align="center"><? echo $row_data['remarks'];  ?></td>
				</tr>
				<?
        		 $k++;
			 	$sl++;
        	}
			$do_total_balance_delivery_qty+=$row_data['delivery_qty'];
			$do_total_balance_value=$do_total_balance_value;

			$do_return_total_balance_delivery_qty+=$row_data['delivery_return_qty'];
			$do_return_total_balance_value=$do_return_total_balance_value;	
			$delivery_balance=($do_total_balance_value-$do_return_total_balance_value);						
        
       	 }
		 
		 ?>
        <tr>
            <td colspan="4" align="right"><b>Color Total: </b></td>
            <td align="right"><b><? echo number_format($do_total_balance_value,2); ?></b></td>
            <td align="right">&nbsp;</td>
            <td align="right"><b><? echo number_format($do_return_total_balance_value,2); ?></b></td>
            <td align="right"><b><? echo number_format($delivery_balance,2); ?></b></td>
            <td align="right">&nbsp;</td>
        </tr>
        <?
        $do_total_delivery_qty+=$total_balance_delivery_qty;
        $do_total_value+=$do_total_balance_value;
        $balance = ($total_value - $do_total_value);
		}
        ?>
        
        </tbody>			               
        </table>
        <?php
        	echo signature_table(215, $company_id, '900px');
        ?>
        </div>

    <?php
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