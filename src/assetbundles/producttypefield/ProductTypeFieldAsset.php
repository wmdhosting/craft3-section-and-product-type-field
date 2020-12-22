<?php

namespace wmd\sectionandproducttype\assetbundles\producttypefield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ProductTypeFieldAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@wmd/sectionandproducttype/assetbundles/producttypefield/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/ProductTypeField.js',
        ];

        parent::init();
    }
}
