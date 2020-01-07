<?php

namespace App\Http\Controllers\Api;

use App\Shop;
use App\ShopItem;
use App\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResourceCollection;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shops = Shop::query();

        $query = $request->input('q', null);
        if ($query) {
            $shops->where('name', 'ilike', "%{$query}%");
        }

        $column = $request->input('column', 'name');
        if (!in_array($column, ['name'])) {
            $column = 'name';
        }

        $order = $request->input('order', 'asc');
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }

        $shops->orderby($column, $order);
        $page = $request->input('page', 1);

        $shops = $shops->paginate(30, ['*'], 'page', $page);

        return new ShopResourceCollection($shops);
    }

    /**
     * Remove Item from the shop
     *
     * @param Item $item
     * @return \Illuminate\Http\Response
     */
    public function remove(Shop $shop, Item $item)
    {
        ShopItem::query()->where([
            'item_id' => $item->id,
            'shop_id' => $shop->id,
        ])->delete();
        return response()->json(null, 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shop $shop)
    {
        $shop->delete();

        return response()->json(null, 204);

    }
}
