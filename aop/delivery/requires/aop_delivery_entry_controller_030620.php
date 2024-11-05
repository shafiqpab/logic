<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$process_finishing="4";

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) $dropdown_name="cbo_location_name";
	else $dropdown_name="cbo_party_location";
	echo create_drop_down( $dropdown_name, 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",1 );	
	exit();
}

if ($action=="load_drop_down_delv_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_delivery_location", 150, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
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



if ($action=="load_drop_down_order_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 


if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$location_id=$data[1];
	if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and b.location_id=$location_id";
	if($db_type==0)
	{
		$group_cond=" GROUP BY a.id";	
	}
	else if($db_type==2)
	{
		$group_cond=" GROUP BY a.id, a.floor_name";	
	}
	
	echo create_drop_down( "cbo_floor_id", 140, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=4 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=4 $location_cond $group_cond order by a.floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/aop_delivery_entry_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );","" );
  exit();	 
}

if ($action=="load_drop_machine")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$floor_id=$data[1];
	if($floor_id==0 || $floor_id=="") $floor_cond=""; else $floor_cond=" and floor_id=$floor_id";
	
	if($db_type==0)
	{
		$sql="select id, concat(machine_no, '-', brand) as machine_name from lib_machine_name where category_id=4 and company_id=$company_id  and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name";
	}
	else if($db_type==2)
	{
		$sql="select id, machine_no || '-' || brand as machine_name from lib_machine_name where category_id=4 and company_id=$company_id  and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name";
	}
	
	
	echo create_drop_down( "cbo_machine_id", 140, $sql,"id,machine_name", 1, "-- Select Machine --", $selected, "","" );
	exit();
}


if ($action=="order_number_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	//$ex_data=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('production_id').value=id;
			parent.emailwindow.hide();
		}
		
	function search_by(val)
	{
		$('#txt_search_common').val('');
		if(val==5 || val==0) $('#search_td').html('Batch No');
		else if(val==1) $('#search_td').html('WO No');
		else if(val==2) $('#search_td').html('Job No');
		else if(val==3) $('#search_td').html('Buyer Style Ref');
		else if(val==4) $('#search_td').html('Buyer PO');
	}

	function fnc_load_party_order_popup(company,within_group,party_name)
	{ 
		load_drop_down( 'aop_delivery_entry_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_order_buyer', 'buyer_td' );
		$('#cbo_party_name').attr('disabled',true);
	}
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $within_group;?>,<? echo $party_name;?>)" >
        <div align="center" style="width:100%;" >
        <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
        <table width="1060" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                	<th colspan="10" align="center">
               			 <? echo create_drop_down( "cbo_search_category", 110, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
                	</th>
                </tr>
                <tr>               	 
                    <th width="120" class="must_entry_caption">Company Name</th>
                    <th width="60">Within Group</th>                           
                    <th width="140">Party Name</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_td">Batch No</th>
                    <th width="80">QC ID</th>
                    <th width="100">AOP Ref.</th>
                    <th width="100">Year</th>
                    <th width="170">Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>         
            </thead>
            <tbody>
                <tr>
                    <td><input type="hidden" id="selected_job">
                        <? 
                        echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", $within_group, "fnc_load_party_popup(1,this.value);",1 ); ?>
                    </td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );   	 
                        ?>
                    </td>
                    <td>
						<?
                            $searchtype_arr=array(1=>"WO No",2=>"Job No",3=>"Buyer Style Ref",4=>"Buyer PO",5=>"Batch No");
                            echo create_drop_down( "cbo_search_type",100, $searchtype_arr,"",0, "",5,'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td>
                        <input type="text" name="txt_qc_id" id="txt_qc_id" class="text_boxes" style="width:80px" />
                        <input type="hidden" id="production_id">
                    </td>
                    <td>
                        <input type="text" name="txt_aop_ref" id="txt_aop_ref" class="text_boxes" style="width:87px" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_qc_id').value+'_'+document.getElementById('txt_aop_ref').value, 'order_id_search_list_view', 'search_div', 'aop_delivery_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                        <td colspan="10" align="center" valign="middle">
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10" align="center" valign="top" id=""><div id="search_div"></div></td>
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

if($action=="order_id_search_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =str_replace("'","",$data[7]);
	$qc_id =$data[9];
	$aop_reference =$data[10];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	//echo $search_type; die;
	$job_cond=""; $style_cond=""; $po_cond=""; $batch_cond=""; $qc_cond=""; $search_com_cond=""; $aop_cond=""; 
	// $searchtype_arr=array(1=>"WO No",2=>"Job No",3=>"Buyer Style Ref",4=>"Buyer PO",5=>"Batch No");
	if($qc_id!="")
	{
		$qc_cond=" and c.product_no like '%$qc_id%'";  
	}
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==1) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==2) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==3) $style_cond=" and a.style_ref_no = '$search_str' ";
			else if ($search_by==5) $batch_cond=" b.batch_no = '$search_str' ";
		}
		if($aop_reference !='') $aop_cond="and a.aop_reference='$aop_reference'";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==2) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
			else if ($search_by==3) $style_cond=" and a.style_ref_no like '%$search_str%'";
			else if ($search_by==5) $batch_cond=" b.batch_no like '%$search_str%'";
		}
		if($aop_reference !='') $aop_cond="and a.aop_reference like '%$aop_reference%'";  
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";
			else if ($search_by==3) $style_cond=" and a.style_ref_no like '$search_str%'"; 
			else if ($search_by==5) $batch_cond=" b.batch_no like '$search_str%'"; 
		}
		if($aop_reference !='') $aop_cond="and a.aop_reference like '$aop_reference%'";  
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==2) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
			else if ($search_by==3) $style_cond=" and a.style_ref_no like '%$search_str'";
			else if ($search_by==5) $batch_cond=" b.batch_no like '%$search_str'";
		}
		if($aop_reference !='') $aop_cond="and a.aop_reference like '%$aop_reference'";  
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
	
	$po_ids=''; $batch_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)"; 
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "pro_batch_create_mst a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}

	if($batch_cond!="" && $search_by==5)
	{
		//echo "select $id_cond as id from pro_batch_create_mst where $batch_cond"; die;
		$batch_ids = return_field_value("$id_cond as id", "pro_batch_create_mst b", "$batch_cond", "id");
	}

	//echo $batch_ids."==";
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	if ($batch_ids!="") $batch_idsCond=" and d.batch_id in ($batch_ids)"; else $batch_idsCond="";
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
		$aopcolor_id_str="group_concat(b.aop_color_id)";
		$buyer_po_id_str="group_concat(b.buyer_po_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$aopcolor_id_str="listagg(b.aop_color_id,',') within group (order by b.aop_color_id)";
		$buyer_po_id_str="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}
	 /*$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $aopcolor_id_str as color_id, $buyer_po_id_str as buyer_po_id
	 from subcon_ord_mst a, subcon_ord_dtls b 
	 where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $withinGroup
	 group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date
	 order by a.id DESC";*/
	
	$sql= "select c.id,a.company_id,a.within_group,a.subcon_job,a.aop_reference,c.product_no,b.order_no, $aopcolor_id_str as color_id, $buyer_po_id_str as buyer_po_id,d.batch_id,d.order_id,d.production_id,d.product_qnty from subcon_ord_mst a ,subcon_ord_dtls b,subcon_production_mst c, subcon_production_dtls d where a.id=b.mst_id and to_char(b.id)=d.order_id and c.id=d.mst_id and a.entry_form=278 $company $party_id_cond $withinGroup $search_com_cond $aop_cond $po_idsCond $batch_idsCond $qc_cond $withinGroup and c.entry_form=294 group by c.id,a.company_id,a.within_group,a.subcon_job,a.aop_reference,c.product_no,b.order_no ,d.batch_id,d.order_id,d.production_id,d.product_qnty";

	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$aopcolor_id_str="group_concat(b.aop_color_id)";
		$buyer_po_id_str="group_concat(b.buyer_po_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$aopcolor_id_str="listagg(b.aop_color_id,',') within group (order by b.aop_color_id)";
		$buyer_po_id_str="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}
	
	
	//echo $sql;
	$data_array=sql_select($sql);
	$production_qty_array=array();
	$prod_sql="  SELECT b.production_id,b.batch_id, b.order_id, SUM (b.product_qnty) AS product_qnty FROM subcon_production_dtls b, subcon_production_mst a WHERE a.id = b.mst_id AND b.status_active = 1 AND b.is_deleted = 0 AND a.entry_form = 307 GROUP BY b.production_id ,b.batch_id, b.order_id";
	$prod_data_sql=sql_select($prod_sql);
	foreach($prod_data_sql as $row)
	{
		$production_qty_array[$row[csf('production_id')]][$row[csf('batch_id')]][$row[csf('order_id')]]=$row[csf('product_qnty')];
	}
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1050" >
        <thead>
            <th width="30">SL</th>
            <th width="100">Work Order No</th>
            <th width="120">QC ID</th>
            <th width="100">Job No</th>
            <th width="100">Buyer PO</th>
            <th width="70">Buyer Style Ref</th>
            <th width="100">Batch No.</th>
            <th width="100">Color</th>
            <th width="100" >AOP Ref.</th>
            <th width="100">Delv. Qty</th>
            <th>Bal. Qty</th>
        </thead>
        </table>
        <div style="width:1050px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            $color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
            $batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$excolor_id=array_unique(explode(",",$row[csf('color_id')]));

				$color_name="";
				if(count($excolor_id)>1)
				{
					foreach ($excolor_id as $color_id)
					{
						if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
					}
				}
				else
				{
					$color_name=$color_arr[$row[csf('color_id')]];	
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
				$click_data=$row[csf('id')]."_".$row[csf('within_group')]."_".$row[csf('company_id')];
				
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $click_data ?>')" style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="100" align="center"><? echo $row[csf('order_no')]; ?></td>
                    <td width="120" align="center"><? echo $row[csf('product_no')]; ?></td>
                    <td width="100"><? echo $row[csf('subcon_job')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                    <td width="70" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $color_name; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $row[csf('aop_reference')]; ?></td>
                    <td width="100" align="right" style="word-break:break-all"><? echo  number_format($production_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('order_id')]],2,'.',''); ?></td>
                    <td align="right" style="word-break:break-all"><? echo number_format($row[csf('product_qnty')]-$production_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('order_id')]],2,'.',''); ?></td>
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

if ($action=="load_php_data_to_form_mst")
{
	$data=explode("_",$data);
	//echo "select c.id,a.company_id,a.within_group,a.subcon_job,c.product_no,b.order_no,a.location_id,a.party_location buyer_po_id,d.batch_id,d.order_id from subcon_ord_mst a ,subcon_ord_dtls b,subcon_production_mst c, subcon_production_dtls d where a.id=b.mst_id and to_char(b.id)=d.order_id and c.id=d.mst_id and a.entry_form=278 and c.entry_form=294 and c.id='$data[0]' and a.within_group='$data[1]' and a.company_id='$data[2]'"; die;
	
	$nameArray=sql_select( "select c.id,a.company_id,a.within_group,a.subcon_job,c.product_no,b.order_no,a.location_id,a.party_location,b.buyer_po_id,d.batch_id,d.order_id from subcon_ord_mst a ,subcon_ord_dtls b,subcon_production_mst c, subcon_production_dtls d where a.id=b.mst_id and to_char(b.id)=d.order_id and c.id=d.mst_id and a.entry_form=278 and c.entry_form=294 and c.id='$data[0]' and a.within_group='$data[1]' and a.company_id='$data[2]'" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_order_no').value 			= '".$row[csf("order_no")]."';\n";
		echo "document.getElementById('txt_order_id').value 			= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('cbo_location_name').value		= '".$row[csf("location_id")]."';\n"; 
		echo "document.getElementById('cbo_party_location').value		= '".$row[csf("party_location")]."';\n"; 

		echo "disable_enable_fields('cbo_location_name*cbo_party_location',1);\n";
		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'aop_delevery_entry',1);\n";
	}
	exit();	
}

