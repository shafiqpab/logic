<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-Select Location-", $selected, "","","","","","",3 );
	exit();	 
}

if ($action == "load_drop_down_buyer") 
{
	$exdata=explode("_",$data);
	if($exdata[1]==1)
	{
		echo create_drop_down( "cbo_buyer_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Party--", $exdata[2], "",1);
	}
	else if($exdata[1]==2)
	{
		echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$exdata[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name", "id,buyer_name", 1, "--Select Party--", $exdata[2], "", 1);
	}
	exit();
}



if($action=="production_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	//echo $cbo_company_id;
	?>
	  <script>
		function js_set_value( str) 
		{
			$('#hidden_production_data').val( str );
			//alert(str);return;
			parent.emailwindow.hide();
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer PO');
			else if(val==5) $('#search_by_td').html('Buyer Style');
			else if(val==6) $('#search_by_td').html('Batch No');
		}
	  </script>
    </head>
    <body>
		<div align="center" style="width:100%;" >
             <fieldset>
                <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                    <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead> 
                        	<tr>
                                <th colspan="7"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
                            </tr>
                            <tr>              	 
                                <th width="150">Location</th>
                                <th width="100">Production ID</th>
                                <th width="100">Search By</th>
                            	<th width="100" id="search_by_td">Wash Job No</th>
                                <th width="130" colspan="2" class="must_entry_caption">Prod. Date Range</th>
                                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:90px;" /></th> 
                            </tr>          
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><? echo create_drop_down( "cbo_location", 150, "select id, location_name from lib_location where company_id='$cbo_company_id' and status_active =1 and is_deleted=0 order by location_name ASC","id,location_name", 1, "-Select Location-", 0, "","","","","","",3 ); ?></td>
                                <td><input name="txt_prod_no" id="txt_prod_no" class="text_boxes" style="width:90px"></td>
                                <td style="display: none;"><input name="txt_recipe_no" id="txt_recipe_no" class="text_boxes" style="width:90px"></td>
                                <td>
									<?
                                        $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style",6=>"Batch No");
                                        echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                    ?>
                                </td>
                                <td><input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" /></td>
                                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From Date" style="width:60px"></td>
                                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:60px"></td> 
                                <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="checkFields();showList();" style="width:90px;" />
                                     <input type="hidden" id="hidden_production_data" name="hidden_production_data" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                            </tr>
                        </tbody>
                    </table> 
                </form>
             </fieldset>   
             <div id="search_div" ></div>
		</div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
    	var isValidated = false;
    	function checkFields() {
    		var prodNo = document.getElementById('txt_prod_no').value;
    		var searchString = document.getElementById('txt_search_string').value;

    		if(prodNo == '' && searchString == '') {
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

    		show_list_view ( '<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('txt_prod_no').value+'_'+document.getElementById('txt_recipe_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_production_search_list_view', 'search_div', 'wash_qc_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
    		isValidated = false;
    	}
    </script>
    </html>
    <?
	exit();
}

if($action=="create_production_search_list_view")
{
	$data=explode('_',$data);
	/*echo "<pre>";
	print_r($data); die;*/
	$company_id=$data[0];
	$location_id=$data[1];
	$prod_no=$data[2];
	$recipe_no=$data[3];
	$date_from=$data[4];
	$date_to=$data[5];
	$search_type=$data[6];
	$search_by=str_replace("'","",$data[7]);
	$search_str=trim(str_replace("'","",$data[8]));
	
	//print_r($data);
	if($db_type==0)
	{
		$start_date= change_date_format($date_from,'yyyy-mm-dd');
		$end_date= change_date_format($date_to,'yyyy-mm-dd');
	}
	else
	{
		$start_date= change_date_format($date_from, "", "",1) ;
		$end_date= change_date_format($date_to, "", "",1);
	}
	
	$date_cond = "";
	if ($start_date != "" && $end_date != "") 
	{
		if ($db_type == 0) 
		{
			$date_cond ="and b.production_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'";
		} 
		else 
		{
			$date_cond="and b.production_date between '".change_date_format($start_date, "yyyy-mm-dd", "-", 1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-", 1)."'";
		}
	} 
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	$entry_form_cond='';
	if($main_batch_allow==1) $entry_form_cond=" and entry_form in (0,281) and process_id like '%35%'"; else $entry_form_cond="and entry_form =281 ";
	$batchIds='';
	
		if($search_str!="")
		{
			if ($search_by==6)
			{
			
			//echo "select id, batch_no, operation_type, sub_operation from pro_batch_create_mst where status_active=1 and is_deleted=0 and entry_form=316 and batch_no like '%$search_str%'"; die;
				$batchDataarrr=sql_select("select id, batch_no, operation_type, sub_operation from pro_batch_create_mst where status_active=1 and is_deleted=0 and  entry_form in (316,543) and batch_no like '%$search_str%'");
				foreach($batchDataarrr as $row)
				{
					$batch_arr_data[$row[csf('id')]].=$row[csf('id')].",";
				}
				$batch_id=chop(implode(",",$batch_arr_data),',');
			}
		}
		
	
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			//else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.buyer_po_no  like '%$search_str'";
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref = '$search_str' ";
			else if ($search_by==6) $batch_cond="and a.recipe_id in ($batch_id)";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			//else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
			else if ($search_by==4) $po_cond=" and b.buyer_po_no like '%$search_str'";
			//else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";  
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref like '%$search_str%'";  
			else if ($search_by==6) $batch_cond="and a.recipe_id in ($batch_id)"; 
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			//else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";
			else if ($search_by==4) $po_cond=" and b.buyer_po_no  like '%$search_str'";
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref like '$search_str%'"; 
			else if ($search_by==6) $batch_cond="and a.recipe_id in ($batch_id)"; 
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			//else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
			else if ($search_by==4) $po_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $style_cond=" and b.buyer_style_ref like '%$search_str'";
			else if ($search_by==6) $batch_cond="and a.recipe_id in ($batch_id)";  
		}
	}
	
	$po_ids=''; $buyer_po_arr=array();
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		//$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
		$po_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.subcon_job=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids."_test"; die;
	//if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	if ($po_ids!="") $po_idsCond=" and b.po_id in ($po_ids)"; else $po_idsCond="";
	
	$spo_ids='';
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.subcon_job=b.job_no_mst $search_com_cond", "id");
	}
	if ( $spo_ids!="") $spo_idsCond=" and b.po_id in ($spo_ids)"; else $spo_idsCond="";
	
	if($company_id==0) { echo "Select Company first"; die; }
	
	if($location_id !="0") $location_cond= "and a.location_id = $location_id"; else $location_cond= "";
	
	if($prod_no!="") $system_no_cond=" and a.sys_no like '%".trim($prod_no)."'"; else $system_no_cond="";
	
	if($recipe_no!="") $recipe_no_cond=" and a.sys_no like '%".trim($recipe_no)."%'"; else $recipe_no_cond="";
	//echo "sdlkjklsdj";
	//if($data[4]!="" && $data[5]!="") $date_cond=" and product_date between '".$date_from."' and '".$date_to."'"; else $date_cond="";
	$order_arr=array();
	$buyer_po_arr=array();
	$order_sql = sql_select("SELECT a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity as qty,b.buyer_style_ref,b.buyer_po_no from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('id')]]['qty']=$row[csf('qty')];
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
	}
	unset($order_sql);
	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	//$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	$batchData=sql_select("select id, batch_no, operation_type, sub_operation, color_id from pro_batch_create_mst where status_active=1 and is_deleted=0  and entry_form in (316,543) ");
	foreach($batchData as $row)
	{
		$batch_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
		$batch_arr[$row[csf('id')]]['sub_operation']=$row[csf('sub_operation')];
		$batch_arr[$row[csf('id')]]['operation_type']=$row[csf('operation_type')];
		$batch_arr[$row[csf('id')]]['color_id']=$color_arr[$row[csf('color_id')]];
	}

	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('mst_id')]]['production_date']=change_date_format($row[csf('production_date')]);
		$prod_data_arr[$row[csf('mst_id')]]['production_hour']=$row[csf('production_hour')];
		$prod_data_arr[$row[csf('mst_id')]]['operator_name']=$row[csf('operator_name')];
		$prod_data_arr[$row[csf('mst_id')]]['shift_id']=$row[csf('shift_id')];
	}

	$pdate_cond=($db_type==2)? " TO_CHAR(production_hour,'HH24:MI') " : " TIME_FORMAT( production_hour, '%H:%i' ) ";
	$prodData=sql_select("select id, mst_id, color_size_id, production_date, $pdate_cond as production_hour, operator_name, shift_id from subcon_embel_production_dtls where status_active=1 and is_deleted=0");
	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('mst_id')]]['production_date']=change_date_format($row[csf('production_date')]);
		$prod_data_arr[$row[csf('mst_id')]]['production_hour']=$row[csf('production_hour')];
		$prod_data_arr[$row[csf('mst_id')]]['operator_name']=$row[csf('operator_name')];
		$prod_data_arr[$row[csf('mst_id')]]['shift_id']=$row[csf('shift_id')];
	}
	
	/*$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	
	unset($po_sql_res);*/
	
	
	/*$buyer_po_arr=array();
	$po_sql ="select c.id,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,c.buyer_buyer,c.party_buyer_name
from subcon_ord_dtls c
where status_active=1 and is_deleted=0  ";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
	}
	
	unset($po_sql_res)*/
	
	
	/*$buyer_sql="select a.id,a.sys_no, a.subcon_date, a.chalan_no, a.embl_job_no, b.id as details_id ,b.quantity, b.job_dtls_id,b.remarks,c.gmts_item_id,c.gmts_color_id,c.order_no,c.buyer_po_no,c.buyer_style_ref,a.party_id,c.buyer_buyer,c.party_buyer_name
from sub_material_mst a, sub_material_dtls b,subcon_ord_dtls c,subcon_ord_mst d
where a. entry_form = 296 and a.trans_type=1 and a.id=b.mst_id and  b.job_dtls_id=c.id  and c.mst_id=d.id $company_name $search_com_cond  $within_group  $party_con $job_no_cond $subcon_date"; 
	
		$buyer_result = sql_select($buyer_sql);
        $buyer_data=array();
        foreach($buyer_result as $row)
        {
			
			$buyer_data[$row[csf('embl_job_no')]]['gmts_item_id'].=$garments_item[$row[csf('gmts_item_id')]].",";
			$buyer_data[$row[csf('embl_job_no')]]['gmts_color_id']=$row[csf('gmts_color_id')];
			$buyer_data[$row[csf('embl_job_no')]]['buyer_po_no'].=$row[csf('buyer_po_no')].",";;
			$buyer_data[$row[csf('embl_job_no')]]['buyer_style_ref'].=$row[csf('buyer_style_ref')].",";;
			$buyer_data[$row[csf('embl_job_no')]]['party_id'].=$party_arr[$row[csf('party_id')]].",";		        
			$buyer_data[$row[csf('embl_job_no')]]['buyer_buyer'].=$row[csf('party_buyer_name')].",";
        }*/
	?>
	<body>
		<div align="center">
			<fieldset style="width:1050px;margin-left:10px">
				<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
							<th width="60">Production No</th>
                            <th width="60">Prod. Date</th>
                            <th width="100">Batch No</th>
                            <th width="120">Gmts Color</th>
                            <th width="100">Operation</th>
                            <th width="80">Prod.Qty</th>
                            <th width="80">Rewash Qty.</th>
                            <th width="100">Job NO</th>
                            <th width="110">Order</th>
                            <th width="110">Buyer PO</th>
            				<th>Buyer Style</th>
						</thead>
					</table>
					<div style="width:1030px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table" id="tbl_list_search" >
							<?
							 $sql="SELECT a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, b.po_id as order_id, b.buyer_po_id, a.floor_id, a.machine_id, a.product_date,b.production_date,b.qcpass_qty,b.rewash_qty from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=301 and a.status_active = 1 and a.is_deleted = 0 and a.company_id='$company_id' $batch_cond $location_cond $system_no_cond $recipe_no_cond $spo_idsCond $po_idsCond $date_cond order by a.id DESC";// $date_cond
							//echo $sql; //die;
							$sql_res=sql_select($sql);

							$i=1;  $sub_operation=''; $batch_no='';  $operation_type='';
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$suboperation=array_unique(explode(",", $batch_arr[$row[csf('recipe_id')]]['sub_operation']));
								foreach ($suboperation as $sub)
								{
									$sub_operation .=$wash_sub_operation_arr[$sub];
								}
								$batch_no=$batch_arr[$row[csf('recipe_id')]]['batch_no'];
								$operation_type=$batch_arr[$row[csf('recipe_id')]]['operation_type'];
								$color_id=$batch_arr[$row[csf('recipe_id')]]['color_id'];
								$str_data="";
								$str_data=$row[csf('id')].'***'.$row[csf('sys_no')].'***'.$row[csf('location_id')].'***'.$row[csf('recipe_id')].'***'.$batch_no.'***'.$row[csf('job_no')].'***'.$row[csf('order_id')].'***'.$order_arr[$row[csf('order_id')]]['po'].'***'.$order_arr[$row[csf('order_id')]]['within_group'].'***'.$order_arr[$row[csf('order_id')]]['party_id'].'***'.$order_arr[$row[csf('order_id')]]['qty'].'***'.$prod_data_arr[$row[csf('id')]]['production_date'].'***'.$prod_data_arr[$row[csf('id')]]['production_hour'].'***'.$prod_data_arr[$row[csf('id')]]['operator_name'].'***'.$row[csf('buyer_po_id')].'***'.$buyer_po_arr[$row[csf("order_id")]]['po'].'***'.$buyer_po_arr[$row[csf("order_id")]]['style'].'***'.$prod_data_arr[$row[csf('id')]]['shift_id'].'***'.$row[csf('floor_id')].'***'.$row[csf('machine_id')].'***'.$operation_type.'***'.$sub_operation;
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $str_data; ?>')"> 
									<td width="30" align="center"><?php echo $i; ?></td>	
									<td width="60" align="center"><?php echo $row[csf('prefix_no_num')]; ?></td>
                                    <td width="60"><?php echo change_date_format($row[csf('production_date')]); ?>&nbsp;</td>
                                    <td width="100"><?php echo $batch_no; ?></td>
                                    <td width="120"><?php echo $color_id; ?></td>
                                    <td width="100"><?php echo $wash_operation_arr[$operation_type]; ?></td>
                                    <td width="80" align="right"><?php echo number_format($row[csf('qcpass_qty')],4); ?></td> 
                                    <td width="80" align="right"><?php echo number_format($row[csf('rewash_qty')],4); ?></td>
                                    <td width="100"><?php echo $row[csf('job_no')]; ?></td>
                                    <td width="110" style="word-break:break-all"><?php echo $order_arr[$row[csf('order_id')]]['po']; ?></td>
                                    <td width="110" style="word-break:break-all"><?php echo $buyer_po_arr[$row[csf("order_id")]]['po']; ?>&nbsp;</td>
                                    <td style="word-break:break-all"><?php echo $buyer_po_arr[$row[csf("order_id")]]['style']; ?>&nbsp;</td>
								</tr>
								<?
								$i++;
							}
							?>
						</table>
					</div>
				</form>
			</fieldset>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="production_popup_backup")
{
	echo load_html_head_contents("Production Pop-up","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	//echo $data.'=='.$cbo_company_id;
	?>
	<script>
		function js_set_value(str)
		{ 
			$("#selected_str_data").val(str);
			parent.emailwindow.hide();
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="680" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140">Production</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_by_td">Buyer Po</th>
                    <th width="60">Job Year</th>
                    <th width="130" colspan="2">Receive Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /> </th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_str_data">
                        <input type="text" name="txt_search_production" id="txt_search_production" class="text_boxes" style="width:90px" placeholder="Search Production" />
                    </td>
                    <td>
						<?
                            $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",4,'search_by(this.value)',"","" );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                    <td align="center"><? echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center"><input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From" style="width:60px"></td>
                    <td align="center"><input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To" style="width:60px"></td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_id; ?>'+'_'+document.getElementById('txt_search_production').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value, 'create_production_search_list_view', 'search_div', 'wash_qc_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr class="general">
                        <td colspan="7" align="center" valign="middle"><? echo load_month_buttons(); ?></td>
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
if($action=="create_production_search_list_view_backup")
{
	$exdata=explode('_',$data);
	$cbo_company_id=$exdata[0];
	$search_production=$exdata[1];
	$form_date=$exdata[2];
	$to_date=$exdata[3];
	
	$search_by=$exdata[4];
	$search_str=trim($exdata[5]);
	$search_type =$exdata[6];
	$year =$exdata[7];
	$within_group=$exdata[8];
	
	if($cbo_company_id!=0) $company=" and a.company_id='$cbo_company_id'"; else { echo "Please Select Company First."; die; }
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and b.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'";   
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'"; 
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'"; 
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";   
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";  
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
		}
	}

	if($search_production!='') $search_production_cond=" and e.prefix_no_num='$search_production'"; else $search_production_cond="";
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4)|| ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";

	if($db_type==0)
	{ 
		if ($form_date!="" &&  $to_date!="") $order_rcv_date = "and a.receive_date between '".change_date_format($form_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $order_rcv_date ="";
		
		$year_select="YEAR(a.insert_date)";
		$year_cond=" and YEAR(a.insert_date)=$year";
	}
	else
	{
		if ($form_date!="" &&  $to_date!="") $order_rcv_date = "and a.receive_date between '".change_date_format($form_date, "", "",1)."' and '".change_date_format($to_date, "", "",1)."'"; else $order_rcv_date ="";
		$year_select="TO_CHAR(a.insert_date,'YYYY')";
		$year_cond=" and to_char(a.insert_date,'YYYY')=$year";
	}
	
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	$color_arr=return_library_array( "select id,color_name from lib_color where", "id", "color_name" );
	
	$production_qty_arr=array();
	$prod_data_arr="select a.id, sum(b.qcpass_qty) as qty from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=301 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id";
	$prod_data_res=sql_select($prod_data_arr);
	
	foreach($prod_data_res as $row)
	{
		$production_qty_arr[$row[csf('id')]]=$row[csf('qty')];
	}
	unset($prod_data_res);
	
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	?>
    <body>
		<div align="center">
			<fieldset style="width:1070px;">
				<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1070" class="rpt_table" align="center">
						<thead>
 							<th width="30">SL</th>
							<th width="60">Production</th>
                            <th width="60">Job</th>
                            <th width="90">Order</th>
                            <th width="100">Buyer Po</th>
            				<th width="100">Buyer Style</th>
                            <th width="90">Gmts. Item</th>
                             <th width="80">Process Name</th>
                            <th width="80">Wash Type</th>
                            <th width="90">Color</th>
                            <th width="70">Order Qty</th>
                            <th width="70">Prod Qty</th>
                            <th>Balance Qty</th>
						</thead>
					</table>
					<div style="width:1070px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" id="list_view">
							<?
							// $sql= "select  a.id, a.job_no_prefix_num, a.subcon_job, a.party_id, a.within_group, b.id as order_id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, sum(c.qnty) as qty, e.id as production_id, e.prefix_no_num, e.sys_no from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d, subcon_embel_production_mst e where a.subcon_job=b.job_no_mst and b.id=c.mst_id  and d.id=e.recipe_id and a.entry_form=295 and d.entry_form=300 and e.entry_form=301 and a.status_active=1 $order_rcv_date $company $search_com_cond $search_production_cond $po_idsCond group by a.job_no_prefix_num, a.subcon_job, a.insert_date, a.party_id, a.id, a.within_group, b.id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, e.id, e.prefix_no_num, e.sys_no order by e.id DESC";
							 
							 
							  $sql="SELECT a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, b.po_id as order_id, b.buyer_po_id, a.floor_id, a.machine_id, a.product_date,b.production_date,b.qcpass_qty,b.rewash_qty from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=301 and a.status_active = 1 and a.is_deleted = 0 and $company $batch_cond $location_cond $system_no_cond $recipe_no_cond $spo_idsCond $po_idsCond $date_cond order by a.id DESC";
							
							
							
							//echo $sql; die;
							$sql_res=sql_select($sql);

							$i=1; 
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								if($row[csf('main_process_id')]==1) $new_subprocess_array= $emblishment_print_type;
								else if($row[csf('main_process_id')]==2) $new_subprocess_array= $emblishment_embroy_type;
								else if($row[csf('main_process_id')]==3) $new_subprocess_array= $emblishment_wash_type;
								else if($row[csf('main_process_id')]==4) $new_subprocess_array= $emblishment_spwork_type;
								else if($row[csf('main_process_id')]==5) $new_subprocess_array= $emblishment_gmts_type;
								else $new_subprocess_array=$blank_array;
								
								$str="";
								$str=$row[csf('production_id')].'___'.$row[csf('sys_no')].'___'.$row[csf('subcon_job')].'___'.$row[csf('order_id')].'___'.$row[csf('order_no')].'___'.$row[csf('party_id')].'___'.$row[csf('within_group')].'___'.$row[csf('qty')].'___'.$row[csf('buyer_po_id')].'___'.$buyer_po_arr[$row[csf("buyer_po_id")]]['po'].'___'.$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
								
								$prod_qty=0; $balance_qty=0;
								$prod_qty=$production_qty_arr[$row[csf('production_id')]];
								$balance_qty=$row[csf('qty')]-$prod_qty;
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
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $str;?>')"> 
									<td width="30" align="center"><?php echo $i; ?></td>	
									<td width="60" align="center"><?php echo $row[csf('prefix_no_num')]; ?></td>
                                    <td width="60" align="center"><?php echo $row[csf('job_no_prefix_num')]; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $row[csf('order_no')]; ?></td>
                                    <td width="100" style="word-break:break-all"><p><? echo $buyer_po; ?></p></td>
                					<td width="100" style="word-break:break-all"><p><? echo $buyer_style; ?></p></td>
                                    <td width="90" style="word-break:break-all"><?php echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                                     <td width="80" style="word-break:break-all"><?php echo $emblishment_name_array[$row[csf('main_process_id')]]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $new_subprocess_array[$row[csf('embl_type')]]; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $color_arr[$row[csf('color_id')]]; ?></td>
                                    <td width="70" align="right"><?php echo number_format($row[csf('qty')],2); ?></td>
                                    <td width="70" align="right"><?php if($prod_qty>0) {echo number_format($prod_qty,2);} else {echo "";} ?></td>
                                    <td align="right"><?php if($balance_qty!=0) {echo number_format($balance_qty,2);} else {echo "";} ?></td>
								</tr>
								<?
								$i++;
							}
							?>
						</table>
					</div>
				</form>
			</fieldset>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();	
}

