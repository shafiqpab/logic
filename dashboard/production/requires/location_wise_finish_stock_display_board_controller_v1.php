<?php
header('Content-type:text/html; charset=utf-8');
session_start();

// if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
// $user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action']; 

if ($action=="load_drop_down_location_lc")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", "", "SELECT id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down('requires/location_wise_finish_stock_display_board_controller_v1', $data[0]+'_'+this.value, 'load_drop_down_store','store_td');" );
	exit();
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	if ($data[1] != "" && $data[1] > 0) {$location_cond = "and a.location_id='$data[1]'";} else { $location_cond = "";}
	echo create_drop_down( "cbo_store_name", "", "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' and b.category_type=2 and a.status_active=1 and a.is_deleted=0 $location_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select Store--",0,"load_drop_down('requires/location_wise_finish_stock_display_board_controller_v1', $data[0]+'_'+$data[1]+'_'+this.value, 'load_drop_down_floor', 'floor_td');");
	exit();
}

if ($action=="load_drop_down_floor")
{ 
	$data=explode("_", $data);

    $company_ids = str_replace("'","",$data[0]); 
    if($data[1] != ""){$location_cond="and b.location_id in ($data[1])";}

    echo create_drop_down("cbo_floor_id", "", "SELECT b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and a.company_id=$data[0] $location_cond  and b.store_id='$data[2]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id,floor_room_rack_name", 1, "--Select Floor--", 1, "load_drop_down('requires/location_wise_finish_stock_display_board_controller_v1', $data[0]+'_'+$data[1]+'_'+$data[2]+'_'+this.value, 'load_drop_down_room', 'room_td');",0);	
	exit();    	 
}

if ($action=="load_drop_down_room")
{ 
	$data=explode("_", $data);

    $company_ids = str_replace("'","",$data[0]); 
    if($data[1] != ""){$location_cond="and b.location_id in ($data[1])";}

    echo create_drop_down("cbo_room_id", "", "SELECT b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and a.company_id='$data[0]' $location_cond  and b.store_id='$data[2]' and b.floor_id='$data[3]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name", "room_id,floor_room_rack_name", 1, "--Select Room--", 1, "load_drop_down('requires/location_wise_finish_stock_display_board_controller_v1', $data[0]+'_'+$data[1]+'_'+$data[2]+'_'+$data[3]+'_'+this.value, 'load_drop_down_rack', 'rack_td');",0);	
	exit();    	 
}

if($action=="load_drop_down_rack")
{
    $data=explode("_", $data);

    $company_ids = str_replace("'","",$data[0]); 
    if($data[1] != ""){$location_cond="and b.location_id in ($data[1])";}

    echo create_drop_down("cbo_rack_id", "", "SELECT b.rack_id,a.floor_room_rack_name, b.serial_no from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and a.company_id='$data[0]' $location_cond and b.store_id='$data[2]' and b.floor_id='$data[3]' and b.room_id='$data[4]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and b.shelf_id is null group by b.rack_id,a.floor_room_rack_name, b.serial_no order by b.serial_no, a.floor_room_rack_name", "rack_id,floor_room_rack_name", 1, "--Select Rack--", 0, "load_drop_down('requires/location_wise_finish_stock_display_board_controller_v1', $data[0]+'_'+$data[1]+'_'+$data[2]+'_'+$data[3]+'_'+$data[4]+'_'+this.value, 'load_drop_down_shelf', 'shelf_td');",0);
	exit();
}

if($action=="load_drop_down_shelf")
{
    extract($_REQUEST);

    $data=explode("_", $data);

    $company_ids = str_replace("'","",$data[0]); 
    if($data[1] != ""){$location_cond="and b.location_id in ($data[1])";}

    echo create_drop_down("cbo_shelf_id", "", "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and a.company_id='$data[0]' $location_cond and b.store_id='$data[2]' and b.floor_id='$data[3]' and b.room_id='$data[4]' and b.rack_id='$data[5]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name", "shelf_id,floor_room_rack_name", 1, "--Select Shelf--", 0, "");
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", "", "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );     	 
	exit(); 
}

if ($action=="show_floor_listview")
{
	$sql = "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id='$data' and production_process=5 order by floor_name";
	$res = sql_select($sql);
	$i=1;
	foreach ($res as $val) 
	{
		?>
		<div class="inputGroup">
		    <input id="option_<?=$i;?>" name="floor_name[]" value="<?=$val['ID'];?>"  type="checkbox"/>
		    <label for="option_<?=$i;?>"><?=$val['FLOOR_NAME'];?></label>
		 </div>
		<?
		$i++;
	}
}

