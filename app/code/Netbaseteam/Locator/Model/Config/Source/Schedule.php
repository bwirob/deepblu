<?php

namespace Netbaseteam\Locator\Model\Config\Source;

class Schedule implements \Magento\Framework\Option\ArrayInterface
{
   
    
    public function toOptionArray()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scheduleModel = $objectManager->get('\Netbaseteam\Locator\Model\ScheduleFactory')->create()->getCollection();
        $scheduleModel->addFieldToFilter('status', array('eq'=>'1'));

        $data = array();
        array_push($data,['value'=>'','label'=>__('--Please Select Schedule--')]);
        foreach ($scheduleModel as $key => $schedule) {
            $arr = ['value'=>$schedule->getScheduleId(),'label'=>__($schedule->getScheduleName())];
            array_push($data,$arr);
        }
        
        
        
        return $data;
    }
}

