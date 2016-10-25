<?php
namespace Plugin\KintoneTransAdmin\Controller;


/**
* KINTONE API 使用クラス
*/
class kintoneAgent{
    /* ユーザサブドメイン */
    private $__UserSubDomain;
    /* ユーザID */
    private $__UserId;
    /* ユーザパスワード */
    private $__UserPass;
    /* アプリID */ 
    private $__AppId;
    
    /* コンストラクタ
    * $input ユーザID,ユーザパスワード
    */
    function __construct($UserSubDomain,$UserId,$UserPass,$AppId){
        # ユーザサブドメインのセット
        $this->__UserSubDomain = $UserSubDomain;
        # ユーザIDのセット
        $this->__UserId = $UserId;
        # ユーザパスワードのセット
        $this->__UserPass = $UserPass;
        # アプリIDのセット
        $this->__AppId = $AppId;
    }

    /*
    * レコードの追加
    * @input 追加する1レコードの連想配列
    */
    public function AddRecord($record){
    
        # リクエスト先URL
        $ReqURL = "https://".$this->__UserSubDomain.".cybozu.com/k/v1/record.json";
        # jsonデータ生成
        $json = json_encode( array(
                    "app" => $this->__AppId,
                    "record" => $record
                ) );
    
        # ヘッダ生成
        $header[] = 'X-Cybozu-Authorization: '.base64_encode($this->__UserId.":".$this->__UserPass);
        $header[] = 'Content-Type: application/json';
        $header[] = 'Content-Length: '.strlen($json);
        
        # コンテキスト生成
        $context = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => implode("\r\n", $header),
                'content' => $json,
             )
        );

        # 接続開始
        try {
            $res = file_get_contents($ReqURL,false,stream_context_create($context));
        } catch (Exception $e) {
            echo $e->getMessage();
            var_dump($res);
            exit;
        }
        return $res;
        
    }

}
