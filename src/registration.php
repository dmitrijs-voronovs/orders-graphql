<?php
/**
 * ScandiPWA_OrdersGraphQl
 *
 * @category    ScandiPWA
 * @package     ScandiPWA_OrdersGraphQl
 * @author      Yefim Butrameev <info@scandiweb.com>
 * @copyright   Copyright (c) 2019 Scandiweb, Ltd (https://scandiweb.com)
 */

use \Magento\Framework\Component\ComponentRegistrar;

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'ScandiPWA_OrdersGraphQl',
    __DIR__
);