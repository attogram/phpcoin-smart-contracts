# Decentralized DNS

This example demonstrates how to build a censorship-resistant Domain Name System (DNS) using a PHPCoin smart contract in combination with IPFS.

## Concept

Traditional DNS is centralized, meaning a single entity controls the registration and resolution of domain names. This contract creates a decentralized alternative where domain names are registered on the blockchain and their content is stored on the decentralized IPFS network.

The result is a website or application that cannot be taken down or censored.

## How it Works

*   **Content Storage:** The website's files (`index.html`, etc.) are first uploaded to IPFS to get a unique content hash (CID).
*   **Name Registry:** This smart contract acts as the registrar. It stores a record mapping a human-readable name (e.g., `my-site.phpc`) to its corresponding IPFS content hash.
*   **Name Resolution:** A special browser or a browser extension can query the `resolve(name)` function of this contract to find the IPFS hash and then fetch the website's content from the IPFS network.

## Contract Functions

*   `register(name, ipfsHash)`: Pay a 1 PHPCoin fee to register a new name that is not already taken.
*   `update(name, newIpfsHash)`: If you are the owner of a name, you can update its IPFS hash to point to new content.
*   `transfer(name, newOwner)`: If you are the owner, you can transfer the ownership of a name to a new address.
*   `resolve(name)`: A free, read-only function that returns the IPFS hash for a given name.
*   `getOwner(name)`: A free, read-only function that returns the PHPCoin address of a name's owner.
