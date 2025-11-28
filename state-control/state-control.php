<?php

class StateControl extends SmartContractBase
{
    const SC_CLASS_NAME = "StateControl";

    /**
     * @SmartContractVar
     * A single, contract-wide message.
     */
    public $message;

    /**
     * @SmartContractMap
     * A key-value store for individual user records.
     */
    public SmartContractMap $records;

    /**
     * @SmartContractDeploy
     * Sets the initial message when the contract is deployed.
     * @param string $initialMessage The first message.
     */
    public function deploy($initialMessage)
    {
        $this->message = $initialMessage;
    }

    /**
     * @SmartContractTransact
     * Updates the contract's main message.
     * @param string $newMessage The new message.
     */
    public function setMessage($newMessage)
    {
        $this->message = $newMessage;
    }

    /**
     * @SmartContractTransact
     * Allows any user to store a personal record.
     * @param string $key The key for the record.
     * @param string $value The value to store.
     */
    public function setRecord($key, $value)
    {
        $this->records[$key] = $value;
    }

    /**
     * @SmartContractView
     * Returns the main message.
     * @return string The current message.
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @SmartContractView
     * Retrieves a specific record by its key.
     * @param string $key The key of the record.
     * @return string|null The record's value, or null if not found.
     */
    public function getRecord($key)
    {
        return $this->records[$key];
    }
}
