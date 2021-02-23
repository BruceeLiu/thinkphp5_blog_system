<?php

use think\migration\Migrator;
use think\migration\db\Column;

class NetworkLine extends Migrator
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
        //博客网站心里历程时间线表
        $this->createNetworkLinesTable();
        //友情链接表
        $this->createLinksTable();
    }


    public function createNetworkLinesTable()
    {
        $tableName = 'network_lines';
        $table = $this->table($tableName,['comment'=>'博客发展时间线表']);
        $table->addColumn('name','string',['comment'=>'名称','length'=>30])
            ->addColumn('desc','string',['comment'=>'名称','length'=>255])
            ->addColumn('time_point','timestamp',['comment'=>'网站发展时间点'])
            ->addTimestamps();
        $table->create();
    }

    public function createLinksTable()
    {
        $tableName = 'links';
        $table = $this->table($tableName,['comment'=>'友情链接']);
        $table->addColumn('name','string',['comment'=>'网站名称','length'=>30])
            ->addColumn('url','string',['comment'=>'链接地址','length'=>255])
            ->addTimestamps();
        $table->create();
    }
}
