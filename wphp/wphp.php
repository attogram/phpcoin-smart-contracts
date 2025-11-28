<?php
require_once 'erc_20_token.php';

class WrappedPHP extends ERC20Token
{
    const SC_CLASS_NAME = "WrappedPHP";

    /**
     * @SmartContractDeploy
     * Deploys the Wrapped PHPCoin contract.
     */
    public function deploy()
    {
        parent::deploy("Wrapped PHPCoin", "WPHP", 8, 0);
    }

    /**
     * @SmartContractTransact
     * Wraps PHPCoin into WPHP tokens by sending PHPCoin to the contract.
     */
    public function wrap()
    {
        $amount = $this->value;
        if ($amount <= 0) {
            $this->error("AMOUNT_TOO_LOW");
        }

        $this->balances[$this->src] = bcadd($this->balances[$this->src], $this->amountToInt($amount));
        $this->totalSupply = bcadd($this->totalSupply, $this->amountToInt($amount));
    }

    /**
     * @SmartContractTransact
     * Unwraps WPHP tokens back into PHPCoin.
     * @param string $amount The amount of WPHP to unwrap.
     */
    public function unwrap($amount)
    {
        $value = $this->amountToInt($amount);
        if ($this->balances[$this->src] < $value) {
            $this->error("INSUFFICIENT_BALANCE");
        }

        $this->balances[$this->src] = bcsub($this->balances[$this->src], $value);
        $this->totalSupply = bcsub($this->totalSupply, $value);

        Transaction::send($this->src, $amount);
    }
}
