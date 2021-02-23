<?php


namespace data\model;

use think\model\relation\HasOne;

class ArticleComment extends Base
{

    /**
     * 文章信息一对一
     * @return HasOne
     */
    public function articleInfo() : HasOne
    {
        return $this->hasOne(ArticleManages::class,'id','article_manage_id');
    }

}