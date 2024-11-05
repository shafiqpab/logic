<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//=======================DROP DOWN LOCATION==================
if ($action=="load_drop_down_location")
{
	//echo $data;
	echo create_drop_down( "cbo_location", 152, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );     	 
}

if ($action=="load_drop_down_party_name")
{
    echo create_drop_down( "cbo_party_name", 152, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and b.tag_company='$data' and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "",'' ); 
	exit();	
}

if ($action=="load_drop_down_item")
{
	$ex_data=explode('_',$data);
	//echo $ex_data[2];
	//echo "select distinct b.id, b.const_comp from subcon_ord_breakdown a, lib_subcon_charge b where a.item_id=b.id and a.order_id='$ex_data[0]' and b.status_active=1 and b.is_deleted=0";
	if ($ex_data[1]==1 || $ex_data[1]==5)
	{
		echo create_drop_down( "txt_item_id", 130, $garments_item,"", 1, "-- Select Item --", $selected, "",'' ); 
	}
	else
	{
		echo create_drop_down( "txt_item_id", 130, "select distinct b.id, b.const_comp from subcon_ord_breakdown a, lib_subcon_charge b where a.item_id=b.id and a.order_id='$ex_data[0]' and b.status_active=1 and b.is_deleted=0","id,const_comp", 1, "-- Select Item --", $selected, "fnc_production_qty(this.value);",'' ); 
	}
	exit();	
}

if ($action=="dalivery_item_popup")
{
	//extract($_REQUEST);	
	$data=explode('_',$data);
	if($data[1]==2 || $data[1]==3 || $data[1]==4 || $data[1]==6 || $data[1]==7)
	{
		echo load_html_head_contents("Material Description Form", "../../", 1, 1,'',1,'');
		?>	
		<script>
			function js_set_value(id)
			{ 
				document.getElementById('hidd_item_id').value=id;
				parent.emailwindow.hide();
			}
        </script>
        </head>
        <body>
            <form name="searchpofrm"  id="searchpofrm">
            <input type="hidden" id="hidd_item_id" />
            <div style="width:100%;">
                <table cellspacing="0" width="100%" class="rpt_table">
                <thead>
                    <th width="50">SL</th>
                    <th width="200" align="center">Const. Compo.</th>
                    <th width="100" align="center">Width/Dia type </th>
                    <th width="100" align="center"> Color </th>                    
                    <th width="" align="center">Sub Process</th>
                </thead>
                </table>
            </div>
            <div style="width:100%;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
                <table cellspacing="0" width="100%" class="rpt_table">
                    <?php  
						$i=1;
                        $sql=sql_select("select id,item_id from subcon_ord_breakdown where order_id='$data[0]'");
                        $item_id=array();
                        foreach($sql as $row)
                        {
							$item_id[]=$row[csf('item_id')];
                        }
                        $abc=implode(',',$item_id);
                        
						$color_library_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
						$sql_result = sql_select("select a.id,a.const_comp,a.width_dia_id,b.color_id,c.process_id from lib_subcon_charge a,subcon_ord_breakdown b,subcon_ord_dtls c where a.id=b.item_id and b.order_id=c.id and a.id in($abc) and c.id='$data[0]'");
						foreach($sql_result as $row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('const_comp')] ?>');" > 
                                <td width="50" align="center"><? echo $i; ?></td>
                                <td width="200" align="center"><p><? echo $row[csf('const_comp')]; ?></p></td>
                                <td width="100" align="center"><? echo $fabric_typee[$row[csf('width_dia_id')]]; ?></td>
                                <td width="100" align="center"><? echo $color_library_arr[$row[csf('color_id')]]; ?></td>
								<?
									$sub='';
									$sub_process=explode(',',$row[csf('process_id')]);
									if($sub_process[0]!=="")$coma=","; else $coma="";
									foreach($sub_process as $process_row)
									{
										$sub.=$conversion_cost_head_array[$process_row].$coma;
									}
								?>
								<td width="" align="center"><? echo $sub; ?></td>
							</tr>
							<?
							$i++;
						}
                    ?>
                </table>
            </div>
            </form>
        </body>           
        <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
        </html>
            <?	
		exit();
	}
	else if($data[1]==1 || $data[1]==5  || $data[1]==8 || $data[1]==9)
	{
		echo load_html_head_contents("Material Description Form", "../../", 1, 1,'',1,'');
		//extract($_REQUEST);
		?>
		<script>
			function js_set_value(id)
			{ 
				document.getElementById('hidd_item_id').value=id;
				parent.emailwindow.hide();
			}
        </script>
        </head>
        <body>
            <div align="center" style="width:100%;" >
            <form name="searchpofrm"  id="searchpofrm">
            <input type="hidden" id="hidd_item_id" />
            <div style="width:100%;">
                 <table cellspacing="0" width="100%" class="rpt_table">
                    <thead>
                        <th width="50" >SL</th>
                        <th width="200" >Garments Item</th>
                        <th width="100" align="center"> Color </th>                    
                        <th width="" align="center">Sub Process</th>
                    </thead>
                </table>
            </div>
            <div style="width:100%;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
                <table cellspacing="0" width="100%" class="rpt_table">
                    <?php  
						$i=1;
						$color_library_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id","color_name");
						$sql_result = sql_select("select b.item_id,b.color_id,c.process_id from subcon_ord_breakdown b,subcon_ord_dtls c where b.order_id=c.id and b.order_id='$data[0]'");
						foreach($sql_result as $row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('item_id')]."_".$garments_item[$row[csf('item_id')]] ?>');" > 
                                <td width="50" align="center"><? echo $i; ?></td>
                                <td width="200" align="center"><p><? echo $garments_item[$row[csf('item_id')]]; ?></p></td>
                                <td width="100" align="center"><? echo $color_library_arr[$row[csf('color_id')]]; ?></td>
                                <?
                                    $sub='';
                                    $sub_process=explode(',',$row[csf('process_id')]);
                                    if($sub_process[0]!=="")$coma=","; else $coma="";
                                    foreach($sub_process as $process_row)
                                    {
                                        $sub.=$conversion_cost_head_array[$process_row].$coma;
                                    }
								?>
                                <td width="" align="center"><? echo $sub; ?></td><!--$conversion_cost_head_array[-->
							</tr>
							<?php
							$i++;
						}
						?>
                    </table>
                </div>
            </form>
            </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?	
	}
	exit();
}

if ($action=="order_number_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1,'');
	$ex_data=explode('_',$data);
	$company_id=$ex_data[0];
	$party_id=$ex_data[1];
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		
		function search_populate(str)
		{
			//alert(str); 
			if(str==0) 
			{
				document.getElementById('search_by_th_up').innerHTML="Order No";
			}
			else if(str==1) 
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref.";
			}
			else
			{
				document.getElementById('search_by_th_up').innerHTML="Job No";
			}																																													
		}
	
		function js_set_value(id)
		{ 
			$("#hidden_order_value").val(id);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="130">Search By</th>
                        <th  width="180" align="center" id="search_by_th_up">Enter Order Number</th>
                        <th width="200">Date Range</th>
                        <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="130">  
                                <? 
									$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Job No");
									echo create_drop_down( "cbo_search_by", 130, $searchby_arr,"", 1, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
                                ?>
                            </td>
                            <td width="180" align="center" id="search_by_td">				
                                <input type="text" style="width:170px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />			
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td> 
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+<? echo $party_id; ?>, 'create_order_search_list_view', 'search_div', 'subcon_delivery_entry_controller', 'setFilterGrid(\'tbl_order_list\',-1)')" style="width:100px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="center" height="40" valign="middle">
                                <? echo load_month_buttons(1);  ?>
                                <input type="hidden" id="hidden_order_value">
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

if($action=="create_order_search_list_view")
{
 	$ex_data = explode("_",$data);
	$cbo_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$party = $ex_data[5];

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($cbo_search_by)==0)
			$sql_cond = " and a.order_no like '%".trim($txt_search_common)."%'";
		else if(trim($cbo_search_by)==1)
			$sql_cond = " and a.cust_style_ref like '%".trim($txt_search_common)."%'";
		else if(trim($cbo_search_by)==2)
			$sql_cond = " and b.job_no_prefix_num='$txt_search_common'"; 	
 	}
	
	if($db_type==0)
	{ 
		if ($txt_date_from!="" &&  $txt_date_to!="") $sql_cond .= "and a.delivery_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'"; 
	}
	else
	{
		if ($txt_date_from!="" &&  $txt_date_to!="") $sql_cond .= "and a.delivery_date between '".change_date_format($txt_date_from, "", "",1)."' and '".change_date_format($txt_date_to, "", "",1)."'"; 
	}


	//if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and a.delivery_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	
	if($db_type==0)
	{
		$year_cond= "year(b.insert_date)as year";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(b.insert_date,'YYYY') as year";
	}
	
	$sql = "select a.id, a.order_rcv_date, a.order_no, a.order_uom, a.main_process_id, a.order_quantity, b.party_id, a.cust_style_ref, b.subcon_job, b.job_no_prefix_num, $year_cond from  subcon_ord_dtls a, subcon_ord_mst b where  a.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0 and b.company_id='$company' and b.party_id='$party' $sql_cond order by b.id DESC";
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (4=>$production_process,5=>$party_arr);
	echo  create_list_view("tbl_order_list", "Job,Year,Delivery Date,Order No,Process,Party,Order Qty, Style", "60,60,70,100,100,120,100,100","750","250",0, $sql , "js_set_value", "id", "", 1, "0,0,0,0,main_process_id,party_id,0,0", $arr , "job_no_prefix_num,year,order_rcv_date,order_no,main_process_id,party_id,order_quantity,cust_style_ref", "requires/subcon_delivery_entry_controller",'','0,0,3,0,0,0,2,0') ;
	exit();
}

