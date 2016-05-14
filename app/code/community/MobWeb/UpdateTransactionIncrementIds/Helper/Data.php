<?php

class MobWeb_UpdateTransactionIncrementIds_Helper_Data extends Mage_Core_Helper_Data
{
    public function log($msg)
    {
        Mage::log($msg, NULL, 'MobWeb_UpdateTransactionIncrementIds.log');
    }

    public function updateIncrementIds()
    {
        // Load the resource singleton and get the write connection
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');

        // Get the table names
        $eavEntityStoreTable = $resource->getTableName('eav_entity_store');
        $eavEntityTypeTable = $resource->getTableName('eav_entity_type');

        // Prepare the first part of the prefixes for each entity type
        $prefixes = array(
            'order' => 'B',
            'invoice' => '',
            'shipment' => 'L'
        );

        // Loop through the stores
        foreach(Mage::app()->getStores() AS $store) {

            // Loop through the entities and update their increment IDs
            foreach($prefixes AS $entityTypeCode => $prefix) {

                // For the prefix, use the identifying letter for each entity type and the current year and month
                $prefix .= date('Ym');

                // Start the numbering at 1001
                $lastId = '1000';

                // Get the entity ID for the current entity type
                if(($entity = Mage::getModel('eav/config')->getEntityType($entityTypeCode)) && ($entityTypeId = $entity->getEntityTypeId())) {

                    // Load the entity store
                    $entityStore = Mage::getModel('eav/entity_store')->loadByEntityStore($entityTypeId, $store->getId());

                    // Update the entity increment ID prefix and last ID
                    $entityStore->addData(array(
                        'increment_prefix' => $prefix,
                        'increment_last_id' => $lastId
                    ))->save();

                    // And disable the padding for the increment ID
                    if($entityType = Mage::getModel('eav/entity_type')->loadByCode($entityTypeCode)) {
                        $entityType->setIncrementPadLength(1)->save(); // 1 means that the increment ID has a minimum length of 1 digit
                    }

                    // Log the updated increment ID
                    self::log(sprintf('Increment ID for %s entity type set to: %s', $entityTypeCode, $prefix . $lastId));
                }
            }
        }
    }
}