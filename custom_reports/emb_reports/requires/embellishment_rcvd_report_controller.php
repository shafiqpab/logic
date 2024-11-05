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
	if($cbo_buyer_id!=0) $buyer_id_cond=" AND SMM.PARTY_ID ='$cbo_buyer_id'"; else $buyer_id_cond="";
	
	if($cbo_company_id!=0) $cbo_company_id_cond=" AND SMM.COMPANY_ID ='$cbo_company_id'"; else $cbo_company_id_cond="";
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and SMM.EMBL_JOB_NO = '$job_no' ";
	//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
	if ($txt_style_ref!='') $style_ref_cond=" AND PM.STYLE_REF_NO like '%$txt_style_ref%'"; else $style_ref_cond="";
	//if(trim($txt_order_no)!="") $order_no_cond="%".trim($txt_order_no)."%"; else $order_no_cond="%%";
	if ($txt_order_no!='') $order_no_cond=" and P.PO_NUMBER like '%$txt_order_no%'"; else $order_no_cond="";
	
	if($cbo_mainbuyer_id!=0) $cbo_mainbuyer_id_cond=" AND PM.BUYER_NAME ='$cbo_mainbuyer_id'"; else $cbo_mainbuyer_id_cond="";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and SMM.SUBCON_DATE  BETWEEN $txt_date_from and $txt_date_to";
	//echo $txt_date_from;echo "AND to date ".$txt_date_to;die;
	//echo $cbo_mainbuyer_id_cond;die;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$gmt_item_Arr = return_library_array("select id,ITEM_NAME from  LIB_GARMENT_ITEM ","id","ITEM_NAME");
	$body_part_Arr = return_library_array("select id,BODY_PART_FULL_NAME from  LIB_BODY_PART ","id","BODY_PART_FULL_NAME");
	
	$buyer_po_Arr = return_library_array("select id,PO_NUMBER from WO_PO_BREAK_DOWN ","id","PO_NUMBER");
	$Style_Arr = return_library_array("SELECT a.ID, b.STYLE_REF_NO FROM WO_PO_BREAK_DOWN a INNER JOIN WO_PO_DETAILS_MASTER b ON a.JOB_NO_MST = b.JOB_NO","id","STYLE_REF_NO");
	$ord_qty = return_library_array("SELECT d.BUYER_PO_ID as ID, SUM(d.ORDER_QUANTITY) * 12 AS ORDER_QUANTITY FROM SUBCON_ORD_DTLS d WHERE d.STATUS_ACTIVE = 1 AND d.IS_DELETED = 0 GROUP BY d.BUYER_PO_ID","ID","ORDER_QUANTITY");
	
	
	
$query_t = "SELECT SUM(CASE WHEN SMM.TRANS_TYPE = '1' THEN SMD.QUANTITY END) AS RECVD_QTY, SUM(CASE WHEN SMM.TRANS_TYPE = '2' THEN SMD.QUANTITY END)  AS ISSUE_QTY,
  SMM.SYS_NO AS TRANS_NO,  SMM.COMPANY_ID,  SMM.TRANS_TYPE,  SMM.LOCATION_ID,  SMM.PARTY_ID,  SMM.SUBCON_DATE AS TRANS_DATE,  SMM.CHALAN_NO,  SMD.EMB_NAME_ID,
  SMM.EMBL_JOB_NO,  SMD.BUYER_PO_ID,  SOB.COLOR_ID, SOB.SIZE_ID,SOB.ITEM_ID, SOB.EMBELLISHMENT_TYPE AS EMB_TYPE,  SOB.BODY_PART,  P.PO_NUMBER,  PM.BUYER_NAME,  PM.STYLE_REF_NO
