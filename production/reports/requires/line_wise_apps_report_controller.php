<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 120, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/line_wise_apps_report_controller.php', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}


if($action=="report_generate")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer where status_active=1 and is_deleted=0","id","short_name");
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name");

	$company_id = str_replace("'","",$cbo_company_name);
	$date	    = str_replace("'","",$txt_date);
	$today      =date('d/m/Y', strtotime($date));

	// $location_id = str_replace("'","",$cbo_location);
	// $floor_id   = str_replace("'","",$cbo_floor);
	// $buyer_name = str_replace("'","",$cbo_buyer_name);
	$sql_cond="";
	$sql_cond .= ($company_id!=0) ? " and a.company_id in($company_id)" : "";

	if(str_replace("'","",$cbo_location)==0)  $location_name=""; else $location_name="and c.location_id =".str_replace("'","",$cbo_location)."";
    if($date=="") $date_cond.="";else $date_cond.=" and to_char(a.recv_date,'DD/MM/YYYY')<='$today'";


   $sql="SELECT a.qc_pass_qnty,b.floor_room_rack_name,a.is_issued from bundle_data_recv_issue_for_cut a,lib_floor_room_rack_mst b,lib_floor_room_rack_dtls c WHERE a.fl_ro_rack_dtl_id=b.floor_room_rack_id and c.floor_room_rack_dtls_id=b.floor_room_rack_id and a.fl_ro_rack_dtl_id=c.floor_room_rack_dtls_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $location_name $date_cond";


   $main_sql=sql_select($sql);
   $main_arr=array();

   foreach($main_sql as $row)
   {
	   if($row[csf('is_issued')]!=1)
	   {

		  $main_arr[$row[csf('floor_room_rack_name')]]['qty']+=$row[csf('qc_pass_qnty')];

	   }

   }

//    echo '<pre>';
//    print_r($main_arr);
//    echo'</pre>';
?>


<br>
	<div id="scroll_body">
		<div style="background-color:pink; width:50%">

          <tr>
             <th><strong>Garments Sewing Input Status</strong></th>
		  </tr>
		  <br>
          <tr style="border:none;">
				<td align="center" style="border:none; font-size:20px;font-weight: bold;" width="100%"><b><?= $company_library[str_replace("'","",$cbo_company_name)]; ?></b>
				</td>
	      </tr>
		  <br>
		  <tr>
			   <strong>Date:<?= $date; ?></strong>
		  </tr>
		</div>
	</div>
	<br>

    <?

	  foreach($main_arr as $floor_rock_row_name=>$row)
	  {
		   $qc_pass_rcv_qty=$row['qty'];

		   if($qc_pass_rcv_qty<0)
		   {

			?>
			  <table  border="1">
				 <tr style="border:1;">
				 <th style="background-color: red;"><?=$floor_rock_row_name;?></th>
				 </tr>
				 <tr style="border:1;">
				 <td><?=$row['qty'];?></td>
				 </tr>
			  </table>
			<?
		   }

		   if($qc_pass_rcv_qty<200)
		   {
			?>
               <table class="rpt_table" width="100px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body  border="1">
					<tr style="border:1;">
					<th style="background-color: yellow;"><?=$floor_rock_row_name;?></th>
					</tr>
					<tr style="border:1;">
					<td align="center"><?=$row['qty'];?></td>
					</tr>
			  </table>


			<?
		   }

		   else
		   {
			 ?>
                <table class="rpt_table" width="100px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body  border="1">
						<tr style="border:1;">
						<th style="background-color: yellow;"><?=$floor_rock_row_name;?></th>
						</tr>
						<tr style="border:1;">
						<td align="center"><?=$row['qty'];?></td>
						</tr>
			    </table>

             <?

		   }





	   ?>

      <?
	  }








    ?>








<?

}

?>