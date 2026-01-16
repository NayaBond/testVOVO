<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Http\Requests\ProductSearchRequest as SearchRequest;

class Product extends Model
{

    // Поля таблицы (строго по ТЗ)
    public const string FIELD_ID = 'id';
    public const string FIELD_NAME = 'name';
    public const string FIELD_PRICE = 'price';
    public const string FIELD_CATEGORY_ID = 'category_id';
    public const string FIELD_IN_STOCK = 'in_stock';
    public const string FIELD_RATING = 'rating';
    public const string FIELD_CREATED_AT = 'created_at';
    public const string FIELD_UPDATED_AT = 'updated_at';

    // Значения сортировки (строго по ТЗ)
    public const string SORT_PRICE_ASC = 'price_asc';
    public const string SORT_PRICE_DESC = 'price_desc';
    public const string SORT_RATING_DESC = 'rating_desc';
    public const string SORT_NEWEST = 'newest';

    protected $fillable = [
        self::FIELD_NAME,
        self::FIELD_PRICE,
        self::FIELD_CATEGORY_ID,
        self::FIELD_IN_STOCK,
        self::FIELD_RATING,
    ];

    protected $casts = [
        self::FIELD_PRICE => 'decimal:2',
        self::FIELD_IN_STOCK => 'boolean',
        self::FIELD_RATING => 'float',
    ];

    /**
     * Отношение к категории
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // =========== SCOPES ДЛЯ ФИЛЬТРАЦИИ ===========

    /**
     * Поиск по подстроке в name (параметр q)
     */
    public function scopeSearchByName(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function (Builder $q, string $search) {
            return $q->where(self::FIELD_NAME, 'like', "%{$search}%");
        });
    }

    /**
     * Фильтр по минимальной цене (price_from)
     */
    public function scopePriceFrom(Builder $query, ?float $price): Builder
    {
        return $query->when(!is_null($price), function (Builder $q) use ($price) {
            return $q->where(self::FIELD_PRICE, '>=', $price);
        });
    }

    /**
     * Фильтр по максимальной цене (price_to)
     */
    public function scopePriceTo(Builder $query, ?float $price): Builder
    {
        return $query->when(!is_null($price), function (Builder $q) use ($price) {
            return $q->where(self::FIELD_PRICE, '<=', $price);
        });
    }

    /**
     * Фильтр по категории (category_id)
     */
    public function scopeByCategory(Builder $query, ?int $categoryId): Builder
    {
        return $query->when($categoryId, function (Builder $q, int $categoryId) {
            return $q->where(self::FIELD_CATEGORY_ID, $categoryId);
        });
    }

    /**
     * Фильтр по наличию (in_stock)
     */
    public function scopeStockStatus(Builder $query, ?bool $status): Builder
    {
        return $query->when(!is_null($status), function (Builder $q) use ($status) {
            return $q->where(self::FIELD_IN_STOCK, $status);
        });
    }

    /**
     * Фильтр по минимальному рейтингу (rating_from)
     */
    public function scopeRatingFrom(Builder $query, ?float $rating): Builder
    {
        return $query->when(!is_null($rating), function (Builder $q) use ($rating) {
            return $q->where(self::FIELD_RATING, '>=', $rating);
        });
    }

    // =========== SCOPES ДЛЯ СОРТИРОВКИ (строго по ТЗ) ===========

    /**
     * Сортировка по цене по возрастанию
     */
    public function scopeSortByPriceAsc(Builder $query): Builder
    {
        return $query->orderBy(self::FIELD_PRICE, 'asc');
    }

    /**
     * Сортировка по цене по убыванию
     */
    public function scopeSortByPriceDesc(Builder $query): Builder
    {
        return $query->orderBy(self::FIELD_PRICE, 'desc');
    }

    /**
     * Сортировка по рейтингу по убыванию
     */
    public function scopeSortByRatingDesc(Builder $query): Builder
    {
        return $query->orderBy(self::FIELD_RATING, 'desc');
    }

    /**
     * Сортировка по новизне
     */
    public function scopeSortByNewest(Builder $query): Builder
    {
        return $query->orderBy(self::FIELD_CREATED_AT, 'desc');
    }

    /**
     * Применение сортировки на основе параметра sort
     */
    public function scopeApplySorting(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            self::SORT_PRICE_ASC => $query->sortByPriceAsc(),
            self::SORT_PRICE_DESC => $query->sortByPriceDesc(),
            self::SORT_RATING_DESC => $query->sortByRatingDesc(),
            self::SORT_NEWEST => $query->sortByNewest(),
            default => $query->sortByNewest(),
        };
    }

    /**
     * Применение всех фильтров (строго по ТЗ)
     */
    public function scopeApplyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when(isset($filters[SearchRequest::ARG_SEARCH_QUERY]),
                fn($q) => $q->searchByName($filters[SearchRequest::ARG_SEARCH_QUERY]))
            ->when(isset($filters[SearchRequest::ARG_PRICE_FROM]),
                fn($q) => $q->priceFrom($filters[SearchRequest::ARG_PRICE_FROM]))
            ->when(isset($filters[SearchRequest::ARG_PRICE_TO]),
                fn($q) => $q->priceTo($filters[SearchRequest::ARG_PRICE_TO]))
            ->when(isset($filters[SearchRequest::ARG_CATEGORY_ID]),
                fn($q) => $q->byCategory($filters[SearchRequest::ARG_CATEGORY_ID]))
            ->when(isset($filters[SearchRequest::ARG_IN_STOCK]),
                fn($q) => $q->stockStatus($filters[SearchRequest::ARG_IN_STOCK]))
            ->when(isset($filters[SearchRequest::ARG_RATING_FROM]),
                fn($q) => $q->ratingFrom($filters[SearchRequest::ARG_RATING_FROM]));
    }

    /**
     * Получение доступных опций сортировки (для валидации)
     */
    public static function getSortOptions(): array
    {
        return [
            self::SORT_PRICE_ASC,
            self::SORT_PRICE_DESC,
            self::SORT_RATING_DESC,
            self::SORT_NEWEST,
        ];
    }
}
