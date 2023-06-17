<div id="sub-tit">
    <h2>Dashboard</h2>
    <em><i class="fa fa-exclamation-circle"></i>좌측 메뉴에서 원하는 관리 메뉴를 선택하세요.</em>
</div>

<!-- article -->
<article>
    <div id="dashboard">
        <div class="inner">
            <div class="wrap">

                <!-- 최근 가입 회원 -->
                <div class="box">
                    <div class="tit">
                        <h4>최근 가입 회원</h4>
                        <a href="<?php echo PH_MANAGE_DIR; ?>/member/result" class="more"><i class="fa fa-plus"></i><p>더 보기</p></a>
                    </div>
                    <div class="cont">
                        <table>
                            <colgroup>
                                <col style="width: auto;" />
                                <col style="width: auto;" />
                                <col style="width: auto;" />
                                <col style="width: auto;" />
                                <col style="width: 70px;" />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>이름</th>
                                    <th>email</th>
                                    <th>가입일</th>
                                    <th>조회</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($print_arr['new_mb'] as $list) { ?>
                                <tr>
                                    <td class="tac"><strong><?php echo $list['mb_id']; ?></strong></td>
                                    <td class="tac"><?php echo $list['mb_name']; ?></td>
                                    <td class="tac"><?php echo $list['mb_email']; ?></td>
                                    <td class="tac"><?php echo $list['mb_regdate']; ?></td>
                                    <td class="tac"><a href="<?php echo PH_MANAGE_DIR; ?>/member/modify?idx=<?php echo $list['mb_idx']; ?>" class="btn1 small">조회</a></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php if ($list_cnt['new_mb'] < 1) { ?>
                        <p id="list-nodata" class="no-bd"><?php echo SET_NODATA_MSG; ?></p>
                        <?php } ?>
                    </div>
                </div>

                <!-- 최근 접속 회원 -->
                <div class="box">
                    <div class="tit">
                        <h4>최근 접속 회원</h4>
                        <a href="<?php echo PH_MANAGE_DIR; ?>/member/record" class="more"><i class="fa fa-plus"></i><p>더 보기</p></a>
                    </div>
                    <div class="cont">
                        <table>
                            <colgroup>
                                <col style="width: auto;" />
                                <col style="width: auto;" />
                                <col style="width: auto;" />
                                <col style="width: auto;" />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>level</th>
                                    <th>ip</th>
                                    <th>접속시간</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($print_arr['visit_mb'] as $list) { ?>
                                    <tr>
                                        <td class="tac"><strong><?php echo $list['mb_id']; ?></strong></td>
                                        <td class="tac"><?php echo $list['mb_level']; ?></td>
                                        <td class="tac"><?php echo $list['ip']; ?></td>
                                        <td class="tac"><?php echo $list['regdate']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php if ($list_cnt['visit_mb'] < 1) { ?>
                        <p id="list-nodata" class="no-bd"><?php echo SET_NODATA_MSG; ?></p>
                        <?php } ?>
                    </div>
                </div>

                <!-- 현재 접속 세션 -->
                <div class="box">
                    <div class="tit">
                        <h4>현재 접속 세션</h4>
                        <a href="<?php echo PH_MANAGE_DIR; ?>/member/session" class="more"><i class="fa fa-plus"></i><p>더 보기</p></a>
                    </div>
                    <div class="cont">
                        <table>
                            <colgroup>
                                <col style="width: auto;" />
                                <col style="width: auto;" />
                                <col style="width: auto;" />
                                <col style="width: auto;" />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>level</th>
                                    <th>ip</th>
                                    <th>마지막활동</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($print_arr['stat_mb'] as $list) { ?>
                                <tr>
                                    <td class="tac"><strong><?php echo $list['mb_id']; ?></strong></td>
                                    <td class="tac"><?php echo $list['mb_level']; ?></td>
                                    <td class="tac"><?php echo $list['ip']; ?></td>
                                    <td class="tac"><?php echo $list['regdate']; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php if ($list_cnt['visit_mb']<1) { ?>
                            <p id="list-nodata" class="no-bd"><?php echo SET_NODATA_MSG; ?></p>
                        <?php } ?>
                    </div>
                </div>

            </div>

            <div class="wrap news-wrap">

                <!-- 최근 소식 -->
                <div class="box">
                    <div class="tit">
                        <h4>최근 피드된 소식 <em><?php echo $news_newfeeds_count; ?></em></h4>
                        <a href="?view_dash_feed=read_all&page=<?php echo $page; ?>" class="btn2 small absbtn">전체 읽음으로 표시</a>
                    </div>
                    <div class="cont">
                        <input type="hidden" name="page" value="<?php echo $page; ?>" />
                        <table>
                            <colgroup>
                                <col style="width: 150px;" />
                                <col style="width: auto;" />
                                <col style="width: 200px;" />
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>이벤트 출처</th>
                                    <th>내용</th>
                                    <th>발생시간</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($print_arr['mng_feed'] as $list) {
                                $chked_cls = "";
                                $chked_em = "";
                                if ($list['chked'] == "N") {
                                    $chked_cls = "no-chked";
                                    $chked_em = "<em>N</em>";
                                }
                                ?>
                                <tr class="<?php echo $chked_cls; ?>">
                                    <td class="tac"><strong class="purple"><?php echo $list['msg_from']; ?></strong></td>
                                    <td><a href="#" target="_blank" class="view-feed-link" data-feed-idx="<?php echo $list['idx']; ?>" data-feed-href="<?php echo PH_DIR; ?><?php echo $list['href']; ?>"><?php echo $chked_em; ?><?php echo $list['memo']; ?></a></td>
                                    <td class="tac"><?php echo $list['regdate']; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <!-- paging -->
                        <div id="list-paging">
                            <?php echo $pagingprint['mng_feed']; ?>
                        </div>

                        <?php if ($list_cnt['mng_feed'] < 1) { ?>
                        <p id="list-nodata" class="no-bd">
                            아직 피드된 이벤트 <?php echo SET_NODATA_MSG; ?>
                            <br /><br />
                            웹사이트에서 회원가입, 문의 접수, 모듈 기능 이용 등<br />
                            각종 이벤트가 발생하는 경우 이 곳에 소식을 알립니다.
                        </p>
                        <?php } ?>

                    </div>
                </div>

            </div>
        </div>
    </div>
</article>
