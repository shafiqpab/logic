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
	echo create_drop_down( "cbo_location_name", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/aop_qc_entry_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/aop_qc_entry_controller', document.getElementById('cbo_company_id').value+'_'+this.value+'_'+0, 'load_drop_down_machine', 'machine_td' );" );	
}

if ($action=="load_drop_down_floor")
{
	$ex_data=explode('_',$data);
	echo create_drop_down( "cbo_floor_name", 140, "select id,floor_name from lib_prod_floor where company_id=$ex_data[0] and location_id=$ex_data[1] and status_active =1 and is_deleted=0 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",1 );
}


if ($action=="load_drop_down_buyer")
{
	//echo $data;
	$data=explode("_",$data);

	/*if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";*/
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --","", "$load_function",1);
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --","", "",1 );
	}	
	exit();	 
} 

if ($action=="load_drop_down_machine")
{
	$data= explode("_", $data);

	if($data[1]==0 || $data[2]==0)
	{
		echo create_drop_down( "cbo_machine_id", 140, $blank_array,"", 1, "-- Select Machine --", $selected, "" );
	}
	else
	{
		if($db_type==2)
		{
			echo create_drop_down( "cbo_machine_id", 140, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where company_id='$data[0]' and location_id='$data[1]' and floor_id='$data[2]' and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no","id,machine_name", 1,"-- Select Machine --", $selected, "","" );
		}
		else if($db_type==0)
		{
			echo create_drop_down( "cbo_machine_id", 140, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where company_id='$data[0]' and location_id='$data[1]' and floor_id='$data[2]' and status_active=1 and is_deleted=0 and is_locked=0 order by  seq_no","id,machine_name", 1, "-- Select Machine --", $selected, "","" );
		}
	}	
}
if ($action=="production_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$ex_data=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('production_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
        <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
        <table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
            	<tr>
            		<th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            	</tr>
            	<tr>               	 
	                <th width="160">Company Name</th>
	                <th width="120">Production ID</th>
	                <th width="120">AOP Ref.</th>
	                <th width="120">Batch No</th>
	                <th width="200">Date Range</th>
	                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>  
                </tr>         
            </thead>
            <tbody>
                <tr>
                    <td> <input type="hidden" id="production_id">  
						<?   
							echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"",1 );
                        ?>
                    </td>
                    <td>
                        <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:113px" />
                    </td>
                    <td>
                        <input type="text" name="txt_aop_ref" id="txt_aop_ref" class="text_boxes" style="width:113px" />
                    </td>
                    <td>
                        <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:113px" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:83px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:83px">
                    </td> 
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_aop_ref').value+'_'+document.getElementById('txt_batch_no').value+'_'+document.getElementById('cbo_string_search_type').value, 'fabric_finishing_id_search_list_view', 'search_div', 'aop_qc_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
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

if ($action=="fabric_finishing_id_search_list_view")
{
	$data=explode('_',$data);
	$search_type =$data[6];
	if ($data[0]!=0) $company_name=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if($db_type==0)
	{ 
		if ($data[1]!="" &&  $data[2]!="") $production_date_cond = "and a.product_date between '".change_date_format($data[1],'yyyy-mm-dd')."' and '".change_date_format($data[2],'yyyy-mm-dd')."'"; else $production_date_cond ="";
	}
	else
	{
		if ($data[1]!="" &&  $data[2]!="") $production_date_cond = "and a.product_date between '".change_date_format($data[1], "", "",1)."' and '".change_date_format($data[2], "", "",1)."'"; else $production_date_cond ="";
	}
	$aop_ref_cond='';
	$product_id_cond='';
	$po_id_cond='';
	$aop_batch_cond='';
	$batch_cond='';
	$batch_no=trim(str_replace("'","",$data[5]));
	if($search_type==1)
	{
		if ($data[3]!='') $product_id_cond=" and a.prefix_no_num='$data[3]'"; 
		if ($data[4]!='') $aop_ref_cond= " and a.aop_reference = '$data[4]'"; 
		if(!empty($batch_no)) $batch_cond=" and batch_no='$batch_no'";

	}
	else if($search_type==4 || $search_type==0)
	{
		if ($data[3]!='') $product_id_cond=" and a.prefix_no_num like '%$data[3]%'"; 
		if ($data[4]!='') $aop_ref_cond= " and a.aop_reference like '%$data[4]%'"; 
		if(!empty($batch_no)) $batch_cond=" and batch_no like '%$batch_no%'";
	}
	else if($search_type==2)
	{
		if ($data[3]!='') $product_id_cond=" and a.prefix_no_num like '$data[3]%'"; 
		if ($data[4]!='') $aop_ref_cond= " and a.aop_reference like '$data[4]%'"; 
		if(!empty($batch_no)) $batch_cond=" and batch_no like '$batch_no%'";
	}
	else if($search_type==3)
	{
		if ($data[3]!='') $product_id_cond=" and a.prefix_no_num like '%$data[3]'"; 
		if ($data[4]!='') $aop_ref_cond= " and a.aop_reference like '%$data[4]'"; 
		if(!empty($batch_no)) $batch_cond=" and batch_no like '%$batch_no'";
	}
	function where_con($arrayData,$dataType=0,$table_coloum){
		$chunk_list_arr=array_chunk($arrayData,999);
		$p=1;
		foreach($chunk_list_arr as $process_arr)
		{
			if($dataType==0){
				if($p==1){$sql .=" and (".$table_coloum." in(".implode(',',$process_arr).")"; }
				else {$sql .=" or ".$table_coloum." in(".implode(',',$process_arr).")";}
			}
			else{
				if($p==1){$sql .=" and (".$table_coloum." in('".implode("','",$process_arr)."')"; }
				else {$sql .=" or ".$table_coloum." in('".implode("','",$process_arr)."')";}
			}
			$p++;
		}
		
		$sql.=") ";
		return $sql;
	}
	
	
	if ($batch_no!='')
	{ 
		$batch_sql = "select id from pro_batch_create_mst where   status_active=1 and is_deleted=0 $batch_cond";
		//echo $batch_sql;
		$batchSql_array=sql_select( $batch_sql );
		foreach ($batchSql_array as $row){
			$batch_ids .= $row[csf('id')].',';
		}
		$batch_ids=array_unique(explode(",",chop($batch_ids,',')));
		//$batchIds=implode(",",$batch_ids);
		if(count($batch_ids))
		{
			$aop_batch_cond=  where_con($batch_ids,0,"b.batch_id"); 
		}
		
	} 
	else
	{
		$aop_batch_cond='';
		
	}	
	
	
	if(!empty($aop_ref_cond))
	{
		$ord_sql = "select b.id,a.subcon_job,a.aop_reference,b.order_no from subcon_ord_mst a ,subcon_ord_dtls b where  company_id =$data[0] $aop_ref_cond and a.entry_form=278 and  a.subcon_job=b.job_no_mst";
		//echo $ord_sql;
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array();
		foreach ($ordArray as $row)
		{
			$po_arr[$row[csf('id')]] = $row[csf('order_no')];
			$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
			$po_id[] .= $row[csf("id")];
		}
		$po_id=array_unique($po_id);
		if(count($po_id))
		{
			//$po_id_cond=" and b.order_id in ('".implode("','",$po_id)."') ";
			$po_id_cond=where_con($po_id,1,"b.order_id"); 
		}
		
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
	$main_batch_allow = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id =$data[0] and variable_list=13 and is_deleted=0 and status_active=1");
	$entry_form_cond='';
	if($main_batch_allow==1) $entry_form_cond=" and entry_form in (0,281)"; else $entry_form_cond="and entry_form =281 ";
	$batch_id_sql="select id,within_group,company_id, batch_no, extention_no from pro_batch_create_mst where  status_active=1 and is_deleted=0 $entry_form_cond";
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
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
		$batch_cond="listagg((cast(b.batch_id as varchar2(4000))),',') within group (order by b.batch_id) as batch_id";
		$order_cond="listagg((cast(b.order_id as varchar2(4000))),',') within group (order by b.order_id) as order_id";
	}

	$sql= "select a.id, product_no, a.prefix_no_num, $year_cond, a.party_id, a.product_date, a.prod_chalan_no, $batch_cond, b.order_id, sum(b.product_qnty) as product_qnty, b.mst_id from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=291 and a.status_active=1 $company_name $production_date_cond $product_id_cond $po_id_cond  $aop_batch_cond  group by a.id, product_no, a.prefix_no_num, a.insert_date, a.party_id, a.product_date, a.prod_chalan_no, b.order_id, b.mst_id  order by a.id DESC";
	//echo $sql;die;
	$result_sql= sql_select($sql);

	$production_qty_array=array();
	$prod_sql="Select b.batch_id, sum(b.product_qnty) as product_qnty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and entry_form=294 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by  b.batch_id";
	//mst_id in($mst_ids)
	//echo $prod_sql; die;
	$prod_data_sql=sql_select($prod_sql);
	foreach($prod_data_sql as $row)
	{
		$production_qty_array[$row[csf('batch_id')]]=$row[csf('product_qnty')];
	}

    ?>
    <div>
    <script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_po_list',-1);
        });

	</script>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
            <thead>
                <th width="30" >SL</th>
                <th width="100" >AOP Ref.</th>
                <th width="110" >Prod. ID</th>
                <th width="60" >Year</th>
                <th width="70" >Prod. Date</th>
                <th width="100" >Batch</th>
                <th width="80">Prod. Qty</th>
                <th width="80">QC. Qty</th>
                <th>Bal. Qty</th>
            </thead>
     	</table>
     </div>
     <div style="width:800px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_po_list">
			<?
			//$result_sql= sql_select($sql);
			$i=1;
            foreach($result_sql as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $order_id=array_unique(explode(",",$row[csf("order_id")]));
				$batch_id=array_unique(explode(",",$row[csf("batch_id")]));
				$batch_no=""; $aop_ref=''; 
				foreach($order_id as $val)
				{
					//echo $aop_ref."=";
					if($aop_ref=="") $aop_ref=$ref_arr[$val]; else $aop_ref.=",".$ref_arr[$val];
				}
				$aop_ref=implode(",",array_unique(explode(",",$aop_ref)));

				foreach($batch_id as $key)
				{
					if($batch_no=="") $batch_no=$batch_array[$key]['batch_no']; else $batch_no.=", ".$batch_array[$key]['batch_no'];
				}
				if($main_batch_allow==1)
				{
					$within_group=1;
				}
				else
				{
					$within_group=$batch_array[$key]['within_group'];
				}
				//print_r($batch_id);
				//$batch_id=array_unique(explode(",",$row[csf("batch_id")]));
				//$batch_id=implode(",",array_unique(explode(",",$batch_id)));
				$click_data=$row[csf('id')]."_".$within_group."_".$batch_array[$batch_id[0]]["company_id"];
				//echo $click_data;


				$qc_qty=$production_qty_array[$row[csf('batch_id')]];
				$bal_qty=$row[csf("product_qnty")]-$production_qty_array[$row[csf('batch_id')]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $click_data;?>');" > 
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="100"><p><? echo $aop_ref; ?></p></td>
						<td width="110" align="center"><? echo $row[csf("product_no")]; ?></td>
                        <td width="60" align="center"><? echo $row[csf("year")]; ?></td>		
						<td width="70"><? echo change_date_format($row[csf("product_date")]); ?></td>
						<td width="100"><p><? echo $batch_no; ?></p></td>
                        <td width="80" align="right"><? echo number_format($row[csf("product_qnty")],2,'.',''); ?></td>
                        <td width="80" align="right"><? echo number_format($qc_qty,2,'.',''); ?></td>
                        <td align="right"><? echo number_format($bal_qty,2,'.',''); ?></td>
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

if ($action=="load_php_data_to_form_mst")
{
	$data=explode("_",$data);
	//echo "select id, product_no, basis, company_id, location_id, party_id, product_date, prod_chalan_no, remarks from subcon_production_mst where entry_form=291 and id='$data[0]'"; die;
	$nameArray=sql_select( "select id, product_no, basis, company_id, location_id, party_id, product_date, prod_chalan_no, remarks from subcon_production_mst where entry_form=291 and id='$data[0]'" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_production_no').value 			= '".$row[csf("product_no")]."';\n";

		echo "document.getElementById('txt_production_id').value 			= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n";

		echo "load_drop_down( 'requires/aop_qc_entry_controller', $data[2]+'_'+$data[1], 'load_drop_down_buyer', 'buyer_td' );";
		echo "document.getElementById('cbo_party_name').value				= '".$row[csf("party_id")]."';\n"; 

		echo "load_drop_down( 'requires/aop_qc_entry_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_floor', 'floor_td' );";

		//echo "document.getElementById('cbo_floor_name').value 			= '".$row[csf("floor_id")]."';\n";

		echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_party_name',1);\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'subcon_fabric_finishing',1);\n";
	}
	exit();	
}

if ($action=="qc_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$ex_data=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('qc_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
        <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>                	 
                <th width="150">Company Name</th>
                <th width="100">QC ID</th>
                <th width="100">AOP Ref.</th>
                <th width="100">Batch No.</th>
                <th width="180">Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
            </thead>
            <tbody>
                <tr>
                    <td> <input type="hidden" id="qc_id">
						<?   
							echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"",1);
                        ?>
                    </td>
                    <td>
                        <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:87px" />
                    </td>
                    <td>
                        <input type="text" name="txt_aop_ref" id="txt_aop_ref" class="text_boxes" style="width:87px" />
                    </td>
                    <td>
                        <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:87px" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:75px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:75px">
                    </td> 
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_aop_ref').value+'_'+document.getElementById('txt_batch_no').value, 'qc_id_search_list_view', 'search_div', 'aop_qc_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
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

if ($action=="qc_id_search_list_view")
{
	$data=explode('_',$data);
	
	
	if ($data[0]!=0) $company_name=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if($data[3]=='' && $data[4]=='' && $data[5]=='' && $data[1]=='' && $data[2]=='' )
	{
		echo "Please select date range.";die;
	}
	
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
	if ($data[5]!='') $batch_cond= " and batch_no like '%$data[5]%'"; else $batch_cond="";	
	
	
	
	
	
	
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
	//echo $po_id_cond; die;
	//$return_to=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$main_batch_allow = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id =$data[0] and variable_list=13 and is_deleted=0 and status_active=1");
	$entry_form_cond='';
	if($main_batch_allow==1) $entry_form_cond=" entry_form in(0,281)"; else $entry_form_cond=" entry_form =281 ";
	$batch_id_sql="select id,within_group,company_id, batch_no, extention_no from pro_batch_create_mst where $entry_form_cond $batch_cond and status_active=1 and is_deleted=0";
	$batch_id_sql_result=sql_select($batch_id_sql); $batch_ids=array();
	foreach ($batch_id_sql_result as $row)
	{
		$batch_array[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_array[$row[csf("id")]]["extention_no"]=$row[csf("extention_no")];
		$batch_array[$row[csf("id")]]["within_group"]=$row[csf("within_group")];
		$batch_array[$row[csf("id")]]["company_id"]=$row[csf("company_id")];
		$batch_ids[]=$row[csf("id")];
	}
	//echo count($batch_ids); die;
	//print_r($batch_ids);
	$batch_ids=array_unique($batch_ids);
	if($db_type==2 && $batch_ids!="") 
	if (count($batch_ids)>0)
	{
		
		//print_r($batch_ids);
		$batch_idsCond=""; 
		if($db_type==2 && count($batch_ids)>=999)
		{
			$chunk_arr=array_chunk($batch_ids,999);
			foreach($chunk_arr as $val)
			{
				//$ids=implode("','",$val);
				$ids="'".implode("','",$val)."'";
				if($batch_idsCond=="")
				{
					$batch_idsCond.=" and ( b.batch_id in ( $ids) ";
				}
				else
				{
					$batch_idsCond.=" or  b.batch_id in ( $ids) ";
				}
			}
			$batch_idsCond.=")";
		}
		else
		{
			$ids="'".implode("','",$batch_ids)."'";
			$batch_idsCond.=" and b.batch_id in ($ids) ";
		}
		//echo $batch_ids."==";
	}
	else if(count($batch_ids)==0 && $data[5]!='')
	{
		echo "Not Found"; die;
	}
	if($db_type==0)
	{
		$year_cond= "year(a.insert_date)as year";
		$batch_cond="group_concat(b.batch_id) as batch_id";
		$order_cond="group_concat(b.order_id) as order_id";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
		$batch_cond="listagg((cast(b.batch_id as varchar2(4000))),',') within group (order by b.batch_id) as batch_id";
		$order_cond="listagg((cast(b.order_id as varchar2(4000))),',') within group (order by b.order_id) as order_id";
	}
	$sql= "select a.id, product_no, a.prefix_no_num, $year_cond, a.party_id, a.product_date, a.prod_chalan_no, $batch_cond, $order_cond , sum(b.product_qnty) as product_qnty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=294 and a.status_active=1 $company_name $buyer_cond $production_date_cond $product_id_cond $po_id_cond $batch_idsCond  group by a.id, product_no, a.prefix_no_num, a.insert_date, a.party_id, a.product_date, a.prod_chalan_no  order by a.id DESC";
	
	//echo  create_list_view("list_view", "Prod. ID,Year,Basis,Party,Prod. Date,Product Challan", "80,80,120,120,70,120","750","250",0, $sql , "js_set_value", "id", "", 1, "0,0,basis,party_id,0,0", $arr , "prefix_no_num,year,basis,party_id,product_date,prod_chalan_no", "aop_production_controller","",'0,0,0,0,3,0');
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
                <th width="100" >AOP Ref.</th>
                <th width="150" >QC ID</th>
                <th width="60" >Year</th>
                <th width="70" >QC. Date</th>
                <th width="200" >Batch</th>
                <th>QC. Qty</th>
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
				
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				$batch_id=array_unique(explode(",",$row[csf("batch_id")])); 
				$aop_ref=''; $batch_no="";
				foreach($order_id as $val)
				{
					//echo $val."=";
					if($aop_ref=="") $aop_ref=$ref_arr[$val]; else $aop_ref.=",".$ref_arr[$val];
				}
				$aop_ref=implode(",",array_unique(explode(",",$aop_ref)));
				foreach($batch_id as $key)
				{
					if($batch_no=="") $batch_no=$batch_array[$key]['batch_no']; else $batch_no.=", ".$batch_array[$key]['batch_no'];
				}
				if($main_batch_allow==1)
				{
					$within_group=1;
				}
				else
				{
					$within_group=$batch_array[$key]['within_group'];
				}
				$click_data=$row[csf('id')]."_".$within_group."_".$batch_array[$batch_id[0]]["company_id"];
				//$data = $row[csf("id")] . "_" . $batch_array[$key]['within_group']. "_" . $batch_array[$key]['company_id'];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $click_data;?>');" > 
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="100"><p><? echo $aop_ref; ?></p></td>
						<td width="150" align="center"><? echo $row[csf("product_no")]; ?></td>
                        <td width="60" align="center"><? echo $row[csf("year")]; ?></td>		
						<td width="70"><? echo change_date_format($row[csf("product_date")]); ?></td>
						<td width="200"><p><? echo $batch_no; ?></p></td>
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


if ($action=="load_qc_data_to_form_mst")
{
	$data=explode("_",$data);
	//echo "select id, product_no, basis, company_id, location_id, party_id, product_date, prod_chalan_no, remarks from subcon_production_mst where entry_form=294 and id='$data[0]'";
	$nameArray=sql_select( "select id, product_no, basis, company_id, location_id, party_id, product_date, prod_chalan_no, remarks from subcon_production_mst where entry_form=294 and id='$data[0]'" ); 

	foreach ($nameArray as $row)
	{	
		//$company_id=$row[csf("company_id")];
		echo "document.getElementById('txt_qc_id').value 					= '".$row[csf("product_no")]."';\n";
		echo "document.getElementById('cbo_receive_basis').value			= '".$row[csf("basis")]."';\n"; 
		echo "document.getElementById('txt_qc_date').value					= '".change_date_format($row[csf("product_date")])."';\n"; 
		
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/aop_qc_entry_controller', $data[2]+'_'+$data[1], 'load_drop_down_buyer', 'buyer_td' );";
		echo "document.getElementById('cbo_party_name').value				= '".$row[csf("party_id")]."';\n";
		echo "load_drop_down( 'requires/aop_qc_entry_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_floor', 'floor_td' );";
 
		//echo "document.getElementById('txt_chal_no').value				= '".$row[csf("prod_chalan_no")]."';\n"; 
		echo "document.getElementById('txt_remarks').value					= '".$row[csf("remarks")]."';\n"; 
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
		echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_party_name*txt_production_no',1);\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'subcon_fabric_finishing',1);\n";
	}
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
                            <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:105px" />
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                        </td> 
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_string_search_type').value, 'batch_search_list_view', 'search_div', 'aop_qc_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
		$sql="select a.id, a.batch_no, a.extention_no, a.color_id, a.batch_weight, a.total_liquor, sum(b.batch_qnty) as batch_qnty, b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.entry_form=281 $company_con $batch_date_cond $batch_no_cond group by a.batch_no, a.extention_no,b.po_id order by a.id DESC";// and a.batch_against=1
	}
	else if($db_type==2)
	{
		$sql="select a.id, a.batch_no, a.extention_no, a.color_id, a.batch_weight, a.total_liquor, sum(b.batch_qnty) as batch_qnty, b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.entry_form=281 $company_con $batch_date_cond $batch_no_cond group by a.id, a.batch_no, a.extention_no, a.color_id, a.batch_weight, a.total_liquor ,b.po_id order by a.id DESC";// and a.batch_against=1
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
		$nameArray=sql_select( "select a.id, a.batch_no, a.extention_no, a.color_id, a.process_id, b.width_dia_type,b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.id='$data'  group by a.batch_no, a.extention_no" );
	}
	elseif($db_type==2)
	{
		$nameArray=sql_select( "select a.id, a.batch_no, a.extention_no, a.color_id, a.process_id, listagg(b.width_dia_type,',') within group (order by b.width_dia_type) as width_dia_type,b.po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.id='$data' group by a.id, a.batch_no, a.extention_no, a.color_id, a.process_id" );
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

if($action=="show_fabric_desc_listview")
{
	//echo $data;
	$data=explode('_',$data);	
	//$gsm_arr=return_library_array( "select id,gsm from lib_subcon_charge",'id','gsm');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$production_qty_array=array();
	$prod_sql="Select batch_id, cons_comp_id, sum(product_qnty) as product_qnty from subcon_production_dtls where production_id= '$data[0]' and status_active=1 and is_deleted=0 group by  batch_id, cons_comp_id";
	$prod_data_sql=sql_select($prod_sql);
	foreach($prod_data_sql as $row)
	{
		$production_qty_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]=$row[csf('product_qnty')];
	}
	if($data[1]==2){
		$ord_sql = "SELECT b.id,a.subcon_job,a.aop_reference,b.order_no,b.buyer_style_ref,b.buyer_po_no from subcon_ord_mst a ,subcon_ord_dtls b where a.company_id =$data[2] and a.entry_form=278 and  a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array();
		foreach ($ordArray as $row)
		{
			//$po_arr[$row[csf('id')]] = $row[csf('order_no')];
			//$po_arr[$row[csf('id')]]['order'] = $row[csf('order_no')];
			//$po_arr[$row[csf('id')]]['job'] = $row[csf('subcon_job')];
			$buyer_po_arr[$row[csf('id')]]['style'] = $row[csf('buyer_style_ref')];
			$buyer_po_arr[$row[csf('id')]]['po'] = $row[csf('buyer_po_no')];
			//$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
		}
	}
	else{
		$po_sql ="Select a.style_ref_no, b.id, b.po_number,a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
			//$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
		}
	}
	

	$sql = "select b.id,b.fabric_description,b.color_id,b.product_qnty,b.process,b.no_of_roll,b.floor_id,b.shift,b.uom_id,b.dia_width,b.gsm,b.order_id,b.batch_id,b.body_part_id,b.buyer_po_id from  subcon_production_mst a, subcon_production_dtls b where a.id in($data[0]) and a.id=b.mst_id and a.entry_form=291 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="460">
        <thead>
            <th width="15">SL</th>
            <th>Fabric Description</th>
            <th width="60">Color</th>
            <th width="60">Buyer PO</th>
            <th width="60">Production Qty</th>
            <th width="40">QC Qty</th>
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
		    	if($data[1]==2) $po_id=$row[csf('order_id')]; else $po_id=$row[csf('buyer_po_id')] ;
		    	$po=$buyer_po_arr[$po_id]['po'];
		    	$style=$buyer_po_arr[$po_id]['style'];
		    	$balance=$row[csf('product_qnty')]-$production_qty_array[$row[csf('batch_id')]][$row[csf('id')]];
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             	?>

                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('id')]."**".$row[csf('fabric_description')]."**".$row[csf('gsm')]."**".$row[csf('dia_width')]."**".$row[csf('color_id')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('process')]."**".$row[csf('no_of_roll')]."**".$row[csf('shift')]."**".$row[csf('uom_id')]."**".$po."**".$style."**".$row[csf('buyer_po_id')]."**".$row[csf('order_id')]."**".$row[csf('batch_id')]."**".$row[csf('body_part_id')]."**".$row[csf('floor_id')]."**".$row[csf('product_qnty')]."**".$data[1]; ?>")' style="cursor:pointer" >
                    <td><? echo $i; ?></td>
                    <td><p><? echo $row[csf('fabric_description')]; ?></p></td>
                    <td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td><p><? echo $po; ?></p></td>
                    <td align="right"><? echo number_format($row[csf('product_qnty')],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($production_qty_array[$row[csf('batch_id')]][$row[csf('id')]],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('product_qnty')]-$production_qty_array[$row[csf('batch_id')]][$row[csf('id')]],2,'.',''); ?></td>
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
			http.open("POST","aop_qc_entry_controller.php",true);
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
					set_button_status(1, permission, 'fnc_reject_save',1,2);
					//release_freezing();
				}
			}
		}

		function fnc_close()
		{
			var tot_row=$('#tbl_list_search tbody tr').length;
			var qnty_qn="";
			var qnty_tot="";
			var qnty_tbl_id="";
			for(var i=1; i<=tot_row; i++)
			{
				qnty_qn += $("#txtRejQty_"+i).val();
				qnty_tot=qnty_tot*1+$("#txtRejQty_"+i).val()*1;
			}
			document.getElementById('hidden_qnty_tot').value=qnty_tot;
			parent.emailwindow.hide();
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
	                	$sqldtls=sql_select(" select id,mst_id,dtls_id,reject_type_id,quantity from  subcon_production_qnty where dtls_id=$data[0]");
	                	foreach ($sqldtls as $row)
						{
							$qty_arr[$row[csf("reject_type_id")]]['quantity']=$row[csf("quantity")];
						}
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
								<td><input type="text" name="txtRejQty[]" id="txtRejQty_<?php echo $i ?>" class="text_boxes_numeric" value="<? echo $qty_arr[$id]['quantity'] ;?>" style="width:60px"/></td>
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
                <? if(count($sqldtls)>0)
                {
                	echo load_submit_buttons($permission, "fnc_reject_save", 1,0,"",2);
                }
                else
                {
                	echo load_submit_buttons($permission, "fnc_reject_save", 0,0,"",2);
                }
                
				//echo load_submit_buttons( $permission, "fnc_reject_operationnnnnn",0,1,"",2);
				?>
                <input type="hidden" id="update_dts_id" name="update_dts_id" value="<?php echo $data[0]; ?>">
                <input type="hidden" id="update_mst_id" name="update_mst_id" value="<?php echo  $data[1]; ?>">
                <input type="hidden" id="hidden_qnty_tot" name="hidden_qnty_tot">
                <input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
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
		$total_rej_qty=0;
		for($i=1;$i<=$total_row;$i++)
		{
			$txt_individual_id="txtIdividualId_".$i;
			$txt_rej_qty="txtRejQty_".$i;

			if(!in_array(str_replace("'","",$$txt_individual_id),$payhead_id_check))
			{
				$payhead_id_check[]=$$txt_individual_id; 
				if(str_replace("'","",$$txt_rej_qty)!='')
				{
					if ($data_array3 != "") $data_array3 .= ",";
					$data_array3 .="(".$id.",'".$update_mst_id."','".$update_dts_id."',".$$txt_individual_id.",".$$txt_rej_qty.")";
					$total_rej_qty+=str_replace("'","",$$txt_rej_qty);
					$id=$id+1;
				}
			}
		}
		
		$field_array = "reject_qnty*updated_by*update_date";
 		$data_array = "'".$total_rej_qty."'*'".$user_id."'*'".$pc_date_time."'";
 		if($data_array!="")
		{
 			$rID=sql_update("subcon_production_dtls",$field_array,$data_array,"id","".$update_dts_id."",1);
 		}

		if($data_array3!="")
		{
			//echo "INSERT INTO subcon_production_qnty (".$field_array3.") VALUES ".$data_array3; die;
			$rID3=sql_insert("subcon_production_qnty",$field_array3,$data_array3,1);
		}
		if($db_type==0)
		{
			if($rID && $rID3)
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
			if($rID && $rID3){
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
		
		$data_array3=""; $total_rej_qty=0;
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
					$total_rej_qty+=str_replace("'","",$$txt_rej_qty);
					$id=$id+1;
				}
			}
		}

		$field_array = "reject_qnty*updated_by*update_date";
 		$data_array = "'".$total_rej_qty."'*'".$user_id."'*'".$pc_date_time."'";
 		if($data_array!="")
		{
 			$rID=sql_update("subcon_production_dtls",$field_array,$data_array,"id","".$update_dts_id."",1);
 		}

 		execute_query( "delete from subcon_production_qnty where  dtls_id =".$update_dts_id."",1);

		if($data_array3!="")
		{
			//echo "INSERT INTO subcon_production_qnty (".$field_array3.") VALUES ".$data_array3; die;
			$rID3=sql_insert("subcon_production_qnty",$field_array3,$data_array3,1);
		}
		if($db_type==0)
		{
			if($rID && $rID3)
			{
				mysql_query("COMMIT");
				echo "1**".$update_mst_id."**".$update_dts_id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$update_mst_id."**".$update_dts_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID3){
				oci_commit($con);
				echo "1**".$update_mst_id."**".$update_dts_id;
			}
			else{
				oci_rollback($con); 
				echo "10**".$update_mst_id."**".$update_dts_id;
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
                <th width="80" align="center">QC Qty</th> 
                <th width="50" align="center">Roll</th>                  
            </thead>
        </table>
    </div>
    <div style="width:100%;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
        <?php  
			$i=1;
			$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
			//$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
			$main_batch_allow = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id =$data[1] and variable_list=13 and is_deleted=0 and status_active=1");
			$entry_form_cond='';
			if($main_batch_allow==1) $entry_form_cond=" entry_form in(0,281)"; else $entry_form_cond=" entry_form =281 ";
			$batch_id_sql="select id,within_group,company_id, batch_no, extention_no from pro_batch_create_mst where $entry_form_cond and status_active=1 and is_deleted=0";
			$batch_id_sql_result=sql_select($batch_id_sql);
			foreach ($batch_id_sql_result as $row)
			{
				$batch_array[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
				$batch_array[$row[csf("id")]]["extention_no"]=$row[csf("extention_no")];
				$batch_array[$row[csf("id")]]["within_group"]=$row[csf("within_group")];
				$batch_array[$row[csf("id")]]["company_id"]=$row[csf("company_id")];
			}
			//$machine_arr=return_library_array( "select id,machine_no from  lib_machine_name",'id','machine_no');
			$sql ="select id, batch_id, order_id, process, fabric_description, color_id, gsm, dia_width, no_of_roll, product_qnty, machine_id from subcon_production_dtls where status_active=1 and mst_id='$data[0]'"; 
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
				if($main_batch_allow==1)
				{
					$within_group=1;
				}
				else
				{
					$within_group=$batch_array[$row[csf("batch_id")]]['within_group'];
				}
				$click_data=$row[csf('id')]."_".$within_group."_".$batch_array[$row[csf("batch_id")]]["company_id"];
				//echo $click_data;
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="get_php_form_data('<? echo $click_data ?>','load_php_data_to_form_dtls','requires/aop_qc_entry_controller');" style="text-decoration:none; cursor:pointer" > 
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
                    $order_num=implode(",",array_unique(explode(",",$order_num)));
                    ?>
                    <td width="80" align="center"><p><? echo $order_num; ?></p></td>
                    <td width="150" align="center"><p><? echo $row[csf('fabric_description')]; ?></p></td>
                    <td width="70" align="center"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td width="50" align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
                    <td width="80" align="right"><p><? echo $row[csf('product_qnty')]; ?>&nbsp;</p></td>
                    <td width="50" align="right"><p><? echo $row[csf('no_of_roll')]; ?>&nbsp;</p></td>
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
	//echo $data;
	$data=explode('_',$data);
	$order_arr=return_library_array("select id,order_no from subcon_ord_dtls",'id','order_no');
	$process_arr=return_library_array("select id,main_process_id from subcon_ord_dtls",'id','main_process_id');
	$color_no_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	
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

	if($data[1]==2){
		$ord_sql = "SELECT b.id,a.subcon_job,a.aop_reference,b.order_no,b.buyer_style_ref,b.buyer_po_no from subcon_ord_mst a ,subcon_ord_dtls b where a.company_id =$data[2] and a.entry_form=278 and  a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); //$ref_arr=array();
		foreach ($ordArray as $row)
		{
			//$po_arr[$row[csf('id')]] = $row[csf('order_no')];
			//$po_arr[$row[csf('id')]]['order'] = $row[csf('order_no')];
			//$po_arr[$row[csf('id')]]['job'] = $row[csf('subcon_job')];
			$buyer_po_arr[$row[csf('id')]]['style'] = $row[csf('buyer_style_ref')];
			$buyer_po_arr[$row[csf('id')]]['po'] = $row[csf('buyer_po_no')];
			//$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
		}
		unset($ordArray);
	}
	else{
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
	}
	
	
	$sql= "select id, batch_id,production_id, width_dia_type, order_id, process, fabric_description, cons_comp_id, color_id, gsm, dia_width, product_qnty, reject_qnty, no_of_roll, floor_id, machine_id, start_hour, start_minutes, start_date, end_hour, end_minutes, end_date,buyer_po_id,shift,uom_id,body_part_id,remarks from subcon_production_dtls where id='$data[0]'";			
 	$nameArray=sql_select($sql);
	foreach ($nameArray as $row)
	{
		$order_id=$row[csf("order_id")];
		$order_ids=explode(',',$order_id);
		$order_no=''; 
		foreach($order_ids as $okey)
		{
			if($order_no=="") $order_no=$order_arr[$okey]; else $order_no .=",".$order_arr[$okey];
		}
		$production_id=$row[csf("production_id")];
		if($data[1]==2) $po_id=$row[csf('order_id')]; else $po_id=$row[csf('buyer_po_id')] ;
    	$po=$buyer_po_arr[$po_id]['po'];
    	$style_ref_no=$buyer_po_arr[$po_id]['style'];

		//$po=$buyer_po_arr[$row[csf('buyer_po_id')]]['po'];
		//$style_ref_no=$buyer_po_arr[$row[csf('buyer_po_id')]]['style'];
		$product_no=return_field_value("product_no","subcon_production_mst","id=$production_id");
		$orderIds="'".$order_id."'"; $buyer_po_id=$row[csf("buyer_po_id")];
		//echo "select product_qnty from subcon_production_dtls where mst_id=$production_id and order_id in ($orderIds) and buyer_po_id=$buyer_po_id";
		$product_qnty=return_field_value("product_qnty","subcon_production_dtls","mst_id=$production_id and order_id in ($orderIds) and buyer_po_id=$buyer_po_id");
		//$pos=chop($pos,","); $styles=chop($styles,",");
		echo "document.getElementById('txt_production_no').value		 		= '".$product_no."';\n";
		echo "document.getElementById('txt_production_id').value		 		= '".$production_id."';\n";
		echo "document.getElementById('txt_batch_id').value		 				= '".$row[csf("batch_id")]."';\n";
		echo "set_multiselect('txt_process_id','0','1','".$row[csf("process")]."','0');\n";
		echo "document.getElementById('hidden_dia_type').value		 			= '".$row[csf("width_dia_type")]."';\n";

		echo "document.getElementById('order_no_id').value						= '".$row[csf("order_id")]."';\n"; 
		echo "document.getElementById('cbo_uom').value							= '".$row[csf("uom_id")]."';\n"; 
		echo "document.getElementById('txt_description').value					= '".$row[csf("fabric_description")]."';\n";
		echo "document.getElementById('txt_color').value		 				= '".$color_no_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('hidden_color_id').value		 			= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_gsm').value		 					= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_dia_width').value		 			= '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_product_qnty').value		 			= '".$product_qnty."';\n";
		echo "document.getElementById('txt_qc_qnty').value            			= '".$row[csf("product_qnty")]."';\n";
		echo "document.getElementById('txt_reject_qty').value            		= '".$row[csf("reject_qnty")]."';\n";
		echo "document.getElementById('txt_roll_no').value            			= '".$row[csf("no_of_roll")]."';\n";
		echo "document.getElementById('cboShift').value            				= '".$row[csf("shift")]."';\n";
		echo "document.getElementById('txt_buyer_po').value            			= '".$po."';\n";
		echo "document.getElementById('txt_buyer_style').value            		= '".$style_ref_no."';\n";
		echo "document.getElementById('txt_buyer_po_id').value            		= '".$row[csf("buyer_po_id")]."';\n";
		echo "document.getElementById('cbo_body_part').value            		= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('cbo_floor_name').value		 			= '".$row[csf("floor_id")]."';\n"; 
		echo "document.getElementById('comp_id').value		 				    = '".$row[csf("cons_comp_id")]."';\n"; 
		echo "document.getElementById('txt_remarks').value		 				= '".$row[csf("remarks")]."';\n"; 
		echo "document.getElementById('update_id_dtl').value            		= '".$row[csf("id")]."';\n";
		echo "document.getElementById('hid_within_group').value            		= '".$data[1]."';\n";
		//echo "show_list_view('$production_id+'_'+$data[1]+'_'+$data[2]','show_fabric_desc_listview','list_fabric_desc_container','requires/aop_qc_entry_controller','');\n";
		echo "show_list_view('" . $production_id . "_" . $data[1] . "_" . $data[2] . "','show_fabric_desc_listview','list_fabric_desc_container','requires/aop_qc_entry_controller','');\n";
		echo "calculate_gain_loss();\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'subcon_fabric_finishing',1);\n";	
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
	
	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'subcon_fabric_finishing',1);\n";
	exit();	
}

$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$process_finishing="0";
	
	
	
	//echo $total_qc_production_qty; die;
	
	
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
		
		// Validation off for micro fiber . concern of Sayeed vai
		/*$production_qty_array=array();
		$prod_sql="Select a.batch_id, a.cons_comp_id, sum(a.product_qnty) as product_qnty from subcon_production_dtls a,subcon_production_mst b where  a.mst_id=b.id and a.production_id=$txt_production_id and a.status_active=1 and a.is_deleted=0 and b.entry_form=294 group by  a.batch_id, a.cons_comp_id";//$txt_batch_id.",".$txt_production_id; die;
		$prod_data_sql=sql_select($prod_sql);
		foreach($prod_data_sql as $row)
		{
			$production_qty_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]=$row[csf('product_qnty')];
		}
		$batchid=str_replace("'","",$txt_batch_id);
		$conscompid=str_replace("'","",$comp_id);
		$total_qc_production_qty=$production_qty_array[$batchid][$conscompid];//$txt_batch_id  $comp_id; die;
		if($total_qc_production_qty){$total_qc_production_qty=$total_qc_production_qty;}else {$total_qc_production_qty=0;}
		$productQty = return_field_value("sum(a.product_qnty) as production_qty",  "subcon_production_dtls a,subcon_production_mst b","a.mst_id=$txt_production_id and a.mst_id=b.id and a.status_active=1 and  a.is_deleted=0 and b.entry_form=291","production_qty");
		$blance_qty=($productQty-$total_qc_production_qty);
		//echo "11**Qc Quantity Over Production Quantity".$blance_qty."dfd".$txt_qc_qnty."dfd".$comp_id; die; 
		$qc_qnty=str_replace("'","",$txt_qc_qnty);
		if($qc_qnty>$blance_qty)
		{
			echo "11**Qc Quantity Over Production Quantity  "."(Bal. Qty =".$blance_qty.")";
			die;
		}*/
		$new_return_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '','AOPQC', date("Y",time()), 5, "select id,prefix_no,prefix_no_num from  subcon_production_mst where entry_form=294 and company_id=$cbo_company_id  $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		//print_r($new_return_no); die;
		if(str_replace("'",'',$update_id)=="")
		{			
			$field_array="id,entry_form,prefix_no,prefix_no_num,product_no,product_type,basis,company_id,location_id,party_id,product_date,prod_chalan_no,within_group,inserted_by,insert_date";
			$id=return_next_id( "id","subcon_production_mst",1); 
			$data_array="(".$id.",294,'".$new_return_no[1]."','".$new_return_no[2]."','".$new_return_no[0]."','".$process_finishing."',".$cbo_receive_basis.",".$cbo_company_id.",".$cbo_location_name.",".$cbo_party_name.",".$txt_qc_date.",".$txt_chal_no.",".$hid_within_group.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";  
			$rID=sql_insert("subcon_production_mst",$field_array,$data_array,0);
			$return_no=$new_return_no[0];
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="product_no*basis*location_id*party_id*product_date*prod_chalan_no*updated_by*update_date";
			$data_array="".$txt_qc_id."*".$cbo_receive_basis."*".$cbo_location_name."*".$cbo_party_name."*".$txt_qc_date."*".$txt_chal_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("subcon_production_mst",$field_array,$data_array,"id",$update_id,0); 
			$return_no=str_replace("'",'',$txt_qc_id);
		}
		
		$id1=return_next_id("id","subcon_production_dtls",1); 
		//$machine_tbl_id=return_field_value("id","lib_machine_name", "machine_no=$cbo_machine_id and status_active =1 and is_deleted=0" );
		//$color_tbl_id = return_id( str_replace("'","",$txt_color), $color_library_arr, "lib_color", "id,color_name");
		
		$txt_process_id=explode(",",str_replace("'","",$txt_process_id));
		asort($txt_process_id);
		$txt_process_id=implode(",",$txt_process_id); 
		
		$field_array2="id, mst_id, batch_id,production_id, width_dia_type, order_id, product_type, process, fabric_description, cons_comp_id, color_id, gsm, dia_width, product_qnty, reject_qnty, no_of_roll, floor_id, machine_id,shift,uom_id,body_part_id,buyer_po_id,remarks, inserted_by, insert_date";
		$data_array2="(".$id1.",".$id.",".$txt_batch_id.",".$txt_production_id.",".$hidden_dia_type.",".$order_no_id.",'".$process_finishing."','".$txt_process_id."',".$txt_description.",".$comp_id.",".$hidden_color_id.",".$txt_gsm.",".$txt_dia_width.",".$txt_qc_qnty.",".$txt_reject_qty.",".$txt_roll_no.",".$cbo_floor_name.",".$cbo_machine_id.",".$cboShift.",".$cbo_uom.",".$cbo_body_part.",".$txt_buyer_po_id.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
		//echo "INSERT INTO subcon_production_mst (".$field_array.") VALUES ".$data_array; die;
		
		
		/*//---------------Check Duplicate product in Same return number ------------------------//
		$duplicate = is_duplicate_field("order_id","subcon_production_dtls"," order_id=$order_no_id  and mst_id=$update_id and status_active=1 and  status_active=1"); 
		
		//echo "10**"; 
		//echo $duplicate; die;
		if($duplicate==1) 
		{
			echo "20**Duplicate Order  is Not Allow in Same MRR Number.";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}*/
		
		$rID2=sql_insert("subcon_production_dtls",$field_array2,$data_array2,0);   

		//echo "10**".$rID."**".$rID2; die;

		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$txt_production_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$txt_production_id);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$txt_production_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$txt_production_id);
			}
		}	
		
		disconnect($con);
		die;
	}
	else if ($operation==1)// Update Here==============================================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$process_finishing="4";
		
		
			
		$production_qty_array=array();
		$updateiddtl=str_replace("'","",$update_id_dtl);
		$prod_sql="Select a.batch_id, a.cons_comp_id, sum(a.product_qnty) as product_qnty from subcon_production_dtls a,subcon_production_mst b where  a.mst_id=b.id and a.production_id=$txt_production_id and a.status_active=1 and a.is_deleted=0 and b.entry_form=294 and a.id not in($updateiddtl) group by  a.batch_id, a.cons_comp_id"; 
		
		
		
		//echo "11**".$prod_sql="Select a.batch_id, a.cons_comp_id, sum(a.product_qnty) as product_qnty from subcon_production_dtls a,subcon_production_mst b where  a.mst_id=b.id and a.production_id=$txt_production_id and a.status_active=1 and a.is_deleted=0 and b.entry_form=294 and a.id not in($updateiddtl) group by  a.batch_id, a.cons_comp_id"."$updateiddtl".$updateiddtl; die;
		
			//$sql = "select b.id,b.fabric_description,b.color_id,b.product_qnty,b.process,b.no_of_roll,b.floor_id,b.shift,b.uom_id,b.dia_width,b.gsm,b.order_id,b.batch_id,b.body_part_id,b.buyer_po_id from  subcon_production_mst a, subcon_production_dtls b where a.id in($data[0]) and a.id=b.mst_id and a.entry_form=291 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		
		// Validation off for micro fiber . concern of Sayeed vai
		/*
		$prod_data_sql=sql_select($prod_sql);
		foreach($prod_data_sql as $row)
		{
			$production_qty_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]=$row[csf('product_qnty')];
		}
		$batchid=str_replace("'","",$txt_batch_id);
		$conscompid=str_replace("'","",$comp_id);
		$total_qc_production_qty=$production_qty_array[$batchid][$conscompid];//$txt_batch_id  $comp_id; die;
		if($total_qc_production_qty){$total_qc_production_qty=$total_qc_production_qty;}else {$total_qc_production_qty=0;}
		$productQty = return_field_value("sum(a.product_qnty) as production_qty",  "subcon_production_dtls a,subcon_production_mst b","a.mst_id=$txt_production_id and a.mst_id=b.id and a.status_active=1 and  a.is_deleted=0 and b.entry_form=291","production_qty");
		$totalqcproduction_qty=$total_qc_production_qty;
		$blanceqty=($productQty-$totalqcproduction_qty);
		$qcqnty=str_replace("'","",$txt_qc_qnty);
		//echo "11**Qc Quantity Over Production Quantity".$totalqcproduction_qty; die; 
		if($qcqnty>$blanceqty)
		{
			echo "11**Qc Quantity Over Production Quantity  "."(Bal. Qty =".$blanceqty.")";
			die;
		}*/
		
		$field_array="product_no*basis*company_id*location_id*party_id*product_date*prod_chalan_no*updated_by*update_date";
		$data_array="".$txt_qc_id."*".$cbo_receive_basis."*".$cbo_company_id."*".$cbo_location_name."*".$cbo_party_name."*".$txt_qc_date."*".$txt_chal_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$rID=sql_update("subcon_production_mst",$field_array,$data_array,"id",$update_id,0);
		
		//$machine_tbl_id=return_field_value("id","lib_machine_name", "machine_no=$cbo_machine_id and status_active =1 and is_deleted=0" );
		//$color_tbl_id = return_id( str_replace("'","",$txt_color), $color_library_arr, "lib_color", "id,color_name");
		$txt_process_id=explode(",",str_replace("'","",$txt_process_id));
		asort($txt_process_id);
		$txt_process_id=implode(",",$txt_process_id);
		$field_array2="batch_id*width_dia_type*order_id*production_id*uom_id*process*fabric_description*cons_comp_id*color_id*gsm*dia_width*product_qnty*reject_qnty*no_of_roll*floor_id*machine_id*body_part_id*buyer_po_id*remarks*updated_by*update_date";

		$data_array2="".$txt_batch_id."*".$hidden_dia_type."*".$order_no_id."*".$txt_production_id."*".$cbo_uom."*'".$txt_process_id."'*".$txt_description."*".$comp_id."*".$hidden_color_id."*".$txt_gsm."*".$txt_dia_width."*".$txt_qc_qnty."*".$txt_reject_qty."*".$txt_roll_no."*".$cbo_floor_name."*".$cbo_machine_id."*".$cbo_body_part."*".$txt_buyer_po_id."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		//echo $data_array2;
		$rID2=sql_update("subcon_production_dtls",$field_array2,$data_array2,"id",$update_id_dtl,0);  
		
		//==========================================================================================
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_qc_id)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$txt_production_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_qc_id)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$txt_production_id);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_qc_id)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$txt_production_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_qc_id)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$txt_production_id);
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
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("subcon_production_dtls",$field_array,$data_array,"id","".$update_id_dtl."",1);
			
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_qc_id)."**".str_replace("'",'',$update_id_dtl);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_qc_id)."**".str_replace("'",'',$update_id_dtl);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="subcon_fabric_finishing_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$machineArr=return_library_array( "select id, machine_no from  lib_machine_name", "id", "machine_no"  );
	
	$sql=" select id, product_no, basis, location_id, party_id, product_date, prod_chalan_no, remarks from subcon_production_mst where product_no='$data[1]'";
	$dataArray=sql_select($sql);

	$mst_id=$dataArray[0][csf('id')];
	$sqldtls=" select a.id, a.batch_id, a.order_id, a.process, a.fabric_description, a.color_id, a.gsm, a.dia_width, a.product_qnty, a.machine_id, a.no_of_roll from  subcon_production_dtls a where a.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0";
	
	$sql_result=sql_select($sqldtls);

	foreach ($sql_result as $value) 
	{
		$order_dtls_ids.=$value[csf('order_id')].',';
	}
	$order_dtls_Ids = rtrim($order_dtls_ids,',');

	$sql_aop_order=sql_select("select a.party_id, a.within_group from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form=278 and a.status_active=1 and b.status_active=1 and b.id in($order_dtls_Ids) group by a.party_id, a.within_group");
	$aop_order_within_group_val=$sql_aop_order[0][csf('within_group')];


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
            <td width="125"><strong>Party Name:</strong></td><td width="175px"><? if ($aop_order_within_group_val==1) echo $company_library[$dataArray[0][csf('party_id')]]; else echo $buyer_arr[$dataArray[0][csf('party_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Finishing Date:</strong></td> <td width="175px"><? echo change_date_format($dataArray[0][csf('product_date')]); ?></td>
            <td><strong>Challan No :</strong></td><td colspan="3"><? echo $dataArray[0][csf('prod_chalan_no')];// ?></td>
        </tr>
        <tr>
            <td><strong>Remarks:</strong></td><td colspan="5"><? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table>
         <br>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="70" align="center">Batch No</th>
                <th width="80" align="center">Order No</th>
                <th width="150" align="center">Process</th>
                <th width="160" align="center">Const. Compo.</th>
                <th width="60" align="center">Color</th> 
                <th width="60" align="center">GSM</th>
                <th width="90" align="center">Dia/Width</th>
                <th width="60" align="center">Roll</th>
                <th width="80" align="center">Product Qty</th>
                <th width="" align="center">Machine No</th>                   
            </thead>
   <?
	
    $i=1;
	$poArr=return_library_array( "select id, order_no from subcon_ord_dtls", "id", "order_no");
	$batchArr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");
		
	foreach($sql_result as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$order_id=explode(",",$row[csf('order_id')]);
			$process=explode(",",$row[csf('process')]);
			$po_no=''; $process_arr='';
			//$data=explode('*',$data);
			
			foreach($order_id as $val)
			{
				if($po_no=='') $po_no=$poArr[$row[csf('order_id')]]; else $po_no.=", ".$poArr[$row[csf('order_id')]];
			}
			foreach($process as $val)
			{
				if($process_arr=='') $process_arr=$conversion_cost_head_array[$val]; else $process_arr.=", ".$conversion_cost_head_array[$val];
			}
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td width="30"><? echo $i; ?></td>
                <td width="70"><p><? echo $batchArr[$row[csf('batch_id')]]; ?></p></td>
                <td width="80"><p><? echo $po_no; ?></p></td>
                <td width="150"><p><? echo $process_arr; ?></p></td>
                <td width="160"><p><? echo $row[csf('fabric_description')]; ?></p></td>
                <td width="60"><p><? echo $color_name_arr[$row[csf('color_id')]]; ?></p></td>
                <td width="60"><p><? echo $row[csf('gsm')]; ?></p></td>
                <td width="90"><p><? echo $row[csf('dia_width')]; ?></p></td>
                <td width="60" align="right"><? echo number_format($row[csf('no_of_roll')],2,'.',''); $total_roll+=$row[csf('no_of_roll')]; ?></td>
                <td width="80" align="right"><? echo number_format($row[csf('product_qnty')],2,'.',''); $total_qty+=$row[csf('product_qnty')]; ?></td>
                <td width=""><p><? echo $machineArr[$row[csf('machine_id')]]; ?></p></td>
			</tr>
			<?php
				$uom_unit="Kg";
				$uom_gm="Grams";
			$i++;
			}
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
            echo signature_table(45, $data[0], "930px");
         ?>
	</div>
	</div>
<?
exit();
}


if($action=="check_qty_is_mandatory")
{
	//$data=explode("_",$data);
	$dyeing_fin_bill=return_field_value( "dyeing_fin_bill","variable_settings_subcon","company_id=$data and variable_list='14'");
	echo $dyeing_fin_bill;
	exit();	
}
?>
