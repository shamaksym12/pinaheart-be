<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
</head>
<body>
    <div id="paypal-button-container">

    </div>
    <script
        src="https://www.paypal.com/sdk/js?client-id=Abv_Rc4sPTz21LG1XLp9R9d99md1mHE3JHINSyqNuDP8Pmk_npZWJb0z-dzZmrXdgtTnzsXNkmdoAoBA&vault=true">
    </script>
    <script>
        // paypal.Buttons().render('#paypal-button-container');
        paypal.Buttons({
            createSubscription: function(data, actions) {
            return actions.subscription.create({
                    'plan_id': 'P-8FB10368RM484572NLVXDYSQ',
                    'auto_renewal': true,
                    });
                },
                onApprove: function(data, actions) {
                    let response = fetch('api/payments/paypal/35', {
                        method: 'POST',
                        headers: {
                        'Content-Type' : 'application/json',
                        Authorization: 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImE0YjljMmIyNTQyM2E2NGUxNmRkOTFhNzFhZGJiNDk5NWRjYzA2YTE0NTkxZWI4MzJhZWJhZTJkZThmNDU4MTU3MzgzZmRhZWNmMTg3NzZiIn0.eyJhdWQiOiIyIiwianRpIjoiYTRiOWMyYjI1NDIzYTY0ZTE2ZGQ5MWE3MWFkYmI0OTk1ZGNjMDZhMTQ1OTFlYjgzMmFlYmFlMmRlOGY0NTgxNTczODNmZGFlY2YxODc3NmIiLCJpYXQiOjE1NjgxMTA3MjYsIm5iZiI6MTU2ODExMDcyNiwiZXhwIjoxNTk5NzMzMTI2LCJzdWIiOiIxNSIsInNjb3BlcyI6WyIqIl19.ugQT_zl9auoYGaiQh3EwugpP4Sm_7AA3R9RtqoijmixQb85lMq8uMyrb1arrwEC21pTskJMpz7nbM1Qpe3tduZKIWKGcOy1D45L7dIV6ONc-EPEpR7AmGySdrYazjeweNaBTbwcZvcAW1RIWLrPhsY7Vrlh1nmooMHjI_3IUKF7596EuKQG-Z9sX3OwozsCZfrYp6HCQLsSZvl0C6aFcgdvV9t7hWO9rD5RsxtnLSk8_jq2rQSBcfzQAvI8EBHP08abtJPSQB2-K_VrEUgBgzwALw3Jc-MfHvYsrCXm8CnD3_w-CIjWCsJNPxLh6kyQmJIK6xkb3sLVdIMw5s9GDk8XJo28jyAmQpNBWkB1NGU3Nmov2M7zNji7Y7KxRrh_mzQaQhyxhElfa4Dyk1Uug5rKoCmu0cPfhKau7HB5oUBhF-bfPsP_TGXwYV1OXRhUkcdJgpp_0rb8oHiETnZQQf0v0yPgZCLkHEYwF_EXQUSaYNE0kyKqyGL5wwYwSmir0k46GHpmG3BTcLjgBDf9H2WfaVXPmXcVy1neOFP7kYU8A2q-cma6iZdzduTODpZuSsnhvX-pT-wmkfqb8HqIKtz7A9AgSong2nURzQM2ky8AkaPwGgmOE-_YGTNMZws1hlZBxeDTNZzZG-ejLjSUlidLSdLG14BRhWKpQVp01ixs'
                        },
                        body: JSON.stringify(data),
                    }).then(async function(response){
                        const json = await response.json();
                        console.log(json);
                    });
                }
            }).render('#paypal-button-container');
        </script>
</body>
</html>