<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 

if ($action=="max_sequence_no")
{
	echo return_field_value("max(row_sequence_no) as seq_no","ppl_gsd_entry_dtls","mst_id=$data  and is_deleted=0","seq_no")+1;exit();
}

if ($action=="load_drop_down_resource")
{
	echo create_drop_down( "cbo_resource", 100, "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID=$data order by RESOURCE_NAME","RESOURCE_ID,RESOURCE_NAME", 1, "-- Select --", $selected, "" );  
	exit();
}

if ($action=="load_drop_down_resource_tc")
{
	echo create_drop_down( "cbo_resource_tc", 130, "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID=$data order by RESOURCE_NAME","RESOURCE_ID,RESOURCE_NAME", 1, "-- Select --", $selected, "");  
	exit();
}


if ($action=="load_drop_down_buyer")
{
	if($data != 0){$where_con = " and b.tag_company='$data'";}
	echo create_drop_down( "cbo_buyer_name", 135, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $where_con $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	exit();
}
 
 
if ($action=="systemid_popup")
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1,'',1,'');
	extract($_REQUEST);
	if($cbo_company_id !=0 ){$where_con = " and b.tag_company=$cbo_company_id";}
    ?>
	<script>

	let fn_generate_report = ()=>{
		
		if( form_validation('cbo_company_id', 'Company Name Required')==false && form_validation('cbo_buyer_name', 'Buyer Name Required')==false && form_validation('cbo_gmt_item', 'Cbo Gmt Item')==false
		&& form_validation('txt_search_common', 'txt search common')==false && form_validation('txt_internal_ref', 'txt internal ref')==false
		&& form_validation('txt_search_prod', 'txt search prod')==false && form_validation('txt_system_id', 'txt system id prod')==false)
		{
			alert(11111111);
			alert("Please one field required");
			return;
		}
		else{ 
			show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_gmt_item').value+'_'+document.getElementById('txt_system_id').value+'_'+document.getElementById('txt_search_prod').value+'_'+document.getElementById('cbo_bulletin_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_id').value, 'systemId_list_view', 'search_div', 'ws_gsd_controller', 'setFilterGrid(\'list_view\',-1)');
		}
	}
	function js_set_value(id)
	{   
		// alert(id);
		// 1829_65_MO-QQ_180_1_51_51_42_17.54_14.6_2.94__1829__1__Delower/WA__0_2__4_22-06-2023_0_444_0_8_32488_MF-22-00970
		document.getElementById('system_id').value=id;
		parent.emailwindow.hide();
	}
	</script>  
</head>
<body>
    <div align="center" style="width:100%;" >
        <form name="system_1" id="system_1" autocomplete="off">
            <table width="960" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                	<tr>
                		<th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",4); ?></th>
                	</tr>
                	<tr>                	 
	                    <th>Company Name</th>
	                    <th>Buyer Name</th>
	                    <th>Garments Item</th>
	                    <th>Style Ref.</th>
	                    <th>Internal Ref</th>
	                    <th>Bulletin Type</th>
	                    <th>Prod Description</th>
	                    <th>System ID</th>
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    </tr>           
                </thead>
                <tr class="general">
					<td>
						<?
							echo create_drop_down( "cbo_company_id", 140,"select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- All company --", $cbo_company_id, "load_drop_down( 'ws_gsd_controller', this.value, 'load_drop_down_buyer', 'buyer_td');" );
						?>
					</td>
                    <td id="buyer_td">
						<?= create_drop_down( "cbo_buyer_name", 135, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  $buyer_cond $where_con and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                        ?>
                    </td>
                    <td>
                        <input type="hidden" id="system_id" style="width:100px;">
                        <?= create_drop_down( "cbo_gmt_item", 120, $garments_item,'', 1, "-Select Gmt. Item-","","","","" ); ?>
                    </td>
                    <td><input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" placeholder="Write"/></td>
                    <td><input type="text" name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:128px" placeholder="Write"/></td> 
                    <td><?= create_drop_down( "cbo_bulletin_type", 120, $bulletin_type_arr,'', 1, "-Select Bulletin Type-","","","","" );?></td>
                    <td><input type="text" style="width:120px" class="text_boxes" name="txt_search_prod" id="txt_search_prod" placeholder="Write"/></td>
                    <td><input type="text" style="width:100px" class="text_boxes_numeric" name="txt_system_id" id="txt_system_id" placeholder="Write"/></td>
                    <td align="center"><input type="button" name="button" class="formbutton" value="Show" onClick="fn_generate_report();" style="width:100px;"/></td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:5px"></div>
        </form>
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
    exit();
}

if ($action=="systemId_list_view")
{
	$data = explode('_',$data);
 
	$buyer_name_arr = return_library_array( "select id,short_name from lib_buyer", "id","short_name"  );
	$user_arr = return_library_array( "select id,user_name from user_passwd", "id","user_name"  );

	if ($data[0]!=0) $buyer_id_cond=" and a.buyer_id='$data[0]'"; else $buyer_id_cond="";
	if (trim($data[3])!="") $system_id_cond=" and a.system_no_prefix='".trim($data[3])."'"; else $system_id_cond="";
	if ($data[2]!=0) $gmt_item_cond=" and a.gmts_item_id='$data[2]'"; else { $gmt_item_cond=""; }
	if ($data[5]!=0) $bulletin_type_cond=" and a.bulletin_type='$data[5]'"; else { $bulletin_type_cond=""; }
	if ($data[8]!=0) $company_id_cond=" and a.company_id=$data[8]"; else { $company_id_cond=""; }

	$search_type = $data[7];
	// print_r($search_type);die;

	if($search_type==1)
	{ 
		if (trim($data[1])!="") $search_field_cond=" and LOWER(a.style_ref) = LOWER('".trim($data[1])."')"; else $search_field_cond=""; 
		
		if (trim($data[4])!="") $prod_id_cond=" and a.prod_description = '".trim($data[4])."' "; else $prod_id_cond=""; 
		if (trim($data[6])!="") $internal_ref_con=" and a.INTERNAL_REF = '".trim($data[6])."'"; else $internal_ref_con="";
	}
	else if($search_type==2)
	{
		if (trim($data[1])!="") $search_field_cond=" and LOWER(a.style_ref) like LOWER('".trim($data[1])."%')"; else $search_field_cond=""; 
		
		if (trim($data[4])!="") $prod_id_cond=" and a.prod_description like '".trim($data[4])."%' "; else $prod_id_cond=""; 
		if (trim($data[6])!="") $internal_ref_con=" and a.INTERNAL_REF like '".trim($data[6])."%'"; else $internal_ref_con="";
	}
	else if($search_type==3)
	{
		if (trim($data[1])!="") $search_field_cond=" and LOWER(a.style_ref) like LOWER('%".trim($data[1])."')"; else $search_field_cond=""; 
		
		if (trim($data[4])!="") $prod_id_cond=" and a.prod_description like '%".trim($data[4])."' "; else $prod_id_cond=""; 
		if (trim($data[6])!="") $internal_ref_con=" and a.INTERNAL_REF like '%".trim($data[6])."'"; else $internal_ref_con="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if (trim($data[1])!="") $search_field_cond=" and LOWER(a.style_ref) like LOWER('%".trim($data[1])."%')"; else $search_field_cond=""; 
		
		if (trim($data[4])!="") $prod_id_cond=" and a.prod_description like '%".trim($data[4])."%' "; else $prod_id_cond=""; 
		if (trim($data[6])!="") $internal_ref_con=" and a.INTERNAL_REF like '%".trim($data[6])."%'"; else $internal_ref_con="";
	} 

 
	$process_arr = array(7 => "Cutting", 4 => "Finishing", 8 => "Sewing");
	$arr=array (2=>$buyer_name_arr,6=>$garments_item,8=>$color_type,9=>$process_arr,10=>$bulletin_type_arr,13=>$user_arr,14=>$user_arr);
	
	if($db_type==0)
	{
		$applicable_period="DATE_FORMAT(a.applicable_period, '%d-%m-%Y') as applicable_period";
	}
	else
	{
		$applicable_period="TO_CHAR( a.applicable_period , 'DD-MM-YYYY' ) as applicable_period";
	}

	
	$sql ="SELECT a.id,a.COMPANY_ID, a.system_no_prefix, a.extention_no,a.prod_description,a.INTERNAL_REF,a.bulletin_type, a.is_copied, a.buyer_id, a.style_ref, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv, a.product_dept, a.inserted_by, a.updated_by,a.req_no, max(b.row_sequence_no) as seq_no, a.custom_style, a.remarks, a.fabric_type, a.color_type, a.approved,$applicable_period,a.internal_ref,a.complexity_level,a.process_id,a.job_id,a.po_job_no
    FROM ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b 
    where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 and a.entry_form=1 $prod_id_cond $buyer_id_cond $search_field_cond $gmt_item_cond  $system_id_cond $bulletin_type_cond $internal_ref_con $company_id_cond
    group by a.id, a.COMPANY_ID,a.system_no_prefix, a.extention_no,a.prod_description,a.INTERNAL_REF,a.bulletin_type, a.is_copied, a.buyer_id, a.style_ref, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv, a.product_dept, a.inserted_by, a.updated_by,a.req_no, a.custom_style, a.remarks, a.fabric_type, a.color_type, a.approved,a.applicable_period,a.internal_ref,a.complexity_level,a.process_id,a.job_id,a.po_job_no order by a.id DESC";
	
    
	echo create_list_view("list_view", "GSD ID, Ext. No, Buyer, Style Ref., Custom Style, Prod Description, Gmt. Item,Internal Ref., Color Type,Process, Bulletin Type, Working Hour, Total SMV,Inserted by,Updated by", "50,50,70,110,110,110,150,120,100,100,70,60,65,65","1390","250",0, $sql , "js_set_value", "id,buyer_id,style_ref,gmts_item_id,working_hour,seq_no,operation_count,mc_operation_count,total_smv,tot_mc_smv,tot_manual_smv,tot_finishing_smv,system_no_prefix,extention_no,product_dept,custom_style,remarks,fabric_type,color_type,approved,prod_description,bulletin_type,applicable_period,is_copied,internal_ref,complexity_level,process_id,job_id,po_job_no,COMPANY_ID,req_no","",1,"0,0,buyer_id,0,0,0,gmts_item_id,0,color_type,process_id,bulletin_type,0,0,inserted_by,updated_by", $arr,"system_no_prefix, extention_no,buyer_id,style_ref,custom_style,prod_description,gmts_item_id,internal_ref,color_type,process_id,bulletin_type,working_hour,total_smv,inserted_by,updated_by","ws_gsd_controller","",'0,0,0,0,0,0,0,0,0,0,0,1,2,0,0');

	exit();
}


if ($action=="show_product_code_view")
{
	// echo $data;
	$dbData=sql_select("select id, product_code from lib_garment_item where id='$data' ");
    echo $dbData[0]['PRODUCT_CODE'];
	//echo "document.getElementById('show_product_code').value = '".$dbData[0]['PRODUCT_CODE']."';\n";
    exit();
}


if ($action=="show_style_ref_list_view")
{
	$style_ref_no=return_field_value("style_ref_no","wo_po_details_master","style_ref_no ='$data' and is_deleted=0 and status_active=1");
	if($style_ref_no != ''){
		echo "$('#txt_style_ref').prop('readonly', true);";
	}
	else{
		echo "$('#txt_style_ref').prop('readonly', false);";
	}
exit();
   
}
if ($action=="show_operation_list_view")
{ 

	$data=explode('_',$data);
	$gmts_item_id=$data[0];
	$body_part_id=$data[1];
	$update_id=$data[2];
	$product_dept_id=$data[3];
	$cbo_process_id=$data[4];
	$company_id=$data[5];
	
	$user_arr=return_library_array( "select id,user_name from user_passwd", "id","user_name"  );
	$production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = $cbo_process_id order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );
	
	$prev_entry_opa_arr=array();
	if($update_id>0)
	{
		$layoutData=sql_select("select lib_sewing_id from ppl_gsd_entry_dtls where mst_id='".$update_id."' and is_deleted=0 and status_active=1");
		foreach($layoutData as $row)
		{
			$prev_entry_opa_arr[$row[csf('lib_sewing_id')]]=$row[csf('lib_sewing_id')];
		}
	}

	if($data[1]>0) $body_part_cond=" and bodypart_id='$data[1]'"; else $body_part_cond="";
	
	$gmts_item_id_cond=($gmts_item_id==0)?" ":" and gmt_item_id=$gmts_item_id ";
	$product_dept_id_cond=($product_dept_id==0)?" ":" and product_dept=$product_dept_id";
	if($company_id>0){$company_con = " and COMPANY_ID=$company_id";}

	//=array (0=>$body_part,2=>$production_resource_arr);
	// ppl_gsd_entry_dtls 
	$sql ="select id, operation_name, resource_sewing, operator_smv, helper_smv,seam_length, bodypart_id, inserted_by, fabric_type from lib_sewing_operation_entry where status_active=1 and is_deleted=0 and department_code=$cbo_process_id $gmts_item_id_cond $product_dept_id_cond $body_part_cond $company_con order by bodypart_id, operation_name";
	
	 //echo $sql;die;
	
	//if($data[0]>0 || $data[1]>0 || $data[3]>0){//issue id 6621;
		$result=sql_select($sql);
	//}
	$composition_arr=array();
	$compositionData=sql_select("select a.construction,b.mst_id, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a,lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0");
	foreach( $compositionData as $row )
	{
		$composition_arr[$row[csf('mst_id')]].=$row[csf('construction')]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	$sql_bpart="select a.id, a.body_part_full_name, b.entry_page_id from lib_body_part_tag_entry_page b, lib_body_part a where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 order by body_part_full_name";
	$sql_result=sql_select($sql_bpart);
	foreach ($sql_result as $value) 
	{
		if($value[csf("entry_page_id")]==149)
		{
			$tag_body_part_arr[$value[csf("id")]]=$value[csf("body_part_full_name")];
		}
			$all_body_part_arr[$value[csf("id")]]=$value[csf("body_part_full_name")];
	}
   $body_partArr=array();
   if(count($tag_body_part_arr)>0)
   {
	$body_partArr=$tag_body_part_arr;   
   }
   else
   {
	 $body_partArr=$all_body_part_arr;     
   }
	?>

<style>
#list_view thead, #list_view tfoot, #list_view tbody{ display: block; }
#list_view tbody {
	max-height: 530px;
	width:425px;
    overflow-y: auto;
    overflow-x: hidden;
}
</style>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th colspan="3"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
            </tr>
            <tr>
                <th width="33%">Body Part</th>
                <th>Prod. Dept</th>
                <th width="33%">Garments Item</th>
            </tr>
            <tr>
                <th><? asort($garments_item); echo create_drop_down( "cbo_body_part_serch", 130, $body_partArr, "", 1, "--  Select --", $body_part_id, "load_operation()", 0); ?></th>
                <th><? asort($garments_item); echo create_drop_down( "cbo_product_department_serch", 130, $product_dept, "", 1, "--  Select --", $product_dept_id, "load_operation()", 0); ?></th>
                <th><? asort($garments_item); echo create_drop_down( "cbo_gmt_item_serch", 130, $garments_item, "", 1, "--  Select --", $gmts_item_id, "load_operation()", 0); ?></th>
            </tr>
        </thead>
	</table>
    <table width="430" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" id="list_view">
        <thead>
            <tr>
                <th width="70">Body Part</th>
                <th width="100">Operation Name</th>
                <th width="50">Seam Length</th>
                <th width="80">Resource</th>
                <th width="50">Machine SMV</th>
                <th width="50">Manual SMV</th>
                <th width="50">User Id</th>
            </tr>
        </thead>
        <tbody>
            <?
			$i=1;
			foreach($result as $row)	
			{
				if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
				 
				if($prev_entry_opa_arr[$row[csf('id')]]!=''){$bgcolor="reen";}
				 
				$data=$row[csf('id')]."_".$row[csf('operation_name')]."_".$row[csf('resource_sewing')]."_".$row[csf('operator_smv')]."_".$row[csf('helper_smv')]."_".$row[csf('bodypart_id')]."_".$row[csf('bodypart_id')].'_'.$row[csf('seam_length')];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $data; ?>')" onDblClick="js_set_save_value(0)" class="tr_<? echo $row[csf('id')]; ?>">
					<td width="70"><p><? echo $body_part[$row[csf('bodypart_id')]]; ?></p></td>
					<td width="100" title="<? echo $composition_arr[$row[csf('fabric_type')]]; ?>"><p><? echo $row[csf('operation_name')]; ?></p></td>
					<td width="50"><p><? echo $row[csf('seam_length')]; ?></p></td>
					<td width="80"><p><? echo $production_resource_arr[$row[csf('resource_sewing')]]; ?></p></td>
                    <td width="50" align="right"><p><? echo number_format($row[csf('operator_smv')],2); ?></p></td>
                    <td align="right" width="50"><p><? echo number_format($row[csf('helper_smv')],2); ?></p></td>
                    <td width="50"><p><? echo $user_arr[$row[csf('inserted_by')]]; ?></p></td>
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

if ($action=="operation_popup")
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1,'',1,'');
	$data=explode('_',$data);
?>	
    <script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
	
		  function js_set_value(id)
		  {  
			  document.getElementById('operation_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
</head>
<body>
    <div style="width:100%" align="center">
	<input type="hidden" id="operation_id" />
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th width="220">Operation Name</th>
                <th width="150">Resource</th>
                <th width="130">Machine SMV</th>
                <th>Man SMV</th>
            </thead>
        </table>
        <div style="width:700px;max-height:300px; overflow-y:scroll" id="gsd_operator_list_view" align="left">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="680" class="rpt_table" id="tbl_list_search">
                <?php  
                $i=1;
                $sql_result=sql_select("select id, operation_name, resource_sewing, operator_smv, helper_smv, total_smv from lib_sewing_operation_entry where status_active=1 and is_deleted=0 order by operation_name asc");
                foreach($sql_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('operation_name')]."_".$row[csf('resource_sewing')]."_".$row[csf('operator_smv')]."_".$row[csf('helper_smv')]; ?>');" > 
                        <td width="50" align="center"><? echo $i; ?></td>
                        <td width="220" ><p><? echo $row[csf('operation_name')]; ?></p></td>
                        <td width="150"><? echo $production_resource[$row[csf('resource_sewing')]]; ?>&nbsp;</td>
                        <td width="130" align="right"><? echo $row[csf('operator_smv')]; ?>&nbsp;</td>
                        <td align="right"><? echo $row[csf('helper_smv')]; ?>&nbsp;</td>
                    </tr>
                <?
                    $i++;
                }
                ?>
            </table>
        </div>
    </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?	
	die;
}