FROM SUB_MATERIAL_MST SMM INNER JOIN SUB_MATERIAL_DTLS SMD ON SMD.MST_ID = SMM.ID INNER JOIN SUBCON_ORD_BREAKDOWN SOB ON SMD.JOB_BREAK_ID = SOB.ID
INNER JOIN WO_PO_BREAK_DOWN P ON SMD.BUYER_PO_ID = P.ID INNER JOIN WO_PO_DETAILS_MASTER PM ON P.JOB_NO_MST         = PM.JOB_NO
WHERE SMD.STATUS_ACTIVE = 1
AND SMM.STATUS_ACTIVE   = 1
$cbo_company_id_cond $style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond
GROUP BY SMM.SYS_NO,  SMM.COMPANY_ID,  SMM.TRANS_TYPE,  SMM.LOCATION_ID,  SMM.PARTY_ID,  SMM.SUBCON_DATE,  SMM.CHALAN_NO,  SMD.EMB_NAME_ID,  SMM.EMBL_JOB_NO,
  SMD.BUYER_PO_ID,  SOB.COLOR_ID,  SOB.SIZE_ID,SOB.ITEM_ID, SOB.EMBELLISHMENT_TYPE,  SOB.BODY_PART,  SMD.JOB_DTLS_ID,  P.PO_NUMBER,  PM.BUYER_NAME,  PM.STYLE_REF_NO
