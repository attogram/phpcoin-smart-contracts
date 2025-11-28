# phpcoin-smart-contracts
PHPCoin Smart Contracts

https://github.com/attogram/node/blob/let-there-be-docs/docs/smart-contracts/builders-guide.md

[PHPCoin Docs](../) > [Smart Contracts](./) > Smart Contract Builder's Guide


---

# 🛠️ PHPCoin Smart Contract Builder's Guide

This guide outlines the essential structural and utility rules for creating a functional smart contract in the PHPCoin environment.

## ⚙️ Structural Rules

Every PHPCoin smart contract is a PHP class with specific structural requirements. These rules ensure that the contract can be correctly deployed and executed by the blockchain.

| Element | Requirement | Example (Minimal) |
|---|---|---|
| **Class Definition** | Must extend `SmartContractBase`. | `class YourContract extends SmartContractBase { ... }` |
| **Class Name Constant** | A `const SC_CLASS_NAME` must be defined, holding the name of the class. | `const SC_CLASS_NAME = "YourContract";` |
| **Deployment Method** | The `deploy()` method, annotated with `@SmartContractDeploy`, is executed once upon deployment to set the contract's initial state. | `/** @SmartContractDeploy */ public function deploy($param) { ... }` |

## 💾 State Management

PHPCoin smart contracts manage persistent state through class properties annotated with specific docblock tags. These properties are automatically backed by the blockchain's state database.

| Data Type | PHP Annotation/Definition | Usage |
|---|---|---|
| **Single Value** | `/** @SmartContractVar */ public $owner;` | Used for scalar values like addresses, numbers, or strings. |
| **Map/Array** | `/** @SmartContractMap */ public SmartContractMap $balances;` | Used for associative arrays, mapping keys to values (e.g., user balances). |

### Reading and Writing State

Interaction with state variables is designed to be intuitive, using standard PHP syntax.

| Operation | Example | Description |
|---|---|---|
| **Write (Single)** | `$this->owner = $this->src;` | Assign a value to a `@SmartContractVar`. |
| **Read (Single)** | `$currentOwner = $this->owner;` | Retrieve the value of a `@SmartContractVar`. |
| **Write (Map)** | `$this->balances[$userAddress] = 100;` | Set a value for a key in a `SmartContractMap`. |
| **Read (Map)** | `$userBalance = $this->balances[$userAddress];` | Get the value for a key from a `SmartContractMap`. |

## 🏷️ Annotations

PHPCoin uses docblock annotations to define the type and behavior of smart contract methods and properties. These annotations are essential for the PHPCoin runtime to correctly interpret and execute the contract's code.

| Annotation | Purpose | Example |
|---|---|---|
| **`@SmartContractDeploy`** | Marks the method that is executed only once when the contract is deployed. This is where you should set the initial state of the contract. | `/** @SmartContractDeploy */ public function deploy() { ... }` |
| **`@SmartContractView`** | Designates a read-only method. View methods can be called without creating a transaction and cannot modify the contract's state. | `/** @SmartContractView */ public function getOwner() { return $this->owner; }` |
| **`@SmartContractTransact`** | Marks a method that modifies the contract's state. Executing a transact method requires sending a transaction to the network. | `/** @SmartContractTransact */ public function setOwner($newOwner) { ... }` |
| **`@SmartContractVar`** | Declares a public property as a persistent, single-value state variable. | `/** @SmartContractVar */ public $owner;` |
| **`@SmartContractMap`** | Declares a public property of type `SmartContractMap` as a persistent key-value store. | `/** @SmartContractMap */ public SmartContractMap $balances;` |

## 🛡️ Core Transaction Templates

All methods marked with `@SmartContractTransact` should include checks to ensure the integrity and security of the contract. The following templates cover the most common requirements.

| Security Rule | Code Requirement / Example | Rationale |
|---|---|---|
| **Access Control** | `if ($this->src != $this->owner) { $this->error("UNAUTHORIZED"); }` | Verifies that the sender (`$this->src`) has permission to execute the function. |
| **Input Validation** | `if (!Account::valid($to)) { $this->error("INVALID_ADDRESS"); }` | Ensures that provided data, such as addresses, are in the correct format before processing. |
| **Fee Check** | `if ($this->value < MINIMUM_FEE) { $this->error("INSUFFICIENT_FEE"); }` | Prevents Denial-of-Service or griefing attacks by requiring a minimum fee. |
| **Data Size Limit** | `if (strlen($input) > MAX_INPUT_SIZE) { $this->error("INPUT_TOO_LONG"); }` | Enforces storage and processing limits on user-provided data. |
| **Non-Overwrite** | `if ($this->records[$id] !== null) { $this->error("RECORD_EXISTS"); }` | Prevents the accidental or malicious overwriting of existing state. |

## 🔩 Built-In Utilities

The `SmartContractBase` class provides a set of properties and methods for accessing transaction data, controlling execution, and interacting with the blockchain environment.

