<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:logout.php");
require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
if($action=="job_popup")
{
	echo load_html_head_contents("Search Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
		
		var selected_id = new Array; var selected_name = new Array;var selected_style = new Array;var selected_id_arr = new Array;
		
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
			 
			if( jQuery.inArray( str[0], selected_id_arr ) == -1 ) {
				selected_id_arr.push( str[0] );
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				selected_style.push( str[3] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_style.splice( i, 1 );
			}
			var id = ''; var name = '';var style = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				style += selected_style[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			style = style.substr( 0, style.length - 1 );
			
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( name );
			$('#hide_style_no').val( style );
		}
	
    </script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:710px;">
	            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th class="must_entry_caption">Company Name</th>
	                    <th>Buyer</th>
	                    <th>Year</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="100">Job No</th>
	                    <th>
                            <input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"> 
                            <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                            <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                            <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
                        </th>
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected,"");
								?>
	                        </td>
	                        <td align="center" id="buyer_td">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 180, "select id,buyer_name from lib_buyer  where  status_active =1 and is_deleted=0 $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" ); 
								
								?>
	                        </td>                  
	                        <td align="center">	
	                    	<?						
								echo create_drop_down( "cbo_year", 110, $year,"",1, "--Select--", "",'',0 );
							?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>     
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'search_list_view', 'search_div', 'smv_change_history_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
	                    	</td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>        
	<script type="text/javascript">
		$("#cbo_year").val('<?=$cbo_year;?>');
	</script>   
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}
if($action=="search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	
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
	if(str_replace("'", "", $data[3])!="")
	{
		$search_string="".trim($data[3])."";
	}

	if($search_by==1) 
		$search_field="a.job_no_prefix_num"; 
	else if($search_by==2) 
		$search_field="a.style_ref_no";
	$search_cond="";
	if($search_string!="")	{$search_cond=" and $search_field like '%$search_string%'";}
	$job_year =$data[4];
	
	if($job_year!=0)
	{
		if($db_type==0)
		{
			$job_year_cond=" and year(a.insert_date)='$job_year'";
		}
		else
		{
			$job_year_cond=" and to_char(a.insert_date,'YYYY')='$job_year'";	
		}
	}
	else
	{
		$job_year_cond="";
	}
	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$arr=array (0=>$company_library,1=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	
	$sql= "SELECT a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $search_cond $buyer_id_cond $job_no_cond $job_year_cond group by a.id,
         a.job_no, a.insert_date, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no order by a.id desc"; 
    // echo $sql;
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "100,100,50,100","550","220",0, $sql , "js_set_value", "id,job_no,style_ref_no","",1,"company_name,buyer_name,0,0,0,0",$arr,"company_name,buyer_name,year,job_no,style_ref_no","",'','0,0,0,0,0','',1) ;
   exit(); 
}

