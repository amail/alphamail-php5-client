<?php
    
    namespace AlphaMail\Examples;

    use AlphaMail\Client\EmailService;
    use AlphaMail\Client\Entities\EmailContact;
    use AlphaMail\Client\Entities\EmailMessagePayload;
    use AlphaMail\Client\Exceptions\ServiceException;

    require __DIR__.'/../autoload.php';
    
    $email_service = new EmailService("YOUR-ACCOUNT-API-TOKEN-HERE");
    
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
    catch(ServiceException $exception)
    {
        printf("Error! %s (%s)", $exception->getMessage(), $exception->getErrorCode());
    }

?>