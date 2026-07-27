<?php

namespace app\admin\model;

use think\Model;

class ClientModel extends Model
{
    protected $name = 'client';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $schema = [
        'id'          => 'int',
        'username'    => 'string',
        'email'       => 'string',
        'phone_code'  => 'string',
        'phone'       => 'string',
        'status'      => 'int',
        'credit'      => 'float',
        'company'     => 'string',
        'address'     => 'string',
        'language'    => 'string',
        'country_id'  => 'int',
        'notes'       => 'string',
        'create_time' => 'int',
        'update_time' => 'int',
    ];
}