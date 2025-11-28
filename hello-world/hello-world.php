<?php

class HelloWorld extends SmartContractBase
{
    const SC_CLASS_NAME = "HelloWorld";

    /**
     * @SmartContractDeploy
     * This method is called once when the contract is deployed.
     * For this simple example, it does nothing.
     */
    public function deploy()
    {
        // No initial state to set.
    }

    /**
     * @SmartContractView
     * Returns a friendly greeting.
     * @return string The greeting message.
     */
    public function greet()
    {
        return "Hello, World!";
    }
}
