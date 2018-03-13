<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/lib/excel_reader2.php';

$data = new Spreadsheet_Excel_Reader();
$data->setOutputEncoding('UTF-8'); 

$data->read("../data/parsing.xls");

for ($x = 1; $x <= count($data->sheets[0]["cells"]); $x++) {
    $inning = $data->sheets[0]["cells"][$x][1];
    $name = $data->sheets[0]["cells"][$x][2];
    $game1 = ($data->sheets[0]["cells"][$x][3] == '')? 0:$data->sheets[0]["cells"][$x][3];
    $game2 = ($data->sheets[0]["cells"][$x][4] == '')? 0:$data->sheets[0]["cells"][$x][4];
    $game3 = ($data->sheets[0]["cells"][$x][5] == '')? 0:$data->sheets[0]["cells"][$x][5];
    $handi = ($data->sheets[0]["cells"][$x][6] == '')? 0:$data->sheets[0]["cells"][$x][6];
    $total = $data->sheets[0]["cells"][$x][7];
    $ave = round($data->sheets[0]["cells"][$x][8], 1);
    $side = ($data->sheets[0]["cells"][$x][9] == '')? 0:$data->sheets[0]["cells"][$x][9];
    $game_count = $data->sheets[0]["cells"][$x][10];
    
    $query = "INSERT INTO pinmaster_score(game_id, user_id, game1, game2, game3, handicap, side, total, average, game_count)
 SELECT pgi.game_id, pu.user_id, ".$game1.", ".$game2.", ".$game3.", ".$handi.", ".$side.", ".$total.", ".$ave.", ".$game_count."
 FROM pinmaster_game_info pgi, pinmaster_user pu
 WHERE pgi.game_inning = '".$inning."' AND pu.user_name = '".$name."';";
    
    if( !mysqli_query($connect, $query) ) {
        die('Error : '.mysqli_error($connect));
    }
}

echo "<script>parent.parent.document.location.href= '../index.php'</script>";

?>