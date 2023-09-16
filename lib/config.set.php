<?php

////////////////////////////////////////////////////
//
// headers
//
////////////////////////////////////////////////////

header('Content-Type: text/html; charset=UTF-8');
header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');
header('Expires: 0');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP 1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP 1.1
header('Pragma: no-cache'); // HTTP 1.0

////////////////////////////////////////////////////
//
// 기본 상수
//
////////////////////////////////////////////////////

define('USE_MOBILE', 'C'); // 반응형 모바일 사용 여부. (사용: 'Y',  비사용: 'N',  관리페이지 설정에 따름: 'C')

////////////////////////////////////////////////////
//
// 경로 상수
//
////////////////////////////////////////////////////

define('PH_MOD_DIR', PH_DIR.'/mod'); // Module 경로
define('PH_MOD_PATH', PH_PATH.'/mod'); // Module PHP 경로
define('PH_PLUGIN_DIR', PH_DIR.'/plugin'); // Plugin 경로
define('PH_PLUGIN_PATH', PH_PATH.'/plugin'); // Plugin PHP 경로
define('PH_DATA_DIR', PH_DIR.'/data'); // Data 경로
define('PH_DATA_PATH', PH_PATH.'/data'); // Data PHP 경로
define('PH_MANAGE_DIR', PH_DIR.'/manage'); // Manage 경로
define('PH_MANAGE_PATH', PH_PATH.'/manage'); // Manage PHP 경로
define('PH_SESSION_FILE_PATH', PH_PATH.'/data/sessions'); //파일세션 생성 경로

////////////////////////////////////////////////////
//
// 개발 환경 상수
//
////////////////////////////////////////////////////

// 정규식 상수
define('REGEXP_EMAIL', "/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/"); // 이메일
define('REGEXP_KOR', "/^[가-힣]+$/"); // 한글
define('REGEXP_NUM', "/^[0-9]+$/"); // 숫자
define('REGEXP_NEGANUM', "/^[0-9-]+$/"); // 숫자 (음수 포함)
define('REGEXP_ENG', "/^[a-zA-Z_]+$/"); // 영어
define('REGEXP_NICK', "/^[가-힣0-9a-zA-Z]+$/"); // 닉네임
define('REGEXP_PHONE', "/^[0-9]+$/"); // 연락처
define('REGEXP_ID', "/^[0-9a-zA-Z]+$/"); // ID
define('REGEXP_IDX', "/^[0-9a-zA-Z_]+$/"); // idx
define('REGEXP_IMG', "/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i"); // 이미지 추출

// 오류문구 상수
define('ERR_MSG_1', '정상적으로 접근 바랍니다. (ERR-CODE: err001)'); // 비정상적인 방법으로 접근한 경우
define('ERR_MSG_2', '사용할 수 없는 태그가 포함되어 있습니다. (ERR-CODE: err002)'); // 사용 금지 태그를 사용한 경우
define('ERR_MSG_3', 'Database에 접속할 수 없습니다. (ERR-CODE: err003)'); // DB Connect가 불가한 경우
define('ERR_MSG_4', 'Database를 찾을 수 없습니다. (ERR-CODE: err004)'); // DB Select_db 가 불가한 경우
define('ERR_MSG_5', 'DB Query가 올바르지 않습니다. (ERR-CODE: err005)'); // DB Query 문법이 잘못된 경우
define('ERR_MSG_6', 'DB Select Query가 올바르지 않습니다. (ERR-CODE: err006)'); // DB Select Query 문법이 잘못된 경우
define('ERR_MSG_7', '외부 SMTP 소켓 연결에 실패 했습니다. (ERR-CODE: err007)'); // 외부 SMTP 소켓 연결에 실패한 경우
define('ERR_MSG_8', '허용되지 않는 파일 유형입니다. (ERR-CODE: err008)'); // 허용되지 않는 파일 유형인 경우
define('ERR_MSG_9', '필수 변수 값이 전달되지 않았습니다. (ERR-CODE: err009)'); // 필수 변수 값이 전달되지 않은 경우
define('ERR_MSG_10', '접근 권한이 없습니다. (ERR-CODE: err010)'); // 페이지 접근 권한이 없는 경우
define('ERR_MSG_11', 'set_category_key 설정 없이 사용할 수 없는 명령어가 있습니다. (ERR-CODE: err011)'); // $func->set_category_key() 없이 $func->page_title() 등을 호출하려는 경우
define('ERR_MSG_12', 'page_navigator 에서 카테고리 key를 확인할 수 없습니다. (ERR-CODE: err012)'); // $func->page_navigator() 에서 카테고리 key를 인증할 수 없는 경우
define('ERR_MSG_13', 'Object Storage 처리에 실패 했습니다. (ERR-CODE: err013)'); // Object Storage 처리에 실패한 경우
define('ERR_MSG_14', 'Submit Controller의 파일 경로가 올바르지 않습니다. (ERR-CODE: err014)'); // Submit으로 존재하지 않는 Controller를 호출한 경우
define('ERR_MSG_15', 'Submit Controller의 Class 가 올바르지 않습니다. (ERR-CODE: err015)'); // Submit으로 존재하지 않는 Controller의 Class를 호출한 경우

