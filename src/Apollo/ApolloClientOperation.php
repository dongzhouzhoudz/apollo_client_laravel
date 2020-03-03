<?php
/**
 * Created by PhpStorm.
 * User: dongzhou
 * Date: 2020/2/27
 * Time: 5:11 PM
 */

namespace CommonTool\Apollo;

use Org\Multilinguals\Apollo\Client\ApolloClient;


class ApolloClientOperation {
    private $apolloClient;
    private $sourcePath;
    private $desFile;

    /**
     * ApolloClientOperation constructor.
     * @param $aServer
     * @param $aAppid
     * @param $aNameSpace
     * @param $sourcePath
     * @param $desFile
     * 初始化apollo 客户端配置
     */
    public function __construct($aServer, $aAppid, $aNameSpace, $sourcePath, $desFile) {
        $this->apolloClient = new ApolloClient($aServer, $aAppid, $aNameSpace);
        $this->sourcePath = $sourcePath;
        $this->apolloClient->save_dir = $sourcePath;
        $this->desFile = $desFile;
    }


    /**
     * 一个监控循环逻辑
     */
    public function start() {
        $sourcePath = $this->sourcePath;
        $desFile = $this->desFile;
        do {
            $this->apolloClient->start(function () use ($sourcePath, $desFile) {
                self::saveConfigToLocal($sourcePath, $desFile);
            });
        } while (true);

    }


    /**
     * @param $sourcePath
     * @param $desFile
     * @return bool
     * 回调函数并且保存数据
     */
    public static function saveConfigToLocal($sourcePath, $desFile) {
        $contentArray = collect(file($desFile, FILE_IGNORE_NEW_LINES));
        $fileList = glob($sourcePath . DIRECTORY_SEPARATOR . 'apolloConfig.*');
        $apollo = [];
        foreach ($fileList as $oneFile) {
            $config = require $oneFile;
            if (is_array($config) && isset($config['configurations'])) {
                $apollo = array_merge($apollo, $config['configurations']);
            }
        }
        $contentArray->transform(function ($item) use (&$apollo, &$newAddContent) {
            foreach ($apollo as $key => $value) {
                if (strpos($item, $key) !== false) {
                    $newAddContent[] = $key . '=' . $value; //新增配置
                    $checkConfig = self::checkStrBetweenEmpty($item, $key, "=");
                    if ($checkConfig) {
                        $configLine = $key . '=' . $value;
                        unset($apollo[$key]);
                        return $configLine;
                    }
                }
            }
            return $item;
        });

        print_r($contentArray);

        print_r($apollo);

        if (!empty($apollo)) {
            foreach ($apollo as $key => $value) {
                $newAddContent[] = $key . '=' . $value;
            }
        }


        $content = implode($contentArray->toArray(), "\n")."\n"; //修改配置部分
        $content .= implode($newAddContent, "\n")."\n";          //新增配置部分

        try {
            file_put_contents($desFile, $content);
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            false;
        }
    }

    /**
     * @param $checkStr
     * @param $startStr
     * @param $endStr
     * @return bool
     *
     */
    public static function checkStrBetweenEmpty($checkStr, $startStr, $endStr) {
        $startPosition = (strpos($checkStr, $startStr)) + strlen($startStr) + 1;
        $endPosition = (strpos($checkStr, $endStr));
        if ($endPosition > $startPosition) {
            $middleString = substr($checkStr, $startPosition, $endPosition);
            if (empty($middleString)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }


}