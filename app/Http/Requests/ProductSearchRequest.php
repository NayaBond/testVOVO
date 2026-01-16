<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductSearchRequest extends FormRequest
{
    // Константы для аргументов запроса
    public const string ARG_SEARCH_QUERY = 'q';
    public const string ARG_PRICE_FROM = 'price_from';
    public const string ARG_PRICE_TO = 'price_to';
    public const string ARG_CATEGORY_ID = 'category_id';
    public const string ARG_IN_STOCK = 'in_stock';
    public const string ARG_RATING_FROM = 'rating_from';
    public const string ARG_SORT = 'sort';
    public const string ARG_PER_PAGE = 'per_page';
    public const string ARG_PAGE = 'page';

    // Константы для значений сортировки
    public const string SORT_PRICE_ASC = 'price_asc';
    public const string SORT_PRICE_DESC = 'price_desc';
    public const string SORT_RATING_DESC = 'rating_desc';
    public const string SORT_NEWEST = 'newest';
    public const string SORT_DEFAULT = self::SORT_NEWEST;

    // Константы для ограничений
    public const int MAX_SEARCH_LENGTH = 255;
    public const int MIN_PRICE = 0;
    public const int MIN_RATING = 0;
    public const int MAX_RATING = 5;
    public const int MIN_PER_PAGE = 1;
    public const int MAX_PER_PAGE = 100;
    public const int MIN_PAGE = 1;
    public const int DEFAULT_PER_PAGE = 15;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            self::ARG_SEARCH_QUERY => 'nullable|string|max:' . self::MAX_SEARCH_LENGTH,
            self::ARG_PRICE_FROM => 'nullable|numeric|min:' . self::MIN_PRICE,
            self::ARG_PRICE_TO => 'nullable|numeric|min:' . self::MIN_PRICE . '|gte:' . self::ARG_PRICE_FROM,
            self::ARG_CATEGORY_ID => 'nullable|integer|exists:categories,id',
            self::ARG_IN_STOCK => 'nullable|boolean',
            self::ARG_RATING_FROM => 'nullable|numeric|min:' . self::MIN_RATING . '|max:' . self::MAX_RATING,
            self::ARG_SORT => [
                'nullable',
                Rule::in(self::getSortOptions())
            ],
            self::ARG_PER_PAGE => 'nullable|integer|min:' . self::MIN_PER_PAGE . '|max:' . self::MAX_PER_PAGE,
            self::ARG_PAGE => 'nullable|integer|min:' . self::MIN_PAGE,
        ];
    }

    /**
     * Get available sort options
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

    /**
     * Get default values for the request
     */
    public function prepareForValidation(): void
    {
        $this->mergeIfMissing([
            self::ARG_PER_PAGE => self::DEFAULT_PER_PAGE,
            self::ARG_SORT => self::SORT_DEFAULT,
        ]);
    }
}
