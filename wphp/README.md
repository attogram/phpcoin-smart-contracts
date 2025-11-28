# Wrapped PHPCoin (WPHP)

This directory contains the smart contracts for Wrapped PHPCoin (WPHP), a token that is pegged 1:1 with PHPCoin.

## Contracts

### `wphp.php`

This is the main contract for the WPHP token. It extends the `ERC20Token` contract to provide the functionality for wrapping and unwrapping PHPCoin.

**Methods:**

*   `wrap()`: Wraps PHPCoin into WPHP tokens by sending PHPCoin to the contract.
*   `unwrap($amount)`: Unwraps WPHP tokens back into PHPCoin.

### `erc_20_token.php`

This is a standard ERC20 token implementation. The `WrappedPHP` contract extends this contract.

## Usage

### Deployment

To deploy the WPHP contract, you will need to compile it and then send a deployment transaction to the network. See the main `README.md` file for more information on how to do this.

### Wrapping and Unwrapping

To wrap PHPCoin, send a transaction to the `wrap()` method of the WPHP contract with the amount of PHPCoin you want to wrap.

To unwrap WPHP, call the `unwrap()` method of the WPHP contract with the amount of WPHP you want to unwrap.