if ($action=="load_php_dtls_form")
{
	$attach_id = return_library_array( "select id,attachment_name from lib_attachment where  STATUS_ACTIVE=1 and IS_DELETED=0",'id','attachment_name');
	$operation_arr = return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );

	$operation_length_arr = return_library_array( "select id,seam_length from lib_sewing_operation_entry", "id","seam_length"  );
	
	$sql_result =sql_select("SELECT a.PROCESS_ID,b.id, b.mst_id, b.row_sequence_no, b.body_part_id, b.lib_sewing_id, b.resource_gsd, b.attachment_id, b.efficiency, b.operator_smv, b.helper_smv, b.target_on_full_perc, b.target_on_effi_perc from PPL_GSD_ENTRY_MST a,ppl_gsd_entry_dtls b where a.id=b.mst_id and b.mst_id=$data and b.is_deleted=0 order by b.row_sequence_no asc");

	//$ddd=  "SELECT a.PROCESS_ID,b.id, b.mst_id, b.row_sequence_no, b.body_part_id, b.lib_sewing_id, b.resource_gsd, b.attachment_id, b.efficiency, b.operator_smv, b.helper_smv, b.target_on_full_perc, b.target_on_effi_perc from PPL_GSD_ENTRY_MST a,ppl_gsd_entry_dtls b where a.id=b.mst_id and b.mst_id=$data and b.is_deleted=0 order by b.row_sequence_no asc";

	//echo $ddd;die;

	$production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and process_id = {$sql_result[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );
				
	?>
    <table width="870" cellspacing="0" border="1" rules="all" class="rpt_table" >
        <thead>
			<th width="25"><input type="checkbox" id="all_operation_check" onclick="all_operation_check()" value="1"></th>
            <th width="30">Seq. No</th>
            <th width="90">Body Part</th>
            <th>Operation</th>
            <th>Seam Length</th>
            <th width="100">Resource</th>
            <th width="55">Attach</th>
            <th width="55">Machine SMV</th>
            <th width="55">Manual SMV</th>
            <th width="50">Eff%</th>
            <th width="50">Tgt 100%</th>
            <th width="68">Tgt (eff.)</th>
        </thead>
    </table>
    <div style="width:870px; overflow-y:scroll; max-height:180px;">
        <table width="850" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_details">
			<?
				$i=1;
               	foreach ($sql_result as $row)
               	{
				   if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
            		<tr bgcolor="<?= $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<?= $row[csf('id')]; ?>', 'populate_details_form_data', 'requires/ws_gsd_controller');"> 
						<td align="center" width="25"><input type="checkbox" name="sequence_id" class="operation_check" value="<?=$row[csf('id')]; ?>"></td>
                		<td width="30"><?= $row[csf('row_sequence_no')]; ?></td>
                        <td width="90"><?= $body_part[$row[csf('body_part_id')]]; ?></td>
                        <td ><p><?= $operation_arr[$row[csf('lib_sewing_id')]]; ?></p></td>
                        <td ><p><?= $operation_length_arr[$row[csf('lib_sewing_id')]]; ?></p></td>
                        <td width="100" align="right"><?= $production_resource_arr[$row[csf('resource_gsd')]]; ?>&nbsp;</td>
                        <td width="55"><?= $attach_id[$row[csf('attachment_id')]]; ?>&nbsp;</td>
                        <td width="55" align="right"><?= number_format($row[csf('operator_smv')],2); ?></td>
                        <td width="55" align="right"><?= number_format($row[csf('helper_smv')],2); ?></td>
                        <td align="right" width="50"><?= number_format($row[csf('efficiency')],2); ?></td>
                        <td align="right" width="50"><?= number_format($row[csf('target_on_full_perc')],0,'.',''); ?></td>
                        <td align="right" width="50"><?= number_format($row[csf('target_on_effi_perc')],0,'.',''); ?></td>
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

if($action=='populate_details_form_data')
{
	$operation_length_arr = return_library_array( "select id,seam_length from lib_sewing_operation_entry", "id","seam_length"  );

	//$operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name");
	//$attach_id=return_library_array( "select id,attachment_name from lib_attachment",'id','attachment_name');
	
	$sql=sql_select("SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, oparetion_type_id, operator_smv, helper_smv, efficiency,target_on_full_perc, target_on_effi_perc, spi, needle_size, risk_factor, remarks from ppl_gsd_entry_dtls where id=$data and is_deleted=0 ");
	/*
	$mstId = $sql[0][csf('mst_id')];
    $sql_mst=sql_select("SELECT bulletin_type from ppl_gsd_entry_mst where id=$mstId and is_deleted=0 ");
    $bul_type = $sql_mst[0][csf('bulletin_type')];
    $sql_settings=sql_select("SELECT smv_editable from variable_settings_production where variable_list=11 and bulletin_type=$bul_type and is_deleted=0 and status_active=1 ");
    $smv_editable = $sql_settings[0][csf('smv_editable')];
    if($smv_editable==2)
    {
        echo "$('#txt_operator').attr('disabled','disabled');\n";
        echo "$('#txt_helper').attr('disabled','disabled');\n";
    }
    else
    {
        echo "$('#txt_operator').attr('disabled',false);\n";
        echo "$('#txt_helper').attr('disabled',false);\n";
    }*/
	

	foreach ($sql as $row)
	{
		$operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry where id=".$row[csf("lib_sewing_id")]."", "id","operation_name");
		$attach_id=return_library_array( "select id,attachment_name from lib_attachment where STATUS_ACTIVE=1 and IS_DELETED=0 and id=".$row[csf("attachment_id")]."",'id','attachment_name');
		
		echo "$('#txt_seqNo').val('".$row[csf("row_sequence_no")]."');\n";
		echo "$('#txt_dtls_id').val('".$row[csf("id")]."');\n";
		echo "$('#cbo_body_part').val('".$row[csf("body_part_id")]."');\n";
		echo "$('#txt_operation').val('".$operation_arr[$row[csf("lib_sewing_id")]]."');\n";
		echo "$('#hidden_operation').val('".$row[csf("lib_sewing_id")]."');\n";
		echo "$('#cbo_resource').val('".$row[csf("resource_gsd")]."');\n";
		echo "$('#cbo_spi').val('".$row[csf("spi")]."');\n";
		echo "$('#cbo_needle_size').val('".$row[csf("needle_size")]."');\n";
		echo "$('#cbo_risk_factor').val('".$row[csf("risk_factor")]."');\n";
		echo "$('#txt_dlts_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#txt_attachment').val('".$attach_id[$row[csf("attachment_id")]]."');\n";
		echo "$('#txt_attachment_id').val('".$row[csf("attachment_id")]."');\n";
		echo "$('#txt_operator').val('".$row[csf("operator_smv")]."');\n";
		echo "$('#txt_helper').val('".$row[csf("helper_smv")]."');\n";
		echo "$('#txt_efficiency').val('".$row[csf("efficiency")]."');\n";
		echo "$('#txt_tgt_perc').val('".$row[csf("target_on_full_perc")]."');\n"; 
		echo "$('#txt_tgt_eff').val('".$row[csf("target_on_effi_perc")]."');\n";
		echo "$('#txt_seam_length').val('".$operation_length_arr[$row[csf("lib_sewing_id")]]."');\n";
        echo "$('#cbo_bulletin_type').attr('disabled',true);\n";
		echo "$('#cbo_gmt_item').attr('disabled',true);\n";
		$mst_id = $row[csf("mst_id")];
		
		echo "fnc_smv_active();\n";
		//echo "load_operation();\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_gsd_entry',1);\n"; 
	}
	
	$approved=0;
	$sql=sql_select("select approved from ppl_gsd_entry_mst where id=$mst_id");
	foreach($sql as $row){
		$approved=$row[csf('approved')];
	}
	
	if($approved==1)
	{
		echo "document.getElementById('approve1').value = 'Un-Approved';\n";
	}
	else
	{
		echo "document.getElementById('approve1').value = 'Approved';\n";
	}
	exit();
}

if ($action=="attachment_popup")
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1,'',1,'');
?>	
    <script>
		  function js_set_value(id)
		  { 
			  document.getElementById('attachment_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
    <input type="hidden" id="attachment_id" />
    <?
	$sql="SELECT id,attachment_name from lib_attachment where  STATUS_ACTIVE=1 and IS_DELETED=0"; 
	
	echo  create_list_view("list_view", "Attachment Name", "340","380","350",0, $sql , "js_set_value", "id,attachment_name", "", 1, "", 0 , "attachment_name", "ws_gsd_controller",'setFilterGrid("list_view",-1);','0') ;
	 die; 
}

if ($action=="save_update_delete")
{
	// echo "save";die;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$txt_job_no = str_replace("'","",$txt_job_no);
	$txt_style_id = str_replace("'","",$txt_style_id);
 
	//echo $txt_job_no;die;

	if ( $operation==0 )   // Insert Here
	{
		// echo "save";die;
        //echo $cbo_bulletin_type=str_replace("'","",$cbo_bulletin_type);die;
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$approved=0;
		$sql=sql_select("select approved from ppl_gsd_entry_mst where id=$update_id");
		foreach($sql as $row){
			$approved=$row[csf('approved')];
		}
		if($approved==3) $approved=1; else $approved=$approved;
		
		if($approved==1){
			echo "approved**".str_replace("'","",$update_id);
            disconnect($con);
			die;
		}

		if(str_replace("'", "", $update_id) == ''){
			$mst_sql = "SELECT ID from PPL_GSD_ENTRY_MST WHERE STYLE_REF = $txt_style_ref AND GMTS_ITEM_ID = $cbo_gmt_item AND BUYER_ID = $cbo_buyer AND PROCESS_ID =$cbo_process_id AND COMPANY_ID = $cbo_company_id AND BULLETIN_TYPE in( 2,3 )  AND BULLETIN_TYPE =$cbo_bulletin_type AND IS_DELETED = 0 AND STATUS_ACTIVE = 1";
			$mst_sql_res = sql_select($mst_sql);
			foreach($mst_sql_res as $row)
			{
				echo "duplicate**";
				disconnect($con);
				die;
			}	
		}

		//echo $update_id;die;

		$tot_smv_operation = str_replace("'",'',$txt_operator)+str_replace("'",'',$txt_helper);
		if(str_replace("'",'',$update_id) == "")
		{
			$txt_operation_count=1;	
			if(str_replace("'",'',$cbo_resource)==40 || str_replace("'",'',$cbo_resource)==41 || str_replace("'",'',$cbo_resource)==43 || str_replace("'",'',$cbo_resource)==44 || str_replace("'",'',$cbo_resource)==48 || str_replace("'",'',$cbo_resource)==68 || str_replace("'",'',$cbo_resource)==69 || str_replace("'",'',$cbo_resource)==147)
			{
				$txt_mc_smv=0;
				$txt_finishing_smv=0;
				$txt_manual_smv=$tot_smv_operation;
				$txt_mcOperationCount=0;
			}
			else if(str_replace("'",'',$cbo_resource)==53 || str_replace("'",'',$cbo_resource)==54 || str_replace("'",'',$cbo_resource)==55 || str_replace("'",'',$cbo_resource)==56 || str_replace("'",'',$cbo_resource)==70 || str_replace("'",'',$cbo_resource)==176)
			{
				$txt_finishing_smv = $tot_smv_operation;
				$txt_mc_smv = 0;
				$txt_manual_smv = 0;
				$txt_mcOperationCount = 0;
			}
			else  
			{
				$txt_manual_smv = 0;
				$txt_finishing_smv = 0;
				$txt_mc_smv = $tot_smv_operation;
				$txt_mcOperationCount = 1;
			}
			// txt_manual_smv txt_attachment_id
			// $total_smv=$tot_smv_operation;
			// $id=return_next_id( "id", "ppl_gsd_entry_mst", 1 ) ;
			// $field_array="id, system_no_prefix, system_no, buyer_id, style_ref, custom_style, remarks, fabric_type,bulletin_type,prod_description, gmts_item_id, working_hour, operation_count, mc_operation_count, total_smv, tot_mc_smv, tot_manual_smv, tot_finishing_smv,quotation_id, product_dept, color_type,applicable_period,internal_ref, complexity_level,process_id,job_id,po_job_no,inserted_by, insert_date";
			// $data_array="(".$id.",".$id.",".$id.",".$cbo_buyer.",".$txt_style_ref.",".$txt_custom_style.",".$txt_remarks.",".$txt_fabric_type.",".$cbo_bulletin_type.",".$txt_product_description.",".$cbo_gmt_item.",".$txt_working_hour.",".$txt_operation_count.",".$txt_mcOperationCount.",".$tot_smv_operation.",".$txt_mc_smv.",".$txt_manual_smv.",".$txt_finishing_smv.",".$hidden_quotation_id.",".$cbo_product_department.",".$cbo_colortype.",".$txt_applicable_period.",".$txt_internal_ref.",".$complexity_level.",".$cbo_process_id.",".$txt_job_id.",'".$txt_job_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			// $next_seq_no=2;

			$total_smv = $tot_smv_operation;
			$id = return_next_id( "id", "ppl_gsd_entry_mst", 1 ) ;
			$field_array = "id, system_no_prefix, system_no,COMPANY_ID, buyer_id, style_ref, custom_style, remarks, fabric_type,bulletin_type,prod_description, gmts_item_id, working_hour, operation_count, mc_operation_count, total_smv, tot_mc_smv, tot_manual_smv, tot_finishing_smv, quotation_id, product_dept, color_type,applicable_period,internal_ref, complexity_level,process_id,job_id,po_job_no,req_no,inserted_by, insert_date, entry_form";
			$data_array = "(".$id.",".$id.",".$id.",".$cbo_company_id.",".$cbo_buyer.",".$txt_style_ref.",".$txt_custom_style.",".$txt_remarks.",".$txt_fabric_type.",".$cbo_bulletin_type.",".$txt_product_description.",".$cbo_gmt_item.",".$txt_working_hour.",".$txt_operation_count.",".$txt_mcOperationCount.",".$tot_smv_operation.",".$txt_mc_smv.",".$txt_manual_smv.",".$txt_finishing_smv.",".$hidden_quotation_id.",".$cbo_product_department.",".$cbo_colortype.",".$txt_applicable_period.",".$txt_internal_ref.",".$complexity_level.",".$cbo_process_id.",".$txt_job_id.",'".$txt_job_no."',".$txt_req_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)"; 
			$next_seq_no = 2;

			

		}
		else
		{
			//echo $cbo_company_id; die;
			$dataArray = sql_select("select max(row_sequence_no) as seq_no, count(id) as op_count, count(case when resource_gsd not in(40,41,43,44,48,68,69,53,54,55,56,70,176,147) then id end) as m_op_count, 
			sum(total_smv) as tot_smv, 
			sum(case when resource_gsd not in(40,41,43,44,48,68,69,53,54,55,56,70,176,147) then total_smv end) as tot_smv_mc, 
			sum(case when resource_gsd in(40,41,43,44,48,68,69,147) then total_smv end) as tot_smv_mn, 
			sum(case when resource_gsd in(53,54,55,56,70,176) then total_smv end) as tot_smv_fn 
			from ppl_gsd_entry_dtls where mst_id=$update_id and is_deleted=0");
			$txt_operation_count=$dataArray[0][csf('op_count')]+1;
			$total_smv = $dataArray[0][csf('tot_smv')]+$tot_smv_operation;
			
			$txt_mcOperationCount = $dataArray[0][csf('m_op_count')];
			$txt_mc_smv = $dataArray[0][csf('tot_smv_mc')];
			$txt_manual_smv = $dataArray[0][csf('tot_smv_mn')];
			$txt_finishing_smv = $dataArray[0][csf('tot_smv_fn')];
			
			//if(str_replace("'",'',$cbo_resource)!=40)
			if(str_replace("'",'',$cbo_resource)==40 || str_replace("'",'',$cbo_resource)==41 || str_replace("'",'',$cbo_resource)==43 || str_replace("'",'',$cbo_resource)==44 || str_replace("'",'',$cbo_resource)==48 || str_replace("'",'',$cbo_resource)==68 || str_replace("'",'',$cbo_resource)==69 || str_replace("'",'',$cbo_resource)==147)
			{
				$txt_manual_smv+=$tot_smv_operation;
			}
			else if(str_replace("'",'',$cbo_resource)==53 || str_replace("'",'',$cbo_resource)==54 || str_replace("'",'',$cbo_resource)==55 || str_replace("'",'',$cbo_resource)==56 || str_replace("'",'',$cbo_resource)==70 || str_replace("'",'',$cbo_resource)==176)
			{
				$txt_finishing_smv+=$tot_smv_operation;
			}
			else
			{
				$txt_mcOperationCount+=1;
				$txt_mc_smv+=$tot_smv_operation;
			}
			$field_array="company_id*buyer_id*style_ref*custom_style*remarks*prod_description*gmts_item_id*working_hour*operation_count*mc_operation_count*total_smv*tot_mc_smv*tot_manual_smv*tot_finishing_smv*quotation_id*product_dept*color_type*applicable_period*internal_ref*complexity_level*process_id*job_id*po_job_no*req_no*updated_by*update_date";
			$data_array=$cbo_company_id."*".$cbo_buyer."*".$txt_style_ref."*".$txt_custom_style."*".$txt_remarks."*".$txt_product_description."*".$cbo_gmt_item."*".$txt_working_hour."*".$txt_operation_count."*".$txt_mcOperationCount."*".$total_smv."*'".$txt_mc_smv."'*'".$txt_manual_smv."'*'".$txt_finishing_smv."'*".$hidden_quotation_id."*".$cbo_product_department."*".$cbo_colortype."*".$txt_applicable_period."*".$txt_internal_ref."*".$complexity_level."*".$cbo_process_id."*".$txt_job_id. "*'".$txt_job_no."'*".$txt_req_no."*" . $_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$id=str_replace("'",'',$update_id);
			$next_seq_no=str_replace("'",'',$txt_seqNo)+1;
		  // echo $data_array;die;
		}
		//$target_per_hour=round(60/(str_replace("'","",$txt_operator)+str_replace("'","",$txt_helper))); req_no
		$id_dtls=return_next_id("id", "ppl_gsd_entry_dtls", 1);
		
		$field_array_dtls = "id, mst_id, row_sequence_no, resource_gsd, body_part_id, lib_sewing_id, attachment_id, efficiency, target_on_full_perc, target_on_effi_perc, operator_smv, helper_smv, total_smv, spi, needle_size, risk_factor, remarks, entry_form";
		$data_array_dtls="(".$id_dtls.",".$id.",".$txt_seqNo.",".$cbo_resource.",".$cbo_body_part.",".$hidden_operation.",".$txt_attachment_id.",".$txt_efficiency.",".$txt_tgt_perc.",".$txt_tgt_eff.",".$txt_operator.",".$txt_helper.",".$tot_smv_operation.",".$cbo_spi.",".$cbo_needle_size.",".$cbo_risk_factor.",".$txt_dlts_remarks.",1)";
		
		$datas=$txt_operation_count."_".$txt_mcOperationCount."_".number_format($total_smv,2,'.','')."_".number_format($txt_mc_smv,2,'.','')."_".number_format($txt_manual_smv,2,'.','')."_".number_format($txt_finishing_smv,2,'.','');
		if(str_replace("'",'',$update_id)=="")
		{
			$rID=sql_insert("ppl_gsd_entry_mst",$field_array,$data_array,1);
		}
		else
		{
			$rID=sql_update("ppl_gsd_entry_mst",$field_array,$data_array,"id",$update_id,1);
		}
		
	    // echo "10**".$rID.'='.$update_id;die;
		
	   //echo "10**INSERT INTO ppl_gsd_entry_mst (".$field_array.") VALUES ".$data_array; die;

		$rID1=sql_insert("ppl_gsd_entry_dtls",$field_array_dtls,$data_array_dtls,1);
         
	    // echo "10**".$rID1;die;
		
		$system_no=(str_replace("'",'',$system_no)!='')?$system_no:$id;


		if($txt_style_id != '' && str_replace("'","",$update_id)==''){
			$file_field_array="ID,MASTER_TBLE_ID,FORM_NAME,IMAGE_LOCATION,PIC_SIZE,IS_DELETED,FILE_TYPE,INSERT_DATE";
			$file_mst_id=return_next_id( "id", "COMMON_PHOTO_LIBRARY", 1 ) ;
			$style_file_copy_sql_res=sql_select("select ID,MASTER_TBLE_ID,FORM_NAME,IMAGE_LOCATION,PIC_SIZE,IS_DELETED,FILE_TYPE,INSERT_DATE from COMMON_PHOTO_LIBRARY where MASTER_TBLE_ID='$txt_style_id' and FORM_NAME='style_ref_entry'
			");
			foreach($style_file_copy_sql_res as $frow){
				if($file_data_array!="") $file_data_array.=","; 
				$file_data_array.="(".$file_mst_id.",'".$id."','gsd_entry','".$frow['IMAGE_LOCATION']."',".$frow['PIC_SIZE'].",".$frow['IS_DELETED'].",".$frow['FILE_TYPE'].",'".$pc_date_time."')"; 
				$file_mst_id++;	 
			}

			if($file_data_array){
				$rID=sql_insert("COMMON_PHOTO_LIBRARY",$file_field_array,$file_data_array,1);
			}
			// echo "10**insert into COMMON_PHOTO_LIBRARY $file_field_array values($file_data_array)";  oci_rollback($con);;die;
		}
		//echo $rID."===". $rID1;die;
	
		if($rID && $rID1)
		{
			oci_commit($con);
			echo "0**".str_replace("'",'',$id)."**".$next_seq_no."**".$datas."**".str_replace("'",'',$system_no);
		}
		else
		{
			oci_rollback($con);
			echo "10**".$id;
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here=================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$approved=0;
		$sql=sql_select("select approved from ppl_gsd_entry_mst where id=$update_id");
		foreach($sql as $row){
			$approved=$row[csf('approved')];
		}
		if($approved==3) $approved=1; else $approved=$approved;
		
		if($approved==1){
			echo "approved**".str_replace("'","",$update_id);
            disconnect($con);
			die;
		}
		
		$tot_smv_operation=str_replace("'",'',$txt_operator)+str_replace("'",'',$txt_helper);
		
		$dataArray=sql_select("select max(row_sequence_no) as seq_no, count(id) as op_count, 
		count(case when resource_gsd not in(40,41,43,44,48,68,69,53,54,55,56,70,176,147) then id end) as m_op_count,
		sum(total_smv) as tot_smv, 
		sum(case when resource_gsd not in(40,41,43,44,48,68,69,53,54,55,56,70,176,147) then total_smv end) as tot_smv_mc,
		sum(case when resource_gsd in(40,41,43,44,48,68,69,147) then total_smv end) as tot_smv_mn, 
		sum(case when resource_gsd in(53,54,55,56,70,176) then total_smv end) as tot_smv_fn from ppl_gsd_entry_dtls where mst_id=$update_id and is_deleted=0 and id<>$txt_dtls_id");
		
		$txt_operation_count=$dataArray[0][csf('op_count')]+1;
		$total_smv=$dataArray[0][csf('tot_smv')]+$tot_smv_operation;
		
		$txt_mcOperationCount=$dataArray[0][csf('m_op_count')];
		$txt_mc_smv=$dataArray[0][csf('tot_smv_mc')];
		$txt_manual_smv=$dataArray[0][csf('tot_smv_mn')];
		$txt_finishing_smv=$dataArray[0][csf('tot_smv_fn')];
		
		//if(str_replace("'",'',$cbo_resource)!=40)
		if(str_replace("'",'',$cbo_resource)==40 || str_replace("'",'',$cbo_resource)==41 || str_replace("'",'',$cbo_resource)==43 || str_replace("'",'',$cbo_resource)==44 || str_replace("'",'',$cbo_resource)==48 || str_replace("'",'',$cbo_resource)==68 || str_replace("'",'',$cbo_resource)==69 || str_replace("'",'',$cbo_resource)==147)
		{
			$txt_manual_smv+=$tot_smv_operation;
		}
		else if(str_replace("'",'',$cbo_resource)==53 || str_replace("'",'',$cbo_resource)==54 || str_replace("'",'',$cbo_resource)==55 || str_replace("'",'',$cbo_resource)==56 || str_replace("'",'',$cbo_resource)==70 || str_replace("'",'',$cbo_resource)==176)
		{
			$txt_finishing_smv+=$tot_smv_operation;
		}
		else 
		{
			$txt_mcOperationCount+=1;
			$txt_mc_smv+=$tot_smv_operation;
		}

		// echo $update_id;die;
		// echo $txt_operation_count;die;
		 
		$next_seq_no=return_field_value("max(row_sequence_no) as seq_no","ppl_gsd_entry_dtls","mst_id=$update_id and is_deleted=0", "seq_no")+1;
		//$next_seq_no=$dataArray[0][csf('seq_no')]+1;
		$field_array="company_id*buyer_id*style_ref*custom_style*remarks*fabric_type*bulletin_type*prod_description*gmts_item_id*working_hour*operation_count*mc_operation_count*total_smv*tot_mc_smv*tot_manual_smv*tot_finishing_smv*quotation_id*product_dept*color_type*applicable_period*internal_ref*complexity_level*process_id*job_id*po_job_no*req_no*updated_by*update_date";
		$data_array=$cbo_company_id."*".$cbo_buyer."*".$txt_style_ref."*".$txt_custom_style."*".$txt_remarks."*".$txt_fabric_type."*".$cbo_bulletin_type."*".$txt_product_description."*".$cbo_gmt_item."*".$txt_working_hour."*".$txt_operation_count."*".$txt_mcOperationCount."*".$total_smv."*'".$txt_mc_smv."'*'".$txt_manual_smv."'*'".$txt_finishing_smv."'*".$hidden_quotation_id."*".$cbo_product_department."*".$cbo_colortype."*".$txt_applicable_period."*".$txt_internal_ref."*".$complexity_level."*".$cbo_process_id."*".$txt_job_id."*'".$txt_job_no."'*".$txt_req_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		//echo $data_array;die;
		
		$field_array_dtls ="row_sequence_no*resource_gsd*body_part_id*lib_sewing_id*attachment_id*efficiency*target_on_full_perc*target_on_effi_perc*operator_smv*helper_smv*total_smv*spi*needle_size*risk_factor*remarks";
		$data_array_dtls=$txt_seqNo."*".$cbo_resource."*".$cbo_body_part."*".$hidden_operation."*".$txt_attachment_id."*".$txt_efficiency."*".$txt_tgt_perc."*".$txt_tgt_eff."*".$txt_operator."*".$txt_helper."*".$tot_smv_operation."*".$cbo_spi."*".$cbo_needle_size."*".$cbo_risk_factor."*".$txt_dlts_remarks; 
		
		$datas=$txt_operation_count."_".$txt_mcOperationCount."_".number_format($total_smv,2,'.','')."_".number_format($txt_mc_smv,2,'.','')."_".number_format($txt_manual_smv,2,'.','')."_".number_format($txt_finishing_smv,2,'.','');
		//echo "10**";
		//echo "10**".$update_id."**".$next_seq_no."**".$datas;die;
		$rID=sql_update("ppl_gsd_entry_mst",$field_array,$data_array,"id",$update_id,1);
		$rID2=sql_update("ppl_gsd_entry_dtls",$field_array_dtls,$data_array_dtls,"id",$txt_dtls_id,1);
		//fnc_smv_style_integration($db_type,$cbo_company_name,$txt_job_no,$cbo_currercy,$sewSmv,$cutSmv,1);
		fnc_smv_style_integration($db_type,$cbo_buyer,$txt_style_ref,$cbo_gmt_item,$total_smv,$update_id,8);
		//$smv=echo $smv; die;
		
		
		// echo "10**".$rID."**".$rID2;die;
		 
		if($rID && $rID2)
		{
			oci_commit($con);  
			echo "1**".str_replace("'",'',$update_id)."**".$next_seq_no."**".$datas."**".str_replace("'",'',$system_no);
		}
		else
		{
			oci_rollback($con);
			echo "10**".str_replace("'",'',$update_id);
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) //Delete Here----------------------------------------------------------
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$approved=0;
		$sql=sql_select("select approved from ppl_gsd_entry_mst where id=$update_id");
		foreach($sql as $row){
			$approved=$row[csf('approved')];
		}
		if($approved==3) $approved=1; else $approved=$approved;
		
		if($approved==1){
			echo "approved**".str_replace("'","",$update_id);
            disconnect($con);
			die;
		}
		//echo "10**".$update_id;die;
		
        /*$rID=execute_query("DELETE FROM ppl_gsd_entry_mst WHERE id=$update_id");
		$rID2=execute_query("DELETE FROM ppl_gsd_entry_dtls WHERE mst_id=$update_id");
		$rID3=execute_query("DELETE FROM ppl_balancing_mst_entry WHERE gsd_mst_id=$update_id");
		$rID4=execute_query("DELETE FROM ppl_balancing_dtls_entry WHERE gsd_mst_id=$update_id");
		$rID5=execute_query("DELETE FROM ppl_bl_wk_dtls_entry WHERE gsd_mst_id=$update_id");
		$rID6=execute_query("DELETE FROM ppl_balancing2_dtls_entry WHERE gsd_mst_id=$update_id");
		$rID7=execute_query("DELETE FROM ppl_layout_dtls_entry WHERE gsd_mst_id=$update_id");
		$rID8=execute_query("DELETE FROM ppl_thread_cons_dtls_entry WHERE gsd_mst_id=$update_id");
		$rID9=execute_query("DELETE FROM ppl_thread_cons_op_dtls_entry WHERE gsd_mst_id=$update_id");
        */		
		$field_array_delete ="status_active*is_deleted*deleted_by*delete_date";
		$data_array_delete="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("ppl_gsd_entry_mst",$field_array_delete,$data_array_delete,"id",$update_id,1);
		$rID2=sql_update("ppl_gsd_entry_dtls",$field_array_delete,$data_array_delete,"mst_id",$update_id,1);
		$rID3=sql_update("ppl_balancing_mst_entry",$field_array_delete,$data_array_delete,"gsd_mst_id",$update_id,1);
		$rID4=sql_update("ppl_balancing_dtls_entry",$field_array_delete,$data_array_delete,"gsd_mst_id",$update_id,1);
		$rID5=sql_update("ppl_bl_wk_dtls_entry",$field_array_delete,$data_array_delete,"gsd_mst_id",$update_id,1);
		$rID6=sql_update("ppl_balancing2_dtls_entry",$field_array_delete,$data_array_delete,"gsd_mst_id",$update_id,1);
		$rID7=sql_update("ppl_layout_dtls_entry",$field_array_delete,$data_array_delete,"gsd_mst_id",$update_id,1);
		$rID8=sql_update("ppl_thread_cons_dtls_entry",$field_array_delete,$data_array_delete,"gsd_mst_id",$update_id,1);
		$rID9=sql_update("ppl_thread_cons_op_dtls_entry",$field_array_delete,$data_array_delete,"gsd_mst_id",$update_id,1);
 		
		//echo "10**".$rID ."&&". $rID2 ."&&". $rID3 ."&&". $rID4 ."&&". $rID5 ."&&". $rID6 ."&&". $rID7 ."&&". $rID8 ."&&". $rID9; oci_rollback($con);die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8 && $rID9)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$update_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id);

			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8 && $rID9)
			{
				oci_commit($con); 
				echo "2**".str_replace("'","",$update_id)."**1";
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==3) //Approve Here----------------------------------------------------------
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$field_array="approved*approved_by*approved_date";
		if(trim(str_replace("'","",$cbo_approved_status))==2) 
		{
			$data_array="'1'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date."'";
		}
		else 
		{
			$data_array="'2'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date."'";
			
		}	
		//echo "0**insert into  wo_price_quotation (".$field_array.") values".$data_array;
		//die;
		$rID=sql_update("ppl_gsd_entry_mst",$field_array,$data_array,"id",$update_id,1); 
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "19**".str_replace("'","",$update_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id);

			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con); 
				echo "19**".str_replace("'","",$update_id)."**1";
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="is_balanced_entry")
{
	$sql_bl="select id from ppl_balancing_mst_entry where gsd_mst_id=$data";
	$result=sql_select($sql_bl);
	if(count($result)>0)
	{
		echo "1";
	}
	else echo "0";
	exit();
}

if ($action=="is_operation_balanced_entry")
{
	$sql_bl="select id from ppl_balancing_dtls_entry where gsd_dtls_id=$data and layout_mp>0 union all select id from ppl_balancing2_dtls_entry where gsd_dtls_id=$data";
	$result=sql_select($sql_bl);
	if(count($result)>0)
	{
		echo "1";
	}
	else echo "0";
	exit();
}

if ($action=="delete_operation")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	foreach(explode(',',$txt_dtls_id_str) as $txt_dtls_id)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$approved=0;
		$sql=sql_select("select approved from ppl_gsd_entry_mst where id=$update_id");
		foreach($sql as $row){
			$approved=$row[csf('approved')];
		}
		if($approved==3) $approved=1; else $approved=$approved;
		
		if($approved==1){
			echo "15**approved**".str_replace("'","",$update_id);
			disconnect($con);
			die;
		}
		
		
		$no_of_work_st=return_field_value("count(id) as no_of_work_st","ppl_layout_dtls_entry","gsd_dtls_id<>$txt_dtls_id and gsd_mst_id=$update_id and is_deleted=0","no_of_work_st");

		$next_seq_no=return_field_value("max(row_sequence_no) as seq_no","ppl_gsd_entry_dtls","mst_id=$update_id and id<>$txt_dtls_id and is_deleted=0","seq_no")+1;
		
		$prev_data=sql_select("select a.input_uom, sum(b.req_qty) as req_qty from ppl_balancing_mst_entry a, ppl_thread_cons_dtls_entry b where a.id=b.mst_id and a.gsd_mst_id=$update_id and b.gsd_mst_id=$update_id and b.gsd_dtls_id!=$txt_dtls_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.input_uom"); 
		if(count($prev_data)>0)
		{
			$totReq=$prev_data[0][csf('req_qty')];
			$input_uom=$prev_data[0][csf('input_uom')];
			
			$req_per_gmts_into_meter=0;
			
			if($input_uom==25)
			{
				$req_per_gmts_into_meter=$totReq/100;
			}
			else
			{
				$req_per_gmts_into_meter=$totReq/39.37;
			}
			
			$field_array="total_req*meter_per_gmts*updated_by*update_date";
			$data_array=$totReq."*".$req_per_gmts_into_meter."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		
		$ids=sql_select("select id, mst_id from ppl_thread_cons_dtls_entry where gsd_dtls_id=$txt_dtls_id and status_active=1 and is_deleted=0");
		$dtlsId=$ids[0][csf('id')];
		$mstId=$ids[0][csf('mst_id')];  

	
		$field_array_delete ="status_active*is_deleted*deleted_by*delete_date";
		$data_array_delete="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		
		$rID=sql_update("ppl_gsd_entry_dtls",$field_array_delete,$data_array_delete,"id",$txt_dtls_id,1);
		$rID2=sql_update("ppl_balancing_dtls_entry",$field_array_delete,$data_array_delete,"gsd_dtls_id",$txt_dtls_id,1);
		$rID3=sql_update("ppl_balancing2_dtls_entry",$field_array_delete,$data_array_delete,"gsd_dtls_id",$txt_dtls_id,1);
		$rID4=sql_update("ppl_layout_dtls_entry",$field_array_delete,$data_array_delete,"gsd_dtls_id",$txt_dtls_id,1);
		$rID5=sql_update("ppl_thread_cons_dtls_entry",$field_array_delete,$data_array_delete,"gsd_dtls_id",$txt_dtls_id,1);
		
		// echo "7**". $data_array_delete;die;
		
		//========================================================
		
			$dataArray=sql_select("select max(row_sequence_no) as seq_no, count(id) as op_count, 
			count(case when resource_gsd not in(40,41,43,44,48,53,54,55,56,68,69,70,176,147) then id end) as m_op_count, 
			sum(total_smv) as tot_smv, 
			sum(case when resource_gsd not in(40,41,43,44,48,53,54,55,56,68,69,70,176,147) then total_smv end) as tot_smv_mc, 
			sum(case when resource_gsd in(40,41,43,44,48,68,69,147) then total_smv end) as tot_smv_mn, 
			sum(case when resource_gsd in(53,54,55,56,70,176) then total_smv end) as tot_smv_fn from ppl_gsd_entry_dtls where mst_id=$update_id and id<>$txt_dtls_id and status_active=1 and is_deleted=0");
			$txt_operation_count=$dataArray[0][csf('op_count')];
			$total_smv=$dataArray[0][csf('tot_smv')];
			$txt_mcOperationCount=$dataArray[0][csf('m_op_count')];
			$txt_mc_smv=$dataArray[0][csf('tot_smv_mc')];
			$txt_manual_smv=$dataArray[0][csf('tot_smv_mn')];
			$txt_finishing_smv=$dataArray[0][csf('tot_smv_fn')];
		if($total_smv>0){
			$field_array_up="operation_count*mc_operation_count*total_smv*tot_mc_smv*tot_manual_smv*tot_finishing_smv";
			$data_array_up=$txt_operation_count."*".$txt_mcOperationCount."*".$total_smv."*'".$txt_mc_smv."'*'".$txt_manual_smv."'*'".$txt_finishing_smv."'"; 
			$rID10=sql_update("ppl_gsd_entry_mst",$field_array_up,$data_array_up,"id",$update_id,1);
		}
		//==============================================================
		
		$rID6=true; $rID7=true;
		if($dtlsId>0)
		{
			//$rID6=execute_query("DELETE FROM ppl_thread_cons_op_dtls_entry WHERE id=$dtlsId");
			$rID6=sql_update("ppl_thread_cons_op_dtls_entry",$field_array_delete,$data_array_delete,"id",$dtlsId,1);
		}
		
		if($data_array!="")
		{
			$rID7=sql_update("ppl_balancing_mst_entry",$field_array,$data_array,"id",$mstId,0);
		}
		
		if($no_of_work_st != 0)
		{
			$mstId=return_field_value("id as mst_id","ppl_balancing_mst_entry","gsd_mst_id=$update_id and balancing_page=3 and status_active=1 and is_deleted=0","mst_id"); 
			
			$field_array="no_of_work_st*updated_by*update_date";
			$data_array=$no_of_work_st."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID7=sql_update("ppl_balancing_mst_entry",$field_array,$data_array,"id",$mstId,0);
		}
		
		//------------------------- work_station change.........................
			$field_array_dtls_update="work_station"; $id_arr=array();
			$lo_dtls_data=sql_select("select id,work_station from ppl_layout_dtls_entry where gsd_mst_id=$update_id and gsd_dtls_id <> $txt_dtls_id and is_deleted=0 order by work_station asc");
			foreach($lo_dtls_data as $rowlo)
			{   $new_work_station+=1;
				$id_arr[]=$rowlo[csf('id')];
				$data_array_dtls_update[$rowlo[csf('id')]]=array($new_work_station);
			}
		$rID8=execute_query(bulk_update_sql_statement("ppl_layout_dtls_entry", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ));
		
		//--------------------------
		$deleted_sequence=return_field_value( "row_sequence_no","ppl_gsd_entry_dtls","mst_id=$update_id and id = $txt_dtls_id","row_sequence_no" );

		$sequence_arr=return_library_array( "select id,row_sequence_no from ppl_gsd_entry_dtls where mst_id=$update_id and id <> $txt_dtls_id and row_sequence_no > $deleted_sequence and is_deleted=0 order by row_sequence_no asc", "id","row_sequence_no" );
		$data="";
		foreach($sequence_arr as $dtlsId=>$seqNo){
				
				$next_seq_no=$seqNo;
				$seqNo=($seqNo-1);
				if($data=="")
				{
					$data=$seqNo."_".$dtlsId;
				}
				else
				{
					$data.="|".$seqNo."_".$dtlsId;
				}
		}
		
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".$next_seq_no."**".$data;
			}
			else 
			{
				mysql_query("ROLLBACK"); 
				echo "7**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "2**".$next_seq_no."**".$data;
			}
			else
			{
				oci_rollback($con);
				echo "7**".$id;
			}
		}
		disconnect($con);
    }

	die;
}

if ($action=="totalSMVAfterDelete")
{
	$sql = "SELECT a.id, a.system_no_prefix, a.extention_no, a.is_copied, a.buyer_id, a.style_ref, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv, max(b.row_sequence_no) as seq_no
			FROM ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b 
			where a.id=$data and  a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0
			group by a.id, a.system_no_prefix, a.extention_no, a.is_copied, a.buyer_id, a.style_ref, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv order by a.system_no_prefix";
	
	$sql_result=sql_select($sql);
	foreach ($sql_result as $row)
	{
		echo "$('#txt_operation_count').val('".number_format($row[csf("operation_count")],2)."');\n";
		echo "$('#txt_mcOperationCount').val('".number_format($row[csf("mc_operation_count")],2)."');\n";
		echo "$('#txt_tot_smv').val('".number_format($row[csf("total_smv")],2)."');\n";
		echo "$('#txt_mc_smv').val('".number_format($row[csf("tot_mc_smv")],2)."');\n";
		echo "$('#txt_manual_smv').val('".number_format($row[csf("tot_manual_smv")],2)."');\n";
		echo "$('#txt_finishing_smv').val('".number_format($row[csf("tot_finishing_smv")],2)."');\n";
	}
	exit();
}

if ($action=="copy_bulletin")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$txt_style_ref=str_replace("'",'',$txt_style_ref);
	$cbo_bulletin_copy=str_replace("'",'',$cbo_bulletin_copy);
	$cbo_bulletin_type=str_replace("'",'',$cbo_bulletin_type);
	$cbo_gmt_item=str_replace("'",'',$cbo_gmt_item);
	$cbo_process_id=str_replace("'",'',$cbo_process_id);
	
	$cbo_buyer=str_replace("'",'',$cbo_buyer);
	
	$id=return_next_id( "id", "ppl_gsd_entry_mst", 1 ) ;
	
	if(str_replace("'",'',$cbo_bulletin_copy)==2)
	{
		$extended_from=return_field_value("EXTENDED_FROM","ppl_gsd_entry_mst","id=$update_id","extended_from");
		$update_id=($extended_from)?$extended_from:$update_id;
		//echo $update_id;die;

		$mst_data=sql_select("select id, system_no_prefix, system_no, buyer_id, style_ref, gmts_item_id, working_hour, operation_count, mc_operation_count, total_smv, tot_mc_smv, tot_manual_smv, tot_finishing_smv, color_type,bulletin_type from ppl_gsd_entry_mst where id=$update_id and is_deleted=0");
		$system_no_prefix=$mst_data[0][csf('id')];
		$extention_no=return_field_value("max(extention_no) as extention_no","ppl_gsd_entry_mst","extended_from=$update_id","extention_no")+1;
		$system_no=$system_no_prefix."-".$extention_no;
		
		if($mst_data[0][csf('style_ref')]!=$txt_style_ref || $cbo_bulletin_type!=$mst_data[0][csf('bulletin_type')]  || $mst_data[0][csf('buyer_id')]!=$cbo_buyer || $mst_data[0][csf('gmts_item_id')]!=$cbo_gmt_item){
			echo "12**";disconnect($con);die;
			}

			//echo $system_no;die;
		
		
	}
	else
	{
		$mst_data=sql_select("select system_no_prefix, system_no, buyer_id, style_ref, gmts_item_id, working_hour, operation_count, mc_operation_count, total_smv, tot_mc_smv, tot_manual_smv, tot_finishing_smv, color_type,bulletin_type from ppl_gsd_entry_mst where id=$update_id and is_deleted=0");
		$system_no_prefix=$id;
		$system_no=$id;
		$extention_no='';
	
		if($mst_data[0][csf('style_ref')]==$txt_style_ref && $cbo_bulletin_copy==1 && $cbo_bulletin_type==$mst_data[0][csf('bulletin_type')] && $mst_data[0][csf('buyer_id')]==$cbo_buyer && $mst_data[0][csf('gmts_item_id')]==$cbo_gmt_item){echo "11**";disconnect($con);die;}
		
		$mst_data[0][csf('buyer_id')]=$cbo_buyer;
		$mst_data[0][csf('style_ref')]=$txt_style_ref;
		$mst_data[0][csf('bulletin_type')]=$cbo_bulletin_type;
	
	}
	
	if(str_replace("'",'',$cbo_bulletin_type)==3){
		if($db_type==0)
		{
			$applicabile_date = date("Y-m-d",time());
		}
		else
		{
			$applicabile_date = change_date_format(date("Y-m-d",time()),'','',1);
		}
	}
	
	
 	
	
	
	$field_array="id, system_no_prefix, extention_no, system_no, extended_from, buyer_id, style_ref, gmts_item_id, working_hour, operation_count, mc_operation_count, total_smv, tot_mc_smv, tot_manual_smv, tot_finishing_smv, color_type, bulletin_type,APPLICABLE_PERIOD, is_copied,process_id, inserted_by, insert_date";
	$data_array="(".$id.",".$system_no_prefix.",'".$extention_no."','".$system_no."',".$update_id.",'".$mst_data[0][csf('buyer_id')]."','".$mst_data[0][csf('style_ref')]."',".$cbo_gmt_item.",'".$mst_data[0][csf('working_hour')]."','".$mst_data[0][csf('operation_count')]."','".$mst_data[0][csf('mc_operation_count')]."','".$mst_data[0][csf('total_smv')]."','".$mst_data[0][csf('tot_mc_smv')]."','".$mst_data[0][csf('tot_manual_smv')]."','".$mst_data[0][csf('tot_finishing_smv')]."','".$mst_data[0][csf('color_type')]."',".$cbo_bulletin_type.",'".$applicabile_date."',".$cbo_bulletin_copy.",".$cbo_process_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
	
	//echo "insert into PPL_GSD_ENTRY_MST ($field_array) value ".$data_array;die;
	
	$dtls_id_arr=array();
	$id_dtls=return_next_id( "id","ppl_gsd_entry_dtls",1);
	$field_array_dtls ="id, mst_id, row_sequence_no, resource_gsd, body_part_id, lib_sewing_id, attachment_id, efficiency, target_on_full_perc, target_on_effi_perc, operator_smv, helper_smv, total_smv";
	
	$sql_dtls="select id, row_sequence_no, resource_gsd, body_part_id, lib_sewing_id, attachment_id, efficiency, target_on_full_perc, target_on_effi_perc, operator_smv, helper_smv, total_smv from ppl_gsd_entry_dtls where mst_id=$update_id and is_deleted=0 order by row_sequence_no asc";
	$result=sql_select($sql_dtls);
	foreach($result as $row)
	{
		if($data_array_dtls!="") $data_array_dtls.=","; 
		$data_array_dtls.="(".$id_dtls.",".$id.",".$row[csf('row_sequence_no')].",'".$row[csf('resource_gsd')]."','".$row[csf('body_part_id')]."','".$row[csf('lib_sewing_id')]."','".$row[csf('attachment_id')]."','".$row[csf('efficiency')]."','".$row[csf('target_on_full_perc')]."','".$row[csf('target_on_effi_perc')]."','".$row[csf('operator_smv')]."','".$row[csf('helper_smv')]."','".$row[csf('total_smv')]."')"; 
		
		$next_seq_no=$row[csf('row_sequence_no')];
		$dtls_id_arr[$row[csf('id')]]=$id_dtls;
		$id_dtls++;
	}
	$next_seq_no+=1;
	
	$idBl=return_next_id( "id", "ppl_balancing_mst_entry", 1 ) ;
	$field_arrayBl="id,gsd_mst_id,allocated_mp,line_no,pitch_time,target,efficiency,balancing_page,max_work_load,min_work_load, tot_smv,body_size,thread_cons_date,input_uom, total_req, meter_per_gmts, balance_mst_id, line_shape, layout_date, no_of_work_st, inserted_by, insert_date";
	
	$field_array_dtls_bl="id, gsd_mst_id, gsd_dtls_id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, smv, target_hundred_perc, cycle_time, theoritical_mp, layout_mp, work_load, weight, worker_tracking";
	$dtls_id_bl = return_next_id( "id", "ppl_balancing_dtls_entry", 1 );
	
	$field_array_dtls_wl="id, gsd_mst_id, mst_id, smv, target, work_load";
	$dtls_id_wk = return_next_id( "id", "ppl_bl_wk_dtls_entry", 1 );
	
	$field_array_dtls_bl2="id, gsd_mst_id, gsd_dtls_id, mst_id, row_sequence_no, lib_sewing_id, resource_gsd, smv";
	$dtls_id_bl2 = return_next_id( "id", "ppl_balancing2_dtls_entry", 1 );
	
	$field_array_dtls_lo="id, gsd_mst_id, gsd_dtls_id, mst_id, row_sequence_no, lib_sewing_id, resource_gsd, smv, target_hundred_perc, work_station";
	$dtls_id_lo = return_next_id( "id", "ppl_layout_dtls_entry", 1 );
	
	$dtls_id2 = return_next_id( "id", "ppl_thread_cons_dtls_entry", 1 );
	$field_array_dtls2="id, gsd_mst_id, gsd_dtls_id, mst_id, operation_id, req_qty, seam_length, inserted_by, insert_date";
	
	$dtls_id_tc = return_next_id( "id", "ppl_thread_cons_op_dtls_entry", 1 );
	$field_array_dtls_tc="id, gsd_mst_id, mst_id, dtls_id, thread_type, thread_desc, thread_length, allowance, req_thread";
	
	$bl_mst_data=sql_select("select id, allocated_mp, line_no, pitch_time, target, efficiency, balancing_page, max_work_load, min_work_load, tot_smv, body_size, thread_cons_date, input_uom, total_req, meter_per_gmts, balance_mst_id, line_shape, layout_date, no_of_work_st from ppl_balancing_mst_entry where gsd_mst_id=$update_id  and is_deleted=0 order by balancing_page");
	foreach($bl_mst_data as $row)
	{
		if($data_array_bl!="") $data_array_bl.=","; 
		$data_array_bl.="(".$idBl.",".$id.",'".$row[csf('allocated_mp')]."','".$row[csf('line_no')]."','".$row[csf('pitch_time')]."','".$row[csf('target')]."','".$row[csf('efficiency')]."','".$row[csf('balancing_page')]."','".$row[csf('max_work_load')]."','".$row[csf('min_work_load')]."','".$row[csf('tot_smv')]."','".$row[csf('body_size')]."','".$row[csf('thread_cons_date')]."','".$row[csf('input_uom')]."','".$row[csf('total_req')]."','".$row[csf('meter_per_gmts')]."','".$row[csf('balance_mst_id')]."','".$row[csf('line_shape')]."','".$row[csf('layout_date')]."','".$row[csf('no_of_work_st')]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		if($row[csf('balancing_page')]==1)
		{
			$bl_dtls_data=sql_select("select gsd_dtls_id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, smv, target_hundred_perc, cycle_time, theoritical_mp, layout_mp, work_load, weight, worker_tracking from ppl_balancing_dtls_entry where mst_id='".$row[csf('id')]."' and is_deleted=0 order by id");
			foreach($bl_dtls_data as $rowbl)
			{
				if($data_array_dtls_bl!="") $data_array_dtls_bl.=",";
				$data_array_dtls_bl.="(".$dtls_id_bl.",".$id.",'".$dtls_id_arr[$rowbl[csf('gsd_dtls_id')]]."',".$idBl.",'".$rowbl[csf('row_sequence_no')]."','".$rowbl[csf('body_part_id')]."','".$rowbl[csf('lib_sewing_id')]."','".$rowbl[csf('resource_gsd')]."','".$rowbl[csf('smv')]."','".$rowbl[csf('target_hundred_perc')]."','".$rowbl[csf('cycle_time')]."','".$rowbl[csf('theoritical_mp')]."','".$rowbl[csf('layout_mp')]."','".$rowbl[csf('work_load')]."','".$rowbl[csf('weight')]."','".$rowbl[csf('worker_tracking')]."')";
				$dtls_id_bl = $dtls_id_bl+1;
			}
		}
		else if($row[csf('balancing_page')]==2)
		{
			$wk_dtls_data=sql_select("select smv, target, work_load from ppl_bl_wk_dtls_entry where mst_id='".$row[csf('id')]."' and is_deleted=0 order by id");
			foreach($wk_dtls_data as $rowWl)
			{
				if($data_array_dtls_wk!="") $data_array_dtls_wk.=",";
				$data_array_dtls_wk.="(".$dtls_id_wk.",".$id.",'".$idBl."','".$rowWl[csf('smv')]."','".$rowWl[csf('target')]."','".$rowWl[csf('work_load')]."')";
				$dtls_id_wk = $dtls_id_wk+1;
			}
			
			$bl2_dtls_data=sql_select("select gsd_dtls_id,row_sequence_no,lib_sewing_id,resource_gsd,smv from ppl_balancing2_dtls_entry where mst_id='".$row[csf('id')]."' and is_deleted=0 order by id");
			foreach($bl2_dtls_data as $rowbl2)
			{
				if($data_array_dtls_bl2!="") $data_array_dtls_bl2.=",";
				$data_array_dtls_bl2.="(".$dtls_id_bl2.",".$id.",'".$dtls_id_arr[$rowbl2[csf('gsd_dtls_id')]]."',".$idBl.",'".$rowbl2[csf('row_sequence_no')]."','".$rowbl2[csf('lib_sewing_id')]."','".$rowbl2[csf('resource_gsd')]."','".$rowbl2[csf('smv')]."')";
				$dtls_id_bl2 = $dtls_id_bl2+1;
			}
		}
		else if($row[csf('balancing_page')]==3)
		{
			$lo_dtls_data=sql_select("select gsd_dtls_id, row_sequence_no, lib_sewing_id, resource_gsd, smv, target_hundred_perc,work_station from ppl_layout_dtls_entry where mst_id='".$row[csf('id')]."' and is_deleted=0 order by id");
			foreach($lo_dtls_data as $rowlo)
			{
				if($data_array_dtls_lo!="") $data_array_dtls_lo.=",";
				$data_array_dtls_lo.="(".$dtls_id_lo.",".$id.",'".$dtls_id_arr[$rowlo[csf('gsd_dtls_id')]]."',".$idBl.",'".$rowlo[csf('row_sequence_no')]."','".$rowlo[csf('lib_sewing_id')]."','".$rowlo[csf('resource_gsd')]."','".$rowlo[csf('smv')]."','".$rowlo[csf('target_hundred_perc')]."','".$rowlo[csf('work_station')]."')";
				$dtls_id_lo = $dtls_id_lo+1;
			}
		}
		else if($row[csf('balancing_page')]==4)
		{
			$tc_data_arr=array();
			$dtls_data2=sql_select("select id, gsd_dtls_id, operation_id, req_qty, seam_length from ppl_thread_cons_dtls_entry where mst_id='".$row[csf('id')]."' and is_deleted=0 order by id");
			foreach($dtls_data2 as $rowtc)
			{
				if($data_array_dtls2!="") $data_array_dtls2.=",";
				$data_array_dtls2.="(".$dtls_id2.",".$id.",'".$dtls_id_arr[$rowtc[csf('gsd_dtls_id')]]."',".$idBl.",".$rowtc[csf('operation_id')].",'".$rowtc[csf('req_qty')]."','".$rowtc[csf('seam_length')]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$tc_data_arr[$rowtc[csf('id')]]=$dtls_id2;
				$dtls_id2 = $dtls_id2+1;
			}

			$tc_dtls_data=sql_select("select dtls_id, thread_type, thread_desc, thread_length, allowance, req_thread from ppl_thread_cons_op_dtls_entry where mst_id='".$row[csf('id')]."' and is_deleted=0 order by id");
			foreach($tc_dtls_data as $rowbtc)
			{
				if($data_array_dtls_tc!="") $data_array_dtls_tc.=",";
				$data_array_dtls_tc.="(".$dtls_id_tc.",".$id.",".$idBl.",'".$tc_data_arr[$rowbtc[csf('dtls_id')]]."','".$rowbtc[csf('thread_type')]."','".$rowbtc[csf('thread_desc')]."','".$rowbtc[csf('thread_length')]."','".$rowbtc[csf('allowance')]."','".$rowbtc[csf('req_thread')]."')";
				$dtls_id_tc = $dtls_id_tc+1;
			}
		}
		
		$idBl++;
	}
	
	//echo "10**insert into ppl_layout_dtls_entry (".$field_array_dtls_lo.") values ".$data_array_dtls_lo;die;
	//echo "10**insert into ppl_balancing_mst_entry (".$field_arrayBl.") values ".$data_array_bl;die;
	//echo "insert into ppl_gsd_entry_mst (".$field_array.") values ".$data_array;die;
	//echo "insert into ppl_gsd_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;  
	$rID=sql_insert("ppl_gsd_entry_mst",$field_array,$data_array,1);
	$rID2=sql_insert("ppl_gsd_entry_dtls",$field_array_dtls,$data_array_dtls,1); 

	
	
	
	$rID3=true; $rID4=true; $rID5=true; $rID6=true; $rID7=true; $rID8=true; $rID9=true;
	
	if($data_array_bl!="")
	{
		$rID3=sql_insert("ppl_balancing_mst_entry",$field_arrayBl,$data_array_bl,0);
	}
	
	if($data_array_dtls_bl!="")
	{
		$rID4=sql_insert("ppl_balancing_dtls_entry",$field_array_dtls_bl,$data_array_dtls_bl,1);
	}
	
	if($data_array_dtls_wk!="")
	{
		$rID5=sql_insert("ppl_bl_wk_dtls_entry",$field_array_dtls_wl,$data_array_dtls_wk,1);
	}
	
	if($data_array_dtls_bl2!="")
	{
		$rID6=sql_insert("ppl_balancing2_dtls_entry",$field_array_dtls_bl2,$data_array_dtls_bl2,1);
	}
	
	if($data_array_dtls2!="")
	{
		$rID7=sql_insert("ppl_thread_cons_dtls_entry",$field_array_dtls2,$data_array_dtls2,1);
	}
	
	if($data_array_dtls_tc!="")
	{
		$rID8=sql_insert("ppl_thread_cons_op_dtls_entry",$field_array_dtls_tc,$data_array_dtls_tc,1);
	}
	
	if($data_array_dtls_lo!="")
	{
		$rID9=sql_insert("ppl_layout_dtls_entry",$field_array_dtls_lo,$data_array_dtls_lo,1);
	}
	//echo $rID ."&&". $rID2 ."&&". $rID3 ."&&". $rID4 ."&&". $rID5 ."&&". $rID6 ."&&". $rID7 ."&&". $rID8 ."&&". $rID9;die;
	if($db_type==0)
	{
		if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8 && $rID9)
		{
			mysql_query("COMMIT");  
			echo "100**".$id."**".$next_seq_no."**".$system_no_prefix."**".$extention_no."**".date("d-m-Y",time());
		}
		else 
		{
			mysql_query("ROLLBACK"); 
			echo "10**".$id;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8 && $rID9)
		{
			oci_commit($con);  
			echo "100**".$id."**".$next_seq_no."**".$system_no_prefix."**".$extention_no."**".date("d-m-Y",time());
		}
		else
		{
			oci_rollback($con);
			echo "10**".$id;
		}
	}
	disconnect($con);
	die;
}