if($action=="show_fabric_desc_listview")
{
	$data=explode('_',$data);	
	//$gsm_arr=return_library_array( "select id,gsm from lib_subcon_charge",'id','gsm');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

	$production_qty_array=array(); 
	$prod_sql="  SELECT b.production_id,b.batch_id, b.order_id, b.product_qnty FROM subcon_production_dtls b, subcon_production_mst a WHERE a.id = b.mst_id AND b.status_active = 1 AND b.is_deleted = 0 AND a.entry_form = 307";
	$prod_data_sql=sql_select($prod_sql);
	foreach($prod_data_sql as $row)
	{
		$production_qty_array[$row[csf('production_id')]][$row[csf('batch_id')]][$row[csf('order_id')]]['product_qnty'] +=$row[csf('product_qnty')];
		//$production_qty_array[$row[csf('production_id')]][$row[csf('batch_id')]][$row[csf('order_id')]]['delivery_status'] =$row[csf('delivery_status')];
		
	}
	//print_r($production_qty_array);
	$po_sql ="Select a.style_ref_no, b.id, b.po_number,a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		//$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}

	/*$sql = "select  a.id,b.fabric_description,b.color_id,b.product_qnty,b.process,b.no_of_roll,b.floor_id,b.shift,b.uom_id,b.dia_width,b.gsm,b.order_id,b.batch_id,b.body_part_id,b.buyer_po_id,b.production_id from  subcon_production_mst a, subcon_production_dtls b, subcon_ord_dtls c where a.id in($data[0]) and a.id=b.mst_id and b.order_id=c.id and c.delivery_status <> 3 and a.entry_form=294 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";*/
	
	$sql = "select  a.id,b.fabric_description,b.color_id,b.product_qnty,b.process,b.no_of_roll,b.floor_id,b.shift,b.uom_id,b.dia_width,b.gsm,b.order_id,b.batch_id,b.body_part_id,b.buyer_po_id,b.production_id,c.delivery_status from  subcon_production_mst a, subcon_production_dtls b, subcon_ord_dtls c where a.id in($data[0]) and a.id=b.mst_id and b.order_id=c.id and c.delivery_status <> 3  and a.entry_form=294 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400">
        <thead>
            <th width="15">SL</th>
            <th>Fabric Description</th>
            <th width="60">Color</th>
            <th width="60">QC Qty</th>
            <th width="40">Del. Qty</th>
            <th width="60">Bal. Qty</th>
        </thead>
        <tbody>
            <?
            $i=1; $po='';  $style='';
            foreach($data_array as $row)
            {  
            	/*$order_ids=explode(',',$row[csf('order_id')]); $pos=''; $styles='';
		    	for($i=0;$i<count($order_ids); $i++)
		    	{
		    		$pos.=$buyer_po_arr[$order_ids][$i]['po'].",";
		    		$styles.=$buyer_po_arr[$order_ids][$i]['style'].",";
		    	}
		    	$pos=chop($pos,","); $styles=chop($styles,",");*/
		    	$po=$buyer_po_arr[$row[csf('buyer_po_id')]]['po'];
		    	$style=$buyer_po_arr[$row[csf('buyer_po_id')]]['style'];
		    	$balance=$row[csf('product_qnty')]-$production_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('order_id')]]['product_qnty'];
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             	?>

                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('id')]."**".$row[csf('fabric_description')]."**".$row[csf('gsm')]."**".$row[csf('dia_width')]."**".$row[csf('color_id')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('process')]."**".$row[csf('no_of_roll')]."**".$row[csf('shift')]."**".$row[csf('uom_id')]."**".$po."**".$style."**".$row[csf('buyer_po_id')]."**".$row[csf('order_id')]."**".$row[csf('batch_id')]."**".$row[csf('body_part_id')]."**".$row[csf('floor_id')]."**".$row[csf('product_qnty')]."**".$balance."**".$row[csf('delivery_status')]; ?>")' style="cursor:pointer" >
                    <td><? echo $i; ?></td>
                    <td><p><? echo $row[csf('fabric_description')]; ?></p></td>
                    <td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td align="right"><? echo number_format($row[csf('product_qnty')],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($production_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('order_id')]]['product_qnty'],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($balance,2,'.',''); ?></td>
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

if ($action=="qc_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if($db_type==0)
	{ 
		if ($data[1]!="" &&  $data[2]!="") $production_date_cond = "and a.product_date between '".change_date_format($data[1],'yyyy-mm-dd')."' and '".change_date_format($data[2],'yyyy-mm-dd')."'"; else $production_date_cond ="";
	}
	else
	{
		if ($data[1]!="" &&  $data[2]!="") $production_date_cond = "and a.product_date between '".change_date_format($data[1], "", "",1)."' and '".change_date_format($data[2], "", "",1)."'"; else $production_date_cond ="";
	}
	
	if ($data[3]!='') $product_id_cond=" and a.prefix_no_num='$data[3]'"; else $product_id_cond="";
	if ($data[4]!=0) $buyer_cond=" and a.party_id='$data[4]'"; else $buyer_cond="";
	
	$return_to=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$batch_array=array();
	$batch_id_sql="select id, batch_no, extention_no from pro_batch_create_mst where entry_form=36 and status_active=1 and is_deleted=0";
	$batch_id_sql_result=sql_select($batch_id_sql);
	foreach ($batch_id_sql_result as $row)
	{
		$batch_array[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_array[$row[csf("id")]]["extention_no"]=$row[csf("extention_no")];
	}
	//var_dump($batch_array);
	//$arr=array (2=>$receive_basis_arr,3=>$return_to);
	if($db_type==0)
	{
		$year_cond= "year(a.insert_date)as year";
		$batch_cond="group_concat(b.batch_id) as batch_id";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
		$batch_cond="listagg((cast(b.batch_id as varchar2(4000))),',') within group (order by b.batch_id) as batch_id";
	}
	$sql= "select a.id, product_no, a.prefix_no_num, $year_cond, a.party_id, a.product_date, a.prod_chalan_no, $batch_cond, sum(b.product_qnty) as product_qnty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=307 and a.status_active=1 $company_name $buyer_cond $production_date_cond $product_id_cond group by a.id, product_no, a.prefix_no_num, a.insert_date, a.party_id, a.product_date, a.prod_chalan_no order by a.id DESC";
	
	//echo  create_list_view("list_view", "Prod. ID,Year,Basis,Party,Prod. Date,Product Challan", "80,80,120,120,70,120","750","250",0, $sql , "js_set_value", "id", "", 1, "0,0,basis,party_id,0,0", $arr , "prefix_no_num,year,basis,party_id,product_date,prod_chalan_no", "aop_delivery_entry_controller","",'0,0,0,0,3,0');
    ?>
    <div>
    <script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_po_list',-1);
        });

	</script>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
            <thead>
                <th width="30" >SL</th>
                <th width="60" >DOE ID</th>
                <th width="60" >Year</th>
                <th width="120" >Party</th>
                <th>QC Date</th>
            </thead>
     	</table>
     </div>
     <div style="width:750px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_po_list">
			<?
			$result_sql= sql_select($sql);
			$i=1;
            foreach($result_sql as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>);" > 
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="60" align="center"><? echo $row[csf("prefix_no_num")]; ?></td>
                        <td width="60" align="center"><? echo $row[csf("year")]; ?></td>		
						<td width="120"><? echo $return_to[$row[csf("party_id")]];  ?></td>	
						<td><? echo change_date_format($row[csf("product_date")]); ?></td>
					</tr>
				<? 
				$i++;
            }
   		?>
			</table>
		</div> 
    <?
	exit();
}



if ($action=="batch_numbers_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_batch_id').value=id;
			parent.emailwindow.hide();
		}		
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
            <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <tr>
                        <th colspan="4"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                	<tr>               	 
                        <th width="160">Company Name</th>
                        <th width="120">Batch No</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    </tr>             
                </thead>
                <tbody>
                    <tr>
                        <td> <input type="hidden" id="selected_batch_id">  
                            <?   
                                $data=explode("_",$data);
                                echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:95px" />
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                        </td> 
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_string_search_type').value, 'batch_search_list_view', 'search_div', 'aop_delivery_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center" height="40" valign="middle">
							<? echo load_month_buttons(1);  ?>
                        </td>
                    </tr>
                 </tbody>
            </table>    
            </form>
            <div id="search_div"></div>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="batch_search_list_view")
{
	//echo $data; die;
	$data=explode('_',$data);
	$search_type =$data[4];

	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	
	if ($data[0]!=0) $company_con=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if($db_type==0)
	{ 
		if ($data[1]!="" &&  $data[2]!="") $batch_date_cond = "and a.batch_date between '".change_date_format($data[1],'yyyy-mm-dd')."' and '".change_date_format($data[2],'yyyy-mm-dd')."'"; else $batch_date_cond ="";
	}
	else
	{
		if ($data[1]!="" &&  $data[2]!="") $batch_date_cond = "and a.batch_date between '".change_date_format($data[1], "", "",1)."' and '".change_date_format($data[2], "", "",1)."'"; else $batch_date_cond ="";
	}
	
	if($search_type==1)
	{
		if ($data[3]!='') $batch_no_cond=" and a.batch_no='$data[3]'"; else $batch_no_cond="";	
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($data[3]!='') $batch_no_cond=" and a.batch_no like '%$data[3]%'"; else $batch_no_cond="";
	}
	else if($search_type==2)
	{
		if ($data[3]!='') $batch_no_cond=" and a.batch_no like '$data[3]%'"; else $batch_no_cond="";
	}
	else if($search_type==3)
	{
		if ($data[3]!='') $batch_no_cond=" and a.batch_no like '%$data[3]'"; else $batch_no_cond="";
	}
	
	if($db_type==0)
	{
		$sql="select a.id, a.batch_no, a.extention_no, a.color_id, a.batch_weight, a.total_liquor, sum(b.batch_qnty) as batch_qnty, group_concat(distinct(b.po_id)) as po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.entry_form=281 $company_con $batch_date_cond $batch_no_cond group by a.batch_no, a.extention_no order by a.id DESC";// and a.batch_against=1
	}
	else if($db_type==2)
	{
		$sql="select a.id, a.batch_no, a.extention_no, a.color_id, a.batch_weight, a.total_liquor, sum(b.batch_qnty) as batch_qnty, listagg(b.po_id,',') within group (order by b.po_id) as po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.entry_form=281 $company_con $batch_date_cond $batch_no_cond group by a.id, a.batch_no, a.extention_no, a.color_id, a.batch_weight, a.total_liquor order by a.id DESC";// and a.batch_against=1
	}
	//echo $sql; die;
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
            <thead>
                <th width="30" >SL</th>
                <th width="100" >Batch no</th>
                <th width="100" >Batch Ext.</th>
                <th width="120" >Batch Color</th>
                <th width="100" >Batch weight</th>
                <th width="80" >Batch liquor</th>
                <th>Order No</th>
            </thead>
     	</table>
     <div style="width:750px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="list_view">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//$order_id=explode(',',$row[csf("po_id")]);
				$order_no='';
				$order_id=array_unique(explode(",",$row[csf("po_id")]));	
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
				}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>);" > 
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $row[csf("batch_no")]; ?></td>
                        <td width="100" align="center"><? echo $row[csf("extention_no")]; ?></td>
                        <td width="120" align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>		
						<td width="100" align="right"><? echo number_format($row[csf("batch_weight")],2); ?></td>
						<td width="80" align="right"><? echo number_format($row[csf("total_liquor")],2);  ?></td>	
						<td><p><? echo $order_no; ?></p></td>
					</tr>
				<? 
				$i++;
            }
   			?>
			</table>
		</div>
     </div>
    <?
	exit();
}

if($action=="load_php_data_to_form_batch")
{
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	$job_no_arr=return_library_array( "select id,job_no_mst from subcon_ord_dtls",'id','job_no_mst');
	$process_arr=return_library_array( "select id,main_process_id from subcon_ord_dtls",'id','main_process_id');
	$party_id_arr=return_library_array( "select subcon_job,party_id from subcon_ord_mst",'subcon_job','party_id');
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	
	//echo "select a.batch_no, a.extention_no, a.color_id, b.width_dia_type, $select_field"."_concat(distinct(b.po_id)) as po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.id='$data' $grop_cond";
	if($db_type==0)
	{
		$nameArray=sql_select( "select a.id, a.batch_no, a.extention_no, a.color_id, a.process_id, b.width_dia_type, group_concat(distinct(b.po_id)) as po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.id='$data'  group by a.batch_no, a.extention_no" );
	}
	elseif($db_type==2)
	{
		$nameArray=sql_select( "select a.id, a.batch_no, a.extention_no, a.color_id, a.process_id, listagg(b.width_dia_type,',') within group (order by b.width_dia_type) as width_dia_type, listagg(b.po_id,',') within group (order by b.po_id) as po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.id='$data' group by a.id, a.batch_no, a.extention_no, a.color_id, a.process_id" );
	}
	foreach ($nameArray as $row)
	{	
		$order_no=''; $main_process_id=''; $process_name=''; $party_id_array='';
		
		$order_id_hidde=implode(",",array_unique(explode(",",$row[csf("po_id")])));	
		echo "document.getElementById('txt_batch_no').value				= '".$row[csf("batch_no")]."';\n"; 
		echo "document.getElementById('txt_batch_id').value				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_batch_ext_no').value			= '".$row[csf("extention_no")]."';\n"; 
		echo "set_multiselect('txt_process_id','0','1','".$row[csf("process_id")]."','0');\n";
		echo "document.getElementById('order_no_id').value				= '".$order_id_hidde."';\n";
		echo "document.getElementById('txt_color').value				= '".$color_arr[$row[csf("color_id")]]."';\n"; 
		echo "document.getElementById('hidden_color_id').value			= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('hidden_dia_type').value			= '".$row[csf("width_dia_type")]."';\n";
		echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_party_name',1);\n";
		
		//echo "document.getElementById('txt_order_numbers').value		= '".$order_no."';\n";
		//echo "document.getElementById('txt_process_name').value			= '".$process_name."';\n"; 
		//echo "document.getElementById('cbo_party_name').value			= '".$party_id_array."';\n"; 
		//echo "document.getElementById('txt_process_id').value			= '".$row[csf("process_id")]."';\n"; 
		//echo "document.getElementById('process_id').value				= '".$main_process_id."';\n";  
	}
	exit();  
}

