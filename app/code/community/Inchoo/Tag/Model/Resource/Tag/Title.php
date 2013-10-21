<?php

/**
 * Inchoo
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Please do not edit or add to this file if you wish to upgrade
 * Magento or this extension to newer versions in the future.
 * Inchoo developers (Inchooer's) give their best to conform to
 * "non-obtrusive, best Magento practices" style of coding.
 * However, Inchoo does not guarantee functional accuracy of
 * specific extension behavior. Additionally we take no responsibility
 * for any possible issue(s) resulting from extension usage.
 * We reserve the full right not to provide any kind of support for our free extensions.
 * Thank you for your understanding.
 *
 * @category    Inchoo
 * @package     Inchoo_Tag
 * @author      Damir Korpar <korpardamir@gmail.com>
 * @copyright   Copyright (c) Inchoo (http://inchoo.net/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Inchoo_Tag_Model_Resource_Tag_Title extends Mage_Core_Model_Resource_Db_Abstract
{
    protected $_isPkAutoIncrement = false;
            
	protected function _construct()
	{
		$this->_init('inchoo_tag/tag_title');
	}
    
    public function getStoreName($tagId, $storeId=null)
    {
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
        
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getMainTable())
            ->where('tag_id=?', (int) $tagId)
            ->where('store_id=?', (int) $storeId);

        return $this->_getReadAdapter()->fetchRow($select);
    }
}