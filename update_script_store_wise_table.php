<?
	include('includes/common.php');
	function sql_insert2( $strTable, $arrNames, $arrValues, $commit, $contain_lob )
	{
		global $con ;
		if($contain_lob=="") $contain_lob=0;
		
		if( $contain_lob==0)
		{
			$count=count($arrValues);
			 //return $count."ss"; 
			if( $count >1 ) // Multirow
			{
				$k=1;	
				foreach( $arrValues as $rows)
				{
					
					if($k==1)
					{
						$strQuery= "INSERT ALL \n";
					}
					$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$rows." \n";
					if( $count==$k )
					{
						$count=$count-$k;
						$strQuery .= "SELECT * FROM dual";
						//return "=".$strQuery; 
						$stid =  oci_parse($con, $strQuery);
						//oci_execute("Character set is AL32UTF8");
						$exestd=oci_execute($stid, OCI_NO_AUTO_COMMIT);
						 if(!$exestd) return 0; //else return $exestd;
						$strQuery="";
						$k=0;
					}
					else if ( $k==50 )
					{
						$count=$count-$k;
						$strQuery .= "SELECT * FROM dual";
						//return $strQuery;
						$stid =  oci_parse($con, $strQuery);
						$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
						if(!$exestd) return 0;
						$strQuery="";
						$k=0;
					}
					$k++;
				}
				return 1;
				 
				//return $strQuery; 
			}
			else // Single Row
			{
				$strQuery= "INSERT  \n";
				foreach( $arrValues as $rows)
				{
					$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$rows." \n";
				}
				$stid =  oci_parse($con, $strQuery);
				$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
				//return $strQuery; 
				 return 1;
			}
		}
		else
		{
			$tmpv=explode(")",$arrValues);
			
			for($i=0; $i<count($tmpv)-1; $i++)
			{
				$strQuery="";
				$strQuery= "INSERT  \n";
				if( strpos(trim($tmpv[$i]), ",")==0)
					$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1); 
				$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
	 
				$stid =  oci_parse($con, $strQuery);
				$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
				if (!$exestd) return "0"; 
			}
			return "1";
	
		}
		//return  $strQuery; die;
		//$strQuery .= "SELECT * FROM dual";
		//echo $strQuery;die;
		//$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;
	
	
	
		$stid =  oci_parse($con, $strQuery);
		$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
		if ($exestd) 
			return "1";
		else 
			return "0";
		die;
		
	}
	
	$con = connect();
	$storeWiseIsID = return_next_id("id", "inv_store_wise_qty_dtls", 1);
	
	$sql_bal=sql_select("select company_id, store_id, item_category, prod_id, 
	sum( (case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as balance_qnty,
	sum( (case when transaction_type in(1,4,5) then cons_amount else 0 end)-(case when transaction_type in(2,3,6) then cons_amount else 0 end)) as balance_amount 
	from inv_transaction where status_active=1 and item_category in (5,6,7,22,23)  
	group by company_id, store_id, item_category, prod_id");
	
	
	$field_array_store_insert="id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,inserted_by,insert_date"; 
	foreach($sql_bal as $row)
	{
		$cons_rate=0;
		if($row[csf("balance_qnty")]!=0)
		{
			$cons_rate=$row[csf("balance_amount")]/$row[csf("balance_qnty")];
			//$cons_rate=number_format($cons_rate,4,".","");
			$cons_rate=$cons_rate*1;
		}
		
		$data_array_store_insert[]= "(".$storeWiseIsID.",'".$row[csf("company_id")]."','".$row[csf("store_id")]."','".$row[csf("item_category")]."','".$row[csf("prod_id")]."','".$row[csf("balance_qnty")]."',".$cons_rate.",'".$row[csf("balance_amount")]."','1','".$pc_date_time."')";
		$storeWiseIsID++;
	}
	
	
	
	$rID = sql_insert2("inv_store_wise_qty_dtls",$field_array_store_insert,$data_array_store_insert,1);
	//echo $rID;die;
	
	if($rID)
	{
		oci_commit($con); 
		echo "Success";
	}
	else
	{
		oci_rollback($con); 
		echo "Failed";
	}
	
	?>
	