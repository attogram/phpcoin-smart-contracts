<?php

class DnsRegistrar extends SmartContractBase
{
    const SC_CLASS_NAME = "DnsRegistrar";
    const REGISTRATION_FEE = 1.0; // 1 PHPCoin to register a new name

    /**
     * @SmartContractMap
     * Maps a domain name to the address of its owner.
     * e.g., "my-site.phpc" -> "P..."
     */
    public SmartContractMap $owners;

    /**
     * @SmartContractMap
     * Maps a domain name to its corresponding IPFS content hash (CID).
     * e.g., "my-site.phpc" -> "QmXo..."
     */
    public SmartContractMap $records;

    /**
     * @SmartContractDeploy
     * This method is called once when the contract is deployed.
     * For this contract, it does nothing.
     */
    public function deploy()
    {
        // No initial state to set.
    }

    /**
     * @SmartContractTransact
     * Registers a new domain name, linking it to an IPFS hash.
     * @param string $name The domain name to register (e.g., "my-site.phpc").
     * @param string $ipfsHash The IPFS content hash (CID) for the website content.
     */
    public function register($name, $ipfsHash)
    {
        if ($this->value < self::REGISTRATION_FEE) {
            $this->error("INSUFFICIENT_FEE");
        }
        if (empty($name) || strlen($name) > 128) {
            $this->error("INVALID_NAME");
        }
        if ($this->owners[$name] !== null) {
            $this->error("NAME_ALREADY_REGISTERED");
        }

        $this->owners[$name] = $this->src;
        $this->records[$name] = $ipfsHash;
    }

    /**
     * @SmartContractTransact
     * Updates the IPFS hash for a domain name you own.
     * @param string $name The domain name to update.
     * @param string $newIpfsHash The new IPFS content hash.
     */
    public function update($name, $newIpfsHash)
    {
        if ($this->owners[$name] === null) {
            $this->error("NAME_NOT_FOUND");
        }
        if ($this->owners[$name] !== $this->src) {
            $this->error("UNAUTHORIZED");
        }

        $this->records[$name] = $newIpfsHash;
    }

    /**
     * @SmartContractTransact
     * Transfers ownership of a domain name to a new address.
     * @param string $name The domain name to transfer.
     * @param string $newOwner The address of the new owner.
     */
    public function transfer($name, $newOwner)
    {
        if ($this->owners[$name] === null) {
            $this->error("NAME_NOT_FOUND");
        }
        if ($this->owners[$name] !== $this->src) {
            $this->error("UNAUTHORIZED");
        }
        if (!Account::valid($newOwner)) {
            $this->error("INVALID_ADDRESS");
        }

        $this->owners[$name] = $newOwner;
    }

    /**
     * @SmartContractView
     * Resolves a domain name to its IPFS content hash.
     * @param string $name The domain name to resolve.
     * @return string|null The IPFS hash, or null if not found.
     */
    public function resolve($name)
    {
        return $this->records[$name];
    }

    /**
     * @SmartContractView
     * Gets the owner of a specific domain name.
     * @param string $name The domain name to check.
     * @return string|null The owner's address, or null if not found.
     */
    public function getOwner($name)
    {
        return $this->owners[$name];
    }
}
