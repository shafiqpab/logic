<?
/*-------------------------------------------- Comments
Purpose			: 	Shift A and Shift B Off Day
Created by		:	Tipu 
Creation date 	: 	15-01-2020
*/
?>
<!DOCTYPE html>
<html>
<head>
	<title>Shift A and Shift B Off Day</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<style type="text/css">
		.shift_a_color{
			font-size: 20px; 
			background-color: #D2691E; 
			width: 248px; 
			display:inline-block;
		}
		.shift_b_color{
			font-size: 20px; 
			background-color: lightblue; 
			width: 248px; 
			display:inline-block;
		}
	</style>
</head>
<body">
	<br><br><br>
	<div class="container" align="center">
		<h1>Shift A and Shift B Off Day List</h1>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<label for="comment">From Date: </label>
			<input type="date" name="from_date" id="from_date">

			<label for="comment">To Date: </label>
			<input type="date" name="to_date" id="to_date">
			<input type="submit" name="submit" id="submit" value="Submit" class="btn btn-info"><br>
		</form>
		<br>
		
		<table width="500"> 
			<div>
			  <div class='shift_a_color'>Shift A Color</div>
			  <div class='shift_b_color'>Shift B Color</div>
			</div>
	        <tr bgcolor="gray"> 
	            <th style="width: 250px;">Shift Name</th> 
	            <th style="width: 250px;">Day and Date</th>
	        </tr> 
	  		<?php
		  		if(isset($_POST['submit'])!="")
				{
				    $from_date=$_POST['from_date'];
				    $to_date=$_POST['to_date'];
				    if ($from_date==""|| $to_date=="") 
				    {
				    	echo "<div style='background-color: red; width:500px;'>Please Select From Date and To Date</div>";die;
				    }
				    else
				    {
					    $tm1 = strtotime($from_date);
					    $tm2 = strtotime($to_date);
					    
					    $dt = Array ();
					    for($i=$tm1; $i<=$tm2;$i=$i+86400) 
					    {
					        if(date("w",$i) == 6) 
					        {
					        	$dt[] = date("l d-F-Y ", $i);
					        }
					    }

					    echo "<div style='background-color: green; width:500px;'>".'Found '.count($dt). ' Saturdays From '.$from_date.' To '.$to_date."</div>";
					    for($i=0;$i<count($dt);$i++) 
					    {
					    	if ($i%2==0) { $bgcolor="#D2691E"; $shift_name="A"; } 
					    	else { $bgcolor="lightblue"; $shift_name="B"; }; 
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>"> 
					            <td style="text-align: center; width: 248px;"><? echo $shift_name; ?></td> 
					            <td style="width: 248px;"><?php echo $dt[$i]."<br>"; ?></td>
					        </tr>
					        <?
					    }
				    }
			    }

			?>
	    </table> 
	</div>    
</body>
</html>