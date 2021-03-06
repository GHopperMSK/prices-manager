<?php

namespace App\Http\Resources\item;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ContractorItemUnrelatedResourceCollection extends ResourceCollection
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
        if ($column && !in_array($column, ['contractor_name', 'real_article', 'name', 'price'])) {
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
                    'sort' => ($column === 'contractor_name') ? $order : false,
                    'type' => 'text',
                    'code' => 'contractor_name',
                    'title' => 'Наименование поставщика'
                ],
                [
                    'class' => ['text-center'],
                    'sortable' => true,
                    'sort' => ($column === 'real_article') ? $order : false,
                    'type' => 'text',
                    'code' => 'real_article',
                    'title' => 'Артикул'
                ],
                [
                    'class' => '',
                    'sortable' => true,
                    'sort' => ($column === 'name') ? $order : false,
                    'type' => 'text',
                    'code' => 'name',
                    'title' => 'Наименование товара'
                ],
                [
                    'class' => ['text-center'],
                    'sortable' => true,
                    'sort' => ($column === 'price') ? $order : false,
                    'type' => 'text',
                    'code' => 'price',
                    'title' => 'Цена'
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