| Category | Method / Property | Purpose |
|---|---|---|
| **Transaction Data** | `string $this->id` | The hash of the current transaction. |
| **Transaction Data** | `string $this->src` | The address of the sender. |
| **Transaction Data** | `float $this->value` | The amount of PHPCoin sent with the transaction. |
| **Transaction Data** | `int $this->height` | The current block height. |
| **Transaction Data** | `string $this->address` | The contract's own address. |
| **State Reversion** | `void $this->error(string $msg)` | Halts execution, reverts all state changes, and returns the error message. |
| **Inter-Contract** | `mixed $this->callSmartContract(string $contract, string $method, array $params)` | Performs a read-only call to a method on another smart contract. |
| **Inter-Contract** | `void $this->execSmartContract(string $contract, string $method, array $params)` | Executes a state-changing transaction on another smart contract. |
| **Outgoing TX** | `Transaction::send(string $to, float $amount)` | Executes an outgoing transaction from the contract's address to another address. |

## 🪙 ERC20 Token Templates

The repository includes a set of pre-built ERC20 token templates that you can extend to create your own tokens. These templates are located in the `include/templates/tokens` directory.

| Template | Description |
|---|---|
| `erc_20_token.php` | A standard ERC20 token with basic transfer and allowance functionality. |
| `erc_20_token_burnable.php` | An ERC20 token that can be "burned" or destroyed, reducing the total supply. |
| `erc_20_token_mintable.php` | An ERC20 token that allows for the creation of new tokens, increasing the total supply. |
| `erc_20_token_burnable_mintable.php` | An ERC20 token that is both burnable and mintable. |

## 📦 Deployment

This section covers the tools and procedures for deploying your smart contracts.

### Compiling the Contract

The `utils/sc_compile.php` script is used to package your smart contract's source code into a `.phar` file.

**Usage:**

```bash
php utils/sc_compile.php [contract_address] [source_file.php] [output_file.phar]
```

-   `[contract_address]`: The address where the contract will be deployed.
-   `[source_file.php]`: The path to your smart contract's PHP source file.
-   `[output_file.phar]`: The path where the compiled `.phar` file will be saved.

### Deploying the Contract

After compiling the contract, you need to create a deployment transaction. This is done by creating a separate PHP script that uses the `SCUtil` class.

### Deployment Cost

Deploying a smart contract to the PHPCoin network incurs a fixed fee. This fee prevents spam and compensates the network for storing the contract's code.

-   **Mainnet:** 1000 PHPCoin
-   **Testnet:** 100 PHPCoin

This value is defined in the `getSmartContractCreateFee()` method within the `include/class/Blockchain.php` file.

**Example Deployment Script (`deploy.php`):**

```php
<?php
require_once 'vendor/autoload.php';
require_once 'utils/scutil.php';

// --- Configuration ---
$node = "https://your-phpcoin-node.com";
$private_key = "your_private_key";
$sc_address = "your_contract_address";
$phar_file = "path/to/your/contract.phar";
$deploy_params = ["Initial Message"]; // e.g., for the StateControl contract

// 1. Generate the deployment transaction
$tx = SCUtil::generateDeployTx($phar_file, $private_key, $sc_address, 0, $deploy_params);

// 2. Send the transaction to the network
$tx_id = SCUtil::sendTx($node, $tx);

echo "Deployment transaction sent: " . $tx_id . "\n";
```

## 🛠️ Command-Line Tools

The `utils/` directory contains several PHP scripts for interacting with smart contracts from the command line. The most important of these is `scutil.php`, which provides a set of tools for deploying and managing contracts.

### `scutil.php`

The `scutil.php` script is not intended to be run directly, but rather included in other scripts to provide a convenient way to interact with smart contracts. It provides a `SCUtil` class with several static methods for common operations.

**Methods:**

| Method | Description |
|---|---|
| `getInterface` | Retrieves the interface definition for a deployed contract. |
| `executeView` | Executes a read-only `@SmartContractView` method on a contract. |
| `getPropertyValue` | Retrieves the value of a `@SmartContractVar` or a key from a `@SmartContractMap`. |
| `generateScExecTx` | Generates a transaction to execute a `@SmartContractTransact` method. |
| `generateScSendTx` | Generates a transaction to send PHPCoin from a smart contract to another address. |

## 🚀 Examples

This repository includes a set of examples that demonstrate how to build smart contracts, from a basic "Hello, World!" to a more feature-rich application.

*   **[Hello, World!](./hello-world/)**: The simplest possible smart contract.
*   **[Basic State Control](./state-control/)**: Demonstrates how to manage state with `@SmartContractVar` and `@SmartContractMap`.
*   **[Advanced Poll](./advanced-poll/)**: A more robust polling contract with owner-only actions and a fee requirement.
*   **[Wrapped PHPCoin (WPHP)](./wphp/)**: Creates a token that is pegged 1:1 with PHPCoin.
