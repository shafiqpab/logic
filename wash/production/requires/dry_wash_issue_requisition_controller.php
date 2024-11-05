<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


// get location by company id....

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-Select Location-", $selected, "load_drop_down('requires/dry_wash_issue_requisition_controller',document.getElementById('cbo_company_id').value+'__'+this.value, 'load_drop_down_floor', 'floor_td');load_drop_down( 'requires/dry_wash_issue_requisition_controller', $('#cbo_company_id').val()+'_'+$('#cbo_location').val(), 'load_drop_down_store', 'store_td' );","","","","","",3 );
	exit();	 
}

if ($action=="load_drop_down_store")
{	// fn_sub_process_enable(this.value);
	list($company_id,$location_id)=explode('_',$data);
	if ($_SESSION['logic_erp']['store_location_id'] != '' && $_SESSION['logic_erp']['store_location_id'] != 0) {$store_location_credential_cond = "and a.id in(".$_SESSION['logic_erp']['store_location_id'].")";} else { $store_location_credential_cond = "";}
	//if($location_id>0){$locationCon="and b.store_location_id=$location_id";}
	$locationCon="";
	if($location_id) $locationCon="and a.location_id=$location_id";
	echo create_drop_down( "cbo_store_name", 140, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in(5,6,7,23) $locationCon $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", $selected, "fnc_item_details(this.value,'','')", "0" );  	 
	exit();
}
if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	$company = $data[0];
	$within_group = $data[1];

	/*if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";*/
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", "", "load_drop_down( 'dry_wash_issue_requisition_controller', this.value+'_'+$within_group, 'load_drop_down_buyer_buyer', 'buyer_buye_td');");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", '', '');
	}	
	exit();	 
} 