if($action=="reject_type_popup")
{
  	echo load_html_head_contents("Reject Info","../../../", 1, 1, $unicode,'','');
  	//echo load_html_head_contents("AOP production", "../../",1, 1,$unicode,1,'');
	$_SESSION['page_permission']=$permission;
	extract($_REQUEST);
	//$data=explode("_",$data);
?>
	<script>
		var permission='<? echo $permission; ?>';
		function fnc_reject_save(operation)
		{
			var row_num=$('#tbl_list_search tbody tr').length*1;
			//alert(row_num);
			var update_mst_id=$('#update_mst_id').val();
			var update_dts_id=$('#update_dts_id').val();

			var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				data_all=data_all+get_submitted_data_string('txtIdividualId_'+i+'*txtRejQty_'+i,"../../../",i);
			} 
			var data="action=save_update_delete_reject&operation="+operation+'&total_row='+row_num+data_all+'&update_mst_id='+update_mst_id+'&update_dts_id='+update_dts_id;//+'&update_id='+update_id
			//alert(data); return;
			freeze_window(operation);
			http.open("POST","aop_delivery_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_reject_save_response;
		}

		function fnc_reject_save_response()
		{
			if(http.readyState == 4) 
			{
			    var reponse=trim(http.responseText).split('**');
				//alert(http.responseText);
				release_freezing();
				if(reponse[0]==0 || reponse[0]==1)
				{
					show_msg(trim(reponse[0]));
					//set_button_status(1, permission, 'fnc_reject_save',1,2);
					set_button_status(1, permission, 'fnc_reject_save',1,1);
					//release_freezing();
				}
			}
		}
    </script>

</head>
<body onLoad="set_hotkey()">
<div align="center">
	<? echo load_freeze_divs ("../../../",$permission);  ?>
	<fieldset style="width:370px;margin-left:10px">
        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                <thead>
                	<tr>
                		<th colspan="3">Before & After AOP Problem list</th>
                	</tr>
                	<tr>
                    	<th width="50">SL</th>
                    	<th width="200">Particular</th>
                    	<th>Qnty.</th>
                	</tr>
                </thead>
            </table>
            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
                	<tbody>
	                <?
	                 	$data=explode("_",$data);
	                    $i=1; 
	                    foreach($aop_qc_reject_type as $id=>$name)
	                    {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>"> 
								<td width="50" align="center"><?php echo "$i"; ?>
									<input type="hidden" name="txtIdividualId[]" id="txtIdividualId_<?php echo $i ?>" value="<? echo $id; ?>"/>
                					<input type="hidden" id="updaterejectid<?php echo $i ?>" name="updaterejectid<?php echo $i ?>" value="">	
								</td>	
								<td width="200"><p><? echo $name; ?></p></td>
								<td><input type="text" name="txtRejQty[]" id="txtRejQty_<?php echo $i ?>" class="text_boxes_numeric" value="" style="width:60px"/></td>
							</tr>
							<?
							$i++;
	                    }
	                	?>
                    </tbody>
                </table>
            </div>
            <table width="350" cellpadding="0" cellspacing="0" id="" rules="all" border="0" class="">
            <tr>
                <td align="center" colspan="3" valign="middle" class="button_container">
                <?
                echo load_submit_buttons($permission, "fnc_reject_save", 0,0,"",2);
					//echo load_submit_buttons( $permission, "fnc_reject_operationnnnnn",0,1,"",2);
				?>
                <input type="hidden" id="update_dts_id" name="update_dts_id" value="<?php echo $data[0] ?>">
                <input type="hidden" id="update_mst_id" name="update_mst_id" value="<?php echo  $data[1] ?>">
            </tr>
        </table>
        </form>
    </fieldset>
</div>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>    
</body>           

<!-- <script>
	set_all();
</script> -->
</html>
<?
exit();
}

if($action=="save_update_delete_reject")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$total_row=str_replace("'","",$total_row);
	$update_mst_id=str_replace("'","",$update_mst_id);
	$update_dts_id=str_replace("'","",$update_dts_id);
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$data_array3="";
		$payhead_id_check=array();
		$field_array3="id,mst_id,dtls_id,reject_type_id,quantity";
		$id = return_next_id("id", "subcon_production_qnty", 1);
		for($i=1;$i<=$total_row;$i++)
		{
			$txt_individual_id="txtIdividualId_".$i;
			$txt_rej_qty="txtRejQty_".$i;

			if(!in_array(str_replace("'","",$$txt_individual_id),$payhead_id_check))
			{
				//echo "20**Duplicate Pay Head Or Charge For is Not Allow in Same Master.";
				$payhead_id_check[]=$$txt_individual_id;
				if(str_replace("'","",$$txt_rej_qty)!='')
				{
					if ($data_array3 != "") $data_array3 .= ",";
					$data_array3 .="(".$id.",'".$update_mst_id."','".$update_dts_id."',".$$txt_individual_id.",".$$txt_rej_qty.")";
					$id=$id+1;
				}
			}
		}
		if($data_array3!="")
		{
			//echo "INSERT INTO subcon_production_qnty (".$field_array3.") VALUES ".$data_array3; die;
			$rID3=sql_insert("subcon_production_qnty",$field_array3,$data_array3,1);
		}
		if($db_type==0)
		{
			if($rID3)
			{
				mysql_query("COMMIT");
				echo "0**".$update_mst_id."**".$update_dts_id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$update_mst_id."**".$update_dts_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID3){
				oci_commit($con);
				echo "0**".$update_mst_id."**".$update_dts_id;
			}
			else{
				oci_rollback($con); 
				echo "10**".$update_mst_id."**".$update_dts_id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		//echo $txt_entry_id;die;
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//$duplicate_sql="select id from com_lc_charge where entry_id=$txt_entry_id and status_active=1 and pay_head_id=".$cboissuebanking_1 ." and change_for=".$cbochargefor_1." and id <>$update_dts_id";
		$duplicate_sql="select id from com_lc_charge where btb_lc_id=$btb_lc_id and entry_id=$txt_entry_id and status_active=1 and pay_head_id=".$cboissuebanking_1 ." and change_for=".$cbochargefor_1." and id <>$update_dts_id";
		
		$duplicate_result=sql_select($duplicate_sql);
		if(count($duplicate_result)>0)
		{
			echo "20**Duplicate Pay Head Or Charge For is Not Allow in Same Master.";
			die;
		}
 		$field_array = "pay_date*pay_head_id*change_for*amount*adjustment_source*updated_by*update_date";
 		$data_array = "'".$txt_pay_date."'*".$cboissuebanking_1."*".$cbochargefor_1."*".$txtamount_1."*".$cboAdjustmentSource_1."*'".$user_id."'*'".$pc_date_time."'";
		
		$prev_invoice_charge_amount=return_field_value("charage_amount","com_import_invoice_mst","id='".$txt_entry_id."'");
		$prev_charge_amount=return_field_value("amount","com_lc_charge","id=".$update_dts_id."");
		
		$field_array_master_update="charage_amount";
		$data_array_master_update=$prev_invoice_charge_amount-$prev_charge_amount+str_replace("'",'',$txtamount_1);
		
		$rID1= sql_update("com_import_invoice_mst",$field_array_master_update,$data_array_master_update,"id",$txt_entry_id,1); 
		//echo $field_array."<br>".$data_array;die;
 		$rID= sql_update("com_lc_charge",$field_array,$data_array,"id",$update_dts_id,1); 
		
		if($db_type==0)
		{
			if($rID & $rID1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_entry_id)."**".str_replace("'",'',$update_dts_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_entry_id)."**".str_replace("'",'',$update_dts_id);
			}
		}
		
		if($db_type==2 || $db_type==1)
		{
			if($rID & $rID1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_entry_id)."**".str_replace("'",'',$update_dts_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_entry_id)."**".str_replace("'",'',$update_dts_id);
			}
		}
		disconnect($con);
		die;
		
	}
	else if ($operation==2)  // Delete Here
	{
		//echo $txt_entry_id;die;
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
	
 		$field_array = "status_active*is_deleted*updated_by*update_date";
 		$data_array = "0*1*'".$user_id."'*'".$pc_date_time."'";
		
		$prev_invoice_charge_amount=return_field_value("charage_amount","com_import_invoice_mst","id='".$txt_entry_id."'");
		$prev_charge_amount=return_field_value("amount","com_lc_charge","id=".$update_dts_id."");
		
		$field_array_master_update="charage_amount";
		$data_array_master_update=$prev_invoice_charge_amount-$prev_charge_amount;
		
		$rID1= sql_update("com_import_invoice_mst",$field_array_master_update,$data_array_master_update,"id",$txt_entry_id,1); 
		//echo $field_array."<br>".$data_array;die;
 		$rID= sql_update("com_lc_charge",$field_array,$data_array,"id",$update_dts_id,1); 
		
		if($db_type==0)
		{
			if($rID & $rID1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$txt_entry_id)."**".str_replace("'",'',$update_dts_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_entry_id)."**".str_replace("'",'',$update_dts_id);
			}
		}
		
		if($db_type==2 || $db_type==1)
		{
			if($rID & $rID1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$txt_entry_id)."**".str_replace("'",'',$update_dts_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$txt_entry_id)."**".str_replace("'",'',$update_dts_id);
			}
		}
		disconnect($con);
		die;
		
	}
}

