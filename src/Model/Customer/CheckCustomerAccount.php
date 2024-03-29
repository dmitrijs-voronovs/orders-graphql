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

namespace ScandiPWA\OrdersGraphQl\Model\Customer;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthenticationException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;

/**
 * Check customer account
 */
class CheckCustomerAccount
{
    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @param AuthenticationInterface $authentication
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     */
    public function __construct(
        AuthenticationInterface $authentication,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement
    ) {
        $this->authentication = $authentication;
        $this->customerRepository = $customerRepository;
        $this->accountManagement = $accountManagement;
    }

    /**
     * Check customer account
     *
     * @param int|null $customerId
     * @param int|null $customerType
     * @return void
     * @throws GraphQlAuthorizationException
     * @throws GraphQlNoSuchEntityException
     * @throws GraphQlAuthenticationException
     */
    public function execute(?int $customerId, ?int $customerType): void
    {
        if ($this->isCustomerGuest($customerId, $customerType)) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        try {
            $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(
                __('Customer with id "%customer_id" does not exist.', ['customer_id' => $customerId]),
                $e
            );
        }

        if ($this->authentication->isLocked($customerId)) {
            throw new GraphQlAuthenticationException(__('The account is locked.'));
        }

        $confirmationStatus = $this->accountManagement->getConfirmationStatus($customerId);
        if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
            throw new GraphQlAuthenticationException(__("This account isn't confirmed. Verify and try again."));
        }
    }

    /**
     * Checking if current customer is guest
     *
     * @param int|null $customerId
     * @param int|null $customerType
     * @return bool
     */
    private function isCustomerGuest(?int $customerId, ?int $customerType): bool
    {
        if (is_null($customerId) || is_null($customerType)) {
            return true;
        }
        return (int)$customerId === 0 || (int)$customerType === UserContextInterface::USER_TYPE_GUEST;
    }
}