if ($action=="load_drop_down_buyer_buyer")
{
    $data=explode("_",$data);
	
	//print_r($data);
	$company=$data[0];
	
    if($data[1]==1)
    {
		echo create_drop_down( "txt_buyer_buyer_no", 125, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company  and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", '', '');
		
    }
    else
    {
       echo '<input name="txt_buyer_buyer_no" id="txt_buyer_buyer_no" class="text_boxes" style="width:115px"  placeholder="Write">';
    }   
    exit();  
}
if ($action=="load_drop_down_floor")
{
	$data=explode("__",$data);
	$company_id=$data[0];
	$location_id=$data[1];
	//if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and b.location_id=$location_id";
	if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and a.location_id=$location_id";

	if($company_id==0 && $location_id==0)
	{
		echo create_drop_down( "cbo_floor_id", 140, $blank_array,"", 1, "--Select Floor--", 0, "",0 );
	}
	else
	{
 		echo create_drop_down( "cbo_floor_id", 140, "select a.id,a.floor_name from lib_prod_floor a, lib_location b where a.location_id=b.id and a.company_id='$company_id' and a.location_id='$location_id' and a.production_process in(7,21) and a.is_deleted=0  and a.status_active=1 $location_cond  order by a.floor_name",'id,floor_name', 1, '--- Select Floor ---', 0);
	}
  	exit();	 
}

if($action=="po_popup")
{
  	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	//if($tot_row>1) $disabled=1; else $disabled=0;
	if($color_name != "") $color_id = return_field_value("id","lib_color","color_name='$color_name' and is_deleted=0 and status_active=1");			
	else $color_id = 0;
	?>
		<script>
		
			function js_set_value( po_id,po_no,gmts_item_id,buyer_id,buyer_po_id,buyer_po_no,color_id,job_no,within_group,po_qnty,buyer_style_ref,order_uom,operation_type,batch_no,batch_id,batch_dtls_id)
			{
				
				document.getElementById('buyer_style_ref').value=buyer_style_ref;
				document.getElementById('po_id').value=po_id;
				parent.emailwindow.hide();
			}

			function fnc_load_party(within_group) 
			{
				 
				var company = '<?php echo $cbo_company_id; ?>';
				var party_name = $('#cbo_party_name').val();
				load_drop_down( 'dry_wash_issue_requisition_controller', company+'_'+within_group, 'load_drop_down_buyer_buyer', 'buyer_buye_td'); 
				load_drop_down( 'dry_wash_issue_requisition_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
			}
		</script>
	</head>
	
	<body onLoad="fnc_load_party(1)">
		<fieldset style="width:950px;margin-left:10px">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table cellpadding="0" cellspacing="0" width="930" class="rpt_table" align="center">
					<thead>
                    <tr>
                        <th colspan="7">
							<?
							echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --");
							?>
                        </th>
                    </tr>
                    <tr>
						<th>Within Group</th>
						<th>Buyer</th>
                        <th>Party Buyer</th>
						<th>Search By</th>
						<th>Search</th>
                        <th class="must_entry_caption">Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="po_id" id="po_id" value="">
							<input type="hidden" name="po_no" id="po_no" value="">
                            <input type="hidden" name="buyer_po_no" id="buyer_po_no" value="">
							<input type="hidden" name="gmts_item_id" id="gmts_item_id" value="">
							<input type="hidden" name="buyer_id" id="buyer_id" value="">
                            <input type="hidden" name="operation_type_id" id="operation_type_id" value="">
                            <input type="hidden" name="batch_color_id" id="batch_color_id" value="">
                            <input type="hidden" name="batch_no" id="batch_no" value="">
                            <input type="hidden" name="batch_id" id="batch_id" value="">
                            <input type="hidden" name="batch_dtls_id" id="batch_dtls_id" value="">
                            <input type="hidden" name="buyer_style_ref" id="buyer_style_ref" value="">
						</th> 
                        </tr>
					</thead>
					<tr class="general">
						<td>
							<?php echo create_drop_down( 'cbo_within_group', 110, $yes_no, '', 0, '', 0, "fnc_load_party(this.value); " ); ?>	
						</td>
						<td id="buyer_td">
							<!-- <input type="text" name="cbo_buyer_name" id="cbo_buyer_name" value="<?php // echo $buyer_id; ?>"> -->
							<?php
							echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", '', "");
							// echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyer_id,'',$disabled); ?></td>
                          <td id="buyer_buye_td">
                         <? 
                          echo create_drop_down( "txt_buyer_buyer_no", 125, $blank_array,"", 1, "-- Select buyer --", $selected, "load_drop_down( 'dry_wash_issue_requisition_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer_buyer', 'buyer_buye_td'); ",1,"" ); ?>
                        </td>
						<td><?
								$search_by_arr=array(1=>"PO No",2=>"Wash Job No",3=>"Buyer Style No");
								echo create_drop_down("cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "2",$dd,0 );
							?>
						</td>                 
						<td><input type="text" style="width:100px" class="text_boxes"  name="txt_search_common" id="txt_search_common" /></td> 
                         <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" value="" readonly>
                                &nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date"  value="" readonly>
                        </td>						
						<td><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_party_name').value+'_'+<? echo $color_id; ?>+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('txt_buyer_buyer_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+ $('#txt_buyer_buyer_no option:selected' ).text()+'_'+<? echo $batch_against; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_po_search_list_view', 'search_div', 'dry_wash_issue_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" /></td>
					</tr>
                    <tfoot>
                        <tr>
                            <td colspan="8" align="center">
                                <? echo load_month_buttons(1); ?>
                            </td>
                        </tr>
                    </tfoot>
				</table>
				<div id="search_div" style="margin-top:10px"></div>   
			</form>
		</fieldset>
	</body>   
    <script>
    	/* if (form_validation('txt_date_from*txt_date_to','Date From*Date To')==false)
		{
			return;
		}*/
    
    </script>        
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_library=return_library_array( "select id, company_name from lib_company", 'id', 'company_name');
	$search_string=trim($data[0]);
	$search_by=$data[1];
	$company_id =$data[2];
	$buyer_id =$data[3];
	$color_id =$data[4];
	$within_group = $data[5];
	$txt_buyer_buyer_no = $data[6];
	$cbo_year_selection = $data[7];
	$buyer_buyer_value = $data[8];
	$batch_against = $data[9];
 	$date_from=$data[10];
	$date_to=$data[11];
	$search_type = $data[12];
	
 // echo $batch_against; die;
 
 		$date_cond="";
		
	if($data[0] != "") $date_cond="";
	else
	{
  		if($date_from != "" && $date_to != "")
		{
			if($db_type==0)
			{
				$date_cond=" and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			}
			else
			{
				$date_cond=" and a.receive_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";
			}
		}
		else
		{
			echo "Please Insert Date Range"; die;
		}
			
	}
	


	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[7]"; }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";}
	
	if($search_by==1) $search_field='b.order_no'; else $search_field='a.subcon_job';
	if($buyer_id==0) $party_cond=""; else $party_cond="and a.party_id=$buyer_id";
	if($color_id==0) $color_cond =""; else $color_cond ="and b.gmts_color_id='$color_id'";
	
	 

   if($within_group==1)
   {
	  if($txt_buyer_buyer_no!=0) 
	  {
         
		  $buyer_buyer_cond=" and b.party_buyer_name like '%$buyer_buyer_value%'";  
	  }
    } 
	else if($within_group==2)
	{
        if ($txt_buyer_buyer_no!='') $buyer_buyer_cond=" and b.party_buyer_name like '%$txt_buyer_buyer_no%'"; else $buyer_buyer_cond="";
    }
	  
	if($db_type==0)  
    {
       $gmts_item_id_cond = "group_concat(b.gmts_item_id) as gmts_item_id";
       $color_id_cond = "group_concat(b.gmts_color_id) as color_id";
    }
    else
    {
       $gmts_item_id_cond = "listagg(cast(b.gmts_item_id as varchar2(4000)),',') within group (order by b.gmts_item_id) as gmts_item_id";
       $color_id_cond = "listagg(cast(b.gmts_color_id as varchar2(4000)),',') within group (order by b.gmts_color_id) as color_id";
    }

    $po_ids='';
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	
	
	if ($search_type == 1) 
	{
  		if($search_string !="" && ($search_by==3 || $search_by==2 || $search_by==1 ))
		{
			$style_cond=''; 
			if($search_by==3)
			{
				//$style_cond=" and a.style_ref_no like '%$search_string%'";
				$buyer_style_ref_cond=" and b.buyer_style_ref='$search_string' ";
			}
			else if($search_by==2)
			{
				$job_cond=" and a.subcon_job='$search_string'";
			}
			else
			{
				$po_cond=" and b.order_no='$search_string'";
			}
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $style_cond", "id");
		}
		
	} 
	else if ($search_type == 4 || $search_type == 0) 
	{
  		
		if($search_string !="" && ($search_by==3 || $search_by==2 || $search_by==1 ))
		{
			$style_cond=''; 
			if($search_by==3)
			{
				//$style_cond=" and a.style_ref_no like '%$search_string%'";
				$buyer_style_ref_cond=" and b.buyer_style_ref like '%$search_string%' ";
			}
			else if($search_by==2)
			{
				$job_cond=" and a.subcon_job like '%$search_string%'";
			}
			else
			{
				$po_cond=" and b.order_no like '%$search_string%'";
			}
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $style_cond", "id");
	}
		
	} 
	else if ($search_type == 2) 
	{
		
		
		if($search_string !="" && ($search_by==3 || $search_by==2 || $search_by==1 ))
		{
			$style_cond=''; 
			if($search_by==3)
			{
				//$style_cond=" and a.style_ref_no like '%$search_string%'";
				$buyer_style_ref_cond=" and b.buyer_style_ref like '$search_string%' ";
			}
			else if($search_by==2)
			{
				$job_cond=" and a.subcon_job like '$search_string%'";
			}
			else
			{
				$po_cond=" and b.order_no like '$search_string%'";
			}
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $style_cond", "id");
		}
	
	} 
	else if ($search_type == 3) 
	{
		if($search_string !="" && ($search_by==3 || $search_by==2 || $search_by==1 ))
		{
			$style_cond=''; 
			if($search_by==3)
			{
				//$style_cond=" and a.style_ref_no like '%$search_string%'";
				$buyer_style_ref_cond=" and b.buyer_style_ref like '%$search_string' ";
			}
			else if($search_by==2)
			{
				$job_cond=" and a.subcon_job like '%$search_string'";
			}
			else
			{
				$po_cond=" and b.order_no like '%$search_string'";
			}
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $style_cond", "id");
		}
	
	}
 	
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql); $buyer_po_arr=array();
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		//$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
		//$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);

	 

	
		  $sql = "select a.subcon_job as job_no, a.within_group, a.party_id as buyer_name,b.buyer_po_id,b.buyer_po_no, b.order_uom, b.gmts_item_id as gmts_item_id, b.gmts_color_id as color_id, b.id, b.order_no as po_number, b.order_quantity as po_qnty,b.buyer_style_ref, a.job_no_prefix_num,b.party_buyer_name
		from subcon_ord_mst a, subcon_ord_dtls b,subcon_ord_breakdown c
		where a.subcon_job=b.job_no_mst and a.id=b.mst_id and b.id=c.mst_id and a.entry_form=295 and a.company_id=$company_id $color_cond $party_cond $po_idsCond $job_cond $po_cond $year_cond $buyer_style_ref_cond and a.within_group = $within_group and a.status_active=1 and c.process=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_buyer_cond $date_cond
		group by a.subcon_job,a.within_group, a.party_id,b.buyer_po_id,b.buyer_po_no, b.order_uom, b.gmts_item_id, b.gmts_color_id, b.id, b.order_no, b.order_quantity,b.buyer_style_ref, a.job_no_prefix_num,b.party_buyer_name
		order by b.id desc";
	
	

//echo $sql;

	$nameArray=sql_select( $sql );
	
	
	
	$ord_dtls_array=array();
	foreach($nameArray as $row)
	{
		$ord_dtls_array[$row[csf('id')]]=$row[csf('id')];
	}
	//echo "<pre>";
	//print_r($ord_dtls_array);
	
	if(implode(',', $ord_dtls_array)!="")
	{
	 /*$operation_type_sql="select a.color_id,a.operation_type, b.id, b.po_id, b.prod_id, b.roll_no, b.batch_qnty, b.buyer_po_id from pro_batch_create_mst a, pro_batch_create_dtls b where  b.po_id in(".implode(',', $ord_dtls_array).") and a.entry_form=316 and  a.id=b.mst_id and b.status_active=1 and b.is_deleted=0  order by b.id";*/
	 
	 $operation_type_sql="select a.color_id,a.operation_type, b.id, b.po_id, b.prod_id, b.roll_no, b.batch_qnty, b.buyer_po_id from pro_batch_create_mst a, pro_batch_create_dtls b where   a.company_id=$company_id and a.entry_form=316 and  a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by b.id";
	 $data_array=sql_select($operation_type_sql); 
	}
	
	 //$operation_type_sql="select a.color_id,a.operation_type, b.id, b.po_id, b.prod_id, b.roll_no, b.batch_qnty, b.buyer_po_id from pro_batch_create_mst a, pro_batch_create_dtls b where  b.po_id in(".implode(',', $ord_dtls_array).") and a.entry_form=316 and  a.id=b.mst_id and b.status_active=1 and b.is_deleted=0  order by b.id";
	//$data_array=sql_select($operation_type_sql); 
	$operation_type_array=array();
	
	foreach($data_array as $row)
	{
		$operation_type_array[$row[csf('po_id')]]['operation_type']=$row[csf('operation_type')];	
		$operation_type_array[$row[csf('po_id')]]['color_id']=$row[csf('color_id')];	
	}
	
	//echo "<pre>";
	//print_r($operation_type_array);
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="110">Job No</th>
                <th width="50">Job Prefix</th>
                <th width="110">PO No</th>
                <th width="110">Buyer Name</th>
                <th width="110">Party Buyer</th>
                <th width="100">Buyer PO</th>
                <th width="110">Buyer Style</th>
                <th width="70">PO Qty</th>
                <th width="50">UOM</th>
                <th>Colors</th>
            </thead>
        </table>
        <div style="width:970px; overflow-y:scroll; max-height:260px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;//if($cbo_within_group==1){echo $buyer_arr[$row['buyer_buyer']];}else{echo $row['buyer_buyer'];}
				
				foreach ($nameArray as $selectResult)
				{
					//$items_id=implode(",", array_unique(explode(",", $selectResult[csf('gmts_item_id')])));
					$qty=0;
					$qty=number_format($selectResult[csf('po_qnty')]*12,0,'','');
					$operation_type=$operation_type_array[$selectResult[csf('id')]]['operation_type'];

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$buyer = $selectResult[csf('within_group')] == 1 ? $company_library[$selectResult[csf('buyer_name')]] : $party_arr[$selectResult[csf('buyer_name')]];
					$party_buyer = $selectResult[csf('within_group')] == 1 ? $selectResult[csf('party_buyer_name')] : $selectResult[csf('party_buyer_name')];
				?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('po_number')]; ?>','<? echo $selectResult[csf('gmts_item_id')]; ?>','<? echo $selectResult[csf('buyer_name')]; ?>','<? echo $selectResult[csf('buyer_po_id')]; ?>','<? echo $selectResult[csf('buyer_po_no')]; ?>','<? echo $selectResult[csf('color_id')]; ?>','<? echo $selectResult[csf('job_no')]; ?>','<? echo $selectResult[csf('within_group')]; ?>','<? echo $selectResult[csf('po_qnty')]; ?>','<? echo $selectResult[csf('buyer_style_ref')]; ?>','<? echo $selectResult[csf('order_uom')]; ?>','<? echo $operation_type; ?>')"> 
                        <td width="30" align="center"><? echo $i; ?></td>	
                        <td width="110" style="word-break:break-all"><? echo $selectResult[csf('job_no')]; ?></td>
                        <td width="50" style="word-break:break-all"><?php echo $selectResult[csf('job_no_prefix_num')]; ?></td>
                        <td width="110" style="word-break:break-all"><? echo $selectResult[csf('po_number')]; ?></td>
                        <td width="110" style="word-break:break-all"><?php echo $buyer; ?></td>
                         <td width="110" style="word-break:break-all"><?php echo $party_buyer; ?></td>
                        <td width="100" style="word-break:break-all" title="<? echo $selectResult[csf('buyer_po_id')]; ?>"><? echo $selectResult[csf('buyer_po_no')]; ?></td>
                        <td width="110" style="word-break:break-all"><? if($selectResult[csf('within_group')]==1){echo $buyer_po_arr[$selectResult[csf('buyer_po_id')]]['style'];}else { echo $selectResult[csf('buyer_style_ref')] ;} ?></td>
                        <td width="70" align="right"><? echo $selectResult[csf('po_qnty')]; ?></td> 
                        <td width="50" align="center"><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></td>
                        <td style="word-break:break-all"><? echo $color_arr[$selectResult[csf('color_id')]]; ?></td>
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


if($action=="populate_data_from_data")
{
	 
	
	$sql = sql_select("select id, requisition_no_prefix, requisition_no_prefix_num, 
   requisition_no, entry_form, company_id, 
   location_id, floor_id, requisition_date, 
   issuse_basis_id, requisition_for, style, 
   store_id, po_id, is_editable from dry_issue_requisition_master where id=$data");
	foreach($sql as $row)
	{
		echo "document.getElementById('txt_requisition_id').value = '".$row[csf("requisition_no")]."';\n"; 
		echo "document.getElementById('poId').value = '".$row[csf("po_id")]."';\n"; 
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n"; 
 		echo "document.getElementById('cbo_company_id').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_location').value = '".$row[csf("location_id")]."';\n"; 
		echo "document.getElementById('cbo_store_name').value = '".$row[csf("store_id")]."';\n"; 
		echo "load_drop_down('requires/dry_wash_issue_requisition_controller',".$row[csf("company_id")]."+'__'+".$row[csf("location_id")].", 'load_drop_down_floor', 'floor_td');\n";
		  
		echo "document.getElementById('cbo_floor_id').value = '".$row[csf("floor_id")]."';\n";  
 		echo "document.getElementById('txt_requisition_date').value = '".change_date_format($row[csf("requisition_date")])."';\n"; 
		echo "document.getElementById('cbo_receive_basis').value = '".$row[csf("issuse_basis_id")]."';\n"; 
 		echo "document.getElementById('cbo_method').value = '".$row[csf("requisition_for")]."';\n";
 		echo "document.getElementById('txt_buyer_style').value = '".$row[csf("style")]."';\n"; 
  		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_dry_wash_issue_requisition_entry',1);\n";  
		exit();
	}
}

if($action=="item_details")
{
	//echo $data; die;
	$data=explode("**",$data);
	$company_id=$data[0];
	$sub_process_id=$data[1];
	$issue_basis=$data[2];
	$is_update=$data[3];
	$cbo_store=$data[4];
	
//	3**3**4**1**56

	 
	//echo $req_id;
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$store_arr=return_library_array( "select a.id as id,a.store_name  as store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in(5,6,7,23) group by a.id,a.store_name order by a.store_name", "id", "store_name"  );
	?>
	<div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="50">Product ID</th>
                <th width="100">Lot No</th>
                <th width="100">Item Cat.</th>
                <th width="100">Group</th>
                <th width="100">Sub Group</th>
                <th width="140">Item Description</th>
                <th width="32">UOM</th>
                <th width="70">Stock Qty</th>
                <th width="">Req. Qty.</th>
            </thead>
        </table>
        <div style="width:900px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="tbl_list_search">
                <tbody>
                <?
                if($is_update=="")
                {
					if($issue_basis==4)
					{
						
						$sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.cons_qty as store_stock, b.lot 
						from product_details_master a, inv_store_wise_qty_dtls b 
						where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(5,6,7,23) and b.cons_qty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
						order by a.item_category_id, a.id";	
						//echo $sql;die;

						$i=1;
						$stock_qty_arr=fnc_store_wise_stock($company_id,$cbo_store);

						$nameArray=sql_select( $sql );

						//print_r($nameArray);

						foreach ($nameArray as $selectResult)
						{
	                        $issue_remain=$totalIssued-$totalIssuedReturn;
							if ($i%2==0)  
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							//$stock_qty=$current_stock=number_format($stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]]['stock'],6,'.', '');
							//$stock_qty_org=number_format($stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]]['stock'],6,'.', '');
							$stock_qty=$current_stock=number_format($selectResult[csf('store_stock')],6,'.', '');
							$stock_qty_org=number_format($selectResult[csf('store_stock')],6,'.', '');
							//$update_stock=$stock_qty_org+$selectResult[csf('req_qny_edit')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>">
                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td> 
                                <td width="50" align="center" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
                                <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
                                </td>
                                <td width="100" align="center"><p><? echo $selectResult[csf('lot')]; ?>
	                                <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" value="<? echo $selectResult[csf('lot')]; ?>"></p></td>
                                <td width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
                                <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
                                </td>
                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
                                <td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
                                <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
                                <td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
                                
                                <td width="70" title="<? echo $stock_qty?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo number_format($stock_qty_org,6,'.', '');//$selectResult[csf('current_stock')]; ?>"  disabled></td>
                                 <td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
                                <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($recipe_qnty,6,'.', ''); ?>" readonly>
                                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $selectResult[csf('dtls_id')]; ?>">	
                                <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
                                <input type="hidden" name="stock_check[]" id="stock_check_<? echo $i; ?>" value="<? echo number_format($stock_qty,6,'.', '');//$selectResult[csf('current_stock')]; ?>">
                                <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo number_format($stock_qty_org,6,'.', ''); ?>)"  value="" >
                                <input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($selectResult[csf('req_qny_edit')],6,'.', ''); ?>" / >
                                <input type="hidden" name="subreqprocessId[]" id="subreqprocessId_<? echo $i; ?>" value="<?  ?>">
                                </td>
							</tr>
							<?
							$i++;
						}
					}
					
                }
                else
                {
				
				
				if(str_replace("'","",$is_update)>0)
				{
					
					
				 
					
					$dtls_data=sql_select("select a.store_id, b.id as dtls_id, b.prod_id, b.requsition_qty from dry_issue_requisition_details b, dry_issue_requisition_master a where a.id = b.mst_id and b.mst_id=$is_update and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.prod_id");
					//$dtls_data=sql_select("select b.id as dtls_id, b.product_id, b.store_id, b.dose_base as dose_base_curr, b.item_lot, b.ratio, b. recipe_qnty, b.required_qnty, b.remarks from dyes_chem_issue_requ_dtls b where b.mst_id=$update_id and b.status_active=1 and b.is_deleted=0 order by b.product_id");
					$dtls_data_arr=array();$recipe_prod_id_arr=array(); $product_data_arr=array();
					foreach($dtls_data as $row)
					{
						$prod_key=$row[csf('prod_id')]."_".$row[csf('store_id')];
						$dtls_data_arr[$prod_key]["dtls_id"]=$row[csf("dtls_id")];
 						$dtls_data_arr[$prod_key]["requsition_qty"]=$row[csf("requsition_qty")];
 						$recipe_prod_id_arr[$prod_key]=$prod_key;

					}
					//echo "<pre>";
					//print_r($recipe_prod_id_arr);
				}
				
				
				
				
						
					   $sql="select a.id, a.item_category_id, a.item_group_id, a.sub_group_name, a.item_description, a.item_size, a.unit_of_measure, a.current_stock, b.cons_qty as store_stock,b.store_id, b.lot 
						from product_details_master a, inv_store_wise_qty_dtls b 
						where a.id=b.prod_id and a.company_id='$company_id' and b.store_id=$cbo_store and a.item_category_id in(5,6,7,23) and b.cons_qty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
						order by a.item_category_id, a.id";	
					
					
					 
					//print_r($total_req_arr); 
					$i=1;
					$stock_qty_arr=fnc_store_wise_stock($company_id,$cbo_store);
					//echo "<pre>";
					//print_r($stock_qty_arr);
					$nameArray=sql_select( $sql );
					$desable_cond="";
					$desable_id=0;
					//if($is_posted_account==1) { $desable_cond=" disabled";  $desable_id=1;}
					foreach ($nameArray as $selectResult)
					{
						if ($i%2==0)  
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						
						
						$prod_key=$selectResult[csf('id')]."_".$selectResult[csf('store_id')];
						
						$dtls_id=$dtls_data_arr[$prod_key]["dtls_id"] ;
 						$requsition_qty=$dtls_data_arr[$prod_key]["requsition_qty"];
						
						
						 
						 
						$stock_qty=number_format($stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]][$dyes_lot]['stock'],6,'.', '');
						$stock_qty_org=number_format($stock_qty_arr[$company_id][$cbo_store][$selectResult[csf('item_category_id')]][$selectResult[csf('id')]][$dyes_lot]['stock'],6,'.', '');
						//echo $selectResult[csf('req_qny_edit')]."==".$total_issue_arr[$selectResult[csf("product_id")]];
						$total_preveious_issue=($total_issue_arr[$selectResult[csf("id")]][$selectResult[csf("sub_req_process_id")]])*1;
 						$total_requisition_qty=($total_req_arr[$selectResult[csf("id")]][$selectResult[csf("sub_req_process_id")]])*1;
 						if($issue_basis==4)
						{
							//echo $stock_qty_org.'=='.$selectResult[csf('req_qny_edit')];
							?>
							<tr align="center" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
                                <td width="30" align="center" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
                                <td width="50" id="product_id_<? echo $i; ?>"><? echo $selectResult[csf('id')]; ?>
                                <input type="hidden" name="txt_prod_id[]" id="txt_prod_id_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('id')]; ?>">
                                </td>
                                <td width="100" align="center"><? echo $selectResult[csf('batch_lot')]; ?><p>
	                                <input type="hidden" name="txt_lot[]" id="txt_lot_<? echo $i; ?>" value="<? echo $selectResult[csf('batch_lot')]; ?>"></p></td>
                                <td  width="100"><p><? echo $item_category[$selectResult[csf('item_category_id')]]; ?></p>
                                <input type="hidden" name="txt_item_cat[]" id="txt_item_cat_<? echo $i; ?>" class="text_boxes_numeric" style="width:38px"  value="<? echo $selectResult[csf('item_category_id')]; ?>">
                                </td>
                                <td width="100" id="item_group_id_<? echo $i; ?>"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]; ?></p> &nbsp;</td>
                                <td width="100" id="sub_group_name_<? echo $i; ?>"><p><? echo $selectResult[csf('sub_group_name')]; ?></p></td>
                                <td width="140" id="item_description_<? echo $i; ?>"><p><? echo $selectResult[csf('item_description')]." ".$selectResult[csf('item_size')]; ?></p></td> 
                                <td width="32" align="center" id="uom_<? echo $i; ?>"><? echo $unit_of_measurement[$selectResult[csf('unit_of_measure')]]; ?></td>
                                
                                <td width="70" title="<? echo $stock_qty?>" align="center" id="td_stock_qty_<? echo $i; ?>"><input type="text" name="stock_qty[]" id="stock_qty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px"  value="<? echo number_format($stock_qty,6,'.', '');//$selectResult[csf('current_stock')]; ?>"  disabled></td>
                                 <td width="" align="center" id="reqn_qnty_<? echo $i; ?>">
                                <input type="hidden" name="txt_reqn_qnty[]" id="txt_reqn_qnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($selectResult[csf('req_qny_edit')],6,'.', ''); ?>" readonly>
                                <input type="text" name="reqn_qnty_edit[]" id="txt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($requsition_qty,6,'.', ''); ?>" onKeyUp="check_data('#txt_reqn_qnty_edit_<? echo $i; ?>',<? echo $update_stock; ?>)"  <? echo $desable_cond; ?> />
                                <input type="hidden" name="hidreqn_qnty_edit[]" id="hidtxt_reqn_qnty_edit_<? echo $i; ?>" class="text_boxes_numeric" style="width:80%"  value="<? echo number_format($requsition_qty,6,'.', ''); ?>" / >
                                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i; ?>" value="<? echo $dtls_id; ?>">	
                                <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $selectResult[csf('trans_id')]; ?>">	
                                <input type="hidden" name="subreqprocessId[]" id="subreqprocessId_<? echo $i; ?>" value="" >
                                </td>
							</tr>
						<?
						}
						
						$i++;
		    	    }
					echo '<input type="hidden"  id="txt_isu_qnty" value="'.$issue_total_val.'" >';
					echo '<input type="hidden"  id="txt_isu_num" value="'.$issue_num_all.'" >';
               }
             ?>
           </tbody>
      </table>
   </div>
	</div>           
	<?
	exit();	
}

