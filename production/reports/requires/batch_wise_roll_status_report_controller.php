<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$order_library=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
if($db_type==0) $field_concat="concat(machine_no,'-',brand) as machine_name"; 
else if($db_type==2) $field_concat="machine_no || '-' || brand as machine_name";
$machine_arr=return_library_array( "select id,$field_concat from  lib_machine_name",'id','machine_name');
$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );

//--------------------------------------------------------------------------------------------------------------------

if($action=="batchDtls_popup")
{
	echo load_html_head_contents("Batch Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:420px" align="center"><input type="button" value="Print Preview" onClick="print_window();" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:400px; margin-left:20px">
		<div id="report_container">
			<?
			$sql_barcode_info="SELECT a.id,sum(b.batch_qnty) as batch_qty,b.barcode_no from pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id and a.batch_against !=3 
			and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id='$batchid' group by a.id,b.barcode_no order by b.barcode_no";

			// echo $sql_barcode_info;die;
			
			$sql_barcode_info_data=sql_select($sql_barcode_info);

			$tblWidth=380;
		   ?>
			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
			    <caption> <h3> Barcode Wise Batch Qnty </h3>	</caption>
				<thead style="font-size:13px">
                	<tr>
                        <th width="30" >SL</th>
                        <th width="100" >Barcode</th>
                        <th width="60" >Batch Qnty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:<? echo $tblWidth; ?>px; max-height:320px;  overflow-y:scroll" id="scroll_body">
           
                <table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
                    <?
					$i=1;
					foreach($sql_barcode_info_data as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//  echo"<pre>";
								//  print_r($row);
                        ?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>"  style="font-size:13px">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100" style="word-break:break-all" align="center"><? echo $row[csf('barcode_no')]; ?></td>
							<td width="60" style="word-break:break-all" align="center"><? echo number_format($row[csf('batch_qty')],2); ?></td>
							
						</tr>
						<?
						$total_batch_qty +=$row[csf('batch_qty')];
						$i++;
					}
                    ?>
					<tr>
						<td width="30"></td>
						<td width="100" align="center"><strong>Total : </strong></td>
						<td width="60" align="center"><strong><? echo number_format($total_batch_qty,2); ?></strong></td>
					</tr>
                </table>
            </div>	
        </div>
	</fieldset>   
 <?
    exit();	
}

if($action=="heatDtls_popup")
{
	echo load_html_head_contents("Batch Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:420px" align="center"><input type="button" value="Print Preview" onClick="print_window();" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:400px; margin-left:20px">
		<div id="report_container">
			<?

			$sql_roll_heat="SELECT a.batch_id,b.barcode_no, sum(b.production_qty) as production_qty from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batchid and a.entry_form =32 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  group by a.batch_id,b.barcode_no order by b.barcode_no";
			//echo $sql_roll_heat;die;			
			$sql_roll_heat_data=sql_select($sql_roll_heat);

			$tblWidth=380;
		   ?>
			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
			    <caption> <h3> Barcode Wise Batch Qnty </h3>	</caption>
				<thead style="font-size:13px">
                	<tr>
                        <th width="30" >SL</th>
                        <th width="100" >Barcode</th>
                        <th width="60" >Batch Qnty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:<? echo $tblWidth; ?>px; max-height:320px;  overflow-y:scroll" id="scroll_body">
           
                <table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
                    <?
					$i=1;
					foreach($sql_roll_heat_data as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//  echo"<pre>";
								//  print_r($row);
                        ?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>"  style="font-size:13px">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100" style="word-break:break-all" align="center"><? echo $row[csf('barcode_no')]; ?></td>
							<td width="60" style="word-break:break-all" align="center"><? echo number_format($row[csf('production_qty')],2); ?></td>
							
						</tr>
						<?
						$total_production_qty +=$row[csf('production_qty')];
						$i++;
					}
                    ?>
					<tr>
						<td width="30"></td>
						<td width="100" align="center"><strong>Total : </strong></td>
						<td width="60" align="center"><strong><? echo number_format($total_production_qty,2); ?></strong></td>
					</tr>
                </table>
            </div>	
        </div>
	</fieldset>   
 <?
    exit();	
}

if($action=="slittingDtls_popup")
{
	echo load_html_head_contents("Sliting Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:420px" align="center"><input type="button" value="Print Preview" onClick="print_window();" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:400px; margin-left:20px">
		<div id="report_container">
			<?

			$sql_sliting_result="SELECT b.production_qty,b.barcode_no from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batchid and entry_form=30 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 order by b.barcode_no";

			
			//echo $sql_sliting_result;die;
			
			$sql_sliting_resultdata=sql_select($sql_sliting_result);

			$tblWidth=380;
		   ?>
			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
			    <caption> <h3> Barcode Wise Batch Qnty </h3>	</caption>
				<thead style="font-size:13px">
                	<tr>
                        <th width="30" >SL</th>
                        <th width="100" >Barcode</th>
                        <th width="60" >Batch Qnty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:<? echo $tblWidth; ?>px; max-height:320px;  overflow-y:scroll" id="scroll_body">
           
                <table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
                    <?
					$i=1;
					foreach($sql_sliting_resultdata as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//  echo"<pre>";
								//  print_r($row);
                        ?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>"  style="font-size:13px">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100" style="word-break:break-all" align="center"><? echo $row[csf('barcode_no')]; ?></td>
							<td width="60" style="word-break:break-all" align="center"><? echo number_format($row[csf('production_qty')],2); ?></td>
							
						</tr>
						<?
						$total_batch_qty +=$row[csf('production_qty')];
						$i++;
					}
                    ?>
					<tr>
						<td width="30"></td>
						<td width="100" align="center"><strong>Total : </strong></td>
						<td width="60" align="center"><strong><? echo number_format($total_batch_qty,2); ?></strong></td>
					</tr>
                </table>
            </div>	
        </div>
	</fieldset>   
 <?
    exit();	
}

if($action=="stenteringDtls_popup")
{
	echo load_html_head_contents("Stentering Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:420px" align="center"><input type="button" value="Print Preview" onClick="print_window();" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:400px; margin-left:20px">
		<div id="report_container">
			<?

			$sql_stentering_result="SELECT b.production_qty,b.barcode_no from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batchid and a.entry_form=48 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by b.barcode_no";

			
			//echo $sql_stentering_result;die;
			
			$sql_stentering_result_data=sql_select($sql_stentering_result);

			$tblWidth=380;
		   ?>
			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
			    <caption> <h3> Barcode Wise Batch Qnty </h3>	</caption>
				<thead style="font-size:13px">
                	<tr>
                        <th width="30" >SL</th>
                        <th width="100" >Barcode</th>
                        <th width="60" >Batch Qnty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:<? echo $tblWidth; ?>px; max-height:320px;  overflow-y:scroll" id="scroll_body">
           
                <table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
                    <?
					$i=1;
					foreach($sql_stentering_result_data as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//  echo"<pre>";
								//  print_r($row);
                        ?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>"  style="font-size:13px">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100" style="word-break:break-all" align="center"><? echo $row[csf('barcode_no')]; ?></td>
							<td width="60" style="word-break:break-all" align="center"><? echo number_format($row[csf('production_qty')],2); ?></td>
							
						</tr>
						<?
						$total_batch_qty +=$row[csf('production_qty')];
						$i++;
					}
                    ?>
					<tr>
						<td width="30"></td>
						<td width="100" align="center"><strong>Total : </strong></td>
						<td width="60" align="center"><strong><? echo number_format($total_batch_qty,2); ?></strong></td>
					</tr>
                </table>
            </div>	
        </div>
	</fieldset>   
 <?
    exit();	
}

if($action=="compactingDtls_popup")
{
	echo load_html_head_contents("Compacting Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:420px" align="center"><input type="button" value="Print Preview" onClick="print_window();" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:400px; margin-left:20px">
		<div id="report_container">
			<?

			$sql_compacting_result="SELECT b.production_qty, b.barcode_no from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batchid and a.entry_form=33 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by b.barcode_no";


			//echo $sql_compacting_result;die;
			
			$sql_compacting_result_data=sql_select($sql_compacting_result);

			$tblWidth=380;
		   ?>
			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
			    <caption> <h3> Barcode Wise Batch Qnty </h3>	</caption>
				<thead style="font-size:13px">
                	<tr>
                        <th width="30" >SL</th>
                        <th width="100" >Barcode</th>
                        <th width="60" >Batch Qnty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:<? echo $tblWidth; ?>px; max-height:320px;  overflow-y:scroll" id="scroll_body">
           
                <table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
                    <?
					$i=1;
					foreach($sql_compacting_result_data as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//  echo"<pre>";
								//  print_r($row);
                        ?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>"  style="font-size:13px">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100" style="word-break:break-all" align="center"><? echo $row[csf('barcode_no')]; ?></td>
							<td width="60" style="word-break:break-all" align="center"><? echo number_format($row[csf('production_qty')],2); ?></td>
							
						</tr>
						<?
						$total_batch_qty +=$row[csf('production_qty')];
						$i++;
					}
                    ?>
					<tr>
						<td width="30"></td>
						<td width="100" align="center"><strong>Total : </strong></td>
						<td width="60" align="center"><strong><? echo number_format($total_batch_qty,2); ?></strong></td>
					</tr>
                </table>
            </div>	
        </div>
	</fieldset>   
 <?
    exit();	
}

if($action=="dryingDtls_popup")
{
	echo load_html_head_contents("Drying Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:420px" align="center"><input type="button" value="Print Preview" onClick="print_window();" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:400px; margin-left:20px">
		<div id="report_container">
			<?

			$sql_drying_result="SELECT b.barcode_no,b.production_qty from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batchid and a.entry_form=31 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by b.barcode_no";

			//echo $sql_drying_result;die;
			
			$sql_drying_result_data=sql_select($sql_drying_result);

			$tblWidth=380;
		   ?>
			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
			    <caption> <h3> Barcode Wise Batch Qnty </h3>	</caption>
				<thead style="font-size:13px">
                	<tr>
                        <th width="30" >SL</th>
                        <th width="100" >Barcode</th>
                        <th width="60" >Batch Qnty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:<? echo $tblWidth; ?>px; max-height:320px;  overflow-y:scroll" id="scroll_body">
           
                <table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
                    <?
					$i=1;
					foreach($sql_drying_result_data as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//  echo"<pre>";
								//  print_r($row);
                        ?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>"  style="font-size:13px">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100" style="word-break:break-all" align="center"><? echo $row[csf('barcode_no')]; ?></td>
							<td width="60" style="word-break:break-all" align="center"><? echo number_format($row[csf('production_qty')],2); ?></td>
							
						</tr>
						<?
						$total_batch_qty +=$row[csf('production_qty')];
						$i++;
					}
                    ?>
					<tr>
						<td width="30"></td>
						<td width="100" align="center"><strong>Total : </strong></td>
						<td width="60" align="center"><strong><? echo number_format($total_batch_qty,2); ?></strong></td>
					</tr>
                </table>
            </div>	
        </div>
	</fieldset>   
 <?
    exit();	
}

if($action=="SpecialFinDtls_popup")
{
	echo load_html_head_contents("Special Finish Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:420px" align="center"><input type="button" value="Print Preview" onClick="print_window();" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:400px; margin-left:20px">
		<div id="report_container">
			<?

			// $sql_drying_result="SELECT a.id,b.id as dtls_id,b.barcode_no,b.prod_id,b.const_composition,b.gsm,b.dia_width,b.width_dia_type,b.batch_qty,b.production_qty,b.no_of_roll,b.roll_no,b.roll_id,b.rate,b.amount from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batchid and a.entry_form=31 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";

			$sql_special_result="SELECT b.production_qty,b.barcode_no from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batchid and a.entry_form=34 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by b.barcode_no";
	
			//echo $sql_special_result;die;
			
			$sql_special_result_data=sql_select($sql_special_result);

			$tblWidth=380;
		   ?>
			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
			    <caption> <h3> Barcode Wise Batch Qnty </h3>	</caption>
				<thead style="font-size:13px">
                	<tr>
                        <th width="30" >SL</th>
                        <th width="100" >Barcode</th>
                        <th width="60" >Batch Qnty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:<? echo $tblWidth; ?>px; max-height:320px;  overflow-y:scroll" id="scroll_body">
           
                <table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
                    <?
					$i=1;
					foreach($sql_special_result_data as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//  echo"<pre>";
								//  print_r($row);
                        ?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>"  style="font-size:13px">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100" style="word-break:break-all" align="center"><? echo $row[csf('barcode_no')]; ?></td>
							<td width="60" style="word-break:break-all" align="center"><? echo number_format($row[csf('production_qty')],2); ?></td>
							
						</tr>
						<?
						$total_batch_qty +=$row[csf('production_qty')];
						$i++;
					}
                    ?>
					<tr>
						<td width="30"></td>
						<td width="100" align="center"><strong>Total : </strong></td>
						<td width="60" align="center"><strong><? echo number_format($total_batch_qty,2); ?></strong></td>
					</tr>
                </table>
            </div>	
        </div>
	</fieldset>   
 <?
    exit();	
}

if($action=="qcPassDtls_popup")
{
	echo load_html_head_contents("QC Pass Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:420px" align="center"><input type="button" value="Print Preview" onClick="print_window();" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:400px; margin-left:20px">
		<div id="report_container">
			<?

			// $sql_special_result="SELECT a.id,b.id as dtls_id,b.prod_id,b.const_composition,b.gsm,b.dia_width,b.width_dia_type,b.batch_qty,b.production_qty,b.no_of_roll,b.barcode_no,b.roll_no,b.roll_id,b.rate,b.amount from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batchid and a.entry_form=34 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by b.const_composition";

			$sql_qc_dtls="SELECT  d.barcode_no, d.roll_weight from pro_finish_fabric_rcv_dtls a, pro_qc_result_mst d,pro_qc_result_dtls e where  a.id=d.pro_dtls_id and d.id=e.mst_id and a.batch_id=$batchid and d.entry_form=267 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by  d.roll_weight, d.barcode_no";

			// d.pro_dtls_id=24893 and 
			// echo $sql_qc_dtls;die; 
			$sql_qc_result_data=sql_select($sql_qc_dtls); 
	
			//echo $sql_special_result;die;
			
			$tblWidth=380;
		   ?>
			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
			    <caption> <h3> Barcode Wise Batch Qnty </h3>	</caption>
				<thead style="font-size:13px">
                	<tr>
                        <th width="30" >SL</th>
                        <th width="100" >Barcode</th>
                        <th width="60" >Batch Qnty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:<? echo $tblWidth; ?>px; max-height:320px;  overflow-y:scroll" id="scroll_body">
           
                <table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
                    <?
					$i=1;
					foreach($sql_qc_result_data as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//  echo"<pre>";
								//  print_r($row);
                        ?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>"  style="font-size:13px">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100" style="word-break:break-all" align="center"><? echo $row[csf('barcode_no')]; ?></td>
							<td width="60" style="word-break:break-all" align="center"><? echo number_format($row[csf('roll_weight')],2); ?></td>
							
						</tr>
						<?
						$total_roll_weight_qty +=$row[csf('roll_weight')];
						$i++;
					}
                    ?>
					<tr>
						<td width="30"></td>
						<td width="100" align="center"><strong>Total : </strong></td>
						<td width="60" align="center"><strong><? echo number_format($total_roll_weight_qty,2); ?></strong></td>
					</tr>
                </table>
            </div>	
        </div>
	</fieldset>   
 <?
    exit();	
}

if($action=="qcrollDtls_popup")
{
	echo load_html_head_contents("QC Roll Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:420px" align="center"><input type="button" value="Print Preview" onClick="print_window();" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:400px; margin-left:20px">
		<div id="report_container">
			<?

			$sql_qc_dtls = "SELECT a.id, c.barcode_no, c.qnty FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b ,pro_roll_details c,pro_qc_result_mst d 
			WHERE a.id=b.mst_id and b.id=c.dtls_id and c.barcode_no=d.barcode_no and b.id=d.pro_dtls_id and c.status_active=1 and b.status_active=1 and a.status_active=1 and a.entry_form in(66) and b.batch_id=$batchid and c.roll_no=b.roll_no order by c.barcode_no";

			// d.pro_dtls_id=24893 and 
			 //echo $sql_qc_dtls;die; 
			$sql_qc_result_data=sql_select($sql_qc_dtls); 
	
			//echo $sql_special_result;die;
			
			$tblWidth=380;
		   ?>
			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
			    <caption> <h3> Barcode Wise Batch Qnty </h3>	</caption>
				<thead style="font-size:13px">
                	<tr>
                        <th width="30" >SL</th>
                        <th width="100" >Barcode</th>
                        <th width="60" >Batch Qnty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:<? echo $tblWidth; ?>px; max-height:320px;  overflow-y:scroll" id="scroll_body">
           
                <table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
                    <?
					$i=1;
					foreach($sql_qc_result_data as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//  echo"<pre>";
								//  print_r($row);
                        ?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>"  style="font-size:13px">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100" style="word-break:break-all" align="center"><? echo $row[csf('barcode_no')]; ?></td>
							<td width="60" style="word-break:break-all" align="center"><? echo number_format($row[csf('qnty')],2); ?></td>
							
						</tr>
						<?
						$total_roll_weight_qty +=$row[csf('qnty')];
						$i++;
					}
                    ?>
					<tr>
						<td width="30"></td>
						<td width="100" align="center"><strong>Total : </strong></td>
						<td width="60" align="center"><strong><? echo number_format($total_roll_weight_qty,2); ?></strong></td>
					</tr>
                </table>
            </div>	
        </div>
	</fieldset>   
 <?
    exit();	
}

if($action=="loadingDtls_popup")
{
	echo load_html_head_contents("QC Roll Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:420px" align="center"><input type="button" value="Print Preview" onClick="print_window();" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:400px; margin-left:20px">
		<div id="report_container">
			<?

			$sql_roll_load ="select a.batch_id, b.barcode_no,
			sum(CASE WHEN a.load_unload_id=1 THEN b.production_qty ELSE 0 END) AS deying_load_qty,
			sum(CASE WHEN a.load_unload_id=2 THEN b.production_qty ELSE 0 END) AS deying_unload_qty
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batchid and a.entry_form=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.batch_id, b.barcode_no";

			//echo $sql_roll_load;die;
			$sql_roll_load_data = sql_select($sql_roll_load);

			// d.pro_dtls_id=24893 and 
			 //echo $sql_qc_dtls;die; 
		
	
			//echo $sql_special_result;die;
			
			$tblWidth=380;
		   ?>
			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
			    <caption> <h3> Barcode Wise Batch Qnty </h3>	</caption>
				<thead style="font-size:13px">
                	<tr>
                        <th width="30" >SL</th>
                        <th width="100" >Barcode</th>
                        <th width="60" >Batch Qnty</th>
                    </tr>
				</thead>
            </table>
            <div style="width:<? echo $tblWidth; ?>px; max-height:320px;  overflow-y:scroll" id="scroll_body">
           
                <table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
                    <?
					$i=1;
					foreach($sql_roll_load_data as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//  echo"<pre>";
								//  print_r($row);
                        ?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>"  style="font-size:13px">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100" style="word-break:break-all" align="center"><? echo $row[csf('barcode_no')]; ?></td>
							<td width="60" style="word-break:break-all" align="center"><? echo number_format($row[csf('deying_load_qty')],2); ?></td>
							
						</tr>
						<?
						$total_deying_load_qty +=$row[csf('deying_load_qty')];
						$i++;
					}
                    ?>
					<tr>
						<td width="30"></td>
						<td width="100" align="center"><strong>Total : </strong></td>
						<td width="60" align="center"><strong><? echo number_format($total_deying_load_qty,2); ?></strong></td>
					</tr>
                </table>
            </div>	
        </div>
	</fieldset>   
 <?
    exit();	
}

if($action=="unloadingDtls_popup")
{
	echo load_html_head_contents("QC Roll Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
	</script>	
	<div style="width:420px" align="center"><input type="button" value="Print Preview" onClick="print_window();" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:400px; margin-left:20px">
		<div id="report_container">
			<?

			$sql_roll_load ="select a.batch_id, b.barcode_no,
			sum(CASE WHEN a.load_unload_id=1 THEN b.production_qty ELSE 0 END) AS deying_load_qty,
			sum(CASE WHEN a.load_unload_id=2 THEN b.production_qty ELSE 0 END) AS deying_unload_qty
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batchid and a.entry_form=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.batch_id, b.barcode_no";

			//echo $sql_roll_load;die;
			$sql_roll_load_data = sql_select($sql_roll_load);

			// d.pro_dtls_id=24893 and 
			//echo $sql_qc_dtls;die; 
		
	
			//echo $sql_special_result;die;
			
			$tblWidth=380;
		?>
			<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
				<caption> <h3> Barcode Wise Batch Qnty </h3>	</caption>
				<thead style="font-size:13px">
					<tr>
						<th width="30" >SL</th>
						<th width="100" >Barcode</th>
						<th width="60" >Batch Qnty</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $tblWidth; ?>px; max-height:320px;  overflow-y:scroll" id="scroll_body">
		
				<table border="1" class="rpt_table" rules="all" width="<?=$tblWidth; ?>" cellpadding="0" cellspacing="0">
					<?
					$i=1;
					foreach($sql_roll_load_data as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//  echo"<pre>";
								//  print_r($row);
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>"  style="font-size:13px">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100" style="word-break:break-all" align="center"><? echo $row[csf('barcode_no')]; ?></td>
							<td width="60" style="word-break:break-all" align="center"><? echo number_format($row[csf('deying_unload_qty')],2); ?></td>
							
						</tr>
						<?
						$total_deying_unload_qty +=$row[csf('deying_unload_qty')];
						$i++;
					}
					?>
					<tr>
						<td width="30"></td>
						<td width="100" align="center"><strong>Total : </strong></td>
						<td width="60" align="center"><strong><? echo number_format($total_deying_unload_qty,2); ?></strong></td>
					</tr>
				</table>
			</div>	
		</div>
	</fieldset>   
<?
	exit();	
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_year_id.'aziz';
	?>
		<script>
		
			function js_set_value(str)
			{
				var splitData = str.split("_");
				//alert (splitData[1]);
				$("#hide_job_id").val(splitData[0]); 
				$("#hide_job_no").val(splitData[1]); 
				parent.emailwindow.hide();
			}
		</script>
	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:580px;">
				<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>                 
							<td align="center">	
							<?
								$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
							</td>     
							<td align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'batch_wise_roll_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
						</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}
if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
	if ($_SESSION['logic_erp']["data_level_secured"]==1)
	{
	if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
	}
	else
	{
	$buyer_id_cond="";
	}
	}
	else
	{
	$buyer_id_cond=" and buyer_name=$data[1]";
	}
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	if($db_type==0)
	{
	if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
	$year_field_con=" and to_char(insert_date,'YYYY')";
	if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit(); 
} // Job Search end

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 100, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=3 and company_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/batch_creation_controller',this.value, 'load_drop_machine', 'td_dyeing_machine' );",0 );

	exit();
}
if ($action == "booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_booking_id").val(splitData[0]); 
			$("#hide_booking_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:740px;">
					<table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th width="170">Please Enter Booking No</th>
							<th>Booking Date</th>

							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
							<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>   
								<td align="center">				
									<input type="text" style="width:150px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />	
								</td> 
								<td align="center">	
									<input type="text" style="width:70px" class="datepicker" name="txt_date_from" id="txt_date_from" readonly/> To
									<input type="text" style="width:70px" class="datepicker" name="txt_date_to" id="txt_date_to" readonly/>
								</td>     

								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'batch_wise_roll_status_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
								</td>
							</tr>
							<tr>
								<td colspan="4" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:15px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();  
} 
if($action == "create_booking_no_search_list_view")
{
	$data=explode('**',$data);

	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; 
	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if ($data[2]!=0) $booking_no=" and a.booking_no_prefix_num='$data[2]'"; else $booking_no='';
	if($db_type==0)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and s.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);
	//pro_batch_create_mst//wo_non_ord_samp_booking_mst
	$sql= "(select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,pro_batch_create_mst b where $company $buyer $booking_no $booking_date and a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 
	union all
	select a.id,a.booking_no_prefix_num as no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no,a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_non_ord_samp_booking_mst a ,pro_batch_create_mst b where $company $buyer $booking_no $booking_date  and a.booking_no=b.booking_no and a.booking_type=4 and b.status_active=1 and b.is_deleted=0) order by id Desc
	";
	//echo "select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,wo_non_ord_samp_booking_mst b where $company $buyer $booking_no $booking_date and a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 "; 
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "110,80,80,80,90,120,80,80,60,50","910","320",0, $sql , "js_set_value", "id,no_prefix_num", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','setFilterGrid(\'list_view\',-1);','0,3,0,0,0,0,0,0,0,0','','');
	exit(); 
}

// Booking Search end

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			if (str!="") str=str.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}
	</script>
	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Order No</th>
						<th>Shipment Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
						<input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>                 
							<td align="center">	
							<?
								$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
							</td>     
							<td align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'batch_wise_roll_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}
if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	//echo $data[1];
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==1) 
		$search_field="b.po_number"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no"; 	
	else 
		$search_field="a.job_no";
	$start_date =trim($data[4]);
	$end_date =trim($data[5]);	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	$arr=array(0=>$company_arr,1=>$buyer_arr);
	$sql= "select b.id, $year_field a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,130,50,60,130,130","760","220",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr , "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "",'','0,0,0,0,0,0,3','',1) ;
	exit(); 
}//Order Search End

if($action=="batch_no_search_popup")
{
	echo load_html_head_contents("Batch No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
		<script>
			var selected_id = new Array; var selected_name = new Array;
			function check_all_data()
			{
				var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
				tbl_row_count = tbl_row_count - 1;

				for( var i = 1; i <= tbl_row_count; i++ )
				{
					$('#tr_'+i).trigger('click'); 
				}
			}
			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}
			
			function js_set_value( str )				
			{
				//alert(str);
				if (str!="") str=str.split("_");
				toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
				if( jQuery.inArray( str[1], selected_id ) == -1 ) {
					selected_id.push( str[1] );
					selected_name.push( str[2] );
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == str[1] ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
				var id = ''; var name = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + '*';
				}
				
				id = id.substr( 0, id.length - 1 );
				name = name.substr( 0, name.length - 1 );
				
				$('#hide_order_id').val( id );
				$('#hide_order_no').val( name );
			}
		</script>
	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:760px;">
				<table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table">
					<thead>
					
						<th>Batch No </th>
						<th>Batch Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
						<input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_batch_no_search_list_view', 'search_div', 'batch_wise_roll_status_report_controller', 'setFilterGrid(\'tbl_list\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}
if($action=="create_batch_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	//echo $data[1];
	
	$batch_no=$data[1];
	$search_string="%".trim($data[3])."%";
	//if($batch_no=='') $search_field="b.po_number";  else  $search_field="b.po_number";
	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no in ('$batch_no') "; 
		
	
	$start_date =trim($data[2]);
	$end_date =trim($data[3]);	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.batch_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.batch_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}
	//if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	//else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	
	$sql="select a.id,a.batch_no,a.batch_for,a.booking_no,a.color_id,a.batch_weight from pro_batch_create_mst a where a.company_id=$company_id and a.is_deleted=0 and a.status_active=1 $date_cond $batch_no_cond";	
	$arr=array(1=>$color_library,3=>$batch_for);
		echo  create_list_view("tbl_list", "Batch no,Color,Booking no, Batch for,Batch weight ", "150,100,150,100,70","700","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,batch_for,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "",'','0','',1) ;
		exit();
}//Batch Search End
$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;



if($action=="report_generate")
{
	$started = microtime(true);
	$process = array( &$_POST );
	//var_dump($process);die;
	extract(check_magic_quote_gpc( $process )); 
	$company_name= str_replace("'","",$cbo_company_name);
	$working_company= str_replace("'","",$cbo_working_company);
	$job_no=str_replace("'","",$txt_job_no);
	$batch_type=str_replace("'","",$cbo_batch_type);
	$batch_no=str_replace("'","",$txt_batch_no);
	$buyer_name= str_replace("'","",$cbo_buyer_name);
	$cbo_year= str_replace("'","",$cbo_year);
	$hide_booking_id = str_replace("'","",$txt_hide_booking_id);
	$txt_booking_no = str_replace("'","",$txt_booking_no);
	$report_type=str_replace("'","",$report_type);
	//echo $program_no;
	$cbo_search_date= str_replace("'","",$cbo_search_date);
	$order_no = str_replace("'","",$txt_order_no);

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name in (".str_replace("'","",$buyer_name).")";
	}
	//$prod_detail_arr=return_library_array( "select id, item_description from product_details_master", "id", "item_description"  );
	
	// $prod_sql= sql_select("select id,gsm,product_name_details from product_details_master");
	// foreach($prod_sql as $row)
	// {
	// 	$prod_detail_arr[$row[csf("id")]]=$row[csf("product_name_details")];
	// 	$prod_detail_gsm_arr[$row[csf("id")]]=$row[csf("gsm")];
	// }

	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($cbo_search_date==1)
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
				$date_cond=" and a.batch_date between '$start_date' and '$end_date'";
				$date_cond_dyeing=" and c.batch_date between '$start_date' and '$end_date'";
				$ship_date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
				$batch_date_cond=" and c.insert_date between '$start_date' and '$end_date'";
				$batch_date_cond2=" and d.batch_date between '$start_date' and '$end_date'";
		}
		else
		{
		if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
				
			}
				$date_cond=" and c.process_end_date between '$start_date' and '$end_date'";
				$date_cond_dyeing=" and a.process_end_date between '$start_date' and '$end_date'";
				$ship_date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
				$batch_date_cond=" and c.insert_date between '$start_date' and '$end_date'";
				$batch_date_cond2=" and d.batch_date between '$start_date' and '$end_date'";
		}
	
	}

	

	if($db_type==0)
	{
	//$year_field_by="and YEAR(a.insert_date)"; 
	$year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year"; 
	if($cbo_year!=0) $year_cond=" and year(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
	//$year_field_by=" and to_char(a.insert_date,'YYYY')";
	$year_field="to_char(a.insert_date,'YYYY') as year";
	if($cbo_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";	
	}

	//echo $year_cond;
	//if(trim($cbo_year)!=0) $year_cond=" $year_field_by=$cbo_year"; else $year_cond="";
	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ('$job_no') ";
	if ($batch_no=="") $batch_no_cond=""; else $batch_no_cond=" and a.batch_no in ('$batch_no') ";
	
	
	if ($txt_booking_no!='') $booking_no_cond="  and a.booking_no LIKE '%$txt_booking_no%'"; else $booking_no_cond="";
	if ($hide_booking_id!=0) $booking_no_cond.="  and a.booking_no_id in($hide_booking_id) "; else $booking_no_cond.="";
	if ($working_company==0) $workingCompany_cond=""; else $workingCompany_cond="  and a.working_company_id=".$working_company." ";
	if ($company_name==0) $workingCompany_cond.=""; else $workingCompany_cond.="  and a.company_id=".$company_name." ";
	if ($company_name>0) 
	{ 
		$sub_conCompany_cond="and a.company_id=".$company_name." ";
	}
	else if ($working_company>0) 
	{ 
		$sub_conCompany_cond="and a.company_id=".$working_company." ";
	}
	else $sub_conCompany_cond="";

	if ($working_company==0) $company_name_cond2=""; else $company_name_cond2="  and a.style_owner=".$working_company." ";
	if ($company_name==0) $company_name_cond2.=""; else $company_name_cond2.="  and a.company_name=".$company_name." ";
	if ($working_company==0) $dyeing_company_cond=""; else $dyeing_company_cond="  and a.service_company=".$working_company." ";
	if ($company_name==0) $dyeing_company_cond.=""; else $dyeing_company_cond.="  and a.company_id=".$company_name." ";
	if ($working_company==0) $knit_company_cond=""; else $knit_company_cond="  and a.knitting_company=".$working_company." ";
	if ($company_name==0) $knit_company_cond.=""; else $knit_company_cond.="  and a.company_id=".$company_name." ";
	//echo $booking_no_cond.'dd'; a.company_id=$company_name

	//SubCon
	if($batch_type==2)
	{
		if ($cbo_buyer_name==0) $sub_buyer_cond=""; else $sub_buyer_cond="and a.party_id='".$cbo_buyer_name."' ";
		if ($order_no!='') $suborder_no="and b.order_no='$order_no'"; else $suborder_no="";
		if ($job_no!='') $sub_job_cond="  and a.job_no_prefix_num='".$job_no."' "; else $sub_job_cond="";
	}

	if($order_no=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim($order_no)."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}

	// ====================================================================

	//  echo "SELECT b.id,b.pub_shipment_date,$year_field, b.po_number,a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style,b.file_no,b.grouping 
	//  from pro_batch_create_dtls c,  wo_po_break_down b,wo_po_details_master a 
	//  where c.po_id=b.id and a.job_no=b.job_no_mst   $ref_cond and b.status_active!=0   $buyer_id_cond $po_cond $job_no_cond $year_cond $batch_date_cond";die;
	$poDataArray=sql_select("SELECT b.id,b.pub_shipment_date,$year_field, b.po_number,a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style,b.file_no,b.grouping 
	from pro_batch_create_dtls c,  wo_po_break_down b,wo_po_details_master a 
	where c.po_id=b.id and a.job_no=b.job_no_mst   $ref_cond and b.status_active!=0   $buyer_id_cond $po_cond $job_no_cond $year_cond $batch_date_cond2");// $ship_date_cond
	
	$self_all_po_id='';
	$job_array=array(); $all_job_id='';

	$po_id_check =array();

	if(!empty($poDataArray))	
	{
		$con = connect();
		$r_id_two=execute_query("delete from tmp_poid where userid=$user_id and type=21");
		
		if($r_id_two )
		{
			oci_commit($con);
		}
	}


	foreach($poDataArray as $row)
	{
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['style_no']=$row[csf('style')];
		$job_array[$row[csf('id')]]['file']=$row[csf('file_no')];
		$job_array[$row[csf('id')]]['refNo']=$row[csf('grouping')];			
		if($self_all_po_id=="") $self_all_po_id=$row[csf('id')]; else $self_all_po_id.=",".$row[csf('id')]; //echo $all_po_id;
		if(!$po_id_check[$row[csf('id')]])
		{
			$po_id_check[$row[csf('id')]]=$row[csf('id')];
			$POID = $row[csf('id')];
			$rID2=execute_query("insert into tmp_poid (userid, poid,type) values ($user_id,$POID,21)");
		}
		
	} 
	if($rID2)
	{
		oci_commit($con);
	}

	
	//var_dump($job_array);die;
	//var_dump($self_all_po_id);die;
	//echo $self_all_po_id;

	// $poDataArray_two=sql_select("SELECT b.id,b.cust_style_ref,$year_field, b.order_no as po_number,a.party_id as buyer_name,a.subcon_job as job_no_prefix_num 
	// from  subcon_ord_dtls b,subcon_ord_mst a 
	// where  a.subcon_job=b.job_no_mst and b.status_active=1 and b.is_deleted=0  $sub_buyer_cond $suborder_no $job_no_cond $year_cond $sub_job_cond ");
	// $subc_all_po_id='';
	// $sub_job_array=array();
	/*echo "SELECT b.id,b.buyer_style_ref,$year_field, b.order_no as po_number,a.party_id as buyer_name,a.subcon_job as job_no_prefix_num 
	from  subcon_ord_dtls b,subcon_ord_mst a 
	where  a.subcon_job=b.job_no_mst and b.status_active=1 and b.is_deleted=0  $sub_buyer_cond $suborder_no $job_no_cond $year_cond $sub_job_cond ";die;*/
	// foreach($poDataArray_two as $row)
	// {
	// 	$sub_job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
	// 	$sub_job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
	// 	$sub_job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
	// 	$sub_job_array[$row[csf('id')]]['style_no']=$row[csf('cust_style_ref')];
	// 	if($subc_all_po_id=="") $subc_all_po_id=$row[csf('id')]; else $subc_all_po_id.=",".$row[csf('id')];
	// } 
	//echo $subc_all_po_id;

	// $non_order_arr=array();
	// $sql_non_order="SELECT c.id,c.company_id,c.grouping as samp_ref_no,c.style_desc, c.buyer_id as buyer_name, b.booking_no, b.bh_qty 
	// from wo_non_ord_samp_booking_mst c, wo_non_ord_samp_booking_dtls b 
	// where c.booking_no=b.booking_no and c.booking_type=4 and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1";
	// $result_sql_order=sql_select($sql_non_order);
	// foreach($result_sql_order as $row)
	// {		
	// 	$non_order_arr[$row[csf('booking_no')]]['buyer_name']=$row[csf('buyer_name')];
	// 	$non_order_arr[$row[csf('booking_no')]]['samp_ref_no']=$row[csf('samp_ref_no')];
	// 	$non_order_arr[$row[csf('booking_no')]]['style_desc']=$row[csf('style_desc')];
	// 	$non_order_arr[$row[csf('booking_no')]]['bh_qty']=$row[csf('bh_qty')];
	// }
	// unset($result_sql_order);

	
	// $po_id_cond="";
	// if($order_no!="" || $job_no!="" || str_replace("'","",$cbo_buyer_name)!=0 || $cbo_year!=0  || $start_date!="")
	// {
	// 	$po_id_cond=" $self_all_po_id";
	// }

	// $self_po_id_cond="";
	// if($order_no!="" || $job_no!="" || str_replace("'","",$cbo_buyer_name)!=0 || $cbo_year!=0 )
	// {
	// 	$self_po_id_cond=" $self_all_po_id";
	// 	//echo  $self_po_id_cond.'D';die;
	// }

	// $subc_po_id_cond="";
	// if($order_no!="" || $job_no!="" || str_replace("'","",$cbo_buyer_name)!=0 || $cbo_year!=0  )
	// {
	// 	$subc_po_id_cond=" $subc_all_po_id";
	// }

	//echo $subc_all_po_id.'DDD';;
	//$po_id_cond_split=array_chunk(array_unique(explode(",",$po_id_cond)),999);
	//$self_po_id_cond_split=array_chunk(array_unique(explode(",",$self_po_id_cond)),999);
	//$subc_po_id_cond_split=array_chunk(array_unique(explode(",",$subc_po_id_cond)),999);
	
	// if($order_no!="" || $job_no!="" || str_replace("'","",$cbo_buyer_name)!=0 || $cbo_year!=0  )
	// {
	// 	$poIds=chop($self_po_id_cond,','); $po_cond_for_in="";
	// 	$po_ids=count(array_unique(explode(",",$self_po_id_cond)));
	// 	// if($db_type==2 && $po_ids>1000)
	// 	// {
	// 	// 	$po_cond_for_in=" and (";
	// 	// 	$poIdsArr=array_chunk(explode(",",$poIds),999);
	// 	// 	foreach($poIdsArr as $ids)
	// 	// 	{
	// 	// 		$ids=implode(",",$ids);
	// 	// 		$po_cond_for_in.=" b.po_id in($ids) or"; 
	// 	// 	}
	// 	// 	$po_cond_for_in=chop($po_cond_for_in,'or ');
	// 	// 	$po_cond_for_in.=")";
	// 	// }
	// 	// else
	// 	// {
	// 	// 	$po_cond_for_in=" and b.po_id in($poIds)";
			
	// 	// }
	// 	$subpoIds=chop($subc_po_id_cond,','); $sub_po_cond_for_in="";
	// 	$subpo_ids=count(array_unique(explode(",",$subc_po_id_cond)));
	// 	if($db_type==2 && $subpo_ids>1000)
	// 	{
	// 		$sub_po_cond_for_in=" and (";
	// 		$subpoIdsArr=array_chunk(explode(",",$subpoIds),999);
	// 		foreach($subpoIdsArr as $ids)
	// 		{
	// 			$ids=implode(",",$ids);
	// 			$sub_po_cond_for_in.=" b.po_id in($ids) or"; 
	// 		}
	// 		$sub_po_cond_for_in=chop($sub_po_cond_for_in,'or ');
	// 		$sub_po_cond_for_in.=")";
	// 	}
	// 	else
	// 	{
	// 		$sub_po_cond_for_in=" and b.po_id in($subpoIds)";
			
	// 	}
	// }   

		if($cbo_search_date==1)
		{            
			
			// Self
			$self_sql_data="(SELECT a.id,a.batch_no,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, a.batch_date,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,b.barcode_no,count(b.width_dia_type) as num_of_rows  
			from tmp_poid c, pro_batch_create_dtls b,pro_batch_create_mst a 
			where c.poid=b.po_id and a.id=b.mst_id and c.userid=$user_id and c.type=21 and a.batch_against !=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.is_sales=0 and a.entry_form=0 $batch_no_cond $date_cond $booking_no_cond $workingCompany_cond group by a.batch_no,a.batch_against,a.entry_form,a.id,a.batch_date,a.color_id,a.booking_no,b.prod_id,b.po_id,b.width_dia_type,b.barcode_no) order by a.id";
			die;
			/*$p=1;
			foreach($self_po_id_cond_split as $po_row)
			{
			if($p==1) $self_sql_data .=" and (b.po_id  in(".implode(',',$po_row).")"; else  $self_sql_data .=" OR b.po_id in(".implode(',',$po_row).")";
			$p++;
			}
			$self_sql_data .=")";
			$self_sql_data .=" group by a.batch_no,a.batch_against,a.entry_form,a.id,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no)";*/
			//echo $self_sql_data.'<br>';


			// Subcon
			// 	$subc_sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,(b.batch_qnty) as batch_qty, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.gsm,b.item_description,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.total_trims_weight,a.color_range_id,b.barcode_no,count(b.width_dia_type) as num_of_rows  
			// from pro_batch_create_dtls b,pro_batch_create_mst a 
			// where a.id=b.mst_id and a.batch_against !=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=36 $batch_no_cond  $date_cond $booking_no_cond $sub_conCompany_cond  $sub_po_cond_for_in group by a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,b.batch_qnty, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.gsm,b.item_description,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.total_trims_weight,a.color_range_id,b.barcode_no ) order by a.id";

			/*$p=1;
			foreach($subc_po_id_cond_split as $po_row)
			{
			if($p==1) $subc_sql_data .=" and (b.po_id  in(".implode(',',$po_row).")"; else  $subc_sql_data .=" OR b.po_id in(".implode(',',$po_row).")";
			$p++;
			}
			$subc_sql_data .=")";
			$subc_sql_data .=" group by a.batch_no,a.batch_against,a.entry_form,a.id,a.batch_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no)";*/
			//echo $subc_sql_data.'<br>';

			// Sample
			// 	$samp_sql_data="SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.total_trims_weight,a.color_range_id,b.barcode_no,count(b.width_dia_type) as num_of_rows  
			// from pro_batch_create_dtls b,pro_batch_create_mst a 
			// where a.id=b.mst_id and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=0 $batch_no_cond  $date_cond $booking_no_cond $workingCompany_cond $po_cond_for_in
			// group by a.batch_no,a.batch_against,a.floor_id,a.entry_form,a.id,a.batch_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,a.total_trims_weight,a.color_range_id,b.barcode_no 
			// union
			// SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.total_trims_weight,a.color_range_id,b.barcode_no,count(b.width_dia_type) as num_of_rows  
			// from pro_batch_create_dtls b,pro_batch_create_mst a 
			// where a.id=b.mst_id and a.batch_against=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and a.is_deleted=0 and a.entry_form=0 
			// and b.po_id=0
			// $batch_no_cond  $date_cond $booking_no_cond $workingCompany_cond
			// group by a.batch_no,a.batch_against,a.floor_id,a.entry_form,a.id,a.batch_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,a.total_trims_weight,a.color_range_id,b.barcode_no";
			// //echo $samp_sql_data.'<br>';
			
			// 	$sales_sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, a.batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.sales_order_no,a.sales_order_id,a.extention_no,c.within_group,c.sales_booking_no,c.buyer_id,c.po_buyer,c.po_job_no, a.total_trims_weight,a.color_range_id,b.barcode_no,count(b.width_dia_type) as num_of_rows 
			// from pro_batch_create_dtls b,pro_batch_create_mst a ,fabric_sales_order_mst c
			// where a.id=b.mst_id and c.id=a.sales_order_id and a.batch_against !=3 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.is_sales=1 and a.entry_form=0 $batch_no_cond $date_cond  $booking_no_cond $workingCompany_cond group by a.batch_no,a.batch_against,a.entry_form,a.floor_id,a.id,a.batch_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.sales_order_no,a.sales_order_id,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,c.within_group,c.sales_booking_no,c.buyer_id,c.po_buyer,c.po_job_no,a.total_trims_weight,a.color_range_id,b.barcode_no)";
				
			//echo $self_sql_data;die;
		}
		else if($cbo_search_date==2)
		{            
			// All
			
			// Self
				$self_sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, c.process_end_date as batch_date,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,b.barcode_no,count(b.width_dia_type) as num_of_rows
			from tmp_poid d, pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess c 
			where d.poid=b.po_id and a.id=b.mst_id  and c.batch_id=a.id  and c.batch_id=b.mst_id and d.userid=$user_id and d.type=21 and c.entry_form=35 and a.batch_against !=3 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $batch_no_cond   $date_cond $booking_no_cond $workingCompany_cond $type_cond group by a.batch_no,a.batch_against,a.entry_form,a.id,a.floor_id,c.process_end_date,a.color_id,a.booking_no,b.prod_id,b.po_id,b.width_dia_type,b.barcode_no)";
			/*$p=1;
			foreach($self_po_id_cond_split as $po_row)
			{
			if($p==1) $self_sql_data .=" and (b.po_id  in(".implode(',',$po_row).")"; else  $self_sql_data .=" OR b.po_id in(".implode(',',$po_row).")";
			$p++;
			}
			$self_sql_data .=")";
			$self_sql_data .=" group by a.batch_no,a.batch_against,a.entry_form,a.id,c.process_end_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no)";*/	


			// Subcon
			// $subc_sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,(b.batch_qnty) as batch_qty, c.process_end_date as batch_date,a.batch_weight,b.prod_id,b.po_id,b.gsm,b.width_dia_type,b.item_description,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.total_trims_weight,a.color_range_id,b.barcode_no,count(b.width_dia_type) as num_of_rows
			// from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess c 
			// where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and c.entry_form=38 and a.batch_against !=3 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $batch_no_cond  $date_cond $booking_no_cond $workingCompany_cond $type_cond  $sub_po_cond_for_in group by a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,b.batch_qnty, c.process_end_date,a.batch_weight,b.prod_id,b.po_id,b.gsm,b.width_dia_type,b.item_description,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.total_trims_weight,a.color_range_id,b.barcode_no) order by a.id";
			/*$p=1;
			foreach($subc_po_id_cond_split as $po_row)
			{
			if($p==1) $subc_sql_data .=" and (b.po_id  in(".implode(',',$po_row).")"; else  $subc_sql_data .=" OR b.po_id in(".implode(',',$po_row).")";
			$p++;
			}
			$subc_sql_data .=")";
			$subc_sql_data .=" group by a.batch_no,a.batch_against,a.entry_form,a.id,c.process_end_date,a.batch_weight,a.color_id,a.booking_no,a.extention_no)";*/

			// Sample
			// 	$samp_sql_data="SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, c.process_end_date as batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no, a.total_trims_weight,a.color_range_id,b.barcode_no,count(b.width_dia_type) as num_of_rows from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess c
			// where a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and a.batch_against=3 and c.entry_form=35 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=0 $batch_no_cond $date_cond $booking_no_cond $workingCompany_cond $po_cond_for_in
			// group by a.batch_no,a.batch_against,a.floor_id,a.entry_form,a.id,c.process_end_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,a.total_trims_weight,a.color_range_id,b.barcode_no 
			// union
			// SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, c.process_end_date as batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.total_trims_weight,a.color_range_id,b.barcode_no,count(b.width_dia_type) as num_of_rows from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess c
			// where a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and a.batch_against=3 and c.entry_form=35  and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=0 and b.po_id=0 $batch_no_cond  $date_cond $booking_no_cond $workingCompany_cond  
			// group by a.batch_no,a.batch_against,a.floor_id,a.entry_form,a.id,c.process_end_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,a.total_trims_weight,a.color_range_id,b.barcode_no 
			// ";
			
			// $sales_sql_data="(SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, c.process_end_date as batch_date,a.batch_weight,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.style_ref_no,a.booking_no,a.extention_no,a.sales_order_no,a.sales_order_id,d.within_group,d.sales_booking_no,d.buyer_id,d.po_buyer,d.po_job_no, a.total_trims_weight,a.color_range_id,b.barcode_no,count(b.width_dia_type) as num_of_rows 
			// from pro_batch_create_dtls b,pro_batch_create_mst a,pro_fab_subprocess c,fabric_sales_order_mst d 
			// where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=a.sales_order_id  and c.entry_form=35 and a.batch_against !=3 and a.is_sales=1 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $batch_no_cond   $date_cond $booking_no_cond $workingCompany_cond $type_cond group by a.batch_no,a.floor_id,a.batch_against,a.entry_form,a.id,c.process_end_date,a.batch_weight,a.color_id,a.style_ref_no,a.booking_no,a.sales_order_no,a.sales_order_id,a.extention_no,b.prod_id,b.po_id,b.width_dia_type,d.sales_booking_no,d.buyer_id,d.po_buyer,d.within_group,d.po_job_no,a.total_trims_weight,a.color_range_id,b.barcode_no)";
			
			//echo $self_sql_data;die;
		}

		if($report_type==1 ) //Roll Wise
		{
			//echo $self_sql_data;die;
			$self_nameArray=sql_select($self_sql_data);
			//$subc_nameArray=sql_select($subc_sql_data);
			//$samp_nameArray=sql_select($samp_sql_data);
			//$sales_nameArray=sql_select($sales_sql_data);
			$roll_wise_arr=array();
			$batch_roll_wise_arr=array();
			$batch_id_check =array();
			$prod_id_check =array();

			// if(!empty($self_nameArray) || !empty($subc_nameArray) || !empty($samp_nameArray) || !empty($sales_nameArray) )	
			// {
			if(!empty($self_nameArray))	
			{
				$con = connect();
				$r_id=execute_query("delete from tmp_batch_id where userid=$user_id");
				$r_id3=execute_query("delete from tmp_prod_id where userid=$user_id");
				if($r_id || $r_id3)
				{
					oci_commit($con);
				}
			}

			foreach($self_nameArray as $row)
			{
				//$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]];
				$batch_roll_wise_arr[$row[csf('id')]]['batch_qty']+=$row[csf('batch_qty')];
				$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['barcode_no']=$row[csf('barcode_no')];
				$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['batch_no']=$row[csf('batch_no')];
				$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['po_id'].=$row[csf('po_id')].',';
				$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['booking_no']=$row[csf('booking_no')];
				$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['batch_qty']+=$row[csf('batch_qty')];
				$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['batch_against']=$row[csf('batch_against')];
				$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['batch_date']=$row[csf('batch_date')];
				$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['entry_form']=$row[csf('entry_form')];
				$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['fabric_type'].=$row[csf('prod_id')].',';
				$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['color_id']=$row[csf('color_id')];
				$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['width_dia_type'].=$row[csf('width_dia_type')].',';

				if(!$batch_id_check[$row[csf('id')]])
				{
					$batch_id_check[$row[csf('id')]]=$row[csf('id')];
					$BATCHID = $row[csf('id')];
					$rID=execute_query("insert into tmp_batch_id (userid, batch_id) values ($user_id,$BATCHID)");
				}

				if(!$prod_id_check[$row[csf('prod_id')]])
				{
					$prod_id_check[$row[csf('prod_id')]]=$row[csf('prod_id')];
					$PRODID = $row[csf('prod_id')];
					$rID3=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_id,$PRODID)");
					
				}
				
			}
			//var_dump($batch_roll_wise_arr);die;
			// foreach($subc_nameArray as $row)
			// {
			// 	//$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]];
			// 	$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['barcode_no']=$row[csf('barcode_no')];
			// 	$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['batch_no']=$row[csf('batch_no')];
			// 	$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['po_id'].=$row[csf('po_id')].',';
			// 	$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['booking_no']=$row[csf('booking_no')];
			// 	$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['batch_qty']+=$row[csf('batch_qty')];
			// 	$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['batch_against']=$row[csf('batch_against')];
			// 	$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['batch_date']=$row[csf('batch_date')];
			// 	$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['entry_form']=$row[csf('entry_form')];
			// 	$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['item_description'].=$row[csf('item_description')].',';
			// 	$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['color_id']=$row[csf('color_id')];
			// 	$roll_wise_arr[$row[csf('id')]][$row[csf('barcode_no')]]['width_dia_type'].=$row[csf('width_dia_type')].',';

			// 	if(!$batch_id_check[$row[csf('id')]])
			// 	{
			// 		$batch_id_check[$row[csf('id')]]=$row[csf('id')];
			// 		$BATCHID = $row[csf('id')];
			// 		$rID=execute_query("insert into tmp_batch_id (userid, batch_id) values ($user_id,$BATCHID)");
			// 	}
			
			// }

			if($rID || $rID3)
			{
				oci_commit($con);
			}

			//echo "select a.id,a.gsm,a.product_name_details from product_details_master a,tmp_prod_id b  where a.id=b.prod_id and b.userid=$user_id and a.status_active=1 and a.is_deleted=0";die;
			$prod_sql= sql_select("select a.id,a.gsm,a.product_name_details from product_details_master a,tmp_prod_id b  where a.id=b.prod_id and b.userid=$user_id and a.status_active=1 and a.is_deleted=0");
			foreach($prod_sql as $row)
			{
			$prod_detail_arr[$row[csf("id")]]=$row[csf("product_name_details")];
			$prod_detail_gsm_arr[$row[csf("id")]]=$row[csf("gsm")];
			}

			$sql_roll_process_qnty="SELECT a.entry_form,a.batch_id,a.process_end_date,a.end_hours,a.end_minutes, b.barcode_no,sum(b.production_qty) AS process_qty from tmp_batch_id c, pro_fab_subprocess a,pro_fab_subprocess_dtls b
			where c.batch_id=a.batch_id and a.id=b.mst_id and c.userid=$user_id and a.company_id=$company_name and a.entry_form in(30,31,32,33,34,48) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.batch_id,a.entry_form,a.process_end_date,a.end_hours,a.end_minutes, b.barcode_no";
			//echo $sql_roll_process_qnty;die;
			$sql_roll_process_qnty_data = sql_select($sql_roll_process_qnty);
			$process_roll_arr_qnty=array();
			foreach($sql_roll_process_qnty_data as $row_h)
			{
				if($row_h[csf('entry_form')]==30)
				{
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['sliting_qnty']=$row_h[csf('process_qty')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['sliting_process_end_date']=$row_h[csf('process_end_date')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['sliting_end_hours']=$row_h[csf('end_hours')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['sliting_end_minutes']=$row_h[csf('end_minutes')];					
				}
				else if($row_h[csf('entry_form')]==31)
				{
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['drying_qnty']=$row_h[csf('process_qty')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['drying_process_end_date']=$row_h[csf('process_end_date')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['drying_end_hours']=$row_h[csf('end_hours')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['drying_end_minutes']=$row_h[csf('end_minutes')];					
				}
				else if($row_h[csf('entry_form')]==32)
				{
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['heat_qnty']=$row_h[csf('process_qty')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['heat_process_end_date']=$row_h[csf('process_end_date')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['heat_end_hours']=$row_h[csf('end_hours')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['heat_end_minutes']=$row_h[csf('end_minutes')];					
				}
				else if($row_h[csf('entry_form')]==33)
				{
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['compct_qnty']=$row_h[csf('process_qty')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['compct_process_end_date']=$row_h[csf('process_end_date')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['compct_end_hours']=$row_h[csf('end_hours')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['compct_end_minutes']=$row_h[csf('end_minutes')];					
				}
				else if($row_h[csf('entry_form')]==34)
				{
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['special_qnty']=$row_h[csf('process_qty')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['special_process_end_date']=$row_h[csf('process_end_date')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['special_end_hours']=$row_h[csf('end_hours')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['special_end_minutes']=$row_h[csf('end_minutes')];					
				}
				else if($row_h[csf('entry_form')]==34)
				{
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['stenter_qnty']=$row_h[csf('process_qty')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['stenter_process_end_date']=$row_h[csf('process_end_date')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['stenter_end_hours']=$row_h[csf('end_hours')];
					$process_roll_arr_qnty[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['stenter_end_minutes']=$row_h[csf('end_minutes')];					
				}				
			}
			//var_dump($process_roll_arr_qnty);die;

			//$sql_roll_h=sql_select("select a.batch_id,b.barcode_no,sum(CASE WHEN a.entry_form=32 THEN b.batch_qty ELSE 0 END) AS heat_qty  from  pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.entry_form in(32,30)  and a.status_active=1 and a.is_deleted=0 and a.batch_id>0 $dyeing_company_cond ");
			// $roll_heat_setting_arr=array();
			// $sql_roll_heat="SELECT a.batch_id,a.process_end_date,a.end_hours,a.end_minutes,b.barcode_no, sum(b.production_qty) as production_qty from pro_fab_subprocess a,pro_fab_subprocess_dtls b,tmp_batch_id c where c.batch_id= a.batch_id and a.id=b.mst_id and c.userid=$user_id and a.company_id=$company_name and a.entry_form =32 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.batch_id,a.process_end_date,a.end_hours,a.end_minutes, b.barcode_no";
			// //echo $sql_roll_heat;die;
			// $sql_roll_heat_data = sql_select($sql_roll_heat);

			// foreach($sql_roll_heat_data as $row_h)
			// {
			// 	$roll_heat_setting_arr[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['qty']=$row_h[csf('production_qty')];
			// 	$roll_heat_setting_arr[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['process_end_date']=$row_h[csf('process_end_date')];
			// 	$roll_heat_setting_arr[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['end_hours']=$row_h[csf('end_hours')];
			// 	$roll_heat_setting_arr[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['end_minutes']=$row_h[csf('end_minutes')];
			// }

			//var_dump($roll_heat_setting_arr);die; 

			// $roll_heat_setting_arr=array();
			// $sql_roll_heat="SELECT a.batch_id,b.barcode_no,sum(CASE WHEN a.entry_form=32 THEN b.batch_qty ELSE 0 END) AS heat_qty  from  pro_fab_subprocess a,pro_fab_subprocess_dtls b,tmp_batch_id c where c.batch_id= a.batch_id and a.id=b.mst_id and c.userid=$user_id and a.company_id=$company_name and a.entry_form in(32,30)  and a.status_active=1 and a.is_deleted=0 and a.batch_id>0  group by a.batch_id";
			// echo $sql_roll_heat;die; 
			// $sql_roll_heat_data = sql_select($sql_roll_heat);
			// foreach($sql_batch_h as $row_h)
			// {
			// 	$roll_heat_setting_arr[$row_h[csf('batch_id')]][$row_h[csf('barcode_no')]]['qty']=$row_h[csf('production_qty')];
			// 	$heat_setting_arr[$row_h[csf('batch_id')]]['qty']=$row_h[csf('heat_qty')];
			// 	//$heat_setting_arr[$row_h[csf('batch_id')]]['machine']=$row_h[csf('machine_id')];
			// } //var_dump($heat_setting_arr);
			
			// $sql_roll_load ="select a.batch_id, b.barcode_no,a.load_unload_id,
			// sum(CASE WHEN a.load_unload_id=1 THEN b.production_qty ELSE 0 END) AS deying_load_qty,
			// sum(CASE WHEN a.load_unload_id=2 THEN b.production_qty ELSE 0 END) AS deying_unload_qty
			// from pro_fab_subprocess a,pro_fab_subprocess_dtls b,tmp_batch_id c where c.batch_id= a.batch_id and a.id=b.mst_id and c.userid=$user_id and a.company_id=$company_name and a.entry_form=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.batch_id, b.barcode_no,a.load_unload_id";

			$sql_barcode="SELECT c.barcode_no,a.receive_date, c.qnty from tmp_batch_id d, inv_receive_mas_batchroll a,pro_grey_batch_dtls b,pro_roll_details c WHERE d.batch_id = b.batch_id and a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and d.userid=$user_id and a.company_id=$company_name and  a.entry_form = 63  and c.entry_form = 63 AND c.roll_no>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 AND c.status_active = 1 AND c.is_deleted = 0 ";
			//echo $sql_barcode;die;
			$data_array=sql_select($sql_barcode);
			if(!empty($data_array))	
			{
				$con = connect();
				$r_id4=execute_query("delete from tmp_barcode_no where userid=$user_id");
				if($r_id4)
				{
					oci_commit($con);
				}
			}
			$barcode_no_check =array();
			//$barcode_nos="";
			$grey_roll_issue_date_arr=array();
			foreach($data_array as $row)
			{
				//$barcode_nos.=$row[csf('barcode_no')].",";
				$grey_roll_issue_date_arr[$row[csf('barcode_no')]]['receive_date']=$row[csf('receive_date')];
				if(!$barcode_no_check[$row[csf('barcode_no')]])
				{
					$barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
					$BARCODENO = $row[csf('barcode_no')];
					//echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$BARCODENO)";
					$rID4=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$BARCODENO)");
					
				}
			}
			//die;
			if($rID4)
			{
				oci_commit($con);
			}

			//$barcode_nos=chop($barcode_nos,",");
			// $barcode_nos=chop($barcode_nos,",");
			//  $barcode_cond_for_in="";
			// $barcode_ids=count(array_unique(explode(",",$barcode_nos)));
			// if($db_type==2 && $barcode_ids>1000)
			// {
			// 	$barcode_cond_for_in=" and (";
			// 	$barcodeIdsArr=array_chunk(explode(",",$barcode_nos),999);
			// 	foreach($barcodeIdsArr as $ids)
			// 	{
			// 		$ids=implode(",",$ids);
			// 		$barcode_cond_for_in.=" b.barcode_no in($ids) or"; 
			// 		$barcode_cond_for_in1.=" c.barcode_no in($ids) or"; 
			// 	}
			// 	$barcode_cond_for_in=chop($barcode_cond_for_in,'or ');
			// 	$barcode_cond_for_in.=")";
			// }
			// else
			// {
			// 	$barcode_cond_for_in=" and b.barcode_no in($barcode_nos)";
			// 	$barcode_cond_for_in1=" and c.barcode_no in($barcode_nos)";
				
			// }
		

			//Grey Roll Issue to Process
			$variable_set_finish=return_field_value("company_name","variable_settings_production","company_name = $company_name and fabric_roll_level = 1 and item_category_id = 2 and variable_list = 3","company_name");
			if ($variable_set_finish) 
			{
				$sql_data="SELECT b.barcode_no as barcode_no, b.production_qty as qnty,f.id as batch_id   
				from tmp_barcode_no g, pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
				where g.barcode_no=b.barcode_no and a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=33 and  g.userid=$user_id and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				group by  b.barcode_no, b.production_qty,f.id";

			}
			else
			{
				$sql_data="SELECT c.barcode_no, c.batch_qnty as qnty,f.id as batch_id 
				from tmp_barcode_no a, pro_batch_create_mst f,pro_batch_create_dtls c, wo_po_break_down d,pro_roll_details e 
				where a.barcode_no=c.barcode_no and f.id=c.mst_id and c.po_id =d.id and  d.id=e.po_breakdown_id and f.id=e.mst_id and c.id=e.dtls_id  and c.barcode_no=e.barcode_no and e.entry_form=64 and a.userid=$user_id and f.company_id=$company_name and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0 
				group by c.barcode_no, c.batch_qnty,f.id 
				union all 
				SELECT c.barcode_no, c.batch_qnty as qnty,f.id as batch_id   
				from tmp_barcode_no a, pro_batch_create_mst f,pro_batch_create_dtls c, wo_po_break_down d,pro_roll_details e
				where a.barcode_no=c.barcode_no and f.id=c.mst_id and c.po_id =d.id and d.id=e.po_breakdown_id and c.barcode_no=e.barcode_no and e.entry_form=61 and a.userid=$user_id and f.company_id=$company_name and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.barcode_no not in(select barcode_no from  pro_roll_details where  entry_form=64)
				group by c.barcode_no, c.batch_qnty,f.id";
			}
			//echo $sql_data;die;
			$data_arrays=sql_select($sql_data);
			$grey_roll_issue_arr=array();
			foreach($data_arrays as $row_sp)
			{
				$grey_roll_issue_arr[$row_sp[csf('batch_id')]][$row_sp[csf('barcode_no')]]['qnty']=$row_sp[csf('qnty')];
				$grey_roll_issue_arr[$row_sp[csf('batch_id')]][$row_sp[csf('barcode_no')]]['issue_date']=$grey_roll_issue_date_arr[$row_sp[csf('barcode_no')]]['receive_date'];
			}
			//var_dump($grey_roll_issue_arr);die;
		
			$sql_roll_load ="SELECT a.batch_id,a.load_unload_id, b.barcode_no,a.process_end_date,a.production_date,a.end_hours,a.end_minutes,
			sum(CASE WHEN a.load_unload_id=1 THEN b.production_qty ELSE 0 END) AS deying_load_qty,
			sum(CASE WHEN a.load_unload_id=2 THEN b.production_qty ELSE 0 END) AS deying_unload_qty
			from tmp_batch_id c, pro_fab_subprocess a,pro_fab_subprocess_dtls b where c.batch_id= a.batch_id and a.id=b.mst_id and c.userid=$user_id and a.company_id=$company_name and a.entry_form=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.batch_id, b.barcode_no,a.process_end_date,a.load_unload_id,a.production_date,a.end_hours,a.end_minutes";

			//echo $sql_roll_load;die;
			$sql_roll_load_data = sql_select($sql_roll_load);
							
			$roll_loading_data_arr=array();
			foreach($sql_roll_load_data as $row_dyeing)// for Loading time
			{
				$roll_loading_data_arr[$row_dyeing[csf('batch_id')]][$row_dyeing[csf('barcode_no')]]['load']+=$row_dyeing[csf('deying_load_qty')];
				$roll_loading_data_arr[$row_dyeing[csf('batch_id')]][$row_dyeing[csf('barcode_no')]]['unload']+=$row_dyeing[csf('deying_unload_qty')];
				$roll_loading_data_arr[$row_dyeing[csf('batch_id')]][$row_dyeing[csf('barcode_no')]]['process_end_date']=$row_dyeing[csf('process_end_date')];
				$roll_loading_data_arr[$row_dyeing[csf('batch_id')]][$row_dyeing[csf('barcode_no')]]['production_date']=$row_dyeing[csf('production_date')];
				$roll_loading_data_arr[$row_dyeing[csf('batch_id')]][$row_dyeing[csf('barcode_no')]]['end_hours']=$row_dyeing[csf('end_hours')];
				$roll_loading_data_arr[$row_dyeing[csf('batch_id')]][$row_dyeing[csf('barcode_no')]]['end_minutes']=$row_dyeing[csf('end_minutes')];
				$roll_loading_data_arr[$row_dyeing[csf('batch_id')]][$row_dyeing[csf('barcode_no')]]['load_unload_id']=$row_dyeing[csf('load_unload_id')];
			
			}
			//var_dump($roll_loading_data_arr);die; 
			
		
			
			// $sql_slitting="SELECT a.batch_id,a.process_end_date,a.end_hours,a.end_minutes, b.barcode_no, sum(b.batch_qty) AS slitting_qty from  pro_fab_subprocess a,pro_fab_subprocess_dtls b,tmp_batch_id c where c.batch_id= a.batch_id and a.id=b.mst_id and a.entry_form in(30)  and a.status_active=1 and a.is_deleted=0 and a.batch_id>0 and c.userid=$user_id and a.company_id=$company_name  group by a.batch_id,a.process_end_date,a.end_hours,a.end_minutes,b.barcode_no";
			// //echo $sql_slitting;die;
			// $sql_slitting_data =sql_select($sql_slitting);
			// $roll_slitting_arr=array();
			// foreach($sql_slitting_data as $row_s)
			// {
			// 	$roll_slitting_arr[$row_s[csf('batch_id')]][$row_s[csf('barcode_no')]]['slitting']=$row_s[csf('slitting_qty')];
			// 	$roll_slitting_arr[$row_s[csf('batch_id')]][$row_s[csf('barcode_no')]]['process_end_date']=$row_s[csf('process_end_date')];
			// 	$roll_slitting_arr[$row_s[csf('batch_id')]][$row_s[csf('barcode_no')]]['end_hours']=$row_s[csf('end_hours')];
			// 	$roll_slitting_arr[$row_s[csf('batch_id')]][$row_s[csf('barcode_no')]]['end_minutes']=$row_s[csf('end_minutes')];
			// }
			//var_dump($roll_slitting_arr);die;
			
			// $roll_sql_stenter="SELECT a.batch_id,a.process_end_date,a.end_hours,a.end_minutes,b.barcode_no,sum(b.production_qty) AS stentering_qty from tmp_batch_id c, pro_fab_subprocess a,pro_fab_subprocess_dtls b where c.batch_id= a.batch_id and a.id=b.mst_id and a.entry_form in(48) and a.status_active=1 and a.is_deleted=0  and c.userid=$user_id and a.company_id=$company_name and a.batch_id>0 group by a.batch_id,a.process_end_date,a.end_hours,a.end_minutes,b.barcode_no"; //  and a.re_stenter_no=0
			// //echo $roll_sql_stenter;die;
			// $sql_stenter_data=sql_select($roll_sql_stenter);
			// $roll_stentering_arr=array();
			// foreach($sql_stenter_data as $row_st)
			// {
			// 	$roll_stentering_arr[$row_st[csf('batch_id')]][$row_st[csf('barcode_no')]]['stentering']=$row_st[csf('stentering_qty')];
			// 	$roll_stentering_arr[$row_st[csf('batch_id')]][$row_st[csf('barcode_no')]]['process_end_date']=$row_st[csf('process_end_date')];
			// 	$roll_stentering_arr[$row_st[csf('batch_id')]][$row_st[csf('barcode_no')]]['end_hours']=$row_st[csf('end_hours')];
			// 	$roll_stentering_arr[$row_st[csf('batch_id')]][$row_st[csf('barcode_no')]]['end_minutes']=$row_st[csf('end_minutes')];
			// }
			//var_dump($roll_stentering_arr);die;

			// $roll_sql_compect="SELECT a.batch_id,a.process_end_date,a.end_hours,a.end_minutes,b.barcode_no,sum(b.production_qty) AS compact_qty  from tmp_batch_id c, pro_fab_subprocess a,pro_fab_subprocess_dtls b where c.batch_id= a.batch_id and a.id=b.mst_id and a.entry_form in(33)  and a.status_active=1 and a.is_deleted=0 and c.userid=$user_id and a.company_id=$company_name and a.batch_id>0 group by a.batch_id,a.process_end_date,a.end_hours,a.end_minutes,b.barcode_no"; // and a.re_stenter_no=0
			// //echo $roll_sql_compect;die;
			// $roll_sql_compect_data=sql_select($roll_sql_compect);  
			// $roll_compacting_arr=array();
			// foreach($roll_sql_compect_data as $row_com)
			// {
			// 	$roll_compacting_arr[$row_com[csf('batch_id')]][$row_com[csf('barcode_no')]]['compact']=$row_com[csf('compact_qty')];
			// 	$roll_compacting_arr[$row_com[csf('batch_id')]][$row_com[csf('barcode_no')]]['process_end_date']=$row_com[csf('process_end_date')];
			// 	$roll_compacting_arr[$row_com[csf('batch_id')]][$row_com[csf('barcode_no')]]['end_hours']=$row_com[csf('end_hours')];
			// 	$roll_compacting_arr[$row_com[csf('batch_id')]][$row_com[csf('barcode_no')]]['end_minutes']=$row_com[csf('end_minutes')];
				
			// }
			//var_dump($roll_compacting_arr);die;

			// $roll_sql_drying="SELECT a.batch_id,a.process_end_date,a.end_hours,a.end_minutes,b.barcode_no,sum(b.production_qty) AS drying_qty from  pro_fab_subprocess a,pro_fab_subprocess_dtls b,tmp_batch_id c where c.batch_id= a.batch_id and a.id=b.mst_id and a.entry_form in(31)  and a.status_active=1 and a.is_deleted=0 and c.userid=$user_id and a.company_id=$company_name group by a.batch_id,a.process_end_date,a.end_hours,a.end_minutes,b.barcode_no";
			// //echo $roll_sql_drying;die;
			// $roll_sql_drying_data=sql_select($roll_sql_drying);
			// $roll_drying_arr=array();
			// foreach($roll_sql_drying_data as $row_d)
			// {
			// 	$roll_drying_arr[$row_d[csf('batch_id')]][$row_d[csf('barcode_no')]]['drying']=$row_d[csf('drying_qty')];
			// 	$roll_drying_arr[$row_d[csf('batch_id')]][$row_d[csf('barcode_no')]]['process_end_date']=$row_d[csf('process_end_date')];
			// 	$roll_drying_arr[$row_d[csf('batch_id')]][$row_d[csf('barcode_no')]]['end_hours']=$row_d[csf('end_hours')];
			// 	$roll_drying_arr[$row_d[csf('batch_id')]][$row_d[csf('barcode_no')]]['end_minutes']=$row_d[csf('end_minutes')];
			// }
			//var_dump($roll_drying_arr);die;
		
			
			$roll_sql_qc_dtls="SELECT a.batch_id, d.barcode_no, d.roll_weight, d.reject_qnty,d.comments,d.qc_date from tmp_batch_id f,pro_finish_fabric_rcv_dtls a, pro_qc_result_mst d,pro_qc_result_dtls e where f.batch_id= a.batch_id and a.id=d.pro_dtls_id and d.id=e.mst_id and f.userid=$user_id and d.entry_form=267 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by a.batch_id,  d.roll_weight, d.reject_qnty ,d.barcode_no,d.comments,d.qc_date";

			// d.pro_dtls_id=24893 and 
			//echo $roll_sql_qc_dtls;die; 
			$roll_sql_qc_dtls_data=sql_select($roll_sql_qc_dtls); 
					
			$roll_qc_dtls_data_arr=array();
			foreach($roll_sql_qc_dtls_data as $row_qc)
			{				
				$roll_qc_dtls_data_arr[$row_qc[csf('batch_id')]][$row_qc[csf('barcode_no')]]['roll_weight']=$row_qc[csf('roll_weight')];
				$roll_qc_dtls_data_arr[$row_qc[csf('batch_id')]][$row_qc[csf('barcode_no')]]['reject_qnty']=$row_qc[csf('reject_qnty')];
				$roll_qc_dtls_data_arr[$row_qc[csf('batch_id')]][$row_qc[csf('barcode_no')]]['comments']=$row_qc[csf('comments')];
				$roll_qc_dtls_data_arr[$row_qc[csf('batch_id')]][$row_qc[csf('barcode_no')]]['qc_date']=$row_qc[csf('qc_date')];
			}
			//var_dump($roll_qc_dtls_data_arr);die;

			$roll_fin_fab_r_del = "SELECT a.id, a.delevery_date, b.batch_id, d.qnty 
			from pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b, pro_batch_create_mst c,pro_roll_details  d, tmp_batch_id e
			where e.batch_id= b.batch_id and a.id=b.mst_id and b.batch_id=c.id and b.id=d.dtls_id and e.userid=$user_id and a.company_id=$company_name and a.entry_form=67 and d.entry_form=67 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  
			group by a.id, a.delevery_date, b.batch_id,  a.knitting_company, d.qnty";

			//echo $roll_fin_fab_r_del;die;
			$roll_fin_fab_r_del_data=sql_select($roll_fin_fab_r_del); 
					
			$roll_fin_fab_r_del_data_arr=array();
			foreach($roll_fin_fab_r_del_data as $row_roll)
			{				 
				$roll_fin_fab_r_del_data_arr[$row_roll[csf('batch_id')]][$row_roll[csf('barcode_num')]]['qnty']=$row_roll[csf('qnty')];
				$roll_fin_fab_r_del_data_arr[$row_roll[csf('batch_id')]][$row_roll[csf('barcode_num')]]['delevery_date']=$row_roll[csf('delevery_date')];
			} 
			//var_dump($roll_fin_fab_r_del_data_arr);die;

			$inserted_roll="SELECT a.receive_date,c.qc_pass_qnty,c.barcode_no,b.batch_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_roll_details c,tmp_batch_id d where d.batch_id= b.batch_id and  a.id=b.mst_id and b.id=c.dtls_id and d.userid=$user_id and a.company_id=$company_name and a.entry_form=68 and c.entry_form=68  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.receive_date,c.qc_pass_qnty,b.batch_id,c.barcode_no";
			//echo $inserted_roll;die;
			$inserted_roll_data=sql_select($inserted_roll); 
			
			$inserted_roll_barcode_arr=array();
			foreach($inserted_roll_data as $inf)
			{
				$inserted_roll_barcode_arr[$inf[csf('batch_id')]][$inf[csf('barcode_no')]]['qc_pass_qnty']+=$inf[csf('qc_pass_qnty')];
				$inserted_roll_barcode_arr[$inf[csf('batch_id')]][$inf[csf('barcode_no')]]['receive_date']=$inf[csf('receive_date')];
			}
			//var_dump($inserted_roll_barcode_arr);die; 			

			// $roll_sql_special="SELECT a.batch_id,a.process_end_date,a.end_hours,a.end_minutes,b.barcode_no,sum(b.production_qty) AS special_qty from  pro_fab_subprocess a,pro_fab_subprocess_dtls b,tmp_batch_id c where c.batch_id = a.batch_id and a.id=b.mst_id and a.entry_form in(34) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.userid=$user_id and a.company_id=$company_name and a.batch_id>0 group by a.batch_id,a.process_end_date,a.end_hours,a.end_minutes,b.barcode_no";
			// //echo $roll_sql_special;die;
			// $roll_sql_special_data=sql_select($roll_sql_special);
			// $roll_special_arr=array();
			// foreach($roll_sql_special_data as $row_sp)
			// {
			// 	$roll_special_arr[$row_sp[csf('batch_id')]][$row_sp[csf('barcode_no')]]['special']=$row_sp[csf('special_qty')];
			// 	$roll_special_arr[$row_sp[csf('batch_id')]][$row_sp[csf('barcode_no')]]['process_end_date']=$row_sp[csf('process_end_date')];
			// 	$roll_special_arr[$row_sp[csf('batch_id')]][$row_sp[csf('barcode_no')]]['end_hours']=$row_sp[csf('end_hours')];
			// 	$roll_special_arr[$row_sp[csf('batch_id')]][$row_sp[csf('barcode_no')]]['end_minutes']=$row_sp[csf('end_minutes')];
			// }
			//var_dump($roll_special_arr);die;

			

			//AOP Roll Receive
			$sql_aop="SELECT  a.id,   a.receive_date, b.batch_id, c.barcode_no,   c.qnty from inv_receive_mas_batchroll a,pro_grey_batch_dtls b,pro_roll_details c,tmp_batch_id d where d.batch_id = b.batch_id and a.id=b.mst_id and b.id=c.dtls_id and a.id=b.mst_id and d.userid=$user_id and a.company_id=$company_name and a.status_active = 1 AND a.is_deleted = 0 and b.status_active = 1 AND b.is_deleted = 0 and c.status_active = 1 AND c.is_deleted = 0 and a.entry_form IN(65) AND c.entry_form IN(65) AND c.roll_no>0";

			//echo $sql_aop;die;
			$sql_aop_result=sql_select($sql_aop);
			$aop_roll_arr=array();
			foreach($sql_aop_result as $row_sp)
			{
				$aop_roll_arr[$row_sp[csf('batch_id')]][$row_sp[csf('barcode_no')]]['qnty']=$row_sp[csf('qnty')];
				$aop_roll_arr[$row_sp[csf('batch_id')]][$row_sp[csf('barcode_no')]]['receive_date']=$row_sp[csf('receive_date')];
			}
			//var_dump($aop_roll_arr);die;

			$r_id=execute_query("delete from tmp_batch_id where userid=$user_id");
			$r_id2=execute_query("delete from tmp_poid where userid=$user_id and type=21");
			$r_id3=execute_query("delete from tmp_prod_id where userid=$user_id");
			$r_id4=execute_query("delete from tmp_barcode_no where userid=$user_id");
			if($r_id) $flag=1; else $flag=0;
			if($r_id2) $flag=2; else $flag=0;
			if($r_id3) $flag=3; else $flag=0;
			if($r_id4) $flag=4; else $flag=0;
			if($flag==1 || $flag==2 || $flag==3 || $flag=4)
			{
				oci_commit($con);
			}

			

		}

		else if($report_type==2 ) //Batch Wise
		{
			//echo $self_sql_data;die;
			$self_nameArray=sql_select($self_sql_data);
			//$subc_nameArray=sql_select($subc_sql_data);
			//$samp_nameArray=sql_select($samp_sql_data);
			//$sales_nameArray=sql_select($sales_sql_data);
			$batch_wise_arr=array();
			$batch_id_check =array();
			$prod_id_check =array();
			// if(!empty($self_nameArray) || !empty($subc_nameArray) || !empty($samp_nameArray) || !empty($sales_nameArray) )	
			// {
			if(!empty($self_nameArray) )	
			{
				$con = connect();
				$r_id=execute_query("delete from tmp_batch_id where userid=$user_id");
				$r_id3=execute_query("delete from tmp_prod_id where userid=$user_id");
				if($r_id || $r_id3)
				{
					oci_commit($con);
				}
			}
			
			//$prod_ids='';
			foreach($self_nameArray as $row)
			{	
				//$prod_ids.= $row[csf('prod_id')].',';
				$batch_wise_arr[$row[csf('id')]]['id']=$row[csf('id')];
				$batch_wise_arr[$row[csf('id')]]['po_id'].=$row[csf('po_id')].',';
				$batch_wise_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
				$batch_wise_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
				$batch_wise_arr[$row[csf('id')]]['fabric_type'].=$row[csf('prod_id')].',';
				// $batch_wise_arr[$row[csf('id')]]['num_of_rows']+=$row[csf('num_of_rows')];
				$batch_wise_arr[$row[csf('id')]]['num_of_rows']++;
				$batch_wise_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
				$batch_wise_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
				$batch_wise_arr[$row[csf('id')]]['width_dia_type'].=$row[csf('width_dia_type')].',';
				$batch_wise_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
				$batch_wise_arr[$row[csf('id')]]['batch_qty']+=$row[csf('batch_qty')];
				$batch_wise_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];


				if(!$batch_id_check[$row[csf('id')]])
				{
					$batch_id_check[$row[csf('id')]]=$row[csf('id')];
					$BATCHID = $row[csf('id')];
					$rID=execute_query("insert into tmp_batch_id (userid, batch_id) values ($user_id,$BATCHID)");
				}

				if(!$prod_id_check[$row[csf('prod_id')]])
				{
					$prod_id_check[$row[csf('prod_id')]]=$row[csf('prod_id')];
					$PRODID = $row[csf('prod_id')];
					$rID3=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_id,$PRODID)");
					
				}
				
			}

			if($rID || $rID3)
			{
				oci_commit($con);
			}
			
			
			 //echo "select a.id,a.gsm,a.product_name_details from product_details_master a,tmp_prod_id b  where a.id=b.prod_id and b.userid=$user_id and a.status_active=1 and a.is_deleted=0";die;
			$prod_sql= sql_select("select a.id,a.gsm,a.product_name_details from product_details_master a,tmp_prod_id b  where a.id=b.prod_id and b.userid=$user_id and a.status_active=1 and a.is_deleted=0");
			foreach($prod_sql as $row)
			{
				$prod_detail_arr[$row[csf("id")]]=$row[csf("product_name_details")];
				$prod_detail_gsm_arr[$row[csf("id")]]=$row[csf("gsm")];
			}

			
			//var_dump($batch_wise_arr);die;

			// foreach($subc_nameArray as $row)
			// {			
			// 	// $booking_no=explode("-",$row[csf('booking_no')]);
			// 	// $booking_no_type=$booking_no[1];

			// 	$batch_wise_arr[$row[csf('id')]]['id']=$row[csf('id')];
			// 	$batch_wise_arr[$row[csf('id')]]['po_id'].=$row[csf('po_id')].',';
			// 	$batch_wise_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
			// 	$batch_wise_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			// 	$batch_wise_arr[$row[csf('id')]]['item_description'].=$row[csf('item_description')].',';
			// 	// $batch_wise_arr[$row[csf('id')]]['num_of_rows']+=$row[csf('num_of_rows')];
			// 	$batch_wise_arr[$row[csf('id')]]['num_of_rows']++;
			// 	$batch_wise_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
			// 	$batch_wise_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
			// 	$batch_wise_arr[$row[csf('id')]]['width_dia_type'].=$row[csf('width_dia_type')].',';
			// 	$batch_wise_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			// 	$batch_wise_arr[$row[csf('id')]]['batch_qty']+=$row[csf('batch_qty')];
			// 	$batch_wise_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];

			// 	if(!$batch_id_check[$row[csf('id')]])
			// 	{
			// 		$batch_id_check[$row[csf('id')]]=$row[csf('id')];
			// 		$BATCHID = $row[csf('id')];
			// 		$rID=execute_query("insert into tmp_batch_id (userid, batch_id) values ($user_id,$BATCHID)");
			// 	}
			// }

			// foreach($samp_nameArray as $row)
			// {
			// 	// $booking_no=explode("-",$row[csf('booking_no')]);
			// 	// $booking_no_type=$booking_no[1];
							
			// 	$batch_wise_arr[$row[csf('id')]]['id']=$row[csf('id')];
			// 	$batch_wise_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
			// 	$batch_wise_arr[$row[csf('id')]]['po_id'].=$row[csf('po_id')].',';
			// 	$batch_wise_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			// 	$batch_wise_arr[$row[csf('id')]]['fabric_type'].=$row[csf('prod_id')].',';
			// 	// $batch_wise_arr[$row[csf('id')]]['num_of_rows']+=$row[csf('num_of_rows')];
			// 	$batch_wise_arr[$row[csf('id')]]['num_of_rows']++;
			// 	$batch_wise_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
			// 	$batch_wise_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
			// 	$batch_wise_arr[$row[csf('id')]]['width_dia_type'].=$row[csf('width_dia_type')].',';
			// 	$batch_wise_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			// 	$batch_wise_arr[$row[csf('id')]]['batch_qty']+=$row[csf('batch_qty')];
			// 	$batch_wise_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];

			// 	if(!$batch_id_check[$row[csf('id')]])
			// 	{
			// 		$batch_id_check[$row[csf('id')]]=$row[csf('id')];
			// 		$BATCHID = $row[csf('id')];
			// 		$rID=execute_query("insert into tmp_batch_id (userid, batch_id) values ($user_id,$BATCHID)");
			// 	}
			// }
			//var_dump($batch_wise_arr);

			// foreach($sales_nameArray as $row)
			// {			
			// 	// $booking_no=explode("-",$row[csf('booking_no')]);
			// 	// $booking_no_type=$booking_no[1];
			
			// 	if($row[csf('within_group')]==2)
			// 	{
			// 		//echo $buyer_library[$row[csf('buyer_id')]].'XZZZ';
			// 		$batch_wise_arr[$row[csf('id')]]['buyer']=$buyer_library[$row[csf('buyer_id')]];
			// 	}
			// 	else {
			// 		$batch_wise_arr[$row[csf('id')]]['buyer']=$company_library[$row[csf('buyer_id')]];
			// 	}

			// 	$batch_wise_arr[$row[csf('id')]]['id']=$row[csf('id')];
			// 	$batch_wise_arr[$row[csf('id')]]['entry_form']=$row[csf('entry_form')];
			// 	$batch_wise_arr[$row[csf('id')]]['po_id'].=$row[csf('po_id')].',';
			// 	$batch_wise_arr[$row[csf('id')]]['booking_no']=$row[csf('sales_order_no')];
			// 	$batch_wise_arr[$row[csf('id')]]['fabric_type'].=$row[csf('prod_id')].',';
			// 	// $batch_wise_arr[$row[csf('id')]]['num_of_rows']+=$row[csf('num_of_rows')];
			// 	$batch_wise_arr[$row[csf('id')]]['num_of_rows']++;
			// 	$batch_wise_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
			// 	$batch_wise_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
			// 	$batch_wise_arr[$row[csf('id')]]['width_dia_type'].=$row[csf('width_dia_type')].',';
			// 	$batch_wise_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
			// 	$batch_wise_arr[$row[csf('id')]]['batch_qty']+=$row[csf('batch_qty')];
			// 	$batch_wise_arr[$row[csf('id')]]['batch_against']=$row[csf('batch_against')];

			// 	if(!$batch_id_check[$row[csf('id')]])
			// 	{
			// 		$batch_id_check[$row[csf('id')]]=$row[csf('id')];
			// 		$BATCHID = $row[csf('id')];
			// 		$rID=execute_query("insert into tmp_batch_id (userid, batch_id) values ($user_id,$BATCHID)");
			// 	}
			// }
		
			$sql_qc_dtls="SELECT a.batch_id, d.roll_weight, d.reject_qnty,a.barcode_no from tmp_batch_id f,pro_finish_fabric_rcv_dtls a, pro_qc_result_mst d,pro_qc_result_dtls e where f.batch_id = a.batch_id and a.id=d.pro_dtls_id and a.barcode_no=d.barcode_no and d.id=e.mst_id and f.userid=$user_id and d.entry_form=267 and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by a.batch_id,  d.roll_weight, d.reject_qnty,a.barcode_no";

			// d.pro_dtls_id=24893 and 
			//echo $sql_qc_dtls;die; 
			$sql_qc_dtls_data=sql_select($sql_qc_dtls); 
					
			$sql_qc_dtls_data_arr=array();
			foreach($sql_qc_dtls_data as $row_qc)
			{				
				$sql_qc_dtls_data_arr[$row_qc[csf('batch_id')]]['roll_weight']+=$row_qc[csf('roll_weight')];
				$sql_qc_dtls_data_arr[$row_qc[csf('batch_id')]]['reject_qnty']+=$row_qc[csf('reject_qnty')];
				$sql_qc_dtls_data_arr[$row_qc[csf('batch_id')]]['no_of_roll']++;
			}

			//var_dump($sql_qc_dtls_data_arr);die;
			//next finding
			$fin_fab_r_del = "SELECT b.batch_id,d.qnty 
			from pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b, pro_batch_create_mst c,pro_roll_details  d, tmp_batch_id e
			where e.batch_id= b.batch_id and a.id=b.mst_id and b.batch_id=c.id and b.id=d.dtls_id and e.userid=$user_id and a.company_id=$company_name and a.entry_form=67 and d.entry_form=67 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  
			group by a.id, b.batch_id, a.knitting_company,d.qnty";

			//echo $fin_fab_r_del;die;
			$sql_fin_fab_r_del_data=sql_select($fin_fab_r_del); 
					
			$sql_fin_fab_r_del_data_arr=array();
			foreach($sql_fin_fab_r_del_data as $row_roll)
			{
				
				$sql_fin_fab_r_del_data_arr[$row_roll[csf('batch_id')]]['qnty']+=$row_roll[csf('qnty')];
				$sql_fin_fab_r_del_data_arr[$row_roll[csf('batch_id')]]['no_of_roll']++;
			} 
			//var_dump($sql_fin_fab_r_del_data_arr);die;

			$inserted_roll="SELECT c.qc_pass_qnty,b.batch_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_roll_details c,tmp_batch_id d where d.batch_id= b.batch_id and  a.id=b.mst_id and b.id=c.dtls_id and d.userid=$user_id and a.company_id=$company_name and a.entry_form=68 and c.entry_form=68  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.qc_pass_qnty,b.batch_id";
			//echo $inserted_roll;die;
			$inserted_roll_data=sql_select($inserted_roll); 
			
			$inserted_roll_arr=array();
			foreach($inserted_roll_data as $inf)
			{
				$inserted_roll_arr[$inf[csf('batch_id')]]['qc_pass_qnty']+=$inf[csf('qc_pass_qnty')];
			}
			//var_dump($inserted_roll_arr);die;
			$sql_process_qnty="SELECT a.entry_form,a.batch_id,sum(b.production_qty) AS process_qty 
			from tmp_batch_id c, pro_fab_subprocess a,pro_fab_subprocess_dtls b
			where c.batch_id = a.batch_id and a.id=b.mst_id and c.userid=$user_id and a.company_id=$company_name and a.entry_form in(30,31,32,33,34,48)
			and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.batch_id,a.entry_form";
			//echo $sql_process_qnty;die;
			$sql_process_qnty_data = sql_select($sql_process_qnty);
			$process_arr_qnty=array();

			foreach($sql_process_qnty_data as $row_h)
			{
				if($row_h[csf('entry_form')]==30)
				{
					$process_arr_qnty[$row_h[csf('batch_id')]]['sliting_qnty']=$row_h[csf('process_qty')];
				}
				else if($row_h[csf('entry_form')]==31)
				{
					$process_arr_qnty[$row_h[csf('batch_id')]]['drying_qnty']=$row_h[csf('process_qty')];
				}
				else if($row_h[csf('entry_form')]==32)
				{
					$process_arr_qnty[$row_h[csf('batch_id')]]['heat_qnty']=$row_h[csf('process_qty')];
				}
				else if($row_h[csf('entry_form')]==33)
				{
					$process_arr_qnty[$row_h[csf('batch_id')]]['compct_qnty']=$row_h[csf('process_qty')];
				}
				else if($row_h[csf('entry_form')]==34)
				{
					$process_arr_qnty[$row_h[csf('batch_id')]]['special_qnty']=$row_h[csf('process_qty')];
				}
				else if($row_h[csf('entry_form')]==48)
				{
					$process_arr_qnty[$row_h[csf('batch_id')]]['stenter_qnty']=$row_h[csf('process_qty')];
				}				
			}
			//var_dump($process_arr_qnty);die;
			
			
			// $heat_setting_arr=array();
			// $sql_roll_heat="SELECT a.batch_id, sum(b.production_qty) as production_qty from tmp_batch_id c, pro_fab_subprocess a,pro_fab_subprocess_dtls b where c.batch_id= a.batch_id and a.id=b.mst_id and c.userid=$user_id and a.company_id=$company_name and a.entry_form =32 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  group by a.batch_id";
			// //echo $sql_roll_heat;die;
			// $sql_roll_heat_data = sql_select($sql_roll_heat);

			// foreach($sql_roll_heat_data as $row_h)
			// {
			// 	$heat_setting_arr[$row_h[csf('batch_id')]]['qty']=$row_h[csf('production_qty')];
			// }

			// $sql_slitting="SELECT a.batch_id,sum(b.batch_qty) AS slitting_qty from tmp_batch_id c, pro_fab_subprocess a,pro_fab_subprocess_dtls b where c.batch_id= a.batch_id and a.id=b.mst_id and c.userid=$user_id and a.company_id=$company_name and a.entry_form in(30)  and a.batch_id>0  and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1   group by a.batch_id";
			// //echo $sql_slitting;die;
			// $sql_slitting_data=sql_select($sql_slitting);
			// $slitting_arr=array();
			// foreach($sql_slitting_data as $row_s)
			// {
			// 	$slitting_arr[$row_s[csf('batch_id')]]['slitting']=$row_s[csf('slitting_qty')];
			// }
			
			// $sql_stenter="SELECT a.batch_id,sum(b.production_qty) AS stentering_qty from pro_fab_subprocess a,pro_fab_subprocess_dtls b,tmp_batch_id c where c.batch_id= a.batch_id and a.id=b.mst_id and  c.userid=$user_id and a.company_id=$company_name and a.entry_form in(48) and a.batch_id>0 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.batch_id"; //  and a.re_stenter_no=0
			// //echo $sql_stenter;die;
			// $sql_stenter_data=sql_select($sql_stenter);
			// $stentering_arr=array();
			// foreach($sql_stenter_data as $row_st)
			// {
			// 	$stentering_arr[$row_st[csf('batch_id')]]['stentering']=$row_st[csf('stentering_qty')];
			// }
			
			// $sql_compct="SELECT a.batch_id,sum(b.production_qty) AS compact_qty from tmp_batch_id c, pro_fab_subprocess a,pro_fab_subprocess_dtls b where c.batch_id= a.batch_id and a.id=b.mst_id and c.userid=$user_id and a.company_id=$company_name and a.entry_form in(33) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.batch_id"; // and a.re_stenter_no=0 
			// //echo $sql_compct;die; 
			// $sql_compect_data=sql_select($sql_compct);
			// $compacting_arr=array();
			// foreach($sql_compect_data as $row_com)
			// {
			// 	$compacting_arr[$row_com[csf('batch_id')]]['compact']=$row_com[csf('compact_qty')];
			// }

			// $sql_drying="SELECT a.batch_id,sum(b.production_qty) AS drying_qty from tmp_batch_id c, pro_fab_subprocess a,pro_fab_subprocess_dtls b where c.batch_id= a.batch_id and a.id=b.mst_id and c.userid=$user_id and a.company_id=$company_name and a.entry_form in(31) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1  group by a.batch_id";
			// //echo $sql_drying;die; 
			// $sql_drying_data=sql_select($sql_drying);
			// $drying_arr=array();
			// foreach($sql_drying_data as $row_d)
			// {
			// 	$drying_arr[$row_d[csf('batch_id')]]['drying']=$row_d[csf('drying_qty')];
			// }
			// $sql_speca="SELECT a.batch_id,sum(b.production_qty) AS special_qty from tmp_batch_id c, pro_fab_subprocess a,pro_fab_subprocess_dtls b where c.batch_id= a.batch_id and a.id=b.mst_id and c.userid=$user_id and a.company_id=$company_name and a.entry_form in(34) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.batch_id";
			// //echo $sql_speca;die;
			// $sql_special_data=sql_select($sql_speca);
			// $special_arr=array();
			// foreach($sql_special_data as $row_sp)
			// {
			// 	$special_arr[$row_sp[csf('batch_id')]]['special']=$row_sp[csf('special_qty')];
			// }			
			
			$loading_data_arr=array();
			if($cbo_search_date==1)
			{	
				$sql_load="SELECT c.id, sum(CASE WHEN a.load_unload_id=1 THEN c.batch_weight ELSE 0 END) AS deying_load_qty,sum(CASE WHEN a.load_unload_id=2 THEN c.batch_weight ELSE 0 END) AS deying_unload_qty 
				from tmp_batch_id d, pro_fab_subprocess a,pro_batch_create_mst c
				where d.batch_id= a.batch_id and a.batch_id=c.id and d.userid=$user_id and a.load_unload_id in(1,2) and a.entry_form in(35,38) $date_cond_dyeing and a.status_active=1 and a.is_deleted=0 $dyeing_company_cond  group by c.id ";	
				/*echo "SELECT c.id, sum(CASE WHEN a.load_unload_id=1 THEN c.batch_weight ELSE 0 END) AS deying_load_qty,sum(CASE WHEN a.load_unload_id=2 THEN c.batch_weight ELSE 0 END) AS deying_unload_qty 
				from pro_fab_subprocess a,pro_batch_create_mst c 
				where  a.batch_id=c.id and a.load_unload_id in(1,2) and a.entry_form in(35,38) $date_cond_dyeing and a.status_active=1 and a.is_deleted=0 $dyeing_company_cond  group by c.id ";*/
			}
			else
			{
				$sql_load="SELECT c.id, sum(CASE WHEN a.load_unload_id=1 THEN c.batch_weight ELSE 0 END) AS deying_load_qty,sum(CASE WHEN a.load_unload_id=2 THEN c.batch_weight ELSE 0 END) AS deying_unload_qty 
				from tmp_batch_id d, pro_fab_subprocess a,pro_batch_create_mst c  
				where d.batch_id= a.batch_id and a.batch_id=c.id and d.userid=$user_id and a.load_unload_id in(1,2) and a.entry_form in(35,38) $date_cond_dyeing and a.status_active=1 and a.is_deleted=0 $dyeing_company_cond group by c.id ";	
			}	
			//echo $sql_load;die;
			$load_data=sql_select($sql_load);
			
			foreach($load_data as $row_dyeing)// for Loading time
			{
				$loading_data_arr[$row_dyeing[csf('id')]]['load']=$row_dyeing[csf('deying_load_qty')];
				$loading_data_arr[$row_dyeing[csf('id')]]['unload']=$row_dyeing[csf('deying_unload_qty')];
			}

			$r_id=execute_query("delete from tmp_batch_id where userid=$user_id");
			$r_id2=execute_query("delete from tmp_poid where userid=$user_id and type=21");
			$r_id3=execute_query("delete from tmp_prod_id where userid=$user_id");

			if($r_id) $flag=1; else $flag=0;
			if($r_id2) $flag2=2; else $flag2=0;
			if($r_id3) $flag3=3; else $flag2=0;
			if($flag==1 || $flag2==2 || $flag3==3)
			{
				oci_commit($con);
			}
		}		

	ob_start();
	?>
	<fieldset style="width:2590px;">
		<table width="2580" cellspacing="0" cellpadding="0" border="0" rules="all" >
			<tr class="form_caption">
				<td colspan="30" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="30" align="center"><?  if($company_name!=0) echo $company_library[$company_name];else echo $company_library[$working_company]; ?><br>
				</b>
				<? //
			//  echo  change_date_format($start_date).' '.To.' '.change_date_format($end_date);
				echo  ($start_date == '0000-00-00' || $start_date == '' ? '' : change_date_format($start_date)).' To ';echo  ($end_date == '0000-00-00' || $end_date == '' ? '' : change_date_format($end_date));
				?> </b>
				</td>
			</tr>
		</table>
		<?
		if($report_type==1 ) //Roll Wise
		{
			?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2780" class="rpt_table" id="table_header_1">
				<caption> <b style=" float:left"><h3>Batch wise Roll Status Report ( Roll wise)</h3></b></caption>
				<thead>
					<th width="40">SL</th>
					<th width="80">Job No</th>
					<th width="110">Buyer</th>
					<th width="110">F.Booking No</th>
					<th width="70">Order No</th>
					<th width="150">Fabrics Type</th>
					<th width="50">GSM</th>
					<th width="90">Color Name</th>
					<th width="60"><p>Dia/Width Type</p></th>
					<th width="80"><? if($cbo_search_date==1){ echo "Batch Date";}else{echo "Dyeing Date";} ?></th>
					<th width="120">Batch No</th>
					<th width="80">Batch Against</th>
					<th width="80">Batch Qty.</th>
					<th width="150">Barcode no</th>
					<th width="70"><p>HeatSetting / Singeing</p></th>
					<th width="70"><p>Batch Roll. Qty</p></th>
					<th width="80">Dyeing Loding</th>
					<th width="70"><p>Dyeing Un-Loding</p></th>
					<th width="60"><p>Slitting / Squeezing</p></th>
					<th width="80">Stentering</th>
					<th width="80">Compacting</th>
					<th width="80">Drying</th>
					<th width="80">Special Finish</th> 
					<th width="80"><p>Grey Roll Issue to Process<p></th>
					<th width="80"><p>AOP Roll Receive<p></th>
					<th width="80"><p>Fin.Fab.Prod. Entry Qty<p></th>
					<th width="60"><p>Process Loss Qty<p></th>
					<th width="100"><p>Process Loss %</p></th>
					<th width="100"><p>Fin.Fab.Delivery to Store Qty</p></th>
					<th width="80"><p>Delivery Balance Qty.</p></th>
					<th width="100">Receive by Store</th>
					<th width="">Remarks</th>					
				</thead>
			</table>
			<div style="width:2800px; overflow-y:scroll; max-height:450px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2780" class="rpt_table" id="table_body">
			<?
			$j=1;
			foreach($roll_wise_arr as $batch_id=>$batch_data)
			{
				foreach($batch_data as $roll_id=>$roll_data)
				{

				if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$po_id=rtrim($roll_data[('po_id')],',');		
				
				$po_id=array_unique(explode(",",$po_id));

				$fabric_desc=array();
				if(rtrim($roll_data[('fabric_type')],','))
				{
					$fabric_type=rtrim($roll_data[('fabric_type')],',');
					$fabric_desc=array_unique(explode(",",$fabric_type));
				}		
				
				$fab='';
				foreach($fabric_desc as $pid)
				{
				// $fabdesc_type=explode(",",$desc);
					$fab_desc=$prod_detail_arr[$pid];	
					if($fab=='')
					{
						$fabdesc_type=explode(",",$fab_desc);
						$fab=$fabdesc_type[0].",".$fabdesc_type[1];	
						$fab_gsm=$prod_detail_gsm_arr[$pid];	
					}
					else
					{
						$fabdesc_type=explode(",",$fab_desc);
						$fab.="<br>".$fabdesc_type[0].",".$fabdesc_type[1];
						$fab_gsm.=", ".$prod_detail_gsm_arr[$pid];
					}
				}

				$item_desc=rtrim($roll_data[('item_description')],',');
				$item_fabric_desc=implode(",",array_unique(explode(",",$item_desc)));

				$fabrics_type ='';
				if($fab)
				{
					$fabrics_type= $fab;
				}
				else if($item_fabric_desc)
				{
					$fabrics_type= $item_fabric_desc;
				}
				
				$po_numbers=""; $job_no=""; $buyer="";
				foreach($po_id as $id)
				{
					if($roll_data[('entry_form')]==36) //SubCon
					{
						if($po_numbers=="") $po_numbers=$sub_job_array[$id]['po']; else $po_numbers.=",".$sub_job_array[$id]['po'];
						if($job_no=="") $job_no=$sub_job_array[$id]['job']; else $job_no.=",".$sub_job_array[$id]['job'];
						if($buyer=="") $buyer=$buyer_library[$sub_job_array[$id]['buyer']]; else $buyer.=",".$buyer_library[$sub_job_array[$id]['buyer']];
					}
					else
					{
						
						if($po_numbers=="") $po_numbers=$job_array[$id]['po']; else $po_numbers.=",".$job_array[$id]['po'];
						if($job_no=="") $job_no=$job_array[$id]['job']; else $job_no.=",".$job_array[$id]['job'];
						if($buyer=="") $buyer=$buyer_library[$job_array[$id]['buyer']]; else $buyer.=",".$buyer_library[$job_array[$id]['buyer']];
						if($file=="") $file=$job_array[$id]['file']; else $file.=",".$job_array[$id]['file']; // file
						if($refNo=="") $refNo=$job_array[$id]['refNo']; else $refNo.=",".$job_array[$id]['refNo']; // ref
						if($buyer_style=="") $buyer_style=$job_array[$id]['style_no']; else $buyer_style.=','.$job_array[$id]['style_no'];
					}				
				
				}

				if($roll_data[('buyer')])
				{
					$buyer=$roll_data[('buyer')];
				}
				if($buyer==""){$buyer = $buyer_library[$non_order_arr[$roll_data[('booking_no')]]['buyer_name']];}

				$job=implode(',',array_unique(explode(",",$job_no)));
				$po_numbers=implode(', ',array_unique(explode(",",$po_numbers)));
				$buyer_name=implode(',',array_unique(explode(",",$buyer)));

				$dia_type='';  $width_dia_type=rtrim($roll_data[('width_dia_type')],',');
				$dia_type_id=array_unique(explode(",",$width_dia_type));
				foreach($dia_type_id as $dia_id)
				{	
				if($dia_type=="") $dia_type=$fabric_typee[$dia_id]; else $dia_type.=",".$fabric_typee[$dia_id];
				}
				$dia_type_data=implode(',',array_unique(explode(",",$dia_type)));
				$batch_qnty = $batch_roll_wise_arr[$batch_id]['batch_qty'];
				//for heating
				$heat_qty=$process_roll_arr_qntyr[$batch_id][$roll_data[('barcode_no')]]['heat_qnty'];
				$roll_heat_setting_date = change_date_format($process_roll_arr_qntyr[$batch_id][$roll_data[('barcode_no')]]['heat_process_end_date']);
				$roll_heat_setting_hours = $process_roll_arr_qntyr[$batch_id][$roll_data[('barcode_no')]]['heat_end_hours'];
				$roll_heat_setting_minutes = $process_roll_arr_qntyr[$batch_id][$roll_data[('barcode_no')]]['heat_end_minutes'];
				
			
				$load_deying_qty=$roll_loading_data_arr[$batch_id][$roll_data[('barcode_no')]]['load'];
				$unload_deying_qty=$roll_loading_data_arr[$batch_id][$roll_data[('barcode_no')]]['unload'];
				$load_date = change_date_format($roll_loading_data_arr[$batch_id][$roll_data[('barcode_no')]]['process_end_date']);//for loading date
				$unload_date = change_date_format($roll_loading_data_arr[$batch_id][$roll_data[('barcode_no')]]['production_date']);//for unloading date
				$load_unload_hours = $roll_loading_data_arr[$batch_id][$roll_data[('barcode_no')]]['end_hours'];
				$load_unload_minutes = $roll_loading_data_arr[$batch_id][$roll_data[('barcode_no')]]['end_minutes'];
				$load_unload_id = $roll_loading_data_arr[$batch_id][$roll_data[('barcode_no')]]['load_unload_id'];
				//for slitting
				$roll_slitting_qty = $process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['sliting_qnty'];
				$roll_slitting_date = change_date_format($process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['sliting_process_end_date']);
				$roll_slitting_hours = $process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['sliting_end_hours'];
				$roll_slitting_minutes = $process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['sliting_end_minutes'];	
				//for stenter
				$roll_stentering_qty = $process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['stenter_qnty'];
				$roll_stentering_date = change_date_format($process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['stenter_process_end_date']);
				$roll_stentering_hours = $process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['stenter_end_hours'];
				$roll_stentering_minutes = $process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['stenter_end_minutes'];
				//for compacting
				$roll_compacting_qty = $process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['compct_qnty'];
				$roll_compacting_date = change_date_format($process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['compct_process_end_date']);
				$roll_compacting_hours = $process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['compct_end_hours'];
				$roll_compacting_minutes = $process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['compct_end_minutes'];	
				//for drying
				$roll_drying_qty = $process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['drying_qnty'];
				$roll_drying_date = change_date_format($process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['drying_process_end_date']);
				$roll_drying_hours = $process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['drying_end_hours'];
				$roll_drying_minutes = $process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['drying_end_minutes'];	
				//for special
				$roll_special_qty = $process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['special_qnty'];
				$roll_special_date = change_date_format($process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['special_process_end_date']);
				$roll_special_hours = $process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['special_end_hours'];
				$roll_special_minutes = $process_roll_arr_qnty[$batch_id][$roll_data[('barcode_no')]]['special_end_minutes'];

				$roll_pass_qty = $roll_qc_dtls_data_arr[$batch_id][$roll_data[('barcode_no')]]['roll_weight'];
				$roll_reject_qty =$roll_qc_dtls_data_arr[$batch_id][$roll_data[('barcode_no')]]['reject_qnty'];
				$roll_comments =$roll_qc_dtls_data_arr[$batch_id][$roll_data[('barcode_no')]]['comments'];
				$qc_date=change_date_format($roll_qc_dtls_data_arr[$batch_id][$roll_data[('barcode_no')]]['qc_date']);
				$process_loss_qty_percent=($roll_reject_qty/$roll_data[('batch_qty')])*100;
				$fin_fab_del_roll_qty = $roll_fin_fab_r_del_data_arr[$batch_id][$roll_data[('barcode_no')]]['qnty'];
				$fin_fab_del_roll_date = change_date_format($roll_fin_fab_r_del_data_arr[$batch_id][$roll_data[('barcode_no')]]['delevery_date']);
				$total_roll_del_balance_qty=$roll_pass_qty-$fin_fab_del_roll_qty;
				$rcv_roll_qnty = $inserted_roll_barcode_arr[$batch_id][$roll_data[('barcode_no')]]['qc_pass_qnty'];
				$rcv_roll_date = change_date_format($inserted_roll_barcode_arr[$batch_id][$roll_data[('barcode_no')]]['receive_date']);
				
				$grey_roll_issue_qnty = $grey_roll_issue_arr[$batch_id][$roll_data[('barcode_no')]]['qnty'];
				$grey_roll_issue_date = change_date_format($grey_roll_issue_arr[$batch_id][$roll_data[('barcode_no')]]['issue_date']);
				$aop_roll_qty = $aop_roll_arr[$batch_id][$roll_data[('barcode_no')]]['qnty']; 
				$aop_roll_date = change_date_format($aop_roll_arr[$batch_id][$roll_data[('barcode_no')]]['receive_date']);
				//Heat Setting Date and Time
				$heat_hour=''; $heat_minute=''; 
				if ($roll_heat_setting_hours != '' && $roll_heat_setting_minutes != '')
				{
					$heat_hour=str_pad($roll_heat_setting_hours,2,'0',STR_PAD_LEFT);
					$heat_minute=str_pad($roll_heat_setting_minutes,2,'0',STR_PAD_LEFT);
				}
				$heat_data_time = 'Production Date = '.$roll_heat_setting_date.' & Time = '.$heat_hour.' : '.$heat_minute;
				//Slitting Date and Time
				$slitting_hour=''; $slitting_minute=''; 
				if ($roll_slitting_hours != '' && $roll_slitting_minutes != '')
				{
					$slitting_hour=str_pad($roll_slitting_hours,2,'0',STR_PAD_LEFT);
					$slitting_minute=str_pad($roll_slitting_minutes,2,'0',STR_PAD_LEFT);
				}
				$slitting_data_time = 'Production Date = '.$roll_slitting_date.' & Time = '.$slitting_hour.' : '.$slitting_minute;
				//Stentering Date and Time
				$stentering_hour=''; $stentering_minute=''; 
				if ($roll_stentering_hours != '' && $roll_stentering_minutes != '')
				{
					$stentering_hour=str_pad($roll_stentering_hours,2,'0',STR_PAD_LEFT);
					$stentering_minute=str_pad($roll_stentering_minutes,2,'0',STR_PAD_LEFT);
				}
				$stentering_data_time = 'Production Date = '.$roll_stentering_date.' & Time = '.$stentering_hour.' : '.$stentering_minute;
				//Compacting Date and Time
				$compacting_hour=''; $compacting_minute=''; 
				if ($roll_compacting_hours != '' && $roll_compacting_minutes != '')
				{
					$compacting_hour=str_pad($roll_compacting_hours,2,'0',STR_PAD_LEFT);
					$compacting_minute=str_pad($roll_compacting_minutes,2,'0',STR_PAD_LEFT);
				}
				$compacting_data_time = 'Production Date = '.$roll_compacting_date.' & Time = '.$compacting_hour.' : '.$compacting_minute;
				//Drying Date and Time
				$drying_hour=''; $drying_minute=''; 
				if ($roll_drying_hours != '' && $roll_drying_minutes != '')
				{
					$drying_hour=str_pad($roll_drying_hours,2,'0',STR_PAD_LEFT);
					$drying_minute=str_pad($roll_drying_minutes,2,'0',STR_PAD_LEFT);
				}
				$drying_data_time = 'Production Date = '.$roll_drying_date.' & Time = '.$drying_hour.' : '.$drying_minute;
				//Special Date and Time
				$special_hour=''; $special_minute=''; 
				if ($roll_special_hours != '' && $roll_special_minutes != '')
				{
					$special_hour=str_pad($roll_special_hours,2,'0',STR_PAD_LEFT);
					$special_minute=str_pad($roll_special_minutes,2,'0',STR_PAD_LEFT);
				}
				$special_data_time = 'Production Date = '.$roll_special_date.' & Time = '.$special_hour.' : '.$special_minute;
				//Load and Unload Date and Time
				$loading_date_time = ''; $unloading_date_time= ''; 
				if($load_deying_qty && $load_unload_id == 1 )
				{
					$loading_date_time = 'Loading Date = '.$load_date.' & Time = '.$load_unload_hours.' : '.$load_unload_minutes;
				}
				elseif($unload_deying_qty && $load_unload_id == 2 ){
					$unloading_date_time = 'Unloading Date = '.$unload_date.' & Time = '.$load_unload_hours.' : '.$load_unload_minutes;
				}
			
				?>
				
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trsales_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trsales_<? echo $j; ?>"> 
					<th width="40"><? echo $j; ?></th>
					<td width="80" align="center"><p><? echo $job; ?></p></td>
					<td width="110" align="center"><p><? echo $buyer_name; ?></p></td>
					<td width="110" align="center" style="word-break:break-all;"><p><? echo $roll_data[('booking_no')]; ?></p></td>
					<td width="70" align="center" style="word-break:break-all;"><? echo $po_numbers; ?></td>
					<td width="150" align="center" style="word-break:break-all;"><p><? echo $fabrics_type;?></p></td>
					<td width="50" align="center" style="word-break:break-all;"><? echo  $fab_gsm;?></td>
					<td width="90" align="center" style="word-break:break-all;"><? echo $color_library[$roll_data[('color_id')]]; ?></td>
					<td width="60" align="center" style="word-break:break-all;"><p><? echo $dia_type_data; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($roll_data[('batch_date')]); ?></td>
					<td width="120" align="center" title="Batch ID=<? echo $batch_id;?>"><? echo $roll_data[('batch_no')]; ?></td>
					<td width="80" align="center"><p><? echo $batch_against[$roll_data[('batch_against')]]; ?></p></td>
					<td width="80" align="center"><p><?  echo $batch_qnty; ?></p></td>
					<td width="150" align="center"><? echo $roll_data[('barcode_no')]; ?></td>
					<td width="70" align="right" title="<? echo  ($heat_data_time) ? $heat_data_time : ''; ?>"><p><? echo number_format($heat_qty,2);  ?></p></td>
					<td width="70" align="right"><p><? echo number_format($roll_data[('batch_qty')],2); ?></p></td>
					<td width="80" align="right" title="<? echo ($loading_date_time) ? $loading_date_time : ''; ?>"><p><? echo number_format($load_deying_qty,2); ?></p></td>
					<td width="70" align="right" title="<? echo ($unloading_date_time) ? $unloading_date_time : ''; ?>"><p><? echo number_format($unload_deying_qty,2); ?></p></td>
					<td width="60" align="right" title="<? echo  ($slitting_data_time) ? $slitting_data_time : ''; ?>"><p><? echo number_format($roll_slitting_qty,2); ?></p></td>
					<td width="80" align="right" title="<? echo  ($stentering_data_time) ? $stentering_data_time : ''; ?>"><p><? echo number_format($roll_stentering_qty,2); ?></p></td>
					<td width="80" align="right" title="<? echo  ($compacting_data_time) ? $compacting_data_time : ''; ?>"><p><? echo number_format($roll_compacting_qty,2); ?></p></td>
					<td width="80" align="right" title="<? echo  ($drying_data_time) ? $drying_data_time : ''; ?>"><p><? echo number_format($roll_drying_qty,2); ?></p></td>
					<td width="80" align="right" title="<? echo  ($special_data_time) ? $special_data_time : ''; ?>"><p><? echo number_format($roll_special_qty,2); ?></p></td>
					<td width="80" align="right" title="<? echo  ($grey_roll_issue_date) ? 'Issue Date = '.$grey_roll_issue_date : ''; ?>"><p><? echo number_format($grey_roll_issue_qnty,2); ?><p></td>
					<td width="80" align="right" title="<? echo  ($aop_roll_date) ? 'Receive Date = '.$aop_roll_date : ''; ?>"><p><? echo number_format($aop_roll_qty,2); ?><p></td>
					<td width="80" align="right" title="<? echo  ($qc_date) ? 'QC Date = '.$qc_date : ''; ?>"><p><? echo number_format($roll_pass_qty,2); ?><p></th>
					<td width="60" align="right" title="<? echo  ($qc_date) ? 'QC Date = '.$qc_date : ''; ?>"><p><? echo number_format($roll_reject_qty,2); ?><p></th>
					<td width="100" align="right"><p><? echo number_format($process_loss_qty_percent,2); ?></p></td>
					<td width="100" align="right" title="<? echo  ($fin_fab_del_roll_date) ? 'Delivery Date = '.$fin_fab_del_roll_date : ''; ?>"><p><? echo number_format($fin_fab_del_roll_qty,2); ?></p></td>
					<td width="80" align="right"><p><? echo number_format($total_roll_del_balance_qty,2); ?></p></td>
					<td width="100" align="right" title="<? echo  ($rcv_roll_date) ? 'Receive Date = '.$rcv_roll_date : ''; ?>"><p><? echo number_format($rcv_roll_qnty,2); ?></p></td>
					<td width=""><p><?  echo substr($roll_comments,0,30) ; ?></p></td>
				</tr>

				<?
				$total_heat_qty+=$heat_qty;
				$total_roll_qty+=$roll_data[('batch_qty')];
				$total_load_deying_qty+=$load_deying_qty;
				$total_unload_deying_qty+=$unload_deying_qty;
				$total_roll_slitting_qty+=$roll_slitting_qty;
				$total_roll_stentering_qty+=$roll_stentering_qty;
				$total_roll_compacting_qty+=$roll_compacting_qty;
				$total_roll_drying_qty+=$roll_drying_qty;
				$total_roll_special_qty+=$roll_special_qty;
				$total_grey_roll_issue_qnty+=$grey_roll_issue_qnty;
				$total_aop_roll_qty+=$$aop_roll_qty;
				$total_roll_pass_qty+=$roll_pass_qty;
				$total_roll_reject_qty+=$roll_reject_qty;
				$total_pro_loss_qty_percent+=$process_loss_qty_percent;
				$total_fin_fab_del_roll_qty+=$fin_fab_del_roll_qty;
				$total_to_roll_del_bal_qty+=$total_roll_del_balance_qty;
				$total_rcv_roll_qnty+=$rcv_roll_qnty;
				$j++;
			}
				
			}	

			?>		
			
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2780" class="rpt_table" id="report_table_footer">
			<tfoot>
				<th width="40"></th>
				<th width="80"></th>
				<th width="110"></th>
				<th width="110"></th>
				<th width="70"> </th>
				<th width="150"> </th>
				<th width="50"></th>
				<th width="90"> </th>
				<th width="60"></th>
				<th width="80"></th>
				<th width="120"></th>
				<th width="80"></th>
				<th width="80"></th>
				<th width="150">Total:</th>
				<th width="70" id="total_heat_qty"><? echo number_format($total_heat_qty,2); ?></th>
				<th width="70" id="total_roll_qty"><? echo number_format($total_roll_qty,2); ?></th>
				<th width="80" id="total_load_deying_qty"><? echo number_format($total_load_deying_qty,2); ?></th>
				<th width="70" id="total_unload_deying_qty"><? echo number_format($total_unload_deying_qty,2); ?></th>
				<th width="60" id="total_roll_slitting_qty"><? echo number_format($total_roll_slitting_qty,2); ?></th>
				<th width="80" id="total_roll_stentering_qty"><? echo number_format($total_roll_stentering_qty,2); ?></th>
				<th width="80" id="total_roll_compacting_qty"><? echo number_format($total_roll_compacting_qty,2); ?></th>
				<th width="80" id="total_roll_drying_qty"><? echo number_format($total_roll_drying_qty,2); ?></th>
				<th width="80" id="total_roll_special_qty"><? echo number_format($total_roll_special_qty,2); ?></th>
				<th width="80" id="total_grey_issue_qnty"><? echo number_format($total_grey_roll_issue_qnty,2); ?></th>
				<th width="80" id="total_aop_roll_qty"><? echo number_format($total_aop_roll_qty,2); ?></th>
				<th width="80" id="total_roll_pass_qty"><? echo number_format($total_roll_pass_qty,2); ?></th>
				<th width="60" id="total_roll_reject_qty"><? echo number_format($total_roll_reject_qty,2); ?></th>
				<th width="100" id="total_pro_loss_qty_percent"><? echo number_format($total_pro_loss_qty_percent,2); ?></th>
				<th width="100" id="total_fin_fab_del_roll_qty"><? echo number_format($total_fin_fab_del_roll_qty,2); ?></th>
				<th width="80" id="total_to_roll_del_bal_qty"><? echo number_format($total_to_roll_del_bal_qty,2); ?></th>
				<th width="100" id="total_rcv_roll_qnty"><? echo number_format($total_rcv_roll_qnty,2); ?></th>
				<th width=""></th>			
			</tfoot>
			</table>
			</div>
			<?
		} //Roll Wise End
		?>

		<?

		if($report_type==2 ) //Batch Wise
		{
			?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2630" class="rpt_table" id="table_header_1">
				<caption> <b style=" float:left"><h3>Batch wise Roll Status Report ( batch wise)</h3></b></caption>
				<thead>
					<th width="40">SL</th>
					<th width="80">Job No</th>
					<th width="110">Buyer</th>
					<th width="110">F.Booking No</th>
					<th width="70">Order No</th>
					<th width="150">Fabrics Type</th>
					<th width="50">GSM</th>
					<th width="90">Color Name</th>
					<th width="60"><p>Dia/Width Type</p></th>
					<th width="80"><? if($cbo_search_date==1){ echo "Batch Date";}else{echo "Dyeing Date";} ?></th>
					<th width="80">Batch No</th>
					<th width="80">Batch Qty.</th>
					<th width="80">Batch Against</th>
					<th width="70"><p>No Of Roll </p></th>
					<th width="70"><p>HeatSetting / Singeing</p></th>
					<th width="80">Dyeing Loding</th>
					<th width="70"><p>Dyeing Un-Loding</p></th>
					<th width="60"><p>Slitting / Squeezing</p></th>
					<th width="80">Stentering</th>
					<th width="80">Compacting</th>
					<th width="80">Drying</th>
					<th width="80">Special Finish</th>
					<th width="80"><p>Fin.Fab.Prod. No of roll<p></th>
					<th width="80"><p>Fin.Fab.Prod. QC Passed Qty <p></th>
					<th width="80"><p>Process Loss Qty<p></th>
					<th width="60"><p>Process Loss %<p></th>
					<th width="100"><p>Fin.Fab.Delivery to Store No of Roll</p></th>
					<th width="100"><p>Fin.Fab.Delivery to Store Qty</p></th>
					<th width="80"><p>Delivery Balance Qty.</p></th>
					<th width="100">Receive by Store</th>
					<th width="">Remarks</th>					
				</thead>
			</table>
			<div style="width:2650px; overflow-y:scroll; max-height:450px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2630" class="rpt_table" id="table_body2">
			<?
			$i=1;
			foreach($batch_wise_arr as $batch_id=>$row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$batch_id=$row[('id')];
				$po_id=rtrim($row[('po_id')],',');
				
				$fabric_desc=array();
				if(rtrim($row[('fabric_type')],','))
				{
					$fabric_type=rtrim($row[('fabric_type')],',');
					$fabric_desc=array_unique(explode(",",$fabric_type));
				}		
				
				$fab='';
				foreach($fabric_desc as $pid)
				{
				// $fabdesc_type=explode(",",$desc);
					$fab_desc=$prod_detail_arr[$pid];	
					if($fab=='')
					{
						$fabdesc_type=explode(",",$fab_desc);
						$fab=$fabdesc_type[0].",".$fabdesc_type[1];	
						$fab_gsm=$prod_detail_gsm_arr[$pid];	
					}
					else
					{
						$fabdesc_type=explode(",",$fab_desc);
						$fab.="<br>".$fabdesc_type[0].",".$fabdesc_type[1];
						$fab_gsm.=", ".$prod_detail_gsm_arr[$pid];
					}
				}

				$item_desc=rtrim($row[('item_description')],',');
				$item_fabric_desc=implode(",",array_unique(explode(",",$item_desc)));

				$fabrics_type ='';
				if($fab)
				{
					$fabrics_type= $fab;
				}
				else if($item_fabric_desc)
				{
					$fabrics_type= $item_fabric_desc;
				}
				// var_dump($barcode_no);die;
				// $barcode_no=array_unique(explode(",",$barcode_no));
				//var_dump($po_id);die;
				$po_id=array_unique(explode(",",$po_id));
				$po_numbers=""; $job_no=""; $buyer="";
				foreach($po_id as $id)
				{
					if($row[('entry_form')]==36) //SubCon
					{
						if($po_numbers=="") $po_numbers=$sub_job_array[$id]['po']; else $po_numbers.=",".$sub_job_array[$id]['po'];
						if($job_no=="") $job_no=$sub_job_array[$id]['job']; else $job_no.=",".$sub_job_array[$id]['job'];
						if($buyer=="") $buyer=$buyer_library[$sub_job_array[$id]['buyer']]; else $buyer.=",".$buyer_library[$sub_job_array[$id]['buyer']];
					}
					else
					{						
						if($po_numbers=="") $po_numbers=$job_array[$id]['po']; else $po_numbers.=",".$job_array[$id]['po'];
						if($job_no=="") $job_no=$job_array[$id]['job']; else $job_no.=",".$job_array[$id]['job'];
						if($buyer=="") $buyer=$buyer_library[$job_array[$id]['buyer']]; else $buyer.=",".$buyer_library[$job_array[$id]['buyer']];
						if($file=="") $file=$job_array[$id]['file']; else $file.=",".$job_array[$id]['file']; // file
						if($refNo=="") $refNo=$job_array[$id]['refNo']; else $refNo.=",".$job_array[$id]['refNo']; // ref
						if($buyer_style=="") $buyer_style=$job_array[$id]['style_no']; else $buyer_style.=','.$job_array[$id]['style_no'];
					}				
				
				}
				if($row[('buyer')])
				{
					$buyer=$row[('buyer')];
				}
				if($buyer==""){$buyer = $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']];}

				$job=implode(',',array_unique(explode(",",$job_no)));
				$po_numbers=implode(', ',array_unique(explode(",",$po_numbers)));
				$buyer_name=implode(',',array_unique(explode(",",$buyer)));

				$dia_type='';  $width_dia_type=rtrim($row[('width_dia_type')],',');
				$dia_type_id=array_unique(explode(",",$width_dia_type));
				foreach($dia_type_id as $dia_id)
				{	
				if($dia_type=="") $dia_type=$fabric_typee[$dia_id]; else $dia_type.=",".$fabric_typee[$dia_id];
				}
				$dia_type_data=implode(',',array_unique(explode(",",$dia_type)));

				// $heat_qty=$heat_setting_arr[$batch_id]['qty'];
				
				$load_deying_qty=$loading_data_arr[$batch_id]['load'];
				$unload_deying_qty=$loading_data_arr[$batch_id]['unload'];

				$slitting_qty=$process_arr_qnty[$batch_id]['sliting_qnty'];
				$drying_qty=$process_arr_qnty[$batch_id]['drying_qnty'];
				$heat_qty=$process_arr_qnty[$batch_id]['heat_qnty'];
				$compacting_qty=$process_arr_qnty[$batch_id]['compct_qnty'];
				$special_qty=$process_arr_qnty[$batch_id]['special_qnty'];
				$stentering_qty=$process_arr_qnty[$batch_id]['stenter_qnty'];

				//$stentering_qty=$stentering_arr[$batch_id]['stentering'];
				//$compacting_qty=$compacting_arr[$batch_id]['compact'];
				// $drying_qty=$drying_arr[$batch_id]['drying'];
				//$special_qty=$special_arr[$batch_id]['special'];
				$no_of_roll = $sql_qc_dtls_data_arr[$batch_id]['no_of_roll'];
				$passQty = $sql_qc_dtls_data_arr[$batch_id]['roll_weight'];
				$process_loss_qty = $sql_qc_dtls_data_arr[$batch_id]['reject_qnty'];
				$process_loss_qty_percent=($process_loss_qty/$row[('batch_qty')])*100;
				$fin_fab_roll_qty = $sql_fin_fab_r_del_data_arr[$batch_id]['qnty'];
				$fin_fab_n_roll = $sql_fin_fab_r_del_data_arr[$batch_id]['no_of_roll'];
				$total_delivery_balance_qty=$passQty-$fin_fab_roll_qty;
				$rcv_qnty = $inserted_roll_arr[$batch_id]['qc_pass_qnty'];

				?>
			
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trsales_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trsales_<? echo $i; ?>"> 
					<td width="40"><? echo $i; ?></td>
					<td width="80"><p><? echo $job; ?></p></td>
					<td width="110"><p><? echo $buyer_name; ?></p></td>
					<td width="110" style="word-break:break-all;"><p><? echo $row[('booking_no')]; ?></p></td>
					<td width="70" style="word-break:break-all;"><? echo $po_numbers; ?></td>
					<td width="150" style="word-break:break-all;"><p><? echo $fabrics_type;?></p></td>
					<td width="50" style="word-break:break-all;"><? echo  $fab_gsm;?></td>
					<td width="90" style="word-break:break-all;"><? echo $color_library[$row[('color_id')]]; ?></td>
					<td width="60" style="word-break:break-all;"><p><? echo $dia_type_data; ?></p></td>
					<td width="80"><? echo change_date_format($row[('batch_date')]); ?></td>
					<td width="80" title="Batch ID=<? echo $batch_id;?>"><p><? echo $row[('batch_no')]; ?></p></td>
					<td width="80" ><p><?  echo number_format($row[('batch_qty')],2); ?> </p></td>
					<td width="80"><p><? echo $batch_against[$row[('batch_against')]]; ?></p></td>
					<td width="70" align="center" onClick="fnc_batchlDtls('<?=$batch_id; ?>','batchDtls_popup');"><p><a href="javascript:void(0)" style="text-decoration: none;" ><? echo number_format($row[('num_of_rows')],2); ?> </a></p></td>
					<td width="70" align="right" onClick="fnc_heatDtls('<?=$batch_id; ?>','heatDtls_popup');"><p><a href="javascript:void(0)" style="text-decoration: none;"><? echo number_format($heat_qty,2);  ?></a></p></td>
					<td width="80" align="right" onClick="fnc_loadingDtls('<?=$batch_id; ?>','loadingDtls_popup');"><p><a href="javascript:void(0)" style="text-decoration: none;"><? echo number_format($load_deying_qty,2); ?></a></p></td>
					<td width="70" align="right" onClick="fnc_unloadingDtls('<?=$batch_id; ?>','unloadingDtls_popup');"><p><a href="javascript:void(0)" style="text-decoration: none;"><? echo number_format($unload_deying_qty,2); ?></a></p></td>
					<td width="60" align="right" onClick="fnc_slittingDtls('<?=$batch_id; ?>','slittingDtls_popup');"><p><a href="javascript:void(0)" style="text-decoration: none;"><? echo number_format($slitting_qty,2); ?></a></p></td>
					<td width="80" align="right" onClick="fnc_stenteringDtls('<?=$batch_id; ?>','stenteringDtls_popup');"><p><a href="javascript:void(0)" style="text-decoration: none;"> <? echo number_format($stentering_qty,2); ?></a></p></td>
					<td width="80" align="right" onClick="fnc_compactingDtls('<?=$batch_id; ?>','compactingDtls_popup');"><p> <a href="javascript:void(0)" style="text-decoration: none;"><? echo number_format($compacting_qty,2); ?></a></p></td>
					<td width="80" align="right" onClick="fnc_dryingDtls('<?=$batch_id; ?>','dryingDtls_popup');"><p> <a href="javascript:void(0)" style="text-decoration: none;"><? echo number_format($drying_qty,2); ?></a></p></td>
					<td width="80" align="right" onClick="fnc_SpecFinDtls('<?=$batch_id; ?>','SpecialFinDtls_popup');"><p> <a href="javascript:void(0)" style="text-decoration: none;"><? echo number_format($special_qty,2,'.',''); ?></a></p></td>
					<td width="80" align="right" onClick="fnc_qcrollDtls('<?=$batch_id; ?>','qcrollDtls_popup');"><p><a href="javascript:void(0)" style="text-decoration: none;"><? echo number_format($no_of_roll,2); ?></a><p></th>
					<td width="80" align="right" onClick="fnc_QcPassDtls('<?=$batch_id; ?>','qcPassDtls_popup');"><p><a href="javascript:void(0)" style="text-decoration: none;"><? echo number_format($passQty,2,'.',''); ?> </a><p></th>
					<td width="80" align="right"><p><? echo number_format($process_loss_qty,2,'.',''); ?><p></th>
					<td width="60" align="right"><p><? echo number_format($process_loss_qty_percent,2,'.',''); ?><p></th>
					<td width="100" align="right"><p><? echo number_format($fin_fab_n_roll,2); ?></p></td>
					<td width="100" align="right"><p><? echo number_format($fin_fab_roll_qty,2,'.',''); ?></p></td>
					<td width="80" align="right"><p><? echo number_format($total_delivery_balance_qty,2); ?></p></td>
					<td width="100" align="right"><p><? echo number_format($rcv_qnty,2); ?></p></td>
					<td width="">&nbsp;</td>
				</tr>
				<?
				$total_heat_qty+=$heat_qty;
				$total_load_deying_qty+=$load_deying_qty;
				$total_unload_deying_qty+=$unload_deying_qty;
				$total_slitting_qty+=$slitting_qty;
				$total_stentering_qty+=$stentering_qty;
				$total_compacting_qty+=$compacting_qty;
				$total_drying_qty+=$drying_qty;
				$total_special_qty+=$special_qty;
				$total_no_of_roll+=$no_of_roll;
				$total_passQty+=$passQty;
				$total_process_loss_qty+=$process_loss_qty;
				$total_process_loss_qty_percent+=$process_loss_qty_percent;
				$total_fin_fab_n_roll+=$fin_fab_n_roll;
				$total_fin_fab_roll_qty+=$fin_fab_roll_qty;
				$total_total_delivery_balance_qty+=$total_delivery_balance_qty;
				$total_rcv_qnty+=$rcv_qnty;
				$i++;
			}
			?>
			
			</table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2630" class="rpt_table" id="report_table_footer">
			<tfoot>

				<th width="40"></th>
				<th width="80"></th>
				<th width="110"></th>
				<th width="110"></th>
				<th width="70"> </th>
				<th width="150"> </th>
				<th width="50"></th>
				<th width="90"> </th>
				<th width="60"></th>
				<th width="80"></th>
				<th width="80"></th>
				<th width="80"></th>
				<th width="80"></th>
				<th width="70">Total</th>
				<th width="70" id="total_heat_qty"><? echo number_format($total_heat_qty,2); ?></th>
				<th width="80" id="total_load_deying_qty"><? echo number_format($total_load_deying_qty,2); ?></th>
				<th width="70" id="total_unload_deying_qty"><? echo number_format($total_unload_deying_qty,2); ?></th>
				<th width="60" id="total_slitting_qty"><? echo number_format($total_slitting_qty,2); ?></th>
				<th width="80" id="total_stentering_qty"><? echo number_format($total_stentering_qty,2); ?></th>
				<th width="80" id="total_compacting_qty"><? echo number_format($total_compacting_qty,2); ?></th>
				<th width="80" id="total_drying_qty"><? echo number_format($total_drying_qty,2); ?></th>
				<th width="80" id="total_special_qty"><? echo number_format($total_special_qty,2); ?></th>
				<th width="80" id="total_no_of_roll"><? echo number_format($total_no_of_roll,2); ?></th>
				<th width="80" id="total_passQty"><? echo number_format($total_passQty,2); ?></th>
				<th width="80" id="total_process_loss_qty"><? echo number_format($total_process_loss_qty,2); ?></th>
				<th width="60" id="total_loss_qty_percent"><? echo number_format($total_process_loss_qty_percent,2); ?></th>
				<th width="100" id="total_fin_fab_n_roll"><? echo number_format($total_fin_fab_n_roll,2); ?></th>
				<th width="100" id="total_fin_fab_roll_qty"><? echo number_format($total_fin_fab_roll_qty,2); ?></th>
				<th width="80" id="total_balance_qty"><? echo number_format($total_total_delivery_balance_qty,2); ?></th>
				<th width="100" id="total_rcv_qnty"><? echo number_format($total_rcv_qnty,2); ?></th>
				<th width=""></th>
			
			</tfoot>
			</table>
			</div>
			<?			

			} //Batch Wise End
			?>
	</fieldset>
	<?
	echo "<br />Execution Time: " . (microtime(true) - $started) . "S";
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
	echo "####$filename####$report_type";
	exit();				
}

?>