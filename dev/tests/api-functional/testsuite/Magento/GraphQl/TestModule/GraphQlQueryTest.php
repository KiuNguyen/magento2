<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GraphQl\TestModule;

use Magento\TestFramework\TestCase\GraphQlAbstract;

/**
 * Class GraphQlQueryTest
 */
class GraphQlQueryTest extends GraphQlAbstract
{
    public function testQueryTestModuleReturnsResults()
    {
        $id = 1;

        $query = <<<QUERY
{
    testItem(id: {$id})
    {
        item_id
        name
    }
}
QUERY;

        $reponse = $this->graphQlQuery($query);
        $this->assertArrayHasKey('testItem', $reponse);
        $testItem = $reponse['testItem'];
        $this->assertArrayHasKey('item_id', $testItem);
        $this->assertArrayHasKey('name', $testItem);
        $this->assertEquals(1, $testItem['item_id']);
        $this->assertEquals('itemName', $testItem['name']);
    }

    public function testQueryTestModuleExtensionAttribute()
    {
        $id = 2;

        $query = <<<QUERY
{
    testItem(id: {$id})
    {
        item_id
        name
        integer_list
    }
}
QUERY;

        $reponse = $this->graphQlQuery($query);
        $this->assertArrayHasKey('testItem', $reponse);
        $testItem = $reponse['testItem'];
        $this->assertArrayHasKey('integer_list', $testItem);
        $this->assertEquals([3, 4, 5], $testItem['integer_list']);
    }
}