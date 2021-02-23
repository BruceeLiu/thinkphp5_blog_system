<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SystemConfig extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->createSystemGroupTable();
        $this->createSystemConfigTable();
        $this->createSystemLogTable();
    }

    protected function createSystemGroupTable()
    {
        $tableName = 'system_group';
        $table = $this->table($tableName,['comment'=>'系统配置分组表'])
            ->addColumn('name','string',['length'=>30,'comment'=>'系统分组名称'])
            ->addColumn('desc','string',['length'=>255,'comment'=>'系统分组描述'])
            ->addColumn('status','integer',['length'=>11,'default'=>1,'comment'=>'状态 1->启用 2->禁用'])
            ->addTimestamps()
            ->addSoftDelete();
        $table->create();
        if ($this->hasTable($tableName)){
            $data = [
                [
                    'name'=>'基本设置',
                    'desc' => '设置该网站基本站点信息',
                    'status' => 1,
                    'create_time' => date('Y-m-d H:i:s',time()),
                    'update_time' => date('Y-m-d H:i:s',time()),
                ],
                [
                    'name'=>'首页导航栏设置',
                    'desc' => '展示首页导航栏',
                    'status' => 1,
                    'create_time' => date('Y-m-d H:i:s',time()),
                    'update_time' => date('Y-m-d H:i:s',time()),
                ],
                [
                    'name'=>'网站拥有者基本资料',
                    'desc' => '设置该网站基本站点信息',
                    'status' => 1,
                    'create_time' => date('Y-m-d H:i:s',time()),
                    'update_time' => date('Y-m-d H:i:s',time()),
                ],
                [
                    'name'=>'邮件设置',
                    'desc' => '管理改网站的邮件信息',
                    'status' => 0,
                    'create_time' => date('Y-m-d H:i:s',time()),
                    'update_time' => date('Y-m-d H:i:s',time()),
                ],
                [
                    'name'=>'安全设置',
                    'desc' => '设置该网站安全信息',
                    'status' => 0,
                    'create_time' => date('Y-m-d H:i:s',time()),
                    'update_time' => date('Y-m-d H:i:s',time()),
                ],
                [
                    'name'=>'其他设置',
                    'desc' => '设置该网站基本站点信息',
                    'status' => 0,
                    'create_time' => date('Y-m-d H:i:s',time()),
                    'update_time' => date('Y-m-d H:i:s',time()),
                ]
            ];
            $this->insert($tableName,$data);
        }
    }

    public function createSystemConfigTable()
    {
        $tableName = 'system_config';
        $table = $this->table($tableName,['comment'=>'系统配置表'])
            ->addColumn('group_id','integer',['length'=>11,'comment'=>'分组ID'])
            ->addColumn('key','string',['length'=>30,'comment'=>'配置名称'])
            ->addColumn('value','string',['length'=>30,'comment'=>'配置名称值'])
            ->addColumn('type','string',['length'=>30,'comment'=>'配置类型','default'=>'text'])
            ->addColumn('status','integer',['length'=>11,'default'=>1,'comment'=>'状态 1->启用 2->禁用'])
            ->addTimestamps()
            ->addSoftDelete();
        $table->create();

    }

    public function createSystemLogTable()
    {
        $tableName = 'system_log';
        $table = $this->table($tableName,['comment'=>'系统日志表'])
            ->addColumn('ip','string',['comment'=>'ip地址','length'=>100])
            ->addColumn('url','string',['comment'=>'请求的url','length'=>100])
            ->addColumn('uid','integer',['comment'=>'请求的url','length'=>11])
            ->addTimestamps();
        $table->create();
    }

}
