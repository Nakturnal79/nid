# NidAPI Package for Laravel

This package provides an easy integration of the National Identification API (NIDAPI) for Laravel applications. It simplifies the process of authenticating users and interacting with the NIDAPI endpoints.

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
NID_AUTH_URI=auth
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

Step 7: Available Methods in NidAPI

generateAuthRedirectUrl(): This method generates a URL for redirecting users to the NID authorization page.

Example usage:

```
$redirectUrl = $nidApi->generateAuthRedirectUrl();
return redirect($redirectUrl);

```

fetchUserData($accessToken): This method retrieves user data using the access token.

Example usage:

```
$userData = $nidApi->fetchUserData($accessToken);

```


### Sections in this `README.md`:
1. **Features**: Briefly explains what the package does.
2. **Installation**: Detailed steps to install and configure the package.
3. **Configuration**: Instructions to set up environment variables.
4. **Usage**: Example on how to use the package in your application with code samples.
5. **Testing**: A section to encourage users to write tests for their integration.
6. **Contributing**: Invitation for other developers to contribute to the package.
7. **License**: License information (assuming it's MIT).

Feel free to adapt or extend this according to your package's specific features!
