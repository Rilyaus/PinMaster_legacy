<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

$query_header = $_REQUEST['query_header'];

if( $query_header == 'insert_game' ) {
    $game_insert_query =
        "INSERT INTO pinmaster_game_info(game_inning, game_date)
         VALUES('$_REQUEST[game_inning]', '$_REQUEST[game_date]')";

    if( !mysqli_query($connect, $game_insert_query) ) {
        die('Error : '.mysqli_error($connect));
    }
    echo "<script>parent.parent.document.location.href= '../index.php'</script>";
}
if( $query_header == 'insert_user' ) {
    $user_insert_query =
        "INSERT INTO pinmaster_user(user_name, user_sex)
         VALUES('$_REQUEST[user_name]', '$_REQUEST[user_sex]')";

    if( !mysqli_query($connect, $user_insert_query) ) {
        die('Error : '.mysqli_error($connect));
    }
    echo "<script>parent.parent.document.location.href= '../index.php'</script>";
}
if( $query_header == 'insert_score' ) {
    $side_check = 0;
    if( $_REQUEST['user_side'] == 1 ) {
        $side_check = 1;
    }
    
    $game_count = 0;
    $score = $_REQUEST['user_score'];
    $total_pin = 0;
    
    for( $i=0 ; $i < count($score) ; $i++ ) {
        if( $score[$i] > 0 ) { $game_count++; }
        $total_pin = $total_pin + $score[$i];
    }
    
    $total_pin = $total_pin + $_REQUEST['user_handi'];
    $average = $total_pin / $game_count;
    
    $user_insert_query =
"INSERT INTO pinmaster_score(game_id, user_id, game1, game2, game3, handicap, side, total, average, game_count)
 SELECT pgi.game_id, pu.user_id, ".$score[0].", ".$score[1].", ".$score[2].", ".$_REQUEST['user_handi'].", '".$side_check."', ".$total_pin.", ".$average.",
 ".$game_count."
 FROM pinmaster_game_info pgi, pinmaster_user pu
 WHERE pgi.game_inning = '".$_REQUEST['game_inning']."' AND pu.user_name = '".$_REQUEST['user_name']."';";

    if( !mysqli_query($connect, $user_insert_query) ) {
        die('Error : '.mysqli_error($connect));
    }
    echo "<script>parent.parent.document.location.href= '../index.php?inning=".$_REQUEST['game_inning']."'</script>";
}

?>