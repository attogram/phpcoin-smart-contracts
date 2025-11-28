<?php

class AdvancedPoll extends SmartContractBase
{
    const SC_CLASS_NAME = "AdvancedPoll";
    const VOTE_FEE = 0.001; // Require a 0.001 PHPCoin fee to vote

    /** @SmartContractVar */
    public $owner; // The address of the contract owner

    /** @SmartContractVar */
    public $question; // The poll question

    /** @SmartContractVar */
    public $isOpen; // Flag to indicate if the poll is active

    /** @SmartContractMap */
    public SmartContractMap $options; // Allowed options for the poll

    /** @SmartContractMap */
    public SmartContractMap $votes; // Stores the vote count for each option

    /** @SmartContractMap */
    public SmartContractMap $voters; // Tracks who has already voted

    /**
     * @SmartContractDeploy
     * Deploys the contract, setting the question and initial options.
     * @param string $question The poll question.
     * @param array $options An array of strings representing the poll options.
     */
    public function deploy($question, $options)
    {
        $this->owner = $this->src;
        $this->question = $question;
        $this->isOpen = true;

        // Initialize options and vote counts
        foreach ($options as $option) {
            if (!empty($option)) {
                $this->options[$option] = true;
                $this->votes[$option] = 0;
            }
        }
    }

    /**
     * @SmartContractTransact
     * Allows any user to cast a vote, provided they pay the fee.
     * @param string $option The option to vote for.
     */
    public function vote($option)
    {
        if (!$this->isOpen) {
            $this->error("POLL_CLOSED");
        }
        if ($this->voters[$this->src] === true) {
            $this->error("ALREADY_VOTED");
        }
        if ($this->options[$option] !== true) {
            $this->error("INVALID_OPTION");
        }
        if ($this->value < self::VOTE_FEE) {
            $this->error("INSUFFICIENT_FEE");
        }

        $this->voters[$this->src] = true;
        $this->votes[$option] = (int)$this->votes[$option] + 1;
    }

    /**
     * @SmartContractTransact
     * Allows the owner to close the poll.
     */
    public function closePoll()
    {
        if ($this->src !== $this->owner) {
            $this->error("UNAUTHORIZED");
        }
        $this->isOpen = false;
    }

    /**
     * @SmartContractTransact
     * Allows the owner to withdraw the collected fees.
     */
    public function withdrawFees()
    {
        if ($this->src !== $this->owner) {
            $this->error("UNAUTHORIZED");
        }

        $balance = Account::getBalance($this->address);
        if ($balance > 0) {
            Transaction::send($this->owner, $balance);
        }
    }

    /**
     * @SmartContractView
     * Returns the current status of the poll.
     * @return array The poll question and its open status.
     */
    public function getStatus()
    {
        return [
            'question' => $this->question,
            'isOpen' => $this->isOpen
        ];
    }

    /**
     * @SmartContractView
     * Returns the results of the poll.
     * @return array A map of options to their vote counts.
     */
    public function getResults()
    {
        $results = [];
        $options = $this->options->keys();
        foreach ($options as $option) {
            $results[$option] = (int)$this->votes[$option];
        }
        return $results;
    }
}
