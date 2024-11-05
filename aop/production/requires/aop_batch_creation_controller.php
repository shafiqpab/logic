<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
$buyer_arr=return_library_array( "SELECT id, short_name from lib_buyer where status_active =1 and is_deleted=0",'id','short_name');

if($db_type==0) $select_field="group"; 
else if($db_type==2) $select_field="wm";

/*if($action=="load_drop_down_item_desc")
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$row_no=$data[1];
	$item_id=$data[2];
	$process_id=$data[3];
	$fabric_from=$data[4];
	$job_no=$data[5];
	
	if($fabric_from==1)
	{
		if ($db_type==0)
		{
			$sql="select concat(b.material_description,',',b.gsm,',',b.grey_dia,',',b.fin_dia) as material_description from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and b.order_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.material_description, b.gsm, b.grey_dia, b.fin_dia";
		}
		elseif($db_type==2)
		{
			$sql="select b.material_description || ',' || b.gsm || ',' || b.grey_dia || ',' || b.fin_dia as material_description from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and b.order_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.material_description, b.gsm, b.grey_dia, b.fin_dia"; 
		}
		echo create_drop_down( "cboItemDesc_".$row_no, 150, $sql,'material_description,material_description', 1, "--Select Item Desc--",'0',"gsm_dia_load(".$row_no.")",'',$item_id);
		echo "<input type='hidden' name='txtItemDesc_".$row_no."' id='txtItemDesc_".$row_no."' class='text_boxes' style='width:60px' />";
	}
	else
	{
		if ($db_type==0)
		{
			$sql="select concat(fabric_description,',', gsm,',', dia_width) as fabric_description from subcon_production_dtls where job_no='$job_no' and status_active=1 group by fabric_description, gsm, dia_width";
		}
		else if ($db_type==2)
		{
			$sql="select fabric_description || ',' || gsm || ',' || dia_width as fabric_description from subcon_production_dtls where job_no='$job_no' and status_active=1 group by fabric_description, gsm, dia_width";
		}
			echo create_drop_down( "cboItemDesc_".$row_no, 150, $sql,'fabric_description, fabric_description', 1, "--Select Item Desc--",'0',"gsm_dia_load(".$row_no.")",'',$item_id);
		echo "<input type='hidden' name='txtItemDesc_".$row_no."' id='txtItemDesc_".$row_no."' class='text_boxes' style='width:60px' />";
	}
	exit();
}*/

/*if($action=="load_receive_production_data")
{
	$ex_data=explode('_',$data);
	$row=$ex_data[2];
	if($ex_data[0]==1)
	{
		$sql="select id, gsm, grey_dia as dia, fin_dia from sub_material_dtls where id=$ex_data[1] and status_active=1 and is_deleted=0";
	}
	else
	{
		$sql="select id, gsm, dia_width as dia, '' as fin_dia from subcon_production_dtls where id=$ex_data[1] and status_active=1 and is_deleted=0";
	}
	$sql_result = sql_select($sql);
 	foreach($sql_result as $result)
	{
		echo "$('#txtGsm_".$row."').val(".$result[csf("gsm")].");\n";
		echo "$('#txtDia_".$row."').val(".$result[csf("dia")].");\n";
		echo "$('#txtFinDia_".$row."').val(".$result[csf("fin_dia")].");\n";
	}
 	exit();
}*/

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "SELECT id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/aop_batch_creation_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/aop_batch_creation_controller', document.getElementById('cbo_company_id').value+'_'+this.value+'_'+0, 'load_drop_down_machine', 'machine_td' );" );	
}

if ($action=="load_drop_down_floor")
{
	$ex_data=explode('_',$data);
	echo create_drop_down( "cbo_floor_name", 172, "SELECT id,floor_name from lib_prod_floor where company_id=$ex_data[0] and location_id=$ex_data[1] and status_active =1 and is_deleted=0 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/aop_batch_creation_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value+'_'+this.value, 'load_drop_down_machine', 'machine_td' );" );
}

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	/*if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";*/
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 170, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", "", "","");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 170, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "",'0' );
	}	

	exit();	 
} 
if ($action=="load_drop_down_search")
{
	$data=explode("_",$data);
	
	if($data[0]==1)
	{	
		$search_by=array(1=>'Work Order',2=>'Buyer JOB NO.',3=>'Buyer PO',4=>'Buyer Style Ref.',5=>"AOP Job No",6=>"Internal Ref");
	}
	else
	{
		$search_by=array(1=>'Work Order',2=>'Buyer JOB NO.',3=>'Buyer PO',4=>'Buyer Style Ref.',5=>"AOP Job No");
	}	
	echo create_drop_down( "cbo_search_by", 110, $search_by,"", 0, "--Select--", 1, "change_caption(this.value);",0 );
	exit();	 
} 