//Store Wise Stock Function
function fnc_store_wise_stock($company_id,$store_id,$category='',$prod_id='')
{
	//echo $company_id."=".$store_id."=".$category."=".$prod_id;
	$result=sql_select("select	 category_id,prod_id,cons_qty, lot 
	from  inv_store_wise_qty_dtls where  company_id=$company_id and store_id=$store_id and status_active=1 and is_deleted=0");
	$stock_qty_arr=array();
	foreach($result as $row)
	{
		 $stock_qty_arr[$company_id][$store_id][$row[csf('category_id')]][$row[csf('prod_id')]][$row[csf('lot')]]['stock']=$row[csf('cons_qty')]; 
	}
	return $stock_qty_arr;
}


if($action=="save_update_delete")
{	  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	 $update_id=str_replace("'","",$update_id);
	 $cbo_company_id=str_replace("'","",$cbo_company_id);
	 $cbo_location=str_replace("'","",$cbo_location);
	 $cbo_floor_id=str_replace("'","",$cbo_floor_id);
	 $txt_requisition_date=str_replace("'","",$txt_requisition_date);
	 $cbo_receive_basis=str_replace("'","",$cbo_receive_basis);
	 $cbo_method=str_replace("'","",$cbo_method);
	 $txt_buyer_style=str_replace("'","",$txt_buyer_style);
	 $cbo_store_name=str_replace("'","",$cbo_store_name);
	 $poId=str_replace("'","",$poId);

	//echo $txt_buyer_po.'hi';die;
	//--------------------------for check issue date with all product id's last receive date
  	$issue_store_id=str_replace("'","",$cbo_store_name);
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		if (str_replace("'", "", $update_id) == "") 
		{
			if ($db_type == 0) $year_cond = "YEAR(insert_date)";
			else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
			else $year_cond = "";
			
			$new_requ_no = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_id), '', 'DWIR', date("Y", time()), 5, "select dry_issue_requisition_master, requisition_no_prefix_num from dry_issue_requisition_master where company_id=$cbo_company_id and entry_form=300 and $year_cond=" . date('Y', time()) . " order by id desc ", "requisition_no_prefix", "requisition_no_prefix_num"));
			
 			$id = return_next_id("id", "dry_issue_requisition_master", 1);
			$requNo=$new_requ_no[0];
 			$field_array = "id, entry_form,requisition_no, requisition_no_prefix, requisition_no_prefix_num, company_id, location_id, floor_id,requisition_date, issuse_basis_id, requisition_for, style, store_id,po_id,inserted_by, insert_date";
			
  			$data_array = "(" . $id . ",524,'" . $new_requ_no[0] . "','" . $new_requ_no[1] . "'," . $new_requ_no[2] . ",".$cbo_company_id.",".$cbo_location.",'".$cbo_floor_id."','".$txt_requisition_date."',".$cbo_receive_basis.",'".$cbo_method."','".$txt_buyer_style."',".$issue_store_id.",".$poId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$requisition_update_id = $id;
  		}
		else
		{
			$requisitionNo=$txt_requisition_id;
			$field_array_update = "company_id*location_id*floor_id*requisition_date*issuse_basis_id*requisition_for*style* store_id*po_id*updated_by*update_date";

			$data_array_update = $cbo_company_id."*".$cbo_location."*'".$cbo_floor_id."'*'".$txt_requisition_date."'*".$cbo_receive_basis."*'".$cbo_method."'*'".$txt_buyer_style."'*".$issue_store_id."*".$poId."*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

			//$rID=sql_update("pro_recipe_entry_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			//if($rID) $flag=1; else $flag=0;
			$requisition_update_id = str_replace("'", "", $update_id);
			
			
		}
		
		
 		
 		$dtls_id=return_next_id( "id", "dry_issue_requisition_details", 1 ) ;
 	    $field_array_dtls = "id,mst_id,prod_id,requsition_qty,inserted_by,insert_date";
		for ($i =1; $i <= $total_row; $i++) 
		{
 			    //$product_id = trim("txt_prod_id_".$i);
				
				$product_id = trim("txt_prod_id_" .$i);
				$txt_reqn_qnty_edit= trim("txt_reqn_qnty_edit_".$i);
 				if($data_array_dtls!="") $data_array_dtls .=",";
 				$data_array_dtls .= "(" . $dtls_id . "," . $id . ",'" . str_replace("'", "",$$product_id) . "','" . str_replace("'", "", $$txt_reqn_qnty_edit) . "',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
   			$dtls_id = $dtls_id + 1;
  			
 		}
		
		
			$rID=sql_insert("dry_issue_requisition_master",$field_array,$data_array,0);
 		    if($rID) $flag=1; else $flag=0;
			$rID2=sql_insert("dry_issue_requisition_details",$field_array_dtls,$data_array_dtls,0);
    		if($rID2) $flag=1; else $flag=0;
  			//echo "10**insert into dry_issue_requisition_details (".$field_array_dtls.") values ".$data_array_dtls.""; disconnect($con); die;
			
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$requisition_update_id."**".$requNo."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$requisition_update_id."**".$requNo."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
			}
		}

		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;

	}
	else if ($operation==1) {

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 $field_array_up = "company_id*location_id*floor_id*requisition_date*issuse_basis_id*requisition_for*style* store_id*po_id*updated_by*update_date";
		$data_array_update = $cbo_company_id."*".$cbo_location."*'".$cbo_floor_id."'*'".$txt_requisition_date."'*".$cbo_receive_basis."*'".$cbo_method."'*'".$txt_buyer_style."'*".$issue_store_id."*".$poId."*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";
 		 
	   $rID=sql_update("dry_issue_requisition_master",$field_array_up,$data_array_update,"id",$update_id,1); 
		if($rID) $flag=1; else $flag=0;
		$delete_dtls=execute_query("delete from dry_issue_requisition_details where mst_id=$update_id",0);
		if($delete_dtls) $flag=1; else $flag=0;
		 
 	    $dtls_id=return_next_id( "id", "dry_issue_requisition_details", 1 ) ;
 	    $field_array_dtls = "id,mst_id,prod_id,requsition_qty,inserted_by,insert_date";
		for ($i =1; $i <= $total_row; $i++) 
		{
 				$product_id = trim("txt_prod_id_" .$i);
				$txt_reqn_qnty_edit= trim("txt_reqn_qnty_edit_".$i);
 				if($data_array_dtls!="") $data_array_dtls .=",";
 				$data_array_dtls .= "(" . $dtls_id . "," . $update_id . ",'" . str_replace("'", "",$$product_id) . "','" . str_replace("'", "", $$txt_reqn_qnty_edit) . "',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
   			$dtls_id = $dtls_id + 1;
  			
 		}
		
		   
 		    $rID2=sql_insert("dry_issue_requisition_details",$field_array_dtls,$data_array_dtls,0);
    		if($rID2) $flag=1; else $flag=0;
			
			
			//echo '10**'; echo str_replace("'","",$txt_requisition_id); die;
  			//echo "10**insert into dry_issue_requisition_details (".$field_array_dtls.") values ".$data_array_dtls.""; disconnect($con); die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
 				echo "0**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_requisition_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_requisition_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_requisition_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_requisition_id);
			}
		}

		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;


	}

}
 

