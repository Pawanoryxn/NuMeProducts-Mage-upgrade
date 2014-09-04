<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced SEO Suite
 * @version   1.0.3
 * @revision  368
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */



class Mirasvit_SEO_Test_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    public function setUp() {
        parent::setUp();
        $this->parseHelper = Mage::helper('seoautolink');
    }

    public function testIsAlnum() {
      $this->assertequals(true, $this->parseHelper->is_alnum('3'));
      $this->assertequals(true, $this->parseHelper->is_alnum('f'));
      $this->assertequals(true, $this->parseHelper->is_alnum('Ñ'));
      $this->assertequals(true, $this->parseHelper->is_alnum('0'));
      $this->assertequals(false, $this->parseHelper->is_alnum(' '));
      $this->assertequals(false, $this->parseHelper->is_alnum('-'));
      $this->assertequals(8, $this->parseHelper->strlen('ÑÐ¿Ð¸Ð½Ð½Ð¸Ð½Ð³'));
      $this->assertequals(8, $this->parseHelper->strlen('spinning'));
      // $this->assertequals('ÐÑÑÑÑÑÑÐ¸Ð½Ð½Ð¸Ð½Ð³Ð¸ ÑÐ»ÑÑÑÐ°Ð»Ð°Ð¹Ñ', $this->parseHelper->substr_replace('ÐÑÑÑÐ¸Ðµ ÑÐ¿Ð¸Ð½Ð½Ð¸Ð½Ð³Ð¸ ÑÐ»ÑÑÑÐ°Ð»Ð°Ð¹Ñ', 'ÑÑÑÑ', 3, 8));
      $this->assertequals('ÐÑÑÑÐ¸Ðµ ÑÑÑÑÐ½Ð¸Ð½Ð³Ð¸ ÑÐ»ÑÑÑÐ°Ð»Ð°Ð¹Ñ', $this->parseHelper->str_replace('ÑÐ¿Ð¸Ð½', 'ÑÑÑÑ', 'ÐÑÑÑÐ¸Ðµ ÑÐ¿Ð¸Ð½Ð½Ð¸Ð½Ð³Ð¸ ÑÐ»ÑÑÑÐ°Ð»Ð°Ð¹Ñ'));
      $this->assertequals('Ð', $this->parseHelper->get_char('ÐÑÑÑÐ¸Ðµ', 0));
      $this->assertequals('Ñ', $this->parseHelper->get_char('ÐÑÑÑÐ¸Ðµ', 3));
      $this->assertequals(false, $this->parseHelper->get_char('ÐÑÑÑÐ¸Ðµ', 6));
      $this->assertequals(false, $this->parseHelper->get_char('Link2', -1));
    }

	/**
	* @dataProvider parseProvider
	*/
    public function testParse($text, $links, $expectedResult) {
    	$result = $this->parseHelper->_addLinks($text, $links);

        $this->assertequals($expectedResult, $result);
    }

    public function parseProvider()
    {

        $link1 = new Varien_Object(array(
            'keyword' => 'link1',
            'url' => 'http://link1.com',
            ));
        $link2 = new Varien_Object(array(
            'keyword' => 'link2',
            'url' => 'http://link2.com',
            ));
        $link3 = new Varien_Object(array(
            'keyword' => 'link2 link3',
            'url' => 'http://link3.com',
            ));
        $link4 = new Varien_Object(array(
            'keyword' => 'ÑÐ¿Ð¸Ð½Ð½Ð¸Ð½Ð³',
            'url' => 'http://spinning.com',
            ));
        $link5 = new Varien_Object(array(
            'keyword' => 'spinning',
            'url' => 'http://spinning.com',
            ));
        return array(
          array('link1 link2', array($link1, $link2, $link3), "<a href='http://link1.com' >link1</a> <a href='http://link2.com' >link2</a>"),
          array('link1 link2 link3', array($link1, $link2, $link3), "<a href='http://link1.com' >link1</a> <a href='http://link2.com' >link2</a> link3"),
          array("<a href='http://link1.com' >link1 aaaa</a>", array($link1, $link3, $link2), "<a href='http://link1.com' >link1 aaaa</a>"),
          array('link2 link3', array($link3, $link2), "<a href='http://link3.com' >link2 link3</a>"),
          array('Link2', array($link3, $link2), "<a href='http://link2.com' >Link2</a>"),
          array('Best spinnings ultra', array($link5), "Best spinnings ultra"),
          array('ÐÑÑÑÐ¸Ðµ ÑÐ¿Ð¸Ð½Ð½Ð¸Ð½Ð³Ð¸ ÑÐ»ÑÑÑÐ°Ð»Ð°Ð¹Ñ', array($link4), "ÐÑÑÑÐ¸Ðµ ÑÐ¿Ð¸Ð½Ð½Ð¸Ð½Ð³Ð¸ ÑÐ»ÑÑÑÐ°Ð»Ð°Ð¹Ñ"),

         );
    }

}
