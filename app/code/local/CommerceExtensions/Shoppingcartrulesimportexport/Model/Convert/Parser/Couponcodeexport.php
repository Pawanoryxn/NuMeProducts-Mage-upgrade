<?php
/**
 * Couponcodeexport.php
 * CommerceExtensions @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commerceextensions.com/LICENSE-M1.txt
 *
 * @category   Couponcode
 * @package    Couponcodeexport
 * @copyright  Copyright (c) 2003-2010 CommerceExtensions @ InterSEC Solutions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */ 
 
class CommerceExtensions_Shoppingcartrulesimportexport_Model_Convert_Parser_Couponcodeexport extends Mage_Eav_Model_Convert_Parser_Abstract
{
	/**
     * @deprecated not used anymore
     */
    public function parse()
    {
			return $this;
		}
	 /**
     * Unparse (prepare data) loaded categories
     *
     * @return Mage_Catalog_Model_Convert_Parser_Couponcodeexport
     */
    public function unparse()
    {
				 $ByStoreID = $this->getVar('store');
				 $recordlimit = $this->getVar('recordlimit');
				 $resource = Mage::getSingleton('core/resource');
				 $prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix');
				 $read = $resource->getConnection('core_read');
				 $row = array();
				 $finalratingoptions="";
				
				 $select_qry = "SELECT ".$prefix."salesrule.rule_id, ".$prefix."salesrule.name, ".$prefix."salesrule.description, ".$prefix."salesrule.from_date, ".$prefix."salesrule.to_date, ".$prefix."salesrule.uses_per_customer, ".$prefix."salesrule.is_active, ".$prefix."salesrule.conditions_serialized, ".$prefix."salesrule.actions_serialized, ".$prefix."salesrule.stop_rules_processing, ".$prefix."salesrule.is_advanced, ".$prefix."salesrule.product_ids, ".$prefix."salesrule.sort_order, ".$prefix."salesrule.simple_action, ".$prefix."salesrule.discount_amount, ".$prefix."salesrule.discount_qty, ".$prefix."salesrule.discount_step, ".$prefix."salesrule.simple_free_shipping, ".$prefix."salesrule.apply_to_shipping, ".$prefix."salesrule.times_used, ".$prefix."salesrule.is_rss, ".$prefix."salesrule.coupon_type, ".$prefix."salesrule_coupon.code, ".$prefix."salesrule_coupon.usage_limit, ".$prefix."salesrule_coupon.expiration_date, ".$prefix."salesrule_coupon.is_primary, ".$prefix."salesrule_label.label FROM ".$prefix."salesrule LEFT JOIN ".$prefix."salesrule_coupon ON ".$prefix."salesrule_coupon.rule_id = ".$prefix."salesrule.rule_id LEFT JOIN ".$prefix."salesrule_label ON ".$prefix."salesrule_label.rule_id = ".$prefix."salesrule.rule_id GROUP BY ".$prefix."salesrule.rule_id";
				 
					$rows = $read->fetchAll($select_qry);
					foreach($rows as $data)
					 { 
					 
					 			$row["name"] = $data['name'];
					 			$row["label"] = $data['label'];
					 			$row["code"] = $data['code'];
								$finalconditionsvalues="";
								$conditions_unserialized = unserialize($data['conditions_serialized']);
								#print_r($conditions_unserialized);
					 			$row["conditions_aggregator"] = $conditions_unserialized["aggregator"];
								if(isset($conditions_unserialized["conditions"])){
									foreach($conditions_unserialized["conditions"] as $conditions_data)
									 { 
										$finalconditionsvalues .= $conditions_data['attribute'] . "~" . $conditions_data['operator'] . "~" . $conditions_data['value'] . "^";
									 }
									$okcleanedfinalvalues = substr_replace($finalconditionsvalues,"",-1);
									$row["conditions"] = $okcleanedfinalvalues;
								} else {
					 			    $row["conditions"] = '';
					 			}
								
								$finalactionsvalues="";
								$actions_unserialized = unserialize($data['actions_serialized']);
					 			$row["actions_aggregator"] = $actions_unserialized["aggregator"];
					 			if(isset($actions_unserialized["conditions"])){
									foreach($actions_unserialized["conditions"] as $actions_data)
									 { 
										$finalactionsvalues .= $actions_data['attribute'] . "~" . $actions_data['operator'] . "~" . $actions_data['value'] . "^";
									 }
									$okcleanedfinalvalues = substr_replace($finalactionsvalues,"",-1);
					 				$row["actions"] = $okcleanedfinalvalues;
								} else {
								    $row["actions"] = '';
								}
					 			$row["usage_limit"] = $data['usage_limit'];
					 			$row["expiration_date"] = $data['expiration_date'];
					 			$row["description"] = $data['description'];
					 			$row["from_date"] = $data['from_date'];
					 			$row["to_date"] = $data['to_date'];
					 			$row["uses_per_customer"] = $data['uses_per_customer'];
								
								$finalvaluesfromoptions="";
								$select_qry2 = "SELECT customer_group_id FROM ".$prefix."salesrule_customer_group WHERE rule_id = '".$data['rule_id']."'";
								$rows2 = $read->fetchAll($select_qry2);
								foreach($rows2 as $data2)
								 { 
					 				$finalvaluesfromoptions .= $data2['customer_group_id'] . ",";
								 }
								$okcleanedfinalvalues = substr_replace($finalvaluesfromoptions,"",-1);
								$row["customer_group_ids"] = $okcleanedfinalvalues;
								
					 			$row["is_active"] = $data['is_active'];
					 			$row["is_primary"] = $data['is_primary'];
					 			$row["stop_rules_processing"] = $data['stop_rules_processing'];
					 			$row["is_advanced"] = $data['is_advanced'];
					 			$row["product_ids"] = $data['product_ids'];
					 			$row["sort_order"] = $data['sort_order'];
					 			$row["simple_action"] = $data['simple_action'];
					 			$row["discount_amount"] = $data['discount_amount'];
					 			$row["discount_qty"] = $data['discount_qty'];
					 			$row["discount_step"] = $data['discount_step'];
					 			$row["simple_free_shipping"] = $data['simple_free_shipping'];
					 			$row["apply_to_shipping"] = $data['apply_to_shipping'];
					 			$row["times_used"] = $data['times_used'];
					 			$row["is_rss"] = $data['is_rss'];
					 			$row["coupon_type"] = $data['coupon_type'];
								
								$finalvaluesfromoptions="";
								$select_qry2 = "SELECT website_id FROM ".$prefix."salesrule_website WHERE rule_id = '".$data['rule_id']."'";
								$rows2 = $read->fetchAll($select_qry2);
								foreach($rows2 as $data2)
								 { 
					 				$finalvaluesfromoptions .= $data2['website_id'] . ",";
								 }
								$okcleanedwebsiteids = substr_replace($finalvaluesfromoptions,"",-1);
					 			$row["website_ids"] =$okcleanedwebsiteids;
								
					 			$batchExport = $this->getBatchExportModel()
												->setId(null)
												->setBatchId($this->getBatchModel()->getId())
												->setBatchData($row)
												->setStatus(1)
												->save();
					 }
					 
					 
        return $this;
		}
}

?>