# Release Notes

## v1.2.1 (2018-10-24)

### Changed
- Changed Photon Update command in order to prevent update between different major or minor versions 

## v1.2.0 (2018-10-22)

### Added
- Added include fields functionality in the API
- Used include fields functionality in admin panel Vue app to minimize the response payloads
- Added field groups functionality
- Added AI-assisted celebrity face-recognition Asset Manager feature
- Added a Big Integer field type
- Added a user account lock functionality
- Added interrupt login and register events
- Added post login and register events
- Added the force delete entry functionality

### Changed
- Handled a case where while inserting a file in Redactor editor a file is selected instead of an image - it now generates a clickable link.
- Changed password reset and change methods - instead of eloquent they now use Photon iAPI
- Sorted modules by name in the generator sidebar
- Changed generator sidebar module selection to double click instead of a single click
- Improved the generator change reporter layout
- Changed the Laravel Mix configuration so that the localhost:3000 isn't automatically opened everytime the browserSync wacther is ran (prefer using the proxy URL)

### Fixed
- Fixed a generator validation error bug
- Fixed an error with jstree destroy method while switching from single to non-sortable module types
- Fixed a number of console errors in generator
- Fixed an issue of parent module not being accurately displayed for selected module in the generator section
- Fixed an issue of automatic table name generator not working under certain conditions
- Fixed a parent entry selection error for sortable modules
- Fixed an error with NotificationTransformer which caused notifications to fail

## v1.1.3 (2018-09-18)

### Added
- Implemented unique column for module fields in generator

### Fixed
- Fixed an issue of not being able to reorder gallery items in certain situations
- Fixed an issue of gallery items not being loaded in a correct order 
- Fixed issue with caching key name on module filter route, minor optimizations to filter route caching
- Fixed issue with missing relationships when caching data after insert and update
- CURLOPT_SSL_VERIFYPEER paramether added for method that validates license key

## v1.1.2 (2018-05-22)

### Fixed
- Fixed an issue of being able to click the empty row in dashboard widget

## v1.1.1 (2018-05-22)

### Added
- Added a not_in parameter to filter method

### Fixed
- Fixed an issue of intro not hiding if .env is set to production
- Fixed a mismatched parameter issue in FilterModuleEntriesDecoder.php

## v1.1.0 (2018-05-08)

### Added
- Enabled users to define their own field types, jobs and module exporters within dependencies section and to use them together with core files
- Added unique flag within fields table, which enables creating unique fields within DB 
- Add a tickbox to add new Module to Main Menu after generator create 
- Implemented introduciton tutorial within frontend
- Added `photon:update` artisan command 

### Changed
- Existing photon field types, jobs and module exporters moved to core section 
- Existing photon notifications, configs, commands, routes, seeders and service providers moved to core section 
- Existing photon middlewares moved to core section and dynamically loaded into kernel
- Default prefix for public API routes changed from `api` to `public-api`
- Enabled automatic login after registration if email confirmation is not required
- FCM tokens now stored in DB and backed up during sync process

### Fixed
- Fixed flag for adding index to DB issue
- Fixed user email confirmation issue
- Fixed issue with with special char escaping for `json_encode` / `json_decode` during sync process
- Fixed missing filters for module exporting
- Fixed validation and added unique attribute for email field within Users core module

### Removed
- Non required modules from core seeders 
- `.gitattributes` file

## v1.0.10 (2018-03-05)

### Added
- Added dynamic validation on user registration route
- photon:sync can now run on empty database table

### Changed
- Grouped artisan commands under photon namespace
- Updated artisan sync method reporting messages
- Disabled foreign key check when syncing relations
- Moved hard reset to artisan command
- Moved soft reset to artisan command
- Disabled hard and soft reset on production environment and enabled sync

### Fixed
- Fixed sync backup issues
- Fixed sync issue related to deleting module
- Fixed password reset issue
- Will not send confirmation email when USE_REGISTRATION_SERVICE_EMAIL=0
- Fixed some permission issues
- Fixed issue with generating confirmation code during user registration

### Removed
- Removed nbproject folder
- Removed some redundant files
- Removed unneded validation from login/register forms
