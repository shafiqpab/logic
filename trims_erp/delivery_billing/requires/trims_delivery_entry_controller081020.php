<?
include('../../../includes/common.php'); 
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else $dropdown_name="cbo_party_location";
	$location_arr=return_library_array( "select id, location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name",'id','location_name');
	if(count($location_arr)==1) $selected = key($location_arr); else $selected=0;
	echo create_drop_down( $dropdown_name, 150, $location_arr,"", 1, "-- select Location --", $selected, "",1 );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	//echo $data[2];
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- select Party --", $data[2], "" );
	}	
	exit();	 
} 


if ($action=="devivery_workorder_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
	//echo $data; die;
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			var within_group = $('#cbo_within_group').val();
			load_drop_down( 'trims_delivery_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('Receive System ID');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
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
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="100">Within Group</th>                           
                    <th width="140">Party Name</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_by_td">Receive System ID</th>
                    <th width="100">Year</th>
                    <th width="170">Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
                        <? 
                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
                    </td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- select Party --", $data[2], "fnc_load_party_popup(1,this.value);" );   	 
                        ?>
                    </td>
                    <td>
						<?
                           $search_by_arr=array(1=>"Receive System ID",2=>"W/O No",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_del_workorder_list_view', 'search_div', 'trims_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="middle">
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
                </tbody>
            </table>    
            </form>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}
	
if($action=="create_del_workorder_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please select Company First."; die; }
	//echo $search_type; die;
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";  
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";   
		}
	}	

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	/*if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids;
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";*/
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_po_arr=array();
	if($within_group==1)
	{
		$po_sql ="select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}
	$buyer_po_id_str=""; $buyer_po_no_str=""; $buyer_po_style_str="";
	if($db_type==0) 
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str=",group_concat(c.color_id) as color_id";
		if($within_group==1)
		{
			$buyer_po_id_str=",group_concat(b.buyer_po_id) as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=",group_concat(b.buyer_po_no) as buyer_po_id";
			$buyer_po_style_str=",group_concat(b.buyer_style_ref) as buyer_style";
		}
	}
	/*else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str=",listagg(c.color_id,',') within group (order by c.color_id) as color_id";
		
		if($within_group==1)
		{
			$buyer_po_id_str=",listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)  as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=",listagg(b.buyer_po_no,',') within group (order by b.id) as buyer_po_no";
			$buyer_po_style_str=",listagg(b.buyer_style_ref,',') within group (order by b.id) as buyer_style";
		}
	}*/
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		//$color_id_str=",listagg(c.color_id,',') within group (order by c.color_id) as color_id";
		$color_id_str=",rtrim(xmlagg(xmlelement(e,c.color_id,',').extract('//text()') order by c.color_id).GetClobVal(),',') as color_id";
		if($within_group==1)
		{
			//$buyer_po_id_str=",listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)  as buyer_po_id";
			
			$buyer_po_id_str=",rtrim(xmlagg(xmlelement(e,b.buyer_po_id,',').extract('//text()') order by b.buyer_po_id).GetClobVal(),',') as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=",rtrim(xmlagg(xmlelement(e,b.buyer_po_no,',').extract('//text()') order by b.buyer_po_no).GetClobVal(),',') as buyer_po_no";
			$buyer_po_style_str=",rtrim(xmlagg(xmlelement(e,b.buyer_style_ref,',').extract('//text()') order by b.buyer_po_id).GetClobVal(),',') as buyer_style_ref";
			
			//$buyer_po_no_str=",listagg(b.buyer_po_no,',') within group (order by b.id) as buyer_po_no";
			//$buyer_po_style_str=",listagg(b.buyer_style_ref,',') within group (order by b.id) as buyer_style";
		}
	}
	
	$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date $color_id_str $buyer_po_id_str $buyer_po_no_str $buyer_po_style_str
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c , trims_production_mst d  
	where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.id=d.received_id and a.status_active=1 and b.status_active=1 and d.status_active=1 $order_rcv_date $company $party_id_cond $search_com_cond  $withinGroup $year_cond and b.id=c.mst_id  
	group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date
	order by a.id DESC";
	//echo $sql;

	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th>Color</th>
        </thead>
        </table>
        <div style="width:885px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="865" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
			
			
				$color=$row[csf('color_id')];
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$color_name="";	
				//echo $excolor_id."ghgh"; 
				if($db_type==2) $color = $color->load();
				$color=array_unique(explode(",",$color));
				//echo $excolor_id ; 
				
				foreach ($color as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=', '.$color_arr[$color_id];
				}
				
				
				if($within_group==1)
				{
					$buyer_po=""; $buyer_style="";
					//$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
					$buyer_po_id=$row[csf('buyer_po_id')];
					if($db_type==2) $buyer_po_id = $buyer_po_id->load();
					$buyer_po_id=array_unique(explode(",",$buyer_po_id));
					
					foreach($buyer_po_id as $po_id)
					{
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					}
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				}
				else
				{
					$buyer_po=implode(",",array_unique(explode(",",$row[csf('buyer_po_no')])));
					$buyer_style=implode(",",array_unique(explode(",",$row[csf('buyer_style')])));
				}
			
			
               /* if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				if($within_group==1)
				{
					$buyer_po=""; $buyer_style="";
					$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
					foreach($buyer_po_id as $po_id)
					{
						//$name[csf('id')]= $name[csf('id')]->load();
						
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					}
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				}
				else
				{
					$buyer_po=implode(",",array_unique(explode(",",$row[csf('buyer_po_no')])));
					$buyer_style=implode(",",array_unique(explode(",",$row[csf('buyer_style')])));
				}*/
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('subcon_job')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td style="word-break:break-all"><? echo $color_name; ?></td>
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
 
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, subcon_job, company_id, location_id, party_id, currency_id, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no,exchange_rate,remarks from subcon_ord_mst where subcon_job='$data' and entry_form=255 and status_active=1" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('received_id').value          	= ".$row[csf("id")].";\n";
		//echo "document.getElementById('txt_job_no').value 				= '".$row[csf("subcon_job")]."';\n";  
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "fnc_load_party(1,".$row[csf("within_group")].");\n";	
	//echo "load_drop_down( 'requires/trims_delivery_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";	
		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";
	//echo "load_drop_down( 'requires/trims_delivery_entry_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('cbo_currency').value				= '".$row[csf("currency_id")]."';\n";
		echo "fnc_load_party(2,".$row[csf("within_group")].");\n";	 
		echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n";	
		//echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		echo "document.getElementById('hid_order_id').value          	= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$row[csf("order_no")]."';\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		echo "$('#cbo_currency').attr('disabled','true')".";\n";
		
		//echo "set_button_status(0,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	}
	exit();	
}

