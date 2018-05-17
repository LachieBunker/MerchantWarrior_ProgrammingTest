<?php
    function HandleUrlReturn($redirectionData) {

        //Get response message from redirected curl request
        $xml = simplexml_load_string($redirectionData);
        $xml = (array)$xml;

        $response = $xml['responseMessage'];

        //Redirect to response page
        if($response == "Trensaction approved")
        {
            header('Location: paymentsuccessful.html');
        }
        else {
            header('Location: paymentfailed.html');
        }
    }

?>
