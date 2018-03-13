<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

$current_inning = $_GET['inning'];
$recent_inning = 0;
$current_season = ($_GET['season'] != '' ) ? $_GET['season'] : '2018-first-half';

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"/>
        
        <title>Pinmaster - 종합 기록</title>

        <link rel="stylesheet" href="/css/pinmaster.css"/>
        <link rel="stylesheet" href="/css/bootstrap.css"/>
        
        <script src="/js/jquery-1.11.3.js"></script>
        <script src="/js/script.js"></script>
        <script src="/js/bootstrap.js"></script>
    </head>
    
    <body>
<?php include "main_menu.php"; ?>
        
        <div class="wrap">
            <div class="header-box">
                <div class="left-blank left"></div>
                <div class="title-box">
                    <span>종합 기록</span>
                </div>
            </div>
        
            <div class="content">
                <div class="left-menu left">
                    <div class="function-box">
                        <form name='season-form' method="get" action="accrue_result.php">
                            <span class="name">기간</span>
                            <select class="season-select form-control" name="season" onchange="this.form.submit()">
                                <option name="2017-second-half" value="2018-first-half" <?php echo ( $current_season == '2018-first-half' ) ? 'selected':''?>>2018 상반기</option>
                                <option name="2017-second-half" value="2017-second-half" <?php echo ( $current_season == '2017-second-half' ) ? 'selected':''?>>2017 하반기</option>
                                <option name="2017-first-half" value="2017-first-half" <?php echo ( $current_season == '2017-first-half' ) ? 'selected':''?>>2017 상반기</option>
                                <option name="2016-second-half" value="2016-second-half" <?php echo ( $current_season == '2016-second-half' ) ? 'selected':''?>>2016 하반기</option>
                                <option name="2016-first-half" value="2016-first-half" <?php echo ( $current_season == '2016-first-half' ) ? 'selected':''?>>2016 상반기</option>
                                <option name="2015-second-half" value="2015-second-half" <?php echo ( $current_season == '2015-second-half' ) ? 'selected':''?>>2015 하반기</option>
                            </select>
                        </form>
                    </div>
                    <div class="point-line"></div>
                    <div class="button-box">
                        <!--<a data-toggle="modal" data-target="#gameAddModal">정기전 추가</a>
                        <a data-toggle="modal" data-target="#userAddModal">회원 추가</a>
                        <a data-toggle="modal" data-target="#scoreAddModal">점수 추가</a>
                        <a href="/mysql/excel_insert.php">EXCEL INSERT</a>-->
                        <!--<a onclick="submitForm('team_delete');">선택삭제</a>-->
                    </div>
                </div>
                <div class="team-info">
<?php if( $current_season == '2015-second-half' ) { ?>
                    <div class="info-box">
                        <div class="title-wrap">
                            <span class="info-title"><span class="inning">2015 하반기</span> 종합 기록</span>
                            <span class="info-date">2015-7 ~ 2015-12</span>
                        </div>
                        
                        <div class="panel panel-default">
                            
                            <form id="team-list-form" method="post" action="../mysql/query.php">
                                <input type="hidden" name="query_header" value="delete_team" />
                                <!-- Table -->
                                <table class="table team-table">
                                    <thead>
                                        <tr>
                                            <th class="th-align">순위</th>
                                            <th class="th-align">이름</th>
                                            <th class="th-align score">98회</th>
                                            <th class="th-align score">99회</th>
                                            <th class="th-align score">100회</th>
                                            <th class="th-align total">총 핀</th>
                                            <th class="th-align handi">게임 수</th>
                                            <th class="th-align average">전체 평균</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php

$accrue_query = "SELECT pu.user_name, re1.average re1_ave, re2.average re2_ave, re3.average re3_ave, ps.total total, ps.game_count game_count, round(ps.total/ps.game_count, 1) ave
FROM
pinmaster_user pu,
(SELECT user_id, sum(total) total, sum(game_count) game_count FROM pinmaster_score WHERE game_id < 12 GROUP BY user_id HAVING game_count >= 9) ps LEFT JOIN
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = 9) re1
ON ps.user_id = re1.user_id LEFT JOIN 
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = 10) re2
ON ps.user_id = re2.user_id LEFT JOIN
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = 11) re3
ON ps.user_id = re3.user_id
WHERE pu.user_id = ps.user_id AND pu.active = 1
ORDER BY ave DESC";

