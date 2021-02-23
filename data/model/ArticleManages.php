<?php


namespace data\model;


use think\Model;
use think\model\concern\SoftDelete;
use think\model\relation\HasMany;
use think\model\relation\HasOne;

class ArticleManages extends Model
{

    use SoftDelete;

    protected $autoWriteTimestamp = 'datetime';

    protected $deleteTime = 'delete_time';

    protected $append = ['status_text'];

    protected $status_text = [
        0 => '草稿',
        1 => '待上架',
        2 => '上架',
        3 => '下架'
    ];

    /**
     * 文章状态
     * @param $data
     * @param $value
     * @return string
     */
    public function getStatusTextAttr($data,$value) : string
    {
        return $this->status_text[$data['status'] ?? 0] ?? '未知状态';
    }

    /**
     * 文件分类
     * @return HasOne
     */
    public function articleTypeInfo() : HasOne
    {
        return $this->hasOne(ArticleTypes::class,'id','article_type');
    }

    /**
     * 文章点击率
     * @return HasMany
     */
    public function articleTimes() : HasMany
    {
        return $this->hasMany(ArticleTimes::class,'article_manage_id','id');
    }

    /**
     * 文章评论
     * @return HasMany
     */
    public function articleComment():HasMany
    {
        return $this->hasMany(ArticleComment::class,'article_manage_id','id');
    }

}