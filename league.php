<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

$rilyaus = $_GET['rilyaus'];

$current_inning = $_GET['inning'];
$recent_inning = 0;

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"/>
        
        <title>Pinmaster - 리그전 기록</title>

        <link rel="stylesheet" href="/css/pinmaster.css"/>
        <link rel="stylesheet" href="/css/bootstrap.css"/>
        
        <script src="/js/jquery-1.11.3.js"></script>
        <script src="/js/script.js"></script>
        <script src="/js/bootstrap.js"></script>
    </head>
    
    <body>
<?php include "main_menu.php";

$game_query = "SELECT game_inning, game_date FROM pinmaster_game_info ORDER BY game_inning DESC;";

$game_result = mysqli_query($connect, $game_query);

?>
        
        <div class="wrap">
            <div class="header-box">
                <div class="left-blank left"></div>
                <div class="title-box">
                    <span>정기전 기록</span>
                </div>
            </div>
        
            <div class="content">
                <div class="left-menu left">
                    <div class="function-box">
                        <form name='season-form' method="get" action="index.php">
                            <span class="name">정기전 회차</span>
                            <select class="season-select form-control" name="inning" onchange="this.form.submit()">
<?php
$i = 0;

while( $row = mysqli_fetch_array($game_result) ) {
    
    if( $i == 0 ) {
        $game_date = $row['game_date'];
        $recent_inning = $row['game_inning'];
        $current_inning = ($_GET['inning'] == '' ) ? $row['game_inning']:$_GET['inning'];
    }
    
    if( $_GET['inning'] == $row['game_inning'] ) {
        $game_date = $row['game_date'];
    }
    $i++;
                                ?>
                                <option name="game-inning<?php echo $row['game_inning']?>" value="<?php echo $row['game_inning']?>" <?php echo ( $current_inning == $row['game_inning'] ) ? 'selected':''?>><?php echo $row['game_inning']?>회 - <?php echo $row['game_date']?></option>
<?php } ?>
                            </select>
                        </form>
                    </div>
                    <div class="point-line"></div>
                    <div class="button-box">
<?php if( $_SERVER['REMOTE_ADDR'] == '223.194.46.32' || $_SERVER['REMOTE_ADDR'] == '125.128.36.208' ) { ?>
                        <a data-toggle="modal" data-target="#gameAddModal">정기전 추가</a>
                        <a data-toggle="modal" data-target="#userAddModal">회원 추가</a>
                        <a data-toggle="modal" data-target="#scoreAddModal">점수 추가</a>
                        <a href="/mysql/excel_insert.php">EXCEL INSERT</a>
<?php } ?>
                        <!--<a onclick="submitForm('team_delete');">선택삭제</a>-->
                    </div>
                </div>
                <div class="team-info">
                    <div class="info-box">
                        <div class="title-wrap">
                            <span class="info-title">핀마스터 제 <span class="inning"><?php echo $current_inning;?></span>회 정기전 점수</span>
                            <span class="info-date"><?php echo $game_date;?></span>
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
                                            <th class="th-align score">1 게임</th>
                                            <th class="th-align score">2 게임</th>
                                            <th class="th-align score">3 게임</th>
                                            <th class="th-align handi">핸디</th>
                                            <th class="th-align total">총점</th>
                                            <th class="th-align average">평균</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php

$score_query = "SELECT pu.user_name user_name, ps.game1 game1, ps.game2 game2, ps.game3 game3,
ps.handicap handi, ps.total total, ps.average ave
FROM pinmaster_score ps, pinmaster_user pu, pinmaster_game_info pgi
WHERE pgi.game_inning = ".$current_inning." AND pgi.game_id = ps.game_id AND pu.user_id = ps.user_id
ORDER BY ave DESC";