$score_res = mysqli_query($connect, $accrue_query);
$rank = 0;
while($row = mysqli_fetch_array($score_res)) {
    $rank++;
?>
                                        <tr class="team-row" onclick="teamView('<?php echo $row['name']; ?>');" data-toggle="modal" data-target="#teamModal">
                                            <td align="center"><?php echo $rank;?></td>
                                            <td align="center" class="score-bold"><?php echo $row['user_name']; ?></td>
                                            <td align="center" class="<?php echo ($row['re1_ave'] >= 200 )?'red':''?>"><?php echo ($row['re1_ave'] == '')?'-':$row['re1_ave']; ?></td>
                                            <td align="center" class="<?php echo ($row['re2_ave'] >= 200 )?'red':''?>"><?php echo ($row['re2_ave'] == '')?'-':$row['re2_ave']; ?></td>
                                            <td align="center" class="<?php echo ($row['re3_ave'] >= 200 )?'red':''?>"><?php echo ($row['re3_ave'] == '')?'-':$row['re3_ave']; ?></td>
                                            <td align="center"><?php echo $row['total']; ?></td>
                                            <td align="center" class="score-bold"><?php echo $row['game_count'];?></td>
                                            <td align="center" class="score-bold average <?php echo ($row['ave'] >= 200 )?'red':''?>"><?php echo $row['ave']; ?></td>
                                        </tr>
<?php } ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                    
<?php } else if( $current_season == '2016-first-half' ) { ?>
                    
                    <div class="info-box">
                        <div class="title-wrap">
                            <span class="info-title"><span class="inning">2016 상반기</span> 종합 기록</span>
                            <span class="info-date">2016-1 ~ 2016-6</span>
                        </div>
                        
                        <div class="panel panel-default">
                            
                            <form id="team-list-form" method="post" action="../mysql/query.php">
                                <input type="hidden" name="query_header" value="delete_team" />
                                <!-- Table -->
                                <table class="table team-table">
                                    <thead>
                                        <tr>
                                            <!--<th class="check-box"><input type="checkbox" onclick="teamCheckAll();" id="team_check_all"/></th>-->
                                            <th class="th-align">순위</th>
                                            <th class="th-align">이름</th>
                                            <th class="th-align score">109회</th>
                                            <th class="th-align score">110회</th>
                                            <th class="th-align score">111회</th>
                                            <th class="th-align total">총 핀</th>
                                            <th class="th-align handi">게임 수</th>
                                            <th class="th-align average">전체 평균</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php

$accrue_query = "SELECT pu.user_name, re1.average re1_ave, re2.average re2_ave, re3.average re3_ave, ps.total total, ps.game_count game_count, round(ps.total/ps.game_count, 1) ave
FROM
pinmaster_user pu,
(SELECT user_id, sum(total) total, sum(game_count) game_count FROM pinmaster_score WHERE game_id >= 12 AND game_id < 24 GROUP BY user_id HAVING game_count >= 9) ps LEFT JOIN
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = 21) re1
ON ps.user_id = re1.user_id LEFT JOIN 
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = 22) re2
ON ps.user_id = re2.user_id LEFT JOIN
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = 23) re3
ON ps.user_id = re3.user_id
WHERE pu.user_id = ps.user_id AND pu.active = 1
ORDER BY ave DESC";

