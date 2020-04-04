<?php
namespace Core\helper;

class FileHelper
{
    public static function getFileMd5($dir, $ignore)
    {
        $files = glob($dir);
        $ret = '';
        foreach ($files as $file) {
            if (is_dir($file) && strpos($file, $ignore) === false) {
                $ret .= self::getFileMd5($file.'/*',$ignore);//递归读取所有文件夹下面的内容
            } elseif(pathinfo($file)['extension'] == 'php') {
                $ret .= md5_file($file);
            }
        }

        return md5($ret);
    }

}