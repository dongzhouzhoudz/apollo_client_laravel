<?php
/**
 * Created by PhpStorm.
 * User: dongzhou
 * Date: 2020/2/27
 * Time: 12:07 PM
 */

use CommonTool\Apollo\ApolloClientOperation;

class Factory {

    public static function startApolloClient($configArray = []) {
        if (!isset($configArray['server'])) {
            echo "请配置参数--server";
            return;
        }
        if (!isset($configArray['appid'])) {
            echo "请配置参数--appid";
            return;
        }
        if (!isset($configArray['namespaces'])) {
            echo "请配置参数--namespaces";
            return;
        } else {
            if (!is_array($configArray['namespaces'])) {
                echo "配置参数namespaces--必须是数组形式,例如=>['ZZ_PHP.dbconfig','ZZ_PHP.redisconfig']";
                return;
            }
        }
        if (!isset($configArray['source_path'])) {
            echo "请配置参数--source_path";
            return;
        } else {
            if (!is_dir($configArray['source_path'])) {
                echo "source_path--必须是一个可用目录，请确认目录是否存在";
                return;
            }
        }
        if (!isset($configArray['des_file'])) {
            echo "请配置参数--des_file";
            return;
        } else {
            if (!file_exists($configArray['des_file'])) {
                echo "des_file--必须是一个可用文件，请确认文件是否存在";
                return;
            }
        }
        $aClient = new ApolloClientOperation($configArray['server'], $configArray['appid'], $configArray['namespaces'], $configArray['source_path'], $configArray['des_file']);
        $aClient->start();
    }

}