if ($action == "style_wise_search") 
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
  ?>
			<script>
				var selected_id = new Array;
				var selected_name = new Array;

				function check_all_data() {
					var tbl_row_count = document.getElementById('list_view').rows.length;
					tbl_row_count = tbl_row_count - 0;
					for (var i = 1; i <= tbl_row_count; i++) {
						var onclickString = $('#tr_' + i).attr('onclick');
						var paramArr = onclickString.split("'");
						var functionParam = paramArr[1];
						js_set_value(functionParam);
					}
				}

				function toggle(x, origColor) {
					var newColor = 'yellow';
					if (x.style) {
						x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
					}
				}

				function js_set_value(strCon) {
					//alert(strCon);
					var splitSTR = strCon.split("_");
					var str = splitSTR[0];
					var selectID = splitSTR[1];
					var selectDESC = splitSTR[2];
					if ($('#tr_' + str).css("display") != 'none') {
						toggle(document.getElementById('tr_' + str), '#FFFFCC');
						if (jQuery.inArray(selectID, selected_id) == -1) {
							selected_id.push(selectID);
							selected_name.push(selectDESC);
						} else {
							for (var i = 0; i < selected_id.length; i++) {
								if (selected_id[i] == selectID) break;
							}
							selected_id.splice(i, 1);
							selected_name.splice(i, 1);
						}
					}
					var id = '';
					var name = '';
					var job = '';
					for (var i = 0; i < selected_id.length; i++) {
						id += selected_id[i] + ',';
						name += selected_name[i] + ',';
					}
					id = id.substr(0, id.length - 1);
					name = name.substr(0, name.length - 1);
					//alert(name);
					$('#txt_selected_id').val(id);
					$('#txt_selected').val(name);
				}
			</script>
		    <?
				extract($_REQUEST);
				if ($company == 0) $company_name = "";
				else $company_name = "and a.company_name=$company";
				if ($buyer == 0) $buyer_name = "";
				else $buyer_name = "and a.buyer_name=$buyer";

				

				
				$arr = array();
				$sql = "SELECT b.id,a.style_ref_no,b.po_number,a.job_no,a.job_no_prefix_num,b.grouping,TO_CHAR(a.insert_date,'YYYY') as year from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst $company_name $buyer_name  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by job_no_prefix_num";
			    // echo $sql; die;
				echo create_list_view("list_view", "Job Year,Job No ,Style Ref No,Internal Ref No", "80,100,120,120", "480", "310", 0, $sql, "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "year,job_no_prefix_num,style_ref_no,grouping", "", "setFilterGrid('list_view',-1)", "0", "", 1);
				// echo $sql;
				echo "<input type='hidden' id='txt_selected_id' />";
				echo "<input type='hidden' id='txt_selected' />";
				exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$job_no = str_replace("'","",$txt_job_no);
	$txt_style_no=trim(str_replace("'","",$txt_style_no));

	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$user_arr=return_library_array( "select id,user_name from user_passwd", "id","user_name"  );


	
	//$buyer_cond="";
	//if($buyer_name !=0) $buyer_cond.=" and a.buyer_id in($buyer_name)"; 
	
	// if($cbo_buyer_name==0)
	// {
	// 	if ($_SESSION['logic_erp']["data_level_secured"]==1)
	// 	{
	// 		if($_SESSION['logic_erp']["buyer_id"]!=""){$buyer_cond.=" and a.BUYER_ID in (".$_SESSION['logic_erp']["buyer_id"].")";}
	// 	}
	// }
	// else
	// {
	// 	$buyer_cond.=" and a.BUYER_ID=$cbo_buyer_name";
	// }

		
	if($date_from !='' && $date_to !=''){	
		if($db_type==0)
		{
			$start_date=change_date_format($date_from,"yyyy-mm-dd","");
			$end_date=change_date_format($date_to,"yyyy-mm-dd","");
			$where_cond.=" and a.INSERT_DATE between '$start_date' and '$end_date'";
		}
		else if($db_type==2)
		{
			$start_date=change_date_format($date_from,"","",1);
			$end_date=change_date_format($date_to,"","",1);
			$where_cond.=" and a.INSERT_DATE between '$start_date' and '".$end_date." 11:59:59 PM'";
		}
	}
	//if ($txt_style_no!=""){$where_cond.=" and a.STYLE_REF like ('%$txt_style_no') ";}

	if($cbo_buyer_name==0) $buyer_cond=""; else $buyer_cond=" and a.BUYER_ID in($cbo_buyer_name) ";
	if($txt_style_no=='') $style_ref_cond=""; else $style_ref_cond=" and a.STYLE_REF IN '($txt_style_no)' ";
	
	$job_cond = "";
	if (str_replace("'", "", trim($txt_job_no)) != "")
	{
		if (str_replace("'", "", $hidden_job_id) != "")
		{
			$job_cond = "and a.job_id in(" . str_replace("'", "", $hidden_job_id) . ")";
		}
		else
		{
			$job_number = "%" . trim(str_replace("'", "", $txt_job_no)) . "%";
			$job_cond = "and a.po_job_no like '$job_number'";
		}
	}
	
	
	

	
	$sql="SELECT A.BUYER_ID,A.SYSTEM_NO,a.SYSTEM_NO_PREFIX,A.STYLE_REF,A.EXTENTION_NO,A.PRODUCT_DEPT,A.GMTS_ITEM_ID,A.COLOR_TYPE,A.BULLETIN_TYPE,A.INSERT_DATE,A.INSERTED_BY,A.APPROVED,A.APPROVED_BY,A.APPROVED_DATE,A.REMARKS,A.TOTAL_SMV ,A.PO_JOB_NO  from PPL_GSD_ENTRY_MST a where A.IS_DELETED=0 and A.STATUS_ACTIVE=1 $where_cond $buyer_cond $style_ref_cond $job_cond order by A.BUYER_ID DESC";
	//  echo $sql;
				
		$width=1500;
		$sql_result=sql_select($sql);
		$cut_data_arr=array();
		foreach($sql_result as $rows)
		{	
			$key=$rows[BUYER_ID].'_'.$rows[SYSTEM_NO];
			$dataArr[$key]=$rows;
		}

ob_start();
?>
        
        
    <table cellspacing="0"  width="<?= $width;?>">
        <tr class="form_caption">
            <td align="center" colspan="16">
                <span style="font-size:18px;">SMV Change History Report </span> <br />
                <? if($start_date!=""){ echo "From " .$start_date. " To ".$end_date;}?>                               
            </td>
        </tr>
    </table>

    <div style="width:<?= $width+20;?>px;">
    <table width="<?= $width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left">    
    <thead>  
        <tr style="font-size:12px;">  
            <th width="30">Sl</th>
            <th width="120">Buyer</th>	
			<th width="100">Job No</th>	
            <th width="120">Style</th>	
            <th width="60">Sys ID</th>	
            <th width="60">Extension No</th>	
            <th width="100">Product Dept</th>	
            <th width="120">Garment Item</th>	
            <th width="100">Color Type</th>	
            <th width="100">Bulletin Type</th>	
            <th width="60">SMV</th>
            <th width="100">Entry User</th>	
            <th width="70">Entry Date</th>	
            <th width="100">Approve BY</th>	
            <th width="70">Approve Date</th>	
            <th width="60">App. Status</th>	
            <th>Remarks</th>
        </tr>
    </thead>	
    </table>   
    </div>	
    <div style="width:<?= $width+18;?>px; max-height:350px; overflow-y:scroll; clear:both" id="scroll_body">	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" align="left"  id="tbl_list">  
		<?
        $i=1;
		$appStatus=array(1=>"Approved");
		foreach( $dataArr as $rows)
        {
			$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<?= $i; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" style="cursor:pointer;"> 
                <td width="30" align="center"><?= $i; ?></td>
                <td width="120"><?= $buyer_arr[$rows[BUYER_ID]];?></td>	
				<td width="100"><?= $rows[PO_JOB_NO];?></td>	
                <td width="120"><p><?= $rows[STYLE_REF];?></p></td>	
                <td width="60" align="center"><p><?= $rows[SYSTEM_NO_PREFIX];?></p></td>	
                <td width="60" align="center"><?= $rows[EXTENTION_NO];?></td>	
                <td width="100"><?= $product_dept[$rows[PRODUCT_DEPT]];?></td>	
                <td width="120"><?= $garments_item[$rows[GMTS_ITEM_ID]];?></td>	
                <td width="100"><?= $color_type[$rows[COLOR_TYPE]];?></td>	
                <td width="100"><?= $bulletin_type_arr[$rows[BULLETIN_TYPE]];?></td>	
                <td width="60" align="center"><?= number_format($rows[TOTAL_SMV],2);?></td>
                <td width="100"><?= $user_arr[$rows[INSERTED_BY]];?></td>	
                <td width="70" align="center"><?= change_date_format($rows[INSERT_DATE]);?></td>	
                <td width="100"><?= $user_arr[$rows[APPROVED_BY]];?></td>	
                <td width="70" align="center"><?= change_date_format($rows[APPROVED_DATE]);?></td>	
                <td width="60" align="center"><?= $appStatus[$rows[APPROVED]];?></td>	
                <td><?= $rows[REMARKS];?></td>
            
            </tr>
			<?
			$i++;
        }
        ?>
    </table>
    </div>


    
    
    <?
	$html=ob_get_contents();
	ob_clean();
	        
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo "$html####$filename";
	
	exit();
		
}





?>
      
 