$score_res = mysqli_query($connect, $accrue_query);
$rank = 0;
while($row = mysqli_fetch_array($score_res)) {
    $rank++;
?>
                                        <tr class="team-row" onclick="teamView('<?php echo $row['name']; ?>');" data-toggle="modal" data-target="#teamModal">
                                            <!--<td class="check-box" onclick="event.cancelBubble = true;"><input type="checkbox" class="team_check" name="team_check[]" value="<?php echo $row['name']; ?>"/></td>-->
                                            <td align="center"><?php echo $rank;?></td>
                                            <td align="center" class="score-bold"><?php echo $row['user_name']; ?></td>
                                            <td align="center" class="<?php echo ($row['re1_ave'] >= 200 )?'red':''?>"><?php echo ($row['re1_ave'] == '')?'-':$row['re1_ave']; ?></td>
                                            <td align="center" class="<?php echo ($row['re2_ave'] >= 200 )?'red':''?>"><?php echo ($row['re2_ave'] == '')?'-':$row['re2_ave']; ?></td>
                                            <td align="center" class="<?php echo ($row['re3_ave'] >= 200 )?'red':''?>"><?php echo ($row['re3_ave'] == '')?'-':$row['re3_ave']; ?></td>
                                            <td align="center"><?php echo $row['total']; ?></td>
                                            <td align="center" class="score-bold"><?php echo $row['game_count'];?></td>
                                            <td align="center" class="score-bold average <?php echo ($row['ave'] >= 200 )?'red':''?>"><?php echo $row['ave']; ?></td>
                                        </tr>
<?php } ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
<?php } else if( $current_season == '2016-second-half' ) { ?>
                    
                    <div class="info-box">
                        <div class="title-wrap">
                            <span class="info-title"><span class="inning">2016 하반기</span> 종합 기록</span>
                            <span class="info-date">2016-7 ~ 2016-12</span>
                        </div>
                        
                        <div class="panel panel-default">
                            
                            <form id="team-list-form" method="post" action="../mysql/query.php">
                                <input type="hidden" name="query_header" value="delete_team" />
                                <!-- Table -->
                                <table class="table team-table">
                                    <thead>
                                        <tr>
                                            <!--<th class="check-box"><input type="checkbox" onclick="teamCheckAll();" id="team_check_all"/></th>-->
                                            <th class="th-align">순위</th>
                                            <th class="th-align">이름</th>
                                            <th class="th-align score">121회</th>
                                            <th class="th-align score">122회</th>
                                            <th class="th-align score">123회</th>
                                            <th class="th-align total">총 핀</th>
                                            <th class="th-align handi">게임 수</th>
                                            <th class="th-align average">전체 평균</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php

$accrue_query = "SELECT pu.user_name, re1.average re1_ave, re2.average re2_ave, re3.average re3_ave, ps.total total, ps.game_count game_count, round(ps.total/ps.game_count, 1) ave
FROM
    pinmaster_user pu,
    (SELECT user_id, sum(total) total, sum(game_count) game_count FROM pinmaster_score 
    WHERE game_id >= 24 AND game_id < 35 GROUP BY user_id HAVING game_count >= 9) ps LEFT JOIN
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = 33) re1
ON ps.user_id = re1.user_id LEFT JOIN 
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = 34) re2
ON ps.user_id = re2.user_id LEFT JOIN
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = 35) re3
ON ps.user_id = re3.user_id
WHERE pu.user_id = ps.user_id AND pu.active = 1
ORDER BY ave DESC";