ORDER BY TRANS_DATE DESC"; 
//echo $query_t;
	
	$sql_data_query = sql_select($query_t);
	ob_start();	$details_data=array();
	foreach( $sql_data_query as $row)
	{  
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["TRANS_DATE"]=$row[csf(TRANS_DATE)];
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["TRANS_NO"]=$row[csf(TRANS_NO)];
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["TRANS_TYPE"]=$row[csf(TRANS_TYPE)];
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["CHALAN_NO"]=$row[csf(CHALAN_NO)];			
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["PO_NUMBER"]=$row[csf(PO_NUMBER)];
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["PARTY_ID"]=$row[csf(PARTY_ID)];
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["PARTY_NAME"]=$row[csf(PARTY_NAME)];
		
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["STYLE_REF_NO"]=$row[csf(STYLE_REF_NO)];
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["COLOR_NAME"]=$row[csf(COLOR_ID)];
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["ITEM_ID"]=$row[csf(ITEM_ID)];
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["SIZE_NAME"]=$row[csf(SIZE_ID)];
		
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["COMPANY_NAME"]=$row[csf(COMPANY_NAME)];
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["BUYER_NAME"]=$row[csf(BUYER_NAME)];
				
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["EMB_TYPE"]=$row[csf(EMB_TYPE)];
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["BODY_PART"]=$row[csf(BODY_PART)];
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["RECVD_QTY"]+=$row[csf(RECVD_QTY)];
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["ISSUE_QTY"]+=$row[csf(ISSUE_QTY)];
		$details_data[$row[csf("TRANS_TYPE")]][$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("SIZE_ID")]]["EMBL_JOB_NO"]=$row[csf(EMBL_JOB_NO)];

	}
	//echo "<pre>";print_r($details_data);die;	
	?>
	<div style="width:1290px; margin:0 auto;">
		<fieldset style="width:100%;">	
		    <table width="98%" cellpadding="0" cellspacing="0" id="caption">
	            <tr>  
	                <td align="center" width="100%" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">Screen Print Order Details</strong>
	                </td>
	            </tr>
	        </table>
			<div style="width:1290px; align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1280"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" rules="all">
					<thead>
						<th style="word-wrap: break-word;word-break: break-all;" width="30" align="center">Sl </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="60" align="center">TRANS DATE </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="115" align="center">TRANS NO </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="70" align="center">TRANS TYPE </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80" align="center">CHALAN</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">PO</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="120" align="center">STYLE</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="120" align="center">COLOR</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">ITEM</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="55" align="center">SIZE</th>
						
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">BUYER</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">EMB_TYPE</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">BODY_PART</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="70" align="center">RECVD QTY</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="65" align="center">ISSUE QTY</th>
											
					</thead>
				</table>
			</div>
			<div style="width:1290px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1280"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<?	
						$k=1;
					foreach($details_data as $type_id=>$type_data)
						{
						foreach($type_data as $company_id=>$company_data)
						{
							foreach($company_data as $party_id=>$party_data)
							{
								foreach($party_data as $po_id=>$po_data)
								{
									foreach($po_data as $color_id=>$color_data)
									{
										foreach($color_data as $size_id=>$size_data)
										{
											if ($k%2==0)  
											$bgcolor="#E9F3FF";
											else
											$bgcolor="#FFFFFF";
										?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
												<td style="word-wrap: break-word;word-break: break-all;" width="30"> <? echo $k; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="60"> <? echo $size_data[('TRANS_DATE')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="115">  <? echo $size_data[('TRANS_NO')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="70">  
												<? 
													$t_type = "";
													if ($size_data[('TRANS_TYPE')]==1)
														$t_type = "Receive" ;
													else $t_type = "Issue";	
													echo $t_type; ?>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" width="80"> <? echo $size_data[('CHALAN_NO')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="100">  <? echo $size_data[('PO_NUMBER')]; ?></td>
												
												<td style="word-wrap: break-word;word-break: break-all;" width="120">  <? echo $size_data[('STYLE_REF_NO')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="120">  <? echo $colorArr[$size_data[('COLOR_NAME')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85">  <? echo $gmt_item_Arr[$size_data[('ITEM_ID')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="55">  <? echo $itemSizeArr [$size_data[('SIZE_NAME')]]; ?></td>
												
												<td style="word-wrap: break-word;word-break: break-all;" width="100"> <? echo $buyerArr[$size_data[('BUYER_NAME')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="85">  <? echo $emblishment_print_type[$size_data[('EMB_TYPE')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">  <? echo $body_part_Arr[$size_data[('BODY_PART')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="70" align="right">  <? echo $size_data[('RECVD_QTY')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="65" align="right">  <? echo $size_data[('ISSUE_QTY')]; ?></td>
												
											</tr>
											<?
											$k++;
											$total_rcvd_qty+=$size_data[('RECVD_QTY')];
											$total_issue_qty+=$size_data[('ISSUE_QTY')];
											
										}
									}
								}
							}
						}
					}
						?>
					</tbody>
				</table>
			</div>
			<table width="1280" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<tfoot>
	                <tr bgcolor="#dddddd">   
						<td style="word-wrap: break-word;word-break: break-all;" width="30"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="60"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="115"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="70"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
						
						<td style="word-wrap: break-word;word-break: break-all;" width="120"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="120"> </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="85"> </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="55"> </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="100"> </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="85" >Grand Total :</td>
						<td style="word-wrap: break-word;word-break: break-all;" width="85" ><? //echo number_format($total_rate,2,'.',''); ?></td>
						<td style="word-wrap: break-word;word-break: break-all;" align="right" width="70" id="value_total_rcvd"><? echo number_format($total_rcvd_qty,2,'.',''); ?></td>
						<td style="word-wrap: break-word;word-break: break-all;" align="right" width="65" id="value_total_issue"><? echo number_format($total_issue_qty,2,'.',''); ?></td>
						
	                </tr>
	            </tfoot>
            </table>
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
	if($cbo_buyer_id!=0) $buyer_id_cond=" AND SMM.PARTY_ID ='$cbo_buyer_id'"; else $buyer_id_cond="";
	
	if($cbo_company_id!=0) $cbo_company_id_cond=" AND SMM.COMPANY_ID ='$cbo_company_id'"; else $cbo_company_id_cond="";
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and SMM.EMBL_JOB_NO = '$job_no' ";
	//if($txt_style_ref!="") $style_ref_cond="%".trim($txt_style_ref)."%"; else $style_ref_cond="%%";
	if ($txt_style_ref!='') $style_ref_cond=" AND PM.STYLE_REF_NO like '%$txt_style_ref%'"; else $style_ref_cond="";
	//if(trim($txt_order_no)!="") $order_no_cond="%".trim($txt_order_no)."%"; else $order_no_cond="%%";
	if ($txt_order_no!='') $order_no_cond=" and P.PO_NUMBER like '%$txt_order_no%'"; else $order_no_cond="";
	
	if($cbo_mainbuyer_id!=0) $cbo_mainbuyer_id_cond=" AND PM.BUYER_NAME ='$cbo_mainbuyer_id'"; else $cbo_mainbuyer_id_cond="";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and SMM.SUBCON_DATE  between $txt_date_from and $txt_date_to";
	//echo $txt_date_from;echo "AND to date ".$txt_date_to;die;
	//echo $cbo_mainbuyer_id_cond;die;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$gmt_item_Arr = return_library_array("select id,ITEM_NAME from  LIB_GARMENT_ITEM ","id","ITEM_NAME");
	$body_part_Arr = return_library_array("select id,BODY_PART_FULL_NAME from  LIB_BODY_PART ","id","BODY_PART_FULL_NAME");
	
	$buyer_po_Arr = return_library_array("select id,PO_NUMBER from WO_PO_BREAK_DOWN ","id","PO_NUMBER");
	$Style_Arr = return_library_array("SELECT a.ID, b.STYLE_REF_NO FROM WO_PO_BREAK_DOWN a INNER JOIN WO_PO_DETAILS_MASTER b ON a.JOB_NO_MST = b.JOB_NO","id","STYLE_REF_NO");
	$ord_qty = return_library_array("SELECT d.BUYER_PO_ID as ID, SUM(d.ORDER_QUANTITY) * 12 AS ORDER_QUANTITY FROM SUBCON_ORD_DTLS d WHERE d.STATUS_ACTIVE = 1 AND d.IS_DELETED = 0 GROUP BY d.BUYER_PO_ID","ID","ORDER_QUANTITY");
	//$buyer_arr = return_library_array("SELECT   a.ID,  c.BUYER_NAME FROM WO_PO_BREAK_DOWN a INNER JOIN WO_PO_DETAILS_MASTER b ON a.JOB_NO_MST = b.JOB_NO
//INNER JOIN LIB_BUYER c ON b.BUYER_NAME = c.ID ","id","BUYER_NAME");  INSERT_DATE
	
	?>
    <div>
		<? /* echo "Test";
		select lib_company.company_name, lib_company1.company_name as party_name, lib_buyer.buyer_name, lib_location.location_name, lib_garment_item.item_name, lib_color.color_name, lib_size.size_name, wo_po_break_down.po_number, wo_po_details_master.style_ref_no, mst.company_id, mst.location_id, mst.party_id, mst.receive_date, mst.delivery_date, mst.job_no_prefix_num, mst.job_no_mst, mst.order_no, mst.po_delivery_date, mst.order_id, mst.item_id, mst.color_id, mst.size_id, mst.qnty, mst.rate, mst.amount, mst.buyer_po_id, mst.gmts_item_id, mst.embl_type, mst.body_part from (select som.company_id, som.location_id, som.party_id, som.receive_date, som.delivery_date, som.job_no_prefix_num, sod.job_no_mst, sod.order_no, sod.delivery_date as po_delivery_date, sob.order_id, sob.item_id, sob.color_id, sob.size_id, sob.qnty, sob.rate, sob.amount, sod.buyer_po_id, sod.gmts_item_id, sod.embl_type, sod.body_part from subcon_ord_mst som inner join subcon_ord_dtls sod on som.id = sod.mst_id inner join subcon_ord_breakdown sob on sod.id = sob.mst_id where som.company_id = 3 and som.job_no_prefix_num = 11 and som.status_active = 1 ) mst inner join lib_company on mst.company_id = lib_company.id inner join lib_location on lib_location.id = mst.location_id inner join lib_garment_item on mst.item_id = lib_garment_item.id inner join lib_color on mst.color_id = lib_color.id inner join lib_size on mst.size_id = lib_size.id inner join wo_po_break_down on mst.buyer_po_id = wo_po_break_down.id inner join wo_po_details_master on wo_po_break_down.job_no_mst = wo_po_details_master.job_no inner join lib_buyer on lib_buyer.id = wo_po_details_master.buyer_name inner join lib_company lib_company1 on lib_company1.id = mst.party_id 
		
		$style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond
		
		*/
		//--------------------------------------------------------Start----------------------------------------
	
			  
	$query_t = "SELECT SUM(CASE WHEN SMM.TRANS_TYPE = '1' THEN SMD.QUANTITY END) AS RECVD_QTY, SUM(CASE WHEN SMM.TRANS_TYPE = '2' THEN SMD.QUANTITY END)  AS ISSUE_QTY,
			  SMM.SYS_NO AS TRANS_NO,  SMM.COMPANY_ID,  SMM.TRANS_TYPE,  SMM.LOCATION_ID,  SMM.PARTY_ID,   SMM.SUBCON_DATE AS TRANS_DATE, SMM.INSERT_DATE AS INSERT_DATE, SMM.CHALAN_NO,  SMD.EMB_NAME_ID,
			  SMM.EMBL_JOB_NO,  SMD.BUYER_PO_ID,  SOB.COLOR_ID,  SOB.EMBELLISHMENT_TYPE AS EMB_TYPE,  SOB.BODY_PART,  P.PO_NUMBER,  PM.BUYER_NAME,  PM.STYLE_REF_NO
			FROM SUB_MATERIAL_MST SMM INNER JOIN SUB_MATERIAL_DTLS SMD ON SMD.MST_ID = SMM.ID INNER JOIN SUBCON_ORD_BREAKDOWN SOB ON SMD.JOB_BREAK_ID = SOB.ID
			INNER JOIN WO_PO_BREAK_DOWN P ON SMD.BUYER_PO_ID = P.ID INNER JOIN WO_PO_DETAILS_MASTER PM ON P.JOB_NO_MST = PM.JOB_NO
			WHERE SMD.STATUS_ACTIVE = 1
			AND SMM.STATUS_ACTIVE   = 1
			$cbo_company_id_cond $style_ref_cond $order_no_cond $job_no_cond $date_cond $buyer_id_cond $cbo_mainbuyer_id_cond
			GROUP BY SMM.SYS_NO,  SMM.COMPANY_ID,  SMM.TRANS_TYPE,  SMM.LOCATION_ID,  SMM.PARTY_ID,  SMM.SUBCON_DATE,SMM.INSERT_DATE,  SMM.CHALAN_NO,  SMD.EMB_NAME_ID,  SMM.EMBL_JOB_NO,
			  SMD.BUYER_PO_ID,  SOB.COLOR_ID,  SOB.EMBELLISHMENT_TYPE,  SOB.BODY_PART,  SMD.JOB_DTLS_ID,  P.PO_NUMBER,  PM.BUYER_NAME,  PM.STYLE_REF_NO
			ORDER BY TRANS_DATE DESC, SMM.SYS_NO";
			  
	
	//echo $query_t;die;
	$sql_data_query = sql_select($query_t);
	$countRecords = count($query_t); 
	//echo $sql_data_query;
	ob_start();
	$details_data=array();

	foreach( $sql_data_query as $row)
	{
		//detail data in Array
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["INSERT_DATE"]=$row[csf(INSERT_DATE)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["TRANS_DATE"]=$row[csf(TRANS_DATE)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["TRANS_NO"]=$row[csf(TRANS_NO)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["TRANS_TYPE"]=$row[csf(TRANS_TYPE)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["CHALAN_NO"]=$row[csf(CHALAN_NO)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["BUYER_PO_ID"]=$row[csf(BUYER_PO_ID)];				
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["PO_NUMBER"]=$row[csf(PO_NUMBER)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["PARTY_ID"]=$row[csf(PARTY_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["PARTY_NAME"]=$row[csf(PARTY_NAME)];
		
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["STYLE_REF_NO"]=$row[csf(STYLE_REF_NO)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["COLOR_ID"]=$row[csf(COLOR_ID)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["ITEM_NAME"]=$row[csf(ITEM_ID)];

		
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["COMPANY_NAME"]=$row[csf(COMPANY_NAME)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["BUYER_NAME"]=$row[csf(BUYER_NAME)];
				
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["EMB_TYPE"]=$row[csf(EMB_TYPE)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["BODY_PART"]=$row[csf(BODY_PART)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["RECVD_QTY"]+=$row[csf(RECVD_QTY)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["ISSUE_QTY"]+=$row[csf(ISSUE_QTY)];
		$details_data[$row[csf("COMPANY_ID")]][$row[csf("PARTY_ID")]][$row[csf("BUYER_PO_ID")]][$row[csf("COLOR_ID")]][$row[csf("TRANS_TYPE")]]["EMBL_JOB_NO"]=$row[csf(EMBL_JOB_NO)];

	}
	//echo "<pre>";print_r($details_data);die;	
	?>
	<div style="width:1350px; margin:0 auto;">
		<fieldset style="width:100%;">	
		    <table width="98%" cellpadding="0" cellspacing="0" id="caption">
	            <tr>  
	                <td align="center" width="100%" colspan="11" class="form_caption" >
	                	<strong style="font-size:18px">Screen Print Receive Issue Report </strong>
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
						<th style="word-wrap: break-word;word-break: break-all;" width="100" align="center">PO</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="60" align="center">T_DATE </th>
                        <th style="word-wrap: break-word;word-break: break-all;" width="60" align="center">Time </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="115" align="center">TRANS NO </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="50" align="center">T_TYPE </th>
						<th style="word-wrap: break-word;word-break: break-all;" width="80" align="center">CHALAN</th>
						
						<th style="word-wrap: break-word;word-break: break-all;" width="150" align="center">PARTY</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="120" align="center">STYLE</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="120" align="center">COLOR</th>
						
						
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">EMB_TYPE</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="85" align="center">BODY_PART</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="70" align="center">RECVD QTY</th>
						<th style="word-wrap: break-word;word-break: break-all;" width="65" align="center">ISSUE QTY</th>
											
					</thead>
				</table>
			</div>
			<div style="width:1350px;" align="center" style="max-height:500px; overflow-y:scroll;" id="scroll_body">	
				<table width="1340"  class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
					<tbody>
						<?	
						$k=1;
						foreach($details_data as $company_id=>$company_data)
						{
							foreach($company_data as $party_id=>$party_data)
							{
								foreach($party_data as $po_id=>$po_data)
								{
									foreach($po_data as $color_id=>$color_data)
									{
										foreach($color_data as $size_id=>$size_data)
										{
											if ($k%2==0)  
											$bgcolor="#E9F3FF";
											else
											$bgcolor="#FFFFFF";
										?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k;?>">
												<td style="word-wrap: break-word;word-break: break-all;" width="30"> <? echo $k; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="100"> <? echo $buyerArr[$size_data[('BUYER_NAME')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="100">  <? echo $buyer_po_Arr[$size_data[('BUYER_PO_ID')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="60"> <? echo $size_data[('TRANS_DATE')]; ?></td>
                                                <td style="word-wrap: break-word;word-break: break-all;" width="60"> <? 
 												$dateTime = DateTime::createFromFormat('d-M-y h.i.s A',$size_data[('INSERT_DATE')]);
 												$time12Hour = $dateTime->format('h:i:s A');
 												echo $time12Hour; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="115">  <? echo $size_data[('TRANS_NO')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="50">  
												<? 
													$t_type = "";
													if ($size_data[('TRANS_TYPE')]==1)
														$t_type = "Receive" ;
													else $t_type = "Issue";	
													echo $t_type; ?>
												</td>
												<td style="word-wrap: break-word;word-break: break-all;" width="80"> <? echo $size_data[('CHALAN_NO')]; ?></td>
		
												<td style="word-wrap: break-word;word-break: break-all;" width="150">  <? echo $companyArr[$size_data[('PARTY_ID')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="120">  <? echo $size_data[('STYLE_REF_NO')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="120">  <? echo $colorArr[$size_data[('COLOR_ID')]]; ?></td>
												
												
												<td style="word-wrap: break-word;word-break: break-all;" width="85">  <? echo $emblishment_print_type[$size_data[('EMB_TYPE')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="80" align="right">  <? echo $body_part_Arr[$size_data[('BODY_PART')]]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="70" align="right">  <? echo $size_data[('RECVD_QTY')]; ?></td>
												<td style="word-wrap: break-word;word-break: break-all;" width="65" align="right">  <? echo $size_data[('ISSUE_QTY')]; ?></td>
												
											</tr>
											<?
											$k++;
											$total_rcvd_qty+=$size_data[('RECVD_QTY')];
											$total_issue_qty+=$size_data[('ISSUE_QTY')];
											
										}
									}
								}
							}
						}
						?>
					</tbody>
				</table>
			</div>
			<table width="1340" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<tfoot>
	                <tr bgcolor="#dddddd">   
						<td style="word-wrap: break-word;word-break: break-all;" width="30"></td>	
						<td style="word-wrap: break-word;word-break: break-all;" width="100"> </td>
						<td style="word-wrap: break-word;word-break: break-all;" width="100"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="60"></td>
                        <td style="word-wrap: break-word;word-break: break-all;" width="60"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="115"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="50"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="80"></td>
						
						<td style="word-wrap: break-word;word-break: break-all;" width="150"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="120"></td>
						<td style="word-wrap: break-word;word-break: break-all;" width="120"> </td>

					
						<td style="word-wrap: break-word;word-break: break-all;" width="85" >Grand Total :</td>
						<td style="word-wrap: break-word;word-break: break-all;" width="85" ><? //echo number_format($total_rate,2,'.',''); ?></td>
						<td style="word-wrap: break-word;word-break: break-all;" align="right" width="70" id="value_total_rcvd_qty"><? echo number_format($total_rcvd_qty,2,'.',''); ?></td>
						<td style="word-wrap: break-word;word-break: break-all;" align="right" width="65" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></td>
	                </tr>
	            </tfoot>
						
            </table>
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