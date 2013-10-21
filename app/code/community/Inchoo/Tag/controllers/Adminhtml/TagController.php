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

include_once 'Mage/Adminhtml/controllers/TagController.php';

class Inchoo_Tag_Adminhtml_TagController extends Mage_Adminhtml_TagController
{
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            if (isset($postData['tag_id'])) {
                $data['tag_id'] = $postData['tag_id'];
            }

            $data['name']               = trim($postData['tag_name']);
            $data['status']             = $postData['tag_status'];
            $data['base_popularity']    = (isset($postData['base_popularity'])) ? $postData['base_popularity'] : 0;
            $data['tag_codes']          = $postData['tag_codes'];
            $data['store']              = 0;
            
            if (!$model = $this->_initTag()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Wrong tag was specified.'));
                return $this->_redirect('*/*/index');
            }

            $model->addData($data);

            if (isset($postData['tag_assigned_products'])) {
                $productIds = Mage::helper('adminhtml/js')->decodeGridSerializedInput(
                    $postData['tag_assigned_products']
                );
                $tagRelationModel = Mage::getModel('tag/tag_relation');
                $tagRelationModel->addRelations($model, $productIds);
            }

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The tag has been saved.'));
                Mage::getSingleton('adminhtml/session')->setTagData(false);

                if (($continue = $this->getRequest()->getParam('continue'))) {
                    return $this->_redirect('*/tag/edit', array('tag_id' => $model->getId(), 'ret' => $continue));
                } else {
                    return $this->_redirect('*/tag/' . $this->getRequest()->getParam('ret', 'index'));
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setTagData($data);

                return $this->_redirect('*/*/edit', array('tag_id' => $model->getId()));
            }
        }

        return $this->_redirect('*/tag/index', array('_current' => true));
    }
}