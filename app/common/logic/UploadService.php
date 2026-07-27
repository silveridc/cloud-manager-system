<?php
namespace app\common\logic;

use app\common\model\FileLogModel;
use think\file\UploadedFile;

/**
 * 上传服务
 * @desc 上传服务
 * @uses \app\common\logic\UploadService
 */
class UploadService
{
    protected array $allowExt = ['png', 'jpg', 'jpeg', 'gif', 'doc', 'docx', 'pdf', 'txt', 'zip', 'rar'];
    protected int $maxSize = 150 * 1024 * 1024; // 150MB

    /**
     * 上传文件并记录日志
     * @author zhaoyj
     * @version v1
     * @param UploadedFile $file - 上传的文件对象
     * @param string $dir - 存储子目录名称，默认common，仅允许字母数字下划线和连字符
     * @return array
     * @return int id - 文件日志记录ID
     * @return string path - 文件相对存储路径
     * @return string filename - 文件原始名称
     * @return string url - 文件访问URL路径
     */
    public function upload(UploadedFile $file, string $dir = 'common'): array
    {
        // 验证$dir参数，防止路径穿越攻击，仅允许字母、数字、下划线和连字符
        if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $dir)) {
            return [
                'status' => 400,
                'messages' => lang('invalid_dir'),
                'time' => time(),
            ];
        }

        $ext = strtolower($file->extension());
        if (!in_array($ext, $this->allowExt)) {
            return [
                'status' => 400,
                'messages' => lang('file_ext_not_allowed'),
                'time' => time(),
            ];
        }

        if ($file->getSize() > $this->maxSize) {
            return [
                'status' => 400,
                'messages' => lang('file_too_large'),
                'time' => time(),
            ];
        }

        $saveName = date('Ymd') . '/' . sha1(uniqid((string)mt_rand(), true)) . '.' . $ext;
        $path = 'upload/' . $dir . '/' . $saveName;

        $file->move(public_path() . 'upload/' . $dir . '/' . date('Ymd'), basename($saveName));

        // 记录到文件日志
        $FileLogModel = new FileLogModel();
        $id = (int)$FileLogModel->insertGetId([
            'path' => $path,
            'filename' => $file->getOriginalName(),
            'ext' => $ext,
            'size' => $file->getSize(),
            'create_time' => time(),
        ]);

        return [
            'id' => $id,
            'path' => $path,
            'filename' => $file->getOriginalName(),
            'url' => '/' . $path,
        ];
    }
}