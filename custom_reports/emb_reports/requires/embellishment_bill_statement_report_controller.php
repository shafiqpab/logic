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
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location--", $selected, "",0 );
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
		echo create_drop_down( "cbo_party_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "load_drop_down( 'requires/embellishment_bill_statement_report_controller', this.value, 'load_drop_down_party_location', 'party_location_td' );");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_party_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
}
if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "" );   	 
	exit();
}
/*if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 70, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
	exit();
}

*/

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
			load_drop_down( 'embellishment_bill_statement_report_controller', company+'_'+within_group+'_'+party, 'load_drop_down_party', 'party_td' );
			
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[2];?>, 'job_search_list_view', 'search_div', 'embellishment_bill_statement_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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
			load_drop_down( 'embellishment_bill_statement_report_controller', company+'_'+within_group+'_'+party, 'load_drop_down_party', 'party_td' );
			
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $within_group;?>+'_'+<? echo $year;?>, 'order_search_list_view', 'search_div', 'embellishment_bill_statement_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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
			load_drop_down( 'embellishment_bill_statement_report_controller', company+'_'+within_group+'_'+party, 'load_drop_down_party', 'party_td' );
			
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $within_group;?>+'_'+<? echo $year;?>, 'style_no_search_list_view', 'search_div', 'embellishment_bill_statement_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
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

