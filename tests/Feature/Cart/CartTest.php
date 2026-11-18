<?php

namespace Tests\Feature\Cart;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_update_and_remove_cart_items(): void
    {
        $product = Product::factory()->create();

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->patch(route('cart.update', $product), ['quantity' => 3])
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $this->delete(route('cart.destroy', $product))
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_cart_quantity_cannot_exceed_available_stock(): void
    {
        $product = Product::factory()->create();
        $product->inventory()->update(['quantity' => 2]);

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 5,
        ])->assertSessionHasErrors('quantity');

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_cart_page_shows_subtotal(): void
    {
        $product = Product::factory()->create(['price' => '25000.00']);

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('NGN 50,000.00');
    }
}
