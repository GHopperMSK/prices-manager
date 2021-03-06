@extends('layouts.app')

@section('content')

    <div class="container">
        <h1>Редактирование товара</h1>
        <hr>
        <form action="{{ route('item.update', $item->id) }}" method="POST">

            {{ csrf_field() }}

            <input type="hidden" name="_method" value="PUT">

            <div class="form-group">
                <label for="shop_id[]">Магазины</label>
                <select id="shop_id[]" name="shop_id[]" class="form-control" size="3" multiple>
                    @foreach ($shops as $shop)
                        <option value="{{ $shop->id }}"{{  in_array($shop->id, $item->shopIds()) ? ' selected' : '' }}>{{ $shop->name }}</option>
                    @endforeach
                </select>

                <div class="invalid-feedback">{{ $errors->first('shop_id') }}</div>
            </div>

            <div class="form-group required">
                <label for="brand_id">Бренд</label>
                <select id="brand_id" name="brand_id"
                        class="form-control{{ $errors->has('brand_id') ? ' is-invalid' : '' }}">
                    <option value="" disabled>Выберите бренд</option>
                    @foreach ($brands as $brand)
                        <option {{ $item->brand->id === $brand->id ? 'selected ' : '' }}value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>

                <div class="invalid-feedback">{{ $errors->first('brand_id') }}</div>
            </div>

            <div class="form-group">
                <label for="country_id">Страна</label>
                <select id="country_id" name="country_id"
                        class="form-control{{ $errors->has('country_id') ? ' is-invalid' : '' }}">
                    <option value="" {{ $item->country ? '' : 'selected ' }} disabled>Выберите страну</option>
                    @foreach ($countries as $country)
                        <option {{ ($item->country && $item->country->id === $country->id) ? 'selected ' : '' }}value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>

                <div class="invalid-feedback">{{ $errors->first('country_id') }}</div>
            </div>

            <div class="form-group">
                <label for="group_id">Группа</label>
                <select id="group_id" name="group_id"
                        class="form-control{{ $errors->has('group_id') ? ' is-invalid' : '' }}">
                    <option value="" disabled>Выберите группу</option>
                    @foreach ($groups as $group)
                        <option {{ $item->group && $item->group->id === $group->id ? 'selected ' : '' }}value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>

                <div class="invalid-feedback">{{ $errors->first('group_id') }}</div>
            </div>

            <div class="form-group required">
                <label for="type">Тип</label>
                <select id="type" name="type" class="form-control">
                    <option value="" disabled{{  old('type') ? '' : ' selected' }}>Выберите тип парфюмерной продукции</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}"{{  $item->type === $type ? ' selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>

                <div class="invalid-feedback">{{ $errors->first('type') }}</div>
            </div>

            <div class="form-group">
                <label for="aroma_id[]">Ароматы</label>
                <select id="aroma_id[]" name="aroma_id[]" class="form-control" size="3" multiple>
                    @foreach ($aromas as $aroma)
                        <option value="{{ $aroma->id }}"{{ in_array($aroma->id, $item->aromaIds())  ? ' selected' : '' }}>{{ $aroma->name }}</option>
                    @endforeach
                </select>

                <div class="invalid-feedback">{{ $errors->first('aroma_id') }}</div>
            </div>

            <div class="form-group required">
                <label for="article">Артикул</label>
                <input type="text"
                       class="form-control{{ $errors->has('article') ? ' is-invalid' : '' }}"
                       id="article"
                       name="article"
                       required
                       value="{{ $item->article }}">

                <div class="invalid-feedback">{{ $errors->first('article') }}</div>
            </div>

            <div class="form-group required">
                <label for="name">Название</label>
                <input type="text"
                       class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                       id="name"
                       name="name"
                       required
                       value="{{ $item->name }}">

                <div class="invalid-feedback">{{ $errors->first('name') }}</div>
            </div>

            <div class="form-group">
                <label for="description">Описание</label>
                <textarea
                    class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                    id="description"
                    name="description">{{ $item->description }}</textarea>

                <div class="invalid-feedback">{{ $errors->first('description') }}</div>
            </div>

            <div class="form-group">
                <label for="volume">Объем</label>
                <input type="text"
                       class="form-control{{ $errors->has('volume') ? ' is-invalid' : '' }}"
                       id="volume"
                       name="volume"
                       value="{{ $item->volume }}">

                <div class="invalid-feedback">{{ $errors->first('volume') }}</div>
            </div>

            <div class="form-group">
                <label for="year">Год выхода</label>
                <input type="number"
                       class="form-control{{ $errors->has('year') ? ' is-invalid' : '' }}"
                       id="year"
                       name="year"
                       value="{{ $item->year }}">

                <div class="invalid-feedback">{{ $errors->first('year') }}</div>
            </div>

            <div class="form-group">
                <label for="stock">Остаток</label>
                <input type="text"
                       class="form-control{{ $errors->has('stock') ? ' is-invalid' : '' }}"
                       id="stock"
                       name="stock"
                       value="{{ $item->stock }}">

                <div class="invalid-feedback">{{ $errors->first('stock') }}</div>
            </div>

            <div class="form-check form-group">
                <input type="checkbox"
                       class="form-check-input{{ $errors->has('is_tester') ? ' is-invalid' : '' }}"
                       id="is_tester"
                       name="is_tester"
                       {{ $item->is_tester ? 'checked' : '' }}>
                <label class="form-check-label" for="is_tester">Является тестером</label>

                <div class="invalid-feedback">{{ $errors->first('is_tester') }}</div>
            </div>

            <div class="form-group">
                <label for="tags">Тэги</label>
                <tags-input element-id="tags"
                    wrapper-class='form-control'
                    :value="[
                        @if (old('tags'))
                            @foreach (json_decode(old('tags')) as $tag)
                                { key: '{{ $tag->key }}', value: '{{ $tag->value }}' },
                            @endforeach
                        @else
                            @foreach ($itemTags as $tag)
                                { key: '{{ $tag->id }}', value: '{{ $tag->name }}' },
                            @endforeach
                        @endif
                    ]"
                    :existing-tags="[
                        @foreach ($allTags as $tag)
                            { key: '{{ $tag->id }}', value: '{{ $tag->name }}' },
                        @endforeach
                    ]"
                    :typeahead="true"></tags-input>
            </div>


            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary float-right">Изменить</button>
                </div>
            </div>
        </form>
    </div>
@endsection
