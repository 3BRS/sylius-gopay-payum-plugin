# 2.3

- Add support for Sylius 1.14

# 2.2

- Add **cancel**
  - GoPay does not support simple cancel, so it will try to refund, then revoke authorization, then if payment is **new** it will just log warning (canceling unpaid new payment after some time is responsibility of GoPay itself)

# 2.1

- **Auto-refund** on Sylius state machine transition (and button in admin)

# 2.0

- Add **refund**
- Add support for Sylius 1.12, 1.13, Symfony 6
- Drop support for Sylius <=1.11

# 1.0

- Add support for Sylius 1.11, PHP 8.0
- Drop support for Sylius <=1.10, PHP 7.4

Note: for older Sylius versions use https://github.com/Prometee/gopay-plugin
