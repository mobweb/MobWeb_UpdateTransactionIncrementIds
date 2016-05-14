<?php

class MobWeb_UpdateTransactionIncrementIds_Model_CronJob extends Mage_Core_Model_Abstract
{
    public function run()
    {
        Mage::helper('MobWeb_UpdateTransactionIncrementIds')->updateIncrementIds();
    }
}