if($action=="populate_data_from_search_popup")
{
	//echo "select a.id, a.delivery_date as order_date, a.main_process_id, a.order_no, a.order_quantity, a.order_uom, a.cust_style_ref, b.company_id, b.party_id, b.subcon_job from  subcon_ord_mst b, subcon_ord_dtls a where a.job_no_mst=b.subcon_job and a.id='$data'";
	$res = sql_select("select a.id, a.delivery_date as order_date, a.main_process_id, a.order_no, a.order_quantity, a.order_uom, a.cust_style_ref, b.company_id, b.party_id, b.subcon_job from  subcon_ord_mst b, subcon_ord_dtls a where a.main_process_id<>3 and a.job_no_mst=b.subcon_job and a.id='$data'"); 
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
 	foreach($res as $result)
	{
		echo "document.getElementById('txt_order_no').value 					= '".$result[csf("order_no")]."';\n";
	    echo "document.getElementById('txt_order_id').value            			= '".$result[csf("id")]."';\n";
		echo "document.getElementById('cbo_process_name').value 				= '".$result[csf("main_process_id")]."';\n";
		echo "document.getElementById('txt_order_date').value 					= '".change_date_format($result[csf("order_date")])."';\n";
		echo "document.getElementById('txt_ordr_qnty').value 					= '".$result[csf("order_quantity")]."';\n";
		echo "document.getElementById('txt_uom').value 							= '".$unit_of_measurement[$result[csf("order_uom")]]."';\n";
		echo "document.getElementById('txt_style').value 						= '".$result[csf("cust_style_ref")]."';\n";
/*		$order_id=$result[csf("id")];
		//echo "select sum(delivery_qty) as delivery_qty from subcon_delivery_dtls where order_id='$order_id'";
		$total_delivery_qnty=return_field_value("sum(delivery_qty) as delivery_qty","subcon_delivery_dtls","order_id='$order_id'","delivery_qty");
		$yet_to_delivery_qnty=$result[csf("order_quantity")]-$total_delivery_qnty;
		echo "document.getElementById('txt_cumul_delivery_qty').value 			= '".$total_delivery_qnty."';\n";
		echo "document.getElementById('txt_del_panding_qnty').value 			= '".$yet_to_delivery_qnty."';\n";
*/	}
	exit();		
}

