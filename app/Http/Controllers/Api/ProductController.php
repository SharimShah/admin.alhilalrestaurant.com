<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function show($slug)
    {
        // Fetch product by slug
        $product = DB::table('products')
            ->whereRaw('BINARY `slug` = ?', [$slug])
            ->where('active_product', 1)
            ->first();

        // Check if product exists
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Format product fields
        $product->cover_image = url($product->cover_image);
        $product->price = number_format($product->price, 0, '.', ',');
        $product->cut_price = number_format($product->cut_price, 0, '.', ',');

        // 🔹 Fetch addons
        $addons = DB::table('product_addons')
            ->where('product_id', $product->id)
            ->orderBy('sequence')
            ->get()
            ->map(function ($addon) {
                $items = DB::table('product_addon_items')
                    ->where('product_addon_id', $addon->id)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'sub_item_id' => (string) $item->id,
                            'sub_item_name' => $item->sub_item_name,
                            'price' => (float) $item->price,
                            'checked' => (bool) $item->checked,
                        ];
                    });

                return [
                    'subcat_id' => (string) $addon->id,
                    'sequence' => $addon->sequence,
                    'subcat_name' => $addon->subcat_name,
                    'multi_option' => $addon->multi_option,
                    'require_addons' => (bool) $addon->require_addons,
                    'sub_item' => $items,
                ];
            });

        // 🔹 Fetch modifiers
        $modifiers = DB::table('modifier_product')
            ->join('products', 'modifier_product.modifier_id', '=', 'products.id')
            ->where('modifier_product.product_id', $product->id)
            ->distinct() // ✅ prevents duplicates
            ->select('products.*') // only select modifier product columns

            ->get()
            ->map(function ($modifier) {
                $modifier->cover_image = url($modifier->cover_image);
                $modifier->price = number_format($modifier->price, 0, '.', ',');
                return $modifier;
            });

        $product->addons = $addons;
        $product->modifiers = $modifiers; // ✅ cleaner assignment
        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    }
}
