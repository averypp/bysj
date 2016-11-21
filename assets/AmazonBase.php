<?php
namespace app\assets;
use DateTime;
use DateTimeZone;
use Exception;
class AmazonBase {
	public $AccessKeyID = 'AKIAI777T77AT5ISTVPQ';
	public $SellerID = 'A3JT3LBRIKYRKF';
	public $SecretKey = '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';
	public $EndPoint='mws.amazonservices.com';
	public $MarketplaceId='';
	public $Sig_URI='/';
	public $AWSVersion='2009-01-01';
	public $_last_reponse_xml;
	public $SignatureMethod = "HmacSHA256";
	public $SignatureVersion = 2;
	public $_last_error;
	static $time_run=60;
	static $time_request=55;
	public $marketplaceids = array(
			'A2EUQ1WTGCTBG2'=>'CA',
			'ATVPDKIKX0DER'=>'US',
			'A1AM78C64UM0Y8'=>'MX',
			'A1PA6795UKMFR9'=>'DE',
			'A1RKKUPIHCS9HS'=>'ES',
			'A13V1IB3VIYZZH'=>'FR',
			'A21TJRUUN4KGV'=>'IN',
			'APJ6JRA9NG5V4'=>'IT',
			'A1F83G8C2ARO7P'=>'UK',
			'A1VC38T7YXB528'=>'JP',
			'AAHKV2X7AFYLW'=>'CN'
		);
	
	/**
	 * 设置验证资料
	 *
	 * @param unknown_type $SellerID
	 * @param unknown_type $AccessKeyID
	 * @param unknown_type $SecretKey
	 * @param unknown_type $Site
	 */
	/*function setAuthData($auth){
		$this->SellerID=$auth['MERCHANT_ID'];
		$this->EndPoint=str_replace('https://', '', $auth['serviceUrl']);
		$this->AccessKeyID=$auth['AWS_ACCESS_KEY_ID'];
		$this->SecretKey=$auth['AWS_SECRET_ACCESS_KEY'];
		$this->MarketplaceId=$auth['MARKETPLACE_ID'];
		
	}*/


	function setAttibutes($att, $value){
		$this->$att = $value;
	}