if($action=="show_fabric_desc_listview")
{
	$data=explode('_',$data);
	$order_id=$data[0];
	$process_id=$data[1];
	//echo $process_id;
	$item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
	$gsm_arr=return_library_array( "select id,gsm from lib_subcon_charge",'id','gsm');
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	
	$order_qty_array=array();
	$sql_qty_order="Select a.id, b.item_id, color_id, sum(b.qnty) as qnty from subcon_ord_dtls a, subcon_ord_breakdown b where a.id=b.order_id and a.status_active=1 and a.is_deleted=0 group by  a.id, b.item_id, color_id";
	$order_data_sql=sql_select($sql_qty_order);
	foreach($order_data_sql as $row)
	{
		if ($process_id==1 || $process_id==5 || $process_id==8 || $process_id==9 || $process_id==10 || $process_id==11)
		{
			$order_qty_array[$row[csf('id')]][$row[csf('item_id')]]['item_id']=$row[csf('qnty')];
			$order_qty_array[$row[csf('id')]][$row[csf('item_id')]]['color_id']=$row[csf('color_id')];
		}
		else
		{
			$order_qty_array[$row[csf('id')]][$row[csf('item_id')]]=$row[csf('qnty')];
		}
	}
	//var_dump($order_qty_array);

		//$sql = "select b.item_id, sum(a.production_qnty) as production_qnty, sum(b.qnty) as qnty from subcon_gmts_prod_dtls a, subcon_ord_breakdown b, subcon_ord_dtls c where c.main_process_id=5 and b.order_id=c.id and a.order_id='$order_id' and b.item_id=a.gmts_item_id and a.order_id=c.id and a.production_type=2 group by b.item_id";
	
	
	$batch_color_arr=array();
	$batch_sql="select batch_no, extention_no, color_id from pro_batch_create_mst where entry_form=36 and status_active=1 and is_deleted=0";
	$result_batch_sql=sql_select($batch_sql);
	foreach($result_batch_sql as $row)
	{
		$batch_color_arr[$row[csf('batch_no')]][$row[csf('extention_no')]]=$row[csf('color_id')];
	}
	//var_dump($batch_color_arr);
	
	$delivery_qty_array=array();
	if ($process_id==1 || $process_id==5 || $process_id==8 || $process_id==9 || $process_id==10 || $process_id==11)
	{
		$delivery_sql="select order_id, item_id, sum(delivery_qty) as delivery_qty from subcon_delivery_dtls where order_id='$order_id' and process_id='$process_id' group by order_id, item_id";
		$delivery_data_sql=sql_select($delivery_sql);
		foreach($delivery_data_sql as $row)
		{
			$delivery_qty_array[$row[csf('order_id')]][$row[csf('item_id')]]=$row[csf('delivery_qty')];
		}
	}
	else if ($process_id==2 )
	{
		$delivery_sql="select item_id, gsm, dia, sum(delivery_qty) as delivery_qty from subcon_delivery_dtls where order_id='$order_id' and process_id='$process_id' group by item_id,  gsm, dia";
		$delivery_data_sql=sql_select($delivery_sql);
		foreach($delivery_data_sql as $row)
		{
			$delivery_qty_array[$row[csf('item_id')]][$row[csf('gsm')]][$row[csf('dia')]]=$row[csf('delivery_qty')];
		}
	}
	elseif ($process_id==3 || $process_id==4 || $process_id==6 || $process_id==7)
	{
		$delivery_sql="select item_id, dia, sum(delivery_qty) as delivery_qty from subcon_delivery_dtls where order_id='$order_id' and process_id='$process_id' group by item_id, dia";
		$delivery_data_sql=sql_select($delivery_sql);
		foreach($delivery_data_sql as $row)
		{
			$delivery_qty_array[$row[csf('item_id')]][$row[csf('dia')]]=$row[csf('delivery_qty')];
		}
	}
	//print_r($delivery_qty_array);
	
	if ($process_id==1)
	{
		$sql = "select order_id, gmts_item_id, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=1 group by order_id, gmts_item_id";
	}
	else if ($process_id==5 )
	{
		$sql = "select order_id, gmts_item_id, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=2 group by order_id, gmts_item_id";
		
	}
	else if ($process_id==10 )
	{
		$sql = "select order_id, gmts_item_id, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=3 group by order_id, gmts_item_id";
		
	}
	else if ($process_id==11 )
	{
		$sql = "select order_id, gmts_item_id, sum(production_qnty) as production_qnty from subcon_gmts_prod_dtls where order_id='$order_id' and production_type=4 group by order_id, gmts_item_id";
		
	}
	else if($process_id==2)
	{
		$sql="select cons_comp_id, fabric_description, gsm, dia_width, sum(product_qnty) as production_qnty from subcon_production_dtls where order_id='$order_id' and product_type=2 and status_active=1 and is_deleted=0 group by cons_comp_id, fabric_description, gsm, dia_width";
	}
	else 
	{
		 if($db_type==0)
		 { 
			 $sql = "select a.batch_no, a.batch_ext, a.gsm, a.dia_width, a.cons_comp_id, a.fabric_description, sum(b.quantity) as production_qnty from subcon_production_dtls a, subcon_production_qnty b where b.order_id in ($order_id) and a.id=b.dtls_id and a.product_type='$process_id' group by a.batch_no, a.batch_ext, a.cons_comp_id, a.gsm, a.dia_width, a.fabric_description";
		 }
		 elseif($db_type==2)
		 {
			$sql = "select a.batch_no, a.batch_ext, a.gsm, a.dia_width, a.cons_comp_id, a.fabric_description, sum(b.quantity) as production_qnty from subcon_production_dtls a, subcon_production_qnty b where b.order_id in ($order_id) and a.id=b.dtls_id and a.product_type='$process_id' group by a.batch_no, a.batch_ext, a.cons_comp_id, a.gsm, a.dia_width, a.fabric_description";
		 }
	}
	$sql_result = sql_select($sql);	
	
	if($process_id==2)
	{
		?>
		 <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400">
			<thead>
				<th width="15">SL</th>
				<th width="100">Item Description</th>
				<th width="35">GSM</th>
				<th width="35">Dia</th>
				<th width="40">Prod. Qty</th>
				<th width="60">Delv. Qty</th>
				<th>Bal. Qty</th>
			</thead>
			<tbody>
				<? 
				$i=1;
				foreach($sql_result as $row)
				{  
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$availabe_delivery_qty=$row[csf('production_qnty')]-$delivery_qty_array[$row[csf('cons_comp_id')]][$row[csf('gsm')]][$row[csf('dia_width')]];	
					if($availabe_delivery_qty!=0)
					{
				 ?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('cons_comp_id')]."**".$row[csf('fabric_description')]."**".$availabe_delivery_qty."**".$row[csf('gsm')]."**".$row[csf('dia_width')]."**".$row[csf('batch_no')]."**".$row[csf('batch_ext')]."**".'0'."**".'0'."**".$row[csf('width_dia_type')]."**".$row[csf('process')]; ?>")' style="cursor:pointer" >
						<td><? echo $i; ?></td>
						<td><? echo $row[csf('fabric_description')]; ?></td>
						<td><? echo $row[csf('gsm')]; ?></td>
						<td><? echo $row[csf('dia_width')]; ?></td>
						<td align="right"><? echo number_format($row[csf('production_qnty')],2,'.',''); ?></td>
						<td align="right"><? echo number_format($delivery_qty_array[$row[csf('cons_comp_id')]][$row[csf('gsm')]][trim($row[csf('dia_width')])],2,'.',''); ?></td>
						<td align="right"><? echo number_format($availabe_delivery_qty,2,'.',''); ?></td>
					</tr>
				<? 
					$i++; 
					}
				} 
				?>
			</tbody>
		</table>
	<?   
	}
	else if($process_id==3 || $process_id==4 || $process_id==6 || $process_id==7)
	{
		?>
		 <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400">
			<thead>
				<th width="15">SL</th>
                <th width="40">Batch</th>
                <th width="20">Ex.</th>
                <th width="50">Color</th>
				<th width="100">Fabric Description</th>
				<th width="30">GSM</th>
				<th width="30">Dia</th>
				<th width="40">Prod. Qty</th>
				<th width="55">Delv. Qty</th>
				<th>Bal. Qty</th>
			</thead>
			<tbody>
				<? 
				$i=1;
				foreach($sql_result as $row)
				{  
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$item_name=explode(',',$row[csf('fabric_description')]);	
					
					$gsm_val=$gsm_arr[$row[csf('item_id')]];
					
					$del_qty=$delivery_qty_array[$row[csf('cons_comp_id')]][$row[csf('dia_width')]];
					$availabe_delivery_qty=$row[csf('production_qnty')]-$del_qty;	
					if($availabe_delivery_qty!=0)
					{		
					 ?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('cons_comp_id')]."**".$item_name[0]."**".$availabe_delivery_qty."**".$row[csf('gsm')]."**".$row[csf('dia_width')]."**".$row[csf('batch_no')]."**".$row[csf('batch_ext')]."**".$batch_color_arr[$row[csf('batch_no')]][$row[csf('batch_ext')]]."**".$color_arr[$batch_color_arr[$row[csf('batch_no')]][$row[csf('batch_ext')]]]."**".$row[csf('width_dia_type')]."**".$row[csf('process')]; ?>")' style="cursor:pointer" >
							<td><? echo $i; ?></td>
							<td><? echo $row[csf('batch_no')]; ?></td>
							<td><? echo $row[csf('batch_ext')]; ?></td>
							<td><? echo $color_arr[$batch_color_arr[$row[csf('batch_no')]][$row[csf('batch_ext')]]]; ?></td>
							<td><? echo $item_name[0]; ?></td>
							<td><? echo $row[csf('gsm')]; ?></td>
							<td><? echo $row[csf('dia_width')]; ?></td>
							<td align="right"><? echo number_format($row[csf('production_qnty')],2,'.',''); ?></td>
							<td align="right"><? echo number_format($del_qty,2,'.',''); ?></td>
							<td align="right"><? echo number_format($row[csf('production_qnty')]-$del_qty,2,'.',''); ?></td>
						</tr>
					<? 
					$i++; 
					}
				} 
				?>
			</tbody>
		</table>
	<?   
	}
	else if($process_id==1 || $process_id==5 || $process_id==8 || $process_id==9 || $process_id==10 || $process_id==11)
	{
	?>
		 <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400">
			<thead>
				<th width="15">SL</th>
				<th width="150">Item Description</th>
				<th width="80">Prod. Qty</th>
				<th width="80">Delv. Qty</th>
				<th>Bal. Qty</th>
			</thead>
			<tbody>
				<? 
				$i=1;
				foreach($sql_result as $row)
				{  
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$item_name=$garments_item[$row[csf('gmts_item_id')]];
						$gsm_val='';
						
					$availabe_delivery_qty=$row[csf('production_qnty')]-$delivery_qty_array[$row[csf('order_id')]][$row[csf('gmts_item_id')]];			
				 ?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('gmts_item_id')]."**".$item_name."**".$availabe_delivery_qty."**".$row[csf('gsm')]."**".$row[csf('dia_width')]."**".$row[csf('batch_no')]."**".$row[csf('batch_ext')]."**".'0'."**".'0'."**".$row[csf('width_dia_type')]."**".$row[csf('process')]; ?>")' style="cursor:pointer" >
						<td><? echo $i; ?></td>
						<td><? echo $item_name; ?></td>
						<td align="right"><? echo number_format($row[csf('production_qnty')],2,'.',''); ?></td>
						<td align="right"><? echo number_format($delivery_qty_array[$row[csf('order_id')]][$row[csf('gmts_item_id')]],2,'.',''); ?></td>
						<td align="right"><? echo number_format($row[csf('production_qnty')]-$delivery_qty_array[$row[csf('order_id')]][$row[csf('gmts_item_id')]],2,'.',''); ?></td>
					</tr>
				<? 
				$i++; 
				} 
				?>
			</tbody>
		</table>
	<?   
	}	
	exit();
}

if($action=="delivery_entry_list_view")
{
	?>	
	<div style="width:810px;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="table_body">
            <thead>
                <th width="30">SL</th>
                <th width="90">Order</th>
                <th width="80">Challan No</th>
                <th width="160">Delivery Item</th>
                <th width="75">Delivery Date</th>
                <th width="80">Delivery Qty</th>                    
                <th width="70">Carton /Roll</th>
                <th width="110">Forwarder</th>  
                <th width="">Bill Status</th>
            </thead>
            <tbody>
		<?php  
			$i=1;
			$party_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
			$order_id_arr=return_library_array( "select id,order_no from  subcon_ord_dtls",'id','order_no');
			$lib_item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
			$inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
			$prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');
			$batch_array=array();
			$batch_sql="select a.batch_no, a.extention_no, b.id, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$batch_sql_result=sql_select($batch_sql);
			foreach($batch_sql_result as $row)
			{
				$batch_array[$row[csf('batch_no')]][$row[csf('extention_no')]][$row[csf('id')]]=$row[csf('item_description')];
			}
			$sql = sql_select("select a.id, a.delivery_date, a.challan_no, a.transport_company, a.forwarder, b.batch_no, b.batch_ext, b.id as dtls_id, b.item_id, b.delivery_qty, b.carton_roll,  b.bill_status, b.order_id, b.process_id from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.id='$data'");  
			foreach($sql as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('dtls_id')].'_'.$row[csf('process_id')]; ?>','load_php_data_to_form_delivery','requires/subcon_delivery_entry_controller');" > 
                    <td width="30" align="center"><? echo $i; ?></td>
                    <?
                    $process_id_val=$row[csf('process_id')];
                    $bill_row_status=array(0=>"Active",1=>"In Active");
                    if($process_id_val==1 || $process_id_val==5 || $process_id_val==8 || $process_id_val==9)
                    {
                        $item_name=$garments_item[$row[csf('item_id')]];
                    }
					else if($process_id_val==2)
					{
						$item_name=$lib_item_arr[$row[csf('item_id')]];	
					}
                    else
                    {
						$item_name=$batch_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('item_id')]];
                    }
                    ?>
                    <td width="90" align="center"><p><? echo $order_id_arr[$row[csf('order_id')]]; ?></p></td>
                     <td width="80" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                    <td width="160" align="center"><p><? echo $item_name; ?></p></td>
                    <td width="75" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td><!--change_date_format()-->
                    <td width="80" align="right"><? echo $row[csf('delivery_qty')]; ?>&nbsp;</td>
                    <td width="70" align="center"><? echo $row[csf('carton_roll')]; ?></td>
                    <td width="115" align="center"><p><? echo $party_arr[$row[csf('forwarder')]]; ?></p></td>
                    <td width="" align="center"><p><? echo $bill_row_status[$row[csf('bill_status')]]; ?></p></td>
                </tr>
                <?php
                $i++;
			}
			?>
            </tbody>
		</table>
       </div>
	<?
	exit();	
}

