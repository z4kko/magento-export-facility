<?php

class Zeta_Exporter_Model_Newsletter_Synchronizer extends Mage_Core_Model_Abstract  {

    /** @var $_collection Mage_Sales_Model_Resource_Order_Collection */
    protected $_collection;
    protected $_counter = 0;

    /**
     * @return int
     */
    public function syncWithOrders() {

        $this->_prepareCollection()->_importCollection();

        return $this->_counter;

    }

    /**
     * @return $this
     */
    protected function _prepareCollection() {

        /** @var $_orderCollection Mage_Sales_Model_Resource_Order_Collection*/
        $_orderCollection = Mage::getResourceModel('sales/order_collection');

        $this->_collection = $_orderCollection;

        return $this;


    }

    /**
     * @param Mage_Newsletter_Model_Resource_Subscriber_Collection $collection
     * @return array
     */
    protected function _prepareFilter(Mage_Newsletter_Model_Resource_Subscriber_Collection $collection) {

        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns('subscriber_email');

        $_registeredCustomers = array();

        foreach($collection as $subscriber)  {

            array_push($_registeredCustomers, $subscriber->getSubscriberEmail());

        }

        return $_registeredCustomers;

    }

    protected function _importCollection() {

        /** @var $_subscriberCollection Mage_Newsletter_Model_Resource_Subscriber_Collection*/
        $_subscriberCollection = Mage::getResourceModel('newsletter/subscriber_collection');

        $_filter = $this->_prepareFilter($_subscriberCollection);

        /** @var $order Mage_Sales_Model_Order */
        foreach($this->_collection as $order)    {

            if(in_array($order->getCustomerEmail(), $_filter))  {

                continue;

            }

            $subscriber = Mage::getModel('newsletter/subscriber');

            $subscriber
                ->setStoreId(1)
                ->setCustomerId( ($order->getCustomerId() == '') ? 0 : $order->getCustomerId() )
                ->setSubscriberEmail($order->getCustomerEmail())
                ->setSubscriberConfirmCode($subscriber->randomSequence())
                ->setStatus($subscriber::STATUS_SUBSCRIBED);

            $subscriber->save();
            $this->_counter++;
            $subscriber->unsetData();

        }

    }

}