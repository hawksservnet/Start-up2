<?php
namespace App\View\Helper;

use Cake\View\Helper\HtmlHelper;

/**
 *
 * ShtHelper for common paging,...
 * @author: Huynh
 */
class ShtHelper extends HtmlHelper
{
    /**
     * To make common paging
     * @author Huynh
     * @param array $option [$name,$id,$value,$checked, $disable, $label]
     * @return void
     */
    public function shtFrontCheckBox($option)
    {
        /*
         * HTML: /front-html/202.html
         */
        echo '<input type="checkbox" name="' . $option["name"] . '" id="' . $option["id"] . '"' . ' value="' . $option["value"] . '"' . ' ' . $option["checked"] . ' >' .
              '<label for="' . $option["id"] . '" class="cs-box">' . $option["label"] . '</label>';
        /*
         * HTML /front-html/109-reservationform-3.html
        echo '<span class="wpcf7-form-control-wrap privacy">' .
            '<span class="wpcf7-form-control wpcf7-checkbox wpcf7-validates-as-required checkbox-container">' .
            '<span class="wpcf7-list-item first last foucus_t">' .
            '<input type="checkbox" name="' . $option["name"] . '" id="' . $option["id"] . '"' . ' value="' . $option["value"] . '"' . ' ' . $option["checked"] . ' class="' . $option["disable"] . '" >' .
            '<span class="wpcf7-list-item-label">' . $option["label"] . '</span>' .
            '</span>' .
            '</span>' .
            '</span>';
        */
    }
    /**
     * To make common paging
     * @author Huynh
     * @param object $paginator Paginator Helper
     * @param array $option paging configuration
     * @return string
     */
    public function shtCommonPaging($paginator, $option)
    {
        $paginator->setTemplates([
            'nextActive' => '<a href="{{url}}" title="前へ">{{text}}</a>',
            'prevActive' => '<a href="{{url}}" title="次へ">{{text}}</a>',
            'nextDisabled' => '{{text}}',
            'prevDisabled' => '{{text}}',
        ]);
        $result = '<div class="pager"><p class="pagerlist">';
        $result = $result . $paginator->prev('&laquo; 前へ &nbsp;', ['escape' => false]);
        $totalPage = $paginator->total();
        $result = $result . ' | ';
        for ($p = 1; $p <= $totalPage; $p++) {
            if ($option['page'] != $p) {
                $url = $paginator->generateUrl([
                    'page' => $p
                ]);

                if (!empty($option['sort'])) {
                    $url = $paginator->generateUrl([
                        'sort' => $option['sort'],
                        'direction' => $option['direction'],
                        'page' => $p
                    ]);
                }
                if ($p == 1) {
                    if (strpos($url, '?') !== false) {
                        $url = $url . '&page=1';
                    } else {
                        $url = $url . '?page=1';
                    }
                }
                $result = $result .
                    '<a href="' .
                    $url . '">' .
                    $p .
                    '</a> | ';
            } else {
                $result = $result .
                    '<a role="button" class="active">' .
                    $p .
                    '</a> | ';
            }
        }
        $result = $result . $paginator->next('&nbsp; 次へ &raquo;', ['escape' => false]);
        $result = $result . '</p></div>';

        return $result;
    }

    /**
     * To make common paging
     * @author Huynh
     * @param object $paginator Paginator Helper
     * @param array $option paging configuration
     * @return string
     */
    public function shtCommonPagingCus($paginator, $option)
    {
        $paginator->setTemplates([
            'nextActive' => '<a href="{{url}}" title="前へ">{{text}}</a>',
            'prevActive' => '<a href="{{url}}" title="次へ">{{text}}</a>',
            'nextDisabled' => '{{text}}',
            'prevDisabled' => '{{text}}',
        ]);
        $result = '<div class="pager"><p class="pagerlist">';
        $result = $result . $paginator->prev('&laquo; 前へ &nbsp;', ['escape' => false]);
        $totalPage = $paginator->total();
        $result = $result . ' | ';
        for ($p = 1; $p <= $totalPage; $p++) {
            if ($option['page'] != $p) {
                $url = $paginator->generateUrl([
                    'page' => $p
                ]);

                if (!empty($option['sort'])) {
                    $url = $paginator->generateUrl([
                        'sort' => $option['sort'],
                        'direction' => $option['direction'],
                        'page' => $p
                    ]);
                } else {
                    $url = $paginator->generateUrl([
                        'sort' => '',
                        'direction' => '',
                        'page' => $p
                    ]);
                }
                if ($p == 1) {
                    if (strpos($url, '?') !== false) {
                        $url = $url . '&page=1';
                    } else {
                        $url = $url . '?page=1';
                    }
                }
                $result = $result .
                    '<a href="' .
                    $url . '">' .
                    $p .
                    '</a> | ';
            } else {
                $result = $result .
                    '<a role="button" class="active">' .
                    $p .
                    '</a> | ';
            }
        }
        $result = $result . $paginator->next('&nbsp; 次へ &raquo;', ['escape' => false]);
        $result = $result . '</p></div>';

        return $result;
    }
    /**
     * Get Version
     * @param string $filename File Name
     * @return void
     */
    public function getVersion($filename)
    {
        if (file_exists(WWW_ROOT . $filename)) {
            echo $filename . '?v=' . filemtime(WWW_ROOT . $filename);
        } else {
            echo $filename;
        }
    }
}
