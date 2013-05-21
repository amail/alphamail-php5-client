<?php
    
    /*
    The MIT License

    Copyright (c) Robin Orheden, 2013 <http://amail.io/>

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.
    */

    include_once("../src/comfirm.alphamail.client/emailservice.class.php");
    
    $email_service = new AlphaMailEmailService("YOUR-ACCOUNT-API-TOKEN-HERE");
    
    $message = array(
        "id" => "abc-123-456",
        "name" => "Some Guy",
        "profile_url" => "http://domain.com/profile/ABC-123-456/"
    );
    
    $payload = EmailMessagePayload::create()
        ->setProjectId(12345) // ID of the AlphaMail project you want to send with
        ->setSender(new EmailContact("Sender Company Name", "your-sender-email@your-sender-domain.com"))
        ->setReceiver(new EmailContact("Joe E. Receiver", "email-of-receiver@comfirm.se"))
        ->setBodyObject($message);
    
    try
    {
        $response = $email_service->queue($payload);
        printf("Mail successfully sent! ID = %s", $response->result);
    }
    catch(AlphaMailServiceException $exception)
    {
        printf("Error! %s (%s)", $exception->getMessage(), $exception->getErrorCode());
    }

?>