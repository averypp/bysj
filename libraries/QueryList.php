<?php

namespace app\libraries;

// 加载php-query插件
require_once(__DIR__ . '/phpquery-master/phpQuery/phpQuery.php');

class QueryList
{
    public $results;

    protected $userAgents = array(
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E)',
            'Mozilla/5.0 (Windows NT 5.2) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.122 Safari/534.30',
            'Mozilla/5.0 (Windows NT 5.1; rv:5.0) Gecko/20100101 Firefox/5.0',
            'Opera/9.80 (Windows NT 5.1; U; zh-cn) Presto/2.9.168 Version/11.50',
            'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.04506.648; .NET CLR 3.5.21022; .NET4.0E; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET4.0C)',
            'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1',
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727; TheWorld)',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36',
        );

    public function __construct($url)
    {
        // $this->results = file_get_contents(__DIR__ . '/page.html');
        $this->_request($url);
    }

    private function _request($url)
    {
        $snoopy = new Snoopy();
        // 随机获取模拟浏览器名
        $snoopy->agent = $this->userAgents[mt_rand(0, count($this->userAgents) - 1)];
        // 告诉服务器能接收的数据类型
        $snoopy->accept = "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
        // 不缓存
        $snoopy->rawheaders["Pragma"] = "no-cache";
        // 接收中文
        $snoopy->rawheaders["Accept-Language"] = "zh-CN,zh;q=0.8";

        if ($snoopy->fetch($url) === false) {
            throw new \Exception($snoopy->error);
        }

        $this->results = $snoopy->results;
        if (!$this->results) {
            throw new \Exception("not get page");
        }
        return $this->results;
    }

    public function query(array $reg, $rang)
    {
        $datas = [];
        \phpQuery::newDocumentHTML($this->results, 'utf-8');
        $lists = pq($rang);
        foreach ($lists as $list) {
            $list = pq($list);
            $data = [];
            foreach ($reg as $column => $rule) {
                $noAttr = ['html', 'text'];
                if (in_array($rule[1], $noAttr)) {
                    $value = $list->find($rule[0])->$rule[1]();
                } else {
                    $value = $list->find($rule[0])->attr($rule[1]);
                }

                $callback = isset($rule[2]) ? $rule[2] : null;
                if (is_callable($callback)) {
                    $value = call_user_func_array($callback, [$value]);
                }

                $data[$column] = trim($value);
            }

            $datas[] = $data;
        }

        // 清内存
        \phpQuery::$documents = array();
        return $datas;
    }

}