if($action=="order_qnty_popup")
{
	echo load_html_head_contents("order qnty Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
	<script>
		function fnc_close()
		{
			var tot_row=$('#tbl_qnty tbody tr').length;
			var qnty_qn="";
			var qnty_tot="";
			var qnty_tbl_id="";
			for(var i=1; i<=tot_row; i++)
			{
				if(i*1>1) qnty_qn +=",";
				if(i*1>1) qnty_tbl_id +=",";
				qnty_qn += $("#orderqnty_"+i).val();
				qnty_tbl_id += $("#hiddtblid_"+i).val();
				qnty_tot=qnty_tot*1+$("#orderqnty_"+i).val()*1;
			}
			document.getElementById('hidden_qnty_tot').value=qnty_tot;
			document.getElementById('hidden_qnty').value=qnty_qn;
			document.getElementById('hidd_qnty_tbl_id').value=qnty_tbl_id;
			parent.emailwindow.hide();
		}
	</script>
	<head>
	<body>
        <form name="searchfrm_1"  id="searchfrm_1">
        <div style="margin-left:10px; margin-top:10px" align="center">
            <table class="rpt_table" id="tbl_qnty" border="1" cellpadding="0" cellspacing="0" rules="all" width="400">
                <thead>
                    <th width="150">Order No</th>
                    <th width="150">Production Qty</th>
                </thead>
                <tbody>
					<? 
                    $data=explode('_',$data);
                    if($data[1]=="")
                    {
						$i=1;
						$order_name=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
						//$nameArray=sql_select( "select id,order_no from subcon_ord_dtls where id in ($data[0])");
						$break_order_id=explode(',',$data[0]);
						$break_order_qnty=explode(',',$data[3]);
						for($k=0; $k<count($break_order_id); $k++)
						{ 
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
                   			?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="150">
									<? echo $order_name[$break_order_id[$k]]; ?>
                                </td>
                                <td width="150" align="center">
                                    <input type="text" name="orderqnty_<? echo $i; ?>" id="orderqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:140px;"  value="<? echo $break_order_qnty[$k]; ?>"/>
                                    <input type="hidden" name="hiddtblid_<? echo $i; ?>" id="hiddtblid_<? echo $i; ?>" value="">
                                </td>
                                <input type="hidden" name="hidden_qnty_tot" id="hidden_qnty_tot">
                                <input type="hidden" name="hidden_qnty" id="hidden_qnty">
                                <input type="hidden" name="hidd_qnty_tbl_id" id="hidd_qnty_tbl_id">
                            </tr>       
							<?		
                            $i++;
                        }
					}
					else
					{
						if($data[2]!="")
						{
							$i=1;
							$order_name=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');	
							//$nameArray=sql_select( "select id,order_no from subcon_ord_dtls where id in ($data[0])");
							$break_order_id=explode(',',$data[0]);
							$break_order_qnty=explode(',',$data[3]);
							for($k=0; $k<count($break_order_id); $k++)
							{ 
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                    <td width="150">
										<? echo $order_name[$break_order_id[$k]]; ?>
                                    </td>
                                    <td width="150" align="center">
                                        <input type="text" name="orderqnty_<? echo $i; ?>" id="orderqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:140px;"  value="<? echo $break_order_qnty[$k]; ?>"/>
                                        <input type="hidden" name="hiddtblid_<? echo $i; ?>" id="hiddtblid_<? echo $i; ?>" value="">
                                    </td>
                                    <input type="hidden" name="hidden_qnty_tot" id="hidden_qnty_tot">
                                    <input type="hidden" name="hidden_qnty" id="hidden_qnty">
                                    <input type="hidden" name="hidd_qnty_tbl_id" id="hidd_qnty_tbl_id">
                                </tr>       
                                <?		
                                $i++;
                            }
						}
						else
						{
							$i=1;
							$order_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
							$nameArray=sql_select( "select id,order_id,quantity from subcon_production_qnty where dtls_id='$data[1]' and order_id in ($data[0])");
							foreach($nameArray as $row)
							{ 
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
							?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                    <td width="150">
										<? echo $order_arr[$row[csf('order_id')]]; ?>
                                    </td>
                                    <td width="150" align="center">
                                        <input type="text" name="orderqnty_<? echo $i; ?>" id="orderqnty_<? echo $i; ?>" value="<? echo $row[csf('quantity')]; ?>" class="text_boxes_numeric" style="width:140px;" />
                                        <input type="hidden" name="hiddtblid_<? echo $i; ?>" id="hiddtblid_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
                                    </td>
                                    <input type="hidden" name="hidden_qnty_tot" id="hidden_qnty_tot">
                                    <input type="hidden" name="hidden_qnty" id="hidden_qnty">
                                    <input type="hidden" name="hidd_qnty_tbl_id" id="hidd_qnty_tbl_id">
                                </tr>       
								<?		
                                $i++;
                            }
						}
					}
					?>
                </tbody>
            </table>
            <table width="400">
                <tr>
                    <td align="center" >
                        <input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>
                </tr>
            </table>
        </div>
        </form>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>            
	<?
	exit();
}

/*if ($action=="aop_delevery_list_view")
{
	?>	
	<div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="90" align="center"  style="display: none;"abbr="">Process</th>
                <th width="60" align="center"  style="display: none;">Batch No</th>
                <th width="80" align="center"  style="display: none;">Order No</th>                    
                <th width="150" align="center" >Const. and Compo.</th>
                <th width="70" align="center">Color</th>
                <th width="50" align="center">Gsm</th>
                <th width="60" align="center">Dia/Width</th>  
                <th width="80" align="center">Prod. Qty</th> 
                <th width="50" align="center">Roll</th>                  
                <th width="" align="center">Machine</th>
            </thead>
        </table>
    </div>
    <div style="width:100%;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
        <?php  
			$i=1;
			$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
			$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
			$machine_arr=return_library_array( "select id,machine_no from  lib_machine_name",'id','machine_no');
			$sql ="select id, mst_id, batch_id,production_id, width_dia_type, order_id, product_type, process, fabric_description, cons_comp_id, color_id, gsm, dia_width, product_qnty, reject_qnty, no_of_roll, floor_id, machine_id,shift,uom_id from subcon_production_dtls where status_active=1 and mst_id='$data'"; 
			$sql_result =sql_select($sql);
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				$process_id=explode(',',$row[csf('process')]);
				$process_val='';
				foreach ($process_id as $val)
				{
					if($process_val=='') $process_val=$conversion_cost_head_array[$val]; else $process_val.=",".$conversion_cost_head_array[$val];
				}
					
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'load_php_data_to_form_dtls','requires/aop_delivery_entry_controller');" style="text-decoration:none; cursor:pointer" > 
                    <td width="30" align="center" style="display: none;"><? echo $i; ?></td>
                    <td width="90" align="center"  style="display: none;"><p><? echo $process_val; ?></p></td>
                    <td width="60" align="center"  style="display: none;"><p><? echo $batch_arr[$row[csf('batch_id')]]; ?></p></td>
					<?
                    $ord_id=$row[csf('order_id')];
                    $order_arr=sql_select("select id,order_no from subcon_ord_dtls where id in($ord_id)");
                    $order_num='';  
                    foreach($order_arr as $okey)
                    {
                        if($order_num=="") $order_num=$okey[csf("order_no")]; else $order_num .=",".$okey[csf("order_no")]; 
                    }
                    ?>
                    <td width="80" align="center"  style="display: none;"><p><? echo $order_num; ?></p></td>
                    <td width="150" align="center"><p><? echo $row[csf('fabric_description')]; ?></p></td>
                    <td width="70" align="center"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td width="50" align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
                    <td width="80" align="right"><p><? echo $row[csf('product_qnty')]; ?>&nbsp;</p></td>
                    <td width="50" align="right"><p><? echo $row[csf('no_of_roll')]; ?>&nbsp;</p></td>
                    <td width="" align="center"><p><? echo $machine_arr[$row[csf('machine_id')]]; ?></p></td>
                </tr>
			<?php
            $i++;
        }
        ?>
        </table>
	</div>
	<?
}*/

/*if ($action=="load_php_data_to_form_dtls")
{
	$order_arr=return_library_array("select id,order_no from subcon_ord_dtls",'id','order_no');
	$process_arr=return_library_array("select id,main_process_id from subcon_ord_dtls",'id','main_process_id');
	$color_no_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	
	$batch_array=array();
	$batch_id_sql="select id, batch_no, extention_no from pro_batch_create_mst where entry_form=281 and status_active=1 and is_deleted=0";
	$batch_id_sql_result=sql_select($batch_id_sql);
	foreach ($batch_id_sql_result as $row)
	{
		$batch_array[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_array[$row[csf("id")]]["extention_no"]=$row[csf("extention_no")];
	}
	
	$sql= "select id, batch_id, width_dia_type, order_id, process, fabric_description, cons_comp_id, color_id, gsm, dia_width, product_qnty, reject_qnty, no_of_roll, floor_id, machine_id, start_hour, start_minutes, start_date, end_hour, end_minutes, end_date from subcon_production_dtls where id='$data'";
		
	$nameArray=sql_select($sql);
	foreach ($nameArray as $row)
	{
		$order_id=explode(',',$row[csf("order_id")]);
		$order_no=''; 
		foreach($order_id as $okey)
		{
			if($order_no=="") $order_no=$order_arr[$okey]; else $order_no .=",".$order_arr[$okey];
		}
		//$field_array2="id, mst_id, batch_id,production_id, width_dia_type, order_id, product_type, process, fabric_description, cons_comp_id, color_id, gsm, dia_width, product_qnty, reject_qnty, no_of_roll, floor_id, machine_id,shift,uom_id, inserted_by, insert_date";
		//$data_array2="(".$id1.",".$id.",".$txt_batch_id.",".$txt_qc_id.",".$hidden_dia_type.",".$order_no_id.",'".$process_finishing."','".$txt_process_id."',".$txt_description.",".$comp_id.",".$hidden_color_id.",".$txt_gsm.",".$txt_dia_width.",".$txt_product_qnty.",".$txt_reject_qty.",".$txt_roll_no.",".$cbo_floor_id.",".$cbo_machine_id.",".$cboShift.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
		
		echo "document.getElementById('txt_batch_id').value		 				= '".$row[csf("batch_id")]."';\n";
		echo "set_multiselect('txt_process_id','0','1','".$row[csf("process")]."','0');\n";
		echo "document.getElementById('hidden_dia_type').value		 			= '".$row[csf("width_dia_type")]."';\n";
		
		echo "document.getElementById('order_no_id').value						= '".$row[csf("order_id")]."';\n"; 
		echo "document.getElementById('txt_description').value					= '".$row[csf("fabric_description")]."';\n";
		echo "document.getElementById('txt_color').value		 				= '".$color_no_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('hidden_color_id').value		 			= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_gsm').value		 					= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_dia_width').value		 			= '".$row[csf("dia_width")]."';\n";
		//echo "document.getElementById('txt_delevery_qnty').value            		= '".$row[csf("product_qnty")]."';\n";
		echo "document.getElementById('txt_reject_qty').value            		= '".$row[csf("reject_qnty")]."';\n";
		echo "document.getElementById('txt_roll_no').value            			= '".$row[csf("no_of_roll")]."';\n";
		//echo "document.getElementById('txt_order_numbers').value		 		= '".$order_no."';\n";
		//echo "load_drop_down( 'requires/aop_delivery_entry_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_floor', 'floor_td' );\n";
		//echo "document.getElementById('cbo_floor_id').value		 			= '".$row[csf("floor_id")]."';\n"; 
		//echo "load_drop_down( 'requires/aop_delivery_entry_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_floor_id').value, 'load_drop_machine', 'machine_td');\n";
		//echo "show_list_view(document.getElementById('order_no_id').value+'_'+document.getElementById('process_id').value+'_'+document.getElementById('txt_batch_id').value,'show_fabric_desc_listview','list_fabric_desc_container','requires/aop_delivery_entry_controller','');\n";	
		
		//echo "document.getElementById('cbo_machine_id').value		 			= '".$row[csf("machine_id")]."';\n"; 
		echo "document.getElementById('update_id_dtl').value            		= '".$row[csf("id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'aop_delevery_entry',1);\n";	
	}
	$qry_result=sql_select( "select id, order_id,quantity from subcon_production_qnty where dtls_id='$data'");// and quantity!=0
	$order_qnty=""; $order_id="";
	foreach ($qry_result as $row)
	{
		if($order_qnty=="") $order_qnty=$row[csf("quantity")]; else $order_qnty.=",".$row[csf("quantity")];
		if($order_id=="") $order_id=$row[csf("order_id")]; else $order_id.=",".$row[csf("order_id")];
	}
	echo "document.getElementById('item_order_id').value 	 				= '".$order_id."';\n";
	//echo "document.getElementById('txt_receive_qnty').value 	 			= '".$order_qnty."';\n";
	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'aop_delevery_entry',1);\n";
	exit();	
}*/

$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$process_finishing="0";
	$shipingStatus=str_replace("'","",$cbo_shiping_status);
	
	if ($operation==0)   // Insert Here===================================================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)";
		}
		else if($db_type==2)
		{
			$year_cond=" and TO_CHAR(insert_date,'YYYY')";
		}
		
		$new_return_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '','AOPDE', date("Y",time()), 5, "select id,prefix_no,prefix_no_num from  subcon_production_mst where entry_form=307 and company_id=$cbo_company_id  $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		//print_r($new_return_no); die;
		if(str_replace("'",'',$update_id)=="")
		{
			$field_array="id,entry_form,prefix_no,prefix_no_num,product_no,product_type,company_id,location_id,party_id,product_date,delivery_party,delv_party_location,remarks,within_group,inserted_by,insert_date";
			$id=return_next_id( "id","subcon_production_mst",1); 
			$data_array="(".$id.",307,'".$new_return_no[1]."','".$new_return_no[2]."','".$new_return_no[0]."','".$process_finishing."',".$cbo_company_id.",".$cbo_location_name.",".$cbo_party_name.",".$txt_delivery_date.",".$cbo_delevery_name.",".$cbo_delivery_location.",".$txt_remarks.",".$cbo_within_group.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";  
			$rID=sql_insert("subcon_production_mst",$field_array,$data_array,0);
			$return_no=$new_return_no[0];
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="product_no*location_id*party_id*product_date*delivery_party*delv_party_location*remarks*within_group*updated_by*update_date";
			$data_array="".$txt_delevery_id."*".$cbo_location_name."*".$cbo_party_name."*".$txt_delivery_date."*".$cbo_delevery_name."*".$cbo_delivery_location."*".$txt_remarks."*".$cbo_within_group."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("subcon_production_mst",$field_array,$data_array,"id",$update_id,0); 
			$return_no=str_replace("'",'',$txt_delevery_id);
		}
		
		$id1=return_next_id("id","subcon_production_dtls",1); 
		$txt_process_id=explode(",",str_replace("'","",$txt_process_id));
		asort($txt_process_id);
		$txt_process_id=implode(",",$txt_process_id);
		$delivery_qty_arr=array();
		$delivery_qty_sql=sql_select("SELECT b.product_qnty, b.order_id from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.id!=$id and a.entry_form=307 and b.order_id=$order_no_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0");
		foreach ($delivery_qty_sql as $row)
		{
			$delivery_qty_arr[$row[csf("order_id")]]['product_qnty']+=$row[csf("product_qnty")];
		}
		unset($delivery_qty_sql);
		$del_status=$cbo_shiping_status;

		$cum_delivery_qty=$delivery_qty_arr[str_replace("'","",$order_no_id)]['product_qnty']+str_replace("'","",$txt_delevery_qnty);
		$order_qty=return_field_value("order_quantity","subcon_ord_dtls","id=$order_no_id and status_active=1 and is_deleted=0");
		if(str_replace("'","",$del_status)!=3){
			//if($cum_delivery_qty >= $order_qty) $order_status=3;  else $order_status=2;
			$order_status=2;
		}else{
			$order_status=3;
		}
		//echo '10**'.$cum_delivery_qty.'**'.$order_qty.'**'.$order_status; die;
		$field_array2="id, mst_id, batch_id,production_id, width_dia_type, order_id, product_type, process, fabric_description,fabric_used_qnty, cons_comp_id, color_id, gsm, dia_width, product_qnty, reject_qnty, no_of_roll,uom_id,body_part_id,buyer_po_id,remarks,dyeing_batch_no,delivery_status, inserted_by, insert_date";
		$data_array2="(".$id1.",".$id.",".$txt_batch_id.",".$txt_qc_id.",".$hidden_dia_type.",".$order_no_id.",'".$process_finishing."','".$txt_process_id."',".$txt_description.",".$txt_fabric_qty.",'".$comp_id."',".$hidden_color_id.",".$txt_gsm.",".$txt_dia_width.",".$txt_delevery_qnty.",".$txt_reject_qty.",".$txt_roll_no.",".$cbo_uom.",".$cbo_body_part.",".$txt_buyer_po_id.",".$txt_remarks.",".$txt_dyeing_batch.",".$del_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
		//echo "10**INSERT INTO subcon_production_mst (".$field_array.") VALUES ".$data_array; die;
		$rID2=sql_insert("subcon_production_dtls",$field_array2,$data_array2,0);
		
		$sts_po = execute_query("update subcon_ord_dtls set delivery_status=$order_status where id=$order_no_id",1);
		/*
		if($shipingStatus==2)
		{
			$sts_po = execute_query("update subcon_ord_dtls set delivery_status=$order_status where id=$order_no_id and delivery_status<>3",1);
		}
		else
		{
			$sts_po = execute_query("update subcon_ord_dtls set delivery_status=$del_status where id=$order_no_id and delivery_status<>3",1);
		}*/
		//echo "10**".$rID."**".$rID2."**".$sts_po; die;
		if($db_type==0)
		{
			if($rID && $rID2  && $sts_po)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$txt_qc_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$txt_qc_id);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2  && $sts_po)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$txt_qc_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$txt_qc_id);
			}
		}	
		
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here==============================================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//$process_finishing="4";
		//echo "10**".$update_id_dtl; die;
		$field_array="product_no*company_id*location_id*party_id*product_date*delivery_party*delv_party_location*remarks*within_group*updated_by*update_date";
		$data_array="".$txt_delevery_id."*".$cbo_company_id."*".$cbo_location_name."*".$cbo_party_name."*".$txt_delivery_date."*".$cbo_delevery_name."*".$cbo_delivery_location."*".$txt_remarks."*".$cbo_within_group."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$rID=sql_update("subcon_production_mst",$field_array,$data_array,"id",$update_id,0);
		$txt_process_id=explode(",",str_replace("'","",$txt_process_id));
		asort($txt_process_id);
		$txt_process_id=implode(",",$txt_process_id);

		$delivery_qty_arr=array();
		$delivery_qty_sql=sql_select("SELECT b.product_qnty, b.order_id from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.id!=$update_id and a.entry_form=307 and b.order_id=$order_no_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0");
		foreach ($delivery_qty_sql as $row)
		{
			$delivery_qty_arr[$row[csf("order_id")]]['product_qnty']+=$row[csf("product_qnty")];
		}
		unset($delivery_qty_sql);
		$del_status=$cbo_shiping_status;
		$cum_delivery_qty=$delivery_qty_arr[str_replace("'","",$order_no_id)]['product_qnty']+str_replace("'","",$txt_delevery_qnty);
		$order_qty=return_field_value("order_quantity","subcon_ord_dtls","id=$order_no_id and status_active=1 and is_deleted=0");
		if(str_replace("'","",$del_status)!=3){
			//if($cum_delivery_qty >= $order_qty) $order_status=3;  else $order_status=2;
			$order_status=2;
		}else{
			$order_status=3;
		}
		//if($cum_delivery_qty >= $order_qty) $order_status=3;  else $order_status=2;

		$field_array2="batch_id*production_id*width_dia_type*order_id*process*fabric_description*fabric_used_qnty*cons_comp_id*color_id*gsm*dia_width*product_qnty*reject_qnty*no_of_roll*uom_id*body_part_id*buyer_po_id*remarks*dyeing_batch_no*delivery_status*updated_by*update_date";
		$data_array2="".$txt_batch_id."*".$txt_qc_id."*".$hidden_dia_type."*".$order_no_id."*'".$txt_process_id."'*".$txt_description."*".$txt_fabric_qty."*'".$comp_id."'*".$hidden_color_id."*".$txt_gsm."*".$txt_dia_width."*".$txt_delevery_qnty."*".$txt_reject_qty."*".$txt_roll_no."*".$cbo_uom."*".$cbo_body_part."*".$txt_buyer_po_id."*".$txt_remarks."*".$txt_dyeing_batch."*".$del_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo $data_array2;
		$rID2=sql_update("subcon_production_dtls",$field_array2,$data_array2,"id",$update_id_dtl,0);
		//$sts_po = execute_query("update subcon_ord_dtls set delivery_status=$order_status where id=$order_no_id and delivery_status<>3",1);
		$sts_po = execute_query("update subcon_ord_dtls set delivery_status=$del_status,del_status_change_date='".$pc_date_time."' where id=$order_no_id ",1);
		//10**1**1****1**1021
		
		//echo "10**".$rID."**".$rID2."**". $rID3."**". $sts_po."**".str_replace("'",'',$txt_qc_id); die;
		if($db_type==0)
		{
			if($rID && $rID2 && $sts_po)
			{
				mysql_query("COMMIT");
				//echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_delevery_id)."**".str_replace("'",'',$update_id_dtl);
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_delevery_id)."**".str_replace("'",'',$update_id_dtl)."**".str_replace("'",'',$txt_qc_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_delevery_id)."**".str_replace("'",'',$update_id_dtl)."**".str_replace("'",'',$txt_qc_id);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2 && $sts_po)
			{
				oci_commit($con);
				//echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_delevery_id);
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_delevery_id)."**".str_replace("'",'',$update_id_dtl)."**".str_replace("'",'',$txt_qc_id);
			}
			else
			{
				oci_rollback($con);
				//echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_delevery_id);
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_delevery_id)."**".str_replace("'",'',$update_id_dtl)."**".str_replace("'",'',$txt_qc_id);
			}
		}	
		
		disconnect($con);
 		die;
	}
	else if ($operation==2)   // Delete Here ============================================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		/*$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("subcon_production_dtls",$field_array,$data_array,"id","".$update_id_dtl."",1);
			
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_delevery_id)."**".str_replace("'",'',$update_id_dtl);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_delevery_id)."**".str_replace("'",'',$update_id_dtl);
			}
		}
		disconnect($con);
		die;*/
	}
}

