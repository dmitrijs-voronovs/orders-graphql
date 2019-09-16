<?php
/**
 * ScandiPWA_OrdersGraphQl
 *
 * @category    ScandiPWA
 * @package     ScandiPWA_OrdersGraphQl
 * @author      Yefim Butrameev <info@scandiweb.com>
 * @copyright   Copyright (c) 2019 Scandiweb, Ltd (https://scandiweb.com)
 */

declare(strict_types=1);
 
namespace ScandiPWA\OrdersGraphQl\Model\Resolver;
 
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Catalog\Model\ProductRepository;

/**
 * Retrieves the Product list in orders
 */
class ProductResolver implements ResolverInterface
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * Get All Product Items of Order.
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['products'])) {
            return null;
        }

        foreach ($value['products'] as $key => $item) {
            $product = $this->productRepository->get($item['sku']);

            $productData = $product->toArray();
            $productData['model'] = $product;

            $data[$key] = $productData;
            $data[$key]['qty'] = $item->getQtyOrdered();
            $data[$key]['row_total'] = $item->getBaseRowTotalInclTax();
            $data[$key]['original_price'] = $item->getBaseOriginalPrice();
            $data[$key]['license_key'] = $item['license_key'];
        }

        return $data;
    }
}
