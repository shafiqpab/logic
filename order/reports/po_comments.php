<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <title>Cost Sheets</title>
	<link href="../../css/style_common.css" rel="stylesheet" type="text/css" media="screen" />
<? 
	include('../../includes/common.php');
	include('../../includes/array_function.php');
	$con = connect();
	$job_no=$_GET["job_no"];
	$sql ="SELECT approval_status as ap,sample_comments,sample_name FROM wo_po_sample_approval_info h, lib_sample s where job_no_mst='$job_no' and s.id=h.sample_type_id and (h.entry_form_id is null or h.entry_form_id=0) order by job_no_mst, sample_type_id" ;  
	$result = mysql_query($sql);
	disconnect($con);
?>

</head>

<body>
    <div align="center" style="width:450px;">
        <fieldset>
            <table border="0" rules="all" width="430" >
			<?
            while($info=mysql_fetch_array($result))
			{
				if($info[csf('sample_comments')]!="")
					echo "<tr><td><b>$sample_name</b></td></tr><tr><td>".$approval_status[$info[csf('ap')]]." : ".$info[csf('sample_comments')]."</td></tr>";
			}			
			?>
            </table>
        </fieldset>
    </div>
</body>