$score_res = mysqli_query($connect, $accrue_query);
$rank = 0;
while($row = mysqli_fetch_array($score_res)) {
    $rank++;
?>
                                        <tr class="team-row" onclick="teamView('<?php echo $row['name']; ?>');" data-toggle="modal" data-target="#teamModal">
                                            <!--<td class="check-box" onclick="event.cancelBubble = true;"><input type="checkbox" class="team_check" name="team_check[]" value="<?php echo $row['name']; ?>"/></td>-->
                                            <td align="center"><?php echo $rank;?></td>
                                            <td align="center" class="score-bold"><?php echo $row['user_name']; ?></td>
                                            <td align="center" class="<?php echo ($row['re1_ave'] >= 200 )?'red':''?>"><?php echo ($row['re1_ave'] == '')?'-':$row['re1_ave']; ?></td>
                                            <td align="center" class="<?php echo ($row['re2_ave'] >= 200 )?'red':''?>"><?php echo ($row['re2_ave'] == '')?'-':$row['re2_ave']; ?></td>
                                            <td align="center" class="<?php echo ($row['re3_ave'] >= 200 )?'red':''?>"><?php echo ($row['re3_ave'] == '')?'-':$row['re3_ave']; ?></td>
                                            <td align="center"><?php echo $row['total']; ?></td>
                                            <td align="center" class="score-bold"><?php echo $row['game_count'];?></td>
                                            <td align="center" class="score-bold average <?php echo ($row['ave'] >= 200 )?'red':''?>"><?php echo $row['ave']; ?></td>
                                        </tr>
<?php } ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
<?php } else if( $current_season == '2017-first-half' ) { ?>
                    
                    <div class="info-box">
                        <div class="title-wrap">
                            <span class="info-title"><span class="inning">2017 상반기</span> 종합 기록</span>
                            <span class="info-date">2017-1 ~ 2017-6</span>
                        </div>
                        
                        <div class="panel panel-default">
                            
                            <form id="team-list-form" method="post" action="../mysql/query.php">
                                <input type="hidden" name="query_header" value="delete_team" />
                                <!-- Table -->
                                <table class="table team-table">
                                    <thead>
<?php
  
$get_inning_query = "SELECT game_inning FROM pinmaster_game_info WHERE game_id < 47 AND game_id > 43";
$inning_res = mysqli_query($connect, $get_inning_query);
?>
                                        <tr>
                                            <!--<th class="check-box"><input type="checkbox" onclick="teamCheckAll();" id="team_check_all"/></th>-->
                                            <th class="th-align">순위</th>
                                            <th class="th-align">이름</th>
<?php
while($row = mysqli_fetch_array($inning_res)) { ?>
                                            <th class="th-align score"><?php echo ($row['game_inning']); ?>회</th>
<?php } ?>
                                            <th class="th-align total">총 핀</th>
                                            <th class="th-align handi">게임 수</th>
                                            <th class="th-align average">전체 평균</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php

$accrue_query = "SELECT pu.user_name, re1.average re1_ave, re2.average re2_ave, re3.average re3_ave, ps.total total, ps.game_count game_count, round(ps.total/ps.game_count, 1) ave
FROM
pinmaster_user pu,
(SELECT user_id, sum(total) total, sum(game_count) game_count FROM pinmaster_score WHERE game_id >= 35 AND game_id < 47 GROUP BY user_id HAVING game_count >= 9) ps LEFT JOIN
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = 44) re1
ON ps.user_id = re1.user_id LEFT JOIN 
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = 45) re2
ON ps.user_id = re2.user_id LEFT JOIN
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = 46) re3
ON ps.user_id = re3.user_id
WHERE pu.user_id = ps.user_id AND pu.active = 1
ORDER BY ave DESC";

