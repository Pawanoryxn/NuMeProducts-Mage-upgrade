<?php
/**
 * @category   Oro
 * @package    Oro_SalesRule
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Oro Helper class
 */
class Oro_SalesRule_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * @param $rule
     */
    public function ruleHasDiscountCondition($rule)
    {
        $hasDiscountConditions = false;
        $conditions = $rule->getConditions()->asArray();
        if (!empty($conditions['conditions'])) {
            foreach ($conditions['conditions'] as $condition) {
                if ($condition['type'] == Oro_SalesRule_Model_Condition_Grandtotal::CONDITION_TYPE) {
                    $hasDiscountConditions = true;
                }
            }
        }

        return $hasDiscountConditions;
    }
}
