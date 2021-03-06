<?php

namespace App\Http\Controllers\Api;

use App\Item;
use App\Shop;
use App\ShopItem;
use App\Brand;
use App\Country;
use App\Group;
use App\Contractor;
use App\Http\Resources\item\ItemResouceCollection;
use App\Http\Resources\item\ItemShopResourceCollection;
use App\Http\Resources\item\ItemGroupResourceCollection;
use App\Http\Resources\item\ItemBrandResourceCollection;
use App\Http\Resources\item\ItemCountryResourceCollection;
use App\Http\Resources\item\ItemUnrelatedResouceCollection;
use App\Http\Resources\item\ItemRelatedResourceCollection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ItemController extends Controller
{
    /**
     * List of items
     *
     * @param Request $request
     * @return ItemResouceCollection
     */
    public function index(Request $request)
    {
        $column = $request->input('column');
        if (!in_array($column, ['article', 'brand_name', 'country_name', 'item_name', 'stock'])) {
            $column = null;
        }
        $order = $request->input('order');
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }

        $query = $request->input('q', null);
        $items = Item::smartSearch($query)
            ->select([
                'items.id as id',
                'article',
                'brands.name as brand_name',
                'countries.name as country_name',
                'items.name as item_name',
                'stock',
            ])
            ->leftJoin('brands', 'items.brand_id', '=', 'brands.id')
            ->leftJoin('countries', 'items.country_id', '=', 'countries.id');

        if (!$query) {
            if ($column) {
                $items->orderBy($column, $order);
            } else {
                $items->orderBy('items.updated_at', 'desc');
            }
        }

        $page = $request->input('page', 1);

        $items = $items->paginate(30, ['*'], 'page', $page);

        return new ItemResouceCollection($items);
    }

    /**
     * List of unrelated items
     *
     * @param Request $request
     * @return ItemResouceCollection
     */
    public function indexUnrelated(Request $request, Contractor $contractor)
    {
        $column = $request->input('column');
        if (!in_array($column, ['article', 'brand_name', 'item_name'])) {
            $column = null;
        }
        $order = $request->input('order');
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }

        $query = $request->input('q', null);
        $items = Item::smartSearch($query)
            ->select([
                'items.id as id',
                'article',
                'brands.name as brand_name',
                'items.name as item_name',
            ])
            ->leftJoin('brands', 'items.brand_id', '=', 'brands.id')
            ->unrelated($contractor->id);

        if (!$query) {
            if ($column) {
                $items->orderBy($column, $order);
            } else {
                $items->orderBy('items.updated_at', 'desc');
            }
        }

        $page = $request->input('page', 1);

        $items = $items->paginate(30, ['*'], 'page', $page);

        return new ItemUnrelatedResouceCollection($items);
    }

    /**
     * List of related items
     *
     * @param Request $request
     * @param Item $item
     * @return ItemResouceCollection
     */
    public function relatedItems(Request $request, Item $item)
    {
        $column = $request->input('column');
        if (!in_array($column, ['contractor', 'name'])) {
            $column = 'name';
        }
        $order = $request->input('order');
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }

        $items = $item->contractorItems()
            ->select([
                'contractor_items.*',
                'contractors.name as contractor',
            ])
            ->leftJoin('contractors', 'contractor_items.contractor_id', '=', 'contractors.id');

        $query = $request->input('q', null);
        if ($query) {
            $items->where('contractor_items.name', 'ilike', "%{$query}%");
        }

        if ($column) {
            $items->orderBy($column, $order);
        } else {
            $items->orderBy('items.updated_at', 'desc');
        }

        $page = $request->input('page', 1);

        $items = $items->paginate(30, ['*'], 'page', $page);

        return new ItemRelatedResourceCollection($items);
    }

    /**
     * Remove the specified item from storage
     *
     * @param Item $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return response()->json(null, 204);
    }

    /**
     * Remove the specified item from storage
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function itemsDestroy(Request $request)
    {
        Item::whereIn('id', $request->ids)->delete();

        return response()->json(null, 200);
    }

    /**
     * List of brand items
     *
     * @param Request $request
     * @param Brand $brand
     * @return ItemBrandResourceCollection
     */
    public function brandItems(Request $request, Brand $brand)
    {
        $column = $request->input('column');
        if (!in_array($column, ['article', 'name', 'stock'])) {
            $column = null;
        }
        $order = $request->input('order');
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }

        $items = Item::where(['brand_id' => $brand->id]);

        $query = $request->input('q', null);
        if ($query) {
            $items->where('name', 'ilike', "%{$query}%");
        }

        if (!$query) {
            if ($column) {
                $items->orderBy($column, $order);
            } else {
                $items->orderBy('updated_at', 'desc');
            }
        }

        $page = $request->input('page', 1);

        $items = $items->paginate(30, ['*'], 'page', $page);

        return new ItemBrandResourceCollection($items);
    }

    /**
     * List of Country items
     *
     * @param Request $request
     * @param Country $country
     * @return ItemCountryResourceCollection
     */
    public function countryItems(Request $request, Country $country)
    {
        $column = $request->input('column');
        if (!in_array($column, ['article', 'name', 'stock'])) {
            $column = null;
        }
        $order = $request->input('order');
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }

        $items = Item::where(['country_id' => $country->id]);

        $query = $request->input('q', null);
        if ($query) {
            $items->where('name', 'ilike', "%{$query}%");
        }

        if (!$query) {
            if ($column) {
                $items->orderBy($column, $order);
            } else {
                $items->orderBy('updated_at', 'desc');
            }
        }

        $page = $request->input('page', 1);

        $items = $items->paginate(30, ['*'], 'page', $page);

        return new ItemCountryResourceCollection($items);
    }

    /**
     * List of shop items
     *
     * @param Request $request
     * @param Brand $group
     * @return ItemShopResourceCollection
     */
    public function shopItems(Request $request, Shop $shop)
    {
        $column = $request->input('column');
        if (!in_array($column, ['article', 'name', 'price', 'stock'])) {
            $column = null;
        }
        $order = $request->input('order');
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }

        $items = $shop->items();

        $query = $request->input('q', null);
        if ($query) {
            $items->where('name', 'ilike', "%{$query}%");
        }

        if (!$query) {
            if ($column) {
                $items->orderBy($column, $order);
            } else {
                $items->orderBy('updated_at', 'desc');
            }
        }

        $page = $request->input('page', 1);

        $items = $items->paginate(30, ['*'], 'page', $page);

        return new ItemShopResourceCollection($items);
    }

    /**
     * List of group items
     *
     * @param Request $request
     * @param Brand $group
     * @return ItemGroupResourceCollection
     */
    public function groupItems(Request $request, Group $group)
    {
        $column = $request->input('column');
        if (!in_array($column, ['article', 'name', 'stock'])) {
            $column = null;
        }
        $order = $request->input('order');
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }

        $items = Item::where(['group_id' => $group->id]);

        $query = $request->input('q', null);
        if ($query) {
            $items->where('name', 'ilike', "%{$query}%");
        }

        if (!$query) {
            if ($column) {
                $items->orderBy($column, $order);
            } else {
                $items->orderBy('updated_at', 'desc');
            }
        }

        $page = $request->input('page', 1);

        $items = $items->paginate(30, ['*'], 'page', $page);

        return new ItemGroupResourceCollection($items);
    }

    /**
     * Add Items into Shops
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignItemToShop(Request $request)
    {
        $itemIds = $request->ids;
        $shopIds = $request->clarifyingStep;

        foreach($shopIds as $shopId) {
            foreach($itemIds as $itemId) {
                ShopItem::firstOrCreate([
                    'shop_id' => $shopId,
                    'item_id' => $itemId,
                ]);
            }
        }

        return response()->json(null, 200);
    }

    /**
     * Remove Items from Shops
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeItemFromShop(Request $request)
    {
        $itemIds = $request->ids;
        $shopIds = $request->clarifyingStep;

        foreach($shopIds as $shopId) {
            foreach($itemIds as $itemId) {
                ShopItem::query()->where([
                    'shop_id' => $shopId,
                    'item_id' => $itemId,
                ])->delete();
            }
        }

        return response()->json(null, 200);
    }

    /**
     * Remove Item from the Group
     *
     * @param Item $item
     * @return \Illuminate\Http\Response
     */
    public function groupRemove(Item $item)
    {
        $item->group_id = null;
        $item->save();

        return response()->json(null, 204);
    }
}