if($action=="load_php_data_to_form_delivery")
{
	$ex_data=explode('_',$data);
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$lib_item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
	
	$batch_array=array();
	$batch_sql="select a.batch_no, a.extention_no, b.id, b.fabric_from, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$batch_sql_result=sql_select($batch_sql);
	foreach($batch_sql_result as $row)
	{
		//$batch_array[$row[csf('batch_no')]][$row[csf('extention_no')]][$row[csf('id')]]['fabric_from']=$row[csf('fabric_from')];
		$batch_array[$row[csf('batch_no')]][$row[csf('extention_no')]][$row[csf('id')]]['item_description']=$row[csf('item_description')];
	}
	
	$order_array=array();
	$sql_order=sql_select("select id, order_no, main_process_id, order_rcv_date, order_quantity, order_uom, cust_style_ref from subcon_ord_dtls where status_active=1 and is_deleted=0 ");
	
	foreach ($sql_order as $row)
	{	
		$order_array[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		$order_array[$row[csf("id")]]['main_process_id']=$row[csf("main_process_id")];
		$order_array[$row[csf("id")]]['order_rcv_date']=$row[csf("order_rcv_date")];
		$order_array[$row[csf("id")]]['order_quantity']=$row[csf("order_quantity")];
		$order_array[$row[csf("id")]]['order_uom']=$row[csf("order_uom")];
		$order_array[$row[csf("id")]]['cust_style_ref']=$row[csf("cust_style_ref")];
	}
	if($db_type==0)
	{
		$bill_info=return_field_value("concat(b.delivery_id,'**',a.bill_no) as delivery_info", "subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b", "a.id=b.mst_id and b.delivery_id='$ex_data[0]' ","delivery_info");
	}
	elseif($db_type==2)
	{
		$bill_info=return_field_value("b.delivery_id || '**' || a.bill_no as delivery_info", "subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b", "a.id=b.mst_id and b.delivery_id='$ex_data[0]' ","delivery_info");
	}
	$nameArray =sql_select("select id, order_id, process_id, sub_process_id, item_id, gsm, dia, batch_no, batch_ext, color_id, width_dia_type, delivery_qty, carton_roll, remarks from  subcon_delivery_dtls where id='$ex_data[0]'");
	
	foreach ($nameArray as $row)
	{	
		$process_id_val=$ex_data[1];
		if($process_id_val==1 || $process_id_val==5 || $process_id_val==8 || $process_id_val==9 || $process_id_val==10 || $process_id_val==11)
		{
			$item_id_arr=$garments_item;
		}
		else if($process_id_val==2)
		{
			$item_name=$lib_item_arr[$row[csf('item_id')]];	
		}
		else
		{
			$item_name=$batch_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('item_id')]]['item_description'];	
		}
		echo "document.getElementById('txt_order_id').value		 					= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value		 					= '".$order_array[$row[csf("order_id")]]['order_no']."';\n";
		echo "document.getElementById('cbo_process_name').value		 				= '".$order_array[$row[csf("order_id")]]['main_process_id']."';\n";
		echo "document.getElementById('txt_order_date').value		 				= '".change_date_format($order_array[$row[csf("order_id")]]['order_rcv_date'])."';\n";
		echo "document.getElementById('txt_ordr_qnty').value		 				= '".$order_array[$row[csf("order_id")]]['order_quantity']."';\n";
		echo "document.getElementById('txt_uom').value		 						= '".$unit_of_measurement[$order_array[$row[csf("order_id")]]['order_uom']]."';\n";
		echo "document.getElementById('txt_style').value		 					= '".$order_array[$row[csf("order_id")]]['cust_style_ref']."';\n";
		echo "document.getElementById('txt_dalivery_item').value		 			= '".$item_name."';\n";	
		echo "document.getElementById('txt_item_id').value		 					= '".$row[csf("item_id")]."';\n";
		echo "document.getElementById('txt_delivery_qnty').value		 			= '".$row[csf("delivery_qty")]."';\n"; 
		echo "document.getElementById('txt_pre_delivery_qnty').value		 		= '".$row[csf("delivery_qty")]."';\n";
		echo "document.getElementById('txt_gsm').value		 						= '".$row[csf("gsm")]."';\n"; 
		echo "document.getElementById('txt_dia').value		 						= '".$row[csf("dia")]."';\n";
		echo "document.getElementById('txt_batch_no').value		 					= '".$row[csf("batch_no")]."';\n"; 
		echo "document.getElementById('txt_ext_no').value		 					= '".$row[csf("batch_ext")]."';\n";
		echo "document.getElementById('txt_color').value		 					= '".$color_arr[$row[csf("color_id")]]."';\n"; 
		echo "document.getElementById('txt_color_id').value		 					= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('hidden_dia_type').value		 				= '".$row[csf("width_dia_type")]."';\n";
		echo "document.getElementById('hid_sub_process').value		 				= '".$row[csf("sub_process_id")]."';\n";
		$bill_delivery=explode("**",$bill_info);
		echo "document.getElementById('bill_info').value		 					= '".$bill_info."';\n";
		echo "active_inactive(document.getElementById('bill_info').value);\n";
 
		 
		echo "document.getElementById('txt_carton_roll_no').value					= '".$row[csf("carton_roll")]."';\n"; 
		echo "show_list_view(document.getElementById('txt_order_id').value+'_'+document.getElementById('cbo_process_name').value, 'show_fabric_desc_listview','list_fabric_desc_container','requires/subcon_delivery_entry_controller','');\n";	
		
		echo "document.getElementById('txt_remarks').value		 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('update_id_dtls').value		 				= '".$row[csf("id")]."';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_subcon_delivery_entry',1,1);\n";
	}
	exit();
}

