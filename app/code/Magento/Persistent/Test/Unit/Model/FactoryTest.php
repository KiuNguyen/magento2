<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Persistent\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Persistent\Model\Factory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class FactoryTest extends TestCase
{
    /**
     * @var ObjectManagerInterface|MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var Factory
     */
    protected $_factory;

    protected function setUp(): void
    {
        $helper = new ObjectManager($this);

        $this->_objectManagerMock = $this->createMock(ObjectManagerInterface::class);
        $this->_factory = $helper->getObject(
            Factory::class,
            ['objectManager' => $this->_objectManagerMock]
        );
    }

    public function testCreate()
    {
        $className = 'SomeModel';

        $classMock = $this->getMockBuilder('SomeModel')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $className,
            []
        )->will(
            $this->returnValue($classMock)
        );

        $this->assertEquals($classMock, $this->_factory->create($className));
    }

    public function testCreateWithArguments()
    {
        $className = 'SomeModel';
        $data = ['param1', 'param2'];

        $classMock = $this->createMock('SomeModel');
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $className,
            $data
        )->will(
            $this->returnValue($classMock)
        );

        $this->assertEquals($classMock, $this->_factory->create($className, $data));
    }
}
