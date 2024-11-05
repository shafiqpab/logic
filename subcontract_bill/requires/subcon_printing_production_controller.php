<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$process_printing="6";

if($db_type==0) $select_field="group"; 
else if($db_type==2) $select_field="wm";
else $select_field="";//defined Later		


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/subcon_printing_production_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );","","","","","",3 );
	exit();
}

if ($action=="load_drop_down_party_name")
{
	echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",4 ); 
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
	
	echo create_drop_down( "cbo_floor_id", 140, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=3 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=6 $location_cond $group_cond order by a.floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/subcon_printing_production_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );","" );
  exit();	 
}

if ($action=="load_drop_machine")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$floor_id=$data[1];
	if($floor_id==0 || $floor_id=="") $floor_cond=""; else $floor_cond=" and floor_id=$floor_id";
	
	echo create_drop_down( "cbo_machine_name", 140, "select id, machine_no || '-' || brand as machine_name from lib_machine_name where category_id=3 and company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name","id,machine_name", 1, "-- Select Machine --", $selected, "","" );
	exit();
}

if ($action=="load_drop_down_batch_no")
{
	if($data==4)
	{
		echo '<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:130px;text-align:right;" tabindex="10" />';
	}
	else
	{
		echo '<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:130px" placeholder="Double Click to Search" onDblClick="openmypage_batchno();" readonly tabindex="10" />';
	}
	exit();
}

if ($action=="load_drop_down_order_numbers")
{
	if($data==4)
	{
		echo "<input type='text' name='txt_order_numbers' id='txt_order_numbers' class='text_boxes' style='width:130px' placeholder='Double Click to Search' onDblClick='openmypage_order_numbers()' readonly tabindex='11' />";
	}
	else
	{
		echo "<input type='text' name='txt_order_numbers' id='txt_order_numbers' class='text_boxes' style='width:130px' readonly tabindex='11' />";
	}
	exit();
}

if ($action=="batch_no_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
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
            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>                	 
                    <th width="150">Company Name</th>
                    <th width="110">Batch No</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>              
                </thead>
                <tbody>
                    <tr>
                    <td> <input type="hidden" id="selected_batch_id">  
						<?   
							$data=explode("_",$data);
							echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1 );//load_drop_down( 'subcon_printing_production_controller', this.value, 'load_drop_down_party_name','party_td' );
                        ?>
                    </td>
                    <td>
                        <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:95px" />
                    </td>
                    <td align="center"><input type="hidden" id="process_id" value="<? echo $data[2];?>">  
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                    </td> 
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value, 'batch_search_list_view', 'search_div', 'subcon_printing_production_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
                    </tr>
                    <tr>
                        <td  colspan="4" align="center" height="40" valign="middle">
							<? echo load_month_buttons(1);  ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
                </tbody>
            </table>    
        </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="batch_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_con=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!="" &&  $data[2]!="") $order_rcv_date = "and a.batch_date between '".change_date_format($data[1], "mm-dd-yyyy", "/",1)."' and '".change_date_format($data[2], "mm-dd-yyyy", "/",1)."'"; else $order_rcv_date ="";
	if ($data[3]!='') $batch_no_cond=" and a.batch_no='$data[3]'"; else $batch_no_cond="";
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	if($db_type==0)
	{
		$grop_cond= " group by a.batch_no";
	}
	else if($db_type==2)
	{
		$grop_cond= " group by a.id, a.batch_no, a.extention_no, a.color_id, a.batch_weight, a.total_liquor";
	}
	$sql="select a.id, a.batch_no, a.extention_no, a.color_id, a.batch_weight, a.total_liquor, sum(b.batch_qnty) as batch_qnty, $select_field"."_concat(distinct(b.po_id)) as po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.entry_form=36 $company_con $order_rcv_date $batch_no_cond $grop_cond";// and a.batch_against=1
	
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellspacing="0" border="1" rules="all" width="717" class="rpt_table">
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
     </div>
     <div style="width:720px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellspacing="0" border="1" rules="all" width="700" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_id=explode(',',$row[csf("po_id")]);
				$order_no='';
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
						<td width="100" align="center"><? echo $row[csf("batch_weight")]; ?></td>
						<td width="80"><? echo $row[csf("total_liquor")];  ?></td>	
						<td><? echo $order_no; ?></td>
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

