<?php

/**
 * Ecomteck
 * Copyright (C) 2018 Ecomteck
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html
 *
 * @category Ecomteck
 * @package Ecomteck_ProductAttachment
 * @copyright Copyright (c) 2018 Ecomteck
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Ecomteck
 */

namespace Ecomteck\ProductAttachment\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

/**
 * Class Edit
 * @package Ecomteck\ProductAttachment\Controller\Adminhtml\Index
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;
    
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Ecomteck\ProductAttachment\Model\ProductAttachment
     */
    private $attachModel;

    /**
     * @var  \Magento\Backend\Model\Session
     */
    private $backSession;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Ecomteck\ProductAttachment\Model\ProductAttachment $attachModel
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Ecomteck\ProductAttachment\Model\ProductAttachment $attachModel
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->attachModel = $attachModel;
        $this->backSession = $context->getSession();
        parent::__construct($context);
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ecomteck_ProductAttachment::manage');
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Ecomteck_ProductAttachment::productattachment_manage'
        )->addBreadcrumb(
            __('Attachment'),
            __('Attachment')
        )->addBreadcrumb(
            __('Manage Attachment'),
            __('Manage Attachment')
        );
        return $resultPage;
    }

    /**
     * Edit CMS page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('productattach_id');
        $model = $this->attachModel;

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Attachment no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
            $this->coreRegistry->register('productattach_id', $model->getId());
        }

        // 3. Set entered data if was error when we do save
        $data = $this->backSession->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        $this->coreRegistry->register('productattach', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Attachment') : __('New Attachment'),
            $id ? __('Edit Attachment') : __('New Attachment')
        );
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getName() : __('New Attachment'));
        return $resultPage;
    }
}
