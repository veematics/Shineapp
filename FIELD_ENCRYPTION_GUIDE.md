# Field-Specific Encryption Guide for Laravel Models

This guide explains how to implement field-specific encryption for sensitive data in Laravel models, based on the pattern established in the AppSetting model.

## Overview

Field-specific encryption allows you to automatically encrypt sensitive data when storing it in the database and decrypt it when accessing it through your Laravel models. This ensures sensitive information like API keys, passwords, and personal data are never stored in plain text.

## Implementation Steps

### 1. Define Encrypted Fields in Model

Add the fields you want encrypted to the `$hidden` array to prevent them from being exposed in JSON responses:

```php
class YourModel extends Model
{
    protected $hidden = [
        'sensitive_field1',
        'sensitive_field2',
        'api_key',
        'secret_token',
    ];
}
```

### 2. Create Accessor Methods

For each encrypted field, create an accessor method that automatically decrypts the value when accessed:

```php
public function getSensitiveField1Attribute($value)
{
    return $value ? decrypt($value) : null;
}

public function getApiKeyAttribute($value)
{
    return $value ? decrypt($value) : null;
}

public function getSecretTokenAttribute($value)
{
    return $value ? decrypt($value) : null;
}
```

**Naming Convention**: Use `get{FieldName}Attribute` where `{FieldName}` is the StudlyCase version of your field name.

### 3. Handle Caching Issues (If Needed)

If you encounter issues with Laravel's attribute caching bypassing your accessors, override the `getAttribute` method:

```php
use Illuminate\Support\Str;

public function getAttribute($key)
{
    // List of encrypted fields
    $encryptedFields = ['sensitive_field1', 'api_key', 'secret_token'];
    
    if (in_array($key, $encryptedFields)) {
        $rawValue = $this->getAttributeFromArray($key);
        $accessorMethod = 'get' . Str::studly($key) . 'Attribute';
        return $this->$accessorMethod($rawValue);
    }
    
    return parent::getAttribute($key);
}
```

### 4. Encrypt Data in Controller

In your controller, encrypt the values before saving to the database:

```php
public function store(Request $request)
{
    $data = $request->validated();
    
    // Encrypt sensitive fields before saving
    $encryptedFields = ['sensitive_field1', 'api_key', 'secret_token'];
    
    foreach ($encryptedFields as $field) {
        if (isset($data[$field]) && !empty($data[$field])) {
            $data[$field] = encrypt($data[$field]);
        }
    }
    
    YourModel::create($data);
}

public function update(Request $request, YourModel $model)
{
    $data = $request->validated();
    
    // Encrypt sensitive fields before updating
    $encryptedFields = ['sensitive_field1', 'api_key', 'secret_token'];
    
    foreach ($encryptedFields as $field) {
        if (isset($data[$field]) && !empty($data[$field])) {
            $data[$field] = encrypt($data[$field]);
        }
    }
    
    $model->update($data);
}
```

### 5. Database Schema Considerations

Ensure your database columns can store encrypted data. Encrypted strings are typically longer than the original text:

```php
// In your migration
Schema::create('your_table', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('sensitive_field1')->nullable(); // Use TEXT for encrypted data
    $table->text('api_key')->nullable();
    $table->text('secret_token')->nullable();
    $table->timestamps();
});
```

## Complete Example

