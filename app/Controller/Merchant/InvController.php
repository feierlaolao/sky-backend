<?php

namespace App\Controller\Merchant;

use App\Controller\AbstractController;
use App\Exception\ServiceException;
use App\Middleware\MerchantAuthMiddleware;
use App\MyResponse;
use App\Request\Merchant\GetBrandsRequest;
use App\Request\Merchant\GetCategoriesRequest;
use App\Request\Merchant\GetChannelsRequest;
use App\Request\Merchant\GetItemsRequest;
use App\Request\Merchant\InvBrandRequest;
use App\Request\Merchant\InvCategoryRequest;
use App\Request\Merchant\InvChannelRequest;
use App\Request\Merchant\InvPurchaseOrderRequest;
use App\Request\Merchant\ItemsRequest;
use App\Resource\BrandResource;
use App\Resource\CategoryResource;
use App\Resource\ChannelResource;
use App\Resource\ItemResource;
use App\Service\Inv\BrandService;
use App\Service\Inv\CategoryService;
use App\Service\Inv\ChannelService;
use App\Service\Inv\ItemService;
use App\Service\Inv\PurchaseOrderService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PatchMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Qbhy\HyperfAuth\AuthManager;

#[Controller('merchant/inv')]
#[Middleware(MerchantAuthMiddleware::class)]
class InvController extends AbstractController
{

    #[Inject]
    protected CategoryService $categoryService;

    #[Inject]
    protected BrandService $brandService;

    #[Inject]
    protected ChannelService $channelService;

    #[Inject]
    protected AuthManager $authManager;

    #[Inject]
    protected ItemService $itemService;

    #[Inject]
    protected PurchaseOrderService $purchaseOrderService;

    #[GetMapping('categories')]
    public function getCategories(GetCategoriesRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        $res = $this->categoryService->getCategoryList($data);
        $temp = CategoryResource::collection($res);
        return MyResponse::page($temp, $res->currentPage(), $res->perPage(), $res->total())->toArray();
    }

    #[PostMapping('categories')]
    public function addCategory(InvCategoryRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        $this->categoryService->addCategory($data);
        return MyResponse::success()->toArray();
    }

    #[GetMapping('categories/{id}')]
    public function getCategory($id): array
    {
        $category = $this->categoryService->getCategoryByMerchantIdAndId($this->authManager->guard('merchant_jwt')->id(), $id);
        return MyResponse::success($category)->toArray();
    }

    #[PatchMapping('categories/{id}')]
    public function editCategory($id, InvCategoryRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        if ($this->categoryService->getCategoryByMerchantIdAndId($this->authManager->guard('merchant_jwt')->id(), $id) == null) {
            throw new ServiceException('分类不存在');
        }
        $this->categoryService->updateCategory($id, $data);
        return MyResponse::success()->toArray();
    }

    #[DeleteMapping('categories/{id}')]
    public function deleteCategory($id): array
    {
        if (!$this->categoryService->getCategoryByMerchantIdAndId($this->authManager->guard('merchant_jwt')->id(), $id)) {
            throw new ServiceException('分类不存在');
        }
        $this->categoryService->deleteCategory($id);
        return MyResponse::success()->toArray();
    }


    #[GetMapping('brands')]
    public function getBrands(GetBrandsRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        $res = $this->brandService->getBrandList($data);
        $temp = BrandResource::collection($res);
        return MyResponse::page($temp, $res->currentPage(), $res->perPage(), $res->total())->toArray();
    }


    #[PostMapping('brands')]
    public function addBrand(InvBrandRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        $this->brandService->addBrand($data);
        return MyResponse::success()->toArray();
    }

    #[GetMapping('brands/{id}')]
    public function getBrand($id): array
    {
        $brand = $this->brandService->getBrandByMerchantIdAndId($this->authManager->guard('merchant_jwt')->id(), $id);
        return MyResponse::success($brand)->toArray();
    }

    #[PatchMapping('brands/{id}')]
    public function editBrand($id, InvCategoryRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        if ($this->brandService->getBrandByMerchantIdAndId($this->authManager->guard('merchant_jwt')->id(), $id) == null) {
            throw new ServiceException('分类不存在');
        }
        $this->brandService->updateBrand($id, $data);
        return MyResponse::success()->toArray();
    }

    #[DeleteMapping('brands/{id}')]
    public function deleteBrand($id): array
    {
        if (!$this->brandService->getBrandByMerchantIdAndId($this->authManager->guard('merchant_jwt')->id(), $id)) {
            throw new ServiceException('分类不存在');
        }
        $this->brandService->deleteBrand($id);
        return MyResponse::success()->toArray();
    }


    #[GetMapping('channels')]
    public function getChannels(GetChannelsRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        $res = $this->channelService->getChannelList($data);
        $temp = ChannelResource::collection($res);
        return MyResponse::page($temp, $res->currentPage(), $res->perPage(), $res->total())->toArray();
    }


    #[PostMapping('channels')]
    public function addChannel(InvChannelRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        $this->channelService->addChannel($data);
        return MyResponse::success()->toArray();
    }

    #[GetMapping('channels/{id}')]
    public function getChannel($id): array
    {
        $channel = $this->channelService->getChannelByMerchantIdAndId($this->authManager->guard('merchant_jwt')->id(), $id);
        return MyResponse::success($channel)->toArray();
    }

    #[PatchMapping('channels/{id}')]
    public function editChannel($id, InvChannelRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        if ($this->channelService->getChannelByMerchantIdAndId($this->authManager->guard('merchant_jwt')->id(), $id) == null) {
            throw new ServiceException('分类不存在');
        }
        $this->channelService->updateChannel($id, $data);
        return MyResponse::success()->toArray();
    }

    #[DeleteMapping('channels/{id}')]
    public function deleteChannels($id): array
    {
        if (!$this->channelService->getChannelByMerchantIdAndId($this->authManager->guard('merchant_jwt')->id(), $id)) {
            throw new ServiceException('分类不存在');
        }
        $this->channelService->deleteChannel($id);
        return MyResponse::success()->toArray();
    }


    #[GetMapping('items')]
    public function getItems(GetItemsRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        $res = $this->itemService->items($data);
        $items = ItemResource::collection($res)->toArray();
        return MyResponse::page($items, $res->currentPage(), $res->perPage(), $res->total())->toArray();
    }

    #[GetMapping('items/{id}')]
    public function getItem($id): array
    {
        $data = $this->itemService->getItemByMerchantIdAndId($this->authManager->guard('merchant_jwt')->id(), $id);
        return MyResponse::success($data)->toArray();
    }

    #[PatchMapping('items/{id}')]
    public function updateItem(string $id)
    {
//        $this->itemService
        return MyResponse::success()->toArray();
    }

    #[PostMapping('items')]
    public function addItem(ItemsRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        $this->itemService->addItem($data);
        return MyResponse::success()->toArray();
    }

    #[DeleteMapping('items/{id}')]
    public function deleteItem($id): array
    {
        return MyResponse::success()->toArray();
    }


    #[GetMapping('sku/{barcode}')]
    public function getSkuBarcode(string $barcode): array
    {
        $res = $this->itemService->getSkuByBarcode($barcode);
        return MyResponse::success($res)->toArray();
    }


    #[PostMapping('purchase-orders')]
    public function addPurchaseOrder(InvPurchaseOrderRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        $this->purchaseOrderService->addPurchaseOrder($data);
        return MyResponse::success()->toArray();
    }

}