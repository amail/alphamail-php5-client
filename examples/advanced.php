<?php

    namespace AlphaMail\Examples;

    use AlphaMail\Client\EmailService;
    use AlphaMail\Client\Entities\EmailContact;
    use AlphaMail\Client\Entities\EmailMessagePayload;
    use AlphaMail\Client\Exceptions\ValidationException;
    use AlphaMail\Client\Exceptions\AuthorizationException;
    use AlphaMail\Client\Exceptions\InternalException;
    use AlphaMail\Client\Exceptions\ServiceException;

    ini_set("display_errors", 1);
    error_reporting(E_ALL);

    require __DIR__.'/../autoload.php';

    // Step #1: Let's start by entering the web service URL and the API-token you've been provided
    // If you haven't gotten your API-token yet. Log into AlphaMail or contact support at 'support@amail.io'.
    $email_service = EmailService::create()
        ->setServiceUrl("http://api.amail.io/v2/")
        ->setApiToken("YOUR-ACCOUNT-API-TOKEN-HERE");
    
    // Step #2: Let's fill in the gaps for the variables (stuff) we've used in our template
    $message = array(
        "id" => "abc-123-456",
        "name" => "Some Guy",
        "profile_url" => "http://domain.com/profile/ABC-123-456/",
        "recommended_profiles" => array(
            array(
                "id" => "abc-222-333",
                "name" => "Jane Joe",
                "profile_url" => "http://domain.com/profile/ABC-222-333/",
                "profile_image_url" => "http://img.domain.com/profile/abc-222-333.jpg",
                "age" => 24
            )
        )
    );
    
    // Step #3: Let's set up everything that is specific for delivering this email
    $payload = EmailMessagePayload::create()
        ->setProjectId(12345) // ID of the AlphaMail project you want to send with
        ->setSender(new EmailContact("Sender Company Name", "your-sender-email@your-sender-domain.com"))
        ->setReceiver(new EmailContact("Joe E. Receiver", "email-of-receiver@comfirm.se"))
        ->setBodyObject($message);
    
    try
    {
        // Step #4: Haven't we waited long enough. Let's send this!
        $response = $email_service->queue($payload);

        // Step #5: Pop the champagné! We got here which mean that the request was sent successfully and the email is on it's way!        
        echo sprintf("Successfully queued message with id '%s' (you can use this ID to get more details about the delivery)", $response->result);
    }
    // Oh heck. Something went wrong. But don't stop here.
    // If you haven't solved it yourself. Just contact our brilliant support and they will help you.
    catch (ValidationException $exception)
    {
        // Example: Handle request specific error code here
        if ($exception->response->error_code == 3)
        {
            // Example: Print a nice message to the user.
        }
        else
        {
            // Something in the input was wrong. Probably good to double double-check!
            echo sprintf("Validation error: %s (%d)", $exception->response->message, $exception->response->error_code);
        }
    }
    catch (AuthorizationException $exception)
    {
        // Ooops! You've probably just entered the wrong API-token.
        echo sprintf("Authentication error: %s (%d)", $exception->response->message, $exception->response->error_code);
    }
    catch (InternalException $exception)
    {
        // Not that it is going to happen.. Right :-)
        echo sprintf("Internal error: %s (%d)", $exception->response->message, $exception->response->error_code);
    }
    catch (ServiceException $exception)
    {
        // Most likely your internet connection that is down. We are covered for most things except "multi-data-center-angry-server-bashing-monkeys" (remember who coined it) or.. nuclear bombs.
        // If one blew. Well.. It's just likely that our servers are down.
        echo sprintf("An error (probably related to connection) occurred: %s", $exception->getMessage());
    }
    
    // Writing to out like a boss
    die("<br /><br /><strong>In doubt or experiencing problems?</strong><br />" .
        "Please email our support at 'support@comfirm.se'");

?>