function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}

	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	echo "10**$strQuery"; die;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}

if ($action=="delv_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$ex_data=explode("_",$data);
	?>
	<script>
		function js_set_value(info)
		{ 
			document.getElementById('delivery_id').value=info;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
        <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
        <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>                	 
                <th width="150">Company Name</th>
                <th width="100">Within Group</th>
                <th width="120">Delv. ID</th>
                <th width="120">AOP Ref.</th>
                <th width="200">Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
            </thead>
            <tbody>
                <tr>
                    <td> <input type="hidden" id="delivery_id">
						<?   
							echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"",1);
                        ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", $ex_data[2], "",1 ); ?>
                    </td>
                    <td>
                        <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:105px" />
                    </td>
                    <td>
                        <input type="text" name="txt_aop_ref" id="txt_aop_ref" class="text_boxes" style="width:107px" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px">
                    </td> 
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_aop_ref').value+'_'+document.getElementById('cbo_within_group').value+'_'+<? echo $ex_data[1] ?>, 'delv_id_search_list_view', 'search_div', 'aop_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center" height="40" valign="middle">
						<? echo load_month_buttons(1);  ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center" valign="top" id=""><div id="search_div"></div></td>
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

if ($action=="delv_id_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[5]!=0) $within_group=" and a.within_group='$data[5]'"; else { echo $within_group=''; }
	if ($data[6]!=0) $party_cond=" and a.party_id='$data[6]'"; else { echo $party_cond=''; }
	
	if($db_type==0)
	{ 
		if ($data[1]!="" &&  $data[2]!="") $production_date_cond = "and a.product_date between '".change_date_format($data[1],'yyyy-mm-dd')."' and '".change_date_format($data[2],'yyyy-mm-dd')."'"; else $production_date_cond ="";
	}
	else
	{
		if ($data[1]!="" &&  $data[2]!="") $production_date_cond = "and a.product_date between '".change_date_format($data[1], "", "",1)."' and '".change_date_format($data[2], "", "",1)."'"; else $production_date_cond ="";
	}
	
	if ($data[3]!='') $product_id_cond=" and a.prefix_no_num='$data[3]'"; else $product_id_cond="";
	//if ($data[4]!=0) $buyer_cond=" and a.party_id='$data[4]'"; else $buyer_cond="";
	if ($data[4]!='') $aop_ref_cond= " and a.aop_reference like '%$data[4]%'"; else $aop_ref_cond="";	
	if($aop_ref_cond!='')
	{
		$ord_sql = "select b.id,a.subcon_job,a.aop_reference,b.order_no from subcon_ord_mst a ,subcon_ord_dtls b where  company_id =$data[0] $aop_ref_cond and a.entry_form=278 and  a.subcon_job=b.job_no_mst";
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array();
		foreach ($ordArray as $row)
		{
			$po_arr[$row[csf('id')]] = $row[csf('order_no')];
			$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
			$po_id[] .= $row[csf("id")];
		}
		$po_id_cond=" and b.order_id in ('".implode("','",$po_id)."') ";
	} 
	else
	{
		$ord_sql = "select b.id,a.subcon_job,a.aop_reference,b.order_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$data[0] and a.entry_form=278 and  a.subcon_job=b.job_no_mst";
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array();
		foreach ($ordArray as $row)
		{
			$po_arr[$row[csf('id')]] = $row[csf('order_no')];
			$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
		}
		//$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
		$po_id_cond='';
	}
	//$return_to=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$batch_array=array();
	$batch_id_sql="select id,within_group,company_id, batch_no, extention_no from pro_batch_create_mst where entry_form=281 and status_active=1 and is_deleted=0";
	$batch_id_sql_result=sql_select($batch_id_sql);
	foreach ($batch_id_sql_result as $row)
	{
		$batch_array[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_array[$row[csf("id")]]["extention_no"]=$row[csf("extention_no")];
		$batch_array[$row[csf("id")]]["within_group"]=$row[csf("within_group")];
		$batch_array[$row[csf("id")]]["company_id"]=$row[csf("company_id")];
	}
	//var_dump($batch_array);
	//$arr=array (2=>$receive_basis_arr,3=>$return_to);
	
	if($db_type==0) 
	{
		$year_cond= "year(a.insert_date)as year";
		$batch_cond="group_concat(b.batch_id) as batch_id";
		$order_cond="group_concat(b.order_id) as order_id";
		$qc_cond="group_concat(b.production_id) as qc_id";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
		$batch_cond="listagg((cast(b.batch_id as varchar2(4000))),',') within group (order by b.batch_id) as batch_id";
		$order_cond="listagg((cast(b.order_id as varchar2(4000))),',') within group (order by b.order_id) as order_id";
		$qc_cond="listagg(b.production_id,',') within group (order by b.production_id) as qc_id";
	}

	$sql= "select a.id, product_no, a.prefix_no_num, $year_cond, a.party_id, a.product_date, a.prod_chalan_no, a.within_group, $batch_cond,$order_cond , $qc_cond, sum(b.product_qnty) as product_qnty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=307 and a.status_active=1 $company_name $party_cond $within_group $po_id_cond  $production_date_cond $product_id_cond group by a.id, product_no, a.prefix_no_num, a.insert_date, a.party_id, a.product_date, a.prod_chalan_no, a.within_group order by a.id DESC";
	
	//echo  create_list_view("list_view", "Prod. ID,Year,Basis,Party,Prod. Date,Product Challan", "80,80,120,120,70,120","750","250",0, $sql , "js_set_value", "id", "", 1, "0,0,basis,party_id,0,0", $arr , "prefix_no_num,year,basis,party_id,product_date,prod_chalan_no", "aop_production_controller","",'0,0,0,0,3,0');
    ?>
    <div>
    <script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_po_list',-1);
        });

	</script>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table">
            <thead>
                <th width="30" >SL</th>
                <th width="120" >Delv. ID</th>
                <th width="50" >Year</th>
                <th width="60" >Delv. Date</th>
                <th width="150" >Batch</th>
                <th width="100" >AOP Ref.</th>
                <th>Delv. Qty</th>
            </thead>
     	</table>
     </div>
     <div style="width:650px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="630" class="rpt_table" id="tbl_po_list">
			<?
			$result_sql= sql_select($sql);
			$i=1;
			//print_r($result_sql);
            foreach($result_sql as $row )
            {

                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$batch_no=""; 
				$batch_id=array_unique(explode(",",$row[csf("batch_id")]));
				foreach($batch_id as $key)
				{
					if($batch_no=="") $batch_no=$batch_array[$key]['batch_no']; else $batch_no.=", ".$batch_array[$key]['batch_no'];
				}
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				//$batch_id=array_unique(explode(",",$row[csf("batch_id")])); 
				$aop_ref=''; //$batch_no="";
				foreach($order_id as $val)
				{
					//echo $aop_ref."=";
					if($aop_ref=="") $aop_ref=$ref_arr[$val]; else $aop_ref.=",".$ref_arr[$val];
				}
				$aop_ref=implode(",",array_unique(explode(",",$aop_ref)));
				$data = $row[csf("id")] . "_" .$row[csf("within_group")]. "_" . $batch_array[$key]['company_id']. "_" .$row[csf("qc_id")]."";

				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $data;?>');" > 
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="120" align="center"><? echo $row[csf("product_no")]; ?></td>
                        <td width="50" align="center"><? echo $row[csf("year")]; ?></td>		
						<td width="60"><? echo change_date_format($row[csf("product_date")]); ?></td>
						<td width="150"><p><? echo $batch_no; ?></p></td>
						<td width="100"><p><? echo $aop_ref; ?></p></td>
                        <td align="right"><? echo number_format($row[csf("product_qnty")],2,'.',''); ?></td>
					</tr>
				<? 
				$i++;
            }
   		?>
			</table>
		</div> 
    <?
	exit();
}