Here's a complete example of a model with encrypted fields:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'social_security_number',
        'credit_card_number',
        'api_token',
    ];

    protected $hidden = [
        'social_security_number',
        'credit_card_number',
        'api_token',
    ];

    // Accessor methods for encrypted fields
    public function getSocialSecurityNumberAttribute($value)
    {
        return $value ? decrypt($value) : null;
    }

    public function getCreditCardNumberAttribute($value)
    {
        return $value ? decrypt($value) : null;
    }

    public function getApiTokenAttribute($value)
    {
        return $value ? decrypt($value) : null;
    }

    // Override getAttribute to handle caching issues
    public function getAttribute($key)
    {
        $encryptedFields = ['social_security_number', 'credit_card_number', 'api_token'];
        
        if (in_array($key, $encryptedFields)) {
            $rawValue = $this->getAttributeFromArray($key);
            $accessorMethod = 'get' . Str::studly($key) . 'Attribute';
            return $this->$accessorMethod($rawValue);
        }
        
        return parent::getAttribute($key);
    }
}
```

And the corresponding controller:

```php
<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'social_security_number' => 'nullable|string',
            'credit_card_number' => 'nullable|string',
            'api_token' => 'nullable|string',
        ]);

        // Encrypt sensitive fields
        $encryptedFields = ['social_security_number', 'credit_card_number', 'api_token'];
        
        foreach ($encryptedFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $data[$field] = encrypt($data[$field]);
            }
        }

        return UserProfile::create($data);
    }

    public function update(Request $request, UserProfile $profile)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'social_security_number' => 'sometimes|nullable|string',
            'credit_card_number' => 'sometimes|nullable|string',
            'api_token' => 'sometimes|nullable|string',
        ]);

        // Encrypt sensitive fields
        $encryptedFields = ['social_security_number', 'credit_card_number', 'api_token'];
        
        foreach ($encryptedFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $data[$field] = encrypt($data[$field]);
            }
        }

        $profile->update($data);
        return $profile;
    }
}
```

## Best Practices

### 1. Security Considerations
- Always use Laravel's built-in `encrypt()` and `decrypt()` functions
- Ensure your `APP_KEY` is properly set and secure
- Never log decrypted values
- Use HTTPS in production to protect data in transit

### 2. Performance Considerations
- Encryption/decryption has a performance cost - only encrypt truly sensitive data
- Consider caching decrypted values in memory for frequently accessed data
- Use database indexes on non-encrypted fields for queries

### 3. Testing
- Always test that data is properly encrypted in the database
- Verify that accessors return decrypted values
- Test edge cases like null values and empty strings

### 4. Backup and Recovery
- Encrypted data cannot be recovered without the correct `APP_KEY`
- Ensure your `APP_KEY` is backed up securely
- Consider key rotation strategies for long-term applications

## Testing Your Implementation

Create a simple test script to verify your encryption is working:

```php
<?php
// test_encryption.php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test your model
$profile = new App\Models\UserProfile();
$profile->name = 'Test User';
$profile->social_security_number = '123-45-6789';
$profile->save();

echo "Raw value in database: " . $profile->getAttributeFromArray('social_security_number') . "\n";
echo "Decrypted value: " . $profile->social_security_number . "\n";
echo "Encryption working: " . ($profile->getAttributeFromArray('social_security_number') !== $profile->social_security_number ? 'YES' : 'NO') . "\n";
```

## Troubleshooting

### Common Issues

1. **Accessor not being called**: Check if the field is cached. Use the `getAttribute` override method.

2. **Double encryption**: Make sure you're not encrypting already encrypted data. Check your controller logic.

3. **Decryption errors**: Ensure the `APP_KEY` hasn't changed and the data was encrypted with the same key.

4. **Performance issues**: Consider if you're encrypting too many fields or accessing them too frequently.

### Debugging

Add temporary logging to debug encryption issues:

```php
public function getSensitiveFieldAttribute($value)
{
    \Log::info('Accessor called', ['raw_value' => $value]);
    $decrypted = $value ? decrypt($value) : null;
    \Log::info('Decrypted value', ['decrypted' => $decrypted]);
    return $decrypted;
}
```

Remember to remove debug logging in production!

## Conclusion

This encryption pattern provides a secure, transparent way to handle sensitive data in Laravel applications. The data is automatically encrypted when stored and decrypted when accessed, with no changes required to your application logic beyond the initial setup.

For questions or issues, refer to the Laravel documentation on encryption or consult with your security team for sensitive data handling requirements.