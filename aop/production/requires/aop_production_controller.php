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
	echo create_drop_down( "cbo_location_name", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/aop_production_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/aop_production_controller', document.getElementById('cbo_company_id').value+'_'+this.value+'_'+0, 'load_drop_down_machine', 'machine_td' );" );	
}

if ($action=="load_drop_down_floor")
{
	$ex_data=explode('_',$data);
	echo create_drop_down( "cbo_floor_name", 140, "select id,floor_name from lib_prod_floor where company_id=$ex_data[0] and location_id=$ex_data[1] and status_active =1 and is_deleted=0 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/aop_production_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value+'_'+this.value, 'load_drop_down_machine', 'machine_td' );" );
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
/*if ($action=="load_drop_down_party_name")
{
	echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",4 ); 
	exit();
}
*/
if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	/*if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";*/
	if($data[1]==1)
	{
		//echo "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name"; 
		echo create_drop_down( "cbo_party_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --","", "$load_function",1);
	}
	else
	{
		//echo "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name";
		echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --","", "",1 );
	}	
	exit();	 
} 




if ($action=="finishing_id_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$ex_data=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('finishing_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
        <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>                	 
                <th width="120">Company Name</th>
                <th width="70">Production ID</th>
                <th width="70">Batch No.</th>
                <th width="70">Design No.</th>
                <th width="70">AOP Ref.</th>
                <th width="150">Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>           
            </thead>
            <tbody>
                <tr>
                    <td> <input type="hidden" id="finishing_id">  
						<?   
							echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"",0 );
                        ?>
                    </td>
                    <td>
                        <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:70px" />
                    </td>
                    <td>
                        <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:70px" />
                    </td>
                    <td>
                        <input type="text" name="txt_design_no" id="txt_design_no" class="text_boxes" style="width:70px" />
                    </td>
                    <td>
                        <input type="text" name="txt_aop_ref" id="txt_aop_ref" class="text_boxes" style="width:90px" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td> 
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_aop_ref').value+'_'+document.getElementById('txt_batch_no').value+'_'+document.getElementById('txt_design_no').value, 'fabric_finishing_id_search_list_view', 'search_div', 'aop_production_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7" align="center" height="40" valign="middle">
						<? echo load_month_buttons(1);  ?>
                    </td>
                </tr>
                
            </tbody>
        </table>    
        <div id="search_div"></div>
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
	if ($data[6]!='') $design_no_cond =" and b.design_no like '%$data[6]%'"; else $design_no_cond="";
	//if ($data[4]!=0) $buyer_cond=" and a.party_id='$data[4]'"; else $buyer_cond="";

	//$return_to=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$batch_array=array();
	$main_batch_allow = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id =$data[0] and variable_list=13 and is_deleted=0 and status_active=1");
	$entry_form_cond='';
	if($main_batch_allow==1) $entry_form_cond=" and entry_form in (0,281) and process_id like '%35%'"; else $entry_form_cond="and entry_form =281 ";
	$batch_id_sql="select id,within_group,company_id, batch_no, extention_no from pro_batch_create_mst where  status_active=1 and is_deleted=0 $entry_form_cond";
	//echo $entry_form_cond; die;
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
	if ($data[4]!='') $aop_ref_cond= " and a.aop_reference like '%$data[4]%'"; else $aop_ref_cond="";	
	if($aop_ref_cond!='' || $design_no_cond!='')
	{
		$ord_sql = "select b.id,a.subcon_job,a.aop_reference,b.order_no,b.design_no from subcon_ord_mst a ,subcon_ord_dtls b where  company_id =$data[0] $aop_ref_cond $design_no_cond and a.entry_form=278 and  a.subcon_job=b.job_no_mst";
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array(); $design_no_arr=array();
		foreach ($ordArray as $row)
		{
			$po_arr[$row[csf('id')]] = $row[csf('order_no')];
			$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
			$design_no_arr[$row[csf('id')]] = $row[csf('design_no')];
			$po_id[] .= $row[csf("id")];
		}
		$po_id_cond=" and b.order_id in ('".implode("','",$po_id)."') ";
	} 
	else
	{
		$ord_sql = "select b.id,a.subcon_job,a.aop_reference,b.order_no,b.design_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$data[0] and a.entry_form=278 and  a.subcon_job=b.job_no_mst $design_no_cond";
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array(); $design_no_arr=array();
		foreach ($ordArray as $row)
		{
			$po_arr[$row[csf('id')]] = $row[csf('order_no')];
			$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
			$design_no_arr[$row[csf('id')]] = $row[csf('design_no')];
		}
		//$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
		$po_id_cond='';
	}

	$batchIds='';
	if($data[5]!='')
	{
		if($db_type==0) $id_cond="group_concat(id) as id";
		else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,id,',').extract('//text()') order by id).GetClobVal(),',') as id";
		//echo "select $id_cond from pro_batch_create_mst where batch_no like '%$data[5]%' $entry_form_cond";
		$batchIds = return_field_value("$id_cond", "pro_batch_create_mst", " batch_no like '%$data[5]%' $entry_form_cond ", "id");
	}
	
	if($db_type==2 && $batchIds!="") $batchIds = $batchIds->load();
	//var_dump($batchIds);
	//print_r($batchIds);
	if ($batchIds!="")
	{
		$batchIds=explode(",",$batchIds);
		$batchIdsCond=""; $poIdsCond="";
		//echo count($batchIds); die;
		if($db_type==2 && count($batchIds)>=999)
		{
			$chunk_arr=array_chunk($batchIds,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode("','",$val);
				if($batchIdsCond=="")
				{
					$batchIdsCond.=" and ( b.batch_id in ( '$ids') ";
				}
				else
				{
					$batchIdsCond.=" or  b.batch_id in ( '$ids') ";
				}
			}
			$batchIdsCond.="')";
		}
		else
		{
			$ids=implode("','",$batchIds);
			$batchIdsCond.=" and b.batch_id in ('$ids') ";
		}
		//echo $po_ids."==";
	}
	else if($batchIds=="" && $data[5]!='')
	{
		echo "Not Found"; die;
	}

	//echo $po_id_cond;
	//echo "<pr>";
	//print_r($ref_arr);
	$sql= "select a.id, product_no, a.prefix_no_num, $year_cond, a.party_id, a.product_date, a.prod_chalan_no, $batch_cond, $order_cond, sum(b.product_qnty) as product_qnty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=291 and a.status_active=1 $company_name $buyer_cond $production_date_cond $product_id_cond $po_id_cond $batchIdsCond group by a.id, product_no, a.prefix_no_num, a.insert_date, a.party_id, a.product_date, a.prod_chalan_no order by a.id DESC";
	
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
                <th width="150" >Production ID</th>
                <th width="60" >Year</th>
                <th width="70" >Prod. Date</th>
                <th width="100" >Batch</th>
                <th width="100" >Design No</th>
                <th width="100" >AOP Ref.</th>
                <th>Prod. Qty</th>
            </thead>
     	</table>
     </div>
     <div style="width:750px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_po_list">
			<?
			$result_sql= sql_select($sql);
			$i=1;
            foreach($result_sql as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				$batch_id=array_unique(explode(",",$row[csf("batch_id")])); 
				$aop_ref=''; $batch_no=""; $design_no="";
				foreach($order_id as $val)
				{
					//echo $aop_ref."=";
					if($aop_ref=="") $aop_ref=$ref_arr[$val]; else $aop_ref.=",".$ref_arr[$val];
					if($design_no=="") $design_no=$design_no_arr[$val]; else $design_no.=",".$design_no_arr[$val];
				}
				$aop_ref=implode(",",array_unique(explode(",",$aop_ref)));
				 
				
				foreach($batch_id as $key)
				{
					if($batch_no=="") $batch_no=$batch_array[$key]['batch_no']; else $batch_no.=", ".$batch_array[$key]['batch_no'];
				}
				$batch_no=implode(",",array_unique(explode(",",$batch_no)));
				if($main_batch_allow==1)
				{
					$within_group=1;
				}
				else
				{
					$within_group=$batch_array[$key]['within_group'];
				}
				$data = $row[csf("id")] . "_" . $within_group. "_" . $batch_array[$key]['company_id'];
				//echo $data."=="; 
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $data;?>');" > 
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="150" align="center"><? echo $row[csf("product_no")]; ?></td>
                        <td width="60" align="center"><? echo $row[csf("year")]; ?></td>		
						<td width="70"><? echo change_date_format($row[csf("product_date")]); ?></td>
						<td width="100"><p><? echo $batch_no; ?></p></td>
						<td width="100"><p><? echo $design_no; ?></p></td>
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

if ($action=="load_php_data_to_form_mst")
{
	$data=explode("_",$data);
	$nameArray=sql_select( "select a.id, a.product_no, a.basis, a.company_id, a.location_id, a.party_id, a.product_date, a.prod_chalan_no, a.remarks from subcon_production_mst a where a.entry_form=291 and a.id='$data[0]'" ); 

	foreach ($nameArray as $row)
	{	
		//$company_id=$row[csf("company_id")];
		echo "document.getElementById('txt_finishing_id').value 			= '".$row[csf("product_no")]."';\n";
		echo "document.getElementById('cbo_receive_basis').value			= '".$row[csf("basis")]."';\n"; 
		
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/aop_production_controller', $data[2]+'_'+$data[1], 'load_drop_down_buyer', 'buyer_td' );";
		echo "document.getElementById('cbo_party_name').value				= '".$row[csf("party_id")]."';\n";
 
		echo "document.getElementById('txt_production_date').value 			= '".change_date_format($row[csf("product_date")])."';\n";   
		echo "document.getElementById('txt_chal_no').value					= '".$row[csf("prod_chalan_no")]."';\n"; 
		echo "document.getElementById('txt_remarks').value					= '".$row[csf("remarks")]."';\n"; 
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
		echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_party_name*txt_batch_no',1);\n";
		
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
            <table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <tr>
                        <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                	<tr>               	 
                        <th width="150">Company Name</th>
                        <th width="120">Batch No</th>
                        <th width="120">Design No</th>
                        <th width="120">AOP Ref.</th>
                        <th width="187">Date Range</th>
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
                            <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:107px" />
                        </td>
                        <td>
                            <input type="text" name="txt_design_no" id="txt_design_no" class="text_boxes" style="width:107px" />
                        </td>
                        <td>
                            <input type="text" name="txt_aop_ref" id="txt_aop_ref" class="text_boxes" style="width:107px" />
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:75px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:75px">
                        </td> 
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_aop_ref').value+'_'+document.getElementById('txt_design_no').value, 'batch_search_list_view', 'search_div', 'aop_production_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" height="40" valign="middle">
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
	$search_str =$data[5];
	$design_no =$data[6];

	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	//$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');

	if ($design_no!='') $design_no_cond =" and b.design_no like '%$data[6]%'"; else $design_no_cond="";
	
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
		if ($data[5]!='') $aop_ref_cond= " and a.aop_reference='$data[5]'"; else $aop_ref_cond="";	
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($data[3]!='') $batch_no_cond=" and a.batch_no like '%$data[3]%'"; else $batch_no_cond="";
		if ($data[5]!='') $aop_ref_cond= " and a.aop_reference like '%$data[5]%'"; else $aop_ref_cond="";
	}
	else if($search_type==2)
	{
		if ($data[3]!='') $batch_no_cond=" and a.batch_no like '$data[3]%'"; else $batch_no_cond="";
		if ($data[5]!='') $aop_ref_cond= " and a.aop_reference like '$data[5]%'"; else $aop_ref_cond="";
	}
	else if($search_type==3)
	{
		if ($data[3]!='') $batch_no_cond=" and a.batch_no like '%$data[3]'"; else $batch_no_cond="";
		if ($data[5]!='') $aop_ref_cond= " and a.aop_reference like '%$data[5]'";else $aop_ref_cond="";
	}
	$main_batch_allow = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id =$data[0] and variable_list=13 and is_deleted=0 and status_active=1");
	$entry_form_cond='';
	if($main_batch_allow==1)
	{
		$ord_sql = "select id, booking_no from wo_booking_mst where status_active=1"; //$company_id a.booking_no=b.booking_no and b.process=35 and a.booking_type=3 and a.pay_mode in (3,5) and a.lock_another_process!=1
		$ordArray=sql_select( $ord_sql ); $main_po_arr=array();
		foreach ($ordArray as $row)
		{
			$main_po_arr[$row[csf('id')]]['order'] = $row[csf('booking_no')];
		}
		$entry_form_cond=" and a.entry_form in(0,281) and a.process_id like '%35%' ";
	}
	else
	{
		$entry_form_cond="and a.entry_form =281 ";
	}
	//echo $aop_ref_cond; die;
	if($aop_ref_cond!='' || $design_no_cond!='')
	{
		$ord_sql = "select b.id,a.subcon_job,a.aop_reference,b.order_no,b.design_no from subcon_ord_mst a ,subcon_ord_dtls b where  company_id =$data[0] $aop_ref_cond and a.entry_form=278 and  a.subcon_job=b.job_no_mst $design_no_cond";
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array(); $design_no_arr=array();
		foreach ($ordArray as $row)
		{
			$po_arr[$row[csf('id')]] = $row[csf('order_no')];
			$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
			$design_no_arr[$row[csf('id')]] = $row[csf('design_no')];
			$po_id[] .= $row[csf("id")];
		}
		$po_id_cond=" and b.po_id in (".implode(",",$po_id).") ";
	} 
	else
	{
		$ord_sql = "select b.id,a.subcon_job,a.aop_reference,b.order_no,b.design_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$data[0] and a.entry_form=278 and  a.subcon_job=b.job_no_mst $design_no_cond";
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array(); $design_no_arr=array();
		foreach ($ordArray as $row)
		{
			$po_arr[$row[csf('id')]] = $row[csf('order_no')];
			$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
			$design_no_arr[$row[csf('id')]] = $row[csf('design_no')];
		}
		//$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
		$po_id_cond='';
	}
	
	
	        $order_buyer_po_array=array();
			$buyer_po_arr=array();
			$order_buyer_po='';
			$order_sql ="select b.id,b.buyer_po_no,b.buyer_style_ref,buyer_po_id, b.design_no from subcon_ord_mst a, subcon_ord_dtls b where a.company_id =$data[0] and a.entry_form=278 and  a.subcon_job=b.job_no_mst $design_no_cond"; 
			$order_sql_res=sql_select($order_sql);
			foreach ($order_sql_res as $row)
			{
				$order_buyer_po_array[]=$row[csf("id")];
				$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
				$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
				$buyer_po_arr[$row[csf("id")]]['buyer_po_id']=$row[csf("buyer_po_id")];
				$design_no_arr[$row[csf('id')]] = $row[csf('design_no')];
			}
			unset($order_sql_res);
			//$order_buyer_po=implode(",",$order_buyer_po_array);
			//echo $order_buyer_po; 
			//if ($order_buyer_po!="") $order_order_buyer_poCond=" and b.job_dtls_id in ($order_buyer_po)"; else $order_order_buyer_poCond="";
	
	
	
	

	/*$buyer_po_arr=array();
	$po_sql ="Select a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);*/
	if($db_type==0)
	{
		$sql="select a.id,a.entry_form, a.batch_no, a.extention_no, a.color_id, a.batch_weight, a.total_liquor,a.location_id,a.party_id,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id, sum(b.batch_qnty) as batch_qnty, b.po_id ,b.buyer_po_id,c.id as recipe_id,c.recipe_no from pro_batch_create_mst a, pro_batch_create_dtls b,pro_recipe_entry_mst c where a.id=b.mst_id and a.id=c.batch_id and a.status_active=1 and c.status_active=1 $entry_form_cond $company_con $batch_date_cond $batch_no_cond $po_id_cond group by a.entry_form,a.batch_no, a.extention_no, b.po_id,b.buyer_po_id,c.id,c.recipe_no order by a.id DESC"; // and a.batch_against=1
	}
	else if($db_type==2)
	{
		$sql="select a.id, a.entry_form, a.batch_no, a.extention_no, a.color_id, a.batch_weight, a.total_liquor,a.location_id,a.party_id,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id, sum(b.batch_qnty) as batch_qnty,b.po_id,b.buyer_po_id,c.id as recipe_id,c.recipe_no from pro_batch_create_mst a,pro_batch_create_dtls b,pro_recipe_entry_mst c where a.id=b.mst_id and a.id=c.batch_id and a.status_active=1 and c.status_active=1 $entry_form_cond $company_con $batch_date_cond $batch_no_cond $po_id_cond group by a.id,a.entry_form , a.batch_no, a.extention_no, a.color_id, a.batch_weight, a.total_liquor,a.location_id,a.party_id,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id, b.po_id,b.buyer_po_id,c.id,c.recipe_no order by a.id DESC";// and a.batch_against=1
	}
	//echo $sql; die;
	$result = sql_select($sql);
	$batch_id_arr=array();
	foreach ($result as $row) {
		$batch_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
	$batch_id_arr = array_unique($batch_id_arr);

	$batch_con=where_con_using_array($batch_id_arr,0,"a.id");


	$prod_sql= "select a.id, b.batch_id, b.product_qnty from pro_batch_create_mst a, subcon_production_dtls b where a.id=b.batch_id $company_con $batch_con";
	//echo $prod_sql; die;
	$prod_sql_result = sql_select($prod_sql);

	$prod_qnty_arr=array();
	foreach ($prod_sql_result as $row) {
		$prod_qnty_arr[$row[csf("id")]]+=$row[csf("product_qnty")];
	}

	/*echo "<pre>";
	print_r($prod_qnty_arr); die;*/

	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
            <thead>
                <th width="20" >SL</th>
                <th width="90" >Recipe No.</th>
                <th width="90" >Batch No.</th>
                <th width="90" >Design No.</th>
                <th width="60" >Batch Ext.</th>
                <th width="90" >Batch Color</th>
                <th width="60" >Batch Qty</th>
                <th width="80" >Prod. Qty</th>
                <th width="90" >Work Order No.</th>
                <th width="90" >Buyer PO</th>
                <th>AOP Ref.</th>
            </thead>
     	</table>
     <div style="width:860px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="list_view">
			<?
			$i=1; //$batch_type= array(0 =>"Main Batch" ,281 =>"AOP Batch");
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//$order_id=explode(',',$row[csf("po_id")]);
				$order_no=''; $order_id='';  $order_ids='';  $aop_jobs=''; $aop_job=''; $buyer_job=''; $buyer_po=''; $buyer_style=''; $all_ref_arr=array(); $all_design_no_arr=array();
				$order_no=''; $ref_no=''; $design_no=''; $buyer_po=''; $buyerpoid='';
				$order_id=array_unique(explode(",",$row[csf("po_id")]));	
				foreach($order_id as $val)
				{
					//echo $val."==".$row[csf("is_sales")];
					if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
					if($aop_jobs=="") $aop_jobs=$po_arr[$val]['job']; else $aop_jobs.=", ".$po_arr[$val]['job'];
					if($order_ids=="") $order_ids=$val; else $order_ids.=", ".$val;
					
					//$buyer_po=$buyer_po_arr[$val]['po'];
					$all_ref_arr[] .= $ref_arr[$val];
					$all_design_no_arr[] .= $design_no_arr[$val];
					if($row[csf("entry_form")]==0)
					{
						if($row[csf("is_sales")]==1)
						{
							$buyer_po=$row[csf("sales_order_no")];
							$order_no=$row[csf("booking_no")];
						}
						else
						{
							$buyer_po=$buyer_po_arr[$val]['po'];
							$buyer_job=$buyer_po_arr[$val]['job'];
							$buyer_style=$buyer_po_arr[$val]['style'];
							$buyerpoid=$buyer_po_arr[$val]['buyer_po_id'];
						}
					}
					else
					{
						$buyer_po=$buyer_po_arr[$val]['po'];
					    $buyer_job=$buyer_po_arr[$val]['job'];
						$buyer_style=$buyer_po_arr[$val]['style'];
						$buyerpoid=$buyer_po_arr[$val]['buyer_po_id'];
						
						//$buyer_job=$buyer_po_arr[$row[csf("buyer_po_id")]]['job'];
						//$buyer_po=$buyer_po_arr[$row[csf("buyer_po_id")]]['po'];
						//$buyer_style=$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
					}
				}
				$aop_job=implode(", ",array_unique(explode(", ",$aop_jobs)));
				$order_no=implode(", ",array_unique(explode(", ",$order_no)));
				$buyer_job=implode(", ",array_unique(explode(", ",$buyer_job)));
				$buyer_po=implode(", ",array_unique(explode(", ",$buyer_po)));
				$buyerpoid=implode(", ",array_unique(explode(", ",$buyerpoid)));
				$buyer_style=implode(", ",array_unique(explode(", ",$buyer_style)));
				$ref_no = implode(",", array_unique($all_ref_arr));
				$design_no = implode(",", array_unique($all_design_no_arr));
				//if($buyer_po=="") $buyer_po_arr[$val]['po']; else $buyer_po.=",".$buyer_po_arr[$val]['po'];
				//$buyer_po=$buyer_po_arr[$row[csf("buyer_po_id")]]['po'];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")]."_".$ref_no."_".$row[csf("recipe_id")]."_".$buyer_po."_".$buyer_style."_".$buyerpoid;?>');" >
						<td width="20" align="center"><? echo $i; ?></td>
						<td width="90" align="center"><p><? echo $row[csf("recipe_no")]; ?></p></td>
						<td width="90" align="center"><p><? echo $row[csf("batch_no")]; ?></p></td>
						<td width="90" align="center"><p><? echo $design_no; ?></p></td>
                        <td width="60" align="center"><? echo $row[csf("extention_no")]; ?></td>
                        <td style="word-wrap:break-word; word-break:break-word;width:100px;" align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
						<td width="60" align="right"><? echo number_format($row[csf("batch_qnty")],2); ?></td>
						<td width="80" align="right"><? echo number_format($prod_qnty_arr[$row[csf("id")]],2); ?></td>
						<td width="90"><p><? echo $order_no; ?></p></td>
						<td width="90" ><p><? echo $buyer_po; ?></p></td>
						<td><p><? echo $ref_no; ?></p></td>
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
	
	
	$data=explode('_',$data);
	$Buyer_PO=$data[1];
	$Buyer_style=$data[2];
	$Buyer_PO_id=$data[3];
	
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	$job_no_arr=return_library_array( "select id,job_no_mst from subcon_ord_dtls",'id','job_no_mst');
	$process_arr=return_library_array( "select id,main_process_id from subcon_ord_dtls",'id','main_process_id');
	$party_id_arr=return_library_array( "select subcon_job,party_id from subcon_ord_mst",'subcon_job','party_id');
	$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	
	//echo "select variable_dtls from variable_setting_aop a where company_name =$data[4] and variable_list=1 and is_deleted=0 and status_active=1";
	$control_based_on_chemical_issue = return_field_value("variable_dtls", "variable_setting_aop", "company_name =$data[4] and variable_list=1 and is_deleted=0 and status_active=1");
	if($control_based_on_chemical_issue==1) // Yes
	{
		//echo 'yes';
		// echo "select batch_no from inv_issue_master where batch_no='$batch_id' and issue_basis=7 and entry_form=5 and is_deleted=0 and status_active=1";
		//$chemical_issue_batch = return_field_value("batch_no", "inv_issue_master", "batch_no like '%$batch_id%' and issue_basis=7 and entry_form=5 and is_deleted=0 and status_active=1","batch_no");
		$chemical_issue_batch = return_field_value("batch_id", "inv_issue_master", "batch_id =$data[0] and issue_basis=7 and entry_form=308 and is_deleted=0 and status_active=1","batch_id");

		
		if ($chemical_issue_batch=="") 
		{
			echo "document.getElementById('hidden_control_chemical_issue').value 	= '20';\n"; die;
		}
		else
		{
			echo "document.getElementById('hidden_control_chemical_issue').value 	= '';\n";
		}
	}


	if ($db_type == 2)
	{
		$group_concat1 = ", listagg(b.width_dia_type,',') within group (order by b.width_dia_type) as width_dia_type";
		$group_concat2 = ", listagg(cast(b.po_id AS VARCHAR2(4000)),',') within group (order by b.id) as po_id";
	}  
	else if ($db_type == 0) 
	{
		$group_concat2 = ", group_concat(b.po_id) as po_id"; 
		$group_concat1 = ", group_concat(b.width_dia_type) as width_dia_type";
	}

	if($db_type==0)
	{
		$sql="SELECT a.id,a.entry_form,a.sales_order_no,a.booking_no ,a.sales_order_id ,a.is_sales, a.batch_no, a.extention_no, a.color_id, a.process_id,a.within_group,a.party_id,a.company_id,a.floor_id,a.machine_no,a.location_id ,a.shift_id, a.print_type, a.design_number, a.coverage, b.po_id $group_concat1  
		from pro_batch_create_mst a,pro_batch_create_dtls b 
		where a.id=b.mst_id and a.id='$data[0]'  
		group by a.batch_no, a.extention_no, a.print_type, a.design_number, a.coverage, b.po_id " ;
	}
	elseif($db_type==2)
	{
		$sql="SELECT a.id,a.entry_form ,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id, a.batch_no, a.extention_no, a.color_id, a.process_id,a.within_group,a.party_id,a.company_id,a.floor_id,a.machine_no,a.location_id ,a.shift_id, a.print_type, a.design_number, a.coverage, b.po_id  $group_concat1  
		from pro_batch_create_mst a,pro_batch_create_dtls b 
		where a.id=b.mst_id and a.id='$data[0]' 
		group by a.id, a.entry_form, a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id, a.batch_no, a.extention_no, a.color_id, a.process_id,a.within_group,a.party_id,a.company_id,a.floor_id,a.machine_no,a.location_id ,a.shift_id, a.print_type, a.design_number, a.coverage, b.po_id ";
	}
	//echo $sql; die;
	$nameArray=sql_select($sql);
	foreach ($nameArray as $row)
	{	
		//$order_id_hidde=implode(",",array_unique(explode(",",$row[csf("po_id")])));	
		$order_no=''; $main_process_id=''; $process_name=''; $party_id_array=''; 
		$company_id=$row[csf("company_id")]; 
		
		if($row[csf("entry_form")]==0)
		{
			$within_group=1;
			if($row[csf("is_sales")]==1)
			{
				$sales_order_id=$row[csf("sales_order_id")]; 
				$data_array = sql_select("select a.id, a.job_no, a.company_id, a.within_group, a.sales_booking_no, a.booking_id, a.booking_date, a.delivery_date, a.buyer_id, a.style_ref_no, a.location_id, a.ship_mode,a.season_id, a.team_leader, a.dealing_marchant, a.remarks, a.currency_id, a.season,a.booking_without_order,a.booking_type,a.booking_approval_date,a.ready_to_approved,a.is_approved,a.booking_entry_form,a.po_job_no, b.fabric_source,b.is_approved booking_is_approved,b.item_category,listagg(c.po_break_down_id, ',') within group (order by c.po_break_down_id) as po_break_down_id from fabric_sales_order_mst a left join wo_booking_mst b on a.booking_id=b.id left join wo_booking_dtls c on c.booking_no=b.booking_no where a.id=$sales_order_id group by a.id, a.job_no, a.company_id, a.within_group, a.sales_booking_no, a.booking_id, a.booking_date, a.delivery_date, a.buyer_id, a.style_ref_no, a.location_id, a.ship_mode,a.season_id, a.team_leader, a.dealing_marchant, a.remarks, a.currency_id, a.season,a.booking_without_order,a.booking_type,a.booking_approval_date,a.ready_to_approved,a.is_approved,a.booking_entry_form,a.po_job_no, b.fabric_source,b.is_approved,b.item_category");
				$buyer=$data_array[0][csf("buyer_id")];
				$location=$data_array[0][csf("location_id")];
			}
			else
			{
				$buyer=$row[csf("party_id")];
				$location=$row[csf("location_id")];
			}
		}
		else
		{
			$within_group=$row[csf("within_group")];
			$buyer=$row[csf("party_id")];
			$location=$row[csf("location_id")];
		}
		
		$datas=$company_id."_".$within_group;
		$order_id_hidde=implode(",",array_unique(explode(",",$row[csf("po_id")])));	
		echo "document.getElementById('txt_batch_no').value				= '".$row[csf("batch_no")]."';\n"; 
		echo "document.getElementById('txt_batch_id').value				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_batch_ext_no').value			= '".$row[csf("extention_no")]."';\n"; 
		//set_multiselect('txt_process_id','0','0','','0');
		echo "set_multiselect('txt_process_id','0','1','".$row[csf("process_id")]."','0');\n";
		echo "document.getElementById('order_no_id').value				= '".$order_id_hidde."';\n";
		echo "document.getElementById('txt_color').value				= '".$color_arr[$row[csf("color_id")]]."';\n"; 
		echo "document.getElementById('txt_design_number').value		= '".$row[csf("design_number")]."';\n"; 
		echo "document.getElementById('txt_coverage').value				= '".$row[csf("coverage")]."';\n"; 
		echo "document.getElementById('txt_print_type').value			= '".$row[csf("print_type")]."';\n"; 
		echo "document.getElementById('hidden_color_id').value			= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('hidden_dia_type').value			= '".$row[csf("width_dia_type")]."';\n";
		echo "document.getElementById('cbo_location_name').value		= '".$location."';\n";
		
		//echo "document.getElementById('txt_buyer_po').value		= '".$Buyer_PO."';\n"; 
		//echo "document.getElementById('txt_buyer_po_id').value		= '".$Buyer_PO_id."';\n";
		//echo "document.getElementById('txt_buyer_style').value		= '".$Buyer_style."';\n";
		
		echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_party_name',1);\n";
		
		echo "load_drop_down( 'requires/aop_production_controller','$datas' , 'load_drop_down_buyer', 'buyer_td' );";
		echo "document.getElementById('cbo_party_name').value			= '".$buyer."';\n";

		echo "load_drop_down( 'requires/aop_production_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor_name').value 			= '".$row[csf("floor_id")]."';\n";
		
		echo "load_drop_down( 'requires/aop_production_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_floor_name').value, 'load_drop_down_machine', 'machine_td' );";
		echo "document.getElementById('cbo_machine_id').value 			= '".$row[csf("machine_no")]."';\n";

		//echo "document.getElementById('cboShift').value				= '".$row[csf("shift_id")]."';\n";
		//echo "document.getElementById('cbo_party_name').value 		= '".$row[csf("party_id")]."';\n";
		//echo "document.getElementById('txt_order_numbers').value		= '".$order_no."';\n";
		//echo "document.getElementById('txt_process_name').value		= '".$process_name."';\n"; 
		//echo "document.getElementById('cbo_party_name').value			= '".$party_id_array."';\n"; 
		//echo "document.getElementById('txt_process_id').value			= '".$row[csf("process_id")]."';\n"; 
		//echo "document.getElementById('process_id').value				= '".$main_process_id."';\n";  
	}
	exit();  
}

if($action=="show_fabric_desc_listview")
{
	//echo $data; die;
	$data=explode('_',$data);

	$order_id=$data[0];
	$process_id=$data[1];
	$company_id=$data[3];
	$Buyer_PO=$data[4];
	//echo "select id, style_ref_no from fabric_sales_order_mst where po_id in ($data[0])";die;
	//$batch_arr=return_library_array( "select id, prod_id, item_description from lib_subcon_charge",'id','const_comp');	
	$gsm_arr=return_library_array( "select id,gsm from lib_subcon_charge",'id','gsm');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$style_array=return_library_array( "select id, style_ref_no from fabric_sales_order_mst where id in ($data[0])",'id','style_ref_no');
	$uom_array=return_library_array( "select id, unit_of_measure from product_details_master ",'id','unit_of_measure');

	$production_qty_array=array();
	//$prod_sql="Select batch_id, cons_comp_id, sum(product_qnty) as product_qnty from subcon_production_dtls where batch_id='$data[2]' and entry_form=291 and status_active=1 and is_deleted=0 group by  batch_id, cons_comp_id";
		$prod_sql="Select a.batch_id, a.cons_comp_id, sum(a.product_qnty) as product_qnty from subcon_production_dtls a , subcon_production_mst b where b.id=a.mst_id and b.entry_form in (291) and  a.batch_id='$data[2]' and a.status_active=1 and a.is_deleted=0 group by  a.batch_id, a.cons_comp_id";
	$prod_data_sql=sql_select($prod_sql);
	foreach($prod_data_sql as $row)
	{
		$production_qty_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]] +=$row[csf('product_qnty')];
	}


          $order_buyer_po_array=array();
			$buyer_po_arr=array();
			$order_buyer_po='';
			$order_sql ="select b.id,b.buyer_po_no,b.buyer_style_ref,buyer_po_id from subcon_ord_mst a, subcon_ord_dtls b where a.company_id =$data[3] and a.entry_form=278 and  a.subcon_job=b.job_no_mst"; 
			$order_sql_res=sql_select($order_sql);
			foreach ($order_sql_res as $row)
			{
				$order_buyer_po_array[]=$row[csf("id")];
				$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
				$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
				$buyer_po_arr[$row[csf("id")]]['buyer_po_id']=$row[csf("buyer_po_id")];
			}
			unset($order_sql_res);


	/*$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number,a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")]; entry_form=291
	}
	unset($po_sql_res);*/
	//var_dump($production_qty_array);
	$main_batch_allow = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id =$company_id and variable_list=13 and is_deleted=0 and status_active=1");
	$entry_form_cond='';
	if($main_batch_allow==1) $entry_form_cond=" and a.entry_form in(0,281) and a.process_id like '%35%' "; else $entry_form_cond="and a.entry_form =281 ";

	$variable_set_production=sql_select("select distribute_qnty, auto_update, production_entry from variable_settings_production where variable_list =51 and company_name=$company_id and item_category_id=3 and status_active=1");

	$over_receive_limit_qnty_kg=$over_receive_limit=0;
	if($variable_set_production[0][csf('production_entry')]*1 >0)
	{
		$over_receive_limit_qnty_kg = $variable_set_production[0][csf('production_entry')];
		$over_receive_caption = " Over receive limit Qnty ".$over_receive_limit_qnty_kg." KG";
	}
	else if($variable_set_production[0][csf('distribute_qnty')]*1 >0)
	{
		$over_receive_limit = $variable_set_production[0][csf('distribute_qnty')];
		$over_receive_caption = " Over receive limit Perc. ".$over_receive_limit." %";
	}

	$sql = "SELECT  a.id as batch_id,a.entry_form, a.batch_no, a.extention_no, a.color_id,a.style_ref_no,a.is_sales,a.sales_order_no,a.booking_no ,a.sales_order_id, a.print_type, a.design_number, a.coverage, b.id, b.po_id, b.buyer_po_id, b.item_description, b.body_part_id,roll_no, b.batch_qnty as qnty,b.prod_id 
	from  pro_batch_create_mst a, pro_batch_create_dtls b 
	where a.id='$data[2]' and a.id=b.mst_id $entry_form_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	// and b.po_id in ($data[0]) group by a.batch_no, a.extention_no, a.color_id, b.id, b.prod_id, b.item_description 
	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="460">
        <thead>
            <th width="15">SL</th>
            <th>Fabric, GSM, G/Dia, F/Dia</th>
            <th width="60">Color</th>
            <th width="60">Buyer PO</th>
            <th width="60">Batch Qty</th>
            <th width="40">Prod. Qty</th>
            <th width="60">Bal. Qty</th>
        </thead>
        <tbody>
            <? 
            $i=1; $total_qty=$total_production_qty=$total_balance=0;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $desc=explode(",",$row[csf('item_description')]); 

				$batch_qnty_with_over_val=0;
				if($over_receive_limit_qnty_kg > 0)
				{
					$batch_qnty_with_over_val = $row[csf('qnty')] + $over_receive_limit_qnty_kg;
				}
				else
				{
					$batch_qnty_with_over_val = $row[csf('qnty')] + ($row[csf('qnty')] * $over_receive_limit)/100;
				}

				//$balance=$row[csf('qnty')]-$production_qty_array[$row[csf('batch_id')]][$row[csf('id')]];
				$balance=$batch_qnty_with_over_val-$production_qty_array[$row[csf('batch_id')]][$row[csf('id')]];

                
                $po_id=$row[csf('po_id')];
               	if($row[csf("entry_form")]==0)
				{
					$within_group=1;
					$po=$row[csf("booking_no")];
					if($row[csf("is_sales")]==1)
					{
						$style_ref_no=$style_array[$row[csf("sales_order_id")]];
						$uom_id=$uom_array[$row[csf("prod_id")]];
					}
					else
					{
						//$style_ref_no=$row[csf("style_ref_no")];
						//$po=$buyer_po_arr[$row[csf('buyer_po_id')]]['po'];
						$po=$buyer_po_arr[$row[csf('po_id')]]['po'];
						$style_ref_no=$buyer_po_arr[$row[csf('po_id')]]['style'];
						$buyerpoid=$buyer_po_arr[$row[csf('po_id')]]['buyer_po_id'];
					}
				}
				else
				{
					$within_group=$row[csf("within_group")];
					//$style_ref_no=$row[csf("style_ref_no")];
					//$po=$buyer_po_arr[$row[csf('buyer_po_id')]]['po'];
					$po=$buyer_po_arr[$row[csf('po_id')]]['po'];
					$style_ref_no=$buyer_po_arr[$row[csf('po_id')]]['style'];
					$buyerpoid=$buyer_po_arr[$row[csf('po_id')]]['buyer_po_id'];
					$uom_id=return_field_value("order_uom","subcon_ord_dtls","id='$po_id' and status_active=1 and is_deleted=0 group by order_uom",'order_uom');
					//$location=$row[csf("location_id")];
				}
				
				
				
				$po=implode(", ",array_unique(explode(", ",$po)));
				$buyerpoid=implode(", ",array_unique(explode(", ",$buyerpoid)));
				$style_ref_no=implode(", ",array_unique(explode(", ",$style_ref_no)));
				
             	?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('id')]."**".$desc[0].",".$desc[1]."**".$desc[2]."**".$desc[3]."**".$row[csf('color_id')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('po_id')]."**".$row[csf('body_part_id')]."**".$po."**".$style_ref_no."**".$row[csf('buyer_po_id')]."**".$row[csf('roll_no')]."**".$uom_id."**".$balance."**".$row[csf('is_sales')]."**".$row[csf('print_type')]."**".$row[csf('design_number')]."**".$row[csf('coverage')]; ?>")' style="cursor:pointer" >
                    <td><? echo $i; ?></td>
                    <td><p><? echo $row[csf('item_description')]; ?></p></td>
                    <td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td><p><? echo $po;//$po; ?></p></td>
                    <td align="right"><? echo number_format($row[csf('qnty')],2,'.',''); $total_qty+=$row[csf('qnty')];?></td>
                    <td align="right"><? echo number_format($production_qty_array[$row[csf('batch_id')]][$row[csf('id')]],2,'.',''); $total_production_qty+=$production_qty_array[$row[csf('batch_id')]][$row[csf('id')]];?></td>
                    <td align="right" title="<? echo $over_receive_caption;?>"><? echo number_format( $balance,2,'.',''); $total_balance+=$balance; ?></td>
                </tr>
	            <? 
	            $i++; 
            } 
            ?>
        </tbody>
        <tfoot>
            <th colspan="4" align="right"><strong>Total :</strong></th>
            <th align="right"><? echo number_format($total_qty,2,'.',''); ?></th>
            <th align="right"><? echo number_format($total_production_qty,2,'.',''); ?></th>
            <th align="right"><? echo number_format( $total_balance,2,'.',''); ?></th>
        </tfoot>
    </table>