if($action=="load_php_data_to_form_batch")
{
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	$process_arr=return_library_array( "select id,main_process_id from subcon_ord_dtls",'id','main_process_id');
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	if($db_type==0)
	{
		$grop_cond= " group by a.batch_no, a.extention_no";
	}
	else if($db_type==2)
	{
		$grop_cond= " group by a.batch_no, a.extention_no, a.color_id";
	}

	$nameArray=sql_select( "select a.batch_no, a.extention_no, a.color_id, $select_field"."_concat(distinct(b.po_id)) as po_id from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.id='$data' $grop_cond" );
	foreach ($nameArray as $row)
	{	
		$order_id=explode(',',$row[csf("po_id")]);
		$order_no='';
		$process_id='';
		foreach($order_id as $val)
		{
			if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
			if($process_id=="") $process_id=$process_arr[$val]; else $process_id.=",".$process_arr[$val];
		}
	
		echo "document.getElementById('txt_batch_no').value				= '".$row[csf("batch_no")]."';\n"; 
		echo "document.getElementById('txt_batch_ext_no').value			= '".$row[csf("extention_no")]."';\n"; 
		echo "document.getElementById('txt_order_numbers').value		= '".$order_no."';\n"; 
		echo "document.getElementById('process_id').value		= '".$process_id."';\n"; 
		echo "document.getElementById('order_no_id').value				= '".$row[csf("po_id")]."';\n";
		echo "document.getElementById('txt_color').value				= '".$color_arr[$row[csf("color_id")]]."';\n"; 
		echo "document.getElementById('hidden_color_id').value			= '".$row[csf("color_id")]."';\n";
	}
	exit();  
}

if($action=="show_fabric_desc_listview")
{
	$data=explode('_',$data);
	$order_id=$data[0];
	$process_id=$data[1];
	$item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');	
	$gsm_arr=return_library_array( "select id,gsm from lib_subcon_charge",'id','gsm');
	
	$production_qty_array=array();
	$prod_sql="Select cons_comp_id, sum(product_qnty) as product_qnty from subcon_production_dtls where order_id='$data[0]' and product_type=6 group by cons_comp_id";
	$prod_data_sql=sql_select($prod_sql);
	foreach($prod_data_sql as $row)
	{
		$production_qty_array[$row[csf('cons_comp_id')]]=$row[csf('product_qnty')];
	}
	//var_dump($production_qty_array);
	$sql = "select item_id, sum(qnty) as qnty from subcon_ord_breakdown where order_id in ($data[0]) group by item_id"; 
	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="350">
        <thead>
            <th width="15">SL</th>
            <th>Fabric Description</th>
            <th width="60">Order Qty</th>
            <th width="40">Prod. Qty</th>
             <th width="60">Bal. Qty</th>
        </thead>
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				
				
				if($process_id==2 || $process_id==3 || $process_id==4 || $process_id==6 || $process_id==7)
				{
					$item_name=$item_arr[$row[csf('item_id')]];	
					$gsm_val=$gsm_arr[$row[csf('item_id')]];	
				}
				else
				{
					$item_name=$garments_item[$row[csf('item_id')]];
					$gsm_val='';
				}				
             ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('item_id')]."**".$item_name."**".$gsm_val; ?>")' style="cursor:pointer" >
                    <td><? echo $i; ?></td>
                    <td><? echo $item_name; ?></td>
                    <td align="right"><? echo number_format($row[csf('qnty')]); ?></td>
                    <td align="right"><? echo number_format($production_qty_array[$row[csf('item_id')]]); ?></td>
                    <td align="right"><? echo number_format($row[csf('qnty')]-$production_qty_array[$row[csf('item_id')]]); ?></td>
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


