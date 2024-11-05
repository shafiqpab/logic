<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$imge_arr=return_library_array( "select id,master_tble_id,image_location from common_photo_library",'id','image_location');
$party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');

if($action=="load_drop_down_mainbuyer")
{
	echo create_drop_down( "cbo_mainbuyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
	exit();
}
if ($action=="load_drop_down_buyer")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_buyer_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_buyer_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
}

if ($action=="job_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_order').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,within_group,party)
		{
			//alert();
			load_drop_down( 'emb_order_details_report_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
			
			$('#cbo_buyer_id').attr('disabled','disabled');
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('Embl. Job No');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==3)
			{
				$('#search_by_td').html('Buyer Job');
			}
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
			}
		}
		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $data[0];?>,<? echo $data[2];?>,<? echo $data[1];?>)">
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>               	 
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="170">Date Range</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">Embl. Job No</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="hidden" id="selected_order">  
								<?   
									echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1 );
                                ?>
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" 
								onClick="show_list_view ( 
															document.getElementById('cbo_company_id').value+'_'+
															document.getElementById('cbo_buyer_id').value+'_'+
															document.getElementById('txt_date_from').value+'_'+
															document.getElementById('txt_date_to').value+'_'+
															document.getElementById('cbo_type').value+'_'+
															document.getElementById('txt_search_string').value+'_'+
															document.getElementById('cbo_string_search_type').value+'_'+
															<? echo $data[2];?>, 'create_job_search_list_view', 'search_div', 'emb_order_details_report_controller', 'setFilterGrid(\'list_view\',-1)')" 
															style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <br>
                <div id="search_div"></div>   
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $buyer=" and a.party_id='$data[1]'"; else { $buyer=""; echo "PLease Select Party name"; die;}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $style_cond=" and a.style_ref_no = '$search_str' ";
			else if ($search_by==5) $po_cond=" and b.po_number = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $style_cond=" and a.style_ref_no like '%$search_str%'";  
			else if ($search_by==5) $po_cond=" and b.po_number like '%$search_str%'";  
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $style_cond=" and a.style_ref_no like '$search_str%'";  
			else if ($search_by==5) $po_cond=" and b.po_number like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $style_cond=" and a.style_ref_no like '%$search_str'";  
			else if ($search_by==5) $po_cond=" and b.po_number like '%$search_str'";  
		}
	}	
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==4)|| ($po_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	
	if ($po_ids!="") 
	{
		$po_idsCond=" and b.buyer_po_id in ($po_ids)";
	} 
	else
	{
		$po_idsCond="";
	}
	
	$buyer_po_arr=array();
	
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	//$color_lib_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$arr=array (3=>$comp,6=>$yes_no,7=>$color_lib_arr);
	
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(c.color_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
	}
	 $sql= "select a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $color_id_str as color_id, b.buyer_po_id  
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	 where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.booking_dtls_id!=0 and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond and b.id=c.mst_id  
	 group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, b.buyer_po_id
	 order by a.id DESC";

	//echo $sql;die;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="15">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th>Color</th>
        </thead>
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
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('embellishment_job')]; ?>")' style="cursor:pointer" >
                    <td width="25"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100"><? echo $buyer_po_arr[$row[csf('buyer_po_id')]]['po']; ?></td>
                    <td width="100"><? echo $buyer_po_arr[$row[csf('buyer_po_id')]]['style']; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td><? echo $color_name; ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?    
	exit();
}

