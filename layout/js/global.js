ph_global_script = {

    //
    // init
    //
    'init' : function() {

        this.site_popup(); // Popup
        this.get_phonecheck(); // 휴대전화번호 SMS 인증 코드 발송 및 검증
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
