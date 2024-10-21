# NidAPI Package for Laravel

This package provides an easy integration of the National Identification API (NIDAPI) for Laravel applications. It simplifies the process of authenticating users and interacting with the NIDAPI endpoints.

## Requirements

- **PHP**: >= 7.3
- **Laravel**: 8.x, 9.x, or higher

## Features

- Easy integration with the National Identification API (NIDAPI)
- Supports OAuth2 authentication
- Fetch user data using access tokens

## Installation

### Step 1: Install the Package

You can install this package via Composer. Add the following line to your `composer.json` file in the `require` section or run the command below:

```bash
composer require ekeng/nid
```

Step 2: Publish the Migrations

After installing the package, you will need to publish the migrations:

```bash
php artisan vendor:publish --tag=migrations
```

Step 3: Run the Migrations

Once the migrations are published, run the migration command:

```bash
php artisan migrate
```

Step 4: Configure Environment Variables

Add the following variables to your .env file, which will be used to connect with the NIDAPI:

NID_CLIENT_ID=your-client-id\
NID_CLIENT_SECRET=your-client-secret\
NID_BASE_URI=https://nid.e-gov.am \
NID_AUTH_URI=auth \
NID_TOKEN_URI=https://nid.e-gov.am/auth/token

Step 5: Add the Service Provider (if needed)

If you are using Laravel's auto-discovery feature, this step can be skipped. However, if you're using an older version of Laravel or auto-discovery doesn’t work, manually add the service provider in the config/app.php file:

```
'providers' => [
    Ekeng\Nid\NidServiceProvider::class,
],
```

Step 6: Using the API in Your Application

To use the NID API in your Laravel application, inject the NidAPI class into your controller or service and create new Instance of NidAPI:

```
$nidApi = new NidAPI($client_id, $client_secret, $base_url, $auth_uri, $nid_token);
```


Step 7: Callback URL
Create callback url and name it "nid_callback" like example below

```
 Route::get('/nid/callback', [\App\Http\Controllers\LoginController::class,'auth'])->name('nid_callback');
```

Step 8: Available Methods in NidAPI

generateAuthRedirectUrl(): This method generates a URL for redirecting users to the NID authorization page.
This method accept one parameter 

$lang - language path of website (optional)

Example usage:

```
$redirectUrl = $nidApi->generateAuthRedirectUrl($lang);
```

getUserDetails($request): This method retrieves user data (available after redirecting to back url).

Example usage:

```
$userData = $nidApi->getUserDetails($request);
```
