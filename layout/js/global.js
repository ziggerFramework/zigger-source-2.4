ph_global_script = {

    //
    // init
    //
    'init' : function() {

        this.site_popup(); // Popup
        this.get_point_giftpop(); // 포인트 선물 팝업
        this.get_phonecheck(); // 휴대전화번호 SMS 인증 코드 발송 및 검증
        this.set_ui_datepicker(); // UI: datepicker
        this.get_kakao_address(); // 카카오 주소검색
        
    },

    //
    // Popup
    //
    'site_popup' : function() {

        var $ele = {
            'closeBtn' : $('.ph-pop .close'),
            'closeTodayBtn' : $('.ph-pop .close-today')
        }

        $ele.closeBtn.on({
            'click' : function(e) {
                e.preventDefault();
                $(this).parents('.ph-pop').remove();
            }
        });

        $ele.closeTodayBtn.on({
            'click' : function(e) {
                e.preventDefault();
                var idx = $(this).data('pop-idx');
                setCookie("ph_pop_"+idx, 1, 1);
                $(this).parents('.ph-pop').remove();
            }
        });
        
    },

    //
    // 포인트 선물 팝업
    //
    'get_point_giftpop' : function() {

        var $ele = {
            'form' : '',
            'sendpop' : '',
            'sendpopBG' : ''
        }

        // open
        $(document).on('click', '*[data-pointgift-send]', function(e) {
            e.preventDefault();

            var to_mb_id = $(this).data('pointgift-send');

            $('<div id="pointgift-send-bg" data-no-tab-index></div>').appendTo('body');
            $('<div id="pointgift-send" data-no-tab-index></div>').appendTo('body');
            $ele.sendpop = $('#pointgift-send');
            $ele.sendpopBG = $('#pointgift-send-bg');

            $.ajax({
                'type' : 'GET',
                'url' : PH_DIR + '/member/pointgift-pop',
                'cache' : false,
                'data' : {
                    'to_mb_id' : to_mb_id,
                },
                'dataType' : 'html',
                'success' : function(data) {
                    $ele.sendpop.html(data).fadeIn(100);
                    $ele.sendpopBG.fadeIn(100);

                    //접근성 위해 layer로 focus 이동.
                    $ele.sendpop.find('.close').focus();
                }
            });
        });

        // close
        $(document).on('click', '#pointgift-send .close', function(e) {
            e.preventDefault();

            //접근성 위해 layer띄운 요소로 focus 이동.
            $('*[data-tab-index='+PH_NOW_TABINDEX+']').focus();

            $ele.sendpop.remove();
            $ele.sendpopBG.remove();
        });
    },

    //
    // 휴대전화번호 SMS 인증 코드 발송 및 검증
    //
    'get_phonecheck' : function() {

        var $ele = {
            'wrap' : $('#get-phone-check-wrap'),
            'sendBtn' : $('#get-phone-check-wrap').find('.send-sms-code'),
            'confirmBtn' : $('#get-phone-check-wrap').find('.confirm-sms-code'),
            'confirmWrap' : $('#get-phone-check-wrap').find('#confirm-sms-code-wrap')
        }

        //send code
        Get_phonecheck_beforeConfirm = function() {
            alert('인증코드를 발송했습니다.\n코드 입력란에 입력해주세요.');
            $ele.confirmWrap.show();
            $ele.wrap.find('input[name=phone_code]').focus();
            $ele.sendBtn.text('SMS 인증코드 재발송');
        }

        $ele.sendBtn.on({
            'click' : function(e) {
                e.preventDefault();

                $.ajax({
                    'type' : 'POST',
                    'url' : PH_DIR + '/sign/phonechk_submit',
                    'cache' : false,
                    'async' : true,
                    'data' : {
                        'phone' : $ele.wrap.find('input[name=phone]').val()
                    },
                    'dataType' : 'html',
                    'success' : function(data) {
                        returnAjaxSubmit($ele.wrap, data);
                    }
                });

            }
        });

        //confirm code
        Get_phonecheck_afterConfirm = function() {
            alert('휴대전화 번호 인증에 성공 하였습니다.')
            $ele.wrap.find('input[name=phone]').attr('readonly', true);
            $ele.sendBtn.hide();
            $ele.confirmWrap.hide();
        }

        $ele.confirmBtn.on({
            'click' : function(e) {
                e.preventDefault();

                $.ajax({
                    'type' : 'POST',
                    'url' : PH_DIR + '/sign/phonechk_confirm_submit',
                    'cache' : false,
                    'async' : true,
                    'data' : {
                        'phone' : $ele.wrap.find('input[name=phone]').val(),
                        'phone_code' : $ele.wrap.find('input[name=phone_code]').val()
                    },
                    'dataType' : 'html',
                    'success' : function(data) {
                        returnAjaxSubmit($ele.wrap, data);
                    }
                });
            }
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
            $nowdate = $('#search-form input[name=nowdate]');
            $fdate = $('#search-form input[name=fdate]');
            $tdate = $('#search-form input[name=tdate]');
        
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
    // 카카오 주소검색
    //
    'get_kakao_address' : function() {

        if (typeof PH_KPOSTCODE_API_URL == 'undefined' || typeof PH_KPOSTCODE_API_URL == 'null') {
            return false;
        }

        var $ele = {
            'wrap' : $('#get-address-search-wrap'),
            'searchBtn' : $('#get-address-search-wrap').find('.search-address-btn'),
        }

        var script = document.createElement('script');
        script.src = PH_KPOSTCODE_API_URL;
        document.getElementsByTagName('head')[0].appendChild(script);

        var getSearch = function() {
            new daum.Postcode({
                oncomplete: function(data) {
                    var roadAddr = data.roadAddress;
                    var extraRoadAddr = '';
                    if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                        extraRoadAddr += data.bname;
                    }

                    if(data.buildingName !== '' && data.apartment === 'Y'){
                       extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }

                    if(extraRoadAddr !== ''){
                        extraRoadAddr = ' (' + extraRoadAddr + ')';
                    }

                    $ele.wrap.find('input[name=address1]').val(data.zonecode);
                    $ele.wrap.find('input[name=address2]').val(roadAddr);
                    $ele.wrap.find('input[name=address3]').val(data.jibunAddress);
                }
            }).open();
        }
        
        $ele.searchBtn.on({
            'click' : function() {
                getSearch();
            }
        });

    }

}

$(function() {
    ph_global_script.init();
});