if($action=="mrr_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(mrr)
	{
 		$("#hidden_issue_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>                	 
                    <th width="150">Search By</th>
                    <th width="200" align="center" id="search_by_td_up">Enter Issue No</th> 
                    <th width="150">Batch No</th>
                    <th width="180" class="must_entry_caption">Issue Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center">
                        <?  
                            $search_by = array(1=>'Requisition No');
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>
                    <td align="center">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_batch_no" id="txt_batch_no" />	
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="checkFields();showList();" style="width:100px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center" height="40" valign="middle" colspan="5">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here -->
                     <input type="hidden" id="hidden_issue_number" value="" />
                    <!-- END -->
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
   </div>
   <script>
    	var isValidated = false;
    	function checkFields() {
    		var searchString = document.getElementById('txt_search_common').value;
    		var batchNo = document.getElementById('txt_batch_no').value;

    		if(searchString == '' && batchNo == '') {
    			if( !form_validation('txt_date_from*txt_date_to','Date From*Date To') ) {
					return;
				}
    		}

    		isValidated = true;
    	}

    	function showList() {
    		if(!isValidated) {
    			return;
    		}

    		show_list_view (document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_batch_no').value+'_'+document.getElementById('cbo_year_selection').value, 'create_mrr_search_list_view', 'search_div', 'dry_wash_issue_requisition_controller', 'setFilterGrid(\'list_view\',-1)');
    		isValidated = false;

    	}
    </script>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$start_date = $ex_data[2];
	$end_date = $ex_data[3];
	$company = $ex_data[4];
	$batch_no = str_replace("'","",$ex_data[5]);
	$yearid = str_replace("'","",$ex_data[6]);
	
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and requisition_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and requisition_date between '".change_date_format($start_date,"yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date,"yyyy-mm-dd", "-",1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	
/*	echo "select id, requisition_no_prefix, requisition_no_prefix_num, 
   requisition_no, entry_form, company_id, 
   location_id, floor_id, requisition_date, 
   issuse_basis_id, requisition_for, style, 
   store_id, po_id, is_editable from dry_issue_requisition_master where company_id=$company $date_cond "; die;
	*/
	$details_sql = sql_select("select id, requisition_no_prefix, requisition_no_prefix_num, 
   requisition_no, entry_form, company_id, 
   location_id, floor_id, requisition_date, 
   issuse_basis_id, requisition_for, style, 
   store_id, po_id, is_editable from dry_issue_requisition_master where company_id=$company $date_cond ");

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_library=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
 	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
?>
	<br/>
    <table  align="right"  cellspacing="0" width="1020"  border="1" rules="all" class="rpt_table"  >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="120" >Company Name</th>
            <th width="120" >Location</th>
            <th width="120" >Floor/Unit</th>
            <th width="120" >Requisition Date</th>
            <th width="120" >Requisition Basis</th>
            <th width="120" >Requisition For</th>
            <th width="120" >Style</th>
            <th  >Store Name</th>
        </thead>
    </table>
 <div style="width:1050px;max-height:300px; padding-left:18px; overflow-y:scroll" id="scroll_body">
 	<table  cellspacing="0" width="1020"  border="1" rules="all" class="rpt_table"  id="list_view" >
        <tbody id="list_view"> 
        <?
		$i=1;
	    foreach($details_sql as $row)
		{
		?>
         	<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf("LOCATION_ID")]."_".$row[csf("FLOOR_ID")]."_".$row[csf("REQUISITION_DATE")]."_".$row[csf("ISSUSE_BASIS_ID")]."_".$row[csf("REQUISITION_FOR")]."_".$row[csf("STYLE")]."_".$row[csf("STORE_ID")]."_".$row[csf("id")];?>")' style="cursor:pointer">
                <td width="30" ><?php echo $i; ?></td>
                <td width="120" align="right"><?php echo  $company_arr[$row[csf("COMPANY_ID")]]; ?></td>
                <td width="120"align="center"><?php echo $location_arr[$row[csf("LOCATION_ID")]]; ?></td>
                <td width="120" align="center"><?php echo $floor_arr[$row[csf("FLOOR_ID")]]; ?></td>
                <td width="120" align="center"><?php echo change_date_format($row[csf("REQUISITION_DATE")]); ?></td>
                <td width="120" align="center"><?php echo  $receive_basis_arr[$row[csf("ISSUSE_BASIS_ID")]]; ?></td>
                <td width="120" align="center"><?php echo $row[csf("REQUISITION_FOR")]; ?></td>
                <td width="120" align="center"><?php echo $row[csf("STYLE")]; ?></td>
                <td width="" align="center"><?php echo $store_library[$row[csf("STORE_ID")]]; ?></td>
               
           </tr>
        <?
		$i++;	
		}
		?>
	
		</tbody>
    </table>
</div>	

	
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

 