if($action=="style_no_popup")
{
	echo load_html_head_contents("Style Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	
	//echo load_html_head_contents("Style Info", "../../../", 1, 1,'','','');
	//extract($_REQUEST);
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
	$year_job = str_replace("'","",$year);

    if($db_type==0) 
	{
		$year_field="year(SOD.INSERT_DATE) as year"; 
		if(trim($year)!=0) $year_field_cond="and YEAR(SOD.INSERT_DATE)=$year_job";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(SOD.INSERT_DATE,'YYYY') as year";
		if(trim($year)!=0) $year_field_cond=" and to_char(SOD.INSERT_DATE,'YYYY')=$year_job";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
    
    if($cbo_party_name==0) $cbo_party_name_cond=""; else $cbo_party_name_cond=" WHERE POM.COMPANY_NAME ='$cbo_party_name'";
    
    $buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	
	$sql1="SELECT SOD.BUYER_PO_ID,  POD.PO_NUMBER,  POM.STYLE_REF_NO,  SOD.ORDER_NO,  POM.COMPANY_NAME,  B.BUYER_NAME,  POM.JOB_NO,SOD.INSERT_DATE
		FROM SUBCON_ORD_DTLS SOD INNER JOIN WO_PO_BREAK_DOWN POD ON SOD.BUYER_PO_ID = POD.ID INNER JOIN WO_PO_DETAILS_MASTER POM
		ON POD.JOB_NO_MST = POM.JOB_NO INNER JOIN LIB_BUYER B ON POM.BUYER_NAME = B.ID $cbo_party_name_cond $year_field_cond";
	//echo $sql1;die;
   $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.order_no, b.cust_style_ref, b.main_process_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' $buyer_cond $year_field_cond order by a.id desc";	
	
    ?>
    <table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="80">Job no</th>
            <th width="130">Buyer</th>
            <th width="110">Style</th>
            <th width="120">Po number</th>
			<th width="120">Work Order</th>
       </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
    <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
     <? $data_array=sql_select($sql1);
        $i=1;
		 foreach($data_array as $row)
		 {
			 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 ?>
			<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value('<? echo $row[csf('STYLE_REF_NO')]; ?>')" style="cursor:pointer;">
				<td width="30"><? echo $i; ?></td>
				<td align="center"  width="80"><? echo $row[csf('JOB_NO')]; ?></td>				
				<td width="130"><? echo $buyer[$row[csf('BUYER_NAME')]]; ?></td>
				<td width="110"><p><? echo $row[csf('STYLE_REF_NO')]; ?></p></td>
				<td width="120"><p><? echo $row[csf('PO_NUMBER')]; ?></p></td>
				<td width="120"><? echo $row[csf('ORDER_NO')]; ?></td>
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

if($action=="order_no_popup")
{
	echo load_html_head_contents("Style Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	
	//echo load_html_head_contents("Style Info", "../../../", 1, 1,'','','');
	//extract($_REQUEST);
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
	$year_job = str_replace("'","",$year);

    if($db_type==0) 
	{
		$year_field="year(SOD.INSERT_DATE) as year"; 
		if(trim($year)!=0) $year_field_cond="and YEAR(SOD.INSERT_DATE)=$year_job";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(SOD.INSERT_DATE,'YYYY') as year";
		if(trim($year)!=0) $year_field_cond=" and to_char(SOD.INSERT_DATE,'YYYY')=$year_job";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
    
    if($cbo_party_name==0) $cbo_party_name_cond=""; else $cbo_party_name_cond=" WHERE POM.COMPANY_NAME ='$cbo_party_name'";
    
    $buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	
	$sql1="SELECT SOD.BUYER_PO_ID,  POD.PO_NUMBER,  POM.STYLE_REF_NO,  SOD.ORDER_NO,  POM.COMPANY_NAME,  B.BUYER_NAME,  POM.JOB_NO,SOD.INSERT_DATE
		FROM SUBCON_ORD_DTLS SOD INNER JOIN WO_PO_BREAK_DOWN POD ON SOD.BUYER_PO_ID = POD.ID INNER JOIN WO_PO_DETAILS_MASTER POM
		ON POD.JOB_NO_MST = POM.JOB_NO INNER JOIN LIB_BUYER B ON POM.BUYER_NAME = B.ID $cbo_party_name_cond $year_field_cond";
	//echo $sql1;die;
   $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.order_no, b.cust_style_ref, b.main_process_id from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' $buyer_cond $year_field_cond order by a.id desc";	
	
    ?>
    <table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="80">Job no</th>
            <th width="130">Buyer</th>
            <th width="110">Style</th>
            <th width="120">Po number</th>
			<th width="120">Work Order</th>
       </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
    <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
     <? $data_array=sql_select($sql1);
        $i=1;
		 foreach($data_array as $row)
		 {
			 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 ?>
			<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value('<? echo $row[csf('PO_NUMBER')]; ?>')" style="cursor:pointer;">
				<td width="30"><? echo $i; ?></td>
				<td align="center"  width="80"><? echo $row[csf('JOB_NO')]; ?></td>				
				<td width="130"><? echo $buyer[$row[csf('BUYER_NAME')]]; ?></td>
				<td width="110"><p><? echo $row[csf('STYLE_REF_NO')]; ?></p></td>
				<td width="120"><p><? echo $row[csf('PO_NUMBER')]; ?></p></td>
				<td width="120"><? echo $row[csf('ORDER_NO')]; ?></td>
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


if($action=="report_generate_size")
{ 
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$job_no=str_replace("'","",$txt_job_no);
	
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$year_id=str_replace("'","",$cbo_year);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	
	$cbo_mainbuyer_id=str_replace("'","",$cbo_mainbuyer_id);//Buyer_ID
	
	if($year_id!=0) $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	if($cbo_buyer_id!=0) $buyer_id_cond=" AND DM.PARTY_ID ='$cbo_buyer_id'"; else $buyer_id_cond="";
	
	if($cbo_company_id!=0) $cbo_company_id_cond=" AND DM.COMPANY_ID ='$cbo_company_id'"; else $cbo_company_id_cond="";
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and DM.JOB_NO = '$job_no' ";
	//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
	if ($txt_style_ref!='') $style_ref_cond=" AND PDM.STYLE_REF_NO like '%$txt_style_ref%'"; else $style_ref_cond="";
	//if(trim($txt_order_no)!="") $order_no_cond="%".trim($txt_order_no)."%"; else $order_no_cond="%%";
	if ($txt_order_no!='') $order_no_cond=" and DD.BUYER_PO_ID like '%$txt_order_no%'"; else $order_no_cond="";
	
	if($cbo_mainbuyer_id!=0) $cbo_mainbuyer_id_cond=" AND PDM.BUYER_NAME ='$cbo_mainbuyer_id'"; else $cbo_mainbuyer_id_cond="";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and DM.DELIVERY_DATE  between $txt_date_from and $txt_date_to";
	//echo $txt_date_from;echo "AND to date ".$txt_date_to;die;
	//echo $cbo_mainbuyer_id_cond;die;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$gmt_item_Arr = return_library_array("select id,ITEM_NAME from  LIB_GARMENT_ITEM ","id","ITEM_NAME");
	$body_part_Arr = return_library_array("select id,BODY_PART_FULL_NAME from  LIB_BODY_PART ","id","BODY_PART_FULL_NAME");
	
	$buyer_po_Arr = return_library_array("select id,PO_NUMBER from WO_PO_BREAK_DOWN ","id","PO_NUMBER");
	$location_Arr = return_library_array("SELECT ID, LOCATION_NAME FROM LIB_LOCATION ","id","LOCATION_NAME");
	$Style_Arr = return_library_array("SELECT a.ID, b.STYLE_REF_NO FROM WO_PO_BREAK_DOWN a INNER JOIN WO_PO_DETAILS_MASTER b ON a.JOB_NO_MST = b.JOB_NO","id","STYLE_REF_NO");
	$ord_qty = return_library_array("SELECT d.BUYER_PO_ID as ID, SUM(d.ORDER_QUANTITY) * 12 AS ORDER_QUANTITY FROM SUBCON_ORD_DTLS d WHERE d.STATUS_ACTIVE = 1 AND d.IS_DELETED = 0 GROUP BY d.BUYER_PO_ID","ID","ORDER_QUANTITY");
	//$buyer_arr = return_library_array("SELECT   a.ID,  c.BUYER_NAME FROM WO_PO_BREAK_DOWN a INNER JOIN WO_PO_DETAILS_MASTER b ON a.JOB_NO_MST = b.JOB_NO
//INNER JOIN LIB_BUYER c ON b.BUYER_NAME = c.ID ","id","BUYER_NAME");
	
	?>
    <div>
		<?/* echo "Test";
		select lib_company.company_name, lib_company1.company_name as party_name, lib_buyer.buyer_name, lib_location.location_name, lib_garment_item.item_name, lib_color.color_name, lib_size.size_name, wo_po_break_down.po_number, wo_po_details_master.style_ref_no, mst.company_id, mst.location_id, mst.party_id, mst.receive_date, mst.delivery_date, mst.job_no_prefix_num, mst.job_no_mst, mst.order_no, mst.po_delivery_date, mst.order_id, mst.item_id, mst.color_id, mst.size_id, mst.qnty, mst.rate, mst.amount, mst.buyer_po_id, mst.gmts_item_id, mst.embl_type, mst.body_part from (select som.company_id, som.location_id, som.party_id, som.receive_date, som.delivery_date, som.job_no_prefix_num, sod.job_no_mst, sod.order_no, sod.delivery_date as po_delivery_date, sob.order_id, sob.item_id, sob.color_id, sob.size_id, sob.qnty, sob.rate, sob.amount, sod.buyer_po_id, sod.gmts_item_id, sod.embl_type, sod.body_part from subcon_ord_mst som inner join subcon_ord_dtls sod on som.id = sod.mst_id inner join subcon_ord_breakdown sob on sod.id = sob.mst_id where som.company_id = 3 and som.job_no_prefix_num = 11 and som.status_active = 1 ) mst inner join lib_company on mst.company_id = lib_company.id inner join lib_location on lib_location.id = mst.location_id inner join lib_garment_item on mst.item_id = lib_garment_item.id inner join lib_color on mst.color_id = lib_color.id inner join lib_size on mst.size_id = lib_size.id inner join wo_po_break_down on mst.buyer_po_id = wo_po_break_down.id inner join wo_po_details_master on wo_po_break_down.job_no_mst = wo_po_details_master.job_no inner join lib_buyer on lib_buyer.id = wo_po_details_master.buyer_name inner join lib_company lib_company1 on lib_company1.id = mst.party_id 
		
		$style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond
		
		*/
		//--------------------------------------------------------Start----------------------------------------
	
			  
	$query_t = "SELECT SUM(CASE WHEN SMM.TRANS_TYPE = '1' THEN SMD.QUANTITY END) AS RECVD_QTY, SUM(CASE WHEN SMM.TRANS_TYPE = '2' THEN SMD.QUANTITY END)  AS ISSUE_QTY,
			  SMM.SYS_NO AS TRANS_NO,  SMM.COMPANY_ID,  SMM.TRANS_TYPE,  SMM.LOCATION_ID,  SMM.PARTY_ID,  SMM.SUBCON_DATE AS TRANS_DATE,  SMM.CHALAN_NO,  SMD.EMB_NAME_ID,
			  SMM.EMBL_JOB_NO,  SMD.BUYER_PO_ID,  SOB.COLOR_ID,  SOB.EMBELLISHMENT_TYPE AS EMB_TYPE,  SOB.BODY_PART,  P.PO_NUMBER,  PM.BUYER_NAME,  PM.STYLE_REF_NO
			FROM SUB_MATERIAL_MST SMM INNER JOIN SUB_MATERIAL_DTLS SMD ON SMD.MST_ID = SMM.ID INNER JOIN SUBCON_ORD_BREAKDOWN SOB ON SMD.JOB_BREAK_ID = SOB.ID
			INNER JOIN WO_PO_BREAK_DOWN P ON SMD.BUYER_PO_ID = P.ID INNER JOIN WO_PO_DETAILS_MASTER PM ON P.JOB_NO_MST = PM.JOB_NO
			WHERE SMD.STATUS_ACTIVE = 1
			AND SMM.STATUS_ACTIVE   = 1
			$cbo_company_id_cond $style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond
			GROUP BY SMM.SYS_NO,  SMM.COMPANY_ID,  SMM.TRANS_TYPE,  SMM.LOCATION_ID,  SMM.PARTY_ID,  SMM.SUBCON_DATE,  SMM.CHALAN_NO,  SMD.EMB_NAME_ID,  SMM.EMBL_JOB_NO,
			  SMD.BUYER_PO_ID,  SOB.COLOR_ID,  SOB.EMBELLISHMENT_TYPE,  SOB.BODY_PART,  SMD.JOB_DTLS_ID,  P.PO_NUMBER,  PM.BUYER_NAME,  PM.STYLE_REF_NO
			ORDER BY TRANS_DATE DESC, SMM.SYS_NO";
			  
	$query = "SELECT DM.COMPANY_ID,  DM.LOCATION_ID,  DM.DELIVERY_DATE,  DM.DELIVERY_NO,  DM.PARTY_ID,  DM.JOB_NO,  DD.COLOR_SIZE_ID,  DD.BUYER_PO_ID,  DD.SORT_QTY, DD.PRINT_REJECT_QTY, DD.FABRIC_REJECT_QTY,
			  DM.REMARKS,  DM.DELI_PARTY,  DM.DELI_PARTY_LOCATION,  DD.DELIVERY_STATUS,  DD.DELIVERY_QTY,  PBD.PO_NUMBER,  PDM.BUYER_NAME,  PDM.STYLE_REF_NO,  PDM.STYLE_DESCRIPTION,
			  OB.COLOR_ID,  OB.ORDER_ID,OB.EMBELLISHMENT_TYPE, OB.BODY_PART
				FROM SUBCON_DELIVERY_MST DM INNER JOIN SUBCON_DELIVERY_DTLS DD ON DM.ID = DD.MST_ID INNER JOIN WO_PO_BREAK_DOWN PBD ON PBD.ID = DD.BUYER_PO_ID
				INNER JOIN WO_PO_DETAILS_MASTER PDM ON PBD.JOB_NO_MST = PDM.JOB_NO INNER JOIN SUBCON_ORD_BREAKDOWN OB ON DD.COLOR_SIZE_ID = OB.ID
				WHERE DM.STATUS_ACTIVE = 1
				AND DD.STATUS_ACTIVE = 1
				$cbo_company_id_cond $style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond ORDER By PDM.BUYER_NAME, DD.BUYER_PO_ID ";
	//echo $query;die;
	$sql_data_query = sql_select($query);
	$countRecords = count($query); 
	//echo $sql_data_query;
	ob_start();
	$details_data=array();
	$delivery_data=array();

	foreach( $sql_data_query as $row)
	{
		//detail data in Array  
		
		//ORDER_ID, COLOR_ID, COMPANY_ID,  LOCATION_ID,  DELIVERY_DATE,  DELIVERY_NO,  PARTY_ID,  JOB_NO, COLOR_SIZE_ID, BUYER_PO_ID,  SORT_QTY,
		///REMARKS,  DELI_PARTY,  DELI_PARTY_LOCATION,  DELIVERY_STATUS,  DELIVERY_QTY,  PO_NUMBER, BUYER_NAME,  STYLE_REF_NO
		
		$delivery_data[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["DELI_PARTY"]=$row[csf(DELI_PARTY)];
		$delivery_data[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["DELI_PARTY_LOCATION"]=$row[csf(DELI_PARTY_LOCATION)];
		$delivery_data[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["ORDER_ID"]=$row[csf(ORDER_ID)];
		$delivery_data[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["BUYER_PO_ID"]=$row[csf(BUYER_PO_ID)];
		$delivery_data[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["COLOR_ID"]=$row[csf(COLOR_ID)];
		$delivery_data[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["BUYER_NAME"]=$row[csf(BUYER_NAME)];
		$delivery_data[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["PO_NUMBER"]=$row[csf(PO_NUMBER)];
		$delivery_data[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["STYLE_REF_NO"]=$row[csf(STYLE_REF_NO)];
		$delivery_data[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["DELIVERY_QTY"]+=$row[csf(DELIVERY_QTY)];
		$delivery_data[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["SORT_QTY"]+=$row[csf(SORT_QTY)];
		$delivery_data[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["PARTY_ID"]=$row[csf(PARTY_ID)];
		$delivery_data[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["PRINT_REJECT_QTY"]+=$row[csf(PRINT_REJECT_QTY)];
		$delivery_data[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["FABRIC_REJECT_QTY"]+=$row[csf(FABRIC_REJECT_QTY)];


		$sub_total_arr[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]]+=$row[csf(DELIVERY_QTY)];
		$sub_rej_print_arr[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]]+=$row[csf(PRINT_REJECT_QTY)];
		$sub_rej_fab_arr[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]]+=$row[csf(FABRIC_REJECT_QTY)];
		$sub_short_arr[$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]]+=$row[csf(SORT_QTY)];
		
		$grand_total_arr[$row[csf("DELI_PARTY")]]+=$row[csf(DELIVERY_QTY)];
		$grand_short_arr[$row[csf("DELI_PARTY")]]+=$row[csf(SORT_QTY)];
		$grand_rej_print_arr[$row[csf("DELI_PARTY")]]+=$row[csf(PRINT_REJECT_QTY)];
		$grand_rej_fab_arr[$row[csf("DELI_PARTY")]]+=$row[csf(FABRIC_REJECT_QTY)];
	}
	//echo "<pre>";print_r($details_data);die;	
	
	$query_sub ="SELECT DISTINCT OB.ID, OB.BODY_PART, OD.ORDER_ID,  OD.BUYER_PO_ID,  OB.COLOR_ID,  OB.EMBELLISHMENT_TYPE
FROM SUBCON_ORD_MST OM INNER JOIN SUBCON_ORD_DTLS OD ON OM.ID = OD.MST_ID INNER JOIN SUBCON_ORD_BREAKDOWN OB ON OD.JOB_NO_MST    = OB.JOB_NO_MST";
	//echo $query_sub;die;
	$sql_data_query_sub = sql_select($query_sub);
	$countRecords = count($query_sub); 
	//echo $sql_data_query_sub;
	$details_data_sub=array();
	foreach( $sql_data_query_sub as $rows)
	{
		$details_data_sub[$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["EMBELLISHMENT_TYPE"]=$rows[csf(EMBELLISHMENT_TYPE)];
		$details_data_sub[$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["BODY_PART"]=$rows[csf(BODY_PART)];
	}
	//echo "<pre>";print_r($details_data_sub);die;
	
	?>
	<div style="width:1350px; margin:0 auto;">
		<fieldset style="width:100%;">	
		    <table width="98%" cellpadding="0" cellspacing="0" id="caption">
	            <tr>  
	                <td align="center" width="100%" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">Screen Print Delivery Report </strong>
	                </td>
	            </tr>
				<tr>  
	                <td align="center" width="100%" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">&nbsp </strong>
	                </td>
	            </tr>
	        </table>
			<div style="width:1350px; align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1340"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" rules="all">
					<thead>
						<th style="word-wrap: break-word;word-break: break-all;" width="30" align="center">Sl </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">BUYER</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">PO No</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="200" align="center">STYLE</th>		
						<th style="word-wrap: break-word;word-break: break-all;" width="115" align="center">Delivery Party </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="150" align="center">Del Place</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="170" align="center">PARTY </th>		
						<th style="word-wrap: break-word;word-break: break-all;" width="120" align="center">COLOR</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="70" align="center">Del QTY</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="70" align="center">Short </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="70" align="center">Rej Fab </th>
						<th style="word-wrap: break-word;word-break: break-all;"  align="center">Rej Print</th>
							
					</thead>
				</table>
			</div>
			<div style="width:1350px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1340"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<?	
						//ORDER_ID, COLOR_ID, COMPANY_ID,  LOCATION_ID,  DELIVERY_DATE,  DELIVERY_NO,  PARTY_ID,  JOB_NO, COLOR_SIZE_ID, BUYER_PO_ID,  DD.SORT_QTY,
						//REMARKS,  DELI_PARTY,  DELI_PARTY_LOCATION,  DELIVERY_STATUS,  DELIVERY_QTY,  PO_NUMBER, BUYER_NAME,  STYLE_REF_NO
						$k=1;
						foreach($delivery_data as $del_party_id=>$del_party_data)
						{
							foreach($del_party_data as $del_party_location_id=>$del_party_location_data)
							{
							  foreach($del_party_location_data as $wo_id=>$wo_data)
							  {
								foreach($wo_data as $po_id=>$po_data)
								{
									foreach($po_data as $color_id=>$color_data)
									{
										if ($k%2==0)  
											$bgcolor="#E9F3FF";
											else
											$bgcolor="#FFFFFF";
										//ORDER_ID, COLOR_ID, COMPANY_ID,  LOCATION_ID,  DELIVERY_DATE,  DELIVERY_NO,  PARTY_ID,  JOB_NO, COLOR_SIZE_ID, BUYER_PO_ID,  DD.SORT_QTY,
						//REMARKS,  DELI_PARTY,  DELI_PARTY_LOCATION,  DELIVERY_STATUS,  DELIVERY_QTY,  PO_NUMBER, BUYER_NAME,  STYLE_REF_NO
						
										?>
											<tr height="30" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
												<td style="word-wrap: break-word;word-break: break-all;" width="30"> <? echo $k; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="100"> <? echo $buyerArr[$color_data[('BUYER_NAME')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="100">  <? echo $buyer_po_Arr[$color_data[('BUYER_PO_ID')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="200">  <? echo $color_data[('STYLE_REF_NO')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="115">  <? echo $companyArr[$color_data[('DELI_PARTY')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="150"> <? echo $location_Arr[$color_data[('DELI_PARTY_LOCATION')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="170">  <? echo $companyArr[$color_data[('PARTY_ID')]]; ?></td>
				
												<td style="word-wrap: break-word;word-break: break-all;" width="120">  <? echo $colorArr[$color_data[('COLOR_ID')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="70" align="right">  <? echo $color_data[('DELIVERY_QTY')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="70" align="center">  <? echo $color_data[('PRINT_REJECT_QTY')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="70" align="center">  <? echo $color_data[('FABRIC_REJECT_QTY')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;"  align="center">  <? echo $color_data[('SORT_QTY')]; ?></td>
												
											</tr>
											<?
											$k++;
											$value_total_del_qty_party+=$color_data[('DELIVERY_QTY')];
											$value_total_rej_print_qty_party+=$color_data[('PRINT_REJECT_QTY')];
											$value_total_rej_fab_qty_party+=$color_data[('FABRIC_REJECT_QTY')];
											$value_total_short_qty_party+=$color_data[('SORT_QTY')];
;
										}
										
										
									}
									
								}
								?>
										<tr bgcolor="#dddddd">   
											<td align="right" colspan="8" style="word-wrap: break-word;word-break: break-all;" width="30"> <strong><? echo $location_Arr[$color_data[('DELI_PARTY_LOCATION')]]; ?> Total :</strong></td>	
											<td align="right" style="word-wrap: break-word;word-break: break-all;"> <strong>
											<?
												echo $sub_total_arr[$color_data[('DELI_PARTY')]][$color_data[('DELI_PARTY_LOCATION')]]; 
											?></strong></td>
											<td style="word-wrap: break-word;word-break: break-all;" align="center"> <strong><? echo $sub_rej_print_arr[$color_data[('DELI_PARTY')]][$color_data[('DELI_PARTY_LOCATION')]];?> </strong></td>
											<td style="word-wrap: break-word;word-break: break-all;" align="center"> <strong><? echo $sub_rej_fab_arr[$color_data[('DELI_PARTY')]][$color_data[('DELI_PARTY_LOCATION')]]; ?> </strong></td>
											<td align="center" style="word-wrap: break-word;word-break: break-all;"><strong>
											<? 
												echo $sub_short_arr[$color_data[('DELI_PARTY')]][$color_data[('DELI_PARTY_LOCATION')]]; 
											?></strong>
											</td>
											
										
										</tr>
										<?
							}
							?>
										<tr bgcolor="#dddddd">   
											<td align="right" colspan="8" style="word-wrap: break-word;word-break: break-all;" width="30"> <strong><? echo $companyArr[$color_data[('DELI_PARTY')]]; ?> Total :</strong></td>	
											<td align="right" style="word-wrap: break-word;word-break: break-all;"> <strong>
											<?
												echo $grand_total_arr[$color_data[('DELI_PARTY')]]; 
											?></strong></td>
											<td style="word-wrap: break-word;word-break: break-all;"align="center"> <strong><? echo $grand_rej_print_arr[$color_data[('DELI_PARTY')]];  ?> </strong></td>
											<td style="word-wrap: break-word;word-break: break-all;"align="center"> <strong><? echo $grand_rej_fab_arr[$color_data[('DELI_PARTY')]];  ?> </strong></td>
											<td align="center" style="word-wrap: break-word;word-break: break-all;"><strong>
											<? 
												echo $grand_short_arr[$color_data[('DELI_PARTY')]]; 
											?></strong>
											</td>
											
											
										</tr>
										<?
						}
						?>
					</tbody>
					<tfoot>
						<tr bgcolor="#dddddd">   
							<td style="word-wrap: break-word;word-break: break-all;" width="30"></td>	
							<td style="word-wrap: break-word;word-break: break-all;" width="100"> </td>
							<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
						
							<td style="word-wrap: break-word;word-break: break-all;" width="200"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="115"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="150"></td>
							<td style="word-wrap: break-word;word-break: break-all;" width="170"></td>
							
							<td style="word-wrap: break-word;word-break: break-all;" width="120"> <strong>Grand Total :  </strong></td>
							<td style="word-wrap: break-word;word-break: break-all;" align="right" width="70" id="value_total_del_qty_party"> <strong><? echo number_format($value_total_del_qty_party,0,'.',''); ?> </strong></td>
							<td style="word-wrap: break-word;word-break: break-all;" align="center" width="70" id="value_total_rej_print_qty_party"> <strong><? echo number_format($value_total_rej_print_qty_party,0,'.',''); ?> </strong></td>
							<td style="word-wrap: break-word;word-break: break-all;" align="center" width="70" id="value_total_rej_fab_qty_party"> <strong><? echo number_format($value_total_rej_fab_qty_party,0,'.',''); ?> </strong></td>
							<td style="word-wrap: break-word;word-break: break-all;" align="center" id="value_total_short_qty_party"> <strong><? echo number_format($value_total_short_qty_party,0,'.',''); ?> </strong></td>
													
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>	
		<?
				//--------------------------------------------------------End----------------------------------------
		?>
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
//----------------------end report_generate-------------------------


//----------------------end report_generate-------------------------


if($action=="material_desc_popup")
{
	echo load_html_head_contents("Material Description Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Receive Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Receive ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Rec. Date</th>
                        <th width="60">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Receive Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                
                $sql= "select a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 and a.trans_type=1 group by a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Return Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Return ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Return Date</th>
                        <th width="60">Party</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Return Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $party_arr=return_library_array( "select id, short_name from  lib_buyer",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                
                $sql_ret= "select a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=3 group by a.sys_no, a.prefix_no_num, a.chalan_no, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_ret_sql= sql_select($sql_ret);
                foreach( $material_ret_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
					$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_ret_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_ret_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
	<?
	exit();
}

if($action=="material_desc_iss_popup")
{
	echo load_html_head_contents("Material Issue Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Issue Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Issue ID</th>
                        <th width="70">Challan No</th>
                        <th width="70">Issue Date</th>
                        <th width="60">Issue To</th>
                        <th width="80">Order No</th>
                        <th width="80">Category</th>
                        <th width="150">Description</th>
                        <th width="80">Issue Qty</th>
                        <th width="80">Bag/ Roll</th>
                        <th>Cone</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
				$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                
                $sql= "select a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia, sum(b.quantity) as quantity, sum(b.subcon_roll) as subcon_roll, sum(b.rec_cone) as rec_cone from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=2 group by a.sys_no, a.prefix_no_num, a.chalan_no, a.prod_source, a.subcon_date, a.party_id, b.order_id, b.item_category_id, b.id, b.material_description, b.color_id, b.gsm, b.grey_dia, b.fin_dia order by a.sys_no, a.subcon_date";
               //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					//if ($row[csf("color_id")]!=0 && $row[csf("gsm")]!=0  && $row[csf("grey_dia")]!=0  && $row[csf("fin_dia")]!=0 )
					//{
						$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
					//}
					$issue_to="";
					if($row[csf("prod_source")]==1) $issue_to=$company_array[$row[csf("party_id")]]; else if($row[csf("prod_source")]==3) $issue_to=$supplier_array[$row[csf("party_id")]]; else $issue_to="";
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo $row[csf("chalan_no")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("subcon_date")]);?> </td> 
                    <td width="60"><p><? echo  $issue_to; ?></p></td>
                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="80"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
                    <td align="center" width="150"><? echo $material_name; ?></td>
                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
                    <td align="center" width="80"><? echo $row[csf("subcon_roll")];; ?></td>
                    <td><p><? echo $row[csf("rec_cone")]; ?></p></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
	<?
	exit();
}

if($action=="product_qty_pop_up")
{
	echo load_html_head_contents("Production Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	$order_id=$expData[0];
	$process_id=$expData[1];
	?>
        <fieldset style="width:820px">
            <div style="width:100%;" align="center">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    	<tr>
                            <th width="30">SL</th>
                            <th width="60"><? if ($process_id==3) echo "Batch NO"; else echo "Sys ID" ?></th>
                            <th width="70">Prod. Date</th>
                            <th width="100">Party</th>
                            <th width="80">Order No</th>
                            <th width="130">Process</th>
                            <th width="150">Description</th>
                            <th width="">Prod. Qty</th>
                    	</tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
					$party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
					$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
					$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
					$po_party_arr=return_library_array( "select b.id, a.party_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst",'id','party_id');
					$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
                    $i=0;
					if ($process_id==1)
					{
						 $sql = "select '' as sys_id, production_date, order_id, gmts_item_id as item_id, '' as process, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=1 group by production_date, order_id, gmts_item_id";
					}
					else if ($process_id==5)
					{
						$sql = "select '' as sys_id, production_date, order_id, gmts_item_id as item_id, '' as process, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=2 group by production_date, order_id, gmts_item_id";

					}
					else if ($process_id==11)
					{
						$sql = "select '' as sys_id, production_date, order_id, gmts_item_id as item_id, '' as process, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=4 group by production_date, order_id, gmts_item_id";

					}
					else if ($process_id==2)
					{
						$sql="select a.prefix_no_num as sys_id, a.product_no, a.party_id, a.product_date as production_date, b.order_id, b.process, b.cons_comp_id as item_id, b.color_id, sum(b.no_of_roll) as roll_qty, sum(b.product_qnty) as production_qnty from subcon_production_mst a, subcon_production_dtls b where b.order_id='$order_id' and b.product_type=2 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prefix_no_num, a.product_no, a.party_id, a.product_date, b.order_id, b.process, b.cons_comp_id, b.color_id order by b.color_id";
					}
					else if($process_id==3)
					{
						if($db_type==0)
						{
							$sql="select b.batch_no as sys_id, a.process_end_date as production_date, c.po_id as order_id, c.item_description as item_id, a.process_id as process, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and c.po_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.batch_no, a.process_end_date, a.process_id, c.po_id, c.item_description ";
						}
						elseif($db_type==2)
						{
							$sql="select b.batch_no as sys_id, a.process_end_date as production_date, c.po_id as order_id, c.item_description as item_id, a.process_id as process, sum(c.batch_qnty) as production_qnty from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c where a.batch_id=b.id and b.id=c.mst_id and a.entry_form=38 and a.result=1 and a.load_unload_id=2 and b.entry_form=36 and c.po_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.batch_no, a.process_end_date, a.process_id, c.po_id, c.item_description ";
						}
					}
					else if($process_id==4)
					{
						$sql = "select a.prefix_no_num as sys_id, a.product_no, a.product_date as production_date, a.party_id, c.order_id, b.process as process, b.fabric_description as item_id, sum(c.quantity) as production_qnty from subcon_production_mst a, subcon_production_dtls b, subcon_production_qnty c where a.id=b.mst_id and b.id=c.dtls_id and c.order_id in ($order_id) and b.product_type='$process_id' group by a.prefix_no_num, a.product_no, a.party_id, a.product_date, c.order_id, b.process, b.fabric_description";
					}
                   //echo $sql;
					$production_sql= sql_select($sql); $color_array=array(); $k=1;
					foreach( $production_sql as $row )
                    {
                        $i++;
                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						
						if ($process_id==1 || $process_id==5 || $process_id==11)
						{
							$item_name=$garments_item[$row[csf('item_id')]];
							$party_name=$party_arr[$po_party_arr[$row[csf("order_id")]]];
						}
						else if ($process_id==2)
						{
							$party_name=$party_arr[$row[csf("party_id")]];
							$process_name=$conversion_cost_head_array[$row[csf("process")]];
							$item_name=$kniting_item_arr[$row[csf('item_id')]];
						}
						else if ($process_id==3)
						{
							$party_name=$party_arr[$po_party_arr[$row[csf("order_id")]]];
							$process_name="";
							$process_id=explode(',',$row[csf('process')]);
							foreach($process_id as $val)
							{
								if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=','.$conversion_cost_head_array[$val];
							}
							$item_name=$row[csf('item_id')];
						}
						else if ($process_id==4)
						{
							$party_name=$party_arr[$row[csf("party_id")]];
							$process_name="";
							$process_id=explode(',',$row[csf('process')]);
							foreach($process_id as $val)
							{
								if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=','.$conversion_cost_head_array[$val];
							}
							$item_name=$row[csf('item_id')];
						}
						else
						{
							$item_name=$row[csf('item_id')];
						}
						if ($process_id==2)
						{
							if (!in_array($row[csf("color_id")],$color_array) )
							{
								if($k!=1)
								{
								?>
									<tr class="tbl_bottom">
										<td colspan="7" align="right"><b>Color Total:</b></td>
										<td align="right"><? echo number_format($color_qty); ?></td>
									</tr>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
								<?
									unset($color_qty);
								}
								else
								{
									?>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
									<?
								}					
								$color_array[]=$row[csf('color_id')];            
								$k++;
							}							
						   ?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60" align="center"><? echo $row[csf("sys_id")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("production_date")]);?> </td> 
								<td width="100"><p><? echo $party_name; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="130"><p><? echo $process_name; ?></p></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("production_qnty")],2); ?></td>
							</tr>
							<? 
							$color_qty+=$row[csf("production_qnty")];
							$tot_qty+=$row[csf("production_qnty")];
							
						}
						else
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60" align="center"><? echo $row[csf("sys_id")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("production_date")]);?> </td> 
								<td width="100"><p><? echo $party_name; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="130"><p><? echo $process_name; ?></p></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("production_qnty")],2); ?></td>
							</tr>
							<?
							$tot_qty+=$row[csf("production_qnty")];
						}
					}
					if ($process_id==2)
					{ 
                    ?>
                        <tr class="tbl_bottom">
                            <td colspan="7" align="right"><b>Color Total:</b></td>
                            <td align="right"><? echo number_format($color_qty); ?></td>
                        </tr>
					<? } ?>
                    <tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total: </td>
                        <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    </tr>
                </table>
            </div> 
	</fieldset>
 </div> 
	<?
	exit();
}

if($action=="delivery_qty_pop_up")
{
	echo load_html_head_contents("Delivery Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
        <fieldset style="width:820px">
            <div style="width:100%;" align="center">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                    <thead>
                    	<tr>
                            <th width="30">SL</th>
                            <th width="60">Delivery ID</th>
                            <th width="70">Delivery Date</th>
                            <th width="80">Batch No</th>
                            <th width="80">Order No</th>
                            <th width="80">Category</th>
                            <th width="150">Description</th>
                            <th width="">Delivery Qty</th>
                    	</tr>
                    </thead>
                </table>
            </div>  
            <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
                <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                    <?
					$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
					$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
					$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
					$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
					$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');
                    $i=0;
                    $sql= "select a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.item_id, b.color_id, b.batch_id, sum(b.delivery_qty) as quantity from  subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.status_active=1 and a.is_deleted=0 group by a.delivery_prefix_num, a.delivery_date, a.party_id, b.order_id, b.process_id, b.batch_id, b.item_id, b.color_id order by a.delivery_prefix_num, a.delivery_date";
                    //echo $sql;
					$production_sql= sql_select($sql); $color_array=array(); $k=1; $process_id=0;
					foreach( $production_sql as $row )
                    {
                        $i++;
                        if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						$process_id=$row[csf("process_id")];
						if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
						{
							$item_name=$garments_item[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==2)
						{
							$item_name=$kniting_item_arr[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
						{
							$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
						}
						
						if ($row[csf("process_id")]==2)
						{
							if (!in_array($row[csf("color_id")],$color_array) )
							{
								if($k!=1)
								{
								?>
									<tr class="tbl_bottom">
										<td colspan="7" align="right"><b>Color Total:</b></td>
										<td align="right"><? echo number_format($color_qty); ?></td>
									</tr>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
								<?
									unset($color_qty);
								}
								else
								{
									?>
									<tr bgcolor="#dddddd">
										<td colspan="8" align="left" ><b>Color : <? echo $color_arr[$row[csf("color_id")]]; ?></b></td>
									</tr>
									<?
								}					
								$color_array[]=$row[csf('color_id')];            
								$k++;
							}							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf("delivery_prefix_num")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
								<td width="80"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("quantity")],2); ?></td>
							</tr>
							<? 
							$color_qty+=$row[csf("quantity")];
							$tot_qty+=$row[csf("quantity")];
						}
						else
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="60"><? echo $row[csf("delivery_prefix_num")];?> </td>
								<td width="70"><? echo change_date_format($row[csf("delivery_date")]);?> </td> 
								<td width="80"><p><? echo $batch_arr[$row[csf("batch_id")]]; ?></p></td>
								<td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
								<td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
								<td align="center" width="150"><p><? echo $item_name; ?></p></td>
								<td align="right" width=""><? echo number_format($row[csf("quantity")],2); ?></td>
							</tr>
							<? 
							$tot_qty+=$row[csf("quantity")];
						}
					} 
					if($process_id==2)
					{
					?>
                        <tr class="tbl_bottom">
                            <td colspan="7" align="right"><b>Color Total:</b></td>
                            <td align="right"><? echo number_format($color_qty); ?></td>
                        </tr>
                    <?
					}
					?>
                    <tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total: </td>
                        <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                    </tr>
                </table>
            </div> 
	</fieldset>
	 </div> 
	<?
	exit();
}

if($action=="bill_qty_pop_up")
{
	echo load_html_head_contents("Delivery Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
	    <fieldset style="width:820px">
	        <div style="width:100%;" align="center">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	                <thead>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="100">Bill ID</th>
	                        <th width="70">Bill Date</th>
	                        <th width="100">Party</th>
	                        <th width="80">Order No</th>
	                        <th width="80">Category</th>
	                        <th width="150">Description</th>
	                        <th width="80">Bill Qty</th>
	                        <th>Amount</th>
	                    </tr>
	                </thead>
	            </table>
	        </div>  
	        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
	            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
	                <?
	                $party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
					$kniting_item_arr=return_library_array( "select id, const_comp from lib_subcon_charge",'id','const_comp');
					$dye_fin_item_arr=return_library_array( "select id, item_description from pro_batch_create_dtls",'id','item_description');
	                $i=0;
	                $sql= "select a.bill_no, a.bill_date, a.party_id, b.order_id, b.process_id, b.item_id, sum(b.delivery_qty) as quantity, sum(b.amount) as amount from  subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and b.order_id in ($expData[0]) and b.process_id='$expData[1]' and a.status_active=1 and a.is_deleted=0  group by a.bill_no, a.bill_date, a.party_id, b.order_id, b.process_id, b.item_id order by a.bill_no, a.bill_date";
	                //echo $sql;
	                $production_sql= sql_select($sql);
	                foreach( $production_sql as $row )
	                {
	                    $i++;
	                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
						if ($row[csf("process_id")]==1 || $row[csf("process_id")]==5)
						{
							$item_name=$garments_item[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==2)
						{
							$item_name=$kniting_item_arr[$row[csf('item_id')]];
						}
						else if ($row[csf("process_id")]==3 || $row[csf("process_id")]==4)
						{
							$item_name=$dye_fin_item_arr[$row[csf('item_id')]];
						}
	               ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="100"><? echo $row[csf("bill_no")];?> </td>
	                    <td width="70"><? echo change_date_format($row[csf("bill_date")]);?> </td> 
	                    <td width="100"><p><? echo $party_arr[$row[csf("party_id")]]; ?></p></td>
	                    <td align="center" width="80"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
	                    <td align="center" width="80"><? echo $production_process[$row[csf("process_id")]]; ?></td>
	                    <td align="center" width="150"><? echo $item_name; ?></td>
	                    <td align="right" width="80"><? echo number_format($row[csf("quantity")],2); ?></td>
	                    <td align="right" width=""><? echo number_format($row[csf("amount")],2); ?></td>
	                </tr>
	                <? 
	                $tot_qty+=$row[csf("quantity")];
	                $tot_amount+=$row[csf("amount")];
	                } ?>
	                <tr class="tbl_bottom">
	                    <td colspan="7" align="right">Total: </td>
	                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
	                    <td align="right"><p><? echo number_format($tot_amount,2); ?></p></td>
	                </tr>
	            </table>
	        </div> 
		</fieldset>
	 </div> 
	<?
	exit();
}

if($action=="image_view_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Work Progress Info","../../../", 1, 1, $unicode);
	//echo "select master_tble_id,image_location from common_photo_library where form_name='sub_contract_order_entry' and file_type=1 and master_tble_id='$id'";

	$imge_data=sql_select("select id,master_tble_id,image_location from common_photo_library where form_name='sub_contract_order_entry' and file_type=1 and master_tble_id='$id'");
	?>
	<table>
        <tr>
			<?
            foreach($imge_data as $row)
            {
				?>
                    <td><img src='../../../<? echo $imge_arr[$row[csf("id")]]; ?>' height='100px' width='100px' /></td>
				<?
            }
            ?>
        </tr>
	</table>
	<?
	exit();
}

if($action=="batch_qty_pop_up")
{
	echo load_html_head_contents("Batch Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	$order_id=$expData[0];
	//$process_id=$expData[1];
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
	?>
    <fieldset style="width:800px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="80">Batch No</th>
                        <th width="30">Ext.</th>
                        <th width="65">Batch Date</th>
                        <th width="100">Color</th>
                        <th width="100">Order</th>
                        <th width="100">Rec. Challan</th>
                        <th width="180">Description</th>
                        <th width="">Batch Qty</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >    
			<?
				$sql_batch="Select a.batch_no, a.extention_no, a.batch_date, a.color_id, b.po_id, b.item_description, b.rec_challan, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and b.po_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.batch_no, a.extention_no, a.batch_date, a.color_id, b.po_id, b.item_description, b.rec_challan";
				$sql_batch_result=sql_select($sql_batch); $i=0;
				foreach ($sql_batch_result as $row)
				{
					$i++;
					if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30"><? echo $i; ?></td>
						<td width="80" align="center"><? echo $row[csf("batch_no")];?> </td>
						<td width="30" align="center"><? echo $row[csf("extention_no")];?> </td>
						<td width="65"><? echo change_date_format($row[csf("batch_date")]);?> </td> 
						<td width="100"><p><? echo $color_arr[$row[csf("color_id")]];?></p></td>
						<td width="100"><? echo $po_arr[$row[csf("po_id")]]; ?></td>
						<td width="100"><p><? echo $row[csf("rec_challan")]; ?></p></td>
						<td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>
						<td align="right" width=""><? echo number_format($row[csf("batch_qnty")],2); ?></td>
					</tr>
					<?
					$tot_batch_qnty+=$row[csf("batch_qnty")];
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_batch_qnty,2); ?></p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
	<?
	exit();
}

if($action=="payment_rec_pop_up")
{
	echo load_html_head_contents("Payment Receive Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	$order_id=$expData[0];
	$order_bill_amount=$expData[1];
	//$process_id=$expData[1];
	$po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Rec. No</th>
                        <th width="120">Party</th>
                        <th width="65">Rec. Date</th>
                        <th width="80">Instrument</th>
                        <th width="60">Currency</th>
                        <th width="120">Bill No</th>
                        <th width="80">Order No</th>
                        <th width="65">Bill Date</th>
                        <th width="">Rec. Amount</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:330px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >    
			<?
			$order_wise_tot_bill="select a.id, d.order_id, b.bill_id, b.bill_amount as bill_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.order_id='$order_id' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by a.id asc";
			$order_wise_tot_bill_result=sql_select($order_wise_tot_bill);
			foreach ($order_wise_tot_bill_result as $row)
			{
				$order_wise_tot_bill_arr2[$row[csf('order_id')]][$row[csf('bill_id')]][$row[csf('id')]]=$row[csf('bill_amount')];
			}

			$sum=0;
			foreach ($order_wise_tot_bill_arr2 as $key=>$value) 
			{
				foreach ($value as $val) 
				{
					foreach ($val as $val2) 
					{
						 $sum+=$val2;
						 break;
					}
				}
				$order_wise_tot_bill_arr[$key]=$sum;
				$sum=0;
			}

				//$payment_sql="select a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id, sum(b.total_adjusted) as rec_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and d.order_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id";

			$payment_sql="select a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id, b.total_adjusted as rec_amount from  subcon_payment_receive_mst a, subcon_payment_receive_dtls b, subcon_inbound_bill_mst c, subcon_inbound_bill_dtls d where a.id=b.master_id and b.bill_id=c.id and c.id=d.mst_id and d.order_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.prefix_no_num, a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.currency_id, c.bill_no, c.bill_date, d.order_id,b.total_adjusted";

				$payment_sql_result=sql_select($payment_sql); $i=0;
				foreach ($payment_sql_result as $row)
				{
					$i++;
					if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $row[csf("receive_no")];?> </td>
						<td width="120" align="center"><? echo $buyer_arr[$row[csf("party_name")]];?> </td>
						<td width="65"><? echo change_date_format($row[csf("receipt_date")]);?> </td> 
						<td width="80"><p><? echo $instrument_payment[$row[csf("instrument_id")]];?></p></td>
						<td width="60"><? echo $currency[$row[csf("currency_id")]]; ?></td>
						<td width="120"><p><? echo $row[csf("bill_no")]; ?></p></td>
                        <td width="80"><p><? echo $po_arr[$row[csf("order_id")]]; ?></p></td>
						<td width="65"><? echo change_date_format($row[csf("bill_date")]);?> </td> 
						<td align="right" width="">
							<? 
							$received_amount = ($row[csf("rec_amount")]/$order_wise_tot_bill_arr[$order_id])*$order_bill_amount;
							echo number_format($received_amount,2); 
							
							//echo number_format($row[csf("rec_amount")],2); 
							?>
						</td>
					</tr>
					<?
					$tot_rec_amount+=$received_amount;
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="9" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_rec_amount,2); ?></p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
	<?
	exit();
}

if($action=="order_desc_popup")
{
	echo load_html_head_contents("Order Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Order Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Order No</th>
                        <th width="70">Category</th>
                        <th width="120">Item Description </th>
                        <th width="80">Color</th>
                        <th width="60">Size</th>
                        <th width="80">Receive Date</th>
                        <th width="50">Rate</th>
                        <th width="93">Quantity</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
				//$company_array=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

                $item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');	
				$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;

                $sql="select a.party_id, b.order_no, b.order_rcv_date, b.main_process_id, c.item_id, c.color_id, c.size_id, c.qnty, c.rate, c.gsm, c.grey_dia, c.finish_dia, c.dia_width_type from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where c.mst_id=a.id and c.order_id=b.id and a.subcon_job=b.job_no_mst and b.id=$expData[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
              
                $order_dtls_sql= sql_select($sql);
                foreach( $order_dtls_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";

                    $process_id=$row[csf('main_process_id')];
					
						//$material_name=$row[csf("material_description")].', '.$color_arr[$row[csf("color_id")]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")];
               ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("order_no")];?> </td>
                    <td align="center" width="70"><? echo $production_process[$row[csf("main_process_id")]];?> </td>
                    <td width="120">
                    	<? 
			                if($process_id==2 || $process_id==3 || $process_id==4 || $process_id==6 || $process_id==7)
							{
								echo $item_arr[$row[csf('item_id')]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("finish_dia")];	
							}
							else
							{
								echo $garments_item[$row[csf('item_id')]].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("finish_dia")];
							}
                    	?> 
                    </td> 
                    <td align="center" width="80"><p><? echo  $color_arr[$row[csf("color_id")]]; ?></p></td>
                    <td align="center" width="60"><? echo $size_arr[$row[csf("size_id")]]; ?></td>
                    <td align="center" width="80"><? echo change_date_format($row[csf("order_rcv_date")]); ?></td>
                    
                    <td align="right" width="50"><? echo $row[csf("rate")]; ?> &nbsp; </td>
                    <td align="right" width="80"><? echo number_format($row[csf("qnty")]); ?> &nbsp;</td>
                   
                </tr>
                <? 
                $tot_qty+=$row[csf("qnty")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="8" align="right">Total: &nbsp;</td>
                    <td align="right"><p><? echo number_format($tot_qty); ?> &nbsp; </p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
	<?
	exit();
}

if($action=="report_generate_color")
{ 
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$job_no=str_replace("'","",$txt_job_no);
	
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$year_id=str_replace("'","",$cbo_year);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	
	$cbo_mainbuyer_id=str_replace("'","",$cbo_mainbuyer_id);//Buyer_ID
	
	if($year_id!=0) $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	if($cbo_buyer_id!=0) $buyer_id_cond=" AND DM.PARTY_ID ='$cbo_buyer_id'"; else $buyer_id_cond="";
	
	if($cbo_company_id!=0) $cbo_company_id_cond=" AND DM.COMPANY_ID ='$cbo_company_id'"; else $cbo_company_id_cond="";
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and DM.JOB_NO = '$job_no' ";
	//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
	if ($txt_style_ref!='') $style_ref_cond=" AND PDM.STYLE_REF_NO like '%$txt_style_ref%'"; else $style_ref_cond="";
	//if(trim($txt_order_no)!="") $order_no_cond="%".trim($txt_order_no)."%"; else $order_no_cond="%%";
	if ($txt_order_no!='') $order_no_cond=" and DD.BUYER_PO_ID like '%$txt_order_no%'"; else $order_no_cond="";
	if ($txt_order_no!='') $order_no_cond_subcon=" and DD.order_id like '%$txt_order_no%'"; else $order_no_cond_subcon="";
	
	
	if($cbo_mainbuyer_id!=0) $cbo_mainbuyer_id_cond=" AND PDM.BUYER_NAME ='$cbo_mainbuyer_id'"; else $cbo_mainbuyer_id_cond="";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and DM.DELIVERY_DATE  between $txt_date_from and $txt_date_to";
	//echo $txt_date_from;echo "AND to date ".$txt_date_to;die;
	//echo $cbo_mainbuyer_id_cond;die;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$gmt_item_Arr = return_library_array("select id,ITEM_NAME from  LIB_GARMENT_ITEM ","id","ITEM_NAME");
	$body_part_Arr = return_library_array("select id,BODY_PART_FULL_NAME from  LIB_BODY_PART ","id","BODY_PART_FULL_NAME");
	
	$buyer_po_Arr = return_library_array("select id,PO_NUMBER from WO_PO_BREAK_DOWN ","id","PO_NUMBER");
	$location_Arr = return_library_array("SELECT ID, LOCATION_NAME FROM LIB_LOCATION ","id","LOCATION_NAME");
	$Style_Arr = return_library_array("SELECT a.ID, b.STYLE_REF_NO FROM WO_PO_BREAK_DOWN a INNER JOIN WO_PO_DETAILS_MASTER b ON a.JOB_NO_MST = b.JOB_NO","id","STYLE_REF_NO");
	$ord_qty = return_library_array("SELECT d.BUYER_PO_ID as ID, SUM(d.ORDER_QUANTITY) * 12 AS ORDER_QUANTITY FROM SUBCON_ORD_DTLS d WHERE d.STATUS_ACTIVE = 1 AND d.IS_DELETED = 0 GROUP BY d.BUYER_PO_ID","ID","ORDER_QUANTITY");
	//$buyer_arr = return_library_array("SELECT   a.ID,  c.BUYER_NAME FROM WO_PO_BREAK_DOWN a INNER JOIN WO_PO_DETAILS_MASTER b ON a.JOB_NO_MST = b.JOB_NO
//INNER JOIN LIB_BUYER c ON b.BUYER_NAME = c.ID ","id","BUYER_NAME");
	
	?>
    <div>
		<?/* echo "Test";
		select lib_company.company_name, lib_company1.company_name as party_name, lib_buyer.buyer_name, lib_location.location_name, lib_garment_item.item_name, lib_color.color_name, lib_size.size_name, wo_po_break_down.po_number, wo_po_details_master.style_ref_no, mst.company_id, mst.location_id, mst.party_id, mst.receive_date, mst.delivery_date, mst.job_no_prefix_num, mst.job_no_mst, mst.order_no, mst.po_delivery_date, mst.order_id, mst.item_id, mst.color_id, mst.size_id, mst.qnty, mst.rate, mst.amount, mst.buyer_po_id, mst.gmts_item_id, mst.embl_type, mst.body_part from (select som.company_id, som.location_id, som.party_id, som.receive_date, som.delivery_date, som.job_no_prefix_num, sod.job_no_mst, sod.order_no, sod.delivery_date as po_delivery_date, sob.order_id, sob.item_id, sob.color_id, sob.size_id, sob.qnty, sob.rate, sob.amount, sod.buyer_po_id, sod.gmts_item_id, sod.embl_type, sod.body_part from subcon_ord_mst som inner join subcon_ord_dtls sod on som.id = sod.mst_id inner join subcon_ord_breakdown sob on sod.id = sob.mst_id where som.company_id = 3 and som.job_no_prefix_num = 11 and som.status_active = 1 ) mst inner join lib_company on mst.company_id = lib_company.id inner join lib_location on lib_location.id = mst.location_id inner join lib_garment_item on mst.item_id = lib_garment_item.id inner join lib_color on mst.color_id = lib_color.id inner join lib_size on mst.size_id = lib_size.id inner join wo_po_break_down on mst.buyer_po_id = wo_po_break_down.id inner join wo_po_details_master on wo_po_break_down.job_no_mst = wo_po_details_master.job_no inner join lib_buyer on lib_buyer.id = wo_po_details_master.buyer_name inner join lib_company lib_company1 on lib_company1.id = mst.party_id 
		
		$style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond
		
		*/
		//--------------------------------------------------------Start----------------------------------------
	
			  
	$query_t = "SELECT SUM(CASE WHEN SMM.TRANS_TYPE = '1' THEN SMD.QUANTITY END) AS RECVD_QTY, SUM(CASE WHEN SMM.TRANS_TYPE = '2' THEN SMD.QUANTITY END)  AS ISSUE_QTY,
			  SMM.SYS_NO AS TRANS_NO,  SMM.COMPANY_ID,  SMM.TRANS_TYPE,  SMM.LOCATION_ID,  SMM.PARTY_ID,  SMM.SUBCON_DATE AS TRANS_DATE,  SMM.CHALAN_NO,  SMD.EMB_NAME_ID,
			  SMM.EMBL_JOB_NO,  SMD.BUYER_PO_ID,  SOB.COLOR_ID,  SOB.EMBELLISHMENT_TYPE AS EMB_TYPE,  SOB.BODY_PART,  P.PO_NUMBER,  PM.BUYER_NAME,  PM.STYLE_REF_NO
			FROM SUB_MATERIAL_MST SMM INNER JOIN SUB_MATERIAL_DTLS SMD ON SMD.MST_ID = SMM.ID INNER JOIN SUBCON_ORD_BREAKDOWN SOB ON SMD.JOB_BREAK_ID = SOB.ID
			INNER JOIN WO_PO_BREAK_DOWN P ON SMD.BUYER_PO_ID = P.ID INNER JOIN WO_PO_DETAILS_MASTER PM ON P.JOB_NO_MST = PM.JOB_NO
			WHERE SMD.STATUS_ACTIVE = 1
			AND SMM.STATUS_ACTIVE   = 1
			$cbo_company_id_cond $style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond
			GROUP BY SMM.SYS_NO,  SMM.COMPANY_ID,  SMM.TRANS_TYPE,  SMM.LOCATION_ID,  SMM.PARTY_ID,  SMM.SUBCON_DATE,  SMM.CHALAN_NO,  SMD.EMB_NAME_ID,  SMM.EMBL_JOB_NO,
			  SMD.BUYER_PO_ID,  SOB.COLOR_ID,  SOB.EMBELLISHMENT_TYPE,  SOB.BODY_PART,  SMD.JOB_DTLS_ID,  P.PO_NUMBER,  PM.BUYER_NAME,  PM.STYLE_REF_NO
			ORDER BY TRANS_DATE DESC, SMM.SYS_NO";
			  
	$query = "SELECT DM.COMPANY_ID,  DM.LOCATION_ID,  DM.DELIVERY_DATE,  DM.DELIVERY_NO,  DM.PARTY_ID,  DM.JOB_NO,  DD.COLOR_SIZE_ID,  DD.BUYER_PO_ID,  DD.SORT_QTY, DD.PRINT_REJECT_QTY, DD.FABRIC_REJECT_QTY,
			  DM.REMARKS,  DM.DELI_PARTY,  DM.DELI_PARTY_LOCATION,  DD.DELIVERY_STATUS,  DD.DELIVERY_QTY,  PBD.PO_NUMBER,  PDM.BUYER_NAME,  PDM.STYLE_REF_NO,  PDM.STYLE_DESCRIPTION,
			  OB.COLOR_ID,  OB.ORDER_ID,OB.EMBELLISHMENT_TYPE, OB.BODY_PART
				FROM SUBCON_DELIVERY_MST DM INNER JOIN SUBCON_DELIVERY_DTLS DD ON DM.ID = DD.MST_ID INNER JOIN WO_PO_BREAK_DOWN PBD ON PBD.ID = DD.BUYER_PO_ID
				INNER JOIN WO_PO_DETAILS_MASTER PDM ON PBD.JOB_NO_MST = PDM.JOB_NO INNER JOIN SUBCON_ORD_BREAKDOWN OB ON DD.COLOR_SIZE_ID = OB.ID
				WHERE DM.STATUS_ACTIVE = 1
				AND DD.STATUS_ACTIVE = 1
				$cbo_company_id_cond $style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond ORDER By PDM.BUYER_NAME, DD.BUYER_PO_ID ";
				
	$query_subcon="select     dm.company_id,    dm.location_id, dm.within_group,    dm.delivery_date,    dm.delivery_no,    dm.party_id,    dm.job_no,  dd.order_id,  dd.color_size_id,    dd.sort_qty,    dd.print_reject_qty,    dd.fabric_reject_qty,
					ob.color_id,    ob.size_id,    ob.embellishment_type,    ob.body_part,     dd.delivery_qty,     dd.delivery_status,     dm.remarks    
					from subcon_delivery_mst dm  inner join subcon_delivery_dtls  dd on dm.id = dd.mst_id inner join subcon_ord_breakdown  ob on dd.color_size_id = ob.id
					where dm.within_group = 2  and dm.status_active = 1  and dd.status_active = 1 $cbo_company_id_cond $job_no_cond $date_cond $buyer_id_cond $order_no_cond_subcon
					order by  dd.order_id";					
	$sql_data_query_subcon = sql_select($query_subcon);
	foreach( $sql_data_query_subcon as $row)
	{		
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["order_id"]=$row[csf(order_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["color_id"]=$row[csf(color_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["size_id"]=$row[csf(size_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["within_group"]=$row[csf(within_group)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["company_id"]=$row[csf(company_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]]["location_id"]=$row[csf(location_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]]["delivery_date"]=$row[csf(delivery_date)];				
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["delivery_no"]=$row[csf(delivery_no)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["job_no"]=$row[csf(job_no)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["color_size_id"]=$row[csf(color_size_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["buyer_po_id"]=$row[csf(buyer_po_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["party_id"]=$row[csf(party_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["delivery_status"]=$row[csf(delivery_status)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["remarks"]=$row[csf(remarks)];		
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["deli_party"]=$row[csf(deli_party)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["buyer_name"]=$row[csf(buyer_name)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["deli_party_location"]=$row[csf(deli_party_location)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["po_number"]=$row[csf(po_number)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["delivery_qty"]+=$row[csf(delivery_qty)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["sort_qty"]+=$row[csf(sort_qty)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["style_ref_no"]=$row[csf(style_ref_no)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["embellishment_type"]=$row[csf(embellishment_type)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["body_part"]=$row[csf(body_part)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["print_reject_qty"]+=$row[csf(print_reject_qty)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["fabric_reject_qty"]+=$row[csf(fabric_reject_qty)];

		$sub_total_arr[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]]+=$row[csf(delivery_qty)];
		
		$sub_short_arr[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]]+=$row[csf(sort_qty)];
		$sub_rej_print_arr[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]]+=$row[csf(print_reject_qty)];
		$sub_rej_fab_arr[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]]+=$row[csf(fabric_reject_qty)];
	}
					
	//echo $query;die;
	$sql_data_query = sql_select($query);
	$countRecords = count($query); 
	//echo $sql_data_query;
	ob_start();
	$details_data=array();

	foreach( $sql_data_query as $row)
	{
		//detail data in Array  
		
		//ORDER_ID, COLOR_ID, COMPANY_ID,  LOCATION_ID,  DELIVERY_DATE,  DELIVERY_NO,  PARTY_ID,  JOB_NO, COLOR_SIZE_ID, BUYER_PO_ID,  DD.SORT_QTY,
		///REMARKS,  DELI_PARTY,  DELI_PARTY_LOCATION,  DELIVERY_STATUS,  DELIVERY_QTY,  PO_NUMBER, BUYER_NAME,  STYLE_REF_NO
				
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["ORDER_ID"]=$row[csf(ORDER_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["COLOR_ID"]=$row[csf(COLOR_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["COMPANY_ID"]=$row[csf(COMPANY_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]]["LOCATION_ID"]=$row[csf(LOCATION_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]]["DELIVERY_DATE"]=$row[csf(DELIVERY_DATE)];				
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["DELIVERY_NO"]=$row[csf(DELIVERY_NO)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["JOB_NO"]=$row[csf(JOB_NO)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["COLOR_SIZE_ID"]=$row[csf(COLOR_SIZE_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["BUYER_PO_ID"]=$row[csf(BUYER_PO_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["PARTY_ID"]=$row[csf(PARTY_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["DELIVERY_STATUS"]=$row[csf(DELIVERY_STATUS)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["REMARKS"]=$row[csf(REMARKS)];		
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["DELI_PARTY"]=$row[csf(DELI_PARTY)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["BUYER_NAME"]=$row[csf(BUYER_NAME)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["DELI_PARTY_LOCATION"]=$row[csf(DELI_PARTY_LOCATION)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["PO_NUMBER"]=$row[csf(PO_NUMBER)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["DELIVERY_QTY"]+=$row[csf(DELIVERY_QTY)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["SORT_QTY"]+=$row[csf(SORT_QTY)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["STYLE_REF_NO"]=$row[csf(STYLE_REF_NO)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["EMBELLISHMENT_TYPE"]=$row[csf(EMBELLISHMENT_TYPE)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["BODY_PART"]=$row[csf(BODY_PART)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["PRINT_REJECT_QTY"]+=$row[csf(PRINT_REJECT_QTY)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("DELIVERY_NO")]] ["FABRIC_REJECT_QTY"]+=$row[csf(FABRIC_REJECT_QTY)];

		$sub_total_arr[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]+=$row[csf(DELIVERY_QTY)];
		
		$sub_short_arr[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]+=$row[csf(SORT_QTY)];
		$sub_rej_print_arr[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]+=$row[csf(PRINT_REJECT_QTY)];
		$sub_rej_fab_arr[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]+=$row[csf(FABRIC_REJECT_QTY)];
	}
	//echo "<pre>";print_r($details_data);die;	
	
	$query_sub ="SELECT DISTINCT OB.ID, OB.BODY_PART, OD.ORDER_ID,  OD.BUYER_PO_ID,  OB.COLOR_ID,  OB.EMBELLISHMENT_TYPE
FROM SUBCON_ORD_MST OM INNER JOIN SUBCON_ORD_DTLS OD ON OM.ID = OD.MST_ID INNER JOIN SUBCON_ORD_BREAKDOWN OB ON OD.JOB_NO_MST    = OB.JOB_NO_MST";
	//echo $query_sub;die;
	$sql_data_query_sub = sql_select($query_sub);
	$countRecords = count($query_sub); 
	//echo $sql_data_query_sub;
	$details_data_sub=array();
	foreach( $sql_data_query_sub as $rows)
	{
		$details_data_sub[$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["EMBELLISHMENT_TYPE"]=$rows[csf(EMBELLISHMENT_TYPE)];
		$details_data_sub[$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["BODY_PART"]=$rows[csf(BODY_PART)];
	}
	//echo "<pre>";print_r($details_data_sub);die;
	
	?>
	<div style="width:1550px; margin:0 auto;">
		<fieldset style="width:100%;">	
		    <table width="98%" cellpadding="0" cellspacing="0" id="caption">
	            <tr>  
	                <td align="center" width="100%" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">Screen Print Delivery Report </strong>
	                </td>
	            </tr>
				<tr>  
	                <td align="center" width="100%" colspan="11" class="form_caption" >
	                	<strong style="font-size:14px">&nbsp <? echo "Report Date is"." ".$txt_date_from." To ".$txt_date_to;?></strong>
	                </td>
	            </tr>
	        </table>
			<div style="width:1550px; align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1540"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" rules="all">
					<thead>
						<th style="word-wrap: break-word;word-break: break-all;" width="30" align="center">Sl </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">BUYER</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">PO No</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="120" align="center">STYLE</th>		
						<th style="word-wrap: break-word;word-break: break-all;" width="60" align="center"> DATE </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="115" align="center">Del NO </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="150" align="center">PARTY </th>		
						<th style="word-wrap: break-word;word-break: break-all;" width="150" align="center">Del Place</th>
						
						<th style="word-wrap: break-word;word-break: break-all;" width="120" align="center">COLOR</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">EMB_TYPE</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">BODY_PART</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="70" align="center">Del QTY</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="65" align="center">S.QTY</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="65" align="center">Rej Print</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="65" align="center">Rej Fab</th>
						<th style="word-wrap: break-word;word-break: break-all;" align="center">Remarks</th>					
					</thead>
				</table>
			</div>
			<div style="width:1550px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1540"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<?	
						//ORDER_ID, COLOR_ID, COMPANY_ID,  LOCATION_ID,  DELIVERY_DATE,  DELIVERY_NO,  PARTY_ID,  JOB_NO, COLOR_SIZE_ID, BUYER_PO_ID,  DD.SORT_QTY,
						//REMARKS,  DELI_PARTY,  DELI_PARTY_LOCATION,  DELIVERY_STATUS,  DELIVERY_QTY,  PO_NUMBER, BUYER_NAME,  STYLE_REF_NO
						$k=1;
						foreach($details_data as $company_id=>$company_data)
						{
							foreach($company_data as $party_id=>$party_data)
							{
							  foreach($party_data as $wo_id=>$wo_data)
							  {
								foreach($wo_data as $po_id=>$po_data)
								{
									foreach($po_data as $color_id=>$color_val)
									{
										foreach($color_val as $del_id=>$color_data)
										{
										if ($k%2==0)  
											$bgcolor="#E9F3FF";
											else
											$bgcolor="#FFFFFF";
										//ORDER_ID, COLOR_ID, COMPANY_ID,  LOCATION_ID,  DELIVERY_DATE,  DELIVERY_NO,  PARTY_ID,  JOB_NO, COLOR_SIZE_ID, BUYER_PO_ID,  DD.SORT_QTY,
						//REMARKS,  DELI_PARTY,  DELI_PARTY_LOCATION,  DELIVERY_STATUS,  DELIVERY_QTY,  PO_NUMBER, BUYER_NAME,  STYLE_REF_NO
						
										?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
												<td style="word-wrap: break-word;word-break: break-all;" width="30"> <? echo $k; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="100"> <? echo $buyerArr[$color_data[('BUYER_NAME')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="100">  <? echo $buyer_po_Arr[$color_data[('BUYER_PO_ID')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="120">  <? echo $color_data[('STYLE_REF_NO')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="60"> <? echo $color_data[('DELIVERY_DATE')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="115">  <? echo $color_data[('DELIVERY_NO')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="150">  <? echo $companyArr[$color_data[('PARTY_ID')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="150"> <? echo $location_Arr[$color_data[('DELI_PARTY_LOCATION')]]; ?></td>
					
												
												<td style="word-wrap: break-word;word-break: break-all;" width="120">  <? echo $colorArr[$color_data[('COLOR_ID')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85">  <? echo $emblishment_print_type[$color_data[('EMBELLISHMENT_TYPE')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85" align="right">  <? echo $body_part_Arr[$color_data[('BODY_PART')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="70" align="right">  <? echo $color_data[('DELIVERY_QTY')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="65" align="right">  <? echo $color_data[('SORT_QTY')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="65" align="center">  <? echo $color_data[('PRINT_REJECT_QTY')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="65" align="center">  <? echo $color_data[('FABRIC_REJECT_QTY')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" align="right">  <? echo $color_data[('REMARKS')]; ?></td>
												
											</tr>
											<?
											$k++;
											$total_del_qty+=$color_data[('DELIVERY_QTY')];
											$total_short_qty+=$color_data[('SORT_QTY')];
											$total_rej_print_qty+=$color_data[('PRINT_REJECT_QTY')];
											$total_rej_fab_qty+=$color_data[('FABRIC_REJECT_QTY')];
;
										}
										
										?>
										<tr bgcolor="#dddddd">   
											<td align="right" colspan="11" style="word-wrap: break-word;word-break: break-all;" width="30"> <strong>PO and Color Total :</strong></td>	
											<td align="right" style="word-wrap: break-word;word-break: break-all;"> <strong>
											<?
												echo $sub_total_arr[$color_data[('COMPANY_ID')]][$color_data[('PARTY_ID')]][$color_data[('ORDER_ID')]][$color_data[('BUYER_PO_ID')]][$color_data[('COLOR_ID')]]; 
											?></strong></td>
											<td align="right" style="word-wrap: break-word;word-break: break-all;"><strong>
											<? 
												echo $sub_short_arr[$color_data[('COMPANY_ID')]][$color_data[('PARTY_ID')]][$color_data[('ORDER_ID')]][$color_data[('BUYER_PO_ID')]][$color_data[('COLOR_ID')]]; 
											?></strong>
											<td align="center" style="word-wrap: break-word;word-break: break-all;"><strong>
											<? 
												echo $sub_rej_print_arr[$color_data[('COMPANY_ID')]][$color_data[('PARTY_ID')]][$color_data[('ORDER_ID')]][$color_data[('BUYER_PO_ID')]][$color_data[('COLOR_ID')]]; 
											?></strong>
											<td align="center" style="word-wrap: break-word;word-break: break-all;"><strong>
											<? 
												echo $sub_rej_fab_arr[$color_data[('COMPANY_ID')]][$color_data[('PARTY_ID')]][$color_data[('ORDER_ID')]][$color_data[('BUYER_PO_ID')]][$color_data[('COLOR_ID')]]; 
											?></strong>
											</td>
											<td style="word-wrap: break-word;word-break: break-all;"></td>
										</tr>
										<?
									}
									}
								}
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr bgcolor="#dddddd">   
							

						
							<td colspan="11"  align="right"><strong>Grand Total :</strong></td>
							<td  align="right"  id="value_total_del_qty"> <strong><? echo number_format($total_del_qty,2,'.',''); ?></strong></td>
							<td  align="right"  id="value_total_short_qty"> <strong> <? echo number_format($total_short_qty,2,'.',''); ?></strong></td>
							<td  align="center"  id="value_total_short_qty"> <strong> <? echo number_format($total_rej_print_qty,2,'.',''); ?></strong></td>
							<td  align="center"  id="value_total_short_qty"> <strong> <? echo number_format($total_rej_fab_qty,2,'.',''); ?></strong></td>
							<td  align="right"> </td>
													
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>	
		<?
				//--------------------------------------------------------End----------------------------------------
		?>
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

if($action=="report_generate_subcon")
{ 
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$job_no=str_replace("'","",$txt_job_no);
	
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$year_id=str_replace("'","",$cbo_year);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	
	$type = str_replace("'","",$type);
	
	$cbo_mainbuyer_id=str_replace("'","",$cbo_mainbuyer_id);//buyer_id
	
	if($year_id!=0) $year_cond=" and to_char(a.insert_date,'yyyy')=$year_id"; else $year_cond="";
	if($cbo_buyer_id!=0) $buyer_id_cond=" and dm.party_id ='$cbo_buyer_id'"; else $buyer_id_cond="";
	
	if($cbo_company_id!=0) $cbo_company_id_cond=" and dm.company_id ='$cbo_company_id'"; else $cbo_company_id_cond="";
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and dm.job_no = '$job_no' ";
	//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
	if ($txt_style_ref!='') $style_ref_cond=" and pdm.style_ref_no like '%$txt_style_ref%'"; else $style_ref_cond="";
	//if(trim($txt_order_no)!="") $order_no_cond="%".trim($txt_order_no)."%"; else $order_no_cond="%%";
	if ($txt_order_no!='') $order_no_cond=" and dd.buyer_po_id like '%$txt_order_no%'"; else $order_no_cond="";
	if ($txt_order_no!='') $order_no_cond_subcon=" and dd.order_id like '%$txt_order_no%'"; else $order_no_cond_subcon="";
	
	
	if($cbo_mainbuyer_id!=0) $cbo_mainbuyer_id_cond=" and pdm.buyer_name ='$cbo_mainbuyer_id'"; else $cbo_mainbuyer_id_cond="";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and dm.delivery_date  between $txt_date_from and $txt_date_to";
	//echo $txt_date_from;echo "and to date ".$txt_date_to;die;
	//echo $cbo_mainbuyer_id_cond;die;
	$colorarr = return_library_array("select id,color_name from lib_color","id","color_name");
	$companyarr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerarr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$supparr = return_library_array("select id,supplier_name from lib_supplier ","id","supplier_name");
	$itemsizearr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$gmt_item_arr = return_library_array("select id,item_name from  lib_garment_item ","id","item_name");
	$body_part_arr = return_library_array("select id,body_part_full_name from  lib_body_part ","id","body_part_full_name");
	$party_style =  return_library_array("select id, buyer_style_ref from  subcon_ord_dtls ","id","buyer_style_ref");
	
	
	$wo_arr = return_library_array("select id, order_no from subcon_ord_dtls ","id","order_no");
	$location_arr = return_library_array("select id, location_name from lib_location ","id","location_name");
	$style_arr = return_library_array("select a.id, b.style_ref_no from wo_po_break_down a inner join wo_po_details_master b on a.job_no_mst = b.job_no","id","style_ref_no");
	$ord_qty = return_library_array("select d.buyer_po_id as id, sum(d.order_quantity) * 12 as order_quantity from subcon_ord_dtls d where d.status_active = 1 and d.is_deleted = 0 group by d.buyer_po_id","id","order_quantity");
	//$buyer_arr = return_library_array("select   a.id,  c.buyer_name from wo_po_break_down a inner join wo_po_details_master b on a.job_no_mst = b.job_no
//INNER JOIN LIB_BUYER c ON b.BUYER_NAME = c.ID ","id","BUYER_NAME");
	
	?>
    <div>
		<?/* echo "Test";
		select lib_company.company_name, lib_company1.company_name as party_name, lib_buyer.buyer_name, lib_location.location_name, lib_garment_item.item_name, lib_color.color_name, lib_size.size_name, wo_po_break_down.po_number, wo_po_details_master.style_ref_no, mst.company_id, mst.location_id, mst.party_id, mst.receive_date, mst.delivery_date, mst.job_no_prefix_num, mst.job_no_mst, mst.order_no, mst.po_delivery_date, mst.order_id, mst.item_id, mst.color_id, mst.size_id, mst.qnty, mst.rate, mst.amount, mst.buyer_po_id, mst.gmts_item_id, mst.embl_type, mst.body_part from (select som.company_id, som.location_id, som.party_id, som.receive_date, som.delivery_date, som.job_no_prefix_num, sod.job_no_mst, sod.order_no, sod.delivery_date as po_delivery_date, sob.order_id, sob.item_id, sob.color_id, sob.size_id, sob.qnty, sob.rate, sob.amount, sod.buyer_po_id, sod.gmts_item_id, sod.embl_type, sod.body_part from subcon_ord_mst som inner join subcon_ord_dtls sod on som.id = sod.mst_id inner join subcon_ord_breakdown sob on sod.id = sob.mst_id where som.company_id = 3 and som.job_no_prefix_num = 11 and som.status_active = 1 ) mst inner join lib_company on mst.company_id = lib_company.id inner join lib_location on lib_location.id = mst.location_id inner join lib_garment_item on mst.item_id = lib_garment_item.id inner join lib_color on mst.color_id = lib_color.id inner join lib_size on mst.size_id = lib_size.id inner join wo_po_break_down on mst.buyer_po_id = wo_po_break_down.id inner join wo_po_details_master on wo_po_break_down.job_no_mst = wo_po_details_master.job_no inner join lib_buyer on lib_buyer.id = wo_po_details_master.buyer_name inner join lib_company lib_company1 on lib_company1.id = mst.party_id 
		
		$style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond
		
		*/
		//--------------------------------------------------------Start----------------------------------------

			  
	$query = "SELECT DM.COMPANY_ID,  DM.LOCATION_ID,  DM.DELIVERY_DATE,  DM.DELIVERY_NO,  DM.PARTY_ID,  DM.JOB_NO,  DD.COLOR_SIZE_ID,  DD.BUYER_PO_ID,  DD.SORT_QTY, DD.PRINT_REJECT_QTY, DD.FABRIC_REJECT_QTY,
			  DM.REMARKS,  DM.DELI_PARTY,  DM.DELI_PARTY_LOCATION,  DD.DELIVERY_STATUS,  DD.DELIVERY_QTY,  PBD.PO_NUMBER,  PDM.BUYER_NAME,  PDM.STYLE_REF_NO,  PDM.STYLE_DESCRIPTION,
			  OB.COLOR_ID,  OB.ORDER_ID,OB.EMBELLISHMENT_TYPE, OB.BODY_PART
				FROM SUBCON_DELIVERY_MST DM INNER JOIN SUBCON_DELIVERY_DTLS DD ON DM.ID = DD.MST_ID INNER JOIN WO_PO_BREAK_DOWN PBD ON PBD.ID = DD.BUYER_PO_ID
				INNER JOIN WO_PO_DETAILS_MASTER PDM ON PBD.JOB_NO_MST = PDM.JOB_NO INNER JOIN SUBCON_ORD_BREAKDOWN OB ON DD.COLOR_SIZE_ID = OB.ID
				WHERE DM.STATUS_ACTIVE = 1
				AND DD.STATUS_ACTIVE = 1
				$cbo_company_id_cond $style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond ORDER By PDM.BUYER_NAME, DD.BUYER_PO_ID ";
				
	$query_subcon="select     dm.company_id,    dm.location_id, dm.within_group,    dm.delivery_date,    dm.delivery_no,    dm.party_id,    dm.job_no,  dd.order_id,  dd.color_size_id,    dd.sort_qty,    dd.print_reject_qty,    dd.fabric_reject_qty,
					ob.color_id,    ob.size_id,    ob.embellishment_type,    ob.body_part,     dd.delivery_qty,     dd.delivery_status,     dm.remarks    
					from subcon_delivery_mst dm  inner join subcon_delivery_dtls  dd on dm.id = dd.mst_id inner join subcon_ord_breakdown  ob on dd.color_size_id = ob.id
					where dm.within_group = 2  and dm.status_active = 1  and dd.status_active = 1 $cbo_company_id_cond $job_no_cond $date_cond $buyer_id_cond $order_no_cond_subcon
					order by  dd.order_id";	

	//echo $query_subcon;die;
	$sql_data_query_subcon = sql_select($query_subcon);
	foreach( $sql_data_query_subcon as $row)
	{		
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["order_id"]=$row[csf(order_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["color_id"]=$row[csf(color_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["size_id"]=$row[csf(size_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["within_group"]=$row[csf(within_group)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["company_id"]=$row[csf(company_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]]["location_id"]=$row[csf(location_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]]["delivery_date"]=$row[csf(delivery_date)];				
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["delivery_no"]=$row[csf(delivery_no)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["job_no"]=$row[csf(job_no)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["color_size_id"]=$row[csf(color_size_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["buyer_po_id"]=$row[csf(buyer_po_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["party_id"]=$row[csf(party_id)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["delivery_status"]=$row[csf(delivery_status)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["remarks"]=$row[csf(remarks)];		
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["deli_party"]=$row[csf(deli_party)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["buyer_name"]=$row[csf(buyer_name)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["deli_party_location"]=$row[csf(deli_party_location)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["po_number"]=$row[csf(po_number)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["delivery_qty"]+=$row[csf(delivery_qty)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["sort_qty"]+=$row[csf(sort_qty)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["style_ref_no"]=$row[csf(style_ref_no)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["embellishment_type"]=$row[csf(embellishment_type)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["body_part"]=$row[csf(body_part)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["print_reject_qty"]+=$row[csf(print_reject_qty)];
		$details_data[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]][$row[csf("delivery_no")]] ["fabric_reject_qty"]+=$row[csf(fabric_reject_qty)];

		$sub_total_arr[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]]+=$row[csf(delivery_qty)];
		
		$sub_short_arr[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]]+=$row[csf(sort_qty)];
		$sub_rej_print_arr[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]]+=$row[csf(print_reject_qty)];
		$sub_rej_fab_arr[$row[csf("company_id")]][$row[csf("party_id")]][$row[csf("order_id")]][$row[csf("color_id")]]+=$row[csf(fabric_reject_qty)];
	}
	//echo "<pre>";print_r($details_data);die;
	
	$query_sub ="select distinct ob.id, ob.body_part, od.order_id,  od.buyer_po_id,  ob.color_id,  ob.embellishment_type
from subcon_ord_mst om inner join subcon_ord_dtls od on om.id = od.mst_id inner join subcon_ord_breakdown ob on od.job_no_mst    = ob.job_no_mst";
	//echo $query_sub;die;
	$sql_data_query_sub = sql_select($query_sub);
	$countrecords = count($query_sub); 
	//echo $sql_data_query_sub;
	$details_data_sub=array();
	foreach( $sql_data_query_sub as $rows)
	{
		$details_data_sub[$row[csf("order_id")]][$row[csf("buyer_po_id")]][$row[csf("color_id")]]["embellishment_type"]=$rows[csf(embellishment_type)];
		$details_data_sub[$row[csf("order_id")]][$row[csf("buyer_po_id")]][$row[csf("color_id")]]["body_part"]=$rows[csf(body_part)];
	}
	//echo "<pre>";print_r($details_data_sub);die;
	
	?>
	<div style="width:1550px; margin:0 auto;">
		<fieldset style="width:100%;">	
		    <table width="98%" cellpadding="0" cellspacing="0" id="caption">
	            <tr>  
	                <td align="center" width="100%" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">Screen Print Delivery Report For Sub Contract </strong>
	                </td>
	            </tr>
				<tr>  
	                <td align="center" width="100%" colspan="11" class="form_caption" >
	                	<strong style="font-size:14px">&nbsp <? echo "Report Date is"." ".$txt_date_from." To ".$txt_date_to;?></strong>
	                </td>
	            </tr>
	        </table>
			<div style="width:1550px;" align="center">	
				<table width="1540"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" rules="all">
					<thead>
						<th style="word-wrap: break-word;word-break: break-all;" width="30" align="center">Sl </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">Party</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">WO No</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="120" align="center">Style</th>		
						<th style="word-wrap: break-word;word-break: break-all;" width="60" align="center"> Date </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="115" align="center">Del no </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="120" align="center">Color</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">Emb_type</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">Body_part</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="70" align="center">Del qty</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="65" align="center">S.qty</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="65" align="center">Rej print</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="65" align="center">Rej fab</th>
						<th style="word-wrap: break-word;word-break: break-all;" align="center">Remarks</th>					
					</thead>
				</table>
			</div>
			<div style="width:1550px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1540"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<?	
						$k=1;
						foreach($details_data as $company_id=>$company_data)
						{
							foreach($company_data as $party_id=>$party_data)
							{
								foreach($party_data as $wo_id=>$wo_data)
								{
									foreach($wo_data as $color_id=>$color_val)
									{
										foreach($color_val as $del_id=>$color_data)
										{
										if ($k%2==0)  
											$bgcolor="#e9f3ff";
											else
											$bgcolor="#ffffff";
						
										?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
												<td style="word-wrap: break-word;word-break: break-all;" width="30"> <? echo $k; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="100"> <? echo $buyerarr[$color_data[('party_id')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="100">  <? echo $wo_arr[$color_data[('order_id')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="120">  <? echo $party_style[$wo_id];//$color_data[('style_ref_no')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="60"> <? echo $color_data[('delivery_date')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="115">  <? echo $color_data[('delivery_no')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="120">  <? echo $colorarr[$color_data[('color_id')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85">  <? echo $emblishment_print_type[$color_data[('embellishment_type')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85" align="right">  <? echo $body_part_arr[$color_data[('body_part')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="70" align="right">  <? echo $color_data[('delivery_qty')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="65" align="right">  <? echo $color_data[('sort_qty')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="65" align="center">  <? echo $color_data[('print_reject_qty')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="65" align="center">  <? echo $color_data[('fabric_reject_qty')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" align="left">  <? echo $color_data[('remarks')]; ?></td>
												
											</tr>
											<?
											$k++;
											$total_del_qty+=$color_data[('delivery_qty')];
											$total_short_qty+=$color_data[('sort_qty')];
											$total_rej_print_qty+=$color_data[('print_reject_qty')];
											$total_rej_fab_qty+=$color_data[('fabric_reject_qty')];
;
										}
										?>
											<!--<tr bgcolor="#dddddd">   
												<td colspan="9"  align="right"><strong>Grand total :</strong></td>
												<td  align="right"> <strong><? //echo number_format($total_del_qty,2,'.',''); ?></strong></td>
												<td  align="right"> <strong> <? //echo number_format($total_short_qty,2,'.',''); ?></strong></td>
												<td  align="center"> <strong> <? //echo number_format($total_rej_print_qty,2,'.',''); ?></strong></td>
												<td  align="center"> <strong> <? //echo number_format($total_rej_fab_qty,2,'.',''); ?></strong></td>
												<td  > </td>			
											</tr>-->
										
										
										<?
									
									}
								}
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr bgcolor="#dddddd">   
							<td colspan="9"  align="right"><strong>Grand total :</strong></td>
							<td  align="right" id="val_total_del_qty"> <strong><? echo number_format($total_del_qty,2,'.',''); ?></strong></td>
							<td  align="right" id="val_total_short_qty"> <strong> <? echo number_format($total_short_qty,2,'.',''); ?></strong></td>
							<td  align="center"id="val_total_rej_qty"> <strong> <? echo number_format($total_rej_print_qty,2,'.',''); ?></strong></td>
							<td  align="center"id="val_total_fab_rec_qty"> <strong> <? echo number_format($total_rej_fab_qty,2,'.',''); ?></strong></td>
							<td  > </td>
													
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>	
		<?
				//--------------------------------------------------------End----------------------------------------
		?>
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
    echo "$html**$filename**$type"; 
    exit();
}
if($action=="report_generate_po")
{ 
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$job_no=str_replace("'","",$txt_job_no);
	
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$year_id=str_replace("'","",$cbo_year);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	
	$cbo_mainbuyer_id=str_replace("'","",$cbo_mainbuyer_id);//Buyer_ID
	
	if($year_id!=0) $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	if($cbo_buyer_id!=0) $buyer_id_cond=" AND DM.PARTY_ID ='$cbo_buyer_id'"; else $buyer_id_cond="";
	
	if($cbo_company_id!=0) $cbo_company_id_cond=" AND DM.COMPANY_ID ='$cbo_company_id'"; else $cbo_company_id_cond="";
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and DM.JOB_NO = '$job_no' ";
	//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
	if ($txt_style_ref!='') $style_ref_cond=" AND PDM.STYLE_REF_NO like '%$txt_style_ref%'"; else $style_ref_cond="";
	//if(trim($txt_order_no)!="") $order_no_cond="%".trim($txt_order_no)."%"; else $order_no_cond="%%";
	if ($txt_order_no!='') $order_no_cond=" and DD.BUYER_PO_ID like '%$txt_order_no%'"; else $order_no_cond="";
	
	if($cbo_mainbuyer_id!=0) $cbo_mainbuyer_id_cond=" AND PDM.BUYER_NAME ='$cbo_mainbuyer_id'"; else $cbo_mainbuyer_id_cond="";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and DM.DELIVERY_DATE  between $txt_date_from and $txt_date_to";
	//echo $txt_date_from;echo "AND to date ".$txt_date_to;die;
	//echo $cbo_mainbuyer_id_cond;die;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$gmt_item_Arr = return_library_array("select id,ITEM_NAME from  LIB_GARMENT_ITEM ","id","ITEM_NAME");
	$body_part_Arr = return_library_array("select id,BODY_PART_FULL_NAME from  LIB_BODY_PART ","id","BODY_PART_FULL_NAME");
	
	$buyer_po_Arr = return_library_array("select id,PO_NUMBER from WO_PO_BREAK_DOWN ","id","PO_NUMBER");
	$location_Arr = return_library_array("SELECT ID, LOCATION_NAME FROM LIB_LOCATION ","id","LOCATION_NAME");
	$Style_Arr = return_library_array("SELECT a.ID, b.STYLE_REF_NO FROM WO_PO_BREAK_DOWN a INNER JOIN WO_PO_DETAILS_MASTER b ON a.JOB_NO_MST = b.JOB_NO","id","STYLE_REF_NO");
	$buyer_po_qty_Arr = return_library_array("SELECT d.BUYER_PO_ID as ID, SUM(d.ORDER_QUANTITY) * 12 AS ORDER_QUANTITY FROM SUBCON_ORD_DTLS d WHERE d.STATUS_ACTIVE = 1 AND d.IS_DELETED = 0 GROUP BY d.BUYER_PO_ID","ID","ORDER_QUANTITY");
	//$buyer_arr = return_library_array("SELECT   a.ID,  c.BUYER_NAME FROM WO_PO_BREAK_DOWN a INNER JOIN WO_PO_DETAILS_MASTER b ON a.JOB_NO_MST = b.JOB_NO
//INNER JOIN LIB_BUYER c ON b.BUYER_NAME = c.ID ","id","BUYER_NAME");
	
	?>
    <div>
		<?/* echo "Test";
		select lib_company.company_name, lib_company1.company_name as party_name, lib_buyer.buyer_name, lib_location.location_name, lib_garment_item.item_name, lib_color.color_name, lib_size.size_name, wo_po_break_down.po_number, wo_po_details_master.style_ref_no, mst.company_id, mst.location_id, mst.party_id, mst.receive_date, mst.delivery_date, mst.job_no_prefix_num, mst.job_no_mst, mst.order_no, mst.po_delivery_date, mst.order_id, mst.item_id, mst.color_id, mst.size_id, mst.qnty, mst.rate, mst.amount, mst.buyer_po_id, mst.gmts_item_id, mst.embl_type, mst.body_part from (select som.company_id, som.location_id, som.party_id, som.receive_date, som.delivery_date, som.job_no_prefix_num, sod.job_no_mst, sod.order_no, sod.delivery_date as po_delivery_date, sob.order_id, sob.item_id, sob.color_id, sob.size_id, sob.qnty, sob.rate, sob.amount, sod.buyer_po_id, sod.gmts_item_id, sod.embl_type, sod.body_part from subcon_ord_mst som inner join subcon_ord_dtls sod on som.id = sod.mst_id inner join subcon_ord_breakdown sob on sod.id = sob.mst_id where som.company_id = 3 and som.job_no_prefix_num = 11 and som.status_active = 1 ) mst inner join lib_company on mst.company_id = lib_company.id inner join lib_location on lib_location.id = mst.location_id inner join lib_garment_item on mst.item_id = lib_garment_item.id inner join lib_color on mst.color_id = lib_color.id inner join lib_size on mst.size_id = lib_size.id inner join wo_po_break_down on mst.buyer_po_id = wo_po_break_down.id inner join wo_po_details_master on wo_po_break_down.job_no_mst = wo_po_details_master.job_no inner join lib_buyer on lib_buyer.id = wo_po_details_master.buyer_name inner join lib_company lib_company1 on lib_company1.id = mst.party_id 
		
		$style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond
		
		*/
		//--------------------------------------------------------Start----------------------------------------
	
			  
	$query_t = "SELECT SUM(CASE WHEN SMM.TRANS_TYPE = '1' THEN SMD.QUANTITY END) AS RECVD_QTY, SUM(CASE WHEN SMM.TRANS_TYPE = '2' THEN SMD.QUANTITY END)  AS ISSUE_QTY,
			  SMM.SYS_NO AS TRANS_NO,  SMM.COMPANY_ID,  SMM.TRANS_TYPE,  SMM.LOCATION_ID,  SMM.PARTY_ID,  SMM.SUBCON_DATE AS TRANS_DATE,  SMM.CHALAN_NO,  SMD.EMB_NAME_ID,
			  SMM.EMBL_JOB_NO,  SMD.BUYER_PO_ID,  SOB.COLOR_ID,  SOB.EMBELLISHMENT_TYPE AS EMB_TYPE,  SOB.BODY_PART,  P.PO_NUMBER,  PM.BUYER_NAME,  PM.STYLE_REF_NO
			FROM SUB_MATERIAL_MST SMM INNER JOIN SUB_MATERIAL_DTLS SMD ON SMD.MST_ID = SMM.ID INNER JOIN SUBCON_ORD_BREAKDOWN SOB ON SMD.JOB_BREAK_ID = SOB.ID
			INNER JOIN WO_PO_BREAK_DOWN P ON SMD.BUYER_PO_ID = P.ID INNER JOIN WO_PO_DETAILS_MASTER PM ON P.JOB_NO_MST = PM.JOB_NO
			WHERE SMD.STATUS_ACTIVE = 1
			AND SMM.STATUS_ACTIVE   = 1
			$cbo_company_id_cond $style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond
			GROUP BY SMM.SYS_NO,  SMM.COMPANY_ID,  SMM.TRANS_TYPE,  SMM.LOCATION_ID,  SMM.PARTY_ID,  SMM.SUBCON_DATE,  SMM.CHALAN_NO,  SMD.EMB_NAME_ID,  SMM.EMBL_JOB_NO,
			  SMD.BUYER_PO_ID,  SOB.COLOR_ID,  SOB.EMBELLISHMENT_TYPE,  SOB.BODY_PART,  SMD.JOB_DTLS_ID,  P.PO_NUMBER,  PM.BUYER_NAME,  PM.STYLE_REF_NO
			ORDER BY TRANS_DATE DESC, SMM.SYS_NO";
			  
	$query = "SELECT DM.COMPANY_ID,  DM.LOCATION_ID,  DM.DELIVERY_DATE,  DM.DELIVERY_NO,  DM.PARTY_ID,  DM.JOB_NO,  DD.COLOR_SIZE_ID,  DD.BUYER_PO_ID,  DD.SORT_QTY,
			  DM.REMARKS,  DM.DELI_PARTY,  DM.DELI_PARTY_LOCATION,  DD.DELIVERY_STATUS,  DD.DELIVERY_QTY,  PBD.PO_NUMBER,  PDM.BUYER_NAME,  PDM.STYLE_REF_NO,  PDM.STYLE_DESCRIPTION,
			  OB.COLOR_ID,  OB.ORDER_ID,OB.EMBELLISHMENT_TYPE, OB.BODY_PART
				FROM SUBCON_DELIVERY_MST DM INNER JOIN SUBCON_DELIVERY_DTLS DD ON DM.ID = DD.MST_ID INNER JOIN WO_PO_BREAK_DOWN PBD ON PBD.ID = DD.BUYER_PO_ID
				INNER JOIN WO_PO_DETAILS_MASTER PDM ON PBD.JOB_NO_MST = PDM.JOB_NO INNER JOIN SUBCON_ORD_BREAKDOWN OB ON DD.COLOR_SIZE_ID = OB.ID
				WHERE DM.STATUS_ACTIVE = 1
				AND DD.STATUS_ACTIVE = 1
				$cbo_company_id_cond $style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond ORDER By PDM.BUYER_NAME, DD.BUYER_PO_ID ";
	//echo $query;die;
	$sql_data_query = sql_select($query);
	$countRecords = count($query); 
	//echo $sql_data_query;
	ob_start();
	$details_data=array();

	foreach( $sql_data_query as $row)
	{
		//detail data in Array  
		
		//ORDER_ID, COLOR_ID, COMPANY_ID,  LOCATION_ID,  DELIVERY_DATE,  DELIVERY_NO,  PARTY_ID,  JOB_NO, COLOR_SIZE_ID, BUYER_PO_ID,  DD.SORT_QTY,
		///REMARKS,  DELI_PARTY,  DELI_PARTY_LOCATION,  DELIVERY_STATUS,  DELIVERY_QTY,  PO_NUMBER, BUYER_NAME,  STYLE_REF_NO
				
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["ORDER_ID"]=$row[csf(ORDER_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["COLOR_ID"]=$row[csf(COLOR_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["COMPANY_ID"]=$row[csf(COMPANY_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["LOCATION_ID"]=$row[csf(LOCATION_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["DELIVERY_DATE"]=$row[csf(DELIVERY_DATE)];				
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["DELIVERY_NO"]=$row[csf(DELIVERY_NO)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["JOB_NO"]=$row[csf(JOB_NO)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["COLOR_SIZE_ID"]=$row[csf(COLOR_SIZE_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["BUYER_PO_ID"]=$row[csf(BUYER_PO_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["PARTY_ID"]=$row[csf(PARTY_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["DELIVERY_STATUS"]=$row[csf(DELIVERY_STATUS)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["REMARKS"]=$row[csf(REMARKS)];		
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["DELI_PARTY"]=$row[csf(DELI_PARTY)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["BUYER_NAME"]=$row[csf(BUYER_NAME)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["DELI_PARTY_LOCATION"]=$row[csf(DELI_PARTY_LOCATION)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["PO_NUMBER"]=$row[csf(PO_NUMBER)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["DELIVERY_QTY"]+=$row[csf(DELIVERY_QTY)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["SORT_QTY"]+=$row[csf(SORT_QTY)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["STYLE_REF_NO"]=$row[csf(STYLE_REF_NO)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["EMBELLISHMENT_TYPE"]=$row[csf(EMBELLISHMENT_TYPE)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["BODY_PART"]=$row[csf(BODY_PART)];

		$sub_total_arr[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]+=$row[csf(DELIVERY_QTY)];
		$sub_short_arr[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]+=$row[csf(SORT_QTY)];
	}
	//echo "<pre>";print_r($details_data);die;	
	
	$query_sub ="SELECT DISTINCT OB.ID, OB.BODY_PART, OD.ORDER_ID,  OD.BUYER_PO_ID,  OB.COLOR_ID,  OB.EMBELLISHMENT_TYPE
FROM SUBCON_ORD_MST OM INNER JOIN SUBCON_ORD_DTLS OD ON OM.ID = OD.MST_ID INNER JOIN SUBCON_ORD_BREAKDOWN OB ON OD.JOB_NO_MST    = OB.JOB_NO_MST";
	//echo $query_sub;die;
	$sql_data_query_sub = sql_select($query_sub);
	$countRecords = count($query_sub); 
	//echo $sql_data_query_sub;
	$details_data_sub=array();
	foreach( $sql_data_query_sub as $rows)
	{
		$details_data_sub[$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["EMBELLISHMENT_TYPE"]=$rows[csf(EMBELLISHMENT_TYPE)];
		$details_data_sub[$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]]["BODY_PART"]=$rows[csf(BODY_PART)];
	}
	//echo "<pre>";print_r($details_data_sub);die;
	
	?>
	<div style="width:1450px; margin:0 auto;">
		<fieldset style="width:100%;">	
		    <table width="98%" cellpadding="0" cellspacing="0" id="caption">
	            <tr>  
	                <td align="center" width="100%" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">Screen Print Delivery Report </strong>
	                </td>
	            </tr>
				<tr>  
	                <td align="center" width="100%" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">&nbsp </strong>
	                </td>
	            </tr>
	        </table>
			<div style="width:1450px; align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1445"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" rules="all">
					<thead>
						<th style="word-wrap: break-word;word-break: break-all;" width="30" align="center">Sl </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">BUYER</th>
						
						<th style="word-wrap: break-word;word-break: break-all;" width="140" align="center">STYLE</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">PO No</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="right">PO Qty(Pcs)</th>						
						<th style="word-wrap: break-word;word-break: break-all;" width="170" align="center">PARTY </th>	
						<th style="word-wrap: break-word;word-break: break-all;" width="180" align="center">COLOR</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="105" align="center">EMB_TYPE</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="105" align="center">BODY_PART</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="90" align="center">Del QTY(Pcs)</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="75" align="center">S.QTY(Pcs)</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="95" align="center">Balance(Pcs)</th>
						<th style="word-wrap: break-word;word-break: break-all;" align="center">Remarks</th>					
					</thead>
				</table>
			</div>
			<div style="width:1450px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1440"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<?	
						//ORDER_ID, COLOR_ID, COMPANY_ID,  LOCATION_ID,  DELIVERY_DATE,  DELIVERY_NO,  PARTY_ID,  JOB_NO, COLOR_SIZE_ID, BUYER_PO_ID,  DD.SORT_QTY,
						//REMARKS,  DELI_PARTY,  DELI_PARTY_LOCATION,  DELIVERY_STATUS,  DELIVERY_QTY,  PO_NUMBER, BUYER_NAME,  STYLE_REF_NO
						$k=1;
						foreach($details_data as $company_id=>$company_data)
						{
							foreach($company_data as $party_id=>$party_data)
							{
								foreach($party_data as $wo_id=>$wo_data)
								{
									foreach($wo_data as $po_id=>$po_data)
									{
										foreach($po_data as $color_id=>$color_data)
										{
											
											if ($k%2==0)  
												$bgcolor="#E9F3FF";
												else
												$bgcolor="#FFFFFF";
											//ORDER_ID, COLOR_ID, COMPANY_ID,  LOCATION_ID,  DELIVERY_DATE,  DELIVERY_NO,  PARTY_ID,  JOB_NO, COLOR_SIZE_ID, BUYER_PO_ID,  DD.SORT_QTY,
							//REMARKS,  DELI_PARTY,  DELI_PARTY_LOCATION,  DELIVERY_STATUS,  DELIVERY_QTY,  PO_NUMBER, BUYER_NAME,  STYLE_REF_NO
							
											?>
												<tr valign="center" height="30" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
													<td style="word-wrap: break-word;word-break: break-all;" width="30"> <? echo $k; ?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="100"> <? echo $buyerArr[$color_data[('BUYER_NAME')]]; ?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="140">  <? echo $color_data[('STYLE_REF_NO')]; ?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="100">  <? echo $buyer_po_Arr[$color_data[('BUYER_PO_ID')]]; ?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="100">  <? echo $buyer_po_qty_Arr[$color_data[('BUYER_PO_ID')]]; ?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="170">  <? echo $companyArr[$color_data[('PARTY_ID')]]; ?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="180">  <? echo $colorArr[$color_data[('COLOR_ID')]]; ?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="105">  <? echo $emblishment_print_type[$color_data[('EMBELLISHMENT_TYPE')]]; ?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="105" align="right">  <? echo $body_part_Arr[$color_data[('BODY_PART')]]; ?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="90" align="right">  <? echo $color_data[('DELIVERY_QTY')]; ?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="75" align="right">  <? echo $color_data[('SORT_QTY')]; ?></td>
													<td style="word-wrap: break-word;word-break: break-all;" width="95" align="right">  <? echo $buyer_po_qty_Arr[$color_data[('BUYER_PO_ID')]]-($color_data[('DELIVERY_QTY')]+$color_data[('SORT_QTY')]); ?></td>
													<td style="word-wrap: break-word;word-break: break-all;" align="right">  <? echo $color_data[('REMARKS')]; ?></td>
													
												</tr>
												<?
												$k++;
												$total_po_qty+=$buyer_po_qty_Arr[$color_data[('BUYER_PO_ID')]];
												$total_del_qty+=$color_data[('DELIVERY_QTY')];
												$total_short_qty+=$color_data[('SORT_QTY')];
												$total_po_qty_balance+=$buyer_po_qty_Arr[$color_data[('BUYER_PO_ID')]]-($color_data[('DELIVERY_QTY')]+$color_data[('SORT_QTY')]);
	;
											
										}
									}
								}
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr bgcolor="#dddddd">   
							<td colspan="4"  align="right"><strong></strong></td>
							
							<td  align="right"  id="value_total_po_qty"><strong><? echo number_format($total_po_qty,2,'.',''); ?></strong></strong></td>
							<td colspan="4"  align="right"><strong>Grand Total :</strong></td>
							<td  align="right"  id="value_total_del_qty"><strong><? echo number_format($total_del_qty,2,'.',''); ?></strong></td>
							<td  align="right"  id="value_total_short_qty"><strong> <? echo number_format($total_short_qty,2,'.',''); ?></strong></td>
							<td  align="right"  id="value_total_po_balance_qty"><strong> <? echo number_format($total_po_qty_balance,2,'.',''); ?></strong></td>
							<td  align="right"> </td>
													
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>	
		<?
				//--------------------------------------------------------End----------------------------------------
		?>
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

if($action=="report_generate_buyer")
{ 
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$job_no=str_replace("'","",$txt_job_no);
	
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$year_id=str_replace("'","",$cbo_year);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	
	$cbo_mainbuyer_id=str_replace("'","",$cbo_mainbuyer_id);//Buyer_ID
	
	if($year_id!=0) $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	if($cbo_buyer_id!=0) $buyer_id_cond=" AND DM.PARTY_ID ='$cbo_buyer_id'"; else $buyer_id_cond="";
	
	if($cbo_company_id!=0) $cbo_company_id_cond=" AND DM.COMPANY_ID ='$cbo_company_id'"; else $cbo_company_id_cond="";
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and DM.JOB_NO = '$job_no' ";
	//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
	if ($txt_style_ref!='') $style_ref_cond=" AND PDM.STYLE_REF_NO like '%$txt_style_ref%'"; else $style_ref_cond="";
	//if(trim($txt_order_no)!="") $order_no_cond="%".trim($txt_order_no)."%"; else $order_no_cond="%%";
	if ($txt_order_no!='') $order_no_cond=" and DD.BUYER_PO_ID like '%$txt_order_no%'"; else $order_no_cond="";
	
	if($cbo_mainbuyer_id!=0) $cbo_mainbuyer_id_cond=" AND PDM.BUYER_NAME ='$cbo_mainbuyer_id'"; else $cbo_mainbuyer_id_cond="";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and DM.DELIVERY_DATE  between $txt_date_from and $txt_date_to";
	//echo $txt_date_from;echo "AND to date ".$txt_date_to;die;
	//echo $cbo_mainbuyer_id_cond;die;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$gmt_item_Arr = return_library_array("select id,ITEM_NAME from  LIB_GARMENT_ITEM ","id","ITEM_NAME");
	$body_part_Arr = return_library_array("select id,BODY_PART_FULL_NAME from  LIB_BODY_PART ","id","BODY_PART_FULL_NAME");
	
	$buyer_po_Arr = return_library_array("select id,PO_NUMBER from WO_PO_BREAK_DOWN ","id","PO_NUMBER");
	$location_Arr = return_library_array("SELECT ID, LOCATION_NAME FROM LIB_LOCATION ","id","LOCATION_NAME");
	$Style_Arr = return_library_array("SELECT a.ID, b.STYLE_REF_NO FROM WO_PO_BREAK_DOWN a INNER JOIN WO_PO_DETAILS_MASTER b ON a.JOB_NO_MST = b.JOB_NO","id","STYLE_REF_NO");
	$ord_qty = return_library_array("SELECT d.BUYER_PO_ID as ID, SUM(d.ORDER_QUANTITY) * 12 AS ORDER_QUANTITY FROM SUBCON_ORD_DTLS d WHERE d.STATUS_ACTIVE = 1 AND d.IS_DELETED = 0 GROUP BY d.BUYER_PO_ID","ID","ORDER_QUANTITY");
	//$buyer_arr = return_library_array("SELECT   a.ID,  c.BUYER_NAME FROM WO_PO_BREAK_DOWN a INNER JOIN WO_PO_DETAILS_MASTER b ON a.JOB_NO_MST = b.JOB_NO
//INNER JOIN LIB_BUYER c ON b.BUYER_NAME = c.ID ","id","BUYER_NAME");
	
	?>
    <div>
		<?
		//--------------------------------------------------------Start----------------------------------------
			  
	$query = "SELECT DM.COMPANY_ID,  DM.LOCATION_ID,  DM.DELIVERY_DATE,  DM.DELIVERY_NO,  DM.PARTY_ID,  DM.JOB_NO,  DD.COLOR_SIZE_ID,  DD.BUYER_PO_ID,  DD.SORT_QTY,
			  DM.REMARKS,  DM.DELI_PARTY,  DM.DELI_PARTY_LOCATION,  DD.DELIVERY_STATUS,  DD.DELIVERY_QTY,  PBD.PO_NUMBER,  PDM.BUYER_NAME,  PDM.STYLE_REF_NO,  PDM.STYLE_DESCRIPTION,
			  OB.COLOR_ID,  OB.ORDER_ID,OB.EMBELLISHMENT_TYPE, OB.BODY_PART
				FROM SUBCON_DELIVERY_MST DM INNER JOIN SUBCON_DELIVERY_DTLS DD ON DM.ID = DD.MST_ID INNER JOIN WO_PO_BREAK_DOWN PBD ON PBD.ID = DD.BUYER_PO_ID
				INNER JOIN WO_PO_DETAILS_MASTER PDM ON PBD.JOB_NO_MST = PDM.JOB_NO INNER JOIN SUBCON_ORD_BREAKDOWN OB ON DD.COLOR_SIZE_ID = OB.ID
				WHERE DM.STATUS_ACTIVE = 1
				AND DD.STATUS_ACTIVE = 1
				$cbo_company_id_cond $style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond ORDER By PDM.BUYER_NAME, DD.BUYER_PO_ID ";
	//echo $query;die;
	$sql_data_query = sql_select($query);
	$countRecords = count($query); 
	//echo $sql_data_query;
	ob_start();
	$details_data=array();
	$delivery_data=array();

	foreach( $sql_data_query as $row)
	{
		//detail data in Array  
		
		//ORDER_ID, COLOR_ID, COMPANY_ID,  LOCATION_ID,  DELIVERY_DATE,  DELIVERY_NO,  PARTY_ID,  JOB_NO, COLOR_SIZE_ID, BUYER_PO_ID,  SORT_QTY,
		///REMARKS,  DELI_PARTY,  DELI_PARTY_LOCATION,  DELIVERY_STATUS,  DELIVERY_QTY,  PO_NUMBER, BUYER_NAME,  STYLE_REF_NO
		
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["DELI_PARTY"]=$row[csf(DELI_PARTY)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["DELI_PARTY_LOCATION"]=$row[csf(DELI_PARTY_LOCATION)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["ORDER_ID"]=$row[csf(ORDER_ID)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["BUYER_PO_ID"]=$row[csf(BUYER_PO_ID)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["COLOR_ID"]=$row[csf(COLOR_ID)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["BUYER_NAME"]=$row[csf(BUYER_NAME)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["PO_NUMBER"]=$row[csf(PO_NUMBER)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["STYLE_REF_NO"]=$row[csf(STYLE_REF_NO)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["DELIVERY_QTY"]+=$row[csf(DELIVERY_QTY)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["SORT_QTY"]+=$row[csf(SORT_QTY)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["PARTY_ID"]=$row[csf(PARTY_ID)];


		$sub_total_arr[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]]+=$row[csf(DELIVERY_QTY)];
		$sub_short_arr[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]]+=$row[csf(SORT_QTY)];
		$grand_total_arr[$row[csf("BUYER_NAME")]]+=$row[csf(DELIVERY_QTY)];
		$grand_short_arr[$row[csf("BUYER_NAME")]]+=$row[csf(SORT_QTY)];
	}
	//echo "<pre>";print_r($details_data);die;	
	
	?>
	<div style="width:1150px; margin:0 auto;">
		<fieldset style="width:100%;">	
		    <table width="98%" cellpadding="0" cellspacing="0" id="caption">
	            <tr>  
	                <td align="center" width="100%" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">Screen Print Delivery Report </strong>
	                </td>
	            </tr>
				<tr>  
	                <td align="center" width="100%" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">&nbsp </strong>
	                </td>
	            </tr>
	        </table>
			<div style="width:1150px; align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1140"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" rules="all">
					<thead>
						<th style="word-wrap: break-word;word-break: break-all;" width="30" align="center">Sl </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">BUYER</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">PO No</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="200" align="center">STYLE</th>		
						<th style="word-wrap: break-word;word-break: break-all;" width="115" align="center">Delivery Party </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="150" align="center">Del Place</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="150" align="center">PARTY </th>		
						<th style="word-wrap: break-word;word-break: break-all;" width="120" align="center">COLOR</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="70" align="center">Del QTY</th>
						<th style="word-wrap: break-word;word-break: break-all;"  align="center">S.QTY</th>
							
					</thead>
				</table>
			</div>
			<div style="width:1150px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1140"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<?	
						//ORDER_ID, COLOR_ID, COMPANY_ID,  LOCATION_ID,  DELIVERY_DATE,  DELIVERY_NO,  PARTY_ID,  JOB_NO, COLOR_SIZE_ID, BUYER_PO_ID,  DD.SORT_QTY,
						//REMARKS,  DELI_PARTY,  DELI_PARTY_LOCATION,  DELIVERY_STATUS,  DELIVERY_QTY,  PO_NUMBER, BUYER_NAME,  STYLE_REF_NO
						$k=1;
						foreach($delivery_data as $buyer_id=>$buyer_data)
						{
							foreach($buyer_data as $del_party_id=>$del_party_data)
							{
								foreach($del_party_data as $del_party_location_id=>$del_party_location_data)
								{
									foreach($del_party_location_data as $wo_id=>$wo_data)
									{
										foreach($wo_data as $po_id=>$po_data)
										{
											foreach($po_data as $color_id=>$color_data)
											{
												if ($k%2==0)  
													$bgcolor="#E9F3FF";
													else
													$bgcolor="#FFFFFF";
												?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
														<td style="word-wrap: break-word;word-break: break-all;" width="30"> <? echo $k; ?></td>
														<td style="word-wrap: break-word;word-break: break-all;" width="100"> <? echo $buyerArr[$color_data[('BUYER_NAME')]]; ?></td>
														<td style="word-wrap: break-word;word-break: break-all;" width="100">  <? echo $buyer_po_Arr[$color_data[('BUYER_PO_ID')]]; ?></td>
														<td style="word-wrap: break-word;word-break: break-all;" width="200">  <? echo $color_data[('STYLE_REF_NO')]; ?></td>
														<td style="word-wrap: break-word;word-break: break-all;" width="115">  <? echo $companyArr[$color_data[('DELI_PARTY')]]; ?></td>
														<td style="word-wrap: break-word;word-break: break-all;" width="150"> <? echo $location_Arr[$color_data[('DELI_PARTY_LOCATION')]]; ?></td>
														<td style="word-wrap: break-word;word-break: break-all;" width="150">  <? echo $companyArr[$color_data[('PARTY_ID')]]; ?></td>
						
														<td style="word-wrap: break-word;word-break: break-all;" width="120">  <? echo $colorArr[$color_data[('COLOR_ID')]]; ?></td>
														<td style="word-wrap: break-word;word-break: break-all;" width="70" align="right">  <? echo $color_data[('DELIVERY_QTY')]; ?></td>
														<td style="word-wrap: break-word;word-break: break-all;"  align="right">  <? echo $color_data[('SORT_QTY')]; ?></td>
														
													</tr>
													<?
													$k++;
													$value_total_del_buyer+=$color_data[('DELIVERY_QTY')];
													$value_total_short_buyer+=$color_data[('SORT_QTY')];
		;
											}
										}
									}
								}
									?>
											<tr bgcolor="#dddddd">   
												<td align="right" colspan="8" style="word-wrap: break-word;word-break: break-all;" width="30"> <strong><? echo  $companyArr[$color_data[('DELI_PARTY')]]; ?> Total :</strong></td>	
												<td align="right" style="word-wrap: break-word;word-break: break-all;"> <strong>
												<?
													echo $sub_total_arr[$color_data[('BUYER_NAME')]][$color_data[('DELI_PARTY')]]; 
												?></strong></td>
												<td align="right" style="word-wrap: break-word;word-break: break-all;"><strong>
												<? 
													echo $sub_short_arr[$color_data[('BUYER_NAME')]][$color_data[('DELI_PARTY')]]; 
												?></strong>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;"></td>
											</tr>
											<?	
							}
							?>
											<tr bgcolor="#dddddd">   
												<td align="right" colspan="8" style="word-wrap: break-word;word-break: break-all;" width="30"> <strong><? echo $buyerArr[$color_data[('BUYER_NAME')]]; ?> Total :</strong></td>	
												<td align="right" style="word-wrap: break-word;word-break: break-all;"> <strong>
												<?
													echo $grand_total_arr[$color_data[('BUYER_NAME')]]; 
												?></strong></td>
												<td align="right" style="word-wrap: break-word;word-break: break-all;"><strong>
												<? 
													echo $grand_short_arr[$color_data[('BUYER_NAME')]]; 
												?></strong>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;"></td>
											</tr>
											<?
						
							
						}
						?>
					</tbody>
					<tfoot>
						<tr bgcolor="#dddddd">   
							<td align="right" colspan="8" style="word-wrap: break-word;word-break: break-all;" width="30"> <strong>Grand Total :</strong></td>	
							<td style="word-wrap: break-word;word-break: break-all;" align="right" width="70" id="value_total_del_qty_buyer"> <strong><? echo number_format($value_total_del_buyer,0,'.',''); ?> </strong></td>
							<td style="word-wrap: break-word;word-break: break-all;" align="right" id="value_total_short_qty_buyer"> <strong><? echo number_format($value_total_short_qty_buyer,0,'.',''); ?> </strong></td>						
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>	
		<?
				//--------------------------------------------------------End----------------------------------------
		?>
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

if($action=="report_generate_statement")
{ 
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$job_no=str_replace("'","",$txt_job_no);
	
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_wo_no=str_replace("'","",$txt_wo_no);
	$year_id=str_replace("'","",$cbo_year);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	
	$cbo_mainbuyer_id=str_replace("'","",$cbo_mainbuyer_id);//Buyer_ID
	
	if($year_id!=0) $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	if($cbo_buyer_id!=0) $buyer_id_cond=" AND DM.PARTY_ID ='$cbo_buyer_id'"; else $buyer_id_cond="";
	if($cbo_buyer_id!=0) $buyer_id_cond_ord=" AND a.PARTY_ID ='$cbo_buyer_id'"; else $buyer_id_cond_ord="";
	
	if($cbo_company_id!=0) $cbo_company_id_cond=" AND DM.COMPANY_ID ='$cbo_company_id'"; else $cbo_company_id_cond="";
		if($cbo_company_id!=0) $cbo_company_id_cond_ord=" AND a.COMPANY_ID ='$cbo_company_id'"; else $cbo_company_id_cond_ord="";
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and DM.JOB_NO = '$job_no' ";
	//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
	if ($txt_style_ref!='') $style_ref_cond=" AND PDM.STYLE_REF_NO like '%$txt_style_ref%'"; else $style_ref_cond="";
	if ($txt_style_ref!='') $style_ref_cond_ord=" AND b.BUYER_STYLE_REF like '%$txt_style_ref%'"; else $style_ref_cond_ord="";
	if(trim($txt_wo_no)!="") $wo_no_cond=" AND c.ORDER_NO LIKE '%$txt_wo_no%' "; else $wo_no_cond=" ";
	if(trim($txt_wo_no)!="") $wo_no_cond_ord=" AND b.ORDER_NO LIKE '%$txt_wo_no%' "; else $wo_no_cond_ord=" ";
	if ($txt_order_no!='') $order_no_cond=" and DD.BUYER_PO_ID like '%$txt_order_no%'"; else $order_no_cond="";
	
	if($cbo_mainbuyer_id!=0) $cbo_mainbuyer_id_cond=" AND PDM.BUYER_NAME ='$cbo_mainbuyer_id'"; else $cbo_mainbuyer_id_cond="";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and DM.DELIVERY_DATE  between $txt_date_from and $txt_date_to";
	
	//echo $txt_date_from;echo "AND to date ".$txt_date_to;die;
	//echo $cbo_mainbuyer_id_cond;die;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$gmt_item_Arr = return_library_array("select id,ITEM_NAME from  LIB_GARMENT_ITEM ","id","ITEM_NAME");
	$body_part_Arr = return_library_array("select id,BODY_PART_FULL_NAME from  LIB_BODY_PART ","id","BODY_PART_FULL_NAME");
	
	//$wo_no=return_library_array( "select distinct  order_id, order_no from subcon_ord_mst",'order_id','order_no');
	$buyer_po_Arr = return_library_array("select id,PO_NUMBER from WO_PO_BREAK_DOWN ","id","PO_NUMBER");
	$location_Arr = return_library_array("SELECT ID, LOCATION_NAME FROM LIB_LOCATION ","id","LOCATION_NAME");
	$Style_Arr = return_library_array("SELECT a.ID, b.STYLE_REF_NO FROM WO_PO_BREAK_DOWN a INNER JOIN WO_PO_DETAILS_MASTER b ON a.JOB_NO_MST = b.JOB_NO","id","STYLE_REF_NO");
	$ord_qty = return_library_array("SELECT d.BUYER_PO_ID as ID, SUM(d.ORDER_QUANTITY) * 12 AS ORDER_QUANTITY FROM SUBCON_ORD_DTLS d WHERE d.STATUS_ACTIVE = 1 AND d.IS_DELETED = 0 GROUP BY d.BUYER_PO_ID","ID","ORDER_QUANTITY");
	//$buyer_arr = return_library_array("SELECT   a.ID,  c.BUYER_NAME FROM WO_PO_BREAK_DOWN a INNER JOIN WO_PO_DETAILS_MASTER b ON a.JOB_NO_MST = b.JOB_NO
//INNER JOIN LIB_BUYER c ON b.BUYER_NAME = c.ID ","id","BUYER_NAME");
	
	?>
    <div>
		<?
		//--------------------------------------------------------Start----------------------------------------
			  
	$query = "SELECT DM.COMPANY_ID,  DM.LOCATION_ID,  DM.DELIVERY_DATE,  DM.DELIVERY_NO,  DM.PARTY_ID,  DM.JOB_NO,  DD.COLOR_SIZE_ID,  DD.BUYER_PO_ID,  DD.SORT_QTY,  DM.REMARKS,
				DM.DELI_PARTY,  DM.DELI_PARTY_LOCATION,  DD.DELIVERY_STATUS,  DD.DELIVERY_QTY,  PBD.PO_NUMBER,  PDM.BUYER_NAME,  PDM.STYLE_REF_NO,  PDM.STYLE_DESCRIPTION,
				OB.COLOR_ID,  OB.EMBELLISHMENT_TYPE,  OB.BODY_PART,  c.ORDER_NO
				FROM SUBCON_DELIVERY_MST DM
				INNER JOIN SUBCON_DELIVERY_DTLS DD
				ON DM.ID = DD.MST_ID
				INNER JOIN WO_PO_BREAK_DOWN PBD
				ON PBD.ID = DD.BUYER_PO_ID
				INNER JOIN WO_PO_DETAILS_MASTER PDM
				ON PBD.JOB_NO_MST = PDM.JOB_NO
				INNER JOIN SUBCON_ORD_BREAKDOWN OB
				ON DD.COLOR_SIZE_ID = OB.ID
				INNER JOIN SUBCON_ORD_DTLS c
				ON OB.MST_ID        = c.ID
			  WHERE DM.STATUS_ACTIVE = 1
			  AND DD.STATUS_ACTIVE = 1
			  $cbo_company_id_cond $style_ref_cond  $wo_no_cond $job_no_cond $date_cond $buyer_id_cond $order_no_cond $cbo_mainbuyer_id_cond ORDER By PDM.BUYER_NAME, DD.BUYER_PO_ID ";
	//echo $query;die;
	$sql_data_query = sql_select($query);
	$countRecords = count($query); 
	//echo $sql_data_query; die;
	ob_start();
	$details_data=array();
	$delivery_data=array();
	
	$sql_order = "SELECT a.PARTY_ID, b.JOB_NO_MST,  b.ORDER_NO,  c.ORDER_ID, b.BUYER_STYLE_REF,  SUM(c.QNTY)   AS QNTY,  AVG(c.RATE)   AS RATE,  SUM(c.AMOUNT) AS AMOUNT,  a.COMPANY_ID,  a.RECEIVE_DATE
					FROM subcon_ord_dtls b
					LEFT JOIN subcon_ord_breakdown c
					ON b.ID = c.MST_ID
					INNER JOIN SUBCON_ORD_MST a
					ON a.ID = b.MST_ID
					WHERE c.STATUS_ACTIVE = 1 
					$cbo_company_id_cond_ord $style_ref_cond_ord $wo_no_cond_ord $buyer_id_cond_ord
					GROUP BY a.PARTY_ID, b.JOB_NO_MST,   b.ORDER_NO, b.BUYER_STYLE_REF,  c.ORDER_ID,  a.COMPANY_ID,  a.RECEIVE_DATE";
		
	//echo $sql_order;
	$sql_data_select = sql_select($sql_order);
	$countRecords_ord = count($sql_order);
	if($countRecords_ord)
	{
		foreach( $sql_data_select as $row)
		{
			$order_data['COMPANY_ID']=$row[csf(COMPANY_ID)];
			$order_data['JOB_NO_MST'].=$row[csf(JOB_NO_MST)]." , ";
			$order_data['WO'].=$row[csf(ORDER_NO)]." , ";
			$order_data['RECEIVE_DATE'].=$row[csf(RECEIVE_DATE)]." , ";
			$order_data['RATE'].=$row[csf(RATE)]." (".$row[csf(ORDER_NO)].")"." , ";
			$order_data['QNTY'].=$row[csf(QNTY)]." (".$row[csf(ORDER_NO)].")"." , ";
			$order_data['BUYER_STYLE_REF']=$row[csf(BUYER_STYLE_REF)];
			$order_data['PARTY_ID']=$companyArr[$row[csf(PARTY_ID)]];
			
			//break;
		}
		
		//if($countRecords_ord==1){break;}
	}

	foreach( $sql_data_query as $row)
	{		
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELIVERY_NO")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["ORDER_NO"]=$row[csf(ORDER_NO)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELIVERY_NO")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["DELI_PARTY"]=$row[csf(DELI_PARTY)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELIVERY_NO")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["DELIVERY_NO"]=$row[csf(DELIVERY_NO)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELIVERY_NO")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["DELI_PARTY_LOCATION"]=$row[csf(DELI_PARTY_LOCATION)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELIVERY_NO")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["ORDER_ID"]=$row[csf(ORDER_ID)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELIVERY_NO")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["BUYER_PO_ID"]=$row[csf(BUYER_PO_ID)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELIVERY_NO")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["COLOR_ID"]=$row[csf(COLOR_ID)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELIVERY_NO")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["BUYER_NAME"]=$row[csf(BUYER_NAME)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELIVERY_NO")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["PO_NUMBER"]=$row[csf(PO_NUMBER)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELIVERY_NO")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["STYLE_REF_NO"]=$row[csf(STYLE_REF_NO)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELIVERY_NO")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["DELIVERY_QTY"]+=$row[csf(DELIVERY_QTY)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELIVERY_NO")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["SORT_QTY"]+=$row[csf(SORT_QTY)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELIVERY_NO")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["PARTY_ID"]=$row[csf(PARTY_ID)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELIVERY_NO")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("ORDER_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]] ["DELIVERY_DATE"]=$row[csf(DELIVERY_DATE)];

		$sub_total_arr[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]]+=$row[csf(DELIVERY_QTY)];
		$sub_short_arr[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]]+=$row[csf(SORT_QTY)];
		$grand_total_arr[$row[csf("BUYER_NAME")]]+=$row[csf(DELIVERY_QTY)];
		$grand_short_arr[$row[csf("BUYER_NAME")]]+=$row[csf(SORT_QTY)];
		
		$STYLE_REF_NO=$row[csf(STYLE_REF_NO)];
		$BUYER_NAME=$row[csf(BUYER_NAME)];
		$PARTY_ID=$row[csf(PARTY_ID)];
		$WO_ORDER_ID=$row[csf(ORDER_ID)];
		$DELIVERY_QTY+=$row[csf(DELIVERY_QTY)];
	}
	//echo "<pre>";print_r($order_data);die;
$value_width =1450;	
	
	?>
	<div align="center" style="width:<? echo $value_width + 33; ?>px;">
            
		<fieldset  style="width:<? echo $value_width + 28; ?>px;" > 
			<br>
		    <table align = "center" width="<? echo $value_width+20; ?>">
	            <tr>  
	                <td align="center" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">Screen Print Delivery Statement </strong>
	                </td>
	            </tr>
	        </table>
			<br>
			<div align = "center" style="width:<? echo $value_width + 33; ?>px;">
				<table align = "center" width="<? echo $value_width+20; ?>">
					<tr>
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left">Work Order Name : </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left"> <? echo $order_data['WO'];?></td>

						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left"> </td>
						
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left">Buyer Name : </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left"> <? echo $buyerArr[$BUYER_NAME];?></td>
					</tr>
					<tr>
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left">Style Name: </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left"> <? echo $order_data['BUYER_STYLE_REF'];?></td>

						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left"> </td>
						
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left">Work Order Qty : </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left"><? echo $order_data['QNTY'];?> </td>		
					</tr>
					<tr>
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left">Avg Rate : </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left"><? echo $order_data['RATE'];?> </td>

						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left"> </td>
						
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left">Rcvd Date  : </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left"><? echo $order_data['RECEIVE_DATE'];?> </td>	
					</tr>
					<tr>
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left">Job No : </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left"><? echo $order_data['JOB_NO_MST'];?> </td>

						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left"> </td>
						
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left">Party Name : </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="30" align="left"> <? echo $order_data['PARTY_ID'];?></td>	
					</tr>
				</table>
			</div>
			<br>
			<div align = "center" style="width:<? echo $value_width + 28; ?>px;">	
				<table align = "center"  width="<? echo $value_width+20; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" rules="all">
					<thead>
						<th  width="30" align="center">Sl </th>
						<th  width="80" align="center">Del Date</th>	
						<th  width="100" align="center">BUYER</th>
						<th  width="170" align="center">WO No </th>	
						<th  width="100" align="center">PO No</th>
						<th  width="200" align="center">STYLE</th>
						<th  width="100" align="center">Del No</th>						
						<th  width="115" align="center">Delivery Party </th>
						<th  width="150" align="center">Del Place</th>
							
						<th  width="120" align="center">COLOR</th>
						<th  width="70" align="center">Del QTY(Pcs)</th>
						<th  width="70" align="center">Del QTY(Dzn)</th>
						<th   align="center">S.QTY</th>
							
					</thead>
				</table>
			</div>
			<div align="center" style="width:<? echo $value_width + 28; ?>px; max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table align = "center" width="<? echo $value_width; ?>"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<?	
						$k=1;
						foreach($delivery_data as $buyer_id=>$buyer_data)
						{
							foreach($buyer_data as $del_no=>$del_no_data)
							{
								foreach($del_no_data as $del_party_id=>$del_party_data)
								{
									foreach($del_party_data as $del_party_location_id=>$del_party_location_data)
									{
										foreach($del_party_location_data as $wo_id=>$wo_data)
										{
											foreach($wo_data as $po_id=>$po_data)
											{
												foreach($po_data as $color_id=>$color_data)
												{
													if ($k%2==0)  
														$bgcolor="#E9F3FF";
														else
														$bgcolor="#FFFFFF";
													?>
														<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
															<td  width="30"> <? echo $k; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="80"> <? echo $color_data[('DELIVERY_DATE')]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="100"> <? echo $buyerArr[$color_data[('BUYER_NAME')]]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="170">  <? echo $color_data[('ORDER_NO')]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="100">  <? echo $buyer_po_Arr[$color_data[('BUYER_PO_ID')]]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="200">  <? echo $color_data[('STYLE_REF_NO')]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="115">  <? echo $color_data[('DELIVERY_NO')]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="115">  <? echo $companyArr[$color_data[('DELI_PARTY')]]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="150"> <? echo  $location_Arr[$color_data[('DELI_PARTY_LOCATION')]]; ?></td>
															
							
															<td style="word-wrap: break-word;word-break: break-all;" width="120">  <? echo $colorArr[$color_data[('COLOR_ID')]]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="70" align="right">  <? echo $color_data[('DELIVERY_QTY')]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="70" align="right">  <? echo $color_data[('DELIVERY_QTY')]/12; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" align="center">  <? echo $color_data[('SORT_QTY')]; ?></td>															
														</tr>
														<?
														$k++;
														$value_total_del_buyer+=$color_data[('DELIVERY_QTY')];
														$value_total_del_dzn_buyer+=$color_data[('DELIVERY_QTY')]/12;
														$value_total_short_buyer+=$color_data[('SORT_QTY')];
												}
											}
										}
									}
								}
							}								
						}
						?>
					</tbody>
					<tfoot>
						<tr bgcolor="#dddddd">   
							<td align="right" colspan="10"> <strong>Grand Total :</strong></td>	
							<td align="right"> <strong><? echo number_format($value_total_del_buyer,0,'.',''); ?> </strong></td>
							<td align="right"> <strong><? echo number_format($value_total_del_dzn_buyer,2,'.',''); ?> </strong></td>
							<td align="center"> <strong><? echo number_format($value_total_short_buyer,2,'.',''); ?> </strong></td>						
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>	
		<?
				//--------------------------------------------------------End----------------------------------------
		?>
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

if($action=="report_generate_st")
{ 
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$job_no=str_replace("'","",$txt_job_no);
	
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_wo_no=str_replace("'","",$txt_wo_no);
	$year_id=str_replace("'","",$cbo_year);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$within_group=str_replace("'","",$cbo_within_group);
	
	$cbo_mainbuyer_id=str_replace("'","",$cbo_mainbuyer_id);//buyer_id
	
	if($year_id!=0) $year_cond=" and to_char(a.insert_date,'yyyy')=$year_id"; else $year_cond="";
	if($cbo_buyer_id!=0) $buyer_id_cond=" and dm.party_id ='$cbo_buyer_id'"; else $buyer_id_cond="";
	if($cbo_buyer_id!=0) $buyer_id_cond_ord=" and a.party_id ='$cbo_buyer_id'"; else $buyer_id_cond_ord="";
	
	if($cbo_company_id!=0) $cbo_company_id_cond=" and dm.company_id ='$cbo_company_id'"; else $cbo_company_id_cond="";
	if($cbo_company_id!=0) $cbo_company_id_cond_ord=" and a.company_id ='$cbo_company_id'"; else $cbo_company_id_cond_ord="";
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and dm.job_no = '$job_no' ";
	//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
	if ($txt_style_ref!='') $style_ref_cond=" and pdm.style_ref_no like '%$txt_style_ref%'"; else $style_ref_cond="";
	if ($txt_style_ref!='') $style_ref_cond_ord=" and b.buyer_style_ref like '%$txt_style_ref%'"; else $style_ref_cond_ord="";
	if(trim($txt_wo_no)!="") $wo_no_cond=" and c.order_no like '%$txt_wo_no%' "; else $wo_no_cond=" ";
	if(trim($txt_wo_no)!="") $wo_no_cond_ord=" and b.order_no like '%$txt_wo_no%' "; else $wo_no_cond_ord=" ";
	if ($txt_order_no!='') $order_no_cond=" and dd.buyer_po_id like '%$txt_order_no%'"; else $order_no_cond="";
	
	if($cbo_mainbuyer_id!=0) $cbo_mainbuyer_id_cond=" and pdm.buyer_name ='$cbo_mainbuyer_id'"; else $cbo_mainbuyer_id_cond="";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and dm.delivery_date  between $txt_date_from and $txt_date_to";
	
	//echo $txt_date_from;echo "and to date ".$txt_date_to;die;
	//echo $cbo_mainbuyer_id_cond;die;
	$colorarr = return_library_array("select id,color_name from lib_color","id","color_name");
	$companyarr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerarr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$itemsizearr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$gmt_item_arr = return_library_array("select id,item_name from  lib_garment_item ","id","item_name");
	$body_part_arr = return_library_array("select id,body_part_full_name from  lib_body_part ","id","body_part_full_name");
	
	//$wo_no=return_library_array( "select distinct  order_id, order_no from subcon_ord_mst",'order_id','order_no');
	
	$buyer_po_arr = return_library_array("select id,po_number from wo_po_break_down ","id","po_number");
	$location_arr = return_library_array("select id, location_name from lib_location ","id","location_name");
	$style_arr = return_library_array("select a.id, b.style_ref_no from wo_po_break_down a inner join wo_po_details_master b on a.job_no_mst = b.job_no","id","style_ref_no");
	$ord_qty = return_library_array("select d.buyer_po_id as id, sum(d.order_quantity) * 12 as order_quantity from subcon_ord_dtls d where d.status_active = 1 and d.is_deleted = 0 group by d.buyer_po_id","id","order_quantity");
	//$buyer_arr = return_library_array("select   a.id,  c.buyer_name from wo_po_break_down a inner join wo_po_details_master b on a.job_no_mst = b.job_no
	//inner join lib_buyer c on b.buyer_name = c.id ","id","buyer_name");
	
	?>
    <div>
		<?
		//--------------------------------------------------------start----------------------------------------
		
		if($within_group==1)
		{
	 		$query = "select dm.company_id,  dm.location_id,  dm.delivery_date,  dm.delivery_no,  dm.party_id,  dm.job_no,  dd.color_size_id,  dd.buyer_po_id,  dd.sort_qty,  dm.remarks,
			dm.deli_party,  dm.deli_party_location,  dd.delivery_status,  dd.delivery_qty,  pbd.po_number, pdm.buyer_name,  pdm.style_ref_no,  pdm.style_description,
			ob.color_id,  ob.embellishment_type,  ob.body_part,  c.buyer_style_ref, c.order_no, sum(c.order_quantity) as order_quantity, avg(c.rate) as d_rate, sum(c.amount) as order_amount
			from subcon_delivery_mst dm
			inner join subcon_delivery_dtls dd
			on dm.id = dd.mst_id
			inner join wo_po_break_down pbd
			on pbd.id = dd.buyer_po_id
			inner join wo_po_details_master pdm
			on pbd.job_no_mst = pdm.job_no
			inner join subcon_ord_breakdown ob
			on dd.color_size_id = ob.id
			inner join subcon_ord_dtls c
			on ob.mst_id        = c.id
			where dm.status_active = 1 and dm.entry_form = 254  and dm.within_group=1  and dd.status_active = 1   $cbo_company_id_cond $style_ref_cond  $wo_no_cond $job_no_cond $date_cond $buyer_id_cond $order_no_cond $cbo_mainbuyer_id_cond 
			group by dm.company_id,  dm.location_id,  dm.delivery_date,  dm.delivery_no,  dm.party_id,  dm.job_no,  dd.color_size_id,  dd.buyer_po_id,  dd.sort_qty,  dm.remarks,  dm.deli_party,
			dm.deli_party_location,  dd.delivery_status,  dd.delivery_qty,  pbd.po_number,  pdm.buyer_name,  pdm.style_ref_no,  pdm.style_description,  ob.color_id,  ob.embellishment_type,
			ob.body_part, c.buyer_style_ref, c.order_no
			  order by pdm.buyer_name, c.order_no, pdm.style_ref_no, dd.buyer_po_id, dm.delivery_no ";
		}
		else if($within_group==2)
		{
			
			
	 		$query = "select dm.company_id,  dm.location_id,  dm.delivery_date,  dm.delivery_no,  dm.party_id,  dm.job_no,  dd.color_size_id, c.buyer_po_id,  dd.sort_qty,  dm.remarks,
			dm.deli_party,  dm.deli_party_location,  dd.delivery_status,  dd.delivery_qty,  c.buyer_po_no as po_number, c.buyer_buyer as buyer_name,  c.buyer_style_ref as style_ref_no,  0 as style_description,
			ob.color_id,  ob.embellishment_type,  ob.body_part,  c.buyer_style_ref, c.order_no, sum(c.order_quantity) as order_quantity, avg(c.rate) as d_rate, sum(c.amount) as order_amount
			from subcon_delivery_mst dm
			inner join subcon_delivery_dtls dd
			on dm.id = dd.mst_id 
			inner join subcon_ord_breakdown ob
			on dd.color_size_id = ob.id
			inner join subcon_ord_dtls c
			on ob.mst_id        = c.id
			where dm.status_active = 1 and dm.entry_form = 254  and dm.within_group=2  and dd.status_active = 1   $cbo_company_id_cond $style_ref_cond  $wo_no_cond $job_no_cond $date_cond $buyer_id_cond $order_no_cond $cbo_mainbuyer_id_cond 
			group by dm.company_id,  dm.location_id,  dm.delivery_date,  dm.delivery_no,  dm.party_id,  dm.job_no,  dd.color_size_id,  dd.sort_qty,  dm.remarks,  dm.deli_party,
			dm.deli_party_location,  dd.delivery_status,  dd.delivery_qty,  c.buyer_po_no,c.buyer_po_id,  c.buyer_buyer,  c.buyer_style_ref,  ob.color_id,  ob.embellishment_type,
			ob.body_part, c.buyer_style_ref, c.order_no
			  order by c.buyer_buyer, c.order_no, c.buyer_style_ref, c.buyer_po_id, dm.delivery_no ";
		}
	//echo $query;//die;
	$sql_data_query = sql_select($query);
	$countrecords = count($query); 
	//echo $query; die;
	
	$details_data=array();
	$delivery_data=array();
	$del_seq=array();
	
	$sql_order_bk = "select a.party_id, b.job_no_mst,  b.order_no,  c.order_id, b.buyer_style_ref,  sum(c.qnty)   as qnty,  avg(c.rate)   as rate,  sum(c.amount) as amount,  
				a.company_id,  a.receive_date,  avg(b.rate) as avg_rate,  b.order_quantity as order_quantity, b.amount as wo_amount, b.BUYER_PO_NO, to_char(a.receive_date,'YYYY') as WO_Year
					from subcon_ord_dtls b
					left join subcon_ord_breakdown c
					on b.id = c.mst_id
					inner join subcon_ord_mst a
					on a.id = b.mst_id
					where c.status_active = 1 and b.status_active = 1 and b.buyer_po_no is not null and to_char(a.receive_date,'YYYY') = '$year_id'
					$cbo_company_id_cond_ord $style_ref_cond_ord $wo_no_cond_ord $buyer_id_cond_ord
					group by a.party_id, b.job_no_mst,   b.order_no, b.buyer_style_ref,  c.order_id,  a.company_id,  a.receive_date, b.order_quantity, b.amount,b.buyer_po_no";
	
	$sql_order = "select a.party_id,  b.job_no_mst,  b.order_no,  b.buyer_style_ref,  a.company_id,  a.receive_date,  avg(b.rate) as avg_rate,  sum(b.order_quantity) as order_quantity,
				  sum(b.amount) as wo_amount,  b.buyer_po_no,  to_char(a.receive_date, 'yyyy') as wo_year, SUM(c.qnty) AS qnty, SUM(c.amount) AS amount 
				  FROM  subcon_ord_dtls b  INNER JOIN subcon_ord_mst a ON a.id = b.mst_id INNER JOIN metroerp3.subcon_ord_breakdown c ON c.mst_id = b.id
				  where  b.status_active                   = 1
				  and to_char(a.receive_date, 'yyyy') >= '2020'
				  and b.buyer_po_no                    is not null
				  $cbo_company_id_cond_ord $style_ref_cond_ord $wo_no_cond_ord $buyer_id_cond_ord
				  group by a.party_id,  b.job_no_mst,  b.order_no,  b.buyer_style_ref,  a.company_id,  a.receive_date,  b.buyer_po_no,  to_char(a.receive_date, 'yyyy')";
		
	//echo $sql_order;
	$sql_data_select = sql_select($sql_order);
	$countrecords_ord = count($sql_order);

		foreach( $sql_data_select as $row)
		{
			$wo_avg_rate[$row[csf("order_no")]]['wo_avg_rate']=$row[csf(avg_rate)];
			$wo_job[$row[csf("order_no")]]['wo_job']=$row[csf(job_no_mst)];
			$wo_date[$row[csf("order_no")]]['wo_date']=$row[csf(receive_date)];
			$wo_amounts[$row[csf("order_no")]]['wo_amount']+=$row[csf(wo_amount)];
			$wo_party[$row[csf("order_no")]]['party_id']=$row[csf(party_id)];
			
			$wo_style_po[$row[csf("job_no_mst")]][$row[csf("order_no")]][$row[csf("buyer_style_ref")]]['buyer_po_no'].=$row[csf(buyer_po_no)].', ';
			$wo_style_rate[$row[csf("job_no_mst")]][$row[csf("order_no")]][$row[csf("buyer_style_ref")]]['avg_rate']=$row[csf(wo_amount)]/$row[csf(order_quantity)];
			$wo_style_qty[$row[csf("job_no_mst")]][$row[csf("order_no")]][$row[csf("buyer_style_ref")]]['wo_qty']+=$row[csf(qnty)];
			$wo_style_amount[$row[csf("job_no_mst")]][$row[csf("order_no")]][$row[csf("buyer_style_ref")]]['wo_amount']+=$row[csf(amount)];
			
			//$wo_style_po_qty[$row[csf("order_no")]][$row[csf("buyer_style_ref")]][$row[csf("buyer_po_no")]]['wo_qty']+=$row[csf(order_quantity)];
			//break;
		}
		
		//if($countrecords_ord==1){break;}

	//echo "<pre>";print_r($wo_style_rate);//die;

	foreach( $sql_data_query as $row)
	{		
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["order_no"]=$row[csf(order_no)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["deli_party"]=$row[csf(deli_party)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["delivery_no"]=$row[csf(delivery_no)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["deli_party_location"]=$row[csf(deli_party_location)];
		//$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["order_no"]=$row[csf(order_no)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["buyer_po_id"]=$row[csf(buyer_po_id)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["color_id"]=$row[csf(color_id)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["buyer_name"]=$row[csf(buyer_name)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["po_number"]=$row[csf(po_number)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["style_ref_no"]=$row[csf(style_ref_no)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["delivery_qty"]+=$row[csf(delivery_qty)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["sort_qty"]+=$row[csf(sort_qty)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["party_id"]=$row[csf(party_id)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["delivery_date"]=$row[csf(delivery_date)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["job_no"]=$row[csf(job_no)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["buyer_style_ref"]=$row[csf(buyer_style_ref)];
		
		$sub_data[$row[csf("buyer_name")]][$row[csf("order_no")]]["delivery_qty"]+=$row[csf(delivery_qty)];		
		$sub_data[$row[csf("buyer_name")]][$row[csf("order_no")]]["sort_qty"]+=$row[csf(sort_qty)];
		$sub_data[$row[csf("buyer_name")]][$row[csf("order_no")]]["party_id"]=$row[csf(party_id)];
		
		$style_sub_data[$row[csf("buyer_name")]][$row[csf("style_ref_no")]][$row[csf("order_no")]]["delivery_qty"]+=$row[csf(delivery_qty)];
		$style_sub_data[$row[csf("buyer_name")]][$row[csf("style_ref_no")]][$row[csf("order_no")]]["sort_qty"]+=$row[csf(sort_qty)];
		$style_sub_data[$row[csf("buyer_name")]][$row[csf("style_ref_no")]][$row[csf("order_no")]]["party_id"]=$row[csf(party_id)];
		
		$sub_total_arr[$row[csf("buyer_name")]][$row[csf("deli_party")]]+=$row[csf(delivery_qty)];
		$sub_short_arr[$row[csf("buyer_name")]][$row[csf("deli_party")]]+=$row[csf(sort_qty)];
		$grand_total_arr[$row[csf("buyer_name")]]+=$row[csf(delivery_qty)];
		$grand_short_arr[$row[csf("buyer_name")]]+=$row[csf(sort_qty)];
		
		$w_qty = $wo_style_qty[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['wo_qty'];
		
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['wo_no']=$row[csf(order_no)];
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['job_no']=$row[csf(job_no)];
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['style_no']=$row[csf(style_ref_no)];
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['buyer_style_ref']=$row[csf(buyer_style_ref)];
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['po_number'].=$row[csf(po_number)].", ";
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['buyer_name']=$row[csf(buyer_name)];
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['party_id']=$row[csf(party_id)];
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['wo_qty']=$row[csf(order_quantity)];
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['order_amount']=$row[csf(order_amount)];
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['wo_rate']=$row[csf(order_amount)]/$row[csf(order_quantity)];
		
		$wo_delivery_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]+=$row[csf(delivery_qty)];
		$wo_ord_b_style[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("buyer_style_ref")]]['buyer_style_ref']=$row[csf(buyer_style_ref)];
		
	}
	//echo "<pre>";print_r($wo_order_data);die;
	$value_width =1250;	
	$sum_width =1250;	
	$t_qty=0;
	
	ob_start();
	?>
    
    <style>
 * Style for the header table */
  #table_header_1 {
    position: sticky;
    top: 0;
    background-color: #f2f2f2;
    z-index: 1; /* Ensure the header is above the scrolling content */
  }
  
  /* Style for the body table */
  #table_body_wrapper {
    max-height: 400px; /* Set the maximum height for scrolling */
    overflow-y: scroll; /* Enable vertical scrolling */
  }
  
   #table_header {
    position: sticky;
    top: 0;
    background-color: #f2f2f2;
    z-index: 1; /* Ensure the header is above the scrolling content */
  }
  
  /* Style for the body table */
  #table_body_wrapper_one {
    max-height: 400px; /* Set the maximum height for scrolling */
    overflow-y: scroll; /* Enable vertical scrolling */
  }
  
  
  #table_body_st1 {
    /* Add any styling you want for the body table */
  }
</style>
	<div align="center" style="width:<? echo $value_width + 33; ?>px;" id="table_header_scorl">
        <fieldset  style="width:<? echo $value_width + 28; ?>px;" > 
		
			<br>
		    <table align = "center" width="<? echo $value_width; ?>">
			<tr>  
	                <td align="center" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">MERCER DESIGN TEX LTD. </strong>
	                </td>
	            </tr>
	            <tr>  
	                <td align="center" colspan="11" class="form_caption" >
	                	<strong style="font-size:16px">Screen Print Delivery Statement </strong>
	                </td>
	            </tr>
				<tr>  
	                <td align="center" colspan="11" class="form_caption" >
	                	<strong style="font-size:14px">Statement Period :<? echo $txt_date_from." TO ".$txt_date_to;?> </strong>
	                </td>
	            </tr>
	        </table>
			<br>
	
			<table align = "left"  width="<? echo $sum_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header" rules="all">
				<thead>
					<th width="30" align="center">Sl </th>
					<th width="120" align="center">Buyer Name</th>
					<th width="100" align="center" >Print JOB NO</th>	
					<th width="100" align="center">WO NO</th>
					<th width="80" align="center">WO Rcvd Date</th>
					<th width="170" align="center">Style Name</th>		
					<th width="80" align="center">WO Qty(Dzn)</th>					
					<th width="60" align="center">Avg Rate(Dzn)</th>
					<th width="80" align="center">Del Qty(Dzn)</th>
					<th align="center">PO No</th>
				</thead>
			</table>
			<div  id="table_body_wrapper_one" align="center" style="width:<? echo $sum_width + 28; ?>px;">	
				<table align = "left" width="<? echo $sum_width; ?>"   class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_st">
					<tbody>
					<?	
					$t=1;
					foreach($wo_order_data as $job_id=>$job_detail)
					{
						foreach($job_detail as $wo_id=>$wo_data_value)
						{
							foreach($wo_data_value as $style_id=>$wo_data)
							{
								if ($t%2==0)  
								$bgcolor="#e9f3ff"; else $bgcolor="#ffffff";
								?>
								<tr  height="27" bgcolor="<? echo $bgcolor; ?>"  id="tr_<? echo $t;?>">
								<?php 
										$wo = $wo_data[('wo_no')];
										$party = $wo_data[('party_id')];
										
								?>
									<td width="30" align="left"><? echo $t; ?> </td>
									<td width="120" style="word-wrap: break-word;word-break: break-all;" align="left"><? echo  $buyerarr[$wo_data[('buyer_name')]]; ?> </td>
									<td width="100" style="word-wrap: break-word;word-break: break-all;"  align="left"><? echo $wo_data[('job_no')];  ?></td>
									<td width="100" style="word-wrap: break-word;word-break: break-all;" align="left">
										<a href="##" onClick="generate_trim_report('show_trim_booking_report2',1,<? echo "'$wo'";?>,<? echo $party;?>,0,1)"><? echo $wo_data[('wo_no')];  ?> </a>
										<!--<a href="#" onClick="fnc_emb_wo_print('show_trim_booking_report2',1,<? //echo "'$wo'";?>,<? //echo $party;?>)"><? //echo $wo_data[('wo_no')];  ?> </a>-->
									</td>
									<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="left"><?  echo $wo_date[$wo_data[('wo_no')]]['wo_date']; ?></td>
									<td width="170" style="word-wrap: break-word;word-break: break-all;" align="left"> <? echo $wo_data[('style_no')];  ?></td>
									<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"> 
									<?
									
									if ($wo_style_qty[$job_id][$wo_id][$style_id]['wo_qty']=="" or $wo_style_qty[$job_id][$wo_id][$style_id]['wo_qty']==0)
									{
										echo $wo_data[('wo_qty')]; 
																				
									}
									else
									{
										echo number_format($wo_style_qty[$job_id][$wo_id][$style_id]['wo_qty'],4); 
									}
									?>
									
									</td>							
									<td width="60" style="word-wrap: break-word;word-break: break-all;" align="center"> 
									<? 
									if($wo_style_rate[$job_id][$wo_id][$style_id]['avg_rate']=="" or $wo_style_rate[$job_id][$wo_id][$style_id]['avg_rate']==0)
									{
										echo $wo_data[('wo_rate')]; 
									}
									else
									{
										echo  number_format($wo_style_amount[$job_id][$wo_id][$style_id]['wo_amount']/$wo_style_qty[$job_id][$wo_id][$style_id]['wo_qty'],2);
											//echo  number_format($wo_style_qty[$job_id][$wo_id][$style_id]['wo_qty'],4);
									}
									?>
									</td>
									<td width="80" style="word-wrap: break-word;word-break: break-all;" align="center" > <? echo number_format($wo_delivery_data[$job_id][$wo_id][$style_id]/12,2); ?></td>
									<td  align="left" style="word-wrap: break-word;word-break: break-all;">
									<? 
										if($wo_style_po[$job_id][$wo_id][$style_id]['buyer_po_no']!="")
										{
											//echo $wo_style_po[$job_id][$wo_id][$style_id]['buyer_po_no']; 
											
											$po_numbers=array_filter(explode(', ', $wo_style_po[$job_id][$wo_id][$style_id]['buyer_po_no']));
											$po_vars="";
											foreach ($po_numbers as $key => $value) 
											{
												$po_numbers2=explode(',', $value);
												$po_vars.=($po_vars=="")?$po_numbers2[0]:",".$po_numbers2[0];
											}
											$unique_po_vars=implode(",", array_unique(explode(",", $po_vars)));
											echo $unique_po_vars;
										}
										else
										{
											//echo "N/A"; 
											//echo $wo_data[('po_number')];
											
											$po_number=array_filter(explode(', ', $wo_data[('po_number')]));
											$po_var="";
											foreach ($po_number as $key => $value) 
											{
												$po_number2=explode(',', $value);
												$po_var.=($po_var=="")?$po_number2[0]:",".$po_number2[0];
											}
											$unique_po_var=implode(",", array_unique(explode(",", $po_var)));
											echo $unique_po_var;
										}
										 
									?>
									</td>
								</tr>
								<?
								
									if ($wo_style_qty[$job_id][$wo_id][$style_id]['wo_qty']=="" or $wo_style_qty[$job_id][$wo_id][$style_id]['wo_qty']==0)
									{
										$t_qty += $wo_data[('wo_qty')]; 
									}
									else
									{

										$t_qty += $wo_style_qty[$job_id][$wo_id][$style_id]['wo_qty'];
									}
										
									$dt_qty += $wo_delivery_data[$job_id][$wo_id][$style_id];
								
								$del_seq[$job_id][$wo_id]=$t;
								$t++;							
							}
						}
					}
					?>
					</tbody>
					<tfoot>
						<tr height="25"  bgcolor=" #47b4ea">  
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td > <strong>Grand total :</strong></td>								
							<td align="right"><strong><? echo number_format($t_qty,4,'.',''); ?> </strong></td>	
							<td  align="right"> </td>	
							<td align="right"> <strong><? echo number_format($dt_qty,4,'.',''); ?> </strong></td>							
							<td  align="right"> <strong></strong></td>							
						</tr>
					</tfoot>
				</table>
			</div>
			<br>
			<div>
			<table align = "left"  width="<? echo $value_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" rules="all">
				<thead>
					<th  width="30" align="center">Sl </th>
					<th  width="80" align="center">Del Date</th>	
					<th  width="100" align="center">Buyer</th>
					<th  width="170" align="center">Wo No </th>
					<th  width="40" align="center">Sum Seq </th>	
					<th  width="200" align="center">Style</th>
					<th  width="100" align="center">Del No</th>						
					<th  width="115" align="center">Delivery Party </th>
					<th  width="150" align="center">Del Place</th>
					<th  width="70" align="center">Del Qty(pcs)</th>
					<th  width="70" align="center">Del Qty(dzn)</th>
					<th  align="center">S.Qty</th>
						
				</thead>
			</table>
			<div id="table_body_wrapper"  align="center" style="width:<? echo $value_width + 28; ?>px;">	
				<table align = "left" width="<? echo $value_width; ?>"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_st1">
					<tbody>
						<?	
						$k=1;
						foreach($delivery_data as $buyer_id=>$buyer_data)
						{
							foreach($buyer_data as $wo_no=>$wo_data)
							{
								foreach($wo_data as $style_id=>$style_data)
								{
									foreach($style_data as $d_id=>$d_data)
									{
										foreach($d_data as $del_party_id=>$del_party_data)
										{
											foreach($del_party_data as $del_party_loc_id=>$color_data)
											{
												//foreach($del_party_loc_data as $po_id=>$color_data)
												//{
													
														if ($k%2==0)  
															$bgcolor="#e9f3ff";
															else
															$bgcolor="#ffffff";
														?>
															<tr  height="27" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
																<td  width="30"> <? echo $k; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="80"> <? echo $color_data[('delivery_date')]; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="100"> <? echo $buyerarr[$color_data[('buyer_name')]]; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="170"><abbr title="Job No :<? echo $color_data[('job_no')].":: Style :".$color_data[('style_ref_no')]; ?>"><? echo $color_data[('order_no')]; ?></abbr></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="40" align="center">  <? echo $del_seq[$color_data[('job_no')]][$wo_no];//$color_data[('style_ref_no')]; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="200">  <? echo $color_data[('style_ref_no')]; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="100">  <? echo $color_data[('delivery_no')]; ?></td>
																<td style="word-wrap: break-all;" width="115">  <? echo $companyarr[$color_data[('deli_party')]]; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="150"> <? echo  $location_arr[$color_data[('deli_party_location')]]; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="70" align="right">  <? echo $color_data[('delivery_qty')]; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="70" align="right">  <? echo number_format($color_data[('delivery_qty')]/12,2); ?></td>
																<td style="word-wrap: break-word;word-break: break-all;"  align="center">  <? echo $color_data[('sort_qty')]; ?></td>															
															</tr>
															<?
															$k++;
															$value_total_del_buyer+=$color_data[('delivery_qty')];
															$value_total_del_dzn_buyer+=$color_data[('delivery_qty')]/12;
															$value_total_short_buyer+=$color_data[('sort_qty')];
													
												//}
											}
										}
									}
								
									?>
									<tr height="23" bgcolor=" #77eef8" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
										<td  colspan="9" align="right"> <strong> Style Sub Total : </strong></td>
										<td style="word-wrap: break-word;word-break: break-all;" align="right"><strong><? echo $style_sub_data[$buyer_id][$style_id][$wo_no]['delivery_qty'];// $color_data[('delivery_qty')]; ?></strong></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="right"><strong>  <? echo number_format($style_sub_data[$buyer_id][$style_id][$wo_no]['delivery_qty']/12,2);// $color_data[('delivery_qty')]/12; ?></strong></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><strong>  <? echo $style_sub_data[$buyer_id][$style_id][$wo_no]['sort_qty'];// $color_data[('sort_qty')]; ?></strong></td>															
									</tr>
										
									<?
								}
								?>
									<tr height="23" bgcolor=" #77eef8" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
											<td   colspan="9" align="right"> <strong>WO Sub Total : </strong></td>
											<td style="word-wrap: break-word;word-break: break-all;"  align="right"><strong><? echo $sub_data[$buyer_id][$wo_no]['delivery_qty'];// $color_data[('delivery_qty')]; ?></strong></td>
											<td style="word-wrap: break-word;word-break: break-all;" align="right"><strong>  <? echo number_format($sub_data[$buyer_id][$wo_no]['delivery_qty']/12,2);// $color_data[('delivery_qty')]/12; ?></strong></td>
											<td style="word-wrap: break-word;word-break: break-all;"  align="center"><strong>  <? echo $sub_data[$buyer_id][$wo_no]['sort_qty'];// $color_data[('sort_qty')]; ?></strong></td>															
										</tr>
								<?
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr height="25"  bgcolor=" #47b4ea">  
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ><strong>Grand total :</strong></td>							
							<td align="right"> <strong><? echo number_format($value_total_del_buyer,0,'.',''); ?> </strong></td>
							<td align="right"> <strong><? echo number_format($value_total_del_dzn_buyer,2,'.',''); ?> </strong></td>
							<td align="center"> <strong><? echo number_format($value_total_short_buyer,2,'.',''); ?> </strong></td>	
						</tr>
					</tfoot>
				</table>
			</div> 
			
		</fieldset>
	</div>	
		<?
				//--------------------------------------------------------end----------------------------------------
		?>
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

if($action=="report_generate_st_bk---------")
{ 
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$job_no=str_replace("'","",$txt_job_no);
	
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_wo_no=str_replace("'","",$txt_wo_no);
	$year_id=str_replace("'","",$cbo_year);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	
	$cbo_mainbuyer_id=str_replace("'","",$cbo_mainbuyer_id);//buyer_id
	
	if($year_id!=0) $year_cond=" and to_char(a.insert_date,'yyyy')=$year_id"; else $year_cond="";
	if($cbo_buyer_id!=0) $buyer_id_cond=" and dm.party_id ='$cbo_buyer_id'"; else $buyer_id_cond="";
	if($cbo_buyer_id!=0) $buyer_id_cond_ord=" and a.party_id ='$cbo_buyer_id'"; else $buyer_id_cond_ord="";
	
	if($cbo_company_id!=0) $cbo_company_id_cond=" and dm.company_id ='$cbo_company_id'"; else $cbo_company_id_cond="";
	if($cbo_company_id!=0) $cbo_company_id_cond_ord=" and a.company_id ='$cbo_company_id'"; else $cbo_company_id_cond_ord="";
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and dm.job_no = '$job_no' ";
	//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
	if ($txt_style_ref!='') $style_ref_cond=" and pdm.style_ref_no like '%$txt_style_ref%'"; else $style_ref_cond="";
	if ($txt_style_ref!='') $style_ref_cond_ord=" and b.buyer_style_ref like '%$txt_style_ref%'"; else $style_ref_cond_ord="";
	if(trim($txt_wo_no)!="") $wo_no_cond=" and c.order_no like '%$txt_wo_no%' "; else $wo_no_cond=" ";
	if(trim($txt_wo_no)!="") $wo_no_cond_ord=" and b.order_no like '%$txt_wo_no%' "; else $wo_no_cond_ord=" ";
	if ($txt_order_no!='') $order_no_cond=" and dd.buyer_po_id like '%$txt_order_no%'"; else $order_no_cond="";
	
	if($cbo_mainbuyer_id!=0) $cbo_mainbuyer_id_cond=" and pdm.buyer_name ='$cbo_mainbuyer_id'"; else $cbo_mainbuyer_id_cond="";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and dm.delivery_date  between $txt_date_from and $txt_date_to";
	
	//echo $txt_date_from;echo "and to date ".$txt_date_to;die;
	//echo $cbo_mainbuyer_id_cond;die;
	$colorarr = return_library_array("select id,color_name from lib_color","id","color_name");
	$companyarr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerarr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$itemsizearr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$gmt_item_arr = return_library_array("select id,item_name from  lib_garment_item ","id","item_name");
	$body_part_arr = return_library_array("select id,body_part_full_name from  lib_body_part ","id","body_part_full_name");
	
	//$wo_no=return_library_array( "select distinct  order_id, order_no from subcon_ord_mst",'order_id','order_no');
	
	$buyer_po_arr = return_library_array("select id,po_number from wo_po_break_down ","id","po_number");
	$location_arr = return_library_array("select id, location_name from lib_location ","id","location_name");
	$style_arr = return_library_array("select a.id, b.style_ref_no from wo_po_break_down a inner join wo_po_details_master b on a.job_no_mst = b.job_no","id","style_ref_no");
	$ord_qty = return_library_array("select d.buyer_po_id as id, sum(d.order_quantity) * 12 as order_quantity from subcon_ord_dtls d where d.status_active = 1 and d.is_deleted = 0 group by d.buyer_po_id","id","order_quantity");
	//$buyer_arr = return_library_array("select   a.id,  c.buyer_name from wo_po_break_down a inner join wo_po_details_master b on a.job_no_mst = b.job_no
	//inner join lib_buyer c on b.buyer_name = c.id ","id","buyer_name");
	
	?>
    <div>
		<?
		//--------------------------------------------------------start----------------------------------------
			  
	$query = "select dm.company_id,  dm.location_id,  dm.delivery_date,  dm.delivery_no,  dm.party_id,  dm.job_no,  dd.color_size_id,  dd.buyer_po_id,  dd.sort_qty,  dm.remarks,
			dm.deli_party,  dm.deli_party_location,  dd.delivery_status,  dd.delivery_qty,  pbd.po_number,  pdm.buyer_name,  pdm.style_ref_no,  pdm.style_description,
			ob.color_id,  ob.embellishment_type,  ob.body_part,  c.order_no, sum(c.order_quantity) as order_quantity, avg(c.rate) as d_rate
			from subcon_delivery_mst dm
			inner join subcon_delivery_dtls dd
			on dm.id = dd.mst_id
			inner join wo_po_break_down pbd
			on pbd.id = dd.buyer_po_id
			inner join wo_po_details_master pdm
			on pbd.job_no_mst = pdm.job_no
			inner join subcon_ord_breakdown ob
			on dd.color_size_id = ob.id
			inner join subcon_ord_dtls c
			on ob.mst_id        = c.id
			where dm.status_active = 1   and dd.status_active = 1   $cbo_company_id_cond $style_ref_cond  $wo_no_cond $job_no_cond $date_cond $buyer_id_cond $order_no_cond $cbo_mainbuyer_id_cond 
			group by dm.company_id,  dm.location_id,  dm.delivery_date,  dm.delivery_no,  dm.party_id,  dm.job_no,  dd.color_size_id,  dd.buyer_po_id,  dd.sort_qty,  dm.remarks,  dm.deli_party,
			dm.deli_party_location,  dd.delivery_status,  dd.delivery_qty,  pbd.po_number,  pdm.buyer_name,  pdm.style_ref_no,  pdm.style_description,  ob.color_id,  ob.embellishment_type,
			ob.body_part,  c.order_no
			  order by pdm.buyer_name, c.order_no, pdm.style_ref_no, dd.buyer_po_id, dm.delivery_no ";
	//echo $query;//die;
	$sql_data_query = sql_select($query);
	$countrecords = count($query); 
	//echo $query; die;
	
	$details_data=array();
	$delivery_data=array();
	$del_seq=array();
	
	$sql_order_bk = "select a.party_id, b.job_no_mst,  b.order_no,  c.order_id, b.buyer_style_ref,  sum(c.qnty)   as qnty,  avg(c.rate)   as rate,  sum(c.amount) as amount,  
				a.company_id,  a.receive_date,  avg(b.rate) as avg_rate,  b.order_quantity as order_quantity, b.amount as wo_amount, b.BUYER_PO_NO, to_char(a.receive_date,'YYYY') as WO_Year
					from subcon_ord_dtls b
					left join subcon_ord_breakdown c
					on b.id = c.mst_id
					inner join subcon_ord_mst a
					on a.id = b.mst_id
					where c.status_active = 1 and b.status_active = 1 and b.buyer_po_no is not null and to_char(a.receive_date,'YYYY') = '$year_id'
					$cbo_company_id_cond_ord $style_ref_cond_ord $wo_no_cond_ord $buyer_id_cond_ord
					group by a.party_id, b.job_no_mst,   b.order_no, b.buyer_style_ref,  c.order_id,  a.company_id,  a.receive_date, b.order_quantity, b.amount,b.buyer_po_no";
	
	$sql_order = "SELECT a.PARTY_ID,  b.JOB_NO_MST,  b.ORDER_NO,  b.BUYER_STYLE_REF,  a.COMPANY_ID,  a.RECEIVE_DATE,  AVG(b.RATE)           AS avg_rate,  SUM(b.ORDER_QUANTITY) AS order_quantity,
				  SUM(b.AMOUNT)         AS wo_amount,  b.BUYER_PO_NO,  TO_CHAR(a.RECEIVE_DATE, 'YYYY') AS WO_Year
				FROM subcon_ord_dtls b INNER JOIN subcon_ord_mst a ON a.ID = b.MST_ID
				WHERE  b.STATUS_ACTIVE                   = 1
				AND TO_CHAR(a.RECEIVE_DATE, 'YYYY') >= '2020'
				
				AND b.BUYER_PO_NO                    IS NOT NULL
				$cbo_company_id_cond_ord $style_ref_cond_ord $wo_no_cond_ord $buyer_id_cond_ord
				GROUP BY a.PARTY_ID,  b.JOB_NO_MST,  b.ORDER_NO,  b.BUYER_STYLE_REF,  a.COMPANY_ID,  a.RECEIVE_DATE,  b.BUYER_PO_NO,  TO_CHAR(a.RECEIVE_DATE, 'YYYY')";
		
	//echo $sql_order;
	$sql_data_select = sql_select($sql_order);
	$countrecords_ord = count($sql_order);

		foreach( $sql_data_select as $row)
		{
			$wo_avg_rate[$row[csf("order_no")]]['wo_avg_rate']=$row[csf(avg_rate)];
			$wo_job[$row[csf("order_no")]]['wo_job']=$row[csf(job_no_mst)];
			$wo_date[$row[csf("order_no")]]['wo_date']=$row[csf(receive_date)];
			$wo_amounts[$row[csf("order_no")]]['wo_amount']+=$row[csf(wo_amount)];
			
			$wo_style_po[$row[csf("job_no_mst")]][$row[csf("order_no")]][$row[csf("buyer_style_ref")]]['buyer_po_no'].=$row[csf(buyer_po_no)].', ';
			$wo_style_rate[$row[csf("job_no_mst")]][$row[csf("order_no")]][$row[csf("buyer_style_ref")]]['avg_rate']=$row[csf(avg_rate)];
			$wo_style_qty[$row[csf("job_no_mst")]][$row[csf("order_no")]][$row[csf("buyer_style_ref")]]['wo_qty']+=$row[csf(order_quantity)];
			
			//$wo_style_PO_qty[$row[csf("order_no")]][$row[csf("buyer_style_ref")]][$row[csf("BUYER_PO_NO")]]['wo_qty']+=$row[csf(order_quantity)];
			//break;
		}
		
		//if($countrecords_ord==1){break;}

	//echo "<pre>";print_r($wo_style_qty);//die;

	foreach( $sql_data_query as $row)
	{		
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["order_no"]=$row[csf(order_no)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["deli_party"]=$row[csf(deli_party)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["delivery_no"]=$row[csf(delivery_no)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["deli_party_location"]=$row[csf(deli_party_location)];
		//$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["order_no"]=$row[csf(order_no)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["buyer_po_id"]=$row[csf(buyer_po_id)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["color_id"]=$row[csf(color_id)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["buyer_name"]=$row[csf(buyer_name)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["po_number"]=$row[csf(po_number)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["style_ref_no"]=$row[csf(style_ref_no)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["delivery_qty"]+=$row[csf(delivery_qty)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["sort_qty"]+=$row[csf(sort_qty)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["party_id"]=$row[csf(party_id)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["delivery_date"]=$row[csf(delivery_date)];
		$delivery_data[$row[csf("buyer_name")]][$row[csf("order_no")]][$row[csf("style_ref_no")]][$row[csf("delivery_no")]][$row[csf("deli_party")]][$row[csf("deli_party_location")]] ["job_no"]=$row[csf(job_no)];
		
		$sub_data[$row[csf("buyer_name")]][$row[csf("order_no")]]["delivery_qty"]+=$row[csf(delivery_qty)];		
		$sub_data[$row[csf("buyer_name")]][$row[csf("order_no")]]["sort_qty"]+=$row[csf(sort_qty)];
		$sub_data[$row[csf("buyer_name")]][$row[csf("order_no")]]["party_id"]=$row[csf(party_id)];
		
		$style_sub_data[$row[csf("buyer_name")]][$row[csf("style_ref_no")]][$row[csf("order_no")]]["delivery_qty"]+=$row[csf(delivery_qty)];
		$style_sub_data[$row[csf("buyer_name")]][$row[csf("style_ref_no")]][$row[csf("order_no")]]["sort_qty"]+=$row[csf(sort_qty)];
		$style_sub_data[$row[csf("buyer_name")]][$row[csf("style_ref_no")]][$row[csf("order_no")]]["party_id"]=$row[csf(party_id)];
		
		$sub_total_arr[$row[csf("buyer_name")]][$row[csf("deli_party")]]+=$row[csf(delivery_qty)];
		$sub_short_arr[$row[csf("buyer_name")]][$row[csf("deli_party")]]+=$row[csf(sort_qty)];
		$grand_total_arr[$row[csf("buyer_name")]]+=$row[csf(delivery_qty)];
		$grand_short_arr[$row[csf("buyer_name")]]+=$row[csf(sort_qty)];
		
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['wo_no']=$row[csf(order_no)];
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['job_no']=$row[csf(job_no)];
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['style_no']=$row[csf(style_ref_no)];
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['po_number'].=$row[csf(po_number)].", ";
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['buyer_name']=$row[csf(buyer_name)];
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['party_id']=$row[csf(party_id)];
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['wo_qty']=$row[csf(order_quantity)];
		$wo_order_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]['wo_rate']=$row[csf(d_rate)];
		
		$wo_delivery_data[$row[csf("job_no")]][$row[csf("order_no")]][$row[csf("style_ref_no")]]+=$row[csf(delivery_qty)];
		
	}
	//echo "<pre>";print_r($wo_order_data);die;
	$value_width =1250;	
	$sum_width =1250;	
	$t_qty=0;
	
	ob_start();
	?>
	<div align="center" style="width:<? echo $value_width + 33; ?>px;">
        <fieldset  style="width:<? echo $value_width + 28; ?>px;" > 
		
			<br>
		    <table align = "center" width="<? echo $value_width; ?>">
			<tr>  
	                <td align="center" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">MERCER DESIGN TEX LTD. </strong>
	                </td>
	            </tr>
	            <tr>  
	                <td align="center" colspan="11" class="form_caption" >
	                	<strong style="font-size:16px">Screen Print Delivery Statement </strong>
	                </td>
	            </tr>
				<tr>  
	                <td align="center" colspan="11" class="form_caption" >
	                	<strong style="font-size:14px">Statement Period :<? echo $txt_date_from." TO ".$txt_date_to;?> </strong>
	                </td>
	            </tr>
	        </table>
			<br>
	
			<table align = "center"  width="<? echo $sum_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header" rules="all">
				<thead>
					<th width="30" align="center">Sl </th>
					<th width="120" align="center">Buyer Name</th>
					<th width="100" align="center">JOB NO</th>	
					<th width="100" align="center">WO NO</th>
					<th width="80" align="center">WO Rcvd Date</th>
					<th width="170" align="center">Style Name</th>		
					<th width="80" align="center">WO Qty(Dzn)</th>					
					<th width="60" align="center">Avg Rate(Dzn)</th>
					<th width="80" align="center">Del Qty(Dzn)</th>
					<th align="center">PO No</th>
				</thead>
			</table>
			<div align="center" style="width:<? echo $sum_width + 28; ?>px;">	
				<table align = "center" width="<? echo $sum_width; ?>"   class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_st">
					<tbody>
					<?	
					$t=1;
					foreach($wo_order_data as $job_id=>$job_detail)
					{
						foreach($job_detail as $wo_id=>$wo_data)
						{
							foreach($wo_data as $style_id=>$wo_data)
							{
								if ($t%2==0)  
								$bgcolor="#e9f3ff"; else $bgcolor="#ffffff";
								?>
								<tr  height="27" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t;?>">
									<td width="30" align="left"><? echo $t; ?> </td>
									<td width="120" style="word-wrap: break-word;word-break: break-all;" align="left"><? echo  $buyerarr[$wo_data[('buyer_name')]]; ?> </td>
									<td width="100" style="word-wrap: break-word;word-break: break-all;" align="left"><? echo $wo_data[('job_no')];  ?></td>
									<td width="100" style="word-wrap: break-word;word-break: break-all;" align="left"><? echo $wo_data[('wo_no')];  ?> </td>
									<td width="80"  style="word-wrap: break-word;word-break: break-all;" align="left"><?  echo $wo_date[$wo_data[('wo_no')]]['wo_date']; ?></td>
									<td width="170" style="word-wrap: break-word;word-break: break-all;" align="left"> <? echo $wo_data[('style_no')];  ?></td>
									<td width="80" style="word-wrap: break-word;word-break: break-all;" align="right"> 
									<?
									
									if ($wo_style_qty[$job_id][$wo_id][$style_id]['wo_qty']=="" or $wo_style_qty[$job_id][$wo_id][$style_id]['wo_qty']==0)
									{
										echo $wo_data[('wo_qty')]; 
									}
									else
									{

										echo number_format($wo_style_qty[$job_id][$wo_id][$style_id]['wo_qty'],4); 
									}
									?>
									
									</td>							
									<td width="60" style="word-wrap: break-word;word-break: break-all;" align="center"> 
									<? 
									if($wo_style_rate[$job_id][$wo_id][$style_id]['avg_rate']=="" or $wo_style_rate[$job_id][$wo_id][$style_id]['avg_rate']==0)
									{
										echo $wo_data[('wo_rate')]; 
									}
									else
									{
										echo  number_format($wo_style_rate[$job_id][$wo_id][$style_id]['avg_rate'],2); 
									}
									?>
									</td>
									<td width="80" style="word-wrap: break-word;word-break: break-all;" align="center" > <? echo number_format($wo_delivery_data[$job_id][$wo_id][$style_id]/12,2); ?></td>
									<td  align="left" style="word-wrap: break-word;word-break: break-all;">
									<? 
										if($wo_style_po[$job_id][$wo_id][$style_id]['buyer_po_no']!="")
										{
											//echo $wo_style_po[$job_id][$wo_id][$style_id]['buyer_po_no']; 
											
											$po_numbers=array_filter(explode(', ', $wo_style_po[$job_id][$wo_id][$style_id]['buyer_po_no']));
											$po_vars="";
											foreach ($po_numbers as $key => $value) 
											{
												$po_numbers2=explode(',', $value);
												$po_vars.=($po_vars=="")?$po_numbers2[0]:",".$po_numbers2[0];
											}
											$unique_po_vars=implode(",", array_unique(explode(",", $po_vars)));
											echo $unique_po_vars;
										}
										else
										{
											//echo "N/A"; 
											//echo $wo_data[('po_number')];
											
											$po_number=array_filter(explode(', ', $wo_data[('po_number')]));
											$po_var="";
											foreach ($po_number as $key => $value) 
											{
												$po_number2=explode(',', $value);
												$po_var.=($po_var=="")?$po_number2[0]:",".$po_number2[0];
											}
											$unique_po_var=implode(",", array_unique(explode(",", $po_var)));
											echo $unique_po_var;
										}
										 
									?>
									</td>
								</tr>
								<?
								
									if ($wo_style_qty[$job_id][$wo_id][$style_id]['wo_qty']=="" or $wo_style_qty[$job_id][$wo_id][$style_id]['wo_qty']==0)
									{
										$t_qty += $wo_data[('wo_qty')]; 
									}
									else
									{

										$t_qty += $wo_style_qty[$job_id][$wo_id][$style_id]['wo_qty'];
									}
										
									$dt_qty += $wo_delivery_data[$job_id][$wo_id][$style_id];
								
								$del_seq[$job_id][$wo_id]=$t;
								$t++;							
							}
						}
					}
					?>
					</tbody>
					<tfoot>
						<tr height="25"  bgcolor=" #47b4ea">  
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td > <strong>Grand total :</strong></td>								
							<td align="right"><strong><? echo number_format($t_qty,4,'.',''); ?> </strong></td>	
							<td  align="right"> </td>	
							<td align="right"> <strong><? echo number_format($dt_qty,4,'.',''); ?> </strong></td>							
							<td  align="right"> <strong></strong></td>							
						</tr>
					</tfoot>
				</table>
			</div>
			<br>
			<div>
			<table align = "center"  width="<? echo $value_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" rules="all">
				<thead>
					<th  width="30" align="center">Sl </th>
					<th  width="80" align="center">Del Date</th>	
					<th  width="100" align="center">Buyer</th>
					<th  width="170" align="center">Wo No </th>
					<th  width="40" align="center">Sum Seq </th>	
					<th  width="200" align="center">Style</th>
					<th  width="100" align="center">Del No</th>						
					<th  width="115" align="center">Delivery Party </th>
					<th  width="150" align="center">Del Place</th>
					<th  width="70" align="center">Del Qty(pcs)</th>
					<th  width="70" align="center">Del Qty(dzn)</th>
					<th  align="center">S.Qty</th>
						
				</thead>
			</table>
			<div align="center" style="width:<? echo $value_width + 28; ?>px;">	
				<table align = "center" width="<? echo $value_width; ?>"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_st1">
					<tbody>
						<?	
						$k=1;
						foreach($delivery_data as $buyer_id=>$buyer_data)
						{
							foreach($buyer_data as $wo_no=>$wo_data)
							{
								foreach($wo_data as $style_id=>$style_data)
								{
									foreach($style_data as $d_id=>$d_data)
									{
										foreach($d_data as $del_party_id=>$del_party_data)
										{
											foreach($del_party_data as $del_party_loc_id=>$color_data)
											{
												//foreach($del_party_loc_data as $po_id=>$color_data)
												//{
													
														if ($k%2==0)  
															$bgcolor="#e9f3ff";
															else
															$bgcolor="#ffffff";
														?>
															<tr  height="27" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
																<td  width="30"> <? echo $k; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="80"> <? echo $color_data[('delivery_date')]; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="100"> <? echo $buyerarr[$color_data[('buyer_name')]]; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="170"><abbr title="Job No :<? echo $color_data[('job_no')].":: Style :".$color_data[('style_ref_no')]; ?>"><? echo $color_data[('order_no')]; ?></abbr></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="40" align="center">  <? echo $del_seq[$color_data[('job_no')]][$wo_no];//$color_data[('style_ref_no')]; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="200">  <? echo $color_data[('style_ref_no')]; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="100">  <? echo $color_data[('delivery_no')]; ?></td>
																<td style="word-wrap: break-all;" width="115">  <? echo $companyarr[$color_data[('deli_party')]]; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="150"> <? echo  $location_arr[$color_data[('deli_party_location')]]; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="70" align="right">  <? echo $color_data[('delivery_qty')]; ?></td>
																<td style="word-wrap: break-word;word-break: break-all;" width="70" align="right">  <? echo number_format($color_data[('delivery_qty')]/12,2); ?></td>
																<td style="word-wrap: break-word;word-break: break-all;"  align="center">  <? echo $color_data[('sort_qty')]; ?></td>															
															</tr>
															<?
															$k++;
															$value_total_del_buyer+=$color_data[('delivery_qty')];
															$value_total_del_dzn_buyer+=$color_data[('delivery_qty')]/12;
															$value_total_short_buyer+=$color_data[('sort_qty')];
													
												//}
											}
										}
									}
								
									?>
									<tr height="23" bgcolor=" #77eef8" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
										<td  colspan="9" align="right"> <strong> Style Sub Total : </strong></td>
										<td style="word-wrap: break-word;word-break: break-all;" align="right"><strong><? echo $style_sub_data[$buyer_id][$style_id][$wo_no]['delivery_qty'];// $color_data[('delivery_qty')]; ?></strong></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="right"><strong>  <? echo number_format($style_sub_data[$buyer_id][$style_id][$wo_no]['delivery_qty']/12,2);// $color_data[('delivery_qty')]/12; ?></strong></td>
										<td style="word-wrap: break-word;word-break: break-all;"  align="center"><strong>  <? echo $style_sub_data[$buyer_id][$style_id][$wo_no]['sort_qty'];// $color_data[('sort_qty')]; ?></strong></td>															
									</tr>
										
									<?
								}
								?>
									<tr height="23" bgcolor=" #77eef8" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
											<td   colspan="9" align="right"> <strong>WO Sub Total : </strong></td>
											<td style="word-wrap: break-word;word-break: break-all;"  align="right"><strong><? echo $sub_data[$buyer_id][$wo_no]['delivery_qty'];// $color_data[('delivery_qty')]; ?></strong></td>
											<td style="word-wrap: break-word;word-break: break-all;" align="right"><strong>  <? echo number_format($sub_data[$buyer_id][$wo_no]['delivery_qty']/12,2);// $color_data[('delivery_qty')]/12; ?></strong></td>
											<td style="word-wrap: break-word;word-break: break-all;"  align="center"><strong>  <? echo $sub_data[$buyer_id][$wo_no]['sort_qty'];// $color_data[('sort_qty')]; ?></strong></td>															
										</tr>
								<?
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr height="25"  bgcolor=" #47b4ea">  
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ></td>
							<td ><strong>Grand total :</strong></td>							
							<td align="right"> <strong><? echo number_format($value_total_del_buyer,0,'.',''); ?> </strong></td>
							<td align="right"> <strong><? echo number_format($value_total_del_dzn_buyer,2,'.',''); ?> </strong></td>
							<td align="center"> <strong><? echo number_format($value_total_short_buyer,2,'.',''); ?> </strong></td>	
						</tr>
					</tfoot>
				</table>
			</div> 
			
		</fieldset>
	</div>	
		<?
				//--------------------------------------------------------end----------------------------------------
		?>
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

if($action=="report_generate_del_v_bill")
{ 
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$job_no=str_replace("'","",$txt_job_no);
	
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$year_id=str_replace("'","",$cbo_year);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	
	$cbo_mainbuyer_id=str_replace("'","",$cbo_mainbuyer_id);//Buyer_ID
	
	if($year_id!=0) $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_cond="";
	if($cbo_buyer_id!=0) $buyer_id_cond=" AND DM.PARTY_ID ='$cbo_buyer_id'"; else $buyer_id_cond="";
	
	if($cbo_company_id!=0) $cbo_company_id_cond=" AND DM.COMPANY_ID ='$cbo_company_id'"; else $cbo_company_id_cond="";
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and DM.JOB_NO = '$job_no' ";
	//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
	if ($txt_style_ref!='') $style_ref_cond=" AND PDM.STYLE_REF_NO like '%$txt_style_ref%'"; else $style_ref_cond="";
	//if(trim($txt_order_no)!="") $order_no_cond="%".trim($txt_order_no)."%"; else $order_no_cond="%%";
	if ($txt_order_no!='') $order_no_cond=" and DD.BUYER_PO_ID like '%$txt_order_no%'"; else $order_no_cond="";
	
	if($cbo_mainbuyer_id!=0) $cbo_mainbuyer_id_cond=" AND PDM.BUYER_NAME ='$cbo_mainbuyer_id'"; else $cbo_mainbuyer_id_cond="";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and DM.DELIVERY_DATE  between $txt_date_from and $txt_date_to";
	//echo $txt_date_from;echo "AND to date ".$txt_date_to;die;
	//echo $cbo_mainbuyer_id_cond;die;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$gmt_item_Arr = return_library_array("select id,ITEM_NAME from  LIB_GARMENT_ITEM ","id","ITEM_NAME");
	$body_part_Arr = return_library_array("select id,BODY_PART_FULL_NAME from  LIB_BODY_PART ","id","BODY_PART_FULL_NAME");
	
	$buyer_po_Arr = return_library_array("select id,PO_NUMBER from WO_PO_BREAK_DOWN ","id","PO_NUMBER");
	$location_Arr = return_library_array("SELECT ID, LOCATION_NAME FROM LIB_LOCATION ","id","LOCATION_NAME");
	$Style_Arr = return_library_array("SELECT a.ID, b.STYLE_REF_NO FROM WO_PO_BREAK_DOWN a INNER JOIN WO_PO_DETAILS_MASTER b ON a.JOB_NO_MST = b.JOB_NO","id","STYLE_REF_NO");
	$ord_qty = return_library_array("SELECT d.BUYER_PO_ID as ID, SUM(d.ORDER_QUANTITY) * 12 AS ORDER_QUANTITY FROM SUBCON_ORD_DTLS d WHERE d.STATUS_ACTIVE = 1 AND d.IS_DELETED = 0 GROUP BY d.BUYER_PO_ID","ID","ORDER_QUANTITY");
	//$buyer_arr = return_library_array("SELECT   a.ID,  c.BUYER_NAME FROM WO_PO_BREAK_DOWN a INNER JOIN WO_PO_DETAILS_MASTER b ON a.JOB_NO_MST = b.JOB_NO
//INNER JOIN LIB_BUYER c ON b.BUYER_NAME = c.ID ","id","BUYER_NAME");
	
	?>
    <div>
		<?
		//--------------------------------------------------------Start----------------------------------------
			  
	$query = "SELECT DM.COMPANY_ID,  DM.LOCATION_ID,  DM.DELIVERY_DATE,  DM.DELIVERY_NO,  DM.PARTY_ID,  DM.JOB_NO,  DD.COLOR_SIZE_ID,  DD.BUYER_PO_ID,  DD.SORT_QTY,
			  DM.REMARKS,  DM.DELI_PARTY,  DM.DELI_PARTY_LOCATION,  DD.DELIVERY_STATUS,  DD.DELIVERY_QTY,  PBD.PO_NUMBER,  PDM.BUYER_NAME,  PDM.STYLE_REF_NO,  PDM.STYLE_DESCRIPTION,
			  OB.COLOR_ID,  OB.ORDER_ID,OB.EMBELLISHMENT_TYPE, OB.BODY_PART
				FROM SUBCON_DELIVERY_MST DM INNER JOIN SUBCON_DELIVERY_DTLS DD ON DM.ID = DD.MST_ID INNER JOIN WO_PO_BREAK_DOWN PBD ON PBD.ID = DD.BUYER_PO_ID
				INNER JOIN WO_PO_DETAILS_MASTER PDM ON PBD.JOB_NO_MST = PDM.JOB_NO INNER JOIN SUBCON_ORD_BREAKDOWN OB ON DD.COLOR_SIZE_ID = OB.ID
				WHERE DM.STATUS_ACTIVE = 1
				AND DD.STATUS_ACTIVE = 1
				$cbo_company_id_cond $style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond ORDER By PDM.BUYER_NAME, DD.BUYER_PO_ID ";
				
	$query_2 = "SELECT DM.COMPANY_ID,  DM.LOCATION_ID,  DM.DELIVERY_DATE,  DM.DELIVERY_NO,  DM.PARTY_ID,  DM.JOB_NO,  DM.DELI_PARTY,  DM.DELI_PARTY_LOCATION,  DD.DELIVERY_STATUS,
			  SUM(DD.DELIVERY_QTY) AS DELIVERY_QTY,  PDM.BUYER_NAME,  AVG(SUBCON_INBOUND_BILL_DTLS.RATE)         AS Avg_RATE,  SUM(SUBCON_INBOUND_BILL_DTLS.DELIVERY_QTY) AS Bill_QTY
			FROM SUBCON_DELIVERY_MST DM INNER JOIN SUBCON_DELIVERY_DTLS DD ON DM.ID = DD.MST_ID 
			INNER JOIN WO_PO_BREAK_DOWN PBD ON PBD.ID = DD.BUYER_PO_ID
			INNER JOIN WO_PO_DETAILS_MASTER PDM ON PBD.JOB_NO_MST = PDM.JOB_NO
			INNER JOIN SUBCON_ORD_BREAKDOWN OB 
			ON DD.COLOR_SIZE_ID = OB.ID
			LEFT JOIN SUBCON_INBOUND_BILL_DTLS
			ON DD.ID               = SUBCON_INBOUND_BILL_DTLS.DELIVERY_ID
			WHERE DM.STATUS_ACTIVE = 1
			AND DD.STATUS_ACTIVE   = 1 
			$cbo_company_id_cond $style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond 
			GROUP BY DM.COMPANY_ID,   DM.LOCATION_ID,  DM.DELIVERY_DATE,  DM.DELIVERY_NO,  DM.PARTY_ID,  DM.JOB_NO,  DM.DELI_PARTY,  DM.DELI_PARTY_LOCATION,  DD.DELIVERY_STATUS,  PDM.BUYER_NAME ";
	$query_1 = "SELECT DM.COMPANY_ID,  DM.LOCATION_ID,  DM.DELIVERY_DATE,  DM.DELIVERY_NO,  DM.PARTY_ID,  DM.JOB_NO,  DM.DELI_PARTY,  DM.DELI_PARTY_LOCATION,  DD.DELIVERY_STATUS,
				  SUM(DD.DELIVERY_QTY) AS DELIVERY_QTY,  PDM.BUYER_NAME,  AVG(SUBCON_INBOUND_BILL_DTLS.RATE) AS Avg_RATE,  SUM(SUBCON_INBOUND_BILL_DTLS.DELIVERY_QTY) AS Bill_QTY,  PDM.STYLE_REF_NO,
				  PBD.PO_NUMBER
				FROM SUBCON_DELIVERY_MST DM
				INNER JOIN SUBCON_DELIVERY_DTLS DD
				ON DM.ID = DD.MST_ID
				LEFT JOIN WO_PO_BREAK_DOWN PBD
				ON PBD.ID = DD.BUYER_PO_ID
				LEFT JOIN WO_PO_DETAILS_MASTER PDM
				ON PBD.JOB_NO_MST = PDM.JOB_NO
				LEFT JOIN SUBCON_ORD_BREAKDOWN OB
				ON DD.COLOR_SIZE_ID = OB.ID
				LEFT JOIN SUBCON_INBOUND_BILL_DTLS
				ON DD.ID               = SUBCON_INBOUND_BILL_DTLS.DELIVERY_ID
				WHERE DM.STATUS_ACTIVE = 1
				AND DD.STATUS_ACTIVE   = 1
				$cbo_company_id_cond $style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond 
				GROUP BY DM.COMPANY_ID,  DM.LOCATION_ID,  DM.DELIVERY_DATE,  DM.DELIVERY_NO,  DM.PARTY_ID,  DM.JOB_NO,  DM.DELI_PARTY,  DM.DELI_PARTY_LOCATION,  DD.DELIVERY_STATUS,  PDM.BUYER_NAME,
				  PDM.STYLE_REF_NO,  PBD.PO_NUMBER";
	//echo $query_1;die;
	$sql_data_query = sql_select($query_1);
	$countRecords = count($query); 
	//echo $sql_data_query;
	ob_start();
	$details_data=array();
	$delivery_data=array();

	foreach( $sql_data_query as $row)
	{
		//detail data in Array  

		//COMPANY_ID, LOCATION_ID,  DELIVERY_DATE,  DELIVERY_NO,  PARTY_ID, JOB_NO,  DELI_PARTY,  DELI_PARTY_LOCATION,  DELIVERY_STATUS, DELIVERY_QTY,  BUYER_NAME,  Avg_RATE,  Bill_QTY
			  
		
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("DELIVERY_NO")]]["DELI_PARTY"]=$row[csf(DELI_PARTY)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("DELIVERY_NO")]]["DELI_PARTY_LOCATION"]=$row[csf(DELI_PARTY_LOCATION)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("DELIVERY_NO")]]["DELIVERY_DATE"]=$row[csf(DELIVERY_DATE)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("DELIVERY_NO")]]["DELIVERY_NO"]=$row[csf(DELIVERY_NO)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("DELIVERY_NO")]]["JOB_NO"]=$row[csf(JOB_NO)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("DELIVERY_NO")]]["BUYER_NAME"]=$row[csf(BUYER_NAME)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("DELIVERY_NO")]]["PO_NUMBER"].=$row[csf("PO_NUMBER")].",";
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("DELIVERY_NO")]]["STYLE_REF_NO"]=$row[csf(STYLE_REF_NO)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("DELIVERY_NO")]]["DELIVERY_QTY"]+=$row[csf(DELIVERY_QTY)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("DELIVERY_NO")]]["PARTY_ID"]=$row[csf(PARTY_ID)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("DELIVERY_NO")]]["DELIVERY_STATUS"]=$row[csf(DELIVERY_STATUS)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("DELIVERY_NO")]]["Avg_RATE"]=$row[csf(Avg_RATE)];
		$delivery_data[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]][$row[csf("DELI_PARTY_LOCATION")]][$row[csf("DELIVERY_NO")]]["Bill_QTY"]+=$row[csf(Bill_QTY)];

		$sub_total_arr[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]]+=$row[csf(DELIVERY_QTY)];
		$sub_short_arr[$row[csf("BUYER_NAME")]][$row[csf("DELI_PARTY")]]+=$row[csf(Bill_QTY)];
		$grand_total_arr[$row[csf("BUYER_NAME")]]+=$row[csf(DELIVERY_QTY)];
		$grand_short_arr[$row[csf("BUYER_NAME")]]+=$row[csf(Bill_QTY)];
	}
	//echo "<pre>";print_r($details_data);die;	
	
	?>
	<div style="width:1350px; margin:0 auto;">
		<fieldset style="width:100%;">	
		    <table width="98%" cellpadding="0" cellspacing="0" id="caption">
	            <tr>  
	                <td align="center" width="100%" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">Screen Print Delivery Report </strong>
	                </td>
	            </tr>
				<tr>  
	                <td align="center" width="100%" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">&nbsp </strong>
	                </td>
	            </tr>
	        </table>
			<div style="width:1350px; align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1340"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" rules="all">
					<thead>
						<th style="word-wrap: break-word;word-break: break-all;" width="30" align="center">Sl </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">BUYER</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80" align="center">DEL DATE</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="120" align="center">DEL NO</th>		
						<th style="word-wrap: break-word;word-break: break-all;" width="115" align="center">Delivery Party </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="150" align="center">Del Place</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="150" align="center">PARTY </th>		
						<th style="word-wrap: break-word;word-break: break-all;" width="70" align="center">Del QTY</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="70" align="center">Bill QTY</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">Job No</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="150" align="center">Style Name</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">Buyer PO </th>
						<th style="word-wrap: break-word;word-break: break-all;"  align="center">Del Status</th>
							
					</thead>
				</table>
			</div>
			<div style="width:1350px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1340"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<?	
						//ORDER_ID, COLOR_ID, COMPANY_ID,  LOCATION_ID,  DELIVERY_DATE,  DELIVERY_NO,  PARTY_ID,  JOB_NO, COLOR_SIZE_ID, BUYER_PO_ID,  DD.SORT_QTY,
						//REMARKS,  DELI_PARTY,  DELI_PARTY_LOCATION,  DELIVERY_STATUS,  DELIVERY_QTY,  PO_NUMBER, BUYER_NAME,  STYLE_REF_NO
						$k=1;
						foreach($delivery_data as $buyer_id=>$buyer_data)
						{
							foreach($buyer_data as $del_party_id=>$del_party_data)
							{
								foreach($del_party_data as $del_party_location_id=>$dloc_data)
								{
									foreach($dloc_data as $del_no_id=>$dl_data)
									{
										if($dl_data[('Bill_QTY')]==0 || $dl_data[('Bill_QTY')]=="")
										{
											$bggcolor="#ff5233";
										}else $bggcolor="#FFFFFF";
													if ($k%2==0)  
														$bgcolor="#E9F3FF";
														else
														$bgcolor="#FFFFFF";
													//COMPANY_ID, LOCATION_ID,  DELIVERY_DATE,  DELIVERY_NO,  PARTY_ID, JOB_NO,  DELI_PARTY,  DELI_PARTY_LOCATION,  DELIVERY_STATUS, DELIVERY_QTY,  BUYER_NAME,  Avg_RATE,  Bill_QTY
													?>
														<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
															<td style="word-wrap: break-word;word-break: break-all;" width="30"> <? echo $k; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="100">  <? echo $buyerArr[$dl_data[('BUYER_NAME')]]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="80"> <? echo $dl_data[('DELIVERY_DATE')]; ?></td>														
															<td bgcolor="<? echo $bggcolor; ?>" style="word-wrap: break-word;word-break: break-all;" width="120">  <? echo $dl_data[('DELIVERY_NO')]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="115">  <? echo $companyArr[$dl_data[('DELI_PARTY')]]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="150"> <? echo $location_Arr[$dl_data[('DELI_PARTY_LOCATION')]]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="150">  <? echo $companyArr[$dl_data[('PARTY_ID')]]; ?></td>						
															<td style="word-wrap: break-word;word-break: break-all;" width="70" align="center">  <? echo $dl_data[('DELIVERY_QTY')]; ?></td>
															<td bgcolor="<? echo $bggcolor; ?>" style="word-wrap: break-word;word-break: break-all;" width="70" align="right">  <? echo $dl_data[('Bill_QTY')]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="100" align="center">  <? echo $dl_data[('JOB_NO')]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="150" align="left">  <? echo $dl_data[('STYLE_REF_NO')]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;" width="100" align="left">  <? echo $dl_data[('PO_NUMBER')]; ?></td>
															<td style="word-wrap: break-word;word-break: break-all;"  align="center">  <? echo $delivery_status[$dl_data[('DELIVERY_STATUS')]]; ?></td>
														</tr>
														<?
														$k++;
														$value_total_del_buyer+=$dl_data[('DELIVERY_QTY')];
														$value_total_short_buyer+=$dl_data[('Bill_QTY')];
									}
								}
									?>
										<!--	<tr bgcolor="#dddddd">   
												<td align="right" colspan="7" style="word-wrap: break-word;word-break: break-all;" width="30"> <strong><? echo  $companyArr[$dl_data[('DELI_PARTY')]]; ?> Total :</strong></td>	
												<td align="right" style="word-wrap: break-word;word-break: break-all;"> <strong>
												<?
													//echo $sub_total_arr[$dl_data[('BUYER_NAME')]][$dl_data[('DELI_PARTY')]]; 
												?></strong></td>
												<td align="right" style="word-wrap: break-word;word-break: break-all;"><strong>
												<? 
													//echo $sub_short_arr[$dl_data[('BUYER_NAME')]][$dl_data[('DELI_PARTY')]]; 
												?></strong>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;"></td>
												<td style="word-wrap: break-word;word-break: break-all;"></td>
											</tr> -->
											<?	
							}
							?>
											<tr bgcolor="#dddddd">   
												<td align="right" colspan="7" style="word-wrap: break-word;word-break: break-all;" width="30"> <strong><? echo $buyerArr[$dl_data[('BUYER_NAME')]]; ?> Total :</strong></td>	
												<td align="right" style="word-wrap: break-word;word-break: break-all;"> <strong>
												<?
													echo $grand_total_arr[$dl_data[('BUYER_NAME')]]; 
												?></strong></td>
												<td align="right" style="word-wrap: break-word;word-break: break-all;"><strong>
												<? 
													echo $grand_short_arr[$dl_data[('BUYER_NAME')]]; 
												?></strong>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;"></td>
												<td style="word-wrap: break-word;word-break: break-all;"></td>
												<td style="word-wrap: break-word;word-break: break-all;"></td>
												<td style="word-wrap: break-word;word-break: break-all;"></td>
											</tr>
											<?
						
							
						}
						?>
					</tbody>
					<tfoot>
						<tr bgcolor="#dddddd">   
							<td align="right" colspan="7" style="word-wrap: break-word;word-break: break-all;" width="30"> <strong>Grand Total :</strong></td>	
							<td style="word-wrap: break-word;word-break: break-all;" align="right" width="70" id="value_total_del_buyer"> <strong><? echo number_format($value_total_del_buyer,0,'.',''); ?> </strong></td>
							<td style="word-wrap: break-word;word-break: break-all;" align="right" id="value_total_short_buyer"> <strong><? echo number_format($value_total_short_buyer,0,'.',''); ?> </strong></td>	
							<td style="word-wrap: break-word;word-break: break-all;"></td>
							<td style="word-wrap: break-word;word-break: break-all;"></td>
							<td style="word-wrap: break-word;word-break: break-all;"></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>	
		<?
				//--------------------------------------------------------End----------------------------------------
		?>
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