if ($action=="order_numbers_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	?>
	<script>
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				eval($('#tr_'+i).attr("onclick"));  
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
		
		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			str=str[1]+'_'+str[2];
			if( jQuery.inArray(  str , selected_id ) == -1 )
			{
				selected_id.push( str );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name='';
			for( var i = 0; i < selected_id.length; i++ )
			{
				var data_id=selected_id[i].split("_");
				id += data_id[0] + ',';
				name += data_id[1] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#selected_order_id').val( id );
			$('#selected_order_name').val( name );
		} 
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Party Name</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>              
                    </thead>
                    <tbody>
                        <tr>
                            <td> <input type="hidden" id="selected_order_id"> 
                                <input type="hidden" id="selected_order_name"> 
                                <?   
                                    $data=explode("_",$data);
                                    echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"load_drop_down( 'subcon_printing_production_controller', this.value, 'load_drop_down_party_name','party_td' );",1 );
                            ?>
                            </td>
                            <td id="party_td">
                                <? 
                                    echo create_drop_down( "cbo_party_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[1], "",1 ); 
                                ?>
                            </td>
                            <td align="center"><input type="hidden" id="process_id" value="<? echo $data[2];?>">  
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('process_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'order_search_list_view', 'search_div', 'subcon_printing_production_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="center" height="40" valign="middle">
                                <? echo load_month_buttons(1);  ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="center" valign="top" id=""><div id="search_div"></div></td>
                        </tr>
                    </tbody>
                </table>    
            </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="order_search_list_view")
{
	$data=explode('_',$data);
	//echo $data[2];
	if ($data[0]!=0) $company_con=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $party_con=" and a.party_id='$data[1]'"; else { echo "Please Select Party First."; die; }
	if ($data[3]!="" &&  $data[4]!="") $order_rcv_date = "and b.delivery_date between '".change_date_format($data[3], "mm-dd-yyyy", "/",1)."' and '".change_date_format($data[4], "mm-dd-yyyy", "/",1)."'"; else $order_rcv_date ="";
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$party_arr,5=>$production_process);
	if($db_type==0)
	{
		$year_cond= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	$sql="select a.subcon_job, a.job_no_prefix_num, $year_cond, a.party_id, a.location_id, a.status_active, b.id, b.job_no_mst, b.order_no, b.order_quantity, b.cust_style_ref, b.order_rcv_date, b.delivery_date, b.main_process_id, b.process_id, b.status_active from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active=1 $company_con $party_con $order_rcv_date order by a.id DESC";
	
	echo  create_list_view("list_view", "Party Name,Job No,Year,Order No,Style,Process,Order Qty", "100,60,80,100,110,110,100","700","250",0, $sql , "js_set_value", "id,order_no", "", 1, "party_id,0,0,0,0,main_process_id,0", $arr , "party_id,job_no_prefix_num,year,order_no,cust_style_ref,main_process_id,order_quantity", "","",'0,0,0,0,0,0,1',"",1) ;
	exit();
}

if ($action=="cons_comp_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('cons_comp_id').value=id;
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
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>              
                </thead>
                <tbody>
                    <tr>
                        <td> <input type="hidden" id="cons_comp_id">  
							<?   
								$data=explode("_",$data);
								echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",0 );
                            ?>
                        </td>
                        <td align="center">  
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            <input type="hidden" id="color_id" name="color_id" value="<? echo $data[1]; ?>">
                        </td> 
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('color_id').value, 'cons_comp_list_view', 'search_div', 'subcon_printing_production_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" height="40" valign="middle">
							<? echo load_month_buttons(1);  ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
                </tbody>
            </table>    
            </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="cons_comp_list_view")
{
	$data=explode('_',$data);
	//echo $data[3];die;
	if ($data[0]!=0) $company_con=" and comapny_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$color_library_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");  
	$arr=array (2=>$fabric_typee,3=>$color_library_arr,4=>$unit_of_measurement,5=>$process_type,6=>$conversion_cost_head_array,7=>$production_process);
	$sql= "select id,comapny_id,body_part,const_comp,gsm,yarn_description,in_house_rate,uom_id,status_active from lib_subcon_charge where is_deleted=0 and rate_type_id=4 $company_con";
	echo  create_list_view ( "list_view", "Const. & Compo.,GSM,Width/Dia,Color,UOM,Process Type,Process Name,Rate", "180,80,80,80,60,80,80,50","770","250",1, $sql, "js_set_value", "id","", 1, "0,0,width_dia_id,color_id,uom_id,process_type_id,process_id,rate_type_id", $arr, "const_comp,gsm,width_dia_id,color_id,uom_id,process_type_id,process_id,rate_type_id","requires/subcon_fabric_finishing_production_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0') ;
	exit();
}

if($action=="load_php_data_to_form_cons_comp")
{
	$data=explode('_',$data);
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	if($data[1]!="")
	{
		$nameArray=sql_select( "select id,const_comp, gsm, color_id from lib_subcon_charge where id='$data[0]'");
		foreach ($nameArray as $row)
		{	
			echo "document.getElementById('txt_composition').value			= '".$row[csf("const_comp")]."';\n"; 
			echo "document.getElementById('txt_gsm').value					= '".$row[csf("gsm")]."';\n";
			echo "document.getElementById('hidd_comp_id').value				= '".$row[csf("id")]."';\n";
		}  
	}
	else
	{
		$nameArray=sql_select( "select id, const_comp, gsm, color_id from lib_subcon_charge where id='$data[0]'");
		foreach ($nameArray as $row)
		{	
		echo "document.getElementById('txt_composition').value			= '".$row[csf("const_comp")]."';\n"; 
		echo "document.getElementById('txt_color').value					= '".$color_arr[$row[csf("color_id")]]."';\n"; 
		echo "document.getElementById('txt_gsm').value					= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('hidd_comp_id').value				= '".$row[csf("id")]."';\n";
		}  
	}
	exit();
}

if($action=="order_qnty_popup")
{
	echo load_html_head_contents("order qnty Info", "../../", 1, 1,'','','');
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
				//echo $data;
					$data=explode('_',$data);
					//print_r ($data[0]);
					if($data[1]=="")
					{
						$i=1;
						$order_name=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');	
						$break_order_id=explode(',',$data[0]);
						$break_order_qnty=explode(',',$data[3]);
						for($k=0; $k<count($break_order_id); $k++)
						{ 
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="150">
                                <? echo $order_name[$break_order_id[$k]]; ?>
                                </td>
                                <td width="150" align="center">
                                <input type="text" name="orderqnty_<? echo $i; ?>" id="orderqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:140px;" value="<? echo $break_order_qnty[$k]; ?>" />
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
							$order_name=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');	
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
							//echo "select id, order_id, quantity from subcon_production_qnty where dtls_id in ($data[1])";
							$nameArray=sql_select( "select id, order_id, quantity from subcon_production_qnty where dtls_id in ($data[1])");
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
                </table>
                <table width="620">
                    <tr>
                        <td align="center" >
                            <input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                        </td>
                    </tr>
                </table>
            </div>
        </form>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>            
	<?
	exit();
}

if ($action=="production_id_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	$ex_data=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('product_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="150">Company Name</th>
                        <th width="150">Party Name</th>
                        <th width="110">Production ID</th>
                        <th width="170">Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>           
                    </thead>
                    <tbody>
                        <tr>
                            <td> <input type="hidden" id="product_id">  
								<?   
									echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"load_drop_down( 'subcon_printing_production_controller', this.value, 'load_drop_down_party_name', 'party_td' );",0 );
                                ?>
                            </td>
                            <td id="party_td">
                                <? echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$ex_data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $ex_data[1], "" );   	 
                                ?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:95px" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_party_name').value, 'printing_production_id_search_list_view', 'search_div', 'subcon_printing_production_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" valign="top" id=""><div id="search_div"></div></td>
                        </tr>
                </table>    
            </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=="printing_production_id_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!="" &&  $data[2]!="") $production_date_cond = "and product_date between '".change_date_format($data[1], "mm-dd-yyyy", "/",1)."' and '".change_date_format($data[2], "mm-dd-yyyy", "/",1)."'"; else $production_date_cond="";
	if ($data[3]!='') $product_id_cond=" and prefix_no_num='$data[3]'"; else $product_id_cond="";
	if ($data[4]!=0) $buyer_cond=" and party_id='$data[4]'"; else $buyer_cond="";
	
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$return_to=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$arr=array (2=>$receive_basis_arr,3=>$return_to);
	if($db_type==0)
	{
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
	$sql= "select id, product_no, prefix_no_num, $year_cond, basis, location_id, party_id, product_date, prod_chalan_no, remarks, status_active from subcon_production_mst where entry_form=293 and product_type=6 and status_active=1 $company_name $buyer_cond $production_date_cond $product_id_cond order by id DESC";
	
	echo  create_list_view("list_view", "Prod. ID,Year,Basis,Party,Prod. Date,Product Challan", "70,80,100,120,70,100","650","250",0, $sql , "js_set_value", "id", "", 1, "0,0,basis,party_id,0,0", $arr , "prefix_no_num,year,basis,party_id,product_date,prod_chalan_no", "subcon_fabric_finishing_production_controller","",'0,0,0,0,3,0');
	exit();
}

if ($action=="load_php_data_to_form_mst")
{
	$nameArray=sql_select( "select id,product_no,basis,company_id,location_id,party_id,product_date,prod_chalan_no,remarks from entry_form=293 and subcon_production_mst where id='$data'" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_production_id').value 			= '".$row[csf("product_no")]."';\n";
		echo "document.getElementById('cbo_receive_basis').value			= '".$row[csf("basis")]."';\n"; 
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/subcon_printing_production_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n"; 
		echo "load_drop_down( 'requires/subcon_printing_production_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_party_name', 'party_td' );\n"; 
		echo "document.getElementById('cbo_party_name').value				= '".$row[csf("party_id")]."';\n"; 
		echo "document.getElementById('txt_production_date').value 			= '".change_date_format($row[csf("product_date")])."';\n";   
		echo "document.getElementById('txt_chal_no').value					= '".$row[csf("prod_chalan_no")]."';\n"; 
		echo "document.getElementById('txt_remarks').value					= '".$row[csf("remarks")]."';\n"; 
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'subcon_printing_production',1);\n";
	}
	exit();
}

if ($action=="printing_production_list_view")
{
	//echo $action; die;
	?>	
	<div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="100" align="center">Process</th>
                <th width="80" align="center">Batch No</th>
                <th width="200" align="center">Order No</th>                    
                <th width="150" align="center">Const. and Compo.</th>
                <th width="70" align="center">Color</th>
                <th width="50" align="center">Gsm</th>
                <th width="80" align="center">Dia/Width</th>  
                <th width="80" align="center">Product Qty</th>                  
                <th width="" align="center">Machine</th>
            </thead>
        <?php  
			$i=1;
			$order_arr=return_library_array("select id,order_no from subcon_ord_dtls",'id','order_no');
			$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
			$machine_arr=return_library_array( "select id, machine_no || '-' || brand as machine_name from  lib_machine_name",'id','machine_name');
			//echo "select id, mst_id, batch_no, order_id, process, fabric_description, color_id, gsm, dia_width, product_qnty, machine_id from subcon_production_dtls where product_type=6 and status_active=1 and mst_id='$data'";die;
			$sql =sql_select("select id, mst_id, batch_no, order_id, process, fabric_description, color_id, gsm, dia_width, product_qnty, machine_id from subcon_production_dtls where product_type=6 and status_active=1 and mst_id='$data'"); 
			foreach($sql as $row)
			{
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'load_php_data_to_form_dtls','requires/subcon_printing_production_controller');" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="100" align="center"><p><? echo $conversion_cost_head_array[$row[csf('process')]]; ?></p></td>
                    <td width="80" align="center"><? echo $row[csf('batch_no')]; ?></td>
                    <?
						$order_id=explode(',',$row[csf("order_id")]);
						$order_no='';
						foreach($order_id as $okey)
						{
							if($order_no=="") $order_no=$order_arr[$okey]; else $order_no .=",".$order_arr[$okey]; 
						}
                    ?>
                    <td width="200" align="center"><p><? echo $order_no; ?></p></td>
                    <td width="150" align="center"><p><? echo $row[csf('fabric_description')]; ?></p></td>
                    <td width="70" align="center"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
                    <td width="50" align="center"><? echo $row[csf('gsm')]; ?></td>
                    <td width="80" align="center"><p><? echo $row[csf('dia_width')]; ?></p></td>
                    <td width="80" align="right"><p><? echo $row[csf('product_qnty')]; ?>&nbsp;</p></td>
                    <td width="" align="center"><p><? echo $machine_arr[$row[csf('machine_id')]]; ?></p></td>
                </tr>
				<?php
                $i++;
            }
			?>
        </table>
	</div>
	<?
	exit();
}

if ($action=="load_php_data_to_form_dtls")
{
	$order_arr=return_library_array("select id,order_no from subcon_ord_dtls",'id','order_no');
	$process_arr=return_library_array("select id,main_process_id from subcon_ord_dtls",'id','main_process_id');
	$color_no_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$sql= "select id, batch_no, batch_ext, order_id, process, fabric_description, cons_comp_id, color_id, gsm, dia_width, product_qnty, floor_id, machine_id, start_hour, start_minutes, start_date, end_hour, end_minutes, end_date from subcon_production_dtls where id='$data'" ;
	$nameArray=sql_select($sql);
	foreach ($nameArray as $row)
	{
		$order_id=explode(',',$row[csf("order_id")]);
		$order_no='';
		$process_id='';
		foreach($order_id as $okey)
		{
			if($order_no=="") $order_no=$order_arr[$okey]; else $order_no .=",".$order_arr[$okey];
			if($process_id=="") $process_id=$process_arr[$okey]; else $process_id.=",".$process_arr[$okey]; 
		}
		echo "document.getElementById('txt_batch_no').value		 				= '".$row[csf("batch_no")]."';\n";
		echo "document.getElementById('txt_batch_ext_no').value		 			= '".$row[csf("batch_ext")]."';\n";
		//echo "$('#txt_batch_no').attr('disabled','true')".";\n";  
		echo "document.getElementById('txt_order_numbers').value		 		= '".$order_no."';\n";
		echo "document.getElementById('order_no_id').value						= '".$row[csf("order_id")]."';\n"; 
		echo "document.getElementById('process_id').value						= '".$process_id."';\n"; 
		echo "document.getElementById('cbo_process').value						= '".$row[csf("process")]."';\n";  
		echo "document.getElementById('txt_composition').value					= '".$row[csf("fabric_description")]."';\n"; 
		echo "document.getElementById('hidd_comp_id').value						= '".$row[csf("cons_comp_id")]."';\n"; 
		echo "document.getElementById('txt_color').value		 				= '".$color_no_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_gsm').value		 					= '".$row[csf("gsm")]."';\n";
		echo "document.getElementById('txt_dia_width').value		 			= '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_product_qnty').value            		= '".$row[csf("product_qnty")]."';\n";
		echo "load_drop_down( 'requires/subcon_printing_production_controller', document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_name').value, 'load_drop_down_floor', 'floor_td' );\n";
		echo "document.getElementById('cbo_floor_id').value		 				= '".$row[csf("floor_id")]."';\n";
		echo "load_drop_down( 'requires/subcon_printing_production_controller',document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_floor_id').value, 'load_drop_machine', 'machine_td' );\n";
		echo "document.getElementById('cbo_machine_name').value		 			= '".$row[csf("machine_id")]."';\n"; 
		echo "document.getElementById('txt_start_hour').value					= '".$row[csf("start_hour")]."';\n";
		echo "document.getElementById('txt_start_minutes').value				= '".$row[csf("start_minutes")]."';\n";   
		echo "document.getElementById('txt_start_date').value					= '".change_date_format($row[csf("start_date")])."';\n";  
		echo "document.getElementById('txt_end_hour').value		 				= '".$row[csf("end_hour")]."';\n";
		echo "document.getElementById('txt_end_minutes').value		 			= '".$row[csf("end_minutes")]."';\n";
		echo "show_list_view(document.getElementById('order_no_id').value+'_'+document.getElementById('process_id').value,'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_printing_production_controller','');\n";	
		
		echo "document.getElementById('txt_end_date').value		 				= '".change_date_format($row[csf("end_date")])."';\n";
		echo "document.getElementById('update_id_dtl').value            		= '".$row[csf("id")]."';\n";
	}	
	$qry_result=sql_select( "select id,order_id,quantity from subcon_production_qnty where dtls_id='$data'");
	foreach ($qry_result as $row)
	{
		if($order_qnty=="") $order_qnty=$row[csf("quantity")]; else $order_qnty.=",".$row[csf("quantity")];
	}
	echo "document.getElementById('txt_receive_qnty').value 	 					= '".$order_qnty."';\n";
	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'subcon_printing_production',1);\n";
	exit();
}

