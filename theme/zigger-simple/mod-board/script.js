ph_mod_board = {

    //
    // init
    //
    'init' : function() {

        this.cnum_allchk(); // 관리 checkbox 전체 선택
        this.get_ctr_popup(); // 관리 팝업
        this.get_member_popup(); // 작성자 정보 팝업
        this.use_notice_opt(); // 공지사항 옵션 체크시 답변알림 옵션 & 카테고리 숨김
        this.load_footer_list(); // view 하단 리스트 로드
        this.get_likes(); // view 좋아요 / 싫어요
        this.get_delete(); // 글 삭제
        this.get_view_comment(); // Comment
        this.get_board_data_temporary(); // 게시글 임시저장

    },

    //
    // 관리 checkbox 전체 선택
    //
    'cnum_allchk' : function() {

        $(document).on('click', 'body.mod-board .cnum_allchk', function() {
            var chked = $(this).is(':checked');
    
            if (chked) {
                $('body.mod-board input[name="cnum[]"]').prop('checked', true);
    
            } else {
                $('body.mod-board input[name="cnum[]"]').prop('checked', false);
            }
        });

    },

    //
    // 관리 팝업
    //
    'get_ctr_popup' : function() {

        var $ele = {
            'form' : '',
            'ctrpop' : '',
            'ctrpopBG' : ''
        }

        // open
        $(document).on('click', 'body.mod-board #list-ctr-btn', function(e) {
            e.preventDefault();

            var $form = $('body.mod-board #board-listForm');
            var cnum = $form.find(':checkbox[name="cnum[]"]:checked');

            if (cnum.length < 1) {
                zigalert('게시글을 한개 이상 선택해 주세요.');
                return false;
            }

            $('<div id="ctrpop-bg" data-no-tab-index></div>').appendTo('body.mod-board ');
            $('<div id="ctrpop" data-no-tab-index></div>').appendTo('body.mod-board ');
            $ele.ctrpop = $('body.mod-board #ctrpop');
            $ele.ctrpopBG = $('body.mod-board #ctrpop-bg');

            $.ajax({
                'type' : 'POST',
                'url' : MOD_BOARD_DIR + '/controller/pop/ctrl',
                'cache' : false,
                'data' : $form.serialize(),
                'dataType' : 'html',
                'success' : function(data) {
                    $ele.ctrpop.html(data).fadeIn(100);
                    $ele.ctrpopBG.fadeIn(100);

                    //접근성 위해 layer로 focus 이동.
                    $ele.ctrpop.find('.close').focus();
                }
            });
        });

        // close
        $(document).on('click', 'body.mod-board #ctrpop .close', function(e) {
            e.preventDefault();

            //접근성 위해 layer띄운 요소로 focus 이동.
            $('*[data-tab-index='+PH_NOW_TABINDEX+']').focus();

            $ele.ctrpop.remove();
            $ele.ctrpopBG.remove();
        });

        // 삭제 버튼을 클릭하는 경우
        $(document).on('click', 'body.mod-board #board_ctrpopForm #delete-btn', function(e) {
            e.preventDefault();
            zigconfirm('정말로 삭제 하시겠습니까?\r\n선택된 게시물이 많은 경우 시간이 다소 소요될 수 있습니다.', function(result) {
                if (result) {
                    $('body.mod-board #board_ctrpopForm input[name=type]').val('del');
                    $('body.mod-board #board_ctrpopForm').submit();
                }
            });
        });

        // 복사 버튼을 클릭하는 경우
        $(document).on('click', 'body.mod-board #board_ctrpopForm #copy-btn', function(e) {
            e.preventDefault();
            zigconfirm('답글은 복사 되지 않습니다.\n계속 진행 하시겠습니까?\n\n선택된 게시물이 많은 경우 시간이 다소 소요될 수 있습니다.', function(result) {
                if (result) {
                    $('body.mod-board #board_ctrpopForm input[name=type]').val('copy');
                    $('body.mod-board #board_ctrpopForm').submit();
                }
            });
        });

        // 이동 버튼을 클릭하는 경우
        $(document).on('click', 'body.mod-board #board_ctrpopForm #move-btn', function(e) {
            e.preventDefault();
            zigconfirm('답글은 부모글 없이 단독으로 이동되지 않습니다.\n계속 진행 하시겠습니까?\n\n선택된 게시물이 많은 경우 시간이 다소 소요될 수 있습니다.', function(result) {
                if (result) {
                    $('body.mod-board #board_ctrpopForm input[name=type]').val('move');
                    $('body.mod-board #board_ctrpopForm').submit();
                }
            });
        });

    },

    //
    // 작성자 정보 팝업
    //
    'get_member_popup' : function() {

        var $ele = {
            'form' : '',
            'mbpop' : '',
            'mbpopBG' : ''
        }

        // open
        $(document).on('click', 'body.mod-board *[data-profile]', function(e) {
            e.preventDefault();

            var mb_idx = $(this).data('profile');
            var board_id = $('body.mod-board input[name=board_id]').val();
            var thisuri = $(this).attr('href');

            $('<div id="mbpop-bg" data-no-tab-index></div>').appendTo('body.mod-board ');
            $('<div id="mbpop" data-no-tab-index></div>').appendTo('body.mod-board ');
            $ele.mbpop = $('body.mod-board #mbpop');
            $ele.mbpopBG = $('body.mod-board #mbpop-bg');

            $.ajax({
                'type' : 'GET',
                'url' : MOD_BOARD_DIR + '/controller/pop/writer?thisuri=' + thisuri,
                'cache' : false,
                'data' : {
                    'board_id' : board_id,
                    'mb_idx' : mb_idx
                },
                'dataType' : 'html',
                'success' : function(data) {
                    $ele.mbpop.html(data).fadeIn(100);
                    $ele.mbpopBG.fadeIn(100);

                    //접근성 위해 layer로 focus 이동.
                    $ele.mbpop.find('.close').focus();
                }
            });
        });

        // close
        $(document).on('click', 'body.mod-board #mbpop .close', function(e) {
            e.preventDefault();

            // 접근성 위해 layer 띄운 요소로 focus 이동.
            $('*[data-tab-index='+PH_NOW_TABINDEX+']').focus();

            $ele.mbpop.remove();
            $ele.mbpopBG.remove();
        });
        
    },

    //
    // 공지사항 옵션 체크시 답변알림 옵션 & 카테고리 숨김
    //
    'use_notice_opt' : function() {

        var use_notice_opt = function($this) {
            var chked = $this.is(':checked');
            if (chked) {
                $('body.mod-board #use_email').closest('label').hide();
                $('body.mod-board #category').prop('disabled', true);
        
            } else {
                $('body.mod-board #use_email').closest('label').show();
                $('body.mod-board #category').prop('disabled', false);
            }
        }

        var $opt = $('body.mod-board #use_notice');

        $opt.on({
            'click' : function() {
                use_notice_opt($opt);
            }
        });

        use_notice_opt($opt);
        
    },

    //
    // view 하단 리스트 로드
    //
    'load_footer_list' : function() {
        
        if ($('body.mod-board #board-ft-list').length > 0) {
            var $ftlist_wrap = $('body.mod-board #board-ft-list');
            var ftlist_board_id = $('body.mod-board #board-readForm input[name=board_id]').val();
            var ftlist_read = $('body.mod-board #board-readForm input[name=read]').val();
            var ftlist_page = $('body.mod-board #board-readForm input[name=page]').val();
            var ftlist_category = $('body.mod-board #board-readForm input[name=category]').val();
            var ftlist_where = $('body.mod-board #board-readForm input[name=where]').val();
            var ftlist_keyword = $('body.mod-board #board-readForm input[name=keyword]').val();
            var ftlist_thisuri = $('body.mod-board #board-readForm input[name=thisuri]').val();
            
            $ftlist_wrap.load(MOD_BOARD_DIR + '/controller/result/result?board_id=' + ftlist_board_id + '&mode=view&read=' + ftlist_read + '&page=' + ftlist_page + '&category=' + encodeURI(ftlist_category) + '&where=' + ftlist_where + '&keyword=' + ftlist_keyword + '&is_ftlist=Y&thisuri=' + ftlist_thisuri);
        }

    },

    //
    // view 좋아요 / 싫어요
    //
    'get_likes' : function() {

        $(document).on('click', 'body.mod-board #board-likes .btn-likes', function(e) {
            e.preventDefault();
            $form = $('body.mod-board #board-likes');
            $('input[name=mode]', $form).val('likes');
            $form.submit();
        });

        $(document).on('click', 'body.mod-board #board-likes .btn-unlikes', function(e) {
            e.preventDefault();
            $form = $('body.mod-board #board-likes');
            $('input[name=mode]', $form).val('unlikes');
            $form.submit();
        });

    },

    //
    // 글 삭제
    //
    'get_delete' : function() {

        $(document).on('click', 'body.mod-board #del-btn', function(e) {
            e.preventDefault();
            var thisuri = $('#board-readForm input[name=thisuri]').val();

            zigconfirm("이 글을 삭제 하시겠습니까?", function(result) {
                if (result) {
                    $('body.mod-board #board-readForm').attr({
                        'method' : 'POST',
                        'action' : PH_DOMAIN + thisuri + '?mode=delete'
                    }).submit();
                }
            });
        });

    },

    //
    // Comment
    //
    'get_view_comment' : function() {

        cmt_stat_mdf = false;

        // load
        view_cmt_load = function() {
            var comment_board_id = $('body.mod-board #board-readForm input[name=board_id]').val();
            var comment_read = $('body.mod-board #board-readForm input[name=read]').val();
            var comment_thisuri = $('body.mod-board #board-readForm input[name=thisuri]').val();
            
            $('body.mod-board #board-comment').load(MOD_BOARD_DIR + '/controller/comment/load?board_id=' + comment_board_id + '&read=' + comment_read + '&thisuri=' + comment_thisuri,function() {
                if ($('.g-recaptcha').length < 1) {
                    return false;
                }
                var comment_timer;
                var comment_load = function(){
                    if (g_recaptcha_captcha_act > 0) {
                        g_recaptcha_captcha(1);
                    } else {
                        if (comment_timer) {
                            clearTimeout(comment_timer);
                        }
                        comment_timer = setTimeout(comment_load, 200);
                    }
                }
                comment_load();
            });
        }
        view_cmt_load();

        // Comment 작성
        $(document).on('click', 'body.mod-board #commentForm .sbm', function(e) {
            e.preventDefault();

            var $form =  $('body.mod-board #commentForm');
            $form.find('input[name=mode]').val('write');
            $form.find('input[name=cidx]').val('');
            $form.submit();
        });

        // Comment 삭제
        $(document).on('click', 'body.mod-board #cmt-delete',function(e) {
            e.preventDefault();

            var $this = $(this);

            zigconfirm("댓글을 삭제 하시겠습니까?", function(result) {
                if (result) {
                    var $form =  $('body.mod-board #commentForm');
                    var cidx = $this.data('cmt-delete');
                    $form.find('input[name=mode]').val('delete');
                    $form.find('input[name=cidx]').val(cidx);
                    $form.submit();
                }
            });
        });

        // Comment 답글 작성
        var comm_re_form_idx = 0;
        var $comm_re_form;

        $(document).on('click','body.mod-board #cmt-reply', function(e) {
            e.preventDefault();

            if (cmt_stat_mdf) {
                $('body.mod-board li.comm-list-li .comment > p').show();
                $('body.mod-board #comm-re-form textarea[name=re_comment]').val('');
                cmt_stat_mdf = false;
            }

            var vis = $('> #comm-re-form', $(this).parents('li.comm-list-li')).is(':visible');

            if (comm_re_form_idx === 0) {
                $comm_re_form = $('body.mod-board #comm-re-form').html();
                comm_re_form_idx++;
            }

            if (!vis) {
                $('body.mod-board #comm-re-form').remove();
                $('body.mod-board #commentForm input[name=cidx]').val($(this).data("cmt-reply"));
                $(this).parents('li.comm-list-li').append('<div id="comm-re-form">' + $comm_re_form + '</div>');
                $(this).parents('li.comm-list-li').find('#comm-re-form').show();

                if ($('.g-recaptcha').length > 0) {
                    g_recaptcha_re_captcha(1);
                }
                cmt_stat_val = 'reply';

            } else {
                $('body.mod-board #comm-re-form').hide();
            }
        });

        // Comment 수정
        $(document).on('click', 'body.mod-board #cmt-modify', function(e) {
            e.preventDefault();
    
            if (cmt_stat_mdf) {
                $('body.mod-board li.comm-list-li').find('.comment').find('p').show();
                $('body.mod-board #comm-re-form textarea[name=re_comment]').val('');
                cmt_stat_mdf = false;
            }
    
            var vis = $('.comment #comm-re-form', $(this).parents('li.comm-list-li')).is(':visible');
            var comment = $('.comment > p', $(this).parents('li.comm-list-li')).text();
    
            if (!vis) {
                $comm_re_form = $('body.mod-board #comm-re-form').clone();
                $('body.mod-board #comm-re-form').remove();
                $('body.mod-board #commentForm input[name=cidx]').val($(this).data("cmt-modify"));
                $('.comment > p',$(this).parents('li.comm-list-li')).hide();
                $('.comment',$(this).parents('li.comm-list-li')).append($comm_re_form);
                $('#comm-re-form',$(this).parents('li.comm-list-li')).show();
                $('body.mod-board #comm-re-form textarea[name=re_comment]').val(comment);
                cmt_stat_mdf = true;
                cmt_stat_val = 'modify';
    
            } else {
                $('body.mod-board #comm-re-form').hide();
                $('.comment > p',$(this).parents('li.comm-list-li')).show();
                cmt_stat_mdf = false;
            }
        });
                
        //
        // Comment 답글 & 수정 Submit
        //
        $(document).on('click', 'body.mod-board #comm-re-form .re_sbm, body.mod-board #commentForm .re_sbm', function(e) {
            e.preventDefault();
            $('body.mod-board #commentForm input[name=mode]').val(cmt_stat_val);
            $('body.mod-board #commentForm').submit();
        });

    },

    //
    // 게시글 임시저장
    //
    'get_board_data_temporary' : function() {

        var $ele = {
            'btn_wrap' : $('body.mod-board #board-temporary-btnbox'),
            'add_btn' : $('body.mod-board #board-temporary-btnbox .save-btn'),
            'load_btn' : $('body.mod-board #board-temporary-btnbox .load-btn'),
            'form' : '',
            'temppop' : '',
            'temppopBG' : ''
        }
        
        // save
        $ele.add_btn.on('click', function(e) {
            e.preventDefault();

            $ele.form = $('body.mod-board #board-writeForm');

            zigconfirm('현재 작성된 글을 임시저장 하시겠습니까?', function(result) {
                if (result) {

                    // temporary form으로 변조
                    $ele.form.attr({
                        'ajax-action' : '/mod/board/controller/write/write-temporary-submit?rewritetype=submit',
                        'id' : 'board-writeTemporaryForm', 
                        'name' : 'board-writeTemporaryForm', 
                    }).off().submit();

                    // 원본 form 으로 다시 돌려 놓음
                    $ele.form.attr({
                        'ajax-action' : '/mod/board/controller/write/write-submit?rewritetype=submit',
                        'id' : 'board-writeForm', 
                        'name' : 'board-writeForm', 
                    });
                }
            });
        });

        // load
        $ele.load_btn.on('click', function(e) {
            e.preventDefault();

            $ele.form = $('body.mod-board #board-writeForm');

            var temp_hash = $ele.form.find('input:hidden[name="temp_hash"]').val();
            var board_id = $('body.mod-board input[name=board_id]').val();

            $('<div id="temppop-bg" data-no-tab-index></div>').appendTo('body.mod-board ');
            $('<div id="temppop" data-no-tab-index></div>').appendTo('body.mod-board ');
            $ele.temppop = $('body.mod-board #temppop');
            $ele.temppopBG = $('body.mod-board #temppop-bg');

            $.ajax({
                'type' : 'GET',
                'url' : MOD_BOARD_DIR + '/controller/pop/temporary',
                'cache' : false,
                'data' : {
                    'board_id' : board_id,
                    'temp_hash' : temp_hash
                },
                'dataType' : 'html',
                'success' : function(data) {
                    $ele.temppop.html(data).fadeIn(100);
                    $ele.temppopBG.fadeIn(100);

                    //접근성 위해 layer로 focus 이동.
                    $ele.temppop.find('.close').focus();
                }
            });
        });

        // close
        $(document).on('click', 'body.mod-board #temppop .close', function(e) {
            e.preventDefault();

            // 접근성 위해 layer 띄운 요소로 focus 이동.
            $('*[data-tab-index='+PH_NOW_TABINDEX+']').focus();

            $ele.temppop.remove();
            $ele.temppopBG.remove();
        });

        // 임시저장글 선택시 처리
        $(document).on('click', 'body.mod-board #temppop a.sbj', function(e) {
            e.preventDefault();

            var temp_hash = $(this).data('temphash');

            zigconfirm('기존 작성중인 글은 초기화됩니다.\r\n임시저장 글을 적용 하시겠습니까?', function(result) {
                if (result) {
                    window.document.location.href = '?mode=write&temp_hash=' + temp_hash
                }
            });
        });

        // 임시저장글 삭제시 처리
        $(document).on('click', 'body.mod-board #temppop a.remove-btn', function(e) {
            e.preventDefault();

            $ele.form = $('body.mod-board #board_temporaryForm');
            var temp_hash = $(this).data('temphash');
            
            zigconfirm('임시글을 삭제 하시겠습니까?', function(result) {
                if (result) {
                    $ele.form.find('input[name="temp_hash"]').val(temp_hash).submit();
                }
            });
        });

        // 임시저장글 삭제 후 처리
        get_board_after_tempdata_delete = function(hash) {
            
            // 팝업에서 해당 데이터 제거
            $ele.temppop = $('body.mod-board #temppop');
            $ele.temppop.find('a.remove-btn[data-temphash="' + hash + '"]').closest('tr').remove();

            if ($ele.temppop.find('a.remove-btn').length < 1) $ele.temppop.find('#board-nodata').show();

            // 글작성 화면에서 임시글 개수 갱신
            var nowCount = parseInt($ele.btn_wrap.find('.load-btn strong').text());
            $ele.btn_wrap.find('.load-btn strong').text(nowCount - 1);
            
        }
    }

}

$(function() {
    ph_mod_board.init();
});