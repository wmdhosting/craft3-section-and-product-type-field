<?php

namespace wmd\sectionandproducttype\fields;

use Craft;
use yii\db\Schema;
use craft\base\Field;
use craft\helpers\Json;
use craft\base\ElementInterface;
use craft\errors\InvalidFieldException;
use craft\base\PreviewableFieldInterface;
use craft\commerce\services\ProductTypes;


class ProductTypeField extends Field implements PreviewableFieldInterface
{
    /**
     * @var bool Contains  values for select all product types.
     */
    public $selectAll = false;

    /**
     * @var bool Contains multi-select values for product types.
     */
    public $multiple = false;

    /**
     * @var array Product types that are allowed for selection in the field settings.
     */
    public $allowProductTypes = [];

    /**
     * @var array Product types that are allowed for selection in the field settings.
     */
    public $excludedProductTypes = [];

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('section-and-product-type', 'Product Type');
    }

    /**
     * @inheritdoc
     */
    public static function hasContentColumn(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();

        $rules[] = [
            ['allowProductTypes'],
            'validateAllowProductTypes'
        ];

        return $rules;
    }

    public function validateAllowProductTypes(string $attribute)
    {
        $productTypes = $this->getProductTypes();

        foreach ($this->allowProductTypes as $productType) {
            if (!isset($productTypes[$productType])) {
                $this->addError($attribute, Craft::t('section-and-product-type', 'Invalid product type selected.'));
            }
        }
    }


    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * Returns the validation rules.
     *
     * @return array
     */
    public function getElementValidationRules(): array
    {
        return [
            ['validateProductType'],
        ];
    }

    /**
     * Product type validation.
     *
     * @param ElementInterface $element Validated element.
     *
     * @return void
     * @throws InvalidFieldException
     */
    public function validateProductType(ElementInterface $element)
    {
        $value = $element->getFieldValue($this->handle);

        if (!is_array($value)) {
            $value = [$value];
        }

        $productTypes = $this->getProductTypes();

        foreach ($value as $productType) {
            if (!isset($productTypes[$productType])) {
                $element->addError($this->handle, Craft::t('section-and-product-type', 'Invalid product type selected.'));
            }
        }
    }

    /**
     * Return sections without excluded sections
     *
     * @return array
     */
    public function getAllowedProductTypes(): array
    {
        $productTypes = $this->getProductTypes();
        $excludedProductTypes = $this->excludedProductTypes;

        if (!empty($excludedProductTypes) && !empty($this->selectAll)) {
            $excludedProductTypes = array_map(function($value) {
                return intval($value);
            }, $excludedProductTypes);

            foreach ($excludedProductTypes as $value) {
                unset($productTypes[$value]);
            }
        }

        return $productTypes;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if (is_string($value)) {
            $value = Json::decodeIfJson($value);
        }

        if (is_int($value) && $this->multiple) {
            $value = [$value];
        } else if (is_array($value) && !$this->multiple && count($value) == 1) {
            $value = intval($value[0]);
        }
        
        if (is_array($value)) {
            foreach ($value as $key => $id) {
                $value[$key] = intval($id);
            }
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        if (is_array($value)) {
            foreach ($value as $key => $id) {
                $value[$key] = intval($id);
            }
        }

        return Json::encode($value);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'section-and-product-type/_components/fields/producttype/_settings',
            [
                'field' => $this,
                'productTypes' => $this->getProductTypes(),
            ]
        );
    }

    /**
     * Return all product types.
     *
     * @return array
     */
    private function getProductTypes()
    {
        $allProductTypes = (new ProductTypes)->getAllProductTypes();

        $productTypes = [];
        foreach ($allProductTypes as $productType) {
            $productTypes[$productType->id] = Craft::t('site', $productType->name);
        }
        return $productTypes;
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        if (empty($this->allowProductTypes) && empty($this->selectAll)) return 'You have not selected any product types for selection, select in the field settings.';

        $productTypes = $this->getProductTypes();

        if (!empty($this->selectAll)) {
            $productTypes = $this->getAllowedProductTypes();
        }

        $allowProductTypes = array_flip($this->allowProductTypes);
        $allowProductTypes[''] = true;
        if (!$this->multiple && !$this->required) {
            $productTypes =  ['' => Craft::t('app', 'None')] + $productTypes;
        }
        $allowProductTypes = array_intersect_key($productTypes, $allowProductTypes);

        return Craft::$app->getView()->renderTemplate(
            'section-and-product-type/_components/fields/producttype/_input', [
                'field' => $this,
                'value' => $value,
                'productTypes' => $allowProductTypes,
            ]
        );
    }
}