if($action=="delivery_qty_check")
{
	$data=explode("**",$data);
	$sql="select a.id, sum(b.product_qnty) as product_qnty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and  b.order_id='$data[0]' and a.product_type='$data[1]' and b.cons_comp_id='$data[2]' and a.status_active=1 and a.is_deleted=0 group by b.order_id, b.process";
	$delivery_sql="select sum(delivery_qnty) as delivery_qnty from  subcon_delivery where order_id='$data[0]' and process_id='$data[1]' and item_id='$data[2]' and status_active=1 and is_deleted=0 group by order_id, process_id, item_id";
	$data_array=sql_select($sql);
	$delivery_array=sql_select($delivery_sql);
	
	echo $data_array[0][csf('product_qnty')].'_'.$delivery_array[0][csf('delivery_qnty')];	
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	 
	if ($operation==0)  // Insert Start Here======================================================================================================================== 
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
		
		$return_delivery_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'DVY', date("Y",time()), 5, "select id, delivery_prefix, delivery_prefix_num from subcon_delivery_mst where company_id=$cbo_company_name $year_cond=".date('Y',time())." order by id desc ", "delivery_prefix", "delivery_prefix_num" ));
		
		
		//echo $update_id;die;
		if(str_replace("'",'',$update_id)==0 || str_replace("'",'',$update_id)=='')
		{
			$id=return_next_id( "id"," subcon_delivery_mst", 1 ) ; 
			$field_array="id, delivery_prefix, delivery_prefix_num, delivery_no, company_id, location_id, party_id, challan_no, delivery_date, forwarder, transport_company, inserted_by, insert_date, status_active, is_deleted";
			
			$challan=str_replace("'",'',$txt_challan_no);
			//echo $challan;die;
			if ($challan!='' && $challan!=0)
			{
				$challan_no=$txt_challan_no;
			}
			else
			{
				$challan_no=$id;
			}
			$data_array="(".$id.",'".$return_delivery_no[1]."','".$return_delivery_no[2]."','".$return_delivery_no[0]."',".$cbo_company_name.",".$cbo_location.",".$cbo_party_name.",".$challan_no.",".$txt_delivery_date.",".$cbo_forwarder.",".$txt_transport_company.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)"; 
			//echo "insert into subcon_delivery_mst (".$field_array.") values ".$data_array;//die;
			$rID=sql_insert("subcon_delivery_mst",$field_array,$data_array,0);
			$return_no=$return_delivery_no[0];
		}
		else
		{
			$challan=str_replace("'",'',$txt_challan_no);
			//echo $challan;die;
			if ($challan!='' && $challan!=0)
			{
				$challan_no=$txt_challan_no;
			}
			else
			{
				$challan_no=$update_id;
			}			
			$field_array="location_id*party_id*challan_no*delivery_date*forwarder*transport_company*updated_by*update_date";
			$data_array="".$cbo_location."*".$cbo_party_name."*".$txt_challan_no."*".$txt_delivery_date."*".$cbo_forwarder."*".$txt_transport_company."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			
			//echo "0***"."insert into subcon_delivery_mst (".$field_array.") values ".$data_array;die;
			$id=$update_id;
			$rID=sql_update("subcon_delivery_mst",$field_array,$data_array,"id",$update_id,0);
			$return_no=$txt_sys_id;
		}
		
		$dtlsid=return_next_id( "id"," subcon_delivery_dtls", 1 ) ; 
		$field_array_dtls="id, mst_id, order_id, process_id, sub_process_id, item_id, gsm, dia, batch_no, batch_ext, color_id, width_dia_type, delivery_qty, carton_roll, remarks";
		
		$data_array_dtls="(".$dtlsid.",".$id.",".$txt_order_id.",".$cbo_process_name.",".$hid_sub_process.",".$txt_item_id.",".$txt_gsm.",".$txt_dia.",".$txt_batch_no.",".$txt_ext_no.",".$txt_color_id.",".$hidden_dia_type.",".$txt_delivery_qnty.",".$txt_carton_roll_no.",".$txt_remarks.")"; 
		//echo "insert into subcon_delivery_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID1=sql_insert("subcon_delivery_dtls",$field_array_dtls,$data_array_dtls,1);		
			
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$challan_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$challan_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id);
			}
		}	
		disconnect($con);
		die; 
	}
	else if ($operation==1)   // Update Here==============================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$challan=str_replace("'",'',$txt_challan_no);
		//echo $challan;die;
		if ($challan!='' && $challan!=0)
		{
			$challan_no=$txt_challan_no;
		}
		else
		{
			$challan_no=$update_id;
		}			
		$field_array="location_id*party_id*challan_no*delivery_date*forwarder*transport_company*updated_by*update_date";
		$data_array="".$cbo_location."*".$cbo_party_name."*".$txt_challan_no."*".$txt_delivery_date."*".$cbo_forwarder."*".$txt_transport_company."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		
		//echo "insert into subcon_delivery_mst (".$field_array.") values ".$data_array;die;txt_gsm*txt_dia
		$rID=sql_update("subcon_delivery_mst",$field_array,$data_array,"id",$update_id,0);
		$return_no=$txt_sys_id;
		$field_array_dtls="order_id*process_id*sub_process_id*item_id*gsm*dia*batch_no*batch_ext*color_id*width_dia_type*delivery_qty*carton_roll*remarks";
		
		$data_array_dtls="".$txt_order_id."*".$cbo_process_name."*".$hid_sub_process."*".$txt_item_id."*".$txt_gsm."*".$txt_dia."*".$txt_batch_no."*".$txt_ext_no."*".$txt_color_id."*".$hidden_dia_type."*".$txt_delivery_qnty."*".$txt_carton_roll_no."*".$txt_remarks.""; 
		
		$rID2=sql_update("subcon_delivery_dtls",$field_array_dtls,$data_array_dtls,"id",$update_id_dtls,1);// die;
		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$challan_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$challan_no);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$challan_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$challan_no);
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
		$challan=str_replace("'",'',$txt_challan_no);
		//echo $challan;die;
		if ($challan!='' && $challan!=0)
		{
			$challan_no=$txt_challan_no;
		}
		else
		{
			$challan_no=$update_id;
		}			
		$bill_info=return_field_value("delivery_id", "subcon_inbound_bill_dtls", "delivery_id=$update_id_dtls","delivery_id");
		if($bill_info!=0 || $bill_info!="") 
		{
			echo "13**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_sys_id)."**".str_replace("'",'',$challan_no);
			disconnect($con);
			die;
		}
		
		//echo $bill_info;die;
		$rID=execute_query( "delete from subcon_delivery_dtls where id=$update_id_dtls",0);
/*		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("subcon_delivery_mst",$field_array,$data_array,"id","".$update_id."",1);
*/		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_sys_id)."**".str_replace("'",'',$challan_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_sys_id)."**".str_replace("'",'',$challan_no);
			}
		}
		elseif($db_type==2)
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_sys_id)."**".str_replace("'",'',$challan_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_sys_id)."**".str_replace("'",'',$challan_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="delivery_id_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_delivery_id').value=id;
			parent.emailwindow.hide();
		}		
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="deliverysearch_1"  id="deliverysearch_1" autocomplete="off">
                <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="140">Company Name</th>
                        <th width="110">Delivery ID</th>
                        <th width="80">Year</th>
                        <th width="200">Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('deliverysearch_1','search_div','','','','');" /></th>           
                    </thead>
                    <tbody>
                        <tr>
                            <td> <input type="hidden" id="selected_delivery_id"><? //$data=explode("_",$data); ?>  <!--  echo $data;-->
								<? echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data, "",0); ?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:95px" />
                            </td>
                            <td> 
                                <?
                                    $selected_year=date("Y");
                                    echo create_drop_down( "cbo_year", 60, $year,"", 1, "-Year-", $selected_year, "",0 );
                                ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_year').value, 'create_delivery_search_list_view', 'search_div', 'subcon_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" align="center" valign="top" id=""><div id="search_div"></div> </td>
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

if($action=="create_delivery_search_list_view")
{
	$data=explode('_',$data);
	//echo $data[3];
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!="" &&  $data[2]!="") $delivery_date = "and delivery_date between '".change_date_format($data[1], "mm-dd-yyyy", "/",1)."' and '".change_date_format($data[2], "mm-dd-yyyy", "/",1)."'"; else $delivery_date ="";
	if ($data[3]!='') $delivery_id_cond=" and delivery_prefix_num= '$data[3]'"; else $delivery_id_cond="";
	//$trans_Type="issue";
	if($db_type==0)
	{
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
	$sql= "select id, delivery_no, company_id, delivery_prefix_num, $year_cond, location_id, party_id, challan_no, delivery_date, forwarder, transport_company from subcon_delivery_mst where status_active=1 and is_deleted=0 $company $delivery_date $delivery_id_cond order by id DESC";

	$result = sql_select($sql);
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name');
	
	?> 
    <script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_po_list',-1);
        });

	</script>   
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="570" class="rpt_table">
            <thead>
                <th width="50" >SL</th>
                <th width="70" >Delivery ID</th>
                <th width="60" >Year</th>
                <th width="120" >Party</th>
                <th width="120" >Challan No</th>
                <th width="70" >Delivery Date</th>
                <th>Location</th>
            </thead>
     	</table>
     </div>
     <div style="width:570px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="550" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>);" > 
						<td width="50" align="center"><?php echo $i; ?></td>
						<td width="70" align="center"><?php echo $row[csf("delivery_prefix_num")]; ?></td>
                        <td width="60" align="center"><?php echo $row[csf("year")]; ?></td>		
						<td width="120" align="center"><?php echo $party_arr[$row[csf("party_id")]]; ?></td>
						<td width="120"><?php echo $row[csf("challan_no")];  ?></td>	
						<td width="70"><?php echo $row[csf("delivery_date")]; ?></td>
						<td ><?php echo $location_arr[$row[csf("location_id")]];?> </td>	
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

if ($action=="load_php_data_to_form")
{
	//echo "select id, delivery_no, company_id, location_id, party_id, challan_no, delivery_date, forwarder, transport_company from subcon_delivery_mst where id='$data' and status_active=1 and is_deleted=0";die;
	$nameArray=sql_select( "select id, delivery_no, company_id, location_id, party_id, challan_no, delivery_date, forwarder, transport_company from subcon_delivery_mst where id='$data' and status_active=1 and is_deleted=0 " ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_sys_id').value 			= '".$row[csf("delivery_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/subcon_delivery_entry_controller', $('#cbo_company_name').val(), 'load_drop_down_location', 'location_td' );";
		echo "document.getElementById('cbo_location').value			= '".$row[csf("location_id")]."';\n";
		echo "load_drop_down( 'requires/subcon_delivery_entry_controller', $('#cbo_company_name').val(), 'load_drop_down_party_name', 'party_td' );";
		echo "document.getElementById('cbo_party_name').value		= '".$row[csf("party_id")]."';\n"; 
		echo "$('#cbo_party_name').attr('disabled','true')".";\n"; 
		echo "document.getElementById('txt_challan_no').value		= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_delivery_date').value	= '".change_date_format($row[csf("delivery_date")])."';\n"; 
		echo "document.getElementById('txt_transport_company').value 		= '".$row[csf("transport_company")]."';\n";   
		echo "document.getElementById('cbo_forwarder').value	= '".$row[csf("forwarder")]."';\n"; 
		echo "document.getElementById('update_id').value			= '".$row[csf("id")]."';\n"; 
		//echo "set_button_status(0, '".$_SESSION['page_permission']."','fnc_material_issue',1);\n";
	}
	exit();	
}

