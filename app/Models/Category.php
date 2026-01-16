<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    // Поля таблицы
    public const string FIELD_ID = 'id';
    public const string FIELD_NAME = 'name';
    public const string FIELD_SLUG = 'slug';
    public const string FIELD_DESCRIPTION = 'description';
    public const string FIELD_CREATED_AT = 'created_at';
    public const string FIELD_UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::FIELD_NAME,
        self::FIELD_SLUG,
        self::FIELD_DESCRIPTION,
    ];

    protected $casts = [
        self::FIELD_CREATED_AT => 'datetime',
        self::FIELD_UPDATED_AT => 'datetime',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, Product::FIELD_CATEGORY_ID);
    }
}
