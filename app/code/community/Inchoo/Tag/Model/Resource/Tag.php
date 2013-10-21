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

class Inchoo_Tag_Model_Resource_Tag extends Mage_Tag_Model_Resource_Tag
{
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            return $this;
        }
        
        $select  = $this->_getReadAdapter()->select()
            ->from($this->getTable('inchoo_tag/tag_title'), array('store_id', 'value'))
            ->where('tag_id=:tag_id');

        if ($result = $this->_getReadAdapter()->fetchPairs($select, array(':tag_id'=>$object->getId()))) {
            $object->setTagCodes($result);
        }
        
        return $this;
    }
    
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $adapter  = $this->_getWriteAdapter();
        $tagId = (int) $object->getId();

        if ($object->hasTagCodes()) {
            $tagTitleTable = $this->getTable('inchoo_tag/tag_title');
            $adapter->beginTransaction();
            
            try {
                $select = $adapter->select()
                    ->from($tagTitleTable, array('store_id', 'value'))
                    ->where('tag_id = :tag_id');
                
                $old    = $adapter->fetchPairs($select, array(':tag_id'=>$tagId));
                $new    = array_filter(array_map('trim', $object->getTagCodes()));

                $insert = array_diff_assoc($new, $old);
                $delete = array_diff_assoc($old, $new);
                
                if (!empty($delete)) {
                    $where = array(
                        'tag_id = ?' => $tagId,
                        'store_id IN(?)' => array_keys($delete)
                    );
                    $adapter->delete($tagTitleTable, $where);
                }

                if ($insert) {
                    $data = array();
                    foreach ($insert as $storeId => $title) {
                        $data[] = array(
                            'tag_id' => $tagId,
                            'store_id'  => (int)$storeId,
                            'value'     => $title
                        );
                    }
                    if (!empty($data)) {
                        $adapter->insertMultiple($tagTitleTable, $data);
                    }
                }
                $adapter->commit();
            }
            catch (Exception $e) {
                Mage::logException($e);
                $adapter->rollBack();
            }
        }
            
        return parent::_afterSave($object);
    }
}