<?php
namespace app\common\model;

use think\Model;

/**
 * 文件日志模型
 * @desc 文件日志模型
 * @uses \app\common\model\FileLogModel
 */
class FileLogModel extends Model
{
    protected $name = 'file_log';

    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = false;

    protected $schema = [
        'id'          => 'int',
        'path'        => 'string',
        'filename'    => 'string',
        'ext'         => 'string',
        'size'        => 'int',
        'create_time' => 'int',
    ];
}