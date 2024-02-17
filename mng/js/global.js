ph_manage_script = {

    //
    // init
    //
    'init' : function() {

        this.make_navigator() // Navigator
        this.set_navigator_action() // Navigator Action
        this.cnum_allchk() // list cnum all check
        this.label_active() // Label Active
        this.make_orderby() // Orderby
        this.set_ui_datepicker() // UI: datepicker
        this.main_tpl_script() // main.tpl.php
        this.siteinfo_theme_script() // siteinfo/theme.tpl.php
        this.siteinfo_sitemap_script() // siteinfo/sitemap.tpl.php
        this.mailler_send_script() // mailler/send.tpl.php
        this.sms_tomember_script() // sms/tomember.tpl.php

    },

    //
    // Navigator
    //
    'make_navigator' : function() {

        $('#side .tab a').on({
            'click' : function(e) {
                e.preventDefault();
                var idx = $(this).parents('li').index();
                $('#side .tab > li').eq(idx).addClass('on').siblings().removeClass('on');
                $('#gnb .menu').eq(idx).stop().fadeIn(100).siblings().hide();
            }
        });
        $('#gnb .menu > li > a').on({
            'click' : function(e) {
                e.preventDefault();
                $(this).next().stop().slideToggle(100).parents('li').toggleClass('on');
            }
        });

    },

    //
    // Navigator Action
    //
    'set_navigator_action' : function() {

        var href = window.document.location.href;
        href = PH_DIR + href.replace(PH_DOMAIN, '', href);
    
        if (href.indexOf('?') !== -1) {
            href = PH_DIR + href.replace(PH_DOMAIN, '', href);
            href = href.replace(href.substring(href.indexOf('?')), '');
        }
        if (href.indexOf('#') !== -1) {
            href = PH_DIR + href.replace(PH_DOMAIN, '', href);
            href = href.replace(href.substring(href.indexOf('#')), '');
        }
        if (href.indexOf('/manage/mod') !== -1) {
            $('#side .tab a[data-tab="mod"]').click();
        }
        if ($('#side #gnb .menu a[href="' + href + '"]').length > 0){
            $('#side #gnb .menu a[href="' + href + '"]')
            .closest('li').addClass('on')
            .closest('ul').prev('a').click();
    
        } else {
            $('#side #gnb .menu a[data-idx-href]').each(function() {
                var idx_href = $(this).data('idx-href');
                idx_href = idx_href.split('|');
                for (var i=0; i < idx_href.length; i++) {
                    if (href.indexOf(idx_href[i]) != -1) {
                        $(this)
                        .closest('li').addClass('on')
                        .closest('ul').prev('a').click();
                    }
                }
    
            });
        }

    },

    //
    // 관리 checkbox 전체 선택
    //
    'cnum_allchk' : function() {

        $(document).on('click', 'input:checkbox.cnum_allchk', function() {
            var chked = $(this).is(':checked');
    
            if (chked) {
                $('input:checkbox[name="cnum[]"]').prop('checked', true);
    
            } else {
                $('input:checkbox[name="cnum[]"]').prop('checked', false);
            }
        });

    },

    //
    // Label Active
    //
    'label_active' : function() {

        function label_active() {
            $('label.__label').each(function() {
                $this = $(this);
                if ($this.find('input').is(':checked')) {
                    $this.addClass('active');
                } else {
                    $this.removeClass('active');
                }
            });
        }
        function label_custom() {
            $('input[type=radio], input[type=checkbox]').each(function(i) {
                var $this = $(this);
                var $label = $this.parent('label');
                var label_name = ($(this).attr('name')) ? $(this).attr('name') : 'label';
                var label_for = label_name + '_' + Math.floor(Math.random() * 9999) + '_' + i;
        
                if ($label.length && !$label.hasClass('added_labelCustom') && !$label.hasClass('__label')) {
                    var $this_clone = $this.clone().attr('id', label_for);
                    $this.remove();
                    $label.addClass('added_labelCustom').attr('for', label_for).before($this_clone);
                }
            });
        }
        $(function() {
            label_custom();
        });
        setInterval(label_custom, 100);
        
        $(window).on({
            'load' : label_active
        });
        $(function() {
            $('label.__label').on({
                'click' : function() {
                    label_active();
                }
            });
        });

    },

    //
    // Orderby
    //
    'make_orderby' : function() {

        function get_query() {
            var url = document.location.href;
            var qs = url.substring(url.indexOf('?') + 1).split('&');
            for (var i = 0, result = {}; i < qs.length; i++) {
                qs[i] = qs[i].split('=');
                result[qs[i][0]] = decodeURIComponent(qs[i][1]);
            }
            return result;
        }
        
        $(function(){
            var qry = get_query();
        
            $('table thead th a').each(function() {
                var href = $(this).attr('href');
                if (href.indexOf('desc') !== -1) {
                    $(this).attr({
                        'title' : '내림차순 정렬'
                    });
                } else {
                    $(this).attr({
                        'title' : '오름차순 정렬'
                    });
                }
        
                if (qry['ordsc'] == 'asc') {
                    qry['order'] = 'desc';
                    qry['icon'] = '<i class="fas fa-caret-up"></i>';
                } else {
                    qry['order'] = 'asc';
                    qry['icon'] = '<i class="fas fa-caret-down"></i>';
                }
        
                if (href.indexOf('&ordtg='+qry['ordtg']+'&ordsc='+qry['order']) != -1) {
                    $(this).html($(this).text() + qry['icon']);
                }
            });
        });

    },

    //
    // UI: datepicker
    //
    'set_ui_datepicker' : function() {

        $(function() {
            $('input[datepicker]').datepicker();
            $.datepicker.setDefaults({
                dateFormat: 'yy-mm-dd',
                prevText: '이전 달',
                nextText: '다음 달',
                monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
                monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
                dayNames: ['일', '월', '화', '수', '목', '금', '토'],
                dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],
                dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
                showMonthAfterYear: true,
                yearSuffix: '년'
            });
        });
        
        $(function() {
            $nowdate = $('#list-sch input[name=nowdate]');
            $fdate = $('#list-sch input[name=fdate]');
            $tdate = $('#list-sch input[name=tdate]');
        
            $fdate.datepicker('option', 'maxDate', $nowdate.val());
            $tdate.datepicker('option', 'maxDate', $nowdate.val());
            $fdate.datepicker('option', 'onClose', function(selectedDate){
                $tdate.datepicker('option', 'minDate', selectedDate);
            });
            $tdate.datepicker('option', 'onClose', function(selectedDate) {
                $fdate.datepicker('option', 'maxDate', selectedDate);
            });
        });

    },

    //
    // main.tpl.php
    //
    'main_tpl_script' : function() {

        $('#dashboard .news-wrap a.view-feed-link').on({
            'click' : function(e) {
                e.preventDefault();
                var page = $('#dashboard .news-wrap input[name=page]').val();
                var idx = $(this).data('feed-idx');
                var href = $(this).data('feed-href');
                window.open(href);
                window.document.location.href = PH_MANAGE_DIR + "/main/dash?view_dash_feed=" + idx+'&page=' + page;
            }
        });

    },

    //
    // siteinfo/theme.tpl.php
    //
    'siteinfo_theme_script' : function() {

        $('#themeForm input[name=theme_slt]').on({
            'change' : function(e) {
                e.preventDefault();
                $('#themeForm').submit();
            }
        });

    },

    //
    // siteinfo/sitemap.tpl.php
    //
    'siteinfo_sitemap_script' : function(functions) {

        var $wait_box = $('#sitemapMofidyForm .sitemap-wait').clone();
        var list_arr = new Array;
        list_arr[0] = {
            'axis' : 'y',
            'stop' : function() {
                $('#sitemapListForm input[name=type]').val('modify');
                list_refrs();
            }
        }
        var $list_ele = new Array;

        var get_sortable = function() {
            $list_ele[0] = $('#sitemapListForm .sortable');
            $list_ele[1] = $('#sitemapListForm .st-2d');
            $list_ele[2] = $('#sitemapListForm .st-3d');
            $list_ele[0].sortable(list_arr[0]).disableSelection();
            $list_ele[1].sortable(list_arr[0]).disableSelection();
            $list_ele[2].sortable(list_arr[0]).disableSelection();
        }

        var list_charlen = function(str) {
            if (escape(str).length < 3) {
                var min = 4 - escape(str).length;
                var output = '';
                for (var i = 0; i < min; i++) {
                    output += '0';
                }
            }
            output = output + str;
            return output;
        }

        var request_sbm = function() {
            $('#sitemapListForm').submit();
        }

        var list_reload = function() {
            $('#sitemapListForm').load(PH_MANAGE_DIR + '/siteinfo/sitemapList', function(){
                get_sortable();
                $('.sitemap').removeClass('with-ajax-cover');
            });
        }
        list_reload();

        var list_refrs = function() {
            var eqidx = new Array();
            var eqval = new Array();

            $('.sitemap').addClass('with-ajax-cover');
            $('#sitemapListForm').append('<div class="ajax-cover"></div>');

            get_sortable();

            $list_ele[0].find('input[name="caidx[]"]').each(function() {
                var $this = $(this);
                var depth = $this.data('depth');

                if ($this.data('depth') === 1) {
                    eqidx[0] = parseInt($this.index('input[name="caidx[]"][data-depth=1]')) + 1;
                    eqval[0] = list_charlen(eqidx[0]);
                    if (eqidx[0]!=0) {
                        $this.val(eqval[0]);
                    }
                }
                if ($this.data('depth') === 2) {
                    eqidx[1] = parseInt($this.parents('li').index()) + 1;
                    eqval[1] = eqval[0] + list_charlen(eqidx[1]);
                    if (eqidx[1]!=0) {
                        $this.val(eqval[1]);
                    }
                }
                if ($this.data('depth') === 3) {
                    eqidx[2] = parseInt($this.parents('li').index()) + 1;
                    eqval[2] = eqval[1] + list_charlen(eqidx[2]);
                    if (eqidx[2] !== 0) {
                        $this.val(eqval[2]);
                    }
                }
            });

            $('input[name="idx[]"]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('sitemap_new_added_ele');

                } else {
                    $(this).removeClass('sitemap_new_added_ele');
                }
            });

            $('#sitemapListForm input[name=new_caidx]').val($('.sitemap_new_added_ele').eq(0).next('input[name="caidx[]"]').val());
            request_sbm();
            $('#sitemapListForm input[name=type]').val('add');
        }

        var secc_modify = function() {
            alert('성공적으로 수정 되었습니다.');
            list_reload();
        }

        if (functions) {
            eval(functions+'()');
            return false;
        }

        $(document).on('click', '#sitemapListForm .add-1d', function(e) {
            e.preventDefault();
            var html = '<div class="st-1d"><h4><a href="#" class="modify-btn"><input type="hidden" name="idx[]" value="" /><input type="hidden" name="caidx[]" value="" data-depth="1" /><input type="hidden" name="org_caidx[]" value="" />새로운 1차 카테고리</a><i class="fa fa-trash-alt st-del del-1d"></i></h4><div class="in"><ul class="st-2d"></ul><span class="st-no-cat">아직 생성된 2차 카테고리가 없습니다.</span></div><a href="#" class="st-add add-2d"><i class="fa fa-plus"></i> 2차 카테고리 추가</a></div>';
            $(html).hide().appendTo($('.sortable'));
            list_refrs();
        });

        $(document).on('click', '#sitemapListForm .add-2d', function(e) {
            e.preventDefault();
            var $this = $(this);
            var html = '<li><p><a href="#" class="modify-btn"><input type="hidden" name="idx[]" value="" /><input type="hidden" name="caidx[]" value="" data-depth="2" /><input type="hidden" name="org_caidx[]" value="" />새로운 2차 카테고리</a><i class="fa fa-plus add-3d"></i><i class="fa fa-trash-alt st-del del-2d"></i></p><ul class="st-3d"></ul></li>';
            $(html).hide().appendTo($this.parents('.st-1d').find('.st-2d')).fadeIn(200, function(){
                $this.parents('.st-1d').find('.st-2d').sortable(list_arr[0]);
                list_refrs();
            });
            $(this).parents('.st-1d').find('.st-no-cat').remove();
        });

        $(document).on('click', '#sitemapListForm .add-3d', function(e) {
            e.preventDefault();
            var $this = $(this);
            var html = '<li><p><a href="#" class="modify-btn"><input type="hidden" name="idx[]" value="" /><input type="hidden" name="caidx[]" value="" data-depth="3" /><input type="hidden" name="org_caidx[]" value="" />새로운 3차 카테고리</a><i class="fa fa-trash-alt st-del del-3d"></i></p></li>';
            $(html).hide().appendTo($this.parents('li').find('.st-3d')).fadeIn(200, function() {
                $this.parents('li').find('.st-3d').sortable(list_arr[0]);
                list_refrs();
            });
        });

        $(document).on('click', '#sitemapListForm .del-1d', function(e) {
            e.preventDefault();
            if (!confirm('삭제하는 경우 복구할 수 없습니다.\n\n그래도 진행 하시겠습니까?')) {
                return false;
            }
            var $this = $(this);
            $this.parents('.st-1d').remove();
            $('#sitemapListForm input[name=type]').val('modify');
            $('#sitemapMofidyForm').empty().append($wait_box);
            list_refrs();
        });

        $(document).on('click', '#sitemapListForm .del-2d', function(e) {
            e.preventDefault();
            if (!confirm('삭제하는 경우 복구할 수 없습니다.\n\n그래도 진행 하시겠습니까?')) {
                return false;
            }
            var $this = $(this);
            $this.parent().parent('li').remove();
            $('#sitemapListForm input[name=type]').val('modify');
            $('#sitemapMofidyForm').empty().append($wait_box);
            list_refrs();
        });

        $(document).on('click', '#sitemapListForm .del-3d', function(e) {
            e.preventDefault();
            if (!confirm('삭제하는 경우 복구할 수 없습니다.\n\n그래도 진행 하시겠습니까?')) {
                return false;
            }
            var $this = $(this);
            $this.parent().parent('li').remove();
            $('#sitemapListForm input[name=type]').val('modify');
            $('#sitemapMofidyForm').empty().append($wait_box);
            list_refrs();
        });

        $(document).on('click', '#sitemapListForm a.modify-btn', function(e) {
            e.preventDefault();
            var idx = $(this).find('input[name="idx[]"]').val();
            $('#sitemapMofidyForm').hide().load(PH_MANAGE_DIR + '/siteinfo/sitemapModify?idx=' + idx).fadeIn(100);
        });

    },

    //
    // mailler/send.tpl.php
    //
    'mailler_send_script' : function() {

        $(document).on('click', '#sendmailForm input[name=type]', function(e) {
            var type = $(this).val();
            $('#sendmailForm table tr.hd-tr[data-type='+type+']').show().siblings('.hd-tr').hide();
        });

        $(document).on('change', '#sendmailForm select[name=tpl]', function(e) {
            var template = $(this).val();
            var soruce = '';
            if (typeof tpl_opts_source[template] !== 'undefined') soruce = tpl_opts_source[template];
            
            CKEDITOR.instances.html.setData(soruce);
        });

    },

    //
    // sms/tomember.tpl.php
    //
    'sms_tomember_script' : function() {

        //내용 byte수 체크
        var get_sms_memobyte = function(val) {
            var bytes = 0;
            for (var i = 0; i < val.length; i++) {
                if (escape(val.charAt(i)).length == 6) {
                    bytes++;
                }
                bytes++;
            }

            $btn_txt = $('#smsSendForm button[type=submit] > strong');

            if (bytes > 80) {
                $btn_txt.text('LMS');
            } else {
                $btn_txt.text('SMS');
            }

            if ($('#smsSendForm input[name=image]').val() != '') {
                $btn_txt.text('MMS');
            }

            return bytes;
        }
        var get_sms_timer;
        var get_sms_printbyte = function() {
            if (get_sms_timer) {
                clearTimeout(get_sms_timer);
            }
            $('#smsSendForm .print_byte > strong > strong').text(get_sms_memobyte($('#smsSendForm #memo').val()));
            get_sms_timer = setTimeout(get_sms_printbyte, 100);
        }
        if ($('#smsSendForm .print_byte').length > 0) {
            get_sms_timer = setTimeout(get_sms_printbyte, 100);
        }

        //수신 범위 지정
        $(document).on('click', '#smsSendForm input[name=type]', function(e) {
            var type = $(this).val();
            $('#smsSendForm table tr.hd-tr[data-type='+type+']').show().siblings('.hd-tr').hide();
        });

        //예약 발송 설정
        var get_sms_resv = function(type) {
            if (type == 'show') {
                $('#smsSendForm .resv-wrap *').attr('disabled', false);

            } else if (type == 'hide') {
                $('#smsSendForm .resv-wrap *').attr('disabled', true);
            }
        }
        $(document).on('click', '#smsSendForm .resv-btn', function(e) {
            var chked = $(this).prev(':checkbox').prop('checked');
            if (chked == false) {
                get_sms_resv('show');

            } else {
                get_sms_resv('hide');
            }
        });
        get_sms_resv('hide');

    }

}

$(function() {
    ph_manage_script.init();
});