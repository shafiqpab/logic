<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$sample_category=array(1=>"Basic",2=>"Casual Wear",3=>"Dress Up",4=>"Holiday",5=>"Occasion Wear",6=>"Sport Wear",7=>"Work Wear");
$sample_source=array(1=>"Product development",2=>"Factory",);
$errors= array();
$file_name = $_FILES['sample_pic']['name'];
$file_size =$_FILES['sample_pic']['size'];
$file_tmp =$_FILES['sample_pic']['tmp_name'];
$file_type=$_FILES['sample_pic']['type'];
$file_ext=strtolower(end(explode('.',$_FILES['sample_pic']['name'])));

$expensions= array("jpeg","jpg","png");

if(in_array($file_ext,$expensions)=== false)
{
	$errors[]="extension not allowed, please choose a JPEG or PNG file.";
}

if($file_size > 2097152){
	$errors[]='File size must be excately 2 MB';
}

if(empty($errors)==true)
{
	move_uploaded_file($file_tmp,"sample_images/".$file_name);	
}



if ($action=="search_list_view")
{
	echo load_html_head_contents("Sample List View","../../", 1, 1, $unicode);
?>	
	<script> 
	function js_set_value(data)
	{
		document.getElementById('update_id').value=data;
		parent.emailwindow.hide();
	}
	</script> 
	<input type="hidden" id="update_id"	 value="">
<?	
	$supplier_name=return_library_array( "select id,company_name from lib_company", "id","company_name" );
	$arr=array (2=>$sample_category,4=>$sample_source,5=>$supplier_name);
	
	echo  create_list_view ( "list_view", "Receive Date,Style Ref,Category,Item,Source,Supplier,Designer,Quantity", "90,90,100,100,100,100,90","850","220",0, "select id,receive_date,category_id,item_id,source_id,produced_by_id,designer,quantity,style_ref from sample_receive_mst where is_deleted=0 order by id", "js_set_value", "id", "'load_php_data_to_form'", 1, "0,0,category_id,0,source_id,produced_by_id", $arr , "receive_date,style_ref,category_id,item_id,source_id,produced_by_id,designer,quantity", "requires/sample_receive_controller", 'setFilterGrid("list_view",-1);','3,0,0,0,0,0,1,0' ) ;         
}

if ($action=="load_php_data_to_form")
{
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
  	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	
	$nameArray=sql_select( "select id,receive_date,category_id,item_id,source_id,produced_by_id,designer,quantity,fabric_nature,construction,composition,gsm,yarn_count_id,yarn_type_id,style_ref from sample_receive_mst where id='$data'" );
	$dtls_data="";
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('receive_date').value		 		= '".change_date_format($inf[csf("receive_date")])."';\n";    
		echo "document.getElementById('cbo_fabric_cat').value  			= '".($inf[csf("category_id")])."';\n";
		echo "document.getElementById('txt_style').value  				= '".($inf[csf("style_ref")])."';\n";
		echo "document.getElementById('txt_item').value  				= '".($inf[csf("item_id")])."';\n";
		echo "document.getElementById('cbo_fabric_source').value 		= '".($inf[csf("source_id")])."';\n";
		echo "document.getElementById('cbo_supplier_source').value  	= '".($inf[csf("produced_by_id")])."';\n";
		echo "document.getElementById('txt_designer').value  			= '".($inf[csf("designer")])."';\n";
		echo "document.getElementById('txt_qty').value  				= '".($inf[csf("quantity")])."';\n";
		echo "document.getElementById('cbo_fabric_natu').value  		= '".($inf[csf("fabric_nature")])."';\n";
		echo "document.getElementById('txt_construction').value 		= '".($inf[csf("construction")])."';\n";
		echo "document.getElementById('txt_Composition').value  		= '".($inf[csf("composition")])."';\n";
		echo "document.getElementById('txt_gsm').value  				= '".($inf[csf("gsm")])."';\n";
		echo "document.getElementById('cbo_yarn_count').value  			= '".($inf[csf("yarn_count_id")])."';\n";
		echo "document.getElementById('cbo_yarn_type').value  			= '".($inf[csf("yarn_type_id")])."';\n";
		echo "document.getElementById('cbo_status').value  				= '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  				= '".($inf[csf("id")])."';\n"; 
		
		$save_string='';
		$sql_dels=sql_select("select id,color_id,size_id,quantity,expected_price,amount,barcode,sample_pic from sample_receive_dtls where mst_id='$data' and is_deleted=0" );
		foreach($sql_dels as $row)
		{
			if($save_string=="")
				$save_string=$color_library[$row[csf('color_id')]]."*".$size_library[$row[csf('size_id')]]."*".$row[csf('quantity')]."*".$row[csf('expected_price')]."*".$row[csf('amount')]."*".$row[csf('barcode')]."*".$row[csf('id')];
			else 
				$save_string.="_".$color_library[$row[csf('color_id')]]."*".$size_library[$row[csf('size_id')]]."*".$row[csf('quantity')]."*".$row[csf('expected_price')]."*".$row[csf('amount')]."*".$row[csf('barcode')]."*".$row[csf('id')];
		}
		echo "document.getElementById('color_qty_breakdown').value = '".$save_string."';\n";
		
		echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_sample_receive',1);\n"; 
	}
}

