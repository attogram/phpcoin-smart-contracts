<?php

class DidRegistry extends SmartContractBase
{
    const SC_CLASS_NAME = "DidRegistry";

    /**
     * @SmartContractMap
     * Maps a user's PHPCoin address to the IPFS hash of their DID Document.
     * A DID Document is a JSON file containing public keys and service endpoints.
     * e.g., "P..." -> "QmYq..."
     */
    public SmartContractMap $records;

    /**
     * @SmartContractDeploy
     * This method is called once when the contract is deployed.
     * For this registry, it does nothing.
     */
    public function deploy()
    {
        // No initial state to set.
    }

    /**
     * @SmartContractTransact
     * Creates or updates the DID record for the sender's address.
     * This links your address to an IPFS file containing your public identity info.
     * @param string $ipfsHash The IPFS content hash (CID) for your DID Document.
     */
    public function setRecord($ipfsHash)
    {
        if (empty($ipfsHash)) {
            $this->error("IPFS_HASH_CANNOT_BE_EMPTY");
        }

        // Using the sender's address as the unique identifier.
        $this->records[$this->src] = $ipfsHash;
    }

    /**
     * @SmartContractTransact
     * Revokes the DID record for the sender's address.
     * This allows a user to remove their public identity link from the blockchain.
     */
    public function revokeRecord()
    {
        if ($this->records[$this->src] === null) {
            $this->error("RECORD_NOT_FOUND");
        }

        unset($this->records[$this->src]);
    }

    /**
     * @SmartContractView
     * Resolves a PHPCoin address to its DID Document's IPFS hash.
     * @param string $address The address to look up.
     * @return string|null The IPFS hash, or null if no record exists.
     */
    public function resolve($address)
    {
        return $this->records[$address];
    }
}
