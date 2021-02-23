<?php

namespace app\admin\validate;

use data\model\ArticleTags;
use data\model\ArticleTypes;
use think\Validate;

class ArticleManage extends Validate
{

    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'id|ID'=>'require|integer',
        'title|文章标题' => 'require|chsDash|max:30',
        'sub_title|文章副标题' => 'require|chsDash|max:20',
        'article_type|文章类型' => 'require|articleTypeScene',
        'article_tag|文章标签' => 'require|array|articleTagScene',
        'sort_no|序号' => 'require|integer|min:0',
        'keywords|关键字' => 'chsDash|max:255',
        'content_summary|文章摘要' => 'max:200',
        'is_allow_comment|是否允许评论' => 'integer|in:0,1',
        'comment_start_time|评论起始时间' => 'date',
        'comment_end_time|评论结束时间' => 'date'
    ];

    /**
     * 验证场景
     * @var string[]
     */
    protected $scene = [
        'create' => [
            'title','sub_title','article_type','article_tag','sort_no','keywords','content_summary','is_allow_comment',
            'comment_start_time','comment_end_time'
        ],
        'update' => [
            'id','title','sub_title','article_type','article_tag','sort_no','keywords','content_summary',
            'is_allow_comment', 'comment_start_time','comment_end_time'
        ]
    ];

    /**
     * 验证分类
     * @param string $value
     * @param string $rule
     * @param array $data
     * @return bool|string
     */
    protected function articleTypeScene(string $value,string $rule,array $data)
    {
        $article_type_info = ArticleTypes::where([
            ['id','=',$data['article_type']],
            ['status','=',1]
        ])->find();

        if ($article_type_info === null){
            return '当前分类不存在';
        }
        return true;
    }

    /**
     * 验证标签
     * @param mixed $value
     * @param mixed  $rule
     * @param array $data
     * @return bool|string
     */
    protected function articleTagScene( $value, $rule,array $data)
    {
        $article_type_info = ArticleTags::where([
            ['status','=',1],
            ['id','IN',$data['article_tag']]
        ])->select();

        if ($article_type_info === null){
            return '当前标签不存在';
        }
        return true;
    }

}
