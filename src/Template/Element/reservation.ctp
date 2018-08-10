<div class="to-popup con-popup">
    <span class="btn-close"></span>
    <div class="popup-cont popup-wrap">
        <h2>利用・予約方法</h2>
        <div class="cont-inner">
            <p>＜コンシェルジュ予約について＞</p>
            <p>・ご予約の際は、表示されているカレンダーから、〇がついている希望の時間帯をクリックし、お申し込みを行ってください。</p>
            <p>×と表示されている場合、該当の時間帯はご予約いただけません。</p>
            <p></p>
            <p>・現在表示されている中に希望するお時間がない場合、「すべて表示」を押下いただくことで、該当日の全時間帯の表示が可能です。</p>
            <p></p>
            <p>・ご予約は開始時間1分前（〜59分）までお申し込み可能ですが、</p>
            <p>開始時間までに申し込みを完了頂く必要がありますので、余裕をもってお申し込みください。</p>
            <p></p>
            <p>・ご予約は１か月４回までとさせていただいております。</p>
            <p></p>
            <p>＜コンシェルジュ相談当日について＞</p>
            <p>・当日は、開始時間になりましたら、受付に「お名前とご予約時間」をお申し出ください。</p>
            <p>受付スタッフがご案内させていただきます。</p>
            <p>（相談時間は１回40分程度とさせていただいております。）</p>
            <p></p>
            <p>＜予約のキャンセルについて＞</p>
            <p>・予約のキャンセルはStartup Hub TokyoのHPにログインいただきMyPageよりお手続きいただきますようお願いいたします。</p>
            <p>　※不測の事情等で開始時間に間に合わない場合、開始時間前までにお電話（03-6551-2470）にてご連絡ください。</p>
            <p>　※ご連絡なく10分以上遅れた場合にはやむを得ずキャンセルとさせていただきますので、ご了承ください。</p>
            <p></p>
            <p>＜その他＞</p>
            <p>・ご予約いただいたコンシェルジュが変更になる場合、メールにてご連絡させていただきます。</p>
            <p>　あらかじめご了承いただけますと幸いです。</p>
            <p>・その他のご不明点については、concierge@startup.tokyoまでメールでお問合せください。</p>
        </div>
        <div class="tcenter m-t15">
            <input id="close-popup" onclick = "disablePopup()" class="button" type="button" name=""  value="閉じる" />
        </div>
    </div>
    <!-- End popup-cont -->
</div>
<div class="background-popup"></div>
<script type="text/javascript">
    function disablePopup() {
        $('.to-popup').hide();
        $(".to-popup").fadeOut(300);
        $(".background-popup").fadeOut(300);
        $('body,html').css("overflow","auto");//enable scroll bar
    }
</script>
