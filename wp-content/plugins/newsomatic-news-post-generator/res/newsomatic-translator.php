<?php
class GoogleTranslator{
	public $ch;
	function __construct(&$ch){
		$this->ch = $ch;
	}
	function translateText($sourceText, $fromLanguage ,$toLanguage, $hideGoogle){
		$tempHnd = tmpfile();
		$metaDatas = stream_get_meta_data($tempHnd);
		$tmpFileUri = $metaDatas['uri'];
		fwrite($tempHnd, $sourceText);
		curl_setopt($this->ch, CURLOPT_URL, "https://translate.googleusercontent.com/translate_f");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POST, true );
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 60);
		if( class_exists('CurlFile')){
			$curlFile = new \CurlFile( $tmpFileUri, 'text/plain', 'test.txt');
		}else{
			$curlFile = '@'.$tmpFileUri.';type=text/plain;filename=test.txt';
		}
		$post = [
				'file' => $curlFile,
				'sl'   => $fromLanguage,
				'tl'   => $toLanguage,
				'js'   => 'y',
				'prev' => '_t',
				'hl'   => 'en',
				'ie'   => 'UTF-8',
                'oe'   => 'UTF-8'
		];
		curl_setopt ( $this->ch, CURLOPT_POSTFIELDS, $post );
		$headers = array();
		$headers[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36";
		$headers[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
		$headers[] = "Accept-Language: en-US,en;q=0.5";
		$headers[] = "Referer: https://translate.google.com/?tr=f&hl=en";
		$headers[] = "Connection: keep-alive";
		$headers[] = "Upgrade-Insecure-Requests: 1";
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		$exec = curl_exec($this->ch);
		fclose($tempHnd);
		if($exec === FALSE || trim($exec) == ''){
			throw new Exception('Empty translator reply with possible curl error');
		}
        if($hideGoogle === '1')
        {
            require_once (dirname(__FILE__) . "/simple_html_dom.php");
            $strip_list = array('google-src-active-text','google-src-text');
            $html_dom_original_html = newshtml_dom_str_get_html($exec);
            if(method_exists($html_dom_original_html, 'find')){
                foreach ($strip_list as $strip_class) {
                    $ret = $html_dom_original_html->find('*[class='.trim($strip_class).']');
                    foreach ($ret as $itm ) {
                        $itm->outertext = '' ;
                    }
                }
                $exec = $html_dom_original_html->save();
                $html_dom_original_html->clear();
                unset($html_dom_original_html);
            }
        }
		return $exec ;
	}
}
?>