if ($action=="show_details_list_view")
{
	$operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name");
	$sql_result =sql_select("SELECT a.PROCESS_ID,b.id, b.mst_id, b.row_sequence_no, b.body_part_id, b.lib_sewing_id, b.resource_gsd, b.operator_smv, b.helper_smv from ppl_gsd_entry_mst a,ppl_gsd_entry_dtls b where a.id=b.mst_id and b.mst_id=$data and b.is_deleted=0 order by b.row_sequence_no asc");

	$production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and process_id = {$mstDataArray[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );
	?>
	<table width="425" cellspacing="0" border="1" rules="all" class="rpt_table" id="gsd_tbl">
        <thead>
        	<th width="20"></th>
            <th width="45">Seq. No</th>
            <th width="70">Body Part</th>
            <th width="100">Operation</th>
            <th width="70">Resource</th>
            <th width="60">Machine SMV</th>
            <th>Manual SMV</th>
        </thead>
        <tbody>
		<?
		$k=1;
        foreach($sql_result as $row)
        {
			if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
        ?>
    		<tr bgcolor="<? echo $bgcolor; ?>" id="gsd_<? echo $k; ?>">
            	<td align="center"><input type="radio" name="seqRa" id="seqRa_<? echo $k; ?>" value="<? echo $k; ?>" /></td> 
                <td align="center">
                	<input type="text" name="seqNo[]" id="seqNo_<? echo $k; ?>" value="<? echo $row[csf('row_sequence_no')]; ?>" class="text_boxes_numeric" style="width:30px;">
                    <input type="hidden" name="dtlsIdS[]" id="dtlsIdS_<? echo $k; ?>" value="<? echo $row[csf('id')]; ?>"> <!--onBlur="duplication_check(<?echo $k; ?>)"-->
                </td>
                <td><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
                <td><? echo $operation_arr[$row[csf('lib_sewing_id')]]; ?></td>
                <td><? echo $production_resource_arr[$row[csf('resource_gsd')]]; ?>&nbsp;</td>
                <td align="right"><? echo number_format($row[csf('operator_smv')],2); ?></td>
                <td align="right"><? echo number_format($row[csf('helper_smv')],2); ?></td>
            </tr>
		<? 
       		$k++;
        }
        ?>
		</tbody>
    </table>
    <table width="420">
    	<tr>
        	<td align="center">
            	<input type="button" name="button" class="formbuttonplasminus" value="Assending" onClick="fnc_save(1);" />&nbsp;&nbsp;&nbsp;
                <input type="button" name="button" class="formbuttonplasminus" value="Assending Seq. Down" onClick="re_arrange_table();" />
            </td>
        </tr>
    </table>
<?
	exit();
}


if($action=="update_seq_no")
{
	$con = connect();
	$datas=explode("|",$data);
	$field_array_dtls_update="row_sequence_no"; $id_arr=array();
	foreach($datas as $value)
	{
		$val=explode("_",$value);
		$seq_no=$val[0];
		$update_dtls_id=$val[1];
		$id_arr[]=$update_dtls_id;
		$data_array_dtls_update[$update_dtls_id]=array($seq_no);
	}
	//echo bulk_update_sql_statement("ppl_gsd_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );
	$rID=execute_query(bulk_update_sql_statement("ppl_gsd_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ));
	if($db_type==0)
	{
		if($rID) echo "1"; else echo "10";
	}
	else
	{
		if($rID) 
		{
			oci_commit($con);
			echo "1";
		}
		else 
		{
			oci_rollback($con);
			echo "10";
		}
	}
	disconnect($con);
	die;
}

if($action=="variable_setting_work_study")
{
	if($data>0){$where_con = " and COMPANY_NAME=$data";}
	$value=return_field_value("work_study_mapping_id", "variable_order_tracking", "variable_list=55 and status_active=1 and is_deleted=0 and  id in(select min(id) from variable_order_tracking where variable_list=55 $where_con and status_active=1 and is_deleted=0) order by id asc");
	echo $value;
	exit();
}

if($action=="quotation_popup_inq")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
 
?>
     
<script>
	function js_set_value(id)
	{
 		$("#hidden_inquiry_id").val(id); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="500" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
            	<tr>
                    <th colspan="8">
                      <?
                       echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "--Searching Type--" );
                      ?>
                    </th>
           		</tr>
                <tr> 
                    <th width="150">Company Name</th>
                    <th width="140">Buyer Name</th>
                    <th width="100">Inquery ID</th>
                    <th width="80">Year</th>
                    <th width="150">Style Reff.</th>
                    <th width="100">Buyer Inquery No</th>
                    <th width="100">Inquery Date </th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr class="general">
					<td>
						<?
							echo create_drop_down( "cbo_company_id",150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $cbo_company_id, "load_drop_down( 'ws_gsd_controller', this.value, 'load_drop_down_buyer', 'buyer_td');","1","","","","",2);
						?>
					</td>
                    <td id="buyer_td">
                        <?  
							echo create_drop_down( "cbo_buyer_name", 135, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 group by id,buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                        ?>
                    </td>
                    <td>				
                        <input type="text" style="width:80px" class="text_boxes"  name="txt_inquery_id" id="txt_inquery_id" />
                        <input type="hidden" id="hidden_inquiry_id" value="" />	
                    </td>
                   <td>
                     <? 
                        echo create_drop_down( "cbo_year", 70, $year,"", 1, "- Select- ", date('Y'), "" );
                     ?>	
                    </td>
                    <td width="" align="center" >				
                        <input type="text" style="width:120px" class="text_boxes"  name="txt_style" id="txt_style" />	
                    </td>
                    <td >				
                        <input type="text" style="width:80px" class="text_boxes"  name="txt_requst_no" id="txt_requst_no" />	
                    </td>    
                    <td>
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="  Date" />
                    </td> 
                    <td>
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_date_from').value+'_'+<? echo "0"; ?>+'_'+document.getElementById('txt_inquery_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_requst_no').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_id').value, 'create_quotation_inq_search_list_view', 'search_div', 'ws_gsd_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />				
                    </td>
            	</tr>
            </tbody>
        </table>
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_quotation_inq_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_buyer = $ex_data[0];
	$txt_style = $ex_data[1];
	$inq_date = $ex_data[2];
	$company =0;
    if($company==0) $company_name=""; else $company_name=" and company_id=$company";
	if($txt_buyer==0) $buyer_name=""; else $buyer_name="and buyer_id=$txt_buyer";
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(`insert_date`, '-', 1)=$ex_data[5]";
	if($db_type==2) $year_cond=" and to_char(insert_date,'YYYY')=$ex_data[5]";
	if( $inq_date!="" )  $inquery_date.= " and inquery_date='".change_date_format($inq_date,'yyyy-mm-dd',"-",1)."'";
	
	$sql_cond='';
	$inquery_id_cond='';
	$request_no='';
	if($ex_data[7]==1)
	{
		if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce='".str_replace("'","",$txt_style)."'";
		if (trim($ex_data[4])!="")  $inquery_id_cond=" and system_number_prefix_num='$ex_data[4]'  $year_cond"; 
		if (trim($ex_data[6])!="") $request_no=" and buyer_request='$ex_data[6]'"; 
	}

	if($ex_data[7]==4 || $ex_data[7]==0)
	{
		if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."%' ";
		if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$ex_data[4]%' $year_cond";
		if (trim($ex_data[6])!="") $request_no=" and buyer_request like '%$ex_data[6]%' ";
	}
	
	if($ex_data[7]==2)
	{
		if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '".str_replace("'","",$txt_style)."%' ";
		if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '$ex_data[4]%' $year_cond";
		if (trim($ex_data[6])!="") $request_no=" and buyer_request like '$ex_data[6]%' "; 
	}
	
	if($ex_data[7]==3)
	{
		if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."' ";
		if (trim($ex_data[4])!="") $inquery_id_cond=" and system_number_prefix_num like '%$ex_data[4]' $year_cond"; 
		if (trim($ex_data[6])!="") $request_no=" and buyer_request like '%$ex_data[6]' "; 
	}

	if($ex_data[8]>0){$company_con = " and COMPANY_ID=".$ex_data[8];}

 
 
	$season_buyer_wise_arr = return_library_array("select id,season_name from  lib_buyer_season ","id","season_name");
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$arr=array(0=>$company_arr,1=>$buyer_arr,7=>$season_buyer_wise_arr,8=>$row_status);
	 $sql = "select system_number_prefix_num, system_number, buyer_request, company_id, buyer_id, season_buyer_wise, inquery_date, style_refernce, status_active, extract(year from insert_date) as year, id from wo_quotation_inquery where is_deleted=0 $company_name $buyer_name $sql_cond $inquery_id_cond $request_no $inquery_date $company_con  order by id  DESC";
	//echo $sql;
	echo create_list_view("list_view", "Company Name,Buyer Name,Inquery ID,Year,Buyer Inquery No,Style Reff., Inquery Date,Season,Status","120,120,70,50,70,120,90,120,100","920","260",0, $sql , "js_set_value", "id", "", 1, "company_id,buyer_id,0,0,0,0,0,season_buyer_wise,status_active", $arr, "company_id,buyer_id,system_number_prefix_num,year,buyer_request,style_refernce,inquery_date,season_buyer_wise,status_active", "",'','0') ;
	?>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="populate_data_from_data")
{
    //$sql2 = "select  id,style_refernce from wo_quotation_inquery where id='$data'";
	//echo $sql2;die;
    $sql = sql_select("select  id,style_refernce,buyer_id from wo_quotation_inquery where id='$data'");
	foreach($sql as $row)
	{
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_refernce")]."';\n"; 
		echo "document.getElementById('cbo_buyer').value = '".$row[csf("BUYER_ID")]."';\n";
		echo "$('#cbo_buyer').attr('disabled','disabled');\n";
	}
}

if($action=="load_drop_down_gmt_item")
{
    $sql = sql_select("select  PRODUCT_DEPARTMENT_ID,GMTS_ITEM_ID from LIB_STYLE_REF where id='$data'");
	foreach($sql as $row)
	{
		 asort($garments_item); 
		 echo create_drop_down( "cbo_gmt_item", 140, $garments_item, "", 1, "--  Select --", 0, "load_operation()", 0,$row['GMTS_ITEM_ID']); exit();
	}
}

if($action=="quotation_popup_quick_costing")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
<script>
	function js_set_value(id)
	{
 		$("#hidden_inquiry_id").val(id); // mrr number
		parent.emailwindow.hide();
	}
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="500" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
				<th colspan="8" align="center">
					<? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "--Searching Type--" );?>
				</th>  
           </thead>
            <thead>
                <tr> 
                    <th width="150">Company Name</th>
                    <th width="140">Buyer Name</th>
                    <th width="100">Template Name</th>
                    <th width="80">Year</th>
                    <th width="150" >Style Reff.</th>
                    <th width="150" >Cost Sheet No</th>
                     <th width="100">Costing Date</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /></th>           
                </tr>
            </thead>
            <tbody>
                <tr>
					<td>
					<?
					echo create_drop_down( "cbo_company_id",150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--All Company--", $cbo_company_id, "load_drop_down( 'ws_gsd_controller', this.value, 'load_drop_down_buyer', 'buyer_td');","1","","","","",2);
					?>
					</td>
                    <td id="buyer_id">
					<?
					echo create_drop_down( "cbo_buyer_name", 135, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 group by id,buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
					?>
                    </td>
                    <td width="" align="center" >				
                    <?  
					$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
					if($db_type==0) $concat_cond="group_concat(lib_item_id)";
					else if($db_type==2) $concat_cond="listagg(cast(lib_item_id as varchar2(4000)),',') within group (order by lib_item_id)";
					else $concat_cond="";
					$sql_tmp="select temp_id, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by temp_id order by temp_id ASC";
					$sql_tmp_res=sql_select($sql_tmp);
					//print_r($sql_tmp_res);die;
					$template_name_arr=array();
					foreach($sql_tmp_res as $row)
					{
						$lib_temp_id='';
						
						$ex_temp_id=explode(',',$row[csf('lib_item_id')]);
						foreach($ex_temp_id as $lib_id)
						{
							if($lib_temp_id=="") $lib_temp_id=$lib_temp_arr[$lib_id]; else $lib_temp_id.=','.$lib_temp_arr[$lib_id];
						}
						
						$template_name_arr[$row[csf('temp_id')]]=$lib_temp_id;
					}
					//print_r($template_name_arr);
					echo create_drop_down( "cbo_temp_id", 130, $template_name_arr,'', 1, "-Select Template-",$selected, "" ); ?>
                    </td>
                    <td>
						<? 
						echo create_drop_down( "cbo_year", 70, $year,"", 1, "- Select- ", date('Y'), "" );
						?>
                    </td>
                    <td width="" align="center" >				
                        <input type="text" style="width:120px" class="text_boxes"  name="txt_style" id="txt_style" />	
                    </td>
                    <td width="" align="center" >				
                        <input type="text" style="width:120px" class="text_boxes"  name="txt_cost_sheet_no" id="txt_cost_sheet_no" />	
                    </td>
					
                   <input type="hidden" style="width:80px" class="text_boxes"  name="txt_requst_no" id="txt_requst_no" />	  
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="  Date" />
                        
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_date_from').value+'_'+<? echo "0"; ?>+'_'+document.getElementById('cbo_temp_id').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_requst_no').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_cost_sheet_no').value, 'create_quotation_quick_cost_search_list_view', 'search_div', 'ws_gsd_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />				
                    </td>
            </tr>
        	<tr>                  
            	<td align="center"valign="middle" colspan="7">
                     <input type="hidden" id="hidden_inquiry_id" value="" />
                </td>
            </tr>    
            </tbody>
         </tr>         
        </table>    
        <div align="center" valign="top" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_quotation_quick_cost_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_buyer = $ex_data[0];
	$txt_style = $ex_data[1];
	$inq_date = $ex_data[2];
	$company =0;
    if($company==0) $company_name=""; else $company_name=" and company_id=$company";
	if($txt_buyer==0) $buyer_name=""; else $buyer_name="and buyer_id=$txt_buyer";
	if($db_type==0) $year_cond=" and SUBSTRING_INDEX(`insert_date`, '-', 1)=$ex_data[5]";
	if($db_type==2) $year_cond=" and to_char(insert_date,'YYYY')=$ex_data[5]";
	if( $inq_date!="" )  $inquery_date.= " and costing_date='".change_date_format($inq_date,'yyyy-mm-dd',"-",1)."'";
	
	$sql_cond='';
	$$temp_id='';
	$request_no='';
	if($ex_data[7]==1)
	{
		
		if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_ref='".str_replace("'","",$txt_style)."'";
		if (trim($ex_data[4])!=0)  $temp_id=" and temp_id='$ex_data[4]'  $year_cond"; 
		if (trim($ex_data[6])!="") $request_no=" and buyer_request='$ex_data[6]'"; 
		if(trim($ex_data[9])){$where_con .= " and cost_sheet_no like( '".trim($ex_data[9])."')";}
	}
	if($ex_data[7]==4 || $ex_data[7]==0)
	{
		if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_ref like '%".str_replace("'","",$txt_style)."%' ";
		if (trim($ex_data[4])!=0) $$temp_id=" and temp_id like '%$ex_data[4]%' $year_cond";
		if (trim($ex_data[6])!="") $request_no=" and buyer_request like '%$ex_data[6]%' ";
		if(trim($ex_data[9])){$where_con .= " and cost_sheet_no like( '%".trim($ex_data[9])."%')";}
	}
	if($ex_data[7]==2)
	{
		if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_ref like '".str_replace("'","",$txt_style)."%' ";
		if (trim($ex_data[4])!=0) $temp_id=" and temp_id like '$ex_data[4]%' $year_cond";
		if (trim($ex_data[6])!="") $request_no=" and buyer_request like '$ex_data[6]%' "; 
		if(trim($ex_data[9])){$where_con .= " and cost_sheet_no like( '".trim($ex_data[9])."%')";}
	}
	if($ex_data[7]==3)
	{
		if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_ref like '%".str_replace("'","",$txt_style)."' ";
		if (trim($ex_data[4])!=0) $temp_id=" and temp_id like '%$ex_data[4]' $year_cond"; 
		if (trim($ex_data[6])!="") $request_no=" and buyer_request like '%$ex_data[6]' "; 
		if(trim($ex_data[9])){$where_con .= " and cost_sheet_no like( '%".trim($ex_data[9])."')";}
	}

	if($ex_data[8]>0){$where_con .= " and company_id=".$ex_data[8];}
	
	 
	
	$season_buyer_wise_arr = return_library_array("select id,season_name from  lib_buyer_season ","id","season_name");
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");

	$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
	if($db_type==0) $concat_cond="group_concat(lib_item_id)";
	else if($db_type==2) $concat_cond="listagg(cast(lib_item_id as varchar2(4000)),',') within group (order by lib_item_id)";
	else $concat_cond="";
	$sql_tmp="select temp_id, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by temp_id order by temp_id ASC";
	$sql_tmp_res=sql_select($sql_tmp);
	//print_r($sql_tmp_res);die;
	$template_name_arr=array();
	foreach($sql_tmp_res as $row)
	{
		$lib_temp_id='';
		
		$ex_temp_id=explode(',',$row[csf('lib_item_id')]);
		foreach($ex_temp_id as $lib_id)
		{
			if($lib_temp_id=="") $lib_temp_id=$lib_temp_arr[$lib_id]; else $lib_temp_id.=','.$lib_temp_arr[$lib_id];
		}
		
		$template_name_arr[$row[csf('temp_id')]]=$lib_temp_id;
	}

	$arr = array(0=>$buyer_arr,1=>$lib_temp_arr,5=>$season_buyer_wise_arr,6=>$row_status);
	$sql = "select id, cost_sheet_id, cost_sheet_no, temp_id, lib_item_id, style_ref, buyer_id, cons_basis, season_id, style_des, department_id, delivery_date, exchange_rate, offer_qty, quoted_price, tgt_price, stage_id, costing_date,status_active,extract(year from insert_date) as year from qc_mst where is_deleted=0 $where_con $buyer_name $temp_id  $inquery_date  order by id desc ";
	 //echo $sql;die;
	echo create_list_view("list_view", "Buyer Name,Template ID,Year,Style Reff.,Cost Sheet No,Costing Date,Season,Status","120,120,50,120,110,110,80,80","910","260",0, $sql , "js_set_value", "id,buyer_id", "", 1, "buyer_id,temp_id,0,0,0,0,season_id,status_active", $arr, "buyer_id,temp_id,year,style_ref,cost_sheet_no,costing_date,season_id,status_active", "",'','0') ;
	?>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="populate_data_from_data_quick_costing")
{
    $sql = sql_select("select  ID,STYLE_REF,QC_NO from qc_mst where id='$data'");
	foreach($sql as $row)
	{
		echo "document.getElementById('txt_style_ref').value = '".$row['STYLE_REF']."';\n"; 
		echo "document.getElementById('txt_style_id').value = '".$row['QC_NO']."';\n"; 
		
	}
}

function fnc_smv_style_integration($db_type,$buyer_id,$txt_style_ref,$currercy,$sewSmv,$cutSmv,$page)
{
	if($page==8)
	{
		$buyer_id=str_replace("'","",$buyer_id);
		$style_ref=str_replace("'","",$txt_style_ref);
		$gmts_item=str_replace("'","",$currercy);
		$upid=str_replace("'","",$cutSmv);
		//echo $db_type.'='.$buyer_id.'='.$txt_style_ref.'='.$currercy.'='.$sewSmv.'='.$cutSmv.'='.$page;
		if($db_type==0) $costid_concat="group_concat(quotation_id)";
		else if($db_type==2) $costid_concat="listagg((cast(quotation_id as varchar2(4000))),',') within group (order by quotation_id)";
		//echo "selct $costid_concat as costid from wo_price_quotation_set_details where quot_id='$upid' ";
		$costidws_mapping_id=return_field_value("$costid_concat as costid", "wo_price_quotation_set_details", "quot_id='$upid' ","costid");
		//$costidws_mapping_id=return_field_value("$costid_concat as costid", "wo_price_quotation_set_details", "ws_id in ($upid) ","costid");
		//echo $costidws_mapping_id;
		if($costidws_mapping_id!="")
		{
			$costid_all=array_unique(explode(",",$costidws_mapping_id));
			$costid_str="";
			foreach($costid_all as $id)
			{
				if($costid_str=="") $costid_str=$id; else $costid_str.=",".$id;
			}
			//echo "select a.id, a.quotation_id, a.gmts_item_id, a.set_item_ratio, a.smv_pcs, a.smv_set, a.quot_id, b.company_id, b.buyer_id, b.currency, b.set_break_down, b.total_set_qnty, b.cm_cost_predefined_method_id, b.sew_smv from wo_price_quotation_set_details a, wo_price_quotation a where a.quotation_id=b.id and a.quotation_id in ($costid_str)";
			$wo_price_set=sql_select("select a.id, a.quotation_id, a.gmts_item_id, a.set_item_ratio, a.smv_pcs, a.smv_set, a.quot_id, b.company_id, b.buyer_id, b.currency, b.set_break_down, b.total_set_qnty, b.cm_cost_predefined_method_id, b.sew_smv from wo_price_quotation_set_details a, wo_price_quotation b where a.quotation_id=b.id and a.quotation_id in ($costid_str)");
			$price_break_down_data=''; $price_set_breck_down_sql=""; $add_row=0; $quotation_data_arr=array();
			$cbo_company_name=$wo_price_set[0][csf("company_id")];
			$quotation_id=$wo_price_set[0][csf("quotation_id")];
			$currercy=$wo_price_set[0][csf("currency")];
			
			foreach($wo_price_set as $row)
			{
				if($row[csf("set_item_ratio")]=='') $row[csf("set_item_ratio")]=0;
				if($row[csf("smv_pcs")]=='') $row[csf("smv_pcs")]=0;
				if($row[csf("smv_set")]=='') $row[csf("smv_set")]=0;
				if($row[csf("quot_id")]=='') $row[csf("quot_id")]=0;
				
				$smv_set=0; $smv=0;
				if($row[csf("gmts_item_id")]==$gmts_item) $smv=$sewSmv;
				else $smv=$row[csf("smv_pcs")];
				//echo $smv.'='.$row[csf("gmts_item_id")].'='.$gmts_item;
				$pre_smv=$row[csf("total_set_qnty")]*$row[csf("smv_pcs")];
				$smv_set=$smv*$row[csf("set_item_ratio")];
				$quotationset_smv=($smv*$row[csf("set_item_ratio")]);
				//echo $pre_smv.'='.$smv_set.'='.$quotationset_smv;
				if ($add_row!=0) $price_break_down_data.="__";
				$price_break_down_data.=$row[csf("gmts_item_id")].'_'.$row[csf("set_item_ratio")].'_'.$smv.'_'.$smv_set.'_'.$row[csf("quot_id")];
				$add_row++;
				
				$quotation_data_arr[$row[csf('quotation_id')]]['str']=$price_break_down_data;//explode("*",("'".$break_down_data."'*'".$jobset_smv."'"));
				$quotation_data_arr[$row[csf('quotation_id')]]['smv']+=$quotationset_smv;
				
				if($price_set_breck_down_sql=="") $price_set_breck_down_sql=$row[csf("id")].'**'.$row[csf("gmts_item_id")].'**'.$row[csf("set_item_ratio")].'**'.$row[csf("smv_pcs")].'**'.$row[csf("smv_set")].'**'.$row[csf("quot_id")].'**'.$row[csf("quotation_id")].'**'.$smv_set.'**'.$smv.'**'.$quotationset_smv;
				else $price_set_breck_down_sql.="***".$row[csf("id")].'**'.$row[csf("gmts_item_id")].'**'.$row[csf("set_item_ratio")].'**'.$row[csf("smv_pcs")].'**'.$row[csf("smv_set")].'**'.$row[csf("quot_id")].'**'.$row[csf("quotation_id")].'**'.$smv_set.'**'.$smv.'**'.$quotationset_smv;
				
			}
			//echo $price_set_breck_down_sql;
			$prifield_arr_set="smv_pcs*smv_set";
			$priset_breck_down_array=explode('***',str_replace("'",'',$price_set_breck_down_sql));
			for($c=0; $c < count($priset_breck_down_array); $c++)
			{
				$priset_breck_down_arr=explode('**',$priset_breck_down_array[$c]);
				$priidSet_arr[]=$priset_breck_down_arr[0];
				
				$pridata_arr_set[$priset_breck_down_arr[0]] =explode("*",("'".$priset_breck_down_arr[7]."'*'".$priset_breck_down_arr[8]."'"));
			}
			//print_r($pridata_arr_set);
			//echo bulk_update_sql_statement("wo_price_quotation_set_details", "id",$prifield_arr_set,$pridata_arr_set,$priidSet_arr );
			$pri_update_ws_to_ord=execute_query(bulk_update_sql_statement("wo_price_quotation_set_details", "id",$prifield_arr_set,$pridata_arr_set,$priidSet_arr ));
			
			//$prifield_arr_job="set_break_down*set_smv";
			if (count($wo_price_set)>0)
			{
				foreach($quotation_data_arr as $qid=>$data)
				{
					execute_query( "update wo_price_quotation set set_break_down='".$data['str']."', sew_smv='".$data['smv']."' where id ='".$qid."'",0);
				}
				
				$cm_cost=0;
				$price_cost_data=sql_select("select id, cm_cost_predefined_method_id, sew_smv, cut_smv, sew_effi_percent, 
	   cut_effi_percent, exchange_rate, machine_line, prod_line_hr, costing_per, quot_date from wo_price_quotation where id='$qid' and is_deleted=0 and status_active=1");
				$cm_cost_predefined_method_id=$price_cost_data[0][csf("cm_cost_predefined_method_id")]*1;
				$txt_sew_smv=str_replace("'","",$sewSmv)*1;//$pre_cost_data[0][csf("sew_smv")];
				$txt_cut_smv=$price_cost_data[0][csf("cut_smv")];
				$txt_sew_efficiency_per=$price_cost_data[0][csf("sew_effi_percent")]*1;
				$txt_cut_efficiency_per=$price_cost_data[0][csf("cut_effi_percent")]*1;
				//var txt_efficiency_wastage= parseFloat(document.getElementById('txt_efficiency_wastage').value);
				
				$cbo_currercy=str_replace("'","",$currercy);
				$txt_exchange_rate= $price_cost_data[0][csf("exchange_rate")]*1;
				$txt_machine_line= $price_cost_data[0][csf("machine_line")];
				$txt_prod_line_hr= $price_cost_data[0][csf("prod_line_hr")];
				$cbo_costing_per= $price_cost_data[0][csf("costing_per")];
				$costing_date= $price_cost_data[0][csf("quot_date")];
				//alert(cm_cost_predefined_method_id)
				$cbo_costing_per_value=0;
				if($cbo_costing_per==1) $cbo_costing_per_value=12;
				if($cbo_costing_per==2) $cbo_costing_per_value=1;
				if($cbo_costing_per==3) $cbo_costing_per_value=24;
				if($cbo_costing_per==4) $cbo_costing_per_value=36;
				if($cbo_costing_per==5) $cbo_costing_per_value=48;
				
				if($cbo_company_name=="" || $costing_date==0)
				{
					if($db_type==0) $txt_quotation_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-");	
					else if($db_type==2) $txt_quotation_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-",1);
				}
				else
				{
					if($db_type==0) $txt_quotation_date=change_date_format($costing_date, "yyyy-mm-dd", "-");	
					else if($db_type==2)$txt_quotation_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				
				$monthly_cm_expense=0; $no_factory_machine=0; $working_hour=0; $cost_per_minute=0;
				
				// MySql
				 /*$sql="select monthly_cm_expense,no_factory_machine,working_hour,cost_per_minute from lib_standard_cm_entry where company_id=$data  and status_active=1 and is_deleted=0 LIMIT 1";*/
				$sql="select monthly_cm_expense,no_factory_machine,working_hour,cost_per_minute from lib_standard_cm_entry where company_id='$cbo_company_name' and '$costing_date' between applying_period_date and applying_period_to_date   and status_active=1 and is_deleted=0";
			
				$data_array=sql_select($sql);
				foreach ($data_array as $row)
				{
					if($row[csf("monthly_cm_expense")] !="") $monthly_cm_expense=$row[csf("monthly_cm_expense")];
					if($row[csf("no_factory_machine")] !="") $no_factory_machine=$row[csf("no_factory_machine")];
					if($row[csf("working_hour")] !="") $working_hour=$row[csf("working_hour")];
					if($row[csf("cost_per_minute")] !="") $cost_per_minute=$row[csf("cost_per_minute")];
				}
				
				$sql_price_cost_dtls="select max(cm_cost) as cm_cost, sum(price_with_commn_dzn) as price_dzn, sum(price_with_commn_pcs) as price_pcs_set, sum(total_cost-cm_cost) as prev_tot_cost from wo_price_quotation_costing_mst where quotation_id='$quotation_id' and is_deleted=0 and status_active=1 group by quotation_id";
				$sql_pre_cost_dtls_arr=sql_select($sql_pre_cost_dtls);
				$price_dzn=0; $cost_pcs_set=0; $prev_tot_cost=0; $prev_cm_cost=0;
				
				$price_dzn=$sql_pre_cost_dtls_arr[0][csf("price_dzn")]*1;
				$price_pcs_set=$sql_pre_cost_dtls_arr[0][csf("price_pcs_set")]*1;
				$prev_tot_cost=$sql_pre_cost_dtls_arr[0][csf("prev_tot_cost")]*1;
				$prev_cm_cost=$sql_pre_cost_dtls_arr[0][csf("cm_cost")]*1;
				
				if($cm_cost_predefined_method_id==1){
					if($cost_per_minute==0 || $cost_per_minute=="" ){
						//alert("Insert Cost Per Minute in Library>Merchandising Detailes>Financial Parameter Setup");
						//return;
					}
					$txt_efficiency_wastage=100-$txt_sew_efficiency_per;
					//document.getElementById('txt_efficiency_wastage').value=txt_efficiency_wastage;
					$cm_cost=($txt_sew_smv*$cost_per_minute*$cbo_costing_per_value)+(($txt_sew_smv*$cost_per_minute*$cbo_costing_per_value)*($txt_efficiency_wastage/100));
					$cm_cost=$cm_cost/$txt_exchange_rate;
				}
				else if($cm_cost_predefined_method_id==2){
					/*if($cost_per_minute==0 || $cost_per_minute=="" ){
						alert("Insert Cost Per Minute in Library>Merchandising Detailes>Financial Parameter Setup");
						return;
					}*/
					$cut_per=$txt_cut_efficiency_per/100;
					$sew_per=$txt_sew_efficiency_per/100;
					$cu=($txt_cut_smv*$cost_per_minute*$cbo_costing_per_value)/$cut_per;
					$su=($txt_sew_smv*$cost_per_minute*$cbo_costing_per_value)/$sew_per;
					$cm_cost=($cu+$su)/$txt_exchange_rate;
				}
				else if($cm_cost_predefined_method_id==3){
					
					$per_day_cost=$monthly_cm_expense/26;
					$per_machine_cost=$per_day_cost/$no_factory_machine;
					$per_line_cost=$per_machine_cost*$txt_machine_line;
					$total_production_per_line=$txt_prod_line_hr*$working_hour;
					$per_product_cost=$per_line_cost/$total_production_per_line;
					if($cbo_costing_per==1){ $cm_cost=($per_product_cost*12)/$txt_exchange_rate; }
					if($cbo_costing_per==2){ $cm_cost=($per_product_cost*1)/$txt_exchange_rate; }
					if($cbo_costing_per==3){ $cm_cost=($per_product_cost*24)/$txt_exchange_rate; }
					if($cbo_costing_per==4){ $cm_cost=($per_product_cost*36)/$txt_exchange_rate; }
					if($cbo_costing_per==5){ $cm_cost=($per_product_cost*48)/$txt_exchange_rate; }
				}
				else if($cm_cost_predefined_method_id==4)
				{
					/*if(data[3]==0 ||data[3]=="" )
					{
					   alert("Insert Cost Per Minute in Library>Merchandising Detailes>Financial Parameter Setup");
					   return;
					}*/
					$sew_per=$txt_sew_efficiency_per/100;
					$su=((trim($cost_per_minute)/$sew_per)*$txt_sew_smv*$cbo_costing_per_value);
					$cm_cost=$su/$txt_exchange_rate;
				}
				else
				{
					$cm_cost=$prev_cm_cost;
				}	
					
				$dec_type=0;
				if (str_replace("'","",$currercy)==1) $dec_type=4; else $dec_type=5;
				
				$cm_cost=number_format($cm_cost,4,'.','');
				$cm_cost_per=number_format((($cm_cost/$price_dzn)*100),2,'.','');
				
				$tot_cost=number_format(($prev_tot_cost+$cm_cost),4,'.','');
				$tot_cost_per=number_format((($tot_cost/$price_dzn)*100),2,'.','');
				
				$margin_dzn=number_format(($price_dzn-$tot_cost),4,'.','');
				$margin_dzn_per=number_format((100-$tot_cost_per),2,'.','');
				
				$cost_pcs_set=number_format(($tot_cost/$cbo_costing_per_value),4,'.','');
				$cost_pcs_set_percent=number_format((($cost_pcs_set/$price_pcs_set)*100),2,'.','');
				
				$margin_pcs_set=number_format(($price_pcs_set-$cost_pcs_set),4,'.','');
				$margin_pcs_set_per=number_format((100-$cost_pcs_set_percent),2,'.','');
				
				
				$field_arr_pre_cost="cm_cost*cm_cost_percent*total_cost*total_cost_percent*margin_dzn*margin_dzn_percent*final_cost_pcs*final_cost_set_pcs_rate*price_with_commn_pcs*price_with_commn_percent_pcs";
				$data_arr_pre_cost="'".$cm_cost."'*'".$cm_cost_per."'*'".$tot_cost."'*'".$tot_cost_per."'*'".$margin_dzn."'*'".$margin_dzn_per."'*'".$cost_pcs_set."'*'".$cost_pcs_set_percent."'*'".$margin_pcs_set."'*'".$margin_pcs_set_per."'";
				
				$rID2=sql_update("wo_price_quotation_costing_mst",$field_arr_pre_cost,$data_arr_pre_cost,"quotation_id","'".$quotation_id."'",1);
			}
		}
		
		//=============================================Price Quotation End=======================
		
		
		//die;
		if($db_type==0) $job_concat="group_concat(a.job_no)";
		else if($db_type==2) $job_concat="listagg((cast(a.job_no as varchar2(4000))),',') within group (order by a.job_no)";
		
		$ws_mapping_id=return_field_value("$job_concat as job_no", "wo_po_details_mas_set_details a, wo_po_details_master b", "a.job_no=b.job_no and b.style_ref_no='$style_ref' and b.buyer_name='$buyer_id' and a.gmts_item_id='$gmts_item' and a.quot_id='$upid' and b.is_deleted=0 and b.status_active=1","job_no");
		//return $ws_mapping_id.'='.$cbo_company_name.'='.$txt_job_no.'='.$currercy.'='.$sewSmv.'='.$cutSmv.'='.$page; die;
		if($ws_mapping_id!='')
		{
			$job_no_all=array_unique(explode(",",$ws_mapping_id));
			$job_str="";
			foreach($job_no_all as $job)
			{
				if($job_str=="") $job_str="'".$job."'"; else $job_str.=",'".$job."'";
			}
			$wo_po_set=sql_select("select a.id, a.job_no, a.gmts_item_id, a.set_item_ratio, a.smv_pcs, a.smv_set, a.smv_pcs_precost, a.smv_set_precost, a.complexity, a.embelishment, a.cutsmv_pcs, a.cutsmv_set, a.finsmv_pcs, a.finsmv_set, a.printseq, a.embro, a.embroseq, a.wash, a.washseq, a.spworks, a.spworksseq, a.gmtsdying, a.gmtsdyingseq, a.quot_id, b.set_break_down, b.total_set_qnty, b.set_smv, b.company_name, currency_id from wo_po_details_mas_set_details a, wo_po_details_master b where a.job_no=b.job_no and a.job_no in ($job_str) and b.is_deleted=0 and b.status_active=1");
			$cbo_company_name=$wo_po_set[0][csf("company_name")];
			$txt_job_no=$wo_po_set[0][csf("job_no")];
			$currercy=$wo_po_set[0][csf("currency_id")];
			$set_breck_down_sql=""; $job_arr=array(); $break_down_data='';
			$job_data_arr=array(); $add=0;
			foreach($wo_po_set as $row)
			{
				if($row[csf("cutsmv_pcs")]=='') $row[csf("cutsmv_pcs")]=0;
				if($row[csf("cutsmv_set")]=='') $row[csf("cutsmv_set")]=0;
				if($row[csf("finsmv_pcs")]=='') $row[csf("finsmv_pcs")]=0;
				if($row[csf("finsmv_set")]=='') $row[csf("finsmv_set")]=0;
				
				if($row[csf("printseq")]=='') $row[csf("printseq")]=1;
				if($row[csf("embroseq")]=='') $row[csf("embroseq")]=2;
				if($row[csf("washseq")]=='') $row[csf("washseq")]=3;
				if($row[csf("spworksseq")]=='') $row[csf("spworksseq")]=4;
				if($row[csf("gmtsdyingseq")]=='') $row[csf("gmtsdyingseq")]=5;
				$smv_set=0; $smv=0;
				if($row[csf("gmts_item_id")]==$gmts_item) $smv=$sewSmv;
				else $smv=$row[csf("smv_pcs")];
				
				$pre_smv=$row[csf("total_set_qnty")]*$row[csf("smv_pcs")];
				$smv_set=$row[csf("set_smv")]*$row[csf("set_item_ratio")];
				$jobset_smv=($smv*$row[csf("set_item_ratio")]);
				//echo $row[csf("set_smv")]."=".$smv_set;
				
				if(!in_array($row[csf('job_no')],$job_arr))
				{
					$add=0;
					$job_arr[]=$row[csf('job_no')];
					$break_down_data='';
				}
				//echo $k; //die;
				if ($add!=0) $break_down_data.="__";
				$break_down_data.=$row[csf("gmts_item_id")].'_'.$row[csf("set_item_ratio")].'_'.$row[csf("smv_pcs")].'_'.$row[csf("smv_set")].'_'.$row[csf("complexity")].'_'.$row[csf("embelishment")].'_'.$row[csf("cutsmv_pcs")].'_'.$row[csf("cutsmv_set")].'_'.$row[csf("finsmv_pcs")].'_'.$row[csf("finsmv_set")].'_'.$row[csf("printseq")].'_'.$row[csf("embro")].'_'.$row[csf("embroseq")].'_'.$row[csf("wash")].'_'.$row[csf("washseq")].'_'.$row[csf("spworks")].'_'.$row[csf("spworksseq")].'_'.$row[csf("gmtsdying")].'_'.$row[csf("gmtsdyingseq")].'_'.$row[csf("quot_id")];
				$add++;
				
				$job_data_arr[$row[csf('job_no')]]['str']=$break_down_data;//explode("*",("'".$break_down_data."'*'".$jobset_smv."'"));
				$job_data_arr[$row[csf('job_no')]]['smv']=$jobset_smv;
				
				if($set_breck_down_sql=="") $set_breck_down_sql=$row[csf("id")].'**'.$row[csf("gmts_item_id")].'**'.$row[csf("set_item_ratio")].'**'.$row[csf("smv_pcs")].'**'.$row[csf("smv_set")].'**'.$row[csf("smv_pcs_precost")].'**'.$row[csf("smv_set_precost")].'**'.$row[csf("quot_id")].'**'.$row[csf("job_no")].'**'.$smv_set.'**'.$smv.'**'.$jobset_smv;
				else $set_breck_down_sql.="***".$row[csf("id")].'**'.$row[csf("gmts_item_id")].'**'.$row[csf("set_item_ratio")].'**'.$row[csf("smv_pcs")].'**'.$row[csf("smv_set")].'**'.$row[csf("smv_pcs_precost")].'**'.$row[csf("smv_set_precost")].'**'.$row[csf("quot_id")].'**'.$row[csf("job_no")].'**'.$smv_set.'**'.$smv.'**'.$jobset_smv;
			}
			//print_r($job_data_arr); die;
			
			$field_arr_set="smv_pcs*smv_set*smv_pcs_precost*smv_set_precost";
			$set_breck_down_array=explode('***',str_replace("'",'',$set_breck_down_sql));
			for($c=0; $c < count($set_breck_down_array); $c++)
			{
				$set_breck_down_arr=explode('**',$set_breck_down_array[$c]);
				$idSet_arr[]=$set_breck_down_arr[0];
				
				$data_arr_set[$set_breck_down_arr[0]] =explode("*",("'".$set_breck_down_arr[10]."'*'".$set_breck_down_arr[9]."'*'".$set_breck_down_arr[10]."'*'".$set_breck_down_arr[9]."'"));
			}
			$update_ws_to_ord=execute_query(bulk_update_sql_statement("wo_po_details_mas_set_details", "id",$field_arr_set,$data_arr_set,$idSet_arr ));
			
			$field_arr_job="set_break_down*set_smv";
			foreach($job_data_arr as $jobno=>$data)
			{
				execute_query( "update wo_po_details_master set set_break_down='".$data['str']."', set_smv='".$data['smv']."' where  job_no ='".$jobno."'",0);
			}
			//print_r($cbo_company_name);
			//echo bulk_update_sql_statement("wo_po_details_master", "job_no",$field_arr_job,$data_arrjob,$jobSet_arr );
		
			
		
			$is_pre_cost="";
			//echo "select job_no, cm_cost_predefined_method_id, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, exchange_rate, machine_line, prod_line_hr, costing_per, costing_date from wo_pre_cost_mst where job_no='$txt_job_no' and is_deleted=0 and status_active=1";die;
			$pre_cost_data=sql_select("select job_no, cm_cost_predefined_method_id, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, exchange_rate, machine_line, prod_line_hr, costing_per, costing_date from wo_pre_cost_mst where job_no='$txt_job_no' and is_deleted=0 and status_active=1");
			$cm_cost=0;
			
			$cm_cost_predefined_method_id=$pre_cost_data[0][csf("cm_cost_predefined_method_id")]*1;
			$txt_sew_smv=str_replace("'","",$sewSmv)*1;//$pre_cost_data[0][csf("sew_smv")];
			$txt_cut_smv=$pre_cost_data[0][csf("cut_smv")];
			$txt_sew_efficiency_per=$pre_cost_data[0][csf("sew_effi_percent")]*1;
			$txt_cut_efficiency_per=$pre_cost_data[0][csf("cut_effi_percent")]*1;
			//var txt_efficiency_wastage= parseFloat(document.getElementById('txt_efficiency_wastage').value);
			
			$cbo_currercy=str_replace("'","",$currercy);
			$txt_exchange_rate= $pre_cost_data[0][csf("exchange_rate")]*1;
			$txt_machine_line= $pre_cost_data[0][csf("machine_line")];
			$txt_prod_line_hr= $pre_cost_data[0][csf("prod_line_hr")];
			$cbo_costing_per= $pre_cost_data[0][csf("costing_per")];
			$costing_date= $pre_cost_data[0][csf("costing_date")];
			//var txt_job_no= document.getElementById('txt_job_no').value;
			
			$cbo_costing_per_value=0;
			if($cbo_costing_per==1) $cbo_costing_per_value=12;
			else if($cbo_costing_per==2) $cbo_costing_per_value=1;
			else if($cbo_costing_per==3) $cbo_costing_per_value=24;
			else if($cbo_costing_per==4) $cbo_costing_per_value=36;
			else if($cbo_costing_per==5) $cbo_costing_per_value=48;
			
			$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=22 and status_active=1 and is_deleted=0");
			if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
			
			if($cm_cost_method_based_on==1)
			{
				if($costing_date=="" || $costing_date==0)
				{
					if($db_type==0) $txt_costing_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-");	
					else if($db_type==2) $txt_costing_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-",1);
				}
				else
				{
					if($db_type==0) $txt_costing_date=change_date_format($costing_date, "yyyy-mm-dd", "-");	
					else if($db_type==2) $txt_costing_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
			}
			else if($cm_cost_method_based_on==2)
			{
				$min_shipment_sql=sql_select("select job_no_mst, min(shipment_date) as min_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
				$min_shipment_date="";
				foreach($min_shipment_sql as $row){ $min_shipment_date=$row[csf('min_shipment_date')]; }
				if($db_type==0) $txt_costing_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");	
				else if($db_type==2) $txt_costing_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
			}
			else if($cm_cost_method_based_on==3)
			{
				$max_shipment_sql=sql_select("select job_no_mst, max(shipment_date) as max_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
				$max_shipment_date="";
				foreach($max_shipment_sql as $row){ $max_shipment_date=$row[csf('max_shipment_date')]; }
				
				if($db_type==0) $txt_costing_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");	
				else if($db_type==2) $txt_costing_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
			}
			else if($cm_cost_method_based_on==4)
			{
				$max_shipment_sql=sql_select("select job_no_mst, min(pub_shipment_date) as min_pub_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
				$min_pub_shipment_date="";
				foreach($max_shipment_sql as $row){ $min_pub_shipment_date=$row[csf('min_pub_shipment_date')]; }
				
				if($db_type==0) $txt_costing_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");	
				else if($db_type==2) $txt_costing_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
			}
			else if($cm_cost_method_based_on==4)
			{
				$max_shipment_sql=sql_select("select job_no_mst, max(pub_shipment_date) as max_pub_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
				$max_pub_shipment_date="";
				foreach($max_shipment_sql as $row){ $max_pub_shipment_date=$row[csf('max_pub_shipment_date')]; }
				
				if($db_type==0) $txt_costing_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");	
				else if($db_type==2) $txt_costing_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
			}
			
			$monthly_cm_expense=0; $no_factory_machine=0; $working_hour=0; $cost_per_minute=0; $depreciation_amorti=0; $operating_expn=0;
			$limit="";
			if($db_type==0) $limit="LIMIT 1"; else if($db_type==2) $limit="";
			$sqlstnd_cm="select monthly_cm_expense, no_factory_machine, working_hour, cost_per_minute, depreciation_amorti, operating_expn from lib_standard_cm_entry where company_id=$cbo_company_name and '$txt_costing_date' between applying_period_date and applying_period_to_date and status_active=1 and is_deleted=0 $limit";
			$sqlstnd_cm_arr=sql_select($sqlstnd_cm);
			foreach ($sqlstnd_cm_arr as $row)
			{
				if($row[csf("monthly_cm_expense")] !="") $monthly_cm_expense=$row[csf("monthly_cm_expense")];
				if($row[csf("no_factory_machine")] !="") $no_factory_machine=$row[csf("no_factory_machine")];
				if($row[csf("working_hour")] !="") $working_hour=$row[csf("working_hour")];
				if($row[csf("cost_per_minute")] !="") $cost_per_minute=$row[csf("cost_per_minute")];
				if($row[csf("depreciation_amorti")] !="") $depreciation_amorti=$row[csf("depreciation_amorti")];
				if($row[csf("operating_expn")] !="")$operating_expn=$row[csf("operating_expn")];
			}
			//$data=$monthly_cm_expense."_".$no_factory_machine."_".$working_hour."_".$cost_per_minute."_".$depreciation_amorti."_".$operating_expn;
			
			$sql_pre_cost_dtls="select max(cm_cost) as cm_cost, sum(price_dzn) as price_dzn, sum(price_pcs_or_set) as price_pcs_set, sum(total_cost-cm_cost) as prev_tot_cost from wo_pre_cost_dtls where job_no='$txt_job_no' and is_deleted=0 and status_active=1 group by job_no";
			$sql_pre_cost_dtls_arr=sql_select($sql_pre_cost_dtls);
			$price_dzn=0; $cost_pcs_set=0; $prev_tot_cost=0; $prev_cm_cost=0;
			
			$price_dzn=$sql_pre_cost_dtls_arr[0][csf("price_dzn")]*1;
			$price_pcs_set=$sql_pre_cost_dtls_arr[0][csf("price_pcs_set")]*1;
			$prev_tot_cost=$sql_pre_cost_dtls_arr[0][csf("prev_tot_cost")]*1;
			$prev_cm_cost=$sql_pre_cost_dtls_arr[0][csf("cm_cost")]*1;
			
			if (count($pre_cost_data)>0)
			{
				execute_query( "update wo_pre_cost_mst set sew_smv='$txt_sew_smv', cut_smv='$txt_cut_smv' where job_no ='".$txt_job_no."'",1);
				if($cm_cost_predefined_method_id==1)
				{
					$txt_efficiency_wastage=100-$txt_sew_efficiency_per;
					//document.getElementById('txt_efficiency_wastage').value=txt_efficiency_wastage;
					$cm_cost=($txt_sew_smv*$cost_per_minute*$cbo_costing_per_value)+(($txt_sew_smv*$cost_per_minute*$cbo_costing_per_value)*($txt_efficiency_wastage/100));
					//alert(txt_exchange_rate)
					$cm_cost=$cm_cost/$txt_exchange_rate;
				}
				else if($cm_cost_predefined_method_id==2)
				{
					$cu=0; $su=0;
					$cut_per=$txt_cut_efficiency_per/100;
					$sew_per=$txt_sew_efficiency_per/100;
					$cu=($txt_cut_smv*trim(($cost_per_minute*1))*$cbo_costing_per_value)/($cut_per*1);
					if($cu=="") $cu=0;
					
					$su=($txt_sew_smv*trim(($cost_per_minute*1))*$cbo_costing_per_value)/($sew_per*1);
					if($su=='') $su=0;
					$cm_cost=($cu+$su)/$txt_exchange_rate;
				}
				else if($cm_cost_predefined_method_id==3)
				{
					//3. CM Cost = {(MCE/26)/NFM)*MPL)}/[{(PHL)*WH}]*Costing Per/Exchange Rate
					$per_day_cost=$monthly_cm_expense/26;
					$per_machine_cost=$per_day_cost/$no_factory_machine;
					$per_line_cost=$per_machine_cost*$txt_machine_line;
					$total_production_per_line=$txt_prod_line_hr*$working_hour;
					$per_product_cost=$per_line_cost/$total_production_per_line;
					
					$cm_cost=($per_product_cost*$cbo_costing_per_value)/$txt_exchange_rate;
				}
				else if($cm_cost_predefined_method_id==4)
				{
					$sew_per=$txt_sew_efficiency_per/100;
					$su=((trim(($cost_per_minute*1))/$sew_per)*($txt_sew_smv*$cbo_costing_per_value));
					$cm_cost=$su/$txt_exchange_rate;
				}
				else
				{
					$cm_cost=$prev_cm_cost;
				}
				
				$dec_type=0;
				if (str_replace("'","",$currercy)==1) $dec_type=4; else $dec_type=5;
				
				$cm_cost=number_format($cm_cost,4,'.','');
				$cm_cost_per=number_format((($cm_cost/$price_dzn)*100),2,'.','');
				
				$tot_cost=number_format(($prev_tot_cost+$cm_cost),4,'.','');
				$tot_cost_per=number_format((($tot_cost/$price_dzn)*100),2,'.','');
				
				$margin_dzn=number_format(($price_dzn-$tot_cost),4,'.','');
				$margin_dzn_per=number_format((100-$tot_cost_per),2,'.','');
				
				$cost_pcs_set=number_format(($tot_cost/$cbo_costing_per_value),4,'.','');
				$cost_pcs_set_percent=number_format((($cost_pcs_set/$price_pcs_set)*100),2,'.','');
				
				$margin_pcs_set=number_format(($price_pcs_set-$cost_pcs_set),4,'.','');
				$margin_pcs_set_per=number_format((100-$cost_pcs_set_percent),2,'.','');
				
				
				$field_arr_pre_cost="cm_cost*cm_cost_percent*total_cost*total_cost_percent*margin_dzn*margin_dzn_percent*cost_pcs_set*cost_pcs_set_percent*margin_pcs_set*margin_pcs_set_percent";
				$data_arr_pre_cost="'".$cm_cost."'*'".$cm_cost_per."'*'".$tot_cost."'*'".$tot_cost_per."'*'".$margin_dzn."'*'".$margin_dzn_per."'*'".$cost_pcs_set."'*'".$cost_pcs_set_percent."'*'".$margin_pcs_set."'*'".$margin_pcs_set_per."'";
				
				$rID2=sql_update("wo_pre_cost_dtls",$field_arr_pre_cost,$data_arr_pre_cost,"job_no","'".$txt_job_no."'",1);
			}
			else
			{
				return;
			}
		}
		//return $field_arr_pre_cost.'='.$data_arr_pre_cost; 
	}
}



if ($action=="style_ref_popup")
{
	echo load_html_head_contents("Style Ref Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );

?> 

	<script>
		
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		
		function js_set_value(id,buyer,style_ref,product_dep_id)
		{
			$('#hidden_style_id').val(id);
			$('#hidden_buyer_id').val(buyer);
			$('#hidden_style_ref').val(style_ref);
			$('#hidden_product_dep_id').val(product_dep_id);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:720px;margin-left:10px">
			<?
				$composition_arr=array();
				$compositionData=sql_select("select mst_id, copmposition_id, percent from lib_yarn_count_determina_dtls where status_active=1 and is_deleted=0");
				foreach( $compositionData as $row )
				{
					$composition_arr[$row[csf('mst_id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
				}
            ?>
            <input type="hidden" name="hidden_style_id" id="hidden_style_id" class="text_boxes" value="">
            <input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" class="text_boxes" value="">  
            <input type="hidden" name="hidden_style_ref" id="hidden_style_ref" class="text_boxes" value="">  
            <input type="hidden" name="hidden_internal_ref" id="hidden_internal_ref" class="text_boxes" value="">  
            <input type="hidden" name="hidden_product_dep_id" id="hidden_product_dep_id" class="text_boxes" value=""> 
            <div style="margin-left:10px; margin-top:10px">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680">
                    <thead>
                        <th width="50">SL</th>
                        <th>Style Ref</th>
                        <th width="150">Buyer</th>
                    </thead>
                </table>
                <div style="width:700px; max-height:300px; overflow-y:scroll" id="list_container" align="left"> 
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680" id="tbl_list_search">  
                        <?
                        $i=1; if($garments_nature=="") $garments_nature=0;
						$data_array=sql_select("select id, PRODUCT_DEPARTMENT_ID,style_ref_name,buyer_id from lib_style_ref where status_active=1 and is_deleted=0");
                        foreach($data_array as $row)
                        {  
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                         ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('buyer_id')]; ?>','<? echo $row[csf('style_ref_name')]; ?>','<? echo $row[PRODUCT_DEPARTMENT_ID]; ?>')" style="cursor:pointer" >
                                <td align="center" width="50"><? echo $i; ?></td>
                                <td><? echo $row[csf('style_ref_name')]; ?></td>
                                <td width="150"><? echo $buyer_name_arr[$row[csf('buyer_id')]]; ?></td>
                            </tr>
                        <? 
                        $i++; 
                        } 
                        ?>
                    </table>
                </div> 
            </div>
		</fieldset>
	</form>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

 

if($action=='check_production_against_this_bulletin')
{
	list($bulletin_type,$style_ref,$gmt_item,$update_id)=explode("__",$data);
	
	
	if($bulletin_type==4){
		$INSERT_DATE= return_field_value("INSERT_DATE","PPL_GSD_ENTRY_MST","id=$update_id","INSERT_DATE");
		
		
		$gsd_sql="SELECT INSERT_DATE,APPLICABLE_PERIOD,APPROVED from PPL_GSD_ENTRY_MST where id=$update_id";
		$gsd_sql_result=sql_select($gsd_sql);
		foreach ($gsd_sql_result as $row)
		{
			$insert_date = $row[INSERT_DATE];
			$applicable_period = $row[APPLICABLE_PERIOD];
			$approved_status = $row[APPROVED];
		}
		
 		$flag=0;
		if(strtotime($applicable_period)<= strtotime(date('Y-m-d',time())) && !empty($applicable_period) && $approved_status==1){
			$flag=1;
		}
		
		if($db_type==0)
		{
			$insert_date = date("Y-m-d H:i:s",strtotime($insert_date));
			//$applicable_period = date("Y-m-d H:i:s",strtotime($applicable_period));
		}
		else
		{
			$insert_date = change_date_format(date("Y-m-d H:i:s",strtotime($insert_date)),'','',1);
			//$applicable_period = change_date_format(date("Y-m-d H:i:s",strtotime($applicable_period)),'','',1);
		}
	
		
		
		$date_cond	=" and c.PRODUCTION_DATE >= '".$insert_date."'";
	
		$sql="SELECT b.style_ref_no, d.GMTS_ITEM_ID,c.PRODUCTION_DATE from wo_po_break_down a, wo_po_details_master b, PRO_GARMENTS_PRODUCTION_MST c,WO_PO_DETAILS_MAS_SET_DETAILS d where d.job_no=b.job_no and a.job_no_mst=b.job_no and a.id=c.PO_BREAK_DOWN_ID and c.PRODUCTION_TYPE =5 and b.STYLE_REF_NO='$style_ref' and d.GMTS_ITEM_ID='$gmt_item' $date_cond";
		//echo $sql;die;
		$sql_result=sql_select($sql);
	}
	
	if(count($sql_result) && $flag==1 ){
		foreach ($sql_result as $row)
		{
			echo "$('#update_parmission').val(0);\n";
			echo "$('#worningMessage').text('Update restricted, This Information is used in another Table');\n";
		}
	}
	else{
		echo "$('#update_parmission').val(1);\n";
		echo "$('#worningMessage').text('');\n";
	}
	
	exit();
}

if($action=='generate_operation_sticker'){
	extract($_REQUEST);
	require('../../../ext_resource/mpdf60/mpdf.php');
	include "../../../ext_resource/phpqrcode/qrlib.php"; 
	

    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
    $PNG_WEB_DIR = 'temp/';

	$errorCorrectionLevel = 'M';//'L','M','Q','H'
    $matrixPointSize = 10;
    if (!file_exists($PNG_TEMP_DIR)){mkdir($PNG_TEMP_DIR);}
        
 
	
	$body_part_arr=return_library_array( "select id,BODY_PART_FULL_NAME from lib_body_part", "id","BODY_PART_FULL_NAME"  );
	$operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
	$resource_name_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID=$cbo_process_id order by RESOURCE_NAME","RESOURCE_ID","RESOURCE_NAME" );

	//print_r($resource_name_arr);

	$sql ="select a.id as SYS_ID,A.SYSTEM_NO,A.BUYER_ID, A.STYLE_REF, A.CUSTOM_STYLE, A.FABRIC_TYPE,A.BULLETIN_TYPE,A.PROD_DESCRIPTION, A.GMTS_ITEM_ID,B.ROW_SEQUENCE_NO,b.LIB_SEWING_ID as OPERATION_ID,b.ID,b.OPERATOR_SMV,b.BODY_PART_ID,c.OPERATION_NAME,b.RESOURCE_GSD from ppl_gsd_entry_mst a,ppl_gsd_entry_dtls b,LIB_SEWING_OPERATION_ENTRY c where a.id=b.MST_ID and b.LIB_SEWING_ID=c.id and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and a.id=$update_id and b.id in($operationidstr) order by B.ROW_SEQUENCE_NO";
	//echo $sql;die;
	$sql_res=sql_select($sql);


	
	ob_start();
	foreach ($sql_res as $row){
		//$QR_DATA_STR = $row['SYS_ID'].'__SEP__'.$row['ID'].'__SEP__'.$row['OPERATION_ID'].'__SEP__'.$row['OPERATION_NAME'];
		$QR_DATA_STR = str_replace([' ',':',',',"/"],'',$row['SYS_ID'].'__SEP__'.$row['ID'].'__SEP__'.$row['OPERATION_ID'].'__SEP__'.$row['OPERATION_NAME']);

		$filename = $PNG_TEMP_DIR.$QR_DATA_STR.'.png';
		QRcode::png($QR_DATA_STR, $filename, $errorCorrectionLevel, $matrixPointSize, 2);   
	?>
		<table cellspacing="0" cellpadding="0">
			
			<tr>
				<td style="font-size:9px ;padding-left:7px; font-weight:bold;">
					B#<?=$buyer_name_arr[$row['BUYER_ID']];?>,
					B.ID#<?=$row['SYSTEM_NO'];?><br>
					Style#<?=$row['STYLE_REF'];?><br>
					Item#<?=$garments_item[$row['GMTS_ITEM_ID']];?><br>
					O.ID#<?=$row['OPERATION_ID'];?>,
					Seq#<?=$row['ROW_SEQUENCE_NO'];?>,
					Body Part#<?=$body_part_arr[$row['BODY_PART_ID']];?><br>
					OP#<?=$row['OPERATION_NAME'];?><br>
					Resource#<?=$resource_name_arr[$row['RESOURCE_GSD']];?>, 
					SMV#<?=$row['OPERATOR_SMV'];?>,
				</td>
			</tr>
			<tr>
				<td><img src="<?=$PNG_TEMP_DIR.basename($filename);?>" width="80" /></td>
				
			</tr>
		</table>
	<?
	}
	$html = ob_get_contents();
	ob_end_clean();
 

	foreach (glob("temp/"."*.pdf") as $filename) {			
		@unlink($filename);
	}

	
	$mpdf = new mPDF('utf-8', array(76.2,50.8));
	$mpdf->AddPageByArray([
		'margin-left' =>5,
		'margin-right' => 0,
		'margin-top' => 5,
		'margin-bottom' => 0,
	]);



	$mpdf->WriteHTML($html,2);
	$REAL_FILE_NAME = 'operation_'.$update_id .'_'. date('j-M-Y_h-iA') . '.pdf';
	$mpdf->Output("temp/".$REAL_FILE_NAME, 'F');
  
	echo 'requires/temp'.DIRECTORY_SEPARATOR.$REAL_FILE_NAME; 
	exit();
	//echo "http://demo.iftiit.com/pdf/sale_invoice_22.pdf";

}

if($action=="breakdown_print")
{
	$data=explode("**",$data);
	$update_id=$data[0];
	$report_title=$data[1];
	//echo $data[0];
	$approved=array(0=>"No",1=>"Yes",2=>"No",3=>"Yes");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_library=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	
	$mstDataArray=sql_select("SELECT a.id, a.system_no_prefix, a.extention_no,a.prod_description,a.INTERNAL_REF,a.bulletin_type, a.is_copied, a.buyer_id, a.style_ref, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv, a.product_dept, a.inserted_by, a.insert_date, a.updated_by,a.applicable_period, max(b.row_sequence_no) as seq_no, a.custom_style, a.remarks, a.fabric_type, a.color_type, a.approved,a.internal_ref,a.complexity_level,a.process_id from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b where a.id=b.mst_id and a.id='".$update_id."' and b.status_active=1 and b.is_deleted=0 GROUP by a.id, a.system_no_prefix, a.extention_no,a.prod_description,a.INTERNAL_REF,a.bulletin_type, a.is_copied, a.buyer_id, a.style_ref, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv, a.product_dept, a.inserted_by, a.insert_date, a.updated_by, a.custom_style,a.applicable_period, a.remarks, a.fabric_type, a.color_type, a.approved,a.internal_ref,a.complexity_level,a.process_id");

	// print_r($mstDataArray);

	$gsd_entry_dtls_sql = "SELECT a.id FROM ppl_gsd_entry_dtls a WHERE mst_id = '$update_id' and a.operator_smv IS NOT NULL";
	$gsd_entry_dtls_res = sql_select($gsd_entry_dtls_sql);
	$no_of_machine = count($gsd_entry_dtls_res); 

	$balancing_mst_entry_sql = "SELECT a.id, a.target FROM ppl_balancing_mst_entry a WHERE gsd_mst_id = '$update_id'";
	$balancing_mst_entry_res = sql_select($balancing_mst_entry_sql);
	$target = $balancing_mst_entry_res['0']['TARGET'];
	       
	$tot_mc_smv = $mstDataArray[0]['TOT_MC_SMV'];
	$working_hour = $mstDataArray[0]['WORKING_HOUR'];
	
 
	// calculation of (Target*Total MC SMV)/(No of Machine*Working Hour*60)
	// $target_eff = ($target*$tot_mc_smv)/($no_of_machine*$working_hour*60);
	
	// ppl_gsd_entry_mst = TOT_MC_SMV
    // ppl_gsd_entry_dtls = operator_smv
	// ppl_balancing_mst_entry_== target     

    $production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$mstDataArray[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME");
	// print_r($mstDataArray);
	?>
	<script src="../../../Chart.js-master/Chart.js"></script>
    <div style="width:990px">
        <table width="870" border="0">
            <tr>
                <td align="center" colspan="9"><strong><u>Operation Balancing Sheet</u></strong></td>
            </tr>
			<tr>
                <td width="130"><strong>System ID</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="130"><? echo $mstDataArray[0][csf('system_no_prefix')]; ?></td>
                <td width="130"><strong>Extention No.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="120"><? echo $mstDataArray[0][csf('extention_no')]; ?></td>
                <td width="130"><strong>Copy</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $bulletin_copy_arr[$mstDataArray[0][csf('is_copied')]]; ?></td>
            </tr>
            <tr>
                <td width="130"><strong>Style Ref.</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="130"><? echo $mstDataArray[0][csf('style_ref')]; ?></td>
				<td width="130"><strong>Internal Ref</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="130"><? echo $mstDataArray[0][csf('internal_ref')]; ?></td>
				<td width="130"><strong>Custom Style</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="130"><? echo $mstDataArray[0][csf('custom_style')]; ?></td>           
            </tr>
			<tr>
				<td width="130"><strong>Buyer Name</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="120"><? echo $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></td>
                <td width="130"><strong>Process</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="120"><? echo $general_issue_purpose[$mstDataArray[0][csf('process_id')]]; ?></td>
				<td width="130"><strong>Garments Item</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Working Hour</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('working_hour')]; ?></td>
                <td><strong>Action</strong></td>
                <td width="10"><strong>:</strong></td>
                <td style="padding-right:5px"><? echo $mstDataArray[0][csf('allocated_mp')]; ?></td>
                <td><strong>Prod. Dept</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $product_dept[$mstDataArray[0][csf('product_dept')]]; ?></td>
            </tr>
			<tr>
				<td width="130"><strong>Fabric Type</strong></td>
                <td width="10"><strong>:</strong></td>
                <td width="120"><? echo $mstDataArray[0][csf('fabric_type')]; ?></td>
				<td><strong>Bulletin Type</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $bulletin_type_arr[$mstDataArray[0][csf('bulletin_type')]]; ?></td>
				<td width="130"><strong>Color Type</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $color_type[$mstDataArray[0][csf('color_type')]]; ?></td>
            </tr>
            <tr>
                
                <td><strong>Applicable Period</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo change_date_format($mstDataArray[0][csf('applicable_period')]); ?></td>
				<td><strong>Product Description</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('prod_description')]; ?></td>
				<td><strong>Approved</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $approved[$mstDataArray[0][csf('approved')]]; ?><td>                                               
            </tr>
			<tr>
                <td><strong>Remarks</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $mstDataArray[0][csf('remarks')]; ?></td>
				<td><strong>Complexity Level</strong></td>
                <td width="10"><strong>:</strong></td>
                <td><? echo $complexity_level[$mstDataArray[0][csf('complexity_level')]]; ?></td>
            </tr>
			
        </table>
        <br />
        <table width="100%" align="right" cellspacing="0"  border="1" rules="all">
            <thead bgcolor="#dddddd" align="center">
                <th>Seq. No</th>
				<th>Body Part</th>
                <th>Operation</th>
                <th>Resource</th>
				<th>Attach</th>
                <th>Machine SMV</th>
				<th>Manual SMV</th>
				<th>Efficiency</th>
                <th>Target (100%)</th>
				<th>Target (eff.)</th>
            </thead>
            <?
                $balanceDataArray=array();
                if($update_id>0)
                {
                    $blData=sql_select("select gsd_dtls_id,smv,target_hundred_perc,cycle_time,theoritical_mp,layout_mp,work_load,weight,worker_tracking from ppl_balancing_dtls_entry where mst_id=$update_id and is_deleted=0");
                    foreach($blData as $row)
                    {
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['smv']=$row[csf('smv')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['perc']=number_format($row[csf('target_hundred_perc')],0,'.','');
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['cycle_time']=$row[csf('cycle_time')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['theoritical_mp']=$row[csf('theoritical_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['layout_mp']=$row[csf('layout_mp')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['work_load']=$row[csf('work_load')];
                        $balanceDataArray[$row[csf('gsd_dtls_id')]]['weight']=$row[csf('weight')];
						$balanceDataArray[$row[csf('gsd_dtls_id')]]['worker_tracking']=$row[csf('worker_tracking')];
                    }
                }
                
                $operation_arr=return_library_array( "select id,operation_name from lib_sewing_operation_entry", "id","operation_name"  );

				  $sqlDtls="SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv,helper_smv, target_on_full_perc, target_on_effi_perc from ppl_gsd_entry_dtls where mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 order by row_sequence_no asc";//body_part_id, 
                $data_array_dtls=sql_select($sqlDtls);
				
                $tot_smv=0; $tot_th_mp=0; $tot_mp=0; $helperSmv=0; $machineSmv=0; $sQISmv=0; $fIMSmv=0; $fQISmv=0; $polyHelperSmv=0; $pkSmv=0; $htSmv=0; $tot_Machine_SMV=0; $tot_Mannual_SMV=0;
                $helperMp=0; $machineMp=0; $sQiMp=0; $fImMp=0; $fQiMp=0; $polyHelperMp=0; $pkMp=0; $htMp=0; $mpSumm=array();
                
                $seqNosArr=array(); $weightsArr=array(); $pitchTimesArr=array(); $uclsArr=array(); $lclsArr=array(); $bodyPartArr=array();
                 
                foreach($data_array_dtls as $slectResult)
                {
                    /* if($balanceDataArray[$slectResult[csf('id')]]['smv']>0)	
                    {
                        $smv=$balanceDataArray[$slectResult[csf('id')]]['smv'];
                        $cycleTime=$balanceDataArray[$slectResult[csf('id')]]['cycle_time'];
                        $perc=$balanceDataArray[$slectResult[csf('id')]]['perc'];
                    }
                    else
                    {
                        $smv=$slectResult[csf('total_smv')];
                        $cycleTime=$slectResult[csf('total_smv')]*60;
                        $perc=$slectResult[csf('target_on_full_perc')];
                    } */
                    $smv=$slectResult[csf('total_smv')];
					$manuel=$slectResult[csf('helper_smv')];
                    $cycleTime=$slectResult[csf('total_smv')]*60;
                    $perc=$slectResult[csf('target_on_full_perc')];
					$efficiency=$slectResult[csf('efficiency')];
					
                    
                    $rescId=$slectResult[csf('resource_gsd')];
                    $layOut=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                     
                    if($rescId==40 || $rescId==41 || $rescId==43 || $rescId==44 || $rescId==48 || $rescId==68 || $rescId==69 || $rescId==70 || $rescId==147)
                    {
                        $helperSmv=$helperSmv+$smv;
                        $helperMp=$helperMp+$layOut;
                    }
                    else if($rescId==53)
                    {
                        $fIMSmv=$fIMSmv+$smv;
                        $fImMp=$fImMp+$layOut;
                    }
                    else if($rescId==54)
                    {
                        $fQISmv=$fQISmv+$smv;
                        $fQiMp=$fQiMp+$layOut;
                    }
                    else if($rescId==55)
                    {
                        $polyHelperSmv=$polyHelperSmv+$smv;
                        $polyHelperMp=$polyHelperMp+$layOut;
                    }
					else if($rescId==56)
                    {
                        $pkSmv=$pkSmv+$smv;
                        $pkMp=$pkMp+$layOut;
                    }
					else if($rescId==90)
                    {
                        $htSmv=$htSmv+$smv;
                        $htMp=$htMp+$layOut;
                    }
					else if($rescId==176)
                    {
                        $imSmv=$imSmv+$smv;
                        $imMp=$imMp+$layOut;
                    }
                    else
                    {
                        $machineSmv=$machineSmv+$smv;
                        $machineMp=$machineMp+$layOut;
                        
                        $mpSumm[$rescId]+= $layOut;
                    }
                    
                    $ucl=number_format(($mstDataArray[0][csf('pitch_time')]/0.85),2,'.','');
                    $lcl=number_format((($mstDataArray[0][csf('pitch_time')]*2)-$ucl),2,'.','');
                    $weight=fn_number_format(($smv*1)/($layOut*1),2);
                    $seqNosArr[]=$slectResult[csf('row_sequence_no')];
                    //$weightsArr[]=number_format($balanceDataArray[$slectResult[csf('id')]]['weight'],2,'.','');
                    $weightsArr[]=number_format($weight,2,'.','');
                    $pitchTimesArr[]=$mstDataArray[0][csf('pitch_time')];
                    $uclsArr[]=$ucl;
                    $lclsArr[]=$lcl;
					
					$tot_th_mp+=$balanceDataArray[$slectResult[csf('id')]]['theoritical_mp'];
					
					/*if(!in_array($slectResult[csf('body_part_id')],$bodyPartArr))
					{
						echo '<tr><td colspan="13"><b>'.$body_part[$slectResult[csf('body_part_id')]].'</b></td></tr>';
						$bodyPartArr[]=$slectResult[csf('body_part_id')];
					}*/
                ?>
                    <tr>
                        <td align="center"><? echo $slectResult[csf('row_sequence_no')]; ?></td>
						<td align="center"><? echo $body_part_type[$slectResult[csf('row_sequence_no')]]; ?></td>
                        <td><? echo $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?></td>
                        <td align="center"><? echo $production_resource_arr[$slectResult[csf('resource_gsd')]]; ?></td>
						<td align="center"><? echo $attach_id[$attachment]; ?></td>
                        <td align="right"><? $tot_Machine_SMV=$tot_Machine_SMV+$smv;
											 echo number_format($smv,2,'.',''); ?></td>
						<td align="right"><? $tot_Mannual_SMV=$tot_Mannual_SMV+$manuel;
											 echo number_format($manuel,2,'.',''); ?></td>
                        <td align="center"><? echo $efficiency; ?></td>                  
						<td align="center"><? echo $perc; ?></td>  
						<td align="center"><? echo number_format($slectResult[csf('target_on_effi_perc')],2,'.',''); ?></td>                 
                    </tr>
                <?	
                    $tot_smv+=$smv;
                    $tot_mp+=$balanceDataArray[$slectResult[csf('id')]]['layout_mp'];
                    $i++;
                }
                
                $seqNos= json_encode($seqNosArr);
                $weights= json_encode($weightsArr); 
                $pitchTimes= json_encode($pitchTimesArr); 
                $ucls= json_encode($uclsArr); 
                $lcls= json_encode($lclsArr);
				
				if(strpos($tot_mp,".")!="")
				{
					$tot_mp=number_format($tot_mp,2,'.','');
				}
			?>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align="center">Total: </td>
						<td align="right"><? echo number_format($tot_Machine_SMV,2); ?></td>
						<td align="right"><? echo number_format($tot_Mannual_SMV,2); ?></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
		</table>
        <br />
		 


        
       
        
    </div>
	<?
	exit();
}
// Al-Hasan
if($action=="breakdown_print2")
{
	$data = explode("**",$data);
	$update_id    = $data[0];
	$report_title = $data[1];
	//echo $data[0];
	$approved = array(0=>"No",1=>"Yes",2=>"No",3=>"Yes");
	$buyer_library = return_library_array("SELECT id, buyer_name from lib_buyer", "id", "buyer_name");
	$user_library = return_library_array("SELECT id, user_name from user_passwd", "id", "user_name");
	$color_library = return_library_array("SELECT id,color_name from lib_color where status_active=1 AND is_deleted=0", "id", "color_name");
	$operation_arr = return_library_array("SELECT id,operation_name FROM lib_sewing_operation_entry", "id","operation_name");
	$spiArr = array(1=>"6/7", 2=>"7/8", 3=>"8/9", 4=>"9/10", 5=>"10/11", 6=>"11/12", 7=>"12/13", 8=>"13/14", 9=>"14/15", 10=>"15/16", 11=>"16/17");
	$needle_sizeArr = array(1=>"6", 2=>"7", 3=>"8", 4=>"9", 5=>"10", 6=>"11");
	$risk_factor_sizeArr = array(1=>"Critical", 2=>"Semin Critical", 3=>"Normal");
	
	$mstDataArray = sql_select("SELECT a.id, a.system_no_prefix, a.extention_no,a.prod_description,a.INTERNAL_REF,a.bulletin_type, a.is_copied, a.buyer_id, a.style_ref, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv, a.product_dept, a.inserted_by, a.updated_by,a.applicable_period, max(b.row_sequence_no) as seq_no, a.custom_style, a.remarks, a.fabric_type, a.color_type, a.approved,a.internal_ref,a.complexity_level,a.process_id FROM ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b WHERE a.id=b.mst_id AND a.id='".$update_id."' and b.status_active=1 and b.is_deleted=0 group by a.id, a.system_no_prefix, a.extention_no,a.prod_description,a.INTERNAL_REF,a.bulletin_type, a.is_copied, a.buyer_id, a.style_ref, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv, a.product_dept, a.inserted_by, a.updated_by, a.custom_style,a.applicable_period, a.remarks, a.fabric_type, a.color_type, a.approved,a.internal_ref,a.complexity_level,a.process_id");
	 
	//print_r($mstDataArray);die; ppl_gsd_entry_mst system_no_prefix 

    $production_resource_arr = return_library_array("SELECT RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID = {$mstDataArray[0]['PROCESS_ID']} order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME");
	?>
	<script src="../../../Chart.js-master/Chart.js"></script>
    <div style="width:990px">
	    <table width="900" border="0">
            <tr>
                <td align="center" colspan="12"><strong><u>RISK ANALYSIS REPORT</u></strong></td>
            </tr>
        </table>
        <table width="400" border="0">
			<tr>
                <td><strong>Buyer Name: <? echo $buyer_library[$mstDataArray[0][csf('buyer_id')]]; ?></strong></td>
            </tr>
            <tr>
                <td><strong>Style: <? echo $mstDataArray[0][csf('style_ref')]; ?></strong></td>
            </tr>
			<tr>
				<td><strong>Item: <? echo $garments_item[$mstDataArray[0][csf('gmts_item_id')]]; ?></strong></td>
            </tr>
            <tr>
                <td><strong><? echo change_date_format($mstDataArray[0][csf('applicable_period')]); ?></strong></td>
            </tr> 
        </table>
		<?php
		function critical_count($mst_id, $data){
			$sqlDtls = "SELECT id, mst_id risk_factor FROM ppl_gsd_entry_dtls WHERE mst_id=$mst_id and risk_factor=$data and is_deleted=0 order by row_sequence_no asc";
			$data_array_dtls = sql_select($sqlDtls);
			return count($data_array_dtls);
		}
		?>
		<table width="200" border="0" style="float: right;margin-top: -93px;">
			<tr>
                <td><strong>CRITICAL:</strong></td>
                <td><strong><?= critical_count($mstDataArray[0][csf('id')], 1);?></strong></td>
            </tr>
            <tr>
                <td><strong>SEMI CRITICAL:</strong></td>
                <td><strong><?= critical_count($mstDataArray[0][csf('id')], 2);?></strong></td>
            </tr>
			<tr>
				<td><strong>NORMAL:</strong></td>
				<td><strong><?= critical_count($mstDataArray[0][csf('id')], 3);?></strong></td>
            </tr>
			<tr>
				<td><strong>SYSTEM ID:</strong></td>
				<td><strong><?= $mstDataArray[0][csf('system_no_prefix')];?></strong></td>
            </tr>
        </table> 
        <br />
        <table width="100%" align="right" cellspacing="0"  border="1" rules="all">
            <thead bgcolor="#dddddd" align="center">
                <th>SL</th>
				<th>PROCESS NAME</th>
                <th>SPI</th>
                <th>STITCH DETAILS</th>
				<th>NEEDLE SIZE</th>
                <th>CRITICAL</th>
				<th>SEMI CRITICAL</th>
				<th>NORMAL</th>
				<th>REMARKS</th>
            </thead>
               <?php
				$sqlDtls = "SELECT id, mst_id, row_sequence_no, body_part_id, lib_sewing_id, resource_gsd, attachment_id, efficiency, total_smv,helper_smv, target_on_full_perc, target_on_effi_perc, spi, needle_size, risk_factor, remarks FROM ppl_gsd_entry_dtls WHERE mst_id='".$mstDataArray[0][csf('id')]."' and is_deleted=0 order by row_sequence_no asc";
				// echo $sqlDtls;die;
                $data_array_dtls = sql_select($sqlDtls);
                foreach($data_array_dtls as $slectResult)
                {
                ?>
                    <tr>
                        <td align="center"><? echo $slectResult[csf('row_sequence_no')]; ?></td>
						<td><? echo $operation_arr[$slectResult[csf('lib_sewing_id')]]; ?></td>
                        <td><? echo $spiArr[$slectResult[csf('spi')]]; ?></td>
                        <td><? echo $production_resource_arr[$slectResult[csf('resource_gsd')]]; ?></td>
						<td align="center"><? echo $needle_sizeArr[$slectResult[csf('needle_size')]]; ?></td>
                        <td align="right"><input type="checkbox" <?php if($slectResult[csf('risk_factor')]==1 ){ echo 'checked';}?>></td>
						<td align="right"><input type="checkbox" <?php if($slectResult[csf('risk_factor')]==2 ){ echo 'checked';}?>></td>
                        <td align="center"><input type="checkbox" <?php if($slectResult[csf('risk_factor')]==3 ){ echo 'checked';}?>></td>
                        <td align="center"><?= $slectResult[csf('remarks')];?></td>
                    </tr>
                <?
                } 
			?>
		</table>
        <br/>
		<table id="signatureTblId" width="1000" style="padding-top:70px;">
			<tr>
				<td style="text-align: center; font-size:18px; border-top:1px solid;width: 65px;"><strong>MERCHANDISING DEPT</strong></td>
				<td width="30"></td>
				<td style="text-align: center; font-size:18px; border-top:1px solid;width: 90px;"><strong>PRODUCTION DEPT</strong></td>
				<td width="30"></td>
				<td style="text-align: center; font-size:18px; border-top:1px solid;width: 100px;"><strong>TECHNICAL DEPT</strong></td>
				<td width="30"></td>
				<td style="text-align: center; font-size:18px; border-top:1px solid;width: 100px;"><strong>SAMPLE DEPT</strong></td>
			</tr>
		</table>
    </div>
	<?
	exit();
}

if ($action=="job_no_popup")
{

	echo load_html_head_contents("Popup Info", "../../../", 1, 1,'',1,'');
	extract($_REQUEST);
    ?>
	<script>
		function js_set_value(str)
		{
			$('#hidden_data_str').val(str);
			parent.emailwindow.hide();
		}
    </script>

	<input type="hidden" name="hidden_data_str" id="hidden_data_str" value=""> 


 </head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="system_1" id="system_1" autocomplete="off">
				<table width="" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>                	 
							<th>Company</th>
							<th>Buyer Name</th>
							<th>Garments Item</th>
							<th>Style Ref.</th>
							<th>Internal Ref</th>
							<th>Job No</th>
							<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
						</tr>           
					</thead>
					<tr>
						<td>
							<?
								echo create_drop_down( "cbo_company_id", 120, "select id,company_name from lib_company","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'ws_gsd_controller', this.value, 'load_drop_down_buyer', 'buyer_td');" );  
							?> 
						</td>
						<td id="buyer_td">
							<?
								echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select --", $cbo_buyer, "" );  
							?> 
						</td>
						<td>
							<? echo create_drop_down( "cbo_gmt_item", 120, $garments_item,'', 1, "-Select Gmt. Item-","","","","" ); ?>
						</td>
						<td>
							<input type="text" style="width:130px" class="text_boxes"  name="txt_style_ref" id="txt_style_ref" placeholder="Write" value="<?=$txt_style_ref;?>" />	
						</td>
						<td>
							<input type="text" style="width:90px" class="text_boxes"  name="txt_internal_ref" id="txt_internal_ref" placeholder="Write" value="<?=$txt_internal_ref;?>" />	
						</td>
						<td>
							<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:128px" placeholder="Write" />
						</td> 

						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_gmt_item').value+'**'+document.getElementById('txt_style_ref').value+'**'+document.getElementById('txt_job_no').value+'**'+document.getElementById('txt_internal_ref').value, 'job_no_list_view', 'list_view_container', 'ws_gsd_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:80px;" />
						</td>
					</tr>
				</table>
				<div id="list_view_container" style="margin-top:5px"></div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
<?
exit();
}
 

if ($action=="job_no_list_view")
{
	echo load_html_head_contents("Job No", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$data = str_replace("'","",$data);
	list($cbo_company_id,$cbo_buyer_name,$cbo_gmt_item,$txt_style_ref,$txt_job_no,$txt_internal_ref)=explode('**',$data);

	if($cbo_buyer_name != 0){$where_con .= "and a.BUYER_NAME=$cbo_buyer_name";}
	if($cbo_company_id != 0){$where_con .= "and a.COMPANY_NAME=$cbo_company_id";}
	//if($cbo_gmt_item != 0){$where_con = "and COMPANY_NAME=$cbo_gmt_item";}
	if($txt_style_ref != ""){$where_con .= "and a.STYLE_REF_NO like('%$txt_style_ref')";}
	if($txt_job_no != ""){$where_con .= "and a.JOB_NO like('%$txt_job_no')";}
	if($txt_internal_ref != ""){$where_con .= "and C.GROUPING like('%$txt_internal_ref')";}
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
    ?>
	</head>
    <body>
	
		<fieldset style="width:720px;margin-left:10px">
			<?
			$job_sql = "select a.ID,a.BUYER_NAME,a.JOB_NO,a.JOB_NO_PREFIX_NUM,a.STYLE_REF_NO,a.JOB_QUANTITY,b.GMTS_ITEM_ID,C.GROUPING from WO_PO_DETAILS_MAS_SET_DETAILS b,WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN c where a.job_no=c.job_no_mst and  c.status_active=1 and c.is_deleted=0 and a.id=b.job_id and  a.status_active=1 and a.is_deleted=0 $where_con";
			// echo $job_sql;
			$job_data_arr=sql_select($job_sql);
            ?>
			<div style="margin-left:10px; margin-top:10px">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680" align="left">
                    <thead>
                        <th width="35">SL</th>
                        <th width="130">Buyer</th>
						<th>Style</th>
                        <th width="80">Job No</th>
                        <th width="80">Job Qty</th>
                        <th width="120">Item Name</th>
                    </thead>
                </table>
                <div style="width:700px; max-height:260px; overflow-y:scroll; float:left;" id="list_container"> 
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680" id="tbl_list_search" align="left">  
                        <?
                        $i=1; 
                        foreach($job_data_arr as $row)
                        {  
                            $bgcolor=($i%2==0) ? "#E9F3FF" : "#FFFFFF";
                         ?>
                            <tr bgcolor="<?=$bgcolor; ?>" onClick="js_set_value('<?=$row['ID'].'**'.$row['JOB_NO'].'**'.$row['GMTS_ITEM_ID'].'**'.$row['STYLE_REF_NO'].'**'.$row['BUYER_NAME'].'**'.$row['GROUPING']; ?>')" style="cursor:pointer" >
                                <td align="center" width="35"><?=$i; ?></td>
								<td width="130"><?=$buyer_arr[$row['BUYER_NAME']]; ?></td>
                                <td><p><?=$row['STYLE_REF_NO']; ?></p></td>
                                <td width="80" align="center"><?=$row['JOB_NO']; ?></td>
                                <td width="80" align="right"><?=$row['JOB_QUANTITY']; ?></td>
                                <td width="120"><?=$garments_item[$row['GMTS_ITEM_ID']]; ?></td>
                            </tr>
                        <? 
                        $i++; 
                        } 
                        ?>
                    </table>
                </div> 
            </div>
		</fieldset>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
	</script>
	</html>
    <?
    exit();
}


// if($action =  "load_drop_down_buyer"){
// 	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
// 	exit();
// }



if ($action=="sampleReq_popup")
{

	echo load_html_head_contents("Popup Info", "../../../", 1, 1,'',1,'');
	extract($_REQUEST);
    ?>
	<script>
		function js_set_value(str)
		{
			//alert(2);
			$('#sample_hidden_data_str').val(str);
			parent.emailwindow.hide();
		}
    </script> 
	<input type="hidden" name="sample_hidden_data_str" id="sample_hidden_data_str" value=""> 


 </head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="system_1" id="system_1" autocomplete="off">
				<table width="" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<tr>                	 
							<th>Company</th>
							<th>Buyer Name</th>
							<th>Sample Style</th>
							<th>Sample Req No.</th>
							<th>Year</th>
							<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
						</tr>           
					</thead>
					<tr>
						<td>
							<?
								echo create_drop_down( "cbo_company_id", 120, "select id,company_name from lib_company","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'ws_gsd_controller', this.value, 'load_drop_down_buyer', 'buyer_td');" );  
							?> 
						</td>
						<td id="buyer_td">
							<?
								echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select --", $cbo_buyer, "" );  
							?> 
						</td>
						<td>
						    <input type="text" style="width:130px" class="text_boxes"  name="txt_style_ref" id="txt_style_ref" placeholder="Write"/>	
						</td>
						<td>
							<input type="text" style="width:130px" class="text_boxes"  name="txt_req_no" id="txt_req_no" placeholder="Write"/>	
						</td>
						<td>
                            <? echo create_drop_down( "cbo_year", 50, create_year_array(),"", 1,"-- All --",0 , "",0,"" );//date("Y",time()) ?>	
                        </td>
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_style_ref').value+'**'+document.getElementById('txt_req_no').value+'**'+document.getElementById('cbo_year').value, 'sample_req_list_view', 'sample_list_view_container', 'ws_gsd_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:80px;" />
						</td>
					</tr>
				</table>
				<div id="sample_list_view_container" style="margin-top:5px"></div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
    exit();
}

if ($action=="sample_req_list_view")
{ 
	echo load_html_head_contents("Job No", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	 
	$data = str_replace("'", "", $data);
	list($cbo_company_id, $cbo_buyer_name,$txt_style_ref,$txt_req_no,$cbo_year)=explode('**',$data);
	$where_con = '';
	if($cbo_company_id != 0){$where_con .= " and a.COMPANY_ID=$cbo_company_id";}
	if($cbo_buyer_name != 0){$where_con .= " and a.BUYER_NAME=$cbo_buyer_name";}
	if($txt_style_ref != ""){$where_con .= " and a.STYLE_REF_NO like('%$txt_style_ref')";}
	if($txt_req_no != ""){$where_con .= " and a.REQUISITION_NUMBER like('%$txt_req_no')";}
	if($cbo_year != 0){$where_con .= " and a.SEASON_YEAR in ($cbo_year)";}
	//echo $where_con;die;
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
    ?>

    </head>
    <body>
		<fieldset style="width:600px;">
			<?
			$sql = "SELECT a.ID, a.COMPANY_ID, a.BUYER_NAME, a.STYLE_REF_NO, a.REQUISITION_NUMBER, a.SEASON_YEAR FROM sample_development_mst a WHERE a.status_active=1 and a.is_deleted=0 $where_con";
			// echo $sql;die;
			$sql_res_arr = sql_select($sql);
            ?>
			<div style="margin-left:1px; margin-top:10px">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="600" align="left">
                    <thead>
                        <th width="35">SL</th> 
						<th width="80">Buyer Name</th>
						<th width="80">Sample Style</th>
						<th width="80">Sample Req No.</th>
						<th width="80">Year</th>
                    </thead>
                </table>
                <div style="width:600px; max-height:260px; overflow-y:scroll; float:left;" id="list_container"> 
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="600" id="tbl_list_search" align="left">  
                        <?
                        $i=1; 
                        foreach($sql_res_arr as $row)
                        {  
                            $bgcolor=($i%2==0) ? "#E9F3FF" : "#FFFFFF";
                         ?>
                            <tr bgcolor="<?=$bgcolor; ?>" onClick="js_set_value('<?=$row['ID'].'**'.$row['REQUISITION_NUMBER'].'**'.$row['STYLE_REF_NO'].'**'.$row['BUYER_NAME']; ?>')" style="cursor:pointer" >
                                <td align="center" width="35"><?=$i; ?></td> 
								<td width="80"><?=$buyer_arr[$row['BUYER_NAME']]; ?></td>
                                <td width="80"><p><?=$row['STYLE_REF_NO']; ?></p></td>
                                <td width="80" align="center"><?=$row['REQUISITION_NUMBER']; ?></td>
                                <td width="80" align="center"><?=$row['SEASON_YEAR']; ?></td>
                            </tr>
                        <? 
                        $i++; 
                        } 
                        ?>
                    </table>
                </div> 
            </div>
		</fieldset>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
	</script>
	</html>
	<?
	exit();
}
















if($action=="pending_style_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);  
	//echo $buyer_id;
	
	if($type == 1){
		$sql = "select LIB_ITEM_ID as ITEM_ID, STYLE_REF as STYLE_REF from qc_mst where BUYER_ID = $buyer_id and is_deleted=0";	
	}
	else if($type == 2){
		$sql = "select GMTS_ITEM as ITEM_ID,STYLE_REFERNCE as STYLE_REF from wo_quotation_inquery where BUYER_ID = $buyer_id and is_deleted=0 ";
	}
	else{
		$sql = "select id, GMTS_ITEM_ID as ITEM_ID,STYLE_REF_NAME as STYLE_REF, OFFER_QNTY from lib_style_ref where BUYER_ID = $buyer_id and  status_active=1 and is_deleted=0";
	}

	$data_array=sql_select($sql);
	$style_ref_data_arr=array();
	foreach($data_array as $row){
		$style_ref_data_arr[$row['STYLE_REF']] = array('STYLE_REF' => $row['STYLE_REF'],'ITEM_ID' => $row['ITEM_ID'],'OFFER_QNTY' => $row['OFFER_QNTY']);
	}


	$gsd_sql = "select STYLE_REF from PPL_GSD_ENTRY_MST where  BUYER_ID = $buyer_id  AND IS_DELETED = 0 AND STATUS_ACTIVE = 1";
	$gsd_sql_res = sql_select($gsd_sql);
	foreach($gsd_sql_res as $row){
		unset($style_ref_data_arr[$row['STYLE_REF']]);
	}

     asort($style_ref_data_arr) ;                   

	?>
	<script>
		function set_style_ref(style_ref,item_id,offer_qnty){
			var type = '<?=$type;?>';
			if(type == 3){
				document.getElementById("hidden_selected_style_ref").value = style_ref;
				document.getElementById("hidden_selected_item_id").value = item_id;
				document.getElementById("hidden_selected_offer_qnty").value = offer_qnty;
				parent.emailwindow.hide();
			}
		}
	</script>
	</head>
	<body>
	<input type="hidden" id="hidden_selected_style_ref" value="">
	<input type="hidden" id="hidden_selected_item_id" value="">
	<input type="hidden" id="hidden_selected_offer_qnty" value="">
	<div align="center" style="width:100%;" >
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="98%">
			<thead>
				<tr>
					<th width="25">SL</th>
					<th>Style No</th>
					<th>Item</th>
					<th>Offer Qnty</th>
				</tr>
			</thead>
			<tbody id="tbl_list_search">
				<?php
				$i=1;
				foreach($style_ref_data_arr as $row){
					$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
				?>
				<tr bgcolor="<?=$bgcolor;?>" onclick="set_style_ref('<?=$row['STYLE_REF'];?>','<?=$row['ITEM_ID'];?>','<?= $row['OFFER_QNTY'];?>')" style="cursor:pointer;">
					<td><?= $i; ?></td>
					<td><?= $row['STYLE_REF']; ?></td>
					<td><?= $garments_item[$row['ITEM_ID']]; ?></td>
					<td><?= $row['OFFER_QNTY']; ?></td>
				</tr>
				<?php
				$i++;
				}
				?>
			</tbody>
		</table>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>setFilterGrid('tbl_list_search',-1);</script>
	</html>
	<?
	exit();
}


if($action = 'check_duplicate_entry'){

	$gsd_sql = "select ID, INSERT_DATE, STYLE_REF from PPL_GSD_ENTRY_MST where job_id = $data  AND IS_DELETED = 0 AND STATUS_ACTIVE = 1";
	$gsd_sql_res = sql_select($gsd_sql);
	foreach($gsd_sql_res as $row){
		$style_ref_data_arr[$row['ID']]= "SYSTEM ID: ".$row['ID'].", STYLE REF: ".$row['STYLE_REF'].", INSERT DATE: ".$row['INSERT_DATE'];
	}
	if(count($style_ref_data_arr)){echo implode("<br>",$style_ref_data_arr);}
	else{echo 0;}
    exit();
}

?>