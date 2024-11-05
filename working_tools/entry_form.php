<?
// Abdul Barik Tipu--
include('../includes/common.php');
?>
<!DOCTYPE html>
<html>
<head>
    <style>
    *{margin: 0;padding: 0;outline: 0;}
    body{font-family: sans-serif; font-size: 14px;}
    table {
        border-collapse: collapse;
    }
    h2{margin: 10px; color: #CA932F}

    th {
        border: 1px solid #CA932F;
        background-color: #CA932F;
        padding: 1px;
        text-align: left;
    }
    td{
        border: 1px solid #f2f2f2;
        text-align: left;
        padding: 1px;
    }
</style>
</head>
<body>
    <div style="width: 100%">
    <h2 align="center">Entry Form Information</h2>    
    <?    
        ksort($entry_form);
        if (count($entry_form) > 30) 
            $entry_form_chunk_arr = array_chunk($entry_form, 30, true);            
        else 
            $entry_form_chunk_arr = $entry_form;

        foreach ($entry_form_chunk_arr as $chunk_key => $chunk_value)
        {
            ?>
            <div width="414" style="float: left; margin-left: 20px;">
                <table>
                    <tr>
                        <th width="50">No.</th>
                        <th width="364">Entry Form Name</th>
                    </tr>
                </table>
                <?
                foreach ($chunk_value as $key => $value)
                {
                    if ($i%2==1) $bgcolor="#DDD"; else $bgcolor="#FFF";
                    ?>
                   <!--  <div width="300"> -->
                        <table width="420">
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td width="50"><? echo $key; ?></td>
                                <td width="370"><? echo $value; ?></td>
                            </tr>
                        </table>
                    <!-- </div>      -->      
                    <?
                    $i++;
                }
                ?>
                <br>
            </div>            
            <?
        }
        ?>
    </div>
</body>
</html>