if ($action=="load_delv_data_to_form_mst")
{
	$data=explode("_",$data);
	$sql="select id,entry_form,prefix_no,prefix_no_num,product_no,product_type,company_id,location_id,party_id,product_date,delivery_party,delv_party_location,remarks from subcon_production_mst where entry_form=307 and id='$data[0]'";
	$nameArray=sql_select( $sql ); 

	foreach ($nameArray as $row)
	{	
		$delivery_party=$row[csf("delivery_party")];

		echo "document.getElementById('txt_delevery_id').value 				= '".$row[csf("product_no")]."';\n";
		//echo "document.getElementById('cbo_receive_basis').value			= '".$row[csf("basis")]."';\n"; 
		echo "document.getElementById('txt_delivery_date').value			= '".change_date_format($row[csf("product_date")])."';\n"; 
		
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/aop_delivery_entry_controller', $data[2]+'_'+$data[1], 'load_drop_down_buyer', 'buyer_td' );";
		echo "document.getElementById('cbo_party_name').value				= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('cbo_delevery_name').value			= '".$row[csf("delivery_party")]."';\n";
		
		echo "load_drop_down( 'requires/aop_delivery_entry_controller', $delivery_party, 'load_drop_down_delv_location', 'delv_location_td' );\n";
		echo "document.getElementById('cbo_delivery_location').value		= '".$row[csf("delv_party_location")]."';\n";
		echo "document.getElementById('txt_remarks').value					= '".$row[csf("remarks")]."';\n";

	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
		echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_party_name*cbo_within_group*cbo_party_name',1);\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'aop_delevery_entry',1);\n";
	}
	exit();	
}

if ($action=="fabric_finishing_list_view")
{
	$data=explode('_',$data);
	?>	
	<div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60" align="center">Batch No</th>
                <th width="80" align="center">Order No</th>                    
                <th width="150" align="center">Const. and Compo.</th>
                <th width="70" align="center">Color</th>
                <th width="50" align="center">Gsm</th>
                <th width="60" align="center">Dia/Width</th>  
                <th width="80" align="center">Delv. Qty</th> 
                <th width="80" align="center">Fabric Used Qty</th> 
                <th width="50" align="center">Roll</th>
                <th width="50" align="center">Remarks</th>                   
            </thead>
        </table>
    </div>
    <div style="width:100%;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
        <?php  
			$i=1;
			$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
			//$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
			$batch_id_sql="select id,within_group,company_id, batch_no, extention_no from pro_batch_create_mst where entry_form=281 and status_active=1 and is_deleted=0";
			$batch_id_sql_result=sql_select($batch_id_sql);
			foreach ($batch_id_sql_result as $row)
			{
				$batch_array[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
				$batch_array[$row[csf("id")]]["extention_no"]=$row[csf("extention_no")];
				$batch_array[$row[csf("id")]]["within_group"]=$row[csf("within_group")];
				$batch_array[$row[csf("id")]]["company_id"]=$row[csf("company_id")];
			}
			//$machine_arr=return_library_array( "select id,machine_no from  lib_machine_name",'id','machine_no');
			$sql ="select id, batch_id, order_id, process, fabric_description, color_id, gsm, dia_width, no_of_roll, product_qnty, machine_id,fabric_used_qnty,remarks from subcon_production_dtls where status_active=1 and mst_id='$data[0]'"; 
			$sql_result =sql_select($sql);
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				$process_id=explode(',',$row[csf('process')]);
				$process_val='';
				foreach ($process_id as $val)
				{
					if($process_val=='') $process_val=$conversion_cost_head_array[$val]; else $process_val.=",".$conversion_cost_head_array[$val];
				}
				$click_data=$row[csf('id')]."_".$batch_array[$row[csf("batch_id")]]["within_group"]."_".$batch_array[$row[csf("batch_id")]]["company_id"];
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="get_php_form_data('<? echo $click_data ?>','load_php_data_to_form_dtls','requires/aop_delivery_entry_controller');" style="text-decoration:none; cursor:pointer" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="90" align="center" style="display: none;"><p><? echo $process_val; ?></p></td>
                    <td width="60" align="center"><p><? echo $batch_array[$row[csf("batch_id")]]["batch_no"]; ?></p></td>
					<?
                    $ord_id=$row[csf('order_id')];
                    $order_arr=sql_select("select id,order_no from subcon_ord_dtls where id in($ord_id)");
                    $order_num='';  
                    foreach($order_arr as $okey)
                    {
                        if($order_num=="") $order_num=$okey[csf("order_no")]; else $order_num .=",".$okey[csf("order_no")]; 
                    }
                    ?>
                    <td width="80" align="center"><p><? echo $order_num; ?></p></td>
                    <td width="150" align="center"><p><? echo $row[csf('fabric_description')]; ?></p></td>
                    <td width="70" align="center"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td width="50" align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
                    <td width="80" align="right"><p><? echo $row[csf('product_qnty')]; ?>&nbsp;</p></td>
                    <td width="80" align="right"><p><? echo $row[csf('fabric_used_qnty')]; ?>&nbsp;</p></td>
                    <td width="50" align="right"><p><? echo $row[csf('no_of_roll')]; ?>&nbsp;</p></td>
                    <td width="50" align="right"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
                </tr>
			<?php
            $i++;
        }
        ?>
        </table>
	</div>
	<?	
}

if ($action=="load_php_data_to_form_dtls")
{	
	$data=explode('_',$data);
	$order_arr=return_library_array("select id,order_no from subcon_ord_dtls",'id','order_no');
	$process_arr=return_library_array("select id,main_process_id from subcon_ord_dtls",'id','main_process_id');
	$color_no_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	
	$production_qty_array=array();
	$prod_sql="  SELECT b.production_id,b.batch_id, b.order_id, SUM (b.product_qnty) AS product_qnty FROM subcon_production_dtls b, subcon_production_mst a WHERE a.id = b.mst_id AND b.status_active = 1 AND b.is_deleted = 0 AND a.entry_form = 307 GROUP BY b.production_id ,b.batch_id, b.order_id";
	$prod_data_sql=sql_select($prod_sql);
	foreach($prod_data_sql as $row)
	{
		$production_qty_array[$row[csf('production_id')]][$row[csf('batch_id')]][$row[csf('order_id')]]=$row[csf('product_qnty')];
	}

	$batch_array=array();
	$batch_id_sql="select a.id,a.within_group , a.batch_no, a.extention_no,b.body_part_id from pro_batch_create_mst a , pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=281 and a.status_active=1 and a.is_deleted=0";
	$batch_id_sql_result=sql_select($batch_id_sql);
	foreach ($batch_id_sql_result as $row)
	{
		$batch_array[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_array[$row[csf("id")]]["extention_no"]=$row[csf("extention_no")];
		$batch_array[$row[csf("id")]]["within_group"]=$row[csf("within_group")];
		$batch_array[$row[csf("id")]]["within_group"]=$row[csf("within_group")];
	}
	
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number,a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);
	$sql= "select id, batch_id,production_id, width_dia_type, order_id, process, fabric_description, cons_comp_id, color_id, gsm, dia_width, product_qnty, reject_qnty, no_of_roll, floor_id, machine_id, start_hour, start_minutes, start_date, end_hour, end_minutes, end_date,buyer_po_id,dyeing_batch_no,shift,uom_id,body_part_id ,fabric_used_qnty, remarks, delivery_status from subcon_production_dtls where id='$data[0]'";			
 	$nameArray=sql_select($sql);
	foreach ($nameArray as $row)
	{
		$order_id=explode(',',$row[csf("order_id")]);
		$order_no=''; 
		foreach($order_id as $okey)
		{
			if($order_no=="") $order_no=$order_arr[$okey]; else $order_no .=",".$order_arr[$okey];
		}
		$balance=$row[csf('product_qnty')]+$production_qty_array[$row[csf('id')]][$row[csf('batch_id')]][$row[csf('order_id')]];

		$qc_id=$row[csf("production_id")];
		$po=$buyer_po_arr[$row[csf('buyer_po_id')]]['po'];
		$style_ref_no=$buyer_po_arr[$row[csf('buyer_po_id')]]['style'];
		$qc_qnty=return_field_value("product_qnty","subcon_production_dtls","mst_id=$qc_id");
		
		echo "document.getElementById('txt_qc_id').value		 				= '".$row[csf("production_id")]."';\n";
		echo "document.getElementById('txt_batch_id').value		 				= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_order_no').value						= '".$order_no."';\n";
		echo "document.getElementById('txt_order_id').value						= '".$row[csf("order_id")]."';\n";
		echo "set_multiselect('txt_process_id','0','1','".$row[csf("process")]."','0');\n";
		echo "document.getElementById('hidden_dia_type').value		 			= '".$row[csf("width_dia_type")]."';\n";

		echo "document.getElementById('order_no_id').value						= '".$row[csf("order_id")]."';\n"; 
		echo "document.getElementById('cbo_uom').value							= '".$row[csf("uom_id")]."';\n"; 
		echo "document.getElementById('txt_description').value					= '".$row[csf("fabric_description")]."';\n";
		echo "document.getElementById('txt_color').value		 				= '".$color_no_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('hidden_color_id').value		 			= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_gsm').value		 					= '".$row[csf("gsm")]."';\n";
		//echo "document.getElementById('txt_dia_width').value		 			= '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_dia_width').value		 			= '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_delevery_qnty').value            	= '".$row[csf("product_qnty")]."';\n";
		echo "document.getElementById('txt_fabric_qty').value            	= '".$row[csf("fabric_used_qnty")]."';\n";
		echo "document.getElementById('txt_qc_qnty').value            			= '".$qc_qnty."';\n";
		echo "document.getElementById('txt_reject_qty').value            		= '".$row[csf("reject_qnty")]."';\n";
		echo "document.getElementById('txt_roll_no').value            			= '".$row[csf("no_of_roll")]."';\n";
		//echo "document.getElementById('cboShift').value            				= '".$row[csf("shift")]."';\n";
		echo "document.getElementById('txt_buyer_po').value            			= '".$po."';\n";
		echo "document.getElementById('txt_buyer_style').value            		= '".$style_ref_no."';\n";
		echo "document.getElementById('txt_buyer_po_id').value            		= '".$row[csf("buyer_po_id")]."';\n";
		echo "document.getElementById('cbo_body_part').value            		= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('txt_remarks').value            			= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_dyeing_batch').value            		= '".$row[csf("dyeing_batch_no")]."';\n";
		echo "document.getElementById('txt_balance').value            			= '".$balance."';\n";
		//echo "document.getElementById('cbo_floor_name').value		 			= '".$row[csf("floor_id")]."';\n"; 
		echo "document.getElementById('update_id_dtl').value            		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_shiping_status').value            	= '".$row[csf("delivery_status")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'aop_delevery_entry',1);\n";	
	}
	
	
	/*$qry_result=sql_select( "select id, order_id,quantity from subcon_production_qnty where dtls_id='$data'");// and quantity!=0
	$order_qnty=""; $order_id="";
	foreach ($qry_result as $row)
	{
		if($order_qnty=="") $order_qnty=$row[csf("quantity")]; else $order_qnty.=",".$row[csf("quantity")];
		if($order_id=="") $order_id=$row[csf("order_id")]; else $order_id.=",".$row[csf("order_id")];
	}
	echo "document.getElementById('item_order_id').value 	 				= '".$order_id."';\n";
	
	//echo "document.getElementById('txt_receive_qnty').value 	 			= '".$order_qnty."';\n";
	
	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'aop_delevery_entry',1);\n";*/
	exit();	
}
if ($action=="aop_delevery_entry_print_Challan")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	//$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_arrs=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
	if($data[3]==2)
	{
		$buyer_arr=$buyer_arrs;
	}
	else
	{
		$buyer_arr=$company_library;
	}
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		//$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		//$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyerBuyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['internalRef']=$row[csf("grouping")];
		//$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);
	//print_r($buyer_po_arr);
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	//$machineArr=return_library_array( "select id, machine_no from  lib_machine_name", "id", "machine_no"  );
	
	$sql=" select id, product_no, basis, location_id, party_id, product_date, prod_chalan_no, remarks,delivery_party,delv_party_location from subcon_production_mst where product_no='$data[1]'";
	$dataArray=sql_select($sql);