$color_library_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$process_printing="6";
		
	if ($operation==0)   // Insert Here==================================================================================================
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
		
		$new_return_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '','PRN', date("Y",time()), 5, "select id,prefix_no,prefix_no_num from  subcon_production_mst where entry_form=293 and  company_id=$cbo_company_id and product_type='$process_printing' $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		if(str_replace("'",'',$update_id)=="")
		{			
			$field_array="id,entry_form,prefix_no,prefix_no_num,product_no,product_type,basis,company_id,location_id,party_id,product_date,prod_chalan_no,remarks,inserted_by,insert_date";
			$id=return_next_id( "id","subcon_production_mst", 1 ) ; 
			$data_array="(".$id.",293,'".$new_return_no[1]."','".$new_return_no[2]."','".$new_return_no[0]."','".$process_printing."',".$cbo_receive_basis.",".$cbo_company_id.",".$cbo_location_name.",".$cbo_party_name.",".$txt_production_date.",".$txt_chal_no.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";  
			$rID=sql_insert("subcon_production_mst",$field_array,$data_array,0);
			$return_no=$new_return_no[0];
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="product_no*basis*company_id*location_id*party_id*product_date*prod_chalan_no*remarks*updated_by*update_date";
			$data_array="".$txt_production_id."*".$cbo_receive_basis."*".$cbo_company_id."*".$cbo_location_name."*".$cbo_party_name."*".$txt_production_date."*".$txt_chal_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$rID=sql_update("subcon_production_mst",$field_array,$data_array,"id",$update_id,0); 
			$return_no=str_replace("'",'',$txt_production_id);
		}
		$id1=return_next_id("id","subcon_production_dtls", 1 ) ; 
		//$machine_tbl_id=return_field_value("id","lib_machine_name", "machine_no=$cbo_machine_name and status_active =1 and is_deleted=0" );
		//$color_tbl_id = return_id($txt_color, $color_library_arr, "lib_color", "id,color_name");

		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_tbl_id = return_id( str_replace("'","",$txt_color), $color_library_arr, "lib_color", "id,color_name","293");
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_color[$color_tbl_id]=str_replace("'","",$txt_color);

			}
			else $color_tbl_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
		}
		else
		{
			$color_tbl_id=0;
		}

		$field_array2="id,mst_id,batch_no,batch_ext,order_id,product_type,process,fabric_description,cons_comp_id,color_id,gsm,dia_width,product_qnty,floor_id,machine_id,start_hour,start_minutes,start_date,end_hour,end_minutes,end_date,inserted_by,insert_date";
		$data_array2="(".$id1.",".$id.",".$txt_batch_no.",".$txt_batch_ext_no.",".$order_no_id.",'".$process_printing."',".$cbo_process.",".$txt_composition.",".$hidd_comp_id.",'".$color_tbl_id."',".$txt_gsm.",".$txt_dia_width.",".$txt_product_qnty.",".$cbo_floor_id.",".$cbo_machine_name.",".$txt_start_hour.",".$txt_start_minutes.",".$txt_start_date.",".$txt_end_hour.",".$txt_end_minutes.",".$txt_end_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
		//echo "INSERT INTO subcon_production_dtls (".$field_array2.") VALUES ".$data_array2; die;
		$rID2=sql_insert("subcon_production_dtls",$field_array2,$data_array2,0);
		//===========================================================================================================================================
		$data_array3="";
		$order_no=explode(',',str_replace("'","",$order_no_id));
		$receive_qnty=explode(',',str_replace("'","",$txt_receive_qnty));
		
		for($i=0; $i<count($order_no); $i++)
		{
			if($id_prod_qnty=="") $id_prod_qnty=return_next_id( "id", "subcon_production_qnty", 1 ); else $id_prod_qnty=$id_prod_qnty+1;
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array3.="$add_comma(".$id_prod_qnty.",".$id.",".$id1.",".$order_no[$i].",".$receive_qnty[$i].",'".$process_printing."')";
		}
		$field_array3="id, mst_id, dtls_id, order_id, quantity, product_type";
		if($data_array3!="")
		{
			$rID3=sql_insert("subcon_production_qnty",$field_array3,$data_array3,1);
		}
		//echo "10**INSERT INTO subcon_production_dtls (".$field_array2.") VALUES ".$data_array2; //die;
		//echo "10**".$rID."**".$rID2."**".$rID3; die;
		//===========================================================================================================================================
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2 && $rID3)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$id1)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}	
		disconnect($con);                                
		die;
	}
	else if ($operation==1)   // Update Here================================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$process_printing="6";
		
		$field_array="product_no*basis*company_id*location_id*party_id*product_date*prod_chalan_no*remarks*updated_by*update_date";
		$data_array="".$txt_production_id."*".$cbo_receive_basis."*".$cbo_company_id."*".$cbo_location_name."*".$cbo_party_name."*".$txt_production_date."*".$txt_chal_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$rID=sql_update("subcon_production_mst",$field_array,$data_array,"id",$update_id,0);
		
		//$machine_tbl_id=return_field_value("id","lib_machine_name", "machine_no=$cbo_machine_name and status_active =1 and is_deleted=0" );
		//$color_tbl_id = return_id( str_replace("'","",$txt_color), $color_library_arr, "lib_color", "id,color_name");
		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_tbl_id = return_id( str_replace("'","",$txt_color), $color_library_arr, "lib_color", "id,color_name","293");
				//echo $$txtColorName.'='.$color_id.'<br>';
				$new_array_color[$color_tbl_id]=str_replace("'","",$txt_color);

			}
			else $color_tbl_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
		}
		else
		{
			$color_tbl_id=0;
		}
			
		$field_array2="batch_no*batch_ext*order_id*process*fabric_description*cons_comp_id*color_id*gsm*dia_width*product_qnty*floor_id*machine_id*start_hour*start_minutes*start_date*end_hour*end_minutes*end_date*updated_by*update_date";
		$data_array2="".$txt_batch_no."*".$txt_batch_ext_no."*".$order_no_id."*".$cbo_process."*".$txt_composition."*".$hidd_comp_id."*'".$color_tbl_id."'*".$txt_gsm."*".$txt_dia_width."*".$txt_product_qnty."*".$cbo_floor_id."*".$cbo_machine_name."*".$txt_start_hour."*".$txt_start_minutes."*".$txt_start_date."*".$txt_end_hour."*".$txt_end_minutes."*".$txt_end_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$rID2=sql_update("subcon_production_dtls",$field_array2,$data_array2,"id",$update_id_dtl,0); 
		
		//===========================================================================================================================================
		if(str_replace("'","",$update_id_qnty)!=="")
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
			$rID3=execute_query(bulk_update_sql_statement( "subcon_production_qnty","id",$field_array_up,$data_array_up,$update_arr ));
		}
		else
		{
			$rID4=execute_query( "delete from subcon_production_qnty where dtls_id=$update_id_dtl",1);
			$data_array3="";
			$order_no=explode(',',str_replace("'","",$order_no_id));
			$receive_qnty=explode(',',str_replace("'","",$txt_receive_qnty));
			
			for($i=0; $i<count($order_no); $i++)
			{
				if($id_prod_qnty=="") $id_prod_qnty=return_next_id( "id", "subcon_production_qnty", 1 ); else $id_prod_qnty=$id_prod_qnty+1;
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array3.="$add_comma(".$id_prod_qnty.",".$update_id.",".$update_id_dtl.",".$order_no[$i].",".$receive_qnty[$i].",'".$process_printing."')";
			}
			$field_array3="id,mst_id,dtls_id,order_id,quantity,product_type";
			if($data_array3!="")
			{
				$rID3=sql_insert("subcon_production_qnty",$field_array3,$data_array3,1);
			}
		}
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id_dtl)."**".str_replace("'",'',$txt_production_id);
			}
			else if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id_dtl)."**".str_replace("'",'',$txt_production_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id_dtl)."**".str_replace("'",'',$txt_production_id);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2 && $rID3 && $rID4)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id_dtl)."**".str_replace("'",'',$txt_production_id);
			}
			else if($rID && $rID2 && $rID3)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id_dtl)."**".str_replace("'",'',$txt_production_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}	
		disconnect($con);
 		die;
	}
	else if ($operation==2)   // Delete Here =====================================================================================================================
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
				echo "2**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id_dtl);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id_dtl);
			}
		}
		disconnect($con);
		die;
	}
}
?>