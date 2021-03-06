@extends('layouts.app')

@section('content')

    <div class="container">
        <h1>Просмотр товара</h1>
        <hr>

        <div class="row">
            <div class="col-4 text-right">
                <strong>Магазины</strong>
            </div>
            <div class="col-8">
                <ul>
                    @foreach ($item->shops as $shop)
                        <li>{{ $shop->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-4 text-right">
                <strong>Бренд</strong>
            </div>
            <div class="col-8">{{ $item->brand->name }}</div>
        </div>

        <div class="row">
            <div class="col-4 text-right">
                <strong>Страна</strong>
            </div>
            <div class="col-8">{{ $item->country ? $item->country->name : '' }}</div>
        </div>

        <div class="row">
            <div class="col-4 text-right">
                <strong>Группа</strong>
            </div>
            <div class="col-8">{{ $item->group ? $item->group->name : ''}}</div>
        </div>

        <div class="row">
            <div class="col-4 text-right">
                <strong>Тип</strong>
            </div>
            <div class="col-8">{{ $item->type }}</div>
        </div>

        <div class="row">
            <div class="col-4 text-right">
                <strong>Ароматы</strong>
            </div>
            <div class="col-8">
                <ul>
                    @foreach ($item->aromas as $aroma)
                        <li>{{ $aroma->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-4 text-right">
                <strong>Артикул</strong>
            </div>
            <div class="col-8">{{ $item->article }}</div>
        </div>

        <div class="row">
            <div class="col-4 text-right">
                <strong>Название</strong>
            </div>
            <div class="col-8">{{ $item->name }}</div>
        </div>

        <div class="row">
            <div class="col-4 text-right">
                <strong>Описание</strong>
            </div>
            <div class="col-8">{{ $item->description }}</div>
        </div>

        <div class="row">
            <div class="col-4 text-right">
                <strong>Объем</strong>
            </div>
            <div class="col-8">{{ $item->volume }}</div>
        </div>

        <div class="row">
            <div class="col-4 text-right">
                <strong>Год выхода</strong>
            </div>
            <div class="col-8">{{ $item->year }}</div>
        </div>

        <div class="row">
            <div class="col-4 text-right">
                <strong>Остаток</strong>
            </div>
            <div class="col-8">{{ $item->stock }}</div>
        </div>

        <div class="row">
            <div class="col-4 text-right">
                <strong>Является тестером</strong>
            </div>
            <div class="col-8">{{ $item->is_tester ? 'Да' : 'Нет' }}</div>
        </div>

        <div class="row">
            <div class="col-4 text-right">
                <strong>Тэги</strong>
            </div>
            <div class="col-8">
                <ul>
                    @foreach ($item->tags as $tag)
                        <li>{{ $tag->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <h2 class="mt-5">Предложения поставщиков</h2>

        <table-component api-link="{{ $apiLink }}"></table-component>

    </div>
@endsection