?>
<div style="width:1430px;">
    <table width="1430" cellspacing="0" align="right" border="0">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="6" align="center">
				<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?> 
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?> 
						Block No: <? echo $result[csf('block_no')];?> 
						City No: <? echo $result[csf('city')];?> 
						Zip Code: <? echo $result[csf('zip_code')]; ?> 
						Province No: <?php echo $result[csf('province')];?> 
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];?> <br>
                        <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
					}
                ?> 
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Challan </u></strong></center></td>
        </tr>
        <tr>
            <td width="160"><strong>Delivery ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('product_no')]; ?></td>
         	 <td width="125"><strong>Party Name:</strong></td><td width="175px"><?   if($data[3]==2)
	{ echo $buyer_arr[$dataArray[0][csf('party_id')]]." , ".$party_loc_arr[$dataArray[0][csf('party_id')]];}else { echo $buyer_arr[$dataArray[0][csf('party_id')]];}//if($data[3]==1){ echo $party_loc_arr[$dataArray[0][csf('party_id')]];}else {echo $buyer_arr[$dataArray[0][csf('party_id')]];} ?></td>
        </tr>
        <tr>
            <td><strong>Delivery Date:</strong></td> <td width="175px"><? echo change_date_format($dataArray[0][csf('product_date')]); ?></td>
            <td><strong>Challan No :</strong></td><td colspan="3"><? echo $dataArray[0][csf('prod_chalan_no')];// ?></td>
        </tr>
        <tr> 
            <td><strong>Delivery Company</strong></td> <td width="175px"><? echo $company_library[$dataArray[0][csf('delivery_party')]]; ?></td>
            <td><strong>Delivery Comp. Location</strong></td><td colspan="3"><? echo $location_arr[$dataArray[0][csf('delv_party_location')]];// ?></td>
        </tr>
    </table>
         <br>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="1430"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="70" align="center">Batch No</th>
				<th width="100" align="center">Internal Ref. No.</th>
				<th width="100" align="center">Dyeing Batch No.</th>
				<th width="100" align="center">AOP Ref.</th>
				<th width="120" align="center">Cust. Buyer</th>			
                <th width="120" align="center">Order No</th>
                <th width="150" align="center">Process</th>
                <th width="160" align="center">Const. Compo.</th>
                <th width="60" align="center">Color</th> 
                <th width="60" align="center">GSM</th>
                <th width="90" align="center">Dia/Width</th>
                <th width="60" align="center">Roll</th>
                <th width="60" align="center">Fabric Used Qty</th>
                <th width="80" align="center">Delivery  Qty</th>
				<th width="60" align="center">Reject Qty</th>
                <th width="" align="center">Remarks</th>                   
            </thead>
   <?
	$mst_id=$dataArray[0][csf('id')];
    $i=1;
	//$poArr=return_library_array( "select id, order_no from subcon_ord_dtls", "id", "order_no");
	$ord_sql = "select b.id,a.subcon_job,a.aop_reference,b.order_no,buyer_buyer from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$data[0] and a.entry_form=278 and  a.subcon_job=b.job_no_mst";
	$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array();$buyer_buyer_arr=array();
	foreach ($ordArray as $row)
	{
		$po_arr[$row[csf('id')]] = $row[csf('order_no')];
		$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
		$buyer_buyer_arr[$row[csf('id')]] = $row[csf('buyer_buyer')];
	}
	$batchArr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");

	$sqldtls=" select a.id, a.batch_id, a.order_id, a.process, a.fabric_description, a.color_id, a.gsm, a.dia_width, a.product_qnty, a.machine_id, a.no_of_roll, a.buyer_po_id,a.dyeing_batch_no,a.reject_qnty,a.fabric_used_qnty,a.remarks from  subcon_production_dtls a where a.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 order by a.id ASC";
	
	$sql_result=sql_select($sqldtls);
	foreach($sql_result as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 $aopRef=''; $internalRef='';	$buyerBuyer='';	
		if($internalRef=='') $internalRef=$buyer_po_arr[$row[csf('buyer_po_id')]]['internalRef']; else $internalRef.=", ".$buyer_po_arr[$row[csf('buyer_po_id')]]['internalRef'];
		if($buyerBuyer=='') $buyerBuyer=$buyer_po_arr[$row[csf('buyer_po_id')]]['buyerBuyer']; else $buyerBuyer.=", ".$buyer_po_arr[$row[csf('buyer_po_id')]]['buyerBuyer'];
			
		$order_id=explode(",",$row[csf('order_id')]);
		$process=explode(",",$row[csf('process')]);
		$po_no='';  $process_arr=''; $buyerbuyerref='';
		//$data=explode('*',$data);
		
		foreach($order_id as $val)
		{
			if($po_no=='') $po_no=$po_arr[$row[csf('order_id')]]; else $po_no.=", ".$po_arr[$row[csf('order_id')]];
			if($aopRef=='') $aopRef=$ref_arr[$row[csf('order_id')]]; else $aopRef.=", ".$ref_arr[$row[csf('order_id')]];
			if($buyerbuyerref=='') $buyerbuyerref=$buyer_buyer_arr[$row[csf('order_id')]]; else $buyerbuyerref.=", ".$buyer_buyer_arr[$row[csf('order_id')]];

		}
		$po_no=implode(",",array_unique(explode(",",$po_no)));
		//$buyerbuyerref=implode(",",array_unique(explode(",",$buyerbuyerref)));
		
		foreach($process as $val)

		{
			if($process_arr=='') $process_arr=$conversion_cost_head_array[$val]; else $process_arr.=", ".$conversion_cost_head_array[$val];
		}
		$internalRef=$buyer_po_arr[$row[csf('buyer_po_id')]]['internalRef'];
		$buyerBuyer=$buyer_po_arr[$row[csf('buyer_po_id')]]['buyerBuyer'];
		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
            <td width="30"><? echo $i; ?></td>
            <td width="70"><p><? echo $batchArr[$row[csf('batch_id')]]; ?></p></td>
			<td width="100" id="ref_td"> <? echo $internalRef; ?></td>
			<td width="100"><p><? echo $row[csf('dyeing_batch_no')]; ?></p></td>
            <td width="100"><p><? echo $aopRef; ?></p></td>
			<td width="120" id="buyer_td"> <?  if($data[3]==1){
			echo $buyer_arrs[$buyerBuyer];}else{ echo $buyerbuyerref;} //echo $buyerbuyerref;//$buyer_arrs[$buyerBuyer]; ?></td>
            <td width="120"><p><? echo $po_no; ?></p></td>
            <td width="150"><p><? echo $process_arr; ?></p></td>
            <td width="160"><p><? echo $row[csf('fabric_description')]; ?></p></td>
            <td width="60"><p><? echo $color_name_arr[$row[csf('color_id')]]; ?></p></td>
            <td width="60"><p><? echo $row[csf('gsm')]; ?></p></td>
            <td width="90"><p><? echo $row[csf('dia_width')]; ?></p></td>
            <td width="60" align="right"><? echo number_format($row[csf('no_of_roll')],2,'.',''); $total_roll+=$row[csf('no_of_roll')]; ?></td>
			
            <td width="80" align="right"><? echo number_format($row[csf('fabric_used_qnty')],2,'.',''); $total_fabric_used_qnty+=$row[csf('fabric_used_qnty')]; ?></td>
			 <td width="80" align="right"><? echo number_format($row[csf('product_qnty')],2,'.',''); $total_qty+=$row[csf('product_qnty')]; ?></td>
			 <td width="80" align="right"><? echo number_format($row[csf('reject_qnty')],2,'.',''); $total_reject_qnty +=$row[csf('reject_qnty')]; ?></td>
            <td width=""><p><? echo $row[csf('remarks')]; //$dataArray[0][csf('remarks')]; ?></p></td>
		</tr>
		<?php
			$uom_unit="Kg";
			$uom_gm="Grams";
	$i++;
	}
	$internalRef=implode(",",array_unique(explode(",",$internalRef)));
	$buyerBuyer=implode(",",array_unique(explode(",",$buyerBuyer)));
	?>
    	<tr> 
            <td align="right" colspan="12" >Total</td>
            <td align="right"><? echo number_format($total_roll,2,'.',''); ?></td>
           <td align="right"><? echo number_format($total_fabric_used_qnty,2,'.',''); ?></td>
            <td align="right"><? echo number_format($total_qty,2,'.',''); ?></td>
             <td align="right"><? echo number_format($total_reject_qnty,2,'.',''); ?></td>
		</tr>
	</table>
    <br>
	 <?
        echo signature_table(173, $data[0], "1430px");
     ?>
     <script type="text/javascript">
     	//document.getElementById("ref_td").innerHTML='<? echo $internalRef; ?>'
     	//document.getElementById("buyer_td").innerHTML='<? echo $buyer_arrs[$buyerBuyer]; ?>'
     </script>
</div>
</div>
<?
exit();
}
if ($action=="aop_delevery_entry_print_Challan1111111111111")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	//$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_arrs=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$party_loc_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
	if($data[3]==2)
	{
		$buyer_arr=$buyer_arrs;
	}
	else
	{
		$buyer_arr=$company_library;
	}
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		//$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		//$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyerBuyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['internalRef']=$row[csf("grouping")];
		//$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);
	//print_r($buyer_po_arr);
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	//$machineArr=return_library_array( "select id, machine_no from  lib_machine_name", "id", "machine_no"  );
	
	$sql=" select id, product_no, basis, location_id, party_id, product_date, prod_chalan_no, remarks,delivery_party,delv_party_location from subcon_production_mst where product_no='$data[1]'";
	$dataArray=sql_select($sql);


