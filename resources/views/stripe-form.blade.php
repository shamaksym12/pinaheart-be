<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  <style>
    /**
 * The CSS shown here will not be introduced in the Quickstart guide, but shows
 * how you can use CSS to style your Element's container.
 */
    .StripeElement {
      box-sizing: border-box;

      height: 40px;

      padding: 10px 12px;

      border: 1px solid transparent;
      border-radius: 4px;
      background-color: white;

      box-shadow: 0 1px 3px 0 #e6ebf1;
      -webkit-transition: box-shadow 150ms ease;
      transition: box-shadow 150ms ease;
    }

    .StripeElement--focus {
      box-shadow: 0 1px 3px 0 #cfd7df;
    }

    .StripeElement--invalid {
      border-color: #fa755a;
    }

    .StripeElement--webkit-autofill {
      background-color: #fefde5 !important;
    }
  </style>
</head>

<body>

  <form action="/stripe/charge" method="post" id="payment-form">
    @csrf
    <div class="form-row">
      <label for="card-element">
        Credit or debit card
      </label>
      <div id="card-element">
        <!-- A Stripe Element will be inserted here. -->
      </div>

      <!-- Used to display form errors. -->
      <div id="card-errors" role="alert"></div>
    </div>

    <button>Submit Payment</button>
  </form>
  <br>
  <input type="text" name="" id="token" readonly>
  <script src="https://js.stripe.com/v3/"></script>
  <script>
    // Create a Stripe client.
    var stripe = Stripe('pk_test_ehJQRUrspVISf6AGXOBqZ3wz');

    // Create an instance of Elements.
    var elements = stripe.elements();

    // Custom styling can be passed to options when creating an Element.
    // (Note that this demo uses a wider set of styles than the guide below.)
    var style = {
      base: {
        color: '#32325d',
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
          color: '#aab7c4'
        }
      },
      invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
      }
    };

    // Create an instance of the card Element.
    var card = elements.create('card', {style: style});

    // Add an instance of the card Element into the `card-element` <div>.
    card.mount('#card-element');

    // Handle real-time validation errors from the card Element.
    card.addEventListener('change', function(event) {
      var displayError = document.getElementById('card-errors');
      if (event.error) {
        displayError.textContent = event.error.message;
      } else {
        displayError.textContent = '';
      }
    });

    // Handle form submission.
    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {
      console.log('EventSubmit');
      event.preventDefault();

      stripe.createToken(card).then(function(result) {
        console.log('createToken');
        if (result.error) {
          // Inform the user if there was an error.
          var errorElement = document.getElementById('card-errors');
          errorElement.textContent = result.error.message;
        } else {
          // Send the token to your server.
          let token = result.token;
          document.getElementById('token').setAttribute('value', token.id);
          let body = {
              token: token.id
            }
          let response = fetch('api/payments/stripe/38', {
            method: 'POST',
            headers: {
              'Content-Type' : 'application/json',
              Authorization: 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImE0YjljMmIyNTQyM2E2NGUxNmRkOTFhNzFhZGJiNDk5NWRjYzA2YTE0NTkxZWI4MzJhZWJhZTJkZThmNDU4MTU3MzgzZmRhZWNmMTg3NzZiIn0.eyJhdWQiOiIyIiwianRpIjoiYTRiOWMyYjI1NDIzYTY0ZTE2ZGQ5MWE3MWFkYmI0OTk1ZGNjMDZhMTQ1OTFlYjgzMmFlYmFlMmRlOGY0NTgxNTczODNmZGFlY2YxODc3NmIiLCJpYXQiOjE1NjgxMTA3MjYsIm5iZiI6MTU2ODExMDcyNiwiZXhwIjoxNTk5NzMzMTI2LCJzdWIiOiIxNSIsInNjb3BlcyI6WyIqIl19.ugQT_zl9auoYGaiQh3EwugpP4Sm_7AA3R9RtqoijmixQb85lMq8uMyrb1arrwEC21pTskJMpz7nbM1Qpe3tduZKIWKGcOy1D45L7dIV6ONc-EPEpR7AmGySdrYazjeweNaBTbwcZvcAW1RIWLrPhsY7Vrlh1nmooMHjI_3IUKF7596EuKQG-Z9sX3OwozsCZfrYp6HCQLsSZvl0C6aFcgdvV9t7hWO9rD5RsxtnLSk8_jq2rQSBcfzQAvI8EBHP08abtJPSQB2-K_VrEUgBgzwALw3Jc-MfHvYsrCXm8CnD3_w-CIjWCsJNPxLh6kyQmJIK6xkb3sLVdIMw5s9GDk8XJo28jyAmQpNBWkB1NGU3Nmov2M7zNji7Y7KxRrh_mzQaQhyxhElfa4Dyk1Uug5rKoCmu0cPfhKau7HB5oUBhF-bfPsP_TGXwYV1OXRhUkcdJgpp_0rb8oHiETnZQQf0v0yPgZCLkHEYwF_EXQUSaYNE0kyKqyGL5wwYwSmir0k46GHpmG3BTcLjgBDf9H2WfaVXPmXcVy1neOFP7kYU8A2q-cma6iZdzduTODpZuSsnhvX-pT-wmkfqb8HqIKtz7A9AgSong2nURzQM2ky8AkaPwGgmOE-_YGTNMZws1hlZBxeDTNZzZG-ejLjSUlidLSdLG14BRhWKpQVp01ixs'
            },
            body: JSON.stringify(body),
          }).then(async function(response){
            const json = await response.json();
            console.log(json);
            const data = json.data;
            if(data.subscription_status == 'incomplete' && data.payment_intent_status == 'requires_action') {
              var paymentIntentSecret = data.secret;

              stripe.handleCardPayment(paymentIntentSecret).then(function(result) {
                if (result.error) {
                  console.log('Error');
                  // Display error.message in your UI.
                } else {
                  console.log('Success');
                  console.log(result);
                }
              });
            } else {
              console.log('NO');
            }
          });
        }
      });
    });

    // Submit the form with the token ID.
    function stripeTokenHandler(token) {
      // Insert the token ID into the form so it gets submitted to the server
      var form = document.getElementById('payment-form');
      var hiddenInput = document.createElement('input');
      hiddenInput.setAttribute('type', 'hidden');
      hiddenInput.setAttribute('name', 'stripeToken');
      hiddenInput.setAttribute('value', token.id);


      // Submit the form
      // form.submit();
    }
  </script>
</body>

</html>