$score_res = mysqli_query($connect, $score_query);
$rank = 0;
while($row = mysqli_fetch_array($score_res)) {
    $rank++;
?>
                                        <tr class="team-row" onclick="teamView('<?php echo $row['name']; ?>');" data-toggle="modal" data-target="#teamModal">
                                            <!--<td class="check-box" onclick="event.cancelBubble = true;"><input type="checkbox" class="team_check" name="team_check[]" value="<?php echo $row['name']; ?>"/></td>-->
                                            <td align="center"><?php echo $rank;?></td>
                                            <td align="center" class="score-bold"><?php echo $row['user_name']; ?></td>
                                            <td align="center" class="<?php echo ($row['game1'] >= 200 )?'red':''?>"><?php echo ($row['game1'] == '0')?'-':$row['game1']; ?></td>
                                            <td align="center" class="<?php echo ($row['game2'] >= 200 )?'red':''?>"><?php echo ($row['game2'] == '0')?'-':$row['game2']; ?></td>
                                            <td align="center" class="<?php echo ($row['game3'] >= 200 )?'red':''?>"><?php echo $row['game3']; ?></td>
                                            <td align="center"><?php echo $row['handi']; ?></td>
                                            <td align="center" class="score-bold <?php echo ($row['total'] >= 600 )?'red':''?>"><?php echo $row['total']; ?></td>
                                            <td align="center" class="score-bold average <?php echo ($row['ave'] >= 200 )?'red':''?>"><?php echo $row['ave']; ?></td>
                                        </tr>
                                        
<?php } 

if( mysqli_num_rows($score_res) == 0 ) { ?>
                                        <tr class="blank-row">
                                            <td colspan="9" align="center">정기전 기록이 없습니다.</td>
                                        </tr>
<?php } ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>

<!-- Game Insert Modal -->
                    <div class="modal fade" id="gameAddModal" tabindex="-1" role="dialog" aria-labelledby="gameAddModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="gameModalLabel">정기전 추가</h4>
                                </div>
                                <form method="post" action="/mysql/query.php">
                                    <div class="modal-body">
                                        <input type="hidden" name="query_header" value="insert_game" />
                                        <div class="form-group">
                                            <label for="game-inning" class="control-label">정기전 타이틀 :</label>
                                            <input type="text" class="form-control" name="game_inning">
                                        </div>
                                        <div class="form-group">
                                            <label for="game-date" class="control-label">날짜 :</label>
                                            <input type="text" class="form-control" name="game_date">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">추가하기</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
<!-- User Insert Modal -->
                    <div class="modal fade" id="userAddModal" tabindex="-1" role="dialog" aria-labelledby="userAddModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="userModalLabel">회원 추가</h4>
                                </div>
                                
                                <form method="post" action="/mysql/query.php">
                                    <div class="modal-body">
                                        <input type="hidden" name="query_header" value="insert_user" />
                                        <div class="form-group">
                                            <label for="user-name" class="control-label">이름 :</label>
                                            <input type="text" class="form-control" name="user_name">
                                        </div>
                                        <div class="form-group">
                                            <label for="user-sex" class="control-label">성별 :</label>
                                            <select class="form-control" name="user_sex">
                                                <option value="남">남</option>
                                                <option value="여">여</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">추가하기</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
<!-- Score Insert Modal -->
                    <div class="modal fade" id="scoreAddModal" tabindex="-1" role="dialog" aria-labelledby="scoreAddModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="scoreModalLabel"><?php echo $current_inning;?>회 - 점수 추가</h4>
                                </div>
                                
                                <form method="post" action="/mysql/query.php">
                                    <div class="modal-body">
                                        <input type="hidden" name="query_header" value="insert_score" />
                                        <input type="hidden" name="game_inning" value="<?php echo $current_inning?>" />
                                        <div class="form-group">
                                            <label for="user-name" class="control-label">이름 :</label>
                                            <input type="text" class="form-control" name="user_name">
                                        </div>
                                        <div class="form-group">
                                            <label for="user-score1" class="control-label">1 게임 :</label>
                                            <input type="number" max="300" value="0" class="form-control" name="user_score[]">
                                        </div>
                                        <div class="form-group">
                                            <label for="user-score2" class="control-label">2 게임 :</label>
                                            <input type="number" max="300" value="0" class="form-control" name="user_score[]">
                                        </div>
                                        <div class="form-group">
                                            <label for="user-score3" class="control-label">3 게임 :</label>
                                            <input type="number" max="300" value="0" class="form-control" name="user_score[]">
                                        </div>
                                        <div class="form-group">
                                            <label for="user-handi" class="control-label">핸디 :</label>
                                            <input type="number" max="300" value="0" class="form-control" name="user_handi">
                                        </div>
                                        <div class="form-group">
                                            <label for="user-side" class="control-label">사이드 :</label>
                                            <input type="checkbox" class="form-control" value="1" name="user_side">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">추가하기</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>