// 경고문구 상수
define('SET_NODATA_MSG', '데이터가 존재하지 않습니다.'); // 데이터가 없는 경우 문구
define('SET_NOAUTH_MSG', '로그인 후에 이용 가능합니다.'); // 접근 권한이 없는 경우 문구
define('SET_ALRAUTH_MSG', '이미 로그인 되어 있습니다.'); // 접근 권한이 없는 경우 문구

// 개발 옵션 상수
define('SET_MAX_UPLOAD', 5242880); // Core 기본 업로드 최대 용량 (byte 단위)
define('SET_MAX_PFIMG_UPLOAD', 512000); // 회원 프로필 이미지 최대 용량 (byte 단위)
define('SET_IMAGE_QUALITY', 100); // IMAGE 압축 Quality (% 단위)
define('SET_SESS_LIFE', 86400); // 세션 유지 시간 (초 단위)
define('SET_SESS_FILE', false); // 파일세션 사용 여부 (true: 파일세션, false: DB세션)
define('SET_COOKIE_LIFE', 2592000); // 쿠키 최대 유지 시간 (초 단위)
define('SET_CURLOPT_CONNECTTIMEOUT', 10); // curl 통신시 connect timeout 제한 시간 설정 (초 단위)
define('SET_CURLOPT_SSL_VERIFYPEER', false); // curl 통신시 ssl 인증 활성화 유무
define('SET_CURLOPT_RETURNTRANSFER', true); // curl 통신시 결과를 문자로 반환할 것인지 설정
define('SET_LIST_LIMIT', 15); // 리스트 기본 노출 개수
define('SET_DATE', 'Y.m.d'); // 날짜 출력 format
define('SET_DATETIME', 'Y.m.d H:i:s'); // 날짜 + 시간 출력 format
define('SET_BLANK_IMG', PH_DOMAIN.'/layout/images/blank-tmb.jpg'); // 이미지가 없는 경우 대체될 blank 썸네일 경로
define('SET_INTDICT_TAGS', preg_replace("/\s+/", "", 'script, iframe, link, meta')); // 사용 금지 태그
define('SET_INTDICT_FILE', preg_replace("/\s+/", "", 'html, htm, shtm, phtml, php, php3, asp, jsp, cgi, js, css, conf, dot')); // 첨부 금지 확장명
define('SET_IMGTYPE', preg_replace("/\s+/", "", 'gif, jpg, jpeg, bmp, png')); // 사용 가능한 모든 이미지 종류
define('SET_MOBILE_DEVICE', preg_replace("/\s+/", "", 'iphone, lgtelecom, skt, mobile, samsung, nokia, blackberry, android, sony, phone')); // 모바일 디바이스 종류
define('SET_CACHE_HASH', '?cache='.md5(date('Ymd'))); // CSS, JS 갱신을 위한 캐시 값 설정 (매일 갱신)
define('SET_GRECAPTCHA_URL', array('https://www.google.com/recaptcha/api.js', 'https://www.google.com/recaptcha/api/siteverify?secret=')); // google recaptcha rest api url
define('SET_KPOSTCODE_URL', 'https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js'); // kakao postcode rest api url

// PHP ini 설정
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set("session.gc_maxlifetime", SET_SESS_LIFE);
ini_set('display_errors', 1);

////////////////////////////////////////////////////
//
// 플러그인 상수
// define('플러그인 상수명', '플러그인 폴더명');
//
////////////////////////////////////////////////////

define('PH_PLUGIN_CAPTCHA', 'securimage');
define('PH_PLUGIN_CKEDITOR', 'ckeditor4');
