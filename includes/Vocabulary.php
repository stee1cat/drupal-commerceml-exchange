<?php
/**
 * Copyright (c) 2017 Gennadiy Khatuntsev <e.steelcat@gmail.com>
 */

namespace CommerceExchange;

/**
 * Class Vocabulary
 */
class Vocabulary {

    /**
     * @var integer
     */
    protected $vid;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $machineName;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var integer
     */
    protected $hierarchy;

    /**
     * @var string
     */
    protected $module;

    /**
     * @var integer
     */
    protected $weight;

    /**
     * @param \stdClass $data
     *
     * @return Vocabulary
     */
    public static function create(\stdClass $data) {
        $object = new self();

        $object->setId($data->vid)
            ->setName($data->name)
            ->setMachineName($data->machine_name)
            ->setDescription($data->description)
            ->setHierarchy($data->hierarchy)
            ->setModule($data->module)
            ->setWeight($data->weight);

        return $object;
    }

    /**
     * @return integer
     */
    public function getId() {
        return $this->vid;
    }

    /**
     * @param integer $vid
     *
     * @return Vocabulary
     */
    public function setId($vid) {
        $this->vid = $vid;

        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Vocabulary
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getMachineName() {
        return $this->machineName;
    }

    /**
     * @param string $machineName
     *
     * @return Vocabulary
     */
    public function setMachineName($machineName) {
        $this->machineName = $machineName;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Vocabulary
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * @return integer
     */
    public function getHierarchy() {
        return $this->hierarchy;
    }

    /**
     * @param integer $hierarchy
     *
     * @return Vocabulary
     */
    public function setHierarchy($hierarchy) {
        $this->hierarchy = $hierarchy;

        return $this;
    }

    /**
     * @return string
     */
    public function getModule() {
        return $this->module;
    }

    /**
     * @param string $module
     *
     * @return Vocabulary
     */
    public function setModule($module) {
        $this->module = $module;

        return $this;
    }

    /**
     * @return integer
     */
    public function getWeight() {
        return $this->weight;
    }

    /**
     * @param integer $weight
     *
     * @return Vocabulary
     */
    public function setWeight($weight) {
        $this->weight = $weight;

        return $this;
    }

}
