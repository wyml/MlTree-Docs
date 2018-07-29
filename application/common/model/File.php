<?php
namespace app\common\model;

use think\Model;
use think\Db;

class File extends Model
{
    /**
     * 初始化方法，判断目录权限 
     */
    protected static function init()
    {
        clearstatcache();
        if (!is_writable(config('mtd.doc_path'))) {
            return [false, 'Doc目录不可写！'];
        } elseif (!is_writable(config('mtd.dochistory_path'))) {
            return [false, 'DocHistory目录不可写！'];
        }

        if (!is_readable(config('mtd.doc_path'))) {
            return [false, 'Doc目录不可读！'];
        } elseif (!is_readable(config('mtd.dochistory_path'))) {
            return [false, 'DocHistory目录不可读！'];
        }
    }

    /**
     * 返回一个指定标识的文件路径
     * @param string $sign 文件标识
     * @param int $type 文件类型 0=正常文件 1=历史文件
     * @return string 文件Uri路径
     */
    static function getUri($sign, $type = 0)
    {
        if ($type == 0) {
            return (config('mtd.doc_path') . $sign . '.md');
        } else {
            return (config('mtd.dochistory_path') . $sign . '.md');
        }
    }

    /**
     * 检查Doc是否存在
     * @param string $sign Doc标识
     * @param int $type 文件类型 0=正式文件 1=历史文件
     */
    static function fileExists($sign, $type = 0)
    {
        if ($type == 0) {
            return file_exists(config('mtd.doc_path') . $sign . '.md');
        } else {
            return file_exists(config('mtd.dochistory_path') . $sign . '.md');
        }
    }

    /**
     * 读取指定文件标识的内容
     * @param string $sign 文件标识
     * @param int $type 文件类型 0=正常文件 1=历史文件
     * @return array [true|false,data|errMsg]
     */
    static function readDoc($sign, $type = 0)
    {
        $path = self::getUri($sign, $type);
        $data = file_get_contents($path);
        if (empty($data)) {
            return [false, '文件不存在或权限不足'];
        } else {
            return [true, $data];
        }
    }

    /**
     * 获取指定文档的信息
     * @param string $sign 文档标识
     * @return array 
     */
    static function getFileInfor($sign, $type = 0)
    {
        if (!self::fileExists($sign, $type)) {
            return [false, '文件不存在或权限不足'];
        }
        $path = self::getUri($sign, $type);
        clearstatcache();
        $res = [
            'lastRadeTime' => fileatime($path),
            'lastWriteTime' => filemtime($path),
            'size' => filesize($path),
            'fileType' => filetype($path),
            'md5' => md5(file_get_contents($path)),
        ];
        return [true, $res];
    }

    /**
     * 修改指定Doc内容同时生成历史文档
     * @param string $sign 文档标识
     * @param string $data 写入内容
     * @param int $isHistroy 是否生成历史文档 0=生成 1=不生成 默认0
     * @return array [true|false,handel|errMsg]
     */
    static function writeDoc($sign, $data, $isHistroy = 0)
    {
        if (self::fileExists($sign)) {
            if ($isHistroy == 0) {
                $res = self::createDocHistroy($sign, $data);
                return $res;
            } else {
                $handel = file_put_contents(self::getUri($sign), $data);
                return [true, $handel];
            }
        } else {
            return [false, '指定文档不存在'];
        }
    }

    /**
     * 新建一个Doc位于指定目录下
     * @param string $sign 文件标识
     * @param string $data 文件内容
     * @return array [true|false,$handel]
     */
    static function createFile($sign, $data)
    {
        $handel = file_put_contents(config('mtd.doc_path') . $sign . '.md', $data);
        return [true, $handel];
    }

    /**
     * 复制一个文件副本位于历史目录下
     * @param string $sign 现行文件标识
     * @param string $data 如不为空则会在复制源写入该数据
     * @return array
     */
    static function createDocHistroy($sign, $data = '')
    {
        if (!self::fileExists($sign)) {
            return [false, '指定文件不存在'];
        }
        $uri = $sign . time();
        if (!empty($data)) {
            $res = copy(self::getUri($sign), self::getUri($uri, 1));
            file_put_contents(self::getUri($data));
            return [true, $uri];
        } else {
            $res = copy(self::getUri($sign), self::getUri($uri, 1));
            return [true, $uri];
        }
    }

    /**
     * 删除指定文件同时生成历史
     * @param string $sign 文件标识
     * @param int $isHistroy 是否生成历史 0=生成 1=不生成 默认0
     * @return array [true|false,uri|errmsg]
     */
    static function delFile($sign, $isHistroy = 0)
    {
        if (!self::fileExists($sign)) {
            return [false, '文档不存在或丢失'];
        }
        if ($isHistroy = 1) {
            unlink(self::getUri($sign));
            return [true, $hispath];
        }
        $hispath = self::getUri($sign . time(), 1);
        copy(self::getUri($sign), $hispath);
        unlink(self::getUri($sign));
        return [true, $hispath];
    }

    /**
     * 删除指定标识符的所有文档、以及历史
     * @param int $did 指定文档did
     * @return array [true|false,errMsg] 
     */
    static function delFileAll($did)
    {
        $res = Db::name('docs')->where('did', $did)->find();
        if (empty($res)) {
            return [false, '指定文档不存在'];
        }
        unlink(self::getUri($res['sign']));
        $histroy = Db::name('dochistroy')->where('did', $res['did'])->select();
        foreach ($histroy as $key => $value) {
            unlink(self::getUri($value['sign']));
        }
        return [true];
    }
}