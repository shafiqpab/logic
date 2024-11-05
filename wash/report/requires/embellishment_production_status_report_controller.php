<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location--", $selected, "",0 );
	exit();     	 
}

if ($action=="load_drop_down_party_location")
{
	echo create_drop_down( "cbo_party_location_id", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location--", $selected, "",0 );
	exit();     	 
}

if ($action=="load_drop_down_party")
{
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_id", 125, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "load_drop_down( 'requires/embellishment_production_status_report_controller', this.value, 'load_drop_down_party_location', 'party_location_td' );");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_party_id", 125, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 125, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
	exit();
}



//job search.......................................................
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
			load_drop_down( 'embellishment_production_status_report_controller', company+'_'+within_group+'_'+party, 'load_drop_down_party', 'party_td' );
			
			$('#cbo_party_id').attr('disabled','disabled');
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
                            <td id="party_td"><? echo create_drop_down( "cbo_party_id", 140, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,"$('#search_by_td').html($(this).find('option:selected').text());$('#txt_search_string').val('');",0 );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[2];?>, 'job_search_list_view', 'search_div', 'embellishment_production_status_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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

if($action=="job_search_list_view")
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
			else if($search_by==2) $search_com_cond="and b.po_number='$search_str'";
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond=" and d.po_number = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str%'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '%$search_str%'";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '%$search_str%'";    
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '$search_str%'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '$search_str%'";  
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '%$search_str'";  
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
	 /*$sql= "select a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $color_id_str as color_id, b.buyer_po_id  
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c ,wo_po_break_down d 
	 where a.entry_form=204 and a.embellishment_job=b.job_no_mst and b.booking_dtls_id!=0 and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond and b.id=c.mst_id 
	 and b.buyer_po_id=d.id  
	 group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, b.buyer_po_id
	 order by a.id DESC";*/
	 
	$sql="select a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $color_id_str as color_id, b.buyer_po_id 
  from  subcon_ord_mst a, subcon_ord_breakdown c ,subcon_ord_dtls b
  left join wo_po_break_down d on b.buyer_po_id=d.id and d.is_deleted=0 and d.status_active=1
  left join  wo_po_details_master e on d.job_no_mst=e.job_no and d.is_deleted=0 and d.status_active=1	
	where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and a.entry_form=204 $company $party $job_cond $year_field_cond $search_com_cond $withinGroup  and a.is_deleted =0 group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, b.buyer_po_id";
	 
	 

	 //echo $sql;

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

//Buyer PO search.......................................................
if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	list($company_id,$party_id,$within_group,$buyer_id,$job_no,$year)=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,within_group,party)
		{
			load_drop_down( 'embellishment_production_status_report_controller', company+'_'+within_group+'_'+party, 'load_drop_down_party', 'party_td' );
			
			$('#cbo_party_id').attr('disabled','disabled');
		}
		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $company_id;?>,<? echo $within_group;?>,<? echo $party_id;?>)">
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
                            <td><input type="hidden" id="selected_id">  
								<?   
									echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1 );
                                ?>
                            </td>
                            <td id="party_td"><? echo create_drop_down( "cbo_party_id", 140, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,"$('#search_by_td').html($(this).find('option:selected').text());$('#txt_search_string').val('');",0 );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $within_group;?>+'_'+<? echo $year;?>, 'order_search_list_view', 'search_div', 'embellishment_production_status_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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

if($action=="order_search_list_view")
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
	$data=explode('_',$data);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	$year =$data[8];
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $party=" and a.party_id='$data[1]'"; else { $party="";}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and b.po_number='$search_str'";
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond=" and d.po_number = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str%'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '%$search_str%'";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '%$search_str%'";    
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '$search_str%'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '$search_str%'";  
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '%$search_str'";  
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
		$year_field="year(a.insert_date) as year"; 
		if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year";  else $year_field_cond="";
		$color_id_str="group_concat(c.color_id)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year"; else $year_field_cond="";
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
	}
	
	
	
	

	
	/*$sql="select  b.job_no_mst as job_no ,e.buyer_name,b.buyer_po_id, b.order_no, a.job_no_prefix_num as job_prefix, $year_field,d.po_number from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c ,wo_po_break_down d,wo_po_details_master e  where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and b.buyer_po_id=d.id and d.job_no_mst=e.job_no and a.entry_form=204 $company $party $job_cond $year_field_cond $search_com_cond  and a.is_deleted =0 group by d.po_number, e.buyer_name, b.job_no_mst,b.buyer_po_id, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";*/	
	
	$sql="select  e.style_ref_no,b.job_no_mst as job_no ,e.buyer_name,b.buyer_po_id, b.order_no, a.job_no_prefix_num as job_prefix, $year_field,d.po_number 
  from  subcon_ord_mst a, subcon_ord_breakdown c ,subcon_ord_dtls b
  left join wo_po_break_down d on b.buyer_po_id=d.id
  left join  wo_po_details_master e on    d.job_no_mst=e.job_no	
	where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and a.entry_form=204 $company $party $job_cond $year_field_cond $search_com_cond $withinGroup  and a.is_deleted =0 group by e.style_ref_no,d.po_number, e.buyer_name, b.job_no_mst,b.buyer_po_id, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";
	
	  //echo $sql;
	
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<table width="600" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="120">Work Order</th>
                <th width="120">Emb. Job no</th>
                <th width="100">Buyer</th>
                <th width="100">Buyer PO</th>
                <th>Year</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="600" border="1" rules="all" class="rpt_table">
			<? 
			$rows=sql_select($sql);
            $i=1;
            foreach($rows as $data)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $data[csf('buyer_po_id')].'_'.$data[csf('po_number')]; ?>')" style="cursor:pointer;">
                    <td width="30"><? echo $i; ?></td>
                    <td width="120"><p><? echo $data[csf('order_no')]; ?></p></td>
                    <td width="120"><p><? echo $data[csf('job_no')]; ?></p></td>
                    <td width="100"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
                    
                      <td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
                  
                    
                    
                    <td align="center"><p><? echo $data[csf('year')]; ?></p></td>
				</tr>
				<? $i++; 
			} ?>
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}


//Buyer Style search.......................................................

if ($action=="style_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	list($company_id,$party_id,$within_group,$buyer_id,$job_no,$year)=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,within_group,party)
		{
			load_drop_down( 'embellishment_production_status_report_controller', company+'_'+within_group+'_'+party, 'load_drop_down_party', 'party_td' );
			
			$('#cbo_party_id').attr('disabled','disabled');
		}
		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $company_id;?>,<? echo $within_group;?>,<? echo $party_id;?>)">
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
                            <td><input type="hidden" id="selected_id">  
								<?   
									echo create_drop_down( "cbo_company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1 );
                                ?>
                            </td>
                            <td id="party_td"><? echo create_drop_down( "cbo_party_id", 140, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,"$('#search_by_td').html($(this).find('option:selected').text());$('#txt_search_string').val('');",0 );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $within_group;?>+'_'+<? echo $year;?>, 'style_no_search_list_view', 'search_div', 'embellishment_production_status_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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

if($action=="style_no_search_list_view")
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
	$data=explode('_',$data);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	$year =$data[8];
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $party=" and a.party_id='$data[1]'"; else { $party="";}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and b.po_number='$search_str'";
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond=" and d.po_number = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str%'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '%$search_str%'";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '%$search_str%'";    
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '$search_str%'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '$search_str%'";  
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.embellishment_job like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str'";  
			else if ($search_by==3) $search_com_cond=" and e.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and d.po_number like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and e.style_ref_no like '%$search_str'";  
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
		$year_field="year(a.insert_date) as year"; 
		if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year";  else $year_field_cond="";
		$color_id_str="group_concat(c.color_id)";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year"; else $year_field_cond="";
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
	}
	
	
	
	

	
	/*$sql="select  e.style_ref_no,b.job_no_mst as job_no ,e.buyer_name,b.buyer_po_id, b.order_no, a.job_no_prefix_num as job_prefix, $year_field,d.po_number from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c ,wo_po_break_down d,wo_po_details_master e  where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and b.buyer_po_id=d.id and d.job_no_mst=e.job_no and a.entry_form=204 $company $party $job_cond $year_field_cond $search_com_cond  and a.is_deleted =0 group by e.style_ref_no,d.po_number, e.buyer_name, b.job_no_mst,b.buyer_po_id, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";*/
	
	$sql="select  e.style_ref_no,b.job_no_mst as job_no ,e.buyer_name,b.buyer_po_id, b.order_no, a.job_no_prefix_num as job_prefix, $year_field,d.po_number 
  from  subcon_ord_mst a, subcon_ord_breakdown c ,subcon_ord_dtls b
  left join wo_po_break_down d on b.buyer_po_id=d.id
  left join  wo_po_details_master e on    d.job_no_mst=e.job_no	
	where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and a.entry_form=204 $company $party $job_cond $year_field_cond $search_com_cond $withinGroup  and a.is_deleted =0 group by e.style_ref_no,d.po_number, e.buyer_name, b.job_no_mst,b.buyer_po_id, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";
		
	   //echo $sql;
	
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<table width="700" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="35">SL</th>
                <th width="100">Work Order</th>
                <th width="120">Emb. Job no</th>
                <th width="120">Style Ref</th>
                <th width="100">Buyer</th>
                <th width="100">Buyer PO</th>
                <th>Year</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="700" border="1" rules="all" class="rpt_table">
			<? 
			$rows=sql_select($sql);
            $i=1;
            foreach($rows as $data)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $data[csf('style_ref_no')]; ?>')" style="cursor:pointer;">
                    <td width="35"><? echo $i; ?></td>
                    <td width="100"><p><? echo $data[csf('order_no')]; ?></p></td>
                    <td width="120"><p><? echo $data[csf('job_no')]; ?></p></td>
                    <td width="120"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
                    <td width="100"><p><? echo $buyer_arr[$data[csf('buyer_name')]]; ?></p></td>
                    <td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
                    <td align="center"><p><? echo $data[csf('year')]; ?></p></td>
				</tr>
				<? $i++; 
			} ?>
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}