<?    
	exit();
}

if($action=="process_name_popup")
{
  	echo load_html_head_contents("Process Name Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
	
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		
		var selected_id = new Array(); var selected_name = new Array(); var buyer_id=''; var style_ref_array= new Array();
		
		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function set_all()
		{
			var old=document.getElementById('txt_process_row_id').value; 
			if(old!="")
			{   
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] ) 
				} 
			}
		}
		
		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			//id.sort();
			
			$('#hidden_process_id').val(id);
			$('#hidden_process_name').val(name);
		}
    </script>

</head>
<body>
<div align="center">
	<fieldset style="width:370px;margin-left:10px">
    	<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
        <input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th>Process Name</th>
                </thead>
            </table>
            <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
                <?
                    $i=1; $process_row_id=''; $not_process_id_print_array=array();
					//$process_id_print_array=array(25,31,32,33,34,39,60,63,64,65,66,67,68,69,70,71,82,83,84,89,90,91,93,125,129,132,133,136,137,147,148,149,150,151,127,35,80);
					
					//$process_id_print_array=array(35,133,148,150,207,84,156,209,93,220,221,230,231,232,233,234,235,236,237);
					$hidden_process_id=explode(",",$txt_process_id);
                    foreach($conversion_cost_head_array as $id=>$name)
                    {
						//if(in_array($id,$process_id_print_array))
						//{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							 
							if(in_array($id,$hidden_process_id)) 
							{ 
								if($process_row_id=="") $process_row_id=$i; else $process_row_id.=",".$i;
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
								<td width="50" align="center"><?php echo "$i"; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>	
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
								</td>	
								<td><p><? echo $name; ?></p></td>
							</tr>
							<?
							$i++;
						//}
                    }
                ?>
                    <input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>
                </table>
            </div>
             <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%"> 
                            <div style="width:50%; float:left" align="left">
                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                            </div>
                            <div style="width:50%; float:left" align="left">
                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
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

		function fnc_qty_validation(qty,batch_qty,i)
		{
			if(qty>batch_qty)
			{
				alert("Production Qty can not exceed Batch Qty"+"\n");
				document.getElementById('orderqnty_'+i).value='';
				return;
			}
		}
	</script>
	<head>
	<body>
        <form name="searchfrm_1"  id="searchfrm_1">
        <div style="margin-left:10px; margin-top:10px" align="center">
            <table class="rpt_table" id="tbl_qnty" border="1" cellpadding="0" cellspacing="0" rules="all" width="400">
                <thead>
                	<? //echo $data;
                	//1964_67768__2_44.4514_0 
                	$data=explode('_',$data);
                	if($data[5]!=1)
                	{
                		?>
                		<th width="150">Work Order No</th>
                		<?
                	}
                	?>
                    <th width="150">Production Qty</th>
                </thead>
                <tbody>
					<? 
                    if($data[1]=="")
                    {
						$i=1;
						//echo $data[0];
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
                            	<? if($data[5]!=1)
			                	{
			                		?>
			                		<td width="150">
										<? echo $order_name[$break_order_id[$k]]; ?>
                                	</td>
			                		<?
			                	}
			                	?>
                                <td width="150" align="center">
                                    <input type="text" name="orderqnty_<? echo $i; ?>" id="orderqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:140px;"  value="<? //echo $break_order_qnty[$k]; ?>" onKeyup="fnc_qty_validation(this.value,<? echo $data[4]; ?>,<? echo $i; ?>)"; />
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
							//echo $data[4]."==";
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
                                	<? if($data[5]!=1)
				                	{
				                		?>
				                		<td width="150">
											<? echo $order_name[$break_order_id[$k]]; ?>
                                    	</td>
				                		<?
				                	}
				                	?>
                                    <td width="150" align="center">
                                        <input type="text" name="orderqnty_<? echo $i; ?>" id="orderqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:140px;"  value="<? //echo $break_order_qnty[$k]; ?>" onKeyup="fnc_qty_validation(this.value,<? echo $data[4]; ?>,<? echo $i; ?>)";/>
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
							//echo "select id,order_id,quantity from subcon_production_qnty where dtls_id='$data[1]' and order_id in ($data[0])";
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
                                        <input type="text" name="orderqnty_<? echo $i; ?>" id="orderqnty_<? echo $i; ?>" value="<? //echo $row[csf('quantity')]; ?>"  onKeyup="fnc_qty_validation(this.value,<? echo $data[4]; ?>,<? echo $i; ?>)"; class="text_boxes_numeric" style="width:140px;" />
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
                <th width="90" align="center">Process</th>
                <th width="60" align="center">Batch No</th>
                <th width="80" align="center">Work Order No</th>                    
                <th width="150" align="center">Const. and Compo.</th>
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
			$main_batch_allow = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id =$data[1] and variable_list=13 and is_deleted=0 and status_active=1");
			$entry_form_cond='';
			if($main_batch_allow==1) $entry_form_cond=" entry_form in(0,281) and process_id like '%35%'"; else $entry_form_cond=" entry_form =281 ";
			//echo $entry_form_cond; die;
			$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
			//$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
			$batch_id_sql="select id,within_group,company_id, batch_no, extention_no from pro_batch_create_mst where $entry_form_cond and status_active=1 and is_deleted=0";
			$batch_id_sql_result=sql_select($batch_id_sql);
			foreach ($batch_id_sql_result as $row)
			{
				$batch_array[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
				$batch_array[$row[csf("id")]]["extention_no"]=$row[csf("extention_no")];
				$batch_array[$row[csf("id")]]["within_group"]=$row[csf("within_group")];
				$batch_array[$row[csf("id")]]["company_id"]=$row[csf("company_id")];
			}
			$machine_arr=return_library_array( "select id,machine_no from  lib_machine_name",'id','machine_no');
			$sql ="select id, batch_id, order_id, buyer_po_id, process, fabric_description, color_id, gsm, dia_width, no_of_roll, product_qnty, machine_id from subcon_production_dtls where status_active=1 and mst_id='$data[0]'"; 
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
					$within_group=$batch_array[$key]['within_group'];
				}
				$click_data=$row[csf('id')]."_".$within_group."_".$batch_array[$row[csf("batch_id")]]["company_id"];
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="get_php_form_data('<? echo $click_data ?>','load_php_data_to_form_dtls','requires/aop_production_controller');" style="text-decoration:none; cursor:pointer" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="90" align="center"><p><? echo $process_val; ?></p></td>
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
                    <td width="" align="center"><p><? echo $machine_arr[$row[csf('machine_id')]]; ?></p></td>
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
	//$order_arr=return_library_array("select id,order_no from subcon_ord_dtls",'id','order_no');
	$process_arr=return_library_array("select id,main_process_id from subcon_ord_dtls",'id','main_process_id');
	$color_no_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	//echo $data[2]; die;
	$main_batch_allow = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id =$data[2] and variable_list=13 and is_deleted=0 and status_active=1");
	$entry_form_cond=''; 
	if($main_batch_allow==1) $entry_form_cond=" and a.entry_form in(0,281) and a.process_id like '%35%'"; else $entry_form_cond=" and a.entry_form =281 ";

	$batch_array=array();
	$batch_id_sql="select a.id,a.within_group , a.batch_no, a.extention_no,b.body_part_id from pro_batch_create_mst a , pro_batch_create_dtls b where a.id=b.mst_id $entry_form_cond and a.status_active=1 and a.is_deleted=0";
	$batch_id_sql_result=sql_select($batch_id_sql);
	foreach ($batch_id_sql_result as $row)
	{
		$batch_array[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$batch_array[$row[csf("id")]]["extention_no"]=$row[csf("extention_no")];
		$batch_array[$row[csf("id")]]["within_group"]=$row[csf("within_group")];
		//$batch_array[$row[csf("id")]]["within_group"]=$row[csf("within_group")];
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

	
	// b.batch_qnty|| '_' ||sum(product_qnty) as product_qnty 
	unset($prod_data_sql);
	$sql= "select id, batch_id, width_dia_type, order_id, process, fabric_description, cons_comp_id, color_id, gsm, dia_width, product_qnty, reject_qnty, no_of_roll, floor_id, machine_id, start_hour, start_minutes, start_date, end_hour, end_minutes, end_date, buyer_po_id, shift, uom_id, body_part_id, remarks, print_type, design_number, coverage from subcon_production_dtls where id='$data[0]'";
	$nameArray=sql_select($sql);
	$production_qty_array=array();
	foreach($prod_data_sql as $row)
	{
		$production_qty_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]=$row[csf('product_qnty')];
	}
	foreach ($nameArray as $row)
	{
		$orderId=$row[csf("order_id")]; $orderNo=''; $aopReference='';
		/*$order_id=explode(',',$orderId);
		$order_no=''; 
		foreach($order_id as $okey)
		{
			if($order_no=="") $order_no=$order_arr[$okey]; else $order_no .=",".$order_arr[$okey];
		}*/
		$ord_sql = "select b.order_no,a.aop_reference,b.buyer_po_no,b.buyer_style_ref from subcon_ord_mst a ,subcon_ord_dtls b where b.id in($orderId) and a.entry_form=278 and  a.subcon_job=b.job_no_mst";
		$orderArray=sql_select($ord_sql);
		foreach($orderArray as $okey)
		{
			$order_no=$okey[csf("order_no")].",";
			$aop_reference=$okey[csf("aop_reference")].",";
			$buyer_po_no=$okey[csf("buyer_po_no")].",";
			$buyer_style_ref=$okey[csf("buyer_style_ref")].",";
		}
		$orderNo=implode(",",array_unique(explode(",",$order_no)));
		$aopReference=implode(",",array_unique(explode(",",$aop_reference)));
		$buyerpono=implode(",",array_unique(explode(",",$buyer_po_no)));
		$buyerstyleref=implode(",",array_unique(explode(",",$buyer_style_ref)));

		//$po=$buyer_po_arr[$row[csf('buyer_po_id')]]['po'];
		//$style_ref_no=$buyer_po_arr[$row[csf('buyer_po_id')]]['style'];
		$po=chop($buyerpono,",");
		$style_ref_no=chop($buyerstyleref,",");
		
		$batch_id=$row[csf("batch_id")];
		$batch_qnty = return_field_value("sum(batch_qnty) as batch_qnty", "pro_batch_create_dtls", "status_active=1 and mst_id=$batch_id","batch_qnty");
		//echo $batch_qnty."**"; die;
		$balance=$batch_qnty-$production_qty_array[$row[csf('batch_id')]][$row[csf('id')]];
		echo "document.getElementById('txt_batch_no').value		 				= '".$batch_array[$batch_id]["batch_no"]."';\n";
		echo "document.getElementById('txt_batch_ext_no').value		 			= '".$batch_array[$batch_id]["extention_no"]."';\n";
		echo "document.getElementById('txt_batch_id').value		 				= '".$batch_id."';\n";
		echo "set_multiselect('txt_process_id','0','1','".$row[csf("process")]."','0');\n";
		echo "document.getElementById('hidden_dia_type').value		 			= '".$row[csf("width_dia_type")]."';\n";
		echo "document.getElementById('order_no_id').value						= '".$row[csf("order_id")]."';\n"; 
		echo "document.getElementById('comp_id').value							= '".$row[csf("cons_comp_id")]."';\n"; 
		//echo "document.getElementById('item_order_id').value					= '".$row[csf("order_id")]."';\n"; 
		 
		echo "document.getElementById('txt_description').value					= '".$row[csf("fabric_description")]."';\n";
		echo "document.getElementById('txt_color').value		 				= '".$color_no_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('hidden_color_id').value		 			= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('cbo_body_part').value		 			= '".$row[csf("body_part_id")]."';\n";
		echo "document.getElementById('cboShift').value		 					= '".$row[csf("shift")]."';\n";
		echo "document.getElementById('cbo_uom').value		 					= '".$row[csf("uom_id")]."';\n";
		echo "document.getElementById('txt_gsm').value		 					= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_dia_width').value		 			= '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_product_qnty').value            		= '".$row[csf("product_qnty")]."';\n";
		echo "document.getElementById('txt_reject_qty').value            		= '".$row[csf("REJECT_QNTY")]."';\n";
		echo "document.getElementById('txt_roll_no').value            			= '".$row[csf("NO_OF_ROLL")]."';\n";
		echo "document.getElementById('txt_buyer_po_id').value					= '".$row[csf("buyer_po_id")]."';\n";
		echo "document.getElementById('txt_buyer_po').value            			= '".$po."';\n";
		echo "document.getElementById('txt_buyer_style').value            		= '".$style_ref_no."';\n";
		echo "document.getElementById('txt_remarks').value            			= '".$row[csf("remarks")]."';\n";

		echo "document.getElementById('txt_print_type').value            		= '".$row[csf("print_type")]."';\n";
		echo "document.getElementById('txt_design_number').value            	= '".$row[csf("design_number")]."';\n";
		echo "document.getElementById('txt_coverage').value            			= '".$row[csf("coverage")]."';\n";
		echo "document.getElementById('txt_batch_qty').value            		= '".$balance."';\n";

		echo "load_drop_down( 'requires/aop_production_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor_name').value 			= '".$row[csf("floor_id")]."';\n";
		
		echo "load_drop_down( 'requires/aop_production_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_floor_name').value, 'load_drop_down_machine', 'machine_td' );";
		echo "document.getElementById('cbo_machine_id').value 			= '".$row[csf("machine_no")]."';\n";
		echo "show_list_view(document.getElementById('order_no_id').value+'_'+document.getElementById('process_id').value+'_'+document.getElementById('txt_batch_id').value+'_'+document.getElementById('cbo_company_id').value,'show_fabric_desc_listview','list_fabric_desc_container','requires/aop_production_controller','');\n";
		
		echo "document.getElementById('cbo_machine_id').value		 	= '".$row[csf("machine_id")]."';\n"; 
		echo "document.getElementById('update_id_dtl').value            = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_aop_ref').value            = '".chop($aopReference,",")."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'subcon_fabric_finishing',1);\n";
		
		/*echo "load_drop_down( 'requires/aop_production_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_floor', 'floor_td' );\n";
		echo "document.getElementById('cbo_floor_name').value		 			= '".$row[csf("floor_id")]."';\n"; 
		echo "load_drop_down( 'requires/aop_production_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_floor_name').value, 'load_drop_machine', 'machine_td');\n";*/
		//echo "document.getElementById('txt_order_numbers').value		 		= '".$order_no."';\n";

		/*echo "load_drop_down( 'requires/aop_production_controller', $data[2]+'_'+$data[1], 'load_drop_down_buyer', 'buyer_td' );";
		echo "document.getElementById('cbo_party_name').value			= '".$row[csf("party_id")]."';\n";*/
	}
	$qry_result=sql_select( "select id, order_id,quantity from subcon_production_qnty where dtls_id='$data[0]'");// and quantity!=0
	$order_qnty=""; $order_id="";
	foreach ($qry_result as $row)
	{
		if($order_qnty=="") $order_qnty=$row[csf("quantity")]; else $order_qnty.=",".$row[csf("quantity")];
		if($order_id=="") $order_id=$row[csf("order_id")]; else $order_id.=",".$row[csf("order_id")];
	}
	echo "document.getElementById('item_order_id').value 	 				= '".$order_id."';\n";
	echo "document.getElementById('txt_receive_qnty').value 	 			= '".$order_qnty."';\n";
	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'subcon_fabric_finishing',1);\n";
	exit();	
}

$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$process_finishing="0";
	
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
		//txt_production_id
		$new_return_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '','AOPP', date("Y",time()), 5, "select id,prefix_no,prefix_no_num from  subcon_production_mst where entry_form=291 and company_id=$cbo_company_id  $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		if(str_replace("'",'',$update_id)=="")
		{			
			$field_array="id,entry_form,prefix_no,prefix_no_num,product_no,product_type,basis,company_id,location_id,party_id,product_date,prod_chalan_no,remarks,inserted_by,insert_date";
			$id=return_next_id( "id","subcon_production_mst",1); 
			$data_array="(".$id.",291,'".$new_return_no[1]."','".$new_return_no[2]."','".$new_return_no[0]."','".$process_finishing."',".$cbo_receive_basis.",".$cbo_company_id.",".$cbo_location_name.",".$cbo_party_name.",".$txt_production_date.",".$txt_chal_no.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$rID=sql_insert("subcon_production_mst",$field_array,$data_array,0);
			$return_no=$new_return_no[0];
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="product_no*basis*location_id*party_id*product_date*prod_chalan_no*remarks*updated_by*update_date";
			$data_array="".$txt_finishing_id."*".$cbo_receive_basis."*".$cbo_location_name."*".$cbo_party_name."*".$txt_production_date."*".$txt_chal_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("subcon_production_mst",$field_array,$data_array,"id",$update_id,0); 
			$return_no=str_replace("'",'',$txt_finishing_id);
		}
		
		$id1=return_next_id("id","subcon_production_dtls",1);
		//$machine_tbl_id=return_field_value("id","lib_machine_name", "machine_no=$cbo_machine_id and status_active =1 and is_deleted=0" );
		//$color_tbl_id = return_id( str_replace("'","",$txt_color), $color_library_arr, "lib_color", "id,color_name");
		
		$txt_process_id=explode(",",str_replace("'","",$txt_process_id));
		asort($txt_process_id);
		$txt_process_id=implode(",",$txt_process_id);
		
		$field_array2="id, mst_id, batch_id, width_dia_type, order_id, product_type, process, fabric_description, cons_comp_id, color_id, gsm, dia_width, product_qnty, reject_qnty, no_of_roll, floor_id, machine_id,shift,uom_id,buyer_po_id,body_part_id, remarks, print_type, design_number, coverage, inserted_by, insert_date";
		$data_array2="(".$id1.",".$id.",".$txt_batch_id.",".$hidden_dia_type.",".$order_no_id.",'".$process_finishing."','".$txt_process_id."',".$txt_description.",".$comp_id.",".$hidden_color_id.",".$txt_gsm.",".$txt_dia_width.",".$txt_product_qnty.",".$txt_reject_qty.",".$txt_roll_no.",".$cbo_floor_name.",".$cbo_machine_id.",".$cboShift.",".$cbo_uom.",".$txt_buyer_po_id.",".$cbo_body_part.",".$txt_remarks.",".$txt_print_type.",".$txt_design_number.",".$txt_coverage.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
		//echo "INSERT INTO subcon_production_dtls (".$field_array2.") VALUES ".$data_array2; die;
		
		//---------------Check Duplicate product in Same return number ------------------------//
		/*$duplicate = is_duplicate_field("order_id","subcon_production_dtls"," order_id=$order_no_id  and mst_id=$update_id and status_active=1 and  status_active=1"); 
		
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
		//===========================================================================================================================================
		$product_type='0';
		$data_array3="";
		$order_no=explode(',',str_replace("'","",$item_order_id));
		$receive_qnty=explode(',',str_replace("'","",$txt_receive_qnty));
		
		for($i=0; $i<count($order_no); $i++)
		{
			$receive_qty='';
			if($receive_qnty[$i]=='')
			{
				$receive_qty=0;	
			}
			else
			{
				$receive_qty=$receive_qnty[$i];
			}
			if($id_prod_qnty=="") $id_prod_qnty=return_next_id( "id", "subcon_production_qnty",1); else $id_prod_qnty=$id_prod_qnty+1;
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array3.="$add_comma(".$id_prod_qnty.",".$id.",".$id1.",".$order_no[$i].",".$receive_qty.",'".$product_type."')";
		}
		$field_array3="id,mst_id,dtls_id,order_id,quantity,product_type";
		if($data_array3!="")
		{
			//echo "INSERT INTO subcon_production_qnty (".$field_array3.") VALUES ".$data_array3; die;
			$rID3=sql_insert("subcon_production_qnty",$field_array3,$data_array3,1);
		}
		//===========================================================================================================================================
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2 && $rID3)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id1);
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
		$process_finishing="4";
		
		$field_array="product_no*basis*company_id*location_id*party_id*product_date*prod_chalan_no*remarks*updated_by*update_date";
		$data_array="".$txt_finishing_id."*".$cbo_receive_basis."*".$cbo_company_id."*".$cbo_location_name."*".$cbo_party_name."*".$txt_production_date."*".$txt_chal_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$rID=sql_update("subcon_production_mst",$field_array,$data_array,"id",$update_id,0);
		
		//$machine_tbl_id=return_field_value("id","lib_machine_name", "machine_no=$cbo_machine_id and status_active =1 and is_deleted=0" );
		//$color_tbl_id = return_id( str_replace("'","",$txt_color), $color_library_arr, "lib_color", "id,color_name");
		$txt_process_id=explode(",",str_replace("'","",$txt_process_id));
		asort($txt_process_id);
		$txt_process_id=implode(",",$txt_process_id);
		$field_array2="batch_id*width_dia_type*order_id*process*fabric_description*cons_comp_id*color_id*gsm*dia_width*product_qnty*reject_qnty*no_of_roll*floor_id*machine_id*shift*uom_id*buyer_po_id*body_part_id*remarks*print_type*design_number*coverage*updated_by*update_date";
		$data_array2="".$txt_batch_id."*".$hidden_dia_type."*".$order_no_id."*'".$txt_process_id."'*".$txt_description."*".$comp_id."*".$hidden_color_id."*".$txt_gsm."*".$txt_dia_width."*".$txt_product_qnty."*".$txt_reject_qty."*".$txt_roll_no."*".$cbo_floor_name."*".$cbo_machine_id."*".$cboShift."*".$cbo_uom."*".$txt_buyer_po_id."*".$cbo_body_part."*".$txt_remarks."*".$txt_print_type."*".$txt_design_number."*".$txt_coverage."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo $data_array2;
		$rID2=sql_update("subcon_production_dtls",$field_array2,$data_array2,"id",$update_id_dtl,0);
		//===========================================================================================================================================
		if(str_replace("'","",$update_id_qnty)!="")
		{
			$update_qnty=explode(',',str_replace("'","",$update_id_qnty));
			$receive_qnty=explode(',',str_replace("'","",$txt_receive_qnty));
			
			for($i=0; $i<count($update_qnty); $i++)
			{
				if($update_qnty[$i]!=="")
				{
					$update_arr[]=$update_qnty[$i];
					$data_array_up[str_replace("'",'',$update_qnty[$i])] =explode(",",("'".$receive_qnty[$i]."'"));
				}
			}
			$field_array_up="quantity";
			$rID3=execute_query(bulk_update_sql_statement( "subcon_production_qnty","id",$field_array_up,$data_array_up,$update_arr));
		}
		else
		{
			$rID4=execute_query( "delete from subcon_production_qnty where dtls_id=$update_id_dtl",1);
			$data_array3="";
			$order_no=explode(',',str_replace("'","",$item_order_id));
			$receive_qnty=explode(',',str_replace("'","",$txt_receive_qnty));
			
			for($i=0; $i<count($order_no); $i++)
			{
				$receive_qty='';
				if($receive_qnty[$i]=='')
				{
					$receive_qty=0;	
				}
				else
				{
					$receive_qty=$receive_qnty[$i];
				}
				
				
				if($id_prod_qnty=="") $id_prod_qnty=return_next_id( "id", "subcon_production_qnty", 1 ); else $id_prod_qnty=$id_prod_qnty+1;
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array3.="$add_comma(".$id_prod_qnty.",".$update_id.",".$update_id_dtl.",".$order_no[$i].",".$receive_qty.",'".$process_finishing."')";
			}
			$field_array3="id,mst_id,dtls_id,order_id,quantity,product_type";
			if($data_array3!="")
			{
				//echo "INSERT INTO subcon_production_qnty (".$field_array3.") VALUES ".$data_array3; die;
				$rID3=sql_insert("subcon_production_qnty",$field_array3,$data_array3,1);
			}
		}
		//=========================================================================================================================================== 
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id)."**".str_replace("'",'',$update_id_dtl);
			}
			else if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id)."**".str_replace("'",'',$update_id_dtl);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id)."**".str_replace("'",'',$update_id_dtl);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2 && $rID3 )
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id);
			}
			else if($rID && $rID2)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id);
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
				echo "2**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id)."**".str_replace("'",'',$update_id_dtl);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_finishing_id)."**".str_replace("'",'',$update_id_dtl);
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

	foreach ($sql_result as $value) {
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
                <th width="80" align="center">Work Order No</th>
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
?>