if($action=="bill_report_generate")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_wo_order_no=str_replace("'","",trim($txt_wo_order_no));
	//$txt_date_from=str_replace("'","",trim($txt_date_from));
	//echo $txt_date_from; die;
	$cbo_year=str_replace("'","",$cbo_year);
	
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");


	if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and SBM.COMPANY_ID=$cbo_company_id";
	
	if(str_replace("'","",$cbo_buyer_id)==0)$buyer_con="";else $buyer_con=" and e.buyer_name=$cbo_buyer_id";
	if(str_replace("'","",$cbo_party_id)==0)$party_con="";else $party_con=" AND SBM.PARTY_ID =$cbo_party_id";
	if(str_replace("'","",$cbo_location_id)==0)$location_con="";else $location_con=" and a.location_id =$cbo_location_id";
	
	if(str_replace("'","",$cbo_party_location_id)==0)$party_location_con="";else $party_location_con=" and a.party_location =$cbo_party_location_id";
	
	
	if(str_replace("'","",$txt_buyer_po)=='')$po_con="";else $po_con=" and d.po_number = $txt_buyer_po";
	if(str_replace("'","",$txt_buyer_po_id)==0)$po_id_con="";else $po_id_con=" AND SBD.BUYER_PO_ID = $txt_buyer_po_id";
	if(str_replace("'","",$txt_style_ref)=='')$style_con="";else $style_con=" and e.style_ref_no like('%".trim(str_replace("'","",$txt_style_ref))."%')";
	//if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	if(str_replace("'","",trim($txt_job_no))!="") $job_cond="and a.embellishment_job = $txt_job_no";  	
	if($txt_wo_order_no!="") $wo_cond=" AND SOM.ORDER_NO like '%".$txt_wo_order_no."%'";  	
	
	//if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	//if(str_replace("'","",trim($txt_date_to))=="")$date_con="";   else $date_con=" and b.production_date = $txt_date_to";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_range_cond=""; else $date_range_cond=" HAVING SBM.BILL_DATE BETWEEN $txt_date_from and $txt_date_to";	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond_t=""; else $date_cond_t="   HAVING SBM.BILL_DATE BETWEEN  $txt_date_from and $txt_date_to";
	
	
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

	ob_start();
	?>
	</br>
    <div  align="center" style="width:1270px"> 
        <table  align="center" width="100%" cellspacing="0" >
            <tr style="border:none;">
                <td colspan="12" align="center" style="border:none; font-size:18px;">
                    <? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>
                </td>
			</tr>
			<tr style="border:none;">
				<td colspan="12" align="center" style="border:none; font-size:16px;">
                    Bill Statement Report</b>
                </td>
            </tr>
        </table>
        <?
			if($db_type==0) $group_concat="group_concat(c.id) as color_size_id";
			else if($db_type==2) $group_concat="listagg(c.id,',') within group (order by c.id) as color_size_id";
			
			$sql="SELECT SBM.ID,  SBM.BILL_NO,  SBM.PARTY_ID,  SBM.BILL_DATE,  SBM.REMARKS,  SUM(SBD.DELIVERY_QTY) AS DELIVERY_QTY,  AVG(SBD.RATE)         AS RATE,  SUM(SBD.AMOUNT)       AS AMOUNT,
				  SBD.DELIVERY_ID,  SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SDM.DELIVERY_DATE,  SDM.DELIVERY_NO,  SOM.ORDER_ID,  SOM.ORDER_NO,
				  SOM.EMBELLISHMENT_JOB,  e.STYLE_REF_NO,  e.BUYER_NAME
				FROM SUBCON_INBOUND_BILL_MST SBM
				INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
				ON SBM.ID = SBD.MST_ID
				LEFT JOIN SUBCON_DELIVERY_DTLS SDD
				ON SBD.DELIVERY_ID = SDD.ID
				INNER JOIN SUBCON_DELIVERY_MST SDM
				ON SDD.MST_ID = SDM.ID
				INNER JOIN SUBCON_ORD_MST SOM
				ON SDM.JOB_NO = SOM.EMBELLISHMENT_JOB
				LEFT JOIN WO_PO_BREAK_DOWN d
				ON SBD.BUYER_PO_ID = d.ID
				LEFT JOIN WO_PO_DETAILS_MASTER e
				ON d.JOB_NO_MST         = e.JOB_NO
				WHERE SBM.STATUS_ACTIVE = 1
				AND SBD.STATUS_ACTIVE   = 1				
				$wo_cond
				$style_con
				$company_name
				$buyer_con
				GROUP BY SBM.ID,  SBM.BILL_NO,  SBM.PARTY_ID,  SBM.BILL_DATE,  SBM.REMARKS,  SBD.DELIVERY_ID,  SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,
				  SDM.DELIVERY_DATE,  SDM.DELIVERY_NO,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,  e.STYLE_REF_NO,  e.BUYER_NAME $date_cond_t $po_id_con $party_con";
			$sql_result=sql_select($sql);//
			foreach($sql_result as $row)
			{
				$totalOrderQtyPcs+=$row[csf('order_qty')];
				$totalPlanQtyPcs+=$row[csf('plan_qty')];
				
				$poArr[$row[csf('buyer_po_id')]]=$row[csf('buyer_po_id')];
				$embJobArr[$row[csf('job_no')]]=$row[csf('job_no')];
				
				$buyerPoDataArr[$row[csf('buyer_name')]][$row[csf('buyer_po_id')]][]=$row;	
			} 
			// Bill query start ***************************************************************************
				$sql_bill = "SELECT SBM.ID,  SBM.BILL_NO, SBM.PARTY_ID, SBM.BILL_DATE, SBM.REMARKS,  SUM(SBD.DELIVERY_QTY) AS DELIVERY_QTY,
				  AVG(SBD.RATE)         AS RATE,  SUM(SBD.AMOUNT)       AS AMOUNT,  SBD.DELIVERY_ID,  SBM.PARTY_SOURCE,  SBM.COMPANY_ID,
				  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SDM.DELIVERY_DATE,  SDM.DELIVERY_NO,  SOM.ORDER_ID, SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB
				FROM SUBCON_INBOUND_BILL_MST SBM
				INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
				ON SBM.ID = SBD.MST_ID
				LEFT JOIN SUBCON_DELIVERY_DTLS SDD
				ON SBD.DELIVERY_ID = SDD.ID
				INNER JOIN SUBCON_DELIVERY_MST SDM
				ON SDD.MST_ID = SDM.ID
				INNER JOIN SUBCON_ORD_MST SOM
				ON SDM.JOB_NO           = SOM.EMBELLISHMENT_JOB
				WHERE SBM.STATUS_ACTIVE = 1
				AND SBD.STATUS_ACTIVE   = 1 $wo_cond $company_name 
				GROUP BY SBM.ID,  SBM.BILL_NO,SBM.PARTY_ID, SBM.BILL_DATE,SBM.REMARKS, SBD.DELIVERY_ID,SBM.PARTY_SOURCE,SBM.COMPANY_ID,
				  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID, SDM.DELIVERY_DATE, SDM.DELIVERY_NO, SOM.ORDER_ID, SOM.ORDER_NO, SOM.EMBELLISHMENT_JOB 
				  $date_cond_t $po_id_con $party_con";
						 
			$sql_result_bill=sql_select($sql);
			
			//echo $sql; // die;
			foreach($sql_result_bill as $bill)
			{
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('DELIVERY_NO')]][$bill[csf('BUYER_PO_ID')]]["PARTY_ID"]=$bill[csf('PARTY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('DELIVERY_NO')]][$bill[csf('BUYER_PO_ID')]]["ORDER_ID"]=$bill[csf('ORDER_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('DELIVERY_NO')]][$bill[csf('BUYER_PO_ID')]]["DELIVERY_ID"]=$bill[csf('DELIVERY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('DELIVERY_NO')]][$bill[csf('BUYER_PO_ID')]]["BUYER_PO_ID"]=$bill[csf('BUYER_PO_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('DELIVERY_NO')]][$bill[csf('BUYER_PO_ID')]]["BILL_NO"]=$bill[csf('BILL_NO')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('DELIVERY_NO')]][$bill[csf('BUYER_PO_ID')]]["ID"]=$bill[csf('ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('DELIVERY_NO')]][$bill[csf('BUYER_PO_ID')]]["BILL_DATE"]=$bill[csf('BILL_DATE')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('DELIVERY_NO')]][$bill[csf('BUYER_PO_ID')]]["REMARKS"]=$bill[csf('REMARKS')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('DELIVERY_NO')]][$bill[csf('BUYER_PO_ID')]]["PARTY_SOURCE"]=$bill[csf('PARTY_SOURCE')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('DELIVERY_NO')]][$bill[csf('BUYER_PO_ID')]]["COMPANY_ID"]=$bill[csf('COMPANY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('DELIVERY_NO')]][$bill[csf('BUYER_PO_ID')]]["DELIVERY_DATE"]=$bill[csf('DELIVERY_DATE')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('DELIVERY_NO')]][$bill[csf('BUYER_PO_ID')]]["DELIVERY_NO"]=$bill[csf('DELIVERY_NO')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('DELIVERY_NO')]][$bill[csf('BUYER_PO_ID')]]["DELIVERY_QTY"]+=$bill[csf('DELIVERY_QTY')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('DELIVERY_NO')]][$bill[csf('BUYER_PO_ID')]]["RATE"]=$bill[csf('RATE')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('DELIVERY_NO')]][$bill[csf('BUYER_PO_ID')]]["AMOUNT"]+=$bill[csf('AMOUNT')];
				
				$billArr_test[$bill[csf('BILL_NO')]][$bill[csf('PARTY_ID')]][]=$bill;
			}
			//echo $billArr; die;
			//echo "<pre>";print_r($billArr);die;
			//echo "<pre>";print_r($item_data_total);die;
			$buyer_po_arr=array();
	
			$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
			$po_sql_res=sql_select($po_sql);
			
			$Del_Mst_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
			$po_sql_res=sql_select($Del_Mst_sql);
			
			foreach ($po_sql_res as $row)
			{
				$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
				$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
			}
			$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$Del_Mst=return_library_array( "select id, MST_ID from SUBCON_DELIVERY_DTLS",'id','MST_ID');
			$WO_No=return_library_array( "select DISTINCT  ORDER_ID, ORDER_NO from SUBCON_ORD_MST",'ORDER_ID','ORDER_NO');
			//Bill query end ***************************************************************************
		?>
        <div style="width:1300px">
            <table width="1300px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
                <thead>
                    <tr>
                        <th width="35">SL</th>
						<th width="130">Party ID</th>
						<th width="100">ORDER NO</th>
						<th width="130">Bill No</th>						
						<th width="90">BILL Date</th>
						<th width="100">Bill Qty</th>
						<th width="100">Rate</th>
						<th width="100">Amount</th>
						<th width="130">DEL NO</th>
						<th width="90">Del Date</th>
						<th width="100">Buyer PO</th>
						<th>Style</th>
                    </tr>
                </thead>
            </table>
			<div style="max-height:350px; width:1300px; overflow-y:auto;" id="scroll_body" >
                <table cellspacing="0" border="1" class="rpt_table"  width="1300px" rules="all" id="table_body" >
                <tbody>
					<?  	
					$i=1;
					
					foreach($billArr as $partyId=>$partyArr)
					{
					  foreach($partyArr as $orderBill=>$orderArr)
					  {
						foreach($orderArr as $delBill=>$delArr)
						{
							foreach($delArr as $poBill=>$row)
							{
								
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";						
								?>
									  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">                 
										<td width="35" align="center"><? echo $i;?></td>
										<td width="130"><? echo $comp[$row[csf('PARTY_ID')]];?></td>
										<td width="100"><? echo $WO_No[$row[csf('ORDER_ID')]];?></td>									
										<td width="130"><a href ="#" onClick="print_report('<? echo $row[csf('COMPANY_ID')].'*'. $row[csf('ID')];?>','embl_bill_issue_print','../../embellishment/delivery/requires/embl_bill_issue_controller')"> <? echo $row[csf('BILL_NO')];?></a></td>
										<td width="90"><? echo $row[csf('BILL_DATE')];?></td>
										<td align="right" width="100"><? echo $row[csf('DELIVERY_QTY')];?></td>
										<td align="center" width="100"><? echo number_format(($row[csf('AMOUNT')]/$row[csf('DELIVERY_QTY')]),2);?></td>
										<td align="right" width="100"><? echo $row[csf('AMOUNT')];?></td>
										<td width="130"><a href ="#" onClick="print_report('<? echo $row[csf('COMPANY_ID')].'*'. $Del_Mst[$row[csf('DELIVERY_ID')]];?>','embl_delivery_entry_print','../../embellishment/delivery/requires/embellishment_delivery_controller')"><? echo $row[csf('DELIVERY_NO')];?></a></td>
										<td width="90"><? echo $row[csf('DELIVERY_DATE')];?></td>
										<td width="100"><? echo $buyer_po_arr[$row[csf('BUYER_PO_ID')]]['po'];?></td>
										<td><? echo $buyer_po_arr[$row[csf('BUYER_PO_ID')]]['style'];?></td>
									  </tr>
								<?		
								$i++;
								$total_bill+=$row[csf('DELIVERY_QTY')];
								$total_amount+=$row[csf('AMOUNT')];
							}
						}
					  }						
					}
                  ?>
                  </tbody>
                    <tfoot>
                    <tr> 
                        <td width="35"></td>
						<td width="130"></td>
						<td width="100"></td>
						<td width="130"></td>
						<td width="90"><strong>TOTAL :</strong></td>
						<td align="right"width="100"><strong><? echo number_format($total_bill,2);?></strong></td>
						<td align="center" width="100"><strong><? echo number_format(($total_amount/$total_bill), 4,'.','');?></strong></td>
						<td align="right"width="100"><strong><? echo number_format($total_amount,2);?></strong></td>
						<td width="130"></td>
						<td width="90"></td>
						<td width="100"></td>
						<td></td>
                	</tr>
                    </tfoot>
                </table>
             </div>
           </div>
		  
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
if($action=="bill_statement_report_generate")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_wo_order_no=str_replace("'","",trim($txt_wo_order_no));
	//$txt_date_from=str_replace("'","",trim($txt_date_from));
	//echo $txt_date_from; die;
	$cbo_year=str_replace("'","",$cbo_year);
	
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");


	if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and SBM.COMPANY_ID =$cbo_company_id";
	
	if(str_replace("'","",$cbo_buyer_id)==0)$buyer_con="";else $buyer_con=" AND POM.BUYER_NAME=$cbo_buyer_id";
	if(str_replace("'","",$cbo_party_id)==0)$party_con="";else $party_con=" AND SBM.PARTY_ID =$cbo_party_id";
	if(str_replace("'","",$cbo_location_id)==0)$location_con="";else $location_con=" and a.location_id =$cbo_location_id";
	
	if(str_replace("'","",$cbo_party_location_id)==0)$party_location_con="";else $party_location_con=" and a.party_location =$cbo_party_location_id";
	
	
	if(str_replace("'","",$txt_buyer_po)=='')$po_con="";else $po_con=" and d.po_number = $txt_buyer_po";
	if(str_replace("'","",$txt_buyer_po_id)==0)$po_id_con="";else $po_id_con=" AND SBD.BUYER_PO_ID = $txt_buyer_po_id";
	if(str_replace("'","",$txt_style_ref)=='')$style_con="";else $style_con=" and POM.STYLE_REF_NO like('%".trim(str_replace("'","",$txt_style_ref))."%')";
	//if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	if(str_replace("'","",trim($txt_job_no))!="") $job_cond="and a.embellishment_job = $txt_job_no";  	
	if($txt_wo_order_no!="") $wo_cond=" AND SOM.ORDER_NO like '%".$txt_wo_order_no."%'";  	
	
	//if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	//if(str_replace("'","",trim($txt_date_to))=="")$date_con="";   else $date_con=" and b.production_date = $txt_date_to";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_range_cond=""; else $date_range_cond=" HAVING SBM.BILL_DATE BETWEEN $txt_date_from and $txt_date_to";	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond_t=""; else $date_cond_t="   AND SBM.BILL_DATE BETWEEN  $txt_date_from and $txt_date_to";
	
	
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

	ob_start();
	
			// Bill query start ***************************************************************************
				/*$sql_bill = "SELECT SBM.ID,  SBM.BILL_NO, SBM.PARTY_ID, SBM.BILL_DATE, SBM.REMARKS,  SUM(SBD.DELIVERY_QTY) AS DELIVERY_QTY,
				  AVG(SBD.RATE)         AS RATE,  SUM(SBD.AMOUNT)       AS AMOUNT,  SBD.DELIVERY_ID,  SBM.PARTY_SOURCE,  SBM.COMPANY_ID,
				  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SDM.DELIVERY_DATE,  SDM.DELIVERY_NO,  SOM.ORDER_ID, SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB
				FROM SUBCON_INBOUND_BILL_MST SBM
				INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
				ON SBM.ID = SBD.MST_ID
				LEFT JOIN SUBCON_DELIVERY_DTLS SDD
				ON SBD.DELIVERY_ID = SDD.ID
				INNER JOIN SUBCON_DELIVERY_MST SDM
				ON SDD.MST_ID = SDM.ID
				INNER JOIN SUBCON_ORD_MST SOM
				ON SDM.JOB_NO           = SOM.EMBELLISHMENT_JOB
				WHERE SBM.STATUS_ACTIVE = 1
				AND SBD.STATUS_ACTIVE   = 1 $wo_cond $company_name 
				GROUP BY SBM.ID,  SBM.BILL_NO,SBM.PARTY_ID, SBM.BILL_DATE,SBM.REMARKS, SBD.DELIVERY_ID,SBM.PARTY_SOURCE,SBM.COMPANY_ID,
				  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID, SDM.DELIVERY_DATE, SDM.DELIVERY_NO, SOM.ORDER_ID, SOM.ORDER_NO, SOM.EMBELLISHMENT_JOB 
				  $date_cond_t $po_id_con $party_con";*/
				  
				  
			
			$sql_bill_statement = "SELECT SBM.PARTY_ID, SBM.ID, SBM.BILL_NO,SBM.BILL_DATE, SUM(SBD.DELIVERY_QTY) AS DELIVERY_QTY,  AVG(SBD.RATE) AS RATE, SUM(SBD.AMOUNT) AS AMOUNT,
				SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,  POM.BUYER_NAME, POM.CURRENCY_ID
				FROM SUBCON_INBOUND_BILL_MST SBM
				INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
				ON SBM.ID = SBD.MST_ID
				LEFT JOIN SUBCON_DELIVERY_DTLS SDD
				ON SBD.DELIVERY_ID = SDD.ID
				INNER JOIN SUBCON_DELIVERY_MST SDM
				ON SDD.MST_ID = SDM.ID
				INNER JOIN SUBCON_ORD_MST SOM
				ON SDM.JOB_NO = SOM.EMBELLISHMENT_JOB
				INNER JOIN WO_PO_BREAK_DOWN PBD
				ON SBD.BUYER_PO_ID = PBD.ID
				INNER JOIN WO_PO_DETAILS_MASTER POM
				ON PBD.JOB_NO_MST       = POM.JOB_NO
				WHERE SBM.STATUS_ACTIVE = 1
				AND SBD.STATUS_ACTIVE   = 1
				$po_id_con
				$wo_cond
				$style_con
				$party_con
				$company_name	$date_cond_t $buyer_con			
				GROUP BY SBM.PARTY_ID, SBM.ID, SBM.BILL_NO, SBM.BILL_DATE, SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,  
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,POM.BUYER_NAME,  POM.CURRENCY_ID
				ORDER BY POM.STYLE_REF_NO, POM.BUYER_NAME, SBD.BUYER_PO_ID, SBM.BILL_DATE";			 
			
			$sql_result_bill=sql_select($sql_bill_statement);
			
			//echo $sql_bill_statement; die;
	
			foreach($sql_result_bill as $bill)
			{
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["PARTY_ID"]=$bill[csf('PARTY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["ORDER_ID"]=$bill[csf('ORDER_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["STYLE_REF_NO"]=$bill[csf('STYLE_REF_NO')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["BUYER_PO_ID"]=$bill[csf('BUYER_PO_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["BILL_NO"]=$bill[csf('BILL_NO')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["PARTY_LOCATION_ID"]=$bill[csf('PARTY_LOCATION_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["COLOR_SIZE_ID"]=$bill[csf('COLOR_SIZE_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["CURRENCY_ID"]=$bill[csf('CURRENCY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["PARTY_SOURCE"]=$bill[csf('PARTY_SOURCE')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["COMPANY_ID"]=$bill[csf('COMPANY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["EMBELLISHMENT_JOB"]=$bill[csf('EMBELLISHMENT_JOB')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["ORDER_NO"]=$bill[csf('ORDER_NO')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["DELIVERY_QTY"]+=$bill[csf('DELIVERY_QTY')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["RATE"]=$bill[csf('RATE')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["AMOUNT"]+=$bill[csf('AMOUNT')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["BILL_DATE"]=$bill[csf('BILL_DATE')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["BUYER_NAME"]=$bill[csf('BILL_DATE')];
				
				$billArr_style_total_qty[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]] += $bill[csf('DELIVERY_QTY')];				
				$billArr_style_total_amount[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]] += $bill[csf('AMOUNT')];
				
				$bill_style_total_qty[$bill[csf('STYLE_REF_NO')]]+=$bill[csf('DELIVERY_QTY')];				
				$bill_style_total_amount[$bill[csf('STYLE_REF_NO')]]+=$bill[csf('AMOUNT')];
			}
			//echo $billArr; die;
			//echo "<pre>";print_r($billArr);die;
			//echo "<pre>";print_r($item_data_total);die;
			$buyer_po_arr=array();
	
			$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
			$po_sql_res=sql_select($po_sql);
			
			$Del_Mst_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
			$po_sql_res=sql_select($Del_Mst_sql);
			
			foreach ($po_sql_res as $row)
			{
				$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
				$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
			}
			$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$Del_Mst=return_library_array( "select id, MST_ID from SUBCON_DELIVERY_DTLS",'id','MST_ID');
			$WO_No=return_library_array( "select DISTINCT  ORDER_ID, ORDER_NO from SUBCON_ORD_MST",'ORDER_ID','ORDER_NO');
			$lib_location=return_library_array( "SELECT ID,  LOCATION_NAME FROM LIB_LOCATION",'ID','LOCATION_NAME');
			//Bill query end ***************************************************************************
			//echo "<pre>";print_r($lib_location);die;
	?>
        <div style="width:1300px">
            <table width="1300px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
                <thead>
                    <tr>
                        <th width="35">SL</th>
						<th width="150">Style</th>
						<th width="100">Po No</th>
						<th width="100">WO No</th>						
						<th width="130">Bill No</th>
						<th width="90">Bill Date</th>
						<th width="100">Bill Qty(Pcs)</th>
						<th width="70">Currency</th>
						<th width="60">Rate</th>
						<th width="100">Amount</th>
						<th width="150">Party</th>
						<th >Party Location</th>
                    </tr>
                </thead>
            </table>
			<div style="max-height:350px; width:1300px; overflow-y:auto;" id="scroll_body" >
                <table cellspacing="0" border="1" class="rpt_table"  width="1300px" rules="all" id="table_body" >
                <tbody>
					<?  	
					$i=1;
					foreach($billArr as $party_id=>$party_data)
					{
						foreach($party_data as $wo_id=>$wo_data)
						{
							foreach($wo_data as $style_id=>$style_data)
							{
								foreach($style_data as $bill_id=>$bill_data)
								{
									foreach($bill_data as $po_id=>$row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
										
										?>  
											  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
						
												<td width="35" align="center"><? echo $i;?></td>
												<td width="150" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_po_arr[$row[csf('BUYER_PO_ID')]]['style'];?></td>
												<td width="100"><? echo $buyer_po_arr[$row[csf('BUYER_PO_ID')]]['po'];?></td>
												<td width="100"><? echo $WO_No[$row[csf('ORDER_ID')]];?></td>	
												<td width="130"><a href ="#" onClick="print_report('<? echo $row[csf('COMPANY_ID')].'*'. $row[csf('ID')];?>','embl_bill_issue_print','../../embellishment/delivery/requires/embl_bill_issue_controller')"> <? echo $row[csf('BILL_NO')];?></a></td>
												<td width="90"><? echo $row[csf('BILL_DATE')];?></td>
												<td align="right" width="100"><? echo $row[csf('DELIVERY_QTY')];?></td>
												<td align="right" width="70"><? echo $currency[$row[csf('CURRENCY_ID')]];?></td>
												<td align="center" width="60"><? echo number_format($row[csf('AMOUNT')]/$row[csf('DELIVERY_QTY')],4);?></td>
												<td align="right" width="100"><? echo number_format($row[csf('AMOUNT')],4);?></td>
												<td width="150"><? echo $comp[$row[csf('PARTY_ID')]];?></td>
												<td><? echo $lib_location [$row[('PARTY_LOCATION_ID')]];?></td>
											  </tr>
										<?		
										$i++;							
										$party_total_bill+=$row[csf('DELIVERY_QTY')];
										$party_total_amount+=$row[csf('AMOUNT')];
									}
								}	
							}
									
						}	?>
										<!--<tr> 
											<td align="right"  colspan="6"><strong>Style total :</strong></td>
											<td align="right" width="100"><strong><? //echo $billArr_style_total_qty[$party_id][$wo_id];?></strong></td>
											<td align="center" width="70"></td>
											<td align="center" width="60"><strong><? //echo number_format($billArr_style_total_amount[$party_id][$wo_id]/$billArr_style_total_qty[$party_id][$wo_id], 4,'.','');?></strong></td>
											<td align="right"  width="100"><strong><?// echo $billArr_style_total_amount[$party_id][$wo_id];?></strong></td>
											<td width="150"></td>
											<td ></td>						
										</tr>-->
									<?						
								
								
						
					 	
					}
                  ?>
                  </tbody>
                    <tfoot>
                    <tr> 
						<td align="right"  colspan="6"><strong>Grand total :</strong></td>
						<td align="right" width="100"><strong><? echo $party_total_bill;?></strong></td>
						<td align="right"  width="70"></td>
						<td align="center" width="60"><strong><? echo number_format(($party_total_amount/$party_total_bill), 4,'.','');?></strong></td>
						<td align="right"  width="100"><strong><? echo number_format($party_total_amount,4);?></strong></td>
						<td width="150"></td>
						<td ></td>						
                	</tr>
                    </tfoot>
                </table>
             </div>
           </div>
		  
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
	
	//echo "Good Now";
}
if($action=="bill_report_excess")  //bill_statement_report_generate
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$txt_wo_order_no=str_replace("'","",trim($txt_wo_order_no));
	//$txt_date_from=str_replace("'","",trim($txt_date_from));
	echo $txt_wo_order_no;// die;
	$cbo_year=str_replace("'","",$cbo_year);
	
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");


	if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and SBM.COMPANY_ID =$cbo_company_id";
	
	if(str_replace("'","",$cbo_buyer_id)==0)$buyer_con="";else $buyer_con=" AND POM.BUYER_NAME=$cbo_buyer_id";
	if(str_replace("'","",$cbo_party_id)==0)$party_con="";else $party_con=" AND SBM.PARTY_ID =$cbo_party_id";
	if(str_replace("'","",$cbo_location_id)==0)$location_con="";else $location_con=" and a.location_id =$cbo_location_id";
	
	if(str_replace("'","",$cbo_party_location_id)==0)$party_location_con="";else $party_location_con=" and a.party_location =$cbo_party_location_id";
	
	
	if(str_replace("'","",$txt_buyer_po)=='')$po_con="";else $po_con=" and d.po_number = $txt_buyer_po";
	if(str_replace("'","",$txt_buyer_po_id)==0)$po_id_con="";else $po_id_con=" AND SBD.BUYER_PO_ID = $txt_buyer_po_id";
	if(str_replace("'","",$txt_style_ref)=='')$style_con="";else $style_con=" and POM.STYLE_REF_NO like('%".trim(str_replace("'","",$txt_style_ref))."%')";
	//if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	if(str_replace("'","",trim($txt_job_no))!="") $job_cond="and a.embellishment_job = $txt_job_no";  	
	if($txt_wo_order_no!="") $wo_cond=" AND SOM.ORDER_NO like '%".$txt_wo_order_no."%'";  	
	
	//if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	//if(str_replace("'","",trim($txt_date_to))=="")$date_con="";   else $date_con=" and b.production_date = $txt_date_to";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_range_cond=""; else $date_range_cond=" HAVING SBM.BILL_DATE BETWEEN $txt_date_from and $txt_date_to";	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond_t=""; else $date_cond_t="   AND SBM.BILL_DATE BETWEEN  $txt_date_from and $txt_date_to";
	
	
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

	

				  
			$sql_ord_qty="select a.company_id,  a.embellishment_job,  a.party_id, a.order_id,  a.order_no,  c.item_id,  c.color_id,  c.size_id,  c.qnty as ord_qty
						from subcon_ord_mst a inner join subcon_ord_dtls b on a.id = b.mst_id inner join subcon_ord_breakdown c
						on b.id             = c.mst_id
						where a.status_active = 1 and b.status_active = 1 and c.status_active = 1";	  
			$sql_ord_qty_res=sql_select($sql_ord_qty);
			
			//echo $sql_ord_qty; die;
	
			foreach($sql_ord_qty_res as $ord)
			{
				$ord_qty_Arr[$ord[csf('company_id')]][$ord[csf('party_id')]][$ord[csf('embellishment_job')]][$ord[csf('order_id')]]+=$ord[csf('ord_qty')];
			}				
			
			$sql_total_bill = "SELECT SBM.PARTY_ID,
							  SBM.BILL_NO,
							  SBM.BILL_DATE,
							  SUM(SBD.DELIVERY_QTY) AS DELIVERY_QTY,
							  SBM.COMPANY_ID,
							  SOM.ORDER_ID,
							  SOM.ORDER_NO,
							  SOM.EMBELLISHMENT_JOB
							FROM SUBCON_INBOUND_BILL_MST SBM
							INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
							ON SBM.ID = SBD.MST_ID
							LEFT JOIN SUBCON_DELIVERY_DTLS SDD
							ON SBD.DELIVERY_ID = SDD.ID
							INNER JOIN SUBCON_DELIVERY_MST SDM
							ON SDD.MST_ID = SDM.ID
							INNER JOIN SUBCON_ORD_MST SOM
							ON SDM.JOB_NO           = SOM.EMBELLISHMENT_JOB
							WHERE SBM.STATUS_ACTIVE = 1 AND SBD.STATUS_ACTIVE   = 1
							AND SBM.PARTY_ID in ($cbo_party_id) AND SBM.COMPANY_ID  in($cbo_company_id)		
							GROUP BY SBM.PARTY_ID,  SBM.BILL_NO,  SBM.BILL_DATE,  SBM.COMPANY_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB";
			//echo $sql_total_bill; //die;
			$sql_total_bill_result=sql_select($sql_total_bill);
			foreach($sql_total_bill_result as $rows)
			{
				$bill_qty_Arr[$rows[csf('COMPANY_ID')]][$rows[csf('PARTY_ID')]][$rows[csf('EMBELLISHMENT_JOB')]][$rows[csf('ORDER_ID')]]+=$rows[csf('DELIVERY_QTY')];
			}				
			
			$sql_bill_statement = "SELECT SBM.PARTY_ID, SBM.ID, SBM.BILL_NO,SBM.BILL_DATE, SUM(SBD.DELIVERY_QTY) AS DELIVERY_QTY,  AVG(SBD.RATE) AS RATE, SUM(SBD.AMOUNT) AS AMOUNT,
				SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,  POM.BUYER_NAME, POM.CURRENCY_ID
				FROM SUBCON_INBOUND_BILL_MST SBM
				INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
				ON SBM.ID = SBD.MST_ID
				LEFT JOIN SUBCON_DELIVERY_DTLS SDD
				ON SBD.DELIVERY_ID = SDD.ID
				INNER JOIN SUBCON_DELIVERY_MST SDM
				ON SDD.MST_ID = SDM.ID
				INNER JOIN SUBCON_ORD_MST SOM
				ON SDM.JOB_NO = SOM.EMBELLISHMENT_JOB
				INNER JOIN WO_PO_BREAK_DOWN PBD
				ON SBD.BUYER_PO_ID = PBD.ID
				INNER JOIN WO_PO_DETAILS_MASTER POM
				ON PBD.JOB_NO_MST       = POM.JOB_NO
				WHERE SBM.STATUS_ACTIVE = 1
				AND SBD.STATUS_ACTIVE   = 1
				$po_id_con
				$wo_cond
				$style_con
				$party_con
				$company_name	$date_cond_t $buyer_con			
				GROUP BY SBM.PARTY_ID, SBM.ID, SBM.BILL_NO, SBM.BILL_DATE, SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,  
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,POM.BUYER_NAME,  POM.CURRENCY_ID
				ORDER BY POM.STYLE_REF_NO, POM.BUYER_NAME, SBD.BUYER_PO_ID, SBM.BILL_DATE";			 
			
			$sql_result_bill=sql_select($sql_bill_statement);
			
			//echo $sql_bill_statement; //die;

			foreach($sql_result_bill as $bill)
			{
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["ID"]=$bill[csf('ID')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["PARTY_ID"]=$bill[csf('PARTY_ID')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["ORDER_ID"]=$bill[csf('ORDER_ID')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["STYLE_REF_NO"]=$bill[csf('STYLE_REF_NO')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["BUYER_PO_ID"]=$bill[csf('BUYER_PO_ID')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["BILL_NO"]=$bill[csf('BILL_NO')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["PARTY_LOCATION_ID"]=$bill[csf('PARTY_LOCATION_ID')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["COLOR_SIZE_ID"]=$bill[csf('COLOR_SIZE_ID')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["CURRENCY_ID"]=$bill[csf('CURRENCY_ID')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["PARTY_SOURCE"]=$bill[csf('PARTY_SOURCE')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["COMPANY_ID"]=$bill[csf('COMPANY_ID')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["EMBELLISHMENT_JOB"]=$bill[csf('EMBELLISHMENT_JOB')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["ORDER_NO"]=$bill[csf('ORDER_NO')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["DELIVERY_QTY"]+=$bill[csf('DELIVERY_QTY')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["RATE"]=$bill[csf('RATE')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["AMOUNT"]+=$bill[csf('AMOUNT')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["BILL_DATE"]=$bill[csf('BILL_DATE')];
				$billArr_excess[$bill[csf('COMPANY_ID')]][$bill[csf('PARTY_ID')]][$bill[csf('EMBELLISHMENT_JOB')]][$bill[csf('ORDER_ID')]]["BUYER_NAME"]=$bill[csf('BILL_DATE')];
	
				$billArr_style_total_qty[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]] += $bill[csf('DELIVERY_QTY')];				
				$billArr_style_total_amount[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]] += $bill[csf('AMOUNT')];
				
				$bill_style_total_qty[$bill[csf('STYLE_REF_NO')]]+=$bill[csf('DELIVERY_QTY')];				
				$bill_style_total_amount[$bill[csf('STYLE_REF_NO')]]+=$bill[csf('AMOUNT')];
			}
			//echo $billArr; die;
			//echo "<pre>";print_r($billArr_excess);die;
			//echo "<pre>";print_r($item_data_total);die;
			$buyer_po_arr=array();
	
			$po_sql ="select b.po_break_down_id,  a.company_id, a.id,  a.booking_no,  b.booking_type,  c.po_number,  d.style_ref_no
					from wo_booking_mst a inner join wo_booking_dtls b on a.booking_no = b.booking_no inner join wo_po_break_down c
					on b.po_break_down_id = c.id inner join wo_po_details_master d on c.job_no_mst      = d.job_no where b.booking_type = 6";
			$po_sql_res=sql_select($po_sql);
			
			$Del_Mst_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
			$Del_po_sql_res=sql_select($Del_Mst_sql);
			$style = "";
			foreach ($po_sql_res as $row)
			{
				if(count($row[csf("style_ref_no")])>1)
				{
					$style = $row[csf("style_ref_no")];
					break;
				}
				else
				{
					$style = $row[csf("style_ref_no")];
				}
				//echo $style;
				$buyer_style_arr[$row[csf("id")]]['style'].=$style.", ";
				$buyer_po_arr[$row[csf("id")]]['po'].=$row[csf("po_number")].", ";
			}
			
			//echo "<pre>"; print_r(array_unique($buyer_po_arr)); die;
			
			$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$Del_Mst=return_library_array( "select id, MST_ID from SUBCON_DELIVERY_DTLS",'id','MST_ID');
			$wo_No=return_library_array( "select DISTINCT  ORDER_ID, ORDER_NO from SUBCON_ORD_MST",'ORDER_ID','ORDER_NO');
			$lib_location=return_library_array( "SELECT ID,  LOCATION_NAME FROM LIB_LOCATION",'ID','LOCATION_NAME');
			
		ob_start();	
	?>
        <div style="width:1300px">
            <table width="1300px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
                <thead>
                    <tr>
                        <th width="35">SL</th>
						<th width="150">Style</th>
						<th width="100">Po No</th>
						<th width="100">WO No</th>
						<th width="100">WO Qty(Pcs)</th>
						<th width="100">Current Bill(Pcs)</th>
						<th width="100">Total Bill(Pcs)</th>
						<th width="100">Excess Qty(Pcs)</th>
						<th width="50">Currency</th>
						<th width="60">Rate</th>
						<th width="100">Amount</th>
						<th width="150">Party</th>
						<th >Party Location</th>
                    </tr>
                </thead>
            </table>
			<div style="max-height:350px; width:1300px; overflow-y:auto;" id="scroll_body" >
                <table cellspacing="0" border="1" class="rpt_table"  width="1300px" rules="all" id="table_body" >
                <tbody>
					<?  
					$i=1;
					foreach($billArr_excess as $com_id=>$com_data)
					{
						foreach($com_data as $party_id=>$party_data)
						{
							foreach($party_data as $job_id=>$job_data)
							{
								foreach($job_data as $wo_id=>$row)
								{
									$t_bill=$bill_qty_Arr[$com_id][$party_id][$job_id][$wo_id];
									$t_ord=$ord_qty_Arr[$com_id][$party_id][$job_id][$wo_id]*12;
									if ($t_bill > $t_ord)
									{
										if ($i%2==0) 
										{$bgcolor="#E9F3FF";}
										else $bgcolor="#FFFFFF";
										?>  
											  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
												<td width="35" align="center"><? echo $i;?></td>
												<td width="150" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_style_arr[$row[csf('ORDER_ID')]]['style'];?></td>
												<td width="100"><? echo $buyer_po_arr[$row[csf('ORDER_ID')]]['po'];?></td>
												<td width="100"><? echo $wo_No[$row[csf('ORDER_ID')]];?></td>
												<td width="100"align="center" ><? echo number_format($ord_qty_Arr[$com_id][$party_id][$job_id][$wo_id]*12,0);?></td>												
												<td align="right" width="100"><? echo number_format($row[csf('DELIVERY_QTY')],0);?></td>
												<td align="right" width="100"><? echo number_format($t_bill,0);?></td>
												<td align="right" width="100"><? echo  number_format($t_ord-$t_bill,0);?></td>
												<td align="right" width="50"><? echo $currency[$row[csf('CURRENCY_ID')]];?></td>
												<td align="center" width="60"><? echo number_format($row[csf('AMOUNT')]/$row[csf('DELIVERY_QTY')],4);?></td>
												<td align="right" width="100"><? echo number_format($row[csf('AMOUNT')],4);?></td>
												<td width="150"><? echo $comp[$row[csf('PARTY_ID')]];?></td>
												<td><? echo $lib_location [$row[('PARTY_LOCATION_ID')]];?></td>
											  </tr>
										<?		
										$i++;			
										$total_bill+= $t_bill;
										$party_total_bill+=$row[csf('DELIVERY_QTY')];
										$bal_total_bill+= $t_ord-$t_bill;
										$party_total_amount+=$row[csf('AMOUNT')];	
									}
									//else echo" ............No Data is founded.........";									
								}		
							}
						}	
					}
                  ?>
                  </tbody>
                    <tfoot>
                    <tr> 
						<td align="right"  colspan="5"><strong>Grand total :</strong></td>
						<td align="right" ><strong><? echo $party_total_bill;?></strong></td>
						<td align="right" ><strong><? echo $total_bill;?></strong></td>
						<td align="right" ><strong><? echo $bal_total_bill;?></strong></td>
						<td align="right"  ></td>
						<td align="center" ><strong><? echo number_format(($party_total_amount/$party_total_bill), 4,'.','');?></strong></td>
						<td align="right"  ><strong><? echo number_format($party_total_amount,4);?></strong></td>
						<td ></td>
						<td ></td>						
                	</tr>
                    </tfoot>
                </table>
             </div>
           </div>
		  
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
	
	//echo "Good Now";
}


if($action=="bill_report_statement")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_wo_order_no=str_replace("'","",trim($txt_wo_order_no));
	//$txt_date_from=str_replace("'","",trim($txt_date_from));
	//echo $txt_date_from; die;
	$cbo_year=str_replace("'","",$cbo_year);
	
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$from_bill_date=str_replace("'","",trim($txt_date_from));

	if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and SBM.COMPANY_ID in ($cbo_company_id)";
	if(str_replace("'","",$cbo_company_id)==0)$company_name_del_qty=""; else $company_name_del_qty=" and DM.COMPANY_ID  in($cbo_company_id)";
	
	if(str_replace("'","",$cbo_buyer_id)==0)$buyer_con="";else $buyer_con=" AND POM.buyer_name=$cbo_buyer_id";
	if(str_replace("'","",$cbo_party_id)==0)$party_con="";else $party_con=" AND SBM.PARTY_ID =$cbo_party_id";
	if(str_replace("'","",$cbo_location_id)==0)$location_con="";else $location_con=" and a.location_id =$cbo_location_id";
	
	if(str_replace("'","",$cbo_party_location_id)==0)$party_location_con="";else $party_location_con=" and a.party_location =$cbo_party_location_id";
	
	
	if(str_replace("'","",$txt_buyer_po)=='')$po_con="";else $po_con=" and d.po_number = $txt_buyer_po";
	if(str_replace("'","",$txt_buyer_po_id)==0)$po_id_con="";else $po_id_con=" AND SBD.BUYER_PO_ID = $txt_buyer_po_id";
	if(str_replace("'","",$txt_style_ref)=='')$style_con="";else $style_con=" and POM.STYLE_REF_NO like('%".trim(str_replace("'","",$txt_style_ref))."%')";
	//if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	if(str_replace("'","",trim($txt_job_no))!="") $job_cond="and a.embellishment_job = $txt_job_no";  	
	if($txt_wo_order_no!="") $wo_cond=" AND SOM.ORDER_NO like '%".$txt_wo_order_no."%'";  	
	
	//if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	//if(str_replace("'","",trim($txt_date_to))=="")$date_con="";   else $date_con=" and b.production_date = $txt_date_to";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_range_cond=""; else $date_range_cond=" HAVING SBM.BILL_DATE BETWEEN $txt_date_from and $txt_date_to";	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond_t=""; else $date_cond_t="   AND SBM.BILL_DATE BETWEEN  $txt_date_from and $txt_date_to";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond_del_qty=""; else $date_cond_del_qty="   AND DM.DELIVERY_DATE BETWEEN $txt_date_from and $txt_date_to";
	
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

	ob_start();
				  
			$del_qty_sql="SELECT DM.COMPANY_ID,  DM.DELIVERY_NO,  DM.PARTY_ID,  SUM(DD.DELIVERY_QTY)  AS DELIVERY_QTY,  SIB.MST_ID as BILL_ID,  SUM(SIB.DELIVERY_QTY) AS Bill_QTY,  DM.DELIVERY_DATE, OB.ORDER_ID
						FROM SUBCON_DELIVERY_MST DM
						INNER JOIN SUBCON_DELIVERY_DTLS DD
						ON DM.ID = DD.MST_ID
						LEFT JOIN SUBCON_ORD_BREAKDOWN OB
						ON DD.COLOR_SIZE_ID = OB.ID
						LEFT JOIN SUBCON_INBOUND_BILL_DTLS SIB
						ON DD.ID               = SIB.DELIVERY_ID
						WHERE DM.STATUS_ACTIVE = 1 AND DD.STATUS_ACTIVE   = 1 $date_cond_del_qty $company_name_del_qty
						GROUP BY DM.COMPANY_ID,  DM.DELIVERY_NO,  DM.PARTY_ID,   DM.DELIVERY_DATE,   SIB.MST_ID,  OB.ORDER_ID";
			
			$sql_res_del_qty=sql_select($del_qty_sql);	 
			foreach($sql_res_del_qty as $del)
			{				
				$del_qtyArr[$del[csf('PARTY_ID')]][$del[csf('ORDER_ID')]]+=$del[csf('DELIVERY_QTY')];
			}
			
			$del_qty_sql_pre="SELECT DM.COMPANY_ID,  DM.DELIVERY_NO,  DM.PARTY_ID,  SUM(DD.DELIVERY_QTY)  AS DELIVERY_QTY,  SIB.MST_ID as BILL_ID,  SUM(SIB.DELIVERY_QTY) AS Bill_QTY,  DM.DELIVERY_DATE, OB.ORDER_ID
						FROM SUBCON_DELIVERY_MST DM
						INNER JOIN SUBCON_DELIVERY_DTLS DD
						ON DM.ID = DD.MST_ID
						LEFT JOIN SUBCON_ORD_BREAKDOWN OB
						ON DD.COLOR_SIZE_ID = OB.ID
						LEFT JOIN SUBCON_INBOUND_BILL_DTLS SIB
						ON DD.ID               = SIB.DELIVERY_ID
						WHERE DM.STATUS_ACTIVE = 1 AND DD.STATUS_ACTIVE   = 1 AND DM.DELIVERY_DATE < '$from_bill_date' $company_name_del_qty
						GROUP BY DM.COMPANY_ID,  DM.DELIVERY_NO,  DM.PARTY_ID,   DM.DELIVERY_DATE,   SIB.MST_ID,  OB.ORDER_ID";
			
			$sql_res_del_qty_pre=sql_select($del_qty_sql_pre);	 
			foreach($sql_res_del_qty_pre as $del_pre)
			{				
				$pre_del_qtyArr[$del_pre[csf('PARTY_ID')]][$del_pre[csf('ORDER_ID')]]+=$del_pre[csf('DELIVERY_QTY')];
			}
			
			
			$sql_bill_statement = "SELECT SBM.PARTY_ID, SBM.ID, SBM.BILL_NO,SBM.BILL_DATE, SUM(SBD.DELIVERY_QTY) AS DELIVERY_QTY,  AVG(SBD.RATE) AS RATE, SUM(SBD.AMOUNT) AS AMOUNT,
				SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,  POM.BUYER_NAME, POM.CURRENCY_ID
				FROM SUBCON_INBOUND_BILL_MST SBM
				INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
				ON SBM.ID = SBD.MST_ID
				LEFT JOIN SUBCON_DELIVERY_DTLS SDD
				ON SBD.DELIVERY_ID = SDD.ID
				INNER JOIN SUBCON_DELIVERY_MST SDM
				ON SDD.MST_ID = SDM.ID
				INNER JOIN SUBCON_ORD_MST SOM
				ON SDM.JOB_NO = SOM.EMBELLISHMENT_JOB
				INNER JOIN WO_PO_BREAK_DOWN PBD
				ON SBD.BUYER_PO_ID = PBD.ID
				INNER JOIN WO_PO_DETAILS_MASTER POM
				ON PBD.JOB_NO_MST       = POM.JOB_NO
				WHERE SBM.STATUS_ACTIVE = 1
				AND SBD.STATUS_ACTIVE   = 1
				$po_id_con
				$wo_cond
				$style_con
				$party_con
				$company_name	$date_cond_t $buyer_con			
				GROUP BY SBM.PARTY_ID, SBM.ID, SBM.BILL_NO, SBM.BILL_DATE, SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,  
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,POM.BUYER_NAME,  POM.CURRENCY_ID
				ORDER BY POM.STYLE_REF_NO, POM.BUYER_NAME, SBD.BUYER_PO_ID, SBM.BILL_DATE";	

			$sql_bill_statement_previous = "SELECT SBM.PARTY_ID, SBM.ID, SBM.BILL_NO,SBM.BILL_DATE, SUM(SBD.DELIVERY_QTY) AS DELIVERY_QTY,  AVG(SBD.RATE) AS RATE, SUM(SBD.AMOUNT) AS AMOUNT,
				SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,  POM.BUYER_NAME, POM.CURRENCY_ID
				FROM SUBCON_INBOUND_BILL_MST SBM
				INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
				ON SBM.ID = SBD.MST_ID
				LEFT JOIN SUBCON_DELIVERY_DTLS SDD
				ON SBD.DELIVERY_ID = SDD.ID
				INNER JOIN SUBCON_DELIVERY_MST SDM
				ON SDD.MST_ID = SDM.ID
				INNER JOIN SUBCON_ORD_MST SOM
				ON SDM.JOB_NO = SOM.EMBELLISHMENT_JOB
				INNER JOIN WO_PO_BREAK_DOWN PBD
				ON SBD.BUYER_PO_ID = PBD.ID
				INNER JOIN WO_PO_DETAILS_MASTER POM
				ON PBD.JOB_NO_MST       = POM.JOB_NO
				WHERE SBM.STATUS_ACTIVE = 1
				AND SBD.STATUS_ACTIVE   = 1
				AND sbm.bill_date < '$from_bill_date'
				$po_id_con
				$wo_cond
				$style_con
				$party_con
				$company_name $buyer_con			
				GROUP BY SBM.PARTY_ID, SBM.ID, SBM.BILL_NO, SBM.BILL_DATE, SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,  
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,POM.BUYER_NAME,  POM.CURRENCY_ID
				ORDER BY POM.STYLE_REF_NO, POM.BUYER_NAME, SBD.BUYER_PO_ID, SBM.BILL_DATE";
			
			$sql_result_bill=sql_select($sql_bill_statement);
			
			$sql_result_bill_previous=sql_select($sql_bill_statement_previous);
			foreach($sql_result_bill_previous as $bill_pre)
			{
				$pre_billArr[$bill_pre[csf('PARTY_ID')]][$bill_pre[csf('ORDER_ID')]][$bill_pre[csf('STYLE_REF_NO')]]+=$bill_pre[csf('DELIVERY_QTY')];
				
			}
			
			//echo $sql_bill_statement_previous; die;
	
			foreach($sql_result_bill as $bill)
			{
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["PARTY_ID"]=$bill[csf('PARTY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["ORDER_ID"]=$bill[csf('ORDER_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["STYLE_REF_NO"]=$bill[csf('STYLE_REF_NO')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["BUYER_PO_ID"]=$bill[csf('BUYER_PO_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["PARTY_LOCATION_ID"]=$bill[csf('PARTY_LOCATION_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["COLOR_SIZE_ID"]=$bill[csf('COLOR_SIZE_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["CURRENCY_ID"]=$bill[csf('CURRENCY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["PARTY_SOURCE"]=$bill[csf('PARTY_SOURCE')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["COMPANY_ID"]=$bill[csf('COMPANY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["ORDER_NO"]=$bill[csf('ORDER_NO')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["DELIVERY_QTY"]+=$bill[csf('DELIVERY_QTY')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["RATE"]=$bill[csf('RATE')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["AMOUNT"]+=$bill[csf('AMOUNT')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["BUYER_NAME"]=$bill[csf('BUYER_NAME')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["B_ID"].=$bill[csf('id')].",";
				
				
				$billArr_style_total_qty[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]] += $bill[csf('DELIVERY_QTY')];				
				$billArr_style_total_amount[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]] += $bill[csf('AMOUNT')];
				
				$bill_style_total_qty[$bill[csf('STYLE_REF_NO')]]+=$bill[csf('DELIVERY_QTY')];				
				$bill_style_total_amount[$bill[csf('STYLE_REF_NO')]]+=$bill[csf('AMOUNT')];
				
				$party=$bill[csf('PARTY_ID')];
				$buyer=$bill[csf('BUYER_NAME')];
				$party_location=$bill[csf('PARTY_LOCATION_ID')];
				}
			//echo $billArr; die;
			//echo "<pre>";print_r($billArr);die;
			//echo "<pre>";print_r($item_data_total);die;
			
			
			$buyer_po_arr=array();
	
			$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
			$po_sql_res=sql_select($po_sql);
			
			$Del_Mst_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
			$po_sql_res=sql_select($Del_Mst_sql);
			
			
			
			foreach ($po_sql_res as $row)
			{
				$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
				$buyer_po_arr[$row[csf("id")]]['po'].=$row[csf("po_number")].", ";
			}
			
			$sql_wo_qty ="select  sum(b.order_quantity) as order_quantity, b.order_no, b.order_id from  subcon_ord_mst a inner join subcon_ord_dtls b on a.id = b.mst_id group by b.order_id, b.order_no";
			
			$sql_wo_qty_res=sql_select($sql_wo_qty);
			
			
			
			foreach ($sql_wo_qty_res as $row_wo)
			{
				$wo_qty_arr[$row_wo[csf("order_id")]]+=$row_wo[csf("order_quantity")];
			}
			//echo "<pre>";print_r($wo_qty_arr);die;
			
			$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$del_mst=return_library_array( "select id, mst_id from subcon_delivery_dtls",'id','mst_id');
			$wo_no=return_library_array( "select distinct  order_id, order_no from subcon_ord_mst",'order_id','order_no');
			$wo_qty=return_library_array( "select b.id, sum(a.order_quantity) as order_quantity from subcon_ord_dtls a inner join subcon_ord_mst b on b.id = a.mst_id group by b.id",'id','order_quantity');
			$lib_location=return_library_array( "select id,  location_name from lib_location",'id','location_name');
			$lib_buyer=return_library_array( "select id,  buyer_name from lib_buyer",'id','buyer_name');
			//bill query end ***************************************************************************
			//echo "<pre>";print_r($lib_buyer['30']);die;
			$value_width =1850;
			$del_qty_arr=return_library_array( "select c.order_id, sum(b.delivery_qty) as delivery_qty  from subcon_delivery_dtls b inner join subcon_ord_breakdown c on b.color_size_id    = c.id where b.status_active = 1 group by c.order_id", "order_id", "delivery_qty");
			
	?>
		<div align = "center" style="width:<? echo $value_width + 33; ?>px;"><br>
            <table width="<? echo $value_width+20; ?>">
				<tr>
					<td align="center" style="font-size:36px;"><p><strong>Company Name : <? echo "Mercer Design Tex Ltd." ?></p></strong></td>
					
				</tr>
				<tr>
					<td align="center" style="font-size:24px;"><strong>Buyer Name : <? echo $lib_buyer[$buyer]; ?></strong></td>
				</tr>
				<tr>
					<td align="center" style="font-size:24px;"><strong>Party Name : <? echo $comp[$party]; echo '/ln';?></strong><strong>Party Location :<?  echo  $lib_location[$party_location];  ?></strong></td>
				</tr>
            </table>
			
			<br>
		</div>
        <div align = "center" style="width:<? echo $value_width + 33; ?>px;">
			<fieldset style="width:<? echo $value_width + 28; ?>px;"> 	
				<table width="<? echo $value_width+20; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr>
							<th width="35">SL</th>
							<th width="120">WO No</th>
							<th width="180">Style</th>
							<th width="130">Buyer PO</th>
							
							<th width="100">WO Qty(Dzn)</th>
							<th width="80">Cur. Rate(Avg)</th>
							<th width="100">Pre Accu Del Qty(Dzn)</th>
							<th width="100">Pre Accu Bill Qty(Dzn)</th>
							<th width="100">Curr Del Qty(Dzn)</th>
							<th width="100">Cur. Bill Qty(Dzn)</th>
							<th width="100">Cur. Amount</th>
							<th width="100">Total Bill Qty(Dzn)</th>
							<th width="100">Total Amount)</th>
							<th width="100">Balance Qty(Dzn)</th>
							
							<th width="100">Balance Amount</th>
							<th width="100">Execss Bill Qty(Dzn)</th>
							<th width="100">Execss Bill Amount</th>
							<th>Total Del Qty(Dzn)</th>
						</tr>
					</thead>
				</table>
				<div style="width:<? echo $value_width + 28; ?>px; max-height:350px; overflow-y:auto;" id="scroll_body" >
					<table  width="<? echo $value_width+20; ?>" cellspacing="0" border="1" class="rpt_table"  rules="all" id="table_body" >
						<tbody>
							<?  	
							$i=1;
							foreach($billArr as $party_id=>$party_data)
							{
								foreach($party_data as $wo_id=>$wo_data)
								{
									foreach($wo_data as $style_id=>$row)
									{

											$tt_bil=($pre_billArr[$party_id][$wo_id][$style_id]/12) + ($row[csf('DELIVERY_QTY')]/12);
											$w_t=$wo_qty_arr[$wo_id];
											
											$t_bill_amount = (($pre_billArr[$party_id][$wo_id][$style_id]/12) + $row[csf('DELIVERY_QTY')]/12)*(($row[csf('AMOUNT')])/($row[csf('DELIVERY_QTY')]/12));
											
											
											if ($w_t < $tt_bil)
											{
												$bgcolor_t="#E9C3CF"; 
												$excess_qty = $wo_qty_arr[$wo_id] - (($pre_billArr[$party_id][$wo_id][$style_id]/12) + $row[csf('DELIVERY_QTY')]/12);
												$excess_qty_amount = ($wo_qty_arr[$wo_id] - (($pre_billArr[$party_id][$wo_id][$style_id]/12) + $row[csf('DELIVERY_QTY')]/12))*(($row[csf('AMOUNT')])/($row[csf('DELIVERY_QTY')]/12));
											}
											else 
											{
												$bgcolor_t="#FFFFFF";
												
												$bal_qty = $wo_qty_arr[$wo_id] - (($pre_billArr[$party_id][$wo_id][$style_id]/12) + $row[csf('DELIVERY_QTY')]/12);
												$bal_amount = ($wo_qty_arr[$wo_id] - (($pre_billArr[$party_id][$wo_id][$style_id]/12) + $row[csf('DELIVERY_QTY')]/12))*(($row[csf('AMOUNT')])/($row[csf('DELIVERY_QTY')]/12));
											}
											
											if ($i%2==0) $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
											?>  
												  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
													<td width="35" align="center"><? echo $i;?></td>
													<td width="120"  style="word-break: break-all;"><? echo $wo_no[$row[csf('ORDER_ID')]];?></td>	
													<td width="180" style="word-break: break-all;"><? echo $buyer_po_arr[$row[csf('BUYER_PO_ID')]]['style'];?></td>
													<td width="130"  style="word-break: break-all;"><? echo $buyer_po_arr[$row[csf('BUYER_PO_ID')]]['po'];?></td>
													
													<td align="right" width="100"  style="word-break: break-all;"><? echo  number_format($wo_qty_arr[$wo_id],2);?></td>
													<td align="center" width="80"><? echo number_format(($row[csf('AMOUNT')])/($row[csf('DELIVERY_QTY')]/12),2);?></td>
													<td align="right" width="100"  style="word-break: break-all;"><? echo  number_format($pre_del_qtyArr[$party_id][$wo_id]/12,2);?></td>
													<td align="right" width="100"><? echo number_format($pre_billArr[$party_id][$wo_id][$style_id]/12,2);?></td>
													<td align="right" width="100"  style="word-break: break-all;"><? echo  number_format($del_qtyArr[$party_id][$wo_id]/12,2);?></td>
													<td align="right" width="100"><? echo number_format($row[csf('DELIVERY_QTY')]/12,2);?></td>
													<td align="right" width="100" ><? echo number_format($row[csf('AMOUNT')],2);?></td>
													<td align="right" width="100" bgcolor="<? echo $bgcolor_t; ?>"><? echo number_format(($pre_billArr[$party_id][$wo_id][$style_id]/12) + $row[csf('DELIVERY_QTY')]/12,2); ?></td>
													<td align="right" width="100" bgcolor="<? echo $bgcolor_t; ?>"><?  echo number_format($t_bill_amount,2); ?></td>
													<td align="right" width="100" bgcolor="<? echo $bgcolor_t; ?>"><?  if ($w_t > $tt_bil){echo number_format($bal_qty,2) ;} else{ echo "";} ?></td>													
													<td align="right" width="100" bgcolor="<? echo $bgcolor_t; ?>"><?   if ($w_t > $tt_bil){echo number_format($bal_amount,2);} else{ echo "";}; ?></td>
													<td align="right" width="100" bgcolor="<? echo $bgcolor_t; ?>"><?  if ($w_t < $tt_bil){echo number_format($excess_qty,2);} else{ echo "";} ; ?></td>
													<td align="right" width="100" bgcolor="<? echo $bgcolor_t; ?>"><?  if ($w_t < $tt_bil){echo number_format($excess_qty_amount,2);} else{ echo "";} ; ?></td>
													<td align="right"><? echo number_format(($del_qtyArr[$party_id][$wo_id]/12) + ($pre_del_qtyArr[$party_id][$wo_id]) ,2);?></td>
												  </tr>
											<?		
											$i++;					
											$wo_qty_total+=$wo_qty_arr[$wo_id];
											$curr_bill_total_qty+=$row[csf('DELIVERY_QTY')]/12;
											$curr_del_total_qty+=$del_qtyArr[$party_id][$wo_id]/12;
											$del_total_qty_pcs+=$del_qtyArr[$party_id][$wo_id];
											$pre_del_total_qty_pcs+=$pre_del_qtyArr[$party_id][$wo_id];
											$pre_del_total_qty_dzn+=$pre_del_qtyArr[$party_id][$wo_id]/12;
											$pre_total_bill_qty +=$pre_billArr[$party_id][$wo_id][$style_id]/12;
											$party_total_amount+=$row[csf('AMOUNT')];
											$tot_bill_qty += ($pre_billArr[$party_id][$wo_id][$style_id]/12) + ($row[csf('DELIVERY_QTY')]/12);
											$tot_del_qty += ($del_qtyArr[$party_id][$wo_id]/12) + ($pre_del_qtyArr[$party_id][$wo_id]);
											$t_bill+=$tt_bil;
											
											$t_bill_amount_g += $t_bill_amount;
											
											
											if ($w_t < $tt_bil)
											{
												
												$excess_qty_g +=$excess_qty ;
												$excess_qty_amount_g +=$excess_qty_amount;
											}
											else 
											{
												
												
												$bal_qty_g += $bal_qty;
												$bal_amount_g += $bal_amount;
											}
									}
											
								}					 	
							}
						  ?>
						  </tbody>
							<tfoot>
								<tr> 
													
									<td align="right" colspan=4><strong>Grand total :</strong></td>
									<td align="right"><strong><? echo number_format($wo_qty_total,2);?></strong></td>
									<td align="center"><strong><? echo number_format(($party_total_amount/$curr_bill_total_qty), 2);?></strong></td>
									<td align="right"><strong><? echo number_format($pre_del_total_qty_dzn,2);?></strong></td>
									<td align="right"><strong><? echo number_format($pre_total_bill_qty,2);?></strong></td>
									<td align="right"><strong><? echo number_format($curr_del_total_qty,2);?></strong></td>
									<td align="right"><strong><? echo number_format($curr_bill_total_qty,2);?></strong></td>
									<td align="right"><strong><? echo number_format($party_total_amount,2);?></strong></td>									
									<td align="right"><strong><? echo number_format($tot_bill_qty ,2);?></strong></td>
									<td align="right"><strong><? echo number_format($t_bill_amount_g,2);?></strong></td>
									<td align="right"><strong><? echo number_format($bal_qty_g,2);?></strong></td>	
										
									<td align="right"><strong><? echo number_format($bal_amount_g,2);?></strong></td>	
									<td align="right"><strong><? echo number_format($excess_qty_g,2);?></strong></td>	
									<td align="right"><strong><? echo number_format($excess_qty_amount_g,2);?></strong></td>										
									<td align="right"><strong><? echo number_format($tot_del_qty ,2);?></strong></td>					
								</tr>
							</tfoot>
					</table>
				 </div>
			</fieldset>
        </div>
		  
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
	
	//echo "Good Now";
}

if($action=="bill_report_statement_bk")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_wo_order_no=str_replace("'","",trim($txt_wo_order_no));
	//$txt_date_from=str_replace("'","",trim($txt_date_from));
	//echo $txt_date_from; die;
	$cbo_year=str_replace("'","",$cbo_year);
	
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$from_bill_date=str_replace("'","",trim($txt_date_from));

	if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and SBM.COMPANY_ID in ($cbo_company_id)";
	if(str_replace("'","",$cbo_company_id)==0)$company_name_del_qty=""; else $company_name_del_qty=" and DM.COMPANY_ID  in($cbo_company_id)";
	
	if(str_replace("'","",$cbo_buyer_id)==0)$buyer_con="";else $buyer_con=" AND POM.buyer_name=$cbo_buyer_id";
	if(str_replace("'","",$cbo_party_id)==0)$party_con="";else $party_con=" AND SBM.PARTY_ID =$cbo_party_id";
	if(str_replace("'","",$cbo_location_id)==0)$location_con="";else $location_con=" and a.location_id =$cbo_location_id";
	
	if(str_replace("'","",$cbo_party_location_id)==0)$party_location_con="";else $party_location_con=" and a.party_location =$cbo_party_location_id";
	
	
	if(str_replace("'","",$txt_buyer_po)=='')$po_con="";else $po_con=" and d.po_number = $txt_buyer_po";
	if(str_replace("'","",$txt_buyer_po_id)==0)$po_id_con="";else $po_id_con=" AND SBD.BUYER_PO_ID = $txt_buyer_po_id";
	if(str_replace("'","",$txt_style_ref)=='')$style_con="";else $style_con=" and POM.STYLE_REF_NO like('%".trim(str_replace("'","",$txt_style_ref))."%')";
	//if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	if(str_replace("'","",trim($txt_job_no))!="") $job_cond="and a.embellishment_job = $txt_job_no";  	
	if($txt_wo_order_no!="") $wo_cond=" AND SOM.ORDER_NO like '%".$txt_wo_order_no."%'";  	
	
	//if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	//if(str_replace("'","",trim($txt_date_to))=="")$date_con="";   else $date_con=" and b.production_date = $txt_date_to";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_range_cond=""; else $date_range_cond=" HAVING SBM.BILL_DATE BETWEEN $txt_date_from and $txt_date_to";	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond_t=""; else $date_cond_t="   AND SBM.BILL_DATE BETWEEN  $txt_date_from and $txt_date_to";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond_del_qty=""; else $date_cond_del_qty="   AND DM.DELIVERY_DATE BETWEEN $txt_date_from and $txt_date_to";
	
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

	ob_start();
				  
			$del_qty_sql="SELECT DM.COMPANY_ID,  DM.DELIVERY_NO,  DM.PARTY_ID,  SUM(DD.DELIVERY_QTY)  AS DELIVERY_QTY,  SIB.MST_ID as BILL_ID,  SUM(SIB.DELIVERY_QTY) AS Bill_QTY,  DM.DELIVERY_DATE, OB.ORDER_ID
						FROM SUBCON_DELIVERY_MST DM
						INNER JOIN SUBCON_DELIVERY_DTLS DD
						ON DM.ID = DD.MST_ID
						LEFT JOIN SUBCON_ORD_BREAKDOWN OB
						ON DD.COLOR_SIZE_ID = OB.ID
						LEFT JOIN SUBCON_INBOUND_BILL_DTLS SIB
						ON DD.ID               = SIB.DELIVERY_ID
						WHERE DM.STATUS_ACTIVE = 1 AND DD.STATUS_ACTIVE   = 1 $date_cond_del_qty $company_name_del_qty
						GROUP BY DM.COMPANY_ID,  DM.DELIVERY_NO,  DM.PARTY_ID,   DM.DELIVERY_DATE,   SIB.MST_ID,  OB.ORDER_ID";
			
			$sql_res_del_qty=sql_select($del_qty_sql);	 
			foreach($sql_res_del_qty as $del)
			{				
				$del_qtyArr[$del[csf('PARTY_ID')]][$del[csf('ORDER_ID')]]+=$del[csf('DELIVERY_QTY')];
			}
			
			$del_qty_sql_pre="SELECT DM.COMPANY_ID,  DM.DELIVERY_NO,  DM.PARTY_ID,  SUM(DD.DELIVERY_QTY)  AS DELIVERY_QTY,  SIB.MST_ID as BILL_ID,  SUM(SIB.DELIVERY_QTY) AS Bill_QTY,  DM.DELIVERY_DATE, OB.ORDER_ID
						FROM SUBCON_DELIVERY_MST DM
						INNER JOIN SUBCON_DELIVERY_DTLS DD
						ON DM.ID = DD.MST_ID
						LEFT JOIN SUBCON_ORD_BREAKDOWN OB
						ON DD.COLOR_SIZE_ID = OB.ID
						LEFT JOIN SUBCON_INBOUND_BILL_DTLS SIB
						ON DD.ID               = SIB.DELIVERY_ID
						WHERE DM.STATUS_ACTIVE = 1 AND DD.STATUS_ACTIVE   = 1 AND DM.DELIVERY_DATE < '$from_bill_date' $company_name_del_qty
						GROUP BY DM.COMPANY_ID,  DM.DELIVERY_NO,  DM.PARTY_ID,   DM.DELIVERY_DATE,   SIB.MST_ID,  OB.ORDER_ID";
			
			$sql_res_del_qty_pre=sql_select($del_qty_sql_pre);	 
			foreach($sql_res_del_qty_pre as $del_pre)
			{				
				$pre_del_qtyArr[$del_pre[csf('PARTY_ID')]][$del_pre[csf('ORDER_ID')]]+=$del_pre[csf('DELIVERY_QTY')];
			}
			
			
			$sql_bill_statement = "SELECT SBM.PARTY_ID, SBM.ID, SBM.BILL_NO,SBM.BILL_DATE, SUM(SBD.DELIVERY_QTY) AS DELIVERY_QTY,  AVG(SBD.RATE) AS RATE, SUM(SBD.AMOUNT) AS AMOUNT,
				SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,  POM.BUYER_NAME, POM.CURRENCY_ID
				FROM SUBCON_INBOUND_BILL_MST SBM
				INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
				ON SBM.ID = SBD.MST_ID
				LEFT JOIN SUBCON_DELIVERY_DTLS SDD
				ON SBD.DELIVERY_ID = SDD.ID
				INNER JOIN SUBCON_DELIVERY_MST SDM
				ON SDD.MST_ID = SDM.ID
				INNER JOIN SUBCON_ORD_MST SOM
				ON SDM.JOB_NO = SOM.EMBELLISHMENT_JOB
				INNER JOIN WO_PO_BREAK_DOWN PBD
				ON SBD.BUYER_PO_ID = PBD.ID
				INNER JOIN WO_PO_DETAILS_MASTER POM
				ON PBD.JOB_NO_MST       = POM.JOB_NO
				WHERE SBM.STATUS_ACTIVE = 1
				AND SBD.STATUS_ACTIVE   = 1
				$po_id_con
				$wo_cond
				$style_con
				$party_con
				$company_name	$date_cond_t $buyer_con			
				GROUP BY SBM.PARTY_ID, SBM.ID, SBM.BILL_NO, SBM.BILL_DATE, SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,  
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,POM.BUYER_NAME,  POM.CURRENCY_ID
				ORDER BY POM.STYLE_REF_NO, POM.BUYER_NAME, SBD.BUYER_PO_ID, SBM.BILL_DATE";	

			$sql_bill_statement_previous = "SELECT SBM.PARTY_ID, SBM.ID, SBM.BILL_NO,SBM.BILL_DATE, SUM(SBD.DELIVERY_QTY) AS DELIVERY_QTY,  AVG(SBD.RATE) AS RATE, SUM(SBD.AMOUNT) AS AMOUNT,
				SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,  POM.BUYER_NAME, POM.CURRENCY_ID
				FROM SUBCON_INBOUND_BILL_MST SBM
				INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
				ON SBM.ID = SBD.MST_ID
				LEFT JOIN SUBCON_DELIVERY_DTLS SDD
				ON SBD.DELIVERY_ID = SDD.ID
				INNER JOIN SUBCON_DELIVERY_MST SDM
				ON SDD.MST_ID = SDM.ID
				INNER JOIN SUBCON_ORD_MST SOM
				ON SDM.JOB_NO = SOM.EMBELLISHMENT_JOB
				INNER JOIN WO_PO_BREAK_DOWN PBD
				ON SBD.BUYER_PO_ID = PBD.ID
				INNER JOIN WO_PO_DETAILS_MASTER POM
				ON PBD.JOB_NO_MST       = POM.JOB_NO
				WHERE SBM.STATUS_ACTIVE = 1
				AND SBD.STATUS_ACTIVE   = 1
				AND sbm.bill_date < '$from_bill_date'
				$po_id_con
				$wo_cond
				$style_con
				$party_con
				$company_name $buyer_con			
				GROUP BY SBM.PARTY_ID, SBM.ID, SBM.BILL_NO, SBM.BILL_DATE, SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,  
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,POM.BUYER_NAME,  POM.CURRENCY_ID
				ORDER BY POM.STYLE_REF_NO, POM.BUYER_NAME, SBD.BUYER_PO_ID, SBM.BILL_DATE";
			
			$sql_result_bill=sql_select($sql_bill_statement);
			
			$sql_result_bill_previous=sql_select($sql_bill_statement_previous);
			foreach($sql_result_bill_previous as $bill_pre)
			{
				$pre_billArr[$bill_pre[csf('PARTY_ID')]][$bill_pre[csf('ORDER_ID')]][$bill_pre[csf('STYLE_REF_NO')]]+=$bill_pre[csf('DELIVERY_QTY')];
				
			}
			
			//echo $sql_bill_statement_previous; die;
	
			foreach($sql_result_bill as $bill)
			{
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["PARTY_ID"]=$bill[csf('PARTY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["ORDER_ID"]=$bill[csf('ORDER_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["STYLE_REF_NO"]=$bill[csf('STYLE_REF_NO')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["BUYER_PO_ID"]=$bill[csf('BUYER_PO_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["PARTY_LOCATION_ID"]=$bill[csf('PARTY_LOCATION_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["COLOR_SIZE_ID"]=$bill[csf('COLOR_SIZE_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["CURRENCY_ID"]=$bill[csf('CURRENCY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["PARTY_SOURCE"]=$bill[csf('PARTY_SOURCE')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["COMPANY_ID"]=$bill[csf('COMPANY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["ORDER_NO"]=$bill[csf('ORDER_NO')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["DELIVERY_QTY"]+=$bill[csf('DELIVERY_QTY')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["RATE"]=$bill[csf('RATE')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["AMOUNT"]+=$bill[csf('AMOUNT')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["BUYER_NAME"]=$bill[csf('BUYER_NAME')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["B_ID"].=$bill[csf('id')].",";
				
				
				$billArr_style_total_qty[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]] += $bill[csf('DELIVERY_QTY')];				
				$billArr_style_total_amount[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]] += $bill[csf('AMOUNT')];
				
				$bill_style_total_qty[$bill[csf('STYLE_REF_NO')]]+=$bill[csf('DELIVERY_QTY')];				
				$bill_style_total_amount[$bill[csf('STYLE_REF_NO')]]+=$bill[csf('AMOUNT')];
				
				$party=$bill[csf('PARTY_ID')];
				$party_location=$bill[csf('PARTY_LOCATION_ID')];
				}
			//echo $billArr; die;
			//echo "<pre>";print_r($billArr);die;
			//echo "<pre>";print_r($item_data_total);die;
			
			
			$buyer_po_arr=array();
	
			$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
			$po_sql_res=sql_select($po_sql);
			
			$Del_Mst_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
			$po_sql_res=sql_select($Del_Mst_sql);
			
			
			
			foreach ($po_sql_res as $row)
			{
				$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
				$buyer_po_arr[$row[csf("id")]]['po'].=$row[csf("po_number")].", ";
			}
			
			$sql_wo_qty ="select  sum(b.order_quantity) as order_quantity, b.order_no, b.order_id from  subcon_ord_mst a inner join subcon_ord_dtls b on a.id = b.mst_id group by b.order_id, b.order_no";
			
			$sql_wo_qty_res=sql_select($sql_wo_qty);
			
			
			
			foreach ($sql_wo_qty_res as $row_wo)
			{
				$wo_qty_arr[$row_wo[csf("order_id")]]+=$row_wo[csf("order_quantity")];
			}
			//echo "<pre>";print_r($wo_qty_arr);die;
			
			$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$del_mst=return_library_array( "select id, mst_id from subcon_delivery_dtls",'id','mst_id');
			$wo_no=return_library_array( "select distinct  order_id, order_no from subcon_ord_mst",'order_id','order_no');
			$wo_qty=return_library_array( "select b.id, sum(a.order_quantity) as order_quantity from subcon_ord_dtls a inner join subcon_ord_mst b on b.id = a.mst_id group by b.id",'id','order_quantity');
			$lib_location=return_library_array( "select id,  location_name from lib_location",'id','location_name');
			$lib_buyer=return_library_array( "select id,  buyer_name from lib_buyer",'id','buyer_name');
			//bill query end ***************************************************************************
			//echo "<pre>";print_r($lib_location);die;
			$value_width =1250;
			$del_qty_arr=return_library_array( "select c.order_id, sum(b.delivery_qty) as delivery_qty  from subcon_delivery_dtls b inner join subcon_ord_breakdown c on b.color_size_id    = c.id where b.status_active = 1 group by c.order_id", "order_id", "delivery_qty");
			
	?>
		<div align = "center" style="width:<? echo $value_width + 33; ?>px;"><br>
            <table width="<? echo $value_width+20; ?>">
				<tr>
					<td align="center" style="font-size:36px;"><p><strong>Company Name : <? echo "Mercer Design Tex Ltd." ?></p></strong></td>
					
				</tr>
				<tr>
					<td align="center" style="font-size:24px;"><strong>Party Name : <? echo $comp[$party]; echo '/ln';?></strong><strong>Party Location :<?  echo  $lib_location[$party_location];  ?></strong></td>
				</tr>
            </table>
			<br>
		</div>
        <div align = "center" style="width:<? echo $value_width + 33; ?>px;">
			<fieldset style="width:<? echo $value_width + 28; ?>px;"> 	
				<table width="<? echo $value_width+20; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr>
							<th width="35">SL</th>
							<th width="130">Buyer</th>
							<th width="120">WO No</th>
							<th width="180">Style</th>
							<th width="120">PO No</th>							
							<th width="100">WO Qty(Dzn)</th>
							<th width="100">Pre Acc Bill Qty(Dzn)</th>
							<th width="100">Curr Del Qty(Dzn)</th>
							
							<th width="100">Cur. Bill Qty(Dzn)</th>
							<th width="80">Cur. Rate(Avg)</th>						
							<th>Cur. Amount</th>
						</tr>
					</thead>
				</table>
				<div style="width:<? echo $value_width + 28; ?>px; max-height:350px; overflow-y:auto;" id="scroll_body" >
					<table  width="<? echo $value_width+20; ?>" cellspacing="0" border="1" class="rpt_table"  rules="all" id="table_body" >
						<tbody>
							<?  	
							$i=1;
							foreach($billArr as $party_id=>$party_data)
							{
								foreach($party_data as $wo_id=>$wo_data)
								{
									foreach($wo_data as $style_id=>$row)
									{
										//foreach($style_data as $po_id=>$row)  //[$party_id][$wo_id][$style_id]
										//{
											/*$unique = array_unique($row[csf('B_ID')]);
											//echo $unique;die;
											$biil_id=$row[csf('B_ID')]."'0'";
											//echo $biil_id;
											
											
												//-- AND SIB.MST_ID in ($biil_id) 			
											$sql_del_qty = "SELECT SOM.ORDER_ID,  SOM.ORDER_NO,  SUM(SDD.DELIVERY_QTY) AS DELIVERY_QTY,  SDM.DELIVERY_NO,  SBM.BILL_NO,  SBM.ID as Bill_ID,  SOM.PARTY_ID
																FROM SUBCON_INBOUND_BILL_MST SBM
																INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
																ON SBM.ID = SBD.MST_ID
																LEFT JOIN SUBCON_DELIVERY_DTLS SDD
																ON SBD.DELIVERY_ID = SDD.ID
																INNER JOIN SUBCON_DELIVERY_MST SDM
																ON SDD.MST_ID = SDM.ID
																INNER JOIN SUBCON_ORD_MST SOM
																ON SDM.JOB_NO           = SOM.EMBELLISHMENT_JOB
																WHERE SBM.STATUS_ACTIVE = 1
																AND SBD.STATUS_ACTIVE   = 1
																AND SBM.ID in ($biil_id)
																GROUP BY SOM.ORDER_ID,SOM.ORDER_NO,SDM.DELIVERY_NO,SBM.BILL_NO,SBM.ID, SOM.PARTY_ID";  
											
											$sql_res_del_qty=sql_select($sql_del_qty);	 
											foreach($sql_res_del_qty as $del)
											{				
												$del_qtyArr[$del[csf('PARTY_ID')]][$del[csf('ORDER_ID')]]+=$del[csf('DELIVERY_QTY')];
											}
											//echo $del_qty_sql;
											//echo "<pre>";print_r($del_qtyArr);die;
											*/
											$tt_bil=($pre_billArr[$party_id][$wo_id][$style_id]/12) + ($row[csf('DELIVERY_QTY')]/12);
											$w_t=$wo_qty_arr[$wo_id];
											if ($w_t < $tt_bil)
											{
												$bgcolor_t="#E9C3CF"; 
											}
											else {$bgcolor_t="#FFFFFF";}
											
											if ($i%2==0) $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
											?>  
												  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
							
													<td width="35" align="center"><? echo $i;?></td>
													<td width="130"  style="word-break: break-all;"><? echo $lib_buyer[$row[csf('BUYER_NAME')]];?></td>
													<td width="120"  style="word-break: break-all;"><? echo $wo_no[$row[csf('ORDER_ID')]];?></td>	
													<td width="180" style="word-break: break-all;"><? echo $buyer_po_arr[$row[csf('BUYER_PO_ID')]]['style'];?></td>
													<td width="120" style="word-break: break-all;"><? echo $buyer_po_arr[$row[csf('BUYER_PO_ID')]]['po'];?></td>
														
													<td align="right" width="100"  style="word-break: break-all;"><? echo  number_format($wo_qty_arr[$wo_id],2);?></td>
													<td align="right" width="100"><? echo number_format($pre_billArr[$party_id][$wo_id][$style_id]/12,2);?></td>
													<td align="right" width="100"  style="word-break: break-all;"><? echo  number_format($del_qtyArr[$party_id][$wo_id]/12,2);?></td>
													
													<td align="right" width="100"><? echo number_format($row[csf('DELIVERY_QTY')]/12,2);?></td>
													<td align="center" width="80"><? echo number_format(($row[csf('AMOUNT')])/($row[csf('DELIVERY_QTY')]/12),2);?></td>
													<td align="right"><? echo number_format($row[csf('AMOUNT')],2);?></td>
												  </tr>
											<?		
											$i++;					
											$wo_qty_total+=$wo_qty_arr[$wo_id];
											$party_total_bill_qty+=$row[csf('DELIVERY_QTY')]/12;
											$del_total_qty+=$del_qtyArr[$party_id][$wo_id]/12;
											$del_total_qty_pcs+=$del_qtyArr[$party_id][$wo_id];
											$pre_del_total_qty_pcs+=$pre_del_qtyArr[$party_id][$wo_id];
											$pre_del_total_qty_dzn+=$pre_del_qtyArr[$party_id][$wo_id]/12;
											$pre_total_bill_qty +=$pre_billArr[$party_id][$wo_id][$style_id]/12;
											$party_total_amount+=$row[csf('AMOUNT')];
											$t_bill+=$tt_bil;
										//}
									}
											
								}					 	
							}
						  ?>
						  </tbody>
							<tfoot>
								<tr> 
									<td align="right" colspan=5><strong>Grand total :</strong></td>
									<td align="right"><strong><? echo number_format($wo_qty_total,2);?></strong></td>
									<td align="right"><strong><? echo number_format($pre_total_bill_qty,2);?></strong></td>
									<td align="right"><strong><? echo number_format($del_total_qty,2);?></strong></td>
									
									<td align="right"><strong><? echo number_format($party_total_bill_qty,2);?></strong></td>
									
									<td align="center"><strong><? echo number_format(($party_total_amount/$party_total_bill_qty), 2);?></strong></td>
									<td align="right"><strong><? echo number_format($party_total_amount,2);?></strong></td>					
								</tr>
							</tfoot>
					</table>
				 </div>
			</fieldset>
        </div>
		  
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
	
	//echo "Good Now";
}

if($action=="bill_report_statement_bk_old")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_wo_order_no=str_replace("'","",trim($txt_wo_order_no));
	//$txt_date_from=str_replace("'","",trim($txt_date_from));
	//echo $txt_date_from; die;
	$cbo_year=str_replace("'","",$cbo_year);
	
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$from_bill_date=str_replace("'","",trim($txt_date_from));

	if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and SBM.COMPANY_ID in ($cbo_company_id)";
	if(str_replace("'","",$cbo_company_id)==0)$company_name_del_qty=""; else $company_name_del_qty=" and DM.COMPANY_ID  in($cbo_company_id)";
	
	if(str_replace("'","",$cbo_buyer_id)==0)$buyer_con="";else $buyer_con=" AND POM.buyer_name=$cbo_buyer_id";
	if(str_replace("'","",$cbo_party_id)==0)$party_con="";else $party_con=" AND SBM.PARTY_ID =$cbo_party_id";
	if(str_replace("'","",$cbo_location_id)==0)$location_con="";else $location_con=" and a.location_id =$cbo_location_id";
	
	if(str_replace("'","",$cbo_party_location_id)==0)$party_location_con="";else $party_location_con=" and a.party_location =$cbo_party_location_id";
	
	
	if(str_replace("'","",$txt_buyer_po)=='')$po_con="";else $po_con=" and d.po_number = $txt_buyer_po";
	if(str_replace("'","",$txt_buyer_po_id)==0)$po_id_con="";else $po_id_con=" AND SBD.BUYER_PO_ID = $txt_buyer_po_id";
	if(str_replace("'","",$txt_style_ref)=='')$style_con="";else $style_con=" and POM.STYLE_REF_NO like('%".trim(str_replace("'","",$txt_style_ref))."%')";
	//if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	if(str_replace("'","",trim($txt_job_no))!="") $job_cond="and a.embellishment_job = $txt_job_no";  	
	if($txt_wo_order_no!="") $wo_cond=" AND SOM.ORDER_NO like '%".$txt_wo_order_no."%'";  	
	
	//if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	//if(str_replace("'","",trim($txt_date_to))=="")$date_con="";   else $date_con=" and b.production_date = $txt_date_to";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_range_cond=""; else $date_range_cond=" HAVING SBM.BILL_DATE BETWEEN $txt_date_from and $txt_date_to";	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond_t=""; else $date_cond_t="   AND SBM.BILL_DATE BETWEEN  $txt_date_from and $txt_date_to";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond_del_qty=""; else $date_cond_del_qty="   AND DM.DELIVERY_DATE BETWEEN $txt_date_from and $txt_date_to";
	
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

	ob_start();
				  
			$del_qty_sql="SELECT DM.COMPANY_ID,  DM.DELIVERY_NO,  DM.PARTY_ID,  SUM(DD.DELIVERY_QTY)  AS DELIVERY_QTY,  SIB.MST_ID as BILL_ID,  SUM(SIB.DELIVERY_QTY) AS Bill_QTY,  DM.DELIVERY_DATE, OB.ORDER_ID
						FROM SUBCON_DELIVERY_MST DM
						INNER JOIN SUBCON_DELIVERY_DTLS DD
						ON DM.ID = DD.MST_ID
						LEFT JOIN SUBCON_ORD_BREAKDOWN OB
						ON DD.COLOR_SIZE_ID = OB.ID
						LEFT JOIN SUBCON_INBOUND_BILL_DTLS SIB
						ON DD.ID               = SIB.DELIVERY_ID
						WHERE DM.STATUS_ACTIVE = 1 AND DD.STATUS_ACTIVE   = 1 $date_cond_del_qty $company_name_del_qty
						GROUP BY DM.COMPANY_ID,  DM.DELIVERY_NO,  DM.PARTY_ID,   DM.DELIVERY_DATE,   SIB.MST_ID,  OB.ORDER_ID";
			
			$sql_res_del_qty=sql_select($del_qty_sql);	 
			foreach($sql_res_del_qty as $del)
			{				
				$del_qtyArr[$del[csf('PARTY_ID')]][$del[csf('ORDER_ID')]]+=$del[csf('DELIVERY_QTY')];
			}
			
			$del_qty_sql_pre="SELECT DM.COMPANY_ID,  DM.DELIVERY_NO,  DM.PARTY_ID,  SUM(DD.DELIVERY_QTY)  AS DELIVERY_QTY,  SIB.MST_ID as BILL_ID,  SUM(SIB.DELIVERY_QTY) AS Bill_QTY,  DM.DELIVERY_DATE, OB.ORDER_ID
						FROM SUBCON_DELIVERY_MST DM
						INNER JOIN SUBCON_DELIVERY_DTLS DD
						ON DM.ID = DD.MST_ID
						LEFT JOIN SUBCON_ORD_BREAKDOWN OB
						ON DD.COLOR_SIZE_ID = OB.ID
						LEFT JOIN SUBCON_INBOUND_BILL_DTLS SIB
						ON DD.ID               = SIB.DELIVERY_ID
						WHERE DM.STATUS_ACTIVE = 1 AND DD.STATUS_ACTIVE   = 1 AND DM.DELIVERY_DATE < '$from_bill_date' $company_name_del_qty
						GROUP BY DM.COMPANY_ID,  DM.DELIVERY_NO,  DM.PARTY_ID,   DM.DELIVERY_DATE,   SIB.MST_ID,  OB.ORDER_ID";
			
			$sql_res_del_qty_pre=sql_select($del_qty_sql_pre);	 
			foreach($sql_res_del_qty_pre as $del_pre)
			{				
				$pre_del_qtyArr[$del_pre[csf('PARTY_ID')]][$del_pre[csf('ORDER_ID')]]+=$del_pre[csf('DELIVERY_QTY')];
			}
			
			
			$sql_bill_statement = "SELECT SBM.PARTY_ID, SBM.ID, SBM.BILL_NO,SBM.BILL_DATE, SUM(SBD.DELIVERY_QTY) AS DELIVERY_QTY,  AVG(SBD.RATE) AS RATE, SUM(SBD.AMOUNT) AS AMOUNT,
				SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,  POM.BUYER_NAME, POM.CURRENCY_ID
				FROM SUBCON_INBOUND_BILL_MST SBM
				INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
				ON SBM.ID = SBD.MST_ID
				LEFT JOIN SUBCON_DELIVERY_DTLS SDD
				ON SBD.DELIVERY_ID = SDD.ID
				INNER JOIN SUBCON_DELIVERY_MST SDM
				ON SDD.MST_ID = SDM.ID
				INNER JOIN SUBCON_ORD_MST SOM
				ON SDM.JOB_NO = SOM.EMBELLISHMENT_JOB
				INNER JOIN WO_PO_BREAK_DOWN PBD
				ON SBD.BUYER_PO_ID = PBD.ID
				INNER JOIN WO_PO_DETAILS_MASTER POM
				ON PBD.JOB_NO_MST       = POM.JOB_NO
				WHERE SBM.STATUS_ACTIVE = 1
				AND SBD.STATUS_ACTIVE   = 1
				$po_id_con
				$wo_cond
				$style_con
				$party_con
				$company_name	$date_cond_t $buyer_con			
				GROUP BY SBM.PARTY_ID, SBM.ID, SBM.BILL_NO, SBM.BILL_DATE, SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,  
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,POM.BUYER_NAME,  POM.CURRENCY_ID
				ORDER BY POM.STYLE_REF_NO, POM.BUYER_NAME, SBD.BUYER_PO_ID, SBM.BILL_DATE";	

			$sql_bill_statement_previous = "SELECT SBM.PARTY_ID, SBM.ID, SBM.BILL_NO,SBM.BILL_DATE, SUM(SBD.DELIVERY_QTY) AS DELIVERY_QTY,  AVG(SBD.RATE) AS RATE, SUM(SBD.AMOUNT) AS AMOUNT,
				SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,  POM.BUYER_NAME, POM.CURRENCY_ID
				FROM SUBCON_INBOUND_BILL_MST SBM
				INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
				ON SBM.ID = SBD.MST_ID
				LEFT JOIN SUBCON_DELIVERY_DTLS SDD
				ON SBD.DELIVERY_ID = SDD.ID
				INNER JOIN SUBCON_DELIVERY_MST SDM
				ON SDD.MST_ID = SDM.ID
				INNER JOIN SUBCON_ORD_MST SOM
				ON SDM.JOB_NO = SOM.EMBELLISHMENT_JOB
				INNER JOIN WO_PO_BREAK_DOWN PBD
				ON SBD.BUYER_PO_ID = PBD.ID
				INNER JOIN WO_PO_DETAILS_MASTER POM
				ON PBD.JOB_NO_MST       = POM.JOB_NO
				WHERE SBM.STATUS_ACTIVE = 1
				AND SBD.STATUS_ACTIVE   = 1
				AND sbm.bill_date < '$from_bill_date'
				$po_id_con
				$wo_cond
				$style_con
				$party_con
				$company_name $buyer_con			
				GROUP BY SBM.PARTY_ID, SBM.ID, SBM.BILL_NO, SBM.BILL_DATE, SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,  
				SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,POM.BUYER_NAME,  POM.CURRENCY_ID
				ORDER BY POM.STYLE_REF_NO, POM.BUYER_NAME, SBD.BUYER_PO_ID, SBM.BILL_DATE";
			
			$sql_result_bill=sql_select($sql_bill_statement);
			
			$sql_result_bill_previous=sql_select($sql_bill_statement_previous);
			foreach($sql_result_bill_previous as $bill_pre)
			{
				$pre_billArr[$bill_pre[csf('PARTY_ID')]][$bill_pre[csf('ORDER_ID')]][$bill_pre[csf('STYLE_REF_NO')]]+=$bill_pre[csf('DELIVERY_QTY')];
				
			}
			
			//echo $sql_bill_statement_previous; die;
	
			foreach($sql_result_bill as $bill)
			{
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["PARTY_ID"]=$bill[csf('PARTY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["ORDER_ID"]=$bill[csf('ORDER_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["STYLE_REF_NO"]=$bill[csf('STYLE_REF_NO')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["BUYER_PO_ID"]=$bill[csf('BUYER_PO_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["PARTY_LOCATION_ID"]=$bill[csf('PARTY_LOCATION_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["COLOR_SIZE_ID"]=$bill[csf('COLOR_SIZE_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["CURRENCY_ID"]=$bill[csf('CURRENCY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["PARTY_SOURCE"]=$bill[csf('PARTY_SOURCE')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["COMPANY_ID"]=$bill[csf('COMPANY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["ORDER_NO"]=$bill[csf('ORDER_NO')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["DELIVERY_QTY"]+=$bill[csf('DELIVERY_QTY')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["RATE"]=$bill[csf('RATE')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["AMOUNT"]+=$bill[csf('AMOUNT')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["BUYER_NAME"]=$bill[csf('BUYER_NAME')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]][$bill[csf('STYLE_REF_NO')]] ["B_ID"].=$bill[csf('id')].",";
				
				
				$billArr_style_total_qty[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]] += $bill[csf('DELIVERY_QTY')];				
				$billArr_style_total_amount[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]] += $bill[csf('AMOUNT')];
				
				$bill_style_total_qty[$bill[csf('STYLE_REF_NO')]]+=$bill[csf('DELIVERY_QTY')];				
				$bill_style_total_amount[$bill[csf('STYLE_REF_NO')]]+=$bill[csf('AMOUNT')];
				
				$party=$bill[csf('PARTY_ID')];
				$party_location=$bill[csf('PARTY_LOCATION_ID')];
				}
			//echo $billArr; die;
			//echo "<pre>";print_r($billArr);die;
			//echo "<pre>";print_r($item_data_total);die;
			
			
			$buyer_po_arr=array();
	
			$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
			$po_sql_res=sql_select($po_sql);
			
			$Del_Mst_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
			$po_sql_res=sql_select($Del_Mst_sql);
			
			
			
			foreach ($po_sql_res as $row)
			{
				$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
				$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
			}
			
			$sql_wo_qty ="select  sum(b.order_quantity) as order_quantity, b.order_no, b.order_id from  subcon_ord_mst a inner join subcon_ord_dtls b on a.id = b.mst_id group by b.order_id, b.order_no";
			
			$sql_wo_qty_res=sql_select($sql_wo_qty);
			
			
			
			foreach ($sql_wo_qty_res as $row_wo)
			{
				$wo_qty_arr[$row_wo[csf("order_id")]]+=$row_wo[csf("order_quantity")];
			}
			//echo "<pre>";print_r($wo_qty_arr);die;
			
			$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$del_mst=return_library_array( "select id, mst_id from subcon_delivery_dtls",'id','mst_id');
			$wo_no=return_library_array( "select distinct  order_id, order_no from subcon_ord_mst",'order_id','order_no');
			$wo_qty=return_library_array( "select b.id, sum(a.order_quantity) as order_quantity from subcon_ord_dtls a inner join subcon_ord_mst b on b.id = a.mst_id group by b.id",'id','order_quantity');
			$lib_location=return_library_array( "select id,  location_name from lib_location",'id','location_name');
			$lib_buyer=return_library_array( "select id,  buyer_name from lib_buyer",'id','buyer_name');
			//bill query end ***************************************************************************
			//echo "<pre>";print_r($lib_location);die;
			$value_width =1550;
			$del_qty_arr=return_library_array( "select c.order_id, sum(b.delivery_qty) as delivery_qty  from subcon_delivery_dtls b inner join subcon_ord_breakdown c on b.color_size_id    = c.id where b.status_active = 1 group by c.order_id", "order_id", "delivery_qty");
			
	?>
		<div align = "center" style="width:<? echo $value_width + 33; ?>px;"><br>
            <table width="<? echo $value_width+20; ?>">
				<tr>
					<td align="center" style="font-size:36px;"><p><strong>Company Name : <? echo "Mercer Design Tex Ltd." ?></p></strong></td>
					
				</tr>
				<tr>
					<td align="center" style="font-size:24px;"><strong>Party Name : <? echo $comp[$party]; echo '/ln';?></strong><strong>Party Location :<?  echo  $lib_location[$party_location];  ?></strong></td>
				</tr>
            </table>
			<br>
		</div>
        <div align = "center" style="width:<? echo $value_width + 33; ?>px;">
			<fieldset style="width:<? echo $value_width + 28; ?>px;"> 	
				<table width="<? echo $value_width+20; ?>" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr>
							<th width="35">SL</th>
							<th width="180">Style</th>
							<th width="130">Buyer</th>
							<th width="120">WO No</th>
							<th width="100">WO Qty(Dzn)</th>
							<th width="100">Previous Del Qty(Pcs)</th>
							<th width="100">Previous Del Qty(Dzn)</th>
							<th width="100">Curr Del Qty(Pcs)</th>
							<th width="100">Curr Del Qty(Dzn)</th>
							<th width="100">Pre Bill Qty(Dzn)</th>
							<th width="100">Cur. Bill Qty(Dzn)</th>
							<th width="100">Total Bill Qty(Dzn)</th>
							<th width="60">UOM</th>
							<th width="80">Cur. Rate(Avg)</th>						
							<th>Cur. Amount</th>
						</tr>
					</thead>
				</table>
				<div style="width:<? echo $value_width + 28; ?>px; max-height:350px; overflow-y:auto;" id="scroll_body" >
					<table  width="<? echo $value_width+20; ?>" cellspacing="0" border="1" class="rpt_table"  rules="all" id="table_body" >
						<tbody>
							<?  	
							$i=1;
							foreach($billArr as $party_id=>$party_data)
							{
								foreach($party_data as $wo_id=>$wo_data)
								{
									foreach($wo_data as $style_id=>$row)
									{
										//foreach($style_data as $po_id=>$row)  //[$party_id][$wo_id][$style_id]
										//{
											/*$unique = array_unique($row[csf('B_ID')]);
											//echo $unique;die;
											$biil_id=$row[csf('B_ID')]."'0'";
											//echo $biil_id;
											
											
												//-- AND SIB.MST_ID in ($biil_id) 			
											$sql_del_qty = "SELECT SOM.ORDER_ID,  SOM.ORDER_NO,  SUM(SDD.DELIVERY_QTY) AS DELIVERY_QTY,  SDM.DELIVERY_NO,  SBM.BILL_NO,  SBM.ID as Bill_ID,  SOM.PARTY_ID
																FROM SUBCON_INBOUND_BILL_MST SBM
																INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
																ON SBM.ID = SBD.MST_ID
																LEFT JOIN SUBCON_DELIVERY_DTLS SDD
																ON SBD.DELIVERY_ID = SDD.ID
																INNER JOIN SUBCON_DELIVERY_MST SDM
																ON SDD.MST_ID = SDM.ID
																INNER JOIN SUBCON_ORD_MST SOM
																ON SDM.JOB_NO           = SOM.EMBELLISHMENT_JOB
																WHERE SBM.STATUS_ACTIVE = 1
																AND SBD.STATUS_ACTIVE   = 1
																AND SBM.ID in ($biil_id)
																GROUP BY SOM.ORDER_ID,SOM.ORDER_NO,SDM.DELIVERY_NO,SBM.BILL_NO,SBM.ID, SOM.PARTY_ID";  
											
											$sql_res_del_qty=sql_select($sql_del_qty);	 
											foreach($sql_res_del_qty as $del)
											{				
												$del_qtyArr[$del[csf('PARTY_ID')]][$del[csf('ORDER_ID')]]+=$del[csf('DELIVERY_QTY')];
											}
											//echo $del_qty_sql;
											//echo "<pre>";print_r($del_qtyArr);die;
											*/
											$tt_bil=($pre_billArr[$party_id][$wo_id][$style_id]/12) + ($row[csf('DELIVERY_QTY')]/12);
											$w_t=$wo_qty_arr[$wo_id];
											if ($w_t < $tt_bil)
											{
												$bgcolor_t="#E9C3CF"; 
											}
											else {$bgcolor_t="#FFFFFF";}
											
											if ($i%2==0) $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
											?>  
												  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
							
													<td width="35" align="center"><? echo $i;?></td>
													<td width="180" style="word-break: break-all;"><? echo $buyer_po_arr[$row[csf('BUYER_PO_ID')]]['style'];?></td>
													<td width="130"  style="word-break: break-all;"><? echo $lib_buyer[$row[csf('BUYER_NAME')]];?></td>
													<td width="120"  style="word-break: break-all;"><? echo $wo_no[$row[csf('ORDER_ID')]];?></td>	
													<td align="right" width="100"  style="word-break: break-all;"><? echo  number_format($wo_qty_arr[$wo_id],2);?></td>
													<td align="right" width="100"  style="word-break: break-all;"><? echo  number_format($pre_del_qtyArr[$party_id][$wo_id],0);?></td>
													<td align="right" width="100"  style="word-break: break-all;"><? echo  number_format($pre_del_qtyArr[$party_id][$wo_id]/12,2);?></td>
													<td align="right" width="100"  style="word-break: break-all;"><? echo  number_format($del_qtyArr[$party_id][$wo_id],0);?></td>
													<td align="right" width="100"  style="word-break: break-all;"><? echo  number_format($del_qtyArr[$party_id][$wo_id]/12,2);?></td>
													<td align="right" width="100"><? echo number_format($pre_billArr[$party_id][$wo_id][$style_id]/12,2);?></td>
													<td align="right" width="100"><? echo number_format($row[csf('DELIVERY_QTY')]/12,2);?></td>
													<td align="right" width="100" bgcolor="<? echo $bgcolor_t; ?>"><? echo number_format(($pre_billArr[$party_id][$wo_id][$style_id]/12) + $row[csf('DELIVERY_QTY')]/12,2); ?></td>
													<td align="center" width="60"><? echo "Dzn";?></td>
													<td align="center" width="80"><? echo number_format(($row[csf('AMOUNT')])/($row[csf('DELIVERY_QTY')]/12),2);?></td>
													<td align="right"><? echo number_format($row[csf('AMOUNT')],2);?></td>
												  </tr>
											<?		
											$i++;					
											$wo_qty_total+=$wo_qty_arr[$wo_id];
											$party_total_bill_qty+=$row[csf('DELIVERY_QTY')]/12;
											$del_total_qty+=$del_qtyArr[$party_id][$wo_id]/12;
											$del_total_qty_pcs+=$del_qtyArr[$party_id][$wo_id];
											$pre_del_total_qty_pcs+=$pre_del_qtyArr[$party_id][$wo_id];
											$pre_del_total_qty_dzn+=$pre_del_qtyArr[$party_id][$wo_id]/12;
											$pre_total_bill_qty +=$pre_billArr[$party_id][$wo_id][$style_id]/12;
											$party_total_amount+=$row[csf('AMOUNT')];
											$t_bill+=$tt_bil;
										//}
									}
											
								}					 	
							}
						  ?>
						  </tbody>
							<tfoot>
								<tr> 
									<td align="right" colspan=4><strong>Grand total :</strong></td>
									<td align="right"><strong><? echo number_format($wo_qty_total,2);?></strong></td>
									<td align="right"><strong><? echo number_format($pre_del_total_qty_pcs,0);?></strong></td>
									<td align="right"><strong><? echo number_format($pre_del_total_qty_dzn,2);?></strong></td>
									<td align="right"><strong><? echo number_format($del_total_qty_pcs,0);?></strong></td>
									<td align="right"><strong><? echo number_format($del_total_qty,2);?></strong></td>
									<td align="right"><strong><? echo number_format($pre_total_bill_qty,2);?></strong></td>
									<td align="right"><strong><? echo number_format($party_total_bill_qty,2);?></strong></td>
									<td align="right"><strong><? echo number_format($t_bill,2);?></strong></td>
									<td align="right"></td>
									<td align="center"><strong><? echo number_format(($party_total_amount/$party_total_bill_qty), 2);?></strong></td>
									<td align="right"><strong><? echo number_format($party_total_amount,2);?></strong></td>					
								</tr>
							</tfoot>
					</table>
				 </div>
			</fieldset>
        </div>
		  
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
	
	//echo "Good Now";
}

if($action=="bill_statement_report_generate_buyer")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_wo_order_no=str_replace("'","",trim($txt_wo_order_no));
	//$txt_date_from=str_replace("'","",trim($txt_date_from));
	//echo $txt_date_from; die;
	$cbo_year=str_replace("'","",$cbo_year);
	
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");


	if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and SBM.COMPANY_ID =$cbo_company_id";
	
	if(str_replace("'","",$cbo_buyer_id)==0)$buyer_con="";else $buyer_con=" AND POM.BUYER_NAME=$cbo_buyer_id";
	if(str_replace("'","",$cbo_party_id)==0)$party_con="";else $party_con=" AND SBM.PARTY_ID =$cbo_party_id";
	if(str_replace("'","",$cbo_location_id)==0)$location_con="";else $location_con=" and a.location_id =$cbo_location_id";
	
	if(str_replace("'","",$cbo_party_location_id)==0)$party_location_con="";else $party_location_con=" and a.party_location =$cbo_party_location_id";
	
	
	if(str_replace("'","",$txt_buyer_po)=='')$po_con="";else $po_con=" and d.po_number = $txt_buyer_po";
	if(str_replace("'","",$txt_buyer_po_id)==0)$po_id_con="";else $po_id_con=" AND SBD.BUYER_PO_ID = $txt_buyer_po_id";
	if(str_replace("'","",$txt_style_ref)=='')$style_con="";else $style_con=" and POM.STYLE_REF_NO like('%".trim(str_replace("'","",$txt_style_ref))."%')";
	//if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	if(str_replace("'","",trim($txt_job_no))!="") $job_cond="and a.embellishment_job = $txt_job_no";  	
	if($txt_wo_order_no!="") $wo_cond=" AND SOM.ORDER_NO like '%".$txt_wo_order_no."%'";  	
	
	//if(str_replace("'","",trim($txt_date_from))=="")$date_con=""; else $date_con=" and b.production_date = $txt_date_from";
	//if(str_replace("'","",trim($txt_date_to))=="")$date_con="";   else $date_con=" and b.production_date = $txt_date_to";
	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_range_cond=""; else $date_range_cond=" HAVING SBM.BILL_DATE BETWEEN $txt_date_from and $txt_date_to";	
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond_t=""; else $date_cond_t="   AND SBM.BILL_DATE BETWEEN  $txt_date_from and $txt_date_to";
	
	
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

	ob_start();
	
			// Bill query start ***************************************************************************
				/*$sql_bill = "SELECT SBM.ID,  SBM.BILL_NO, SBM.PARTY_ID, SBM.BILL_DATE, SBM.REMARKS,  SUM(SBD.DELIVERY_QTY) AS DELIVERY_QTY,
				  AVG(SBD.RATE)         AS RATE,  SUM(SBD.AMOUNT)       AS AMOUNT,  SBD.DELIVERY_ID,  SBM.PARTY_SOURCE,  SBM.COMPANY_ID,
				  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SDM.DELIVERY_DATE,  SDM.DELIVERY_NO,  SOM.ORDER_ID, SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB
				FROM SUBCON_INBOUND_BILL_MST SBM
				INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
				ON SBM.ID = SBD.MST_ID
				LEFT JOIN SUBCON_DELIVERY_DTLS SDD
				ON SBD.DELIVERY_ID = SDD.ID
				INNER JOIN SUBCON_DELIVERY_MST SDM
				ON SDD.MST_ID = SDM.ID
				INNER JOIN SUBCON_ORD_MST SOM
				ON SDM.JOB_NO           = SOM.EMBELLISHMENT_JOB
				WHERE SBM.STATUS_ACTIVE = 1
				AND SBD.STATUS_ACTIVE   = 1 $wo_cond $company_name 
				GROUP BY SBM.ID,  SBM.BILL_NO,SBM.PARTY_ID, SBM.BILL_DATE,SBM.REMARKS, SBD.DELIVERY_ID,SBM.PARTY_SOURCE,SBM.COMPANY_ID,
				  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID, SDM.DELIVERY_DATE, SDM.DELIVERY_NO, SOM.ORDER_ID, SOM.ORDER_NO, SOM.EMBELLISHMENT_JOB 
				  $date_cond_t $po_id_con $party_con";*/
			
			$sql_bill_statement = "SELECT SBM.PARTY_ID,  SBM.ID,  SBM.BILL_NO,  SBM.BILL_DATE,  SUM(SBD.DELIVERY_QTY) AS DELIVERY_QTY,  AVG(SBD.RATE)         AS RATE,
					  SUM(SBD.AMOUNT)       AS AMOUNT,  SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,  SOM.ORDER_ID,  SOM.ORDER_NO,
					  SOM.EMBELLISHMENT_JOB,  SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,  POM.CURRENCY_ID,  SOB.COLOR_ID,  POM.BUYER_NAME,  POM.JOB_NO_PREFIX_NUM
					FROM SUBCON_INBOUND_BILL_MST SBM
					INNER JOIN SUBCON_INBOUND_BILL_DTLS SBD
					ON SBM.ID = SBD.MST_ID
					LEFT JOIN SUBCON_DELIVERY_DTLS SDD
					ON SBD.DELIVERY_ID = SDD.ID
					INNER JOIN SUBCON_DELIVERY_MST SDM
					ON SDD.MST_ID = SDM.ID
					INNER JOIN SUBCON_ORD_MST SOM
					ON SDM.JOB_NO = SOM.EMBELLISHMENT_JOB
					INNER JOIN WO_PO_BREAK_DOWN PBD
					ON SBD.BUYER_PO_ID = PBD.ID
					INNER JOIN WO_PO_DETAILS_MASTER POM
					ON PBD.JOB_NO_MST = POM.JOB_NO
					INNER JOIN SUBCON_ORD_BREAKDOWN SOB
					ON SBD.COLOR_SIZE_ID      = SOB.ID
					WHERE SBM.STATUS_ACTIVE   = 1
					AND SBD.STATUS_ACTIVE     = 1
					$po_id_con	$wo_cond $style_con	$party_con	$company_name	 $date_cond_t $buyer_con
					GROUP BY SBM.PARTY_ID,  SBM.ID,  SBM.BILL_NO,  SBM.BILL_DATE,  SBM.PARTY_SOURCE,  SBM.COMPANY_ID,  SBD.BUYER_PO_ID,  SBD.COLOR_SIZE_ID,
					  SOM.ORDER_ID,  SOM.ORDER_NO,  SOM.EMBELLISHMENT_JOB,  SBM.PARTY_LOCATION_ID,  POM.STYLE_REF_NO,  POM.CURRENCY_ID,  SOB.COLOR_ID,
					  POM.BUYER_NAME,  POM.JOB_NO_PREFIX_NUM
					  ORDER BY POM.STYLE_REF_NO, SBD.BUYER_PO_ID, SBM.BILL_DATE";			 
			
			$sql_result_bill=sql_select($sql_bill_statement);
			
			//echo $sql_bill_statement; die;
	
			foreach($sql_result_bill as $bill)
			{
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["PARTY_ID"]=$bill[csf('PARTY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["ORDER_ID"]=$bill[csf('ORDER_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["BUYER_NAME"]=$bill[csf('BUYER_NAME')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["STYLE_REF_NO"]=$bill[csf('STYLE_REF_NO')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["BUYER_PO_ID"]=$bill[csf('BUYER_PO_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["BILL_NO"]=$bill[csf('BILL_NO')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["PARTY_LOCATION_ID"]=$bill[csf('PARTY_LOCATION_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["COLOR_SIZE_ID"]=$bill[csf('COLOR_SIZE_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["CURRENCY_ID"]=$bill[csf('CURRENCY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["PARTY_SOURCE"]=$bill[csf('PARTY_SOURCE')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["COMPANY_ID"]=$bill[csf('COMPANY_ID')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["EMBELLISHMENT_JOB"]=$bill[csf('EMBELLISHMENT_JOB')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["ORDER_NO"]=$bill[csf('ORDER_NO')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["DELIVERY_QTY"]+=$bill[csf('DELIVERY_QTY')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["RATE"]=$bill[csf('RATE')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["AMOUNT"]+=$bill[csf('AMOUNT')];
				$billArr[$bill[csf('PARTY_ID')]][$bill[csf('BUYER_NAME')]][$bill[csf('ORDER_ID')]][$bill[csf('ID')]] [$bill[csf('BUYER_PO_ID')]]["BILL_DATE"]=$bill[csf('BILL_DATE')];
				
				$billArr_style_total_qty[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]] += $bill[csf('DELIVERY_QTY')];				
				$billArr_style_total_amount[$bill[csf('PARTY_ID')]][$bill[csf('ORDER_ID')]] += $bill[csf('AMOUNT')];
				
				$bill_style_total_qty[$bill[csf('BUYER_NAME')]]+=$bill[csf('DELIVERY_QTY')];				
				$bill_style_total_amount[$bill[csf('BUYER_NAME')]]+=$bill[csf('AMOUNT')];
			}
			//echo $billArr; die;
			//echo "<pre>";print_r($billArr);die;
			//echo "<pre>";print_r($item_data_total);die;
			$buyer_po_arr=array();
	
			$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
			$po_sql_res=sql_select($po_sql);
			
			$Del_Mst_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
			$po_sql_res=sql_select($Del_Mst_sql);
			
			foreach ($po_sql_res as $row)
			{
				$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
				$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
			}
			$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
			$Del_Mst=return_library_array( "select id, MST_ID from SUBCON_DELIVERY_DTLS",'id','MST_ID');
			$WO_No=return_library_array( "select DISTINCT  ORDER_ID, ORDER_NO from SUBCON_ORD_MST",'ORDER_ID','ORDER_NO');
			$lib_location=return_library_array( "SELECT ID,  LOCATION_NAME FROM LIB_LOCATION",'ID','LOCATION_NAME');
			//Bill query end ***************************************************************************
			//echo "<pre>";print_r($lib_location);die;
	?>
        <div style="width:1300px">
            <table width="1300px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
                <thead>
                    <tr>
                        <th width="35">SL</th>
						<th width="100">Buyer</th>
						<th width="150">Style</th>
						<th width="100">Po No</th>
						<th width="100">WO No</th>						
						<th width="130">Bill No</th>
						<th width="90">Bill Date</th>
						<th width="100">Bill Qty</th>
						<th width="70">Currency</th>
						<th width="60">Rate</th>
						<th width="100">Amount</th>
						<th width="150">Party</th>
						<th >Party Location</th>
                    </tr>
                </thead>
            </table>
			<div style="max-height:350px; width:1300px; overflow-y:auto;" id="scroll_body" >
                <table cellspacing="0" border="1" class="rpt_table"  width="1300px" rules="all" id="table_body" >
                <tbody>
					<?  	
					$i=1;
					foreach($billArr as $party_id=>$party_data)
					{
						foreach($party_data as $buyer_id=>$buyer_data)
						{
							foreach($buyer_data as $wo_id=>$wo_data)
							{
								foreach($wo_data as $bill_id=>$bill_data)
								{
									foreach($bill_data as $po_id=>$row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
										
										?>  
											  <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
						
												<td width="35" align="center"><? echo $i;?></td>
												<td width="100"><? echo $buyer_arr[$row[csf('BUYER_NAME')]];?></td>
												<td width="150" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_po_arr[$row[csf('BUYER_PO_ID')]]['style'];?></td>
												<td width="100"><? echo $buyer_po_arr[$row[csf('BUYER_PO_ID')]]['po'];?></td>
												<td width="100"><? echo $WO_No[$row[csf('ORDER_ID')]];?></td>	
												<td width="130"><a href ="#" onClick="print_report('<? echo $row[csf('COMPANY_ID')].'*'. $row[csf('ID')];?>','embl_bill_issue_print','../../embellishment/delivery/requires/embl_bill_issue_controller')"> <? echo $row[csf('BILL_NO')];?></a></td>
												<td width="90"><? echo $row[csf('BILL_DATE')];?></td>
												<td align="right" width="100"><? echo $row[csf('DELIVERY_QTY')];?></td>
												<td align="right" width="70"><? echo $currency[$row[csf('CURRENCY_ID')]];?></td>
												<td align="center" width="60"><? echo number_format($row[csf('RATE')],2);?></td>
												<td align="right" width="100"><? echo $row[csf('AMOUNT')];?></td>
												<td width="150"><? echo $comp[$row[csf('PARTY_ID')]];?></td>
												<td><? echo $lib_location [$row[('PARTY_LOCATION_ID')]];?></td>
											  </tr>
										<?		
										$i++;							
										$party_total_bill+=$row[csf('DELIVERY_QTY')];
										$party_total_amount+=$row[csf('AMOUNT')];
									}
								}	
							}
								?>
										<tr> 
											<td align="right"  colspan="7"><strong>Buyer total :</strong></td>
											<td align="right" width="100"><strong><? echo $bill_style_total_qty[$buyer_id];?></strong></td>
											<td align="center" width="70"></td>
											<td align="center" width="60"><strong><? echo number_format($bill_style_total_amount[$buyer_id] /$bill_style_total_qty[$buyer_id] , 4,'.','');?></strong></td>
											<td align="right"  width="100"><strong><? echo $bill_style_total_amount[$buyer_id] ;?></strong></td>
											<td width="150"></td>
											<td ></td>						
										</tr>
									<?						
								
									
						}	
						
					 	
					}
                  ?>
                  </tbody>
                    <tfoot>
                    <tr> 
						<td align="right"  colspan="7"><strong>Grand total :</strong></td>
						<td align="right" width="100"><strong><? echo $party_total_bill;?></strong></td>
						<td align="center" width="70"></td>
						<td align="center" width="60"><strong><? echo number_format($party_total_amount/$party_total_bill, 4,'.','');?></strong></td>
						<td align="right"  width="100"><strong><? echo $party_total_amount;?></strong></td>
						<td width="150"></td>
						<td ></td>						
                	</tr>
                    </tfoot>
                </table>
             </div>
           </div>
		  
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
	
	//echo "Good Now";
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