if($action=="order_details")
{
	
 	$data=explode("***",$data);
 	$company_id=$data[0];
	$production_id=$data[1];
	$update_id=$data[2];

	$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	
	$prod_data_arr=array(); //$recipe_prod_id_arr=array(); $product_data_arr=array();
	if($production_id!=0)
	{	
		$prodData=sql_select("select b.id, b.color_size_id, b.qcpass_qty, b.operator_name, b.shift_id from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id='$production_id' and a.entry_form=301 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($prodData as $row)
		{
			$prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty']=$row[csf('qcpass_qty')];
			$prod_data_arr[$row[csf('color_size_id')]]['shift_id']=$row[csf('shift_id')];
		}
		unset($prodData);
	}
	
	$qc_data_arr=array(); $prev_qc_arr=array(); //$product_data_arr=array();
	//if($update_id!=0)
	//{	
		$qcData=sql_select("select b.id, b.mst_id, b.color_size_id, b.qcpass_qty, b.reje_qty, b.shift_id, b.remarks from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.recipe_id='$production_id' and a.id=b.mst_id and a.entry_form=302 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach($qcData as $row)
		{
			if($row[csf('mst_id')]==$update_id)
			{
				$qc_data_arr[$row[csf('color_size_id')]]['qcpass_qty']=$row[csf('qcpass_qty')];
				$qc_data_arr[$row[csf('color_size_id')]]['reje_qty']=$row[csf('reje_qty')];
				$qc_data_arr[$row[csf('color_size_id')]]['id']=$row[csf('id')];
				$qc_data_arr[$row[csf('color_size_id')]]['shift_id']=$row[csf('shift_id')];
				$qc_data_arr[$row[csf('color_size_id')]]['remarks']=$row[csf('remarks')];
			}
			else
			{
				$prev_qc_arr[$row[csf('color_size_id')]]['qcpass_qty']+=$row[csf('qcpass_qty')];
				$prev_qc_arr[$row[csf('color_size_id')]]['reje_qty']+=$row[csf('reje_qty')];
			}
		}
		unset($qcData);
	//}
	//print_r($prev_qc_arr);

	/*  $sql= "select  a.id, a.job_no_prefix_num, a.subcon_job, a.party_id, a.within_group, b.id as order_id, b.order_no, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id as color_size_id, c.color_id, c.size_id, sum(c.qnty) as qty, d.id as recipe_id, d.recipe_no_prefix_num, d.recipe_no from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d, subcon_embel_production_mst e where a.subcon_job=b.job_no_mst and b.id=c.mst_id and a.subcon_job=d.job_no and b.id=d.po_id and d.id=e.recipe_id 
	and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and c.body_part=d.body_part and c.color_id=d.color_id 
	and a.entry_form=295 and d.entry_form=300 and e.entry_form=301 and a.status_active=1 and a.company_id='$company_id' and e.id='$production_id' group by a.id, a.job_no_prefix_num, a.subcon_job, a.insert_date, a.party_id, a.within_group, b.id, b.order_no, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id, c.color_id, c.size_id, d.id, d.recipe_no_prefix_num, d.recipe_no order by d.recipe_no_prefix_num, c.id ASC";*/
	
	 
  $sql="SELECT a.color_id,   a.operation_type,e.recipe_no,e.id as recipe_id, e.recipe_no_prefix_num,f.order_no, f.gmts_item_id, f.embl_type, a.process_id as main_process_id,  a.within_group,b.id, b.po_id AS order_id,b.prod_id,b.roll_no AS batch_qnty,  b.buyer_po_id,d.qcpass_qty  AS qty
  FROM pro_batch_create_mst          a,
       pro_batch_create_dtls         b,
       subcon_embel_production_mst   c,
       subcon_embel_production_dtls  d,
        pro_recipe_entry_mst          e,
		subcon_ord_dtls f
 WHERE     a.entry_form IN (316, 543)
       AND c.entry_form = 301
       AND a.id = b.mst_id
       AND c.id = d.mst_id 
       and b.po_id=d.po_id 
	   and f.id=d.po_id 
	   and b.po_id=f.id
       AND b.status_active = 1
       AND b.is_deleted = 0 
       AND c.status_active = 1
       AND c.is_deleted = 0
       AND d.status_active = 1
       AND d.is_deleted = 0 
       and a.id=e.batch_id 
        AND a.id = c.recipe_id and c.id=$production_id 
           order by b.id";
	
	$sql_res=sql_select($sql);

	$i=1; 
	foreach($sql_res as $row)
	{
		if($row[csf('main_process_id')]==1) $new_subprocess_array= $emblishment_print_type;
		else if($row[csf('main_process_id')]==2) $new_subprocess_array= $emblishment_embroy_type;
		else if($row[csf('main_process_id')]==3) $new_subprocess_array= $emblishment_wash_type;
		else if($row[csf('main_process_id')]==4) $new_subprocess_array= $emblishment_spwork_type;
		else if($row[csf('main_process_id')]==5) $new_subprocess_array= $emblishment_gmts_type;
		else $new_subprocess_array=$blank_array;
		
		$production_qty=0; $qc_qty=0; $rej_qty=0; $prev_qc_qty=0; $prev_rej_qty=0; $remarks='';
		$production_qty=$prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty'];
		
		$prev_qc_qty=$prev_qc_arr[$row[csf('color_size_id')]]['qcpass_qty'];
		$prev_rej_qty=$prev_qc_arr[$row[csf('color_size_id')]]['reje_qty'];
		
		if($update_id!=0)
		{
			$upid=$qc_data_arr[$row[csf('color_size_id')]]['id'];
			$qc_qty=$qc_data_arr[$row[csf('color_size_id')]]['qcpass_qty'];
			$rej_qty=$qc_data_arr[$row[csf('color_size_id')]]['reje_qty'];
			$remarks=$qc_data_arr[$row[csf('color_size_id')]]['remarks'];
			$bal=$production_qty-($prev_qc_qty);
		}
		else 
		{
			$qc_qty='';//$production_qty-($prev_qc_qty);
			$bal=$production_qty-($prev_qc_qty);
		}
		
		
		//$shift_id=$prod_data_arr[$row[csf('color_size_id')]]['shift_id'];
		//if($update_id!=0) $qc_qty=$qc_qty; else $qc_qty=$production_qty-($qc_qty);
		
		//if($qc_qty==0) $qc_qty=$production_qty;
		
		?>
		<tr class="general" name="tr[]" id="tr_<? echo $i;?>">
			<td><input type="text" name="txtSl[]" id="txtSl_<? echo $i;?>" class="text_boxes_numeric" style="width:20px" disabled value="<? echo $i; ?>" /></td>
			<td>
				<input type="text" name="txtRecipeNo[]" id="txtRecipeNo_<? echo $i;?>" class="text_boxes" style="width:90px" placeholder="Display"disabled value="<? echo $row[csf('recipe_no')]; ?>" />
				<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $i;?>" style="width:50px" value="<? echo $upid; ?>" />
				<input type="hidden" name="colorSizeId[]" id="colorSizeId_<? echo $i;?>" style="width:50px" value="<? echo $row[csf('color_size_id')]; ?>" />
			</td>
			<td>
				<input type="text" name="txtGmtsItem[]" id="txtGmtsItem_<? echo $i;?>" class="text_boxes" style="width:80px" placeholder="Display"disabled value="<? echo $garments_item[$row[csf('gmts_item_id')]]; ?>" />
				<input type="hidden" name="txtGmtsItemId[]" id="txtGmtsItemId_<? echo $i;?>" style="width:50px" value="<? echo $row[csf('gmts_item_id')]; ?>" />
			</td> 
			<td>
				<input type="text" name="txtEmblName[]" d="txtEmblName_<? echo $i;?>" class="text_boxes" style="width:90px" placeholder="Display" disabled value="<? echo $emblishment_name_array[$row[csf('main_process_id')]]; ?>" />
				<input type="hidden" name="txtEmblNameId[]" id="txtEmblNameId_<? echo $i;?>" value="<? echo $row[csf('main_process_id')]; ?>" />
			</td>
			<td>
				<input type="text" name="txtEmblType[]" id="txtEmblType_<? echo $i;?>" class="text_boxes" style="width:90px" placeholder="Display" disabled value="<? echo $new_subprocess_array[$row[csf('embl_type')]]; ?>"/>
				<input type="hidden" name="txtEmblTypeId[]" id="txtEmblTypeId_<? echo $i;?>" value="<? echo $row[csf('embl_type')]; ?>" />                        
			</td>
			<td>
				<input type="text" name="txtColor[]" id="txtColor_<? echo $i;?>" class="text_boxes"  style="width:70px" placeholder="Display" disabled value="<? echo $color_arr[$row[csf('color_id')]]; ?>"/>
				<input type="hidden" name="txtColorId[]" id="txtColorId_<? echo $i;?>" value="<? echo $row[csf('color_id')]; ?>" />
			</td>
			<td>
				<input type="text" name="txtSize[]" id="txtSize_<? echo $i;?>" class="text_boxes"  style="width:60px" placeholder="Display" disabled value="<? echo $size_arr[$row[csf('size_id')]]; ?>"/>
				<input type="hidden" name="txtSizeId[]" id="txtSizeId_<? echo $i;?>" value="<? echo $row[csf('size_id')]; ?>" />
			</td>
			<td>
				<input type="text" name="txtProdQty[]" id="txtProdQty_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" placeholder="Dispaly" value="<? echo $production_qty; ?>" disabled />
			</td>
            <td>
                <input type="text" name="txtRejQty[]" id="txtRejQty_<? echo $i;?>" class="text_boxes_numeric" style="width:55px" placeholder="Write" value="<? echo $rej_qty; ?>" onBlur="fnc_total_calculate();" /><!--onBlur="fnc_calculate_qcqty(<? //echo $i;?>);"-->
            </td>
            <td>
                <input type="text" name="txtQcQty[]" id="txtQcQty_<? echo $i;?>" class="text_boxes_numeric" style="width:50px" value="<? echo $qc_qty; ?>" placeholder="<? echo $prev_qc_qty; ?>" onBlur="fnc_calculate_qcqty(<? echo $i; ?>); fnc_total_calculate();"/>
            </td>
            <td><input type="text" name="txtremarks[]" id="txtremarks_<? echo $i; ?>" style="width:40px" value="<? echo $remarks; ?>" class="text_boxes" placeholder="Remark" onClick="openmypage_remarks(<? echo $i; ?>);" /></td>
		</tr>
		<?
		$i++;
	}
	exit();
}

/*/Search Saved data/*/
if($action=="embel_qc_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	//echo $cbo_company_id;
	?>
	  <script>
		function js_set_value( str) 
		{
			$('#hidden_qc_data').val( str );
			//alert(str);return;
			parent.emailwindow.hide();
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}
	  </script>
    </head>
    <body>
		<div align="center" style="width:100%;" >
             <fieldset>
                <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                    <table width="" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead> 
                        	<tr>
                                <th colspan="8"><? echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
                            </tr>
                            <tr>              	 
                                <th width="150">Location</th>
                                <th width="100">Production ID</th>
                                <th width="100">QC ID</th>
                                <th width="100">Search By</th>
                            	<th width="100" id="search_by_td">Wash Job No</th>
                                <th width="160" colspan="2">QC Date Range</th>
                                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:90px;" /></th> 
                            </tr>          
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><? echo create_drop_down( "cbo_location", 150, "select id, location_name from lib_location where company_id='$cbo_company_id' and status_active =1 and is_deleted=0 order by location_name ASC","id,location_name", 1, "-Select Location-", 0, "","","","","","",3 ); ?></td>
                                <td><input name="txt_prod_no" id="txt_prod_no" class="text_boxes" style="width:90px"></td>
                                <td><input name="txt_qc_no" id="txt_qc_no" class="text_boxes" style="width:90px"></td>
                                <td>
									<?
                                        $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                                        echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                    ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:90px" placeholder="" />
                                </td>
                                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From Date" style="width:70px"></td>
                                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:70px"></td> 
                                <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<? echo $cbo_company_id; ?>'+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('txt_prod_no').value+'_'+document.getElementById('txt_qc_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_qc_no_list_view', 'search_div', 'wash_qc_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:90px;" />
                                    
                                     <input type="hidden" id="hidden_qc_data" name="hidden_qc_data" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="8" align="center" valign="middle">
                                    <? echo load_month_buttons(1);  ?>
                                </td>
                            </tr>
                        </tbody>
                    </table> 
                </form>
             </fieldset>   
             <div id="search_div" ></div>
		</div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_qc_no_list_view")
{
	$data=explode('_',$data);
	$company_id=$data[0];
	$location_id=$data[1];
	$prod_no=$data[2];
	$qc_no=$data[3];
	$date_from=$data[4];
	$date_to=$data[5];
	$search_type=$data[6];
	$search_by = $data[7];
	$search_str = trim($data[8]);
	
	if($db_type==0)
	{
		$date_from= change_date_format($date_from,'yyyy-mm-dd');
		$date_to= change_date_format($date_to,'yyyy-mm-dd');
	}
	else
	{
		$date_from= change_date_format($date_from, "", "",1) ;
		$date_to= change_date_format($date_to, "", "",1);
	}
	
	if($company_id==0) { echo "Select Company first"; die; }
	
	if($location_id !="0") $location_cond= "and a.location_id = $location_id"; else $location_cond= "";
	if($prod_no!="") $system_no_cond=" and a.sys_no like '%".trim($prod_no)."%'"; else $system_no_cond="";
	if($qc_no!="") $qc_no_cond=" and a.prefix_no_num='".trim($qc_no)."'"; else $qc_no_cond="";
	//echo "sdlkjklsdj";
	//if($data[4]!="" && $data[5]!="") $date_cond=" and product_date between '".$date_from."' and '".$date_to."'"; else $date_cond="";
	
	$order_arr=array(); $colorid_arr=array();
	$order_sql = sql_select("select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, c.id as color_zise_id, c.color_id as color_id, c.qnty as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.mst_id and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('id')]]['qty']+=$row[csf('qty')];
		$colorid_arr[$row[csf('color_zise_id')]]['color_id']=$row[csf('color_id')];
	}
	unset($order_sql);
	
	$prodno_arr = return_library_array("select id, sys_no from subcon_embel_production_mst where entry_form=301", 'id', 'sys_no');
	$prodData=sql_select("select id, mst_id, color_size_id, production_date, operator_name, shift_id from subcon_embel_production_dtls where status_active=1 and is_deleted=0");
	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('mst_id')]]['production_date']=change_date_format($row[csf('production_date')]);
		$prod_data_arr[$row[csf('mst_id')]]['operator_name']=$row[csf('operator_name')];
		$prod_data_arr[$row[csf('mst_id')]]['shift_id']=$row[csf('shift_id')];
	}
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and c.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and c.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";   
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and c.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'"; 
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";   
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and b.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and c.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";  
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
		}
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4)|| ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	
	if ($po_ids!="") $po_idsCond=" and a.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	$color_name_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	?>
	<body>
		<div align="center">
			<fieldset style="width:820px;margin-left:10px">
				<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
							<th width="60">QC No</th>
                            <th width="110">Production No</th>
                            <th width="110">Job NO</th>
                            <th width="100">Order</th>
                            <th width="110">Color</th>
                            <th width="100">Buyer Po</th>
                            <th width="100">Buyer Style</th>
                            <th>QC Qty</th>
						</thead>
					</table>
					<div style="width:820px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search" >
							<?
							if($db_type==0) $color_size_id_str="group_concat(b.color_size_id)";
							else if($db_type==2) $color_size_id_str="listagg(b.color_size_id,',') within group (order by b.color_size_id)";
							
							$sql="select a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, a.order_id, a.buyer_po_id, $color_size_id_str as color_size_id, sum(b.qcpass_qty) as qc_qty from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=302 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$company_id' $location_cond $system_no_cond $qc_no_cond $po_idsCond group by a.id, a.prefix_no_num, a.sys_no, a.location_id, a.recipe_id, a.job_no, a.order_id, a.buyer_po_id order by a.id DESC";// $date_cond
							//echo $sql; die;
							$sql_res=sql_select($sql);

							$i=1; 
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$str_data="";
								
								$str_data=$row[csf('id')].'***'.$row[csf('sys_no')].'***'.$row[csf('location_id')].'***'.$row[csf('recipe_id')].'***'.$prodno_arr[$row[csf('recipe_id')]].'***'.$row[csf('job_no')].'***'.$row[csf('order_id')].'***'.$order_arr[$row[csf('order_id')]]['po'].'***'.$order_arr[$row[csf('order_id')]]['within_group'].'***'.$order_arr[$row[csf('order_id')]]['party_id'].'***'.$order_arr[$row[csf('order_id')]]['qty'].'***'.$prod_data_arr[$row[csf('id')]]['production_date'].'***'.$prod_data_arr[$row[csf('id')]]['operator_name'].'***'.$row[csf('buyer_po_id')].'***'.$buyer_po_arr[$row[csf("buyer_po_id")]]['po'].'***'.$buyer_po_arr[$row[csf("buyer_po_id")]]['style'].'***'.$prod_data_arr[$row[csf('id')]]['shift_id'];
								
								$colorsize_id_ex=explode(",",$row[csf('color_size_id')]);
								$color_name_str="";
								foreach($colorsize_id_ex as $breakdown_id)
								{
									if(	$color_name_str=="") $color_name_str=$color_name_arr[$colorid_arr[$breakdown_id]['color_id']]; else $color_name_str.=','.$color_name_arr[$colorid_arr[$breakdown_id]['color_id']];
								}
								
								$color_name_str=implode(",",array_filter(array_unique(explode(",",$color_name_str))));
								
								//echo $color_name_str;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $str_data; ?>')"> 
									<td width="30" align="center"><?php echo $i; ?></td>	
									<td width="60" align="center"><?php echo $row[csf('prefix_no_num')]; ?></td>
                                    <td width="110" style="word-break:break-all"><?php echo $prodno_arr[$row[csf('recipe_id')]]; ?></td>
                                    <td width="110" style="word-break:break-all"><?php echo $row[csf('job_no')]; ?></td>
                                    <td width="100" style="word-break:break-all"><?php echo $order_arr[$row[csf('order_id')]]['po']; ?></td>
                                    
                                    <td width="110" style="word-break:break-all"><?php echo $color_name_str; ?></td>
                                    <td width="100" style="word-break:break-all"><?php echo $buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?></td>
                                    <td width="100" style="word-break:break-all"><?php echo $buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?></td>
                                    <td align="right"><?php echo $row[csf('qc_qty')]; ?></td>
								</tr>
								<?
								$i++;
							}
							?>
						</table>
					</div>
				</form>
			</fieldset>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)   // Insert Here==============================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $year_cond=" and YEAR(insert_date)";	
		else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		
		$new_return_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'WQC', date("Y",time()), 5, "select id,prefix_no,prefix_no_num from  subcon_embel_production_mst where company_id=$cbo_company_id and entry_form=302 $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		$id=return_next_id( "id", "subcon_embel_production_mst", 1 ) ; 
		$field_array="id, prefix_no, prefix_no_num, sys_no, company_id, location_id, recipe_id, job_no, order_id, buyer_po_id, inserted_by, insert_date, status_active, is_deleted, entry_form";
		$data_array="(".$id.",'".$new_return_no[1]."','".$new_return_no[2]."','".$new_return_no[0]."',".$cbo_company_id.",".$cbo_location.",".$txt_production_id.",".$txt_job_no.",".$txt_order_id.",".$txtbuyerPoId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,302)"; 
		
		$production_no=$new_return_no[0];
		
		$data_array_dtls=""; $issave=1;
		$id_dtls=return_next_id( "id", "subcon_embel_production_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, color_size_id, po_id, job_no, production_date, reje_qty, qcpass_qty, operator_name, shift_id, remarks, inserted_by, insert_date, status_active, is_deleted";
		//echo "10**"; //die; 
		for($i=1;$i<=$total_row;$i++)
		{
			$colorSizeId="colorSizeId_".$i; 
			$txtRejQty="txtRejQty_".$i;
			$txtQcQty="txtQcQty_".$i;
			//$cboShift="cboShift_".$i;
			$txtremarks="txtremarks_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$id_dtls.",".$id.",'".$$colorSizeId."',".$txt_order_id.",".$txt_job_no.",".$txt_prod_date.",'".$$txtRejQty."','".$$txtQcQty."',".$txt_super_visor.",".$cboShift.",'".$$txtremarks."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',1,0)"; 
				
			$id_dtls=$id_dtls+1;
		}
		//die;
		//echo "10**insert into subcon_embel_production_mst ($field_array) values $data_array "; die;
		//echo "10**insert into subcon_embel_production_dtls ($field_array_dtls) values $data_array_dtls "; die;
		$flag=1;
		$rID=sql_insert("subcon_embel_production_mst",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_insert("subcon_embel_production_dtls",$field_array_dtls,$data_array_dtls,0);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**".$rID."**".$rID1; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".$production_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".$production_no;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".$production_no;
			}
		}	
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here============================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array_update="location_id*recipe_id*job_no*order_id*buyer_po_id*updated_by*update_date";
		$data_array_update="".$cbo_location."*".$txt_production_id."*".$txt_job_no."*".$txt_order_id."*".$txtbuyerPoId."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 		
		
		$data_array_dtls_update="";
		$field_array_dtls_update="color_size_id*po_id*job_no*production_date*reje_qty*qcpass_qty*operator_name*shift_id*remarks*updated_by*update_date";
		
		$production_no=str_replace("'","",$txt_qc_id);
		
		$data_array_dtls="";
		$id_dtls=return_next_id( "id", "subcon_embel_production_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, color_size_id, po_id, job_no, production_date, reje_qty, qcpass_qty, operator_name, shift_id, remarks, inserted_by, insert_date, status_active, is_deleted";
		$issave=1;
		for($i=1;$i<=$total_row;$i++)
		{
			$colorSizeId="colorSizeId_".$i; 
			$txtRejQty="txtRejQty_".$i;
			$txtQcQty="txtQcQty_".$i;
			//$cboShift="cboShift_".$i;
			$txtremarks="txtremarks_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			
			$updateIds = str_replace("'","",$$updateIdDtls);
			
			if( $updateIds != "")
			{
				$updateIdDtls_array[]=$updateIds;
				//if($data_array_dtls_update != "") $data_array_dtls_update .= ","; 	
				$data_array_dtls_update[$updateIds] = explode("*",("'".$$colorSizeId."'*".$txt_order_id."*".$txt_job_no."*".$txt_prod_date."*'".$$txtRejQty."'*'".$$txtQcQty."'*".$txt_super_visor."*".$cboShift."*'".$$txtremarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
			}else{
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$id.",'".$$colorSizeId."',".$txt_order_id.",".$txt_job_no.",".$txt_prod_date.",'".$$txtRejQty."','".$$txtQcQty."',".$txt_super_visor.",".$cboShift.",'".$$txtremarks."','".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',1,0)"; 
					
				$id_dtls=$id_dtls+1;
			}
		}
		//echo "10**".bulk_update_sql_statement("subcon_embel_production_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array); die;
		//echo "10**insert into subcon_embel_production_dtls ($field_array_dtls) values $data_array_dtls "; die;
		
		$flag=1;
		$rID=sql_update("subcon_embel_production_mst",$field_array_update,$data_array_update,"id",$update_id,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		if($data_array_dtls !=""){
			$rID1=sql_insert("subcon_embel_production_dtls",$field_array_dtls,$data_array_dtls,0);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if($data_array_dtls_update !=""){
			$rID2=execute_query(bulk_update_sql_statement( "subcon_embel_production_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array ));
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**".$rID."**".$rID1."**".$rID2;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".$production_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id)."**".$production_no;
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".$production_no;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id)."**".$production_no;
			}
		}	
		disconnect($con);
 		die;
	}
	else if ($operation==2)   // Delete Here ============================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$flag=1;
		
		$rID=sql_delete("subcon_embel_production_mst",$field_array,$data_array,"id","".$update_id."",1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_delete("subcon_embel_production_dtls",$field_array,$data_array,"mst_id","".$update_id."",1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$rID."**".$rID1; die;	
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2)
		{
			if ($flag==1) {
                oci_commit($con);
                echo "2**" .str_replace("'",'',$update_id);
            } else {
                oci_rollback($con);
                echo "10**" .str_replace("'",'',$update_id);
            }
		}
		disconnect($con);
		die;
	}
}

if($action=="embl_production_entry_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$recipe_arr = return_library_array("select id, recipe_no from pro_recipe_entry_mst", 'id', 'recipe_no');
	
	$order_arr=array();
	$order_sql = sql_select("select a.subcon_job, a.order_no, a.within_group, a.party_id, b.main_process_id, sum(c.qnty) as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.mst_id and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.subcon_job, a.order_no, a.within_group, a.party_id, b.main_process_id");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('subcon_job')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('subcon_job')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('subcon_job')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('subcon_job')]]['qty']=$row[csf('qty')];
		$order_arr[$row[csf('subcon_job')]]['embl_name']=$row[csf('main_process_id')];
	}
	unset($order_sql);
	
	/*$cust_arr=array();
	$cust_buyer_style_array=sql_select("SELECT id, subcon_job, order_id, order_no from subcon_ord_mst where entry_form=295 and status_active=1 and is_deleted=0");
	foreach ($cust_buyer_style_array as $cust_val) 
	{
		$cust_arr[$cust_val[csf('subcon_job')]]['order_no']=$cust_val[csf('order_no')]; 
		$cust_arr[$cust_val[csf('subcon_job')]]['job']=$cust_val[csf('subcon_job')]; 
	}
	unset($cust_buyer_style_array);*/
$sql="select id, prefix_no_num, sys_no, location_id, recipe_id, job_no, order_id from subcon_embel_production_mst where entry_form=301 and status_active = 1 and is_deleted = 0 and company_id='$company_id' $location_cond $system_no_cond $recipe_no_cond order by id DESC";

	$recipe_arr = return_library_array("select id, recipe_no from pro_recipe_entry_mst", 'id', 'recipe_no');
	$pdate_cond=($db_type==2)? " TO_CHAR(production_hour,'HH24:MI') " : " TIME_FORMAT( production_hour, '%H:%i' ) ";
	$prodData=sql_select("select id, mst_id, color_size_id, production_date, $pdate_cond as production_hour, operator_name from subcon_embel_production_dtls where status_active=1 and is_deleted=0 and mst_id='$data[1]'");
	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('mst_id')]]['production_date']=change_date_format($row[csf('production_date')]);
		$prod_data_arr[$row[csf('mst_id')]]['production_hour']=$row[csf('production_hour')];
		$prod_data_arr[$row[csf('mst_id')]]['operator_name']=$row[csf('operator_name')];
	}
	unset($prodData);
	
	$prodData=sql_select("select id, color_size_id, production_date, production_hour, qcpass_qty, operator_name, shift_id from subcon_embel_production_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0");
	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('color_size_id')]]['id']=$row[csf('id')];
		$prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty']=$row[csf('qcpass_qty')];
		$prod_data_arr[$row[csf('color_size_id')]]['shift_id']=$row[csf('shift_id')];
	}
	
	unset($prodData);
	
	$sql_mst = "select id, prefix_no_num, sys_no, location_id, recipe_id, job_no, order_id from subcon_embel_production_mst where entry_form=301 and id='$data[1]'";
	$dataArray = sql_select($sql_mst); $party_name="";
	if(  $order_arr[$dataArray[0][csf('job_no')]]['within_group'] ==1) $party_name=$company_library[$order_arr[$dataArray[0][csf('job_no')]]['party_id']];
	else if($order_arr[$dataArray[0][csf('job_no')]]['within_group']==2) $party_name=$buyer_library[$order_arr[$dataArray[0][csf('job_no')]]['party_id']];
	
	?>
    <div style="width:930px; font-size:6px">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="70" align="right"> 
                    <img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
                </td>
                <td>
                    <table width="800" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                        </tr>
                        <tr class="form_caption">
                            <td  align="center" style="font-size:14px">  
                                <? echo show_company($data[0],'',''); ?> 
                            </td>  
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong><? echo $data[2]; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table width="100%" cellpadding="0" cellspacing="0" >  
            <tr>
                <td width="130"><strong>Production ID:</strong></td>
                <td width="175"><? echo $dataArray[0][csf('sys_no')]; ?></td>
                <td width="130"><strong>Party Name: </strong></td>
                <td width="175px"><? echo $party_name; ?></td>
                <td width="130"><strong>Production Date: </strong></td>
                <td width="175"><? echo change_date_format($prod_data_arr[$dataArray[0][csf('id')]]['production_date']); ?></td>
            </tr>
            <tr>
                <td><strong>Job No:</strong></td>
                <td><? echo $dataArray[0][csf('job_no')]; ?></td>
                <td><strong>Order No:</strong></td>
                <td><? echo $order_arr[$dataArray[0][csf('job_no')]]['po']; ?></td>
                <td><strong>Embel. Name:</strong></td>
                <td><? echo $emblishment_name_array[$order_arr[$dataArray[0][csf('job_no')]]['embl_name']]; ?></td>
            </tr>
            <tr>
                <td><strong>Recipe No:</strong></td>
                <td><? echo $recipe_arr[$dataArray[0][csf('recipe_id')]]; ?></td>
                <td><strong>Remarks:</strong></td>
                <td><? //echo $recipe_for[$dataArray[0][csf('recipe_for')]]; ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                    <th width="30">SL</th>
                    <th width="140">Gmts Item</th>
                    <th width="120">Body Part</th>
                    <th width="110">Process/ Type</th>
                    <th width="120">Color</th>
                    <th width="70">Size</th>
                    <th width="80">Production Qty (Pcs)</th>
                    <th width="90">Operator/Superviser</th>
                    <th>Shift</th>
                </thead>
				<?
				
				$mst_id = $data[1];
				$com_id = $data[0];
				$job_no = $dataArray[0][csf('job_no')];
				$recipe_id = $dataArray[0][csf('recipe_id')];
				

				$sql= "select  a.id, a.subcon_job, b.main_process_id, c.id as color_size_id, c.item_id, c.embellishment_type, c.body_part, c.color_id, c.size_id, sum(c.qnty) as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d where a.subcon_job=b.job_no_mst and b.id=c.mst_id and a.subcon_job=d.job_no and a.order_id=d.po_id 
							
				and b.main_process_id=d.embl_name and c.item_id=d.gmts_item and c.embellishment_type=d.embl_type and c.body_part=d.body_part and c.color_id=d.color_id
				and a.entry_form=295 and d.entry_form=300 and a.status_active=1 and a.company_id='$com_id' and d.id='$recipe_id' group by a.id, a.subcon_job, b.main_process_id, c.id, c.item_id, c.embellishment_type, c.body_part, c.color_id, c.size_id, d.recipe_no_prefix_num order by d.recipe_no_prefix_num DESC";
				//echo $sql; die;
				$sql_res=sql_select($sql);
				
 				$i=1; $grand_tot_qty=0;

				foreach ($sql_res as $row) 
				{
					if($row[csf('main_process_id')]==1) $new_subprocess_array= $emblishment_print_type;
					else if($row[csf('main_process_id')]==2) $new_subprocess_array= $emblishment_embroy_type;
					else if($row[csf('main_process_id')]==3) $new_subprocess_array= $emblishment_wash_type;
					else if($row[csf('main_process_id')]==4) $new_subprocess_array= $emblishment_spwork_type;
					else if($row[csf('main_process_id')]==5) $new_subprocess_array= $emblishment_gmts_type;
					else $new_subprocess_array=$blank_array;
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i; ?></td>
                        <td><? echo $garments_item[$row[csf('item_id')]]; ?></td>
                        <td><? echo $body_part[$row[csf('body_part')]]; ?>&nbsp;</td>
                        <td><? echo $new_subprocess_array[$row[csf('embellishment_type')]]; ?>&nbsp;</td>
                        <td><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</td>
                        <td align="center"><? echo $size_arr[$row[csf('size_id')]]; ?>&nbsp;</td>
                        <td align="right"><? echo number_format($prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty'], 2, '.', ''); ?>&nbsp;</td>
                        <td><? echo $prod_data_arr[$mst_id]['operator_name']; ?>&nbsp;</td>
                        <td><? echo $shift_name[$prod_data_arr[$row[csf('color_size_id')]]['shift_id']]; ?>&nbsp;</td>
                    </tr>
					<?
					$i++;
					$grand_tot_qty+=$prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty'];
				}
				?>
                <tr class="tbl_bottom">
                    <td align="right" colspan="6"><strong>Grand Total</strong></td>
                    <td align="right"><? echo number_format($grand_tot_qty, 2, '.', ''); ?>&nbsp;</td>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
            
            <br>
			<? echo signature_table(140, $com_id, "930px"); ?>
        </div>
    </div>
	<?
	exit();
}

