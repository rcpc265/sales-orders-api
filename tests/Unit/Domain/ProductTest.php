<?php

use App\Models\Product;

it('has expected fillable attributes', function () {
    // Given
    $product = new Product();

    // When
    $fillable = $product->getFillable();

    // Then
    expect($fillable)->toEqual(['name', 'sku', 'price', 'stock']);
});
