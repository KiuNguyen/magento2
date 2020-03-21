<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Quote\Plugin;

use Magento\Catalog\Model\Product\Visibility;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * Check simple product invisibility status in quote
 */
class CheckInvisibilityProductCondition
{
    /**
     * @var Grouped
     */
    protected $groupType;

    /**
     * @param Grouped $groupType
     */
    public function __construct(
        Grouped $groupType
    ) {
        $this->groupType = $groupType;
    }

    /**
     * Throw a error message if simple product not visible individually
     *
     * @param AbstractItem $subject
     * @param AbstractItem $result
     * @return AbstractItem
     */
    public function afterCheckData(AbstractItem $subject, AbstractItem $result)
    {
        if ($subject->getProduct()->getVisibility() == Visibility::VISIBILITY_NOT_VISIBLE
            && !$subject->getParentItem()
            && count($this->groupType->getParentIdsByChild($subject->getProduct()->getId())) == 0
        ) {
            $result->setHasError(true)->setMessage(__('Product not available at the moment'));
            $result->getQuote()->setHasError(
                true
            )->addMessage(
                __('Some of the products not available at the moment.')
            );
        }

        return $result;
    }
}