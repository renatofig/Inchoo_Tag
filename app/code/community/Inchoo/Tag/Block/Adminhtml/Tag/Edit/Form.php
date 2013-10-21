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

class Inchoo_Tag_Block_Adminhtml_Tag_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_form');
        $this->setTitle(Mage::helper('tag')->__('Block Information'));
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('tag_tag');

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );
        $this->setForm($form);

        // Tag Titles
        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('tag')->__('General Information')));

        if ($model->getTagId()) {
            $fieldset->addField('tag_id', 'hidden', array(
                'name' => 'tag_id',
            ));
        }
        
        $fieldset->addField('form_key', 'hidden', array(
            'name'  => 'form_key',
            'value' => Mage::getSingleton('core/session')->getFormKey(),
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('tag')->__('Status'),
            'title' => Mage::helper('tag')->__('Status'),
            'name' => 'tag_status',
            'required' => true,
            'options' => array(
                Mage_Tag_Model_Tag::STATUS_DISABLED => Mage::helper('tag')->__('Disabled'),
                Mage_Tag_Model_Tag::STATUS_APPROVED => Mage::helper('tag')->__('Approved'),
            ),
            'after_element_html' => ' ' . Mage::helper('adminhtml')->__('[GLOBAL]').'<br><br>',
        ));

        $fieldset->addField('name', 'text', array(
            'name' => 'tag_name',
            'label' => Mage::helper('tag')->__('Default Value'),
            'title' => Mage::helper('tag')->__('Default Value'),
            'required' => true,
        ));
        
        foreach (Mage::getSingleton('adminhtml/system_store')->getStoreCollection() as $store) {
            $fieldset->addField('tag_code_' . $store->getId(), 'text', array(
                'label' => $store->getName(),
                'name' => 'tag_codes[' . $store->getId() . ']',
            ));
        }
        
        if (!$model->getId() && !Mage::getSingleton('adminhtml/session')->getTagData() ) {
            $model->setStatus(Mage_Tag_Model_Tag::STATUS_APPROVED);
        }

        if ( Mage::getSingleton('adminhtml/session')->getTagData() ) {
            $form->addValues(Mage::getSingleton('adminhtml/session')->getTagData());
            Mage::getSingleton('adminhtml/session')->setTagData(null);
        } else {
            $form->addValues($model->getData());
        }
        
        if ($model->hasTagCodes()) {
            $this->_setTagCodes($model->getTagCodes());
        }

        return parent::_prepareForm();
    }
    
    protected function _setTagCodes($tagCodes)
    {
        foreach($tagCodes as $store=>$value) {
            if ($element = $this->getForm()->getElement('tag_code_' . $store)) {
               $element->setValue($value);
            }
        }
    }
    
    protected function _toHtml()
    {
        return $this->_getWarningHtml() . parent::_toHtml();
    }

    protected function _getWarningHtml()
    {
        return '<div>
        <ul class="messages">
            <li class="notice-msg">
                <ul>
                    <li>'.$this->__('If you do not specify a tag title for a store, the default value will be used.').'</li>
                </ul>
            </li>
        </ul>
        </div>';
    }
}