if ($action=="load_drop_down_machine")
{
	$data= explode("_", $data);

	if($data[1]==0 || $data[2]==0)
	{
		echo create_drop_down( "txt_machine_no", 172, $blank_array,"", 1, "-- Select Machine --", $selected, "" );
	}
	else
	{
		if($db_type==2)
		{
			echo create_drop_down( "txt_machine_no", 172, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where company_id='$data[0]' and location_id='$data[1]' and floor_id='$data[2]' and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no","id,machine_name", 1,"-- Select Machine --", $selected, "","" );
		}
		else if($db_type==0)
		{
			echo create_drop_down( "txt_machine_no", 172, "SELECT id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where company_id='$data[0]' and location_id='$data[1]' and floor_id='$data[2]' and status_active=1 and is_deleted=0 and is_locked=0 order by  seq_no","id,machine_name", 1, "-- Select Machine --", $selected, "","" );
		}
	}	
}

if ($action=="load_drop_down_po_id")
{	
	//$data= explode("_", $data);and a.company_id='$data[0]'
	echo create_drop_down( "poId_1", 150,"SELECT b.id as po_id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst  and b.id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.order_no order by b.order_no ASC","po_id,po_number", 0, "-- Select PO --", $selected, "hidden_data_load(1);","","","", "", "", "", "","poId[]" );
}


if($action=="po_wise_data_load")
{
	$party_arr=return_library_array( "SELECT id, short_name from lib_buyer where status_active =1 and is_deleted=0",'id','short_name');

	$data= explode("_", $data);
	$row_no=$data[0];
	$company_id=$data[1];
	$job_no=$data[2];
	$po_id=$data[3];

	$order_con=" and b.id ='$po_id'";
	$job_con=" and a.subcon_job like '%$job_no%'";

	if($db_type==0)
	{
		$sql="SELECT a.subcon_job as job_no, a.job_no_prefix_num, YEAR(a.insert_date) as year, a.party_id, b.id as po_id, b.main_process_id, b.process_id, b.cust_style_ref as style_ref_no, b.order_uom, b.order_no as po_number, b.order_quantity as po_qnty_in_pcs, b.delivery_date as pub_shipment_date, group_concat(distinct(c.item_id)) as item_id from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_con $job_con group by b.id order by b.id DESC";// die;
	}
	else if($db_type==2)
	{		
		$sql="SELECT LISTAGG(CAST(a.subcon_job AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.subcon_job) as job_no, a.party_id, LISTAGG(CAST(a.job_no_prefix_num AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.job_no_prefix_num) as job_no_prefix_num, TO_CHAR(max(a.insert_date),'YYYY') as year, b.id as po_id, b.main_process_id, b.process_id, b.cust_style_ref as style_ref_no, b.order_uom, b.order_no as po_number, b.order_quantity as po_qnty_in_pcs, b.delivery_date as pub_shipment_date, 
		LISTAGG(CAST(c.item_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.item_id) as item_id from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_con $job_con group by a.party_id, b.id, b.main_process_id, b.process_id, b.cust_style_ref, b.order_uom, b.order_no, b.order_quantity, b.delivery_date order by b.id DESC";// die;
	}

	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row)
	{

		echo "document.getElementById('txtPoNo_".$row_no."').value 		= '".$row[csf("po_number")]."';\n";
		echo "document.getElementById('processId_".$row_no."').value 	= '".$row[csf("main_process_id")]."';\n";
		echo "document.getElementById('hide_job_no').value 				= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('hide_party_id').value 			= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('txtJobParty_".$row_no."').value  = '".$party_arr[$row[csf("party_id")]]."';\n";
	}

exit();	
}



if ($action=="load_variable_settings")
{
	echo "$('#variable_check').val(0);\n";
	$sql_result = sql_select("SELECT batch_no_creation from variable_settings_production where company_name=$data and variable_list=24 and status_active=1 and is_deleted=0");
 	foreach($sql_result as $result)
	{
		echo "$('#variable_check').val(".$result[csf("batch_no_creation")].");\n";
	}
 	exit();
}
if ($action=="load_fabric_source_from_variable_settings")
{
	$sql_result = sql_select("SELECT dyeing_fin_bill from variable_settings_subcon where company_id = $data and variable_list = 4 and is_deleted = 0 and status_active = 1");
	
	$fabricfrom=array(1=>"Receive",2=>"Production",3=>"Issue"); 
	if($sql_result)
	{
		$data_ids=explode(",", $sql_result[0][csf('dyeing_fin_bill')]);
		$values=$sql_result[0][csf('dyeing_fin_bill')];

		$selected = (count($data_ids)==1)? $data_ids[0] : "0";
	}
	else
	{
		$values=1;
		$selected =1;
	}

	//echo create_drop_down("cbofabricfrom_1", 70, $fabricfrom, "", 1, "--Select --", $selected, "", 0,$values,"","","","","","","fabric_source");
	echo create_drop_down("cbofabricfrom_1", 70, $fabricfrom, "", 1, "--Select --", $selected, "", 0, $values, "", "", "", "", "", "cbofabricfrom[]");

	/*if($sql_result)
	{
	    foreach($sql_result as $result)
		{
	        echo "$('.fabric_source').val(".$result[csf("dyeing_fin_bill")].");\n";
		}
    }
    else
    {
            echo "$('.fabric_source').val(1);\n";
    }*/
 	exit();
}

if($action=="itemdes_popup")
{
  	echo load_html_head_contents("Item Description Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value( prod_id,challan,description,gsm,grey_dia,fin_dia,balance)
		{
			document.getElementById('prod_id').value=prod_id;
			document.getElementById('challan').value=challan;
			document.getElementById('description').value=description;
			document.getElementById('gsm').value=gsm;
			document.getElementById('grey_dia').value=grey_dia;
			document.getElementById('fin_dia').value=fin_dia;
            document.getElementById('balance').value=balance;
			parent.emailwindow.hide();
		}
    </script>
</head>
<?
	$batch_array=array();
	$batch_sql="SELECT po_id, prod_id, sum(batch_qnty) as batch_qnty from pro_batch_create_dtls where po_id='$po_id' and status_active=1 and is_deleted=0 group by po_id, prod_id";
	$result_batch_sql=sql_select( $batch_sql );
	foreach($result_batch_sql as $row)
	{
		$batch_array[$row[csf('po_id')]][$row[csf('prod_id')]]=$row[csf('batch_qnty')];
	}
	
	$material_issue_arr=array();
	$material_return_arr=array();
	
	if ($db_type==0)
	{
		$sql_issue="SELECT b.rec_challan, concat(b.material_description,',',b.gsm,',',b.grey_dia,',',b.fin_dia) as description, b.color_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=2 and a.company_id=$cbo_company_id and b.order_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.rec_challan, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by b.rec_challan";
		$sql_return="SELECT b.rec_challan, concat(b.material_description,',',b.gsm,',',b.grey_dia,',',b.fin_dia) as description, b.color_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=3 and a.company_id=$cbo_company_id and b.order_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.rec_challan, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by b.rec_challan";
	}
	elseif($db_type==2)
	{
		$sql_issue="SELECT b.rec_challan, b.material_description || ',' || b.gsm || ',' || b.grey_dia || ',' || b.fin_dia as description, b.color_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=2 and a.company_id=$cbo_company_id and b.order_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.rec_challan, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by b.rec_challan"; 
		$sql_return="SELECT b.rec_challan, b.material_description || ',' || b.gsm || ',' || b.grey_dia || ',' || b.fin_dia as description, b.color_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=3 and a.company_id=$cbo_company_id and b.order_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.rec_challan, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by b.rec_challan"; 
	}
	
	$nameArray_issue=sql_select($sql_issue);
	foreach ($nameArray_issue as $row)
	{
		$material_issue_arr[$row[csf('rec_challan')]][$row[csf('description')]][$row[csf('color_id')]]=$row[csf('quantity')];
	}
	
	$nameArray_return=sql_select($sql_return);
	foreach($nameArray_return as $row)
	{
		$material_return_arr[$row[csf('rec_challan')]][$row[csf('description')]][$row[csf('color_id')]]=$row[csf('quantity')];
	}
	//var_dump($material_return_arr);
	
	if($fabricfrom==1)
	{
		if ($db_type==0)
		{
			$sql="SELECT a.chalan_no, b.material_description, b.id, b.gsm, b.grey_dia, b.fin_dia, b.color_id, sum(b.quantity) as quantity, concat(b.material_description,',',b.gsm,',',b.grey_dia,',',b.fin_dia) as description from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and a.company_id=$cbo_company_id and b.order_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 group by a.chalan_no, b.id, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by a.chalan_no";
		}
		elseif($db_type==2)
		{
			$sql="SELECT  a.chalan_no, b.material_description, b.id, b.gsm, b.grey_dia, b.fin_dia, b.color_id, sum(b.quantity) as quantity, b.material_description || ',' || b.gsm || ',' || b.grey_dia || ',' || b.fin_dia as description from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and a.company_id=$cbo_company_id and b.order_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 group by a.chalan_no, b.id, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id order by a.chalan_no"; 
		}
	}
	else if($fabricfrom==2)
	{
		if ($db_type==0)
		{
			$sql="SELECT '' as chalan_no, fabric_description as material_description, id, gsm, dia_width as fin_dia, color_id, sum(product_qnty) as quantity, concat(fabric_description,',', gsm,',', dia_width) as description from subcon_production_dtls where order_id='$po_id' and product_type=2 and status_active=1 group by id, fabric_description, gsm, dia_width, color_id";
		}
		else if ($db_type==2)
		{
			$sql="SELECT '' as chalan_no, fabric_description as material_description, id, gsm, dia_width as fin_dia, color_id, sum(product_qnty) as quantity, fabric_description || ',' || gsm || ',' || dia_width as description from subcon_production_dtls where order_id='$po_id' and product_type=2 and status_active=1 and is_deleted=0 group by id, fabric_description, gsm, dia_width, color_id";
		}
    }
    else 
    {
        
        if($db_type ==0)
            {
                $sql="SELECT  a.chalan_no, b.material_description, b.id, b.gsm, b.grey_dia, b.fin_dia, b.color_id, b.order_id,
                sum(b.quantity) as quantity, concat(b.material_description,',',b.gsm,',',b.grey_dia,',',b.fin_dia) as description
                from sub_material_mst a, sub_material_dtls b 
                where a.id=b.mst_id and a.trans_type=2
                and a.company_id = $cbo_company_id and b.order_id in ($po_id) 
                and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
                group by a.chalan_no, b.id, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id ,b.order_id
                order by a.chalan_no";
            } 
        else if ($db_type == 2)
            {
                $sql="SELECT  a.chalan_no, b.material_description, b.id, b.gsm, b.grey_dia, b.fin_dia, b.color_id, b.order_id,
                sum(b.quantity) as quantity, b.material_description || ',' || b.gsm || ',' || b.grey_dia || ',' || b.fin_dia as description 
                from sub_material_mst a, sub_material_dtls b 
                where a.id=b.mst_id and a.trans_type=2
                and a.company_id = $cbo_company_id and b.order_id in ($po_id) 
                and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
                group by a.chalan_no, b.id, b.material_description, b.gsm, b.grey_dia, b.fin_dia, b.color_id ,b.order_id
                order by a.chalan_no";
            }
   	}
	//        echo $batch_sql;
	//        echo "<pre>";
	//        print_r($batch_array);
	//	   	  echo $sql;
	?>
        <input type="hidden" name="prod_id" id="prod_id" value="">
        <input type="hidden" name="challan" id="challan" value="">
        <input type="hidden" name="description" id="description" value="">
        <input type="hidden" name="gsm" id="gsm" value="">
        <input type="hidden" name="grey_dia" id="grey_dia" value="">
        <input type="hidden" name="fin_dia" id="fin_dia" value="">
        <input type="hidden" name="balance" id="balance" value="">

    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="60">Challan</th>
                <th width="130">Fab. Desc.</th>
                <th width="40">GSM</th>
                <th width="50">G.Dia</th>
                <th width="50">F.Dia</th>
                <th width="80">Color</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$return_qty=$material_return_arr[$selectResult[csf('chalan_no')]][$selectResult[csf('description')]][$selectResult[csf('color_id')]];

                    $balance=$selectResult[csf('quantity')]-$return_qty-$batch_array[$po_id][$selectResult[csf('id')]];
                    if($fabricfrom == 3)
                    {
                        $balance=  $selectResult[csf('quantity')] - $batch_array[$po_id][$selectResult[csf('id')]];
                    }
                    //echo $selectResult[csf('quantity')]."**".$return_qty."**".$batch_array[$po_id][$selectResult[csf('id')]];
					if($balance>0)
					{
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('chalan_no')]; ?>','<? echo $selectResult[csf('material_description')]; ?>','<? echo $selectResult[csf('gsm')]; ?>','<? echo $selectResult[csf('grey_dia')]; ?>','<? echo $selectResult[csf('fin_dia')]; ?>','<? echo number_format($balance,2);?>')"> 
							<td width="30" align="center"><? echo $i; ?></td>	
							<td width="60" align="center"><p><? echo $selectResult[csf('chalan_no')]; ?></p></td>
							<td width="130" align="center"><? echo $selectResult[csf('material_description')]; ?></td>
							<td width="40"  align="center"><p><? echo $selectResult[csf('gsm')]; ?></p></td>
							<td width="50"  align="center"><p><? echo $selectResult[csf('grey_dia')]; ?></p></td>
							<td width="50"  align="center"><p><? echo $selectResult[csf('fin_dia')]; ?></p></td>
							<td width="80"  align="center"><p><? echo  $color_arr[$selectResult[csf('color_id')]]; ?></p></td> 
						</tr>
					<? 
						$i++;
					}
				}
			?>
            </table>
        </div>
	</div>           
	<?
    exit();	
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	/*	$po_batch_no_arr=array();
		$po_batch_data=sql_select("select max(a.po_batch_no) as po_batch_no, a.po_id, b.color_id from  pro_batch_create_dtls a, pro_batch_create_mst b where a.mst_id=b.id group by b.color_id, a.po_id");
		foreach($po_batch_data as $row)
		{
			$po_batch_no_arr[$row[csf('color_id')]][$row[csf('po_id')]]=$row[csf('po_batch_no')];
		}
	*/		 

	if (str_replace("'", "", $txt_ext_no) != "" || $db_type == 0) 
	{
		$extention_no_cond = "extention_no=$txt_ext_no";
	}
	else 
	{
		$extention_no_cond = "extention_no is null";
	}
	if($operation==0)// Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$batch_update_id=''; $batch_no_creation=str_replace("'","",$batch_no_creation);
		$new_array_color=array(); 
		if (str_replace("'", "", trim($txt_batch_color)) != "") {
			if (!in_array(str_replace("'", "", trim($txt_batch_color)),$new_array_color)){
				$color_id = return_id( str_replace("'", "", trim($txt_batch_color)), $color_arr, "lib_color", "id,color_name","281");
				$new_array_color[$color_id]=str_replace("'", "", trim($txt_batch_color));
			}
			else $color_id =  array_search(str_replace("'", "", trim($txt_batch_color)), $new_array_color);
		} else $color_id = 0;
		//$color_id=return_id( $txt_batch_color, $color_arr, "lib_color", "id,color_name");

		$flag=1;
		if(str_replace("'","",$update_id)=="")
		{
			//$id=return_next_id( "id", "pro_batch_create_mst", 1 ) ;
			
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later

			$system_entry_form=281; $prefix='ABC';
			$new_batch_sl_system_id = explode("*", return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst",$con,1,$cbo_company_id,$prefix,$system_entry_form,date("Y",time()),13 ));
			
			
			$id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
			$batch_update_id=$id;
			//$serial_no=date("y",strtotime($pc_date_time))."-".$id;
			$serial_no = $new_batch_sl_system_id[0];
		 	if($batch_no_creation==1)
			{
				//$txt_batch_number="'".$serial_no."'";
				//$txt_batch_number="'".$id."'";
				$txt_batch_number = "'" .$new_batch_sl_system_id[0]. "'";
			}
			else
			{
				//echo "10**select batch_no from pro_batch_create_mst company_id=$cbo_company_id and batch_no=$txt_batch_number and $extention_no_cond and entry_form=281"; die;
				if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "company_id=$cbo_company_id and batch_no=$txt_batch_number and $extention_no_cond and entry_form=281" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
					disconnect($con); die;			
				}
				$txt_batch_number=$txt_batch_number;
			}
			
			$field_array="id, batch_against, batch_no,machine_no,location_id, batch_date, entry_form, company_id, extention_no, color_id, batch_weight, total_trims_weight, color_range_id, process_id, dur_req_hr, dur_req_min,remarks, aop_type, floor_id,buyer_po_id,style_ref_no,within_group,party_id,aop_batch_for,print_type,design_number,coverage,batch_sl_prefix,batch_sl_prefix_num,batch_sl_no,inserted_by, insert_date"; 
			
			$data_array="(".$id.",".$cbo_batch_against.",".$txt_batch_number.",".$txt_machine_no.",".$cbo_location_name.",".$txt_batch_date.",281,".$cbo_company_id.",".$txt_ext_no.",".$color_id.",".$txt_batch_weight.",".$txt_tot_trims_weight.",".$cbo_color_range.",".$txt_process_id.",".$txt_du_req_hr.",".$txt_du_req_min.",".$txt_remarks.",".$txt_aop_type.",".$cbo_floor_name.",".$txt_po_id.",".$txt_style_ref.",".$hide_within_group.",".$hide_party_id.",".$cbo_batch_for.",".$txt_print_type.",".$txt_design_number.",".$txt_coverage.",'" . $new_batch_sl_system_id[1] ."',".$new_batch_sl_system_id[2] .",'".$new_batch_sl_system_id[0]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into pro_batch_create_mst (".$field_array.") values ".$data_array;disconnect($con); die;
			$rID=sql_insert("pro_batch_create_mst",$field_array,$data_array,0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		else
		{
			$batch_update_id=str_replace("'","",$update_id);
			$serial_no=str_replace("'","",$txt_batch_sl_no);
			
			if($batch_no_creation!=1)
			{
				if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "company_id=$cbo_company_id and batch_no=$txt_batch_number and color_id=$color_id  and $extention_no_cond and id<>$update_id and entry_form=281" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
					disconnect($con); die;			
				}
			}

			$field_array_update="batch_against*batch_no*machine_no*location_id*batch_date*company_id*extention_no*color_id*batch_weight*total_trims_weight*color_range_id*process_id*dur_req_hr*dur_req_min*remarks*aop_type*floor_id*buyer_po_id*style_ref_no*within_group*party_id*updated_by*update_date";
			
			$data_array_update=$cbo_batch_against."*".$txt_batch_number."*".$txt_machine_no."*".$cbo_location_name."*".$txt_batch_date."*".$cbo_company_id."*".$txt_ext_no."*".$color_id."*".$txt_batch_weight."*".$txt_tot_trims_weight."*".$cbo_color_range."*".$txt_process_id."*".$txt_du_req_hr."*".$txt_du_req_min."*".$txt_remarks."*".$txt_aop_type."*".$cbo_floor_name."*".$txt_po_id."*".$txt_style_ref."*".$hide_within_group."*".$hide_party_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$rID=sql_update("pro_batch_create_mst",$field_array_update,$data_array_update,"id",$update_id,0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		//echo "10**insert into pro_batch_create_mst (".$field_array.") values ".$data_array;disconnect($con); die;
		//$id_dtls=return_next_id( "id", "pro_batch_create_dtls", 1 ) ;
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		
		$field_array_dtls="id, mst_id, po_id,buyer_po_id, item_description, prod_id, width_dia_type, roll_no, batch_qnty,body_part_id, inserted_by, insert_date";
		//$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, inserted_by, insert_date";
		//$roll_table_id='';
		
		for($i=1;$i<=$total_row;$i++)
		{
			$id_dtls = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
			$po_id="poId_".$i;
			$txtBuyerPoId="txtBuyerPoId_".$i;
			
			$prod_id="txtItemDescid_".$i;
			$prod_desc="txtItemDesc_".$i;
			$txtRollNo="txtRollNo_".$i;
			$hideRollNo="hideRollNo_".$i;
			$txtBatchQnty="txtBatchQnty_".$i;
			$cboDiaWidthType="cboDiaWidthType_".$i;
			$cboBodyPart="cboBodyPart_".$i;

			if($data_array_dtls!="") $data_array_dtls.=",";
			
			$data_array_dtls.="(".$id_dtls.",".$batch_update_id.",".$$po_id.",".$$txtBuyerPoId.",".$$prod_desc.",".$$prod_id.",".$$cboDiaWidthType.",".$$txtRollNo.",".$$txtBatchQnty.",".$$cboBodyPart.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
		}
		
		$rID2=sql_insert("pro_batch_create_dtls",$field_array_dtls,$data_array_dtls,1);
		// echo "10**".$rID."**".$rID2; disconnect($con); die;
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
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

		if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_no=$txt_batch_number and batch_id=$update_id and entry_form=38 and load_unload_id=2 and result=1 and status_active=1 and is_deleted=0") == 1) 
		{
			disconnect($con);
			echo "14**0";
			disconnect($con); die;
		}
		
		$prev_batch_data_arr=array();
		$prev_batch_data=sql_select("select a.id as dtls_id, a.po_id, b.color_id from pro_batch_create_dtls a, pro_batch_create_mst b where a.mst_id=b.id and b.id=$update_id");
		foreach($prev_batch_data as $row)
		{
			$prev_batch_data_arr[$row[csf('dtls_id')]]['po_id']=$row[csf('po_id')];
			$prev_batch_data_arr[$row[csf('dtls_id')]]['color']=$row[csf('color_id')];
		}

		//$color_id=return_id( $txt_batch_color, $color_arr, "lib_color", "id,color_name");
		$new_array_color=array();
		if (str_replace("'", "", trim($txt_batch_color)) != "") {
			if (!in_array(str_replace("'", "", trim($txt_batch_color)),$new_array_color)){
				$color_id = return_id( str_replace("'", "", trim($txt_batch_color)), $color_arr, "lib_color", "id,color_name","281");
				$new_array_color[$color_id]=str_replace("'", "", trim($txt_batch_color));
			}
			else $color_id =  array_search(str_replace("'", "", trim($txt_batch_color)), $new_array_color);
		} else $color_id = 0;
		$flag=1; $batch_no_creation=str_replace("'","",$batch_no_creation);
		
		if(str_replace("'","",$cbo_batch_against)!=2 && str_replace("'","",$hide_update_id)=="" )
		{
			
			if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_id=$update_id and entry_form=38 and load_unload_id in(2) and result=1 and status_active=1 and is_deleted=0") == 1) 			
			{
				echo "14**0";
				disconnect($con); die;
			}
			
		}
		if(str_replace("'","",$cbo_batch_against)==2 && str_replace("'", "", $unloaded_batch) != "" && str_replace("'", "", $ext_from) == 0)
		{
			if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "company_id=$cbo_company_id and batch_no=$txt_batch_number and  $extention_no_cond and entry_form=281 and status_active=1 and is_deleted=0" )==1)
			{
				//check_table_status( $_SESSION['menu_id'],0);
				echo "11**0"; 
				disconnect($con); die;			
			}
			
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			$system_entry_form=281; $prefix='ABC';
			$new_batch_sl_system_id = explode("*", return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst",$con,1,$cbo_company_id,$prefix,$system_entry_form,date("Y",time()),13 ));
			
			
			//$id=return_next_id( "id", "pro_batch_create_mst", 1 ) ;
			$id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
			$batch_update_id=$id;
			//$serial_no=date("y",strtotime($pc_date_time))."-".$id;
			$serial_no = $new_batch_sl_system_id[0];
			$field_array="id, batch_against, batch_no,machine_no,location_id, batch_date, entry_form, company_id, extention_no, color_id, batch_weight, total_trims_weight, color_range_id, process_id, dur_req_hr, dur_req_min,remarks, aop_type, floor_id,buyer_po_id,style_ref_no,within_group,party_id,aop_batch_for,print_type,design_number,coverage,batch_sl_prefix,batch_sl_prefix_num,batch_sl_no,inserted_by, insert_date";
			
			$data_array="(".$id.",".$cbo_batch_against.",".$txt_batch_number.",".$txt_machine_no.",".$cbo_location_name.",".$txt_batch_date.",281,".$cbo_company_id.",".$txt_ext_no.",".$color_id.",".$txt_batch_weight.",".$txt_tot_trims_weight.",".$cbo_color_range.",".$txt_process_id.",".$txt_du_req_hr.",".$txt_du_req_min.",".$txt_remarks.",".$txt_aop_type.",".$cbo_floor_name.",".$txt_po_id.",".$txt_style_ref.",".$hide_within_group.",".$hide_party_id.",".$cbo_batch_for.",".$txt_print_type.",".$txt_design_number.",".$txt_coverage.",'" . $new_batch_sl_system_id[1] ."',".$new_batch_sl_system_id[2] .",'".$new_batch_sl_system_id[0] ."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$field_array_dtls="id, mst_id, po_id,buyer_po_id, item_description, prod_id, width_dia_type, roll_no, batch_qnty,body_part_id, inserted_by, insert_date";
			
			
			for($i=1;$i<=$total_row;$i++)
			{
				$id_dtls = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				$po_id="poId_".$i;
				$txtBuyerPoId="txtBuyerPoId_".$i;
				$prod_id="txtItemDescid_".$i;
				$prod_desc="txtItemDesc_".$i;
				$txtRollNo="txtRollNo_".$i;
				$hideRollNo="hideRollNo_".$i;
				$txtBatchQnty="txtBatchQnty_".$i;
				$cboDiaWidthType="cboDiaWidthType_".$i;
				$updateIdDtls="updateIdDtls_".$i;
				$cboBodyPart="cboBodyPart_".$i;

				//$itemDesc=str_replace("'","",$$prod_desc).', '.str_replace("'","",$$gsm).', '.str_replace("'","",$$dia);
			
				if($data_array_dtls!="") $data_array_dtls.=",";
				
				$data_array_dtls.="(".$id_dtls.",".$batch_update_id.",".$$cbofabricfrom.",".$$po_id.",".$$txtBuyerPoId.",'".$$prod_desc."',".$$prod_id.",'".$$gsm."','".$$dia."','".$$findia."','".$$cboDiaWidthType."','".$$txtRollNo."',".$$txtBatchQnty.",'".$$txtrecChallan."',".$$cboBodyPart.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
				
				//$id_dtls=$id_dtls+1;
			}
			
			$rID=sql_insert("pro_batch_create_mst",$field_array,$data_array,0);
			if($rID==1  && $flag==1) $flag=1; else $flag=0;
			//echo "10**insert into pro_batch_create_dtls (".$field_array_dtls.") values ".$data_array_dtls;disconnect($con); die;
			$rID2=sql_insert("pro_batch_create_dtls",$field_array_dtls,$data_array_dtls,0);//disconnect($con); die;
			
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
			// echo "10**".$rID.'='.$rID2.'='.$flag."nnn"; disconnect($con); die;
		}
		else
		{
			//$poBatchNoArr=array();
			$batch_update_id=str_replace("'","",$update_id);
			$serial_no=str_replace("'","",$txt_batch_sl_no);
			
			if($batch_no_creation!=1)
			{
				if(is_duplicate_field( "batch_no", "pro_batch_create_mst", "company_id=$cbo_company_id and batch_no=$txt_batch_number and $extention_no_cond and id<>$update_id and entry_form=281 and status_active=1 and is_deleted=0" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
					disconnect($con); die;			
				}
			}
		
			$field_array_update="batch_against*batch_no*machine_no*location_id*floor_id*batch_date*extention_no*color_id*batch_weight*total_trims_weight*color_range_id*process_id*dur_req_hr*dur_req_min*remarks*buyer_po_id*style_ref_no*within_group*party_id*aop_type*aop_batch_for*print_type*design_number*coverage*updated_by*update_date";
			
			$data_array_update=$cbo_batch_against."*".$txt_batch_number."*".$txt_machine_no."*".$cbo_location_name."*".$cbo_floor_name."*".$txt_batch_date."*".$txt_ext_no."*".$color_id."*".$txt_batch_weight."*".$txt_tot_trims_weight."*".$cbo_color_range."*".$txt_process_id."*".$txt_du_req_hr."*".$txt_du_req_min."*".$txt_remarks."*".$txt_po_id."*".$txt_style_ref."*".$hide_within_group."*".$hide_party_id."*".$txt_aop_type."*".$cbo_batch_for."*".$txt_print_type."*".$txt_design_number."*".$txt_coverage."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$field_array_dtls="id, mst_id, po_id,buyer_po_id, item_description, prod_id, width_dia_type, roll_no, batch_qnty,body_part_id, inserted_by, insert_date";
			$field_array_dtls_update="po_id*buyer_po_id*item_description*prod_id*width_dia_type*roll_no*batch_qnty*body_part_id*updated_by*update_date";
			
			for($i=1;$i<=$total_row;$i++)
			{
				$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				$po_id="poId_".$i;
				$txtBuyerPoId="txtBuyerPoId_".$i;
				$prod_id="txtItemDescid_".$i;
				$prod_desc="txtItemDesc_".$i;
				$txtRollNo="txtRollNo_".$i;
				$hideRollNo="hideRollNo_".$i;
				$txtBatchQnty="txtBatchQnty_".$i;
				$cboDiaWidthType="cboDiaWidthType_".$i;
				$updateIdDtls="updateIdDtls_".$i;
				$cboBodyPart="cboBodyPart_".$i;
				//$itemDesc=str_replace("'","",$$prod_desc).', '.str_replace("'","",$$gsm).', '.str_replace("'","",$$dia);
				
				if(str_replace("'","",$$updateIdDtls)!="")
				{
					$id_arr[]=str_replace("'",'',$$updateIdDtls);
					$data_array_dtls_update[str_replace("'",'',$$updateIdDtls)] = explode("*",($$po_id."*".$$txtBuyerPoId."*".$$prod_desc."*".$$prod_id."*".$$cboDiaWidthType."*".$$txtRollNo."*".$$txtBatchQnty."*".$$cboBodyPart."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
					$id_dtls=str_replace("'",'',$$updateIdDtls);
				}
				else
				{
					if($data_array_dtls!="") $data_array_dtls.=",";
			
					$data_array_dtls.="(".$id_dtls_batch.",".$batch_update_id.",".$$po_id.",".$$txtBuyerPoId.",".$$prod_desc.",".$$prod_id.",".$$cboDiaWidthType.",".$$txtRollNo.",".$$txtBatchQnty.",".$$cboBodyPart.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 	
					//$id_dtls_batch=$id_dtls_batch+1;
				}
				
			}
			
			//echo "10**".bulk_update_sql_statement( "pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );disconnect($con); die;
			$rID=sql_update("pro_batch_create_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID==1  && $flag==1) $flag=1; else $flag=0;
			
			if($data_array_dtls_update!="")
			{
				$rID2=execute_query(bulk_update_sql_statement( "pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ));
				//echo bulk_update_sql_statement( "pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );
				if($rID2==1 && $flag==1) $flag=1; else $flag=0; 
			}
			
			//echo "6**0**insert into pro_batch_create_dtls (".$field_array_dtls.") values ".$data_array_dtls;disconnect($con); die;
			if($data_array_dtls!="")
			{
				$rID3=sql_insert("pro_batch_create_dtls",$field_array_dtls,$data_array_dtls,0);
				if($rID3==1 && $flag==1) $flag=1; else $flag=0; 
				
			}
		
			if($txt_deleted_id!="")
			{
				$field_array_status="updated_by*update_date*status_active*is_deleted";
				$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		
				$rID4=sql_multirow_update("pro_batch_create_dtls",$field_array_status,$data_array_status,"id",$txt_deleted_id,1);
				if($rID4==1 && $flag==1) $flag=1; else $flag=0; 
				
			}
		}
		//echo "10**".$flag;die;
		//echo "10**".$rID."=".$rID2."=".$rID3."=".$rID4."=".$flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**1";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".$batch_update_id."**".$serial_no."**".str_replace("'","",$txt_batch_number);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="batch_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
	function js_set_value( batch_id,batch_no,aop_ref,unloaded_batch,ext)
	{
		//alert (batch_id);
		document.getElementById('hidden_batch_id').value=batch_id;
		document.getElementById('hidden_batch_no').value = batch_no;
		document.getElementById('hidden_unloaded_batch').value = unloaded_batch;
		document.getElementById('hidden_aop_ref').value = aop_ref;
		document.getElementById('hidden_ext').value = ext;
			
		parent.emailwindow.hide();
	}
    </script>
</head>
<body>
<div align="center">
	<fieldset style="width:800px;margin-left:4px;">
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="780" class="rpt_table">
                <thead>
                    <tr>
                        <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                	<tr>
                        <th>Batch SL</th>
                        <th>Batch No</th>
                        <th>Design No</th>
                        <th>AOP Ref.</th>
                        <th >Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                            <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" value="">
							<input type="hidden" name="hidden_aop_ref" id="hidden_aop_ref" value="">
							<input type="hidden" name="hidden_unloaded_batch" id="hidden_unloaded_batch" value="">
							<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" value="">
							<input type="hidden" name="hidden_ext" id="hidden_ext" value="">
                        </th>
                    </tr> 
                </thead>
                <tr class="general">
                    <td align="center">	
                    	<input type="text" style="width:100px" class="text_boxes"  name="txt_search_batch_sl" id="txt_search_batch_sl" placeholder="Write Before ( - )" />
                        <?
                           //$search_by_arr=array(1=>"Batch No");
                            //echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>                 
                    <td align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td>
                    <td align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_design_no" id="txt_design_no" />	
                    </td>
                    <td align="center">
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_aop_ref" id="txt_aop_ref" />	
                    </td>
                    <td>
                    	<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                        <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                    </td>						
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_batch_sl').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_aop_ref').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_design_no').value, 'create_batch_search_list_view', 'search_div', 'aop_batch_creation_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
					<td colspan="6" align="center" width="100%"><? echo load_month_buttons(1); ?></td>                    
				</tr>
            </table>
            <div id="search_div" style="margin-top:10px"></div>   
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_batch_search_list_view")
{
	//print_r ($data);
	$data=explode('_',$data);
	$search_sl=$data[1];
	$batch_number_search =$data[0];
	$company_id =$data[2];
	$search_type =$data[3];
	$aop_ref =$data[4];
	$txt_date_from=$data[5];
	$txt_date_to=$data[6];
	$design_no=$data[7];


	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));

	if($design_no!='') $design_no_cond=" and design_no like '$design_no%'"; else $design_no_cond="";
	if($design_no!='') $design_no_cond1=" and b.design_no like '$design_no%'"; else $design_no_cond1="";

	$po_arr=return_library_array( "SELECT id,order_no from subcon_ord_dtls where status_active=1 and is_deleted=0 $design_no_cond",'id','order_no');
	if($search_sl!='') $search_sl_cond=" and a.id='$search_sl'"; else $search_sl_cond="";
	
	if($search_type==1)
	{
		if($batch_number_search!='') $batch_number_cond=" and a.batch_no='$batch_number_search'"; else $batch_number_cond="";
		if($aop_ref!='') $aop_cond=" and a.aop_reference='$aop_ref'"; else $aop_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($batch_number_search!='') $batch_number_cond=" and a.batch_no like '%$batch_number_search%'"; else $batch_number_cond="";
		if($aop_ref!='') $aop_cond=" and a.aop_reference like '%$aop_ref%'"; else $aop_cond="";
	}
	else if($search_type==2)
	{
		if($batch_number_search!='') $batch_number_cond=" and a.batch_no like '$batch_number_search%'"; else $batch_number_cond="";
		if($aop_ref!='') $aop_cond=" and a.aop_reference like '$aop_ref%'"; else $aop_cond="";
	}
	else if($search_type==3)
	{
		if($batch_number_search!='') $batch_number_cond=" and a.batch_no like '%$batch_number_search'"; else $batch_number_cond="";
		if($aop_ref!='') $aop_cond=" and a.aop_reference like '%$aop_ref'"; else $aop_cond="";
	}	
	//echo $aop_cond; die;
	if($aop_ref!='' || $design_no!='')
	{
		$ord_sql = "SELECT b.id,a.subcon_job,a.aop_reference,b.order_no, b.design_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$company_id $aop_cond $design_no_cond1 and a.entry_form=278 and  a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
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
		$ord_sql = "SELECT b.id,a.subcon_job,a.aop_reference,b.order_no, b.design_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$company_id $design_no_cond1 and a.entry_form=278 and  a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array();$design_no_arr=array();
		foreach ($ordArray as $row)
		{
			$po_arr[$row[csf('id')]] = $row[csf('order_no')];
			$ref_arr[$row[csf('id')]] = $row[csf('aop_reference')];
			$design_no_arr[$row[csf('id')]] = $row[csf('design_no')];
		}
		//$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
		$po_id_cond='';
	}

	
	if($db_type==0)
	{
		if ($start_date!="" &&  $end_date!="") $date_search_cond  = "and a.batch_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $date_search_cond ="";
	}

	if($db_type==2)
	{
		if ($start_date!="" &&  $end_date!="") $date_search_cond  = "and a.batch_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $date_search_cond ="";
	}
	
	if($db_type==0)
	{
		$sql = "SELECT a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min, group_concat(b.po_id) as po_id from pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=281 $batch_number_cond $search_sl_cond $po_id_cond $date_search_cond group by a.id, a.batch_no, a.extention_no order by a.id DESC"; 
	}
	elseif($db_type==2)
	{
		$sql = "SELECT a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min, listagg(b.po_id,',') within group (order by b.po_id) as po_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=281 $batch_number_cond $search_sl_cond $po_id_cond $date_search_cond group by a.id, a.batch_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.dur_req_hr, a.dur_req_min order by a.id DESC"; 
	}
	//echo $sql;
	
	$nameArray=sql_select( $sql );
	$batch_id=array();
	foreach ($nameArray as $row) {
		$ids = implode(",", array_unique(explode(",", $row[csf("po_id")])));
		$po_ids .= $ids . ",";
		$batch_id[] .= $row[csf("id")];
	}
	$sql_load_unload="SELECT id, batch_id,load_unload_id,result from pro_fab_subprocess where batch_id in (".implode(",",$batch_id).") and load_unload_id in (1,2) and entry_form=38 and is_deleted=0 and status_active=1";
	$load_unload_data=sql_select($sql_load_unload);
	foreach ($load_unload_data as $row)
	{
		if($row[csf('load_unload_id')]==1)
		{
			$load_unload_arr[$row[csf('batch_id')]] = $row[csf('load_unload_id')];
		}
		else if($row[csf('load_unload_id')]==2 )
		{
			$unloaded_batch[$row[csf('batch_id')]] = $row[csf('batch_id')];
		}
	}
	
	$re_dyeing_from = return_library_array("SELECT  re_dyeing_from from pro_batch_create_mst where re_dyeing_from <>0 and status_active = 1 and is_deleted = 0 and entry_form=281","re_dyeing_from","re_dyeing_from");
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="70">Batch No</th>
                <th width="70">Design No</th>
                <th width="90">Color</th>
                <th width="80">Batch Weight</th>
                <th width="70">Batch Date</th>
                <th width="120">AOP Ref.</th>
                <th>PO No.</th>
            </thead>
        </table>
        <div style="width:728px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="list_view" >
            <?
				$i=1;
				
				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($re_dyeing_from[$selectResult[csf('id')]])
					{
						$ext_from = $re_dyeing_from[$selectResult[csf('id')]];
					}else{
						$ext_from = "0";
					}

					$order_no=''; $order_ids=''; $order_ids=''; $all_ref_arr=array(); $all_design_no_arr=array(); $ref_no=''; $design_no='';
					$order_id=array_unique(explode(",",$selectResult[csf("po_id")]));
					foreach($order_id as $val)
					{
						if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
						if($order_ids=="") $order_ids=$val; else $order_ids.=", ".$val;
						$all_ref_arr[] .= $ref_arr[$val];
						$all_design_no_arr[] = $design_no_arr[$val];
						//$ref_arr[$row[csf("id")]];
					}
					//echo "<pre>";
					//print_r($all_ref_arr);
					$ref_no = implode(",", array_unique($all_ref_arr));
					$ref_no = chop($ref_no,',');
					$order_no=implode(",",array_unique(explode(",",chop($order_no,','))));

					$design_no = implode(",", array_unique($all_design_no_arr));
					$design_no = chop($design_no,',');
					
				?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('batch_no')]; ?>','<? echo $ref_no ;?>','<? echo $unloaded_batch[$selectResult[csf('id')]]; ?>','<? echo $ext_from ;?>')"> 
                        <td width="30" align="center"><? echo $i; ?></td>	
                        <td width="70" align="center"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
                        <td width="70" align="center"><p><? echo $design_no; ?></p></td>
                        <td width="90"  align="center"><p><? echo  $color_arr[$selectResult[csf('color_id')]]; ?></p></td> 
                        <td width="80"  align="center"><p><? echo $selectResult[csf('batch_weight')]; ?></p></td>
                        <td width="70"  align="center"><p><? echo change_date_format($selectResult[csf('batch_date')]); ?></p></td>
                        <td width="120"  align="center"><p><? echo $ref_no; ?></p></td>
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
	//echo  create_list_view("list_view", "Batch No,Ext. No,Batch Weight,Total Trims Weight, Batch Date, Color", "100,70,80,80,80,80","600","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,0,0,color_id", $arr, "batch_no,extention_no,batch_weight,total_trims_weight,batch_date,color_id", "",'','0,0,2,2,3,0');
	
exit();	
}
 
if ($action=="populate_data_from_search_popup")
{
	$data=explode("**",$data);
	$batch_id=$data[1];
	$batch_against=$data[0];
	//echo $batch_against.'dd';
	$batch_no = $data[2];
	$ext_from = $data[3];
	$company_id = $data[4];
	$unloaded_batch = $data[5];
	//echo "select id, company_id, batch_no, extention_no, batch_weight, total_trims_weight, batch_date, color_id, color_range_id, process_id, DATE_FORMAT(insert_date,'%y') as year from pro_batch_create_mst where id='$batch_id'";
	if($db_type==0)
	{
		$year_cond=" DATE_FORMAT(insert_date,'%y') as year";
	}
	else if($db_type==2)
	{
		$year_cond=" TO_CHAR(insert_date,'RR') as year";
	}
	$incrementExtentionNo="";
	if($batch_against==2) // Re-dyeing- Extention sequence maintain
	{
		if($unloaded_batch!="" && $ext_from ==0)
		{
			$exists_data_no = sql_select("SELECT a.batch_no,max(a.extention_no) as max_extention_no from pro_batch_create_mst a  where a.batch_no = '".$batch_no."' and a.company_id= $company_id and a.status_active = 1 and a.entry_form=281 and a.is_deleted = 0 group by batch_no");
			//echo "select a.batch_no,max(a.extention_no) as max_extention_no from pro_batch_create_mst a  where a.batch_no = '".$batch_no."' and a.company_id= $company_id and a.status_active = 1 and a.entry_form=281 and a.is_deleted = 0 group by batch_no";
			$exists_extention_no = $exists_data_no[0][csf('max_extention_no')];
			if($exists_extention_no>0)
			{
				$incrementExtentionNo = $exists_extention_no+1;
			}else {
				$incrementExtentionNo = 1;
			}
		}
	}
	
	$dyeing_batch="SELECT batch_id from pro_fab_subprocess where batch_id='$batch_id' and entry_form=38 and status_active=1 and is_deleted=0";
	$dyeing_batch_result=sql_select($dyeing_batch);
	foreach ($dyeing_batch_result as $row)
	{
		echo "document.getElementById('dyeing_batch_id').value 	= '".$row[csf("batch_id")]."';\n";  
	}
	
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		//$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	$data_array=sql_select("SELECT id, company_id,location_id,machine_no, batch_against, batch_no, extention_no, batch_weight, total_trims_weight, batch_date, re_dyeing_from, color_id, color_range_id, process_id,aop_batch_for, dur_req_hr, dur_req_min, remarks, aop_type, print_type, design_number, coverage, floor_id, buyer_po_id,style_ref_no,within_group,party_id ,$year_cond,batch_sl_no from pro_batch_create_mst where id='$batch_id' and entry_form=281 and status_active =1 and is_deleted =0");
	//print_r($data_array);
	foreach ($data_array as $row)
	{
		//if($row[csf("extention_no")]==0) $ext_no=''; else $ext_no=$row[csf("extention_no")];
		if($incrementExtentionNo=="")
		{
			if ($row[csf("extention_no")] == 0) $incrementExtentionNo = ''; else $incrementExtentionNo = $row[csf("extention_no")];
		}
		
		//$serial_no=$row[csf("year")]."-".$row[csf("id")];
		if($row[csf("batch_sl_no")] !="")
		{
			$serial_no = $row[csf("batch_sl_no")] ;
		}
		else
		{
			$serial_no = $row[csf("year")]."-".$row[csf("id")] ;
		}

		//echo "10**".$serial_no; die;
		echo "document.getElementById('txt_batch_sl_no').value 		= '".$serial_no."';\n"; 
		echo "document.getElementById('hide_within_group').value 	= '".$row[csf("within_group")]."';\n";    
		echo "document.getElementById('hide_party_id').value 		= '".$row[csf("party_id")]."';\n";    
		echo "document.getElementById('cbo_batch_against').value 	= '".$row[csf("batch_against")]."';\n";    
		//echo "active_inactive();\n";
		echo "document.getElementById('txt_batch_date').value 		= '".change_date_format($row[csf("batch_date")])."';\n";  
		echo "document.getElementById('txt_batch_weight').value 	= '".$row[csf("batch_weight")]."';\n";  
		echo "document.getElementById('cbo_company_id').value 		= '".$row[csf("company_id")]."';\n"; 
		echo "document.getElementById('cbo_batch_for').value 		= '".$row[csf("aop_batch_for")]."';\n";   
		echo "document.getElementById('txt_tot_trims_weight').value = '".$row[csf("total_trims_weight")]."';\n";  
		echo "document.getElementById('txt_batch_number').value 	= '".$row[csf("batch_no")]."';\n";  
		
		echo "document.getElementById('txt_batch_color').value 		= '".$color_arr[$row[csf("color_id")]]."';\n";  
		echo "document.getElementById('cbo_color_range').value 		= '".$row[csf("color_range_id")]."';\n";

		echo "set_multiselect('txt_process_id','0','1','".$row[csf("process_id")]."','0');\n";
		echo "document.getElementById('txt_du_req_hr').value 		= '".$row[csf("dur_req_hr")]."';\n";
		echo "document.getElementById('txt_du_req_min').value 		= '".$row[csf("dur_req_min")]."';\n";
		echo "document.getElementById('txt_remarks').value 			= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('update_id').value 			= '".$row[csf("id")]."';\n";
		echo "document.getElementById('hide_update_id').value 		= '';\n";
		echo "document.getElementById('ext_from').value = '".$ext_from."';\n";
		echo "document.getElementById('unloaded_batch').value = '".$unloaded_batch."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_batch_creation',1);\n";	

		//echo "document.getElementById('txt_process_name').value 	= '".$process_name."';\n";
		//echo "document.getElementById('txt_ext_no').value 			= '".$ext_no."';\n";  
		//echo "document.getElementById('txt_ext_no').value = '" . $incrementExtentionNo . "';\n";
		//echo "document.getElementById('hidden_main_process_id').value 	= '1';\n";	

		echo "document.getElementById('cbo_location_name').value 	= '".$row[csf("location_id")]."';\n";

		echo "load_drop_down( 'requires/aop_batch_creation_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_floor', 'floor_td' );";
		echo "document.getElementById('cbo_floor_name').value 	= '".$row[csf("floor_id")]."';\n";
		
		echo "load_drop_down( 'requires/aop_batch_creation_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_floor_name').value, 'load_drop_down_machine', 'machine_td' );";
		echo "document.getElementById('txt_machine_no').value 	= '".$row[csf("machine_no")]."';\n";	
		echo "document.getElementById('txt_aop_type').value 	= '".$row[csf("aop_type")]."';\n";
		echo "document.getElementById('txt_print_type').value 	= '".$row[csf("print_type")]."';\n";	
		echo "document.getElementById('txt_design_number').value= '".$row[csf("design_number")]."';\n";
		echo "document.getElementById('txt_coverage').value 	= '".$row[csf("coverage")]."';\n";	
		echo "document.getElementById('cbo_floor_name').value 	= '".$row[csf("floor_id")]."';\n";	
		echo "document.getElementById('txt_style_ref').value 	= '".$row[csf("style_ref_no")]."';\n";	
		echo "document.getElementById('txt_po_no').value 		= '".$buyer_po_arr[$row[csf("buyer_po_id")]]['po']."';\n";	
		//echo "document.getElementById('txt_style_ref').value 	= '".$buyer_po_arr[$row[csf("buyer_po_id")]]['style']."';\n";	

		if($batch_against==2)
		{
			echo "document.getElementById('cbo_batch_against').value = '".$batch_against."';\n";
			//echo "$('#txt_ext_no').removeAttr('disabled','disabled');\n";
			echo "$('#txt_batch_color').attr('disabled','disabled');\n";
			echo "$('#txt_batch_number').attr('readOnly','readOnly');\n";
			echo "$('#cbo_color_range').attr('disabled','disabled');\n";
			echo "$('#txt_process_name').attr('disabled','disabled');\n";
		}
		
		if($row[csf("batch_against")]==2)
		{
			$prv_batch_against=return_field_value("batch_against","pro_batch_create_mst","id=$row[re_dyeing_from]");
			echo "document.getElementById('hide_batch_against').value = '".$prv_batch_against."';\n"; 
			echo "document.getElementById('hide_update_id').value = '".$row[csf("id")]."';\n";
		}
		else
		{
			echo "document.getElementById('hide_batch_against').value = '".$row[csf("batch_against")]."';\n"; 
			echo "document.getElementById('hide_update_id').value = '';\n";
		}
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_batch_creation',1);\n";	 		
		 
	}
	exit();
}

if( $action == 'batch_details' ) 
{
	$data=explode('**',$data);
	$batch_id=$data[1];
	$batch_against=$data[0];
	$dyeing_batch_id=$data[2];
	$tblRow=0;
 
	if($batch_against==2)
	{
		$disbled="disabled='disabled'";
		$disbled_drop_down=1; 
	}
	elseif ($batch_against==1)
	{
		if ($dyeing_batch_id=='')
		{
			$disbled="";
			$disbled_drop_down=0; 
		}
		else
		{
			$disbled="disabled='disabled'";
			$disbled_drop_down=1; 
		}
	}
 	/*$party_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
 	$job_no_arr = return_library_array( "select id, job_no_mst from subcon_ord_dtls",'id','job_no_mst');
 	$main_process_arr=return_library_array( "select id, main_process_id from subcon_ord_dtls",'id','main_process_id');

	//$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id',' short_name');
	$order_arr=array();
	
	$order_sql=sql_select("select a.party_id, b.id, b.main_process_id, b.order_no from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['po_no']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('id')]]['main_process_id']=$row[csf('main_process_id')];
	}*/
	
	$order_sql ="SELECT id,order_no,buyer_style_ref,buyer_po_no from subcon_ord_dtls where status_active =1 and is_deleted =0";
		$order_sql_res=sql_select($order_sql);
	foreach ($order_sql_res as $row)
	{
		//$order_arr[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		//$order_arr[$row[csf("id")]]['buyer_style_ref']=$row[csf("buyer_style_ref")];
		$order_arr[$row[csf("id")]]['buyer_po_no']=$row[csf("buyer_po_no")];
	}
	unset($order_sql_res);

	$buyer_po_arr=array();
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number,a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);

	$data_array=sql_select("SELECT a.company_id,a.within_group,b.id, b.fabric_from, b.po_id,b.buyer_po_id, b.item_description, b.prod_id, b.fin_dia, b.width_dia_type, b.roll_no, b.batch_qnty, b.rec_challan,b.grey_dia,b.gsm,b.body_part_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.mst_id='$batch_id' and a.status_active =1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=281 order by b.id");
	foreach($data_array as $row)
	{
		$tblRow++;
		?>
		<tr class="general" id="tr_<? echo $tblRow; ?>">
			<?
			$po_no=$row[csf('po_id')];
			$item_id=$row[csf('prod_id')];
			$item_desc=$row[csf('item_description')];
			$ex_item_desc=explode(',',$item_desc);

			if($row[csf('within_group')]==1)
			{
				$po_no=$buyer_po_arr[$row[csf('buyer_po_id')]]['po'];
			}else{
				$po_no=$order_arr[$row[csf('po_id')]]['buyer_po_no'];
			}
			//$body_part_id=return_field_value("body_part","subcon_ord_dtls","id='$po_no' and status_active=1 and is_deleted=0 group by body_part",'body_part');
			?>
			 <td id="field_po_id_<? echo $tblRow; ?>">	
            	<?
				echo create_drop_down( "poId_".$tblRow, 150, "SELECT b.id as po_id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and b.id='".$row[csf('po_id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.order_no order by b.order_no ASC","po_id,po_number", 0, "-- Select PO --", $po_no, "hidden_data_load($tblRow);","", "", "", "", "", "","","poId[]" );
            	?>
            </td> 
            <td>
                <input type="text" name="txtBuyerPoNo[]" id="txtBuyerPoNo_<? echo $tblRow; ?>" class="text_boxes" style="width:100px;" value="<? echo $po_no; ?>" readonly />
                <input type="hidden" name="txtBuyerPoId[]" id="txtBuyerPoId_<? echo $tblRow; ?>"  value="<? echo $row[csf('buyer_po_id')]; ?>"  />
        	</td> 
            <td>
            	<? 
            	echo create_drop_down( "cboBodyPart_".$tblRow, 130, $body_part,"", 1, "--Select--",$row[csf('body_part_id')], "","","","", "", "", "", "","cboBodyPart[]" );
            	?>
            </td>  
        	<td align='center' id='itemDescTd_<? echo $tblRow; ?>'>
				<input type="hidden" name="txtPoNo[]" id="txtPoNo_<? echo $tblRow; ?>" class="text_boxes" style="width:70px;" value="<? echo $order_arr[$po_no]['po_no']; ?>" <? echo $disbled; ?> readonly />
            	<input type="text" name="txtItemDesc[]" id="txtItemDesc_<? echo $tblRow; ?>" value="<? echo $row[csf('item_description')]; ?>" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="openmypage_itemdes(<? echo $tblRow; ?>)" readonly />
            	<input type="hidden" name="txtItemDescid[]" id="txtItemDescid_<? echo $tblRow; ?>" value="<? echo $row[csf('prod_id')]; ?>" class="text_boxes" style="width:60px" />
			</td>
			<td id='DiaWidthType_<? echo $tblRow; ?>'>
				<?
				echo create_drop_down("cboDiaWidthType_".$tblRow, 80, $fabric_typee, "", 1, "-- Select --", $row[csf('width_dia_type')], "", $disbled_drop_down, "", "", "", "", "", "", "cboDiaWidthType[]");
				?>
			</td>                             
            <td>
                <input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? if($row[csf('roll_no')]!=0) echo $row[csf('roll_no')]; ?>" style="width:50px" />
                <input type="hidden" name="hideRollNo[]" id="hideRollNo_<? echo $tblRow; ?>" class="text_boxes" readonly />
                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('id')]; ?>" readonly />
            </td>
            <td>
                <input type="text" name="txtBatchQnty[]"  id="txtBatchQnty_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('batch_qnty')]; ?>" onKeyUp="calculate_batch_qnty();" onChange="check_balance_qnty(this.id)" style="width:60px" />
                <input type="hidden" name="txtBalance[]" id="txtBalance_<? echo $tblRow; ?>" class="text_boxes">
            </td>
            
            <td style="display: none;">
                <input type="text" name="txtrecChallan[]"  id="txtrecChallan_<? echo $tblRow; ?>" class="text_boxes" style="width:60px" readonly />
           
                <input type="text" name="txtJobParty[]"  id="txtJobParty_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" readonly />
                <input type="button" id="increase<? echo $tblRow; ?>" name="increase<? echo $tblRow; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)" />
                <input type="button" id="decrease<? echo $tblRow; ?>" name="decrease<? echo $tblRow; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
            </td>
            <td id="gsmTd<? echo $tblRow; ?>" style="display: none;">
                <input type="text" name="txtGsm[]" id="txtGsm_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:50px" readonly />
            </td>
            <td id="diaTd<? echo $tblRow; ?>" style="display: none;">
                <input type="text" name="txtDia[]" id="txtDia_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:50px" readonly />
            </td>
            <td id="finDiaTd<? echo $tblRow; ?>" style="display: none;">
                <input type="text" name="txtFinDia[]" id="txtFinDia_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:50px" readonly />
            </td>
            <td id="dyenamic_fabricfrom" style="display: none;">
                <?  
				echo create_drop_down("cbofabricfrom_".$tblRow, 70, $blank_array, "", 1, "--Select --", 0, "", 1, "", "", "", "", "", "", "cbofabricfrom[]");                            
                ?>
            </td>
		</tr>
	<?
	}
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
                    $i=1; $process_row_id='';// $not_process_id_print_array=array(1,2,3,4,101,120,121,122,124); 
					//$process_id_print_array=array(25,31,32,33,34,35,39,60,63,64,65,66,67,68,69,70,71,82,83,84,89,90,91,93,125,129,132,133,136,137,146);
					$process_id_print_array=array(35,133,148,150,207,84,156,209,93,220,221,230,231,232,233,234,235,236,237);


					$hidden_process_id=explode(",",$txt_process_id);
                    foreach($conversion_cost_head_array as $id=>$name)
                    {
						if(in_array($id,$process_id_print_array))
						{
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
						}
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

if($action=="batch_no_creation")
{
	$batch_no_creation=return_field_value("batch_no_creation","variable_settings_production","company_name ='$data' and variable_list=24 and is_deleted=0 and status_active=1");

	if($batch_no_creation!=1) $batch_no_creation=0;
	
	echo "document.getElementById('batch_no_creation').value 				= '".$batch_no_creation."';\n";
	echo "$('#txt_batch_number').val('');\n";
	echo "$('#update_id').val('');\n";
	if($batch_no_creation==1)
	{
		echo "$('#txt_batch_number').attr('readonly','readonly');\n";
	}
	else
	{
		echo "$('#txt_batch_number').removeAttr('readonly','readonly');\n";
	}
	exit();	
}

if($action=="batch_card_print2")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$update_id=$data[1];
	$batch_sl_no=$data[2];
	$batch_mst_update_id=str_pad($update_id,10,'0',STR_PAD_LEFT);
	//echo $data[3]; die;
	if($db_type==2) $machine_field="machine_no || '-' || brand as machine_no";
	else $machine_field="concat(machine_no,'-',brand) as machine_no";
	$buyer_arrs=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	$company_library=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name");
	$machine_library=return_library_array( "SELECT id, $machine_field from lib_machine_name where status_active =1 and is_deleted=0", "id", "machine_no");
	$job_buyer=return_library_array( "SELECT subcon_job, party_id from subcon_ord_mst where status_active =1 and is_deleted=0", "subcon_job", "party_id");
	//$grouping_arr=return_library_array( "select id, grouping from wo_po_break_down", "id", "grouping");
	
	$buyer_po_arr=array();
	$po_sql ="SELECT b.id,a.job_no,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['internalRef']=$row[csf("grouping")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);
	
	$ord_sql = "SELECT b.id,a.subcon_job,a.aop_reference,a.delivery_date,a.party_id,a.within_group,b.order_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$company and a.entry_form=278 and  a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$order_arr = array();
	$embl_sql ="Select a.subcon_job, a.within_group,a.delivery_date, b.id,a.aop_reference, b.order_no, b.buyer_buyer, b.buyer_po_no, b.buyer_style_ref, b.item_color_id, b.print_type,b.design_no from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=278 and a.subcon_job=b.job_no_mst";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row) 
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("subcon_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
		$order_arr[$row[csf("id")]]['ref']=$row[csf("aop_reference")];
		$order_arr[$row[csf("id")]]['delivery_date']=$row[csf("delivery_date")];
		$order_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
		$order_arr[$row[csf("id")]]['buyer_buyer'] =$row[csf("buyer_buyer")];
		$order_arr[$row[csf("id")]]['buyer_po_no'] =$row[csf("buyer_po_no")];
		$order_arr[$row[csf("id")]]['buyer_style_ref'] =$row[csf("buyer_style_ref")];
		$order_arr[$row[csf("id")]]['print_type'] =$row[csf("print_type")];
		$order_arr[$row[csf("id")]]['item_color_id'] =$row[csf("item_color_id")];
		$order_arr[$row[csf("id")]]['design_no'] =$row[csf("design_no")];
	}
	unset($embl_sql_res);

	if($db_type==0)
	{
		$sql=" SELECT a.id, a.batch_no,a.batch_date,a.machine_no, a.color_id, a.color_range_id, a.extention_no, a.batch_weight, a.process_id,a.party_id,a.within_group,a.remarks,group_concat(distinct(,b.buyer_po_id)) AS buyer_po_id,group_concat(distinct(,b.po_id)) AS po_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.entry_form=281 and a.id=b.mst_id and a.id=$update_id  and a.status_active=1 and a.is_deleted=0 group by a.id, a.batch_no,a.batch_date,a.machine_no, a.color_id, a.color_range_id, a.extention_no, a.batch_weight, a.process_id,a.party_id,a.within_group,a.remarks";
	}
	else if ($db_type==2)
	{
		$sql=" SELECT a.id, a.batch_no,a.batch_date,a.machine_no, a.color_id, a.color_range_id, a.extention_no, a.batch_weight,a.aop_batch_for, a.process_id,a.party_id,a.within_group,a.remarks ,listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id) as buyer_po_id ,listagg(b.po_id,',') within group (order by b.po_id) as po_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.entry_form=281 and a.id=b.mst_id and a.id=$update_id  and a.status_active=1 and a.is_deleted=0 group by a.id, a.batch_no,a.batch_date,a.machine_no, a.color_id, a.color_range_id, a.extention_no, a.batch_weight,a.aop_batch_for, a.process_id,a.party_id,a.within_group,a.remarks";
	}
	$dataArray=sql_select($sql);
	$job_no=$item_color_id=$po_no=$buyer_po_no=$buyer_style_ref=$design_no=$within_group=$buyer_buyer=$aop_ref=$delivery_date=$internalRef=$buyer_job='';
	$order_id=array_unique(explode(",",$dataArray[0][csf('po_id')]));
	foreach($order_id as $val)
	{
		if($job_no=="") $job_no=$order_arr[$val]['job']; else $job_no.=", ".$order_arr[$val]['job'];

		if($item_color_id=="") $item_color_id=$order_arr[$val]['item_color_id']; else $item_color_id.=", ".$order_arr[$val]['item_color_id'];

		if($po_no=="") $po_no=$order_arr[$val]['po']; else $po_no.=", ".$order_arr[$val]['po'];
		if($buyer_po_no=="") $buyer_po_no=$order_arr[$val]['buyer_po_no']; else $buyer_po_no.=", ".$order_arr[$val]['buyer_po_no'];
		if($buyer_style_ref=="") $buyer_style_ref=$order_arr[$val]['buyer_style_ref']; else $buyer_style_ref.=", ".$order_arr[$val]['buyer_style_ref'];
		if($design_no=="") $design_no=$order_arr[$val]['design_no']; else $design_no.=", ".$order_arr[$val]['design_no'];
		if($print_types=="") $print_types=$print_type[$order_arr[$val]['print_type']]; else $print_types.=", ".$print_type[$order_arr[$val]['print_type']];
		if($delivery_date=="") $delivery_date=change_date_format($order_arr[$val]['delivery_date']); else $delivery_date.=", ".change_date_format($order_arr[$val]['delivery_date']);
		if($aop_ref=="") $aop_ref=$order_arr[$val]['ref']; else $aop_ref.=", ".$order_arr[$val]['ref'];

		if($dataArray[0][csf('within_group')]==1) 
		{
			if($buyer_buyer=="") $buyer_buyer=$buyer_arrs[$order_arr[$val]['buyer_buyer']]; else $buyer_buyer.=", ".$buyer_arrs[$order_arr[$val]['buyer_buyer']];
		}
		else
		{
			if($buyer_buyer=="") $buyer_buyer=$order_arr[$val]['buyer_buyer']; else $buyer_buyer.=", ".$order_arr[$val]['buyer_buyer'];
		}
	}
	$buyer_po_id=array_unique(explode(",",$dataArray[0][csf('buyer_po_id')]));
	foreach($buyer_po_id as $val)
	{
		if($internalRef=="") $internalRef=$buyer_po_arr[$val]['internalRef']; else $internalRef.=", ".$buyer_po_arr[$val]['internalRef'];
		if($buyer_job=="") $buyer_job=$buyer_po_arr[$val]['job']; else $buyer_job.=", ".$buyer_po_arr[$val]['job'];
	}
	$job_no = implode(",", array_unique(explode(", ",$job_no)));
	$item_color_id = implode(",", array_unique(explode(", ",$item_color_id)));
	$po_no = implode(",", array_unique(explode(", ",$po_no)));
	$buyer_po_no = implode(",", array_unique(explode(", ",$buyer_po_no)));
	$buyer_style_ref = implode(",", array_unique(explode(", ",$buyer_style_ref)));
	$design_no = implode(",", array_unique(explode(", ",$design_no)));
	$print_types = implode(",", array_unique(explode(", ",$print_types)));
	$buyer_buyer = implode(",", array_unique(explode(", ",$buyer_buyer)));
	$aop_ref = implode(",", array_unique(explode(", ",$aop_ref)));
	$delivery_date = implode(",", array_unique(explode(", ",$delivery_date)));
	$internalRef = implode(",", array_unique(explode(", ",$internalRef)));
	$buyer_job = implode(",", array_unique(explode(", ",$buyer_job)));
	?>
    <div style="width:930px">
    <div align="right"><strong>Printing Time: &nbsp;</strong> <? echo $date=date("F j, Y, g:i a"); ?> </div>
	<table width="930" cellspacing="0" align="center" border="0">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$company]; ?></strong></td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18"><strong><u>AOP Batch Card</u></strong></td>
        </tr>
         <tr>
            <td colspan="6" align="left" style="font-size:16px;"><strong><u>Reference Details</u></strong></td>
        </tr>
		<tr>
			<td colspan="4" align="right" width="110"></td>
            <td bgColor = "#37acf7"  align="right" width="110"><strong>Delivery Unit </strong></td><td width="150px">:</td>
        </tr>
        <tr>
            <td width="110"><strong>Batch No</strong></td><td width="250px"> : <? echo $dataArray[0][csf('batch_no')]; ?></td>
            <td width="110"><strong>Batch Serial</strong></td><td width="200px"> : <? echo $batch_sl_no; ?></td>
            <td width="110"><strong>Batch Color</strong></td><td width="150px"> : <? echo   $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
        </tr>
        <tr>
            <td width="110"><strong>Batch Weight</strong></td><td width="150"> : <? echo $dataArray[0][csf('batch_weight')];?></td>
            <td width="110"><strong>Party</strong></td>
            <td width="250"> : <? if($dataArray[0][csf('within_group')]==1) echo $company_library[$dataArray[0][csf('party_id')]]; else echo $buyer_arrs[$dataArray[0][csf('party_id')]];?></td>
            <td width="110"><strong>AOP Job No.</strong></td>
            <td width="200"> : <? echo $job_no; ?></td>
        </tr>
        <tr>
            <td width="110"><strong>Delivery Date</strong></td>
            <td width="150"> : <? echo $delivery_date; ?></td>
            <td width="110" align="left"><strong>Work Order</strong></td>
            <td width="250"> : <? echo $po_no; ?></td>
			<td width="110" align="left"><strong>Machine No</strong></td>
			<td width="200"> : <? echo $machine_library[$dataArray[0][csf('machine_no')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Internal Ref.</strong>
        	<td width="150" id="ref_td">: <? echo $internalRef; ?></td>
        	<td><strong>Cust. Buyer</strong>
        	</td><td id="buyer_td">: <? echo $buyer_buyer; ?></td>
        	<td width="110"><strong>Batch Date</strong>  </td>
            <td  width="150">: <? echo change_date_format($dataArray[0][csf('batch_date')]);?></td>
       </tr>
        <tr>
        	
        	<td width="110"><strong>Buyer Job</strong></td>
            <td  width="150">: <? echo $buyer_job;?></td>
            <td width="110"><strong>Buyer PO</strong></td>
            <td >: <? echo $buyer_po_no;?></td>
            <td width="110"><strong>B. Style Ref </strong></td>
        	<td width="250">: <? echo $buyer_style_ref; ?></td>
       </tr>
       <tr>
            <td width="110"><strong>AOP Ref.</strong> </td>
            <td >: <? echo $aop_ref;?></td>
       		<td width="110"><strong>Batch For</strong> </td>
            <td>: <?  $aop_batch_for = array(1 =>"Bulk",2 =>"Sample"); 
			echo $aop_batch_for[$dataArray[0][csf('aop_batch_for')]];?></td>
            <td width="110"><strong>Print Type</strong></td>
        	<td width="250">: <? echo $print_types; ?></td>
       </tr>  
       <tr>
       		<td width="110"><strong>Batch Color</strong></td>
       		<td > : <? echo   $color_arr[$item_color_id]; ?></td>
       		<td width="110"><strong>Remarks</strong> </td>
            <td>: <? echo $dataArray[0][csf('remarks')];?></td>
            <td width="110"><strong>Design No</strong> </td>
            <td>: <? echo $design_no;?></td>
       </tr>
       <tr><td>&nbsp;</td></tr>
    </table>
    <div style="float:left; margin-left:10px;"><strong><u>Fabrication Details</u></strong> </div>
    <table align="center" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" style="font-size:12px">
       <thead bgcolor="#dddddd">
            <th width="30">SL</th>
            <th width="300">Const. & Comp.</th>
            <th width="70">Booking GSM</th>
            <th width="70">Actual GSM</th>
            <th width="90">Booking Dia</th>
            <th width="90">Actual Dia</th>
            <th width="90">D/W Type</th>
            <th width="110">Grey Qty.</th>
            <th>Roll No.</th>
        </thead>
        <tbody>
		<?
			$i=1;
			$yarncount=return_library_array( "SELECT id, yarn_count from  lib_yarn_count where status_active =1 and is_deleted=0",'id','yarn_count');
			$yarn_lot_supp=return_library_array( "SELECT lot, supplier_id from  product_details_master where status_active =1 and is_deleted=0",'lot','supplier_id');
			$machine_lib_dia=return_library_array( "SELECT id,dia_width from  lib_machine_name where status_active =1 and is_deleted=0", "id", "dia_width"  );
			$machine_lib_gauge=return_library_array( "SELECT id,gauge from  lib_machine_name where status_active =1 and is_deleted=0", "id", "gauge"  );
			
			$yarn_dtls_arr=array();$mc_dia_gauge_arr=array();
			$yarn_lot_data=sql_select("SELECT order_id, cons_comp_id, yarn_lot, yrn_count_id, machine_id from  subcon_production_dtls where product_type=2 and status_active=1 and is_deleted=0 and yarn_lot!='' group by cons_comp_id, order_id");
			foreach($yarn_lot_data as $rows)
			{
				$yarn_dtls_arr[$rows[csf('cons_comp_id')]][$rows['order_id']]['yarn_lot']=$rows[csf('yarn_lot')];//implode(",",array_unique($rows[csf('yarn_lot')]));
				$yarn_dtls_arr[$rows[csf('cons_comp_id')]][$rows['order_id']]['yarn_count']=$rows[csf('yarn_count')];
				$yarn_dtls_arr[$rows[csf('cons_comp_id')]][$rows['order_id']]['machine_no_id']=$rows[csf('machine_no_id')];
			}
			//var_dump($yarn_dtls_arr);
			$mc_dia_gauge_data=sql_select("SELECT order_id, material_description, mc_dia, mc_gauge from sub_material_dtls where status_active=2 and is_deleted=0");
			foreach($mc_dia_gauge_data as $datas)
			{
				$mc_dia_gauge_arr[$datas[csf('order_id')]][$datas[csf('material_description')]]['mc_dia']=$datas[csf('mc_dia')];
				$mc_dia_gauge_arr[$datas[csf('order_id')]][$datas[csf('material_description')]]['mc_gauge']=$datas[csf('mc_gauge')];
			}
			
			$sql_dtls="SELECT id, SUM(batch_qnty) AS batch_qnty, roll_no, item_description, fin_dia,gsm,grey_dia, po_id, prod_id, width_dia_type, rec_challan, fabric_from, buyer_po_id,program_no  from pro_batch_create_dtls where mst_id=$update_id and  status_active=1 and is_deleted=0 GROUP BY id, roll_no, item_description, fin_dia, po_id, prod_id, width_dia_type, rec_challan, gsm,grey_dia, fabric_from,buyer_po_id,program_no";
			//echo $sql_dtls;
			$sql_result=sql_select($sql_dtls);	
			$aopRef=''; $internalRef='';	$buyerBuyer='';	
	
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//echo $row[csf('buyer_po_id')]."===";
				//if($internalRef=='') $internalRef=$buyer_po_arr[$row[csf('buyer_po_id')]]['internalRef']; else $internalRef.=", ".$buyer_po_arr[$row[csf('buyer_po_id')]]['internalRef'];
				//if($buyerBuyer=='') $buyerBuyer=$buyer_po_arr[$row[csf('buyer_po_id')]]['buyerBuyer']; else $buyerBuyer.=", ".$buyer_po_arr[$row[csf('buyer_po_id')]]['buyerBuyer'];
				
				$desc=explode(",",$row[csf('item_description')]); 
				$y_count=$yarn_dtls_arr[$rows[csf('prod_id')]][$rows['po_id']]['yarn_count'];
				$y_count_id=explode(',',$y_count);
				$yarn_count_value='';
				foreach ($y_count_id as $val)
				{
					if($yarn_count_value=='') $yarn_count_value=$yarncount[$val]; else $yarn_count_value.=", ".$yarncount[$val];
				
				}
				$yarn_lot_d=$yarn_dtls_arr[$rows[csf('prod_id')]][$rows['po_id']]['yarn_lot'];
				$exp_lot=explode(',',$yarn_lot_d);
					
				$machine_dia_up=$machine_lib_dia[$yarn_dtls_arr[$row[csf('prod_id')]][$row['po_id']]['machine_no_id']];
				$machine_gauge_up=$machine_lib_gauge[$yarn_dtls_arr[$row[csf('prod_id')]][$row['po_id']]['machine_no_id']];
				
				
				if($mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_gauge']!="")
				{
					$mc_dia_gauge_in=$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_dia'] .' X ' .$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_gauge'];
				}
				else
				{
					$mc_dia_gauge_in=$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_dia'];
				}
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="30"><? echo $i; ?></td>
					<td width="300" ><? echo $desc[0].",".$desc[1];//$desc[0]; ?></td>
					<td width="70" align="center"><? echo $desc[2];//$desc[1]; ?></td>
					<td width="70" align="center"></td>
					<td width="90" align="center"><? echo $desc[3];//$desc[2]; ?></td>
					<td width="90"><? echo $desc[4]; ?></td>
					<td width="90" align="center"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
					<td width="110" align="right"><? echo number_format($row[csf('batch_qnty')],2); ?></td>
					<td align="center"><? echo $row[csf('roll_no')];  ?></td>
				</tr>
				<?php
			   $b_qty+= $row[csf('batch_qnty')];
				$i++;
			}
			//echo $internalRef."====";
			//$internalRef=implode(",",array_unique(explode(", ",$internalRef)));
			//$buyerBuyer=implode(",",array_unique(explode(", ",$buyerBuyer)));
			?>
        </tbody>
        <tr>
            <td colspan="7" align="right"><b>Sum:</b></td>
            <td align="right" ><b><? echo number_format($b_qty,2); ?> </b></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="7" align="right"><b>Trims Weight:</b></td>
            <td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')],2); ?> </b></td>
            <td>&nbsp;</td>
        </tr>
         <tr>
            <td colspan="7" align="right"><b>Total:</b></td>
            <td align="right"><b><? echo number_format($b_qty+$dataArray[0][csf('total_trims_weight')],2); ?></b></td>
            <td>&nbsp;</td>
        </tr>
         <tr>
            <td colspan="9"  align="right">&nbsp; </td>
        </tr>
		</table>
		<table align="center" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" style="font-size:12px">
		<tr bgColor="#aaa7ab">
																
							<td style="min-height:70px;" align="left" width="30"> <strong>SL</strong></td>
							<td style="min-height:70px;" align="left" width="100"> <strong>Process Name</strong></td>
							<td style="min-height:70px;" align="left" width="100"> <strong>Machine</strong></td>
							<td style="min-height:70px;" align="center" width="100"> <strong>Qty</strong></td>
							<td style="min-height:70px;" align="left" width="200"> <strong>Operator</strong></td>
							<td style="min-height:70px;" align="left" width="100"> <strong>Date</strong></td>
							<td colspan="2" style="min-height:70px;" align="center" > <strong>Remarks</strong></td>
		</tr>
		
		
		
			<? 
                $process=$dataArray[0][csf('process_id')];
                $process_id=explode(',',$process);
                $process_value='';
                $i=1;
                foreach ($process_id as $val)
                {
                    if($process_value=='') 
						$process_value=$i.'. '. $conversion_cost_head_array[$val]; 
					else $process_value.=", ".$i.'. '.$conversion_cost_head_array[$val];
				
					 if ($i%2==0)  
						$bgcolor="#FFFFFF";
						else
						$bgcolor="#FFFFFF";
					?>
						<tr height="60px" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
																
							<td  align="left" width="30"> <?  echo  $i; ?></td>
							<td  align="left" width="100"> <?  echo  $conversion_cost_head_array[$val]; ?></td>
							<td  align="left" width="100"> <?  ?></td>
							<td  align="center" width="100"> <?  ?></td>
							<td  align="left" width="200"> <? ?></td>
							<td  align="left" width="100"> <?  ?></td>
							<td  colspan="2" align="left"> <?   ?></td>
						</tr>
					<?
                    $i++;
                }
             ?><!--
            <tr>
                <th colspan="8" align="left" ><strong>Process Required</strong></th>
            </tr>
            <tr>
                <td colspan="8" title="<? //echo $process_value; ?>"> <strong><? //echo $process_value; ?> </strong></td>
            </tr>-->
        </table><br>
	<div style="width: 930px ; border:1px; float: left; min-height:100px;" align="left">
		<? 
		   	echo get_spacial_instruction($batch_sl_no,"100%",281);
		?>
	</div>
    <table width="930" cellspacing="0" align="center" >
        <tr>
            <td width="465" colspan="3">
                <table cellspacing="0" border="1" rules="all" class="rpt_table" width="465" >
                    <tr>
                        <th align="left"><strong>Shade Result(<i>Hand Written By QC</i>)</strong></th>
                    </tr>
                    <tr>
                        <td colspan="1" style="width:451px; height:80px" >&nbsp;</td>
                    </tr>
                </table>
        	</td> 
            <td width="465" colspan="3">
                <table cellspacing="0" border="1" rules="all" class="rpt_table" width="465" >
                    <tr>
                        <th align="center"><strong>Other Information(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <td colspan="1" style="width:451px; height:80px" >&nbsp;</td>
                    </tr>
                </table> 
            </td>
        </tr>
    </table><br>
    
    
     <table width="230"  border="1" cellspacing="0" align="left" >
        
                    <tr>
                        <td align="right"><b>Aop Order Qty</b></td>
                        <td><input type="text" style="width:100px;"/></td>
                    </tr>
                    <tr>
                        <td  align="right"><b>Previous  Production Qty</b></td>
                        <td><input type="text" style="width:100px;"/></td>
                    </tr>
                     <tr>
                        <td align="right"><b>Date</b></td>
                         <td><input type="text" style="width:100px;"/></td>
                    </tr>
                     <tr>
                        <td align="right"><b>Print Program</b></td>
                        <td><input type="text" style="width:100px;"/></td>
                    </tr>
                     <tr>
                        <td align="right"><b>Batch Qty</b></td>
                         <td><input type="text" style="width:100px;"/></td>
                    </tr>
                    
                </table> 
    </table><br>

		<?
            echo signature_table(162, $data[0], "930px");
        ?>
    </div>
   <!--  <script type="text/javascript">
    	document.getElementById("ref_td").innerHTML='<?// echo ": ".$internalRef; ?>'
    	document.getElementById("buyer_td").innerHTML='<? //echo ": ".$buyer_arrs[$buyerBuyer]; ?>'
   </script> -->
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
    <script>
		function generateBarcode( valuess )
		{
			//alert(valuess);
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
			$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $batch_mst_update_id; ?>');
	</script>
    <? 
}

if($action=="batch_card_print3")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$update_id=$data[1];
	$batch_sl_no=$data[2];
	$location=0;
	
	$batch_mst_update_id=str_pad($update_id,10,'0',STR_PAD_LEFT);
	//echo $data[3]; die;
	if($db_type==2) $machine_field="machine_no || '-' || brand as machine_no";
	else $machine_field="concat(machine_no,'-',brand) as machine_no";
	$buyer_arrs=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	$company_library=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name");
	$machine_library=return_library_array( "SELECT id, $machine_field from lib_machine_name where status_active =1 and is_deleted=0", "id", "machine_no");
	$job_buyer=return_library_array( "SELECT subcon_job, party_id from subcon_ord_mst where status_active =1 and is_deleted=0", "subcon_job", "party_id");
	//$grouping_arr=return_library_array( "select id, grouping from wo_po_break_down", "id", "grouping");
	$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name" );

	$com_dtls = fnc_company_location_address($company, $location, 2);

	
	$buyer_po_arr=array();
	$po_sql ="SELECT b.id,a.job_no,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['internalRef']=$row[csf("grouping")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);
	
	$order_arr = array();
	$embl_sql ="SELECT a.subcon_job, a.within_group,a.delivery_date, b.id,a.aop_reference, b.order_no, b.buyer_buyer, b.buyer_po_no, b.buyer_style_ref, b.item_color_id, b.print_type,b.design_no,b.gmts_color_id,b.gsm,b.grey_dia,b.fin_dia from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=278 and a.subcon_job=b.job_no_mst";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row) 
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("subcon_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
		$order_arr[$row[csf("id")]]['ref']=$row[csf("aop_reference")];
		$order_arr[$row[csf("id")]]['delivery_date']=$row[csf("delivery_date")];
		$order_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
		$order_arr[$row[csf("id")]]['buyer_buyer'] =$row[csf("buyer_buyer")];
		$order_arr[$row[csf("id")]]['buyer_po_no'] =$row[csf("buyer_po_no")];
		$order_arr[$row[csf("id")]]['buyer_style_ref'] =$row[csf("buyer_style_ref")];
		$order_arr[$row[csf("id")]]['print_type'] =$row[csf("print_type")];
		$order_arr[$row[csf("id")]]['item_color_id'] =$row[csf("item_color_id")];
		$order_arr[$row[csf("id")]]['design_no'] =$row[csf("design_no")];
		$order_arr[$row[csf("id")]]['gmts_color_id'] =$row[csf("gmts_color_id")];
		$order_arr[$row[csf("id")]]['gsm'] =$row[csf("gsm")];
		$order_arr[$row[csf("id")]]['grey_dia'] =$row[csf("grey_dia")];
		$order_arr[$row[csf("id")]]['fin_dia'] =$row[csf("fin_dia")];
	}
	unset($embl_sql_res);

	if($db_type==0)
	{
		$sql=" SELECT a.id, a.batch_no,a.batch_date,a.machine_no, a.color_id, a.color_range_id, a.extention_no, a.batch_weight, a.process_id,a.party_id,a.within_group,a.remarks,group_concat(distinct(,b.buyer_po_id)) AS buyer_po_id,group_concat(distinct(,b.po_id)) AS po_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.entry_form=281 and a.id=b.mst_id and a.id=$update_id  and a.status_active=1 and a.is_deleted=0 group by a.id, a.batch_no,a.batch_date,a.machine_no, a.color_id, a.color_range_id, a.extention_no, a.batch_weight, a.process_id,a.party_id,a.within_group,a.remarks";
	}
	else if ($db_type==2)
	{
		$sql=" SELECT a.id, a.batch_no,a.batch_date,a.machine_no, a.color_id, a.color_range_id, a.extention_no, a.batch_weight,a.aop_batch_for, a.process_id,a.party_id,a.within_group,a.remarks ,listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id) as buyer_po_id ,listagg(b.po_id,',') within group (order by b.po_id) as po_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.entry_form=281 and a.id=b.mst_id and a.id=$update_id  and a.status_active=1 and a.is_deleted=0 group by a.id, a.batch_no,a.batch_date,a.machine_no, a.color_id, a.color_range_id, a.extention_no, a.batch_weight,a.aop_batch_for, a.process_id,a.party_id,a.within_group,a.remarks";
	}
	$dataArray=sql_select($sql);
	$job_no=$item_color_id=$po_no=$buyer_po_no=$buyer_style_ref=$fin_dia=$design_no=$gsm=$grey_dia=$gmts_color_id=$within_group=$buyer_buyer=$aop_ref=$delivery_date=$internalRef=$buyer_job='';
	$order_id=array_unique(explode(",",$dataArray[0][csf('po_id')]));
	foreach($order_id as $val)
	{
		if($job_no=="") $job_no=$order_arr[$val]['job']; else $job_no.=", ".$order_arr[$val]['job'];

		if($item_color_id=="") $item_color_id=$order_arr[$val]['item_color_id']; else $item_color_id.=", ".$order_arr[$val]['item_color_id'];

		if($po_no=="") $po_no=$order_arr[$val]['po']; else $po_no.=", ".$order_arr[$val]['po'];
		if($buyer_po_no=="") $buyer_po_no=$order_arr[$val]['buyer_po_no']; else $buyer_po_no.=", ".$order_arr[$val]['buyer_po_no'];
		if($buyer_style_ref=="") $buyer_style_ref=$order_arr[$val]['buyer_style_ref']; else $buyer_style_ref.=", ".$order_arr[$val]['buyer_style_ref'];
		if($design_no=="") $design_no=$order_arr[$val]['design_no']; else $design_no.=", ".$order_arr[$val]['design_no'];
		if($gmts_color_id=="") $gmts_color_id=$order_arr[$val]['gmts_color_id']; else $gmts_color_id.=", ".$order_arr[$val]['gmts_color_id'];
		if($gsm=="") $gsm=$order_arr[$val]['gsm']; else $gsm.=", ".$order_arr[$val]['gsm'];
		if($fin_dia=="") $fin_dia=$order_arr[$val]['fin_dia']; else $fin_dia.=", ".$order_arr[$val]['fin_dia'];
		if($grey_dia=="") $grey_dia=$order_arr[$val]['grey_dia']; else $grey_dia.=", ".$order_arr[$val]['grey_dia'];
		if($print_types=="") $print_types=$print_type[$order_arr[$val]['print_type']]; else $print_types.=", ".$print_type[$order_arr[$val]['print_type']];
		if($delivery_date=="") $delivery_date=change_date_format($order_arr[$val]['delivery_date']); else $delivery_date.=", ".change_date_format($order_arr[$val]['delivery_date']);
		if($aop_ref=="") $aop_ref=$order_arr[$val]['ref']; else $aop_ref.=", ".$order_arr[$val]['ref'];

		if($dataArray[0][csf('within_group')]==1) 
		{
			if($buyer_buyer=="") $buyer_buyer=$buyer_arrs[$order_arr[$val]['buyer_buyer']]; else $buyer_buyer.=", ".$buyer_arrs[$order_arr[$val]['buyer_buyer']];
		}
		else
		{
			if($buyer_buyer=="") $buyer_buyer=$order_arr[$val]['buyer_buyer']; else $buyer_buyer.=", ".$order_arr[$val]['buyer_buyer'];
		}
	}
	$buyer_po_id=array_unique(explode(",",$dataArray[0][csf('buyer_po_id')]));
	foreach($buyer_po_id as $val)
	{
		if($internalRef=="") $internalRef=$buyer_po_arr[$val]['internalRef']; else $internalRef.=", ".$buyer_po_arr[$val]['internalRef'];
		if($buyer_job=="") $buyer_job=$buyer_po_arr[$val]['job']; else $buyer_job.=", ".$buyer_po_arr[$val]['job'];
	}
	$job_no = implode(",", array_unique(explode(", ",$job_no)));
	$item_color_id = implode(",", array_unique(explode(", ",$item_color_id)));
	$po_no = implode(",", array_unique(explode(", ",$po_no)));
	$buyer_po_no = implode(",", array_unique(explode(", ",$buyer_po_no)));
	$buyer_style_ref = implode(",", array_unique(explode(", ",$buyer_style_ref)));
	$design_no = implode(",", array_unique(explode(", ",$design_no)));
	$gmts_color_id = implode(",", array_unique(explode(", ",$gmts_color_id)));
	$gsm = implode(",", array_unique(explode(", ",$gsm)));
	$fin_dia = implode(",", array_unique(explode(", ",$fin_dia)));
	$grey_dia = implode(",", array_unique(explode(", ",$grey_dia)));
	$print_types = implode(",", array_unique(explode(", ",$print_types)));
	$buyer_buyer = implode(",", array_unique(explode(", ",$buyer_buyer)));
	$aop_ref = implode(",", array_unique(explode(", ",$aop_ref)));
	$delivery_date = implode(",", array_unique(explode(", ",$delivery_date)));
	$internalRef = implode(",", array_unique(explode(", ",$internalRef)));
	$buyer_job = implode(",", array_unique(explode(", ",$buyer_job)));
	?>
    <div style="width:930px">
        <div align="right"><strong>Printing Time: &nbsp;</strong> <? echo $date=date("F j, Y, g:i a"); ?> </div>
		<table width="930" cellspacing="0" align="center" border="0">
			<tr>
			<td rowspan="2"width="150" align="left"> 
				<img  src='../../<? echo $com_dtls[2]; ?>' height='70%' width='70%' />
			</td>
				<td colspan="4" align="center" style="font-size:22px"><strong><? echo $company_library[$company]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="4" align="center" ><? echo $com_dtls[1]; ?></td>
				<td align="right" style="font-size:15"><strong><?if($data[5]==1){echo "Organic Cotton";}?></strong></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18"><strong><u>AOP Batch Card</u></strong></td>
			</tr>
			<tr style="height: 30px;">
				<td colspan="6" align="left" style="font-size:16px;"><strong><u>Reference Details</u></strong></td>
			</tr>
			<tr style="height: 30px;">
				<td colspan="6" align="left" style="font-size:16px;"><strong></strong></td>
			</tr>

			<tr>
				<td width="110"><strong>Internal Ref.</strong>
				<td width="250px" id="ref_td">: <? echo $internalRef; ?></td>
				<td width="140"><strong>Batch No</strong></td><td width="200px"> : <? echo $dataArray[0][csf('batch_no')]; ?></td>
				<td width="110"><strong>Batch Date</strong>  </td>
				<td  width="150">: <? echo change_date_format($dataArray[0][csf('batch_date')]);?></td>
			</tr>
			<tr>     	
				<td  width="110"><strong>Cust. Buyer</strong></td>
				<td width="250px" id="buyer_td">: <? echo $buyer_buyer; ?></td>
				<td width="110"><strong>Design No</strong></td>
				<td width="200px"> : <? echo $design_no ?></td>
				<td width="110" align="left"><strong>Machine No</strong></td>
				<td width="200"> : <? echo $machine_library[$dataArray[0][csf('machine_no')]]; ?></td>   	
		</tr>
			<tr>
				<td width="110"><strong>AOP Job No.</strong></td>
				<td width="250px"> : <? echo $job_no; ?></td>
				<td width="110"><strong>Gmt Color</strong></td><td width="150"> : <? echo $color_arr[$gmts_color_id];?></td>
				<td width="110"><strong>Batch Serial</strong></td>
				<td width="200px"> : <? echo $batch_sl_no; ?></td>	
			</tr>
			<tr>
				<td width="110"><strong>Fabric Type</strong></td>
				<td width="150"> : </td>
				<td width="110"><strong>Batch Weight</strong></td><td width="150"> : <? echo $dataArray[0][csf('batch_weight')];?></td>			
			</tr>
			<tr>
					<td width="110"><strong>Booking GSM</strong> </td>
					<td >: <? echo $gsm;?></td>
					<td width="110"><strong>Booking Width</strong> </td>
					<td >: <? echo $grey_dia;?></td>
					<td width="110"><strong>Actual GSM</strong> </td>
					<td >: </td>
			</tr>  
			<tr>
					<td width="110"><strong>Batch Color</strong></td>
					<td > : <? echo $color_arr[$item_color_id]; ?></td>
					<td width="110"><strong>Actual Width</strong></td><td width="150px">: <?=$fin_dia?></td>
					<td width="110"><strong>D/W Type</strong></td><td width="150px"> :</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
		</table>
    
		<table align="center" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" style="font-size:12px">
			<tr bgColor="#aaa7ab">															
				<td style="min-height:70px;" align="left" width="30"> <strong>SL</strong></td>
				<td style="min-height:70px;" align="left" width="100"> <strong>Process Name</strong></td>
				<td style="min-height:70px;" align="left" width="100"> <strong>Machine</strong></td>
				<td style="min-height:70px;" align="center" width="100"> <strong>Qty</strong></td>
				<td style="min-height:70px;" align="left" width="200"> <strong>Operator/Suppervisor</strong></td>
				<td style="min-height:70px;" align="left" width="100"> <strong>Date</strong></td>
				<td colspan="2" style="min-height:70px;" align="center" > <strong>Remarks</strong></td>
			</tr>	
				<? 
				$process=$dataArray[0][csf('process_id')];
				$process_id=explode(',',$process);
				$process_value='';
				$i=1;
				foreach ($process_id as $val)
				{
					if($process_value=='') 
						$process_value=$i.'. '. $conversion_cost_head_array[$val]; 
					else $process_value.=", ".$i.'. '.$conversion_cost_head_array[$val];
				
					if ($i%2==0)  
						$bgcolor="#FFFFFF";
						else
						$bgcolor="#FFFFFF";
					?>
						<tr height="60px" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
																
							<td  align="left" width="30"> <?  echo  $i; ?></td>
							<td  align="left" width="100"> <?  echo  $conversion_cost_head_array[$val]; ?></td>
							<td  align="left" width="100"> <?  ?></td>
							<td  align="center" width="100"> <?  ?></td>
							<td  align="left" width="200"> <? ?></td>
							<td  align="left" width="100"> <?  ?></td>
							<td  colspan="2" align="left"> <?   ?></td>
						</tr>
					<?
					$i++;
				}
			?>
		</table><br><br>
		<table align="center" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" style="font-size:12px">
			<tr style="height: 50px;font-size:20">
				<td width="100"><b>Detail Process:</b></td>
				<td colspan="5"> <b> Print + Curing/Steaming Wash + Stenter Finish+Compacting+Tumble Delivery</b></td>
			</tr>
			<tr style="height: 50px;">
				<td width="100"></td>
				<td colspan="5"></td>
			</tr>
			<tr style="height: 50px;">
				<td width="100"></td>
				<td colspan="5"></td>
			</tr>
		</table>
		<?
            echo signature_table(162, $data[0], "930px");
        ?>
    </div>
   
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
    <script>
		function generateBarcode( valuess )
		{
			//alert(valuess);
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
			$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $batch_mst_update_id; ?>');
	</script>
    <? 
}


if($action=="roll_maintained")
{
	
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and variable_list=3 and is_deleted=0 and status_active=1");
	if($roll_maintained=="" || $roll_maintained==2) $roll_maintained=0; else $roll_maintained=$roll_maintained;
	echo "document.getElementById('roll_maintained').value 				= '".$roll_maintained."';\n";
	exit();	
}

/*if($action=="batch_card_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$update_id=$data[1];
	$batch_sl_no=$data[2];
	$batch_mst_update_id=str_pad($update_id,10,'0',STR_PAD_LEFT);
	//echo $data[3]; die;
	if($db_type==2) $machine_field="machine_no || '-' || brand as machine_no";
	else $machine_field="concat(machine_no,'-',brand) as machine_no";
	
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
	$machine_library=return_library_array( "select id, $machine_field from lib_machine_name", "id", "machine_no");
	$job_buyer=return_library_array( "select subcon_job, party_id from subcon_ord_mst", "subcon_job", "party_id");
	if($db_type==0)
	{
		$sql=" select a.id, a.batch_no,a.batch_date,a.party_id,a.within_group,a.machine_no, a.color_id, a.color_range_id, a.extention_no, a.batch_weight, a.process_id, group_concat(distinct(c.order_no)) AS po_number, a.total_trims_weight, a.style_ref_no,c.job_no_mst,c.buyer_buyer,c.cust_style_ref, c.delivery_date,a.remarks from pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_dtls c where a.entry_form=281 and a.id=b.mst_id and b.po_id=c.id and a.id=$update_id  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.batch_no,a.batch_date,a.party_id ,a.within_group, a.extention_no,a.remarks, a.style_ref_no";
	}
	else if ($db_type==2)
	{
		$sql=" select a.batch_no,a.batch_date ,a.party_id,a.within_group, a.color_id, a.machine_no, a.color_range_id, a.extention_no, a.batch_weight, a.process_id, listagg(cast(c.order_no as varchar2(4000)),',') within group (order by c.order_no) as po_number, a.total_trims_weight, a.style_ref_no, listagg(cast(c.job_no_mst as varchar2(4000)),',') within group (order by c.job_no_mst) as job_no_mst, listagg(cast(c.delivery_date as varchar2(4000)),',') within group (order by c.delivery_date) as delivery_date,a.remarks ,c.buyer_buyer,c.cust_style_ref from pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_dtls c where a.entry_form=281 and a.id=b.mst_id and b.po_id=c.id and a.id=$update_id  and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.batch_no,a.batch_date,a.party_id,a.within_group, a.color_id,a.machine_no, a.color_range_id, a.extention_no, a.batch_weight, a.process_id, a.total_trims_weight,a.remarks,c.buyer_buyer,c.cust_style_ref , a.style_ref_no";
	}
	//echo $sql; die;
	$dataArray=sql_select($sql);
	
	
	$ord_sql = "select b.id,a.subcon_job,a.aop_reference,a.delivery_date,a.party_id,a.within_group,b.order_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$company and a.entry_form=278 and  a.subcon_job=b.job_no_mst";
	
	$ordArray=sql_select( $ord_sql ); $po_arr=array(); $ref_arr=array(); $delDate_arr=array();
	foreach ($ordArray as $row)
	{
		//$po_arr[$row[csf('id')]] = $row[csf('order_no')];
		$ref_arr[$row[csf('id')]]['ref_no'] = $row[csf('aop_reference')];
		$delDate_arr[$row[csf('id')]]['del_date'] = $row[csf('delivery_date')];
		$delDate_arr[$row[csf('id')]]['party_id'] = $row[csf('party_id')];
		$delDate_arr[$row[csf('id')]]['within_group'] = $row[csf('within_group')];
	}
$order_no=''; $ref_no=''; $party_id=''; $within_group=''; $all_ref_arr=array();
	$order_id=array_unique(explode(",",$dataArray[0][csf('order_id')]));
	//print_r	($order_id);
	foreach($order_id as $val)
	{
		//echo $ref_arr[$val]['ref_no'];
		//if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
		$all_ref_arr[] .= $ref_arr[$val]['ref_no'];
		$delivery_date=array_unique(explode(",",$delDate_arr[$val]['del_date']));
		//$party_id=array_unique(explode(",",$delDate_arr[$val]['party_id']));
		//$within_group=array_unique(explode(",",$delDate_arr[$val]['within_group']));
	}
	//print_r($all_ref_arr);
	$ref_no = implode(",", array_unique($all_ref_arr));

	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number,a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		//$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);
	
	
?>
    <div style="width:930px">
    <div align="right"><strong>Printing Time: &nbsp;</strong> <? echo $date=date("F j, Y, g:i a"); ?> </div>
	<table width="930" cellspacing="0" align="center" border="0">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$company]; ?></strong></td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18"><strong><u> Batch Card</u></strong></td>
        </tr>
         <tr>
            <td colspan="6" align="left" style="font-size:16px;"><strong><u>Reference Details</u></strong></td>
        </tr>
        <tr>
            <td width="130"><strong>Batch No</strong></td><td width="250px"> : <? echo $dataArray[0][csf('batch_no')]; ?></td>
            <td width="110"><strong>Batch Serial</strong></td><td width="200px"> : <? echo $batch_sl_no; ?></td>
            <td width="110"><strong>Batch Color</strong></td><td width="150px"> : <? echo   $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
        </tr>
        <tr>
            <td width="130"><strong>Batch Weight</strong></td><td width="150"> : <? echo $dataArray[0][csf('batch_weight')];?></td>
            <td width="110"><strong>Party</strong></td>
            <td width="250"> : <? //$job_no_party=implode(",",array_unique(explode(",",$dataArray[0][csf('job_no_mst')]))); echo $buyer_arr[$job_buyer[$job_no_party]] ; 
            echo $buyer_arr[$dataArray[0][csf('party_id')]];
            ?>
            	
            </td>
            <td width="130"><strong>Job No</strong></td>
            <td width="200"> : <? 
			if($db_type==0)
			{
				$job_no=$dataArray[0][csf('job_no_mst')];
			}
			else if ($db_type==2)
			{
				$job_no=implode(",",array_unique(explode(",",$dataArray[0][csf('job_no_mst')])));
			}
			
			echo $job_no; ?>
				
			</td>
        </tr>
        <tr>
            <td width="130"><strong>Delivery Date</strong></td>
            <td width="150"> : <?
			$delivery_date=$dataArray[0][csf('delivery_date')];
			$delivery_date=array_unique(explode(",",$delivery_date));
			$ddate_con='';
			foreach($delivery_date as $ddate)
			{
				if($ddate_con=='') $ddate_con=change_date_format($ddate);else $ddate_con.=','.change_date_format($ddate);
			}
			echo $ddate_con;
			?>
            	
            </td>
            <td width="130" align="left"><strong>Work Order</strong></td>
            <td width="250"> : <? 
			if($db_type==0)
			{
				$po_no=$dataArray[0][csf('po_number')];
			}
			else if ($db_type==2)
			{
				$po_no=implode(",",array_unique(explode(",",$dataArray[0][csf('po_number')])));
			}
			echo $po_no; ?>
				
			</td>

			<td width="110" align="left"><strong>Machine No</strong></td>
			<td width="200"> : <? 
			
			echo $machine_library[$dataArray[0][csf('machine_no')]]; 
			?>
				
			</td>
        </tr>
        <tr>
        	<td><strong>Internal Ref.</strong>
        	<td width="150" id="ref_td"></td>
        	
        	<td><strong>Cust. Buyer:</strong>
        	</td><td id="buyer_td"></td>
        	<td width="110">Batch Date</td>
            <td  width="150">: <? echo change_date_format($dataArray[0][csf('batch_date')]);?></td>
       </tr>
       <tr>
        	
        	<td width="110"><strong>Buyer Job</strong></td>
            <td  width="150">: <? echo $buyer_po_arr[$dataArray[0][csf('buyer_po_id')]]['job'];?></td>
            <td width="110"><strong>Buyer PO</strong></td>
            <td >: <? echo $buyer_po_arr[$dataArray[0][csf('buyer_po_id')]]['po'];?></td>
            <td width="110"><strong>B. Style Ref </strong></td>
        	<td width="250">:<? 
			echo $dataArray[0][csf('style_ref_no')]; 
			?></td>
       </tr>
       <tr>
            <td width="110">AOP Ref.</td>
            <td >: <? echo $ref_no;?></td>
            <td>Remarks</td>
            <td colspan="3">: <? echo $dataArray[0][csf('remarks')];?></td>
       </tr>
       <tr>
       		<td width="110">&nbsp;</td>
            <td >&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="3">&nbsp;</td>
       </tr>
    </table>
    <div style="float:left; margin-left:10px;"><strong><u>Fabrication Details</u></strong> </div>
    <table align="center" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" style="font-size:12px">
       <thead bgcolor="#dddddd">
            <th width="30">SL</th>
            <th width="300">Const. & Comp.</th>
            <th width="70">GSM</th>
            <th width="90">Grey Dia/Width</th>
            <th width="90">Fin. Dia/Width</th>
            <th width="90">D/W Type</th>
            <th width="110">Grey Qty.</th>
            <th>Roll No.</th>
        </thead>
        <tbody>
		<?
			$i=1;
			$yarncount=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
			$yarn_lot_supp=return_library_array( "select lot, supplier_id from  product_details_master",'lot','supplier_id');
			$machine_lib_dia=return_library_array( "select id,dia_width from  lib_machine_name", "id", "dia_width"  );
			$machine_lib_gauge=return_library_array( "select id,gauge from  lib_machine_name", "id", "gauge"  );
			
			$yarn_dtls_arr=array();$mc_dia_gauge_arr=array();
			$yarn_lot_data=sql_select("select order_id, cons_comp_id, yarn_lot, yrn_count_id, machine_id from  subcon_production_dtls where product_type=2 and status_active=1 and is_deleted=0 and yarn_lot!='' group by cons_comp_id, order_id");
			foreach($yarn_lot_data as $rows)
			{
				$yarn_dtls_arr[$rows[csf('cons_comp_id')]][$rows['order_id']]['yarn_lot']=$rows[csf('yarn_lot')];//implode(",",array_unique($rows[csf('yarn_lot')]));
				$yarn_dtls_arr[$rows[csf('cons_comp_id')]][$rows['order_id']]['yarn_count']=$rows[csf('yarn_count')];
				$yarn_dtls_arr[$rows[csf('cons_comp_id')]][$rows['order_id']]['machine_no_id']=$rows[csf('machine_no_id')];
			}
			//var_dump($yarn_dtls_arr);
			$mc_dia_gauge_data=sql_select("select order_id, material_description, mc_dia, mc_gauge from sub_material_dtls where status_active=2 and is_deleted=0");
			foreach($mc_dia_gauge_data as $datas)
			{
				$mc_dia_gauge_arr[$datas[csf('order_id')]][$datas[csf('material_description')]]['mc_dia']=$datas[csf('mc_dia')];
				$mc_dia_gauge_arr[$datas[csf('order_id')]][$datas[csf('material_description')]]['mc_gauge']=$datas[csf('mc_gauge')];
			}
			$sql_dtls="select id, SUM(batch_qnty) AS batch_qnty, roll_no, item_description, fin_dia,gsm,grey_dia,buyer_po_id, po_id, prod_id, width_dia_type, rec_challan, fabric_from  from pro_batch_create_dtls where mst_id=$update_id and  status_active=1 and is_deleted=0 GROUP BY id, roll_no, item_description, fin_dia,buyer_po_id, po_id, prod_id, width_dia_type, rec_challan, gsm,grey_dia, fabric_from";
			//echo $sql_dtls;
			$sql_result=sql_select($sql_dtls); $aopRef=''; $internalRef='';	$buyerBuyer='';	
	
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($internalRef=='') $internalRef=$buyer_po_arr[$row[csf('buyer_po_id')]]['internalRef']; else $internalRef.=", ".$buyer_po_arr[$row[csf('buyer_po_id')]]['internalRef'];
				if($buyerBuyer=='') $buyerBuyer=$buyer_po_arr[$row[csf('buyer_po_id')]]['buyerBuyer']; else $buyerBuyer.=", ".$buyer_po_arr[$row[csf('buyer_po_id')]]['buyerBuyer'];
				
				$desc=explode(",",$row[csf('item_description')]); 
				$y_count=$yarn_dtls_arr[$rows[csf('prod_id')]][$rows['po_id']]['yarn_count'];
				$y_count_id=explode(',',$y_count);
				$yarn_count_value='';
				foreach ($y_count_id as $val)
				{
					if($yarn_count_value=='') $yarn_count_value=$yarncount[$val]; else $yarn_count_value.=", ".$yarncount[$val];
				
				}
				$yarn_lot_d=$yarn_dtls_arr[$rows[csf('prod_id')]][$rows['po_id']]['yarn_lot'];
				$exp_lot=explode(',',$yarn_lot_d);
					
				$machine_dia_up=$machine_lib_dia[$yarn_dtls_arr[$row[csf('prod_id')]][$row['po_id']]['machine_no_id']];
				$machine_gauge_up=$machine_lib_gauge[$yarn_dtls_arr[$row[csf('prod_id')]][$row['po_id']]['machine_no_id']];
				
				
				if($mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_gauge']!="")
				{
					$mc_dia_gauge_in=$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_dia'] .' X ' .$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_gauge'];
				}
				else
				{
					$mc_dia_gauge_in=$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_dia'];
				}
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="30"><? echo $i; ?></td>
					<td width="300" ><? echo $desc[0].",".$desc[1];//$desc[0]; ?></td>
					<td width="70" align="center"><? echo $desc[2];//$desc[1]; ?></td>
					<td width="90" align="center"><? echo $desc[3];//$desc[2]; ?></td>
					<td width="90"><? echo $desc[4]; ?></td>
					<td width="90" align="center"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
					<td width="110" align="right"><? echo number_format($row[csf('batch_qnty')],2); ?></td>
					<td align="center"><? echo $row[csf('roll_no')];  ?></td>
				</tr>
				<?php
			   $b_qty+= $row[csf('batch_qnty')];
				$i++;
			}
			$internalRef=implode(",",array_unique(explode(",",$internalRef)));
			$buyerBuyer=implode(",",array_unique(explode(",",$buyerBuyer)));
			?>
        </tbody>
        <tr>
            <td colspan="6" align="right"><b>Sum:</b></td>
            <td align="right" ><b><? echo number_format($b_qty,2); ?> </b></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="6" align="right"><b>Trims Weight:</b></td>
            <td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')],2); ?> </b></td>
            <td>&nbsp;</td>
        </tr>
         <tr>
            <td colspan="6" align="right"><b>Total:</b></td>
            <td align="right"><b><? echo number_format($b_qty+$dataArray[0][csf('total_trims_weight')],2); ?></b></td>
            <td>&nbsp;</td>
        </tr>
         <tr>
            <td colspan="9"  align="right">&nbsp; </td>
        </tr>
			<? 
                $process=$dataArray[0][csf('process_id')];
                $process_id=explode(',',$process);
                $process_value='';
                $i=1;
                foreach ($process_id as $val)
                {
                    if($process_value=='') $process_value=$i.'. '. $conversion_cost_head_array[$val]; else $process_value.=", ".$i.'. '.$conversion_cost_head_array[$val];
                    $i++;
                }
             ?>
            <tr>
                <th colspan="8" align="left" ><strong>Process Required</strong></th>
            </tr>
            <tr>
                <td colspan="8" title="<? echo $process_value; ?>"> <strong><? echo $process_value; ?> </strong></td>
            </tr>
            <tr>
                <td colspan="3" align="left">Heat Setting:</td>
                <td colspan="3" align="left">Loading Date:</td>
                <td colspan="2" align="left">UnLoading Date:</td>
            </tr>
        </table>
     <div style="float:left; margin-left:10px;"><strong> Quality Instruction(<i>Hand Written</i>)</strong> </div>
    <table width="930" cellspacing="0" align="center" >
        <tr>
            <td valign="top" align="left" width="440">
                <table cellspacing="0" width="430"  align="left" border="1" rules="all" class="rpt_table">
                    <tr>
                    	<th>SL</th><th>Roll No</th><th>Actual Dia</th><th>Roll Wgt.</th><th>Remarks</th>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                </table>
            </td>
            <td width="15" align="justify" valign="top">&nbsp;</td>
            <td width="440" valign="top" align="right">
                <table width="430"  cellspacing="0"  border="1" rules="all" class="rpt_table">
                    <tr>
                        <th>SL</th><th>Roll No</th><th>Actual Dia</th><th>Roll Wgt.</th><th>Remarks</th>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="440" valign="top">
                <table width="430" cellspacing="0" border="1" rules="all" class="rpt_table">
                    <tr>
                        <th align="left"><strong>Shade Result(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <td colspan="1" style="width:451px; height:80px" >&nbsp;</td>
                    </tr>
                </table>
        	</td>
            <td width="15" align="justify" valign="top">&nbsp;</td>
            <td width="440" valign="top" align="right">
                <table cellspacing="0" border="1" rules="all" class="rpt_table" width="428" >
                    <tr>
                        <th align="left" colspan="3"><strong>Shrinkage(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <th><b>Length % </b></th><th><b>Width % </b></th><th><b> Twist % </b></th>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="930" colspan="3">
                <table cellspacing="0" border="1" rules="all" class="rpt_table" width="930" >
                    <tr>
                        <th align="center"><strong>Other Information(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <td style="width:930px; height:120px" >&nbsp;</td>
                    </tr>
                </table> 
            </td>
        </tr>
    </table>
    <div style="width: 930px ; float: left;" align="left">
		<? 
		   	echo get_spacial_instruction($data[1],"100%",281);
		?>
	</div>
    <br>
		<?
            echo signature_table(281, $data[0], "930px");
        ?>
    </div>
    <script type="text/javascript">
     	document.getElementById("ref_td").innerHTML='<? echo ": ".$internalRef; ?>'
     	document.getElementById("buyer_td").innerHTML='<? echo ": ".$buyer_arrs[$buyerBuyer]; ?>'
     </script>
    <script type="text/javascript" src="../js/jquery.js"></script>
    <script type="text/javascript" src="../js/jquerybarcode.js"></script>
    <script>
		function generateBarcode( valuess )
		{
			//alert(valuess);
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
			$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $batch_mst_update_id; ?>');
	</script>
    <? 
}*/
if($action=="batch_card_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$update_id=$data[1];
	$batch_sl_no=$data[2];
	$batch_mst_update_id=str_pad($update_id,10,'0',STR_PAD_LEFT);
	//echo $data[3]; die;
	if($db_type==2) $machine_field="machine_no || '-' || brand as machine_no";
	else $machine_field="concat(machine_no,'-',brand) as machine_no";
	$buyer_arrs=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	$company_library=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name");
	$machine_library=return_library_array( "SELECT id, $machine_field from lib_machine_name where status_active =1 and is_deleted=0", "id", "machine_no");
	$job_buyer=return_library_array( "SELECT subcon_job, party_id from subcon_ord_mst where status_active =1 and is_deleted=0", "subcon_job", "party_id");
	//$grouping_arr=return_library_array( "select id, grouping from wo_po_break_down", "id", "grouping");
	
	$buyer_po_arr=array();
	$po_sql ="SELECT b.id,a.job_no,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['internalRef']=$row[csf("grouping")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);
	
	$ord_sql = "SELECT b.id,a.subcon_job,a.aop_reference,a.delivery_date,a.party_id,a.within_group,b.order_no from subcon_ord_mst a ,subcon_ord_dtls b where company_id =$company and a.entry_form=278 and  a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$order_arr = array();
	$embl_sql ="Select a.subcon_job, a.within_group,a.delivery_date, b.id,a.aop_reference, b.order_no, b.buyer_buyer, b.buyer_po_no, b.buyer_style_ref, b.print_type from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=278 and a.subcon_job=b.job_no_mst";
	$embl_sql_res=sql_select($embl_sql);
	foreach ($embl_sql_res as $row) 
	{
		$order_arr[$row[csf("id")]]['job']=$row[csf("subcon_job")];
		$order_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
		$order_arr[$row[csf("id")]]['ref']=$row[csf("aop_reference")];
		$order_arr[$row[csf("id")]]['delivery_date']=$row[csf("delivery_date")];
		$order_arr[$row[csf("id")]]['within_group']=$row[csf("within_group")];
		$order_arr[$row[csf("id")]]['buyer_buyer'] =$row[csf("buyer_buyer")];
		$order_arr[$row[csf("id")]]['buyer_po_no'] =$row[csf("buyer_po_no")];
		$order_arr[$row[csf("id")]]['buyer_style_ref'] =$row[csf("buyer_style_ref")];
		$order_arr[$row[csf("id")]]['print_type'] =$row[csf("print_type")];
	}
	unset($embl_sql_res);

	if($db_type==0)
	{
		$sql=" SELECT a.id, a.batch_no,a.batch_date,a.machine_no, a.color_id, a.color_range_id, a.extention_no, a.batch_weight, a.process_id,a.party_id,a.within_group,a.remarks,a.print_type,a.design_number,a.coverage,group_concat(distinct(,b.buyer_po_id)) AS buyer_po_id,group_concat(distinct(,b.po_id)) AS po_id 
		from pro_batch_create_mst a, pro_batch_create_dtls b 
		where a.entry_form=281 and a.id=b.mst_id and a.id=$update_id  and a.status_active=1 and a.is_deleted=0 
		group by a.id, a.batch_no,a.batch_date,a.machine_no, a.color_id, a.color_range_id, a.extention_no, a.batch_weight, a.process_id,a.party_id,a.within_group,a.remarks,a.print_type,a.design_number,a.coverage";
	}
	else if ($db_type==2)
	{
		$sql=" SELECT a.id, a.batch_no,a.batch_date,a.machine_no, a.color_id, a.color_range_id, a.extention_no, a.batch_weight,a.aop_batch_for, a.process_id,a.party_id,a.within_group,a.remarks,a.print_type,a.design_number,a.coverage ,listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id) as buyer_po_id ,listagg(b.po_id,',') within group (order by b.po_id) as po_id 
		from pro_batch_create_mst a, pro_batch_create_dtls b 
		where a.entry_form=281 and a.id=b.mst_id and a.id=$update_id  and a.status_active=1 and a.is_deleted=0 
		group by a.id, a.batch_no,a.batch_date,a.machine_no, a.color_id, a.color_range_id, a.extention_no, a.batch_weight,a.aop_batch_for, a.process_id,a.party_id,a.within_group,a.remarks,a.print_type,a.design_number,a.coverage";
	}
	$dataArray=sql_select($sql);
	$job_no=$po_no=$buyer_po_no=$buyer_style_ref=$within_group=$buyer_buyer=$aop_ref=$delivery_date=$internalRef=$buyer_job='';
	$order_id=array_unique(explode(",",$dataArray[0][csf('po_id')]));
	foreach($order_id as $val)
	{
		if($job_no=="") $job_no=$order_arr[$val]['job']; else $job_no.=", ".$order_arr[$val]['job'];
		if($po_no=="") $po_no=$order_arr[$val]['po']; else $po_no.=", ".$order_arr[$val]['po'];
		if($buyer_po_no=="") $buyer_po_no=$order_arr[$val]['buyer_po_no']; else $buyer_po_no.=", ".$order_arr[$val]['buyer_po_no'];
		if($buyer_style_ref=="") $buyer_style_ref=$order_arr[$val]['buyer_style_ref']; else $buyer_style_ref.=", ".$order_arr[$val]['buyer_style_ref'];
		//if($print_types=="") $print_types=$print_type[$order_arr[$val]['print_type']]; else $print_types.=", ".$print_type[$order_arr[$val]['print_type']];
		if($delivery_date=="") $delivery_date=change_date_format($order_arr[$val]['delivery_date']); else $delivery_date.=", ".change_date_format($order_arr[$val]['delivery_date']);
		if($aop_ref=="") $aop_ref=$order_arr[$val]['ref']; else $aop_ref.=", ".$order_arr[$val]['ref'];

		if($dataArray[0][csf('within_group')]==1) 
		{
			if($buyer_buyer=="") $buyer_buyer=$buyer_arrs[$order_arr[$val]['buyer_buyer']]; else $buyer_buyer.=", ".$buyer_arrs[$order_arr[$val]['buyer_buyer']];
		}
		else
		{
			if($buyer_buyer=="") $buyer_buyer=$order_arr[$val]['buyer_buyer']; else $buyer_buyer.=", ".$order_arr[$val]['buyer_buyer'];
		}
	}
	$buyer_po_id=array_unique(explode(",",$dataArray[0][csf('buyer_po_id')]));
	foreach($buyer_po_id as $val)
	{
		if($internalRef=="") $internalRef=$buyer_po_arr[$val]['internalRef']; else $internalRef.=", ".$buyer_po_arr[$val]['internalRef'];
		if($buyer_job=="") $buyer_job=$buyer_po_arr[$val]['job']; else $buyer_job.=", ".$buyer_po_arr[$val]['job'];

		if($buyer_job1=="") $buyer_job1="'".$buyer_po_arr[$val]['job']."'"; else $buyer_job1.=",'".$buyer_po_arr[$val]['job']."'";
	}

	$booking_sql ="Select booking_no from wo_booking_mst where status_active=1 and is_deleted=0 and fabric_source=1 and job_no in($buyer_job1) and company_id=$company and entry_form=118";

	$bookingArray=sql_select($booking_sql);

	$booking_no = "";

	foreach($bookingArray as $val)
	{
		if($booking_no=="") $booking_no=$val[csf('booking_no')]; else $booking_no.=", ".$val[csf('booking_no')];
	}

	$job_no = implode(",", array_unique(explode(", ",$job_no)));
	$po_no = implode(",", array_unique(explode(", ",$po_no)));
	$buyer_po_no = implode(",", array_unique(explode(", ",$buyer_po_no)));
	$buyer_style_ref = implode(",", array_unique(explode(", ",$buyer_style_ref)));
	//$print_types = implode(",", array_unique(explode(", ",$print_types)));
	$buyer_buyer = implode(",", array_unique(explode(", ",$buyer_buyer)));
	$aop_ref = implode(",", array_unique(explode(", ",$aop_ref)));
	$delivery_date = implode(",", array_unique(explode(", ",$delivery_date)));
	$internalRef = implode(",", array_unique(explode(", ",$internalRef)));
	$buyer_job = implode(",", array_unique(explode(", ",$buyer_job)));
	?>
    <div style="width:930px">
    <div style="position:relative;" align="right"><strong>Printing Time: &nbsp;</strong> <? echo $date=date("F j, Y, g:i a"); ?>
    	<div  style="position: absolute; top:35px; left:760px;" id="barcode_img_id"></div>
	</div>
	<table width="930" cellspacing="0" align="center" border="0">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$company]; ?></strong></td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18"><strong><u> Batch Card</u></strong></td>
        </tr>
         <tr>
            <td colspan="6" align="left" style="font-size:16px;"><strong><u>Reference Details</u></strong></td>
        </tr>
        <tr>
            <td width="110"><strong>Batch No</strong></td><td width="250px"> : <? echo $dataArray[0][csf('batch_no')]; ?></td>
            <td width="110"><strong>Batch Serial</strong></td><td width="200px"> : <? echo $batch_sl_no; ?></td>
            <td width="110"><strong>Batch Color</strong></td><td width="150px"> : <? echo   $color_arr[$dataArray[0][csf('color_id')]]; ?></td>
        </tr>
        <tr>
            <td width="110"><strong>Batch Weight</strong></td><td width="150"> : <? echo $dataArray[0][csf('batch_weight')];?></td>
            <td width="110"><strong>Party</strong></td>
            <td width="250"> : <? if($dataArray[0][csf('within_group')]==1) echo $company_library[$dataArray[0][csf('party_id')]]; else echo $buyer_arrs[$dataArray[0][csf('party_id')]];?></td>
            <td width="110"><strong>AOP Job No.</strong></td>
            <td width="200"> : <? echo $job_no; ?></td>
        </tr>
        <tr>
            <td width="110"><strong>Delivery Date</strong></td>
            <td width="150"> : <? echo $delivery_date; ?></td>
            <td width="110" align="left"><strong>Work Order</strong></td>
            <td width="250"> : <? echo $po_no; ?></td>
			<td width="110" align="left"><strong>Machine No</strong></td>
			<td width="200"> : <? echo $machine_library[$dataArray[0][csf('machine_no')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Internal Ref.</strong>
        	<td width="150" id="ref_td">: <? echo $internalRef; ?></td>
        	<td><strong>Cust. Buyer</strong>
        	</td><td id="buyer_td">: <? echo $buyer_buyer; ?></td>
        	<td width="110"><strong>Batch Date</strong>  </td>
            <td  width="150">: <? echo change_date_format($dataArray[0][csf('batch_date')]);?></td>
       </tr>
        <tr>
        	
        	<td width="110"><strong>Buyer Job</strong></td>
            <td  width="150">: <? echo $buyer_job;?></td>
            <td width="110"><strong>Buyer PO</strong></td>
            <td >: <? echo $buyer_po_no;?></td>
            <td width="110"><strong>B. Style Ref </strong></td>
        	<td width="250">: <? echo $buyer_style_ref; ?></td>
       </tr>
       <tr>
            <td width="110"><strong>AOP Ref.</strong> </td>
            <td >: <? echo $aop_ref;?></td>
       		<td width="110"><strong>Batch For</strong> </td>
            <td>: <?  $aop_batch_for = array(1 =>"Bulk",2 =>"Sample"); 
			echo $aop_batch_for[$dataArray[0][csf('aop_batch_for')]];?></td>
			<td style="vertical-align:top;" width="110"><strong>Fb. Booking</strong> </td>
            <td>: <? echo $booking_no;?></td>
            
       </tr>  
       <tr>
       		<td width="110"><strong>Print Type</strong></td>
        	<td width="250">: <? echo $dataArray[0][csf('print_type')]; ?></td>
       		<td width="110"><strong>Design No</strong> </td>
            <td>: <? echo $dataArray[0][csf('design_number')];?></td>            
            <td style="vertical-align:top;" width="110"><strong>Coverage</strong> </td>
            <td>: <? echo $dataArray[0][csf('coverage')];?></td>
       </tr>
       <tr>
       		<td width="110"><strong>Remarks</strong> </td>
            <td>: <? echo $dataArray[0][csf('remarks')];?></td>
       		
       </tr>
       <tr><td>&nbsp;</td></tr>
    </table>
    <div style="float:left; margin-left:10px;"><strong><u>Fabrication Details</u></strong> </div>
    <table align="center" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" style="font-size:12px">
       <thead bgcolor="#dddddd">
            <th width="30">SL</th>
            <th width="300">Const. & Comp.</th>
            <th width="70">GSM</th>
            <th width="90">Grey Dia/Width</th>
            <th width="90">Fin. Dia/Width</th>
            <th width="90">D/W Type</th>
            <th width="110">Grey Qty.</th>
            <th>Roll No.</th>
        </thead>
        <tbody>
		<?
			$i=1;
			$yarncount=return_library_array( "SELECT id, yarn_count from  lib_yarn_count where status_active =1 and is_deleted=0",'id','yarn_count');
			$yarn_lot_supp=return_library_array( "SELECT lot, supplier_id from  product_details_master where status_active =1 and is_deleted=0",'lot','supplier_id');
			$machine_lib_dia=return_library_array( "SELECT id,dia_width from  lib_machine_name where status_active =1 and is_deleted=0", "id", "dia_width"  );
			$machine_lib_gauge=return_library_array( "SELECT id,gauge from  lib_machine_name where status_active =1 and is_deleted=0", "id", "gauge"  );
			
			$yarn_dtls_arr=array();$mc_dia_gauge_arr=array();
			$yarn_lot_data=sql_select("SELECT order_id, cons_comp_id, yarn_lot, yrn_count_id, machine_id from  subcon_production_dtls where product_type=2 and status_active=1 and is_deleted=0 and yarn_lot!='' group by cons_comp_id, order_id");
			foreach($yarn_lot_data as $rows)
			{
				$yarn_dtls_arr[$rows[csf('cons_comp_id')]][$rows['order_id']]['yarn_lot']=$rows[csf('yarn_lot')];//implode(",",array_unique($rows[csf('yarn_lot')]));
				$yarn_dtls_arr[$rows[csf('cons_comp_id')]][$rows['order_id']]['yarn_count']=$rows[csf('yarn_count')];
				$yarn_dtls_arr[$rows[csf('cons_comp_id')]][$rows['order_id']]['machine_no_id']=$rows[csf('machine_no_id')];
			}
			//var_dump($yarn_dtls_arr);
			$mc_dia_gauge_data=sql_select("SELECT order_id, material_description, mc_dia, mc_gauge from sub_material_dtls where status_active=2 and is_deleted=0");
			foreach($mc_dia_gauge_data as $datas)
			{
				$mc_dia_gauge_arr[$datas[csf('order_id')]][$datas[csf('material_description')]]['mc_dia']=$datas[csf('mc_dia')];
				$mc_dia_gauge_arr[$datas[csf('order_id')]][$datas[csf('material_description')]]['mc_gauge']=$datas[csf('mc_gauge')];
			}
			
			$sql_dtls="SELECT id, SUM(batch_qnty) AS batch_qnty, roll_no, item_description, fin_dia,gsm,grey_dia, po_id, prod_id, width_dia_type, rec_challan, fabric_from, buyer_po_id  from pro_batch_create_dtls where mst_id=$update_id and  status_active=1 and is_deleted=0 GROUP BY id, roll_no, item_description, fin_dia, po_id, prod_id, width_dia_type, rec_challan, gsm,grey_dia, fabric_from,buyer_po_id";
			//echo $sql_dtls;
			$sql_result=sql_select($sql_dtls);	
			$aopRef=''; $internalRef='';	$buyerBuyer='';	
	
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//echo $row[csf('buyer_po_id')]."===";
				//if($internalRef=='') $internalRef=$buyer_po_arr[$row[csf('buyer_po_id')]]['internalRef']; else $internalRef.=", ".$buyer_po_arr[$row[csf('buyer_po_id')]]['internalRef'];
				//if($buyerBuyer=='') $buyerBuyer=$buyer_po_arr[$row[csf('buyer_po_id')]]['buyerBuyer']; else $buyerBuyer.=", ".$buyer_po_arr[$row[csf('buyer_po_id')]]['buyerBuyer'];
				
				$desc=explode(",",$row[csf('item_description')]); 
				$y_count=$yarn_dtls_arr[$rows[csf('prod_id')]][$rows['po_id']]['yarn_count'];
				$y_count_id=explode(',',$y_count);
				$yarn_count_value='';
				foreach ($y_count_id as $val)
				{
					if($yarn_count_value=='') $yarn_count_value=$yarncount[$val]; else $yarn_count_value.=", ".$yarncount[$val];
				
				}
				$yarn_lot_d=$yarn_dtls_arr[$rows[csf('prod_id')]][$rows['po_id']]['yarn_lot'];
				$exp_lot=explode(',',$yarn_lot_d);
					
				$machine_dia_up=$machine_lib_dia[$yarn_dtls_arr[$row[csf('prod_id')]][$row['po_id']]['machine_no_id']];
				$machine_gauge_up=$machine_lib_gauge[$yarn_dtls_arr[$row[csf('prod_id')]][$row['po_id']]['machine_no_id']];
				
				
				if($mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_gauge']!="")
				{
					$mc_dia_gauge_in=$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_dia'] .' X ' .$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_gauge'];
				}
				else
				{
					$mc_dia_gauge_in=$mc_dia_gauge_arr[$row[csf('po_id')]][$row[csf('item_description')]]['mc_dia'];
				}
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="30"><? echo $i; ?></td>
					<td width="300" ><? echo $desc[0].",".$desc[1];//$desc[0]; ?></td>
					<td width="70" align="center"><? echo $desc[2];//$desc[1]; ?></td>
					<td width="90" align="center"><? echo $desc[3];//$desc[2]; ?></td>
					<td width="90"><? echo $desc[4]; ?></td>
					<td width="90" align="center"><? echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
					<td width="110" align="right"><? echo number_format($row[csf('batch_qnty')],2); ?></td>
					<td align="center"><? echo $row[csf('roll_no')];  ?></td>
				</tr>
				<?php
			   $b_qty+= $row[csf('batch_qnty')];
				$i++;
			}
			//echo $internalRef."====";
			//$internalRef=implode(",",array_unique(explode(", ",$internalRef)));
			//$buyerBuyer=implode(",",array_unique(explode(", ",$buyerBuyer)));
			?>
        </tbody>
        <tr>
            <td colspan="6" align="right"><b>Sum:</b></td>
            <td align="right" ><b><? echo number_format($b_qty,2); ?> </b></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="6" align="right"><b>Trims Weight:</b></td>
            <td align="right"><b><? echo number_format($dataArray[0][csf('total_trims_weight')],2); ?> </b></td>
            <td>&nbsp;</td>
        </tr>
         <tr>
            <td colspan="6" align="right"><b>Total:</b></td>
            <td align="right"><b><? echo number_format($b_qty+$dataArray[0][csf('total_trims_weight')],2); ?></b></td>
            <td>&nbsp;</td>
        </tr>
         <tr>
            <td colspan="9"  align="right">&nbsp; </td>
        </tr>
			<? 
                $process=$dataArray[0][csf('process_id')];
                $process_id=explode(',',$process);
                $process_value='';
                $i=1;
                foreach ($process_id as $val)
                {
                    if($process_value=='') $process_value=$i.'. '. $conversion_cost_head_array[$val]; else $process_value.=", ".$i.'. '.$conversion_cost_head_array[$val];
                    $i++;
                }
             ?>
            <tr>
                <th colspan="8" align="left" ><strong>Process Required</strong></th>
            </tr>
            <tr>
                <td colspan="8" title="<? echo $process_value; ?>"> <strong><? echo $process_value; ?> </strong></td>
            </tr>
        </table><br>
     <div style="float:left; margin-left:10px;"><strong> Quality Instruction(<i>Hand Written</i>)</strong> </div>
    <table width="930" cellspacing="0" align="center" >
        <tr>
            <td valign="top" align="left" width="440">
                <table cellspacing="0" width="430"  align="left" border="1" rules="all" class="rpt_table">
                    <tr>
                    	<th>SL</th><th>Roll No</th><th>Actual Dia</th><th>Roll Wgt.</th><th>Remarks</th>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                </table>
            </td>
            <td width="15" align="justify" valign="top">&nbsp;</td>
            <td width="440" valign="top" align="right">
                <table width="430"  cellspacing="0"  border="1" rules="all" class="rpt_table">
                    <tr>
                        <th>SL</th><th>Roll No</th><th>Actual Dia</th><th>Roll Wgt.</th><th>Remarks</th>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="440" valign="top">
                <table width="430" cellspacing="0" border="1" rules="all" class="rpt_table">
                    <tr>
                        <th align="left"><strong>Shade Result(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <td colspan="1" style="width:451px; height:80px" >&nbsp;</td>
                    </tr>
                </table>
        	</td>
            <td width="15" align="justify" valign="top">&nbsp;</td>
            <td width="440" valign="top" align="right">
                <table cellspacing="0" border="1" rules="all" class="rpt_table" width="428" >
                    <tr>
                        <th align="left" colspan="3"><strong>Shrinkage(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <th><b>Length % </b></th><th><b>Width % </b></th><th><b> Twist % </b></th>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                    <tr>
                        <td>&nbsp; </td><td>&nbsp; </td><td>&nbsp; </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="930" colspan="3">
                <table cellspacing="0" border="1" rules="all" class="rpt_table" width="930" >
                    <tr>
                        <th align="center"><strong>Other Information(<i>Hand Written</i>)</strong></th>
                    </tr>
                    <tr>
                        <td style="width:930px; height:120px" >&nbsp;</td>
                    </tr>
                </table> 
            </td>
        </tr>
    </table>
    <div style="width: 930px ; float: left;" align="left">
		<? 
		   	echo get_spacial_instruction($batch_sl_no,"100%",281);
		?>
	</div>
    <br>
		<?
            echo signature_table(162, $data[0], "930px");
        ?>
    </div>
   <!--  <script type="text/javascript">
    	document.getElementById("ref_td").innerHTML='<?// echo ": ".$internalRef; ?>'
    	document.getElementById("buyer_td").innerHTML='<? //echo ": ".$buyer_arrs[$buyerBuyer]; ?>'
   </script> -->
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
		function generateBarcode( valuess )
		{
			//alert(valuess);
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
			$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $batch_mst_update_id; ?>');
	</script>
    <? 
}
if( $action == "batch_color_popup"){
	echo load_html_head_contents("Color Name Info","../../../", 1, 1, '','1','');
    extract($_REQUEST);
    ?>
    <script>

    function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function set_all()
	{
		var old=document.getElementById('txt_row_id').value;
		if(old!="")
		{
			old=old.split(",");
			for(var i=0; i<old.length; i++)
			{
				js_set_value( old[i] )
			}
		}
	}

	var color_arr = new Array();
	var selected_color_id = new Array;
	var selected_order_no = new Array;
	var selected_job_no = new Array;
	var selected_po_no = new Array;
	var selected_po_dtls_id = new Array;
	var selected_po_id = new Array;
	var color_ids=0;
    function js_set_value( strParam )
	{
		//alert(strParam);
		var splitArr = strParam.split("_");
		var str = splitArr[0];
		var po_dtls_id = splitArr[1];
		var buyer_po_id = splitArr[2]; 
		var aop_color_id = splitArr[3]; 
		var po_no = splitArr[4]; 
		var order_no = splitArr[5]; 
		var job_no = splitArr[6]; 
		var print_type_names = splitArr[8]; 
		var aop_type_names = splitArr[9]; 
		//var any_selected = $('#txt_selected_job').val();
		var any_selected = $('#txt_selected').val();
		//alert(buyer_po_id+'=='+po_dtls_id+'=='+aop_color_id);
		if(any_selected=="")
		{
			selected_po_id = [];
			selected_po_dtls_id = [];
			selected_color_id = [];
			selected_po_no = [];
			selected_order_no = [];
			selected_job_no = [];
		}
		if( jQuery.inArray( job_no, selected_job_no )==-1 &&  selected_job_no.length>0)
		{
			alert("Job No Mixed is Not Allow");
			return;
		}

		if( jQuery.inArray( aop_color_id, selected_color_id )==-1 &&  selected_color_id.length>0)
		{
			alert("Color Mixed is Not Allow");
			return;
		}

		toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
		if( jQuery.inArray( po_dtls_id, selected_po_dtls_id ) == -1 ) 
		{
			//alert(job_no+'i');
			selected_job_no.push( job_no );
			selected_po_dtls_id.push(po_dtls_id);
			selected_po_id.push( buyer_po_id );
			selected_color_id.push( aop_color_id );
		}
		else 
		{
			//alert(job_no+'m');
			for( var i = 0; i < selected_po_dtls_id.length; i++ ) 
			{
				if( selected_po_dtls_id[i] == po_dtls_id ) break;
			}
			selected_job_no.splice( i, 1 );
			selected_po_dtls_id.splice( i, 1 );
			selected_po_id.splice( i, 1 );
			selected_color_id.splice( i, 1 );

			//selected_po_dtls_id.push(po_dtls_id);
			//selected_po_id.push( buyer_po_id );
		}

		var poDtls_id =''; var po_id = ''; 
		for( var i = 0; i < selected_po_dtls_id.length; i++ ) 
		{
			poDtls_id += selected_po_dtls_id[i] + ',';
			po_id += selected_po_id[i] + ',';
			//alert(poDtls_id);
		}
		//alert(po_id+'n');
		poDtls_id = poDtls_id.substr( 0, poDtls_id.length - 1 );
		po_id = po_id.substr( 0, po_id.length - 1 );

		$('#txt_selected_buyer_po_id').val( po_id );
		$('#txt_selected_po_dtls_id').val( poDtls_id );
		$('#txt_selected_color_id').val( aop_color_id );
		$('#txt_selected_job').val( job_no );
		$('#txt_selected').val( order_no );
		$('#txt_print_type_names').val( print_type_names );
		$('#txt_aop_type_names').val(aop_type_names);
 	}

    function change_caption(type)
	{
		if(type==1) $('#td_search').html('Enter Work Order NO');
		else if(type==2) $('#td_search').html('Enter Buyer JOB NO.');
		else if(type==3) $('#td_search').html('Enter Buyer PO');
		else if(type==4) $('#td_search').html('Enter Buyer Style Ref.');
		else if(type==5) $('#td_search').html('AOP Job No');
		else if(type==6) $('#td_search').html('Internal Ref');
	}

	function fnc_load_party_order_popup(company,within_group)
	{ 
		load_drop_down( 'aop_batch_creation_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		//$('#cbo_party_name').attr('disabled',true);
	}
	function load_arr(within_group){
		load_drop_down( 'aop_batch_creation_controller', within_group, 'load_drop_down_search', 'search_td' );
	}
    </script>        
    <body onLoad="fnc_load_party_order_popup(<? echo $company_id;?>,<? echo 1;?>)">
	<fieldset style="width:880px;margin-left:10px">
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table align="center" cellpadding="0" cellspacing="0" border="1" rules="all" width="890" class="rpt_table" align="center">
                <thead>
                	<tr>
                		<td style="display: none;">
                    	<? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?>
                    	</td>
                	</tr>
                    <tr>
                    	<th>Within Group</th>
						<th>Party Name</th>
						<th>Search By</th> 
                        <th id="td_search">Work Order NO</th>
                        <th>Design No</th>
                        <th>AOP Ref.</th>
                        <th colspan="2">Date range</th>
                        <th>
                            <input type="hidden" name="txt_selected_buyer_po_id" id="txt_selected_buyer_po_id" value="">
                            <input type="hidden" name="txt_selected_po_dtls_id" id="txt_selected_po_dtls_id" value="">
                            <input type="hidden" name="txt_selected_color_id" id="txt_selected_color_id" value="">
                            <input type="hidden" name="txt_selected_job" id="txt_selected_job" value="">
                            <input type="hidden" name="txt_selected" id="txt_selected" value="">
                            <input type="hidden" name="txt_print_type_names" id="txt_print_type_names" value="">
                            <input type="hidden" name="txt_aop_type_names" id="txt_aop_type_names" value="">
                        </th>
                    </tr>
                </thead>
                <tr class="general">
                	<td align="center" style="display: none;">				
                        <? echo create_drop_down( "cbo_process_name", 90, $production_process,"", 1, "--Select Process--",0,"", "","" ); ?>
                    </td>
                    <td>
						<?php echo create_drop_down( "cbo_within_group", 90, $yes_no,"", 0, "--  --", 0, "fnc_load_party_order_popup($company_id,this.value),load_arr(this.value)"); ?>
					</td> 
					<td id="buyer_td">
					 	<? 
						$sql_buyer="SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id'  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name";
						
						echo create_drop_down( "cbo_party_name", 170, $sql_buyer,"id,buyer_name", 1, "-- Select Party --", $selected, "" );
				 		?>
					</td>
					<td id="search_td"> 
                        <?
							$search_by=array(1=>'Work Order',2=>'Buyer JOB NO.',3=>'Buyer PO',4=>'Buyer Style Ref.',5=>"AOP Job No",6=>"Internal Ref");
							//$search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_search_by", 110, $search_by,"", 0, "--Select--", 1, "change_caption(this.value);",0 );
                        ?>
                    </td>
                    <td align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_search_comm" id="txt_search_comm"/>	
                    </td> 
                    <td align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_design_no" id="txt_design_no"/>	
                    </td>
                    <td align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_aop_ref" id="txt_aop_ref"/>	
                    </td>						
                    <td align="center">				
                        <input type="text" style="width:70px" class="text_boxes datepicker "  name="txt_date_from" id="txt_date_from"  />	
                    </td> 
                    <td align="center">		
                        <input type="text" style="width:70px" class="text_boxes datepicker"  name="txt_date_to" id="txt_date_to"  />	
                    </td> 
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_comm').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('cbo_within_group').value+'_'+'<? echo $hidden_po_dtls_id; ?>'+'_'+document.getElementById('txt_aop_ref').value+'_'+document.getElementById('txt_design_no').value, 'create_batch_color_search_list_view', 'search_div', 'aop_batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);set_all();')" style="width:70px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="9" align="center" valign="middle">
						<? echo load_month_buttons(1);  ?>
                    </td>
                </tr>
            </table>
            <div id="search_div" style="margin-top:10px"></div>   
        </form>
    </fieldset>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	//set_all();
</script>
</html>
<?
exit();
}
if( $action== "create_batch_color_search_list_view")
{
	// echo $data; die;
    $data=explode('_',$data);
	$search_by=$data[0];
	$company_id =$data[2];
	$form_date =$data[3];
	$to_date =$data[4];
	$party_id =$data[6];
	$within_group =$data[7];
	$poDtlsId =$data[8];
	$poDtlsId=explode(",",$poDtlsId);
	$search_str=trim(str_replace("'","",$data[1]));
	$aop_ref=trim(str_replace("'","",$data[9]));
	$design_no=trim(str_replace("'","",$data[10]));

	if($design_no!='') $design_no_cond=" and b.design_no like '$design_no%'"; else $design_no_cond="";

	if($party_id!=0) $party_id_cond="and a.party_id=$party_id";else $party_id_cond="";
	
    $sql_cond = ""; $job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond=""; $aop_job_cond="";$internal_ref="";
    if($search_by==1 && $search_str !='') $search_com_cond="and a.order_no like '%$search_str%'"; 
    if ($search_by==2 && $search_str !='') $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
	else if ($search_by==3 && $search_str !='') $po_cond=" and b.po_number like '%$search_str%'";
	else if ($search_by==4 && $search_str !='') $style_cond=" and a.style_ref_no like '%$search_str%'";
	else if ($search_by==5 && $search_str !='') $aop_job_cond=" and a.job_no_prefix_num like '%$search_str%'";
	else if ($search_by==6 && $search_str !='') $internal_ref=" and b.grouping like '%$search_str%' ";
	if ($aop_ref!="") $aop_cond=" and a.aop_reference like '%$aop_ref%'";
		
	$po_ids=''; $buyer_po_arr=array();
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id) as id";
		else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";

		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==2) || ($po_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==4) || ($internal_ref!="" && $search_by==6))
		{
			$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond $internal_ref and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
		}
		//echo $po_ids;
		if($db_type==2 && $po_ids!="") $po_ids = $po_ids->load();
		if ($po_ids!="")
		{
			$po_ids=explode(",",$po_ids);
			$po_idsCond=""; $poIdsCond="";
			//echo count($po_ids); die;
			if($db_type==2 && count($po_ids)>=999)
			{
				$chunk_arr=array_chunk($po_ids,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",",$val);
					if($po_idsCond=="")
					{
						$po_idsCond.=" and ( b.buyer_po_id in ( $ids) ";
						$poIdsCond.=" and ( b.id in ( $ids) ";
					}
					else
					{
						$po_idsCond.=" or  b.buyer_po_id in ( $ids) ";
						$poIdsCond.=" or  b.id in ( $ids) ";
					}
				}
				$po_idsCond.=")";
				$poIdsCond.=")";
			}
			else
			{
				$ids=implode(",",$po_ids);
				$po_idsCond.=" and b.buyer_po_id in ($ids) ";
				$poIdsCond.=" and b.id in ($ids) ";
			}
		}
		else if($po_ids=="" && ($job_cond!="" && $search_by==2) || ($po_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==4) || ($internal_ref!="" && $search_by==6))
		{
			die;
			//$po_idsCond.=" and b.buyer_po_id in ($ids) ";
		}
		
		$po_sql ="SELECT a.style_ref_no,a.job_no_prefix_num, b.grouping, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $poIdsCond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		// echo $po_sql;
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
			$buyer_po_arr[$row[csf("id")]]['int_ref']=$row[csf("grouping")];
			$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
		}
		unset($po_sql_res);
	}
		
	//if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

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
		$party_arr=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	}
	$color_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name" );
	
	unset($po_sql_res);


	$sql= "SELECT a.subcon_job, a.aop_reference, b.id as po_dtls_id, b.order_no,  b.buyer_po_id, b.aop_color_id,b.buyer_style_ref,b.buyer_po_no, b.design_no, b.print_type, b.booking_dtls_id
	from subcon_ord_mst a, subcon_ord_dtls b 
	where a.subcon_job=b.job_no_mst and a.entry_form=278 and a.company_id=$company_id and a.within_group=$within_group and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_rcv_date  $search_com_cond  $party_id_cond $po_idsCond $aop_cond $aop_job_cond $design_no_cond 
	group by a.subcon_job, a.aop_reference,b.id, b.order_no,  b.buyer_po_id, b.aop_color_id,b.buyer_style_ref,b.buyer_po_no, b.design_no, b.print_type, b.booking_dtls_id order by b.id DESC";
	// echo $sql;
	$nameArray=sql_select( $sql ); 
	foreach ($nameArray as $row)
	{
		$booking_dtls_id_arr[$row[csf("booking_dtls_id")]]=$row[csf("booking_dtls_id")];
	}

	if (!empty($booking_dtls_id_arr)) 
	{
		$booking_dtls_id = implode(",", $booking_dtls_id_arr);
        if($db_type==2 && count($booking_dtls_id_arr)>999)
        {
            $booking_dtls_id_arr_chunk=array_chunk($booking_dtls_id_arr,999) ;
            $booking_dtls_id_cond = " and (";

            foreach($booking_dtls_id_arr_chunk as $chunk_arr)
            {
                $booking_dtls_id_cond.=" id in(".implode(",",$chunk_arr).") or ";
            }

            $booking_dtls_id_cond = chop($booking_dtls_id_cond,"or ");
            $booking_dtls_id_cond .=")";
        }
        else
        {
            $booking_dtls_id_cond=" and id in($booking_dtls_id)";
        }

        $service_booking_sql="SELECT id, aop_type from WO_BOOKING_DTLS where ENTRY_FORM_ID=162 and BOOKING_TYPE=3 and IS_SHORT=2 $booking_dtls_id_cond and STATUS_ACTIVE=1 and IS_DELETED=0";
        // echo $service_booking_sql;
		$service_booking_sql_data=sql_select( $service_booking_sql ); 
		foreach ($service_booking_sql_data as $key => $row) 
		{
			$aop_type_data_arr[$row[csf("id")]]=$row[csf("aop_type")];
		}	
	}

	$data_arr=array();
	foreach ($nameArray as $row)
	{
		$aop_type=$aop_type_data_arr[$row[csf("booking_dtls_id")]];
		$data_arr[$row[csf("po_dtls_id")]][$row[csf("buyer_po_id")]][$row[csf("aop_color_id")]]['subcon_job']=$row[csf("subcon_job")];
		$data_arr[$row[csf("po_dtls_id")]][$row[csf("buyer_po_id")]][$row[csf("aop_color_id")]]['aop_reference']=$row[csf("aop_reference")];
		$data_arr[$row[csf("po_dtls_id")]][$row[csf("buyer_po_id")]][$row[csf("aop_color_id")]]['order_no']=$row[csf("order_no")];
		$data_arr[$row[csf("po_dtls_id")]][$row[csf("buyer_po_id")]][$row[csf("aop_color_id")]]['buyer_po_no']=$row[csf("buyer_po_no")];
		$data_arr[$row[csf("po_dtls_id")]][$row[csf("buyer_po_id")]][$row[csf("aop_color_id")]]['buyer_style_ref']=$row[csf("buyer_style_ref")];
		$data_arr[$row[csf("po_dtls_id")]][$row[csf("buyer_po_id")]][$row[csf("aop_color_id")]]['design_no']=$row[csf("design_no")];
		//$data_arr[$row[csf("po_dtls_id")]][$row[csf("buyer_po_id")]][$row[csf("aop_color_id")]]['body_part']=$row[csf("body_part")];
		//$data_arr[$row[csf("po_dtls_id")]][$row[csf("buyer_po_id")]][$row[csf("aop_color_id")]]['order_quantity']=$row[csf("order_quantity")];
		$data_arr[$row[csf("po_dtls_id")]][$row[csf("buyer_po_id")]][$row[csf("aop_color_id")]]['print_type'].=$row[csf("print_type")].',';
		$data_arr[$row[csf("po_dtls_id")]][$row[csf("buyer_po_id")]][$row[csf("aop_color_id")]]['aop_type'].=$aop_type.',';
	}
    ?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="100">Work Order No</th>
                <th width="100">Design No</th>
                <th width="100">Job No</th>
                <th width="100">Buyer Job No</th>
                <th width="100">Buyer PO</th>
                <th width="100">Internal Ref.</th>
                <th width="100">Buyer Style Ref.</th>
                <th width="100">Aop Ref.</th>
                <th width="">Color</th>
            </thead>
        </table>
        <div style="width:950px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table" id="tbl_list_search" >
            <?
            $i=1;
            foreach ($data_arr as $po_dtls_id=>$po_dtls_id_val)
            {
            	foreach ($po_dtls_id_val as $buyer_po_id=>$buyer_po_id_val)
            	{
            		foreach ($buyer_po_id_val as $aop_color_id=>$aop_color_id_val)
            		{
            			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

            			$print_type_id=$aop_color_id_val['print_type'];
            			$print_type_id_arr=array_unique(explode(",", chop($print_type_id,",")));
            			$print_type_name="";
            			foreach ($print_type_id_arr as $key => $value) 
            			{
            				//$print_type_name .= ($print_type_name =="") ? $print_type[$value] :  ",". $print_type[$value];
            				$print_type_name.=$print_type[$value].",";
            			}
            			$print_type_name = chop($print_type_name,",");
            			//echo $print_type_name.'<br>';

            			$aop_type_id=$aop_color_id_val['aop_type'];
            			$aop_type_id_arr=array_unique(explode(",", chop($aop_type_id,",")));
            			$aop_type_name="";
            			foreach ($aop_type_id_arr as $key => $value) 
            			{
            				$aop_type_name.=$print_type[$value].",";
            			}
            			

            			if($within_group==1)
						{
							$po_no=$buyer_po_arr[$buyer_po_id]['po'];
							$style_no=$buyer_po_arr[$buyer_po_id]['style'];
							$int_ref=$buyer_po_arr[$buyer_po_id]['int_ref'];
							$aop_type_name = chop($aop_type_name,",");
						}
						else
						{
							$po_no=$aop_color_id_val['buyer_po_no'];
							$style_no=$aop_color_id_val['buyer_style_ref'];
							$int_ref="";
							$aop_type_name="";
						}
            			

            			$data = $i . "_" . $po_dtls_id. "_" . $buyer_po_id . "_" . $aop_color_id . "_" . $buyer_po_arr[$buyer_po_id]['po']. "_" . $aop_color_id_val['order_no']. "_" . $buyer_po_arr[$buyer_po_id]['job']. "_" . $aop_color_id_val['aop_reference']. "_" . $print_type_name. "_" . $aop_type_name;

        				if(in_array($po_dtls_id,$poDtlsId))
						{
							//echo "==+".$data;
							if($row_id=="") $row_id=$data; else $row_id.=",".$data;
						}

						

            			//if(in_array($po_dtls_id, $poDtlsId)) $bgcolor="yellow"; else $bgcolor=$bgcolor;
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $data; ?>')">
	                        <td width="30" align="center"><? echo $po_dtls_id; ?></td>
	                        <td width="100" align="center"><p><? echo $aop_color_id_val['order_no']; ?></p></td>
	                        <td width="100" align="center"><p><? echo $aop_color_id_val['design_no']; ?></p></td>
	                        <td width="100" align="center"><p><? echo $aop_color_id_val['subcon_job']; ?></p></td>
	                        <td width="100" style="word-break:break-all"><? echo $buyer_po_arr[$buyer_po_id]['job']; ?></td>
	                        <td width="100" style="word-break:break-all"><?php echo $po_no; ?></td>
	                        <td width="100" style="word-break:break-all"><?php echo $int_ref; ?></td>
	                        <td width="100" style="word-break:break-all"><?php echo $style_no; ?></td>
	                        <td width="100" style="word-break:break-all"><?php echo $aop_color_id_val['aop_reference']; ?></td>
	                        <td width="" title="<? echo $aop_color_id;?>"><p><? echo $color_arr[$aop_color_id]; ?></p></td>
	                    </tr>
	                    <?
	                    $i++;
            		}
            	}
            }       
			?>
			<input type="hidden" name="txt_row_id" id="txt_row_id" value="<? echo $row_id; ?>"/>
            </table>
            <div style="width:100%; float:left" align="center">
		    	<input type="button" class="formbutton" id="close" style="width:80px" onClick="parent.emailwindow.hide();" value="Close" />
		    </div>
        </div>
	</div>  
        <?
}

if ($action=="load_php_data_to_form")
{
	$data=explode('**',$data);
	$color_library=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name" );
	$buyer_po_arr=array();
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number,a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	$sql= "SELECT a.within_group, a.party_id, a.party_location,a.aop_reference, b.id as po_dtls_id, b.order_no,  b.buyer_po_id, b.aop_color_id, b.body_part,b.construction ,b.composition,b.order_quantity,b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=278 and a.status_active=1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and b.buyer_po_id in($data[1]) and b.id in($data[2]) and b.aop_color_id =$data[3] order by b.id DESC";
	$color=''; $color_id=''; $style=''; 
	$nameArray=sql_select($sql);
	foreach ($nameArray as $row)
	{	
		$color=$color_library[$row[csf('aop_color_id')]];
		$color_id=$row[csf('aop_color_id')];
		
		$party=$row[csf('party_id')];
		$within_group=$row[csf('within_group')];
		$aop_reference=$row[csf('aop_reference')];
		if($within_group==1){
			$style=$buyer_po_arr[$row[csf('buyer_po_id')]]['style'];
		}else{
			$style=$row[csf('buyer_style_ref')];
		}
	}
	echo "document.getElementById('hide_within_group').value 			= '".$within_group."';\n";
	echo "document.getElementById('hide_party_id').value 				= '".$party."';\n";
	echo "document.getElementById('txt_batch_color').value 				= '".$color."';\n";
	echo "document.getElementById('hidden_batch_color_id').value 		= '".$color_id."';\n";
	echo "document.getElementById('txt_aop_ref').value 					= '".$aop_reference."';\n";
	echo "document.getElementById('txt_style_ref').value 				= '".chop($style,",")."';\n";
	exit();	
}

if( $action=='order_dtls_list_view' ) 
{
	//echo $data;
	$data=explode('**',$data);
	//buyer_po_id+'**'+po_dtls_id+'**'+color_id
	$color_library=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name" );
	if($data[0]==1)
	{
		$buyer_po_arr=array();
		$po_sql ="SELECT a.style_ref_no, b.id, b.po_number,a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
			$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
		}
		unset($po_sql_res);
		$sql= "SELECT a.within_group, b.id as po_dtls_id, b.order_no,  b.buyer_po_id,b.buyer_po_no, b.aop_color_id, b.body_part,b.construction ,b.composition,b.gsm,b.grey_dia,b.fin_dia,b.order_quantity from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=278 and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 and b.buyer_po_id in ($data[1]) and b.id in($data[2]) and b.aop_color_id =$data[3] order by b.id DESC"; 

		//echo $sql; //die; 
		$data_array=sql_select($sql); $tblRow=0; 
		//echo count($data_array)."=="; die;
		if(count($data_array) > 0)
		{
			foreach($data_array as $row)
			{
				if($row[csf('within_group')]==1){
					$po_no=$buyer_po_arr[$row[csf('buyer_po_id')]]['po'];
				}else{
					$po_no=$row[csf('buyer_po_no')];
				}
				$tblRow++;
				//echo load_drop_down( 'aop_batch_creation_controller', $row[csf('id')], 'load_drop_down_po_id', "field_po_id_1" );
				?>
				<tr class="general" id="tr_<? echo $tblRow; ?>">
                    <td id="field_po_id_<? echo $tblRow; ?>">	
                    	<?
                    	$id=$row[csf('po_dtls_id')];
                    	echo create_drop_down( "poId_$tblRow", 150,"SELECT b.id as po_id, b.order_no as po_number from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst  and b.id=$id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.order_no order by b.order_no ASC","po_id,po_number", 0, "-- Select PO --", $selected, "hidden_data_load(<? echo $tblRow; ?>);","","","", "", "", "", "","poId[]" );
                    	?>
                    </td>
                    <td>
                        <input type="text" name="txtBuyerPoNo[]" id="txtBuyerPoNo_<? echo $tblRow; ?>" class="text_boxes" style="width:100px;" value="<? echo $po_no; ?>" readonly />
                        <input type="hidden" name="txtBuyerPoId[]" id="txtBuyerPoId_<? echo $tblRow; ?>"  value="<? echo $row[csf('buyer_po_id')]; ?>"  />
                	</td> 
                    <td>
                    	<? 
                    		echo create_drop_down( "cboBodyPart_$tblRow", 130, $body_part,"", 1, "--Select--",$row[csf('body_part')], "1","1","","", "", "", "", "","cboBodyPart[]" );
                    	?>
                    </td>                            
                    <td id="itemDescTd_<? echo $tblRow; ?>">
						<input type="text" name="txtItemDesc[]" id="txtItemDesc_<? echo $tblRow; ?>" class="text_boxes" style="width:150px" value="<? echo $row[csf('construction')].",".$row[csf('composition')].",".$row[csf('gsm')].",".$row[csf('grey_dia')].",".$row[csf('fin_dia')]; ?>"  placeholder="Display" readonly />
                    	<input type="hidden" name="txtPoNo[]"  id="txtPoNo_<? echo $tblRow; ?>" class="text_boxes" style="width:70px" readonly/>
                        <input type="hidden" name="processId[]" id="processId_<? echo $tblRow; ?>" style="width:50px" class="text_boxes" readonly />
                        <input type="hidden" name="txtItemDescid[]" id="txtItemDescid_<? echo $tblRow; ?>" class="text_boxes" style="width:60px" />
                    </td>
                    <td id="DiaWidthType_<? echo $tblRow; ?>">
                        <?
							echo create_drop_down( "cboDiaWidthType_$tblRow", 80, $fabric_typee,"", 1, "-- Select  --", 0, "","","","", "", "", "", "","cboDiaWidthType[]" );
                        ?>
                    </td>                              
                    <td>
                        <input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:50px" />
                        <input type="hidden" name="hideRollNo[]" id="hideRollNo_<? echo $tblRow; ?>" class="text_boxes" readonly />
                        <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $tblRow; ?>" class="text_boxes" readonly />
                    </td>
                    <td>
                        <input type="text" name="txtBatchQnty[]"  id="txtBatchQnty_<? echo $tblRow; ?>" class="text_boxes_numeric"  value="<? echo $row[csf('order_quantity')]; ?>"  onKeyUp="calculate_batch_qnty(<? echo $tblRow; ?>);" onChange="check_balance_qnty(this.id)" style="width:60px" />
                        <input type="hidden" name="txtBalance[]" id="txtBalance_<? echo $tblRow; ?>" class="text_boxes">
                    </td>
                    <td style="display: none;">
                        <input type="text" name="txtrecChallan[]"  id="txtrecChallan_<? echo $tblRow; ?>" class="text_boxes" style="width:60px" readonly />
                   
                        <input type="text" name="txtJobParty[]"  id="txtJobParty_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" readonly />
                        <input type="button" id="increase_<? echo $tblRow; ?>" name="increase_<? echo $tblRow; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)" />
                        <input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease_<? echo $tblRow; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                    </td>
                    <td id="gsmTd_<? echo $tblRow; ?>" style="display: none;">
                        <input type="text" name="txtGsm[]" id="txtGsm_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:50px" readonly />
                    </td>
                    <td id="diaTd_<? echo $tblRow; ?>" style="display: none;">
                        <input type="text" name="txtDia[]" id="txtDia_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:50px" readonly />
                    </td>
                    <td id="finDiaTd_<? echo $tblRow; ?>" style="display: none;">
                        <input type="text" name="txtFinDia[]" id="txtFinDia_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:50px" readonly />
                    </td>
                    <td id="dyenamic_fabricfrom" style="display: none;">
                        <?  
						echo create_drop_down("cbofabricfrom_$tblRow", 70, $blank_array, "", 1, "--Select --", 0, "", 1, "", "", "", "", "", "", "cbofabricfrom[]");                            
                        ?>
                    </td>
                </tr>
				<?
			}

		}
		else
		{
			?>		
			<tr id="row_1">
	            <td><input id="txtbuyerPo_1" name="txtbuyerPo[]" name="text" class="text_boxes" style="width:100px" placeholder="Display" disabled/>
	            	<input id="txtbuyerPoId_1" name="txtbuyerPoId[]" type="hidden" class="text_boxes" style="width:70px" disabled />
	            </td>
	            <td><input id="txtstyleRef_1" name="txtstyleRef[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" disabled/></td>
	             <td><input id="txtbuyer_1" name="txtbuyer[]" type="text" class="text_boxes" style="width:100px" placeholder="Display" disabled /></td>
	            <td><? echo create_drop_down( "cboSection_1", 90, "select a.id, a.section_name from lib_section a,lib_department b,lib_division c where b.division_id=c.id and a.department_id=b.id and a.status_active=1 and b.status_active=1 and c.status_active=1","id,section_name", 1, "-- Select Section --","",'',0,'','','','','','',"cboSection[]"); ?></td>
	            <td><? echo create_drop_down( "cboItemGroup_1", 90, "select id, item_name from lib_item_group where status_active=1","id,item_name", 1, "-- Select --",$selected, "",0,'','','','','','',"cboItemGroup[]"); ?>	</td>
	            <td><? echo create_drop_down( "cboUom_1", 70, $unit_of_measurement,"", 1, "-- Select --",2,1, 1,'','','','','','',"cboUom[]"); ?>	</td>
	            <td><input id="txtOrderQuantity_1" name="txtOrderQuantity[]" class="text_boxes_numeric" type="text"  style="width:67px" onClick="openmypage_order_qnty(1,'0',1)" placeholder="Click To Search" readonly /></td>
	            <td><input id="txtRate_1" name="txtRate[]" type="text"  class="text_boxes_numeric" style="width:60px" readonly /></td>
	            <td><input id="txtAmount_1" name="txtAmount[]" type="text" style="width:70px"  class="text_boxes_numeric" readonly /></td> 
	            <td><input id="txtDomRate_1" name="txtDomRate[]" type="text"  class="text_boxes_numeric" style="width:57px" readonly /></td> 
	            <td><input id="txtDomamount_1" name="txtDomamount[]" type="text"  class="text_boxes_numeric" style="width:77px" readonly  /></td> 
	            <td><input type="text" name="txtOrderDeliveryDate[]" id="txtOrderDeliveryDate_1" class="datepicker" style="width:67px" />
	            	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_1">
	            	<input type="hidden" name="hdnRecDtlsIDs[]" id="hdnRecDtlsIDs_1">
	                <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_1">
	                <input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_1">
	            </td>
	            <td width="65">
					<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_emb','row_')" />
					<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_emb','row_');" />
				</td>
	        </tr> 
			<?
		}
	}
	else
	{
		//$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name"  );
		$sql= "SELECT a.id, a.mst_id, a.job_no_mst,a.receive_dtls_id, a.book_con_dtls_id,a.booking_dtls_id,a.buyer_po_no,a.buyer_po_id, a.buyer_style_ref, a.item_description, a.color_id, a.size_id, a.sub_section, a.uom, a.job_quantity,  a.impression,a.material_color, b.id as break_id, b.order_id, b.job_no_mst, b.product_id, b.description, b.specification, b.unit, b.pcs_unit, b.cons_qty, b.process_loss, b.process_loss_qty, b.req_qty, b.remarks from trims_job_card_dtls a ,trims_job_card_breakdown b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.mst_id=$data[1]";
		$data_array=sql_select($sql); $dtls_arr=array();
		foreach ($data_array as  $row) 
		{
			
			$rawcolor=''; $materialColor=''; $material_color=explode("__",$row[csf("material_color")]);
			//print_r($material_color);
			for($j=0; $j<count($material_color); $j++)
			{
				$materialColor.=$color_library[$material_color[$j]]."__";
			}
			$rawcolor=chop($materialColor,"__");
			// echo $rawcolor;

			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["id"]=$row[csf("id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["receive_dtls_id"]=$row[csf("receive_dtls_id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["book_con_dtls_id"].=$row[csf("book_con_dtls_id")].",";
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["booking_dtls_id"].=$row[csf("booking_dtls_id")].",";
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["buyer_po_no"]=$row[csf("buyer_po_no")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["buyer_po_id"]=$row[csf("buyer_po_id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["buyer_style_ref"]=$row[csf("buyer_style_ref")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["item_description"]=$row[csf("item_description")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["color_id"]=$row[csf("color_id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["size_id"]=$row[csf("size_id")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["sub_section"]=$row[csf("sub_section")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["uom"]=$row[csf("uom")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["job_quantity"]=$row[csf("job_quantity")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["impression"]=$row[csf("impression")];
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["material_color"]=$rawcolor;
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["break_id"].=$row[csf("break_id")].",";
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["description"].=$row[csf("description")].",";
			$dtls_arr[$row[csf("mst_id")]][$row[csf("id")]]["break_data"].=$row[csf("description")]."_".$row[csf("specification")]."_".$row[csf("unit")]."_".$row[csf("pcs_unit")]."_".$row[csf("cons_qty")]."_".$row[csf("process_loss")]."_".$row[csf("process_loss_qty")]."_".$row[csf("req_qty")]."_".$row[csf("remarks")]."_".$row[csf("break_id")]."_".$row[csf("product_id")]."**";
		}
		//echo "<pre>";
		//print_r($dtls_arr);
		foreach($dtls_arr as $dtls_data)
		{
			foreach($dtls_data as $row)
			{
				$tblRow++;
				//echo $row['item_description']."nazim" ; die;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
					<td><input id="txtbuyerPo_<? echo $tblRow; ?>" name="txtbuyerPo[]"   class="text_boxes" type="text" value="<? echo $row['buyer_po_no']; ?>" style="width:100px" disabled />
						<input id="txtbuyerPoId_<? echo $tblRow; ?>" name="txtbuyerPoId[]" value="<? echo $row['buyer_po_id']; ?>" class="text_boxes" type="hidden" style="width:70px" disabled />
					</td>
	                <td><input id="txtstyleRef_<? echo $tblRow; ?>" name="txtstyleRef[]" type="text" class="text_boxes" value="<? echo $row['buyer_style_ref']; ?>"  style="width:100px" placeholder="Display" disabled/></td>
	                <td><input id="txtdescription_<? echo $tblRow; ?>" name="txtdescription[]" type="text" class="text_boxes" value="<? echo $row['item_description']; ?>"  style="width:100px" placeholder="Display" disabled/></td>
	                <td><input id="txtcolor_<? echo $tblRow; ?>" name="txtcolor[]" type="text" class="text_boxes" value="<? echo $color_library[$row['color_id']]; ?>" style="width:100px" placeholder="Display" disabled/>
						<input id="txtcolorID_<? echo $tblRow; ?>" name="txtcolorID[]" type="hidden" class="text_boxes" value="<? echo $row['color_id']; ?>" style="width:100px" placeholder="Display" disabled/>
	                </td>
	                <td><input id="txtsize_<? echo $tblRow; ?>" name="txtsize[]" type="text" class="text_boxes" value="<? echo $size_arr[$row['size_id']]; ?>" style="width:100px" placeholder="Display" disabled/>
						<input id="txtsizeID_<? echo $tblRow; ?>" name="txtsizeID[]" type="hidden" class="text_boxes" value="<? echo $row['size_id']; ?>" style="width:100px" placeholder="Display" disabled/>
	                </td>
	                <td id="subSectionTd_<? echo $tblRow; ?>"><? echo create_drop_down( "cboSubSection_".$tblRow, 70, $trims_sub_section,"", 1, "-- Select Section --", $row['sub_section'],"",1,'','','','','','',"cboSubSection[]"); ?></td>
	                <td><? echo create_drop_down( "cboUom_".$tblRow, 70, $unit_of_measurement,"", 1, "-- Select --",$row['uom'],1, 1,'','','','','','',"cboUom[]"); ?>	</td>
	                <td><input id="txtJobQuantity_<? echo $tblRow; ?>" name="txtJobQuantity[]" class="text_boxes_numeric" type="text" value="<? echo round($row['job_quantity']); ?>" readonly style="width:67px" /></td>
					<td><input id="txtRawMat_<? echo $tblRow; ?>" name="txtRawMat[]" type="text" class="text_boxes" value="<? echo $row['description']; ?>" style="width:100px"  onClick="openmypage_row_metarial(2,'0',<? echo $tblRow; ?>)" placeholder="Double Click"/></td>
					<td><input id="txtImpression_<? echo $tblRow; ?>" name="txtImpression[]" type="text" class="text_boxes" value="<? echo $row['impression']; ?>" style="width:100px" placeholder="Display"/></td>
					<td><input id="txtRawcolor_<? echo $tblRow; ?>" name="txtRawcolor[]" type="text" class="text_boxes" value="<? echo $row['material_color']; ?>"  onClick="open_color(1,<? echo $tblRow; ?>)" style="width:100px" placeholder="Click"/>
						<input id="hdnRawcolor_<? echo $tblRow; ?>" name="hdnRawcolor[]" type="hidden" class="text_boxes" value="<? echo $row['material_color']; ?>" />
	                	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_<? echo $tblRow; ?>" value="<? echo $row['id']; ?>">
	                	<input type="hidden" name="hdnRecDtlsIDs[]" id="hdnRecDtlsIDs_<? echo $tblRow; ?>" value="<? echo $row['receive_dtls_id']; ?>">
	                	<input type="hidden" name="hdnbookingDtlsId[]" id="hdnbookingDtlsId_<? echo $tblRow; ?>" value="<? echo chop($row['booking_dtls_id'],","); ?>">
	                    <input type="hidden" name="bookConDtlsId[]" id="bookConDtlsId_<? echo $tblRow; ?>" value="<? echo chop($row['book_con_dtls_id'],","); ?>">
	                    <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_<? echo $tblRow; ?>" value="<? echo chop($row['break_data'],"**"); ?>" >
	                    <input type="hidden" name="hdnBreakIDs[]" id="hdnBreakIDs_<? echo $tblRow; ?>" value="<? echo chop($row['break_id'],","); ?>">
	                </td>
				</tr>
				<?
			}
		}
	}
	exit();
}
if($action == "show_color_listview")
{
    $data = explode("*", $data);
    $company_id = $data[0];
    $job = $data[1];
    $main_process_id = $data[2];

    $sql_cond = "";

    if ($job) {
        $sql_cond .= " and a.subcon_job like '%$job%' ";
    }

    $sql = "SELECT c.color_id, a.job_no_prefix_num, b.order_no, a.subcon_job, b.process_id, a.insert_date
                from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.id=c.order_id
                and a.company_id = $company_id and b.main_process_id=$main_process_id
                and a.status_active = 1 and a.is_deleted = 0  
                and b.status_active = 1 and b.is_deleted = 0
                $sql_cond
                and c.color_id  is not null";
  
        $color_arr = return_library_array("SELECT id, color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
        $i = 1;
        $nameArray = sql_select($sql);
?>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="330" class="rpt_table">
                <thead>
                    <th width="25">SL</th>
                    <th width="80">Color</th>
                    <th width="75">Job</th>
                    <th width="">Work Order</th>                 
                </thead>
                <tbody>
    <?
        foreach ($nameArray as $selectResult) {
            if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

            $processid = explode(",", $selectResult[csf('process_id')]);
			$process_name="";
            foreach($processid as $proc_id)
			{
				if($process_name=="") $process_name=$conversion_cost_head_array[$proc_id]; else $process_name.=','.$conversion_cost_head_array[$proc_id];
			}
            	
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="color_set_value('<? echo $color_arr[$selectResult[csf('color_id')]]; ?>','<? echo $selectResult[csf('subcon_job')]; ?>','<? echo $selectResult[csf('process_id')]; ?>','<? echo $process_name; ?>')"> 
                    <td width="" align="center"><? echo $i; ?></td>
                    <td width="" title="<? echo $selectResult[csf('color_id')]; ?>"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
                    <td width="" align="center"><p><? echo $selectResult[csf('subcon_job')]; ?></p></td>
                    <td width=""><p><? echo $selectResult[csf('order_no')]; ?></p></td>
                </tr>
              
                <?
            $i++;
        	//}
    }
    ?>
                  </tbody>
    </table>
    <?
}
?>