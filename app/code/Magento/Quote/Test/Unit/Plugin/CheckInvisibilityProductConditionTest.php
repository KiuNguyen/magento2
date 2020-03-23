<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Plugin;

use Magento\Catalog\Model\Product\Visibility;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Quote\Plugin\CheckInvisibilityProductCondition;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Model\Quote;
use Magento\Catalog\Model\Product;

/**
 * Class CheckInvisibilityProductConditionTest
 */
class CheckInvisibilityProductConditionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\Phrase
     */
    protected $expectedProductErrorMessage;
    /**
     * @var \Magento\Framework\Phrase
     */
    protected $expectedQuoteErrorMessage;
    /**
     * @var boolean
     */
    protected $expectErrorStatus;
    /**
     * @var \Magento\Quote\Plugin\CheckInvisibilityProductCondition
     */
    protected $pluginClass;
    /**
     * @var Grouped|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupMock;
    /**
     * @var Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;
    /**
     * @var Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;
    /**
     * @var AbstractItem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $abstractItemMock;

    public function setUp()
    {
        $this->groupMock = $this->getMockBuilder(Grouped::class)
            ->disableOriginalConstructor()
            ->setMethods(['getParentIdsByChild'])
            ->getMock();
        $this->groupMock->expects($this->once())
            ->method('getParentIdsByChild')
            ->willReturn(array());

        $objectManager     = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->pluginClass = $objectManager->getObject(CheckInvisibilityProductCondition::class,
            [
                'groupType' => $this->groupMock
            ]
        );
    }

    /**
     * Test error message if product is not visible and don't have any parent product
     */
    public function testCheckDataHasProductInvisibility()
    {
        $this->expectedProductErrorMessage = __('Product not available at the moment');
        $this->expectErrorStatus           = true;
        $this->abstractItemMock            = $this->getMockForAbstractClass(
            AbstractItem::class,
            [],
            '',
            false,
            false,
            true,
            ['getProduct', 'getParentItem', 'getQuote', 'setQuote']
        );

        $this->quoteMock   = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->setMethods(['getVisibility', 'getId'])
            ->getMock();
        $this->productMock->expects($this->once())
            ->method('getVisibility')
            ->willReturn(Visibility::VISIBILITY_NOT_VISIBLE);
        $this->productMock->expects($this->once())
            ->method('getId')
            ->willReturn(9999999);
        $parentItem = null;
        $this->abstractItemMock->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($this->productMock));
        $this->abstractItemMock->expects($this->once())
            ->method('getParentItem')
            ->will($this->returnValue($parentItem));
        $this->abstractItemMock->expects($this->any())
            ->method('getQuote')
            ->willReturn($this->quoteMock);
        $this->abstractItemMock->expects($this->any())
            ->method('setQuote')
            ->willReturn($this->quoteMock);
        $result = $this->pluginClass->afterCheckData($this->abstractItemMock, $this->abstractItemMock);
        $this->assertEquals($this->expectedProductErrorMessage, $result->getMessage());
        $this->assertEquals($this->expectErrorStatus, $result->getHasError());
    }
}