//Generat report.......................................................


if($action=="report_generate")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_wo_order_no=str_replace("'","",trim($txt_wo_order_no));
	$cbo_year=str_replace("'","",$cbo_year);
	
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");


	if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and a.company_id=$cbo_company_id";
	
	if(str_replace("'","",$cbo_buyer_id)==0)$buyer_con="";else $buyer_con=" and e.buyer_name=$cbo_buyer_id";
	if(str_replace("'","",$cbo_party_id)==0)$party_con="";else $party_con=" and a.party_id=$cbo_party_id";
	if(str_replace("'","",$cbo_location_id)==0)$location_con="";else $location_con=" and a.location_id =$cbo_location_id";
	
	
	
	if(str_replace("'","",$cbo_party_location_id)==0)$party_location_con="";else $party_location_con=" and a.party_location =$cbo_party_location_id";
	
	
	
	if(str_replace("'","",$txt_buyer_po)=='')$po_con="";else $po_con=" and d.po_number = $txt_buyer_po";
	if(str_replace("'","",$txt_buyer_po_id)==0)$po_id_con="";else $po_id_con=" and d.id = $txt_buyer_po_id";
	if(str_replace("'","",$txt_style_ref)=='')$style_con="";else $style_con=" and e.style_ref_no like('%".trim(str_replace("'","",$txt_style_ref))."%')";
	if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	if(str_replace("'","",trim($txt_job_no))!="") $job_cond="and a.embellishment_job = $txt_job_no";  	
	if($txt_wo_order_no!="") $wo_cond="and a.order_no like '%".$txt_wo_order_no."'";  	
		
		
	if($db_type==0)
	{
		//$year_field="year(a.insert_date) as year"; 
		if($cbo_year!=0) $year_cond="and YEAR(a.insert_date)=$cbo_year";  else $year_field_cond="";
	}
	else if($db_type==2)
	{
		//$year_field="to_char(a.insert_date,'YYYY') as year";
		if($cbo_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_field_cond="";
		//$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
	}


	
/*	$sql="select
	a.job_no,a.order_id,a.buyer_po_id,b.qcpass_qty,b.color_size_id
	from subcon_embel_production_mst a,subcon_embel_production_dtls b where a.id=b.mst_id  and a.entry_form=222 $year_field_cond $date_con__ 
	 and a.company_id=$cbo_company_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	$pro_sql_result=sql_select($sql);
	foreach($pro_sql_result as $row)
	{
		//$poArr[$row[csf('buyer_po_id')]]=$row[csf('buyer_po_id')];
		$deliveryJobArr[$row[csf('job_no')]]=$row[csf('job_no')];
		//$embColorSizeArr[$row[csf('color_size_id')]]=$row[csf('color_size_id')];
	}*/
	
	$cbo_shiping_status=str_replace("'","",$cbo_shiping_status);
	if($cbo_shiping_status != 0){
		$sql="select a.job_no from subcon_delivery_mst a,subcon_delivery_dtls b where a.id=b.mst_id and b.delivery_status=$cbo_shiping_status  and a.entry_form=254 and a.within_group=$cbo_within_group and a.company_id=$cbo_company_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$pro_sql_result=sql_select($sql);
		foreach($pro_sql_result as $row)
		{
			$deliveryJobArr[$row[csf('job_no')]]=$row[csf('job_no')];
		}
	}

		

	
	//Emb. Order Entry..............................................................
	if($cbo_shiping_status != 0){
		$p=1;
		$job_chunk_arr=array_chunk($deliveryJobArr,999);
		foreach($job_chunk_arr as $job_arr)
		{
			if($p==1){$sql_con =" and (a.embellishment_job in('".implode("','",$job_arr)."')";} 
			else{$sql_con .=" or a.embellishment_job in('".implode("','",$job_arr)."')";}
			$p++;
		}
		$sql_con .=")";
	}
	else
	{
		$sql_con ="";
	}
	
	
	if($db_type==0) $group_concat="group_concat(c.id) as color_size_id";
	else if($db_type==2) $group_concat="listagg(c.id,',') within group (order by c.id) as color_size_id";
	
	
	$sql="select  b.id as po_id,a.embellishment_job as job_no , b.order_no,e.style_ref_no,e.buyer_name,b.buyer_po_id, a.job_no_prefix_num as job_prefix, d.po_number,b.body_part ,c.color_id,
	a.delivery_date, $group_concat,
	sum(c.qnty*12) as order_qty,
	sum((c.qnty*b.wastage)+c.qnty*12) as plan_qty
  from subcon_ord_mst a, subcon_ord_breakdown c ,subcon_ord_dtls b
	left join wo_po_break_down d on b.buyer_po_id=d.id and d.is_deleted=0 and d.status_active=1
	left join wo_po_details_master e on d.job_no_mst=e.job_no and e.is_deleted=0 and e.status_active=1
 
	where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and a.entry_form=204 and a.within_group=$cbo_within_group 
	$sql_con $job_cond $wo_cond $year_cond $party_con $buyer_con $location_con $party_location_con
	$po_con $po_id_con $style_con

	  and a.is_deleted =0 group by a.embellishment_job,e.style_ref_no,d.po_number, e.buyer_name, b.buyer_po_id, b.id,b.order_no, a.job_no_prefix_num,a.delivery_date, a.company_id,b.body_part,c.color_id";
	$sql_result=sql_select($sql);//
	foreach($sql_result as $row)
	{
		$totalOrderQtyPcs+=$row[csf('order_qty')];
		$totalPlanQtyPcs+=$row[csf('plan_qty')];
		
		$poArr[$row[csf('buyer_po_id')]]=$row[csf('buyer_po_id')];
		$embJobArr[$row[csf('job_no')]]=$row[csf('job_no')];
		
		
	}
	
	
	$p=1;
	$po_chunk_arr=array_chunk($poArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (po_break_down_id in(".implode(",",$po_arr).")";} 
		else{$sql_con .=" or po_break_down_id in(".implode(",",$po_arr).")";}
		$p++;
	}
	$sql_con .=")";
		
	$sql_order="select po_break_down_id,min(cutup_date) as min_shipment_date from wo_po_color_size_breakdown where is_deleted=0 and status_active=1 $sql_con group by po_break_down_id";
	$sql_order_result=sql_select($sql_order);
	foreach($sql_order_result as $row)
	{
		$buyerCutupDateArr[$row[csf('po_break_down_id')]]=$row[csf('min_shipment_date')];
	}
	
	
	
	
	//Receive & Issue................................................
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (a.embl_job_no in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or a.embl_job_no in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";
	
	
	$sql_receive_materials="select 
    a.embl_job_no,c.body_part,c.id as po_id,b.job_break_id,a.entry_form,
    sum(case when a.subcon_date = $txt_date_from then b.quantity else 0 end) as today_rec_qty,
    sum(case when a.subcon_date < $txt_date_from then b.quantity else 0 end) as prev_rec_qty,
    sum(b.quantity) as total_rec_qty
    from sub_material_mst a,sub_material_dtls b,subcon_ord_dtls c where a.id=b.mst_id and c.id=b.job_dtls_id $sql_con and a.entry_form in( 205,207) and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
    group by a.embl_job_no,c.body_part,c.id,b.job_break_id,a.entry_form";
	$sql_receive_materials_result=sql_select($sql_receive_materials);
	$materialReceiveDataArr=array();
	$materialIssueDataArr=array();
	foreach($sql_receive_materials_result as $row)
	{
		if($row[csf('entry_form')]==205){//205=Receive;
			$key=$row[csf('embl_job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('job_break_id')];
			$materialReceiveDataArr['today'][$key]=$row[csf('today_rec_qty')];
			$materialReceiveDataArr['prev'][$key]=$row[csf('prev_rec_qty')];
			$materialReceiveDataArr['total'][$key]=$row[csf('total_rec_qty')];
		}
		else if($row[csf('entry_form')]==207){//207=Issue;
			$key=$row[csf('embl_job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('job_break_id')];
			$materialIssueDataArr['today'][$key]=$row[csf('today_rec_qty')];
			$materialIssueDataArr['prev'][$key]=$row[csf('prev_rec_qty')];
			$materialIssueDataArr['total'][$key]=$row[csf('total_rec_qty')];
		}
	}
	

	
	//Production & Qc................................................
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (a.job_no in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or a.job_no in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";
	$sql_production="select a.job_no,c.body_part,c.id as po_id,b.color_size_id,a.entry_form,
	sum(case when b.production_date = $txt_date_from then b.qcpass_qty else 0 end) as today_rec_qty,
	sum(case when b.production_date < $txt_date_from then b.qcpass_qty else 0 end) as prev_rec_qty, 
	sum(b.qcpass_qty) as total_rec_qty
  from subcon_embel_production_mst a,subcon_embel_production_dtls b,subcon_ord_dtls c 
  where a.id=b.mst_id and c.id=a.order_id $sql_con and a.entry_form in( 222,223)  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
   group by a.job_no,c.body_part ,c.id,b.color_size_id,a.entry_form";
	$sql_production_result=sql_select($sql_production);
	$productionDataArr=array();
	$QcDataArr=array();
	foreach($sql_production_result as $row)
	{
		if($row[csf('entry_form')]==222){//222=Production;
			$key=$row[csf('job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('color_size_id')];
			$productionDataArr['today'][$key]=$row[csf('today_rec_qty')];
			$productionDataArr['prev'][$key]=$row[csf('prev_rec_qty')];
			$productionDataArr['total'][$key]=$row[csf('total_rec_qty')];
		}
		if($row[csf('entry_form')]==223){//223=Qc;
			$key=$row[csf('job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('color_size_id')];
			$QcDataArr['today'][$key]=$row[csf('today_rec_qty')];
			$QcDataArr['prev'][$key]=$row[csf('prev_rec_qty')];
			$QcDataArr['total'][$key]=$row[csf('total_rec_qty')];
		}
	}
	
	
	//Delivery................................................
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (a.job_no in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or a.job_no in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";
	$sql_delivery="select a.job_no,c.body_part,c.id as po_id,b.color_size_id,
	sum(case when a.delivery_date = $txt_date_from then b.delivery_qty else 0 end) as today_delivery_qty,
	sum(case when a.delivery_date < $txt_date_from then b.delivery_qty else 0 end) as prev_delivery_qty, 
	sum(b.delivery_qty) as total_delivery_qty
  from subcon_delivery_mst a,subcon_delivery_dtls b,subcon_ord_dtls c 
  where a.id=b.mst_id and c.id=b.order_id $sql_con and a.entry_form=254 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
   group by a.job_no,c.body_part ,c.id,b.color_size_id";
	$sql_delivery_result=sql_select($sql_delivery);
	$deliveryDataArr=array();
	foreach($sql_delivery_result as $row)
	{
		$key=$row[csf('job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('color_size_id')];
		$deliveryDataArr['today'][$key]=$row[csf('today_delivery_qty')];
		$deliveryDataArr['prev'][$key]=$row[csf('prev_delivery_qty')];
		$deliveryDataArr['total'][$key]=$row[csf('total_delivery_qty')];
	}
	
	
	//Bill................................................
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (c.job_no_mst in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or c.job_no_mst in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";

	$sql_bill="select c.job_no_mst as job_no,c.body_part,c.id as po_id,b.color_size_id,
	sum(case when a.bill_date = $txt_date_from then b.delivery_qty else 0 end) as today_bill_qty,
	sum(case when a.bill_date < $txt_date_from then b.delivery_qty else 0 end) as prev_bill_qty, 
	sum(b.delivery_qty) as total_bill_qty,
	sum(b.amount) as total_bill_amount
  from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b,subcon_ord_dtls c 
  where a.id=b.mst_id and c.id=b.order_id $sql_con and b.process_id=13 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
   group by c.job_no_mst,c.body_part ,c.id,b.color_size_id";
	$sql_bill_result=sql_select($sql_bill);
	$billDataArr=array();
	foreach($sql_bill_result as $row)
	{
		$key=$row[csf('job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('color_size_id')];
		//$billDataArr['today'][$key]=$row[csf('today_bill_qty')];
		//$billDataArr['prev'][$key]=$row[csf('prev_bill_qty')];
		$billDataArr['total_qty'][$key]=$row[csf('total_bill_qty')];
		$billDataArr['total_amount'][$key]=$row[csf('total_bill_amount')];
	}
	
	
	
	
	
	ob_start();
	?>
    <div style="width:3220px"> 
        <table width="100%" cellspacing="0" >
            <tr style="border:none;">
                <td colspan="37" align="center" style="border:none; font-size:14px;">
                    <b><? echo $company_library[str_replace("'","",$cbo_company_id)]; ?></b>
                </td>
            </tr>
        </table>
        
        <div style="float:left; width:3220px">
            <table width="3200" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
                <thead>
                    <tr>
                        <th rowspan="2" width="35">SL</th>
                        <th rowspan="2" width="100">Emb. Job</th>
                        <th rowspan="2" width="100">Work Order</th>
                        <th rowspan="2" width="100">Buyer Name</th>
                        <th rowspan="2" width="130">Buyer PO</th>
                        <th rowspan="2" width="130">Style</th>
                        <th rowspan="2" width="100">Body Color</th>
                        <th rowspan="2" width="100">Body Part</th>
                        <th rowspan="2" width="80">Order Qty (Pcs)</th>
                        <th rowspan="2" width="80">Plan Qty</th>
                        <th rowspan="2" width="80">Buyer Ship Date</th>
                        <th rowspan="2" width="80">Delivery Date</th>
                        <th colspan="4">Cut Piece Received Status</th>
                        
                        <th colspan="4">Cut Panel Issue Status</th>
                        <th colspan="5">Print Status</th>
                        <th colspan="4">QC Status</th>
                        <th colspan="5">Delivery Status</th>
                        <th colspan="3">Bill Status</th>
                        
                    </tr>
                    <tr>
                        <th width="80">Previous Receive</th>
                        <th width="80">Today Receive</th>
                        <th width="80">Total Receive</th>
                        <th width="80">Recv Balance</th>
                        
                        <th width="80">Previous Issue</th>
                        <th width="80">Today Issue</th>
                        <th width="80">Total Issue</th>
                        <th width="80">Issue Balance</th>
                        
                        <th width="80">Previous Print</th>
                        <th width="80">Today Print</th>
                        <th width="80">Total Print</th>
                        <th width="80">Balance</th>
                        <th width="80">WIP</th>
                        
                        <th width="80">Previous QC</th>
                        <th width="80">Today QC</th>
                        <th width="80">Total QC</th>
                        <th width="80">QC Balance</th>
                        
                        <th width="80">Previous Delivery</th>
                        <th width="80">Today Delivery</th>
                        <th width="80">Total Delivery</th>
                        <th width="80">Delivery Balance</th>
                        <th width="80">Left Over Qty</th>
                        
                        <th width="80">Bill Qty</th>
                        <th width="80">Bill Amount</th>
                        <th>Balance Bill Qty</th>
                        
                    </tr>
                </thead>
            </table>
            <div style="max-height:350px; width:3220px; overflow-y:auto;" id="scroll_body" >
                <table cellspacing="0" border="1" class="rpt_table"  width="3200" rules="all" id="table_body" >
				<?  
					$i=1;
					
					foreach($sql_result as $row)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						
						$toDayMaterRec=0;$prevMaterRec=0;$totalMaterRec=0;
						$toDayMaterIssue=0;$prevMaterIssue=0;$totalMaterIssue=0;
						$toDayProduc=0;$prevProduc=0;$totalProduc=0;
						$toDayDelivery=0; $prevDelivery=0; $totalDelivery=0;
						$toDayQc=0;$prevQc=0;$totalQc=0;
						$totalBillQty=0;$totalBillAmount=0;
						
						foreach(explode(',',$row[csf('color_size_id')]) as $csi){
							$key=$row[csf('job_no')].$row[csf('po_id')].$row[csf('body_part')].$csi;
							
							//Receive.............................................
							$toDayMaterRec+=$materialReceiveDataArr['today'][$key];
							$prevMaterRec+=$materialReceiveDataArr['prev'][$key];
							$totalMaterRec+=$materialReceiveDataArr['total'][$key];
							
							$grand_toDayMaterRec+=$materialReceiveDataArr['today'][$key];
							$grand_prevMaterRec+=$materialReceiveDataArr['prev'][$key];
							$grand_totalMaterRec+=$materialReceiveDataArr['total'][$key];
							//Issue.............................................
							$toDayMaterIssue+=$materialIssueDataArr['today'][$key];
							$prevMaterIssue+=$materialIssueDataArr['prev'][$key];
							$totalMaterIssue+=$materialIssueDataArr['total'][$key];
							
							$grand_toDayMaterIssue+=$materialIssueDataArr['today'][$key];
							$grand_prevMaterIssue+=$materialIssueDataArr['prev'][$key];
							$grand_totalMaterIssue+=$materialIssueDataArr['total'][$key];
							
							
							//Production(print).............................................							
							$toDayProduc+=$productionDataArr['today'][$key];
							$prevProduc+=$productionDataArr['prev'][$key];	
							$totalProduc+=$productionDataArr['total'][$key];
							
							$grand_toDayProduc+=$productionDataArr['today'][$key];
							$grand_prevProduc+=$productionDataArr['prev'][$key];	
							$grand_totalProduc+=$productionDataArr['total'][$key];
							//Qc.............................................							
							$toDayQc+=$QcDataArr['today'][$key];
							$prevQc+=$QcDataArr['prev'][$key];	
							$totalQc+=$QcDataArr['total'][$key];
							
							$grand_toDayQc+=$QcDataArr['today'][$key];
							$grand_prevQc+=$QcDataArr['prev'][$key];	
							$grand_totalQc+=$QcDataArr['total'][$key];
							//Delivery.............................................							
							$toDayDelivery+=$deliveryDataArr['today'][$key];
							$prevDelivery+=$deliveryDataArr['prev'][$key];	
							$totalDelivery+=$deliveryDataArr['total'][$key];
							
							$grand_toDayDelivery+=$deliveryDataArr['today'][$key];
							$grand_prevQDelivery+=$deliveryDataArr['prev'][$key];	
							$grand_totalDelivery+=$deliveryDataArr['total'][$key];
							
							
							//Bill.............................................							
							$totalBillQty+=$billDataArr['total_qty'][$key];
							$totalBillAmount+=$billDataArr['total_amount'][$key];
							
							$grand_totalBillQty+=$billDataArr['total_qty'][$key];
							$grand_totalBillAmount+=$billDataArr['total_amount'][$key];
							
						}
						
						?>
						<tbody>
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                          
                            <td width="35" align="center"><? echo $i;?></td>
                            <td width="100"><? echo $row[csf('job_no')];?></td>
                            <td width="100"><? echo $row[csf('order_no')];?></td>
                            <td width="100"><p style="width:95px; word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
                            <td width="130"><p style="width:125px; word-break:break-all"><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="130"><p style="width:125px; word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></p></td>
                            <td width="100"><p style="width:95px; word-break:break-all"><? echo $color_library_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100"><p style="width:95px; word-break:break-all"><? echo $body_part[$row[csf('body_part')]]; ?></p></td>
                            <td width="80" align="right"><? echo number_format($row[csf('order_qty')],2); ?></td>
                            <td width="80" align="right"><? echo number_format($row[csf('plan_qty')],2); ?></td>
                            <td width="80" align="center"><? echo change_date_format($buyerCutupDateArr[$row[csf('buyer_po_id')]]); ?></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
                           
                            <td align="right" width="80"><? echo number_format($prevMaterRec,2); ?></td>
                            <td align="right" width="80"><? echo number_format($toDayMaterRec,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalMaterRec,2); ?></td>
                            <td align="right" width="80"><? echo number_format($row[csf('plan_qty')]-$totalMaterRec,2); ?></td>
                            <td align="right" width="80"><? echo number_format($prevMaterIssue,2); ?></td>
                            <td align="right" width="80"><? echo number_format($toDayMaterIssue,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalMaterIssue,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalMaterRec-$totalMaterIssue,2); ?></td>
                            <td align="right" width="80"><? echo number_format($prevProduc,2); ?></td>
                            <td align="right" width="80"><? echo number_format($toDayProduc,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalProduc,2); ?></td>
                            <td align="right" width="80"><? echo number_format($row[csf('plan_qty')]-$totalProduc,2); ?></td>
                            <td align="right" width="80"><? echo number_format(($row[csf('plan_qty')]-$totalProduc)-($row[csf('plan_qty')]-$totalMaterRec),2);?></td>

                            <td align="right" width="80"><? echo number_format($prevQc,2); ?></td>
                            <td align="right" width="80"><? echo number_format($toDayQc,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalQc,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalProduc-$totalQc,2); ?></td>
                            
                            <td align="right" width="80"><? echo number_format($prevDelivery,2); ?></td>
                            <td align="right" width="80"><? echo number_format($toDayDelivery,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalDelivery,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalProduc-$totalDelivery,2); ?></td>
                            <td align="right" width="80"><? echo number_format(($row[csf('plan_qty')]-$totalDelivery),2);?></td>
                           
                            <td align="right" width="80"><? echo number_format($totalBillQty,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalBillAmount,2); ?></td>
                            <td align="right"><? echo number_format($row[csf('plan_qty')]-$totalBillQty,2); ?></td>
						  </tr>
                        </tbody> 	
						<?		
					$i++;
					}
                  ?>
                </table>
                <table width="3200" border="1" class="tbl_bottom" rules="all" id="report_table_footer" >
                    <tfoot>
                    <tr> 
                        <td width="35"></td>
                        <td width="100"></td>
                        <td width="100"></td>
                        <td width="100"></td>
                        <td width="130"></td>
                        <td width="130"></td>
                        <td width="100"> </td>
                        <td width="100"> </td>
                        <td width="80" align="right" id="gt_order_qty_id"><? echo number_format($totalOrderQtyPcs,2);?></td>
                        <td width="80" align="right" id="gt_plan_qty_id"><? echo number_format($totalPlanQtyPcs,2);?></td>
                        <td width="80"></td>
                        <td width="80"></td>
                       
                        <td width="80" align="right" id="gt_rec_prev_qty_id"><? echo number_format($grand_prevMaterRec,2);?></td>
                        <td width="80" align="right" id="gt_rec_today_qty_id"><? echo number_format($grand_toDayMaterRec,2);?></td>
                        <td width="80" align="right" id="gt_rec_total_qty_id"><? echo number_format($grand_totalMaterRec,2);?></td>
                        <td width="80" align="right" id="gt_rec_bal_qty_id"><? echo number_format($totalPlanQtyPcs-$grand_totalMaterRec,2);?></td>
                        <td width="80" align="right" id="gt_issue_prev_qty_id"><? echo number_format($grand_prevMaterIssue,2);?></td>
                        <td width="80" align="right" id="gt_issue_today_qty_id"><? echo number_format($grand_toDayMaterIssue,2);?></td>
                        <td width="80" align="right" id="gt_issue_total_qty_id"><? echo number_format($grand_totalMaterIssue,2);?></td>
                        <td width="80" align="right" id="gt_issue_bal_qty_id"><? echo number_format($grand_totalMaterRec-$grand_totalMaterIssue,2);?></td>
                        
                        <td width="80" align="right" id="gt_print_prev_qty_id"><? echo number_format($grand_prevProduc,2);?></td>
                        <td width="80" align="right" id="gt_print_today_qty_id"><? echo number_format($grand_toDayProduc,2);?></td>
                        <td width="80" align="right" id="gt_print_total_qty_id"><? echo number_format($grand_totalProduc,2);?></td>
                        <td width="80" align="right" id="gt_print_bal_qty_id"><? echo number_format($totalPlanQtyPcs-$grand_totalProduc,2);?></td>
                        <td width="80" align="right" id="gt_print_wip_qty_id"><? echo number_format(($totalPlanQtyPcs-$grand_totalProduc)-($totalPlanQtyPcs-$grand_totalMaterRec),2);?></td>
                        
                        
                        <td width="80" align="right" id="gt_qc_prev_qty_id"><? echo number_format($grand_prevQc,2);?></td>
                        <td width="80" align="right" id="gt_qc_today_qty_id"><? echo number_format($grand_toDayQc,2);?></td>
                        <td width="80" align="right" id="gt_qc_total_qty_id"><? echo number_format($grand_totalQc,2);?></td>
                        <td width="80" align="right" id="gt_qc_bal_qty_id"><? echo number_format($grand_totalProduc-$grand_totalQc,2);?></td>
                        
                        
                        
                        
                        <td width="80" align="right" id="gt_delivery_prev_qty_id"><? echo number_format($grand_prevDelivery,2);?></td>
                        <td width="80" align="right" id="gt_delivery_today_qty_id"><? echo number_format($grand_toDayDelivery,2);?></td>
                        <td width="80" align="right" id="gt_delivery_total_qty_id"><? echo number_format($grand_totalDelivery,2);?></td>
                        <td width="80" align="right" id="gt_delivery_bal_qty_id"><? echo number_format($grand_totalProduc-$grand_totalDelivery,2);?></td>
                        <td width="80" align="right" id="gt_delivery_loq_qty_id"><? echo number_format(($totalPlanQtyPcs-$grand_totalDelivery),2);?></td>
                        
                        <td width="80" align="right" id="gt_bill_total_qty_id"><? echo number_format($grand_totalBillQty,2);?></td>
                        <td width="80" align="right" id="gt_bill_total_amount_id"><? echo number_format($grand_totalBillAmount,2);?></td>
                        <td align="right" id="gt_bill_bal_qty_id"><? echo number_format($totalPlanQtyPcs-$grand_totalBillQty,2);?></td>
                        
                        
                        
                	</tr>
                    </tfoot>
                </table>
             </div>
           </div>
     	
        </div><!-- end main div -->
		<?
	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	
	
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html**$filename";
	exit();
}



if($action=="report_generate_group_by_buyer_job")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_wo_order_no=str_replace("'","",trim($txt_wo_order_no));
	$cbo_year=str_replace("'","",$cbo_year);
	
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");


	if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and a.company_id=$cbo_company_id";
	
	if(str_replace("'","",$cbo_buyer_id)==0)$buyer_con="";else $buyer_con=" and e.buyer_name=$cbo_buyer_id";
	if(str_replace("'","",$cbo_party_id)==0)$party_con="";else $party_con=" and a.party_id=$cbo_party_id";
	if(str_replace("'","",$cbo_location_id)==0)$location_con="";else $location_con=" and a.location_id =$cbo_location_id";
	
	if(str_replace("'","",$cbo_party_location_id)==0)$party_location_con="";else $party_location_con=" and a.party_location =$cbo_party_location_id";
	
	
	if(str_replace("'","",$txt_buyer_po)=='')$po_con="";else $po_con=" and d.po_number = $txt_buyer_po";
	if(str_replace("'","",$txt_buyer_po_id)==0)$po_id_con="";else $po_id_con=" and d.id = $txt_buyer_po_id";
	if(str_replace("'","",$txt_style_ref)=='')$style_con="";else $style_con=" and e.style_ref_no like('%".trim(str_replace("'","",$txt_style_ref))."%')";
	if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	if(str_replace("'","",trim($txt_job_no))!="") $job_cond="and a.embellishment_job = $txt_job_no";  	
	if($txt_wo_order_no!="") $wo_cond="and a.order_no like '%".$txt_wo_order_no."'";  	
		
		
	if($db_type==0)
	{
		//$year_field="year(a.insert_date) as year"; 
		if($cbo_year!=0) $year_cond="and YEAR(a.insert_date)=$cbo_year";  else $year_field_cond="";
	}
	else if($db_type==2)
	{
		//$year_field="to_char(a.insert_date,'YYYY') as year";
		if($cbo_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_field_cond="";
		//$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
	}


	
/*	$sql="select
	a.job_no,a.order_id,a.buyer_po_id,b.qcpass_qty,b.color_size_id
	from subcon_embel_production_mst a,subcon_embel_production_dtls b where a.id=b.mst_id  and a.entry_form=222 $year_field_cond $date_con__ 
	 and a.company_id=$cbo_company_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	$pro_sql_result=sql_select($sql);
	foreach($pro_sql_result as $row)
	{
		//$poArr[$row[csf('buyer_po_id')]]=$row[csf('buyer_po_id')];
		$deliveryJobArr[$row[csf('job_no')]]=$row[csf('job_no')];
		//$embColorSizeArr[$row[csf('color_size_id')]]=$row[csf('color_size_id')];
	}*/
	
	$cbo_shiping_status=str_replace("'","",$cbo_shiping_status);
	if($cbo_shiping_status != 0){
		$sql="select a.job_no from subcon_delivery_mst a,subcon_delivery_dtls b where a.id=b.mst_id and b.delivery_status=$cbo_shiping_status  and a.entry_form=254 and a.within_group=$cbo_within_group and a.company_id=$cbo_company_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$pro_sql_result=sql_select($sql);
		foreach($pro_sql_result as $row)
		{
			$deliveryJobArr[$row[csf('job_no')]]=$row[csf('job_no')];
		}
	}

		

	
	//Emb. Order Entry..............................................................
	if($cbo_shiping_status != 0){
		$p=1;
		$job_chunk_arr=array_chunk($deliveryJobArr,999);
		foreach($job_chunk_arr as $job_arr)
		{
			if($p==1){$sql_con =" and (a.embellishment_job in('".implode("','",$job_arr)."')";} 
			else{$sql_con .=" or a.embellishment_job in('".implode("','",$job_arr)."')";}
			$p++;
		}
		$sql_con .=")";
	}
	else
	{
		$sql_con ="";
	}
	
	
	if($db_type==0) $group_concat="group_concat(c.id) as color_size_id";
	else if($db_type==2) $group_concat="listagg(c.id,',') within group (order by c.id) as color_size_id";
	
	
	$sql="select  b.id as po_id,a.embellishment_job as job_no , b.order_no,e.style_ref_no,e.buyer_name,b.buyer_po_id, a.job_no_prefix_num as job_prefix, d.po_number,b.body_part ,c.color_id,
	a.delivery_date, $group_concat,
	sum(c.qnty*12) as order_qty,
	sum((c.qnty*b.wastage)+c.qnty*12) as plan_qty
  from subcon_ord_mst a, subcon_ord_breakdown c ,subcon_ord_dtls b
	left join wo_po_break_down d on b.buyer_po_id=d.id and d.is_deleted=0 and d.status_active=1
	left join wo_po_details_master e on d.job_no_mst=e.job_no and e.is_deleted=0 and e.status_active=1
 
	where b.job_no_mst=a.embellishment_job and b.id=c.mst_id and a.entry_form=204 and a.within_group=$cbo_within_group 
	$sql_con $job_cond $wo_cond $year_cond $party_con $buyer_con $location_con $party_location_con
	$po_con $po_id_con $style_con

	  and a.is_deleted =0 group by a.embellishment_job,e.style_ref_no,d.po_number, e.buyer_name, b.buyer_po_id, b.id,b.order_no, a.job_no_prefix_num,a.delivery_date, a.company_id,b.body_part,c.color_id";
	$sql_result=sql_select($sql);//
	foreach($sql_result as $row)
	{
		$totalOrderQtyPcs+=$row[csf('order_qty')];
		$totalPlanQtyPcs+=$row[csf('plan_qty')];
		
		$poArr[$row[csf('buyer_po_id')]]=$row[csf('buyer_po_id')];
		$embJobArr[$row[csf('job_no')]]=$row[csf('job_no')];
		
		$buyerPoDataArr[$row[csf('buyer_name')]][$row[csf('buyer_po_id')]][]=$row;
		
	}
	
	
	$p=1;
	$po_chunk_arr=array_chunk($poArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (po_break_down_id in(".implode(",",$po_arr).")";} 
		else{$sql_con .=" or po_break_down_id in(".implode(",",$po_arr).")";}
		$p++;
	}
	$sql_con .=")";
		
	$sql_order="select po_break_down_id,min(cutup_date) as min_shipment_date from wo_po_color_size_breakdown where is_deleted=0 and status_active=1 $sql_con group by po_break_down_id";
	$sql_order_result=sql_select($sql_order);
	foreach($sql_order_result as $row)
	{
		$buyerCutupDateArr[$row[csf('po_break_down_id')]]=$row[csf('min_shipment_date')];
	}
	
	
	
	
	//Receive & Issue................................................
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (a.embl_job_no in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or a.embl_job_no in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";
	$sql_receive_materials="select 
    a.embl_job_no,c.body_part,c.id as po_id,b.job_break_id,a.entry_form,
    sum(case when a.subcon_date = $txt_date_from then b.quantity else 0 end) as today_rec_qty,
    sum(case when a.subcon_date < $txt_date_from then b.quantity else 0 end) as prev_rec_qty,
    sum(b.quantity) as total_rec_qty
    from sub_material_mst a,sub_material_dtls b,subcon_ord_dtls c where a.id=b.mst_id and c.id=b.job_dtls_id $sql_con and a.entry_form in( 205,207) and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
    group by a.embl_job_no,c.body_part,c.id,b.job_break_id,a.entry_form";
	$sql_receive_materials_result=sql_select($sql_receive_materials);
	$materialReceiveDataArr=array();
	$materialIssueDataArr=array();
	foreach($sql_receive_materials_result as $row)
	{
		if($row[csf('entry_form')]==205){//205=Receive;
			$key=$row[csf('embl_job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('job_break_id')];
			$materialReceiveDataArr['today'][$key]=$row[csf('today_rec_qty')];
			$materialReceiveDataArr['prev'][$key]=$row[csf('prev_rec_qty')];
			$materialReceiveDataArr['total'][$key]=$row[csf('total_rec_qty')];
		}
		else if($row[csf('entry_form')]==207){//207=Issue;
			$key=$row[csf('embl_job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('job_break_id')];
			$materialIssueDataArr['today'][$key]=$row[csf('today_rec_qty')];
			$materialIssueDataArr['prev'][$key]=$row[csf('prev_rec_qty')];
			$materialIssueDataArr['total'][$key]=$row[csf('total_rec_qty')];
		}
	}
	

	
	//Production & Qc................................................
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (a.job_no in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or a.job_no in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";
	$sql_production="select a.job_no,c.body_part,c.id as po_id,b.color_size_id,a.entry_form,
	sum(case when b.production_date = $txt_date_from then b.qcpass_qty else 0 end) as today_rec_qty,
	sum(case when b.production_date < $txt_date_from then b.qcpass_qty else 0 end) as prev_rec_qty, 
	sum(b.qcpass_qty) as total_rec_qty
  from subcon_embel_production_mst a,subcon_embel_production_dtls b,subcon_ord_dtls c 
  where a.id=b.mst_id and c.id=a.order_id $sql_con and a.entry_form in( 222,223)  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
   group by a.job_no,c.body_part ,c.id,b.color_size_id,a.entry_form";
	$sql_production_result=sql_select($sql_production);
	$productionDataArr=array();
	$QcDataArr=array();
	foreach($sql_production_result as $row)
	{
		if($row[csf('entry_form')]==222){//222=Production;
			$key=$row[csf('job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('color_size_id')];
			$productionDataArr['today'][$key]=$row[csf('today_rec_qty')];
			$productionDataArr['prev'][$key]=$row[csf('prev_rec_qty')];
			$productionDataArr['total'][$key]=$row[csf('total_rec_qty')];
		}
		if($row[csf('entry_form')]==223){//223=Qc;
			$key=$row[csf('job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('color_size_id')];
			$QcDataArr['today'][$key]=$row[csf('today_rec_qty')];
			$QcDataArr['prev'][$key]=$row[csf('prev_rec_qty')];
			$QcDataArr['total'][$key]=$row[csf('total_rec_qty')];
		}
	}
	
	
	//Delivery................................................
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (a.job_no in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or a.job_no in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";
	$sql_delivery="select a.job_no,c.body_part,c.id as po_id,b.color_size_id,
	sum(case when a.delivery_date = $txt_date_from then b.delivery_qty else 0 end) as today_delivery_qty,
	sum(case when a.delivery_date < $txt_date_from then b.delivery_qty else 0 end) as prev_delivery_qty, 
	sum(b.delivery_qty) as total_delivery_qty
  from subcon_delivery_mst a,subcon_delivery_dtls b,subcon_ord_dtls c 
  where a.id=b.mst_id and c.id=b.order_id $sql_con and a.entry_form=254 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
   group by a.job_no,c.body_part ,c.id,b.color_size_id";
	$sql_delivery_result=sql_select($sql_delivery);
	$deliveryDataArr=array();
	foreach($sql_delivery_result as $row)
	{
		$key=$row[csf('job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('color_size_id')];
		$deliveryDataArr['today'][$key]=$row[csf('today_delivery_qty')];
		$deliveryDataArr['prev'][$key]=$row[csf('prev_delivery_qty')];
		$deliveryDataArr['total'][$key]=$row[csf('total_delivery_qty')];
	}
	
	
	//Bill................................................
	$p=1;
	$po_chunk_arr=array_chunk($embJobArr,999);
	foreach($po_chunk_arr as $po_arr)
	{
		if($p==1){$sql_con =" and (c.job_no_mst in('".implode("','",$po_arr)."')";} 
		else{$sql_con .=" or c.job_no_mst in('".implode("','",$po_arr)."')";}
		$p++;
	}
	$sql_con .=")";

	$sql_bill="select c.job_no_mst as job_no,c.body_part,c.id as po_id,b.color_size_id,
	sum(case when a.bill_date = $txt_date_from then b.delivery_qty else 0 end) as today_bill_qty,
	sum(case when a.bill_date < $txt_date_from then b.delivery_qty else 0 end) as prev_bill_qty, 
	sum(b.delivery_qty) as total_bill_qty,
	sum(b.amount) as total_bill_amount
  from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b,subcon_ord_dtls c 
  where a.id=b.mst_id and c.id=b.order_id $sql_con and b.process_id=13 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
   group by c.job_no_mst,c.body_part ,c.id,b.color_size_id";
	$sql_bill_result=sql_select($sql_bill);
	$billDataArr=array();
	foreach($sql_bill_result as $row)
	{
		$key=$row[csf('job_no')].$row[csf('po_id')].$row[csf('body_part')].$row[csf('color_size_id')];
		//$billDataArr['today'][$key]=$row[csf('today_bill_qty')];
		//$billDataArr['prev'][$key]=$row[csf('prev_bill_qty')];
		$billDataArr['total_qty'][$key]=$row[csf('total_bill_qty')];
		$billDataArr['total_amount'][$key]=$row[csf('total_bill_amount')];
	}
	
	
	
	
	
	ob_start();
	?>
    <div style="width:3220px"> 
        <table width="100%" cellspacing="0" >
            <tr style="border:none;">
                <td colspan="37" align="center" style="border:none; font-size:14px;">
                    <b><? echo $company_library[str_replace("'","",$cbo_company_id)]; ?></b>
                </td>
            </tr>
        </table>
        
        <div style="float:left; width:3220px">
            <table width="3200" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
                <thead>
                    <tr>
                        <th rowspan="2" width="35">SL</th>
                        <th rowspan="2" width="100">Emb. Job</th>
                        <th rowspan="2" width="100">Work Order</th>
                        <th rowspan="2" width="100">Buyer Name</th>
                        <th rowspan="2" width="130">Buyer PO</th>
                        <th rowspan="2" width="130">Style</th>
                        <th rowspan="2" width="100">Body Color</th>
                        <th rowspan="2" width="100">Body Part</th>
                        <th rowspan="2" width="80">Order Qty (Pcs)</th>
                        <th rowspan="2" width="80">Plan Qty</th>
                        <th rowspan="2" width="80">Buyer Ship Date</th>
                        <th rowspan="2" width="80">Delivery Date</th>
                        <th colspan="4">Cut Piece Received Status</th>
                        
                        <th colspan="4">Cut Panel Issue Status</th>
                        <th colspan="5">Print Status</th>
                        <th colspan="4">QC Status</th>
                        <th colspan="5">Delivery Status</th>
                        <th colspan="3">Bill Status</th>
                        
                    </tr>
                    <tr>
                        <th width="80">Previous Receive</th>
                        <th width="80">Today Receive</th>
                        <th width="80">Total Receive</th>
                        <th width="80">Recv Balance</th>
                        
                        <th width="80">Previous Issue</th>
                        <th width="80">Today Issue</th>
                        <th width="80">Total Issue</th>
                        <th width="80">Issue Balance</th>
                        
                        <th width="80">Previous Print</th>
                        <th width="80">Today Print</th>
                        <th width="80">Total Print</th>
                        <th width="80">Balance</th>
                        <th width="80">WIP</th>
                        
                        <th width="80">Previous QC</th>
                        <th width="80">Today QC</th>
                        <th width="80">Total QC</th>
                        <th width="80">QC Balance</th>
                        
                        <th width="80">Previous Delivery</th>
                        <th width="80">Today Delivery</th>
                        <th width="80">Total Delivery</th>
                        <th width="80">Delivery Balance</th>
                        <th width="80">Left Over Qty</th>
                        
                        <th width="80">Bill Qty</th>
                        <th width="80">Bill Amount</th>
                        <th>Balance Bill Qty</th>
                        
                    </tr>
                </thead>
            </table>
            <div style="max-height:350px; width:3220px; overflow-y:auto;" id="scroll_body" >
                <table cellspacing="0" border="1" class="rpt_table"  width="3200" rules="all" id="table_body" >
                <tbody>
				<?  
					$i=1;
					
				foreach($buyerPoDataArr as $buyerId=>$buyArr)
				{
				  foreach($buyArr as $buyerPo=>$sql_result)
				  {
					foreach($sql_result as $row)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						
						$toDayMaterRec=0;$prevMaterRec=0;$totalMaterRec=0;
						$toDayMaterIssue=0;$prevMaterIssue=0;$totalMaterIssue=0;
						$toDayProduc=0;$prevProduc=0;$totalProduc=0;
						$toDayDelivery=0; $prevDelivery=0; $totalDelivery=0;
						$toDayQc=0;$prevQc=0;$totalQc=0;
						$totalBillQty=0;$totalBillAmount=0;
						
						foreach(explode(',',$row[csf('color_size_id')]) as $csi){
							$key=$row[csf('job_no')].$row[csf('po_id')].$row[csf('body_part')].$csi;
							
							//Receive.............................................
							$toDayMaterRec+=$materialReceiveDataArr['today'][$key];
							$prevMaterRec+=$materialReceiveDataArr['prev'][$key];
							$totalMaterRec+=$materialReceiveDataArr['total'][$key];
							
							$grand_toDayMaterRec+=$materialReceiveDataArr['today'][$key];
							$grand_prevMaterRec+=$materialReceiveDataArr['prev'][$key];
							$grand_totalMaterRec+=$materialReceiveDataArr['total'][$key];
							
							$po_toDayMaterRec+=$materialReceiveDataArr['today'][$key];
							$po_prevMaterRec+=$materialReceiveDataArr['prev'][$key];
							$po_totalMaterRec+=$materialReceiveDataArr['total'][$key];

							$buyer_toDayMaterRec+=$materialReceiveDataArr['today'][$key];
							$buyer_prevMaterRec+=$materialReceiveDataArr['prev'][$key];
							$buyer_totalMaterRec+=$materialReceiveDataArr['total'][$key];
							
							
							//Issue.............................................
							$toDayMaterIssue+=$materialIssueDataArr['today'][$key];
							$prevMaterIssue+=$materialIssueDataArr['prev'][$key];
							$totalMaterIssue+=$materialIssueDataArr['total'][$key];
							
							$grand_toDayMaterIssue+=$materialIssueDataArr['today'][$key];
							$grand_prevMaterIssue+=$materialIssueDataArr['prev'][$key];
							$grand_totalMaterIssue+=$materialIssueDataArr['total'][$key];
							
							$po_toDayMaterIssue+=$materialIssueDataArr['today'][$key];
							$po_prevMaterIssue+=$materialIssueDataArr['prev'][$key];
							$po_totalMaterIssue+=$materialIssueDataArr['total'][$key];

							$buyer_toDayMaterIssue+=$materialIssueDataArr['today'][$key];
							$buyer_prevMaterIssue+=$materialIssueDataArr['prev'][$key];
							$buyer_totalMaterIssue+=$materialIssueDataArr['total'][$key];

							//Production(print).............................................							
							$toDayProduc+=$productionDataArr['today'][$key];
							$prevProduc+=$productionDataArr['prev'][$key];	
							$totalProduc+=$productionDataArr['total'][$key];
							
							$grand_toDayProduc+=$productionDataArr['today'][$key];
							$grand_prevProduc+=$productionDataArr['prev'][$key];	
							$grand_totalProduc+=$productionDataArr['total'][$key];
							
							$po_toDayProduc+=$productionDataArr['today'][$key];
							$po_prevProduc+=$productionDataArr['prev'][$key];	
							$po_totalProduc+=$productionDataArr['total'][$key];
							
							$buyer_toDayProduc+=$productionDataArr['today'][$key];
							$buyer_prevProduc+=$productionDataArr['prev'][$key];	
							$buyer_totalProduc+=$productionDataArr['total'][$key];
							
							
							//Qc.............................................							
							$toDayQc+=$QcDataArr['today'][$key];
							$prevQc+=$QcDataArr['prev'][$key];	
							$totalQc+=$QcDataArr['total'][$key];
							
							$grand_toDayQc+=$QcDataArr['today'][$key];
							$grand_prevQc+=$QcDataArr['prev'][$key];	
							$grand_totalQc+=$QcDataArr['total'][$key];
							
							$po_toDayQc+=$QcDataArr['today'][$key];
							$po_prevQc+=$QcDataArr['prev'][$key];	
							$po_totalQc+=$QcDataArr['total'][$key];
							
							$buyer_toDayQc+=$QcDataArr['today'][$key];
							$buyer_prevQc+=$QcDataArr['prev'][$key];	
							$buyer_totalQc+=$QcDataArr['total'][$key];
							
							
							//Delivery.............................................							
							$toDayDelivery+=$deliveryDataArr['today'][$key];
							$prevDelivery+=$deliveryDataArr['prev'][$key];	
							$totalDelivery+=$deliveryDataArr['total'][$key];
							
							$grand_toDayDelivery+=$deliveryDataArr['today'][$key];
							$grand_prevQDelivery+=$deliveryDataArr['prev'][$key];	
							$grand_totalDelivery+=$deliveryDataArr['total'][$key];
							
							$po_toDayDelivery+=$deliveryDataArr['today'][$key];
							$po_prevQDelivery+=$deliveryDataArr['prev'][$key];	
							$po_totalDelivery+=$deliveryDataArr['total'][$key];
							
							$buyer_toDayDelivery+=$deliveryDataArr['today'][$key];
							$buyer_prevQDelivery+=$deliveryDataArr['prev'][$key];	
							$buyer_totalDelivery+=$deliveryDataArr['total'][$key];
							
							//Bill.............................................							
							$totalBillQty+=$billDataArr['total_qty'][$key];
							$totalBillAmount+=$billDataArr['total_amount'][$key];
							
							$grand_totalBillQty+=$billDataArr['total_qty'][$key];
							$grand_totalBillAmount+=$billDataArr['total_amount'][$key];
							
							$po_totalBillQty+=$billDataArr['total_qty'][$key];
							$po_totalBillAmount+=$billDataArr['total_amount'][$key];
							
							$buyer_totalBillQty+=$billDataArr['total_qty'][$key];
							$buyer_totalBillAmount+=$billDataArr['total_amount'][$key];
							
							
						}
						
							$po_totalOrderQtyPcs+=$row[csf('order_qty')];
							$po_totalPlanQtyPcs+=$row[csf('plan_qty')];
							
							$buyer_totalOrderQtyPcs+=$row[csf('order_qty')];
							$buyer_totalPlanQtyPcs+=$row[csf('plan_qty')];
						
							$grand_totalOrderQtyPcs+=$row[csf('order_qty')];
							$grand_totalPlanQtyPcs+=$row[csf('plan_qty')];
						
						
						
						?>
						
                          <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                          
                            <td width="35" align="center"><? echo $i;?></td>
                            <td width="100"><? echo $row[csf('job_no')];?></td>
                            <td width="100"><? echo $row[csf('order_no')];?></td>
                            <td width="100"><p style="width:95px; word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
                            <td width="130"><p style="width:125px; word-break:break-all"><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="130"><p style="width:125px; word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></p></td>
                            <td width="100"><p style="width:95px; word-break:break-all"><? echo $color_library_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100"><p style="width:95px; word-break:break-all"><? echo $body_part[$row[csf('body_part')]]; ?></p></td>
                            <td width="80" align="right"><? echo number_format($row[csf('order_qty')],2); ?></td>
                            <td width="80" align="right"><? echo number_format($row[csf('plan_qty')],2); ?></td>
                            <td width="80" align="center"><? echo change_date_format($buyerCutupDateArr[$row[csf('buyer_po_id')]]); ?></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
                           
                            <td align="right" width="80"><? echo number_format($prevMaterRec,2); ?></td>
                            <td align="right" width="80"><? echo number_format($toDayMaterRec,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalMaterRec,2); ?></td>
                            <td align="right" width="80"><? echo number_format($row[csf('plan_qty')]-$totalMaterRec,2); ?></td>
                            <td align="right" width="80"><? echo number_format($prevMaterIssue,2); ?></td>
                            <td align="right" width="80"><? echo number_format($toDayMaterIssue,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalMaterIssue,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalMaterRec-$totalMaterIssue,2); ?></td>
                            <td align="right" width="80"><? echo number_format($prevProduc,2); ?></td>
                            <td align="right" width="80"><? echo number_format($toDayProduc,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalProduc,2); ?></td>
                            <td align="right" width="80"><? echo number_format($row[csf('plan_qty')]-$totalProduc,2); ?></td>
                            <td align="right" width="80"><? echo number_format(($row[csf('plan_qty')]-$totalProduc)-($row[csf('plan_qty')]-$totalMaterRec),2);?></td>

                            <td align="right" width="80"><? echo number_format($prevQc,2); ?></td>
                            <td align="right" width="80"><? echo number_format($toDayQc,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalQc,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalProduc-$totalQc,2); ?></td>
                            
                            <td align="right" width="80"><? echo number_format($prevDelivery,2); ?></td>
                            <td align="right" width="80"><? echo number_format($toDayDelivery,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalDelivery,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalProduc-$totalDelivery,2); ?></td>
                            <td align="right" width="80"><? echo number_format(($row[csf('plan_qty')]-$totalDelivery),2);?></td>
                           
                            <td align="right" width="80"><? echo number_format($totalBillQty,2); ?></td>
                            <td align="right" width="80"><? echo number_format($totalBillAmount,2); ?></td>
                            <td align="right"><? echo number_format($row[csf('plan_qty')]-$totalBillQty,2); ?></td>
						  </tr>
                        	
						<?		
					$i++;
					}
					?>		
                    <tr bgcolor='#EEEEEE'> 
                        <td width="35"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td> </td>
                        <td align="right">Order Total:  </td>
                        <td align="right"><? echo number_format($po_totalOrderQtyPcs,2);?></td>
                        <td align="right"><? echo number_format($po_totalPlanQtyPcs,2);?></td>
                        <td></td>
                        <td></td>
                       
                        <td align="right"><? echo number_format($po_prevMaterRec,2);?></td>
                        <td align="right"><? echo number_format($po_toDayMaterRec,2);?></td>
                        <td align="right"><? echo number_format($po_totalMaterRec,2);?></td>
                        <td align="right"><? echo number_format($po_totalPlanQtyPcs-$po_totalMaterRec,2);?></td>
                        <td align="right"><? echo number_format($po_prevMaterIssue,2);?></td>
                        <td align="right"><? echo number_format($po_toDayMaterIssue,2);?></td>
                        <td align="right"><? echo number_format($po_totalMaterIssue,2);?></td>
                        <td align="right"><? echo number_format($po_totalMaterRec-$po_totalMaterIssue,2);?></td>
                        
                        <td align="right"><? echo number_format($po_prevProduc,2);?></td>
                        <td align="right"><? echo number_format($po_toDayProduc,2);?></td>
                        <td align="right"><? echo number_format($po_totalProduc,2);?></td>
                        <td align="right"><? echo number_format($po_totalPlanQtyPcs-$po_totalProduc,2);?></td>
                        <td align="right"><? echo number_format(($po_totalPlanQtyPcs-$po_totalProduc)-($po_totalPlanQtyPcs-$po_totalMaterRec),2);?></td>
                        
                        
                        <td align="right"><? echo number_format($po_prevQc,2);?></td>
                        <td align="right"><? echo number_format($po_toDayQc,2);?></td>
                        <td align="right"><? echo number_format($po_totalQc,2);?></td>
                        <td align="right"><? echo number_format($po_totalProduc-$po_totalQc,2);?></td>
                        
                        <td align="right"><? echo number_format($po_prevDelivery,2);?></td>
                        <td align="right"><? echo number_format($po_toDayDelivery,2);?></td>
                        <td align="right"><? echo number_format($po_totalDelivery,2);?></td>
                        <td align="right"><? echo number_format($po_totalProduc-$po_totalDelivery,2);?></td>
                        <td align="right"><? echo number_format(($po_totalPlanQtyPcs-$po_totalDelivery),2);?></td>
                        
                        <td align="right"><? echo number_format($po_totalBillQty,2);?></td>
                        <td align="right"><? echo number_format($po_totalBillAmount,2);?></td>
                        <td align="right"><? echo number_format($po_totalPlanQtyPcs-$po_totalBillQty,2);?></td>
                	</tr>
					<?		
					
						$po_totalOrderQtyPcs=0;
						$po_totalPlanQtyPcs=0;
						//Receive.......
						$po_toDayMaterRec=0;
						$po_prevMaterRec=0;
						$po_totalMaterRec=0;
						//Issue.............
						$po_toDayMaterIssue=0;
						$po_prevMaterIssue=0;
						$po_totalMaterIssue=0;
						//Production(print)...					
						$po_toDayProduc=0;
						$po_prevProduc=0;	
						$po_totalProduc=0;
						//Qc..................				
						$po_toDayQc=0;
						$po_prevQc=0;
						$po_totalQc=0;
						//Delivery............					
						$po_toDayDelivery=0;
						$po_prevQDelivery=0;
						$po_totalDelivery=0;
						//Bill................				
						$po_totalBillQty=0;
						$po_totalBillAmount=0;
					
					
					}
					?>		
                    <tr bgcolor='#999999'> 
                        <td width="35"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td> </td>
                        <td align="right">Buyer Total: </td>
                        <td align="right"><? echo number_format($buyer_totalOrderQtyPcs,2);?></td>
                        <td align="right"><? echo number_format($buyer_totalPlanQtyPcs,2);?></td>
                        <td></td>
                        <td></td>
                       
                        <td align="right"><? echo number_format($buyer_prevMaterRec,2);?></td>
                        <td align="right"><? echo number_format($buyer_toDayMaterRec,2);?></td>
                        <td align="right"><? echo number_format($buyer_totalMaterRec,2);?></td>
                        <td align="right"><? echo number_format($buyer_totalPlanQtyPcs-$buyer_totalMaterRec,2);?></td>
                        <td align="right"><? echo number_format($buyer_prevMaterIssue,2);?></td>
                        <td align="right"><? echo number_format($buyer_toDayMaterIssue,2);?></td>
                        <td align="right"><? echo number_format($buyer_totalMaterIssue,2);?></td>
                        <td align="right"><? echo number_format($buyer_totalMaterRec-$buyer_totalMaterIssue,2);?></td>
                        
                        <td align="right"><? echo number_format($buyer_prevProduc,2);?></td>
                        <td align="right"><? echo number_format($buyer_toDayProduc,2);?></td>
                        <td align="right"><? echo number_format($buyer_totalProduc,2);?></td>
                        <td align="right"><? echo number_format($buyer_totalPlanQtyPcs-$buyer_totalProduc,2);?></td>
                        <td align="right"><? echo number_format(($buyer_totalPlanQtyPcs-$buyer_totalProduc)-($buyer_totalPlanQtyPcs-$buyer_totalMaterRec),2);?></td>
                        
                        
                        <td align="right"><? echo number_format($buyer_prevQc,2);?></td>
                        <td align="right"><? echo number_format($buyer_toDayQc,2);?></td>
                        <td align="right"><? echo number_format($buyer_totalQc,2);?></td>
                        <td align="right"><? echo number_format($buyer_totalProduc-$buyer_totalQc,2);?></td>
                        
                        <td align="right"><? echo number_format($buyer_prevDelivery,2);?></td>
                        <td align="right"><? echo number_format($buyer_toDayDelivery,2);?></td>
                        <td align="right"><? echo number_format($buyer_totalDelivery,2);?></td>
                        <td align="right"><? echo number_format($buyer_totalProduc-$buyer_totalDelivery,2);?></td>
                        <td align="right"><? echo number_format(($buyer_totalPlanQtyPcs-$buyer_totalDelivery),2);?></td>
                        
                        <td align="right"><? echo number_format($buyer_totalBillQty,2);?></td>
                        <td align="right"><? echo number_format($buyer_totalBillAmount,2);?></td>
                        <td align="right"><? echo number_format($buyer_totalPlanQtyPcs-$buyer_totalBillQty,2);?></td>
                	</tr>
					<?
					$buyer_totalOrderQtyPcs=0;
					$buyer_totalPlanQtyPcs=0;
					//Receive...............
					$buyer_toDayMaterRec=0;
					$buyer_prevMaterRec=0;
					$buyer_totalMaterRec=0;
					//Issue.................
					$buyer_toDayMaterIssue=0;
					$buyer_prevMaterIssue=0;
					$buyer_totalMaterIssue=0;
					//Production(print)......						
					$buyer_toDayProduc=0;
					$buyer_prevProduc=0;	
					$buyer_totalProduc=0;
					//Qc.................					
					$buyer_toDayQc=0;
					$buyer_prevQc=0;	
					$buyer_totalQc=0;
					//Delivery.............				
					$buyer_toDayDelivery=0;
					$buyer_prevQDelivery=0;	
					$buyer_totalDelivery=0;
					//Bill................					
					$buyer_totalBillQty=0;
					$buyer_totalBillAmount=0;
							
					}
                  ?>
                  </tbody>
                </table>
                <table width="3200" border="1" class="tbl_bottom" rules="all" id="report_table_footer" >
                    <tfoot>
                    <tr> 
                        <td width="35"></td>
                        <td width="100"></td>
                        <td width="100"></td>
                        <td width="100"></td>
                        <td width="130"></td>
                        <td width="130"></td>
                        <td width="100"> </td>
                        <td width="100" align="right">Grand Total: </td>
                        <td width="80" align="right" id="gt_order_qty_id"><? echo number_format($grand_totalOrderQtyPcs,2);?></td>
                        <td width="80" align="right" id="gt_plan_qty_id"><? echo number_format($grand_totalPlanQtyPcs,2);?></td>
                        <td width="80"></td>
                        <td width="80"></td>
                       
                        <td width="80" align="right" id="gt_rec_prev_qty_id"><? echo number_format($grand_prevMaterRec,2);?></td>
                        <td width="80" align="right" id="gt_rec_today_qty_id"><? echo number_format($grand_toDayMaterRec,2);?></td>
                        <td width="80" align="right" id="gt_rec_total_qty_id"><? echo number_format($grand_totalMaterRec,2);?></td>
                        <td width="80" align="right" id="gt_rec_bal_qty_id"><? echo number_format($grand_totalPlanQtyPcs-$grand_totalMaterRec,2);?></td>
                        <td width="80" align="right" id="gt_issue_prev_qty_id"><? echo number_format($grand_prevMaterIssue,2);?></td>
                        <td width="80" align="right" id="gt_issue_today_qty_id"><? echo number_format($grand_toDayMaterIssue,2);?></td>
                        <td width="80" align="right" id="gt_issue_total_qty_id"><? echo number_format($grand_totalMaterIssue,2);?></td>
                        <td width="80" align="right" id="gt_issue_bal_qty_id"><? echo number_format($grand_totalMaterRec-$grand_totalMaterIssue,2);?></td>
                        
                        <td width="80" align="right" id="gt_print_prev_qty_id"><? echo number_format($grand_prevProduc,2);?></td>
                        <td width="80" align="right" id="gt_print_today_qty_id"><? echo number_format($grand_toDayProduc,2);?></td>
                        <td width="80" align="right" id="gt_print_total_qty_id"><? echo number_format($grand_totalProduc,2);?></td>
                        <td width="80" align="right" id="gt_print_bal_qty_id"><? echo number_format($totalPlanQtyPcs-$grand_totalProduc,2);?></td>
                        <td width="80" align="right" id="gt_print_wip_qty_id"><? echo number_format(($grand_totalPlanQtyPcs-$grand_totalProduc)-($grand_totalPlanQtyPcs-$grand_totalMaterRec),2);?></td>
                        
                        
                        <td width="80" align="right" id="gt_qc_prev_qty_id"><? echo number_format($grand_prevQc,2);?></td>
                        <td width="80" align="right" id="gt_qc_today_qty_id"><? echo number_format($grand_toDayQc,2);?></td>
                        <td width="80" align="right" id="gt_qc_total_qty_id"><? echo number_format($grand_totalQc,2);?></td>
                        <td width="80" align="right" id="gt_qc_bal_qty_id"><? echo number_format($grand_totalProduc-$grand_totalQc,2);?></td>
                        
                        
                        
                        
                        <td width="80" align="right" id="gt_delivery_prev_qty_id"><? echo number_format($grand_prevDelivery,2);?></td>
                        <td width="80" align="right" id="gt_delivery_today_qty_id"><? echo number_format($grand_toDayDelivery,2);?></td>
                        <td width="80" align="right" id="gt_delivery_total_qty_id"><? echo number_format($grand_totalDelivery,2);?></td>
                        <td width="80" align="right" id="gt_delivery_bal_qty_id"><? echo number_format($grand_totalProduc-$grand_totalDelivery,2);?></td>
                        <td width="80" align="right" id="gt_delivery_loq_qty_id"><? echo number_format(($grand_totalPlanQtyPcs-$grand_totalDelivery),2);?></td>
                        
                        <td width="80" align="right" id="gt_bill_total_qty_id"><? echo number_format($grand_totalBillQty,2);?></td>
                        <td width="80" align="right" id="gt_bill_total_amount_id"><? echo number_format($grand_totalBillAmount,2);?></td>
                        <td align="right" id="gt_bill_bal_qty_id"><? echo number_format($grand_totalPlanQtyPcs-$grand_totalBillQty,2);?></td>
                        
                        
                        
                	</tr>
                    </tfoot>
                </table>
             </div>
           </div>
     	
        </div><!-- end main div -->
		<?
	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	
	
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html**$filename";
	exit();
}
























































if($action=="order_no_popup______________")
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
	$buyer = str_replace("'","",$cbo_buyer_name);
	$location = str_replace("'","",$cbo_location_id);
	$cbo_floor = str_replace("'","",$cbo_floor_id);

    if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
	}
    else 
	{
		$year_field="";
	}

	if(trim($location)==0) $sub_location_name_cond=""; else $sub_location_name_cond=" and a.location_id=$location";
	if(trim($buyer)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$buyer";
	
	$sql="select distinct b.id, b.job_no_mst as job_no ,a.party_id as buyer_name, b.order_no as po_number, a.job_no_prefix_num as job_prefix, $year_field from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_id $sub_buyer_name_cond $sub_location_name_cond and a.is_deleted =0 group by b.id, a.party_id, b.job_no_mst, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";
	
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">Order Number</th>
                <th width="50">Job no</th>
                <th width="80">Buyer</th>
                <th width="40">Year</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
			<? 
			$rows=sql_select($sql);
            $i=1;
            foreach($rows as $data)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $data[csf('id')].'_'.$data[csf('po_number')]; ?>')" style="cursor:pointer;">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
                    <td width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
                    <td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
                    <td width="40" align="center"><p><? echo $data[csf('year')]; ?></p></td>
				</tr>
				<? $i++; 
			} ?>
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}










?>