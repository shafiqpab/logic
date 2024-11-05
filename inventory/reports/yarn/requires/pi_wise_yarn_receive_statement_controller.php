<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$seource_des_array=array(3=>"Non EPZ",4=>"Non EPZ",5=>"Abroad",6=>"Abroad",11=>"EPZ",12=>"EPZ");

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_suppler_name", 150, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b  where a.id=b.supplier_id and b.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name ","id,supplier_name", 1, "-- Select Store --", 0, "" );
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );


if($action=="pinumber_popup")
{
  	echo load_html_head_contents("PI Number Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
    ?>

    <script>
    	var selected_id = new Array();
    	var selected_name = new Array();

    	function check_all_data()
    	{
    		var tbl_row_count = document.getElementById( 'table_body' ).rows.length;
    		tbl_row_count = tbl_row_count-1;
    		for( var i = 1; i <= tbl_row_count; i++ )
    		{
    			js_set_value( i );
    		}
    	}

    	function toggle( x, origColor )
    	{
    		var newColor = 'yellow';
    		if ( x.style )
    		{
    			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
    		}
    	}

    	function set_all()
    	{
    		var old=document.getElementById('selected_ids').value;
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
    		if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 )
    		{
    			selected_id.push( $('#txt_individual_id' + str).val() );
    			selected_name.push( $('#txt_individual' + str).val() );

    		}
    		else
    		{
    			for( var i = 0; i < selected_id.length; i++ )
    			{
    				if( selected_id[i] == $('#txt_individual_id' + str).val() )
    					break;
    			}
    			selected_id.splice( i, 1 );
    			selected_name.splice( i, 1 );
    		}

    		var id = ''; var name = '';
    		for( var i = 0; i < selected_id.length; i++ )
    		{
    			id += selected_id[i] + ',';
    			name += selected_name[i] + ',';
    		}

    		id = id.substr( 0, id.length - 1 );
    		name = name.substr( 0, name.length - 1 );

    		$('#selected_id').val(id);
    		$('#selected_name').val(name);
    	}
    </script>

    </head>

    <body>
    <div align="center" style="width:100%; margin-top:5px" >
        <form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
	       <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th id="search_by_td_up">Enter PI Number</th>
                    <th>Enter PI Date</th>
                    <th>
                    	<input type="reset" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchlcfrm_1','search_div','','','','');" />
                        <input type="hidden" id="pi_id" value="" />
                        <input type="hidden" id="pi_no" value="" />
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr align="center">
                    <td>
                        <?
							echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=2",'id,supplier_name', 1, '-- All Supplier --',0,'',0);
                        ?>
                    </td>
                    <td align="center" id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td align="center" id="search_by_td">
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;" placeholder="From Date" readonly />
                        To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;" placeholder="To Date" readonly />
                    </td>
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_pi_search_list_view', 'search_div', 'pi_wise_yarn_receive_statement_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
           	 	</tr>
                <tr>
                	<td colspan="4" align="center"><? echo load_month_buttons(1); ?></td>
                </tr>
            </tbody>
            </table>
            <div align="center" style="margin-top:10px" id="search_div"> </div>
        </form>
    </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_pi_search_list_view")
{
	$ex_data = explode("_",$data);

	if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	if( $from_date!="" && $to_date!="")
	{
		if($db_type==0)
		{
			$pi_date_cond= " and pi_date between '".change_date_format($from_date,"yyyy-mm-dd")."' and '".change_date_format($to_date,"yyyy-mm-dd")."'";
		}
		else
		{
			$pi_date_cond= " and pi_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
		}
	}
	else $pi_date_cond="";

	$pi_sql= "select id, pi_number, supplier_id, importer_id, pi_date, last_shipment_date, total_amount from com_pi_master_details where importer_id in($company) and entry_form=165 and supplier_id like '$cbo_supplier' and pi_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1 $pi_date_cond";

	?>
	<input type="hidden" name="selected_name" id="selected_name" value="">
	<input type="hidden" name="selected_id" id="selected_id" value="">
	<table class="rpt_table" id="rpt_tablelist_view" rules="all" width="790" cellspacing="0" cellpadding="0" border="0">
		<thead>
			<tr>
				<th width="50">SL No</th>
				<th width="130">PI No</th>
				<th width="110">Importer</th>
				<th width="130"> Supplier Name</th>
				<th width="90">PI Date</th>
				<th width="130"> Last Shipment Date</th>
				<th> PI Value</th>
			</tr>
		</thead>
	</table>

	<div style="max-height:260px; width:788px; overflow-y:scroll" id="scroll_body">
		<table class="rpt_table" id="list_view" rules="all" width="768" height="" cellspacing="0" cellpadding="0" border="0">
			<tbody>
				<?
				$pi_sql_result = sql_select($pi_sql);
				$selected_id_arr=explode(",",$cbo_company);
				$i = 1;
				foreach($pi_sql_result as $row)
				{
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					if(in_array($row[csf("id")],$selected_id_arr))
					{
						if($selected_ids=="") $selected_ids=$i; else $selected_ids.=",".$i;
					}

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer; height: 20px;"  id="search<? echo $i;?>" >
						<td width="50" align="center">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
							<input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row[csf('pi_number')]; ?>"/>
						</td>
						<td width="130" align="left"><p><? echo $row[csf('pi_number')]; ?></p></td>
						<td width="110" align="left"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
						<td width="130" align="left"><p><? echo $company_arr[$row[csf('importer_id')]]; ?></p></td>
						<td width="90" align="left"><p><? echo change_date_format($row[csf('pi_date')]); ?></p></td>
						<td width="130" align="left"><p><? echo change_date_format($row[csf('last_shipment_date')]); ?></p></td>
						<td align="right"><p><? echo $row[csf('total_amount')]; ?></p></td>
					</tr>
					<?
					$i++;
				}
				?>
				<input type="hidden" name="selected_ids" id="selected_ids" value="<? echo $selected_ids; ?>"/>
			</tbody>
		</table>

		<table width="768" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	</div>

	

	<script type="text/javascript">
		setFilterGrid('table_body',-1);
		set_all();
	</script>
	<?
	
	//$arr=array(1=>$company_arr,2=>$supplier_arr);
	//echo create_list_view("list_view", "PI No, Importer, Supplier Name, PI Date, Last Shipment Date, PI Value","130,110,130,90,130","780","260",0, $sql , "js_set_value", "id,pi_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "pi_number,importer_id,supplier_id,pi_date,last_shipment_date,total_amount", "",'','0,0,0,3,3,2') ;
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_suppler_name=str_replace("'","",$cbo_suppler_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);

	$pi_id=str_replace("'","",$pi_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	$company_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_name_arr=return_library_array( "select id,short_name from lib_buyer ",'id','short_name');
	$supplier_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name"  );
	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$brand_arr=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  ); 
	
	if($cbo_suppler_name>0) $sql_cond=" and a.supplier_id in ($cbo_suppler_name)";
	$pi_cond = "";
	if($pi_id!="") 
	{
		$pi_cond .=" and a.id in ($pi_id)";
		$pi_cond2 = " and a.booking_id in ($pi_id)";
	}

	if($txt_date_from!="" && $txt_date_to!="")
	{
		$pi_cond.="  and a.pi_date between '".$txt_date_from."' and '".$txt_date_to."'";
	}

	$pi_sql= "SELECT a.id AS pi_id, a.importer_id as company_id, a.supplier_id, a.item_category_id, a.pi_number, a.buyer_id,a.brand_id, a.pi_date, a.net_total_amount AS pi_value, p.id AS pi_dtls_id, p.count_name, p.yarn_composition_item1, p.yarn_type, p.color_id, p.quantity AS pi_item_qtny, p.net_pi_amount AS pi_item_value, a.remarks FROM com_pi_item_details  p, com_pi_master_details a WHERE  p.pi_id = a.id AND a.entry_form = 165 AND a.importer_id in ($cbo_company_name) AND a.is_deleted = 0 AND a.status_active = 1 AND p.is_deleted = 0 AND p.status_active = 1 $pi_cond  GROUP BY a.id, a.importer_id, a.supplier_id, a.item_category_id, a.pi_number, a.buyer_id, a.brand_id, a.pi_date, a.net_total_amount, p.id, p.count_name, p.yarn_composition_item1, p.yarn_type, p.color_id, p.quantity, p.net_pi_amount, a.remarks ORDER BY a.importer_id,a.supplier_id,a.id";	
		
	//echo $pi_sql;//die;
	$pi_result=sql_select($pi_sql);
	$pi_val_arr=array();
	foreach($pi_result as $row)
	{
		$pi_val_arr[$row[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_item_qtny"]+=$row[csf("pi_item_qtny")];
		$pi_val_arr[$row[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_item_value"]+=$row[csf("pi_item_value")];

		$pi_arr[$row[csf("pi_id")]]['pi_number'] = $row[csf("pi_number")];
		$pi_arr[$row[csf("pi_id")]]['pi_date'] = $row[csf("pi_date")];
		$pi_arr[$row[csf("pi_id")]]['buyer_id'] = $row[csf("buyer_id")];
		$pi_arr[$row[csf("pi_id")]]['remarks'] = $row[csf("remarks")];

		$pi_ids .=$row[csf("pi_id")].",";
	}

	$pi_ids =chop($pi_ids,",");
	$pi_ids=implode(",",array_filter(array_unique(explode(",",$pi_ids))));
	//echo "<pre>"; 
	//print_r($issue_ids);

	if($pi_ids!="")
	{
	    $pi_ids=explode(",",$pi_ids);  
	    $pi_ids_chnk=array_chunk($pi_ids,999);
	    $pi_id_cond=" and";
	    foreach($pi_ids_chnk as $piId)
	    {
	        if($pi_id_cond==" and")  $pi_id_cond.="(a.booking_id in(".implode(',',$piId).")"; else $pi_id_cond.=" or a.booking_id in(".implode(',',$piId).")";
	    }
		$pi_id_cond.=")";

		$receive_sql="select a.id as rcv_id,a.company_id,a.supplier_id,a.booking_id as pi_id, a.receive_basis,c.id as prod_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color,b.buyer_id,b.brand_id, b.order_qnty as order_qnty, b.order_amount as order_amount 
		from inv_receive_master a, inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and a.receive_basis=1 and a.entry_form=1 and b.item_category=1 and b.transaction_type=1 and c.item_category_id=1 and a.company_id in ($cbo_company_name) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $pi_cond2 $pi_id_cond";
		//echo $receive_sql;

		$receive_result = sql_select($receive_sql);

		$recv_data_arr=array();
		foreach($receive_result as $row)
		{
			$recv_data_arr[$row[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("pi_id")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["rcv_qnty"]+=$row[csf("order_qnty")];
			$recv_data_arr[$row[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("pi_id")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["rcv_qnty"]["rcv_amt"]+=$row[csf("order_amount")];

			$rcv_prod_brand_id_arr[$row[csf("supplier_id")]][$row[csf("pi_id")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]] = $row[csf("brand_id")];

			$rcv_book_id[$row[csf("rcv_id")]]=$row[csf("pi_id")]."__".$row[csf("supplier_id")];

			$rcv_ids .=$row[csf("rcv_id")].",";
		}
	}
		
	$rcv_ids =chop($rcv_ids,",");
	$rcv_ids=implode(",",array_filter(array_unique(explode(",",$rcv_ids))));

	if($rcv_ids!="")
	{
	    $rcv_ids=explode(",",$rcv_ids);  
	    $rcv_ids_chnk=array_chunk($rcv_ids,999);
	    $rcv_id_cond=" and";
	    foreach($rcv_ids_chnk as $rcvId)
	    {
	        if($rcv_id_cond==" and")  $rcv_id_cond.="(a.received_id in(".implode(',',$rcvId).")"; else $rcv_id_cond.=" or a.received_id in(".implode(',',$rcvId).")";
	    }
		$rcv_id_cond.=")";

		$receive_return_sql="select a.company_id, a.pi_id, a.received_id,c.id as prod_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color,b.buyer_id, b.cons_quantity as cons_quantity, b.rcv_amount as cons_amount 
		from inv_issue_master a,  inv_transaction b, product_details_master c  
		where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and c.item_category_id=1  and a.entry_form=8 and a.company_id in($cbo_company_name) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $rcv_id_cond";
		//echo $receive_return_sql; die();

		$receive_return_result = sql_select($receive_return_sql);
		$recv_rtn_data_arr=array();
		foreach($receive_return_result as $row)
		{
			$rcv_data_ref=explode("__",$rcv_book_id[$row[csf("received_id")]]);
			
			$wo_pi_id=$rcv_data_ref[0];
			$supplier_id=$rcv_data_ref[1];

			$recv_rtn_data_arr[$row[csf("company_id")]][$supplier_id][$wo_pi_id][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["recv_rtn_qnty"]+=$row[csf("cons_quantity")];
			$recv_rtn_data_arr[$row[csf("company_id")]][$supplier_id][$wo_pi_id][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["recv_rtn_amt"]+=$row[csf("cons_amount")];
			
			if($recv_rtn_data_arr[$row[csf("company_id")]][$supplier_id][$wo_pi_id][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["received_ids"]=="")
			{
				$recv_rtn_data_arr[$row[csf("company_id")]][$supplier_id][$wo_pi_id][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["received_ids"] = $row[csf("received_id")];
			}
			else
			{
				$recv_rtn_data_arr[$row[csf("company_id")]][$supplier_id][$wo_pi_id][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["received_ids"] .= $row[csf("received_id")].",";
			}

		}
	}

	//echo "<pre>";
	//print_r($recv_rtn_data_arr);

	ob_start();
	?>
	<div style="width:1600px; margin-left:10px;" align="left">
		<table width="1580" cellpadding="0" cellspacing="0" style="visibility:hidden; border:none" id="caption">
			<tr>
			   <td align="center" width="100%" colspan="24" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
			</tr>
		</table>
		<table width="1580" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">Company</th>
                    <th width="100">Spinning Mill</th>
					<th width="100">Buyer</th>
					<th width="100">PI No</th>
                    <th width="100">PI Date</th>
                    <th width="80">Count</th>
					<th width="140">Composition</th>
					<th width="80">Yarn Type</th>
					<th width="80">Color</th>
					<th width="80">Brand</th>  
					<th width="80">PI Qty</th>
					<th width="80">Receive Qty</th>
					<th width="80">Return Qty</th>  
					<th width="80">Total Received</th>
                    <th width="80">Yarn Balance</th>
					<th>Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:1600px; overflow-y:scroll; max-height:350px; overflow-x:hidden;" id="scroll_body" align="left">
		<table width="1580" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">   
        <tbody>
        <?
        $i=1; 
        
		$total_pi_qnty=0;
        $total_received_qty=0;
        $total_return_qnty=0;
        $total_actual_received_qnty=0;
        $total_yarn_balance=0;

        foreach($pi_val_arr as $comp_id=>$supplArr)
        {
            foreach($supplArr as $supplier_id=>$piArr)
            {              
                foreach($piArr as $pi_id=>$countArr)
                {
                    foreach($countArr as $count_id=>$compositionArr)
                    {
                    	foreach($compositionArr as $composition_id=>$yarntypeArr)
                    	{
                    		foreach($yarntypeArr as $yarn_type_id=>$colorArr)
                    		{
                    			foreach($colorArr as $color_id=>$row)
                    			{                     				

		                            if ($i%2==0)  
		                                $bgcolor="#E9F3FF";
		                            else
		                                $bgcolor="#FFFFFF";

		                            $pi_rcv_id=chop($book_rcv_id[$pi_id],",");

		                            $brand_id = $rcv_prod_brand_id_arr[$supplier_id][$pi_id][$count_id][$composition_id][$yarn_type_id][$color_id];

		                            $recv_qty = $recv_data_arr[$comp_id][$supplier_id][$pi_id][$count_id][$composition_id][$yarn_type_id][$color_id]["rcv_qnty"];

	                                $recv_rtn_qty = $recv_rtn_data_arr[$comp_id][$supplier_id][$pi_id][$count_id][$composition_id][$yarn_type_id][$color_id]["recv_rtn_qnty"];

	                                $actual_received_qty = ($recv_qty-$recv_rtn_qty);
	                                $yarn_balance = ($row["pi_item_qtny"]-$total_received);

	                                $received_ids = $recv_rtn_data_arr[$comp_id][$supplier_id][$pi_id][$count_id][$composition_id][$yarn_type_id][$color_id]["received_ids"];
	                                ?>
	                                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
	                                    <td width="30" align="center" title=""><? echo $i; ?></td>
	                                    <td width="100" align="center"><p> <? echo $company_arr[$comp_id]; ?>&nbsp;</p></td>
	                                    <td width="100"><p><? echo $supplier_arr[$supplier_id]; ?>&nbsp;</p></td>
	                                    <td width="100"><p><? echo $buyer_name_arr[$pi_arr[$pi_id]['buyer_id']]; ?>&nbsp;</p></td>
	                                    <td width="100"><p><? echo $pi_arr[$pi_id]['pi_number']; ?>&nbsp;</p></td>
	                                    <td width="100"><p><? echo change_date_format($pi_arr[$pi_id]['pi_date']);?>&nbsp;</p></td>
	                                    <td width="80"><p><? echo $yarn_count_arr[$count_id]; ?>&nbsp;</p></td>
	                                    <td width="140"><p><? echo $composition[$composition_id];?>&nbsp;</p></td>
	                                    <td width="80"><p><? echo $yarn_type[$yarn_type_id]; ?>&nbsp;</p></td>                      
	                                    <td width="80"><p><? echo $color_arr[$color_id]; ?>&nbsp;</p></td>
	                                    <td width="80"><p><? echo $brand_arr[$brand_id]; ?>&nbsp;</p></td>
	                                    <td width="80" align="right"><? echo number_format($row["pi_item_qtny"],2); ?></td>

	                                    <td width="80" align="right"><a href="##" onClick="openmypage_mrr('<? echo $comp_id; ?>','<? echo $pi_id; ?>','<? echo $count_id; ?>','<? echo $composition_id; ?>','<? echo $yarn_type_id; ?>','<? echo $color_id; ?>','<? echo $received_ids; ?>','Receive Info','receive_mrr_popup');"><?  echo number_format($recv_qty,2);?></a></td>

	                                    <td width="80" align="right"><a href="##" onClick="openmypage_mrr('<? echo $comp_id; ?>','<? echo $pi_id; ?>','<? echo $count_id; ?>','<? echo $composition_id; ?>','<? echo $yarn_type_id; ?>','<? echo $color_id; ?>','<? echo $received_ids; ?>','Return Info','return_mrr_popup');"><? echo number_format($recv_rtn_qty,2); ?></a></td>
	                                    
	                                    <td width="80" align="right"><? echo number_format($actual_received_qty,2); ?></td> 
	                                    <td width="80" align="right"><? echo number_format( ($row["pi_item_qtny"]-$actual_received_qty) ,2); ?></td>
	                                    <td><p><? echo $pi_arr[$pi_id]['remarks'];//echo $val["remarks"]; ?>&nbsp;</p></td>                                    
	                                </tr>

	                                <?
	                                $i++;
	                                $total_pi_qnty+=$row["pi_item_qtny"];
	                                $total_received_qty+=$recv_qty;
	                                $total_return_qnty+=$recv_rtn_qty;
	                                $total_actual_received_qnty+=$actual_received_qty;
	                                $total_yarn_balance+=$yarn_balance;
                            	}
                            }
                        }
                    	
                    }
                }
            }
            
        }
        ?>
        </tbody>         	
		</table>
		</div>
		<table width="1580" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
        <tfoot>
            <tr>
                <th width="30">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="100">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="140">&nbsp;</th>  
                <th width="80">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="80">Grand Total</th>
                <th width="80" id="value_total_pi_qnty"><? echo number_format($total_pi_qnty,2); ?></th>
                <th width="80" id="value_total_rcv_qnty"><? echo number_format($total_received_qty,2); ?></th>
                <th width="80" id="value_total_rtn_qnty"><? echo number_format($total_return_qnty,2); ?></th> 
                <th width="80" id="value_total_actual_rcv_qnty"><? echo number_format($total_actual_received_qnty,2); ?></th>
                <th width="80" id="value_total_yarn_balance"><? echo number_format($total_yarn_balance,2); ?></th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
		</table>
	</div>      
	<?
	
    foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=="receive_mrr_popup")
{	
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$pi_id=str_replace("'","",$pi_id);
	$count_id=str_replace("'","",$count_id);
	$composition_id=str_replace("'","",$composition_id);
	$type_id=str_replace("'","",$type_id);
	$color_id=str_replace("'","",$color_id);
	$yarn_count_arr=return_library_array("SELECT id,yarn_count FROM lib_yarn_count","id","yarn_count");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	?>	
    <div id="report_container" align="center" style="width:950px">
	<fieldset style="width:950px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="940" cellpadding="0" cellspacing="0">
             	<thead>
                	<tr>
                        <th width="80">Receive Date</th>
                        <th width="120">MRR Number</th>
                        <th width="80">Challan No</th>
                        <th width="80">Lot No</th>
                        <th width="80">Count</th>
                        <th width="80">Composition</th>
                        <th width="80">Type</th>
                        <th width="80">Color</th>
                        <th width="80">Receive Qnty</th>
                        <th width="80">Rate</th>
                        <th width="100">Value</th>
                        <th >Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?
				
				$sql="select a.id as mrr_id, a.recv_number, a.receive_date, a.challan_no, a.remarks, sum(b.order_qnty) as qnty, sum(b.order_amount) as amt, c.lot, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color
					from inv_receive_master a,  inv_transaction b, product_details_master c 
					where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=1 and b.item_category=1 and a.receive_basis=1 and b.transaction_type=1 and a.company_id=$company_id and a.booking_id in($pi_id) and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition_id and c.yarn_type=$type_id and c.color=$color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					group by a.id, a.recv_number, a.receive_date, a.challan_no, a.remarks, c.lot, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color";
				//echo $sql;
					
				$result=sql_select($sql);$i=1;
				foreach($result as $row)  
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center"><p><? if($row[csf('receive_date')]!="" && $row[csf('receive_date')]!="0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
						<td align="center"><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $row[csf('lot')]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $yarn_count_arr[$row[csf('yarn_count_id')]]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $composition[$row[csf('yarn_comp_type1st')]]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $color_arr[$row[csf('color')]]; ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2);  ?></td>
                        <td align="right"><? $rate=$row[csf('amt')]/$row[csf('qnty')]; echo number_format($rate,2);?></td>
                        <td align="right"><? echo number_format($row[csf('amt')],2);?></td>
                        <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
					$total_qnty+=$row[csf('qnty')];
					$total_amt+=$row[csf('amt')];
				}
				?>
                </tbody> 
                <tfoot>
                	<tr>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>Total :</th>
                        <th align="right"><? echo number_format($total_qnty,2) ; ?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_amt,2) ; ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>  
            </table>
        </fieldset>
    </div>
	<?
    exit();
}

if($action=="return_mrr_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$pi_id=str_replace("'","",$pi_id);
	$count_id=str_replace("'","",$count_id);
	$composition_id=str_replace("'","",$composition_id);
	$type_id=str_replace("'","",$type_id);
	$color_id=str_replace("'","",$color_id);
	$received_ids=str_replace("'","",$received_ids);

	$yarn_count_arr=return_library_array("SELECT id,yarn_count FROM lib_yarn_count","id","yarn_count");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	?>	
    <div id="report_container" align="center" style="width:950px">
	<fieldset style="width:950px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="940" cellpadding="0" cellspacing="0">
             	<thead>
                	<tr>
                        <th width="80">Return Date</th>
                        <th width="120">Return Number</th>
                        <th width="80">Lot No</th>
                        <th width="80">Count</th>
                        <th width="80">Composition</th>
                        <th width="80">Type</th>
                        <th width="80">Color</th>
                        <th width="100">Return Qnty</th>
                        <th width="100">Rate</th>
                        <th width="100">Value</th>
                        <th >Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and id=(select max(id) as id from currency_conversion_rate where currency=2)" , "conversion_rate" );
				$sql="select a.id as mrr_id, a.issue_number as recv_number, a.issue_date as receive_date, a.remarks, sum(b.cons_quantity) as qnty, sum(b.rcv_amount) as amt,c.lot, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color
					from inv_issue_master a,  inv_transaction b, product_details_master c 
					where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=8 and b.item_category=1 and b.transaction_type=3 and a.company_id=$company_id and a.pi_id in ($pi_id) and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition_id and c.yarn_type=$type_id and c.color=$color_id and a.received_id in ($received_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					group by a.id, a.issue_number, a.issue_date, a.remarks,c.lot, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color";
				//echo $sql;//die;	
				$result=sql_select($sql);$i=1;
				foreach($result as $row)  
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$amount=$row[csf('amt')]/$currency_rate;
					$rate=$amount/$row[csf('qnty')];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center"><p><? if($row[csf('receive_date')]!="" && $row[csf('receive_date')]!="0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
						<td align="center"><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $row[csf('lot')]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $yarn_count_arr[$row[csf('yarn_count_id')]]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $composition[$row[csf('yarn_comp_type1st')]]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $color_arr[$row[csf('color')]]; ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2);  ?></td>
                        <td align="right" title="<? echo $currency_rate; ?>"><? echo number_format($rate,2);?></td>
                        <td align="right"><? echo number_format($amount,2);?></td>
                        <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
					$total_qnty+=$row[csf('qnty')];
					$total_amt+=$amount;
				}
				?>
                </tbody> 
                <tfoot>
                	<tr>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>Total :</th>
                        <th align="right"><? echo number_format($total_qnty,2) ; ?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_amt,2) ; ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>  
            </table>
        </fieldset>
    </div>
	<?
    exit();
}
?>
