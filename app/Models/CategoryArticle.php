<?php

namespace App\Models;

use App\Utils\HelperFunc;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryArticle extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "categories_articles";

    protected $fillable = [
        'id',
        'name',
        'slug',
        'parent_id',
        'description',
        'status',
        'image'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = HelperFunc::getTimestampAsId();
        });
    }

    public function article()
    {
        return $this->hasMany(Article::class);
    }

    public function parent()
    {
        return $this->belongsTo(CategoryArticle::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CategoryArticle::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    public function getLevelAttribute()
    {
        $level = 0;
        $parent = $this->parent;
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }
        return $level;
    }

    public static function getTreeList($parentId = null, $level = 0)
    {
        $result = [];
        $categories = self::where('parent_id', $parentId)->orderBy('name')->get();
        foreach ($categories as $category) {
            $category->level = $level;
            $result[] = $category;
            $result = array_merge($result, self::getTreeList($category->id, $level + 1));
        }
        return $result;
    }
}