if( $action=='dalivery_order_dtls_list_view_old' ) 
{
	$data=explode('_',$data);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4 and status_active=1",'id','item_name');
	$tblRow=0;
	//die;
	//print_r($data);
	if($data[0]==1)
	{
		$sql = "select a.id, a.mst_id, a.job_no_mst, a.order_id, a.order_no, a.buyer_po_id, a.booking_dtls_id,b.id as receive_dtls_id, b.qnty as order_quantity , a.order_uom,a.booked_uom, a.booked_conv_fac, b.rate, a.amount, a.delivery_date, a.buyer_po_no, a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.rate_domestic,  a.amount_domestic,b.item_id, b.color_id, b.size_id,b.description,b.id as break_id,b.book_con_dtls_id from subcon_ord_dtls a,subcon_ord_breakdown b where   a.id=b.mst_id and a.job_no_mst=b.job_no_mst and a.job_no_mst='$data[1]' and a.mst_id='$data[3]'  and a.order_quantity<>0 and a.booked_qty<>0 and a.status_active=1 and a.is_deleted=0  order by a.id ASC";
	}
	else
	{
		$sql = "select a.id,a.mst_id,a.job_no_mst,a.order_id,a.order_no,a.buyer_po_id,a.booking_dtls_id,b.id AS receive_dtls_id,b.qnty AS order_quantity,a.order_uom,a.booked_uom,a.booked_conv_fac,b.rate,a.amount,a.delivery_date,a.buyer_po_no,a.buyer_style_ref,a.buyer_buyer,a.section,a.item_group AS trim_group,a.rate_domestic,a.amount_domestic,b.item_id,b.color_id,b.size_id,b.description,b.id AS break_id,b.book_con_dtls_id,c.delevery_qty,c.claim_qty,c.id AS delDtlsId,c.size_name,c.color_name,c.remarks,c.status_active,c.no_of_roll_bag FROM subcon_ord_dtls a ,subcon_ord_breakdown b LEFT JOIN trims_delivery_dtls c ON c.break_down_details_id = b.id AND c.mst_id ='$data[1]' and c.status_active<>0 WHERE a.id = b.mst_id and a.job_no_mst=b.job_no_mst and a.mst_id='$data[3]'";
	}
	//echo $sql;
	$rcv_result =sql_select($sql); 
	$variable_status=return_field_value("production_update_area","variable_setting_trim_prod","company_name='$data[4]' and variable_list =1 and is_deleted = 0 and status_active = 1");
	if($variable_status==2)
	{
		//$field_array3="id,mst_id,qc_pass_qty,reject_qty,remarks,buyer_po_no,receive_details_id, break_dtls_id,buyer_po_id,order_id,job_id, job_dtls_id,receive_id";
		$production_sql ="select c.break_dtls_id,sum(c.qc_pass_qty) as qc_pass_qty  from trims_production_mst a, trims_production_dtls b, trims_prod_order_breakdown c where a.id=b.mst_id and b.id=c.mst_id and a.received_id=$data[3] and c.receive_id=$data[3] and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by c.break_dtls_id";
		$production_sql_res=sql_select($production_sql);
		$production_qty_arr=array();
		foreach ($production_sql_res as $row){
			$brkIds=explode(",",$row[csf("break_dtls_id")]);
			foreach ($brkIds as $key => $value) {
				$production_qty_arr[$value]['qc_pass_qty']=$row[csf("qc_pass_qty")];
			}
		}
	}
	//echo count($production_qty_arr);
	if($variable_status==1 || count($production_qty_arr)<1)
	{
		$break_qty_arr=array();
		foreach ($rcv_result as $row){
			$break_qty_arr[$row[csf("break_id")]]['order_quantity']=$row[csf("order_quantity")];
		}
		
		$production_sql ="select b.id,b.break_id,b.qc_qty from trims_production_mst a, trims_production_dtls b where a.id=b.mst_id and a.received_id=$data[3] and a.status_active=1 and b.status_active=1 and b.is_deleted=0"; 
		
		$production_sql_res=sql_select($production_sql); $production_arr=array(); $break_ids=''; $production_percent=''; $break_qty_sum_arr=array(); $production_percent=''; $tblRow=0;
		foreach ($production_sql_res as $row)
		{
			$break_ids=explode(",",$row[csf("break_id")]); $order_quantity='';
			for($i=0; $i<count($break_ids);$i++)
			{
				//echo $break_ids[$i]."==".$row[csf("qc_qty")]."++";
				$production_arr[$break_ids[$i]]['qc_qty']=$row[csf('qc_qty')];
				$order_quantity+=$break_qty_arr[$break_ids[$i]]['order_quantity'];
			}
			
			$production_percent=($row[csf("qc_qty")]*100)/$order_quantity;
			for($i=0; $i<=count($break_ids);$i++)
			{
				$break_qty_sum_arr[$break_ids[$i]]['cal_qty']+=($production_percent*$break_qty_arr[$break_ids[$i]]['order_quantity'])/100;
			}
			unset($production_sql_res);
		}
		//echo "<pre>";
		//print_r($break_qty_sum_arr);
	}
	$delevery_qty_trims_arr=array();
	$pre_sql ="select break_down_details_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by break_down_details_id";
	$pre_sql_res=sql_select($pre_sql);
	foreach ($pre_sql_res as $row)
	{
		$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']=$row[csf("delevery_qty")];
	}
	unset($pre_sql_res);
	$trims_groups_arr=array();
	$trim_sql ="select id, item_group,buyer_buyer ,booked_conv_fac from subcon_ord_dtls where status_active=1 and is_deleted=0 and item_group is not null ";
	$trim_sql_res=sql_select($trim_sql);
	foreach ($trim_sql_res as $row){
		$trims_groups_arr[$row[csf("id")]]['item_group']=$item_group_arr[$row[csf("item_group")]];
		$trims_groups_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
		$trims_groups_arr[$row[csf("id")]]['booked_conv_fac']=$row[csf("booked_conv_fac")];
	}
	unset($trim_sql_res); $convertedBQty=''; $shipStatus=0;
	if($data[0]==1)
	{
		foreach($rcv_result as  $row)
		{
			$tblRow++; $prev_qty='';
			//echo $variable_status.'nnn';
			if($variable_status==2){
				$convertedBQty= $production_qty_arr[$row[csf('break_id')]]['qc_pass_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
			}
			else{
				$convertedBQty= $break_qty_sum_arr[$row[csf('break_id')]]['cal_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
			}

			/************** This condition is for few days . Previous data syncronized .****************/
			if($variable_status==2 && $convertedBQty==''){
				$convertedBQty= $break_qty_sum_arr[$row[csf('break_id')]]['cal_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
			}
			if(is_nan($convertedBQty)) $convertedBQty=0; else $convertedBQty = $convertedBQty;
			$orderQuantity= $row[csf('order_quantity')];
			$del_bal=($orderQuantity-($delevery_qty_trims_arr[$row[csf("break_id")]]['delevery_qty']));
			if($orderQuantity*1==$del_bal*1)
			{$shipStatus=1;} 
			if($orderQuantity*1>$del_bal*1)
			{ $shipStatus=2;} 
			if($del_bal*1==0)
			{$shipStatus=3;}
			//echo $delevery_qty_trims_arr[$row[csf("break_id")]]['delevery_qty'];
			if($delevery_qty_trims_arr[$row[csf("break_id")]]['delevery_qty']!=''){
				$prev_qty= number_format($delevery_qty_trims_arr[$row[csf("break_id")]]['delevery_qty'],4,'.','');}else{ $prev_qty=0;} 
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<td><? echo $row[csf('buyer_po_no')]; ?></td>
				<td><? echo $trims_section[$row[csf('section')]]; ?></td>
				<td><? echo $trims_groups_arr[chop($row[csf('id')],",")]['item_group']; ?></td>		
				<td><? echo $row[csf('description')]; ?></td>
				<td><? echo $color_library[$row[csf('color_id')]] ?></td>
				<td><? echo $size_arr[$row[csf('size_id')]] ?></td>
				<td><? echo $unit_of_measurement[$row[csf('order_uom')]] ?></td>
				<td align="right" id="txtWorkOrderQuantity_<? echo $tblRow; ?>"  title="<?= $orderQuantity; ?>"><? echo number_format($orderQuantity,4,'.',''); ?></td>
				<td align="right" id="txtOrderQuantity_<? echo $tblRow; ?>" title="<?= number_format($convertedBQty,4,'.','');?>"><? echo number_format($convertedBQty,4,'.',''); ?></td>
				<td align="right" id="txtPrevQty_<? echo $tblRow; ?>" title="<?= $prev_qty;?>"><? echo $prev_qty; ?></td>
                
				<td align="right"><input id="txtCurQty_<? echo $tblRow; ?>" name="txtCurQty[]" onKeyUp="cal_values(<? echo $tblRow; ?>);" value="" type="text" style="width:70px"  class="text_boxes_numeric"  /></td>
                
                <td align="right"><input id="noOfRollBag_<? echo $tblRow; ?>" name="noOfRollBag[]" value="" type="text" style="width:70px"  class="text_boxes"  /></td>
				<td align="right" id="txtDelvBalance_<? echo $tblRow; ?>" title="<?= number_format($del_bal,4,'.','');?>"> <? echo number_format($del_bal,4,'.','');// $del_bal ; ?></td> 
				<td id="cboshipingStatus_<? echo $tblRow; ?>" title="<?= $shipStatus;?>"><? echo $delivery_status[$shipStatus]; ?></td>
				<td><input id="txtRemarksDtls_<? echo $tblRow; ?>" name="txtRemarksDtls[]" value="<? echo $remarks; ?>" type="text" class="text_boxes" style="width:77px" />
					<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" id="hdn_break_down_id_<? echo $tblRow; ?>" name="hdn_break_down_id[]" value="<? echo $row[csf('break_id')]; ?>">
				</td>
				<td><? echo create_drop_down( "cboStatus_".$tblRow, 60, $row_status,"", '', "-- select --",$selected,"", 0,'','','','','','',"cboStatus[]"); ?>	
				</td>
			</tr>
			<?
		}
	}	
	else
	{
		foreach($rcv_result as $row)
		{
			$tblRow++; $shipStatus=''; 
			$orderQuantity= $row[csf('order_quantity')];
			if($variable_status==2){
				$convertedBQty= $production_qty_arr[$row[csf('break_id')]]['qc_pass_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
			}
			else{
				$convertedBQty= $break_qty_sum_arr[$row[csf('break_id')]]['cal_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
			}

			/************** This condition is for few days . Previous data syncronized .****************/
			if($variable_status==2 && $convertedBQty==''){
				$convertedBQty= $break_qty_sum_arr[$row[csf('break_id')]]['cal_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
			}
			if(is_nan($convertedBQty)) $convertedBQty=0; else $convertedBQty = $convertedBQty;

			$CumDelvQty=($delevery_qty_trims_arr[$row[csf("break_id")]]['delevery_qty']-$row[csf('delevery_qty')]);
			$del_bal=($orderQuantity-($CumDelvQty+$row[csf('delevery_qty')]));

			if($orderQuantity*1==$del_bal*1) {$shipStatus=1;}
        	if($orderQuantity*1>$del_bal*1) {$shipStatus=2;} 
        	if($del_bal*1==0){ $shipStatus=3;}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<td><? echo $row[csf('buyer_po_no')]; ?></td>
				<td><? echo $trims_section[$row[csf('section')]]; ?></td>
				<td><? echo $trims_groups_arr[chop($row[csf('id')],",")]['item_group']; ?></td>		
				<td><? echo $row[csf('description')]; ?></td>
				<td><? echo $color_library[$row[csf('color_id')]] ?></td>
				<td><? echo $size_arr[$row[csf('size_id')]] ?></td>
				<td><? echo $unit_of_measurement[$row[csf('order_uom')]] ?></td>
				<td align="right" id="txtWorkOrderQuantity_<? echo $tblRow; ?>"  title="<?= $orderQuantity; ?>"><? echo number_format($orderQuantity,4,'.',''); ?></td>
				<td align="right" id="txtOrderQuantity_<? echo $tblRow; ?>" title="<?= number_format($convertedBQty,4,'.','');?>"><? echo number_format($convertedBQty,4,'.',''); ?></td>
				<td align="right" id="txtPrevQty_<? echo $tblRow; ?>" title="<?= number_format($CumDelvQty,4,'.','');?>"><? echo number_format($CumDelvQty,4); ?></td>
				<td><input id="txtCurQty_<? echo $tblRow; ?>" name="txtCurQty[]" onKeyUp="cal_values(<? echo $tblRow; ?>);" value="<? echo number_format($row[csf('delevery_qty')],4,'.',''); ?> " type="text" style="width:70px"  class="text_boxes_numeric"  /></td>
                <td align="right"><input id="noOfRollBag_<? echo $tblRow; ?>" name="noOfRollBag[]" value="<? echo $row[csf('no_of_roll_bag')]; ?>" type="text" style="width:70px"  class="text_boxes"  /></td>
				<td align="right" id="txtDelvBalance_<? echo $tblRow; ?>" title="<?= number_format($del_bal,4,'.','');?>"> <? echo number_format($del_bal,4,'.','');// $del_bal ; ?></td> 
				<td id="cboshipingStatus_<? echo $tblRow; ?>" title="<?= $shipStatus;?>"><? echo $delivery_status[$shipStatus]; ?></td>
				<td><input id="txtRemarksDtls_<? echo $tblRow; ?>" name="txtRemarksDtls[]" value="<? echo $row[csf('remarks')]; ?>" type="text" class="text_boxes" style="width:77px" />
					<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="<? echo $row[csf('delDtlsId')]; ?>" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" id="hdn_break_down_id_<? echo $tblRow; ?>" name="hdn_break_down_id[]" value="<? echo $row[csf('break_id')]; ?>">
				</td>
				<td><? echo create_drop_down( "cboStatus_".$tblRow, 60, $row_status,"", '', "-- select --",$row[csf('status_active')],"", 0,'','','','','','',"cboStatus[]"); ?>	
				</td>
			</tr>
			<?
		}
	}
	exit();
}
if( $action=='dalivery_order_dtls_list_view' ) 
{
	$data=explode('_',$data);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4 and status_active=1",'id','item_name');
	$tblRow=0;
	//die;
	//print_r($data);
	if($data[0]==1)
	{
		$sql = "select a.id, a.mst_id, a.job_no_mst, a.order_id, a.order_no, a.buyer_po_id, a.booking_dtls_id,b.id as receive_dtls_id, b.qnty as order_quantity , a.order_uom,a.booked_uom, a.booked_conv_fac, b.rate, a.amount, a.delivery_date, a.buyer_po_no, a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.rate_domestic,  a.amount_domestic,b.item_id, b.color_id, b.size_id,b.description,b.id as break_id,b.book_con_dtls_id from subcon_ord_dtls a,subcon_ord_breakdown b where   a.id=b.mst_id and a.job_no_mst=b.job_no_mst and a.job_no_mst='$data[1]' and a.mst_id='$data[3]'  and a.order_quantity<>0 and a.booked_qty<>0 and a.status_active=1 and a.is_deleted=0  order by a.id ASC";
	}
	else
	{
		$sql = "select a.id,a.mst_id,a.job_no_mst,a.order_id,a.order_no,a.buyer_po_id,a.booking_dtls_id,b.id AS receive_dtls_id,b.qnty AS order_quantity,a.order_uom,a.booked_uom,a.booked_conv_fac,b.rate,a.amount,a.delivery_date,a.buyer_po_no,a.buyer_style_ref,a.buyer_buyer,a.section,a.item_group AS trim_group,a.rate_domestic,a.amount_domestic,b.item_id,b.color_id,b.size_id,b.description,b.id AS break_id,b.book_con_dtls_id,c.delevery_qty,c.claim_qty,c.id AS delDtlsId,c.size_name,c.color_name,c.remarks,c.status_active,c.no_of_roll_bag FROM subcon_ord_dtls a ,subcon_ord_breakdown b LEFT JOIN trims_delivery_dtls c ON c.break_down_details_id = b.id AND c.mst_id ='$data[1]' and c.status_active<>0 WHERE a.id = b.mst_id and a.job_no_mst=b.job_no_mst and a.mst_id='$data[3]'";
	}
	//echo $sql;
	$rcv_result =sql_select($sql); 
	$break_qty_arr=array();
	foreach ($rcv_result as $row){
		$break_qty_arr[$row[csf("break_id")]]['order_quantity']=$row[csf("order_quantity")];
		$break_qty_arr[$row[csf("break_id")]]['id']=$row[csf("id")];
	}

	$trims_groups_arr=array();
	$trim_sql ="select id, item_group,buyer_buyer ,booked_conv_fac from subcon_ord_dtls where status_active=1 and is_deleted=0 and item_group is not null ";
	$trim_sql_res=sql_select($trim_sql);
	foreach ($trim_sql_res as $row){
		$trims_groups_arr[$row[csf("id")]]['item_group']=$item_group_arr[$row[csf("item_group")]];
		$trims_groups_arr[$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
		$trims_groups_arr[$row[csf("id")]]['booked_conv_fac']=$row[csf("booked_conv_fac")];
	}
	unset($trim_sql_res);
	$variable_status=return_field_value("production_update_area","variable_setting_trim_prod","company_name='$data[4]' and variable_list =1 and is_deleted = 0 and status_active = 1");
	if($variable_status==2)
	{
		$production_sql ="select c.break_dtls_id,sum(c.qc_pass_qty) as qc_pass_qty  from trims_production_mst a, trims_production_dtls b, trims_prod_order_breakdown c where a.id=b.mst_id and b.id=c.mst_id and a.received_id=$data[3] and c.receive_id=$data[3] and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by c.break_dtls_id";
		$production_sql_res=sql_select($production_sql);
		$production_qty_arr=array(); $brkIds=''; 
		foreach ($production_sql_res as $row)
		{
			$brkIds=explode(",",$row[csf("break_dtls_id")]); $totalOrdQty='';
			foreach ($brkIds as $key => $value) {
				$totalOrdQty +=	$break_qty_arr[$value]['order_quantity'];
			}

			foreach ($brkIds as $key => $value) {
				$production_qty_arr[$value]['qc_pass_qty']+=$row[csf("qc_pass_qty")]*$break_qty_arr[$value]['order_quantity']/$totalOrdQty;
			}
		}
	}
	//echo count($production_qty_arr);
	if($variable_status==1 || count($production_qty_arr)<1)
	{
		$production_sql ="select b.id,b.break_id,b.qc_qty from trims_production_mst a, trims_production_dtls b where a.id=b.mst_id and a.received_id=$data[3] and a.status_active=1 and b.status_active=1 and b.is_deleted=0"; 
		
		$production_sql_res=sql_select($production_sql); $production_arr=array(); $break_ids=''; $production_percent=''; $break_qty_sum_arr=array(); $production_percent=''; $tblRow=0;
		foreach ($production_sql_res as $row){
			$break_ids=explode(",",$row[csf("break_id")]); $order_quantity='';
			for($i=0; $i<count($break_ids);$i++){
				$production_arr[$break_ids[$i]]['qc_qty']=$row[csf('qc_qty')];
				$order_quantity+=$break_qty_arr[$break_ids[$i]]['order_quantity'];
			}
			
			$production_percent=($row[csf("qc_qty")]*100)/$order_quantity;
			for($i=0; $i<=count($break_ids);$i++){
				$break_qty_sum_arr[$break_ids[$i]]['cal_qty']+=($production_percent*$break_qty_arr[$break_ids[$i]]['order_quantity'])/100;
			}
			unset($production_sql_res);
		}
		//echo "<pre>";
		//print_r($break_qty_sum_arr);
	}
	$delevery_qty_trims_arr=array();
	$pre_sql ="select break_down_details_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by break_down_details_id";
	$pre_sql_res=sql_select($pre_sql);
	foreach ($pre_sql_res as $row)
	{
		$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']=$row[csf("delevery_qty")];
	}
	unset($pre_sql_res);
	$convertedBQty=''; $shipStatus=0;
	if($data[0]==1)
	{
		foreach($rcv_result as  $row)
		{
			$tblRow++; $prev_qty='';
			//echo $variable_status.'nnn';
			if($variable_status==2){
				$convertedBQty= $production_qty_arr[$row[csf('break_id')]]['qc_pass_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
			}
			else{
				$convertedBQty= $break_qty_sum_arr[$row[csf('break_id')]]['cal_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
			}

			/************** This condition is for few days . Previous data syncronized .****************/
			if($variable_status==2 && $convertedBQty==''){
				$convertedBQty= $break_qty_sum_arr[$row[csf('break_id')]]['cal_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
			}
			if(is_nan($convertedBQty)) $convertedBQty=0; else $convertedBQty = $convertedBQty;
			$orderQuantity= $row[csf('order_quantity')];
			$del_bal=($orderQuantity-($delevery_qty_trims_arr[$row[csf("break_id")]]['delevery_qty']));
			if($orderQuantity*1==$del_bal*1)
			{$shipStatus=1;} 
			if($orderQuantity*1>$del_bal*1)
			{ $shipStatus=2;} 
			if($del_bal*1==0)
			{$shipStatus=3;}
			//echo $delevery_qty_trims_arr[$row[csf("break_id")]]['delevery_qty'];
			if($delevery_qty_trims_arr[$row[csf("break_id")]]['delevery_qty']!=''){
				$prev_qty= number_format($delevery_qty_trims_arr[$row[csf("break_id")]]['delevery_qty'],4,'.','');}else{ $prev_qty=0;} 
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<td><? echo $row[csf('buyer_po_no')]; ?></td>
				<td><? echo $trims_section[$row[csf('section')]]; ?></td>
				<td><? echo $trims_groups_arr[chop($row[csf('id')],",")]['item_group']; ?></td>		
				<td><? echo $row[csf('description')]; ?></td>
				<td><? echo $color_library[$row[csf('color_id')]] ?></td>
				<td><? echo $size_arr[$row[csf('size_id')]] ?></td>
				<td><? echo $unit_of_measurement[$row[csf('order_uom')]] ?></td>
				<td align="right" id="txtWorkOrderQuantity_<? echo $tblRow; ?>"  title="<?= $orderQuantity; ?>"><? echo number_format($orderQuantity,4,'.',''); ?></td>
				<td align="right" id="txtOrderQuantity_<? echo $tblRow; ?>" title="<?= number_format($convertedBQty,4,'.','');?>"><? echo number_format($convertedBQty,4,'.',''); ?></td>
				<td align="right" id="txtPrevQty_<? echo $tblRow; ?>" title="<?= $prev_qty;?>"><? echo number_format($prev_qty,4,'.',''); ?></td>
				<td align="right"><input id="txtCurQty_<? echo $tblRow; ?>" name="txtCurQty[]" onKeyUp="cal_values(<? echo $tblRow; ?>);" value="0" type="text" style="width:70px"  class="text_boxes_numeric"  /></td>
                <td align="right"><input id="noOfRollBag_<? echo $tblRow; ?>" name="noOfRollBag[]" value="" type="text" style="width:70px"  class="text_boxes"  /></td>
				<td align="right" id="txtDelvBalance_<? echo $tblRow; ?>" title="<?= number_format($del_bal,4,'.','');?>"> <? echo number_format($del_bal,4,'.','');// $del_bal ; ?></td> 
				<td id="cboshipingStatus_<? echo $tblRow; ?>" title="<?= $shipStatus;?>"><? echo $delivery_status[$shipStatus]; ?></td>
				<td><input id="txtRemarksDtls_<? echo $tblRow; ?>" name="txtRemarksDtls[]" value="<? echo $remarks; ?>" type="text" class="text_boxes" style="width:77px" />
					<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" id="hdn_break_down_id_<? echo $tblRow; ?>" name="hdn_break_down_id[]" value="<? echo $row[csf('break_id')]; ?>">
				</td>
				<td><? echo create_drop_down( "cboStatus_".$tblRow, 60, $row_status,"", '', "-- select --",$selected,"", 0,'','','','','','',"cboStatus[]"); ?>	
				</td>
			</tr>
			<?
		}
	}	
	else
	{
		foreach($rcv_result as $row)
		{
			$tblRow++; $shipStatus=''; 
			$orderQuantity= $row[csf('order_quantity')];
			if($variable_status==2){
				$convertedBQty= $production_qty_arr[$row[csf('break_id')]]['qc_pass_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
			}
			else{
				$convertedBQty= $break_qty_sum_arr[$row[csf('break_id')]]['cal_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
			}

			/************** This condition is for few days . Previous data syncronized .****************/
			if($variable_status==2 && $convertedBQty==''){
				$convertedBQty= $break_qty_sum_arr[$row[csf('break_id')]]['cal_qty']/$trims_groups_arr[$row[csf("id")]]['booked_conv_fac'];
			}
			if(is_nan($convertedBQty)) $convertedBQty=0; else $convertedBQty = $convertedBQty;

			$CumDelvQty=($delevery_qty_trims_arr[$row[csf("break_id")]]['delevery_qty']-$row[csf('delevery_qty')]);
			$del_bal=($orderQuantity-($CumDelvQty+$row[csf('delevery_qty')]));

			if($orderQuantity*1==$del_bal*1) {$shipStatus=1;}
        	if($orderQuantity*1>$del_bal*1) {$shipStatus=2;} 
        	if($del_bal*1==0){ $shipStatus=3;}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<td><? echo $row[csf('buyer_po_no')]; ?></td>
				<td><? echo $trims_section[$row[csf('section')]]; ?></td>
				<td><? echo $trims_groups_arr[chop($row[csf('id')],",")]['item_group']; ?></td>		
				<td><? echo $row[csf('description')]; ?></td>
				<td><? echo $color_library[$row[csf('color_id')]] ?></td>
				<td><? echo $size_arr[$row[csf('size_id')]] ?></td>
				<td><? echo $unit_of_measurement[$row[csf('order_uom')]] ?></td>
				<td align="right" id="txtWorkOrderQuantity_<? echo $tblRow; ?>"  title="<?= $orderQuantity; ?>"><? echo number_format($orderQuantity,4,'.',''); ?></td>
				<td align="right" id="txtOrderQuantity_<? echo $tblRow; ?>" title="<?= number_format($convertedBQty,4,'.','');?>"><? echo number_format($convertedBQty,4,'.',''); ?></td>
				<td align="right" id="txtPrevQty_<? echo $tblRow; ?>" title="<?= number_format($CumDelvQty,4,'.','');?>"><? echo number_format($CumDelvQty,4,'.',''); ?></td>
				<td><input id="txtCurQty_<? echo $tblRow; ?>" name="txtCurQty[]" onKeyUp="cal_values(<? echo $tblRow; ?>);" value="<? echo number_format($row[csf('delevery_qty')],4,'.',''); ?> " type="text" style="width:70px"  class="text_boxes_numeric"  /></td>
                <td align="right"><input id="noOfRollBag_<? echo $tblRow; ?>" name="noOfRollBag[]" value="<? echo $row[csf('no_of_roll_bag')]; ?>" type="text" style="width:70px"  class="text_boxes"  /></td>
				<td align="right" id="txtDelvBalance_<? echo $tblRow; ?>" title="<?= number_format($del_bal,4,'.','');?>"> <? echo number_format($del_bal,4,'.','');// $del_bal ; ?></td> 
				<td id="cboshipingStatus_<? echo $tblRow; ?>" title="<?= $shipStatus;?>"><? echo $delivery_status[$shipStatus]; ?></td>
				<td><input id="txtRemarksDtls_<? echo $tblRow; ?>" name="txtRemarksDtls[]" value="<? echo $row[csf('remarks')]; ?>" type="text" class="text_boxes" style="width:77px" />
					<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="<? echo $row[csf('delDtlsId')]; ?>" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" id="hdn_break_down_id_<? echo $tblRow; ?>" name="hdn_break_down_id[]" value="<? echo $row[csf('break_id')]; ?>">
				</td>
				<td><? echo create_drop_down( "cboStatus_".$tblRow, 60, $row_status,"", '', "-- select --",$row[csf('status_active')],"", 0,'','','','','','',"cboStatus[]"); ?>	
				</td>
			</tr>
			<?
		}
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	/*echo '<pre>';
	print_r($cbo_company_name);die;*/
	$user_id=$_SESSION['logic_erp']['user_id'];
	
	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_name and variable_list=23 and category =4 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;

	$production_sql ="select b.id as prod_dtls_id,b.break_id,b.qc_qty,b.job_dtls_id from trims_production_mst a, trims_production_dtls b where a.id=b.mst_id and a.received_id=$received_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
	$production_sql_res=sql_select($production_sql); $production_arr=array(); $break_ids='';
	foreach ($production_sql_res as $row)
	{
		$break_ids=explode(",",$row[csf("break_id")]); $order_quantity='';
		for($i=0; $i<count($break_ids);$i++)
		{
			$production_arr[$break_ids[$i]]['job_dtls_id'] = $row[csf('job_dtls_id')];
			$production_arr[$break_ids[$i]]['prod_dtls_id'] = $row[csf('prod_dtls_id')];
			$production_arr[$break_ids[$i]]['qc_qty'] +=$row[csf('qc_qty')];
			//echo $break_ids[$i]."==".$row[csf("qc_qty")]."++";
			
			//$order_quantity+=$break_qty_arr[$break_ids[$i]]['order_quantity'];
			//$production_arr[$break_ids[$i]]['receive_dtls_id']=$row[csf('receive_dtls_id')];
		}
		/*$production_percent=($row[csf("qc_qty")]*100)/$order_quantity;
		//echo $production_percent."=="; 
		for($i=0; $i<=count($break_ids);$i++)
		{
			//$break_qty_sum_arr[$break_ids[$i]]['sum_qty']=$order_quantity;
			$break_qty_sum_arr[$break_ids[$i]]['cal_qty']+=($production_percent*$break_qty_arr[$break_ids[$i]]['order_quantity'])/100;
		}*/
	} 

	$delivery_sql ="select b.break_down_details_id,b.delevery_qty,trims_del from trims_delivery_mst a, trims_delivery_dtls b where a.id=b.mst_id and a.received_id=$received_id and a.id!=$update_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
	$delivery_sql_res=sql_select($delivery_sql); $del_qty_arr=array();
	foreach ($delivery_sql_res as $row)
	{
		$del_qty_arr[$row[csf('break_down_details_id')]]['delevery_qty'] += $row[csf('delevery_qty')];
		$del_qty_arr[$row[csf('break_down_details_id')]]['trims_del'] .= $row[csf('delevery_qty')];
	}
	
	//echo "10**".$over_receive_limit;die;
	if ($operation==0) // Insert Start Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		//$new_del_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'TD', date("Y",time()), 5, "select del_no_prefix,del_no_prefix_num from trims_delivery_mst where entry_form=208 and company_id=$cbo_company_name $insert_date_con order by id desc ", "del_no_prefix", "del_no_prefix_num" ));
		$new_del_no = explode("*", return_next_id_by_sequence("TRIMS_DELIVERY_MST_PK_SEQ", "trims_delivery_mst",$con,1,$cbo_company_name,'TD',208,date("Y",time()) ));
		
		/*echo "10**<pre>";
		print_r($new_del_no);die;*/
		if($db_type==0){
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
		}else{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
		}
		$id = return_next_id_by_sequence("TRIMS_DELIVERY_MST_PK_SEQ", "trims_delivery_mst", $con);
		//$id=return_next_id("id","trims_delivery_mst",1);
		$id1=return_next_id( "id", "trims_delivery_dtls",1) ;
		$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$size_library_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
		//$id3=return_next_id( "id", "trims_delivery_breakdown", 1 ) ;
		$rID3=true;
		$field_array="id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks , inserted_by, insert_date";

		$data_array="(".$id.", 208, '".$new_del_no[0]."', '".$new_del_no[1]."', '".$new_del_no[2]."', '".$cbo_company_name."', '".$cbo_location_name."', '".$cbo_within_group."', '".$cbo_party_name."', '".$cbo_party_location."', '".$cbo_currency."', '".$txt_delivery_date."','".$received_id."','".$hid_order_id."', '".$txt_challan_no."', '".$txt_gate_pass_no."', '".$txt_remarks."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";
		
		$txt_job_no=$new_del_no[0];
		// echo "10**".$txt_job_no; die;
		

		$sql = "select a.id as receive_dtls_id, a.mst_id, a.job_no_mst, a.order_id, a.order_no, a.buyer_po_id, a.booking_dtls_id, b.qnty as order_quantity , a.order_uom,a.booked_uom, a.booked_conv_fac, b.rate, a.amount, a.delivery_date, a.buyer_po_no, a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.rate_domestic,  a.amount_domestic,a.delevery_status,b.item_id, b.color_id, b.size_id,b.description,b.id as break_id,b.book_con_dtls_id from subcon_ord_dtls a,subcon_ord_breakdown b where   a.id=b.mst_id and a.mst_id=$received_id  and a.order_quantity<>0 and a.booked_qty<>0 and a.status_active=1 and a.is_deleted=0  order by a.id ASC";
		$order_result=sql_select($sql); 
		foreach ($order_result as $rows)
		{
			$order_dtls_arr[$rows[csf("break_id")]]['break_id']			=$rows[csf("break_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['booking_dtls_id']	=$rows[csf("booking_dtls_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['receive_dtls_id']	=$rows[csf("receive_dtls_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['order_id']			=$rows[csf("order_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['order_no']			=$rows[csf("order_no")];
			$order_dtls_arr[$rows[csf("break_id")]]['buyer_po_id']		=$rows[csf("buyer_po_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['buyer_po_no']		=$rows[csf("buyer_po_no")];
			$order_dtls_arr[$rows[csf("break_id")]]['buyer_style_ref']	=$rows[csf("buyer_style_ref")];
			$order_dtls_arr[$rows[csf("break_id")]]['buyer_buyer']		=$rows[csf("buyer_buyer")];
			$order_dtls_arr[$rows[csf("break_id")]]['section']			=$rows[csf("section")];
			$order_dtls_arr[$rows[csf("break_id")]]['item_group']		=$rows[csf("trim_group")];
			$order_dtls_arr[$rows[csf("break_id")]]['description']		=$rows[csf("description")];
			$order_dtls_arr[$rows[csf("break_id")]]['color_id']			=$rows[csf("color_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['color_name']		=$color_library_arr[$rows[csf("color_id")]];
			$order_dtls_arr[$rows[csf("break_id")]]['size_id']			=$rows[csf("size_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['size_name']		=$size_library_arr[$rows[csf("size_id")]];
			$order_dtls_arr[$rows[csf("break_id")]]['order_quantity']	=$rows[csf("order_quantity")];
			$order_dtls_arr[$rows[csf("break_id")]]['rate']				=$rows[csf("rate")];
			$order_dtls_arr[$rows[csf("break_id")]]['order_uom']		=$rows[csf("order_uom")];
			$order_dtls_arr[$rows[csf("break_id")]]['delevery_status']		=$rows[csf("delevery_status")];
		}
		//echo "10**<pre>";
		//print_r($order_dtls_arr); die;
		
		//echo "10**<pre>";
		//print_r($production_arr); die;
		$field_array2="id, mst_id, received_id, booking_dtls_id, receive_dtls_id, job_dtls_id, production_dtls_id,  order_id, order_no, buyer_po_id, buyer_po_no,  buyer_style_ref, buyer_buyer, section,item_group, order_uom, order_quantity, delevery_qty, remarks,description, color_id, size_id,color_name,size_name,delevery_status,workoder_qty,order_receive_rate, break_down_details_id,no_of_roll_bag, inserted_by, insert_date";
		
		$field_array3="delivery_status*updated_by*update_date";
		$field_array5="delivery_status";
		
		$id1=return_next_id( "id", "trims_delivery_dtls",1) ;
		$data_array2 = $data_array3="";  $add_commaa=0; $add_commadtls=0; 
		for($i=1; $i<=$total_row; $i++)
		{	
			$shipStatus='';
			$txtPrevQty 			= "txtPrevQty_".$i;
			$txtCurQty 				= "txtCurQty_".$i;	
			$noOfRollBag 			= "noOfRollBag_".$i;
			$txtOrderQuantity		= "txtOrderQuantity_".$i;		
			$txtRemarksDtls 		= "txtRemarksDtls_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$cboStatus				= "cboStatus_".$i;
			$cboshipingStatus	    = "cboshipingStatus_".$i;
			$hdn_break_down_id	    = "hdn_break_down_id_".$i;

			$brkID	=str_replace("'",'',$$hdn_break_down_id);
			$hdnJobDtlsId 			= $production_arr[$brkID]['job_dtls_id'];
			$hdnProductionDtlsId 	= $production_arr[$brkID]['prod_dtls_id'];
			$prodQcQty 				= $production_arr[$brkID]['qc_qty'];
			$hdnReceiveDtlsId 		= $order_dtls_arr[$brkID]['receive_dtls_id'];
			$hdnbookingDtlsId 		= $order_dtls_arr[$brkID]['booking_dtls_id'];
			$txtWorkOrderID 		= $order_dtls_arr[$brkID]['order_id'];
			$txtWorkOrder 			= $order_dtls_arr[$brkID]['order_no'];
			$txtbuyerPoId 			= $order_dtls_arr[$brkID]['buyer_po_id'];
			$txtbuyerPo 			= $order_dtls_arr[$brkID]['buyer_po_no'];
			$txtstyleRef 			= $order_dtls_arr[$brkID]['buyer_style_ref'];
			$txtbuyer 				= $order_dtls_arr[$brkID]['buyer_buyer'];
			$cboSection 			= $order_dtls_arr[$brkID]['section'];
			$cboItemGroup 			= $order_dtls_arr[$brkID]['item_group'];
			$txtItem 				= $order_dtls_arr[$brkID]['description'];
			$txtcolorID 			= $order_dtls_arr[$brkID]['color_id'];
			$txtcolor 				= $order_dtls_arr[$brkID]['color_name'];
			$txtsizeID 				= $order_dtls_arr[$brkID]['size_id'];
			$txtsize 				= $order_dtls_arr[$brkID]['size_name'];
			$txtWorkOrderQuantity 	= $order_dtls_arr[$brkID]['order_quantity'];
			$hdn_break_down_rate 	= $order_dtls_arr[$brkID]['rate'];
			$cboUom 				= $order_dtls_arr[$brkID]['order_uom'];
			$delevery_status 		= $order_dtls_arr[$brkID]['delevery_status'];
			$prevDelQty 			= $del_qty_arr[$brkID]['delevery_qty'];
			$prevTrimsDel 			= $del_qty_arr[$brkID]['trims_del'];
			if($prevTrimsDel!=''){
				$prevTrimsDel=implode(", ",array_unique(explode(", ",chop($prevTrimsDel),',')));
			}
			

			//$aa	=$hdnReceiveDtlsId;
			/*if($$txtWorkOrderQuantity==$$txtDelvBalance) $shipStatus=1; 
        	else if($$txtWorkOrderQuantity>$$txtDelvBalance) $shipStatus=2; 
        	else if($$txtDelvBalance==0) $shipStatus=3;*/
			/*$totaldoqnty=($$txtCurQty+$$txtPrevQty);
			if($$txtDelvBalance==0) $shipStatus=3;
			if($$txtDelvBalance>0 && $$txtDelvBalance!=$totaldoqnty) $shipStatus=2;
			if($$txtDelvBalance>0 && $$txtDelvBalance==$$txtWorkOrderQuantity) $shipStatus=1;
			if($$txtDelvBalance<0 && $$txtCurQtye>$$txtWorkOrderQuantity) $shipStatus=1;*/
			$WorkOrderQuantity=trim($txtWorkOrderQuantity)*1;
			$CurQty=trim(str_replace("'",'',$$txtCurQty))*1;
			$PrevQty=trim(str_replace("'",'',$$txtPrevQty))*1;
			////////////////////////// over_receive_limit_qnty start
			$Cur_Do_Qty =$CurQty;
			$prev_Do_Qty=$PrevQty;
			$total_Do_Qty=$prev_Do_Qty+$Cur_Do_Qty;
			$woDoQnty=$WorkOrderQuantity;
			$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $woDoQnty:0;			
			$allow_total_val = $woDoQnty + $over_receive_limit_qnty;

			$orderBalance=$woDoQnty-($prevDelQty+$Cur_Do_Qty);
			$prodBalance=$prodQcQty-($prevDelQty+$Cur_Do_Qty);
			
			//echo "10**".$Cur_Do_Qty."prev_Do_Qty".$prev_Do_Qty."total_Do_Qty".$total_Do_Qty."woDoQnty".$woDoQnty."over_receive_limit_qnty".$over_receive_limit_qnty."allow_total_val".$allow_total_val; 
			//1prev_Do_Qty 2 total_Do_Qty 3 woDoQnty 3.5 over_receive_limit_qnty 0.7 allow_total_val 4.2

			if($prodBalance<0) {
				//echo "40**".$prodBalance."==".$prodQcQty."==".$prevDelQty."==".$Cur_Do_Qty; die;
				echo "40**No Balance Quantity.\nPlease check previous Delivery \nPrevious Delivery System ID = $prevTrimsDel";
				die;
			}

			$overRecvLimitMsg="Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty.)";
			if($allow_total_val<$total_Do_Qty) {
				$over_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
				echo "40**Delv. quantity can not be greater than WO quantity.\n\nWO/quantity = $woDoQnty \n$overRecvLimitMsg $over_msg";
				die;
			}
			////////////////////////////////////////////// over_receive_limit_qnty end
			if($WorkOrderQuantity>($CurQty+$PrevQty) && ($CurQty+$PrevQty)>0){
				$shipStatus=2;	
			}
			elseif($WorkOrderQuantity==($CurQty+$PrevQty) || $WorkOrderQuantity <($CurQty+$PrevQty)){
				$shipStatus=3;
			}
			else{
				$shipStatus=1;
			}
			//echo  "10**".$$txtWorkOrderQuantity.'>('.$$txtCurQty.'+'.$$txtPrevQty."___";
			//echo "".$shipStatus;die;
			//$shipStatus=$$cboshipingStatus;
			if(str_replace("'",'',$txtbuyerPoId)=="") $txtbuyerPoId=0; else $txtbuyerPoId=str_replace("'",'',$txtbuyerPoId);
			if ($add_commaa!=0) $data_array2 .=","; $add_comma=0;
			
			$data_array2 .="(".$id1.",".$id.",'".$received_id."','".$hdnbookingDtlsId."','".$hdnReceiveDtlsId."','".$hdnJobDtlsId."','".$hdnProductionDtlsId."','".$txtWorkOrderID."','".$txtWorkOrder."','".$txtbuyerPoId."','".$txtbuyerPo."','".$txtstyleRef."','".$txtbuyer."','".$cboSection."','".$cboItemGroup."','".$cboUom."',".str_replace(",",'',$$txtOrderQuantity).",".str_replace(",",'',$$txtCurQty).",".$$txtRemarksDtls.",'".$txtItem."','".$txtcolorID."','".$txtsizeID."','".$txtcolor."','".$txtsize."','".$shipStatus."','".$txtWorkOrderQuantity."','".$hdn_break_down_rate."','".$brkID."',".str_replace(",",'',$$noOfRollBag).",'".$user_id."','".$pc_date_time."')";
			
			$id1=$id1+1; $add_commaa++;
			if(str_replace("'",'',$$hdnReceiveDtlsId)!="")
			{
				if($shipStatus>1 )
				{
					$data_array3[$hdnReceiveDtlsId]=explode("*",("".$shipStatus."*".$user_id."*'".$pc_date_time."'"));
					$hdnRcvIdArr[]=str_replace("'",'',$$hdnReceiveDtlsId);
				}
				
			}
			if(str_replace("'",'',$$hdn_break_down_id)!="")
			{
				if($shipStatus>1 )
				{
					$data_array5[$brkID]=explode("*",("".$shipStatus.""));
					$hdnBrkIdArr[]=$brkID;
				}
			}
		}
		$flag=1;
		if($data_array3!="")
		{
			//echo "10**".bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr);
			$rID3=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr),1);
			if($rID3) $flag=1; else $flag=0;
		}
		if($data_array5!="")
		{
			//echo "10**".bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr);
			$rID5=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array5,$data_array5,$hdnBrkIdArr),1);
			if($rID5) $flag=1; else $flag=0;
		}
		//echo "10**INSERT INTO subcon_ord_mst (".$field_array.") VALUES ".$data_array; die;
		//echo "10**INSERT INTO trims_delivery_breakdown (".$field_array3.") VALUES ".$data_array3; die;
		//echo "10**INSERT INTO trims_delivery_dtls (".$field_array2.") VALUES ".$data_array2; die;
		
		$rID=sql_insert("trims_delivery_mst",$field_array,$data_array,1);
		if($rID==1) $flag=1; else $flag=0;
		$rID2=sql_insert("trims_delivery_dtls",$field_array2,$data_array2,1);
		if($rID2==1) $flag=1; else $flag=0;
		
		//echo "10**".$rID."**".$rID2."**".$rID3; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_job_no)."**".$id."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_job_no)."**".$id."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_job_no)."**".$id."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_job_no)."**".$id."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name"  );
		$size_library_arr=return_library_array( "select id,size_name from lib_size  where status_active=1 and is_deleted=0", "id", "size_name"  );
		if($db_type==0){
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date),'yyyy-mm-dd');
		}else{
			$txt_delivery_date=change_date_format(str_replace("'",'',$txt_delivery_date), "", "",1);
		}

		$field_array="location_id*within_group*party_id*party_location*currency_id*delivery_date*received_id*order_id*challan_no*gate_pass_no*remarks*updated_by*update_date";	
		$data_array="'".$cbo_location_name."'*'".$cbo_within_group."'*'".$cbo_party_name."'*'".$cbo_party_location."'*'".$cbo_currency."'*'".$txt_delivery_date."'*'".$received_id."'*'".$hid_order_id."'*'".$txt_challan_no."'*'".$txt_gate_pass_no."'*'".$txt_remarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array2="received_id*booking_dtls_id*receive_dtls_id*job_dtls_id*production_dtls_id*order_id*order_no*buyer_po_id*buyer_po_no*buyer_style_ref*buyer_buyer*section*item_group*order_uom*order_quantity*delevery_qty*remarks*description*color_id*size_id*color_name*size_name*delevery_status*workoder_qty*order_receive_rate* break_down_details_id*no_of_roll_bag*status_active*updated_by*update_date";

		$field_array3="delivery_status*updated_by*update_date";
		$field_array4="id, mst_id, received_id, booking_dtls_id, receive_dtls_id, job_dtls_id, production_dtls_id,  order_id, order_no, buyer_po_id, buyer_po_no,  buyer_style_ref, buyer_buyer, section,   item_group, order_uom, order_quantity, delevery_qty, remarks,description, color_id, size_id,color_name,size_name,delevery_status,workoder_qty,order_receive_rate, break_down_details_id,no_of_roll_bag, inserted_by, insert_date";
		$field_array5="delivery_status";
		$id1=return_next_id( "id", "trims_delivery_dtls",1) ;
		$add_comma=0;	
		$flag=1;

		$sql = "select a.id as receive_dtls_id, a.mst_id, a.job_no_mst, a.order_id, a.order_no, a.buyer_po_id, a.booking_dtls_id, b.qnty as order_quantity , a.order_uom,a.booked_uom, a.booked_conv_fac, b.rate, a.amount, a.delivery_date, a.buyer_po_no, a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.rate_domestic,  a.amount_domestic,a.delevery_status,b.item_id, b.color_id, b.size_id,b.description,b.id as break_id,b.book_con_dtls_id from subcon_ord_dtls a,subcon_ord_breakdown b where   a.id=b.mst_id and a.mst_id=$received_id  and a.order_quantity<>0 and a.booked_qty<>0 and a.status_active=1 and a.is_deleted=0  order by a.id ASC";
		$order_result=sql_select($sql); 
		foreach ($order_result as $rows)
		{
			$order_dtls_arr[$rows[csf("break_id")]]['break_id']			=$rows[csf("break_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['booking_dtls_id']	=$rows[csf("booking_dtls_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['receive_dtls_id']	=$rows[csf("receive_dtls_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['order_id']			=$rows[csf("order_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['order_no']			=$rows[csf("order_no")];
			$order_dtls_arr[$rows[csf("break_id")]]['buyer_po_id']		=$rows[csf("buyer_po_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['buyer_po_no']		=$rows[csf("buyer_po_no")];
			$order_dtls_arr[$rows[csf("break_id")]]['buyer_style_ref']	=$rows[csf("buyer_style_ref")];
			$order_dtls_arr[$rows[csf("break_id")]]['buyer_buyer']		=$rows[csf("buyer_buyer")];
			$order_dtls_arr[$rows[csf("break_id")]]['section']			=$rows[csf("section")];
			$order_dtls_arr[$rows[csf("break_id")]]['item_group']		=$rows[csf("trim_group")];
			$order_dtls_arr[$rows[csf("break_id")]]['description']		=$rows[csf("description")];
			$order_dtls_arr[$rows[csf("break_id")]]['color_id']			=$rows[csf("color_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['color_name']		=$color_library_arr[$rows[csf("color_id")]];
			$order_dtls_arr[$rows[csf("break_id")]]['size_id']			=$rows[csf("size_id")];
			$order_dtls_arr[$rows[csf("break_id")]]['size_name']		=$size_library_arr[$rows[csf("size_id")]];
			$order_dtls_arr[$rows[csf("break_id")]]['order_quantity']	=$rows[csf("order_quantity")];
			$order_dtls_arr[$rows[csf("break_id")]]['rate']				=$rows[csf("rate")];
			$order_dtls_arr[$rows[csf("break_id")]]['order_uom']		=$rows[csf("order_uom")];
			$order_dtls_arr[$rows[csf("break_id")]]['delevery_status']	=$rows[csf("delevery_status")];
		}

		/*$production_sql ="select b.id as prod_dtls_id,b.break_id,b.qc_qty,b.job_dtls_id from trims_production_mst a, trims_production_dtls b where a.id=b.mst_id and a.received_id=$received_id and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
		$production_sql_res=sql_select($production_sql); $production_arr=array(); $break_ids='';
		foreach ($production_sql_res as $row)
		{
			$break_ids=explode(",",$row[csf("break_id")]); $order_quantity='';
			for($i=0; $i<count($break_ids);$i++)
			{
				$production_arr[$break_ids[$i]]['job_dtls_id'] = $row[csf('job_dtls_id')];
				$production_arr[$break_ids[$i]]['prod_dtls_id'] = $row[csf('prod_dtls_id')];
			}
		} */

		for($i=1; $i<=$total_row; $i++)
		{	
			$shipStatus='';
			$txtPrevQty 			= "txtPrevQty_".$i;
			$txtCurQty 				= "txtCurQty_".$i;	
			$noOfRollBag 			= "noOfRollBag_".$i;	
			$txtOrderQuantity		= "txtOrderQuantity_".$i;		
			$txtRemarksDtls 		= "txtRemarksDtls_".$i;
			$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
			$cboStatus				= "cboStatus_".$i;
			$cboshipingStatus	    = "cboshipingStatus_".$i;
			$hdn_break_down_id	    = "hdn_break_down_id_".$i;

			$brkID	=str_replace("'",'',$$hdn_break_down_id);
			$hdnJobDtlsId 			= $production_arr[$brkID]['job_dtls_id'];
			$hdnProductionDtlsId 	= $production_arr[$brkID]['prod_dtls_id'];
			$prodQcQty 				= $production_arr[$brkID]['qc_qty'];
			$hdnReceiveDtlsId 		= $order_dtls_arr[$brkID]['receive_dtls_id'];
			$hdnbookingDtlsId 		= $order_dtls_arr[$brkID]['booking_dtls_id'];
			$txtWorkOrderID 		= $order_dtls_arr[$brkID]['order_id'];
			$txtWorkOrder 			= $order_dtls_arr[$brkID]['order_no'];
			$txtbuyerPoId 			= $order_dtls_arr[$brkID]['buyer_po_id'];
			$txtbuyerPo 			= $order_dtls_arr[$brkID]['buyer_po_no'];
			$txtstyleRef 			= $order_dtls_arr[$brkID]['buyer_style_ref'];
			$txtbuyer 				= $order_dtls_arr[$brkID]['buyer_buyer'];
			$cboSection 			= $order_dtls_arr[$brkID]['section'];
			$cboItemGroup 			= $order_dtls_arr[$brkID]['item_group'];
			$txtItem 				= $order_dtls_arr[$brkID]['description'];
			$txtcolorID 			= $order_dtls_arr[$brkID]['color_id'];
			$txtcolor 				= $order_dtls_arr[$brkID]['color_name'];
			$txtsizeID 				= $order_dtls_arr[$brkID]['size_id'];
			$txtsize 				= $order_dtls_arr[$brkID]['size_name'];
			$txtWorkOrderQuantity 	= $order_dtls_arr[$brkID]['order_quantity'];
			$hdn_break_down_rate 	= $order_dtls_arr[$brkID]['rate'];
			$cboUom 				= $order_dtls_arr[$brkID]['order_uom'];
			$delevery_status 		= $order_dtls_arr[$brkID]['delevery_status'];
			$prevDelQty 			= $del_qty_arr[$brkID]['delevery_qty'];
			$prevTrimsDel 			= $del_qty_arr[$brkID]['trims_del'];
			if($prevTrimsDel!=''){
				$prevTrimsDel=implode(", ",array_unique(explode(", ",chop($prevTrimsDel),',')));
			}
			/*if($$txtWorkOrderQuantity==$$txtDelvBalance) $shipStatus=1; 
        	else if($$txtWorkOrderQuantity>$$txtDelvBalance) $shipStatus=2; 
        	else if($$txtDelvBalance==0) $shipStatus=3;*/
			/*$totaldoqnty=($$txtCurQty+$$txtPrevQty);
			if($$txtDelvBalance==0) $shipStatus=3;
			if($$txtDelvBalance>0 && $$txtDelvBalance!=$totaldoqnty) $shipStatus=2;
			if($$txtDelvBalance>0 && $$txtDelvBalance==$$txtWorkOrderQuantity) $shipStatus=1;
			if($$txtDelvBalance<0 && $$txtCurQtye>$$txtWorkOrderQuantity) $shipStatus=1;*/
			//$shipStatus=$$cboshipingStatus;

			$WorkOrderQuantity=trim($txtWorkOrderQuantity)*1;
			$CurQty=trim(str_replace("'",'',$$txtCurQty))*1;
			$PrevQty=trim(str_replace("'",'',$$txtPrevQty))*1;
			//echo "10**".$WorkOrderQuantity.'=='.$$txtWorkOrderQuantity.'=='.$txtWorkOrderQuantity.'+';
			
			////////////////////////// over_receive_limit_qnty start
			$Cur_Do_Qty =$CurQty;
			$prev_Do_Qty=$PrevQty;
			$total_Do_Qty=$prev_Do_Qty+$Cur_Do_Qty;
			$woDoQnty=$WorkOrderQuantity;
			$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $woDoQnty:0;			
			$allow_total_val = $woDoQnty + $over_receive_limit_qnty;
			//echo "10**".$woDoQnty.'=='.$CurQty.'=='.$PrevQty.'=='.$total_Do_Qty.'=='.$allow_total_val;
			//10**0==1==0==1==0
			//echo "10**".$Cur_Do_Qty."prev_Do_Qty".$prev_Do_Qty."total_Do_Qty".$total_Do_Qty."woDoQnty".$woDoQnty."over_receive_limit_qnty".$over_receive_limit_qnty."allow_total_val".$allow_total_val; 
			//1prev_Do_Qty 2 total_Do_Qty 3 woDoQnty 3.5 over_receive_limit_qnty 0.7 allow_total_val 4.2

			$orderBalance=$woDoQnty-($prevDelQty+$Cur_Do_Qty);
			$prodBalance=$prodQcQty-($prevDelQty+$Cur_Do_Qty);
			////-396.1645
			if($prodBalance<0) {
				echo "40**No Balance Quantity.\nPlease check previous Delivery \nPrevious Delivery System ID = $prevTrimsDel";
				die;
			}

			/*$overRecvLimitMsg="Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty.)";
			if($allow_total_val<$total_Do_Qty) 
			{
				$over_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
				echo "40**Delv. quantity can not be greater than WO quantity.\n\nWO/quantity = $woDoQnty \n$overRecvLimitMsg $over_msg";
				die;
			}*/
			////////////////////////////////////////////// over_receive_limit_qnty end
			if($WorkOrderQuantity>($CurQty+$PrevQty) && ($CurQty+$PrevQty)>0){
				$shipStatus=2;	
			}
			elseif($WorkOrderQuantity==($CurQty+$PrevQty) || $WorkOrderQuantity <($CurQty+$PrevQty)){
				$shipStatus=3;
			}
			else{
				$shipStatus=1;
			}
			
			$aa	=str_replace("'",'',$$hdnDtlsUpdateId);
			$bb	=$hdnReceiveDtlsId;
			$brkID	=str_replace("'",'',$$hdn_break_down_id);

			if($txtbuyerPoId=="") $txtbuyerPoId=0; else $txtbuyerPoId=$txtbuyerPoId;
			if ($add_commaa!=0) $data_array4 .=","; $add_comma=0;
			//echo "10**".str_replace("'",'',$$hdnDtlsUpdateId).'++'; 
			if(str_replace("'",'',$$hdnDtlsUpdateId)!="")
			{
				$data_array2[$aa]=explode("*",("'".$received_id."'*'".$hdnbookingDtlsId."'*'".$hdnReceiveDtlsId."'*'".$hdnJobDtlsId."'*'".$hdnProductionDtlsId."'*'".$txtWorkOrderID."'*'".$txtWorkOrder."'*'".$txtbuyerPoId."'*'".$txtbuyerPo."'*'".$txtstyleRef."'*'".$txtbuyer."'*'".$cboSection."'*'".$cboItemGroup."'*'".$cboUom."'*".str_replace(",",'',$$txtOrderQuantity)."*".str_replace(",",'',$$txtCurQty)."*".$$txtRemarksDtls."*'".$txtItem."'*'".$txtcolorID."'*'".$txtsizeID."'*'".$txtcolor."'*'".$txtsize."'*".$$cboshipingStatus."*'".$txtWorkOrderQuantity."'*'".$hdn_break_down_rate."'*'".$brkID."'*".str_replace(",",'',$$noOfRollBag)."*".$$cboStatus."*".$user_id."*'".$pc_date_time."'"));
				$hdn_dtls_id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
			}
			else
			{
				$data_array4 .="(".$id1.",".$update_id.",'".$received_id."','".$hdnbookingDtlsId."','".$hdnReceiveDtlsId."','".$hdnJobDtlsId."','".$hdnProductionDtlsId."','".$txtWorkOrderID."','".$txtWorkOrder."','".$txtbuyerPoId."','".$txtbuyerPo."','".$txtstyleRef."','".$txtbuyer."','".$cboSection."','".$cboItemGroup."','".$cboUom."',".str_replace(",",'',$$txtOrderQuantity).",".str_replace(",",'',$$txtCurQty).",".$$txtRemarksDtls.",'".$txtItem."','".$txtcolorID."','".$txtsizeID."','".$txtcolor."','".$txtsize."','".$shipStatus."','".$txtWorkOrderQuantity."','".$hdn_break_down_rate."','".$brkID."',".str_replace(",",'',$$noOfRollBag).",'".$user_id."','".$pc_date_time."')";
			
				$id1=$id1+1; $add_commaa++;
			}

			if(str_replace("'",'',$$hdnReceiveDtlsId)!="")
			{
				if($shipStatus>1 )
				{
					$data_array3[$bb]=explode("*",("".$shipStatus."*".$user_id."*'".$pc_date_time."'"));
					$hdnRcvIdArr[]=str_replace("'",'',$$hdnReceiveDtlsId);
				}
			}
			if(str_replace("'",'',$$hdn_break_down_id)!="")
			{
				//if($delevery_status!=1 )
				if($shipStatus>1 )
				{
					$data_array5[$brkID]=explode("*",("".$shipStatus.""));
					$hdnBrkIdArr[]=str_replace("'",'',$$hdn_break_down_id);
				}
			}
		}
		//die;
		//echo "10**".bulk_update_sql_statement( "trims_delivery_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr); die;
		$rID=sql_update("trims_delivery_mst",$field_array,$data_array,"id",$update_id,0);  
		if($rID) $flag=1; else $flag=0;
		//echo "10**".$received_id; die;
		if($data_array2!="" && $flag=1)
		{
			//echo "10**".bulk_update_sql_statement( "trims_delivery_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr);die;
			$rID2=execute_query(bulk_update_sql_statement( "trims_delivery_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
			if($rID2) $flag=1; else $flag=0;
		}

		if($data_array3!="" && $flag=1)
		{
			//echo "10**".bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr);
			$rID3=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr),1);
			if($rID3) $flag=1; else $flag=0;
		}

		if($data_array4!="" && $flag=1)
		{
			//echo "10**INSERT INTO trims_delivery_dtls (".$field_array4.") VALUES ".$data_array4; die;
			$rID4=sql_insert("trims_delivery_dtls",$field_array4,$data_array4,1);
			if($rID4==1) $flag=1; else $flag=0;
		}
		if($data_array5!="" && $flag=1)
		{
			//echo "10**".bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr);
			$rID5=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array5,$data_array5,$hdnBrkIdArr),1);
			if($rID5) $flag=1; else $flag=0;
		}
		//echo "10**".$rID."**".$rID2."**".$rID3."**".$rID4."**".$rID5; die;
		//10**1******0**1
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
		}
		else if($db_type==2)
		{  
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // delete here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");  
		}
		$flag='';
		//echo "10**select trims_bill from trims_bill_mst where challan_no=$txt_dalivery_no and status_active=1 and is_deleted=0"; die;
		$chk_next_transaction=return_field_value("trims_bill","trims_bill_mst","challan_no like'%$txt_dalivery_no%' and status_active=1 and is_deleted=0","trims_bill");
		$chk_booking_id=return_field_value("booking_id","inv_receive_master","booking_id=$hid_order_id and status_active=1 and is_deleted=0","booking_id");
		$booking_no=return_field_value("booking_no","inv_receive_master","booking_id=$hid_order_id and status_active=1 and is_deleted=0","booking_no");
		//echo "10**".$chk_booking_id; die;
		
		if($chk_next_transaction !="")
		{ 
			echo "18**Delete not allowed. Bill Found. Bill No.".$chk_next_transaction; die;
		}
		else if($chk_booking_id !="")
		{ 
			echo "18**Delete not allowed. Receive Found. Work Order No.".$booking_no; die;
		}
		else
		{
			for($i=1; $i<=$total_row; $i++)
			{	
				$txtWorkOrderQuantity	= "txtWorkOrderQuantity_".$i;
				$txtCurQty 				= "txtCurQty_".$i;
				$txtPrevQty 			= "txtPrevQty_".$i;
				$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
				$hdnReceiveDtlsId 		= "hdnReceiveDtlsId_".$i;
				$hdn_break_down_id	    = "hdn_break_down_id_".$i;	
				$WorkOrderQuantity=trim(str_replace("'",'',$$txtWorkOrderQuantity))*1;
				$CurQty=trim(str_replace("'",'',$$txtCurQty))*1;
				$PrevQty=trim(str_replace("'",'',$$txtPrevQty))*1;
				
				if($WorkOrderQuantity>($PrevQty-$CurQty) && ($PrevQty-$CurQty)>0){
					$shipStatus=2;	
				}
				elseif($WorkOrderQuantity==($PrevQty-$CurQty) || $WorkOrderQuantity <($PrevQty-$CurQty)){
					$shipStatus=3;
				}
				else{
					$shipStatus=1;
				}
				
				$aa	=str_replace("'",'',$$hdnDtlsUpdateId);
				$bb	=str_replace("'",'',$$hdnReceiveDtlsId);
				$cc	=str_replace("'",'',$$hdn_break_down_id);

				if(str_replace("'",'',$$hdnReceiveDtlsId)!="")
				{
					$data_array3[$bb]=explode("*",("".$shipStatus."*".$user_id."*'".$pc_date_time."'"));
					$hdnRcvIdArr[]=str_replace("'",'',$$hdnReceiveDtlsId);
				}
				if(str_replace("'",'',$$hdn_break_down_id)!="")
				{
					$data_array5[$cc]=explode("*",("".$shipStatus.""));
					$hdnBrkIdArr[]=str_replace("'",'',$$hdn_break_down_id);
				}
			}
			$field_array="status_active*is_deleted*updated_by*update_date";
			$field_array3="delivery_status*updated_by*update_date";
			$field_array5="delivery_status";
			$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("trims_delivery_mst",$field_array,$data_array,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
			$rID1=sql_update("trims_delivery_dtls",$field_array,$data_array,"mst_id",$update_id,1); 
			if($rID1) $flag=1; else $flag=0;
			if($data_array3!="")
			{
				//echo "10**".bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr);
				$rID3=execute_query(bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr),1);
				if($rID3) $flag=1; else $flag=0;
			}
			if($data_array5!="")
			{
				//echo "10**".bulk_update_sql_statement( "subcon_ord_dtls", "id",$field_array3,$data_array3,$hdnRcvIdArr);
				$rID5=execute_query(bulk_update_sql_statement( "subcon_ord_breakdown", "id",$field_array5,$data_array5,$hdnBrkIdArr),1);
				if($rID5) $flag=1; else $flag=0;
			}
		}
		//echo "10**".$rID."**".$rID1."**".$rID3."**".$rID5."**".$flag; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
		}
		else if($db_type==2)
		{  
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_dalivery_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_order_no)."**".str_replace("'",'',$received_id);
			}
		}
		disconnect($con);
		die;
	}
}


if( $action=='order_dtls_list_view' ) 
{
	//echo $data; die; 1_FAL-TB-18-00091_1 
	$data=explode('_',$data);
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$tblRow=0;
	$buyer_po_arr=array();
	
	$buyer_po_sql = sql_select("select a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");
	
	foreach($buyer_po_sql as $row)
	{
		$buyer_po_arr[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$buyer_po_arr[$row[csf('id')]]['buyerpo']=$row[csf('po_number')];
		$buyer_po_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
	}
	unset($buyer_po_sql);
	if($data[0]==2)
	{

		$qry_result=sql_select( "select id, mst_id, order_id, job_no_mst, book_con_dtls_id, description, color_id, size_id, qnty, rate, amount from subcon_ord_breakdown where job_no_mst='$data[1]'");	
		$data_dreak_arr=array(); $data_dreak=''; $add_comma=0; $k=1;
		foreach ($qry_result as $row)
		{
			if($row[csf('description')]=="") $row[csf('description')]=0;
			if($row[csf('color_id')]=="") $row[csf('color_id')]=0;
			if($row[csf('size_id')]=="") $row[csf('size_id')]=0;
			if($row[csf('qnty')]=="") $row[csf('qnty')]=0;
			if($row[csf('rate')]=="") $row[csf('rate')]=0;
			if($row[csf('amount')]=="") $row[csf('amount')]=0;
			if($row[csf('book_con_dtls_id')]=="") $row[csf('book_con_dtls_id')]=0;
			if(!in_array($row[csf('mst_id')],$temp_arr_mst_id))
			{
				$temp_arr_mst_id[]=$row[csf('mst_id')];
				//if($k!=1) {  }
				$add_comma=0; $data_dreak='';
				
			}
			//echo $add_comma.'='.$data_dreak.'='.$k.'<br>';
			$k++;
			
			if ($add_comma!=0) $data_dreak ="__";
			$data_dreak_arr[$row[csf('mst_id')]].=$row[csf('description')].'_'.$color_library[$row[csf('color_id')]].'_'.$size_arr[$row[csf('size_id')]].'_'.$row[csf('qnty')].'_'.$row[csf('rate')].'_'.$row[csf('amount')].'_'.$row[csf('book_con_dtls_id')].'_'.$row[csf('id')].',';
			$add_comma++;
		}
	}
	//die;
	//print_r($data_dreak_arr);
	if($data[2]==1 && $data[0]==1 )
	{
		$sql = "select  a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id as booking_dtls_id, b.po_break_down_id,  b.trim_group ,b.delivery_date,b.fabric_description, b.uom, b.wo_qnty, b.rate, b.amount
		from  wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.booking_no=b.booking_no and a.booking_type=2 and c.wo_trim_booking_dtls_id=b.id and c.requirment>0 and  b.booking_no=trim('$data[1]') and a.status_active=1and a.lock_another_process!=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.booking_type, a.booking_no, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id, b.id, b.po_break_down_id, b.trim_group ,b.delivery_date,b.fabric_description, b.uom, b.wo_qnty, b.rate, b.amount order by b.id ASC";
	}
	else if($data[2]==1 && $data[0]==2 )
	{
		$sql = "select id, mst_id, job_no_mst, order_id, order_no, buyer_po_id as po_break_down_id, booking_dtls_id, order_quantity as wo_qnty, order_uom, rate, amount, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section, item_group as trim_group, rate_domestic,  amount_domestic from subcon_ord_dtls where job_no_mst='$data[1]' and mst_id='$data[3]' and status_active=1 and is_deleted=0 order by id ASC";
	}
	else
	{
		$sql = "select id, mst_id, job_no_mst, order_id, order_no, buyer_po_id as po_break_down_id, booking_dtls_id, order_quantity as wo_qnty, order_uom, rate, amount, delivery_date, buyer_po_no, buyer_style_ref, buyer_buyer, section, item_group as trim_group, rate_domestic,  amount_domestic from subcon_ord_dtls where job_no_mst='$data[1]' and mst_id='$data[3]' and status_active=1 and is_deleted=0 order by id ASC";
	}
	//echo $sql; //die; 
	$data_array=sql_select($sql);
	if(count($data_array) > 0)
	{
		$exchange_rate=$data[3];
		foreach($data_array as $row)
		{
			$tblRow++;
			$dtls_id=0; $order_uom=0; $wo_qnty=0;
			if($data[2]==1)  //within group yes 
			{
				$dtls_id=$row[csf('id')]; 
				$row[csf("delivery_date")]=$row[csf('delivery_date')];
				if($data[0]==1)
				{
					$order_uom=$row[csf('uom')];
				}
				else
				{
					$order_uom=$row[csf('order_uom')];
				} 
				$wo_qnty=$row[csf('wo_qnty')];
				$buyerpo=$buyer_po_arr[$row[csf('po_break_down_id')]]['buyerpo'];
				$style=$buyer_po_arr[$row[csf('po_break_down_id')]]['style'];
				$break_down_id=$row[csf('po_break_down_id')];
				/*if($data[0]==2) //update
				{
					$dtls_id=$embl_po_arr[$row[csf('booking_dtls_id')]]['id']; 
					$row[csf("delivery_date")]=$embl_po_arr[$row[csf('booking_dtls_id')]]['delivery_date']; 
					$order_uom=$embl_po_arr[$row[csf('booking_dtls_id')]]['order_uom'];
					$wo_qnty=$row[csf('wo_qnty')];
				}*/
			}
			else if($data[2]==2)
			{
				if($data[0]==2)
				{
					$dtls_id=$row[csf('id')]; 
					$row[csf("delivery_date")]=$row[csf('delivery_date')];
					$order_uom=$row[csf('order_uom')];
					$wo_qnty=$row[csf('wo_qnty')];
					$buyerpo=$row[csf('buyer_po_no')];
					$style=$row[csf('buyer_style_ref')];
					$buyer_buyer=$row[csf('buyer_buyer')];
					$break_down_id="";
				}
				else
				{
					$wo_qnty=0;
				}
			}

			if($data[0]==1)
			{
				$domRate=$row[csf('rate')]*$exchange_rate; 
				$domAmount=$row[csf('amount')]*$exchange_rate;
				$buyer_buyer='';
				$disabled='disabled';
				$disable_dropdown='1';
			}
			else
			{
				$domRate=$row[csf('rate_domestic')]; 
				$domAmount=$row[csf('amount_domestic')];
				$buyer_buyer=$row[csf('buyer_buyer')];
				$disabled='';
				$disable_dropdown='0';
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
				<td><input id="txtbuyerPo_<? echo $tblRow; ?>" name="txtbuyerPo[]" value="<? echo $buyerpo; ?>" class="text_boxes" type="text"  style="width:100px" <? echo $disabled ?> />
					<input id="txtbuyerPoId_<? echo $tblRow; ?>" name="txtbuyerPoId[]" value="<? echo $break_down_id; ?>" class="text_boxes" type="hidden" style="width:70px" readonly />
				</td>
				<td><input id="txtstyleRef_<? echo $tblRow; ?>" name="txtstyleRef[]" value="<? echo $style; ?>" class="text_boxes" type="text"  style="width:100px" <? echo $disabled ?> /></td>
				<td>
					<? 
					if($data[2]==1)
					{
						echo create_drop_down( "txtbuyer_".$tblRow, 100, "select id, buyer_name from lib_buyer where status_active=1","id,buyer_name", 1, "-- select --",$buyer_po_arr[$row[csf('po_break_down_id')]]['buyer_name'], "",$disable_dropdown,'','','','','','',"txtbuyer[]"); 
					}
					else
					{
						?>
						<input id="txtbuyer_<? echo $tblRow; ?>" name="txtbuyer[]" value="<? echo $buyer_buyer; ?>" class="text_boxes" type="text"  style="width:87px" $disabled />
						<?
					}
					?>
				</td>
				<td><? echo create_drop_down( "cboSection_".$tblRow, 90, $trims_section,"", 1, "-- select Section --",$row[csf('section')],'',1,'','','','','','',"cboSection[]"); ?></td>			
				<td><? echo create_drop_down( "cboItemGroup_".$tblRow, 90, "select id, item_name from lib_item_group where item_category=4 and status_active=1","id,item_name", 1, "-- select --",$row[csf('trim_group')], "",1,'','','','','','',"cboItemGroup[]"); ?></td>
				<td><? echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- select --",$order_uom,"", 1,'','','','','','',"cboUom[]"); ?>	</td>
				<td><input id="txtOrderQuantity_<? echo $tblRow; ?>" name="txtOrderQuantity[]" value="<? echo number_format($row[csf('wo_qnty')],4,'.',''); ?>" class="text_boxes_numeric" type="text"  style="width:60px"  placeholder="" readonly /></td>
				<!-- Previous Delv Qty 	Curr. Delv Qnty 	Claim Qnty -->
				<td><input id="txtPrevQty_<? echo $tblRow; ?>" name="txtPrevQty[]" value="<? echo number_format($row[csf('rate')],4); ?>" type="text"  class="text_boxes_numeric" style="width:60px" readonly/></td>
				<td><input id="txtCurQty_<? echo $tblRow; ?>" name="txtCurQty[]"  value="<? echo number_format($row[csf('amount')],4); ?>" type="text" style="width:70px"  class="text_boxes_numeric" disabled /></td>
                 <td align="right"><input id="noOfRollBag_<? echo $tblRow; ?>" name="noOfRollBag[]" value="" type="text" style="width:70px"  class="text_boxes"  /></td>
                
				<td><input id="txtClaimQty_<? echo $tblRow; ?>" name="txtClaimQty[]" value="<? echo number_format($domRate,4); ?>" type="text"  class="text_boxes_numeric" style="width:57px" <? echo $disabled ?> /></td>
				<td><input id="txtRemarksDtls_<? echo $tblRow; ?>" name="txtRemarksDtls[]" value="<? echo number_format($domAmount,4); ?>" type="text"  class="text_boxes_numeric" style="width:77px" <? echo $disabled ?> />
					<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" value="<? echo $dtls_id; ?>" class="text_boxes_numeric" style="width:40px" />
					<input type="hidden" id="hdnDtlsdata_<? echo $tblRow; ?>" name="hdnDtlsdata[]" value="<? echo implode("__",array_filter(explode(',',$data_dreak_arr[$dtls_id]))); ?>">
	                <input type="hidden" id="hdnbookingDtlsId_<? echo $tblRow; ?>" name="hdnbookingDtlsId[]" value="<? echo $row[csf('booking_dtls_id')]; ?>">
				</td>
				
                <td width="65">
					<input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(
					<? echo $tblRow.","."'tbl_dtls_emb'".","."'row_'" ;?>)" />
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<?echo $tblRow.","."'tbl_dtls_emb'".","."'row_'" ;?>);" />
				</td>
			</tr>
			<?
		}
	}
	else
	{
		?>		
		<tr id="row_1">
            <td><input id="txtbuyerPo_1" name="txtbuyerPo[]" name="text" class="text_boxes" style="width:100px" placeholder="Display"/>
            	<input id="txtbuyerPoId_1" name="txtbuyerPoId[]" type="hidden" class="text_boxes" style="width:70px"readonly />
            </td>
            <td><input id="txtstyleRef_1" name="txtstyleRef[]" type="text" class="text_boxes" style="width:100px" placeholder="Display"/></td>
             <td><input id="txtbuyer_1" name="txtbuyer[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" /></td>
            <td><? echo create_drop_down( "cboSection_1", 90, $trims_section,"id,section_name", 1, "-- select Section --","",'',0,'','','','','','',"cboSection[]"); ?></td>
            <td><? echo create_drop_down( "cboItemGroup_1", 90, "select id, item_name from lib_item_group where item_category=4 and  status_active=1","id,item_name", 1, "-- select --",$selected, "",0,'','','','','','',"cboItemGroup[]"); ?>	</td>
            <td><? echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "-- select --",2,1, 1,'','','','','','',"cboUom[]"); ?>	</td>
            <td><input id="txtOrderQuantity_1" name="txtOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:60px"  readonly /></td>
            <td><input id="txtPrevQty_1" name="txtPrevQty[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly /></td>
            <td><input id="txtCurQty_1" name="txtCurQty[]" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td>
             <td align="right"><input id="noOfRollBag_1" name="noOfRollBag[]" value="" type="text" style="width:70px"  class="text_boxes"  /></td> 
            <td><input id="txtClaimQty_1" name="txtClaimQty[]" type="text"  class="text_boxes_numeric" style="width:57px" readonly /></td> 
            <td><input id="txtRemarksDtls_1" name="txtRemarksDtls[]" type="text"  class="text_boxes_numeric" style="width:77px" readonly  />
            	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1">
                <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_1">
                <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_1"></td> 
            <td>
            </td>
            <td width="65">
				<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_emb','row_')" />
				<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_emb','row_');" />
			</td>
        </tr> 
		<?
	}
	exit();
}


if($action=="check_conversion_rate")
{
	//$data=explode("**",$data);
	
	/*if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}*/
	$conversion_date=date("Y/m/d");
	$exchange_rate=set_conversion_rate( $data, $conversion_date );
	echo $exchange_rate;
	exit();	
}

if($action=="check_uom")
{
	$uom=return_field_value( "order_uom","lib_item_group","id='$data'");
	echo $uom;
	exit();	
}


if ($action=="delivery_popup")
{
	echo load_html_head_contents("Delivery Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_popup(type,within_group)
		{
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			var within_group = $('#cbo_within_group').val();
			load_drop_down( 'trims_delivery_entry_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td_del' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('System ID');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
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
	<body onLoad="fnc_load_party_popup(<? echo "$data[0]";?>,<? echo "$data[3]";?>)">
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead> 
	                <tr>
	                    <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
	                </tr>
	                <tr>               	 
	                    <th width="140" class="must_entry_caption">Company Name</th>
	                    <th width="60">Within Group</th>                           
	                    <th width="140">Party Name</th>
	                    <th width="80">Search By</th>
	                    <th width="100" id="search_by_td">System ID</th>
	                    <th width="60">Year</th>
	                    <th width="170">Date Range</th>                            
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr class="general">
	                    <td><input type="hidden" id="selected_job">  <!--  echo $data;-->
	                        <? 
	                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
	                    </td>
	                    <td>
	                        <?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);",0 ); ?>
	                    </td>
	                    <td id="buyer_td_del">
	                        <? echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- select Party --", $data[2], "fnc_load_party_popup(1,this.value);" );   	 
	                        ?>
	                    </td>
	                    <td>
	                    	<?
	                            $search_by_arr=array(1=>"System ID",2=>"W/O No",4=>"Buyer Po",5=>"Buyer Style");
	                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
	                        ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
	                    </td>
	                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- select --", date('Y'), "",0 ); ?></td>
	                    <td align="center">
	                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
	                    </td>
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_delivery_search_list_view', 'search_div', 'trims_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
	                    </tr>
	                    <tr>
	                        <td colspan="9" align="center" valign="middle">
	                            <? echo load_month_buttons();  ?>
	                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
	                        </td>
	                    </tr>
	                    <tr>
	                        <td colspan="9" align="center" valign="top" id=""><div id="search_div"></div></td>
	                    </tr>
	                </tbody>
	            </table>    
	            </form>
	        </div>
	    </body>           
	    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	    </html>
	    <?
	    exit();
	}

	if($action=="create_delivery_search_list_view")
	{	
		$data=explode('_',$data);
		$party_id=str_replace("'","",$data[1]);
		$search_by=str_replace("'","",$data[4]);
		$search_str=trim(str_replace("'","",$data[5]));
		$search_type =$data[6];
		$within_group =$data[7];
		if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
		if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

		if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please select Company First."; die; }
		//echo $search_type; die;
		$job_cond=""; $style_cond=""; $po_cond=""; $search_com="";
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com="and b.order_no='$search_str'";
				else if ($search_by==4) $search_com=" and b.buyer_po_no = '$search_str' ";
				else if ($search_by==5) $search_com=" and b.buyer_style_ref = '$search_str' ";
			}
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num like '$search_str%'";  
				else if($search_by==2) $search_com="and b.order_no like '$search_str%'";  
				else if ($search_by==4) $search_com=" and b.buyer_po_no like '$search_str%'";
				else if ($search_by==5) $search_com=" and b.buyer_style_ref like '$search_str%'";  
			}
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num like '%$search_str'";  
				else if($search_by==2) $search_com="and b.order_no like '%$search_str'";  
				else if ($search_by==4) $search_com=" and b.buyer_po_no like '%$search_str'";
				else if ($search_by==5) $search_com=" and b.buyer_style_ref like '%$search_str'";  
			}
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com="and a.del_no_prefix_num like '%$search_str%'";  
				else if($search_by==2) $search_com="and b.order_no like '%$search_str%'";  
				else if ($search_by==4) $search_com=" and b.buyer_po_no like '%$search_str%'"; 
				else if ($search_by==5) $search_com=" and b.buyer_style_ref like '%$search_str%'";   
			}
		}

		if($search_str!="" && ($search_by==2 || $search_by==4 || $search_by==5))
		{
			if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			if($db_type==0) $id_cond="group_concat(b.id) as id";
			else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";

			$job_dtls_ids = return_field_value("$id_cond", "trims_job_card_mst a, trims_job_card_dtls b", "a.entry_form=257 and a.trims_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $search_com_cond", "id");
		}

		if($db_type==2 && $job_dtls_ids!="") $job_dtls_ids = $job_dtls_ids->load();
		if ($job_dtls_ids!="")
		{
			$job_dtls_ids=explode(",",$job_dtls_ids);
			$job_dtls_idsCond=""; $jobDtlsCond="";
			//echo count($job_dtls_ids); die;
			if($db_type==2 && count($job_dtls_ids)>=999)
			{
				$chunk_arr=array_chunk($job_dtls_ids,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",",$val);
					if($job_dtls_idsCond=="")
					{
						$job_dtls_idsCond.=" and ( b.job_dtls_id in ( $ids) ";
					}
					else
					{
						$job_dtls_idsCond.=" or  b.job_dtls_id in ( $ids) ";
					}
				}
				$job_dtls_idsCond.=")";
			}
			else
			{
				$ids=implode(",",$job_dtls_ids);
				$job_dtls_idsCond.=" and b.job_dtls_id in ($ids) ";
			}
		}
		else if($job_dtls_ids=='' && ($search_str!="" && ($search_by==2 || $search_by==4 || $search_by==5)))
		{
			echo "Not Found"; die;
		}	

		if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

		if($db_type==0)
		{ 
			if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $delivery_date ="";
		}
		else
		{
			if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $delivery_date ="";
		}
		if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
		if($within_group==1)
		{
			$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		}
		else
		{
			$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		}

		if($db_type==0) 
		{
			$ins_year_cond="year(a.insert_date)";
		}
		else if($db_type==2)
		{
			$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		}
		
		
		$sql= "select a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no,b.order_no ,$ins_year_cond as year from trims_delivery_mst a, trims_delivery_dtls b where a.entry_form=208 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $delivery_date $company $party_id_cond $withinGroup $search_com $withinGroup group by a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no,b.order_no ,a.insert_date order by a.id DESC";
		// echo $sql;
		 $data_array=sql_select($sql);
		?>
	     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="820" >
	        <thead>
	            <th width="30">SL</th>
	            <th width="150">Delivery No</th>
	            <th width="150">Work Order No</th>
	            <th width="80">Year</th>
	            <th width="170">Challan No.</th>
	            <th width="80">Delivery Date</th>
	            <th> Within Group</th>
	        </thead>
	        </table>
	        <div style="width:820px; max-height:270px;overflow-y:scroll;" >	 
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_po_list">
	        <tbody>
	            <? 
	            $i=1;
	            foreach($data_array as $row)
	            {  
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('trims_del')].'_'.$row[csf('received_id')]; ?>")' style="cursor:pointer" >
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="150"><? echo $row[csf('trims_del')]; ?></td>
	                    <td width="150"><? echo $row[csf('order_no')]; ?></td>
	                    <td width="80" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
	                    <td width="170"><? echo $row[csf('challan_no')]; ?></td>
	                    <td width="80"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
	                    <td style="text-align:center;"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
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

	if ($action=="load_delivery_data_to_form")
	{
		$sql="select a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no, a.remarks from trims_delivery_mst a where a.entry_form=208 and a.id=$data and a.status_active=1 ";

		$nameArray=sql_select( $sql );
		foreach ($nameArray as $row)
		{
			$order_no = return_field_value("order_no", "subcon_ord_mst", "id=".$row[csf("received_id")]."", "order_no");

			echo "document.getElementById('txt_dalivery_no').value 			= '".$row[csf("trims_del")]."';\n";
			echo "document.getElementById('update_id').value 				= '".$row[csf("id")]."';\n";
			echo "document.getElementById('received_id').value 				= '".$row[csf("received_id")]."';\n";
			echo "document.getElementById('txt_order_no').value 			= '".$order_no."';\n";  
			echo "document.getElementById('hid_order_id').value 			= '".$row[csf("order_id")]."';\n";  

			echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
			echo "document.getElementById('cbo_within_group').value 		= '".$row[csf("within_group")]."';\n";  
			
			echo "document.getElementById('txt_challan_no').value 			= '".$row[csf("challan_no")]."';\n";  
			echo "document.getElementById('txt_gate_pass_no').value 		= '".$row[csf("gate_pass_no")]."';\n";  
			echo "document.getElementById('cbo_currency').value 			= '".$row[csf("currency_id")]."';\n";  
			echo "document.getElementById('txt_remarks').value 				= '".$row[csf("remarks")]."';\n";  
			
			echo "fnc_load_party(1,'".$row[csf("within_group")]."');\n";
			echo "document.getElementById('cbo_party_name').value			= ".$row[csf("party_id")].";\n";
			echo "fnc_load_party(2,'".$row[csf("within_group")]."');\n";
			echo "document.getElementById('cbo_party_location').value		= ".$row[csf("party_location")].";\n";
			echo "document.getElementById('cbo_location_name').value 		= ".$row[csf("location_id")].";\n";
			echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($row[csf("delivery_date")])."';\n"; 
			echo "$('#cbo_company_name').attr('disabled','true')".";\n";
			echo "$('#cbo_within_group').attr('disabled','true')".";\n";
			echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		}
		exit();	
	}	

if($action=="challan_print") 
{
	extract($_REQUEST);
	//echo $data;die;
	$data=explode('*',$data);
	$cbo_template_id=$data[6];
	$sql_company = sql_select("select * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $company_data[csf('country_id')].','.' ';else $country='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
	
	
	
	$buyer_po_arr=array();
	
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		?>
	<style type="text/css">
			.opacity_1
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}	
			.opacity_2
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}
			/* .opacity_3
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			} */					
			
			@media print {
				.page-break	{ display: block; page-break-after: always;}
			}
			
			#table_1,#table_2{  background-position: center;background-repeat: no-repeat; }
			#table_1{background-image:url(../../../img/bg-1.jpg);}
			#table_2{background-image:url(../../../img/bg-2.jpg); }
			/* #table_3{background-image:url(../../../img/bg-3.jpg);} */
			
		</style>
		<?
		//echo "select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id= $data[1]";
			$sql_mst = sql_select("select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id= $data[1]");
			$inserted_by=$sql_mst[0][csf("inserted_by")];
			
	
	
	if($data[2]==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
		$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
		$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
	}
	
	//$lib_location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	//$copy_print = 3;
	//for($k=1; $k <= $copy_print; $k++)
	//{
	$k=0;	
	$copy_no=array(1,2); //for Dynamic Copy here 
	 foreach($copy_no as $cid)
	 {
		 $k++;
	?>
        
    <div style="width:1200px" class="page-break">
        <table width="100%" id="table_<? echo $cid;?>">
			<tr>
				<td rowspan="2" width="200">
					<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
				</td>
            	<td style="font-size:20px;" align="center"><strong>
					<? echo $company_arr[$data[0]]; ?></strong>
                </td>
                <td align="right" width="100">
					<? 
					if($k==1){
					echo "<b><h2>1st Copy</h2></b>";
					}
					else if($k==2){
					echo "<b><h2>2nd Copy</h2></b>";
					}
					/*else if($k==3){
					echo "3rd Copy";
					}*/
					?> 
				</td>
            </tr>
            <tr>
				<td align="center">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number,city from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
						?>
						<? echo $result[csf('city')]; ?><br>
						<b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
					}
					?> 
				</td>
        		<td id="barcode_img_id_<? echo $k; ?>"></td>
			</tr>
			<tr>
            	<td>&nbsp;</td>
            	<td style="font-size:20px;" align="center"> <strong><? echo $data[3]; ?></strong></td>
                <td>&nbsp;</td>
            </tr> 
            <tr>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr> 
        </table>
        <br>
		<table class="rpt_table" width="100%" cellspacing="1" >
            <tr>
                <td valign="top" width="100"><strong> Delivery To</strong></td>
                <td valign="top" width="150">:<strong> <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></strong></td>
                <td valign="top" width="250">&nbsp;</td>
                <td valign="top" width="120"><strong>Challan No. </strong></td>
                <td valign="top"><strong>: <? echo $data[5]; ?></strong></td>
            </tr>
            <tr>
            	<td valign="top" width="120">Address</td>
                <td valign="top">: <? echo $party_location; ?> </td>
                <td valign="top" width="250">&nbsp;</td>
                <td valign="top" width="100">Delivery Date</td>
                <td valign="top" width="150">: <? echo change_date_format($sql_mst[0][csf("delivery_date")],'yyyy-mm-dd'); ?></td>
            </tr>
            <tr>
            	<td valign="top" width="100">WO NO.</td>
                <td valign="top" width="150">: <? echo $data[4];//$order_no_trims_arr[$sql_mst[0][csf("received_id")]]['order_no']; ?></td>
            </tr>
      	</table>
         <br>
      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
      		<thead>
	            <tr>
	            	<th width="40">SL</th>
                    <th width="130">Cust. PO</th>
                    <th width="130">Buyer's Buyer </th>
                    <th width="80">Section</th>
	                <th width="90">Item Group</th>
	                <th width="140">Item Description</th>	
                    <th width="80">Item Color </th>
	                <th width="70">Item Size</th>				
	                <th width="60">Order UOM</th>
                    <th width="70">WO Qty.</th>
	                <th width="80">Cum. Delv Qty</th>
	                <th width="80">Curr. Delv Qty</th>
	                <th width="80">Delv Balance Qty</th>
	                <th>Remarks</th>
	            </tr>
            </thead>
            <tbody>
			<?
			$i = 1;
			$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
			$total_quantity=0;$total_delevery_quantity=0;$Curr_delevery_quantity=0;$delevery_Balance_quantity=0;
			$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
			$sql = "select id, mst_id, booking_dtls_id, receive_dtls_id, job_dtls_id, production_dtls_id,  order_id, order_no, buyer_po_id, buyer_po_no,  buyer_style_ref, buyer_buyer, section,   item_group as trim_group, order_uom, order_quantity,   delevery_qty, claim_qty, remarks,color_id, size_id, 
			description, delevery_status, color_name,size_name,workoder_qty,break_down_details_id from trims_delivery_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0 order by id ASC";
		  	/* 	$delevery_qty_trims_arr=array();
			$pre_sql ="select job_dtls_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by job_dtls_id";
			$pre_sql_res=sql_select($pre_sql);
			foreach ($pre_sql_res as $row)
			{
				$delevery_qty_trims_arr[$row[csf("job_dtls_id")]]['delevery_qty']=$row[csf("delevery_qty")];
				
			}
			unset($pre_sql_res);
			*/
	
			$delevery_qty_trims_arr=array();
			$pre_sql ="select break_down_details_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by break_down_details_id";
			$pre_sql_res=sql_select($pre_sql);
			foreach ($pre_sql_res as $row)
			{
				$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']=$row[csf("delevery_qty")];
			}
			unset($pre_sql_res);
			$data_array=sql_select($sql);
			foreach($data_array as $row)
			{
			?>
                <tr>
                <td><?php echo $i; ?></td>
                <td><p><?php echo $row[csf('buyer_po_no')]; ?></p></td>
                <td><p><?php if($data[2]==1)
				{  echo $buyer_arr[$row[csf('buyer_buyer')]]; } else { echo $row[csf('buyer_buyer')];  } ?></p></td>
                <td><?php echo $trims_section[$row[csf('section')]]; ?></td>
                <td><p><?php echo $item_group_arr[$row[csf('trim_group')]]; ?></p></td>
                <td><p><?php echo $row[csf('description')]; ?></p></td>	
                <td><p><?php echo $row[csf('color_name')]; ?></p> </td>
                <td><p><?php echo $row[csf('size_name')]; ?></p></td>				
                <td><?php echo $unit_of_measurement[$row[csf('order_uom')]]; $unique_uom[$row[csf('order_uom')]]=$row[csf('order_uom')]; ?></td>
                <td align="right"><?php echo $row[csf('workoder_qty')]; $total_quantity += $row[csf('workoder_qty')]; ?></td>
                <td align="right"><?php echo  
				$CumDelvQty=$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')];  
				$total_delevery_quantity += $delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')];
				 ?></td>
                <td align="right"><?php echo $row[csf('delevery_qty')];  $Curr_delevery_quantity += $row[csf('delevery_qty')];  ?></td>
                <td align="right"><?php echo number_format($row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$CumDelvQty),4); $delevery_Balance_quantity += $row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$CumDelvQty);  ?></td>
                <td><?php echo $row[csf('remarks')]; ?></td>
                </tr>
			<?
			$i++;
            } 
         	if(count($unique_uom)==1){ 
			?>
            <tr> 
				<td colspan="8"><strong>&nbsp;&nbsp;</strong></td>
				<td align="right"><strong>Total:</strong></td>
				<td align="right"><strong><? echo number_format($total_quantity,2); ?></strong></td>
				<td align="right"><strong><? echo number_format($total_delevery_quantity,2); ?></strong></td>
				<td align="right"><strong><? echo number_format($Curr_delevery_quantity,2); ?></strong></td>
				<td align="right"><strong><? echo number_format($delevery_Balance_quantity,2); ?></strong></td>
                <td><strong>&nbsp;&nbsp;</strong></td>
			</tr>
            <? } ?>
        </tbody> 
    </table>
	<?
		$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");
		echo signature_table(174, $data[0], "1200px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
    ?>	
    </div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    function generateBarcode( valuess )
    {
        var value = valuess;//$("#barcodeValue").val();
        var btype = 'code39';//$("input[name=btype]:checked").val();
        var renderer ='bmp';// $("input[name=renderer]:checked").val();
        var settings = {
          output:renderer,
          bgColor: '#FFFFFF',
          color: '#000000',
          barWidth: 1,
          barHeight: 30,
          moduleSize:5,
          posX: 10,
          posY: 20,
          addQuietZone: 1
        };
        $("#barcode_img_id_<? echo $k; ?>").html('11');
         value = {code:value, rect: false};
        $("#barcode_img_id_<? echo $k; ?> ").show().barcode(value, btype, settings);
    } 
    generateBarcode("<? echo $data[5]; ?>");
    </script>
   <?
	}
 	exit();
}

if($action=="challan_print2") 
{
	extract($_REQUEST);
	//echo $data;die;
	$data=explode('*',$data);
	$cbo_template_id=$data[6];
	$sql_company = sql_select("select * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $company_data[csf('country_id')].','.' ';else $country='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
	$buyer_po_arr=array();
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		?>
	<style type="text/css">
			.opacity_1
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}	
			.opacity_2
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			}
			 .opacity_3
			{
				opacity:0.3; 
				position: absolute; 
				font-size: 400px; 
				left: 40%; 
				top: 20%;
			} 
			
			@media print {
				.page-break	{ display: block; page-break-after: always;}
			}
			
			#table_1,#table_2{  background-position: center;background-repeat: no-repeat; }
			#table_1{background-image:url(../../../img/bg-1.jpg);}
			#table_2{background-image:url(../../../img/bg-2.jpg); }
			/* #table_3{background-image:url(../../../img/bg-3.jpg);} */
			
		</style>
		<?
		//echo "select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id= $data[1]";
			$sql_mst = sql_select("select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id= $data[1]");
			$inserted_by=$sql_mst[0][csf("inserted_by")];
			
	
	
	if($data[2]==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
		$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
		$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
	}
	
	//$lib_location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	//$copy_print = 3;
	//for($k=1; $k <= $copy_print; $k++)
	//{
	$k=0;	
	$copy_no=array(1,2,3); //for Dynamic Copy here 
	 foreach($copy_no as $cid)
	 {
		 $k++;
	?>
        
    <div style="width:1200px" class="page-break">
        <table width="100%" id="table_<? echo $cid;?>">
			<tr>
				<td rowspan="2" width="200">
					<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
				</td>
            	<td style="font-size:20px;" align="center"><strong>
					<? echo $company_arr[$data[0]]; ?></strong>
                </td>
                <td align="right" width="100">
					<? 
					if($k==1){
					echo "<b><h2>1st Copy</h2></b>";
					}
					else if($k==2){
					echo "<b><h2>2nd Copy</h2></b>";
					}
					else if($k==3){
					echo "<b><h2>3rd Copy</h2></b>";
					}
					?> 
				</td>
            </tr>
            <tr>
				<td align="center">
					<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number,city from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
						?>
						<? echo $result[csf('city')]; ?><br>
						<b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
					}
					?> 
				</td>
        		<td id="barcode_img_id_<? echo $k; ?>"></td>
			</tr>
			<tr>
            	<td>&nbsp;</td>
            	<td style="font-size:20px;" align="center"> <strong><? echo $data[3]; ?></strong></td>
                <td>&nbsp;</td>
            </tr> 
            <tr>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr> 
        </table>
        <br>
		<table class="rpt_table" width="100%" cellspacing="1" >
            <tr>
                <td valign="top" width="100"><strong> Delivery To</strong></td>
                <td valign="top" width="150">:<strong> <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></strong></td>
                <td valign="top" width="250">&nbsp;</td>
                <td valign="top" width="120"><strong>Challan No. </strong></td>
                <td valign="top"><strong>: <? echo $data[5]; ?></strong></td>
            </tr>
            <tr>
            	<td valign="top" width="120">Address</td>
                <td valign="top">: <? echo $party_location; ?> </td>
                <td valign="top" width="250">&nbsp;</td>
                <td valign="top" width="100">Delivery Date</td>
                <td valign="top" width="150">: <? echo change_date_format($sql_mst[0][csf("delivery_date")],'yyyy-mm-dd'); ?></td>
            </tr>
            <tr>
            	<td valign="top" width="100">WO NO.</td>
                <td valign="top" width="150">: <? echo $data[4]; ?></td>
                <td valign="top" width="250">&nbsp;</td>
                <td valign="top" width="100">Currencey </td>
                <td valign="top" width="150">: <? echo $currency[$sql_mst[0][csf("currency_id")]];?></td>
            </tr>
            <tr>
            	<td valign="top" width="100">Remarks</td>
                <td valign="top" width="150">: <? echo $sql_mst[0][csf("remarks")];//$order_no_trims_arr[$sql_mst[0][csf("received_id")]]['order_no']; ?></td>
                
            </tr>
      	</table>
         <br>
      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
      		<thead>
	            <tr>
	            	<th width="40">SL</th>
                    <th width="130">Cust. PO</th> 
                    <th width="130">Internal Ref.No</th>
                    <th width="130">Buyer's Buyer </th>
                    <th width="80">Section</th>
	                <th width="90">Item Group</th>
	                <th width="140">Item Description</th>	
                    <th width="80">Item Color </th>
	                <th width="70">Item Size</th>				
	                <th width="60">Order UOM</th>
                    <th width="70">WO Qty.</th>
	                <th width="80">Cum. Delv Qty</th>
	                <th width="80">Curr. Delv Qty</th>
                     <th width="80">No of Roll/Bag</th>
	                <th width="80">Delv Balance Qty</th>
	                <th>Remarks</th>
	            </tr>
            </thead>
            <tbody>
			<?
			$i = 1;
			$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
			$total_quantity=0;$total_delevery_quantity=0;$Curr_delevery_quantity=0;$delevery_Balance_quantity=0;
			$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
						
			$sql = "select a.id, a.mst_id, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id, a.production_dtls_id, a.order_id, a.order_no, b.buyer_po_id, b.buyer_po_no,  a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.order_uom, a.order_quantity,a.delevery_qty,a.claim_qty,a.remarks,a.color_id,a.size_id, 
			a.description, a.delevery_status, a.color_name,a.size_name,a.workoder_qty,a.break_down_details_id,a.no_of_roll_bag from trims_delivery_dtls a,subcon_ord_dtls b  where a.mst_id='$data[1]' and  a.receive_dtls_id=b.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id ASC";
			
			$delevery_qty_trims_arr=array();
			$pre_sql ="select break_down_details_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by break_down_details_id";
			$pre_sql_res=sql_select($pre_sql);
			foreach ($pre_sql_res as $row)
			{
				$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']=$row[csf("delevery_qty")];
			}
			unset($pre_sql_res);
			$data_array=sql_select($sql);
			$orderIds='';
			$buyer_po_id_array=array();
			foreach($data_array as $order_row)
			{
				$orderIds.=$order_row[csf('buyer_po_id')].",";
			}
			$orderIds=chop($orderIds,','); 
			$orderIds=implode(",",array_unique(explode(",",$orderIds)));
			//echo $orderIds; die;
			
			//echo "<pre>";
			//print_r($buyer_po_id_array); die;
			
			$piArray=array();
			$sql="select a.id, a.po_number,a.grouping,b.job_no,b.internal_ref from wo_po_break_down a,wo_order_entry_internal_ref b where a.job_no_mst=b.job_no and  a.id in ($orderIds)";
			$po_data=sql_select($sql);
			foreach($po_data as $row)
			{
				$piArray[$row[csf('id')]]['grouping']=$row[csf('internal_ref')];
			}
			
			foreach($data_array as $row)
			{
			?>
                <tr>
                <td><?php echo $i; ?></td>
                <td><p><?php echo $row[csf('buyer_po_no')]; ?></p></td>
                <td><p><?php echo $piArray[$row[csf('buyer_po_id')]]['grouping']; ?></p></td>
                <td><p><?php if($data[2]==1)
				{  echo $buyer_arr[$row[csf('buyer_buyer')]]; } else { echo $row[csf('buyer_buyer')];  } ?></p></td>
                <td><?php echo $trims_section[$row[csf('section')]]; ?></td>
                <td><p><?php echo $item_group_arr[$row[csf('trim_group')]]; ?></p></td>
                <td><p><?php echo $row[csf('description')]; ?></p></td>	
                <td><p><?php echo $row[csf('color_name')]; ?></p> </td>
                <td><p><?php echo $row[csf('size_name')]; ?></p></td>				
                <td><?php echo $unit_of_measurement[$row[csf('order_uom')]]; $unique_uom[$row[csf('order_uom')]]=$row[csf('order_uom')]; ?></td>
                <td align="right"><?php echo $row[csf('workoder_qty')]; $total_quantity += $row[csf('workoder_qty')]; ?></td>
                <td align="right"><?php echo  
				$CumDelvQty=$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')];  
				$total_delevery_quantity += $delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']-$row[csf('delevery_qty')];
				 ?></td>
                <td align="right"><?php echo $row[csf('delevery_qty')];  $Curr_delevery_quantity += $row[csf('delevery_qty')];  ?></td>
                 <td align="right"><?php echo $row[csf('no_of_roll_bag')];?></td>
                <td align="right"><?php echo number_format($row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$CumDelvQty),4); $delevery_Balance_quantity += $row[csf('workoder_qty')]-($row[csf('delevery_qty')]+$CumDelvQty);  ?></td>
                <td><?php echo $row[csf('remarks')]; ?></td>
                </tr>
			<?
			$i++;
            } 
         	if(count($unique_uom)==1){ 
			?>
            <tr> 
				<td colspan="9"><strong>&nbsp;&nbsp;</strong></td>
				<td align="right"><strong>Total:</strong></td>
				<td align="right"><strong><? echo number_format($total_quantity,2); ?></strong></td>
				<td align="right"><strong><? echo number_format($total_delevery_quantity,2); ?></strong></td>
				<td align="right"><strong><? echo number_format($Curr_delevery_quantity,2); ?></strong></td>
                <td><strong>&nbsp;&nbsp;</strong></td>
				<td align="right"><strong><? echo number_format($delevery_Balance_quantity,2); ?></strong></td>
                <td><strong>&nbsp;&nbsp;</strong></td>
			</tr>
            <? } ?>
        </tbody> 
    </table>
	<?
		$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");
		echo signature_table(174, $data[0], "1200px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
    ?>	
    </div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    function generateBarcode( valuess )
    {
        var value = valuess;//$("#barcodeValue").val();
        var btype = 'code39';//$("input[name=btype]:checked").val();
        var renderer ='bmp';// $("input[name=renderer]:checked").val();
        var settings = {
          output:renderer,
          bgColor: '#FFFFFF',
          color: '#000000',
          barWidth: 1,
          barHeight: 30,
          moduleSize:5,
          posX: 10,
          posY: 20,
          addQuietZone: 1
        };
        $("#barcode_img_id_<? echo $k; ?>").html('11');
         value = {code:value, rect: false};
        $("#barcode_img_id_<? echo $k; ?> ").show().barcode(value, btype, settings);
    } 
    generateBarcode("<? echo $data[5]; ?>");
    </script>
   <?
  	 
	 }
 exit();
	
 }

if($action=="del_multi_number_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode("_",$data);
	?>

	<script>

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function check_all_data()
		{
			
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			//tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var attrData=$('#tr_' +i).attr('onclick');
				var splitArr = attrData.split('"');
				js_set_value( splitArr[1] );
			}
		}

		var selected_id=Array();
		var selected_name=Array();
		var selected_ord=Array();

		function js_set_value(mrr)
		{
			//alert(mrr);
			var splitArr = mrr.split("_");
			$("#hidden_del_number").val(splitArr[1]); // mrr number
			$("#hidden_del_id").val(splitArr[2]); // id
			$("#hidden_ord_no").val(splitArr[3]); // order no

			toggle( document.getElementById( 'tr_' + splitArr[0] ), '#FFFFCC' );

	 		if( jQuery.inArray(splitArr[2], selected_id ) == -1 ) {			
	 			selected_name.push(splitArr[1]);
	 			selected_id.push( splitArr[2]);
	 			selected_ord.push( splitArr[3]);

	 		}
	 		else 
	 		{
	 			for( var i = 0; i < selected_id.length; i++ ) {
	 				if( selected_id[i] == splitArr[2]) break;
	 			} 			
	 			selected_name.splice( i, 1 );
	 			selected_id.splice( i, 1 );
	 			selected_ord.splice( i, 1 );
	 		}

	 		var id = ''; var name = ''; var ord = '';
	 		for( var i = 0; i < selected_id.length; i++ ) {
	 			id += selected_id[i] + ',';
	 			name += selected_name[i] + ',';
	 			ord += selected_ord[i] + ',';
	 		}

	 		id = id.substr( 0, id.length - 1 );
	 		name = name.substr( 0, name.length - 1 );
	 		ord = ord.substr( 0, ord.length - 1 );

	 		$('#hidden_del_id').val(id);
	 		$('#hidden_del_number').val(name);
	 		$('#hidden_ord_number').val(ord);
	 	}

	 	function fnc_close ()
	 	{
	 		parent.emailwindow.hide();
	 	}

	 	function fnc_load_party_popup(type,within_group,party)
		{
			//alert(within_group);
			var company = $('#cbo_company_name').val();
			var party_name = $('#cbo_party_name').val();
			var location_name = $('#cbo_location_name').val();
			//var within_group = $('#cbo_within_group').val();
			load_drop_down( 'trims_delivery_entry_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td_del' );
			$('#cbo_party_name').attr('disabled',true);
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0){
				$('#search_by_td').html('System ID');
			}else if(val==2){
				$('#search_by_td').html('W/O No');
			}else if(val==4){
				$('#search_by_td').html('Buyer Po');
			}else if(val==5){
				$('#search_by_td').html('Buyer Style');
			}
		}
		
 	</script>
	</head>

	<body onLoad="fnc_load_party_popup(<? echo "$data[0]";?>,<? echo "$data[3]";?>,<? echo "$data[2]";?>)">
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead> 
	                <tr>
	                    <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
	                </tr>
	                <tr>               	 
	                    <th width="140" class="must_entry_caption">Company Name</th>
	                    <th width="60">Within Group</th>                           
	                    <th width="140">Party Name</th>
	                    <th width="80">Search By</th>
	                    <th width="100" id="search_by_td">System ID</th>
	                    <th width="60">Year</th>
	                    <th width="170">Date Range</th>                            
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr class="general">
	                    <td><input type="hidden" id="selected_job">  <!--  echo $data;-->
	                        <? 
	                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
	                    </td>
	                    <td>
	                        <?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);",1 ); ?>
	                    </td>
	                    <td id="buyer_td_del">
	                        <? echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- select Party --", $data[2], "fnc_load_party_popup(1,this.value);",1 );   	 
	                        ?>
	                    </td>
	                    <td>
	                    	<?
	                            $search_by_arr=array(1=>"System ID",2=>"W/O No",4=>"Buyer Po",5=>"Buyer Style");
	                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
	                        ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
	                    </td>
	                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- select --", date('Y'), "",0 ); ?></td>
	                    <td align="center">
	                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
	                    </td>
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_multi_delivery_search_list_view', 'search_div', 'trims_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)');" style="width:70px;" /></td>
	                    </tr>
	                    <tr>
	                        <td colspan="9" align="center" valign="middle">
	                            <? echo load_month_buttons();  ?>
	                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
	                        </td>
	                    </tr>
	                    <tr>
	                        <td colspan="9" align="center" valign="top" id=""><div id="search_div"></div></td>
	                    </tr>
	                </tbody>
	            </table>
	            <table width="820" cellspacing="0" cellpadding="0" style="border:none" align="center">
					<tr>
						<td align="center" height="30" valign="bottom">
							<div style="width:100%;">
								<div style="width:50%; float:left" align="left" id="button_div">
									<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
								</div>
								<div style="width:50%; float:left" align="left">
									<input type="button" name="close" onClick="fnc_close();" class="formbutton" value="Close" style="width:100px" />
								</div>
							</div>
						</td>
					</tr>
				</table>  
			<div align="center" style="margin-top:10px" valign="top" id="search_divvvv"> </div>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}


if($action=="create_multi_delivery_search_list_view")
{	
	echo '<input type="hidden" id="hidden_del_number" value="" /><input type="hidden" id="hidden_del_id" value="" /><input type="hidden" id="hidden_ord_number" value="" />';
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please select Company First."; die; }
	//echo $search_type; die;
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com="and a.del_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com="and b.order_no='$search_str'";
			else if ($search_by==4) $search_com=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com=" and b.buyer_style_ref = '$search_str' ";
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com="and a.del_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com="and b.order_no like '$search_str%'";  
			else if ($search_by==4) $search_com=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com=" and b.buyer_style_ref like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com="and a.del_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com="and b.order_no like '%$search_str'";  
			else if ($search_by==4) $search_com=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com=" and b.buyer_style_ref like '%$search_str'";  
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com="and a.del_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com="and b.order_no like '%$search_str%'";  
			else if ($search_by==4) $search_com=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com=" and b.buyer_style_ref like '%$search_str%'";   
		}
	}

	if($search_str!="" && ($search_by==2 || $search_by==4 || $search_by==5))
	{
		if($db_type==0) $id_cond="group_concat(b.id) as id";
		else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";

		$job_dtls_ids = return_field_value("$id_cond", "trims_job_card_mst a, trims_job_card_dtls b", "a.entry_form=257 and a.trims_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $search_com_cond", "id");
	}

	if($db_type==2 && $job_dtls_ids!="") $job_dtls_ids = $job_dtls_ids->load();
	if ($job_dtls_ids!="")
	{
		$job_dtls_ids=explode(",",$job_dtls_ids);
		$job_dtls_idsCond=""; $jobDtlsCond="";
		//echo count($job_dtls_ids); die;
		if($db_type==2 && count($job_dtls_ids)>=999)
		{
			$chunk_arr=array_chunk($job_dtls_ids,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",",$val);
				if($job_dtls_idsCond=="")
				{
					$job_dtls_idsCond.=" and ( b.job_dtls_id in ( $ids) ";
				}
				else
				{
					$job_dtls_idsCond.=" or  b.job_dtls_id in ( $ids) ";
				}
			}
			$job_dtls_idsCond.=")";
		}
		else
		{
			$ids=implode(",",$job_dtls_ids);
			$job_dtls_idsCond.=" and b.job_dtls_id in ($ids) ";
		}
	}
	else if($job_dtls_ids=='' && ($search_str!="" && ($search_by==2 || $search_by==4 || $search_by==5)))
	{
		echo "Not Found"; die;
	}	

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $delivery_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $delivery_date ="";
	}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}

	if($db_type==0) 
	{
		$ins_year_cond="year(a.insert_date)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
	}
	
	
	$sql= "select a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no,b.order_no ,$ins_year_cond as year from trims_delivery_mst a, trims_delivery_dtls b where a.entry_form=208 and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $delivery_date $company $party_id_cond $withinGroup $search_com $withinGroup group by a.id, a.trims_del, a.del_no_prefix, a.del_no_prefix_num, a.company_id, a.location_id, a.within_group, a.party_id, a.party_location, a.currency_id,  a.delivery_date, a.received_id, a.order_id, a.challan_no, a.gate_pass_no,b.order_no ,a.insert_date order by a.id DESC";
	// echo $sql;
	 $data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="820" >
        <thead>
            <th width="30">SL</th>
            <th width="150">Delivery No</th>
            <th width="150">Work Order No</th>
            <th width="80">Year</th>
            <th width="170">Challan No.</th>
            <th width="80">Delivery Date</th>
            <th> Within Group</th>
        </thead>
        </table>
        <div style="width:820px; max-height:240px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                //1_FAL-YIR-20-00009_39249_20708_8978
                ?>
                <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $i.'_'.$row[csf('trims_del')].'_'.$row[csf('id')].'_'.$row[csf('order_no')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="150"><? echo $row[csf('trims_del')]; ?></td>
                    <td width="150"><? echo $row[csf('order_no')]; ?></td>
                    <td width="80" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="170"><? echo $row[csf('challan_no')]; ?></td>
                    <td width="80"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
                    <td style="text-align:center;"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
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


if($action=="multi_del_print") 
{
	extract($_REQUEST);
	//echo $data;//die;
	$data=explode('*',$data);
	$cbo_template_id=$data[6];
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_po_arr=array();
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		?>
	<style type="text/css">
		.opacity_1
		{
			opacity:0.3; 
			position: absolute; 
			font-size: 400px; 
			left: 40%; 
			top: 20%;
		}	
		.opacity_2
		{
			opacity:0.3; 
			position: absolute; 
			font-size: 400px; 
			left: 40%; 
			top: 20%;
		}
		 .opacity_3
		{
			opacity:0.3; 
			position: absolute; 
			font-size: 400px; 
			left: 40%; 
			top: 20%;
		} 

		@media print {
			.page-break	{ display: block; page-break-after: always;}
		}
		
		#table_1,#table_2{  background-position: center;background-repeat: no-repeat; }
		#table_1{background-image:url(../../../img/bg-1.jpg);}
		#table_2{background-image:url(../../../img/bg-2.jpg); }
		/* #table_3{background-image:url(../../../img/bg-3.jpg);} */
		
	</style>
	<?
	$sql_mst = sql_select("select id, entry_form, trims_del, del_no_prefix, del_no_prefix_num, company_id, location_id, within_group, party_id, party_location, currency_id,  delivery_date, received_id, order_id, challan_no, gate_pass_no, remarks,inserted_by from trims_delivery_mst where id in($data[1])");
	$inserted_by=$sql_mst[0][csf("inserted_by")]; 

	if($data[2]==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$party_loc_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
		$party_location=$party_loc_arr[$sql_mst[0][csf("party_location")]];
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
		$party_location=$party_loc_arr[$sql_mst[0][csf("party_id")]];
	}
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$com_dtl_info=fnc_company_location_address($data[0],'',1);
	$k=0;	
	$copy_no=array(1,2,3); //for Dynamic Copy here 
	foreach($copy_no as $cid)
	{
		$k++;
		?>
	    <div style="width:1500px" class="page-break">
	        <table width="100%" id="table_<? echo $cid;?>">
				<tr>
					<td rowspan="2" width="200">
						<img src="../../<? echo $com_dtl_info[2]; ?>" height="60" width="200" style="float:left;">
					</td>
	            	<td style="font-size:20px;" align="center"><strong>
						<? echo $com_dtl_info[0]; ?></strong>
	                </td>
	                <td align="right" width="100">
						<? 
						if($k==1){
						echo "<b><h2>1st Copy</h2></b>";
						}
						else if($k==2){
						echo "<b><h2>2nd Copy</h2></b>";
						}
						else if($k==3){
						echo "<b><h2>3rd Copy</h2></b>";
						}
						?> 
					</td>
	            </tr>
	            <tr>
					<td align="center">
						<?
						echo $com_dtl_info[1];
						?> 
					</td>
	        		<td id="barcode_img_id_<? echo $k; ?>"></td>
				</tr>
				<tr>
	            	<td>&nbsp;</td>
	            	<td style="font-size:20px;" align="center"> <strong><? echo $data[3]; ?></strong></td>
	                <td>&nbsp;</td>
	            </tr> 
	            <tr>
	            	<td>&nbsp;</td>
	            	<td>&nbsp;</td>
	                <td>&nbsp;</td>
	            </tr> 
	        </table>
	        <br>
			<table class="rpt_table" width="100%" cellspacing="1" >
	            <tr>
	                <td valign="top" width="100"><strong> Delivery To</strong></td>
	                <td valign="top">:<strong> <? echo $party_arr[$sql_mst[0][csf("party_id")]]; ?></strong></td>
	            </tr>
	            <tr>
	            	<td valign="top" width="120">Address</td>
	                <td valign="top">: <? echo $party_location; ?> </td>
	            </tr>
	      	</table>
	        <br>
	      	<table  class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
	      		<thead>
		            <tr>
		            	<th width="40">SL</th>
	                    <th width="130">Cust. PO</th>
	                    <th width="150">Cust. WO No</th> 
	                    <th width="150">Delivery Challan</th> 
	                    <th width="60">Delivery Date</th> 
	                    <th width="80">Internal Ref.No</th>
	                    <th width="130">Cust Buyer </th>
	                    <th width="80">Section</th>
		                <th width="90">Item Group</th>
		                <th width="140">Item Description</th>	
	                    <th width="80">Item Color </th>
		                <th width="70">Item Size</th>				
		                <th width="60">Order UOM</th>
	                    <th width="70">WO Qty.</th>
		                <th width="80">Cum. Delv Qty</th>
		                <th width="80">Curr. Delv Qty</th>
	                    <th width="80">No of Roll/Bag</th>
		                <th width="80">Delv Balance Qty</th>
		                <th>Remarks</th>
		            </tr>
	            </thead>
	            <tbody>
				<?
				$i = 1;
				$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
				$total_quantity=0;$total_delevery_quantity=0;$Curr_delevery_quantity=0;$delevery_Balance_quantity=0;
				$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
							
				$sql = "select a.id, a.mst_id, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id, a.production_dtls_id, a.order_id, a.order_no, b.buyer_po_id, b.buyer_po_no,  a.buyer_style_ref, a.buyer_buyer, a.section, a.item_group as trim_group, a.order_uom, a.order_quantity,a.delevery_qty,a.claim_qty,a.remarks,a.color_id,a.size_id, a.description, a.delevery_status,a.color_name,a.size_name,a.workoder_qty,a.break_down_details_id,a.no_of_roll_bag,c.trims_del, c.delivery_date from trims_delivery_dtls a,subcon_ord_dtls b,trims_delivery_mst c  where a.mst_id in($data[1]) and  a.receive_dtls_id=b.id and a.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id ASC";
				
				$delevery_qty_trims_arr=array();
				$pre_sql ="select break_down_details_id, sum(delevery_qty) as delevery_qty  from trims_delivery_dtls where status_active=1 and is_deleted=0 group by break_down_details_id";
				$pre_sql_res=sql_select($pre_sql);
				foreach ($pre_sql_res as $row)
				{
					$delevery_qty_trims_arr[$row[csf("break_down_details_id")]]['delevery_qty']=$row[csf("delevery_qty")];
				}
				unset($pre_sql_res);
				$data_array=sql_select($sql);
				$orderIds='';
				$buyer_po_id_array=array();
				foreach($data_array as $row)
				{
					$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['buyer_po_no']=$row[csf("buyer_po_no")];
					$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['order_no']=$row[csf("order_no")];
					$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['trims_del']=$row[csf("trims_del")];
					$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['delivery_date']=$row[csf("delivery_date")];
					$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['buyer_po_id']=$row[csf("buyer_po_id")];
					$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['buyer_buyer']=$row[csf("buyer_buyer")];
					$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['section']=$row[csf("section")];
					$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['description']=$row[csf("description")];
					$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['color_name']=$row[csf("color_name")];
					$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['size_name']=$row[csf("size_name")];
					$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['workoder_qty']=$row[csf("workoder_qty")];
					$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['break_down_details_id']=$row[csf("break_down_details_id")];
					$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['delevery_qty']=$row[csf("delevery_qty")];
					$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['no_of_roll_bag']=$row[csf("no_of_roll_bag")];
					$all_data_arr[$row[csf("trim_group")]][$row[csf("order_uom")]][$row[csf("trims_del")]][$row[csf("id")]]['remarks']=$row[csf("remarks")];
					$orderIds.=$row[csf('buyer_po_id')].",";
				}

				$orderIds=chop($orderIds,','); 
				$orderIds=implode(",",array_unique(explode(",",$orderIds)));
				//echo $orderIds; die;
				
				//echo "<pre>";
				//print_r($buyer_po_id_array); die;
				
				$piArray=array();
				$sql="select a.id, a.po_number,a.grouping,b.job_no,b.internal_ref from wo_po_break_down a,wo_order_entry_internal_ref b where a.job_no_mst=b.job_no and  a.id in ($orderIds)";
				$po_data=sql_select($sql);
				foreach($po_data as $row)
				{
					$piArray[$row[csf('id')]]['grouping']=$row[csf('internal_ref')];
				}
				//print_r($all_data_arr);

				foreach($all_data_arr as $trimGroup => $trimGroup_arr)
				{
					$total_quantity=$CumDelvQty=$total_delevery_quantity=$delevery_Balance_quantity='';
					foreach($trimGroup_arr as $trimUOM => $trimUOM_arr)
					{
						foreach($trimUOM_arr as $trim_del => $trim_del_arr)
						{
							foreach($trim_del_arr as $id => $row)
							{
								?>
				                <tr>
				                <td><?php echo $i; ?></td>
				                <td><p><?php echo $row['buyer_po_no']; ?></p></td>
				                <td><p><?php echo $row['order_no']; ?></p></td>
				                <td><p><?php echo $trim_del; ?></p></td>
				                <td><p><?php echo change_date_format($row['delivery_date']); ?></p></td>
				                <td><p><?php echo $piArray[$row['buyer_po_id']]['grouping']; ?></p></td>
				                <td><p><?php if($data[2]==1)
								{  echo $buyer_arr[$row['buyer_buyer']]; } else { echo $row['buyer_buyer'];  } ?></p></td> 
				                <td><?php echo $trims_section[$row['section']]; ?></td>
				                <td><p><?php echo $item_group_arr[$trimGroup]; ?></p></td>
				                <td><p><?php echo $row['description']; ?></p></td>	
				                <td><p><?php echo $row['color_name']; ?></p> </td>
				                <td><p><?php echo $row['size_name']; ?></p></td>				
				                <td><?php echo $unit_of_measurement[$trimUOM]; $unique_uom[$trimUOM]=$trimUOM; ?></td>
				                <td align="right"><?php echo number_format($row['workoder_qty'],4); $total_quantity += $row['workoder_qty']; ?></td>
				                <td align="right"><?php  
								$CumDelvQty=$delevery_qty_trims_arr[$row["break_down_details_id"]]['delevery_qty']-$row['delevery_qty'];  
								$total_delevery_quantity += $delevery_qty_trims_arr[$row["break_down_details_id"]]['delevery_qty']-$row['delevery_qty'];
								echo number_format($CumDelvQty,4);
								 ?></td>
				                <td align="right"><?php echo number_format($row['delevery_qty'],4);  $Curr_delevery_quantity += $row['delevery_qty'];  ?></td>
				                 <td align="right"><?php echo $row['no_of_roll_bag'];?></td>
				                <td align="right"><?php echo number_format($row['workoder_qty']-($row['delevery_qty']+$CumDelvQty),4); $delevery_Balance_quantity += $row['workoder_qty']-($row['delevery_qty']+$CumDelvQty);  ?></td>
				                <td><?php echo $row['remarks']; ?></td>
				                </tr>
								<?
							}
						}
					}
					$i++;
					?>
		            <tr> 
						<td colspan="13" align="right"><strong>UOM Wise Total:</strong></td>
						<td align="right"><strong><? echo number_format($total_quantity,4); ?></strong></td>
						<td align="right"><strong><? echo number_format($total_delevery_quantity,4); ?></strong></td>
						<td align="right"><strong><? echo number_format($Curr_delevery_quantity,4); ?></strong></td>
		                <td><strong>&nbsp;&nbsp;</strong></td>
						<td align="right"><strong><? echo number_format($delevery_Balance_quantity,4); ?></strong></td>
		                <td><strong>&nbsp;&nbsp;</strong></td>
					</tr>
	            <?
	            } 
	         	?>
	        </tbody> 
	    </table>
		<?
			$user_lib_name=return_library_array("select id,user_full_name from user_passwd where id=$inserted_by", "id", "user_full_name");
			echo signature_table(174, $data[0], "1500px",$cbo_template_id,70,$user_lib_name[$inserted_by]);
	    ?>	
	    </div>
	   	<?
	}
 exit();
}
?>