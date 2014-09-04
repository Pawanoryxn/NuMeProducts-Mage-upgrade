<?php
/**
 * Couponcodeimport.php
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
 * @package    Couponcodeimport
 * @copyright  Copyright (c) 2003-2009 CommerceExtensions @ InterSEC Solutions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */ 

class CommerceExtensions_Shoppingcartrulesimportexport_Model_Convert_Adapter_Couponcodeimport
extends Mage_Catalog_Model_Convert_Adapter_Product
{
	
	/**
	* Save product (import)
	* 
	* @param array $importData 
	* @throws Mage_Core_Exception
	* @return bool 
	*/
	public function saveRow( array $importData )
	{
		
		// Status logging
		//Mage::log('Beginning Coupon Import', null, 'coupons.log');
        $prefix = Mage::getConfig()->getNode('global/resources/db/table_prefix');
		$w = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$conditions = array();
		$ctype = '';
		$cond = array();
		$act = array();
		
		if(isset($importData['conditions_aggregator']) && $importData['conditions_aggregator'] != "") {
			$final_conditions_aggregator = $importData['conditions_aggregator'];
		} else {
			$final_conditions_aggregator = "all";
		}
		if(isset($importData['actions_aggregator']) && $importData['actions_aggregator'] != "") {
			$final_actions_aggregator = $importData['actions_aggregator'];
		} else {
			$final_actions_aggregator = "any";
		}
		
		if(isset($importData['conditions'])) {
			if($importData['conditions'] != "") {
				$conditions = array();
				$conditionsforimport = explode('^',$importData['conditions']);
				foreach ($conditionsforimport as $conditionsvalue) {
					
					$conditionsvalues = explode('~',$conditionsvalue);
					$conditions[] = array(
					'type'=> 'salesrule/rule_condition_address',
					'attribute' => $conditionsvalues[0],
					'operator' => $conditionsvalues[1],
					'value' => $conditionsvalues[2],
					'is_value_processed' => ''
					);
				}
		    } else {
				$conditions = array();
			}
		}
		
		//conditions_serialized
		$cond = array (
		'type'=>'salesrule/rule_condition_combine', //rule_condition_product_found
		'attribute'=>'',
		'operator'=>'',
		'value' => 1,
		'is_value_processed'=>'',
		'aggregator'=> $final_conditions_aggregator,
		'conditions'=> $conditions
		);
		
		//actions_serialized
		if(isset($importData['actions'])) {
			if($importData['actions'] != "") {
				$actions_conditions = array();
				$actionsforimport = explode('^',$importData['actions']);
				foreach ($actionsforimport as $actionvalue) {
					
					$actionsvalues = explode('~',$actionvalue);
					$actions_conditions[] = array(
					'type'=> 'salesrule/rule_condition_product',
					//'quote_item_qty' => '',
					'attribute' => $actionsvalues[0],
					'operator' => $actionsvalues[1],
					'value' => $actionsvalues[2],
					'is_value_processed' => ''
					);
				}
		    } else {
				$actions_conditions = array();
			}
		}
		//actions_serialized
		$act = array (
		'type'=>'salesrule/rule_condition_product_combine',
		'attribute'=>'',
		'operator'=>'',
		'value' => 1,
		'is_value_processed'=>'',
		'aggregator'=> $final_actions_aggregator,
		'conditions'=> $actions_conditions
		);
		
		if($importData['from_date'] != "") {
			$finalfromdate = $importData['from_date'];
		} else {
			$finalfromdate = NULL;
		}
		if($importData['to_date'] != "") {
			$finaltodate = $importData['to_date'];
		} else {
			$finaltodate = NULL;
		}
		
		$insData = array(
		'name'=> $importData['name'],
		'description'=> $importData['description'],
		'from_date'=> $finalfromdate,
		'to_date'=> $finaltodate,
		'uses_per_customer'=> $importData['uses_per_customer'],
		'is_active'=> $importData['is_active'],
		'conditions_serialized'=> serialize($cond),
		'actions_serialized'=> serialize($act),
		'stop_rules_processing'=> $importData['stop_rules_processing'],
		'is_advanced'=> $importData['is_advanced'],
		'product_ids'=> $importData['product_ids'],
		'sort_order'=> $importData['sort_order'],
		'simple_action'=> $importData['simple_action'], //by_percent || cart_fixed
		'discount_amount'=> $importData['discount_amount'],
		'discount_qty'=> ((is_numeric($importData['discount_qty']) && $importData['discount_qty'] > 0) ? $importData['discount_qty'] : null),
		'discount_step'=> $importData['discount_step'],
		'simple_free_shipping'=> $importData['simple_free_shipping'], //0 1 or 2
		'apply_to_shipping'=> $importData['apply_to_shipping'],
		'times_used'=> $importData['times_used'],
		'is_rss'=> $importData['is_rss'],
		'coupon_type'=> $importData['coupon_type']
		);
		
		
		if(isset($importData['delete_rule'])) {
			if ( $importData['delete_rule'] == 'Delete' || $importData['delete_rule'] == 'delete' ) {
				$select_qry = $w->query("SELECT rule_id FROM ".$prefix."salesrule_coupon WHERE code='".$importData['code']."';");
				$row = $select_qry->fetch();
				$rule_id = $row['rule_id'];
				$rs2 = $w->query("DELETE FROM ".$prefix."salesrule where rule_id='".$rule_id."';");	
				return true;
			}
		}
		
		if($importData['coupon_type'] == "2") {
			//means we have a actual coupon code.. 
			$rs = $w->query("SELECT rule_id FROM ".$prefix."salesrule_coupon WHERE code='".$importData['code']."';");
		} else {
			//else we match by name of rule
			$rs = $w->query("SELECT rule_id FROM ".$prefix."salesrule where name='".$importData['name']."';");
		}
		$rows = $rs->fetchAll();
		if (count($rows)) {
			
			if($importData['label'] !="") {
				$w->update(''.$prefix.'salesrule_label', array(
				'label'=> $importData['label']), 'rule_id=' . $rows[0]['rule_id'] . ' and store_id=0');
			}
			$w->update(''.$prefix.'salesrule', $insData, 'rule_id=' . $rows[0]['rule_id']);
			$w->update(''.$prefix.'salesrule_coupon',  array(
			'code'=>$importData['code'],
			'usage_limit'=>$importData['usage_limit'],
			'usage_per_customer'=>$importData['uses_per_customer'],
			'times_used'=>$importData['times_used'],
			'expiration_date'=>$importData['expiration_date'],
			'is_primary'=>$importData['is_primary']
			), 'rule_id=' . $rows[0]['rule_id']);
			
			//adds coupon codes to website(s)	
			$rs2 = $w->query("DELETE FROM ".$prefix."salesrule_website WHERE rule_id='".$rows[0]['rule_id']."';");	
			$websiteidsforimport = explode(',',$importData['website_ids']);
			foreach ($websiteidsforimport as $websiteidvalue) {
				
				$w->insert(''.$prefix.'salesrule_website', array(
				'rule_id'=>$rows[0]['rule_id'],
				'website_id'=>$websiteidvalue
				));
			}
			
			//adds customer group(s) to coupon				
			$rs2 = $w->query("DELETE FROM ".$prefix."salesrule_customer_group WHERE rule_id='".$rows[0]['rule_id']."';");
			$customergroupidsforimport = explode(',',$importData['customer_group_ids']);
			foreach ($customergroupidsforimport as $customergroupid) {
				$w->insert(''.$prefix.'salesrule_customer_group', array(
				'rule_id'=>$rows[0]['rule_id'],
				'customer_group_id'=>$customergroupid
				));
			}
		} else {
			$w->insert(''.$prefix.'salesrule', $insData);
			$shoppingcartruleid = $w->lastInsertId();
			$w->insert(''.$prefix.'salesrule_coupon', array(
			'rule_id'=>$shoppingcartruleid,
			'code'=>$importData['code'],
			'usage_limit'=>$importData['usage_limit'],
			'usage_per_customer'=>$importData['uses_per_customer'],
			'times_used'=>$importData['times_used'],
			'expiration_date'=>$importData['expiration_date'],
			'is_primary'=>$importData['is_primary']
			));
			
			if($importData['label'] !="") {
				$w->insert(''.$prefix.'salesrule_label', array(
				'rule_id'=>$shoppingcartruleid,
				'store_id'=>0,
				'label'=>$importData['label']
				));
			}
			
			
			
			if($importData['website_ids'] !="") {
				//adds coupon codes to website(s)			
				$websiteidsforimport = explode(',',$importData['website_ids']);
				foreach ($websiteidsforimport as $websiteidvalue) {
					
					$w->insert(''.$prefix.'salesrule_website', array(
					'rule_id'=>$shoppingcartruleid,
					'website_id'=>$websiteidvalue
					));
				}
			}
			if($importData['customer_group_ids'] !="") {
				//adds customer group(s) to coupon			
				$customergroupidsforimport = explode(',',$importData['customer_group_ids']);
				foreach ($customergroupidsforimport as $customergroupid) {
					
					$w->insert(''.$prefix.'salesrule_customer_group', array(
					'rule_id'=>$shoppingcartruleid,
					'customer_group_id'=>$customergroupid
					));
				}
			}
		}
		return true;
	} 
	
}