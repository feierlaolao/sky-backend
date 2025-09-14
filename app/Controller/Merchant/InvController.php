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
use App\Request\Merchant\GetPurchaseOrdersRequest;
use App\Request\Merchant\GetSkuPricesRequest;
use App\Request\Merchant\GetSkusRequest;
use App\Request\Merchant\InvBrandRequest;
use App\Request\Merchant\InvCategoryRequest;
use App\Request\Merchant\InvChannelRequest;
use App\Request\Merchant\InvPurchaseOrderItemRequest;
use App\Request\Merchant\InvPurchaseOrderRequest;
use App\Request\Merchant\ItemsRequest;
use App\Request\Merchant\SkuPricesRequest;
use App\Resource\BrandResource;
use App\Resource\CategoryResource;
use App\Resource\ChannelResource;
use App\Resource\ItemResource;
use App\Resource\ItemSkuPriceResource;
use App\Resource\PurchaseOrderResource;
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
        $data = CategoryResource::make($category)->toArray();
        return MyResponse::success($data)->toArray();
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
        $data = BrandResource::make($brand)->toArray();
        return MyResponse::success($data)->toArray();
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
        $data = ChannelResource::make($channel)->toArray();
        return MyResponse::success($data)->toArray();
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
        $item = $this->itemService->getItemByMerchantIdAndId($this->authManager->guard('merchant_jwt')->id(), $id);
        $data = ItemResource::make($item)->toArray();
        return MyResponse::success($data)->toArray();
    }

    #[PatchMapping('items/{id}')]
    public function updateItem(string $id, ItemsRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        $this->itemService->updateItem($id, $data);
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


    #[GetMapping('skus')]
    public function skus(GetSkusRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        $temp = $this->itemService->skuList($data);
        $res = ItemResource::collection($temp);
        return MyResponse::page($res, $temp->currentPage(), $temp->perPage(), $temp->total())->toArray();
    }

    #[GetMapping('sku-prices')]
    public function skuPrices(GetSkuPricesRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        $temp = $this->itemService->skuPriceList($data);
        $data = ItemSkuPriceResource::collection($temp);
        return MyResponse::page($data, $temp->currentPage(), $temp->perPage(), $temp->total())->toArray();
    }

    #[PostMapping('sku-prices')]
    public function addSkuPrice(SkuPricesRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        $this->itemService->addSkuPrice($data);
        return MyResponse::success()->toArray();
    }

    #[PatchMapping('sku-prices/{id}')]
    public function updateSkuPrice(string $id, SkuPricesRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        $this->itemService->updateSkuPrice($id, $data);
        return MyResponse::success()->toArray();
    }

    #[DeleteMapping('sku-prices/{id}')]
    public function deleteSkuPrice($id): array
    {
        $skuPrice = $this->itemService->getSkuPriceByMerchantIdAndId($this->authManager->guard('merchant_jwt')->id(), $id);
        if ($skuPrice == null) {
            throw new ServiceException('价格不存在');
        }
        $this->itemService->deleteSkuPrice($id);
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

    #[GetMapping('purchase-orders')]
    public function purchaseOrders(GetPurchaseOrdersRequest $request): array
    {
        $data = $request->validatedWithMerchant();
        $temp = $this->purchaseOrderService->purchaseOrderList($data);
        $data = PurchaseOrderResource::collection($temp)->toArray();
        return MyResponse::page($data, $temp->currentPage(), $temp->perPage(), $temp->total())->toArray();
    }

    #[GetMapping('purchase-orders/{id}')]
    public function purchaseOrder(string $id): array
    {
        $data = $this->purchaseOrderService->getPurchaseOrderByMerchantIdAndId($this->authManager->guard('merchant_jwt')->id(), $id);
        $data = PurchaseOrderResource::make($data)->toArray();
        return MyResponse::success($data)->toArray();
    }


    #[PatchMapping('purchase-orders/{id}')]
    public function updatePurchaseOrder(string $id, InvPurchaseOrderRequest $request): array
    {

        $data = $request->validatedWithMerchant();
        $this->purchaseOrderService->updatePurchaseOrder($id, $data);
        return MyResponse::success()->toArray();
    }

    #[DeleteMapping('purchase-orders/{id}')]
    public function deletePurchaseOrder($id): array
    {
        $order = $this->purchaseOrderService->getPurchaseOrderByMerchantIdAndId($this->authManager->guard('merchant_jwt')->id(), $id);
        if ($order == null) {
            throw new ServiceException('订单不存在');
        }
        $this->purchaseOrderService->deletePurchaseOrder($id);
        return MyResponse::success()->toArray();
    }

    #[PostMapping('purchase-orders/{order_id}/items')]
    public function addPurchaseOrderItems($order_id, InvPurchaseOrderItemRequest $request): array
    {
        $order = $this->purchaseOrderService->getPurchaseOrderByMerchantIdAndId($this->authManager->guard('merchant_jwt')->id(), $order_id);
        if ($order == null) {
            throw new ServiceException('订单不存在');
        }
        $this->purchaseOrderService->addPurchaseOrderItem($order_id, $request->validatedWithMerchant());
        return MyResponse::success()->toArray();
    }

    #[DeleteMapping('purchase-orders/{order_id}/items/{id}')]
    public function deletePurchaseOrderItems($order_id, $id): array
    {
        return MyResponse::success()->toArray();
    }

    #[PatchMapping('purchase-orders/{order_id}/items/{id}')]
    public function updatePurchaseOrderItems($order_id, $id)
    {

    }


    #[GetMapping('sale-orders')]
    public function saleOrders()
    {

    }

    #[GetMapping('sale-orders/{id}')]
    public function saleOrder(string $id): array
    {
        return MyResponse::success()->toArray();
    }

    #[PostMapping('sale-orders')]
    public function addSaleOrder()
    {

    }

    #[PatchMapping('sale-orders/{id}')]
    public function updateSaleOrder(string $id)
    {

    }

    #[DeleteMapping('sale-orders/{id}')]
    public function deleteSaleOrder($id): array
    {
        return MyResponse::success()->toArray();
    }

}