if ($action == 'show_production_no')
{
	$ex_data=explode("__",$data);
	$company_id=$ex_data[0];
	$job_no=$ex_data[1];
	$embl_job_arr=array();
	$embl_job_sql="select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity as qty from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=295 and a.subcon_job=b.job_no_mst";
	$embl_job_res=sql_select($embl_job_sql);
	foreach ($embl_job_res as $row)
	{
		$embl_job_arr[$row[csf("id")]]['order']=$row[csf("order_no")];
		$embl_job_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
		$embl_job_arr[$row[csf("id")]]['party_id']=$row[csf("party_id")];
		$embl_job_arr[$row[csf("id")]]['qty']=$row[csf("qty")];
	}
	unset($embl_job_res);
	
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	$job_no_cond="";
	if($job_no!="") $job_no_cond="and a.job_no='$job_no'";
	
	$qc_qty_arr=array();
	$qc_sql="select a.recipe_id, sum(b.qcpass_qty) as qty from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=302 and a.company_id='$company_id' $job_no_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.recipe_id";
	$qc_sql_res = sql_select($qc_sql);
	foreach ($qc_sql_res as $row)
	{
		$qc_qty_arr[$row[csf("recipe_id")]]=$row[csf("qty")];
	}
	unset($qc_sql_res);
	
	$sql = "select a.id, a.sys_no, a.prefix_no_num, a.job_no, a.order_id, a.buyer_po_id, sum(b.qcpass_qty) as qty from subcon_embel_production_mst a, subcon_embel_production_dtls b where a.id=b.mst_id and a.entry_form=301 and a.company_id='$company_id' $job_no_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id, a.sys_no, a.prefix_no_num, a.job_no, a.order_id, a.buyer_po_id order by a.id desc";
	$data_array = sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="360">
		<thead>
			<th width="20" align="center">SL</th>
			<th width="40">Prod ID</th>
			<th width="90">Job No.</th>
			<th width="90">Buyer PO</th>
            <th width="60">QC Qty.</th>
            <th>Balance</th>
		</thead>
	</table>
	<div style="width:360px; max-height:250px; overflow-y:scroll" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="340" class="rpt_table" id="tbl_prod_list_search">
			<?
			$i = 1;
			foreach ($data_array as $row)
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				
				$within_group=0; $order_no=""; $party_id=0; $order_qty=0;
				$within_group=$embl_job_arr[$row[csf("order_id")]]['within_group'];
				$order_no=$embl_job_arr[$row[csf("order_id")]]['order'];
				$party_id=$embl_job_arr[$row[csf("order_id")]]['party_id'];
				$order_qty=$embl_job_arr[$row[csf("order_id")]]['qty'];
				
				$buyer_po=""; $buyer_style="";
				
				if($within_group==1)
				{
					$buyer_po=$buyer_po_arr[$row[csf("buyer_po_id")]]['po'];
					$buyer_style=$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
				}
				
				$str="";
				$str=$row[csf('id')].'___'.$row[csf('sys_no')].'___'.$row[csf('job_no')].'___'.$row[csf('order_id')].'___'.$order_no.'___'.$party_id.'___'.$within_group.'___'.$order_qty.'___'.$row[csf('buyer_po_id')].'___'.$buyer_po.'___'.$buyer_style;
				
				$qc_qty=0; $balance_qty=0;
				$qc_qty=$qc_qty_arr[$row[csf("id")]];
				$balance_qty=$row[csf("qty")]-$qc_qty;
				
				if($balance_qty>0)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $str; ?>")' style="cursor:pointer">
						<td width="20" align="center"><? echo $i; ?></td>
						<td width="40" align="center"><? echo $row[csf('prefix_no_num')]; ?></td>
						<td width="90"><? echo $row[csf('job_no')]; ?></td>
						<td width="90" style="word-break:break-all"><? echo $buyer_po; ?></td>
						<td width="60" align="right"><? echo $qc_qty; ?></td>
						<td align="right"><? echo $balance_qty; ?></td>
					</tr>
					<?
					$i++;
				}
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(val)
	{
		document.getElementById('text_new_remarks').value=val;
		parent.emailwindow.hide();
	}
	</script>
    </head>
