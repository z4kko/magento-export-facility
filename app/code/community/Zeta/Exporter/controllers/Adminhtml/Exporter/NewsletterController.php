<?php

class Zeta_Exporter_Adminhtml_Exporter_NewsletterController extends Mage_Adminhtml_Controller_Action   {

    public function syncAction()    {

        try {

            $model = Mage::getModel('zeta_exporter/newsletter_synchronizer');

            $counter = $model->syncWithOrders();

            ($counter == 0)   ? $this->_getSession()->addSuccess($this->__('Subscriber lists is up-to-date.'))
                : $this->_getSession()->addSuccess($this->__('Subscriber list updated with %s emails', $counter));


        } catch(Mage_Core_Exception $e)    {

            $this->_getSession()->addError($e->getMessage());

        } catch(Exception $e)   {

            $this->_getSession()->addError($this->__('PHP Error occurred. See log for details.'));
            Mage::log($e->getMessage(), Zend_Log::ERR, 'Zeta_Exporter_Exception.log', true);

        }

        $this->_goBack();
        return;


    }

    /**
     * Return back to the referer url
     */
    protected function _goBack()   {

        $refererUrl = $this->_getRefererUrl();
        $this->_redirectUrl($refererUrl);

    }

}