if($action=="subcon_delivery_entry_print")
{
	extract($_REQUEST);
	$ex_data=explode('*',$data);
	$company=$ex_data[0];
	$update_id=$ex_data[1];
	$sys_id=$ex_data[2];
	//print_r ($data);
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");//die;
	//$item_arr=return_library_array( "select cons_comp_id, color_name from lib_color", "cons_comp_id", "color_name");
	
	//$color_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
	$sql_job_po="select a.id, a.order_no, a.main_process_id, a.cust_buyer, b.party_id, b.subcon_job, a.process_id from  subcon_ord_dtls a, subcon_ord_mst b where a.job_no_mst=b.subcon_job and b.is_deleted=0 and b.status_active=1 and a.status_active=1 and a.is_deleted=0";
	$job_po_array=array();
	$result_job_po=sql_select($sql_job_po);
	foreach($result_job_po as $row)
	{
		$job_po_array[$row[csf('id')]]['order_no']=$row[csf('order_no')];
		$job_po_array[$row[csf('id')]]['main_process_id']=$row[csf('main_process_id')];
		$job_po_array[$row[csf('id')]]['process_id']=$row[csf('process_id')];
		$job_po_array[$row[csf('id')]]['party_id']=$row[csf('party_id')]; 
		$job_po_array[$row[csf('id')]]['subcon_job']=$row[csf('subcon_job')]; 
		$job_po_array[$row[csf('id')]]['cust_buyer']=$row[csf('cust_buyer')]; 
	}
	//var_dump($job_po_array);
	$recChallan_arr=array();
	if($db_type==0)
	{
		$sql_rec="select b.order_id, group_concat(distinct(a.chalan_no)) as chalan_no, group_concat(distinct(b.grey_dia)) as grey_dia from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 group by b.order_id";
	}
	else if ($db_type==2)
	{
		$sql_rec="select b.order_id, wm_concat(distinct(cast(a.chalan_no as varchar2(500)))) as chalan_no, wm_concat(distinct(cast(b.grey_dia as varchar2(500)))) as grey_dia from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 group by b.order_id";
	}
	$result_sql_rec=sql_select($sql_rec);
	foreach($result_sql_rec as $row)
	{
		$recChallan_arr[$row[csf('order_id')]]['chalan_no']=$row[csf('chalan_no')];
		$recChallan_arr[$row[csf('order_id')]]['grey_dia']=$row[csf('grey_dia')];
	}
	
	$sql="select id, party_id, challan_no, delivery_date, forwarder, transport_company from subcon_delivery_mst where id='$update_id' and company_id='$company' and status_active=1 and is_deleted=0";
	
	$dataArray=sql_select($sql);
?>
    <div style="width:930px;">
   <table width="100%" cellpadding="0" cellspacing="0" >
       <tr>
           <td width="70" align="right"> 
               <img  src='../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='70%' width='70%' />
           </td>
           <td>
    <table width="800" cellspacing="0" align="center">
        <tr>
            <td align="center" style="font-size:x-large"><strong ><? echo $company_library[$company]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td  align="center" style="font-size:14px">  
				<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website from lib_company where id=$company and status_active=1 and is_deleted=0"); 
					foreach ($nameArray as $result)
					{ 
					?>
						<? echo $result[csf('plot_no')]; ?> &nbsp; 
                        <? echo $result[csf('level_no')]?> &nbsp; 
                        <? echo $result[csf('road_no')]; ?> &nbsp; 
                        <? echo $result[csf('block_no')];?> &nbsp; 
                        <? echo $result[csf('city')];?> &nbsp; 
                        <? echo $result[csf('zip_code')]; ?> &nbsp; 
                        <? echo $result[csf('province')];?> &nbsp; 
                        <? echo $country_arr[$result[csf('country_id')]]; ?>&nbsp; <br>
                        <? echo $result[csf('contact_no')];?> &nbsp; 
                        <? echo $result[csf('email')];?> &nbsp; 
                        <? echo $result[csf('website')]; 
					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td align="center" style="font-size:18px"><strong><? echo $production_process[$job_po_array[$dataArray[0][csf('order_id')]]['main_process_id']]; ?> Delivery Challan</strong></td>
        </tr>
        </table>
        </td>
        </tr>
    </table>
    <table width="900" cellspacing="0" align="right">
            <tr><td colspan="6" align="center"><hr></hr></td></tr>
        <tr>
			<? 
                $party_add=$dataArray[0][csf('party_id')];
                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
                foreach ($nameArray as $result)
                { 
                    $address="";
                    if($result!="") $address=$result['address_1'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
            ?> 
        	<td width="300" rowspan="4" valign="top" colspan="2" style="font-size:12px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.$address;  ?></strong></td>
            <td width="125" style="font-size:12px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td width="125" style="font-size:12px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
        </tr>
        <tr>
            <td style="font-size:12px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
            <td style="font-size:12px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
        </tr>
        <tr><td colspan="6">&nbsp;</td> </tr>
    </table>
    <br>
    <div style="width:100%; height:930px">
			<?
			$gray_dia_array=array(); $prod_dia_array=array();
			if ($db_type==0)
			{
				$prod_dia_sql="select a.batch_no, a.batch_ext, a.cons_comp_id, a.process, a.dia_width from subcon_production_dtls a, subcon_production_qnty b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 group by a.batch_no, a.batch_ext, a.cons_comp_id, a.process ";
			}
			else if ($db_type==2)
			{
				$prod_dia_sql="select a.batch_no, a.batch_ext, a.cons_comp_id, cast(a.process as varchar2(100)) as process, a.dia_width from subcon_production_dtls a, subcon_production_qnty b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 group by a.batch_no, a.batch_ext, a.cons_comp_id, a.dia_width, a.process";
			}
			$result_prod_dia_sql=sql_select($prod_dia_sql);
			foreach($result_prod_dia_sql as $row)
			{
				$prod_dia_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('cons_comp_id')]]['dia_width']=$row[csf('dia_width')];
				$prod_dia_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('cons_comp_id')]]['process']=$row[csf('process')];
			}
			//print_r($prod_dia_array);
			$inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
			$prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');
			
			
			$mst_id=$dataArray[0][csf('id')];
			$sql_dtls="select batch_no, batch_ext, color_id, width_dia_type, dia, order_id, sub_process_id, item_id, sum(delivery_qty) as delivery_qty, sum(carton_roll) as carton_roll, gsm, remarks from subcon_delivery_dtls where mst_id='$mst_id' and process_id in (3,4) group by batch_no, batch_ext, color_id, width_dia_type, gsm, dia, order_id, sub_process_id, item_id, remarks, id order by batch_no, batch_ext, color_id";
			
			$i=1; $k=1; $width_dia_type_array=array(); $sub_process_array=array(); $batch_array=array(); $color_array=array();
			
			$dtls_value=sql_select($sql_dtls);
			
			if(count($dtls_value)>0)
			{
				?>
                <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
                    <thead bgcolor="#dddddd" align="center" style="font-size:12px">
                        <th width="30">SL</th>
                        <th width="70" align="center">Order No</th>
                        <th width="60" align="center">Cust. Buyer</th>
                        <th width="70" align="center">Rec. Challan</th>
                        <th width="110" align="center">Description</th>
                        <th width="50" align="center">GSM</th>
                        <th width="50" align="center">G/Dia</th>
                        <th width="50" align="center">F/Dia</th>
                        <th width="50" align="center">Roll</th>
                        <th width="80" align="center">Grey Qty</th>
                        <th width="80" align="center">Fin. Qty</th>
                        <th width="" align="center">Remarks</th>
                    </thead>
                    <?
                        $sql_batch="Select a.batch_no, a.extention_no, b.fabric_from, b.po_id, b.id, b.width_dia_type, b.item_description, sum(b.roll_no) as roll_no, sum(b.batch_qnty) as batch_qnty, b.rec_challan from  pro_batch_create_mst a, pro_batch_create_dtls b where a.entry_form=36 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.batch_no, a.extention_no, a.process_id, b.fabric_from, b.item_description, b.po_id, b.id, b.width_dia_type, b.rec_challan";
                        $batch_full_array=array();
                        $result_batch=sql_select($sql_batch);
                        foreach($result_batch as $row)
                        {
                            //$batch_array[$row[csf('po_id')]]['id']=$row[csf('id')];  *batch_no*batch_ext*color_id
                            $item=explode(',',$row[csf('item_description')]);
                            $batch_full_array[$row[csf('batch_no')]][$row[csf('extention_no')]][$row[csf('po_id')]][$row[csf('id')]]['item_description']=$row[csf('item_description')];
							$batch_full_array[$row[csf('batch_no')]][$row[csf('extention_no')]][$row[csf('po_id')]][$row[csf('id')]]['rec_challan']=$row[csf('rec_challan')];
                            $batch_full_array[$row[csf('batch_no')]][$row[csf('extention_no')]][$row[csf('po_id')]][$row[csf('id')]]['dia']=$item[2];
                            $batch_full_array[$row[csf('batch_no')]][$row[csf('extention_no')]][$row[csf('po_id')]][$row[csf('id')]]['roll_no']=$row[csf('roll_no')];
                            $batch_full_array[$row[csf('batch_no')]][$row[csf('extention_no')]][$row[csf('po_id')]][$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
							$batch_full_array[$row[csf('batch_no')]][$row[csf('extention_no')]][$row[csf('po_id')]][$row[csf('id')]]['fabric_from']=$row[csf('fabric_from')];
							$batch_full_array[$row[csf('batch_no')]][$row[csf('extention_no')]][$row[csf('po_id')]][$row[csf('id')]]['width_dia_type']=$row[csf('width_dia_type')];
                        }
                       // var_dump($batch_full_array);				
				
					foreach($dtls_value as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
						if($row[csf('process_id')]==1 || $row[csf('process_id')]==5 || $row[csf('process_id')]==8 || $row[csf('process_id')]==9)
						{
							$item_name=$garments_item[$row[csf('item_id')]];
						}
						else
						{
							$item_name=$batch_full_array[$row[csf('batch_no')]][$row[csf('extention_no')]][$row[csf('po_id')]][$row[csf('item_id')]]['item_description'];
/*							if($batch_full_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('order_id')]][$row[csf('item_id')]]['fabric_from']==1)
							{
								$item_name=$inv_item_arr[$row[csf('item_id')]];
							}
							else
							{
								$item_name=$prod_item_arr[$row[csf('item_id')]];
							}
*/							//$item_id_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');	
						}
						//$subprocess=$subprocess_arr[$row[csf('order_id')]];
						//rsort($prod_dia_array[$row[csf('order_id')]][$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('item_id')]]['process']);
						$process_id=explode(',',$prod_dia_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('item_id')]]['process']);
						
						$process_val='';
						foreach ($process_id as $val)
						{
							//rsort($val);
							if($process_val=='') $process_val=$conversion_cost_head_array[$val]; else $process_val.="+".$conversion_cost_head_array[$val];
							//echo $val;
						}
						
						if ($row[csf('batch_no')]!=0 || $row[csf('batch_ext')]!='')
						{
							$batch_no=$row[csf('batch_no')].' '." Ex: ". $row[csf('batch_ext')];
						}
						else
						{
							$batch_no=$row[csf('batch_no')];
						}
						$gsm_dia=explode(',',$batch_full_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('order_id')]][$row[csf('item_id')]]['item_description']);
						if ($gsm_dia[1]!='' && $gsm_dia[1]!=0 )
						{
							$item_des=$row[csf('batch_no')];
							$batch_dia=$gsm_dia[2];
						}
						else
						{
							$item_des=$row[csf('batch_no')];
							$batch_dia=$gsm_dia[2];
						}
						
						$recChallan_no='';
						$rec_challan=explode(',',$recChallan_arr[$row[csf('order_id')]]['chalan_no']);
						foreach($rec_challan as $val)
						{
							if ($recChallan_no=='') $recChallan_no=$val; else $recChallan_no.=', '.$val;
						}
						//print_r($process_id);
						$chack_string=$row[csf('batch_no')].$row[csf('batch_ext')].$row[csf('color_id')].$prod_dia_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('item_id')]]['process'];
						if(!in_array($chack_string,$sub_process_array))
						{
							if($i!=1)
							{
							?>
							<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-size:12px">
								<th width="30">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="60">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="50"><strong>Total</strong></th>
								<th width="50" align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</th>
								<th width="80" align="right"><? echo number_format($tot_grey_qty,2,'.',''); ?>&nbsp;</th>
								<th width="80" align="right"><? echo number_format($tot_finish_qty,2,'.',''); ?>&nbsp;</th>
								<th width="" >&nbsp;</th>
							</tr>
						<?
								unset($tot_roll);
								unset($tot_grey_qty);
								unset($tot_finish_qty);
							}	
						?>
							<tr height="30"><td colspan="12" style="font-size:14px" bgcolor="#CCCCAA"><p><?php echo "<i>Batch No: </i><u>".$batch_no; echo ";</u> <i>Color: </i><u>".$color_arr[$row[csf('color_id')]];  echo "; </u><i>Process: </i><u>".$process_val."</u>"; ?></p></td></tr>
						<?	
							$sub_process_array[$i]=$chack_string;
							//unset($sub_process_array);
						}
						$dia_type='';
						if($batch_full_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==1)
						{
							$dia_type="Open";
						}
						else if($batch_full_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==2)
						{
							$dia_type="Tube";
						}
						else if($batch_full_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('order_id')]][$row[csf('item_id')]]['width_dia_type']==3)
						{
							$dia_type="Niddle";
						}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px">
						<td width="30"><? echo $i; ?></td>
						<td width="70"><p><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></p></td>
						<td width="60"><p><? echo $job_po_array[$row[csf('order_id')]]['cust_buyer']; ?></p></td>
						<td width="70"><p><? echo $batch_full_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('order_id')]][$row[csf('item_id')]]['rec_challan']; ?></p></td>
						<td width="110"><p><? echo $gsm_dia[0].'('.$dia_type.')'; ?></p></td>
						<td width="50" align="center"><p><? echo $row[csf('gsm')]; ?></p></td>
						<td width="50" align="center"><p><? echo $gsm_dia[2]; ?></p></td>
						<td width="50" align="center"><p><? echo $row[csf('dia')];//$prod_dia_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('item_id')]]['dia_width']; ?></p></td>
						<td width="50" align="right"><? echo $row[csf('carton_roll')]; ?>&nbsp;</td>
						<td width="80" align="right"><p><? echo number_format($batch_full_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_qnty'],2,'.',''); ?>&nbsp;</p></td>
						<td width="80" align="right"><p><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?>&nbsp;</p></td>
						<td width=""><p><? echo $row[csf('remarks')]; ?></p></td>
					</tr>
					<?
					$tot_roll+=$row[csf('carton_roll')];
					$tot_grey_qty+=$batch_full_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_qnty'];
					$tot_finish_qty+=$row[csf('delivery_qty')];
					
					$grand_tot_roll+=$row[csf('carton_roll')];
					$grand_tot_grey_qty+=$batch_full_array[$row[csf('batch_no')]][$row[csf('batch_ext')]][$row[csf('order_id')]][$row[csf('item_id')]]['batch_qnty'];
					$grand_tot_finish_qty+=$row[csf('delivery_qty')];
					$i++;
					}
				?>
					<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-size:12px">
						<th width="30">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50"><strong>Total</strong></th>
						<th width="50" align="right"><? echo number_format($tot_roll,2,'.',''); ?>&nbsp;</th>
						<th width="80" align="right"><? echo number_format($tot_grey_qty,2,'.',''); ?>&nbsp;</th>
						<th width="80" align="right"><? echo number_format($tot_finish_qty,2,'.',''); ?>&nbsp;</th>
						<th width="" >&nbsp;</th>
					</tr>
					<tfoot style="font-size:14px">
						<th width="30">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="50"><strong>Grand Total</strong></th>
						<th width="50" align="right"><? echo number_format($grand_tot_roll,2,'.',''); ?>&nbsp;</th>
						<th width="80" align="right"><? echo number_format($grand_tot_grey_qty,2,'.',''); ?>&nbsp;</th>
						<th width="80" align="right"><? echo number_format($grand_tot_finish_qty,2,'.',''); ?>&nbsp;</th>
						<th width="" >&nbsp;</th>
					</tfoot>
				</table> <br>
				 <?
			}
			
			$sql_kniting="select dia, order_id, item_id, delivery_qty, carton_roll, gsm, remarks from subcon_delivery_dtls where mst_id='$mst_id' and process_id=2 ";
			$sql_kniting_result=sql_select($sql_kniting);
			if(count($sql_kniting_result)>0)
			{
?>
                <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
                    <thead bgcolor="#dddddd" align="center" style="font-size:12px">
                        <th width="30">SL</th>
                        <th width="80" align="center">Order No</th>
                        <th width="120" align="center">Process</th>
                        <th width="70" align="center">Cust. Buyer</th>
                        <th width="120" align="center">Rec. Challan</th>
                        <th width="150" align="center">Description</th>
                        <th width="60" align="center">Roll</th>
                        <th width="80" align="center">Prod. Qty</th>
                        <th width="" align="center">Remarks</th>
                    </thead>                
                <?
				$lib_item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				$i=1;
				foreach($sql_kniting_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
					$process_id=explode(',',$job_po_array[$row[csf('order_id')]]['process_id']);
					$process_val='';
					foreach ($process_id as $val)
					{
						if($process_val=='') $process_val=$conversion_cost_head_array[$val]; else $process_val.="+".$conversion_cost_head_array[$val];
					}
					
					$recChallan_no='';
					$rec_challan=explode(',',$recChallan_arr[$row[csf('order_id')]]['chalan_no']);
					foreach($rec_challan as $val)
					{
						if ($recChallan_no=='') $recChallan_no=$val; else $recChallan_no.=', '.$val;
					}
						
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px">
						<td width="30"><? echo $i; ?></td>
						<td width="80"><p><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></p></td>
                        <td width="120"><p><? echo $process_val; ?></p></td>
						<td width="70"><p><? echo $job_po_array[$row[csf('order_id')]]['cust_buyer']; ?></p></td>
						<td width="120"><p><? echo $recChallan_no; ?></p></td>
						<td width="150"><p><? echo $lib_item_arr[$row[csf('item_id')]]; ?></p></td>
                        <td width="60" align="right"><? echo number_format($row[csf('carton_roll')],2,'.',''); ?></td>
						<td width="80" align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
						<td><p><? echo $row[csf('remarks')]; ?></p></td>
					</tr>
					<?
					$grand_tot_roll+=$row[csf('carton_roll')];
					$grand_tot_finish_qty+=$row[csf('delivery_qty')];
					$i++;
					}
				?>
					<tfoot>
						<th width="30">&nbsp;</th>
						<th width="80">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="70">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="150" align="right"><strong>Grand Total</strong></th>
                        <th width="60" align="right"><? echo number_format($grand_tot_roll,2,'.',''); ?></th>
                        <th width="80" align="right"><? echo number_format($grand_tot_finish_qty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
					</tfoot>
				</table> <br>
				 <?	
			}
			
			$sql_gmts="select order_id, item_id, delivery_qty, carton_roll, process_id, remarks from subcon_delivery_dtls where mst_id='$mst_id' and process_id in (1,5,8,9) ";
			
			$i=1; $process_name_array=array();
			
			$sql_gmts_result=sql_select($sql_gmts);
			if(count($sql_gmts_result)>0)
			{
				?>
                <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
                    <thead bgcolor="#dddddd" align="center" style="font-size:12px">
                        <th width="30">SL</th>
                        <th width="80" align="center">Order No</th>
                        <th width="150" align="center">Process</th>
                        <th width="70" align="center">Cust. Buyer</th>
                        <th width="120" align="center">Rec. Challan</th>
                        <th width="180" align="center">Description</th>
                        <th width="80" align="center">Prod. Qty</th>
                        <th width="" align="center">Remarks</th>
                    </thead>                
                <?
				foreach($sql_gmts_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
					$process_id=explode(',',$job_po_array[$row[csf('order_id')]]['process_id']);
					$process_val='';
					foreach ($process_id as $val)
					{
						if($process_val=='') $process_val=$conversion_cost_head_array[$val]; else $process_val.="+".$conversion_cost_head_array[$val];
					}
					
					$recChallan_no='';
					$rec_challan=explode(',',$recChallan_arr[$row[csf('order_id')]]['chalan_no']);
					foreach($rec_challan as $val)
					{
						if ($recChallan_no=='') $recChallan_no=$val; else $recChallan_no.=', '.$val;
					}
						
					if(!in_array($row[csf('process_id')],$process_name_array))
					{
						if($i!=1)
						{
						?>
						<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-size:14px">
							<th width="30">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="150">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="180" align="right"><strong>Sub-Total</strong></th>
							<th width="80"><? echo number_format($tot_finish_qty,2,'.',''); ?></th>
							<th>&nbsp;</th>
						</tr>
					<?
							unset($tot_finish_qty);
						}	
					?>
						<tr height="30"><td colspan="8" style="font-size:14px" bgcolor="#CCCCAA"><p><? echo $production_process[$row[csf('process_id')]]; ?></p></td></tr>
					<?	
						$process_name_array[$i]=$row[csf('process_id')];
						//unset($sub_process_array);
					}
						
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px">
						<td width="30"><? echo $i; ?></td>
						<td width="80"><p><? echo $job_po_array[$row[csf('order_id')]]['order_no']; ?></p></td>
                        <td width="150"><p><? echo $process_val; ?></p></td>
						<td width="70"><p><? echo $job_po_array[$row[csf('order_id')]]['cust_buyer']; ?></p></td>
						<td width="120"><p><? echo $recChallan_no; ?></p></td>
						<td width="180"><p><? echo $garments_item[$row[csf('item_id')]]; ?></p></td>
						<td width="80" align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
						<td><p><? echo $row[csf('remarks')]; ?></p></td>
					</tr>
					<?
					$tot_finish_qty+=$row[csf('delivery_qty')];
					
					$grand_tot_finish_qty+=$row[csf('delivery_qty')];
					$i++;
					}
				?>
					<tr class="tbl_bottom" bgcolor="#CCCCCC" style="font-size:14px">
						<th width="30">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="120">&nbsp;</th>
                        <th width="180" align="right"><strong>Sub-Total</strong></th>
                        <th width="80"><? echo number_format($tot_finish_qty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
					</tr>
					
					<tfoot style="font-size:14px">
						<th width="30">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="150">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="120">&nbsp;</th>
                        <th width="180" align="right"><strong>Grand Total</strong></th>
                        <th width="80"><? echo number_format($grand_tot_finish_qty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
					</tfoot>
				</table> <br>
				 <?			
			}
            echo signature_table(46, $company, "900px");
         ?>
          </div> 
    	<table width="900" cellspacing="0" >
        	<tr><td colspan="6">
            
            
            </td></tr>
            <tr><td colspan="6" align="center">,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,</td></tr>
            <tr>
				<td colspan="6"> 
                    <table cellpadding="0" cellspacing="0" >
                        <tr>
                            <td width="70" align="right"> 
                                <img  src='../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='70%' width='70%' />
                            </td>
                            <td align="right">
                                <table width="800px" cellspacing="0" align="center">
                                    <tr>
                                        <td align="center" style="font-size:18px"><strong ><? echo $company_library[$company]; ?></strong></td>
                                    </tr>
                                    <tr class="form_caption">
                                        <td  align="center" style="font-size:14px">  
                                        <?
											$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, country_id, province, city, zip_code, contact_no, email, website from lib_company where id=$company and status_active=1 and is_deleted=0"); 
											foreach ($nameArray as $result)
											{ 
												echo $result[csf('plot_no')]; ?> &nbsp; 
												<? echo $result[csf('level_no')]?> &nbsp; 
												<? echo $result[csf('road_no')]; ?> &nbsp; 
												<? echo $result[csf('block_no')];?> &nbsp; 
												<? echo $result[csf('city')];?> &nbsp; 
												<? echo $result[csf('zip_code')]; ?> &nbsp; 
												<? echo $result[csf('province')];?> &nbsp; 
												<? echo $country_arr[$result[csf('country_id')]]; ?>&nbsp; <br>
												<? echo $result[csf('contact_no')];?> &nbsp; 
												<? echo $result[csf('email')];?> &nbsp; 
												<? echo $result[csf('website')]; 
											}
                                        ?> 
                                        </td>  
                                    </tr>
                                    <tr>
                                        <td align="center" style="font-size:16px"><strong><u>Gate Pass</u></strong></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>               
    			</td>
            </tr>
            <tr>
                <? 
                $party_add=$dataArray[0][csf('party_id')];
                $nameArray=sql_select( "select address_1, web_site, buyer_email, country_id from  lib_buyer where id=$party_add"); 
                foreach ($nameArray as $result)
                { 
                    $address="";
                    if($result!="") $address=$result['address_1'];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
                ?> 
                <td width="300" rowspan="4" valign="top" colspan="2" style="font-size:12px"><strong>Party : <? echo $buyer_library[$party_add].'<br>'.$address;  ?></strong></td>
                <td width="120" style="font-size:12px"><strong>Challan No :</strong></td><td width="170px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td width="120" style="font-size:12px"><strong>Delivery Date :</strong></td><td width="170px"><? echo change_date_format($dataArray[0][csf('delivery_date')]); ?></td>
            </tr>
            <tr>
                <td style="font-size:12px"><strong>Transport Com.:</strong></td><td><? echo $dataArray[0][csf('transport_company')]; ?></td>
                <td style="font-size:12px"><strong>Forwarder:</strong></td><td><? echo $supplier_library[$dataArray[0][csf('forwarder')]]; ?></td>
            </tr>
            <tr>
                <td colspan="6" align="right" style="font-size:14px">
                    <table cellspacing="0" width="350"  border="1" rules="all" class="rpt_table" >
                        <thead bgcolor="#dddddd" align="center">
                            <th width="150">Roll</th>
                            <th width="150">Weight</th>
                        </thead>
                        <tbody>
                        	<tr>
                            	<td align="center"><? echo $grand_tot_roll; ?></td>
                               <td align="center"><? echo $grand_tot_finish_qty; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        &nbsp;<br>
        <table cellspacing="0" width="900" >
        	<thead>
            	<tr><th colspan="9">&nbsp;</th></tr>
            	<tr height="16px" style="font-size:12px">
                	<th width="50">&nbsp;</th>
                    <th width="100"><hr>Receive By</th>
                    <th width="50">&nbsp;</th>
                    <th width="100"><hr>Audited By</th>
                    <th width="50">&nbsp;</th>
                    <th width="100"><hr>Prepared By</th>
                    <th width="50">&nbsp;</th>
                    <th width="100"><hr>Gate Entry</th>
                    <th width="50">&nbsp;</th>
                </tr>
            </thead>
        </table>
	</div>
<?
exit();
}
?>
