<?php

namespace Pimcore\Model\DataObject\ClassDefinition\Data\Relations;

use Pimcore\Model\DataObject;
use Pimcore\Model\Element\DirtyIndicatorInterface;

trait ManyToManyRelationTrait
{
    /**
     * TODO: move validation to checkValidity & throw exception in Pimcore 7
     *
     * @param DataObject\Concrete|DataObject\Localizedfield|DataObject\Objectbrick\Data\AbstractData|\Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData $container
     * @param array $params
     */
    public function save($container, $params = [])
    {
        if (!isset($params['forceSave']) || $params['forceSave'] !== true) {
            if (!DataObject\AbstractObject::isDirtyDetectionDisabled() && $container instanceof DirtyIndicatorInterface) {
                if ($container instanceof DataObject\Localizedfield) {
                    if ($container->getObject() instanceof DirtyIndicatorInterface) {
                        if (!$container->hasDirtyFields()) {
                            return;
                        }
                    }
                } else {
                    if ($this->supportsDirtyDetection()) {
                        if (!$container->isFieldDirty($this->getName())) {
                            return;
                        }
                    }
                }
            }
        }

        $data = $this->getDataFromObjectParam($container, $params);
        $this->filterMultipleAssignments($data, $container, $params);

        parent::save($container, $params);
    }

    /**
     * returns sql query statement to filter according to this data types value(s)
     *
     * @param  mixed $value
     * @param  string $operator
     * @param  mixed $params
     *
     * @return string
     *
     */
    public function getFilterCondition($value, $operator, $params = [])
    {
        $operator = 'LIKE';

        return $this->getFilterConditionExt(
            $value,
            $operator,
            $params
        );
    }
}