if ($action=="save_update_delete")
{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
		$size_name_array=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
		
	if ($operation==0)  // Insert Here
	  	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
		$id=return_next_id( "id","sample_receive_mst", 1 ) ;
		$dtls_id=return_next_id( "id","sample_receive_dtls", 1 ) ;
		  
		$field_array="id,receive_date,category_id,item_id,source_id,produced_by_id,designer,quantity,fabric_nature,construction,composition,gsm,yarn_count_id,yarn_type_id,inserted_by,insert_date,status_active,is_deleted,style_ref";
		
		$data_array="(".$id.",".$receive_date.",".$cbo_fabric_cat.",".$txt_item.",".$cbo_fabric_source.",".$cbo_supplier_source.",".$txt_designer.",".$txt_qty.",".$cbo_fabric_natu.",".$txt_construction.",".$txt_Composition.",".$txt_gsm.",".$cbo_yarn_count.",".$cbo_yarn_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0,".$txt_style.")";
		$color_qty_breakdown=str_replace("'","",$color_qty_breakdown);
		$color_break_down_tr=explode("_",$color_qty_breakdown);
		$field_array_dtls="id,mst_id,color_id,size_id,quantity,expected_price,amount,barcode,inserted_by,insert_date,status_active,is_deleted,prod_year,sample_pic";
		$i=0;
		$barcode=return_field_value("max(barcode) as barcode","sample_receive_dtls"," prod_year=".date("y",strtotime(str_replace("'","",$receive_date)))."","barcode");
		// echo "0**"."max(barcode) as barcode","sample_receive_dtls"," prod_year=".date("y",strtotime(str_replace("'","",$receive_date)))."=".$barcode; die;
		if($barcode<1)
			$barcode=date("y",strtotime(str_replace("'","",$receive_date)))."".str_pad(1,6,"0",STR_PAD_LEFT);
		 else
			$barcode=$barcode+1;
			
	foreach($color_break_down_tr as $tr_value)
			{
				$color_break_down_td_value=explode("*",$tr_value);
				
				// $color_id=$color_name[$color_break_down_td_value[0]];
				//$size=$size_name_array[$color_break_down_td_value[1]];
				$qty=$color_break_down_td_value[2];
				$rate=$color_break_down_td_value[3];
				$amount=$color_break_down_td_value[4];
				$sample_pic=$color_break_down_td_value[6];
				$colorName=$color_break_down_td_value[0];
				 
				if(str_replace("'","",$colorName)!="")
				{ 
					if (!in_array(str_replace("'","",$colorName),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$colorName), $color_library, "lib_color", "id,color_name");  
						$new_array_color[$color_id]=str_replace("'","",$colorName);
					}
					else 
						$color_id =  array_search(str_replace("'","",$colorName), $new_array_color); 
				}
				else
				{
					$color_id=0;
				}
				
				$sizeName=$color_break_down_td_value[1]; 
				if(str_replace("'","",$sizeName)!="")
				{ 
					if (!in_array(str_replace("'","",$sizeName),$new_array_color))
					{
						$size = return_id( str_replace("'","",$sizeName), $size_name_array, "lib_size", "id,size_name");  
						$new_array_color[$color_id]=str_replace("'","",$sizeName);
					}
					else 
						$size =  array_search(str_replace("'","",$sizeName), $new_array_color); 
				}
				else
				{
					$size=0;
				}
				
				if($i==0) $comma_set=''; else $comma_set=',';
				
				$data_array_dtls.="$comma_set (".$dtls_id.",".$id.",'".$color_id."','".$size."','".$qty."','".$rate."','".$amount."','".$barcode."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0,".date("y",strtotime(str_replace("'","",$receive_date))).",'".$sample_pic."')";
				$dtls_id++;
				$i++;
				$barcode=$barcode+1;
			}
			 
		  $rID_dtls=sql_insert("sample_receive_dtls",$field_array_dtls,$data_array_dtls,1);
		  $rID=sql_insert("sample_receive_mst",$field_array,$data_array,1);
		//echo "INSERT INTO sample_receive_mst(".$field_array.")VALUES".$data_array;die;
		//echo "INSERT INTO sample_receive_dtls(".$field_array_dtls.")VALUES".$data_array_dtls;die;
		  if($db_type==0)
		  {
			  
			if($rID && $rID_dtls )
			{
			  mysql_query("COMMIT");  
			  echo "0**".$id;
			}
			else
			{
			  mysql_query("ROLLBACK"); 
			  echo "10**".$id;
			}
		  }
		  
		  if($db_type==2 || $db_type==1 )
		  {
			if($rID && $rID_dtls)
			{
			  oci_commit($con);  
			  echo "0**".$id;
			}
			else
			{
			  oci_rollback($con);
			  echo "10**".$id;
			}
		  }
			disconnect($con);
			die;
		  }
	  
	else if ($operation==1)
		{   // Update Here
		
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array="receive_date*category_id*item_id*source_id*produced_by_id*designer*quantity*fabric_nature*construction*composition*gsm*yarn_count_id*yarn_type_id*updated_by*update_date*status_active*is_deleted*style_ref";
			$data_array="".$receive_date."*".$cbo_fabric_cat."*".$txt_item."*".$cbo_fabric_source."*".$cbo_supplier_source."*".$txt_designer."*".$txt_qty."*".$cbo_fabric_natu."*".$txt_construction."*".$txt_Composition."*".$txt_gsm."*".$cbo_yarn_count."*".$cbo_yarn_type."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0*".$txt_style."";
			//
			$color_qty_breakdown=str_replace("'","",$color_qty_breakdown);
			$color_break_down_tr=explode("_",$color_qty_breakdown);
			$field_array_dtls="id,mst_id,color_id,size_id,quantity,expected_price,amount,barcode,inserted_by,insert_date,status_active,is_deleted,prod_year";
			$i=0;
			  $barcode=return_field_value("max(barcode) as barcode","sample_receive_dtls"," prod_year=".date("y",strtotime(str_replace("'","",$receive_date)))."","barcode");
			// echo "0**"."max(barcode) as barcode","sample_receive_dtls"," prod_year=".date("y",strtotime(str_replace("'","",$receive_date)))."=".$barcode; die;
			if($barcode<1)
				$barcode=date("y",strtotime(str_replace("'","",$receive_date)))."".str_pad(1,6,"0",STR_PAD_LEFT);
			else
				$barcode=$barcode+1;
			
			$dtls_id=return_next_id( "id","sample_receive_dtls", 1 ) ; 
			$field_array_dtls_insert="id,mst_id,color_id,size_id,quantity,expected_price,amount,barcode,inserted_by,insert_date,status_active,is_deleted,prod_year";
			$field_array_dtls_update="color_id*size_id*quantity*expected_price*amount*updated_by*update_date";
			$field_array_dtls_delete="updated_by*update_date*status_active*is_deleted";
			$i=0;
			// echo "10**";print_r($color_break_down_tr);die;
			$data_array_dtls_insert='';
			foreach($color_break_down_tr as $tr_value)
			{
				 $color_break_down_td_value=explode("*",$tr_value);
				 //$color_id=$color_name[$color_break_down_td_value[0]];
				 $size=$size_name_array[$color_break_down_td_value[1]];
				 $qty=$color_break_down_td_value[2];
				 $rate=$color_break_down_td_value[3];
				 $amount=$color_break_down_td_value[4];
				 $dtls_update_id=$color_break_down_td_value[6];
				 $colorName=$color_break_down_td_value[0]; 
				 if(str_replace("'","",$colorName)!="")
					{ 
						if (!in_array(str_replace("'","",$colorName),$new_array_color))
						{
							$color_id = return_id( str_replace("'","",$colorName), $color_library, "lib_color", "id,color_name");  
							$new_array_color[$color_id]=str_replace("'","",$colorName);
						}
						else 
							$color_id =  array_search(str_replace("'","",$colorName), $new_array_color); 
					}
					else
					{
						$color_id=0;
					}
				 
				 
				  $sizeName=$color_break_down_td_value[1]; 
					if(str_replace("'","",$sizeName)!="")
					{ 
						if (!in_array(str_replace("'","",$sizeName),$new_array_color))
						{
							$size = return_id( str_replace("'","",$sizeName), $size_name_array, "lib_size", "id,size_name");  
							$new_array_color[$color_id]=str_replace("'","",$sizeName);
						}
						else 
							$size =  array_search(str_replace("'","",$sizeName), $new_array_color); 
					}
					else
					{
						$size=0;
					}
				 

				 if($dtls_update_id!='')
				 {
					 $dtls_update_id_arr[]=$dtls_update_id;
					 $dtls_update_id_arr2[]=$dtls_update_id;
					 $data_array_dtls_update[$dtls_update_id]=explode(",",($color_id.",".$size.",".$qty.",".$rate.",".$amount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
				 }
				 else
				 {
					 if($i==0) $comma_set=''; else $comma_set='*';
					 $data_array_dtls_insert.="$comma_set (".$dtls_id.",".$update_id.",'".$color_id."','".$size."','".$qty."','".$rate."','".$amount."','".$barcode."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0,".date("y",strtotime(str_replace("'","",$receive_date))).")";
					$i++;
					$dtls_update_id_arr2[]=$dtls_id;
					$dtls_id++;
					
					$barcode=$barcode+1;
				 }
				
			}
			
				$flag=1;
			
				$rID1=sql_update("sample_receive_mst",$field_array,$data_array,"id",$update_id,1);
				if($flag==1) 
				{
					if($rID1) $flag=1; else $flag=0; 
				} 
				
				$rID2=execute_query(bulk_update_sql_statement("sample_receive_dtls","id", $field_array_dtls_update,$data_array_dtls_update,$dtls_update_id_arr),1);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
				//echo "10**".$flag.bulk_update_sql_statement("sample_receive_dtls","id", $field_array_dtls_update,$data_array_dtls_update,$dtls_update_id_arr);die;
				$rID3=sql_insert("sample_receive_dtls",$field_array_dtls_insert,$data_array_dtls_insert,1);
				//echo "insert into sample_receive_dtls(".$field_array_dtls_insert.")values".$data_array_dtls_insert;die;
				if($flag==1 && $data_array_dtls_insert!='') 
				{
					if($rID3) $flag=1; else $flag=0; 
				} 
				
				$rID4=execute_query("UPDATE sample_receive_dtls SET status_active=0,is_deleted=1 WHERE id not in(".implode(',',$dtls_update_id_arr2).") and mst_id=$update_id");
				if($flag==1) 
				{
					if($rID4) $flag=1; else $flag=0; 
				} 

				 if($db_type==0)
				 {
					if($flag==1)
					{
						mysql_query("COMMIT");
						echo "1**".str_replace("'","",$update_id);
					}
					else
					{
						mysql_query("ROLLBACK");
						echo "10**".str_replace("'","",$update_id);;
					}
				 }
				 elseif($db_type==2 || $db_type==1 )
				 {
					if($flag==1)
					{
						oci_commit($con);   
						echo "1**".str_replace("'","",$update_id);;
					}
					else
					{
						oci_rollback($con);
						echo "10**".str_replace("'","",$update_id);;
					}
				 }
					disconnect($con);
					die;
		}
	  
	else if ($operation==2)   // Delete Here
	  {
		  $con = connect();
		  if($db_type==0)
		  {
		    mysql_query("BEGIN");
		  }
		  
		  $field_array="updated_by*update_date*status_active*is_deleted";
		  $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		  $rID=sql_delete("sample_receive_mst",$field_array,$data_array,"id",$update_id,1);
		  $rID_dtls_delete=sql_delete("sample_receive_dtls",$field_array, $data_array,"mst_id",$update_id,1);
		  
		  if($db_type==0)
		  {
		    if($rID && $rID_dtls_delete)
		    {
			  	mysql_query("COMMIT");  
			  	echo "2**".$rID;
		    }
		    else
		    {
			  	mysql_query("ROLLBACK"); 
			  	echo "10**".$rID;
		    }
		  }
		  
		  if($db_type==2 || $db_type==1 )
		  {
		    if($rID && $rID_dtls_delete)
		    {
			    oci_commit($con);   
			    echo "2**".$rID;
		    }
		    else
		    {
			    oci_rollback($con);
			    echo "10**".$rID;
		    }
		  }
		  disconnect($con);
		  die;
		}
}

if($action=="sample_re_popup"){
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST); 
  	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
  	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
?>
<script type="text/javascript">
    function add_break_down_tr(i)
    {
        var row_num = $('#tbl_qty tbody tr').length;
        if ( row_num != i )
        {
            return false;
        }
		
        else
        {
            i++;
            $("#tbl_qty tbody tr:last").clone().find("input,select").each(function() 
			{
                $(this).attr({
                    'id': function(_, id) 
					{
                        var id = id.split("_");
                        return id[0] + "_" + i
                    },
                    'name': function(_, name) 
					{
                        return name + i
                    },
                    'value': function(_, value)
					{
                        return value
                    }
                });
            }).end().appendTo("#tbl_qty tbody");
			 $('#txtorderquantity_' + i).removeAttr("onKeyup").attr("onKeyup", "calculate_total_amnt(" + i + ");calculate_total_qty()");
            $('#txtorderrate_' + i).removeAttr("onKeyup").attr("onKeyup", "calculate_total_amnt(" + i + ");");
            $('#increase_' + i).removeAttr("onClick").attr("onClick", "add_break_down_tr(" + i + ");");
            $('#decrease_' + i).removeAttr("onClick").attr("onClick", "fn_deletebreak_down_tr(" + i + ");");
			$('#cboColor_' + i).removeAttr("onFocus").attr("onFocus", "add_auto_complete(" + i + ");");
            $('#txtsize_' + i).removeAttr("onFocus").attr("onFocus", "add_auto_complete(" + i + ");");
            $('#txtslno_' + i).val(i);
            $('#cboColor_' + i).val('');
            $('#txtsize_' + i).val('');
            $('#txtorderquantity_' + i).val('');
            $('#txtorderrate_' + i).val('');
            $('#txtorderamount_' + i).val('');
            $('#txtbarcode_' + i).val('');
			$('#sample_pic_' + i).val('');
            $('#updatedtlsid_' + i).val('');
        }
    }

    function fn_deletebreak_down_tr(rowNo, table_id)
    {
        var numRow = $('table#tbl_qty tbody tr').length;
        if (numRow == rowNo && rowNo != 1)
        {
            $('#tbl_qty tbody tr:last').remove();
			calculate_total_qty();
        }
    }

    function calculate_total_amnt(i) 
	{
        var qty = $('#txtorderquantity_' + i).val();
        var unprice = $('#txtorderrate_' + i).val();
        var amount = qty * unprice;
        $('#txtorderamount_' + i).val(amount);
    }
	
	//function math_operation( target_fld, value_fld, operator, fld_range, dec_point)
	 function calculate_total_qty() 
	{
		var tot_row=$('#tbl_qty tbody tr').length;
		math_operation( "totalqty", "txtorderquantity_", "+", tot_row );
    }

		function fnc_process_sample()
		{
			var process_string="";
			var total_qty=0;
			total_row=$("#tbl_qty tbody tr").length;
			
			for(var sl=1;sl<=total_row;sl++)
			{
				var dtls_update_id=$("#updatedtlsid_"+sl).val();
				var color_id=$("#cboColor_"+sl).val();
				var size=$("#txtsize_"+sl).val();
				var qty=$("#txtorderquantity_"+sl).val()*1;
				var rate=$("#txtorderrate_"+sl).val()*1;
				var amount=$("#txtorderamount_"+sl).val()*1;
				var barcode=$("#txtbarcode_"+sl).val()*1;
				if(qty>0)
				{
					if(trim(process_string)!="") process_string=process_string+"_";
					process_string=process_string+color_id+"*"+size+"*"+qty+"*"+rate+"*"+amount+"*"+barcode+"*"+dtls_update_id;
					total_qty+=qty;
				}
			}
			$('#hidden_process_string').val( process_string );
			$('#hidden_total_qty').val( total_qty );
			 //alert(process_string);
			
			parent.emailwindow.hide();
		}
			
	
	var str_color = [<? echo substr(return_library_autocomplete( "select distinct(color_name) from lib_color","color_name" ), 0, -1); ?>];
	var str_size = [<? echo substr(return_library_autocomplete( "select distinct(size_name) from lib_size","size_name" ), 0, -1); ?>];
	//alert(str_color);
	function add_auto_complete(j)
	{

		 $("#cboColor_"+j).autocomplete({
			 source: str_color
		  });
		  
		  $("#txtsize_"+j).autocomplete({
			 source: str_size
		  });
	}
</script>
</head>
<body>
    <div align="center"  style="padding:0px; margin:0">
    	<input type="hidden" name="hidden_process_string" id="hidden_process_string" value="" />
        <input type="hidden" name="hidden_total_qty" id="hidden_total_qty" value="" />
        <fieldset style="width:90%;">
            <legend></legend>
            <form name="smapleqty_1" id="smapleqty_1" enctype="multipart/form-data">
                <table width="60%" border="0" cellspacing="0" cellpadding="0" class="rpt_table" id="tbl_qty" rules="all">
                    <thead>
                        <tr>
                            <th width="20">SL</th>
                            <th width="100">Color </th>
                            <th width="100">Size</th>
                            <th width="80">Qty</th>
                            <th width="80">Expected price</th>
                            <th width="80">Amount</th>
                            <th width="50">Barcode</th>
                            <th width="50" colspan="3">Sample Picture</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
					
				  	$sql_dels=sql_select( "select id,mst_id,color_id,size_id,quantity,expected_price,amount,barcode,sample_pic from sample_receive_dtls where mst_id='$update_id' and is_deleted=0" );
					if( count($sql_dels))
					{
					 $color_break_down_tr=explode("_",$color_qty_breakdown);
					 $j=0;
					 $total_qty=0;
					 foreach($sql_dels as $rows)
					 {
						 $j++;
						$color_break_down_td_value=explode("*",$tr_value);
						 $total_qty+=$color_break_down_td_value[3];
						
					 ?>
						<tr id="break_<?php echo $j;?>"><!--onClick="tr_index(this)"-->
                            <td>
                                <input type="text" id="txtslno_<?php echo $j;?>" name="txtslno_<?php echo $j;?>" class="text_boxes" style="width:30px" value="<?php echo $j;?>" disabled/>
                                
                           <input type="hidden" name="updatedtlsid_<?php echo $j;?>" id="updatedtlsid_<?php echo $j;?>" value="<?php echo $rows[csf('id')];?>" />      
                                
                            </td>
                            <td>
                             <input type="text" id="cboColor_<?php echo $j;?>" name="cboColor_<?php echo $j;?>" class="text_boxes" style="width:70px;" value="<?php echo $color_library[$rows[csf('color_id')]]; ?>" onFocus="add_auto_complete(1)"    />
                               
                            </td>
                            <td>
                                <input type="text" id="txtsize_<?php echo $j;?>" name="txtsize_<?php echo $j;?>"  class="text_boxes" style="width:70px"  value="<?php echo $size_library[$rows[csf('size_id')]]; ?>" /> 
                            </td>
                            <td>
                                <input type="text" id="txtorderquantity_<?php echo $j;?>" name="txtorderquantity_<?php echo $j;?>"  class="text_boxes_numeric" onKeyup="calculate_total_qty();calculate_total_amnt(<?php echo $j;?>);" style="width:75px" value="<?php echo $rows[csf('quantity')]; $total_qty+=$rows[csf('quantity')]; ?>" /> 
                            </td>
                            <td id='caltd'>
                                <input type="text" id="txtorderrate_<?php echo $j;?>" onKeyup="calculate_total_amnt(<?php echo $j;?>)"   name="txtorderrate_<?php echo $j;?>"  class="text_boxes_numeric" style="width:75px"  value="<?php echo $rows[csf('expected_price')]; ?>" />
                            </td>
                            <td>
                                <input type="text" id="txtorderamount_<?php echo $j;?>" name="txtorderamount_<?php echo $j;?>" readonly class="text_boxes_numeric" style="width:75px" value="<?php echo $rows[csf('amount')]; ?>" /> 
                            </td>
                            <td>
                                <input type="text" id="txtbarcode_<?php echo $j;?>"  value="<?php echo $rows[csf('barcode')]; ?>" name="txtbarcode_<?php echo $j;?>" class="text_boxes" style="width:70px" /> 
                            </td> 
                            <td height="25" valign="middle">
                           <!--function file_uploader ( url, mst_id, det_id,  form, file_type, is_multi, show_button )-->
                    	<input type="file" id="sample_pic" class="sample_pic" style="width:192px" value="" >
                    		</td>
                            <td>
                                <input type="button" id="increase_<?php echo $j;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<?php echo $j;?>)" />
                            </td>
                            <td>
                                <input type="button" id="decrease_<?php echo $j;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(<?php echo $j;?>);" />
                            </td> 
                            
                        </tr> 
						 
					 <?php
					 }
						
					}
					else
					{
					
					
					?>
                        <tr id="break_1"><!--onClick="tr_index(this)"-->
                            <td>
                                <input type="text" id="txtslno_1" name="txtslno_1" class="text_boxes" style="width:30px" value="1" disabled/>
                                <input type="hidden" name="updatedtlsid_<?php echo $j;?>" id="updatedtlsid_<?php echo $j;?>" value="<?php echo $rows[csf('id')];?>" />
                            </td>
                            <td>
                              <input type="text" id="cboColor_1" name="cboColor_1"  class="text_boxes" style="width:70px" onFocus="add_auto_complete( 1 )"   /> 
                                
                            </td>
                            <td>
                                <input type="text" id="txtsize_1" name="txtsize_1"  class="text_boxes" style="width:70px"  /> 
                            </td>
                            <td>
                                <input type="text" id="txtorderquantity_1" name="txtorderquantity_1"  class="text_boxes_numeric" onKeyup="calculate_total_qty();calculate_total_amnt(1);" style="width:75px" placeholder="Write" /> 
                            </td>
                            <td id='caltd'>
                                <input type="text" id="txtorderrate_1" onKEyup="calculate_total_amnt(1)" value=""  name="txtorderrate_1"  class="text_boxes_numeric" style="width:75px" placeholder="Write"/>
                                
                            </td>
                            <td>
                                <input type="text" id="txtorderamount_1" name="txtorderamount_1" readonly class="text_boxes_numeric" style="width:75px" /> 
                            </td>
                           <td>
                                <input type="text" id="txtbarcode_1"  value="" name="txtbarcode_1" class="text_boxes" style="width:70px" readonly/> 
                            </td>
                            <td height="25" valign="middle">
                    	<input type="file" id="sample_pic" class="sample_pic" style="width:192px" value="" >
                    		</td>
                            <td>
                                <input type="button" id="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)" />
                            </td>
                            
                            <td>
                                <input type="button" id="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_deletebreak_down_tr(1);" />
                            </td> 
                            
                        </tr>
                   <?php 
					}
					 ?>   
                    </tbody>
                    <tfoot>
                    <tr>
                    <td></td>
                    <td></td>
                    <td align="right">Total Qty:</td>
                    
                            <td align="center" width="30"><input type="hidden" name="qty" id="qty" class="text_boxes"  value="10"><input type="text" name="totalqty" id="totalqty" class="text_boxes_numeric" style="width:75px"  value="<?php echo $total_qty; ?>" disabled></td> 
                            </tr>
                       <tr>
                       <td>
                       </td>
                       </tr>
                    </tfoot>
                </table>
                <br/>
                <div align="center"><input type="button" name="close" onClick="fnc_process_sample();" class="formbutton" value="Close" style="width:100px" /></div>
            </form>
        </fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="sample_receive_print")
{
	echo load_html_head_contents("Sample Receive","../", 1, 1, $unicode);
	
	$barcode_no="";
	$sql_dels=sql_select( "select id,mst_id,color_id,size_id,quantity,expected_price,amount,barcode from sample_receive_dtls where is_deleted=0 and mst_id='$data'" );
	 
	 foreach($sql_dels as $rows)
	 {
		 $barcode_arr[$rows[csf('barcode')]]=$rows[csf('quantity')];
		 if($barcode_no=="")
		 {
			 $barcode_no=$rows[csf('barcode')];
		 }
		 else
		 {
			 $barcode_no=$barcode_no.",".$rows[csf('barcode')];
		 }
	 }
	 
	$barcode_no=explode(',',$barcode_no);
	
	$bardata=sql_select( "select id,receive_date,category_id,item_id,source_id,produced_by_id,designer,quantity,fabric_nature,construction,composition,gsm,yarn_count_id,yarn_type_id,style_ref from sample_receive_mst where id='$data'" );
   
	foreach($bardata as $row)
	{
		?>
		
		<table>
		   <?
          $yarnid=return_library_array( "select id,yarn_count from lib_yarn_count", "id","yarn_count" );	
          $supplier=return_library_array("select id,company_name from lib_company");
       
          $i=0;
		  ?>
          <tr>
          <?
		  $wid="margin-top:-20px;";
          foreach($barcode_arr as $barcode=>$qnty)
          {
              for( $m=0; $m<$qnty; $m++ )
              {
                  $mn=$i%2;
                  if( $i!=0 && $mn==0 ) { $wid="margin-top:10px;";   echo "</tr>"; }
                  if( $mn==0  )
                      echo "<tr>";
                  ?>
                     
                      <td>
                          <table cellpadding="0" cellspacing="0" style=" width:136.062992126px; height:93px; <? echo $wid; ?>  ">
                              <tr>
                              	<td bcode="<? echo $barcode; ?>" id="barcode_img_id<? echo $i; ?>"></td>
                              </tr>
                              <tr>
                              	<td style="float:left; margin-left:8px;"><strong><? echo $barcode; ?></strong></td>
                              </tr>
                              <tr>
                              	<td style="float:left;margin-left:10px; margin-top:0px; font-size:8px;"><strong >Style Ref:&nbsp;</strong></td>
                              	<td style="float:left;font-size:10px;" ><? echo $row[csf('style_ref')]; ?></td>
                              </tr>
                              <tr>
                              	<td style="float:left;font-size:10px;margin-left:8px;"><? echo $row[csf('construction')]; ?>,<? echo $row[csf('composition')]; ?>,<? echo $row[csf('gsm')]; ?>
                                </td>
                              </tr>
                          </table>
                      </td>
                 <?
                 $i++;
				
              }
			   
          }
      ?> 
          </tr>
    </table>
    <? } ?>

	<script type="text/javascript" src="../js/jquerybarcode.js"></script>
	<script> 
		var qnty=<? echo $i; ?>;
		//var value = valuess;//$("#barcodeValue").val();
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
	
	for(var k= 0; k<qnty; k++)
	{
		//fnc_generate_Barcode( $('#barcode_img_id'+k).attr('bcode') ,'barcode_img_id'+k );
		$("#barcode_img_id"+k).html('11');
		var value = {code:value, rect: false};
		$("#barcode_img_id"+k).show().barcode( $('#barcode_img_id'+k).attr('bcode'), btype, settings);
	}
    </script>
		<?php
	
	exit();
 
}
?>
 