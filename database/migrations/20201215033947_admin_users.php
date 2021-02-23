<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminUsers extends Migrator
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
        $this->createAuthSTable();
        $this->createTableRolesTable();
        $this->createTableAdminUsers();
    }
    //管理员表
    protected function createTableAdminUsers()
    {
        $table_name = 'admin_users';
        $table = $this->table($table_name,['comment'=>'管理员表'])
            ->addColumn('username','string',['length'=>30,'null'=>false,'comment'=>'账户名'])
            ->addColumn('password','string',['length'=>191,'null'=>false,'comment'=>'密码'])
            ->addColumn('phone','string',['length'=>30,'null'=>true,'comment'=>'电话号码'])
            ->addColumn('email','string',['length'=>191,'null'=>true,'comment'=>'电子邮箱'])
            ->addColumn('role_id','integer',['length'=>11,'null'=>false,'comment'=>'角色ID'])
            ->addColumn('status','integer',['length'=>1,'null'=>false,'default'=>0,'comment'=>'账户状态(0->禁用 1->启用)'])
            ->addColumn('access_token','string',['length'=>191,'null'=>true,'comment'=>'access_token值'])
            ->addColumn('access_token_expire_in','integer',['length'=>11,'null'=>true,'comment'=>'access_token有效期'])
            ->addColumn('access_token_create_at','integer',['length'=>11,'null'=>true,'comment'=>'access_token创建时间'])
            ->addTimestamps();
        $table->create();
        if ($this->hasTable($table_name)){
            $data = [
                'username'=>'super_admin',
                'password'=>password_hash('123456',PASSWORD_DEFAULT),
                'role_id'=>1,
                'status'=>1,
                'create_time'=>date('Y-m-d H:i:s',time()),
                'update_time'=>date('Y-m-d H:i:s',time())
            ];
            $this->insert($table_name,$data);
        }
    }
    //角色表
    protected function createTableRolesTable()
    {
        $table_name = 'roles';
        $table = $this->table($table_name,['comment'=>'角色表'])
            ->addColumn('name','string',['length'=>30,'null'=>false,'comment'=>'角色名'])
            ->addColumn('desc','string',['length'=>191,'null'=>true,'comment'=>'角色描述'])
            ->addColumn('auth_id','string',['length'=>191,'null'=>true,'comment'=>'权限ID(示例:1,2,3,4,5,6...)'])
            ->addColumn('status','integer',['length'=>1,'null'=>false,'default'=>0,'comment'=>'账户状态(0->禁用 1->启用)'])
            ->addTimestamps();
        $table->create();
        if ($this->hasTable($table_name)){
            $data = [
                ['name'=>'超级管理员', 'desc'=>'该系统最高权限角色', 'auth_id'=>'__AUTH_ALL__', 'status'=>1, 'create_time'=>date('Y-m-d H:i:s',time()), 'update_time'=>date('Y-m-d H:i:s',time())],
                ['name'=>'默认角色', 'desc'=>'什么权限都没有', 'auth_id'=>'', 'status'=>1, 'create_time'=>date('Y-m-d H:i:s',time()), 'update_time'=>date('Y-m-d H:i:s',time())]
            ];
           $this->insert($table_name,$data);
        }
    }
    //权限表
    protected function createAuthSTable()
    {
        $table_name = 'auths';
        $table = $this->table($table_name,['comment'=>'权限表'])
            ->addColumn('name','string',['length'=>30,'null'=>false,'comment'=>'权限名'])
            ->addColumn('pid','integer',['length'=>11,'null'=>false,'default'=>0,'comment'=>'上级ID(默认为0)'])
            ->addColumn('auth_rule','string',['length'=>191,'null'=>true,'comment'=>'权限路径'])
            ->addColumn('desc','string',['length'=>191,'null'=>true,'comment'=>'权限描述'])
            ->addColumn('status','integer',['length'=>1,'null'=>false,'default'=>0,'comment'=>'权限状态(0->禁用 1->启用)'])
            ->addColumn('extend','text',['null'=>true,'comment'=>'扩展字段(JSON字段)'])
            ->addTimestamps();
        $table->create();
    }
}
