<?php

namespace wmd\sectionandproducttype\fields;

use Craft;
use yii\db\Schema;
use craft\base\Field;
use craft\helpers\Json;
use craft\base\ElementInterface;
use craft\base\PreviewableFieldInterface;


class TagGroupField extends Field implements PreviewableFieldInterface
{
    /**
     * @var bool Contains  values for select all groups.
     */
    public $selectAll = false;

    /**
     * @var bool Contains multi-select values for groups.
     */
    public $multiple = false;

    /**
     * @var array Sections that are allowed for selection in the field settings.
     */
    public $allowedGroups = [];

    /**
     * @var array Sections that are allowed for selection in the field settings.
     */
    public $excludedGroups = [];

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('section-and-product-type', 'Tag Group');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [
            ['allowedGroups'],
            'validateAllowedSections'
        ];
        return $rules;
    }

    /**
     * Checking for the existence of groupss for selection.
     *
     * @param string $attribute Attribute validated.
     *
     * @return void
     */
    public function validateAllowedSections(string $attribute)
    {
        $groups = $this->getGroups();

        foreach ($this->allowedGroups as $group) {
            if (!isset($groups[$group])) {
                $this->addError($attribute, Craft::t('section-and-product-type', 'Invalid groups selected.'));
            }
        }
    }

    /**
     * Return all groupss.
     *
     * @return array
     */
    private function getGroups()
    {
        $groups = [];

        $editableTagGroups = Craft::$app->getTags()->getAllTagGroups();

        if (!empty($editableTagGroups)) {
            foreach ($editableTagGroups as $group) {
                $groups[$group->id] = Craft::t('site', $group->name);
            }
        }

        return $groups;
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
     * Return groups without excluded groups
     *
     * @return array
     */
    public function getAllowedGroups(): array
    {
        $groups = $this->getGroups();
        $excludedGroups = $this->excludedGroups;

        if (!empty($excludedGroups)  && !empty($this->selectAll) ) {
            $excludedGroups = array_map(function($value) {
                return intval($value);
            }, $excludedGroups);

            foreach ($excludedGroups as $value) {
                unset($groups[$value]);
            }
        }

        return $groups;
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
            'section-and-product-type/_components/fields/taggroup/_settings',
            [
                'field' => $this,
                'groups' => $this->getGroups(),
                'selectAll' => $this->selectAll,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        if (empty($this->allowedGroups) && empty($this->selectAll)) return 'You have not selected any groups for selection, select in the field settings.';

        $groups = $this->getGroups();
        $allowGroupsConfig = $this->allowedGroups;

        if ($this->selectAll) {
            if (is_array($this->excludedGroups)) {
                foreach ($this->excludedGroups as $groupsId) {
                    unset($groups[$groupsId]);
                }
            }
            $allowGroupsConfig = array_keys($groups);
        }

        $allowGroups = array_flip($allowGroupsConfig);
        $allowGroups[''] = true;
        if (!$this->multiple && !$this->required) {
            $groups = ['' => Craft::t('app', 'None')] + $groups;
        }
        $allowGroups = array_intersect_key($groups, $allowGroups);

        return Craft::$app->getView()->renderTemplate(
            'section-and-product-type/_components/fields/taggroup/_input', [
                'field' => $this,
                'value' => $value,
                'groups' => $allowGroups,
            ]
        );
    }
}