if($action=="report_generate")
{
	extract($_REQUEST);
	$process = array( &$_POST );
	
	$company_id=str_replace("'","",$cbo_company_name);
	$cbo_location_id=str_replace("'","",$cbo_location);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	$cbo_room_id=str_replace("'","",$cbo_room_id);
	$cbo_rack_id=str_replace("'","",$cbo_rack_id);
	$cbo_shelf_id=str_replace("'","",$cbo_shelf_id);
	$buyer_id=str_replace("'","",$cbo_buyer_name);
	
	if($cbo_location_id==0) $location_cond=""; else $location_cond=" and a.location_id=$cbo_location_id";
	if($cbo_store_name==0) $store_cond=""; else $store_cond=" and a.store_id=$cbo_store_name";
	if($cbo_floor_id==0) $floor_cond=""; else $floor_cond=" and a.floor_id=$cbo_floor_id";
	if($cbo_room_id==0) $room_cond=""; else $room_cond=" and a.room=$cbo_room_id";
	if($cbo_rack_id==0) $rack_cond=""; else $rack_cond=" and a.rack=$cbo_rack_id";
	if($cbo_shelf_id==0) $shelf_cond=""; else $shelf_cond=" and a.self=$cbo_shelf_id";
	if($buyer_id==0) $buyer_id_cond=""; else $buyer_id_cond=" and c.buyer_name=$buyer_id";    

	$buyerArr = return_library_array("select id,buyer_name from lib_buyer","id","buyer_name"); 
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name"); 
	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active =1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");

	$store_sql="SELECT A.ID, A.STORE_NAME from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company_id and b.category_type=2 and a.status_active=1 and a.is_deleted=0 $location_cond";
	$store_sql_result=sql_select($store_sql);

	if(empty($store_sql_result))
	{
		echo "<p style='font-size:20px;color:red;', align='center'>Finish Fabric Store Not Found.<p/>";
		disconnect($con);
		die;		
	}

	$finish_store_id_arr=array();
	foreach ($store_sql_result as $key => $row) 
	{
		$finish_store_id_arr[$row[ID]]=$row[ID];
	}
	$all_finish_store_id=implode(",", $finish_store_id_arr);
	
	/*===================================================================================== /
	/								get finish data											/
	/===================================================================================== */
	$sql="SELECT A.COMPANY_ID, A.STORE_ID, A.FLOOR_ID, A.ROOM, A.RACK, A.SELF, A.BIN_BOX, A.PROD_ID, a.TRANSACTION_TYPE, A.CONS_QUANTITY, B.COLOR_ID, C.BUYER_ID, d.PRODUCT_NAME_DETAILS 
	from inv_transaction a, pro_batch_create_mst b, wo_booking_mst c, PRODUCT_DETAILS_MASTER d
	where a.PI_WO_BATCH_NO=b.id and b.booking_no=c.booking_no and a.PROD_ID=d.id and A.COMPANY_ID=$company_id and a.ITEM_CATEGORY=2 and a.TRANSACTION_TYPE in(1,2,3,4,5,6) and c.BOOKING_TYPE in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and A.STORE_ID in($all_finish_store_id) $store_cond $floor_cond $room_cond $rack_cond $shelf_cond $buyer_id_cond and A.RACK!=0 and A.SELF!=0
	UNION ALL
	SELECT A.COMPANY_ID, A.STORE_ID, A.FLOOR_ID, A.ROOM, A.RACK, A.SELF, A.BIN_BOX, A.PROD_ID, a.TRANSACTION_TYPE, A.CONS_QUANTITY, B.COLOR_ID, C.BUYER_ID, d.PRODUCT_NAME_DETAILS 
	from inv_transaction a, pro_batch_create_mst b, WO_NON_ORD_SAMP_BOOKING_MST c, PRODUCT_DETAILS_MASTER d
	where a.PI_WO_BATCH_NO=b.id and b.booking_no=c.booking_no and a.PROD_ID=d.id and A.COMPANY_ID=$company_id and a.ITEM_CATEGORY=2 and a.TRANSACTION_TYPE in(1,2,3,4,5,6) and c.BOOKING_TYPE in(4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and A.STORE_ID in($all_finish_store_id) $store_cond $floor_cond $room_cond $rack_cond $shelf_cond $buyer_id_cond and A.RACK!=0 and A.SELF!=0"; // and a.mst_id in(77814,9627,42782,78011)
	// echo $sql;die;
	$sql_resqlt=sql_select($sql);
	foreach ($sql_resqlt as $key => $row) 
	{
		$ref_str = $row["ROOM"]."*".$row["RACK"]."*".$row["SELF"]."*".$row["PROD_ID"]."*".$row["BUYER_ID"]."*".$row["COLOR_ID"]."*".$row["PRODUCT_NAME_DETAILS"];
		$data_arr[$row['STORE_ID']][$row['FLOOR_ID']][$ref_str][$row['TRANSACTION_TYPE']]+=$row['CONS_QUANTITY'];
	}
	// echo "<pre>";print_r($data_arr);die;
	
	$tbl_width = $page_width-30;
	$tr_height = 30;
	$font_size = "style='font-size: 20px'";

	ob_start();
    ?>          
	<div style="width:<? echo $tbl_width;?>px;background:#000000;">
		<style type="text/css">
			td div{font-weight: bold;font-size: 14px;vertical-align: middle;}
			#new_style div { position: relative; }
			td#new_style div::before { position: absolute; left: 0; top: 0; width: 100%; height: 50%; background: #ffe800;z-index: 99999; }
			#new_style div { box-shadow: inset 0px 7px 0px #ffe800; }
			.rpt_table tfoot th, td,td p{font-weight: bold;vertical-align: middle;color: #FFFFFF;text-shadow: rgb(0, 0, 0) 1px 0px 0px, rgb(0, 0, 0) 0.540302px 0.841471px 0px, rgb(0, 0, 0) -0.416147px 0.909297px 0px, rgb(0, 0, 0) -0.989993px 0.14112px 0px, rgb(0, 0, 0) -0.653644px -0.756803px 0px, rgb(0, 0, 0) 0.283662px -0.958924px 0px, rgb(0, 0, 0) 0.96017px -0.279416px 0px;
				}
				/* text-shadow: rgb(0, 0, 0) 1px 0px 0px, rgb(0, 0, 0) 0.540302px 0.841471px 0px, rgb(0, 0, 0) -0.416147px 0.909297px 0px, rgb(0, 0, 0) -0.989993px 0.14112px 0px, rgb(0, 0, 0) -0.653644px -0.756803px 0px, rgb(0, 0, 0) 0.283662px -0.958924px 0px, rgb(0, 0, 0) 0.96017px -0.279416px 0px; */
			#table_body thead th,#table_body tfoot th{background: #191A19;}
			#table_body thead th{color: #FFFFFF;font-weight: bold;font-size: 16px;}
			#table_body tfoot th{font-weight: bold;font-size: 16px;}
			#table_body  tr{border-bottom: .001em solid #444;}
			#table_body th,#table_body td{border-right: .001em solid #444 ; padding: 0 .5px 0 .5px;}
			.rpt_info tr td{color: #000000;font-weight: bold;font-size: 16px;}
			#table_body tbody tr th{font-size: 20px; color:red;}
			
			td div:hover span {
				bottom: 50px;
				visibility: visible;
				opacity: 1;
				z-index: 999999;
				display: block;
				text-shadow: none;
			}

			td.parentCell, div.block_div
			{
				position: relative;
			}
			th div.block_div
			{
				position: relative;
				height: 100%;
				width: 100%;
			}

			span.tooltips{
				display: none;
				position: absolute;
				z-index: 100;
				background: white;
				padding: 3px;
				color: #000000;
				top: 20px;
				left: 20px;
				font-size: 14px;
				font-weight: bold;
				text-shadow: none;
				width: 150px;
			}
			div.block_div span.tooltips{width: 80px;}
			td.parentCell:hover span.tooltips,div.block_div:hover span.tooltips{display:block;}
		</style>

        <table width="<? echo $tbl_width;?>" cellpadding="1" cellspacing="0" border="0" rules="all" id="table_body" align="left" style="border-collapse:seperate;border:.001em solid #444;">
			<thead>
				<tr height="<?=$tr_height;?>">
					<th width="60"><p <?=$font_size;?>>SL</p></th>
					<th width="150"><p <?=$font_size;?>>Rack</p></th>
					<th width="150"><p <?=$font_size;?>>Shelf</p></th>
					<th width="150"><p <?=$font_size;?>>Buyer</p></th>
					<th width="150"><p <?=$font_size;?>>Fabric Type</p></th>
					<th width="150"><p <?=$font_size;?>>Fabric Color</p></th>
					<th width="150"><p <?=$font_size;?>>Stock</p></th>
				</tr>
			</thead>
		<!-- </table> -->
		<!-- <div style="width:<?= $tbl_width; ?>px;  max-height:500px; overflow-x:hidden;" id="scroll_body"> -->
            <!-- <table width="<? echo $tbl_width;?>" cellpadding="1" cellspacing="0" border="0" rules="all" id="table_body" align="left" style="border-collapse:seperate;border:.001em solid #444; font-size: "> -->
	            <tbody>
	            	<?
	            	$i=1;
	            	foreach($data_arr as $storeId=>$store_data_arr) 
	            	{
	            		foreach ($store_data_arr as $floor_id => $floor_data_id) 
	            		{
	            			foreach ($floor_data_id as $str_ref => $row) 
	            			{
	            				$ref_data = explode("*", $str_ref);
	            				$room=$ref_data[0];
	            				$rack=$ref_data[1];
	            				$self=$ref_data[2];
	            				$prod_id=$ref_data[3];
	            				$buyer_id=$ref_data[4];
	            				$color_id=$ref_data[5];
	            				$item_description=$ref_data[6];
	            				$fabric_type=explode(",", $item_description);

	            				$tot_receive 	= $row[1] + $row[4] + $row[5];//recv+iss_rtn+trans_in
	            				$total_issue  	= $row[2] + $row[3] + $row[6];//iss+recv_rtn+trans_out
	            				$stock_qnty 	= $tot_receive - $total_issue;
	            				$stock_title 	= "Receive:". $tot_receive ."- Issue:". $total_issue;
	            				if(number_format($stock_qnty,2,".","") == "-0.00")
								{
									$stock_qnty=0;
								}
	            				
								$bgcolor=($i%2==0)?"#000000":"#000000";
								if($stock_qnty!=0)
								{
				                	?>
				                	<tr height="<?=$tr_height;?>" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
				                        <td width="60"><p <?=$font_size;?>><?=$i;?></p></td>
					                    <td width="150"><p <?=$font_size;?>><?=$floor_room_rack_arr[$rack];?></p></td>
					                    <td width="150"><p <?=$font_size;?>><?=$floor_room_rack_arr[$self];?></p></td>
					                    <td width="150"><p <?=$font_size;?>><?=$buyerArr[$buyer_id];?></p></td>
										<td width="150"><p <?=$font_size;?>><?=$fabric_type[0];?></p></td>
										<td width="150"><p <?=$font_size;?>><?=$colorArr[$color_id];?></p></td>
					                    <td width="150" align="center" title="<?=$stock_title;?>"><p <?=$font_size;?>><?=number_format($stock_qnty,2,".","");?></p></td>
				                    </tr>
				                    <?
				                    $i++;
			                	}
		                	}
	            		}
			        }
		            ?>
		        </tbody>
	        </table>
		<!-- </div> -->
	</div>

	<script type="text/javascript">
		$(document).ready(function () 
		{
	        /*function autoScrollWithinElement() {
			  const container = document.getElementById('scroll_body'); // Replace with your specific container ID
			  const scrollSpeed = 1; // Adjust this value as needed
			  let scrollPosition = 0;
			  let scrollingDown = true;

			  function scroll() {
			    if (scrollingDown) {
			      scrollPosition += scrollSpeed;
			      if (scrollPosition >= container.scrollHeight - container.clientHeight) {
			        scrollingDown = false;
			      }
			    } else {
			      scrollPosition -= scrollSpeed;
			      if (scrollPosition <= 0) {
			        scrollingDown = true;
			      }
			    }

			    container.scrollTop = scrollPosition;
			    setTimeout(scroll, 10); // Adjust the scroll speed and interval as needed
			  }

			  if (container.scrollHeight > container.clientHeight) {
			    scroll(); // Start scrolling
			  }
			}
			autoScrollWithinElement();*/

	    });
	</script>
	<?    
	$user_id=($user_id=='')?1000000000000:$user_id;

	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	//---------end------------//
	// $name=time();
	/* $name="display_board";
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w') or die('can not open');
	$is_created = fwrite($create_new_doc,ob_get_contents()) or die('can not write'); */
	echo "$total_data####$filename####".date('d-m-Y');	
	// echo "$total_data####$filename####30-11-2022";
	disconnect($con);
	exit();      

}
?>