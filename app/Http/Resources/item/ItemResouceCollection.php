<?php

namespace App\Http\Resources\item;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemResouceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $column = $request->input('column');
        if ($column && !in_array($column, ['article', 'brand_name', 'country_name', 'item_name', 'stock'])) {
            $column = null;
        }

        $order = $request->input('order');
        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }

        return [
            'data' => $this->collection,
            'columns' => [
                [
                    'class' => ['text-center'],
                    'sortable' => true,
                    'sort' => ($column === 'article') ? $order : false,
                    'type' => 'text',
                    'code' => 'article',
                    'title' => 'Артикул'
                ],
                [
                    'class' => ['text-center'],
                    'sortable' => true,
                    'sort' => ($column === 'brand_name') ? $order : false,
                    'type' => 'text',
                    'code' => 'brand_name',
                    'title' => 'Бренд'
                ],
                [
                    'class' => ['text-center'],
                    'sortable' => true,
                    'sort' => ($column === 'country_name') ? $order : false,
                    'type' => 'text',
                    'code' => 'country_name',
                    'title' => 'Страна'
                ],
                [
                    'class' => '',
                    'sortable' => true,
                    'sort' => ($column === 'item_name') ? $order : false,
                    'type' => 'text',
                    'code' => 'item_name',
                    'title' => 'Наименование товара'
                ],
                [
                    'class' => ['text-center'],
                    'sortable' => true,
                    'sort' => ($column === 'stock') ? $order : false,
                    'type' => 'text',
                    'code' => 'stock',
                    'title' => 'Остаток'
                ],
                [
                    'class' => ['text-center'],
                    'sortable' => false,
                    'type' => 'component',
                    'code' => 'func',
                    'title' => 'Функции',
                ],
            ],
        ];
    }
}
