<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductSearchRequest;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    /**
     * Display a listing of products with filters and sorting.
     */
    public function index(ProductSearchRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $query = Product::query()
            ->with($this->getRelationships())
            ->applyFilters($this->extractFilters($validated));

        $sort = $validated[ProductSearchRequest::ARG_SORT] ?? null;
        $query->applySorting($sort);

        $perPage = $validated[ProductSearchRequest::ARG_PER_PAGE] ?? ProductSearchRequest::DEFAULT_PER_PAGE;

        $products = $query->paginate($perPage);

        return ProductResource::collection($products)
            ->additional($this->getMetaData($validated));
    }

    /**
     * Get relationships to eager load.
     */
    protected function getRelationships(): array
    {
        return [
            'category'
        ];
    }

    /**
     * Extract filter parameters from validated data.
     */
    protected function extractFilters(array $validated): array
    {
        return [
            ProductSearchRequest::ARG_SEARCH_QUERY => $validated[ProductSearchRequest::ARG_SEARCH_QUERY] ?? null,
            ProductSearchRequest::ARG_PRICE_FROM => $validated[ProductSearchRequest::ARG_PRICE_FROM] ?? null,
            ProductSearchRequest::ARG_PRICE_TO => $validated[ProductSearchRequest::ARG_PRICE_TO] ?? null,
            ProductSearchRequest::ARG_CATEGORY_ID => $validated[ProductSearchRequest::ARG_CATEGORY_ID] ?? null,
            ProductSearchRequest::ARG_IN_STOCK => $validated[ProductSearchRequest::ARG_IN_STOCK] ?? null,
            ProductSearchRequest::ARG_RATING_FROM => $validated[ProductSearchRequest::ARG_RATING_FROM] ?? null,
        ];
    }

    /**
     * Get additional metadata for the response.
     */
    protected function getMetaData(array $validated): array
    {
        return [
            'meta' => [
                'filters_applied' => array_keys(array_filter($this->extractFilters($validated))),
                'sort_applied' => $validated[ProductSearchRequest::ARG_SORT] ?? ProductSearchRequest::SORT_DEFAULT,
                'available_sorts' => ProductSearchRequest::getSortOptions(),
            ],
        ];
    }
}