	function getAttibutes($att){
		echo $this->$att;die;
	}
	/**
	 * 发送请求	
	 * @param string $verb
	 * @param array $queryArray
	 * @param string $body
	 * @throws AmazonException
	 * @return SimpleXMLElement

	 */
	function request($verb,$queryArray,$service_version='',$body='',$header=''){
		set_time_limit(self::$time_run);
		$params = array(
				'AWSAccessKeyId' => $this->AccessKeyID,
				'Action' => $verb,
				'SellerId' => $this->SellerID,
				'SignatureMethod' => $this->SignatureMethod,
				'SignatureVersion' => $this->SignatureVersion,
				'Timestamp'=> gmdate("Y-m-d\TH:i:s\Z", time()),
				'Version'=> $this->AWSVersion,
		);
		if(count($queryArray)){
			$params=array_merge($params,$queryArray);
		}
		// Sort the URL parameters
		$url_parts = array();
		foreach(array_keys($params) as $key)
		$url_parts[$key] = $key . "=" . str_replace('%7E', '~', rawurlencode($params[$key]));
		ksort($url_parts);
		// Construct the string to sign
		$url_string = implode("&", $url_parts);
		$string_to_sign = "POST\n{$this->EndPoint}\n{$this->Sig_URI}{$service_version}\n" . $url_string;
		//var_dump($string_to_sign);die;
		$signature=urlencode(base64_encode(hash_hmac("sha256", $string_to_sign, $this->SecretKey, True)));
		
		$url = 'https://'.$this->EndPoint.$this->Sig_URI  . $service_version . '?' . $url_string . "&Signature=" . $signature;
		//var_dump($url);die;
		//QLog::log('request URL: '.$url);
		
		
		$ch = curl_init();
		//socks5 使用qee13 作为sock5代理服务器转发请求，需要服务器上开启服务 service supervisord start
		// https://github.com/clowwindy/shadowsocks/wiki/%E7%94%A8-Supervisor-%E8%BF%90%E8%A1%8C-Shadowsocks
		// http://ydt619.blog.51cto.com/316163/1055334
		//curl_setopt($ch, CURLOPT_PROXY, '54.248.245.123:1999');
		//curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		//data
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, self::$time_request);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		//curl_setopt($ch, CURLOPT_HEADER, true);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$response = curl_exec($ch);
		$error = curl_error($ch);
		//var_dump($response);die;
		/*if ($error) {
			$this->_last_error=$error;
			throw new AmazonException($error);
		}*/
		//$this->_last_reponse_xml=$response;
		//QLog::log('response: '.$response);
		//$response = substr($response, strpos( $response, '<?xml' ));
		$result= $this->FromXml($response);
		if ($result===false){
			throw new Exception('Invalide XML');
		}
		return  $result;
	}
	function requestErrorReturn($verb,$queryArray,$service_version='',$body='',$header=''){
		set_time_limit(self::$time_run);
		$params = array(
				'AWSAccessKeyId' => $this->AccessKeyID,
				'Action' => $verb,
				'SellerId' => $this->SellerID,
				'SignatureMethod' => $this->SignatureMethod,
				'SignatureVersion' => $this->SignatureVersion,
				'Timestamp'=> gmdate("Y-m-d\TH:i:s\Z", time()),
				'Version'=> $this->AWSVersion,
		);
		if(count($queryArray)){
			$params=array_merge($params,$queryArray);
		}
		// Sort the URL parameters
		$url_parts = array();
		foreach(array_keys($params) as $key)
		$url_parts[$key] = $key . "=" . str_replace('%7E', '~', rawurlencode($params[$key]));
		ksort($url_parts);
		// Construct the string to sign
		$url_string = implode("&", $url_parts);
		$string_to_sign = "POST\n{$this->EndPoint}\n{$this->Sig_URI}{$service_version}\n" . $url_string;
		//var_dump($string_to_sign);die;
		$signature=urlencode(base64_encode(hash_hmac("sha256", $string_to_sign, $this->SecretKey, True)));
		
		$url = 'https://'.$this->EndPoint.$this->Sig_URI  . $service_version . '?' . $url_string . "&Signature=" . $signature;
		//var_dump($url);die;
		//QLog::log('request URL: '.$url);
		
		
		$ch = curl_init();
		//socks5 使用qee13 作为sock5代理服务器转发请求，需要服务器上开启服务 service supervisord start
		// https://github.com/clowwindy/shadowsocks/wiki/%E7%94%A8-Supervisor-%E8%BF%90%E8%A1%8C-Shadowsocks
		// http://ydt619.blog.51cto.com/316163/1055334
		//curl_setopt($ch, CURLOPT_PROXY, '54.248.245.123:1999');
		//curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		//data
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, self::$time_request);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		//curl_setopt($ch, CURLOPT_HEADER, true);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$response = curl_exec($ch);
		$error = curl_error($ch);
		//var_dump($response);die;
		/*if ($error) {
			$this->_last_error=$error;
			throw new AmazonException($error);
		}*/
		//$this->_last_reponse_xml=$response;
		//QLog::log('response: '.$response);
		//$response = substr($response, strpos( $response, '<?xml' ));
		// $result= $this->FromXml($response);
		// if ($result===false){
		// 	throw new Exception('Invalide XML');
		// }
		return  $response;
	}


	/**
	 * 发送请求	
	 * @param string $verb
	 * @param array $queryArray
	 * @param string $body
	 * @throws AmazonException
	 * @return SimpleXMLElement
	 */
	function url_request(){
		
		
		$ch = curl_init();
		
		//socks5 使用qee13 作为sock5代理服务器转发请求，需要服务器上开启服务 service supervisord start
		// https://github.com/clowwindy/shadowsocks/wiki/%E7%94%A8-Supervisor-%E8%BF%90%E8%A1%8C-Shadowsocks
		// http://ydt619.blog.51cto.com/316163/1055334
		
		//curl_setopt($ch, CURLOPT_PROXY, '54.248.245.123:1999');
		//curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		$url='https://sellercentral.amazon.com/hz/inventory/classify';
		$param=['selectedCategory' =>'2335752011/2335753011',
				'nodeFilter'=>null,
				'application_name'=>'PRODUCT_CLASSIFIER',
				'isParent'=>true,
				'subcontroller'=>'classifier-browse-ajax',
				'sequenceId'=>2,
				'isAjaxRequest'=>true
				];
		//data
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$param);


		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, self::$time_request);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		//curl_setopt($ch, CURLOPT_HEADER, true);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		
		$response = curl_exec($ch);
		$error = curl_error($ch);
		echo $response;die;
	
	}
	
	
	
	public function FromXml($xml)
	{	
		if(!$xml){
			throw new Exception("xml数据异常！");
		}
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $this->values;
	}
	
	
	/**
	 * 根据api文档的结构，整理xml接口，特别对于数据部分的接口做处理，保证数据节点返回数组，而不是第一节点的内容，如果节点不存在，补充节点，最多补充一级
	 * 
	 * @example
	 * 	<orderItem><a>b</a></orderItem>
	 *  返回 orderItem = array ( array(a=>b))
	 * @param string $verb
	 * @param SimpleXMLElement $xml
	 * @return SimpleXMLElement
	 */
	static function xmlFormater($verb,$xml){
		static $arrayNodes=array(
			'ListOrders'=>array(
				'Orders.Order'
			),
			'GetOrder'=>array(
				'Orders.Order'
			),
			'ListOrderByNextToken'=>array(
				'Orders.Order'
			),
			'ListOrderItems'=>array(
				'OrderItems.OrderItem'
			),
			'GetReportList'=>array(
				'ReportInfo'
			),
			'GetReportListByNextToken'=>array(
				'ReportInfo'
			),
			// 因为每种 Report 里面的内容都不同，内容需要再调用 xmlFormater 进行格式化
			'GetReport'=>array(
				'Message'
			),
			
		);
		$xml=self::xmlToArray($xml);
		//有错误
		if (isset($r['Error'])){
			throw new AmazonExceptionFail(print_r($r,true));
		}
		if (!isset($xml[$verb.'Result'])){
			$r=$xml;
		}else {
			$r=$xml[$verb.'Result'];
		}
		if (isset($arrayNodes[$verb])){
			foreach ($arrayNodes[$verb] as $rule){
				$nds=explode('.', $rule);
				$r=self::xmlFormaterHelper($r, $nds);
			}
		}
		return $r;
	}
	/**
	 * SimpleXmlElement 对象转换为数组，字段属性会转化为 字段名_属性名的 数组项，例如 Total_currency
	 * 注意：属性名会自动转换为全小写
	 * @param SimpleXMLElement $o
	 * @return string
	 */
	static function xmlToArray($xml, $root = false) {
		if (!$xml->children()) {
			return (string)$xml;
		}
	
		$array = array();
		foreach ($xml->children() as $element => $node) {
			$totalElement = count($xml->{$element});
			
			if (!isset($array[$element])) {
				$array[$element] = "";
			}
	
			// Has attributes
			if ($attributes = $node->attributes()) {
				$data=(count($node) > 0) ? self::xmlToArray($node) : (string)$node;
				if ($totalElement > 1) {
					$array[$element][] = $data;
				} else {
					$array[$element] = $data;
					foreach ($attributes as $attr => $value) {
						$array[$element.'_'.strtolower($attr)] = (string)$value;
					}
				}
	
			// Just a value
			} else {
				if ($totalElement > 1) {
					$array[$element][] = self::xmlToArray($node);
				} else {
					$array[$element] = self::xmlToArray($node);
				}
			}
		}
	
		if ($root) {
			return array($xml->getName() => $array);
		} else {
			return $array;
		}
	}
	/**
	 * 数组格式化,实现类似 xmlFormater 的功能
	 * @param array $rules 规则
	 * @param array $arr 数据数组
	 * @example 参考 reports.php getReport
	 * @return array
	 */
	static function arrayFormater($rules,$arr){
		foreach ($rules as $rule){
			$nds=explode('.', $rule);
			$arr=self::xmlFormaterHelper($arr, $nds);
		}
		return $arr;
	}
	static function xmlFormaterHelper($node,$rest){
		$next=array_shift($rest);
		if (is_null($next)){
			if (is_array($node) && isset($node[0])){
				return $node;
			}else {
				return array($node);
			}
		}elseif (isset($node[$next])) {
			$node[$next]=self::xmlFormaterHelper($node[$next],  $rest);
		}elseif (!isset($node[$next])){
			$node[$next]=array();
		}
		return $node;
	}

	public function getReturnErrorMsg($result)
  	{
  		if (is_null($result)) {
  			throw new \Exception("\$result is null", 1);
  		}
  		if (!preg_match('/\n\n/is', $result)) {
  			throw new \Exception("\$result dataformat error $result", 1);
  		}

  		$arr = explode("\n\n", $result);

  		$ret = $this->text2Array($arr[1]);
  		$errorMsg = [];
  		foreach ($ret as $v) {
  			if (in_array($v['error-type'], ['Error', 'Fatal'])) {

  				$errorMsg[] = "SKU是{$v['sku']}的产品错误信息为:{$v['error-message']}";
  			}
  		}

  		return $errorMsg;
  	}

  	public static function text2Array($text)
  	{
  		$arr = explode("\n", $text);
		$keys = explode("\t", array_shift($arr));
		return array_map(function ($val) use ($keys) {
				if ($val) {
					return array_combine($keys, explode("\t", $val));
				}
			}, $arr);
  	}
}
//class AmazonException extends QException {}
//class AmazonExceptionFail extends AmazonException{}