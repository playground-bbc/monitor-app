<?php

namespace app\modules\monitor;

/**
 * monitor module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $name;
    public $controllerNamespace = 'app\modules\monitor\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        // custom initialization code goes here
        $this->name = 'monitor';
    }
}
