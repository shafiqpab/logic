<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

 //--------------------------------------------------------------------------------------------
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 142, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/cut_and_lay_entry_variable_wise_controller', this.value, 'load_drop_down_floor', 'floor_td' )" );
	exit();
}

/* if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_name", 135, "select id,floor_name from lib_prod_floor where production_process=1 and status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
} */
if ($action=="load_drop_down_floor")
{

	echo create_drop_down( "cbo_floor_name", 135, "select id,floor_name from lib_prod_floor where production_process=1 and status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );

	/* echo create_drop_down( "cbo_floor_name", 135, "select id,floor_name from lib_prod_floor where production_process=1 and status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected,"load_drop_down( 'requires/cut_and_lay_entry_variable_wise_controller', document.getElementById('cbo_working_company_name').value+'_'+$data+'_'+this.value, 'load_drop_down_table', 'table_td' )" ); */
}
if ($action=="load_drop_down_table")
{
	$explode_data = explode("_",$data);
	$company_id = $explode_data[0];
	$location_id = $explode_data[1];
	$floor_id = $explode_data[2];
	$sql = "SELECT id,table_no FROM LIB_CUTTING_TABLE WHERE is_deleted = 0 and status_active=1 and company_id='$company_id' and location_id='$location_id' and floor_id='$floor_id' and table_no is not null order by table_no";
	// echo $sql; die;

	echo create_drop_down( "txt_table_no", 135,$sql,"id,table_no", 1, "-- Select Table --", $selected, "",0 );
}

if($db_type==0){ $insert_year="SUBSTRING_INDEX(b.insert_date, '-', 1)";}
else if($db_type==2){ $insert_year="extract(year from b.insert_date)";}

if ($action=="load_drop_down_buyer")
{
	$data=explode("**",$data);
	$sql="select distinct c.id,c.buyer_name from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where a.job_no_mst=b.job_no and b.company_name=".$data[2]."  and job_no_prefix_num='".$data[0]."' and $insert_year='".$data[1]."' and b.buyer_name=c.id and a.status_active=1";
	$result=sql_select($sql);
	foreach($result as $val)
	{
		$buyer_value=$val[csf('buyer_name')];
	}
	echo create_drop_down( "txt_buyer_name", 140,$sql, "id,buyer_name", 0, "select Buyer" ,$buyer_value);
	exit();
}

if ($action=="load_drop_down_order_garment")
{
	$ex_data = explode("_",$data);
	$gmt_item_arr=return_library_array( "select gmts_item_id from wo_po_details_master where job_no='".$ex_data[0]."' and status_active=1",'id','gmts_item_id');
    $gmt_item_id=implode(",",$gmt_item_arr);
	if(count($gmt_item_arr)==1)
	{
		echo create_drop_down( "cbogmtsitem_$ex_data[1]", 120, $garments_item,"", 1, "-- Select Item --", $gmt_item_id, "","",$gmt_item_id);
	}
    else if(count($gmt_item_arr)>1)
	{
		echo create_drop_down( "cbogmtsitem_$ex_data[1]", 120, $garments_item,"", 1, "-- Select Item --", $selected, "","",$gmt_item_id);
	}
	else if(count($gmt_item_arr)==0)
	{
		echo create_drop_down( "cbogmtsitem_$ex_data[1]", 120, $blank_array,"", 1, "-- Select Item --", $selected, "","");
	}
	exit();
}

if ($action=="load_drop_down_color")
{
	$ex_data = explode("_",$data);
	// print_r($ex_data);
	$color_item_arr=return_library_array( "SELECT a.id,a.color_name from lib_color a,wo_po_color_size_breakdown b,wo_po_break_down c  where  a.id=b.color_number_id  and c.id =b.po_break_down_id and b.job_no_mst='".$ex_data[0]."'  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and b.shiping_status<>3 and c.shiping_status<>3 and c.is_deleted=0  group by a.id,a.color_name","id","color_name");
	echo create_drop_down( "cbocolor_$ex_data[1]", 100, $color_item_arr,"", 1, "select color",'', "reset_fld($ex_data[1])");
	exit();
}
if ($action=="load_drop_down_fab_color")
{
	$ex_data = explode("_",$data);
	//  print_r($ex_data);
	$contrast_color_arr=return_library_array("SELECT a.id,a.color_name from lib_color a, wo_pre_cos_fab_co_color_dtls b WHERE a.id=b.contrast_color_id and job_no='".$ex_data[0]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'id','color_name');
	// echo $sql="SELECT a.id,a.color_name from lib_color a, wo_pre_cos_fab_co_color_dtls b WHERE a.id=b.contrast_color_id and job_no='".$ex_data[0]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	echo create_drop_down( "cbocontrastcolor_$ex_data[1]", 100, $contrast_color_arr,"", 1, "select color",'', "reset_fld($ex_data[1])");
	exit();
}

if ($action=="load_drop_down_batch")
{
	$ex_data = explode("_",$data);
	$batch_array=array();
	$sql="select a.id, a.batch_no, a.extention_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.color_id='".$ex_data[1]."' and b.po_id in(".$ex_data[0].") and b.status_active=1 and b.is_deleted=0 and a.entry_form in(0,7,37,66,68) group by a.id, a.batch_no, a.extention_no";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$batch_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}

	$sql="select a.id, a.batch_no, a.extention_no from pro_batch_create_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.batch_id and b.id=c.dtls_id and c.color_id='".$ex_data[1]."' and c.po_breakdown_id in(".$ex_data[0].") and b.status_active=1 and b.is_deleted=0 and c.entry_form in(14,15) and c.trans_type=5 group by a.id, a.batch_no, a.extention_no";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$batch_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}
	if(count($batch_array)>0)
	{
		echo create_drop_down( "cbobatch_$ex_data[2]", 100, $batch_array,"", 1, "select Batch",$selected, "batch_match(this.id,this.value)");
	}
	else
	{
		echo create_drop_down( "cbobatch_$ex_data[2]", 100, $blank_array,"", 1, "select Batch",$selected, "batch_match(this.id,this.value)");
	}
	exit();
}

if ($action=="load_drop_down_order_qty")
{
	$ex_data = explode("_",$data);

	 $sql="SELECT sum(CAST(plan_cut_qnty as INT)) as plan_qty from  wo_po_color_size_breakdown where po_break_down_id in(".$ex_data[0].") and item_number_id=".$ex_data[1]." and color_number_id=".$ex_data[2]." and status_active=1 and is_deleted=0";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		echo "document.getElementById('txtorderqty_$ex_data[3]').value  = '".($row[csf("plan_qty")])."';\n";
		$plan_qty=$row[csf("plan_qty")];
	}

	$sql_marker="select sum(b.marker_qty) as mark_qty from  ppl_cut_lay_dtls a, ppl_cut_lay_size b where a.id=b.dtls_id and b.order_id in(".$ex_data[0].") and a.gmt_item_id=".$ex_data[1]." and a.color_id=".$ex_data[2]." and a.status_active=1 and b.status_active=1 and b.is_deleted=0";
	$result=sql_select($sql_marker);
	foreach($result as $rows)
	{
		echo "document.getElementById('txttotallay_$ex_data[3]').value  = '".$rows[csf("mark_qty")]."';\n";
		$marker_qty=$rows[csf("mark_qty")];
	}
	$lay_balance=$plan_qty-$marker_qty;
	echo "document.getElementById('txtlaybalanceqty_$ex_data[3]').value  = $lay_balance\n";

	exit();
}

if ($action=="tna_date_status")
{
	$ex_data = explode("**",$data);
	$cut_start_date=$ex_data[0];
	$cut_end_date=$ex_data[1];
	$order_all=$ex_data[2];
	//echo $cut_start_date;die;
	//**********************************Tna Date*********************************************************************************************
	for($sl=1; $sl<=$row_num; $sl++)
	{
		$cbo_order_id="cboorderno_".$sl;
		if($tna_order!="") $tna_order.=",".$$cbo_order_id;
		else $tna_order.=$$cbo_order_id;
	}
	$tna_variable=return_field_value("tna_integrated","variable_order_tracking"," company_name=$ex_data[3] AND variable_list=14");
	if($tna_variable==1)
	{
		$min_tna_date=return_field_value(" min(a.task_start_date) as min_start_date","tna_process_mst a, lib_tna_task b"," b.task_name=a.task_number and po_number_id in ($order_all) and task_name=84","min_start_date");
	  	$max_tna_date=return_field_value("max(a.task_finish_date) as max_end_date ","tna_process_mst a, lib_tna_task b"," b.task_name=a.task_number and po_number_id in ($order_all) and task_name=84","max_end_date");

	 	$order_number_arr=return_library_array( "select id, po_number from wo_po_break_down where id in ($order_all)",'id','po_number');

	//  $min_start_date=date("Y-m_d",strtotime($min_start_date));
	  $max_end_date=date("Y-m_d",strtotime($max_tna_date));
	  $cut_start_date=date("Y-m_d",strtotime($cut_start_date));
	  $cut_end_date=date("Y-m_d",strtotime($cut_end_date));
	  if($cut_end_date>$max_end_date)
	  {
		 $sql_tna_date=sql_select("select a.po_number_id, a.task_start_date,a.task_finish_date	from  tna_process_mst a, lib_tna_task b where b.task_name=a.task_number and po_number_id in ($order_all) and task_name=84");
		 if(count($sql_tna_date)>0)
			 {
				 foreach($sql_tna_date as $row)
				 {
					 if($poNumber=="")
					 {
						$poNumber=$order_number_arr[$row[csf('po_number_id')]];
						$po_st_date=$row[csf('task_start_date')];
						$po_en_date=$row[csf('task_finish_date')];
						$po_end_date=date("d-m-Y",strtotime($po_en_date));
						$po_start_date=date("d-m-Y",strtotime($po_st_date));
					 }
					 else
					 {
						$poNumber=$poNumber."**".$order_number_arr[$row[csf('po_number_id')]];
						$po_st_date=$row[csf('task_start_date')];
						$po_en_date=$row[csf('task_finish_date')];
						$po_start_date=$po_start_date."**".date("d-m-Y",strtotime($po_st_date));
						$po_end_date=$po_end_date."**".date("d-m-Y",strtotime($po_en_date));
					 }
				 }
			  $min_start_date=date("d-m-Y",strtotime($min_tna_date));
			  $max_end_date=date("d-m-Y",strtotime($max_tna_date));
			  echo "0##".$poNumber."##".$po_start_date."##".$po_end_date."##".$min_start_date."##".$max_end_date;die;
		 }
		  else echo 1;die;
	  }
	echo 1;die;
	}
	else echo 2;die;

		//***********************************End Tna date*******************************************************************************************
}
if($action=="table_popup")
{
	echo load_html_head_contents("Body Part Info","../../../", 1, 1, $unicode); 
	$data_all=$data;
	$data=explode("***",$data);
	extract($_REQUEST);   
	?>
      <script>  
		function js_set_value( id,tableName )
		{
			 
			parent.window.document.getElementById('txt_table_no').value=tableName;
			parent.window.document.getElementById('table_entry_id').value=id;
			fn_onClosed();
		}

		function fn_onClosed()
		{ 
		 parent.emailwindow.hide();
		}
		</script>
    <?
	 
    $sql="SELECT id,table_name from lib_table_entry where company_name=$companyID and location_name=$location and floor_name=$floor and is_deleted = 0 and status_active=1 and table_name is not null order by table_sequence";
	// echo $sql;die;
	$res = sql_select($sql);
	// pre($res );  
	?>
	<table cellspacing="0" width="200"  border="1" rules="all" class="rpt_table" >
		<thead>
			<th width="30">Sl</th>
			<th width="170">Table Name</th> 
		</thead>
    </table>
	<div style="width:220px; max-height:200px; overflow-y:scroll" id="scroll_body" >
		<table cellspacing="0" width="200"  border="1" rules="all" class="rpt_table" id="tbl_list_search" >	
		<?
			$i=1;
			foreach($res as $v)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				$set_val =  "'".$v['ID']."','".$v['TABLE_NAME']."'"; 
				?>

				<tr bgcolor="<?= $bgcolor ; ?>" id="tr_<?= $i; ?>"  style="cursor:pointer;">
					<td  onClick="js_set_value(<?=$set_val; ?>)" width="30"><?= $i;?></td>
					<td  id="td_<?= $i; ?>" onClick="js_set_value(<?=$set_val; ?>)" width="170">
					<?= $v['TABLE_NAME'];?>
					</td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<table width="200">
		<tr align="center">
			<td> 
				<div align="center">
					<input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" />
				</div>
			</td>
		</tr>
	</table>
	<script>
		let old_rows = '<?= $selected_rows ?>'
		setFilterGrid("tbl_list_search",-1);
	</script>
	<?
 exit();

}

if($action=="check_batch_no")
{
	$data=explode("**",$data);
	$added_barcode_no=$data[2];
	$scanned_barcode_arr=array();
	$barcodeData=sql_select( "select barcode_no from pro_roll_details where entry_form=509 and status_active=1 and is_deleted=0");

	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}

	if($added_barcode_no!='') 	$added_barcode_cond=" and c.barcode_no not in (".$added_barcode_no.")";
					else 						$added_barcode_cond="";
	//print_r($scanned_barcode_arr);
	$sql="select c.barcode_no from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in (64,37,72) and a.batch_no='".trim($data[0])."' and b.po_id ".str_replace("'","",trim($data[1]))."' and a.is_deleted=0 and   a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $added_barcode_cond ";

	$data_array=sql_select($sql);
	$barcode_arr=array();
	if(count($data_array)>0)
	{
		foreach($data_array as $val)
		{
			if($scanned_barcode_arr[$val[csf('barcode_no')]]=='')
			{
				$barcode_arr[$val[csf('barcode_no')]]=$val[csf('barcode_no')];
			}
		}
		//$barcode_arr=json_encode($barcode_arr);
		//echo $barcode_arr;
		echo trim(implode(",",$barcode_arr));
		//print_r($barcode_arr);die;
	}
	else
	{
		echo "0";
	}
	exit();
}

if($action=="roll_popup")
{
  	echo load_html_head_contents("Plies Info Roll Wise","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	//$order_no=str_replace("'","",$order_no);
	//echo $order_no;die;
	//$roll_maintained=1;

	?>
	<script>

		var roll_maintained=<? echo $roll_maintained; ?>;
		var rollData='<? echo $rollData; ?>';
		var scanned_barcode=new Array(); var roll_details_array=new Array(); var barcode_array=new Array();
		<?
			$scanned_barcode_array=array();
			$scanned_barcode_data=sql_select("select barcode_no from pro_roll_details where entry_form=93 and status_active=1 and is_deleted=0");
			foreach($scanned_barcode_data as $row)
			{
				$scanned_barcode_array[]=$row[csf('barcode_no')];
			}
			$jsscanned_barcode_array= json_encode($scanned_barcode_array);
			echo "scanned_barcode = ". $jsscanned_barcode_array . ";\n";


			$data_array=sql_select("SELECT c.barcode_no, c.roll_id, c.roll_no, c.qnty from pro_grey_batch_dtls b, pro_roll_details c where b.id=c.dtls_id and c.entry_form=72 and c.po_breakdown_id in(".str_replace("'","",$order_no).") and b.color_id=$color and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
			$roll_details_array=array(); $barcode_array=array();
			foreach($data_array as $row)
			{
				$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
				$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
				$roll_details_array[$row[csf("barcode_no")]]['qnty']=$row[csf("qnty")];
				$barcode_array[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
			}
			$data_array=sql_select("SELECT a.batch_no,c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.item_description from  pro_roll_details c, pro_batch_create_dtls b,pro_batch_create_mst a where a.id=b.mst_id and b.id=c.dtls_id and  c.entry_form in (64,37) and c.po_breakdown_id in(".str_replace("'","",$order_no).") and c.status_active=1 and c.is_deleted=0"); //and b.color_id=$color
 			$roll_details_array=array(); $barcode_array=array();
			foreach($data_array as $row)
			{
				$item_description_arr=explode(",",$row[csf('item_description')]);
				$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
				$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
				$roll_details_array[$row[csf("barcode_no")]]['qnty']=$row[csf("qnty")];
				$roll_details_array[$row[csf("barcode_no")]]['batch_no']=$row[csf("batch_no")];
				$roll_details_array[$row[csf("barcode_no")]]['gsm']=$item_description_arr[2];
				$barcode_array[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
			}






			$jsroll_details_array= json_encode($roll_details_array);
			echo "var roll_details_array = ". $jsroll_details_array . ";\n";

			$jsbarcode_array= json_encode($barcode_array);
			echo "var barcode_array = ". $jsbarcode_array . ";\n";
		?>


		function openmypage_batch()
		{

			var row_num=$('#txt_tot_row').val();
			var added_barcode_no='';
			for(var k=1; k<=row_num; k++)
			{
				if($('#barcodeNo_'+k).val()!="" && typeof ($('#barcodeNo_'+k).val()) !== 'undefined')
				{
					if(added_barcode_no!="") added_barcode_no=added_barcode_no+","+$('#barcodeNo_'+k).val();
					else added_barcode_no=$('#barcodeNo_'+k).val();
				}
			}

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','cut_and_lay_entry_variable_wise_controller.php?order_no='+<? echo $order_no; ?>+'&color='+<? echo $color; ?>+'&added_barcode_no='+added_barcode_no+'&action=batch_popup','Batch Barcode Popup', 'width=580px,height=300px,center=1,resize=1,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos

				if(barcode_nos!="")
				{
					var barcode_upd=barcode_nos.split(",");
					var row_num=$('#txt_tot_row').val();
					for(var k=0; k<barcode_upd.length; k++)
					{
						if($('#barcodeNo_'+row_num).val()!="")
						{
							add_break_down_tr(row_num);
							row_num++;
						}

						var bar_code=barcode_upd[k];
						load_data(row_num, bar_code);
					}
				}
			}
		}

		$(document).ready(function(e) {
            if(roll_maintained==1)
			{
				$('#barcode_div').show();
				$('#batch_div').show();
			}
			else
			{
				$('#barcode_div').hide();

			}

			if(rollData!="")
			{
				var data=rollData.split("**");
				for(var k=0; k<data.length; k++)
				{
					var datas=data[k].split("=");
					var barcode_no=datas[0];
					var rollNo=datas[1];
					var rollId=datas[2];
					var rollWgt=datas[3];
					var plies=datas[4];
					var batchNo=datas[5];
					var shade=datas[6];

					var row_num=$('#txt_tot_row').val();
					if($('#barcodeNo_'+row_num).val()!="")
					{
						add_break_down_tr(row_num);
						row_num++;
					}

					$("#barcodeNo_"+row_num).val(barcode_no);
					$("#rollNo_"+row_num).val(rollNo);
					$("#rollId_"+row_num).val(rollId);
					$("#rollWgt_"+row_num).val(rollWgt);
					$("#plies_"+row_num).val(plies);
					$("#batchNo_"+row_num).val(batchNo);
					$("#txtshade_"+row_num).val(shade);

					if( jQuery.inArray( barcode_no, scanned_barcode )>-1)
					{
						scanned_barcode.push(barcode_no);
					}
				}
			}
        });

		function add_break_down_tr( i )
		{
			//var row_num=$('#tbl_list_search tbody tr').length;
			var row_num=$('#txt_tot_row').val();
			row_num++;

			var clone= $("#tr_"+i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});

			clone.find("input,select").each(function(){

			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
			  'name': function(_, name) { return name },
			  'value': function(_, value) { return '' }
			});

			}).end();

			$("#tr_"+i).after(clone);

			//$('#rollNo_'+row_num).removeAttr("onBlur").attr("onBlur","roll_duplication_check("+row_num+");");

			$('#increase_'+row_num).removeAttr("value").attr("value","+");
			$('#decrease_'+row_num).removeAttr("value").attr("value","-");
			$('#increase_'+row_num).removeAttr("onclick").attr("onclick","add_break_down_tr("+row_num+");");
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			set_all_onclick();
			$('#txt_tot_row').val(row_num);
		}

		function fn_deleteRow(rowNo)
		{
			var numRow = $('#tbl_list_search tbody tr').length;
			if(numRow!=1)
			{

				var bar_code=$('#barcodeNo_'+rowNo).val();
				var index = scanned_barcode.indexOf(bar_code);
				scanned_barcode.splice(index,1);
				$("#tr_"+rowNo).remove();
			}
		}

		function roll_duplication_check(row_id)
		{
			var row_num=$('#tbl_list_search tr').length;
			var roll_no=$('#rollNo_'+row_id).val();

			if(roll_no*1>0)
			{
				for(var j=1; j<=row_num; j++)
				{
					if(j==row_id)
					{
						continue;
					}
					else
					{
						var roll_no_check=$('#rollNo_'+j).val();
						if(roll_no==roll_no_check)
						{
							alert("Duplicate Roll No.");
							$('#rollNo_'+row_id).val('');
							return;
						}
					}
				}
			}
		}


		$('#txt_batch_no').live('keydown', function(e) {
			if (e.keyCode === 13)
			{
				e.preventDefault();
				var batch_no=$('#txt_batch_no').val();
				var order_id=<?php echo $order_no; ?>;

				var row_num=$('#txt_tot_row').val();
				var added_barcode_no='';
				for(var k=1; k<=row_num; k++)
				{
					if($('#barcodeNo_'+row_num).val()!="")
					{
						if(added_barcode_no!="") added_barcode_no=added_barcode_no+","+$('#barcodeNo_'+k).val();
						else added_barcode_no=$('#barcodeNo_'+k).val();
					}
				}
				var response_data=return_global_ajax_value( batch_no+"**"+order_id+"**"+added_barcode_no, 'check_batch_no', '', 'cut_and_lay_entry_variable_wise_controller');
				//alert(response_data);return;
				//var row_num=$('#txt_tot_row').val();
				if(response_data!=0)
				{
					response_data_arr=trim(response_data).split(",");
					//alert(response_data_arr.length);return;
					for (var i = 0; i < response_data_arr.length; i++)
					{
						var bar_code=response_data_arr[i];
						//alert(bar_code);return;
						if( jQuery.inArray( bar_code, scanned_barcode )>-1)
						{
							alert('Sorry! Barcode Already Scanned.');
							//$('#txt_bar_code_num').val('');
							return;
						}

						if(barcode_array[bar_code])
						{
							if($('#barcodeNo_'+row_num).val()!="")
							{
								add_break_down_tr(row_num);
								row_num++;
							}
							load_data(row_num, bar_code);
						}
					}



				//alert(response_data)
					//response_data_arr=response_data.split(",");
					//for (var i = 0; i < response_data_arr.length; i++)
					//{

						//var bar_code=response_data_arr[i];
					//alert(bar_code)
						/*if( jQuery.inArray( bar_code, scanned_barcode )>-1)
						{
							alert('Sorry! Barcode Already Scanned.');
							$('#txt_bar_code_num').val('');
							return;
						}*/
					//}

				}
				/*if(!barcode_array[bar_code])
				{
					alert('Barcode is Not Valid');
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_bar_code_num').val('');
					return;
				}

				if( jQuery.inArray( bar_code, scanned_barcode )>-1)
				{
					alert('Sorry! Barcode Already Scanned.');
					$('#txt_bar_code_num').val('');
					return;
				}

				var row_num=$('#txt_tot_row').val();
				if($('#barcodeNo_'+row_num).val()!="")
				{
					add_break_down_tr(row_num);
					row_num++;
				}
				load_data(row_num, bar_code);*/
			}
		});



		$('#txt_bar_code_num').live('keydown', function(e) {
			if (e.keyCode === 13)
			{
				e.preventDefault();
				var bar_code=$('#txt_bar_code_num').val();

				if(!barcode_array[bar_code])
				{
					alert('Barcode is Not Valid');
					$('#messagebox_main', window.parent.document).fadeTo(100,1,function() //start fading the messagebox
					 {
						$('#messagebox_main', window.parent.document).html('Barcode is Not Valid.').removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					 });
					 $('#txt_bar_code_num').val('');
					return;
				}

				if( jQuery.inArray( bar_code, scanned_barcode )>-1)
				{
					alert('Sorry! Barcode Already Scanned.');
					$('#txt_bar_code_num').val('');
					return;
				}

				var row_num=$('#txt_tot_row').val();
				if($('#barcodeNo_'+row_num).val()!="")
				{
					add_break_down_tr(row_num);
					row_num++;
				}
				load_data(row_num, bar_code);
			}
		});

		function openmypage_barcode()
		{

			var row_num=$('#txt_tot_row').val();
			var added_barcode_no='';
			for(var k=1; k<=row_num; k++)
			{
				if($('#barcodeNo_'+row_num).val()!="")
				{
					if(added_barcode_no!="") added_barcode_no=added_barcode_no+","+$('#barcodeNo_'+k).val();
					else added_barcode_no=$('#barcodeNo_'+k).val();
				}
			}


			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','cut_and_lay_entry_variable_wise_controller.php?order_no='+<? echo $order_no; ?>+'&color='+<? echo $color; ?>+'&action=barcode_popup','Barcode Popup', 'width=480px,height=300px,center=1,resize=1,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var barcode_nos=this.contentDoc.getElementById("hidden_barcode_nos").value; //Barcode Nos

				if(barcode_nos!="")
				{
					var barcode_upd=barcode_nos.split(",");
					var row_num=$('#txt_tot_row').val();
					for(var k=0; k<barcode_upd.length; k++)
					{
						if($('#barcodeNo_'+row_num).val()!="")
						{
							add_break_down_tr(row_num);
							row_num++;
						}

						var bar_code=barcode_upd[k];
						load_data(row_num, bar_code);
					}
				}
			}
		}

		function load_data(row_num, bar_code)
		{
			if(bar_code=="") bar_code=0;
			$("#barcodeNo_"+row_num).val(bar_code);
			$("#rollNo_"+row_num).val(roll_details_array[bar_code]['roll_no']);
			$("#rollId_"+row_num).val(roll_details_array[bar_code]['roll_id']);
			$("#batchNo_"+row_num).val(roll_details_array[bar_code]['batch_no']);
			$("#rollWgt_"+row_num).val(roll_details_array[bar_code]['qnty']);
			scanned_barcode.push(bar_code);
		}

		function fnc_close()
		{
			var save_string='';	var tot_plies='';let flag=true;
			$("#tbl_list_search").find('tr').each(function()
			{
				var barcodeNo=$(this).find('input[name="barcodeNo[]"]').val();
				var rollNo=$(this).find('input[name="rollNo[]"]').val();
				var rollId=$(this).find('input[name="rollId[]"]').val();
				var rollWgt=$(this).find('input[name="rollWgt[]"]').val();
				var plies=$(this).find('input[name="plies[]"]').val();
				var batchNo=$(this).find('input[name="batchNo[]"]').val();
				var txtshade=$(this).find('input[name="txtshade[]"]').val();

				if(plies*1>0)
				{
					tot_plies=tot_plies*1+plies*1;
					if(barcodeNo=="") barcodeNo=0;
					if(save_string=="")
					{
						save_string=barcodeNo+"="+rollNo+"="+rollId+"="+rollWgt+"="+plies+"="+batchNo+"="+txtshade;
					}
					else
					{
						save_string+="**"+barcodeNo+"="+rollNo+"="+rollId+"="+rollWgt+"="+plies+"="+batchNo+"="+txtshade;
					}
				}

				if(rollWgt*1 < 1)
				{
					flag=false;
				}
			});

			$('#hide_data').val( save_string );
			$('#hide_plies').val( tot_plies );
			if(flag)
			{
				parent.emailwindow.hide();
			}
			else
			{
				alert(`Roll Weight Required.`);
			}
		}

    </script>
	</head>
	<body>
	<div align="center" style="width:100%; overflow-y:hidden;" >
		<fieldset style="width:590px">
			<div style="margin-bottom:5px; display:none; float:left" id="batch_div">
			<div style="color: red;text-align:center;font-weight:700;padding:3px 0;">If you change/update something this popup, please make sure you update <u><i>Size Ratio</i></u> popup</div>
				<strong>Batch No</strong>&nbsp;&nbsp;
				<input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:170px;" onDblClick="openmypage_batch()" placeholder="Browse/Write/scan"/>
			</div>
			<div style="margin-bottom:5px; display:none" id="barcode_div">
				<strong>Barcode Number</strong>&nbsp;&nbsp;
				<input type="text" name="txt_bar_code_num" id="txt_bar_code_num" class="text_boxes_numeric" style="width:170px;" onDblClick="openmypage_barcode()" placeholder="Browse/Write/scan"/>
			</div>
			<table width="590" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
				<thead>
					<th>Roll Number</th>
					<th>Batch No.</th>
					<th>Shade</th>
					<th>Roll Weight</th>
					<?
						$disbled="";
						if($roll_maintained==1)
						{
							echo "<th>Barcode No</th>";
							$disbled="disabled";
						}
					?>
					<th>Plies</th>
					<th></th>
				</thead>
				<tbody>
					<tr id="tr_1" class="general">
						<td>
							<input type="text" id="rollNo_1" name="rollNo[]" class="text_boxes_numeric" style="width:80px" value="" <? echo $disbled; ?> />
							<input type="hidden" id="rollId_1" name="rollId[]" value=""/><!--onBlur="roll_duplication_check(1);"-->
						</td>
						<td>
							<input type="text" id="batchNo_1" name="batchNo[]" class="text_boxes" value="" style="width:80px"/>
						</td>

						<td>
							<input type="text" id="txtshade_1" name="txtshade[]" class="text_boxes" value="" style="width:80px"/>
						</td>
						<td>
							<input type="text" id="rollWgt_1" name="rollWgt[]" class="text_boxes_numeric" value="" style="width:80px" <? echo $disbled; ?>/>
						</td>
						<? if($roll_maintained==1)
						{
						?>
							<td><input type="text" id="barcodeNo_1" name="barcodeNo[]" class="text_boxes_numeric" value="" style="width:80px" disabled/></td>
						<?
						}
						else
						{
						?>
							<td style="display:none"><input type="text" id="barcodeNo_1" name="barcodeNo[]" class="text_boxes_numeric" value="" style="width:80px" disabled/></td>
						<?
						}
						?>
						<td>
							<input type="text" id="plies_1" name="plies[]" class="text_boxes_numeric" value="" style="width:80px"/>
						</td>
						<td width="70">
							<? if($roll_maintained!=1)
							{
							?>
								<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
							<?
							}
							?>
							<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(1);" />
						</td>
					</tr>
				</tbody>
			</table>
			<div align="center" style="margin-top:10px">
				<input type="button" class="formbutton" onClick="fnc_close()" value="Close" style="width:100px"/>
				<input type="hidden" id="hide_plies" />
				<input type="hidden" id="hide_data" />
				<input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="1">
			</div>
		</fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>

		$(document).ready(function(e) {
        	setFilterGrid('tbl_list_search',-1);
        });

		var selected_id = new Array();

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str)
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#hidden_barcode_nos').val( id );
		}

		function fnc_close()
		{
			parent.emailwindow.hide();
		}

    </script>

</head>

<body>
<div align="center" style="width:450px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:440px; margin-left:2px">
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="420">
                <thead>
                    <th width="50">SL</th>
                    <th width="130">Barcode No</th>
                    <th width="100">Roll No</th>
                    <th>Roll Qty.</th>
                </thead>
            </table>
            <div style="width:420px; max-height:200px; overflow-y:scroll" id="list_container" align="left">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="400" id="tbl_list_search">
                    <?
					$scanned_barcode_arr=array();
					$barcodeData=sql_select( "select barcode_no from pro_roll_details where entry_form=93 and status_active=1 and is_deleted=0");
					foreach ($barcodeData as $row)
					{
						$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
					}
					if($added_barcode_no!='') 	$added_barcode_cond=" and c.barcode_no not in (".$added_barcode_no.")";
					else 						$added_barcode_cond="";

                    $i=1;
                    $data_array=sql_select("select c.barcode_no, c.roll_id, c.roll_no, c.qnty from pro_grey_batch_dtls b, pro_roll_details c where b.id=c.dtls_id and c.entry_form=72 and c.po_breakdown_id in($order_no) and b.color_id=$color and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $added_barcode_cond");
					foreach($data_array as $row)
                    {
                        if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
                                <td width="50">
                                    <? echo $i; ?>
                                     <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                                </td>
                                <td width="130"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                                <td width="100"><? echo $row[csf('roll_no')]; ?></td>
                                <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                            </tr>
						<?
							$i++;
						}
                    }
                    ?>
				</table>
            </div>
            <table width="420">
                <tr>
                    <td align="center" >
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_barcode_search_list_view")
{
	$data = explode("_",$data);

	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];

	if($company_id==0) { echo "Please Select Company First."; die; }

	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and d.po_number like '$search_string'";
	}

	$scanned_barcode_arr=array();
	$barcodeData=sql_select( "select barcode_num from pro_grey_prod_delivery_dtls where entry_form=56 and status_active=1 and is_deleted=0");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_num')]]=$row[csf('barcode_num')];
	}

	$sql="SELECT a.recv_number, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond";
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="120">System Id</th>
            <th width="110">Job No</th>
            <th width="110">Order No</th>
            <th width="80">Shipment Date</th>
            <th width="100">Barcode No</th>
            <th width="60">Roll No</th>
            <th>Roll Qty.</th>
        </thead>
	</table>
	<div style="width:740px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">
        <?
            $i=1;
            foreach ($result as $row)
            {
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="40">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
						<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="60"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
				<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="720">
        <tr>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?
exit();
}

if($action=="roll_maintained")
{
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and variable_list=3 and item_category_id=51 and is_deleted=0 and status_active=1");
	if($roll_maintained==1) $roll_maintained=$roll_maintained; else $roll_maintained=0;

	echo "document.getElementById('roll_maintained').value 	= '".$roll_maintained."';\n";


	$print_report_format_arr=return_library_array( "select template_name, format_id from lib_report_template where  module_id=4 and report_id=118 and is_deleted=0 and status_active=1 and template_name=$data", "template_name", "format_id");
	$report_id=explode(",",$print_report_format_arr[$data]);

	//print_r($report_id);
	foreach($report_id as $res){

		if($res==857 )
		{
			echo "$('#btn_cost_print').show();";

		}
		elseif($res==858 )
		{
			echo "$('#btn_cost_print2').show();";
		}




	}



}

$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');

if($action=="size_popup")
{
  	echo load_html_head_contents("Cut and bundle details","../../../", 1, 1, '','1','');
	extract($_REQUEST);
   	$country_is_blank_sql=sql_select("SELECT country_id FROM wo_po_color_size_breakdown where status_active=1 AND is_deleted=0 AND (country_id is null or country_id ='' or country_id=0) AND  po_break_down_id IN(".str_replace("'","",$order_id).")");
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_id."' and module_id=4 and report_id=118 and is_deleted=0 and status_active=1");
	$rmg_no_creation=return_field_value("smv_source","variable_settings_production","company_name=$cbo_company_id and variable_list=39 and is_deleted=0 and status_active=1");
 	if($rmg_no_creation=="") $rmg_no_creation=2; else $rmg_no_creation=$rmg_no_creation;

	?>
	<script>

		var permission='<? echo $permission; ?>';
		var without_country='<? echo count($country_is_blank_sql); ?>';
		var report_ids='<?=$print_report_format; ?>';
		let rmg_no_creation='<? echo $rmg_no_creation;?>';

		function js_set_value( data)
	   	{
			var data=data.split("_");
			document.getElementById('hidden_batch_no').value=data[0];
			document.getElementById('hidden_batch_id').value=data[1];
			parent.emailwindow.hide();
	   	}

		function check_sizef_qty(value1,value2,id)
		{
			var x=id.split('_');
			var prev_qty=$("#txt_sizef_prev_qty_"+x[3]).val()*1;
			var value=(value1*1)*(value2*1);
			var lay_value=$("#txt_layf_balance_"+x[3]).val()*1;
			// alert(value1+'__'+value2+'__'+prev_qty+'__'+lay_value);
			if(value>(lay_value*1+prev_qty*1))
			{
				alert("Marker qty is geater than Lay Balance");
				$("#txt_sizef_qty_"+x[3]).css({"background-color":"red"});
			}
			else
			{
				$("#txt_sizef_qty_"+x[3]).css({"background-color":"white"});
			}
			$("#txt_sizef_qty_"+x[3]).val(value);

			var size_id=$("#hidden_sizef_id_"+x[3]).val();

			distribute_qnty(size_id, value2);
			distribute_qnty_bl_wise(size_id, value);
			calculate_size_wise_total();
			total_size_qty();
		}

		function distribute_qnty(size_id, size_ratio)
		{
			var row_num=$("#tbl_roll tbody tr").length;
			for(var i=1; i<=row_num; i++)
			{
				var plies=$("#piles_"+i).val()*1;
				var qty=size_ratio*plies;

				$("#sqty_"+size_id+"_"+i).val(qty);
			}
		}

		function distribute_qnty_bl_wise(size_id, size_qty)
		{
			var row_num=$("#tbl_size_details tbody tr").length;
			for(var i=1; i<=row_num; i++)
			{
				var lay_balance=$("#txt_lay_balance_"+i).val()*1;
				var curr_size_id=$("#hidden_size_id_"+i).val();

				if(size_id==curr_size_id)
				{
					$("#txt_excess_"+i).val('');

					if(size_qty*1>0 && lay_balance*1>0)
					{
						var bl_size_qty=size_qty-lay_balance;
						if(bl_size_qty>0)
						{
							$("#txt_size_qty_"+i).val(lay_balance);
							size_qty=bl_size_qty;
						}
						else
						{
							$("#txt_size_qty_"+i).val(size_qty);
							break;
						}
					}
					else
					{
						$("#txt_size_qty_"+i).val('');
					}
				}
			}
		}

		function calculate_total()
		{
			var row_num=$("#tbl_size tbody tr").length;
			var ratio_total=0; var qty_total=0; var distributed_total=0;
			for(var i=1; i<=row_num; i++)
			{
				ratio_total=ratio_total+$("#txt_sizef_ratio_"+i).val()*1;
				qty_total=qty_total+$("#txt_sizef_qty_"+i).val()*1;
				distributed_total=distributed_total+$("#txt_distributed_qty_"+i).val()*1;
			}

			$('#total_sizef_ratio').text(ratio_total);
			$('#total_sizef_qty').text(qty_total);
			$('#total_distributed_qty').text(distributed_total);
		}

		function calculate_size_wise_total()
		{
			var size_arr=[];
			var row_num=$("#tbl_size_details tbody tr").length;
			for(var i=1; i<=row_num; i++)
			{
				var size_id=$("#hidden_size_id_"+i).val();
				var size_qty=$("#txt_size_qty_"+i).val();
				if(size_arr[size_id] == undefined) size_arr[size_id] = 0;
				size_arr[size_id]+= size_qty*1;
			}

			var row_num=$("#tbl_size tbody tr").length;
			for(var i=1; i<=row_num; i++)
			{
				var size_id=$("#hidden_sizef_id_"+i).val();
				$('#txt_distributed_qty_'+i).val(size_arr[size_id]);
			}
			calculate_total();
		}

	 	function total_size_qty()
		{
			var row_num=$("#tbl_size_details tbody tr").length;
			var tot_qty=0;
			for(var i=1; i<=row_num; i++)
			{
				tot_qty+=($("#txt_size_qty_"+i).val()!='')?$("#txt_size_qty_"+i).val()*1:0;
			}
			$('#total_size_qty').text(tot_qty);
		}

		function check_size_qty(i)
		{
			var curr_size_qty=$("#txt_size_qty_"+i).val()*1;
			var curr_size_id=$("#hidden_size_id_"+i).val();
			var tot_sizeQty='';

			var row_num=$("#tbl_size_details tbody tr").length;
			for(var j=1; j<=row_num; j++)
			{
				var size_id=$("#hidden_size_id_"+j).val();
				var size_qty=$("#txt_size_qty_"+j).val();
				if(size_id == curr_size_id)
				{
					tot_sizeQty=tot_sizeQty*1+size_qty*1;
				}
			}

			var row_num=$("#tbl_size tbody tr").length;
			var sizef_qty=0;
			for(var j=1; j<=row_num; j++)
			{
				var size_id=$("#hidden_sizef_id_"+j).val();
				if(size_id == curr_size_id)
				{
					sizef_qty=$("#txt_sizef_qty_"+j).val();
				}
			}

			if(tot_sizeQty>sizef_qty)
			{
				alert("Marker Qty Exceeds Distributed Qty.");
				$("#txt_size_qty_"+i).val('');
				$("#txt_excess_"+i).val('');
			}
			calculate_size_wise_total();
			total_size_qty();
		}

		function copy_perc(i)
		{
			var value=$('#txt_excess_'+i).val();
			var curr_size_id=$("#hidden_size_id_"+i).val();

			if($('#checkbox').is(':checked'))
			{
				var row_num=$("#tbl_size tbody tr").length;
				var sizef_qty=0;
				for(var j=1; j<=row_num; j++)
				{
					var size_id=$("#hidden_sizef_id_"+j).val();
					if(size_id == curr_size_id)
					{
						sizef_qty=$("#txt_sizef_qty_"+j).val();
					}
				}

				var tot_sizeQty=0;
				for(var j=1; j<i; j++)
				{
					var size_id=$("#hidden_size_id_"+j).val();
					var size_qty=$("#txt_size_qty_"+j).val();
					if(size_id == curr_size_id)
					{
						tot_sizeQty=tot_sizeQty*1+size_qty*1;
					}
				}

				var rowCount=$('#tbl_size_details tbody tr').length;
				for(var j=i; j<=rowCount; j++)
				{
					var size_id=$("#hidden_size_id_"+j).val();
					var bl_qty=$('#txt_lay_balance_'+j).val()*1;

					if(bl_qty>0 && size_id == curr_size_id)
					{
						document.getElementById('txt_excess_'+j).value=value;
						var excess_qty=Math.round(bl_qty*1+(value/100)*bl_qty);
						tot_sizeQty=tot_sizeQty*1+excess_qty*1;

						if(tot_sizeQty>sizef_qty)
						{
							alert("Marker Qty Exceeds Distributed Qty.");
							$("#txt_size_qty_"+j).val('');
							$("#txt_excess_"+j).val('');
							$("#txt_excess_"+j).focus();
							break;
						}
						$('#txt_size_qty_'+j).val(Math.abs(excess_qty));
					}
				}
				calculate_size_wise_total();
				total_size_qty();
			}
			else
			{
				calculate_excess_qty(i)
			}
		}

		function calculate_excess_qty(i)
		{
			var bl_qty=$('#txt_lay_balance_'+i).val()*1;
			var excess_perc=$('#txt_excess_'+i).val()*1;
			if(bl_qty>0)
			{
				var excess_qty=Math.round(bl_qty*1+(excess_perc/100)*bl_qty);
				$('#txt_size_qty_'+i).val(Math.abs(excess_qty));
				check_size_qty(i);
			}
		}

		function calculate_perc(i)
		{
			var bl_qty=$('#txt_lay_balance_'+i).val()*1;
			var size_qty=$('#txt_size_qty_'+i).val()*1;
			var excess_qty=size_qty-bl_qty;
			if(excess_qty>0)
			{
				if(bl_qty==0)
				{
					$('#txt_excess_'+i).val(0);
				}
				else
				{
					var excess_perc=(excess_qty/bl_qty)*100;
					$('#txt_excess_'+i).val(excess_perc.toFixed(2));
				}
			}
			else
			{
				$('#txt_excess_'+i).val('');
			}
		}

		function fnc_cut_lay_size_info( operation )
		{
			const btn = (operation==0) ? document.getElementById('save1') : document.getElementById('update1');
			btn.disabled=true;
			if(operation==2)
			{
				alert("Delete Restricted.");
				const btn = (operation==0) ? document.getElementById('save1') : document.getElementById('update1');
				btn.disabled=false;
				return;
			}
			if(form_validation('txt_bundle_pcs','Pcs Per Bundle')==false)
			{
				const btn = (operation==0) ? document.getElementById('save1') : document.getElementById('update1');
				btn.disabled=false;
				return;
			}
			if(trim(without_country)>0)
			{
				alert("Delivery Country Blank In Color Size Page");
				const btn = (operation==0) ? document.getElementById('save1') : document.getElementById('update1');
				btn.disabled=false;
				return;
			}


			var order_id='<? echo $order_id; ?>';
			var gmt_id=<? echo $cbo_gmt_id; ?>;
			var color_id=<? echo $cbo_color_id; ?>;
			var mst_id=<? echo $mst_id; ?>;
			var dtls_id=<? echo $details_id; ?>;
			var cbo_company_id=<? echo $cbo_company_id; ?>;
			var color_type_id=<? echo $cbo_color_type; ?>;


			var bundle_per_pcs=$("#txt_bundle_pcs").val();
			var to_marker_qty=$("#total_sizef_qty").text()*1;
			var job_id=$("#hidden_update_job_id").val();
			var cut_no=$("#hidden_update_cut_no").val();
			var txt_plies=$("#txt_search_common").val();
			var txt_bundle_pcs=$("#txt_bundle_pcs").val();
			var total_distributed_qty=$("#total_distributed_qty").text()*1;
			if(to_marker_qty<=0)
			{
				alert("Please Insert Size Qty.");
				const btn = (operation==0) ? document.getElementById('save1') : document.getElementById('update1');
				btn.disabled=false;
				return;
			}

			if(to_marker_qty!=total_distributed_qty)
			{
				alert("Total Size Qty. and Total Distributed Qty. Should be same.");
				const btn = (operation==0) ? document.getElementById('save1') : document.getElementById('update1');
				btn.disabled=false;
				return;
			}
			//alert(to_marker_qty+"**"+total_distributed_qty);return;
			//var roll_data=$("#roll_data").val();

			var row_num=$('#tbl_size_details tbody tr').length;
			var data1="action=save_update_delete_size&operation="+operation+"&row_num="+row_num+"&color_id="+color_id+"&mst_id="+mst_id+"&dtls_id="+dtls_id+"&bundle_per_pcs="+bundle_per_pcs+"&to_marker_qty="+to_marker_qty+"&cbo_company_id="+cbo_company_id+"&job_id="+job_id+"&cut_no="+cut_no+"&order_id="+order_id+"&gmt_id="+gmt_id+"&txt_plies="+txt_plies+"&txt_bundle_pcs="+txt_bundle_pcs+"&color_type_id="+color_type_id;
			 var data2=''; var size_data=''; var max_seq=0; var size_arr=[]; var roll_data='';

			var size_row_num=$('#tbl_size tbody tr').length;
			for(var k=1; k<=size_row_num; k++)
			{
				var seq=$("#txt_bundle_"+k).val()*1;
				if(seq>max_seq) max_seq=seq;
				// size_data+=get_submitted_data_string('txt_layf_balance_'+k+'*txt_sizef_ratio_'+k+'*txt_sizef_qty_'+k+'*hidden_sizef_id_'+k+'*txt_bundle_'+k,"../../../",k);
				size_data+='&txt_layf_balance_'+k+'='+$('#txt_layf_balance_'+k).val()+'&txt_sizef_ratio_'+k+'='+$('#txt_sizef_ratio_'+k).val()+'&txt_sizef_qty_'+k+'='+$('#txt_sizef_qty_'+k).val()+'&hidden_sizef_id_'+k+'='+$('#hidden_sizef_id_'+k).val()+'&txt_bundle_'+k+'='+$('#txt_bundle_'+k).val();

				var size_id=$("#hidden_sizef_id_"+k).val();
				//size_arr[]=size_id;
				size_arr.push(size_id);
			}

			var roll_row_num=$("#tbl_roll tbody tr").length;
			for(var i=1; i<=roll_row_num; i++)
			{
				var barcode_no=0;
				var roll_no=$("#rollNo_"+i).val()*1;
				var roll_id=$("#rollId_"+i).val()*1;
				var roll_wgt=$("#rollWgt_"+i).val()*1;
				var plies=$("#piles_"+i).val()*1;

				if(roll_data=="")
				{
					roll_data=barcode_no+"="+roll_no+"="+roll_id+"="+roll_wgt+"="+plies;
				}
				else
				{
					roll_data+="|"+barcode_no+"="+roll_no+"="+roll_id+"="+roll_wgt+"="+plies;
				}

				for(var z=0; z<size_arr.length; z++)
				{
					var size_id=size_arr[z];
					var qty=$("#sqty_"+size_id+"_"+i).val();
					roll_data+="="+qty
				}
			}

			size_data=size_data+"&size_row_num="+size_row_num+"&max_seq="+max_seq+"&roll_data="+roll_data;

			for(var k=1; k<=row_num; k++)
			{
				// data2+=get_submitted_data_string('cboCountryType_'+k+'*cboCountry_'+k+'*txt_lay_balance_'+k+'*txt_excess_'+k+'*txt_size_qty_'+k+'*hidden_size_id_'+k+'*update_size_id_'+k+'*poId_'+k,"../../../",2);
				data2+='&cboCountryType_'+k+'='+$('#cboCountryType_'+k).val()+'&cboCountry_'+k+'='+$('#cboCountry_'+k).val()+'&txt_lay_balance_'+k+'='+$('#txt_lay_balance_'+k).val()+'&txt_excess_'+k+'='+$('#txt_excess_'+k).val()+'&txt_size_qty_'+k+'='+$('#txt_size_qty_'+k).val()+'&hidden_size_id_'+k+'='+$('#hidden_size_id_'+k).val()+'&update_size_id_'+k+'='+$('#update_size_id_'+k).val()+'&poId_'+k+'='+$('#poId_'+k).val();
			}
			var data=data1+data2+size_data;
			//alert(size_data);return;
			freeze_window(operation);
			http.open("POST","cut_and_lay_entry_variable_wise_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_cut_lay_size_info_reponse;
		}

		function fnc_cut_lay_size_info_reponse()
		{
			if(http.readyState == 4)
			{
				//release_freezing(); return;
				//alert(http.responseText);
				var reponse=trim(http.responseText).split('**');
			    if(reponse[0]==0 || reponse[0]==1)
				 {
					 if(reponse[0]==0)
					 {
						 $('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
						 {
							$('#msg_box_popp').html("Data Save Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
						 });
					 }
					 else if(reponse[0]==1)
					 {
						 $('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
						 {
							$('#msg_box_popp').html("Data Update Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
						 });
					 }

					show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3]+'**'+reponse[7],'show_bundle_list_view','search_div','cut_and_lay_entry_variable_wise_controller','setFilterGrid("list_view",-1)');
					var update_size_id=reponse[3].split('_');
					$("#hidden_plant_qty").val(reponse[4]);
					$("#hidden_total_marker").val(reponse[5]);
					$("#hidden_lay_balance").val(reponse[6]);

					if(reponse[7]==1)
					{
						var update_data=reponse[3].split(',');
						var dtlsId_array = new Array();
						for(var k=0; k<update_data.length; k++)
						{
							var datas=update_data[k].split("__");
							var index=datas[1];
							dtlsId_array[index] = datas[0]+"**"+datas[2];
						}

						var row_num=$('#tbl_size tbody tr').length;
						for(var i=1;i<=row_num;i++)
						{
							var index=	$("#hidden_size_id_"+i).val();
							var dtls_id=''; var sequence_no='';
							if(dtlsId_array[index])
							{
								var datas=dtlsId_array[index].split("**");
								dtls_id=datas[0];
								sequence_no=datas[1];
							}
							$('#update_size_id_'+i).val(dtls_id);
							$('#txt_bundle_'+i).val(sequence_no);
						}
					}
					else
					{
						for(var i=1;i<=update_size_id.length;i++)
						{
							$('#update_size_id_'+i).val(update_size_id[i-1]);
						}
					}
					set_button_status(1, permission, 'fnc_cut_lay_size_info',1,1);
				}

				else if(reponse[0]==15)
				{
					alert("No Data Found");
				}
				else if(reponse[0]==200)
				{
					alert("Update Restricted. This information found in Cutting Qc Page Which System Id "+reponse[3]+".");
				}
				else if(reponse[0]==201)
				{
					alert("Save Restricted. This information found in Cutting Qc Page Which System Id "+reponse[3]+".");
				}
				const btn = (reponse[0]==0) ? document.getElementById('save1') : document.getElementById('update1');
				btn.disabled=false;
				release_freezing();
			}
		}

		function sequence_duplication_check(row_id)
		{
			var row_num=$('#tbl_size_details tbody tr').length;
			var sequence_no=$('#txt_bundle_'+row_id).val();

			if(sequence_no*1>0)
			{
				for(var j=1; j<=row_num; j++)
				{
					if(j==row_id)
					{
						continue;
					}
					else
					{
						var sequence_no_check=$('#txt_bundle_'+j).val();

						if(sequence_no==sequence_no_check)
						{
							alert("Duplicate Sequence No.");
							$('#txt_bundle_'+row_id).val('');
							return;
						}
					}
				}
			}
		}

		function clear_size_form()
		{
			$("#txt_bundle_pcs").val('');
			var row_num=$('#tbl_size_details tbody tr').length;
			for(var i=1;i<=row_num;i++)
			{
				$('#txt_size_qty_'+i).val('');
				$('#txt_bundle_'+i).val('');
			}
		}

		function size_popup_close(id,marker,plan,tomarker,lay_balance)
		{
			var pass_string=id+"**"+marker+"**"+plan+"**"+tomarker+"**"+lay_balance;

			document.getElementById('hidden_marker_no_x').value=pass_string;
		  	parent.emailwindow.hide();
		}

		function check_all_report()
		{
			$("input[name=chkbundle]").each(function(index, element) {

				if( $('#check_all').prop('checked')==true)
					$(this).attr('checked','true');
				else
					$(this).removeAttr('checked');
			});
		}

		function fnc_bundle_report(column_list)
		{
			var data="";
			var error=1;
			$("input[name=chkbundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{

					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[1] ).val(); else data=data+","+$('#hiddenid_'+idd[1] ).val();
				}
			});

			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id; ?>;

			if(column_list==6)
			{
				var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_entry_variable_wise_controller");
				window.open(url,"##");
			}
			else
			{
				var url=return_ajax_request_value(data, "print_report_bundle_barcode_eight", "cut_and_lay_entry_variable_wise_controller");
				window.open(url,"##");
			}

		}


		function fnc_bundle_report_eight()
		{
			var data="";
			var error=1;
			$("input[name=chkbundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[1] ).val(); else data=data+","+$('#hiddenid_'+idd[1] ).val();
				}
			});

			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id; ?>;
			data2="***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id; ?>;

			var title = 'Search Job No';
			var page_link = 'cut_and_lay_entry_variable_wise_controller.php?data='+data2+'&action=print_report_bundle_barcode_eight';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				data=data+'***'+prodID;
				var url=return_ajax_request_value_post(data, "print_barcode_eight", "cut_and_lay_entry_variable_wise_controller");
				window.open(url,"##");
			}

			//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_entry_variable_wise_controller");
			//window.open(url,"##");
		}


		function fnc_bundle_report_nine()
		{
			var data="";
			var error=1;
			$("input[name=chkbundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[1] ).val(); else data=data+","+$('#hiddenid_'+idd[1] ).val();
				}
			});

			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id; ?>;
			data2="***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id; ?>;

			var title = 'Search Job No';
			var page_link = 'cut_and_lay_entry_variable_wise_controller.php?data='+data2+'&action=print_report_bundle_barcode_nine';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
			emailwindow.onclose=function()
			 {
				var theform=this.contentDoc.forms[0];
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				data=data+'***'+prodID;
				var url=return_ajax_request_value_post(data, "print_barcode_nine", "cut_and_lay_entry_variable_wise_controller");
				window.open(url,"##");
			 }

			//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_entry_variable_wise_controller");
			//window.open(url,"##");
		}

		function fnc_bundle_report_one()
		{
			var data="";
			var error=1;
			$("input[name=chkbundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[1] ).val(); else data=data+","+$('#hiddenid_'+idd[1] ).val();
				}
			});

			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id; ?>;

			var title = 'Search Job No';
			var page_link = 'cut_and_lay_entry_variable_wise_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
			emailwindow.onclose=function()
			 {
				var theform=this.contentDoc.forms[0];
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				data=data+'***'+prodID;
				//var url=return_ajax_request_value(data, "print_barcode_one_pdf", "cut_and_lay_entry_variable_wise_controller");
				//window.open(url,"##");
				window.open("cut_and_lay_entry_variable_wise_controller.php?data=" + data+'&action=print_barcode_one', true );
			 }

			//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_entry_variable_wise_controller");
			//window.open(url,"##");
		}

		//fnc_bundle_report_one_urmi

		function fnc_bundle_report_one_urmi()
		{
			var data="";
			var error=1;

			$("input[name=chkbundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[1] ).val(); else data=data+","+$('#hiddenid_'+idd[1] ).val();
				}
			});
			// alert(data);return;
			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			var order_id='<? echo $order_id; ?>';
			var chk_status= $("#check_all").prop('checked');
			/*if( chk_status==true)
			{
				data=420;
			}*/

			data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+order_id;

			var title = 'Search Job No ';
			var page_link = 'cut_and_lay_entry_variable_wise_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
			emailwindow.onclose=function()
			 {
				var theform=this.contentDoc.forms[0];
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				data=data+'***'+prodID;
				var url=return_ajax_request_value(data, "print_barcode_one_urmi", "cut_and_lay_entry_variable_wise_controller");
				window.open(url,"##");
				//window.open("cut_and_lay_entry_variable_wise_controller.php?data=" + data+'&action=print_barcode_one_urmi', true );
			 }

			//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_entry_variable_wise_controller");
			//window.open(url,"##");
		}

		function fnc_bundle_report_ten_urmi()
		{
			var data="";
			var error=1;

			$("input[name=chkbundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[1] ).val(); else data=data+","+$('#hiddenid_'+idd[1] ).val();
				}
			});
			// alert(data);return;
			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			var order_id='<? echo $order_id; ?>';
			var chk_status= $("#check_all").prop('checked');
			/*if( chk_status==true)
			{
				data=420;
			}*/

			data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+order_id;

			var title = 'Search Job No';
			var page_link = 'cut_and_lay_entry_variable_wise_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
			emailwindow.onclose=function()
			 {
				var theform=this.contentDoc.forms[0];
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				data=data+'***'+prodID;
				var url=return_ajax_request_value(data, "print_barcode_ten_urmi", "cut_and_lay_entry_variable_wise_controller");
				window.open(url,"##");
				//window.open("cut_and_lay_entry_variable_wise_controller.php?data=" + data+'&action=print_barcode_one_urmi', true );
			 }

			//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_entry_variable_wise_controller");
			//window.open(url,"##");
		}

		function fnc_bundle_report_ten_youth()
		{
			var data="";
			var error=1;

			$("input[name=chkbundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					error=0;
					var idd=$(this).attr('id').split("_");
					//alert(idd);
					if(data=="") data=$('#hiddenid_'+idd[1] ).val(); else data=data+","+$('#hiddenid_'+idd[1] ).val();
					//if(data=="") data=$(this).find('input[name="hiddenid[]"]').val(); else data=data+","+$(this).find('input[name="hiddenid[]"]').val();
				}
			});
			//alert(data);return;
			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			var order_id='<? echo $order_id; ?>';
			var chk_status= $("#check_all").prop('checked');
			/*if( chk_status==true)
			{
				data=420;
			}*/

			data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+order_id;

			var title = 'Search Job No';
			var page_link = 'cut_and_lay_entry_variable_wise_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
			emailwindow.onclose=function()
			 {
				var theform=this.contentDoc.forms[0];
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				data=data+'***'+prodID;
				var url=return_ajax_request_value(data, "print_barcode_ten_youth", "cut_and_lay_entry_variable_wise_controller");
				window.open(url,"##");
				//window.open("cut_and_lay_entry_variable_wise_controller.php?data=" + data+'&action=print_barcode_one_urmi', true );
			 }

			//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_entry_variable_wise_controller");
			//window.open(url,"##");
		}



		function fnc_bundle_report_ten_youth_new()
		{
			var data="";
			var error=1;

			$("input[name=chkbundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{
					error=0;
					var idd=$(this).attr('id').split("_");
					//alert(idd);
					if(data=="") data=$('#hiddenid_'+idd[1] ).val(); else data=data+","+$('#hiddenid_'+idd[1] ).val();
					//if(data=="") data=$(this).find('input[name="hiddenid[]"]').val(); else data=data+","+$(this).find('input[name="hiddenid[]"]').val();
				}
			});
			//alert(data);return;
			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			var order_id='<? echo $order_id; ?>';
			var chk_status= $("#check_all").prop('checked');
			/*if( chk_status==true)
			{
				data=420;
			}*/

			data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+order_id;

			var title = 'Search Job No';
			var page_link = 'cut_and_lay_entry_variable_wise_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
			emailwindow.onclose=function()
			 {
				var theform=this.contentDoc.forms[0];
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				data=data+'***'+prodID;
				var url=return_ajax_request_value(data, "print_barcode_ten_youth_new", "cut_and_lay_entry_variable_wise_controller");
				window.open(url,"##");
				//window.open("cut_and_lay_entry_variable_wise_controller.php?data=" + data+'&action=print_barcode_one_urmi', true );
			 }

			//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_entry_variable_wise_controller");
			//window.open(url,"##");
		}


		function fnc_bundle_report_qr_code()
		{
			var data="";
			var error=1;
			$("input[name=chkbundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{

					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[1] ).val(); else data=data+","+$('#hiddenid_'+idd[1] ).val();
				}
			});

			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			var order_id='<? echo $order_id; ?>';
			data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+order_id;

			var title = 'Search Job No';
			var page_link = 'cut_and_lay_entry_variable_wise_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				data=data+'***'+prodID;
				//var url=return_ajax_request_value(data, "print_barcode_operation", "cut_and_lay_entry_variable_wise_controller");
				http.open( 'POST', 'cut_and_lay_entry_variable_wise_controller.php?action=print_qrcode_operation&data='+ data );

				http.onreadystatechange = response_pdf_data;
				http.send(null);
			 }
		}

        function fnc_bundle_report_qr_code1()
        {
            var data="";
            var error=1;
            $("input[name=chkbundle]").each(function(index, element) {
                if( $(this).prop('checked')==true)
                {
                    error=0;
                    var idd=$(this).attr('id').split("_");
                    if(data=="") data=$('#hiddenid_'+idd[1] ).val(); else data=data+","+$('#hiddenid_'+idd[1] ).val();
                }
            });

            if( error==1 )
            {
                alert('No data selected');
                return;
            }
            var job_id=$("#hidden_update_job_id").val();
            var order_id='<? echo $order_id; ?>';
            data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+order_id;

            var title = 'Search Job No';

            var page_link = 'cut_and_lay_entry_variable_wise_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0];
                var prodID=this.contentDoc.getElementById("txt_selected_id").value;
                data=data+'***'+prodID;
                //var url=return_ajax_request_value(data, "print_barcode_operation", "cut_and_lay_entry_variable_wise_controller");
                http.open( 'POST', 'cut_and_lay_entry_variable_wise_controller.php?action=print_qrcode_operation1&data='+ data );
                http.onreadystatechange = response_pdf_data;
                http.send(null);
            }
        }
		function fnc_bundle_report_qr_code2()
        {
            var data="";
            var error=1;
            $("input[name=chkbundle]").each(function(index, element) {
                if( $(this).prop('checked')==true)
                {
                    error=0;
                    var idd=$(this).attr('id').split("_");
                    if(data=="") data=$('#hiddenid_'+idd[1] ).val(); else data=data+","+$('#hiddenid_'+idd[1] ).val();
                }
            });

            if( error==1 )
            {
                alert('No data selected');
                return;
            }
            var job_id=$("#hidden_update_job_id").val();
            var order_id='<? echo $order_id; ?>';
            data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+order_id;

            var title = 'Search Job No';

            var page_link = 'cut_and_lay_entry_variable_wise_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0];
                var prodID=this.contentDoc.getElementById("txt_selected_id").value;
                data=data+'***'+prodID;
                //var url=return_ajax_request_value(data, "print_barcode_operation", "cut_and_lay_entry_variable_wise_controller");
                http.open( 'POST', 'cut_and_lay_entry_variable_wise_controller.php?action=print_qrcode_operation2&data='+ data );
                http.onreadystatechange = response_pdf_data;
                http.send(null);
            }
        }


		function response_pdf_data()
		{
			if(http.readyState == 4)
			{
				var response = http.responseText.split('###');
				window.open(''+response[1], '', '');
			}
		}

		function fnc_bundle_report_128()
		{
			var data="";
			var error=1;
			$("input[name=chkbundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{

					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[1] ).val(); else data=data+","+$('#hiddenid_'+idd[1] ).val();
				}
			});

			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			var order_id='<? echo $order_id; ?>';
			data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+order_id;



			var title = 'Search Job No';
			var page_link = 'cut_and_lay_entry_variable_wise_controller.php?data='+data+'&action=print_report_bundle_barcode_eight';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
			emailwindow.onclose=function()
			 {
				var theform=this.contentDoc.forms[0];
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				data=data+'***'+prodID;
				var url=return_ajax_request_value(data, "print_barcode_one_128", "cut_and_lay_entry_variable_wise_controller");
				window.open(url,"##");
				//window.open("cut_and_lay_entry_variable_wise_controller.php?data=" + data+'&action=print_barcode_one_urmi', true );
			 }

			//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_entry_variable_wise_controller");
			//window.open(url,"##");
		}

		function fnc_bundle_report_128_v2(type)
		{
			var data="";
			var error=1;
			$("input[name=chkbundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{

					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[1] ).val(); else data=data+","+$('#hiddenid_'+idd[1] ).val();
				}
			});

			if( error==1 )
			{
				alert('No data selected');
				return;
			}

			let bundle_source='';
			let body_part_action = 'print_report_bundle_barcode_eight'

			if(type==2){
				bundle_source = '***1'; // Body part name come from *** Style Wise Body Part Entry Page ***
				body_part_action = 'print_report_bundle_barcode_eight_v2'
			}

			var job_id=$("#hidden_update_job_id").val();
			var order_id='<? echo $order_id; ?>';

			data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+order_id;

			var title = 'Search Job No';

			var page_link = 'cut_and_lay_entry_variable_wise_controller.php?data='+data+bundle_source+'&action='+body_part_action;

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
			var action = (type==1) ? "print_barcode_one_128_v2" : "print_barcode_one_128_v3";
			emailwindow.onclose=function()
			 {
				var theform=this.contentDoc.forms[0];
				var prodID=this.contentDoc.getElementById("txt_selected_id").value;
				var print=this.contentDoc.getElementById("txt_selected_print").value;
				var emb=this.contentDoc.getElementById("txt_selected_emb").value;
				data=data+'***'+prodID+'***'+print+'***'+emb;

				//var url=return_ajax_request_value(data, "print_barcode_one_128_v2", "cut_and_lay_entry_variable_wise_controller");
				var url=return_ajax_request_value(data, action, "cut_and_lay_entry_variable_wise_controller");
				// alert(url);
				window.open(url,"##");
				//window.open("cut_and_lay_entry_variable_wise_controller.php?data=" + data+'&action=print_barcode_one_urmi', true );
			 }

			//var url=return_ajax_request_value(data, "print_report_bundle_barcode", "cut_and_lay_entry_variable_wise_controller");
			//window.open(url,"##");
		}


		function fnc_print_bundle(work_comp,work_location)
		{
			//alert(work_comp,work_location);
			var report_title="Cut and Lay bundle ";
		   	var country=$('#cboCountryBundle').val();
		   	var data=<?php echo $cbo_company_id; ?>;

		   	var title = 'Search Job No ';
			var page_link = 'cut_and_lay_entry_variable_wise_controller.php?data='+data+'&action=print_report_bundle_list_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
			emailwindow.onclose=function()
			 {
				var theform=this.contentDoc.forms[0];
				var bundle_use_for=this.contentDoc.getElementById("txt_selected").value;
				//alert(prodID);

				print_report(<? echo $cbo_company_id; ?>+'*'+<? echo $mst_id; ?>+'*'+<? echo $details_id; ?>+'*'+report_title+'*'+country+'*'+bundle_use_for, "cut_lay_bundle_print", "cut_and_lay_entry_variable_wise_controller")
			 }




		}
		function fnc_print_bundle_list_tow(work_comp,work_location)
		{
			//alert(work_comp,work_location);
			var report_title="Cut and Lay bundle ";
		   	var country=$('#cboCountryBundle').val();
			var job_id=$("#hidden_update_job_id").val();
		   	var data=<?php echo $cbo_company_id; ?>+"***"+job_id;
		   	var title = 'Search Job No ';
			var page_link = 'cut_and_lay_entry_variable_wise_controller.php?data='+data+'&action=print_report_bundle_list_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=270px,height=250px,center=1,resize=0,scrolling=0','../../');
			emailwindow.onclose=function()
			 {
				var theform=this.contentDoc.forms[0];
				var bundle_use_for=this.contentDoc.getElementById("txt_selected").value;
				//alert(prodID);

				print_report(<? echo $cbo_company_id; ?>+'*'+<? echo $mst_id; ?>+'*'+<? echo $details_id; ?>+'*'+report_title+'*'+country+'*'+bundle_use_for, "cut_lay_bundle_list_print2", "cut_and_lay_entry_variable_wise_controller")
			 }




		}



		function fnc_send_printer_text()
		{
			var data="";
			var error=1;
			$("input[name=chkbundle]").each(function(index, element) {
				if( $(this).prop('checked')==true)
				{

					error=0;
					var idd=$(this).attr('id').split("_");
					if(data=="") data=$('#hiddenid_'+idd[1] ).val(); else data=data+","+$('#hiddenid_'+idd[1] ).val();
				}
			});

			if( error==1 )
			{
				alert('No data selected');
				return;
			}
			var job_id=$("#hidden_update_job_id").val();
			data=data+"***"+job_id+'***'+<? echo $mst_id; ?>+'***'+<? echo $details_id; ?>+'***'+<? echo $cbo_gmt_id; ?>+'***'+<? echo $cbo_color_id; ?>+'***'+<? echo $order_id; ?>;
			var url=return_ajax_request_value(data, "report_bundle_text_file", "cut_and_lay_entry_variable_wise_controller");

		    window.open(url+".zip","##");


		}
		function fnc_addRow(actual_id,i)
		{
			var row_num=$('#trBundleListSave tr').length;
			row_num++;
			var clone= $("#trBundleListSave_"+actual_id).clone();
			clone.attr({
				id: "trBundleListSave_"+ row_num,
			});

			clone.find("input,select").each(function(){

			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
			  'name': function(_, name) { return name },
			  'value': function(_, value) { return value }
			});

			}).end();

			$("#trBundleListSave_"+i).after(clone);
			$('#addButton_'+actual_id).removeAttr("onclick").attr("onclick","fnc_addRow("+actual_id+","+row_num+");");
			$('#bundleSizeQty_'+row_num).removeAttr("onBlur").attr("onBlur","bundle_calclution("+row_num+");");

			//=================================================================================================================
			$('#addButton_'+row_num).removeAttr("onclick").attr("onclick","delete_bundle_row("+actual_id+","+row_num+");");
			$("#addButton_"+row_num).val('-');
			//===================================================================================================================
			$("#hiddenExtraTr_"+actual_id).val($("#hiddenExtraTr_"+actual_id).val()+"**"+row_num);
			$("#bundleSizeQty_"+actual_id).attr("disabled",false);
		    $("#bundleSizeQty_"+row_num).attr("disabled",false);
		    $("#bundleNo_"+row_num).attr("disabled",false);
			$("#bundleSizeQty_"+row_num).val('');
			$("#serialNo_"+row_num).html('');
			$("#bundleNo_"+row_num).val($("#bundleNo_"+actual_id).val()+"-");
			$("#rmgNoStart_"+row_num).val('');
			$("#rmgNoEnd_"+row_num).val('');
			$("#hiddenUpdateValue_"+row_num).val('');
			$("#hiddenUpdateFlag_"+actual_id).val(6);
			$("#hiddenUpdateFlag_"+row_num).val(6);
			serial_rearrange();
		}

		function delete_bundle_row(actual_id,rowNo)
		{
			var total_add_id=$("#hiddenExtraTr_"+actual_id).val();
			var countryId=$("#hiddenCountryB_"+rowNo).val();
			var sizeId=$("#hiddenSizeId_"+rowNo).val();
			var pattern=$("#patternNo_"+rowNo).val();
			var rollId=$("#rollId_"+rowNo).val();
			// alert(total_add_id);
			var id_arr=total_add_id.split("**")

			id_arr.splice(id_arr.indexOf(rowNo), 1);
			// alert(id_arr.length)
			if( id_arr.length==1)  $('#addButton_'+actual_id).removeAttr("onclick").attr("onclick","fnc_addRow("+actual_id+","+actual_id+");");
			var new_id=id_arr.join("**");
			$("#hiddenExtraTr_"+actual_id).val(new_id);
			//alert( $("#hiddenExtraTr_"+actual_id).val())
			$("#trBundleListSave_"+rowNo).remove();
			bundle_calclution_on_dlt(countryId,sizeId,pattern,rollId);
			serial_rearrange();
		}

		function bundle_calclution_on_dlt(countryId,sizeId,pattern,rollId)
		{
			var min_rmg_no=1;
			$("#tbl_bundle_list_save").find('tbody tr').each(function()
			{
				var qty=$(this).find('input[name="bundleSizeQty[]"]').val()*1;

				var countryIdC=parseInt($(this).find('input[name="hiddenCountryB[]"]').val());
				var sizeIdC=parseInt($(this).find('input[name="hiddenSizeId[]"]').val());
				var patternNoC=trim($(this).find('input[name="patternNo[]"]').val());
				var rollIdC=parseInt($(this).find('input[name="rollId[]"]').val());

				//if(countryId==countryIdC && sizeId==sizeIdC && pattern==patternNoC && rollId==rollIdC)
				//{
					if(qty*1>0)
					{
						var from=min_rmg_no;
						var to=min_rmg_no*1+qty*1-1;
						min_rmg_no+=qty*1;
						$(this).find('input[name="rmgNoStart[]"]').val(from);
						$(this).find('input[name="rmgNoEnd[]"]').val(to);
					}
					else
					{
						$(this).find('input[name="rmgNoStart[]"]').val('');
						$(this).find('input[name="rmgNoEnd[]"]').val('');
					}
				//}
			});
		}

		function serial_rearrange()
		{
			var k=1;
			$("#tbl_bundle_list_save").find('tbody tr').each(function()
			 {
				$(this).find('input[name="sirialNo[]"]').val(k);
				//$("#tbl_bundle_list_save tr:eq("+k+")").removeAttr('id').attr('id','bundleNo_'+k);
				k++;
			});
		}

		function fnc_updateRow(id_row)
		{
			$("#bundleSizeQty_"+id_row).attr("disabled",false);
			//$("#sizeName_"+id_row).attr("disabled",false);
			//$("#cboCountryB_"+id_row).removeAttr("disabled","disabled");
			$("#hiddenUpdateFlag_"+id_row).val(6);
		}

		function fnc_rearrange_rmg (id_num)
		{
			var s=0;
			var first_rmg=$("#rmgNoStart_1").val();
			var last_rmg=0;
			var bundle_qty=0;
			$("#tbl_bundle_list_save").find('tbody tr').each(function()
			 {
				  bundle_qty=$(this).find('input[name="bundleSizeQty[]"]').val();

				 if(s==0)
				 {
					 $(this).find('input[name="rmgNoEnd[]"]').val(parseInt(bundle_qty)+parseInt(first_rmg)-1);
					  last_rmg=parseInt(bundle_qty)+parseInt(first_rmg)-1;
				 }
				 else
				 {
					$(this).find('input[name="rmgNoStart[]"]').val(parseInt(last_rmg)+1);
					last_rmg=parseInt(last_rmg)+parseInt(bundle_qty);
					$(this).find('input[name="rmgNoEnd[]"]').val(parseInt(last_rmg));
				 }
				s++;
			});
		}

		function bundle_calclution(rowNo)
		{
			var countryId=$("#hiddenCountryB_"+rowNo).val();
			var sizeId=$("#hiddenSizeId_"+rowNo).val();
			var pattern=$("#patternNo_"+rowNo).val();
			var rollId=$("#rollId_"+rowNo).val();


			var bundleQty=$("#bundleSizeQty_"+rowNo).val();
			var bundleArr = new Array(); var p=1;
			$("#tbl_bundle_list_save").find('tbody tr').each(function()
			{
				var bundle=$(this).find('input[name="bundleNo[]"]').val();
				bundleArr[p]=bundle;
				p++;
			});

			if(bundleQty<1)
			{
				var actual_id=$("#hiddenExtraTr_"+rowNo).val();
				var total_add_id=$("#hiddenExtraTr_"+actual_id).val();

				var id_arr=total_add_id.split("**")

				id_arr.splice(id_arr.indexOf(rowNo), 1);
				// alert(id_arr.length)
				if( id_arr.length==1)  $('#addButton_'+actual_id).removeAttr("onclick").attr("onclick","fnc_addRow("+actual_id+","+actual_id+");");
				var new_id=id_arr.join("**");
				$("#hiddenExtraTr_"+actual_id).val(new_id);
				//alert( $("#hiddenExtraTr_"+actual_id).val())

				$("#trBundleListSave_"+rowNo).remove();
				//serial_rearrange();
				var k=1;
				$("#tbl_bundle_list_save").find('tbody tr').each(function()
				{
					$(this).find('input[name="sirialNo[]"]').val(k);
					$(this).find('input[name="bundleNo[]"]').val( bundleArr[k] );
					k++;
				});
			}

			var min_rmg_no=1;
			$("#tbl_bundle_list_save").find('tbody tr').each(function()
			{
				var qty=$(this).find('input[name="bundleSizeQty[]"]').val()*1;

				var countryIdC=parseInt($(this).find('input[name="hiddenCountryB[]"]').val());
				var sizeIdC=parseInt($(this).find('input[name="hiddenSizeId[]"]').val());
				var patternNoC=trim($(this).find('input[name="patternNo[]"]').val());


				if(rmg_no_creation=='5') // size and pattern wise
				{
					if(pattern==patternNoC && sizeId==sizeIdC)
					{
						if(qty*1>0)
						{
							var from=min_rmg_no;
							var to=min_rmg_no*1+qty*1-1;
							min_rmg_no+=qty*1;
							$(this).find('input[name="rmgNoStart[]"]').val(from);
							$(this).find('input[name="rmgNoEnd[]"]').val(to);
						}
						else
						{
							$(this).find('input[name="rmgNoStart[]"]').val('');
							$(this).find('input[name="rmgNoEnd[]"]').val('');
						}
					}

				}
				else
				{
					if(sizeId==sizeIdC) //countryId==countryIdC &&  && pattern==patternNoC
					{
						if(qty*1>0)
						{
							var from=min_rmg_no;
							var to=min_rmg_no*1+qty*1-1;
							min_rmg_no+=qty*1;
							$(this).find('input[name="rmgNoStart[]"]').val(from);
							$(this).find('input[name="rmgNoEnd[]"]').val(to);
						}
						else
						{
							$(this).find('input[name="rmgNoStart[]"]').val('');
							$(this).find('input[name="rmgNoEnd[]"]').val('');
						}
					}
				}
			});



			/*$("#tbl_bundle_list_save").find('tbody tr').each(function()
			{
				var qty=$(this).find('input[name="bundleSizeQty[]"]').val()*1;
				var from=min_rmg_no;
				var to=min_rmg_no*1+qty*1-1;
				min_rmg_no+=qty*1;

				$(this).find('input[name="rmgNoStart[]"]').val(from);
				$(this).find('input[name="rmgNoEnd[]"]').val(to);
			});*/

		}
		//**************************************bundle update ***************************************

		function fnc_cut_lay_bundle_info(operation)
		{
			var cbo_color_type=<? echo $cbo_color_type; ?>;

			if(operation==2)
			{
				alert("Delete Restricted.");
				show_msg('13');
				return;
			}
			var dataString_bundle="";
			var j=0; var z=0; var tot_row=0; var sl=0; var error=0;
			var bundle_check_arr=new Array();
			$("#tbl_bundle_list_save").find('tbody tr').each(function()
			{
				var bundle_break=($(this).find('input[name="bundleNo[]"]').val()).split('-');
				var bundle_no_split_length=bundle_break.length;
				if(bundle_no_split_length>3)
				{
					var check_bundle_prifix=bundle_no_split_length-1;
					 if(bundle_break[check_bundle_prifix]=="")
					 {
					  $(this).find('input[name="bundleNo[]"]').css({"background-color":"red"});
					  error=1;
					 }
				}


				if( jQuery.inArray( $(this).find('input[name="bundleNo[]"]').val(), bundle_check_arr )>-1)
				{
					alert('Duplicate Bundle. Bundle No '+$(this).find('input[name="bundleNo[]"]').val());
					error=1;
					return;
				}


				bundle_check_arr.push($(this).find('input[name="bundleNo[]"]').val());




				//bundle_check_arr[$(this).find('input[name="bundleNo[]"]').val()]=$(this).find('input[name="bundleNo[]"]').val();
					/*var bundle_no=($(this).find('input[name="bundleNo[]"]').val()).match("/");
				if(bundle_no=="/")
				{
					 var bundle_break=($(this).find('input[name="bundleNo[]"]').val()).split('/');
					 if(bundle_break[1]=="")
					 {
					  $(this).find('input[name="bundleNo[]"]').css({"background-color":"red"});
					  error=1;
					 }
				}*/
				sl++;
			});
			if(error==1) { return;}

			$("#tbl_bundle_list_save").find('tbody tr').each(function()
			 {
				var bundle_no=$(this).find('input[name="bundleNo[]"]').val();
				var bundle_size_qty=$(this).find('input[name="bundleSizeQty[]"]').val();
				var bundle_from=$(this).find('input[name="rmgNoStart[]"]').val();
				var bundle_to=$(this).find('input[name="rmgNoEnd[]"]').val();
				var bundle_size_id=$(this).find('select[name="sizeName[]"]').val();
				var hidden_size_id=$(this).find('input[name="hiddenSizeId[]"]').val();
				var hidden_size_qty=$(this).find('input[name="hiddenSizeQty[]"]').val();
				var hidden_update_flag=$(this).find('input[name="hiddenUpdateFlag[]"]').val();
				var hiddenUpdateValue=$(this).find('input[name="hiddenUpdateValue[]"]').val();
				var rollNo=$(this).find('input[name="rollNo[]"]').val();
				var rollId=$(this).find('input[name="rollId[]"]').val();
				var patternNo=$(this).find('input[name="patternNo[]"]').val();
				var isExcess=$(this).find('input[name="isExcess[]"]').val();

				var hiddenCountryType=$(this).find('input[name="hiddenCountryTypeB[]"]').val();
				var cboCountry=$(this).find('select[name="cboCountryB[]"]').val();
				var po_id=$(this).find('select[name="cboPoId[]"]').val();
				var hiddenCountry=$(this).find('input[name="hiddenCountryB[]"]').val();

				j++;
				tot_row++;
				dataString_bundle+='&txtBundleNo_' + j + '=' + bundle_no + '&txtBundleQty_' + j + '=' + bundle_size_qty + '&txtBundleFrom_' + j + '=' + bundle_from+ '&txtBundleTo_' + j + '=' + bundle_to+ '&txtSizeId_' + j + '=' + bundle_size_id+ '&txtHiddenSizeId_' + j + '=' + hidden_size_id+ '&hiddenSizeqty_' + j + '=' + hidden_size_qty+ '&hiddenUpdateFlag_' + j + '=' + hidden_update_flag+'&hiddenUpdateValue_' + j + '=' + hiddenUpdateValue+'&hiddenCountryType_' + j + '=' + hiddenCountryType+'&hiddenCountry_' + j + '=' + hiddenCountry+'&cboCountry_' + j + '=' + cboCountry +'&rollNo_' + j + '=' + rollNo +'&rollId_' + j + '=' + rollId +'&patternNo_' + j + '=' + patternNo +'&isExcess_' + j + '=' + isExcess +'&cboPoId_' + j + '=' + po_id;
			});


			var bundle_mst_id=$("#hidden_mst_id").val();
			var bundle_dtls_id=$("#hidden_detls_id").val();
			//alert(bundle_dtls_id);return;
			var hidden_cutting_no=$("#hidden_cutting_no").val();
			var data="action=save_update_delete_bundle&operation="+operation+'&tot_row='+tot_row+'&bundle_dtls_id='+bundle_dtls_id+'&bundle_mst_id='+bundle_mst_id+dataString_bundle+'&hidden_cutting_no='+hidden_cutting_no+'&color_type_id='+cbo_color_type;
			//alert(data);return;hidden_cutting_no
			freeze_window(operation);
			http.open("POST","cut_and_lay_entry_variable_wise_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =fnc_cut_lay_bundle_reply_info;
		}

		function fnc_cut_lay_bundle_reply_info()
		{
			if(http.readyState == 4)
			{
				var reponse=trim(http.responseText).split('**');

				show_msg(trim(reponse[0]));

				if((reponse[0]==0 || reponse[0]==1))
				{
					$('#msg_box_popp').fadeTo(100,1,function() //start fading the messagebox
					{
						$('#msg_box_popp').html("Data Update  Sussessfully").removeClass('messagebox').addClass('messagebox_error').fadeOut(5500);
					});
					set_button_status(1, permission, 'fnc_cut_lay_bundle_info',2);
					show_list_view(reponse[1]+'**'+reponse[2]+'**'+reponse[3],'show_bundle_list_view','search_div','cut_and_lay_entry_variable_wise_controller','setFilterGrid("list_view",-1)');
				}
				else if(reponse[0]==200)
				{
					alert("Update Restricted.This information found in Cutting Qc Page Which System Id "+reponse[1]+".");
				}
				release_freezing();
			}
		}

		function fnc_rollWiseSizeQty()
		{
			var size_row_num=$('#tbl_size tbody tr').length;
			var size_data='';
			for(var k=1; k<=size_row_num; k++)
			{
				var hidden_sizef_id=$("#hidden_sizef_id_"+k).val();
				var txt_sizef_ratio=$("#txt_sizef_ratio_"+k).val();

				if(size_data=="")
				{
					size_data=hidden_sizef_id+"_"+txt_sizef_ratio;
				}
				else
				{
					size_data+="|"+hidden_sizef_id+"_"+txt_sizef_ratio;
				}
			}

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe','cut_and_lay_entry_variable_wise_controller.php?rollData='+'<? echo $rollData; ?>'+'&size_data='+size_data+'&action=rollSize_popup','Roll Popup', 'width=680px,height=300px,center=1,resize=1,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var roll_data=this.contentDoc.getElementById("hidden_roll_data").value; //Barcode Nos
				$("#roll_data").val(roll_data);
			}
		}

		function fnc_printBtnShowHide()
		{
			var report_id=report_ids.split(",");
			for (var k=0; k<report_id.length; k++)
			{
				if(report_id[k]==294) $("#btn1").show();
				if(report_id[k]==295) $("#btn2").show();
				if(report_id[k]==296) $("#btn3").show();
				if(report_id[k]==297) $("#btn4").show();
				if(report_id[k]==298) $("#btn5").show();
				if(report_id[k]==299) $("#btn6").show();
				if(report_id[k]==300) $("#btn7").show();
				if(report_id[k]==301) $("#btn8").show();
				if(report_id[k]==302) $("#btn9").show();
				//if(report_id[k]==303) $("#btn10").show();

			}
		}

		function fnc_print_qc_bundle(type)
		{
			var report_title="Cut. Panel Inspection Report";
			var country=$('#cboCountryBundle').val();
			if(type==1)
			{
			print_report(<? echo $cbo_company_id; ?>+'*'+<? echo $mst_id; ?>+'*'+<? echo $details_id; ?>+'*'+report_title+'*'+country+'*'+type, "cut_lay_qc_bundle_print", "cut_and_lay_entry_variable_wise_controller")
			}
			else if(type==2)
			{
				print_report(<? echo $cbo_company_id; ?>+'*'+<? echo $mst_id; ?>+'*'+<? echo $details_id; ?>+'*'+report_title+'*'+country+'*'+type, "cut_lay_qc_bundle_print2", "cut_and_lay_entry_variable_wise_controller")
			}
			else if(type==3)
			{
				print_report(<? echo $cbo_company_id; ?>+'*'+<? echo $mst_id; ?>+'*'+<? echo $details_id; ?>+'*'+report_title+'*'+country+'*'+type, "cut_lay_qc_bundle_print3", "cut_and_lay_entry_variable_wise_controller")
			}
		}

	</script>

</head>
<body onLoad="set_hotkey()">
<div id="msg_box_popp" style=" height:15px; width:200px;  position:relative; left:250px "></div>
	<div align="center" style="width:100%; overflow-y:hidden; position:absolute; top:5px;">
		<input type="hidden" id="hidden_cutting_no" name="hidden_cutting_no" value="<? echo $cutting_no; ?>" />
        <div style="display:none"><?=load_freeze_divs ("../../../",$permission); ?></div>
		<?
			$color_name=return_field_value("color_name","lib_color","id='".$cbo_color_id."'");
			$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
			$pcs_per_bundle=return_field_value("pcs_per_bundle","ppl_cut_lay_dtls","id=$details_id ","pcs_per_bundle");
		?>
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <fieldset style="width:450px;">
                <table cellpadding="0" cellspacing="0" width="450" class="" id="tbl_bundle_size">
                    <thead>
                        <tr>
                            <td><strong>Color</strong></td>
                            <td>
                                <input type="text" style="width:80px" class="text_boxes"  name="txt_show_color" id="txt_show_color" value="<? echo $color_name; ?>" disabled readonly/>
                                <input type="hidden" id="hidden_update_job_id" name="hidden_update_job_id" value="<? echo $job_id; ?>"/>
                                <input type="hidden" id="hidden_update_cut_no" name="hidden_update_cut_no" value="<? echo $cutting_no; ?>"/>
                            </td>
                            <td><strong>Plies</strong></td>
                            <td>
                                <input type="text" style="width:80px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<?php echo $txt_piles;?>" disabled readonly/>
                            </td>
                            <td class="must_entry_caption"><strong>Pcs Per Bundle</strong></td>
                            <td><input type="text" style="width:50px" class="text_boxes_numeric"  name="txt_bundle_pcs" id="txt_bundle_pcs" value="<? echo $pcs_per_bundle; ?>" /></td>
                        </tr>
                    </thead>
                </table>
            </fieldset>
            <br/>
            <fieldset style="width:450px;">
            	<?
				$po_no_arr=return_library_array("select id, po_number from wo_po_break_down where id in($order_id)",'id','po_number');
				$po_country_array=array(); $size_order_arr=array(); $poArr=array();
                $sql_query=sql_select("SELECT po_break_down_id, country_type, country_id, size_number_id, CAST(plan_cut_qnty as INT) as plan_cut_qnty, country_ship_date, size_order from wo_po_color_size_breakdown where item_number_id=$cbo_gmt_id and po_break_down_id in($order_id) and color_number_id=$cbo_color_id and status_active=1 and is_deleted=0 order by country_ship_date, size_order,country_type");
				//echo "select country_type, country_id, size_number_id, plan_cut_qnty, country_ship_date, size_order from wo_po_color_size_breakdown where item_number_id=$cbo_gmt_id and po_break_down_id=$order_id and color_number_id=$cbo_color_id and status_active=1 and is_deleted=0 order by size_order, country_ship_date, country_type, id";
                $size_details=array(); $sizeId_arr=array(); $shipDate_arr=array(); $distributed_qty_arr=array();
                foreach($sql_query as $row)
                {
                    //if($row[csf('country_type')]==1) $country_id=0; else $country_id=$row[csf('country_id')];
                    $po_id=$row[csf('po_break_down_id')];
					$country_id=$row[csf('country_id')];

                    $size_details[$po_id][$row[csf('country_type')]][$country_id][$row[csf('size_number_id')]]+=$row[csf("plan_cut_qnty")];
                    $sizeId_arr[$row[csf('size_number_id')]]+=$row[csf("plan_cut_qnty")];
                    $shipDate_arr[$po_id][$row[csf('country_type')]][$country_id]=$row[csf("country_ship_date")];
					$po_country_array[$country_id]=$country_arr[$country_id];

					$size_order_arr[$row[csf('size_number_id')]]=$row[csf("size_order")];
                }

                $size_wise_arr=array();
                $sizeWiseData=sql_select("SELECT size_ratio, size_id, marker_qty, bundle_sequence from ppl_cut_lay_size_dtls where mst_id=".$mst_id." and dtls_id=".$details_id." and status_active=1");
                foreach($sizeWiseData as $value)
                {
                    $size_wise_arr[$value[csf('size_id')]]['ratio']=$value[csf('size_ratio')];
                    $size_wise_arr[$value[csf('size_id')]]['marker_qty']=$value[csf('marker_qty')];
					$size_wise_arr[$value[csf('size_id')]]['seq']=$value[csf('bundle_sequence')];
                }

                $sizeDaraArr=array();
                $sizeData=sql_select("SELECT a.id, a.size_ratio, a.size_id, a.marker_qty, a.bundle_sequence, a.order_id, a.country_type, a.country_id, a.excess_perc from ppl_cut_lay_size a where a.mst_id=".$mst_id." and a.dtls_id=".$details_id." and a.status_active=1");
                if(count($sizeData)>0)
                {
                    $is_update=1;
                    foreach($sizeData as $value)
                    {
                        $sizeDaraArr[$value[csf('order_id')]][$value[csf('country_type')]][$value[csf('country_id')]][$value[csf('size_id')]]=$value[csf('size_ratio')]."**".$value[csf('marker_qty')]."**".$value[csf('bundle_sequence')]."**".$value[csf('id')]."**".$value[csf('excess_perc')];
						$distributed_qty_arr[$value[csf('size_id')]]+=$value[csf('marker_qty')];
                    }
                }
                else
                {
                    $is_update=0;
                }

                $lay_bl_qty_arr=array();
                $lay_blData=sql_select("SELECT a.order_id, sum(a.size_qty) as marker_qty, a.country_type, a.country_id, a.size_id from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b where b.id=a.dtls_id and a.order_id in($order_id) and b.gmt_item_id=$cbo_gmt_id and b.color_id=$cbo_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.order_id, a.country_type,a.country_id, a.size_id");
                foreach($lay_blData as $value)
                {
                    $lay_bl_qty_arr[$value[csf('order_id')]][$value[csf('country_type')]][$value[csf('country_id')]][$value[csf('size_id')]]=$value[csf('marker_qty')];
                    $lay_bl_qty_size_arr[$value[csf('size_id')]]+=$value[csf('marker_qty')];
                }

				$size_bl_qty_arr=return_library_array("SELECT sum(a.size_qty) as size_qty, a.size_id from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b where b.id=a.dtls_id and a.order_id in($order_id) and b.gmt_item_id=$cbo_gmt_id and b.color_id=$cbo_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.size_id",'size_id','size_qty');

                ?>
                <table cellpadding="0" cellspacing="0" width="530" id="tbl_size">
                    <thead class="form_table_header">
                        <th>Size</th>
                        <th>Lay Balance</th>
                        <th>Size Ratio</th>
                        <th>Size Qty.</th>
                        <th>Bundle Priority</th>
                        <th>Distributed Qty.</th>
                    </thead>
                    <tbody>
                    <?
						//print_r($size_order_arr);
                        $i=1; $total_layf_balance=0; $total_markerf_qty=0; $total_sizef_ratio=0; $sizeDataArray=array();
						//asort($size_order_arr);
						//foreach($size_order_arr as $size_id=>$size_order)
                        foreach($sizeId_arr as $size_id=>$plan_cut_qty)
                        {
                            //echo $plan_cut_qty."-".$lay_bl_qty_size_arr[$size_id];
							//$lay_balance=$plan_cut_qty-$lay_bl_qty_size_arr[$size_id]+$size_wise_arr[$size_id]['marker_qty'];
							//$plan_cut_qty=$sizeId_arr[$size_id];
							$lay_balance=$plan_cut_qty-$size_bl_qty_arr[$size_id];
                            $total_layf_balance+=$lay_balance;

                            $total_markerf_qty+=$size_wise_arr[$size_id]['marker_qty'];
							$total_distributed_qty+=$distributed_qty_arr[$size_id];
                            $total_sizef_ratio+=$size_wise_arr[$size_id]['ratio'];

							$sizeDataArray[$size_id]=$size_wise_arr[$size_id]['ratio'];
                            ?>
                            <tr id="size_<? echo $i; ?>">
                                <td align="center">
                                      <input type="text" style="width:80px" class="text_boxes" name="txt_sizef_<? echo $i; ?>" id="txt_sizef_<? echo $i; ?>" value="<? echo $size_arr[$size_id]; ?>" disabled readonly/>
                                      <input type="hidden" id="hidden_sizef_id_<? echo $i; ?>" name="hidden_sizef_id_<? echo $i; ?>" value="<? echo $size_id; ?>">
                                </td>
                                <td align="center">
                                    <input type="text" style="width:80px" class="text_boxes_numeric"  name="txt_layf_balance_<? echo $i; ?>" id="txt_layf_balance_<? echo $i; ?>"  value="<? echo $lay_balance; ?>" disabled />
                                </td>
                                <td align="center">
                                    <input type="text" style="width:80px" class="text_boxes_numeric" onKeyUp="check_sizef_qty(<? echo $txt_piles; ?>,this.value,this.id)" name="txt_sizef_ratio_<? echo $i; ?>" id="txt_sizef_ratio_<? echo $i; ?>" value="<? echo $size_wise_arr[$size_id]['ratio']; ?>" />
                                </td>
                                <td align="center">
                                    <input type="text" style="width:80px" class="text_boxes_numeric" name="txt_sizef_qty_<? echo $i; ?>" id="txt_sizef_qty_<? echo $i; ?>"  value="<? echo $size_wise_arr[$size_id]['marker_qty']; ?>" disabled readonly />
                                     <input type="hidden" name="txt_sizef_prev_qty_<? echo $i; ?>" id="txt_sizef_prev_qty_<? echo $i; ?>"  value="<? echo $size_wise_arr[$size_id]['marker_qty']; ?>" />
                                </td>
                                 <td align="center">
                                    <input type="text" style="width:60px" class="text_boxes_numeric" name="txt_bundle_<? echo $i; ?>" id="txt_bundle_<? echo $i; ?>" onKeyUp="sequence_duplication_check(<? echo $i; ?>)" value="<? echo $size_wise_arr[$size_id]['seq']; ?>" />
                                </td>
                                <td align="center">
                                    <input type="text" style="width:100px" class="text_boxes_numeric" name="txt_distributed_qty_<? echo $i; ?>" id="txt_distributed_qty_<? echo $i; ?>" value="<? echo $distributed_qty_arr[$size_id]; ?>" disabled readonly />
                                </td>
                            </tr>
                        <?
                            $i++;
                        }
						$allData=$rollData;
                    ?>
                    </tbody>
                    <tfoot>
                        <tr class="form_table_header">
                            <th>Total</th>
                            <th align="right"><? echo $total_layf_balance; ?></th>
                            <th id="total_sizef_ratio" align="right"><? echo $total_sizef_ratio; ?></th>
                            <th id="total_sizef_qty" align="right"><? echo $total_markerf_qty; ?>
                            <input type='hidden' id="hidden_size_marker_qty" name="hidden_size_marker_qty" value="<? echo $total_markerf_qty; ?>"/></th>
                            <th>&nbsp;<input type='hidden' id="roll_data" name="roll_data" value="<? //echo chop($allData,'|'); ?>"/></th>
                            <th align="right" id="total_distributed_qty"><? echo $total_distributed_qty; ?></th>
                        </tr>
                    </tfoot>
                </table>
				</fieldset>
                <br>
                <div>
                	<!--<input type="button" style="width:150px" value="Roll Wise Size Qty" name="btn" id="btn" class="formbuttonplasminus" onClick="fnc_rollWiseSizeQty();"/>-->
                    <fieldset style="width:780px">
                    	<legend>Roll Wise Size Qty</legend>
                    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="770" id="tbl_roll">
                            <thead>
                                <th width="60">Roll No</th>
                                <th width="70">Roll Wgt.</th>
                                <th width="60">Plies</th>
                                <?
                                foreach($sizeDataArray as $key=>$value)
                                {
                                    echo '<th>'.$size_arr[$key].'</th>';
                                }
                                ?>
                            </thead>
                           	<?
								$i=1; $rollDatas=explode("**",$allData);
								foreach($rollDatas as $data)
								{
                                    $datas=explode("=",$data);
                                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>">
                                        <td>
                                            <input type="text" id="rollNo_<? echo $i;?>" name="rollNo[]" style="width:50px" class="text_boxes_numeric" value="<? echo $datas[1]; ?>" disabled>
                                            <input type="hidden" id="rollId_<? echo $i; ?>" name="rollId[]" value="<? echo $datas[2]; ?>">
                                        </td>
                                        <td><input type="text" id="rollWgt_<? echo $i; ?>" name="rollWgt[]" style="width:60px" class="text_boxes_numeric" value="<? echo $datas[3]; ?>" disabled></td>
                                        <td><input type="text" id="piles_<? echo $i; ?>" name="piles[]" style="width:50px" class="text_boxes_numeric" value="<? echo $datas[4]; ?>" disabled></td>
                                        <?
                                        foreach($sizeDataArray as $key=>$value)
                                        {
                                        ?>
                                            <td align="center"><input type="text" id="sqty_<? echo $key."_".$i; ?>" name="sqty[]" style="width:50px" class="text_boxes_numeric" value="<? if($value*$datas[4]>0) echo $value*$datas[4]; ?>" disabled></td>
                                        <?
                                        }
                                        ?>
                                    </tr>
                                    <?
                                    $i++;
                                }
                        	?>
                        </table>
                        <table>
                        	<tr>
                                <td align="center" valign="middle" colspan="5" >
                                    <? echo load_submit_buttons( $permission, "fnc_cut_lay_size_info", $is_update,0,"clear_size_form()",1);?>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" colspan="7" >

                                    <input type="button" id="close_size_id" name="close_size_id"  class="formbutton"  style="width:50px" onClick="size_popup_close(<? echo $size; ?>,$('#hidden_marker_qty').val(),$('#hidden_plant_qty').val(),$('#hidden_total_marker').val(),$('#hidden_lay_balance').val())" value="Close"/>
                                    <? echo create_drop_down("cboCountryBundle",120,$po_country_array,'',1,'-- ALL Country --','','',0); ?>

                                    <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 6/Page" class="formbutton" onClick="fnc_bundle_report(6)" style="display:none"/>
                                    <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 8/Page" class="formbutton" onClick="fnc_bundle_report_eight()" style="display:none"/>
									<input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 9/Page" class="formbutton" onClick="fnc_bundle_report_nine()" style="display:none"/>
                                    <input type="button" id="btn_bundle_stiker" name="btn_bundle_stiker" value="Bundle Sticker 1/Page" class="formbutton" onClick="fnc_bundle_report_one()" style="display:none"/>

                                    <?
								    $print_report_format_arr=return_library_array( "select template_name, format_id from lib_report_template where  module_id=4 and report_id=118 and is_deleted=0 and status_active=1 and template_name=$cbo_company_id", "template_name", "format_id");
                                    $report_id=explode(",",$print_report_format_arr[$cbo_company_id]);
                                    // echo "<pre>"; print_r($report_id); die;
                                    foreach($report_id as $res){

                                        if($res==298){?>
                                            <input type="button" id="btn_stiker_urmi" name="btn_stiker_urmi" value="Sticker 1/Page" class="formbutton" onClick="fnc_bundle_report_one_urmi()"/>
                                        <?}elseif($res==333){?>
                                            <input type="button" id="btn_stiker_urmi" name="btn_stiker_urmi" value="Sticker 10/Page" class="formbutton" onClick="fnc_bundle_report_ten_urmi()"/>
                                        <?}elseif($res==332){?>
                                            <input type="button" id="btn_print" name="btn_print" value=" Bundle List" class="formbutton" onClick="fnc_print_bundle()"/>
										<?}elseif($res==806){?>
                                            <input type="button" id="btn_print" name="btn_print" value=" Bundle List 2" class="formbutton" onClick="fnc_print_bundle_list_tow()"/>
                                        <?}elseif($res==331){?>
                                            <input type="button" id="btn_stiker_youth" name="btn_stiker_youth" value="Sticker 10/Page 2" class="formbutton" onClick="fnc_bundle_report_ten_youth()"/>
                                         <?}elseif($res==300){?>
                                            <input type="button" id="btn_send_to_printer" name="btn_send_to_printer" value="Send To Printer" class="formbutton" onClick="fnc_send_printer_text()" style="display:none"/>
                                        <?}elseif($res==328){?>
                                            <input type="button" id="btn_bundle_stiker128" name="btn_bundle_stiker128" value="Bundle Sticker 128" class="formbutton" onClick="fnc_bundle_report_128()" />
                                        <?}elseif($res==329){?>
                                            <input type="button" id="btn_bundle_stiker128_v2" name="btn_bundle_stiker128_v2" value="Bundle Sticker 128 V2" class="formbutton" onClick="fnc_bundle_report_128_v2(1)" />
                                        <?}elseif($res==330){?>
                                            <input type="button" id="btn_bundle_qr_code" name="btn_bundle_qr_code" value="Bundle Sticker QRCode" class="formbutton" onClick="fnc_bundle_report_qr_code()" />
                                        <?}elseif($res==379){?>
                                            <input type="button" id="btn_stiker_youth1" name="btn_stiker_youth1" value="Sticker 10/Page 3" class="formbutton" onClick="fnc_bundle_report_ten_youth_new()"/>
                                        <?}elseif($res==380){?>
                                            <input type="button" id="btn_bundle_stiker128_v3" name="btn_bundle_stiker128_v3" value="128 V3" class="formbutton" onClick="fnc_bundle_report_128_v2(2)" />
                                        <?}elseif($res==297){?>
                                            <input type="button" id="btn4" name="btn4" value="Sticker 8/Page" class="formbutton" onClick="fnc_bundle_report_eight();"/>
                                        <?}elseif($res==453){?>
                                            <input type="button" id="btn9" name="btn9" value="Sticker 9/Page" class="formbutton" onClick="fnc_bundle_report_nine();"/>
                                        <?}elseif($res==714){?>
                                            <input type="button" id="btn12" name="btn12" value="QC Bundle" class="formbutton" onClick="fnc_print_qc_bundle(1);"/>
                                            <input type="button" id="btn15" name="btn15" value="QC Bundle 2" class="formbutton" onClick="fnc_print_qc_bundle(2);"/>
                                            <input type="button" id="btn16" name="btn16" value="QC Bundle 3" class="formbutton" onClick="fnc_print_qc_bundle(3);"/>
                                        <?}elseif($res==787){?>
                                            <input type="button" id="btn13" name="btn13" value="QR Code Sticker" class="formbutton" onClick="fnc_bundle_report_qr_code1();"/>
                                        <?}elseif($res==808){?>
                                            <input type="button" id="btn14" name="btn14" value="QR Code Sticker 2" class="formbutton" onClick="fnc_bundle_report_qr_code2();"/>
                                        <?}
                                    }
									?>
                                    <input type='hidden' id="hidden_marker_no_x" name="hidden_marker_no_x"  />
                                    <input type='hidden' id="hidden_total_marker" name="hidden_total_marker"  />
                                    <input type='hidden' id="hidden_lay_balance" name="hidden_lay_balance"  />
                                    <input type='hidden' id="hidden_plant_qty" name="hidden_plant_qty"  />
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
                <br>
				<fieldset style="width:450px">
                <h3 align="left" id="accordion_h1" style="width:810px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> +Country & Size Wise Lay Balance</h3>
         		<div id="content_search_panel">  <!-- style="display:none"-->
                    <table cellpadding="0" cellspacing="0" width="800" class="" rules="all" border="1" id="tbl_size_details">
                        <thead class="form_table_header">
                        	<th>Order No.</th>
                            <th>Country Type</th>
                            <th>Country Name</th>
                            <th>Country Ship. Date</th>
                            <th>Size</th>
                            <th>Lay Balance</th>
                            <th>Copy&nbsp;<input type="checkbox" name="checkbox" id="checkbox"><br>&nbsp;Excess %</th>
                            <th>Qty.</th>
                        </thead>
                        <tbody>
                        <?
                        $i=1; $total_lay_balance=0; $total_marker_qty=0; $total_size_ratio=0;
                       	foreach($size_details as $po_id=>$po_val)
						{
							foreach($po_val as $country_type_id=>$country_val)
							{
								foreach($country_val as $country_id=>$size_data)
								{
									foreach($size_data as $size_id=>$plan_cut_qnty)
									{
										$data=explode("**",$sizeDaraArr[$po_id][$country_type_id][$country_id][$size_id]);
										$lay_balance=$plan_cut_qnty-$lay_bl_qty_arr[$po_id][$country_type_id][$country_id][$size_id]+$data[1];
										$total_lay_balance+=$lay_balance;
										$total_marker_qty+=$data[1];
										$total_size_ratio+=$data[0];
									?>
										<tr id="gsd_<? echo $i; ?>">
											<td align="center">
												 <input type="text" style="width:100px" class="text_boxes" name="poNo_<? echo $i; ?>" id="poNo_<? echo $i; ?>" value="<? echo $po_no_arr[$po_id]; ?>" disabled />
                                                 <input type="hidden" name="poId_<? echo $i; ?>" id="poId_<? echo $i; ?>" value="<? echo $po_id; ?>"/>
											</td>
											<td align="center">
												<?
													echo create_drop_down( "cboCountryType_".$i, 100, $country_type,'', 0, '',$country_type_id,'',1);
												?>
											</td>
											<td align="center">
												<?
													echo create_drop_down( "cboCountry_".$i, 110, $country_arr, '', 1, '',$country_id,'',1);
												?>
											</td>
											<td align="center">
												<input type="text" style="width:80px" class="datepicker" name="shipdate_<? echo $i; ?>" id="shipdate_<? echo $i; ?>" value="<? echo change_date_format($shipDate_arr[$po_id][$country_type_id][$country_id]); ?>" disabled readonly />
											</td>
											<td align="center">
												  <input type="text" style="width:80px" class="text_boxes"  name="txt_size_<? echo $i; ?>" id="txt_size_<? echo $i; ?>" value="<? echo $size_arr[$size_id]; ?>" disabled readonly />
												  <input type="hidden" id="hidden_size_id_<? echo $i; ?>" name="hidden_size_id_<? echo $i; ?>" value="<? echo $size_id; ?>">
												  <input type="hidden" id="update_size_id_<? echo $i; ?>" name="update_size_id_<? echo $i; ?>" value="<? echo $data[3]; ?>">
											</td>
											<td align="center">
												<input type="text" style="width:80px" class="text_boxes_numeric"  name="txt_lay_balance_<? echo $i; ?>" id="txt_lay_balance_<? echo $i; ?>"  value="<? echo $lay_balance; ?>" disabled readonly />
											</td>
											<td align="center">
												<input type="text" style="width:50px" class="text_boxes_numeric" onKeyUp="copy_perc(<? echo $i; ?>);" name="txt_excess_<? echo $i; ?>" id="txt_excess_<? echo $i; ?>" value="<? echo $data[4]; ?>"/>
											</td>
											<td align="center">
												<input type="text" style="width:80px" class="text_boxes_numeric" name="txt_size_qty_<? echo $i; ?>" id="txt_size_qty_<? echo $i; ?>"  value="<? echo $data[1]; ?>" onKeyUp="calculate_perc(<? echo $i; ?>);" onBlur="check_size_qty(<? echo $i; ?>);" />
											</td>
										</tr>
										<?
										$i++;
									}
								}
							}
						}
                       ?>
                        </tbody>
                        <tfoot>
                            <tr class="form_table_header">
                                <th colspan="5" align="right">Total</th>
                                <th align="right"><? echo $total_lay_balance; ?>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th align="right" id="total_size_qty"><? echo $total_marker_qty; ?>&nbsp;</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
     	</form>
        <form name="searchorderfrm_2"  id="searchorderfrm_2" autocomplete="off">
            <br/>
            <div id="search_div" style="margin-top:10px">
                <?
				$sql_size_name=sql_select("select size_id from ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$details_id." and status_active=1 and is_deleted=0");
                $size_colour_arr=array();
                foreach($sql_size_name as $asf)
                {
                    $size_colour_arr[$asf[csf("size_id")]]=$size_arr[$asf[csf("size_id")]];
                }
                $i=1;
                $bundle_data=sql_select("select a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty,a.update_flag,a.update_value, a.country_type, a.country_id, a.roll_no, a.roll_id, a.pattern_no,a.barcode_no, a.is_excess, a.order_id from ppl_cut_lay_bundle a where a.mst_id=".$mst_id." and a.dtls_id=".$details_id." and a.status_active=1 and a.is_deleted=0 order by a.id ASC");
                if(count($bundle_data)>0)
                {
                ?>
                    <fieldset style="width:960px">
                        <legend>Bundle No and RMG qty details</legend>
                        <table cellpadding="0" cellspacing="0" width="950" rules="all" border="1" class="rpt_table" id="tbl_bundle_list_save">
                            <thead class="form_table_header">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th colspan="2">RMG Number</th>
                                <th>
                                    <input type="hidden" id="hidden_mst_id" name="hidden_mst_id" value="<? echo $mst_id; ?>" />
                                    <input type="hidden" id="hidden_detls_id" name="hidden_detls_id" value="<? echo $details_id; ?>" />
                                </th>
                                <th>Report &nbsp;</th>
                            </thead>
                            <thead class="form_table_header">
                                <th>SL No</th>
                                <th>Order No.</th>
                                <th>Country Type</th>
                                <th>Country Name</th>
                                <th>Size</th>
                                <th>Pattern</th>
                                <th>Roll No</th>
                                <th>Bundle No</th>
                                <th>Quantity</th>
                                <th>From</th>
                                <th>To</th>
                                <th></th>
                                <th width="40"><input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>
                            </thead>
                            <tbody id="trBundleListSave">
                            <?
                            foreach($bundle_data as $row)
                            {
                                $update_f_value="";
                                if(str_replace("'","",$row[csf('update_flag')])==1)
                                {
                                    $update_f_value=explode("**",$row[csf('update_value')]);
                                }
                            ?>
                                <tr id="trBundleListSave_<? echo $i;  ?>">
                                    <td align="center" id="">
                                        <input type="text" id="sirialNo_<? echo $i; ?>" name="sirialNo[]" style="width:25px;" class="text_boxes" value="<? echo $i; ?>" disabled/>
                                        <input type="hidden" id="hiddenExtraTr_<? echo $i;  ?>" name="hiddenExtraTr[]"  value="<? echo $i;  ?>" />
                                        <input type="hidden" id="hiddenUpdateFlag_<? echo $i;  ?>" name="hiddenUpdateFlag[]"  value="<? echo $row[csf('update_flag')];?> " />
                                        <input type="hidden" id="hiddenUpdateValue_<? echo $i;  ?>" name="hiddenUpdateValue[]"  value="<? echo $row[csf('update_value')];?> " />
                                    </td>
                                    <td align="center">
                                        <?
                                            echo create_drop_down( "cboPoId_".$i, 130, $po_no_arr,'', 0, '',$row[csf('order_id')],'',1,'','','','','','','cboPoId[]');
                                        ?>
                                    </td>
                                    <td align="center">
                                        <?
                                            echo create_drop_down( "cboCountryTypeB_".$i, 70, $country_type,'', 0, '',$row[csf('country_type')],'',1);
                                        ?>
                                         <input type="hidden" id="hiddenCountryTypeB_<? echo $i;  ?>" name="hiddenCountryTypeB[]"  value="<? echo $row[csf('country_type')];?> "/>
                                    </td>
                                    <td align="center">
                                        <?
                                            echo create_drop_down("cboCountryB_".$i,80,$po_country_array,'',1,'',$row[csf('country_id')],'',1,'','','','','','','cboCountryB[]');
                                        ?>
                                        <input type="hidden" id="hiddenCountryB_<? echo $i; ?>" name="hiddenCountryB[]" value="<? echo $row[csf('country_id')];?> " />
                                    </td>
                                    <td align="center" id="update_sizename_<? echo $i;  ?>">
                                        <select name="sizeName[]" id="sizeName_<? echo $i;  ?>" class="text_boxes" style="width:60px; text-align:center; <? if($update_f_value[1]!="") echo "background-color:#F3F;"; ?> "  disabled>
                                        <?
                                        // $l=1;
                                        foreach($sql_size_name as $asf)
                                        {
                                            if($asf[csf("size_id")]==$row[csf('size_id')]) $select_text="selected"; else $select_text="";
                                            ?>
                                                <option value="<? echo $asf[csf("size_id")]; ?>" <? echo $select_text;  ?>><? echo $size_arr[$asf[csf("size_id")]]; ?> </option>
                                            <?
                                        }
                                        ?>
                                        </select>
                                    	<input type="hidden" name="hiddenSizeId[]" id="hiddenSizeId_<? echo $i;  ?>" value="<? echo $row[csf('size_id')];  ?>" />
                                    </td>
                                    <td align="center"><input type="text" name="patternNo[]" id="patternNo_<? echo $i; ?>" value="<? echo $row[csf('pattern_no')]; ?>" class="text_boxes" style="width:35px; text-align:center" disabled/><input type="hidden" name="isExcess[]" id="isExcess_<? echo $i; ?>" value="<? echo $row[csf('is_excess')]; ?>"/></td>
                                    <td align="center">
                                    	<input type="text" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>" class="text_boxes" style="width:40px;  text-align:center" disabled/>
                                    	<input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
                                    </td>
                                    <td align="center" title="">
                                    	<input type="text" name="bundleNo[]" id="bundleNo_<?=$i;  ?>" value="<? echo $row[csf('bundle_no')];  ?>" class="text_boxes"  style="width:120px; text-align:center" disabled title="<?php echo $row[csf('barcode_no')]; ?>"/>
                                    </td>
                                    <td align="center">
                                    	<input type="text" name="bundleSizeQty[]" id="bundleSizeQty_<?=$i; ?>" onBlur="bundle_calclution(<?=$i; ?>)" value="<?=$row[csf('size_qty')]; ?>" style="width:40px; text-align:right; <? if($update_f_value[0]!="") echo "background-color:#F3F;"; ?>"  class="text_boxes"  disabled/>
                                    	<input type="hidden" name="hiddenSizeQty[]" id="hiddenSizeQty_<?=$i; ?>" value="<?=$row[csf('size_qty')]; ?>" disabled/>
                                    </td>
                                    <td align="center">
                                    	<input type="text" name="rmgNoStart[]" id="rmgNoStart_<?=$i;  ?>" value="<?=$row[csf('number_start')];  ?>" style="width:40px; text-align:right" class="text_boxes"  =disabled />
                                    </td>
                                    <td align="center">
                                    	<input type="text" name="rmgNoEnd[]" id="rmgNoEnd_<?=$i;  ?>" value="<?=$row[csf('number_end')];  ?>" style="width:40px; text-align:right" class="text_boxes"  disabled/>
                                    </td>
                                    <td align="center">
                                        <input type="button" value="+" name="addButton[]" id="addButton_<?=$i; ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_addRow('<?=$i;  ?>','<?=$i;  ?>')"/>
                                        <input type="button" value="Adj." name="rowUpdate[]" id="rowUpdate_<?=$i; ?>" style="width:40px;" class="formbuttonplasminus" onClick="fnc_updateRow('<?=$i;  ?>')"/>
                                    </td>
                                    <td align="center">
                                        <input id="chkbundle_<?=$i;  ?>" type="checkbox" name="chkbundle" >
                                        <input type="hidden" id="hiddenid_<?=$i; ?>" name="hiddenid_<?=$i; ?>" value="<? echo $row[csf('id')];  ?>" style="width:15px;" class="text_boxes"/>
                                    </td>
                                </tr>
                            <?
                            	$i++;
                            }
                            ?>
                            </tbody>
                        </table>
                        <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all">
                            <tr>
                                <td colspan="13" align="center" class="button_container">
                                    <? echo load_submit_buttons( $permission, "fnc_cut_lay_bundle_info", 1,0,"clear_size_form()",1);?>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                <?
                }
                ?>
            </div>
        </form>
	</div>
</body>
<script src="../../../includes/functions_bottom_noselect.js" type="text/javascript"></script>
<script>
	$('#cboCountryBundle').val(0);
</script>
</html>
<?
exit();
}

if($action=="rollSize_popup")
{
	echo load_html_head_contents("Roll Size Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$sizeDataArray=array();
	$size_datas=explode("|",$size_data);
	foreach($size_datas as $data)
	{
		$datas=explode("_",$data);
		$sizeDataArray[$datas[0]]=$datas[1];
	}
?>
	<script>

		function fnc_close()
		{
			parent.emailwindow.hide();
		}

    </script>

</head>

<body>
<div align="center" style="width:650px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:640px; margin-left:2px">
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="620">
                <thead>
                    <th width="60">Roll No</th>
                    <th width="70">Roll Wgt.</th>
                    <th width="60">Plies</th>
                    <?
					foreach($sizeDataArray as $key=>$value)
					{
						echo '<th>'.$size_arr[$key].'</th>';
					}
					?>
                </thead>
               <?
                   	$rollDatas=explode("**",$rollData); $allData='';
					foreach($rollDatas as $data)
					{
						$datas=explode("=",$data);
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$allData.=$data;
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td>
                            	<input type="text" id="rollNo_<? echo $i; ?>" name="rollNo[]" style="width:50px" class="text_boxes_numeric" value="<? echo $datas[1]; ?>">
                            	<input type="hidden" id="rollId_<? echo $i; ?>" name="rollId[]" value="<? echo $datas[2]; ?>">
                            </td>
                            <td><input type="text" id="rollWgt_<? echo $i; ?>" name="rollWgt[]" style="width:60px" class="text_boxes_numeric" value="<? echo $datas[3]; ?>"></td>
                            <td><input type="text" id="piles_<? echo $i; ?>" name="piles[]" style="width:50px" class="text_boxes_numeric" value="<? echo $datas[4]; ?>"></td>
                            <?
							foreach($sizeDataArray as $key=>$value)
							{
								$allData.="=".$value*$datas[4];
							?>
								<td align="center"><input type="text" id="sqty_<? echo $key."_".$i; ?>" name="sqty[]" style="width:50px" class="text_boxes_numeric" value="<? echo $value*$datas[4]; ?>"></td>
                            <?
							}
							$allData.="|";
							?>
                        </tr>
						<?
						$i++;
                    }
                    ?>
            </table>
            <table width="620">
                <tr>
                    <td align="center" >
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                        <input type="hidden" name="hidden_roll_data" id="hidden_roll_data" value="<? echo chop($allData,'|'); ?>"/>
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="save_update_delete_bundle")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==1)  // Insert Here=======================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$cutting_qc_no= return_field_value("cutting_qc_no","pro_gmts_cutting_qc_mst","status_active=1 and is_deleted=0 and cutting_no='".$hidden_cutting_no."'");
		//echo $cutting_qc_no;die;
		if($cutting_qc_no!="") { echo "200**".$cutting_qc_no; disconnect($con); die;}

		$previous_barcode_data=sql_select("select bundle_no,barcode_no,barcode_year,barcode_prifix from ppl_cut_lay_bundle where mst_id=".$bundle_mst_id."  and  dtls_id=".$bundle_dtls_id." and status_active=1 and is_deleted=0 ");
		foreach($previous_barcode_data as $b_val)
		{
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['year']=$b_val[csf("barcode_year")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['prifix']=$b_val[csf("barcode_prifix")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['barcode']=$b_val[csf("barcode_no")];
		}


		$id=return_next_id("id","ppl_cut_lay_bundle",1);


		$field_array="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,barcode_year,barcode_prifix,barcode_no,number_start,number_end,size_qty,update_flag,update_value,country_type,country_id,roll_id,roll_no,pattern_no,is_excess,order_id,color_type_id,inserted_by,insert_date,status_active,is_deleted";

		$year_id=date('Y',time());
		if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$barcode_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle","barcode_year=$year_id","suffix_no");

		for($j=1;$j<=$tot_row;$j++)
		{
			$new_bundle_no="txtBundleNo_".$j;
			$new_bundle_qty="txtBundleQty_".$j;
			$hidden_bundle_qty="hiddenSizeqty_".$j;
			$new_bundle_from="txtBundleFrom_".$j;
			$new_bundle_to="txtBundleTo_".$j;
			$new_bundle_size_id="txtSizeId_".$j;
			$new_update_flag="hiddenUpdateFlag_".$j;
			$hidden_size_id="txtHiddenSizeId_".$j;
			$new_update_value="hiddenUpdateValue_".$j;
			$hiddenCountry="cboCountry_".$j;
			$hiddenCountryType="hiddenCountryType_".$j;
			$rollId="rollId_".$j;
			$rollNo="rollNo_".$j;
			$patternNo="patternNo_".$j;
			$isExcess="isExcess_".$j;
			$cboPoId="cboPoId_".$j;
			$bundle_prif=explode("-",$$new_bundle_no);
			$new_bundle_prif_no=explode('-',$bundle_prif[3]);
			$new_bundle_prifix=$bundle_prif[0]."-".$bundle_prif[1]."-".$bundle_prif[2];
			$update_flag=0;
			$update_flag_value="";
			//echo $$new_update_flag."**".$$new_update_value;die;
			if(str_replace("'","",$$new_update_flag)!=1)
			{
				if(str_replace("'","",$$new_update_flag)==6)
				{
					if(trim($$hidden_bundle_qty)!=trim($$new_bundle_qty))
					{
						$update_flag_value="".str_replace("'","",$$hidden_bundle_qty)."";
						$update_flag=1;
					}
					else
					{
						$update_flag_value="";
					}
					if(trim($$hidden_size_id)!=trim($$new_bundle_size_id))
					{
						$update_flag_value.="**".str_replace("'","",$$new_bundle_size_id)."";
						$update_flag=1;
					}
					else
					{
						$update_flag_value.="**";
					}
				}
			}
			else
			{
				$update_flag=1;
				$update_flag_value=$$new_update_value;
			}

			if(empty($previous_barcode_arr[str_replace("'","",$$new_bundle_no)]))
			{
				$barcode_suffix_no=$barcode_suffix_no+1;
				$up_barcode_suffix=$barcode_suffix_no;
				$up_barcode_year=$year_id;
				$barcode_no=$year_id."509".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
			}
			else
			{
				$up_barcode_suffix=$previous_barcode_arr[str_replace("'","",$$new_bundle_no)]['prifix'];
				$up_barcode_year=$previous_barcode_arr[str_replace("'","",$$new_bundle_no)]['year'];
				$barcode_no=$previous_barcode_arr[str_replace("'","",$$new_bundle_no)]['barcode'];
			}


				//echo $update_flag_value."***";die;
			if($data_array!="") $data_array.=",";
			 $data_array.="(".$id.",".$bundle_mst_id.",".$bundle_dtls_id.",".$$new_bundle_size_id.",'".$new_bundle_prifix."','".$new_bundle_prif_no[0]."','".$$new_bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."','".$$new_bundle_from."','".$$new_bundle_to."','".$$new_bundle_qty."',".$update_flag.",'".$update_flag_value."','".str_replace("'","",$$hiddenCountryType)."','".str_replace("'","",$$hiddenCountry)."','".str_replace("'","",$$rollId)."','".str_replace("'","",$$rollNo)."','".str_replace("'","",$$patternNo)."','".str_replace("'","",$$isExcess)."','".str_replace("'","",$$cboPoId)."',".$color_type_id.",'".$user_id."','".$pc_date_time."',1,0)";
			$id = $id+1;
		}
		//echo $data_array;die;
		//echo "10**insert into ppl_cut_lay_bundle($field_array) values".$data_array;die;
		$rID=execute_query("delete from ppl_cut_lay_bundle where mst_id=".$bundle_mst_id." and dtls_id=".$bundle_dtls_id."",0);
		$rID1=sql_insert("ppl_cut_lay_bundle",$field_array,$data_array,1);
		//echo "10**".$rID.$rID1;die;
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");
				echo "0**".$bundle_mst_id."**".$bundle_dtls_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$bundle_mst_id."**".$bundle_dtls_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
			{
			    if($rID && $rID1)
					{
						oci_commit($con);
						echo "0**".$bundle_mst_id."**".$bundle_dtls_id;
					}
				else{
						oci_rollback($con);
						echo "10**".$bundle_mst_id."**".$bundle_dtls_id;
					}
			}

		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here=======================================================================
	{

	}
}
//----------------------------------bundle qty update finish---------------------------------------------------------------------------------


if($action=="report_bundle_printer")
{
	$data=explode("***",$data);
	$con = connect();
	if($db_type==0)	{ mysql_query("BEGIN"); }
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$color_sizeID_arr=sql_select( "select id,size_id,bundle_no,number_start,number_end,size_qty from ppl_cut_lay_bundle
	                              where mst_id=$data[1] and dtls_id=$data[2] order by id" );  //where id in ($data)
	$color_sizeID_arr=sql_select( "select id,size_id,bundle_no,number_start,number_end,size_qty from ppl_cut_lay_bundle where id in ( $data[0] ) " );  //where id in ($data)

	$sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a
	                      where a.job_no_mst=b.job_no and a.id=$data[5]");
    foreach($sql_name as $value)
	 {
		 $product_dept_name=$value[csf('product_dept')];
		 $style_name=$value[csf('style_ref_no')];
		 $buyer_name=$value[csf('buyer_name')];
		 $po_number=$value[csf('po_number')];
	 }
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[1]");
	 foreach($sql_cut_name as $cut_value)
	 {
		 $table_name=$table_no_library[$cut_value[csf('table_no')]];
	     $cut_date=$cut_value[csf('entry_date')];
         $cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		 $company_id=$cut_value[csf('company_id')];
		 $batch_no=$cut_value[csf('batch_id')];
		 $comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		 $new_cut_no=$comp_name."-".$cut_prifix;
		 $bundle_title="";
	 }
	 $bundle_calculate_id=return_next_id("id", "ppl_cut_lay_bundle_history",1);
	 $field_array_bundle="I1,I2,I3,I4,I5,I6,I7,I8,I9,I10";
	 $field_array="id,order_id,mst_id,detls_id,total_bundle,inserted_by,insert_date";
	 $data_array_print="";
	 $i=1;
	 foreach($color_sizeID_arr as $val)
	      {
			 $field1=$val[csf("bundle_no")];
			 $field2=$new_cut_no.",".$cut_date;
			 $field3=$buyer_library[$buyer_name].",".$po_number;
			 $field4=$style_name;
			 $field5=$garments_item[$data[3]];
			 $field6=$color_library[$data[4]];
			 $field7=$size_arr[$val[csf("size_id")]].",".$val[csf("bundle_no")];
			 $field8=$val[csf("size_qty")].",".$val[csf("number_start")]."-".$val[csf("number_end")];
			 $field9=$batch_no;
			 if(trim($data_array_print)!="") $data_array_print.=",";
			 $data_array_print.="('".$field1."','".$field2."','".$field3."','".$field4."','".$field5."','".$field6."','".$field7."','".$field8."','".$field9."','".$table_name."')";
			 $i++;
		 }
		 $total_bundle=$i-1;
		 $data_array="(".$bundle_calculate_id.",'".$data[5]."','".$data[1]."','".$data[2]."','".$total_bundle."','".$user_id."','".$pc_date_time."')";
		// echo $data_array;die;
		 $rID=sql_insert("ppl_cut_lay_bundle_history",$field_array,$data_array,1);
		 $rID1=sql_insert("LABEL_OUT",$field_array_bundle,$data_array_print,1);
		 if($db_type==0)
			 {
				if($rID && $rID1)
				   {
					mysql_query("COMMIT");
					echo 0;
					}
				else
				   {
					mysql_query("ROLLBACK");
					echo 10;
				   }
			 }
			if($db_type==2 || $db_type==1 )
			  {
				if($rID && $rID1)
				   {
					oci_commit($con);
					echo 0;
					}
				else
				   {
					oci_rollback($con);
					echo 10;
				   }
			  }
			disconnect($con);
			die;

}
//bundle_bar_code stiker****************************************************************************************************************************************************

if($action=="report_bundle_text_file")
{
	$data=explode("***",$data);
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$color_sizeID_arr=sql_select( "select id,size_id,bundle_no,number_start,number_end,size_qty from ppl_cut_lay_bundle where id in ( $data[0] ) " );  //where id in ($data)
    $bundle_array=array();
	 $sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	 foreach($sql_name as $value)
	 {
		 $product_dept_name=$value[csf('product_dept')];
		 $style_name=$value[csf('style_ref_no')];
		 $buyer_name=$value[csf('buyer_name')];
		 $po_number=$value[csf('po_number')];
	 }
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	 foreach($sql_cut_name as $cut_value)
	 {
		 $table_name=$cut_value[csf('table_no')];
	     $cut_date=change_date_format($cut_value[csf('entry_date')]);
         $cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		 $company_id=$cut_value[csf('company_id')];
		 $batch_no=$cut_value[csf('batch_id')];
		 $comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		 $new_cut_no=$comp_name."-".$cut_prifix;
		 $bundle_title="";

	 }
			foreach (glob(""."*.zip") as $filename)
			{
			@unlink($filename);
		    }
		    $i=1;
			$zip = new ZipArchive();			// Load zip library
			$filename = str_replace(".sql",".zip",'norsel_bundle.sql');			// Zip name
			if($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE)
			{		// Opening zip file to load files
				$error .=  "* Sorry ZIP creation failed at this time<br/>"; echo $error;
			}
			 $batch_number="";
			 if ($batch_no!="") $batch_number="(".$batch_no.")";
			foreach($color_sizeID_arr as $val)
			   {
						$file_name="NORSEL-IMPORT_".$i;
						$myfile = fopen($file_name.".txt", "w") or die("Unable to open file!");
						$txt ="Norsel_imp\r\n1\r\n";
						$txt .=$val[csf("bundle_no")]."\r\n";
						$txt .="Bundle: ".$val[csf("bundle_no")]."".$batch_number."\r\n";
						$txt .= "Cut No ".$new_cut_no.", ".$cut_date."\r\n";
						$txt .= $buyer_library[$buyer_name].", Ord: ". $po_number."\r\n";
						$txt .="Style ". $style_name."\r\n";
						$txt .=$garments_item[$data[4]]."\r\n";
						$txt .="Color ".trim($color_library[$data[5]])."\r\n";
						$txt .="Size ". $size_arr[$val[csf("size_id")]].", Table ".$table_no_library[$table_name]."\r\n";
						$txt .= "Gmts Qty. ".$val[csf("size_qty")];
						$txt .= ", SL No ".$val[csf("number_start")]."-".$val[csf("number_end")];


						fwrite($myfile, $txt);
						fclose($myfile);
					$i++;
				 }
				 foreach (glob(""."*.txt") as $filenames){
				   $zip->addFile($file_folder.$filenames);			// Adding files into zip
				}
			$zip->close();

	foreach (glob(""."*.txt") as $filename) {
			@unlink($filename);
		}
	echo "norsel_bundle";
	exit();
}

if($action=="cut_lay_qc_bundle_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$btnType=$data[5];
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name" );

	$company_library=array(); $company_short_arr=array();
	$comapny_data=sql_select("select id, company_short_name, company_name from lib_company");
	foreach($comapny_data as $comR)
	{
		$company_library[$comR[csf('id')]]=$comR[csf('company_name')];
		$company_short_arr[$comR[csf('id')]]=$comR[csf('company_short_name')];
	}

	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$buyer_library=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$working_comp_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$working_location_library=return_library_array("select id, location_name from lib_location", "id", "location_name");
	$working_floor_library=return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	$table_no_library=return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$countryCodeArr=return_library_array("select id, ultimate_country_code from lib_country_loc_mapping", "id", "ultimate_country_code");



	$sql="select a.cut_num_prefix_no, a.table_no,a.cutting_no, a.job_no, a.entry_date,a.marker_length, a.marker_width, a.fabric_width, a.gsm, a.batch_id as batch_no,a.location_id,a.floor_id,a.working_company_id, b.order_id, b.color_id, b.gmt_item_id, b.plies, b.marker_qty, b.order_qty, b.order_ids, b.batch_id, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id='$data[1]' and a.company_id='$data[0]' and b.id='$data[2]' ";
	// echo $sql;
	$dataArray=sql_select($sql);
	$sql_buyer=sql_select("select buyer_name,style_ref_no as ref,style_description as des from wo_po_details_master where job_no='".$dataArray[0][csf('job_no')]."' and company_name=$data[0]");
	$style_ref=$sql_buyer[0][csf('ref')];
	$style_desc=$sql_buyer[0][csf('des')];
	$cut_no_prifix=$dataArray[0][csf('cut_num_prefix_no')];
	$cut_no=$dataArray[0][csf('cutting_no')];
	$order_cut_no=$dataArray[0][csf('order_cut_no')];

	$po_arr=return_library_array( "select id, po_number from wo_po_break_down where id in(".$dataArray[0][csf('order_ids')].")","id","po_number" );
	$batch_no=$dataArray[0][csf('batch_no')];

	$poCodeIdArr=array();

	$sqlCode="Select po_break_down_id, country_id, size_number_id, code_id from wo_po_color_size_breakdown where po_break_down_id in (".$dataArray[0][csf('order_ids')].") and status_active=1 and is_deleted=0 ";
	$sqlCodeData=sql_select($sqlCode);
	foreach($sqlCodeData as $crow)
	{
		$poCodeIdArr[$crow[csf('po_break_down_id')]][$crow[csf('country_id')]][$crow[csf('size_number_id')]]=$countryCodeArr[$crow[csf('code_id')]];
	}
	unset($sqlCodeData);
	if($btnType==1) { $tblwidth="1200"; $totTrSpan="8"; }
	else if($btnType==2) { $tblwidth="1340"; $totTrSpan="10"; }
	?>
	<div style="width:1000px; " align="center" >
    <table width="990" cellspacing="0" align="center">
         <tr>
            <td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:16px"><strong><u>Cut. Panel Inspection Report</u></strong></td>
        </tr>
         <tr>
        	<td width="130"><strong>Cut No:</strong></td><td width="200"><? echo $cut_no; ?></td>
            <td width="130"><strong>Table No :</strong></td> <td width="200"><?  echo $table_no_library[$dataArray[0][csf('table_no')]]; ?></td>
            <td width="130"><strong>Job No :</strong></td> <td><? echo $dataArray[0][csf('job_no')]; ?></td>
        </tr>
         <tr>
			<td><strong>Buyer:</strong></td><td><? echo $buyer_library[$sql_buyer[0][csf('buyer_name')]]; ?></td>
            <td><strong>Batch No:</strong></td><td><? echo $batch_no; ?></td>
        </tr>
        <tr>
			 <td><strong>Gmt Item:</strong></td> <td><? echo $garments_item[$dataArray[0][csf('gmt_item_id')]]; ?></td>
             <td><strong>Color :</strong></td><td><? echo $color_library[$dataArray[0][csf('color_id')]]; ?></td>
             <td><strong>Marker Length :</strong></td><td><? echo $dataArray[0][csf('marker_length')]; ?></td>
        </tr>
        <tr>
            <td><strong>Marker Width :</strong></td><td><? echo $dataArray[0][csf('marker_width')]; ?></td>
            <td><strong>Fabric Width:</strong></td><td><? echo $dataArray[0][csf('fabric_width')]; ?></td>
            <td></td><td></td>
        </tr>
        <tr>
             <td><strong>Order Cut No:</strong></td> <td><? echo $order_cut_no; ?></td>
             <td><strong>Plies:</strong></td> <td><? echo $dataArray[0][csf('plies')]; ?></td>
             <td><strong>Cut Date:</strong></td><td><? echo $dataArray[0][csf('entry_date')]; ?></td>
        </tr>
        <tr>
       		 <td><strong>Style Ref:</strong></td><td><? echo $style_ref; ?></td>
             <td><strong>Style Desc.:</strong></td><td><? echo $style_desc; ?></td>
             <td align="left" colspan="2" id="barcode_img_id"></td>
        </tr>
        <tr>
       		 <td><strong>W. Company:</strong></td> <td><? echo $working_comp_library[$dataArray[0][csf('working_company_id')]]; ?></td>
             <td><strong>W. Location:</strong></td> <td><? echo $working_location_library[$dataArray[0][csf('location_id')]]; ?></td>
             <td><strong>W. Floor:</strong></td> <td><? echo $working_floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
        </tr>
        <tr>
       		 <td><strong>Cutting Part:</strong></td> <td  colspan="5"><? echo $data[5]; ?></td>
        </tr>
    </table>
    <br>
     <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

			function generateBarcode( valuess ){

					var value = valuess;
					var btype = 'code39';
					var renderer ='bmp';
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
					 value = {code:value, rect: false};
					$("#barcode_img_id").show().barcode(value, btype, settings);
				}
			   generateBarcode('<? echo $dataArray[0][csf('cutting_no')]; ?>');
	 </script>
	<div style="width:<?=$tblwidth; ?>px;">
    	<table align="center" cellspacing="0" width="<?=$tblwidth-20; ?>" border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
            	<tr>
					<th width="30" rowspan="3">SL</th>
					<th width="100" rowspan="3">Cut No</th>
					<th width="110" rowspan="3">Order No</th>
					<th width="100" rowspan="3">Country</th>
					<th width="70" rowspan="3">Pattern No</th>
					<th width="60" rowspan="3">Roll No</th>
					<th width="60" rowspan="3">Shade no</th>
					<th width="60" rowspan="3">Bundle No</th>
					<th width="80" rowspan="3">Barcode</th>
					<th width="70" rowspan="3">Bundle Qty.</th>

					<th colspan="2">RMG Number</th>
					<th colspan="4">QC</th>
					<th width="120" rowspan="3">Remarks</th>
            	</tr>
              	<tr bgcolor="#dddddd" align="center">
					<th width="70" rowspan="2">From</th>
					<th width="70" rowspan="2">To</th>
					<th width="80" rowspan="2">Size</th>
					<th colspan="2">REJ</th>
					<th width="40" rowspan="2">REP</th>
                </tr>
				<tr bgcolor="#dddddd" align="center">
					<th>Front</th>
					<th>Back</th>
                </tr>
            </thead>
            <tbody>
                <?
					 $batchShadeNo_arr=array();

					 $sqlRoll=sql_select("select a.id, a.batch_no, a.shade from pro_roll_details a, ppl_cut_lay_bundle b, ppl_cut_lay_mst c where a.id=b.roll_id and a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.entry_form=c.entry_form");
					//  var_dump($sqlRoll);
					 foreach($sqlRoll as $rrow)
					 {
						 $batchShadeNo_arr[$rrow[csf('id')]]['batch']=$rrow[csf('batch_no')];
						 $batchShadeNo_arr[$rrow[csf('id')]]['shade']=$rrow[csf('shade')];
					 }

					 if($data[4]==0) $country_cond=""; else $country_cond=" and a.country_id='".$data[4]."'";
                     $size_data=sql_select("select a.id,a.size_id,a.bundle_sequence,a.marker_qty from ppl_cut_lay_size_dtls a where a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.bundle_sequence ASC");
                     $j=1;
                     foreach($size_data as $size_val)
                     {
						$total_marker_qty_size=0;
						   $bundle_data=sql_select("select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,a.barcode_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC");
					    //var_dump ($bundle_data);
						//echo "select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC";
                        foreach($bundle_data as $row)
                        {
               	 			?>
                           <tr>
                               <td align="center"><? echo $j;  ?></td>
                               <td align="center"><? echo $cut_no; ?></td>
                               <td style="word-wrap:break-word"><? echo $po_arr[$row[csf('order_id')]]; ?></td>
                               <td style="word-wrap:break-word"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                               <td align="center"><? echo $row[csf('pattern_no')]; ?></td>
                               <td align="center"><? echo $row[csf('roll_no')]; ?></td>
                               <td align="center" style="word-wrap:break-word"><?=$batchShadeNo_arr[$row[csf('roll_id')]]['shade']; ?></td>
                               <td align="center"><? echo $row[csf('bundle_num_prefix_no')]; //$row[csf('bundle_num_prefix_no')];  ?></td>
                               <td align="center"><? echo $row[csf('barcode_no')];?></td>
                               <td align="center"><? echo $row[csf('size_qty')];  ?></td>
                               <td align="center"><? echo $row[csf('number_start')];  ?></td>
                               <td align="center"><? echo $row[csf('number_end')];  ?></td>
                               <td align="center"><? echo $size_arr[$row[csf('size_id')]];  ?></td>
                               <td align="center">&nbsp;</td>
                               <td align="center">&nbsp;</td>
                               <td align="center">&nbsp;</td>
							   <td align="center">&nbsp;</td>
                          </tr>
               	 			<?
                           $j++;
						   $total_marker_qty_size+=$row[csf('size_qty')];
						   $total_marker_qty+=$row[csf('size_qty')];
                         }
                       //  $total_marker_qty+=$size_val[csf('marker_qty')];
                		?>
                        <tr bgcolor="#eeeeee">
                           <td align="center"></td>
                           <td  colspan="<?=$totTrSpan; ?>" align="right"><? echo $size_arr[$row[csf('size_id')]]; ?> Size Total</td>
                           <td align="center"><? echo $total_marker_qty_size;  ?></td>
                           <td align="center">&nbsp;</td>
                           <td align="center">&nbsp;</td>
                           <td align="center">&nbsp;</td>
                           <td align="center">&nbsp;</td>
                           <td align="center">&nbsp;</td>
                           <td align="center">&nbsp;</td>
                        </tr>
                <?
                     }
                ?>
               <tr bgcolor="#BBBBBB">
                   <td align="center"></td>
                   <td  colspan="<?=$totTrSpan; ?>" align="right"> Total marker qty.</td>
                   <td align="center"><? echo $total_marker_qty;  ?></td>
                   <td align="center">&nbsp;</td>
                   <td align="center">&nbsp;</td>
                   <td align="center">&nbsp;</td>
                   <td align="center">&nbsp;</td>
                   <td align="center">&nbsp;</td>
                   <td align="center">&nbsp;</td>
                </tr>
			</tbody>
		</table>
        <br>
		<? echo signature_table(221, $data[0], "900px"); ?>
		</div>
	</div>
	<?
	exit();
}

if($action=="cut_lay_qc_bundle_print2") //QC Bundle 2
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name" );

	$company_library=array(); $company_short_arr=array();
	$comapny_data=sql_select("select id, company_short_name, company_name from lib_company");
	foreach($comapny_data as $comR)
	{
		$company_library[$comR[csf('id')]]=$comR[csf('company_name')];
		$company_short_arr[$comR[csf('id')]]=$comR[csf('company_short_name')];
	}

	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$buyer_library=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$working_comp_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$working_location_library=return_library_array("select id, location_name from lib_location", "id", "location_name");
	$working_floor_library=return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	$table_no_library=return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$countryCodeArr=return_library_array("select id, ultimate_country_code from lib_country_loc_mapping", "id", "ultimate_country_code");

	// echo "<pre>"; print_r($table_no_library); die;

	$sql="SELECT a.cut_num_prefix_no, a.table_no,a.cutting_no, a.job_no, a.entry_date,a.marker_length, a.marker_width, a.fabric_width, a.gsm, a.batch_id as batch_no,a.location_id,a.floor_id,a.working_company_id, b.order_id, b.color_id, b.gmt_item_id, b.plies, b.marker_qty, b.order_qty, b.order_ids, b.batch_id, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id='$data[1]' and a.company_id='$data[0]' and b.id='$data[2]' ";
	// echo $sql;
	$dataArray=sql_select($sql);
	// echo "<pre>"; print_r($dataArray); die;
	$sql_buyer=sql_select("select buyer_name,style_ref_no as ref,style_description as des from wo_po_details_master where job_no='".$dataArray[0][csf('job_no')]."' and company_name=$data[0]");
	$style_ref=$sql_buyer[0][csf('ref')];
	$style_desc=$sql_buyer[0][csf('des')];
	$cut_no_prifix=$dataArray[0][csf('cut_num_prefix_no')];
	$cut_no=$dataArray[0][csf('cutting_no')];
	$order_cut_no=$dataArray[0][csf('order_cut_no')];

	$po_arr=return_library_array( "select id, po_number from wo_po_break_down where id in(".$dataArray[0][csf('order_ids')].")","id","po_number" );
	$sql_int_booking=sql_select("SELECT listagg(distinct cast(GROUPING AS varchar(4000)),',') within group(ORDER BY GROUPING) AS INT_BOOKING FROM WO_PO_BREAK_DOWN WHERE ID IN(".$dataArray[0][csf('order_ids')].")");
	$int_booking=$sql_int_booking[0]['INT_BOOKING'];
	// echo $int_booking; die;

	$batch_no=$dataArray[0][csf('batch_no')];

	$poCodeIdArr=array();

	$sqlCode="Select po_break_down_id, country_id, size_number_id, code_id from wo_po_color_size_breakdown where po_break_down_id in (".$dataArray[0][csf('order_ids')].") and status_active=1 and is_deleted=0 ";
	$sqlCodeData=sql_select($sqlCode);
	foreach($sqlCodeData as $crow)
	{
		$poCodeIdArr[$crow[csf('po_break_down_id')]][$crow[csf('country_id')]][$crow[csf('size_number_id')]]=$countryCodeArr[$crow[csf('code_id')]];
	}
	unset($sqlCodeData);
	?>
	<div style="width:1120px; " align="center" >
		<table width="1100" cellspacing="0" align="center">
			<tr>
				<td colspan="2" width="367">&nbsp;</td>
				<td colspan="2" width="366" align="center" style="font-size:20px"><strong><? echo  $working_comp_library[$dataArray[0][csf('working_company_id')]]; ?></strong></td>
				<td align="right" width="367" colspan="2" id="barcode_img_id"></td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u>Cut. Panel Inspection Report</u></strong></td>
			</tr>
			<tr>
				<td><strong>Buyer:</strong></td><td><? echo $buyer_library[$sql_buyer[0][csf('buyer_name')]]; ?></td>
				<td><strong>Company:</strong></td> <td><? echo $company_library[$data[0]]; ?></td>
				<td><strong>Cutting Date:</strong></td><td><? echo $dataArray[0][csf('entry_date')]; ?></td>
			</tr>
			<tr>
				<td width="130"><strong>Int. Booking:</strong></td><td width="200"><? echo $int_booking; ?></td>
				<td width="130"><strong>S. Cutting No:</strong></td><td width="200"><? echo $cut_no; ?></td>
				<td><strong>Marker Length :</strong></td><td><? echo $dataArray[0][csf('marker_length')]; ?></td>
			</tr>
			<tr>
				<td><strong>Style:</strong></td><td><? echo $style_ref; ?></td>
				<td><strong>Gmt Item:</strong></td> <td><? echo $garments_item[$dataArray[0][csf('gmt_item_id')]]; ?></td>
				<td><strong>Marker Width :</strong></td><td><? echo $dataArray[0][csf('marker_width')]; ?></td>
			</tr>
			<tr>
				<td><strong>C. Floor:</strong></td> <td><? echo $working_floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
				<td><strong>Color :</strong></td><td><? echo $color_library[$dataArray[0][csf('color_id')]]; ?></td>
				<td><strong>Batch No:</strong></td><td><? echo $batch_no; ?></td>
			</tr>
			<tr>
				<td width="130"><strong>Table No :</strong></td> <td width="200"><?  echo $table_no_library[$dataArray[0][csf('table_no')]]; ?></td>
				<td><strong>Order Cut No:</strong></td> <td><? echo $order_cut_no; ?></td>
				<td><strong>Plies:</strong></td> <td><? echo $dataArray[0][csf('plies')]; ?></td>
			</tr>
		</table>
		<br>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess)
			{

				var value = valuess;
				var btype = 'code39';
				var renderer ='bmp';
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
				value = {code:value, rect: false};
				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $dataArray[0][csf('cutting_no')]; ?>');
		</script>
		<div style="width:1120px;">
			<table align="center" cellspacing="0" width="1100" border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30" rowspan="3">SL</th>
						<th width="90" rowspan="3">Order No</th>
						<th width="100" rowspan="3">Country</th>
						<th width="70" rowspan="3">Pattern No</th>
						<th width="100" rowspan="3">Barcode</th>
						<th width="60" rowspan="3">Bundle No</th>
						<th width="60" rowspan="3">Bundle Qty.</th>

						<th colspan="2">RMG Number</th>
						<th colspan="4">QC</th>
						<th width="100" rowspan="3">Defect Name- Front</th>
						<th width="100" rowspan="3">Defect Name- Back</th>
					</tr>
					<tr bgcolor="#dddddd" align="center">
						<th width="60" rowspan="2">From</th>
						<th width="60" rowspan="2">To</th>
						<th width="70" rowspan="2">Size</th>
						<th colspan="2">REJ</th>
						<th width="40" rowspan="2">REP</th>
					</tr>
					<tr bgcolor="#dddddd" align="center">
						<th width="60">Front</th>
						<th width="60">Back</th>
					</tr>
				</thead>
				<tbody>
					<?
					$batchShadeNo_arr=array();

					$sqlRoll=sql_select("SELECT a.id, a.batch_no, a.shade from pro_roll_details a, ppl_cut_lay_bundle b, ppl_cut_lay_mst c where a.id=b.roll_id and b.mst_id=c.id and a.mst_id=c.id and a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.entry_form=509");
					//  var_dump($sqlRoll);
					foreach($sqlRoll as $rrow)
					{
						$batchShadeNo_arr[$rrow[csf('id')]]['batch']=$rrow[csf('batch_no')];
						$batchShadeNo_arr[$rrow[csf('id')]]['shade']=$rrow[csf('shade')];
					}

					if($data[4]==0) $country_cond=""; else $country_cond=" and a.country_id='".$data[4]."'";
					$size_data=sql_select("SELECT a.id,a.size_id,a.bundle_sequence,a.marker_qty from ppl_cut_lay_size_dtls a where a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.bundle_sequence ASC");
					$j=1;
					foreach($size_data as $size_val)
					{
						$total_marker_qty_size=0;
						$bundle_data=sql_select("SELECT a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,a.barcode_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC");
						//var_dump ($bundle_data);
						//echo "select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC";
						// echo "<pre>"; print_r($bundle_data); die;
						foreach($bundle_data as $row)
						{
							?>
							<tr>
								<td align="center"><? echo $j;  ?></td>
								<td style="word-wrap:break-word"><? echo $po_arr[$row[csf('order_id')]]; ?></td>
								<td style="word-wrap:break-word"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
								<td align="center"><? echo $row[csf('pattern_no')]; ?></td>
								<td align="center"><? echo $row[csf('barcode_no')];?></td>
								<td align="center"><? echo $row[csf('bundle_num_prefix_no')]; //$row[csf('bundle_num_prefix_no')];  ?></td>
								<td align="center"><? echo $row[csf('size_qty')];  ?></td>
								<td align="center"><? echo $row[csf('number_start')];  ?></td>
								<td align="center"><? echo $row[csf('number_end')];  ?></td>
								<td align="center"><? echo $size_arr[$row[csf('size_id')]];  ?></td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
							</tr>
							<?
							$j++;
							$total_marker_qty+=$row[csf('size_qty')];
						}
					}
					?>
					<tr bgcolor="#eeeeee">
						<td align="center"></td>
						<td  colspan="4" align="right">Gross Total</td>
						<td align="center"></td>
						<td align="center"><? echo $total_marker_qty;  ?></td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
					</tr>
				</tbody>
			</table>
			<br>
			<table width="1050" cellspacing="0" align="center">
				<tr>
					<th align="left" width="900"><h3><u> Inspection by</u></h3></th>
					<th align="left" width="150"><h3><u> Replace Cut by</u></h3></th>
				</tr>
				<tr>
					<td>
						<p>01 ............................... </p>
						<p>02 ............................... </p>
						<p>03 ............................... </p>
					</td>
					<td>
						<p>01 ............................... </p>
						<p>02 ............................... </p>
						<p>03 ............................... </p>
					</td>
				</tr>
			</table>
			<? //echo signature_table(221, $data[0], "900px"); ?>
		</div>
	</div>
	<?
	exit();
}
if($action=="cut_lay_qc_bundle_print3") //QC Bundle 3
{
	// echo "hello"; die;
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name" );

	$company_library=array(); $company_short_arr=array();
	$comapny_data=sql_select("select id, company_short_name, company_name from lib_company");
	foreach($comapny_data as $comR)
	{
		$company_library[$comR[csf('id')]]=$comR[csf('company_name')];
		$company_short_arr[$comR[csf('id')]]=$comR[csf('company_short_name')];
	}

	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$buyer_library=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$working_comp_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$working_location_library=return_library_array("select id, location_name from lib_location", "id", "location_name");
	$working_floor_library=return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	$table_no_library=return_library_array("select id,table_no  from  lib_cutting_table", "id", "table_no");
	$countryCodeArr=return_library_array("select id, ultimate_country_code from lib_country_loc_mapping", "id", "ultimate_country_code");

	// echo "<pre>"; print_r($table_no_library); die;

	$sql="SELECT a.cut_num_prefix_no, a.table_no,a.cutting_no, a.job_no, a.entry_date,a.marker_length, a.marker_width, a.fabric_width, a.gsm, a.batch_id as batch_no,a.location_id,a.floor_id,a.working_company_id, b.order_id, b.color_id, b.gmt_item_id, b.plies, b.marker_qty, b.order_qty, b.order_ids, b.batch_id, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id=b.mst_id and a.id='$data[1]' and a.company_id='$data[0]' and b.id='$data[2]' ";
	// echo $sql;
	$dataArray=sql_select($sql);
	// echo "<pre>"; print_r($dataArray); die;
	$sql_buyer=sql_select("select buyer_name,style_ref_no as ref,style_description as des from wo_po_details_master where job_no='".$dataArray[0][csf('job_no')]."' and company_name=$data[0]");
	$style_ref=$sql_buyer[0][csf('ref')];
	$style_desc=$sql_buyer[0][csf('des')];
	$cut_no_prifix=$dataArray[0][csf('cut_num_prefix_no')];
	$cut_no=$dataArray[0][csf('cutting_no')];
	$order_cut_no=$dataArray[0][csf('order_cut_no')];

	$po_arr=return_library_array( "select id, po_number from wo_po_break_down where id in(".$dataArray[0][csf('order_ids')].")","id","po_number" );
	$sql_int_booking=sql_select("SELECT listagg(distinct cast(GROUPING AS varchar(4000)),',') within group(ORDER BY GROUPING) AS INT_BOOKING FROM WO_PO_BREAK_DOWN WHERE ID IN(".$dataArray[0][csf('order_ids')].")");
	$int_booking=$sql_int_booking[0]['INT_BOOKING'];
	// echo $int_booking; die;

	$batch_no=$dataArray[0][csf('batch_no')];

	$poCodeIdArr=array();

	$sqlCode="Select po_break_down_id, country_id, size_number_id, code_id from wo_po_color_size_breakdown where po_break_down_id in (".$dataArray[0][csf('order_ids')].") and status_active=1 and is_deleted=0 ";
	$sqlCodeData=sql_select($sqlCode);
	foreach($sqlCodeData as $crow)
	{
		$poCodeIdArr[$crow[csf('po_break_down_id')]][$crow[csf('country_id')]][$crow[csf('size_number_id')]]=$countryCodeArr[$crow[csf('code_id')]];
	}
	unset($sqlCodeData);
	?>
	<div style="width:1240px; " align="center" >
		<table width="1220" cellspacing="0" align="center">
			<tr>
				<td colspan="2" width="407">&nbsp;</td>
				<td colspan="2" width="406" align="center" style="font-size:20px"><strong><? echo $working_comp_library[$dataArray[0][csf('working_company_id')]]; ?></strong></td>
				<td align="right" width="407" colspan="2" id="barcode_img_id"></td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:16px"><strong><u>Cut. Panel Inspection Report</u></strong></td>
			</tr>
			<tr>
				<td><strong>Buyer:</strong></td><td><? echo $buyer_library[$sql_buyer[0][csf('buyer_name')]]; ?></td>
				<td><strong>Company:</strong></td> <td><? echo $company_library[$data[0]] ; ?></td>
				<td><strong>Cutting Date:</strong></td><td><? echo $dataArray[0][csf('entry_date')]; ?></td>
			</tr>
			<tr>
				<td width="130"><strong>Int. Booking:</strong></td><td width="200"><? echo $int_booking; ?></td>
				<td width="130"><strong>S. Cutting No:</strong></td><td width="200"><? echo $cut_no; ?></td>
				<td><strong>Marker Length :</strong></td><td><? echo $dataArray[0][csf('marker_length')]; ?></td>
			</tr>
			<tr>
				<td><strong>Style:</strong></td><td><? echo $style_ref; ?></td>
				<td><strong>Gmt Item:</strong></td> <td><? echo $garments_item[$dataArray[0][csf('gmt_item_id')]]; ?></td>
				<td><strong>Marker Width :</strong></td><td><? echo $dataArray[0][csf('marker_width')]; ?></td>
			</tr>
			<tr>
				<td><strong>C. Floor:</strong></td> <td><? echo $working_floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
				<td><strong>Color :</strong></td><td><? echo $color_library[$dataArray[0][csf('color_id')]]; ?></td>
				<td><strong>Batch No:</strong></td><td><? echo $batch_no; ?></td>
			</tr>
			<tr>
				<td width="130"><strong>Table No :</strong></td> <td width="200"><?  echo $table_no_library[$dataArray[0][csf('table_no')]]; ?></td>
				<td><strong>Order Cut No:</strong></td> <td><? echo $order_cut_no; ?></td>
				<td><strong>Plies:</strong></td> <td><? echo $dataArray[0][csf('plies')]; ?></td>
			</tr>
		</table>
		<br>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess)
			{

				var value = valuess;
				var btype = 'code39';
				var renderer ='bmp';
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
				value = {code:value, rect: false};
				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $dataArray[0][csf('cutting_no')]; ?>');
		</script>
		<div style="width:1240px;">
			<table align="center" cellspacing="0" width="1220" border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<tr>
						<th width="30" rowspan="3">SL</th>
						<th width="90" rowspan="3">Order No</th>
						<th width="100" rowspan="3">Country</th>
						<th width="70" rowspan="3">Pattern No</th>
						<th width="100" rowspan="3">Barcode</th>
						<th width="60" rowspan="3">Bundle No</th>
						<th width="60" rowspan="3">Bundle Qty.</th>

						<th colspan="2">RMG Number</th>
						<th colspan="6">QC</th>
						<th width="100" rowspan="3">Defect Name- Front</th>
						<th width="100" rowspan="3">Defect Name- Back</th>
					</tr>
					<tr bgcolor="#dddddd" align="center">
						<th width="60" rowspan="2">From</th>
						<th width="60" rowspan="2">To</th>
						<th width="70" rowspan="2">Size</th>
						<th colspan="2">REJ</th>
						<th colspan="2">REJ</th>
						<th width="40" rowspan="2">REP</th>
					</tr>
					<tr bgcolor="#dddddd" align="center">
						<th width="60">Front-L</th>
						<th width="60">Front-R</th>
						<th width="60">Back-L</th>
						<th width="60">Back-R</th>
					</tr>
				</thead>
				<tbody>
					<?
					$batchShadeNo_arr=array();

					$sqlRoll=sql_select("SELECT a.id, a.batch_no, a.shade from pro_roll_details a, ppl_cut_lay_bundle b, ppl_cut_lay_mst c where a.id=b.roll_id and b.mst_id=c.id and a.mst_id=c.id and a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.entry_form=509");
					//  var_dump($sqlRoll);
					foreach($sqlRoll as $rrow)
					{
						$batchShadeNo_arr[$rrow[csf('id')]]['batch']=$rrow[csf('batch_no')];
						$batchShadeNo_arr[$rrow[csf('id')]]['shade']=$rrow[csf('shade')];
					}

					if($data[4]==0) $country_cond=""; else $country_cond=" and a.country_id='".$data[4]."'";
					$size_data=sql_select("SELECT a.id,a.size_id,a.bundle_sequence,a.marker_qty from ppl_cut_lay_size_dtls a where a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.bundle_sequence ASC");
					$j=1;
					foreach($size_data as $size_val)
					{
						$total_marker_qty_size=0;
						$bundle_data=sql_select("SELECT a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,a.barcode_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC");
						//var_dump ($bundle_data);
						//echo "select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC";
						// echo "<pre>"; print_r($bundle_data); die;
						foreach($bundle_data as $row)
						{
							?>
							<tr>
								<td align="center"><? echo $j;  ?></td>
								<td style="word-wrap:break-word"><? echo $po_arr[$row[csf('order_id')]]; ?></td>
								<td style="word-wrap:break-word"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
								<td align="center"><? echo $row[csf('pattern_no')]; ?></td>
								<td align="center"><? echo $row[csf('barcode_no')];?></td>
								<td align="center"><? echo $row[csf('bundle_num_prefix_no')]; //$row[csf('bundle_num_prefix_no')];  ?></td>
								<td align="center"><? echo $row[csf('size_qty')];  ?></td>
								<td align="center"><? echo $row[csf('number_start')];  ?></td>
								<td align="center"><? echo $row[csf('number_end')];  ?></td>
								<td align="center"><? echo $size_arr[$row[csf('size_id')]];  ?></td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
							</tr>
							<?
							$j++;
							$total_marker_qty+=$row[csf('size_qty')];
						}
						//  $total_marker_qty+=$size_val[csf('marker_qty')];
					}
					?>
					<tr bgcolor="#eeeeee">
						<td align="center"></td>
						<td  colspan="4" align="right">Gross Total</td>
						<td align="center"></td>
						<td align="center"><? echo $total_marker_qty;  ?></td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
						<td align="center">&nbsp;</td>
					</tr>
				</tbody>
			</table>
			<br>
			<table width="1170" cellspacing="0" align="center">
				<tr>
					<th align="left" width="1000"><h3><u> Inspection by</u></h3></th>
					<th align="left" width="170"><h3><u> Replace Cut by</u></h3></th>
				</tr>
				<tr>
					<td>
						<p>01 ............................... </p>
						<p>02 ............................... </p>
					</td>
					<td>
						<p>01 ............................... </p>
						<p>02 ............................... </p>
					</td>
				</tr>
			</table>
			<? //echo signature_table(221, $data[0], "900px"); ?>
		</div>
	</div>
	<?
	exit();
}


if($action=="print_report_bundle_barcode_eight")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
	$data_all=$data;
	$data=explode("***",$data);
	// echo $data[1];die;
	//  print_r($data);
	?>
      <script>

		var selected_id = new Array;
		var selected_name = new Array;

    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#td_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );

	}

	function fn_onClosed()
	{
		var print = '';
		$('#txt_selected_print').val('');
		$("input[name=print]").each(function(index, element)
		{
			if( $(this).prop('checked')==true)
			{
				print += (print=="") ? $(this).val() : ","+$(this).val();
			}
		});
		// alert(print);return;
		$('#txt_selected_print').val(print);
		// ===========================================
		var emb = '';
		$('#txt_selected_emb').val('');
		$("input[name=emb]").each(function(index, element)
		{
			if( $(this).prop('checked')==true)
			{
				emb += (emb=="") ? $(this).val() : ","+$(this).val();
			}
		});
		$('#txt_selected_emb').val(emb);
		// alert(print);return;

		parent.emailwindow.hide();
	}
	</script>
    <?
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	 foreach($sql_cut_name as $cut_value)
	 {
		 $table_name=$cut_value[csf('table_no')];
	     $cut_date=change_date_format($cut_value[csf('entry_date')]);
         $cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		 $company_id=$cut_value[csf('company_id')];
		 $batch_no=$cut_value[csf('batch_id')];
		 $comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		 $new_cut_no=$comp_name."-".$cut_prifix;
		 $bundle_title="";
	 }

	 $yes_no_query="SELECT b.company_name,b.variable_list,b.finish_rate_come_from from variable_settings_production b WHERE b.variable_list=80 and b.company_name= $company_id ";
	//   echo $yes_no_query;die();
	 $yes_no=sql_select($yes_no_query);
	 $yes_no_arr=array();
	 foreach($yes_no as $val)
	 {
		   $finish_rate_come_from=$val[csf('finish_rate_come_from')];
	 }

	 if($finish_rate_come_from==1)
	 {
		$style_wise_body_part_sql="SELECT id,body_part_ids from style_wise_body_part_mst where company_name=$company_id and job_no= '$data[1]'  and status_active=1 and is_deleted=0";
		// echo $style_wise_body_part_sql;
		$style_wise_body_part_sql_res = sql_select($style_wise_body_part_sql);

		if (count($style_wise_body_part_sql_res) > 0) {
			$body_part_ids = '';
			foreach ($style_wise_body_part_sql_res as   $v) {
				$body_part_ids .= $v['BODY_PART_IDS'] . ',';
				// echo $body_part_ids;
			}
			$body_part_ids = rtrim($body_part_ids, ',');
			$body_part_id_con = "and id in($body_part_ids)";
		}
	 }

		$sql_bundle_copy="select id,bundle_use_for from ppl_bundle_title where company_id=$company_id $body_part_id_con";
	//  echo $sql_bundle_copy;die();


	$res = sql_select($sql_bundle_copy);
	// echo  create_list_view("tbl_list_search", "Bundle Use For", "240","240","180",0, $sql_bundle_copy, "js_set_value", "id,bundle_use_for", "", 1, "0", $arr, "bundle_use_for", "","setFilterGrid('tbl_list_search',-1)",'0',"",1);
    echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_print' />";
	echo "<input type='hidden' id='txt_selected_emb' />";
	?>
	<table cellspacing="0" width="260"  border="1" rules="all" class="rpt_table" >
		<thead>
			<th width="30">Sl</th>
			<th width="170">BUndle User For</th>
			<th width="30">Print</th>
			<th width="30">Emb</th>
		</thead>
    </table>
	<div style="width:280px; max-height:300px; overflow-y:scroll" id="scroll_body" >
		<table cellspacing="0" width="260"  border="1" rules="all" class="rpt_table" id="tbl_list_search" >
		<?

		$i=1;
			foreach($res as $row)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>"  style="cursor:pointer;">
					<td onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$row[csf('BUNDLE_USE_FOR')]; ?>')" width="30"><? echo $i;?></td>
					<td id="td_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$row[csf('BUNDLE_USE_FOR')]; ?>')" width="170">
					<? echo $row['BUNDLE_USE_FOR'];?>
					</td>
					<td width="30" align="center"><input type="checkbox" name="print" value="<?=$row[csf('id')];?>"></td>
					<td width="30" align="center"><input type="checkbox" name="emb" value="<?=$row[csf('id')];?>"></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<table width="280">
		<tr align="center">
			<td>
				<div align="left" style="width:50%; float:left">
					<input id="check_all" type="checkbox" onclick="check_all_data()" name="check_all">
						Check / Uncheck All
				</div>
				<div align="left" style="width:50%; float:left">
					<input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" />
				</div>
			</td>
		</tr>
	</table>
	<script>
		setFilterGrid("tbl_list_search",-1);
		set_all_data();
	</script>
	<?
	exit();
}

if($action=="print_report_bundle_barcode_eight_v2")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
	$data_all=$data;
	$data=explode("***",$data);

	$bundle_source = $data[7];
	$job = $data[1];
	$item_id = $data[4];

	//echo $data[0];die;
	?>
      <script>

		var selected_id = new Array;
		var selected_name = new Array;

    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#td_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );

	}

	function fn_onClosed()
	{
		var print = '';
		$('#txt_selected_print').val('');
		$("input[name=print]").each(function(index, element)
		{
			if( $(this).prop('checked')==true)
			{
				print += (print=="") ? $(this).val() : ","+$(this).val();
			}
		});
		// alert(print);return;
		$('#txt_selected_print').val(print);
		// ===========================================
		var emb = '';
		$('#txt_selected_emb').val('');
		$("input[name=emb]").each(function(index, element)
		{
			if( $(this).prop('checked')==true)
			{
				emb += (emb=="") ? $(this).val() : ","+$(this).val();
			}
		});
		$('#txt_selected_emb').val(emb);
		// alert(print);return;

		parent.emailwindow.hide();
	}
	</script>
    <?
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	 foreach($sql_cut_name as $cut_value)
	 {
		 $table_name=$cut_value[csf('table_no')];
	     $cut_date=change_date_format($cut_value[csf('entry_date')]);
         $cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		 $company_id=$cut_value[csf('company_id')];
		 $batch_no=$cut_value[csf('batch_id')];
		 $comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		 $new_cut_no=$comp_name."-".$cut_prifix;
		 $bundle_title="";

	 }

	$body_part_id_con = '';
	if($bundle_source == 1 ) // Body part name come from *** Style Wise Body Part Entry Page ***
	{
		$style_wise_body_part_sql = "SELECT id,body_part_ids from style_wise_body_part_mst where company_name=$company_id and job_no= '$job' and item_id = '$item_id' and status_active=1 and is_deleted=0";
		// echo $style_wise_body_part_sql;
		$style_wise_body_part_sql_res = sql_select($style_wise_body_part_sql);
		$body_part_ids = '';
		foreach ($style_wise_body_part_sql_res as   $v) {
			$body_part_ids .= $v['BODY_PART_IDS'].',';
		}


		if ($body_part_ids != '')
		{
			$body_part_ids = rtrim($body_part_ids,',');
			$body_part_id_con = "and id in($body_part_ids)";
		}

	}
	$sql_bundle_copy="select id,bundle_use_for from ppl_bundle_title where company_id=$company_id $body_part_id_con";

	// echo $sql_bundle_copy; die;

	$res = sql_select($sql_bundle_copy);
	// echo  create_list_view("tbl_list_search", "Bundle Use For", "240","240","180",0, $sql_bundle_copy, "js_set_value", "id,bundle_use_for", "", 1, "0", $arr, "bundle_use_for", "","setFilterGrid('tbl_list_search',-1)",'0',"",1);
    echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_print' />";
	echo "<input type='hidden' id='txt_selected_emb' />";
	?>
	<table cellspacing="0" width="260"  border="1" rules="all" class="rpt_table" >
		<thead>
			<th width="30">Sl</th>
			<th width="170">BUndle User For</th>
			<th width="30">Print</th>
			<th width="30">Emb</th>
		</thead>
    </table>
	<div style="width:280px; max-height:300px; overflow-y:scroll" id="scroll_body" >
		<table cellspacing="0" width="260"  border="1" rules="all" class="rpt_table" id="tbl_list_search" >
		<?

		$i=1;
			foreach($res as $row)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor ; ?>" id="tr_<? echo $i; ?>"  style="cursor:pointer;">
					<td onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$row[csf('BUNDLE_USE_FOR')]; ?>')" width="30"><? echo $i;?></td>
					<td id="td_<? echo $i; ?>" onClick="js_set_value('<? echo $i.'_'.$row[csf('id')].'_'.$row[csf('BUNDLE_USE_FOR')]; ?>')" width="170">
					<? echo $row['BUNDLE_USE_FOR'];?>
					</td>
					<td width="30" align="center"><input type="checkbox" name="print" value="<?=$row[csf('id')];?>"></td>
					<td width="30" align="center"><input type="checkbox" name="emb" value="<?=$row[csf('id')];?>"></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<table width="280">
		<tr align="center">
			<td>
				<div align="left" style="width:50%; float:left">
					<input id="check_all" type="checkbox" onclick="check_all_data()" name="check_all">
						Check / Uncheck All
				</div>
				<div align="left" style="width:50%; float:left">
					<input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" />
				</div>
			</td>
		</tr>
	</table>
	<script>
		setFilterGrid("tbl_list_search",-1);
		set_all_data();
	</script>
	<?
	exit();
}

if($action=="print_report_bundle_barcode_nine")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
	//$data_all=$data;
	$data=explode("***",$data);
	//echo $data[0];die;
	// echo "<pre>";
	// //print_r($data);
	?>
      <script>

		var selected_id = new Array;
		var selected_name = new Array;

    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );

	}
	</script>
    <?
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	 foreach($sql_cut_name as $cut_value)
	 {
		 $table_name=$cut_value[csf('table_no')];
	     $cut_date=change_date_format($cut_value[csf('entry_date')]);
         $cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		 $company_id=$cut_value[csf('company_id')];
		 $batch_no=$cut_value[csf('batch_id')];
		 $comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		 $new_cut_no=$comp_name."-".$cut_prifix;
		 $bundle_title="";

	 }
    $sql_bundle_copy="select id,bundle_use_for from ppl_bundle_title where company_id=$company_id";
	echo  create_list_view("tbl_list_search", "Bundle Use For", "240","240","180",0, $sql_bundle_copy, "js_set_value", "id,bundle_use_for", "", 1, "0", $arr, "bundle_use_for", "","setFilterGrid('tbl_list_search',-1)",'0',"",1);
    echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}

if($action=="print_report_bundle_list_popup_bk")   //1/4/2023
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
	?>
      <script>

		var selected_id = new Array;
		var selected_name = new Array;

    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 );

				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );

	}
	</script>
    <?

    $sql_bundle_copy="select id,bundle_use_for from ppl_bundle_title where company_id=$data";
	echo  create_list_view("tbl_list_search", "Bundle Use For", "240","240","180",0, $sql_bundle_copy, "js_set_value", "id,bundle_use_for", "", 1, "0", $arr, "bundle_use_for", "","setFilterGrid('tbl_list_search',-1)",'0',"",1);
    echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}
if($action=="print_report_bundle_list_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
	$data=explode("***",$data);
	?>
      <script>

		var selected_id = new Array;
		var selected_name = new Array;

    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 );

				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name );

	}
	</script>
    <?
	 $yes_no_query="SELECT b.company_name,b.variable_list,b.finish_rate_come_from from variable_settings_production b WHERE b.variable_list=80 and b.company_name= $data[0] ";
	    // echo $yes_no_query;die();
	  $yes_no=sql_select($yes_no_query);
	  $yes_no_arr=array();
	  foreach($yes_no as $val)
	  {
			$finish_rate_come_from=$val[csf('finish_rate_come_from')];
	  }

	  if($finish_rate_come_from==1)
	  {
		 $style_wise_body_part_sql="SELECT id,body_part_ids from style_wise_body_part_mst where company_name=$data[0] and job_no= '$data[1]'  and status_active=1 and is_deleted=0";
		//  echo $style_wise_body_part_sql;
		 $style_wise_body_part_sql_res = sql_select($style_wise_body_part_sql);

		 if (count($style_wise_body_part_sql_res) > 0) {
			 $body_part_ids = '';
			 foreach ($style_wise_body_part_sql_res as   $v) {
				 $body_part_ids .= $v['BODY_PART_IDS'] . ',';
				 // echo $body_part_ids;
			 }
			 $body_part_ids = rtrim($body_part_ids, ',');
			 $body_part_id_con = "and id in($body_part_ids)";
		 }
	  }


    $sql_bundle_copy="select id,bundle_use_for from ppl_bundle_title where company_id=$data[0] $body_part_id_con";
	echo  create_list_view("tbl_list_search", "Bundle Use For", "240","240","180",0, $sql_bundle_copy, "js_set_value", "id,bundle_use_for", "", 1, "0", $arr, "bundle_use_for", "","setFilterGrid('tbl_list_search',-1)",'0',"",1);
    echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}


if($action=="print_qrcode_operation")
{
	//echo "1000".$data;die;
	$data=explode("***",$data);
	$detls_id=$data[3];
	$garments_item_name=$garments_item[$data[4]];
	$color_library=return_library_array( "select id, color_name from lib_color where id=$data[5]", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');


	$color_sizeID_arr=sql_select("select
										a.id,
										a.size_id,
										a.bundle_no,
										a.barcode_no,
										a.order_id,
										a.number_start,
										a.number_end,
										a.size_qty,
										a.country_id,
										a.roll_no,
										b.bundle_sequence,
										b.color_id
									from
										ppl_cut_lay_bundle a,
										ppl_cut_lay_size_dtls b
									where
										a.mst_id=b.mst_id and
										a.dtls_id=b.dtls_id and
										a.size_id=b.size_id and
										a.id in ($data[0])
									order by
										b.bundle_sequence,
										a.id");

	foreach($color_sizeID_arr as $val_qty)
	{
		$total_cut_qty+=$val_qty[csf('size_qty')];
		$total_size_qty_arr[$val_qty[csf('size_id')]]+=$val_qty[csf('size_qty')];
	}
	$bundle_array=array();
	$sql_name=sql_select("select
							b.buyer_name,
							b.style_ref_no,
							b.product_dept,
							a.po_number,
							a.id
						from
							wo_po_details_master b,
							wo_po_break_down a
						where
							a.job_no_mst=b.job_no and
							a.job_no_mst='".$data[1]."'");

	foreach($sql_name as $value)
	{
		$product_dept_name 						=$value[csf('product_dept')];
		$style_name 							=$value[csf('style_ref_no')];
		$buyer_name 							=$value[csf('buyer_name')];
		$po_number_arr[$value[csf('id')]] 		=$value[csf('po_number')];
	}
	$buyer_short_name=return_field_value("short_name","lib_buyer", "id=$buyer_name ");
	//return_library_array( "select id,short_name from lib_buyer where id=$buyer_name ", "id", "short_name");
	$sql_cut_name=sql_select("select
								entry_date,
								table_no,
								cut_num_prefix_no,
								batch_id,
								company_id,
								cutting_no
							from
								ppl_cut_lay_mst
							where id=$data[2]");

	foreach($sql_cut_name as $cut_value)
	{
		$ful_cut_no 		=$cut_value[csf('cutting_no')];
		$table_id 			=$cut_value[csf('table_no')];
		$cut_date 			=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix 		=$cut_value[csf('cut_num_prefix_no')];
		$company_id 		=$cut_value[csf('company_id')];
		$batch_no 			=$cut_value[csf('batch_id')];
		$comp_name 			=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no 		=$comp_name."-".$cut_prifix;
		$bundle_title 		="";
	}

	$table_no=return_field_value("table_no","lib_cutting_table", "id=$table_id ");
	//return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	?>


    <?
     $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$ful_cut_no.DIRECTORY_SEPARATOR;
     $PNG_WEB_DIR = 'qrcode_image/'.$ful_cut_no.'/';

     foreach (glob($PNG_WEB_DIR."*.png") as $filename) {
			@unlink($filename);
		}

     if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);

     $filename = $PNG_TEMP_DIR.'test.png';
     $errorCorrectionLevel = 'L';
     $matrixPointSize = 4;

    include "../../../ext_resource/phpqrcode/qrlib.php";
	require_once("../../../ext_resource/mpdf60/mpdf.php");

	$mpdf = new mPDF('',    // mode - default ''
					array(60,70),		// array(65,210),    // format - A4, for example, default ''
					 5,     // font size - default 0
					 '',    // default font family
					 3,    // margin_left
					 3,    // margin right
					 3,     // margin top
					 0,    // margin bottom
					 0,     // margin header
					 0,     // margin footer
					 'L');

	if($data[7]=="") $data[7]=0;
	$i 		=1;
	$html 	='';
	$total_number_of_bundle=count($color_sizeID_arr);
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($color_sizeID_arr as $val)
		{
			foreach($sql_bundle_copy as $inf)
			{
				$filename = $PNG_TEMP_DIR.'test'.md5($val[csf("barcode_no")]).'.png';
		    	QRcode::png($val[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
				$po_number=$po_number_arr[$val[csf('order_id')]];
				$country_name=$country_arr[$val[csf('country_id')]];
				$bundle_array[$i]=$val[csf("barcode_no")];

				$mpdf->AddPage('',    // mode - default ''
					array(60,70),		// array(65,210),    // format - A4, for example, default ''
					 5,     // font size - default 0
					 '',    // default font family
					 3,    // margin_left
					 3,    // margin right
					 3,     // margin top
					 0,    // margin bottom
					 0,     // margin header
					 0,     // margin footer
					 'L');

				$html.='<table cellpadding="0" cellspacing="0" width="" class="" style="font-size:12px; font-weight:bold;margin:0px" rules="all" id="">

					        	<tr >
									<td  width="40%"  >
										<table  width="100%" border="0">
											<tr>
												<td  width=""  >
												<div id="div_'.$i.'"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="60" width=""></div></td>
											</tr>
										</table>
									</td>
									<td  width="60%"  >
										<table  width="100%">
											<tr>
												<td width="">'.$val[csf("barcode_no")].'</td>
											</tr>
											<tr>
												<td width="">'.$val[csf("bundle_no")].'</td>
											</tr>
											<tr>
												<td width="">'.$data[1].'</td>
											</tr>
										</table>
									</td>
								</tr>

					</table>
					<table cellpadding="0" cellspacing="0" width="" class="" style="font-size:12px; font-weight:bold;margin:0px" rules="all" id="">

						<tr>
							<td>Cut Qty: ('.$total_cut_qty.')</td>
			            	<td width="">'.$inf[csf("bundle_use_for")].'</td>
			            </tr>
			            <tr>
			            	<td width="50%">Table No :'.$table_no.' </td>
			            	<td width="50%">Date :'.$cut_date.'</td>
			            </tr>
			            <tr>
			            	<td>'.$buyer_short_name.'</td>
			            	<td>O:'.$po_number.'</td>
			            </tr>

			            <tr>
			            	<td width="" colspan="2">Style :'.$style_name.' </td>
			            </tr>

			            <tr>
			            	<td width="" colspan="2">Country :'.$country_name.' </td>
			            </tr>

			            <tr>
			            	<td colspan="2">Item :'.$garments_item_name.'</td>
			            </tr>

			            <tr>
			            	<td colspan="2">Color:'.$color_library[$data[5]].'</td>
			            </tr>

			            <tr>
			            	<td>Size : '.$size_arr[$val[csf("size_id")]].'('.$total_size_qty_arr[$val[csf("size_id")]].')</td>
			            	<td>Batch:'.$batch_no.'</td>

			            </tr>

			            <tr>
			            	<td>Gmts. No:'.$val[csf("number_start")].'-'.$val[csf("number_end")].'</td>
			            	<td>Gmts. Qnty:'.$val[csf("size_qty")].'</td>
			            </tr>

			            <tr>
			            	<td></td>
			            	<td align="right">Page '.$i.'</td>
			            </tr>


				    </table>';


				$mpdf->WriteHTML($html);
				$html='';
				$i++;
			}

		}
	}
	else
	{
		foreach($color_sizeID_arr as $val)
		{

			$filename = $PNG_TEMP_DIR.'test'.md5($val[csf("barcode_no")]).'.png';
	    	QRcode::png($val[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
			$country_name=$country_arr[$val[csf('country_id')]];
			$po_number=$po_number_arr[$val[csf('order_id')]];
			$bundle_array[$i]=$val[csf("barcode_no")];

			$mpdf->AddPage('',    // mode - default ''
				array(60,70),		// array(65,210),    // format - A4, for example, default ''
				 5,     // font size - default 0
				 '',    // default font family
				 3,    // margin_left
				 3,    // margin right
				 3,     // margin top
				 0,    // margin bottom
				 0,     // margin header
				 0,     // margin footer
				 'L');

			$html.='<table cellpadding="0" cellspacing="0" width="" class="" style="font-size:12px; font-weight:bold;margin:0px" rules="all" id="">

				        	<tr >
								<td  width="40%"  >
									<table  width="100%" border="0">
										<tr>
											<td  width=""  >
											<div id="div_'.$i.'"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="60" width=""></div></td>
										</tr>
									</table>
								</td>
								<td  width="60%"  >
									<table  width="100%">
										<tr>
											<td width="">'.$val[csf("barcode_no")].'</td>
										</tr>
										<tr>
											<td width="">'.$val[csf("bundle_no")].'</td>
										</tr>
										<tr>
											<td width="">'.$data[1].'</td>
										</tr>
									</table>
								</td>
							</tr>

				</table>
				<table cellpadding="0" cellspacing="0" width="" class="" style="font-size:12px; font-weight:bold;margin:0px" rules="all" id="">

					<tr>
						<td>Cut Qty: ('.$total_cut_qty.')</td>
		            	<td width="">'.$inf[csf("bundle_use_for")].'</td>
		            </tr>
		            <tr>
		            	<td width="50%">Table No :'.$table_no.' </td>
		            	<td width="50%">Date :'.$cut_date.'</td>
		            </tr>
		            <tr>
		            	<td>'.$buyer_short_name.'</td>
		            	<td>O:'.$po_number.'</td>
		            </tr>

		            <tr>
		            	<td width="" colspan="2">Style :'.$style_name.' </td>
		            </tr>

		            <tr>
		            	<td width="" colspan="2">Country :'.$country_name.' </td>
		            </tr>

		            <tr>
		            	<td colspan="2">Item :'.$garments_item_name.'</td>
		            </tr>

		            <tr>
		            	<td colspan="2">Color:'.$color_library[$data[5]].'</td>
		            </tr>

		            <tr>
		            	<td>Size : '.$size_arr[$val[csf("size_id")]].'('.$total_size_qty_arr[$val[csf("size_id")]].')</td>
		            	<td>Batch:'.$batch_no.'</td>

		            </tr>

		            <tr>
		            	<td>Gmts. No:'.$val[csf("number_start")].'-'.$val[csf("number_end")].'</td>
		            	<td>Gmts. Qnty:'.$val[csf("size_qty")].'</td>
		            </tr>

		            <tr>
		            	<td></td>
		            	<td align="right">Page '.$i.'</td>
		            </tr>

			    </table>';

			$mpdf->WriteHTML($html);
			$html='';
			$i++;
		}

	}
	//$mpdf->WriteHTML($html);
	foreach (glob("*.pdf") as $filename) {
			@unlink($filename);
		}
	$name = 'lotRatioEntry_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
	$mpdf->Output($name, 'F');
	echo "1###$name";
	exit();
}

if($action=="print_qrcode_operation1")
{
    // echo "1000".$data;die;
    $data=explode("***",$data);
    $detls_id=$data[3];
    $garments_item_name=$garments_item[$data[4]];
    $color_library=return_library_array( "select id, color_name from lib_color where id=$data[5]", "id", "color_name");
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");

    $total_cut_qty_int=return_field_value("sum(a.size_qty) as total_qty", "ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b", "a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id in ($data[2])", "total_qty");

    $color_sizeID_arr=sql_select("SELECT a.id,a.size_id,a.bundle_no,a.barcode_no,a.order_id,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_id,a.roll_no,b.bundle_sequence,b.color_id,a.pattern_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b
	where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	order by b.bundle_sequence,a.id");
	// print_r($color_sizeID_arr);


    foreach($color_sizeID_arr as $val_qty)
    {
        $total_cut_qty+=$val_qty[csf('size_qty')];
        $total_size_qty_arr[$val_qty[csf('size_id')]]+=$val_qty[csf('size_qty')];
    }
    $bundle_array=array();

    $sql_name=sql_select("SELECT b.job_no,b.buyer_name,b.style_ref_no,b.product_dept,a.po_number,a.id from wo_po_details_master b,wo_po_break_down a
	where a.job_no_mst=b.job_no and a.job_no_mst='".$data[1]."'");

    foreach($sql_name as $value)
    {
        $product_dept_name 						=$value[csf('product_dept')];
        $style_name 							=$value[csf('style_ref_no')];
        $buyer_name 							=$value[csf('buyer_name')];
        $po_number_arr[$value[csf('id')]] 		=$value[csf('po_number')];
        $job_number 		                    =$value[csf('job_no')];
    }
    $buyer_short_name=return_field_value("short_name","lib_buyer", "id=$buyer_name ");

    $sql_cut_name=sql_select("SELECT a.entry_date, a.table_no, a.cut_num_prefix_no, a.batch_id, a.company_id, a.cutting_no, a.shipment_part,b.roll_data,b.contrust_color_id  from ppl_cut_lay_mst a, ppl_cut_lay_dtls b  where a.id = b.mst_id and a.id=$data[2]");

	// echo $sql="SELECT a.entry_date, a.table_no, a.cut_num_prefix_no, a.batch_id, a.company_id, a.cutting_no, a.shipment_part,b.roll_data  from ppl_cut_lay_mst a, ppl_cut_lay_dtls b  where a.id = b.mst_id and a.id=$data[2]";die();

    $roll_data = explode("=", $sql_cut_name[0][csf('roll_data')]);
    $batch_shade = (isset($roll_data[5]) ? $roll_data[5] : "") ."-".(isset($roll_data[6]) ? $roll_data[6] : "");

    foreach($sql_cut_name as $cut_value)
    {
        $ful_cut_no 		=$cut_value[csf('cutting_no')];
        $table_id 			=$cut_value[csf('table_no')];
        $cut_date 			=change_date_format($cut_value[csf('entry_date')]);
        $cut_prifix 		=$cut_value[csf('cut_num_prefix_no')];
        $company_id 		=$cut_value[csf('company_id')];
        $shipment_part 		=$cut_value[csf('shipment_part')];
        $batch_no 			=$cut_value[csf('batch_id')];
		$contrust_color_id  =$cut_value[csf('contrust_color_id')];
        $comp_name 			=return_field_value("company_short_name","lib_company", "id=$company_id");
        $new_cut_no 		=$comp_name."-".$cut_prifix;
        $bundle_title 		="";
    }
    $table_no=return_field_value("table_no","lib_cutting_table", "id=$table_id ");
	$contrust_color_name=return_field_value("color_name","lib_color","id=$contrust_color_id");
	//  echo $contrust_color_id;die();

    $roll_data_arr=return_library_array("select roll_id, (batch_no || '-' || shade) as batch_shade from pro_roll_details where entry_form=509 and status_active=1", "roll_id", "batch_shade");


    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$ful_cut_no.DIRECTORY_SEPARATOR;
    $PNG_WEB_DIR = 'qrcode_image/'.$ful_cut_no.'/';
    foreach (glob($PNG_WEB_DIR."*.png") as $filename) {
        @unlink($filename);
    }
    if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
    $filename = $PNG_TEMP_DIR.'test.png';
    $errorCorrectionLevel = 'L';
    $matrixPointSize = 4;

    include "../../../ext_resource/phpqrcode/qrlib.php";
    require_once("../../../ext_resource/mpdf60/mpdf.php");

    $mpdf = new mPDF('',    // mode - default ''
        array(190,300),		// array(65,210),    // format - A4, for example, default ''
        5,     // font size - default 0
        '',    // default font family
        3,    // margin_left
        3,    // margin right
        3,     // margin top
        0,    // margin bottom
        0,     // margin header
        0,     // margin footer
        'P');
    if($data[7]=="") $data[7]=0;
    $i 		=1;
    $html 	='';
    $total_number_of_bundle=count($color_sizeID_arr);
    $sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
    if(count($sql_bundle_copy)!=0)
    {
        $bundle_arr_main = [];
        foreach($color_sizeID_arr as $val)
        {
            foreach($sql_bundle_copy as $breakData)
            {
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['ID'] = $val[csf('id')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['barcode_no'] = $val[csf('barcode_no')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['po_number'] = $po_number_arr[$val[csf('order_id')]];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['country'] = $country_arr[$val[csf('country_id')]];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['roll_data'] = $roll_data_arr[$val['ROLL_ID']];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['bundle_use_for'] = $breakData[csf('bundle_use_for')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['color'] = $color_library[$val[csf("color_id")]];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['pattern_no'] = $val[csf("pattern_no")];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['size'] = $size_arr[$val[csf("size_id")]];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['size_qty'] = $val[csf('size_qty')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['number_start'] = $val[csf('number_start')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['number_end'] = $val[csf('number_end')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['bundle_no'] = $val[csf('bundle_no')];
            }
        }

            foreach(array_chunk($bundle_arr_main, 8) as $breakData)
            {
                $mpdf->AddPage('',    // mode - default ''
                    array(190,300),		// array(65,210),    // format - A4, for example, default ''
                    5,     // font size - default 0
                    '',    // default font family
                    0,    // margin_left
                    0,    // margin right
                    0,     // margin top
                    0,    // margin bottom
                    0,     // margin header
                    0,     // margin footer
                    'P');
                $html .= '<style>
                            td, th {
                                border: .4px solid black;
                            }
                        </style>';
                $html .='<table width="100%" border="0" style="border:none;">';
                foreach (array_chunk($breakData, 2) as $rowData) {
                    $html .='<tr style="border:none;">';
                    foreach ($rowData as $val) {
                        $filename = $PNG_TEMP_DIR.'test'.md5($val["barcode_no"]).'.png';
                        QRcode::png($val["barcode_no"], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
                        $po_number=$val['po_number'];
                        $country_name=$val['country'];
                        $bundle_array[$i]=$val["barcode_no"];
                        $html .= '<td style="border:none; padding-bottom:53px;padding-top:10px;"><table cellpadding="0" cellspacing="0" width="321" height="184" class="" style="font-weight:normal;margin:0 auto;font-size:10px;" rules="all" id="" border="1" align="left">
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;">LOT-SHADE# ' . substr($val['roll_data'], 0, 14) . '</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;" width="50%">JOB# ' . substr($job_number, 0, 17) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;">BUYER# ' . substr($buyer_short_name, 0, 16) . '</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;" width="50%" > STY# '.substr($style_name, 0, 20).'</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;" width="50%">PO# ' . substr($po_number, 0, 37) . '</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;">B.PART# ' . substr($val["bundle_use_for"], 0, 37) . '</td>
                                </tr>
                                <tr>
								<td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;">F.CLR# ' . substr($contrust_color_name, 0, 37) . '</td>
								<td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;" width="">GROUP# ' . substr($val["pattern_no"], 0, 16) . '</td>
                                </tr>
								<tr>
								<td colspan="2" style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;">GMT.CLR# ' . substr($val["color"], 0, 30) . '</td>
								</tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;">CUT NO# ' . substr($order_cut_no, 0, 16) . '</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;" width="">CUT QTY# ' . $total_cut_qty_int . '</td>

                                </tr>

                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;">BUNDLE SIZE# ' . $val["size"] . '</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;">PART# ' . substr($shipment_part, 0, 37) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;">BUNDLE PCS# ' . $val["size_qty"] . '</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;" width="" rowspan="4" align="right"><div id="div_' . $i . '"><img src="' . $PNG_WEB_DIR . basename($filename) . '" height="60" width="" ></div></td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;">SERIAL NO# ' . substr($val["number_start"] . '-' . $val["number_end"], 0, 16) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;">BNDL# ' . substr($val['bundle_no'], 0, 16) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;">' . $val["barcode_no"] . '</td>
                                </tr>

                            </table></td>';
                    }
                    $html .='</tr>';
                }
                $html .= "</table>";
                $mpdf->WriteHTML($html);
                $html='';
                $i++;
            }
    }
    //$mpdf->WriteHTML($html);
    foreach (glob("*.pdf") as $filename) {
        @unlink($filename);
    }
    $name = 'lotRatioEntry_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
    $mpdf->Output($name, 'F');
    echo "1###$name";

    exit();

}

if($action=="print_qrcode_operation2__")// 04-feb-2023
{
    //echo "1000".$data;die;

    $data=explode("***",$data);
    $detls_id=$data[3];
    $garments_item_name=$garments_item[$data[4]];
    $color_library=return_library_array( "select id, color_name from lib_color where id=$data[5]", "id", "color_name");
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");

    $total_cut_qty_int=return_field_value("sum(a.size_qty) as total_qty", "ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b", "a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id in ($data[2])", "total_qty");

    $color_sizeID_arr=sql_select("SELECT a.id,a.size_id,a.bundle_no,a.barcode_no,a.order_id,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_id,a.roll_no,b.bundle_sequence,b.color_id,a.pattern_no,a.color_type_id,c.entry_date
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b,ppl_cut_lay_mst c
	where a.mst_id=b.mst_id and c.id=a.mst_id and c.id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	order by b.bundle_sequence,a.id");

    foreach($color_sizeID_arr as $val_qty)
    {
        $total_cut_qty+=$val_qty[csf('size_qty')];
        $total_size_qty_arr[$val_qty[csf('size_id')]]+=$val_qty[csf('size_qty')];
    }
    $bundle_array=array();

    $sql_name=sql_select("SELECT b.job_no,b.buyer_name,b.style_ref_no,b.product_dept,a.po_number,a.grouping,a.id from wo_po_details_master b,wo_po_break_down a
	where a.job_no_mst=b.job_no and a.job_no_mst='".$data[1]."'");

    foreach($sql_name as $value)
    {
        $product_dept_name 						=$value[csf('product_dept')];
        $style_name 							=$value[csf('style_ref_no')];
        $buyer_name 							=$value[csf('buyer_name')];
        $po_number_arr[$value[csf('id')]] 		=$value[csf('po_number')];
        $job_number 		                    =$value[csf('job_no')];
		$grouping 							    =$value[csf('grouping')];
    }
    $buyer_short_name=return_field_value("short_name","lib_buyer", "id=$buyer_name ");

    $sql_cut_name=sql_select("SELECT a.entry_date, a.table_no, a.cut_num_prefix_no, a.batch_id, a.company_id, a.cutting_no, a.shipment_part,b.roll_data  from ppl_cut_lay_mst a, ppl_cut_lay_dtls b  where a.id = b.mst_id and a.id=$data[2]");

    $roll_data = explode("=", $sql_cut_name[0][csf('roll_data')]);
    $batch_shade = (isset($roll_data[5]) ? $roll_data[5] : "") ."-".(isset($roll_data[6]) ? $roll_data[6] : "");

    foreach($sql_cut_name as $cut_value)
    {
        $ful_cut_no 		=$cut_value[csf('cutting_no')];
        $table_id 			=$cut_value[csf('table_no')];
        $cut_date 			=change_date_format($cut_value[csf('entry_date')]);
        $cut_prifix 		=$cut_value[csf('cut_num_prefix_no')];
        $company_id 		=$cut_value[csf('company_id')];
        $shipment_part 		=$cut_value[csf('shipment_part')];
        $batch_no 			=$cut_value[csf('batch_id')];
        $comp_name 			=return_field_value("company_short_name","lib_company", "id=$company_id");
        $new_cut_no 		=$comp_name."-".$cut_prifix;
        $bundle_title 		="";
    }
    $table_no=return_field_value("table_no","lib_cutting_table", "id=$table_id ");

    $roll_data_arr=return_library_array("select roll_id, (batch_no || '-' || shade) as batch_shade from pro_roll_details where entry_form=509 and status_active=1", "roll_id", "batch_shade");


    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$ful_cut_no.DIRECTORY_SEPARATOR;
    $PNG_WEB_DIR = 'qrcode_image/'.$ful_cut_no.'/';
    foreach (glob($PNG_WEB_DIR."*.png") as $filename) {
        @unlink($filename);
    }
    if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
    $filename = $PNG_TEMP_DIR.'test.png';
    $errorCorrectionLevel = 'L';
    $matrixPointSize = 4;

    include "../../../ext_resource/phpqrcode/qrlib.php";
    require_once("../../../ext_resource/mpdf60/mpdf.php");

    $mpdf = new mPDF('',    // mode - default ''
	array(297,210),//array(297,210),		// array(65,210),    // format - A4, for example, default ''
        18,     // font size - default 0
        '',    // default font family
        1,    // margin_left
        1,    // margin right
        1,     // margin top
        1,    // margin bottom
        1,     // margin header
        1,     // margin footer
        'P');
    if($data[7]=="") $data[7]=0;
    $i 		=1;
    $html 	='';
    $total_number_of_bundle=count($color_sizeID_arr);
    $sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
    if(count($sql_bundle_copy)!=0)
    {
        $bundle_arr_main = [];
        foreach($color_sizeID_arr as $val)
        {
            foreach($sql_bundle_copy as $breakData)
            {
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['ID'] = $val[csf('id')];
				$bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['entry_date'] = $val[csf('entry_date')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['barcode_no'] = $val[csf('barcode_no')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['po_number'] = $po_number_arr[$val[csf('order_id')]];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['country'] = $country_arr[$val[csf('country_id')]];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['roll_data'] = $roll_data_arr[$val['ROLL_ID']];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['bundle_use_for'] = $breakData[csf('bundle_use_for')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['color'] = $color_library[$val[csf("color_id")]];
				$bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['color_type_name'] = $color_type[$val[csf("color_type_id")]];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['pattern_no'] = $val[csf("pattern_no")];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['size'] = $size_arr[$val[csf("size_id")]];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['size_qty'] = $val[csf('size_qty')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['number_start'] = $val[csf('number_start')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['number_end'] = $val[csf('number_end')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['bundle_no'] = $val[csf('bundle_no')];
            }
        }

            foreach(array_chunk($bundle_arr_main, 1) as $breakData)
            {
                $mpdf->AddPage('',    // mode - default ''
				array(297,210),//array(297,210),		// array(65,210),    // format - A4, for example, default ''
                    18,     // font size - default 0
                    '',    // default font family
                    1,    // margin_left
                    1,    // margin right
                    1,     // margin top
                    1,    // margin bottom
                    1,     // margin header
                    1,     // margin footer
                    'P');
                $html .= '<style>
                            td, th {
                                border: .4px solid black;
                            }
                        </style>';
                $html .='<table width="100%" border="0" style="border:none;">';
                foreach (array_chunk($breakData, 2) as $rowData) {
                    $html .='<tr style="border:none;">';
                    foreach ($rowData as $val) {
                        $filename = $PNG_TEMP_DIR.'test'.md5($val["barcode_no"]).'.png';
                        QRcode::png($val["barcode_no"], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
                        $po_number=$val['po_number'];
                        $country_name=$val['country'];
                        $bundle_array[$i]=$val["barcode_no"];
                        $html .= '<td style="border:none; "><table cellpadding="0" cellspacing="0" width="100%" height="100%" class="" style="font-weight:normal;margin:0 auto;font-size:9px;" rules="all" id="" border="1" align="left">
                                <tr>
								<td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;">BUYER# ' . substr($buyer_short_name, 0, 16) . '</td>
								<td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;" width="50%" >PO NO# ' . substr($po_number, 0, 16) . ' </td>
                                </tr>
                                <tr>
								<td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;">INT.Booking No# ' . substr($grouping, 0, 16) . '</td>
								<td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;">B. PART# ' . substr($val["bundle_use_for"], 0, 15) . '</td>

                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;" width="50%">STY# ' . substr($style_name, 0, 20) . ' </td>
									<td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;" width="50%">GMTS SIZE# ' . substr($val['size'], 0, 20) . ' </td>

                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;">CLR# ' . substr($val["color"], 0, 20) . '</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;" width="">CUT NO# ' . substr($order_cut_no, 0, 16) . ',T# ' . substr($table_no, 0, 16) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;">CLR TYPE# ' . substr($val["color_type_name"], 0, 16) . '</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;" width="">CUT QTY# ' . $total_cut_qty_int . '</td>

                                </tr>

                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;">BATCH# ' . substr($batch_no, 0, 16) . '</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;">DATE# ' . substr($val["entry_date"], 0, 17) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;">BUNDLE PCS# ' . $val["size_qty"] . '</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;" width="" rowspan="4" align="right"><div id="div_' . $i . '"><img src="' . $PNG_WEB_DIR . basename($filename) . '" height="360" width="" ></div></td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;">SERIAL NO# ' . substr($val["number_start"] . '-' . $val["number_end"], 0, 16) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;">BUNDLE# ' . substr($val['bundle_no'], 0, 16) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:50px;">' . $val["barcode_no"] . '</td>
                                </tr>

                            </table></td>';
                    }
                    $html .='</tr>';
                }
                $html .= "</table>";
                $mpdf->WriteHTML($html);
                $html='';
                $i++;
            }
    }
    //$mpdf->WriteHTML($html);
    foreach (glob("*.pdf") as $filename) {
        @unlink($filename);
    }
    $name = 'lotRatioEntry_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
    $mpdf->Output($name, 'F');
    echo "1###$name";

    exit();

}

if($action=="print_qrcode_operation2")
{
    //echo "1000".$data;die;

    $data=explode("***",$data);
	$mst_id=$data[2];
    $detls_id=$data[3];
    $garments_item_name=$garments_item[$data[4]];
    $color_library=return_library_array( "select id, color_name from lib_color where id=$data[5]", "id", "color_name");
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");

    $total_cut_qty_int=return_field_value("sum(a.size_qty) as total_qty", "ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b", "a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.mst_id in ($data[2])", "total_qty");

    $color_sizeID_arr=sql_select("SELECT a.id,a.size_id,a.bundle_no,a.barcode_no,a.order_id,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_id,a.roll_no,b.bundle_sequence,b.color_id,a.pattern_no,a.color_type_id,c.entry_date
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b,ppl_cut_lay_mst c
	where a.mst_id=b.mst_id and c.id=a.mst_id and c.id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	order by b.bundle_sequence,a.id");
	// $roll_id_array=array();

    foreach($color_sizeID_arr as $val_qty)
    {
		// $roll_id_array[$val_qty[csf('roll_id')]]=$val_qty[csf('roll_id')];
        $total_cut_qty+=$val_qty[csf('size_qty')];
        $total_size_qty_arr[$val_qty[csf('size_id')]]+=$val_qty[csf('size_qty')];
    }
	// echo "<pre>";
	// print_r($roll_id_array);
    $bundle_array=array();

    $sql_name=sql_select("SELECT b.job_no,b.buyer_name,b.style_ref_no,b.product_dept,a.po_number,a.grouping,a.id from wo_po_details_master b,wo_po_break_down a
	where a.job_id=b.id and a.job_no_mst='".$data[1]."'");
	$grouping_arr = array();
    foreach($sql_name as $value)
    {
        $product_dept_name 						=$value[csf('product_dept')];
        $style_name 							=$value[csf('style_ref_no')];
        $buyer_name 							=$value[csf('buyer_name')];
        $po_number_arr[$value[csf('id')]] 		=$value[csf('po_number')];
        $job_number 		                    =$value[csf('job_no')];
		$grouping_arr[$value[csf('id')]]	    =$value[csf('grouping')];
    }
    $buyer_short_name=return_field_value("short_name","lib_buyer", "id=$buyer_name ");

    $sql_cut_name=sql_select("SELECT a.entry_date, a.table_no, a.cut_num_prefix_no, a.batch_id, a.company_id, a.cutting_no, a.shipment_part,b.roll_data,b.shade  from ppl_cut_lay_mst a, ppl_cut_lay_dtls b  where a.id = b.mst_id and a.id=$data[2]");
	// ======================= batch and shade ==========================
	$roll_sql=sql_select("SELECT id, batch_no,shade from pro_roll_details where entry_form=509 and status_active=1 and mst_id='$mst_id' and dtls_id='$detls_id'");


	$roll_data_arr=array();
	foreach($roll_sql as $row)
	{
		$roll_data_arr[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
		$roll_data_arr[$row[csf("id")]]["shade"]=$row[csf("shade")];
	}



    $roll_data = explode("=", $sql_cut_name[0][csf('roll_data')]);
    $batch_shade = (isset($roll_data[5]) ? $roll_data[5] : "") ."-".(isset($roll_data[6]) ? $roll_data[6] : "");


    foreach($sql_cut_name as $cut_value)
    {
        $ful_cut_no 		=$cut_value[csf('cutting_no')];
        $table_id 			=$cut_value[csf('table_no')];
        $cut_date 			=change_date_format($cut_value[csf('entry_date')]);
        $cut_prifix 		=$cut_value[csf('cut_num_prefix_no')];
        $company_id 		=$cut_value[csf('company_id')];
        $shipment_part 		=$cut_value[csf('shipment_part')];
        // $batch_no 			=$cut_value[csf('batch_id')];
		// $shade              =$cut_value[csf('shade')];
        $comp_name 			=return_field_value("company_short_name","lib_company", "id=$company_id");
        $new_cut_no 		=$comp_name."-".$cut_prifix;
        $bundle_title 		="";
    }
    $table_no=return_field_value("table_no","lib_cutting_table", "id=$table_id ");

    // $roll_data_arr=return_library_array("select roll_id, (batch_no || '-' || shade) as batch_shade from pro_roll_details where entry_form=509 and status_active=1", "roll_id", "batch_shade");


    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$ful_cut_no.DIRECTORY_SEPARATOR;
    $PNG_WEB_DIR = 'qrcode_image/'.$ful_cut_no.'/';
    foreach (glob($PNG_WEB_DIR."*.png") as $filename) {
        @unlink($filename);
    }
    if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
    $filename = $PNG_TEMP_DIR.'test.png';
    $errorCorrectionLevel = 'L';
    $matrixPointSize = 4;

    include "../../../ext_resource/phpqrcode/qrlib.php";
    require_once("../../../ext_resource/mpdf60/mpdf.php");

    $mpdf = new mPDF('',    // mode - default ''
	array(66,44),//array(297,210),		// array(65,210),    // format - A4, for example, default ''
        8,     // font size - default 0
        '',    // default font family
        1,    // margin_left
        1,    // margin right
        1,     // margin top
        1,    // margin bottom
        1,     // margin header
        1,     // margin footer
        'P');
    if($data[7]=="") $data[7]=0;
    $i 		=1;
    $html 	='';
    $total_number_of_bundle=count($color_sizeID_arr);
    $sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
    if(count($sql_bundle_copy)!=0)
    {
        $bundle_arr_main = [];
        foreach($color_sizeID_arr as $val)
        {
            foreach($sql_bundle_copy as $breakData)
            {
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['ID'] = $val[csf('id')];
				$bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['entry_date'] = $val[csf('entry_date')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['barcode_no'] = $val[csf('barcode_no')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['po_number'] = $po_number_arr[$val[csf('order_id')]];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['grouping'] = $grouping_arr[$val[csf('order_id')]];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['country'] = $country_arr[$val[csf('country_id')]];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['roll_id'] = $val['ROLL_ID'];

                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['bundle_use_for'] = $breakData[csf('bundle_use_for')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['color'] = $color_library[$val[csf("color_id")]];
				$bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['color_type_name'] = $color_type[$val[csf("color_type_id")]];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['pattern_no'] = $val[csf("pattern_no")];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['size'] = $size_arr[$val[csf("size_id")]];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['size_qty'] += $val[csf('size_qty')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['number_start'] = $val[csf('number_start')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['number_end'] = $val[csf('number_end')];
                $bundle_arr_main[$val[csf('id')]."**".$breakData[csf('bundle_use_for')]]['bundle_no'] = $val[csf('bundle_no')];
            }
        }

            foreach(array_chunk($bundle_arr_main, 1) as $breakData)
            {
                $mpdf->AddPage('',    // mode - default ''
				array(66,44),//array(297,210),		// array(65,210),    // format - A4, for example, default ''
                    8,     // font size - default 0
                    '',    // default font family
                    1,    // margin_left
                    1,    // margin right
                    1,     // margin top
                    1,    // margin bottom
                    1,     // margin header
                    1,     // margin footer
                    'P');
                $html .= '<style>
                            td, th {
                                border: .4px solid black;
                            }
                        </style>';
                $html .='<table width="100%" border="0" style="border:none;">';
                foreach (array_chunk($breakData, 2) as $rowData) {
                    $html .='<tr style="border:none;">';
                    foreach ($rowData as $val) {
                        $filename = $PNG_TEMP_DIR.'test'.md5($val["barcode_no"]).'.png';
                        QRcode::png($val["barcode_no"], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
                        $po_number=$val['po_number'];
                        $country_name=$val['country'];
                        $bundle_array[$i]=$val["barcode_no"];
                        $html .= '<td style="border:none; "><table cellpadding="0" cellspacing="0" width="100%" height="100%" class="" style="font-weight:normal;margin:0 auto;font-size:9px;" rules="all" id="" border="1" align="left">
                                <tr>
								<td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">BUYER# ' . substr($buyer_short_name, 0, 16) . '</td>
								<td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;" width="50%" >PO NO# ' . substr($po_number, 0, 16) . ' </td>
                                </tr>
                                <tr>
								<td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">IB# ' . substr($val['grouping'], 0, 16) . '</td>
								<td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">B. PART# ' . substr($val["bundle_use_for"], 0, 15) . '</td>

                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;" width="50%">STY# ' . substr($style_name, 0, 20) . ' </td>
									<td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;" width="50%">GMTS SIZE# ' . substr($val['size'], 0, 20) . ' </td>

                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">CLR# ' . substr($val["color"], 0, 35) . '</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;" width="">CUT NO# ' . substr($order_cut_no, 0, 16) . ',T# ' . substr($table_no, 0, 16) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">CLR TYP# ' . substr($val["color_type_name"], 0, 16) . ','.substr($garments_item_name, 0,7).'</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;" width="">C.QTY#' . $total_cut_qty_int . ' B.Qty#'.$total_number_of_bundle.  '</td>

                                </tr>

                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">BATCH# ' . substr($roll_data_arr[$val['roll_id']]['batch_no'], 0, 16) . ',S# ' . substr($roll_data_arr[$val['roll_id']]['shade'], 0, 16) . '</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">DATE# ' . substr($val["entry_date"], 0, 17) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">BUNDLE PCS# ' . $val["size_qty"] . '</td>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;" width="" rowspan="4" align="center"><div id="div_' . $i . '"><img src="' . $PNG_WEB_DIR . basename($filename) . '" height="60" width="" ></div></td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">SERIAL NO# ' . substr($val["number_start"] . '-' . $val["number_end"], 0, 16) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">B# ' . substr($val['bundle_no'], 0, 18) . '</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1.5px 0px 1.5px 1px;font-weight:bold;font-size:9px;">' . $val["barcode_no"] . '</td>
                                </tr>

                            </table></td>';
                    }
                    $html .='</tr>';
                }
                $html .= "</table>";
                $mpdf->WriteHTML($html);
                $html='';
                $i++;
            }
    }
    //$mpdf->WriteHTML($html);
    foreach (glob("*.pdf") as $filename) {
        @unlink($filename);
    }
    $name = 'lotRatioEntry_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
    $mpdf->Output($name, 'F');
    echo "1###$name";

    exit();

}

if($action=="print_qrcode_operation13")
{
    //echo "1000".$data;die;
    $data=explode("***",$data);
    $detls_id=$data[3];
    $garments_item_name=$garments_item[$data[4]];
    $color_library=return_library_array( "select id, color_name from lib_color where id=$data[5]", "id", "color_name");
    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");

    $color_sizeID_arr=sql_select("SELECT a.id,a.size_id,a.bundle_no,a.barcode_no,a.order_id,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_no,b.bundle_sequence,b.color_id,a.pattern_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b
	where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	order by b.bundle_sequence,a.id");

    foreach($color_sizeID_arr as $val_qty)
    {
        $total_cut_qty+=$val_qty[csf('size_qty')];
        $total_size_qty_arr[$val_qty[csf('size_id')]]+=$val_qty[csf('size_qty')];
    }
    $bundle_array=array();

    $sql_name=sql_select("SELECT b.job_no,b.buyer_name,b.style_ref_no,b.product_dept,a.po_number,a.id
	from wo_po_details_master b,wo_po_break_down a
	where a.job_no_mst=b.job_no and a.job_no_mst='".$data[1]."'");

    foreach($sql_name as $value)
    {
        $product_dept_name 						=$value[csf('product_dept')];
        $style_name 							=$value[csf('style_ref_no')];
        $buyer_name 							=$value[csf('buyer_name')];
        $po_number_arr[$value[csf('id')]] 		=$value[csf('po_number')];
        $job_number 		                    =$value[csf('job_no')];
    }
    $buyer_short_name=return_field_value("short_name","lib_buyer", "id=$buyer_name ");

    $sql_cut_name=sql_select("SELECT entry_date, table_no, cut_num_prefix_no, batch_id, company_id,cutting_no, shipment_part  from ppl_cut_lay_mst where id=$data[2]");

    foreach($sql_cut_name as $cut_value)
    {
        $ful_cut_no 		=$cut_value[csf('cutting_no')];
        $table_id 			=$cut_value[csf('table_no')];
        $cut_date 			=change_date_format($cut_value[csf('entry_date')]);
        $cut_prifix 		=$cut_value[csf('cut_num_prefix_no')];
        $company_id 		=$cut_value[csf('company_id')];
        $shipment_part 		=$cut_value[csf('shipment_part')];
        $batch_no 			=$cut_value[csf('batch_id')];
        $comp_name 			=return_field_value("company_short_name","lib_company", "id=$company_id");
        $new_cut_no 		=$comp_name."-".$cut_prifix;
        $bundle_title 		="";
    }
    $table_no=return_field_value("table_no","lib_cutting_table", "id=$table_id ");

    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR.$ful_cut_no.DIRECTORY_SEPARATOR;
    $PNG_WEB_DIR = 'qrcode_image/'.$ful_cut_no.'/';
    foreach (glob($PNG_WEB_DIR."*.png") as $filename) {
        @unlink($filename);
    }
    if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);
    $filename = $PNG_TEMP_DIR.'test.png';
    $errorCorrectionLevel = 'L';
    $matrixPointSize = 4;
    include "../../../ext_resource/phpqrcode/qrlib.php";
    require_once("../../../ext_resource/mpdf60/mpdf.php");

    $mpdf = new mPDF('',    // mode - default ''
        array(68,39),		// array(65,210),    // format - A4, for example, default ''
        5,     // font size - default 0
        '',    // default font family
        3,    // margin_left
        3,    // margin right
        3,     // margin top
        0,    // margin bottom
        0,     // margin header
        0,     // margin footer
        'P');
    if($data[7]=="") $data[7]=0;
    $i 		=1;
    $html 	='';
    $total_number_of_bundle=count($color_sizeID_arr);
    $sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
    if(count($sql_bundle_copy)!=0)
    {
        foreach($color_sizeID_arr as $val)
        {
            foreach($sql_bundle_copy as $inf)
            {
                $filename = $PNG_TEMP_DIR.'test'.md5($val[csf("barcode_no")]).'.png';
                QRcode::png($val[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
                $po_number=$po_number_arr[$val[csf('order_id')]];
                $country_name=$country_arr[$val[csf('country_id')]];
                $bundle_array[$i]=$val[csf("barcode_no")];

                $mpdf->AddPage('',    // mode - default ''
                    array(68,39),		// array(65,210),    // format - A4, for example, default ''
                    5,     // font size - default 0
                    '',    // default font family
                    0,    // margin_left
                    0,    // margin right
                    0,     // margin top
                    0,    // margin bottom
                    0,     // margin header
                    0,     // margin footer
                    'P');
                $html.='
					<table cellpadding="0" cellspacing="0" width="" class="" style="width:100%; font-weight:bold;margin:0px;font-size:9px;" rules="all" id="" border="1">
						<tr>
			            	<td>LOT NO-SHADE# '.$batch_no.' - </td>
			            	<td colspan="2" width="50%">JOB# '.substr($job_number,0,12).'</td>
			            </tr>
						<tr>
			            	<td>BUYER# '.substr($buyer_short_name,0,12).'</td>
			            	<td width="50%" colspan="2">PO NO# '.substr($po_number,0,28).' </td>
			            </tr>
						<tr>
			            	<td width="50%">STY# '.substr($style_name,0,12).' </td>
			            	<td colspan="2">BODY PART# '.substr($inf[csf("bundle_use_for")],0,12).'</td>
			            </tr>
						<tr>
							<td>COLOR# '.substr($color_library[$val[csf("color_id")]],0,9).'</td>
			            	<td width="28%">GROUP# '.substr($val[csf("pattern_no")],0,5).'</td>
			            	<td width="22%">PART# '.substr($shipment_part,0,5).'</td>
			            </tr>
						<tr>
							<td>BUNDLE SIZE# '.$size_arr[$val[csf("size_id")]].'</td>
			            	<td width="">CUT QTY# '.$total_cut_qty.'</td>
			            	<td width="">CUT NO# '.substr($order_cut_no,0,6).'</td>
			            </tr>
						<tr>
							<td>BUNDLE PCS# '.$val[csf("size_qty")].'</td>
			            	<td width="" colspan="2" rowspan="4"><div id="div_'.$i.'"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="60" width="" ></div></td></td>
			            </tr>
						<tr>
							<td>SERIAL NO# '.substr($val[csf("number_start")].'-'.$val[csf("number_end")],0,12).'</td>
			            </tr>
						<tr>
							<td>BUNDLE NO# '.substr($val[csf('bundle_no')],0,12).'</td>
			            </tr>
						<tr>
							<td>'.$val[csf("barcode_no")].'</td>
			            </tr>
						<tr>
							<td align="right" colspan="3">Page'.$i.'</td>
			            </tr>
				    </table>';

                $mpdf->WriteHTML($html);
                $html='';
                $i++;
            }
        }
    }
    else
    {
        foreach($color_sizeID_arr as $val)
        {

            $filename = $PNG_TEMP_DIR.'test'.md5($val[csf("barcode_no")]).'.png';
            QRcode::png($val[csf("barcode_no")], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
            $country_name=$country_arr[$val[csf('country_id')]];
            $po_number=$po_number_arr[$val[csf('order_id')]];
            $bundle_array[$i]=$val[csf("barcode_no")];

            $mpdf->AddPage('',    // mode - default ''
                array(60,70),		// array(65,210),    // format - A4, for example, default ''
                5,     // font size - default 0
                '',    // default font family
                3,    // margin_left
                3,    // margin right
                3,     // margin top
                0,    // margin bottom
                0,     // margin header
                0,     // margin footer
                'L');


            $html.='
				<table cellpadding="0" cellspacing="0" width="" class="" style="font-size:12px; font-weight:bold;margin:0px" rules="all" id="">

					<tr>
						<td>Cut Qty: ('.$total_cut_qty.')</td>
		            	<td width="">'.$inf[csf("bundle_use_for")].'</td>
		            </tr>
		            <tr>
		            	<td width="50%">Table No :'.$table_no.' </td>
		            	<td width="50%">Date :'.$cut_date.'</td>
		            </tr>
		            <tr>
		            	<td>'.$buyer_short_name.'</td>
		            	<td>O:'.$po_number.'</td>
		            </tr>

		            <tr>
		            	<td width="" colspan="2">Style :'.$style_name.' </td>
		            </tr>

		            <tr>
		            	<td width="" colspan="2">Country :'.$country_name.' </td>
		            </tr>

		            <tr>
		            	<td colspan="2">Item :'.$garments_item_name.'</td>
		            </tr>

		            <tr>
		            	<td colspan="2">Color:'.substr($color_library[$data[5]],0,25).'</td>
		            </tr>

		            <tr>
		            	<td>Size : '.$size_arr[$val[csf("size_id")]].'('.$total_size_qty_arr[$val[csf("size_id")]].')</td>
		            	<td>Batch:'.$batch_no.'</td>

		            </tr>

		            <tr>
		            	<td>Gmts. No:'.$val[csf("number_start")].'-'.$val[csf("number_end")].'</td>
		            	<td>Gmts. Qnty:'.$val[csf("size_qty")].'</td>
		            </tr>

		            <tr>
		            	<td></td>
		            	<td align="right">Page '.$i.'</td>
		            </tr>

			    </table>';

            $mpdf->WriteHTML($html);
            $html='';
            $i++;
        }

    }
    //$mpdf->WriteHTML($html);
    foreach (glob("*.pdf") as $filename) {
        @unlink($filename);
    }
    $name = 'lotRatioEntry_' . date('j-M-Y_h-iA') .'_'.$user_id.'.pdf';
    $mpdf->Output($name, 'F');
    echo "1###$name";

    exit();

}


if($action=="print_barcode_eight")
{
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');

	$data=explode("***",$data);
	$detls_id=$data[3];
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	// $sub_process_id=return_field_value2("group_concat(b.sub_process_id)  as sub_process_id ","pro_recipe_entry_dtls b, pro_recipe_entry_mst a",
	$order_cut_no=return_field_value(" order_cut_no ","ppl_cut_lay_dtls"," id=$detls_id and status_active=1 and is_deleted=0 ","order_cut_no");
	//echo $order_cut_no;
	$pdf=new PDF_Code39();
	//$pdf->SetFont('Times', '', 20);
	$pdf->AddPage();

	$bundle_id_arr = explode(",", $data[0]);
	$bundle_id_cond = where_con_using_array($bundle_id_arr,0,"a.id");
	$color_sizeID_arr=sql_select("SELECT a.id,a.size_id,a.bundle_no,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_id,a.roll_no,b.bundle_sequence, a.pattern_no,a.barcode_no,a.order_id
	from ppl_cut_lay_bundle  a,ppl_cut_lay_size_dtls b
	where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id $bundle_id_cond and a.mst_id=$data[2] order by b.bundle_sequence,a.id");
	$order_id_arr = array();
	foreach($color_sizeID_arr as $val)
	{
		$order_id_arr[$val['order_id']] = $val['order_id'];
	}
	$order_ids = implode(",", $order_id_arr);
	$i=10; $j=12; $k=0;
    $bundle_array=array();
	$br=0;
	$n=0;

	$roll_sql=sql_select("select roll_id, batch_no, shade from pro_roll_details where mst_id='$data[2]' and entry_form=509 and status_active=1");
	$roll_data_arr=array();
	foreach($roll_sql as $row)
	{
		$roll_data_arr[$row[csf("roll_id")]]['batch']=$row[csf("batch_no")];
		$roll_data_arr[$row[csf("roll_id")]]['shade']=$row[csf("shade")];
	}

	$sql_name=sql_select("SELECT a.id, b.buyer_name,b.style_ref_no,b.product_dept,a.po_number,a.grouping from wo_po_details_master b,wo_po_break_down a where a.job_id=b.id and a.id in($order_ids)");
	foreach($sql_name as $value)
	{
		$product_dept_name=$value[csf('product_dept')];
		$style_name=$value[csf('style_ref_no')];
		$buyer_name=$value[csf('buyer_name')];
		$po_number_arr[$value[csf('id')]]=$value[csf('po_number')];
		$int_ref_no=$value[csf('grouping')];
	}

	$sql_cut_name=sql_select("SELECT entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		 $table_name=$cut_value[csf('table_no')];
	     $cut_date=change_date_format($cut_value[csf('entry_date')]);
         $cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		 $company_id=$cut_value[csf('company_id')];
		// $batch_no=$cut_value[csf('batch_id')];
		 $comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		 $new_cut_no=$comp_name."-".$cut_prifix;
		 $bundle_title="";
	}

	 	 if($data[7]=="") $data[7]=0;
    	 $sql_bundle_copy=sql_select("SELECT id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
		 $cope_page=1;
		 if(count($sql_bundle_copy)!=0)
		 {
			 foreach($color_sizeID_arr as $val)
			 {
				// bottom Right side page no show
				$pdf->Code40(180, 275, 'Page No: '.$cope_page, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 6);
				foreach($sql_bundle_copy as $inf)
				   {
						if($br==8)
						{
							$cope_page++;
							 $pdf->AddPage(); $br=0; $i=10; $j=12; $k=0;
						}

						if( $k>0 && $k<2 ) { $i=$i+105; }
						 $shade=$roll_data_arr[$val[csf('roll_id')]]['shade'];
						 $batch_no=$roll_data_arr[$val[csf("roll_id")]]['batch'];
						$pdf->Code39($i, $j, $val[csf("barcode_no")]);
						$pdf->Code39($i+58, $j-4, "Bundle Card", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i+58, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i+35, $j+6, "Country: ". substr($country_arr[$val[csf("country_id")]],0,18), $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i, $j+6, "Cut  No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
						$pdf->Code39($i+38, $j+6, "Cut Date	 : ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
						$pdf->Code39($i, $j+11,  "Buyer : ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
						$pdf->Code39($i+38, $j+11,"Ord:".$po_number_arr[$val['order_id']], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
						$pdf->Code39($i, $j+16,  "Style :".substr($style_name,0,80), $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
						$pdf->Code39($i, $j+31,  "Int.Ref :".substr($int_ref_no,0,30), $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
						$pdf->Code39($i+38, $j+21, "Item :  ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
						//$pdf->Code39($i+40, $j+21, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+21, "Size :  ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,15) ;
						// $pdf->Code39($i, $j+26, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+26, "Color	:  ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
					    $pdf->Code39($i+45, $j+31, "S:".$shade, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;

						$pdf->Code39($i+55, $j+31, "T.N:  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
						$pdf->Code39($i+72, $j+31, "B.N: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
						//$pdf->Code39($i+38, $j+36, "Dept : ".$product_dept[$product_dept_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+36, "Gmts. No :  ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
						$pdf->Code39($i, $j+36, "Gmts. Qnty : ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
						$pdf->Code39($i, $j+42, "Order Cut No: ".$order_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
						$pdf->Code39($i+38, $j+42, "Bundle No: ".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,11) ;
					$k++;

					if($k==2)
					{
						$k=0; $i=10; $j=$j+67;
					}
					$br++;

				 }
				// $br=8;

			}
	    }
		else
		{
		   	foreach($color_sizeID_arr as $val)
			{
				if($br==8)
				{
					$cope_page++;
					 $pdf->AddPage(); $br=0; $i=10; $j=12; $k=0;
				}

				if( $k>0 && $k<2 ) { $i=$i+105; }
				$shade=$roll_data_arr[$val[csf('roll_id')]]['shade'];
				$batch_no=$roll_data_arr[$val[csf("roll_id")]]['batch'];
				$pdf->Code39($i, $j, $val[csf("barcode_no")]);
				$pdf->Code39($i+58, $j-4, "Bundle Card", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
				$pdf->Code39($i+58, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
				$pdf->Code39($i+35, $j+6, "Country: ". substr($country_arr[$val[csf("country_id")]],0,18), $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
				$pdf->Code39($i, $j+6, "Cut  No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+38, $j+6, "Cut Date	 : ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+11,  "Buyer : ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+38, $j+11,"Ord:".$po_number_arr[$val['order_id']], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+16,  "Style :".substr($style_name,0,12), $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+38, $j+16,  "Int.Ref :".substr($int_ref_no,0,12), $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+38, $j+21, "Item :  ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				//$pdf->Code39($i+40, $j+21, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+21, "Size :  ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,14) ;
				// $pdf->Code39($i, $j+26, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+26, "Color	:  ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			    $pdf->Code39($i+38, $j+31, "Shade:".$shade, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;

				$pdf->Code39($i+60, $j+31, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+31, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				//$pdf->Code39($i+38, $j+36, "Dept : ".$product_dept[$product_dept_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+38, $j+36, "Gmts. No :  ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+36, "Gmts. Qnty : ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+42, "Order Cut No: ".$order_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+38, $j+42, "Bundle No: ".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$k++;

				if($k==2)
				{
					$k=0; $i=10; $j=$j+67;
				}
				$br++;

			}
		}
	foreach (glob(""."*.pdf") as $filename) {
			@unlink($filename);
		}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

//Sticker 9/Page
if($action=="print_barcode_nine")
{
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');

	$data=explode("***",$data);

	$detls_id=$data[3];
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	// $sub_process_id=return_field_value2("group_concat(b.sub_process_id)  as sub_process_id ","pro_recipe_entry_dtls b, pro_recipe_entry_mst a",
	$order_cut_no=return_field_value(" order_cut_no ","ppl_cut_lay_dtls"," id=$detls_id and status_active=1 and is_deleted=0 ","order_cut_no");

	$pdf=new PDF_Code39();
	//$pdf->SetFont('Times', '', 10);
	$pdf->AddPage();

	$bundle_id_arr = explode(",", $data[0]);
	$bundle_id_cond = where_con_using_array($bundle_id_arr,0,"a.id");
	$color_sizeID_arr=sql_select("SELECT a.id,a.size_id,a.bundle_no,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_id,a.roll_no,b.bundle_sequence, a.pattern_no,a.barcode_no ,a.order_id
	from ppl_cut_lay_bundle  a,ppl_cut_lay_size_dtls b
	where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id $bundle_id_cond and a.mst_id=$data[2] order by b.bundle_sequence,a.id");
	//echo "<pre> $color_sizeID_arr";die;


	$roll_sql=sql_select("select roll_id, batch_no, shade from pro_roll_details where mst_id='$data[2]' and entry_form=509 and status_active=1");
	$roll_data_arr=array();
	foreach($roll_sql as $row)
	{
		$roll_data_arr[$row[csf("roll_id")]]['batch']=$row[csf("batch_no")];


	}
//echo "<pre>";print_r($roll_data_arr);die;
	$i=0; $j=2; $k=0; $bundle_array=array();


	$sql_cut_name=sql_select("SELECT job_no,entry_date,table_no,cut_num_prefix_no,batch_id,company_id,remarks from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		 $table_name=$cut_value[csf('table_no')];
	     $cut_date=change_date_format($cut_value[csf('entry_date')]);
         $cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		 $company_id=$cut_value[csf('company_id')];
		 $batch_no=$cut_value[csf('batch_id')];
		 $remarks=$cut_value[csf('remarks')];
		 $job_no=$cut_value[csf('job_no')];
		 $comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		 $new_cut_no=$comp_name."-".$cut_prifix;
		 $bundle_title="";
	}

	$sql_name=sql_select("SELECT b.buyer_name,b.style_ref_no,b.product_dept,a.id,a.po_number,a.grouping from wo_po_details_master b,wo_po_break_down a where a.job_id=b.id and b.job_no='$job_no'");
	//echo "<pre> $sql_name";
	$internal_ref_no_array=array();
	foreach($sql_name as $value)
	{
		$product_dept_name=$value[csf('product_dept')];
		$style_name=$value[csf('style_ref_no')];
		$buyer_name=$value[csf('buyer_name')];
		$po_number=$value[csf('po_number')];
		$internal_ref_no_array[$value[csf('id')]]=$value[csf('grouping')];

	}
//	echo "<pre>";print_r($internal_ref_no_array);die;

	 	 if($data[7]=="") $data[7]=0;
    	 $sql_bundle_copy=sql_select("SELECT id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");


		$x=6;
	    $y=6;
		$z=22;

		 $cur_page = 1;
		 if(count($sql_bundle_copy)!=0)
		 {
			foreach( $sql_bundle_copy as $inf)
			{
				if($br==11 && $cur_page!=1)
				{
					$x=6;
					$y=6;
					$z=22;
				}
				/* $x=6;
				$y=6;
				$z=22;
				if($cur_page!=1)
				{
					$pdf->AddPage(); $br=0; $i=0; $j=2; $k=0;$x=16;$y=16;$z=22;

				} */
				foreach($color_sizeID_arr as $val)
				{

					/* if($br==11) {
						$pdf->Code39($i+170, $j+6, "Page :" .$cur_page, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,9) ;

					} */

					if($br==11) { $pdf->AddPage(); $br=0; $i=0; $j=2; $k=0;$x=6;$y=6;$z=22;

						$cur_page++;

					}


				//horizontal line
				$pdf -> Line(40, $x, 195, $x);
				$x = $x+4;
				$pdf -> Line(40, $x, 195, $x);
				$x = $x+4;
				$pdf -> Line(40, $x, 195, $x);
				$x = $x+4;
				$pdf -> Line(40, $x, 195, $x);
				$x = $x+4;
				$pdf -> Line(40, $x, 195, $x);

				// verticale line
				$pdf -> Line(40, $y, 40, $z); // left border
				$pdf -> Line(60, $y, 60, $z);
				$pdf -> Line(90, $y, 90, $z);
				$pdf -> Line(110, $y, 110, $z);
				$pdf -> Line(140, $y, 140, $z);
				$pdf -> Line(160, $y, 160, $z);

				$pdf -> Line(195, $y, 195, $z); // right border
				$y=$y+25;
				$z=$z+25;

						$pdf->Code39($i+40.5, $j-7, "Remarks : ", $ext = true, $cks = false, $w = 0.5, $h = 10, $wide = true, true,8);
						$pdf->Code39($i+62, $j-7,$remarks, $ext = true, $cks = false, $w = 0.5, $h = 10, $wide = true, true,8);

						$pdf->Code39($i+90.5, $j-7, "Size :", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,9) ;

						$pdf->Code39($i+112, $j-7,$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,9) ;


						$pdf->Code39($i+140.5, $j-0, "Body Part:",  $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true,8) ;

						$pdf->Code39($i+165, $j-0,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true,8) ;

							$pdf->Code39($i+40.5, $j-2.8, "Cut Sys No: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;
							$pdf->Code39($i+62, $j-2.8,$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+90.5, $j-2.8, "Gmts. Qnty/NO : ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,7) ;

							$pdf->Code39($i+112, $j-2.8,$val[csf("size_qty")]."/".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;


							$pdf->Code39($i+140.5, $j-1.2,  "Int Ref  :  ", $ext = true, $cks = false, $w = 0.2, $h = 8, $wide = true, true,8) ;
							$pdf->Code39($i+165, $j-1.2,	$internal_ref_no_array[$val['ORDER_ID']], $ext = true, $cks = false, $w = 0.2, $h = 8, $wide = true, true,8) ;


							$pdf->Code39($i+40.5, $j-(0.-1),  "Buyer : ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+62,$j-(0.-1),$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+90.5,  $j-(0.-1), "Order Cut No: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+112,  $j-(0.-1),$order_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;


							$pdf->Code39($i+140.5,$j-(0.-1) , "Bundle No: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+165, $j-(0.-1),$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+40.5, $j-(0.-0005), "Color: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+60, $j-(0.-0005), $color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,7) ;

							$pdf->Code39($i+90.5, $j-(0.-0005),"Cut Date: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+111, $j-(0.-0005),$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;


							$pdf->Code39($i+140.5, $j-(0.-0005), "Batch No:", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+165, $j-(0.-0005),$roll_data_arr[$val[csf("roll_id")]]['batch'], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;



							if($br<10)
							{

							$pdf->Code39($i+40, $j+20, "------------------------------------------------------------------------------------------------------------------------------------", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,10) ;
							}


							$k++;
					     	$i=0; $j=$j+25;
						   	$x=$x+9;
						   	$br++;
						   	// if($br==11)
							// {
								$pdf->Code39($i+170, 280, "Page :" .$cur_page, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,9) ;

							// }


					}
					if($br==11)
					{
						$pdf->AddPage(); $br=0; $i=0; $j=2; $k=0;$x=6;$y=6;$z=22;
					}

					if(count($color_sizeID_arr)>11)
					{
						// $cur_page++;
					}

			}

	    }
		else
		{
			foreach( $sql_bundle_copy as $inf)
			{
				if($br==11 && $cur_page!=1)
				{
					$x=6;
					$y=6;
					$z=22;
				}
				/* $x=6;
				$y=6;
				$z=22;
				if($cur_page!=1)
				{
					$pdf->AddPage(); $br=0; $i=0; $j=2; $k=0;$x=16;$y=16;$z=22;

				} */
				foreach($color_sizeID_arr as $val)
				{

					/* if($br==11) {
						$pdf->Code39($i+170, $j+6, "Page :" .$cur_page, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,9) ;

					} */

					if($br==11) { $pdf->AddPage(); $br=0; $i=0; $j=2; $k=0;$x=6;$y=6;$z=22;

						$cur_page++;

					}


				//horizontal line
				$pdf -> Line(40, $x, 195, $x);
				$x = $x+4;
				$pdf -> Line(40, $x, 195, $x);
				$x = $x+4;
				$pdf -> Line(40, $x, 195, $x);
				$x = $x+4;
				$pdf -> Line(40, $x, 195, $x);
				$x = $x+4;
				$pdf -> Line(40, $x, 195, $x);

				// verticale line
				$pdf -> Line(40, $y, 40, $z); // left border
				$pdf -> Line(60, $y, 60, $z);
				$pdf -> Line(90, $y, 90, $z);
				$pdf -> Line(110, $y, 110, $z);
				$pdf -> Line(140, $y, 140, $z);
				$pdf -> Line(160, $y, 160, $z);

				$pdf -> Line(195, $y, 195, $z); // right border
				$y=$y+25;
				$z=$z+25;

						$pdf->Code39($i+40.5, $j-7, "Remarks : ", $ext = true, $cks = false, $w = 0.5, $h = 10, $wide = true, true,8);
						$pdf->Code39($i+62, $j-7,$remarks, $ext = true, $cks = false, $w = 0.5, $h = 10, $wide = true, true,8);

						$pdf->Code39($i+90.5, $j-7, "Size :", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,9) ;

						$pdf->Code39($i+112, $j-7,$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,9) ;


						$pdf->Code39($i+140.5, $j-0, "Body Part:",  $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true,8) ;

						$pdf->Code39($i+165, $j-0,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true,8) ;

							$pdf->Code39($i+40.5, $j-2.8, "Cut Sys No: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;
							$pdf->Code39($i+62, $j-2.8,$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+90.5, $j-2.8, "Gmts. Qnty/NO : ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,7) ;

							$pdf->Code39($i+112, $j-2.8,$val[csf("size_qty")]."/".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;


							$pdf->Code39($i+140.5, $j-1.2,  "Int Ref  :  ", $ext = true, $cks = false, $w = 0.2, $h = 8, $wide = true, true,8) ;
							$pdf->Code39($i+165, $j-1.2,$internal_ref_no, $ext = true, $cks = false, $w = 0.2, $h = 8, $wide = true, true,8) ;


							$pdf->Code39($i+40.5, $j-(0.-1),  "Buyer : ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+62,$j-(0.-1),$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+90.5,  $j-(0.-1), "Order Cut No: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+112,  $j-(0.-1),$order_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;


							$pdf->Code39($i+140.5,$j-(0.-1) , "Bundle No: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+165, $j-(0.-1),$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+40.5, $j-(0.-0005), "Color: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+60, $j-(0.-0005), $color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,7) ;

							$pdf->Code39($i+90.5, $j-(0.-0005),"Cut Date: ", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+111, $j-(0.-0005),$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;


							$pdf->Code39($i+140.5, $j-(0.-0005), "Batch No:", $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;

							$pdf->Code39($i+165, $j-(0.-0005),$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true,8) ;



							if($br<10)
							{

							$pdf->Code39($i+40, $j+20, "------------------------------------------------------------------------------------------------------------------------------------", $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,10) ;
							}


							$k++;
					     	$i=0; $j=$j+25;
						   	$x=$x+9;
						   	$br++;
						   	if($br==11)
							{
								$pdf->Code39($i+170, $j+6, "Page :" .$cur_page, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,9) ;

							}


					}
					if($br==11)
					{
						$pdf->AddPage(); $br=0; $i=0; $j=2; $k=0;$x=6;$y=6;$z=22;
					}

					if(count($color_sizeID_arr)>11)
					{
						$cur_page++;
					}

			}


		}
	foreach (glob(""."*.pdf") as $filename) {
			@unlink($filename);
		}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

if($action=="print_barcode_one_urmi_real")
{
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data=explode("***",$data);
	$detls_id=$data[3];
	$batch_id=return_field_value("batch_id","ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, short_name from lib_country",'id','short_name');
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	//echo $order_cut_no;
	$pdf=new PDF_Code39('P','mm','a10');
	//$pdf=new PDF_Code39();
	//$pdf->SetFont('Arial', '', 1000);
	$pdf->AddPage();


	$bacth_array=array();
	$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach($batchData as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$bacth_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}


	$color_sizeID_arr=sql_select("select a.id, a.size_id, a.bundle_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence,a.id");

	foreach($color_sizeID_arr as $row)
	{
		$test_data[$row[csf("roll_id")]]=$row[csf("roll_id")];
	}
	//print_r($test_data);die;
	//echo $data[6].jahid;die;
	$i=2; $j=2; $k=0; $bundle_array=array(); $br=0; $n=0;
	$sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(".$data[6].")");
	$po_data_arr=array();
	foreach($sql_name as $value)
	{
		/*$product_dept_name=$value[csf('product_dept')];
		$style_name=$value[csf('style_ref_no')];
		$buyer_name=$value[csf('buyer_name')];
		$po_number=$value[csf('po_number')];*/

		$po_data_arr[$value[csf('po_id')]]["style_ref_no"]=$value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"]=$value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["po_number"]=$value[csf('po_number')];

	}
	unset($sql_name);

	$roll_sql=sql_select("select roll_id, batch_no from pro_roll_details where entry_form=509 and status_active=1");
	$roll_data_arr=array();
	foreach($roll_sql as $row)
	{
		$roll_data_arr[$row[csf("roll_id")]]=$row[csf("batch_no")];
	}
	unset($roll_sql);

	//print_r($roll_data_arr);die;

	$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$batch_no=$cut_value[csf('batch_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}
	if($data[7]=="") $data[7]=0;
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($sql_bundle_copy as $inf)
		{
			if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=2; $k=0; }
			foreach($color_sizeID_arr as $val)
			{
				if($br==1)
				{
					$pdf->AddPage(); $br=0; $i=2; $j=2; $k=0;
				}

				//BNDL
				$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no=$roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];

				if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];

				$pdf->Code40($i, $j-2, $buyer_library[$buyer_name]."  COUN# ".$country."  QTY# ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+1.4, "STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")]."  STY# ". $style_name, $ext = true, $cks = false, $w =0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+4.8,$val[csf("bundle_no")]."  PO# ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+8.2, "COLOR# ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
				$pdf->Code40($i, $j+11.6, "PART# ".$inf[csf("bundle_use_for")]."  Batch# ".$batch_no, $ext = true, $cks = false, $w = 0.1, $h = 1, $wide=true,true,8);
				$pdf->Code40($i, $j+15, "CUT & ROLL# ".$order_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.1, $h = 1, $wide = true, true,8) ;
				$pdf->Code39($i, $j+21, $val[csf("bundle_no")]);
				//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);


				$k++;
				$i=2; $j=$j+21;
				/*if($k==2)
				{
					$k=0; $i=10; $j=$j+75;
				}*/

				$br++;
			}
		}
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1)
			{
				$pdf->AddPage(); $br=0; $i=2; $j=2; $k=0;
			}

			$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
			$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
			$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
			$batch_no=$roll_data_arr[$val[csf('roll_id')]];
			$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];
			/*$bundle_no_arr=explode("-",$val[csf("bundle_no")]);
			foreach ( $bundle_no_arr as $key=>$bdl_value)
			{
				if($key>=3) {
					if( $bundle_no_prifix!='') $bundle_no_prifix.="-";
					$bundle_no_prifix.=$bdl_value;
				}
			}*/

			if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];

			$pdf->Code40($i, $j-2, $buyer_library[$buyer_name]."  COUN# ".$country."  QTY# ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+1.4, "STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")]."  STY# ". $style_name, $ext = true, $cks = false, $w =0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+4.8,$val[csf("bundle_no")]."  PO# ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+8.2, "COLOR# ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,8) ;
			$pdf->Code40($i, $j+11.6, "PART# ".$inf[csf("bundle_use_for")]."  Batch# ".$batch_no, $ext = true, $cks = false, $w = 0.1, $h = 1, $wide=true,true,8);
			$pdf->Code40($i, $j+15, "CUT & ROLL# ".$order_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.1, $h = 1, $wide = true, true,8) ;
			//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
			$pdf->Code39($i, $j+21, $val[csf("bundle_no")]);
			$k++;
			$i=2; $j=$j+20;
			/*if($k==2)
			{
				$k=0; $i=10; $j=$j+75;
			}*/

			$br++;
		}
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

if($action=="print_barcode_one_urmi")
{
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no
	// print_r($data);die();
	$data=explode("***",$data);
	$mst_id=$data[2];
	$detls_id=$data[3];
	$batch_id=return_field_value("batch_id","ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, short_name from lib_country",'id','short_name');
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	$pdf=new PDF_Code39('P','mm','a10');
	$pdf->AddPage();


	$bacth_array=array();
	$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach($batchData as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$bacth_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}
	$dynamic_cond=" and a.id in ($data[0]) ";
	// if($data[0]==420)$dynamic_cond=" and a.mst_id in ($mst_id) and a.dtls_id=$detls_id ";

	$color_sizeID_arr=sql_select("SELECT a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id $dynamic_cond
	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no order by b.bundle_sequence,a.id");
	foreach($color_sizeID_arr as $row)
	{
		$test_data[$row[csf("roll_id")]]=$row[csf("roll_id")];
	}
	$i=2; $j=0; $k=0; $bundle_array=array(); $br=0; $n=0;
	$sql_name=sql_select("select b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_matrix,b.season_buyer_wise, a.po_number,a.grouping, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(".$data[6].")");
	$po_data_arr=array();
	foreach($sql_name as $value)
	{
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"]=$value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"]=$value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"]=$value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"]=$value[csf('po_number')];
		$po_data_arr[$value[csf('po_id')]]["grouping"]=$value[csf('grouping')];
		// $matrix_season=$value[csf('season_matrix')];
		$matrix_season=$value[csf('season_buyer_wise')];

	}
	unset($sql_name);

	$roll_sql=sql_select("select roll_id, batch_no from pro_roll_details where entry_form=509 and status_active=1");
	$roll_data_arr=array();
	foreach($roll_sql as $row)
	{
		$roll_data_arr[$row[csf("roll_id")]]=$row[csf("batch_no")];
	}

	unset($roll_sql);
	$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}
	if($data[7]=="") $data[7]=0;
	$seq=explode("," ,$data[7] );
	$seq_first=$seq[0];

	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($sql_bundle_copy as $inf)
		{
			if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=0; $k=0; }
			foreach($color_sizeID_arr as $val)
			{
				if($br==1)
				{
					$pdf->AddPage(); $br=0; $i=2; $j=0; $k=0;
				}
				if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
				//BNDL
				$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id=$po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
				$internal_ref=$po_data_arr[$val[csf('order_id')]]["grouping"];
				$batch_no=$roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];
				$buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_library[$buyer_name].'-'.$buyer_library[$client_id]; else $buyer_name_str=$buyer_library[$buyer_name];
				//$dbl_no="17910000000012";
				if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];

				$pdf->Code40($i, $j-2, $symb." ".$buyer_name_str."  COUN# ".$country."  QTY# ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i, $j+1.2, "STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")]."  STY# ". $style_name, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i, $j+4.4, $val[csf("bundle_no")]."  PO# ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i, $j+7.6, "COLOR# ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i, $j+10.8, $inf[csf("bundle_use_for")]."  B# ".$batch_no."  S# ".$lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide=true,true,7);
				$pdf->Code40($i, $j+14, "CUT & ROLL# ".$order_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i+1.3, $j+17.2, $val[csf("barcode_no")]." IR# ".$internal_ref, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
				//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
				$pdf->Code39($i+1.3, $j+23, $val[csf("barcode_no")]);
				$k++;
				$i=2; $j=$j+23;
				$br++;
			}
		}
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1)
			{
				$pdf->AddPage(); $br=0; $i=2; $j=0; $k=0;
			}
			if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
			$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
			$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
			$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
			$internal_ref=$po_data_arr[$val[csf('order_id')]]["grouping"];
			$batch_no=$roll_data_arr[$val[csf('roll_id')]];
			$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];

			if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];

			$pdf->Code40($i, $j-2, $symb." ".$buyer_library[$buyer_name]."  COUN# ".$country."  QTY# ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+1.2, "STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")]."  STY# ". $style_name, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+4.4, $val[csf("bundle_no")]."  PO# ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+7.6, "COLOR# ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+10.8, $inf[csf("bundle_use_for")]."  B# ".$batch_no."  S# ".$lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide=true,true,7);
			$pdf->Code40($i, $j+14, "CUT & ROLL# ".$order_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i+1.3, $j+17.2, $val[csf("barcode_no")]." IR# ".$internal_ref, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
			$pdf->Code39($i+1.3, $j+23, $val[csf("barcode_no")]);
			$k++;
			$i=2; $j=$j+23;

			$br++;
		}
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}




if($action=="print_barcode_ten_youth_new")
{
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no
	// print_r($data);die();
	$data=explode("***",$data);
	$mst_id=$data[2];
	$detls_id=$data[3];
	$batch_id=return_field_value("batch_id", "ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array("select id,short_name from lib_buyer", "id", "short_name");
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array("select id,table_no from lib_cutting_table", "id", "table_no"  );
	$floor_name_library=return_library_array("select id,floor_name from lib_prod_floor where production_process=1  and status_active =1 and is_deleted=0", "id", "floor_name"  );
	$country_arr=return_library_array("select id, short_name from lib_country", 'id', 'short_name');
	$lib_season_arr=return_library_array("select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0", 'id', 'season_name');
	$order_cut_no=return_field_value("order_cut_no", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "order_cut_no");
	$item_id=return_field_value("gmt_item_id", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "gmt_item_id");
	$pdf=new PDF_Code39('P','mm','a10');
	//$pdf->SetFont('Arial','',8);
	//$pdf->AddPage();

	$bacth_array=array();
	$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach($batchData as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$bacth_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}
	$dynamic_cond=" and a.id in ($data[0]) ";
	// if($data[0]==420)$dynamic_cond=" and a.mst_id in ($mst_id) and a.dtls_id=$detls_id ";

	$color_sizeID_arr=sql_select("SELECT a.id, a.size_id, a.bundle_no, a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id, a.bundle_num_prefix, a.bundle_num_prefix_no, a.color_type_id
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id $dynamic_cond
	group by a.id, a.size_id, a.bundle_no, a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id, a.bundle_num_prefix, a.bundle_num_prefix_no, a.color_type_id order by b.bundle_sequence,a.id");
	/*echo "select a.id, a.size_id, a.bundle_no, a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id, a.bundle_num_prefix, a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id $dynamic_cond
	group by a.id, a.size_id, a.bundle_no, a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id, a.bundle_num_prefix, a.bundle_num_prefix_no order by b.bundle_sequence,a.id";*/

	foreach($color_sizeID_arr as $row)
	{
		$test_data[$row[csf("roll_id")]]=$row[csf("roll_id")];
	}
	$i=2; $j=0; $k=0; $bundle_array=array(); $br=1; $n=0;
	$sql_name=sql_select("select b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_matrix,b.season_buyer_wise, a.po_number,a.grouping, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_id=b.id and a.id in(".$data[6].")");
	$po_data_arr=array();
	foreach($sql_name as $value)
	{
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"]=$value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"]=$value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"]=$value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"]=$value[csf('po_number')];
		$po_data_arr[$value[csf('po_id')]]["grouping"]=$value[csf('grouping')];
		// $matrix_season=$value[csf('season_matrix')];
		$matrix_season=$value[csf('season_buyer_wise')];
	}
	unset($sql_name);

	$roll_sql=sql_select("select roll_id, batch_no from pro_roll_details where entry_form=509 and status_active=1");
	$roll_data_arr=array();
	foreach($roll_sql as $row)
	{
		$roll_data_arr[$row[csf("roll_id")]]=$row[csf("batch_no")];
	}

	unset($roll_sql);
	// $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	$sql_cut_name=sql_select("select a.entry_date,a.table_no,a.cut_num_prefix_no,a.batch_id,a.company_id,b.floor_id from ppl_cut_lay_mst a,lib_cutting_table b where a.table_no=b.id and  a.id=$data[2]");

	foreach($sql_cut_name as $cut_value)
	{
		$table_no=$table_no_library[$cut_value[csf('table_no')]];
		$floor_name=$floor_name_library[$cut_value[csf('floor_id')]];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}

	if($data[7]=="") $data[7]=0;
	$seq=explode("," ,$data[7] );
	$seq_first=$seq[0];
	$counter = 1;
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($sql_bundle_copy as $inf)
		{
			//if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=0; $k=0; }
			foreach($color_sizeID_arr as $val)
			{

				$bundleNo = $val[csf("bundle_no")];
				$tmpBundleArr = explode('-', $bundleNo);
				$bundlePrefix = $tmpBundleArr[0].'-'.$tmpBundleArr[1].'-'.$tmpBundleArr[2];
				$bundleNoNumber = '';

				for ($i=3; $i < count($tmpBundleArr); $i++) {
					$bundleNoNumber .= $tmpBundleArr[$i] . '-';
				}
				$bundleNoNumber = rtrim($bundleNoNumber, '-');

				if($br==1)
				{
					$pdf->AddPage(); $br=0; $i=2; $j=0; $k=0;
				}
				if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
				//BNDL
				$style_name= substr($po_data_arr[$val[csf('order_id')]]["style_ref_no"], 0 , 22);
				$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id=$po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
				$internal_ref=$po_data_arr[$val[csf('order_id')]]["grouping"];
				$batch_no=$roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix_no=$val[csf("bundle_num_prefix_no")];
				$buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_library[$buyer_name].'-'.$buyer_library[$client_id]; else $buyer_name_str=$buyer_library[$buyer_name];
				$colorType = $color_type[$val[csf('color_type_id')]];
				$colorNameTrimmed = substr($color_library[$data[5]], 0, 35);
				if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];

				//$i = $br > 5 ? 109 : 7;
				//$j = $br == 6 ? 0 : $j;

				$pdf->Code40($i, $j-2, $symb." ".$buyer_name_str."  QTY# ".$val[csf("size_qty")] . "  STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")].", Itm#".$garments_item[$item_id], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				// $pdf->Code40($i, $j+4.4, $val[csf("bundle_no")]."  PO# ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,7) ;
				$pdf->Code40($i, $j+1, "PO# ".$po_number, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j+3.7, $bundlePrefix . '-', $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 8);
				$pdf->Code40($i+25, $j+3.7, $bundleNoNumber, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 8);
				$pdf->Code40($i+33, $j+3.7, "STY# ". $style_name, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 7);
				// $pdf->Code40($i+30, $j+15.4, , $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 9);
				$pdf->Code40($i, $j+6, "COLOR# ".$colorNameTrimmed, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j+8.2, $inf[csf("bundle_use_for")]."  B# ".$batch_no."  S# ".$lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide=true,true, 7);
				$pdf->Code40($i, $j+10.6, "CUT & ROLL# ".$order_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true, 7);
				$pdf->Code40($i, $j+13, "IR# ".$internal_ref." TAB# ".$table_no." CLT# ".$colorType." ".$floor_name, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true, 7);
				//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
				$pdf->Code39($i, $j+19, $val[csf("barcode_no")]);
				// $pdf->Code39($i+1.3, $j+30, '');
				$j+=55;
				$k++;
				// $i=2;
				$br++;
			}
		}
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1)
			{
				$pdf->AddPage(); $br=0; $i=2; $j=0; $k=0;
			}
			if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
			$style_name= substr($po_data_arr[$val[csf('order_id')]]["style_ref_no"], 0 , 22);
			$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
			$client_id=$po_data_arr[$val[csf('order_id')]]["client_id"];
			$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
			$internal_ref=$po_data_arr[$val[csf('order_id')]]["grouping"];
			$batch_no=$roll_data_arr[$val[csf('roll_id')]];
			$bundle_no_prifix_no=$val[csf("bundle_num_prefix_no")];
			$buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_library[$buyer_name].'-'.$buyer_library[$client_id]; else $buyer_name_str=$buyer_library[$buyer_name];
			$colorType = $color_type[$val[csf('color_type_id')]];
			$colorNameTrimmed = substr($color_library[$data[5]], 0, 35);
			if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];

			//$i = $br > 5 ? 109 : 7;
			//$j = $br == 6 ? 0 : $j;

			$pdf->Code40($i, $j-2, $symb." ".$buyer_name_str."  QTY# ".$val[csf("size_qty")] . "  STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")].", Itm#".$garments_item[$item_id], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
			// $pdf->Code40($i, $j+4.4, $val[csf("bundle_no")]."  PO# ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+1, "PO# ".$po_number, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i, $j+3.7, $bundlePrefix . '-', $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 8);
			$pdf->Code40($i+25, $j+3.7, $bundleNoNumber, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 8);
			$pdf->Code40($i+33, $j+3.7, "STY# ". $style_name, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 7);
			// $pdf->Code40($i+30, $j+15.4, , $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 9);
			$pdf->Code40($i, $j+6, "COLOR# ".$colorNameTrimmed, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i, $j+8.2, $inf[csf("bundle_use_for")]."  B# ".$batch_no."  S# ".$lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide=true,true, 7);
			$pdf->Code40($i, $j+10.6, "CUT & ROLL# ".$order_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true, 7);
			$pdf->Code40($i, $j+13, "IR# ".$internal_ref." TAB# ".$table_no." CLT# ".$colorType." ".$floor_name, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true, 7);
			//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
			$pdf->Code39($i, $j+19, $val[csf("barcode_no")]);
			// $pdf->Code39($i+1.3, $j+30, '');
			$j+=55;
			$k++;
			// $i=2;
			$br++;
		}
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}


if($action=="print_barcode_ten_urmi")
{
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no
	// print_r($data);die(); Page No:
	$data=explode("***",$data);
	$mst_id=$data[2];
	$detls_id=$data[3];
	$batch_id=return_field_value("batch_id","ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, short_name from lib_country",'id','short_name');
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	$pdf=new PDF_Code39('P','mm','A4');
	$pdf->AddPage();


	$bacth_array=array();
	$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach($batchData as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$bacth_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}
	$dynamic_cond=" and a.id in ($data[0]) ";
	// if($data[0]==420)$dynamic_cond=" and a.mst_id in ($mst_id) and a.dtls_id=$detls_id ";

	$color_sizeID_arr=sql_select("SELECT a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id $dynamic_cond
	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no order by b.bundle_sequence,a.id");
	foreach($color_sizeID_arr as $row)
	{
		$test_data[$row[csf("roll_id")]]=$row[csf("roll_id")];
	}
	$i=2; $j=0; $k=0; $bundle_array=array(); $br=0; $n=0;
	$sql_name=sql_select("select b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_matrix,b.season_buyer_wise, a.po_number,a.grouping, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(".$data[6].")");
	$po_data_arr=array();
	foreach($sql_name as $value)
	{
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"]=$value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"]=$value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"]=$value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"]=$value[csf('po_number')];
		$po_data_arr[$value[csf('po_id')]]["grouping"]=$value[csf('grouping')];
		// $matrix_season=$value[csf('season_matrix')];
		$matrix_season=$value[csf('season_buyer_wise')];

	}
	unset($sql_name);

	$roll_sql=sql_select("select roll_id, batch_no from pro_roll_details where entry_form=509 and status_active=1");
	$roll_data_arr=array();
	foreach($roll_sql as $row)
	{
		$roll_data_arr[$row[csf("roll_id")]]=$row[csf("batch_no")];
	}

	unset($roll_sql);
	$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}
	if($data[7]=="") $data[7]=0;
	$seq=explode("," ,$data[7] );
	$seq_first=$seq[0];
	$counter = 1;
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($sql_bundle_copy as $inf)
		{
			if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=0; $k=0; }
			foreach($color_sizeID_arr as $val)
			{
				if($br==10)
				{
					$pdf->AddPage(); $br=0; $i=2; $j=0; $k=0;
				}
				if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
				//BNDL
				$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id=$po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
				$internal_ref=$po_data_arr[$val[csf('order_id')]]["grouping"];
				$batch_no=$roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];
				$buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_library[$buyer_name].'-'.$buyer_library[$client_id]; else $buyer_name_str=$buyer_library[$buyer_name];
				//$dbl_no="17910000000012";
				if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];
				if($counter % 2==0)
				{
					$pdf->Code40($i+140, $j+13, $symb." ".$buyer_name_str."  COUN# ".$country."  QTY# ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
					$pdf->Code40($i+140, $j+16.2, "STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")]."  STY# ". $style_name, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
					$pdf->Code40($i+140, $j+19.4, $val[csf("bundle_no")]."  PO# ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,7) ;
					$pdf->Code40($i+140, $j+23.6, "COLOR# ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
					$pdf->Code40($i+140, $j+25.8, $inf[csf("bundle_use_for")]."  B# ".$batch_no."  S# ".$lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide=true,true,7);
					$pdf->Code40($i+140, $j+29, "CUT & ROLL# ".$order_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
					$pdf->Code40($i+140, $j+32.2, $val[csf("barcode_no")]." IR# ".$internal_ref, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
					//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
					$pdf->Code39($i+140, $j+38, $val[csf("barcode_no")]);
					// $pdf->Code39($i+1.3, $j+30, '');
					$j=$j+50;
				}
				else
				{
					$pdf->Code40($i+10, $j+13, $symb." ".$buyer_name_str."  COUN# ".$country."  QTY# ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
					$pdf->Code40($i+10, $j+16.2, "STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")]."  STY# ". $style_name, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
					$pdf->Code40($i+10, $j+19.4, $val[csf("bundle_no")]."  PO# ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,7) ;
					$pdf->Code40($i+10, $j+23.6, "COLOR# ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
					$pdf->Code40($i+10, $j+25.8, $inf[csf("bundle_use_for")]."  B# ".$batch_no."  S# ".$lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide=true,true,7);
					$pdf->Code40($i+10, $j+29, "CUT & ROLL# ".$order_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
					$pdf->Code40($i+10, $j+32.2, $val[csf("barcode_no")]." IR# ".$internal_ref, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
					//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
					$pdf->Code39($i+10, $j+38, $val[csf("barcode_no")]);
					// $pdf->Code39($i+1.3, $j+30, '');
				}
				$counter++;
				$k++;
				$i=2;
				$br++;
			}
		}
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1)
			{
				$pdf->AddPage(); $br=0; $i=2; $j=0; $k=0;
			}
			if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
			$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
			$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
			$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
			$internal_ref=$po_data_arr[$val[csf('order_id')]]["grouping"];
			$batch_no=$roll_data_arr[$val[csf('roll_id')]];
			$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];

			if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];

			$pdf->Code40($i, $j-2, $symb." ".$buyer_library[$buyer_name]."  COUN# ".$country."  QTY# ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+1.2, "STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")]."  STY# ". $style_name, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+4.4, $val[csf("bundle_no")]."  PO# ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+7.6, "COLOR# ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+10.8, $inf[csf("bundle_use_for")]."  B# ".$batch_no."  S# ".$lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide=true,true,7);
			$pdf->Code40($i, $j+14, "CUT & ROLL# ".$order_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i+1.3, $j+17.2, $val[csf("barcode_no")]." IR# ".$internal_ref, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
			$pdf->Code39($i+1.3, $j+23, $val[csf("barcode_no")]);
			$k++;
			$i=2; $j=$j+23;

			$br++;
		}
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

if($action=="print_barcode_ten_youth")
{
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no
	// print_r($data);die();
	$data=explode("***",$data);
	$mst_id=$data[2];
	$detls_id=$data[3];
	$batch_id=return_field_value("batch_id", "ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array("select id,short_name from lib_buyer", "id", "short_name");
	$color_library=return_library_array("select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array("select id,table_no from lib_cutting_table", "id", "table_no"  );
	$floor_name_library=return_library_array("select id,floor_name from lib_prod_floor where production_process=1  and status_active =1 and is_deleted=0", "id", "floor_name"  );
	$country_arr=return_library_array("select id, short_name from lib_country", 'id', 'short_name');
	$lib_season_arr=return_library_array("select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0", 'id', 'season_name');
	$order_cut_no=return_field_value("order_cut_no", "ppl_cut_lay_dtls", "id=$detls_id and status_active=1 and is_deleted=0", "order_cut_no");
	$pdf=new PDF_Code39('P', 'mm', 'A4');
	$pdf->AddPage();

	$bacth_array=array();
	$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach($batchData as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$bacth_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}
	$dynamic_cond=" and a.id in ($data[0]) ";
	// if($data[0]==420)$dynamic_cond=" and a.mst_id in ($mst_id) and a.dtls_id=$detls_id ";

	$color_sizeID_arr=sql_select("SELECT a.id, a.size_id, a.bundle_no, a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id, a.bundle_num_prefix, a.bundle_num_prefix_no, a.color_type_id
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id $dynamic_cond
	group by a.id, a.size_id, a.bundle_no, a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id, a.bundle_num_prefix, a.bundle_num_prefix_no, a.color_type_id order by b.bundle_sequence,a.id");
	/*echo "select a.id, a.size_id, a.bundle_no, a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id, a.bundle_num_prefix, a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id $dynamic_cond
	group by a.id, a.size_id, a.bundle_no, a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id, a.bundle_num_prefix, a.bundle_num_prefix_no order by b.bundle_sequence,a.id";*/

	foreach($color_sizeID_arr as $row)
	{
		$test_data[$row[csf("roll_id")]]=$row[csf("roll_id")];
	}
	$i=2; $j=0; $k=0; $bundle_array=array(); $br=1; $n=0;
	$sql_name=sql_select("select b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_matrix,b.season_buyer_wise, a.po_number,a.grouping, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(".$data[6].")");
	$po_data_arr=array();
	foreach($sql_name as $value)
	{
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"]=$value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"]=$value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"]=$value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"]=$value[csf('po_number')];
		$po_data_arr[$value[csf('po_id')]]["grouping"]=$value[csf('grouping')];
		// $matrix_season=$value[csf('season_matrix')];
		$matrix_season=$value[csf('season_buyer_wise')];
	}
	unset($sql_name);

	$roll_sql=sql_select("select roll_id, batch_no from pro_roll_details where entry_form=509 and status_active=1");
	$roll_data_arr=array();
	foreach($roll_sql as $row)
	{
		$roll_data_arr[$row[csf("roll_id")]]=$row[csf("batch_no")];
	}

	unset($roll_sql);
	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	// $sql_cut_name=sql_select("select a.entry_date,a.table_no,a.cut_num_prefix_no,a.batch_id,a.company_id,b.floor_id from ppl_cut_lay_mst a,lib_cutting_table b where a.table_no=b.id and  a.id=$data[2]");


	foreach($sql_cut_name as $cut_value)
	{
		$table_no=$table_no_library[$cut_value[csf('table_no')]];
		// $floor_name=$floor_name_library[$cut_value[csf('floor_id')]];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}

	if($data[7]=="") $data[7]=0;
	$seq=explode("," ,$data[7] );
	$seq_first=$seq[0];
	$counter = 1;
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		$pdf->Code40(50, 1, 'Page No: '.$counter, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 6);
		$pdf->Code40(160, 1, 'Page No: '.$counter, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 6);
		foreach($sql_bundle_copy as $inf)
		{
			// if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=0; $k=0; }
			foreach($color_sizeID_arr as $val)
			{
				$bundleNo = $val[csf("bundle_no")];
				$tmpBundleArr = explode('-', $bundleNo);
				$bundlePrefix = $tmpBundleArr[0].'-'.$tmpBundleArr[1].'-'.$tmpBundleArr[2];
				$bundleNoNumber = '';

				for ($i=3; $i < count($tmpBundleArr); $i++) {
					$bundleNoNumber .= $tmpBundleArr[$i] . '-';
				}
				$bundleNoNumber = rtrim($bundleNoNumber, '-');

				if($br==11)
				{
					$counter++;
					$pdf->AddPage(); $br=1; $i=2; $j=0; $k=0;
					//$pdf->Code40(100, 2, 'Page No: '.$counter, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 6);
					$pdf->Code40(50, 1, 'Page No: '.$counter, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 6);
					$pdf->Code40(160, 1, 'Page No: '.$counter, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 6);
				}
				if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
				//BNDL
				$style_name= substr($po_data_arr[$val[csf('order_id')]]["style_ref_no"], 0 , 22);
				$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id=$po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
				$internal_ref=$po_data_arr[$val[csf('order_id')]]["grouping"];
				$batch_no=$roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix_no=$val[csf("bundle_num_prefix_no")];
				$buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_library[$buyer_name].'-'.$buyer_library[$client_id]; else $buyer_name_str=$buyer_library[$buyer_name];
				$colorType = $color_type[$val[csf('color_type_id')]];
				$colorNameTrimmed = substr($color_library[$data[5]], 0, 35);
				if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];

				$i = $br > 5 ? 109 : 7;
				$j = $br == 6 ? 0 : $j;

				$pdf->Code40($i, $j+5, $symb." ".$buyer_name_str."  QTY# ".$val[csf("size_qty")] . "  STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 9);
				// $pdf->Code40($i, $j+10.2, "STY# ". $style_name, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true, 9);
				$pdf->Code40($i, $j+10.2, "PO# ".$po_number, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true, 9);
				$pdf->Code40($i, $j+15.4, $bundlePrefix . '-', $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 9);
				$pdf->Code40($i+25, $j+15.4, $bundleNoNumber, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 10);
				$pdf->Code40($i+33, $j+15.4, "STY# ". $style_name, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 9);
				// $pdf->Code40($i+30, $j+15.4, , $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true, 9);
				$pdf->Code40($i, $j+20.5, "COLOR# ".$colorNameTrimmed, $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true, 9);
				$pdf->Code40($i, $j+25.8, $inf[csf("bundle_use_for")]."  B# ".$batch_no."  S# ".$lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide=true,true, 9);
				$pdf->Code40($i, $j+31, "CUT & ROLL# ".$order_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true, 9);
				$pdf->Code40($i, $j+36.2, "IR# ".$internal_ref." TAB# ".$table_no." CLT# ".$colorType, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true, 9);
				//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
				$pdf->Code39($i, $j+42, $val[csf("barcode_no")]);
				// $pdf->Code39($i+1.3, $j+30, '');
				$j+=55;
				$k++;
				// $i=2;
				$br++;

			}
		}
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1)
			{
				$pdf->AddPage(); $br=0; $i=2; $j=0; $k=0;
			}
			if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
			$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
			$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
			$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
			$internal_ref=$po_data_arr[$val[csf('order_id')]]["grouping"];
			$batch_no=$roll_data_arr[$val[csf('roll_id')]];
			$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];

			if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];

			$pdf->Code40($i, $j-2, $symb." ".$buyer_library[$buyer_name]."  COUN# ".$country."  QTY# ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+1.2, "STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")]."  STY# ". $style_name, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+4.4, $val[csf("bundle_no")]."  PO# ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+7.6, "COLOR# ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+10.8, $inf[csf("bundle_use_for")]."  B# ".$batch_no."  S# ".$lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide=true,true,7);
			$pdf->Code40($i, $j+14, "CUT, Table No & ROLL# ".$order_cut_no.", ".$table_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i+1.3, $j+17.2, $val[csf("barcode_no")]." IR# ".$internal_ref." ".$floor_name, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
			$pdf->Code39($i+1.3, $j+23, $val[csf("barcode_no")]);
			$k++;
			$i=2; $j=$j+23;

			$br++;
		}
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}



if($action=="print_barcode_one_128ddddddddddd")
{
	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data=explode("***",$data);
	$detls_id=$data[3];
	$batch_id=return_field_value("batch_id","ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, short_name from lib_country",'id','short_name');
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	//$pdf=new PDF_Code39('P','mm','a10');
	//$pdf->AddPage();


	$pdf=new PDF_Code128('P','mm','a9');
	$pdf->AddPage();
	$pdf->SetFont('Arial','',8);

	$bacth_array=array();
	$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach($batchData as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$bacth_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}


	$color_sizeID_arr=sql_select("select a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no order by b.bundle_sequence,a.id");
	foreach($color_sizeID_arr as $row)
	{
		$test_data[$row[csf("roll_id")]]=$row[csf("roll_id")];
	}
	$i=2; $j=0; $k=0; $bundle_array=array(); $br=0; $n=0;
	$sql_name=sql_select("select b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_matrix, a.po_number, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(".$data[6].")");
	$po_data_arr=array();
	foreach($sql_name as $value)
	{
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"]=$value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"]=$value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"]=$value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"]=$value[csf('po_number')];
		$matrix_season=$value[csf('season_matrix')];

	}
	unset($sql_name);

	$roll_sql=sql_select("select roll_id, batch_no from pro_roll_details where entry_form=509 and status_active=1");
	$roll_data_arr=array();
	foreach($roll_sql as $row)
	{
		$roll_data_arr[$row[csf("roll_id")]]=$row[csf("batch_no")];
	}

	unset($roll_sql);
	$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}
	if($data[7]=="") $data[7]=0;
	$seq=explode("," ,$data[7] );
	$seq_first=$seq[0];
	$i=2; $j=2; $k=0; $br=0; $n=0;
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($sql_bundle_copy as $inf)
		{
			if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=2; $k=0; }
			foreach($color_sizeID_arr as $val)
			{
				if($br==1)
				{
					$pdf->AddPage(); $br=0; $i=2; $j=2; $k=0;
				}
				if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
				//BNDL
				$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id=$po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no=$roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];
				$buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_library[$buyer_name].'-'.$buyer_library[$client_id]; else $buyer_name_str=$buyer_library[$buyer_name];
				//$dbl_no="17910000000012";
				if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];

				$pdf->SetXY($i, $j);
				$pdf->Write(0, $symb." ".$buyer_name_str."  COUN# ".$country."  QTY# ".$val[csf("size_qty")]);

				$pdf->SetXY($i, $j+3.2);
				$pdf->Write(0, "STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")]."  STY# ". $style_name);
				$pdf->SetXY($i, $j+6.4);
				$pdf->Write(0, $val[csf("bundle_no")]."  PO# ".$po_number);
				$pdf->SetXY($i, $j+9.6);
				$pdf->Write(0, "COLOR# ".$color_library[$data[5]]);

				$pdf->SetXY($i, $j+12.8);
				$pdf->Write(0, $inf[csf("bundle_use_for")]."  B# ".$batch_no."  S# ".$lib_season_arr[$matrix_season]);

				$pdf->SetXY($i, $j+16);
				$pdf->Write(0, "CUT & ROLL# ".$order_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")");

				//$pdf->SetXY($i, $j+14.2);
				//$pdf->Write(0, $val[csf("barcode_no")]);

				//$pdf->Code128($i,$j+25,$val[csf("bundle_no")],40,8);

				$k++;
				//$i=2; $j=$j+25;
				$br++;
			}
		}
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1)
			{
				$pdf->AddPage(); $br=0; $i=2; $j=0; $k=0;
			}
			if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
			$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
			$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
			$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
			$batch_no=$roll_data_arr[$val[csf('roll_id')]];
			$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];

			if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];

			$pdf->Code40($i, $j-2, $symb." ".$buyer_library[$buyer_name]."  COUN# ".$country."  QTY# ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+1.2, "STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")]."  STY# ". $style_name, $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+4.4, $val[csf("bundle_no")]."  PO# ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+7.6, "COLOR# ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i, $j+10.8, $inf[csf("bundle_use_for")]."  B# ".$batch_no."  S# ".$lib_season_arr[$matrix_season], $ext = true, $cks = false, $w = 0.15, $h = 1, $wide=true,true,7);
			$pdf->Code40($i, $j+14, "CUT & ROLL# ".$order_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")", $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			$pdf->Code40($i+1.3, $j+17.2, $val[csf("barcode_no")], $ext = true, $cks = false, $w =0.15, $h = 1, $wide = true, true,7) ;
			//$pdf->Code40($i, $j+22, $val[csf("bundle_no")],$w =0.2, $h = 8);
			$pdf->Code39($i+1.3, $j+23, $val[csf("barcode_no")]);
			$k++;
			$i=2; $j=$j+23;

			$br++;
		}
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

if($action=="print_barcode_one_128")
{
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code128.php');

	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data=explode("***",$data);
	$detls_id=$data[3];
	$batch_id=return_field_value("batch_id","ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, short_name from lib_country",'id','short_name');
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	//echo $order_cut_no;
	//$pdf=new PDF_Code39('P','mm','a10');
	//$pdf=PDF_Code128();
	//$pdf=new PDF_Code39();
	//$pdf->SetFont('Arial', '', 1000);
	//$pdf->AddPage();


	$bacth_array=array();
	$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach($batchData as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$bacth_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}

	//echo "select c.gmt_item_id,c.color_id,a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	//from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls c  where c.id=a.dtls_id and a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	//	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,c.gmt_item_id,c.color_id order by b.bundle_sequence,a.id";die;
	$color_sizeID_arr=sql_select("select c.gmt_item_id,c.color_id,a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls c  where c.id=a.dtls_id and a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,c.gmt_item_id,c.color_id order by b.bundle_sequence,a.id");
	foreach($color_sizeID_arr as $row)
	{
		$test_data[$row[csf("roll_id")]]=$row[csf("roll_id")];
	}

	$sql_name=sql_select("select b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_matrix, a.po_number, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(".$data[6].")");
	$po_data_arr=array();
	foreach($sql_name as $value)
	{
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"]=$value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["product_dept"]=$value[csf('product_dept')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"]=$value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"]=$value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"]=$value[csf('po_number')];
		$matrix_season=$value[csf('season_matrix')];

	}
	unset($sql_name);


	$sql_article=sql_select("select article_number,po_break_down_id,item_number_id,color_number_id,size_number_id,country_id       from wo_po_color_size_breakdown where status_active=1 and is_deleted=0   and po_break_down_id in(".$data[6].")");
	$po_article_data_arr=array();
	foreach($sql_article as $value)
	{
		$po_article_data_arr[$value[csf('po_break_down_id')]][$value[csf('item_number_id')]][$value[csf('country_id')]][$value[csf('color_number_id')]][$value[csf('size_number_id')]]=$value[csf('article_number')];
	}
	unset($sql_article);

	$roll_sql=sql_select("select roll_id, batch_no from pro_roll_details where entry_form=509 and status_active=1");
	$roll_data_arr=array();
	foreach($roll_sql as $row)
	{
		$roll_data_arr[$row[csf("roll_id")]]=$row[csf("batch_no")];
	}

	unset($roll_sql);
	$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}


	$pdf=new PDF_Code128('P','mm','a128');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',8);
	//$pdf->SetRightMargin(0);

	if($data[7]=="") $data[7]=0;
	$seq=explode("," ,$data[7] );
	$i=2; $j=3; $k=0; $bundle_array=array(); $br=0; $n=0;
	$seq_first=$seq[0];
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($sql_bundle_copy as $inf)
		{
			if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=3; $k=0; }
			foreach($color_sizeID_arr as $val)
			{
				if($br==1)
				{
					$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
				}

				$article_no=$po_article_data_arr[$val[csf('order_id')]][$val[csf('gmt_item_id')]][$val[csf('country_id')]][$val[csf('color_id')]][$val[csf('size_id')]];
				if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
				//BNDL
				$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$dep_name=$product_dept[$po_data_arr[$val[csf('order_id')]]["product_dept"]];
				$style_name = $style_name." / ".$dep_name;
				$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id=$po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no=$roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];
				$buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_library[$buyer_name].'-'.$buyer_library[$client_id]; else $buyer_name_str=$buyer_library[$buyer_name];
				if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];



				$pdf->SetXY($i, $j);
				$pdf->Write(0, $symb." ".$buyer_name_str.", ".$country.", QTY# ".$val[csf("size_qty")]);

				$pdf->SetXY($i, $j+3);
				$pdf->Write(0, " STK# ".$val[csf("number_start")]."-".$val[csf("number_end")] ." ". $inf[csf("bundle_use_for")]);//24

				$pdf->SetXY($i, $j+6);
				$pdf->Write(0, " ".$val[csf("bundle_no")] ." B# ".$batch_no);


				$pdf->SetXY($i, $j+9);
				$pdf->Write(0, " STY# ".substr($style_name, 0, 40));//24 $style_name


				$pdf->SetXY($i, $j+12);
				$pdf->Write(0, " Ar.# ".$article_no." ,PO# ".substr($po_number, 0, 35));
				//$pdf->Write(0, " ".$val[csf("bundle_no")] ." B# ".$batch_no);

				$pdf->SetXY($i, $j+15);
				$pdf->Write(0, " Color# ".substr($color_library[$data[5]], 0, 30));//$color_library[$data[5]]

				$pdf->SetXY($i, $j+18);
				$pdf->Write(0, " CUT#  ".$order_cut_no." ROLL# ".$val[csf("roll_no")]." SIZE# ".$size_arr[$val[csf("size_id")]] ."(".$val[csf("pattern_no")].")");

				$pdf->Code128($i+1,$j+21,$val[csf("barcode_no")],50,8);

				$k++;
				$br++;
			}
		}
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1)
			{
				$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
			}

			$article_no=$po_article_data_arr[$val[csf('order_id')]][$val[csf('gmt_item_id')]][$val[csf('country_id')]][$val[csf('color_id')]][$val[csf('size_id')]];
			if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
				//BNDL
				$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id=$po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no=$roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];
				$buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_library[$buyer_name].'-'.$buyer_library[$client_id]; else $buyer_name_str=$buyer_library[$buyer_name];
				//$dbl_no="17910000000012";
				if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];

				$pdf->SetXY($i, $j);
				$pdf->Write(0, $symb." ".$buyer_name_str."  COUN# ".$country."  QTY# ".$val[csf("size_qty")]);

				$pdf->SetXY($i, $j+3);
				//$pdf->Write(0, "STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")]."  STY# ". $style_name);
				$pdf->Write(0, " STK# ".$val[csf("number_start")]."-".$val[csf("number_end")] ." ". $inf[csf("bundle_use_for")]);//24

				$pdf->SetXY($i, $j+6);
				$pdf->Write(0, " ".$val[csf("bundle_no")] ." B# ".$batch_no);

				$pdf->SetXY($i, $j+9);
				$pdf->Write(0, " STY# ".substr($style_name, 0, 40));//24 $style_name

				$pdf->SetXY($i, $j+12);
				$pdf->Write(0, " Ar.# ".$article_no." ,PO# ".substr($po_number, 0, 35));

				$pdf->SetXY($i, $j+15);
				$pdf->Write(0, " Color# ".substr($color_library[$data[5]], 0, 40));//$color_library[$data[5]]

				$pdf->SetXY($i, $j+18);
				$pdf->Write(0, " CUT#  ".$order_cut_no." ROLL# ".$val[csf("roll_no")]." SIZE# ".$size_arr[$val[csf("size_id")]] ."(".$val[csf("pattern_no")].")");

				$pdf->Code128($i+1,$j+22,$val[csf("barcode_no")],50,8);
				$k++;
				$br++;
		}
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

if($action=="print_barcode_one_128_v2")
{
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code128.php');

	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data=explode("***",$data);
	$detls_id=$data[3];
	$batch_id=return_field_value("batch_id","ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, short_name from lib_country",'id','short_name');
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	//echo $order_cut_no;
	//$pdf=new PDF_Code39('P','mm','a10');
	//$pdf=PDF_Code128();
	//$pdf=new PDF_Code39();
	//$pdf->SetFont('Arial', '', 1000);
	//$pdf->AddPage();


	$bacth_array=array();
	$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach($batchData as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$bacth_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}

	//echo "select c.gmt_item_id,c.color_id,a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	//from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls c  where c.id=a.dtls_id and a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	//	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,c.gmt_item_id,c.color_id order by b.bundle_sequence,a.id";die;
	$color_sizeID_arr=sql_select("SELECT c.gmt_item_id,c.color_id,a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls c  where c.id=a.dtls_id and a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,c.gmt_item_id,c.color_id order by b.bundle_sequence,a.id");
	foreach($color_sizeID_arr as $row)
	{
		$test_data[$row[csf("roll_id")]]=$row[csf("roll_id")];
	}

	$sql_name=sql_select("select b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_matrix, a.po_number, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(".$data[6].")");
	$po_data_arr=array();
	foreach($sql_name as $value)
	{
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"]=$value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["product_dept"]=$value[csf('product_dept')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"]=$value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"]=$value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"]=$value[csf('po_number')];
		$matrix_season=$value[csf('season_matrix')];

	}
	unset($sql_name);


	$sql_article=sql_select("select article_number,po_break_down_id,item_number_id,color_number_id,size_number_id,country_id       from wo_po_color_size_breakdown where status_active=1 and is_deleted=0   and po_break_down_id in(".$data[6].")");
	$po_article_data_arr=array();
	foreach($sql_article as $value)
	{
		$po_article_data_arr[$value[csf('po_break_down_id')]][$value[csf('item_number_id')]][$value[csf('country_id')]][$value[csf('color_number_id')]][$value[csf('size_number_id')]]=$value[csf('article_number')];
	}
	unset($sql_article);

	$roll_sql=sql_select("select roll_id, batch_no from pro_roll_details where entry_form=509 and status_active=1");
	$roll_data_arr=array();
	foreach($roll_sql as $row)
	{
		$roll_data_arr[$row[csf("roll_id")]]=$row[csf("batch_no")];
	}

	unset($roll_sql);
	$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}


	$pdf=new PDF_Code128('P','mm','a128');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',8);
	//$pdf->SetRightMargin(0);

	if($data[7]=="") $data[7]=0;
	$seq=explode("," ,$data[7] );
	$i=2; $j=3; $k=0; $bundle_array=array(); $br=0; $n=0;
	$seq_first=$seq[0];
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7]) order by id");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($color_sizeID_arr as $val)
		{
			if($br==1)
			{
				$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
			}
			foreach($sql_bundle_copy as $inf)
			{
				if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=3; $k=0; }

				$article_no=$po_article_data_arr[$val[csf('order_id')]][$val[csf('gmt_item_id')]][$val[csf('country_id')]][$val[csf('color_id')]][$val[csf('size_id')]];
				if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
				//BNDL
				$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$dep_name=$product_dept[$po_data_arr[$val[csf('order_id')]]["product_dept"]];
				$style_name = $style_name." / ".$dep_name;
				$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id=$po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no=$roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];
				$buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_library[$buyer_name].'-'.$buyer_library[$client_id]; else $buyer_name_str=$buyer_library[$buyer_name];
				if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];


				$pdf->SetFont('Arial','B',11);
				$pdf->SetXY($i, $j);
				$pdf->Write(0, " ".$val[csf("bundle_no")] ." B# ".$batch_no." Tbl#".$table_no_library[$table_name]);
				$pdf->SetFont('Arial','B',8);
				$pdf->SetXY($i, $j+3);
				$pdf->Write(0, $symb." ".$buyer_name_str.", ".$country.", QTY# ".$val[csf("size_qty")]);
				$pdf->SetXY($i, $j+6);
				$pdf->Write(0, " STK# ".$val[csf("number_start")]."-".$val[csf("number_end")] ." ". $inf[csf("bundle_use_for")]);//24
				$pdf->SetXY($i, $j+9);
				$pdf->Write(0, " STY# ".substr($style_name, 0, 40));//24 $style_name


				$pdf->SetXY($i, $j+12);
				$pdf->Write(0, " Ar.# ".$article_no." ,PO# ".substr($po_number, 0, 35));
				//$pdf->Write(0, " ".$val[csf("bundle_no")] ." B# ".$batch_no);

				$pdf->SetXY($i, $j+15);
				$pdf->Write(0, " Color# ".substr($color_library[$data[5]], 0, 30));//$color_library[$data[5]]

				$pdf->SetXY($i, $j+18);
				$pdf->Write(0, " CUT#  ".$order_cut_no." ROLL# ".$val[csf("roll_no")]." SIZE# ".$size_arr[$val[csf("size_id")]] ."(".$val[csf("pattern_no")].")");

				$pdf->Code128($i+1,$j+21,$val[csf("barcode_no")],50,8);

				$k++;
				$br++;
			}
		}
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1)
			{
				$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
			}

			$article_no=$po_article_data_arr[$val[csf('order_id')]][$val[csf('gmt_item_id')]][$val[csf('country_id')]][$val[csf('color_id')]][$val[csf('size_id')]];
			if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
				//BNDL
				$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id=$po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no=$roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];
				$buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_library[$buyer_name].'-'.$buyer_library[$client_id]; else $buyer_name_str=$buyer_library[$buyer_name];
				//$dbl_no="17910000000012";
				if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];
				$pdf->SetFont('Arial','B',11);
				$pdf->SetXY($i, $j);
				$pdf->Write(0, " ".$val[csf("bundle_no")] ." B# ".$batch_no." Tbl#".$table_no_library[$table_name]);

				$pdf->SetFont('Arial','B',8);
				$pdf->SetXY($i, $j+3);
				$pdf->Write(0, $symb." ".$buyer_name_str."  COUN# ".$country."  QTY# ".$val[csf("size_qty")]);
				//$pdf->Write(0, "STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")]."  STY# ". $style_name);

				$pdf->SetXY($i, $j+6);
				$pdf->Write(0, " STK# ".$val[csf("number_start")]."-".$val[csf("number_end")] ." ". $inf[csf("bundle_use_for")]);//24

				$pdf->SetXY($i, $j+9);
				$pdf->Write(0, " STY# ".substr($style_name, 0, 40));//24 $style_name

				$pdf->SetXY($i, $j+12);
				$pdf->Write(0, " Ar.# ".$article_no." ,PO# ".substr($po_number, 0, 35));

				$pdf->SetXY($i, $j+15);
				$pdf->Write(0, " Color# ".substr($color_library[$data[5]], 0, 40));//$color_library[$data[5]]

				$pdf->SetXY($i, $j+18);
				$pdf->Write(0, " CUT#  ".$order_cut_no." ROLL# ".$val[csf("roll_no")]." SIZE# ".$size_arr[$val[csf("size_id")]] ."(".$val[csf("pattern_no")].")");

				$pdf->Code128($i+1,$j+22,$val[csf("barcode_no")],50,8);
				$k++;
				$br++;
		}
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}


if($action=="print_barcode_one_128_v3")
{
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code128.php');

	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data=explode("***",$data);
	$detls_id=$data[3];
	$garments_item_name=$garments_item[$data[4]];
	$batch_id=return_field_value("batch_id","ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, short_name from lib_country",'id','short_name');
	$lib_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	//echo $order_cut_no;
	//$pdf=new PDF_Code39('P','mm','a10');
	//$pdf=PDF_Code128();
	//$pdf=new PDF_Code39();
	//$pdf->SetFont('Arial', '', 1000);
	//$pdf->AddPage();


	$bacth_array=array();
	$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach($batchData as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$bacth_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}

	//echo "select c.gmt_item_id,c.color_id,a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	//from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls c  where c.id=a.dtls_id and a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	//	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,c.gmt_item_id,c.color_id order by b.bundle_sequence,a.id";die;
	$color_sizeID_arr=sql_select("SELECT c.gmt_item_id,c.color_id,a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls c  where c.id=a.dtls_id and a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0])
	group by a.id, a.size_id, a.bundle_no,a.barcode_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,c.gmt_item_id,c.color_id order by b.bundle_sequence,a.id");
	foreach($color_sizeID_arr as $row)
	{
		$test_data[$row[csf("roll_id")]]=$row[csf("roll_id")];
	}

	$sql_name=sql_select("SELECT b.job_no_prefix_num, b.buyer_name, b.client_id, b.style_ref_no, b.product_dept, b.season_matrix, a.po_number, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_id=b.id and a.id in(".$data[6].")");
	$po_data_arr=array();
	foreach($sql_name as $value)
	{
		$po_data_arr[$value[csf('po_id')]]["style_ref_no"]=$value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["product_dept"]=$value[csf('product_dept')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"]=$value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["client_id"]=$value[csf('client_id')];
		$po_data_arr[$value[csf('po_id')]]["po_number"]=$value[csf('po_number')];
		$po_data_arr[$value[csf('po_id')]]["job_num"]=$value[csf('job_no_prefix_num')];
		$matrix_season=$value[csf('season_matrix')];

	}
	unset($sql_name);


	$sql_article=sql_select("select article_number,po_break_down_id,item_number_id,color_number_id,size_number_id,country_id       from wo_po_color_size_breakdown where status_active=1 and is_deleted=0   and po_break_down_id in(".$data[6].")");
	$po_article_data_arr=array();
	foreach($sql_article as $value)
	{
		$po_article_data_arr[$value[csf('po_break_down_id')]][$value[csf('item_number_id')]][$value[csf('country_id')]][$value[csf('color_number_id')]][$value[csf('size_number_id')]]=$value[csf('article_number')];
	}
	unset($sql_article);

	$roll_sql=sql_select("select roll_id, batch_no from pro_roll_details where entry_form=509 and status_active=1");
	$roll_data_arr=array();
	foreach($roll_sql as $row)
	{
		$roll_data_arr[$row[csf("roll_id")]]=$row[csf("batch_no")];
	}

	unset($roll_sql);
	$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}


	$pdf=new PDF_Code128('P','mm','a128');
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',8);
	//$pdf->SetRightMargin(0);

	if($data[7]=="") $data[7]=0;
	$seq=explode("," ,$data[7] );
	$i=2; $j=3; $k=0; $bundle_array=array(); $br=0; $n=0;
	$seq_first=$seq[0];
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7]) order by id");
	$is_print_arr = array_filter(explode(",",$data[8]));
	foreach ($is_print_arr as $key => $v)
	{
		$is_print_array[$v]=$v;
	}
	// $is_print_arr = array_flip($is_print_arr);
	$is_emb_arr = array_filter(explode(",",$data[9]));
	foreach ($is_emb_arr as $key => $v)
	{
		$is_emb_array[$v]=$v;
	}
	// $is_emb_arr = array_flip($is_emb_arr);
	// print_r($is_print_array);die;
	if(count($sql_bundle_copy)!=0)
	{
		foreach($sql_bundle_copy as $inf)
		{

			if($br==1)
			{
				$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
			}
			foreach($color_sizeID_arr as $val)
			{
				if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=3; $k=0; }
				// echo $inf[csf("id")];die;
				$article_no=$po_article_data_arr[$val[csf('order_id')]][$val[csf('gmt_item_id')]][$val[csf('country_id')]][$val[csf('color_id')]][$val[csf('size_id')]];
				if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";

				$print_text = ($is_print_array[$inf[csf("id")]]!="") ? "Print" : "";
				$emb_text = ($is_emb_array[$inf[csf("id")]]!="") ? "Emb" : "";

				//BNDL
				$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$dep_name=$product_dept[$po_data_arr[$val[csf('order_id')]]["product_dept"]];
				$style_name = $style_name." / ".$dep_name;
				$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id=$po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
				$job_num=$po_data_arr[$val[csf('order_id')]]["job_num"];
				$batch_no=$roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];
				$buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_library[$buyer_name].'-'.$buyer_library[$client_id]; else $buyer_name_str=$buyer_library[$buyer_name];
				if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];


				$pdf->SetFont('Arial','B',11);
				$pdf->SetXY($i, $j);
				$pdf->Write(0, " ".$val[csf("bundle_no")] ." B# ".$batch_no);
				$pdf->SetFont('Arial','B',10);
				$pdf->SetXY($i, $j+3);
				$pdf->SetFont('Arial','B',8.5);
				$pdf->Write(0,($symb." ".substr($buyer_name_str,0,35).", ".substr($country,0,35).", QTY# ".substr($val[csf("size_qty")],0,30)." T#".substr($table_no_library[$table_name],0,30)));
				$pdf->SetFont('Arial','B',8);
				$pdf->SetXY($i, $j+6);
				$pdf->Write(0, " STK# ".$val[csf("number_start")]."-".$val[csf("number_end")] ." ". $inf[csf("bundle_use_for")]);//24
				$pdf->SetXY($i, $j+9);
				$pdf->Write(0, " STY# ".substr($style_name.", ".$garments_item_name, 0, 40));//24 $style_name


				$pdf->SetXY($i, $j+12);
				$pdf->Write(0, " Job.# ".$job_num." ,PO# ".substr($po_number, 0, 30));
				//$pdf->Write(0, " ".$val[csf("bundle_no")] ." B# ".$batch_no);

				$pdf->SetXY($i, $j+15);
				$pdf->Write(0, " Color# ".substr($color_library[$data[5]], 0, 30));//$color_library[$data[5]]

				$pdf->SetXY($i, $j+18);
				$pdf->Write(0, " CUT#  ".$order_cut_no." ROLL# ".$val[csf("roll_no")]." SIZE# ".$size_arr[$val[csf("size_id")]] ."(".$val[csf("pattern_no")].")");

				$pdf->SetXY($i+1, $j+22);
				if($print_text || $emb_text)
				{
					$pdf->Write(0,"#".chop($print_text.",".$emb_text,","));

				}

				$pdf->Code128($i+1,$j+26,$val[csf("barcode_no")],50,8);

				$k++;
				$br++;
			}
		}
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1)
			{
				$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
			}

			$article_no=$po_article_data_arr[$val[csf('order_id')]][$val[csf('gmt_item_id')]][$val[csf('country_id')]][$val[csf('color_id')]][$val[csf('size_id')]];
			if($seq_first==$inf[csf("id")]) $symb= "@@"; else $symb= "";
				//BNDL
				$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$client_id=$po_data_arr[$val[csf('order_id')]]["client_id"];
				$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
				$job_num=$po_data_arr[$val[csf('order_id')]]["job_num"];
				$batch_no=$roll_data_arr[$val[csf('roll_id')]];
				$bundle_no_prifix=$val[csf("bundle_num_prefix_no")];
				$buyer_name_str=""; if($client_id!=0) $buyer_name_str=$buyer_library[$buyer_name].'-'.$buyer_library[$client_id]; else $buyer_name_str=$buyer_library[$buyer_name];
				//$dbl_no="17910000000012";
				if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];
				$pdf->SetFont('Arial','B',11);
				$pdf->SetXY($i, $j);
				$pdf->Write(0, " ".$val[csf("bundle_no")] ." B# ".$batch_no." T#".$table_no_library[$table_name]);

				$pdf->SetFont('Arial','B',8);
				$pdf->SetXY($i, $j+3);
				$pdf->Write(0, $symb." ".$buyer_name_str."  COUN# ".$country."  QTY# ".$val[csf("size_qty")]);
				//$pdf->Write(0, "STKR# ".$val[csf("number_start")]."-".$val[csf("number_end")]."  STY# ". $style_name);

				$pdf->SetXY($i, $j+6);
				$pdf->Write(0, " STK# ".$val[csf("number_start")]."-".$val[csf("number_end")] ." ". $inf[csf("bundle_use_for")]);//24

				$pdf->SetXY($i, $j+9);
				$pdf->Write(0, " STY# ".substr($style_name.", ".$garments_item_name, 0, 40));//24 $style_name

				$pdf->SetXY($i, $j+12);
				$pdf->Write(0, " Job.# ".$job_num." ,PO# ".substr($po_number, 0, 35));

				$pdf->SetXY($i, $j+15);
				$pdf->Write(0, " Color# ".substr($color_library[$data[5]], 0, 40));//$color_library[$data[5]]

				$pdf->SetXY($i, $j+18);
				$pdf->Write(0, " CUT#  ".$order_cut_no." ROLL# ".$val[csf("roll_no")]." SIZE# ".$size_arr[$val[csf("size_id")]] ."(".$val[csf("pattern_no")].")");

				$pdf->Code128($i+1,$j+22,$val[csf("barcode_no")],50,8);
				$k++;
				$br++;


		}
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();

}

if($action=="print_barcode_one_urmi_40")
{
	//define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('../../../ext_resource/pdf/code128.php');

	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');//order_cut_no

	$data=explode("***",$data);
	$detls_id=$data[3];
	$batch_id=return_field_value("batch_id","ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, short_name from lib_country",'id','short_name');
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	//echo $order_cut_no;
	//$pdf=new PDF_Code39('P','mm','a10');
	//$pdf=PDF_Code128();
	//$pdf=new PDF_Code39();
	//$pdf->SetFont('Arial', '', 1000);
	//$pdf->AddPage();


	$bacth_array=array();
	$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach($batchData as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$bacth_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}


	$color_sizeID_arr=sql_select("select a.id, a.size_id, a.bundle_no, a.number_start, a.number_end, a.size_qty, a.country_id, a.roll_id, a.roll_no, b.bundle_sequence, a.pattern_no, a.is_excess, a.order_id
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence,a.id");
	foreach($color_sizeID_arr as $row)
	{
		$test_data[$row[csf("roll_id")]]=$row[csf("roll_id")];
	}
	//print_r($test_data);die;
	//echo $data[6].jahid;die;
	$i=2; $j=2; $k=0; $bundle_array=array(); $br=0; $n=0;
	$sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number, a.id as po_id from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id in(".$data[6].")");
	$po_data_arr=array();
	foreach($sql_name as $value)
	{
		/*$product_dept_name=$value[csf('product_dept')];
		$style_name=$value[csf('style_ref_no')];
		$buyer_name=$value[csf('buyer_name')];
		$po_number=$value[csf('po_number')];*/

		$po_data_arr[$value[csf('po_id')]]["style_ref_no"]=$value[csf('style_ref_no')];
		$po_data_arr[$value[csf('po_id')]]["buyer_name"]=$value[csf('buyer_name')];
		$po_data_arr[$value[csf('po_id')]]["po_number"]=$value[csf('po_number')];

	}
	unset($sql_name);

	$roll_sql=sql_select("select roll_id, batch_no from pro_roll_details where entry_form=509 and status_active=1");
	$roll_data_arr=array();
	foreach($roll_sql as $row)
	{
		$roll_data_arr[$row[csf("roll_id")]]=$row[csf("batch_no")];
	}
	unset($roll_sql);

	//print_r($roll_data_arr);die;

	$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		//$batch_no=$cut_value[csf('batch_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}



	$pdf=new PDF_Code128('P','mm','a9');
	$pdf->AddPage();
	$pdf->SetFont('Arial','',8);

if($data[7]=="") $data[7]=0;
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($sql_bundle_copy as $inf)
		{
			if($br==1) { $pdf->AddPage(); $br=0; $i=2; $j=2; $k=0; }
			foreach($color_sizeID_arr as $val)
			{
				if($br==1)
				{
					$pdf->AddPage(); $br=0; $i=2; $j=2; $k=0;
				}

				//BNDL
				$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
				$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
				$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
				$batch_no=$roll_data_arr[$val[csf('roll_id')]];

				$bundle_no_arr=explode("-",$val[csf("bundle_no")]);

				if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];

				$pdf->SetXY($i, $j);
				$pdf->Write(0,$buyer_library[$buyer_name]."  COUN# ".$country);

				$pdf->SetXY($i, $j+3);
				$pdf->Write(0,"STYLE# ". $style_name);

				$pdf->SetXY($i, $j+6);
				$pdf->Write(0,"PO# ".$po_number."  BNDL# ".$bundle_no_arr[2]);
				$pdf->SetXY($i, $j+9);
				$pdf->Write(0,"COLOR# ".$color_library[$data[5]]);

				$pdf->SetXY($i, $j+12);
				$pdf->Write(0,"PART# ".$inf[csf("bundle_use_for")]."  Batch# ".$batch_no);

				$pdf->SetXY($i, $j+15);
				$pdf->Write(0,"CUT & ROLL# ".$order_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")");
				$pdf->Code128($i,$j+20,$val[csf("bundle_no")],40,8);
				$i=2; $j=$j+20;
				$k++;
				$br++;
			}
		}
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1)
			{
				$pdf->AddPage(); $br=0; $i=2; $j=2; $k=0;
			}

			$style_name=$po_data_arr[$val[csf('order_id')]]["style_ref_no"];
			$buyer_name=$po_data_arr[$val[csf('order_id')]]["buyer_name"];
			$po_number=$po_data_arr[$val[csf('order_id')]]["po_number"];
			$batch_no=$roll_data_arr[$val[csf('roll_id')]];

			$bundle_no_arr=explode("-",$val[csf("bundle_no")]);
			if($val[csf('is_excess')]==1) $country="EXCESS"; else $country=$country_arr[$val[csf("country_id")]];

			$pdf->SetXY($i, $j);
				$pdf->Write(0,$buyer_library[$buyer_name]."  COUN# ".$country);

				$pdf->SetXY($i, $j+3);
				$pdf->Write(0,"STYLE# ". $style_name);

				$pdf->SetXY($i, $j+6);
				$pdf->Write(0,"PO# ".$po_number."  BNDL# ".$bundle_no_arr[2]);
				$pdf->SetXY($i, $j+9);
				$pdf->Write(0,"COLOR# ".$color_library[$data[5]]);

				$pdf->SetXY($i, $j+12);
				$pdf->Write(0,"PART# ".$inf[csf("bundle_use_for")]."  Batch# ".$batch_no);

				$pdf->SetXY($i, $j+15);
				$pdf->Write(0,"CUT & ROLL# ".$order_cut_no." & ".$val[csf("roll_no")]."  SIZE# ".$size_arr[$val[csf("size_id")]]."(".$val[csf("pattern_no")].")");
				$pdf->Code128($i,$j+20,$val[csf("bundle_no")],40,8);
				$k++;
				$i=2; $j=$j+20;
			/*if($k==2)
			{
				$k=0; $i=10; $j=$j+75;
			}*/

			$br++;
		}
	}


	//$pdf->Output();

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

if($action=="print_barcode_one_pdf")
{
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');

	$data=explode("***",$data);
	$detls_id=$data[3];
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");
	//echo $order_cut_no;
	$pdf=new PDF_Code39('P','mm','a6');
	//$pdf=new PDF_Code39();
	//$pdf->SetFont('Arial', '', 1000);
	$pdf->AddPage();


	$color_sizeID_arr=sql_select("select a.id,a.size_id,a.bundle_no,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_no,b.bundle_sequence
	from ppl_cut_lay_bundle  a,ppl_cut_lay_size_dtls b
	where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence,a.id");

	$i=8; $j=8; $k=0; $bundle_array=array(); $br=0; $n=0;
	$sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	foreach($sql_name as $value)
	{
		$product_dept_name=$value[csf('product_dept')];
		$style_name=$value[csf('style_ref_no')];
		$buyer_name=$value[csf('buyer_name')];
		$po_number=$value[csf('po_number')];
	}

	$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$batch_no=$cut_value[csf('batch_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}
	if($data[7]=="") $data[7]=0;
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($color_sizeID_arr as $val)
		{
			if($br==1) { $pdf->AddPage(); $br=0; $i=8; $j=8; $k=0; }
			foreach($sql_bundle_copy as $inf)
			{
				if($br==1)
				{
					$pdf->AddPage(); $br=0; $i=8; $j=8; $k=0;
				}

				//if( $k>0 && $k<2 ) { $i=$i+105; }
				$pdf->Code39($i, $j, $val[csf("bundle_no")]);
				$pdf->Code39($i+45, $j-4, "Bundle Card", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
				$pdf->Code39($i+45, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
				$pdf->Code39($i+45, $j+6, "Roll No: ". $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
				$pdf->Code39($i, $j+6, "Cut Sys No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+6, "Cut Date: ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+11,  "Buyer: ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+11,"PO: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+16,  "Style Ref: ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+21, "Item: ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+21, "Size: ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+26, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+26, "Color: ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+31, "Bundle No: ".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+31, "Gmts. No: ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+36, "Country: ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+36, "Gmts. Qnty: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;

				$k++;
				$i=8; $j=$j+60;
				/*if($k==2)
				{
					$k=0; $i=10; $j=$j+75;
				}*/

				$br++;
			}
		}
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			if($br==1) { $pdf->AddPage(); $br=0; $i=8; $j=8; $k=0;}

			$pdf->Code39($i, $j, $val[csf("bundle_no")]);
			$pdf->Code39($i+45, $j-4, "Roll No: ". $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
			$pdf->Code39($i+45, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
			$pdf->Code39($i, $j+6, "Cutting No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+6, "Cut Date: ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+11,  "Buyer: ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+11,"PO: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+16,  "Style Ref: ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+21, "Item: ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+21, "Size: ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+26, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+26, "Color: ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+31, "Bundle No: ".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+31, "Gmts. No: ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+36, "Country: ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10,$wide = true,true) ;
			$pdf->Code39($i, $j+36, "Gmts. Qnty: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;

			$i=8; $j=$j+60;

			/*$k++;
			if($k==2)
			{ $k=0; $i=10; $j=$j+75; }*/
			$br++;
		}
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

if($action=="print_barcode_one")
{
	?>
    <style type="text/css" media="print">
       	 p{ page-break-after: always;}
    	</style>
    <?
	$data=explode("***",$data);
	$detls_id=$data[3];
	$batch_id=return_field_value("batch_id","ppl_cut_lay_dtls", "id=$detls_id");
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$order_cut_no=return_field_value("order_cut_no","ppl_cut_lay_dtls","id=$detls_id and status_active=1 and is_deleted=0","order_cut_no");

	$bacth_array=array();
	$batchData=sql_select("select id, batch_no, extention_no from pro_batch_create_mst where entry_form in(0,7,37,66,68)");
	foreach($batchData as $row)
	{
		$ext='';
		if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
		$bacth_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
	}

	$color_sizeID_arr=sql_select("select a.id,a.size_id,a.bundle_no,a.number_start,a.number_end,a.size_qty,a.country_id,a.roll_no,b.bundle_sequence
	from ppl_cut_lay_bundle a, ppl_cut_lay_size_dtls b where a.mst_id=b.mst_id and a.dtls_id=b.dtls_id and a.size_id=b.size_id and a.id in ($data[0]) order by b.bundle_sequence,a.id");

	$bundle_array=array();
	$sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	foreach($sql_name as $value)
	{
		$product_dept_name=$value[csf('product_dept')];
		$style_name=$value[csf('style_ref_no')];
		$buyer_name=$value[csf('buyer_name')];
		$po_number=$value[csf('po_number')];
	}
	$sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	foreach($sql_cut_name as $cut_value)
	{
		$table_name=$cut_value[csf('table_no')];
		$cut_date=change_date_format($cut_value[csf('entry_date')]);
		$cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		$company_id=$cut_value[csf('company_id')];
		$batch_no=$cut_value[csf('batch_id')];
		$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		$new_cut_no=$comp_name."-".$cut_prifix;
		$bundle_title="";
	}

	if($data[7]=="") $data[7]=0;
	$i=1;
	$sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id and id in ($data[7])");
	if(count($sql_bundle_copy)!=0)
	{
		foreach($color_sizeID_arr as $val)
		{
			foreach($sql_bundle_copy as $inf)
			{
				$bundle_array[$i]=$val[csf("bundle_no")];
				echo '<table style="width: 4.0in;" border="0" cellpadding="0" cellspacing="0">';
				$bundle="&nbsp;&nbsp;".$val[csf("bundle_no")];
				$title="Bundle Card<br>".$inf[csf("bundle_use_for")]."<br>"."Roll No: ". $val[csf("roll_no")];
				/*
				$pdf->Code39($i, $j+11,  "Buyer: ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+11,"PO: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+16,  "Style Ref: ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+21, "Item: ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+21, "Size: ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+26, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+26, "Color: ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+31, "Bundle No: ".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+31, "Gmts. No: ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i+40, $j+36, "Country: ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
				$pdf->Code39($i, $j+36, "Gmts. Qnty: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;*/
				echo '<tr><td style="padding-left:5px;padding-top:10px;padding-bottom:5px"><div id="div_'.$i.'"></div>'.$bundle.'</td><td>'.$title.'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Cut Sys No: '.$new_cut_no.'</td><td>Cut Date: '.$cut_date.'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Buyer: '.$buyer_library[$buyer_name].'</td><td>PO: '.$po_number.'</td></tr>';
				echo '<tr><td style="padding-left:5px;" colspan="2">&nbsp;&nbsp;Style Ref: '.$style_name.'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Size: '.$size_arr[$val[csf("size_id")]].'</td><td>Item: '.$garments_item[$data[4]].'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Batch No: '.$bacth_array[$batch_id].'</td><td>Color: '.$color_library[$data[5]].'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Gmts. No: '.$val[csf("number_start")]."-".$val[csf("number_end")].'</td><td>Bundle No: '.$val[csf("bundle_no")].'</td></tr>';
				echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Gmts. Qnty: '.$val[csf("size_qty")].'</td><td>Country: '.$country_arr[$val[csf("country_id")]].'</td></tr>';
				echo '</table><p></p>';
				$i++;
			}
		}
	}
	else
	{
		foreach($color_sizeID_arr as $val)
	   	{
			/*if($br==1) { $pdf->AddPage(); $br=0; $i=8; $j=8; $k=0;}

			$pdf->Code39($i, $j, $val[csf("bundle_no")]);
			$pdf->Code39($i+45, $j-4, "Roll No: ". $val[csf("roll_no")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
			$pdf->Code39($i+45, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
			$pdf->Code39($i, $j+6, "Cutting No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+6, "Cut Date: ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+11,  "Buyer: ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+11,"PO: ".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+16,  "Style Ref: ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+21, "Item: ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+21, "Size: ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+26, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+26, "Color: ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+31, "Bundle No: ".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i, $j+31, "Gmts. No: ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
			$pdf->Code39($i+40, $j+36, "Country: ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10,$wide = true,true) ;
			$pdf->Code39($i, $j+36, "Gmts. Qnty: ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;*/

			$bundle_array[$i]=$val[csf("bundle_no")];
			echo '<table style="width: 4.0in;" border="0" cellpadding="0" cellspacing="0">';
			$bundle="&nbsp;&nbsp;".$val[csf("bundle_no")];
			$title="Roll No: ". $val[csf("roll_no")];
			echo '<tr><td style="padding-left:5px;padding-top:10px;padding-bottom:5px"><div id="div_'.$i.'"></div>'.$bundle.'</td><td>'.$title.'</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Cut Sys No: '.$new_cut_no.'</td><td>Cut Date: '.$cut_date.'</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Buyer: '.$buyer_library[$buyer_name].'</td><td>PO: '.$po_number.'</td></tr>';
			echo '<tr><td style="padding-left:5px;" colspan="2">&nbsp;&nbsp;Style Ref: '.$style_name.'</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Size: '.$size_arr[$val[csf("size_id")]].'</td><td>Item: '.$garments_item[$data[4]].'</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Batch No: '.$bacth_array[$batch_id].'</td><td>Color: '.$color_library[$data[5]].'</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Gmts. No: '.$val[csf("number_start")]."-".$val[csf("number_end")].'</td><td>Bundle No: '.$val[csf("bundle_no")].'</td></tr>';
			echo '<tr><td style="padding-left:5px;">&nbsp;&nbsp;Gmts. Qnty: '.$val[csf("size_qty")].'</td><td>Country: '.$country_arr[$val[csf("country_id")]].'</td></tr>';
			echo '</table><p></p>';
			$i++;
		}
	}

	?>

    <script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		var barcode_array =<? echo json_encode($bundle_array); ?>;
		function generateBarcode( td_no, valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
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
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};

			$("#div_"+td_no).show().barcode(value, btype, settings);
		}

		for (var i in barcode_array)
		{
			generateBarcode(i,barcode_array[i]);
		}
	</script>
    <?
	exit();
}

if($action=="print_report_bundle_barcode")
{
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');
	//require('file://///192.168.11.252/logic_erp_3rd_version/production/requires/html_table.php');
	$data=explode("***",$data);

	//$ext_data=explode("__",$data[1]);
	//$cs_data=explode("__",$data[2]);
	$buyer_library=return_library_array( "select id,short_name from lib_buyer  ", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$pdf=new PDF_Code39();
	//$pdf->SetFont('Times', '', 20);
	$pdf->AddPage();
	$color_sizeID_arr=sql_select( "select id,size_id,bundle_no,number_start,number_end,size_qty,country_id from ppl_cut_lay_bundle where id in ( $data[0] ) " );  //where id in ($data)
	$i=5; $j=10; $k=0;
    $bundle_array=array();
	$br=0;
	$n=0;
	$sql_name=sql_select("select b.buyer_name,b.style_ref_no,b.product_dept,a.po_number from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no and a.id=$data[6]");
	foreach($sql_name as $value)
	{
		 $product_dept_name=$value[csf('product_dept')];
		 $style_name=$value[csf('style_ref_no')];
		 $buyer_name=$value[csf('buyer_name')];
		 $po_number=$value[csf('po_number')];


	}

	 $sql_cut_name=sql_select("select entry_date,table_no,cut_num_prefix_no,batch_id,company_id from ppl_cut_lay_mst where id=$data[2]");
	 foreach($sql_cut_name as $cut_value)
	 {
		 $table_name=$cut_value[csf('table_no')];
	     $cut_date=change_date_format($cut_value[csf('entry_date')]);
         $cut_prifix=$cut_value[csf('cut_num_prefix_no')];
		 $company_id=$cut_value[csf('company_id')];
		 $batch_no=$cut_value[csf('batch_id')];
		 $comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
		 $new_cut_no=$comp_name."-".$cut_prifix;
		 $bundle_title="";

	 }
    	 $sql_bundle_copy=sql_select("select id,bundle_use_for from ppl_bundle_title where company_id=$company_id");

		 $cope_page=1;
		 if(count($sql_bundle_copy)!=0)
		 {
		 foreach($sql_bundle_copy as $inf)
		 {
			if($br==6) { $pdf->AddPage(); $br=0; $i=5; $j=10; $k=0; }
			foreach($color_sizeID_arr as $val)
			   {

					if($br==6)
					{
						 $pdf->AddPage(); $br=0; $i=5; $j=10; $k=0;

					 }

					if( $k>0 && $k<2 ) { $i=$i+100; }

						$pdf->Code39($i, $j, $val[csf("bundle_no")]);
						$pdf->Code39($i+45, $j-4, "Bundle Card ", $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i+45, $j, "Country: ".$country_arr[$val[csf("country_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+45, $j+1,  $inf[csf("bundle_use_for")], $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i, $j+6, "Cutting No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+6, "Cut Date	 : ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+12,  "Buyer : ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+12,"Ord:".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+18,  "Style Ref  :  ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+24, "Item :  ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+30, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+36, "Color	:  ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+30, "Size :  ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+42, "Dept : ".$product_dept[$product_dept_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;

					   $pdf->Code39($i, $j+42, "Bundle No:".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+48, "Gmts. No :  ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+48, "Gmts. Qnty : ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+54, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
					$k++;

					if($k==2)
					{  $k=0; $i=5; $j=$j+90; }
					$br++;
				 }
				 $br=6;
		    $cope_page++;
	    }
		 }
		else
		{
		   foreach($color_sizeID_arr as $val)
			   {
					if($br==6) { $pdf->AddPage(); $br=0; $i=5; $j=10; $k=0; }
					if( $k>0 && $k<2 ) { $i=$i+100; }
						$pdf->Code39($i, $j, $val[csf("bundle_no")]);
						//$pdf->Code39($i+45, $j, "Bundle Card ".$bundle_title, $ext = true, $cks = false, $w = 0.4, $h = 3, $wide = true, true) ;
						$pdf->Code39($i, $j+6, "Cutting No: ".$new_cut_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+6, "Cut Date	 : ".$cut_date, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+12,  "Buyer : ".$buyer_library[$buyer_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+12,"Ord:".$po_number, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+18,  "Style Ref  :  ".$style_name, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+24, "Item :  ".$garments_item[$data[4]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+30, "Table No :  ".$table_no_library[$table_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+36, "Color	:  ".$color_library[$data[5]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+30, "Size :  ".$size_arr[$val[csf("size_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+42, $j+42, "Dept : ".$product_dept[$product_dept_name], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;

					   $pdf->Code39($i, $j+42, "Bundle No:".$val[csf("bundle_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+48, "Gmts. No :  ".$val[csf("number_start")]."-".$val[csf("number_end")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i+38, $j+48, "Gmts. Qnty : ".$val[csf("size_qty")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
						$pdf->Code39($i, $j+54, "Batch No: ".$batch_no, $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
					$k++;

					if($k==2)
					{  $k=0; $i=5; $j=$j+90; }
					$br++;

				}
		}
	foreach (glob(""."*.pdf") as $filename) {
			@unlink($filename);
		}
	$name = 'bundle_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
	exit();
}

//save size and bundle
if($action=="save_update_delete_size")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
    	$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$cutting_qc_no= return_field_value("cutting_qc_no","pro_gmts_cutting_qc_mst","status_active=1 and is_deleted=0 and cutting_no='".$cut_no."'");
		if($cutting_qc_no!="") { echo "201**".$mst_id."**".$dtls_id."**".$cutting_qc_no; disconnect($con); die;}

		$cutData=sql_select("select cut_num_prefix_no, job_no from ppl_cut_lay_mst where cutting_no = '".$cut_no."'");
		$cut_on_prifix=$cutData[0][csf('cut_num_prefix_no')];
		$job_no=$cutData[0][csf('job_no')];

		$plan_qty= return_field_value("sum(CAST(plan_cut_qnty as INT)) as plan_qty","wo_po_color_size_breakdown","po_break_down_id in(".$order_id.") and item_number_id=".$gmt_id." and color_number_id=".$color_id." and status_active=1","plan_qty");

		$total_marker_qty_prev= return_field_value("sum(b.marker_qty) as mark_qty","ppl_cut_lay_dtls a, ppl_cut_lay_size b","a.id=b.dtls_id and b.order_id in(".$order_id.") and a.gmt_item_id=".$gmt_id." and a.color_id=".$color_id." and a.status_active=1 and b.status_active=1 and b.is_deleted=0","mark_qty");

		$sizeRatioArr=array(); $sizeQtyArr=array(); $sizeQtyArrForC=array(); $sizeIdAgainstSeq=array(); $seqDatas='';
		$id_size=return_next_id("id", "ppl_cut_lay_size_dtls", 1);
		$field_array_size="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,bundle_sequence,inserted_by,insert_date";
		for($i=1; $i<=$size_row_num; $i++)
		{
			$hidden_sizef_id="hidden_sizef_id_".$i;
			$txt_layf_balance="txt_layf_balance_".$i;
			$txt_sizef_ratio="txt_sizef_ratio_".$i;
			$txt_sizef_qty="txt_sizef_qty_".$i;
			$bundle_sequence="txt_bundle_".$i;

			if(str_replace("'",'',$$txt_sizef_qty)>0)
			{
				if(str_replace("'",'',$$bundle_sequence)>0)
				{
					$seq=str_replace("'",'',$$bundle_sequence);
				}
				else
				{
					$max_seq++;
					$seq=$max_seq;
				}

				$dataSize=$mst_id.",".$dtls_id.",".$color_id.",".$$hidden_sizef_id.",".$$txt_sizef_ratio.",".$$txt_sizef_qty.",".$seq;
				$data_array_up[$seq]=$dataSize;
				$sizeIdAgainstSeq[$seq]=str_replace("'",'',$$hidden_sizef_id);

				$sizeRatioArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_ratio);
				$sizeQtyArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
				$sizeQtyArrForC[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
				$sizeSeqArr[str_replace("'",'',$$hidden_sizef_id)]=$seq;
			}
		}

		ksort($data_array_up);
		foreach($data_array_up as $sequence=>$data)
		{
			if($data_array_size!="") $data_array_size.= ",";
			$data_array_size.="(".$id_size.",".$data.",".$user_id.",'".$pc_date_time."')";
			$seqDatas.=$id_size."__".$sizeIdAgainstSeq[$sequence]."__".$sequence.",";
			$id_size++;
		}

		$roll_size_arr=array();
		$roll_no_arr=array();
		$rollsizeBl=array();
		$rollDatas=explode("|",$roll_data);
		$rollDtls_id=return_next_id("id", "ppl_cut_lay_roll_dtls",1);
		$field_array_roll_dtls="id,mst_id,dtls_id,roll_id,roll_no,roll_wgt,plies,size_id,size_qty,inserted_by,insert_date";
		foreach($rollDatas as $data)
		{
			$datas=explode("=",$data);
			$roll_no=$datas[1];
			$roll_id=$datas[2];
			$roll_wgt=$datas[3];
			$plies=$datas[4];

			if($roll_id=="" || $roll_id==0) $roll_id=$rollDtls_id;

			foreach($sizeRatioArr as $size_id=>$size_ratio)
			{
				$roll_no_arr[$size_id][$roll_id]=$roll_no;
				$size_qty=$size_ratio*$plies;
				if($data_array_roll_dtls!="") $data_array_roll_dtls.= ",";
				$data_array_roll_dtls.="(".$rollDtls_id.",".$mst_id.",".$dtls_id.",'".$roll_id."','".$roll_no."','".$roll_wgt."','".$plies."','".$size_id."',".$size_qty.",".$user_id.",'".$pc_date_time."')";

				$rollsizeBl[$size_id][$roll_id]=$size_qty;
				$rollPliesArr[$size_id][$roll_id]=$plies;
				$rollDtls_id++;
			}
		}

		$bundle_no_array=array();
		$bundle_id=return_next_id("id", "ppl_cut_lay_bundle",1);

		$field_array_bundle="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,barcode_year,barcode_prifix,barcode_no,number_start,number_end,size_qty,country_type,country_id,roll_id,roll_no,pattern_no,order_id,is_excess,color_type_id,inserted_by,insert_date";

		$bundleData=sql_select("select max(number_end) as last_rmg, max(bundle_num_prefix_no) as last_prefix from ppl_cut_lay_bundle where mst_id=$mst_id");
		$last_rmg_no=$bundleData[0][csf('last_rmg')];
		$last_bundle_no=$bundleData[0][csf('last_prefix')];

		$update_id=''; $tot_marker_qnty_curr=0; $bundle_prif_no=$last_bundle_no; $size_country_array=array(); $country_type_array=array(); $sizeRatioBlArr=array();
		$bundlePrifNo = $last_bundle_no;

		$id=return_next_id("id", "ppl_cut_lay_size", 1);
		$field_array="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,country_type,country_id,excess_perc,order_id,size_wise_repeat,inserted_by,insert_date";
		for($i=1; $i<=$row_num; $i++)
		{
			$txt_size_id="hidden_size_id_".$i;
			$txt_lay_balance="txt_lay_balance_".$i;
			$cboCountryType="cboCountryType_".$i;
			$cboCountry="cboCountry_".$i;
			$excess_perc="txt_excess_".$i;
			$po_id="poId_".$i;
			$txt_size_qty="txt_size_qty_".$i;

			$marker_qty=0;
			$order_id=str_replace("'",'',$$po_id);
			$size_id=str_replace("'",'',$$txt_size_id);
			$lay_balance=str_replace("'",'',$$txt_lay_balance);
			//$size_qty=$sizeQtyArrForC[$size_id];
			$marker_qty=str_replace("'",'',$$txt_size_qty);
			$country_type_array[$order_id][str_replace("'",'',$$cboCountry)]=str_replace("'",'',$$cboCountryType);

			if($marker_qty>0)
			{
				$size_country_array[$size_id][$order_id][str_replace("'",'',$$cboCountry)]+=$marker_qty;
				$tot_marker_qnty_curr+=$marker_qty;

				if($data_array!="") $data_array.= ",";
				$data_array.="(".$id.",".$mst_id.",".$dtls_id.",".$color_id.",".$$txt_size_id.",0,".$marker_qty.",".$$cboCountryType.",".$$cboCountry.",'".$$excess_perc."',".$$po_id.",0,".$user_id.",'".$pc_date_time."')";
				$id=$id+1;
			}
		}
		//echo "10**";
		//print_r($size_country_array);die;
		//echo "10**";echo $txt_plies;die;
		$company_sort_name=explode("-",$cut_no);
		$bundle_per_pcs=str_replace("'","",$bundle_per_pcs);
		asort($sizeSeqArr);
		//foreach($sizeQtyArr as $size_id=>$size_qty)
		foreach($sizeSeqArr as $size_id=>$size_seq)
		{
			$size_ratio=$sizeRatioArr[$size_id]; $size_qty=$sizeQtyArr[$size_id]; $pattern_no="A";
			for($k=1; $k<=$size_ratio; $k++)
			{
				foreach($roll_no_arr[$size_id] as $rollId=>$rollNo)
				{
					$sizeRatioBlArr[$size_id][$pattern_no][$rollId]=$rollPliesArr[$size_id][$rollId];
				}
				$pattern_no++;
			}
		}

		//foreach($sizeQtyArr as $size_id=>$size_qty)
		// $year_id=date('Y',time());
		$cutNo = str_replace("'", "", $cut_no);
		$cutNoEx = explode("-", $cutNo);
		$year_id=$cutNoEx[1];
		if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$barcode_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle","barcode_year=$year_id","suffix_no");
		$barcodeSuffixNo=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle","barcode_year=$year_id","suffix_no");
		//echo $barcode_suffix_no."***";die;
		//echo "10**";
		// ========================== rmg no creation as per variable setting ==============================
		$rmg_no_creation=return_field_value("smv_source","variable_settings_production","company_name=$cbo_company_id and variable_list=39 and is_deleted=0 and status_active=1");
		if($rmg_no_creation=="") $rmg_no_creation=2; else $rmg_no_creation=$rmg_no_creation;
		// echo "10**$rmg_no_creation";die();
		$erange=0;
		if($rmg_no_creation==2) // cutting wise
		{
			$erange=return_field_value("max(number_end) as last_rmg","ppl_cut_lay_bundle","mst_id=$mst_id and dtls_id!=$dtls_id and status_active=1 and is_deleted=0","last_rmg");
		}
		else if($rmg_no_creation==3) // job wise
		{
			$erange=return_field_value("max(a.number_end) as last_rmg","ppl_cut_lay_bundle a, ppl_cut_lay_mst b","a.mst_id=b.id and b.entry_form=509 and b.job_no='$job_id' and a.status_active=1 and a.is_deleted=0","last_rmg");
		}
		else if($rmg_no_creation==4) // order wise
		{
			// $erange=return_field_value("max(a.number_end) as last_rmg","ppl_cut_lay_bundle a, ppl_cut_lay_dtls b","a.dtls_id=b.id and a.order_id in($orderIds) and a.status_active=1 and a.is_deleted=0","last_rmg");
			$sql = "SELECT a.order_id,max(a.number_end) as LAST_RMG from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b where a.dtls_id=b.id and a.order_id in($orderIds) and a.status_active=1 and a.is_deleted=0 group by a.order_id";
			$res = sql_select($sql);
			$order_wise_rmg_no_array = array();
			foreach ($res as $val)
			{
				$order_wise_rmg_no_array[$val['order_id']] = $val['LAST_RMG'];
			}
		}
		$dataArrayBundle = array();
		$dataArrayBundle2 = array();
		foreach($sizeSeqArr as $size_id=>$size_seq)
		{
			if($rmg_no_creation==1 || $erange=="") { $erange=0; }
			$size_ratio=$sizeRatioArr[$size_id];
			$size_qty=$sizeQtyArr[$size_id];
			$pattern_no="A";
			for($k=1; $k<=$size_ratio; $k++)
			{
				if($rmg_no_creation==5) { $erange=0; }
				$plies=$txt_plies;
				$tmp_bl_arr=array();
				foreach($roll_no_arr[$size_id] as $rollId=>$rollNo)
				{
					if($rmg_no_creation==4)
					{
						$erange = $order_wise_rmg_no_array[$order_id];
					}
					foreach($size_country_array[$size_id] as $order_id=>$order_data)
					{
						foreach($order_data as $country_id=>$size_country_qty)
						{
							if($sizeRatioBlArr[$size_id][$pattern_no][$rollId]>0)
							{
								$temp_bal_flag=1;
								$bl_size_qty=$size_country_array[$size_id][$order_id][$country_id];
								if($plies>0 && $tmp_bl_arr[$size_id][$rollId][1]>0 && $bl_size_qty>0)
								{
									$temp_bal_flag=0;
									if($tmp_bl_arr[$size_id][$rollId][1]>$bl_size_qty)
									{
										$bundle_qty2=$bl_size_qty;
										$tmp_bl_arr[$size_id][$rollId][1]-=$bundle_qty2;
										$tmp_bl_arr[$size_id][$rollId][2]-=$bundle_qty2;
										$bl_roll_plies=$tmp_bl_arr[$size_id][$rollId][1];
									}
									else
									{
										$bundle_qty2=$tmp_bl_arr[$size_id][$rollId][1];
										$tmp_bl_arr[$size_id][$rollId][1]=0;
										$tmp_bl_arr[$size_id][$rollId][2]=0;
										$bl_roll_plies=0;
									}

									$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
									$bundle_prif_no=$bundle_prif_no+1;
									$bundle_no=$bundle_prif."-".$bundle_prif_no;
									$srange=$erange+1;
									$erange=$srange+$bundle_qty2-1;
									$tot_bundle_qty+=$bundle_qty2;

									$barcode_suffix_no=$barcode_suffix_no+1;
									$barcode_no=$year_id."509".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);

									//$bl_size_qty-=$bundle_qty2;
									$size_country_array[$size_id][$order_id][$country_id]-=$bundle_qty2;
									$plies-=$bundle_qty2;

									$country_type=$country_type_array[$order_id][$country_id];

									if($data_array_bundle!="") $data_array_bundle.= ",";
									$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$year_id."','".$barcode_suffix_no."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."','".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
									$bundle_id=$bundle_id+1;
									//$bl_roll_plies=$rollPliesArr[$size_id][$rollId]-($bundle_qty2+$tmp_bl_arr[$size_id][$rollId][2]);
									// $tmp_bl_arr[$size_id][$rollId][2]=0;
									$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty2;

									//tmp solution
									//do not delete
									/*
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['bundle_id'] = $bundle_id;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['mst_id'] = $mst_id;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['dtls_id'] = $dtls_id;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['size_id'] = $size_id;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['year_id'] = $year_id;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['country_type'] = $country_type;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['country_id'] = $country_id;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['rollId'] = $rollId;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['rollNo'] = $rollNo;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['pattern_no'] = $pattern_no;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['order_id'] = $order_id;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['color_type_id'] = $color_type_id;

									$dataArrayBundle2[$size_id]['bundle_qty'] += $bundle_qty2;
									$dataArrayBundle2[$size_id]['bundle_per_pcs'] = $bundle_per_pcs;
									*/
								}
								else
								{
									$bl_roll_plies=$rollPliesArr[$size_id][$rollId];
								}

								//$bl_size_qty=$size_country_array[$size_id][$country_id];
								if($plies>0 && $bl_roll_plies>0 && $bl_size_qty>0 && $temp_bal_flag==1)
								{
									if($bl_roll_plies>=$bundle_per_pcs)
									{
										$bundle_per_size=ceil($bl_roll_plies/$bundle_per_pcs);
										for($z=1; $z<=$bundle_per_size; $z++)
										{
											$bl_size_qty=$size_country_array[$size_id][$order_id][$country_id];
											if($bl_size_qty>0 && $sizeRatioBlArr[$size_id][$pattern_no][$rollId]>0)
											{
												if($bl_roll_plies>$bundle_per_pcs)
												{
													$bundle_qty=$bundle_per_pcs;
												}
												else
												{
													$bundle_qty=$bl_roll_plies;
												}

												if($bundle_qty>$bl_size_qty)
												{
													$bundle_qty=$bl_size_qty;
												}

												if($bundle_qty>$plies)
												{
													$bundle_qty=$plies;
												}

												if($bundle_qty>$sizeRatioBlArr[$size_id][$pattern_no][$rollId])
												{
													$bundle_qty=$sizeRatioBlArr[$size_id][$pattern_no][$rollId];
												}

												$bl_roll_plies-=$bundle_qty;

												//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
												$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
												$bundle_prif_no=$bundle_prif_no+1;
												$bundle_no=$bundle_prif."-".$bundle_prif_no;
												$srange=$erange+1;
												$erange=$srange+$bundle_qty-1;
												$tot_bundle_qty+=$bundle_qty;

												$barcode_suffix_no=$barcode_suffix_no+1;
												$barcode_no=$year_id."509".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
												//$bl_size_qty-=$bundle_qty;
												$size_country_array[$size_id][$order_id][$country_id]-=$bundle_qty;
												$plies-=$bundle_qty;

												$country_type=$country_type_array[$order_id][$country_id];

												if($data_array_bundle!="") $data_array_bundle.= ",";
												$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$year_id."','".$barcode_suffix_no."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."','".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
												$bundle_id=$bundle_id+1;
												$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty;

												//tmp solution
												//do not delete
												/*
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['bundle_id'] = $bundle_id;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['mst_id'] = $mst_id;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['dtls_id'] = $dtls_id;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['size_id'] = $size_id;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['year_id'] = $year_id;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['country_type'] = $country_type;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['country_id'] = $country_id;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['rollId'] = $rollId;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['rollNo'] = $rollNo;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['pattern_no'] = $pattern_no;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['order_id'] = $order_id;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['color_type_id'] = $color_type_id;

												$dataArrayBundle2[$size_id]['bundle_qty'] += $bundle_qty;
												$dataArrayBundle2[$size_id]['bundle_per_pcs'] = $bundle_per_pcs;
												*/
											}
										}
									}
									else
									{
										if($bl_roll_plies>$plies)
										{
											$bundle_qty2=$plies;
											$bl_roll_plies=$bl_roll_plies-$plies;
										}
										else
										{
											$bundle_qty2=$bl_roll_plies;
											$bl_roll_plies=0;
										}

										if($bundle_qty2>0)
										{
											if($bundle_qty2>$bl_size_qty)
											{
												$tmp_bl_arr[$size_id][$rollId][1]=$bundle_qty2-$bl_size_qty;//echo $bundle_qty2."==".$bl_size_qty."==".$country_id;
												$tmp_bl_arr[$size_id][$rollId][2]=$bl_size_qty;
												$bundle_qty2=$bl_size_qty;
											}
											else { $tmp_bl_arr[$size_id][$rollId][1]=0; $tmp_bl_arr[$size_id][$rollId][2]=0; }
											//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
											$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
											$bundle_prif_no=$bundle_prif_no+1;
											$bundle_no=$bundle_prif."-".$bundle_prif_no;
											$srange=$erange+1;
											$erange=$srange+$bundle_qty2-1;
											$tot_bundle_qty+=$bundle_qty2;

											$barcode_suffix_no=$barcode_suffix_no+1;
											$barcode_no=$year_id."509".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
											//$bl_size_qty-=$bundle_qty2;
											$size_country_array[$size_id][$order_id][$country_id]-=$bundle_qty2;
											$plies-=$bundle_qty2;

											$country_type=$country_type_array[$order_id][$country_id];

											if($data_array_bundle!="") $data_array_bundle.= ",";
											$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$year_id."','".$barcode_suffix_no."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."','".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
											$bundle_id=$bundle_id+1;
											$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty2;
											//echo $rollNo.",".$srange.",".$erange;

											//tmp solution
											//do not delete
											/*
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['bundle_id'] = $bundle_id;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['mst_id'] = $mst_id;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['dtls_id'] = $dtls_id;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['size_id'] = $size_id;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['year_id'] = $year_id;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['bundle_qty'] = $bundle_qty2;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['country_type'] = $country_type;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['country_id'] = $country_id;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['rollId'] = $rollId;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['rollNo'] = $rollNo;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['pattern_no'] = $pattern_no;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['order_id'] = $order_id;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['color_type_id'] = $color_type_id;

											$dataArrayBundle2[$size_id]['bundle_qty'] += $bundle_qty2;
											$dataArrayBundle2[$size_id]['bundle_per_pcs'] = $bundle_per_pcs;
											*/
										}
									}
								}
							}
						}
					}
				}
				$pattern_no++;
			}
		}

		//tmp solution
		//do not delete
		/*
		$bundleId=return_next_id("id", "ppl_cut_lay_bundle",1);
		$data_array_bundle = '';

		foreach($dataArrayBundle as $sizeId=>$sizeArr)
		{
			$toNo = 0;
			foreach($sizeArr as $rlId => $rlArr)
			{
				$usedSizeQty = 0;
				foreach($rlArr as $bndlId => $row)
				{
					//$sizeQty = $dataArrayBundle2[$sizeId]['bundle_qty'];
					$sizeQty = $rollsizeBl[$sizeId][$rlId];
					$perBundle = $dataArrayBundle2[$sizeId]['bundle_per_pcs'];
					$restSizeQty = $sizeQty-$usedSizeQty;

					if($restSizeQty > 0)
					{
						if($restSizeQty >= $perBundle)
						{
							$bundleQty = $perBundle;
						}
						else
						{
							$fQty = floor($sizeQty/$perBundle);
							$rQty = $fQty*$perBundle;
							$bundleQty = $sizeQty-$rQty;
						}

						$usedSizeQty = $usedSizeQty + $bundleQty;
						$row['bundle_prif'] = $company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
						$bundlePrifNo=$bundlePrifNo+1;
						$row['bundle_prif_no'] = $bundlePrifNo;
						$row['bundle_no'] = $row['bundle_prif']."-".$row['bundle_prif_no'];
						$barcodeSuffixNo = $barcodeSuffixNo+1;
						$row['barcode_suffix_no'] = $barcodeSuffixNo;
						$row['barcode_no'] = $year_id."99".str_pad($barcodeSuffixNo,10,"0",STR_PAD_LEFT);
						$fromNo = $toNo+1;
						$toNo = $fromNo+$bundleQty-1;

						if($data_array_bundle != '')
							$data_array_bundle.= ',';
						$data_array_bundle.="(".$bundleId.",".$row['mst_id'].",".$row['dtls_id'].",".$row['size_id'].",'".$row['bundle_prif']."','".$row['bundle_prif_no']."','".$row['bundle_no']."','".$row['year_id']."','".$row['barcode_suffix_no']."','".$row['barcode_no']."',".$fromNo.",".$toNo.",".$bundleQty.",'".$row['country_type']."',".$row['country_id'].",'".$row['rollId']."',".$row['rollNo'].",'0','".$row['order_id']."','0',".$row['color_type_id'].",".$user_id.",'".$pc_date_time."')";
						$bundleId=$bundleId+1;
					}
				}
			}
		}
		*/

		//echo "10**insert into ppl_cut_lay_bundle($field_array_bundle) values".$data_array_bundle; die;
		//echo "10**insert into ppl_cut_lay_roll_dtls($field_array_roll_dtls) values".$data_array_roll_dtls; die;
		//echo "10**insert into ppl_cut_lay_size_dtls($field_array_size)values".$data_array_size; die;
		//echo "10**insert into ppl_cut_lay_size($field_array)values".$data_array;die;
		//echo "10**insert into ppl_cut_lay_bundle($field_array_bundle)values".$data_array_bundle; die;
		$rID=sql_insert("ppl_cut_lay_size",$field_array,$data_array,0);
		$rID_size=sql_insert("ppl_cut_lay_size_dtls",$field_array_size,$data_array_size,0);
		$rID2=sql_insert("ppl_cut_lay_bundle",$field_array_bundle,$data_array_bundle,0);
		$rID3=sql_insert("ppl_cut_lay_roll_dtls",$field_array_roll_dtls,$data_array_roll_dtls,0);
		$field_array_up="marker_qty*pcs_per_bundle*updated_by*update_date";
		$data_array_up=$to_marker_qty."*'".$txt_bundle_pcs."'*'".$user_id."'*'".$pc_date_time."'";
		$rID4=sql_update("ppl_cut_lay_dtls",$field_array_up,$data_array_up,"id",$dtls_id,0);
		//echo "10**".$rID.'='.$rID_size.'='.$rID2.'='.$rID3.'='.$rID4; die;

		$total_marker_qty=$total_marker_qty_prev+$tot_marker_qnty_curr;
		$lay_balance=$plan_qty-$total_marker_qty;
		//echo "10**".$seqDatas;die;
		if($db_type==0)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4)
			{
				mysql_query("COMMIT");
				echo "0**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4)
			{
				oci_commit($con);
				echo "0**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
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
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$cutting_qc_no= return_field_value("cutting_qc_no","pro_gmts_cutting_qc_mst","status_active=1 and is_deleted=0 and cutting_no='".$cut_no."'");
		$cutData=sql_select("select cut_num_prefix_no, job_no from ppl_cut_lay_mst where cutting_no = '".$cut_no."'");
		//$cut_on_prifix = return_field_value("cut_num_prefix_no","ppl_cut_lay_mst","cutting_no = '".$cut_no."'");
		$cut_on_prifix=$cutData[0][csf('cut_num_prefix_no')];
		$job_no=$cutData[0][csf('job_no')];
		//echo $cutting_qc_no."jkjkj";die;
		if($cutting_qc_no!="")
		{
			echo "200**".$mst_id."**".$dtls_id."**".$cutting_qc_no; disconnect($con); die;
		}


		$previous_barcode_data=sql_select("select bundle_no,barcode_no,barcode_year,barcode_prifix from ppl_cut_lay_bundle where mst_id=".$mst_id."  and  dtls_id=".$dtls_id." and status_active=1 and is_deleted=0 ");
		foreach($previous_barcode_data as $b_val)
		{
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['year']=$b_val[csf("barcode_year")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['prifix']=$b_val[csf("barcode_prifix")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['barcode']=$b_val[csf("barcode_no")];
		}
		//print_r($previous_barcode_arr);die;


		$plan_qty= return_field_value("sum(CAST(plan_cut_qnty as INT)) as plan_qty","wo_po_color_size_breakdown","po_break_down_id in(".$order_id.") and item_number_id=".$gmt_id." and color_number_id=".$color_id." and status_active=1","plan_qty");

		$total_marker_qty_prev= return_field_value("sum(b.marker_qty) as mark_qty","ppl_cut_lay_dtls a, ppl_cut_lay_size b","a.id=b.dtls_id and a.id!=$dtls_id and  b.order_id in(".$order_id.") and a.gmt_item_id=".$gmt_id." and a.color_id=".$color_id." and a.status_active=1 and b.status_active=1 and b.is_deleted=0","mark_qty");

		$sizeRatioArr=array(); $sizeQtyArr=array(); $sizeQtyArrForC=array(); $sizeIdAgainstSeq=array();
		$id_size=return_next_id("id", "ppl_cut_lay_size_dtls", 1);
		$field_array_size="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,bundle_sequence,inserted_by,insert_date";
		for($i=1; $i<=$size_row_num; $i++)
		{
			$hidden_sizef_id="hidden_sizef_id_".$i;
			$txt_layf_balance="txt_layf_balance_".$i;
			$txt_sizef_ratio="txt_sizef_ratio_".$i;
			$txt_sizef_qty="txt_sizef_qty_".$i;
			$bundle_sequence="txt_bundle_".$i;

			if(str_replace("'",'',$$txt_sizef_qty)>0)
			{
				if(str_replace("'",'',$$bundle_sequence)>0)
				{
					$seq=str_replace("'",'',$$bundle_sequence);
				}
				else
				{
					$max_seq++;
					$seq=$max_seq;
				}

				$dataSize=$mst_id.",".$dtls_id.",".$color_id.",".$$hidden_sizef_id.",".$$txt_sizef_ratio.",".$$txt_sizef_qty.",".$seq;
				$data_array_up[$seq]=$dataSize;
				$sizeIdAgainstSeq[$seq]=str_replace("'",'',$$hidden_sizef_id);

				$sizeRatioArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_ratio);
				$sizeQtyArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
				$sizeQtyArrForC[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
				$sizeSeqArr[str_replace("'",'',$$hidden_sizef_id)]=$seq;
			}
		}

		$seqDatas='';
		ksort($data_array_up);
		foreach($data_array_up as $sequence=>$data)
		{
			if($data_array_size!="") $data_array_size.= ",";
			$data_array_size.="(".$id_size.",".$data.",".$user_id.",'".$pc_date_time."')";
			$seqDatas.=$id_size."__".$sizeIdAgainstSeq[$sequence]."__".$sequence.",";
			$id_size++;
		}

		$roll_size_arr=array(); $roll_no_arr=array(); $rollsizeBl=array(); $rollPliesArr=array(); $sizeRatioBlArr=array();
		$rollDatas=explode("|",$roll_data);
		$rollDtls_id=return_next_id("id", "ppl_cut_lay_roll_dtls",1);
		$field_array_roll_dtls="id,mst_id,dtls_id,roll_id,roll_no,roll_wgt,plies,size_id,size_qty,inserted_by,insert_date";
		foreach($rollDatas as $data)
		{
			$datas=explode("=",$data);
			$roll_no=$datas[1];
			$roll_id=$datas[2];
			$roll_wgt=$datas[3];
			$plies=$datas[4];

			if($roll_id=="" || $roll_id==0) $roll_id=$rollDtls_id;


			foreach($sizeRatioArr as $size_id=>$size_ratio)
			{
				$roll_no_arr[$size_id][$roll_id]=$roll_no;
				$size_qty=$size_ratio*$plies;
				if($data_array_roll_dtls!="") $data_array_roll_dtls.= ",";
				$data_array_roll_dtls.="(".$rollDtls_id.",".$mst_id.",".$dtls_id.",'".$roll_id."','".$roll_no."','".$roll_wgt."','".$plies."','".$size_id."',".$size_qty.",".$user_id.",'".$pc_date_time."')";

				$rollsizeBl[$size_id][$roll_id]=$size_qty;
				$rollPliesArr[$size_id][$roll_id]=$plies;

				$rollDtls_id++;
			}
		}
		//echo "10**<pre>";
		//print_r($rollsizeBl); die;

		$bundle_no_array=array(); $last_rmg_no='';
		$bundle_id=return_next_id("id", "ppl_cut_lay_bundle",1);
		$field_array_bundle="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,barcode_year,barcode_prifix,barcode_no,number_start,number_end,size_qty,country_type,country_id,roll_id,roll_no,pattern_no,order_id,is_excess,color_type_id,inserted_by,insert_date";

		//$last_rmg_no=return_field_value("max(a.number_end) as last_rmg", "ppl_cut_lay_bundle a, ppl_cut_lay_mst b","a.mst_id=b.id and a.mst_id='".$mst_id."' and a.dtls_id!=$dtls_id","last_rmg");
		//$last_bundle_no=return_field_value("max(bundle_num_prefix_no) as last_prefix", "ppl_cut_lay_bundle","mst_id=$mst_id and dtls_id!=$dtls_id","last_prefix");

		$bundleData=sql_select("select max(number_end) as last_rmg, max(bundle_num_prefix_no) as last_prefix from ppl_cut_lay_bundle where mst_id=$mst_id and dtls_id!=$dtls_id");
		$last_rmg_no=$bundleData[0][csf('last_rmg')];
		$last_bundle_no=$bundleData[0][csf('last_prefix')];

		$tot_marker_qnty_curr=0; $bundle_prif_no=$last_bundle_no; $size_country_array=array(); $country_type_array=array();
		$bundlePrifNo = $last_bundle_no;

		//echo "10**"."SELECT size_id, max(number_end) as last_rmg FROM ppl_cut_lay_bundle WHERE mst_id='".$mst_id."' and dtls_id!='".$dtls_id."' group by size_id";die;
		$id=return_next_id("id", "ppl_cut_lay_size", 1);
		$field_array="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,country_type,country_id,excess_perc,order_id,size_wise_repeat,inserted_by,insert_date";
		for($i=1; $i<=$row_num; $i++)
		{
			$txt_size_id="hidden_size_id_".$i;
			$txt_lay_balance="txt_lay_balance_".$i;
			$cboCountryType="cboCountryType_".$i;
			$cboCountry="cboCountry_".$i;
			$excess_perc="txt_excess_".$i;
			$po_id="poId_".$i;
			$txt_size_qty="txt_size_qty_".$i;

			$marker_qty=0;
			$order_id=str_replace("'",'',$$po_id);
			$size_id=str_replace("'",'',$$txt_size_id);
			$lay_balance=str_replace("'",'',$$txt_lay_balance);
			//$size_qty=$sizeQtyArrForC[$size_id];
			$marker_qty=str_replace("'",'',$$txt_size_qty);
			$country_type_array[$order_id][str_replace("'",'',$$cboCountry)]=str_replace("'",'',$$cboCountryType);

			if($marker_qty>0)
			{
				$size_country_array[$size_id][$order_id][str_replace("'",'',$$cboCountry)]+=$marker_qty;
				$tot_marker_qnty_curr+=$marker_qty;

				if($data_array!="") $data_array.= ",";
				$data_array.="(".$id.",".$mst_id.",".$dtls_id.",".$color_id.",".$$txt_size_id.",0,".$marker_qty.",".$$cboCountryType.",".$$cboCountry.",'".$$excess_perc."',".$$po_id.",0,".$user_id.",'".$pc_date_time."')";
				$id=$id+1;
			}
		}
		//echo "10**<pre>";
		//print_r($size_country_array); die;

		$company_sort_name=explode("-",$cut_no); $bundle_per_pcs=str_replace("'","",$bundle_per_pcs);
		asort($sizeSeqArr);
		//foreach($sizeQtyArr as $size_id=>$size_qty)
		foreach($sizeSeqArr as $size_id=>$size_seq)
		{
			$size_ratio=$sizeRatioArr[$size_id]; $size_qty=$sizeQtyArr[$size_id]; $pattern_no="A";
			for($k=1; $k<=$size_ratio; $k++)
			{
				foreach($roll_no_arr[$size_id] as $rollId=>$rollNo)
				{
					$sizeRatioBlArr[$size_id][$pattern_no][$rollId]=$rollPliesArr[$size_id][$rollId];
				}
				$pattern_no++;
			}
		}
		//echo "10**";
		//print_r($sizeRatioBlArr); die;
		//foreach($sizeQtyArr as $size_id=>$size_qty)
		//$year_id=date('Y',time());
		//if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$cut_year_ex=explode("-", $cut_no);
		$year_id=$cut_year_ex[1];
 		$barcode_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle","barcode_year=$year_id","suffix_no");
		$barcodeSuffixNo=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle","barcode_year=$year_id","suffix_no");

		// ========================== rmg no creation as per variable setting ==============================
		$rmg_no_creation=return_field_value("smv_source","variable_settings_production","company_name=$cbo_company_id and variable_list=39 and is_deleted=0 and status_active=1");
		if($rmg_no_creation=="") $rmg_no_creation=2; else $rmg_no_creation=$rmg_no_creation;
		// echo "10**$rmg_no_creation";die();
		$erange=0;
		if($rmg_no_creation==2) // cutting wise
		{
			$erange=return_field_value("max(number_end) as last_rmg","ppl_cut_lay_bundle","mst_id=$mst_id and dtls_id!=$dtls_id and status_active=1 and is_deleted=0","last_rmg");
		}
		else if($rmg_no_creation==3) // job wise
		{
			$erange=return_field_value("max(a.number_end) as last_rmg","ppl_cut_lay_bundle a, ppl_cut_lay_mst b","a.mst_id=b.id and b.entry_form=509 and b.job_no='$job_id' and a.status_active=1 and a.is_deleted=0","last_rmg");
		}
		else if($rmg_no_creation==4) // order wise
		{
			// $erange=return_field_value("max(a.number_end) as last_rmg","ppl_cut_lay_bundle a, ppl_cut_lay_dtls b","a.dtls_id=b.id and a.order_id in($orderIds) and a.status_active=1 and a.is_deleted=0","last_rmg");
			$sql = "SELECT a.order_id,max(a.number_end) as LAST_RMG from ppl_cut_lay_bundle a, ppl_cut_lay_dtls b where a.dtls_id=b.id and a.order_id in($orderIds) and a.status_active=1 and a.is_deleted=0 group by a.order_id";
			$res = sql_select($sql);
			$order_wise_rmg_no_array = array();
			foreach ($res as $val)
			{
				$order_wise_rmg_no_array[$val['order_id']] = $val['LAST_RMG'];
			}
		}

		$dataArrayBundle = array();
		$dataArrayBundle2 = array();

		foreach($sizeSeqArr as $size_id=>$size_seq)
		{
			// $erange=0;
			if($rmg_no_creation==1 || $erange=="") { $erange=0; }
			$size_ratio=$sizeRatioArr[$size_id]; $size_qty=$sizeQtyArr[$size_id]; $pattern_no="A";
			for($k=1; $k<=$size_ratio; $k++)
			{
				if($rmg_no_creation==5) { $erange=0; }
				$plies=$txt_plies; $tmp_bl_arr=array();
				foreach($roll_no_arr[$size_id] as $rollId=>$rollNo)
				{
					foreach($size_country_array[$size_id] as $order_id=>$order_data)
					{
						if($rmg_no_creation==4)
						{
							$erange = $order_wise_rmg_no_array[$order_id];
						}
						//print_r($order_data);die;
						foreach($order_data as $country_id=>$size_country_qty)
						{
							if($sizeRatioBlArr[$size_id][$pattern_no][$rollId]>0)
							{
								$temp_bal_flag=1;
								$bl_size_qty=$size_country_array[$size_id][$order_id][$country_id];
								//echo $bundle_no."=".$bundle_qty2."=".$bl_size_qty."=".$bl_roll_plies.'<br>';
								if($plies>0 && $tmp_bl_arr[$size_id][$rollId][1]>0 && $bl_size_qty>0)
								{
									$temp_bal_flag=0;
									//$bundle_qty2=$tmp_bl_arr[$size_id][$rollId][1];
									if($tmp_bl_arr[$size_id][$rollId][1]>$bl_size_qty)
									{
										$bundle_qty2=$bl_size_qty;
										$tmp_bl_arr[$size_id][$rollId][1]-=$bundle_qty2;//echo $bundle_qty2."==".$bl_size_qty."==".$country_id;
										$tmp_bl_arr[$size_id][$rollId][2]-=$bundle_qty2;
										$bl_roll_plies=$tmp_bl_arr[$size_id][$rollId][1];
									}
									else
									{
										$bundle_qty2=$tmp_bl_arr[$size_id][$rollId][1];
										$tmp_bl_arr[$size_id][$rollId][1]=0;
										$tmp_bl_arr[$size_id][$rollId][2]=0;
										$bl_roll_plies=0;
									}
									//$bundle_qty2=$bl_size_qty;
									//$tmp_bl_arr[$size_id][$rollId][1]=0;
									//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
									$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
									$bundle_prif_no=$bundle_prif_no+1;
									$bundle_no=$bundle_prif."-".$bundle_prif_no;
									$srange=$erange+1;
									$erange=$srange+$bundle_qty2-1;
									$tot_bundle_qty+=$bundle_qty2;

									if(empty($previous_barcode_arr[$bundle_no]))
									{
										$barcode_suffix_no=$barcode_suffix_no+1;
										$up_barcode_suffix=$barcode_suffix_no;
										$up_barcode_year=$year_id;
										$barcode_no=$year_id."509".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
									}
									else
									{
										$up_barcode_suffix=$previous_barcode_arr[$bundle_no]['prifix'];
										$up_barcode_year=$previous_barcode_arr[$bundle_no]['year'];
										$barcode_no=$previous_barcode_arr[$bundle_no]['barcode'];
									}
									//$bl_size_qty-=$bundle_qty2;
									$size_country_array[$size_id][$order_id][$country_id]-=$bundle_qty2;
									$plies-=$bundle_qty2;
									//echo $bundle_no."--".$bundle_qty2.'<br>';
									$country_type=$country_type_array[$order_id][$country_id];

									if($data_array_bundle!="") $data_array_bundle.= ",";
									$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."','".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
									$bundle_id=$bundle_id+1;
									//$bl_roll_plies=$rollPliesArr[$size_id][$rollId]-($bundle_qty2+$tmp_bl_arr[$size_id][$rollId][2]);
									//$tmp_bl_arr[$size_id][$rollId][2]=0;
									$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty2;

									//tmp solution
									//do not delete
									/*
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['bundle_id'] = $bundle_id;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['mst_id'] = $mst_id;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['dtls_id'] = $dtls_id;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['size_id'] = $size_id;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['year_id'] = $year_id;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['country_type'] = $country_type;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['country_id'] = $country_id;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['rollId'] = $rollId;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['rollNo'] = $rollNo;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['pattern_no'] = $pattern_no;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['order_id'] = $order_id;
									$dataArrayBundle[$size_id][$rollId][$bundle_id]['color_type_id'] = $color_type_id;
									//$dataArrayBundle[$size_id][$rollId][$bundle_id]['bundle_qty'] = $bundle_qty2;

									$dataArrayBundle2[$size_id]['bundle_qty'] += $bundle_qty2;
									$dataArrayBundle2[$size_id]['bundle_per_pcs'] = $bundle_per_pcs;
									*/
								}
								else
								{
									$bl_roll_plies=$rollPliesArr[$size_id][$rollId];
								}

								//$bl_size_qty=$size_country_array[$size_id][$country_id];
								//echo $plies.'='.$bl_roll_plies.'='.$bl_size_qty.'='.$temp_bal_flag.'<br>';
								if($plies>0 && $bl_roll_plies>0 && $bl_size_qty>0 && $temp_bal_flag==1)
								{
									if($bl_roll_plies>=$bundle_per_pcs)
									{
										$bundle_per_size=ceil($bl_roll_plies/$bundle_per_pcs);
										for($z=1; $z<=$bundle_per_size; $z++)
										{
											$bl_size_qty=$size_country_array[$size_id][$order_id][$country_id];
											if($bl_size_qty>0 && $sizeRatioBlArr[$size_id][$pattern_no][$rollId]>0)
											{
												if($bl_roll_plies>$bundle_per_pcs)
												{
													$bundle_qty=$bundle_per_pcs;
												}
												else
												{
													$bundle_qty=$bl_roll_plies;
												}

												if($bundle_qty>$bl_size_qty)
												{
													$bundle_qty=$bl_size_qty;
												}

												if($bundle_qty>$plies)
												{
													$bundle_qty=$plies;
												}

												if($bundle_qty>$sizeRatioBlArr[$size_id][$pattern_no][$rollId])
												{
													$bundle_qty=$sizeRatioBlArr[$size_id][$pattern_no][$rollId];
												}

												$bl_roll_plies-=$bundle_qty;

												//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
												$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
												$bundle_prif_no=$bundle_prif_no+1;
												$bundle_no=$bundle_prif."-".$bundle_prif_no;
												$srange=$erange+1;
												$erange=$srange+$bundle_qty-1;
												$tot_bundle_qty+=$bundle_qty;

												if(empty($previous_barcode_arr[$bundle_no]))
												{
													$barcode_suffix_no=$barcode_suffix_no+1;
													$up_barcode_suffix=$barcode_suffix_no;
													$up_barcode_year=$year_id;
													$barcode_no=$year_id."509".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
												}
												else
												{
													$up_barcode_suffix=$previous_barcode_arr[$bundle_no]['prifix'];
													$up_barcode_year=$previous_barcode_arr[$bundle_no]['year'];
													$barcode_no=$previous_barcode_arr[$bundle_no]['barcode'];
												}

												//$bl_size_qty-=$bundle_qty;
												$size_country_array[$size_id][$order_id][$country_id]-=$bundle_qty;
												$plies-=$bundle_qty;

												$country_type=$country_type_array[$order_id][$country_id];

												if($data_array_bundle!="") $data_array_bundle.= ",";
												$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."','".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
												$bundle_id=$bundle_id+1;
												$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty;

												//tmp solution
												//do not delete
												/*
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['bundle_id'] = $bundle_id;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['mst_id'] = $mst_id;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['dtls_id'] = $dtls_id;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['size_id'] = $size_id;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['year_id'] = $year_id;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['country_type'] = $country_type;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['country_id'] = $country_id;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['rollId'] = $rollId;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['rollNo'] = $rollNo;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['pattern_no'] = $pattern_no;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['order_id'] = $order_id;
												$dataArrayBundle[$size_id][$rollId][$bundle_id]['color_type_id'] = $color_type_id;
												//$dataArrayBundle[$size_id][$rollId][$bundle_id]['bundle_qty'] = $bundle_qty2;

												$dataArrayBundle2[$size_id]['bundle_qty'] += $bundle_qty;
												$dataArrayBundle2[$size_id]['bundle_per_pcs'] = $bundle_per_pcs;
												*/
											}
										}
									}
									else
									{
										if($bl_roll_plies>$plies)
										{
											$bundle_qty2=$plies;
											$bl_roll_plies=$bl_roll_plies-$plies;
											//echo $bundle_no."=".$bundle_qty2."=".$bl_size_qty."=".$bl_roll_plies.'<br>';
										}
										else
										{
											$bundle_qty2=$bl_roll_plies;
											$bl_roll_plies=0;
										}

										if($bundle_qty2>0)
										{
											if($bundle_qty2>$bl_size_qty)
											{
												$tmp_bl_arr[$size_id][$rollId][1]=$bundle_qty2-$bl_size_qty;//echo $bundle_qty2."==".$bl_size_qty."==".$country_id;
												$tmp_bl_arr[$size_id][$rollId][2]=$bl_size_qty;
												$bundle_qty2=$bl_size_qty;
											}
											else { $tmp_bl_arr[$size_id][$rollId][1]=0; $tmp_bl_arr[$size_id][$rollId][2]=0; }
											//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
											//echo $bundle_no."=".$bundle_qty2."=".$bl_size_qty."=".$bl_roll_plies.'--1<br>';

											$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
											$bundle_prif_no=$bundle_prif_no+1;
											$bundle_no=$bundle_prif."-".$bundle_prif_no;
											$srange=$erange+1;
											$erange=$srange+$bundle_qty2-1;
											$tot_bundle_qty+=$bundle_qty2;

											//echo $bundle_no."=".$bundle_qty2."=".$bl_size_qty."=".$bl_roll_plies.'--2<br>';
											if(empty($previous_barcode_arr[$bundle_no]))
											{
												$barcode_suffix_no=$barcode_suffix_no+1;
												$up_barcode_suffix=$barcode_suffix_no;
												$up_barcode_year=$year_id;
												$barcode_no=$year_id."509".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
											}
											else
											{
												$up_barcode_suffix=$previous_barcode_arr[$bundle_no]['prifix'];
												$up_barcode_year=$previous_barcode_arr[$bundle_no]['year'];
												$barcode_no=$previous_barcode_arr[$bundle_no]['barcode'];
											}

											//$bl_size_qty-=$bundle_qty2;
											$size_country_array[$size_id][$order_id][$country_id]-=$bundle_qty2;
											$plies-=$bundle_qty2;

											$country_type=$country_type_array[$order_id][$country_id];

											if($data_array_bundle!="") $data_array_bundle.= ",";
											$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."','".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
											$bundle_id=$bundle_id+1;
											$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty2;
											//echo $rollNo.",".$srange.",".$erange;

											//tmp solution
											//do not delete
											/*
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['bundle_id'] = $bundle_id;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['mst_id'] = $mst_id;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['dtls_id'] = $dtls_id;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['size_id'] = $size_id;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['year_id'] = $year_id;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['bundle_qty'] = $bundle_qty2;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['country_type'] = $country_type;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['country_id'] = $country_id;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['rollId'] = $rollId;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['rollNo'] = $rollNo;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['pattern_no'] = $pattern_no;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['order_id'] = $order_id;
											$dataArrayBundle[$size_id][$rollId][$bundle_id]['color_type_id'] = $color_type_id;
											//$dataArrayBundle[$size_id][$rollId][$bundle_id]['bundle_qty'] = $bundle_qty2;

											$dataArrayBundle2[$size_id]['bundle_qty'] += $bundle_qty2;
											$dataArrayBundle2[$size_id]['bundle_per_pcs'] = $bundle_per_pcs;
											*/
										}
									}
								}
							}
						}
					}
				}
				$pattern_no++;
			}
		}

		//tmp solution
		//do not delete
		/*
		$bundleId=return_next_id("id", "ppl_cut_lay_bundle",1);
		$data_array_bundle = '';

		foreach($dataArrayBundle as $sizeId=>$sizeArr)
		{
			$toNo = 0;
			foreach($sizeArr as $rlId => $rlArr)
			{
				$usedSizeQty = 0;
				foreach($rlArr as $bndlId => $row)
				{
					//$sizeQty = $dataArrayBundle2[$sizeId]['bundle_qty'];
					$sizeQty = $rollsizeBl[$sizeId][$rlId];
					$perBundle = $dataArrayBundle2[$sizeId]['bundle_per_pcs'];
					$restSizeQty = $sizeQty-$usedSizeQty;

					if($restSizeQty > 0)
					{
						if($restSizeQty >= $perBundle)
						{
							$bundleQty = $perBundle;
						}
						else
						{
							$fQty = floor($sizeQty/$perBundle);
							$rQty = $fQty*$perBundle;
							$bundleQty = $sizeQty-$rQty;
						}

						$usedSizeQty = $usedSizeQty + $bundleQty;
						$row['bundle_prif'] = $company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
						$bundlePrifNo=$bundlePrifNo+1;
						$row['bundle_prif_no'] = $bundlePrifNo;
						$row['bundle_no'] = $row['bundle_prif']."-".$row['bundle_prif_no'];
						$barcodeSuffixNo = $barcodeSuffixNo+1;
						$row['barcode_suffix_no'] = $barcodeSuffixNo;
						$row['barcode_no'] = $year_id."99".str_pad($barcodeSuffixNo,10,"0",STR_PAD_LEFT);
						$fromNo = $toNo+1;
						$toNo = $fromNo+$bundleQty-1;

						if($data_array_bundle != '')
							$data_array_bundle.= ',';
						$data_array_bundle.="(".$bundleId.",".$row['mst_id'].",".$row['dtls_id'].",".$row['size_id'].",'".$row['bundle_prif']."','".$row['bundle_prif_no']."','".$row['bundle_no']."','".$row['year_id']."','".$row['barcode_suffix_no']."','".$row['barcode_no']."',".$fromNo.",".$toNo.",".$bundleQty.",'".$row['country_type']."',".$row['country_id'].",'".$row['rollId']."',".$row['rollNo'].",'0','".$row['order_id']."','0',".$row['color_type_id'].",".$user_id.",'".$pc_date_time."')";
						$bundleId=$bundleId+1;
					}
				}
			}
		}
		*/
		//echo "10**".$data_array_bundle; die;

		//die;
		//echo "10**";print_r($sizeRatioBlArr);die;
	    //echo "10**insert into ppl_cut_lay_bundle($field_array_bundle)values".$data_array_bundle;die;
		//echo "10**insert into ppl_cut_lay_size($field_array)values".$data_array;die;

		$delete=execute_query("delete from ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		$delete_size=execute_query("delete from ppl_cut_lay_size_dtls where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		$delete_bundle=execute_query("delete from ppl_cut_lay_bundle where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		$delete_roll=execute_query("delete from ppl_cut_lay_roll_dtls where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);

		$rID=sql_insert("ppl_cut_lay_size",$field_array,$data_array,0);
		$rID_size=sql_insert("ppl_cut_lay_size_dtls",$field_array_size,$data_array_size,0);
		$rID2=sql_insert("ppl_cut_lay_bundle",$field_array_bundle,$data_array_bundle,0);
		//echo "10**insert into ppl_cut_lay_bundle($field_array_bundle) values".$data_array_bundle."**".$rID2;die;
		$rID3=sql_insert("ppl_cut_lay_roll_dtls",$field_array_roll_dtls,$data_array_roll_dtls,0);
		$field_array_up="marker_qty*pcs_per_bundle*updated_by*update_date";
		$data_array_up="".$to_marker_qty."*'".$txt_bundle_pcs."'*'".$user_id."'*'".$pc_date_time."'";
		$rID4=sql_update("ppl_cut_lay_dtls",$field_array_up,$data_array_up,"id",$dtls_id,0);

		//echo "10**".$rID ."**". $rID_size ."**". $rID2 ."**". $rID3 ."**". $rID4 ."**". $delete ."**". $delete_size."**".$delete_bundle."**".$delete_roll;die;

		$total_marker_qty=$total_marker_qty_prev+$tot_marker_qnty_curr;
		$lay_balance=$plan_qty-$total_marker_qty;
		//echo "10**".$lay_balance."**".$total_marker_qty."**".$total_marker_qty_prev."**".$tot_marker_qnty_curr;die;

		if($db_type==0)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4 && $delete && $delete_size && $delete_bundle && $delete_roll)
			{
				mysql_query("COMMIT");
				echo "1**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4 && $delete && $delete_size && $delete_bundle && $delete_roll)
			{
				oci_commit($con);
				echo "1**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
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
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		exit();
	}
}

if($action=="save_update_delete_size_28072020")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo "10**"; die;
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
    	$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$cutting_qc_no= return_field_value("cutting_qc_no","pro_gmts_cutting_qc_mst","status_active=1 and is_deleted=0 and cutting_no='".$cut_no."'");
		if($cutting_qc_no!="") { echo "201**".$mst_id."**".$dtls_id."**".$cutting_qc_no; disconnect($con); die;}

		$cutData=sql_select("select cut_num_prefix_no, job_no from ppl_cut_lay_mst where cutting_no = '".$cut_no."'");
		$cut_on_prifix=$cutData[0][csf('cut_num_prefix_no')];
		$job_no=$cutData[0][csf('job_no')];

		$plan_qty= return_field_value("sum(CAST(plan_cut_qnty as INT)) as plan_qty","wo_po_color_size_breakdown","po_break_down_id in(".$order_id.") and item_number_id=".$gmt_id." and color_number_id=".$color_id." and status_active=1","plan_qty");

		$total_marker_qty_prev= return_field_value("sum(b.marker_qty) as mark_qty","ppl_cut_lay_dtls a, ppl_cut_lay_size b","a.id=b.dtls_id and b.order_id in(".$order_id.") and a.gmt_item_id=".$gmt_id." and a.color_id=".$color_id." and a.status_active=1 and b.status_active=1 and b.is_deleted=0","mark_qty");

		$sizeRatioArr=array(); $sizeQtyArr=array(); $sizeQtyArrForC=array(); $sizeIdAgainstSeq=array(); $seqDatas='';
		$id_size=return_next_id("id", "ppl_cut_lay_size_dtls", 1);
		$field_array_size="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,bundle_sequence,inserted_by,insert_date";
		for($i=1; $i<=$size_row_num; $i++)
		{
			$hidden_sizef_id="hidden_sizef_id_".$i;
			$txt_layf_balance="txt_layf_balance_".$i;
			$txt_sizef_ratio="txt_sizef_ratio_".$i;
			$txt_sizef_qty="txt_sizef_qty_".$i;
			$bundle_sequence="txt_bundle_".$i;

			if(str_replace("'",'',$$txt_sizef_qty)>0)
			{
				if(str_replace("'",'',$$bundle_sequence)>0)
				{
					$seq=str_replace("'",'',$$bundle_sequence);
				}
				else
				{
					$max_seq++;
					$seq=$max_seq;
				}

				$dataSize=$mst_id.",".$dtls_id.",".$color_id.",".$$hidden_sizef_id.",".$$txt_sizef_ratio.",".$$txt_sizef_qty.",".$seq;
				$data_array_up[$seq]=$dataSize;
				$sizeIdAgainstSeq[$seq]=str_replace("'",'',$$hidden_sizef_id);

				$sizeRatioArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_ratio);
				$sizeQtyArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
				$sizeQtyArrForC[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
				$sizeSeqArr[str_replace("'",'',$$hidden_sizef_id)]=$seq;
			}
		}

		ksort($data_array_up);
		foreach($data_array_up as $sequence=>$data)
		{
			if($data_array_size!="") $data_array_size.= ",";
			$data_array_size.="(".$id_size.",".$data.",".$user_id.",'".$pc_date_time."')";
			$seqDatas.=$id_size."__".$sizeIdAgainstSeq[$sequence]."__".$sequence.",";
			$id_size++;
		}

		$roll_size_arr=array(); $roll_no_arr=array(); $rollsizeBl=array();
		$rollDatas=explode("|",$roll_data);
		$rollDtls_id=return_next_id("id", "ppl_cut_lay_roll_dtls",1);
		$field_array_roll_dtls="id,mst_id,dtls_id,roll_id,roll_no,roll_wgt,plies,size_id,size_qty,inserted_by,insert_date";
		foreach($rollDatas as $data)
		{
			$datas=explode("=",$data);
			$roll_no=$datas[1];
			$roll_id=$datas[2];
			$roll_wgt=$datas[3];
			$plies=$datas[4];

			if($roll_id=="" || $roll_id==0) $roll_id=$rollDtls_id;

			foreach($sizeRatioArr as $size_id=>$size_ratio)
			{
				$roll_no_arr[$size_id][$roll_id]=$roll_no;
				$size_qty=$size_ratio*$plies;
				if($data_array_roll_dtls!="") $data_array_roll_dtls.= ",";
				$data_array_roll_dtls.="(".$rollDtls_id.",".$mst_id.",".$dtls_id.",'".$roll_id."','".$roll_no."','".$roll_wgt."','".$plies."','".$size_id."',".$size_qty.",".$user_id.",'".$pc_date_time."')";

				$rollsizeBl[$size_id][$roll_id]=$size_qty;
				$rollPliesArr[$size_id][$roll_id]=$plies;
				$rollDtls_id++;
			}
		}

		$bundle_no_array=array();
		$bundle_id=return_next_id("id", "ppl_cut_lay_bundle",1);

		$field_array_bundle="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,barcode_year,barcode_prifix,barcode_no,number_start,number_end,size_qty,country_type,country_id,roll_id,roll_no,pattern_no,order_id,is_excess,color_type_id,inserted_by,insert_date";

		$bundleData=sql_select("select max(number_end) as last_rmg, max(bundle_num_prefix_no) as last_prefix from ppl_cut_lay_bundle where mst_id=$mst_id");
		$last_rmg_no=$bundleData[0][csf('last_rmg')];
		$last_bundle_no=$bundleData[0][csf('last_prefix')];

		$update_id=''; $tot_marker_qnty_curr=0; $bundle_prif_no=$last_bundle_no; $size_country_array=array(); $country_type_array=array(); $sizeRatioBlArr=array();

		$id=return_next_id("id", "ppl_cut_lay_size", 1);
		$field_array="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,country_type,country_id,excess_perc,order_id,size_wise_repeat,inserted_by,insert_date";
		for($i=1; $i<=$row_num; $i++)
		{
			$txt_size_id="hidden_size_id_".$i;
			$txt_lay_balance="txt_lay_balance_".$i;
			$cboCountryType="cboCountryType_".$i;
			$cboCountry="cboCountry_".$i;
			$excess_perc="txt_excess_".$i;
			$po_id="poId_".$i;
			$txt_size_qty="txt_size_qty_".$i;

			$marker_qty=0;
			$order_id=str_replace("'",'',$$po_id);
			$size_id=str_replace("'",'',$$txt_size_id);
			$lay_balance=str_replace("'",'',$$txt_lay_balance);
			//$size_qty=$sizeQtyArrForC[$size_id];
			$marker_qty=str_replace("'",'',$$txt_size_qty);
			$country_type_array[$order_id][str_replace("'",'',$$cboCountry)]=str_replace("'",'',$$cboCountryType);

			if($marker_qty>0)
			{
				$size_country_array[$size_id][$order_id][str_replace("'",'',$$cboCountry)]+=$marker_qty;
				$tot_marker_qnty_curr+=$marker_qty;

				if($data_array!="") $data_array.= ",";
				$data_array.="(".$id.",".$mst_id.",".$dtls_id.",".$color_id.",".$$txt_size_id.",0,".$marker_qty.",".$$cboCountryType.",".$$cboCountry.",".$$excess_perc.",".$$po_id.",0,".$user_id.",'".$pc_date_time."')";
				$id=$id+1;
			}
		}
		//echo "10**";
		//print_r($size_country_array);die;
		//echo "10**";echo $txt_plies;die;
		$company_sort_name=explode("-",$cut_no); $bundle_per_pcs=str_replace("'","",$bundle_per_pcs);
		asort($sizeSeqArr);
		//foreach($sizeQtyArr as $size_id=>$size_qty)
		foreach($sizeSeqArr as $size_id=>$size_seq)
		{
			$size_ratio=$sizeRatioArr[$size_id]; $size_qty=$sizeQtyArr[$size_id]; $pattern_no="A";
			for($k=1; $k<=$size_ratio; $k++)
			{
				foreach($roll_no_arr[$size_id] as $rollId=>$rollNo)
				{
					$sizeRatioBlArr[$size_id][$pattern_no][$rollId]=$rollPliesArr[$size_id][$rollId];
				}
				$pattern_no++;
			}
		}

		//foreach($sizeQtyArr as $size_id=>$size_qty)
		$year_id=date('Y',time());
		if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$barcode_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle","barcode_year=$year_id","suffix_no");
		//echo $barcode_suffix_no."***";die;
		//echo "10**";
		foreach($sizeSeqArr as $size_id=>$size_seq)
		{
			$erange=0;
			$size_ratio=$sizeRatioArr[$size_id]; $size_qty=$sizeQtyArr[$size_id]; $pattern_no="A";
			for($k=1; $k<=$size_ratio; $k++)
			{
				$plies=$txt_plies;
				$tmp_bl_arr=array();
				foreach($roll_no_arr[$size_id] as $rollId=>$rollNo)
				{
					foreach($size_country_array[$size_id] as $order_id=>$order_data)
					{
						foreach($order_data as $country_id=>$size_country_qty)
						{
							if($sizeRatioBlArr[$size_id][$pattern_no][$rollId]>0)
							{
								$temp_bal_flag=1;
								$bl_size_qty=$size_country_array[$size_id][$order_id][$country_id];
								if($plies>0 && $tmp_bl_arr[$size_id][$rollId][1]>0 && $bl_size_qty>0)
								{
									//$bundle_qty2=$tmp_bl_arr[$size_id][$rollId][1]; $tmp_bl_arr[$size_id][$rollId][1]=0;
									$temp_bal_flag=0;
									//$bundle_qty2=$tmp_bl_arr[$size_id][$rollId][1];
									if($tmp_bl_arr[$size_id][$rollId][1]>$bl_size_qty)
									{
										$bundle_qty2=$bl_size_qty;
										$tmp_bl_arr[$size_id][$rollId][1]-=$bundle_qty2;//echo $bundle_qty2."==".$bl_size_qty."==".$country_id;
										$tmp_bl_arr[$size_id][$rollId][2]-=$bundle_qty2;
										$bl_roll_plies=$tmp_bl_arr[$size_id][$rollId][1];
									}
									else
									{
										$bundle_qty2=$tmp_bl_arr[$size_id][$rollId][1];
										$tmp_bl_arr[$size_id][$rollId][1]=0;
										$tmp_bl_arr[$size_id][$rollId][2]=0;
										$bl_roll_plies=0;
									}
									$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
									$bundle_prif_no=$bundle_prif_no+1;
									$bundle_no=$bundle_prif."-".$bundle_prif_no;
									$srange=$erange+1;
									$erange=$srange+$bundle_qty2-1;
									$tot_bundle_qty+=$bundle_qty2;

									$barcode_suffix_no=$barcode_suffix_no+1;
									$barcode_no=$year_id."509".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);

									//$bl_size_qty-=$bundle_qty2;
									$size_country_array[$size_id][$order_id][$country_id]-=$bundle_qty2;
									$plies-=$bundle_qty2;

									$country_type=$country_type_array[$order_id][$country_id];

									if($data_array_bundle!="") $data_array_bundle.= ",";
									$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$year_id."','".$barcode_suffix_no."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."','".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
									$bundle_id=$bundle_id+1;
									//$bl_roll_plies=$rollPliesArr[$size_id][$rollId]-($bundle_qty2+$tmp_bl_arr[$size_id][$rollId][2]);
									// $tmp_bl_arr[$size_id][$rollId][2]=0;
									$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty2;
								}
								else
								{
									$bl_roll_plies=$rollPliesArr[$size_id][$rollId];
								}

								//$bl_size_qty=$size_country_array[$size_id][$country_id];
								if($plies>0 && $bl_roll_plies>0 && $bl_size_qty>0 && $temp_bal_flag==1)
								{
									if($bl_roll_plies>=$bundle_per_pcs)
									{
										$bundle_per_size=ceil($bl_roll_plies/$bundle_per_pcs);
										for($z=1; $z<=$bundle_per_size; $z++)
										{
											$bl_size_qty=$size_country_array[$size_id][$order_id][$country_id];
											if($bl_size_qty>0 && $sizeRatioBlArr[$size_id][$pattern_no][$rollId]>0)
											{
												if($bl_roll_plies>$bundle_per_pcs)
												{
													$bundle_qty=$bundle_per_pcs;
												}
												else
												{
													$bundle_qty=$bl_roll_plies;
												}

												if($bundle_qty>$bl_size_qty)
												{
													$bundle_qty=$bl_size_qty;
												}

												if($bundle_qty>$plies)
												{
													$bundle_qty=$plies;
												}

												if($bundle_qty>$sizeRatioBlArr[$size_id][$pattern_no][$rollId])
												{
													$bundle_qty=$sizeRatioBlArr[$size_id][$pattern_no][$rollId];
												}

												$bl_roll_plies-=$bundle_qty;

												//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
												$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
												$bundle_prif_no=$bundle_prif_no+1;
												$bundle_no=$bundle_prif."-".$bundle_prif_no;
												$srange=$erange+1;
												$erange=$srange+$bundle_qty-1;
												$tot_bundle_qty+=$bundle_qty;

												$barcode_suffix_no=$barcode_suffix_no+1;
												$barcode_no=$year_id."509".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
												//$bl_size_qty-=$bundle_qty;
												$size_country_array[$size_id][$order_id][$country_id]-=$bundle_qty;
												$plies-=$bundle_qty;

												$country_type=$country_type_array[$order_id][$country_id];

												if($data_array_bundle!="") $data_array_bundle.= ",";
												$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$year_id."','".$barcode_suffix_no."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."','".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
												$bundle_id=$bundle_id+1;
												$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty;
											}
										}
									}
									else
									{
										if($bl_roll_plies>$plies)
										{
											$bundle_qty2=$plies;
											$bl_roll_plies=$bl_roll_plies-$plies;
										}
										else
										{
											$bundle_qty2=$bl_roll_plies;
											$bl_roll_plies=0;
										}

										if($bundle_qty2>0)
										{
											if($bundle_qty2>$bl_size_qty)
											{
												$tmp_bl_arr[$size_id][$rollId][1]=$bundle_qty2-$bl_size_qty;//echo $bundle_qty2."==".$bl_size_qty."==".$country_id;
												$tmp_bl_arr[$size_id][$rollId][2]=$bl_size_qty;
												$bundle_qty2=$bl_size_qty;
											}
											else { $tmp_bl_arr[$size_id][$rollId][1]=0; $tmp_bl_arr[$size_id][$rollId][2]=0; }
											//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
											$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
											$bundle_prif_no=$bundle_prif_no+1;
											$bundle_no=$bundle_prif."-".$bundle_prif_no;
											$srange=$erange+1;
											$erange=$srange+$bundle_qty2-1;
											$tot_bundle_qty+=$bundle_qty2;

											$barcode_suffix_no=$barcode_suffix_no+1;
											$barcode_no=$year_id."509".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
											//$bl_size_qty-=$bundle_qty2;
											$size_country_array[$size_id][$order_id][$country_id]-=$bundle_qty2;
											$plies-=$bundle_qty2;

											$country_type=$country_type_array[$order_id][$country_id];

											if($data_array_bundle!="") $data_array_bundle.= ",";
											$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$year_id."','".$barcode_suffix_no."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."','".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
											$bundle_id=$bundle_id+1;
											$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty2;
											//echo $rollNo.",".$srange.",".$erange;
										}
									}
								}
							}
						}
					}
				}
				$pattern_no++;
			}
		}

		//echo "10**insert into ppl_cut_lay_bundle($field_array_bundle) values".$data_array_bundle; die;
		//die;
		//echo "10**".key($size_country_array[4]);die;
		/*echo "10**";
		print_r($sizeRatioBlArr);die;
		foreach($sizeRatioBlArr as $size_id=>$size_data)
		{
			$country_type=1; $country_id=0;
			foreach($size_data as $pattern_no=>$pattern_data)
			{
				$erange=0;
				foreach($pattern_data as $rollId=>$bundle_qty)
				{
					if($bundle_qty>0)
					{
						if($bundle_qty>=$bundle_per_pcs)
						{
							$bundle_per_size=ceil($bundle_qty/$bundle_per_pcs);
							$bl_size_qty=$bundle_qty;
							for($z=1; $z<=$bundle_per_size; $z++)
							{
								if($bl_size_qty>0)
								{
									if($bl_size_qty>$bundle_per_pcs)
									{
										$bundle_qty2=$bundle_per_pcs;
									}
									else
									{
										$bundle_qty2=$bl_size_qty;
									}

									$bl_size_qty-=$bundle_qty2;

									$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
									$bundle_prif_no=$bundle_prif_no+1;
									$bundle_no=$bundle_prif."-".$bundle_prif_no;
									$srange=$erange+1;
									$erange=$srange+$bundle_qty2-1;
									$tot_bundle_qty+=$bundle_qty2;

									$rollNo=$roll_no_arr[$rollId];

									if($data_array_bundle!="") $data_array_bundle.= ",";
									$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."','".$country_id."','".$rollId."',".$rollNo.",'".$pattern_no."',1,".$user_id.",'".$pc_date_time."')";
									$bundle_id=$bundle_id+1;
								}
							}
						}
						else
						{
							$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
							$bundle_prif_no=$bundle_prif_no+1;
							$bundle_no=$bundle_prif."-".$bundle_prif_no;
							$srange=$erange+1;
							$erange=$srange+$bundle_qty-1;
							$tot_bundle_qty+=$bundle_qty;

							$rollNo=$roll_no_arr[$rollId];

							if($data_array_bundle!="") $data_array_bundle.= ",";
							$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."',".$srange.",".$erange.",".$bundle_qty.",'".$country_type."','".$country_id."','".$rollId."',".$rollNo.",'".$pattern_no."',1,".$user_id.",'".$pc_date_time."')";
							$bundle_id=$bundle_id+1;
						}
					}
				}
			}
		}*/

		//echo "10**insert into ppl_cut_lay_roll_dtls($field_array_roll_dtls) values".$data_array_roll_dtls;die;
		//echo "10**insert into ppl_cut_lay_size_dtls($field_array_size)values".$data_array_size; die;
		//echo "10**insert into ppl_cut_lay_size($field_array)values".$data_array;die;
		//echo "10**insert into ppl_cut_lay_bundle($field_array_bundle)values".$data_array_bundle; die;
		$rID=sql_insert("ppl_cut_lay_size",$field_array,$data_array,0);
		$rID_size=sql_insert("ppl_cut_lay_size_dtls",$field_array_size,$data_array_size,0);
		//echo "10**insert into ppl_cut_lay_roll_dtls($field_array_roll_dtls) values".$data_array_roll_dtls;die;
		$rID2=sql_insert("ppl_cut_lay_bundle",$field_array_bundle,$data_array_bundle,0);
		$rID3=sql_insert("ppl_cut_lay_roll_dtls",$field_array_roll_dtls,$data_array_roll_dtls,0);
		$field_array_up="marker_qty*pcs_per_bundle*updated_by*update_date";
		$data_array_up=$to_marker_qty."*'".$txt_bundle_pcs."'*'".$user_id."'*'".$pc_date_time."'";
		$rID4=sql_update("ppl_cut_lay_dtls",$field_array_up,$data_array_up,"id",$dtls_id,0);
		//echo "10**".$rID.$rID_size.$rID2.$rID3.$rID4;die;

		$total_marker_qty=$total_marker_qty_prev+$tot_marker_qnty_curr;
		$lay_balance=$plan_qty-$total_marker_qty;
		//echo "10**".$seqDatas;die;
		if($db_type==0)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4)
			{
				mysql_query("COMMIT");
				echo "0**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4)
			{
				oci_commit($con);

				echo "0**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}

		disconnect($con);
		die;
	}//last_bundle_no
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$cutting_qc_no= return_field_value("cutting_qc_no","pro_gmts_cutting_qc_mst","status_active=1 and is_deleted=0 and cutting_no='".$cut_no."'");
		$cutData=sql_select("select cut_num_prefix_no, job_no from ppl_cut_lay_mst where cutting_no = '".$cut_no."'");
		//$cut_on_prifix = return_field_value("cut_num_prefix_no","ppl_cut_lay_mst","cutting_no = '".$cut_no."'");
		$cut_on_prifix=$cutData[0][csf('cut_num_prefix_no')];
		$job_no=$cutData[0][csf('job_no')];
		//echo $cutting_qc_no."jkjkj";die;
		if($cutting_qc_no!="") { echo "200**".$mst_id."**".$dtls_id."**".$cutting_qc_no; disconnect($con); die;}


		$previous_barcode_data=sql_select("select bundle_no,barcode_no,barcode_year,barcode_prifix from ppl_cut_lay_bundle where mst_id=".$mst_id."  and  dtls_id=".$dtls_id." and status_active=1 and is_deleted=0 ");
		foreach($previous_barcode_data as $b_val)
		{
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['year']=$b_val[csf("barcode_year")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['prifix']=$b_val[csf("barcode_prifix")];
			$previous_barcode_arr[$b_val[csf("bundle_no")]]['barcode']=$b_val[csf("barcode_no")];
		}
		//print_r($previous_barcode_arr);die;


		$plan_qty= return_field_value("sum(CAST(plan_cut_qnty as INT)) as plan_qty","wo_po_color_size_breakdown","po_break_down_id in(".$order_id.") and item_number_id=".$gmt_id." and color_number_id=".$color_id." and status_active=1","plan_qty");

		$total_marker_qty_prev= return_field_value("sum(b.marker_qty) as mark_qty","ppl_cut_lay_dtls a, ppl_cut_lay_size b","a.id=b.dtls_id and a.id!=$dtls_id and  b.order_id in(".$order_id.") and a.gmt_item_id=".$gmt_id." and a.color_id=".$color_id." and a.status_active=1 and b.status_active=1 and b.is_deleted=0","mark_qty");

		$sizeRatioArr=array(); $sizeQtyArr=array(); $sizeQtyArrForC=array(); $sizeIdAgainstSeq=array();
		$id_size=return_next_id("id", "ppl_cut_lay_size_dtls", 1);
		$field_array_size="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,bundle_sequence,inserted_by,insert_date";
		for($i=1; $i<=$size_row_num; $i++)
		{
			$hidden_sizef_id="hidden_sizef_id_".$i;
			$txt_layf_balance="txt_layf_balance_".$i;
			$txt_sizef_ratio="txt_sizef_ratio_".$i;
			$txt_sizef_qty="txt_sizef_qty_".$i;
			$bundle_sequence="txt_bundle_".$i;

			if(str_replace("'",'',$$txt_sizef_qty)>0)
			{
				if(str_replace("'",'',$$bundle_sequence)>0)
				{
					$seq=str_replace("'",'',$$bundle_sequence);
				}
				else
				{
					$max_seq++;
					$seq=$max_seq;
				}

				$dataSize=$mst_id.",".$dtls_id.",".$color_id.",".$$hidden_sizef_id.",".$$txt_sizef_ratio.",".$$txt_sizef_qty.",".$seq;
				$data_array_up[$seq]=$dataSize;
				$sizeIdAgainstSeq[$seq]=str_replace("'",'',$$hidden_sizef_id);

				$sizeRatioArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_ratio);
				$sizeQtyArr[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
				$sizeQtyArrForC[str_replace("'",'',$$hidden_sizef_id)]=str_replace("'",'',$$txt_sizef_qty);
				$sizeSeqArr[str_replace("'",'',$$hidden_sizef_id)]=$seq;
			}
		}

		$seqDatas='';
		ksort($data_array_up);
		foreach($data_array_up as $sequence=>$data)
		{
			if($data_array_size!="") $data_array_size.= ",";
			$data_array_size.="(".$id_size.",".$data.",".$user_id.",'".$pc_date_time."')";
			$seqDatas.=$id_size."__".$sizeIdAgainstSeq[$sequence]."__".$sequence.",";
			$id_size++;
		}

		$roll_size_arr=array(); $roll_no_arr=array(); $rollsizeBl=array(); $rollPliesArr=array(); $sizeRatioBlArr=array();
		$rollDatas=explode("|",$roll_data);
		$rollDtls_id=return_next_id("id", "ppl_cut_lay_roll_dtls",1);
		$field_array_roll_dtls="id,mst_id,dtls_id,roll_id,roll_no,roll_wgt,plies,size_id,size_qty,inserted_by,insert_date";
		foreach($rollDatas as $data)
		{
			$datas=explode("=",$data);
			$roll_no=$datas[1];
			$roll_id=$datas[2];
			$roll_wgt=$datas[3];
			$plies=$datas[4];

			if($roll_id=="" || $roll_id==0) $roll_id=$rollDtls_id;


			foreach($sizeRatioArr as $size_id=>$size_ratio)
			{
				$roll_no_arr[$size_id][$roll_id]=$roll_no;
				$size_qty=$size_ratio*$plies;
				if($data_array_roll_dtls!="") $data_array_roll_dtls.= ",";
				$data_array_roll_dtls.="(".$rollDtls_id.",".$mst_id.",".$dtls_id.",'".$roll_id."','".$roll_no."','".$roll_wgt."','".$plies."','".$size_id."',".$size_qty.",".$user_id.",'".$pc_date_time."')";

				$rollsizeBl[$size_id][$roll_id]=$size_qty;
				$rollPliesArr[$size_id][$roll_id]=$plies;

				$rollDtls_id++;
			}
		}

		$bundle_no_array=array(); $last_rmg_no='';
		$bundle_id=return_next_id("id", "ppl_cut_lay_bundle",1);
		$field_array_bundle="id,mst_id,dtls_id,size_id,bundle_num_prefix,bundle_num_prefix_no,bundle_no,barcode_year,barcode_prifix,barcode_no,number_start,number_end,size_qty,country_type,country_id,roll_id,roll_no,pattern_no,order_id,is_excess,color_type_id,inserted_by,insert_date";

		//$last_rmg_no=return_field_value("max(a.number_end) as last_rmg", "ppl_cut_lay_bundle a, ppl_cut_lay_mst b","a.mst_id=b.id and a.mst_id='".$mst_id."' and a.dtls_id!=$dtls_id","last_rmg");
		//$last_bundle_no=return_field_value("max(bundle_num_prefix_no) as last_prefix", "ppl_cut_lay_bundle","mst_id=$mst_id and dtls_id!=$dtls_id","last_prefix");

		$bundleData=sql_select("select max(number_end) as last_rmg, max(bundle_num_prefix_no) as last_prefix from ppl_cut_lay_bundle where mst_id=$mst_id and dtls_id!=$dtls_id");
		$last_rmg_no=$bundleData[0][csf('last_rmg')];
		$last_bundle_no=$bundleData[0][csf('last_prefix')];

		$tot_marker_qnty_curr=0; $bundle_prif_no=$last_bundle_no; $size_country_array=array(); $country_type_array=array();
		//echo "10**"."SELECT size_id, max(number_end) as last_rmg FROM ppl_cut_lay_bundle WHERE mst_id='".$mst_id."' and dtls_id!='".$dtls_id."' group by size_id";die;
		$id=return_next_id("id", "ppl_cut_lay_size", 1);
		$field_array="id,mst_id,dtls_id,color_id,size_id,size_ratio,marker_qty,country_type,country_id,excess_perc,order_id,size_wise_repeat,inserted_by,insert_date";
		for($i=1; $i<=$row_num; $i++)
		{
			$txt_size_id="hidden_size_id_".$i;
			$txt_lay_balance="txt_lay_balance_".$i;
			$cboCountryType="cboCountryType_".$i;
			$cboCountry="cboCountry_".$i;
			$excess_perc="txt_excess_".$i;
			$po_id="poId_".$i;
			$txt_size_qty="txt_size_qty_".$i;

			$marker_qty=0;
			$order_id=str_replace("'",'',$$po_id);
			$size_id=str_replace("'",'',$$txt_size_id);
			$lay_balance=str_replace("'",'',$$txt_lay_balance);
			//$size_qty=$sizeQtyArrForC[$size_id];
			$marker_qty=str_replace("'",'',$$txt_size_qty);
			$country_type_array[$order_id][str_replace("'",'',$$cboCountry)]=str_replace("'",'',$$cboCountryType);

			if($marker_qty>0)
			{
				$size_country_array[$size_id][$order_id][str_replace("'",'',$$cboCountry)]+=$marker_qty;
				$tot_marker_qnty_curr+=$marker_qty;

				if($data_array!="") $data_array.= ",";
				$data_array.="(".$id.",".$mst_id.",".$dtls_id.",".$color_id.",".$$txt_size_id.",0,".$marker_qty.",".$$cboCountryType.",".$$cboCountry.",".$$excess_perc.",".$$po_id.",0,".$user_id.",'".$pc_date_time."')";
				$id=$id+1;
			}
		}

		//echo "10**";
		//print_r($sizeRatioBlArr);die;
		$company_sort_name=explode("-",$cut_no); $bundle_per_pcs=str_replace("'","",$bundle_per_pcs);
		asort($sizeSeqArr);
		//foreach($sizeQtyArr as $size_id=>$size_qty)
		foreach($sizeSeqArr as $size_id=>$size_seq)
		{
			$size_ratio=$sizeRatioArr[$size_id]; $size_qty=$sizeQtyArr[$size_id]; $pattern_no="A";
			for($k=1; $k<=$size_ratio; $k++)
			{
				foreach($roll_no_arr[$size_id] as $rollId=>$rollNo)
				{
					$sizeRatioBlArr[$size_id][$pattern_no][$rollId]=$rollPliesArr[$size_id][$rollId];
				}
				$pattern_no++;
			}
		}
		//print_r($rollsizeBl);//die;
		//foreach($sizeQtyArr as $size_id=>$size_qty)
		//$year_id=date('Y',time());
		//if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$cut_year_ex=explode("-", $cut_no);
		$year_id=$cut_year_ex[1];

 		$barcode_suffix_no=return_field_value("max(barcode_prifix) as suffix_no","ppl_cut_lay_bundle","barcode_year=$year_id","suffix_no");
		foreach($sizeSeqArr as $size_id=>$size_seq)
		{
			$size_ratio=$sizeRatioArr[$size_id]; $size_qty=$sizeQtyArr[$size_id]; $pattern_no="A";
			for($k=1; $k<=$size_ratio; $k++)
			{
				$erange=0; $plies=$txt_plies; $tmp_bl_arr=array();
				foreach($roll_no_arr[$size_id] as $rollId=>$rollNo)
				{
					foreach($size_country_array[$size_id] as $order_id=>$order_data)
					{
						//print_r($order_data);die;
						foreach($order_data as $country_id=>$size_country_qty)
						{
							if($sizeRatioBlArr[$size_id][$pattern_no][$rollId]>0)
							{
								$temp_bal_flag=1;
								$bl_size_qty=$size_country_array[$size_id][$order_id][$country_id];
								//echo $bundle_no."=".$bundle_qty2."=".$bl_size_qty."=".$bl_roll_plies.'<br>';
								if($plies>0 && $tmp_bl_arr[$size_id][$rollId][1]>0 && $bl_size_qty>0)
								{
									$temp_bal_flag=0;
									//$bundle_qty2=$tmp_bl_arr[$size_id][$rollId][1];
									if($tmp_bl_arr[$size_id][$rollId][1]>$bl_size_qty)
									{
										$bundle_qty2=$bl_size_qty;
										$tmp_bl_arr[$size_id][$rollId][1]-=$bundle_qty2;//echo $bundle_qty2."==".$bl_size_qty."==".$country_id;
										$tmp_bl_arr[$size_id][$rollId][2]-=$bundle_qty2;
										$bl_roll_plies=$tmp_bl_arr[$size_id][$rollId][1];
									}
									else
									{
										$bundle_qty2=$tmp_bl_arr[$size_id][$rollId][1];
										$tmp_bl_arr[$size_id][$rollId][1]=0;
										$tmp_bl_arr[$size_id][$rollId][2]=0;
										$bl_roll_plies=0;
									}
									//$bundle_qty2=$bl_size_qty;
									//$tmp_bl_arr[$size_id][$rollId][1]=0;
									//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
									$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
									$bundle_prif_no=$bundle_prif_no+1;
									$bundle_no=$bundle_prif."-".$bundle_prif_no;
									$srange=$erange+1;
									$erange=$srange+$bundle_qty2-1;
									$tot_bundle_qty+=$bundle_qty2;

									if(empty($previous_barcode_arr[$bundle_no]))
									{
										$barcode_suffix_no=$barcode_suffix_no+1;
										$up_barcode_suffix=$barcode_suffix_no;
										$up_barcode_year=$year_id;
										$barcode_no=$year_id."509".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
									}
									else
									{
										$up_barcode_suffix=$previous_barcode_arr[$bundle_no]['prifix'];
										$up_barcode_year=$previous_barcode_arr[$bundle_no]['year'];
										$barcode_no=$previous_barcode_arr[$bundle_no]['barcode'];
									}
									//$bl_size_qty-=$bundle_qty2;
									$size_country_array[$size_id][$order_id][$country_id]-=$bundle_qty2;
									$plies-=$bundle_qty2;
									//echo $bundle_no."--".$bundle_qty2.'<br>';
									$country_type=$country_type_array[$order_id][$country_id];

									if($data_array_bundle!="") $data_array_bundle.= ",";
									$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."','".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
									$bundle_id=$bundle_id+1;
									//$bl_roll_plies=$rollPliesArr[$size_id][$rollId]-($bundle_qty2+$tmp_bl_arr[$size_id][$rollId][2]);
									//$tmp_bl_arr[$size_id][$rollId][2]=0;
									$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty2;
								}
								else
								{
									$bl_roll_plies=$rollPliesArr[$size_id][$rollId];
								}

								//$bl_size_qty=$size_country_array[$size_id][$country_id];
								//echo $plies.'='.$bl_roll_plies.'='.$bl_size_qty.'='.$temp_bal_flag.'<br>';
								if($plies>0 && $bl_roll_plies>0 && $bl_size_qty>0 && $temp_bal_flag==1)
								{
									if($bl_roll_plies>=$bundle_per_pcs)
									{
										$bundle_per_size=ceil($bl_roll_plies/$bundle_per_pcs);
										for($z=1; $z<=$bundle_per_size; $z++)
										{
											$bl_size_qty=$size_country_array[$size_id][$order_id][$country_id];
											if($bl_size_qty>0 && $sizeRatioBlArr[$size_id][$pattern_no][$rollId]>0)
											{
												if($bl_roll_plies>$bundle_per_pcs)
												{
													$bundle_qty=$bundle_per_pcs;
												}
												else
												{
													$bundle_qty=$bl_roll_plies;
												}

												if($bundle_qty>$bl_size_qty)
												{
													$bundle_qty=$bl_size_qty;
												}

												if($bundle_qty>$plies)
												{
													$bundle_qty=$plies;
												}

												if($bundle_qty>$sizeRatioBlArr[$size_id][$pattern_no][$rollId])
												{
													$bundle_qty=$sizeRatioBlArr[$size_id][$pattern_no][$rollId];
												}

												$bl_roll_plies-=$bundle_qty;

												//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
												$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
												$bundle_prif_no=$bundle_prif_no+1;
												$bundle_no=$bundle_prif."-".$bundle_prif_no;
												$srange=$erange+1;
												$erange=$srange+$bundle_qty-1;
												$tot_bundle_qty+=$bundle_qty;

												if(empty($previous_barcode_arr[$bundle_no]))
												{
													$barcode_suffix_no=$barcode_suffix_no+1;
													$up_barcode_suffix=$barcode_suffix_no;
													$up_barcode_year=$year_id;
													$barcode_no=$year_id."509".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
												}
												else
												{
													$up_barcode_suffix=$previous_barcode_arr[$bundle_no]['prifix'];
													$up_barcode_year=$previous_barcode_arr[$bundle_no]['year'];
													$barcode_no=$previous_barcode_arr[$bundle_no]['barcode'];
												}

												//$bl_size_qty-=$bundle_qty;
												$size_country_array[$size_id][$order_id][$country_id]-=$bundle_qty;
												$plies-=$bundle_qty;

												$country_type=$country_type_array[$order_id][$country_id];

												if($data_array_bundle!="") $data_array_bundle.= ",";
												$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."','".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
												$bundle_id=$bundle_id+1;
												$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty;
											}
										}
									}
									else
									{
										if($bl_roll_plies>$plies)
										{
											$bundle_qty2=$plies;
											$bl_roll_plies=$bl_roll_plies-$plies;
											//echo $bundle_no."=".$bundle_qty2."=".$bl_size_qty."=".$bl_roll_plies.'<br>';
										}
										else
										{
											$bundle_qty2=$bl_roll_plies;
											$bl_roll_plies=0;
										}

										if($bundle_qty2>0)
										{
											if($bundle_qty2>$bl_size_qty)
											{
												$tmp_bl_arr[$size_id][$rollId][1]=$bundle_qty2-$bl_size_qty;//echo $bundle_qty2."==".$bl_size_qty."==".$country_id;
												$tmp_bl_arr[$size_id][$rollId][2]=$bl_size_qty;
												$bundle_qty2=$bl_size_qty;
											}
											else { $tmp_bl_arr[$size_id][$rollId][1]=0; $tmp_bl_arr[$size_id][$rollId][2]=0; }
											//$bundle_prif=$company_sort_name[0]."-".$cut_on_prifix;
											//echo $bundle_no."=".$bundle_qty2."=".$bl_size_qty."=".$bl_roll_plies.'--1<br>';

											$bundle_prif=$company_sort_name[0]."-".$year_id."-".$cut_on_prifix;
											$bundle_prif_no=$bundle_prif_no+1;
											$bundle_no=$bundle_prif."-".$bundle_prif_no;
											$srange=$erange+1;
											$erange=$srange+$bundle_qty2-1;
											$tot_bundle_qty+=$bundle_qty2;

											//echo $bundle_no."=".$bundle_qty2."=".$bl_size_qty."=".$bl_roll_plies.'--2<br>';
											if(empty($previous_barcode_arr[$bundle_no]))
											{
												$barcode_suffix_no=$barcode_suffix_no+1;
												$up_barcode_suffix=$barcode_suffix_no;
												$up_barcode_year=$year_id;
												$barcode_no=$year_id."509".str_pad($barcode_suffix_no,10,"0",STR_PAD_LEFT);
											}
											else
											{
												$up_barcode_suffix=$previous_barcode_arr[$bundle_no]['prifix'];
												$up_barcode_year=$previous_barcode_arr[$bundle_no]['year'];
												$barcode_no=$previous_barcode_arr[$bundle_no]['barcode'];
											}

											//$bl_size_qty-=$bundle_qty2;
											$size_country_array[$size_id][$order_id][$country_id]-=$bundle_qty2;
											$plies-=$bundle_qty2;

											$country_type=$country_type_array[$order_id][$country_id];

											if($data_array_bundle!="") $data_array_bundle.= ",";
											$data_array_bundle.="(".$bundle_id.",".$mst_id.",".$dtls_id.",".$size_id.",'".$bundle_prif."','".$bundle_prif_no."','".$bundle_no."','".$up_barcode_year."','".$up_barcode_suffix."','".$barcode_no."',".$srange.",".$erange.",".$bundle_qty2.",'".$country_type."',".$country_id.",'".$rollId."',".$rollNo.",'".$pattern_no."','".$order_id."',0,".$color_type_id.",".$user_id.",'".$pc_date_time."')";
											$bundle_id=$bundle_id+1;
											$sizeRatioBlArr[$size_id][$pattern_no][$rollId]-=$bundle_qty2;
											//echo $rollNo.",".$srange.",".$erange;
										}
									}
								}
							}
						}
					}
				}
				$pattern_no++;
			}
		}

		//die;
		//echo "10**";print_r($sizeRatioBlArr);die;
	    //echo "10**insert into ppl_cut_lay_bundle($field_array_bundle)values".$data_array_bundle;die;
		//echo "10**insert into ppl_cut_lay_size($field_array)values".$data_array;die;

		$delete=execute_query("delete from ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		$delete_size=execute_query("delete from ppl_cut_lay_size_dtls where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		$delete_bundle=execute_query("delete from ppl_cut_lay_bundle where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);
		$delete_roll=execute_query("delete from ppl_cut_lay_roll_dtls where mst_id=".$mst_id." and dtls_id=".$dtls_id."",0);

		$rID=sql_insert("ppl_cut_lay_size",$field_array,$data_array,0);
		$rID_size=sql_insert("ppl_cut_lay_size_dtls",$field_array_size,$data_array_size,0);
		$rID2=sql_insert("ppl_cut_lay_bundle",$field_array_bundle,$data_array_bundle,0);
		//echo "10**insert into ppl_cut_lay_bundle($field_array_bundle) values".$data_array_bundle."**".$rID2;die;
		$rID3=sql_insert("ppl_cut_lay_roll_dtls",$field_array_roll_dtls,$data_array_roll_dtls,0);
		$field_array_up="marker_qty*pcs_per_bundle*updated_by*update_date";
		$data_array_up="".$to_marker_qty."*'".$txt_bundle_pcs."'*'".$user_id."'*'".$pc_date_time."'";
		$rID4=sql_update("ppl_cut_lay_dtls",$field_array_up,$data_array_up,"id",$dtls_id,0);

		//echo "10**".$rID ."**". $rID_size ."**". $rID2 ."**". $rID3 ."**". $rID4 ."**". $delete ."**". $delete_size."**".$delete_bundle."**".$delete_roll;die;

		$total_marker_qty=$total_marker_qty_prev+$tot_marker_qnty_curr;
		$lay_balance=$plan_qty-$total_marker_qty;
		//echo "10**".$lay_balance."**".$total_marker_qty."**".$total_marker_qty_prev."**".$tot_marker_qnty_curr;die;

		if($db_type==0)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4 && $delete && $delete_size && $delete_bundle && $delete_roll)
			{
				mysql_query("COMMIT");
				echo "1**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1)
		{
			if($rID && $rID_size && $rID2 && $rID3 && $rID4 && $delete && $delete_size && $delete_bundle && $delete_roll)
			{
				oci_commit($con);
				echo "1**".$mst_id."**".$dtls_id."**".substr($seqDatas,0,-1)."**".$plan_qty."**".$total_marker_qty."**".$lay_balance;
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
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		exit();
	}
}

if($action=="show_bundle_list_view")
{
	$ex_data= explode("**",$data);
	$mst_id=$ex_data[0];
	$dtls_id=$ex_data[1];

	$country_arr=return_library_array("select id, country_name from lib_country",'id','country_name');
	$po_country_array=array();
	$sql_query=sql_select("select distinct a.country_id as country_id from wo_po_color_size_breakdown a, ppl_cut_lay_dtls b, ppl_cut_lay_size c where a.item_number_id=b.gmt_item_id and a.po_break_down_id=c.order_id and b.id=c.dtls_id and a.color_number_id=b.color_id and b.mst_id=$mst_id and b.id=$dtls_id and a.status_active=1 and a.is_deleted=0");
	$size_details=array(); $sizeId_arr=array(); $shipDate_arr=array();
	foreach($sql_query as $row)
	{
		$po_country_array[$row[csf('country_id')]]=$country_arr[$row[csf('country_id')]];
	}

	$po_no_arr=return_library_array("select a.id, a.po_number from wo_po_break_down a, ppl_cut_lay_size b where a.id=b.order_id and b.mst_id=$mst_id and b.dtls_id=$dtls_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number",'id','po_number');

	?>
    <fieldset style="width:960px">
        <legend>Bundle No and RMG qty details</legend>
        <table cellpadding="0" cellspacing="0" width="950" rules="all" border="1" class="rpt_table" id="tbl_bundle_list_save">
            <thead class="form_table_header">
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th colspan="2">RMG Number</th>
                <th>
                    <input type="hidden" id="hidden_mst_id" name="hidden_mst_id" value="<? echo $mst_id; ?>" />
                    <input type="hidden" id="hidden_detls_id" name="hidden_detls_id" value="<? echo $dtls_id; ?>" />
                </th>
                <th>Report &nbsp;</th>
            </thead>
            <thead class="form_table_header">
                <th>SL No</th>
                <th>Order No.</th>
                <th>Country Type</th>
                <th>Country Name</th>
                <th>Size</th>
                <th>Pattern</th>
                <th>Roll No</th>
                <th>Bundle No</th>
                <th>Quantity</th>
                <th>From</th>
                <th>To</th>
                <th></th>
                <th width="40"><input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>
            </thead>
            <tbody id="trBundleListSave">
            <?
            $sql_size_name=sql_select("select size_id from ppl_cut_lay_size where mst_id=".$mst_id." and dtls_id=".$dtls_id."");
            $size_colour_arr=array();
            foreach($sql_size_name as $asf)
            {
                $size_colour_arr[$asf[csf("size_id")]]=$size_arr[$asf[csf("size_id")]];
            }

            $bundle_data=sql_select("select a.id,a.bundle_no,a.size_id,a.number_start,a.number_end,a.size_qty,a.update_flag,a.update_value, a.country_type, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess,a.barcode_no, a.order_id from ppl_cut_lay_bundle a where a.mst_id=".$mst_id." and a.dtls_id=".$dtls_id." order by a.id ASC");
            $i=1;
            foreach($bundle_data as $row)
            {
                $update_f_value="";
                if(str_replace("'","",$row[csf('update_flag')])==1)
                {
                    $update_f_value=explode("**",$row[csf('update_value')]);
                }
            ?>
                <tr id="trBundleListSave_<? echo $i;  ?>">
                    <td align="center"  id="">
                        <input type="text" id="sirialNo_<? echo $i;  ?>" name="sirialNo[]" style="width:25px;" class="text_boxes"  value="<? echo $i;  ?>"  disabled/>
                        <input type="hidden" id="hiddenExtraTr_<? echo $i;  ?>" name="hiddenExtraTr[]"  value="<? echo $i;  ?>"/>
                        <input type="hidden" id="hiddenUpdateFlag_<? echo $i; ?>" name="hiddenUpdateFlag[]"  value="<? echo $row[csf('update_flag')];?> "/>
                        <input type="hidden" id="hiddenUpdateValue_<? echo $i; ?>" name="hiddenUpdateValue[]"  value="<? echo $row[csf('update_value')];?> " />
                    </td>
                    <td align="center">
						<?
                            echo create_drop_down( "cboPoId_".$i, 130, $po_no_arr,'', 0, '',$row[csf('order_id')],'',1,'','','','','','','cboPoId[]');
                        ?>
                    </td>
                    <td align="center">
                        <?
                            echo create_drop_down( "cboCountryTypeB_".$i, 70, $country_type,'', 0, '',$row[csf('country_type')],'',1);
                        ?>
                        <input type="hidden" id="hiddenCountryTypeB_<? echo $i;  ?>" name="hiddenCountryTypeB[]"  value="<? echo $row[csf('country_type')];?> "/>
                    </td>
                    <td align="center">
                        <?
							 echo create_drop_down("cboCountryB_".$i,80,$po_country_array,'',1,'',$row[csf('country_id')],'',1,'','','','','','','cboCountryB[]');
                        ?>
                        <input type="hidden" id="hiddenCountryB_<? echo $i;  ?>" name="hiddenCountryB[]"  value="<? echo $row[csf('country_id')];?> " />
                    </td>
                    <td align="center" id="update_sizename_<? echo $i;  ?>">
                        <select name="sizeName[]" id="sizeName_<? echo $i;  ?>" class="text_boxes" style="width:60px; text-align:center;  <? if($update_f_value[1]!="") echo "background-color:#F3F;"; ?> "  disabled  >
                        <?
                        // $l=1;
                        foreach($sql_size_name as $asf)
                        {
                            if($asf[csf("size_id")]==$row[csf('size_id')]) $select_text="selected"; else $select_text="";
                            ?>
                            <option value="<? echo $asf[csf("size_id")]; ?> " <? echo $select_text;  ?>><? echo $size_arr[$asf[csf("size_id")]]; ?> </option>
                            <?
                            }
                        ?>
                        </select>
                        <input type="hidden" name="hiddenSizeId[]" id="hiddenSizeId_<? echo $i;  ?>" value="<? echo $row[csf('size_id')];  ?>" />
                    </td>
                    <td align="center"><input type="text" name="patternNo[]" id="patternNo_<? echo $i; ?>" value="<? echo $row[csf('pattern_no')]; ?>" class="text_boxes" style="width:35px; text-align:center" disabled/><input type="hidden" name="isExcess[]" id="isExcess_<? echo $i; ?>" value="<? echo $row[csf('is_excess')]; ?>"/></td>
                    <td align="center">
                    	<input type="text" name="rollNo[]" id="rollNo_<? echo $i;  ?>" value="<? echo $row[csf('roll_no')];  ?>" class="text_boxes"  style="width:40px;  text-align:center" disabled/>
                        <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
                    </td>
                    <td align="center">
                    	<input type="text" name="bundleNo[]" id="bundleNo_<? echo $i;  ?>" value="<? echo $row[csf('bundle_no')];  ?>" class="text_boxes"  style="width:120px;  text-align:center" disabled  title="<?php echo $row[csf('barcode_no')]; ?>"/>
                    </td>
                    <td align="center">
                        <input type="text" name="bundleSizeQty[]" id="bundleSizeQty_<? echo $i;  ?>" onBlur="bundle_calclution(<? echo $i;  ?>)" value="<? echo $row[csf('size_qty')];  ?>"  style="width:40px; text-align:right; <? if($update_f_value[0]!="") echo "background-color:#F3F;"; ?>"  class="text_boxes"  disabled/>
                        <input type="hidden" name="hiddenSizeQty[]" id="hiddenSizeQty_<? echo $i;  ?>" value="<? echo $row[csf('size_qty')];  ?>" disabled/>
                    </td>
                    <td align="center">
                    	<input type="text" name="rmgNoStart[]" id="rmgNoStart_<? echo $i;  ?>" value="<? echo $row[csf('number_start')];  ?>" style="width:40px; text-align:right" class="text_boxes"  disabled />
                    </td>
                    <td align="center">
                    	<input type="text" name="rmgNoEnd[]" id="rmgNoEnd_<? echo $i;  ?>" value="<? echo $row[csf('number_end')];  ?>" style="width:40px; text-align:right" class="text_boxes"  disabled/>
                    </td>
                    <td align="center">
                        <input type="button" value="+" name="addButton[]" id="addButton_<? echo $i;  ?>" style="width:30px" class="formbuttonplasminus" onClick="fnc_addRow('<? echo $i;  ?>','<? echo $i;  ?>')"/>
                        <input type="button" value="Adj." name="rowUpdate[]" id="rowUpdate_<? echo $i;  ?>" style="width:40px;" class="formbuttonplasminus" onClick="fnc_updateRow('<? echo $i;  ?>')"/>
                    </td>
                    <td align="center">
                        <input id="chkbundle_<? echo $i;  ?>" type="checkbox" name="chkbundle" >
                        <input type="hidden" id="hiddenid_<? echo $i; ?>" name="hiddenid_<? echo $i; ?>" value="<? echo $row[csf('id')];  ?>" style="width:15px;" class="text_boxes"/>
                    </td>
                </tr>
            <?
            $i++;
            }
            ?>
            </tbody>
        </table>
        <table cellpadding="0" cellspacing="0" width="700">
            <tr>
                <td colspan="10" align="center" class="button_container">
                    <? echo load_submit_buttons($permission, "fnc_cut_lay_bundle_info", 1,0,"clear_size_form()",1);?>
                </td>
            </tr>
        </table>
    </fieldset>
    <?
	exit();
}

if($action=="cut_lay_bundle_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name" );

	$company_library=array(); $company_short_arr=array();
	$comapny_data=sql_select("select id, company_short_name, company_name from lib_company");
	$cur_page=1;
	foreach($comapny_data as $comR)
	{
		$company_library[$comR[csf('id')]]=$comR[csf('company_name')];
		$company_short_arr[$comR[csf('id')]]=$comR[csf('company_short_name')];
	}

	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$working_comp_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$working_location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$working_floor_library=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name"  );
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );


	$sql="select a.cut_num_prefix_no, a.table_no,a.cutting_no, a.job_no, a.entry_date,a.marker_length, a.marker_width, a.fabric_width, a.gsm, a.batch_id as batch_no,a.location_id,a.floor_id,a.working_company_id,a.remarks, b.order_id, b.color_id, b.gmt_item_id, b.plies, b.marker_qty, b.order_qty, b.order_ids, b.batch_id, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id='$data[1]' and a.company_id='$data[0]' and b.id='$data[2]' ";

	//echo $sql;die;

	$dataArray=sql_select($sql);
	$sql_buyer=sql_select("select buyer_name,style_ref_no as ref,style_description as des from wo_po_details_master where job_no='".$dataArray[0][csf('job_no')]."' and company_name=$data[0]");
	$style_ref=$sql_buyer[0][csf('ref')];
	$style_desc=$sql_buyer[0][csf('des')];
	$cut_no_prifix=$dataArray[0][csf('cut_num_prefix_no')];
	$cut_no=$dataArray[0][csf('cutting_no')];
	$order_cut_no=$dataArray[0][csf('order_cut_no')];
	$remarks=$dataArray[0][csf('remarks')];
	$batch_no=$dataArray[0][csf('batch_no')];


	//echo "<pre>";print_r($data_array);die;

	$po_arr=return_library_array( "select id, po_number from wo_po_break_down where id in(".$dataArray[0][csf('order_ids')].")","id","po_number" );
	$intref_arr=return_library_array( "select job_no_mst, grouping from wo_po_break_down where id in(".$dataArray[0][csf('order_ids')].")", 'job_no_mst', 'grouping' );


	//echo "<pre>";print_r($batch_no);die;


?>
<div style="width:1000px; " align="center" >
    <table width="990" cellspacing="0" align="center">
         <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Lay and Bundle Information</u></strong></td>
        </tr>
         <tr>
        	<td width="120"><strong>Cut No:</strong></td><td width="160"><? echo $cut_no; ?></td>
            <td width="120"><strong>Table No :</strong></td> <td width="160"><?  echo $table_no_library[$dataArray[0][csf('table_no')]]; ?></td>
            <td width="120"><strong>Job No :</strong></td> <td width="160"><? echo $dataArray[0][csf('job_no')]; ?></td>
        </tr>
         <tr>
			<td><strong>Buyer:</strong></td> <td width="160"><? echo $buyer_library[$sql_buyer[0][csf('buyer_name')]]; ?></td>
            <td><strong>Batch No:</strong></td> <td width="160"><? echo $batch_no; ?></td>
            <td><strong>Internal Ref:</strong></td> <td width="160"><?php echo $intref_arr[$dataArray[0][csf('job_no')]]; ?></td>
        </tr>
        <tr>
			 <td><strong>Gmt Item:</strong></td> <td width="160"><? echo $garments_item[$dataArray[0][csf('gmt_item_id')]]; ?></td>
             <td><strong>Color :</strong></td><td width="160"><? echo$color_library[$dataArray[0][csf('color_id')]]; ?></td>
             <td><strong>Marker Length :</strong></td><td width="160"><? echo $dataArray[0][csf('marker_length')]; ?></td>
        </tr>
        <tr>
             <td><strong>Marker Width :</strong></td><td width="160"><? echo $dataArray[0][csf('marker_width')]; ?></td>
            <td><strong>Fabric Width:</strong></td><td width="160"><? echo $dataArray[0][csf('fabric_width')]; ?></td>
              <td><strong>Gsm:</strong></td> <td width="160"><? echo $dataArray[0][csf('gsm')]; ?></td>
        </tr>
        <tr>
             <td><strong>Order Cut No:</strong></td> <td width="160"><? echo $order_cut_no; ?></td>
             <td><strong>Plies:</strong></td> <td width="160"><? echo $dataArray[0][csf('plies')]; ?></td>
             <td><strong>Cut Date:</strong></td><td width="160"><? echo $dataArray[0][csf('entry_date')]; ?></td>
        </tr>
        <tr>
       		 <td><strong>Style Ref:</strong></td> <td width="160"><? echo $style_ref; ?></td>
             <td><strong>Style Desc.:</strong></td> <td width="160"><? echo $style_desc; ?></td>
             <td align="left" colspan="2" id="barcode_img_id"></td>
        </tr>
        <tr>
       		 <td><strong>Working Company:</strong></td> <td width="160"><? echo $working_comp_library[$dataArray[0][csf('working_company_id')]]; ?></td>
             <td><strong>Working Location:</strong></td> <td width="160"><? echo $working_location_library[$dataArray[0][csf('location_id')]]; ?></td>


             <td><strong>Working Floor:</strong></td> <td width="160"><? echo $working_floor_library[$dataArray[0][csf('floor_id')]]; ?></td>


        </tr>
        <tr>
		    <td><strong>Remarks:</strong></td><td width="100"><? echo $remarks;?></td>


           <td><strong>Cutting Part:</strong></td> <td  colspan="5"><? echo $data[5]; ?></td>
		   <td><strong>Page:</strong></td> <td width="120"><?echo $cur_page ?></td>


	</tr>

</table>
    <br>
     <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

			function generateBarcode( valuess ){

					var value = valuess;
					var btype = 'code39';
					var renderer ='bmp';
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
					 value = {code:value, rect: false};
					$("#barcode_img_id").show().barcode(value, btype, settings);
				}
			   generateBarcode('<? echo $dataArray[0][csf('cutting_no')]; ?>');
	 </script>
	<div style="width:1200px;">
    	<table align="center" cellspacing="0" width="1180" border="1" rules="all" class="rpt_table" >
              <thead bgcolor="#dddddd" align="center">
					<th></th>
					<th colspan="8"></th>
					<th>Bundle</th>
					<th colspan="2">RMG Number</th>
					<th colspan="3">QC</th>
					<th></th>
              </thead>
              <thead bgcolor="#dddddd" align="center">
                      <th width="40">SL</th>
                      <th width="100">Cut No</th>
                      <th width="90">Order No</th>
                      <th>Country Name</th>
                      <th width="70">Pattern No</th>
                      <th width="60">Roll No</th>
                      <th width="80">Batch No</th>
                      <th width="80">Bundle No</th>
                      <th width="80">Barcode</th>
                      <th width="70">Quantity</th>
                      <th width="70">From</th>
                      <th width="70">To</th>
                      <th width="80">Size</th>
                      <th width="40">REJ</th>
                      <th width="40">REP</th>
                      <th width="150">Remarks</th>
                </thead>
                <tbody>
                <?
					 $batchNo_arr=return_library_array( "select a.id, a.batch_no from pro_roll_details a, ppl_cut_lay_bundle b where a.id=b.roll_id and a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.entry_form=509","id","batch_no" );

					 if($data[4]==0) $country_cond=""; else $country_cond=" and a.country_id='".$data[4]."'";
                     $size_data=sql_select("select a.id,a.size_id,a.bundle_sequence,a.marker_qty,a.size_ratio from ppl_cut_lay_size_dtls a where a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.bundle_sequence ASC");
					 //echo "select a.id,a.size_id,a.bundle_sequence,a.marker_qty,a.size_ratio from ppl_cut_lay_size_dtls a where a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.bundle_sequence ASC";
					 $size_ratio_arr=array();
					 foreach ($size_data as $val) {
						$size_ratio_arr[$val[csf("size_id")]]["size_ratio"]=$val[csf("size_ratio")];
					 }
                     $j=1;
                     foreach($size_data as $size_val)
                     {
						$total_marker_qty_size=0;
                       	$bundle_data=sql_select("SELECT a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,a.barcode_no,a.update_flag from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC");
						//echo "select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC";
                        foreach($bundle_data as $row)
                        {
                        	$bundleNo = $row[csf("bundle_no")];
                        	$update_flag = $row[csf("update_flag")];
                        	if ($update_flag==1) {
                        		 $remarks="Grading";
                        	}else{
                        		 $remarks="";
                        	}

							$tmpBundleArr = explode('-', $bundleNo);
							$bundleNoNumber = '';

							for ($i=3; $i < count($tmpBundleArr); $i++) {
								$bundleNoNumber .= $tmpBundleArr[$i] . '-';
							}
							$bundleNoNumber = rtrim($bundleNoNumber, '-');
               	 			?>
                           <tr>
                               <td align="center"><? echo $j;  ?></td>
                               <td align="center"><? echo $cut_no; ?></td>
                               <td style="word-wrap:break-word"><? echo $po_arr[$row[csf('order_id')]]; ?></td>
                               <td style="word-wrap:break-word"><? echo $country_arr[$row[csf('country_id')]]; ?></td>
                               <td align="center"><? echo $row[csf('pattern_no')]; ?></td>
                               <td align="center"><? echo $row[csf('roll_no')]; ?></td>
                               <td style="word-wrap:break-word"><? echo $batchNo_arr[$row[csf('roll_id')]]; ?></td>
                               <td align="center"><? echo $bundleNoNumber; ?></td>
                               <td align="center"><? echo $row[csf('barcode_no')];?></td>
                               <td align="center"><? echo $row[csf('size_qty')];  ?></td>
                               <td align="center"><? echo $row[csf('number_start')];  ?></td>
                               <td align="center"><? echo $row[csf('number_end')];  ?></td>
                               <td align="center"><? echo $size_arr[$row[csf('size_id')]];  ?></td>
                               <td align="center"></td>
                               <td align="center"></td>
                               <td align="center"><? echo $remarks; ?></td>
                          </tr>
               	 			<?
                           $j++;
						   $total_marker_qty_size+=$row[csf('size_qty')];
						   $total_marker_qty+=$row[csf('size_qty')];
                         }
                       //  $total_marker_qty+=$size_val[csf('marker_qty')];
                         $size_wise_qnty_arr[$row[csf('size_id')]]['qty']+=$total_marker_qty_size;
                         $size_wise_qnty_arr[$row[csf('size_id')]]['ratio']=$size_val[csf('size_id')];
                		?>
                        <tr bgcolor="#eeeeee">
                           <td align="center"></td>
                           <td  colspan="8" align="right"><? echo $size_arr[$row[csf('size_id')]]; ?> Size Total</td>
                           <td align="center"><? echo $total_marker_qty_size;  ?></td>
                           <td align="center"></td>
                           <td align="center"></td>
                           <td align="center"></td>
                           <td align="center"></td>
                           <td align="center"></td>
                           <td align="center"></td>
                        </tr>
                <?
                     }

                ?>
               <tr bgcolor="#BBBBBB">
                   <td align="center"></td>
                   <td  colspan="8"  align="right"> Total marker qty.</td>
                   <td align="center"><? echo $total_marker_qty;  ?></td>
                   <td align="center"></td>
                   <td align="center"></td>
                   <td align="center"></td>
                   <td align="center"></td>
                   <td align="center"></td>
                   <td align="center"></td>
                </tr>
			</tbody>
		</table>
		<br>
		<br>
		<table align="left" cellspacing="0" width="400" border="1" rules="all" class="rpt_table" >
			<thead>
				<tr>
					<th colspan="3" align="left">Size Wise Summary</th>
				</tr>
				<tr>
					<th>SL No.</th>
					<th>Size</th>
					<th>Size Ratio</th>
					<th>Cut Qty.</th>
				</tr>
			</thead>
			<tbody>
				<?
				$kk=1;
				foreach($size_wise_qnty_arr as $key=>$vals)
				{
					?>
					<tr>
						<td align="center"><? echo $kk++;?></td>

						<td align="center"><? echo $size_arr[$key];?></td>
						<td align="center"><? echo $size_ratio_arr[$key]["size_ratio"]; ?></td>
						<td align="center"><? echo $vals['qty'];?></td>
					</tr>

					<?
					$size_wise_tot_qtyr+=$vals['qty'];
					$size_wise_tot_ratio+=$size_ratio_arr[$key]["size_ratio"];
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" align="right">Total= </td>
					<td align="center"><? echo $size_wise_tot_ratio ;?></td>
					<td align="center"><? echo $size_wise_tot_qtyr ;?></td>
				</tr>
			</tfoot>

		</table>
        <br>
		<? echo signature_table(9, $data[0], "900px"); ?>
		</div>
	</div>
	<?
	exit();
}
if($action=="cut_lay_bundle_list_print2")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name" );

	$company_library=array(); $company_short_arr=array();
	$comapny_data=sql_select("select id, company_short_name, company_name from lib_company");
	$cur_page=1;
	foreach($comapny_data as $comR)
	{
		$company_library[$comR[csf('id')]]=$comR[csf('company_name')];
		$company_short_arr[$comR[csf('id')]]=$comR[csf('company_short_name')];
	}

	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$working_comp_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$working_location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$working_floor_library=return_library_array( "select id, floor_name from lib_prod_floor", "id", "floor_name"  );
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );


	$sql="select a.cut_num_prefix_no, a.table_no,a.cutting_no, a.job_no, a.entry_date,a.marker_length, a.marker_width, a.fabric_width, a.gsm, a.batch_id as batch_no,a.location_id,a.floor_id,a.working_company_id,a.remarks, b.order_id, b.color_id, b.gmt_item_id, b.plies, b.marker_qty, b.order_qty, b.order_ids, b.batch_id, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.id='$data[1]' and a.company_id='$data[0]' and b.id='$data[2]' ";

	//  echo $sql;die;

	$dataArray=sql_select($sql);
	$sql_buyer=sql_select("select buyer_name,style_ref_no as ref,style_description as des from wo_po_details_master where job_no='".$dataArray[0][csf('job_no')]."' and company_name=$data[0]");
	$style_ref=$sql_buyer[0][csf('ref')];
	$style_desc=$sql_buyer[0][csf('des')];
	$cut_no_prifix=$dataArray[0][csf('cut_num_prefix_no')];
	$cut_no=$dataArray[0][csf('cutting_no')];
	$order_cut_no=$dataArray[0][csf('order_cut_no')];
	$remarks=$dataArray[0][csf('remarks')];
	$batch_no=$dataArray[0][csf('batch_no')];
	$working_company=$dataArray[0][csf('working_company_id')];


	//echo "<pre>";print_r($data_array);die;

	$po_arr=return_library_array( "select id, po_number from wo_po_break_down where id in(".$dataArray[0][csf('order_ids')].")","id","po_number" );
	//$intref_arr=( "select job_no_mst, grouping from wo_po_break_down ", 'job_no_mst', 'grouping' );
	$int_ref="select job_no_mst, grouping from wo_po_break_down where job_no_mst in('".$dataArray[0][csf('job_no')]."') order by grouping ";
	$int_ref_Array=sql_select($int_ref);
	$int_ref_no="";
	foreach($int_ref_Array as $row)
	{
		if($int_ref_no!="")
		{
			$int_ref_no .=",";
		}
      $int_ref_no .=$row[csf('grouping')];
	}
    //echo	$int_ref_no;
	//echo "<pre>";print_r($batch_no);die;


 ?>
 <div style="width:1000px; " align="center" >
    <table width="990" cellspacing="0" align="center">
         <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$working_company]; ?></strong></td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u>Lay and Bundle Information</u></strong></td>
        </tr>
         <tr>
        	<td width="120"><strong>Cut No:</strong></td><td width="160"><? echo $cut_no; ?></td>
            <td width="120"><strong>Table No :</strong></td> <td width="160"><?  echo $table_no_library[$dataArray[0][csf('table_no')]]; ?></td>
            <td width="120"><strong>Job No :</strong></td> <td width="160"><? echo $dataArray[0][csf('job_no')]; ?></td>
        </tr>
         <tr>
			<td><strong>Buyer:</strong></td> <td width="160"><? echo $buyer_library[$sql_buyer[0][csf('buyer_name')]]; ?></td>
            <td><strong>Batch No:</strong></td> <td width="160"><? echo $batch_no; ?></td>
            <td><strong>Internal Ref:</strong></td> <td width="160"><?php echo $int_ref_no;?></td>
		<!-- <? echo  $dataArray[0][csf('job_no')]."4444";?> -->
        </tr>
        <tr>
			 <td><strong>Gmt Item:</strong></td> <td width="160"><? echo $garments_item[$dataArray[0][csf('gmt_item_id')]]; ?></td>
             <td><strong>Color :</strong></td><td width="160"><? echo$color_library[$dataArray[0][csf('color_id')]]; ?></td>
             <td><strong>Marker Length :</strong></td><td width="160"><? echo $dataArray[0][csf('marker_length')]; ?></td>
        </tr>
        <tr>
             <td><strong>Marker Width :</strong></td><td width="160"><? echo $dataArray[0][csf('marker_width')]; ?></td>
            <td><strong>Fabric Width:</strong></td><td width="160"><? echo $dataArray[0][csf('fabric_width')]; ?></td>
              <td><strong>Gsm:</strong></td> <td width="160"><? echo $dataArray[0][csf('gsm')]; ?></td>
        </tr>
        <tr>
             <td><strong>Order Cut No:</strong></td> <td width="160"><? echo $order_cut_no; ?></td>
             <td><strong>Plies:</strong></td> <td width="160"><? echo $dataArray[0][csf('plies')]; ?></td>
             <td><strong>Cut Date:</strong></td><td width="160"><? echo $dataArray[0][csf('entry_date')]; ?></td>
        </tr>
        <tr>
       		 <td><strong>Style Ref:</strong></td> <td width="160"><? echo $style_ref; ?></td>
             <td><strong>Style Desc.:</strong></td> <td width="160"><? echo $style_desc; ?></td>
             <td align="left" colspan="2" id="barcode_img_id"></td>
        </tr>
        <tr>
       		 <td><strong>Working Company:</strong></td> <td width="160"><? echo $working_comp_library[$dataArray[0][csf('working_company_id')]]; ?></td>
             <td><strong>Working Location:</strong></td> <td width="160"><? echo $working_location_library[$dataArray[0][csf('location_id')]]; ?></td>


             <td><strong>Working Floor:</strong></td> <td width="160"><? echo $working_floor_library[$dataArray[0][csf('floor_id')]]; ?></td>


        </tr>
        <tr>
		    <td><strong>Remarks:</strong></td><td width="100"><? echo $remarks;?></td>


           <td><strong>Cutting Part:</strong></td> <td  colspan="5"><? echo $data[5]; ?></td>
		   <td><strong>Page:</strong></td> <td width="120"><?echo $cur_page ?></td>


	</tr>

 </table>
    <br>
     <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

			function generateBarcode( valuess ){

					var value = valuess;
					var btype = 'code39';
					var renderer ='bmp';
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
					 value = {code:value, rect: false};
					$("#barcode_img_id").show().barcode(value, btype, settings);
				}
			   generateBarcode('<? echo $dataArray[0][csf('cutting_no')]; ?>');
	 </script>

		<!-- Size Wise Summary Start from here  -->
		<table align="left" cellspacing="0" width="400" border="1" rules="all" class="rpt_table" >
			<thead>
				<tr>
					<th colspan="3" align="left">Size Wise Summary</th>
				</tr>
				<tr>
					<th>SL No.</th>
					<th>Size</th>
					<th>Size Ratio</th>
					<th>Cut Qty.</th>
				</tr>
			</thead>
			<tbody>
				<?
				$size_data_one=sql_select("select a.id,a.size_id,a.bundle_sequence,a.marker_qty,a.size_ratio from ppl_cut_lay_size_dtls a where a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.bundle_sequence ASC");
				$size_ratio_arr=array();
				foreach ($size_data_one as $val)
				{
					$size_ratio_arr[$val[csf("size_id")]]["size_ratio"]=$val[csf("size_ratio")];
				}

				foreach($size_data_one as $size_val)
				{
					$total_marker_qty_size=0;
					$bundle_data_one=sql_select("SELECT a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,a.barcode_no,a.update_flag from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC");
					foreach($bundle_data_one as $row)
					{
						$total_marker_qty_size=$row[csf('size_qty')];
						$size_wise_qnty_arr_one[$row[csf('size_id')]]['qty']+=$total_marker_qty_size;
						$size_wise_qnty_arr_one[$row[csf('size_id')]]['ratio']=$size_val[csf('size_id')];
					}
				}

				$kk=1;
				foreach($size_wise_qnty_arr_one as $key=>$vals)
				{
					?>
					<tr>
						<td align="center"><? echo $kk++;?></td>

						<td align="center"><? echo $size_arr[$key];?></td>
						<td align="center"><? echo $size_ratio_arr[$key]["size_ratio"]; ?></td>
						<td align="center"><? echo $vals['qty'];?></td>
					</tr>

					<?
					$size_wise_tot_qtyr+=$vals['qty'];
					$size_wise_tot_ratio+=$size_ratio_arr[$key]["size_ratio"];
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" align="right">Total= </td>
					<td align="center"><? echo $size_wise_tot_ratio ;?></td>
					<td align="center"><? echo $size_wise_tot_qtyr ;?></td>
				</tr>
			</tfoot>

		</table>
        <br>
		<br clear="all">
		<br clear="all">

	 <!-- Main Table -->
	<div style="width:1200px;">
    	<table align="center" cellspacing="0" width="1180" border="1" rules="all" class="rpt_table" >
              <thead bgcolor="#dddddd" align="center">
					<th></th>
					<th colspan="9"></th>
					<th>Bundle</th>
					<th colspan="2">RMG Number</th>
					<th colspan="3">QC</th>
					<th></th>
              </thead>
              <thead bgcolor="#dddddd" align="center">
                      <th width="40">SL</th>
                      <th width="100">Cut No</th>
                       <th width="90">Order No</th>
                      <!--<th>Country Name</th> -->
                      <th width="70">Pattern No</th>
                      <th width="60">Roll No</th>
                      <th width="80">Batch No</th>
                      <th width="80">Bundle No</th>
                      <th width="80">Barcode</th>
                      <th width="70">Quantity</th>
                      <th width="70">From</th>
                      <th width="70">To</th>
                      <th width="80">Size</th>
                      <th width="40">REJ</th>
                      <th width="40">REP</th>
                      <th width="150">Remarks</th>
                </thead>
                <tbody>
                <?
					 $batchNo_arr=return_library_array( "select a.id, a.batch_no from pro_roll_details a, ppl_cut_lay_bundle b where a.id=b.roll_id and a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.entry_form=509","id","batch_no" );

					 if($data[4]==0) $country_cond=""; else $country_cond=" and a.country_id='".$data[4]."'";
                     $size_data=sql_select("select a.id,a.size_id,a.bundle_sequence,a.marker_qty,a.size_ratio from ppl_cut_lay_size_dtls a where a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.bundle_sequence ASC");
					//  echo "select a.id,a.size_id,a.bundle_sequence,a.marker_qty,a.size_ratio from ppl_cut_lay_size_dtls a where a.mst_id='$data[1]' and a.dtls_id='$data[2]'  order by a.bundle_sequence ASC";
					 $size_ratio_arr=array();
					 foreach ($size_data as $val) {
						$size_ratio_arr[$val[csf("size_id")]]["size_ratio"]=$val[csf("size_ratio")];
					 }

                     $j=1;
                     foreach($size_data as $size_val)
                     {
						$total_marker_qty_size=0;
                       	$bundle_data=sql_select("SELECT a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no,a.barcode_no,a.update_flag from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC");
						//echo "select a.id,a.bundle_no as bundle_no,a.bundle_num_prefix_no,a.size_id,a.number_start,a.number_end,a.size_qty, a.country_id, a.roll_no, a.roll_id, a.pattern_no, a.is_excess, a.order_id,a.bundle_num_prefix_no from ppl_cut_lay_bundle a where a.mst_id='$data[1]' and a.dtls_id='$data[2]' and a.size_id=".$size_val[csf('size_id')]." $country_cond order by a.id ASC";
                        foreach($bundle_data as $row)
                        {
                        	$bundleNo = $row[csf("bundle_no")];
                        	$update_flag = $row[csf("update_flag")];
                        	if ($update_flag==1) {
                        		 $remarks="Grading";
                        	}else{
                        		 $remarks="";
                        	}

							$tmpBundleArr = explode('-', $bundleNo);
							$bundleNoNumber = '';

							for ($i=3; $i < count($tmpBundleArr); $i++) {
								$bundleNoNumber .= $tmpBundleArr[$i] . '-';
							}
							$bundleNoNumber = rtrim($bundleNoNumber, '-');
               	 			?>
                           <tr>
                               <td align="center"><? echo $j;  ?></td>
                               <td align="center"><? echo $cut_no; ?></td>
                                <td style="word-wrap:break-word"><? echo $po_arr[$row[csf('order_id')]]; ?></td>
                               <!--<td style="word-wrap:break-word"><? echo $country_arr[$row[csf('country_id')]]; ?></td> -->
                               <td align="center"><? echo $row[csf('pattern_no')]; ?></td>
                               <td align="center"><? echo $row[csf('roll_no')]; ?></td>
                               <td style="word-wrap:break-word"><? echo $batchNo_arr[$row[csf('roll_id')]]; ?></td>
                               <td align="center"><? echo $bundleNoNumber; ?></td>
                               <td align="center"><? echo $row[csf('barcode_no')];?></td>
                               <td align="center"><? echo $row[csf('size_qty')];  ?></td>
                               <td align="center"><? echo $row[csf('number_start')];  ?></td>
                               <td align="center"><? echo $row[csf('number_end')];  ?></td>
                               <td align="center"><? echo $size_arr[$row[csf('size_id')]];  ?></td>
                               <td align="center"></td>
                               <td align="center"></td>
                               <td align="center"><? echo $remarks; ?></td>
                          </tr>
               	 			<?
                           $j++;
						   $total_marker_qty_size+=$row[csf('size_qty')];
						   $total_marker_qty+=$row[csf('size_qty')];
                         }
                       //  $total_marker_qty+=$size_val[csf('marker_qty')];
                         $size_wise_qnty_arr[$row[csf('size_id')]]['qty']+=$total_marker_qty_size;
                         $size_wise_qnty_arr[$row[csf('size_id')]]['ratio']=$size_val[csf('size_id')];
                		?>
                        <tr bgcolor="#eeeeee">
                           <td align="center"></td>
                           <td  colspan="7" align="right"><? echo $size_arr[$row[csf('size_id')]]; ?> Size Total</td>
                           <td align="center"><? echo $total_marker_qty_size;  ?></td>
                           <td align="center"></td>
                           <td align="center"></td>
                           <td align="center"></td>
                           <td align="center"></td>
                           <td align="center"></td>
                           <td align="center"></td>
                        </tr>
                <?
                     }

                ?>
               <tr bgcolor="#BBBBBB">
                   <td align="center"></td>
                   <td  colspan="7"  align="right"> Total marker qty.</td>
                   <td align="center"><? echo $total_marker_qty;  ?></td>
                   <td align="center"></td>
                   <td align="center"></td>
                   <td align="center"></td>
                   <td align="center"></td>
                   <td align="center"></td>
                   <td align="center"></td>
                </tr>
			</tbody>
		</table>
		<br>
		<br>
	  					<!-- Size Wise Summary Start from here
		<table align="left" cellspacing="0" width="400" border="1" rules="all" class="rpt_table" >
			<thead>
				<tr>
					<th colspan="3" align="left">Size Wise Summary</th>
				</tr>
				<tr>
					<th>SL No.</th>
					<th>Size</th>
					<th>Size Ratio</th>
					<th>Cut Qty.</th>
				</tr>
			</thead>
			<tbody>
				<?
				$kk=1;
				foreach($size_wise_qnty_arr as $key=>$vals)
				{
					?>
					<tr>
						<td align="center"><? echo $kk++;?></td>

						<td align="center"><? echo $size_arr[$key];?></td>
						<td align="center"><? echo $size_ratio_arr[$key]["size_ratio"]; ?></td>
						<td align="center"><? echo $vals['qty'];?></td>
					</tr>

					<?
					$size_wise_tot_qtyr+=$vals['qty'];
					$size_wise_tot_ratio+=$size_ratio_arr[$key]["size_ratio"];
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" align="right">Total= </td>
					<td align="center"><? echo $size_wise_tot_ratio ;?></td>
					<td align="center"><? echo $size_wise_tot_qtyr ;?></td>
				</tr>
			</tfoot>

		</table>
        <br> -->
		<? echo signature_table(9, $data[0], "900px"); ?>
		</div>
	</div>
	<?
	exit();
}

if($action=="job_search_popup")
{
  	echo load_html_head_contents("Batch Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);

?>
	<script>
		function js_set_order(strCon )
		{
		document.getElementById('hidden_job_no').value=strCon;
		parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%; overflow-y:hidden;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="1020" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th width="150">Company name</th>
                    <th width="150">Buyer name</th>
                    <th width="60">Job No</th>
                    <th width="100">Style Ref.</th>
                    <th width="100">Order No</th>
                     <th width="100">File No</th>
                    <th width="100">Internal Ref. No</th>
                    <th width="220">Date Range</th>
                    <th width=""><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton"  /></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                          <?
                               echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", $cbo_company_id, "",1);
                         ?>
                    </td>
                    <td align="center" width="150">
                             <?
							   $sql="select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$cbo_company_id order by a.buyer_name";
							echo create_drop_down( "cbo_buyer_name", 140,$sql,"id,buyer_name", 1, "-- Select --", 0, "", 0,"5,6,7","","","" );
                            ?>
                            <input type="hidden" id="hidden_job_qty" name="hidden_job_qty" />
                            <input type="hidden" id="hidden_sip_date" name="hidden_sip_date" />
                            <input type="hidden" id="hidden_prifix" name="hidden_prifix" />
                            <input type="hidden" id="hidden_job_no" name="hidden_job_no" />
                    </td>
                    <td width="60">
                          <input style="width:50px;" type="text"  class="text_boxes"   name="txt_job_prifix" id="txt_job_prifix"  />
                    </td>
                    <td width="100">
                          <input style="width:90px;" type="text"  class="text_boxes"   name="txt_style_no" id="txt_style_no"  />
                    </td>
                    <td width="100">
                          <input style="width:90px;" type="text"  class="text_boxes"   name="txt_po_no" id="txt_po_no"  />
                    </td>
                    <td width="100">
                          <input style="width:80px;" type="text"  class="text_boxes"   name="txt_file_no" id="txt_file_no"  />
                    </td>
                    <td width="100">
                          <input style="width:80px;" type="text"  class="text_boxes"   name="txt_internal_ref" id="txt_internal_ref"  />
                    </td>
                    <td align="center" width="220">
                           <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                           <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td>
                    <td align="center">
                         <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_po_no').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_style_no').value, 'create_job_search_list_view', 'search_div', 'cut_and_lay_entry_variable_wise_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                    </td>
            </tr>
        		<tr>
            	<td align="center" height="40" valign="middle" colspan="8">
					<? echo load_month_buttons(1);  ?>
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
}
if($action=="create_job_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];
	$buyer = $ex_data[1];
	$from_date = $ex_data[2];
	$to_date = $ex_data[3];
	$job_prifix= $ex_data[4];
	$job_year = $ex_data[5];
	$po_no = $ex_data[6];
	$file_no = $ex_data[7];
	$internal_reff = $ex_data[8];
	$style_reff = $ex_data[9];


	$job_cond="";

	if(str_replace("'","",$company)=="") $conpany_cond=""; else $conpany_cond="and b.company_name=".str_replace("'","",$company)."";
	if(str_replace("'","",$buyer)==0) $buyer_cond=""; else $buyer_cond="and b.buyer_name=".str_replace("'","",$buyer)."";
    if($db_type==2) $year_cond=" and extract(year from b.insert_date)=$job_year";
    if($db_type==0) $year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=$job_year";
	if(str_replace("'","",$job_prifix)!="")  $job_cond="and b.job_no_prefix_num=".str_replace("'","",$job_prifix)."  $year_cond";
	if(str_replace("'","",$po_no)!="")  $order_cond="and a.po_number like '%".str_replace("'","",$po_no)."%' "; else $order_cond="";

	if(str_replace("'","",$file_no)!="")  $file_cond="and a.file_no like '%".str_replace("'","",$file_no)."%' "; else $file_cond="";

	if(str_replace("'","",$style_reff)!="")  $style_cond="and b.style_ref_no like '%".str_replace("'","",$style_reff)."%' "; else $style_cond="";
	if(str_replace("'","",$internal_reff)!="")  $internal_reff_cond=" and a.grouping like '%".str_replace("'","",$internal_reff)."%' "; else $internal_reff_cond="";

	if($db_type==0)
	{
		if( $from_date!="" && $to_date!="" ) $sql_cond = " and a.pub_shipment_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
	$sql_order="SELECT b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,SUBSTRING_INDEX(b.insert_date, '-', 1) as year from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond $file_cond $internal_reff_cond $style_cond and a.shiping_status<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.buyer_name,b.job_no,a.po_number ";
	}

	if($db_type==2)
	{
	 if( str_replace("'","",$from_date)!="" && str_replace("'","",$to_date)!="" )
	  {
		  $sql_cond = " and a.pub_shipment_date  between '".date("j-M-Y",strtotime($from_date))."' and '".date("j-M-Y",strtotime($to_date))."'";
	  }

    $yes_no_query="SELECT b.company_name,b.variable_list,b.finish_rate_come_from from variable_settings_production b WHERE b.variable_list=80  $conpany_cond ";
    // echo $yes_no_query;die();
	$yes_no=sql_select($yes_no_query);
	$yes_no_arr=array();
	foreach($yes_no as $val)
	{
          $finish_rate_come_from=$val[csf('finish_rate_come_from')];
	}
	//  echo $finish_rate_come_from;die();
    if($finish_rate_come_from !=1)
	{
		// echo "1";
		$sql_order="SELECT b.job_no,b.buyer_name,a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num as job_prefix,extract(year from b.insert_date) as year,a.file_no,a.grouping from wo_po_details_master b,wo_po_break_down a where a.job_no_mst=b.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond $file_cond $internal_reff_cond $style_cond and a.shiping_status<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.job_no,b.buyer_name, a.po_number,a.pub_shipment_date,b.style_ref_no,b.job_no_prefix_num, b.insert_date,a.file_no,a.grouping order by  job_no_prefix_num";

	}
    else
	{
    	// echo "abc";
		$sql_order="SELECT c.job_no,c.buyer_name,c.style_ref_no,a.po_number,a.pub_shipment_date,b.job_no_prefix_num as job_prefix,extract(year from b.insert_date) as year,a.file_no,a.grouping from wo_po_details_master b,wo_po_break_down a,style_wise_body_part_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and b.job_no=c.job_no $buyer_cond  $sql_cond $conpany_cond $job_cond $order_cond $file_cond $internal_reff_cond $style_cond and a.shiping_status<>3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by  job_no_prefix_num";
	}
	}
	//   echo $sql_order;die();
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$arr=array (3=>$buyer_arr);
	echo create_list_view("list_view", "Job NO,Year,Style Ref,Buyer Name,File No,Internal Ref. No, Order No,Shipment Date","60,60,150,150,100,100,150,100","1000","270",0, $sql_order , "js_set_order", "job_no,buyer_name,year,grouping", "", 1, "0,0,0,buyer_name,0,0,0,0,0", $arr, "job_prefix,year,style_ref_no,buyer_name,file_no,grouping,po_number,pub_shipment_date", "","setFilterGrid('list_view',-1)") ;

}
//master data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$gmtItems = '';
		for($i=1; $i<=$row_num; $i++)
		{
			$tmpItem = "cbogmtsitem_$i";
			$gmtItems .= str_replace("'", '', $$tmpItem) . ',';
		}

		$gmtItems = rtrim($gmtItems, ',');

		// echo "10**$gmtItems";die;

		$prev_cut_no_arr=array();
		$dataArrayMst=sql_select("select a.cutting_no, b.color_id, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.company_id=".$cbo_company_name." and a.location_id=".$cbo_location_name." and a.floor_id=".$cbo_floor_name." and b.gmt_item_id in($gmtItems) and a.entry_form=509 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.job_no=$txt_job_no");

		foreach($dataArrayMst as $row)
		{
			$prev_cut_no_arr[$row[csf('color_id')]][$row[csf('order_cut_no')]]=$row[csf('cutting_no')];
		}

		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$sql_table=return_field_value("id","lib_cutting_table","company_id =$cbo_company_name AND location_id =$cbo_location_name AND floor_id =$cbo_floor_name AND table_no = $txt_table_no");
		if($sql_table!="")
		{
		   $tbl_id=$sql_table;
		}
		else
		{
			$tbl_id=return_next_id("id", "lib_cutting_table", 1);
			$field_array_table="id,table_no,company_id,working_company_id,location_id,floor_id,inserted_by,insert_date,status_active,is_deleted";
			$data_array_table="(".$tbl_id.",".$txt_table_no.",".$cbo_company_name.",".$cbo_working_company_name.",".$cbo_location_name.",".$cbo_floor_name.",'".$user_id."','".$pc_date_time."',1,0)";
			//$rID=sql_insert("lib_cutting_table",$field_array_table,$data_array_table,0);
		}

		$job_prifix=return_field_value("job_no_prefix_num","wo_po_details_master","job_no=$txt_job_no");

		$new_sys_number = explode("*", return_next_id_by_sequence("", "ppl_cut_lay_mst",$con,1,$cbo_company_name,'',0,date("Y",time()),0,0,0,0,0 ));

        $cut_no_prifix[]=$new_sys_number[2];

		$comp_prefix=return_field_value("company_short_name","lib_company", "id=$cbo_company_name");
		$cut_no=str_pad((int) $cut_no_prifix[0],6,"0",STR_PAD_LEFT);
		$year_id=date('Y',time());
		if (strlen($year_id)==4) $year_id=substr($year_id,2,2);
		$new_cutting_number=str_replace("--", "-",$new_sys_number[1]).$cut_no;
		$new_cutting_prifix=str_replace("--", "-",$new_sys_number[1]);
		//$id=return_next_id("id", "ppl_cut_lay_mst", 1);
		$id= return_next_id_by_sequence(  "ppl_cut_lay_mst_seq",  "ppl_cut_lay_mst", $con );

		$field_array="id,entry_form,cut_num_prefix,cut_num_prefix_no,cutting_no,table_no,table_entry_id,job_no,batch_id,company_id,working_company_id,location_id,floor_id,entry_date,start_time,end_date,end_time,remarks,shipment_part,internal_ref,wastage,marker_length,marker_width,fabric_width,gsm,width_dia,cad_marker_cons,marker_type,inserted_by,insert_date,status_active,is_deleted";
		$start_time=str_replace("'","",$txt_in_time_hours).":".str_replace("'","",$txt_in_time_minuties);
		$end_time=str_replace("'","",$txt_out_time_hours).":".str_replace("'","",$txt_out_time_minuties);
		$data_array="(".$id.",509,'".$new_cutting_prifix."',".$cut_no_prifix[0].",'".$new_cutting_number."',".$tbl_id.",".$table_entry_id.",".$txt_job_no.",".$txt_batch_no.",".$cbo_company_name.",".$cbo_working_company_name.",".$cbo_location_name.",".$cbo_floor_name.",".$txt_entry_date.",'".$start_time."',".$txt_end_date.",'".$end_time."',".$txt_remark.",".$txt_shipment_part.",".$txt_internal_ref.",".$txt_wastage.",".$txt_marker_length.",".$txt_marker_width.",".$txt_fabric_width.",".$txt_gsm.",".$cbo_width_dia.",".$txt_marker_cons.",".$cbo_marker_type.",'".$user_id."','".$pc_date_time."',1,0)";

		$detls_id= return_next_id_by_sequence(  "ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
		$field_array1="id, mst_id,order_ids,color_type_id,order_cut_no,color_id,contrust_color_id,batch_id,gmt_item_id,plies,order_qty,roll_data,inserted_by,insert_date,status_active,is_deleted";

		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_ids, entry_form, qnty, roll_id, roll_no, plies, batch_no, shade, inserted_by, insert_date";
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$add_comma=0;

		$duplicateMsg=''; $duplicateStatus=true;
		for($i=1; $i<=$row_num; $i++)
		{
			$cbo_order_id="poId_".$i;
			$txt_ship_date="txtshipdate_".$i;
			$cbocolor="cbocolor_".$i;
			$cbocontrastcolor="cbocontrastcolor_".$i;
			$cbo_gmt_id="cbogmtsitem_".$i;
			$order_qty="txtorderqty_".$i;
			$txt_plics="txtplics_".$i;
			$update_details_id="updateDetails_".$i;
			$order_cut_no="orderCutNo_".$i;
			$rollData="rollData_".$i;
			$cbobatch="cbobatch_".$i;
			$cbobatch="cbobatch_".$i;
			$cboColorType="cboColorType_".$i;



			$prev_cut_no=$prev_cut_no_arr[str_replace("'",'',$$cbocolor)][str_replace("'",'',$$order_cut_no)];
			//echo "10**".$prev_cut_no;die;
			if(str_replace("'",'',$$order_cut_no)!=""  && $prev_cut_no!="")
			{
				$duplicateStatus=false;
				$duplicateMsg.="Cutting No: ".$prev_cut_no." Found Against Order Cut No-".str_replace("'",'',$$order_cut_no);
			}

			$save_string=explode("**",str_replace("'",'',$$rollData)); $response_data='';
			for($x=0;$x<count($save_string);$x++)
			{
				$roll_dtls=explode("=",$save_string[$x]);
				$barcode_no=$roll_dtls[0];
				$roll_no=$roll_dtls[1];
				$roll_id=$roll_dtls[2];
				$roll_qnty=$roll_dtls[3];
				$plies=$roll_dtls[4];
				$batchNo=$roll_dtls[5];
				$shade=$roll_dtls[6];

				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				if(str_replace("'",'',$$roll_maintained)!=1) $roll_id=$id_roll;

				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$barcode_no.",".$id.",".$detls_id.",".$$cbo_order_id.",509,'".$roll_qnty."','".$roll_id."','".$roll_no."','".$plies."','".$batchNo."','".$shade."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$response_data.=$barcode_no."=".$roll_no."=".$roll_id."=".$roll_qnty."=".$plies."=".$batchNo."=".$shade."**";
				//$id_roll = $id_roll+1;
			}

			$response_data=substr($response_data,0,-2);

			if ($add_comma!=0) { $data_array1 .=","; $detls_id_array .="_"; }

			$data_array1.="(".$detls_id.",".$id.",".$$cbo_order_id.",".$$cboColorType.",".$$order_cut_no.",".$$cbocolor.",".$$cbocontrastcolor.",".$$cbobatch.",".$$cbo_gmt_id.",".$$txt_plics.",".$$order_qty.",'".$response_data."','".$user_id."','".$pc_date_time."',1,0)";
			$detls_id_array.=$detls_id."#".str_replace("'",'',$$order_cut_no);
			//$detls_id=$detls_id+1;
			$detls_id= return_next_id_by_sequence(  "ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
			$add_comma++;
		}

		if($duplicateStatus==false)
		{
			echo "13**".$duplicateMsg;
			disconnect($con);
			//check_table_status( $_SESSION['menu_id'],0);
			die;
		}

		$rID=true; $rID3=true; $rID4=true;

		if($sql_table=="")
		{ $rID=sql_insert("lib_cutting_table",$field_array_table,$data_array_table,0); }

		$rID2=sql_insert("ppl_cut_lay_mst",$field_array,$data_array,0);

		if($data_array1!="")
		{
			$rID3=sql_insert("ppl_cut_lay_dtls",$field_array1,$data_array1,0);
		}

		if($data_array_roll!="")
		{
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		}

		//echo "10**insert into ppl_cut_lay_mst( $field_array) values".$data_array;die;
		//echo "10**".$rID ."**". $rID2 ."**". $rID3 ."**". $rID4;die;
		//echo "10**".$rID2;die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**".$new_cutting_number."**".str_replace("'","",$tbl_id)."**".$detls_id_array;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4)
			{
				oci_commit($con);
				echo "0**".$id."**".$new_cutting_number."**".str_replace("'","",$tbl_id)."**".$detls_id_array;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		  	$prev_cut_no_arr=array();
			$dataArrayMst=sql_select("select a.cutting_no, b.color_id, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.company_id=".$cbo_company_name." and a.entry_form=509 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.job_no=$txt_job_no and a.id!=$update_id");

			$gmtItems = '';
			for($i=1; $i<=$row_num; $i++)
			{
				$tmpItem = "cbogmtsitem_$i";
				$gmtItems .= str_replace("'", '', $$tmpItem) . ',';
			}

			$gmtItems = rtrim($gmtItems, ',');

			$dataArrayMst=sql_select("select a.cutting_no, b.color_id, b.order_cut_no from ppl_cut_lay_mst a, ppl_cut_lay_dtls b where a.company_id=".$cbo_company_name." and a.location_id=".$cbo_location_name." and a.floor_id=".$cbo_floor_name." and b.gmt_item_id in($gmtItems) and a.entry_form=509 and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.job_no=$txt_job_no");

			foreach($dataArrayMst as $row)
			{
				//$prev_cut_no_arr[$row[csf('color_id')]][1].=$row[csf('order_cut_no')].",";
				//$prev_cut_no_arr[$row[csf('color_id')]][2]=$row[csf('cutting_no')];
				$prev_cut_no_arr[$row[csf('color_id')]][$row[csf('order_cut_no')]]=$row[csf('cutting_no')];
			}

			$con = connect();
			if($db_type==0)	{ mysql_query("BEGIN"); }
			$cutting_qc_no= return_field_value("cutting_qc_no"," pro_gmts_cutting_qc_mst","status_active=1 and is_deleted=0 and cutting_no=".$txt_cutting_no."");
			if($cutting_qc_no!="") { echo "200**".$cutting_qc_no; disconnect($con); die;}
		    $sql_table=return_field_value("id","lib_cutting_table","company_id =$cbo_company_name AND location_id =$cbo_location_name AND floor_id =$cbo_floor_name AND table_no = $txt_table_no");
			$rID=true; $rID2=true; $rID3=true; $rID4=true;
		    if($sql_table!="")
			{
				 $tbl_id=$sql_table;
			}
			else
			{
				$tbl_id=return_next_id("id", "lib_cutting_table", 1);
				$field_array_table="id,table_no,company_id,working_company_id,location_id,floor_id,inserted_by,insert_date,status_active,is_deleted";
				$data_array_table="(".$tbl_id.",".$txt_table_no.",".$cbo_company_name.",".$cbo_working_company_name.",".$cbo_location_name.",".$cbo_floor_name.",'".$user_id."','".$pc_date_time."',1,0)";
				//echo "insert into  ppl_cut_lay_table_no($field_array_table) values".$data_array_table;
				//$rID=sql_insert("lib_cutting_table",$field_array_table,$data_array_table,0);
			}
			//master table update*********************************************************************
			$field_array="table_no*table_entry_id*job_no*batch_id*company_id*working_company_id*location_id*floor_id*entry_date*start_time*end_date*end_time*remarks*shipment_part*internal_ref*wastage*marker_length*marker_width*fabric_width*gsm*width_dia*cad_marker_cons*marker_type*updated_by*update_date";
			$start_time=str_replace("'","",$txt_in_time_hours).":".str_replace("'","",$txt_in_time_minuties);
			$end_time=str_replace("'","",$txt_out_time_hours).":".str_replace("'","",$txt_out_time_minuties);
			$data_array="".$tbl_id."*".$table_entry_id."*".$txt_job_no."*".$txt_batch_no."*".$cbo_company_name."*".$cbo_working_company_name."*".$cbo_location_name."*".$cbo_floor_name."*".$txt_entry_date."*'".$start_time."'*".$txt_end_date."*'".$end_time."'*".$txt_remark."*".$txt_shipment_part."*".$txt_internal_ref."*".$txt_wastage."*".$txt_marker_length."*".$txt_marker_width."*".$txt_fabric_width."*".$txt_gsm."*".$cbo_width_dia."*".$txt_marker_cons."*".$cbo_marker_type."*'".$user_id."'*'".$pc_date_time."'";


		    //$detls_id=return_next_id("id", " ppl_cut_lay_dtls", 1);
		    $detls_id= return_next_id_by_sequence(  "ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
			$field_array1="id, mst_id,order_ids,color_type_id,order_cut_no,ship_date,color_id,contrust_color_id,batch_id,gmt_item_id,plies,order_qty,roll_data,inserted_by,insert_date,status_active,is_deleted";
			$field_array_up="order_ids*color_type_id*order_cut_no*ship_date*color_id*contrust_color_id*batch_id*gmt_item_id*plies*order_qty*roll_data*updated_by*update_date";
			$field_array_roll="id, barcode_no, mst_id, dtls_id, po_ids, entry_form, qnty, roll_id, roll_no, plies, batch_no,shade, inserted_by, insert_date";
			//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
			$add_comma=0;

			$duplicateMsg=''; $duplicateStatus=true;
			//$order_cut_no_arr=return_library_array( "select order_id,max(order_cut_no) as order_cut_no from ppl_cut_lay_dtls group by order_id", "order_id", "order_cut_no"  );
			//$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_id, roll_no, plies, inserted_by, insert_date";
			for($i=1; $i<=$row_num; $i++)
				{
					$cbo_order_id="poId_".$i;
					$orderCutNo="orderCutNo_".$i;
					$txt_ship_date="txtshipdate_".$i;
					$cbocolor="cbocolor_".$i;
					$cbocontrastcolor="cbocontrastcolor_".$i;
					$cbo_gmt_id="cbogmtsitem_".$i;
					$order_qty="txtorderqty_".$i;
					$txt_plics="txtplics_".$i;
					$order_cut_no="orderCutNo_".$i;
					$update_details_id="updateDetails_".$i;
					$rollData="rollData_".$i;
					$cbobatch="cbobatch_".$i;
					$cboColorType="cboColorType_".$i;


					$prev_cut_no=$prev_cut_no_arr[str_replace("'",'',$$cbocolor)][str_replace("'",'',$$order_cut_no)];

					if(str_replace("'",'',$$order_cut_no)!=""  && $prev_cut_no!="" && $prev_cut_no != str_replace("'",'',$txt_cutting_no))
					{
						$duplicateStatus=false;
						$duplicateMsg.="Cutting No: ".$prev_cut_no." Found Against Order Cut No-".str_replace("'",'',$$order_cut_no);
					}

					if(str_replace("'","",$update_id)!="")
					{
						$msster_id=$update_id;
					}
					else
					{
						$msster_id=$id;
					}

					if(str_replace("'",'',$$update_details_id)!="")
					{
						$dtlsId=str_replace("'",'',$$update_details_id);
					}
					else
					{
						$dtlsId=$detls_id;
					}

					$save_string=explode("**",str_replace("'",'',$$rollData)); $response_data='';
					for($x=0;$x<count($save_string);$x++)
					{
						$roll_dtls=explode("=",$save_string[$x]);
						$barcode_no=$roll_dtls[0];
						$roll_no=$roll_dtls[1];
						$roll_id=$roll_dtls[2];
						$roll_qnty=$roll_dtls[3];
						$plies=$roll_dtls[4];
						$batchNo=$roll_dtls[5];
						$shade=$roll_dtls[6];
						$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
						if(str_replace("'",'',$$roll_maintained)!=1) $roll_id=$id_roll;

						if($data_array_roll!="") $data_array_roll.= ",";
						$data_array_roll.="(".$id_roll.",".$barcode_no.",".$msster_id.",".$dtlsId.",".$$cbo_order_id.",509,'".$roll_qnty."','".$roll_id."','".$roll_no."','".$plies."','".$batchNo."','".$shade."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						$response_data.=$barcode_no."=".$roll_no."=".$roll_id."=".$roll_qnty."=".$plies."=".$batchNo."=".$shade."**";
						//$id_roll = $id_roll+1;
					}

					$response_data=substr($response_data,0,-2);

					if(str_replace("'",'',$$update_details_id)!="")
					  {
							$updateID_array[]=str_replace("'",'',$$update_details_id);
							$data_array_up[str_replace("'",'',$$update_details_id)]=explode("_",("".$$cbo_order_id."_".$$cboColorType."_".$$order_cut_no."_".$$txt_ship_date."_".$$cbocolor."_".$$cbocontrastcolor."_".$$cbobatch."_".$$cbo_gmt_id."_".$$txt_plics."_".$$order_qty."_'".$response_data."'_'".$user_id."'_'".$pc_date_time."'_1_0"));
							//$dtlsId=str_replace("'",'',$$update_details_id);

							if ($add_comma!=0) $detls_id_array .="_";
							$detls_id_array.=str_replace("'",'',$$update_details_id)."#".str_replace("'",'',$$order_cut_no);
							$add_comma++;
					  }
					  else
					  {
							if ($data_array1){ $data_array1 .=","; $detls_id_array .="_"; }
							$data_array1.="(".$detls_id.",".$msster_id.",".$$cbo_order_id.",".$$cboColorType.",".$$order_cut_no.",'".$$txt_ship_date."',".$$cbocolor.",".$$cbocontrastcolor.",".$$cbobatch.",".$$cbo_gmt_id.",".$$txt_plics.",".$$order_qty.",'".$response_data."',".$user_id.",'".$pc_date_time."',1,0)";
							$detls_id_array.=$detls_id."#".str_replace("'",'',$$order_cut_no);
							//$dtlsId=$detls_id;
							//$detls_id=$detls_id+1;
							$detls_id= return_next_id_by_sequence(  "ppl_cut_lay_dtls_seq",  "ppl_cut_lay_dtls", $con );
							$add_comma++;
					  }
				 }

				if($duplicateStatus==false)
				{
					echo "13**".$duplicateMsg;
					disconnect($con);
					die;
				}
				//  echo "10**";
			//$detls_id_update.=implode("_",$updateID_array);
			//echo "10**insert into lib_cutting_table( $field_array_table) values".$data_array_table;die;
			if($sql_table=="")
			{
				$rID=sql_insert("lib_cutting_table",$field_array_table,$data_array_table,0);
			}

			$rID1=sql_update("ppl_cut_lay_mst",$field_array,$data_array,"id",$update_id,0);

			$detls_id_update.=$detls_id_array;
			if(count($updateID_array)>0)
				{
					$rID2=execute_query(bulk_update_sql_statement("ppl_cut_lay_dtls","id",$field_array_up,$data_array_up,$updateID_array),1);
				}

				if($data_array1!="")
				{
				   $rID3=sql_insert("ppl_cut_lay_dtls",$field_array1,$data_array1,1);
				}

			$delete_roll=execute_query("delete from pro_roll_details where mst_id=$msster_id and entry_form=509",0);
			if($data_array_roll!="")
			{
				$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			}
			//echo "10**".$rID ."**". $rID1 ."**". $rID2 ."**". $rID3 ."**". $rID4."**".$delete_roll;die;
			// echo "10**insert into pro_roll_details( $field_array_roll) values".$data_array_roll;die;
		 if($db_type==0)
			 {
				if($rID && $rID1 && $rID2 && $rID3 && $rID4 && $delete_roll)
				   {
					mysql_query("COMMIT");
					echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_cutting_no)."**".str_replace("'","",$tbl_id)."**".$detls_id_update;
					}
				else
				   {
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$txt_cutting_no);
				   }
			 }

			else if($db_type==2 || $db_type==1 )
			  {
					if($rID && $rID1 && $rID2 && $rID3 && $rID4 && $delete_roll)
				   {
					oci_commit($con);
					echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_cutting_no)."**".str_replace("'","",$tbl_id)."**".$detls_id_update;
				   }
				else
				   {
					oci_rollback($con);
					echo "10**".str_replace("'","",$txt_cutting_no);
				   }
			  }
			disconnect($con);
			die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$cutting_qc_no= return_field_value("cutting_qc_no"," pro_gmts_cutting_qc_mst","status_active=1 and is_deleted=0 and cutting_no=".$txt_cutting_no."");
		if($cutting_qc_no!="") { echo "200**".$cutting_qc_no; disconnect($con); die;}

		// echo "2**200**".$txt_cutting_no;
		$rID=$rID2=$rID3=$rID4=$rID5=$rID6=1;
		$rID = sql_delete("ppl_cut_lay_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$update_id,1);
		$rID2 = sql_delete("ppl_cut_lay_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'mst_id ',$update_id,1);
		$rID3 = sql_delete("ppl_cut_lay_bundle","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'mst_id ',$update_id,1);
		$rID4 = sql_delete("ppl_cut_lay_size","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'mst_id ',$update_id,1);
		$rID5 = sql_delete("ppl_cut_lay_size_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'mst_id ',$update_id,1);
		$rID6 = sql_delete("ppl_cut_lay_roll_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'mst_id ',$update_id,1);

		// echo "10**$rID ** $rID2 ** $rID3 ** $rID4 ** $rID5 ** $rID6";die();
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6)
		   	{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$update_id);
			}
			else
		   	{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_cutting_no);
		   	}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
		   	{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_cutting_no);
		   	}
		}
		disconnect($con);
		die;
	}
}

if($action=="po_popup")
{
	echo load_html_head_contents("PO Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>

		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});

		var selected_id = new Array(); var selected_name = new Array();

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
			var old=document.getElementById('txt_po_row_id').value;
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

			$('#po_id').val(id);
			$('#po_no').val(name);
		}


		function po_data_with_full_shipment()
		{
			show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value, 'create_cutting_search_list_view', 'search_div', 'cut_and_lay_entry_controller', 'setFilterGrid(\'list_view\',-1)')
		}

    </script>

</head>

<body>
<div align="center">
	<fieldset style="width:400px;margin-left:10px">
    	<input type="hidden" name="po_id" id="po_id" class="text_boxes" value="">
        <input type="hidden" name="po_no" id="po_no" class="text_boxes" value="">
        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="390" class="rpt_table" >
                <thead>
                	<tr>
	                    <th width="40"></th>
	                    <th width="200"><input type="button" class="formbutton" value="With Full Shipment Po" onClick="show_list_view ( '<?php echo $poId; ?>' +'_'+'<?php echo $txt_job; ?>'+'_'+'<?php echo $cbocolor; ?>'+'_'+'<?php echo $gmt_id; ?>'+'_'+'<?php echo $process_row_id; ?>', 'create_list_view_with_full_shipment_po', 'buyer_list_view', 'cut_and_lay_entry_variable_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1)')"></th>
	                    <th>

	                    </th>
	                </tr>
	                <tr>
	                    <th width="40">SL</th>
	                    <th width="200">PO No.</th>
	                    <th>Shipment Date</th>
	                </tr>
                </thead>
            </table>
            <div style="width:390px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table" id="tbl_list_search" >
                <?
                	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$company_id");
                	$projected_po_cond = ($is_projected_po_allow==2) ? " and a.is_confirmed=1" : "";

                    $i=1; $po_row_id=''; $poId=explode(",",$poId);
					$sql="SELECT a.id, a.po_number, a.pub_shipment_date from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.job_no_mst='$txt_job' and b.color_number_id='$cbocolor' and b.item_number_id='$gmt_id' $projected_po_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.shiping_status<>3 group by a.id, a.po_number, a.pub_shipment_date order by a.pub_shipment_date";
					$result=sql_select($sql);
                    foreach($result as $row)
                    {
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if(in_array($row[csf('id')],$poId))
						{
							if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
							<td width="40" align="center"><?php echo "$i"; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
								<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf('po_number')]; ?>"/>
							</td>
							<td width="200"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td align="center"><p><? echo change_date_format($row[csf('pub_shipment_date')]); ?></p></td>
						</tr>
						<?
						$i++;
                    }
                ?>
                    <input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<?php echo $process_row_id; ?>"/>
                </table>
            </div>
             <table width="370" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_all();
</script>
</html>
<?
exit();
}

if($action=="create_list_view_with_full_shipment_po")
{

    $ex_data = explode("_",$data);
	$poId = $ex_data[0];
	$txt_job = $ex_data[1];
	$cbocolor = $ex_data[2];
	$gmt_id = $ex_data[3];
	$process_row_id = $ex_data[4];
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table" id="tbl_list_search" >
    <?
        $i=1; $po_row_id=''; $poId=explode(",",$poId);
		$sql="SELECT a.id, a.po_number, a.pub_shipment_date from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.job_no_mst='$txt_job' and b.color_number_id='$cbocolor' and b.item_number_id='$gmt_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.id, a.po_number, a.pub_shipment_date order by a.pub_shipment_date";
		//echo $sql;
		$result=sql_select($sql);
        foreach($result as $row)
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			if(in_array($row[csf('id')],$poId))
			{
				if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
				<td width="40" align="center"><?php echo "$i"; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
					<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf('po_number')]; ?>"/>
				</td>
				<td width="200"><p><? echo $row[csf('po_number')]; ?></p></td>
                <td align="center"><p><? echo change_date_format($row[csf('pub_shipment_date')]); ?></p></td>
			</tr>
			<?
			$i++;
        }
    ?>
        <input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<?php echo $process_row_id; ?>"/>
    </table>
    <?


}

if($action=="cutting_number_popup")
{
  	echo load_html_head_contents("Cutting Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
?>
	<script>
		function js_set_cutting_value(strCon )
		{

		document.getElementById('update_mst_id').value=strCon;
		parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:100%; overflow-y:hidden;" >


<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="1150" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th width="175"></th>
                    <th width="140">Company name</th>
                    <th width="130">Cutting No</th>
                    <th width="130">Job No</th>
                    <th width="130" style="display:none">Order No</th>
                    <th width="250">Date Range</th>
                    <th width="100" align="left"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
                    <th width="175"></th>
                </tr>
            </thead>
            <tbody>
                  <tr class="general">
                  <td></td>
                        <td>
                             <?
                                   echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name","id,company_name", 0, "-- Select Company --",$company_id, "",1);
                             ?>
                        </td>

                        <td align="center" >
                                <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:120px"  class="text_boxes_numeric"/>
                                <input type="hidden" id="update_mst_id" name="update_mst_id" />
                        </td>
                        <td align="center">
                               <input name="txt_job_search" id="txt_job_search" class="text_boxes_numeric" style="width:120px"  />
                        </td>
                        <td align="center" style="display:none">
                               <input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"  />
                        </td>
                        <td align="center" width="250">
                               <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
                               <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
                        </td>
                        <td align="left">
                               <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value, 'create_cutting_search_list_view', 'search_div', 'cut_and_lay_entry_variable_wise_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                        </td>
                  <td></td>
                 </tr>
        		 <tr>
                    <td align="center" height="40" valign="middle" colspan="8">
                        <? echo load_month_buttons(1);  ?>
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
}

if($action=="create_cutting_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];
    if($db_type==2) { $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year ";}
    if($db_type==0) {$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year ";}

	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and a.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  $year_cond";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	//if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond="and c.po_number like '%".trim($order_no)."%' ";

	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}

	//$sql_order="select a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width, c.po_number, d.order_id,d.color_id,$year FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d where a.id=d.mst_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and c.id=d.order_id and a.entry_form=509 $conpany_cond $cut_cond $job_cond $sql_cond $order_cond order by id";
	$buyer_library = return_library_array('SELECT id, buyer_name FROM lib_buyer','id','buyer_name');
	?>
	<script type="text/javascript">
	  var buyerName = <? echo json_encode($buyer_library); ?>;
	</script>
	<?
	$sql_order="SELECT a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width,c.color_id, c.marker_qty, c.order_cut_no,$year,b.buyer_name,b.style_ref_no FROM ppl_cut_lay_mst a,wo_po_details_master b,ppl_cut_lay_dtls c where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  c.mst_id=a.id and a.job_no=b.job_no and a.entry_form=509 $conpany_cond $cut_cond $job_cond $sql_cond order by id desc";

	$table_no_arr=return_library_array( "select id,table_no from lib_cutting_table",'id','table_no');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

	$arr=array(3=>$table_no_arr,6=>$buyer_library,7=>$color_arr);//,4=>$order_number_arr,5=>$color_arr,Order NO,Color
	echo create_list_view("list_view", "Cut No,Year,Order Cut No,Table No,Job No,Style Ref.,Buyer Name,Color,Marker Qty,Marker Length,Markar Width,Fabric Width,Entry Date","60,50,70,60,90,100,100,100,80,90,90,100,120","1150","270",0, $sql_order , "js_set_cutting_value", "id", "", 1, "0,0,0,table_no,0,0,buyer_name,color_id,0,0,0,0,0,0", $arr, "cut_num_prefix_no,year,order_cut_no,table_no,job_no,style_ref_no,buyer_name,color_id,marker_qty,marker_length,marker_width,fabric_width,entry_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0,0,0,0,0,0,0,3") ;
	exit();
}

if($action=="load_php_mst_form")
{
    $sql_data=sql_select("SELECT b.id as   tbl_id,b.table_no,b.location_id,b.floor_id,a.id,a.job_no,a.company_id,a.working_company_id,a.entry_date,end_date,a.marker_length,a.marker_width,a.fabric_width,a.gsm,a.width_dia,a.cad_marker_cons,a.cutting_no,a.batch_id,a.start_time,a.end_time, a.shipment_part,a.marker_type,c.grouping,wastage, a.remarks
	from ppl_cut_lay_mst a, lib_cutting_table b,wo_po_break_down c
	where a.table_no=b.id and a.job_no=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=".$data." ");

    foreach($sql_data as $val)
	  {
		    $start_time=explode(":",$val[csf("start_time")]);
		    $end_time=explode(":",$val[csf("end_time")]);
			$location_id = $val[csf("location_id")];
			$floor_id = $val[csf("floor_id")];
			echo "document.getElementById('cbo_company_name').value = '".($val[csf("company_id")])."';\n";
			echo "document.getElementById('cbo_working_company_name').value = '".($val[csf("working_company_id")])."';\n";
			echo "load_drop_down( 'requires/cut_and_lay_entry_variable_wise_controller','".$val[csf("working_company_id")]."', 'load_drop_down_location', 'location_td') ;";
			// echo "load_drop_down( 'requires/cut_and_lay_entry_variable_wise_controller','".$val[csf("working_company_id")]."_$location_id"."_$floor_id"."', 'load_drop_down_table', 'table_td') ;";
			echo "document.getElementById('txt_table_no').value = '".($val[csf("table_no")])."';\n";
			echo "$('#txt_entry_date').val('".change_date_format($val[csf("entry_date")])."');\n";
			echo "$('#txt_end_date').val('".change_date_format($val[csf("end_date")])."');\n";
			echo "document.getElementById('txt_marker_length').value  = '".($val[csf("marker_length")])."';\n";
			echo "document.getElementById('txt_marker_width').value  = '".($val[csf("marker_width")])."';\n";
			echo "document.getElementById('txt_fabric_width').value = '".($val[csf("fabric_width")])."';\n";
			echo "document.getElementById('txt_gsm').value  = '".($val[csf("gsm")])."';\n";
			echo "document.getElementById('cbo_marker_type').value  = '".($val[csf("marker_type")])."';\n";
			echo "document.getElementById('cbo_width_dia').value  = '".($val[csf("width_dia")])."';\n";
			echo "document.getElementById('txt_marker_cons').value  = '".($val[csf("cad_marker_cons")])."';\n";
			echo "document.getElementById('cbo_location_name').value  = '".($val[csf("location_id")])."';\n";
			echo "document.getElementById('txt_batch_no').value = '".($val[csf("batch_id")])."';\n";
			echo "document.getElementById('txt_job_no').value = '".($val[csf("job_no")])."';\n";
			echo "document.getElementById('update_tbl_id').value  = '".($val[csf("tbl_id")])."';\n";
			echo "document.getElementById('update_id').value = '".($val[csf("id")])."';\n";
			echo "document.getElementById('txt_cutting_no').value = '".($val[csf("cutting_no")])."';\n";
			echo "document.getElementById('cbo_floor_name').value  = '".($val[csf("floor_id")])."';\n";
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_cut_lay_info',1);\n";
			echo "document.getElementById('txt_in_time_hours').value  = '".($start_time[0])."';\n";
			echo "document.getElementById('txt_in_time_minuties').value = '".($start_time[1])."';\n";
			echo "document.getElementById('txt_out_time_hours').value = '".($end_time[0])."';\n";
			echo "document.getElementById('txt_out_time_minuties').value  = '".($end_time[1])."';\n";
			echo "document.getElementById('cbo_floor_name').value  = '".($val[csf("floor_id")])."';\n";
			echo "document.getElementById('txt_remark').value  = '".$val[csf("remarks")]."';\n";
			echo "document.getElementById('txt_wastage').value  = '".$val[csf("wastage")]."';\n";
			echo "document.getElementById('txt_shipment_part').value  = '".$val[csf("shipment_part")]."';\n";
			echo "document.getElementById('txt_internal_ref').value  = '".$val[csf("grouping")]."';\n";
			if($db_type==0){ $insert_year="SUBSTRING_INDEX(b.insert_date, '-', 1) as year";}
if($db_type==2){ $insert_year="extract(year from b.insert_date) as year";}
			$sql=sql_select("select distinct c.id,c.buyer_name,$insert_year from  wo_po_break_down a,wo_po_details_master b,lib_buyer c where  a.job_no_mst=b.job_no and b.job_no='".$val[csf("job_no")]."' and b.buyer_name=c.id and a.status_active=1");

			foreach($sql as $row)
			   {
				    echo "document.getElementById('txt_buyer_name').value = '".$row[csf("id")]."';\n";
					echo "document.getElementById('txt_job_year').value = '".$row[csf("year")]."';\n";
			   }
	  }
	  exit();
}

if($action=="order_details_list")
{
	// $sql_gmt_arr="select ";
	// $ex_data = explode("_",$data);
	// print_r($ex_data);

	 $tbl_row=0;
	 $sql_dtls=sql_select("select a.id,a.order_ids,a.ship_date,a.color_id,a.color_type_id,a.batch_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,a.lay_balance_qty,b.job_no,b.job_year,b.company_id,b.internal_ref, a.order_cut_no, a.roll_data,a.contrust_color_id from ppl_cut_lay_dtls a, ppl_cut_lay_mst b where b.id=a.mst_id and mst_id=".$data." order by a.id");

    // echo $sql_dtls1="select a.id,a.order_ids,a.ship_date,a.color_id,a.color_type_id,a.batch_id,a.gmt_item_id,a.plies,a.marker_qty,a.order_qty,a.total_lay_qty,a.lay_balance_qty,b.job_no,b.job_year,b.company_id,b.internal_ref, a.order_cut_no, a.roll_data,a.contrust_color_id from ppl_cut_lay_dtls a, ppl_cut_lay_mst b where b.id=a.mst_id and mst_id=".$data." order by a.id";

	$gmt_item_arr=return_library_array( "select gmts_item_id from wo_po_details_master where job_no='".$sql_dtls[0][csf('job_no')]."' and status_active=1",'id','gmts_item_id');
	$gmt_item_id=implode(",",$gmt_item_arr);
	$color_item_arr=return_library_array( "select a.id,a.color_name from lib_color a,wo_po_color_size_breakdown b,wo_po_break_down c  where a.id=b.color_number_id and c.id=b.po_break_down_id and b.job_no_mst='".$sql_dtls[0][csf('job_no')]."' and c.status_active=1 and b.status_active=1 group by a.id,a.color_name","id","color_name");
	// echo $color_sql= "select a.id,a.color_name from lib_color a,wo_po_color_size_breakdown b,wo_po_break_down c  where a.id=b.color_number_id and c.id=b.po_break_down_id and b.job_no_mst='".$sql_dtls[0][csf('job_no')]."' and c.status_active=1 and b.status_active=1 group by a.id,a.color_name";die();
	 $color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
    // $sql_contrast="SELECT a.contrast_color_id from wo_pre_cos_fab_co_color_dtls a, ppl_cut_lay_mst b WHERE a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='".$sql_dtls[0][csf('job_no')]."'";
	// $sql_contrast_total=sql_select($sql_contrast);
	// $contrast_arr=array();
	// foreach($sql_contrast_total as $row)
	// {
	// 	$contrast_arr[$row[csf('contrast_color_id')]]=$row[csf('contrast_color_id')];
	// }
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down where job_no_mst='".$sql_dtls[0][csf('job_no')]."'","id","po_number");

	//   echo '<pre>'; print_r($contrast_arr);  echo'</pre>';
	$contrast_color_arr=return_library_array("SELECT a.id,a.color_name from lib_color a, wo_pre_cos_fab_co_color_dtls b WHERE a.id=b.contrast_color_id and job_no='".$sql_dtls[0][csf('job_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0",'id','color_name');

	// $contrast_color_id=implode(",",$contrast_color_arr);

	$color_type_arr=array();
	$sql="SELECT b.color_type_id from  wo_po_break_down a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c where a.job_no_mst=b.job_no  and b.id=c.pre_cost_fabric_cost_dtls_id and a.id=c.po_break_down_id and b.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id in(".$sql_dtls[0][csf('order_ids')].") and c.cons>0  group by b.color_type_id";
	foreach(sql_select($sql) as $vals)
	{
		$color_type_arr[$vals[csf("color_type_id")]]=$color_type[$vals[csf("color_type_id")]];
	}


	foreach($sql_dtls as $val)
	{
		$sql="select sum(CAST(plan_cut_qnty as INT)) as plan_qty from wo_po_color_size_breakdown where po_break_down_id in(".$val[csf("order_ids")].") and item_number_id=".$val[csf("gmt_item_id")]." and color_number_id=".$val[csf("color_id")]." and status_active=1 group by po_break_down_id,item_number_id,color_number_id ";
		$result=sql_select($sql);
		foreach($result as $row)
		{
			$plan_qty+=$row[csf("plan_qty")];
		}

		$sql_marker="select sum(marker_qty) as mark_qty from ppl_cut_lay_dtls where order_ids='".$val[csf("order_ids")]."' and gmt_item_id=".$val[csf("gmt_item_id")]." and color_id=".$val[csf("color_id")]." and status_active=1";
		$result=sql_select($sql_marker);
		foreach($result as $rows)
		{
			$total_marker_qty=$rows[csf("mark_qty")];
		}
		$lay_balance=$plan_qty-$total_marker_qty;

		$po_no='';
		$po_ids=explode(",",$val[csf('order_ids')]);
		foreach($po_ids as $poId)
		{
			$po_no.=$po_arr[$poId].",";
		}


	     $tbl_row++;
		?>
	   <tr class="" id="tr_<? echo $tbl_row; ?>" style="height:10px;">
       		<td align="center" id="color_<? echo $tbl_row; ?>">
				 <?
					 echo create_drop_down( "cbocolor_".$tbl_row, 100, $color_item_arr,"", 1, "select color", $val[csf('color_id')], "reset_fld(".$tbl_row.")");
				 ?>
			</td>
			<td align="center" id="colorcontrast_<? echo $tbl_row; ?>">
				 <?
				    echo create_drop_down( "cbocontrastcolor_".$tbl_row, 100, $contrast_color_arr,"", 1, "select color", $val[csf('contrust_color_id')], "");

				 ?>
			</td>

			<td align="center" id="garment_<? echo $tbl_row; ?>">
				 <?
					 echo create_drop_down( "cbogmtsitem_".$tbl_row, 120, $garments_item,"", 1, "-- Select Item --", $val[csf('gmt_item_id')], "","",$gmt_item_id);
				 ?>
			</td>
			<td align="center" id="orderId_<? echo $tbl_row; ?>">
            	<input type="text" name="cboPoNo_<? echo $tbl_row; ?>" id="cboPoNo_<? echo $tbl_row; ?>" class="text_boxes" style="width:100px;" placeholder="Double Click to Search" onDblClick="openmypage_po(<? echo $tbl_row; ?>)" value="<? echo chop($po_no,','); ?>" readonly />
                <input type="hidden" name="poId_<? echo $tbl_row; ?>"  id="poId_<? echo $tbl_row; ?>" value="<? echo $val[csf('order_ids')]; ?>" />
			</td>

            <td align="center" id="colorTypeId_<? echo $tbl_row; ?>">
                <?
                echo create_drop_down( "cboColorType_".$tbl_row, 100, $color_type_arr,"", 1, "--Select--",$val[csf('color_type_id')], "",1,0 );
                ?>
            </td>



			<td align="center" id="cutNo_<? echo $tbl_row; ?>">
				<input style="width:60px;" class="text_boxes_numeric" type="text" name="orderCutNo_<? echo $tbl_row; ?>" id="orderCutNo_<? echo $tbl_row; ?>" placeholder="" value="<? echo $val[csf('order_cut_no')]; ?>" onBlur="cut_no_duplication_check(<? echo $tbl_row; ?>);" />
			</td>
			<td align="center" id="batch_<? echo $tbl_row; ?>">
				 <?
					$sql="select a.id, a.batch_no, a.extention_no from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.color_id='".$val[csf('color_id')]."' and b.po_id in(".$val[csf('order_ids')].") and a.entry_form in(0,7,37,66,68) and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no";
					$result=sql_select($sql);
					foreach($result as $row)
					{
						$ext='';
						if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
						$batch_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
					}
					$sql="select a.id, a.batch_no, a.extention_no from pro_batch_create_mst a, inv_item_transfer_dtls b, order_wise_pro_details c where a.id=b.batch_id and b.id=c.dtls_id and c.color_id='".$val[csf('color_id')]."' and c.po_breakdown_id in(".$val[csf('order_ids')].") and b.status_active=1 and b.is_deleted=0 and c.entry_form in(14,15) and c.trans_type=5 group by a.id, a.batch_no, a.extention_no";
					$result=sql_select($sql);
					foreach($result as $row)
					{
						$ext='';
						if($row[csf('extention_no')]>0) {$ext='-'.$row[csf('extention_no')];}
						$batch_array[$row[csf('id')]]=$row[csf('batch_no')].$ext;
					}
					if(count($batch_array)>0)
					{
					 	echo create_drop_down( "cbobatch_".$tbl_row, 100, $batch_array,"", 1, "select Batch",  $val[csf('batch_id')], "");
					}
					else
					{
						echo create_drop_down( "cbobatch_".$tbl_row, 100, $blank_array,"", 1, "select Batch",  $val[csf('batch_id')], "");
					}
				 ?>
			</td>
			<td align="center">
				   <input type="text" name="txtplics_<? echo $tbl_row; ?>"  id="txtplics_<? echo $tbl_row; ?>" class="text_boxes_numeric" style="width:60px" value="<? echo $val[csf('plies')];?>" placeholder="Double Click" onDblClick="openmypage_roll(<? echo $tbl_row; ?>)" readonly/>
				  <input type="hidden" name="updateDetails_<? echo $tbl_row; ?>"  id="updateDetails_<? echo $tbl_row; ?>"  value="<? echo $val[csf('id')]; ?>" />
				  <input type="hidden" name="rollData_<? echo $tbl_row; ?>" id="rollData_<? echo $tbl_row; ?>" class="text_boxes" value="<? echo $val[csf('roll_data')]; ?>" />
			</td>
			<td align="center">
				  <input type="text" name="txtsizeratio_<? echo $tbl_row; ?>" id="txtsizeratio_<? echo $tbl_row; ?>" class="text_boxes_numeric" onDblClick="openmypage_sizeNo(this.id);"  placeholder="Browse" style="width:50px" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).onclick(1)" readonly/>
			</td>
			<td align="center" id="marker_<? echo $tbl_row; ?>">
				  <input type="text" name="txtmarkerqty_<? echo $tbl_row; ?>"  id="txtmarkerqty_<? echo $tbl_row; ?>" class="text_boxes_numeric"  placeholder="Display" style="width:60px" value="<? echo $val[csf('marker_qty')];?>" disabled />
			</td>
			 <td align="center" id="order_<? echo $tbl_row; ?>">
				 <input type="text" name="txtorderqty_<? echo $tbl_row; ?>" id="txtorderqty_<? echo $tbl_row; ?>" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  value="<? echo $plan_qty;?>" disabled/>
			</td>
			 <td align="center">
				 <input type="text" name="txttotallay_<? echo $tbl_row; ?>"  id="txttotallay_<? echo $tbl_row; ?>"class="text_boxes_numeric"  placeholder="Display" style="width:60px" value="<? echo $total_marker_qty;?>" disabled/>
			</td>
			<td align="center">
				 <input type="text" name="txtlaybalanceqty_<? echo $tbl_row; ?>"  id="txtlaybalanceqty_<? echo $tbl_row; ?>" class="text_boxes_numeric"  placeholder="Display" style="width:60px"  value="<? echo $lay_balance;?>" disabled/>
			</td>
			<td width="70">
				 <input type="button" id="increase_<? echo $tbl_row; ?>" name="increase_<? echo $tbl_row; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $tbl_row; ?>)" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).onclick()" />
				<input type="button" id="decrease_<? echo $tbl_row; ?>" name="decrease_<? echo $tbl_row; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tbl_row; ?>);" />
			</td>
	   </tr>
<?
	 }
	 exit();
}

if($action=="cut_lay_entry_report_print")
{
    // extract($_REQUEST);
	$data=explode('*',$data);
	//print_r($data);
	$sql=sql_select("select id,job_no,cut_num_prefix_no,table_no,marker_length,marker_width,fabric_width,gsm,width_dia,cad_marker_cons,batch_id,company_id from ppl_cut_lay_mst where cutting_no='".$data[0]."' ");
	foreach($sql as $val)
		{
			$mst_id=$val[csf('id')];
			$company_id=$val[csf('company_id')];
			$cut_prifix=$val[csf('cut_num_prefix_no')];
			$table_no=$val[csf('table_no')];
			$marker_length=$val[csf('marker_length')];
			$marker_with=$val[csf('marker_width')];
			$fabric_with=$val[csf('fabric_width')];
			$gsm=$val[csf('gsm')];
			$dia_width=$val[csf('width_dia')];
			$txt_batch=$val[csf('batch_id')];
			$cad_marker_cons=$val[csf('cad_marker_cons')];
			$job_no=$val[csf('job_no')];
		}

	$costing_per=return_field_value("costing_per","wo_pre_cost_mst", "job_no='$job_no'");
	if($costing_per==1)
	{
		$costing_per_qty=12;
	}
	else if($costing_per==2)
	{
		$costing_per_qty=1;
	}
	else if($costing_per==3)
	{
		$costing_per_qty=24;
	}
	else if($costing_per==4)
	{
		$costing_per_qty=36;
	}
	else if($costing_per==5)
	{
		$costing_per_qty=48;
	}

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
	$sql_buyer_arr=sql_select("select buyer_name,style_ref_no from  wo_po_details_master where job_no='$data[1]'");
	$sql_order=sql_select("select order_ids,gmt_item_id,order_qty from ppl_cut_lay_dtls where mst_id='$mst_id'");
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  );
	$order_number_arr=return_library_array( "select id, po_number from wo_po_break_down where job_no_mst='$data[1]'",'id','po_number');
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );
	//print_r($sql_order);
	$order_number=""; $order_id='';
	foreach($sql_order as $order_val)
	{
		$item_name=$order_val[csf('gmt_item_id')];
		$order_qty+=$order_val[csf('order_qty')];
		if($order_id!="")
		{
			$order_id.=",".$order_val[csf('order_ids')];
		}
		else
		{
			$order_id=$order_val[csf('order_ids')];
		}
	}
	$order_ids=array_unique(explode(",",$order_id));
	foreach($order_ids as $poId)
	{
		if($order_number!="")
		{
			$order_number.=", ".$order_number_arr[$poId];
		}
		else
		{
			$order_number=$order_number_arr[$poId];
		}
	}

	?>
    <div style="width:1100px; position:relative">
    <div style=" width:500; height:200px; position:absolute; left:300px; top:0; ">
        <table width="500" cellspacing="0" align="center">
            <tr>
                <td  align="center" style="font-size:22px; font-weight:bold;"><strong><? echo $company_library[$company_id]; ?></strong></td>
            </tr>
            <tr>
                <td  align="center" style="font-size:18px; font-weight:bold;"><strong>LAY CHART & CONSUMPTION REPORT</strong></td>
            </tr>
       </table>

    </div>
    <div style=" width:200; height:40px; position:absolute; right:0; top:50px; ">Date:  ......../......../.......... </div>
    <div style=" width:200; height:60px; position:absolute; right:0; top:90px; " id="barcode_img_id"> </div>
    <div style=" top:80px; width:270; height:200px; position:absolute; left:0; ">
	<table border="1"  cellspacing="0"  width="260"class="rpt_table" rules="all">
         <tr>
              <td width="80">Buyer</td><td width="180" align="center"><? echo $buyer_arr[$sql_buyer_arr[0][csf('buyer_name')]]; ?></td>
         </tr>
         <tr>
              <td>Job No</td><td align="center"> <? echo $data[1]; ?></td>
         </tr>
         <tr>
              <td>Style</td><td align="center"> <? echo $sql_buyer_arr[0][csf('style_ref_no')]; ?></td>
         </tr>
         <tr>
              <td>Item Name</td> <td align="center"><? echo $garments_item[$item_name]; ?></td>
        </tr>
         <tr>
              <td>Order No</td><td align="center"><p> <? echo $order_number; ?></p></td>
         </tr>
         <tr>
              <td>Order Qty</td><td align="right"><? echo $order_qty; ?></td>
         </tr>

    </table>
    </div>
	<div  style="width:550; position:absolute; height:30px; top:70px; left:280px">
    	<table>
        	<tr>
            	<td><b>Working Company: </b></td>
                <td width="260"><? echo $company_library[$data[2]]; ?> </td>
                <td><b>Location: </b></td>
                <td><? echo $location_arr[$data[3]]; ?> </td>
            </tr>
        </table>


	 </div>
    <div  style="width:250; position:absolute; height:30px; top:118px; left:280px">
          <table border="1" cellpadding="1" cellspacing="1"   width="220"class="rpt_table" rules="all">
              <tr >
              <td width="170"> CAD Fabric Width/Dia</td><td width="50" align="center" colspan="2"><? echo $fabric_with; ?></td>
             </tr>
          </table>
    </div>


   <div  style="width:250; position:absolute; height:30px; top:160px; left:280px">
      <table border="1" cellpadding="1" cellspacing="1"   width="220"class="rpt_table" rules="all">
          <tr >
          <td width="170">CAD GSM</td><td width="50" align="center" colspan="2"><? echo $gsm; ?></td>
         </tr>
      </table>
    </div>
    <div  style="width:300; position:absolute; height:100px; top:280px; left:280px">
      <table border="1" cellpadding="1" cellspacing="1"   width="300"class="rpt_table" rules="all">
          <tr height="20">
          <td width="80">Table No</td>
          <td width="75" align="center"><? echo $table_no_library[$table_no]; ?></td>
          <td width="75" align="center">Batch No </td>
          <td width="80" align="center">Dia(Tube<br>/Open)</td>
         </tr>
         <tr height="30">
          <td width="80">Cutting No</td>
          <td width="75" align="center"><? echo $comp_name."-".$cut_prifix; ?></td>
          <td width="75" align="center">  <? echo $txt_batch; ?></td>
          <td width="80" align="center"> <? echo $fabric_typee[$dia_width]; ?></td>
         </tr>
      </table>
    </div>

     <div  style="width:200; position:absolute; height:400px; top:164px; left:580px">
       <table border="1" cellpadding="1" cellspacing="1"   width="200"class="rpt_table" rules="all">
          <tr height="30">
          <td width="90">Sperading Operators</td>
          <td width="100" align="center"></td>
         </tr>
         <tr height="30">
          <td width="">Checked by Marker Man</td>
          <td width="" align="center"></td>
         </tr>
           <tr height="30">
          <td width="90">Cutter Man-1</td>
          <td width="100" align="center"></td>
         </tr>
         <tr height="43">
          <td width="">Cutter Man-2</td>
          <td width="" align="center"></td>
         </tr>
          <tr height="30">
          <td width="">Cutter Man-3</td>
          <td width="" align="center"></td>
         </tr>
      </table>
    </div>


    <div style=" width:300; position:absolute; top:175px; right:0px; ">
	<table border="1" cellpadding="1" cellspacing="1"   width="300"class="rpt_table" rules="all">
         <tr height="30">
              <td width="100"><strong>Line Q.I</strong></td><td width="200" align="center" colspan="2"></td>
         </tr>
         <tr height="30">
              <td><strong>Jr. DQ.C</strong></td><td align="center" colspan="2"></td>
         </tr>
         <tr height="30">
              <td><strong>Checked By Q.C</strong></td> <td align="center" colspan="2"></td>
        </tr >
         <tr height="30">
              <td>Start Time</td><td align="center" width="100"></td><td align="center" width="100"><strong>Total Time Taken</strong></td>
         </tr >
         <tr height="30">
              <td>End Time</td><td align="center" width="100"></td><td align="center" width="100"></td>
         </tr>

    </table>
    </div>
    <div style=" width:270; position:absolute; top:250px;  ">
	<div style=" float:left; text-align:center; margin-top:20px; width:80px;"><Strong>STEP LAY DETAILS</Strong></div>
    <div style=" float:right;width:190px;">
         <div style="  width:90px; background-color:#666666; color:white;"><Strong>Step-1</Strong></div>
        <table border="1" cellpadding="1" cellspacing="1"   width="180"class="rpt_table" rules="all">
         <tr height="30">
              <td width="80">CAD Marker Length</td><td width="80" align="center" ><? echo $marker_length;  ?></td>
         </tr>
         <tr height="30"  >
              <td>CAD Marker Width</td><td align="center" ><? echo $marker_with;  ?></td>
         </tr>


    </table>
    </div>
    </div>
 </div>

     <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess ){

			var value = valuess;//$("#barcodeValue").val();
		// alert(value)
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
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);

		}
	   generateBarcode('<? echo $data[0]; ?>');
	 </script>


 <div style=" width:1100px; position:absolute; top:385px; ">
   <style type="text/css">
            .block_div {
                    width:auto;
                    height:auto;
                    text-wrap:normal;
					font-size:10.5px;
                    vertical-align:bottom;
                    display: block;

                    -webkit-transform: rotate(-90deg);
                    -moz-transform: rotate(-90deg);
            }

        </style>
   <?

     $sql_size_ration=sql_select("select a.id,b.size_id,b.size_ratio from
     ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls a where a.id=b.dtls_id  and a.mst_id=$mst_id and b.status_active=1  and  b.is_deleted=0 and a.status_active=1
     and a.is_deleted=0 ");
	 $detali_data_arr=array();
	 $plice_data_arr=array();
	 $size_id_arr=array();
	 $total_gmt_qty=array();
	 $grand_total=0;
	 $size_qty=0;
	 $size_ratio_arr=array();
	 foreach($sql_size_ration as $size_val)
	 {
	  	$size_ratio_arr[$size_val[csf('id')]][$size_val[csf('size_id')]]=$size_val[csf('size_ratio')];
	 }

	$sql_main_qry=sql_select("select c.id,a.id,a.color_id,c.size_id,c.roll_no,sum(c.roll_wgt) as roll_weight,c.plies, sum(c.size_qty) as size_qty,c.roll_id
	 from  ppl_cut_lay_roll_dtls c,ppl_cut_lay_dtls a
	 where  a.id=c.dtls_id and a.mst_id=$mst_id and c.status_active=1  and  c.is_deleted=0 and a.status_active=1 and a.is_deleted=0
	 group by c.id,a.id,a.color_id,c.size_id,c.roll_no,c.plies,c.roll_id
	 order by a.id,c.id");
	 $detali_data_arr=array();
	 $plice_data_arr=array();
	 $size_id_arr=array();
	 $total_gmt_qty=array();
	 $grand_total=0;
	 $size_qty=0;
	 foreach($sql_main_qry as $main_val)
	 {
	    $size_id_arr[$main_val[csf('size_id')]]=$main_val[csf('size_id')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['plies']=$main_val[csf('plies')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['size']=$main_val[csf('size_id')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['marker_qty']=$main_val[csf('size_qty')];
		$total_gmt_qty[$main_val[csf('id')]][$main_val[csf('roll_id')]]['gmt_qty']+=$main_val[csf('size_qty')];
		$grand_total+=$main_val[csf('marker_qty')];
		$size_qty=return_field_value("size_qty","ppl_cut_lay_bundle","mst_id =$mst_id AND dtls_id =".$main_val[csf('id')]." ");
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['bundle_qty']=$size_qty;
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['color']=$main_val[csf('color_id')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_no']=$main_val[csf('roll_no')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_weight']=$main_val[csf('roll_weight')];
	 }
 //print_r($plice_data_arr);die;
   $col_span=count($size_id_arr);
   $td_width=450/$col_span;

  // echo $td_width;die;

   ?>
    <table border="1" cellpadding="1" cellspacing="1"   width="1100"class="rpt_table" rules="all">
          <tr height="30" >
          <td width="30">SL</td>
          <td width="60" align="center">Roll No </td>
          <td width="60" align="center">Roll Kgs </td>
          <td width="50" align="center">Color </td>
          <td width="70"> Plies & Pcs/Bundle</td>
           <td width="80">Particulars</td>
          <td width="470" align="center" colspan="<? echo $col_span; ?>">Size, Ratio and Garments Qty.</td>
          <td width="50" align="center">Total Gmts</td>
          <td width="70" align="center">Per Roll Cons</td>
           <td width="60">Cut Out Faults</td>
          <td width="60" align="center">End of Roll Length</td>
          <td width="60" align="center">Total Unused Length </td>
         </tr>
        <?
		 $i=1; $tot_gmts=0; $tot_roll_wght=0;
		  foreach($plice_data_arr as $dtls_id=>$dtls_val)
			  {
				foreach($dtls_val as $plice_id=>$plice_val)
				 {
					 $tot_roll_wght+=$plice_val['roll_weight'];
				 ?>
                 <tr height="20">
                      <td width="" rowspan="4"><? echo $i;  ?></td>
                      <td width="" align="center" rowspan="4"><? echo $plice_val['roll_no']; ?> </td>
                      <td width="" align="center" rowspan="4"><? echo $plice_val['roll_weight']; ?></td>
                      <td width="" align="center" rowspan="4" style="vertical-align:middle"><div class="block_div"><? echo $color_arr[$plice_val['color']];  ?></div></td>
                      <td width="" align="left" rowspan="2"><? echo $plice_val['plies']." Plies";  ?></td>
                       <td width="">Size</td>

                   <?
					  foreach($size_id_arr as $size_id=>$size_val)
						{
					 ?>
						   <td width="<? echo $td_width; ?>" align="center" ><? echo $size_arr[$detali_data_arr[$dtls_id][$plice_id][$size_id]['size']];  ?>	</td>

					 <?
						 }
					 ?>
                      <td width="" align="right" valign="bottom" ></td>
                      <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>
                   <tr height="20">
                     <td width="">CAD Ratio</td>
               	 <?
				  foreach($size_id_arr as $size_id=>$size_val)
				    {
						$total_size_ratio+=$size_ratio_arr[$dtls_id][$size_id]['size_ratio'];
				 ?>
                       <td width="<? echo $td_width; ?>" align="center" ><? echo $size_ratio_arr[$dtls_id][$size_id]['size_ratio'];  ?>	</td>
                 <?
				     }
                 ?>
                      <td width="" align="right" valign="bottom" ><? echo $total_size_ratio; $total_size_ratio=0;  ?></td>
                      <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>
                     <tr height="20">
                       <td width="" align="left" rowspan="2"><?  echo $plice_val['bundle_qty']."/Bundle";  ?></td>
                       <td width=""> Gmts Qty.
</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
							{
								$total_gmt_qty_roll+=$detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];
						 ?>
							   <td width="<? echo $td_width; ?>" align="center" ><? echo $detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];  ?>	</td>

						 <?
							 }
							 $tot_gmts+=$total_gmt_qty_roll;
						 ?>
                      <td width="" align="right" valign="bottom" ><? echo $total_gmt_qty_roll;$total_gmt_qty_roll=0;  ?></td>
					  <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>
              </tr>
                     <tr height="20">
                       <td width="">Bundle Qty.</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
						  {
						 ?>
							   <td width="<? echo $td_width; ?>" align="center"  style="font-size:14px;">
							   <?
							   $bdl_qty=floor($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty']/$plice_val['bundle_qty']);
							    $extra_bdl=($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty']%$plice_val['bundle_qty']);
								if($extra_bdl!=0) $bdl_qty=$bdl_qty." Full & one $extra_bdl  pcs";

							    echo $bdl_qty;
							    ?>
                               </td>
						 <?
						 }
						 ?>
					  <td width="" align="center"></td>
                      <td width=""></td>
                      <td width="" align="center"></td>
                      <td width="" align="center"> </td>
                   </tr>

              <?
				 $i=$i+1;
				 }
			  }

		     ?>


      </table>
      <?
      $table_height=30+($i+1)*20;
	//echo $table_height;die;
	$div_position=$table_height+420;

	$color_size_qty_arr=array();
	$color_size_sql=sql_select ("SELECT po_break_down_id,item_number_id,size_number_id,color_number_id,sum(CAST(plan_cut_qnty as INT)) as plan_cut_qnty from wo_po_color_size_breakdown
	where is_deleted=0 and status_active=1 and po_break_down_id in (".$order_id.") group by po_break_down_id,item_number_id,size_number_id,color_number_id");
	foreach($color_size_sql as $s_id)
	{
		$color_size_qty_arr[$s_id[csf('po_break_down_id')]][$s_id[csf('item_number_id')]][$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')];
		//$tot_plan_qty+=$s_id[csf('plan_cut_qnty')];
	}

   $sql_sewing=sql_select("SELECT b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes,sum( b.cons ) AS conjumction
   FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a
   WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".$order_id.") and b.cons!=0 and a.body_part_id in (1,20,125)
   GROUP BY b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
   $con_per_dzn=array();
   $po_item_qty_arr=array();
   $color_size_conjumtion=array();
   foreach($sql_sewing as $row_sew)
   {
		$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);

		$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
		$po_item_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];

		$tot_plan_qty+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
   }
   //print_r($color_size_conjumtion);
	$con_qnty=0;
	foreach($color_size_conjumtion as $p_id=>$p_value)
	{
		foreach($p_value as $i_id=>$i_value)
		{
			foreach($i_value as $c_id=>$c_value)
			{
			foreach($c_value as $s_id=>$s_value)
				{
					foreach($s_value as $b_id=>$b_value)
					{
						$order_color_size_qty=$b_value['plan_cut_qty'];
						// $order_qty=$po_item_qty_arr[$p_id][$i_id][$c_id][$b_id]['plan_cut_qty'];
						$order_qty=$tot_plan_qty;
						$order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
						$conjunction_per= ($b_value['conjum']*$order_color_size_qty_per/100);
						$con_per_dzn[$p_id][$c_id]+=$conjunction_per;
						$con_qnty+=$conjunction_per;
					}
				}
			}
		}
	}

	$con_qnty=($con_qnty/$costing_per_qty)*12;
	$net_cons=($tot_roll_wght/$tot_gmts)*12;
	$loss_gain='&nbsp;'; $gain='&nbsp;'; $loss='&nbsp;';
	/*$cons_balance=$cad_marker_cons-$net_cons;
	if($cad_marker_cons>$net_cons)
	{
		$loss_gain='Gain';
		$gain=number_format($cons_balance,4);
	}
	else if($cad_marker_cons<$net_cons)
	{
		$loss_gain='Loss';
		$loss=number_format(abs($cons_balance),4);
	}*/

	$cons_balance=$con_qnty-$net_cons;
	if($con_qnty>$net_cons)
	{
		$loss_gain='Gain';
		$gain=number_format($cons_balance,4);
	}
	else if($con_qnty<$net_cons)
	{
		$loss_gain='Loss';
		$loss=number_format(abs($cons_balance),4);
	}
	?>


       <div style=" width:160px; position:absolute; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="200" class="rpt_table" rules="all">
                  <tr  height="30">
                       <td width="100">Booking<br>Consumption <br>Per Dzn</td>
                       <td width="100" align="center" ><? echo number_format($con_qnty,4); ?></td>
                  </tr>
            </table>
       </div>

        <div style=" width:160px; position:absolute; left:220px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="200"class="rpt_table" rules="all">
                  <tr  height="30" >
                       <td width="100" >CAD Marker<br>Consumption <br>Per Dzn</td>
                       <td width="100" align="center" ><? echo $cad_marker_cons; ?></td>
                  </tr>
            </table>
       </div>
         <div style=" width:180px; position:absolute; left:440px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1" width="180"class="rpt_table" rules="all">
                  <tr  height="30">
                       <td width="40" rowspan="2">Net<br>KGS <br>Used</td>
                       <td width="70" align="center" >KGs</td>
                       <td width="70" align="center" >G.Qty</td>
                  </tr>
                   <tr  height="30">

                       <td width="70" align="center" ><? echo $tot_roll_wght; ?></td>
                       <td width="70" align="center" ><? echo $tot_gmts; ?></td>
                  </tr>
            </table>
       </div>

        <div style=" width:230px; position:absolute; right:191px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1" width="220"class="rpt_table" rules="all">
                  <!--<tr height="20">
                       <td width="80" rowspan="2">Net<br>Composition <br>Per Dzn</td>
                       <td width="70" align="center" >Net</td>
                       <td width="70" align="center" ></td>
                  </tr>
                   <tr height="20">
                       <td width="70" align="center" ><?echo number_format($net_cons,4); ?></td>
                       <td width="70" align="center" ></td>
                  </tr>-->
                  <tr height="20">
                       <td width="80" rowspan="2">Net<br>Consumption <br>Per Dzn</td>
                       <td width="70" align="center">Net</td>
                       <td width="70" align="center">Loss</td>
                       <td width="70" align="center">Gain</td>
                  </tr>
                   <tr height="20">
                       <td width="70" align="center" ><? echo number_format($net_cons,4); ?></td>
                       <td width="70" align="center" ><? echo $loss; ?></td>
                       <td width="70" align="center"><? echo $gain; ?></td>
                  </tr>
            </table>
       </div>
       <div style="width:180px; position:absolute; right:0; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1" width="180" class="rpt_table" rules="all">
                  <tr>
                       <td width="100">Lay<br>Loss/Gain</td>
                       <td width="80" align="center" ><? echo $loss_gain; ?></td>
                  </tr>
            </table>
       </div>
       <br><br><br>
       <? echo signature_table(58, $company_id, "1100px"); ?>
	</div>
<?
   exit();
}
if($action=="cut_lay_entry_report_print_two")
{
    // extract($_REQUEST);
	$data=explode('*',$data);
	// print_r($data);

	$sql=sql_select("select a.id,a.job_no,a.cut_num_prefix_no,a.table_no,a.marker_length,a.marker_width,a.fabric_width,a.gsm,a.width_dia,a.start_time,a.end_time,a.cad_marker_cons,a.batch_id,a.company_id, b.grouping,d.color_id,d.order_cut_no,d.roll_data,d.gmt_item_id,e.roll_id,a.marker_type from ppl_cut_lay_mst a,wo_po_break_down b,wo_po_color_size_breakdown c,ppl_cut_lay_dtls d,ppl_cut_lay_bundle e  where a.job_no=b.job_no_mst and a.id=d.mst_id and b.id=c.po_break_down_id and a.id=e.mst_id and cutting_no='".$data[0]."' ");

//  $sql_result="select a.id,a.job_no,a.cut_num_prefix_no,a.table_no,a.marker_length,a.marker_width,a.fabric_width,a.gsm,a.width_dia,a.start_time,a.end_time,a.cad_marker_cons,a.batch_id,a.company_id, b.grouping,d.color_id,d.order_cut_no,d.roll_data,d.gmt_item_id,e.roll_id from ppl_cut_lay_mst a,wo_po_break_down b,wo_po_color_size_breakdown c,ppl_cut_lay_dtls d,ppl_cut_lay_bundle e  where a.job_no=b.job_no_mst and a.id=d.mst_id and b.id=c.po_break_down_id and a.id=e.mst_id and cutting_no='".$data[0]."' ";
// 	 echo $sql_result;


	$batchsql="select a.id, a.batch_no from pro_roll_details a, ppl_cut_lay_bundle b,ppl_cut_lay_mst c where a.id=b.roll_id and b.mst_id=c.id and a.entry_form=509 and cutting_no='".$data[0]."' ";
	// echo $batchsql;
	$main_batch_sql=sql_select($batchsql);
	$batch_no_arr=array();

	foreach($main_batch_sql as $row)
	{
		$batch_no_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')].",";

	}
	// echo '<pre>';
	// print_r($batch_no_arr);
	// echo '</pre>';



	foreach($sql as $val)
		{
			$mst_id=$val[csf('id')];
			$company_id=$val[csf('company_id')];
			$cut_prifix=$val[csf('cut_num_prefix_no')];
		    $table_no=$val[csf('table_no')];
			$marker_length=$val[csf('marker_length')];
			$marker_with=$val[csf('marker_width')];
			$fabric_with=$val[csf('fabric_width')];
			$gsm=$val[csf('gsm')];
			$marker_type=$val[csf('marker_type')];

			$dia_width=$val[csf('width_dia')];
			$txt_batch=$val[csf('batch_id')];
			$cad_marker_cons=$val[csf('cad_marker_cons')];
			$job_no=$val[csf('job_no')];
			$grouping=$val[csf('grouping')];
			$color=$val[csf('color_id')];
			$item=$val[csf('gmt_item_id')];
			$order_cut_no=$val[csf('order_cut_no')];
			$start_time=$val[csf('start_time')];
			$end_time=$val[csf('end_time')];
			// $batch_no=$val[csf("roll_id")];

		}



	$costing_per=return_field_value("costing_per","wo_pre_cost_mst", "job_no='$job_no'");
	if($costing_per==1)
	{
		$costing_per_qty=12;
	}
	else if($costing_per==2)
	{
		$costing_per_qty=1;
	}
	else if($costing_per==3)
	{
		$costing_per_qty=24;
	}
	else if($costing_per==4)
	{
		$costing_per_qty=36;
	}
	else if($costing_per==5)
	{
		$costing_per_qty=48;
	}

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$comp_name=return_field_value("company_short_name","lib_company", "id=$company_id");
	$sql_buyer_arr=sql_select("select buyer_name,style_ref_no from  wo_po_details_master where job_no='$data[1]'");
	$sql_order=sql_select("select order_ids,gmt_item_id,order_qty from ppl_cut_lay_dtls where mst_id='$mst_id'");
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id,color_name  from  lib_color", "id", "color_name"  );
	$order_number_arr=return_library_array( "select id, po_number from wo_po_break_down where job_no_mst='$data[1]'",'id','po_number');
	$table_no_library=return_library_array( "select id,table_no  from  lib_cutting_table", "id", "table_no"  );

	 $table_lib= return_library_array("SELECT id,table_no FROM LIB_CUTTING_TABLE WHERE is_deleted = 0 and status_active=1 and company_id='$company_id'  order by table_no","id","table_no");
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	// $batchNo_arr=return_library_array( "select a.id, a.batch_no from pro_roll_details a, ppl_cut_lay_bundle b where a.id=b.roll_id and mst_id='$mst_id' and a.entry_form=509","id","batch_no" );

	//print_r($sql_order);
	// $table_name=sql_select($table_lib);
	// // $table_arr=array();
	// foreach($table_name as $val)
	// {
	// 	$table_no=$val[csf('table_name')];
	// }
	// echo $table_no;
	//  echo '<pre>';print_r($table_arr);echo'</pre>';
	$order_number=""; $order_id='';
	foreach($sql_order as $order_val)
	{
		$item_name=$order_val[csf('gmt_item_id')];
		$order_qty+=$order_val[csf('order_qty')];
		if($order_id!="")
		{
			$order_id.=",".$order_val[csf('order_ids')];
		}
		else
		{
			$order_id=$order_val[csf('order_ids')];
		}
	}


	$order_ids=array_unique(explode(",",$order_id));
	foreach($order_ids as $poId)
	{
		if($order_number!="")
		{
			$order_number.=", ".$order_number_arr[$poId];
		}
		else
		{
			$order_number=$order_number_arr[$poId];
		}
	}


	?>
    <div style="width:1100px; position:relative">
    <div style=" width:500; height:200px; position:absolute; left:300px; top:0; ">
        <table width="500" cellspacing="0" align="center">
            <tr>
                <td  align="center" style="font-size:22px; font-weight:bold;"><strong><? echo $company_library[$company_id]; ?></strong></td>
            </tr>
            <tr>
                <td  align="center" style="font-size:18px; font-weight:bold;"><strong>LAY CHART & CONSUMPTION REPORT</strong></td>
            </tr>
       </table>

    </div>
    <div style=" width:200; height:40px; position:absolute; right:0; top:50px; ">Date:  ......../......../.......... </div>
    <div style=" width:200; height:60px; position:absolute; right:0; top:90px; " id="barcode_img_id"> </div>
    <div style=" top:80px; width:270; height:200px; position:absolute; left:0; ">
	<table border="1"  cellspacing="0"  width="260"class="rpt_table" rules="all">
         <tr>
              <td width="80">Buyer</td><td width="180" align="center"><? echo $buyer_arr[$sql_buyer_arr[0][csf('buyer_name')]]; ?></td>
         </tr>
         <tr>
              <td>Int B No</td><td align="center"> <? echo $grouping; ?></td>
         </tr>
         <tr>
              <td>Style</td><td align="center"> <? echo $sql_buyer_arr[0][csf('style_ref_no')]; ?></td>
         </tr>
         <tr>
              <td>Item</td> <td align="center"><? echo $garments_item[$item_name]; ?></td>
        </tr>
		<tr>
              <td>Garments Color</td> <td align="center"><? echo $color_arr[$color]; ?></td>
        </tr>
         <tr>
              <td>Order Qty</td><td align="right"><? echo $order_qty; ?></td>
         </tr>

    </table>
    </div>
    <div  style="width:270; position:absolute; height:30px; top:80px; left:280px">
	<table border="1"  cellspacing="0"  width="260"class="rpt_table" rules="all">
	    <tr>
              <td width="80">CAD Marker Length</td><td width="80" align="center" ><? echo $marker_length;  ?></td>
         </tr>
         <tr>
              <td>CAD Marker Width</td><td align="center" ><? echo $marker_with;  ?></td>
         </tr>
         <tr>
		    <td> CAD Fabric Width/Dia</td><td align="center"><? echo $fabric_with; ?></td>
         </tr>
         <tr>
              <td>CAD GSM</td> <td align="center"><? echo $gsm; ?></td>
        </tr>
		<tr>
              <td>Marker Type</td> <td><p><? echo $marker_type_array[$marker_type]; ?></p></td>
        </tr>

    </table>
    </div>
    <br><br>
    <div  style="width:505; position:relative;  top:250px; left:0px">
      <table border="1" cellpadding="1" cellspacing="1"   width="480"class="rpt_table" rules="all">
          <tr>
          <td width="80">Table No</td>
          <td width="75" align="center"><? echo $table_lib[$table_no_library[$table_no]]; ?></td>
          <td width="75" align="center">Batch No </td>
		  <td width="75" align="center">  <? foreach($batch_no_arr as $id=>$val){ echo $val['batch_no']; }; ?></td>
          <td width="80" align="center">Dia(Tube<br>/Open)</td>
          <td >Start Time</td>
		  <td align="center"><? echo $start_time; ?></td>
		  <td align="center" width="100"><strong>Total Time Taken</strong></td>

         </tr>
         <tr>
          <td>Sys Cutting No</td> <td align="center"><? echo $comp_name."-".$cut_prifix; ?></td>

		  <td width="80">M.Cutting No</td>
		 <td width="75" align="center"><?echo $order_cut_no;?></td>
         <td width="80" align="center"> <? echo $fabric_typee[$dia_width]; ?></td>
		 <td >End  Time</td>
		 <td align="center"><? echo $end_time; ?></td>
		 <td align="center" width="100"><? $total_time_taken=$end_time-$start_time; echo $total_time_taken; ?></td>
         </tr>
      </table>
    </div>

     <div  style="width:200; position:absolute; height:400px; top:80px; left:580px">
       <table border="1" cellpadding="1" cellspacing="1"   width="200"class="rpt_table" rules="all">
          <tr height="30">
          <td width="90">Sperading Operators</td>
          <td width="100" align="center"></td>
         </tr>
         <tr height="30">
          <td width="">Checked by Marker Man</td>
          <td width="" align="center"></td>
         </tr>
           <tr height="30">
          <td width="90">Cutter Man-1</td>
          <td width="100" align="center"></td>
         </tr>
         <tr height="43">
          <td width="">Cutter Man-2</td>
          <td width="" align="center"></td>
         </tr>
          <tr height="30">
          <td width="">Cutter Man-3</td>
          <td width="" align="center"></td>
         </tr>
      </table>
    </div>




     <script type="text/javascript" src="../../../js/jquery.js"></script>
     <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess ){

			var value = valuess;//$("#barcodeValue").val();
		// alert(value)
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
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);

		}
	   generateBarcode('<? echo $data[0]; ?>');
	 </script>


 <div style=" width:1100px; position:absolute; top:385px; ">
   <style type="text/css">
            .block_div {
                    width:auto;
                    height:auto;
                    text-wrap:normal;
					font-size:10.5px;
                    vertical-align:bottom;
                    display: block;

                    -webkit-transform: rotate(-90deg);
                    -moz-transform: rotate(-90deg);
            }

        </style>
   <?

     $sql_size_ration=sql_select("select a.id,b.size_id,b.size_ratio from
     ppl_cut_lay_size_dtls b,ppl_cut_lay_dtls a where a.id=b.dtls_id  and a.mst_id=$mst_id and b.status_active=1  and  b.is_deleted=0 and a.status_active=1
     and a.is_deleted=0 ");
	 $detali_data_arr=array();
	 $plice_data_arr=array();
	 $size_id_arr=array();
	 $total_gmt_qty=array();
	 $grand_total=0;
	 $size_qty=0;
	 $size_ratio_arr=array();
	 foreach($sql_size_ration as $size_val)
	 {
	  	$size_ratio_arr[$size_val[csf('id')]][$size_val[csf('size_id')]]=$size_val[csf('size_ratio')];
	 }
	//  echo '<pre>';
	//  print_r($size_ratio_arr);
	//  echo '</pre>';

	$sql_main_qry=sql_select("select c.id,a.id,a.color_id,c.size_id,c.roll_no,sum(c.roll_wgt) as roll_weight,c.plies, sum(c.size_qty) as size_qty,c.roll_id
	 from  ppl_cut_lay_roll_dtls c,ppl_cut_lay_dtls a
	 where  a.id=c.dtls_id and a.mst_id=$mst_id and c.status_active=1  and  c.is_deleted=0 and a.status_active=1 and a.is_deleted=0
	 group by c.id,a.id,a.color_id,c.size_id,c.roll_no,c.plies,c.roll_id
	 order by a.id,c.id");
	 $detali_data_arr=array();
	 $plice_data_arr=array();
	 $size_id_arr=array();
	 $total_gmt_qty=array();
	 $grand_total=0;
	 $size_qty=0;
	 foreach($sql_main_qry as $main_val)
	 {
	    $size_id_arr[$main_val[csf('size_id')]]=$main_val[csf('size_id')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['plies']=$main_val[csf('plies')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['size']=$main_val[csf('size_id')];
		$detali_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]][$main_val[csf('size_id')]]['marker_qty']=$main_val[csf('size_qty')];
		$total_gmt_qty[$main_val[csf('id')]][$main_val[csf('roll_id')]]['gmt_qty']+=$main_val[csf('size_qty')];
		$grand_total+=$main_val[csf('marker_qty')];
		$size_qty=return_field_value("size_qty","ppl_cut_lay_bundle","mst_id =$mst_id AND dtls_id =".$main_val[csf('id')]." ");
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['bundle_qty']=$size_qty;
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['color']=$main_val[csf('color_id')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_no']=$main_val[csf('roll_no')];
		$plice_data_arr[$main_val[csf('id')]][$main_val[csf('roll_id')]]['roll_weight']=$main_val[csf('roll_weight')];
	 }
 //print_r($plice_data_arr);die;
   $col_span=count($size_id_arr);
   $td_width=450/$col_span;

  // echo $td_width;die;

   ?>
   <br>
    <table border="1" cellpadding="1" cellspacing="1"   width="970px position:absolute" top="450px" class="rpt_table" rules="all">
          <tr height="30" >
          <td width="30">SL</td>
          <td width="60" align="center">Roll No </td>
          <td width="60" align="center">Roll Kgs </td>
          <td width="50" align="center">Cuttable End Bit.</td>
		  <td width="50" align="center">Unusable End Bit</td>
		  <td width="50" align="center">Unusable Wastage</td>
          <td width="70"> Plies & Pcs/Bundle</td>
           <td width="80">Particulars</td>
          <td width="470" align="center" colspan="<? echo $col_span; ?>">Size, Ratio and Garments Qty.</td>
          <td width="50" align="center">Total Gmts</td>
         </tr>
        <?
		 $i=1; $tot_gmts=0; $tot_roll_wght=0;
		  foreach($plice_data_arr as $dtls_id=>$dtls_val)
			  {
				foreach($dtls_val as $plice_id=>$plice_val)
				 {
					 $tot_roll_wght+=$plice_val['roll_weight'];
				 ?>
                 <tr height="20">
                      <td width="" rowspan="4"><? echo $i;  ?></td>
                      <td width="" align="center" rowspan="4"><? echo $plice_val['roll_no']; ?> </td>
                      <td width="" align="center" rowspan="4"><? echo $plice_val['roll_weight']; ?></td>
                      <td width="" align="center" rowspan="4" style="vertical-align:middle"><div class="block_div"><? ?></div></td>
					  <td width="" align="center" rowspan="4" style="vertical-align:middle"><div class="block_div"><? ?></div></td>
					  <td width="" align="center" rowspan="4" style="vertical-align:middle"><div class="block_div"><? ?></div></td>

                      <td width="" align="left" rowspan="2"><? echo $plice_val['plies']." Plies";  ?></td>
                       <td width="">Size</td>

                   <?
					  foreach($size_id_arr as $size_id=>$size_val)
						{

					 ?>
						   <td width="<? echo $td_width; ?>" align="center" ><? echo $size_arr[$detali_data_arr[$dtls_id][$plice_id][$size_id]['size']]; ?>	</td>

					 <?
						 }
					 ?>
                      <td width="" align="right" valign="bottom" ></td>

                   </tr>
                   <tr height="20">
                     <td width="">CAD Ratio</td>
               	 <?
				  foreach($size_id_arr as $size_id=>$size_val)
				    {

				 ?>
                       <td width="<? echo $td_width; ?>" align="center" ><? echo $size_ratio_arr[$dtls_id][$size_id]; $total_size_ratio+=$size_ratio_arr[$dtls_id][$size_id];  ?></td>
                 <?
				     }
                 ?>
                      <td width="" align="right" valign="bottom" ><? echo $total_size_ratio; $total_size_ratio=0;  ?></td>

                   </tr>
                     <tr height="20">
                       <td width="" align="left" rowspan="2"><?  echo $plice_val['bundle_qty']."/Bundle";  ?></td>
                       <td width=""> Gmts Qty.
</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
							{
								$total_gmt_qty_roll+=$detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];
						 ?>
							   <td width="<? echo $td_width; ?>" align="center" ><? echo $detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty'];  ?>	</td>

						 <?
							 }
							 $tot_gmts+=$total_gmt_qty_roll;
						 ?>
                      <td width="" align="right" valign="bottom" ><? echo $total_gmt_qty_roll;$total_gmt_qty_roll=0;  ?></td>


                   </tr>
              </tr>
                     <tr height="20">
                       <td width="">Bundle Qty.</td>
                         <?
						  foreach($size_id_arr as $size_id=>$size_val)
						  {
						 ?>
							   <td width="<? echo $td_width; ?>" align="center"  style="font-size:14px;">
							   <?
							   $bdl_qty=floor($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty']/$plice_val['bundle_qty']);
							    $extra_bdl=($detali_data_arr[$dtls_id][$plice_id][$size_id]['marker_qty']%$plice_val['bundle_qty']);
								if($extra_bdl!=0) $bdl_qty=$bdl_qty." Full & one $extra_bdl  pcs";

							    echo $bdl_qty;
							    ?>
                               </td>
						 <?
						 }
						 ?>

                   </tr>

              <?
				 $i=$i+1;
				 }
			  }

		     ?>


      </table>
      <?
      $table_height=30+($i+1)*20;
	//echo $table_height;die;
	$div_position=$table_height+420;

	$color_size_qty_arr=array();
	$color_size_sql=sql_select ("SELECT po_break_down_id,item_number_id,size_number_id,color_number_id,sum(CAST(plan_cut_qnty as INT)) as plan_cut_qnty from wo_po_color_size_breakdown
	where is_deleted=0 and status_active=1 and po_break_down_id in (".$order_id.") group by po_break_down_id,item_number_id,size_number_id,color_number_id");
	foreach($color_size_sql as $s_id)
	{
		$color_size_qty_arr[$s_id[csf('po_break_down_id')]][$s_id[csf('item_number_id')]][$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')];
		//$tot_plan_qty+=$s_id[csf('plan_cut_qnty')];
	}

   $sql_sewing=sql_select("SELECT b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes,sum( b.cons ) AS conjumction
   FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a
   WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".$order_id.") and b.cons!=0 and a.body_part_id in (1,20,125)
   GROUP BY b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
   $con_per_dzn=array();
   $po_item_qty_arr=array();
   $color_size_conjumtion=array();
   foreach($sql_sewing as $row_sew)
   {
		$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);

		$color_size_conjumtion[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
		$po_item_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];

		$tot_plan_qty+=$color_size_qty_arr[$row_sew[csf('po_break_down_id')]][$row_sew[csf('item_number_id')]][$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];
   }
   //print_r($color_size_conjumtion);
	$con_qnty=0;
	foreach($color_size_conjumtion as $p_id=>$p_value)
	{
		foreach($p_value as $i_id=>$i_value)
		{
			foreach($i_value as $c_id=>$c_value)
			{
			foreach($c_value as $s_id=>$s_value)
				{
					foreach($s_value as $b_id=>$b_value)
					{
						$order_color_size_qty=$b_value['plan_cut_qty'];
						// $order_qty=$po_item_qty_arr[$p_id][$i_id][$c_id][$b_id]['plan_cut_qty'];
						$order_qty=$tot_plan_qty;
						$order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
						$conjunction_per= ($b_value['conjum']*$order_color_size_qty_per/100);
						$con_per_dzn[$p_id][$c_id]+=$conjunction_per;
						$con_qnty+=$conjunction_per;
					}
				}
			}
		}
	}

	$con_qnty=($con_qnty/$costing_per_qty)*12;
	$net_cons=($tot_roll_wght/$tot_gmts)*12;
	$loss_gain='&nbsp;'; $gain='&nbsp;'; $loss='&nbsp;';
	$cons_balance=$net_cons-$cad_marker_cons;
	// if($cad_marker_cons>$net_cons)
	// {
	// 	$loss_gain='Gain';
	// 	$gain=number_format($cons_balance,4);
	// }
	if($cons_balance>0)
	{
	 	$loss_gain='Loss';
	   	$loss=number_format($cons_balance,2);

	}
	else if($cons_balance<0)
	{
		$loss_gain='Gain';
		$gain=number_format(abs($cons_balance),2);
	}

	// $cons_balance=$con_qnty-$net_cons;
	// if($con_qnty>$net_cons)
	// {
	// 	$loss_gain='Gain';
	// 	$gain=number_format($cons_balance,4);
	// }
	// else if($con_qnty<$net_cons)
	// {
	// 	$loss_gain='Loss';
	// 	$loss=number_format(abs($cons_balance),4);
	// }
	?>


       <div style=" width:160px; position:absolute; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="200" class="rpt_table" rules="all">
                  <tr  height="30">
                       <td width="100">Booking<br>Consumption <br>Per Dzn</td>
                       <td width="100" align="center" ><? echo number_format($con_qnty,4); ?></td>
                  </tr>
            </table>
       </div>

        <div style=" width:160px; position:absolute; left:220px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1"   width="200"class="rpt_table" rules="all">
                  <tr  height="30" >
                       <td width="100" >CAD Marker<br>Consumption <br>Per Dzn</td>
                       <td width="100" align="center" ><? echo $cad_marker_cons; ?></td>
                  </tr>
            </table>
       </div>
         <div style=" width:180px; position:absolute; left:440px; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1" width="180"class="rpt_table" rules="all">
                  <tr  height="30">
                       <td width="40" rowspan="2">Net<br>KGS <br>Used</td>
                       <td width="70" align="center" >KGs</td>
                       <td width="70" align="center" >G.Qty</td>
                  </tr>
                   <tr  height="30">

                       <td width="70" align="center" ><? echo $tot_roll_wght; ?></td>
                       <td width="70" align="center" ><? echo $tot_gmts; ?></td>
                  </tr>
            </table>
       </div>

        <div style=" width:450px; position:absolute; right:10px; margin-top:20px;">
          <table border="1" cellpadding="1" cellspacing="1" width="430"class="rpt_table" rules="all">
                  <!--<tr height="20">
                       <td width="80" rowspan="2">Net<br>Composition <br>Per Dzn</td>
                       <td width="70" align="center" >Net</td>
                       <td width="70" align="center" ></td>
                  </tr>
                   <tr height="20">
                       <td width="70" align="center" ><?echo number_format($net_cons,4); ?></td>
                       <td width="70" align="center" ></td>
                  </tr>-->
                  <tr height="20">
                       <td width="80">Actual Consumption Per Dzn </td>
                       <td width="100" align="center">Loss From Booking Consumption</td>
                       <td width="100" align="center">Gain From Booking Consumption</td>
                       <td width="100" align="center">Loss From Cad Consumption</td>
					   <td width="100" align="center">Gain From Cad Consumption</td>
                  </tr>
                   <tr height="20">
				       <td width="80"><? echo fn_number_format($net_cons,2);  ?></td>
                       <td width="100" align="center" ><?   ?></td>
                       <td width="100" align="center" ><? ?></td>
                       <td width="100" align="center"><? echo $loss; ?></td>
                       <td width="100" align="center"><? echo $gain; ?></td>

                  </tr>
            </table>
       </div>
	   <br><br><br>
	   <!-- Query For Total Size -->

       <!-- <div style="width:180px; position:absolute; right:0; margin-top:20px;   ">
          <table border="1" cellpadding="1" cellspacing="1" width="180" class="rpt_table" rules="all">
                  <tr>
                       <td width="100">Lay<br>Loss/Gain</td>
                       <td width="80" align="center" ><? echo $loss_gain; ?></td>
                  </tr>
            </table>
       </div> -->
       <br><br><br>
  <?


//    $sql_cut=sql_select("select a.size_number_id,a.order_quantity,a.plan_cut_qnty,a.excess_cut_perc from wo_po_color_size_breakdown a where job_no_mst='".$data[1]."' ");

   $sql_cut=sql_select("select a.id, a.color_number_id,a.size_number_id,a.item_number_id,a.order_quantity,a.plan_cut_qnty from wo_po_color_size_breakdown a where a.po_break_down_id in (".$order_id.") and a.color_number_id='$color' and a.item_number_id='$item' and a.status_active=1 and a.is_deleted=0 order by a.id,size_number_id asc");

//    echo $sql_cut_one="select a.color_number_id,a.size_number_id,sum(a.order_quantity),a.plan_cut_qnty,a.excess_cut_perc from wo_po_color_size_breakdown a where po_break_down_id in (".$order_id.") group by a.color_number_id,a.size_number_id,a.plan_cut_qnty,a.excess_cut_perc";

   $excesscutsql=sql_select("select a.job_no,a.excess_input_per from wo_booking_mst a  where job_no='".$data[1]."' and a.status_active=1 and a.is_deleted=0 ");
   $excessinputarr=array();
   foreach($excesscutsql as $v)
   {
	   $excessinputarr[$v[csf('job_no')]]['excess_input_per']=$v['EXCESS_INPUT_PER'];

   }





   $sql_size=sql_select("select  a.size_qty,a.size_id from ppl_cut_lay_bundle a,ppl_cut_lay_mst b where b.id=a.mst_id and  job_no='".$data[1]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
//    $sql_size="select sum(a.size_qty),a.size_id from ppl_cut_lay_bundle a,ppl_cut_lay_mst b where b.id=a.mst_id and  cutting_no='".$data[0]."'  group by a.size_id";
//   echo $sql_size;
  $size_id_arr=array();
   $order_arr=array();
   foreach($sql_cut as $row)
   {
	     $size_id_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		// $order_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
	    $order_arr[$row[csf('size_number_id')]]['order_quantity']+=$row[csf('order_quantity')];

   }
    //  print_r($size_id_arr);


   $order_size_arr=array();
   foreach($sql_size as $value)
   {
	     $order_size_arr[$value[csf('size_id')]]['size_qty']+=$value[csf('size_qty')];
   }
//    echo "<br>";
//    print_r($order_size_arr);

//    echo $sql_size;
$tbl_width=450+(count($size_id_arr)*50);


?>

   <div style="width:<?=$tbl_width+20;?>px;" align="center">
       <table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" >

		 <tr>
		  <th width="50">Size</th>
				<?
				foreach($size_id_arr as $size_id=>$size_val)
					{
				?>
						<td width="<? echo $td_width; ?>" align="center" ><? echo $size_library[$size_id]; ?></td>

				<?
					}
                 ?>
				 <th width="50">Total</th>
			</tr>
			<tr>
            <td width="80">Order Qty,Pcs</td>
				<?
				foreach($order_arr as $size_id=>$size_val)
					{
				?>
						<td width="<? echo $td_width; ?>" align="center" ><? echo $size_val['order_quantity']; ?>	</td>
						<? $totalorderquantity+=$size_val['order_quantity']?>

				<?
					}
                 ?>
				 <td width=""><? echo $totalorderquantity; ?></td>
			</tr>
			<tr>
			<td width="80">Plan cut qty,pcs</td>
				<?
				foreach($order_arr as $size_id=>$size_val)
					{
				?>
						<td width="<? echo $td_width; ?>" align="center" ><? $totalorder=$size_val['order_quantity']*$v[csf('excess_input_per')]/100 ; $sumorder=0; $sumorder+=$size_val['order_quantity']+$totalorder; echo round($sumorder); ?>	</td>
						<? $totalplanquantity+=$sumorder;?>


				<?
					}
                 ?>
				 <td width=""><? echo round($totalplanquantity); ?></td>
			</tr>
			<tr>
			<td width="80">Total cut qty,Pcs</td>
				<?
				foreach($size_id_arr as $size_id=>$value)
					{
				?>
						<td width="<? echo $td_width; ?>" align="center" ><? echo $order_size_arr[$size_id]['size_qty']; ?></td>
						<? $totalcutquantity+=$order_size_arr[$size_id]['size_qty'];?>
				<?
					}
                 ?>
				  <td width="50"><? echo $totalcutquantity; ?></td>
			</tr>

			<tr>
			 <td width="80">Balance,pcs</td>
			 <?
			    // $mainorder=0;
				foreach($order_arr as $size_id=>$size_val)
				{
				    $totalorder=$size_val['order_quantity']*$v[csf('excess_input_per')]/100;
					$mainorder=$size_val['order_quantity']+$totalorder;
					//  echo $mainorder;
					$totalmainorder=$mainorder-$order_size_arr[$size_id]['size_qty'];
					// echo $totalmainorder;

				?>
				<td width="<? echo $td_width; ?>" align="center" ><? echo round($totalmainorder); ?>	</td>
					<?$totalrow+=$totalmainorder;?>
				<?
					}
                 ?>

                 <td width=""><? echo round($totalrow); ?></td>
			</tr>
			<tr>

			  <td width="80">Next Ratio</td>
			  <?
				foreach($order_arr as $size_id=>$size_val)
					{
				?>
						<td width="<? echo $td_width; ?>" align="center" ><? ?>	</td>
						<? ?>

				<?
					}
                 ?>
				 <td width="" align="right"><?  ?></td>

			</tr>



	   </table>
</div>





<?

//    $sql_cut=sql_select("select a.size_number_id,a.order_quantity,a.plan_cut_qnty from wo_po_color_size_breakdown a,ppl_cut_lay_mst b where a.job_no_mst=b.job_no and cutting_no='".$data[0]."' ");
//    echo $sql_cut="select a.size_number_id,a.order_quantity,a.plan_cut_qnty from wo_po_color_size_breakdown a,ppl_cut_lay_mst b where a.job_no_mst=b.job_no and cutting_no='".$data[0]."' ";
?>
       <? echo signature_table(58, $company_id, "1100px"); ?>
	</div>
<?
   exit();
}

/*if($action=="size_wise_repeat_cut_no")
{
	$size_wise_repeat_cut_no=return_field_value("gmt_num_rep_sty","variable_order_tracking","company_name='$data' and variable_list=28 and is_deleted=0 and status_active=1");
	if($size_wise_repeat_cut_no==1) $size_wise_repeat_cut_no=$size_wise_repeat_cut_no; else $size_wise_repeat_cut_no=0;

	echo "document.getElementById('size_wise_repeat_cut_no').value 			= '".$size_wise_repeat_cut_no."';\n";
	exit();
}*/

if ($action=="load_drop_down_color_type")
{   list($po_id,$row_no,$color,$gmt_id)=explode('_',$data);

	$sql_dtls=sql_select("select color_type_id from ppl_cut_lay_dtls where order_ids in(".$po_id.") and color_id=$color and gmt_item_id=$gmt_id");

	$color_type_arr=array();
	$sql="SELECT b.color_type_id from  wo_po_break_down a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c where a.job_no_mst=b.job_no  and b.id=c.pre_cost_fabric_cost_dtls_id and a.id=c.po_break_down_id and b.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id in($po_id) and c.cons>0  group by b.color_type_id";
	foreach(sql_select($sql) as $vals)
	{
		$color_type_arr[$vals[csf("color_type_id")]]=$color_type[$vals[csf("color_type_id")]];
	}
	$status=($sql_dtls[0][csf('color_type_id')])?1:0;

	echo create_drop_down( "cboColorType_".$row_no, 100, $color_type_arr,"", 1, "--Select--",$sql_dtls[0][csf('color_type_id')],"",$status,0);


	exit();
}



if($action=="batch_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	//echo $added_barcode_no;
?>
	<script>

		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });

		var selected_id = new Array();

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str)
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#hidden_barcode_nos').val( id );
		}

		function fnc_close()
		{
			parent.emailwindow.hide();
		}

    </script>

</head>

<body>
<div align="center" style="width:550px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:540px; margin-left:2px">
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="520">
                <thead>
                    <th width="50">SL</th>
					<th width="100">Batch No</th>
                    <th width="130">Barcode No</th>
                    <th width="100">Roll No</th>
                    <th width="55">Roll Qty.</th>
                    <th>GSM</th>
                </thead>
            </table>
            <div style="width:520px; max-height:200px; overflow-y:scroll" id="list_container" align="left">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="500" id="tbl_list_search">
                    <?
					$scanned_barcode_arr=array();
					$barcodeData=sql_select( "select barcode_no from pro_roll_details where entry_form=509 and status_active=1 and is_deleted=0");

					foreach ($barcodeData as $row)
					{
						$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
					}
					if($added_barcode_no!='') 	$added_barcode_cond=" and c.barcode_no not in (".$added_barcode_no.")";
					else 						$added_barcode_cond="";
					//echo "select a.batch_no,c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in (64,37) and c.po_breakdown_id=$order_no  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $added_barcode_cond group by a.batch_no,c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.item_description";

                    $data_array=sql_select("select a.batch_no,c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in (64,37) and c.po_breakdown_id in(".str_replace("'","",$order_no).") and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $added_barcode_cond group by a.batch_no,c.barcode_no, c.roll_id, c.roll_no, c.qnty,b.item_description"); // change by subbir and b.color_id=$color
					$i=1;
					foreach($data_array as $row)
                    {
                        if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
						{
							$item_description_arr=explode(",",$row[csf('item_description')]);
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
                                <td width="50">
                                    <? echo $i; ?>
                                     <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                                </td>
								<td width="100"><? echo $row[csf('batch_no')]; ?></td>
                                <td width="130"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                                <td width="100"><? echo $row[csf('roll_no')]; ?></td>
                                <td align="right" width="55"><? echo number_format($row[csf('qnty')],2); ?></td>
                                <td><? echo $item_description_arr[2]; ?></td>
                            </tr>
						<?
						$i++;
						}
                    }
                    ?>
                </table>
            </div>
            <table width="520">
                <tr>
                    <td align="center" >
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>
</div>
</body>

<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
?>