<body>
<div align="center">
	<fieldset style="width:400px;margin-left:4px;">
        <form name="remarksfrm_1"  id="remarksfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="370" >
                <tr>
                    <td align="center"><input type="hidden" name="auto_id" id="auto_id" value="<? echo $data; ?>" />
                      <textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:330px; height:270px" placeholder="Remarks Here. Maximum 1000 Character." ><? echo $data; ?></textarea>
                    </td>
                </tr>
                <tr>
                	<td align="center">
                 <input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" />
                 	</td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
if($action=="wash_qc_list_view")
{
	?>	
	<div style="width:400px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="120" >Order No</th>
                <th width="120" >Job No</th>
                <th width="80" >Delivery Qty</th>                    
            </thead>
    	</table> 
    </div>
	<div style="width:400px;max-height:180px; overflow:y-scroll" id="" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="details_table">
		<?php  
			$i=1;
			
		$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
		if($db_type==0)
		{
			$wo_cond="group_concat(distinct(b.order_id))";
			$do_cond="group_concat(distinct(b.id))";
		}
		else if($db_type==2)
		{
			$wo_cond="listagg(b.order_id,',') within group (order by b.order_id)";
			$do_cond="listagg(b.id,',') within group (order by b.id)";
		}
	$sql= "select a.id,sum(b.delivery_qty) as delivery_qty,$wo_cond as order_id,c.job_no_mst,$do_cond as dtls_id  from subcon_delivery_mst a, subcon_delivery_dtls b , subcon_ord_dtls c 
	where a.id=b.mst_id and a.entry_form='303' and  a.id='$data' and b.order_id=c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,c.job_no_mst order by a.id DESC ";
			$sqlResult =sql_select($sql); 
 			foreach($sqlResult as $row)
			{
					
				$order_no='';
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
				}
				$order_no=implode(",",array_unique(explode(",",$order_no)));
				
 			?>
                <tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="fnc_embl_delivery_dtls('<?php echo $row[csf('id')]; ?>_<?php echo $row[csf('dtls_id')]; ?>_<?php echo $row[csf('job_no_mst')]; ?>');" > 
                    <td width="40" align="center"><?php echo $i; ?></td>
                    <td width="120" align="center"><p><?php echo $order_no; ?>&nbsp;</p></td>
                    <td width="120" align="center"><p><?php echo $row[csf('job_no_mst')];; ?>&nbsp;</p></td>
                    <td width="80" align="center"><p><?php echo $row[csf('delivery_qty')]; ?></p></td>
                </tr>
			<?php
			$i++;
			}
			?>
		</table>
	</div>
	<?php
	exit();
}
?>