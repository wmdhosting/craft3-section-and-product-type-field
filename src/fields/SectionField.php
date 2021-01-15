<?php

namespace wmd\sectionandproducttype\fields;

use Craft;
use yii\db\Schema;
use craft\base\Field;
use craft\helpers\Json;
use craft\base\ElementInterface;
use craft\base\PreviewableFieldInterface;


class SectionField extends Field implements PreviewableFieldInterface
{
    /**
     * @var bool Contains  values for select all sections.
     */
    public $selectAll = false;

    /**
     * @var bool Contains multi-select values for sections.
     */
    public $multiple = false;

    /**
     * @var array Sections that are allowed for selection in the field settings.
     */
    public $allowedSections = [];

    /**
     * @var array Sections that are allowed for selection in the field settings.
     */
    public $excludedSections = [];

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('section-and-product-type', 'Section');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [
            ['allowedSections'],
            'validateAllowedSections'
        ];
        return $rules;
    }

    /**
     * Checking for the existence of sections for selection.
     *
     * @param string $attribute Attribute validated.
     *
     * @return void
     */
    public function validateAllowedSections(string $attribute)
    {
        $sections = $this->getSections();

        foreach ($this->allowedSections as $section) {
            if (!isset($sections[$section])) {
                $this->addError($attribute, Craft::t('section-and-product-type', 'Invalid section selected.'));
            }
        }
    }

    /**
     * Return all sections.
     *
     * @return array
     */
    private function getSections()
    {
        $sections = [];
        $editableSections = Craft::$app->getSections()->getEditableSections();

        if (!empty($editableSections)) {
            foreach ($editableSections as $section) {
                $sections[$section->id] = Craft::t('site', $section->name);
            }
        }

        return $sections;
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
    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * Return sections without excluded sections
     *
     * @return array
     */
    public function getAllowedSections(): array
    {
        $sections = $this->getSections();
        $excludedSections = $this->excludedSections;

        if (!empty($excludedSections)  && !empty($this->selectAll) ) {
            $excludedSections = array_map(function($value) {
                return intval($value);
            }, $excludedSections);

            foreach ($excludedSections as $value) {
                unset($sections[$value]);
            }
        }

        return $sections;
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
            'section-and-product-type/_components/fields/section/_settings',
            [
                'field' => $this,
                'sections' => $this->getSections(),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        if (empty($this->allowedSections) && empty($this->selectAll)) return 'You have not selected any section for selection, select in the field settings.';

        $sections = $this->getSections();
        if (!empty($this->selectAll)) {
            $sections = $this->getAllowedSections();
        }

        $allowSections = array_flip($this->allowedSections);
        $allowSections[''] = true;
        if (!$this->multiple && !$this->required) {
            $sections = ['' => Craft::t('app', 'None')] + $sections;
        }
        $allowSections = array_intersect_key($sections, $allowSections);

        return Craft::$app->getView()->renderTemplate(
            'section-and-product-type/_components/fields/section/_input', [
                'field' => $this,
                'value' => $value,
                'sections' => $allowSections,
            ]
        );
    }
}
