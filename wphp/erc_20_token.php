<?php

class ERC20Token extends SmartContractBase
{
    const SC_CLASS_NAME = "ERC20Token";

    /** @SmartContractVar */
    public $name;

    /** @SmartContractVar */
    public $symbol;

    /** @SmartContractVar */
    public $decimals;

    /** @SmartContractVar */
    public $totalSupply;

    /** @SmartContractMap */
    public SmartContractMap $balances;

    /** @SmartContractMap */
    public SmartContractMap $allowances;

    public function deploy($name, $symbol, $decimals, $initialSupply)
    {
        $this->name = $name;
        $this->symbol = $symbol;
        $this->decimals = $decimals;
        $this->totalSupply = $this->amountToInt($initialSupply);
        $this->balances[$this->src] = $this->totalSupply;
    }

    /**
     * @SmartContractView
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @SmartContractView
     */
    public function symbol()
    {
        return $this->symbol;
    }

    /**
     * @SmartContractView
     */
    public function decimals()
    {
        return $this->decimals;
    }

    /**
     * @SmartContractView
     */
    public function totalSupply()
    {
        return $this->intToAmount($this->totalSupply);
    }

    /**
     * @SmartContractView
     */
    public function balanceOf($owner)
    {
        return $this->intToAmount($this->balances[$owner]);
    }

    /**
     * @SmartContractTransact
     */
    public function transfer($to, $amount)
    {
        $value = $this->amountToInt($amount);
        if ($this->balances[$this->src] < $value) {
            $this->error("INSUFFICIENT_BALANCE");
        }
        $this->balances[$this->src] = bcsub($this->balances[$this->src], $value);
        $this->balances[$to] = bcadd($this->balances[$to], $value);
        return true;
    }

    /**
     * @SmartContractTransact
     */
    public function approve($spender, $amount)
    {
        $value = $this->amountToInt($amount);
        $this->allowances[$this->src][$spender] = $value;
        return true;
    }

    /**
     * @SmartContractView
     */
    public function allowance($owner, $spender)
    {
        return $this->intToAmount($this->allowances[$owner][$spender]);
    }

    /**
     * @SmartContractTransact
     */
    public function transferFrom($from, $to, $amount)
    {
        $value = $this->amountToInt($amount);
        if ($this->allowances[$from][$this->src] < $value) {
            $this->error("INSUFFICIENT_ALLOWANCE");
        }
        if ($this->balances[$from] < $value) {
            $this->error("INSUFFICIENT_BALANCE");
        }
        $this->balances[$from] = bcsub($this->balances[$from], $value);
        $this->balances[$to] = bcadd($this->balances[$to], $value);
        $this->allowances[$from][$this->src] = bcsub($this->allowances[$from][$this->src], $value);
        return true;
    }

    public function amountToInt($amount)
    {
        return bcmul($amount, bcpow("10", $this->decimals));
    }

    public function intToAmount($amount)
    {
        return bcdiv($amount, bcpow("10", $this->decimals), $this->decimals);
    }
}
