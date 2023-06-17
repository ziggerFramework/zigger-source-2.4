<div id="sub-tit">
    <h2>통합검색 설정</h2>
    <em><i class="fa fa-exclamation-circle"></i>통합검색 화면에 노출될 콘텐츠 설정</em>
</div>

<!-- article -->
<article>

    <p class="article-wait">
        <i class="fa fa-info-circle"></i>
        통합검색 결과에 노출될 콘텐츠를 추가하세요.
        <em>마우스로 드래그 하여 순서를 변경할 수 있습니다.</em>
    </p>

    <div class="searchbox">
        <div class="lef pb100">
            <form <?php echo $this->form(); ?>></form>
        </div>

        <div class="rig">
            <form <?php echo $this->form2(); ?>>

                <p class="search-wait">
                    <i class="fa fa-info-circle"></i>
                    검색 결과 화면에 노출될 콘텐츠를 설정하세요.
                    <em>좌측 박스에서 항목 생성 및 순서 설정 후<br />현재 박스에서 세부 설정이 가능합니다.</em>
                </p>

            </form>
        </div>
    </div>

</article>
