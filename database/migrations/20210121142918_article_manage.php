<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ArticleManage extends Migrator
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
        $this->createArticleComment();
        $this->createArticleTags();
        $this->createArticleTypes();
        $this->createArticleManages();
        $this->createArticleTimes();
    }


    protected function createArticleManages()
    {
        $table_name = 'article_manages';
        $table = $this->table($table_name,['comment'=>'文章管理表'])
            ->addColumn('title','string',['length'=>30,'comment'=>'文章标题'])
            ->addColumn('sub_title','string',['length'=>30,'comment'=>'文章副标题(简略标题)'])
            ->addColumn('article_type','integer',['length'=>11,'comment'=>'文章分类ID'])
            ->addColumn('article_tag','string',['length'=>191,'comment'=>'文章标签id(1,2,3,4...)','null'=>true,'default'=>null])
            ->addColumn('sort_no','integer',['length'=>11,'comment'=>'排序','default'=>100])
            ->addColumn('keywords','string',['length'=>191,'comment'=>'关键字','null'=>true,'default'=>null])
            ->addColumn('content','longtext',['comment'=>'文章内容','null'=>true,'default'=>null])
            ->addColumn('content_summary','string',['length'=>191,'comment'=>'文章摘要','null'=>true,'default'=>null])
            ->addColumn('create_user','integer',['length'=>11,'comment'=>'创建人ID'])
            ->addColumn('update_user','integer',['length'=>11,'comment'=>'最近编辑人ID'])
            ->addColumn('is_allow_comment','integer',['length'=>11,'comment'=>'是否允许评论(1->允许评论 2->不允许评论)','default'=>1])
            ->addColumn('comment_start_time','timestamp',['comment'=>'评论开始时间','null'=>true,'default'=>null])
            ->addColumn('comment_end_time','timestamp',['comment'=>'评论结束时间','null'=>true,'default'=>null])
            ->addColumn('status','integer',['comment'=>'文章状态(0->草稿 1->待审核 2->上架 3->下架)','default'=>1])
            ->addTimestamps()
            ->addSoftDelete();
        $table->create();
    }

    public function createArticleTypes()
    {
        $table_name = 'article_types';
        $table = $this->table($table_name,['comment'=>'文章分类表'])
            ->addColumn('name','string',['length'=>30,'comment'=>'分类名'])
            ->addColumn('pid','integer',['length'=>11,'null'=>false,'default'=>0,'comment'=>'上级ID(默认为0)'])
            ->addColumn('desc','string',['length'=>191,'null'=>true,'comment'=>'分类描述'])
            ->addColumn('status','integer',['length'=>1,'default'=>1,'comment'=>'状态状态(0->禁用 1->启用)'])
            ->addTimestamps();
        $table->create();
    }

    public function createArticleTags()
    {
        $table_name = 'article_tags';
        $table = $this->table($table_name,['comment'=>'文章标签表'])
            ->addColumn('name','string',['length'=>30,'comment'=>'标签名'])
            ->addColumn('desc','string',['length'=>191,'null'=>true,'comment'=>'标签描述'])
            ->addColumn('status','integer',['length'=>1,'default'=>1,'comment'=>'状态状态(0->禁用 1->启用)'])
            ->addTimestamps();
        $table->create();
    }

    public function createArticleComment()
    {
        $table_name = 'article_comment';
        $table = $this->table($table_name,['comment'=>'文章评论表'])
            ->addColumn('article_manage_id','integer',['length'=>11,'comment'=>'文章ID'])
            ->addColumn('nickname','string',['length'=>191,'null'=>true,'comment'=>'昵称'])
            ->addColumn('email','string',['length'=>191,'null'=>true,'comment'=>'电子邮箱'])
            ->addColumn('content','string',['length'=>191,'null'=>true,'comment'=>'评论内容'])
            ->addColumn('create_user','integer',['length'=>11,'comment'=>'创建人ID'])
            ->addColumn('update_user','integer',['length'=>11,'comment'=>'最近编辑人ID'])
            ->addTimestamps();
        $table->create();
    }


    public function createArticleTimes()
    {
        $table_name = 'article_times';
        $table = $this->table($table_name,['comment'=>'文章热度表'])
            ->addColumn('article_manage_id','integer',['length'=>11,'comment'=>'文章ID'])
            ->addColumn('ip','string',['length'=>255,'comment'=>'访问的Ip地址'])
            ->addColumn('click_time','timestamp',['comment'=>'点击时间']);
        $table->create();
    }


}