$score_res = mysqli_query($connect, $accrue_query);
$rank = 0;
while($row = mysqli_fetch_array($score_res)) {
    $rank++;
?>
                                        <tr class="team-row" onclick="teamView('<?php echo $row['name']; ?>');" data-toggle="modal" data-target="#teamModal">
                                            <!--<td class="check-box" onclick="event.cancelBubble = true;"><input type="checkbox" class="team_check" name="team_check[]" value="<?php echo $row['name']; ?>"/></td>-->
                                            <td align="center"><?php echo $rank;?></td>
                                            <td align="center" class="score-bold"><?php echo $row['user_name']; ?></td>
                                            <td align="center" class="<?php echo ($row['re1_ave'] >= 200 )?'red':''?>"><?php echo ($row['re1_ave'] == '')?'-':$row['re1_ave']; ?></td>
                                            <td align="center" class="<?php echo ($row['re2_ave'] >= 200 )?'red':''?>"><?php echo ($row['re2_ave'] == '')?'-':$row['re2_ave']; ?></td>
                                            <td align="center" class="<?php echo ($row['re3_ave'] >= 200 )?'red':''?>"><?php echo ($row['re3_ave'] == '')?'-':$row['re3_ave']; ?></td>
                                            <td align="center"><?php echo $row['total']; ?></td>
                                            <td align="center" class="score-bold"><?php echo $row['game_count'];?></td>
                                            <td align="center" class="score-bold average <?php echo ($row['ave'] >= 200 )?'red':''?>"><?php echo $row['ave']; ?></td>
                                        </tr>
<?php } ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
<?php } else if( $current_season == '2017-second-half' ) { ?>
                    
                    <div class="info-box">
                        <div class="title-wrap">
                            <span class="info-title"><span class="inning">2017 하반기</span> 종합 기록</span>
                            <span class="info-date">2017-7 ~ 2017-12</span>
                        </div>
                        
                        <div class="panel panel-default">
                            
                            <form id="team-list-form" method="post" action="../mysql/query.php">
                                <input type="hidden" name="query_header" value="delete_team" />
                                <!-- Table -->
                                <table class="table team-table">
                                    <thead>
<?php
  
$get_inning_query = "SELECT game_inning FROM pinmaster_game_info WHERE game_id > (SELECT COUNT(*) FROM pinmaster_game_info)-3";
    
$inning_res = mysqli_query($connect, $get_inning_query);
?>
                                        <tr>
                                            <!--<th class="check-box"><input type="checkbox" onclick="teamCheckAll();" id="team_check_all"/></th>-->
                                            <th class="th-align">순위</th>
                                            <th class="th-align">이름</th>
<?php
while($row = mysqli_fetch_array($inning_res)) { ?>
                                            <th class="th-align score"><?php echo ($row['game_inning']); ?>회</th>
<?php } ?>
                                            <th class="th-align total">총 핀</th>
                                            <th class="th-align handi">게임 수</th>
                                            <th class="th-align average">전체 평균</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php

$accrue_query = "SELECT pu.user_name, re1.average re1_ave, re2.average re2_ave, re3.average re3_ave, ps.total total, ps.game_count game_count, round(ps.total/ps.game_count, 1) ave
FROM
pinmaster_user pu,
(SELECT user_id, sum(total) total, sum(game_count) game_count FROM pinmaster_score WHERE game_id >= 47 AND game_id < 59 GROUP BY user_id HAVING game_count >= 9) ps LEFT JOIN
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = (SELECT COUNT(*) FROM pinmaster_game_info)-2) re1
ON ps.user_id = re1.user_id LEFT JOIN 
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = (SELECT COUNT(*) FROM pinmaster_game_info)-1) re2
ON ps.user_id = re2.user_id LEFT JOIN
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = (SELECT COUNT(*) FROM pinmaster_game_info)) re3
ON ps.user_id = re3.user_id
WHERE pu.user_id = ps.user_id AND pu.active = 1
ORDER BY ave DESC";

