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

class Inchoo_Tag_Block_Adminhtml_Tag_Tag_Grid extends Mage_Adminhtml_Block_Tag_Tag_Grid
{
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getIndex()) {
            return parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'        => Mage::helper('tag')->__('Tag'),
            'index'         => 'name',
        ));

        $this->addColumn('products', array(
            'header'        => Mage::helper('tag')->__('Products'),
            'width'         => 140,
            'align'         => 'right',
            'index'         => 'products',
            'type'          => 'number',
        ));

        $this->addColumn('status', array(
            'header'        => Mage::helper('tag')->__('Status'),
            'width'         => 90,
            'index'         => 'status',
            'type'          => 'options',
            'options'       => $this->helper('tag/data')->getStatusesArray(),
        ));

        $this->sortColumnsByOrder();
        return $this;
    }
}