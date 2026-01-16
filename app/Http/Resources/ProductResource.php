<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    // Константы для ключей в ответе
    public const string RESPONSE_ID = 'id';
    public const string RESPONSE_NAME = 'name';
    public const string RESPONSE_PRICE = 'price';
    public const string RESPONSE_CATEGORY_ID = 'category_id';
    public const string RESPONSE_IN_STOCK = 'in_stock';
    public const string RESPONSE_RATING = 'rating';
    public const string RESPONSE_CREATED_AT = 'created_at';
    public const string RESPONSE_UPDATED_AT = 'updated_at';
    public const string RESPONSE_CATEGORY = 'category';
    public const string RESPONSE_FORMATTED_PRICE = 'formatted_price';

    // Форматы дат
    public const string DATE_FORMAT_ISO = 'Y-m-d H:i:s';
    public const string DATE_FORMAT_HUMAN = 'd.m.Y H:i';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $baseData = $this->getBaseData();

        if ($this->relationLoaded('category')) {
            $baseData[self::RESPONSE_CATEGORY] = $this->getCategoryData();
        }

        return $baseData;
    }

    /**
     * Get base product data
     */
    protected function getBaseData(): array
    {
        return [
            self::RESPONSE_ID => $this->getAttribute(Product::FIELD_ID),
            self::RESPONSE_NAME => $this->getAttribute(Product::FIELD_NAME),
            self::RESPONSE_PRICE => $this->getPrice(),
            self::RESPONSE_CATEGORY_ID => $this->getAttribute(Product::FIELD_CATEGORY_ID),
            self::RESPONSE_IN_STOCK => (bool) $this->getAttribute(Product::FIELD_IN_STOCK),
            self::RESPONSE_RATING => $this->getRating(),
            self::RESPONSE_CREATED_AT => $this->getFormattedDate(Product::FIELD_CREATED_AT),
            self::RESPONSE_UPDATED_AT => $this->getFormattedDate(Product::FIELD_UPDATED_AT),
        ];
    }

    /**
     * Get category data if loaded
     */
    protected function getCategoryData(): ?array
    {
        if (!$this->category) {
            return null;
        }

        return [
            Category::FIELD_ID => $this->category->getAttribute(Category::FIELD_ID),
            Category::FIELD_NAME => $this->category->getAttribute(Category::FIELD_NAME),
        ];
    }

    /**
     * Get formatted price
     */
    protected function getPrice(): float
    {
        return (float) $this->getAttribute(Product::FIELD_PRICE);
    }

    /**
     * Get formatted rating
     */
    protected function getRating(): float
    {
        return round((float) $this->getAttribute(Product::FIELD_RATING), 1);
    }

    /**
     * Get formatted date
     */
    protected function getFormattedDate(string $field): ?string
    {
        $date = $this->getAttribute($field);

        if (!$date) {
            return null;
        }

        return $date->format(self::DATE_FORMAT_ISO);
    }

    /**
     * Get response with additional formatted fields
     */
    public function withFormattedFields(Request $request): array
    {
        $data = $this->toArray($request);

        $data[self::RESPONSE_FORMATTED_PRICE] = $this->getFormattedPrice();
        $data['rating_formatted'] = number_format($this->getRating(), 1);
        $data['created_at_human'] = $this->getHumanDate(Product::FIELD_CREATED_AT);
        $data['stock_status'] = $this->getStockStatus();

        return $data;
    }

    /**
     * Get formatted price with currency
     */
    protected function getFormattedPrice(): string
    {
        return number_format($this->getPrice(), 2, '.', ' ') . ' ₽';
    }

    /**
     * Get human-readable date
     */
    protected function getHumanDate(string $field): ?string
    {
        $date = $this->getAttribute($field);

        if (!$date) {
            return null;
        }

        return $date->format(self::DATE_FORMAT_HUMAN);
    }

    /**
     * Get stock status text
     */
    protected function getStockStatus(): string
    {
        return $this->getAttribute(Product::FIELD_IN_STOCK)
            ? 'В наличии'
            : 'Нет в наличии';
    }

    /**
     * Customize the response with metadata
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'api_version' => '1.0',
                'product_fields' => $this->getResponseKeys(),
                'timestamp' => now()->toIso8601String(),
            ],
        ];
    }

    /**
     * Get all response keys for documentation
     */
    public static function getResponseKeys(): array
    {
        return [
            self::RESPONSE_ID,
            self::RESPONSE_NAME,
            self::RESPONSE_PRICE,
            self::RESPONSE_CATEGORY_ID,
            self::RESPONSE_IN_STOCK,
            self::RESPONSE_RATING,
            self::RESPONSE_CREATED_AT,
            self::RESPONSE_UPDATED_AT,
            self::RESPONSE_CATEGORY,
        ];
    }

    /**
     * Get minimal product data (for lists)
     */
    public function toMinimalArray(Request $request): array
    {
        return [
            self::RESPONSE_ID => $this->getAttribute(Product::FIELD_ID),
            self::RESPONSE_NAME => $this->getAttribute(Product::FIELD_NAME),
            self::RESPONSE_PRICE => $this->getPrice(),
            self::RESPONSE_IN_STOCK => (bool) $this->getAttribute(Product::FIELD_IN_STOCK),
            self::RESPONSE_RATING => $this->getRating(),
        ];
    }
}
