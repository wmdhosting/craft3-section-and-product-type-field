<?php

namespace wmd\sectionandproducttype\assetbundles\sectionfield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class SectionFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@wmd/sectionandproducttype/assetbundles/sectionfield/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/SectionField.js',
        ];

        parent::init();
    }
}
