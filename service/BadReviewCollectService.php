<?php

namespace app\service;

use Yii;
use app\libraries\MyHelper;
use yii\helpers\ArrayHelper;
use app\libraries\QueryList;

class BadReviewCollectService
{
    public $asin;
    public $lastDate;
    public $isFirst;
    public $error;

    private $_maxPageNum;
    private $_error = [];
    private $_allowMaxPageNum = 2;
    private $_host = 'https://www.amazon.com';

    public function __construct($asin, $lastDate = null, $isFirst = false)
    {
        $this->asin = $asin;
        $this->lastDate = $lastDate;
        $this->isFirst = $isFirst;
    }

    public function getMultiDatas()
    {
        // 休眠0.07s
        usleep(70000);
        try {
            $datas = $this->_queryListData($this->_getUrl());
        } catch (\Exception $e) {
            // 第一页抓取出错直接终止
            $this->error = (string)$e;
            return false;
        }

        // 过滤旧数据,判断是否产生了新数据,新数据是否有多页
        if ($this->isFirst || !$this->_hasNewDatas($datas)) {
            return $datas;
        }

        // 获取多个分页数据
        for ($pageNum = 2; $pageNum <= $this->_maxPageNum; $pageNum++) {
            // 休眠0.07s
            usleep(70000); 
            try {
                $res = $this->_queryListData($this->_getUrl($pageNum));
            } catch (\Exception $e) {
                // 下一页采集出错则退出
                break;                
            }
            // 无新数据退出
            if (!$this->_hasNewDatas($res)) { 
                $datas = array_merge($datas, $res);
                break;
            }
            $datas = array_merge($datas, $res);
        }

        // 去重（可能由于抓取延迟导致数据重复）
        $datas = array_values((ArrayHelper::index($datas, 'review_id')));
        return $datas;
    }

    private function _hasNewDatas(array &$datas)
    {
        if (!$datas) {
            return false;
        }

        $minDate = end($datas)['review_date'];
        $maxDate = reset($datas)['review_date'];
        if ($maxDate <= $this->lastDate) {
            $datas = [];
            return false;
        }
        if ($minDate < $this->lastDate) {
            foreach ($datas as $k => $data) {
                if ($data['review_date'] <= $this->lastDate) {
                    unset($datas[$k]);
                }
            }
            return false;
        }
        return true;
    }

    private function _queryListData($url)
    {
        $queryList = new QueryList($url);

        // review_data
        $reviewReg = array(
            'reply_url' => array('.review-title', 'href'),
            'title' => array('.review-title', 'html'),
            'buyer_url' => array('.review-byline .author', 'href'),
            'buyer' => array('.review-byline .author', 'html'),
            'review_date' => array(
                '.review-date', 
                'html', 
                function($date) {
                    // 格式化日期
                    $date = ltrim($date, 'on');
                    return date('Y-m-d', strtotime($date));
                }
            ),
            'review_info' => array('.review-text', 'html'),
            'star' => array('.a-icon-star', 'class', function ($starClass) {
                $starClass = preg_replace('/\s+/', ' ', $starClass);
                $arr = explode(' ', $starClass);
                foreach ($arr as $one) {
                    if (strpos($one, 'a-star-') !== false) {
                        list(,,$star) = explode('-', $one);
                        return $star;
                    }
                }
            }),
        );
        $reviewDatas = $queryList->query($reviewReg, '#cm_cr-review_list .review');

        // image_url
        $imageReg = array(
            'image_url' => array('img', 'src'),
        );
        $imageUrl = $queryList->query($imageReg, '.product-image a');
        $imageUrl = reset(array_column($imageUrl, 'image_url'));

        // page_num
        if (!$this->_maxPageNum) {
            $pageReg = array(
                'pageNum' => array('a', 'html', function ($num) {
                    return (int)$num;
                }),
            );
            $pageNums = $queryList->query($pageReg, '#cm_cr-pagination_bar .a-pagination li');
            $pageNums = array_column($pageNums, 'pageNum');
            $maxPage = $pageNums ? max($pageNums) : 1;
            // 设则最大分页
            $this->_maxPageNum = min($maxPage, $this->_allowMaxPageNum);
        }

        foreach ($reviewDatas as &$review) {
            list(,,,,$review['profile_id']) = explode('/', $review['buyer_url']);
            list(,,,$review['review_id']) = explode('/', $review['reply_url']);
            $review['image_url'] = $imageUrl;
            $review['asin'] = $this->asin;
            $review['reply_url'] = $this->_host . $review['reply_url'];
            $review['buyer_url'] = $this->_host . $review['buyer_url'];
        }
        
        return $reviewDatas;
    }

    private function _getUrl($pageNum = 1)
    {
        return "https://www.amazon.com/reviews/{$this->asin}/ref=cm_cr_getr_d_paging_btm_{$pageNum}?pageNumber={$pageNum}&filterByStar=critical&sortBy=recent";
    }
}