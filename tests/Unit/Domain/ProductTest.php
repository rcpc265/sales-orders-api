<?php

namespace Tests\Unit\Domain;

use App\Models\Product;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function test_product_has_expected_fillable_attributes(): void
    {
        // Given
        $product = new Product();

        // When
        $fillable = $product->getFillable();

        // Then
        $this->assertEquals(['name', 'sku', 'price', 'stock'], $fillable);
    }
}
