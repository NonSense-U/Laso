<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFastSellingItemRequest;
use App\Http\Requests\UpdateFastSellingItemRequest;
use App\Models\FastSellingItem;
use App\Services\FastSellingItemsService;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;

class FastSellingItemController extends Controller
{
    use ApiResponse;

    private $fastSellingItemsService;

    public function __construct(FastSellingItemsService $fastSellingItemsService)
    {
        $this->fastSellingItemsService = $fastSellingItemsService;
    }

    public function index(Request $request)
    {
        $items = $this->fastSellingItemsService->getItems($request->user());
        return ApiResponse::success('ok', ['items' => $items]);
    }

    public function addItem(StoreFastSellingItemRequest $request)
    {
        $item = FastSellingItem::query()->where('name', '=', $request['name'])->first();
        if (isset($item)) {
            $item = $this->fastSellingItemsService->updateItem($request->validated(), $item);
        } else {
            $item = $this->fastSellingItemsService->addItem($request->validated(), $request->user());
        }
        return ApiResponse::success('Item has been added successfully', ['item' => $item], 201);
    }

    public function updateItem(UpdateFastSellingItemRequest $request, $item_id)
    {
        $item = FastSellingItem::findOrFail($item_id);
        $item = $this->fastSellingItemsService->updateItem($request->validated(), $item);
        return ApiResponse::success('Item updated successfully', ['item' => $item],200);
    }
}
