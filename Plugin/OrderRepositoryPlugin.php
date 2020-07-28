<?php
namespace Namluu\Sap\Plugin;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
/**
 * Class OrderRepositoryPlugin
 */
class OrderRepositoryPlugin
{
    /**
     * Order feedback field name
     */
    const FIELD_NAME = 'example_attribute';
    /**
     * Order Extension Attributes Factory
     *
     * @var OrderExtensionFactory
     */
    protected $extensionFactory;

    protected $orderRepository;

    protected $currentOrder;
    /**
     * OrderRepositoryPlugin constructor
     *
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(OrderExtensionFactory $extensionFactory, OrderRepositoryInterface $orderRepository)
    {
        $this->extensionFactory = $extensionFactory;
        $this->orderRepository = $orderRepository;
    }
    /**
     * Add extension attribute to order data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        $data = $order->getData(self::FIELD_NAME);
        $extensionAttributes = $order->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
        $extensionAttributes->setExampleAttribute($data);
        $order->setExtensionAttributes($extensionAttributes);
        return $order;
    }
    /**
     * Add extension attribute to order data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
        $orders = $searchResult->getItems();
        foreach ($orders as &$order) {
            $data = $order->getData(self::FIELD_NAME);
            $extensionAttributes = $order->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
            $extensionAttributes->setExampleAttribute($data);
            $order->setExtensionAttributes($extensionAttributes);
        }
        return $searchResult;
    }

    public function beforeSave(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $extensionAttributes = $order->getExtensionAttributes();
        if (null !== $extensionAttributes &&
            null !== $extensionAttributes->getExampleAttribute()
        ) {
            $data = $extensionAttributes->getExampleAttribute();
            $order->setData(self::FIELD_NAME, $data);
        }
        return [$order];
    }
}