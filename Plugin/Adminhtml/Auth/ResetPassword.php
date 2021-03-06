<?php

namespace Genesisoft\Base\Plugin\Adminhtml\Auth;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Encryption\Helper\Security;
use Magento\Framework\Exception\LocalizedException;
use Magento\User\Model\UserFactory;

class ResetPassword extends \Magento\User\Controller\Adminhtml\Auth\ResetPassword
{
    /**
     * Display reset forgotten password form
     *
     * User is redirected on this action when he clicks on the corresponding link in password reset confirmation email
     *
     * @return void
     */
    public function aroundExecute(\Magento\User\Controller\Adminhtml\Auth\ResetPassword $subject, callable $proceed)
    {
        $passwordResetToken = (string)$this->_request->getQuery('token');
        $userId = (int)$this->_request->getQuery('id');
        try {
            $this->_validateResetPasswordLinkToken($userId, $passwordResetToken);

            $this->_view->loadLayout();

            $content = $this->_view->getLayout()->getBlock('resetforgottenpassword.content');
            if ($content) {
                $content->setData('user_id', $userId)->setData('reset_password_link_token', $passwordResetToken);
            }

            $this->_view->renderLayout();
        } catch (\Exception $exception) {
            $this->messageManager->addError(__('Your password reset link has expired.'));
            $this->_redirect('adminhtml/auth/forgotpassword', ['_nosecret' => true]);
            return;
        }
    }
}