?>
<div style="width:1430px;">
    <table width="1430" cellspacing="0" align="right" border="0">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="6" align="center">
				<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?> 
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?> 
						Block No: <? echo $result[csf('block_no')];?> 
						City No: <? echo $result[csf('city')];?> 
						Zip Code: <? echo $result[csf('zip_code')]; ?> 
						Province No: <?php echo $result[csf('province')];?> 
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];?> <br>
                        <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
					}
                ?> 
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Challan </u></strong></center></td>
        </tr>
        <tr>
            <td width="160"><strong>Delivery ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('product_no')]; ?></td>
         	 <td width="125"><strong>Party Name:</strong></td><td width="175px"><?   if($data[3]==2)
	{ echo $buyer_arr[$dataArray[0][csf('party_id')]]." , ".$party_loc_arr[$dataArray[0][csf('party_id')]];}else { echo $buyer_arr[$dataArray[0][csf('party_id')]];}//if($data[3]==1){ echo $party_loc_arr[$dataArray[0][csf('party_id')]];}else {echo $buyer_arr[$dataArray[0][csf('party_id')]];} ?></td>
        </tr>
        <tr>
            <td><strong>Delivery Date:</strong></td> <td width="175px"><? echo change_date_format($dataArray[0][csf('product_date')]); ?></td>
            <td><strong>Challan No :</strong></td><td colspan="3"><? echo $dataArray[0][csf('prod_chalan_no')];// ?></td>
        </tr>
        <tr> 
            <td><strong>Delivery Company</strong></td> <td width="175px"><? echo $company_library[$dataArray[0][csf('delivery_party')]]; ?></td>
            <td><strong>Delivery Comp. Location</strong></td><td colspan="3"><? echo $location_arr[$dataArray[0][csf('delv_party_location')]];// ?></td>
        </tr>
    </table>
         <br>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="1430"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="70" align="center">Batch No</th>
				<th width="100" align="center">Internal Ref. No.</th>
				<th width="100" align="center">Dyeing Batch No.</th>
				<th width="100" align="center">AOP Ref.</th>
				<th width="120" align="center">Cust. Buyer</th>			
                <th width="120" align="center">Order No</th>
                <th width="150" align="center">Process</th>
                <th width="160" align="center">Const. Compo.</th>
                <th width="60" align="center">Color</th> 
                <th width="60" align="center">GSM</th>
                <th width="90" align="center">Dia/Width</th>
                <th width="60" align="center">Roll</th>
                <th width="60" align="center">Fabric Used Qty</th>
                <th width="80" align="center">Delivery  Qty</th>
				<th width="60" align="center">Reject Qty</th>
                <th width="" align="center">Remarks</th>                   
            </thead>
   <?
	$mst_id=$dataArray[0][csf('id')];
    $i=1;
	//$poArr=return_library_array( "select id, order_no from subcon_ord_dtls", "id", "order_no");
	$ord_sql = "select b.id,a.subcon_job,a.aop_reference,b.order_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$data[0] and a.entry_form=278 and  a.subcon_job=b.job_no_mst";
	$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array();
	foreach ($ordArray as $row)
	{
		$po_arr[$row[csf('id')]] = $row[csf('order_no')];
		$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
	}
	$batchArr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");

	$sqldtls=" select a.id, a.batch_id, a.order_id, a.process, a.fabric_description, a.color_id, a.gsm, a.dia_width, a.product_qnty, a.machine_id, a.no_of_roll, a.buyer_po_id,a.dyeing_batch_no,a.reject_qnty,a.fabric_used_qnty,a.remarks from  subcon_production_dtls a where a.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 order by a.id ASC";
	
	$sql_result=sql_select($sqldtls);
	foreach($sql_result as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 $aopRef=''; $internalRef='';	$buyerBuyer='';	
		//if($internalRef=='') $internalRef=$buyer_po_arr[$row[csf('buyer_po_id')]]['internalRef']; else $internalRef.=", ".$buyer_po_arr[$row[csf('buyer_po_id')]]['internalRef'];
		//if($buyerBuyer=='') $buyerBuyer=$buyer_po_arr[$row[csf('buyer_po_id')]]['buyerBuyer']; else $buyerBuyer.=", ".$buyer_po_arr[$row[csf('buyer_po_id')]]['buyerBuyer'];
			
		$order_id=explode(",",$row[csf('order_id')]);
		$process=explode(",",$row[csf('process')]);
		$po_no='';  $process_arr='';
		//$data=explode('*',$data);
		
		foreach($order_id as $val)
		{
			if($po_no=='') $po_no=$po_arr[$row[csf('order_id')]]; else $po_no.=", ".$po_arr[$row[csf('order_id')]];
			if($aopRef=='') $aopRef=$ref_arr[$row[csf('order_id')]]; else $aopRef.=", ".$ref_arr[$row[csf('order_id')]];

		}
		$po_no=implode(",",array_unique(explode(",",$po_no)));
		
		foreach($process as $val)
		{
			if($process_arr=='') $process_arr=$conversion_cost_head_array[$val]; else $process_arr.=", ".$conversion_cost_head_array[$val];
		}
		$internalRef=$buyer_po_arr[$row[csf('buyer_po_id')]]['internalRef'];
		$buyerBuyer=$buyer_po_arr[$row[csf('buyer_po_id')]]['buyerBuyer'];
		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
            <td width="30"><? echo $i; ?></td>
            <td width="70"><p><? echo $batchArr[$row[csf('batch_id')]]; ?></p></td>
			<td width="100" id="ref_td"> <? echo $internalRef; ?></td>
			<td width="100"><p><? echo $row[csf('dyeing_batch_no')]; ?></p></td>
            <td width="100"><p><? echo $aopRef; ?></p></td>
			<td width="120" id="buyer_td"> <? echo $buyer_arrs[$buyerBuyer]; ?></td>
            <td width="120"><p><? echo $po_no; ?></p></td>
            <td width="150"><p><? echo $process_arr; ?></p></td>
            <td width="160"><p><? echo $row[csf('fabric_description')]; ?></p></td>
            <td width="60"><p><? echo $color_name_arr[$row[csf('color_id')]]; ?></p></td>
            <td width="60"><p><? echo $row[csf('gsm')]; ?></p></td>
            <td width="90"><p><? echo $row[csf('dia_width')]; ?></p></td>
            <td width="60" align="right"><? echo number_format($row[csf('no_of_roll')],2,'.',''); $total_roll+=$row[csf('no_of_roll')]; ?></td>
			
            <td width="80" align="right"><? echo number_format($row[csf('fabric_used_qnty')],2,'.',''); $total_fabric_used_qnty+=$row[csf('fabric_used_qnty')]; ?></td>
			 <td width="80" align="right"><? echo number_format($row[csf('product_qnty')],2,'.',''); $total_qty+=$row[csf('product_qnty')]; ?></td>
			 <td width="80" align="right"><? echo number_format($row[csf('reject_qnty')],2,'.',''); $total_reject_qnty +=$row[csf('reject_qnty')]; ?></td>
            <td width=""><p><? echo $row[csf('remarks')]; //$dataArray[0][csf('remarks')]; ?></p></td>
		</tr>
		<?php
			$uom_unit="Kg";
			$uom_gm="Grams";
	$i++;
	}
	$internalRef=implode(",",array_unique(explode(",",$internalRef)));
	$buyerBuyer=implode(",",array_unique(explode(",",$buyerBuyer)));
	?>
    	<tr> 
            <td align="right" colspan="12" >Total</td>
            <td align="right"><? echo number_format($total_roll,2,'.',''); ?></td>
           <td align="right"><? echo number_format($total_fabric_used_qnty,2,'.',''); ?></td>
            <td align="right"><? echo number_format($total_qty,2,'.',''); ?></td>
             <td align="right"><? echo number_format($total_reject_qnty,2,'.',''); ?></td>
		</tr>
	</table>
    <br>
	 <?
        echo signature_table(173, $data[0], "1430px");
     ?>
     <script type="text/javascript">
     	//document.getElementById("ref_td").innerHTML='<? echo $internalRef; ?>'
     	//document.getElementById("buyer_td").innerHTML='<? echo $buyer_arrs[$buyerBuyer]; ?>'
     </script>
</div>
</div>
<?
exit();
}

if ($action=="aop_delevery_entry_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	//$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_arrs=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	if($data[3]==2)
	{
		$buyer_arr=$buyer_arrs;
	}
	else
	{
		$buyer_arr=$company_library;
	}
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		//$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		//$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyerBuyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['internalRef']=$row[csf("grouping")];
		//$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);
	//print_r($buyer_po_arr);
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	//$machineArr=return_library_array( "select id, machine_no from  lib_machine_name", "id", "machine_no"  );
	
	$sql=" select id, product_no, basis, location_id, party_id, product_date, prod_chalan_no, remarks from subcon_production_mst where product_no='$data[1]'";
	$dataArray=sql_select($sql);


?>
<div style="width:930px;">
    <table width="930" cellspacing="0" align="right" border="0">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="6" align="center">
				<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, vat_number from lib_company where id=$data[0] and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?> 
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?> 
						Block No: <? echo $result[csf('block_no')];?> 
						City No: <? echo $result[csf('city')];?> 
						Zip Code: <? echo $result[csf('zip_code')]; ?> 
						Province No: <?php echo $result[csf('province')];?> 
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];?> <br>
                        <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
					}
                ?> 
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Note/Challan</u></strong></center></td>
        </tr>
        <tr>
            <td width="160"><strong>Production ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('product_no')]; ?></td>
            <td width="120"><strong>Receive Basis:</strong></td><td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('basis')]]; ?></td>
            <td width="125"><strong>Party Name:</strong></td><td width="175px"><? echo  $buyer_arr[$dataArray[0][csf('party_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Finishing Date:</strong></td> <td width="175px"><? echo change_date_format($dataArray[0][csf('product_date')]); ?></td>
            <td><strong>Challan No :</strong></td><td colspan="3"><? echo $dataArray[0][csf('prod_chalan_no')];// ?></td>
        </tr>
        <tr>
        	<td><strong>Internal Ref. No.:</strong></td><td id="ref_td"></td>
        	<td><strong>Cust. Buyer:</strong></td><td id="buyer_td"></td>
            <td><strong>Remarks:</strong></td><td><? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table>
         <br>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="70" align="center">Batch No</th>
                <th width="120" align="center">Order No</th>
                <th width="150" align="center">Process</th>
                <th width="160" align="center">Const. Compo.</th>
                <th width="60" align="center">Color</th> 
                <th width="60" align="center">GSM</th>
                <th width="60" align="center">Dia/Width</th>
                <th width="60" align="center">Roll</th>
                <th width="80" align="center">Product Qty</th>
                <th width="" align="center">Machine No</th>                   
            </thead>
   <?
	$mst_id=$dataArray[0][csf('id')];
    $i=1;
	//$poArr=return_library_array( "select id, order_no from subcon_ord_dtls", "id", "order_no");
	$ord_sql = "select b.id,a.subcon_job,a.aop_reference,b.order_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$data[0] and a.entry_form=278 and  a.subcon_job=b.job_no_mst";
	$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array();
	foreach ($ordArray as $row)
	{
		$po_arr[$row[csf('id')]] = $row[csf('order_no')];
		$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
	}
	$batchArr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");

	$sqldtls=" select a.id, a.batch_id, a.order_id, a.process, a.fabric_description, a.color_id, a.gsm, a.dia_width, a.product_qnty, a.machine_id, a.no_of_roll, a.buyer_po_id from  subcon_production_dtls a where a.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 order by a.id ASC";
	
	$sql_result=sql_select($sqldtls); $aopRef=''; $internalRef='';	$buyerBuyer='';	
	foreach($sql_result as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		if($internalRef=='') $internalRef=$buyer_po_arr[$row[csf('buyer_po_id')]]['internalRef']; else $internalRef.=", ".$buyer_po_arr[$row[csf('buyer_po_id')]]['internalRef'];
		if($buyerBuyer=='') $buyerBuyer=$buyer_po_arr[$row[csf('buyer_po_id')]]['buyerBuyer']; else $buyerBuyer.=", ".$buyer_po_arr[$row[csf('buyer_po_id')]]['buyerBuyer'];
			
		$order_id=explode(",",$row[csf('order_id')]);
		$process=explode(",",$row[csf('process')]);
		$po_no='';  $process_arr='';
		//$data=explode('*',$data);
		
		foreach($order_id as $val)
		{
			if($po_no=='') $po_no=$po_arr[$row[csf('order_id')]]; else $po_no.=", ".$po_arr[$row[csf('order_id')]];
			if($aopRef=='') $aopRef=$ref_arr[$row[csf('order_id')]]; else $aopRef.=", ".$ref_arr[$row[csf('order_id')]];

		}
		$po_no=implode(",",array_unique(explode(",",$po_no)));
		
		foreach($process as $val)
		{
			if($process_arr=='') $process_arr=$conversion_cost_head_array[$val]; else $process_arr.=", ".$conversion_cost_head_array[$val];
		}
		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
            <td width="30"><? echo $i; ?></td>
            <td width="70"><p><? echo $batchArr[$row[csf('batch_id')]]; ?></p></td>
            <td width="120"><p><? echo $po_no; ?></p></td>
            <td width="150"><p><? echo $process_arr; ?></p></td>
            <td width="160"><p><? echo $row[csf('fabric_description')]; ?></p></td>
            <td width="60"><p><? echo $color_name_arr[$row[csf('color_id')]]; ?></p></td>
            <td width="60"><p><? echo $row[csf('gsm')]; ?></p></td>
            <td width="60"><p><? echo $row[csf('dia_width')]; ?></p></td>
            <td width="60" align="right"><? echo number_format($row[csf('no_of_roll')],2,'.',''); $total_roll+=$row[csf('no_of_roll')]; ?></td>
            <td width="80" align="right"><? echo number_format($row[csf('product_qnty')],2,'.',''); $total_qty+=$row[csf('product_qnty')]; ?></td>
            <td width=""><p><? echo $machineArr[$row[csf('machine_id')]]; ?></p></td>
		</tr>
		<?php
			$uom_unit="Kg";
			$uom_gm="Grams";
	$i++;
	}
	$internalRef=implode(",",array_unique(explode(", ",$internalRef)));
	$buyerBuyer=implode(",",array_unique(explode(", ",$buyerBuyer)));
	?>
    	<tr> 
            <td align="right" colspan="8" >Total</td>
            <td align="right"><? echo number_format($total_roll,2,'.',''); ?></td>
            <td align="right"><? echo number_format($total_qty,2,'.',''); ?></td>
            <td align="right" >&nbsp;</td>
		</tr>
	</table>
    <br>
	 <?
        echo signature_table(173, $data[0], "930px");
     ?>
     <script type="text/javascript">
     	document.getElementById("ref_td").innerHTML='<? echo $internalRef; ?>'
     	document.getElementById("buyer_td").innerHTML='<? echo $buyer_arrs[$buyerBuyer]; ?>'
     </script>
</div>
</div>
<?
exit();
}
?>
