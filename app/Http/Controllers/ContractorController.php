<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractorRequest;
use App\PriceProcessingJobStatus;
use App\Item;
use App\Relation;
use App\Contractor;
use App\ContractorItem;
use Illuminate\Http\Request;
use App\Jobs\ParsePrice;

class ContractorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $apiLink = route('api.contractor.index');
        return view('contractor/index', compact( 'apiLink'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contractor/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContractorRequest $request)
    {
        $config = [
            'col_article' => $request->col_article,
            'col_name' => $request->col_name,
            'col_price' => $request->col_price,
        ];
        $contractor = Contractor::create([
            'name' => $request->name,
            'config' => $config,
        ]);

        $request->session()->flash('message', 'Новый поставщик успешно добавлен!');

        return redirect(route('contractor.show' , $contractor->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contractor  $contractor
     * @return \Illuminate\Http\Response
     */
    public function show(Contractor $contractor)
    {
        if ($contractor->job && !$contractor->job->hasError()) {
            return view('price_processing_placeholder', [
                'job' => $contractor->job,
                'owner' => $contractor->name,
            ]);
        } else {

            $apiLink = route('api.contractor-item.index', [$contractor->id]);

            $contractorItems = $contractor
                ->items()
                ->with('relatedItem')
                ->paginate(30);

            return view('contractor/show', compact('contractor', 'contractorItems', 'apiLink'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Contractor  $contractor
     * @return \Illuminate\Http\Response
     */
    public function edit(Contractor $contractor)
    {
        return view('contractor/edit', compact('contractor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Contractor  $contractor
     * @return \Illuminate\Http\Response
     */
    public function update(ContractorRequest $request, Contractor $contractor)
    {
        $contractor->name = $request->name;
        $config = [
            'col_article' => $request->col_article,
            'col_name' => $request->col_name,
            'col_price' => $request->col_price,
        ];
        $contractor->config = $config;
        $contractor->save();

        $request->session()->flash('message', 'Поставщик успешно обновлен!');

        return redirect(route('contractor.show' , $contractor->id));
    }

    public function showPriceUploadForm(Contractor $contractor)
    {
        return view('contractor/upload_form', compact('contractor'));
    }

    public function priceUpload(Request $request, Contractor $contractor)
    {
        if (!$request->hasFile('price')) {
            abort(404);
        }

        $price = $request->file('price');
        $tmpName   = time() . '.' . $price->getClientOriginalExtension();
        $price->move(storage_path('tmp'), $tmpName);

        ParsePrice::dispatch(\Auth::id(), $contractor->id, storage_path('tmp') . '/' . $tmpName)
            ->onQueue('pricelist_processing');

        PriceProcessingJobStatus::create([
            'contractor_id' => $contractor->id,
            'status_id' => 1,
            'message' => 'Прайс успешно загружен',
        ]);

        $request->session()->flash('message', 'Прайс поставщика успешно загружен!');

        return redirect(route('contractor.index'));
    }

    public function showReationForm(Contractor $contractor, ContractorItem $contractorItem)
    {
        $apiLink = route('api.item.unrelated', [$contractor->id]);

        return view('contractor/relation_form', compact('contractor', 'contractorItem', 'apiLink'));
    }

    public function updateRelation(Request $request, Contractor $contractor, ContractorItem $contractorItem)
    {
        $itemOrig = $contractorItem->relatedItem;
        if ($itemOrig) {
            if ($itemOrig->article === (int)$request->article) {
                $request->session()->flash('message', sprintf("Связь '%s' => '%s' для поставщика '%s' уже установлена!",
                    $itemOrig->article,
                    $contractorItem->name,
                    $contractor->name
                ));

                return redirect(route('contractor.show', $contractor->id));
            }
        }

        $item = Item::where(['article' => $request->article])->first();
        if (!$item) {
            $request->session()->flash('message', sprintf("Собственный товар с артикулом '%s' не найден!",
                $request->article
            ));

            return redirect(route('contractor.show', $contractor->id));
        }

        $relationAlreadyExists = Relation::where([
            'item_id' => $item->id,
            'contractor_id' => $contractor->id,
        ])->exists();

        if ($relationAlreadyExists) {
            $request->session()->flash('message', sprintf("Связь для товара с артикулом '%s' "
                    . "для поставщика '%s' уже существует!<br />"
                    . "Нельзя связать товар с одинм поставщиком дважды!",
                    $item->article,
                    $contractor->name
                )
            );

            return redirect(route('contractor.show', $contractor->id));
        }

        if ($itemOrig) {
            $relation = Relation::where([
                'item_id' => $itemOrig->id,
                'contractor_item_id' => $contractorItem->id,
            ])->first();

            $relation->item_id = $item->id;
            $relation->save();
        } else {
            Relation::create([
                'item_id' => $item->id,
                'contractor_id' => $contractor->id,
                'contractor_item_id' => $contractorItem->id,
            ]);
        }

        $request->session()->flash('message', 'Связь успешно обновлена!');

        return redirect(route('contractor.show', $contractor->id));
    }

    public function deletedItems(Contractor $contractor)
    {
        if ($contractor->job && !$contractor->job->hasError()) {
            return view('price_processing_placeholder', [
                'job' => $contractor->job,
                'owner' => $contractor->name,
            ]);
        } else {
            $apiLink = route('api.contractor-item.deleted_items', [$contractor->id]);

            return view('contractor/show_deleted', compact('contractor', 'apiLink'));
        }

    }

}
