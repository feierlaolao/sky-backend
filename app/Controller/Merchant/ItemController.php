<?php

namespace App\Controller\Merchant;

use App\Controller\AbstractController;
use App\MyResponse;
use App\Request\ItemsRequest;
use App\Service\ItemService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller('merchant')]
class ItemController extends AbstractController
{

    #[Inject]
    protected ItemService $itemService;

    #[GetMapping('items')]
    public function index(ItemsRequest $itemsRequest): array
    {
        $data = $itemsRequest->validated();
        return MyResponse::formPaginator($this->itemService->items($data))->toArray();
    }

    #[GetMapping('items/{id}')]
    public function item($id): array
    {
        $data = $this->itemService->item($id);
        return MyResponse::success($data)->toArray();
    }

}