# Google OAuth Setup Guide for Bhutan Taxi

This guide will help you set up "Sign in with Google" functionality for your Bhutan Taxi application.

## ✅ What's Already Done

- ✓ Laravel Socialite package installed
- ✓ Google OAuth controller created
- ✓ Routes configured
- ✓ Login/Register pages updated with Google sign-in buttons
- ✓ Database migration for google_id column completed
- ✓ User model updated

## 📝 Step-by-Step Setup

### 1. Create Google OAuth Credentials

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one:
   - Click on the project dropdown at the top
   - Click "New Project"
   - Name it: "Bhutan Taxi"
   - Click "Create"

3. Enable Google+ API:
   - In the left sidebar, go to **APIs & Services** > **Library**
   - Search for "Google+ API"
   - Click on it and press **Enable**

4. Create OAuth 2.0 Credentials:
   - Go to **APIs & Services** > **Credentials**
   - Click **Create Credentials** > **OAuth client ID**
   - If prompted, configure the OAuth consent screen first:
     - Choose **External** user type
     - Fill in:
       - App name: Bhutan Taxi
       - User support email: your-email@gmail.com
       - Developer contact: your-email@gmail.com
     - Click **Save and Continue** through all steps

5. Create OAuth Client ID:
   - Application type: **Web application**
   - Name: Bhutan Taxi Web Client
   - **Authorized JavaScript origins:**
     ```
     http://localhost:8000
     http://127.0.0.1:8000
     http://your-production-domain.com
     ```
   - **Authorized redirect URIs:**
     ```
     http://localhost:8000/auth/google/callback
     http://127.0.0.1:8000/auth/google/callback
     http://your-production-domain.com/auth/google/callback
     ```
   - Click **Create**

6. Copy your credentials:
   - You'll see your **Client ID** and **Client Secret**
   - Keep these safe!

### 2. Update Your .env File

Open `.env` file and add your Google credentials:

```env
GOOGLE_CLIENT_ID=your-client-id-here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

**Important:** Replace the placeholder values with your actual credentials from Google Cloud Console.

### 3. Update APP_URL for Production

For production, update the APP_URL in `.env`:

```env
APP_URL=https://yourdomain.com
```

### 4. Clear Configuration Cache

After updating `.env`, run:

```bash
php artisan config:clear
php artisan optimize:clear
```

## 🧪 Testing

### Local Testing

1. Start your server:
   ```bash
   php artisan serve
   ```

2. Visit `http://localhost:8000/login`

3. Click on "Continue with Google" button

4. You should be redirected to Google's login page

5. After authenticating, you'll be redirected back to your application

### What Happens During Google Sign-In

1. **New Users:**
   - A new account is created automatically
   - Name and email from Google are used
   - User is assigned 'passenger' role by default
   - Email is marked as verified
   - User is logged in

2. **Existing Users:**
   - If email already exists, user is logged in
   - Google ID is linked to the account

## 🔧 Troubleshooting

### Error: "redirect_uri_mismatch"
- Go back to Google Cloud Console > Credentials
- Edit your OAuth 2.0 Client ID
- Make sure the redirect URI exactly matches: `http://localhost:8000/auth/google/callback`
- Include both `http://localhost:8000` and `http://127.0.0.1:8000` in authorized origins

### Error: "Class 'Laravel\Socialite\Facades\Socialite' not found"
Run:
```bash
composer require laravel/socialite
php artisan config:clear
```

### Error: "Google+ API has not been enabled"
- Go to Google Cloud Console > APIs & Services > Library
- Search for "Google+ API" and enable it
- Wait a few minutes for it to propagate

### Error: "Unable to login with Google. Please try again or use email/password login."
This generic message is thrown when the application catches an exception during the callback.
Check `storage/logs/laravel.log` for details. A common cause is the `users.phone_number` column not
allowing null values; Google often doesn't return a phone number. The controller now generates a
placeholder (`google_{id}`) but you can also make the column nullable with a migration as described above.

### Users can't link Google to existing accounts
This is by design for security. If a user:
1. Registers with email/password
2. Later tries to sign in with Google using the same email
3. They will be logged into their existing account (Google ID will be added)

## 🔒 Security Considerations

1. **HTTPS in Production:** Always use HTTPS for your production domain
2. **Keep credentials secret:** Never commit `.env` file to version control
3. **Restrict domains:** In Google Cloud Console, only add your actual domains

## 📱 Additional Features You Can Add

### 1. Allow users to disconnect Google account
Create a route to set `google_id` to null

### 2. Show Google avatar
Store `$googleUser->avatar` when creating user

### 3. Allow email-less registration
Make email nullable for Google users who don't share it

## 🌐 Production Deployment

When deploying to production:

1. Update your Google OAuth credentials:
   - Add production domain to **Authorized JavaScript origins**
   - Add production callback URL to **Authorized redirect URIs**

2. Update `.env` on production server:
   ```env
   APP_URL=https://yourdomain.com
   GOOGLE_CLIENT_ID=your-prod-client-id
   GOOGLE_CLIENT_SECRET=your-prod-client-secret
   ```

3. Run migrations:
   ```bash
   php artisan migrate --force
   ```

4. Clear cache:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## 📧 Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode in `.env`: `APP_DEBUG=true` (only for development!)
3. Check Google Cloud Console > APIs & Services > Credentials for proper configuration

## 🎉 That's It!

Your users can now sign in with Google seamlessly!
