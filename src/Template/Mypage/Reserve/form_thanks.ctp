<h2 id="page-title" class="clearfix">
    <div class="page-title-inner">
        <span class="en">CONCIERGE</span>
        <span class="jp">コンシェルジュ</span>
    </div>
</h2>
<section id="concierge" class="section-container">
    <div class="section-inner">
        <div class="section-contents">
            <section class="main-cont">
                <!--<div class="thanks">-->
                    <p><?= $this->Flash->render() ?></p>
                <!--</div>-->
                <div class="tcenter">
                    <div class="btn-block btn-frm">
                        <?php
                        $url_back = '#';
                        if ( $his == '111' ) {
                            $url_back = $this->Url->build(["controller" => "Reserve", "action" => "index"]);
                        } else if ( $his == '104' ) {
                            $url_back = $this->Url->build(["prefix" => "concierge", "controller" => "Index", "action" => "index", "?" => ["id" => $data_save['reserves'], "dt" => date("Y-m-d", strtotime($data_save['work_date']))]]);
                        } else if ( $his == '107' ) {
                            $url_back = $this->Url->build(["prefix" => "concierge", "controller" => "Schedule", "action" => "month", "?" => ["id" => $data_save['reserves'], "dt" => date("Y-m-d", strtotime($data_save['work_date']))]]);
                        } else if ( $his == '108' ) {
                            $url_back = $this->Url->build(["prefix" => "concierge", "controller" => "Schedule", "action" => "week", "?" => ["id" => $data_save['reserves'], "dt" => date("Y-m-d", strtotime($data_save['work_date']))]]);
                        } else if ( $his == '106' ) {
                            $url_back = $this->Url->build(["prefix" => "concierge", "controller" => "Schedule", "action" => "day", "?" => ["id" => $data_save['reserves'], "dt" => date("Y-m-d", strtotime($data_save['work_date']))]]);
                        }
                        ?>
                        <div class="btn w160 h60 icon-none back btn-medium">
                            <div class="btn-inner clear">
                                <a class="overlay-text" id="reset-btn" href="<?=$url_back ?>">
                                    <span class="text en">戻る</span>
                                </a>
                                <div class="line"></div>
                                <div class="line2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>
