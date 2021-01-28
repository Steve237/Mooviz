(function() {
    var stripe = Stripe('pk_test_51HpdbCLfEkLbwHD1UtFDGj4m6I3U8JdlzEKugeOjDIK8wzbYWv68cvNuGWzUb9dk8KYnJyaSfeUrEFRYvjtiExsJ00pZMF1kFn');
    var checkoutButton = document.getElementById('checkout-button-price_1Hrd49LfEkLbwHD1TCJkouof');
    checkoutButton.addEventListener('click', function () {
      // When the customer clicks on the button, redirect
      // them to Checkout.
      stripe.redirectToCheckout({
        lineItems: [{price: 'price_1Hrd49LfEkLbwHD1TCJkouof', quantity: 1}],
        mode: 'subscription',
        // Do not rely on the redirect to the successUrl for fulfilling
        // purchases, customers may not always reach the success_url after
        // a successful payment.
        // Instead use one of the strategies described in
        // https://stripe.com/docs/payments/checkout/fulfill-orders

        successUrl: window.location.protocol + '//127.0.0.1:8000/7b9eae84f7af073e9b667e57cac32ce0',
        cancelUrl: window.location.protocol + '//127.0.0.1:8000/error/0a8b325a1197ab7d92c66b894c11ead2',
      })
      .then(function (result) {
        if (result.error) {
          // If `redirectToCheckout` fails due to a browser or network
          // error, display the localized error message to your customer.
          var displayError = document.getElementById('error-message');
          displayError.textContent = result.error.message;
        }
      });
    });
  })();