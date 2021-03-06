@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-6">
                <h1>Магазины</h1>
            </div>
            <div class="col-6">
                <a href="{{ route('shop.create') }}"><button type="button" class="btn btn-link float-right">Добавить магазин</button></a>
            </div>
        </div>

        <table-component api-link="{{ $apiLink }}"></table-component>

    </div>
@endsection