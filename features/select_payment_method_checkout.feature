@select_payment_method_checkout
Feature: Select GoPay payment method in checkout
	In order to select GoPay payment method
	As a Customer
	I want to select GoPay payment method

	Background:
		Given the store operates on a single channel in "United States"
		And the store has a product "PHP T-Shirt" priced at "$19.99"
		And the store ships everywhere for free
		And the store allows paying with "GoPay"
	    And the store allows paying with name "GoPay" and code "gopay" GoPay gateway
		And I am a logged in customer

	@ui
	Scenario: Selecting a payment method
		Given I have product "PHP T-Shirt" in the cart
		And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
		And I select "Free" shipping method
		And I complete the shipping step
		When I select "GoPay" payment method
		And I complete the payment step
		Then I should be on the checkout complete step
