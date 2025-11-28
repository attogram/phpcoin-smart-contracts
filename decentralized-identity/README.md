# Decentralized Identity (DID)

This example demonstrates a foundational smart contract for a Decentralized Identity system, often called a DID Registry. It showcases how a PHPCoin address can be used as the root of a self-sovereign digital identity.

## Concept

In traditional web applications, your identity is owned by the service provider (e.g., Google, Facebook). Decentralized Identity (DID) flips this model: you, the user, own and control your identity.

This contract acts as the "root of trust." It creates a simple, unbreakable link between your PHPCoin address (something you control) and a public "DID Document" (a file that describes your identity), which is stored on IPFS.

### What is a DID Document?

A DID Document is a simple JSON file, stored on IPFS, that you control. It contains your public keys (for signing and encryption) and can list service endpoints (like a personal website or a social media profile). This allows other people and applications to find your information and interact with you securely, without relying on a central directory.

## How it Works

1.  **Create a DID Document:** A user creates a JSON file with their public identity information and uploads it to IPFS to get a content hash (CID).
2.  **Register the DID:** The user calls the `setRecord(ipfsHash)` function on this contract. The contract stores a link: `Your PHPCoin Address -> Your DID Document's IPFS Hash`. Your address is now your DID.
3.  **Resolve the DID:** Another user or application can call the `resolve(address)` function to look up your address and find the location of your public DID Document.

## Contract Functions

*   `setRecord(ipfsHash)`: Creates or updates the link between your address and your DID document on IPFS. This is the core function for managing your identity.
*   `revokeRecord()`: If you are the owner, you can delete your record from the registry, effectively revoking your public DID.
*   `resolve(address)`: A free, read-only function that returns the IPFS hash of a DID Document for a given PHPCoin address.
