<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_resource")
{
	echo create_drop_down( "resource", 75, "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID=$data order by RESOURCE_NAME","RESOURCE_ID,RESOURCE_NAME", 1, "-- Select --", $selected, "" );  
	exit();
}


if ($action=="garment_item_list_view")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		
		function js_set_value(id)
		{
			var id_text=$('#id_text_'+id).html();
			$('#hidden_selected_data_id').val(id);
			$('#hidden_selected_data_text').val(id_text);
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body>
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:720px;margin-left:10px">
				<input type="hidden" name="hidden_desc_id" id="hidden_selected_data_id" value="">
				<input type="hidden" name="hidden_desc_no" id="hidden_selected_data_text" value="">  
				
				<div style="margin-left:10px; margin-top:10px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680">
						<thead>
							<th width="50">SL</th>
							<th> Garments Item</th>
						</thead>
					</table>
					<div style="width:700px; max-height:300px; overflow-y:scroll" id="list_container" align="left"> 
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680" id="tbl_list_search">  
							<? 
							$i=1;
						asort($garments_item);
							foreach($garments_item as $id=>$value)
							{  
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $id; ?>)" style="cursor:pointer" >
									<td width="50"><? echo $i; ?></td>
									<td id="id_text_<? echo $id;?>"><? echo $value; ?></td>
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

if ($action=="bodypart_list_view")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		
		function js_set_value(id)
		{
			var id_text=$('#id_text_'+id).html();
			$('#hidden_selected_data_id').val(id);
			$('#hidden_selected_data_text').val(id_text);
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body>
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:720px;margin-left:10px">
				<input type="hidden" name="hidden_desc_id" id="hidden_selected_data_id" value="">
				<input type="hidden" name="hidden_desc_no" id="hidden_selected_data_text" value="">  
				
				<div style="margin-left:10px; margin-top:10px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680">
						<thead>
							<th width="50">SL</th>
							<th> Body Part</th>
						</thead>
					</table>
					<div style="width:700px; max-height:300px; overflow-y:scroll" id="list_container" align="left"> 
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680" id="tbl_list_search">  
							<? 
							$i=1;
							//echo "select mst_id,entry_page_id from lib_body_part_tag_entry_page where status_active=1 and is_deleted=0 and entry_page_id=35";
						$sql_bpart="select a.body_part_full_name,b.mst_id,b.entry_page_id from lib_body_part_tag_entry_page b, lib_body_part a where a.id=b.mst_id and b.status_active=1 and a.status=1  and b.is_deleted=0  and a.is_deleted=0";
							$sql_result=sql_select($sql_bpart);
							foreach ($sql_result as $value) 
							{
								if($value[csf("entry_page_id")]==148)
								{
									$tag_body_part_arr[$value[csf("mst_id")]]=$value[csf("body_part_full_name")];
								}
									$all_body_part_arr[$value[csf("mst_id")]]=$value[csf("body_part_full_name")];
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
							asort($body_partArr);
							foreach($body_partArr as $id=>$value)
							{  
							// if(!in_array($id,$body_part_not_arr)){
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $id; ?>)" style="cursor:pointer" >
									<td width="50"><? echo $i; ?></td>
									<td id="id_text_<? echo $id;?>"><? echo $value; ?></td>
								</tr>
							<? 
							$i++; 
								//}
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

if ($action=="resource_list_view")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
 
	$production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID=$process_id order by RESOURCE_NAME",'RESOURCE_ID','RESOURCE_NAME');
	?> 
	<script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		
		function js_set_value(id)
		{
			var id_text=$('#id_text_'+id).html();
			$('#hidden_selected_data_id').val(id);
			$('#hidden_selected_data_text').val(id_text);
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body>
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:720px;margin-left:10px">
				<input type="hidden" name="hidden_desc_id" id="hidden_selected_data_id" value="">
				<input type="hidden" name="hidden_desc_no" id="hidden_selected_data_text" value="">  
				
				<div style="margin-left:10px; margin-top:10px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680">
						<thead>
							<th width="50">SL</th>
							<th>Resource</th>
							<th width="250">Resource Customize Name</th>
						</thead>
					</table>
					<div style="width:700px; max-height:300px; overflow-y:scroll" id="list_container" align="left"> 
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680" id="tbl_list_search">  
							<? 
							$i=1;
							
							foreach($production_resource_arr as $id=>$value)
							{  
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $id; ?>)" style="cursor:pointer" >
									<td width="50"><? echo $i; ?></td>
									<td><?=$production_resource[$id]; ?></td>
									<td id="id_text_<? echo $id;?>" width="250"><? echo $value; ?></td>
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





if ($action=="fabricDescription_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

	<script>
		
		function js_set_value(id,comp,gsm)
		{
			$('#hidden_desc_id').val(id);
			$('#hidden_desc_no').val(comp);
			$('#hidden_gsm').val(gsm);
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body>
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:720px;margin-left:10px">			
				<input type="hidden" name="hidden_desc_id" id="hidden_desc_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_desc_no" id="hidden_desc_no" class="text_boxes" value="">  
				<input type="hidden" name="hidden_gsm" id="hidden_gsm" class="text_boxes" value=""> 
			
				<div style="margin-left:10px; margin-top:10px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680">
						<thead>
							<th width="150">Fabric Nature</th>
							<th>Construction</th>
							<th width="150">GSM/Weight</th>
							<th width="100"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
						</thead>
						<tbody>
						<tr>
							<td align="center"><? echo create_drop_down( "cbo_fabric_nature", 150, $item_category,'', '1', '-- All --','2','','','2,3' ); ?></td>
							<td align="center"><input type="text" name="txt_construction" id="txt_construction" class="text_boxes" style="width:250px" /></td>
							<td align="center"><input type="text" name="txt_gsm_weight" id="txt_gsm_weight" class="text_boxes" style="width:150px" /></td>
							<td align="center">
							<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_fabric_nature').value+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value, 'fabric_description_list_view', 'search_div', 'sewing_operation_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
							</td>
						</tr>
						</tbody>
					</table>
					</div>
			</fieldset>
			<div id="search_div" style="margin-top:5px"></div>
			
		</form>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}


if ($action=="fabric_description_list_view")
{
	list($garments_nature,$construction,$gsm_weight)=explode('**',$data);
	if($construction!=''){$whereCnon=" and construction like('%".$construction."%')";}
	if($gsm_weight!=''){$whereCnon.=" and gsm_weight=".$gsm_weight;}
	?>    
    <div style="width:700px;">
    <div style="margin-left:10px; margin-top:10px">
        <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680">
            <thead>
                <th width="50">SL</th>
                <th width="100">Fabric Nature</th>
                <th width="150">Construction</th>
                <th>Composition</th>
                <th width="100">GSM/Weight</th>
            </thead>
        </table>
        <div style="width:700px; max-height:250px; overflow-y:scroll" id="list_container" align="left"> 
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680" id="tbl_list_search">  
                <?

                $composition_arr=array();
				$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
				$sql="select a.construction, a.gsm_weight, b.copmposition_id, b.percent, b.count_id, b.type_id, a.id, b.id as bid from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id, b.id";
				$sql_res=sql_select($sql);
				if (count($sql_res)>0)
				{
					foreach($sql_res as $row )
					{
						if(array_key_exists($row[csf('id')],$composition_arr))
						{
							$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
						}
						else
						{
							$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
						}
					}
				}

                $i=1; if($garments_nature=="") $garments_nature=0;
                $data_array=sql_select("select id, construction, fab_nature_id, gsm_weight from lib_yarn_count_determina_mst where fab_nature_id='$garments_nature' $whereCnon and status_active=1 and is_deleted=0");
                foreach($data_array as $row)
                {  
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
                    $comp=$composition_arr[$row[csf('id')]];            
                  
                 	?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $comp; ?>','<? echo $row[csf('gsm_weight')]; ?>')" style="cursor:pointer">
                        <td width="50"><? echo $i; ?></td>
                        <td width="100"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
                        <td width="150"><p><? echo $row[csf('construction')]; ?></p></td>
                        <td><p><? echo $comp; ?></p></td>
                        <td width="100"><? echo $row[csf('gsm_weight')]; ?></td>
                    </tr>
                	<? 
                	$i++; 
                } 
                ?>
            </table>
        </div> 
    </div>
    </div>
	<?
	exit();	
}



if ($action=="smvCalculation_popup")
{
	echo load_html_head_contents("SMV Calculation Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$save_data=explode("_",$smv_data);
	$txt_observer_name=$save_data[0];
	$txt_checked_by=$save_data[1];
	$txtAlterPerc=$save_data[7];
	$txtMachinePerc=$save_data[8];
	$txtRelxPerc=$save_data[9];
	
	if($smv_data=="") $txtAlterPerc=1;
	?> 
	<script>
		function add_break_down_tr( i )
		{ 
			var lastTrId = $('#tbl_list_search tbody tr:last').attr('id').split('_');
			var row_num=lastTrId[1];
			
			if(row_num!=i)
			{
				return false;
			}
			else
			{ 
				i++;
		
				$("#tbl_list_search tbody tr:last").clone().find("input,select").each(function(){
					$(this).attr({ 
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  'name': function(_, name) { return name },
					  'value': function(_, value) { return '' }              
					});
				}).end().appendTo("#tbl_list_search");
					
				$("#tbl_list_search tbody tr:last").removeAttr('id').attr('id','tr_'+i);

				$('#txtOrPick_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate("+i+");");	
				$('#txtOtPick_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate("+i+");");
				$('#txtOrEx_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate("+i+");");	
				$('#txtOtEx_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate("+i+");");
				$('#txtOrDis_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate("+i+");");	
				$('#txtOtDis_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate("+i+");");
				$('#txtOrElm_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate("+i+");");	
				$('#txtOtElm_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate("+i+");");
				
				$('#tr_' + i).find("td:eq(0)").text(i);
				
				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
			}
			
			set_all_onclick();
		}
		
		function fn_deleteRow(rowNo) 
		{ 
			var numRow = $('#tbl_list_search tbody tr').length; 
			if(rowNo!=1)
			{
				$('#tr_'+rowNo).remove();
				calculate_tot();
				calculate_allowance();
			}
			else
			{
				return false;
			}
		}
		
		function calculate(i)
		{
			var orPick=$('#txtOrPick_'+i).val()*1;
			var otPick=$('#txtOtPick_'+i).val()*1;
			var orEx=$('#txtOrEx_'+i).val()*1;
			var otEx=$('#txtOtEx_'+i).val()*1;
			var orDis=$('#txtOrDis_'+i).val()*1;
			var otDis=$('#txtOtDis_'+i).val()*1;
			var orElm=$('#txtOrElm_'+i).val()*1;
			var otElm=$('#txtOtElm_'+i).val()*1;
			
			var btPick=(otPick/100)*orPick;
			var btEx=(otEx/100)*orEx;
			var btDis=(otDis/100)*orDis;
			var btElm=(otElm/100)*orElm;
			
			$('#txtBtPick_'+i).val(btPick.toFixed(2));
			$('#txtBtEx_'+i).val(btEx.toFixed(2));
			$('#txtBtDis_'+i).val(btDis.toFixed(2));
			$('#txtBtElm_'+i).val(btElm.toFixed(2));
			
			var totBt=btPick*1+btEx*1+btDis*1+btElm*1;
			$('#txtTotBt_'+i).val(totBt.toFixed(2));
			
			calculate_tot();
			calculate_allowance();
		}
		
		function calculate_tot()
		{
			var totOtPick=0; var totBtPick=0; var totOtEx=0; var totBtEx=0; var totOtDis=0; var totBtDis=0; var totOtElm=0; var totBtElm=0;
			var readingNos=0;
			$("#tbl_list_search").find('tbody tr').each(function()
			{
				var txtOtPick=trim($(this).find('input[name="txtOtPick[]"]').val());
				var txtBtPick=trim($(this).find('input[name="txtBtPick[]"]').val());
				var txtOtEx=trim($(this).find('input[name="txtOtEx[]"]').val());
				var txtBtEx=trim($(this).find('input[name="txtBtEx[]"]').val());
				var txtOtDis=trim($(this).find('input[name="txtOtDis[]"]').val());
				var txtBtDis=trim($(this).find('input[name="txtBtDis[]"]').val());
				var txtOtElm=trim($(this).find('input[name="txtOtElm[]"]').val());
				var txtBtElm=trim($(this).find('input[name="txtBtElm[]"]').val());
				
				totOtPick=totOtPick*1+txtOtPick*1;
				totBtPick=totBtPick*1+txtBtPick*1;
				totOtEx=totOtEx *1+txtOtEx *1;
				totBtEx=totBtEx*1+txtBtEx*1;
				totOtDis=totOtDis*1+txtOtDis*1;
				totBtDis=totBtDis*1+txtBtDis*1;
				totOtElm=totOtElm*1+txtOtElm*1;
				totBtElm=totBtElm*1+txtBtElm*1;
				
				if(txtBtPick*1>0 || txtBtEx*1>0 || txtBtDis*1>0 || txtBtElm*1>0)
				{
					readingNos++;
				}
			});

			$('#txtOrPickTot').val( totOtPick.toFixed(2));
			$('#txtBtPickTot').val( totBtPick.toFixed(2));
			$('#txtOtExTot').val( totOtEx.toFixed(2));
			$('#txtBtExTot').val( totBtEx.toFixed(2));
			$('#txtOtDisTot').val( totOtDis.toFixed(2));
			$('#txtBtDisTot').val( totBtDis.toFixed(2));
			$('#txtOtElmTot').val( totOtElm.toFixed(2));
			$('#txtBtElmTot').val( totBtElm.toFixed(2));
			
			if(readingNos>0)
			{
				var btPickAvg=(totBtPick/readingNos).toFixed(2);
				var btExAvg=(totBtEx/readingNos).toFixed(2);
				var btDisAvg=(totBtDis/readingNos).toFixed(2);
				var btElmAvg=(totBtElm/readingNos).toFixed(2);
				var totAvgBt=(btPickAvg*1+btExAvg*1+btDisAvg*1+btElmAvg*1).toFixed(2);
				
			}
			else
			{
				var btPickAvg='';
				var btExAvg='';
				var btDisAvg='';
				var btElmAvg='';
				var totAvgBt='';
			}
			
			$('#txtBtPickAvg').val(btPickAvg);
			$('#txtBtExAvg').val(btExAvg);
			$('#txtBtDisAvg').val(btDisAvg);
			$('#txtBtElmAvg').val(btElmAvg);
			$('#txtTotAvgBt').val(totAvgBt);
			var tot_bt=(totBtPick*1+totBtEx*1+totBtDis*1+totBtElm*1).toFixed(2)+' BT';
			$('#tot_bt').text(tot_bt);
		}
		
		function calculate_allowance()
		{
			var txtTotAvgBt=$('#txtTotAvgBt').val()*1;
			var txtAlterPerc=$('#txtAlterPerc').val()*1;
			var txtMachinePerc=$('#txtMachinePerc').val()*1;
			var txtRelxPerc=$('#txtRelxPerc').val()*1;
			
			var txtAlterPercBt=''; var txtMachinePercBt=''; var txtRelxPercBt=''; var txtGtot=''; var smv=''; var target_per_hour='';
			if(txtTotAvgBt>0)
			{
				if(txtAlterPerc>0) { txtAlterPercBt=((txtTotAvgBt/100)*txtAlterPerc).toFixed(2); }
				if(txtAlterPerc>0) { txtMachinePercBt=((txtTotAvgBt/100)*txtMachinePerc).toFixed(2); }
				if(txtAlterPerc>0) { txtRelxPercBt=((txtTotAvgBt/100)*txtRelxPerc).toFixed(2); }
				txtGtot=(txtTotAvgBt*1+txtAlterPercBt*1+txtMachinePercBt*1+txtRelxPercBt*1).toFixed(2);
				smv=(txtGtot/60).toFixed(2);
				target_per_hour=(60/smv).toFixed(2);
			}
			
			$('#txtAlterPercBt').val(txtAlterPercBt);
			$('#txtMachinePercBt').val(txtMachinePercBt);
			$('#txtRelxPercBt').val(txtRelxPercBt);
			$('#txtGtot').val(txtGtot);
			$('#smv').text(smv);
			$('#target_per_hour').text(target_per_hour);
		}
		
		function fnc_close()
		{
			var txt_observer_name=$('#txt_observer_name').val();
			var txt_checked_by=$('#txt_checked_by').val();
			
			var smv=$('#smv').text();
			var target_per_hour=$('#target_per_hour').text();
			
			var txtBtPickAvg=$('#txtBtPickAvg').val();
			var txtBtExAvg=$('#txtBtExAvg').val();
			var txtBtDisAvg=$('#txtBtDisAvg').val();
			var txtBtElmAvg=$('#txtBtElmAvg').val();
			var txtTotAvgBt=$('#txtTotAvgBt').val();
			
			var txtAlterPerc=$('#txtAlterPerc').val();
			var txtMachinePerc=$('#txtMachinePerc').val();
			var txtRelxPerc=$('#txtRelxPerc').val();
			
			var txtAlterPercBt=$('#txtAlterPercBt').val();
			var txtMachinePercBt=$('#txtMachinePercBt').val();
			var txtRelxPercBt=$('#txtRelxPercBt').val();
			var txtGtot=$('#txtGtot').val();
			
			var save_data=txt_observer_name+"_"+txt_checked_by+"_"+target_per_hour+"_"+txtBtPickAvg+"_"+txtBtExAvg+"_"+txtBtDisAvg+"_"+txtTotAvgBt+"_"+txtAlterPerc+"_"+txtMachinePerc+"_"+txtRelxPerc+"_"+txtAlterPercBt+"_"+txtMachinePercBt+"_"+txtRelxPercBt+"_"+txtGtot+"_"+txtBtElmAvg;
			
			var dtls_data='';

			$("#tbl_list_search").find('tbody tr').each(function()
			{
				var txtOrPick=$(this).find('input[name="txtOrPick[]"]').val();
				var txtOtPick=$(this).find('input[name="txtOtPick[]"]').val();
				var txtBtPick=$(this).find('input[name="txtBtPick[]"]').val();
				var txtOrEx=$(this).find('input[name="txtOrEx[]"]').val();
				var txtOtEx=trim($(this).find('input[name="txtOtEx[]"]').val());
				var txtBtEx=$(this).find('input[name="txtBtEx[]"]').val();
				var txtOrDis=trim($(this).find('input[name="txtOrDis[]"]').val());
				var txtOtDis=trim($(this).find('input[name="txtOtDis[]"]').val());
				var txtBtDis=$(this).find('input[name="txtBtDis[]"]').val();
				var txtOrElm=trim($(this).find('input[name="txtOrElm[]"]').val());
				var txtOtElm=trim($(this).find('input[name="txtOtElm[]"]').val());
				var txtBtElm=$(this).find('input[name="txtBtElm[]"]').val();
				
				if(txtBtPick*1>0 || txtBtEx*1>0 || txtBtDis*1>0)
				{
					if(dtls_data=="")
					{
						dtls_data=txtOrPick+"_"+txtOtPick+"_"+txtBtPick+"_"+txtOrEx+"_"+txtOtEx+"_"+txtBtEx+"_"+txtOrDis+"_"+txtOtDis+"_"+txtBtDis+"_"+txtOrElm+"_"+txtOtElm+"_"+txtBtElm;
					}
					else
					{
						dtls_data+="|"+txtOrPick+"_"+txtOtPick+"_"+txtBtPick+"_"+txtOrEx+"_"+txtOtEx+"_"+txtBtEx+"_"+txtOrDis+"_"+txtOtDis+"_"+txtBtDis+"_"+txtOrElm+"_"+txtOtElm+"_"+txtBtElm;
					}
				}
			});
			
			$('#hidden_data').val( save_data );
			$('#hidden_dtls_data').val( dtls_data );
			$('#hidden_smv').val( smv );
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body>
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:965px;margin-left:5px">
				<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
				<input type="hidden" name="hidden_dtls_data" id="hidden_dtls_data" class="text_boxes" value="">
				<input type="hidden" name="hidden_smv" id="hidden_smv" class="text_boxes" value="">
				<div style="margin-top:5px; margin-left:5px">
					<table width="960" cellpadding="0" cellspacing="2" border="0">
						<tr>
							<td width="100"><b>Garments Item</b></td> 
							<td><? echo create_drop_down( "cbo_garment_item", 132, $garments_item,'', 1,"--Select Gmts. Item--",$cbo_garment_item,'',1 ); ?></td>
							<td width="80"><b>Body Part</b></td>
							<td><? echo create_drop_down( "cbo_bodypart", 142, $body_part,'', 1, "-- Select Body Part--",$cbo_bodypart,'',1 ); ?></td>
							<td width="100"><b>Machine Type</b></td>
							<td><? echo create_drop_down( "cbo_machine_type", 112, $production_resource,'', 1, "-- Select Machine Type--",$cbo_resource,'',1 ); ?></td>
							<td width="120"><b>Operation Name</b></td>
							<td><input type="text" name="txt_operation" id="txt_operation" class="text_boxes" value="<? echo $txt_operation; ?>" style="width:100px" disabled="disabled"/></td>
						</tr>
						<tr>
							<td><b>Seam Length</b></td> 
							<td><input type="text" name="txt_seam_length" id="txt_seam_length" class="text_boxes" value="<? echo $txt_seam_length; ?>" style="width:120px" disabled="disabled"/></td>
							<td><b>Fabric Type</b></td>
							<td><input type="text" name="txt_fabric_description" id="txt_fabric_description" class="text_boxes" value="<? echo $txt_fabric_description; ?>" style="width:130px" disabled="disabled"/></td>
							<td><b>Observer Name</b></td>
							<td><input type="text" name="txt_observer_name" id="txt_observer_name" class="text_boxes" value="<? echo $txt_observer_name; ?>" placeholder="Write" style="width:100px"/></td>
							<td><b>Checked By</b></td>
							<td><input type="text" name="txt_checked_by" id="txt_checked_by" class="text_boxes" value="<? echo $txt_checked_by; ?>" placeholder="Write" style="width:100px"/></td>
						</tr>
					</table>
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="955">
						<thead>
							<tr>
								<th width="50" rowspan="2">Element/<br /> Cycle No</th>
								<th width="180" colspan="3">Element-1</th>
								<th width="180" colspan="3">Element-2</th>
								<th width="180" colspan="3">Element-3</th>
								<th width="180" colspan="3">Element-4</th>
								<th rowspan="2" width="65"></th>
								<th rowspan="2">Total BT</th>
							</tr>
							<tr>
								<th width="60">OR %</th>
								<th width="60">OT</th>
								<th width="60" title="Observe time/100*Observe rating">BT</th>
								<th width="60">OR %</th>
								<th width="60">OT</th>
								<th width="60" title="Observe time/100*Observe rating">BT</th>
								<th width="60">OR %</th>
								<th width="60">OT</th>
								<th width="60" title="Observe time/100*Observe rating">BT</th>
								<th width="60">OR %</th>
								<th width="60">OT</th>
								<th width="60" title="Observe time/100*Observe rating">BT</th>
							</tr>
						</thead>
					</table>
					<div style="width:955px; max-height:190px; overflow-y:scroll" id="list_container" align="left"> 
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="935" id="tbl_list_search">  
							<tbody>
							<?
							$i=0;
							if(str_replace("'", '',$prev_data)!="")
							{
								$prevDatas=explode("|",str_replace("'", '',$prev_data));
								foreach($prevDatas as $value)
								{
									$i++;
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$smv_val=explode('_',$value);
									$txtOrPick = $smv_val[0];
									$txtOtPick = $smv_val[1];
									$txtBtPick = $smv_val[2];
									$txtOrEx = $smv_val[3];
									$txtOtEx = $smv_val[4];
									$txtBtEx = $smv_val[5];
									$txtOrDis = $smv_val[6];
									$txtOtDis = $smv_val[7];
									$txtBtDis = $smv_val[8];
									$txtOrElm = $smv_val[9];
									$txtOtElm = $smv_val[10];
									$txtBtElm = $smv_val[11];
									$totBt=$txtBtPick+$txtBtEx+$txtBtDis+$txtBtElm;
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" align="center" id="tr_<? echo $i; ?>">
										<td width="50"><? echo $i; ?></td>
										<td width="60">
											<input type="text" name="txtOrPick[]" id="txtOrPick_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)" value="<? echo $txtOrPick; ?>"/>
										</td>
										<td width="60">
											<input type="text" name="txtOtPick[]" id="txtOtPick_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)" value="<? echo $txtOtPick; ?>"/>
										</td>
										<td width="60">
											<input type="text" name="txtBtPick[]" id="txtBtPick_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" value="<? echo $txtBtPick; ?>" readonly/>
										</td>
										<td width="60">
											<input type="text" name="txtOrEx[]" id="txtOrEx_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)" value="<? echo $txtOrEx; ?>"/>
										</td>
										<td width="60">
											<input type="text" name="txtOtEx[]" id="txtOtEx_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)" value="<? echo $txtOtEx; ?>"/>
										</td>
										<td width="60">
											<input type="text" name="txtBtEx[]" id="txtBtEx_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" value="<? echo $txtBtEx; ?>" readonly/>
										</td>
										<td width="60">
											<input type="text" name="txtOrDis[]" id="txtOrDis_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)" value="<? echo $txtOrDis; ?>"/>
										</td>
										<td width="60">
											<input type="text" name="txtOtDis[]" id="txtOtDis_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)" value="<? echo $txtOtDis; ?>"/>
										</td>
										<td width="60">
											<input type="text" name="txtBtDis[]" id="txtBtDis_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" value="<? echo $txtBtDis; ?>" readonly/>
										</td>
										<td width="60">
											<input type="text" name="txtOrElm[]" id="txtOrElm_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)" value="<? echo $txtOrElm; ?>"/>
										</td>
										<td width="60">
											<input type="text" name="txtOtElm[]" id="txtOtElm_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)" value="<? echo $txtOtElm; ?>"/>
										</td>
										<td width="60">
											<input type="text" name="txtBtElm[]" id="txtBtElm_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" value="<? echo $txtBtElm; ?>" readonly/>
										</td>
										<td width="65">
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>)"/>
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
										</td>
										<td><input type="text" name="txtTotBt[]" id="txtTotBt_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" value="<? echo $totBt; ?>" readonly/></td>
									</tr>
								<?
								}
							}
							
							$defaultNoOfRows=20;
							if($i<$defaultNoOfRows)
							{
								while($i<$defaultNoOfRows)
								{
									$i++;
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" align="center" id="tr_<? echo $i; ?>">
										<td width="50"><? echo $i; ?></td>
										<td width="60">
											<input type="text" name="txtOrPick[]" id="txtOrPick_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)"/>
										</td>
										<td width="60">
											<input type="text" name="txtOtPick[]" id="txtOtPick_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)"/>
										</td>
										<td width="60">
											<input type="text" name="txtBtPick[]" id="txtBtPick_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" readonly/>
										</td>
										<td width="60">
											<input type="text" name="txtOrEx[]" id="txtOrEx_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)"/>
										</td>
										<td width="60">
											<input type="text" name="txtOtEx[]" id="txtOtEx_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)"/>
										</td>
										<td width="60">
											<input type="text" name="txtBtEx[]" id="txtBtEx_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" readonly/>
										</td>
										<td width="60">
											<input type="text" name="txtOrDis[]" id="txtOrDis_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)"/>
										</td>
										<td width="60">
											<input type="text" name="txtOtDis[]" id="txtOtDis_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)"/>
										</td>
										<td width="60">
											<input type="text" name="txtBtDis[]" id="txtBtDis_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" readonly/>
										</td>
										<td width="60">
											<input type="text" name="txtOrElm[]" id="txtOrElm_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)"/>
										</td>
										<td width="60">
											<input type="text" name="txtOtElm[]" id="txtOtElm_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" onKeyUp="calculate(<? echo $i; ?>)"/>
										</td>
										<td width="60">
											<input type="text" name="txtBtElm[]" id="txtBtElm_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px" readonly/>
										</td>
										<td width="65">
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i; ?>)"/>
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
										</td>
										<td><input type="text" name="txtTotBt[]" id="txtTotBt_<? echo $i; ?>" class="text_boxes_numeric" style="width:65px" readonly/></td>
									</tr>
								<?
								}
							}
							?>
							</tbody>
						</table>
					</div> 
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="935" id="tbl_list_tot">
						<tr bgcolor="#FFFFFF" align="center">
							<td width="50" align="left"><b>&nbsp;Total BT</b></td>
							<td width="60">&nbsp;</td>
							<td width="60">
								<input type="text" name="txtOrPickTot" id="txtOrPickTot" class="text_boxes_numeric" style="width:45px" readonly/>
							</td>
							<td width="60">
								<input type="text" name="txtBtPickTot" id="txtBtPickTot" class="text_boxes_numeric" style="width:45px" readonly/>
							</td>
							<td width="60">&nbsp;</td>
							<td width="60">
								<input type="text" name="txtOtExTot" id="txtOtExTot" class="text_boxes_numeric" style="width:45px" readonly/>
							</td>
							<td width="60">
								<input type="text" name="txtBtExTot" id="txtBtExTot" class="text_boxes_numeric" style="width:45px" readonly/>
							</td>
							<td width="60">&nbsp;</td>
							<td width="60">
								<input type="text" name="txtOtDisTot" id="txtOtDisTot" class="text_boxes_numeric" style="width:45px" readonly/>
							</td>
							<td width="60">
								<input type="text" name="txtBtDisTot" id="txtBtDisTot" class="text_boxes_numeric" style="width:45px" readonly/>
							</td>
							<td width="60">&nbsp;</td>
							<td width="60">
								<input type="text" name="txtOtElmTot" id="txtOtElmTot" class="text_boxes_numeric" style="width:45px" readonly/>
							</td>
							<td width="60">
								<input type="text" name="txtBtElmTot" id="txtBtElmTot" class="text_boxes_numeric" style="width:45px" readonly/>
							</td>
							<td width="65">&nbsp;</td>
							<td id="tot_bt" style="font-weight:bold">Total BT</td>
						</tr>
						<tr bgcolor="#E9F3FF" align="center">
							<td width="110" align="left" colspan="2" title="Total basic time/number of cycle/elements"><b>&nbsp;Average BT</b></td>
							<td width="60">&nbsp;</td>
							<td width="60">
								<input type="text" name="txtBtPickAvg" id="txtBtPickAvg" class="text_boxes_numeric" style="width:45px" readonly/>
							</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">
								<input type="text" name="txtBtExAvg" id="txtBtExAvg" class="text_boxes_numeric" style="width:45px" readonly/>
							</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">
								<input type="text" name="txtBtDisAvg" id="txtBtDisAvg" class="text_boxes_numeric" style="width:45px" readonly/>
							</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">
								<input type="text" name="txtBtElmAvg" id="txtBtElmAvg" class="text_boxes_numeric" style="width:45px" readonly/>
							</td>
							<td width="65">&nbsp;</td>
							<td><input type="text" name="txtTotAvgBt" id="txtTotAvgBt" class="text_boxes_numeric" style="width:65px" readonly/></td>
						</tr>
					<tr bgcolor="#FFFFFF" align="center">
							<td width="170" align="left" colspan="3"><b>Personal Allowance (%)&nbsp;</b></td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">
								<input type="text" name="txtRelxPerc" id="txtRelxPerc" class="text_boxes_numeric" value="<? echo $txtRelxPerc; ?>" onKeyUp="calculate_allowance()" style="width:50px"/>
							</td>
							<td><input type="text" name="txtRelxPercBt" id="txtRelxPercBt" class="text_boxes_numeric" style="width:65px" readonly/></td>
						</tr>
						<tr bgcolor="#E9F3FF" align="center">
							<td width="170" align="left" colspan="3"><b>Machine Allowance (%)&nbsp;</b></td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">
								<input type="text" name="txtMachinePerc" id="txtMachinePerc" class="text_boxes_numeric" value="<? echo $txtMachinePerc; ?>" onKeyUp="calculate_allowance();" style="width:50px"/>
							</td>
							<td><input type="text" name="txtMachinePercBt" id="txtMachinePercBt" class="text_boxes_numeric" style="width:65px" readonly/></td>
						</tr>
						<tr bgcolor="#FFFFFF" align="center">
							<td width="170" align="left" colspan="3"><b>Fatic Allowance (%)&nbsp;</b></td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">&nbsp;</td>
							<td width="60">
								<input type="text" name="txtAlterPerc" id="txtAlterPerc" class="text_boxes_numeric" value="<? echo $txtAlterPerc; ?>" onKeyUp="calculate_allowance();" style="width:50px"/>
							</td>
							<td><input type="text" name="txtAlterPercBt" id="txtAlterPercBt" class="text_boxes_numeric" style="width:65px" readonly/></td>
						</tr>
						<tr bgcolor="#FFFFFF" align="center">
							<td align="left" colspan="14"><b>G. Total Avg. BT + Personal Allowance + Machine Allowance + Fatic Allowance&nbsp;</b></td>
							<td><input type="text" name="txtGtot" id="txtGtot" class="text_boxes_numeric" style="width:65px" readonly/></td>
						</tr>
					</table>
				</div>
				<table width="920">
					<tr>
						<td width="200" title=": {(Avg basic time + Personal allowance% + Machine allowance% + Fatigue allowance %) /60 Minute)}"><b>SMV- <span id="smv"></span></b></td>
						<td width="200" title="Target/HR: 60/SMV"><b>Target/HR- <span  id="target_per_hour"></span></b></td>
						<td align="left">
							<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		<?
		if(str_replace("'", '',$prev_data)!="")
		{
		?>
			calculate_tot();
			calculate_allowance();
		<?
		}
		?>
	</script>
	</html>
	<?
	exit();
}

if ($action=="sewing_operation_list_view")
{
	$data=explode("__", $data);
	//$arr=array(0=>$product_dept,1=>$garments_item,2=>$body_part,5=>$smv_basis,7=>$production_resource,10=>$machine_category);
	//echo create_list_view ( "list_view", "Product Dept., Garments Item, Body Part,Code,Operation, SMV Basis, Seam Length,Resources,Machine SMV,Manual SMV, Department Code", "80,115,80,90,90,60,75,70,75,70","950","220",1, "select product_dept,gmt_item_id, bodypart_id,code,operation_name,smv_basis,seam_length, resource_sewing, operator_smv, helper_smv, department_code, id from lib_sewing_operation_entry where is_deleted=0 order by id Desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "product_dept,gmt_item_id,bodypart_id,0,0,smv_basis,0,resource_sewing,0,0,department_code", $arr , "product_dept,gmt_item_id,bodypart_id,code,operation_name,smv_basis,seam_length,resource_sewing,operator_smv,helper_smv,department_code","requires/sewing_operation_controller",'','0,0,0,0,0,0,0,0,2,2,0');

	$libSql="select PROCESS_ID,RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 order by RESOURCE_NAME";
	$libSqlRes = sql_select($libSql);
	foreach ($libSqlRes as $rows) {
		$production_resource_arr[$rows['PROCESS_ID']][$rows['RESOURCE_ID']]=$rows['RESOURCE_NAME'];
	}


	//$production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 order by RESOURCE_NAME",'RESOURCE_ID','RESOURCE_NAME');


	$sql_cond='';
	if($data[0]>0) $sql_cond=" and product_dept='".$data[0]."'";
	if($data[1]>0) $sql_cond.=" and gmt_item_id='".$data[1]."'";
	if($data[2]>0) $sql_cond.=" and bodypart_id='".$data[2]."'";
	if($data[3]>0) $sql_cond.=" and resource_sewing='".$data[3]."'";
	$sql_cond .= " and company_id=".$data[4]."";


	
	
	$sql="select product_dept,gmt_item_id, ope_grade, bodypart_id,code,operation_name,smv_basis,seam_length, resource_sewing, operator_smv, helper_smv, DEPARTMENT_CODE, id from lib_sewing_operation_entry where is_deleted=0 $sql_cond order by id Desc";
	 //echo $sql; 
	$result = sql_select($sql);
	?>
	<div style="width: 1420px;">
	<table align="left" cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="85">Product Dept.<br><? echo create_drop_down( "productDept", 80, $product_dept,'', 1,"-- Select --",$data[0],'show_operation(2);' ); ?></th>
			<th width="110">Garments Item<br><? asort($garments_item); echo create_drop_down( "garmentItem", 105, $garments_item,'', 1,"-- Select --",$data[1],'show_operation(2);' ); ?></th>
			<th width="85">Body Part<br>
			<? 
				$sql_bpart="select a.body_part_full_name,b.mst_id,b.entry_page_id from lib_body_part_tag_entry_page b, lib_body_part a where a.id=b.mst_id and b.status_active=1 and a.status=1  and b.is_deleted=0  and a.is_deleted=0";
						$sql_result=sql_select($sql_bpart);
						foreach ($sql_result as $value) 
						{
							if($value[csf("entry_page_id")]==148)
							{
								$tag_body_part_arr[$value[csf("mst_id")]]=$value[csf("body_part_full_name")];
							}
								$all_body_part_arr[$value[csf("mst_id")]]=$value[csf("body_part_full_name")];
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
					    asort($body_partArr);
			asort($body_part); echo create_drop_down( "bodypart", 80, $body_partArr,'', 1, "-- Select --",$data[2],'show_operation(2);','','' ); 
			//asort($body_part); echo create_drop_down( "bodypart", 80, $body_part,'', 1, "-- Select --",$data[2],'show_operation(2);','','2,3,6,7,9,10,11,26,28,40,53,59,60,63,79,92,106,196,197,314' ); 
			?>
            </th>               
			<th width="80">Code</th>
			<th width="120">Operation</th>
			<th width="120">Ope. Grade</th>
			<th width="75">SMV Basis</th>
			<th width="75">Seam Length</th>
            <th width="85">Resources<br>
			<span id="resource_td">
				<? 
					echo create_drop_down( "resource", 75, "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID=8 order by RESOURCE_NAME","RESOURCE_ID,RESOURCE_NAME", 1, "-- Select --", $selected, $data[3],'show_operation(2);' );
				?>
			</span>
		</th>
			<th width="100">Resource Customize</th>
            <th width="75">Machine SMV</th>
            <th width="70">Manual SMV</th>
			<th>Department Code</th>
		</thead>
	</table>
	<div style="width:1420px; max-height:220px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table align="left" cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table" id="list_view">  
		<?
		    $operations_grade = [1=>'H-1', 2=>'H-2', 3=>'P', 4=>'Q', 5=>'R', 6=>'S'];
			$i=1;
			foreach ($result as $row)
			{  
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	 
			?>
				 <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $row[csf('id')]; ?>,'load_php_data_to_form', 'requires/sewing_operation_controller');">  
					<td width="30"><? echo $i; ?></td>
					<td width="85"><p><? echo $product_dept[$row[csf('product_dept')]]; ?></p></td>
					<td width="110"><p><? echo $garments_item[$row[csf('gmt_item_id')]]; ?></p></td>  
					<td width="85"><p><? echo $body_part[$row[csf('bodypart_id')]]; ?></p></td>             
					<td width="80"><p><? echo $row[csf('code')]; ?></p></td>
					<td width="120"><p><? echo $row[csf('operation_name')]; ?></p></td>
					<td width="120"><p><? echo $operations_grade[$row[csf('ope_grade')]]; ?></p></td>
					<td width="75"><p><? echo $smv_basis[$row[csf('smv_basis')]]; ?></p></td>
					<td width="75" align="right"><? echo $row[csf('seam_length')]; ?></td>
                    <td width="85"><p><? echo $production_resource[$row[csf('resource_sewing')]]; ?></p></td>
                    <td width="100"><p><? echo $production_resource_arr[$row[csf('DEPARTMENT_CODE')]][$row[csf('resource_sewing')]]; ?></p></td>
                    <td width="75" align="right"><? echo number_format($row[csf('operator_smv')],2); ?></td>
                    <td width="70" align="right"><? echo number_format($row[csf('helper_smv')],2); ?></td>
					<td><p><? echo $machine_category[$row[csf('department_code')]]; ?></p></td>
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

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "SELECT id, company_id,operation_name, ope_grade, rate, uom, resource_sewing, operator_smv, helper_smv, product_dept, bodypart_id, gmt_item_id, product_code, gmts_code, body_part_code, code_prefix, code, fabric_type, smv_basis, seam_length, DEPARTMENT_CODE, observer_name, checked_by, avg_bt_pick, avg_bt_ex, avg_bt_dis, avg_bt_elm, tot_avg_bt, alter_perc, machine_perc, relaxation_perc, alter_avg_bt, machine_avg_bt, relaxation_avg_bt, grand_tot_avg_bt, status_active ,is_qc,sequence_no  from lib_sewing_operation_entry where id='$data'" );

	// echo $nameArray

	$production_resource_arr=return_library_array( "select RESOURCE_ID,RESOURCE_NAME from LIB_OPERATION_RESOURCE where is_deleted=0  and status_active=1 and PROCESS_ID={$nameArray[0]['DEPARTMENT_CODE']}  order by RESOURCE_NAME", "RESOURCE_ID","RESOURCE_NAME"  );
	foreach ($nameArray as $inf)
	{
		if($inf[csf("is_qc")]==1)
		{
			echo "$('#chk_qc_config').attr('checked',true);\n";
		}
		else
		{
			echo "$('#chk_qc_config').attr('checked',false);\n";
		}
		echo "document.getElementById('cbo_company_id').value  = '".($inf[csf("company_id")])."';\n";
		echo "document.getElementById('cbo_product_dept').value  = '".($inf[csf("product_dept")])."';\n";
		echo "document.getElementById('cbo_garment_item').value  = '".($inf[csf("gmt_item_id")])."';\n";
		echo "document.getElementById('cbo_bodypart').value  = '".($inf[csf("bodypart_id")])."';\n";
		echo "document.getElementById('txt_product_code').value  = '".($inf[csf("product_code")])."';\n";
		echo "document.getElementById('txt_gmts_code').value  = '".($inf[csf("gmts_code")])."';\n";
		echo "document.getElementById('txt_body_part_code').value  = '".($inf[csf("body_part_code")])."';\n";
		echo "document.getElementById('txt_code').value  = '".($inf[csf("code_prefix")])."';\n";
		echo "document.getElementById('txt_operation').value = '".($inf[csf("operation_name")])."';\n";    
		echo "document.getElementById('txt_rate').value  = '".($inf[csf("rate")])."';\n"; 
		echo "document.getElementById('cbo_uom').value  = '".($inf[csf("uom")])."';\n";
		echo "document.getElementById('cbo_resource').value  = '".($inf[csf("resource_sewing")])."';\n";
		echo "document.getElementById('cbo_smv_basis').value  = '".($inf[csf("smv_basis")])."';\n";
		
		echo "document.getElementById('cbo_ope_grade').value  = '".($inf[csf("ope_grade")])."';\n";
		echo "document.getElementById('txt_sequence').value  = '".($inf[csf("sequence_no")])."';\n";

		echo "document.getElementById('cbo_garment_item_view').value  = '".($garments_item[$inf[csf("gmt_item_id")]])."';\n";
		echo "document.getElementById('cbo_bodypart_view').value  = '".($body_part[$inf[csf("bodypart_id")]])."';\n";
		echo "document.getElementById('cbo_resource_view').value  = '".($production_resource_arr[$inf[csf("resource_sewing")]])."';\n";
		
		
		
		echo "fnc_smv_active();\n";
		
		echo "document.getElementById('txt_operator_smv').value  = '".($inf[csf("operator_smv")])."';\n";
		echo "document.getElementById('txt_helper_smv').value  = '".($inf[csf("helper_smv")])."';\n";
		
		$comp='';
		if($inf[csf('fabric_type')]>0)
		{
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$inf[csf('fabric_type')]);
			if($determination_sql[0][csf('construction')]!="")
			{
				$comp=$determination_sql[0][csf('construction')].", ";
			}
			
			foreach( $determination_sql as $d_row )
			{
				$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
			}
		}
		
		$target_per_hour='';
		$smv_data=$inf[csf("observer_name")]."_".$inf[csf("checked_by")]."_".$target_per_hour."_".$inf[csf("avg_bt_pick")]."_".$inf[csf("avg_bt_ex")]."_".$inf[csf("avg_bt_dis")]."_".$inf[csf("tot_avg_bt")]."_".$inf[csf("alter_perc")]."_".$inf[csf("machine_perc")]."_".$inf[csf("relaxation_perc")]."_".$inf[csf("alter_avg_bt")]."_".$inf[csf("machine_avg_bt")]."_".$inf[csf("relaxation_avg_bt")]."_".$inf[csf("grand_tot_avg_bt")]."_".$inf[csf("avg_bt_elm")]; 
		
		$dtls_data='';
		if($inf[csf("smv_basis")]==2)
		{
			$data_array=sql_select("select or_pick, ot_pick, bt_pick, or_ex, ot_ex, bt_ex, or_dis, ot_dis, bt_dis, or_elm, ot_elm, bt_elm from lib_sewing_smv_dtls where mst_id='$data' and status_active=1 and is_deleted=0 order by id");
			foreach($data_array as $row)
			{ 
				if($dtls_data=="")
				{
					$dtls_data=$row[csf("or_pick")]."_".$row[csf("ot_pick")]."_".$row[csf("bt_pick")]."_".$row[csf("or_ex")]."_".$row[csf("ot_ex")]."_".$row[csf("bt_ex")]."_".$row[csf("or_dis")]."_".$row[csf("ot_dis")]."_".$row[csf("bt_dis")]."_".$row[csf("or_elm")]."_".$row[csf("ot_elm")]."_".$row[csf("bt_elm")];
				}
				else
				{
					$dtls_data.="|".$row[csf("or_pick")]."_".$row[csf("ot_pick")]."_".$row[csf("bt_pick")]."_".$row[csf("or_ex")]."_".$row[csf("ot_ex")]."_".$row[csf("bt_ex")]."_".$row[csf("or_dis")]."_".$row[csf("ot_dis")]."_".$row[csf("bt_dis")]."_".$row[csf("or_elm")]."_".$row[csf("ot_elm")]."_".$row[csf("bt_elm")];
				}
			}
		}
		
		if($inf[csf("resource_sewing")]==40 || $inf[csf("resource_sewing")]==41 || $inf[csf("resource_sewing")]==43 || $inf[csf("resource_sewing")]==44 || $inf[csf("resource_sewing")]==48 || $inf[csf("resource_sewing")]==53 || $inf[csf("resource_sewing")]==54 || $inf[csf("resource_sewing")]==55 || $inf[csf("resource_sewing")]==56 || $inf[csf("resource_sewing")]==68 || $inf[csf("resource_sewing")]==69 || $inf[csf("resource_sewing")]==70 || $inf[csf("resource_sewing")]==90 || $inf[csf("resource_sewing")]==147 || $inf[csf("resource_sewing")]==176 )
		{
			echo "document.getElementById('smv_data_helper').value 		= '".$dtls_data."';\n";
			echo "document.getElementById('smv_data_operator').value 	= '';\n";
		}
		else
		{
			echo "document.getElementById('smv_data_operator').value 	= '".$dtls_data."';\n";
			echo "document.getElementById('smv_data_helper').value 		= '';\n";
		}
		
		echo "document.getElementById('txt_fabric_description').value 		= '".$comp."';\n";
		echo "document.getElementById('fabric_desc_id').value  = '".($inf[csf("fabric_type")])."';\n";
		echo "document.getElementById('txt_seam_length').value  = '".($inf[csf("seam_length")])."';\n";
		echo "document.getElementById('cbo_department_code').value  = '".($inf[csf("department_code")])."';\n";
		echo "document.getElementById('smv_data').value  = '".$smv_data."';\n";
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('cbo_ope_grade').value  = '".($inf[csf("ope_grade")])."';\n";
		
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sewing_operation_entry',1);\n";  
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0)
		{
			$null_check="IFNULL";
		}
		else 
		{
			$null_check="NVL";
		}
		
		if(str_replace("'","",$txt_seam_length)=="") $seam_length=0; else $seam_length=str_replace("'","",$txt_seam_length);
		
		if (is_duplicate_field( "operation_name", "lib_sewing_operation_entry", "company_id=$cbo_company_id and gmt_item_id=$cbo_garment_item and bodypart_id=$cbo_bodypart and operation_name=$txt_operation and resource_sewing=$cbo_resource and $null_check(seam_length,0)=$seam_length and product_dept=$cbo_product_dept and is_deleted=0" ) == 1)
		{
			echo "11**0"; disconnect($con); die;
			//issue id :144; add product_dept for duplicate check 
		}
		else
		{
			$save_data=explode("_",str_replace("'","",$smv_data));
			$txt_observer_name=$save_data[0];
			$txt_checked_by=$save_data[1];
			$target_per_hour=$save_data[2];
			$txtBtPickAvg=$save_data[3];
			$txtBtExAvg=$save_data[4];
			$txtBtDisAvg=$save_data[5];
			$txtTotAvgBt=$save_data[6];
			$txtAlterPerc=$save_data[7];
			$txtMachinePerc=$save_data[8];
			$txtRelxPerc=$save_data[9];
			$txtAlterPercBt=$save_data[10];
			$txtMachinePercBt=$save_data[11];
			$txtRelxPercBt=$save_data[12];
			$txtGtot=$save_data[13];
			$txtBtElmAvg=$save_data[14];
			
			if(str_replace("'","",$txt_product_code)!="" || $db_type==0)
			{
				$product_code_cond="product_code=$txt_product_code";
			}
			else 
			{
				$product_code_cond="product_code is null";
			}
			
			if(str_replace("'","",$txt_gmts_code)!="" || $db_type==0)
			{
				$gmts_code_cond="gmts_code=$txt_gmts_code";
			}
			else 
			{
				$gmts_code_cond="gmts_code is null";
			}
			
			if(str_replace("'","",$txt_body_part_code)!="" || $db_type==0)
			{
				$body_part_code_cond="body_part_code=$txt_body_part_code";
			}
			else 
			{
				$body_part_code_cond="body_part_code is null";
			}
			
			//$code_no=return_field_value("code_prefix","lib_sewing_operation_entry","$product_code_cond and $gmts_code_cond and $body_part_code_cond")+1;
			$code_no=return_next_id( "code_prefix", "lib_sewing_operation_entry where $product_code_cond and $gmts_code_cond and $body_part_code_cond", 1 ) ;

			$code=strtoupper(str_replace("'","",$txt_product_code)).'-'.strtoupper(str_replace("'","",$txt_gmts_code)).'-'.strtoupper(str_replace("'","",$txt_body_part_code)).'-'.$code_no;
			// chk_qc_config
		 
			$id=return_next_id( "id", "lib_sewing_operation_entry", 1 ) ;
			$field_array="id, company_id,is_qc, ope_grade,sequence_no , operation_name, rate, uom, resource_sewing, operator_smv, helper_smv, product_dept, bodypart_id, gmt_item_id, product_code, gmts_code, body_part_code, code_prefix, code, fabric_type, smv_basis, seam_length, department_code, observer_name, checked_by, avg_bt_pick, avg_bt_ex, avg_bt_dis, avg_bt_elm, tot_avg_bt, alter_perc, machine_perc, relaxation_perc, alter_avg_bt, machine_avg_bt, relaxation_avg_bt, grand_tot_avg_bt, inserted_by,  insert_date";
			$data_array="(".$id.",".$cbo_company_id.",".$chk_qc_config.",".$cbo_ope_grade.",".$txt_sequence.",".$txt_operation.",".$txt_rate.",".$cbo_uom.",".$cbo_resource.",".$txt_operator_smv.",".$txt_helper_smv.",".$cbo_product_dept.",".$cbo_bodypart.",".$cbo_garment_item.",".$txt_product_code.",".$txt_gmts_code.",".$txt_body_part_code.",'".$code_no."','".$code."',".$fabric_desc_id.",".$cbo_smv_basis.",".$txt_seam_length.",".$cbo_department_code.",'".$txt_observer_name."','".$txt_checked_by."','".$txtBtPickAvg."','".$txtBtExAvg."','".$txtBtDisAvg."','".$txtBtElmAvg."','".$txtTotAvgBt."','".$txtAlterPerc."','".$txtMachinePerc."','".$txtRelxPerc."','".$txtAlterPercBt."','".$txtMachinePercBt."','".$txtRelxPercBt."','".$txtGtot."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if(str_replace("'","",$cbo_resource)==40 || str_replace("'","",$cbo_resource)==41 || str_replace("'","",$cbo_resource)==43 || str_replace("'","",$cbo_resource)==44 || str_replace("'","",$cbo_resource)==48 || str_replace("'","",$cbo_resource)==53 || str_replace("'","",$cbo_resource)==54 || str_replace("'","",$cbo_resource)==55 || str_replace("'","",$cbo_resource)==56 || str_replace("'","",$cbo_resource)==68 || str_replace("'","",$cbo_resource)==69 || str_replace("'","",$cbo_resource)==70 || str_replace("'","",$cbo_resource)==90 || str_replace("'","",$cbo_resource)==147 || str_replace("'","",$cbo_resource)==176)
			{
				$dtls_data=str_replace("'","",$smv_data_helper);
			}
			else
			{
				$dtls_data=str_replace("'","",$smv_data_operator);
			}
			
			if(str_replace("'","",$cbo_smv_basis)==2)
			{
				$id_dtls=return_next_id( "id", "lib_sewing_smv_dtls", 1 ) ;
				$field_array_dtls="id, mst_id, or_pick, ot_pick, bt_pick, or_ex, ot_ex, bt_ex, or_dis, ot_dis, bt_dis, or_elm, ot_elm, bt_elm, inserted_by, insert_date";
				
				$dtlsDatas=explode("|",str_replace("'", '',$dtls_data));
				foreach($dtlsDatas as $value)
				{
					$smv_val=explode('_',$value);
					$txtOrPick = $smv_val[0];
					$txtOtPick = $smv_val[1];
					$txtBtPick = $smv_val[2];
					$txtOrEx = $smv_val[3];
					$txtOtEx = $smv_val[4];
					$txtBtEx = $smv_val[5];
					$txtOrDis = $smv_val[6];
					$txtOtDis = $smv_val[7];
					$txtBtDis = $smv_val[8];
					$txtOrElm = $smv_val[9];
					$txtOtElm = $smv_val[10];
					$txtBtElm = $smv_val[11];
					
					if($data_array_dtls!="") $data_array_dtls.=","; 	
					$data_array_dtls.="(".$id_dtls.",".$id.",'".$txtOrPick."','".$txtOtPick."','".$txtBtPick."','".$txtOrEx."','".$txtOtEx."','".$txtBtEx."','".$txtOrDis."','".$txtOtDis."','".$txtBtDis."','".$txtOrElm."','".$txtOtElm."','".$txtBtElm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id_dtls=$id_dtls+1;
				}
			}
			
			//echo "10**insert into lib_sewing_operation_entry (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("lib_sewing_operation_entry",$field_array,$data_array,1);
			$rID2=true;
			if($data_array_dtls!="")
			{
				//echo "10**insert into lib_sewing_smv_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
				$rID2=sql_insert("lib_sewing_smv_dtls",$field_array_dtls,$data_array_dtls,1);
			}
			//oci_rollback($con);
			//echo "10**".$rID."&&".$rID2;die;
			if($db_type==0)
			{
				if($rID && $rID2){
					mysql_query("COMMIT");  
					echo "0**".$id;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "5**".$id;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID2)
			    {
					oci_commit($con);   
					echo "0**".$id;
				}
				else{
					oci_rollback($con);
					echo "5**".$id;
				}
			}
			disconnect($con);
			die;
		}
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0)
		{
			$null_check="IFNULL";
		}
		else 
		{
			$null_check="NVL";
		}
		
		if(str_replace("'","",$txt_seam_length)=="") $seam_length=0; else $seam_length=str_replace("'","",$txt_seam_length);
		
		if (is_duplicate_field( "operation_name", "lib_sewing_operation_entry", "company_id=$cbo_company_id and gmt_item_id=$cbo_garment_item and bodypart_id=$cbo_bodypart and operation_name=$txt_operation and resource_sewing=$cbo_resource and $null_check(seam_length,0)=$seam_length  and product_dept=$cbo_product_dept and is_deleted=0 and id!=".$update_id."" ) == 1)
		{
			echo "11**0"; disconnect($con); die;
		}
		else
		{
			$field_array="operation_name*rate*uom*resource_sewing*operator_smv*helper_smv*total_smv*bodypart_id*gmt_item_id*updated_by*update_date*status_active*is_deleted";
			$data_array="".$txt_operation."*".$txt_rate."*".$cbo_uom."*".$cbo_resource."*".$txt_operator_smv."*".$txt_helper_smv."*".$txt_total_smv."*".$cbo_bodypart."*".$cbo_garment_item."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";
			$save_data=explode("_",str_replace("'","",$smv_data));
			$txt_observer_name=$save_data[0];
			$txt_checked_by=$save_data[1];
			$target_per_hour=$save_data[2];
			$txtBtPickAvg=$save_data[3];
			$txtBtExAvg=$save_data[4];
			$txtBtDisAvg=$save_data[5];
			$txtTotAvgBt=$save_data[6];
			$txtAlterPerc=$save_data[7];
			$txtMachinePerc=$save_data[8];
			$txtRelxPerc=$save_data[9];
			$txtAlterPercBt=$save_data[10];
			$txtMachinePercBt=$save_data[11];
			$txtRelxPercBt=$save_data[12];
			$txtGtot=$save_data[13];
			$txtBtElmAvg=$save_data[14];
			
			$prevData=sql_select("select product_code, gmts_code, body_part_code from lib_sewing_operation_entry where id=$update_id");
			
			if($prevData[0][csf('product_code')]==str_replace("'","",$txt_product_code) && $prevData[0][csf('gmts_code')]==str_replace("'","",$txt_gmts_code) && $prevData[0][csf('body_part_code')]==str_replace("'","",$txt_body_part_code))
			{
				$code_no=str_replace("'","",$txt_code);
				$code=strtoupper(str_replace("'","",$txt_product_code)).'-'.strtoupper(str_replace("'","",$txt_gmts_code)).'-'.strtoupper(str_replace("'","",$txt_body_part_code)).'-'.$code_no;
			}
			else
			{
				if(str_replace("'","",$txt_product_code)!="" || $db_type==0)
				{
					$product_code_cond="product_code=$txt_product_code";
				}
				else 
				{
					$product_code_cond="product_code is null";
				}
				
				if(str_replace("'","",$txt_gmts_code)!="" || $db_type==0)
				{
					$gmts_code_cond="gmts_code=$txt_gmts_code";
				}
				else 
				{
					$gmts_code_cond="gmts_code is null";
				}
				
				if(str_replace("'","",$txt_body_part_code)!="" || $db_type==0)
				{
					$body_part_code_cond="body_part_code=$txt_body_part_code";
				}
				else 
				{
					$body_part_code_cond="body_part_code is null";
				}
				
				//$code_no=return_field_value("code_prefix","lib_sewing_operation_entry","$product_code_cond and $gmts_code_cond and $body_part_code_cond")+1;
				
				$code_no=return_next_id( "code_prefix", "lib_sewing_operation_entry where $product_code_cond and $gmts_code_cond and $body_part_code_cond", 1 ) ;
				
				$code=strtoupper(str_replace("'","",$txt_product_code)).'-'.strtoupper(str_replace("'","",$txt_gmts_code)).'-'.strtoupper(str_replace("'","",$txt_body_part_code)).'-'.$code_no;
			}
			
			$field_array="company_id*operation_name*rate*uom*resource_sewing*operator_smv*helper_smv*product_dept*bodypart_id*gmt_item_id*product_code*gmts_code*body_part_code*code_prefix*code*fabric_type*smv_basis*seam_length*department_code*observer_name*checked_by*avg_bt_pick*avg_bt_ex*avg_bt_dis*avg_bt_elm*tot_avg_bt*alter_perc*machine_perc*relaxation_perc*alter_avg_bt*machine_avg_bt*relaxation_avg_bt*grand_tot_avg_bt*updated_by*update_date*status_active*is_qc*ope_grade*sequence_no";

			// print_r($update_id);die;

			$data_array=$cbo_company_id."*".$txt_operation."*".$txt_rate."*".$cbo_uom."*".$cbo_resource."*".$txt_operator_smv."*".$txt_helper_smv."*".$cbo_product_dept."*".$cbo_bodypart."*".$cbo_garment_item."*".$txt_product_code."*".$txt_gmts_code."*".$txt_body_part_code."*'".$code_no."'*'".$code."'*".$fabric_desc_id."*".$cbo_smv_basis."*".$txt_seam_length."*".$cbo_department_code."*'".$txt_observer_name."'*'".$txt_checked_by."'*'".$txtBtPickAvg."'*'".$txtBtExAvg."'*'".$txtBtDisAvg."'*'".$txtBtElmAvg."'*'".$txtTotAvgBt."'*'".$txtAlterPerc."'*'".$txtMachinePerc."'*'".$txtRelxPerc."'*'".$txtAlterPercBt."'*'".$txtMachinePercBt."'*'".$txtRelxPercBt."'*'".$txtGtot."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*".$chk_qc_config."*".$cbo_ope_grade."*".$txt_sequence."";
			
			// echo $field_array;die;

			//if(str_replace("'","",$cbo_resource)==40)
			if(str_replace("'","",$cbo_resource)==40 || str_replace("'","",$cbo_resource)==41 || str_replace("'","",$cbo_resource)==43 || str_replace("'","",$cbo_resource)==44 || str_replace("'","",$cbo_resource)==48 || str_replace("'","",$cbo_resource)==53 || str_replace("'","",$cbo_resource)==54 || str_replace("'","",$cbo_resource)==55 || str_replace("'","",$cbo_resource)==56 || str_replace("'","",$cbo_resource)==68 || str_replace("'","",$cbo_resource)==69 || str_replace("'","",$cbo_resource)==70 || str_replace("'","",$cbo_resource)==90 || str_replace("'","",$cbo_resource)==147 || str_replace("'","",$cbo_resource)==176)
			{
				$dtls_data=str_replace("'","",$smv_data_helper);
			}
			else
			{
				$dtls_data=str_replace("'","",$smv_data_operator);
			}
			
			if(str_replace("'","",$cbo_smv_basis)==2)
			{
				$id_dtls=return_next_id( "id", "lib_sewing_smv_dtls", 1 ) ;
				$field_array_dtls="id, mst_id, or_pick, ot_pick, bt_pick, or_ex, ot_ex, bt_ex, or_dis, ot_dis, bt_dis, or_elm, ot_elm, bt_elm, inserted_by, insert_date";
				
				$dtlsDatas=explode("|",str_replace("'", '',$dtls_data));
				foreach($dtlsDatas as $value)
				{
					$smv_val=explode('_',$value);
					$txtOrPick = $smv_val[0];
					$txtOtPick = $smv_val[1];
					$txtBtPick = $smv_val[2];
					$txtOrEx = $smv_val[3];
					$txtOtEx = $smv_val[4];
					$txtBtEx = $smv_val[5];
					$txtOrDis = $smv_val[6];
					$txtOtDis = $smv_val[7];
					$txtBtDis = $smv_val[8];
					$txtOrElm = $smv_val[9];
					$txtOtElm = $smv_val[10];
					$txtBtElm = $smv_val[11];
					
					if($data_array_dtls!="") $data_array_dtls.=","; 	
					$data_array_dtls.="(".$id_dtls.",".$update_id.",'".$txtOrPick."','".$txtOtPick."','".$txtBtPick."','".$txtOrEx."','".$txtOtEx."','".$txtBtEx."','".$txtOrDis."','".$txtOtDis."','".$txtBtDis."','".$txtOrElm."','".$txtOtElm."','".$txtBtElm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id_dtls=$id_dtls+1;
				}
			}
			
			$rID=sql_update("lib_sewing_operation_entry",$field_array,$data_array,"id",$update_id,1);
			$rID2=execute_query( "delete from lib_sewing_smv_dtls where mst_id=$update_id",0);
			$rID3=true;
			if($data_array_dtls!="")
			{
				//echo "10**insert into lib_sewing_smv_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
				$rID3=sql_insert("lib_sewing_smv_dtls",$field_array_dtls,$data_array_dtls,1);
			}
			//oci_rollback($con);
			//echo "10**".$rID."&&".$rID2."&&".$rID3;die;
			if($db_type==0)
			{
				if($rID && $rID2 && $rID3)
				{
					mysql_query("COMMIT");  
					echo "1**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID2 && $rID3)
			    {
					oci_commit($con);   
					echo "1**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
	else if ($operation==2)   // Delete Here
	{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$used_operation_id=return_field_value("lib_sewing_id","ppl_gsd_entry_dtls","lib_sewing_id=$update_id");
		
		if($used_operation_id==''){
			$rID=execute_query( "delete from lib_sewing_operation_entry where id=$update_id",0);
			$rID2=execute_query( "delete from lib_sewing_smv_dtls where mst_id=$update_id",0);
		}
		
		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "2**".$rID."**".$used_operation_id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "13**".$rID."**".$used_operation_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);   
				echo "2**".$rID."**".$used_operation_id;
			}
			else{
				oci_rollback($con);
				echo "13**".$rID."**".$used_operation_id;
			}
		}
		disconnect($con);
		die;
		
	}
}

if($action=="time_study_sheet_print")
{
	$dataArray=sql_select("select operation_name, resource_sewing, operator_smv, helper_smv, bodypart_id, gmt_item_id, fabric_type, smv_basis, seam_length, department_code, observer_name, checked_by, avg_bt_pick, avg_bt_ex, avg_bt_dis, avg_bt_elm, tot_avg_bt, alter_perc, machine_perc, relaxation_perc, alter_avg_bt, machine_avg_bt, relaxation_avg_bt, grand_tot_avg_bt from lib_sewing_operation_entry where id=$data");
	
	$smv='';
	//if($dataArray[0][csf('resource_sewing')]==40)
	if($dataArray[0][csf('resource_sewing')]==40 || $dataArray[0][csf('resource_sewing')]==41 || $dataArray[0][csf('resource_sewing')]==43 || $dataArray[0][csf('resource_sewing')]==44 || $dataArray[0][csf('resource_sewing')]==48 || $dataArray[0][csf('resource_sewing')]==53 || $dataArray[0][csf('resource_sewing')]==54 || $dataArray[0][csf('resource_sewing')]==55 || $dataArray[0][csf('resource_sewing')]==56 || $dataArray[0][csf('resource_sewing')]==68 || $dataArray[0][csf('resource_sewing')]==69 || $dataArray[0][csf('resource_sewing')]==70 || $dataArray[0][csf('resource_sewing')]==90 || $dataArray[0][csf('resource_sewing')]==147 || $dataArray[0][csf('resource_sewing')]==176 )
	{
		$smv=$dataArray[0][csf('helper_smv')];
	}
	else
	{
		$smv=$dataArray[0][csf('operator_smv')];
	}
	
	$tph=60/$smv;
	
	$comp='';
	if($dataArray[0][csf('fabric_type')]>0)
	{
		$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$dataArray[0][csf('fabric_type')]);
		if($determination_sql[0][csf('construction')]!="")
		{
			$comp=$determination_sql[0][csf('construction')].", ";
		}
		
		foreach( $determination_sql as $d_row )
		{
			$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
		}
	}
	?>
    <div style="width:940px">
        <div style="width:940px" align="center">&nbsp;&nbsp;<b><u>Time Study Sheet</u></b></div>
        <table style="margin-top:5px;" width="940" border="1" rules="all" cellpadding="0" cellspacing="0">
            <tr>
            	<th width="130" align="left">Operation Name</th><td width="170" align="left"><? echo $dataArray[0][csf('operation_name')]; ?></td>
                <th width="110" align="left">Gmts. Item</th><td width="170" align="left"><? echo $garments_item[$dataArray[0][csf('gmt_item_id')]]; ?></td>
                <th width="110" align="left">Body Part</th><td align="left"><? echo $body_part[$dataArray[0][csf('bodypart_id')]]; ?></td>
            </tr>
            <tr>
            	<th align="left">Machine Type</th><td><? echo $production_resource[$dataArray[0][csf('resource_sewing')]]; ?></td>
                <th align="left">Seam Length</th><td><? echo $dataArray[0][csf('seam_length')]; ?></td>
                <th align="left">Fabric Type</th><td><? echo $comp; ?></td>
            </tr>
            <tr>  
            	<th align="left">Observer Name</th><td><? echo $dataArray[0][csf('observer_name')]; ?></td>
                <th align="left">Checked By</th><td colspan="3"><? echo $dataArray[0][csf('checked_by')]; ?></td>
            </tr>
        </table>
        <table class="rpt_table" style="margin-top:10px;" border="1" cellpadding="0" cellspacing="0" rules="all" width="940">
            <thead>
                <tr>
                    <th width="70" rowspan="2">Element/<br /> Cycle No</th>
                    <th width="190" colspan="3">Element-1</th>
                    <th width="190" colspan="3">Element-2</th>
                    <th width="190" colspan="3">Element-3</th>
                    <th width="190" colspan="3">Element-4</th>
                    <th rowspan="2"></th>
                </tr>
                <tr>
                    <th width="60">OR %</th>
                    <th width="65">OT</th>
                    <th width="65">BT</th>
                    <th width="60">OR %</th>
                    <th width="65">OT</th>
                    <th width="65">BT</th>
                    <th width="60">OR %</th>
                    <th width="65">OT</th>
                    <th width="65">BT</th>
                    <th width="60">OR %</th>
                    <th width="65">OT</th>
                    <th width="65">BT</th>
                </tr>
            </thead>
            <?
				$i=0; $tot_ot_pick=0; $tot_bt_pick=0; $tot_ot_ex=0; $tot_bt_ex=0; $tot_ot_dis=0; $tot_bt_dis=0; $tot_ot_elm=0; $tot_bt_elm=0;
				$data_array=sql_select("select or_pick, ot_pick, bt_pick, or_ex, ot_ex, bt_ex, or_dis, ot_dis, bt_dis, or_elm, ot_elm, bt_elm from lib_sewing_smv_dtls where mst_id='$data' and status_active=1 and is_deleted=0 order by id");
				foreach($data_array as $row)
				{
					$i++; 
				?>
                	<tr>
                    	<td><? echo $i; ?></td>
                        <td align="right"><? echo $row[csf('or_pick')]; ?></td>
                        <td align="right"><? echo $row[csf('ot_pick')]; ?></td>
                        <td align="right"><? echo $row[csf('bt_pick')]; ?></td>
                        <td align="right"><? echo $row[csf('or_ex')]; ?></td>
                        <td align="right"><? echo $row[csf('ot_ex')]; ?></td>
                        <td align="right"><? echo $row[csf('bt_ex')]; ?></td>
                        <td align="right"><? echo $row[csf('or_dis')]; ?></td>
                        <td align="right"><? echo $row[csf('ot_dis')]; ?></td>
                        <td align="right"><? echo $row[csf('bt_dis')]; ?></td>
                        <td align="right"><? echo $row[csf('or_elm')]; ?></td>
                        <td align="right"><? echo $row[csf('ot_elm')]; ?></td>
                        <td align="right"><? echo $row[csf('bt_elm')]; ?></td>
                    </tr>
                <?
					$tot_ot_pick+=$row[csf('ot_pick')]; 
					$tot_bt_pick+=$row[csf('bt_pick')]; 
					$tot_ot_ex+=$row[csf('ot_ex')]; 
					$tot_bt_ex+=$row[csf('bt_ex')]; 
					$tot_ot_dis+=$row[csf('ot_dis')]; 
					$tot_bt_dis+=$row[csf('bt_dis')];
					$tot_ot_elm+=$row[csf('ot_elm')]; 
					$tot_bt_elm+=$row[csf('bt_elm')];
				}
			?>
            <tr>
                <td colspan="2"><b>Total BT&nbsp;</b></td>
                <td align="right"><? echo $tot_ot_pick; ?></td>
                <td align="right"><? echo number_format($tot_bt_pick,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td align="right"><? echo $tot_ot_ex; ?></td>
                <td align="right"><? echo number_format($tot_bt_ex,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td align="right"><? echo $tot_ot_dis; ?></td>
                <td align="right"><? echo $tot_bt_dis; ?></td>
                <td>&nbsp;</td>
                <td align="right"><? echo $tot_ot_elm; ?></td>
                <td align="right"><? echo $tot_bt_elm; ?></td>
                <td align="right"><b><? echo number_format($tot_bt_pick+$tot_bt_ex+$tot_bt_dis+$tot_bt_elm,2,'.',''); ?> BT</b></td>
            </tr>
            <tr>
                <td colspan="2"><b>Average BT</b></td>
                <td>&nbsp;</td>
                <td align="right"><? echo $dataArray[0][csf('avg_bt_pick')]; ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? echo $dataArray[0][csf('avg_bt_ex')]; ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? echo $dataArray[0][csf('avg_bt_dis')]; ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? echo $dataArray[0][csf('avg_bt_elm')]; ?></td>
                <td align="right"><? echo $dataArray[0][csf('tot_avg_bt')]; ?></td>
            </tr>
            <tr>
                <td align="left" colspan="3"><b>Personal Allowance (%)&nbsp;</b></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? echo $dataArray[0][csf('relaxation_perc')]; ?></td>
                <td align="right"><? echo $dataArray[0][csf('relaxation_avg_bt')]; ?></td>
            </tr>
            <tr>
                <td align="left" colspan="3"><b>machine Allowance (%)&nbsp;</b></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? echo $dataArray[0][csf('machine_perc')]; ?></td>
                <td align="right"><? echo $dataArray[0][csf('machine_avg_bt')]; ?></td>
            </tr>
            <tr>
                <td align="left" colspan="3"><b>Fatic Allowance (%)&nbsp;</b></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? echo $dataArray[0][csf('alter_perc')]; ?></td>
                <td align="right"><? echo $dataArray[0][csf('alter_avg_bt')]; ?></td>
            </tr>
            <tr bgcolor="#FFFFFF" align="center">
                <td align="left" colspan="13"><b>G. Total Avg. BT + Personal Allowance + Machine Allowance + Fatic Allowance&nbsp;</b></td>
                <td align="right"><? echo $dataArray[0][csf('grand_tot_avg_bt')]; ?></td>
            </tr>
        </table>
        <table width="600" style="margin-top:20px">
        	<tr>
            	<td width="200"><b>SMV- <? echo $smv; ?></b></td>
                <td></td>
                <td width="200"><b>Target/HR- <? echo number_format($tph,2); ?></b></td>
            </tr>
        </table>
    </div>
<? 
exit();   
}

?>