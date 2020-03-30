# Change Log

## Version Compatibility

| Laravel Version   | Oxygen Version    |
| ------------------|:-----------------:|
| 7                 | 4.x               |
| 6                 | 3.x               |
| 5.8               | 2.3.x             |
| 5.7               | 2.2.20            |
| 5.6               | 1.1.6             |
| 5.4               | 1.0.8             |
| 5.3               | 0.3.2             |
| 5.2               | 0.1.4             |

## Version 4
- Upgraded to support Laravel 7
- Auto-sync feature of nested relationships removed
- Bower usage removed. Now all client side libraries should be compiled through Laravel Mix.
- `emedia/helpers` package renamed to `emedia/laravel-helpers`
- Cleaned up files in `resources`

## Version 3
- Upgraded to support Laravel 6
- Added Local Development Notes in `DEVELOPMENT.md`
- Added script to sync source files from main Laravel repo

## Version 2.2.5
- Added Input stream parser for PUT requests
- Changed API profile `POST` to `PUT`

## Version 2.2.0
- Added API builder to dashboard
- Added files section to dashboard
- Added default API login controllers
- Added device management section to dashboard
- Changed default readme.md file

## Version 2.1.0

- Auto-generate UUID for Users
- Added Users to Dashboard
- Added User disabling, safe deletes with PII removal
- Added Scaffolding for default views with `scaffold:views` command
- Added standard date/time formats
- Separated Users migration file

## Version 2.0

- Laravel 5.7 Support
- Admin panel Upgraded to Bootstrap 4
- Simplified setup command with `--confirm` flag
- Authentication routes changed
- All dependent packages refactored
- Added feature flags
- Added `App Settings` section by default
- User profile options moved into main dashboard area
- Package Auto-discovery added
- `Readme.md` auto-updated after the setup process with build instructions
- Removed multi-tenant option temporarily
- Added Recaptcha to contact forms, fixed contact-us mail

## Version 1.1

- Laravel 5.6