<?php

namespace app\libraries;
use Yii;

class MyHelper
{

    public static function text2Array($text)
    {
        $arr = array_filter(explode("\n", $text));
        $keys = explode("\t", array_shift($arr));
        return array_map(function ($val) use ($keys) {
                return array_combine($keys, explode("\t", $val));
            }, $arr);
    }

    /**
     * example
     * <ErrorResponse xmlns="http://mws.amazonservices.com/doc/2009-01-01/">
     *    <Error>
     *      <Type>Sender</Type>
     *      <Code>InvalidClientTokenId</Code>
     *      <Message>
     *        The AWS Access Key Id you provided does not exist in our records.
     *      </Message>
     *      <Detail mail="642032979@qq.com">com.amazonservices.mws.model.Error@17b6643</Detail>
     *    </Error>
     *    <RequestID>b7afc6c3-6f75-4707-bcf4-0475ad23162c</RequestID>
     *  </ErrorResponse>
     * @return array
     */
    public static function xml2array($url, $get_attributes = 1, $priority = 'tag')
    {

        if (preg_match('/^https?\:/is', $url)) {

            $contents = "";
            
            if (!($fp = @ fopen($url, 'rb'))) {
                return array ();
            }
            while (!feof($fp)) {
                $contents .= fread($fp, 8192);
            }
            fclose($fp);
        } else {
            $contents = $url;
        }

        if (!function_exists('xml_parser_create')) {
            return array ();
        }

        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);
        if (!$xml_values)
            return; //Hmm...
        $xml_array = array ();
        $parents = array ();
        $opened_tags = array ();
        $arr = array ();
        $current = & $xml_array;
        $repeated_tag_index = array (); 
        foreach ($xml_values as $data)
        {
            unset ($attributes, $value);
            extract($data);
            $result = array ();
            $attributes_data = array ();
            if (isset ($value))
            {
                if ($priority == 'tag')
                    $result = $value;
                else
                    $result['value'] = $value;
            }
            if (isset ($attributes) && $get_attributes)
            {
                foreach ($attributes as $attr => $val)
                {
                    if ($priority == 'tag')
                        $attributes_data[$attr] = $val;
                    else
                        $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }
            if ($type == "open")
            { 
                $parent[$level -1] = & $current;
                if (!is_array($current) or (!in_array($tag, array_keys($current))))
                {
                    $current[$tag] = $result;
                    if ($attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    $current = & $current[$tag];
                }
                else
                {
                    if (isset ($current[$tag][0]))
                    {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        $repeated_tag_index[$tag . '_' . $level]++;
                    }
                    else
                    { 
                        $current[$tag] = array (
                            $current[$tag],
                            $result
                        ); 
                        $repeated_tag_index[$tag . '_' . $level] = 2;
                        if (isset ($current[$tag . '_attr']))
                        {
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset ($current[$tag . '_attr']);
                        }
                    }
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = & $current[$tag][$last_item_index];
                }
            }
            elseif ($type == "complete")
            {
                if (!isset ($current[$tag]))
                {
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                }
                else
                {
                    if (isset ($current[$tag][0]) and is_array($current[$tag]))
                    {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        if ($priority == 'tag' and $get_attributes and $attributes_data)
                        {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;
                    }
                    else
                    {
                        $current[$tag] = array (
                            $current[$tag],
                            $result
                        ); 
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if ($priority == 'tag' and $get_attributes)
                        {
                            if (isset ($current[$tag . '_attr']))
                            { 
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset ($current[$tag . '_attr']);
                            }
                            if ($attributes_data)
                            {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                    }
                }
            }
            elseif ($type == 'close')
            {
                $current = & $parent[$level -1];
            }
        }
        return ($xml_array);
    }

    /**
     * example
     * array (
     *      'Error' => 
     *          array (
     *            'Type' => 'Sender',
     *            'Code' => 'InvalidClientTokenId',
     *            'Message' => 'The AWS Access Key Id you provided does not exist in our records.',
     *            'Detail' => 'com.amazonservices.mws.model.Error@17b6643',
     *            'Detail_attr' => 
     *                array (
     *                  'mail' => '642032979@qq.com',
     *                  'asdf' => '642032979@qq.com',
     *                ),
     *          ),
     *      'Error_attr' => ['abc' => 'asdfasdfas'],
     *      'RequestID' => 'b7afc6c3-6f75-4707-bcf4-0475ad23162c',
     *      'RequestID_attr' => 
     *        array (
     *          'xmlns' => 'http://mws.amazonservices.com/doc/2009-01-01/',
     *        ),
     *  );
     * @return string(xml)
     */
    public static function array2xml($data, $rootNodeName, $xml = null)
    {
        // var_dump($data);die;

        if (ini_get('zend.ze1_compatibility_mode') == 1) {
            ini_set ('zend.ze1_compatibility_mode', 0);
        }
        
        if ($xml == null) {
            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
        }
        
        $nodeXML = [];
        foreach($data as $key => $value) {

            if (is_numeric($key)) {
                $key = "unknownNode_{$key}";
            }
            
            $key = preg_replace('/[^a-z@_0-9]/i', '', $key);

            if (substr($key, -5) == '_attr') {
                $key = substr($key, 0, -5);
                if (isset($nodeXML[$key])) {
                    foreach ($value as $attrKey => $attrValue) {
                        if (is_array($nodeXML[$key])) {
                            foreach ($nodeXML[$key] as $oneXML) {
                                $oneXML->addAttribute($attrKey, $attrValue);
                            }
                        } else {
                            $nodeXML[$key]->addAttribute($attrKey, $attrValue);
                        }
                    }
                }
                continue;
            }
            
            if (is_array($value)) {
                foreach ($value as $one) {
                    $node = $xml->addChild($key);
                    $nodeXML[$key][] = $node;
                    self::array2xml($one, $rootNodeName, $node);
                }
            } else  {
                $value = htmlentities($value);
                $node = $xml->addChild($key, $value);
                $nodeXML[$key] = $node;
            }

        }
        
        return $xml->asXML();
    }


    //using
    static function xml_to_array( $xml ){
        $reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
        if(preg_match_all($reg, $xml, $matches))
        {
            $count = count($matches[0]);
            $arr = array();
            for($i = 0; $i < $count; $i++)
            {
                $key= $matches[1][$i];
                $val = self::xml_to_array( $matches[2][$i] );  // 递归
                if(array_key_exists($key, $arr))
                {
                    if(is_array($arr[$key]))
                    {
                        if(!array_key_exists(0,$arr[$key]))
                        {
                            $arr[$key] = array($arr[$key]);
                        }
                    }else{
                        $arr[$key] = array($arr[$key]);
                    }
                    $arr[$key][] = $val;
                }else{
                    $arr[$key] = $val;
                }
            }
            return $arr;
        }else{
            return $xml;
        }
    }

    static function arrayToXml($arr){
        $xml = "";
        foreach ($arr as $key=>$val){
                if(is_array($val)){
                        $xml.="<".$key.">".self::arrayToXml($val)."</".$key.">";
                }else{
                        $xml.="<".$key.">".$val."</".$key.">";
                }
        }
        return $xml;
    }
    //arrayBuildXml方法 第一个参数MerchantIdentifier就是seller_id
    static function arrayBuildXml($MerchantIdentifier, $arrayData , $type){
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?> 
        <AmazonEnvelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"amzn-envelope.xsd\">
        <Header>
          <DocumentVersion>1.01</DocumentVersion>
          <MerchantIdentifier>".$MerchantIdentifier."</MerchantIdentifier>
        </Header><MessageType>".$type."</MessageType>";
        $xml .= MyHelper::arrayToXml($arrayData);
        $xml .= "</AmazonEnvelope>";
        $xml = preg_replace('/<.\d>/', '' ,preg_replace('/<\d>/', '' ,$xml));
        $xml = preg_replace('/<StandardPrice>/', '<StandardPrice currency="USD">' ,$xml);
        $xml = preg_replace('/<SalePrice>/', '<SalePrice currency="USD">' ,$xml);
        
        $xml = preg_replace('/<BulletPoint\d>/', '<BulletPoint>' ,$xml);
        $xml = preg_replace('/<\/BulletPoint\d>/', '</BulletPoint>' ,$xml);

        $xml = preg_replace('/<SearchTerms\d>/', '<SearchTerms>' ,$xml);
        $xml = preg_replace('/<\/SearchTerms\d>/', '</SearchTerms>' ,$xml);
        return $xml;
    }
    //时间格式转换   2016-07-02 00:00:00  ->  2009-01-31T00:00:00Z
    static function Time2Gtime($time){
        //$time = "2016-06-27 08:23:54";
        $d1=substr($time,17,2); //秒
        $d2=substr($time,14,2); //分
        $d3=substr($time,11,2); // 时
        $d4=substr($time,8,2); //日
        $d5=substr($time,5,2); //月
        $d6=substr($time,0,4); //年
        $gtime =  $d6.'-'.$d5.'-'.$d4.'T'.$d3.':'.$d2.':'.$d1.'Z';
        return $gtime;
    }
    //时间格式转换   2009-01-31T00:00:00Z  -> 2016-07-02 00:00:00
    static function Gtime2Time($gtime){

        //$gtime = "2016-06-27T08:23:54Z";
        $d1=substr($gtime,17,2); //秒
        $d2=substr($gtime,14,2); //分
        $d3=substr($gtime,11,2); // 时
        $d4=substr($gtime,8,2); //日
        $d5=substr($gtime,5,2); //月
        $d6=substr($gtime,0,4); //年
        $date =  $d6.'-'.$d5.'-'.$d4.' '.$d3.':'.$d2.':'.$d1;
        $time = date("Y-m-d H:i:s",$date);
        return $time;
    }


    //在多维数组里判断是否存在 某个值
    public function deepInArray($value, $array) {   
        foreach($array as $item) {   
            if(!is_array($item)) {   
                if ($item == $value) {  
                    return true;  
                } else {  
                    continue;   
                }  
            }   
               
            if(in_array($value, $item)) {  
                return true;      
            } else if($this->deepInArray($value, $item)) {  
                return true;      
            }  
        }   
        return false;   
    }
    
    public static function request($url, array $headers = [], array $datas = [])
    {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_USERAGENT, self::getUserAgent());
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if ($datas) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        }

        $result = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno) {
            throw new \Exception($error);
        }

        return $result;
    }

    public static function snoopy($url, array $header = [], $timeout = 60)
    {
        $snoopy = new Snoopy();
        $snoopy->agent = self::getUserAgent();
        $snoopy->read_timeout = $timeout;
        $snoopy->expandlinks = false;
        if ($header) {
            $snoopy->rawheaders = $header;
        }
        $snoopy->fetch($url);

        $result = $snoopy->results;
        if ($snoopy->error) {
            throw new \Exception($snoopy->error);
        }
      
        return $result;
    }

    private static function getUserAgent()
    {
        $agents = [
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36',
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
            'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
            'Opera/9.80 (Windows NT 6.1; U; en) Presto/2.8.131 Version/11.11',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11',
            'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Maxthon 2.0)',
        ];

        return $agents[array_rand($agents)];
    }

}