$score_res = mysqli_query($connect, $accrue_query);
$rank = 0;
while($row = mysqli_fetch_array($score_res)) {
    $rank++;
?>
                                        <tr class="team-row" onclick="teamView('<?php echo $row['name']; ?>');" data-toggle="modal" data-target="#teamModal">
                                            <!--<td class="check-box" onclick="event.cancelBubble = true;"><input type="checkbox" class="team_check" name="team_check[]" value="<?php echo $row['name']; ?>"/></td>-->
                                            <td align="center"><?php echo $rank;?></td>
                                            <td align="center" class="score-bold"><?php echo $row['user_name']; ?></td>
                                            <td align="center" class="<?php echo ($row['re1_ave'] >= 200 )?'red':''?>"><?php echo ($row['re1_ave'] == '')?'-':$row['re1_ave']; ?></td>
                                            <td align="center" class="<?php echo ($row['re2_ave'] >= 200 )?'red':''?>"><?php echo ($row['re2_ave'] == '')?'-':$row['re2_ave']; ?></td>
                                            <td align="center" class="<?php echo ($row['re3_ave'] >= 200 )?'red':''?>"><?php echo ($row['re3_ave'] == '')?'-':$row['re3_ave']; ?></td>
                                            <td align="center"><?php echo $row['total']; ?></td>
                                            <td align="center" class="score-bold"><?php echo $row['game_count'];?></td>
                                            <td align="center" class="score-bold average <?php echo ($row['ave'] >= 200 )?'red':''?>"><?php echo $row['ave']; ?></td>
                                        </tr>
<?php } ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
<?php } else if( $current_season == '2018-first-half' ) { ?>
                    
                    <div class="info-box">
                        <div class="title-wrap">
                            <span class="info-title"><span class="inning">2018 상반기</span> 종합 기록</span>
                            <span class="info-date">2018-1 ~ 2018-6</span>
                        </div>
                        
                        <div class="panel panel-default">
                            
                            <form id="team-list-form" method="post" action="../mysql/query.php">
                                <input type="hidden" name="query_header" value="delete_team" />
                                <!-- Table -->
                                <table class="table team-table">
                                    <thead>
<?php
  
$get_inning_query = "SELECT game_inning FROM pinmaster_game_info WHERE game_id > (SELECT COUNT(*) FROM pinmaster_game_info)-3";
    
$inning_res = mysqli_query($connect, $get_inning_query);
?>
                                        <tr>
                                            <!--<th class="check-box"><input type="checkbox" onclick="teamCheckAll();" id="team_check_all"/></th>-->
                                            <th class="th-align">순위</th>
                                            <th class="th-align">이름</th>
<?php
while($row = mysqli_fetch_array($inning_res)) { ?>
                                            <th class="th-align score"><?php echo ($row['game_inning']); ?>회</th>
<?php } ?>
                                            <th class="th-align total">총 핀</th>
                                            <th class="th-align handi">게임 수</th>
                                            <th class="th-align average">전체 평균</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php

$accrue_query = "SELECT pu.user_name, re1.average re1_ave, re2.average re2_ave, re3.average re3_ave, ps.total total, ps.game_count game_count, round(ps.total/ps.game_count, 1) ave
FROM
pinmaster_user pu,
(SELECT user_id, sum(total) total, sum(game_count) game_count FROM pinmaster_score WHERE game_id >= 59 GROUP BY user_id HAVING game_count >= 6) ps LEFT JOIN
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = (SELECT COUNT(*) FROM pinmaster_game_info)-2) re1
ON ps.user_id = re1.user_id LEFT JOIN 
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = (SELECT COUNT(*) FROM pinmaster_game_info)-1) re2
ON ps.user_id = re2.user_id LEFT JOIN
(SELECT pu.user_id user_id, pu.user_name user_name, average FROM pinmaster_score ps JOIN pinmaster_user pu ON ps.user_id = pu.user_id AND ps.game_id = (SELECT COUNT(*) FROM pinmaster_game_info)) re3
ON ps.user_id = re3.user_id
WHERE pu.user_id = ps.user_id AND pu.active = 1
ORDER BY ave DESC";

$score_res = mysqli_query($connect, $accrue_query);
$rank = 0;
while($row = mysqli_fetch_array($score_res)) {
    $rank++;
?>
                                        <tr class="team-row" onclick="teamView('<?php echo $row['name']; ?>');" data-toggle="modal" data-target="#teamModal">
                                            <!--<td class="check-box" onclick="event.cancelBubble = true;"><input type="checkbox" class="team_check" name="team_check[]" value="<?php echo $row['name']; ?>"/></td>-->
                                            <td align="center"><?php echo $rank;?></td>
                                            <td align="center" class="score-bold"><?php echo $row['user_name']; ?></td>
                                            <td align="center" class="<?php echo ($row['re1_ave'] >= 200 )?'red':''?>"><?php echo ($row['re1_ave'] == '')?'-':$row['re1_ave']; ?></td>
                                            <td align="center" class="<?php echo ($row['re2_ave'] >= 200 )?'red':''?>"><?php echo ($row['re2_ave'] == '')?'-':$row['re2_ave']; ?></td>
                                            <td align="center" class="<?php echo ($row['re3_ave'] >= 200 )?'red':''?>"><?php echo ($row['re3_ave'] == '')?'-':$row['re3_ave']; ?></td>
                                            <td align="center"><?php echo $row['total']; ?></td>
                                            <td align="center" class="score-bold"><?php echo $row['game_count'];?></td>
                                            <td align="center" class="score-bold average <?php echo ($row['ave'] >= 200 )?'red':''?>"><?php echo $row['ave']; ?></td>
                                        </tr>
<?php } ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
<?php } ?>
                </div>
            </div>
        </div>
    </body>
</html>